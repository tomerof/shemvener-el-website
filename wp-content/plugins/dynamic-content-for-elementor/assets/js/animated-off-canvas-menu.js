(function ($) {
	var WidgetElements_AnimatedOffcanvasMenu = function ($scope, $) {
		var elementSettings = dceGetElementSettings($scope);

		var id_scope = $scope.attr("data-id");

		var animatedoffcanvasmenu = "#animatedoffcanvasmenu-" + id_scope;
		var class_hamburger = ".dce-button-hamburger";
		var class_modal = animatedoffcanvasmenu + " .dce-menu-aocm";
		var class_sidebg = animatedoffcanvasmenu + " .dce-bg";
		var class_quit = animatedoffcanvasmenu + " .dce-menu-aocm .dce-close";
		var class_menu_li =
			animatedoffcanvasmenu + " .dce-menu-aocm ul#dce-ul-menu > li";
		var class_template_after =
			animatedoffcanvasmenu + " .dce-template-after";

		var items_menu = $scope.find(
			class_menu_li + ", " + class_template_after,
		);

		var rate_menuside_desktop = Number(
			elementSettings.animatedoffcanvasmenu_rate.size,
		);
		var rate_menuside_tablet = Number(
			elementSettings.animatedoffcanvasmenu_rate_tablet.size,
		);
		var rate_menuside_mobile = Number(
			elementSettings.animatedoffcanvasmenu_rate_mobile.size,
		);
		var rate_menuside = rate_menuside_desktop;

		var side_background = elementSettings.side_background;
		var menu_position = elementSettings.aocm_position;

		// never set 0 durations, it seems to break anime.js
		const getTime = (key) =>
			Math.max(Number(elementSettings[key].size) / 1000, 0.001);

		const time_side_background_opening = side_background
			? getTime("time_side_background_opening")
			: 0.001;
		const time_menu_pane_opening = getTime("time_menu_pane_opening");
		const time_menu_list_opening = getTime("time_menu_list_opening");
		const time_menu_list_stagger = getTime("time_menu_list_stagger");

		var deviceMode = $("body").attr("data-elementor-device-mode");
		if (deviceMode === "tablet" && rate_menuside_tablet) {
			rate_menuside = rate_menuside_tablet;
		} else if (deviceMode === "mobile" && rate_menuside_mobile) {
			rate_menuside = rate_menuside_mobile;
		}

		$(class_modal).css({
			width: 0,
		});

		$(class_quit).css({
			transform: "scale(0)",
		});

		if (side_background) {
			$(class_sidebg).css({
				width: 0,
			});
			if (menu_position === "right") {
				$(class_sidebg).css({ right: rate_menuside + "%" });
			} else {
				$(class_sidebg).css({ left: rate_menuside + "%" });
			}
		}

		items_menu.css({
			transform: "translateY(12%)",
			opacity: 0,
		});

		var tl = anime.timeline({
			autoplay: false,
		});

		tl.add({
			targets: class_modal,
			width: [0, rate_menuside + "%"],
			duration: time_menu_pane_opening * 1000,
			easing: "easeOutExpo",
		});

		if (side_background) {
			tl.add(
				{
					targets: class_sidebg,
					width: [0, 100 - rate_menuside + "%"],
					duration: time_side_background_opening * 1000,
					easing: "easeInOutExpo",
				},
				0,
			);
		}

		tl.add({
			targets: items_menu.toArray(),
			translateY: ["12%", "0%"],
			opacity: [0, 1],
			duration: time_menu_list_opening * 1000,
			delay: anime.stagger(time_menu_list_stagger * 1000),
			easing: "easeOutExpo",
		});

		tl.add(
			{
				targets: class_quit,
				scale: [0, 1],
				duration: time_menu_pane_opening * 1000,
				easing: "easeInOutExpo",
			},
			0,
		);

		var isOpen = false;

		function toggleMenu() {
			if (!isOpen) {
				if (tl.reversed) {
					tl.reverse();
					tl.seek(0);
				}
				$(class_quit).fadeIn(
					time_menu_pane_opening * 1000,
					function () {
						$(this).removeClass("close-hidden");
					},
				);
				tl.play();
				isOpen = true;

				if (!elementorFrontend.isEditMode()) {
					$("body,html").addClass("dce-off-canvas-menu-open");
				}
			} else {
				$(class_quit).fadeOut(
					time_menu_pane_opening * 1000,
					function () {
						$(this).addClass("close-hidden");
					},
				);
				tl.reverse();
				tl.play();
				isOpen = false;

				if (!elementorFrontend.isEditMode()) {
					$("body,html").removeClass("dce-off-canvas-menu-open");
				}
			}
		}

		$scope.on("click", class_hamburger, function (e) {
			e.preventDefault();
			toggleMenu();
			return false;
		});

		$(document).on("click", class_quit, function (e) {
			e.preventDefault();
			if (isOpen) {
				toggleMenu();
			}
			return false;
		});

		$(document).on("keyup", function (e) {
			if (
				e.keyCode === 27 &&
				$("body").hasClass("dce-off-canvas-menu-open")
			) {
				toggleMenu();
			}
		});

		if (!side_background) {
			$(document).click(function (e) {
				if (!$("body").hasClass("dce-off-canvas-menu-open")) return;
				var $target = $(e.target);
				if (!$target.closest(animatedoffcanvasmenu).length) {
					if (isOpen) {
						e.preventDefault();
						toggleMenu();
					}
				}
			});
		}

		// Add new event listener to close the menu when clicking on an anchor link that points to an in-page anchor
		$(animatedoffcanvasmenu)
			.find("a")
			.filter(function () {
				// This filter checks that the link has a hash and points to the current page
				return (
					this.hash &&
					(this.pathname === location.pathname ||
						"/" + this.pathname === location.pathname) &&
					this.hostname === location.hostname
				);
			})
			.on("click", function () {
				if (isOpen) {
					toggleMenu();
				}
			});

		$(
			".animatedoffcanvasmenu ul > li.menu-item-has-children > .menu-item-wrap",
		).append('<span class="indicator-child no-transition">+</span>');
		$(".animatedoffcanvasmenu ul li span a:not([href])").addClass(
			"no-link no-transition",
		);
		$('.animatedoffcanvasmenu ul li span a[href="#"]').addClass(
			"no-link no-transition",
		);

		$(
			".animatedoffcanvasmenu ul > li.menu-item-has-children > .menu-item-wrap .indicator-child",
		).click(function (e) {
			e.preventDefault();
			$(this)
				.closest("li")
				.find("> .sub-menu")
				.not(":animated")
				.slideToggle();
		});
		$(".animatedoffcanvasmenu ul li span a.no-link").click(function (e) {
			e.preventDefault();
			$(this)
				.closest("li")
				.find("> .sub-menu")
				.not(":animated")
				.slideToggle();
		});

		if (!elementorFrontend.isEditMode()) {
			$(animatedoffcanvasmenu).prependTo("body");
		}
	};

	$(window).on("elementor/frontend/init", function () {
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/dce-animatedoffcanvasmenu.default",
			WidgetElements_AnimatedOffcanvasMenu,
		);
	});
})(jQuery);
