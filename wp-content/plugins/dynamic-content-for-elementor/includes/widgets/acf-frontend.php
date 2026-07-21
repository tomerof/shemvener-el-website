<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly.
}
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use DynamicContentForElementor\Helper;
class AcfFrontend extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    /**
     * Run Once
     *
     * This method is called once per widget type, not per instance.
     * It's used to register hooks that should only be registered once.
     *
     * @return void
     */
    public function run_once()
    {
        parent::run_once();
        // Enqueue styles for editor preview
        add_action('elementor/editor/before_enqueue_scripts', [__CLASS__, 'enqueue_editor_styles']);
        add_action('elementor/preview/enqueue_styles', [__CLASS__, 'enqueue_editor_styles']);
    }
    /**
     * Enqueue Editor Styles
     *
     * Enqueues ACF scripts and styles in the Elementor editor
     * to ensure proper preview of form styles (e.g., label placement).
     *
     * @return void
     */
    public static function enqueue_editor_styles()
    {
        // Load ACF scripts and styles for editor preview
        if (\function_exists('acf_enqueue_scripts')) {
            \acf_enqueue_scripts();
        }
    }
    /**
     * @return array<string>
     */
    public function get_script_depends()
    {
        return ['dce-google-maps-api'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_acf_frontend', ['label' => $this->get_title()]);
        $this->add_control('retrieve_from', ['label' => esc_html__('Retrieve the fields from', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'current_post', 'options' => ['current_post' => esc_html__('Current Post', 'dynamic-content-for-elementor'), 'other_post' => esc_html__('Other Post', 'dynamic-content-for-elementor'), 'new_post' => esc_html__('New Post', 'dynamic-content-for-elementor')]]);
        $this->add_control('retrieve_from_id', ['label' => esc_html__('Select Post', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Post Title', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'condition' => ['retrieve_from' => 'other_post']]);
        $this->add_control('new_post_type', ['label' => esc_html__('Post Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_public_post_types(), 'label_block' => \true, 'default' => 'page', 'condition' => ['retrieve_from' => ['new_post']]]);
        $this->add_control('new_post_status', ['label' => esc_html__('Post Status', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => \array_merge(['future' => esc_html__('Future', 'dynamic-content-for-elementor')], get_post_statuses()), 'label_block' => \true, 'default' => 'publish', 'condition' => ['retrieve_from' => ['new_post']]]);
        $this->end_controls_section();
        $this->start_controls_section('section_fields', ['label' => esc_html__('Fields to Show', 'dynamic-content-for-elementor')]);
        $this->add_control('all_fields', ['label' => esc_html__('Show All ACF Fields', 'dynamic-content-for-elementor'), 'default' => 'yes', 'frontend_available' => \true, 'type' => Controls_Manager::SWITCHER, 'condition' => ['retrieve_from!' => 'new_post']]);
        $this->add_control('type', ['label' => esc_html__('Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'fields', 'options' => ['fields' => esc_html__('ACF Fields', 'dynamic-content-for-elementor'), 'field_groups' => esc_html__('ACF Field Groups', 'dynamic-content-for-elementor')], 'conditions' => ['relation' => 'or', 'terms' => [['name' => 'all_fields', 'operator' => '===', 'value' => ''], ['name' => 'retrieve_from', 'operator' => '===', 'value' => 'new_post']]]]);
        $fields = new \Elementor\Repeater();
        $fields->add_control('field', ['label' => esc_html__('ACF Field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acf', 'dynamic' => ['active' => \false]]);
        $this->add_control('fields_list', ['label' => esc_html__('ACF Fields', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::REPEATER, 'fields' => $fields->get_controls(), 'title_field' => '{{{ field }}}', 'conditions' => ['relation' => 'or', 'terms' => [['relation' => 'and', 'terms' => [['name' => 'all_fields', 'operator' => '===', 'value' => ''], ['name' => 'type', 'operator' => '===', 'value' => 'fields']]], ['relation' => 'and', 'terms' => [['name' => 'retrieve_from', 'operator' => '===', 'value' => 'new_post'], ['name' => 'type', 'operator' => '===', 'value' => 'fields']]]]]]);
        $group = new \Elementor\Repeater();
        $group->add_control('group', ['label' => esc_html__('ACF Field Group', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Select the field group...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acf_groups', 'dynamic' => ['active' => \false]]);
        $this->add_control('groups_list', ['label' => esc_html__('ACF Field Groups', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::REPEATER, 'fields' => $group->get_controls(), 'title_field' => '{{{ group }}}', 'conditions' => ['relation' => 'or', 'terms' => [['relation' => 'and', 'terms' => [['name' => 'all_fields', 'operator' => '===', 'value' => ''], ['name' => 'type', 'operator' => '===', 'value' => 'field_groups']]], ['relation' => 'and', 'terms' => [['name' => 'retrieve_from', 'operator' => '===', 'value' => 'new_post'], ['name' => 'type', 'operator' => '===', 'value' => 'field_groups']]]]]]);
        $this->add_control('post_title', ['label' => esc_html__('Post Title', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_on' => esc_html__('Show', 'dynamic-content-for-elementor'), 'label_off' => esc_html__('Hide', 'dynamic-content-for-elementor'), 'default' => 'yes']);
        $this->add_control('post_content', ['label' => esc_html__('Post Content', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_on' => esc_html__('Show', 'dynamic-content-for-elementor'), 'label_off' => esc_html__('Hide', 'dynamic-content-for-elementor'), 'default' => '']);
        $this->end_controls_section();
        $this->start_controls_section('submit', ['label' => esc_html__('Submit', 'dynamic-content-for-elementor')]);
        $this->add_control('submit_value', ['label' => esc_html__('Submit Value', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Update', 'dynamic-content-for-elementor')]);
        $this->add_control('after_submit', ['label' => esc_html__('After Submit', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'message', 'options' => ['message' => esc_html__('Message', 'dynamic-content-for-elementor'), 'redirect' => esc_html__('Redirect', 'dynamic-content-for-elementor')]]);
        $this->add_control('updated_message', ['label' => esc_html__('Updated Message', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => Controls_Manager::TEXT, 'condition' => ['after_submit' => 'message']]);
        $this->add_control('url_target', ['label' => esc_html__('Target', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'same', 'options' => ['same' => esc_html__('Same Page', 'dynamic-content-for-elementor'), 'another' => esc_html__('Another Page', 'dynamic-content-for-elementor')], 'condition' => ['after_submit' => 'redirect']]);
        $this->add_control('redirect', ['label' => esc_html__('Redirect To', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::URL, 'placeholder' => esc_html__('https://your-link.com', 'dynamic-content-for-elementor'), 'default' => ['url' => ''], 'condition' => ['after_submit' => 'redirect', 'url_target' => 'another']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style', ['label' => $this->get_title(), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_control('label_placement', ['label' => esc_html__('Label Placement', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'left', 'options' => ['left' => esc_html__('Left', 'dynamic-content-for-elementor'), 'top' => esc_html__('Top', 'dynamic-content-for-elementor')]]);
        $this->add_control('instruction_placement', ['label' => esc_html__('Instruction Placement', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'label', 'options' => ['label' => esc_html__('Label', 'dynamic-content-for-elementor'), 'field' => esc_html__('Field', 'dynamic-content-for-elementor')]]);
        $this->add_control('uploader', ['label' => esc_html__('Uploader', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'wp', 'options' => ['wp' => 'WP', 'basic' => 'Basic']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style_labels', ['label' => esc_html__('Labels', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'labels_typography', 'selector' => '{{WRAPPER}} form.acf-form div.acf-label label']);
        $this->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'labels_text_shadow', 'selector' => '{{WRAPPER}} form.acf-form div.acf-label label']);
        $this->add_control('labels_text_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} form.acf-form div.acf-label label' => 'fill: {{VALUE}}; color: {{VALUE}};']]);
        $this->add_control('labels_background_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} form.acf-form div.acf-label label' => 'background-color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'labels_border_only', 'selector' => '{{WRAPPER}} form.acf-form div.acf-label label', 'separator' => 'before']);
        $this->add_control('labels_border_color', ['label' => esc_html__('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['labels_border_only!' => ''], 'selectors' => ['{{WRAPPER}} form.acf-form div.acf-label label' => 'border-color: {{VALUE}};']]);
        $this->add_control('labels_border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} form.acf-form div.acf-label label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'labels_box_shadow', 'selector' => '{{WRAPPER}} form.acf-form div.acf-label']);
        $this->end_controls_section();
        $this->start_controls_section('section_style_inner_labels', ['label' => esc_html__('Inner Labels', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'inner_labels_typography', 'selector' => '{{WRAPPER}} form.acf-form div.acf-input label']);
        $this->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'inner_labels_text_shadow', 'selector' => '{{WRAPPER}} form.acf-form div.acf-input label']);
        $this->add_control('inner_labels_text_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} form.acf-form div.acf-input label' => 'fill: {{VALUE}}; color: {{VALUE}};']]);
        $this->add_control('inner_labels_background_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} form.acf-form div.acf-input label' => 'background-color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'inner_labels_border_only', 'selector' => '{{WRAPPER}} form.acf-form div.acf-input label', 'separator' => 'before']);
        $this->add_control('inner_labels_border_color', ['label' => esc_html__('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['inner_labels_border_only!' => ''], 'selectors' => ['{{WRAPPER}} form.acf-form div.acf-input label' => 'border-color: {{VALUE}};']]);
        $this->add_control('inner_labels_border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} form.acf-form div.acf-input label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'inner_labels_box_shadow', 'selector' => '{{WRAPPER}} form.acf-form div.acf-input']);
        $this->end_controls_section();
        $this->start_controls_section('section_style_buttons', ['label' => esc_html__('Buttons', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'buttons_typography', 'selector' => '{{WRAPPER}} form.acf-form button']);
        $this->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'buttons_text_shadow', 'selector' => '{{WRAPPER}} form.acf-form button']);
        $this->add_control('buttons_text_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} form.acf-form button' => 'fill: {{VALUE}}; color: {{VALUE}};']]);
        $this->add_control('buttons_background_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} form.acf-form button' => 'background-color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'buttons_border_only', 'selector' => '{{WRAPPER}} form.acf-form button', 'separator' => 'before']);
        $this->add_control('buttons_border_color', ['label' => esc_html__('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['buttons_border_only!' => ''], 'selectors' => ['{{WRAPPER}} form.acf-form button' => 'border-color: {{VALUE}};']]);
        $this->add_control('buttons_border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} form.acf-form button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'buttons_box_shadow', 'selector' => '{{WRAPPER}} form.acf-form button']);
        $this->end_controls_section();
        $this->start_controls_section('section_style_text_info', ['label' => esc_html__('Text info', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'text_info_typography', 'selector' => '{{WRAPPER}} form.acf-form div.acf-input p']);
        $this->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'text_info_text_shadow', 'selector' => '{{WRAPPER}} form.acf-form div.acf-input p']);
        $this->add_control('text_info_text_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} form.acf-form div.acf-input p' => 'fill: {{VALUE}}; color: {{VALUE}};']]);
        $this->add_control('text_info_background_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} form.acf-form div.acf-input p' => 'background-color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'text_info_border_only', 'selector' => '{{WRAPPER}} form.acf-form div.acf-input p', 'separator' => 'before']);
        $this->add_control('text_info_border_color', ['label' => esc_html__('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['text_info_border_only!' => ''], 'selectors' => ['{{WRAPPER}} form.acf-form div.acf-input p' => 'border-color: {{VALUE}};']]);
        $this->add_control('text_info_border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} form.acf-form div.acf-input p' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'text_info_box_shadow', 'selector' => '{{WRAPPER}} form.acf-form div.acf-input p']);
        $this->end_controls_section();
        $this->start_controls_section('section_style_update_button', ['label' => esc_html__('Update Button', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('update_button_align', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'selectors' => ['{{WRAPPER}} form.acf-form div.acf-form-submit' => 'text-align: {{VALUE}};'], 'default' => is_rtl() ? 'right' : 'left']);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'update_button_typography', 'selector' => '{{WRAPPER}} form.acf-form div.acf-form-submit input[type="submit"]']);
        $this->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'update_button_text_shadow', 'selector' => '{{WRAPPER}} form.acf-form div.acf-form-submit input[type="submit"]']);
        $this->start_controls_tabs('update_button_tabs');
        $this->start_controls_tab('update_button_tab_normal', ['label' => esc_html__('Normal', 'dynamic-content-for-elementor')]);
        $this->add_control('update_button_text_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} form.acf-form div.acf-form-submit input[type="submit"]' => 'fill: {{VALUE}}; color: {{VALUE}};']]);
        $this->add_control('update_button_background_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} form.acf-form div.acf-form-submit input[type="submit"]' => 'background-color: {{VALUE}};']]);
        $this->add_control('update_button_border_color', ['label' => esc_html__('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} form.acf-form div.acf-form-submit input[type="submit"]' => 'border-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->start_controls_tab('update_button_tab_hover', ['label' => esc_html__('Hover', 'dynamic-content-for-elementor')]);
        $this->add_control('update_button_text_color_hover', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} form.acf-form div.acf-form-submit input[type="submit"]:hover' => 'fill: {{VALUE}}; color: {{VALUE}};']]);
        $this->add_control('update_button_background_color_hover', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} form.acf-form div.acf-form-submit input[type="submit"]:hover' => 'background-color: {{VALUE}};']]);
        $this->add_control('update_button_border_color_hover', ['label' => esc_html__('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} form.acf-form div.acf-form-submit input[type="submit"]:hover' => 'border-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'update_button_border_only', 'selector' => '{{WRAPPER}} form.acf-form div.acf-form-submit input[type="submit"]', 'separator' => 'before']);
        $this->add_control('update_button_border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} form.acf-form div.acf-form-submit input[type="submit"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'update_button_box_shadow', 'selector' => '{{WRAPPER}} form.acf-form div.acf-form-submit input[type="submit"]']);
        $this->add_responsive_control('update_button_margin', ['label' => esc_html__('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'default' => ['top' => '20', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'isLinked' => \false], 'selectors' => ['{{WRAPPER}} form.acf-form div.acf-form-submit' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->end_controls_section();
        // Messages Style Section
        $this->start_controls_section('section_style_messages', ['label' => esc_html__('Messages', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        // Success Message Heading
        $this->add_control('success_message_heading', ['label' => esc_html__('Success Message', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING]);
        $this->add_control('success_message_background_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '#d4edda', 'selectors' => ['{{WRAPPER}} .dce-acf-frontend-message--success' => 'background-color: {{VALUE}};']]);
        $this->add_control('success_message_text_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '#155724', 'selectors' => ['{{WRAPPER}} .dce-acf-frontend-message--success' => 'color: {{VALUE}};']]);
        $this->add_control('success_message_border_color', ['label' => esc_html__('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '#c3e6cb', 'selectors' => ['{{WRAPPER}} .dce-acf-frontend-message--success' => 'border-color: {{VALUE}};']]);
        // Error Message Heading
        $this->add_control('error_message_heading', ['label' => esc_html__('Error Message', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('error_message_background_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '#f8d7da', 'selectors' => ['{{WRAPPER}} .acf-error-message, {{WRAPPER}} .acf-notice.-error' => 'background-color: {{VALUE}};']]);
        $this->add_control('error_message_text_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '#721c24', 'selectors' => ['{{WRAPPER}} .acf-error-message, {{WRAPPER}} .acf-notice.-error' => 'color: {{VALUE}};']]);
        $this->add_control('error_message_border_color', ['label' => esc_html__('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '#f5c6cb', 'selectors' => ['{{WRAPPER}} .acf-error-message, {{WRAPPER}} .acf-notice.-error' => 'border-color: {{VALUE}};']]);
        // Common Message Styles
        $this->add_control('message_common_heading', ['label' => esc_html__('Common Styles', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'message_typography', 'selector' => '{{WRAPPER}} .dce-acf-frontend-message, {{WRAPPER}} .acf-error-message, {{WRAPPER}} .acf-notice']);
        $this->add_responsive_control('message_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'default' => ['top' => '12', 'right' => '20', 'bottom' => '12', 'left' => '20', 'unit' => 'px', 'isLinked' => \false], 'selectors' => ['{{WRAPPER}} .dce-acf-frontend-message, {{WRAPPER}} .acf-error-message, {{WRAPPER}} .acf-notice' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('message_margin', ['label' => esc_html__('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'default' => ['top' => '0', 'right' => '0', 'bottom' => '20', 'left' => '0', 'unit' => 'px', 'isLinked' => \false], 'selectors' => ['{{WRAPPER}} .dce-acf-frontend-message, {{WRAPPER}} .acf-error-message, {{WRAPPER}} .acf-notice' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('message_border_width', ['label' => esc_html__('Border Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px'], 'range' => ['px' => ['min' => 0, 'max' => 10]], 'default' => ['unit' => 'px', 'size' => 1], 'selectors' => ['{{WRAPPER}} .dce-acf-frontend-message, {{WRAPPER}} .acf-error-message, {{WRAPPER}} .acf-notice' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;']]);
        $this->add_control('message_border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'default' => ['top' => '4', 'right' => '4', 'bottom' => '4', 'left' => '4', 'unit' => 'px', 'isLinked' => \true], 'selectors' => ['{{WRAPPER}} .dce-acf-frontend-message, {{WRAPPER}} .acf-error-message, {{WRAPPER}} .acf-notice' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->end_controls_section();
    }
    /**
     * Safe Render
     *
     * @return void
     */
    protected function safe_render()
    {
        // Enqueue ACF scripts and styles
        if (\function_exists('acf_enqueue_scripts')) {
            \acf_enqueue_scripts();
        }
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $args = [
            'id' => $this->get_id(),
            // Widget ID
            'post_title' => !empty($settings['post_title']),
            'submit_value' => $settings['submit_value'],
            'label_placement' => $settings['label_placement'],
            'instruction_placement' => $settings['instruction_placement'],
            'uploader' => $settings['uploader'],
            'kses' => \true,
            'post_content' => \false,
        ];
        // Only set message args if message mode is enabled AND message is not empty.
        if ('message' === $settings['after_submit'] && !empty($settings['updated_message'])) {
            $args['updated_message'] = $settings['updated_message'];
            $args['html_updated_message'] = '<div class="dce-acf-frontend-message dce-acf-frontend-message--success">%s</div>';
        } else {
            $args['updated_message'] = \false;
        }
        // Only set 'return' for redirect mode. For message mode, let ACF use its
        // default behavior (redirect to current URL with ?updated=true).
        $return_value = $this->get_return_value($settings);
        if (\false !== $return_value) {
            $args['return'] = $return_value;
        }
        if ($settings['all_fields'] && 'new_post' !== $settings['retrieve_from']) {
            $args['fields'] = [];
            // An empty array means all fields
        } else {
            switch ($settings['type']) {
                case 'fields':
                    $args['fields'] = $this->get_fields_to_show($settings);
                    break;
                case 'field_groups':
                    $args['field_groups'] = $this->get_field_groups_to_show($settings);
                    break;
            }
        }
        switch ($settings['retrieve_from']) {
            case 'current_post':
                $post_id = get_the_ID();
                $args['post_id'] = $post_id;
                $document = \Elementor\Plugin::$instance->documents->get($post_id);
                if (!$document || !$document->is_built_with_elementor()) {
                    $args['post_content'] = !empty($settings['post_content']);
                }
                break;
            case 'other_post':
                $post_id = absint($settings['retrieve_from_id']);
                if (empty($post_id) || !get_post($post_id)) {
                    Helper::notice(\false, esc_html__('Please select the post', 'dynamic-content-for-elementor'), 'danger');
                    return;
                }
                $args['post_id'] = $post_id;
                $args['post_content'] = !empty($settings['post_content']);
                break;
            case 'new_post':
                $args['post_id'] = 'new_post';
                $args += ['new_post' => ['post_type' => $settings['new_post_type'], 'post_status' => $settings['new_post_status']]];
                $args['post_content'] = !empty($settings['post_content']);
                break;
        }
        if (!$this->has_fields_to_show($args)) {
            Helper::notice(\false, esc_html__('You have no fields to show', 'dynamic-content-for-elementor'), 'danger');
            return;
        }
        acf_form($args);
    }
    /**
     * Get Return Value
     *
     * @param array<mixed> $settings
     * @return string|false
     */
    protected function get_return_value($settings)
    {
        if ('redirect' === $settings['after_submit']) {
            switch ($settings['url_target']) {
                case 'same':
                    return get_the_permalink();
                case 'another':
                    $url = $settings['redirect']['url'] ?? '';
                    if (\filter_var($url, \FILTER_VALIDATE_URL)) {
                        return esc_url($url);
                    }
                    // Invalid URL: fall back to current page instead of message mode behavior.
                    return get_the_permalink();
            }
        }
        // For message mode, return false so ACF uses its default behavior
        // (redirect to current URL with ?updated=true appended).
        return \false;
    }
    /**
     * Get Fields to Show
     *
     * @param array<mixed> $settings
     * @return array<int,mixed>|array<null>|false
     */
    protected function get_field_groups_to_show($settings)
    {
        if (empty($settings['groups_list'])) {
            return [null];
        }
        // Get selected groups
        $selected_groups = [];
        foreach ($settings['groups_list'] as $value) {
            if (!empty($value['group'])) {
                $selected_groups[] = $value['group'];
            }
        }
        if (empty($selected_groups)) {
            return [null];
        }
        // Prepare visibility check parameters
        $args = [];
        switch ($settings['retrieve_from']) {
            case 'current_post':
                $post_id = get_the_ID();
                if ($post_id) {
                    $args['post_id'] = $post_id;
                }
                break;
            case 'other_post':
                $post_id = absint($settings['retrieve_from_id']);
                if ($post_id) {
                    $args['post_id'] = $post_id;
                }
                break;
            case 'new_post':
                $args['post_type'] = $settings['new_post_type'];
                break;
        }
        // Get visible groups
        $visible_groups = [];
        foreach ($selected_groups as $group_key) {
            $group = acf_get_field_group($group_key);
            if ($group && acf_get_field_group_visibility($group, $args)) {
                $visible_groups[] = $group_key;
            }
        }
        return !empty($visible_groups) ? $visible_groups : [null];
    }
    /**
     * Get Fields to Show
     *
     * @param array<mixed> $settings
     * @return array<int,mixed>|null
     */
    protected function get_fields_to_show($settings)
    {
        if (empty($settings['fields_list'])) {
            return [null];
        }
        $fields = [];
        foreach ($settings['fields_list'] as $value) {
            if (!empty($value['field'])) {
                $fields[] = $value['field'];
            }
        }
        return !empty($fields) ? $fields : [null];
    }
    /**
     * Checks if there are fields or field groups to show
     *
     * @param array<string,mixed> $args Arguments for displaying the form.
     * @return boolean True if there are fields or field groups to show, false otherwise.
     */
    protected function has_fields_to_show($args)
    {
        if ($args['post_title'] || $args['post_content']) {
            return \true;
        }
        if (isset($args['fields']) && [] === $args['fields']) {
            return \true;
        }
        if (isset($args['fields']) && !empty($args['fields']) && $args['fields'][0] !== null) {
            return \true;
        }
        if (isset($args['field_groups']) && \is_array($args['field_groups']) && !empty($args['field_groups'])) {
            return \true;
        }
        return \false;
    }
}
