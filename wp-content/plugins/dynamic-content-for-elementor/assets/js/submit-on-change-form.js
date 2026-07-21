jQuery(window).on('elementor/frontend/init', function() {
    elementorFrontend.hooks.addAction('frontend/element_ready/form.default', function($scope) {
        var configStr = $scope.attr('data-dce-submit-on-change');
        if (!configStr) {
            return;
        }

        var config = JSON.parse(configStr);
        if (!config.fields || config.fields.length === 0) {
            return;
        }

        var widgetId = $scope.data('id');

        config.fields.forEach(function(customId) {
            var $inputs = jQuery('.elementor-element-' + widgetId + ' .elementor-field-group-' + customId +' input, .elementor-element-' + widgetId + ' .elementor-field-group-' + customId + ' select');
            
            $inputs.on('change', function() {
                var $field = jQuery(this).closest('.elementor-field-group');
                
                var $step = $field.closest('.elementor-field-type-step');
                var $next = $step.length ? $step.find('.elementor-field-type-next button').first() : jQuery();

                if ($next.length) {
                    // Multi-step form: advance to the next step
                    $next.trigger('click');
                } else {
                    // Single-step form or last step: submit
                    jQuery(this).closest('form').find('.elementor-field-type-submit button').trigger('click');
                }
            });
        });

        // Remove Select2 arrow if present
        jQuery('.elementor-element-' + widgetId + ' .select2-selection__arrow').remove();
    });
});
