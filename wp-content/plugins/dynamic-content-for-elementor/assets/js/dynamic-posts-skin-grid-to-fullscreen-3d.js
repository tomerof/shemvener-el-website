var dceDynamicPostsGridToFullscreen3D = function ($scope, $) {
	// Returns a valid float from element settings or a fallback
	function getSizeVal(obj, fallback) {
		if (!obj || typeof obj.size === "undefined" || !obj.size) {
			return fallback;
		}
		let val = parseFloat(obj.size);
		if (isNaN(val) || val <= 0) {
			return fallback;
		}
		return Math.max(0.0001, val);
	}

	const elementSettings = dceGetElementSettings($scope);
	const gridWrapper = $scope.find(".dce-gridtofullscreen3d-wrapper");
	const wrapperEl = gridWrapper[0];
	const gridTarget = ".dce-post-image";
	const rawPanelPos =
		elementSettings.gridtofullscreen3d_gridtofullscreen3d_panel_position ||
		"right";
	const panel_position =
		typeof rawPanelPos === "string" ? rawPanelPos.trim() : "right";

	const deviceMode = $("body").attr("data-elementor-device-mode");
	let panel_width = 50,
		panel_height = 50;
	let title_width = 50,
		title_height = 50;

	if (panel_position === "left" || panel_position === "right") {
		const wDesk = getSizeVal(
			elementSettings.gridtofullscreen3d_gridtofullscreen3d_panel_width,
			50,
		);
		const wTab = getSizeVal(
			elementSettings.gridtofullscreen3d_gridtofullscreen3d_panel_width_tablet,
			50,
		);
		const wMobile = getSizeVal(
			elementSettings.gridtofullscreen3d_gridtofullscreen3d_panel_width_mobile,
			50,
		);

		panel_width = wDesk;
		if (deviceMode === "tablet") {
			panel_width = wTab;
		} else if (deviceMode === "mobile") {
			panel_width = wMobile;
		}
		panel_width = Math.max(0.0001, panel_width);
		title_width = 100 - panel_width;
	} else {
		const hDesk = getSizeVal(
			elementSettings.gridtofullscreen3d_gridtofullscreen3d_panel_height,
			50,
		);
		const hTab = getSizeVal(
			elementSettings.gridtofullscreen3d_gridtofullscreen3d_panel_height_tablet,
			50,
		);
		const hMobile = getSizeVal(
			elementSettings.gridtofullscreen3d_gridtofullscreen3d_panel_height_mobile,
			50,
		);

		panel_height = hDesk;
		if (deviceMode === "tablet") {
			panel_height = hTab;
		} else if (deviceMode === "mobile") {
			panel_height = hMobile;
		}
		panel_height = Math.max(0.0001, panel_height);
		title_height = 100 - panel_height;
	}

	const durObj =
		elementSettings[
			dceDynamicPostsSkinPrefix + "gridtofullscreen3d_duration"
		];
	let transitionEffectDuration = 1.8;
	if (durObj && durObj.size) {
		transitionEffectDuration = parseFloat(durObj.size);
	}

	const itemsWrapper = $scope.find(".dce-gridtofullscreen3d-wrapper")[0];
	const thumbs = [
		...itemsWrapper.querySelectorAll(
			"img.grid__item-img:not(.grid__item-img--large)",
		),
	];

	let currentIndex;
	const fullviewItems = [...document.querySelectorAll(".fullview__item")];
	const backToGridCtrl = document.querySelector(".fullview__close");

	// Predefined effects
	const effect1 = {
		activation: { type: "closestCorner" },
		timing: { duration: transitionEffectDuration },
		transformation: { type: "flipX" },
		flipBeizerControls: { c0: { x: 0.4, y: -0.8 }, c1: { x: 0.5, y: 0.9 } },
		onToFullscreenStart: ({ index }) => {
			currentIndex = index;
			if (thumbs[currentIndex]) {
				thumbs[currentIndex].style.opacity = 0;
			}
			toggleFullview();
		},
		onToGridFinish: ({ index, lastIndex }) => {
			if (thumbs[lastIndex]) {
				thumbs[lastIndex].style.opacity = 1;
			}
			fullviewItems[currentIndex].classList.remove(
				"fullview__item--current",
			);
		},
		easings: {
			toFullscreen: "easeOutQuint",
			toGrid: "easeOutQuint",
		},
	};

	const effect2 = {
		activation: { type: "sinX" },
		flipX: false,
		timing: {
			type: "sections",
			sections: 4,
			duration: transitionEffectDuration,
		},
		onToFullscreenStart: ({ index }) => {
			currentIndex = index;
			if (thumbs[currentIndex]) {
				thumbs[currentIndex].style.opacity = 0;
			}
			toggleFullview();
		},
		onToGridFinish: ({ index, lastIndex }) => {
			if (thumbs[lastIndex]) {
				thumbs[lastIndex].style.opacity = 1;
			}
			fullviewItems[currentIndex].classList.remove(
				"fullview__item--current",
			);
		},
		easings: {
			toFullscreen: "easeInOutCubic",
			toGrid: "easeInOutCubic",
		},
	};

	const effect3 = {
		activation: { type: "top" },
		timing: {
			type: "sections",
			sections: 20,
			duration: transitionEffectDuration,
		},
		onToFullscreenStart: ({ index }) => {
			currentIndex = index;
			if (thumbs[currentIndex]) {
				thumbs[currentIndex].style.opacity = 0;
			}
			toggleFullview();
		},
		onToGridFinish: ({ index, lastIndex }) => {
			if (thumbs[lastIndex]) {
				thumbs[lastIndex].style.opacity = 1;
			}
			fullviewItems[currentIndex].classList.remove(
				"fullview__item--current",
			);
		},
		easings: {
			toFullscreen: "easeInOutQuint",
			toGrid: "easeInOutQuint",
		},
	};

	const effect4 = {
		activation: { type: "mouse" },
		timing: { duration: transitionEffectDuration },
		transformation: {
			type: "simplex",
			props: {
				seed: "8000",
				frequencyX: 0.2,
				frequencyY: 0.2,
				amplitudeX: 0.3,
				amplitudeY: 0.3,
			},
		},
		onToFullscreenStart: ({ index }) => {
			currentIndex = index;
			if (thumbs[currentIndex]) {
				thumbs[currentIndex].style.opacity = 0;
			}
			transitionEffect.uniforms.uSeed.value = index * 10;
			toggleFullview();
		},
		onToGridFinish: ({ index, lastIndex }) => {
			if (thumbs[lastIndex]) {
				thumbs[lastIndex].style.opacity = 1;
			}
			fullviewItems[currentIndex].classList.remove(
				"fullview__item--current",
			);
		},
		seed: 800,
		easings: {
			toFullscreen: "easeOutQuad",
			toGrid: "easeInOutQuad",
		},
	};

	const effect5 = {
		activation: { type: "bottom" },
		timing: { duration: transitionEffectDuration },
		transformation: {
			type: "wavy",
			props: {
				seed: "8000",
				frequency: 1,
				amplitude: 0.6,
			},
		},
		onToFullscreenStart: ({ index }) => {
			currentIndex = index;
			if (thumbs[currentIndex]) {
				thumbs[currentIndex].style.opacity = 0;
			}
			transitionEffect.uniforms.uSeed.value = index * 10;
			toggleFullview();
		},
		onToGridFinish: ({ index, lastIndex }) => {
			if (thumbs[lastIndex]) {
				thumbs[lastIndex].style.opacity = 1;
			}
			fullviewItems[currentIndex].classList.remove(
				"fullview__item--current",
			);
		},
		seed: 800,
		easings: {
			toFullscreen: "easeOutQuint",
			toGrid: "easeOutCubic",
		},
	};

	const effect6 = {
		timing: {
			type: "sections",
			sections: 1,
			duration: transitionEffectDuration,
		},
		activation: { type: "mouse" },
		transformation: {
			type: "wavy",
			props: {
				seed: "8000",
				frequency: 0.1,
				amplitude: 1,
			},
		},
		onToFullscreenStart: ({ index }) => {
			currentIndex = index;
			if (thumbs[currentIndex]) {
				thumbs[currentIndex].style.opacity = 0;
			}
			transitionEffect.uniforms.uSeed.value = index * 10;
			toggleFullview();
		},
		onToGridFinish: ({ index, lastIndex }) => {
			if (thumbs[lastIndex]) {
				thumbs[lastIndex].style.opacity = 1;
			}
			fullviewItems[currentIndex].classList.remove(
				"fullview__item--current",
			);
		},
		seed: 800,
		easings: {
			toFullscreen: "easeOutCubic",
			toGrid: "easeInOutCubic",
		},
	};

	const activationType =
		elementSettings[
			dceDynamicPostsSkinPrefix + "gridtofullscreen3d_activations"
		] || "corners";
	const custom_effect = {
		timing: {
			type: "sections",
			sections: 1,
			duration: transitionEffectDuration,
		},
		activation: { type: activationType },
		transformation: {
			type: "wavy",
			props: {
				seed: "8000",
				frequency: 0.1,
				amplitude: 1,
			},
		},
		onToFullscreenStart: ({ index }) => {
			currentIndex = index;
			if (thumbs[currentIndex]) {
				thumbs[currentIndex].style.opacity = 0;
			}
			transitionEffect.uniforms.uSeed.value = index * 10;
			toggleFullview();
		},
		onToGridFinish: ({ index, lastIndex }) => {
			if (thumbs[lastIndex]) {
				thumbs[lastIndex].style.opacity = 1;
			}
			fullviewItems[currentIndex].classList.remove(
				"fullview__item--current",
			);
		},
		seed: 800,
		easings: {
			toFullscreen: "easeOutCubic",
			toGrid: "easeOutCubic",
		},
	};

	// Determine which effect to use
	const effectName =
		elementSettings[
			dceDynamicPostsSkinPrefix + "gridtofullscreen3d_effects"
		] || "effect1";
	let settings_effect;
	switch (effectName) {
		case "effect1":
			settings_effect = effect1;
			break;
		case "effect2":
			settings_effect = effect2;
			break;
		case "effect3":
			settings_effect = effect3;
			break;
		case "effect4":
			settings_effect = effect4;
			break;
		case "effect5":
			settings_effect = effect5;
			break;
		case "effect6":
			settings_effect = effect6;
			break;
		case "custom_effect":
			settings_effect = custom_effect;
			break;
		default:
			settings_effect = effect1;
			break;
	}

	function createEffect(options) {
		const eff = new GridToFullscreenEffect(
			document.getElementById("fullscreen-effect"),
			wrapperEl,
			gridTarget,
			Object.assign(
				{
					scrollContainer: window,
					onToGridStart: ({ index }) => {},
				},
				options,
			),
		);
		return eff;
	}

	const transitionEffect = createEffect(settings_effect);
	transitionEffect.init();

	const toggleFullview = () => {
		if (transitionEffect.isFullscreen) {
			anime({
				targets: fullviewItems[currentIndex].querySelector(
					".fullview__item-title",
				),
				duration: 200,
				easing: "easeOutQuad",
				opacity: 0,
				translateX: "5%",
			});

			if (
				elementSettings[
					dceDynamicPostsSkinPrefix + "gridtofullscreen3d_template"
				]
			) {
				if (panel_position === "left" || panel_position === "right") {
					anime({
						targets: fullviewItems[currentIndex].querySelector(
							".fullview__item-box",
						),
						duration: 600,
						easing: "easeInOutExpo",
						width: "0%",
						complete: function () {
							transitionEffect.toGrid();
							$("body").removeClass("dce-fullview-open");
						},
					});
				} else {
					anime({
						targets: fullviewItems[currentIndex].querySelector(
							".fullview__item-box",
						),
						duration: 600,
						easing: "easeInOutExpo",
						height: "0%",
						complete: function () {
							transitionEffect.toGrid();
							$("body").removeClass("dce-fullview-open");
						},
					});
				}
			} else {
				transitionEffect.toGrid();
				$("body").removeClass("dce-fullview-open");
			}

			anime({
				targets: backToGridCtrl,
				duration: 500,
				easing: "easeOutQuad",
				opacity: 0,
				scale: 0,
			});
		} else {
			fullviewItems[currentIndex].classList.add(
				"fullview__item--current",
			);
			$("body").addClass("dce-fullview-open");

			anime.set(
				fullviewItems[currentIndex].querySelector(
					".fullview__item-title",
				),
				{
					translateX: "5%",
					opacity: 0,
				},
			);
			anime({
				targets: fullviewItems[currentIndex].querySelector(
					".fullview__item-title",
				),
				duration: 800,
				easing: "easeOutExpo",
				translateX: "0%",
				opacity: 1,
				delay: transitionEffectDuration * 600,
			});

			if (
				elementSettings[
					dceDynamicPostsSkinPrefix + "gridtofullscreen3d_template"
				]
			) {
				if (panel_position === "left" || panel_position === "right") {
					anime({
						targets: fullviewItems[currentIndex].querySelector(
							".fullview__item-box",
						),
						duration: 800,
						easing: "easeInOutExpo",
						width: panel_width + "%",
						delay: transitionEffectDuration * 900,
					});
					anime({
						targets: fullviewItems[currentIndex].querySelector(
							".fullview__item-title",
						),
						duration: 0,
						width: 100 - panel_width + "%",
					});
				} else {
					anime({
						targets: fullviewItems[currentIndex].querySelector(
							".fullview__item-box",
						),
						duration: 800,
						easing: "easeInOutExpo",
						height: panel_height + "%",
						delay: transitionEffectDuration * 900,
					});
					anime({
						targets: fullviewItems[currentIndex].querySelector(
							".fullview__item-title",
						),
						duration: 0,
						height: 100 - panel_height + "%",
					});
				}
			}
			anime.set(backToGridCtrl, { scale: 0, opacity: 0 });
			anime({
				targets: backToGridCtrl,
				duration: 800,
				easing: "easeOutExpo",
				opacity: 1,
				scale: 1,
				delay: transitionEffectDuration * 500,
			});
		}
	};

	backToGridCtrl.addEventListener("click", () => {
		if (transitionEffect.isAnimating) return false;
		toggleFullview();
		return false;
	});

	document.addEventListener("keyup", (e) => {
		if (transitionEffect.isAnimating) return;
		if (e.key === "Escape") {
			toggleFullview();
		}
	});

	let images = [];
	const $posts = $scope.find(".dce-post-item");

	$posts.each(function (idx, postEl) {
		let $imageLink = $(postEl).find(".dce-post-image");
		let smallImg = $(postEl)
			.find("img.grid__item-img:not(.grid__item-img--large)")
			.get(0);
		let largeImg = $(postEl).find("img.grid__item-img--large").get(0);

		let smallObj = { element: smallImg, image: smallImg || null };
		let largeObj = { element: largeImg, image: largeImg || null };
		images.push({ small: smallObj, large: largeObj });

		if (smallImg || largeImg) {
			$imageLink.on("click", function (ev) {
				ev.preventDefault();
				ev.stopPropagation();
				if (
					!transitionEffect.isAnimating &&
					!transitionEffect.isFullscreen
				) {
					transitionEffect.toFullscreen(idx, ev);
				}
				return false;
			});
		}
	});

	transitionEffect.createTextures(images);

	if (typeof dceDynamicPostsGrid === "function") {
		dceDynamicPostsGrid($scope, $);
	}
};

jQuery(window).on("elementor/frontend/init", function () {
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-dynamicposts-v2.gridtofullscreen3d",
		dceDynamicPostsGridToFullscreen3D,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-woo-products-cart.gridtofullscreen3d",
		dceDynamicPostsGridToFullscreen3D,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-dynamic-woo-products.gridtofullscreen3d",
		dceDynamicPostsGridToFullscreen3D,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-dynamic-show-favorites.gridtofullscreen3d",
		dceDynamicPostsGridToFullscreen3D,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-my-posts.gridtofullscreen3d",
		dceDynamicPostsGridToFullscreen3D,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-sticky-posts.gridtofullscreen3d",
		dceDynamicPostsGridToFullscreen3D,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-search-results.gridtofullscreen3d",
		dceDynamicPostsGridToFullscreen3D,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-metabox-relationship.gridtofullscreen3d",
		dceDynamicPostsGridToFullscreen3D,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-acf-relationship.gridtofullscreen3d",
		dceDynamicPostsGridToFullscreen3D,
	);
});
