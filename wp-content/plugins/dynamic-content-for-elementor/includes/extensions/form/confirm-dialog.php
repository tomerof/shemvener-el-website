<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Tokens;
if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
class ConfirmDialog extends \DynamicContentForElementor\Extensions\ExtensionPrototype
{
    private static $actions_added = \false;
    public $has_action = \false;
    public $depended_scripts = ['dce-confirm-dialog'];
    public $depended_styles = ['dce-jquery-confirm'];
    /**
     * @return void
     */
    public function run_once()
    {
        $save_guard = \DynamicContentForElementor\Plugin::instance()->save_guard;
        $save_guard->register_unsafe_control('form', 'dce_confirm_dialog_title');
        $save_guard->register_unsafe_control('form', 'dce_confirm_dialog_content');
    }
    public function get_label()
    {
        return esc_html__('Confirm Dialog', 'dynamic-content-for-elementor');
    }
    /**
     * @return bool
     */
    public function is_common()
    {
        return \false;
    }
    public function add_assets_depends($instance, $form)
    {
        if ('yes' === $instance['dce_confirm_dialog_enabled']) {
            foreach ($this->depended_scripts as $script) {
                wp_enqueue_script($script);
            }
            foreach ($this->depended_styles as $style) {
                wp_enqueue_style($style);
            }
        }
    }
    protected function add_actions()
    {
        if (self::$actions_added) {
            return;
        }
        self::$actions_added = \true;
        add_action('elementor-pro/forms/pre_render', [$this, 'add_assets_depends'], 10, 2);
        add_action('elementor/element/form/section_buttons/after_section_end', [$this, 'add_controls_to_form']);
        add_filter('wpml_elementor_widgets_to_translate', [$this, 'wpml_widgets_to_translate_filter'], 50, 1);
    }
    public function add_controls_to_form($widget)
    {
        if (!\DynamicContentForElementor\Helper::can_register_unsafe_controls()) {
            return;
        }
        $widget->start_controls_section('section_dce_confirm_dialog', ['label' => '<span class="color-dce icon-dce-logo-dce pull-right ml-1"></span> ' . esc_html__('Confirm Dialog before Submit', 'dynamic-content-for-elementor')]);
        $widget->add_control('dce_confirm_dialog_enabled', ['label' => esc_html__('Confirm Dialog', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'no', 'frontend_available' => \true]);
        $widget->add_control('dce_confirm_dialog_title', ['label' => esc_html__('Title', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'ai' => ['active' => \false], 'default' => 'Confirm', 'label_block' => 'true', 'description' => esc_html__('You can use the same syntax as in Live HTML field', 'dynamic-content-for-elementor'), 'condition' => ['dce_confirm_dialog_enabled' => 'yes'], 'frontend_available' => \true]);
        $widget->add_control('dce_confirm_dialog_content', ['label' => esc_html__('Content', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::CODE, 'ai' => ['active' => \false], 'default' => 'Hi {{ form.name }}, please confirm submission', 'language' => 'html', 'label_block' => 'true', 'description' => esc_html__('HTML code to be displayed in the modal. You can use the same syntax as in Live HTML field', 'dynamic-content-for-elementor'), 'condition' => ['dce_confirm_dialog_enabled' => 'yes'], 'frontend_available' => \true]);
        $widget->add_responsive_control('dce_confirm_dialog_width', ['label' => esc_html__('Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '30', 'unit' => '%'], 'size_units' => ['%', 'px', 'vw'], 'range' => ['%' => ['min' => 20, 'max' => 100], 'px' => ['min' => 400, 'max' => 1200]], 'condition' => ['dce_confirm_dialog_enabled' => 'yes'], 'frontend_available' => \true]);
        $widget->add_control('dce_confirm_dialog_theme', ['label' => esc_html__('Theme', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SELECT, 'default' => 'light', 'options' => ['light' => esc_html__('Light', 'dynamic-content-for-elementor'), 'dark' => esc_html__('Dark', 'dynamic-content-for-elementor'), 'material' => 'Material', 'supervan' => 'Supervan', 'bootstrap' => 'Bootstrap'], 'separator' => 'after', 'label_block' => 'true', 'condition' => ['dce_confirm_dialog_enabled' => 'yes'], 'frontend_available' => \true]);
        $widget->add_control('dce_confirm_dialog_confirm_button', ['label' => esc_html__('Confirm Button', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['dce_confirm_dialog_enabled' => 'yes'], 'frontend_available' => \true]);
        $widget->add_control('dce_confirm_dialog_confirm_button_text', ['label' => esc_html__('Text', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'ai' => ['active' => \false], 'default' => esc_html__('Confirm', 'dynamic-content-for-elementor'), 'condition' => ['dce_confirm_dialog_enabled' => 'yes'], 'frontend_available' => \true]);
        $widget->add_control('dce_confirm_dialog_confirm_button_color', ['label' => esc_html__('Color', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SELECT, 'default' => 'default', 'options' => ['default' => esc_html__('Default', 'dynamic-content-for-elementor'), 'blue' => esc_html__('Blue', 'dynamic-content-for-elementor'), 'green' => esc_html__('Green', 'dynamic-content-for-elementor'), 'red' => esc_html__('Red', 'dynamic-content-for-elementor'), 'orange' => esc_html__('Orange', 'dynamic-content-for-elementor'), 'purple' => esc_html__('Purple', 'dynamic-content-for-elementor'), 'dark' => esc_html__('Dark', 'dynamic-content-for-elementor')], 'condition' => ['dce_confirm_dialog_enabled' => 'yes'], 'frontend_available' => \true]);
        $widget->add_control('dce_confirm_dialog_cancel_button', ['label' => esc_html__('Cancel Button', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['dce_confirm_dialog_enabled' => 'yes'], 'frontend_available' => \true]);
        $widget->add_control('dce_confirm_dialog_cancel_button_text', ['label' => esc_html__('Text', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'ai' => ['active' => \false], 'default' => esc_html__('Cancel', 'dynamic-content-for-elementor'), 'condition' => ['dce_confirm_dialog_enabled' => 'yes'], 'frontend_available' => \true]);
        $widget->add_control('dce_confirm_dialog_cancel_button_color', ['label' => esc_html__('Color', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SELECT, 'default' => 'default', 'options' => ['default' => esc_html__('Default', 'dynamic-content-for-elementor'), 'blue' => esc_html__('Blue', 'dynamic-content-for-elementor'), 'green' => esc_html__('Green', 'dynamic-content-for-elementor'), 'red' => esc_html__('Red', 'dynamic-content-for-elementor'), 'orange' => esc_html__('Orange', 'dynamic-content-for-elementor'), 'purple' => esc_html__('Purple', 'dynamic-content-for-elementor'), 'dark' => esc_html__('Dark', 'dynamic-content-for-elementor')], 'condition' => ['dce_confirm_dialog_enabled' => 'yes'], 'frontend_available' => \true]);
        $widget->end_controls_section();
    }
    /**
     * @param array<mixed> $widgets
     * @return array<mixed>
     */
    public function wpml_widgets_to_translate_filter($widgets)
    {
        if (!isset($widgets['form'])) {
            return $widgets;
        }
        $widgets['form']['fields'][] = ['field' => 'dce_confirm_dialog_title', 'type' => esc_html__('Confirm Dialog Title', 'dynamic-content-for-elementor'), 'editor_type' => 'LINE'];
        $widgets['form']['fields'][] = ['field' => 'dce_confirm_dialog_content', 'type' => esc_html__('Confirm Dialog Content', 'dynamic-content-for-elementor'), 'editor_type' => 'AREA'];
        $widgets['form']['fields'][] = ['field' => 'dce_confirm_dialog_confirm_button_text', 'type' => esc_html__('Confirm Button Text', 'dynamic-content-for-elementor'), 'editor_type' => 'LINE'];
        $widgets['form']['fields'][] = ['field' => 'dce_confirm_dialog_cancel_button_text', 'type' => esc_html__('Cancel Button Text', 'dynamic-content-for-elementor'), 'editor_type' => 'LINE'];
        return $widgets;
    }
}
