<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Extensions;

use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Tokens;
use DynamicContentForElementor\Utils\ExpressionEvaluator;
use DynamicOOOS\Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Repeater;
use ElementorPro\Plugin;
use ElementorPro\Modules\Forms\Fields\Field_Base;
use ElementorPro\Modules\Forms\Module as Form_Module;
use ElementorPro\Modules\Forms\Registrars\Form_Actions_Registrar;
use ElementorPro\Modules\Forms\Classes\Form_Record;
use ElementorPro\Modules\Forms\Classes\Ajax_Handler;
if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
class ConditionalFields extends \DynamicContentForElementor\Extensions\ExtensionPrototype
{
    /**
     * @var bool
     */
    public $has_action = \false;
    /**
     * @var string[]
     */
    public $depended_scripts = ['dce-conditional-fields'];
    /**
     * @var string[]
     */
    public $depended_styles = ['dce-conditional-fields'];
    /**
     * @var string[]
     */
    public $conditional_actions = ['activecampaign', 'mailchimp', 'webhook', 'mailerlite', 'mailpoet', 'mailpoet3', 'dce_form_export'];
    /**
     * @return string
     */
    public function get_name()
    {
        return 'dce_conditional_fields_v2';
    }
    /**
     * @return void
     */
    public function run_once()
    {
        $save_guard = \DynamicContentForElementor\Plugin::instance()->save_guard;
        $save_guard->register_unsafe_control('form', 'dce_conditional_validations::expression');
        $save_guard->register_unsafe_control('form', 'form_fields::dce_conditions_expression');
        foreach ($this->conditional_actions as $action) {
            $save_guard->register_unsafe_control('form', "dce_action_condition_{$action}_expression");
        }
    }
    /**
     * @return string
     */
    public function get_label()
    {
        return esc_html__('Conditional Fields', 'dynamic-content-for-elementor');
    }
    /**
     * @param mixed $field
     * @return bool
     */
    private static function are_conditions_enabled($field)
    {
        $enabled = $field['dce_field_conditions_mode'] === 'show' || $field['dce_field_conditions_mode'] === 'hide';
        return $enabled && !\preg_match('/^\\s*$/', $field['dce_conditions_expression']);
    }
    /**
     * @param mixed $instance
     * @return array<int, array<string, mixed>>
     */
    private function get_fields_conditions($instance)
    {
        $conditions = [];
        foreach ($instance['form_fields'] as $field) {
            if (self::are_conditions_enabled($field)) {
                $conditions[] = ['id' => $field['custom_id'], 'condition' => ExpressionEvaluator::and_join_lines($field['dce_conditions_expression']), 'mode' => $field['dce_field_conditions_mode'], 'disableOnly' => $field['dce_conditions_disable_only'] === 'yes'];
            }
        }
        return $conditions;
    }
    /**
     * @param mixed $instance
     * @return array<int, array<string, mixed>>
     */
    private function get_submit_conditions($instance)
    {
        $conditions = [];
        foreach ($instance['dce_conditional_validations'] as $validation) {
            $hide = $validation['hide_submit'];
            if ($validation['disabled'] !== 'yes' && $hide !== '' && $hide !== 'visible') {
                $conditions[] = [
                    'expression' => ExpressionEvaluator::and_join_lines($validation['expression']),
                    // yes is the old value of the option
                    'hide' => $hide === 'yes' ? 'hide' : $hide,
                ];
            }
        }
        return $conditions;
    }
    /**
     * @return void
     */
    public function print_js_error_notice()
    {
        echo '<div class="dce-conditions-js-error-notice elementor-message elementor-message-danger" style="display: none;">';
        if (current_user_can('administrator') && !\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            \printf(
                /* translators: %s: Link to the article. */
                esc_html__('Dynamic.ooo - Conditional Fields: a JS Error has been detected. This could be caused by a JS Optimizer plugin. Please read this %1$sarticle%2$s. This message is not visible to site visitors', 'dynamic-content-for-elementor'),
                '<a href="' . esc_url('https://dnmc.ooo/jserror') . '">',
                '</a>'
            );
        } else {
            echo esc_html__('A problem was detected in the following Form. Submitting it could result in errors. Please contact the site administrator.', 'dynamic-content-for-elementor');
        }
        // the message is hidden on page load to avoid flash of content. If
        // everything is ok the error is than deleted by the js file of
        // conditional fields. If not we want the error to appear:
        echo '</div>';
        echo <<<'SCRIPT'
			<script>
			setTimeout(function() {
				let el = document.querySelector(".dce-conditions-js-error-notice");
				if (el)
					el.style.display = "block";
			}, 6000);
			</script>
SCRIPT;
    }
    /**
     * @param mixed $instance
     * @param mixed $form
     * @return void
     */
    public function add_assets_depends($instance, $form)
    {
        // fetch all the settings data we need to pass to the JavaScript code:
        $field_conditions = $this->get_fields_conditions($instance);
        $submit_conditions = $this->get_submit_conditions($instance);
        $enabled = \false;
        if (!empty($field_conditions)) {
            $form->add_render_attribute('wrapper', 'data-field-conditions', wp_json_encode($field_conditions));
            $enabled = \true;
        }
        if (!empty($submit_conditions)) {
            $form->add_render_attribute('wrapper', 'data-submit-conditions', wp_json_encode($submit_conditions));
            $enabled = \true;
        }
        if ($enabled) {
            if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                $this->print_js_error_notice();
            }
            $field_ids = [];
            foreach ($instance['form_fields'] as $field) {
                $field_ids[] = $field['custom_id'];
            }
            $form->add_render_attribute('wrapper', 'data-field-ids', wp_json_encode($field_ids));
            foreach ($this->depended_scripts as $script) {
                wp_enqueue_script($script);
            }
            foreach ($this->depended_styles as $style) {
                wp_enqueue_style($style);
            }
        }
    }
    /**
     * @return void
     */
    protected function add_actions()
    {
        add_action('elementor/element/form/section_form_fields/before_section_end', [$this, 'update_fields_controls']);
        add_action('elementor/element/form/section_buttons/after_section_end', [$this, 'update_validation_controls']);
        add_action('elementor/element/form/section_buttons/after_section_end', [$this, 'update_max_submissions_controls']);
        add_action('elementor-pro/forms/pre_render', [$this, 'add_assets_depends'], 10, 2);
        // very low priority because it needs to fix validation of other validation hooks.
        add_action('elementor_pro/forms/validation', [$this, 'validation'], 1000, 2);
        foreach ($this->conditional_actions as $action) {
            add_action("elementor/element/form/section_{$action}/before_section_end", [$this, 'update_actions_controls']);
        }
        add_action('elementor_pro/forms/process', [$this, 'actions_validation'], 10, 2);
        add_filter('wpml_elementor_widgets_to_translate', [$this, 'wpml_widgets_to_translate_filter'], 50, 1);
    }
    /**
     * @param mixed $widget
     * @return void
     */
    public function update_max_submissions_controls($widget)
    {
        $widget->start_controls_section('section_max_submissions', ['label' => '<span class="color-dce icon-dce-logo-dce pull-right ml-1"></span> ' . esc_html__('Max Submissions', 'dynamic-content-for-elementor')]);
        $widget->add_control('dce_max_submissions_enabled', ['label' => esc_html__('Enable', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SWITCHER]);
        $widget->add_control('dce_max_submission_counter_name', ['condition' => ['dce_max_submissions_enabled' => 'yes'], 'label' => esc_html__('Name of the Counter Field to be checked', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'ai' => ['active' => \false], 'label_block' => \true]);
        $widget->add_control('dce_max_sumbissions_limit', ['condition' => ['dce_max_submissions_enabled' => 'yes'], 'label' => esc_html__('Max', 'dynamic-content-for-elementor'), 'default' => 100, 'description' => esc_html__('The limit that the counter must not exceed. Note that in very rare circumstances the limit may actually be exceeded. This can happen if the counter value is close to the limit and multiple submit attempts are made at the same time.', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::NUMBER, 'label_block' => \true]);
        $widget->add_control('dce_max_submissions_error_message', ['condition' => ['dce_max_submissions_enabled' => 'yes'], 'label' => esc_html__('Error Message', 'dynamic-content-for-elementor'), 'default' => esc_html__('Too many submissions, sorry', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'ai' => ['active' => \false], 'label_block' => \true]);
        $widget->end_controls_section();
    }
    /**
     * @param mixed $widget
     * @return void
     */
    public function update_validation_controls($widget)
    {
        $widget->start_controls_section('section_conditional_validation', ['label' => '<span class="color-dce icon-dce-logo-dce pull-right ml-1"></span> ' . esc_html__('Conditional Validation', 'dynamic-content-for-elementor')]);
        $repeater = new \Elementor\Repeater();
        $repeater->add_control('disabled', ['label' => esc_html__('Disable', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SWITCHER]);
        $repeater->add_control('expression', ['condition' => ['disabled!' => 'yes'], 'label' => esc_html__('Expression', 'dynamic-content-for-elementor'), 'description' => \sprintf(
            /* translators: %1$s: opening tag of the link to the Conditions Generator, %2$s: closing tag of the link */
            esc_html__('One condition per line. All conditions are and-connected. Conditions are expressions that can also use the or operator and much more! You can use our online tool %1$sConditions Generator%2$s to generate your conditions more easily.', 'dynamic-content-for-elementor'),
            '<a target="_blank" href="' . esc_url('https://dnmc.ooo/condgen') . '">',
            '</a>'
        ), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'ai' => ['active' => \false], 'label_block' => \true]);
        $repeater->add_control('error_message', ['condition' => ['disabled!' => 'yes'], 'label' => esc_html__('Error Message', 'dynamic-content-for-elementor'), 'default' => esc_html__('Form Validation Error', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'ai' => ['active' => \false], 'label_block' => \true]);
        $repeater->add_control('error_field_id', ['condition' => ['disabled!' => 'yes'], 'label' => esc_html__('Field ID to attach the error to (optional)', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'ai' => ['active' => \false], 'label_block' => \true]);
        $repeater->add_control('hide_submit', ['condition' => ['disabled!' => 'yes'], 'label' => esc_html__('If this condition is not satisfied the Submit Button should be', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SELECT, 'options' => ['visible' => esc_html__('Visible', 'dynamic-content-for-elementor'), 'hide' => esc_html__('Hidden', 'dynamic-content-for-elementor'), 'disable' => esc_html__('Disabled', 'dynamic-content-for-elementor')], 'default' => 'visible', 'label_block' => \true]);
        $widget->add_control('dce_conditional_validations', ['label' => esc_html__('Conditional Validations', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(), 'default' => [['disabled' => 'yes', 'error_message' => 'Your name should be Joe', 'expression' => 'name == "Joe"', 'error_field_id' => 'name']], 'title_field' => 'Condition {{ error_field_id }}', 'hide_submit' => 'no']);
        $widget->end_controls_section();
    }
    /**
     * @param \Elementor\Controls_Stack $widget
     * @return void
     */
    public function update_actions_controls(\Elementor\Controls_Stack $widget)
    {
        $current_section = $widget->get_current_section();
        if (isset($current_section['condition']['submit_actions'])) {
            $action = $current_section['condition']['submit_actions'];
        } else {
            return;
        }
        $widget->add_control("dce_action_condition_{$action}_enabled", ['label' => '<span class="color-dce icon-dce-logo-dce"></span> ' . esc_html__('Conditionally run action', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SWITCHER]);
        $widget->add_control("dce_action_condition_{$action}_expression", ['condition' => ["dce_action_condition_{$action}_enabled" => 'yes'], 'label' => esc_html__('Expression', 'dynamic-content-for-elementor'), 'description' => \sprintf(
            /* translators: %1$s: opening tag of the link to the Conditions Generator, %2$s: closing tag of the link */
            esc_html__('The action will only be run if this expression is true. One condition per line. All conditions are and-connected. Conditions are expressions that can also use the or operator and much more! You can use our online tool %1$sConditions Generator%2$s to generate your conditions more easily.', 'dynamic-content-for-elementor'),
            '<a target="_blank" href="' . esc_url('https://dnmc.ooo/condgen') . '">',
            '</a>'
        ), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'ai' => ['active' => \false], 'label_block' => \true]);
    }
    /**
     * @param mixed $widget
     * @return void
     */
    public function update_fields_controls($widget)
    {
        $elementor = Plugin::elementor();
        $control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');
        if (is_wp_error($control_data)) {
            return;
        }
        $field_controls = ['form_fields_conditions_tab' => ['type' => 'tab', 'tab' => 'content', 'label' => esc_html__('Conditions', 'dynamic-content-for-elementor'), 'conditions' => ['terms' => [['name' => 'field_type', 'operator' => '!in', 'value' => ['hidden', 'step']]]], 'tabs_wrapper' => 'form_fields_tabs', 'name' => 'form_fields_conditions_tab'], 'dce_field_conditions_mode' => ['name' => 'dce_field_conditions_mode', 'label' => esc_html__('Condition', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['visible' => ['title' => esc_html__('Always Visible', 'dynamic-content-for-elementor'), 'icon' => 'eicon-check'], 'show' => ['title' => esc_html__('Show if', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-eye'], 'hide' => ['title' => esc_html__('Hide if', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-eye-slash']], 'toggle' => \false, 'default' => 'visible', 'tab' => 'content', 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_conditions_tab'], 'dce_conditions_expression' => [
            'name' => 'dce_conditions_expression',
            'type' => \Elementor\Controls_Manager::TEXTAREA,
            'label' => esc_html__('Conditions Expressions', 'dynamic-content-for-elementor'),
            'description' => \sprintf(
                /* translators: %1$s: start anchor tag with URL to the Conditions Generator, %2$s: end anchor tag */
                esc_html__('One condition per line. All conditions are and-connected. Conditions are expressions that can also use the or operator and much more! You can use our online tool %1$sConditions Generator%2$s to generate your conditions more easily.', 'dynamic-content-for-elementor'),
                '<a target="_blank" href="' . esc_url('https://dnmc.ooo/condgen') . '">',
                '</a>'
            ),
            'placeholder' => "name == 'Joe'",
            'condition' => ['dce_field_conditions_mode!' => 'visible'],
            'tab' => 'content',
            'tabs_wrapper' => 'form_fields_tabs',
            'inner_tab' => 'form_fields_conditions_tab',
            /**
             * Enable Dynamic Tags support for Conditions Expressions field.
             *
             * This filter allows developers to enable Dynamic Tags support for the
             * Conditions Expressions field. By default, it's disabled to avoid
             * confusion for end users who might not understand how to use Dynamic Tags.
             *
             * Developers can enable this feature by adding this filter to their theme or plugin:
             * add_filter('dynamicooo/conditional-fields/dynamic-tags', '__return_true');
             *
             * @since 3.3.11
             * @param bool $enable_dynamic_tags Whether to enable Dynamic Tags support. Default false.
             */
            'dynamic' => ['active' => apply_filters('dynamicooo/conditional-fields/dynamic-tags', \false)],
            'ai' => ['active' => \false],
        ], 'dce_conditions_disable_only' => ['name' => 'dce_conditions_disable_only', 'label' => esc_html__('Disable only', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'tab' => 'content', 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_conditions_tab', 'condition' => ['dce_field_conditions_mode!' => 'visible']]];
        $control_data['fields'] = \array_merge($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);
    }
    /**
     * Disable Form action by overriding registrar
     *
     * @param string[] $actions_to_disable
     * @return void
     */
    public function disable_actions(array $actions_to_disable)
    {
        /** @var Form_Module $module */
        $module = Form_Module::instance();
        $actions = $module->actions_registrar->get();
        foreach ($actions_to_disable as $a) {
            unset($actions[$a]);
        }
        $module->actions_registrar = new class($actions) extends Form_Actions_Registrar
        {
            /**
             * @var mixed
             */
            private $override_items;
            /**
             * @param mixed $items
             */
            public function __construct($items)
            {
                $this->override_items = $items;
            }
            /**
             * @param mixed $id
             * @return array<string, mixed>|mixed
             */
            public function get($id = null)
            {
                if (!$id) {
                    return $this->override_items;
                }
                return isset($this->override_items[$id]) ? $this->override_items[$id] : null;
            }
        };
    }
    /**
     * Check action validations and remove the action that should not run.
     *
     * @param \ElementorPro\Modules\Forms\Classes\Form_Record $record
     * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
     * @return void
     */
    public function actions_validation($record, $ajax_handler)
    {
        $disable = [];
        $values = $this->get_field_values($record);
        foreach ($this->conditional_actions as $ca) {
            if ($record->get_form_settings("dce_action_condition_{$ca}_enabled") === 'yes') {
                $expr = $record->get_form_settings("dce_action_condition_{$ca}_expression");
                $expr = ExpressionEvaluator::and_join_lines($expr);
                try {
                    $res = ExpressionEvaluator::get_language()->evaluate($expr, $values);
                } catch (\DynamicOOOS\Symfony\Component\ExpressionLanguage\SyntaxError $e) {
                    $ajax_handler->add_error_message(esc_html__('Conditional Action error: ', 'dynamic-content-for-elementor') . $e->getMessage());
                    $ajax_handler->send();
                    return;
                }
                if (!$res) {
                    $disable[] = $ca;
                }
            }
        }
        if (!empty($disable)) {
            $this->disable_actions($disable);
        }
    }
    /**
     * Determine all field visibilities based on the conditions.
     *
     * @param mixed $conditions
     * @param mixed $values
     * @param mixed $ajax_handler
     * @return array<string, bool>|false
     */
    public function determine_visibilities($conditions, $values, $ajax_handler)
    {
        $visibility = [];
        // Assume they are all visible at the beginning:
        foreach ($conditions as $id => $_) {
            $visibility[$id] = \true;
        }
        foreach ($conditions as $id => $condition) {
            try {
                $res = ExpressionEvaluator::get_language()->evaluate($condition['condition'], $values);
            } catch (\Throwable $e) {
                /* translators: %s: field ID */
                $msg = \sprintf(esc_html__('There is an error in your Conditional Fields code (field: %s)', 'dynamic-content-for-elementor'), esc_html($id));
                $ajax_handler->add_error_message($msg);
                $ajax_handler->add_admin_error_message(esc_html($e->getMessage()));
                return \false;
            }
            $res = $condition['mode'] === 'show' ? $res : !$res;
            if (!$res) {
                // we don't want an inactive field value to influence
                // further conditions:
                $values[$id] = '';
            }
            $visibility[$id] = $res;
        }
        return $visibility;
    }
    /**
     * Return an array with key field id and value its raw_value
     *
     * @param mixed $record
     * @return array<string, mixed>
     */
    private function get_field_values($record)
    {
        $raw_fields = $record->get_field([]);
        $values = [];
        foreach ($raw_fields as $field) {
            $values[$field['id']] = $field['raw_value'];
        }
        return $values;
    }
    /**
     * Returns true if there are errors on the form ajax
     * handler. Unfortunately this doesn't work if set_success is used
     * directly, however this does not occur anywhere neither in Elementor pro
     * nor DCE.
     *
     * @param mixed $ajax_handler
     * @return bool
     */
    private static function ajax_handler_has_errors($ajax_handler)
    {
        $has_error = \false;
        $has_error |= !empty($ajax_handler->errors);
        $has_error |= !empty($ajax_handler->messages['error']);
        $has_error |= !empty($ajax_handler->messages['admin_error']);
        return (bool) $has_error;
    }
    /**
     * @param Form_Record $record
     * @param Ajax_Handler $ajax_handler
     * @return void
     */
    public function max_submissions_validation($record, $ajax_handler)
    {
        if ($record->get_form_settings('dce_max_submissions_enabled') !== 'yes') {
            return;
        }
        $name = $record->get_form_settings('dce_max_submission_counter_name') ?? '';
        $res = $record->get_field(['id' => $name]);
        if (empty($res)) {
            $ajax_handler->add_admin_error_message(esc_html__('Cannot find the max submission counter field. Please just put the name of the field, not inside tags or shortcodes.', 'dynamic-content-for-elementor'))->send();
        }
        $counter = $res[$name];
        if ($counter['type'] !== 'dce_counter') {
            $ajax_handler->add_admin_error_message(esc_html__('Cannot find the max submission counter field. The field does not seems to be a counter.', 'dynamic-content-for-elementor'))->send();
        }
        $value = $counter['value'];
        if ($value >= $record->get_form_settings('dce_max_sumbissions_limit')) {
            $ajax_handler->add_error_message($record->get_form_settings('dce_max_submissions_error_message'))->send();
        }
    }
    /**
     * Remove validation errors related to fields that are required but
     * that have been hidden by a condition.
     *
     * @param mixed $record
     * @param mixed $ajax_handler
     * @return void
     */
    public function fix_validation($record, $ajax_handler)
    {
        $conditions = [];
        $values = $this->get_field_values($record);
        foreach ($record->get_form_settings('form_fields') as $field) {
            if (self::are_conditions_enabled($field)) {
                $conditions[$field['custom_id']] = ['condition' => ExpressionEvaluator::and_join_lines($field['dce_conditions_expression']), 'mode' => $field['dce_field_conditions_mode']];
            }
        }
        $visibilities = $this->determine_visibilities($conditions, $values, $ajax_handler);
        if ($visibilities === \false) {
            return;
        }
        foreach ($visibilities as $id => $visible) {
            if (!$visible) {
                $type = $record->get_field(['id' => $id])[$id]['type'];
                // counter value is always present because set by its validation function.
                if ($type !== 'dce_counter' && !empty($values[$id])) {
                    // this can happen because JS expressionlanguage work slightly differently than PHP:
                    $ajax_handler->add_admin_error_message(esc_html__('Conditional Fields Error: When you have a field where you can pick multiple items, like checkbox or the select field with multiple select active, you must use the operator in. If instead you have a field where you pick only one value (like Select) you most likely want to use == and not in. Check the docs if unsure.', 'dynamic-content-for-elementor'));
                } else {
                    // Remove potential validation error related to the field:
                    unset($ajax_handler->errors[$id]);
                }
            }
        }
        // if there are no errors then the form is actually good.
        if (!$this->ajax_handler_has_errors($ajax_handler)) {
            $ajax_handler->set_success(\true);
        }
    }
    /**
     * @param mixed $record
     * @param mixed $ajax_handler
     * @return void
     */
    public function conditional_validation($record, $ajax_handler)
    {
        $values = $this->get_field_values($record);
        $validations = $record->get_form_settings('dce_conditional_validations');
        foreach ($validations as $validation) {
            if ($validation['disabled'] !== 'yes') {
                try {
                    $res = ExpressionEvaluator::get_language()->evaluate(ExpressionEvaluator::and_join_lines($validation['expression']), $values);
                } catch (\DynamicOOOS\Symfony\Component\ExpressionLanguage\SyntaxError $e) {
                    $ajax_handler->add_error('*no-field*', 'error');
                    $ajax_handler->add_admin_error_message(esc_html__('Conditional validation error: ', 'dynamic-content-for-elementor') . $e->getMessage());
                    return;
                }
                if (!$res) {
                    if ($validation['error_field_id']) {
                        $ajax_handler->add_error($validation['error_field_id'], $validation['error_message']);
                    } else {
                        $ajax_handler->add_error('*no-field*', 'error');
                        $ajax_handler->add_error_message($validation['error_message']);
                    }
                }
            }
        }
    }
    /**
     * @param mixed $record
     * @param mixed $ajax_handler
     * @return void
     */
    public function validation($record, $ajax_handler)
    {
        $this->conditional_validation($record, $ajax_handler);
        $this->max_submissions_validation($record, $ajax_handler);
        $this->fix_validation($record, $ajax_handler);
    }
    /**
     * @param array<mixed> $widgets
     * @return array<mixed>
     */
    public function wpml_widgets_to_translate_filter($widgets)
    {
        if (!isset($widgets['form'])) {
            return $widgets;
        }
        $widgets['form']['fields']['dce_max_submissions_error_message'] = ['field' => 'dce_max_submissions_error_message', 'type' => esc_html__('Max Submissions - Error Message', 'dynamic-content-for-elementor'), 'editor_type' => 'LINE'];
        $widgets['form']['fields_in_item']['dce_conditional_validations'][] = ['field' => 'error_message', 'type' => esc_html__('Conditional Validation - Error Message', 'dynamic-content-for-elementor'), 'editor_type' => 'LINE'];
        return $widgets;
    }
}
