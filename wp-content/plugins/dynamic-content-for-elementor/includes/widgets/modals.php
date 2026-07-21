<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Utils;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Modals extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    /**
     * Unique ID
     *
     * @var string
     */
    protected $unique_id;
    public function get_script_depends()
    {
        return ['dce-jquery-visible', 'dce-cookie', 'dce-modals'];
    }
    public function get_style_depends()
    {
        return ['animatecss', 'dce-animations', 'dce-modal'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_popup_content', ['label' => esc_html__('Content', 'dynamic-content-for-elementor')]);
        $this->add_control('show_popup_editor', ['label' => esc_html__('Show Modal Preview in Edit Mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_control('content_hr', ['type' => \Elementor\Controls_Manager::DIVIDER, 'style' => 'thick']);
        $this->add_control('content_type', ['label' => esc_html__('Content type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'options' => ['content' => ['title' => esc_html__('Content', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'template' => ['title' => esc_html__('Template', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-th-large']], 'default' => 'content']);
        $this->add_control('template', ['label' => esc_html__('Template', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Template Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'object_type' => 'elementor_library', 'description' => esc_html__('Use an Elementor Template as the content of the modal', 'dynamic-content-for-elementor'), 'condition' => ['content_type' => 'template']]);
        $this->add_control('modal_content', ['label' => esc_html__('Modal Content', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::WYSIWYG, 'placeholder' => esc_html__('Type here the content', 'dynamic-content-for-elementor'), 'description' => esc_html__('The main content of the modal', 'dynamic-content-for-elementor'), 'dynamic' => ['active' => \true], 'condition' => ['content_type' => 'content']]);
        $this->end_controls_section();
        $this->start_controls_section('section_popup_settings', ['label' => esc_html__('Settings', 'dynamic-content-for-elementor')]);
        $this->add_control('trigger', ['label' => esc_html__('Trigger', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'onload', 'frontend_available' => \true, 'options' => ['onload' => esc_html__('On Page Load', 'dynamic-content-for-elementor'), 'button' => esc_html__('On Click Button', 'dynamic-content-for-elementor'), 'scroll' => esc_html__('On Scroll Page', 'dynamic-content-for-elementor'), 'widget' => esc_html__('On Widget position', 'dynamic-content-for-elementor')]]);
        $this->add_control('trigger_other', ['label' => esc_html__('Catch other elements', 'dynamic-content-for-elementor'), 'description' => esc_html__('Other elements on the page can show this modal', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['trigger' => 'button']]);
        $this->add_control('trigger_other_selectors', ['label' => esc_html__('Selectors', 'dynamic-content-for-elementor'), 'description' => esc_html__('Type here the CSS selector of the other elements in jQuery format. For example "#name, .button"', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'frontend_available' => \true, 'condition' => ['trigger' => 'button', 'trigger_other!' => '']]);
        $this->add_control('trigger_other_limit_to_loop', ['label' => esc_html__('Limit to Loop Item', 'dynamic-content-for-elementor'), 'description' => esc_html__('Limits the search of other elements to the current loop item', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['trigger' => 'button', 'trigger_other!' => '']]);
        $this->add_control('hr_button', ['type' => Controls_Manager::DIVIDER, 'style' => 'thick', 'condition' => ['trigger' => 'button']]);
        $this->add_control('title_button', ['label' => esc_html__('Button', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'condition' => ['trigger' => 'button']]);
        $this->add_control('button_type', ['label' => esc_html__('Button type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'default' => 'text', 'options' => ['text' => ['title' => esc_html__('Text', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-italic'], 'image' => ['title' => esc_html__('Image', 'dynamic-content-for-elementor'), 'icon' => 'eicon-image'], 'hamburger' => ['title' => esc_html__('Hamburger', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-bars']], 'condition' => ['trigger' => 'button']]);
        $this->add_control('hamburger_style', ['label' => esc_html__('Hamburger Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'x', 'options' => ['x' => 'X', 'arrow_left' => esc_html__('Arrow Left', 'dynamic-content-for-elementor'), 'arrow_right' => esc_html__('Arrow Right', 'dynamic-content-for-elementor'), 'fall' => esc_html__('Fall', 'dynamic-content-for-elementor')], 'condition' => ['trigger' => 'button', 'button_type' => 'hamburger']]);
        $this->add_control('button_text', ['label' => esc_html__('Text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Get Started', 'dynamic-content-for-elementor'), 'placeholder' => esc_html__('Get Started', 'dynamic-content-for-elementor'), 'label_block' => \true, 'dynamic' => ['active' => \true], 'condition' => ['trigger' => 'button', 'button_type' => 'text']]);
        $this->add_control('selected_button_icon', ['label' => esc_html__('Icon', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ICONS, 'fa4compatibility' => 'button_icon', 'label_block' => \true, 'condition' => ['trigger' => 'button', 'button_type' => 'text']]);
        $this->add_control('button_image', ['label' => esc_html__('Button Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::MEDIA, 'dynamic' => ['active' => \true], 'default' => ['url' => ''], 'condition' => ['trigger' => 'button', 'button_type' => 'image'], 'description' => esc_html__('Use an image instead of the button', 'dynamic-content-for-elementor')]);
        $this->add_control('button_purpose', ['label' => esc_html__('Button Purpose', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'options' => ['open' => ['title' => esc_html__('Default', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-expand'], 'close' => ['title' => esc_html__('Use the button in the template to close', 'dynamic-content-for-elementor'), 'icon' => 'eicon-close']], 'default' => 'open', 'description' => esc_html__('Decide if this is a simple Call-To-Action button to insert it in another modal to generate no modals for this widget. If you need to use another element to close the modal, add class .dce-button-close-modal to them.', 'dynamic-content-for-elementor'), 'condition' => ['trigger' => 'button'], 'separator' => 'before']);
        $this->add_control('scroll_display_displacement', ['label' => esc_html__('Scroll displacement', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 0, 'description' => esc_html__('Pixel to wait until make modal appear', 'dynamic-content-for-elementor'), 'frontend_available' => \true, 'condition' => ['trigger' => 'scroll']]);
        $this->add_control('title_options', ['label' => esc_html__('Options', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['button_purpose!' => 'close']]);
        $this->add_control('scroll_hide', ['label' => esc_html__('Hide on scroll', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('Hide the modal when the user scrolls the page', 'dynamic-content-for-elementor'), 'frontend_available' => \true, 'condition' => ['button_purpose!' => 'close']]);
        $this->add_control('close_delay', ['label' => esc_html__('Close Delay (ms)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 0, 'frontend_available' => \true, 'condition' => ['scroll_hide!' => ['', '0'], 'button_purpose!' => 'close']]);
        $this->add_control('esc_hide', ['label' => esc_html__('Hide on press ESC', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'description' => esc_html__('Hide the modal when the user press ESC button', 'dynamic-content-for-elementor'), 'condition' => ['button_purpose!' => 'close']]);
        $this->add_control('always_visible', ['label' => esc_html__('Don’t use cookies', 'dynamic-content-for-elementor'), 'description' => esc_html__('Yes will show the modal on every page load.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'separator' => 'before', 'frontend_available' => \true, 'condition' => ['button_purpose!' => 'close', 'trigger!' => 'button']]);
        $this->add_control('cookie_name', ['label' => esc_html__('Cookie name', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => 'modals_accepted', 'frontend_available' => \true, 'description' => esc_html__('Should be unique, or it will affect all modals on your site. Only letters, digits and underscores accepted.', 'dynamic-content-for-elementor'), 'condition' => ['always_visible' => '', 'button_purpose!' => 'close', 'trigger!' => 'button']]);
        $this->add_control('cookie_set', ['label' => esc_html__('Set Cookie on Close', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true, 'description' => esc_html__('If Yes the cookie will be set when closing the modal. If No, the cookie will be checked but never set. Useful for those who set the cookie manually.', 'dynamic-content-for-elementor'), 'condition' => ['always_visible' => '', 'button_purpose!' => 'close', 'trigger!' => 'button']]);
        $this->add_control('cookie_lifetime', ['label' => esc_html__('Cookie expiration', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 0, 'frontend_available' => \true, 'description' => esc_html__('Time in days. 0 means that is active only while the browser remains open.', 'dynamic-content-for-elementor'), 'condition' => ['always_visible' => '', 'button_purpose!' => 'close', 'trigger!' => 'button']]);
        $this->end_controls_section();
        $this->start_controls_section('section_popup_animations', ['label' => esc_html__('Animations', 'dynamic-content-for-elementor')]);
        $this->add_control('enabled_push', ['label' => esc_html__('Push', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'separator' => 'before', 'description' => esc_html__('Move body wrapper', 'dynamic-content-for-elementor'), 'condition' => ['button_purpose!' => 'close']]);
        $this->add_control('wrapper_maincontent', ['label' => esc_html__('Wrapper of Content', 'dynamic-content-for-elementor'), 'description' => esc_html__('The ID of the main content on your site', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'frontend_available' => \true, 'default' => '#wrap', 'placeholder' => '#wrap', 'condition' => ['button_purpose!' => 'close', 'enabled_push!' => '']]);
        $this->add_control('close_animation_push', ['label' => esc_html__('Close body push', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'groups' => Helper::get_anim_close(), 'default' => 'exitToScaleBack', 'frontend_available' => \true, 'separator' => 'after', 'render_type' => 'template', 'condition' => ['button_purpose!' => 'close', 'enabled_push!' => ''], 'selectors' => ['body.modal-open-dce-popup-{{ID}} .dce-push' => 'animation-name: {{VALUE}}; -webkit-animation-name: {{VALUE}};']]);
        $this->add_control('open_animation_push', ['label' => esc_html__('Open body push', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'groups' => Helper::get_anim_open(), 'default' => 'enterFormScaleBack', 'frontend_available' => \true, 'render_type' => 'template', 'condition' => ['button_purpose!' => 'close', 'enabled_push!' => ''], 'selectors' => ['body.modal-close-dce-popup-{{ID}} .dce-push' => 'animation-name: {{VALUE}}; -webkit-animation-name: {{VALUE}};']]);
        $this->add_control('title_open_modal', ['label' => esc_html__('Open Animation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['button_purpose!' => 'close']]);
        $this->add_control('open_animation', ['label' => esc_html__('Style', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'groups' => Helper::get_anim_open(), 'default' => 'enterFromTop', 'frontend_available' => \true, 'render_type' => 'template', 'condition' => ['button_purpose!' => 'close'], 'selectors' => ['body.modal-open-dce-popup-{{ID}} .dce-modal.dce-popup-{{ID}} .modal-dialog' => 'animation-name: {{VALUE}}Popup; -webkit-animation-name: {{VALUE}}Popup;']]);
        $this->add_control('open_timingFunction', ['label' => esc_html__('Timing function', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'groups' => Helper::get_anim_timing_functions(), 'default' => 'ease-in-out', 'frontend_available' => \true, 'condition' => ['button_purpose!' => 'close'], 'selectors' => ['body.modal-open-dce-popup-{{ID}} .dce-push, body.modal-open-dce-popup-{{ID}} .dce-modal.dce-popup-{{ID}} .modal-dialog' => 'animation-timing-function: {{VALUE}}; -webkit-animation-timing-function: {{VALUE}};']]);
        $this->add_control('open_speed', ['label' => esc_html__('Speed', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 0, 'max' => 5, 'step' => 0.1]], 'default' => ['size' => 0.6], 'selectors' => ['body.modal-open-dce-popup-{{ID}} .dce-push, body.modal-open-dce-popup-{{ID}} .dce-modal.dce-popup-{{ID}} .modal-dialog.animated' => '-webkit-animation-duration: {{SIZE}}s; animation-duration: {{SIZE}}s'], 'condition' => ['button_purpose!' => 'close']]);
        $this->add_control('open_delay', ['label' => esc_html__('Delay (ms)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 0, 'max' => 5000, 'step' => 100]], 'frontend_available' => \true, 'default' => ['size' => 0], 'condition' => ['button_purpose!' => 'close']]);
        $this->add_control('title_close_modal', ['label' => esc_html__('Close Animation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['button_purpose!' => 'close']]);
        $this->add_control('close_animation', ['label' => esc_html__('Style', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'groups' => Helper::get_anim_close(), 'default' => 'exitToBottom', 'frontend_available' => \true, 'render_type' => 'template', 'condition' => ['button_purpose!' => 'close'], 'selectors' => ['.dce-modal.dce-popup-{{ID}} .modal-dialog' => 'animation-name: {{VALUE}}Popup; -webkit-animation-name: {{VALUE}}Popup;']]);
        $this->add_control('close_timingFunction', ['label' => esc_html__('Timing function', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'groups' => Helper::get_anim_timing_functions(), 'default' => 'ease-in-out', 'frontend_available' => \true, 'condition' => ['button_purpose!' => 'close'], 'selectors' => ['body.modal-close-dce-popup-{{ID}} .dce-push, .dce-modal.dce-popup-{{ID}} .modal-dialog' => 'animation-timing-function: {{VALUE}}; -webkit-animation-timing-function: {{VALUE}};']]);
        $this->add_control('close_speed', ['label' => esc_html__('Speed', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 0, 'max' => 5, 'step' => 0.1]], 'default' => ['size' => 0.6], 'selectors' => ['body.modal-close-dce-popup-{{ID}} .dce-push, .dce-modal.dce-popup-{{ID}} .modal-dialog.animated' => '-webkit-animation-duration: {{SIZE}}s; animation-duration: {{SIZE}}s'], 'condition' => ['button_purpose!' => 'close']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style_bglayer', ['label' => esc_html__('Overlay', 'dynamic-content-for-elementor'), 'condition' => ['button_purpose!' => 'close']]);
        $this->add_control('background_layer', ['label' => esc_html__('Overlay', 'dynamic-content-for-elementor'), 'description' => esc_html__('Show a page overlay when the modal is visible', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'background_overlay', 'label' => esc_html__('Background Overlay Color', 'dynamic-content-for-elementor'), 'types' => ['classic', 'gradient'], 'selector' => '{{WRAPPER}} .dce-modal-background-layer:before, dce-popup-container.dce-popup-container-{{ID}} .dce-modal-background-layer:before', 'condition' => ['background_layer' => 'yes']]);
        $this->add_responsive_control('overlay_opacity', ['label' => esc_html__('Opacity', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 0, 'max' => 1, 'step' => 0.01]], 'default' => ['size' => '', 'unit' => 'px'], 'selectors' => ['dce-popup-container.dce-popup-container-{{ID}} .dce-modal-background-layer:before, {{WRAPPER}} .dce-modal-background-layer:before' => 'opacity: {{SIZE}};'], 'condition' => ['background_layer' => 'yes']]);
        $this->add_control('background_layer_close', ['label' => esc_html__('Close modal clicking on the background layer', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['background_layer!' => '']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style_modal', ['label' => esc_html__('Modal', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['button_purpose!' => 'close']]);
        $this->add_control('modal_align', ['label' => esc_html__('Horizontal Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-right']], 'default' => 'center']);
        $this->add_control('modal_valign', ['label' => esc_html__('Vertical Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['bottom' => ['title' => esc_html__('Bottom', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-bottom'], 'middle' => ['title' => esc_html__('Middle', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-middle'], 'top' => ['title' => esc_html__('Top', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-top']], 'default' => 'middle']);
        $this->add_responsive_control('modal_width', ['label' => esc_html__('Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'range' => ['px' => ['min' => 0, 'max' => 1920, 'step' => 1], '%' => ['min' => 5, 'max' => 100, 'step' => 1]], 'size_units' => ['%', 'px', 'vw'], 'selectors' => ['.dce-modal.dce-popup-{{ID}}' => 'width: {{SIZE}}{{UNIT}};'], 'separator' => 'before', 'condition' => ['extend_full_window' => '']]);
        $this->add_responsive_control('modal_height', ['label' => esc_html__('Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['min' => 0, 'max' => 1920, 'step' => 1], '%' => ['min' => 5, 'max' => 100, 'step' => 1], 'vh' => ['min' => 5, 'max' => 100, 'step' => 1]], 'size_units' => ['%', 'px', 'vh'], 'selectors' => ['.dce-modal.dce-popup-{{ID}} .modal-content' => 'height: {{SIZE}}{{UNIT}};'], 'condition' => ['extend_full_window' => '']]);
        $this->add_control('extend_full_window', ['label' => esc_html__('Extend Full Window', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'separator' => 'after']);
        $this->add_control('modal_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['.dce-modal.dce-popup-{{ID}} .modal-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('modal_margin', ['label' => esc_html__('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['.dce-modal.dce-popup-{{ID}} .modal-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'modal_text_typography', 'label' => esc_html__('Typography', 'dynamic-content-for-elementor'), 'selector' => '.dce-modal.dce-popup-{{ID}} .modal-content', 'condition' => ['content_type' => 'content']]);
        $this->add_control('modal_text_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['.dce-modal.dce-popup-{{ID}} .modal-content' => 'color: {{VALUE}};'], 'condition' => ['content_type' => 'content']]);
        $this->add_control('modal_background_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['.dce-modal.dce-popup-{{ID}} .modal-content' => 'background-color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'modal_border', 'label' => esc_html__('Border', 'dynamic-content-for-elementor'), 'placeholder' => '1px', 'selector' => '.dce-modal.dce-popup-{{ID}} .modal-content']);
        $this->add_control('modal_border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['.dce-modal.dce-popup-{{ID}} .modal-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['label' => esc_html__('Box Shadow', 'dynamic-content-for-elementor'), 'name' => 'modal_box_shadow', 'selector' => '.dce-modal.dce-popup-{{ID}} .modal-content', 'separator' => 'before']);
        $this->end_controls_section();
        $this->start_controls_section('section_style_button', ['label' => esc_html__('Button', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['trigger' => 'button']]);
        $this->add_control('button_icon_align', ['label' => esc_html__('Icon Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'left', 'options' => ['left' => esc_html__('Before', 'dynamic-content-for-elementor'), 'right' => esc_html__('After', 'dynamic-content-for-elementor')], 'condition' => ['selected_button_icon[value]!' => '', 'button_image[id]' => '']]);
        $this->add_responsive_control('button_icon_size', ['label' => esc_html__('Icon Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px', 'em', 'rem', 'vw', 'custom'], 'range' => ['px' => ['min' => 6, 'max' => 300]], 'default' => ['size' => '', 'unit' => 'px'], 'selectors' => ['{{WRAPPER}} .elementor-button-icon' => 'font-size: {{SIZE}}{{UNIT}};'], 'condition' => ['enable_close_button!' => '', 'selected_button_icon[value]!' => '', 'button_image[id]' => '']]);
        $this->add_responsive_control('button_icon_indent', ['label' => esc_html__('Icon Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 50]], 'condition' => ['selected_button_icon[value]!' => '', 'button_image[id]' => ''], 'selectors' => ['{{WRAPPER}} .dce-button-popup .elementor-align-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-button-popup .elementor-align-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};']]);
        $this->add_control('button_image_size', ['label' => esc_html__('Image Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['%' => ['min' => 1, 'max' => 100], 'px' => ['min' => 6, 'max' => 300], 'em' => ['min' => 0, 'max' => 20]], 'size_units' => ['px', '%', 'em'], 'default' => ['size' => '', 'unit' => 'px'], 'selectors' => ['{{WRAPPER}} .dce-button-img' => 'max-width: {{SIZE}}{{UNIT}};'], 'condition' => ['button_image[id]!' => '', 'button_type' => 'image']]);
        $this->add_control('title_button_colors', ['label' => esc_html__('Colors', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'condition' => ['button_type!' => 'image']]);
        $this->start_controls_tabs('buttontext_colors');
        $this->start_controls_tab('buttontext_colors_normal', ['label' => esc_html__('Normal', 'dynamic-content-for-elementor'), 'condition' => ['button_type!' => 'image']]);
        $this->add_control('button_text_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-button-popup' => 'fill: {{VALUE}}; color: {{VALUE}};'], 'condition' => ['button_type' => 'text']]);
        $this->add_control('bars_color', ['label' => esc_html__('Bars Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-button-hamburger .bar' => 'background-color: {{VALUE}};'], 'condition' => ['button_type' => 'hamburger']]);
        $this->add_control('button_background_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-button-popup' => 'background-color: {{VALUE}};'], 'condition' => ['button_type!' => ['image', 'x']]]);
        $this->end_controls_tab();
        $this->start_controls_tab('buttontext_colors_hover', ['label' => esc_html__('Hover', 'dynamic-content-for-elementor'), 'condition' => ['button_type!' => 'image']]);
        $this->add_control('title_button_hover', ['label' => esc_html__('Hover', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING]);
        $this->add_control('button_hover_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-button-popup:hover' => 'color: {{VALUE}};'], 'condition' => ['button_type' => 'text']]);
        $this->add_control('bars_hover_color', ['label' => esc_html__('Bars Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-button-hamburger .con:hover .bar, {{WRAPPER}} .special-con:hover .bar' => 'background-color: {{VALUE}};'], 'condition' => ['button_type' => 'hamburger']]);
        $this->add_control('button_background_hover_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-button-popup:hover' => 'background-color: {{VALUE}};']]);
        $this->add_control('button_hover_border_color', ['label' => esc_html__('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['button_border_border!' => ''], 'selectors' => ['{{WRAPPER}} .dce-button-popup:hover' => 'border-color: {{VALUE}};']]);
        $this->add_control('button_hover_animation', ['label' => esc_html__('Animation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HOVER_ANIMATION]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_responsive_control('button_align', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right'], 'justify' => ['title' => esc_html__('Justified', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-justify']], 'separator' => 'before', 'prefix_class' => 'elementor-align-', 'selectors' => ['{{WRAPPER}} .dce-button-wrapper' => 'text-align: {{VALUE}};'], 'default' => '']);
        $this->add_responsive_control('button_justify_align', ['label' => esc_html__('Justify Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'separator' => 'after', 'selectors' => ['{{WRAPPER}}.elementor-align-justify .dce-button-popup' => 'text-align: {{VALUE}};'], 'condition' => ['button_align' => 'justify']]);
        $this->add_responsive_control('hamburger_size', ['label' => esc_html__('Hamburger Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'separator' => 'before', 'default' => ['size' => 50, 'unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 1, 'max' => 100, 'step' => 1]], 'condition' => ['button_type' => 'hamburger'], 'selectors' => ['{{WRAPPER}} .dce-button-hamburger .bar' => 'width: {{SIZE}}{{UNIT}}', '{{WRAPPER}} .dce-button-hamburger .con:hover .arrow-top, {{WRAPPER}} .dce-button-hamburger .con:hover .arrow-bottom, {{WRAPPER}} .dce-button-hamburger .con:hover .arrow-top-r, {{WRAPPER}} .dce-button-hamburger .con:hover .arrow-bottom-r' => 'width: calc({{SIZE}}{{UNIT}} / 2)']]);
        $this->add_control('hamburger_weight', ['label' => esc_html__('Hamburger weight', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 5, 'unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 1, 'max' => 20, 'step' => 1]], 'condition' => ['button_type' => 'hamburger'], 'selectors' => ['{{WRAPPER}} .dce-button-hamburger .bar' => 'height: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-button-hamburger .con:hover .top' => 'top: 0; transform: translate(0px, calc(50% + {{SIZE}}{{UNIT}})) rotate(45deg);', '{{WRAPPER}} .dce-button-hamburger .con:hover .middle' => 'top: 0; width: 0;', '{{WRAPPER}} .dce-button-hamburger .con:hover .bottom' => 'top: 0; transform: translate(0px, calc(50% - {{SIZE}}{{UNIT}})) rotate(-45deg);', '{{WRAPPER}} .dce-button-hamburger .con:hover .arrow-top' => 'top: calc({{SIZE}}{{UNIT}} / 4); transform: translate(0%,{{SIZE}}{{UNIT}}) rotateZ(45deg);', '{{WRAPPER}} .dce-button-hamburger .con:hover .arrow-middle' => 'top: 0; transform: translate(-50%, 0)', '{{WRAPPER}} .dce-button-hamburger .con:hover .arrow-bottom' => 'top: calc(-{{SIZE}}{{UNIT}} / 4); transform: translate(0%,-{{SIZE}}{{UNIT}}) rotateZ(-45deg);', '{{WRAPPER}} .dce-button-hamburger .con:hover .arrow-top-r' => 'top: calc({{SIZE}}{{UNIT}} / 4); transform: translate(100%,{{SIZE}}{{UNIT}}) rotateZ(-45deg);', '{{WRAPPER}} .dce-button-hamburger .con:hover .arrow-middle-r' => 'top: 0; transform: translate(50%, 0)', '{{WRAPPER}} .dce-button-hamburger .con:hover .arrow-bottom-r' => 'top: calc(-{{SIZE}}{{UNIT}} / 4); transform: translate(100%,-{{SIZE}}{{UNIT}}) rotateZ(45deg);', '{{WRAPPER}} .dce-button-hamburger .special-con:hover .arrow-top-fall' => 'top: 0;', '{{WRAPPER}} .dce-button-hamburger .special-con:hover .arrow-middle-fall' => 'top: 0;', '{{WRAPPER}} .dce-button-hamburger .special-con:hover .arrow-bottom-fall' => 'top: 0']]);
        $this->add_control('hamburger_space', ['label' => esc_html__('Hamburger space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 10, 'unit' => 'px'], 'size_units' => ['px'], 'range' => ['%' => ['min' => 1, 'max' => 50, 'step' => 1]], 'condition' => ['button_type' => 'hamburger'], 'selectors' => ['{{WRAPPER}} .dce-button-hamburger .con .top, {{WRAPPER}} .dce-button-hamburger .con .arrow-top, {{WRAPPER}} .dce-button-hamburger .con .arrow-top-r, {{WRAPPER}} .dce-button-hamburger .special-con .arrow-top-fall' => 'top: -{{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-button-hamburger .con .bottom, {{WRAPPER}} .dce-button-hamburger .con .arrow-bottom, {{WRAPPER}} .dce-button-hamburger .con .arrow-bottom-r, {{WRAPPER}} .dce-button-hamburger .special-con .arrow-bottom-fall' => 'top: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-button-hamburger .con' => 'top: calc({{SIZE}}{{UNIT}} * 2);']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'button_typography', 'label' => esc_html__('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-button-popup', 'separator' => 'after', 'condition' => ['button_type' => 'text']]);
        $this->add_control('button_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .dce-button-popup' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['button_type!' => 'image']]);
        $this->add_control('title_button_border', ['label' => esc_html__('Border', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'button_border', 'label' => esc_html__('Border', 'dynamic-content-for-elementor'), 'placeholder' => '1px', 'default' => '1px', 'selector' => '{{WRAPPER}} .dce-button-popup']);
        $this->add_control('button_border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-button-popup' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'button_box_shadow', 'selector' => '{{WRAPPER}} .dce-button-popup', 'condition' => ['button_type!' => 'image']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style_close', ['label' => esc_html__('Close Button', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['button_purpose!' => 'close']]);
        $this->add_control('enable_close_button', ['label' => esc_html__('Close Button', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_control('close_type', ['label' => esc_html__('Close type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['x' => ['title' => esc_html__('X', 'dynamic-content-for-elementor'), 'icon' => 'eicon-close'], 'icon' => ['title' => esc_html__('Icon', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-asterisk'], 'image' => ['title' => esc_html__('Image', 'dynamic-content-for-elementor'), 'icon' => 'eicon-image'], 'text' => ['title' => esc_html__('Text', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-italic']], 'toggle' => \false, 'default' => 'x', 'condition' => ['enable_close_button!' => '']]);
        $this->add_control('selected_close_icon', ['label' => esc_html__('Close Icon', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ICONS, 'fa4compatibility' => 'close_icon', 'label_block' => \true, 'default' => ['library' => 'fa-solid', 'value' => 'fas fa-times'], 'condition' => ['close_type' => 'icon', 'enable_close_button!' => '']]);
        $this->add_control('close_image', ['label' => esc_html__('Close Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::MEDIA, 'default' => ['url' => ''], 'condition' => ['close_type' => 'image', 'enable_close_button!' => '']]);
        $this->add_control('close_text', ['label' => esc_html__('Close Text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Close', 'dynamic-content-for-elementor'), 'condition' => ['close_type' => 'text', 'enable_close_button!' => '']]);
        $this->start_controls_tabs('close_colors');
        $this->start_controls_tab('close_colors_normal', ['label' => esc_html__('Normal', 'dynamic-content-for-elementor'), 'condition' => ['close_type!' => 'image', 'enable_close_button!' => '']]);
        $this->add_control('close_icon_color', ['label' => esc_html__('Icon color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['.dce-modal.dce-popup-{{ID}} button.dce-close' => 'color: {{VALUE}};'], 'condition' => ['close_type' => 'icon', 'enable_close_button!' => '']]);
        $this->add_control('close_text_color', ['label' => esc_html__('Text color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['.dce-modal.dce-popup-{{ID}} button.dce-close' => 'color: {{VALUE}};'], 'condition' => ['close_type' => 'text', 'close_text!' => '', 'enable_close_button!' => '']]);
        $this->add_control('x_close_text_color', ['label' => esc_html__('X color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-modal-close .dce-quit-ics:after, {{WRAPPER}} .dce-modal-close .dce-quit-ics:before, .dce-modal.dce-popup-{{ID}} .dce-modal-close .dce-quit-ics:after, .dce-modal.dce-popup-{{ID}} .dce-modal-close .dce-quit-ics:before' => 'background-color: {{VALUE}};'], 'condition' => ['close_type' => 'x', 'enable_close_button!' => '']]);
        $this->add_control('close_bg_color', ['label' => esc_html__('Background color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['.dce-modal.dce-popup-{{ID}} button.dce-close' => 'background-color: {{VALUE}};'], 'condition' => ['enable_close_button!' => '', 'close_type!' => ['image', 'x']]]);
        $this->add_control('x_close_bg_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'condition' => ['close_type' => 'x', 'enable_close_button!' => ''], 'selectors' => ['{{WRAPPER}} .dce-modal-close .dce-quit-ics, .dce-modal.dce-popup-{{ID}} .dce-modal-close .dce-quit-ics' => 'background-color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'close_bg_border', 'label' => esc_html__('Border', 'dynamic-content-for-elementor'), 'selector' => '.dce-modal.dce-popup-{{ID}} button.dce-close', 'condition' => ['enable_close_button!' => '']]);
        $this->end_controls_tab();
        $this->start_controls_tab('close_colors_hover', ['label' => esc_html__('Hover', 'dynamic-content-for-elementor'), 'condition' => ['close_type!' => 'image', 'enable_close_button!' => '']]);
        $this->add_control('close_icon_color_hover', ['label' => esc_html__('Icon color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['.dce-modal.dce-popup-{{ID}} button.dce-close:hover' => 'color: {{VALUE}};'], 'condition' => ['close_type' => 'icon', 'enable_close_button!' => '']]);
        $this->add_control('close_text_color_hover', ['label' => esc_html__('Text color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['.dce-modal.dce-popup-{{ID}} button.dce-close:hover' => 'color: {{VALUE}};'], 'condition' => ['close_type' => 'text', 'close_text!' => '', 'enable_close_button!' => '']]);
        $this->add_control('x_close_text_color_hover', ['label' => esc_html__('X color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-modal-close:hover .dce-quit-ics:after, {{WRAPPER}} .dce-modal-close:hover .dce-quit-ics:before, .dce-modal.dce-popup-{{ID}} .dce-modal-close:hover .dce-quit-ics:after, .dce-modal.dce-popup-{{ID}} .dce-modal-close:hover .dce-quit-ics:before' => 'background-color: {{VALUE}};'], 'condition' => ['close_type' => 'x', 'enable_close_button!' => '']]);
        $this->add_control('close_background_color_hover', ['label' => esc_html__('Background color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} button.dce-close:hover, .dce-modal.dce-popup-{{ID}} button.dce-close:hover' => 'background-color: {{VALUE}};'], 'condition' => ['close_type!' => ['image', 'x'], 'enable_close_button!' => '']]);
        $this->add_control('x_close_background_color_hover', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-modal-close .dce-quit-ics:hover, .dce-modal.dce-popup-{{ID}} .dce-modal-close .dce-quit-ics:hover' => 'background-color: {{VALUE}};'], 'condition' => ['close_type' => 'x', 'enable_close_button!' => '']]);
        $this->add_control('close_bg_color_hover', ['label' => esc_html__('Background color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['.dce-modal.dce-popup-{{ID}} button.dce-close:hover' => 'border-color: {{VALUE}};'], 'condition' => ['enable_close_button!' => '', 'close_bg_border_border!' => '']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_responsive_control('x_buttonsize_closemodal', ['label' => esc_html__('Button Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'separator' => 'before', 'default' => ['size' => 50, 'unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 20, 'max' => 100, 'step' => 1]], 'condition' => ['close_type' => 'x', 'enable_close_button!' => ''], 'selectors' => ['{{WRAPPER}} .dce-modal-close .dce-quit-ics, .dce-modal.dce-popup-{{ID}} .dce-modal-close .dce-quit-ics' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};']]);
        $this->add_control('x_weight_closemodal', ['label' => esc_html__('Close Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 1, 'unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 1, 'max' => 20, 'step' => 1]], 'condition' => ['close_type' => 'x', 'enable_close_button!' => ''], 'selectors' => ['{{WRAPPER}} .dce-modal-close .dce-quit-ics:after, {{WRAPPER}} .dce-modal-close .dce-quit-ics:before, .dce-modal.dce-popup-{{ID}} .dce-modal-close .dce-quit-ics:after, .dce-modal.dce-popup-{{ID}} .dce-modal-close .dce-quit-ics:before' => 'height: {{SIZE}}{{UNIT}}; top: calc(50% - ({{SIZE}}{{UNIT}}/2));']]);
        $this->add_control('x_size_closemodal', ['label' => esc_html__('Close Size (%)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 60, 'unit' => '%'], 'size_units' => ['%'], 'range' => ['%' => ['min' => 20, 'max' => 200, 'step' => 1]], 'condition' => ['close_type' => 'x', 'enable_close_button!' => ''], 'selectors' => ['{{WRAPPER}} .dce-modal-close .dce-quit-ics:after, {{WRAPPER}} .dce-modal-close .dce-quit-ics:before, .dce-modal.dce-popup-{{ID}} .dce-modal-close .dce-quit-ics:after, .dce-modal.dce-popup-{{ID}} .dce-modal-close .dce-quit-ics:before' => 'width: {{SIZE}}{{UNIT}}; left: calc(50% - ({{SIZE}}{{UNIT}}/2));']]);
        $this->add_responsive_control('x_vertical_close', ['label' => esc_html__('Y Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0, 'unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => -100, 'max' => 100, 'step' => 1]], 'condition' => ['close_type' => 'x', 'enable_close_button!' => ''], 'selectors' => ['{{WRAPPER}} .dce-modal-close .dce-quit-ics, .dce-modal.dce-popup-{{ID}} .dce-modal-close .dce-quit-ics' => 'top: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('x_horizontal_close', ['label' => esc_html__('X Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0, 'unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => -100, 'max' => 100, 'step' => 1]], 'condition' => ['close_type' => 'x', 'enable_close_button!' => ''], 'selectors' => ['{{WRAPPER}} .dce-modal-close .dce-quit-ics, .dce-modal.dce-popup-{{ID}} .dce-modal-close .dce-quit-ics' => 'right: {{SIZE}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'close_typography', 'label' => esc_html__('Close Typography', 'dynamic-content-for-elementor'), 'selector' => '.dce-modal.dce-popup-{{ID}} button.dce-close:not(i)', 'condition' => ['close_type' => 'text', 'close_text!' => '', 'enable_close_button!' => '']]);
        $this->add_control('close_align', ['label' => esc_html__('Horizontal position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'separator' => 'before', 'toggle' => \false, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-right']], 'default' => 'right', 'condition' => ['enable_close_button!' => '']]);
        $this->add_control('close_valign', ['label' => esc_html__('Vertical position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'options' => ['bottom' => ['title' => esc_html__('Bottom', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-bottom'], 'top' => ['title' => esc_html__('Top', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-top']], 'default' => 'top', 'condition' => ['enable_close_button!' => '']]);
        $this->add_responsive_control('close_size', ['label' => esc_html__('Icon Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px', 'em', 'rem', 'vw', 'custom'], 'range' => ['px' => ['min' => 6, 'max' => 300], 'em' => ['min' => 0, 'max' => 10], 'rem' => ['min' => 0, 'max' => 10]], 'default' => ['size' => 20, 'unit' => 'px'], 'selectors' => ['.dce-modal.dce-popup-{{ID}} button.dce-close' => 'font-size: {{SIZE}}{{UNIT}};', '.dce-modal.dce-popup-{{ID}} button.dce-close .close-img' => 'width: {{SIZE}}{{UNIT}}; height: auto;'], 'condition' => ['close_type' => ['icon', 'image'], 'enable_close_button!' => '']]);
        $this->add_control('close_bg_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['.dce-modal.dce-popup-{{ID}} button.dce-close' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['close_type!' => 'x', 'enable_close_button!' => '']]);
        $this->add_control('close_margin', ['label' => esc_html__('Close Margin', 'dynamic-content-for-elementor'), 'description' => esc_html__('Helpful insert close button external from modal by insert negative values', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['.dce-modal.dce-popup-{{ID}} button.dce-close' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'separator' => 'before', 'condition' => ['close_type!' => 'x', 'enable_close_button!' => '']]);
        $this->add_control('close_padding', ['label' => esc_html__('Close Padding', 'dynamic-content-for-elementor'), 'description' => esc_html__('Please note that padding bottom has no effect - Left/Right padding will depend on button position!', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['.dce-modal.dce-popup-{{ID}} button.dce-close' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'separator' => 'before', 'condition' => ['close_type!' => 'x', 'enable_close_button!' => '']]);
        $this->end_controls_section();
    }
    /**
     * Get Unique ID
     *
     * @return string
     */
    protected function get_unique_id()
    {
        return $this->unique_id;
    }
    /**
     * Set Unique ID
     *
     * @return void
     */
    protected function set_unique_id()
    {
        $uuid = wp_generate_uuid4();
        // modals.js expects the id without `-`s
        $this->unique_id = \str_replace('-', '', $uuid);
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $this->set_unique_id();
        if ($settings['trigger'] == 'button' && ($settings['button_type'] == 'text' || $settings['button_type'] == 'image')) {
            $has_button_icon = isset($settings['selected_button_icon']) || isset($settings['button_icon']);
            $button_icon = Helper::get_migrated_icon($settings, 'button_icon', '');
            ?>
			<div class="dce-button-wrapper">
				<?php 
            if ($settings['button_type'] == 'image') {
                if (!empty($settings['button_image']['url'])) {
                    ?>
						<img class="button-img dce-button-popup dce-button-img dce-button-<?php 
                    echo esc_attr($settings['button_purpose']);
                    ?>-modal dce-animation-<?php 
                    echo esc_attr($settings['button_hover_animation']);
                    ?>"
							aria-hidden="true"
							<?php 
                    if (\in_array($settings['button_purpose'], array('open', 'next'))) {
                        ?>data-target="dce-popup-<?php 
                        echo esc_attr($this->get_id());
                        ?>-<?php 
                        echo esc_attr($this->get_unique_id());
                        ?>"<?php 
                    }
                    ?>
							src="<?php 
                    echo esc_url($settings['button_image']['url']);
                    ?>" />
						<?php 
                }
            } else {
                ?>
					<button class="dce-button-<?php 
                echo esc_attr($settings['button_purpose']);
                ?>-modal dce-button-popup dce-animation-<?php 
                echo esc_attr($settings['button_hover_animation']);
                ?>"
							<?php 
                if (\in_array($settings['button_purpose'], array('open', 'next'))) {
                    ?>data-target="dce-popup-<?php 
                    echo esc_attr($this->get_id());
                    ?>-<?php 
                    echo esc_attr($this->get_unique_id());
                    ?>"<?php 
                }
                ?>
							>
					<?php 
                if ($has_button_icon) {
                    ?>
							<span class="elementor-button-icon elementor-align-icon-<?php 
                    echo esc_attr($settings['button_icon_align']);
                    ?>">
								<?php 
                    echo $button_icon;
                    ?>
							</span>
						<?php 
                }
                ?>
						<span class="dce-button-text"><?php 
                echo esc_html($settings['button_text']);
                ?></span>
					</button>
			<?php 
            }
            ?>
			</div>
			<?php 
        }
        if ($settings['trigger'] == 'button' && $settings['button_type'] == 'hamburger') {
            ?>
			<div class="dce-button-wrapper">
				<div class="dce-button-<?php 
            echo esc_attr($settings['button_purpose']);
            ?>-modal dce-button-hamburger dce-button-popup dce-animation-<?php 
            echo esc_attr($settings['button_hover_animation']);
            ?>"
						<?php 
            if (\in_array($settings['button_purpose'], array('open', 'next'))) {
                ?>data-target="dce-popup-<?php 
                echo esc_attr($this->get_id());
                ?>-<?php 
                echo esc_attr($this->get_unique_id());
                ?>"<?php 
            }
            ?>
					>
			<?php 
            if ($settings['hamburger_style'] == 'x') {
                ?>
						<div class="con">
							<div class="bar top"></div>
							<div class="bar middle"></div>
							<div class="bar bottom"></div>
						</div>
					<?php 
            } elseif ($settings['hamburger_style'] == 'arrow_left') {
                ?>
						<div class="con">
							<div class="bar arrow-top-r"></div>
							<div class="bar arrow-middle-r"></div>
							<div class="bar arrow-bottom-r"></div>
						</div>
			<?php 
            } elseif ($settings['hamburger_style'] == 'arrow_right') {
                ?>
						<div class="con">
							<div class="bar arrow-top"></div>
							<div class="bar arrow-middle"></div>
							<div class="bar arrow-bottom"></div>
						</div>
			<?php 
            } elseif ($settings['hamburger_style'] == 'fall') {
                ?>
						<div class="special-con">
							<div class="bar arrow-top-fall"></div>
							<div class="bar arrow-middle-fall"></div>
							<div class="bar arrow-bottom-fall"></div>
						</div>
			<?php 
            }
            ?>
				</div>
			</div>
			<?php 
        }
        $this->generate_modals();
    }
    public function generate_modals()
    {
        $settings = $this->get_settings_for_display();
        if (\Elementor\Plugin::$instance->editor->is_edit_mode() && $settings['show_popup_editor'] && $settings['button_purpose'] != 'close' || !\Elementor\Plugin::$instance->editor->is_edit_mode() && !$this->check_cookie() || !\Elementor\Plugin::$instance->editor->is_edit_mode() && $settings['trigger'] == 'button') {
            $infinite = '';
            if ($settings['extend_full_window']) {
                $fullWindow = ' dce-poup-full-window';
            } else {
                $fullWindow = '';
            }
            ?>
			<div class="dce-popup-container dce-popup-container-<?php 
            echo esc_attr($this->get_id());
            ?> dce-popup-<?php 
            echo esc_attr($settings['trigger'] . $fullWindow);
            ?>">

				<?php 
            if ($settings['background_layer']) {
                ?>
					<div id="dce-popup-<?php 
                echo esc_attr($this->get_id());
                ?>-<?php 
                echo esc_attr($this->get_unique_id());
                ?>-background" class="animated dce-modal-background-layer<?php 
                if ($settings['background_layer_close']) {
                    ?> dce-modal-background-layer-close<?php 
                }
                ?> modal-background-layer<?php 
                if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                    ?> block-i<?php 
                }
                ?>" data-dismiss="modal" data-target="dce-popup-<?php 
                echo esc_attr($this->get_id());
                ?>-<?php 
                echo esc_attr($this->get_unique_id());
                ?>"></div>
			<?php 
            }
            ?>

				<div id="dce-popup-<?php 
            echo esc_attr($this->get_id());
            ?>-<?php 
            echo esc_attr($this->get_unique_id());
            ?>"
					class="dce-popup-<?php 
            echo esc_attr($this->get_id());
            ?> dce-modal<?php 
            if ($settings['esc_hide'] && !\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                ?> modal-hide-esc<?php 
            }
            if ($settings['scroll_hide'] && !\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                ?> modal-hide-on-scroll<?php 
            }
            ?> modal-<?php 
            echo esc_attr($settings['modal_align']);
            ?> modal-<?php 
            echo esc_attr($settings['modal_valign']);
            ?> <?php 
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                ?> block-i<?php 
            }
            ?>"
					tabindex="-1"
					role="dialog"
					>

					<div class="modal-dialog<?php 
            if ($settings['open_animation']) {
                ?> animated<?php 
                echo $infinite;
            }
            ?>" role="document" >
						<div class="modal-content">

							<div class="modal-body">
								<?php 
            if ($settings['template']) {
                $template_system = \DynamicContentForElementor\Plugin::instance()->template_system;
                echo $template_system->build_elementor_template_special(['id' => $settings['template'], 'inlinecss' => \true]);
            } else {
                echo do_shortcode($settings['modal_content']);
            }
            ?>
							</div>


								<?php 
            if ($settings['enable_close_button']) {
                ?>
								<button type="button" class="dce-close dce-modal-close close-<?php 
                echo esc_attr($settings['close_type']);
                ?> close-<?php 
                echo esc_attr($settings['close_align']);
                ?> close-<?php 
                echo esc_attr($settings['close_valign']);
                ?>" data-dismiss="modal" aria-label="Close">

									<?php 
                if ($settings['close_type'] == 'text') {
                    ?><span class="dce-button-text"><?php 
                    echo esc_html($settings['close_text']);
                    ?></span><?php 
                }
                ?>

									<?php 
                if ($settings['close_type'] == 'icon') {
                    if (isset($settings['close_icon']) || isset($settings['selected_close_icon'])) {
                        echo Helper::get_migrated_icon($settings, 'close_icon', 'fa fa-times');
                    }
                }
                ?>

									<?php 
                if ($settings['close_type'] == 'image') {
                    if ($settings['close_image']['id']) {
                        ?><img class="close-img" aria-hidden="true" src="<?php 
                        echo esc_url($settings['close_image']['url']);
                        ?>" /><?php 
                    }
                }
                ?>

									<?php 
                if ($settings['close_type'] == 'x') {
                    ?>
										<span class="dce-quit-ics"></span>
								<?php 
                }
                ?>

								</button>
				<?php 
            }
            ?>
						</div>
					</div>
				</div>
			</div>
			<?php 
        }
    }
    protected function check_cookie()
    {
        $settings = $this->get_settings_for_display();
        if ($settings['always_visible']) {
            return \false;
        }
        $dce_cookie = \false;
        if (!empty($_COOKIE) && isset($_COOKIE['dce-popup-' . $this->get_id()])) {
            $dce_cookie = \true;
        }
        return $dce_cookie;
    }
}
