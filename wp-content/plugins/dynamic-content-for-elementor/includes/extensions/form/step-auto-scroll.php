<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class StepAutoScroll extends \DynamicContentForElementor\Extensions\ExtensionPrototype
{
    /**
     * @var boolean
     */
    public $has_action = \false;
    /**
     * @return string
     */
    public function get_name()
    {
        return 'dce_form_step';
    }
    /**
     * @return string
     */
    public function get_label()
    {
        return esc_html__('Step Auto-Scroll', 'dynamic-content-for-elementor');
    }
    /**
     * @return bool
     */
    public function is_common()
    {
        return \false;
    }
    /**
     * @return void
     */
    protected function add_actions()
    {
        add_action('elementor/frontend/widget/before_render', [$this, 'before_render']);
        add_action('elementor/element/form/section_steps_settings/before_section_end', [$this, 'add_control_section_to_steps'], 10, 2);
    }
    /**
     * @param \Elementor\Element_Base $element
     * @param array<string,mixed> $args
     * @return void
     */
    public function add_control_section_to_steps($element, $args)
    {
        $element->add_control('dce_step_scroll', ['label' => '<span class="color-dce icon-dce-logo-dce"></span> ' . esc_html__('Scroll to Top on Step change', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
    }
    /**
     * @param \Elementor\Widget_Base $widget
     * @return void
     */
    public function before_render($widget)
    {
        if ($widget->get_name() !== 'form') {
            return;
        }
        $settings = $widget->get_settings_for_display();
        if (empty($settings['dce_step_scroll'])) {
            return;
        }
        // Check if form has steps
        $has_steps = \false;
        if (!empty($settings['form_fields'])) {
            foreach ($settings['form_fields'] as $field) {
                if ($field['field_type'] === 'step') {
                    $has_steps = \true;
                    break;
                }
            }
        }
        if (!$has_steps) {
            return;
        }
        wp_enqueue_script('dce-step-auto-scroll');
        $widget->add_render_attribute('form', 'data-dce-scroll-to-step', 'yes');
    }
}
