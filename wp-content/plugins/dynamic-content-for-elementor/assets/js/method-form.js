document.addEventListener('DOMContentLoaded', function() {
    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/form.default', function($scope) {
            var configStr = $scope.attr('data-dce-form-method');
            if (!configStr) {
                return;
            }

            var config = JSON.parse(configStr);
            if (config.method === 'ajax') {
                return;
            }

            // Stop Elementor AJAX submit and use native form submission
            var $submit = $scope.find('button[type="submit"]');
            $submit.on('click', function(event) {
                event.stopImmediatePropagation();
                var $form = $scope.find('form').first();
                $form.off();
                if ($form[0].checkValidity()) {
                    $form.submit();
                }
            });
        });
    });
});
