<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Tokens;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use ElementorPro\Modules\Forms\Fields;
use Elementor\Widget_Base;
use ElementorPro\Modules\Forms\Classes;
use ElementorPro\Modules\Forms\Widgets\Form;
use ElementorPro\Plugin;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class RegexField extends \DynamicContentForElementor\Extensions\ExtensionPrototype
{
    public $has_action = \false;
    /**
     * @var string[]
     */
    private $allowed_types = ['text', 'email', 'url', 'password'];
    /**
     * @return string
     */
    public function get_name()
    {
        return 'dce_form_regex';
    }
    /**
     * @return bool
     */
    public function is_common()
    {
        return \false;
    }
    /**
     * @return string
     */
    public function get_label()
    {
        return esc_html__('Regex Field for Elementor Pro Form', 'dynamic-content-for-elementor');
    }
    /**
     * @return void
     */
    protected function add_actions()
    {
        add_action('elementor/widget/before_render_content', array($this, 'apply_regex_attributes'));
        add_action('elementor/element/form/section_form_fields/before_section_end', [$this, 'update_fields_controls']);
    }
    /**
     * @return void
     */
    /**
     * @param \Elementor\Widget_Base $widget
     * @return void
     */
    public function apply_regex_attributes($widget)
    {
        if ($widget->get_name() !== 'form') {
            return;
        }
        $settings = $widget->get_settings_for_display();
        if (empty($settings['form_fields']) || !\is_array($settings['form_fields'])) {
            return;
        }
        foreach ($settings['form_fields'] as $key => $afield) {
            if (empty($afield['field_regex'])) {
                continue;
            }
            if (!\in_array($afield['field_type'], $this->allowed_types, \true)) {
                continue;
            }
            $field_key = 'input' . $key;
            $widget->add_render_attribute($field_key, 'pattern', $afield['field_regex']);
            $widget->add_render_attribute($field_key, 'data-regex', 'true');
        }
    }
    /**
     * @return void
     */
    public function update_fields_controls($widget)
    {
        $elementor = \ElementorPro\Plugin::elementor();
        $control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');
        if (is_wp_error($control_data)) {
            return;
        }
        $field_controls = ['field_regex' => ['name' => 'field_regex', 'label' => esc_html__('Regex', 'dynamic-content-for-elementor'), 'description' => esc_html__('A regular expression is a sequence of characters that define a pattern. Use it to restrict the characters permitted on this field.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'ai' => ['active' => \false], 'separator' => 'before', 'return_value' => 'true', 'conditions' => ['terms' => [['name' => 'field_type', 'operator' => 'in', 'value' => $this->allowed_types]]], 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_enchanted_tab', 'tab' => 'enchanted']];
        $control_data['fields'] = \array_merge($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);
    }
}
