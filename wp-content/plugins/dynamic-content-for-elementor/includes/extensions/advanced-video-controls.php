<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class AdvancedVideoControls extends \DynamicContentForElementor\Extensions\ExtensionPrototype
{
    /**
     * @return bool
     */
    public function is_common()
    {
        return \false;
    }
    /**
     * @return array<string>
     */
    public function get_script_depends()
    {
        return ['dce-advanced-video'];
    }
    /**
     * @return array<string>
     */
    public function get_style_depends()
    {
        return ['dce-plyr', 'dce-advanced-video'];
    }
    /**
     * @return void
     */
    protected function add_actions()
    {
        add_action('elementor/frontend/section/before_render', function ($element) {
            $settings = $element->get_settings_for_display();
            $frontend_settings = $element->get_frontend_settings();
            if (empty($frontend_settings['background_video_link']) && $settings['background_video_link']) {
                \ob_start();
            }
        }, 10, 1);
        add_action('elementor/frontend/section/after_render', function ($element) {
            $settings = $element->get_settings_for_display();
            $frontend_settings = $element->get_frontend_settings();
            if (empty($frontend_settings['background_video_link']) && $settings['background_video_link']) {
                $content = \ob_get_contents();
                \ob_end_clean();
                if (\strpos($content, 'background_video_link') === \false) {
                    $content = \str_replace('&quot;background_background&quot;:&quot;video&quot;', '&quot;background_background&quot;:&quot;video&quot;,&quot;background_video_link&quot;:&quot;' . esc_attr(\substr((string) wp_json_encode((string) $settings['background_video_link']), 1, -1)) . '&quot;', $content);
                }
                echo $content;
            }
        }, 10, 1);
        add_filter('elementor/widget/render_content', [$this, 'render_video'], 10, 2);
        add_action('elementor/element/video/section_video_style/before_section_start', [$this, 'add_control_section_to_video'], 10, 2);
        add_action('elementor/element/video/section_video/before_section_end', function ($element, $args) {
            // Make the video settings available in the frontend
            $element->update_control('video_type', array('frontend_available' => \true));
            $element->update_control('autoplay', array('frontend_available' => \true));
            $element->update_control('mute', array('frontend_available' => \true));
            $element->update_control('loop', array('frontend_available' => \true));
            $element->update_control('lightbox', array('frontend_available' => \true));
        }, 10, 2);
    }
    public function add_control_section_to_video($element, $args)
    {
        $element->start_controls_section('dce_video_section', ['label' => esc_html__('Advanced', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT, 'condition' => ['video_type' => ['youtube', 'vimeo', 'hosted']]]);
        $element->add_control('dce_video_custom_controls', ['label' => '<span class="color-dce icon-dce-logo-dce"></span> ' . esc_html__('Custom Controls', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'render_type' => 'template', 'selectors' => ['{{WRAPPER}} .plyr, #elementor-lightbox-{{ID}} .plyr' => 'height: auto;', '{{WRAPPER}} input:focus:not([type="button"]):not([type="submit"]), #elementor-lightbox-{{ID}} input:focus:not([type="button"]):not([type="submit"])' => 'background-color: transprent; border: none; box-shadow: none;', '{{WRAPPER}} .plyr input[type="range"]::-moz-range-track, #elementor-lightbox-{{ID}} .plyr input[type="range"]::-moz-range-track, {{WRAPPER}} .plyr input[type="range"]::-moz-range-thumb' => 'box-shadow: none;']]);
        $element->add_control('dce_video_custom_controls_hover', ['label' => esc_html__('Show controls only in hover', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'render_type' => 'template', 'selectors' => ['{{WRAPPER}} .plyr:not(:hover) .plyr__controls, {{WRAPPER}} .plyr:not(:hover) .plyr__control' => 'opacity: 0; transition: 0.3s;'], 'condition' => ['dce_video_custom_controls!' => '']]);
        $element->add_control('dce_video_custom_controls_nodx', ['label' => '<span class="color-dce icon-dce-logo-dce"></span> ' . esc_html__('Prevent Video Download', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'render_type' => 'template', 'selectors' => ['{{WRAPPER}} .plyr__video-wrapper:after' => 'content: ""; display: block; position: absolute; left: 0; top: 0; width: 100%; height: 100%;'], 'condition' => ['dce_video_custom_controls!' => '', 'video_type' => 'hosted']]);
        $element->add_control('dce_video_controls', ['label' => esc_html__('Show these controls', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'multiple' => \true, 'label_block' => \true, 'options' => [
            'play-large' => esc_html__('play-large', 'dynamic-content-for-elementor'),
            // The large play button in the center
            'restart' => esc_html__('restart', 'dynamic-content-for-elementor'),
            // Restart playback
            'rewind' => esc_html__('rewind', 'dynamic-content-for-elementor'),
            // Rewind by the seek time (default 10 seconds)
            'play' => esc_html__('play', 'dynamic-content-for-elementor'),
            // Play/pause playback
            'fast-forward' => esc_html__('fast-forward', 'dynamic-content-for-elementor'),
            // Fast forward by the seek time (default 10 seconds)
            'progress' => esc_html__('progress', 'dynamic-content-for-elementor'),
            // The progress bar and scrubber for playback and buffering
            'current-time' => esc_html__('current-time', 'dynamic-content-for-elementor'),
            // The current time of playback
            'duration' => esc_html__('duration', 'dynamic-content-for-elementor'),
            // The full duration of the media
            'mute' => esc_html__('mute', 'dynamic-content-for-elementor'),
            // Toggle mute
            'volume' => esc_html__('volume', 'dynamic-content-for-elementor'),
            // Volume control
            'captions' => esc_html__('captions', 'dynamic-content-for-elementor'),
            // Toggle captions
            'settings' => esc_html__('settings', 'dynamic-content-for-elementor'),
            // Settings menu
            'pip' => esc_html__('pip', 'dynamic-content-for-elementor'),
            // Picture-in-picture (currently Safari only)
            'airplay' => esc_html__('airplay', 'dynamic-content-for-elementor'),
            // Airplay (currently Safari only)
            'download' => esc_html__('download', 'dynamic-content-for-elementor'),
            // Show a download button with a link to either the current source or a custom URL you specify in your options
            'fullscreen' => esc_html__('fullscreen', 'dynamic-content-for-elementor'),
        ], 'default' => ['mute', 'play-large', 'play', 'progress', 'current-time', 'volume', 'captions', 'settings', 'pip', 'airplay', 'fullscreen'], 'frontend_available' => \true, 'condition' => ['dce_video_custom_controls!' => '']]);
        $element->add_control('dce_captions_on_start', ['label' => esc_html__('Subtitles On by default', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['dce_video_custom_controls!' => '']]);
        $element->add_control('dce_captions_lang', ['label' => esc_html__('Subtitles Track', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => 'auto', 'frontend_available' => \true, 'condition' => ['dce_captions_on_start' => 'yes', 'dce_video_custom_controls' => 'yes']]);
        $element->end_controls_section();
        $element->start_controls_section('dce_video_style', ['label' => esc_html__('Custom Controls', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['dce_video_custom_controls!' => '']]);
        $element->add_control('dce_video_color', ['label' => esc_html__('Controls', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .plyr--video .plyr__control_item svg, #elementor-lightbox-{{ID}} .plyr--video .plyr__control_item svg' => 'fill: {{VALUE}}', '{{WRAPPER}} .plyr--video .plyr__controls, #elementor-lightbox-{{ID}} .plyr--video .plyr__controls' => 'color: {{VALUE}}', '{{WRAPPER}} .plyr--video .plyr__progress__buffer, #elementor-lightbox-{{ID}} .plyr--video .plyr__progress__buffer, {{WRAPPER}} .plyr--video .plyr__control--overlaid, #elementor-lightbox-{{ID}} .plyr--video .plyr__control--overlaid' => 'background-color: {{VALUE}}']]);
        $element->add_control('dce_video_bgcolor', ['label' => esc_html__('Backgrounds buttons Controls', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .plyr--video .plyr__controls .plyr__control, #elementor-lightbox-{{ID}} .plyr--video .plyr__controls .plyr__control' => 'background-color: {{VALUE}}']]);
        $element->add_control('dce_video_color_hover', ['label' => esc_html__('Controls Color Hover', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .plyr--video .plyr__control.plyr__tab-focus, #elementor-lightbox-{{ID}} .plyr--video .plyr__control.plyr__tab-focus, {{WRAPPER}} .plyr--video .plyr__control:hover, #elementor-lightbox-{{ID}} .plyr--video .plyr__control:hover, {{WRAPPER}} .plyr--video .plyr__control[aria-expanded="true"], #elementor-lightbox-{{ID}} .plyr--video .plyr__control[aria-expanded="true"]' => 'background-color: {{VALUE}}']]);
        $element->add_control('dce_video_progress_color', ['label' => esc_html__('Progress Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .plyr--full-ui input[type="range"], #elementor-lightbox-{{ID}} .plyr--full-ui input[type="range"]' => 'color: {{VALUE}}']]);
        $element->add_control('dce_video_controlsbackground_color', ['label' => esc_html__('Controls Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .plyr--video .plyr__controls, #elementor-lightbox-{{ID}}' => 'background: {{VALUE}}']]);
        $element->add_responsive_control('dce_video_videostyle_border', ['label' => esc_html__('Controls Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .plyr--video .plyr__controls, #elementor-lightbox-{{ID}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $element->add_control('dce_video_play_heading', ['label' => esc_html__('Play Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $element->add_control('dce_video_play_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .plyr--video .plyr__control.plyr__control--overlaid' => 'background-color: {{VALUE}}']]);
        $element->add_control('dce_video_play_icon_color', ['label' => esc_html__('Icon Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .plyr__control.plyr__control--overlaid svg' => 'fill: {{VALUE}}']]);
        $element->add_group_control(Group_Control_Border::get_type(), ['name' => 'dce_video_play_border', 'label' => esc_html__('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .plyr__control.plyr__control--overlaid']);
        $element->add_responsive_control('dce_video_play_size', ['label' => esc_html__('Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'range' => ['px' => ['min' => 10, 'max' => 140, 'step' => 1]], 'render_type' => 'ui', 'selectors' => ['{{WRAPPER}} .plyr__control.plyr__control--overlaid svg' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important;', '{{WRAPPER}} .plyr__control--overlaid' => 'padding: {{SIZE}}{{UNIT}} !important; display: block;']]);
        $element->add_responsive_control('dce_video_play_radius', ['label' => esc_html__('Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'size_units' => ['px', '%'], 'range' => ['px' => ['min' => 0, 'max' => 140, 'step' => 1], '%' => ['min' => 0, 'max' => 100, 'step' => 1]], 'render_type' => 'ui', 'selectors' => ['{{WRAPPER}} .plyr__control.plyr__control--overlaid' => 'border-radius: {{SIZE}}{{UNIT}};']]);
        $element->add_control('dce_video_videostyle_heading', ['label' => esc_html__('Video style', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $element->add_control('dce_video_videostyle_bgcolor', ['label' => esc_html__('Background color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .plyr--video' => 'background-color: {{VALUE}}']]);
        $element->add_responsive_control('dce_video_videostyle_radius', ['label' => esc_html__('Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => ''], 'size_units' => ['px', '%'], 'range' => ['px' => ['min' => 0, 'max' => 140, 'step' => 1], '%' => ['min' => 0, 'max' => 100, 'step' => 1]], 'render_type' => 'ui', 'selectors' => ['{{WRAPPER}} .plyr' => 'border-radius: {{SIZE}}{{UNIT}};']]);
        $element->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'dce_video_videostyle_shadow', 'selector' => '{{WRAPPER}} .plyr']);
        $element->add_group_control(Group_Control_Border::get_type(), ['name' => 'dce_video_videostyle_border', 'label' => esc_html__('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .plyr']);
        $element->end_controls_section();
    }
    /**
     * @param string $content
     * @param \Elementor\Widget_Base $widget
     * @return string
     */
    public function render_video($content, $widget)
    {
        if ('video' === $widget->get_name()) {
            $settings = $widget->get_settings();
            if (\Elementor\Plugin::$instance->editor->is_edit_mode() || !empty($settings['dce_video_custom_controls'])) {
                $this->enqueue_all();
            }
        }
        return $content;
    }
}
