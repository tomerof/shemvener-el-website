(function ($) {
	var WidgetElements_CursorTrackerHandler = function ($scope, $) {
		var elementSettings = dceGetElementSettings($scope);
		var id_scope = $scope.attr("data-id");
		var cursorTarget = $scope.find("#cursors-" + id_scope);

		var init_cursortracker = function () {
			var progressTracker = elementSettings.cursortracker_scroll;
			var delay = parseFloat(elementSettings.delay.size) || 0;

			if (progressTracker) {
				var progressPath = document.querySelector(
					".progress-wrap path.dce-cursortrack-path1",
				);
				if (progressPath) {
					var pathLength = progressPath.getTotalLength();

					progressPath.style.transition =
						progressPath.style.WebkitTransition = "none";
					progressPath.style.strokeDasharray =
						pathLength + " " + pathLength;
					progressPath.style.strokeDashoffset = pathLength;
					progressPath.getBoundingClientRect();
					progressPath.style.transition =
						progressPath.style.WebkitTransition =
							"stroke-dashoffset 10ms linear";

					var updateProgress = function () {
						var scroll = $(window).scrollTop();
						var height = $(document).height() - $(window).height();
						var progress =
							pathLength - (scroll * pathLength) / height;
						progressPath.style.strokeDashoffset = progress;
					};
					window.removeEventListener("scroll", updateProgress);
					window.addEventListener("scroll", updateProgress);
					updateProgress();
				}
			}

			if (elementorFrontend.isEditMode() && $("body > .cursors").length) {
				$("body > .cursors").remove();
			}
			cursorTarget.prependTo("body");

			var container = document.body;
			var cursors = cursorTarget[0];
			var cursorWrap = cursors.querySelector(".cursor-wrap");

			function positionCircle(e, delaySec) {
				anime({
					targets: cursorWrap,
					translateX: e.clientX,
					translateY: e.clientY,
					duration: delaySec * 1000,
					easing: "linear",
				});
			}

			container.addEventListener("mouseenter", function (e) {
				anime.set(cursorWrap, {
					translateX: e.clientX,
					translateY: e.clientY,
				});
				anime({
					targets: cursors,
					scale: 1,
					duration: 500,
					easing: "linear",
				});
			});

			container.addEventListener("mouseleave", function (e) {
				anime({
					targets: cursors,
					scale: 0,
					duration: 500,
					easing: "linear",
				});
			});

			container.addEventListener("mousemove", function (e) {
				positionCircle(e, delay);
			});

			function addHoverClass() {
				cursors.classList.add("hover");
			}
			function removeHoverClass() {
				cursors.classList.remove("hover");
			}

			var hoverTargets = document.querySelectorAll(".cursor-target");
			hoverTargets.forEach(function (el) {
				el.addEventListener("mouseover", addHoverClass);
				el.addEventListener("mouseout", removeHoverClass);
			});
		};

		$(".cursors").show();

		var responsive_cursorTracker = elementSettings.responsive_cursorTracker;
		var deviceMode = $("body").attr("data-elementor-device-mode");

		if ($.inArray(deviceMode, responsive_cursorTracker) >= 0) {
			init_cursortracker();
		} else {
			cursorTarget.remove();
		}
	};

	$(window).on("elementor/frontend/init", function () {
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/dyncontel-cursorTracker.default",
			WidgetElements_CursorTrackerHandler,
		);
	});
})(jQuery);
