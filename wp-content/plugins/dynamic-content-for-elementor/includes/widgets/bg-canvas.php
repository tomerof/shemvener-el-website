<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use DynamicContentForElementor\Helper;
use Elementor\Repeater;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class BgCanvas extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    /**
     * @return array<string>
     */
    public function get_script_depends()
    {
        return ['dce-threejs-lib', 'dce-threejs-EffectComposer', 'dce-threejs-RenderPass', 'dce-threejs-ShaderPass', 'dce-threejs-FilmPass', 'dce-threejs-HalftonePass', 'dce-threejs-DotScreenPass', 'dce-threejs-GlitchPass', 'dce-threejs-CopyShader', 'dce-threejs-HalftoneShader', 'dce-threejs-RGBShiftShader', 'dce-threejs-DotScreenShader', 'dce-threejs-ConvolutionShader', 'dce-threejs-FilmShader', 'dce-threejs-DigitalGlitch', 'dce-threejs-PixelShader', 'dce-anime-lib', 'dce-bgcanvas-js'];
    }
    /**
     * @return array<string>
     */
    public function get_style_depends()
    {
        return ['dce-bgCanvas'];
    }
    /**
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_bgcanvas', ['label' => esc_html__('Image', 'dynamic-content-for-elementor')]);
        $this->add_control('bgcanvas_image', ['label' => esc_html__('Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::MEDIA, 'dynamic' => ['active' => \true], 'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()]]);
        $this->add_group_control(Group_Control_Image_Size::get_type(), ['name' => 'image', 'default' => 'thumbnail', 'condition' => ['bgcanvas_image[id]!' => '']]);
        $this->add_responsive_control('bgcanvas_height', ['label' => esc_html__('Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 400, 'unit' => 'px'], 'tablet_default' => ['unit' => 'px'], 'mobile_default' => ['unit' => 'px'], 'render_type' => 'template', 'size_units' => ['px', '%', 'vh'], 'separator' => 'after', 'range' => ['%' => ['min' => 1, 'max' => 100], 'px' => ['min' => 1, 'max' => 1000], 'vh' => ['min' => 1, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-container-bgcanvas' => 'height: {{SIZE}}{{UNIT}};']]);
        $this->end_controls_section();
        $this->start_controls_section('section_postprocessing', ['label' => esc_html__('Postprocessing & Shaders', 'dynamic-content-for-elementor')]);
        $this->add_control('postprocessing_film', ['label' => esc_html__('Film', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'return_value' => 'yes', 'frontend_available' => \true, 'separator' => 'before']);
        $this->add_control('postprocessing_film_grayscale', ['label' => esc_html__('Gray Scale', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'return_value' => 'yes', 'frontend_available' => \true, 'condition' => ['postprocessing_film!' => '']]);
        $this->add_control('postprocessing_film_noiseIntensity', ['label' => esc_html__('Noise Intensity', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'frontend_available' => \true, 'default' => ['size' => 0.35], 'range' => ['px' => ['min' => 0.01, 'max' => 1, 'step' => 0.01]], 'condition' => ['postprocessing_film!' => '']]);
        $this->add_control('postprocessing_film_scanlinesIntensity', ['label' => esc_html__('Scanlines Intensity', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'frontend_available' => \true, 'default' => ['size' => 0.035], 'range' => ['px' => ['min' => 0.01, 'max' => 1, 'step' => 0.001]], 'condition' => ['postprocessing_film!' => '']]);
        $this->add_control('postprocessing_film_scanlinesCount', ['label' => esc_html__('Scanlines Count', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'frontend_available' => \true, 'default' => ['size' => 648], 'range' => ['px' => ['min' => 1, 'max' => 1000, 'step' => 1]], 'condition' => ['postprocessing_film!' => '']]);
        $this->add_control('postprocessing_halftone', ['label' => esc_html__('Halftone', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'return_value' => 'yes', 'frontend_available' => \true, 'separator' => 'before']);
        $this->add_control('postprocessing_halftone_shape', ['label' => esc_html__('Shape', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'frontend_available' => \true, 'options' => ['1' => esc_html__('Dots', 'dynamic-content-for-elementor'), '2' => esc_html__('Ellipse', 'dynamic-content-for-elementor'), '3' => esc_html__('Lines', 'dynamic-content-for-elementor'), '4' => esc_html__('Squre', 'dynamic-content-for-elementor')], 'default' => '1', 'condition' => ['postprocessing_halftone!' => '']]);
        $this->add_control('postprocessing_halftone_grayscale', ['label' => esc_html__('Gray Scale', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'return_value' => 'yes', 'frontend_available' => \true, 'condition' => ['postprocessing_halftone!' => '']]);
        $this->add_control('postprocessing_halftone_radius', ['label' => esc_html__('Dot Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'frontend_available' => \true, 'default' => ['size' => 10], 'range' => ['px' => ['min' => 1, 'max' => 100, 'step' => 1]], 'condition' => ['postprocessing_halftone!' => '']]);
        $this->add_control('postprocessing_rgbShiftShader', ['label' => esc_html__('RGB Shift Shader', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'return_value' => 'yes', 'frontend_available' => \true, 'separator' => 'before']);
        $this->add_control('postprocessing_rgbshift_amount', ['label' => esc_html__('Amount', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'frontend_available' => \true, 'default' => ['size' => 15], 'range' => ['px' => ['min' => 1, 'max' => 30, 'step' => 0.001]], 'condition' => ['postprocessing_rgbShiftShader!' => '']]);
        $this->add_control('postprocessing_glitch', ['label' => esc_html__('Glitch', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'return_value' => 'yes', 'frontend_available' => \true, 'separator' => 'before']);
        $this->add_control('postprocessing_dot', ['label' => esc_html__('Dot', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'return_value' => 'yes', 'frontend_available' => \true, 'separator' => 'before']);
        $this->add_control('postprocessing_dot_scale', ['label' => esc_html__('Dot Scale', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'frontend_available' => \true, 'default' => ['size' => 1], 'range' => ['px' => ['min' => 0.1, 'max' => 10, 'step' => 0.1]], 'condition' => ['postprocessing_dot!' => '']]);
        $this->add_control('postprocessing_dot_angle', ['label' => esc_html__('Dot Angle', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'frontend_available' => \true, 'default' => ['size' => 0.5], 'range' => ['px' => ['min' => -1, 'max' => 1, 'step' => 0.01]], 'condition' => ['postprocessing_dot!' => '']]);
        $this->add_control('postprocessing_pixels', ['label' => esc_html__('Pixels', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'return_value' => 'yes', 'frontend_available' => \true, 'separator' => 'before']);
        $this->add_control('postprocessing_pixels_size', ['label' => esc_html__('Pixels Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'frontend_available' => \true, 'default' => ['size' => 16], 'range' => ['px' => ['min' => 1, 'max' => 100, 'step' => 1]], 'condition' => ['postprocessing_pixels!' => '']]);
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
        $image_url = Group_Control_Image_Size::get_attachment_image_src($settings['bgcanvas_image']['id'], 'image', $settings);
        ?>
		<div class="dce-container-bgcanvas" data-bgcanvasimage="<?php 
        echo esc_url($image_url);
        ?>">
			<div class="scene js-scene"></div>
		</div>
		<?php 
    }
    /**
     * @return void
     */
    protected function content_template()
    {
        ?>

		<#
		var image = {
			id: settings.bgcanvas_image.id,
			url: settings.bgcanvas_image.url,
			size: settings.image_size,
			dimension: settings.image_custom_dimension,
			model: view.getEditModel()
		};
		var url_image = elementor.imagesManager.getImageUrl( image );
		#>
		<div class="dce-container-bgcanvas" data-bgcanvasimage="{{url_image}}">
			<div class="scene js-scene"></div>
		</div>
		<?php 
    }
}
