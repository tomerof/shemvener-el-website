var Widget_DCE_Dynamicposts_timeline_Handler = function ($scope, $) {
	var elementSettings = dceGetElementSettings($scope);

	function initTimeline() {
	
		var config = {
			containerSelector: '.js-dce-timeline',
			rootEl: $scope[0],
			offset: 0.5,
			progressLine: true,
			progressLineConfig: {
				wrapperSelector: '.dce-timeline-wrapper',
				rowspace: Number(
					elementSettings[dceDynamicPostsSkinPrefix + "timeline_rowspace"]["size"] || 0
				),
			},
			// Class names configuration
			blockClassName: 'dce-timeline__block',
			imgClassName: 'dce-timeline__img',
			contentClassName: 'dce-timeline__content',
			hiddenImgClassName: 'dce-timeline__img--hidden',
			hiddenContentClassName: 'dce-timeline__content--hidden',
			bounceImgClassName: 'dce-timeline__img--bounce-in',
			bounceContentClassName: 'dce-timeline__content--bounce-in',
			focusClassName: 'dce-timeline__focus',
			onResize: function(timelines) {
				// Update rowspace on resize
				rowspace = Number(
					elementSettings[dceDynamicPostsSkinPrefix + "timeline_rowspace"]["size"] || 0
				);
				// Update configuration for all timelines
				timelines.forEach(function(timeline) {
					if (timeline.progressLineConfig) {
						timeline.progressLineConfig.rowspace = rowspace;
						timeline.updateProgressLine();
					}
				});
			}
		};

		createVerticalTimelines(config);
	}

	initTimeline();
};

jQuery(window).on("elementor/frontend/init", function () {
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-dynamicposts-v2.timeline",
		Widget_DCE_Dynamicposts_timeline_Handler
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-dynamic-woo-products.timeline",
		Widget_DCE_Dynamicposts_timeline_Handler
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-dynamic-woo-products-on-sale.timeline",
		Widget_DCE_Dynamicposts_timeline_Handler
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-dynamic-show-favorites.timeline",
		Widget_DCE_Dynamicposts_timeline_Handler
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-my-posts.timeline",
		Widget_DCE_Dynamicposts_timeline_Handler
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-dynamic-archives.timeline",
		Widget_DCE_Dynamicposts_timeline_Handler
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-search-results.timeline",
		Widget_DCE_Dynamicposts_timeline_Handler
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-sticky-posts.timeline",
		Widget_DCE_Dynamicposts_timeline_Handler
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-metabox-relationship.timeline",
		Widget_DCE_Dynamicposts_timeline_Handler
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-acf-relationship.timeline",
		Widget_DCE_Dynamicposts_timeline_Handler
	);
});
