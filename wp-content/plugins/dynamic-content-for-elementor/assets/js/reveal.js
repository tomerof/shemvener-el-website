(function ($) {
	var WidgetElements_RevealHandler = function ($scope, $) {
		var elementSettings = dceGetElementSettings($scope);
		var rev1;

		var revealAction = function () {
			rev1 = new RevealFx(revealistance, {
				isContentHidden: true,
				layers: 1,
				revealSettings: {
					direction: elementSettings.reveal_direction,
					bgColors: [elementSettings.reveal_bgcolor],
					duration: Number(elementSettings.reveal_speed.size) * 100,
					delay: Number(elementSettings.reveal_delay.size) * 100,
					onHalfway: function (contentEl, revealerEls) {
						contentEl.style.opacity = 1;
					},
				},
			});
		};

		var runReveal = function () {
			rev1.reveal();
		};

		if (elementSettings.enabled_reveal) {
			var revealId = "#reveal-" + $scope.data("id");
			var revealistance = document.querySelector(revealId);

			if (!jQuery(revealId).hasClass("block-revealer")) {
				revealAction();
			}

			var obsCallback = function (entries, observer) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						runReveal();
						observer.unobserve(entry.target);
					}
				});
			};

			var observerOptions = {
				root: null,
				rootMargin: "0px",
				threshold: 1.0,
			};

			var observer = new IntersectionObserver(
				obsCallback,
				observerOptions,
			);
			observer.observe(revealistance);
		}
	};

	$(window).on("elementor/frontend/init", function () {
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/global",
			WidgetElements_RevealHandler,
		);
	});
})(jQuery);
