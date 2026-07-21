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
class ImagesDistortionHover extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_script_depends()
    {
        return ['dce-imagesdistortion-js'];
    }
    public function get_style_depends()
    {
        return ['dce-imagesDistortion'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_distortion', ['label' => $this->get_title()]);
        $this->add_control('distortion_effect', ['label' => esc_html__('Distortion effect', 'dynamic-content-for-elementor'), 'type' => 'images_selector', 'toggle' => \false, 'type_selector' => 'image', 'columns_grid' => 4, 'options' => ['drip' => ['title' => esc_html__('Drip', 'dynamic-content-for-elementor'), 'return_val' => 'val', 'image' => DCE_URL . 'assets/img/distortions/drip.jpg'], 'wave' => ['title' => esc_html__('Wave', 'dynamic-content-for-elementor'), 'return_val' => 'val', 'image' => DCE_URL . 'assets/img/distortions/wave.jpg'], 'ring' => ['title' => esc_html__('Ring', 'dynamic-content-for-elementor'), 'return_val' => 'val', 'image' => DCE_URL . 'assets/img/distortions/ring.jpg'], 'horizdisp' => ['title' => esc_html__('Horizontal displacement', 'dynamic-content-for-elementor'), 'return_val' => 'val', 'image' => DCE_URL . 'assets/img/distortions/horizdisp.jpg'], 'vertdisp' => ['title' => esc_html__('Vertical displacement', 'dynamic-content-for-elementor'), 'return_val' => 'val', 'image' => DCE_URL . 'assets/img/distortions/vertdisp.jpg'], 'displacement' => ['title' => esc_html__('Displacement', 'dynamic-content-for-elementor'), 'return_val' => 'val', 'image' => DCE_URL . 'assets/img/distortions/displacement.jpg'], 'subdivision' => ['title' => esc_html__('subdivision', 'dynamic-content-for-elementor'), 'return_val' => 'val', 'image' => DCE_URL . 'assets/img/distortions/subdivision.jpg'], 'blow' => ['title' => esc_html__('Blow', 'dynamic-content-for-elementor'), 'return_val' => 'val', 'image' => DCE_URL . 'assets/img/distortions/blow.jpg']], 'default' => 'drip']);
        $this->add_control('width_distortion', ['label' => esc_html__('Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0.5], 'range' => ['px' => ['min' => 0, 'max' => 10, 'step' => 0.1]], 'condition' => ['distortion_effect' => 'drip']]);
        $this->add_control('scalex_distortion', ['label' => esc_html__('ScaleX', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 40], 'range' => ['px' => ['min' => 0.1, 'max' => 60, 'step' => 0.1]], 'condition' => ['distortion_effect' => 'drip']]);
        $this->add_control('scaley_distortion', ['label' => esc_html__('ScaleY', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 40], 'range' => ['px' => ['min' => 0.1, 'max' => 60, 'step' => 0.1]], 'condition' => ['distortion_effect' => 'drip']]);
        $this->add_control('width_distortion_wave', ['label' => esc_html__('Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0.5], 'range' => ['px' => ['min' => 0, 'max' => 10, 'step' => 0.1]], 'condition' => ['distortion_effect' => 'wave']]);
        $this->add_control('width_distortion_ring', ['label' => esc_html__('Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0.35], 'range' => ['px' => ['min' => 0, 'max' => 1, 'step' => 0.01]], 'condition' => ['distortion_effect' => 'ring']]);
        $this->add_control('radius_distortion_ring', ['label' => esc_html__('Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0.9], 'range' => ['px' => ['min' => 0.1, 'max' => 2, 'step' => 0.1]], 'condition' => ['distortion_effect' => 'ring']]);
        $this->add_control('intensity_distortion_vertdisp', ['label' => esc_html__('Intensity', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0.3], 'range' => ['px' => ['min' => 0, 'max' => 2, 'step' => 0.1]], 'condition' => ['distortion_effect' => ['vertdisp', 'displacement']]]);
        $this->add_control('intensity_distortion_subdivisionblow', ['label' => esc_html__('Intensity', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 50], 'range' => ['px' => ['min' => 1, 'max' => 100, 'step' => 1]], 'condition' => ['distortion_effect' => ['subdivision', 'blow']]]);
        $this->add_control('speed_distortion', ['label' => esc_html__('Speed', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'separator' => 'before', 'default' => ['size' => 1.6], 'range' => ['px' => ['min' => 0, 'max' => 5, 'step' => 0.01]]]);
        $this->add_control('easing_distortion', ['label' => esc_html__('Easing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_ease(), 'default' => 'easeInOut', 'label_block' => \false]);
        $this->add_control('hr3', ['type' => Controls_Manager::DIVIDER, 'style' => 'thick']);
        $this->add_control('image_1', ['label' => esc_html__('Before Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::MEDIA, 'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()]]);
        $this->add_control('image_2', ['label' => esc_html__('After image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::MEDIA, 'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()]]);
        $this->add_group_control(Group_Control_Image_Size::get_type(), ['name' => 'size', 'label' => esc_html__('Image Size', 'dynamic-content-for-elementor'), 'default' => 'large']);
        $this->add_control('image_displacement', ['label' => esc_html__('Displacement Image', 'dynamic-content-for-elementor'), 'type' => 'images_selector', 'toggle' => \false, 'type_selector' => 'image', 'description' => esc_html__('Displacement image map, generates the movement of the pixels', 'dynamic-content-for-elementor'), 'columns_grid' => 4, 'options' => ['disp1' => ['title' => 'Displacement 1', 'image' => DCE_URL . 'assets/displacement/1.jpg', 'image_preview' => DCE_URL . 'assets/displacement/low/1.jpg'], 'disp2' => ['title' => 'Displacement 2', 'image' => DCE_URL . 'assets/displacement/2.jpg', 'image_preview' => DCE_URL . 'assets/displacement/low/2.jpg'], 'disp3' => ['title' => 'Displacement 3', 'image' => DCE_URL . 'assets/displacement/3.jpg', 'image_preview' => DCE_URL . 'assets/displacement/low/3.jpg'], 'disp4' => ['title' => 'Displacement 4', 'image' => DCE_URL . 'assets/displacement/4.jpg', 'image_preview' => DCE_URL . 'assets/displacement/low/4.jpg'], 'disp5' => ['title' => 'Displacement 5', 'image' => DCE_URL . 'assets/displacement/5.jpg', 'image_preview' => DCE_URL . 'assets/displacement/low/5.jpg'], 'disp6' => ['title' => 'Displacement 6', 'image' => DCE_URL . 'assets/displacement/6.jpg', 'image_preview' => DCE_URL . 'assets/displacement/low/6.jpg'], 'disp7' => ['title' => 'Displacement 7', 'image' => DCE_URL . 'assets/displacement/7.jpg', 'image_preview' => DCE_URL . 'assets/displacement/low/7.jpg'], 'disp8' => ['title' => 'Displacement 8', 'image' => DCE_URL . 'assets/displacement/8.jpg', 'image_preview' => DCE_URL . 'assets/displacement/low/8.jpg'], 'disp9' => ['title' => 'Displacement 9', 'image' => DCE_URL . 'assets/displacement/9.jpg', 'image_preview' => DCE_URL . 'assets/displacement/low/9.jpg'], 'disp10' => ['title' => 'Displacement 10', 'image' => DCE_URL . 'assets/displacement/10.jpg', 'image_preview' => DCE_URL . 'assets/displacement/low/10.jpg'], 'disp11' => ['title' => 'Displacement 11', 'image' => DCE_URL . 'assets/displacement/11.jpg', 'image_preview' => DCE_URL . 'assets/displacement/low/11.jpg'], 'disp12' => ['title' => 'Displacement 12', 'image' => DCE_URL . 'assets/displacement/12.jpg', 'image_preview' => DCE_URL . 'assets/displacement/low/12.jpg'], 'disp14' => ['title' => 'Displacement 14', 'image' => DCE_URL . 'assets/displacement/14.jpg', 'image_preview' => DCE_URL . 'assets/displacement/low/14.jpg'], 'disp15' => ['title' => 'Displacement 15', 'image' => DCE_URL . 'assets/displacement/15.jpg', 'image_preview' => DCE_URL . 'assets/displacement/low/15.jpg'], 'disp16' => ['title' => 'Displacement 16', 'image' => DCE_URL . 'assets/displacement/16.jpg', 'image_preview' => DCE_URL . 'assets/displacement/low/16.jpg'], 'disp17' => ['title' => 'Displacement 17', 'image' => DCE_URL . 'assets/displacement/17.jpg', 'image_preview' => DCE_URL . 'assets/displacement/low/17.jpg'], 'disp18' => ['title' => 'Displacement 18', 'image' => DCE_URL . 'assets/displacement/18.jpg', 'image_preview' => DCE_URL . 'assets/displacement/low/18.jpg'], 'disp19' => ['title' => 'Displacement 19', 'image' => DCE_URL . 'assets/displacement/19.jpg', 'image_preview' => DCE_URL . 'assets/displacement/low/19.jpg'], 'disp_custom' => ['title' => 'Displacement Custom', 'return_val' => 'val', 'image' => DCE_URL . 'assets/displacement/custom.jpg', 'image_preview' => DCE_URL . 'assets/displacement/custom.jpg']], 'default' => \Elementor\Utils::get_placeholder_image_src(), 'condition' => ['distortion_effect' => ['ring ', 'horizdisp', 'displacement']]]);
        $this->add_control('displacementImage', ['label' => esc_html__('Displacement Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::MEDIA, 'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()], 'condition' => ['image_displacement' => 'disp_custom']]);
        $this->add_control('hr1', ['type' => Controls_Manager::DIVIDER, 'style' => 'thick']);
        $this->add_control('link_to', ['label' => esc_html__('Link to', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'none', 'options' => ['none' => esc_html__('None', 'dynamic-content-for-elementor'), 'home' => esc_html__('Home URL', 'dynamic-content-for-elementor'), 'custom' => esc_html__('Custom URL', 'dynamic-content-for-elementor')]]);
        $this->add_control('link', ['label' => esc_html__('Link to', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::URL, 'placeholder' => esc_html__('https://your-link.com', 'dynamic-content-for-elementor'), 'condition' => ['link_to' => 'custom'], 'show_label' => \false]);
        $this->end_controls_section();
        $this->start_controls_section('section_style', ['label' => esc_html__('Image', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('align_image', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['flex-start' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-center'], 'flex-end' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-right']], 'selectors' => ['{{WRAPPER}} .dce_distortion' => 'justify-content: {{VALUE}};']]);
        $this->add_responsive_control('size_height_image', ['label' => esc_html__('Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['%', 'px', 'vh'], 'range' => ['%' => ['min' => 1, 'max' => 100, 'step' => 1], 'vh' => ['min' => 1, 'max' => 100, 'step' => 1], 'px' => ['min' => 1, 'max' => 800, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce_distortion-content' => 'height: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('size_width_image', ['label' => esc_html__('Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['%', 'px', 'vw'], 'range' => ['%' => ['min' => 1, 'max' => 100, 'step' => 1], 'vw' => ['min' => 1, 'max' => 100, 'step' => 1], 'px' => ['min' => 1, 'max' => 800, 'step' => 1]], 'selectors' => ['{{WRAPPER}} .dce_distortion-content' => 'width: {{SIZE}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'image_border', 'label' => esc_html__('Image Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce_distortion-content']);
        $this->add_control('image_border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce_distortion-content' => 'overflow: hidden; border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_responsive_control('image_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce_distortion' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'image_box_shadow', 'selector' => '{{WRAPPER}} .dce_distortion-content']);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $fragmentStyle = $settings['distortion_effect'];
        $image1_url = $settings['image_1']['id'] ? Group_Control_Image_Size::get_attachment_image_src($settings['image_1']['id'], 'size', $settings) : $settings['image_1']['url'];
        $image2_url = $settings['image_2']['id'] ? Group_Control_Image_Size::get_attachment_image_src($settings['image_2']['id'], 'size', $settings) : $settings['image_2']['url'];
        $displacement_url = $settings['image_displacement'];
        $displacement_datastring = ' data-disp=""';
        if ($settings['image_displacement'] == 'disp_custom') {
            $displacement_url = Group_Control_Image_Size::get_attachment_image_src($settings['displacementImage']['id'], 'size', $settings);
        }
        if ($displacement_url) {
            $displacement_datastring = ' data-disp="' . esc_url($displacement_url) . '"';
        }
        $speed_distortion = $settings['speed_distortion']['size'];
        $easing_distortion = $settings['easing_distortion'];
        $progress_distortion = '';
        $data = ['intensity' => '', 'radius' => '', 'width' => '', 'scalex' => '', 'scaley' => ''];
        if (\in_array($settings['distortion_effect'], ['subdivision', 'blow'], \true)) {
            $data['intensity'] = $settings['intensity_distortion_subdivisionblow']['size'];
        } elseif (\in_array($settings['distortion_effect'], ['vertdisp', 'displacement'], \true)) {
            $data['intensity'] = $settings['intensity_distortion_vertdisp']['size'];
        }
        if ($settings['distortion_effect'] === 'ring') {
            $data['radius'] = $settings['radius_distortion_ring']['size'];
            $data['width'] = $settings['width_distortion_ring']['size'];
        } elseif ($settings['distortion_effect'] === 'wave') {
            $data['width'] = $settings['width_distortion_wave']['size'];
        } elseif ($settings['distortion_effect'] === 'drip') {
            $data['scaley'] = $settings['scaley_distortion']['size'];
            $data['scalex'] = $settings['scalex_distortion']['size'];
            $data['width'] = $settings['width_distortion']['size'];
        }
        $datastring = '';
        foreach ($data as $key => $value) {
            if (!empty($value)) {
                $datastring .= ' data-' . $key . '="' . $value . '"';
            }
        }
        echo '<div class="dce_distortion">';
        echo '<div class="dce_distortion-content">';
        $this->add_render_attribute('distortion-slider', ['class' => 'dce_distortion-slider', 'data-fragment-style' => $fragmentStyle, 'data-progress' => $progress_distortion, 'data-speed' => $speed_distortion, 'data-easing' => $easing_distortion, 'data-images' => wp_json_encode([$image1_url, $image2_url]), 'data-disp' => $displacement_url]);
        if ($settings['distortion_effect'] == 'subdivision' || $settings['distortion_effect'] == 'blow') {
            $this->add_render_attribute('distortion-slider', 'data-intensity', $settings['intensity_distortion_subdivisionblow']['size']);
        }
        if ($settings['distortion_effect'] == 'vertdisp' || $settings['distortion_effect'] == 'displacement') {
            $this->add_render_attribute('distortion-slider', 'data-intensity', $settings['intensity_distortion_vertdisp']['size']);
        }
        if ($settings['distortion_effect'] == 'ring') {
            $this->add_render_attribute('distortion-slider', 'data-radius', $settings['radius_distortion_ring']['size']);
            $this->add_render_attribute('distortion-slider', 'data-width', $settings['width_distortion_ring']['size']);
        }
        if ($settings['distortion_effect'] == 'wave') {
            $this->add_render_attribute('distortion-slider', 'data-width', $settings['width_distortion_wave']['size']);
        }
        if ($settings['distortion_effect'] == 'drip') {
            $this->add_render_attribute('distortion-slider', 'data-scaley', $settings['scaley_distortion']['size']);
            $this->add_render_attribute('distortion-slider', 'data-scalex', $settings['scalex_distortion']['size']);
            $this->add_render_attribute('distortion-slider', 'data-width', $settings['width_distortion']['size']);
        }
        echo '<div ' . $this->get_render_attribute_string('distortion-slider') . '>';
        echo '</div>';
        switch ($settings['link_to']) {
            case 'custom':
                if (!empty($settings['link']['url'])) {
                    $link = esc_url($settings['link']['url']);
                } else {
                    $link = \false;
                }
                break;
            case 'home':
                $link = esc_url(get_home_url());
                break;
            case 'none':
            default:
                $link = \false;
                break;
        }
        if ($link) {
            $attrs = ['href' => $link, 'class' => 'dce-link-distortion'];
            if (!empty($settings['link'])) {
                if ($settings['link']['is_external']) {
                    $attrs['target'] = '_blank';
                }
                if ($settings['link']['nofollow']) {
                    $attrs['rel'] = 'nofollow';
                }
            }
            \printf('<a %s></a>', \implode(' ', \array_map(function ($key, $value) {
                return \sprintf('%s="%s"', $key, esc_attr($value));
            }, \array_keys($attrs), $attrs)));
        }
        echo '</div>';
        echo '</div>';
    }
    protected function content_template()
    {
        ?>
		<#
		var fragmentStyle = settings.distortion_effect;

		var image1 = {
			id: settings.image_1.id,
			url: settings.image_1.url,
			size: settings.size,
			dimension: settings.image_custom_dimension,
			model: view.getEditModel()
		};
		var image2 = {
			id: settings.image_2.id,
			url: settings.image_2.url,
			size: settings.size,
			dimension: settings.image_custom_dimension,
			model: view.getEditModel()
		};
		var imageDisplacement = {
			id: settings.displacementImage.id,
			url: settings.displacementImage.url,
			size: settings.size,
			dimension: settings.image_custom_dimension,
			model: view.getEditModel()
		};
		var url_image1 = elementor.imagesManager.getImageUrl( image1 );
		var url_image2 = elementor.imagesManager.getImageUrl( image2 );

		var url_displacement = settings.image_displacement;
		if(settings.image_displacement == 'disp_custom'){
			url_displacement = elementor.imagesManager.getImageUrl( imageDisplacement )
		}


		var speed_distortion = settings.speed_distortion.size;
		var progress_distortion = ''; //settings.progress_distortion.size;
		var easing_distortion = settings.easing_distortion;

		// params
		var intensity_datastring = '';
		var radius_datastring = '';
		var width_datastring = '';
		var scalex_datastring = '';
		var scaley_datastring = '';

		var intensity_distortion_subdivisionblow = 50; // 'subdivision','blow'
		if(settings.distortion_effect == 'subdivision' || settings.distortion_effect == 'blow'){
			intensity_datastring = settings.intensity_distortion_subdivisionblow.size;
		}
		var intensity_distortion_vertdisp = 0.3; //vertdisp','displacement
		if(settings.distortion_effect == 'vertdisp' || settings.distortion_effect == 'displacement'){
			intensity_datastring = settings.intensity_distortion_vertdisp.size;
		}
		var radius_distortion_ring = 0.9; // ring
		if(settings.distortion_effect == 'ring'){
			radius_datastring = settings.radius_distortion_ring.size;
		}
		var width_distortion_ring = 0.35; // ring
		if(settings.distortion_effect == 'ring'){
			width_datastring = settings.width_distortion_ring.size;
		}
		var width_distortion_wave = 0.5; // wave
		if(settings.distortion_effect == 'wave'){
			width_datastring = settings.width_distortion_wave.size;
		}
		var scaley_distortion = 40; // drip
		if(settings.distortion_effect == 'drip'){
			scaley_datastring = settings.scaley_distortion.size;
		}
		var scalex_distortion = 40; // drip
		if(settings.distortion_effect == 'drip'){
			scalex_datastring = settings.scalex_distortion.size;
		}
		var width_distortion = 0.5; // 'drip','wave'
		if(settings.distortion_effect == 'drip'){
			width_datastring = settings.width_distortion.size;
		}

		#>
		<div class="dce_distortion">
			<div class="dce_distortion-content">
				<div class="dce_distortion-slider" data-fragment-style="{{fragmentStyle}}" data-progress="{{progress_distortion}}" data-speed="{{speed_distortion}}" data-easing="{{easing_distortion}}" data-images='["{{url_image1}}","{{url_image2}}"]' data-disp="{{url_displacement}}" data-intensity="{{intensity_datastring}}" data-radius="{{radius_datastring}}" data-width="{{width_datastring}}" data-scalex="{{scalex_datastring}}" data-scaley="{{scaley_datastring}}">
			</div>
		</div>
		<?php 
    }
}
