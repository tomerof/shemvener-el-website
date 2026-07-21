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
class WebScraper extends \DynamicContentForElementor\Widgets\RemoteContentBase
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
        $this->add_control('tag_id', ['label' => esc_html__('Tag, ID or Class', 'dynamic-content-for-elementor'), 'description' => esc_html__('To include only subcontent of remote page. Use like jQuery selector (footer, #element, h2.big, etc).', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => 'body']);
        $this->add_control('limit_tags', ['label' => esc_html__('Limit elements', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'description' => esc_html__('Set -1 for unlimited', 'dynamic-content-for-elementor'), 'default' => -1]);
        $this->add_control('secure_mode', ['label' => esc_html__('Secure Mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_on' => esc_html__('On', 'dynamic-content-for-elementor'), 'label_off' => esc_html__('Off', 'dynamic-content-for-elementor'), 'return_value' => 'yes', 'default' => 'yes', 'description' => esc_html__('Removes scripts and other active content from remote HTML before rendering. Disable it only if the remote source is trusted and active content is required.', 'dynamic-content-for-elementor')]);
        $this->end_controls_section();
    }
    /**
     * @param string $response
     * @param array<mixed> $settings
     * @return array<string>
     */
    protected function retrieve_page_body($response, $settings)
    {
        if (!empty($settings['tag_id']) && \is_string($settings['tag_id'])) {
            $crawler = new \DynamicOOOS\Symfony\Component\DomCrawler\Crawler($response);
            $page_body = $crawler->filter($settings['tag_id'])->each(function (\DynamicOOOS\Symfony\Component\DomCrawler\Crawler $node) {
                return $node->html();
            });
        } else {
            $page_body = [$response];
        }
        if (!empty($settings['limit_tags']) && \is_numeric($settings['limit_tags']) && $settings['limit_tags'] > 0) {
            $page_body = \array_slice($page_body, 0, (int) $settings['limit_tags']);
        }
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
        $allowed_html = wp_kses_allowed_html('post');
        $excluded_tags = ['applet', 'base', 'button', 'embed', 'form', 'frame', 'frameset', 'iframe', 'input', 'link', 'math', 'meta', 'object', 'option', 'script', 'select', 'style', 'svg', 'template', 'textarea'];
        foreach ($excluded_tags as $tag) {
            unset($allowed_html[$tag]);
        }
        foreach ($allowed_html as &$attributes) {
            $attributes['class'] = \true;
            $attributes['id'] = \true;
            $attributes['role'] = \true;
            $attributes['style'] = \true;
            $attributes['title'] = \true;
            $attributes['aria-*'] = \true;
            $attributes['data-*'] = \true;
            foreach (\array_keys($attributes) as $attribute) {
                if (!\is_string($attribute)) {
                    continue;
                }
                if (0 === \stripos($attribute, 'on') || \in_array($attribute, ['action', 'formaction', 'http-equiv', 'srcdoc'], \true)) {
                    unset($attributes[$attribute]);
                }
            }
        }
        unset($attributes);
        return wp_kses($element, $allowed_html, wp_allowed_protocols());
    }
    /**
     * @return string
     */
    public function get_transient_prefix()
    {
        return 'dce_web_scraper_';
    }
}
