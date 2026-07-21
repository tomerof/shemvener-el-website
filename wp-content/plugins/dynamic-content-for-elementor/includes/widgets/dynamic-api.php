<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Plugin;
if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
class DynamicApi extends \DynamicContentForElementor\Widgets\RemoteContentBase
{
    /**
     * @return void
     */
    public function run_once()
    {
        parent::run_once();
        Plugin::instance()->save_guard->register_unsafe_control($this->get_name(), 'secure_mode');
    }
    /**
     * @return void
     */
    protected function add_data_section()
    {
        $this->start_controls_section('section_data', ['label' => esc_html__('Data', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT]);
        Plugin::instance()->text_templates->maybe_add_notice($this);
        $this->add_control('data_template', ['label' => esc_html__('Dynamic HTML', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CODE, 'default' => \DynamicContentForElementor\Plugin::instance()->text_templates->get_default_value(['dynamic-shortcodes' => '{data:result|dump}', 'tokens' => '<div class="dce-remote-content"><h3 class="dce-remote-content-title">[DATA:title:rendered]</h3><div class="dce-remote-content-body">[DATA:excerpt:rendered]</div><a class="btn btn-primary" href="[DATA:link]">Read more</a></div>']), 'description' => \DynamicContentForElementor\Plugin::instance()->text_templates->get_default_value(['dynamic-shortcodes' => \sprintf(esc_html__('Available Dynamic Shortcodes include: %1$s You can also use all other Dynamic Shortcodes to further customize the content.', 'dynamic-content-for-elementor'), '<ul>' . '<li>' . \sprintf(esc_html__('Use %s to fetch an array of the data returned by the specified endpoint.', 'dynamic-content-for-elementor'), '<code>{data:result}</code>') . '</li>' . '<li>' . \sprintf(esc_html__("Use %s to to perform a dump of the result, providing a visual representation of the data structure. This is helpful for understanding the data's format and for debugging purposes.", 'dynamic-content-for-elementor'), '<code>{data:result|dump}</code>') . '</li>' . '<li>' . \sprintf(esc_html__('Use %s to fetch a specific element of the data.', 'dynamic-content-for-elementor'), '<code>{data:result||element}</code>') . '</li>' . '</ul>'), 'tokens' => esc_html__('Add a specific format to data elements. Use Tokens to represent JSON fields.', 'dynamic-content-for-elementor')]), 'ai' => ['active' => \false], 'dynamic' => ['active' => \false]]);
        $this->add_control('secure_mode', ['label' => esc_html__('Secure Mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_on' => esc_html__('On', 'dynamic-content-for-elementor'), 'label_off' => esc_html__('Off', 'dynamic-content-for-elementor'), 'return_value' => 'yes', 'default' => 'yes', 'description' => esc_html__('Removes active content from the generated HTML before rendering. Disable it only if the API source is trusted and active content is required.', 'dynamic-content-for-elementor')]);
        if (\DynamicContentForElementor\Tokens::is_active() && Helper::is_plugin_active('dynamic-shortcodes')) {
            $this->add_control('notice_tokens', ['type' => \Elementor\Controls_Manager::NOTICE, 'notice_type' => 'warning', 'dismissible' => \false, 'heading' => esc_html__('Warning', 'dynamic-content-for-elementor'), 'content' => esc_html__('The settings below are specific to Tokens and are not used with Dynamic Shortcodes', 'dynamic-content-for-elementor')]);
        }
        if (\DynamicContentForElementor\Tokens::is_active()) {
            $this->add_control('single_or_archive', ['label' => esc_html__('Single or Archive', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_off' => esc_html__('Archive', 'dynamic-content-for-elementor'), 'label_on' => esc_html__('Single', 'dynamic-content-for-elementor'), 'default' => 'yes']);
            $this->add_control('archive_path', ['label' => esc_html__('Archive Array path', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '', 'description' => esc_html__('Leave empty if JSON result is a direct array (like in WP API). For web services usually might use "results". You can browse sub arrays separate them by comma like "data.people"', 'dynamic-content-for-elementor'), 'condition' => ['single_or_archive' => '']]);
            $this->add_control('limit_contents', ['label' => esc_html__('Limit elements', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'description' => esc_html__('Set -1 for unlimited', 'dynamic-content-for-elementor'), 'default' => -1, 'condition' => ['single_or_archive' => '']]);
            $this->add_control('offset_contents', ['label' => esc_html__('Start from', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'min' => 0, 'description' => esc_html__('0 or empty to start from the first', 'dynamic-content-for-elementor'), 'default' => -1, 'condition' => ['single_or_archive' => '']]);
        }
        $this->end_controls_section();
    }
    /**
     * @param string $response
     * @param array<mixed> $settings
     * @return array<mixed>
     */
    protected function retrieve_page_body($response, $settings)
    {
        $json_result = \json_decode($response, \true);
        $page_body = [];
        $page_body[] = Plugin::instance()->text_templates->expand_shortcodes_or_callback($settings['data_template'], ['result' => $json_result], function ($str) use($settings, $json_result) {
            if (!\DynamicContentForElementor\Tokens::is_active()) {
                return $str;
            }
            // Single
            if ($settings['single_or_archive']) {
                return $this->replace_template_tokens(Helper::get_dynamic_value($str), $json_result);
            }
            // Archive
            $body = [];
            $json_data_archive = $json_result;
            if (!empty($settings['archive_path'])) {
                $settings['archive_path'] = \str_replace('.', ':', $settings['archive_path']);
                $pieces = \explode(':', $settings['archive_path']);
                $tmp_val = Helper::get_array_value_by_keys($json_result, $pieces);
                if ($tmp_val) {
                    $json_data_archive = $tmp_val;
                }
            }
            if (!empty($json_data_archive)) {
                foreach ($json_data_archive as $aJsonData) {
                    $body[] = $this->replace_template_tokens(Helper::get_dynamic_value($str), $aJsonData);
                }
            }
            return $body;
        });
        return $page_body;
    }
    /**
     * @param mixed $element
     * @param array<mixed> $settings
     * @return mixed
     */
    protected function prepare_element_for_output($element, $settings)
    {
        if (!\is_string($element) || '' === ($settings['secure_mode'] ?? 'yes')) {
            return $element;
        }
        return wp_kses_post($element);
    }
    /**
     * @return string
     */
    public function get_transient_prefix()
    {
        return 'dce_dynamic_api_';
    }
    /**
     * @param string $text
     * @param array<mixed> $content
     * @return string
     */
    protected function replace_template_tokens($text, $content)
    {
        $text = \DynamicContentForElementor\Tokens::replace_var_tokens($text, 'DATA', $content);
        $pieces = \explode('[', $text);
        if (\count($pieces) > 1) {
            foreach ($pieces as $key => $avalue) {
                if ($key) {
                    $piece = \explode(']', $avalue);
                    $meta_params = \reset($piece);
                    $option_params = \explode('.', $meta_params);
                    $field_name = $option_params[0];
                    $option_value = isset($content[$field_name]) ? $content[$field_name] : '';
                    $replace_value = $this->check_array_value($option_value, $option_params);
                    $text = \str_replace('[' . $meta_params . ']', $replace_value, $text);
                }
            }
        }
        return $text;
    }
    /**
     * @param mixed $option_value
     * @param mixed $option_params
     * @return string
     */
    private function check_array_value($option_value = [], $option_params = [])
    {
        if (\is_array($option_value)) {
            if (1 === \count($option_value)) {
                $tmpValue = \reset($option_value);
                if (!\is_array($tmpValue)) {
                    return $tmpValue;
                }
            }
            if (\is_array($option_params)) {
                $val = $option_value;
                foreach ($option_params as $key => $value) {
                    if (isset($val[$value])) {
                        $val = $val[$value];
                    }
                }
                if (\is_array($val)) {
                    $val = \var_export($val, \true);
                }
                return $val;
            }
            if ($option_params) {
                return $option_value[$option_params];
            }
            return \var_export($option_value, \true);
        }
        return $option_value;
    }
}
