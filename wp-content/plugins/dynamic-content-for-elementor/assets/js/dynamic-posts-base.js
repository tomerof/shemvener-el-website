var dceDynamicPostsSkin = "";
var dceDynamicPostsSkinPrefix = "";
var Widget_DCE_Dynamicposts_base_Handler = function ($scope, $) {
	const widgetType = $scope.attr("data-widget_type");
	if (typeof widgetType !== "string") {
		return;
	}

	const typeAndSkin = widgetType.split(".");
	if (typeAndSkin.length !== 2) {
		return;
	}

	// Features Dynamic Posts Collection
	let features = dce_features_collection_dynamic_posts;

	if (!features.includes(typeAndSkin[0])) {
		return;
	}
	dceDynamicPostsSkin = typeAndSkin[1];
	if (dceDynamicPostsSkin === "grid-filters") {
		dceDynamicPostsSkin = "grid_filters";
	}
	dceDynamicPostsSkinPrefix = dceDynamicPostsSkin + "_";
	var elementSettings = dceGetElementSettings($scope);

	// Run on load
	fitImages();
	initAjaxCallbacks();

	// HOVER EFFECTS
	var blocks_hoverEffects = $scope.find(".dce-post-block.dce-hover-effects");
	if (blocks_hoverEffects.length) {
		blocks_hoverEffects.each(function (i, el) {
			$(el).on("mouseenter touchstart", function () {
				$(this)
					.find(".dce-hover-effect-content")
					.removeClass("dce-close")
					.addClass("dce-open");
			});
			$(el).on("mouseleave touchend", function () {
				$(this)
					.find(".dce-hover-effect-content")
					.removeClass("dce-open")
					.addClass("dce-close");
			});
		});
	}

	// Linkable Template
	if (
		false === elementorFrontend.isEditMode() &&
		"yes" === elementSettings.templatemode_linkable
	) {
		$scope
			.find(".dce-post.dce-post-item[data-post-link]")
			.click(function () {
				window.location.assign($(this).attr("data-post-link"));
				return false;
			});
	}

	// Fit Images Ratio
	function fitImage($post) {
		var $imageParent = $post.find(".dce-img");
		$image = $imageParent.find("img");
		image = $image[0];

		if (!image) {
			return;
		}

		var imageParentRatio =
				$imageParent.outerHeight() / $imageParent.outerWidth(),
			imageRatio = image.naturalHeight / image.naturalWidth;
		$imageParent.toggleClass("dce-fit-img", imageRatio < imageParentRatio);
	}

	function fitImages() {
		var itemRatio = $scope
			.find(".dce-post-image figure")
			.first()
			.data("image-ratio");

		if (!itemRatio) {
			return;
		}
		$scope.find(".dce-posts-container").toggleClass("dce-is-ratio", true);
		$scope.find(".dce-post-image").each(function () {
			var $post = $(this);
			$image = $post.find(".dce-img img");
			fitImage($post);
			$image.on("load", function () {
				fitImage($post);
			});
		});
	}

	function initAjaxCallbacks() {
		// Re init layout after ajax request on Search&Filter Pro v2
		$(document).on("sf:ajaxfinish", ".searchandfilter", function (e, data) {
			if (
				window.elementorFrontend &&
				window.elementorFrontend.elementsHandler &&
				window.elementorFrontend.elementsHandler.runReadyTrigger
			) {
				var runReadyTrigger =
					window.elementorFrontend.elementsHandler.runReadyTrigger;
				let form = data.object;
				let ajaxUpdateSections = "[]";
				if (typeof form.dataset.ajaxUpdateSections == "string") {
					ajaxUpdateSections = form.dataset.ajaxUpdateSections;
				}
				let sections = JSON.parse(ajaxUpdateSections);
				sections.push(data.targetSelector);
				for (let section of sections) {
					runReadyTrigger($(section));
					$(section)
						.find(".elementor-widget")
						.each(function () {
							runReadyTrigger($(this));
						});
				}
			}
		});

		// Re init layout after ajax request on Search&Filter Pro v3
		function reInitElementorWidgets(domElement) {
			if (
				!domElement ||
				!window.elementorFrontend ||
				!window.elementorFrontend.elementsHandler ||
				!window.elementorFrontend.elementsHandler.runReadyTrigger
			) {
				return;
			}
			const runReadyTrigger =
				window.elementorFrontend.elementsHandler.runReadyTrigger;

			// Run the trigger for the dom element itself
			runReadyTrigger($(domElement));

			// Run the trigger for any child elementor widgets
			$(domElement)
				.find(".elementor-widget")
				.each(function () {
					runReadyTrigger($(this));
				});
		}

		// Function to add listeners to queries
		function initSearchAndFilterProQueryListeners(query) {
			// Re-init the layout of results after a query has updated the DOM
			const supportedIntegrations = [
				"dynamicooo/dynamic-content-for-elementor",
			];

			// Don't update if dynamic update isn't enabled
			if (query.getAttribute("resultsDynamicUpdate") !== "yes") {
				return;
			}

			// Ensure the integration type is one of the supported ones
			if (
				!supportedIntegrations.includes(
					query.getAttribute("queryIntegration"),
				)
			) {
				return;
			}

			// Listen for the update to the results
			query.on("get-results/finish", function (queryObject) {
				const queryContainer = queryObject.getQueryContainer();
				reInitElementorWidgets(queryContainer);
			});
		}

		// Initialize for Search & Filter Pro v3
		window.addEventListener("searchandfilter/init", () => {
			if (
				window.searchAndFilter &&
				window.searchAndFilter.frontend &&
				window.searchAndFilter.frontend.queries &&
				window.searchAndFilter.frontend.queries.getAll
			) {
				const queries =
					window.searchAndFilter.frontend.queries.getAll();
				for (let i = 0; i < queries.length; i++) {
					initSearchAndFilterProQueryListeners(queries[i]);
				}
			}
		});
	}
};

var dceFavoritesHandler = function ($scope, $) {
	$(document).one("dce::finish_add_to_favorites", function () {
		var widgetId = $scope.attr("data-id");

		$scope.css({
			opacity: "0.5",
			"pointer-events": "none",
		});

		$.ajax({
			url: window.location.href,
			type: "GET",
			success: function (response) {
				var updatedContent = $(response)
					.find('[data-id="' + widgetId + '"]')
					.html();
				$scope.html(updatedContent);

				if (
					window.elementorFrontend &&
					window.elementorFrontend.elementsHandler &&
					window.elementorFrontend.elementsHandler.runReadyTrigger
				) {
					window.elementorFrontend.elementsHandler.runReadyTrigger(
						$scope,
					);
				}
			},
			error: function (error) {
				console.log("Error in AJAX call", error);
			},
			complete: function () {
				$scope.css({
					opacity: "",
					"pointer-events": "",
				});
			},
		});
	});
};

jQuery(window).on("elementor/frontend/init", function () {
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/widget",
		Widget_DCE_Dynamicposts_base_Handler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-dynamic-show-favorites.grid",
		dceFavoritesHandler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-dynamic-show-favorites.carousel",
		dceFavoritesHandler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-dynamic-show-favorites.dualcarousel",
		dceFavoritesHandler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-dynamic-show-favorites.accordion",
		dceFavoritesHandler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-dynamic-show-favorites.list",
		dceFavoritesHandler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-dynamic-show-favorites.table",
		dceFavoritesHandler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-dynamic-show-favorites.timeline",
		dceFavoritesHandler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-dynamic-show-favorites.3d",
		dceFavoritesHandler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-dynamic-show-favorites.gridtofullscreen3d",
		dceFavoritesHandler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-dynamic-show-favorites.crossroadsslideshow",
		dceFavoritesHandler,
	);
});
