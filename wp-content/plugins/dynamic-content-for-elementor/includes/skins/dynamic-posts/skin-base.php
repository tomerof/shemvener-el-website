<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Includes\Skins;

use DynamicContentForElementor\DynamicQuery;
use Elementor\Skin_Base as Elementor_Skin_Base;
use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use DynamicContentForElementor\Plugin;
use Elementor\Icons_Manager;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
abstract class SkinBase extends Elementor_Skin_Base
{
    protected $current_permalink;
    protected $current_id;
    protected $counter = 0;
    protected $depended_scripts;
    protected $depended_styles;
    public function get_script_depends()
    {
        return $this->depended_scripts;
    }
    public function get_style_depends()
    {
        return $this->depended_styles;
    }
    public function register_controls_layout(\DynamicContentForElementor\Widgets\DynamicPostsBase $widget)
    {
        $this->parent = $widget;
        // Block Style
        $this->register_style_controls();
        // Pagination Style
        $this->register_style_pagination_controls();
        // Infinite Scroll Style
        $this->register_style_infinitescroll_controls();
    }
    /**
     * Get Parent or throw an error
     *
     * @return \DynamicContentForElementor\Widgets\DynamicPostsBase
     */
    protected function get_parent()
    {
        /**
         * @var \DynamicContentForElementor\Widgets\DynamicPostsBase|null $parent
         */
        $parent = $this->parent;
        if ($parent === null) {
            throw new \Error('Skin Parent is NULL');
        }
        return $parent;
    }
    protected function register_style_pagination_controls()
    {
        $this->start_controls_section('section_style_pagination', ['label' => esc_html__('Pagination', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['_skin!' => 'carousel', 'pagination_enable' => 'yes', 'infiniteScroll_enable' => '']]);
        $this->add_control('pagination_heading_style', ['label' => esc_html__('Pagination', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_responsive_control('pagination_align', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'options' => ['flex-start' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-center'], 'flex-end' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-right']], 'default' => 'center', 'selectors' => ['{{WRAPPER}} .dce-pagination' => 'justify-content: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'pagination_typography', 'label' => esc_html__('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-pagination']);
        $this->add_responsive_control('pagination_space', ['label' => esc_html__('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 10], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-pagination-top' => 'padding-bottom: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination-bottom' => 'padding-top: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('pagination_spacing', ['label' => esc_html__('Horizontal Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 1], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-pagination span, {{WRAPPER}} .dce-pagination a' => 'margin-right: {{SIZE}}{{UNIT}};']]);
        $this->add_control('pagination_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .dce-pagination span, {{WRAPPER}} .dce-pagination a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('pagination_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-pagination span, {{WRAPPER}} .dce-pagination a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('pagination_heading_colors', ['label' => esc_html__('Colors', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->start_controls_tabs('pagination_colors');
        $this->start_controls_tab('pagination_text_colors', ['label' => esc_html__('Normal', 'dynamic-content-for-elementor')]);
        $this->add_control('pagination_text_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-pagination span, {{WRAPPER}} .dce-pagination a' => 'color: {{VALUE}};']]);
        $this->add_control('pagination_background_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination span, {{WRAPPER}} .dce-pagination a' => 'background-color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'pagination_border', 'label' => esc_html__('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-pagination span, {{WRAPPER}} .dce-pagination a']);
        $this->end_controls_tab();
        $this->start_controls_tab('pagination_text_colors_hover', ['label' => esc_html__('Hover', 'dynamic-content-for-elementor')]);
        $this->add_control('pagination_hover_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination a:hover' => 'color: {{VALUE}};']]);
        $this->add_control('pagination_background_hover_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination a:hover' => 'background-color: {{VALUE}};']]);
        $this->add_control('pagination_hover_border_color', ['label' => esc_html__('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['pagination_border_border!' => ''], 'selectors' => ['{{WRAPPER}} .dce-pagination a:hover' => 'border-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->start_controls_tab('pagination_text_colors_current', ['label' => esc_html__('Current', 'dynamic-content-for-elementor')]);
        $this->add_control('pagination_current_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination span.current' => 'color: {{VALUE}};']]);
        $this->add_control('pagination_background_current_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination span.current' => 'background-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_control('pagination_heading_prevnext', ['label' => esc_html__('Prev/Next', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_responsive_control('pagination_spacing_prevnext', ['label' => esc_html__('Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-pagination .pageprev' => 'margin-right: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pagenext' => 'margin-left: {{SIZE}}{{UNIT}};'], 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_responsive_control('pagination_icon_spacing_prevnext', ['label' => esc_html__('Icon Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px', 'em', 'rem', 'vw', 'custom'], 'range' => ['px' => ['max' => 50, 'min' => 0, 'step' => 1]], 'default' => ['size' => 10, 'unit' => 'px'], 'selectors' => ['{{WRAPPER}} .dce-pagination .pageprev.icon.left .fa' => 'margin-right: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pagenext.icon.left .fa' => 'margin-right: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pageprev.icon.left .fas' => 'margin-right: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pagenext.icon.left .fas' => 'margin-right: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pageprev.icon.left svg' => 'margin-right: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pagenext.icon.left svg' => 'margin-right: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pageprev.icon.right .fa' => 'margin-left: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pagenext.icon.right .fa' => 'margin-left: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pageprev.icon.right .fas' => 'margin-left: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pagenext.icon.right .fas' => 'margin-left: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pageprev.icon.right svg' => 'margin-left: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pagenext.icon.right svg' => 'margin-left: {{SIZE}}{{UNIT}};'], 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_responsive_control('pagination_icon_size_prevnext', ['label' => esc_html__('Icon Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px', 'em', 'rem', 'vw', 'custom'], 'range' => ['px' => ['max' => 100], 'em' => ['min' => 0, 'max' => 10], 'rem' => ['min' => 0, 'max' => 10]], 'default' => ['size' => 10, 'unit' => 'px'], 'selectors' => ['{{WRAPPER}} .dce-pagination .pageprev.icon .fa' => 'font-size: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pageprev.icon .fas' => 'font-size: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pagenext.icon .fa' => 'font-size: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pagenext.icon .fas' => 'font-size: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pageprev.icon svg' => 'height: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pagenext.icon svg' => 'height: {{SIZE}}{{UNIT}};'], 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->start_controls_tabs('pagination_prevnext_colors');
        $this->start_controls_tab('pagination_prevnext_text_colors', ['label' => esc_html__('Normal', 'dynamic-content-for-elementor'), 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_control('pagination_prevnext_text_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-pagination .pageprev, {{WRAPPER}} .dce-pagination .pagenext' => 'color: {{VALUE}}; fill: {{VALUE}}'], 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_control('pagination_prevnext_background_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-pagination .pageprev, {{WRAPPER}} .dce-pagination .pagenext' => 'background-color: {{VALUE}};'], 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'pagination_prevnext_border', 'label' => esc_html__('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-pagination .pageprev, {{WRAPPER}} .dce-pagination .pagenext', 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_control('pagination_prevnext_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-pagination .pageprev, {{WRAPPER}} .dce-pagination .pagenext' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->end_controls_tab();
        $this->start_controls_tab('pagination_prevnext_text_colors_hover', ['label' => esc_html__('Hover', 'dynamic-content-for-elementor'), 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_control('pagination_prevnext_hover_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination .pageprev:hover, {{WRAPPER}} .dce-pagination .pagenext:hover' => 'color: {{VALUE}}; fill: {{VALUE}}'], 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_control('pagination_prevnext_background_hover_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination .pageprev:hover, {{WRAPPER}} .dce-pagination .pagenext:hover' => 'background-color: {{VALUE}};'], 'condition' => ['pagination_show_prevnext' => 'yes']]);
        $this->add_control('pagination_prevnext_hover_border_color', ['label' => esc_html__('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination .pageprev:hover, {{WRAPPER}} .dce-pagination .pagenext:hover' => 'border-color: {{VALUE}};'], 'condition' => ['pagination_show_prevnext' => 'yes', 'pagination_prevnext_border_border!' => '']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_control('pagination_heading_firstlast', ['label' => esc_html__('First/Last', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_responsive_control('pagination_icon_spacing_firstlast', ['label' => esc_html__('Icon Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px', 'em', 'rem', 'vw', 'custom'], 'range' => ['px' => ['max' => 50, 'min' => 0, 'step' => 1]], 'default' => ['size' => 10, 'unit' => 'px'], 'selectors' => ['{{WRAPPER}} .dce-pagination .pagefirst.icon.left .fa' => 'margin-right: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pagelast.icon.left .fa' => 'margin-right: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pagefirst.icon.left .fas' => 'margin-right: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pagelast.icon.left .fas' => 'margin-right: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pagefirst.icon.left svg' => 'margin-right: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pagelast.icon.left svg' => 'margin-right: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pagefirst.icon.right .fa' => 'margin-left: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pagelast.icon.right .fa' => 'margin-left: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pagefirst.icon.right .fas' => 'margin-left: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pagelast.icon.right .fas' => 'margin-left: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pagefirst.icon.right svg' => 'margin-left: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pagelast.icon.right svg' => 'margin-left: {{SIZE}}{{UNIT}};'], 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_responsive_control('pagination_icon_size_firstlast', ['label' => esc_html__('Icon Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px', 'em', 'rem', 'vw', 'custom'], 'range' => ['px' => ['max' => 100], 'em' => ['min' => 0, 'max' => 10], 'rem' => ['min' => 0, 'max' => 10]], 'default' => ['size' => 10, 'unit' => 'px'], 'selectors' => ['{{WRAPPER}} .dce-pagination .pagefirst.icon .fa' => 'font-size: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pagefirst.icon .fas' => 'font-size: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pagelast.icon .fa' => 'font-size: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pagelast.icon .fas' => 'font-size: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pagefirst.icon svg' => 'height: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pagelast.icon svg' => 'height: {{SIZE}}{{UNIT}};'], 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_responsive_control('pagination_spacing_firstlast', ['label' => esc_html__('Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-pagination .pagefirst' => 'margin-right: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .dce-pagination .pagelast' => 'margin-left: {{SIZE}}{{UNIT}};'], 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->start_controls_tabs('pagination_firstlast_colors');
        $this->start_controls_tab('pagination_firstlast_text_colors', ['label' => esc_html__('Normal', 'dynamic-content-for-elementor'), 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_control('pagination_firstlast_text_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-pagination .pagefirst, {{WRAPPER}} .dce-pagination .pagelast' => 'color: {{VALUE}}; fill: {{VALUE}}'], 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_control('pagination_firstlast_background_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-pagination .pagefirst, {{WRAPPER}} .dce-pagination .pagelast' => 'background-color: {{VALUE}};'], 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'pagination_firstlast_border', 'label' => esc_html__('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-pagination .pagefirst, {{WRAPPER}} .dce-pagination .pagelast', 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_control('pagination_firstlast_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-pagination .pagefirst, {{WRAPPER}} .dce-pagination .pagelast' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->end_controls_tab();
        $this->start_controls_tab('pagination_firstlast_text_colors_hover', ['label' => esc_html__('Hover', 'dynamic-content-for-elementor'), 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_control('pagination_firstlast_hover_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination .pagefirst:hover, {{WRAPPER}} .dce-pagination .pagelast:hover' => 'color: {{VALUE}}; fill: {{VALUE}}'], 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_control('pagination_firstlast_background_hover_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination .pagefirst:hover, {{WRAPPER}} .dce-pagination .pagelast:hover' => 'background-color: {{VALUE}};'], 'condition' => ['pagination_show_firstlast' => 'yes']]);
        $this->add_control('pagination_firstlast_hover_border_color', ['label' => esc_html__('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination .pagefirst:hover, {{WRAPPER}} .dce-pagination .pagelast:hover' => 'border-color: {{VALUE}};'], 'condition' => ['pagination_show_firstlast' => 'yes', 'pagination_firstlast_border_border!' => '']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_control('pagination_heading_progression', ['label' => esc_html__('Progression', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['pagination_show_progression' => 'yes']]);
        $this->add_responsive_control('pagination_spacing_progression', ['label' => esc_html__('Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce-pagination .progression' => 'margin-right: {{SIZE}}{{UNIT}};'], 'condition' => ['pagination_show_progression' => 'yes']]);
        $this->start_controls_tabs('pagination_progression_colors');
        $this->start_controls_tab('pagination_progression_text_colors', ['label' => esc_html__('Normal', 'dynamic-content-for-elementor'), 'condition' => ['pagination_show_progression' => 'yes']]);
        $this->add_control('pagination_progression_text_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-pagination .progression' => 'color: {{VALUE}};'], 'condition' => ['pagination_show_progression' => 'yes']]);
        $this->add_control('pagination_progression_background_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-pagination .progression' => 'background-color: {{VALUE}};'], 'condition' => ['pagination_show_progression' => 'yes']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'pagination_progression_border', 'label' => esc_html__('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-pagination .progression', 'condition' => ['pagination_show_progression' => 'yes']]);
        $this->add_control('pagination_progression_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-pagination .progression' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['pagination_show_progression' => 'yes']]);
        $this->end_controls_tab();
        $this->start_controls_tab('pagination_progression_text_colors_hover', ['label' => esc_html__('Hover', 'dynamic-content-for-elementor'), 'condition' => ['pagination_show_progression' => 'yes']]);
        $this->add_control('pagination_progression_hover_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination .progression' => 'color: {{VALUE}};'], 'condition' => ['pagination_show_progression' => 'yes']]);
        $this->add_control('pagination_progression_background_hover_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination .progression' => 'background-color: {{VALUE}};'], 'condition' => ['pagination_show_progression' => 'yes']]);
        $this->add_control('pagination_progression_hover_border_color', ['label' => esc_html__('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-pagination .progression' => 'border-color: {{VALUE}};'], 'condition' => ['pagination_show_progression' => 'yes', 'pagination_firstlast_border_border!' => '']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();
    }
    protected function register_style_infinitescroll_controls()
    {
        $this->start_controls_section('section_style_infiniteScroll', ['label' => esc_html__('Infinite Scroll', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['infiniteScroll_enable' => 'yes']]);
        $this->add_responsive_control('infiniteScroll_spacing', ['label' => esc_html__('Spacing status', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 1], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .infiniteScroll' => 'margin-top: {{SIZE}}{{UNIT}};']]);
        $this->add_control('infiniteScroll_heading_button_style', ['label' => esc_html__('Button', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['infiniteScroll_trigger' => 'button']]);
        $this->add_responsive_control('infiniteScroll_button_align', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'options' => ['flex-start' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-center'], 'flex-end' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-right']], 'default' => 'center', 'selectors' => ['{{WRAPPER}} div.infiniteScroll' => 'justify-content: {{VALUE}};'], 'condition' => ['infiniteScroll_trigger' => 'button']]);
        $this->start_controls_tabs('infiniteScroll_button_colors');
        $this->start_controls_tab('infiniteScroll_button_text_colors', ['label' => esc_html__('Normal', 'dynamic-content-for-elementor'), 'condition' => ['infiniteScroll_trigger' => 'button']]);
        $this->add_control('infiniteScroll_button_text_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .infiniteScroll button' => 'color: {{VALUE}};'], 'condition' => ['infiniteScroll_trigger' => 'button']]);
        $this->add_control('infiniteScroll_button_background_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .infiniteScroll button' => 'background-color: {{VALUE}};'], 'condition' => ['infiniteScroll_trigger' => 'button']]);
        $this->end_controls_tab();
        $this->start_controls_tab('infiniteScroll_button_text_colors_hover', ['label' => esc_html__('Hover', 'dynamic-content-for-elementor'), 'condition' => ['infiniteScroll_trigger' => 'button']]);
        $this->add_control('infiniteScroll_button_hover_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .infiniteScroll button:hover' => 'color: {{VALUE}};'], 'condition' => ['infiniteScroll_trigger' => 'button']]);
        $this->add_control('infiniteScroll_button_background_hover_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .infiniteScroll button:hover' => 'background-color: {{VALUE}};'], 'condition' => ['infiniteScroll_trigger' => 'button']]);
        $this->add_control('infiniteScroll_button_hover_border_color', ['label' => esc_html__('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .infiniteScroll button:hover' => 'border-color: {{VALUE}};'], 'condition' => ['infiniteScroll_trigger' => 'button']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_control('infiniteScroll_button_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .infiniteScroll button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['infiniteScroll_trigger' => 'button'], 'separator' => 'before']);
        $this->add_control('infiniteScroll_button_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .infiniteScroll button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['infiniteScroll_trigger' => 'button']]);
        $this->end_controls_section();
    }
    protected function register_style_controls()
    {
        // Blocks - Style
        $this->start_controls_section('section_blocks_style', ['label' => esc_html__('Blocks', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['style_items!' => ['template']]]);
        $this->add_responsive_control('blocks_align', ['label' => esc_html__('Text Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'default' => is_rtl() ? 'right' : 'left', 'render_type' => 'template', 'prefix_class' => 'dce-align%s-', 'selectors' => ['{{WRAPPER}} .dce-post-item' => 'text-align: {{VALUE}};'], 'separator' => 'before']);
        $this->add_responsive_control('blocks_align_v', ['label' => esc_html__('Vertical Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['flex-start' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-top'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-middle'], 'flex-end' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-bottom'], 'space-between' => ['title' => esc_html__('Space Between', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-stretch'], 'space-around' => ['title' => esc_html__('Space Around', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-stretch']], 'separator' => 'after', 'selectors' => ['{{WRAPPER}} .dce-post-block, {{WRAPPER}} .dce-item-area' => 'justify-content: {{VALUE}} !important;'], 'condition' => ['v_pos_postitems' => ['', 'stretch']]]);
        $this->add_control('blocks_bgcolor', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-post-item .dce-post-block' => 'background-color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'blocks_border', 'selector' => '{{WRAPPER}} .dce-post-item .dce-post-block']);
        $this->add_responsive_control('blocks_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-post-item .dce-post-block' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('blocks_border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'selectors' => ['{{WRAPPER}} .dce-post-item .dce-post-block' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'blocks_boxshadow', 'selector' => '{{WRAPPER}} .dce-post-item .dce-post-block']);
        // Vertical Alternate
        $this->add_control('dis_alternate', ['type' => Controls_Manager::RAW_HTML, 'show_label' => \false, 'separator' => 'before', 'raw' => '<img src="' . DCE_URL . 'assets/img/skins/alternate.png" />', 'content_classes' => 'dce-skin-dis', 'condition' => ['grid_type' => ['flex']]]);
        $this->add_responsive_control('blocks_alternate', ['label' => esc_html__('Vertical Alternate', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px'], 'range' => ['px' => ['max' => 100, 'min' => 0, 'step' => 1]], 'selectors' => ['{{WRAPPER}}.dce-col-3 .dce-post-item:nth-child(3n+2) .dce-post-block, {{WRAPPER}}:not(.dce-col-3) .dce-post-item:nth-child(even) .dce-post-block' => 'margin-top: {{SIZE}}{{UNIT}};'], 'condition' => ['grid_type' => ['flex']]]);
        $this->end_controls_section();
        $this->start_controls_section('section_fallback_style', ['label' => esc_html__('No Results Behaviour', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['fallback!' => '', 'fallback_type' => 'text']]);
        $this->add_responsive_control('fallback_align', ['label' => esc_html__('Text Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \false, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'default' => is_rtl() ? 'right' : 'left', 'selectors' => ['{{WRAPPER}} .dce-posts-fallback' => 'text-align: {{VALUE}};'], 'separator' => 'before']);
        $this->add_control('fallback_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-posts-fallback' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'fallback_typography', 'selector' => '{{WRAPPER}} .dce-posts-fallback']);
        $this->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'fallback_text_shadow', 'selector' => '{{WRAPPER}} .dce-posts-fallback']);
        $this->end_controls_section();
    }
    // Render main
    public function render()
    {
        $this->get_parent()->query_posts();
        $query = $this->get_parent()->get_query();
        $this->counter = 0;
        // Add WP_Query args to a data attribute so you can retrieve it for debug on the Elementor Editor
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $this->get_parent()->add_render_attribute('container', 'data-dce-debug-query-args', wp_json_encode($this->get_parent()->get_query_args()));
        }
        // RTL
        if ($this->get_parent()->get_settings_for_display('rtl')) {
            $this->get_parent()->add_render_attribute('container', 'class', 'dce-rtl');
        }
        Plugin::instance()->integrations->search_and_filter_pro->maybe_add_search_filter_class($this->get_parent(), ['class_prefix_v3' => 'search-filter-dynamic-posts-results-']);
        $fallback = $this->get_parent()->get_settings_for_display('fallback');
        if ($this->get_parent()->get_settings('infiniteScroll_enable') && $this->get_parent()->get_settings('pagination_enable') && 'rand' === $this->get_parent()->get_settings('orderby') && current_user_can('edit_posts')) {
            Helper::notice(\false, esc_html__('Infinite Scroll does not work correctly if you set the order randomly. Please choose another sorting type. This notice is not visible to your visitors.', 'dynamic-content-for-elementor'));
        }
        if ($this->get_parent()->get_settings('infiniteScroll_enable') && $this->get_parent()->get_settings('pagination_enable') && \Elementor\Plugin::$instance->editor->is_edit_mode()) {
            Helper::notice(\false, esc_html__('Infinite Scroll is not displayed correctly in the Elementor editor due to technical limitations but works correctly in the frontend.', 'dynamic-content-for-elementor'));
        }
        if ('masonry' === $this->get_instance_value('grid_type') && \Elementor\Plugin::$instance->editor->is_edit_mode()) {
            Helper::notice(\false, esc_html__('Masonry is not displayed correctly in the Elementor editor due to technical limitations but works correctly in the frontend.', 'dynamic-content-for-elementor'));
        }
        if ('grid-filters' === $this->get_parent()->get_settings('_skin') && \Elementor\Plugin::$instance->editor->is_edit_mode()) {
            Helper::notice(\false, esc_html__('Grid with Filters Skin is not displayed correctly in the Elementor editor due to technical limitations but works correctly in the frontend.', 'dynamic-content-for-elementor'));
        }
        if (!empty($this->get_parent()->get_settings_for_display('template_id')) && \Elementor\Plugin::$instance->editor->is_edit_mode() && 'loop' === get_post_meta($this->get_parent()->get_settings_for_display('template_id'), '_elementor_template_type', \true)) {
            Helper::notice(esc_html__('Alert', 'dynamic-content-for-elementor'), esc_html__('You have used a Loop template, created by Ele Custom Skin specifically for their features. Please use another type of template to avoid incompatibility.', 'dynamic-content-for-elementor'));
        }
        if (empty($query->found_posts)) {
            if (!empty($fallback)) {
                $this->render_fallback();
            } elseif ('search_filter' === $this->get_parent()->get_settings('query_type')) {
                // Show Container when using Search and Filter Pro to avoid incompatibility issues
                $this->render_loop_start();
                $this->render_loop_end();
            }
            return;
        }
        $query_post_before_loop = \false;
        $post_id = get_the_ID();
        if ($post_id !== \false) {
            $query_post_before_loop = get_post($post_id);
        }
        $this->get_parent()->add_render_attribute('container', 'class', 'dce-fix-background-loop');
        $this->render_loop_start();
        if ($query->in_the_loop) {
            $this->current_permalink = get_permalink();
            $this->current_id = get_the_ID();
            $this->render_post();
        } else {
            while ($query->have_posts()) {
                $query->the_post();
                $this->current_permalink = get_permalink();
                $this->current_id = get_the_ID();
                $this->render_post();
            }
        }
        global $wp_query;
        if ($query_post_before_loop) {
            // in case we are already nested inside a loop wp_reset_postdata would
            // reset the post to the one at the very top and not the one we are in:
            $wp_query->post = $query_post_before_loop;
            $wp_query->reset_postdata();
        } else {
            wp_reset_postdata();
        }
        $this->render_loop_end();
    }
    protected function render_post()
    {
        $style_items = $this->get_parent()->get_settings('style_items');
        $this->render_post_start();
        $skins_with_style_items = ['', 'grid', 'grid-filters', 'carousel', 'filters', 'dualcarousel', 'smoothscroll', '3d', 'accordion'];
        $skin = $this->get_parent()->get_settings('_skin');
        if (\in_array($skin, $skins_with_style_items, \true)) {
            if ('template' === $style_items) {
                $this->render_post_template();
            } elseif ('html_tokens' === $style_items) {
                $this->render_post_dynamic_html();
            } else {
                $this->render_post_items();
            }
        } else {
            $this->render_post_items();
        }
        $this->render_post_end();
        ++$this->counter;
    }
    protected function render_post_template()
    {
        $template_id = $this->get_parent()->get_settings_for_display('template_id');
        $template_id = apply_filters('wpml_object_id', $template_id, 'elementor_library', \true);
        $templatemode_enable_2 = $this->get_parent()->get_settings('templatemode_enable_2');
        $template_2_id = $this->get_parent()->get_settings_for_display('template_2_id');
        $template_2_id = apply_filters('wpml_object_id', $template_2_id, 'elementor_library', \true);
        $native_templatemode_enable = $this->get_parent()->get_settings('native_templatemode_enable') ?? '';
        if ($native_templatemode_enable && \DynamicContentForElementor\Plugin::instance()->template_system->is_active()) {
            $type_of_posts = get_post_type($this->current_id);
            $cptaxonomy = get_post_taxonomies($this->current_id);
            $options = get_option(DCE_TEMPLATE_SYSTEM_OPTION);
            // 2 - Archive
            $templatesystem_template_key = 'dyncontel_field_archive' . $type_of_posts;
            $post_template_id = $options[$templatesystem_template_key];
            if (isset($cptaxonomy) && \count($cptaxonomy) > 0) {
                $key = $cptaxonomy[0];
                $archive_key = 'dyncontel_field_archive_taxonomy_' . $key;
                // 3 - Taxonomy
                if (isset($options[$archive_key])) {
                    $post_template_id_taxo = $options[$archive_key];
                    if (!empty($post_template_id_taxo) && $post_template_id_taxo > 0) {
                        $templatesystem_template_key = $archive_key;
                    }
                }
                $post_template_id = $options[$templatesystem_template_key];
                // 4 - Terms
                $cptaxonomyterm = get_the_terms($this->current_id, $cptaxonomy[0]);
                if (isset($cptaxonomyterm) && $cptaxonomyterm) {
                    foreach ($cptaxonomyterm as $cpterm) {
                        $term_id = $cpterm->term_id;
                        $post_template_id_term = get_term_meta($term_id, 'dynamic_content_block', \true);
                        if (!empty($post_template_id_term)) {
                            $post_template_id = $post_template_id_term;
                        }
                    }
                }
            }
        } elseif ($templatemode_enable_2) {
            if ($this->counter % 2 == 0) {
                // Even
                $post_template_id = $template_id;
            } else {
                // Odd
                $post_template_id = $template_2_id;
            }
        } else {
            $post_template_id = $template_id;
        }
        if ($post_template_id) {
            $this->render_template($post_template_id);
        }
    }
    /**
     * @return void
     */
    protected function render_post_dynamic_html()
    {
        $html = $this->get_parent()->get_settings('html_tokens_editor');
        if (empty($html)) {
            return;
        }
        echo Plugin::instance()->text_templates->expand_shortcodes_or_callback($html, [], function ($str) {
            return Helper::get_dynamic_value($str);
        });
    }
    /**
     * Render Template
     *
     * @param int $template_id
     * @return void
     */
    protected function render_template(int $template_id)
    {
        $parent = $this->get_parent();
        $template_system = \DynamicContentForElementor\Plugin::instance()->template_system;
        echo $template_system->build_elementor_template_special(['id' => $template_id, 'post_id' => $this->current_id, 'inlinecss' => \true]);
        $this->parent = $parent;
    }
    protected function render_post_items()
    {
        $_skin = $this->get_parent()->get_settings('_skin');
        $style_items = $this->get_parent()->get_settings_for_display('style_items');
        $post_items = $this->get_parent()->get_settings_for_display('list_items');
        $hover_animation = $this->get_parent()->get_settings_for_display('hover_content_animation');
        $animation_class = !empty($hover_animation) && $style_items != 'float' && $_skin != 'gridtofullscreen3d' ? ' elementor-animation-' . esc_attr($hover_animation) : '';
        $hover_effects = $this->get_parent()->get_settings('hover_text_effect');
        $hoverEffects_class = !empty($hover_effects) && $style_items == 'float' && $_skin != 'gridtofullscreen3d' ? ' dce-hover-effect-' . esc_attr($hover_effects) . ' dce-hover-effect-content dce-close' : '';
        $hoverEffects_start = !empty($hover_effects) && $style_items == 'float' && $_skin != 'gridtofullscreen3d' ? '<div class="dce-hover-effect-' . esc_attr($hover_effects) . ' dce-hover-effect-content dce-close">' : '';
        $hoverEffects_end = !empty($hover_effects) && $style_items == 'float' ? '</div>' : '';
        $imagearea_start = '';
        $contentarea_start = '';
        $area_end = '';
        if ($style_items && $style_items != 'default') {
            $imagearea_start = '<div class="dce-image-area dce-item-area">';
            $contentarea_start = '<div class="dce-content-area dce-item-area' . $animation_class . '">';
            $area_end = '</div>';
            echo $imagearea_start;
            foreach ($post_items as $item) {
                $_id = $item['_id'];
                if ($item['item_id'] == 'item_image') {
                    $this->render_repeater_item_start($item['_id'], $item['item_id'], $item);
                    $this->render_featured_image($item);
                    $this->render_repeater_item_end();
                }
            }
            echo $area_end;
        }
        echo $hoverEffects_start . $contentarea_start;
        foreach ($post_items as $key => $item) {
            $item_id = $item['item_id'];
            // If there are both wrappers (image-area and content-area), exclude rendering the image,
            // or render everything if the layout is default.
            if ($item_id != 'item_image' && $imagearea_start || !$imagearea_start) {
                $this->render_repeater_item_start($item['_id'], $item['item_id'], $item);
            }
            switch ($item_id) {
                case 'item_image':
                    if (!$imagearea_start) {
                        $this->render_featured_image($item);
                    }
                    break;
                case 'item_title':
                    $this->render_title($item);
                    break;
                case 'item_token':
                    echo wp_kses_post(Helper::get_dynamic_value($item['item_token_code'] ?? ''));
                    break;
                case 'item_addtocart':
                    $this->render_add_to_cart($item);
                    break;
                case 'item_productprice':
                    $this->render_product_price($item);
                    break;
                case 'item_sku':
                    $this->render_product_sku($item);
                    break;
                case 'item_date':
                    $this->render_date($item);
                    break;
                case 'item_author':
                    $this->render_author($item);
                    break;
                case 'item_termstaxonomy':
                    $this->render_terms($item);
                    break;
                case 'item_content':
                    $this->render_content($item);
                    break;
                case 'item_custommeta':
                    $this->render_custom_meta($item);
                    break;
                case 'item_jetengine':
                    $this->render_jetengine($item);
                    break;
                case 'item_metabox':
                    $this->render_metabox($item);
                    break;
                case 'item_readmore':
                    $this->render_read_more($item);
                    break;
                case 'item_posttype':
                    $this->render_post_type($item);
                    break;
            }
            if ($item_id != 'item_image' && $imagearea_start) {
                $this->render_repeater_item_end();
            } elseif (!$imagearea_start) {
                $this->render_repeater_item_end();
            }
        }
        echo $area_end . $hoverEffects_end;
    }
    /**
     * Render Repeater Item - Start
     *
     * @param string $id
     * @param string $item_id
     * @param array<string,mixed> $item_settings
     * @return void
     */
    protected function render_repeater_item_start(string $id, string $item_id, array $item_settings)
    {
        $this->get_parent()->set_render_attribute('dynposts_' . $id, ['class' => ['dce-item', 'dce-' . $item_id, 'elementor-repeater-item-' . $id]]);
        $this->render_responsive_settings($id, $item_settings);
        echo '<div ' . $this->get_parent()->get_render_attribute_string('dynposts_' . $id) . '>';
    }
    /**
     * Render Repeater Item - End
     *
     * @return void
     */
    protected function render_repeater_item_end()
    {
        echo '</div>';
    }
    /**
     * Render Responsive Settings
     *
     * @param string $id
     * @param array<string,mixed> $settings
     * @return void
     */
    protected function render_responsive_settings(string $id, array $settings)
    {
        $active_devices = Helper::get_active_devices_list();
        foreach ($active_devices as $breakpoint_key) {
            /**
             * @var \Elementor\Core\Breakpoints\Breakpoint $breakpoint
             */
            $breakpoint = \Elementor\Plugin::$instance->breakpoints->get_active_breakpoints($breakpoint_key);
            $label = 'desktop' === $breakpoint_key ? esc_html__('Desktop', 'dynamic-content-for-elementor') : $breakpoint->get_label();
            if (!empty($settings['hide_' . $breakpoint_key])) {
                $this->get_parent()->add_render_attribute('dynposts_' . $id, 'class', 'elementor-hidden-' . $breakpoint_key);
            }
        }
    }
    /**
     * Render Featured Image
     *
     * @param array<mixed> $settings
     * @return void
     */
    protected function render_featured_image(array $settings)
    {
        $setting_key = $settings['thumbnail_size_size'];
        $thumbnail_id = get_post_thumbnail_id();
        // Priority: Media Library alt text → caption → post title
        $alt_attribute = \trim(\strip_tags(get_post_meta((int) $thumbnail_id, '_wp_attachment_image_alt', \true)));
        if (empty($alt_attribute)) {
            $alt_attribute = get_the_post_thumbnail_caption();
        }
        if (empty($alt_attribute)) {
            $alt_attribute = (string) get_the_title();
        }
        $alt_attribute = esc_attr($alt_attribute);
        $this->get_parent()->set_render_attribute('featured-image', 'class', 'dce-post-image');
        $image_attr = ['class' => $this->get_image_class(), 'alt' => $alt_attribute];
        $thumbnail_html = wp_get_attachment_image($thumbnail_id, $setting_key, \false, $image_attr);
        // Fallback: use fallback image's own alt text, then post title
        if (empty($thumbnail_html) && !empty($settings['featured_image_fallback'])) {
            $fallback_id = $settings['featured_image_fallback']['id'];
            $fallback_alt = \trim(\strip_tags(get_post_meta($fallback_id, '_wp_attachment_image_alt', \true)));
            if (empty($fallback_alt)) {
                $fallback_alt = (string) get_the_title();
            }
            $thumbnail_html = wp_get_attachment_image($fallback_id, $setting_key, \false, \array_merge($image_attr, ['alt' => esc_attr($fallback_alt)]));
        }
        if (empty($thumbnail_html)) {
            return;
        }
        if (!empty($settings['ratio_image']['size'])) {
            $this->get_parent()->set_render_attribute('figure-featured-image', 'data-image-ratio', $settings['ratio_image']['size']);
        }
        if ($settings['use_bgimage']) {
            // Use Featured Image as background
            $this->get_parent()->add_render_attribute('featured-image', 'class', 'dce-post-bgimage');
        }
        if ($settings['use_overlay']) {
            // Use Overlay
            $this->get_parent()->add_render_attribute('featured-image', 'class', 'dce-post-overlayimage');
        }
        if ($this->get_parent()->get_settings('use_overlay_hover')) {
            // Use Overlay Hover
            $this->get_parent()->add_render_attribute('featured-image', 'class', 'dce-post-overlayhover');
        }
        $html_tag = 'div';
        if (!empty($settings['use_link'])) {
            $html_tag = 'a';
            $this->get_parent()->set_render_attribute('featured-image', 'href', $this->current_permalink);
            if (!empty($settings['open_target_blank'])) {
                $this->get_parent()->set_render_attribute('featured-image', 'target', '_blank');
            }
        }
        echo '<' . $html_tag . ' ' . $this->get_parent()->get_render_attribute_string('featured-image') . '>';
        if ($settings['use_bgimage']) {
            // Image as Background
            $image_url = \false;
            if (get_post_thumbnail_id()) {
                $image_url = Group_Control_Image_Size::get_attachment_image_src((string) get_post_thumbnail_id(), 'thumbnail_size', $settings);
            }
            if (empty($image_url) && !empty($settings['featured_image_fallback'])) {
                $image_url = Group_Control_Image_Size::get_attachment_image_src($settings['featured_image_fallback']['id'], 'thumbnail_size', $settings);
            }
            if (!empty($image_url)) {
                echo '<figure ' . $this->get_parent()->get_render_attribute_string('figure-featured-image') . ' class="dce-img dce-bgimage" style="background-image: url(' . esc_url($image_url) . '); background-repeat: no-repeat; background-size: cover; display: block;"></figure>';
            }
        } else {
            echo '<figure ' . $this->get_parent()->get_render_attribute_string('figure-featured-image') . ' class="dce-img">' . $thumbnail_html . '</figure>';
        }
        echo '</' . $html_tag . '>';
    }
    protected function render_title($settings)
    {
        $html_tag = !empty($settings['html_tag']) ? \DynamicContentForElementor\Helper::validate_html_tag($settings['html_tag']) : 'h3';
        $title_text = get_the_title() ? wp_kses_post(get_the_title()) : get_the_ID();
        $use_link = $settings['use_link'];
        $open_target_blank = $settings['open_target_blank'];
        \printf('<%1$s class="dce-post-title">', $html_tag);
        echo $this->render_item_link_text($title_text, $use_link, $this->current_permalink, $open_target_blank);
        \printf('</%s>', $html_tag);
    }
    protected function render_add_to_cart($settings)
    {
        if ('product' !== get_post_type(get_the_ID()) && 'product_variation' !== get_post_type(get_the_ID())) {
            return;
        }
        $product_id = get_the_ID();
        $product = \wc_get_product($product_id);
        if (!$product) {
            return;
        }
        $cart_url = wc_get_cart_url();
        $attribute_button = 'button_addtocart_' . $this->counter;
        if ($product->is_type('simple') || $product->is_type('course') || $product->is_type('variation')) {
            $button_text = wp_kses_post($settings['add_to_cart_text']);
            $this->get_parent()->add_render_attribute($attribute_button, 'class', ['elementor-button-link', 'elementor-button', 'dce-button']);
            $this->get_parent()->add_render_attribute($attribute_button, 'href', $cart_url . '?add-to-cart=' . $product_id);
            $this->get_parent()->add_render_attribute($attribute_button, 'role', 'button');
            if ('ajax' === $settings['add_to_cart_action']) {
                $this->get_parent()->add_render_attribute($attribute_button, 'class', ['add_to_cart_button', 'ajax_add_to_cart']);
                $this->get_parent()->add_render_attribute($attribute_button, 'data-product_id', (string) $product_id);
                $this->get_parent()->add_render_attribute($attribute_button, 'data-quantity', '1');
            }
            ?>

			<div class="dce-post-button">
				<a <?php 
            echo $this->get_parent()->get_render_attribute_string($attribute_button);
            ?>><?php 
            echo $button_text;
            ?></a>
			</div>

			<?php 
        }
    }
    protected function render_product_price($settings)
    {
        if (\false === get_the_ID()) {
            return;
        }
        if ('product' !== get_post_type(get_the_ID()) && 'product_variation' !== get_post_type(get_the_ID())) {
            return;
        }
        $product = \wc_get_product(get_the_ID());
        if (!$product) {
            return;
        }
        switch ($settings['price_format']) {
            case 'regular':
                echo \wc_price($product->get_regular_price());
                break;
            case 'sale':
                if ($product->is_on_sale()) {
                    echo \wc_price($product->get_sale_price()) . $product->get_price_suffix();
                } else {
                    echo $product->get_price_html();
                }
                break;
            case 'both':
                echo $product->get_price_html();
                break;
        }
    }
    protected function render_product_sku($settings)
    {
        if (\false === get_the_ID()) {
            // phpstan
            return;
        }
        if ('product' !== get_post_type(get_the_ID()) && 'product_variation' !== get_post_type(get_the_ID())) {
            return;
        }
        $product = \wc_get_product(get_the_ID());
        if (!$product) {
            return;
        }
        if ($product->get_sku()) {
            echo esc_html($product->get_sku());
        }
    }
    protected function render_read_more($settings)
    {
        $readmore_text = wp_kses_post($settings['readmore_text']);
        $readmore_size = $settings['readmore_size'];
        $attribute_button = 'button_' . $this->counter;
        $open_target_blank = $settings['open_target_blank'];
        $this->get_parent()->add_render_attribute($attribute_button, 'href', $this->current_permalink);
        $this->get_parent()->add_render_attribute($attribute_button, 'class', ['elementor-button-link', 'elementor-button', 'dce-button']);
        $this->get_parent()->add_render_attribute($attribute_button, 'role', 'button');
        if (!empty($readmore_size)) {
            $this->get_parent()->add_render_attribute($attribute_button, 'class', 'elementor-size-' . $readmore_size);
        }
        ?>
		<div class="dce-post-button">
			<a <?php 
        echo $this->get_parent()->get_render_attribute_string($attribute_button);
        ?> <?php 
        if ($open_target_blank) {
            echo 'target="_blank"';
        }
        ?>>
			<?php 
        echo $readmore_text;
        ?>
			</a>
		</div>
		<?php 
    }
    protected function render_author($settings)
    {
        /**
         * @var array<string> $author_user_key;
         */
        $author_user_key = $settings['author_user_key'];
        $author_user_key = Helper::validate_user_fields($author_user_key);
        if (empty($author_user_key)) {
            return;
        }
        $avatar_image_size = $settings['author_image_size'];
        $avatar_args['size'] = $avatar_image_size;
        $author_id = get_the_author_meta('ID');
        $avatar = get_avatar_url($author_id, $avatar_args);
        ?>
		<div class="dce-post-author">
			<div class="dce-author-image">
				<?php 
        foreach ($author_user_key as $akey => $author_value) {
            if ($author_value == 'avatar') {
                ?>
						<div class="dce-author-avatar">
							<img class="dce-img" src="<?php 
                echo $avatar ? esc_url($avatar) : '';
                ?>" alt="<?php 
                echo esc_attr(get_the_author_meta('display_name'));
                ?>" />
						</div>
					<?php 
            }
        }
        ?>
			</div>
			<div class="dce-author-text">
				<?php 
        foreach ($author_user_key as $akey => $author_value) {
            if ($author_value != 'avatar') {
                echo '<div class="dce-author-' . esc_attr($author_value) . '">' . wp_kses_post(get_the_author_meta($author_value)) . '</div>';
            }
        }
        ?>
			</div>
			<?php 
        echo '</div>';
    }
    protected function render_content($settings)
    {
        $content_type = $settings['content_type'];
        $textcontent_limit = $settings['textcontent_limit'];
        echo '<div class="dce-post-content">';
        if ($content_type === '1') {
            // Content
            if ($textcontent_limit) {
                echo $this->limit_content($textcontent_limit);
            } else {
                echo wp_kses_post(wpautop(get_the_content()));
            }
        } else {
            // Excerpt
            $post = get_post();
            if ($content_type === 'auto-excerpt') {
                // Auto-Excerpt
                echo wp_kses_post(get_the_excerpt($post));
            } else {
                // Manual Excerpt
                echo wp_kses_post($post->post_excerpt);
            }
        }
        echo '</div>';
    }
    /**
     * Render the post type label
     *
     * @param array<string,mixed> $settings Element settings
     * @return void
     */
    protected function render_post_type($settings)
    {
        $type = get_post_type();
        if (!$type) {
            return;
        }
        $post_type_object = get_post_type_object($type);
        if (!$post_type_object) {
            return;
        }
        $post_type = $this->get_post_type_label($post_type_object, $settings['posttype_label']);
        if (!$post_type) {
            return;
        }
        $parent = $this->get_parent();
        $parent->add_render_attribute('post-type', ['class' => 'dce-post-ptype']);
        echo '<div ' . $parent->get_render_attribute_string('post-type') . '>';
        echo esc_html($post_type);
        echo '</div>';
        $parent->remove_render_attribute('post-type');
    }
    /**
     * Get the post type label based on settings
     *
     * @param \WP_Post_Type $post_type_object Post type object
     * @param string $label_type Type of label to get (plural|singular)
     * @return string|null
     */
    private function get_post_type_label($post_type_object, $label_type)
    {
        if ($label_type === 'plural') {
            return $post_type_object->labels->name;
        }
        return $post_type_object->labels->singular_name;
    }
    protected function render_date($settings)
    {
        $date_type = $settings['date_type'];
        $date_format = wp_kses_post($settings['date_format']);
        $icon_enable = $settings['icon_enable'];
        $use_link = $settings['use_link'];
        if (!$date_format) {
            $date_format = get_option('date_format');
        }
        $icon = '';
        if ($icon_enable) {
            $icon = '<i class="dce-post-icon fa fa-calendar" aria-hidden="true"></i> ';
        }
        switch ($date_type) {
            case 'modified':
                $date = get_the_modified_date($date_format, '');
                break;
            case 'publish':
            default:
                $date = get_the_date($date_format, '');
                break;
        }
        ?>
		<div class="dce-post-date"><?php 
        echo $icon . $date;
        ?></div><?php 
    }
    /**
     * Render Custom Meta Field
     *
     * @param array<string,mixed> $settings
     * @return void
     */
    protected function render_custom_meta($settings)
    {
        $this->render_meta_field($settings);
    }
    /**
     * Get Custom Meta HTML
     *
     * Return HTML for custom meta field, based on meta field type like JetEngine, MetaBox
     *
     * @param mixed $meta_value
     * @param array<string,mixed> $settings
     * @param string $type
     * @return void|string
     */
    protected function get_custom_meta_html($meta_value, $settings, string $type)
    {
        if (!$meta_value) {
            return;
        }
        $_id = $settings['_id'];
        $metafield_type = $settings['metafield_type'];
        $link_element = 'a_link_' . $this->counter;
        $attribute_custommeta_item = 'custommeta_item-' . $this->counter;
        $meta_html = '';
        $use_link = \false;
        switch ($metafield_type) {
            case 'date':
                $metafield_date_format_source = $settings['metafield_date_format_source'];
                $metafield_date_format_display = $settings['metafield_date_format_display'];
                // If the source format specifies that the value is a timestamp, convert it directly
                if ('timestamp' === $metafield_date_format_source) {
                    $timestamp = (int) $meta_value;
                } else {
                    $timestamp = \false;
                    // Initialize $timestamp with false to handle failure check
                    // If a source format is specified, attempt conversion with DateTime
                    if ($metafield_date_format_source) {
                        $d = \DateTime::createFromFormat($metafield_date_format_source, $meta_value);
                        if ($d) {
                            $timestamp = $d->getTimestamp();
                        }
                    }
                    // If the DateTime conversion fails or no source format is specified, use strtotime
                    if ($timestamp === \false) {
                        $timestamp = \strtotime($meta_value);
                    }
                }
                // Use date_i18n to format the timestamp according to the desired format
                $meta_html = date_i18n($metafield_date_format_display, $timestamp);
                break;
            case 'image':
                $image_size_key = $settings['image_size_size'];
                $image_html = '';
                // If meta_value is an ID (numeric string or integer), fetch the image
                if (\is_numeric($meta_value)) {
                    $image_html = wp_get_attachment_image($meta_value, $image_size_key, \false, ['class' => 'dce-img']);
                } elseif (\is_string($meta_value)) {
                    $image_html = '<img src="' . esc_url($meta_value) . '" class="dce-img" />';
                } elseif (\is_array($meta_value) && isset($meta_value['ID'])) {
                    $imageSrc = wp_get_attachment_image_src(\intval($meta_value['ID']), $image_size_key);
                    if ($imageSrc) {
                        $image_html = '<img src="' . esc_url($imageSrc[0]) . '" class="dce-img" />';
                    }
                }
                $meta_html = wp_kses_post($image_html);
                break;
            case 'button':
                $use_link = \true;
                $this->get_parent()->set_render_attribute($link_element, 'href', esc_url($meta_value));
                $this->get_parent()->set_render_attribute($link_element, 'role', 'button');
                if (!empty($settings['metafield_url_target'])) {
                    $this->get_parent()->set_render_attribute($link_element, 'target', $settings['metafield_url_target']);
                }
                $this->get_parent()->set_render_attribute($link_element, 'class', ['elementor-button-link', 'elementor-button', 'dce-button']);
                if (!empty($settings['metafield_button_size'])) {
                    $this->get_parent()->add_render_attribute($link_element, 'class', 'elementor-size-' . $settings['metafield_button_size']);
                }
                $meta_html = esc_html($settings['metafield_button_label']);
                break;
            case 'url':
                $use_link = \true;
                $this->get_parent()->set_render_attribute($link_element, 'href', esc_url($meta_value));
                if (!empty($settings['metafield_url_target'])) {
                    $this->get_parent()->set_render_attribute($link_element, 'target', $settings['metafield_url_target']);
                }
                $meta_html = esc_url($meta_value);
                break;
            case 'textarea':
                $meta_html = \nl2br(wp_kses_post(\is_scalar($meta_value) ? (string) $meta_value : ''));
                break;
            case 'text':
                $meta_str = \is_scalar($meta_value) ? (string) $meta_value : '';
                if (!empty($settings['html_tag_item'])) {
                    $html_tag_item = Helper::validate_html_tag($settings['html_tag_item']);
                    $meta_html = '<' . $html_tag_item . '>' . wp_kses_post($meta_str) . '</' . $html_tag_item . '>';
                } else {
                    $meta_html = wp_kses_post($meta_str);
                }
                break;
            default:
                $meta_html = wp_kses_post(\is_scalar($meta_value) ? (string) $meta_value : '');
        }
        $this->get_parent()->add_render_attribute('custom-meta-wrapper', 'class', 'dce-post-' . $type);
        $this->get_parent()->add_render_attribute($attribute_custommeta_item, ['class' => ['dce-meta-item', 'dce-meta-' . $_id, 'dce-meta-' . $metafield_type, 'elementor-repeater-item-' . $settings['_id']]]);
        $html_output = '<div ' . $this->get_parent()->get_render_attribute_string('custom-meta-wrapper') . '>';
        $html_output .= '<div ' . $this->get_parent()->get_render_attribute_string($attribute_custommeta_item) . '>';
        if ($use_link) {
            $html_output .= '<a ' . $this->get_parent()->get_render_attribute_string($link_element) . '>';
        }
        $html_output .= $meta_html;
        if ($use_link) {
            $html_output .= '</a>';
        }
        $html_output .= '</div></div>';
        return $html_output;
    }
    /**
     * Render a JetEngine Field
     *
     * @param array<string,mixed> $settings
     * @return void
     */
    protected function render_jetengine(array $settings)
    {
        $this->render_meta_field($settings);
    }
    /**
     * Render a Metabox Field
     *
     * @param array<string,mixed> $settings
     * @return void
     */
    protected function render_metabox(array $settings)
    {
        $this->render_meta_field($settings);
    }
    /**
     * Unified Meta Field Renderer
     *
     * Handles rendering for WordPress custom meta, JetEngine, and MetaBox fields
     *
     * @param array<string,mixed> $settings
     * @return void
     */
    protected function render_meta_field($settings)
    {
        if (!empty($settings['metafield_key'])) {
            $field_key = $settings['metafield_key'];
            $meta_value = get_post_meta($this->current_id, $field_key, \true);
            $type = 'custommeta';
        } elseif (!empty($settings['jetengine_key'])) {
            if (!Helper::is_plugin_active('jet-engine')) {
                return;
            }
            $field_key = $settings['jetengine_key'];
            $meta_value = jet_engine()->listings->data->get_meta($field_key);
            $type = 'jetengine';
        } elseif (!empty($settings['metabox_key'])) {
            if (!Helper::is_plugin_active('metabox')) {
                return;
            }
            $field_key = $settings['metabox_key'];
            $meta_value = rwmb_get_value($field_key);
            $type = 'metabox';
        } else {
            return;
        }
        echo $this->get_custom_meta_html($meta_value, $settings, $type);
    }
    /**
     * Render the terms list for the current post
     *
     * @param array<string,mixed> $settings Element settings
     * @return void
     */
    protected function render_terms($settings)
    {
        // Extract settings
        $taxonomy_filter = $settings['taxonomy_filter'] ?? [];
        $separator = wp_kses_post($settings['separator_chart']);
        $only_parent_terms = $settings['only_parent_terms'];
        $use_link = $settings['use_link'];
        $open_target_blank = $settings['open_target_blank'];
        $show_icon = $settings['icon_enable'];
        // Get post taxonomies
        $taxonomies = get_post_taxonomies($this->current_id);
        if (empty($taxonomies)) {
            return;
        }
        echo '<div class="dce-post-terms">';
        foreach ($taxonomies as $taxonomy) {
            // Skip if not in filter or is post format
            if (!$this->should_render_taxonomy($taxonomy, $taxonomy_filter)) {
                continue;
            }
            $terms = Helper::get_the_terms_ordered($this->current_id, $taxonomy);
            if (!\is_array($terms) || empty($terms)) {
                continue;
            }
            $this->render_taxonomy_terms($taxonomy, $terms, $only_parent_terms, $show_icon, $separator, $use_link, $open_target_blank);
        }
        echo '</div>';
    }
    /**
     * Check if taxonomy should be rendered
     *
     * @param string $taxonomy Taxonomy name
     * @param array<string> $taxonomy_filter Filtered taxonomies
     * @return bool
     */
    private function should_render_taxonomy($taxonomy, $taxonomy_filter)
    {
        if ($taxonomy === 'post_format') {
            return \false;
        }
        if (empty($taxonomy_filter)) {
            return \true;
        }
        return \in_array($taxonomy, $taxonomy_filter);
    }
    /**
     * Render terms for a specific taxonomy
     *
     * @param string $taxonomy Taxonomy name
     * @param array<\WP_Term> $terms Terms to render
     * @param string $only_parent_terms Filter for parent/child terms
     * @param bool $show_icon Whether to show taxonomy icon
     * @param string $separator Terms separator
     * @param bool $use_link Whether to link terms
     * @param bool $open_target_blank Whether to open links in new tab
     * @return void
     */
    private function render_taxonomy_terms($taxonomy, $terms, $only_parent_terms, $show_icon, $separator, $use_link, $open_target_blank)
    {
        echo '<ul class="dce-terms-list dce-taxonomy-' . esc_attr($taxonomy) . '">';
        // Show taxonomy icon
        if ($show_icon) {
            $this->render_taxonomy_icon($taxonomy);
        }
        $is_first = \true;
        foreach ($terms as $term) {
            if (!$this->should_render_term($term, $only_parent_terms)) {
                continue;
            }
            $this->render_term($term, $use_link, $open_target_blank, $separator, $is_first);
            $is_first = \false;
        }
        echo '</ul>';
    }
    /**
     * Check if term should be rendered based on parent/child filter
     *
     * @param \WP_Term $term Term object
     * @param string $only_parent_terms Parent terms filter setting
     * @return bool
     */
    private function should_render_term($term, $only_parent_terms)
    {
        if (empty($only_parent_terms)) {
            return \true;
        }
        if ($only_parent_terms === 'yes' && $term->parent) {
            return \false;
        }
        if ($only_parent_terms === 'children' && !$term->parent) {
            return \false;
        }
        return \true;
    }
    /**
     * Render taxonomy icon
     *
     * @param string $taxonomy Taxonomy name
     * @return void
     */
    private function render_taxonomy_icon($taxonomy)
    {
        $icon = is_taxonomy_hierarchical($taxonomy) ? '<i class="dce-post-icon fa fa-folder-open" aria-hidden="true"></i> ' : '<i class="dce-post-icon fa fa-tags" aria-hidden="true"></i> ';
        echo wp_kses_post($icon);
    }
    /**
     * Render single term
     *
     * @param \WP_Term $term Term object
     * @param bool $use_link Whether to link term
     * @param bool $open_target_blank Whether to open link in new tab
     * @param string $separator Terms separator
     * @param bool $is_first Whether this is the first term
     * @return void
     */
    private function render_term($term, $use_link, $open_target_blank, $separator, $is_first)
    {
        $term_link = get_term_link($term);
        $term_url = is_wp_error($term_link) ? '' : trailingslashit($term_link);
        $this->get_parent()->add_render_attribute('term-item', ['class' => 'dce-term-item']);
        $this->get_parent()->add_render_attribute('term-wrapper', ['class' => ['dce-term', 'dce-term-' . $term->term_id], 'data-dce-order' => get_term_meta($term->term_id, 'order', \true)]);
        // Add separator if not first item
        $divider = !$is_first ? \sprintf('<span class="dce-separator">%s</span>', $separator) : '';
        echo '<li ' . $this->get_parent()->get_render_attribute_string('term-item') . '>';
        echo $divider . '<span ' . $this->get_parent()->get_render_attribute_string('term-wrapper') . '>';
        echo $this->render_item_link_text($term->name, $use_link, $term_url, $open_target_blank);
        echo '</span></li>';
        $this->get_parent()->remove_render_attribute('term-item');
        $this->get_parent()->remove_render_attribute('term-wrapper');
    }
    protected function render_item_link_text($link_text = '', $use_link = '', $url = '', $open_target_blank = '')
    {
        $url = esc_url($url);
        if (!empty($use_link) && $url && $link_text) {
            $open_target_blank = !empty($open_target_blank) ? ' target="_blank"' : '';
            return '<a href="' . $url . '"' . $open_target_blank . '>' . wp_kses_post($link_text) . '</a>';
        } else {
            return $link_text ? wp_kses_post($link_text) : '';
        }
    }
    /**
     * Render Post - Start
     *
     * @return void
     */
    protected function render_post_start()
    {
        $this->get_parent()->set_render_attribute('post', ['class' => get_post_class()]);
        $this->get_parent()->add_render_attribute('post', 'class', 'dce-post');
        $this->get_parent()->add_render_attribute('post', 'class', 'dce-post-item');
        $this->get_parent()->add_render_attribute('post', 'class', $this->get_item_class());
        $this->get_parent()->set_render_attribute('post', 'data-dce-post-id', $this->current_id);
        $this->get_parent()->set_render_attribute('post', 'data-dce-post-index', $this->counter);
        // Template Linkable
        if ($this->get_parent()->get_settings('templatemode_linkable') && !empty(get_permalink($this->current_id))) {
            $this->get_parent()->set_render_attribute('post', 'data-post-link', get_permalink($this->current_id));
        }
        $this->get_parent()->set_render_attribute('post-block', 'class', 'dce-post-block');
        // Hover Animation
        if (!empty($this->get_parent()->get_settings('hover_animation'))) {
            $this->get_parent()->add_render_attribute('post-block', 'class', 'elementor-animation-' . $this->get_parent()->get_settings('hover_animation'));
        }
        // Hover Effect
        if (!empty($this->get_parent()->get_settings('hover_text_effect')) && !empty($this->get_parent()->get_settings('style_items')) && 'float' === $this->get_parent()->get_settings('style_items')) {
            $this->get_parent()->add_render_attribute('post-block', 'class', 'dce-hover-effects');
        }
        // Get wrapper tag from settings (default to article)
        $wrapper_tag = $this->get_parent()->get_settings('post_wrapper_tag');
        if (empty($wrapper_tag)) {
            $wrapper_tag = 'article';
        }
        $wrapper_tag = Helper::validate_html_tag($wrapper_tag);
        ?>

		<<?php 
        echo $wrapper_tag;
        ?> <?php 
        echo $this->get_parent()->get_render_attribute_string('post');
        ?>>
			<div <?php 
        echo $this->get_parent()->get_render_attribute_string('post-block');
        ?>>
		<?php 
    }
    /**
     * Render Post - End
     *
     * @return void
     */
    protected function render_post_end()
    {
        // Get wrapper tag from settings (default to article)
        $wrapper_tag = $this->get_parent()->get_settings('post_wrapper_tag');
        if (empty($wrapper_tag)) {
            $wrapper_tag = 'article';
        }
        $wrapper_tag = Helper::validate_html_tag($wrapper_tag);
        ?>
			</div>
		</<?php 
        echo $wrapper_tag;
        ?>>
		<?php 
    }
    /**
     * Render Fallback
     *
     * @return void
     */
    protected function render_fallback()
    {
        $fallback_type = $this->get_parent()->get_settings_for_display('fallback_type');
        $fallback_text = $this->get_parent()->get_settings_for_display('fallback_text');
        $fallback_template = $this->get_parent()->get_settings_for_display('fallback_template');
        $this->get_parent()->add_render_attribute('container', ['class' => ['dce-posts-container', 'dce-posts', $this->get_scrollreveal_class()]]);
        $this->get_parent()->add_render_attribute('container_wrap', ['class' => ['dce-posts-fallback']]);
        ?>
		<div <?php 
        echo $this->get_parent()->get_render_attribute_string('container');
        ?>>
			<div <?php 
        echo $this->get_parent()->get_render_attribute_string('container_wrap');
        ?>>
				<?php 
        if (isset($fallback_type) && $fallback_type === 'template') {
            $template_system = \DynamicContentForElementor\Plugin::instance()->template_system;
            $fallback_content = $template_system->build_elementor_template_special(['id' => $fallback_template]);
        } else {
            // Intentionally left unescaped:
            $fallback_content = '<p>' . $fallback_text . '</p>';
        }
        echo $fallback_content;
        ?>
			</div>
		</div>
		<?php 
    }
    /**
     * Add Direction
     *
     * @return void
     */
    protected function add_direction(string $attribute = 'container')
    {
        if ($this->get_parent()->get_settings_for_display('rtl')) {
            $this->get_parent()->add_render_attribute($attribute, ['dir' => ['rtl']]);
        }
    }
    /**
     * Render Loop Start
     *
     * @return void
     */
    protected function render_loop_start()
    {
        if (!$this->parent) {
            throw new \Exception('Parent not found');
        }
        $this->get_parent()->add_render_attribute('container', ['class' => ['dce-posts-container', 'dce-posts', 'dce-dynamic-posts-collection', $this->get_scrollreveal_class(), $this->get_container_class()]]);
        $this->get_parent()->add_render_attribute('container_wrap', ['class' => ['dce-posts-wrapper', $this->get_wrapper_class()]]);
        $this->maybe_render_pagination_top();
        ?>

		<div <?php 
        echo $this->get_parent()->get_render_attribute_string('container');
        ?>>
			<?php 
        $this->render_posts_before();
        ?>
			<div <?php 
        echo $this->get_parent()->get_render_attribute_string('container_wrap');
        ?>>
			<?php 
        $this->render_posts_wrapper_before();
    }
    /**
     * Render Top Pagination
     *
     * @return void
     */
    protected function maybe_render_pagination_top()
    {
        $settings = $this->get_parent()->get_settings_for_display();
        $p_query = $this->get_parent()->get_query();
        $rtl = $this->get_parent()->get_settings_for_display('rtl');
        if ($settings['pagination_enable'] && ('top' === $settings['pagination_position'] || 'both' === $settings['pagination_position'])) {
            $this->render_pagination($p_query->max_num_pages, $settings, 'dce-pagination-top', (bool) $rtl);
        }
    }
    /**
     * @param int|string $pages
     * @param array<mixed> $settings
     * @param string $class
     * @param bool $rtl
     * @return void
     */
    public function render_pagination($pages, $settings, $class = '', $rtl = \false)
    {
        $search_filter_query = \false;
        if ('search_filter' === ($settings['query_type'] ?? \false)) {
            $search_filter_query = \true;
        }
        $icons = $this->get_navigation_icons($settings);
        $parent = $this->get_parent();
        $range = (int) $settings['pagination_range'] - 1;
        $show_items = $range * 2 + 1;
        $paged = \max(1, get_query_var('paged'), get_query_var('page'));
        if (empty($paged)) {
            $paged = 1;
        }
        if ($pages == '') {
            global $wp_query;
            $pages = $wp_query->max_num_pages;
            if (!$pages) {
                $pages = 1;
            }
        }
        if ($pages === 1) {
            return;
        }
        $parent->add_render_attribute('pagination-container', ['class' => ['dce-pagination', $class], 'dir' => $rtl ? 'rtl' : null]);
        echo '<div ' . $parent->get_render_attribute_string('pagination-container') . '>';
        $this->maybe_render_progression($parent, $paged, $pages, $settings);
        $this->maybe_render_first_link($parent, $paged, $pages, $settings, $search_filter_query, $range, $show_items, $icons);
        $this->maybe_render_prev_link($parent, $paged, $pages, $settings, $search_filter_query, $show_items, $icons);
        $this->maybe_render_numbers($parent, $paged, $pages, $settings, $search_filter_query, $range, $show_items);
        $this->maybe_render_next_link($parent, $paged, $pages, $settings, $search_filter_query, $show_items, $icons);
        $this->maybe_render_last_link($parent, $paged, $pages, $settings, $search_filter_query, $range, $show_items, $icons);
        echo '</div>';
        $parent->remove_render_attribute('pagination-container');
    }
    /**
     * Render the pagination progression
     *
     * @param \Elementor\Element_Base $parent The parent element
     * @param int $paged Current page number
     * @param int $pages Total number of pages
     * @param array<string,mixed> $settings Element settings
     * @return void
     */
    protected function maybe_render_progression($parent, $paged, $pages, $settings)
    {
        if (!$settings['pagination_show_progression']) {
            return;
        }
        $parent->add_render_attribute('progression', 'class', 'progression');
        echo '<span ' . $parent->get_render_attribute_string('progression') . '>';
        echo $paged . ' / ' . $pages;
        echo '</span>';
    }
    /**
     * Render the first page link
     *
     * @param \Elementor\Element_Base $parent The parent element
     * @param int $paged Current page number
     * @param int $pages Total number of pages
     * @param array<string,mixed> $settings Element settings
     * @param bool $search_filter_query Whether using search filter
     * @param int $range Pagination range
     * @param int $show_items Number of items to show
     * @param array<string,string> $icons Navigation icons
     * @return void
     */
    protected function maybe_render_first_link($parent, $paged, $pages, $settings, $search_filter_query, $range, $show_items, $icons)
    {
        if (!$settings['pagination_show_firstlast']) {
            return;
        }
        // If there are fewer pages than items to show, no need for "first" link
        if ($show_items >= $pages) {
            return;
        }
        // If we are in the first few pages, no need for "first" link
        if ($paged <= \max(2, $range + 1)) {
            return;
        }
        $link = $search_filter_query ? $this->get_wp_link_page_sf(1) : $this->get_wp_link_page(1);
        $parent->add_render_attribute('link-first', ['href' => $link, 'class' => 'pagefirst', 'rel' => 'prev']);
        if (!empty($icons['firstlast_left'])) {
            $parent->add_render_attribute('link-first', 'class', 'icon left');
        }
        echo '<a ' . $parent->get_render_attribute_string('link-first') . '>';
        if (!empty($icons['firstlast_left'])) {
            echo $icons['firstlast_left'] . ' ';
        }
        echo wp_kses_post($settings['pagination_first_label']) . '</a>';
        $parent->remove_render_attribute('link-first');
    }
    /**
     * Render the previous page link
     *
     * @param \Elementor\Element_Base $parent The parent element
     * @param int $paged Current page number
     * @param int $pages Total number of pages
     * @param array<string,mixed> $settings Element settings
     * @param bool $search_filter_query Whether using search filter
     * @param int $show_items Number of items to show
     * @param array<string,string> $icons Navigation icons
     * @return void
     */
    protected function maybe_render_prev_link($parent, $paged, $pages, $settings, $search_filter_query, $show_items, $icons)
    {
        if (!$settings['pagination_show_prevnext']) {
            return;
        }
        if ($paged <= 1 || $show_items >= $pages) {
            return;
        }
        $link = $search_filter_query ? $this->get_wp_link_page_sf($paged - 1) : $this->get_wp_link_page($paged - 1);
        $parent->add_render_attribute('link-prev', ['href' => $link, 'rel' => 'prev']);
        if (!empty($icons['prevnext_left'])) {
            $parent->add_render_attribute('link-prev', 'class', 'icon left');
        }
        echo '<a ' . $parent->get_render_attribute_string('link-prev') . '>';
        if (!empty($icons['prevnext_left'])) {
            echo $icons['prevnext_left'];
        }
        echo wp_kses_post($settings['pagination_prev_label']) . '</a>';
        $parent->remove_render_attribute('link-prev');
    }
    /**
     * Render the page numbers
     *
     * @param \Elementor\Element_Base $parent The parent element
     * @param int $paged Current page number
     * @param int $pages Total number of pages
     * @param array<string,mixed> $settings Element settings
     * @param bool $search_filter_query Whether using search filter
     * @param int $range Pagination range
     * @param int $show_items Number of items to show
     * @return void
     */
    protected function maybe_render_numbers($parent, $paged, $pages, $settings, $search_filter_query, $range, $show_items)
    {
        if (!$settings['pagination_show_numbers']) {
            return;
        }
        $this->maybe_render_first_page_number($parent, $paged, $pages, $settings, $search_filter_query, $range);
        $this->maybe_render_number_range($parent, $paged, $pages, $settings, $search_filter_query, $range, $show_items);
        $this->maybe_render_last_page_number($parent, $paged, $pages, $settings, $search_filter_query, $range);
    }
    /**
     * Render the first page number in the range
     *
     * @param \Elementor\Element_Base $parent The parent element
     * @param int $paged Current page number
     * @param int $pages Total number of pages
     * @param array<string,mixed> $settings Element settings
     * @param bool $search_filter_query Whether using search filter
     * @param int $range Pagination range
     * @return void
     */
    protected function maybe_render_first_page_number($parent, $paged, $pages, $settings, $search_filter_query, $range)
    {
        if (!$settings['pagination_show_first_last_pages'] || $paged <= 1 || $paged <= $range + 1) {
            return;
        }
        $link = $search_filter_query ? $this->get_wp_link_page_sf(1) : $this->get_wp_link_page(1);
        $parent->add_render_attribute('first-page-link', ['href' => $link, 'class' => 'inactive', 'rel' => 'prev']);
        echo '<a ' . $parent->get_render_attribute_string('first-page-link') . '>1</a>';
        $parent->remove_render_attribute('first-page-link');
        if ($paged > $range + 2) {
            echo '<span class="pagination-ellipsis">...</span>';
        }
    }
    /**
     * Render the range of page numbers
     *
     * @param \Elementor\Element_Base $parent The parent element
     * @param int $paged Current page number
     * @param int $pages Total number of pages
     * @param array<string,mixed> $settings Element settings
     * @param bool $search_filter_query Whether using search filter
     * @param int $range Pagination range
     * @param int $show_items Number of items to show
     * @return void
     */
    protected function maybe_render_number_range($parent, $paged, $pages, $settings, $search_filter_query, $range, $show_items)
    {
        for ($i = 1; $i <= $pages; $i++) {
            if ($pages === 1) {
                continue;
            }
            $is_in_range = !($i >= $paged + $range + 1 || $i <= $paged - $range - 1);
            $show_all_numbers = $pages <= $show_items;
            if ($is_in_range || $show_all_numbers) {
                $link = $search_filter_query ? $this->get_wp_link_page_sf($i) : $this->get_wp_link_page($i);
                if ($link === null) {
                    //phpstan
                    continue;
                }
                if ($paged == $i) {
                    $this->render_current_page_number($parent, $i);
                } else {
                    $this->render_page_number_link($parent, $i, $paged, $link);
                }
            }
            $parent->remove_render_attribute("page-{$i}");
            $parent->remove_render_attribute("page-link-{$i}");
        }
    }
    /**
     * Render the current page number
     *
     * @param \Elementor\Element_Base $parent The parent element
     * @param int $page_number The page number to render
     * @return void
     */
    private function render_current_page_number($parent, $page_number)
    {
        $parent->add_render_attribute("page-{$page_number}", ['class' => 'current', 'aria-current' => 'page']);
        echo '<span ' . $parent->get_render_attribute_string("page-{$page_number}") . '>' . $page_number . '</span>';
    }
    /**
     * Render a link to a specific page number
     *
     * @param \Elementor\Element_Base $parent The parent element
     * @param int $page_number The page number to link to
     * @param int $current_page Current page number
     * @param string $link The URL for the page
     * @return void
     */
    private function render_page_number_link($parent, $page_number, $current_page, $link)
    {
        $rel = '';
        if ($page_number < $current_page) {
            $rel = 'prev';
        } elseif ($page_number > $current_page) {
            $rel = 'next';
        }
        $parent->add_render_attribute("page-link-{$page_number}", ['href' => $link, 'class' => 'inactive']);
        if ($rel) {
            $parent->add_render_attribute("page-link-{$page_number}", 'rel', $rel);
        }
        echo '<a ' . $parent->get_render_attribute_string("page-link-{$page_number}") . '>' . $page_number . '</a>';
    }
    /**
     * Render the last page number in the range
     *
     * @param \Elementor\Element_Base $parent The parent element
     * @param int $paged Current page number
     * @param int $pages Total number of pages
     * @param array<string,mixed> $settings Element settings
     * @param bool $search_filter_query Whether using search filter
     * @param int $range Pagination range
     * @return void
     */
    protected function maybe_render_last_page_number($parent, $paged, $pages, $settings, $search_filter_query, $range)
    {
        if (!$settings['pagination_show_first_last_pages'] || $paged >= $pages || $pages <= $range + 1) {
            return;
        }
        // Avoid duplicating the last page number when it's already visible in the current range
        if ($paged + $range >= $pages) {
            return;
        }
        if ($paged + $range < $pages - 1) {
            echo '<span class="pagination-ellipsis">...</span>';
        }
        $link = $search_filter_query ? $this->get_wp_link_page_sf($pages) : $this->get_wp_link_page($pages);
        $parent->add_render_attribute('last-page-link', ['href' => $link, 'class' => 'inactive', 'rel' => 'next']);
        echo '<a ' . $parent->get_render_attribute_string('last-page-link') . '>' . $pages . '</a>';
        $parent->remove_render_attribute('last-page-link');
    }
    /**
     * Render the next page link
     *
     * @param \Elementor\Element_Base $parent The parent element
     * @param int $paged Current page number
     * @param int $pages Total number of pages
     * @param array<string,mixed> $settings Element settings
     * @param bool $search_filter_query Whether using search filter
     * @param int $show_items Number of items to show
     * @param array<string,string> $icons Navigation icons
     * @return void
     */
    protected function maybe_render_next_link($parent, $paged, $pages, $settings, $search_filter_query, $show_items, $icons)
    {
        if (!$settings['pagination_show_prevnext']) {
            return;
        }
        if ($paged >= $pages || $show_items >= $pages) {
            return;
        }
        $link = $search_filter_query ? $this->get_wp_link_page_sf($paged + 1) : $this->get_wp_link_page($paged + 1);
        $parent->add_render_attribute('next-page-link', ['href' => $link, 'rel' => 'next']);
        if (!empty($icons['prevnext_right'])) {
            $parent->add_render_attribute('next-page-link', 'class', 'icon right');
        }
        echo '<a ' . $parent->get_render_attribute_string('next-page-link') . '>';
        echo wp_kses_post($settings['pagination_next_label']);
        if (!empty($icons['prevnext_right'])) {
            echo ' ' . $icons['prevnext_right'];
        }
        echo '</a>';
        $parent->remove_render_attribute('next-page-link');
    }
    /**
     * Render the last page link
     *
     * @param \Elementor\Element_Base $parent The parent element
     * @param int $paged Current page number
     * @param int $pages Total number of pages
     * @param array<string,mixed> $settings Element settings
     * @param bool $search_filter_query Whether using search filter
     * @param int $range Pagination range
     * @param int $show_items Number of items to show
     * @param array<string,string> $icons Navigation icons
     * @return void
     */
    protected function maybe_render_last_link($parent, $paged, $pages, $settings, $search_filter_query, $range, $show_items, $icons)
    {
        if (!$settings['pagination_show_firstlast']) {
            return;
        }
        if ($paged >= $pages - 1 || $paged + $range - 1 >= $pages || $show_items >= $pages) {
            return;
        }
        $link = $search_filter_query ? $this->get_wp_link_page_sf($pages) : $this->get_wp_link_page($pages);
        $parent->add_render_attribute('last-page-link', ['href' => $link, 'class' => 'pagelast', 'rel' => 'next']);
        if (!empty($icons['firstlast_right'])) {
            $parent->add_render_attribute('last-page-link', 'class', 'icon right');
        }
        echo '<a ' . $parent->get_render_attribute_string('last-page-link') . '>';
        echo wp_kses_post($settings['pagination_last_label']);
        if (!empty($icons['firstlast_right'])) {
            echo ' ' . $icons['firstlast_right'];
        }
        echo '</a>';
        $parent->remove_render_attribute('last-page-link');
    }
    /**
     * @param array<string,mixed> $settings
     * @return array<string,string>
     */
    private function get_navigation_icons($settings)
    {
        // Next
        $icon_prevnext_right = $this->get_navigation_icon_html($settings['selected_pagination_icon_prevnext'], 'right');
        // Previous
        $icon_prevnext_left = $this->get_left_navigation_icon_html($settings['selected_pagination_icon_prevnext'], !empty($settings['pagination_icon_prev_mirror_next']), !empty($settings['pagination_icon_prev_mirror_next']) ? null : $settings['selected_pagination_icon_prevnext_previous']);
        // Last
        $icon_firstlast_right = $this->get_navigation_icon_html($settings['selected_pagination_icon_firstlast'], 'right');
        // First
        $icon_firstlast_left = $this->get_left_navigation_icon_html($settings['selected_pagination_icon_firstlast'], !empty($settings['pagination_icon_first_mirror_last']), !empty($settings['pagination_icon_first_mirror_last']) ? null : $settings['selected_pagination_icon_firstlast_first']);
        return ['prevnext_left' => $icon_prevnext_left, 'prevnext_right' => $icon_prevnext_right, 'firstlast_left' => $icon_firstlast_left, 'firstlast_right' => $icon_firstlast_right];
    }
    /**
     * @param array<string,mixed> $icon_settings
     * @param string $direction
     * @return string
     */
    private function get_navigation_icon_html($icon_settings, $direction = 'right')
    {
        if ($direction === 'left') {
            $icon_settings['value'] = \str_replace('right', 'left', $icon_settings['value'] ?? '');
        }
        \ob_start();
        \Elementor\Icons_Manager::render_icon($icon_settings, ['aria-hidden' => 'true']);
        return \ob_get_clean();
    }
    /**
     * @param array<string,mixed> $icon_settings
     * @param bool $mirror
     * @param array|null $alternate_icon_settings
     * @return string
     */
    private function get_left_navigation_icon_html($icon_settings, $mirror = \false, $alternate_icon_settings = null)
    {
        if ($mirror) {
            return $this->get_navigation_icon_html($icon_settings, 'left');
        } elseif ($alternate_icon_settings !== null) {
            return $this->get_navigation_icon_html($alternate_icon_settings, 'left');
        }
        return $this->get_navigation_icon_html($icon_settings, 'left');
    }
    /**
     * @param int|string $i
     * @return string
     */
    protected function get_wp_link_page_sf($i)
    {
        return get_pagenum_link(\intval($i));
    }
    /**
     * @param int|string $i
     * @return string|null
     */
    protected function get_wp_link_page($i)
    {
        $page_number = (int) $i;
        // For archives, categories, search results etc.
        if (!is_singular() || is_front_page()) {
            return get_pagenum_link($page_number);
        }
        // Get current post ID
        $post_id = get_queried_object_id();
        if (!$post_id) {
            return '';
        }
        $post = get_post($post_id);
        if (!$post instanceof \WP_Post) {
            return '';
        }
        global $wp_rewrite;
        $post_link = get_permalink($post);
        if (!$post_link) {
            return '';
        }
        // Handle different permalink structures
        if ($page_number > 1) {
            if ('' === $wp_rewrite->permalink_structure || \in_array($post->post_status, ['draft', 'pending'])) {
                $post_link = add_query_arg('page', $page_number, $post_link);
            } else {
                $post_link = trailingslashit($post_link) . user_trailingslashit((string) $page_number, 'single_paged');
            }
        }
        // Preserve existing query parameters
        if (!empty($_GET)) {
            foreach ($_GET as $key => $value) {
                if ($key === 'page') {
                    continue;
                    // Skip 'page' parameter as it's already handled
                }
                $sanitized_value = sanitize_text_field(wp_unslash($value));
                $post_link = add_query_arg(sanitize_key($key), $sanitized_value, $post_link);
            }
        }
        return $post_link;
    }
    /**
     * Render Bottom Pagination
     *
     * @return void
     */
    protected function maybe_render_pagination_bottom()
    {
        $settings = $this->get_parent()->get_settings_for_display();
        $p_query = $this->get_parent()->get_query();
        $rtl = $this->get_parent()->get_settings_for_display('rtl');
        if ($settings['pagination_enable'] && ('bottom' === $settings['pagination_position'] || 'both' === $settings['pagination_position'])) {
            $this->render_pagination($p_query->max_num_pages, $settings, 'dce-pagination-bottom', (bool) $rtl);
        }
    }
    /**
     * Render Loop End
     *
     * @return void
     */
    protected function render_loop_end()
    {
        $this->render_posts_wrapper_after();
        ?>
			</div>
			<?php 
        $this->render_posts_after();
        ?>
		</div>
		<?php 
        $this->maybe_render_pagination_bottom();
        $this->render_infinite_scroll();
    }
    protected function render_posts_before()
    {
    }
    protected function render_posts_after()
    {
    }
    protected function render_posts_wrapper_before()
    {
    }
    protected function render_posts_wrapper_after()
    {
    }
    /**
     * @return string
     */
    public function get_container_class()
    {
        return 'dce-skin-' . $this->get_id();
    }
    /**
     * @return string
     */
    public function get_wrapper_class()
    {
        return 'dce-wrapper-' . $this->get_id();
    }
    /**
     * @return string
     */
    public function get_item_class()
    {
        return 'dce-item-' . $this->get_id();
    }
    public function get_image_class()
    {
    }
    /**
     * @return string
     */
    public function get_scrollreveal_class()
    {
        return '';
    }
    /**
     * @return string
     */
    public function filter_excerpt_length()
    {
        return $this->get_instance_value('textcontent_limit');
    }
    /**
     * @return string
     */
    public function filter_excerpt_more($more)
    {
        return '';
    }
    /**
     * @return string
     */
    protected function limit_content($limit)
    {
        $post = get_post();
        if (!$post) {
            return '';
        }
        $content = $post->post_content;
        $charset = get_bloginfo('charset');
        $content = \mb_substr(wp_strip_all_tags($content), 0, $limit, $charset) . '&hellip;';
        return wp_kses_post($content);
    }
    /**
     * Check if infinite scroll should be rendered
     *
     * @param array<string,mixed> $settings Element settings
     * @param int $post_length Number of posts in current query
     * @param int $posts_per_page Posts per page setting
     * @return bool
     */
    protected function should_render_infinite_scroll($settings, $post_length, $posts_per_page)
    {
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            return \true;
        }
        if (!$settings['infiniteScroll_enable']) {
            return \false;
        }
        if ($settings['query_type'] === 'search_filter') {
            return $post_length >= $posts_per_page;
        }
        return $post_length >= $settings['num_posts'] && $settings['num_posts'] >= 0;
    }
    /**
     * @return void
     */
    protected function render_infinite_scroll()
    {
        $settings = $this->get_parent()->get_settings_for_display();
        $p_query = $this->get_parent()->get_query();
        $post_length = $p_query->post_count;
        $posts_per_page = $p_query->query_vars['posts_per_page'];
        if (!$this->should_render_infinite_scroll($settings, $post_length, $posts_per_page)) {
            return;
        }
        $preview_mode = '';
        if ($settings['infiniteScroll_enable_status']) {
            ?>
			<nav class="infiniteScroll">
				<div class="page-load-status<?php 
            echo $preview_mode;
            ?>">

					<?php 
            if ($settings['infiniteScroll_loading_type'] == 'text') {
                ?>
						<div class="infinite-scroll-request status-text"><?php 
                echo wp_kses_post($settings['infiniteScroll_label_loading']);
                ?></div>
						<?php 
            } elseif ($settings['infiniteScroll_loading_type'] == 'ellips') {
                ?>
						<div class="loader-ellips infinite-scroll-request">
							<span class="loader-ellips__dot"></span>
							<span class="loader-ellips__dot"></span>
							<span class="loader-ellips__dot"></span>
							<span class="loader-ellips__dot"></span>
						</div>
						<?php 
            }
            ?>
					<div class="infinite-scroll-last status-text"><?php 
            echo wp_kses_post($settings['infiniteScroll_label_last']);
            ?></div>
					<div class="infinite-scroll-error status-text"><?php 
            echo wp_kses_post($settings['infiniteScroll_label_error']);
            ?></div>

					<div class="pagination" role="navigation">
						<?php 
            if ($settings['query_type'] != 'search_filter') {
                ?>
						<a class="pagination__next" href="<?php 
                echo esc_url(\DynamicContentForElementor\Helper::get_next_pagination());
                ?>"></a>
						<?php 
            } else {
                ?>
						<a class="pagination__next" href="<?php 
                echo esc_url(\DynamicContentForElementor\Helper::get_next_pagination_sf());
                ?>"></a>
						<?php 
            }
            ?>
					</div>
				</div>


			</nav>
			<?php 
        }
        // Infinite Scroll - Button
        if ($settings['infiniteScroll_trigger'] == 'button') {
            ?>
			<div class="infiniteScroll">
				<button class="view-more-button"><?php 
            echo wp_kses_post($settings['infiniteScroll_label_button']);
            ?></button>
			</div>
			<?php 
        }
    }
}
