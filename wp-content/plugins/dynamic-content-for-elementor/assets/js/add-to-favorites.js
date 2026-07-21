(function ($) {
    var dceWidgetAddToFavorites = function ($scope, $) {
        let $button = $scope.find('.elementor-button');
        if (!$button.length) return;

        let actionSettings;
        try {
            actionSettings = JSON.parse($button.attr('data-dce-action-settings'));
        } catch (e) {
            console.error('Invalid action settings');
            return;
        }

        const canRemoveFavorite = $button.attr('data-dce-can-remove-favorites') === '1';
        let isFavorited = $button.attr('data-dce-is-favorited') === '1';
        const $counterElement = $scope.find('.elementor-button-counter');
        let counterValue = parseInt($button.attr('data-dce-counter')) || 0;
        const counterIconHtml = $button.attr('data-dce-counter-icon') || '';
        let isProcessing = false;
        let clickTimeout;

        updateButtonState($scope, isFavorited);
        renderOrUpdateCounter();

        function renderOrUpdateCounter(newValue = null) {
            if (!$counterElement.length) return;
            
            if (newValue !== null) {
                // Ensure counter never goes below 0
                counterValue = Math.max(0, newValue);
            }

            let counterHtml = counterValue;
            if (counterIconHtml) {
                counterHtml += counterIconHtml;
            }

            $counterElement.html(counterHtml);
        }

        function updateButtonState($scope, isFavorited) {
            const $button = $scope.find('.elementor-button');
            const settings = JSON.parse($button.attr('data-dce-action-settings'));
            const canRemoveFavorite = $button.attr('data-dce-can-remove-favorites') === '1';
            
            let state = isFavorited ? (canRemoveFavorite ? 'remove' : 'added') : 'add';
            
            if (settings[state]) {
                if (settings[state].title) {
                    $button.find('.elementor-button-text').text(settings[state].title);
                }
                if (settings[state].icon_html) {
                    $button.find('.elementor-button-icon').html(settings[state].icon_html);
                }
            }
            
            $button.toggleClass('dce-add-to-favorites-disabled', isFavorited && !canRemoveFavorite);
            $button.toggleClass('dce-add-to-favorites-add', !isFavorited);
            $button.toggleClass('dce-add-to-favorites-remove', isFavorited && canRemoveFavorite);

            if (isFavorited && !canRemoveFavorite) {
                $button.css('cursor', 'default');
                $button.css('pointer-events', 'none');
            } else {
                $button.css('cursor', '');
                $button.css('pointer-events', '');
            }
        }

        function setLoading(isLoading) {
            isProcessing = isLoading;
            $button.prop('disabled', isLoading);
            if (isLoading) {
                $button.addClass('loading');
            } else {
                $button.removeClass('loading');
            }
        }

        function handleError(error) {
            setLoading(false);
            console.error('Error updating favorites:', error);
            
            // Revert button state if there was an error
            updateButtonState($scope, !isFavorited);
            renderOrUpdateCounter(isFavorited ? counterValue - 1 : counterValue + 1);

            // Show error message to user if provided
            if (error?.responseJSON?.message) {
                alert(error.responseJSON.message);
            }
        }

        // Debounce function
        function debounce(func, wait) {
            return function executedFunction(...args) {
                const later = () => {
                    clickTimeout = null;
                    func(...args);
                };
                clearTimeout(clickTimeout);
                clickTimeout = setTimeout(later, wait);
            };
        }

        $button.on('click', debounce(function (e) {
            e.preventDefault();
            
            // Check for login URL and redirect if needed
            const loginUrl = $button.attr('data-dce-login-url');
            if (loginUrl) {
                window.location.href = loginUrl;
                return;
            }
            
            // Prevent multiple clicks while processing
            if (isProcessing) return;

            // If already favorited and can't remove, do nothing
            if (isFavorited && !canRemoveFavorite) return;

            const postId = $button.attr('data-dce-post-id');
            const key = $button.attr('data-dce-key');
            const scope = $button.attr('data-dce-scope');
            const whitelistToken = $button.attr('data-dce-whitelist-token');
            const action = isFavorited ? 'remove' : 'add';

            if (!postId || !key || !scope || !whitelistToken) {
                const missing = [];
                if (!postId) missing.push('post ID');
                if (!key) missing.push('key');
                if (!scope) missing.push('scope');
                if (!whitelistToken) missing.push('whitelist token');
                return;
            }

            setLoading(true);

            // Store current state before optimistic update
            const previousState = {
                isFavorited: isFavorited,
                counterValue: counterValue
            };

            // Optimistic UI update
            isFavorited = !isFavorited;
            counterValue = isFavorited ? counterValue + 1 : counterValue - 1;
            updateButtonState($scope, isFavorited);
            renderOrUpdateCounter(counterValue);

            wp.ajax.post('dce_favorite_action', {
                post_id: postId,
                key: key,
                scope: scope,
                action_type: action,
                whitelist_token: whitelistToken,
                nonce: dce_vars.nonce
            })
            .done(function(response) {
                if (response && response.success === false) {
                    isFavorited = previousState.isFavorited;
                    counterValue = previousState.counterValue;
                    updateButtonState($scope, isFavorited);
                    renderOrUpdateCounter(counterValue);
                    
                    if (response.message) {
                        alert(response.message);
                    }
                }

                // Update all buttons with same post_id
                $('.elementor-button[data-dce-post-id="' + postId + '"][data-dce-key="' + key + '"][data-dce-scope="' + scope + '"]').each(function() {
                    const $otherButton = $(this);
                    if (!$otherButton.is($button)) {
                        const $otherScope = $otherButton.closest('.elementor-element');
                        $otherButton.attr('data-dce-is-favorited', isFavorited ? '1' : '0');
                        updateButtonState($otherScope, isFavorited);
                    }
                });

                if ($button.attr('data-dce-trigger-finish') === '1') {
                    $(document).trigger('dce::finish_add_to_favorites');
                }
            })
            .fail(function(error) {
                isFavorited = previousState.isFavorited;
                counterValue = previousState.counterValue;
                updateButtonState($scope, isFavorited);
                renderOrUpdateCounter(counterValue);
                handleError(error);
            })
            .always(function() {
                setLoading(false);
            });
        }, 300));

        // Listen for clear favorites event
        $(document).on('dce:clearFavorites', function(e, data) {
            if (data.key === $button.attr('data-dce-key') && 
                data.scope === $button.attr('data-dce-scope')) {
                isFavorited = false;
                updateButtonState($scope, isFavorited);
                renderOrUpdateCounter(0);
            }
        });

        // Cleanup on widget destroy
        return function() {
            if (clickTimeout) {
                clearTimeout(clickTimeout);
            }
        };
    };

    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/dce-add-to-favorites.default', dceWidgetAddToFavorites);
        elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-woo-wishlist.default', dceWidgetAddToFavorites);
    });
})(jQuery);
