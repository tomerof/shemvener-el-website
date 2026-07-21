$ = jQuery.noConflict();

var player;
var window_width = jQuery(window).width();
var done = false;
var isOpen = false;
var bodyEl = document.body;
var content = $('#wrapper');
var openbtn = $('.mobile-btn');
var $window = $(window);
var state;

$(window).on('resize', function () {
  if ($window.width() > 768) {
    $('body').removeClass('show-menu');
    isOpen = false;
  }
});

$(document).on('click', 'li.menu-item-has-children i', function () {
  var is_open = $(this).parent().hasClass("open_menu");
  state = true;
  if (is_open) state = true;

  if (state && !is_open) {
    //$(this).parent().siblings().removeClass('open_menu');
    $(this).parent().addClass('open_menu');
    state = !state;
    is_open = true;
  } else {
    $(this).parent().removeClass('open_menu');
    state = true;
    is_open = false;
  }
});

$(window).on('scroll', function () {
  var y = jQuery(this).scrollTop();
  if (y > 100) {
    jQuery('body .header-mobile').addClass('scrolled');
  } else {
    jQuery('body .header-mobile').removeClass('scrolled');
  }

});

$( function() {
  if( $('.video-banner-slider').length) {
    jQuery('.video-banner-slider').slick({
      dots: false,
      infinite: true,
      speed: 2000,
      autoplay: true,
      autoplaySpeed: 4000,
      slidesToScroll: 1,
      slidesToShow: 1,
      arrows: false,
      rtl: !!info.rtl,
      fade: true,
      cssEase: 'ease',
      touchThreshold: 100,
      pauseOnHover: false,
      responsive: [
          {
              breakpoint: 1300,
              settings: {
                  slidesToShow: 1
              }
          },
          {
              breakpoint: 600,
              settings: {
                  slidesToShow: 1
              }
          },
          {
              breakpoint: 480,
              settings: {
                  slidesToShow: 1,
                  autoplaySpeed: 3000
              }
          }
      ]
    });
  }

  /**
   * CF7 - Avoid duplicate submissions
   */
	var disableSubmit = false;
	$('input.wpcf7-submit[type="submit"]').click(function() {
	    $(':input[type="submit"]').attr('value', 'שולח...');
	    if (disableSubmit == true) {
	        return false;
	    }
	    disableSubmit = true;
	    return true;
	})

  if( $('.wpcf7').length) {
    var wpcf7Elm = document.querySelector( '.wpcf7' );
    wpcf7Elm.addEventListener( 'wpcf7_before_send_mail', function() {
        $(':input[type="submit"]').attr('value', 'נשלח');
        disableSubmit = false;
    }, false );

    wpcf7Elm.addEventListener( 'wpcf7invalid', function() {
        $(':input[type="submit"]').attr('value', 'לביצוע ההזמנה')
        disableSubmit = false;
    }, false );
  }

  /**
   * CF7 - Autocomplete
   */
  $('input.wpcf7-form-control').attr('autocomplete', 'off');

  /**
   * Spanish modal
   */
  $('#spanish-modal').click( function(e) {
    e.preventDefault();

    $.magnificPopup.open({
      items: {
          src: '#select-deceased',
          type: 'inline'
      },
      callbacks: {
          close: function () {
          }
      }
    });

    $('#select-deceased').fadeIn('fast');
  });

  $(document).on('click', '#select-deceased #download', function (e) {
    e.preventDefault();
    var val = $('#select-deceased select').val();
    if( val && val.indexOf('sticker-') !== -1) {
      $(document).find('.mfp-close').trigger('click');
      window.location.href = '/wp-content/themes/shemvener-child/include/parts/global/splitted stickers/' + val;
    }
  });
});

/*script*/
$(document).ready(function () {
  /**
   * Smoove page templates
   */
  if( $('body').hasClass('rtl') && $('body').hasClass('page-template-page-smoove')) {
    $('select option[value=""]').each( function() {
      if( $(this).text().indexOf('Please choose an option') !== -1) {
        $(this).text('- נא לבחור אפשרות -');
      }
    });
  }

  /**
   * Smoove page totals
   */
  $('[data-name="how-many-boxes-holocaust"] input, [data-name="how-many-boxes-memorial"] input').on('change keyup', function() {
    // Default: 0
    var new_val_holocaust = parseInt( $('[data-name="how-many-boxes-holocaust"] input').val());
    if( ! new_val_holocaust || isNaN( new_val_holocaust) || new_val_holocaust == undefined) {
      new_val_holocaust = 0;
    }
    // Default: 0
    var new_val_memorial = parseInt( $('[data-name="how-many-boxes-memorial"] input').val());
    if( ! new_val_memorial || isNaN( new_val_memorial) || new_val_memorial == undefined) {
      new_val_memorial = 0;
    }

    // Page ID: 170691 - Companies (Live)
    // Page ID: 170624 - Schools (Live)

    // Page ID: 171625 - Companies (Draft for testing)
    // Page ID: 171634 - Schools (Draft for testing)

    var price = ($('body').hasClass('page-id-170691') || $('body').hasClass('page-id-171625')) ? 93.6 : 67.20;
    var total_boxes = new_val_holocaust + new_val_memorial;
    var total_price = total_boxes * price;

    var final_holocaust_price = (new_val_holocaust * price).toFixed(2);
    var final_memorial_price = (new_val_memorial * price).toFixed(2);

    if( total_boxes) {
      $('.final-qty').first().text( new_val_holocaust);
      $('.final-qty').last().text( new_val_memorial);
      $('.total-qty').text( new_val_holocaust + new_val_memorial);
      $('.total-price').text( total_price.toFixed(2));
      $('#total-price').val( total_price.toFixed(2));
      $('#total-price-holocaust').val( final_holocaust_price);
      $('#total-price-memorial').val( final_memorial_price);
      $('#total-boxes-holocaust').val( new_val_holocaust);
      $('#total-boxes-memorial').val( new_val_memorial);
    }
  });

  touchStart();
  sectionPreCreateLabel();
  sectionCreateLabelForm();
  searchAndFilters();
  searchAndFiltersNew();
  sectionMovies();
  sectionFaq();
  sectionGallery();
  sectionGoldPartners();
  offCanvas();
  deceasedDetails();
  langValidation();
  generalPopup();
  donationPopup();
  initSelect2();
  magnificExtension();
  handleFixedHeader();
  handleUpload();
  addAnotherRecordButton();

  handleBannerVideo();
  initStipSlider();
  initSectionContentSlider();
  handleFilter();
  handleWishlist();
  appendPropToInput();
  selectPlaceholders();
  initLogoSlider();
  init_home_about_sec_slider();

  if (is_mobile()) {
    blocks_wrapper_slider_mob();
  }

  enable_action_buttons();

  $('#access-plug').accessPlug({
    contrastLight: false,
    lang: info.lang // title : "סרגל נגישות",
  });
  $('.readfulldesc').click(function(e){
      $(this).closest('.entry-content').find('.memberexcerpt').slideToggle();
      $(this).closest('.entry-content').find('.memberfulldesc').slideToggle();
      e.preventDefault();
  });
  $('.closefulldesc').click(function(e){
      $(this).closest('.entry-content').find('.memberfulldesc').slideToggle();
      $(this).closest('.entry-content').find('.memberexcerpt').slideToggle();			
      e.preventDefault();
  });
});
