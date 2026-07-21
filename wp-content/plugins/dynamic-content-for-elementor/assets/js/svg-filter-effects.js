(function ($) {
	var WidgetElements_SvgFilterEffectsHandler = function ($scope, $) {
		let feDisp = $scope.find("feDisplacementMap#displacement-map")[0];
		let tl = anime.timeline({
			loop: true,
		});

		tl.add({
			targets: feDisp,
			scale: [0, 1],
			duration: 1000,
			easing: "easeInOutQuad",
		}).add({
			targets: feDisp,
			scale: [1, 0],
			duration: 1000,
			easing: "easeInOutQuad",
			endDelay: 1000,
		});

		if (elementorFrontend.isEditMode()) {
			tl.pause();
		}
	};

	$(window).on("elementor/frontend/init", function () {
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/dyncontel-filtereffects.default",
			WidgetElements_SvgFilterEffectsHandler,
		);
	});
})(jQuery);
