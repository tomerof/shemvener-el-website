"use strict";
(function () {
	async function initializeAutocompleteFields($form) {
		const $outWrapper = $form.find(".elementor-form-fields-wrapper");
		let autocompleteFields = $outWrapper.attr("data-autocomplete-fields");
		if (autocompleteFields === undefined) {
			return; // no autocomplete fields.
		}
		
		// Check if Google Maps API is available
		if (typeof google === 'undefined' || !google.maps) {
			console.error('Google Maps API not available');
			return;
		}

		let Autocomplete;
		
		// Try modern importLibrary approach first (Google Maps API v3.50+ - 2022)
		if (google.maps.importLibrary) {
			try {
				const placesLib = await google.maps.importLibrary("places");
				Autocomplete = placesLib.Autocomplete;
			} catch (error) {
				console.warn('Failed to load Places with importLibrary, falling back to legacy method:', error);
			}
		} else {
			console.warn('google.maps.importLibrary not available, falling back to legacy method');
			
		}
		
		// Final check if Autocomplete is available
		if (!Autocomplete) {
			console.error('Places Autocomplete not available - neither modern nor legacy method worked');
			return;
		}

		try {
			autocompleteFields = JSON.parse(autocompleteFields);
			for (let field of autocompleteFields) {
				let el = $form.find(`[name=form_fields\\[${field.id}\\]]`)[0];
				let autocomplete = new Autocomplete(el, {
					types: ["geocode"],
				});
				autocomplete.setFields(["address_component"]);
				if (field.country !== undefined) {
					autocomplete.setComponentRestrictions({
						country: field.country,
					});
				}
			}
		} catch (error) {
			console.error('Error initializing autocomplete fields:', error);
		}
	}

	function autocompleteCallback($form) {
		// google api might loaded before or after this script based on third
		// party plugins. So we take both cases into account:
		if (typeof google !== 'undefined' && google.maps) {
			initializeAutocompleteFields($form);
		} else {
			window.addEventListener("dce-google-maps-api-loaded", () =>
				initializeAutocompleteFields($form),
			);
		}
	}

	jQuery(window).on("elementor/frontend/init", function () {
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/form.default",
			autocompleteCallback,
		);
	});
})();
