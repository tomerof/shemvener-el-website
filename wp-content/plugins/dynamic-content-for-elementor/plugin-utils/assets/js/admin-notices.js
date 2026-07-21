(function ($) {
	$(function () {
		if (typeof AdminNotices === "undefined") {
			console.error("AdminNotices object is not defined");
			return;
		}

		$(".notice.is-dismissible." + AdminNotices.pluginPrefix).on(
			"click",
			".notice-dismiss",
			function (e) {
				e.preventDefault();
				var notice = $(this).closest(".notice");
				var noticeId = notice.data("notice-id");

				if (!noticeId) {
					return;
				}

				wp.ajax
					.post(AdminNotices.pluginPrefix + "_dismiss_notice", {
						notice_id: noticeId,
						nonce: AdminNotices.nonce,
					})
					.done(function () {
						notice.fadeOut();
					})
					.fail(function (error) {
						console.error("Error dismissing notice:", error);
					});
			},
		);
	});
})(jQuery);
