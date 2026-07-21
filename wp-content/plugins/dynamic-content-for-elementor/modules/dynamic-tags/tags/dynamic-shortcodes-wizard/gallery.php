<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Modules\DynamicTags\Tags\DynamicShortcodesWizard;

use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Plugin;
use DynamicShortcodes\Core\Shortcodes\Composer;
use DynamicShortcodes\Core\Shortcodes\EvaluationError;
use DynamicShortcodes\Core\Shortcodes\ParseError;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Gallery extends Data_Tag
{
    use \DynamicContentForElementor\Modules\DynamicTags\Tags\DynamicShortcodesWizard\WizardTrait;
    /**
     * @return string
     */
    public function get_name()
    {
        return 'dce-dsh-wizard-gallery';
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
        return [\Elementor\Modules\DynamicTags\Module::GALLERY_CATEGORY];
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
     * @param string $content
     * @param string|\Throwable $err
     * @return void
     */
    private function add_parse_error($content, $err)
    {
        $msg = esc_html__('Parse error in Wizard - Dynamic Shortcodes.', 'dynamic-shortcodes');
        if (\is_string($err)) {
            $msg .= $err;
        } else {
            $msg .= $err->getMessage();
        }
        \DynamicShortcodes\Plugin::instance()->shortcodes_manager->add_error($msg, $content);
    }
    /**
     * @param array<mixed> $options
     * @return array<int,mixed>|void
     */
    public function get_value(array $options = [])
    {
        $shortcode = $this->get_settings_for_display('shortcode');
        if (empty($shortcode) || !Plugin::instance()->text_templates->dce_shortcodes->is_dsh_active()) {
            return;
        }
        try {
            $input = \DynamicShortcodes\Plugin::instance()->shortcodes_manager->evaluate_and_return_value($shortcode);
        } catch (ParseError $e) {
            $this->add_parse_error($shortcode, $e);
            return [];
        } catch (EvaluationError $e) {
            $this->add_evaluation_error($shortcode, $e);
            return [];
        }
        if (!\is_array($input)) {
            $this->add_evaluation_error($shortcode, esc_html__('The last shortcode should return an array', 'dynamic-shortcodes'));
            return [];
        }
        $res = [];
        foreach ($input as $id) {
            if (!\is_numeric($id)) {
                $this->add_evaluation_error($shortcode, esc_html__('The last shortcode should return an array of Media IDs', 'dynamic-shortcodes'));
                return [];
            }
            $res[] = ['id' => $id];
        }
        return $res;
    }
}
