( function( $, window ) {
	'use strict';

	var initRating = function( $scope ) {
		$scope.find( '.dce-rating' ).each( function() {
			var $wrap = $( this );
			var max = parseInt( $wrap.data( 'max' ), 10 ) || 5;
			var $input = $wrap.siblings( 'input.dce-rating-value' ).first();
			var allowClear = $input.data( 'allow-clear' ) === 'yes';
			var allowHalf = $input.data( 'half-selection' ) === 'yes';
			var current = parseFloat( $input.val() || 0 );
			var touchHandled = false;

			// Pre-create overlays for half-icon effect (performance optimization)
			$wrap.find( '.dce-rating-icon' ).each( function() {
				var $icon = $( this );
				if ( $icon.find( '.dce-rating-half-overlay' ).length === 0 ) {
					var $clonedIcon = $icon.children().first().clone();
					var $overlay = $( '<span class="dce-rating-half-overlay"></span>' );
					$overlay.append( $clonedIcon );
					$icon.append( $overlay );
				}
				// Add tabindex for keyboard accessibility
				$icon.attr( 'tabindex', '0' );
				$icon.attr( 'role', 'button' );
				$icon.attr( 'aria-label', $icon.data( 'value' ) + ' of ' + max );
			} );

			// Unbind previous events to avoid duplicates
			$wrap.off( '.dceRating' );

			/**
			 * Calculate rating value based on click/touch position
			 *
			 * @param {jQuery} $icon The icon element
			 * @param {number} pageX The X coordinate of click/touch
			 * @return {number} The calculated rating value
			 */
			function getValueFromPosition( $icon, pageX ) {
				var val = parseInt( $icon.data( 'value' ), 10 );
				if ( allowHalf ) {
					var iconWidth = $icon.outerWidth();
					var clickX = pageX - $icon.offset().left;
					var isLeftHalf = clickX < ( iconWidth / 2 );
					return isLeftHalf ? val - 0.5 : val;
				}
				return val;
			}

			/**
			 * Handle selection (click, touch, or keyboard)
			 *
			 * @param {jQuery} $icon The icon element
			 * @param {number|null} pageX The X coordinate (null for keyboard)
			 */
			function handleSelection( $icon, pageX ) {
				var val = parseInt( $icon.data( 'value' ), 10 );
				var newVal;

				if ( pageX !== null && allowHalf ) {
					newVal = getValueFromPosition( $icon, pageX );
				} else if ( allowHalf ) {
					// Keyboard: toggle between half and full
					if ( current === val ) {
						newVal = val - 0.5;
					} else if ( current === val - 0.5 ) {
						newVal = allowClear ? 0 : val;
					} else {
						newVal = val;
					}
				} else {
					newVal = val;
				}

				// Allow clear: clicking same value clears it
				if ( allowClear && current === newVal ) {
					newVal = 0;
				}

				current = newVal;
				$input.val( current === 0 ? '' : current ).trigger( 'change' );
				update();
			}

			// Click handler - skip if touch was just handled
			$wrap.on( 'click.dceRating', '.dce-rating-icon', function( e ) {
				if ( touchHandled ) {
					e.preventDefault();
					return;
				}
				e.preventDefault();
				handleSelection( $( this ), e.pageX );
			} );

			// Touch handler for mobile - use flag to prevent duplicate click event
			$wrap.on( 'touchend.dceRating', '.dce-rating-icon', function( e ) {
				e.preventDefault();
				touchHandled = true;
				var touch = e.originalEvent.changedTouches[ 0 ];
				handleSelection( $( this ), touch.pageX );
				// Reset flag after delay to allow future clicks
				setTimeout( function() {
					touchHandled = false;
				}, 300 );
			} );

			// Keyboard handler (a11y)
			$wrap.on( 'keydown.dceRating', '.dce-rating-icon', function( e ) {
				var $icon = $( this );
				var val = parseInt( $icon.data( 'value' ), 10 );

				switch ( e.key ) {
					case 'Enter':
					case ' ':
						e.preventDefault();
						handleSelection( $icon, null );
						break;
					case 'ArrowRight':
					case 'ArrowUp':
						e.preventDefault();
						if ( val < max ) {
							$icon.next( '.dce-rating-icon' ).focus();
						}
						break;
					case 'ArrowLeft':
					case 'ArrowDown':
						e.preventDefault();
						if ( val > 1 ) {
							$icon.prev( '.dce-rating-icon' ).focus();
						}
						break;
					case 'Home':
						e.preventDefault();
						$wrap.find( '.dce-rating-icon' ).first().focus();
						break;
					case 'End':
						e.preventDefault();
						$wrap.find( '.dce-rating-icon' ).last().focus();
						break;
				}
			} );

			// Hover handlers with half-selection preview
			$wrap.on( 'mouseenter.dceRating', '.dce-rating-icon', function( e ) {
				var hoverVal = getValueFromPosition( $( this ), e.pageX );
				update( hoverVal );
			} );

			$wrap.on( 'mousemove.dceRating', '.dce-rating-icon', function( e ) {
				if ( allowHalf ) {
					var hoverVal = getValueFromPosition( $( this ), e.pageX );
					update( hoverVal );
				}
			} );

			$wrap.on( 'mouseleave.dceRating', function() {
				update();
			} );

			/**
			 * Update visual state of rating icons
			 *
			 * @param {number|undefined} hoverVal Optional hover value for preview
			 */
			function update( hoverVal ) {
				var activeVal = typeof hoverVal === 'number' ? hoverVal : current;

				$wrap.find( '.dce-rating-icon' ).each( function() {
					var $icon = $( this );
					var v = parseInt( $icon.data( 'value' ), 10 );
					var isActive = v <= Math.floor( activeVal );
					var isHalfActive = allowHalf && v === Math.ceil( activeVal ) && activeVal % 1 === 0.5;

					$icon.toggleClass( 'is-active', isActive );
					$icon.toggleClass( 'is-half-active', isHalfActive );

					// Update ARIA for screen readers
					$icon.attr( 'aria-pressed', isActive || isHalfActive );
				} );
			}

			// Initial update
			update();
		} );
	};

	$( window ).on( 'elementor/frontend/init', function() {
		var handler = function( $scope ) {
			initRating( $scope );
		};
		elementorFrontend.hooks.addAction( 'frontend/element_ready/form.default', handler );
	} );
} )( jQuery, window );
