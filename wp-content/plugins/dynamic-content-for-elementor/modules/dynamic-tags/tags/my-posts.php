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
class MyPosts extends \DynamicContentForElementor\Modules\DynamicTags\Tags\Posts
{
    /**
     * Get Name
     *
     * @return string
     */
    public function get_name()
    {
        return 'dce-my-posts';
    }
    /**
     * Get Title
     *
     * @return string
     */
    public function get_title()
    {
        return esc_html__('Posts by the Current User', 'dynamic-content-for-elementor');
    }
    /**
     * Get Args
     *
     * @return array<string,int|string>
     */
    protected function get_args()
    {
        $args = parent::get_args();
        $args['author__in'] = (int) get_current_user_id();
        return $args;
    }
}
