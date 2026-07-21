(function ($) {
	var smoothScroll = null;
	const math = {
		lerp: (a, b, n) => {
			return (1 - n) * a + n * b;
		},
		norm: (value, min, max) => {
			return (value - min) / (max - min);
		},
	};

	const config = {
		height: window.innerHeight,
		width: window.innerWidth,
	};

	class Smooth {
		constructor() {
			this.bindMethods();

			this.data = {
				ease: coefSpeed_inertiaScroll || 0.05,
				current: 0,
				last: 0,
			};

			this.dom = {
				el: main,
				content: mainWrap,
			};

			this.rAF = null;
			this.init();
		}

		bindMethods() {
			["scroll", "run", "resize"].forEach(
				(fn) => (this[fn] = this[fn].bind(this)),
			);
		}

		setStyles() {
			Object.assign(this.dom.el.style, {
				position: "fixed",
				top: 0,
				left: 0,
				height: "100%",
				width: "100%",
				overflow: "hidden",
			});
		}

		setWidth() {
			heightAdminBar = 0;
			if ($("body").is(".admin-bar") && !elementorFrontend.isEditMode()) {
				heightAdminBar = 45;
			}
			var larghezza = window.innerWidth;
			var altezza = this.dom.el.offsetHeight + heightAdminBar;

			// Total Length
			var larghezzaTotale =
				this.dom.el.offsetHeight + larghezza * sectionsAvailable.length;

			// Body Height
			var altezza = larghezzaTotale - this.dom.el.offsetWidth;
			sizeTotalScroll = altezza;
			document.body.style.height = `${altezza}px`;

			var l = larghezza * sectionsAvailable.length;
			// Wrapper
			sectionsWrapper.width(`${l}px`);
			// Section
			sectionsAvailable.each(function (i, el) {
				$(this).width(larghezza);
			});
		}

		setHeight() {
			heightAdminBar = 0;
			if ($("body").is(".admin-bar") && !elementorFrontend.isEditMode()) {
				heightAdminBar = 45;
			}
			var height = this.dom.content.offsetHeight - heightAdminBar;
			sizeTotalScroll = height;
			document.body.style.height = `${height}px`;
		}

		preload() {
			imagesLoaded(this.dom.content, (instance) => {
				if (directionScroll == "vertical") {
					this.setHeight();
				} else if (directionScroll == "horizontal") {
					this.setWidth();
				}
			});
		}

		scroll() {
			this.data.current = window.scrollY;
		}

		run() {
			this.data.last = math.lerp(
				this.data.last,
				this.data.current,
				this.data.ease,
			);
			this.data.last = Math.floor(this.data.last * 100) / 100;

			if (this.data.last < 0.1) {
				this.data.last = 0;
			}

			const skewVal = skew_inertiaScroll;
			const scaleVal = bounce_inertiaScroll;
			const diff = this.data.current - this.data.last;
			const acc = diff / config.width;
			const velo = +acc;
			const bounce = 1 - Math.abs(velo * scaleVal);
			const skew = velo * skewVal;

			var percentOfScroll = (this.data.current / sizeTotalScroll) * 100;

			if (directionScroll == "vertical") {
				var verticalmovement = this.data.last;
				this.dom.content.style.transform = `translate3d(0, -${verticalmovement}px, 0) skewY(${skew}deg) scaleY(${bounce})`;
				this.dom.content.style.transformOrigin = `50% ${percentOfScroll}% 0`;
			} else if (directionScroll == "horizontal") {
				var horizontalmovement = this.data.last;
				this.dom.content.style.transform = `translate3d(-${horizontalmovement}px, 0, 0) skewX(${skew}deg) scaleY(${bounce})`;
				this.dom.content.style.transformOrigin = `${percentOfScroll}% 50% 0`;
			}

			this.requestAnimationFrame();
		}

		on() {
			this.setStyles();
			if (directionScroll == "vertical") {
				this.setHeight();
			} else if (directionScroll == "horizontal") {
				this.setWidth();
			}
			this.addEvents();

			this.requestAnimationFrame();
		}

		off() {
			this.cancelAnimationFrame();
			this.removeEvents();
		}

		requestAnimationFrame() {
			this.rAF = requestAnimationFrame(this.run);
		}

		cancelAnimationFrame() {
			cancelAnimationFrame(this.rAF);
		}

		destroy() {
			document.body.style.height = "";
			this.data = null;

			this.removeEvents();
			this.cancelAnimationFrame();
		}

		resize() {
			if (directionScroll == "vertical") {
				this.setHeight();
			} else if (directionScroll == "horizontal") {
				this.setWidth();
			}
			this.scroll();
		}

		addEvents() {
			window.addEventListener("resize", this.resize, { passive: true });
			window.addEventListener("scroll", this.scroll, { passive: true });
		}

		removeEvents() {
			window.removeEventListener("resize", this.resize, {
				passive: true,
			});
			window.removeEventListener("scroll", this.scroll, {
				passive: true,
			});
		}

		init() {
			this.preload();
			this.on();
		}
	}

	var settings_page = {};
	var sectionsAvailable = [];
	var sections = "";
	var sectionsWrapper = null;

	var heightAdminBar = 0;
	var sizeTotalScroll = 0;

	is_pageScroll = false;
	// Scrollify
	var is_scrollify = false,
		titleStyle = "",
		navStyle = "default";

	// ScrollEffects
	var isScrollEffects = false;
	var currentPostId;
	var is_enable_dceScrolling,
		is_enable_scrollify,
		is_enable_scrollEffects,
		is_enable_inertiaScroll;

	var datalax = [
		"data-lax-opacity",
		"data-lax-translate",
		"data-lax-translate-x",
		"data-lax-translate-y",
		"data-lax-scale",
		"data-lax-scale-x",
		"data-lax-scale-y",
		"data-lax-skew",
		"data-lax-skew-x",
		"data-lax-skew-y",
		"data-lax-rotate",
		"data-lax-rotate-x",
		"data-lax-rotate-y",
		"data-lax-brightness",
		"data-lax-contrast",
		"data-lax-hue-rotate",
		"data-lax-blur",
		"data-lax-invert",
		"data-lax-saturate",
		"data-lax-grayscale",
		"data-lax-bg-pos",
		"data-lax-bg-pos-x",
		"data-lax-bg-pos-y",
		"data-lax-anchor",
	];
	// InertiaScroll

	// Version 1
	var is_inertiaScroll = false;
	var directionScroll = "vertical";
	var coefSpeed_inertiaScroll = 0.05;

	// Version 2
	const body = document.body;
	var main = {};
	var mainWrap = {};
	var skew_inertiaScroll = 20;
	var bounce_inertiaScroll = 0;
	var requestId;

	// Init - Page Snap
	var init_Scrollify = function () {
		$("body").addClass("dce-scrollify dce-scrolling");

		if (settings_page.custom_class_section_sfy) {
			$customClass = settings_page.custom_class_section_sfy;
			if ("." === settings_page.custom_class_section_sfy[0]) {
				$customClass =
					settings_page.custom_class_section_sfy.substring(1);
			}
		} else {
			$customClass =
				"elementor-section:not(.elementor-inner-section):not(.elementor-sticky__spacer)";
		}

		target_sections = ".elementor-" + currentPostId;
		if (!target_sections) {
			target_sections = "";
		}

		if (!$(".elementor-section-wrap").length) {
			$("body .elementor").wrapInner(
				'<div class="elementor-section-wrap"></div>',
			);
		}

		sections =
			target_sections +
			".elementor .elementor-section-wrap ." +
			$customClass;
		sectionsWrapper = $(
			target_sections + ".elementor .elementor-section-wrap",
		);

		// Class direction
		$(target_sections).addClass(
			"scroll-direction-" + settings_page.directionScroll,
		);

		$.scrollify({
			section: sections,
			sectionName: "id",
			interstitialSection: settings_page.interstitialSection,
			easing: "easeOutExpo",
			scrollSpeed: Number(settings_page.scrollSpeed.size) || 1100,
			offset: Number(settings_page.offset.size) || 0,
			scrollbars: Boolean(settings_page.scrollBars),
			setHeights: Boolean(settings_page.setHeights),
			overflowScroll: Boolean(settings_page.overflowScroll),
			updateHash: Boolean(settings_page.updateHash),
			touchScroll: Boolean(settings_page.touchScroll),
			before: function (i, panels) {
				var ref = panels[i].attr("data-id");
				$(".dce-scrollify-pagination .nav__item--current").removeClass(
					"nav__item--current",
				);
				$(".dce-scrollify-pagination")
					.find('a[href="#' + ref + '"]')
					.addClass("nav__item--current");
			},
			afterRender: function () {
				is_scrollify = true;
				if (
					settings_page.enable_scrollify_nav ||
					elementorFrontend.isEditMode()
				) {
					var scrollify_pagination = "";
					createNavigation(settings_page.snapscroll_nav_style);

					// At pagination click
					$("body").on(
						"click",
						".dce-scrollify-pagination a",
						function () {
							$.scrollify.move($(this).attr("href"));
							return false;
						},
					);

					if (!Boolean(settings_page.enable_scrollify_nav)) {
						handleScrollify_enablenavigation("");
					}
					if (Boolean(settings_page.enable_scrollEffects)) {
						handleScrollEffects(settings_page.enable_scrollEffects);
					}
				}
			},
		});
		$.scrollify.update();
	};
	var createNavigationTitles = function ($style, $reload = false) {
		titleStyle = $style;
		if ($reload) {
			createNavigation(settings_page.snapscroll_nav_style);
		}
	};
	var createNavigation = function ($style) {
		navStyle = $style;

		if ($(".dce-scrollify-pagination").length > 0)
			$(".dce-scrollify-pagination").remove();

		var newPagination = "";
		var activeClass;
		var titleString;
		createNavigationTitles(settings_page.snapscroll_nav_title_style);

		newPagination =
			'<ul class="dce-scrollify-pagination nav--' + $style + '">';

		if ($style == "ayana") {
			newPagination +=
				'<svg class="hidden"><defs><symbol id="icon-circle" viewBox="0 0 16 16"><circle cx="8" cy="8" r="6.215"></circle></symbol></defs></svg>';
		}
		if ($style == "desta") {
			newPagination +=
				'<svg class="hidden"><defs><symbol id="icon-triangle" viewBox="0 0 24 24"><path d="M4.5,19.8C4.5,19.8,4.5,19.8,4.5,19.8V4.2c0-0.3,0.2-0.5,0.4-0.7c0.2-0.1,0.5-0.1,0.8,0l13.5,7.8c0.2,0.1,0.4,0.4,0.4,0.7c0,0.3-0.2,0.5-0.4,0.7L5.7,20.4c-0.1,0.1-0.3,0.1-0.5,0.1C4.8,20.6,4.5,20.2,4.5,19.8z M6,5.6v12.8L17.2,12L6,5.6z"/></symbol></defs></svg>';
		}
		$(sections).each(function (i) {
			activeClass = "";
			if (i === 0) {
				activeClass = "nav__item--current";
			}

			if (titleStyle == "number") {
				var prefN = "";
				if (i < 9) {
					prefN = "0";
				}
				titleString = prefN + (i + 1);
			} else if (titleStyle == "classid") {
				titleString = $(this).attr("id") || "no id";
				titleString = titleString.replace(/_|-|\./g, " ");
			} else {
				titleString = "";
			}

			if ($style == "default") {
				newPagination +=
					'<li><a class="' +
					activeClass +
					'" href="#' +
					$(this).attr("data-id") +
					'"></a></li>';
			} else {
				$itemInner = "";
				$itemTitle =
					'<span class="nav__item-title">' + titleString + "</span>";

				if ($style == "etefu") {
					$itemInner = '<span class="nav__item-inner"></span>';
				} else if ($style == "ayana") {
					$itemTitle =
						'<svg class="nav__icon"><use xlink:href="#icon-circle"></use></svg>';
				} else if ($style == "totit") {
					var navIcon = settings_page.scrollify_nav_icon.value;
					if (navIcon)
						$itemInner =
							'<i class="nav__icon ' +
							navIcon +
							'" aria-hidden="true"></i>';
				} else if ($style == "desta") {
					$itemInner =
						'<svg class="nav__icon"><use xlink:href="#icon-triangle"></use></svg>';
				} else if (
					$style == "magool" ||
					$style == "ayana" ||
					$style == "timiro"
				) {
					$itemTitle = "";
				}
				newPagination +=
					'<li><a href="#' +
					$(this).attr("data-id") +
					'" class="' +
					activeClass +
					' nav__item" aria-label="' +
					(i + 1) +
					'">' +
					$itemInner +
					$itemTitle +
					"</a></li>";
			}
		});
		newPagination += "</ul>";

		$("body").append(newPagination);
	};

	// Init - Scroll Effects
	function initScrollEffects() {
		$("body").addClass("dce-pageScroll dce-scrolling");

		let customClass = "elementor-section:not(.elementor-inner-section)";
		if (settings_page.custom_class_section) {
			customClass = settings_page.custom_class_section.replace(/^\./, '');
		}

		let target_sections = ".elementor-" + currentPostId;
		if (!target_sections) {
			target_sections = "";
		}

		// Build the correct selector
		const sections = target_sections + " ." + customClass;
		sectionsAvailable = $(sections);

		// Class direction
		$(target_sections).addClass("scroll-direction-" + settings_page.directionScroll);

		// property
		animationType = settings_page.animation_effects || ["scaleDown"];
		var animationType_string = "";

		if (Array.isArray(animationType) && animationType.length) {
			animationType_string = animationType.join(" ");
		} else if (typeof animationType === 'string') {
			animationType_string = animationType;
		}

		// Determine starting section for scroll effects
		var startingSection = 0;
		if (settings_page.remove_first_scrollEffects) {
			startingSection = 1;
		}

		sectionsAvailable.each(function () {
			if ($(this).index() >= startingSection) {
				$(this).addClass("lax");
			}
		});

		lax.init()

		// Add a driver that we use to control our animations
		lax.addDriver('scrollY', function () {
			return window.scrollY
		}, {
			inertiaEnabled: true,
			inertia: 0.1,
			frameStep: 1
		})

		const presets = {
			scaleDown: {
				scale: [
					["elInY", "elCenterY", "elOutY"],
					[0, 1, 1],
					{
						easing: 'easeOutQuad'
					}
				]
			},
			opacity: {
				opacity: [
					["elInY", "elCenterY", "elOutY"],
					[0, 1, 1],
					{
						easing: 'easeOutQuad'
					}
				]
			},
			fixed: {
				translateY: [
					["elInY", "elCenterY", "elOutY"],
					[0, 0, -window.innerHeight],
					{
						easing: 'easeOutQuad'
					}
				]
			},
			rotation: {
				rotate: [
					["elInY", "elCenterY", "elOutY"],
					[30, 0, 0],
					{
						easing: 'easeOutQuad'
					}
				]
			},
			spin: {
				rotate: [
					["elInY", "elCenterY", "elOutY"],
					[-180, 0, 0],
					{
						easing: 'easeOutQuad'
					}
				]
			},
			spinRev: {
				rotate: [
					["elInY", "elCenterY", "elOutY"],
					[180, 0, 0],
					{
						easing: 'easeOutQuad'
					}
				]
			},
			leftToRight: {
				translateX: [
					["elInY", "elCenterY", "elOutY"],
					[-window.innerWidth, 0, 0],
					{
						easing: 'easeOutQuad'
					}
				]
			},
			rightToLeft: {
				translateX: [
					["elInY", "elCenterY", "elOutY"],
					[window.innerWidth, 0, 0],
					{
						easing: 'easeOutQuad'
					}
				]
			},
			zoomInOut: {
				scale: [
					["elInY", "elCenterY", "elOutY"],
					[0, 1, 1],
					{
						easing: 'easeOutQuad'
					}
				]
			},
			zoomIn: {
				scale: [
					["elInY", "elCenterY", "elOutY"],
					[0.5, 1, 1],
					{
						easing: 'easeOutQuad'
					}
				]
			},
			zoomOut: {
				scale: [
					["elInY", "elCenterY", "elOutY"],
					[1.5, 1, 1],
					{
						easing: 'easeOutQuad'
					}
				]
			},
			driftLeft: {
				translateX: [
					["elInY", "elCenterY", "elOutY"],
					[100, 0, 0],
					{
						easing: 'easeOutQuad'
					}
				]
			},
			driftRight: {
				translateX: [
					["elInY", "elCenterY", "elOutY"],
					[-100, 0, 0],
					{
						easing: 'easeOutQuad'
					}
				]
			},
			slalom: {
				translateX: [
					["elInY", "elCenterY", "elOutY"],
					[-50, 50, 50],
					{
						easing: 'easeInOutQuad'
					}
				]
			},
			lazy: {
				translateY: [
					["elInY", "elCenterY", "elOutY"],
					[0, 0, 0],
					{
						easing: 'easeOutQuad'
					}
				]
			},
			eager: {
				translateY: [
					["elInY", "elCenterY", "elOutY"],
					[100, 0, 0],
					{
						easing: 'easeOutQuad'
					}
				]
			},
			swing: {
				rotate: [
					["elInY", "elCenterY", "elOutY"],
					[-10, 10, 10],
					{
						easing: 'easeInOutQuad'
					}
				]
			},
			speedy: {
				translateY: [
					["elInY", "elCenterY", "elOutY"],
					[-100, 0, 0],
					{
						easing: 'easeInOutQuad'
					}
				],
				rotate: [
					["elInY", "elCenterY", "elOutY"],
					[10, 0, 0],
					{
						easing: 'easeInOutQuad'
					}
				]
			}
		};

		const selectedEffects = (animationType_string || "").split(" ").filter(Boolean);
		
		lax.addElements('.lax', {
			scrollY: selectedEffects.reduce((acc, effect) => {
				if (presets[effect]) {
					return { ...acc, ...presets[effect] };
				}
				return acc;
			}, {})
		});

		isScrollEffects = true;
	}

	// Init - Inertia Scroll
	var initInertiaScroll = function () {
		if (settings_page.custom_class_section) {
			$customClass = settings_page.custom_class_section;
		} else {
			$customClass = "elementor-top-section";
		}

		// SPEED
		if (typeof settings_page.coefSpeed_inertiaScroll.size !== "undefined")
			coefSpeed_inertiaScroll = Number(
				settings_page.coefSpeed_inertiaScroll.size,
			);
		// SKEW
		if (typeof settings_page.skew_inertiaScroll.size !== "undefined")
			skew_inertiaScroll = Number(settings_page.skew_inertiaScroll.size);
		// BOUNCE
		if (typeof settings_page.bounce_inertiaScroll.size !== "undefined")
			bounce_inertiaScroll = Number(
				settings_page.bounce_inertiaScroll.size,
			);
		// DIRECTIONS
		if (typeof settings_page.directionScroll !== "undefined")
			directionScroll = settings_page.directionScroll || "vertical";

		target_sections = ".elementor-" + currentPostId;
		if (!target_sections) {
			target_sections = "";
		}

		if (!$(".elementor-section-wrap").length) {
			$("body .elementor").wrapInner(
				'<div class="elementor-section-wrap"></div>',
			);
		}

		// Get the section widgets of first level in content-page
		sections =
			target_sections +
			".elementor .elementor-section-wrap ." +
			$customClass;
		sectionsAvailable = $(sections);
		sectionsWrapper = $(
			target_sections + ".elementor .elementor-section-wrap",
		);

		// Define Wrapper
		if ($(".elementor-template-canvas").length) {
			main = document.querySelector(target_sections);
			mainWrap = document.querySelector(
				target_sections + ".elementor .elementor-section-wrap",
			);
		} else {
			if (settings_page.automatic_wrapper) {
				if (!$("#outer-wrap").length) {
					$("body .elementor").wrapInner(
						'<div id="outer-wrap"><div id="wrap"></div></div>',
					);
				}
				main = document.querySelector("#outer-wrap");
				mainWrap = document.querySelector("#wrap");
			} else {
				main =
					document.querySelector(settings_page.scroll_viewport) ||
					document.querySelector("#outer-wrap");
				mainWrap =
					document.querySelector(
						settings_page.scroll_contentScroll,
					) || document.querySelector("#wrap");
			}
		}

		// per distribuire le section orizzontalmente
		if (directionScroll == "horizontal") {
			sectionsWrapper.css("display", "flex");
		}

		// Class direction
		$(target_sections).addClass("scroll-direction-" + directionScroll);

		// configure
		sectionsAvailable.addClass("inertia-scroll");

		if (smoothScroll) {
			smoothScroll.destroy();
		}

		smoothScroll = new Smooth();
		is_inertiaScroll = true;
	};

	function reloadScrolling() {
		if (settings_page.enable_dceScrolling) {
			handlescroll_viewport("");
			handlescroll_viewport("yes");
		}
	}

	// UTIL ScrollEffects ----------------------------------------
	function removeScrollEffects() {
		$("body").removeClass("dce-pageScroll");
		if (sectionsAvailable.length) sectionsAvailable.removeClass("lax");
		
		if (lax && typeof lax !== "undefined") {
			lax.removeElements('.lax');
		}

		window.cancelAnimationFrame(requestId);
		isScrollEffects = false;
	}

	// UTIL Inertia ----------------------------------------
	function removeInertiaScroll() {
		$("body").removeClass("dce-inertiaScroll");
		if (sectionsAvailable.length)
			sectionsAvailable.removeClass("inertia-scroll");

		sectionsAvailable.each(function (i, el) {
			$(this).removeAttr("style");
		});
		sectionsWrapper.removeAttr("style");

		smoothScroll.destroy();
		is_inertiaScroll = false;

		$(main).removeAttr("style");
		$(mainWrap).removeAttr("style");
	}

	// Change CallBack
	function handlescroll_viewport(newValue) {
		is_pageScroll = Boolean(newValue);
		if (settings_page.enable_scrollEffects) handleScrollEffects(newValue);
		if (settings_page.enable_scrollify) handleScrollify(newValue);
		if (settings_page.enable_inertiaScroll) handleInertiaScroll(newValue);
	}

	// Change CallBack SCROLLIFY
	function handleScrollify(newValue) {
		if (newValue) {
			if (is_scrollify) {
				$.scrollify.enable();
			}
			init_Scrollify();
			handleScrollify_enablenavigation(
				settings_page.enable_scrollify_nav,
			);
		} else {
			// NO
			$.scrollify.destroy();
			if (sectionsAvailable.length) {
				sectionsAvailable.removeAttr("style");
			}
			handleScrollify_enablenavigation("");

			is_scrollify = false;
		}
	}
	function handleScrollify_speed(newValue) {
		$.scrollify.setOptions({ scrollSpeed: newValue.size });
	}
	function handleScrollify_interstitialSection(newValue) {
		$.scrollify.setOptions({ scrollSpeed: newValue });
	}
	function handleScrollify_offset(newValue) {
		$.scrollify.setOptions({ offset: newValue.size });
	}
	function handleScrollify_ease(newValue) {
		$.scrollify.setOptions({ easing: newValue });
	}
	function handleScrollify_setHeights(newValue) {
		$.scrollify.setOptions({ setHeights: newValue ? true : false });
	}
	function handleScrollify_overflowScroll(newValue) {
		$.scrollify.setOptions({ overflowScroll: newValue ? true : false });
	}
	function handleScrollify_updateHash(newValue) {
		$.scrollify.setOptions({ updateHash: newValue ? true : false });
	}
	function handleScrollify_touchScroll(newValue) {
		$.scrollify.setOptions({ touchScroll: newValue ? true : false });
	}
	function handleScrollify_scrollBars(newValue) {
		$.scrollify.setOptions({ scrollbars: newValue ? true : false });
	}
	function handleScrollify_enablenavigation(newValue) {
		if (newValue) {
			$("body")
				.addClass("dce-scrollify")
				.find(".dce-scrollify-pagination")
				.show();
		} else {
			$("body")
				.removeClass("dce-scrollify")
				.find(".dce-scrollify-pagination")
				.hide();
		}
	}
	function handleScrollify_navstyle(newValue) {
		if (newValue) {
			createNavigation(newValue);
		}
	}
	function handleScrollify_titlestyle(newValue) {
		if (newValue) {
			createNavigationTitles(newValue, true);
		}
	}
	// Change CallBack SCROLL-EFFECTS
	function handleScrollEffects(newValue) {
		if (newValue) {
			if (isScrollEffects) {
				removeScrollEffects();
			}
			initScrollEffects();
		} else {
			removeScrollEffects();
		}
	}
	function handleScrollEffects_animations(newValue) {
		var animationType_string = newValue.join(" ");
		if (newValue.length) {
			removeScrollEffects();
			initScrollEffects();
		}

		reloadScrolling();
	}
	function handleScrollEffects_removefirst(newValue) {
		removeScrollEffects();
		initScrollEffects();
	}

	// Change CallBack INERTIA-SCROLL
	function handleInertiaScroll(newValue) {
		if (newValue) {
			if (is_inertiaScroll) {
				removeInertiaScroll();
			}
			if (settings_page.enable_inertiaScroll) {
				initInertiaScroll();
			}
		} else {
			removeInertiaScroll();
		}
	}
	function handleInertiaScroll_direction(newValue) {
		directionScroll = newValue;
		if (newValue) {
			if (is_inertiaScroll) {
				removeInertiaScroll();
			}
			if (settings_page.enable_inertiaScroll) {
				initInertiaScroll();
			}
		} else {
			removeInertiaScroll();
		}
	}

	$(window).on("elementor/frontend/init", function () {
		elementorFrontend.hooks.addAction("frontend/element_ready/global", function () {
			if (typeof elementorFrontendConfig.settings.page !== "undefined") {
				settings_page = elementorFrontendConfig.settings.page;
				currentPostId = elementorFrontendConfig.post.id;

                                // Check if scrolling is enabled globally
				if (!settings_page.enable_dceScrolling) {
					return;
				}

				var deviceMode = $("body").attr("data-elementor-device-mode");

                                // Initialize only the effects that are enabled and compatible with the device
				if (settings_page.enable_scrollEffects && 
					settings_page.responsive_scrollEffects && 
					$.inArray(deviceMode, settings_page.responsive_scrollEffects) >= 0) {
					initScrollEffects();
				}

				if (settings_page.enable_scrollify && 
					settings_page.responsive_snapScroll && 
					$.inArray(deviceMode, settings_page.responsive_snapScroll) >= 0) {
					init_Scrollify();
				}

				if (settings_page.enable_inertiaScroll && 
					settings_page.responsive_inertiaScroll && 
					$.inArray(deviceMode, settings_page.responsive_inertiaScroll) >= 0) {
					initInertiaScroll();
				}

				// Editor mode callbacks
				if (elementorFrontend.isEditMode()) {
					if (elementor) {
						settings_page =
							elementor.settings.page.model.attributes;

						elementor.settings.page.addChangeCallback(
							"enable_dceScrolling",
							handlescroll_viewport,
						);

						// Scrollify
						elementor.settings.page.addChangeCallback(
							"enable_scrollify",
							handleScrollify,
						);
						elementor.settings.page.addChangeCallback(
							"scrollSpeed",
							handleScrollify_speed,
						);
						elementor.settings.page.addChangeCallback(
							"offset",
							handleScrollify_offset,
						);
						elementor.settings.page.addChangeCallback(
							"ease_scrollify",
							handleScrollify_ease,
						);
						elementor.settings.page.addChangeCallback(
							"setHeights",
							handleScrollify_setHeights,
						);
						elementor.settings.page.addChangeCallback(
							"overflowScroll",
							handleScrollify_overflowScroll,
						);
						elementor.settings.page.addChangeCallback(
							"updateHash",
							handleScrollify_updateHash,
						);
						elementor.settings.page.addChangeCallback(
							"scrollBars",
							handleScrollify_scrollBars,
						);
						elementor.settings.page.addChangeCallback(
							"touchScroll",
							handleScrollify_touchScroll,
						);
						elementor.settings.page.addChangeCallback(
							"enable_scrollify_nav",
							handleScrollify_enablenavigation,
						);
						elementor.settings.page.addChangeCallback(
							"snapscroll_nav_style",
							handleScrollify_navstyle,
						);
						elementor.settings.page.addChangeCallback(
							"snapscroll_nav_title_style",
							handleScrollify_titlestyle,
						);

						// ScrollEffects
						elementor.settings.page.addChangeCallback(
							"enable_scrollEffects",
							handleScrollEffects,
						);
						elementor.settings.page.addChangeCallback(
							"animation_effects",
							handleScrollEffects_animations,
						);
						elementor.settings.page.addChangeCallback(
							"remove_first_scrollEffects",
							handleScrollEffects_removefirst,
						);

						// InertiaScroll
						elementor.settings.page.addChangeCallback(
							"enable_inertiaScroll",
							handleInertiaScroll,
						);
						elementor.settings.page.addChangeCallback(
							"directionScroll",
							handleInertiaScroll_direction,
						);

						elementor.settings.page.addChangeCallback(
							"coefSpeed_inertiaScroll",
							handleInertiaScroll,
						);
						elementor.settings.page.addChangeCallback(
							"skew_inertiaScroll",
							handleInertiaScroll,
						);
						elementor.settings.page.addChangeCallback(
							"bounce_inertiaScroll",
							handleInertiaScroll,
						);
					}
				}
			}
		});
	});
})(jQuery);
