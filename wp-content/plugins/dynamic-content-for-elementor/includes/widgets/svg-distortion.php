<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class SvgDistortion extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    /**
     * @return array<string>
     */
    public function get_script_depends()
    {
        return ['dce-anime-lib', 'dce-svgdistortion'];
    }
    /**
     * @return array<string>
     */
    public function get_style_depends()
    {
        return ['dce-svg'];
    }
    /**
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_distortion', ['label' => esc_html__('Distortion', 'dynamic-content-for-elementor')]);
        $this->add_control('svg_trigger', ['label' => esc_html__('Trigger', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['static' => esc_html__('Static', 'dynamic-content-for-elementor'), 'animation' => esc_html__('Animation', 'dynamic-content-for-elementor'), 'rollover' => esc_html__('Rollover', 'dynamic-content-for-elementor'), 'scroll' => esc_html__('Scroll', 'dynamic-content-for-elementor')], 'frontend_available' => \true, 'default' => 'static', 'render_type' => 'template', 'prefix_class' => 'svg-trigger-']);
        $this->add_control('link_to', ['label' => esc_html__('Link to', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'none', 'options' => ['none' => esc_html__('None', 'dynamic-content-for-elementor'), 'home' => esc_html__('Home URL', 'dynamic-content-for-elementor'), 'custom' => esc_html__('Custom URL', 'dynamic-content-for-elementor')], 'condition' => ['svg_trigger' => 'rollover']]);
        $this->add_control('link', ['label' => esc_html__('Link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::URL, 'placeholder' => esc_html__('https://your-link.com', 'dynamic-content-for-elementor'), 'dynamic' => ['active' => \true], 'condition' => ['link_to' => 'custom', 'svg_trigger' => 'rollover'], 'default' => ['url' => ''], 'show_label' => \false]);
        $this->add_control('playpause_control', ['label' => esc_html__('Animation Controls', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'default' => 'running', 'toggle' => \false, 'options' => ['running' => ['title' => esc_html__('Play', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-play'], 'paused' => ['title' => esc_html__('Pause', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-pause']], 'frontend_available' => \true, 'separator' => 'before', 'render_type' => 'ui', 'condition' => ['svg_trigger' => ['animation']]]);
        $this->add_control('animation_heading', ['label' => esc_html__('Animation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['svg_trigger' => ['animation', 'rollover', 'scroll']]]);
        $this->add_control('speed_animation', ['label' => esc_html__('Speed', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'label_block' => \false, 'default' => ['size' => 3], 'range' => ['px' => ['min' => 0.1, 'max' => 10, 'step' => 0.1]], 'frontend_available' => \true, 'condition' => ['svg_trigger' => ['animation', 'rollover', 'scroll']]]);
        $this->add_control('delay_animation', ['label' => esc_html__('Delay', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'label_block' => \false, 'default' => ['size' => 1], 'range' => ['px' => ['min' => 0, 'max' => 10, 'step' => 0.1]], 'frontend_available' => \true, 'condition' => ['svg_trigger' => ['animation', 'rollover', 'scroll']]]);
        $this->add_control('easing_animation', ['label' => esc_html__('Easing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => esc_html__('Default', 'dynamic-content-for-elementor')] + Helper::get_ease(), 'default' => 'easeInOut', 'frontend_available' => \true, 'label_block' => \false, 'condition' => ['svg_trigger' => ['animation', 'rollover', 'scroll']]]);
        $this->add_control('easing_animation_ease', ['label' => esc_html__('Equation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => esc_html__('Default', 'dynamic-content-for-elementor')] + Helper::get_timing_functions(), 'default' => 'Power3', 'frontend_available' => \true, 'label_block' => \false, 'condition' => ['svg_trigger' => ['animation', 'rollover', 'scroll']]]);
        $this->add_control('base_image', ['label' => esc_html__('The base Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::MEDIA, 'separator' => 'before', 'dynamic' => ['active' => \true], 'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()]]);
        $this->add_control('displacement_image', ['label' => esc_html__('Displacement Map Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::MEDIA, 'dynamic' => ['active' => \true], 'default' => ['url' => ''], 'frontend_available' => \true]);
        $this->add_group_control(Group_Control_Image_Size::get_type(), [
            'name' => 'image',
            // Actually its `image_size`
            'default' => 'thumbnail',
            'condition' => ['base_image[id]!' => ''],
        ]);
        $this->add_control('displacementmap_heading', ['label' => esc_html__('DisplacementMap', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('disp_factor', ['label' => esc_html__('Depth Factor', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '100', 'unit' => 'px'], 'size_units' => ['px'], 'frontend_available' => \true, 'range' => ['px' => ['min' => -300, 'max' => 300]]]);
        $this->add_control('disp_scale', ['label' => esc_html__('Scale (%)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'frontend_available' => \true, 'size_units' => ['px'], 'default' => ['size' => '100'], 'range' => ['px' => ['min' => 0, 'max' => 300]]]);
        $this->add_control('disp_factor_to', ['label' => esc_html__('Depth Factor: TO', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '0', 'unit' => 'px'], 'frontend_available' => \true, 'condition' => ['svg_trigger' => ['rollover', 'scroll']], 'range' => ['px' => ['min' => 0, 'max' => 300]]]);
        $this->add_control('disp_scale_to', ['label' => esc_html__('Scale TO (%)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '100'], 'size_units' => ['px'], 'frontend_available' => \true, 'condition' => ['svg_trigger' => ['rollover', 'scroll']], 'range' => ['px' => ['min' => 0, 'max' => 300]]]);
        $this->add_control('random_animation', ['label' => esc_html__('Random animation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'separator' => 'before', 'frontend_available' => \true, 'condition' => ['svg_trigger' => 'animation']]);
        $this->add_control('random_animation_range', ['label' => esc_html__('Range of value', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'frontend_available' => \true, 'default' => ['size' => '100', 'unit' => 'px'], 'size_units' => ['px'], 'range' => ['px' => ['min' => 1, 'max' => 200]], 'condition' => ['svg_trigger' => 'animation', 'random_animation!' => '']]);
        $this->add_control('preserveAR', ['label' => esc_html__('Aspect ratio', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'separator' => 'before']);
        $this->add_control('preserveMode', ['label' => esc_html__('Ratio mode ', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['slice' => esc_html__('Crop', 'dynamic-content-for-elementor'), 'meet' => esc_html__('Keep', 'dynamic-content-for-elementor')], 'default' => 'slice', 'condition' => ['preserveAR' => 'yes']]);
        $this->end_controls_section();
        $this->start_controls_section('section_viewbox', ['label' => esc_html__('Viewbox', 'dynamic-content-for-elementor')]);
        $this->add_control('viewbox_width', ['label' => esc_html__('Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'label_block' => \false, 'default' => 600, 'min' => 100, 'max' => 2000, 'step' => 1]);
        $this->add_control('viewbox_height', ['label' => esc_html__('Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'label_block' => \false, 'default' => 600, 'min' => 100, 'max' => 2000, 'step' => 1]);
        $this->add_responsive_control('image_max_width', ['label' => esc_html__('Max-Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '', 'unit' => 'px'], 'size_units' => ['px', '%', 'vw'], 'range' => ['px' => ['min' => 0, 'max' => 1000], '%' => ['min' => 0, 'max' => 100], 'vw' => ['min' => 0, 'max' => 100]]]);
        $this->end_controls_section();
        $this->start_controls_section('section_style', ['label' => esc_html__('Style', 'dynamic-content-for-elementor')]);
        $this->add_responsive_control('svg_align', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'prefix_class' => 'align-', 'default' => 'left', 'selectors' => ['{{WRAPPER}} .dce_distortion-wrapper' => 'text-align: {{VALUE}};']]);
        $this->end_controls_section();
        $this->start_controls_section('section_source', ['label' => esc_html__('Source', 'dynamic-content-for-elementor'), 'condition' => ['svg_trigger' => 'static']]);
        $this->add_control('distortion_output', ['label' => esc_html__('Output', 'dynamic-content-for-elementor'), 'description' => esc_html__('Use distortion only for appling to other page elements. With this Option activated, the svg element will not be displayed.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '']);
        $this->add_control('id_svg_class', ['label' => esc_html__('CSS Class', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '', 'frontend_available' => \true, 'condition' => ['distortion_output' => 'yes']]);
        $this->add_control('note_idclass', ['type' => Controls_Manager::RAW_HTML, 'show_label' => \false, 'raw' => esc_html__('Here you can write the class of the element to trasform with the SVG distortion. Remember to write the class name on your element in advanced tab.', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning', 'separator' => 'after', 'condition' => ['distortion_output' => 'yes']]);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $widgetId = $this->get_id();
        $id_svg_class = $settings['id_svg_class'];
        $filterId = '';
        if ($settings['displacement_image']['url']) {
            $filterId = 'filter="url(#distortion-filter-' . esc_attr($widgetId) . ')"';
        }
        $image_size = $settings['image_size'];
        $image_url = Group_Control_Image_Size::get_attachment_image_src($settings['base_image']['id'], 'image', $settings);
        $dispimage_url = Group_Control_Image_Size::get_attachment_image_src($settings['displacement_image']['id'], 'image', $settings);
        $viewBoxW = $settings['viewbox_width'];
        $viewBoxH = $settings['viewbox_height'];
        $svg_trigger = $settings['svg_trigger'];
        $disp_factor = $settings['disp_factor']['size'];
        $scale_factor = $settings['disp_scale']['size'];
        $pos_factor = (100 - \intval($settings['disp_scale']['size'])) / 2;
        $data_run = 'paused';
        if ($svg_trigger == 'animation') {
            $data_run = $settings['playpause_control'];
        }
        $this->add_render_attribute('svgdistortion', ['class' => 'dce_distortion', 'data-coef' => 0.5, 'data-dispimage' => $dispimage_url]);
        $preserveAR = 'xMidYMid ' . $settings['preserveMode'];
        if (!$settings['preserveAR']) {
            $preserveAR = 'none';
        }
        ?>
		<div class="dce_distortion-wrapper">
			<div <?php 
        echo $this->get_render_attribute_string('svgdistortion');
        ?>>
			<svg  id="dce-svg-<?php 
        echo esc_attr($widgetId);
        ?>" data-run="<?php 
        echo esc_attr($data_run);
        ?>" class="dce-svg-distortion" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 <?php 
        echo esc_attr($viewBoxW);
        ?> <?php 
        echo esc_attr($viewBoxH);
        ?>">
				<defs>
				<filter id="distortion-filter-<?php 
        echo esc_attr($widgetId);
        ?>" x="0" y="0" width="100%" height="100%" filterUnits="objectBoundingBox" primitiveUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
					<feImage xlink:href="<?php 
        echo esc_url($dispimage_url);
        ?>" id="displacement-image" x="<?php 
        echo esc_attr((string) $pos_factor);
        ?>%" y="<?php 
        echo esc_attr((string) $pos_factor);
        ?>%" width="<?php 
        echo esc_attr((string) $scale_factor);
        ?>%" height="<?php 
        echo esc_attr((string) $scale_factor);
        ?>%" preserveAspectRatio="<?php 
        echo esc_attr($preserveAR);
        ?>" result="distortionImage" />

					<feDisplacementMap id="displacement-map"
								xChannelSelector="R"
								yChannelSelector="G"
								in="SourceGraphic"
								in2="distortionImage"
								result="displacementMap"
								color-interpolation-filters="sRGB"
								scale="<?php 
        echo esc_attr($disp_factor);
        ?>" />

					<feComposite operator="in" in2="distortionImage"></feComposite>
				</filter>
				</defs>

			<g id="item-distortion">
				<image id="img-distorted"
					preserveAspectRatio="<?php 
        echo esc_attr($preserveAR);
        ?>"
					width="100%"
					height="100%"
					xlink:href="<?php 
        echo esc_url($image_url);
        ?>" />

				<image id="img-distorted"
					preserveAspectRatio="<?php 
        echo esc_attr($preserveAR);
        ?>"
					width="100%"
					height="100%"
					xlink:href="<?php 
        echo esc_url($image_url);
        ?>"
					<?php 
        echo $filterId;
        ?> />
			</g>

			<style>
			<?php 
        if ($settings['image_max_width']['size'] && $settings['image_max_width']['size'] > 0) {
            ?>
				#dce-svg-<?php 
            echo esc_attr($widgetId);
            ?>{
					max-width: <?php 
            echo \intval($settings['image_max_width']['size']);
            ?>px;
				}
			<?php 
        }
        ?>

			<?php 
        if ($settings['distortion_output'] && $settings['id_svg_class'] != '') {
            ?>
				.<?php 
            echo esc_attr($id_svg_class);
            ?> svg > image,
				.<?php 
            echo esc_attr($id_svg_class);
            ?> svg > path,
				.<?php 
            echo esc_attr($id_svg_class);
            ?> svg > polyline,
				.<?php 
            echo esc_attr($id_svg_class);
            ?> img,
				.<?php 
            echo esc_attr($id_svg_class);
            ?> p,
				.<?php 
            echo esc_attr($id_svg_class);
            ?> .elementor-heading-title,
				.<?php 
            echo esc_attr($id_svg_class);
            ?> .elementor-icon i:before,
				.<?php 
            echo esc_attr($id_svg_class);
            ?> .elementor-button{
					-webkit-filter: url(#distortion-filter-<?php 
            echo esc_attr($widgetId);
            ?>);
					filter: url(#distortion-filter-<?php 
            echo esc_attr($widgetId);
            ?>);

				}
				#dce-svg-<?php 
            echo esc_attr($widgetId);
            ?>{
					position: absolute;
					width: 0;
					height: 0;
				}
			<?php 
        }
        ?>
			</style>


			</svg>
		</div>
		</div>
		<?php 
    }
    protected function content_template()
    {
        ?>
		<#
		var idWidget = id;
		var iFrameDOM = jQuery("iframe#elementor-preview-iframe").contents();
		var scope = iFrameDOM.find('.elementor-element[data-id='+idWidget+']');
		var id_svg_class = settings.id_svg_class;

		var viewBoxW = settings.viewbox_width;
		var viewBoxH = settings.viewbox_height;
		var maxWidth = settings.image_max_width.size;
		var dist_output = settings.distortion_output;

		var dispFactor = settings.disp_factor.size;
		var dispScale = settings.disp_scale.size;
		var pos_factor = ((100-(Number(settings.disp_scale.size)))/2);



		var baseImage = settings.base_image.url;


		var image = {
		id: settings.base_image.id,
		url: settings.base_image.url,
		size: settings.image_size,
		dimension: settings.image_custom_dimension,
		model: view.getEditModel()
		};
		var dispimage = {
		id: settings.displacement_image.id,
		url: settings.displacement_image.url,
		size: settings.image_size,
		dimension: settings.image_custom_dimension,
		model: view.getEditModel()
		};

		var image_url = elementor.imagesManager.getImageUrl( image );
		var dispimage_url = elementor.imagesManager.getImageUrl( dispimage );

		if ( ! image_url ) {
		return;
		}
		var svg_trigger = settings.svg_trigger;

		var data_run = 'paused';
		if(svg_trigger == 'animation'){
		data_run = settings.playpause_control;
		}

		view.addRenderAttribute( {
		'svgdistortion' : {
			'class' : [
			'dce_distortion',
			],
			'data-coef' : [
			0.5,
			],
			'data-dispimage' : [
			dispimage_url,
			],
		},
		});

		dce_getimageSizes(image_url, function (data) {
		// to do
		});

		var preserveAR = 'xMidYMid '+settings.preserveMode;
		if(!settings.preserveAR) preserveAR = 'none';


		#>
		<div class="dce_distortion-wrapper">

		<div {{{ view.getRenderAttributeString( 'svgdistortion') }}}>

			<svg id="dce-svg-{{idWidget}}" data-run="{{data_run}}" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 {{viewBoxW}} {{viewBoxH}}">
				<defs>
				<filter id="distortion-filter-{{idWidget}}" x="0" y="0" width="100%" height="100%" filterUnits="objectBoundingBox" primitiveUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
					<feImage xlink:href="{{dispimage_url}}" id="displacement-image" x="{{pos_factor}}%" y="{{pos_factor}}%" width="{{dispScale}}%" height="{{dispScale}}%" preserveAspectRatio="{{preserveAR}}" result="distortionImage" />
					<feDisplacementMap id="displacement-map"
									xChannelSelector="R"
									yChannelSelector="G"
									in="SourceGraphic"
									in2="distortionImage"
									result="displacementMap"
									color-interpolation-filters="sRGB"
									scale="{{dispFactor}}" />

					<feComposite operator="in" in2="distortionImage"></feComposite>
				</filter>
				</defs>
				<g id="item-distortion">
				<image id="img-base"
						preserveAspectRatio="{{preserveAR}}"
						width="100%"
						height="100%"
						xlink:href="{{image_url}}" />
				<# if(settings.displacement_image.url){ #>
				<image id="img-distorted"
						preserveAspectRatio="{{preserveAR}}"
						width="100%"
						height="100%"
						xlink:href="{{image_url}}"
						filter="url(#distortion-filter-{{idWidget}})" />
				<# } #>
			</g>


			<style>
			<# if( maxWidth && maxWidth > 0 ){ #>
				#dce-svg-{{idWidget}}{
					max-width: {{maxWidth}}px;
				}
				<# } #>

			<# if( dist_output && id_svg_class != '' ){ #>
				.{{id_svg_class}} svg > image,
				.{{id_svg_class}} svg  path,
				.{{id_svg_class}} svg polyline,
				.{{id_svg_class}} img,
				.{{id_svg_class}} p,
				.{{id_svg_class}} .elementor-heading-title,
				.{{id_svg_class}} .elementor-icon i:before,
				.{{id_svg_class}} .elementor-button
				{
					-webkit-filter: url(#distortion-filter-{{idWidget}});
					filter: url(#distortion-filter-{{idWidget}});

				}
				#dce-svg-{{idWidget}}{
					position: absolute;
					width: 0;
					height: 0;
				}
			<# } #>
			</style>


			</svg>
		</div>
		</div>
		<?php 
    }
}
