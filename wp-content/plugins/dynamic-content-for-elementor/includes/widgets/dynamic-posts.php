<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Includes\Skins;
use DynamicContentForElementor\Controls\Group_Control_Filters_CSS;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class DynamicPosts extends \DynamicContentForElementor\Widgets\DynamicPostsBase
{
    public function get_name()
    {
        return 'dce-dynamicposts-v2';
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
}
