(function ($) {
	var WidgetElements_SvgMorphHandler = function ($scope, $) {
		var elementSettings = dceGetElementSettings($scope);
		var id_scope = $scope.attr("data-id");
		var trigger_svg = elementSettings.svg_trigger;
		var playpause_control = elementSettings.playpause_control || "paused";
		var run = $("#dce-svg-" + id_scope).attr("data-run");
		var is_running = false;
		var one_by_one = elementSettings.one_by_one;
		var shape_type = "path";
		var enable_image = elementSettings.enable_image || 0;
		var repeater_name = "repeater_shape_" + shape_type;
		var repeaterShape = elementSettings[repeater_name];
		var contentElemsTotal = repeaterShape.length;
		var shapes = [];
		var dceshape = "#shape-" + id_scope;
		var dceshape_svg = "#dce-svg-" + id_scope;
		var isEditMode = elementorFrontend.isEditMode();

		// Timeline references
		var mainTimeline = null;
		var mainTimelinePos = null;

		// Convert polyline to path format
		function polylineToPath(pointsStr) {
			if (!pointsStr) return "M0,0";
			var coords = pointsStr
				.trim()
				.split(/[\s,]+/)
				.map(parseFloat);
			if (coords.length < 2) return "M0,0";
			var d = "M" + coords[0] + "," + coords[1];
			for (var i = 2; i < coords.length; i += 2) {
				d += " L" + coords[i] + "," + coords[i + 1];
			}
			return d;
		}

		// Convert shape numbers to path format
		function shapeNumbersToPath(str) {
			var raw = (str || "").trim();
			if (!raw) return "M0,0";
			if (raw[0].toUpperCase() === "M") {
				return raw;
			} else {
				return polylineToPath(raw);
			}
		}

		// Create flubber interpolator between two paths
		function createFlubber(pathA, pathB) {
			if (!pathA || !pathB || pathA === pathB) {
				return function () {
					return pathA;
				};
			}
			return window.flubber.interpolate(pathA, pathB, {
				maxSegmentLength: 2,
			});
		}

		// Color interpolation utility
		function interpolateColor(color1, color2, factor) {
			if (color1.charAt(0) === "#") color1 = color1.substring(1);
			if (color2.charAt(0) === "#") color2 = color2.substring(1);

			var r1 = parseInt(color1.substring(0, 2), 16);
			var g1 = parseInt(color1.substring(2, 4), 16);
			var b1 = parseInt(color1.substring(4, 6), 16);

			var r2 = parseInt(color2.substring(0, 2), 16);
			var g2 = parseInt(color2.substring(2, 4), 16);
			var b2 = parseInt(color2.substring(4, 6), 16);

			var r = Math.round(r1 + (r2 - r1) * factor);
			var g = Math.round(g1 + (g2 - g1) * factor);
			var b = Math.round(b1 + (b2 - b1) * factor);

			r = ("0" + r.toString(16)).slice(-2);
			g = ("0" + g.toString(16)).slice(-2);
			b = ("0" + b.toString(16)).slice(-2);

			return "#" + r + g + b;
		}

		// Load shape data from repeater
		function getShapeData() {
			shapes = [];
			var shapeDataList = isEditMode
				? repeaterShape.models
				: repeaterShape;
			var previousPoints = "";
			var defaultSpeed =
				elementSettings.speed_morph && elementSettings.speed_morph.size
					? elementSettings.speed_morph.size
					: 0.7;
			var defaultDuration =
				elementSettings.duration_morph &&
				elementSettings.duration_morph.size
					? elementSettings.duration_morph.size
					: 1;

			$.each(shapeDataList, function (i, el) {
				var atts = isEditMode
					? repeaterShape.models[i].attributes
					: repeaterShape[i];
				var points = atts.shape_numbers || "";
				if (!points) {
					points = previousPoints;
				}
				previousPoints = points;
				var fillColor = atts.fill_color || "#ccc";
				var strokeColor = atts.stroke_color || "#000";
				var strokeW =
					atts.stroke_width && atts.stroke_width.size
						? atts.stroke_width.size
						: 0;
				var shapeX =
					atts.shape_x && atts.shape_x.size ? atts.shape_x.size : 0;
				var shapeY =
					atts.shape_y && atts.shape_y.size ? atts.shape_y.size : 0;
				var shapeRot =
					atts.shape_rotation && atts.shape_rotation.size
						? atts.shape_rotation.size
						: 0;
				var spd =
					atts.speed_morph && atts.speed_morph.size !== ""
						? atts.speed_morph.size
						: defaultSpeed;
				var dur =
					atts.duration_morph && atts.duration_morph.size !== ""
						? atts.duration_morph.size
						: defaultDuration;
				var eas =
					atts.easing_morph_ease || elementSettings.easing_morph_ease;
				var mor = atts.easing_morph || elementSettings.easing_morph;

				shapes.push({
					points: points,
					pathData: {
						speed: spd,
						duration: dur,
						easing: eas,
						morph: mor,
					},
					fill: {
						color: fillColor,
						image: atts.fill_image ? atts.fill_image.id : "",
					},
					stroke: {
						width: strokeW,
						color: strokeColor,
					},
					svg: {
						x: shapeX,
						y: shapeY,
						rotate: shapeRot,
					},
				});
			});

			// Apply initial position and attributes
			if (shapes.length > 0) {
				var initialShape = shapes[0];
				$(dceshape_svg).css(
					"transform",
					`translate(${initialShape.svg.x || 0}px,${initialShape.svg.y || 0}px) rotate(${initialShape.svg.rotate || 0}deg)`,
				);
				$(dceshape).attr({
					"stroke-width": initialShape.stroke.width || 0,
					stroke: initialShape.stroke.color || "#000",
					fill: !enable_image
						? initialShape.fill.color
						: $(dceshape).attr("fill"),
				});
			}
		}

		// Create main animation timelines
		function createMainTimelines() {
			if (mainTimeline) {
				anime.remove(dceshape);
			}
			if (mainTimelinePos) {
				anime.remove(dceshape_svg);
			}
			mainTimeline = null;
			mainTimelinePos = null;

			var loopVal = false;
			var rpt = parseInt(elementSettings.repeat_morph, 10) || 0;
			if (rpt < 0) loopVal = true;
			else if (rpt > 0) loopVal = rpt;

			var doYoyo = !!elementSettings.yoyo;
			var cycleCount = 0;

			var path0 = shapeNumbersToPath(shapes[0].points);
			$(dceshape).attr("d", path0);

			let isReverse = false;

			mainTimeline = anime.timeline({
				autoplay: false,
				loop: false,
				complete: function () {
					if (!doYoyo || isReverse) {
						cycleCount++;
					}

					if (loopVal === true || cycleCount < rpt) {
						if (doYoyo) {
							isReverse = !isReverse;
							mainTimeline.pause();
							mainTimelinePos.pause();
							createSequence(isReverse);
							mainTimeline.play();
							mainTimelinePos.play();
						} else {
							mainTimeline.restart();
							mainTimelinePos.restart();
						}
					} else {
						mainTimeline.pause();
						mainTimelinePos.pause();
						is_running = false;
					}
				},
			});
			mainTimelinePos = anime.timeline({
				autoplay: false,
				loop: false,
			});

			function createSequence(reverse) {
				mainTimeline = anime.timeline({
					autoplay: false,
					loop: false,
					complete: mainTimeline.complete,
				});
				mainTimelinePos = anime.timeline({
					autoplay: false,
					loop: false,
				});

				var steps = [];
				for (let i = 0; i < shapes.length; i++) {
					steps.push(i);
				}

				if (reverse) {
					steps.reverse();
				}

				let basePath = shapeNumbersToPath(shapes[steps[0]].points);
				for (let s = 0; s < steps.length - 1; s++) {
					let currentIndex = steps[s];
					let nextIndex = steps[s + 1];
					let transitionSpeed = shapes[currentIndex].pathData.speed;
					let transitionDelay =
						shapes[currentIndex].pathData.duration;

					let nextPath = shapeNumbersToPath(shapes[nextIndex].points);
					let fillC = shapes[nextIndex].fill.color;
					let strW = shapes[nextIndex].stroke.width || 0;
					let strC = shapes[nextIndex].stroke.color || "#000";
					let flub = createFlubber(basePath, nextPath);

					let currentFillColor = shapes[currentIndex].fill.color;
					let nextFillColor = shapes[nextIndex].fill.color;

					mainTimeline.add(
						{
							duration: transitionSpeed * 1000,
							easing: buildEasing(
								shapes[currentIndex].pathData.morph,
								shapes[currentIndex].pathData.easing,
							),
							update: function (anim) {
								let t = anim.progress / 100;
								let newD = flub(t);

								$(dceshape).attr({
									d: newD,
									"stroke-width":
										shapes[currentIndex].stroke.width +
										(shapes[nextIndex].stroke.width -
											shapes[currentIndex].stroke.width) *
											t,
									stroke: interpolateColor(
										shapes[currentIndex].stroke.color ||
											"#000",
										shapes[nextIndex].stroke.color ||
											"#000",
										t,
									),
								});

								if (!enable_image) {
									$(dceshape).attr(
										"fill",
										interpolateColor(
											shapes[currentIndex].fill.color,
											shapes[nextIndex].fill.color,
											t,
										),
									);
								}

								let x =
									(shapes[currentIndex].svg.x || 0) +
									((shapes[nextIndex].svg.x || 0) -
										(shapes[currentIndex].svg.x || 0)) *
										t;
								let y =
									(shapes[currentIndex].svg.y || 0) +
									((shapes[nextIndex].svg.y || 0) -
										(shapes[currentIndex].svg.y || 0)) *
										t;
								let rot =
									(shapes[currentIndex].svg.rotate || 0) +
									((shapes[nextIndex].svg.rotate || 0) -
										(shapes[currentIndex].svg.rotate ||
											0)) *
										t;
								$(dceshape_svg).css(
									"transform",
									`translate(${x}px,${y}px) rotate(${rot}deg)`,
								);
							},
						},
						`+=${transitionDelay * 1000}`,
					);

					basePath = nextPath;
				}
			}

			createSequence(false);

			if (run === "running") {
				mainTimeline.play();
				mainTimelinePos.play();
				is_running = true;
			}
		}

		function stop() {
			if (mainTimeline) mainTimeline.pause();
			if (mainTimelinePos) mainTimelinePos.pause();
			is_running = false;
		}
		function play() {
			if (mainTimeline) mainTimeline.play();
			if (mainTimelinePos) mainTimelinePos.play();
			is_running = true;
		}
		function pause() {
			if (mainTimeline) mainTimeline.pause();
			if (mainTimelinePos) mainTimelinePos.pause();
			is_running = false;
		}

		function playShapeEl() {
			function watcher() {
				if (run !== $(dceshape_svg).attr("data-run")) {
					getShapeData();
					run = $(dceshape_svg).attr("data-run");
					if (run === "running") play();
					else stop();
				}
				requestAnimationFrame(watcher);
			}
			requestAnimationFrame(watcher);
		}

		function triggerScrollAnimation($el) {
			if (!$el.length) return;

			if (one_by_one) {
				$(dceshape_svg).attr("data-morphid", "0");
			} else {
				is_running = false;
				createMainTimelines();
			}

			var observer = new IntersectionObserver(
				(entries) => {
					entries.forEach((entry) => {
						if (entry.isIntersecting) {
							if (one_by_one) {
								let currentStep =
									parseInt(
										$(dceshape_svg).attr("data-morphid"),
									) || 0;
								if (currentStep < shapes.length - 1) {
									currentStep++;
									$(dceshape_svg).attr(
										"data-morphid",
										currentStep,
									);

									let currentShape = shapes[currentStep - 1];
									let nextShape = shapes[currentStep];
									let flub = createFlubber(
										shapeNumbersToPath(currentShape.points),
										shapeNumbersToPath(nextShape.points),
									);

									let timeline = anime({
										targets: dceshape,
										duration:
											currentShape.pathData.speed * 1000,
										easing: buildEasing(
											currentShape.pathData.morph,
											currentShape.pathData.easing,
										),
										update: function (anim) {
											let t = anim.progress / 100;
											$(dceshape).attr({
												d: flub(t),
												"stroke-width":
													currentShape.stroke.width +
													(nextShape.stroke.width -
														currentShape.stroke
															.width) *
														t,
												stroke: interpolateColor(
													currentShape.stroke.color ||
														"#000",
													nextShape.stroke.color ||
														"#000",
													t,
												),
											});
											if (!enable_image) {
												$(dceshape).attr(
													"fill",
													interpolateColor(
														currentShape.fill.color,
														nextShape.fill.color,
														t,
													),
												);
											}

											let x =
												(currentShape.svg.x || 0) +
												((nextShape.svg.x || 0) -
													(currentShape.svg.x || 0)) *
													t;
											let y =
												(currentShape.svg.y || 0) +
												((nextShape.svg.y || 0) -
													(currentShape.svg.y || 0)) *
													t;
											let rot =
												(currentShape.svg.rotate || 0) +
												((nextShape.svg.rotate || 0) -
													(currentShape.svg.rotate ||
														0)) *
													t;
											$(dceshape_svg).css(
												"transform",
												`translate(${x}px,${y}px) rotate(${rot}deg)`,
											);
										},
									});
								}
							} else {
								if (!is_running) {
									mainTimeline.play();
									mainTimelinePos.play();
									is_running = true;
								}
							}
						} else {
							if (!one_by_one) {
								if (is_running) {
									mainTimeline.pause();
									mainTimelinePos.pause();
									is_running = false;
								}
							} else {
								$(dceshape_svg).attr("data-morphid", "0");
								let initialShape = shapes[0];
								$(dceshape).attr({
									d: shapeNumbersToPath(initialShape.points),
									"stroke-width":
										initialShape.stroke.width || 0,
									stroke: initialShape.stroke.color || "#000",
									fill: !enable_image
										? initialShape.fill.color
										: $(dceshape).attr("fill"),
								});
								$(dceshape_svg).css(
									"transform",
									`translate(${initialShape.svg.x || 0}px,${initialShape.svg.y || 0}px) rotate(${initialShape.svg.rotate || 0}deg)`,
								);
							}
						}
					});
				},
				{
					root: null,
					rootMargin: "50px",
					threshold: [0, 0.25, 0.5, 0.75, 1],
				},
			);

			observer.observe($el.get(0));
		}

		function mouseEnterFn() {
			if (!is_running) {
				createMainTimelines();
				mainTimeline.play();
				mainTimelinePos.play();
				is_running = true;
			}
		}

		function mouseLeaveFn() {
			if (is_running) {
				mainTimeline.pause();
				mainTimelinePos.pause();

				let returnTimeline = anime.timeline({
					autoplay: true,
					loop: false,
					complete: function () {
						is_running = false;
					},
				});

				let currentPath = $(dceshape).attr("d");
				let currentStrokeWidth =
					parseFloat($(dceshape).attr("stroke-width")) || 0;
				let currentStrokeColor = $(dceshape).attr("stroke") || "#000";
				let currentFillColor = $(dceshape).attr("fill");
				let currentTransform = $(dceshape_svg).css("transform");
				let currentX = 0,
					currentY = 0,
					currentRotate = 0;

				if (currentTransform && currentTransform !== "none") {
					let matrix = new DOMMatrix(currentTransform);
					currentX = matrix.e;
					currentY = matrix.f;
					currentRotate =
						Math.atan2(matrix.b, matrix.a) * (180 / Math.PI);
				}

				let flub = createFlubber(
					currentPath,
					shapeNumbersToPath(shapes[0].points),
				);

				returnTimeline.add({
					duration: shapes[0].pathData.speed * 1000,
					easing: buildEasing(
						shapes[0].pathData.morph,
						shapes[0].pathData.easing,
					),
					update: function (anim) {
						let t = anim.progress / 100;

						$(dceshape).attr({
							d: flub(t),
							"stroke-width":
								currentStrokeWidth +
								(shapes[0].stroke.width - currentStrokeWidth) *
									t,
							stroke: interpolateColor(
								currentStrokeColor,
								shapes[0].stroke.color || "#000",
								t,
							),
						});

						if (!enable_image) {
							$(dceshape).attr(
								"fill",
								interpolateColor(
									currentFillColor,
									shapes[0].fill.color,
									t,
								),
							);
						}

						let x =
							currentX + ((shapes[0].svg.x || 0) - currentX) * t;
						let y =
							currentY + ((shapes[0].svg.y || 0) - currentY) * t;
						let rot =
							currentRotate +
							((shapes[0].svg.rotate || 0) - currentRotate) * t;
						$(dceshape_svg).css(
							"transform",
							`translate(${x}px,${y}px) rotate(${rot}deg)`,
						);
					},
				});
			}
		}

		if (
			!isEditMode &&
			contentElemsTotal > 1 &&
			trigger_svg === "animation"
		) {
			$(dceshape_svg).attr("data-run", "running");
		}

		setTimeout(function () {
			getShapeData();

			if (trigger_svg === "animation") {
				createMainTimelines();
				if (isEditMode && contentElemsTotal > 1) {
					playShapeEl();
				}
			} else if (trigger_svg === "rollover") {
				mainTimeline = anime.timeline();
				mainTimelinePos = anime.timeline();
				$scope.off(".svgMorph");
				$scope.on("mouseenter.svgMorph", "svg", mouseEnterFn);
				$scope.on("mouseleave.svgMorph", "svg", mouseLeaveFn);
				$scope.on("touchstart.svgMorph", "svg", mouseEnterFn);
				$scope.on("touchend.svgMorph", "svg", mouseLeaveFn);
			} else if (trigger_svg === "scroll") {
				if (one_by_one) {
					mainTimeline = anime.timeline();
					mainTimelinePos = anime.timeline();
				} else {
					if (playpause_control === "paused") {
						stop();
					} else {
						createMainTimelines();
					}
					if (isEditMode && contentElemsTotal > 1) {
						playShapeEl();
					}
				}
				triggerScrollAnimation($(dceshape_svg));
			}
		}, 100);
	};

	$(window).on("elementor/frontend/init", function () {
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/dyncontel-svgmorphing.default",
			WidgetElements_SvgMorphHandler,
		);
	});

	function buildEasing(ease, equation) {
		if (!ease || !equation) return "linear";
		return equation + "." + ease;
	}
})(jQuery);
