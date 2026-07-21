<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Modules\DynamicTags\Tags\DynamicShortcodesWizard;

use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
use DynamicShortcodes\Core\Shortcodes\Composer;
use DynamicShortcodes\Core\Shortcodes\EvaluationError;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Image extends Data_Tag
{
    use \DynamicContentForElementor\Modules\DynamicTags\Tags\DynamicShortcodesWizard\WizardTrait;
    /**
     * @return string
     */
    public function get_name()
    {
        return 'dce-dsh-wizard-image';
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
        return [\Elementor\Modules\DynamicTags\Module::IMAGE_CATEGORY];
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
     * @param string $content
     * @param string|\Throwable $err
     * @return void
     */
    private function add_evaluation_error($content, $err)
    {
        $msg = esc_html__('Evaluation error in Wizard - Dynamic Shortcodes.', 'dynamic-shortcodes');
        if (\is_string($err)) {
            $msg .= $err;
        } else {
            $msg .= $err->getMessage();
        }
        \DynamicShortcodes\Plugin::instance()->shortcodes_manager->add_error($msg, $content);
    }
    /**
     * @param array<mixed> $options
     * @return array<string,mixed>|void
     */
    public function get_value(array $options = [])
    {
        $shortcode = $this->get_settings_for_display('shortcode');
        if (empty($shortcode)) {
            return;
        }
        try {
            $res = \DynamicContentForElementor\Plugin::instance()->text_templates->dce_shortcodes->expand($shortcode, ['catch_errors' => \false, 'privileges' => 'all']);
        } catch (EvaluationError $e) {
            $this->add_evaluation_error($shortcode, $e);
            return ['url' => '', 'id' => null];
        }
        if (\is_numeric($res)) {
            $url = wp_get_attachment_image_url((int) $res, 'full');
            if (!$url) {
                $this->add_evaluation_error($shortcode, esc_html__('Could not find the media url', 'dynamic-shortcodes'));
                return ['url' => '', 'id' => null];
            }
            return ['id' => $res, 'url' => $url];
        }
        return ['url' => $res, 'id' => null];
    }
}
