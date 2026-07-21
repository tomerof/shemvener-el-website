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
class AcfRelationship extends \DynamicContentForElementor\Modules\DynamicTags\Tags\Posts
{
    /**
     * Get Name
     *
     * @return string
     */
    public function get_name()
    {
        return 'dce-acf-relationship';
    }
    /**
     * Get Title
     *
     * @return string
     */
    public function get_title()
    {
        return esc_html__('ACF Relationship', 'dynamic-content-for-elementor');
    }
    /**
     * Register Controls
     *
     * @return void
     */
    protected function register_controls()
    {
        $this->add_control('acf_relationship_field', ['label' => esc_html__('ACF Relationship Field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acf', 'object_type' => ['post_object', 'relationship']]);
        parent::register_controls();
    }
    /**
     * Get Args
     *
     * @return array<string,int|string>
     */
    protected function get_args()
    {
        $args = parent::get_args();
        $settings = $this->get_settings_for_display();
        $relations = \get_field($settings['acf_relationship_field'], \false, \false);
        if (!$relations) {
            $relations = get_sub_field($settings['acf_relationship_field'], \false);
        }
        if (empty($relations)) {
            return;
        }
        if (Helper::is_plugin_active('wpml')) {
            // WPML Translation
            $relations = Helper::wpml_translate_object_id($relations);
        }
        // Descending order when Order By is set to "Preserve Post ID order given"
        if ('post__in' === $args['orderby'] && 'DESC' === $args['order']) {
            $relations = \array_reverse($relations);
        }
        $args['post__in'] = \is_array($relations) ? $relations : [$relations];
        return $args;
    }
}
