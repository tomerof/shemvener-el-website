<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Icons_Manager;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class SubmitOnChange extends \DynamicContentForElementor\Extensions\ExtensionPrototype
{
    public $has_action = \false;
    public function get_name()
    {
        return 'dce_form_onchange';
    }
    public function get_label()
    {
        return esc_html__('Onchange', 'dynamic-content-for-elementor');
    }
    /**
     * @return bool
     */
    public function is_common()
    {
        return \false;
    }
    protected function add_actions()
    {
        add_action('elementor/widget/render_content', array($this, '_render_form'), 10, 2);
        add_action('elementor/element/form/section_form_fields/before_section_end', [$this, 'update_fields_controls']);
        add_filter('elementor/widget/print_template', function ($template, $widget) {
            if ('form' === $widget->get_name()) {
                $template = \false;
            }
            return $template;
        }, 10, 2);
    }
    public function _render_form($content, $widget)
    {
        if ($widget->get_name() != 'form') {
            return $content;
        }
        $settings = $widget->get_settings_for_display();
        $onchange_fields = [];
        foreach ($settings['form_fields'] as $field) {
            if (!empty($field['field_onchange'])) {
                $onchange_fields[] = $field['custom_id'];
            }
        }
        if (empty($onchange_fields)) {
            return $content;
        }
        wp_enqueue_script('dce-submit-on-change');
        $config = ['fields' => $onchange_fields];
        $widget->add_render_attribute('_wrapper', 'data-dce-submit-on-change', wp_json_encode($config));
        return $content;
    }
    public function update_fields_controls($widget)
    {
        $elementor = \ElementorPro\Plugin::elementor();
        $control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');
        if (is_wp_error($control_data)) {
            return;
        }
        $field_controls = ['field_onchange' => ['name' => 'field_onchange', 'label' => esc_html__('Submit on Change', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'separator' => 'before', 'condition' => ['field_type' => ['radio', 'select']], 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_enchanted_tab', 'tab' => 'enchanted']];
        $control_data['fields'] = \array_merge($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);
    }
}
