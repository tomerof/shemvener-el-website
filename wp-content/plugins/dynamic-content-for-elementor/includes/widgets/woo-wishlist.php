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
class WooWishlist extends \DynamicContentForElementor\Widgets\DynamicPostsBase
{
    public function get_name()
    {
        return 'dce-woo-wishlist';
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
        $this->add_skin(new Skins\SkinTimeline($this));
        $this->add_skin(new Skins\Skin3D($this));
        $this->add_skin(new Skins\SkinGridToFullscreen3D($this));
        $this->add_skin(new Skins\SkinCrossroadsSlideshow($this));
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        parent::safe_register_controls();
        $this->update_control('list_items', ['default' => [['item_id' => 'item_image'], ['item_id' => 'item_title'], ['item_id' => 'item_productprice'], ['item_id' => 'item_addtocart']]]);
        $this->update_control('query_type', ['type' => Controls_Manager::HIDDEN, 'default' => 'favorites']);
        $this->update_control('favorites_scope', ['type' => Controls_Manager::HIDDEN, 'default' => 'user']);
        $this->update_control('favorites_key', ['type' => Controls_Manager::HIDDEN, 'default' => 'dce_wishlist']);
        $this->update_control('fallback_text', ['default' => esc_html__('No products on the wishlist.', 'dynamic-content-for-elementor')]);
    }
}
