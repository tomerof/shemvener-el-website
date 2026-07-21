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
class Iframe extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    /**
     * @return array<string>
     */
    public function get_script_depends()
    {
        return ['dce-iframe'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_iframe', ['label' => $this->get_title(), 'tab' => Controls_Manager::TAB_CONTENT]);
        $this->add_control('url', ['label' => esc_html__('URL', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '']);
        $this->add_responsive_control('iframe_height', ['label' => esc_html__('Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '80', 'unit' => 'vh'], 'range' => ['px' => ['min' => 0, 'max' => 1920, 'step' => 1], '%' => ['min' => 5, 'max' => 100, 'step' => 1], 'vh' => ['min' => 5, 'max' => 100, 'step' => 1]], 'size_units' => ['%', 'px', 'vh'], 'selectors' => ['{{WRAPPER}} iframe' => 'height: {{SIZE}}{{UNIT}};']]);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings['url'])) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                Helper::notice(\false, esc_html__('Add an URL to begin', 'dynamic-content-for-elementor'));
            }
            return;
        }
        $url = $settings['url'];
        if (!\filter_var($url, \FILTER_VALIDATE_URL)) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                Helper::notice(\false, esc_html__('URL not valid', 'dynamic-content-for-elementor'));
            }
            return;
        }
        $this->add_render_attribute('iframe', [$this->get_src_attribute() => $this->get_src($url), 'frameborder' => '0', 'width' => '100%', 'height' => $settings['iframe_height']['size'] ?? '80vh', 'allowfullscreen' => 'true']);
        ?>
		<iframe <?php 
        echo $this->get_render_attribute_string('iframe');
        ?>></iframe>
		<?php 
    }
    /**
     * @return string
     */
    protected function get_src_attribute()
    {
        return 'src';
    }
    /**
     * @param string $url
     * @return string
     */
    protected function get_src($url)
    {
        return esc_url_raw($url);
    }
}
