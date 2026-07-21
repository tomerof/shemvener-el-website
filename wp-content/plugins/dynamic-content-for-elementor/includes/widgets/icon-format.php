<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class IconFormat extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_style_depends()
    {
        return ['dashicons', 'dce-iconFormat'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_cpt', ['label' => $this->get_title()]);
        $this->add_responsive_control('icon_size', ['label' => esc_html__('Icon Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px', 'em', 'rem', 'vw', 'custom'], 'default' => ['size' => 30, 'unit' => 'px'], 'range' => ['px' => ['min' => 0, 'max' => 100], 'em' => ['min' => 0, 'max' => 10], 'rem' => ['min' => 0, 'max' => 10]], 'selectors' => ['{{WRAPPER}} .dashicons:before' => 'font-size: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('padding_size', ['label' => esc_html__('Padding Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 10], 'range' => ['px' => ['min' => 0, 'max' => 50]], 'selectors' => ['{{WRAPPER}} .dashicons' => 'padding: {{SIZE}}{{UNIT}};']]);
        $this->add_control('color_icon', ['label' => esc_html__('Icon Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dashicons:before' => 'color: {{VALUE}};']]);
        $this->add_control('color_bg', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dashicons' => 'background-color: {{VALUE}};']]);
        $this->add_responsive_control('icon_align', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'default' => '', 'selectors' => ['{{WRAPPER}}' => 'text-align: {{VALUE}};']]);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $id_page = Helper::get_the_id();
        $format = get_post_format($id_page);
        switch ($format) {
            case 'aside':
                $strformat = 'dashicons-format-aside';
                break;
            case 'chat':
                $strformat = 'dashicons-format-chat';
                break;
            case 'gallery':
                $strformat = 'dashicons-format-gallery';
                break;
            case 'link':
                $strformat = 'dashicons-admin-links';
                break;
            case 'image':
                $strformat = 'dashicons-format-image';
                break;
            case 'quote':
                $strformat = 'dashicons-format-quote';
                break;
            case 'status':
                $strformat = 'dashicons-format-status';
                break;
            case 'video':
                $strformat = 'dashicons-format-video';
                break;
            case 'audio':
                $strformat = 'dashicons-format-audio';
                break;
            case '':
            default:
                $strformat = 'dashicons-admin-post';
                break;
        }
        echo '<span class="dashicons ' . $strformat . '"></span>';
    }
}
