(function ($) {
	'use strict';

	var $activeOverlay = null;

	function closeDialog() {
		if (!$activeOverlay) {
			return;
		}

		$activeOverlay.remove();
		$activeOverlay = null;
		$('body').removeClass('menora-payment-dialog-open');
		$(document).off('keydown.menoraPaymentDialog');
	}

	function buildDialog(payment) {
		var $overlay = $('<div/>', {
			'class': 'menora-payment-overlay'
		});

		var $box = $('<div/>', {
			'class': 'menora-payment-dialog',
			role: 'dialog',
			'aria-modal': 'true',
			'aria-labelledby': 'menora-payment-title'
		});

		var $closeBtn = $('<button/>', {
			type: 'button',
			'class': 'menora-payment-close',
			'aria-label': 'סגירה',
			html: '&times;'
		}).on('click', closeDialog);

		var $title = $('<h3/>', {
			'class': 'menora-payment-title',
			id: 'menora-payment-title',
			text: 'ההזמנה נקלטה בהצלחה'
		});

		var $text = $('<p/>', {
			'class': 'menora-payment-text',
			text: 'אפשר לבצע את התשלום באשראי באתר מנורה.'
		});

		var $button = $('<a/>', {
			'class': 'menora-payment-button',
			href: payment.payment_link,
			target: '_blank',
			rel: 'noopener noreferrer',
			text: payment.button_text || 'מעבר לתשלום באתר מנורה'
		});

		$box.append($closeBtn, $title, $text, $button);
		$overlay.append($box);

		// Intentionally persistent: clicking the backdrop must NOT close the dialog,
		// only the explicit close button (event never reaches the overlay because
		// $box click events don't bubble past it being a separate element, but be explicit anyway).
		$overlay.on('mousedown', function (event) {
			if (event.target === $overlay[0]) {
				event.preventDefault();
			}
		});

		return $overlay;
	}

	function showDialog(payment) {
		if ($activeOverlay) {
			return;
		}

		$activeOverlay = buildDialog(payment);
		$('body').append($activeOverlay).addClass('menora-payment-dialog-open');

		// Block Escape from closing it - the user must use the close button.
		$(document).on('keydown.menoraPaymentDialog', function (event) {
			if ('Escape' === event.key || 27 === event.keyCode) {
				event.preventDefault();
				event.stopPropagation();
			}
		});
	}

	function initMenoraForm($scope) {
		var $form = $scope.find('.elementor-form');

		if (!$form.length) {
			return;
		}

		$form.on('submit_success', function (event, response) {
			var payment = response && response.data && response.data.menora_payment;

			if (payment && payment.payment_link) {
				showDialog(payment);
			}
		});
	}

	$(window).on('elementor/frontend/init', function () {
		if (typeof elementorFrontend === 'undefined') {
			return;
		}

		elementorFrontend.hooks.addAction('frontend/element_ready/form.default', initMenoraForm);
	});
})(jQuery);
