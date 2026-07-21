<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

use Elementor\Icons_Manager;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class DynamicCookie extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_script_depends()
    {
        return ['dce-dynamic-cookie'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_content', ['label' => $this->get_title()]);
        $this->add_control('setcookie', ['label' => esc_html__('Mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_on' => esc_html__('Set', 'dynamic-content-for-elementor'), 'label_off' => esc_html__('Unset', 'dynamic-content-for-elementor'), 'return_value' => 'yes', 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('cookie_name', ['label' => esc_html__('Cookie name', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'frontend_available' => \true]);
        $this->add_control('cookie_if_exists', ['label' => esc_html__('If the cookie exists', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'append_comma', 'frontend_available' => \true, 'options' => ['append_comma' => esc_html__('Append the new value with a comma', 'dynamic-content-for-elementor'), 'overwrite' => esc_html__('Overwrite the cookie with the new value', 'dynamic-content-for-elementor')], 'condition' => ['setcookie' => 'yes']]);
        $this->add_control('cookie_value', ['label' => esc_html__('Value', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'frontend_available' => \true, 'dynamic' => ['active' => \true], 'condition' => ['setcookie' => 'yes']]);
        $this->add_control('cookie_expires', ['label' => esc_html__('Cookie expiration', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'separator' => 'before', 'default' => 30, 'frontend_available' => \true, 'min' => 0, 'description' => esc_html__('Set 0 or empty for session duration.', 'dynamic-content-for-elementor'), 'condition' => ['setcookie' => 'yes']]);
        $this->add_control('cookie_expires_value', ['label' => esc_html__('Cookie expiration value in', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'days', 'frontend_available' => \true, 'options' => ['minutes' => esc_html__('minutes', 'dynamic-content-for-elementor'), 'days' => esc_html__('days', 'dynamic-content-for-elementor')], 'condition' => ['setcookie' => 'yes']]);
        $this->end_controls_section();
    }
    public function safe_render()
    {
        echo '<!-- dynamic cookie -->';
    }
}
