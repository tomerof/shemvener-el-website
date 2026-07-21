<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Modules\DynamicTags\Tags\DynamicShortcodesWizard;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
use DynamicShortcodes\Core\Shortcodes\Composer;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Text extends Tag
{
    use \DynamicContentForElementor\Modules\DynamicTags\Tags\DynamicShortcodesWizard\WizardTrait;
    /**
     * @return string
     */
    public function get_name()
    {
        return 'dce-dsh-wizard';
    }
    /**
     * @return string
     */
    public function get_title()
    {
        return esc_html__('Wizard for Dynamic Shortcodes', 'dynamic-shortcodes');
    }
    /**
     * @return array<string>
     */
    public function get_group()
    {
        return ['dynamic-shortcodes'];
    }
    /**
     * @return array<string>
     */
    public function get_categories()
    {
        return [\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY, \Elementor\Modules\DynamicTags\Module::URL_CATEGORY, \Elementor\Modules\DynamicTags\Module::NUMBER_CATEGORY, \Elementor\Modules\DynamicTags\Module::DATETIME_CATEGORY, \Elementor\Modules\DynamicTags\Module::COLOR_CATEGORY, \Elementor\Modules\DynamicTags\Module::POST_META_CATEGORY];
    }
    /**
     * @return void
     */
    protected function register_controls()
    {
        if (\DynamicContentForElementor\Helper::can_register_unsafe_controls()) {
            $this->register_controls_settings();
        } else {
            $this->register_controls_non_admin_notice();
        }
    }
    /**
     * @return void
     */
    protected function register_controls_non_admin_notice()
    {
        $this->add_control('html_notice', ['type' => Controls_Manager::RAW_HTML, 'raw' => esc_html__('You will need administrator capabilities to edit this Dynamic Tag.', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning']);
    }
    /**
     * @return void
     */
    public function render()
    {
        // Values of dynamic tags containing CSS were not rendered correctly due to Elementor's performance optimisations
        $original_use_style_controls = \Elementor\Core\Frontend\Performance::is_use_style_controls();
        \Elementor\Core\Frontend\Performance::set_use_style_controls(\true);
        try {
            $this->render_real();
        } finally {
            \Elementor\Core\Frontend\Performance::set_use_style_controls($original_use_style_controls);
        }
    }
    /**
     * @return void
     */
    private function render_real()
    {
        $shortcode = $this->get_settings_for_display('shortcode');
        if (empty($shortcode)) {
            return;
        }
        echo \DynamicContentForElementor\Plugin::instance()->text_templates->dce_shortcodes->expand($shortcode, ['privileges' => 'all']);
    }
}
