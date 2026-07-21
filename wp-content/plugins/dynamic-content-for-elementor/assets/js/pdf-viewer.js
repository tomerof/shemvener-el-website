import * as pdfjsLib from "../node/pdfjs-dist/pdf.min.js";

(function ($) {
	const PdfViewerWidget = function ($scope, $) {
		const container = $scope[0];
		const settings = dceGetElementSettings($scope);
		let pdfUrl = '';
	
		if (settings.source === 'url' && settings.source_url.url) {
			pdfUrl = settings.source_url.url;
		} else if (settings.source === 'media_file' && settings.source_media && settings.source_media.url) {
			pdfUrl = settings.source_media.url;
		}
		if (!pdfUrl) {
			return;
		}
  
		pdfjsLib.GlobalWorkerOptions.workerSrc = dcePdfViewerConfig.workerSrc;
	  
		const pdfState = {
			pdf: null,
			currentPage: 1,
			zoom: dynamicooo.getResponsiveValue(settings, 'zoom', 1) || 1,
		};
	
		pdfjsLib.getDocument(pdfUrl).promise.then((pdfDoc) => {
			pdfState.pdf = pdfDoc;
			renderPage();
		});
	
		if (settings.download_controls === 'yes') {
			const downloadButton = container.getElementsByClassName('dce-pdf-download')[0];
			if (downloadButton) {
			downloadButton.addEventListener('click', () => {
				const link = document.createElement('a');
				const urlValue = typeof pdfUrl === 'object' && pdfUrl.url ? pdfUrl.url : pdfUrl;
				link.href = urlValue;
				link.download = urlValue.split('/').pop();
				link.style.display = 'none';
				document.body.appendChild(link);
				link.click();
				document.body.removeChild(link);
			});
			}
		}
	
		if (settings.print_controls === 'yes') {
			const printButton = container.getElementsByClassName('dce-pdf-print')[0];
			if (printButton) {
			printButton.addEventListener('click', () => {
				window.print();
			});
			}
		}
	
		if (settings.navigation_controls === 'yes') {
			const prevButton = container.getElementsByClassName('dce-pdf-go-previous')[0];
			const nextButton = container.getElementsByClassName('dce-pdf-go-next')[0];
			const pageInput = container.getElementsByClassName('dce-pdf-current-page')[0];
	
			if (prevButton) {
				
			prevButton.addEventListener('click', () => {
				if (!pdfState.pdf || pdfState.currentPage === 1) return;
				pdfState.currentPage -= 1;
				if (pageInput) {
				pageInput.value = pdfState.currentPage;
				}
				renderPage();
			});
			}
			if (nextButton) {
			nextButton.addEventListener('click', () => {
				if (!pdfState.pdf || pdfState.currentPage === pdfState.pdf.numPages) return;
				pdfState.currentPage += 1;
				if (pageInput) {
				pageInput.value = pdfState.currentPage;
				}
				renderPage();
			});
			}
			if (pageInput) {
			pageInput.addEventListener('keypress', (e) => {
				if (!pdfState.pdf) return;
				const code = e.keyCode || e.which;
				if (code === 13) {
				const desiredPage = pageInput.valueAsNumber;
				if (desiredPage >= 1 && desiredPage <= pdfState.pdf.numPages) {
					pdfState.currentPage = desiredPage;
					pageInput.value = desiredPage;
					renderPage();
				}
				}
			});
			}
		}
	
		if (settings.zoom_controls === 'yes') {
			const zoomInButton = container.getElementsByClassName('dce-pdf-zoom-in')[0];
			const zoomOutButton = container.getElementsByClassName('dce-pdf-zoom-out')[0];
	
			if (zoomInButton) {
			zoomInButton.addEventListener('click', () => {
				if (!pdfState.pdf) return;
				pdfState.zoom += 0.5;
				renderPage();
			});
			}
			if (zoomOutButton) {
			zoomOutButton.addEventListener('click', () => {
				if (!pdfState.pdf) return;
				pdfState.zoom = Math.max(0.5, pdfState.zoom - 0.5);
				renderPage();
			});
			}
		}
	
		function renderPage() {
			pdfState.pdf.getPage(pdfState.currentPage).then((page) => {
			const canvas = container.getElementsByClassName('dce-pdf-renderer')[0];
			if (!canvas) return;
			const context = canvas.getContext('2d');
			let viewport = page.getViewport({ scale: pdfState.zoom });
	
			if (settings.size_adjustable_controls === 'yes') {
				const desiredWidth = Number(dynamicooo.getResponsiveValue(settings, 'size_adjustable_width', 800));
				const desiredHeight = Number(dynamicooo.getResponsiveValue(settings, 'size_adjustable_height', 600));
				const baseViewport = page.getViewport({ scale: 1 });
				const scaleX = desiredWidth / baseViewport.width;
				const scaleY = desiredHeight / baseViewport.height;
				const scale = Math.min(scaleX, scaleY) * pdfState.zoom;
				viewport = page.getViewport({ scale: scale });
			}
	
			canvas.width = viewport.width;
			canvas.height = viewport.height;
	
			const renderContext = {
				canvasContext: context,
				viewport: viewport,
			};
			page.render(renderContext);
			});
		}

		// Re-render on resize so per-device Width/Height/Zoom apply when crossing breakpoints.
		let resizeTimer;
		window.addEventListener('resize', () => {
			if (!pdfState.pdf) return;
			clearTimeout(resizeTimer);
			resizeTimer = setTimeout(renderPage, 200);
		});
	};
  
	$(window).on('elementor/frontend/init', function () {
	  	elementorFrontend.hooks.addAction('frontend/element_ready/dce-pdf-viewer.default', PdfViewerWidget);
	});
  })(jQuery);
  