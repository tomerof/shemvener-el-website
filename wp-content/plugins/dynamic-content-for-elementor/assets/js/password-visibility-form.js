jQuery(window).on('elementor/frontend/init', function() {
    elementorFrontend.hooks.addAction('frontend/element_ready/form.default', function($scope) {
        var configStr = $scope.attr('data-dce-password-visibility');
        if (!configStr) {
            return;
        }

        var config = JSON.parse(configStr);
        if (!config.fields || config.fields.length === 0) {
            return;
        }

        // Prevent multiple initializations
        if ($scope.data('dce-psw-set') === 'yes') {
            return;
        }
        $scope.data('dce-psw-set', 'yes');

        var widgetId = $scope.data('id');

        config.fields.forEach(function(customId) {
            var $field = jQuery('.elementor-element-' + widgetId + ' #form-field-' + customId);
            if ($field.length === 0) {
                return;
            }

            $field.addClass('dce-form-password-toggle');
            $field.wrap('<div class="dce-field-input-wrapper dce-field-input-wrapper-' + customId + '"></div>');
            $field.parent().append('<span class="fa far fa-eye-slash field-icon dce-toggle-password"></span>');
            
            $field.next('.dce-toggle-password').on('click', function() {
                var $inputPsw = jQuery(this).prev();
                if ($inputPsw.attr('type') === 'password') {
                    $inputPsw.attr('type', 'text');
                } else {
                    $inputPsw.attr('type', 'password');
                }
                jQuery(this).toggleClass('fa-eye').toggleClass('fa-eye-slash');
            });
        });
    });
});
