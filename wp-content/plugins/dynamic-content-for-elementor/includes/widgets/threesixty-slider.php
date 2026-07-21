<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

use DynamicContentForElementor\Helper;
use Elementor\Controls_Manager;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class ThreesixtySlider extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    /**
     * @return array<string>
     */
    public function get_script_depends()
    {
        return ['dce-threesixtyslider-lib', 'dce-360-slider'];
    }
    /**
     * @return array<string>
     */
    public function get_style_depends()
    {
        return ['dce-threesixtySlider'];
    }
    /**
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_threesixtyslider', ['label' => $this->get_title()]);
        $this->add_responsive_control('width', ['label' => esc_html__('Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 400, 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'size_units' => ['px', '%'], 'range' => ['px' => ['min' => 0, 'max' => 2000], '%' => ['min' => 10, 'max' => 100]], 'render_type' => 'template', 'selectors' => ['{{WRAPPER}} .dce-threesixty' => 'max-width: {{SIZE}}{{UNIT}};'], 'frontend_available' => \true]);
        $this->add_control('pathimages', ['label' => esc_html__('Images path', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => Controls_Manager::TEXT, 'default' => '', 'placeholder' => '360-gallery', 'description' => \sprintf('%s<br>%s', esc_html__('Enter the folder path inside your WordPress Media Library (wp-content/uploads). For example, if your images are in "wp-content/uploads/360-gallery", just enter "360-gallery".', 'dynamic-content-for-elementor'), esc_html__('Images must be named sequentially: 1.jpg, 2.jpg, 3.jpg, etc.', 'dynamic-content-for-elementor')), 'frontend_available' => \true]);
        $this->add_control('navigation', ['label' => esc_html__('Navigation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'frontend_available' => \true]);
        $this->add_control('disable_spin', ['label' => esc_html__('Disable the initial spin on load', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'frontend_available' => \true]);
        $this->add_control('play_speed', ['label' => esc_html__('Play Speed (ms)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 100, 'unit' => 'ms'], 'size_units' => ['ms'], 'range' => ['ms' => ['min' => 100, 'max' => 1000, 'step' => 100]], 'render_type' => 'template', 'frontend_available' => \true]);
        $this->end_controls_section();
        $this->start_controls_section('section_style', ['label' => esc_html__('Style', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_control('navigation_position', ['label' => esc_html__('Navigation Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['top-left' => esc_html__('Top Left', 'dynamic-content-for-elementor'), 'top-center' => esc_html__('Top Center', 'dynamic-content-for-elementor'), 'top-right' => esc_html__('Top Right', 'dynamic-content-for-elementor'), 'center-left' => esc_html__('Center Left', 'dynamic-content-for-elementor'), 'center-center' => esc_html__('Center Center', 'dynamic-content-for-elementor'), 'center-right' => esc_html__('Center Right', 'dynamic-content-for-elementor'), 'bottom-left' => esc_html__('Bottom Left', 'dynamic-content-for-elementor'), 'bottom-center' => esc_html__('Bottom Center', 'dynamic-content-for-elementor'), 'bottom-right' => esc_html__('Bottom Right', 'dynamic-content-for-elementor')], 'default' => 'bottom-center', 'frontend_available' => \true, 'condition' => ['navigation!' => '']]);
        $this->end_controls_section();
    }
    /**
     * @return void
     */
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        if (empty($settings['pathimages']) && \Elementor\Plugin::$instance->editor->is_edit_mode()) {
            Helper::notice(esc_html__('360° Slider Setup Required', 'dynamic-content-for-elementor'), wp_kses(\sprintf(__('1. Create a folder in your uploads directory (e.g. "/wp-content/uploads/360/")<br>2. Upload your sequence of images named as: %s<br>3. Copy the folder path and paste it in the "Images path" field', 'dynamic-content-for-elementor'), '<code>1.jpg, 2.jpg, 3.jpg, ...</code>'), ['br' => [], 'code' => []]), 'elementor-alert-warning');
        }
        // Get WordPress uploads directory info
        $upload_dir = wp_upload_dir();
        if (!empty($upload_dir['error'])) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                Helper::notice(esc_html__('Upload Directory Error', 'dynamic-content-for-elementor'), esc_html($upload_dir['error']), 'elementor-alert-danger');
            }
            return;
        }
        // Sanitize and validate path
        $pathimages = \ltrim($settings['pathimages'], '/');
        // Remove wp-content/uploads if present
        $pathimages = \preg_replace('#^wp-content/uploads/#', '', $pathimages);
        // Build full path using uploads directory
        $full_path = wp_normalize_path(trailingslashit($upload_dir['basedir']) . $pathimages);
        $real_path = \realpath($full_path);
        $real_upload_dir = \realpath($upload_dir['basedir']);
        // Security checks:
        // 1. realpath returns valid result
        // 2. path is inside uploads directory
        // 3. path is actually a directory
        if (!$real_path || !$real_upload_dir || \strpos($real_path, $real_upload_dir) !== 0 || !\is_dir($real_path)) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                Helper::notice(esc_html__('Invalid Directory', 'dynamic-content-for-elementor'), \sprintf(esc_html__('The directory "%1$s" must be located inside your WordPress uploads directory (%2$s).', 'dynamic-content-for-elementor'), esc_html($settings['pathimages']), esc_html('wp-content/uploads/')), 'elementor-alert-danger');
            }
            return;
        }
        // Find first file to determine extension
        $extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        $format_file = '';
        foreach ($extensions as $ext) {
            if (\file_exists($full_path . '/1.' . $ext)) {
                $format_file = $ext;
                break;
            }
        }
        if (empty($format_file)) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                Helper::notice(esc_html__('No Images Found', 'dynamic-content-for-elementor'), esc_html__('Could not find any image starting with "1." in the specified directory.', 'dynamic-content-for-elementor'), 'elementor-alert-warning');
            }
            return;
        }
        // Count files and collect image paths
        $total_frame = 1;
        while (\file_exists($full_path . '/' . $total_frame . '.' . $format_file)) {
            ++$total_frame;
        }
        --$total_frame;
        // Convert path to URL for frontend
        $path_url = $settings['pathimages'];
        if (\strpos($path_url, 'http') !== 0) {
            // If path doesn't start with http, convert it to URL
            $path_url = site_url($settings['pathimages']);
        }
        // Make sure the path ends with a slash
        $path_url = \rtrim($path_url, '/') . '/';
        $this->add_render_attribute('threesixty', ['class' => ['threesixty', 'dce-threesixty'], 'data-pathimages' => $path_url, 'data-format_file' => $format_file, 'data-total_frame' => $total_frame, 'data-end_frame' => $total_frame]);
        ?>
		<div <?php 
        $this->print_render_attribute_string('threesixty');
        ?>>
			<div class="spinner">
				<span>0%</span>
			</div>
			<ol class="threesixty_images"></ol>
		</div>
		<?php 
    }
}
