<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor;

use ElementorPro\Modules\Forms\Module as Forms_Module;
if (!\defined('ABSPATH')) {
    exit;
}
class Stripe
{
    private const STRIPE_MAX_CONCURRENT_REQUESTS = 10;
    private const STRIPE_RATE_LIMIT = 60;
    private const STRIPE_RATE_WINDOW = 60;
    private const STRIPE_RATE_LOCK_TTL = 10;
    private const STRIPE_REQUEST_LEASE_TTL = 300;
    private const STRIPE_REQUEST_SLOT_PREFIX = 'dce_stripe_request_slot_';
    private const STRIPE_RATE_STATE_OPTION = 'dce_stripe_rate_state';
    private const STRIPE_RATE_LOCK_OPTION = 'dce_stripe_rate_lock';
    private string $stripe_request_slot_option = '';
    private string $stripe_request_slot_lease = '';
    private string $stripe_idempotency_context = '';
    public function get_publishable_key()
    {
        if (get_option('dce_stripe_api_mode') === 'live') {
            return get_option('dce_stripe_api_publishable_key_live');
        } else {
            return get_option('dce_stripe_api_publishable_key_test');
        }
    }
    public function set_key()
    {
        if (get_option('dce_stripe_api_mode') === 'live') {
            \DynamicOOOS\Stripe\Stripe::setApiKey(get_option('dce_stripe_api_secret_key_live'));
        } else {
            \DynamicOOOS\Stripe\Stripe::setApiKey(get_option('dce_stripe_api_secret_key_test'));
        }
    }
    public function __construct()
    {
        $this->set_key();
        add_action('wp_ajax_dce_stripe_get_payment_intent', [$this, 'get_payment_intent_ajax']);
        add_action('wp_ajax_nopriv_dce_stripe_get_payment_intent', [$this, 'get_payment_intent_ajax']);
    }
    private function clear_option_cache(string $option_name) : void
    {
        wp_cache_delete($option_name, 'options');
        wp_cache_delete('notoptions', 'options');
    }
    private function option_lease_is_expired(string $lease) : bool
    {
        $separator = \strrpos($lease, '|');
        if (\false === $separator) {
            return \false;
        }
        $expires_at = (int) \substr($lease, $separator + 1);
        return $expires_at > 0 && $expires_at <= \time();
    }
    private function replace_option_lease(string $option_name, string $current_lease, string $new_lease) : bool
    {
        global $wpdb;
        $updated = $wpdb->query($wpdb->prepare("UPDATE {$wpdb->options} SET option_value = %s WHERE option_name = %s AND option_value = %s", $new_lease, $option_name, $current_lease));
        if (1 !== $updated) {
            return \false;
        }
        $this->clear_option_cache($option_name);
        return \true;
    }
    private function acquire_option_lease(string $option_name, int $ttl) : ?string
    {
        $lease = wp_generate_uuid4() . '|' . (\time() + $ttl);
        if (add_option($option_name, $lease, '', \false)) {
            return $lease;
        }
        $current_lease = get_option($option_name);
        if (!\is_string($current_lease) || !$this->option_lease_is_expired($current_lease)) {
            return null;
        }
        return $this->replace_option_lease($option_name, $current_lease, $lease) ? $lease : null;
    }
    private function owns_option_lease(string $option_name, string $lease) : bool
    {
        global $wpdb;
        $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name = %s AND option_value = %s", $option_name, $lease));
        return 1 === (int) $count;
    }
    private function release_option_lease(string $option_name, string $lease) : void
    {
        global $wpdb;
        $deleted = $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name = %s AND option_value = %s", $option_name, $lease));
        if (1 === $deleted) {
            $this->clear_option_cache($option_name);
        }
    }
    private function acquire_stripe_request_slot() : bool
    {
        $max_concurrent = \min(50, \max(1, (int) apply_filters('dynamicooo/stripe/max-concurrent-requests', self::STRIPE_MAX_CONCURRENT_REQUESTS)));
        $execution_limit = (int) \ini_get('max_execution_time');
        $default_ttl = $execution_limit > 0 ? \max(self::STRIPE_REQUEST_LEASE_TTL, $execution_limit + 60) : self::STRIPE_REQUEST_LEASE_TTL;
        $ttl = \max(60, (int) apply_filters('dynamicooo/stripe/request-lease-ttl', $default_ttl));
        for ($slot = 0; $slot < $max_concurrent; ++$slot) {
            $option_name = self::STRIPE_REQUEST_SLOT_PREFIX . $slot;
            $lease = $this->acquire_option_lease($option_name, $ttl);
            if (null === $lease) {
                continue;
            }
            $this->stripe_request_slot_option = $option_name;
            $this->stripe_request_slot_lease = $lease;
            \register_shutdown_function(function () {
                $this->release_stripe_request_slot();
            });
            return \true;
        }
        return \false;
    }
    private function release_stripe_request_slot() : void
    {
        if ('' === $this->stripe_request_slot_option || '' === $this->stripe_request_slot_lease) {
            return;
        }
        $this->release_option_lease($this->stripe_request_slot_option, $this->stripe_request_slot_lease);
        $this->stripe_request_slot_option = '';
        $this->stripe_request_slot_lease = '';
    }
    private function consume_stripe_rate_budget() : bool
    {
        $rate_lock = null;
        for ($attempt = 0; $attempt < 5 && null === $rate_lock; ++$attempt) {
            $rate_lock = $this->acquire_option_lease(self::STRIPE_RATE_LOCK_OPTION, self::STRIPE_RATE_LOCK_TTL);
            if (null === $rate_lock && $attempt < 4) {
                \usleep(20000);
            }
        }
        if (null === $rate_lock) {
            return \false;
        }
        try {
            $limit = \min(5000, \max(1, (int) apply_filters('dynamicooo/stripe/rate-limit', self::STRIPE_RATE_LIMIT)));
            $window = \max(1, (int) apply_filters('dynamicooo/stripe/rate-window', self::STRIPE_RATE_WINDOW));
            $now = \microtime(\true);
            $cutoff = $now - $window;
            $state = get_option(self::STRIPE_RATE_STATE_OPTION, []);
            $timestamps = [];
            if (\is_array($state) && isset($state['timestamps']) && \is_array($state['timestamps'])) {
                foreach ($state['timestamps'] as $timestamp) {
                    if (\is_numeric($timestamp) && (float) $timestamp > $cutoff) {
                        $timestamps[] = (float) $timestamp;
                    }
                }
            }
            if (\count($timestamps) >= $limit || !$this->owns_option_lease(self::STRIPE_RATE_LOCK_OPTION, $rate_lock)) {
                return \false;
            }
            $timestamps[] = $now;
            return update_option(self::STRIPE_RATE_STATE_OPTION, ['timestamps' => $timestamps], \false);
        } finally {
            $this->release_option_lease(self::STRIPE_RATE_LOCK_OPTION, $rate_lock);
        }
    }
    private function send_stripe_busy_response() : void
    {
        \header('Retry-After: 10');
        wp_send_json_error(['message' => __('Payment service is temporarily busy. Please try again.', 'dynamic-content-for-elementor')], 429);
    }
    private function initialize_stripe_idempotency_context(string $form_post_id, string $post_id, string $queried_id, string $form_id, string $field_id) : void
    {
        $this->stripe_idempotency_context = '';
        $request_id = isset($_POST['request_id']) && \is_scalar($_POST['request_id']) ? sanitize_text_field(wp_unslash((string) $_POST['request_id'])) : '';
        if (!\preg_match('/^[a-zA-Z0-9_-]{16,128}$/D', $request_id)) {
            return;
        }
        $this->stripe_idempotency_context = \hash('sha256', \implode("\x00", [(string) get_current_blog_id(), (string) get_current_user_id(), (string) get_option('dce_stripe_api_mode'), $form_post_id, $post_id, $queried_id, $form_id, $field_id, $request_id]));
    }
    private function get_stripe_idempotency_key(string $operation) : ?string
    {
        if ('' === $this->stripe_idempotency_context) {
            return null;
        }
        return 'dce_' . \hash('sha256', $this->stripe_idempotency_context . "\x00" . $operation);
    }
    private function get_stripe_replay_guard() : string
    {
        if ('' === $this->stripe_idempotency_context) {
            return wp_generate_uuid4();
        }
        return \hash('sha256', $this->stripe_idempotency_context . "\x00subscription-replay");
    }
    /**
     * Get form element form post_id, queried_id and form_id.
     * Code taken from Elementor Pro Ajax Handler.
     */
    public function get_form_element()
    {
        // $post_id that holds the form settings.
        $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
        if (!$post_id || !\DynamicContentForElementor\Helper::can_current_user_view_post($post_id)) {
            return \false;
        }
        // $queried_id the post for dynamic values data.
        if (isset($_POST['queried_id'])) {
            $queried_id = absint($_POST['queried_id']);
        } else {
            $queried_id = $post_id;
        }
        if (!$queried_id) {
            $queried_id = $post_id;
        }
        if (!\DynamicContentForElementor\Helper::can_current_user_view_post($queried_id)) {
            return \false;
        }
        $elementor = \Elementor\Plugin::$instance;
        // Make the post as global post for dynamic values.
        $elementor->db->switch_to_post($queried_id);
        $form_id = isset($_POST['form_id']) ? sanitize_text_field($_POST['form_id']) : '';
        $document = $elementor->documents->get($post_id);
        $form = null;
        $template_id = null;
        if ($document) {
            $form = Forms_Module::find_element_recursive($document->get_elements_data(), $form_id);
        }
        if (!empty($form['templateID'])) {
            $template = $elementor->documents->get($form['templateID']);
            if (!$template) {
                return \false;
            }
            $template_id = $template->get_id();
            $form = $template->get_elements_data()[0];
        }
        $widget = $elementor->elements_manager->create_element_instance($form);
        $form['settings'] = $widget->get_settings_for_display();
        $form['settings']['id'] = $form_id;
        $form['settings']['form_post_id'] = $template_id ? $template_id : $post_id;
        $form['settings']['dce_post_id'] = $post_id;
        $form['settings']['dce_queried_id'] = $queried_id;
        // TODO: Should be removed if there is an ability to edit "global widgets"
        $form['settings']['edit_post_id'] = $post_id;
        return $form;
    }
    public function get_payment_intent_ajax()
    {
        if (!check_ajax_referer('dce_stripe_payment_intent', 'nonce', \false)) {
            wp_send_json_error(['message' => 'Nonce verification failed'], 403);
        }
        $form = $this->get_form_element();
        if (empty($form)) {
            wp_send_json_error(['message' => 'Invalid Form']);
        }
        $field_index = isset($_POST['field_index']) ? sanitize_text_field($_POST['field_index']) : '';
        $field_settings = $form['settings']['form_fields'][$field_index] ?? \false;
        if ($field_settings === \false) {
            wp_send_json_error(['message' => 'Invalid Form']);
        }
        $this->initialize_stripe_idempotency_context((string) ($form['settings']['form_post_id'] ?? ''), (string) ($form['settings']['dce_post_id'] ?? ''), (string) ($form['settings']['dce_queried_id'] ?? ''), (string) ($form['settings']['id'] ?? ''), (string) ($field_settings['custom_id'] ?? ''));
        if (!$this->acquire_stripe_request_slot()) {
            $this->send_stripe_busy_response();
            return;
        }
        if (!$this->consume_stripe_rate_budget() || !$this->owns_option_lease($this->stripe_request_slot_option, $this->stripe_request_slot_lease)) {
            $this->release_stripe_request_slot();
            $this->send_stripe_busy_response();
            return;
        }
        $data = null;
        $error_message = null;
        try {
            if (($field_settings['dce_stripe_is_subscription'] ?? '') === 'yes') {
                $data = $this->create_subscription($form, $field_settings);
            } else {
                // simple payment
                $data = $this->create_single_payment($form, $field_settings);
            }
        } catch (\Throwable $e) {
            $error_message = current_user_can('manage_options') ? $e->getMessage() : 'Stripe Error';
        } finally {
            $this->release_stripe_request_slot();
        }
        if (null !== $error_message) {
            wp_send_json_error(['message' => $error_message]);
        }
        if ($data === \false) {
            wp_send_json_error(['message' => 'Stripe Authentication Error']);
        }
        wp_send_json_success($data);
    }
    /** Example: 10, USD will return 1000. 10, YEN will return 10. */
    public function get_amount_in_currency_smallest_unit(float $amount, string $currency_code)
    {
        $iso4217 = new \DynamicOOOS\Payum\ISO4217\ISO4217();
        $currency = $iso4217->findByAlpha3($currency_code);
        $exponent = $currency->getExp();
        return \intval(\round($amount * \pow(10, $exponent)));
    }
    /** Notify the admin if one of the customer reference fields cannot be found */
    public function debug_check_customer_reference_fields($item)
    {
        if (current_user_can('manage_options')) {
            $fields = ['dce_stripe_customer_name_field_id', 'dce_stripe_customer_email_field_id', 'dce_stripe_customer_phone_field_id'];
            foreach ($fields as $field) {
                $field_name = $item[$field] ?? '';
                if ($field_name !== '' && !isset($_POST['form_fields'][$field_name])) {
                    /* translators: %1$s: field name */
                    $msg = \sprintf(esc_html__('Stripe: cannot find customer field `%1$s`. Please, do not use shortcode or tokens, just insert the ID of the field as it is.', 'dynamic-content-for-elementor'), $field_name);
                    wp_send_json_error(['message' => $msg]);
                }
            }
        }
    }
    public function make_stripe_customer($item)
    {
        $this->debug_check_customer_reference_fields($item);
        $customer = ['name' => $item['dce_stripe_customer_name_field_id'] ?? '', 'email' => $item['dce_stripe_customer_email_field_id'] ?? '', 'phone' => $item['dce_stripe_customer_phone_field_id'] ?? ''];
        $customer = \array_filter($customer, function ($id) {
            return !empty($id);
        });
        $customer = \array_map(function ($id) {
            return $_POST['form_fields'][$id];
        }, $customer);
        $idempotency_key = $this->get_stripe_idempotency_key('customer-' . \hash('sha256', (string) wp_json_encode($customer)));
        $options = null === $idempotency_key ? null : ['idempotency_key' => $idempotency_key];
        return \DynamicOOOS\Stripe\Customer::create($customer, $options);
    }
    public function expand_description_form_tokens($description)
    {
        return \preg_replace_callback('/\\[form:([^\\]]+)\\]/', function ($matches) {
            return $_POST['form_fields'][$matches[1] ?? ''] ?? '';
        }, $description);
    }
    public function create_subscription($form, $item)
    {
        if (($item['dce_form_stripe_price_id_from_field'] ?? '') === 'yes') {
            $field_id = $item['dce_form_stripe_price_id_field_id'] ?? '';
            $price_id = $_POST['form_fields'][$field_id] ?? \false;
            if ($price_id === \false) {
                wp_send_json_error(['message' => esc_html__('Could not find the Price ID field. Please just insert the field ID as it is, not inside a token or shortcode.', 'dynamic-content-for-elementor')]);
            }
        } else {
            $price_id = $item['dce_form_stripe_price_id'];
        }
        $customer = $this->make_stripe_customer($item);
        $subscription_data = ['customer' => $customer->id, 'items' => [['price' => $price_id]], 'payment_behavior' => 'default_incomplete', 'expand' => ['latest_invoice.payment_intent'], 'metadata' => ['dce_id' => $form['settings']['id'] . '-' . $item['custom_id'], 'dce_blog_id' => (string) get_current_blog_id(), 'dce_user_id' => (string) get_current_user_id(), 'dce_post_id' => (string) $form['settings']['dce_post_id'], 'dce_queried_id' => (string) $form['settings']['dce_queried_id'], 'dce_replay_guard' => $this->get_stripe_replay_guard()]];
        try {
            $idempotency_key = $this->get_stripe_idempotency_key('subscription-' . \hash('sha256', (string) wp_json_encode($subscription_data)));
            $options = null === $idempotency_key ? null : ['idempotency_key' => $idempotency_key];
            $subscription = \DynamicOOOS\Stripe\Subscription::create($subscription_data, $options);
            return ['client_secret' => $subscription->latest_invoice->payment_intent->client_secret, 'subscription_id' => $subscription->id];
        } catch (\DynamicOOOS\Stripe\Exception\AuthenticationException $e) {
            return \false;
        }
    }
    /**
     *  @return array<int|string,array{value:mixed,raw_value:mixed}>
     */
    public function get_shortcode_form_fields()
    {
        $res = [];
        foreach ($_POST['form_fields'] as $key => $value) {
            $res[$key]['raw_value'] = $value;
            if (\is_array($value)) {
                $value = \implode(',', $value);
            }
            $res[$key]['value'] = $value;
        }
        return $res;
    }
    public function create_single_payment($form, $item)
    {
        // We might get the amount from another field, or statically.
        if (($item['dce_form_stripe_value_from_field'] ?? '') === 'yes') {
            $field_id = $item['dce_form_stripe_value_field_id'] ?? '';
            $amount = $_POST['form_fields'][$field_id] ?? \false;
            if ($amount === \false) {
                wp_send_json_error(['message' => esc_html__('Could not find the Amount field. Please just insert the field ID as it is, not inside a token or shortcode.', 'dynamic-content-for-elementor')]);
            }
        } else {
            $amount = $item['dce_form_stripe_item_value'];
        }
        $amount = (float) $amount;
        if ($amount <= 0) {
            wp_send_json_error(['message' => 'Invalid amount given']);
        }
        $currency = \trim($item['dce_form_stripe_currency']);
        $amount = $this->get_amount_in_currency_smallest_unit($amount, $currency);
        $intent_data = ['amount' => $amount, 'currency' => $currency, 'confirm' => \false, 'capture_method' => 'manual', 'description' => \DynamicContentForElementor\Plugin::instance()->text_templates->expand_shortcodes_or_callback($item['dce_form_stripe_item_description'] ?? '', ['form-fields' => $this->get_shortcode_form_fields()], [$this, 'expand_description_form_tokens']), 'automatic_payment_methods' => ['enabled' => \false], 'payment_method_types' => ['card'], 'metadata' => ['dce_id' => $form['settings']['id'] . '-' . $item['custom_id'], 'dce_blog_id' => (string) get_current_blog_id(), 'dce_user_id' => (string) get_current_user_id(), 'dce_post_id' => (string) $form['settings']['dce_post_id'], 'dce_queried_id' => (string) $form['settings']['dce_queried_id'], 'sku' => $item['dce_form_stripe_item_sku'] ?? '']];
        if (($item['dce_stripe_future_usage'] ?? '') === 'yes') {
            $intent_data['setup_future_usage'] = 'off_session';
        }
        if (($item['dce_stripe_attach_customer'] ?? '') === 'yes') {
            $customer = $this->make_stripe_customer($item);
            $intent_data['customer'] = $customer->id;
        }
        try {
            $idempotency_key = $this->get_stripe_idempotency_key('payment-intent-' . \hash('sha256', (string) wp_json_encode($intent_data)));
            $options = null === $idempotency_key ? null : ['idempotency_key' => $idempotency_key];
            $intent = \DynamicOOOS\Stripe\PaymentIntent::create($intent_data, $options);
            return ['client_secret' => $intent->client_secret];
        } catch (\DynamicOOOS\Stripe\Exception\AuthenticationException $e) {
            return \false;
        }
    }
}
