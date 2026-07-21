<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use DynamicContentForElementor\Utils\ExpressionEvaluator;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Tokens;
use DynamicContentForElementor\Plugin;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class DynamicRedirect extends \ElementorPro\Modules\Forms\Classes\Action_Base
{
    public $has_action = \true;
    public function run_once()
    {
        $save_guard = \DynamicContentForElementor\Plugin::instance()->save_guard;
        $save_guard->register_unsafe_control('form', 'dce_form_redirect_repeater::dce_form_redirect_condition_field');
        $save_guard->register_unsafe_control('form', 'dce_form_redirect_repeater::dce_form_redirect_condition_status');
        $save_guard->register_unsafe_control('form', 'dce_form_redirect_repeater::dce_form_redirect_condition_value');
        $save_guard->register_unsafe_control('form', 'dce_form_redirect_repeater::dce_form_redirect_condition_mode');
        $save_guard->register_unsafe_control('form', 'dce_form_redirect_repeater::dce_form_redirect_condition_expression');
        $save_guard->register_unsafe_control('form', 'dce_form_redirect_repeater::dce_form_redirect_to');
    }
    /**
     * Get Name
     *
     * Return the action name
     *
     * @access public
     * @return string
     */
    public function get_name()
    {
        return 'dce_form_redirect';
    }
    public function get_script_depends()
    {
        return [];
    }
    public function get_style_depends()
    {
        return [];
    }
    /**
     * Get Label
     *
     * Returns the action label
     *
     * @access public
     * @return string
     */
    public function get_label()
    {
        return esc_html__('Dynamic Redirect', 'dynamic-content-for-elementor');
    }
    /**
     * Register Settings Section
     *
     * Registers the Action controls
     *
     * @access public
     * @param \Elementor\Widget_Base $widget
     */
    public function register_settings_section($widget)
    {
        $widget->start_controls_section('section_dce_form_redirect', ['label' => Helper::dce_logo() . $this->get_label(), 'condition' => ['submit_actions' => $this->get_name()]]);
        if (!\DynamicContentForElementor\Helper::can_register_unsafe_controls()) {
            $widget->add_control('admin_notice', ['name' => 'admin_notice', 'type' => Controls_Manager::RAW_HTML, 'raw' => esc_html__('You will need administrator capabilities to edit these settings.', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning']);
            $widget->end_controls_section();
            return;
        }
        Plugin::instance()->text_templates->maybe_add_notice($widget, 'dynamic_redirect');
        $repeater_fields = new \Elementor\Repeater();
        $repeater_fields->add_control('dce_form_redirect_condition_mode', ['label' => esc_html__('Condition Mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['simple' => esc_html__('Simple', 'dynamic-content-for-elementor'), 'advanced' => esc_html__('Advanced (Expression)', 'dynamic-content-for-elementor')], 'default' => 'simple']);
        $repeater_fields->add_control('dce_form_redirect_condition_field', ['label' => esc_html__('Field ID', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'description' => esc_html__('Type here the ID of the form field to check', 'dynamic-content-for-elementor'), 'dynamic' => ['active' => \false], 'condition' => ['dce_form_redirect_condition_mode' => 'simple']]);
        $repeater_fields->add_control('dce_form_redirect_condition_status', ['label' => esc_html__('Condition Status', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['empty' => ['title' => esc_html__('Empty', 'dynamic-content-for-elementor'), 'icon' => 'eicon-circle-o'], 'valued' => ['title' => esc_html__('Valorized with any value', 'dynamic-content-for-elementor'), 'icon' => 'eicon-dot-circle-o'], 'lt' => ['title' => esc_html__('Less than', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-angle-left'], 'gt' => ['title' => esc_html__('Greater than', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-angle-right'], 'equal' => ['title' => esc_html__('Equal to', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-circle'], 'contain' => ['title' => esc_html__('Contains', 'dynamic-content-for-elementor'), 'icon' => 'eicon-check']], 'default' => 'valued', 'toggle' => \false, 'label_block' => \true, 'condition' => ['dce_form_redirect_condition_mode' => 'simple']]);
        $repeater_fields->add_control('dce_form_redirect_condition_value', ['label' => esc_html__('Condition Value', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'description' => esc_html__('A value to compare the value of the field', 'dynamic-content-for-elementor'), 'condition' => ['dce_form_redirect_condition_mode' => 'simple', 'dce_form_redirect_condition_status' => ['lt', 'gt', 'equal', 'contain']]]);
        $repeater_fields->add_control('dce_form_redirect_condition_expression', ['label' => esc_html__('Expression', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXTAREA, 'description' => \sprintf(
            /* translators: %1$s: opening tag of the link to the Conditions Generator, %2$s: closing tag of the link */
            esc_html__('The redirect will occur only if this expression is true. One condition per line. All conditions are and-connected. You can use our online tool %1$sConditions Generator%2$s to generate your conditions more easily.', 'dynamic-content-for-elementor'),
            '<a target="_blank" href="' . esc_url('https://dnmc.ooo/condgen') . '">',
            '</a>'
        ), 'ai' => ['active' => \false], 'label_block' => \true, 'condition' => ['dce_form_redirect_condition_mode' => 'advanced']]);
        $repeater_fields->add_control('dce_form_redirect_to', ['label' => esc_html__('Redirect To', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'placeholder' => esc_html__('https://your-link.com', 'dynamic-content-for-elementor'), 'dynamic' => ['active' => \true, 'categories' => [TagsModule::POST_META_CATEGORY, TagsModule::TEXT_CATEGORY, TagsModule::URL_CATEGORY]], 'label_block' => \true, 'render_type' => 'none', 'classes' => 'elementor-control-direction-ltr']);
        $widget->add_control('dce_form_redirect_repeater', ['label' => esc_html__('Redirects', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::REPEATER, 'title_field' => '{{{ dce_form_redirect_to }}}', 'fields' => $repeater_fields->get_controls()]);
        $widget->end_controls_section();
    }
    /**
     * Run
     *
     * Runs the action after submit
     *
     * @access public
     * @param \ElementorPro\Modules\Forms\Classes\Form_Record $record
     * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
     */
    public function run($record, $ajax_handler)
    {
        $fields = Helper::get_form_data($record);
        $post_id = absint($_POST['post_id']);
        $form_id = sanitize_text_field($_POST['form_id']);
        if (!empty($fields['submitted_on_id'])) {
            // force post for Dynamic Tags and Widgets
            $submitted_on_id = absint($fields['submitted_on_id']);
            if ($submitted_on_id && Helper::can_current_user_view_post($submitted_on_id)) {
                global $post, $wp_query;
                $post = get_post($submitted_on_id);
                $wp_query->queried_object = $post;
                $wp_query->queried_object_id = $submitted_on_id;
            }
        }
        $document = \Elementor\Plugin::$instance->documents->get($post_id);
        if ($document) {
            $form = \ElementorPro\Modules\Forms\Module::find_element_recursive($document->get_elements_data(), $form_id);
            $widget = \Elementor\Plugin::$instance->elements_manager->create_element_instance($form);
            $settings = $widget->get_settings_for_display();
        } else {
            $settings = $record->get('form_settings');
        }
        $this->redirection($fields, $settings, $ajax_handler, $record);
    }
    protected function redirection($fields, $settings = null, $ajax_handler = null, $record = null)
    {
        foreach ($settings['dce_form_redirect_repeater'] as $mkey => $aredirect) {
            $condition_satisfy = \true;
            $condition_mode = $aredirect['dce_form_redirect_condition_mode'] ?? 'simple';
            $conditional_field = $aredirect['dce_form_redirect_condition_field'] ?? '';
            if ($condition_mode === 'advanced') {
                // Advanced expression mode
                if (!empty($aredirect['dce_form_redirect_condition_expression'])) {
                    try {
                        $condition_satisfy = ExpressionEvaluator::evaluate($aredirect['dce_form_redirect_condition_expression'], $fields);
                    } catch (\Throwable $e) {
                        $ajax_handler->add_admin_error_message(esc_html__('Dynamic Redirect condition error: ', 'dynamic-content-for-elementor') . $e->getMessage());
                        $condition_satisfy = \false;
                    }
                }
            } elseif ($condition_mode === 'simple' && !empty($conditional_field)) {
                // Simple condition mode
                $field_value = $fields[$conditional_field] ?? '';
                $condition_status = $aredirect['dce_form_redirect_condition_status'] ?? 'valued';
                $condition_value = $aredirect['dce_form_redirect_condition_value'] ?? '';
                // Convert checkbox values to array for contain check
                if ($condition_status === 'contain') {
                    $field_type = Helper::get_field_type($conditional_field, $settings);
                    if ($field_type === 'checkbox') {
                        $field_value = Helper::str_to_array(', ', $field_value);
                    }
                }
                $mapped_status = Helper::map_form_condition_status($condition_status);
                $condition_satisfy = Helper::is_condition_satisfied($field_value, $mapped_status, $condition_value);
            }
            if ($condition_satisfy) {
                $redirect_to = $aredirect['dce_form_redirect_to'];
                $redirect_to = Plugin::instance()->text_templates->expand_shortcodes_or_callback($redirect_to, ['form-fields' => $record->get('fields')], function ($str) use($fields) {
                    return Helper::get_dynamic_value($str, $fields);
                });
                if (!empty($redirect_to)) {
                    if (\filter_var($redirect_to, \FILTER_VALIDATE_URL)) {
                        $ajax_handler->add_response_data('redirect_url', $redirect_to);
                        return \true;
                    } else {
                        $hint = '';
                        if (is_user_logged_in()) {
                            $base_hint = esc_html__('Hint: url must begin with http and params should be encoded with "%1$s" filter, for example %2$s', 'dynamic-content-for-elementor');
                            $encoded_link = '<a target="_blank" href="https://www.php.net/manual/en/function.urlencode.php">urlencode</a>';
                            $hint_templates = ['dynamic-shortcodes' => \sprintf($base_hint, $encoded_link, '{form:name|urlencode}'), 'tokens' => \sprintf($base_hint, $encoded_link, '[form:name|urlencode]'), null => esc_html__('Hint: url must begin with http and params should be encoded', 'dynamic-content-for-elementor')];
                            $hint = \DynamicContentForElementor\Plugin::instance()->text_templates->get_default_value($hint_templates);
                        }
                        $ajax_handler->add_error_message('URL not valid: <a href="' . esc_url($redirect_to) . '" target="_blank">' . esc_html($redirect_to) . '</a><br />' . $hint);
                        return \false;
                    }
                }
            }
        }
        return \false;
    }
    public function on_export($element)
    {
        return $element;
    }
}
