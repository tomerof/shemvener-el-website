(function ($) {
    var DCE_ScrollToStep_Init = function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/form.default', function ($scope) {
            var $form = $scope.find('.elementor-form[data-dce-scroll-to-step="yes"]');

            if (!$form.length) {
                return;
            }

            // Scroll to Top on step change
            setTimeout(function () {
                let nextButtons = $form.find('.elementor-field-type-next');
                let prevButtons = $form.find('.elementor-field-type-previous');
                let stepButtons = nextButtons.add(prevButtons);

                stepButtons.on('click', function () {
                    let formOffset = $form.offset().top - 70;
                    jQuery("html, body").animate({ scrollTop: formOffset });
                });
            }, 200);
        });
    };

    if (typeof elementorFrontend !== 'undefined' && elementorFrontend.hooks) {
        DCE_ScrollToStep_Init();
    } else {
        $(window).on('elementor/frontend/init', DCE_ScrollToStep_Init);
    }
})(jQuery);
