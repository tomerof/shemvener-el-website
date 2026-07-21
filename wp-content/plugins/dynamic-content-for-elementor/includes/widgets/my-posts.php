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
class MyPosts extends \DynamicContentForElementor\Widgets\DynamicPostsBase
{
    public function get_name()
    {
        return 'dce-my-posts';
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
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        parent::safe_register_controls();
        $this->update_control('query_type', ['type' => Controls_Manager::HIDDEN, 'default' => 'get_cpt']);
        $this->update_control('query_filter', ['label' => esc_html__('By', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => ['date' => esc_html__('Date', 'dynamic-content-for-elementor'), 'term' => esc_html__('Terms & Taxonomy', 'dynamic-content-for-elementor'), 'author' => esc_html__('Author', 'dynamic-content-for-elementor'), 'metakey' => esc_html__('Metakey', 'dynamic-content-for-elementor')], 'multiple' => \true, 'label_block' => \true, 'default' => ['author']]);
        $this->update_control('heading_query_filter_author', ['type' => Controls_Manager::HIDDEN, 'condition' => ['query_filter' => 'author']]);
        $this->update_control('author_from', ['type' => Controls_Manager::HIDDEN, 'default' => 'current_user']);
    }
}
