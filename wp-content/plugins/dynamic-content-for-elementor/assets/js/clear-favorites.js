(function ($) {
    var dceClearFavorites = function ($scope, $) {
        let $button = $scope.find('.dce-clear-favorites-button');
        if (!$button.length) return;

        let isProcessing = false;

        function setLoading(isLoading) {
            isProcessing = isLoading;
            $button.prop('disabled', isLoading);
            if (isLoading) {
                $button.addClass('loading');
            } else {
                $button.removeClass('loading');
            }
        }

        $button.on('click', function (e) {
            e.preventDefault();
            
            // Prevent multiple clicks while processing
            if (isProcessing) return;

            const key = $button.data('dce-key');
            const scope = $button.data('dce-scope');
            const whitelistToken = $button.attr('data-dce-whitelist-token');
            const confirmationText = $button.data('dce-confirm');

            if (!key || !scope || !whitelistToken) {
                console.error('Missing required parameters');
                return;
            }

            // Show confirmation if enabled
            if (confirmationText && !confirm(confirmationText)) {
                return;
            }

            setLoading(true);

            wp.ajax.post('dce_clear_favorites', {
                key: key,
                scope: scope,
                whitelist_token: whitelistToken,
                nonce: dce_clear_favorites_vars.nonce
            })
            .done(function() {
				$(document).trigger('dce:clearFavorites', { key: key, scope: scope });
                // Trigger the event for dynamic posts update only if allowed by PHP filter
                if ($button.attr('data-dce-trigger-finish') === '1') {
                    $(document).trigger('dce::finish_add_to_favorites');
                }
            })
            .fail(function(error) {
                console.error('Error clearing favorites:', error);
                if (error?.responseJSON?.message) {
                    alert(error.responseJSON.message);
                }
            })
            .always(function() {
                setLoading(false);
            });
        });
    };

    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/dce-clear-favorites.default', dceClearFavorites);
    });
})(jQuery); 
