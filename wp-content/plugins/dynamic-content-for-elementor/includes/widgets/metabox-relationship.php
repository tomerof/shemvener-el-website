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
class MetaboxRelationship extends \DynamicContentForElementor\Widgets\DynamicPostsBase
{
    public function get_name()
    {
        return 'dce-metabox-relationship';
    }
    /**
     * Register Skins
     *
     * @return void
     */
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
        $this->update_control('query_type', ['type' => Controls_Manager::HIDDEN, 'default' => 'metabox_relationship']);
    }
    /**
     * Register Widget Specific Controls
     *
     * @return void
     */
    protected function register_widget_specific_controls()
    {
        $this->start_controls_section('section_field', ['label' => esc_html__('Meta Box Relationship Field', 'dynamic-content-for-elementor')]);
        $this->add_control('id', ['label' => esc_html__('Meta Box Relationship', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Select the Relationship...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'metabox_relationship']);
        $this->add_control('relation', ['label' => esc_html__('Relation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['from' => esc_html__('From', 'dynamic-content-for-elementor'), 'to' => esc_html__('To', 'dynamic-content-for-elementor')], 'default' => 'from']);
        $this->end_controls_section();
    }
}
