function initializeSwiper(options, element) {
	const asyncSwiper = elementorFrontend.utils.swiper;

	new asyncSwiper(element, options)
		.then((newSwiperInstance) => {
			mySwiper = newSwiperInstance;
		})
		.catch((error) => console.log(error));
}

// Cannot initialize Swiper when hidden. This happens when it is inside a tab that is not the first.
const manageAutoplayInsideTab = (tabWrapper, options, elementSwiper) => {
	if (!tabWrapper.hidden) {
		// it's in the first tab.
		initializeSwiper(options, elementSwiper);
	}
	var observer = new MutationObserver(function (_, observer) {
		if (!tabWrapper.hidden) {
			initializeSwiper(options, elementSwiper);
		}
	});
	var observerConfig = {
		attributes: true,
		attributeFilter: ["hidden"],
	};
	observer.observe(tabWrapper, observerConfig);
};

(function ($) {
	var WidgetDyncontel_ACFRepeaterHandler = function ($scope, $) {
		let accordion = function () {
			let wrapper = $scope.find(".accordion");
			let accordionStart = elementSettings.dce_acf_repeater_accordion_start;
			let closeOtherSections = elementSettings.dce_acf_repeater_accordion_close;
			let speed = elementSettings.accordion_speed?.size || 300;
			
			const buttons = wrapper.find('.accordion-button');
			const contents = wrapper.find('.acc_content');
			
			buttons.each(function(index) {
				const $button = $(this);
				const $content = $($button.attr('aria-controls') ? '#' + $button.attr('aria-controls') : '');
				const section = $button.closest('.acc_section');
				
				let shouldBeActive = false;
				
				if (accordionStart === 'first' && index === 0) {
					shouldBeActive = true;
				} else if (accordionStart === 'custom') {
					const customIndex = elementSettings.accordion_start_custom || 1;
					shouldBeActive = (index + 1) === customIndex;
				} else if (accordionStart === 'all') {
					shouldBeActive = true;
				}
				
				if (shouldBeActive) {
					$button.attr('aria-expanded', 'true');
					$content.attr('aria-hidden', 'false').show();
					section.addClass('acc_active');
				} else {
					$button.attr('aria-expanded', 'false');
					$content.attr('aria-hidden', 'true').hide();
					section.removeClass('acc_active');
				}
			});
			
			buttons.on('click', function(e) {
				e.preventDefault();
				const $button = $(this);
				const $content = $($button.attr('aria-controls') ? '#' + $button.attr('aria-controls') : '');
				const section = $button.closest('.acc_section');
				const isExpanded = $button.attr('aria-expanded') === 'true';
				
				if (closeOtherSections && !isExpanded) {
					buttons.not($button).each(function() {
						const $otherButton = $(this);
						const $otherContent = $($otherButton.attr('aria-controls') ? '#' + $otherButton.attr('aria-controls') : '');
						const otherSection = $otherButton.closest('.acc_section');
						
						$otherButton.attr('aria-expanded', 'false');
						$otherContent.attr('aria-hidden', 'true').slideUp(speed);
						otherSection.removeClass('acc_active');
					});
				}
				
				if (isExpanded) {
					$button.attr('aria-expanded', 'false');
					$content.attr('aria-hidden', 'true').slideUp(speed);
					section.removeClass('acc_active');
				} else {
					$button.attr('aria-expanded', 'true');
					$content.attr('aria-hidden', 'false').slideDown(speed);
					section.addClass('acc_active');
				}
			});
		};

		let elementSettings = dceGetElementSettings($scope);
		var $block_acfgallery = ".dce-acf-repeater";

		if (elementSettings.dce_acf_repeater_format == "accordion") {
			let wrapper = $scope.find("ul.dce-acf-repeater-wrapper");
			let accordionStart = elementSettings.dce_acf_repeater_accordion_start;
			let activeIndex;

			if (accordionStart === "none") {
				activeIndex = false;
			} else if (accordionStart === "first") {
				activeIndex = 1;
			} else if (accordionStart === "custom") {
				activeIndex = elementSettings.accordion_start_custom || 1;
			} else {
				let elements = $scope.find("ul.dce-acf-repeater-wrapper .dce-acf-repeater-item").length;
				activeIndex = [];
				for (let i = 0; i <= elements; i++) {
					activeIndex[i] = i;
				}
			}

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
					
					openSection: function(section) {
						$(section).find('.acc_button').attr('aria-expanded', 'true');
						$(section).find('.acc_content').attr('aria-hidden', 'false');
					},
					
					beforeOpenSection: function(section) {
						if (Boolean(closeOtherSections)) {
							wrapper.find('.acc_button').attr('aria-expanded', 'false');
							wrapper.find('.acc_content').attr('aria-hidden', 'true');
						}
					},
					
					closeSection: function(section) {
						$(section).find('.acc_button').attr('aria-expanded', 'false');
						$(section).find('.acc_content').attr('aria-hidden', 'true');
					}
				});
			};
			
			accordionJs(
				wrapper,
				elementSettings.dce_acf_repeater_accordion_close,
				elementSettings.accordion_speed.size || 300,
				activeIndex
			);
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
			var loopEnabled = Boolean(elementSettings.loop);
			var slideInitNum = 0;
			var slidesPerView = Number(elementSettings.slidesPerView);
			
			// Prevent text selection on navigation buttons
			let nextElement = $scope.find(".swiper-button-next")[0];
			let prevElement = $scope.find(".swiper-button-prev")[0];
			if (nextElement) {
				nextElement.addEventListener('mousedown', function(e) {
					e.preventDefault();
				});
				// Keyboard accessibility for navigation buttons
				nextElement.addEventListener('keydown', function(e) {
					if (e.key === 'Enter' || e.key === ' ') {
						e.preventDefault();
						nextElement.click();
					}
				});
			}
			if (prevElement) {
				prevElement.addEventListener('mousedown', function(e) {
					e.preventDefault();
				});
				// Keyboard accessibility for navigation buttons
				prevElement.addEventListener('keydown', function(e) {
					if (e.key === 'Enter' || e.key === ' ') {
						e.preventDefault();
						prevElement.click();
					}
				});
			}
			
			var swiperOptions = {
				// Optional parameters
				direction: "horizontal",
				initialSlide: slideInitNum,
				speed: Number(elementSettings.speed_slider) || 300,
				autoHeight: Boolean(elementSettings.autoHeight), //false, // Set to true and slider wrapper will adopt its height to the height of the currently active slide
				roundLengths: Boolean(elementSettings.roundLengths), //false, // Set to true to round values of slides width and height to prevent blurry texts onusual resolution screens (if you have such)
				effect: elementSettings.effects || "slide",
				slidesPerView: slidesPerView || "auto",
				slidesPerGroup: Number(elementSettings.slidesPerGroup) || 1, // Set numbers of slides to define and enable group sliding. Useful to use with slidesPerView > 1
				spaceBetween: Number(elementSettings.spaceBetween) || 0, // 30,
				slidesOffsetBefore: 0, // Add (in px) additional slide offset in the beginning of the container (before all slides)
				slidesOffsetAfter: 0, // Add (in px) additional slide offset in the end of the container (after all slides)
				slidesPerColumn: Number(elementSettings.slidesColumn) || 1, // 1, // Number of slides per column, for multirow layout
				slidesPerColumnFill: "row", // Could be 'column' or 'row'. Defines how slides should fill rows, by column or by row
				centerInsufficientSlides:
					elementSettings.centerInsufficientSlides === "yes",
				watchOverflow: true,
				centeredSlides: elementSettings.centeredSlides2 === "yes",
				grabCursor: Boolean(elementSettings.grabCursor),
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
					nextEl: nextElement,
					prevEl: prevElement,
				},
				pagination: {
					el: id_post
						? ".dce-elementor-post-" +
							id_post +
							" .elementor-element-" +
							id_scope +
							" .pagination-" +
							counter_id
						: ".pagination-" + counter_id, //'.swiper-pagination', //'.pagination-acfslider-'+id_scope,
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
				mousewheel: elementSettings.mousewheelControl
					? {
							releaseOnEdges: true,
						}
					: false,
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

				var autoplayDelay = Number(elementSettings.autoplay);
				if (!autoplayDelay) {
					autoplayDelay = 3000;
				} else {
					autoplayDelay = Number(elementSettings.autoplay);
				}
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

			// Responsive Params
			swiperOptions.breakpoints = dynamicooo.makeSwiperBreakpoints(
				{
					slidesPerView: {
						elementor_key: "slidesPerView",
						default_value: "auto",
					},
					slidesPerGroup: {
						elementor_key: "slidesPerGroup",
						default_value: 1,
					},
					spaceBetween: {
						elementor_key: "spaceBetween",
						default_value: 1,
					},
					slidesPerColumn: {
						elementor_key: "slidesColumn",
						default_value: 1,
					},
				},
				elementSettings,
			);

			{
				let isCarousel =
					elementSettings.dce_acf_repeater_format ==
					"slider_carousel";
				if (!isCarousel) {
					initializeSwiper(swiperOptions, elementSwiper);
				} else {
					let elementorTab = elementSwiper.closest(
						".elementor-tab-content",
					);
					if (elementorTab.length && elementSettings.useAutoplay) {
						// we are inside a tab, autoplay needs to be updated.
						manageAutoplayInsideTab(
							elementorTab,
							swiperOptions,
							elementSwiper,
						);
					} else {
						initializeSwiper(swiperOptions, elementSwiper);
					}
				}
			}
		} 		// end if SliderCarousel

		// DataTables initialization
		if (elementSettings.dce_acf_repeater_format === "table" && elementSettings.dce_acf_repeater_datatables) {
			let initializeDataTables = function() {
				var $table = $scope.find('table.dce-datatable');
				
				// Safety checks before initializing DataTables
				if ($table.length === 0) {
					return;
				}
				
				// Check if table has valid structure
				var $thead = $table.find('thead');
				var $tbody = $table.find('tbody');
				var $rows = $tbody.find('tr');
				
				if ($rows.length === 0) {
					return;
				}
				

				
				// Get configuration from elementSettings
				var config = {
					autofill: elementSettings.dce_acf_repeater_style_table_data_autofill || false,
					buttons: elementSettings.dce_acf_repeater_style_table_data_buttons || false,
					colreorder: elementSettings.dce_acf_repeater_style_table_data_colreorder || false,
					fixedcolumns: elementSettings.dce_acf_repeater_style_table_data_fixedcolumns || false,
					fixedheader: elementSettings.dce_acf_repeater_style_table_data_fixedheader || false,
					keytable: elementSettings.dce_acf_repeater_style_table_data_keytable || false,
					responsive: elementSettings.dce_acf_repeater_style_table_data_responsive || false,
					rowgroup: elementSettings.dce_acf_repeater_style_table_data_rowgroup || false,
					rowreorder: elementSettings.dce_acf_repeater_style_table_data_rowreorder || false,
					scroller: elementSettings.dce_acf_repeater_style_table_data_scroller || false,
					scroller_y: elementSettings.dce_acf_repeater_style_table_data_scroller_y || false,
					select: elementSettings.dce_acf_repeater_style_table_data_select || false,
					// UI Options
					search: elementSettings.dce_acf_repeater_style_table_data_search || false,
					pagination: elementSettings.dce_acf_repeater_style_table_data_pagination || false,
					info: elementSettings.dce_acf_repeater_style_table_data_info || false,
					lengthMenu: elementSettings.dce_acf_repeater_style_table_data_length_menu || false,
					ordering: elementSettings.dce_acf_repeater_style_table_data_ordering || false
				};
				
				// Build DataTables options
				var options = {
					order: [],
					autoWidth: false,
					columnDefs: [
						{ targets: '_all', defaultContent: '' }
					],
					// UI Options
					searching: config.search,
					paging: config.pagination,
					info: config.info,
					lengthChange: config.lengthMenu,
					ordering: config.ordering
				};
				
				// Add language URL from separate i18n files
				if (typeof window.getDataTablesLanguageUrl === 'function') {
					var language = $('html').attr('lang') || 'en';
					var languageUrl = window.getDataTablesLanguageUrl(language);
					
					if (languageUrl) {
						options.language = {
							url: languageUrl
						};
					}
				}
				
				// Apply conditional options based on configuration
				if (config.autofill) {
					options.autoFill = true;
				}
				
				if (config.buttons) {
					options.dom = 'Bfrtip';
					options.buttons = [
						'copyHtml5',
						'excelHtml5',
						'csvHtml5',
						'pdfHtml5'
					];
				}
				
				if (config.colreorder) {
					options.colReorder = true;
				}
				
				if (config.fixedcolumns) {
					options.fixedColumns = true;
				}
				
				if (config.fixedheader) {
					options.fixedHeader = true;
				}
				
				if (config.keytable) {
					options.keys = true;
				}
				
				if (config.responsive) {
					options.responsive = true;
				}
				
				if (config.rowgroup) {
					options.rowGroup = {
						dataSrc: 'group'
					};
				}
				
				if (config.rowreorder) {
					options.rowReorder = true;
				}
				
				if (config.scroller) {
					options.scroller = true;
					options.scrollX = true;
					if (config.scroller_y) {
						options.scrollY = 200;
					}
					options.paging = true; // Scroller requires paging
					options.deferRender = true;
				}
				
				if (config.select) {
					options.select = true;
				}
				
				// Initialize DataTables
				$table.DataTable(options);
			};
			
			// Initialize DataTables
			initializeDataTables();
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
			"frontend/element_ready/dce-acf-repeater-v2.default",
			WidgetDyncontel_ACFRepeaterHandler,
		);
	});
})(jQuery);
