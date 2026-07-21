/* SPDX-FileCopyrightText: 2014-2025 Amber Creative
   SPDX-FileCopyrightText: 2025 Ovation S.r.l. <help@dynamic.ooo>
   SPDX-License-Identifier: MIT
   Forked-From: codyhouse/vertical-timeline@69f178d5f982330bc2bfd89d9ec1b623ef7dbd42
   Forked-Date: 2025-08-27
   Upstream-URL: https://github.com/codyhouse/vertical-timeline
   License-URL: https://codyhouse.co/mit */

var createVerticalTimelines = (function() {
	function VerticalTimeline( element, config ) {
		this.element = element;
		this.config = config || {};
		var blockClass = this.config.blockClassName || "cd-timeline__block";
		var imgClass = this.config.imgClassName || "cd-timeline__img";
		var contentClass = this.config.contentClassName || "cd-timeline__content";
		this.blocks = this.element.getElementsByClassName(blockClass);
		this.images = this.element.getElementsByClassName(imgClass);
		this.contents = this.element.getElementsByClassName(contentClass);
		this.offset = typeof this.config.offset === 'number' ? this.config.offset : 0.8;
		this.classHiddenImg = this.config.hiddenImgClassName || "cd-timeline__img--hidden";
		this.classHiddenContent = this.config.hiddenContentClassName || "cd-timeline__content--hidden";
		this.classBounceImg = this.config.bounceImgClassName || "cd-timeline__img--bounce-in";
		this.classBounceContent = this.config.bounceContentClassName || "cd-timeline__content--bounce-in";
		this.focusClassName = this.config.focusClassName || null;
		
		// Progress line support
		this.progressLine = this.config.progressLine || false;
		this.progressLineConfig = this.config.progressLineConfig || {};
		
		// Store references to first and last blocks
		if (this.blocks.length > 0) {
			this.firstBlock = this.blocks[0].querySelector('.' + imgClass);
			this.lastBlock = this.blocks[this.blocks.length - 1].querySelector('.' + imgClass);
		}
		
		// Initialize
		this.hideBlocks();
		
		// Setup progress line if enabled
		if (this.progressLine && this.firstBlock && this.lastBlock) {
			this.setupProgressLine();
		}
	};

	VerticalTimeline.prototype.hideBlocks = function() {
		if ( !"classList" in document.documentElement ) {
			return; // no animation on older browsers
		}
		//hide timeline blocks which are outside the viewport
		var self = this;
		for( var i = 0; i < this.blocks.length; i++) {
			(function(i){
				if( self.blocks[i].getBoundingClientRect().top > window.innerHeight*self.offset ) {
					self.images[i].classList.add(self.classHiddenImg);
					self.contents[i].classList.add(self.classHiddenContent);
				}
			})(i);
		}
	};

	VerticalTimeline.prototype.showBlocks = function() {
		if ( ! "classList" in document.documentElement ) {
			return;
		}
		var self = this;
		for( var i = 0; i < this.blocks.length; i++) {
			(function(i){
				var isVisible = self.blocks[i].getBoundingClientRect().top <= window.innerHeight*self.offset;
				
				if( self.contents[i].classList.contains(self.classHiddenContent) && isVisible ) {
					// add bounce-in animation
					self.images[i].classList.add(self.classBounceImg);
					self.contents[i].classList.add(self.classBounceContent);
					self.images[i].classList.remove(self.classHiddenImg);
					self.contents[i].classList.remove(self.classHiddenContent);
				}
				
				// Handle focus class
				if (self.focusClassName) {
					if (isVisible) {
						self.blocks[i].classList.add(self.focusClassName);
					} else {
						self.blocks[i].classList.remove(self.focusClassName);
					}
				}
			})(i);
		}
		
		// Update progress line if enabled
		if (self.progressLine) {
			self.updateProgressLine();
		}
	};
	
	VerticalTimeline.prototype.setupProgressLine = function() {
		var self = this;
		var wrapper = this.progressLineConfig.wrapperSelector ? 
			document.querySelector(this.progressLineConfig.wrapperSelector) : 
			this.element.parentElement;
		
		if (!wrapper) return;
		
		this.wrapper = wrapper;
		
		// Call initial update
		this.updateProgressLine();
	};
	
	VerticalTimeline.prototype.updateProgressLine = function() {
		if (!this.wrapper || !this.firstBlock || !this.lastBlock) return;
		
		var self = this;
		var containerOffset = this.element.getBoundingClientRect().top;
		var wrapperElement = this.wrapper;
		
		// Calculate first block position
		var firstBlockPos = this.firstBlock.getBoundingClientRect().top - containerOffset;
		if (firstBlockPos <= 0) firstBlockPos = 0;
		
		// Calculate last block position
		var rowspace = this.progressLineConfig.rowspace || 0;
		var lastBlockPos = this.lastBlock.getBoundingClientRect().top - containerOffset + rowspace;
		
		// Calculate scroll progress
		var windowHeight = window.innerHeight;
		var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
		var elementOffsetTop = this.element.offsetTop;
		var scrollProgress = scrollTop - elementOffsetTop + (windowHeight * this.offset);
		
		if (scrollProgress >= lastBlockPos) {
			scrollProgress = lastBlockPos;
		}
		
		// Apply CSS custom properties
		if (wrapperElement && wrapperElement.style) {
			wrapperElement.style.setProperty('--lineTop', (firstBlockPos + 10) + 'px');
			wrapperElement.style.setProperty('--lineFixed', lastBlockPos + 'px');
			wrapperElement.style.setProperty('--lineProgress', scrollProgress + 'px');
		}
		
		// Call custom update callback if provided
		if (this.progressLineConfig.onUpdate) {
			this.progressLineConfig.onUpdate({
				firstBlockPos: firstBlockPos,
				lastBlockPos: lastBlockPos,
				scrollProgress: scrollProgress
			});
		}
	};

	function bootstrap(config) {
		var root = (config && config.rootEl) || document;
		var containers = [];
		if (config && config.containers && config.containers.length) {
			containers = Array.prototype.slice.call(config.containers);
		} else if (config && config.containerSelector) {
			containers = Array.prototype.slice.call(root.querySelectorAll(config.containerSelector));
		} else {
			containers = Array.prototype.slice.call(document.getElementsByClassName("js-cd-timeline"));
		}

		var verticalTimelinesArray = [];
		var scrolling = false;
		if (containers.length > 0) {
			for (var i = 0; i < containers.length; i++) {
				(function(i){
					verticalTimelinesArray.push(new VerticalTimeline(containers[i], config || {}));
				})(i);
			}

			function checkTimelineScroll() {
				verticalTimelinesArray.forEach(function(timeline){
					timeline.showBlocks();
				});
				scrolling = false;
			};

			// initial check
			checkTimelineScroll();

			//show timeline blocks on scrolling
			window.addEventListener("scroll", function() {
				if (!scrolling) {
					scrolling = true;
					(!window.requestAnimationFrame) ? setTimeout(checkTimelineScroll, 250) : window.requestAnimationFrame(checkTimelineScroll);
				}
			});
			
			// Handle resize events if configured
			if (config && (config.onResize || config.progressLine)) {
				window.addEventListener("resize", function() {
					checkTimelineScroll();
					if (config.onResize) {
						config.onResize(verticalTimelinesArray);
					}
				});
			}
		}
		
		// Return the array of timeline instances for external manipulation
		return verticalTimelinesArray;
	}

	// Return the main function
	return function(config) {
		return bootstrap(config || {});
	};
})();
