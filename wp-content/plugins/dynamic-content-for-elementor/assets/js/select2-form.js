jQuery(window).on('elementor/frontend/init', function() {
    elementorFrontend.hooks.addAction('frontend/element_ready/form.default', function($scope) {
        var configStr = $scope.attr('data-dce-select2');
        if (!configStr) {
            return;
        }

        var config = JSON.parse(configStr);

        if (!config.fields || !jQuery.fn.select2) {
            return;
        }

        jQuery.each(config.fields, function(index, fieldData) {
            var $field = $scope.find('#form-field-' + fieldData.custom_id);
            if (!$field.length) return;

            $field.addClass('dce-ext-select2');
            var form = $scope.find('form')[0];

            // Trigger change events for Elementor form validation/logic
            $field.on('select2:select', function() {
                var evtChange = document.createEvent("HTMLEvents");
                evtChange.initEvent("change", false, true);
                var evtInput = document.createEvent("HTMLEvents");
                evtInput.initEvent("input", false, true);
                form.dispatchEvent(evtChange);
                form.dispatchEvent(evtInput);
            });

            var classes = $field.attr('class');
            var select2Options = {};
            if (fieldData.placeholder) {
                select2Options.placeholder = fieldData.placeholder;
                select2Options.allowClear = true;

                // Select2 requires an empty option for placeholder to work on single selects
                if (!$field.prop('multiple') && $field.find('option[value=""]').length === 0) {
                    // Prepend and select the empty option so the placeholder shows
                    $field.prepend('<option value="" selected></option>');
                }
            }

            var $select2 = $field.select2(select2Options);
            
            // Add classes to container to match Elementor styling
            if ($select2.data('select2') && $select2.data('select2').$container) {
                $select2.data('select2').$container.find('.select2-selection').addClass(classes);
            }
        });
        
        // Remove default arrow if present
        $scope.find('.select2-selection__arrow').remove();
    });
});
