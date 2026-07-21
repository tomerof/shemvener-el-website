(function ($) {
	var WidgetDyncontel_ACFRepeaterHandler = function ($scope, $) {
		var elementSettings = dceGetElementSettings($scope);
		var $block_acfgallery = ".dce-acf-repeater";

		if (elementSettings.dce_acf_repeater_format == "accordion") {
			$scope.find(".elementor-tab-title").on("click", function () {
				if (elementSettings.dce_acf_repeater_accordion_close) {
					$scope
						.find(".elementor-active")
						.not(this)
						.each(function () {
							jQuery(this).toggleClass("elementor-active");
							jQuery(this)
								.next()
								.slideToggle()
								.toggleClass("elementor-active");
						});
				}
				jQuery(this).toggleClass("elementor-active");
				jQuery(this)
					.next()
					.slideToggle()
					.toggleClass("elementor-active");
				return false;
			});
		}

		if (elementSettings.dce_acf_repeater_format == "masonry") {
			var $grid_dce_repeater = $scope.find($block_acfgallery).masonry({
				// options
				itemSelector: ".dce-acf-repeater-item",
			});
			// ---------- [ imagesLoaded ] ---------
			$grid_dce_repeater.imagesLoaded().progress(function () {
				$grid_dce_repeater.masonry("layout");
			});
		} else if (elementSettings.dce_acf_repeater_format == "justified") {
			$scope
				.find(".justified-grid")
				.imagesLoaded()
				.progress(function () {});

			$scope.find(".justified-grid").justifiedGallery({
				rowHeight:
					Number(elementSettings.justified_rowHeight.size) || 170,
				maxRowHeight: -1,
				selector: "figure, div:not(.spinner)",
				imgSelector: "> img, > a > img, > div > a > img, > div > img",
				margins: Number(elementSettings.justified_margin.size) || 0,
				lastRow: elementSettings.justified_lastRow,
			});
		} else if (
			elementSettings.dce_acf_repeater_format == "slider_carousel"
		) {
			var elementSwiper = $scope.find(
				".dce-acf-repeater-slider_carousel",
			);

			var id_scope = $scope.attr("data-id");
			var id_post = $scope.closest(".elementor").attr("data-post-id");
			var counter_id = $scope
				.find(".dce-acf-repeater-slider_carousel")
				.attr("counter-id");
			var centroDiapo = Boolean(elementSettings.centeredSlides);
			var loopEnabled = Boolean(elementSettings.loop);
			var slideInitNum = 0;
			var slidesPerView = Number(elementSettings.slidesPerView);
			var elementorBreakpoints = elementorFrontend.config.breakpoints;

			var swiperOptions = {
				// Optional parameters
				direction: "horizontal",
				initialSlide: slideInitNum,
				speed: Number(elementSettings.speed_slider) || 300,
				autoHeight: Boolean(elementSettings.autoHeight), //false, // Set to true and slider wrapper will adopt its height to the height of the currently active slide
				roundLengths: Boolean(elementSettings.roundLengths), //false, // Set to true to round values of slides width and height to prevent blurry texts on usual resolution screens (if you have such)
				effect: elementSettings.effects || "slide",
				slidesPerView: slidesPerView || "auto",
				slidesPerGroup: Number(elementSettings.slidesPerGroup) || 1, // Set numbers of slides to define and enable group sliding. Useful to use with slidesPerView > 1
				spaceBetween: Number(elementSettings.spaceBetween) || 0,
				slidesOffsetBefore: 0, //   Add (in px) additional slide offset in the beginning of the container (before all slides)
				slidesOffsetAfter: 0, //    Add (in px) additional slide offset in the end of the container (after all slides)
				slidesPerColumn: Number(elementSettings.slidesColumn) || 1, // 1, // Number of slides per column, for multirow layout
				slidesPerColumnFill: "row", // Could be 'column' or 'row'. Defines how slides should fill rows, by column or by row
				centerInsufficientSlides: true,
				watchOverflow: true,
				centeredSlides: centroDiapo,
				grabCursor: Boolean(elementSettings.grabCursor), //true,

				//------------------- Freemode
				freeMode: Boolean(elementSettings.freeMode),
				freeModeMomentum: Boolean(elementSettings.freeModeMomentum),
				freeModeMomentumRatio:
					Number(elementSettings.freeModeMomentumRatio) || 1,
				freeModeMomentumVelocityRatio:
					Number(elementSettings.freeModeMomentumVelocityRatio) || 1,
				freeModeMomentumBounce: Boolean(
					elementSettings.freeModeMomentumBounce,
				),
				freeModeMomentumBounceRatio: Number(elementSettings.speed) || 1,
				freeModeMinimumVelocity: Number(elementSettings.speed) || 0.02,
				freeModeSticky: Boolean(elementSettings.freeModeSticky),
				loop: loopEnabled,
				navigation: {
					nextEl: $scope.find(".swiper-button-next")[0],
					prevEl: $scope.find(".swiper-button-prev")[0],
				},
				pagination: {
					el: $scope.find(".swiper-pagination")[0],
					clickable: true,
					type: String(elementSettings.pagination_type) || "bullets",
					dynamicBullets: true,
					renderFraction: function (currentClass, totalClass) {
						return (
							'<span class="' +
							currentClass +
							'"></span>' +
							'<span class="separator">' +
							String(elementSettings.fraction_separator) +
							"</span>" +
							'<span class="' +
							totalClass +
							'"></span>'
						);
					},
				},
				mousewheel: Boolean(elementSettings.mousewheelControl),
				keyboard: {
					enabled: Boolean(elementSettings.keyboardControl),
				},

				on: {
					init: function () {
						$("body").attr(
							"data-carousel-" + id_scope,
							this.realIndex,
						);
					},
					slideChange: function (e) {
						$("body").attr(
							"data-carousel-" + id_scope,
							this.realIndex,
						);
					},
				},
			};
			if (elementSettings.useAutoplay) {
				//default
				swiperOptions = $.extend(swiperOptions, { autoplay: true });
				var autoplayDelay = Number(elementSettings.autoplay) || 3000;
				swiperOptions = $.extend(swiperOptions, {
					autoplay: {
						delay: autoplayDelay,
						disableOnInteraction: Boolean(
							elementSettings.autoplayDisableOnInteraction,
						),
						stopOnLastSlide: Boolean(
							elementSettings.autoplayStopOnLast,
						),
					},
				});
			}

			//------------------- Responsive Params
			var spaceBetween = 0;
			if (elementSettings.spaceBetween) {
				spaceBetween = elementSettings.spaceBetween;
			}
			var responsivePoints = (swiperOptions.breakpoints = {});
			responsivePoints[elementorBreakpoints.lg] = {
				slidesPerView: Number(elementSettings.slidesPerView) || "auto",
				slidesPerGroup: Number(elementSettings.slidesPerGroup) || 1,
				spaceBetween: Number(spaceBetween) || 0,
				slidesPerColumn: Number(elementSettings.slidesColumn) || 1,
			};

			var spaceBetween_tablet = spaceBetween;
			if (elementSettings.spaceBetween_tablet) {
				spaceBetween_tablet = elementSettings.spaceBetween_tablet;
			}
			responsivePoints[elementorBreakpoints.md] = {
				slidesPerView:
					Number(elementSettings.slidesPerView_tablet) ||
					Number(elementSettings.slidesPerView) ||
					"auto",
				slidesPerGroup:
					Number(elementSettings.slidesPerGroup_tablet) ||
					Number(elementSettings.slidesPerGroup) ||
					1,
				spaceBetween: Number(spaceBetween_tablet) || 0,
				slidesPerColumn:
					Number(elementSettings.slidesColumn_tablet) ||
					Number(elementSettings.slidesColumn) ||
					1,
			};

			var spaceBetween_mobile = spaceBetween_tablet;
			if (elementSettings.spaceBetween_mobile) {
				spaceBetween_mobile = elementSettings.spaceBetween_mobile;
			}
			responsivePoints[elementorBreakpoints.xs] = {
				slidesPerView:
					Number(elementSettings.slidesPerView_mobile) ||
					Number(elementSettings.slidesPerView_tablet) ||
					Number(elementSettings.slidesPerView) ||
					"auto",
				slidesPerGroup:
					Number(elementSettings.slidesPerGroup_mobile) ||
					Number(elementSettings.slidesPerGroup_tablet) ||
					Number(elementSettings.slidesPerGroup) ||
					1,
				spaceBetween: Number(spaceBetween_mobile) || 0,
				slidesPerColumn:
					Number(elementSettings.slidesColumn_mobile) ||
					Number(elementSettings.slidesColumn_tablet) ||
					Number(elementSettings.slidesColumn) ||
					1,
			};
			swiperOptions = $.extend(swiperOptions, responsivePoints);
			const asyncSwiper = elementorFrontend.utils.swiper;

			new asyncSwiper(elementSwiper, swiperOptions)
				.then((newSwiperInstance) => {
					mySwiper = newSwiperInstance;
				})
				.catch((error) => console.log(error));
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
	};

	$(window).on("elementor/frontend/init", function () {
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/dyncontel-acf-repeater.default",
			WidgetDyncontel_ACFRepeaterHandler,
		);
	});
})(jQuery);
