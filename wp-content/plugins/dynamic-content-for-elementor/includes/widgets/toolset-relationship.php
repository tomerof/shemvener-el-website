<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Plugin;
// Exit if accessed directly
if (!\defined('ABSPATH')) {
    exit;
}
class ToolsetRelationship extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_style_depends()
    {
        return ['dce-relationship'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $rels = Helper::get_toolset_relationship_fields();
        $this->start_controls_section('section_content', ['label' => $this->get_title()]);
        $this->add_control('toolset_relation_field', ['label' => esc_html__('Toolset Relationship Field', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'groups' => $rels, 'default' => '0']);
        $this->add_control('query_role', ['label' => esc_html__('Query role', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'parent', 'options' => ['parent' => esc_html__('Parent', 'dynamic-content-for-elementor'), 'child' => esc_html__('Child', 'dynamic-content-for-elementor')]]);
        $this->add_control('toolset_relation_render', ['label' => esc_html__('Render mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['title' => ['title' => esc_html__('Title', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-list'], 'text' => ['title' => esc_html__('Dynamic HTML', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'template' => ['title' => esc_html__('Template', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-th-large']], 'toggle' => \false, 'default' => 'title', 'separator' => 'before']);
        $this->add_control('toolset_relation_template', ['label' => esc_html__('Select Template', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Template Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'object_type' => 'elementor_library', 'condition' => ['toolset_relation_render' => 'template']]);
        Plugin::instance()->text_templates->maybe_add_notice($this, '', ['toolset_relation_render' => 'text']);
        $read_more = esc_html__('Read more', 'dynamic-content-for-elementor');
        $this->add_control('toolset_relation_text', ['label' => esc_html__('Dynamic HTML', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CODE, 'default' => \DynamicContentForElementor\Plugin::instance()->text_templates->get_default_value(['dynamic-shortcodes' => <<<EOF
<h4><a href="{post:permalink}">{post:title}</a></h4>
{if:{post:featured-image-id} [<a href="{post:permalink}">{media:image @ID={post:featured-image-id}}</a>]}
<p>{post:excerpt}</p>
<a class="btn btn-primary" href="{post:permalink}">{$read_more}</a>
EOF
, 'tokens' => '<h4>[post:title|esc_html]</h4>[post:thumb]<p>[post:excerpt]</p><a class="btn btn-primary" href="[post:permalink]">READ MORE</a>']), 'description' => \DynamicContentForElementor\Plugin::instance()->text_templates->get_default_value(['dynamic-shortcodes' => esc_html__('You can use Dynamic Shortcodes and HTML.', 'dynamic-content-for-elementor'), 'tokens' => esc_html__('You can use HTML and Tokens.', 'dynamic-content-for-elementor')]), 'ai' => ['active' => \false], 'dynamic' => ['active' => \false], 'condition' => ['toolset_relation_render' => 'text']]);
        $this->add_control('toolset_relation_format', ['label' => esc_html__('Display mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => esc_html__('Natural', 'dynamic-content-for-elementor'), 'ul' => esc_html__('Unordered List', 'dynamic-content-for-elementor'), 'ol' => esc_html__('Ordered List', 'dynamic-content-for-elementor'), 'grid' => esc_html__('Grid', 'dynamic-content-for-elementor'), 'tab' => esc_html__('Tabs', 'dynamic-content-for-elementor'), 'accordion' => esc_html__('Accordion', 'dynamic-content-for-elementor'), 'select' => esc_html__('Select', 'dynamic-content-for-elementor')]]);
        $this->add_control('toolset_relation_tag', ['label' => esc_html__('HTML Tag', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_html_tags(), 'default' => 'h2']);
        $this->add_control('toolset_relation_link', ['label' => esc_html__('Link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['toolset_relation_render' => 'title']]);
        Plugin::instance()->text_templates->maybe_add_notice($this, 'label', ['toolset_relation_format' => ['tab', 'accordion', 'select']]);
        $this->add_control('toolset_relation_label', ['label' => esc_html__('Label', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => \DynamicContentForElementor\Plugin::instance()->text_templates->get_default_value(['dynamic-shortcodes' => '{post:title}', 'tokens' => '[post:title|esc_html]']), 'condition' => ['toolset_relation_format' => ['tab', 'accordion', 'select']]]);
        $this->add_control('toolset_relation_close', ['label' => esc_html__('Close by default', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['toolset_relation_format' => ['accordion', 'select']]]);
        $this->add_control('toolset_relation_close_label', ['label' => esc_html__('Empty value text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Choose an option', 'dynamic-content-for-elementor'), 'condition' => ['toolset_relation_close!' => '', 'toolset_relation_format' => 'select']]);
        $this->add_responsive_control('toolset_relation_col', ['label' => esc_html__('Columns', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::NUMBER, 'default' => 3, 'min' => 1, 'description' => esc_html__('Set 1 to show one result per line', 'dynamic-content-for-elementor'), 'condition' => ['toolset_relation_format' => 'grid']]);
        $this->add_control('toolset_relation_tab', ['label' => esc_html__('Tab orientation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['horizontal' => ['title' => esc_html__('Horizontal', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-chevron-up'], 'vertical' => ['title' => esc_html__('Vertical', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-chevron-left']], 'toggle' => \false, 'default' => 'horizontal', 'condition' => ['toolset_relation_format' => 'tab']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style_title', ['label' => esc_html__('Title', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_control('toolset_relation_title_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => ['{{WRAPPER}} .elementor-heading-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('toolset_relation_title_margin', ['label' => esc_html__('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => ['{{WRAPPER}} .elementor-heading-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('toolset_relation_title_align', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right'], 'justify' => ['title' => esc_html__('Justified', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-justify']], 'default' => '', 'selectors' => ['{{WRAPPER}} .elementor-heading-title' => 'text-align: {{VALUE}};']]);
        $this->add_control('toolset_relation_title_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-heading-title' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'toolset_relation_title_typography', 'selector' => '{{WRAPPER}} .elementor-heading-title']);
        $this->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'toolset_relation_title_text_shadow', 'selector' => '{{WRAPPER}} .elementor-heading-title']);
        $this->end_controls_section();
        $this->start_controls_section('section_style_atitle', ['label' => esc_html__('Title Active', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['toolset_relation_format' => 'tab']]);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'toolset_relation_bgcolor_aitem', 'label' => esc_html__('Background', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-tab-item.dce-tab-item-active']);
        $this->add_control('toolset_relation_color_aitem', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-tab-item.dce-tab-item-active .elementor-heading-title' => 'color: {{VALUE}};']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style_item', ['label' => esc_html__('Item', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['toolset_relation_format' => ['accordion', 'tab']]]);
        $this->add_control('toolset_relation_padding_item', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => ['{{WRAPPER}} .dce-view-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'toolset_relation_border_item', 'label' => esc_html__('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-view-item']);
        $this->add_control('toolset_relation_border_radius_item', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => ['{{WRAPPER}} .dce-view-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'toolset_relation_bgcolor_item', 'label' => esc_html__('Background', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-view-item']);
        $this->end_controls_section();
        $this->start_controls_section('section_style_pane', ['label' => esc_html__('Pane', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['toolset_relation_format' => ['accordion', 'tab', 'grid', 'select', 'ul', 'ol']]]);
        $this->add_control('toolset_relation_padding_pane', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => ['{{WRAPPER}} .dce-view-pane' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('toolset_relation_margin_pane', ['label' => esc_html__('Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => ['{{WRAPPER}} .dce-view-pane' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'toolset_relation_border_pane', 'label' => esc_html__('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-view-pane']);
        $this->add_control('toolset_relation_border_radius_pane', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => ['{{WRAPPER}} .dce-view-pane' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('toolset_relation_color_pane', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-view-pane' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'toolset_relation_bgcolor_pane', 'label' => esc_html__('Background', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-view-pane']);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        global $post;
        $old_post = $post;
        if ($settings['toolset_relation_field']) {
            // Check if current post ID is valid
            $current_post_id = get_the_ID();
            if (!$current_post_id) {
                return;
            }
            $query_args = ['query_by_role' => $settings['query_role'], 'return' => 'post_id'];
            $rel_posts = toolset_get_related_posts($current_post_id, $settings['toolset_relation_field'], $query_args);
            if (\is_array($rel_posts) && !empty($rel_posts)) {
                if (\count($rel_posts) > 1 && $settings['toolset_relation_format']) {
                    $labels = array();
                    if (\in_array($settings['toolset_relation_format'], array('tab', 'accordion', 'select'))) {
                        foreach ($rel_posts as $arel) {
                            $post = get_post($arel);
                            $labels[$post->ID] = Plugin::instance()->text_templates->expand_shortcodes_or_callback($settings['toolset_relation_label'], [], function ($str) {
                                return \DynamicContentForElementor\Tokens::do_tokens($str);
                            });
                        }
                    }
                    switch ($settings['toolset_relation_format']) {
                        case 'ul':
                            echo '<ul class="dce-toolset-relational-list">';
                            break;
                        case 'ol':
                            echo '<ol class="dce-toolset-relational-list">';
                            break;
                        case 'grid':
                            echo '<div class="' . esc_attr('dce-view-row grid-page grid-col-md-' . $settings['toolset_relation_col'] . ' grid-col-sm-' . $settings['toolset_relation_col_tablet'] . ' grid-col-xs-' . $settings['toolset_relation_col_mobile']) . '">';
                            break;
                        case 'tab':
                            echo '<div class="' . esc_attr('dce-view-tab dce-tab dce-tab-' . $settings['toolset_relation_tab']) . '"><ul>';
                            $i = 0;
                            foreach ($labels as $pkey => $alabel) {
                                ?>
								<li>
									<a class="dce-view-item dce-tab-item<?php 
                                echo !$i ? ' dce-tab-item-active' : '';
                                ?>" href="#dce-toolset-relational-post-<?php 
                                echo esc_attr($this->get_id() . '-' . $pkey);
                                ?>" onclick="jQuery('.elementor-element-<?php 
                                echo esc_js($this->get_id());
                                ?> .dce-toolset-relational-post').hide();jQuery('.elementor-element-<?php 
                                echo esc_js($this->get_id());
                                ?> .dce-tab-item-active').removeClass('dce-tab-item-active');jQuery(jQuery(this).attr('href')).show();jQuery(this).addClass('dce-tab-item-active'); return false;">
										<<?php 
                                echo \DynamicContentForElementor\Helper::validate_html_tag($settings['toolset_relation_tag']);
                                ?> class="elementor-heading-title">
								<?php 
                                echo wp_kses_post($alabel);
                                ?>
										</<?php 
                                echo \DynamicContentForElementor\Helper::validate_html_tag($settings['toolset_relation_tag']);
                                ?>>
									</a>
								</li>
								<?php 
                                ++$i;
                            }
                            echo '</ul><div class="dce-tab-content">';
                            break;
                        case 'select':
                            ?>
							<select class="elementor-heading-title dce-view-select" onchange="jQuery('.elementor-element-<?php 
                            echo esc_js($this->get_id());
                            ?> .dce-toolset-relational-post').slideUp();jQuery(jQuery(this).val()).slideDown();">
								<?php 
                            if ($settings['toolset_relation_close'] && $settings['toolset_relation_close_label']) {
                                echo '<option value="#dce-view-no-show">' . esc_html($settings['toolset_relation_close_label']) . '</option>';
                            }
                            foreach ($labels as $pkey => $alabel) {
                                echo '<option value="#dce-toolset-relational-post-' . esc_attr($this->get_id() . '-' . $pkey) . '">' . esc_html($alabel) . '</option>';
                            }
                            ?>
							</select>
							<div class="dce-select-content">
								<?php 
                            break;
                    }
                }
                foreach ($rel_posts as $rkey => $arel) {
                    $post = get_post($arel);
                    if (\count($rel_posts) > 1) {
                        switch ($settings['toolset_relation_format']) {
                            case 'ul':
                            case 'ol':
                                echo '<li class="' . esc_attr('dce-view-pane dce-toolset-relational-post dce-toolset-relational-post-' . $post->ID) . '">';
                                break;
                            default:
                                if ($settings['toolset_relation_format'] == 'accordion' && $settings['toolset_relation_render'] != 'title') {
                                    ?>
										<div class="dce-accordion-item">
											<a class="dce-view-item" href="#dce-toolset-relational-post-<?php 
                                    echo esc_attr($this->get_id() . '-' . $post->ID);
                                    ?>" onclick="if (!jQuery(jQuery(this).attr('href')).is(':visible')) {
														jQuery('.elementor-element-<?php 
                                    echo esc_js($this->get_id());
                                    ?> .dce-toolset-relational-post').slideUp();
																		jQuery(jQuery(this).attr('href')).slideDown();
																	} else {
																		jQuery(jQuery(this).attr('href')).slideUp();
																	} return false;">
												<<?php 
                                    echo \DynamicContentForElementor\Helper::validate_html_tag($settings['toolset_relation_tag']);
                                    ?> class="elementor-heading-title">
											<?php 
                                    echo wp_kses_post($labels[$post->ID]);
                                    ?>
												</<?php 
                                    echo \DynamicContentForElementor\Helper::validate_html_tag($settings['toolset_relation_tag']);
                                    ?>>
											</a>
										</div>
									<?php 
                                }
                                $is_hidden = \false;
                                if (\in_array($settings['toolset_relation_format'], array('accordion', 'select'))) {
                                    if ($settings['toolset_relation_close'] && !$rkey || $rkey) {
                                        $is_hidden = \true;
                                    }
                                }
                                if (\in_array($settings['toolset_relation_format'], array('tab')) && $rkey) {
                                    $is_hidden = \true;
                                }
                                $pstyle = $is_hidden ? ' style="display: none;"' : '';
                                $pane_id = 'dce-toolset-relational-post-' . $this->get_id() . '-' . $post->ID;
                                $pane_class = 'dce-view-pane dce-' . $settings['toolset_relation_format'] . '-pane dce-toolset-relational-post dce-toolset-relational-post-' . $post->ID . ($settings['toolset_relation_format'] == 'grid' ? ' item-page' : '');
                                echo '<div id="' . esc_attr($pane_id) . '" class="' . esc_attr($pane_class) . '"' . $pstyle . '>';
                                break;
                        }
                    }
                    if ($settings['toolset_relation_render'] == 'template' && $settings['toolset_relation_template']) {
                        $template_system = \DynamicContentForElementor\Plugin::instance()->template_system;
                        echo $template_system->build_elementor_template_special(['id' => $settings['toolset_relation_template']]);
                    } elseif ($settings['toolset_relation_render'] == 'text') {
                        echo Plugin::instance()->text_templates->expand_shortcodes_or_callback($settings['toolset_relation_text'], [], function ($str) {
                            return \DynamicContentForElementor\Tokens::do_tokens($str);
                        });
                    } else {
                        if ($settings['toolset_relation_link']) {
                            echo '<a class="dce-toolset-relational-post-link" href="' . esc_url((string) get_permalink($post->ID)) . '">';
                        }
                        echo '<' . \DynamicContentForElementor\Helper::validate_html_tag($settings['toolset_relation_tag']) . ' class="elementor-heading-title">' . wp_kses_post(get_the_title($post->ID)) . '</' . \DynamicContentForElementor\Helper::validate_html_tag($settings['toolset_relation_tag']) . '>';
                        if ($settings['toolset_relation_link']) {
                            echo '</a>';
                        }
                    }
                    if (\count($rel_posts) > 1) {
                        switch ($settings['toolset_relation_format']) {
                            case 'ul':
                            case 'ol':
                                echo '</li>';
                                break;
                            default:
                                echo '</div>';
                                break;
                        }
                    }
                }
                if (\count($rel_posts) > 1 && $settings['toolset_relation_format']) {
                    switch ($settings['toolset_relation_format']) {
                        case 'ul':
                            echo '</ul>';
                            break;
                        case 'ol':
                            echo '</ol>';
                            break;
                        case 'tab':
                            echo '</div>';
                            break;
                        case 'grid':
                        case 'select':
                            echo '</div>';
                            break;
                    }
                }
            }
        }
        wp_reset_postdata();
        $post = $old_post;
        setup_postdata($old_post);
    }
}
