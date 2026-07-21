<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Parallax extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    /**
     * @return array<string>
     */
    public function get_script_depends()
    {
        return ['dce-parallaxjs-lib', 'dce-parallax-js'];
    }
    /**
     * @return array<string>
     */
    public function get_style_depends()
    {
        return ['dce-parallax'];
    }
    /**
     * @param int $index
     * @return string
     */
    protected function get_placeholder_svg($index)
    {
        $size = 100 + $index * 20;
        $color = \sprintf('#%06X', \mt_rand(0, 0xffffff));
        return 'data:image/svg+xml,' . \rawurlencode('<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 ' . $size . ' ' . $size . '" xmlns="http://www.w3.org/2000/svg"><rect width="100%" height="100%" fill="' . $color . '" opacity="0.2"/><text x="50%" y="50%" font-size="14" text-anchor="middle" fill="#000000">Layer ' . ($index + 1) . '</text></svg>');
    }
    /**
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_parallaxsettings', ['label' => $this->get_title()]);
        $this->add_control('parallaxjs_relative_input', ['label' => esc_html__('Relative Input', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '']);
        $this->add_control('parallaxjs_clip_relative_input', ['label' => esc_html__('Clip Relative Input', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '']);
        $this->add_control('parallaxjs_hover_only', ['label' => esc_html__('Hover Only', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '']);
        $this->add_control('parallaxjs_input_element', ['label' => esc_html__('Input Element', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '#myinput']);
        $this->add_control('parallaxjs_calibrate_x', ['label' => esc_html__('Calibrate X', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '']);
        $this->add_control('parallaxjs_calibrate_y', ['label' => esc_html__('Calibrate Y', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '']);
        $this->add_control('parallaxjs_invert_x', ['label' => esc_html__('Invert X', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '']);
        $this->add_control('parallaxjs_invert_y', ['label' => esc_html__('Invert Y', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '']);
        $this->add_control('parallaxjs_limit_x', ['label' => esc_html__('Limit X', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 100, 'min' => 0, 'max' => 1000, 'step' => 10]);
        $this->add_control('parallaxjs_limit_y', ['label' => esc_html__('Limit Y', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 100, 'min' => 0, 'max' => 1000, 'step' => 10]);
        $this->add_control('parallaxjs_scalar_x', ['label' => esc_html__('Scalar X', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 2], 'range' => ['min' => 0, 'max' => 100]]);
        $this->add_control('parallaxjs_scalar_y', ['label' => esc_html__('Scalar Y', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 8], 'range' => ['min' => 0, 'max' => 100]]);
        $this->add_control('parallaxjs_friction_x', ['label' => esc_html__('Friction X', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 0.1, 'min' => 0, 'max' => 1, 'step' => 0.1]);
        $this->add_control('parallaxjs_friction_y', ['label' => esc_html__('Friction Y', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 0.1, 'min' => 0, 'max' => 1, 'step' => 0.1]);
        $this->add_control('parallaxjs_origin_x', ['label' => esc_html__('Origin X', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 0.5, 'min' => 0, 'max' => 1, 'step' => 0.1]);
        $this->add_control('parallaxjs_origin_y', ['label' => esc_html__('Origin Y', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 0.5, 'min' => 0, 'max' => 1, 'step' => 0.1]);
        $this->add_control('parallaxjs_pointer_events', ['label' => esc_html__('Pointer Events', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '']);
        $this->end_controls_section();
        $this->start_controls_section('section_parallaxitems', ['label' => esc_html__('Parallax Items', 'dynamic-content-for-elementor')]);
        $this->add_control('parallax_coef', ['label' => esc_html__('Default depth factor', 'dynamic-content-for-elementor'), 'description' => esc_html__('It is used when the DepthFactor value is 0', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 0.2, 'min' => 0.05, 'max' => 1, 'step' => 0.05]);
        $repeater = new Repeater();
        $repeater->add_control('parallax_image', ['label' => esc_html__('Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::MEDIA, 'default' => ['url' => '']]);
        $repeater->add_control('factor_item', ['label' => esc_html__('Depth Factor', 'dynamic-content-for-elementor'), 'description' => esc_html__('If 0, the default value is used', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 0, 'min' => -1, 'max' => 1, 'step' => 0.01]);
        $this->add_control('parallaxjs', ['label' => esc_html__('Items', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::REPEATER, 'default' => [], 'fields' => $repeater->get_controls(), 'title_field' => esc_html__('Parallax Item', 'dynamic-content-for-elementor')]);
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
        if (empty($settings['parallaxjs']) && \Elementor\Plugin::$instance->editor->is_edit_mode()) {
            Helper::notice(\false, esc_html__('Add at least one parallax item', 'dynamic-content-for-elementor'));
            return;
        }
        $this->add_render_attribute('container', ['class' => 'container', 'id' => 'container']);
        $this->add_render_attribute('scene', ['id' => 'scene', 'class' => 'scene', 'data-relative-input' => $settings['parallaxjs_relative_input'] === 'yes' ? 'true' : 'false', 'data-clip-relative-input' => $settings['parallaxjs_clip_relative_input'] === 'yes' ? 'true' : 'false', 'data-hover-only' => $settings['parallaxjs_hover_only'] === 'yes' ? 'true' : 'false', 'data-input-element' => '#myinput', 'data-calibrate-x' => $settings['parallaxjs_calibrate_x'] === 'yes' ? 'true' : 'false', 'data-calibrate-y' => $settings['parallaxjs_calibrate_y'] === 'yes' ? 'true' : 'false', 'data-invert-x' => $settings['parallaxjs_invert_x'] === 'yes' ? 'true' : 'false', 'data-invert-y' => $settings['parallaxjs_invert_y'] === 'yes' ? 'true' : 'false', 'data-limit-x' => $settings['parallaxjs_limit_x'], 'data-limit-y' => $settings['parallaxjs_limit_y'], 'data-scalar-x' => $settings['parallaxjs_scalar_x']['size'], 'data-scalar-y' => $settings['parallaxjs_scalar_y']['size'], 'data-friction-x' => $settings['parallaxjs_friction_x'], 'data-friction-y' => $settings['parallaxjs_friction_y'], 'data-origin-x' => $settings['parallaxjs_origin_x'], 'data-origin-y' => $settings['parallaxjs_origin_y'], 'data-precision' => '1', 'data-pointer-events' => $settings['parallaxjs_pointer_events'] === 'yes' ? 'true' : 'false']);
        ?>
		<div <?php 
        echo $this->get_render_attribute_string('container');
        ?>>
			<div <?php 
        echo $this->get_render_attribute_string('scene');
        ?>>
				<?php 
        $parallaxItems = $settings['parallaxjs'];
        if (!empty($parallaxItems)) {
            foreach ($parallaxItems as $key => $parallaxitem) {
                $factor = $parallaxitem['factor_item'];
                if ($factor == 0) {
                    $coef = \is_numeric($settings['parallax_coef']) ? $settings['parallax_coef'] : 0.2;
                    $factor = $key * $coef;
                }
                $image = $parallaxitem['parallax_image']['url'] ?? $this->get_placeholder_svg($key);
                $this->add_render_attribute('layer-' . $key, ['class' => 'layer', 'data-depth' => $factor]);
                ?>
						<div <?php 
                echo $this->get_render_attribute_string('layer-' . $key);
                ?>>
							<img src="<?php 
                echo esc_url($image);
                ?>">
						</div>
						<?php 
            }
        }
        ?>
			</div>
		</div>
		<?php 
    }
    protected function content_template()
    {
        ?>
		<# if ( settings.parallaxjs.length ) { #>
		<div id="container" class="container">
			<div id="scene" class="scene"
				data-relative-input="{{settings.parallaxjs_relative_input ? 'true' : 'false'}}"
				data-clip-relative-input="{{settings.parallaxjs_clip_relative_input ? 'true' : 'false'}}"
				data-hover-only="{{settings.parallaxjs_hover_only ? 'true' : 'false'}}"
				data-input-element="#myinput"
				data-calibrate-x="{{settings.parallaxjs_calibrate_x ? 'true' : 'false'}}"
				data-calibrate-y="{{settings.parallaxjs_calibrate_y ? 'true' : 'false'}}"
				data-invert-x="{{settings.parallaxjs_invert_x ? 'true' : 'false'}}"
				data-invert-y="{{settings.parallaxjs_invert_y ? 'true' : 'false'}}"
				data-limit-x="{{settings.parallaxjs_limit_x}}"
				data-limit-y="{{settings.parallaxjs_limit_y}}"
				data-scalar-x="{{settings.parallaxjs_scalar_x.size}}"
				data-scalar-y="{{settings.parallaxjs_scalar_y.size}}"
				data-friction-x="{{settings.parallaxjs_friction_x}}"
				data-friction-y="{{settings.parallaxjs_friction_y}}"
				data-origin-x="{{settings.parallaxjs_origin_x}}"
				data-origin-y="{{settings.parallaxjs_origin_y}}"
				data-precision="1"
				data-pointer-events="{{settings.parallaxjs_pointer_events ? 'true' : 'false'}}">
				<# _.each( settings.parallaxjs, function( parallaxitem, index ) {
					var factor = parallaxitem.factor_item;
					if (factor == 0) {
						factor = index * settings.parallax_coef;
					}
					
					var svg = 'data:image/svg+xml,' + encodeURIComponent('<svg width="' + (100 + (index * 20)) + '" height="' + (100 + (index * 20)) + '" viewBox="0 0 ' + (100 + (index * 20)) + ' ' + (100 + (index * 20)) + '" xmlns="http://www.w3.org/2000/svg"><rect width="100%" height="100%" fill="#' + Math.floor(Math.random()*16777215).toString(16) + '" opacity="0.2"/><text x="50%" y="50%" font-size="14" text-anchor="middle" fill="#000000">Layer ' + (index + 1) + '</text></svg>');
					
					var imageParallaxItem = parallaxitem.parallax_image.url || svg;
					#>
					<div class="layer" data-depth="{{factor}}"><img src="{{imageParallaxItem}}"></div>
				<# }); #>
			</div>
		</div>
		<# } #>
		<?php 
    }
}
