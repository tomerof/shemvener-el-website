<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
class PhpRaw extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_rawphp', ['label' => esc_html__('PHP Raw', 'dynamic-content-for-elementor')]);
        $this->add_control('custom_php', ['label' => esc_html__('PHP Code', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CODE, 'language' => 'php']);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        $evalError = \false;
        // The following is needed because if the code echoes only a
        // '0', Elementor will not render the widget at all.
        echo '<!-- Dynamic PHP Raw -->';
        try {
            @eval($settings['custom_php']);
        } catch (\ParseError $e) {
            $evalError = \true;
        } catch (\Throwable $e) {
            $evalError = \true;
        }
        if ($evalError && \Elementor\Plugin::$instance->editor->is_edit_mode()) {
            echo '<strong>';
            echo esc_html__('Please check your PHP code', 'dynamic-content-for-elementor');
            echo '</strong><br />';
            echo 'ERROR: ', $e->getMessage(), "\n";
        }
    }
}
