<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Includes\Skins;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Icons_Manager;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Skin3D extends \DynamicContentForElementor\Includes\Skins\SkinBase
{
    /**
     * @var array<string>
     */
    public $depended_scripts = ['dce-anime-lib', 'dce-threejs-lib', 'dce-threejs-OrbitControls', 'dce-threejs-CSS3DRenderer', 'dce-dynamicPosts-3d'];
    /**
     * @var array<string>
     */
    public $depended_styles = ['dce-dynamicPosts-3d'];
    /**
     * @return void
     */
    protected function _register_controls_actions()
    {
        add_action('elementor/element/' . $this->get_parent()->get_name() . '/section_query/after_section_end', [$this, 'register_controls_layout']);
        add_action('elementor/element/' . $this->get_parent()->get_name() . '/section_dynamicposts/after_section_end', [$this, 'register_additional_3d_controls']);
    }
    /**
     * @return string
     */
    public function get_id()
    {
        return '3d';
    }
    /**
     * @return string
     */
    public function get_title()
    {
        return esc_html__('3D', 'dynamic-content-for-elementor');
    }
    /**
     * @param \DynamicContentForElementor\Widgets\DynamicPostsBase $widget
     * @return void
     */
    public function register_additional_3d_controls(\DynamicContentForElementor\Widgets\DynamicPostsBase $widget)
    {
        $this->parent = $widget;
        $this->start_controls_section('section_3d', ['label' => esc_html__('3D', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT]);
        $this->add_control('type_3d', ['label' => esc_html__('3D Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'circle', 'options' => ['circle' => esc_html__('Circle', 'dynamic-content-for-elementor'), 'fila' => esc_html__('Row', 'dynamic-content-for-elementor')], 'frontend_available' => \true]);
        $this->add_control('size_plane_3d', ['label' => esc_html__('Single Item Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '320', 'unit' => 'px'], 'range' => ['px' => ['max' => 600, 'min' => 100, 'step' => 10]], 'render_type' => 'template', 'frontend_available' => \true, 'selectors' => ['{{WRAPPER}} .dce-posts-container.dce-skin-3d .dce-3d-element' => 'width: {{SIZE}}{{UNIT}};']]);
        $this->add_control('blur_depth_3d', ['label' => esc_html__('Depth blur', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true, 'condition' => [$this->get_control_id('type_3d') => 'circle']]);
        $this->add_control('mousewheel_3d', ['label' => esc_html__('Mouse wheel', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'frontend_available' => \true]);
        $this->add_control('mousewheel_3d_stop_at_end', ['label' => esc_html__('Free mouse wheel at the end', 'dynamic-content-for-elementor'), 'description' => esc_html__('Free mouse wheel after last element is reached', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => [$this->get_control_id('mousewheel_3d') => 'yes']]);
        $this->add_control('3d_center_at_start', ['label' => esc_html__('Center the first item at the start', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->end_controls_section();
    }
    /**
     * @return void
     */
    protected function render_posts_before()
    {
        $_skin = $this->get_parent()->get_settings('_skin');
        ?>
		<div id="dce-scene-3d-container" class="dce-posts-wrapper"></div>
		
		<div class="dce-3d-navigation">
			<div class="dce-3d-prev dce-3d-arrow">
				<?php 
        // Render left arrow icon
        Icons_Manager::render_icon(['value' => 'fas fa-arrow-left', 'library' => 'fa-solid'], ['aria-hidden' => 'true', 'class' => 'arrow-icon']);
        ?>
			</div>
			<div class="dce-3d-next dce-3d-arrow">
				<?php 
        // Render right arrow icon
        Icons_Manager::render_icon(['value' => 'fas fa-arrow-right', 'library' => 'fa-solid'], ['aria-hidden' => 'true', 'class' => 'arrow-icon']);
        ?>
			</div>
		</div>
		
		<div class="dce-3d-quit">
			<?php 
        // Render times (X) icon
        Icons_Manager::render_icon(['value' => 'fas fa-times', 'library' => 'fa-solid'], ['aria-hidden' => 'true']);
        ?>
		</div>
		<?php 
    }
    /**
     * @return string
     */
    public function get_container_class()
    {
        return 'dce-3d-container dce-skin-' . $this->get_id();
    }
    /**
     * @return string
     */
    public function get_wrapper_class()
    {
        return 'dce-grid-3d dce-3d-wrapper dce-3d-wrapper-hidden dce-wrapper-' . $this->get_id();
    }
    /**
     * @return string
     */
    public function get_item_class()
    {
        return 'dce-item-' . $this->get_id();
    }
    /**
     * @return string
     */
    public function get_image_class()
    {
        return '';
    }
}
