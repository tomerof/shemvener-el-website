jQuery(window).on('elementor/frontend/init', function() {
    elementorFrontend.hooks.addAction('frontend/element_ready/form.default', function($scope) {
        var widgetId = $scope.data('id');
        var config = (window.dceFieldDescriptions || {})[widgetId];
        if (!config || !config.fields || !config.fields.length) {
            return;
        }

        config.fields.forEach(function(field) {
            if (field.position === 'elementor-field-label') {
                if (field.tooltip) {
                    var $label = $scope.find('.elementor-field-group-' + field.custom_id + ' .elementor-field-label');
                    $label.addClass('dce-tooltip').addClass('elementor-field-label-description');
                    var tooltipPos = field.tooltip_position || 'top';
                    var $tooltip = jQuery('<span class="dce-tooltiptext dce-tooltip-' + tooltipPos + '"></span>');
                    $tooltip.html(field.description);
                    $label.append($tooltip);
                } else {
                    var $label = $scope.find('.elementor-field-group-' + field.custom_id + ' .elementor-field-label');
                    var $abbr = jQuery('<abbr class="elementor-field-label-description elementor-field-label-description-' + field.custom_id + '"></abbr>');
                    $abbr.attr('title', field.description_text);
                    $label.wrap($abbr);
                }
            } else if (field.position === 'elementor-field') {
                var $fieldGroup = $scope.find('.elementor-field-group-' + field.custom_id);
                var $desc = jQuery('<div class="elementor-field-input-description elementor-field-input-description-' + field.custom_id + '"></div>');
                $desc.html(field.description);
                $fieldGroup.append($desc);
            }
        });
    });
});
