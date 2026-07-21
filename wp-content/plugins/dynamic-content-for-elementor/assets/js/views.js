(function ($) {
	var WidgetElements_ViewsHandler = function ($scope, $) {
		var id_scope = $scope.attr("data-id");
		var elementSettings = dceGetElementSettings($scope);
		var widgetId = $scope.data('id'); 
		
		// Handle slideshow format with Swiper
		if (elementSettings.dce_views_style_format === "slideshow") {
			// Variables for Slideshow
			let swiper_class = '.swiper';
			var elementSwiper = $scope.find(swiper_class);
			var speed = elementSettings.transition_speed;
			var disableOnInteraction = Boolean(elementSettings.pause_on_interaction) || false;
			var loop = false;

			if ("yes" === elementSettings.infinite) {
				loop = true;
			}

			var id_post = $scope.attr("data-post-id");

			var viewsSwiperOptions = {
				autoHeight: true,
				speed: speed,
				loop: loop,
			};

			// Responsive Parameters
			viewsSwiperOptions.breakpoints = dynamicooo.makeSwiperBreakpoints(
				{
					slidesPerView: {
						elementor_key: "slides_to_show",
						default_value: "auto",
					},
					slidesPerGroup: {
						elementor_key: "slides_to_scroll",
						default_value: 1,
					},
					spaceBetween: {
						elementor_key: "spaceBetween",
						default_value: 0,
					},
				},
				elementSettings,
			);

			// Navigation
			if (elementSettings.navigation != "none") {
				if (
					elementSettings.navigation == "both" ||
					elementSettings.navigation == "arrows"
				) {
					// Prevent text selection on navigation buttons
					let nextSelector = id_post
						? ".elementor-element-" +
							id_scope +
							'[data-post-id="' +
							id_post +
							'"] .elementor-swiper-button-next'
						: ".elementor-swiper-button-next";
					let prevSelector = id_post
						? ".elementor-element-" +
							id_scope +
							'[data-post-id="' +
							id_post +
							'"] .elementor-swiper-button-prev'
						: ".elementor-swiper-button-prev";
					
					let nextElement = $scope.find(nextSelector)[0];
					let prevElement = $scope.find(prevSelector)[0];
					
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
					
					viewsSwiperOptions = $.extend(viewsSwiperOptions, {
						navigation: {
							nextEl: nextSelector,
							prevEl: prevSelector,
						},
					});
				}

				if (
					elementSettings.navigation == "both" ||
					elementSettings.navigation == "dots"
				) {
					viewsSwiperOptions = $.extend(viewsSwiperOptions, {
						pagination: {
							el: id_post
								? ".elementor-element-" +
									id_scope +
									'[data-post-id="' +
									id_post +
									'"] .swiper-pagination'
								: ".swiper-pagination",
							type: "bullets",
							clickable: true,
						},
					});
				}
			}

			// Autoplay
			if (elementSettings.autoplay) {
				viewsSwiperOptions = $.extend(viewsSwiperOptions, {
					autoplay: {
						autoplay: true,
						delay: elementSettings.autoplay_speed,
						disableOnInteraction: disableOnInteraction,
					},
				});
			}

			const asyncSwiper = elementorFrontend.utils.swiper;

			new asyncSwiper(elementSwiper, viewsSwiperOptions)
				.then((newSwiperInstance) => {
					viewsSwiper = newSwiperInstance;
				})
				.catch((error) => console.log(error));

			// Pause on hover
			if (elementSettings.autoplay && elementSettings.pause_on_hover) {
				$(elementSwiper).on({
					mouseenter: function () {
						viewsSwiper.autoplay.stop();
					},
					mouseleave: function () {
						viewsSwiper.autoplay.start();
					},
				});
			}
			
			return; // Exit early for slideshow format
		}
		
		// Handle table format with DataTables
		if (elementSettings.dce_views_style_format === "table" && elementSettings.dce_views_style_table_data) {
			var $table = $scope.find('table.dce-datatable');
			
			var options = {
				order: [],
				ordering: true
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
			
			if (elementSettings.dce_views_style_table_data_autofill) {
				options.autoFill = true;
			}
			
			if (elementSettings.dce_views_style_table_data_buttons) {
				options.dom = 'Bfrtip';
				options.buttons = [
					'copyHtml5',
					'excelHtml5',
					'csvHtml5',
					'pdfHtml5'
				];
			}
			
			if (elementSettings.dce_views_style_table_data_colreorder) {
				options.colReorder = true;
			}
			
			if (elementSettings.dce_views_style_table_data_fixedcolumns) {
				options.fixedColumns = true;
			}
			
			if (elementSettings.dce_views_style_table_data_fixedheader) {
				options.fixedHeader = true;
			}
			
			if (elementSettings.dce_views_style_table_data_keytable) {
				options.keys = true;
			}
			
			if (elementSettings.dce_views_style_table_data_responsive) {
				options.responsive = true;
			}
			
			if (elementSettings.dce_views_style_table_data_rowgroup) {
				options.rowGroup = {
					dataSrc: 'group'
				};
			}
			
			if (elementSettings.dce_views_style_table_data_rowreorder) {
				options.rowReorder = true;
			}
			
			if (elementSettings.dce_views_style_table_data_scroller) {
				options.scroller = true;
				options.scrollX = true;
				if (elementSettings.dce_views_style_table_data_scroller_y) {
					options.scrollY = 200;
				}
				options.paging = true;
				options.deferRender = true;
			} else {
				options.paging = false;
			}
			
			if (elementSettings.dce_views_style_table_data_select) {
				options.select = true;
			}
			
			$table.DataTable(options);
		}
		
		// Handle AJAX Exposed Form
		if (elementSettings.dce_views_where_form_ajax) {
			
			// Update results function
			function updateResults() {
				var resultContainer = '.elementor-element-' + widgetId + ' .dce-view-results-wrapper';
				var sortContainer = '.elementor-element-' + widgetId + ' .dce-view-exposed-sort-wrapper';
				var paginationContainer = '.elementor-element-' + widgetId + ' .dce-view-posts-pagination-wrapper';
				var filtersContainer = '.elementor-element-' + widgetId + ' .dce-view-form-filters-wrapper';
				
				jQuery(resultContainer).html('<div class="dce-preloader" style="text-align: center;"><i class="fa fa-cog fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span></div>');
				
				jQuery.get('?' + jQuery('#dce-view-form-' + widgetId).serialize(), function(data) {
					jQuery(resultContainer).html(jQuery(data).find(resultContainer).html());
					jQuery(sortContainer).html(jQuery(data).find(sortContainer).html());
					
					if (jQuery(data).find(paginationContainer).length) {
						jQuery(paginationContainer).html(jQuery(data).find(paginationContainer).html());
					} else {
						jQuery(paginationContainer).html('');
					}
					
					jQuery(filtersContainer).html(jQuery(data).find(filtersContainer).html());					
					jQuery(document).trigger('dce/views/exposedform/after');
				});
			}
			
			// Form submit handler
			$scope.find('.dce-view-form').on('submit', function() {
				updateResults();
				return false;
			});
			
		// On change handler if enabled
		if (elementSettings.dce_views_where_form_ajax_onchange) {
			// Text inputs: use 'input' for real-time updates
			$scope.find('.dce-view-form input:not([type=checkbox]):not([type=radio]):not([type=reset])').on('input', function() {
				updateResults();
			});
			
			// Select, checkbox, radio: use 'change'
			$scope.find('.dce-view-form select, .dce-view-form input[type=checkbox], .dce-view-form input[type=radio]').on('change', function() {
				updateResults();
			});
			
			$scope.find('.dce-view-form input[type=reset]').on('click', function() {
				setTimeout(updateResults, 100);
			});
		}
		}
		
		// Handle AJAX Pagination
		if (elementSettings.dce_views_pagination_ajax) {
			$scope.on('click', '.dce-posts-pagination a', function(e) {
				e.preventDefault(); // Ensure default action is prevented
				
				var widgetContainer = '.elementor-element-' + widgetId + ' .dce-view-results-wrapper';
				var paginationContainer = '.elementor-element-' + widgetId + ' .dce-view-posts-pagination-wrapper';
				
				jQuery(widgetContainer).html('<div class="dce-preloader" style="text-align: center;"><i class="fa fa-cog fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span></div>');

				jQuery.get(jQuery(this).attr('href'), function(data) {
					if (jQuery(data).find(widgetContainer).length) {
						jQuery(widgetContainer).html(jQuery(data).find(widgetContainer).html());
					}
					
					if (jQuery(data).find(paginationContainer).length) {
						jQuery(paginationContainer).html(jQuery(data).find(paginationContainer).html());
					}
				});
				
				return false;
			});
		}
		
		// Handle Infinite Scroll
		if (elementSettings.dce_views_pagination_type === 'infinite_scroll' && !elementorFrontend.isEditMode()) {
			var infiniteOptions = {
				path: '.elementor-element-' + widgetId + ' .pagination__next',
				append: '.elementor-element-' + widgetId + ' .dce-view-archive .dce-view-single-wrapper',
				hideNav: '.elementor-element-' + widgetId + ' .dce-posts-navigation',
				status: '.elementor-element-' + widgetId + ' .scroller-status',
				debug: true
			};
			
			if (elementSettings.dce_views_limit_scroll_button) {
				infiniteOptions.button = '.elementor-element-' + widgetId + ' .view-more-button';
				infiniteOptions.scrollThreshold = false;
			}
			
			$scope.find('.dce-view-archive').infiniteScroll(infiniteOptions);
			
			// Handle events (InfiniteScroll only handles p.infinite-scroll-last, not div)
			$scope.find('.dce-view-archive')
				.on('last.infiniteScroll', function(event, response, path) {
					jQuery(this).find('.infinite-scroll-last').fadeIn();
				})
				.on('request.infiniteScroll', function(event, path) {
					jQuery(this).find('.infinite-scroll-request').show();
				})
				.on('append.infiniteScroll', function(event, response, path, items) {
					jQuery(this).find('.infinite-scroll-request').hide();
				});
		}
		
		// Handle AJAX Filter
		if (elementSettings.dce_views_where_form_ajax) {
			$scope.on('click', '.dce-view-form-filter a', function() {
				var field_container = '.elementor-element-' + widgetId + ' .dce-view-form-wrapper .elementor-field-group-' + jQuery(this).data('field');
				jQuery(field_container).find('input').val('');
				jQuery(field_container).find('select').val('');
				jQuery(field_container).find('input:checked').prop('checked', false);
				jQuery(field_container).find('input,select').trigger('change');
				return false;
			});
		}
	};

	$(window).on("elementor/frontend/init", function () {
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/dce-views.default",
			WidgetElements_ViewsHandler,
		);
	});
})(jQuery);
