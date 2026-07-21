<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Includes\Skins;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class WooProductsCart extends \DynamicContentForElementor\Widgets\DynamicPostsBase
{
    public function get_name()
    {
        return 'dce-woo-products-cart';
    }
    protected function register_skins()
    {
        $this->add_skin(new Skins\SkinGrid($this));
        $this->add_skin(new Skins\SkinGridFilters($this));
        $this->add_skin(new Skins\SkinCarousel($this));
        $this->add_skin(new Skins\SkinDualCarousel($this));
        $this->add_skin(new Skins\SkinAccordion($this));
        $this->add_skin(new Skins\SkinList($this));
        $this->add_skin(new Skins\SkinTable($this));
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        parent::safe_register_controls();
        $this->update_control('query_type', ['type' => Controls_Manager::HIDDEN, 'default' => 'products_cart']);
        $this->update_control('list_items', ['default' => [['item_id' => 'item_image'], ['item_id' => 'item_title'], ['item_id' => 'item_productprice']]]);
        $this->update_control('taxonomy', ['options' => ['' => esc_html__('All', 'dynamic-content-for-elementor')] + Helper::get_woocommerce_taxonomies(), 'default' => '']);
        $this->update_control('fallback_text', ['default' => esc_html__('The cart is empty.', 'dynamic-content-for-elementor')]);
        $this->update_control('post_status', ['label' => esc_html__('Product Status', 'dynamic-content-for-elementor')]);
        $this->update_control('post_offset', ['label' => esc_html__('Products Offset', 'dynamic-content-for-elementor'), 'description' => esc_html__('Warning: products offset doesn\'t support pagination', 'dynamic-content-for-elementor')]);
        $this->update_control('exclude_io', ['label' => esc_html__('Current Product', 'dynamic-content-for-elementor')]);
        $this->update_control('exclude_posts', ['label' => esc_html__('Specific Products', 'dynamic-content-for-elementor')]);
        $this->update_control('exclude_page_parent', ['type' => Controls_Manager::HIDDEN]);
        $this->update_control('terms_current_post', ['label' => esc_html__('Dynamic Current Product Terms', 'dynamic-content-for-elementor'), 'description' => esc_html__('Filter results by taxonomy terms associated to the current product', 'dynamic-content-for-elementor')]);
        $this->update_control('include_author', ['description' => esc_html__('Filter products by selected Authors', 'dynamic-content-for-elementor')]);
        $this->update_control('exclude_author', ['description' => esc_html__('Filter products by selected Authors', 'dynamic-content-for-elementor')]);
    }
}
