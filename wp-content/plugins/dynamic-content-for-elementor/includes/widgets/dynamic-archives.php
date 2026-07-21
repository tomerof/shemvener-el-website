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
class DynamicArchives extends \DynamicContentForElementor\Widgets\DynamicPostsBase
{
    public function get_name()
    {
        return 'dce-dynamic-archives';
    }
    /**
     * Register Skins
     *
     * @return void
     */
    protected function register_skins()
    {
        $this->add_skin(new Skins\SkinGrid($this));
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
        $this->update_control('query', ['type' => Controls_Manager::HIDDEN]);
        $this->update_control('query_type', ['type' => Controls_Manager::HIDDEN, 'default' => 'dynamic_archives']);
    }
}
