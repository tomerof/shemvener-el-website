<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

use Elementor\Icons_Manager;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class QrAndBarcodes extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    /**
     * Cache key prefix for barcode transients
     */
    const CACHE_KEY_PREFIX = 'dce_barcode_';
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $types1d = ['C39', 'C39+', 'C39E', 'C39E+', 'C93', 'S25', 'S25+', 'I25', 'I25+', 'C128', 'C128A', 'C128B', 'C128C', 'EAN2', 'EAN5', 'EAN8', 'EAN13', 'UPCA', 'UPCE', 'MSI', 'MSI+', 'POSTNET', 'PLANET', 'RMS4CC', 'KIX', 'IMB', 'IMBPRE', 'CODABAR', 'CODE11', 'PHARMA', 'PHARMA2T'];
        $types2d = ['DATAMATRIX', 'PDF417', 'QRCODE', 'RAW', 'RAW2'];
        $this->start_controls_section('section_barcode', ['label' => esc_html__('Code', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_barcode_dimension', ['label' => esc_html__('Dimension', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['1d' => ['title' => esc_html__('1D', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-barcode'], '2d' => ['title' => esc_html__('2D', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-qrcode']], 'label_block' => \true, 'default' => '2d', 'toggle' => \false]);
        $types1d_options = [];
        foreach ($types1d as $key => $value) {
            $types1d_options[$value] = $value;
        }
        $this->add_control('dce_barcode_1d_type', ['label' => esc_html__('Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => $types1d_options, 'default' => 'EAN13', 'condition' => ['dce_barcode_dimension' => '1d']]);
        $types2d_options = array();
        foreach ($types2d as $key => $value) {
            $types2d_options[$value] = $value;
        }
        $this->add_control('dce_barcode_2d_type', ['label' => esc_html__('Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => $types2d_options, 'default' => 'QRCODE', 'condition' => ['dce_barcode_dimension' => '2d']]);
        $this->add_control('dce_barcode_type_options', ['label' => esc_html__('PDF417 Options', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'placeholder' => 'xx,yy,zz', 'condition' => ['dce_barcode_dimension' => '2d', 'dce_barcode_2d_type' => 'PDF417']]);
        $this->add_control('dce_barcode_type_qr', ['label' => esc_html__('QR Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => array('L' => 'L', 'M' => 'M', 'Q' => 'Q', 'H' => 'H'), 'default' => 'L', 'condition' => ['dce_barcode_dimension' => '2d', 'dce_barcode_2d_type' => 'QRCODE']]);
        $this->add_control('dce_barcode_code', ['label' => esc_html__('Code', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => get_bloginfo('url')]);
        $this->add_control('dce_barcode_render', ['label' => esc_html__('Render as', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['SVGcode' => 'SVG', 'PngData' => 'PNG', 'HTML' => 'HTML'], 'default' => 'PngData', 'toggle' => \false]);
        $this->add_control('dce_barcode_cols', ['label' => esc_html__('Cols', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'min' => 1]);
        $this->add_control('dce_barcode_rows', ['label' => esc_html__('Rows', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'min' => 1]);
        $this->end_controls_section();
        $this->start_controls_section('section_style_code', ['label' => esc_html__('Code', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('align', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-right'], 'justify' => ['title' => esc_html__('Justified', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-justify']], 'selectors' => ['{{WRAPPER}}' => 'text-align: {{VALUE}};']]);
        $this->add_control('dce_code_color', ['label' => esc_html__('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'render_type' => 'template', 'selectors' => ['{{WRAPPER}} .dce-barcode-svg #elements' => 'fill: {{VALUE}} !important;', '{{WRAPPER}} .dce-barcode-html > div' => 'background-color: {{VALUE}} !important;']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style_image', ['label' => esc_html__('Image', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['dce_barcode_render' => ['PngData', 'SVGcode']]]);
        $this->add_responsive_control('width', ['label' => esc_html__('Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['unit' => '%'], 'tablet_default' => ['unit' => '%'], 'mobile_default' => ['unit' => '%'], 'size_units' => ['%', 'px', 'vw'], 'range' => ['%' => ['min' => 1, 'max' => 100], 'px' => ['min' => 1, 'max' => 1000], 'vw' => ['min' => 1, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-barcode' => 'width: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('space', ['label' => esc_html__('Max Width', 'dynamic-content-for-elementor') . ' (%)', 'type' => Controls_Manager::SLIDER, 'default' => ['unit' => '%'], 'tablet_default' => ['unit' => '%'], 'mobile_default' => ['unit' => '%'], 'size_units' => ['%'], 'range' => ['%' => ['min' => 1, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-barcode' => 'max-width: {{SIZE}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'image_border', 'selector' => '{{WRAPPER}} .dce-barcode', 'separator' => 'before']);
        $this->add_responsive_control('image_border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .dce-barcode' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'image_box_shadow', 'exclude' => ['box_shadow_position'], 'selector' => '{{WRAPPER}} .dce-barcode']);
        $this->end_controls_section();
    }
    /**
     * Generate cache key based on all parameters that affect barcode output
     *
     * @param string $code
     * @param string $dimension
     * @param string $type
     * @param string $render_type
     * @param int $cols
     * @param int $rows
     * @param mixed $color
     * @return string
     */
    private function get_barcode_cache_key($code, $dimension, $type, $render_type, $cols, $rows, $color)
    {
        return self::CACHE_KEY_PREFIX . \md5($code . $dimension . $type . $render_type . $cols . $rows . wp_json_encode($color));
    }
    /**
     * Generate barcode result with caching
     *
     * @param object $barcode TCPDF barcode instance
     * @param string $render_type
     * @param int $cols
     * @param int $rows
     * @param mixed $color
     * @param string $cache_key
     * @return string|false
     */
    private function generate_barcode_result($barcode, $render_type, $cols, $rows, $color, $cache_key)
    {
        $result = \false;
        // Skip cache in editor mode to allow real-time preview
        $is_editor = \Elementor\Plugin::$instance->editor->is_edit_mode();
        if (!$is_editor) {
            // Try to get cached result (base64 encoded for binary safety)
            $cached_data = get_transient($cache_key);
            if (\false !== $cached_data) {
                $result = \base64_decode($cached_data);
            }
        }
        if (\false === $result) {
            // Generate barcode if not cached
            if ('PngData' === $render_type) {
                $result = $barcode->getBarcodePngData($cols, $rows, $color);
            } elseif ('SVGcode' === $render_type) {
                $result = $barcode->getBarcodeSVGcode($cols, $rows, $color);
            } else {
                // HTML
                $result = $barcode->getBarcodeHTML($cols, $rows, $color);
            }
            // Cache the result for 24 hours (only on frontend)
            // Base64 encode binary data to avoid serialization issues
            if (!$is_editor) {
                $encoded_data = \base64_encode($result);
                set_transient($cache_key, $encoded_data, DAY_IN_SECONDS);
            }
        }
        return $result;
    }
    /**
     * Render barcode HTML output
     *
     * @param string $result Raw barcode data
     * @param string $render_type
     * @return void
     */
    private function render_barcode_output($result, $render_type)
    {
        if ('PngData' === $render_type) {
            $this->add_render_attribute('barcode-img', ['class' => 'dce-barcode dce-barcode-png', 'loading' => 'eager', 'src' => 'data:image/png;base64,' . \base64_encode($result)]);
            echo '<img ' . $this->get_render_attribute_string('barcode-img') . '>';
        } elseif ('SVGcode' === $render_type) {
            $this->add_render_attribute('barcode-svg', ['class' => 'dce-barcode dce-barcode-svg']);
            $result = \str_replace('<svg ', '<svg ' . $this->get_render_attribute_string('barcode-svg') . ' ', $result);
            echo $result;
        } else {
            // HTML
            $this->add_render_attribute('barcode-html', ['class' => 'dce-barcode dce-barcode-html']);
            echo '<div ' . $this->get_render_attribute_string('barcode-html') . ' ' . \substr($result, 5);
        }
    }
    /**
     * @return void
     */
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings) || empty($settings['dce_barcode_code'])) {
            return;
        }
        $code = $settings['dce_barcode_code'];
        switch ($settings['dce_barcode_dimension']) {
            case '1d':
                $type = $settings['dce_barcode_1d_type'];
                $barcode = new \DynamicOOOS\TCPDFBarcode($code, $type);
                break;
            case '2d':
                $type = $settings['dce_barcode_2d_type'];
                if ('QRCODE' === $type) {
                    $type .= ',' . $settings['dce_barcode_type_qr'];
                }
                if ('PDF417' === $type) {
                    $type .= ',' . $settings['dce_barcode_type_options'];
                }
                $barcode = new \DynamicOOOS\TCPDF2DBarcode($code, $type);
                break;
        }
        if (!isset($barcode)) {
            return;
        }
        $render_type = $settings['dce_barcode_render'];
        if (!\in_array($render_type, ['PngData', 'SVGcode', 'HTML'], \true)) {
            return;
        }
        $cols = !empty($settings['dce_barcode_cols']) ? (int) $settings['dce_barcode_cols'] : 10;
        $rows = !empty($settings['dce_barcode_rows']) ? (int) $settings['dce_barcode_rows'] : 10;
        // Prepare color (PNG needs RGB array, others need string)
        $color = 'black';
        if (!empty($settings['dce_code_color'])) {
            $color = $settings['dce_code_color'];
        }
        $color_for_cache = $color;
        if ('PngData' === $render_type) {
            if (!empty($settings['dce_code_color'])) {
                $color = \sscanf($settings['dce_code_color'], '#%02x%02x%02x');
            } else {
                $color = [0, 0, 0];
            }
            $color_for_cache = $color;
        }
        // Generate cache key
        $cache_key = $this->get_barcode_cache_key($code, $settings['dce_barcode_dimension'], $type, $render_type, $cols, $rows, $color_for_cache);
        // Generate barcode with caching
        $result = $this->generate_barcode_result($barcode, $render_type, $cols, $rows, $color, $cache_key);
        if (\false === $result) {
            return;
        }
        // Render output
        $this->render_barcode_output($result, $render_type);
    }
}
