<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Modules\DynamicTags\Tags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Controls_Manager;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Template extends Tag
{
    public function get_name()
    {
        return 'dce-template';
    }
    public function get_title()
    {
        return esc_html__('Template', 'dynamic-content-for-elementor');
    }
    public function get_group()
    {
        return 'dce';
    }
    public function get_categories()
    {
        return ['base', 'text'];
    }
    public function get_docs()
    {
        return 'https://www.dynamic.ooo/dynamic-content-for-elementor/features/dynamic-tag-template/';
    }
    /**
     * Register Controls
     *
     * Registers the Dynamic tag controls
     *
     * @since 2.0.0
     * @access protected
     *
     * @return void
     */
    protected function register_controls()
    {
        $this->add_control('dce_template', ['label' => esc_html__('Template', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Template Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'object_type' => 'elementor_library']);
        $this->add_control('dce_template_new', ['type' => Controls_Manager::RAW_HTML, 'raw' => '<a class="elementor-button elementor-button-block elementor-field-textual elementor-size-sm elementor-button-success" href="' . admin_url('edit.php?post_type=elementor_library#add_new') . '" target="_blank"><i class="eicon-plus"></i> ' . esc_html__('Add new template', 'dynamic-content-for-elementor') . '</a>', 'condition' => ['dce_template' => '']]);
        $this->add_control('dce_template_post_id', ['label' => esc_html__('Post', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Force Post content', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'condition' => ['dce_template!' => '']]);
        $this->add_control('dce_template_inline_css', ['label' => esc_html__('Inline CSS', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_template!' => '']]);
        $this->add_control('dce_template_help', ['type' => Controls_Manager::RAW_HTML, 'raw' => '<div id="elementor-panel__editor__help" class="p-0"><a id="elementor-panel__editor__help__link" href="' . $this->get_docs() . '" target="_blank">' . esc_html__('Need Help', 'dynamic-content-for-elementor') . ' <i class="eicon-help-o"></i></a></div>', 'separator' => 'before']);
    }
    public function render()
    {
        // Values of dynamic tags containing CSS were not rendered correctly due to Elementor's performance optimisations
        $original_use_style_controls = \Elementor\Core\Frontend\Performance::is_use_style_controls();
        \Elementor\Core\Frontend\Performance::set_use_style_controls(\true);
        try {
            $this->render_real();
        } finally {
            \Elementor\Core\Frontend\Performance::set_use_style_controls($original_use_style_controls);
        }
    }
    private function render_real()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        if (!empty($settings['dce_template'])) {
            $atts = ['id' => $settings['dce_template']];
            if ($settings['dce_template_post_id']) {
                $atts['post_id'] = $settings['dce_template_post_id'];
            }
            if ($settings['dce_template_inline_css']) {
                $atts['inlinecss'] = \true;
            }
            $template_system = \DynamicContentForElementor\Plugin::instance()->template_system;
            echo $template_system->build_elementor_template_special($atts);
        }
    }
}
