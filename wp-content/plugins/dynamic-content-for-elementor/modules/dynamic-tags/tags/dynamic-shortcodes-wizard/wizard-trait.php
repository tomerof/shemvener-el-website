<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Modules\DynamicTags\Tags\DynamicShortcodesWizard;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
trait WizardTrait
{
    /**
     * @return void
     */
    protected function register_controls_settings()
    {
        // Remove types if the related plugin is not active
        /**
         * @var array<string,mixed>
         */
        $types = \array_filter(\DynamicContentForElementor\Modules\DynamicTags\Tags\DynamicShortcodesWizard\Engine::get_types(), function ($type) {
            if (!isset($type['plugin_depends'])) {
                return \true;
            }
            return Helper::is_plugin_active($type['plugin_depends']);
        });
        $unique_ids = ['post', 'term', 'user'];
        $options = [];
        foreach ($types as $key => $value) {
            $options[$key] = $value['label'];
        }
        $types_keys_with_ids = \array_keys(\array_filter($types, function ($type) {
            return !empty($type['from']);
        }));
        $types_accept_filter = \array_keys(\array_filter($types, function ($type) {
            return !empty($type['filter']) && \true === $type['filter'];
        }));
        $types_accept_fallback = \array_keys(\array_filter($types, function ($type) {
            return !empty($type['fallback']) && \true === $type['fallback'];
        }));
        $this->add_control('status', ['type' => Controls_Manager::HIDDEN, 'default' => 'pre']);
        $this->add_control('notice_pre', ['content' => esc_html__('Select the options for your content, and our wizard will automatically generate the Dynamic Shortcode for you.', 'dynamic-shortcodes'), 'type' => Controls_Manager::NOTICE, 'notice_type' => 'info', 'condition' => ['status' => 'pre']]);
        $this->add_control('notice_edit_mode', ['content' => esc_html__('You are in edit mode, remember to generate it.', 'dynamic-shortcodes'), 'type' => Controls_Manager::NOTICE, 'notice_type' => 'warning', 'condition' => ['status' => 'edit']]);
        $this->add_control('dsh_type', ['label' => esc_html__('Type', 'dynamic-shortcodes'), 'label_block' => \true, 'type' => Controls_Manager::SELECT, 'options' => $options, 'default' => 'post', 'condition' => ['status!' => ['generated', 'pre']]]);
        foreach ($types as $type => $info) {
            if (\true === ($info['require_field'] ?? \false)) {
                $this->add_control($type . '_field', ['label' => esc_html__('Field', 'dynamic-shortcodes'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Meta key or Field Name', 'dynamic-shortcodes'), 'label_block' => \true, 'query_type' => 'dsh_fields', 'object_type' => $type, 'condition' => ['dsh_type' => $type, 'status!' => ['generated', 'pre']]]);
            }
        }
        foreach ($types_keys_with_ids as $type_key) {
            $from_options = [];
            foreach ($types[$type_key]['from'] as $id => $value) {
                if ('option' === $id) {
                    $from_options['option'] = 'Option';
                } else {
                    $from_options['current_' . $id] = \sprintf(esc_html__('Current %s', 'dynamic-shortcodes'), $value['label']);
                    $from_options['another_' . $id] = \sprintf(esc_html__('Another %s', 'dynamic-shortcodes'), $value['label']);
                }
            }
            $this->add_control($type_key . '_from', ['label' => esc_html__('From', 'dynamic-shortcodes'), 'type' => Controls_Manager::SELECT, 'label_block' => \true, 'options' => $from_options, 'default' => \array_key_first($from_options), 'condition' => ['dsh_type' => $type_key, 'status!' => ['generated', 'pre']]]);
            foreach ($types[$type_key]['from'] as $id => $value) {
                $options = ['label' => esc_html__('Source', 'dynamic-shortcodes') . ' ' . $value['label'], 'type' => 'ooo_query', 'placeholder' => esc_html__('Search', 'dynamic-shortcodes') . ' ' . $value['label'] . '...', 'label_block' => \true, 'query_type' => $id . 's', 'condition' => ['dsh_type' => $type_key, $type_key . '_from' => 'another_' . $id, 'status!' => ['generated', 'pre']]];
                if ('post' === $id) {
                    $options['post_type'] = $value['post_type'];
                }
                $this->add_control($type_key . '_source_' . $id, $options);
            }
        }
        $this->add_control('format_as_date', ['label' => esc_html__('Format as Date', 'dynamic-shortcodes'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'label_on' => esc_html__('Yes', 'dynamic-shortcodes'), 'label_off' => esc_html__('No', 'dynamic-shortcodes'), 'return_value' => 'yes', 'separator' => 'before', 'default' => '', 'condition' => ['status!' => ['generated', 'pre'], 'dsh_type!' => 'date']]);
        $this->add_control('date_modificator', ['label' => esc_html__('Date Modificator', 'dynamic-shortcodes'), 'type' => \Elementor\Controls_Manager::SELECT, 'options' => ['custom' => esc_html__('Custom...', 'dynamic-shortcodes'), 'now' => 'now', 'yesterday' => 'yesterday', 'tomorrow' => 'tomorrow', '-10 minutes' => '-10 minutes', '+10 minutes' => '+10 minutes', '-1 hour' => '-1 hour', '+1 hour' => '+1 hour', '-1 day' => '-1 day', '+1 day' => '+1 day', '+1 week' => '+1 week', '-1 week' => '-1 week', 'first day of this month' => 'first day of this month', 'last day of this month' => 'last day of this month', 'first day of next month' => 'first day of next month', 'last day of next month' => 'last day of next month', 'first day of last month' => 'first day of last month', 'last day of last month' => 'last day of last month', '+1 month' => '+1 month', '-1 month' => '-1 month', '+1 year' => '+1 year', '-1 year' => '-1 year', 'next Monday' => 'next Monday', 'next Sunday' => 'next Sunday'], 'default' => 'now', 'label_block' => \true, 'conditions' => ['relation' => 'or', 'terms' => [['terms' => [['name' => 'dsh_type', 'value' => 'date', 'operator' => '==='], ['name' => 'status', 'operator' => '!in', 'value' => ['generated', 'pre']]]]]]]);
        $this->add_control('date_modificator_custom', ['label' => esc_html__('Insert your modificator', 'dynamic-shortcodes'), 'type' => \Elementor\Controls_Manager::TEXT, 'label_block' => \true, 'default' => '+1 day', 'ai' => ['active' => \false], 'conditions' => ['relation' => 'or', 'terms' => [['terms' => [['name' => 'dsh_type', 'value' => 'date', 'operator' => '==='], ['name' => 'status', 'operator' => '!in', 'value' => ['generated', 'pre']], ['name' => 'date_modificator', 'operator' => '===', 'value' => 'custom']]]]]]);
        $date_formats = ['custom' => esc_html__('Custom...', 'dynamic-shortcodes'), 'Y-m-d' => 'Y-m-d', 'd/m/Y' => 'd/m/Y', 'm-d-Y' => 'm-d-Y', 'Y/m/d' => 'Y/m/d', 'd-m-Y' => 'd-m-Y', 'd M Y' => 'd M Y', 'M d, Y' => 'M d, Y', 'D, d M Y H:i:s' => 'D, d M Y H:i:s', 'l, jS F Y' => 'l, jS F Y', 'Y-m-d H:i:s' => 'Y-m-d H:i:s', 'd/m/Y H:i' => 'd/m/Y H:i', 'H:i:s' => 'H:i:s', 'h:i:s A' => 'h:i:s A', 'Y' => 'Y', 'm' => 'm', 'F' => 'F', 'd' => 'd', 'D' => 'D', 'l' => 'l', 'W' => 'W'];
        $current_date_formats = [];
        foreach ($date_formats as $key => $format) {
            if ('custom' === $key) {
                $current_date_formats[$key] = $format;
            } else {
                $current_date_formats[$key] = $format . ' (' . \date($format) . ')';
            }
        }
        $this->add_control('date_format', ['label' => esc_html__('Date Format', 'dynamic-shortcodes'), 'type' => \Elementor\Controls_Manager::SELECT, 'options' => $current_date_formats, 'label_block' => \true, 'default' => 'Y-m-d H:i:s', 'conditions' => ['relation' => 'or', 'terms' => [['terms' => [['name' => 'dsh_type', 'value' => 'date', 'operator' => '==='], ['name' => 'status', 'operator' => '!in', 'value' => ['generated', 'pre']]]], ['terms' => [['name' => 'dsh_type', 'value' => 'date', 'operator' => '!=='], ['name' => 'status', 'operator' => '!in', 'value' => ['generated', 'pre']], ['name' => 'format_as_date', 'operator' => '!==', 'value' => '']]]]]]);
        $this->add_control('date_format_custom', ['label' => esc_html__('Insert your format', 'dynamic-shortcodes'), 'type' => \Elementor\Controls_Manager::TEXT, 'label_block' => \true, 'default' => 'Y-m-d H:i:s', 'ai' => ['active' => \false], 'conditions' => ['relation' => 'or', 'terms' => [['terms' => [['name' => 'dsh_type', 'value' => 'date', 'operator' => '==='], ['name' => 'status', 'operator' => '!in', 'value' => ['generated', 'pre']], ['name' => 'date_format', 'operator' => '==', 'value' => 'custom']]], ['terms' => [['name' => 'dsh_type', 'value' => 'date', 'operator' => '!=='], ['name' => 'status', 'operator' => '!in', 'value' => ['generated', 'pre']], ['name' => 'format_as_date', 'operator' => '!==', 'value' => ''], ['name' => 'date_format', 'operator' => '==', 'value' => 'custom']]]]]]);
        $this->add_control('use_filter', ['label' => esc_html__('Use Filter', 'dynamic-shortcodes'), 'type' => Controls_Manager::SWITCHER, 'separator' => 'before', 'condition' => ['dsh_type' => $types_accept_filter, 'status!' => ['generated', 'pre']]]);
        $this->add_control('filter_type', ['label' => esc_html__('Filter Type', 'dynamic-shortcodes'), 'type' => Controls_Manager::SELECT, 'options' => ['pipe_first' => esc_html__('PHP Function', 'dynamic-shortcodes'), 'array_access' => esc_html__('Array Access', 'dynamic-shortcodes')], 'default' => 'pipe_first', 'label_block' => \true, 'condition' => ['dsh_type' => $types_accept_filter, 'use_filter!' => '', 'status!' => ['generated', 'pre']]]);
        $this->add_control('function_name', ['label' => esc_html__('PHP Function Name', 'dynamic-shortcodes'), 'type' => Controls_Manager::TEXT, 'placeholder' => 'strtoupper', 'label_block' => \true, 'ai' => ['active' => \false], 'condition' => ['dsh_type' => $types_accept_filter, 'use_filter!' => '', 'filter_type!' => 'array_access', 'status!' => ['generated', 'pre']]]);
        $this->add_control('array_key', ['label' => esc_html__('Array Key', 'dynamic-shortcodes'), 'type' => Controls_Manager::TEXT, 'default' => 0, 'label_block' => \true, 'ai' => ['active' => \false], 'condition' => ['dsh_type' => $types_accept_filter, 'use_filter!' => '', 'filter_type' => 'array_access', 'status!' => ['generated', 'pre']]]);
        $this->add_control('shortcode_generated', ['content' => esc_html__('Dynamic Shortcode generated.', 'dynamic-shortcodes'), 'type' => Controls_Manager::NOTICE, 'notice_type' => 'success', 'condition' => ['shortcode!' => '', 'status' => 'generated']]);
        $this->add_control('shortcode', ['type' => 'dce-textarea-readonly', 'condition' => ['status' => 'generated']]);
        $this->add_control('start', ['type' => Controls_Manager::BUTTON, 'button_type' => 'success', 'text' => esc_html__('Start', 'dynamic-shortcodes'), 'event' => 'dynamicShortcodesWizard::startShortcode', 'condition' => ['status' => 'pre']]);
        $this->add_control('generate', ['type' => Controls_Manager::BUTTON, 'button_type' => 'success', 'text' => esc_html__('Generate Dynamic Shortcode', 'dynamic-shortcodes'), 'event' => 'dynamicShortcodesWizard::generateShortcode', 'condition' => ['status!' => ['generated', 'pre']]]);
        $this->add_control('edit', ['type' => Controls_Manager::BUTTON, 'button_type' => 'info', 'render_type' => 'template', 'text' => esc_html__('Edit', 'dynamic-shortcodes'), 'event' => 'dynamicShortcodesWizard::editShortcode', 'condition' => ['shortcode!' => '', 'status' => 'generated']]);
        $this->add_control('copy', ['type' => Controls_Manager::BUTTON, 'button_type' => 'warning', 'render_type' => 'ui', 'text' => esc_html__('Copy', 'dynamic-shortcodes'), 'event' => 'dynamicShortcodesWizard::copyShortcode', 'condition' => ['shortcode!' => '', 'status' => 'generated']]);
    }
}
