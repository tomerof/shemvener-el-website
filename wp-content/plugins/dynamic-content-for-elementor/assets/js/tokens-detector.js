/**
 * Tokens Detector - Clear detected tokens functionality
 */
(function($) {
	'use strict';

	$(document).ready(function() {
		$('#dce-clear-tokens-btn').on('click', function(e) {
			e.preventDefault();

			if (!confirm(dceTokensDetector.confirmMessage)) {
				return;
			}

			var $btn = $(this);
			var originalText = $btn.text();
			$btn.prop('disabled', true).text(dceTokensDetector.clearingText);

			$.ajax({
				url: dceTokensDetector.ajaxUrl,
				type: 'POST',
				data: {
					action: 'dce_clear_detected_tokens',
					nonce: $btn.data('nonce')
				},
				success: function(response) {
					if (response.success) {
						location.reload();
					} else {
						alert(response.data.message || dceTokensDetector.errorMessage);
						$btn.prop('disabled', false).text(originalText);
					}
				},
				error: function() {
					alert(dceTokensDetector.ajaxErrorMessage);
					$btn.prop('disabled', false).text(originalText);
				}
			});
		});
	});
})(jQuery);
