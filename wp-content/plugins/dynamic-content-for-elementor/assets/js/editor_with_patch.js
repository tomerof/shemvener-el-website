// Load Elementor Icons. Needed because we use fa- icons while elementor uses
// eicon- icons, that do not need loading icon libraries.
jQuery(window).on("elementor:init", function () {
	elementor.iconManager.loadIconLibraries();

	// NOTE: Inline editing patch was removed as it was not needed.
	// The issue was resolved by using editor/documents/switch instead of open
	// and properly waiting for the document:loaded event.
	/*
	// Patch inline editing to prevent errors when switching documents
	// Wait for InlineEditing behavior to be registered
	setTimeout(() => {
		console.log('🔧 DCE: Attempting to patch inline editing behavior...');

		// Hook into Elementor's hooks system to add safety check
		elementor.hooks.addFilter(
			'elements/widget/behaviors',
			function(behaviors) {
				console.log('🔍 DCE: Hook called, checking behaviors:', behaviors);

				// If InlineEditing behavior exists, wrap it with safety checks
				if (behaviors.InlineEditing) {
					console.log('✅ DCE: Found InlineEditing behavior');
					const originalBehavior = behaviors.InlineEditing.behaviorClass;

					// Only patch if not already patched
					if (originalBehavior && !originalBehavior._dcePatched) {
						console.log('🔨 DCE: Patching behavior (not yet patched)');
						// Mark as patched
						originalBehavior._dcePatched = true;

						// Store original methods
						const originalGetEditingSettingKey = originalBehavior.prototype.getEditingSettingKey;
						const originalOnInlineEditingUpdate = originalBehavior.prototype.onInlineEditingUpdate;

						console.log('📋 DCE: Original methods:', {
							hasGetEditingSettingKey: !!originalGetEditingSettingKey,
							hasOnInlineEditingUpdate: !!originalOnInlineEditingUpdate
						});

						// Patch getEditingSettingKey with null check
						if (originalGetEditingSettingKey) {
							originalBehavior.prototype.getEditingSettingKey = function() {
								if (!this.$currentEditingArea || this.$currentEditingArea.length === 0) {
									console.warn('⚠️ DCE: Inline editing area not ready, skipping');
									return null;
								}
								return originalGetEditingSettingKey.call(this);
							};
							console.log('✅ DCE: Patched getEditingSettingKey');
						}

						// Patch onInlineEditingUpdate with null check
						if (originalOnInlineEditingUpdate) {
							originalBehavior.prototype.onInlineEditingUpdate = function() {
								if (!this.$currentEditingArea || this.$currentEditingArea.length === 0) {
									console.warn('⚠️ DCE: Inline editing area not ready, skipping update');
									return;
								}
								return originalOnInlineEditingUpdate.call(this);
							};
							console.log('✅ DCE: Patched onInlineEditingUpdate');
						}

						console.log('✅ DCE: Inline editing behavior patched for safety');
					} else if (originalBehavior && originalBehavior._dcePatched) {
						console.log('ℹ️ DCE: Behavior already patched, skipping');
					} else {
						console.log('⚠️ DCE: originalBehavior is null or undefined');
					}
				} else {
					console.log('⚠️ DCE: InlineEditing behavior not found in:', Object.keys(behaviors));
				}

				return behaviors;
			}
		);

		console.log('✅ DCE: Hook registered successfully');
	}, 100);
	*/
});

function posts_v2_item_id_to_label(id) {
	return posts_v2_item_label_localization[id];
}

// Add WP_Query args in Context Menu
elementor.hooks.addFilter(
	"elements/widget/contextMenuGroups",
	function (groups, element) {
		// Features with collection "dynamic-posts"
		let features = dce_features_collection_dynamic_posts;

		if (!features.includes(element.options.model.get("widgetType"))) {
			return groups;
		}

		groups.push({
			name: "dce_dynamic_collection_query_args",
			actions: [
				{
					name: "dce_dynamic_collection_query_args",
					title: "Get Query Args for Debug",
					icon: "icon-dce-logo-dce",
					callback: function () {
						let query_args = jQuery(element.el)
							.find(".dce-posts-container")
							.data("dce-debug-query-args");
						query_args = JSON.stringify(query_args, null, 2);

						navigator.clipboard
							.writeText(query_args)
							.then(function () {
								elementor.notifications.showToast({
									message:
										"WP_Query Args copied to clipboard",
								});
							});
					},
				},
			],
		});
		return groups;
	},
);

// Loading the section items in the widget Dynamic Posts is extremely slow.
// We thus want to give a feedback to the user that the section is actually loading.
jQuery(window).on("elementor:init", function () {
	elementor.hooks.addAction(
		"panel/open_editor/widget/dce-dynamicposts-v2",
		function (panel, model, view) {
			const callback = () => {
				let $items = panel.$el.find(".elementor-control-section_items");
				if (
					$items.length === 0 ||
					$items.attr("data-dce-loader-set") === "yes"
				) {
					return;
				}
				$items.attr("data-dce-loader-set", "yes");
				let $title = $items.find(".elementor-panel-heading-title");
				let $cc = $items.find(".elementor-control-content");
				// Get elementor original click handler and unbind it. We need
				// it so we can run it by hand after a timeout. Without the
				// timeout it would block page rendering and the loading
				// message wound not appear.
				let handler = jQuery._data($items[0], "events").click[0]
					.handler;
				$items.unbind("click");
				$cc.on("click", () => {
					$title.append(' <em style="color: gray;">loading...</em>');
					setTimeout(handler, 5);
				});
			};
			const config = { childList: true, subtree: true };
			const observer = new MutationObserver(callback);
			observer.observe(panel.$el[0], config);
		},
	);
});

function dce_get_element_id_from_cid(cid) {
	var iFrameDOM = jQuery("iframe#elementor-preview-iframe").contents();
	var eid = iFrameDOM
		.find(".elementor-element[data-model-cid=" + cid + "]")
		.data("id");
	return eid;
}

function dce_getimageSizes(url, callback) {
	var img = new Image();
	img.crossOrigin = "anonymous";
	img.onload = function () {
		var sizes = {};
		sizes.height = this.height;
		sizes.width = this.width;
		sizes.coef = sizes.height / sizes.width;
		callback(sizes);
	};
	img.src = url;
}

jQuery(function () {
	// Add DCE global settings panel menu item
	if (elementor && ElementorConfig.settings.dynamicooo) {
		elementor.on("panel:init", function () {
			var menuSettings = ElementorConfig.settings.dynamicooo;
			var groupName = "style"; // Elementor v3 'Settings' panel group name
			var beforeItem = "editor-preferences"; // Elementor v3 'User Preferences' panel menu name
			var namespace = "panel/" + menuSettings.name + "-settings",
				menuItemOptions = {
					icon: "icon-dce-logo-dce",
					title: menuSettings.panelPage.title,
					type: "page",
					pageName: menuSettings.name + "_settings",
					callback: function callback() {
						return elementorCommon.api.route(
							"".concat(namespace, "/settings"),
						);
					},
				};
			elementorCommon.api.bc.ensureTab(
				namespace,
				"settings",
				menuItemOptions.pageName,
			);
			elementor.modules.layouts.panel.pages.menu.Menu.addItem(
				menuItemOptions,
				groupName,
				beforeItem,
			);
		});
	}

	jQuery(document).on(
		"mousedown",
		".elementor-control-repeater_shape_path .elementor-repeater-fields, .elementor-control-repeater_shape_polyline .elementor-repeater-fields",
		function () {
			var repeater_index = jQuery(this).index();
			var eid = dce_get_element_id_from_cid(dce_model_cid);
			var iFrameDOM = jQuery(
				"iframe#elementor-preview-iframe",
			).contents();
			var morphed = iFrameDOM.find(
				".elementor-element[data-id=" + eid + "] svg.dce-svg-morph",
			);

			if (morphed.attr("data-run") == "paused")
				morphed.attr("data-morphid", repeater_index);
		},
	);
	jQuery(document).on(
		"change",
		".elementor-control-playpause_control",
		function () {
			var runAnimation =
				elementorFrontend.config.elements.data[dce_model_cid]
					.attributes["playpause_control"];
			var eid = dce_get_element_id_from_cid(dce_model_cid);
			var iFrameDOM = jQuery(
				"iframe#elementor-preview-iframe",
			).contents();
			var morphed = iFrameDOM.find(
				".elementor-element[data-id=" + eid + "] #dce-svg-" + eid,
			);
			morphed.attr("data-run", runAnimation);
		},
	);
	//---- global settings -----
	var inputSelector =
		".elementor-control-selector_wrapper.elementor-control-type-text input, .elementor-control-selector_header.elementor-control-type-text input";
	var detect_content_frame = function ($content) {
		var iFrameDOM = jQuery("iframe#elementor-preview-iframe").contents();
		var classController =
			".elementor-control-dce_smoothtransition_class_controller input, .elementor-control-dce_trackerheader_class_controller input";
		const queryCheck = (s) =>
			document.createDocumentFragment().querySelector(s);
		const isSelectorValid = (selector) => {
			try {
				queryCheck(selector);
			} catch (e) {
				return false;
			}
			return true;
		};
		if (isSelectorValid($content) && typeof $content !== "undefined") {
			var sectorList = $content.split(",");
			var sectorListLength = sectorList.length;
			var countSelectorValid = 0;
			sectorList.forEach(selectorIteration);
			function selectorIteration(value) {
				value = value.trim();
				if (
					iFrameDOM.find(value).length &&
					value.length > 1 &&
					(value.substring(0, 1) == "." ||
						value.substring(0, 1) == "#")
				) {
					countSelectorValid++;
				}
			}
			if (countSelectorValid >= sectorListLength) {
				jQuery(".dce-class-debug")
					.text("Detected")
					.addClass("detected");
				jQuery(classController).val("detected").trigger("input");
			} else {
				jQuery(".dce-class-debug")
					.text("No matches")
					.removeClass("detected");
				jQuery(classController).val("").trigger("input");
			}
		}
	};
	var detect_from_text = function ($target) {
		var selectorVal = $target;
		detect_content_frame(selectorVal);
	};
	jQuery(document).on(
		"mousedown",
		"#elementor-panel-dynamicooo-settings, .elementor-panel-menu-item-undefined",
		function (e) {
			setTimeout(function () {
				detect_from_text(jQuery(inputSelector).val());
			}, 200);
			detect_from_text(jQuery(inputSelector).val());
		},
	);
	jQuery(document).on("input", inputSelector, function () {
		detect_from_text(jQuery(this).val());
	});
});

(function () {
	/**
	 * Open a document as nested (with Save & Back button)
	 * Uses the new $e.run API instead of elementorCommon.api.commands.run
	 * This ensures the document is opened as a nested document with proper back navigation
	 *
	 * @param {number} docId - The document ID to open
	 */
	window.dceOpenNestedDocument = function(docId) {
		console.log('🚀 Opening nested document:', docId);

		// Store the current document ID before opening the new one
		const currentDocId = elementor ? elementor.documents.getCurrentId() : null;
		console.log('📄 Current document ID:', currentDocId);

		// Check if $e is available (new Elementor API)
		if (typeof $e !== 'undefined' && typeof elementor !== 'undefined') {
			try {
				// Setup one-time listener for document:loaded event
				const onDocumentLoaded = function() {
					console.log('✅ Document fully loaded, adding back button');
					addCustomBackButton(currentDocId);

					// Give time for all views to initialize before allowing inline editing
					setTimeout(() => {
						console.log('✅ Document views initialized');
					}, 300);

					// Remove listener after it fires once
					elementor.off('document:loaded', onDocumentLoaded);
				};

				// Listen for document:loaded event
				elementor.once('document:loaded', onDocumentLoaded);

				// First save current document before switching
				$e.run('document/save/auto').then(() => {
					console.log('✅ Current document autosaved');

					// Use 'switch' command instead of 'open' - better for nested editing
					return $e.run('editor/documents/switch', {
						id: docId,
						setAsInitial: false
					});
				}).then(() => {
					console.log('✅ Document switch initiated');
					// Don't add button here - wait for document:loaded event
				}).catch((error) => {
					console.error('❌ Error switching document:', error);
					// Remove listener on error
					elementor.off('document:loaded', onDocumentLoaded);
					fallbackOpenDocument(docId);
				});

			} catch (error) {
				console.error('❌ Error with $e.run:', error);
				fallbackOpenDocument(docId);
			}
		} else {
			console.warn('⚠️ $e not available, using fallback method');
			fallbackOpenDocument(docId);
		}
	};

	/**
	 * Add a custom Back button to the edit area overlay
	 * This mimics the Kit's back button behavior but for nested documents
	 */
	function addCustomBackButton(originalDocId) {
		console.log('🔙 Adding custom back button to return to doc:', originalDocId);

		// Remove existing back button if any
		jQuery('#dce-panel-back-button').remove();

		// Wait a bit for the edit area to be ready
		setTimeout(() => {
			// Check if elementor-embedded-editor exists (when editing nested template)
			const iframe = jQuery('#elementor-preview-iframe');
			const iframeDocument = iframe.contents();
			console.log('iframeDocument', iframeDocument);
			


			//const $embeddedEditor = jQuery('.elementor-edit-mode');
			const $embeddedEditor = iframeDocument.find('.elementor-edit-mode');
			console.log('🔍 Checking for .elementor-edit-mode:', $embeddedEditor);
			if ($embeddedEditor.length) {
				
				console.log('✅ Found .elementor-edit-mode, adding button there');
				const embeddedEditorsingle = $embeddedEditor[0];
				console.log('embeddedEditorsingle' + embeddedEditorsingle);
				

				// Create back button HTML for edit area
				const arrowClass = 'eicon-chevron-' + (elementorCommon.config.isRTL ? 'right' : 'left');
				const backButtonHtml = `
					<div id="dce-panel-back-button" class="elementor-edit-area-back-button" data-target-doc="${originalDocId}" style="
						position: fixed;
						top: 40px;
						left: 20%;
						transform: translateX(-50%);
						z-index: 99999;
						background: #93003c;
						color: white;
						padding: 12px 24px;
						border-radius: 3px;
						cursor: pointer;
						font-size: 14px;
						font-weight: 500;
						display: flex;
						align-items: center;
						gap: 8px;
						box-shadow: 0 2px 8px rgba(0,0,0,0.3);
						transition: all 0.3s ease;
					" onmouseover="this.style.background='#7a0032'" onmouseout="this.style.background='#93003c'">
						<i class="eicon ${arrowClass}" style="font-size: 16px;"></i>
						<span>Save & Back</span>
					</div>
				`;

				// Append to body (so it's always visible)
				jQuery(embeddedEditorsingle).append(backButtonHtml);
				console.log('✅ Custom back button added to edit area');

				// Hide Elementor's default "Edit Page" button to avoid confusion
				iframeDocument.find('.elementor-edit-area-back-button:not(#dce-panel-back-button)').hide();
				console.log('✅ Hidden Elementor default edit button');

				// Add click handler
				jQuery(iframeDocument.find('#dce-panel-back-button')).on('click', function() {
					console.log('🔙 Back button clicked, returning to document:', originalDocId);

					// Save current document and go back
					if (originalDocId) {
						// Show loading state
						jQuery(this).html('<i class="eicon-loading eicon-animation-spin"></i> <span>Saving...</span>');
						//elementorCommon.api.commands.run('editor/documents/close', {id: 8925,mode: 'save'});
						//elementorCommon.api.commands.run('editor/documents/close', {id: 8925,mode: 'save'});
						$e.run('document/save/auto').then(() => {
							console.log('✅ Document autosaved');
							return $e.run('editor/documents/switch', {
								id: originalDocId,
								mode: 'autosave',
								setAsInitial: true
							});
						}).then(() => {
							console.log('✅ Returned to original document');
							// Remove the back button
							jQuery('#dce-panel-back-button').remove();
						}).catch((error) => {
							console.error('❌ Error returning to document:', error);
							// Restore button text on error
							const arrowClass = 'eicon-chevron-' + (elementorCommon.config.isRTL ? 'right' : 'left');
							jQuery('#dce-panel-back-button').html(`<i class="eicon ${arrowClass}"></i> <span>Save & Back</span>`);
						});
					}
				});

			} else {
				console.warn('⚠️ .elementor-edit-area-active not found, trying panel header fallback');

				// Fallback to panel header if edit area not found
				const $panelHeader = jQuery('#elementor-panel-header');
				if ($panelHeader.length) {
					const arrowClass = 'eicon-chevron-' + (elementorCommon.config.isRTL ? 'right' : 'left');
					const backButtonHtml = `
						<button id="dce-panel-back-button" class="elementor-header-button" aria-label="Back to Document" style="order: -1;" data-target-doc="${originalDocId}">
							<i class="elementor-icon ${arrowClass} tooltip-target" aria-hidden="true" data-tooltip="Save & Back"></i>
							<span class="elementor-screen-only">Save & Back</span>
						</button>
					`;

					$panelHeader.prepend(backButtonHtml);
					console.log('✅ Custom back button added to panel header (fallback)');

					// Hide Elementor's default buttons in fallback mode
					jQuery('#elementor-panel-header-add-button, #elementor-panel-header-menu-button').hide();
					console.log('✅ Hidden default panel buttons');

					// Add click handler for fallback
					jQuery('#dce-panel-back-button').on('click', function() {
						console.log('🔙 Back button clicked (fallback), returning to document:', originalDocId);

						if (originalDocId) {
							$e.run('document/save/auto').then(() => {
								console.log('✅ Document autosaved');
								return $e.run('editor/documents/switch', {
									id: originalDocId,
									mode: 'autosave',
									setAsInitial: false
								});
							}).then(() => {
								console.log('✅ Returned to original document');
								jQuery('#dce-panel-back-button').remove();
							}).catch((error) => {
								console.error('❌ Error returning to document:', error);
							});
						}
					});
				} else {
					console.error('❌ Neither edit area nor panel header found');
				}
			}
		}, 500); // Wait 500ms for DOM to be ready
	}

	/**
	 * Clean up back button when switching documents
	 * This is a global listener that will clean up our custom back button
	 */
	if (typeof elementor !== 'undefined') {
		elementor.on('document:loaded', function() {
			// Check if our custom back button exists
			if (jQuery('#dce-panel-back-button').length > 0) {
				console.log('🧹 Document loaded, checking if we need to clean up back button');

				// If we're back to a document that doesn't have the nested document open,
				// remove the back button and restore the normal buttons
				const backButton = jQuery('#dce-panel-back-button');
				if (backButton.length > 0) {
					// Get the original document ID from the button's data
					const targetDocId = parseInt(backButton.attr('data-target-doc'));
					const currentDocId = elementor.documents.getCurrentId();

					// If we're back to the original document, clean up
					if (targetDocId && currentDocId === targetDocId) {
						backButton.remove();

						// Restore panel buttons
						jQuery('#elementor-panel-header-add-button, #elementor-panel-header-menu-button').show();

						// Restore Elementor's default edit button in iframe
						const iframe = jQuery('#elementor-preview-iframe');
						const iframeDocument = iframe.contents();
						iframeDocument.find('.elementor-edit-area-back-button').show();

						console.log('✅ Cleaned up back button, restored to document:', currentDocId);
					}
				}
			}
		});
	}

	/**
	 * Fallback method using old API
	 * This won't show Save & Back but will still open the document
	 *
	 * @param {number} docId - The document ID to open
	 */
	function fallbackOpenDocument(docId) {
		if (typeof elementorCommon !== 'undefined' && elementorCommon.api && elementorCommon.api.commands) {
			try {
				elementorCommon.api.commands.run('editor/documents/open', { id: docId });
				console.log('⚠️ Document opened with fallback method (no Save & Back)');
			} catch (error) {
				console.error('❌ Error opening document with fallback:', error);
			}
		} else {
			console.error('❌ No method available to open document');
		}
	}

	const dce_update_query_btn = (ooo) => {
		var setting = jQuery(ooo).data("setting"),
			query_type = jQuery(ooo).attr("data-query_type"),
			object_type = jQuery(ooo).attr("data-object_type");
		jQuery(ooo).siblings(".dce-elementor-control-quick-edit").remove();
		if (
			jQuery(ooo).val() &&
			(!jQuery.isArray(jQuery(ooo).val()) ||
				(jQuery.isArray(jQuery(ooo).val()) &&
					jQuery(ooo).val().length == 1))
		) {
			var edit_link = "#";
			switch (query_type) {
				case "posts":
					if (!object_type || object_type != "type") {
						edit_link =
							ElementorConfig.home_url +
							"/wp-admin/post.php?post=" +
							jQuery(ooo).val();
						if (object_type == "elementor_library") {
							edit_link += "&action=elementor";
						} else {
							edit_link += "&action=edit";
						}
					}
					break;
				case "users":
					if (!object_type || object_type != "role") {
						edit_link =
							ElementorConfig.home_url +
							"/wp-admin/user-edit.php?user_id=" +
							jQuery(ooo).val();
					}
					break;
				case "terms":
					if (object_type) {
						edit_link =
							ElementorConfig.home_url +
							"/wp-admin/term.php?tag_ID=" +
							jQuery(ooo).val();
						edit_link += "&taxonomy=" + object_type;
					}
					break;
			}
			if (edit_link != "#") {
				jQuery(ooo)
					.parent()
					.after(
						'<div onclick="dceOpenNestedDocument(' + jQuery(ooo).val() + ');" class="dce-elementor-control-quick-edit-wrapper" style="padding: 10px 0; width: 100%;"><button type="button" class="elementor-button elementor-button-default" style="width: 100%;"><i class="eicon-pencil" style="margin-right: 5px;"></i>Quick Edit</button></div>'
					);
					// Old implementation with link:
					// .parent()
					// .append(
					// 	'<div class="elementor-control-unit-1 tooltip-target dce-elementor-control-quick-edit" data-tooltip="Quick Edit"><a href="' +
					// 		edit_link +
					// 		'" target="_blank" class="dce-quick-edit-btn"><i class="eicon-pencil"></i></a></div>',
					// );
			}
		} else {
			var new_link = "#";
			switch (query_type) {
				case "posts":
					if (!object_type || object_type != "type") {
						new_link =
							ElementorConfig.home_url + "/wp-admin/post-new.php";
						if (object_type) {
							new_link += "?post_type=" + object_type;
							if (object_type == "elementor_library") {
								new_link =
									ElementorConfig.home_url +
									"/wp-admin/edit.php?post_type=" +
									object_type +
									"#add_new";
							}
						}
					}
					break;
				case "users":
					if (!object_type || object_type != "role") {
						new_link =
							ElementorConfig.home_url + "/wp-admin/user-new.php";
					}
					break;
				case "terms":
					new_link =
						ElementorConfig.home_url + "/wp-admin/edit-tags.php";
					if (object_type) {
						edit_link += "&taxonomy=" + object_type;
					}
					break;
			}
			if (new_link != "#") {
				jQuery(ooo)
					.parent()
					.prepend(
						'<div class="elementor-control-unit-1 tooltip-target dce-elementor-control-quick-edit" data-tooltip="Add New"><a href="' +
							new_link +
							'" target="_blank" class="dce-quick-edit-btn"><i class="eicon-plus"></i></a></div>',
					);
			}
		}
	};
	const editorInit = () => {
		// FILEBROWSER
		elementor.channels.editor.on(
			"dceFileBrowser:removeMedia",
			(childView) => {
				tinyMCE.editors[0].setContent("");
			},
		);

		elementor.channels.editor.on(
			"dynamicShortcodesWizard::generateShortcode",
			(childView) => {
				wp.ajax
					.post("dce_generate_dynamic_shortcode", {
						settings: childView.container.settings.attributes,
						nonce: dce_editor_config.nonce,
					})
					.done(function (shortcode) {
						childView.container.settings.setExternalChange(
							"shortcode",
							shortcode,
						);
						childView.container.settings.setExternalChange(
							"status",
							"generated",
						);

						container = childView.container;
						const tagText =
								elementor.dynamicTags.tagContainerToTagText(
									container,
								),
							commandArgs = {
								container: container.parent,
								settings: {
									[container.view.options.controlName]:
										tagText,
								},
							};
						$e.run("document/dynamic/settings", commandArgs);
						elementor.notifications.showToast({
							message: "Dynamic Shortcode generated.",
						});
					})
					.fail(function (errorThrown) {
						elementor.notifications.showToast({
							message:
								"ERROR! Dynamic Shortcode not generated. " +
								errorThrown,
						});
						console.log(
							"ERROR! Dynamic Shortcode not generated. " +
								errorThrown,
						);
					});
			},
		);

		elementor.channels.editor.on(
			"dynamicShortcodesWizard::editShortcode",
			(childView) => {
				childView.container.settings.setExternalChange(
					"status",
					"edit",
				);
				elementor.notifications.showToast({
					message: "Dynamic Shortcode edit mode.",
				});
			},
		);

		elementor.channels.editor.on(
			"dynamicShortcodesWizard::startShortcode",
			(childView) => {
				childView.container.settings.setExternalChange(
					"status",
					"initial",
				);
			},
		);

		elementor.channels.editor.on(
			"dynamicShortcodesWizard::copyShortcode",
			(childView) => {
				navigator.clipboard
					.writeText(
						childView.container.settings.attributes.shortcode,
					)
					.then(function () {
						elementor.notifications.showToast({
							message: "Dynamic Shortcode copied to clipboard",
						});
					});
			},
		);
		// Query Control
		var DCEControlQuery = elementor.modules.controls.Select2.extend({
			cache: null,
			isTitlesReceived: false,
			getSelect2Placeholder: function getSelect2Placeholder() {
				var self = this;
				return {
					id: "",
					text: self.model.get("placeholder"), //'All',
				};
			},
			getSelect2DefaultOptions: function getSelect2DefaultOptions() {
				var self = this;
				return jQuery.extend(
					elementor.modules.controls.Select2.prototype.getSelect2DefaultOptions.apply(
						this,
						arguments,
					),
					{
						ajax: {
							transport: function transport(
								params,
								success,
								failure,
							) {
								var data = {
									q: params.data.q,
									query_type: self.model.get("query_type"),
									object_type: self.model.get("object_type"),
								};
								return elementorCommon.ajax.addRequest(
									"dce_query_control_filter_autocomplete",
									{
										data: data,
										success: success,
										error: failure,
									},
								);
							},
							data: function data(params) {
								return {
									q: params.term,
									page: params.page,
								};
							},
							cache: true,
						},
						escapeMarkup: function escapeMarkup(markup) {
							return markup;
						},
						minimumInputLength: 2,
					},
				);
			},
			// translate with an ajax post ids to post titles.
			getValueTitles: function getValueTitles() {
				var self = this;
				var ids = this.getControlValue();
				var queryType = this.model.get("query_type");
				objectType = this.model.get("object_type");
				if (!ids || !queryType) return;
				if (!_.isArray(ids)) {
					ids = [ids];
				}
				// no translation needed for these query types:
				if (
					queryType === "pods" ||
					queryType === "jet" ||
					queryType === "metabox" ||
					queryType === "metabox_relationship" ||
					queryType === "options"
				) {
					let t = {};
					for (id of ids) {
						t[id] = id; // Label is the same as the value.
					}
					self.isTitlesReceived = true;
					self.model.set("options", t);
					self.render();
					return;
				}

				elementorCommon.ajax.loadObjects({
					action: "dce_query_control_value_titles",
					ids: ids,
					data: {
						query_type: queryType,
						object_type: objectType,
						unique_id: "" + self.cid + queryType,
					},
					success: function success(data) {
						self.isTitlesReceived = true;
						self.model.set("options", data);
						self.render();
					},
					before: function before() {
						self.addSpinner();
					},
				});
			},
			addSpinner: function addSpinner() {
				this.ui.select.prop("disabled", true);
				this.$el
					.find(".elementor-control-title")
					.after(
						'<span class="elementor-control-spinner dce-control-spinner">&nbsp;<i class="fa fa-spinner fa-spin"></i>&nbsp;</span>',
					);
			},
			onReady: function onReady() {
				setTimeout(
					elementor.modules.controls.Select2.prototype.onReady.bind(
						this,
					),
				);
				if (this.ui.select) {
					var self = this,
						ids = this.getControlValue(),
						queryType = this.model.get("query_type");
					objectType = this.model.get("object_type");
					jQuery(this.ui.select).attr("data-query_type", queryType);
					if (objectType) {
						jQuery(this.ui.select).attr(
							"data-object_type",
							objectType,
						);
					}
					dce_update_query_btn(this.ui.select);
				}

				if (!this.isTitlesReceived) {
					this.getValueTitles();
				}
			},
			onBeforeDestroy: function onBeforeDestroy() {
				if (this.ui.select.data("select2")) {
					this.ui.select.select2("destroy");
				}

				this.$el.remove();
			},
		});
		// Add Control Handlers
		elementor.addControlView("ooo_query", DCEControlQuery);
		jQuery(document).on(
			"change",
			".elementor-control-type-ooo_query select",
			function () {
				dce_update_query_btn(this);
			},
		);
	};
	if (typeof elementor !== "undefined") {
		editorInit();
	} else {
		jQuery(window).on("elementor:init", editorInit);
	}

	// Auto-detect date format using Elementor button control events
	$run_old_style = false;
	if ($run_old_style) {
		jQuery(window).on("elementor:init", function () {
			// Listen for auto-detect button click (normal mode)
			elementor.channels.editor.on(
				"dce:detectDateFormat",
				function (controlView) {
					handleDateFormatDetection(
						controlView,
						"querydate_field_meta",
						"querydate_field_meta_format",
					);
				},
			);

			// Listen for auto-detect button click (future mode)
			elementor.channels.editor.on(
				"dce:detectDateFormatFuture",
				function (controlView) {
					handleDateFormatDetection(
						controlView,
						"querydate_field_meta_future",
						"querydate_field_meta_future_format",
					);
				},
			);
		});
	}
	

	function handleDateFormatDetection(
		controlView,
		metaFieldSetting,
		formatSetting,
	) {
		const model = controlView.container.settings;
		const postType = model.get("post_type") || "post";
		const metaKey = model.get(metaFieldSetting);

		if (!metaKey) {
			elementor.notifications.showToast({
				message: "Please select a meta field first",
				type: "warning",
			});
			return;
		}

		// Show loading state
		const $btn = controlView.$el.find(".elementor-button");
		$btn.prop("disabled", true);
		const originalText = $btn.text();
		$btn.html(
			'<i class="eicon-loading eicon-animation-spin" style="margin-right: 5px;"></i>Detecting...',
		);

		// Call AJAX to detect format
		detectDateFormatFromButton(
			metaKey,
			postType,
			model,
			formatSetting,
			function () {
				// Re-enable button
				$btn.prop("disabled", false);
				$btn.text(originalText);
			},
		);
	}

	function detectDateFormatFromButton(
		metaKey,
		postType,
		model,
		targetSetting,
		callback,
	) {
		jQuery.ajax({
			url: ajaxurl,
			type: "POST",
			data: {
				action: "dce_detect_date_format",
				meta_key: metaKey,
				post_type: postType,
			},
			success: function (response) {
				if (callback) callback();

				if (response.success && response.data) {
					showFormatConfirmationModal(
						response.data,
						model,
						targetSetting,
					);

				} else {
					// Auto-detection failed
					const errorMsg =
						response.data && response.data.message
							? response.data.message
							: "Unknown error";
					console.log(
						"Date format auto-detection failed:",
						errorMsg,
					);
					elementor.notifications.showToast({
						message:
							"Could not auto-detect date format. Please enter manually.",
						type: "warning",
					});
				}
			},
			error: function () {
				if (callback) callback();
				console.error("AJAX error during date format detection");
				elementor.notifications.showToast({
					message: "Error during auto-detection. Please try again.",
					type: "error",
				});
			},
		});
	}

	function showFormatConfirmationModal(data, model, settingName) {
		const dialog = elementorCommon.dialogsManager.createWidget("confirm", {
			message: `
				<div style="font-family: Arial, sans-serif; line-height: 1.6;">
					<p><strong>Formato rilevato:</strong> <code style="background: #f0f0f0; padding: 2px 6px; border-radius: 3px;">${data.format}</code></p>
					<p><strong>Esempio dal database:</strong> <code style="background: #f0f0f0; padding: 2px 6px; border-radius: 3px;">${data.sample}</code></p>
					<p><strong>Interpretato come:</strong> ${data.readable}</p>
					<p style="color: #666; font-size: 12px;">Da post: "${data.post_title}" (ID: ${data.post_id})</p>
					<hr style="margin: 15px 0; border: none; border-top: 1px solid #ddd;">
					<p><strong>Il formato è corretto?</strong></p>
				</div>
			`,
			headerMessage: "Conferma Formato Data",
			strings: {
				confirm: "Sì, è corretto",
				cancel: "No, inserirò manualmente",
			},
			onConfirm: function () {
				// Set the detected format using setExternalChange
				console.log('model', model);
				console.log("settingName", settingName, "data.format", data.format);
				
				// Use setExternalChange like in other parts of the code
				model.setExternalChange(settingName, data.format);

				// Show success notification
				elementor.notifications.showToast({
					message: `Formato "${data.format}" applicato automaticamente`,
					type: "success",
				});
			},
			onCancel: function () {
				// User will enter manually
				elementor.notifications.showToast({
					message: "Inserisci il formato manualmente",
					type: "info",
				});
			},
		});

		dialog.show();
	}
})();
