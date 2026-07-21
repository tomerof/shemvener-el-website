// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later

// libphonenumber-js is loaded as a dependency and available globally
const libphonenumber = window.libphonenumber || {};
// Examples are loaded via wp_localize_script
const examples = window.libphonenumberExamples || {};

class InternationalPhoneField {
	constructor(wrapper) {
		this.wrapperElement = wrapper;
		let options = {};
		try {
			options = JSON.parse(this.wrapperElement.dataset.options || '{}');
		} catch (e) {
			console.warn('International Phone Field: Invalid options JSON, using defaults');
		}
		this.options = options;
		
		// Find elements rendered by PHP
		this.countrySelectElement = this.wrapperElement.querySelector('.dce-international-phone-country');
		this.phoneInputElement = this.wrapperElement.querySelector('.dce-international-phone-number');
		this.hiddenField = this.wrapperElement.querySelector('.dce-international-phone-value');
		
		if (!this.countrySelectElement || !this.phoneInputElement || !this.hiddenField) {
			console.warn('International Phone Field: Missing required elements');
			return;
		}
	}

	init() {
		// Set placeholder with example number
		this.updatePlaceholder();
		
		// Setup event listeners
		this.setupEventListeners();
		
		// Parse initial value if present
		if (this.hiddenField.value) {
			this.parseInitialValue(this.hiddenField.value);
		}
	}

	setupEventListeners() {
		// Update placeholder and value when country changes
		this.countrySelectElement.addEventListener("change", () => {
			this.updatePlaceholder();
			this.updateValue();
		});

		// Format number as user types
		this.phoneInputElement.addEventListener("input", (e) => {
			try {
				const formatter = this.createAsYouTypeFormatter(this.countrySelectElement.value);
				const formattedValue = formatter.input(e.target.value);

				// Only update if the formatting doesn't remove what the user is typing
				if (
					formattedValue.length >= e.target.value.length ||
					e.inputType === "deleteContentBackward"
				) {
					e.target.value = formattedValue;
				}
			} catch (error) {
				// Formatting failed, keep the raw value
			}

			this.updateValue();
		});

		// Handle form reset: clear hidden field when form is reset
		const form = this.wrapperElement.closest("form");
		if (form) {
			form.addEventListener("reset", () => {
				// Use setTimeout to run after the browser resets the form fields
				setTimeout(() => {
					this.hiddenField.value = "";
					this.updatePlaceholder();
				}, 0);
			});
		}
	}

	createAsYouTypeFormatter(countryCode) {
		if (libphonenumber.AsYouType) {
			return new libphonenumber.AsYouType(countryCode);
		}
		// Fallback: no formatting
		return { input: (v) => v };
	}

	updatePlaceholder() {
		// Skip if example numbers are disabled
		if (!this.options.showExample) {
			return;
		}

		const countryCode = this.countrySelectElement.value;
		try {
			if (libphonenumber.getExampleNumber) {
				const exampleNumber = libphonenumber.getExampleNumber(countryCode, examples);
				if (exampleNumber) {
					this.phoneInputElement.placeholder = exampleNumber.formatNational();
					return;
				}
			}
		} catch (error) {
			// Fallback handled below
		}
		// Keep the default placeholder set by PHP
	}

	updateValue() {
		const countryCode = this.countrySelectElement.value;
		const phoneNumber = this.phoneInputElement.value.trim();

		if (phoneNumber) {
			try {
				if (libphonenumber.parsePhoneNumberFromString) {
					const parsedNumber = libphonenumber.parsePhoneNumberFromString(
						phoneNumber,
						countryCode,
					);

					if (parsedNumber) {
						// Store in E.164 format
						this.hiddenField.value = parsedNumber.format("E.164");
					} else {
						// Fallback: prepend country code
						const selectedOption = this.countrySelectElement.selectedOptions[0];
						const dialCode = selectedOption?.dataset?.prefix || '';
						this.hiddenField.value = dialCode ? `+${dialCode}${phoneNumber.replace(/\D/g, "")}` : phoneNumber;
					}

					// Validate if enabled
					if (this.options.validation) {
						this.validatePhoneNumber();
					}
					return;
				}
			} catch (error) {
				// Fallback below
			}
			// Fallback if libphonenumber is not available
			const selectedOption = this.countrySelectElement.selectedOptions[0];
			const dialCode = selectedOption?.dataset?.prefix || '';
			this.hiddenField.value = dialCode ? `+${dialCode}${phoneNumber.replace(/\D/g, "")}` : phoneNumber;
		} else {
			this.hiddenField.value = "";
		}
	}

	parseInitialValue(value) {
		try {
			if (libphonenumber.parsePhoneNumberFromString) {
				const parsedNumber = libphonenumber.parsePhoneNumberFromString(value);

				if (parsedNumber) {
					// Set the country select to the detected country
					if (parsedNumber.country) {
						this.countrySelectElement.value = parsedNumber.country;
						this.updatePlaceholder();
					}

					// Format the number in national format for the input
					this.phoneInputElement.value = parsedNumber.formatNational();
					return;
				}
			}
		} catch (error) {
			// Fallback if parsing fails
		}

		// Fallback: put number directly in input
		if (value.startsWith("+")) {
			this.phoneInputElement.value = value.substring(1);
		} else {
			this.phoneInputElement.value = value;
		}
	}

	validatePhoneNumber() {
		const countryCode = this.countrySelectElement.value;
		const phoneNumber = this.phoneInputElement.value.trim();

		if (!phoneNumber) {
			this.phoneInputElement.setCustomValidity("");
			return;
		}

		try {
			if (libphonenumber.parsePhoneNumberFromString) {
				const parsedNumber = libphonenumber.parsePhoneNumberFromString(
					phoneNumber,
					countryCode,
				);

				if (!parsedNumber) {
					this.phoneInputElement.setCustomValidity("Invalid phone number format");
				} else if (!parsedNumber.isValid()) {
					this.phoneInputElement.setCustomValidity("This phone number appears to be invalid");
				} else {
					this.phoneInputElement.setCustomValidity("");
				}
				return;
			}
		} catch (error) {
			// Fallback
		}
		// If libphonenumber is not available, don't validate
		this.phoneInputElement.setCustomValidity("");
	}
}

// Initialize a single wrapper
function initializeWrapper(wrapper) {
	if (wrapper.dataset.dcePhoneInitialized) return;
	const field = new InternationalPhoneField(wrapper);
	if (field.countrySelectElement) {
		field.init();
		wrapper.dataset.dcePhoneInitialized = 'true';
	}
}

// Initialize all wrappers in a scope
function initializeInternationalPhoneFields($scope) {
	const container = $scope ? $scope[0] : document;
	container.querySelectorAll('.dce-international-phone-wrapper').forEach(initializeWrapper);
}

// Initialize using Elementor's frontend hooks
jQuery(window).on("elementor/frontend/init", function () {
	elementorFrontend.hooks.addAction(
		"frontend/element_ready/form.default",
		initializeInternationalPhoneFields
	);
});
