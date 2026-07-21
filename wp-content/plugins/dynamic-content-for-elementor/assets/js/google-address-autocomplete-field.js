async function fireGoogleAddressAutocompleteField(wrapper, inputEl) {
	if (!inputEl) return console.warn('Input not found for autocomplete');

	// Prevent double initialization (important for multi-step forms)
	if (inputEl.nextElementSibling?.classList.contains('dce-google-autocomplete-wrapper')) {
		return;
	}

	const inputId = inputEl.id;

	// Create wrapper immediately with loading text
	const autocompleteWrapper = document.createElement('div');
	autocompleteWrapper.className = 'dce-google-autocomplete-wrapper';
	autocompleteWrapper.setAttribute('data-loading-text', 'Loading...');
	
	// Apply color scheme
	const colorScheme = inputEl.getAttribute('data-color-scheme') || 'light';
	autocompleteWrapper.setAttribute('data-color-scheme', colorScheme);
	
	inputEl.parentNode.insertBefore(autocompleteWrapper, inputEl.nextSibling);

	// Country restrictions
	let countryRestrictions = [];
	if (inputEl.hasAttribute('data-google-autocomplete-fields')) {
		try {
			countryRestrictions = JSON.parse(inputEl.getAttribute('data-google-autocomplete-fields'));
		} catch (e) {
			console.error('Error parsing countryRestrictions:', e);
		}
	}

	// Check if Google Maps API is available
	if (typeof google === 'undefined' || !google.maps) {
		console.error('Google Maps API not available for address autocomplete');
		autocompleteWrapper.setAttribute('data-loading-text', 'Error: Google Maps not available');
		return;
	}

	let PlaceAutocompleteElement;

	// Try modern importLibrary approach first (NEW Places API)
	if (google.maps.importLibrary) {
		try {
			const placesLib = await google.maps.importLibrary("places");
			PlaceAutocompleteElement = placesLib.PlaceAutocompleteElement;
		} catch (error) {
			console.warn('Failed to load NEW Places API with importLibrary, falling back to legacy method:', error);
		}
	} else {
		console.warn('google.maps.importLibrary not available, falling back to legacy method');
	}

	// Final check if PlaceAutocompleteElement is available
	if (!PlaceAutocompleteElement) {
		autocompleteWrapper.setAttribute('data-loading-text', 'Error: Places API not available');
		console.warn('NEW Places API (PlaceAutocompleteElement) not available - Google Address Autocomplete Field will not work');
		return;
	}

	// Create the PlaceAutocompleteElement (NEW Places API)
	const placeEl = new PlaceAutocompleteElement();
	placeEl.style.width = '100%';
	
	// Copy placeholder from original input
	const placeholder = inputEl.getAttribute('placeholder');
	if (placeholder) {
		placeEl.setAttribute('placeholder', placeholder);
	}

	// Save the reference ID
	if (inputId) {
		placeEl.dataset.autocompleteId = inputId;
	}

	// Apply country restrictions
	if (countryRestrictions.length > 0) {
		placeEl.includedRegionCodes = countryRestrictions;
	}

	// Add the Google component to the wrapper (already in DOM with loading text)
	autocompleteWrapper.appendChild(placeEl);

	// Selection handling
	placeEl.addEventListener('gmp-select', async ({ placePrediction }) => {
		const place = placePrediction.toPlace();
		try {
			await place.fetchFields({
				fields: ['formattedAddress', 'location', 'displayName', 'addressComponents']
			});
		} catch (error) {
			console.error('Error loading place data:', error);
			return;
		}

		const addressComponents = place.addressComponents;

		const autofillMap = {
			street: getComponent(addressComponents, 'route'),
			street_number: getComponent(addressComponents, 'street_number'),
			postal_code: getComponent(addressComponents, 'postal_code'),
			city: getComponent(addressComponents, 'locality'),
			province: getComponent(addressComponents, 'administrative_area_level_2'),
			region: getComponent(addressComponents, 'administrative_area_level_1'),
			country: getComponent(addressComponents, 'country')
		};

		const autocompleteId = placeEl.dataset.autocompleteId;
		
		const form = autocompleteWrapper.closest('form');
		if (!form) {
			console.warn('Form not found for autocomplete field');
			return;
		}

		// Find all fields in the form
		const allFields = Array.from(form.querySelectorAll('input, select, textarea'));
		const autocompleteIndex = allFields.findIndex(field => field.id === autocompleteId);

		if (autocompleteIndex === -1) return;

		const targetMap = new Map();
		let foundOtherAutocomplete = false;

		for (let i = autocompleteIndex + 1; i < allFields.length; i++) {
			const target = allFields[i];

			// If you find another main autocomplete, mark it and skip it but continue scanning
			if (target.classList.contains('data-google-autocomplete-fields')) {
				foundOtherAutocomplete = true;
				continue;
			}

			// If it's a target field
			if (target.hasAttribute('data-autofill-type')) {
				const trigger = target.getAttribute('data-trigger-selector');
				
				// Zone-based logic: after finding another autocomplete, only process fields with matching trigger
				if (foundOtherAutocomplete && trigger !== autocompleteId) continue;
				// Before finding another autocomplete, skip only fields with different triggers
				if (!foundOtherAutocomplete && trigger && trigger !== autocompleteId) continue;

				const types = (target.getAttribute('data-autofill-type') || '')
					.split(',')
					.map(s => s.trim())
					.filter(Boolean);

				const parts = types.map(t => autofillMap[t]).filter(Boolean);
				if (parts.length) {
					const separator = target.getAttribute('data-autofill-separator') || ', ';
					targetMap.set(target, parts.join(separator));
				}
			}
		}

		// Set the values
		for (const [target, combinedValue] of targetMap.entries()) {
			target.value = combinedValue;
			// Trigger change event for form validation
			target.dispatchEvent(new Event('change', { bubbles: true }));
		}
		
		inputEl.value = place.formattedAddress;
		// Trigger change event for form validation
		inputEl.dispatchEvent(new Event('change', { bubbles: true }));
	});
}

function getComponent(components, type) {
	return components.find(c => c.types.includes(type))?.longText || '';
}

async function initializeGoogleAutocompleteFields($scope) {
	const fields = $scope[0].querySelectorAll('.data-google-autocomplete-fields');
	
	for (const input of fields) {
		await fireGoogleAddressAutocompleteField(input.closest('.elementor-field-group'), input);
	}
}

function googleAutocompleteFieldCallback($scope) {
	// google api might be loaded before or after this script based on third
	// party plugins. So we take both cases into account:
	if (typeof google !== 'undefined' && google.maps?.importLibrary) {
		initializeGoogleAutocompleteFields($scope);
	} else {
		window.addEventListener("dce-google-maps-api-loaded", () =>
			initializeGoogleAutocompleteFields($scope),
		);
	}
}

jQuery(window).on("elementor/frontend/init", function () {
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/form.default",
		googleAutocompleteFieldCallback,
	);
});
