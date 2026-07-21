(function ($) {
	var WidgetElements_ParallaxHandler = function ($scope, $) {
		var scene = $scope.find("#scene");
		if (scene.length && scene.find('.layer').length > 0) {
			new Parallax(scene[0]);
		}
	};

	$(window).on("elementor/frontend/init", function () {
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/dyncontel-parallax.default",
			WidgetElements_ParallaxHandler,
		);
	});
})(jQuery);
