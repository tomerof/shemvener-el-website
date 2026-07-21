var dceDynamicPostsCrossroadsSlideshow = function ($scope, $) {
	var elementSwiper = $scope.find(".swiper")[0];
	var mainSwiper = null;
	var isTransitioning = false;

	if (!elementSwiper) {
		return;
	}

	function updateClasses(swiper) {
		if (!swiper || !swiper.slides) return;

		var slides = swiper.slides;
		var totalSlides = slides.length;
		var centerIndex = swiper.activeIndex;

		$(slides)
			.find(".dce-grid__item")
			.removeClass(
				"dce-grid__item--left dce-grid__item--center dce-grid__item--right",
			);

		var prevIndex = (centerIndex - 1 + totalSlides) % totalSlides;
		var nextIndex = (centerIndex + 1) % totalSlides;

		$(slides[prevIndex])
			.find(".dce-grid__item")
			.addClass("dce-grid__item--left");
		$(slides[centerIndex])
			.find(".dce-grid__item")
			.addClass("dce-grid__item--center");
		$(slides[nextIndex])
			.find(".dce-grid__item")
			.addClass("dce-grid__item--right");
	}

	function initClickHandlers(swiper) {
		$(elementSwiper).off("click.dceSlideshow");

		$(elementSwiper).on(
			"click.dceSlideshow",
			".dce-grid__item--left",
			function (e) {
				e.preventDefault();
				if (!isTransitioning) {
					isTransitioning = true;
					swiper.slidePrev();
				}
			},
		);

		$(elementSwiper).on(
			"click.dceSlideshow",
			".dce-grid__item--right",
			function (e) {
				e.preventDefault();
				if (!isTransitioning) {
					isTransitioning = true;
					swiper.slideNext();
				}
			},
		);

		$(elementSwiper).on(
			"click.dceSlideshow",
			".dce-grid__item--center .dce-post-link",
			function (e) {
				e.stopPropagation();
			},
		);
	}

	var swiperOptions = {
		slidesPerView: 2.6,
		centeredSlides: true,
		spaceBetween: "18%",
		speed: 1000,
		initialSlide: 0,
		watchOverflow: true,
		observer: true,
		observeParents: true,
		loop: true,
		loopedSlides: 2,
		preventClicks: false,
		preventClicksPropagation: false,
		on: {
			init: function () {
				updateClasses(this);
				initClickHandlers(this);
			},
			slideChange: function () {
				updateClasses(this);
			},
			slideChangeTransitionEnd: function () {
				isTransitioning = false;
			},
			loopFix: function () {
				updateClasses(this);
			},
			beforeTransitionStart: function () {
				updateClasses(this);
			},
		},
	};

	const asyncSwiper = elementorFrontend.utils.swiper;
	new asyncSwiper(elementSwiper, swiperOptions)
		.then((newSwiperInstance) => {
			mainSwiper = newSwiperInstance;
			updateClasses(mainSwiper);
		})
		.catch((error) => console.log(error));
};

jQuery(window).on("elementor/frontend/init", function () {
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-dynamicposts-v2.crossroadsslideshow",
		dceDynamicPostsCrossroadsSlideshow,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-woo-products-cart.crossroadsslideshow",
		dceDynamicPostsCrossroadsSlideshow,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-dynamic-woo-products.crossroadsslideshow",
		dceDynamicPostsCrossroadsSlideshow,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-dynamic-show-favorites.crossroadsslideshow",
		dceDynamicPostsCrossroadsSlideshow,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-my-posts.crossroadsslideshow",
		dceDynamicPostsCrossroadsSlideshow,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-sticky-posts.crossroadsslideshow",
		dceDynamicPostsCrossroadsSlideshow,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-search-results.crossroadsslideshow",
		dceDynamicPostsCrossroadsSlideshow,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-metabox-relationship.crossroadsslideshow",
		dceDynamicPostsCrossroadsSlideshow,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-acf-relationship.crossroadsslideshow",
		dceDynamicPostsCrossroadsSlideshow,
	);
});
