<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Includes\Skins;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class SkinList extends \DynamicContentForElementor\Includes\Skins\SkinBase
{
    /**
     * Depended Scripts
     *
     * @var array<string>
     */
    public $depended_scripts = [];
    /**
     * Depended Styles
     *
     * @var array<string>
     */
    public $depended_styles = [];
    /**
     * Get ID
     *
     * @return string
     */
    public function get_id()
    {
        return 'list';
    }
    /**
     * Get Title
     *
     * @return string
     */
    public function get_title()
    {
        return esc_html__('List', 'dynamic-content-for-elementor');
    }
    /**
     * Register Controls Actions
     *
     * @return void
     */
    protected function _register_controls_actions()
    {
        add_action('elementor/element/' . $this->get_parent()->get_name() . '/section_query/after_section_end', [$this, 'register_controls_layout']);
        add_action('elementor/element/' . $this->get_parent()->get_name() . '/section_dynamicposts/after_section_end', [$this, 'register_additional_list_controls'], 20);
    }
    /**
     * Register Additional Controls
     *
     * @param \DynamicContentForElementor\Widgets\DynamicPostsBase $widget
     * @return void
     */
    public function register_additional_list_controls(\DynamicContentForElementor\Widgets\DynamicPostsBase $widget)
    {
        $this->parent = $widget;
        $this->start_controls_section('section_list', ['label' => esc_html__('List', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT]);
        $this->add_control('type', ['label' => esc_html__('List Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['unordered' => ['title' => esc_html__('Unordered List', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-list-ul'], 'ordered' => ['title' => esc_html__('Ordered List', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-list-ol']], 'default' => 'unordered']);
        $this->add_control('numbering', ['label' => esc_html__('Numbering Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['numbers' => esc_html__('Numbers', 'dynamic-content-for-elementor'), 'lowercase_letters' => esc_html__('Lowercase letters', 'dynamic-content-for-elementor'), 'uppercase_letters' => esc_html__('Uppercase letters', 'dynamic-content-for-elementor'), 'lowercase_roman_numerals' => esc_html__('Lowercase Roman numerals', 'dynamic-content-for-elementor'), 'uppercase_roman_numerals' => esc_html__('Uppercase Roman numerals', 'dynamic-content-for-elementor')], 'default' => 'numbers', 'condition' => [$this->get_control_id('type') => 'ordered']]);
        $this->end_controls_section();
    }
    /**
     * Register Style Controls
     *
     * @return void
     */
    protected function register_style_controls()
    {
        parent::register_style_controls();
        $this->start_controls_section('section_style_list', ['label' => esc_html__('List', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_control('style_type', ['label' => esc_html__('Style Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['disc' => esc_html__('Disc', 'dynamic-content-for-elementor'), 'circle' => esc_html__('Circle', 'dynamic-content-for-elementor'), 'square' => esc_html__('Square', 'dynamic-content-for-elementor'), 'none' => esc_html__('None', 'dynamic-content-for-elementor')], 'default' => 'disc', 'selectors' => ['{{WRAPPER}} ul' => 'list-style-type: {{VALUE}};'], 'condition' => [$this->get_control_id('type') => 'unordered']]);
        $this->add_responsive_control('row_gap', ['label' => esc_html__('Rows Gap', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0], 'range' => ['px' => ['min' => 0, 'max' => 100]], 'selectors' => ['{{WRAPPER}} li.dce-post' => 'padding-bottom: {{SIZE}}{{UNIT}};']]);
        $this->end_controls_section();
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
        $settings = $this->get_parent()->get_settings_for_display();
        $p_query = $this->get_parent()->get_query();
        $this->add_direction();
        $this->get_parent()->add_render_attribute('container', ['class' => ['dce-posts-container', 'dce-posts']]);
        $this->maybe_render_pagination_top();
        if ('unordered' === $settings['list_type']) {
            ?>
			<ul <?php 
            echo $this->get_parent()->get_render_attribute_string('container');
            ?>>
			<?php 
        } else {
            $type = '';
            switch ($settings['list_numbering']) {
                case 'lowercase_letters':
                    $type = 'a';
                    break;
                case 'uppercase_letters':
                    $type = 'A';
                    break;
                case 'lowercase_roman_numerals':
                    $type = 'i';
                    break;
                case 'uppercase_roman_numerals':
                    $type = 'I';
                    break;
            }
            $this->get_parent()->add_render_attribute('container', 'type', $type);
            ?>
			<ol <?php 
            echo $this->get_parent()->get_render_attribute_string('container');
            ?>>
		<?php 
        }
        $this->render_posts_before();
        $this->render_posts_wrapper_before();
    }
    /**
     * Render Loop End
     *
     * @return void
     */
    protected function render_loop_end()
    {
        $settings = $this->get_parent()->get_settings_for_display();
        $this->render_posts_wrapper_after();
        $this->render_posts_after();
        if ('unordered' === $settings['list_type']) {
            ?>
			</ul>
			<?php 
        } else {
            ?>
			</ol>
		<?php 
        }
        $this->maybe_render_pagination_bottom();
        $this->render_infinite_scroll();
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
        ?>

		<li <?php 
        echo $this->get_parent()->get_render_attribute_string('post');
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
        ?>
		</li>
		<?php 
    }
}
