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
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Plugin;
use DynamicContentForElementor\Tokens;
// Exit if accessed directly
if (!\defined('ABSPATH')) {
    exit;
}
class AcfFlexibleContent extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_script_depends()
    {
        return [];
    }
    public function get_style_depends()
    {
        return [];
    }
    /**
     * @return void
     */
    public function run_once()
    {
        parent::run_once();
        $save_guard = \DynamicContentForElementor\Plugin::instance()->save_guard;
        $save_guard->register_unsafe_control($this->get_name(), 'layouts::html');
        $save_guard->register_unsafe_control($this->get_name(), 'other_post_source');
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_content', ['label' => $this->get_title()]);
        $this->add_control('flexible_field', ['label' => esc_html__('Select ACF Flexible Content Field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acf', 'object_type' => 'flexible_content', 'dynamic' => ['active' => \false]]);
        $this->add_control('flexible_field_from', ['label' => esc_html__('Retrieve the field from', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'current_post', 'options' => ['current_post' => esc_html__('Current Post', 'dynamic-content-for-elementor'), 'current_user' => esc_html__('Current User', 'dynamic-content-for-elementor'), 'current_author' => esc_html__('Current Author', 'dynamic-content-for-elementor'), 'current_term' => esc_html__('Current Term', 'dynamic-content-for-elementor'), 'options_page' => esc_html__('Options Page', 'dynamic-content-for-elementor')]]);
        $repeater_layout = new \Elementor\Repeater();
        $repeater_layout->start_controls_tabs('layout_repeater');
        $repeater_layout->add_control('layout', ['type' => 'ooo_query', 'label' => esc_html__('Layout', 'dynamic-content-for-elementor'), 'placeholder' => esc_html__('Select the layout', 'dynamic-content-for-elementor'), 'query_type' => 'acf_flexible_content_layouts', 'label_block' => \true]);
        $repeater_layout->add_control('display_mode', ['label' => esc_html__('Display mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['html' => ['title' => esc_html__('Dynamic HTML', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-code'], 'template' => ['title' => esc_html__('Template', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-th-large']], 'toggle' => \false, 'default' => 'template']);
        Plugin::instance()->text_templates->maybe_add_notice($repeater_layout, '', ['display_mode' => 'html']);
        $repeater_layout->add_control('html', ['label' => esc_html__('Dynamic HTML', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CODE, 'default' => \DynamicContentForElementor\Plugin::instance()->text_templates->get_default_value(['dynamic-shortcodes' => "{for:item {data:row} {get:item} @sep=', '}", 'tokens' => '[ROW]']), 'ai' => ['active' => \false], 'dynamic' => ['active' => \false], 'description' => \DynamicContentForElementor\Plugin::instance()->text_templates->get_default_value(['dynamic-shortcodes' => \sprintf(esc_html__('Available Dynamic Shortcodes include: %1$s You can also use all other Dynamic Shortcodes to further customize the content.', 'dynamic-content-for-elementor'), '<ul>' . '<li>' . \sprintf(esc_html__("Use %s to fetch an array of the current row's data.", 'dynamic-content-for-elementor'), '<code>{data:row}</code>') . '</li>' . '<li>' . \sprintf(esc_html__("Use %s to fetch a comma-separated string of the current row's data.", 'dynamic-content-for-elementor'), "<code>{for:item {data:row} {get:item} @sep=', '}</code>") . '</li>' . '<li>' . \sprintf(esc_html__('Use %s to fetch the index of the current row.', 'dynamic-content-for-elementor'), '<code>{data:row-index}</code>') . '</li>' . '</ul>'), 'tokens' => esc_html__('You can use HTML and Tokens.', 'dynamic-content-for-elementor')]), 'condition' => ['display_mode' => 'html']]);
        $repeater_layout->add_control('template_id', ['label' => esc_html__('Template', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Select Template', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'dynamic' => ['active' => apply_filters('dynamicooo/allow-experimental-dynamic-tags', \false)], 'object_type' => 'elementor_library', 'condition' => ['display_mode' => 'template']]);
        $this->add_control('layouts', ['label' => esc_html__('Show these layouts', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::REPEATER, 'fields' => $repeater_layout->get_controls(), 'title_field' => '{{{layout}}}', 'prevent_empty' => \false, 'item_actions' => ['add' => \true, 'duplicate' => \true, 'remove' => \true, 'sort' => \false]]);
        $this->end_controls_section();
        $this->start_controls_section('section_toggle_style', ['label' => $this->get_title(), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography', 'selector' => '{{WRAPPER}} ']);
        $this->add_control('color', ['label' => esc_html__('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}}' => 'color: {{VALUE}};']]);
        $this->add_responsive_control('alignment', ['label' => esc_html__('Global Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \true, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'selectors' => ['{{WRAPPER}}' => 'text-align: {{VALUE}};']]);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings) || empty($settings['layouts'])) {
            return;
        }
        $id = Helper::get_acf_source_id($settings['flexible_field_from'], $settings['other_post_source'] ?? \false);
        $defined_layouts = \array_column($settings['layouts'], 'layout');
        if (have_rows($settings['flexible_field'], $id)) {
            while (have_rows($settings['flexible_field'], $id)) {
                the_row();
                if (\in_array(get_row_layout(), $defined_layouts)) {
                    $key_layout = \array_search(get_row_layout(), $defined_layouts);
                    if ('html' === $settings['layouts'][$key_layout]['display_mode']) {
                        $sub_fields = Helper::get_acf_flexible_content_sub_fields_by_row($settings['flexible_field'], get_row_index());
                        $html = $settings['layouts'][$key_layout]['html'];
                        echo '<div>';
                        // Tokens [ROW]
                        // Dynamic Shortcodes {data:row}
                        $data = $sub_fields;
                        echo Plugin::instance()->text_templates->expand_shortcodes_or_callback($settings['layouts'][$key_layout]['html'], ['row' => $data], function ($str) use($data) {
                            $str = Helper::get_dynamic_value($str);
                            return Tokens::replace_var_tokens($str, 'ROW', $data);
                        });
                        echo '</div>';
                    } elseif ('template' === $settings['layouts'][$key_layout]['display_mode']) {
                        $atts = ['id' => $settings['layouts'][$key_layout]['template_id'], 'inlinecss' => \Elementor\Plugin::$instance->editor->is_edit_mode()];
                        $template_system = \DynamicContentForElementor\Plugin::instance()->template_system;
                        echo $template_system->build_elementor_template_special($atts);
                    }
                }
            }
        }
    }
}
