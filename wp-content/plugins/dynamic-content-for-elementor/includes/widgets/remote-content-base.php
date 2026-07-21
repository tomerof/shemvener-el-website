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
abstract class RemoteContentBase extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    const CACHE_MAX_AGES = ['1m' => 60, '5m' => 60 * 5, '15m' => 60 * 15, '1h' => 60 * 60, '6h' => 60 * 60 * 6, '12h' => 60 * 60 * 12, '24h' => 60 * 60 * 24];
    /**
     * @return void
     */
    public function run_once()
    {
        parent::run_once();
        $save_guard = \DynamicContentForElementor\Plugin::instance()->save_guard;
        $save_guard->register_unsafe_control($this->get_name(), 'data_template');
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_remotecontent', ['label' => $this->get_title(), 'tab' => Controls_Manager::TAB_CONTENT]);
        $this->add_control('url', ['label' => esc_html__('URL', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '']);
        $this->add_control('method', ['label' => esc_html__('Method', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'GET', 'options' => ['GET' => 'GET', 'POST' => 'POST']]);
        $this->add_control('headers', ['label' => esc_html__('Headers', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXTAREA, 'placeholder' => 'Authorization: <type> <token>', 'description' => esc_html__('Please use the format "Key: value", one per line', 'dynamic-content-for-elementor'), 'rows' => '3']);
        $repeater_parameters = new \Elementor\Repeater();
        $repeater_parameters->add_control('key', ['label' => esc_html__('Key', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT]);
        $repeater_parameters->add_control('value', ['label' => esc_html__('Value', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT]);
        $this->add_control('parameters', ['label' => esc_html__('Parameters', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::REPEATER, 'fields' => $repeater_parameters->get_controls(), 'title_field' => '{{{ key }}} = {{{ value }}}', 'prevent_empty' => \false, 'item_actions' => ['add' => \true, 'duplicate' => \false, 'remove' => \true, 'sort' => \true]]);
        $this->add_control('parameters_json_encode', ['label' => esc_html__('Encode parameters in JSON', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default ' => 'yes', 'condition' => ['method' => 'POST']]);
        $this->add_control('require_authorization', ['label' => esc_html__('Require Authorization', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
        $this->add_control('authorization_user', ['label' => esc_html__('Basic HTTP User', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'condition' => ['require_authorization' => 'yes']]);
        $this->add_control('authorization_pass', ['label' => esc_html__('Basic HTTP Password', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'condition' => ['require_authorization' => 'yes']]);
        $this->add_control('connect_timeout', ['label' => esc_html__('Connection Timeout (s)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 5, 'min' => 0, 'max' => 30, 'description' => esc_html__('Time period within which the connection between your server and the endpoint must be established', 'dynamic-content-for-elementor')]);
        $this->add_control('data_cache', ['label' => esc_html__('Cache', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
        $this->add_control('cache_age', ['label' => esc_html__('Store in cache for', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => $this->get_cache_age_options(), 'default' => '5m', 'label_block' => \true, 'condition' => ['data_cache!' => '']]);
        $this->end_controls_section();
        $this->add_data_section();
        $this->start_controls_section('section_html_manipulation', ['label' => esc_html__('HTML Manipulation', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT]);
        $this->add_control('fix_links', ['label' => esc_html__('Fix Relative links', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('Use it if the remote page contains relative links', 'dynamic-content-for-elementor')]);
        $this->add_control('blank_links', ['label' => esc_html__('Target Blank links', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('Open links on a new page', 'dynamic-content-for-elementor')]);
        $this->add_control('lazy_images', ['label' => esc_html__('Fix Lazy Images src', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('Display lazy images without using specific JS', 'dynamic-content-for-elementor')]);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings['url'])) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                Helper::notice(\false, esc_html__('Add an URL to begin', 'dynamic-content-for-elementor'));
            }
            return;
        }
        $url = esc_url_raw($settings['url']);
        if (!\filter_var($url, \FILTER_VALIDATE_URL)) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                Helper::notice(\false, esc_html__('URL not valid', 'dynamic-content-for-elementor'));
            }
            return;
        }
        $args = [];
        // Headers
        if (!empty($settings['headers']) && !\preg_match('/^\\s+$/', $settings['headers'])) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode() && "'" === \substr($settings['headers'], 0, 1)) {
                $format = "<br /><pre><code>Key: value\nKey: value</code></pre>";
                Helper::notice(esc_html__('Wrong Headers format', 'dynamic-content-for-elementor'), esc_html__('Please use this format', 'dynamic-content-for-elementor') . $format);
                return;
            }
            $headers = \explode("\n", $settings['headers']);
            foreach ($headers as $header) {
                $header = \explode(':', $header, 2);
                if (2 === \count($header)) {
                    $args['headers'][$header[0]] = $header[1];
                } else {
                    $format = "<br /><pre><code>Key: value\nKey: value</code></pre>";
                    if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                        Helper::notice(esc_html__('Wrong Headers format', 'dynamic-content-for-elementor'), esc_html__('Please use this format', 'dynamic-content-for-elementor') . $format);
                    }
                    return;
                }
            }
        }
        // Parameters
        if (!empty($settings['parameters'])) {
            if ('GET' === $settings['method']) {
                foreach ($settings['parameters'] as $parameter) {
                    $url = add_query_arg($parameter['key'], $parameter['value'], $url);
                }
            } elseif ('POST' === $settings['method']) {
                $parameters = [];
                foreach ($settings['parameters'] as $parameter) {
                    $parameters[$parameter['key']] = $parameter['value'];
                }
                // JSON Encode
                if (!empty($settings['parameters_json_encode'])) {
                    $args['body'] = wp_json_encode($parameters);
                } else {
                    $args['body'] = $parameters;
                }
            }
        }
        // Basic Authentication
        if (!empty($settings['authorization_user']) && !empty($settings['authorization_pass'])) {
            $args['headers']['Authorization'] = 'Basic ' . $settings['authorization_user'] . ' ' . \base64_encode($settings['authorization_pass']);
        }
        // Connection Timeout
        if ($settings['connect_timeout']) {
            $args['timeout'] = \intval($settings['connect_timeout']);
        }
        $cache_age = $settings['cache_age'] ?? '5m';
        if ($settings['data_cache'] !== 'yes') {
            $cache_age = \false;
        }
        $response = $this->get_response($url, $args, $settings['method'], $cache_age);
        if ('' === $response) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                Helper::notice(\false, esc_html__('Can\'t fetch remote content. Please check url', 'dynamic-content-for-elementor'));
            }
            return;
        }
        $page_body = $this->retrieve_page_body($response, $settings);
        $host = '';
        if (!empty($settings['fix_links'])) {
            $pieces = \explode('/', $settings['url'], 4);
            \array_pop($pieces);
            $host = \implode('/', $pieces);
        }
        echo '<div class="dynamic-remote-content">';
        $showed = -1;
        foreach ($page_body as $key => $element) {
            ++$showed;
            if (!empty($settings['limit_contents']) && $showed > $settings['limit_contents']) {
                break;
            }
            if (!empty($settings['offset_contents']) && $key < $settings['offset_contents']) {
                continue;
            }
            echo '<div class="dynamic-remote-content-element">';
            if (!empty($settings['fix_links'])) {
                $element = \str_replace('href="/', 'href="' . $host . '/', $element);
            }
            if (\is_string($element)) {
                if (!empty($settings['lazy_images'])) {
                    $imgs = \explode('<img ', $element);
                    foreach ($imgs as $ikey => $aimg) {
                        if (\strpos($aimg, 'data-lazy-src') !== \false) {
                            $imgs[$ikey] = \str_replace(' src="', 'data-placeholder-src="', $imgs[$ikey]);
                            $imgs[$ikey] = \str_replace('data-lazy-src="', 'src="', $imgs[$ikey]);
                            $imgs[$ikey] = \str_replace('data-lazy-srcset="', 'srcset="', $imgs[$ikey]);
                            $imgs[$ikey] = \str_replace('data-lazy-sizes="', 'sizes="', $imgs[$ikey]);
                        }
                    }
                    $element = \implode('<img ', $imgs);
                }
                if (!empty($settings['blank_links'])) {
                    $anchors = \explode('<a ', $element);
                    foreach ($anchors as $akey => $anchor) {
                        if (\strpos($anchor, ' target="_') !== \false) {
                            $anchors[$akey] = 'target="_blank" ' . $anchors[$akey];
                        }
                    }
                    $element = \implode('<a ', $anchors);
                }
            }
            $element = apply_filters('dynamicooo/remote-content/html-element', $element);
            $element = $this->prepare_element_for_output($element, $settings);
            echo $element;
            echo '</div>';
        }
        echo '</div>';
    }
    /**
     * @param mixed $element
     * @param array<mixed> $settings
     * @return mixed
     */
    protected function prepare_element_for_output($element, $settings)
    {
        return $element;
    }
    /**
     * @return string
     */
    protected abstract function get_transient_prefix();
    /**
     * @param string $url
     * @param array<mixed> $args
     * @param string $method
     * @param string|false $max_age
     * @return string|array<mixed>
     */
    protected function get_response($url, $args, $method, $max_age)
    {
        if ($max_age) {
            /**
             * @var string
             */
            $md5_url = \md5($url);
            $md5_args = \md5(wp_json_encode($args) ?: '');
            $transient_key = $this->get_transient_prefix() . "{$max_age}_{$md5_url}_{$md5_args}";
            $transient = get_transient($transient_key);
            if (!\Elementor\Plugin::$instance->editor->is_edit_mode() && $transient !== \false) {
                return \json_decode($transient, \true);
            }
        }
        if ('POST' === $method) {
            $request = wp_safe_remote_post($url, $args);
        } else {
            $request = wp_safe_remote_get($url, $args);
        }
        if (is_wp_error($request)) {
            return '';
        }
        $response = wp_remote_retrieve_body($request);
        if ($max_age && !\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            set_transient($transient_key, wp_json_encode($response), self::CACHE_MAX_AGES[$max_age]);
        }
        return $response;
    }
    /**
     * @return array<string,string>
     */
    protected function get_cache_age_options()
    {
        return ['1m' => esc_html__('1 Minute', 'dynamic-content-for-elementor'), '5m' => esc_html__('5 Minutes', 'dynamic-content-for-elementor'), '15m' => esc_html__('15 Minutes', 'dynamic-content-for-elementor'), '1h' => esc_html__('1 Hour', 'dynamic-content-for-elementor'), '6h' => esc_html__('6 Hours', 'dynamic-content-for-elementor'), '12h' => esc_html__('12 Hours', 'dynamic-content-for-elementor'), '24h' => esc_html__('24 Hours', 'dynamic-content-for-elementor')];
    }
    /**
     * @param array<mixed> $element
     * @return array<mixed>
     */
    public function on_export($element)
    {
        unset($element['settings']['authorization_user']);
        unset($element['settings']['authorization_pass']);
        unset($element['settings']['url']);
        unset($element['settings']['headers']);
        unset($element['settings']['parameters']);
        return $element;
    }
    /**
     * @return void
     */
    protected abstract function add_data_section();
    /**
     * @param string|array<mixed> $response
     * @param array<mixed> $settings
     * @return array<mixed>
     */
    protected abstract function retrieve_page_body($response, $settings);
}
