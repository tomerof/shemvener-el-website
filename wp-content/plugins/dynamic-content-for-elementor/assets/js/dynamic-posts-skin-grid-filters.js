var Widget_DCE_Dynamicposts_grid_filters_Handler = function ($scope, $) {
	if (elementorFrontend.isEditMode()) {
		return;
	}
	let elementSettings = dceGetElementSettings($scope);
	let container = $scope.find(
		".dce-posts-container.dce-skin-grid .dce-posts-wrapper",
	);
	let layoutMode = elementSettings[dceDynamicPostsSkinPrefix + "grid_type"];
	let rtl = Boolean(elementSettings["rtl"]);

	// "Match Height by Row" is a switcher whose return_value is the string
	// "true" (see skin definition), and it is absent when off. Passing undefined
	// to matchHeight would fall back to its byRow:true default (equalising per
	// row only), so the off state would never equalise the whole grid. Coerce to
	// a real boolean: on => per row, off => the whole grid.
	let byRow = elementSettings.grid_filters_match_height_by_row === "true";

	// Match Height must equalize the element that visually represents the card,
	// i.e. the template's top-level container(s)/section(s) (or the post block
	// when no template is used), the same target the Grid skin uses. Equalizing
	// the outer .dce-post-block wrapper instead would grow a transparent box
	// while the styled container inside keeps its natural height.
	const getMatchHeightElementsWithContainers = () => {
		let $nestedContainers = $scope.find(".e-con .e-con");
		let $articles = $scope.find(".dce-post-block");
		let matchHeightEls = [];
		$articles
			.first()
			.find(".e-con")
			.not($nestedContainers)
			.each((i) => {
				let $els = $articles.map((_, $e) => {
					return jQuery($e).find(".e-con").not($nestedContainers)[i];
				});
				matchHeightEls.push($els);
			});
		return matchHeightEls;
	};

	const findMatchHeightSlices = () => {
		let matchHeightEls;
		if (elementSettings.style_items === "template") {
			if (
				$scope.find(".dce-post-block .elementor-inner-section").length
			) {
				matchHeightEls = [];
				$scope
					.find(".dce-post-block")
					.first()
					.find(".elementor-inner-section")
					.each((i) => {
						let $els = $scope
							.find(".dce-post-block")
							.map((_, $e) => {
								return jQuery($e).find(
									".elementor-inner-section",
								)[i];
							});
						matchHeightEls.push($els);
					});
			} else if (
				$scope.find(".dce-post-block .elementor-top-section").length
			) {
				matchHeightEls = [
					$scope.find(".dce-post-block .elementor-top-section"),
				];
			} else {
				matchHeightEls = getMatchHeightElementsWithContainers();
			}
		} else {
			matchHeightEls = [$scope.find(".dce-post-block")];
		}
		return matchHeightEls;
	};

	// Re-apply Match Height to the currently visible items only, then re-layout
	// Isotope so its absolute positions track the equalized heights. Isotope
	// hides filtered-out items by setting display:none on the parent
	// (.dce-item-filterable), so hidden children must be excluded from the
	// height calculation. Shared by the initial load and every filter change so
	// the result is identical in both cases.
	const applyMatchHeight = () => {
		if (!elementSettings.grid_filters_match_height) {
			return;
		}
		findMatchHeightSlices().forEach(function ($els) {
			$els.matchHeight("remove");
			var $visible = $els.filter(function () {
				return (
					jQuery(this)
						.closest(".dce-item-filterable")
						.css("display") !== "none"
				);
			});
			$visible.matchHeight({ byRow: byRow });
			$visible.imagesLoaded().progress(function () {
				jQuery.fn.matchHeight._update();
				container.isotope("layout");
			});
		});
		container.isotope("layout");
	};

	const onFilterChange = (filter) => {
		container.isotope({
			filter: filter,
		});
		// Re-apply Match Height only to visible items after layout completes
		if (elementSettings.grid_filters_match_height) {
			container.one("layoutComplete", applyMatchHeight);
		}
		return false;
	};
	let defaultFilter;
	let $select = $scope.find(".dce-filters select");
	if ($select.length) {
		// select skin:
		defaultFilter = $select.val();
		$select.on("change", () => {
			onFilterChange($select.val());
		});
	} else {
		defaultFilter = $scope
			.find(".dce-filters .filter-active a")
			.attr("data-filter");
		let $filterItems = $scope.find(".dce-filters .filters-item");
		$filterItems.on("click", "a", function (e) {
			e.preventDefault();
			$(this).parent().siblings().removeClass("filter-active");
			$(this).parent().addClass("filter-active");
			let filterValue = $(this).attr("data-filter");
			onFilterChange(filterValue);
		});
	}

	const isotopeOptions = {
		itemSelector: ".dce-item-filterable",
		layoutMode: "masonry" === layoutMode ? "masonry" : "fitRows",
		sortBy: "original-order",
		filter: defaultFilter,
		percentPosition: true,
		originLeft: !rtl,
		masonry: {
			horizontalOrder: true,
			columnWidth: ".dce-item-filterable",
		},
	};

	// Initialize Isotope immediately so layout is computed before images load.
	// Then re-layout progressively as each image loads to adjust for actual
	// image dimensions. This prevents overlap when images are lazy-loaded.
	container.isotope(isotopeOptions);
	container.imagesLoaded().progress(function () {
		container.isotope("layout");
	});
	// Apply Match Height on the initial load using the same logic as filter
	// changes, once images have settled, so the first paint is equalized
	// consistently instead of being left to the generic grid handler.
	container.imagesLoaded().always(applyMatchHeight);
};

jQuery(window).on("elementor/frontend/init", function () {
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-dynamicposts-v2.grid-filters",
		Widget_DCE_Dynamicposts_grid_filters_Handler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-woo-products-cart.grid-filters",
		Widget_DCE_Dynamicposts_grid_filters_Handler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-dynamic-woo-products.grid-filters",
		Widget_DCE_Dynamicposts_grid_filters_Handler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-dynamic-woo-products-on-sale.grid-filters",
		Widget_DCE_Dynamicposts_grid_filters_Handler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-woo-product-upsells.grid-filters",
		Widget_DCE_Dynamicposts_grid_filters_Handler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-woo-product-crosssells.grid-filters",
		Widget_DCE_Dynamicposts_grid_filters_Handler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-dynamic-show-favorites.grid-filters",
		Widget_DCE_Dynamicposts_grid_filters_Handler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-my-posts.grid-filters",
		Widget_DCE_Dynamicposts_grid_filters_Handler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-sticky-posts.grid-filters",
		Widget_DCE_Dynamicposts_grid_filters_Handler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-search-results.grid-filters",
		Widget_DCE_Dynamicposts_grid_filters_Handler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-metabox-relationship.grid-filters",
		Widget_DCE_Dynamicposts_grid_filters_Handler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-acf-relationship.grid-filters",
		Widget_DCE_Dynamicposts_grid_filters_Handler,
	);
});
