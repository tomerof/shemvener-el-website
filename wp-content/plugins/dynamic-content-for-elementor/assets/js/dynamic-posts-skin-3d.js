var Widget_DCE_Dynamicposts_3d_Handler = function ($scope, $) {
	var elementSettings = dceGetElementSettings($scope);
	var threeDContainer = $scope.find(".dce-posts-container.dce-skin-3d");
	var items3D = $scope.find(".dce-3d-wrapper .dce-item-3d");
	var is3DEnabled = false;
	var isAnimating = false;
	var totalItems = items3D.length;
	var camera, scene, renderer, controls;
	var objects = [];
	var targets = { row: [], circle: [], sphere: [], helix: [], grid: [] };
	var alternateTargets = {
		row: [],
		circle: [],
		sphere: [],
		helix: [],
		grid: [],
	};
	var mousePosX = 0;
	var mousePosY = 0;
	var halfWindowX = window.innerWidth / 2;
	var halfWindowY = window.innerHeight / 2;

	// Default layout values
	var planeWidth =
		Number(elementSettings[dceDynamicPostsSkinPrefix + "size_plane_3d"]) ||
		320;
	var planeGap = 400;
	var distance3D = 1000;
	var diameter = (totalItems * (planeWidth + planeGap)) / Math.PI;
	var radius = diameter / 2;
	var cameraOffsetY = 300;
	var enableBlur =
		Boolean(elementSettings[dceDynamicPostsSkinPrefix + "blur_depth_3d"]) ||
		false;

	var layoutType =
		elementSettings[dceDynamicPostsSkinPrefix + "type_3d"] || "circle";
	var arrangement = layoutType === "fila" ? targets.row : targets.circle;

	var currentIndex = 0;

	initializeScene();
	animateScene();

	function initializeScene() {
		scene = new THREE.Scene();
		setupObjects();
		buildShapes();
		setupCameraAndRenderer();
		setupControls();
		resetCamera();
		applyTransform(arrangement, 2000);
		addEventListeners();

		setTimeout(function () {
			Widget_DCE_Dynamicposts_base_Handler($scope, $);
		}, 300);

		if (
			elementSettings[
				dceDynamicPostsSkinPrefix + "3d_center_at_start"
			] === "yes"
		) {
			centerItem(0, 1000);
		}
	}

	function setupObjects() {
		for (var i = 0; i < totalItems; i++) {
			var wrapper = document.createElement("div");
			wrapper.className = "dce-3d-element dce-3d-element-" + i;
			items3D.eq(i).detach().appendTo($(wrapper));

			var linkArea = document.createElement("div");
			linkArea.className = "dce-3d-linkarea";
			wrapper.appendChild(linkArea);

			var cssObj = new THREE.CSS3DObject(wrapper);
			cssObj.position.x = Math.random() * 10000 - 6000;
			cssObj.position.y = Math.random() * 10000 - 6000;
			cssObj.position.z = Math.random() * 10000 - 6000;
			scene.add(cssObj);

			objects.push(cssObj);
		}
	}

	function buildShapes() {
		var tempVec = new THREE.Vector3();
		var positionTracker = 0;

		// Sphere
		for (var i = 0; i < objects.length; i++) {
			var phi = Math.acos(-1 + (2 * i) / objects.length);
			var theta = Math.sqrt(objects.length * Math.PI) * phi;
			var sphereObject = new THREE.Object3D();
			sphereObject.position.setFromSphericalCoords(800, phi, theta);
			tempVec.copy(sphereObject.position).multiplyScalar(2);
			sphereObject.lookAt(tempVec);
			targets.sphere.push(sphereObject);
		}

		// Helix
		for (var j = 0; j < objects.length; j++) {
			var angleH = j * 0.175 + Math.PI;
			var yPos = -(j * 18) + 450;
			var helixObj = new THREE.Object3D();
			helixObj.position.setFromCylindricalCoords(900, angleH, yPos);
			tempVec.x = helixObj.position.x * 2;
			tempVec.y = helixObj.position.y;
			tempVec.z = helixObj.position.z * 2;
			helixObj.lookAt(tempVec);
			targets.helix.push(helixObj);
		}

		// Row
		for (var k = 0; k < objects.length; k++) {
			var rowObj = new THREE.Object3D();
			rowObj.position.x = 0;
			rowObj.position.y = 0;
			rowObj.position.z = positionTracker;
			positionTracker -= distance3D + planeGap;
			targets.row.push(rowObj);
		}

		// Circle
		var angleOffset = 0;
		for (var c = 0; c < objects.length; c++) {
			targets.circle.push(getCirclePosition(c, radius));
			alternateTargets.circle.push(
				getCirclePosition(c, radius + distance3D),
			);
			angleOffset += (2 * Math.PI) / totalItems;
		}

		// Grid
		for (var g = 0; g < objects.length; g++) {
			var gridObj = new THREE.Object3D();
			gridObj.position.x = (g % 5) * 200 - 400;
			gridObj.position.y = -(Math.floor(g / 5) % 5) * 200 + 400;
			gridObj.position.z = Math.floor(g / 25) * 1000 - 2000;
			targets.grid.push(gridObj);
		}
	}

	function getCirclePosition(index, r) {
		var stepAngle = (Math.PI * 2) / totalItems;
		var angle = stepAngle * index + Math.PI / 2;
		var circleObj = new THREE.Object3D();
		circleObj.position.x = r * Math.cos(angle);
		circleObj.position.y = 0;
		circleObj.position.z = r * Math.sin(angle);
		return circleObj;
	}

	function setupCameraAndRenderer() {
		camera = new THREE.PerspectiveCamera(
			40,
			window.innerWidth / window.innerHeight,
			1,
			10000,
		);
		renderer = new THREE.CSS3DRenderer();
		renderer.setSize(
			$("#dce-scene-3d-container")[0].clientWidth,
			window.innerHeight,
		);
		$scope
			.find("#dce-scene-3d-container")[0]
			.appendChild(renderer.domElement);
	}

	function setupControls() {
		if (layoutType === "circle") {
			controls = new THREE.OrbitControls(camera, renderer.domElement);
			controls.minDistance = -diameter;
			controls.maxDistance = diameter + distance3D;
		} else if (layoutType === "fila") {
			controls = new THREE.MapControls(camera, renderer.domElement);
		}

		controls.enableDamping = true;
		controls.dampingFactor = 0.05;
		controls.enableZoom = false;
		controls.autoRotate = false;
		controls.screenSpacePanning = true;
		controls.maxPolarAngle = Math.PI / 1.7;
		controls.addEventListener("change", renderScene);
	}

	function applyTransform(targetArray, durationMs) {
		for (var i = 0; i < objects.length; i++) {
			var currentObj = objects[i];
			var targetObj = targetArray[i];
			transformItem(currentObj, targetObj, durationMs);

			anime({
				targets: currentObj.position,
				duration: durationMs * 2,
				easing: "linear",
				update: function () {
					renderScene();
				},
			});
		}
	}

	function transformItem(obj, target, durationMs) {
		var randomDuration = Math.random() * durationMs + durationMs;

		anime({
			targets: obj.position,
			x: target.position.x,
			y: target.position.y,
			z: target.position.z,
			duration: randomDuration,
			easing: "easeInOutCubic",
		});

		anime({
			targets: obj.rotation,
			x: target.rotation.x,
			y: target.rotation.y,
			z: target.rotation.z,
			duration: randomDuration,
			easing: "easeInOutCubic",
		});
	}

	function centerItem(itemIndex, transitionMs) {
		if (layoutType === "circle") {
			moveCameraTo(
				targets.circle[itemIndex].position.x,
				targets.circle[itemIndex].position.y,
				targets.circle[itemIndex].position.z,
				alternateTargets.circle[itemIndex].position.x,
				alternateTargets.circle[itemIndex].position.y,
				alternateTargets.circle[itemIndex].position.z,
				transitionMs,
			);
		} else if (layoutType === "fila") {
			moveCameraTo(
				targets.row[itemIndex].position.x,
				targets.row[itemIndex].position.y,
				targets.row[itemIndex].position.z,
				targets.row[itemIndex].position.x,
				cameraOffsetY,
				targets.row[itemIndex].position.z + distance3D,
				transitionMs,
			);
		}
	}

	function moveCameraTo(x1, y1, z1, x2, y2, z2, durationMs) {
		$scope.find(".dce-3d-navigation").addClass("dce-pancam-item");

		for (var i = 0; i < objects.length; i++) {
			if (i === currentIndex) {
				objects[i].element.childNodes[1].style.display = "none";
				if (enableBlur) {
					anime({
						targets: objects[i].element,
						filter: "blur(0px)",
						duration: 1000,
						easing: "easeInOutCubic",
					});
				}
			} else {
				objects[i].element.childNodes[1].style.display = "block";
				if (enableBlur) {
					anime({
						targets: objects[i].element,
						filter: "blur(7px)",
						duration: 1000,
						easing: "easeInQuad",
					});
				}
			}
		}

		isAnimating = true;

		anime({
			targets: camera.position,
			x: x2,
			y: y2,
			z: z2,
			duration: durationMs,
			easing: "easeInOutCubic",
			update: function () {
				renderScene();
			},
			complete: function () {
				isAnimating = false;
			},
		});

		anime({
			targets: controls.target,
			x: x1,
			y: y1,
			z: z1,
			duration: durationMs,
			easing: "easeInOutQuart",
		});
	}

	function resetCamera() {
		$scope.find(".dce-3d-navigation").removeClass("dce-pancam-item");

		var camX = 0;
		var camY = cameraOffsetY;
		var camZ = 0;

		if (layoutType === "circle") {
			camX = alternateTargets.circle[currentIndex].position.x;
			camY = cameraOffsetY;
			camZ = alternateTargets.circle[currentIndex].position.z;
		} else if (layoutType === "fila") {
			camX = targets.row[0].position.x;
			camY = cameraOffsetY;
			camZ = targets.row[0].position.z + distance3D;
			currentIndex = 0;
		}

		controls.enableRotate = true;
		is3DEnabled = false;

		anime({
			targets: camera.position,
			x: camX,
			y: camY,
			z: camZ,
			duration: 1000,
			easing: "easeOutCubic",
		});

		anime({
			targets: controls.target,
			x: 0,
			y: 0,
			z: 0,
			duration: 1000,
			easing: "easeOutCubic",
		});

		for (var i = 0; i < objects.length; i++) {
			objects[i].element.childNodes[1].style.display = "block";
			if (enableBlur) {
				applyBlur(objects[i], true);
			}
		}
	}

	function animateScene() {
		requestAnimationFrame(animateScene);
		controls.update();
	}

	function renderScene() {
		for (var i = 0; i < objects.length; i++) {
			if (!is3DEnabled && enableBlur) {
				applyBlur(objects[i], false);
			}
			if (layoutType === "circle") {
				objects[i].lookAt(camera.position);
			} else if (layoutType === "fila" && i === currentIndex) {
				objects[i].lookAt(camera.position);
			}
		}
		renderer.render(scene, camera);
	}

	function applyBlur(obj, animateIt) {
		var cameraWorldPos = new THREE.Vector3().setFromMatrixPosition(
			camera.matrixWorld,
		);
		var dist = cameraWorldPos.distanceTo(obj.position) / distance3D - 1;
		dist = dist * (3 * dist);
		var blurValue = dist.toFixed(2) + "px";

		if (animateIt) {
			anime({
				targets: obj.element,
				filter: "blur(" + blurValue + ")",
				webkitFilter: "blur(" + blurValue + ")",
				duration: 600,
				easing: "linear",
			});
		} else {
			obj.element.style.filter = "blur(" + blurValue + ")";
			obj.element.style.webkitFilter = "blur(" + blurValue + ")";
		}
	}

	function addEventListeners() {
		document.addEventListener("mousemove", onMouseMove, false);
		window.addEventListener("resize", onResize, false);

		if (elementSettings[dceDynamicPostsSkinPrefix + "mousewheel_3d"]) {
			threeDContainer.on("mousewheel DOMMouseScroll", onScrollWheel);
		}

		$scope.find("#dce-scene-3d-container > div")[0].addEventListener(
			"dblclick",
			function (ev) {
				resetCamera();
				$scope
					.find("#dce-scene-3d-container > div")
					.removeClass("hide-cursor");
				ev.stopPropagation();
			},
			false,
		);

		$scope.find("#dce-scene-3d-container > div")[0].addEventListener(
			"mousedown",
			function () {
				if (!is3DEnabled) {
					$(this).addClass("grab");
				}
			},
			false,
		);

		$scope.find("#dce-scene-3d-container > div")[0].addEventListener(
			"mouseup",
			function () {
				if (!is3DEnabled) {
					$(this).removeClass("grab");
				}
			},
			false,
		);

		for (var i = 0; i < objects.length; i++) {
			(function (index) {
				objects[index].element.addEventListener(
					"click",
					function (ev) {
						ev.stopPropagation();
						currentIndex = index;
						centerItem(index, 1000);
						is3DEnabled = true;
						$scope
							.find("#dce-scene-3d-container > div")
							.addClass("hide-cursor");
						controls.enableRotate = false;
					},
					false,
				);
			})(i);
		}

		$scope.find(".dce-3d-navigation .dce-3d-next")[0].addEventListener(
			"click",
			function (evt) {
				evt.stopPropagation();
				currentIndex =
					currentIndex < totalItems - 1 ? currentIndex + 1 : 0;
				centerItem(currentIndex, 1000);
			},
			false,
		);

		$scope.find(".dce-3d-navigation .dce-3d-prev")[0].addEventListener(
			"click",
			function (evt) {
				evt.stopPropagation();
				currentIndex =
					currentIndex > 0 ? currentIndex - 1 : totalItems - 1;
				centerItem(currentIndex, 1000);
			},
			false,
		);

		document.addEventListener("keyup", function (e) {
			if (e.keyCode === 27 && is3DEnabled) {
				resetCamera();
			}
			if (e.keyCode === 39 && is3DEnabled) {
				currentIndex =
					currentIndex > 0 ? currentIndex - 1 : totalItems - 1;
				centerItem(currentIndex, 1000);
			}
			if (e.keyCode === 37 && is3DEnabled) {
				currentIndex =
					currentIndex < totalItems - 1 ? currentIndex + 1 : 0;
				centerItem(currentIndex, 1000);
			}
		});
	}

	function onScrollWheel(e) {
		if (isAnimating) {
			e.preventDefault();
			return;
		}
		var scrollValue =
			e.originalEvent.wheelDelta / 30 || -e.originalEvent.detail;

		if (scrollValue < -1) {
			handleScrollDown();
		} else if (scrollValue > 1) {
			handleScrollUp();
		}
		e.preventDefault();
	}

	function handleScrollDown() {
		if (layoutType === "circle") {
			currentIndex++;
			if (currentIndex >= totalItems) {
				currentIndex = 0;
				if (
					elementSettings[
						dceDynamicPostsSkinPrefix + "mousewheel_3d_stop_at_end"
					] === "yes"
				) {
					threeDContainer.off(
						"mousewheel DOMMouseScroll",
						onScrollWheel,
					);
					return;
				}
			}
		} else if (layoutType === "fila") {
			currentIndex = currentIndex > 0 ? currentIndex - 1 : 0;
		}
		centerItem(currentIndex, 1000);
	}

	function handleScrollUp() {
		if (layoutType === "circle") {
			currentIndex = currentIndex > 0 ? currentIndex - 1 : totalItems - 1;
		} else if (layoutType === "fila") {
			currentIndex++;
			if (currentIndex >= totalItems) {
				currentIndex = 0;
				if (
					elementSettings[
						dceDynamicPostsSkinPrefix + "mousewheel_3d_stop_at_end"
					] === "yes"
				) {
					threeDContainer.off(
						"mousewheel DOMMouseScroll",
						onScrollWheel,
					);
					return;
				}
			}
		}
		centerItem(currentIndex, 1000);
	}

	function onMouseMove(event) {
		mousePosX = (event.clientX - halfWindowX) * 10;
		mousePosY = (event.clientY - halfWindowY) * 10;
	}

	function onResize() {
		camera.aspect =
			$("#dce-scene-3d-container")[0].clientWidth / window.innerHeight;
		camera.updateProjectionMatrix();
		renderer.setSize(
			$("#dce-scene-3d-container")[0].clientWidth,
			window.innerHeight,
		);
		renderScene();
	}
};

jQuery(window).on("elementor/frontend/init", function () {
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-dynamicposts-v2.3d",
		Widget_DCE_Dynamicposts_3d_Handler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-dynamic-woo-products.3d",
		Widget_DCE_Dynamicposts_3d_Handler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-dynamic-show-favorites.3d",
		Widget_DCE_Dynamicposts_3d_Handler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-my-posts.3d",
		Widget_DCE_Dynamicposts_3d_Handler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-sticky-posts.3d",
		Widget_DCE_Dynamicposts_3d_Handler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-search-results.3d",
		Widget_DCE_Dynamicposts_3d_Handler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-metabox-relationship.3d",
		Widget_DCE_Dynamicposts_3d_Handler,
	);
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-acf-relationship.3d",
		Widget_DCE_Dynamicposts_3d_Handler,
	);
});
