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
class AcfRelationship extends \DynamicContentForElementor\Widgets\DynamicPostsBase
{
    public function get_name()
    {
        return 'dce-acf-relationship';
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
        $this->update_control('query_type', ['type' => Controls_Manager::HIDDEN, 'default' => 'relationship']);
    }
    /**
     * Register Widget Specific Controls
     *
     * @return void
     */
    protected function register_widget_specific_controls()
    {
        $this->start_controls_section('section_field', ['label' => esc_html__('ACF Relationship Field', 'dynamic-content-for-elementor')]);
        $this->add_acf_relationship_controls();
        $this->end_controls_section();
    }
}
