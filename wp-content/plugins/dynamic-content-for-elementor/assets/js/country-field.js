jQuery(window).on('elementor/frontend/init', function() {
    elementorFrontend.hooks.addAction('frontend/element_ready/form.default', function($scope) {
        if (!jQuery.fn.select2) {
            return;
        }

        var $countryFields = $scope.find('select[data-dce-country-field]');
        if (!$countryFields.length) {
            return;
        }

        $countryFields.each(function() {
            var $field = jQuery(this);
            
            // Skip Select2 initialization if disabled
            if ($field.is('[data-dce-no-select2]')) {
                return;
            }
            
            var placeholder = $field.data('dce-country-placeholder') || '';
            var classes = $field.attr('class');
            var form = $scope.find('form')[0];

            $field.addClass('dce-country-select2');

            // Trigger change events for Elementor form validation/logic
            $field.on('select2:select select2:unselect', function() {
                var evtChange = document.createEvent('HTMLEvents');
                evtChange.initEvent('change', false, true);
                var evtInput = document.createEvent('HTMLEvents');
                evtInput.initEvent('input', false, true);
                form.dispatchEvent(evtChange);
                form.dispatchEvent(evtInput);
            });

            var select2Options = {};
            if (placeholder) {
                select2Options.placeholder = placeholder;
                select2Options.allowClear = true;
            }

            var $select2 = $field.select2(select2Options);

            // Add classes to container to match Elementor styling
            if ($select2.data('select2') && $select2.data('select2').$container) {
                $select2.data('select2').$container.find('.select2-selection').addClass(classes);
            }
        });

        // Remove default arrow if present
        $scope.find('.dce-country-select2').siblings('.select2-container').find('.select2-selection__arrow').remove();
    });
});
