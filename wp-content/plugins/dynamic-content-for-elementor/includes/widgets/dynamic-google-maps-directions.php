<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Extensions\DynamicTags\DynamicGoogleMapsDirectionsInfo;
use DynamicContentForElementor\Extensions\DynamicTags\DynamicGoogleMapsDirectionsInstructions;
if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly map
class DynamicGoogleMapsDirections extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    /**
     * @var array<int,array<string,mixed>>
     */
    protected $positions = [];
    /**
     * Get Scripts Depends
     *
     * @return array<string>
     */
    public function get_script_depends()
    {
        return ['dce-dynamic-google-maps-directions'];
    }
    /**
     * Get Style Depends
     *
     * @return array<string>
     */
    public function get_style_depends()
    {
        return ['dce-dynamic-google-maps-directions'];
    }
    /**
     * Run Once
     *
     * @return void
     */
    public function run_once()
    {
        new DynamicGoogleMapsDirectionsInfo();
        new DynamicGoogleMapsDirectionsInstructions();
        $save_guard = \DynamicContentForElementor\Plugin::instance()->save_guard;
        $save_guard->register_unsafe_control($this->get_name(), 'departure_info_window');
        $save_guard->register_unsafe_control($this->get_name(), 'destination_info_window');
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_map', ['label' => $this->get_title()]);
        if (!get_option('dce_google_maps_api')) {
            $this->add_control('api_notice', ['type' => Controls_Manager::RAW_HTML, 'raw' => esc_html__('In order to use this feature you should set Google Maps API, with Geocoding API enabled, on Integrations section', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning']);
        }
        $this->add_control('map_data_type', ['label' => esc_html__('Data Type', 'dynamic-content-for-elementor'), 'type' => 'images_selector', 'toggle' => \false, 'type_selector' => 'icon', 'columns_grid' => 5, 'default' => 'address', 'options' => ['address' => ['title' => esc_html__('Address', 'dynamic-content-for-elementor'), 'return_val' => 'val', 'icon' => 'fa fa-map-marker-alt'], 'latlng' => ['title' => esc_html__('Latitude and longitude', 'dynamic-content-for-elementor'), 'return_val' => 'val', 'icon' => 'fa fa-globe-europe']], 'frontend_available' => \true]);
        $this->add_control('travel_mode', ['label' => esc_html__('Travel Mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'frontend_available' => \true, 'default' => 'driving', 'options' => ['driving' => esc_html__('Driving', 'dynamic-content-for-elementor'), 'walking' => esc_html__('Walking', 'dynamic-content-for-elementor'), 'bicycling' => esc_html__('Bicycling', 'dynamic-content-for-elementor'), 'transit' => esc_html__('Transit', 'dynamic-content-for-elementor'), 'two_wheeler' => esc_html__('Two Wheeler', 'dynamic-content-for-elementor')]]);
        $this->add_control('departure_address', ['label' => esc_html__('Departure', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => 'Venice, Italy', 'label_block' => \true, 'condition' => ['map_data_type' => 'address']]);
        $this->add_control('destination_address', ['label' => esc_html__('Destination', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => 'Milan, Italy', 'label_block' => \true, 'condition' => ['map_data_type' => 'address']]);
        $this->add_control('departure_heading', ['type' => Controls_Manager::HEADING, 'label' => esc_html__('Departure', 'dynamic-content-for-elementor'), 'condition' => ['map_data_type' => 'latlng']]);
        $this->add_control('departure_latitude', ['label' => esc_html__('Latitude', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '45.4408474', 'condition' => ['map_data_type' => 'latlng']]);
        $this->add_control('departure_longitude', ['label' => esc_html__('Longitude', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '12.3155151', 'condition' => ['map_data_type' => 'latlng']]);
        $this->add_control('destination_heading', ['type' => Controls_Manager::HEADING, 'label' => esc_html__('Destination', 'dynamic-content-for-elementor'), 'condition' => ['map_data_type' => 'latlng']]);
        $this->add_control('destination_latitude', ['label' => esc_html__('Latitude', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '45.4600113', 'condition' => ['map_data_type' => 'latlng']]);
        $this->add_control('destination_longitude', ['label' => esc_html__('Longitude', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '9.1797373', 'condition' => ['map_data_type' => 'latlng']]);
        $this->end_controls_section();
        $this->start_controls_section('section_map_name', ['label' => esc_html__('Map Name', 'dynamic-content-for-elementor')]);
        $this->add_control('map_name', ['label' => esc_html__('Map Name', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '', 'description' => esc_html__('Enter a name without spaces and lower case to identify the map when used with Dynamic Tags', 'dynamic-content-for-elementor'), 'label_block' => 'true', 'frontend_available' => \true]);
        $this->end_controls_section();
        $this->start_controls_section('section_controlling', ['label' => esc_html__('Controlling', 'dynamic-content-for-elementor')]);
        $this->add_control('geolocation', ['label' => esc_html__('Geolocation', 'dynamic-content-for-elementor'), 'description' => esc_html__('Display the geographic location of the user on the map, using browser\'s HTML5 Geolocation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->add_control('use_geolocation_as', ['label' => esc_html__('Use Geolocation Point as', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'frontend_available' => \true, 'default' => 'departure', 'options' => ['departure' => esc_html__('Departure', 'dynamic-content-for-elementor'), 'end' => esc_html__('Destination', 'dynamic-content-for-elementor')], 'condition' => ['geolocation' => 'yes']]);
        $this->add_control('geolocation_button_text', ['label' => esc_html__('Text for Geolocation button', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Choose the Geolocation Point', 'dynamic-content-for-elementor'), 'label_block' => 'true', 'frontend_available' => \true, 'condition' => ['geolocation' => 'yes']]);
        $this->add_control('zoom', ['label' => esc_html__('Zoom', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'separator' => 'before', 'frontend_available' => \true, 'size_units' => ['px'], 'range' => ['px' => ['min' => 1, 'max' => 22, 'step' => 1]], 'default' => ['size' => 5]]);
        $this->end_controls_section();
        $this->start_controls_section('section_map_type', ['label' => esc_html__('Map Type', 'dynamic-content-for-elementor')]);
        $this->add_control('map_type', ['label' => esc_html__('Map Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'frontend_available' => \true, 'default' => 'roadmap', 'options' => ['roadmap' => esc_html__('Roadmap', 'dynamic-content-for-elementor'), 'satellite' => esc_html__('Satellite', 'dynamic-content-for-elementor'), 'hybrid' => esc_html__('Hybrid', 'dynamic-content-for-elementor'), 'terrain' => esc_html__('Terrain', 'dynamic-content-for-elementor')]]);
        $this->end_controls_section();
        $this->start_controls_section('section_marker', ['label' => esc_html__('Markers', 'dynamic-content-for-elementor')]);
        $this->add_control('markers', ['label' => esc_html__('Custom Markers', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        /* START MARKERS SECTIONS */
        $this->add_control('markers_departure_heading', ['label' => esc_html__('Departure', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'condition' => ['markers' => 'yes']]);
        $this->add_control('departure_marker_title', ['label' => esc_html__('Title', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'label_block' => 'true', 'frontend_available' => \true, 'condition' => ['markers' => 'yes']]);
        $this->add_control('departure_marker_label', ['label' => esc_html__('Label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'label_block' => 'true', 'frontend_available' => \true, 'condition' => ['markers' => 'yes']]);
        $this->add_control('departure_marker_image', ['label' => esc_html__('Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::MEDIA, 'frontend_available' => \true, 'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()], 'skin' => 'inline', 'condition' => ['markers' => 'yes']]);
        $this->add_control('departure_marker_size', ['label' => esc_html__('Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'frontend_available' => \true, 'separator' => 'after', 'size_units' => ['px'], 'range' => ['px' => ['min' => 20, 'max' => 100, 'step' => 1]], 'default' => ['size' => 30], 'condition' => ['markers' => 'yes']]);
        $this->add_control('markers_destination_heading', ['label' => esc_html__('Destination', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'condition' => ['markers' => 'yes']]);
        $this->add_control('destination_marker_title', ['label' => esc_html__('Title', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'label_block' => 'true', 'frontend_available' => \true, 'condition' => ['markers' => 'yes']]);
        $this->add_control('destination_marker_label', ['label' => esc_html__('Label', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'label_block' => 'true', 'frontend_available' => \true, 'condition' => ['markers' => 'yes']]);
        $this->add_control('destination_marker_image', ['label' => esc_html__('Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::MEDIA, 'frontend_available' => \true, 'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()], 'condition' => ['markers' => 'yes']]);
        $this->add_control('destination_marker_size', ['label' => esc_html__('Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'frontend_available' => \true, 'size_units' => ['px'], 'range' => ['px' => ['min' => 20, 'max' => 100, 'step' => 1]], 'default' => ['size' => 30], 'condition' => ['markers' => 'yes']]);
        $this->end_controls_section();
        /* Info Window Options */
        $this->start_controls_section('section_info_window', ['label' => esc_html__('InfoWindow', 'dynamic-content-for-elementor'), 'condition' => ['markers' => 'yes']]);
        $this->add_control('infoWindow', ['label' => esc_html__('Custom InfoWindow', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->add_control('departure_info_window', ['label' => esc_html__('Departure', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXTAREA, 'frontend_available' => \true, 'default' => esc_html__('Departure', 'dynamic-content-for-elementor'), 'rows' => 5, 'placeholder' => esc_html__('Type your text here', 'dynamic-content-for-elementor'), 'condition' => ['infoWindow' => 'yes']]);
        $this->add_control('destination_info_window', ['label' => esc_html__('Destination', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXTAREA, 'frontend_available' => \true, 'default' => esc_html__('Destination', 'dynamic-content-for-elementor'), 'rows' => 5, 'placeholder' => esc_html__('Type your text here', 'dynamic-content-for-elementor'), 'condition' => ['infoWindow' => 'yes']]);
        $this->end_controls_section();
        $this->start_controls_section('section_controls', ['label' => esc_html__('Controls', 'dynamic-content-for-elementor')]);
        $this->add_control('scrollwheel', ['label' => esc_html__('Scroll Wheel', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('maptypecontrol', ['label' => esc_html__('Map Type Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('pancontrol', ['label' => esc_html__('Pan Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('rotatecontrol', ['label' => esc_html__('Rotate Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('scalecontrol', ['label' => esc_html__('Scale Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('streetviewcontrol', ['label' => esc_html__('Street View Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('zoomcontrol', ['label' => esc_html__('Zoom Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('fullscreenControl', ['label' => esc_html__('Full Screen Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->end_controls_section();
        $this->start_controls_section('section_style', ['label' => esc_html__('Map', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('height', ['label' => esc_html__('Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['unit' => 'px', 'size' => 300], 'range' => ['px' => ['min' => 40, 'max' => 1440]], 'selectors' => ['{{WRAPPER}} .map' => 'height: {{SIZE}}{{UNIT}};']]);
        $this->end_controls_section();
    }
    /**
     * Safe Render
     *
     * @return void
     */
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $list_options = $settings['list_options'] ?? [];
        $this->add_render_attribute('map', ['data-list-options' . $this->get_id() => wp_json_encode($list_options)]);
        // Map Name
        if (!empty($settings['map_name'])) {
            if (!\ctype_lower($settings['map_name']) || \strpos($settings['map_name'], ' ')) {
                Helper::notice('', esc_html__('The name of the map should be written without spaces and in lower case', 'dynamic-content-for-elementor'), 'danger');
            }
            $this->add_render_attribute('map', ['data-map-name' => $settings['map_name']]);
        }
        if ('address' === $settings['map_data_type']) {
            $departure_address = $settings['departure_address'];
            $destination_address = $settings['destination_address'];
            self::add_position($departure_address);
            self::add_position($destination_address);
        } elseif ('latlng' === $settings['map_data_type']) {
            $departure_latitude = $settings['departure_latitude'];
            $departure_longitude = $settings['departure_longitude'];
            self::add_position('', $departure_latitude, $departure_longitude);
            $destination_latitude = $settings['destination_latitude'];
            $destination_longitude = $settings['destination_longitude'];
            self::add_position('', $destination_latitude, $destination_longitude);
        }
        $this->render_map();
    }
    /**
     * Render Map
     *
     * @return void
     */
    protected function render_map()
    {
        $this->add_render_attribute('map', ['class' => ['map'], 'id' => ['map'], 'widget_id' => [$this->get_id()], 'style' => ['width: 100%']]);
        $this->add_render_attribute('map', ['data-positions' . $this->get_id() => [wp_json_encode($this->get_positions())]]);
        ?>

		<div <?php 
        echo $this->get_render_attribute_string('map');
        ?>></div>
		<?php 
    }
    /**
     * Get Positions
     *
     * @return array<int,array<string,mixed>>
     */
    protected function get_positions()
    {
        return $this->positions;
    }
    /**
     * Add a position to positions list
     *
     * @param string $address
     * @param string $latitude
     * @param string $longitude
     * @return void
     */
    protected function add_position($address = '', $latitude = '', $longitude = '')
    {
        if (!$address && (!$latitude || !$longitude)) {
            return;
        }
        $this->positions[] = ['address' => sanitize_text_field($address), 'lat' => \floatval($latitude), 'lng' => \floatval($longitude)];
    }
}
