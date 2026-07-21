<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Tokens;
use DynamicContentForElementor\ExtensionInfo;
use DynamicContentForElementor\Plugin;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class MessageGenerator extends \ElementorPro\Modules\Forms\Classes\Action_Base
{
    use ExtensionInfo;
    public $has_action = \true;
    public $action_priority = 1000;
    public function __construct()
    {
        add_filter('wpml_elementor_widgets_to_translate', [$this, 'wpml_widgets_to_translate_filter'], 50, 1);
    }
    /**
     * Get Name
     *
     * Return the action name
     *
     * @access public
     * @return string
     */
    public function get_name()
    {
        return 'dce_form_message';
    }
    /**
     * Get Label
     *
     * Returns the action label
     *
     * @access public
     * @return string
     */
    public function get_label()
    {
        return esc_html__('Message Generator', 'dynamic-content-for-elementor');
    }
    public function run_once()
    {
        $save_guard = \DynamicContentForElementor\Plugin::instance()->save_guard;
        $save_guard->register_unsafe_control('form', 'dce_form_message_type');
        $save_guard->register_unsafe_control('form', 'dce_form_message_text');
        $save_guard->register_unsafe_control('form', 'dce_form_message_text_floating');
        $save_guard->register_unsafe_control('form', 'dce_form_message_text_floating_align');
        $save_guard->register_unsafe_control('form', 'dce_form_message_template');
        $save_guard->register_unsafe_control('form', 'dce_form_message_post');
        $save_guard->register_unsafe_control('form', 'dce_form_message_close');
        $save_guard->register_unsafe_control('form', 'dce_form_message_close_position');
        $save_guard->register_unsafe_control('form', 'dce_form_message_hide');
    }
    public function get_script_depends()
    {
        return [];
    }
    public function get_style_depends()
    {
        return [];
    }
    /**
     * Register Settings Section
     *
     * Registers the Action controls
     *
     * @access public
     * @param \Elementor\Widget_Base $widget
     */
    public function register_settings_section($widget)
    {
        $widget->start_controls_section('section_dce_form_message', ['label' => Helper::dce_logo() . $this->get_label(), 'condition' => ['submit_actions' => $this->get_name()]]);
        if (!\DynamicContentForElementor\Helper::can_register_unsafe_controls()) {
            $widget->add_control('admin_notice', ['name' => 'admin_notice', 'type' => Controls_Manager::RAW_HTML, 'raw' => esc_html__('You will need administrator capabilities to edit these settings.', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning']);
            $widget->end_controls_section();
            return;
        }
        Plugin::instance()->text_templates->maybe_add_notice($widget, 'message_generator');
        $widget->add_control('dce_form_message_type', ['label' => esc_html__('Message type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['text' => ['title' => esc_html__('Text', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'template' => ['title' => esc_html__('Template', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-th-large']], 'toggle' => \false, 'default' => 'text']);
        $widget->add_control('dce_form_message_text', ['label' => esc_html__('Message Text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::WYSIWYG, 'default' => esc_html__('Thanks for submitting this form', 'dynamic-content-for-elementor'), 'label_block' => \true, 'condition' => ['dce_form_message_type' => 'text']]);
        $widget->add_control('dce_form_message_text_floating', ['label' => esc_html__('Floating message', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'selectors' => ['{{WRAPPER}} .elementor-message' => 'position: fixed; display: block; z-index: 100; bottom: 0;'], 'condition' => ['dce_form_message_type' => 'text']]);
        $widget->add_control('dce_form_message_text_floating_align', ['label' => esc_html__('Floationg Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-left'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-right']], 'default' => 'right', 'selectors' => ['{{WRAPPER}} .elementor-message' => '{{VALUE}}: 0;'], 'condition' => ['dce_form_message_type' => 'text', 'dce_form_message_text_floating!' => '']]);
        $widget->add_control('dce_form_message_template', ['label' => esc_html__('Template', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Template Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'object_type' => 'elementor_library', 'description' => esc_html__('Use an Elementor Template as body for this Email.', 'dynamic-content-for-elementor'), 'condition' => ['dce_form_message_type' => 'template']]);
        $widget->add_control('dce_form_message_post', ['label' => esc_html__('Post', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Select a Post', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'description' => esc_html__('Force a Post as Template content for Dynamic fields. Leave empty for use current Page.', 'dynamic-content-for-elementor'), 'condition' => ['dce_form_message_type' => 'template']]);
        $widget->add_control('dce_form_message_close', ['label' => esc_html__('Add close button to message', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
        $widget->add_control('dce_form_message_close_position', ['label' => esc_html__('Close button Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'selectors' => ['{{WRAPPER}} .elementor-message-dce' => 'position: relative;', '{{WRAPPER}} .elementor-message-btn-dismiss' => 'position: absolute; top: 0; {{VALUE}}: 0; cursor: pointer;'], 'toggle' => \false, 'default' => 'right', 'condition' => ['dce_form_message_close!' => '']]);
        $widget->add_control('dce_form_message_hide', ['label' => esc_html__('Hide Form after submit', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
        $widget->add_control('dce_form_message_help', ['type' => \Elementor\Controls_Manager::RAW_HTML, 'raw' => '<div id="elementor-panel__editor__help" class="p-0"><a id="elementor-panel__editor__help__link" href="' . $this->get_docs() . '" target="_blank">' . esc_html__('Need Help', 'dynamic-content-for-elementor') . ' <i class="eicon-help-o"></i></a></div>', 'separator' => 'before']);
        $widget->end_controls_section();
    }
    public function add_style($widget, $args)
    {
        $widget->start_controls_section('dce_section_message_generator_style', ['label' => '<span class="color-dce icon-dce-logo-dce pull-right ml-1"></span> ' . esc_html__('Message Generator', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $widget->add_control('success_message_header', ['label' => esc_html__('Success Message', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $widget->add_group_control(Group_Control_Background::get_type(), ['name' => 'success_message_bgcolor', 'types' => ['classic', 'gradient'], 'selector' => '{{WRAPPER}} .elementor-message.elementor-message-success']);
        $widget->add_group_control(Group_Control_Border::get_type(), ['name' => 'success_message_border', 'selector' => '{{WRAPPER}} .elementor-message.elementor-message-success']);
        $widget->add_control('success_message_border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .elementor-message.elementor-message-success' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $widget->add_responsive_control('success_message_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .elementor-message.elementor-message-success' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $widget->add_responsive_control('success_message_margin', ['label' => esc_html__('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .elementor-message.elementor-message-success' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $widget->add_responsive_control('success_message_width', ['label' => esc_html__('Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['unit' => '%'], 'tablet_default' => ['unit' => '%'], 'mobile_default' => ['unit' => '%'], 'size_units' => ['%', 'px', 'vw'], 'range' => ['%' => ['min' => 1, 'max' => 100], 'px' => ['min' => 1, 'max' => 1000], 'vw' => ['min' => 1, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .elementor-message.elementor-message-success' => 'width: {{SIZE}}{{UNIT}};'], 'condition' => ['dce_form_message_text_floating!' => '']]);
        $widget->add_control('error_message_header', ['label' => esc_html__('Error Message', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $widget->add_group_control(Group_Control_Background::get_type(), ['name' => 'error_message_bgcolor', 'types' => ['classic', 'gradient'], 'selector' => '{{WRAPPER}} .elementor-message.elementor-message-danger']);
        $widget->add_group_control(Group_Control_Border::get_type(), ['name' => 'error_message_border', 'selector' => '{{WRAPPER}} .elementor-message.elementor-message-danger']);
        $widget->add_control('error_message_border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .elementor-message.elementor-message-danger' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $widget->add_responsive_control('error_message_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .elementor-message.elementor-message-danger' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $widget->add_responsive_control('error_message_margin', ['label' => esc_html__('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .elementor-message.elementor-message-danger' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $widget->add_responsive_control('error_message_width', ['label' => esc_html__('Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['unit' => '%'], 'tablet_default' => ['unit' => '%'], 'mobile_default' => ['unit' => '%'], 'size_units' => ['%', 'px', 'vw'], 'range' => ['%' => ['min' => 1, 'max' => 100], 'px' => ['min' => 1, 'max' => 1000], 'vw' => ['min' => 1, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .elementor-message.elementor-message-danger' => 'width: {{SIZE}}{{UNIT}};'], 'condition' => ['dce_form_message_text_floating!' => '']]);
        $widget->add_control('inline_message_header', ['label' => esc_html__('Inline Message', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $widget->add_group_control(Group_Control_Background::get_type(), ['name' => 'inline_message_bgcolor', 'types' => ['classic', 'gradient'], 'selector' => '{{WRAPPER}} .elementor-message.elementor-help-inline']);
        $widget->end_controls_section();
    }
    /**
     * Run
     *
     * Runs the action after submit
     *
     * @access public
     * @param \ElementorPro\Modules\Forms\Classes\Form_Record $record
     * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
     */
    public function run($record, $ajax_handler)
    {
        $settings = $record->get('form_settings');
        if (isset($settings['dce_form_message_text'])) {
            $settings['dce_form_message_text'] = Plugin::instance()->text_templates->expand_shortcodes_or_callback($settings['dce_form_message_text'], ['form-fields' => $record->get('fields')], function ($str) use($record) {
                $fields = Helper::get_form_data($record);
                return Helper::get_dynamic_value($str, $fields);
            });
        }
        $this->message($settings, $ajax_handler, $record);
    }
    /**
     * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
     * @param \ElementorPro\Modules\Forms\Classes\Form_Record $record
     */
    protected function message($settings, $ajax_handler, $record)
    {
        $element_id = $settings['id'];
        $message_html = '';
        if ($settings['dce_form_message_type'] == 'template') {
            if (!empty($settings['dce_form_message_template'])) {
                $atts = ['id' => $settings['dce_form_message_template'], 'inlinecss' => \true];
                if (!empty($settings['dce_form_message_post'])) {
                    $atts['post_id'] = $settings['dce_form_message_post'];
                }
                $template_system = \DynamicContentForElementor\Plugin::instance()->template_system;
                $message_html = Plugin::instance()->text_templates->dce_shortcodes->call_with_data(['form-fields' => $record->get('fields')], [$template_system, 'build_elementor_template_special'], [$atts]);
                $message_html = '</div><div class="elementor-message-dce" role="alert">' . $message_html;
                $message_html .= '<style>.elementor-element-' . $element_id . ' .elementor-form .elementor-message {display: none !important;}</style>';
            }
        } else {
            $message_html = $settings['dce_form_message_text'];
            $message_html .= '<style>.elementor-form .elementor-message{position: relative;}.elementor-form .elementor-message::before{float: left;}</style>';
        }
        if ($settings['dce_form_message_close']) {
            $message_html .= '<div class="elementor-message-btn-dismiss" onClick="jQuery(this).parent().fadeOut();"><i class="eicon-editor-close" aria-hidden="true"></i></div>';
        }
        if ($settings['dce_form_message_hide']) {
            $message_html .= '<style>.elementor-element-' . $element_id . ' .elementor-form-fields-wrapper,';
            $message_html .= '.elementor-element-' . $element_id . ' .dce-form-progressbar,';
            // Steps:
            $message_html .= '.elementor-element-' . $element_id . ' .e-form__indicators {display: none !important;}</style>';
        }
        if ($ajax_handler->is_success) {
            add_action('elementor_pro/forms/new_record', function ($record, $ajax_handler) use($message_html) {
                wp_send_json_success(['message' => $message_html, 'data' => $ajax_handler->data]);
            }, 100000, 2);
        } else {
            $ajax_handler->add_error_message($message_html);
        }
    }
    public function on_export($element)
    {
        return $element;
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
        $widgets['form']['fields'][] = ['field' => 'dce_form_message_text', 'type' => esc_html__('Message Generator Text', 'dynamic-content-for-elementor'), 'editor_type' => 'AREA'];
        return $widgets;
    }
}
