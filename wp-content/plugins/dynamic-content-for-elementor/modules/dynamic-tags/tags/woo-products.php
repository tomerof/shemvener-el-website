<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Modules\DynamicTags\Tags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class WooProducts extends \DynamicContentForElementor\Modules\DynamicTags\Tags\Posts
{
    /**
     * Get Name
     *
     * @return string
     */
    public function get_name()
    {
        return 'dce-woo-products';
    }
    /**
     * Get Title
     *
     * @return string
     */
    public function get_title()
    {
        return esc_html__('Products', 'dynamic-content-for-elementor');
    }
    /**
     * Register Controls
     *
     * @return void
     */
    protected function register_controls()
    {
        parent::register_controls();
        $this->remove_control('post_type');
        $this->update_control('fallback', ['default' => esc_html__('No products found', 'dynamic-content-for-elementor')]);
        $this->update_control('return_format', ['options' => ['title_id' => esc_html__('Title | ID', 'dynamic-content-for-elementor'), 'title_price_value' => esc_html__('Title | Price', 'dynamic-content-for-elementor'), 'title_price_id' => esc_html__('Title (Price) | ID', 'dynamic-content-for-elementor'), 'title_price' => esc_html__('Title (Price)', 'dynamic-content-for-elementor'), 'title' => esc_html__('Title', 'dynamic-content-for-elementor'), 'id' => esc_html__('ID', 'dynamic-content-for-elementor')], 'default' => 'title']);
        $this->update_control('orderby', ['options' => \DynamicContentForElementor\Helper::get_woo_orderby_options()]);
        $this->remove_control('meta_type');
    }
    /**
     * Get Items (WooCommerce products)
     *
     * @return array<int>
     */
    protected function get_query_items()
    {
        $settings = $this->get_settings_for_display();
        $limit = (int) $settings['posts'];
        $order = $settings['order'];
        $orderby = $settings['orderby'];
        $status = $settings['post_status'];
        $wc_args = ['limit' => $limit, 'order' => $order, 'orderby' => \in_array($orderby, ['date', 'title', 'id', 'menu_order'], \true) ? $orderby : 'date', 'status' => $status, 'visibility' => 'visible', 'return' => 'ids'];
        /** @var array<int> $product_ids */
        $product_ids = wc_get_products($wc_args);
        return $product_ids;
    }
    /**
     * Get Post By Format
     *
     * @param int $post_id
     * @return string|int|false
     */
    protected function get_post_by_format($post_id)
    {
        $return_format = $this->get_settings('return_format');
        /** @var \WC_Product $product */
        $product = \wc_get_product($post_id);
        $display_price = wc_get_price_to_display($product);
        $price = \wc_price((float) $display_price);
        switch ($return_format) {
            case 'title_price_value':
                return esc_html(get_the_title($post_id)) . '|' . (string) $display_price;
            case 'title_price_id':
                return esc_html(get_the_title($post_id)) . ' (' . $price . ')|' . $post_id;
            case 'title_price':
                return esc_html(get_the_title($post_id)) . ' (' . $price . ')';
            default:
                return parent::get_post_by_format($post_id);
        }
    }
}
