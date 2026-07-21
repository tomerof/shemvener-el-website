(function ($) {
	"use strict";

	$(document).ready(function () {
		$("#ooo-rollback-form").on("submit", function (e) {
			e.preventDefault();

			if (!confirm(oooRollback.confirmMessage)) {
				return;
			}

			var $form = $(this);
			var $submit = $form.find('button[type="submit"]');
			var $spinner = $form.find(".spinner");

			$submit.prop("disabled", true);
			$spinner.addClass("is-active");

			$.ajax({
				url: ajaxurl,
				type: "POST",
				data: {
					action: oooRollback.action,
					version: $form.find('select[name="version"]').val(),
					nonce: $form.find('[name="rollback_nonce"]').val(),
				},
				success: function (response) {
					if (response && response.success) {
						alert(response.data);
						window.location.href = "plugins.php";
					} else {
						alert(response.data || oooRollback.unknownError);
					}
				},
				error: function (jqXHR, textStatus, errorThrown) {
					var message = oooRollback.connectionError;
					if (jqXHR.responseJSON && jqXHR.responseJSON.data) {
						message = jqXHR.responseJSON.data;
					}
					alert(message);
					console.error("Rollback error details:", {
						status: textStatus,
						error: errorThrown,
						response: jqXHR.responseText,
					});
				},
				complete: function () {
					$submit.prop("disabled", false);
					$spinner.removeClass("is-active");
				},
			});
		});
	});
})(jQuery);
