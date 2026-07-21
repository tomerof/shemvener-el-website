<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Extensions;

use Elementor\Group_Control_Border;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Stripe;
use ElementorPro\Modules\Forms\Fields;
use ElementorPro\Modules\Forms\Classes;
use ElementorPro\Modules\Forms\Widgets\Form;
use ElementorPro\Plugin;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class StripeField extends \ElementorPro\Modules\Forms\Fields\Field_Base
{
    public $has_action = \false;
    public $depended_scripts = ['dce-stripe'];
    private static $validated_intents = [];
    public function __construct()
    {
        add_filter('elementor/widget/print_template', function ($template, $widget) {
            if ('form' === $widget->get_name()) {
                $template = \false;
            }
            return $template;
        }, 10, 2);
        add_filter('wpml_elementor_widgets_to_translate', [$this, 'wpml_widgets_to_translate_filter'], 50, 1);
        parent::__construct();
    }
    /**
     * @return void
     */
    public function run_once()
    {
        $save_guard = \DynamicContentForElementor\Plugin::instance()->save_guard;
        $save_guard->register_unsafe_control('form', 'form_fields::dce_stripe_attach_customer');
        $save_guard->register_unsafe_control('form', 'form_fields::dce_stripe_is_subscription');
        $save_guard->register_unsafe_control('form', 'form_fields::dce_form_stripe_price_id_from_field');
        $save_guard->register_unsafe_control('form', 'form_fields::dce_form_stripe_subscription_schedule');
        $save_guard->register_unsafe_control('form', 'form_fields::dce_form_stripe_subscription_single_phase_iterations');
        $save_guard->register_unsafe_control('form', 'form_fields::dce_form_stripe_price_id');
        $save_guard->register_unsafe_control('form', 'form_fields::dce_form_stripe_price_id_field_id');
        $save_guard->register_unsafe_control('form', 'form_fields::dce_form_stripe_currency');
        $save_guard->register_unsafe_control('form', 'form_fields::dce_form_stripe_value_from_field');
        $save_guard->register_unsafe_control('form', 'form_fields::dce_form_stripe_item_value');
        $save_guard->register_unsafe_control('form', 'form_fields::dce_form_stripe_dont_capture');
        $save_guard->register_unsafe_control('form', 'form_fields::dce_form_stripe_value_field_id');
        $save_guard->register_unsafe_control('form', 'form_fields::dce_stripe_future_usage');
    }
    /**
     * @return array<string>
     */
    public function get_script_depends() : array
    {
        return $this->depended_scripts;
    }
    public function get_name()
    {
        return 'Stripe';
    }
    public function get_label()
    {
        return esc_html__('Stripe', 'dynamic-content-for-elementor');
    }
    public function get_type()
    {
        return 'dce_form_stripe';
    }
    /**
     * @return array<string>
     */
    public function get_style_depends() : array
    {
        return $this->depended_styles;
    }
    // return the conditions related to the customer information controls, which should only appear in certain circumstances.
    private function get_customer_info_condition()
    {
        return ['relation' => 'and', 'terms' => [['name' => 'field_type', 'operator' => '==', 'value' => $this->get_type()], ['relation' => 'or', 'terms' => [['name' => 'dce_stripe_attach_customer', 'operator' => '==', 'value' => 'yes'], ['name' => 'dce_stripe_is_subscription', 'operator' => '==', 'value' => 'yes']]]]];
    }
    public function update_controls($widget)
    {
        $elementor = Plugin::elementor();
        $control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');
        if (is_wp_error($control_data)) {
            return;
        }
        if (!\DynamicContentForElementor\Helper::can_register_unsafe_controls()) {
            $field_controls = ['admin_notice' => ['name' => 'admin_notice', 'type' => Controls_Manager::RAW_HTML, 'raw' => esc_html__('You will need administrator capabilities to edit this form field.', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type()]]];
        } else {
            $customer_info_conditions = $this->get_customer_info_condition();
            $field_controls = ['dce_stripe_is_subscription' => ['name' => 'dce_stripe_is_subscription', 'label' => esc_html__('This is a Subscription', 'dynamic-content-for-elementor'), 'description' => esc_html__('Please notice that Subscriptions are not validated.', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'tab' => 'content', 'default' => 'no', 'separator' => 'before', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type()]], 'dce_form_stripe_price_id_from_field' => ['name' => 'dce_form_stripe_price_id_from_field', 'label' => esc_html__('Get Subscription Price ID dynamically from another field in the form', 'dynamic-content-for-elementor'), 'label_block' => \true, 'default' => 'no', 'type' => \Elementor\Controls_Manager::SWITCHER, 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type(), 'dce_stripe_is_subscription' => 'yes']], 'dce_form_stripe_subscription_schedule' => ['name' => 'dce_form_stripe_subscription_schedule', 'label' => esc_html__('Subscription Schedule', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => \Elementor\Controls_Manager::SELECT, 'default' => 'none', 'options' => ['none' => esc_html__('None', 'dynamic-content-for-elementor'), 'single-phase' => esc_html__('Simple - One phase (Experiment!)', 'dynamic-content-for-elementor')], 'description' => esc_html__('In case of None the subscription does not have a scheduled end date. It will continue until manually cancelled.', 'dynamic-content-for-elementor'), 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type(), 'dce_stripe_is_subscription' => 'yes']], 'dce_form_stripe_subscription_single_phase_iterations' => ['name' => 'dce_form_stripe_subscription_single_phase_iterations', 'label' => esc_html__('Iterations for Schedule', 'dynamic-content-for-elementor'), 'description' => esc_html__('After the iterations the subscription is cancelled', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => \Elementor\Controls_Manager::NUMBER, 'default' => 12, 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type(), 'dce_stripe_is_subscription' => 'yes', 'dce_form_stripe_subscription_schedule' => 'single-phase']], 'dce_form_stripe_price_id' => ['name' => 'dce_form_stripe_price_id', 'label' => esc_html__('Subscription Price ID', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'dynamic' => ['active' => \true], 'condition' => ['field_type' => $this->get_type(), 'dce_form_stripe_price_id_from_field!' => 'yes', 'dce_stripe_is_subscription' => 'yes']], 'dce_form_stripe_price_id_field_id' => ['name' => 'dce_form_stripe_price_id_field_id', 'label' => esc_html__('Subscription Price ID Field ID', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type(), 'dce_form_stripe_price_id_from_field' => 'yes', 'dce_stripe_is_subscription' => 'yes']], 'dce_form_stripe_currency' => ['name' => 'dce_form_stripe_currency', 'label' => esc_html__('Transaction Currency', 'dynamic-content-for-elementor'), 'description' => esc_html__('The currency three-letter ISO Code (for example USD or EUR).', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'USD', 'label_block' => 'true', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type(), 'dce_stripe_is_subscription!' => 'yes']], 'dce_form_stripe_value_from_field' => ['name' => 'dce_form_stripe_value_from_field', 'label' => esc_html__('Get Amount dynamically from another field in the form', 'dynamic-content-for-elementor'), 'label_block' => \true, 'default' => 'no', 'type' => \Elementor\Controls_Manager::SWITCHER, 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type(), 'dce_stripe_is_subscription!' => 'yes']], 'dce_form_stripe_item_value' => ['name' => 'dce_form_stripe_item_value', 'label' => esc_html__('Transaction Amount', 'dynamic-content-for-elementor'), 'description' => esc_html__('Amount intended to be collected by this transaction in the currency unit', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '10.99', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'dynamic' => ['active' => \true], 'condition' => ['field_type' => $this->get_type(), 'dce_form_stripe_value_from_field!' => 'yes', 'dce_stripe_is_subscription!' => 'yes']], 'dce_form_stripe_dont_capture' => ['name' => 'dce_form_stripe_dont_capture', 'label' => esc_html__('Do not Capture', 'dynamic-content-for-elementor'), 'description' => esc_html__('Switching this on will only authorized the payment, but no money transfer will happen, the receiver will need to "capture" the payment manually in the Stripe dashboard. Warning: No validation happens in this case.', 'dynamic-content-for-elementor'), 'default' => 'no', 'type' => \Elementor\Controls_Manager::SWITCHER, 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type(), 'dce_stripe_is_subscription!' => 'yes']], 'dce_form_stripe_value_field_id' => ['name' => 'dce_form_stripe_value_field_id', 'label' => esc_html__('Amount Field ID', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type(), 'dce_form_stripe_value_from_field' => 'yes', 'dce_stripe_is_subscription!' => 'yes']], 'dce_stripe_attach_customer' => ['name' => 'dce_stripe_attach_customer', 'label' => esc_html__('Attach Customer Information to the Payment', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => \Elementor\Controls_Manager::SWITCHER, 'tab' => 'content', 'separator' => 'before', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type(), 'dce_stripe_is_subscription!' => 'yes']], 'admin_notice' => ['name' => 'admin_notice', 'type' => Controls_Manager::RAW_HTML, 'raw' => '<div class="elementor-control-field-description">' . esc_html__('The customer information taken from other fields will be attached to the payment and available in the Stripe Panel. Please insert the field IDs associated with each information, leave blank if not available. Notice that the Customer will be duplicated if they make multiple payments.', 'dynamic-content-for-elementor') . '</div>', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'conditions' => $customer_info_conditions], 'dce_stripe_future_usage' => ['name' => 'dce_stripe_future_usage', 'label' => esc_html__('Save the Customer payment method for future usage', 'dynamic-content-for-elementor'), 'description' => esc_html__('Associate the paying method with the Customer. You can then create recurring payments and subscriptions in the Stripe Panel. Customer email is required.', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => \Elementor\Controls_Manager::SWITCHER, 'tab' => 'content', 'separator' => 'before', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type(), 'dce_stripe_attach_customer' => 'yes', 'dce_stripe_is_subscription!' => 'yes']], 'dce_stripe_customer_email_field_id' => ['name' => 'dce_stripe_customer_email_field_id', 'label' => esc_html__('Customer Email Field', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => \Elementor\Controls_Manager::TEXT, 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'conditions' => $customer_info_conditions], 'dce_stripe_customer_name_field_id' => ['name' => 'dce_stripe_customer_name_field_id', 'label' => esc_html__('Customer Full Name Field', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => \Elementor\Controls_Manager::TEXT, 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'conditions' => $customer_info_conditions], 'dce_stripe_customer_phone_field_id' => ['name' => 'dce_stripe_customer_phone_field_id', 'separator' => 'after', 'label' => esc_html__('Customer Phone Number Field', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => \Elementor\Controls_Manager::TEXT, 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'conditions' => $customer_info_conditions], 'dce_form_stripe_item_description' => ['name' => 'dce_form_stripe_item_description', 'label' => esc_html__('Item Description', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'placeholder' => esc_html__('Item Description', 'dynamic-content-for-elementor'), 'description' => \DynamicContentForElementor\Plugin::instance()->text_templates->get_default_value(['dynamic-shortcodes' => esc_html__('You can also use Dynamic Shortcodes like {form:field-id} to refer to other fields.', 'dynamic-content-for-elementor'), 'tokens' => esc_html__('You can also use tokens like [form:fieldid] to refer to other fields.', 'dynamic-content-for-elementor')]), 'label_block' => 'true', 'default' => '', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'dynamic' => ['active' => 1], 'condition' => ['dce_stripe_is_subscription!' => 'yes', 'field_type' => $this->get_type()]], 'dce_form_stripe_item_sku' => ['name' => 'dce_form_stripe_item_sku', 'label' => esc_html__('Item SKU', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'placeholder' => esc_html__('Item SKU', 'dynamic-content-for-elementor'), 'label_block' => 'true', 'default' => '', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'dynamic' => ['active' => 1], 'condition' => ['dce_stripe_is_subscription!' => 'yes', 'field_type' => $this->get_type()]]];
        }
        $control_data['fields'] = $this->inject_field_controls($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);
    }
    public function render($item, $item_index, $form)
    {
        $stripe = \DynamicContentForElementor\Plugin::instance()->stripe;
        $stripe_key = $stripe->get_publishable_key();
        if (empty($stripe_key)) {
            Helper::Notice(\false, esc_html__('Stripe Error: Missing Publishable Key.', 'dynamic-content-for-elementor'));
            return;
        }
        $form->add_render_attribute('input' . $item_index, 'type', 'hidden', \true);
        $form->add_render_attribute('dce_stripe' . $item_index, 'data-required', $item['required'] ? 'true' : 'false', \true);
        $form->add_render_attribute('dce_stripe' . $item_index, 'data-publishable-key', $stripe_key, \true);
        $form->add_render_attribute('dce_stripe' . $item_index, 'data-field-index', $item_index, \true);
        $intent_url = admin_url('admin-ajax.php?action=dce_stripe_get_payment_intent');
        $form->add_render_attribute('dce_stripe' . $item_index, 'data-intent-url', $intent_url, \true);
        $form->add_render_attribute('dce_stripe' . $item_index, 'data-stripe-nonce', wp_create_nonce('dce_stripe_payment_intent'), \true);
        $form->add_render_attribute('dce_stripe' . $item_index, 'style', 'padding-top: 10px;', \true);
        $form->add_render_attribute('dce_stripe' . $item_index, 'class', 'elementor-field elementor-field-textual dce-stripe-elements', \true);
        echo '<input ' . $form->get_render_attribute_string('input' . $item_index) . '>';
        echo '<span class="stripe-error elementor-message elementor-message-danger elementor-help-inline elementor-form-help-inline" role="alert" style="display: none;"></span>';
        echo '<div ' . $form->get_render_attribute_string('dce_stripe' . $item_index) . '></div>';
    }
    private static function get_field_settings($id, Classes\Form_Record $record)
    {
        $field_settings = $record->get_form_settings('form_fields');
        $field_settings = \array_filter($field_settings, function ($field) use($id) {
            return $field['custom_id'] === $id;
        });
        return \array_values($field_settings)[0];
    }
    private function is_subscription($settings)
    {
        if (($settings['dce_stripe_is_subscription'] ?? '') === 'yes') {
            return \true;
        }
        return \false;
    }
    /**
     * @param string $id
     * @return void
     */
    private function set_subscription_default_payment_method($subscription)
    {
        $invoice = $subscription->latest_invoice;
        if ($invoice === null) {
            return;
        }
        /**
         * @var \Stripe\Invoice $invoice
         */
        $intent = $invoice->payment_intent;
        if ($intent === null) {
            return;
        }
        /**
         * @var \Stripe\PaymentIntent $intent
         */
        $pm_id = $intent->payment_method;
        \DynamicOOOS\Stripe\Subscription::update($subscription->id, ['default_payment_method' => $pm_id]);
    }
    private function set_subscription_schedule($subscription, $settings)
    {
        $iterations = \intval($settings['dce_form_stripe_subscription_single_phase_iterations']);
        $sched = \DynamicOOOS\Stripe\SubscriptionSchedule::create(['from_subscription' => $subscription->id]);
        \DynamicOOOS\Stripe\SubscriptionSchedule::update($sched->id, ['phases' => [['items' => [['price' => $subscription->items->data[0]->price->id]], 'start_date' => $subscription->created, 'iterations' => $iterations]], 'end_behavior' => 'cancel']);
    }
    private function subscription_matches(\DynamicOOOS\Stripe\Subscription $subscription, string $expected_dce_id, string $expected_price_id, string $expected_blog_id, string $expected_user_id, string $expected_post_id, string $expected_queried_id) : bool
    {
        $dce_id = (string) ($subscription->metadata['dce_id'] ?? '');
        $dce_blog_id = (string) ($subscription->metadata['dce_blog_id'] ?? '');
        $dce_user_id = (string) ($subscription->metadata['dce_user_id'] ?? '');
        $dce_post_id = (string) ($subscription->metadata['dce_post_id'] ?? '');
        $dce_queried_id = (string) ($subscription->metadata['dce_queried_id'] ?? '');
        $dce_replay_guard = (string) ($subscription->metadata['dce_replay_guard'] ?? '');
        $dce_consumed_at = (string) ($subscription->metadata['dce_consumed_at'] ?? '');
        if (!\hash_equals($expected_dce_id, $dce_id) || !\hash_equals($expected_blog_id, $dce_blog_id) || !\hash_equals($expected_user_id, $dce_user_id) || !\hash_equals($expected_post_id, $dce_post_id) || !\hash_equals($expected_queried_id, $dce_queried_id) || !$dce_replay_guard || $dce_consumed_at || 'active' !== $subscription->status) {
            return \false;
        }
        $subscription_item = $subscription->items->data[0] ?? null;
        $price_id = (string) ($subscription_item->price->id ?? '');
        if (!$expected_price_id || !\hash_equals($expected_price_id, $price_id)) {
            return \false;
        }
        $invoice = $subscription->latest_invoice;
        if (!$invoice instanceof \DynamicOOOS\Stripe\Invoice || 'paid' !== $invoice->status || \true !== $invoice->paid) {
            return \false;
        }
        $intent = $invoice->payment_intent;
        return $intent instanceof \DynamicOOOS\Stripe\PaymentIntent && 'succeeded' === $intent->status;
    }
    private function retrieve_subscription(string $id) : \DynamicOOOS\Stripe\Subscription
    {
        return \DynamicOOOS\Stripe\Subscription::retrieve(['id' => $id, 'expand' => ['latest_invoice.payment_intent']]);
    }
    private function release_subscription_lock(string $lock_key, string $lock_owner) : void
    {
        $lock_value = get_option($lock_key);
        if (\is_array($lock_value) && \hash_equals($lock_owner, (string) ($lock_value['owner'] ?? ''))) {
            delete_option($lock_key);
        }
    }
    private function handle_subscription($id, $settings, string $expected_dce_id, string $expected_price_id, string $expected_blog_id, string $expected_user_id, string $expected_post_id, string $expected_queried_id) : bool
    {
        if (!\is_string($id) || !\preg_match('/^sub_[a-zA-Z0-9]+$/', $id)) {
            return \false;
        }
        $lock_key = 'dce_stripe_sub_lock_' . \hash('sha256', $id);
        $lock_value = [];
        $lock_owner = wp_generate_uuid4();
        $lock_acquired = \false;
        try {
            $subscription = $this->retrieve_subscription($id);
            if (!$this->subscription_matches($subscription, $expected_dce_id, $expected_price_id, $expected_blog_id, $expected_user_id, $expected_post_id, $expected_queried_id)) {
                return \false;
            }
            $dce_replay_guard = (string) $subscription->metadata['dce_replay_guard'];
            $consumed_context = \hash('sha256', \implode("\x00", [$expected_blog_id, $expected_user_id, $expected_post_id, $expected_queried_id, $expected_dce_id, $expected_price_id, $dce_replay_guard]));
            $lock_value = ['context' => $consumed_context, 'created_at' => \time(), 'owner' => $lock_owner];
            if (!add_option($lock_key, $lock_value, '', \false)) {
                return \false;
            }
            $lock_acquired = \true;
            \register_shutdown_function(function () use($lock_key, $lock_owner) {
                $this->release_subscription_lock($lock_key, $lock_owner);
            });
            $subscription = $this->retrieve_subscription($id);
            if (!$this->subscription_matches($subscription, $expected_dce_id, $expected_price_id, $expected_blog_id, $expected_user_id, $expected_post_id, $expected_queried_id)) {
                return \false;
            }
            \DynamicOOOS\Stripe\Subscription::update($id, ['metadata' => ['dce_consumed_at' => (string) \time(), 'dce_consumed_context' => $consumed_context]]);
            $this->set_subscription_default_payment_method($subscription);
            if ($settings['dce_form_stripe_subscription_schedule'] === 'single-phase') {
                $this->set_subscription_schedule($subscription, $settings);
            }
        } catch (\DynamicOOOS\Stripe\Exception\ApiErrorException $e) {
            return \false;
        } finally {
            if ($lock_acquired) {
                $this->release_subscription_lock($lock_key, $lock_owner);
            }
        }
        return \true;
    }
    public function process_field($field, Classes\Form_Record $record, Classes\Ajax_Handler $ajax_handler)
    {
        $error_msg = esc_html__('There was an error while completing the payment, please try again later or contact the merchant directly.', 'dynamic-content-for-elementor');
        $id = $field['id'];
        $intent_id = $field['value'];
        $settings = self::get_field_settings($id, $record);
        if ($this->is_subscription($settings)) {
            $post_id = isset($_POST['post_id']) && \is_scalar($_POST['post_id']) ? absint(wp_unslash($_POST['post_id'])) : 0;
            $queried_id = isset($_POST['queried_id']) && \is_scalar($_POST['queried_id']) ? absint(wp_unslash($_POST['queried_id'])) : $post_id;
            if (!$queried_id) {
                $queried_id = $post_id;
            }
            if (!$post_id || !Helper::can_current_user_view_post($post_id) || !Helper::can_current_user_view_post($queried_id)) {
                $ajax_handler->add_error($id, $error_msg);
                return;
            }
            $dce_id_expected = $record->get_form_settings('id') . '-' . $id;
            $price_id_expected = (string) ($settings['dce_form_stripe_price_id'] ?? '');
            if (($settings['dce_form_stripe_price_id_from_field'] ?? '') === 'yes') {
                $price_field_id = $settings['dce_form_stripe_price_id_field_id'] ?? '';
                $price_field = $record->get_field(['id' => $price_field_id]);
                $price_field_value = $price_field[$price_field_id]['value'] ?? '';
                $price_id_expected = \is_scalar($price_field_value) ? (string) $price_field_value : '';
            }
            if (!$this->handle_subscription($intent_id, $settings, $dce_id_expected, $price_id_expected, (string) get_current_blog_id(), (string) get_current_user_id(), (string) $post_id, (string) $queried_id)) {
                $ajax_handler->add_error($id, $error_msg);
            }
            return;
        }
        if (empty($intent_id)) {
            // Value is not allowed to be empty when field is required. So
            // if empty then the field is not required and no validation is
            // needed.
            return;
        }
        if ($settings['dce_form_stripe_dont_capture'] === 'yes') {
            return;
        }
        $post_id = isset($_POST['post_id']) && \is_scalar($_POST['post_id']) ? absint(wp_unslash($_POST['post_id'])) : 0;
        $queried_id = isset($_POST['queried_id']) && \is_scalar($_POST['queried_id']) ? absint(wp_unslash($_POST['queried_id'])) : $post_id;
        if (!$queried_id) {
            $queried_id = $post_id;
        }
        if (!$post_id || !Helper::can_current_user_view_post($post_id) || !Helper::can_current_user_view_post($queried_id)) {
            $ajax_handler->add_error($id, $error_msg);
            return;
        }
        $dce_id_expected = $record->get_form_settings('id') . '-' . $id;
        $validation_key = \implode(':', [$intent_id, (string) get_current_blog_id(), (string) get_current_user_id(), (string) $post_id, (string) $queried_id, $dce_id_expected]);
        if (isset(self::$validated_intents[$validation_key])) {
            return;
            // good, already validated.
        }
        try {
            $intent = \DynamicOOOS\Stripe\PaymentIntent::retrieve($intent_id);
            // This filter gives the ability to check that the amount is the
            // one expected or abort.
            $intent = apply_filters('dynamicooo/stripe-field/payment-intent', $intent, $record, $ajax_handler);
            if (!$intent) {
                $ajax_handler->add_error($id, $error_msg);
                return;
            }
            // we make sure the payment intent was created by this stripe
            // field and not elsewhere:
            $dce_id = (string) ($intent->metadata['dce_id'] ?? '');
            $dce_blog_id = (string) ($intent->metadata['dce_blog_id'] ?? '');
            $dce_user_id = (string) ($intent->metadata['dce_user_id'] ?? '');
            $dce_post_id = (string) ($intent->metadata['dce_post_id'] ?? '');
            $dce_queried_id = (string) ($intent->metadata['dce_queried_id'] ?? '');
            if (!\hash_equals($dce_id_expected, $dce_id) || !\hash_equals((string) get_current_blog_id(), $dce_blog_id) || !\hash_equals((string) get_current_user_id(), $dce_user_id) || !\hash_equals((string) $post_id, $dce_post_id) || !\hash_equals((string) $queried_id, $dce_queried_id)) {
                $ajax_handler->add_error($id, $error_msg);
                return;
            }
            $intent->capture();
            if ('succeeded' !== $intent->status) {
                $ajax_handler->add_error($id, $error_msg);
            } else {
                self::$validated_intents[$validation_key] = \true;
            }
        } catch (\DynamicOOOS\Stripe\Exception\InvalidRequestException $e) {
            $ajax_handler->add_error($id, $error_msg);
        }
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
        $widgets['form']['fields_in_item']['form_fields'][] = ['field' => 'dce_form_stripe_item_description', 'type' => esc_html__('Stripe Item Description', 'dynamic-content-for-elementor'), 'editor_type' => 'AREA'];
        $widgets['form']['fields_in_item']['form_fields'][] = ['field' => 'dce_form_stripe_item_sku', 'type' => esc_html__('Stripe Item SKU', 'dynamic-content-for-elementor'), 'editor_type' => 'LINE'];
        return $widgets;
    }
}
