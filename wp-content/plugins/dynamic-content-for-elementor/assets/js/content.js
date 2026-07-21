(function ($) {
	var WidgetElements_ContentHandler = function ($scope, $) {
		var dcecontent = $scope.find(".dce-content");
		var dcecontentWrap = $scope.find(".dce-content-wrapper");
		var elementSettings = dceGetElementSettings($scope);

		if (elementSettings.enable_unfold) {
			var originalHeightUnfold = dcecontentWrap.outerHeight();
			var heightUnfold = elementSettings.height_content.size;

			if (originalHeightUnfold <= heightUnfold) {
				// Content is shorter, show all
				dcecontent.height("auto");
				$(".unfold-btn").hide();
			} else {
				// Content is longer, apply truncation
				dcecontent.addClass("unfolded");
				dcecontent.height(heightUnfold);
				$(".unfold-btn").show();

				$(".unfold-btn").click(function (e) {
					e.preventDefault();

					// Remove fixed height and set to 'auto'
					dcecontent.css("height", "auto");

					// Recalculate the actual content height
					var fullHeight = dcecontentWrap.outerHeight();

					// Animate content expansion
					dcecontent.animate(
						{ height: fullHeight },
						1000,
						function () {
							// Callback after animation
							dcecontent.css("height", "auto"); // Ensure height remains 'auto'
						},
					);

					$(".unfold-btn").remove();
				});
			}
		}

		function onResize() {
			if (elementSettings.enable_unfold && !$(".unfold-btn").length) {
				// Reset content height to auto after resize if unfolded
				dcecontent.css("height", "auto");
			}
		}
		window.addEventListener("resize", onResize);
	};

	$(window).on("elementor/frontend/init", function () {
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/dyncontel-content.default",
			WidgetElements_ContentHandler,
		);
	});
})(jQuery);
