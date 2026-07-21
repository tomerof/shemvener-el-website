<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Tilt extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_script_depends()
    {
        return ['dce-tilt'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_tilt', ['label' => esc_html__('Tilt', 'dynamic-content-for-elementor')]);
        $this->add_control('template', ['label' => esc_html__('Select Template', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Template Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'object_type' => 'elementor_library']);
        $this->add_control('translatez_template', ['label' => esc_html__('Translate Z', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 0, 'min' => 0, 'max' => 200, 'step' => 1, 'frontend_available' => \true, 'selectors' => ['{{WRAPPER}} .template-inner' => 'transform: translateZ({{VALUE}}px);']]);
        $this->add_control('tilt_maxtilt', ['label' => esc_html__('Max Tilt', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 5], 'range' => ['min' => 0, 'max' => 10, 'step' => 1], 'frontend_available' => \true]);
        $this->add_control('tilt_perspective', ['label' => esc_html__('Perspective', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 1000, 'min' => 0, 'max' => 2000, 'step' => 10, 'frontend_available' => \true]);
        $this->add_control('tilt_scale', ['label' => esc_html__('Scale', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 1, 'min' => 1, 'max' => 2, 'step' => 0.01]);
        $this->add_control('tilt_speed', ['label' => esc_html__('Speed', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 300, 'min' => 0, 'max' => 1000, 'step' => 10, 'frontend_available' => \true]);
        $this->add_control('tilt_transition', ['label' => esc_html__('Transition', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('tilt_reset', ['label' => esc_html__('Reset', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('tilt_glare', ['label' => esc_html__('Glare', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true]);
        $this->add_control('tilt_maxGlare', ['label' => esc_html__('Max Glare', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 1, 'min' => 0, 'max' => 1, 'step' => 0.1]);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $this->add_render_attribute('wrapper', 'class', 'dce_tilt');
        $this->add_render_attribute('tilt-container', 'class', ['js-tilt']);
        $this->add_render_attribute('template-container', 'class', ['template-inner']);
        $template = $settings['template'];
        ?>
		<div <?php 
        echo $this->get_render_attribute_string('wrapper');
        ?>>
			<div <?php 
        echo $this->get_render_attribute_string('tilt-container');
        ?>>
				<?php 
        if ($template != '') {
            $template_system = \DynamicContentForElementor\Plugin::instance()->template_system;
            ?>
					<div <?php 
            echo $this->get_render_attribute_string('template-container');
            ?>>
						<?php 
            echo $template_system->build_elementor_template_special(['id' => $template]);
            ?>
					</div>
				<?php 
        } else {
            ?>
					<div class="tilt-inner"></div>
				<?php 
        }
        ?>
			</div>
		</div>
		<?php 
    }
}
