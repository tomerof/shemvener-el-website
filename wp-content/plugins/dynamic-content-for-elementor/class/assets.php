<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor;

use DynamicContentForElementor\JsLocalization\Manager as JsLocalizationManager;
if (!\defined('ABSPATH')) {
    exit;
}
class Assets
{
    /**
     * @var array<string,mixed>
     */
    public static $styles = array(
        'dce-icon' => '/assets/css/dce-icon.css',
        'dce-lazy-iframe' => '/assets/css/lazy-iframe.css',
        'dce-crypto-badge' => '/assets/css/crypto-badge.css',
        'dce-icons-form-style' => '/assets/css/icons-form.css',
        'dce-hidden-label' => 'assets/css/hidden-label.css',
        'dce-rating' => '/assets/css/rating.css',
        'dce-advanced-video' => ['path' => '/assets/css/advanced-video.css', 'deps' => ['dce-plyr']],
        'dce-google-address-autocomplete-field' => '/assets/css/google-address-autocomplete-field.css',
        'dce-globalsettings' => 'assets/css/global-settings.css',
        'dce-style' => '/assets/css/style.css',
        'dce-animations' => '/assets/css/animations.css',
        'dce-preview' => '/assets/css/preview.css',
        'dce-acf' => '/assets/css/acf-fields.css',
        'dce-acf-relationship-old-version' => '/assets/css/acf-relationship-old-version.css',
        'dce-acfslider' => '/assets/css/acf-slider.css',
        'dce-acfGallery' => '/assets/css/acf-gallery.css',
        'dce-acf-repeater-old' => '/assets/css/acf-repeater-old.css',
        'dce-acf-repeater' => '/assets/css/acf-repeater.css',
        'dce-add-to-calendar' => '/assets/css/add-to-calendar.css',
        'dce-dynamic-visibility' => '/assets/css/dynamic-visibility.css',
        'dce-copy-to-clipboard' => '/assets/css/copy-to-clipboard.css',
        'dce-google-maps' => '/assets/css/dynamic-google-maps.css',
        'dce-dynamic-google-maps-directions' => '/assets/css/dynamic-google-maps-directions.css',
        'dce-pods' => '/assets/css/pods-fields.css',
        'dce-pods-gallery' => '/assets/css/pods-gallery.css',
        'dce-toolset' => '/assets/css/toolset-fields.css',
        'dce-tooltip' => '/assets/css/tooltip.css',
        'dce-pdf-viewer' => '/assets/css/pdf-viewer.css',
        'dce-international-phone' => '/assets/css/international-phone.css',
        // Dynamic Posts - old version
        'dce-dynamic-posts-old-version' => '/assets/css/dynamic-posts-old-version.css',
        'dce-dynamicPosts_slick' => '/assets/css/dynamic-posts-slick.css',
        'dce-dynamicPosts_swiper' => '/assets/css/dynamic-posts-swiper.css',
        'dce-dynamicPosts_timeline' => '/assets/css/dynamic-posts-timeline.css',
        // Dynamic Posts
        'dce-dynamic-posts' => '/assets/css/dynamic-posts.css',
        'dce-dynamicPosts-grid' => '/assets/css/dynamic-posts-skin-grid.css',
        'dce-dynamicPosts-carousel' => '/assets/css/dynamic-posts-skin-carousel.css',
        'dce-dynamicPosts-dualcarousel' => '/assets/css/dynamic-posts-skin-dual-carousel.css',
        'dce-dynamicPosts-accordion' => ['path' => '/assets/css/dynamic-posts-skin-accordion.css', 'deps' => ['dce-accordionjs']],
        'dce-dynamicPosts-timeline' => '/assets/css/dynamic-posts-skin-timeline.css',
        'dce-dynamicPosts-gridtofullscreen3d' => '/assets/css/dynamic-posts-skin-grid-to-fullscreen-3d.css',
        'dce-dynamicPosts-crossroadsslideshow' => '/assets/css/dynamic-posts-skin-crossroads-slideshow.css',
        'dce-dynamicPosts-3d' => '/assets/css/dynamic-posts-skin-3d.css',
        'dce-dynamicUsers' => '/assets/css/dynamic-users.css',
        'dce-iconFormat' => '/assets/css/icon-format.css',
        'dce-nextPrev' => '/assets/css/prev-next.css',
        'dce-list' => '/assets/css/taxonomy-terms-list.css',
        'dce-featuredImage' => '/assets/css/featured-image.css',
        'dce-modalWindow' => '/assets/css/fire-modal-window.css',
        'dce-modal' => '/assets/css/modals.css',
        'dce-pageScroll' => '/assets/css/page-scroll.css',
        'dce-reveal' => '/assets/css/reveal.css',
        'dce-threesixtySlider' => '/assets/css/360-slider.css',
        'dce-before-after' => '/assets/css/before-after.css',
        'dce-parallax' => '/assets/css/parallax.css',
        'dce-filebrowser' => '/assets/css/file-browser.css',
        'dce-animated-text' => '/assets/css/animated-text.css',
        'dce-imagesDistortion' => '/assets/css/distortion-image.css',
        'dce-animatedOffcanvasMenu' => '/assets/css/animated-off-canvas-menu.css',
        'dce-cursorTracker' => '/assets/css/cursor-tracker.css',
        'dce-dynamic-title' => '/assets/css/dynamic-title.css',
        'dce-breadcrumbs' => '/assets/css/breadcrumbs.css',
        'dce-date' => '/assets/css/date.css',
        'dce-add-to-favorites' => '/assets/css/add-to-favorites.css',
        'dce-terms' => '/assets/css/terms-and-taxonomy.css',
        'dce-content' => '/assets/css/content.css',
        'dce-excerpt' => '/assets/css/excerpt.css',
        'dce-readmore' => '/assets/css/read-more.css',
        'dce-bgCanvas' => '/assets/css/bg-canvas.css',
        'dce-svg' => '/assets/css/svg.css',
        'dce-views' => '/assets/css/views.css',
    );
    /**
     * @var array<string,mixed>
     */
    public static $vendor_css = array('dce-prism-css' => '/assets/node/prismjs/prism.min.css', 'dce-prism-line-numbers-css' => '/assets/node/prismjs/prism-line-numbers.min.css', 'dce-jquery-confirm' => '/assets/node/jquery-confirm/jquery-confirm.min.css', 'dce-osm-map' => '/assets/node/leaflet/leaflet.css', 'dce-osm-map-marker-cluster' => '/assets/node/leaflet.markercluster/MarkerCluster.Default.css', 'dce-photoswipe-skin' => '/assets/node/photoswipe/photoswipe.css', 'dce-justifiedGallery' => '/assets/node/justifiedGallery/justifiedGallery.min.css', 'dce-file-icon' => '/assets/node/file-icon-vectors/file-icon-vivid.min.css', 'animatecss' => '/assets/node/animate.css/animate.min.css', 'dce-datatables' => '/assets/node/datatables/datatables.min.css', 'dce-plyr' => '/assets/node/plyr/plyr.css', 'dce-swiper' => '/assets/node/swiper/css/swiper.min.css', 'dce-accordionjs' => '/assets/node/accordionjs/accordion.css', 'dce-diamonds-css' => '/assets/node/jquery.diamonds.js/diamonds.css', 'dce-select2' => '/assets/node/select2/select2.min.css');
    public static $vendor_js;
    /**
     * @return void
     */
    private static function init_vendor_js()
    {
        $google_maps_api = get_option('dce_google_maps_api');
        $locale2 = \substr(get_locale(), 0, 2);
        if (get_option('dce_paypal_api_mode', 'sandbox') === 'sandbox') {
            $paypal_option = get_option('dce_paypal_api_client_id_sandbox');
        } else {
            $paypal_option = get_option('dce_paypal_api_client_id_live');
        }
        // Many user have incorrectly set their email as client id, make sure
        // this is not the case:
        if (!\strpos($paypal_option, '@')) {
            $paypal_client_id = $paypal_option;
        } else {
            $paypal_client_id = \false;
        }
        $paypal_currency = get_option('dce_paypal_api_currency', 'USD');
        // Get Stripe publishable key based on mode
        if (get_option('dce_stripe_api_mode') === 'live') {
            $stripe_publishable_key = get_option('dce_stripe_api_publishable_key_live');
        } else {
            $stripe_publishable_key = get_option('dce_stripe_api_publishable_key_test');
        }
        self::$vendor_js = [
            'dce-prism-js' => '/assets/node/prismjs/prism.js',
            'dce-prism-markup-js' => '/assets/node/prismjs/prism-markup.min.js',
            'dce-prism-markup-templating-js' => '/assets/node/prismjs/prism-markup-templating.min.js',
            'dce-prism-php-js' => '/assets/node/prismjs/prism-php.min.js',
            'dce-prism-line-numbers-js' => '/assets/node/prismjs/prism-line-numbers.min.js',
            'dce-chart-js' => '/assets/node/chart.js/chart.umd.min.js',
            'dce-imagesloaded' => '/assets/node/imagesloaded/imagesloaded.pkgd.min.js',
            'dce-jquery-confirm' => '/assets/node/jquery-confirm/jquery-confirm.min.js',
            'dce-leaflet' => '/assets/node/leaflet/leaflet.js',
            'dce-leaflet-markercluster' => '/assets/node/leaflet.markercluster/leaflet.markercluster.js',
            'dce-expressionlanguage' => '/assets/node/expression-language/expressionlanguage.min.js',
            'dce-html2canvas' => '/assets/node/html2canvas/html2canvas.min.js',
            'dce-jspdf' => '/assets/node/jspdf/jspdf.umd.min.js',
            'dce-aframe' => ['path' => '/assets/node/aframe/aframe.min.js', 'deps' => [], 'in_footer' => \false],
            'dce-datatables' => ['path' => '/assets/node/datatables/datatables.min.js', 'deps' => ['jquery']],
            'dce-plyr-js' => '/assets/node/plyr/plyr.polyfilled.min.js',
            'dce-dayjs' => '/assets/node/dayjs/dayjs.min.js',
            'dce-wow' => '/assets/node/wowjs/wow.min.js',
            'dce-jquery-match-height' => 'assets/node/jquery-match-height/jquery.matchHeight-min.js',
            'dce-isotope' => '/assets/node/isotope-layout/isotope.pkgd.min.js',
            'dce-infinitescroll' => '/assets/node/infinite-scroll/infinite-scroll.pkgd.min.js',
            'dce-jquery-slick' => '/assets/node/slick-carousel/slick.min.js',
            'dce-velocity' => '/assets/node/velocity-animate/velocity.min.js',
            'dce-diamonds-js' => '/assets/node/jquery.diamonds.js/jquery.diamonds.js',
            'dce-homeycombs' => '/assets/node/honeycombs/jquery.homeycombs.js',
            'photoswipe' => '/assets/node/photoswipe/photoswipe.umd.min.js',
            'photoswipe-lightbox' => '/assets/node/photoswipe/photoswipe-lightbox.umd.min.js',
            'tilt-lib' => '/assets/node/tilt.js/tilt.jquery.js',
            'dce-jquery-visible' => '/assets/node/jquery-visible/jquery.visible.min.js',
            'jquery-easing' => '/assets/node/jquery.easing/jquery-easing.min.js',
            'justifiedGallery-lib' => '/assets/node/justifiedGallery/jquery.justifiedGallery.min.js',
            'dce-parallaxjs-lib' => '/assets/node/parallax-js/parallax.min.js',
            'dce-threesixtyslider-lib' => '/assets/node/threesixty-slider/threesixty.min.js',
            'dce-jqueryeventmove-lib' => ['path' => '/assets/node/zurb-twentytwenty/jquery.event.move.js', 'deps' => ['jquery']],
            'dce-twentytwenty-lib' => '/assets/node/zurb-twentytwenty/jquery.twentytwenty.js',
            'dce-anime-lib' => '/assets/node/animejs/anime.min.js',
            'dce-flubber-lib' => '/assets/node/flubber/flubber.min.js',
            'dce-signature-lib' => '/assets/node/signature_pad/signature_pad.umd.min.js',
            'dce-threejs-lib' => '/assets/node/three/three.min.js',
            'dce-libphonenumber-js' => '/assets/node/libphonenumber-js/libphonenumber.min.js',
            'dce-threejs-EffectComposer' => '/assets/lib/threejs/postprocessing/EffectComposer.js',
            'dce-threejs-RenderPass' => '/assets/lib/threejs/postprocessing/RenderPass.js',
            'dce-threejs-ShaderPass' => '/assets/lib/threejs/postprocessing/ShaderPass.js',
            'dce-threejs-FilmPass' => '/assets/lib/threejs/postprocessing/FilmPass.js',
            'dce-threejs-HalftonePass' => '/assets/lib/threejs/postprocessing/HalftonePass.js',
            'dce-threejs-DotScreenPass' => '/assets/lib/threejs/postprocessing/DotScreenPass.js',
            'dce-threejs-GlitchPass' => '/assets/lib/threejs/postprocessing/GlitchPass.js',
            'dce-threejs-CopyShader' => '/assets/lib/threejs/shaders/CopyShader.js',
            'dce-threejs-HalftoneShader' => '/assets/lib/threejs/shaders/HalftoneShader.js',
            'dce-threejs-RGBShiftShader' => '/assets/lib/threejs/shaders/RGBShiftShader.js',
            'dce-threejs-DotScreenShader' => '/assets/lib/threejs/shaders/DotScreenShader.js',
            'dce-threejs-ConvolutionShader' => '/assets/lib/threejs/shaders/ConvolutionShader.js',
            'dce-threejs-FilmShader' => '/assets/lib/threejs/shaders/FilmShader.js',
            'dce-threejs-DigitalGlitch' => '/assets/lib/threejs/shaders/DigitalGlitch.js',
            'dce-threejs-PixelShader' => '/assets/lib/threejs/shaders/PixelShader.js',
            // WebGL Distortion
            'dce-dat-gui' => '/assets/lib/threejs/libs/dat.gui.min.js',
            'dce-displacement-sketch' => '/assets/lib/threejs/sketch.js',
            // Dynamic Posts
            'dce-threejs-OrbitControls' => '/assets/lib/threejs/controls/OrbitControls.js',
            'dce-threejs-CSS3DRenderer' => '/assets/lib/threejs/renderers/CSS3DRenderer.js',
            'dce-threejs-gridtofullscreeneffect' => '/assets/lib/threejs/GridToFullscreenEffect.js',
            // CANVAS
            'dce-rellaxjs-lib' => '/assets/node/rellax/rellax.min.js',
            'dce-clipboard-js' => '/assets/node/clipboard/clipboard.min.js',
            'dce-revealFx' => '/assets/node/revealfx/revealFx.js',
            'dce-scrollify' => '/assets/node/jquery-scrollify/jquery.scrollify.js',
            'dce-inertia-scroll' => '/assets/node/jquery-inertia-scroll/jquery-inertiaScroll.js',
            'dce-lax-lib' => '/assets/node/lax.js/lax.min.js',
            'dce-google-maps-markerclusterer' => '/assets/node/markerclustererplus/index.min.js',
            // MODULES
            'dce-google-modules_helpers' => '/assets/js/modules/google-api-module-helpers.js',
            'dce-google-maps' => ['path' => '/assets/js/dynamic-google-maps.js', 'deps' => ['wp-util', 'dce-google-maps-markerclusterer', 'dce-google-maps-api']],
            'dce-dynamic-google-maps-directions' => ['path' => '/assets/js/dynamic-google-maps-directions.js', 'deps' => ['dce-google-maps-api']],
            'dce-popper' => '/assets/node/popperjs/popper.min.js',
            'dce-tippy' => ['path' => '/assets/node/tippy.js/tippy-bundle.umd.min.js', 'deps' => ['dce-popper']],
            'dce-jquery-color' => ['path' => '/assets/node/jquery-color/jquery.color.min.js', 'deps' => ['jquery']],
            'dce-tinymce-js' => includes_url('js/tinymce/') . 'wp-tinymce.php',
            'dce-accordionjs' => '/assets/node/accordionjs/accordion.min.js',
            'dce-mustache-js' => '/assets/node/mustache/mustache.min.js',
            'dce-vertical-timeline' => '/assets/node/vertical-timeline-ooo/main.js',
            'dce-select2-lib' => '/assets/node/select2/select2.full.min.js',
        ];
        if (!empty($google_maps_api)) {
            self::$vendor_js['dce-google-maps-api'] = ['deps' => ['dce-settings'], 'path' => "https://maps.googleapis.com/maps/api/js?key={$google_maps_api}&language={$locale2}&loading=async&callback=initMap"];
        }
        if (!empty($paypal_client_id)) {
            self::$vendor_js['dce-paypal-sdk'] = "https://www.paypal.com/sdk/js?client-id={$paypal_client_id}&currency={$paypal_currency}";
        }
        if (!empty($stripe_publishable_key)) {
            self::$vendor_js['dce-stripe-js'] = 'https://js.stripe.com/v3';
        }
    }
    /**
     * @var array<string,mixed>
     */
    public static $scripts = array(
        'dce-add-to-favorites' => ['path' => '/assets/js/add-to-favorites.js', 'deps' => ['wp-util']],
        'dce-clear-favorites' => ['path' => '/assets/js/clear-favorites.js', 'deps' => ['wp-util']],
        'dce-file-browser' => ['path' => '/assets/js/file-browser.js', 'deps' => ['jquery']],
        'dce-copy-to-clipboard' => ['path' => '/assets/js/copy-to-clipboard.js', 'deps' => ['dce-clipboard-js']],
        'dce-lazy-iframe' => '/assets/js/lazy-iframe.js',
        'dce-mirror-field' => ['path' => '/assets/js/mirror-field.js', 'deps' => ['jquery']],
        'dce-pdf-button' => '/assets/js/pdf-button.js',
        'dce-visibility' => '/assets/js/visibility.js',
        'dce-form-address-autocomplete' => ['path' => '/assets/js/form-address-autocomplete.js', 'deps' => ['dce-google-maps-api']],
        'dce-dynamic-cookie' => '/assets/js/dynamic-cookie.js',
        'dce-osm-map' => ['path' => '/assets/js/osm-map.js', 'deps' => ['dce-leaflet']],
        'dce-dynamic-osm-map' => ['path' => '/assets/js/dynamic-osm-map.js', 'deps' => ['dce-leaflet', 'dce-leaflet-markercluster']],
        'dce-conditional-fields' => ['path' => '/assets/js/conditional-fields.js', 'deps' => ['dce-expressionlanguage']],
        'dce-pdf-viewer' => ['path' => '/assets/js/pdf-viewer.js'],
        'dce-dynamic-countdown' => '/assets/js/dynamic-countdown.js',
        'dce-formatted-number' => ['path' => '/assets/js/formatted-number.js', 'deps' => ['jquery']],
        'dce-js-field' => ['path' => '/assets/js/js-field.js', 'deps' => ['jquery']],
        'dce-amount-field' => '/assets/js/amount-field.js',
        'dce-international-phone-field' => ['path' => '/assets/js/international-phone.js', 'deps' => ['jquery', 'dce-libphonenumber-js']],
        'dce-range' => '/assets/js/range.js',
        'dce-rating' => '/assets/js/rating.js',
        'dce-live-html' => ['path' => '/assets/js/live-html.js', 'deps' => ['dce-mustache-js']],
        'dce-google-address-autocomplete-field' => ['path' => '/assets/js/google-address-autocomplete-field.js', 'deps' => ['dce-google-maps-api']],
        'dce-confirm-dialog' => ['path' => '/assets/js/confirm-dialog.js', 'deps' => ['dce-jquery-confirm', 'dce-live-html']],
        'dce-stripe' => ['path' => '/assets/js/stripe.js', 'deps' => ['dce-stripe-js']],
        'dce-paypal' => ['path' => '/assets/js/paypal.js', 'deps' => ['dce-paypal-sdk']],
        'dce-pdf-jsconv' => ['path' => '/assets/js/pdf-button-js-converter.js', 'deps' => ['dce-jspdf', 'dce-html2canvas']],
        'dce-discover-tokens' => ['path' => '/assets/js/discover-tokens.js', 'deps' => ['dce-clipboard-js', 'dce-tippy', 'dce-popper']],
        'dce-dynamic-select' => '/assets/js/dynamic-select.js',
        'dce-dynamic-charts' => '/assets/js/dynamic-charts.js',
        'dce-hidden-label' => '/assets/js/hidden-label.js',
        'dce-admin-js' => 'assets/js/admin.js',
        'dce-globalsettings-js' => ['path' => 'assets/js/global-settings.js', 'deps' => ['jquery']],
        'dce-acf' => '/assets/js/acf-fields.js',
        'dce-cookie' => '/assets/js/cookie.js',
        'dce-settings' => ['path' => '/assets/js/settings.js', 'deps' => ['jquery']],
        'dce-fix-background-loop' => '/assets/js/fix-background-loop.js',
        'dce-animated-text' => '/assets/js/animated-text.js',
        'dce-modals' => '/assets/js/modals.js',
        'dce-acfgallery' => ['path' => '/assets/js/acf-gallery.js', 'deps' => ['dce-diamonds-js', 'imagesloaded', 'jquery-masonry', 'dce-wow', 'photoswipe', 'photoswipe-lightbox', 'dce-homeycombs', 'justifiedGallery-lib']],
        'dce-podsgallery' => ['path' => '/assets/js/pods-gallery.js', 'deps' => ['dce-wow', 'photoswipe', 'photoswipe-lightbox']],
        'dce-acfslider-js' => '/assets/js/acf-slider.js',
        'dce-parallax-js' => '/assets/js/parallax.js',
        'dce-360-slider' => '/assets/js/360-slider.js',
        'dce-views' => ['path' => '/assets/js/views.js', 'deps' => ['dce-datatables']],
        'dce-bgcanvas-js' => '/assets/js/bg-canvas.js',
        'dce-before-after' => ['path' => '/assets/js/before-after.js', 'deps' => ['dce-imagesloaded']],
        'dce-tilt' => ['path' => '/assets/js/tilt.js', 'deps' => ['tilt-lib']],
        'dce-dynamic-posts-old-version' => '/assets/js/dynamic-posts-old-version.js',
        'dce-icons-form' => '/assets/js/icons-form.js',
        // Dynamic Posts
        'dce-dynamicPosts-base' => '/assets/js/dynamic-posts-base.js',
        'dce-dynamicPosts-grid' => '/assets/js/dynamic-posts-skin-grid.js',
        'dce-dynamicPosts-accordion' => ['path' => '/assets/js/dynamic-posts-skin-accordion.js', 'deps' => ['dce-accordionjs']],
        'dce-dynamicPosts-grid-filters' => ['path' => '/assets/js/dynamic-posts-skin-grid-filters.js', 'deps' => ['dce-imagesloaded']],
        'dce-dynamicPosts-carousel' => '/assets/js/dynamic-posts-skin-carousel.js',
        'dce-dynamicPosts-timeline' => ['path' => '/assets/js/dynamic-posts-skin-timeline.js', 'deps' => ['dce-vertical-timeline']],
        'dce-dynamicPosts-gridtofullscreen3d' => '/assets/js/dynamic-posts-skin-grid-to-fullscreen-3d.js',
        'dce-dynamicPosts-crossroadsslideshow' => '/assets/js/dynamic-posts-skin-crossroads-slideshow.js',
        'dce-dynamicPosts-3d' => '/assets/js/dynamic-posts-skin-3d.js',
        'dce-acf-repeater-old' => '/assets/js/acf-repeater-old.js',
        'dce-acf-repeater' => ['path' => '/assets/js/acf-repeater.js', 'deps' => ['dce-accordionjs', 'imagesloaded', 'swiper', 'jquery-masonry', 'dce-wow', 'dce-datatables']],
        'dce-content-js' => '/assets/js/content.js',
        'dce-dynamic_users' => '/assets/js/dynamic-users.js',
        'dce-acf_fields' => '/assets/js/acf-fields.js',
        'dce-modalwindow' => '/assets/js/fire-modal-window.js',
        'dce-nextPrev' => '/assets/js/next-prev.js',
        'dce-rellax' => '/assets/js/rellax.js',
        'dce-reveal' => '/assets/js/reveal.js',
        'dce-svgmorph' => '/assets/js/svg-morphing.js',
        'dce-svgdistortion' => '/assets/js/svg-distortion.js',
        'dce-svgfe' => '/assets/js/svg-filter-effects.js',
        'dce-svgblob' => '/assets/js/svg-blob.js',
        'dce-imagesdistortion-js' => ['path' => '/assets/js/distortion-image.js', 'deps' => ['dce-threejs-lib', 'dce-anime-lib', 'dce-dat-gui', 'dce-displacement-sketch']],
        'dce-scrolling' => '/assets/js/scrolling.js',
        'dce-animatedoffcanvasmenu-js' => '/assets/js/animated-off-canvas-menu.js',
        'dce-cursorTracker-js' => '/assets/js/cursor-tracker.js',
        'dce-advanced-video' => ['path' => '/assets/js/advanced-video.js', 'deps' => ['dce-plyr-js']],
        'dce-signature' => '/assets/js/signature.js',
        'dce-tooltip' => '/assets/js/tooltip.js',
        'dce-select2' => ['path' => '/assets/js/select2-form.js', 'deps' => ['dce-select2-lib']],
        'dce-country-field' => ['path' => '/assets/js/country-field.js', 'deps' => ['dce-select2-lib']],
        'dce-password-visibility' => '/assets/js/password-visibility-form.js',
        'dce-submit-on-change' => '/assets/js/submit-on-change-form.js',
        'dce-inline-align' => '/assets/js/inline-align-form.js',
        'dce-method-form' => '/assets/js/method-form.js',
        'dce-field-description' => '/assets/js/field-description-form.js',
        'dce-step-auto-scroll' => '/assets/js/step-auto-scroll.js',
    );
    public function __construct()
    {
        self::init_vendor_js();
        $this->init();
    }
    /**
     * This filter is needed if we want to change the attr of a registerd
     * script, for example by adding the module type.
     *
     * @param string $tag
     * @param string $handle
     * @param string $src
     * @return string
     */
    public function loader_tag_filter($tag, $handle, $src)
    {
        $modules_script = ['dce-dynamic-google-maps-directions', 'dce-pdf-viewer'];
        if (!\in_array($handle, $modules_script, \true)) {
            return $tag;
        }
        // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript
        $tag = '<script type="module" src="' . esc_url($src) . '"></script>';
        return $tag;
    }
    /**
     * @return void
     */
    public function init()
    {
        add_filter('script_loader_tag', [$this, 'loader_tag_filter'], 10, 3);
        // Admin Scripts
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
        // Dashboard
        add_action('admin_head', [$this, 'register_and_enqueue_dce_icons']);
        // Scripts
        add_action('wp_enqueue_scripts', [$this, 'register_scripts']);
        // Styles
        add_action('wp_enqueue_scripts', [$this, 'register_styles']);
        add_action('wp_enqueue_scripts', [$this, 'register_vendor_styles']);
        // Editor
        add_action('elementor/editor/after_enqueue_scripts', [$this, 'editor_enqueue']);
        add_action('elementor/preview/enqueue_styles', [$this, 'preview_enqueue']);
        // Frontend
        add_action('elementor/frontend/after_enqueue_scripts', [$this, 'frontend_enqueue']);
        // Global enqueue Script and Style
        add_action('wp_enqueue_scripts', [$this, 'dce_globals_stylescript']);
        // Custom Editor App
        add_action('elementor/editor/before_enqueue_scripts', [$this, 'enqueue_custom_editor_app']);
        // Localize strings for all JS components (after scripts are enqueued)
        add_action('elementor/editor/after_enqueue_scripts', [JsLocalizationManager::class, 'localize_scripts']);
    }
    /**
     * @return void
     */
    public static function dce_globals_stylescript()
    {
        // Fix for rare error: calling is_edit_mode on null
        try {
            $is_in_editor = \Elementor\Plugin::$instance->editor->is_edit_mode();
        } catch (\Throwable $e) {
            $is_in_editor = \false;
        }
        $features = \DynamicContentForElementor\Plugin::instance()->features;
        // Global
        $smooth_enabled = $features->get_feature_info('gst_smooth_transition', 'status') === 'active';
        $theader_enabled = $features->get_feature_info('gst_tracker_header', 'status') === 'active';
        if ($smooth_enabled && (get_option('enable_smoothtransition') || $is_in_editor)) {
            // Global Settings CSS LIB
            wp_enqueue_style('animsition-base', DCE_URL . 'assets/node/animsition/animsition.min.css', array(), DCE_VERSION);
            wp_enqueue_style('dce-animations');
            // Global Settings JS LIB
            wp_enqueue_script('dce-animsition-lib', DCE_URL . 'assets/node/animsition/animsition.min.js', array('jquery'), DCE_VERSION);
        }
        if ($theader_enabled && (get_option('enable_trackerheader') || $is_in_editor)) {
            // Global Settings JS LIB
            wp_enqueue_script('dce-trackerheader-lib', DCE_URL . 'assets/node/headroom.js/headroom.min.js', array('jquery'), DCE_VERSION);
        }
        if (($theader_enabled || $smooth_enabled) && (get_option('enable_trackerheader') || get_option('enable_smoothtransition') || $is_in_editor)) {
            wp_enqueue_script('dce-globalsettings-js');
            wp_enqueue_style('dce-globalsettings');
            $settings_controls = get_option(\DynamicContentForElementor\GlobalSettings::META_KEY, []);
            wp_localize_script('dce-globalsettings-js', 'dceGlobalSettings', $settings_controls);
        }
    }
    /**
     * @return void
     */
    public static function add_depends($element)
    {
        // Style dependencies
        $w_styles = $element->get_style_depends();
        if (!empty($w_styles)) {
            foreach ($w_styles as $style) {
                wp_enqueue_style($style);
            }
        }
        // Script dependencies
        $w_scripts = $element->get_script_depends();
        if (!empty($w_scripts)) {
            foreach ($w_scripts as $script) {
                wp_enqueue_script($script);
            }
        }
    }
    /**
     * @return void
     */
    public function register_styles()
    {
        foreach (self::$styles as $name => $path) {
            $deps = [];
            // if the styles specifies dependencies:
            if (\is_array($path)) {
                $deps = $path['deps'] ?? [];
                $path = $path['path'];
            }
            if ('dce-style' !== $name) {
                // Add dce-style dependency for all styles
                $deps[] = 'dce-style';
            }
            if ('http' !== \substr($path, 0, 4)) {
                if (wp_get_environment_type() !== 'development' && !(WP_DEBUG || SCRIPT_DEBUG)) {
                    $path = \str_replace('.css', '.min.css', $path);
                }
                $path = plugins_url($path, DCE__FILE__);
            }
            wp_register_style($name, $path, $deps, DCE_VERSION);
        }
    }
    /**
     * @return void
     */
    public function register_vendor_styles()
    {
        foreach (self::$vendor_css as $name => $path) {
            // if the styles specifies dependencies:
            if (\is_array($path)) {
                $deps = $path['deps'] ?? [];
                $path = $path['path'];
            } else {
                $deps = [];
            }
            if ('http' !== \substr($path, 0, 4)) {
                $path = plugins_url($path, DCE__FILE__);
            }
            wp_register_style($name, $path, $deps, DCE_VERSION);
        }
    }
    /**
     * @return void
     */
    public static function register_dce_scripts()
    {
        foreach (self::$scripts as $name => $path) {
            $deps = [];
            // if the script specifies dependencies:
            if (\is_array($path)) {
                $deps = $path['deps'] ?? [];
                $path = $path['path'];
            }
            // Skip scripts with missing dependencies (e.g. API keys not configured)
            foreach ($deps as $dep) {
                if (!wp_script_is($dep, 'registered') && !isset(self::$vendor_js[$dep]) && !isset(self::$scripts[$dep])) {
                    continue 2;
                }
            }
            $deps[] = 'jquery';
            if ('dce-fix-background-loop' !== $name && 'dce-settings' !== $name) {
                // Add dce-fix-background-loop and dce-settings as dependencies for all scripts
                $deps[] = 'dce-fix-background-loop';
                $deps[] = 'dce-settings';
            }
            if ('http' !== \substr($path, 0, 4)) {
                if (wp_get_environment_type() !== 'development' && !(WP_DEBUG || SCRIPT_DEBUG)) {
                    $path = \str_replace('.js', '.min.js', $path);
                }
                $path = plugins_url($path, DCE__FILE__);
            }
            wp_register_script($name, $path, $deps, DCE_VERSION, \true);
            if ('dce-pdf-viewer' === $name) {
                wp_localize_script($name, 'dcePdfViewerConfig', ['workerSrc' => plugins_url('/assets/node/pdfjs-dist/pdf.worker.min.js', DCE__FILE__)]);
            }
        }
    }
    /**
     * @return void
     */
    public static function register_vendor_scripts()
    {
        foreach (self::$vendor_js as $name => $js_info) {
            // if the script specifies dependencies or in_footer
            if (\is_array($js_info)) {
                $deps = $js_info['deps'] ?? [];
                $in_footer = $js_info['in_footer'] ?? \true;
                $path = $js_info['path'];
            } else {
                $deps = [];
                $in_footer = \true;
                $path = $js_info;
            }
            // Skip scripts with missing dependencies (e.g. API keys not configured)
            foreach ($deps as $dep) {
                if (!wp_script_is($dep, 'registered') && !isset(self::$vendor_js[$dep])) {
                    continue 2;
                }
            }
            if (\substr($path, 0, 4) != 'http') {
                // Add DCE Path
                $path = plugins_url($path, DCE__FILE__);
                wp_register_script($name, $path, $deps, DCE_VERSION, $in_footer);
            } else {
                // version should stay null, paypal will complain about ver parameter otherwise:
                wp_register_script($name, $path, $deps, null, $in_footer);
            }
            // Set Leaflet marker images path explicitly (CSS minification breaks auto-detection).
            if ('dce-leaflet' === $name) {
                wp_localize_script($name, 'dceLeafletConfig', ['imagePath' => plugins_url('/assets/node/leaflet/images/', DCE__FILE__)]);
            }
        }
    }
    /**
     * @return void
     */
    public function register_scripts()
    {
        self::register_dce_scripts();
        self::register_vendor_scripts();
        self::add_libphonenumber_examples_inline_script();
    }
    /**
     * Adds libphonenumber examples data for the international phone field.
     *
     * The examples data is used by getExampleNumber() to show placeholder
     * phone numbers in the international phone field for each country.
     *
     * @return void
     */
    private static function add_libphonenumber_examples_inline_script()
    {
        $examples_file = DCE_PATH . '/assets/node/libphonenumber-js/examples.mobile.json';
        $examples_data = wp_json_file_decode($examples_file, ['associative' => \true]);
        if (!\is_array($examples_data)) {
            return;
        }
        wp_add_inline_script('dce-libphonenumber-js', 'window.libphonenumberExamples=' . wp_json_encode($examples_data) . ';', 'before');
    }
    /**
     * Enqueue inline CSS. Handles output automatically.
     *
     * @param string $handle Unique handle for the inline style
     * @param string $css CSS content (without <style> tags)
     * @param bool $element_id Not used, kept for backwards compatibility
     * @return void
     */
    public static function enqueue_inline_style($handle, $css = '', $element_id = \false)
    {
        if (empty($css)) {
            return;
        }
        // In edit mode, output directly for immediate rendering
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            echo '<style id="' . esc_attr($handle) . '">' . $css . '</style>';
            return;
        }
        // Frontend: use wp_add_inline_style
        // wp_add_inline_style() requires a registered style handle as an anchor point.
        // We register a "dummy" style (with false as src = no external CSS file)
        // just to have a handle to attach our inline CSS to.
        if (!wp_style_is($handle, 'registered')) {
            wp_register_style($handle, \false);
            // Register without external file
        }
        wp_enqueue_style($handle);
        wp_add_inline_style($handle, $css);
    }
    /**
     * Enqueue Admin Scripts
     *
     * @return void
     */
    public function enqueue_admin_scripts()
    {
        if (!$this->is_dce_admin_context()) {
            return;
        }
        // select2
        wp_enqueue_style('dce-select2', plugins_url('assets/node/select2/select2.min.css', DCE__FILE__), [], DCE_VERSION);
        wp_enqueue_script('dce-select2', plugins_url('assets/node/select2/select2.full.min.js', DCE__FILE__), array('jquery'), DCE_VERSION, \true);
        // Enqueue Admin Script
        wp_enqueue_script('dce-admin-js', plugins_url('assets/js/admin.js', DCE__FILE__), ['dce-select2'], DCE_VERSION, \true);
        $screen = get_current_screen();
        if ($screen && $screen->id === 'toplevel_page_dce-features') {
            $script_path = '/assets/js/features-page.js';
            if (wp_get_environment_type() !== 'development' && !(WP_DEBUG || SCRIPT_DEBUG)) {
                $script_path = \str_replace('.js', '.min.js', $script_path);
            }
            wp_enqueue_script('dce-features-page', plugins_url($script_path, DCE__FILE__), ['jquery'], DCE_VERSION, \true);
            wp_localize_script('dce-features-page', 'dceFeatures', ['nonce' => wp_create_nonce('dce_features_nonce'), 'i18n' => ['confirmDeactivate' => esc_html__('This feature is used %d times on your site. Deactivating it may affect those pages.', 'dynamic-content-for-elementor'), 'confirmBulkDeactivate' => esc_html__('%d features in use (%t total usages) will be deactivated. Continue?', 'dynamic-content-for-elementor'), 'saved' => esc_html__('Settings saved.', 'dynamic-content-for-elementor'), 'saveError' => esc_html__('Error saving settings. Please try again.', 'dynamic-content-for-elementor')]]);
        }
    }
    /**
     * Check if we are in our admin context
     *
     * @return bool
     */
    private function is_dce_admin_context()
    {
        $screen = get_current_screen();
        if (!$screen) {
            return \false;
        }
        // DCE admin pages (e.g. page=dce-features or page=dce_opt)
        if (\strpos($screen->id, 'dce-') !== \false || \strpos($screen->id, 'dce_') !== \false || \strpos($screen->id, 'dynamic-content-for-elementor') !== \false) {
            return \true;
        }
        // Post/term edit screens need Select2 only if Template System is active (for metabox)
        if ('post' === $screen->base || 'term' === $screen->base) {
            return \DynamicContentForElementor\Plugin::instance()->template_system->is_active();
        }
        return \false;
    }
    public function register_and_enqueue_dce_icons()
    {
        // Register styles
        wp_register_style('dce-style-icons', plugins_url('/assets/css/dce-icon.css', DCE__FILE__), [], DCE_VERSION);
        // Enqueue styles Icons
        wp_enqueue_style('dce-style-icons');
        // Logos
        wp_register_style('dce-logos', plugins_url('/assets/css/logos.css', DCE__FILE__), [], DCE_VERSION);
        wp_enqueue_style('dce-logos');
    }
    /**
     * The following scripts and styles are registered here because when
     * loading the elementor editor the wp actions used for the registrations
     * are not run.
     */
    public function editor_enqueue()
    {
        $this->register_and_enqueue_dce_icons();
        // Register styles
        wp_register_style('dce-style-editor', plugins_url('/assets/css/editor.css', DCE__FILE__), [], DCE_VERSION);
        wp_enqueue_style('dce-style-editor');
        // JS for Editor
        wp_register_script('dce-script-editor', plugins_url('/assets/js/editor.js', DCE__FILE__), [], DCE_VERSION, \true);
        wp_enqueue_script('dce-script-editor');
        // Labels on Dynamic Collection
        wp_localize_script('dce-script-editor', 'posts_v2_item_label_localization', ['item_title' => '<i class="fa fa-font" aria-hidden="true"></i> ' . esc_html__('Title', 'dynamic-content-for-elementor'), 'item_image' => '<i class="eicon-featured-image" aria-hidden="true"></i> ' . esc_html__('Featured Image', 'dynamic-content-for-elementor'), 'item_date' => '<i class="fa fa-calendar" aria-hidden="true"></i> ' . esc_html__('Date', 'dynamic-content-for-elementor'), 'item_termstaxonomy' => '<i class="eicon-tags" aria-hidden="true"></i> ' . esc_html__('Terms', 'dynamic-content-for-elementor'), 'item_content' => '<i class="fa fa-align-left" aria-hidden="true"></i> ' . esc_html__('Content', 'dynamic-content-for-elementor'), 'item_author' => '<i class="eicon-user-circle-o" aria-hidden="true"></i> ' . esc_html__('Author', 'dynamic-content-for-elementor'), 'item_custommeta' => '<i class="eicon-custom" aria-hidden="true"></i> ' . esc_html__('Custom Meta Field', 'dynamic-content-for-elementor'), 'item_jetengine' => '<i class="icon-dce-jetengine" aria-hidden="true"></i> ' . esc_html__('JetEngine Field', 'dynamic-content-for-elementor'), 'item_metabox' => '<i class="icon-dce-metabox" aria-hidden="true"></i> ' . esc_html__('Meta Box Field', 'dynamic-content-for-elementor'), 'item_readmore' => '<i class="eicon-button" aria-hidden="true"></i> ' . esc_html__('Read More', 'dynamic-content-for-elementor'), 'item_posttype' => '<i class="eicon-post-info" aria-hidden="true"></i> ' . esc_html__('Post Type', 'dynamic-content-for-elementor'), 'item_productprice' => '<i class="eicon-product-price" aria-hidden="true"></i> ' . esc_html__('Product Price', 'dynamic-content-for-elementor'), 'item_sku' => '<i class="eicon-product-info" aria-hidden="true"></i> ' . esc_html__('Product SKU', 'dynamic-content-for-elementor'), 'item_addtocart' => '<i class="eicon-product-add-to-cart" aria-hidden="true"></i> ' . esc_html__('Add to Cart', 'dynamic-content-for-elementor')]);
        // Features by Collection 'dynamic-posts'
        wp_localize_script('dce-script-editor', 'dce_features_collection_dynamic_posts', $this->features_collection_dynamic_posts());
        // Nonce for Dynamic Shortcodes Wizard
        wp_localize_script('dce-script-editor', 'dce_editor_config', ['nonce' => wp_create_nonce('dce_generate_shortcode')]);
    }
    public function frontend_enqueue()
    {
        // Features by Collection 'dynamic-posts'
        wp_localize_script('dce-dynamicPosts-base', 'dce_features_collection_dynamic_posts', $this->features_collection_dynamic_posts());
        wp_localize_script('dce-google-maps', 'dce_ajax_object', array('ajax_url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('dce_load_template')));
        wp_localize_script('dce-add-to-favorites', 'dce_vars', array('ajaxurl' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('dce_add_to_favorites')));
        wp_localize_script('dce-clear-favorites', 'dce_clear_favorites_vars', array('nonce' => wp_create_nonce('dce_clear_favorites')));
        wp_localize_script('dce-file-browser', 'dce_file_browser_vars', array('ajaxurl' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('dce_file_browser_hits')));
        wp_localize_script('dce-views', 'dce_frontend_vars', array('url' => DCE_URL, 'ajax_url' => admin_url('admin-ajax.php')));
    }
    /**
     * Features Collection Dynamic Posts
     *
     * @return array<string>
     */
    public function features_collection_dynamic_posts()
    {
        static $res = null;
        if ($res === null) {
            $res = \DynamicContentForElementor\Plugin::instance()->features->get_feature_info_by_array(\DynamicContentForElementor\Plugin::instance()->features->filter_by_collection('dynamic-posts'), 'name');
        }
        return $res;
    }
    /**
     * Enqueue preview styles
     *
     * @since 1.0.3
     *
     * @access public
     */
    public function preview_enqueue()
    {
        // Enqueue DCE Elementor Style
        wp_enqueue_style('dce-preview');
    }
    /**
     * @return void
     */
    public function enqueue_custom_editor_app()
    {
        $asset_path = DCE_PATH . 'includes/custom-editor/build/index.asset.php';
        if (!\file_exists($asset_path)) {
            return;
        }
        /** @var array{dependencies:string[],version:string} $asset_file */
        $asset_file = (include $asset_path);
        $dependencies = \array_merge($asset_file['dependencies'], ['jquery', 'elementor-editor']);
        // Enqueue React JS with correct dependencies
        wp_enqueue_script('dce-custom-editor-app', DCE_URL . 'includes/custom-editor/build/index.js', $dependencies, $asset_file['version'], \true);
        wp_localize_script('dce-custom-editor-app', 'dceCustomEditorConfig', ['pluginUrl' => DCE_URL, 'pluginPath' => DCE_PATH, 'version' => DCE_VERSION, 'ajaxUrl' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('dce-custom-editor'), 'elementorVersion' => ELEMENTOR_VERSION, 'isEditMode' => \true, 'dev_mode' => \false]);
    }
    /**
     * Enqueue DCE Icons
     *
     * @return void
     */
    public static function enqueue_dce_icons()
    {
        // Enqueue styles Icons
        wp_enqueue_style('dce-style-icons');
    }
}
