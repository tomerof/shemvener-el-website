<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Modules\DynamicTags\Tags;

use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class DynamicGoogleMapsDirectionsInstructions extends Data_Tag
{
    /**
     * Get Name
     *
     * @return string
     */
    public function get_name()
    {
        return 'dce-dynamic-google-maps-directions-instructions';
    }
    /**
     * Get Title
     *
     * @return string
     */
    public function get_title()
    {
        return esc_html__('Map Instructions', 'dynamic-content-for-elementor');
    }
    /**
     * Get Group
     *
     * @return string
     */
    public function get_group()
    {
        return 'dce-dynamic-google-maps-directions';
    }
    /**
     * Get Categories
     *
     * @return array<string>
     */
    public function get_categories()
    {
        return ['base', 'text'];
    }
    /**
     * Get value
     *
     * @param array<mixed> $options
     * @return string|null
     */
    public function get_value(array $options = [])
    {
        $map_name = $this->get_settings('map_name');
        $loading_text = wp_kses_post($this->get_settings('loading_text'));
        if (empty($map_name) && \Elementor\Plugin::$instance->editor->is_edit_mode()) {
            return Helper::notice(\false, esc_html__('Please type a Map Name', 'dynamic-content-for-elementor'));
        }
        return "<div data-tag-name='" . esc_attr($map_name) . "' id='dce-gm-directions-instructions'><span id='print_instructions' data-instructions='" . esc_attr($map_name) . "' class='distance dce-gm-directions-instructions'>" . $loading_text . '</span></div>';
    }
    /**
     * Register Controls
     *
     * @return void
     */
    protected function register_controls()
    {
        $this->add_control('map_name', ['label' => esc_html__('Map Name', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '']);
        $this->add_control('loading_text', ['label' => esc_html__('Loading Text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Loading...', 'dynamic-content-for-elementor'), 'label_block' => 'true']);
    }
}
