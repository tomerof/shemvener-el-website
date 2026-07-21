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
class StickyPosts extends \DynamicContentForElementor\Modules\DynamicTags\Tags\Posts
{
    /**
     * Get Name
     *
     * @return string
     */
    public function get_name()
    {
        return 'dce-sticky-posts';
    }
    /**
     * Get Title
     *
     * @return string
     */
    public function get_title()
    {
        return esc_html__('Sticky Posts', 'dynamic-content-for-elementor');
    }
    /**
     * Get Args
     *
     * @return array<string,int|string>
     */
    protected function get_args()
    {
        $args = parent::get_args();
        $sticky_posts = get_option('sticky_posts');
        if (empty($sticky_posts)) {
            // If no sticky posts, return impossible ID to get no results
            $args['post__in'] = [-1];
        } else {
            $args['post__in'] = \is_array($sticky_posts) ? $sticky_posts : [$sticky_posts];
        }
        return $args;
    }
}
