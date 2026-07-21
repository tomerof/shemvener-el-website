(function ($) {
	var WidgetDyncontel_PodsGalleryHandler = function ($scope, $) {
		var elementSettings = dceGetElementSettings($scope);

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

		if (!$scope.find('.dynamic_gallery.is-lightbox.photoswipe').length) {
			return;
		}

		var initPSLightbox = function initPSLightbox($sc) {
			var root = ($sc && $sc[0]) ? $sc[0] : $scope[0];
			var galleries = root ? root.querySelectorAll('.dynamic_gallery.is-lightbox.photoswipe') : [];
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

	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction(
			'frontend/element_ready/dyncontel-podsgallery.default',
			WidgetDyncontel_PodsGalleryHandler
		);
	});
})(jQuery);


