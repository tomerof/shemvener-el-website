<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Modules\DynamicTags\Tags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Modules\DynamicTags\Tags\Favorites;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class WooWishlist extends Favorites
{
    public function get_name()
    {
        return 'dce-wishlist';
    }
    public function get_title()
    {
        return esc_html__('Woo Wishlist', 'dynamic-content-for-elementor');
    }
    protected function register_controls()
    {
        parent::register_controls();
        $this->update_control('favorites_scope', ['type' => Controls_Manager::HIDDEN, 'default' => 'user']);
        $this->update_control('favorites_key', ['type' => Controls_Manager::HIDDEN, 'default' => 'dce_wishlist']);
        $this->update_control('favorites_link', ['label' => esc_html__('Link to product', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['favorites_separator!' => 'new_line']]);
        $this->update_control('favorites_post_type', ['type' => Controls_Manager::HIDDEN]);
        $this->update_control('favorites_fallback', ['default' => esc_html__('No products in the wishlist', 'dynamic-content-for-elementor')]);
    }
}
