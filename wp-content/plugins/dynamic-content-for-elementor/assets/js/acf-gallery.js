(function ($) {
	var WidgetDyncontel_ACFGalleryHandler = function ($scope, $) {
		var elementSettings = dceGetElementSettings($scope);
		var $block_acfgallery = ".dce-acf-gallery";
		var $items_acfgallery = ".acfgallery-item";
		var $grid_dce_posts = $scope.find($block_acfgallery);

		if (elementSettings.gallery_type == "masonry") {
			var $masonry_dce_posts = $grid_dce_posts.masonry();

			$grid_dce_posts.imagesLoaded().progress(function () {
				$scope.find($items_acfgallery).css("opacity", 1);
				$masonry_dce_posts.masonry("layout");
			});
		} else if (elementSettings.gallery_type == "justified") {
			$scope
				.find(".dce-acf-gallery-justified")
				.imagesLoaded()
				.progress(function () {
					$scope.find($items_acfgallery).css("opacity", 1);
				});
			$scope.find(".dce-acf-gallery-justified").justifiedGallery({
				rowHeight:
					Number(elementSettings.justified_rowHeight.size) || 170,
				maxRowHeight: -1,
				selector: "figure, div:not(.spinner)",
				imgSelector: "> img, > a > img, > div > a > img, > div > img",
				margins: Number(elementSettings.justified_margin.size) || 0,
				lastRow: elementSettings.justified_lastRow,
			});
		} else if (elementSettings.gallery_type == "diamond") {
			var $size_d = elementSettings.size_diamond;
			var column_d = elementSettings.column_diamond;
			$scope.find($block_acfgallery).diamonds({
				size: $size_d.size || 240, // Size of the squares
				gap: elementSettings.gap_diamond || 0, // Pixels between squares
				itemSelector: ".acfgallery-item",
				hideIncompleteRow: Boolean(elementSettings.hideIncompleteRow),
				autoRedraw: true,
				minDiamondsPerRow: column_d,
			});
			$(window).resize(function () {
				$scope.find($block_acfgallery).diamonds("draw");
			});
		} else if (elementSettings.gallery_type == "hexagon") {
			var combWidth = dynamicooo.getResponsiveValue(
				elementSettings,
				"size_honeycombs",
				250,
			);
			$scope.find(".dce-acf-gallery-hexagon").honeycombs({
				combWidth: Number(combWidth),
				margin: Number(elementSettings.gap_honeycombs),
			});
		}

		if (elementSettings.enabled_wow) {
			var wow = new WOW({
				boxClass: 'wow',
				animateClass: 'animated',
				offset: 0,
				mobile: true,
				live: true,
				scrollContainer: null
			});
			wow.init();
		}

		var initPSLightbox = function initPSLightbox($sc) {
			var galleries = ($sc && $sc[0]) ? $sc[0].querySelectorAll('.dce-acf-gallery.is-lightbox.photoswipe') : [];
			if (!galleries || !galleries.length) return;
			for (var i = 0; i < galleries.length; i++) {
				var galleryEl = galleries[i];
				if ($(galleryEl).data('pswpInitialized')) continue;
				$(galleryEl).data('pswpInitialized', true);
				var lightbox = new PhotoSwipeLightbox({
					gallery: galleryEl,
					children: 'a.is-lightbox',
					pswpModule: PhotoSwipe
				});
				lightbox.init();
			}
		};
		initPSLightbox($scope);
	};

	$(window).on("elementor/frontend/init", function () {
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/dyncontel-acfgallery.default",
			WidgetDyncontel_ACFGalleryHandler,
		);
	});
})(jQuery);
