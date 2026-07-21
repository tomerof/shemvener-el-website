<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Icons_Manager;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class PdfViewer extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_script_depends()
    {
        return ['dce-pdf-viewer'];
    }
    public function get_style_depends()
    {
        return ['dce-pdf-js-viewer', 'dce-pdf-viewer'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_pdfviewer', ['label' => $this->get_title()]);
        $this->add_control('source', ['label' => esc_html__('Source', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'media_file', 'options' => ['media_file' => esc_html__('Media File', 'dynamic-content-for-elementor'), 'url' => esc_html__('URL', 'dynamic-content-for-elementor')], 'frontend_available' => \true]);
        $this->add_control('source_url', ['label' => esc_html__('PDF URL', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::URL, 'description' => esc_html__('You can only enter URL from your own domain', 'dynamic-content-for-elementor'), 'show_external' => \false, 'condition' => ['source' => 'url'], 'frontend_available' => \true]);
        $this->add_control('source_media', ['label' => esc_html__('Upload PDF File', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::MEDIA, 'media_type' => 'application/pdf', 'frontend_available' => \true, 'condition' => ['source' => 'media_file']]);
        $this->add_control('size_adjustable_controls', ['frontend_available' => \true, 'label' => esc_html__('Size Adjustable', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_responsive_control('size_adjustable_width', ['frontend_available' => \true, 'label' => esc_html__('Width (px)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '800', 'condition' => ['size_adjustable_controls!' => '']]);
        $this->add_responsive_control('size_adjustable_height', ['frontend_available' => \true, 'label' => esc_html__('Height (px)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '600', 'condition' => ['size_adjustable_controls!' => '']]);
        $this->add_control('download_controls', ['frontend_available' => \true, 'label' => esc_html__('Download Button', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_control('download_button_text', ['label' => esc_html__('Text for "Download" Button', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Download PDF', 'dynamic-content-for-elementor'), 'condition' => ['download_controls!' => '']]);
        $this->add_control('print_controls', ['frontend_available' => \true, 'label' => esc_html__('Print Button', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_control('print_button_text', ['label' => esc_html__('Text for "Print" Button', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Print PDF', 'dynamic-content-for-elementor'), 'condition' => ['print_controls!' => '']]);
        $this->add_control('navigation_controls', ['frontend_available' => \true, 'label' => esc_html__('Navigation Controls', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_control('previous_icon', ['label' => esc_html__('Previous Icon', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ICONS, 'skin' => 'inline', 'label_block' => \false, 'condition' => ['navigation_controls!' => '']]);
        $this->add_control('previous_text', ['label' => esc_html__('Text for "Previous" Button', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Previous', 'dynamic-content-for-elementor'), 'condition' => ['navigation_controls!' => '']]);
        $this->add_control('next_icon', ['label' => esc_html__('Next Icon', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ICONS, 'skin' => 'inline', 'label_block' => \false, 'condition' => ['navigation_controls!' => '']]);
        $this->add_control('next_text', ['label' => esc_html__('Text for "Next" Button', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Next', 'dynamic-content-for-elementor'), 'condition' => ['navigation_controls!' => '']]);
        $this->add_control('zoom_controls', ['frontend_available' => \true, 'label' => esc_html__('Zoom Controls', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_responsive_control('zoom', ['frontend_available' => \true, 'label' => esc_html__('Zoom', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'separator' => 'before', 'default' => '1']);
        $this->end_controls_section();
        $this->start_controls_section('section_global', ['label' => $this->get_title(), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('pdf_align', ['label' => esc_html__('PDF Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-right']], 'prefix_class' => 'elementor%s-align-', 'default' => '']);
        $this->add_responsive_control('controls_align', ['label' => esc_html__('Controls Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['flex-start' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-center'], 'flex-end' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-right']], 'selectors' => ['{{WRAPPER}} .dce-pdf-controls' => 'justify-content: {{VALUE}};'], 'default' => 'flex-start']);
        $this->add_responsive_control('pdf_space', ['label' => esc_html__('Space between controls and PDF', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 20], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-pdf-canvas-container' => 'padding-top: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('controls_space', ['label' => esc_html__('Space between controls', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 20], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-pdf-zoom-controls' => 'padding-left: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pdf-download-controls' => 'padding-left: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pdf-print-controls' => 'padding-left: {{SIZE}}{{UNIT}};'], 'conditions' => ['relation' => 'or', 'terms' => [['name' => 'navigation_controls', 'operator' => '!==', 'value' => ''], ['name' => 'zoom_controls', 'operator' => '!==', 'value' => ''], ['name' => 'download_controls', 'operator' => '!==', 'value' => ''], ['name' => 'print_controls', 'operator' => '!==', 'value' => '']]]]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'canvas_border', 'selector' => '{{WRAPPER}} canvas.dce-pdf-renderer', 'separator' => 'before']);
        $this->add_control('canvas_border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} canvas.dce-pdf-renderer' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->end_controls_section();
        $this->start_controls_section('section_navigation_style', ['label' => esc_html__('Navigation Controls', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['navigation_controls!' => '']]);
        $this->add_responsive_control('navigation_controls_space', ['label' => esc_html__('Space between Navigation Controls', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 5, 'unit' => 'px'], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-pdf-go-previous' => 'margin-right: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pdf-go-next' => 'margin-left: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pdf-current-page' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};']]);
        $this->start_controls_tabs('tabs_navigation_controls_style');
        $this->start_controls_tab('tab_navigation_controls_normal', ['label' => esc_html__('Normal', 'dynamic-content-for-elementor')]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'navigation_typography', 'selector' => '{{WRAPPER}} .dce-pdf-navigation-controls button, {{WRAPPER}} .dce-pdf-navigation-controls input']);
        $this->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'navigation_text_shadow', 'selector' => '{{WRAPPER}} .dce-pdf-navigation-controls button, {{WRAPPER}} .dce-pdf-navigation-controls input']);
        $this->add_control('navigation_controls_text_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-pdf-navigation-controls button, {{WRAPPER}} .dce-pdf-navigation-controls input' => 'color: {{VALUE}};']]);
        $this->add_control('navigation_controls_background_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pdf-navigation-controls button, {{WRAPPER}} .dce-pdf-navigation-controls input' => 'background-color: {{VALUE}};']]);
        $this->add_responsive_control('current_page_width', ['label' => esc_html__('Width of the Current Page Indicator', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SLIDER, 'size_units' => ['px', 'em', '%'], 'default' => ['unit' => 'em', 'size' => 5], 'range' => ['px' => ['min' => 1, 'max' => 800, 'step' => 5], 'em' => ['min' => 1, 'max' => 10, 'step' => 0.1], '%' => ['min' => 1, 'max' => 100, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-pdf-current-page' => 'width: {{SIZE}}{{UNIT}};']]);
        $this->end_controls_tab();
        $this->start_controls_tab('tab_navigation_controls_hover', ['label' => esc_html__('Hover', 'dynamic-content-for-elementor')]);
        $this->add_control('navigation_controls_hover_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pdf-navigation-controls button:hover, {{WRAPPER}} .dce-pdf-navigation-controls input:hover, {{WRAPPER}} .dce-pdf-navigation-controls button:focus, {{WRAPPER}} .dce-pdf-navigation-controls input:focus' => 'color: {{VALUE}};']]);
        $this->add_control('navigation_controls_background_hover_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pdf-navigation-controls button:hover, {{WRAPPER}} .dce-pdf-navigation-controls input:hover, {{WRAPPER}} .dce-pdf-navigation-controls button:focus, {{WRAPPER}} .dce-pdf-navigation-controls input:focus' => 'background-color: {{VALUE}};']]);
        $this->add_control('navigation_controls_hover_border_color', ['label' => esc_html__('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['border_border!' => ''], 'selectors' => ['{{WRAPPER}} .dce-pdf-navigation-controls button:hover, {{WRAPPER}} .dce-pdf-navigation-controls input:hover, {{WRAPPER}} .dce-pdf-navigation-controls button:focus, {{WRAPPER}} .dce-pdf-navigation-controls input:focus' => 'border-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'navigation_controls_border', 'selector' => '{{WRAPPER}} .dce-pdf-navigation-controls button, {{WRAPPER}} .dce-pdf-navigation-controls input', 'separator' => 'before']);
        $this->add_control('navigation_controls_border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-pdf-navigation-controls button, {{WRAPPER}} .dce-pdf-navigation-controls input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'navigation_controls_box_shadow', 'selector' => '{{WRAPPER}} .dce-pdf-navigation-controls button:hover']);
        $this->add_responsive_control('navigation_controls_text_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .dce-pdf-navigation-controls button, {{WRAPPER}} .dce-pdf-navigation-controls input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'separator' => 'before']);
        $this->end_controls_section();
        $this->start_controls_section('section_download_style', ['label' => esc_html__('Download Control', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['download_controls!' => '']]);
        $this->start_controls_tabs('tabs_download_controls_style');
        $this->start_controls_tab('tab_download_controls_normal', ['label' => esc_html__('Normal', 'dynamic-content-for-elementor')]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'download_typography', 'selector' => '{{WRAPPER}} .dce-pdf-download-controls button']);
        $this->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'download_text_shadow', 'selector' => '{{WRAPPER}} .dce-pdf-download-controls button.dce-pdf-download']);
        $this->add_control('download_controls_text_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-pdf-download-controls button' => 'color: {{VALUE}};']]);
        $this->add_control('download_controls_background_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pdf-download-controls button' => 'background-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->start_controls_tab('tab_download_controls_hover', ['label' => esc_html__('Hover', 'dynamic-content-for-elementor')]);
        $this->add_control('download_controls_hover_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pdf-download-controls button:hover, {{WRAPPER}} .dce-pdf-download-controls button:focus' => 'color: {{VALUE}};']]);
        $this->add_control('download_controls_background_hover_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pdf-download-controls button:hover, {{WRAPPER}} .dce-pdf-download-controls button:focus' => 'background-color: {{VALUE}};']]);
        $this->add_control('download_controls_hover_border_color', ['label' => esc_html__('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['border_border!' => ''], 'selectors' => ['{{WRAPPER}} .dce-pdf-download-controls button:hover, {{WRAPPER}} .dce-pdf-download-controls button:focus' => 'border-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'download_controls_border', 'selector' => '{{WRAPPER}} .dce-pdf-download-controls button, {{WRAPPER}} .dce-pdf-download-controls input', 'separator' => 'before']);
        $this->add_control('download_controls_border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-pdf-download-controls button, {{WRAPPER}} .dce-pdf-download-controls input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'download_controls_box_shadow', 'selector' => '{{WRAPPER}} .dce-pdf-download-controls button:hover']);
        $this->add_responsive_control('download_controls_text_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .dce-pdf-download-controls button, {{WRAPPER}} .dce-pdf-download-controls input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'separator' => 'before']);
        $this->end_controls_section();
        $this->start_controls_section('section_print_style', ['label' => esc_html__('Print Control', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['print_controls!' => '']]);
        $this->start_controls_tabs('tabs_print_controls_style');
        $this->start_controls_tab('tab_print_controls_normal', ['label' => esc_html__('Normal', 'dynamic-content-for-elementor')]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'print_typography', 'selector' => '{{WRAPPER}} .dce-pdf-print-controls button']);
        $this->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'print_text_shadow', 'selector' => '{{WRAPPER}} .dce-pdf-print-controls button.dce-pdf-print']);
        $this->add_control('print_controls_text_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-pdf-print-controls button' => 'color: {{VALUE}};']]);
        $this->add_control('print_controls_background_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pdf-print-controls button' => 'background-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->start_controls_tab('tab_print_controls_hover', ['label' => esc_html__('Hover', 'dynamic-content-for-elementor')]);
        $this->add_control('print_controls_hover_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pdf-print-controls button:hover, {{WRAPPER}} .dce-pdf-print-controls button:focus' => 'color: {{VALUE}};']]);
        $this->add_control('print_controls_background_hover_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pdf-print-controls button:hover, {{WRAPPER}} .dce-pdf-print-controls button:focus' => 'background-color: {{VALUE}};']]);
        $this->add_control('print_controls_hover_border_color', ['label' => esc_html__('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['border_border!' => ''], 'selectors' => ['{{WRAPPER}} .dce-pdf-print-controls button:hover, {{WRAPPER}} .dce-pdf-print-controls button:focus' => 'border-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'print_controls_border', 'selector' => '{{WRAPPER}} .dce-pdf-print-controls button, {{WRAPPER}} .dce-pdf-print-controls input', 'separator' => 'before']);
        $this->add_control('print_controls_border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-pdf-print-controls button, {{WRAPPER}} .dce-pdf-print-controls input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'print_controls_box_shadow', 'selector' => '{{WRAPPER}} .dce-pdf-print-controls button:hover']);
        $this->add_responsive_control('print_controls_text_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .dce-pdf-print-controls button, {{WRAPPER}} .dce-pdf-print-controls input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'separator' => 'before']);
        $this->end_controls_section();
        $this->start_controls_section('section_zoom_style', ['label' => esc_html__('Zoom Controls', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['zoom_controls!' => '']]);
        $this->add_responsive_control('zoom_controls_space', ['label' => esc_html__('Space between Zoom Controls', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 5, 'unit' => 'px'], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-pdf-zoom-in' => 'margin-right: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pdf-zoom-out' => 'margin-left: {{SIZE}}{{UNIT}};']]);
        $this->start_controls_tabs('tabs_zoom_controls_style');
        $this->start_controls_tab('tab_zoom_controls_normal', ['label' => esc_html__('Normal', 'dynamic-content-for-elementor')]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'zoom_typography', 'selector' => '{{WRAPPER}} .dce-pdf-zoom-controls button']);
        $this->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'zoom_text_shadow', 'selector' => '{{WRAPPER}} .dce-pdf-zoom-controls button']);
        $this->add_control('zoom_controls_text_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-pdf-zoom-controls button' => 'color: {{VALUE}};']]);
        $this->add_control('zoom_controls_background_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pdf-zoom-controls button' => 'background-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->start_controls_tab('tab_zoom_controls_hover', ['label' => esc_html__('Hover', 'dynamic-content-for-elementor')]);
        $this->add_control('zoom_controls_hover_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pdf-zoom-controls button:hover, {{WRAPPER}} .dce-pdf-zoom-controls button:focus' => 'color: {{VALUE}};']]);
        $this->add_control('zoom_controls_background_hover_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pdf-zoom-controls button:hover, {{WRAPPER}} .dce-pdf-zoom-controls button:focus' => 'background-color: {{VALUE}};']]);
        $this->add_control('zoom_controls_hover_border_color', ['label' => esc_html__('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['border_border!' => ''], 'selectors' => ['{{WRAPPER}} .dce-pdf-zoom-controls button:hover, {{WRAPPER}} .dce-pdf-zoom-controls button:focus' => 'border-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'zoom_controls_border', 'selector' => '{{WRAPPER}} .dce-pdf-zoom-controls button', 'separator' => 'before']);
        $this->add_control('zoom_controls_border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-pdf-zoom-controls button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'zoom_controls_box_shadow', 'selector' => '{{WRAPPER}} .dce-pdf-zoom-controls button:hover']);
        $this->add_responsive_control('zoom_controls_text_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .dce-pdf-zoom-controls button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'separator' => 'before']);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        if ('url' === $settings['source'] && !$settings['source_url']['url'] || 'media_file' === $settings['source'] && !$settings['source_media']['url']) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                Helper::notice(\false, esc_html__('Select a PDF', 'dynamic-content-for-elementor'));
            }
            return;
        }
        ?>

		<?php 
        if ($settings['navigation_controls'] || $settings['zoom_controls'] || $settings['download_controls'] || $settings['print_controls']) {
            ?>
		<div class="dce-pdf-controls">
			<?php 
            if ($settings['navigation_controls']) {
                ?>
			<div class="dce-pdf-navigation-controls">
				<button class="dce-pdf-go-previous"><?php 
                if (!empty($settings['previous_icon']['value'])) {
                    Icons_Manager::render_icon($settings['previous_icon'], ['aria-hidden' => 'true']);
                }
                echo sanitize_text_field($settings['previous_text']);
                ?></button>
				<input class="dce-pdf-current-page" value="1" type="number" />
				<button class="dce-pdf-go-next"><?php 
                echo sanitize_text_field($settings['next_text']);
                if (!empty($settings['next_icon']['value'])) {
                    Icons_Manager::render_icon($settings['next_icon'], ['aria-hidden' => 'true']);
                }
                ?></button>
			</div>
			<?php 
            }
            ?>

			<?php 
            if ($settings['zoom_controls']) {
                ?>
			<div class="dce-pdf-zoom-controls">
				<button class="dce-pdf-zoom-in">+</button>
				<button class="dce-pdf-zoom-out">-</button>
			</div>
			<?php 
            }
            ?>

			<?php 
            if ($settings['download_controls']) {
                ?>
			<div class="dce-pdf-download-controls">
			<button class="dce-pdf-download"><?php 
                echo esc_html($settings['download_button_text']);
                ?></button>
			</div>
			<?php 
            }
            ?>

			<?php 
            if ($settings['print_controls']) {
                ?>
			<div class="dce-pdf-print-controls">
			<button class="dce-pdf-print"><?php 
                echo esc_html($settings['print_button_text']);
                ?></button>
			</div>
			<?php 
            }
            ?>
		</div>
		<?php 
        }
        ?>

		<div class="dce-pdf-canvas-container">
			<canvas class="dce-pdf-renderer"></canvas>
		</div>
<?php 
    }
}
