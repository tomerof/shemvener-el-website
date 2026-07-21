var dceDynamicPostsAccordionHandler = function ($scope, $) {
	let elementSettings = dceGetElementSettings($scope);
	let wrapper = $scope.find("ul.dce-posts-wrapper");
	let accordionStart = elementSettings.accordion_start;
	let activeIndex;

	// Destroy existing accordion instance if it exists
	if (wrapper.data("accordionjs")) {
		wrapper.accordionjs("destroy");
	}

	if (accordionStart === "none") {
		activeIndex = false;
	} else if (accordionStart === "first") {
		activeIndex = 1;
	} else if (accordionStart === "custom") {
		activeIndex = elementSettings.accordion_start_custom || 1;
	} else {
		let elements = $scope.find("ul.dce-posts-wrapper .dce-post").length;
		activeIndex = [];
		for (let i = 0; i <= elements; i++) {
			activeIndex[i] = i;
		}
	}

	// AccordionJS
	let accordionJs = function (
		wrapper,
		closeOtherSections,
		speed,
		activeIndex,
	) {
		wrapper.accordionjs({
			// Allow self close.(data-close-able)
			closeAble: true,

			// Close other sections.(data-close-other)
			closeOther: Boolean(closeOtherSections),

			// Animation Speed.(data-slide-speed)
			slideSpeed: speed,

			// The section open on first init. A number from 1 to X or false.(data-active-index)
			activeIndex: activeIndex,

			openSection: function (section) {
				$(section).find(".acc_button").attr("aria-expanded", "true");
				$(section).find(".acc_content").attr("aria-hidden", "false");
			},

			beforeOpenSection: function (section) {
				if (Boolean(closeOtherSections)) {
					wrapper.find(".acc_button").attr("aria-expanded", "false");
					wrapper.find(".acc_content").attr("aria-hidden", "true");
				}
			},

			closeSection: function (section) {
				$(section).find(".acc_button").attr("aria-expanded", "false");
				$(section).find(".acc_content").attr("aria-hidden", "true");
			},
		});
	};
	accordionJs(
		wrapper,
		elementSettings.accordion_close_other_sections,
		elementSettings.accordion_speed.size,
		activeIndex,
	);
};

jQuery(window).on("elementor/frontend/init", function () {
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-dynamicposts-v2.accordion",
		dceDynamicPostsAccordionHandler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-dynamic-archives.accordion",
		dceDynamicPostsAccordionHandler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-woo-products-cart.accordion",
		dceDynamicPostsAccordionHandler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-woo-products-cart-on-sale.accordion",
		dceDynamicPostsAccordionHandler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-woo-product-upsells.accordion",
		dceDynamicPostsAccordionHandler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-woo-product-crosssells.accordion",
		dceDynamicPostsAccordionHandler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-dynamic-woo-products.accordion",
		dceDynamicPostsAccordionHandler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-dynamic-show-favorites.accordion",
		dceDynamicPostsAccordionHandler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-my-posts.accordion",
		dceDynamicPostsAccordionHandler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-sticky-posts.accordion",
		dceDynamicPostsAccordionHandler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-search-results.accordion",
		dceDynamicPostsAccordionHandler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-metabox-relationship.accordion",
		dceDynamicPostsAccordionHandler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-acf-relationship.accordion",
		dceDynamicPostsAccordionHandler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-woo-products-variations.accordion",
		dceDynamicPostsAccordionHandler,
	);

	// Re-initialize accordion after Search Filter Pro AJAX updates
	// Flag to prevent duplicate event listener registration
	if (!window.dceSFProAccordionInitialized) {
		window.dceSFProAccordionInitialized = true;

		jQuery(document).ready(function ($) {
			// Search Filter Pro v2 support
			$(document).on(
				"sf:ajaxfinish",
				".searchandfilter",
				function (data) {
					if (!data || !data.targetSelector) {
						return;
					}

					$(data.targetSelector)
						.find(
							'.elementor-widget[data-widget_type*="accordion"]',
						)
						.each(function () {
							var $element = $(this);
							if (
								$element.find(".dce-posts-wrapper").length > 0
							) {
								dceDynamicPostsAccordionHandler($element, $);
							}
						});
				},
			);

			// Search Filter Pro v3 support
			function initSearchFilterV3AccordionListeners() {
				if (
					window.searchAndFilter &&
					window.searchAndFilter.frontend &&
					window.searchAndFilter.frontend.queries
				) {
					const queries =
						window.searchAndFilter.frontend.queries.getAll();
					for (let i = 0; i < queries.length; i++) {
						const query = queries[i];

						// Check if this is our integration
						if (
							query.getAttribute("queryIntegration") ===
							"dynamicooo/dynamic-content-for-elementor"
						) {
							query.on(
								"get-results/finish",
								function (queryObject) {
									const queryContainer =
										queryObject.getQueryContainer();

									const $container = $(queryContainer);
									const widgetType =
										$container.attr("data-widget_type");

									// Only re-initialize if this is an accordion widget
									if (
										widgetType &&
										widgetType.endsWith(".accordion")
									) {
										if (
											$container.find(
												".dce-posts-wrapper",
											).length > 0
										) {
											dceDynamicPostsAccordionHandler(
												$container,
												$,
											);
										}
									}
								},
							);
						}
					}
				}
			}

			// Initialize Search Filter Pro v3 listeners when available
			if (window.searchAndFilter) {
				initSearchFilterV3AccordionListeners();
			} else {
				window.addEventListener(
					"searchandfilter/init",
					initSearchFilterV3AccordionListeners,
				);
			}
		});
	}
});
