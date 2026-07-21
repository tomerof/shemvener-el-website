<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

use Elementor\Icons_Manager;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
class AddToWooWishlist extends \DynamicContentForElementor\Widgets\AddToFavorites
{
    public function get_name()
    {
        return 'dce-dynamic-woo-wishlist';
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        parent::safe_register_controls();
        $this->update_control('scope', ['type' => Controls_Manager::HIDDEN, 'default' => 'user']);
        $this->update_control('counter', ['type' => Controls_Manager::HIDDEN, 'default' => '']);
        $this->update_control('title_add', ['default' => esc_html__('Add to my Wishlist', 'dynamic-content-for-elementor')]);
        $this->update_control('title_remove', ['default' => esc_html__('Remove from my Wishlist', 'dynamic-content-for-elementor')]);
        $this->update_control('remove', ['type' => Controls_Manager::HIDDEN, 'default' => 'yes']);
        $this->update_control('key', ['type' => Controls_Manager::HIDDEN, 'default' => 'dce_wishlist']);
        $this->update_control('visitor_hide', ['type' => Controls_Manager::HIDDEN, 'default' => 'yes']);
    }
}
