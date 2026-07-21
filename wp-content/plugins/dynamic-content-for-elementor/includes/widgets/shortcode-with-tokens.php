<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Plugin;
if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
class ShortcodeWithTokens extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_doshortcode', ['label' => $this->get_title()]);
        Plugin::instance()->text_templates->maybe_add_notice($this);
        $this->add_control('doshortcode_string', ['label' => $this->get_title(), 'type' => Controls_Manager::TEXTAREA, 'description' => esc_html__('Example:', 'dynamic-content-for-elementor') . ' [gallery ids="[post:custom-meta]"]']);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings) || empty($settings['doshortcode_string'])) {
            return;
        }
        echo do_shortcode(Helper::get_dynamic_value($settings['doshortcode_string']));
    }
}
