(function ($) {
    'use strict';

    var dceInlineAlignInit = function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/form.default', function ($scope) {
            var configData = $scope.data('dce-inline-align');
            
            if (!configData || !configData.fields) {
                return;
            }

            // Add repeater-item class to each field group
            $.each(configData.fields, function (index, field) {
                $('.elementor-field-group-' + field.custom_id)
                    .addClass('elementor-repeater-item-' + field.field_id);
            });
        });
    };

    // Initialize
    if (typeof elementorFrontend !== 'undefined' && elementorFrontend.hooks) {
        dceInlineAlignInit();
    } else {
        $(window).on('elementor/frontend/init', dceInlineAlignInit);
    }
})(jQuery);
