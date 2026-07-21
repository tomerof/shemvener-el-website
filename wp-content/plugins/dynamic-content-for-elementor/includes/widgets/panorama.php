<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Panorama extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    /**
     * @return array<string>
     */
    public function get_script_depends()
    {
        return ['dce-aframe'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_panorama', ['label' => esc_html__('Panorama', 'dynamic-content-for-elementor')]);
        $this->add_control('image_source', ['label' => esc_html__('Source image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'frontend_available' => \true, 'options' => ['from_media' => esc_html__('From Media Library', 'dynamic-content-for-elementor'), 'custom_url' => esc_html__('Custom URL', 'dynamic-content-for-elementor')], 'default' => 'from_media']);
        $this->add_control('custom_url_panorama_image', ['label' => esc_html__('Custom URL', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'placeholder' => 'https://www...', 'label_block' => \true, 'dynamic' => ['active' => \true], 'condition' => ['image_source' => 'custom_url']]);
        $this->add_control('panorama_image', ['label' => esc_html__('Panorama Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::MEDIA, 'dynamic' => ['active' => \true], 'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()], 'condition' => ['image_source' => 'from_media']]);
        $this->add_responsive_control('height_scene', ['label' => esc_html__('Scene height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px', 'rem', 'vh'], 'default' => ['unit' => 'px', 'size' => 550], 'range' => ['px' => ['min' => 0, 'max' => 1000], 'rem' => ['min' => 0, 'max' => 30], 'vh' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} a-scene, {{WRAPPER}} .a-scene-placeholder' => 'height: {{SIZE}}{{UNIT}};'], 'render_type' => 'template', 'separator' => 'after']);
        $this->add_control('params_heading', ['label' => esc_html__('Parameters', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('fullscreen_vr', ['label' => esc_html__('Fullscreen', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
        $this->add_control('vr_mode_ui', ['label' => esc_html__('VR mode UI', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
        $this->add_control('keyboard_shortcuts', ['label' => esc_html__('Enables the shortcut to press F to enter VR', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
        $this->add_control('reversemousecontrol', ['label' => esc_html__('Reverse mouse control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
        $this->end_controls_section();
    }
    /**
     * @return void
     */
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $this->add_render_attribute('a-scene', 'class', 'panorama-scene');
        if (!$settings['fullscreen_vr']) {
            $this->add_render_attribute('a-scene', 'embedded', '');
        }
        if (!$settings['vr_mode_ui']) {
            $this->add_render_attribute('a-scene', 'vr-mode-ui', 'enabled: false');
        }
        if (!$settings['keyboard_shortcuts']) {
            $this->add_render_attribute('a-scene', 'keyboard-shortcuts', 'enterVR: false');
        }
        $url_image = $settings['panorama_image']['url'];
        if ($settings['image_source'] === 'custom_url' && !empty($settings['custom_url_panorama_image'])) {
            $url_image = $settings['custom_url_panorama_image'];
        }
        if (!$url_image) {
            $url_image = \Elementor\Utils::get_placeholder_image_src();
        }
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            ?>
			<div class="a-scene-placeholder" style="background-image: url('<?php 
            echo esc_url($url_image);
            ?>');background-size: cover;background-position: center center;"></div>
			<?php 
        } else {
            ?>
			<a-scene <?php 
            $this->print_render_attribute_string('a-scene');
            ?>>
				<a-assets>
					<img id="sky_<?php 
            echo esc_attr($this->get_id());
            ?>" loading="eager" src="<?php 
            echo esc_url($url_image);
            ?>" crossorigin="anonymous">
				</a-assets>
				<?php 
            if (!empty($settings['reversemousecontrol'])) {
                ?>
					<a-camera mouse-cursor reverse-mouse-drag="true" id="cam" zoom="1.3"></a-camera>
				<?php 
            }
            ?>
				<a-sky src="#sky_<?php 
            echo esc_attr($this->get_id());
            ?>" rotation="0 -130 0"></a-sky>
			</a-scene>
			<?php 
        }
    }
}
