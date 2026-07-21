jQuery(window).on("elementor/frontend/init", () => {
	class LazyIframe extends elementorModules.frontend.handlers.Base {
		onInit() {
			super.onInit();
			const iframe = this.$element.find("iframe")[0];
			if (!iframe) {
				console.error("Iframe not found");
				return;
			}
			const originalSrc = iframe.dataset.src;
			if (!originalSrc) {
				console.error("No data-src found for iframe");
				return;
			}
			if ("IntersectionObserver" in window) {
				const iframeObserver = new IntersectionObserver((entries) => {
					entries.forEach((entry) => {
						if (entry.isIntersecting) {
							this.loadIframe(iframe, originalSrc);
							iframeObserver.unobserve(iframe);
						}
					});
				});
				iframeObserver.observe(iframe);
			} else {
				console.warn(
					"Browser does not support IntersectionObserver. Loading iframe immediately.",
				);
				this.loadIframe(iframe, originalSrc);
			}
		}

		loadIframe(iframe, src) {
			iframe.onload = () => {
				iframe.classList.add("loaded");
			};
			iframe.src = src;
			iframe.removeAttribute("data-src");
		}
	}

	const addHandler = ($element) => {
		elementorFrontend.elementsHandler.addHandler(LazyIframe, { $element });
	};

	elementorFrontend.hooks.addAction(
		"frontend/element_ready/dce-lazy-iframe.default",
		addHandler,
	);
});
