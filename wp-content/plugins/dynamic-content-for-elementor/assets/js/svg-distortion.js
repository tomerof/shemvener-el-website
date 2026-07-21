(function ($) {
	var WidgetElements_SvgDistortionHandler = function ($scope, $) {
		var elementSettings = dceGetElementSettings($scope);
		var id_scope = $scope.attr("data-id");
		var image_url = $scope.find(".dce_distortion").attr("data-dispimage");

		if (!image_url) {
			return;
		}

		var feDisp = $scope.find("feDisplacementMap#displacement-map")[0];
		var feImage = $scope.find("feImage#displacement-image")[0];

		var scaleMap = elementSettings.disp_factor.size;
		var scaleImage = elementSettings.disp_scale.size + "%";
		var posImage =
			(100 - Number(elementSettings.disp_scale.size)) / 2 + "%";

		var random_animation = false;
		var random_animation_range = 0;

		if (
			elementSettings.svg_trigger === "rollover" ||
			elementSettings.svg_trigger === "scroll"
		) {
			var scaleMapTo = elementSettings.disp_factor_to.size || 0;
			var scaleImageTo =
				(elementSettings.disp_scale_to.size || "100") + "%";
			var posImageTo =
				(100 - Number(elementSettings.disp_scale_to.size || 100)) / 2 +
				"%";
		}

		if (elementSettings.svg_trigger === "animation") {
			random_animation = Boolean(elementSettings.random_animation);
			if (random_animation) {
				random_animation_range = Number(
					elementSettings.random_animation_range.size,
				);
				var scaleMap_rand_min = Number(
					scaleMap - random_animation_range,
				);
				var scaleMap_rand_max = Number(
					scaleMap + random_animation_range,
				);
				var random_val_1 = scaleMap;
				var random_val_2 = getRandomValue(
					scaleMap_rand_min,
					scaleMap_rand_max,
				);
			}
		}

		if (elementSettings.svg_trigger !== "static") {
			var animation_delay = elementSettings.delay_animation.size || 1;
			var animation_speed = elementSettings.speed_animation.size || 3;
			var easing_animation_ease =
				elementSettings.easing_animation_ease || "Power3";
			var easing_animation =
				elementSettings.easing_animation || "easeInOut";
			var easeFunction = easing_animation_ease + "." + easing_animation;
		}

		var run = $("#dce-svg-" + id_scope).attr("data-run");

		if (elementorFrontend.isEditMode()) {
			anime.remove(feDisp);
			anime.remove(feImage);

			$(
				".elementor-element[data-id=" +
					id_scope +
					"] svg, ." +
					elementSettings.id_svg_class +
					" a",
			).off("mouseenter");
			$(
				".elementor-element[data-id=" +
					id_scope +
					"] svg, ." +
					elementSettings.id_svg_class +
					" a",
			).off("mouseleave");
			$(
				".elementor-element[data-id=" +
					id_scope +
					"] svg, ." +
					elementSettings.id_svg_class +
					" a",
			).off("touchstart");
			$(
				".elementor-element[data-id=" +
					id_scope +
					"] svg, ." +
					elementSettings.id_svg_class +
					" a",
			).off("touchend");
		}

		var tl;
		var tli;

		var ferma = function () {
			if (tl) tl.pause();
			if (tli) tli.pause();
		};
		var riproduci = function () {
			if (tl) tl.play();
			if (tli) tli.play();
		};

		var playShapeEl = function () {
			function repeatOften() {
				if (run !== $("#dce-svg-" + id_scope).attr("data-run")) {
					run = $("#dce-svg-" + id_scope).attr("data-run");
					if (run === "running") {
						riproduci();
					} else {
						ferma();
					}
				}
				requestAnimationFrame(repeatOften);
			}
			requestAnimationFrame(repeatOften);
		};

		var moveFnComplete = function () {
			random_val_1 = random_val_2;
			random_val_2 = getRandomValue(scaleMap_rand_min, scaleMap_rand_max);
			createAnimation(true);
		};

		function createAnimation($random = false) {
			if ($random) {
				tl = anime
					.timeline({
						autoplay: false,
						complete: function () {
							moveFnComplete();
						},
					})
					.add(
						{
							targets: { value: random_val_1 },
							value: random_val_2,
							duration: animation_speed * 1000,
							easing: convertEasing(easeFunction),
							update: function (anim) {
								feDisp.setAttribute(
									"scale",
									anim.animations[0].currentValue,
								);
							},
						},
						0,
					);
			} else {
				tl = anime
					.timeline({
						autoplay: false,
						loop: true,
					})
					.add(
						{
							targets: { value: 0 },
							value: scaleMap,
							duration: animation_speed * 1000,
							easing: convertEasing(easeFunction),
							update: function (anim) {
								feDisp.setAttribute(
									"scale",
									anim.animations[0].currentValue,
								);
							},
						},
						0,
					)
					.add(
						{
							targets: { value: scaleMap },
							value: 0,
							duration: animation_speed * 1000,
							easing: convertEasing(easeFunction),
							update: function (anim) {
								feDisp.setAttribute(
									"scale",
									anim.animations[0].currentValue,
								);
							},
							endDelay: animation_delay * 1000,
						},
						animation_speed * 1000,
					);
			}

			if (run === "paused" && elementorFrontend.isEditMode()) {
				ferma();
			} else {
				riproduci();
			}
		}

		var mouseenterFn = function () {
			tl = anime.timeline({ autoplay: true });
			tli = anime.timeline({ autoplay: true });

			tl.add(
				{
					targets: { value: scaleMap },
					value: scaleMapTo,
					duration: animation_speed * 1000,
					easing: convertEasing(easeFunction),
					update: function (anim) {
						feDisp.setAttribute(
							"scale",
							anim.animations[0].currentValue,
						);
					},
				},
				0,
			);

			tli.add(
				{
					targets: {
						x: parseFloat(posImage),
						y: parseFloat(posImage),
						w: parseFloat(scaleImage),
						h: parseFloat(scaleImage),
					},
					x: parseFloat(posImageTo),
					y: parseFloat(posImageTo),
					w: parseFloat(scaleImageTo),
					h: parseFloat(scaleImageTo),
					duration: animation_speed * 1000,
					easing: convertEasing(easeFunction),
					update: function (anim) {
						feImage.setAttribute(
							"x",
							anim.animations[0].currentValue,
						);
						feImage.setAttribute(
							"y",
							anim.animations[1].currentValue,
						);
						feImage.setAttribute(
							"width",
							anim.animations[2].currentValue + "%",
						);
						feImage.setAttribute(
							"height",
							anim.animations[3].currentValue + "%",
						);
					},
				},
				0,
			);
		};

		var mouseleaveFn = function () {
			tl = anime.timeline({ autoplay: true });
			tli = anime.timeline({ autoplay: true });

			tl.add(
				{
					targets: { value: scaleMapTo },
					value: scaleMap,
					duration: animation_speed * 1000,
					easing: convertEasing(easeFunction),
					update: function (anim) {
						feDisp.setAttribute(
							"scale",
							anim.animations[0].currentValue,
						);
					},
				},
				0,
			);

			tli.add(
				{
					targets: {
						x: parseFloat(posImageTo),
						y: parseFloat(posImageTo),
						w: parseFloat(scaleImageTo),
						h: parseFloat(scaleImageTo),
					},
					x: parseFloat(posImage),
					y: parseFloat(posImage),
					w: parseFloat(scaleImage),
					h: parseFloat(scaleImage),
					duration: animation_speed * 1000,
					easing: convertEasing(easeFunction),
					update: function (anim) {
						feImage.setAttribute(
							"x",
							anim.animations[0].currentValue,
						);
						feImage.setAttribute(
							"y",
							anim.animations[1].currentValue,
						);
						feImage.setAttribute(
							"width",
							anim.animations[2].currentValue + "%",
						);
						feImage.setAttribute(
							"height",
							anim.animations[3].currentValue + "%",
						);
					},
				},
				0,
			);
		};

		var triggerScrollAnimation = function ($el) {
			if ($el) {
				tl = anime.timeline({ autoplay: false });
				tli = anime.timeline({ autoplay: false });

				var runAnim = function (entries, observer) {
					entries.forEach(function (entry) {
						if (entry.isIntersecting) {
							if (
								entry.boundingClientRect.top <
								entry.rootBounds.top
							) {
								tl.add({
									targets: { value: scaleMap },
									value: scaleMapTo,
									duration: animation_speed * 1000,
									delay: animation_delay * 1000,
									easing: convertEasing(easeFunction),
									update: function (anim) {
										feDisp.setAttribute(
											"scale",
											anim.animations[0].currentValue,
										);
									},
								});
								tli.add({
									targets: {
										x: parseFloat(posImage),
										y: parseFloat(posImage),
										w: parseFloat(scaleImage),
										h: parseFloat(scaleImage),
									},
									x: parseFloat(posImageTo),
									y: parseFloat(posImageTo),
									w: parseFloat(scaleImageTo),
									h: parseFloat(scaleImageTo),
									duration: animation_speed * 1000,
									delay: animation_delay * 1000,
									easing: convertEasing(easeFunction),
									update: function (anim) {
										feImage.setAttribute(
											"x",
											anim.animations[0].currentValue,
										);
										feImage.setAttribute(
											"y",
											anim.animations[1].currentValue,
										);
										feImage.setAttribute(
											"width",
											anim.animations[2].currentValue +
												"%",
										);
										feImage.setAttribute(
											"height",
											anim.animations[3].currentValue +
												"%",
										);
									},
								});
								tl.restart();
								tli.restart();
							} else {
								tl.add({
									targets: { value: scaleMapTo },
									value: scaleMap,
									duration: animation_speed * 1000,
									delay: animation_delay * 1000,
									easing: convertEasing(easeFunction),
									update: function (anim) {
										feDisp.setAttribute(
											"scale",
											anim.animations[0].currentValue,
										);
									},
								});
								tli.add({
									targets: {
										x: parseFloat(posImageTo),
										y: parseFloat(posImageTo),
										w: parseFloat(scaleImageTo),
										h: parseFloat(scaleImageTo),
									},
									x: parseFloat(posImage),
									y: parseFloat(posImage),
									w: parseFloat(scaleImage),
									h: parseFloat(scaleImage),
									duration: animation_speed * 1000,
									delay: animation_delay * 1000,
									easing: convertEasing(easeFunction),
									update: function (anim) {
										feImage.setAttribute(
											"x",
											anim.animations[0].currentValue,
										);
										feImage.setAttribute(
											"y",
											anim.animations[1].currentValue,
										);
										feImage.setAttribute(
											"width",
											anim.animations[2].currentValue +
												"%",
										);
										feImage.setAttribute(
											"height",
											anim.animations[3].currentValue +
												"%",
										);
									},
								});
								tl.restart();
								tli.restart();
							}
						}
					});
				};

				var observerOptions = {
					root: null,
					rootMargin: "0px",
					threshold: 0.1,
				};

				var observer = new IntersectionObserver(
					runAnim,
					observerOptions,
				);
				observer.observe($el.get(0));
			}
		};

		if (elementSettings.svg_trigger === "animation") {
			createAnimation(random_animation);
			if (elementorFrontend.isEditMode()) {
				playShapeEl();
			}
		} else if (elementSettings.svg_trigger === "rollover") {
			$(
				".elementor-element[data-id=" +
					id_scope +
					"] svg, ." +
					elementSettings.id_svg_class +
					" a",
			)
				.on("mouseenter", mouseenterFn)
				.on("mouseleave", mouseleaveFn)
				.on("touchstart", mouseenterFn)
				.on("touchend", mouseleaveFn);
		} else if (elementSettings.svg_trigger === "scroll") {
			$("#dce-svg-" + id_scope).attr("data-run", "paused");
			triggerScrollAnimation($("#dce-svg-" + id_scope));
		}

		function getRandomValue(min, max) {
			min = Math.ceil(min);
			max = Math.floor(max);
			return Math.floor(Math.random() * (max - min)) + min;
		}

		function convertEasing(ease) {
			switch (ease) {
				case "Power0.easeNone":
					return "linear";
				case "Power1.easeIn":
					return "easeInCubic";
				case "Power1.easeOut":
					return "easeOutCubic";
				case "Power1.easeInOut":
					return "easeInOutCubic";
				case "Power2.easeIn":
					return "easeInQuart";
				case "Power2.easeOut":
					return "easeOutQuart";
				case "Power2.easeInOut":
					return "easeInOutQuart";
				case "Power3.easeIn":
					return "easeInCubic";
				case "Power3.easeOut":
					return "easeOutCubic";
				case "Power3.easeInOut":
					return "easeInOutCubic";
				case "Power4.easeIn":
					return "easeInQuart";
				case "Power4.easeOut":
					return "easeOutQuart";
				case "Power4.easeInOut":
					return "easeInOutQuart";
				default:
					return "easeInOutQuad";
			}
		}
	};

	$(window).on("elementor/frontend/init", function () {
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/dyncontel-svgdistortion.default",
			WidgetElements_SvgDistortionHandler,
		);
	});
})(jQuery);
