<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Plugin;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class WooCartAction extends \ElementorPro\Modules\Forms\Classes\Action_Base
{
    /**
     * Has Action
     *
     * @var boolean
     */
    public $has_action = \true;
    /**
     * @return string
     */
    public function get_name()
    {
        return 'dce_form_woo_cart';
    }
    /**
     * @return string
     */
    public function get_label()
    {
        return esc_html__('WooCommerce Add to Cart', 'dynamic-content-for-elementor');
    }
    /**
     * @return array<string>
     */
    public function get_script_depends()
    {
        return [];
    }
    /**
     * @return array<string>
     */
    public function get_style_depends()
    {
        return [];
    }
    /**
     * @return void
     */
    public function run_once()
    {
        $save_guard = \DynamicContentForElementor\Plugin::instance()->save_guard;
        $save_guard->register_unsafe_control('form', 'dce_form_woo_cart_field');
        $save_guard->register_unsafe_control('form', 'dce_form_woo_cart_ids');
    }
    /**
     * @param \Elementor\Widget_Base $widget
     * @return void
     */
    public function register_settings_section($widget)
    {
        $widget->start_controls_section('section_dce_form_woo_cart', ['label' => Helper::dce_logo() . $this->get_label(), 'condition' => ['submit_actions' => $this->get_name()]]);
        if (!\DynamicContentForElementor\Helper::can_register_unsafe_controls()) {
            $widget->add_control('admin_notice', ['name' => 'admin_notice', 'type' => Controls_Manager::RAW_HTML, 'raw' => '<div class="elementor-panel-alert elementor-panel-alert-warning">' . esc_html__('You will need administrator capabilities to edit these settings.', 'dynamic-content-for-elementor') . '</div>']);
            $widget->end_controls_section();
            return;
        }
        Plugin::instance()->text_templates->maybe_add_notice($widget, 'woo_cart');
        $widget->add_control('dce_form_woo_cart_source', ['label' => esc_html__('Product IDs Source', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'field', 'options' => ['field' => esc_html__('Form field', 'dynamic-content-for-elementor'), 'ids' => esc_html__('List', 'dynamic-content-for-elementor')]]);
        $widget->add_control('dce_form_woo_cart_field', ['label' => esc_html__('Field ID', 'dynamic-content-for-elementor'), 'description' => esc_html__('Enter the ID of a form field (e.g. a Select or Checkbox field with product IDs as values).', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'label_block' => \true, 'ai' => ['active' => \false], 'dynamic' => ['active' => \false], 'condition' => ['dce_form_woo_cart_source' => 'field']]);
        $widget->add_control('dce_form_woo_cart_ids', ['label' => esc_html__('Product IDs', 'dynamic-content-for-elementor'), 'description' => esc_html__('Enter one or more product IDs (comma-separated), or use a Dynamic Tag.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'label_block' => \true, 'ai' => ['active' => \false], 'dynamic' => ['active' => \true], 'condition' => ['dce_form_woo_cart_source' => 'ids']]);
        $widget->end_controls_section();
    }
    /**
     * @param \ElementorPro\Modules\Forms\Classes\Form_Record   $record
     * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
     * @return void
     */
    public function run($record, $ajax_handler)
    {
        if (!Helper::is_plugin_active('woocommerce')) {
            $ajax_handler->add_error_message(esc_html__('WooCommerce is not active.', 'dynamic-content-for-elementor'));
            return;
        }
        $settings = $record->get('form_settings');
        $source = $settings['dce_form_woo_cart_source'] ?? 'field';
        $raw = '';
        if ($source === 'field') {
            $field_id = $settings['dce_form_woo_cart_field'] ?? '';
            if (empty($field_id)) {
                return;
            }
            $fields = Helper::get_form_data($record);
            $raw = $fields[$field_id] ?? '';
        } else {
            $raw = $settings['dce_form_woo_cart_ids'] ?? '';
        }
        if (empty($raw)) {
            return;
        }
        $product_ids = $this->normalize_product_ids($raw);
        if (empty($product_ids)) {
            return;
        }
        // Limit to 20 products to prevent abuse
        $product_ids = \array_slice($product_ids, 0, 20);
        // Ensure Woo session
        if (null === WC()->session) {
            wc_load_cart();
        }
        foreach ($product_ids as $product_id) {
            $product = \wc_get_product($product_id);
            if (!$product) {
                continue;
            }
            if (!$product->is_purchasable() || !$product->is_in_stock()) {
                continue;
            }
            // Let Woo handle existing-in-cart behavior (quantity increment)
            try {
                WC()->cart->add_to_cart((int) $product_id);
            } catch (\Throwable $e) {
                // Skip on failure, optionally collect error
                continue;
            }
        }
    }
    /**
     * Normalize input into a list of WooCommerce product IDs.
     *
     * Accepts the following input shapes:
     * - string CSV (e.g. "12,34,56")
     * - string with line separators (one ID per line)
     * - single string or number (e.g. "12" or 12)
     * - numeric array of IDs (e.g. [12,34,56])
     * - mixed array of strings/numbers representing IDs
     *
     * Spaces removed, duplicates eliminated, only positive integers.
     *
     * @param mixed $value Raw value from form or dynamic shortcodes
     * @return int[] List of normalized product IDs (unique, > 0)
     */
    private function normalize_product_ids($value)
    {
        $ids = [];
        if (\is_array($value)) {
            $ids = $value;
        } elseif (\is_string($value)) {
            // Could be the raw value or a comma-separated list
            $ids = \preg_split('/[,\\n\\r]+/', $value);
        } elseif (\is_numeric($value)) {
            $ids = [$value];
        }
        $ids = \array_map('trim', \array_map('strval', (array) $ids));
        $ids = \array_filter($ids, function ($v) {
            return $v !== '';
        });
        $ids = \array_map('intval', $ids);
        $ids = \array_filter($ids, function ($v) {
            return $v > 0;
        });
        $ids = \array_values(\array_unique($ids));
        return $ids;
    }
    /**
     * @param array<mixed> $element
     * @return array<mixed>
     */
    public function on_export($element)
    {
        return $element;
    }
}
