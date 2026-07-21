<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Extensions\DynamicVisibility\Triggers;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Extensions\DynamicVisibility;
use DynamicContentForElementor\Extensions\DynamicVisibility\Elements;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Manager
{
    /**
     * @var array<string,mixed>
     */
    public $triggers = [];
    /**
     * @var Elements
     */
    protected $elements;
    /**
     * @var array<string,array<string,mixed>>
     */
    protected $triggers_list = [];
    /**
     * @param Elements $elements
     */
    public function __construct($elements)
    {
        $this->elements = $elements;
        $this->init_triggers();
        $this->register_conditions();
    }
    /**
     * @return void
     */
    protected function init_triggers()
    {
        $triggers = [
            'post' => ['label' => esc_html__('Post and Page', 'dynamic-content-for-elementor'), 'class' => \DynamicContentForElementor\Extensions\DynamicVisibility\Triggers\Post::class],
            'user' => ['label' => esc_html__('User and Role', 'dynamic-content-for-elementor'), 'class' => \DynamicContentForElementor\Extensions\DynamicVisibility\Triggers\User::class],
            'archive' => ['label' => esc_html__('Archive', 'dynamic-content-for-elementor'), 'class' => \DynamicContentForElementor\Extensions\DynamicVisibility\Triggers\Archive::class],
            'dynamic_tag' => ['label' => esc_html__('Dynamic Tag', 'dynamic-content-for-elementor'), 'class' => \DynamicContentForElementor\Extensions\DynamicVisibility\Triggers\DynamicTag::class],
            'device' => ['label' => esc_html__('Device and Browser', 'dynamic-content-for-elementor'), 'class' => \DynamicContentForElementor\Extensions\DynamicVisibility\Triggers\Device::class],
            'datetime' => ['label' => esc_html__('Date and Time', 'dynamic-content-for-elementor'), 'class' => \DynamicContentForElementor\Extensions\DynamicVisibility\Triggers\DateTime::class],
            'geotargeting' => ['label' => esc_html__('Geotargeting', 'dynamic-content-for-elementor'), 'class' => \DynamicContentForElementor\Extensions\DynamicVisibility\Triggers\Geotargeting::class],
            'context' => ['label' => esc_html__('Context', 'dynamic-content-for-elementor'), 'class' => \DynamicContentForElementor\Extensions\DynamicVisibility\Triggers\Context::class],
            'woocommerce' => ['label' => 'WooCommerce', 'class' => \DynamicContentForElementor\Extensions\DynamicVisibility\Triggers\WooCommerce::class],
            //+exclude_start
            'favorites' => ['label' => esc_html__('Favorites', 'dynamic-content-for-elementor'), 'class' => \DynamicContentForElementor\Extensions\DynamicVisibility\Triggers\Favorites::class],
            //+exclude_end
            'random' => ['label' => esc_html__('Random', 'dynamic-content-for-elementor'), 'class' => \DynamicContentForElementor\Extensions\DynamicVisibility\Triggers\Random::class],
            'custom' => ['label' => esc_html__('Custom Condition', 'dynamic-content-for-elementor'), 'class' => \DynamicContentForElementor\Extensions\DynamicVisibility\Triggers\Custom::class],
            'myfastapp' => ['label' => 'My FastAPP', 'class' => \DynamicContentForElementor\Extensions\DynamicVisibility\Triggers\MyFastApp::class],
            'events' => ['label' => esc_html__('JS Events', 'dynamic-content-for-elementor'), 'class' => \DynamicContentForElementor\Extensions\DynamicVisibility\Triggers\Events::class],
        ];
        /**
         * Filters the list of available triggers for Dynamic Visibility.
         *
         * @since 3.3.5
         *
         * @param array<string,array<string,mixed>> $triggers The list of triggers.
         */
        $this->triggers_list = apply_filters('dynamicooo/dynamic-visibility/triggers', $triggers);
    }
    /**
     * @return array<string,array<string,mixed>>
     */
    public function get_triggers()
    {
        return $this->triggers_list;
    }
    /**
     * @return void
     */
    protected function register_conditions()
    {
        foreach ($this->triggers_list as $trigger_id => $trigger_data) {
            if (isset($trigger_data['class']) && \class_exists($trigger_data['class'])) {
                $this->triggers[$trigger_id] = new $trigger_data['class']();
            }
        }
    }
    /**
     * @param \Elementor\Element_Base $element
     * @return void
     */
    public function register_base_controls($element)
    {
        $element->add_control('enabled_visibility', ['label' => esc_html__('Visibility', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $element->add_control('dce_visibility_hidden', ['label' => esc_html__('Always hide this element', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['enabled_visibility' => 'yes'], 'separator' => 'before']);
        $element->add_control('dce_visibility_dom', ['label' => esc_html__('Keep HTML', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('Keep the HTML element in the DOM and hide this element via CSS', 'dynamic-content-for-elementor'), 'condition' => ['enabled_visibility' => 'yes'], 'separator' => 'before']);
        $element->add_control('dce_visibility_selected', ['label' => esc_html__('Display mode', 'dynamic-content-for-elementor'), 'description' => esc_html__('Hide or show an element when a condition is triggered', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['yes' => ['title' => esc_html__('Show', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-eye'], 'hide' => ['title' => esc_html__('Hide', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-eye-slash']], 'default' => 'yes', 'toggle' => \false, 'condition' => ['enabled_visibility' => 'yes', 'dce_visibility_hidden' => ''], 'frontend_available' => \true]);
        $element->add_control('dce_visibility_logical_connective', ['label' => esc_html__('Logical connective', 'dynamic-content-for-elementor'), 'description' => esc_html__('This setting determines how the conditions are combined. If OR is selected the condition is satisfied when at least one condition is satisfied. If AND is selected all conditions must be satisfied', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'or', 'return_value' => 'and', 'label_on' => esc_html__('AND', 'dynamic-content-for-elementor'), 'label_off' => esc_html__('OR', 'dynamic-content-for-elementor'), 'condition' => ['enabled_visibility' => 'yes', 'dce_visibility_hidden' => '']]);
        $triggers_list_options = $this->get_triggers_options($element->get_type());
        $element->add_control('dce_visibility_triggers', ['label' => esc_html__('Triggers', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => $triggers_list_options, 'default' => \array_keys($triggers_list_options), 'multiple' => \true, 'label_block' => \true, 'condition' => ['enabled_visibility' => 'yes', 'dce_visibility_hidden' => '']]);
        $element->add_control('dce_visibility_triggers_deselect', ['type' => Controls_Manager::BUTTON, 'text' => esc_html__('Deselect All Triggers', 'dynamic-content-for-elementor'), 'event' => 'dceVisibility:deselect_triggers', 'condition' => ['enabled_visibility' => 'yes', 'dce_visibility_hidden' => '']]);
        if (\defined('DVE_PLUGIN_BASE')) {
            $element->add_control('dce_visibility_review', ['label' => '<b>' . esc_html__('Did you enjoy Dynamic Visibility extension?', 'dynamic-content-for-elementor') . '</b>', 'type' => Controls_Manager::RAW_HTML, 'raw' => \strtr(
                /* translators: %1$s: opening link, %2$s: closing link, %3$s: line break */
                esc_html__('Please leave us a %1$s★★★★★%2$s rating.%3$sWe really appreciate your support!', 'dynamic-content-for-elementor'),
                ['%1$s' => '<a target="_blank" href="https://wordpress.org/support/plugin/dynamic-visibility-for-elementor/reviews/?filter=5/#new-post">', '%2$s' => '</a>', '%3$s' => '<br>']
            ), 'separator' => 'before']);
        }
    }
    /**
     * @param \Elementor\Element_Base $element
     * @return void
     */
    public function register_fallback_controls($element)
    {
        $element->add_control('dce_visibility_fallback', ['label' => esc_html__('Enable Fallback', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['enabled_visibility' => 'yes']]);
        $element->add_control('dce_visibility_fallback_type', ['label' => esc_html__('Content type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['text' => ['title' => esc_html__('Text', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'template' => ['title' => esc_html__('Template', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-th-large']], 'default' => 'text', 'condition' => ['enabled_visibility' => 'yes', 'dce_visibility_fallback!' => '']]);
        $element->add_control('dce_visibility_fallback_template', ['label' => esc_html__('Render Template', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Template Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'object_type' => 'elementor_library', 'condition' => ['dce_visibility_fallback!' => '', 'dce_visibility_fallback_type' => 'template']]);
        $element->add_control('dce_visibility_fallback_text', ['label' => esc_html__('Text Fallback', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::WYSIWYG, 'default' => esc_html__('This element is currently hidden.', 'dynamic-content-for-elementor'), 'condition' => ['enabled_visibility' => 'yes', 'dce_visibility_fallback!' => '', 'dce_visibility_fallback_type' => 'text']]);
    }
    /**
     * Get triggers options for dropdowns
     *
     * @param string $element_type
     * @return array<string,string>
     */
    protected function get_triggers_options($element_type)
    {
        $elements = $this->elements->get();
        $is_page_type = isset($elements[$element_type]) && $this->elements::TRIGGER_TYPE_PAGE === $elements[$element_type]['type'];
        $options = [];
        foreach ($this->triggers_list as $trigger_id => $trigger_data) {
            if ($trigger_id === 'events' && $is_page_type) {
                continue;
            }
            if ($trigger_id === 'myfastapp' && !Helper::is_plugin_active('myfastapp')) {
                continue;
            }
            if (isset($this->triggers[$trigger_id])) {
                $condition = $this->triggers[$trigger_id];
                if (!$condition->is_available()) {
                    if ($condition->get_availability_requirements_message()) {
                        $options[$trigger_id] = $trigger_data['label'];
                    }
                } else {
                    $options[$trigger_id] = $trigger_data['label'];
                }
            }
        }
        return $options;
    }
    /**
     * @param \Elementor\Element_Base $element
     * @param string $condition_key
     * @return void
     */
    public function register_condition_controls($element, $condition_key)
    {
        if (isset($this->triggers[$condition_key])) {
            $condition = $this->triggers[$condition_key];
            if (!$condition->is_available()) {
                $message = $condition->get_promotion_message();
                if ($message) {
                    $element->add_control('dce_visibility_' . $condition_key . '_notice', ['type' => Controls_Manager::RAW_HTML, 'raw' => $message, 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning']);
                }
                return;
            }
            $condition->register_controls($element);
        }
    }
    /**
     * @param array<string,mixed> $settings
     * @param \Elementor\Element_Base $element
     * @return array<string,mixed>
     */
    public function check_conditions($settings, $element)
    {
        if (empty($settings['dce_visibility_triggers'])) {
            return ['triggers' => [], 'conditions' => [], 'required' => []];
        }
        $triggers_list = [];
        $triggers = [];
        $required = [];
        foreach ($settings['dce_visibility_triggers'] as $trigger) {
            if (isset($this->triggers[$trigger])) {
                $condition = $this->triggers[$trigger];
                if (!$condition->is_available()) {
                    continue;
                }
                $condition->check_conditions($settings, $triggers_list, $triggers, $required, $element);
            }
        }
        return ['triggers' => $triggers_list, 'conditions' => $triggers, 'required' => $required];
    }
}
