<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor;

use DynamicContentForElementor\Helper;
use ElementorPro\Modules\AssetsManager\AssetTypes\Fonts_Manager;
use ElementorPro\Modules\AssetsManager\AssetTypes\Fonts\Custom_Fonts;
use DynamicContentForElementor\Plugin;
// disable phpcs because of a bug with wordpress sniffs with fix not yet released.
// phpcs:ignoreFile
if (!\defined('ABSPATH')) {
    exit;
}
class PdfHtmlTemplatesHttpClient implements \DynamicOOOS\Mpdf\Http\ClientInterface
{
    private const MAX_REDIRECTS = 3;
    private const MAX_RESPONSE_SIZE = 20971520;
    private const NON_PUBLIC_RANGES = ['0.0.0.0/8', '10.0.0.0/8', '100.64.0.0/10', '127.0.0.0/8', '169.254.0.0/16', '172.16.0.0/12', '192.0.0.0/24', '192.0.2.0/24', '192.88.99.0/24', '192.168.0.0/16', '198.18.0.0/15', '198.51.100.0/24', '203.0.113.0/24', '224.0.0.0/4', '240.0.0.0/4', '::/128', '::1/128', '::ffff:0:0/96', '64:ff9b:1::/48', '100::/64', '2001::/23', '2001:db8::/32', '2002::/16', 'fc00::/7', 'fe80::/10', 'ff00::/8'];
    public function sendRequest(\DynamicOOOS\Psr\Http\Message\RequestInterface $request) : \DynamicOOOS\Psr\Http\Message\ResponseInterface
    {
        return $this->fetch((string) $request->getUri(), self::MAX_REDIRECTS);
    }
    private function fetch(string $url, int $redirects_left) : \DynamicOOOS\Psr\Http\Message\ResponseInterface
    {
        if (!$this->is_url_allowed($url)) {
            return $this->create_response(403);
        }
        $args = ['timeout' => 5, 'redirection' => 0, 'limit_response_size' => self::MAX_RESPONSE_SIZE];
        $wp_response = $this->is_trusted_url($url) ? wp_remote_get($url, $args) : wp_safe_remote_get($url, $args);
        if (is_wp_error($wp_response)) {
            return $this->create_response(502);
        }
        $status = (int) wp_remote_retrieve_response_code($wp_response);
        if ($status >= 300 && $status < 400 && $redirects_left > 0) {
            $location = wp_remote_retrieve_header($wp_response, 'location');
            if (\is_array($location)) {
                $location = \end($location);
            }
            if (\is_string($location) && '' !== $location) {
                $redirect_url = \DynamicOOOS\WP_Http::make_absolute_url($location, $url);
                return $this->fetch($redirect_url, $redirects_left - 1);
            }
        }
        if ($status < 100 || $status > 599) {
            $status = 502;
        }
        return $this->create_response($status, wp_remote_retrieve_body($wp_response));
    }
    private function create_response(int $status, string $body = '') : \DynamicOOOS\Psr\Http\Message\ResponseInterface
    {
        return (new \DynamicOOOS\Mpdf\PsrHttpMessageShim\Response())->withStatus($status)->withBody(\DynamicOOOS\Mpdf\PsrHttpMessageShim\Stream::create($body));
    }
    private function is_url_allowed(string $url) : bool
    {
        $parts = wp_parse_url($url);
        if (!\is_array($parts) || !isset($parts['scheme'], $parts['host'])) {
            return \false;
        }
        if (!\in_array(\strtolower($parts['scheme']), ['http', 'https'], \true) || isset($parts['user']) || isset($parts['pass'])) {
            return \false;
        }
        if ($this->is_trusted_url($url)) {
            return \true;
        }
        $addresses = $this->resolve_host($parts['host']);
        if (empty($addresses)) {
            return \false;
        }
        foreach ($addresses as $address) {
            if (!$this->is_public_address($address)) {
                return \false;
            }
        }
        return \true;
    }
    private function is_trusted_url(string $url) : bool
    {
        $parts = wp_parse_url($url);
        if (!\is_array($parts) || !isset($parts['scheme'], $parts['host'])) {
            return \false;
        }
        $scheme = \strtolower($parts['scheme']);
        $host = \strtolower(\trim($parts['host'], '[] .'));
        $port = isset($parts['port']) ? (int) $parts['port'] : ('https' === $scheme ? 443 : 80);
        $trusted_urls = [home_url(), site_url(), content_url()];
        $uploads = wp_upload_dir();
        if (!empty($uploads['baseurl'])) {
            $trusted_urls[] = $uploads['baseurl'];
        }
        foreach ($trusted_urls as $trusted_url) {
            $trusted = wp_parse_url($trusted_url);
            if (!\is_array($trusted) || empty($trusted['host'])) {
                continue;
            }
            $trusted_scheme = isset($trusted['scheme']) ? \strtolower($trusted['scheme']) : 'http';
            $trusted_host = \strtolower(\trim($trusted['host'], '[] .'));
            $trusted_port = isset($trusted['port']) ? (int) $trusted['port'] : ('https' === $trusted_scheme ? 443 : 80);
            if ($host === $trusted_host && $port === $trusted_port) {
                return \true;
            }
        }
        return \false;
    }
    /** @return string[] */
    private function resolve_host(string $host) : array
    {
        $host = \strtolower(\trim($host, '[] .'));
        if (\filter_var($host, \FILTER_VALIDATE_IP)) {
            return [$host];
        }
        $addresses = [];
        if (\function_exists('dns_get_record')) {
            $records = @\dns_get_record($host, \DNS_A | \DNS_AAAA);
            if (\is_array($records)) {
                foreach ($records as $record) {
                    if (!empty($record['ip'])) {
                        $addresses[] = $record['ip'];
                    }
                    if (!empty($record['ipv6'])) {
                        $addresses[] = $record['ipv6'];
                    }
                }
            }
        }
        $ipv4_addresses = @\gethostbynamel($host);
        if (\is_array($ipv4_addresses)) {
            $addresses = \array_merge($addresses, $ipv4_addresses);
        }
        return \array_values(\array_unique($addresses));
    }
    private function is_public_address(string $address) : bool
    {
        if (!\filter_var($address, \FILTER_VALIDATE_IP)) {
            return \false;
        }
        foreach (self::NON_PUBLIC_RANGES as $range) {
            if ($this->address_is_in_range($address, $range)) {
                return \false;
            }
        }
        return \true;
    }
    private function address_is_in_range(string $address, string $range) : bool
    {
        [$network, $prefix] = \explode('/', $range, 2);
        $address_bytes = \inet_pton($address);
        $network_bytes = \inet_pton($network);
        if (\false === $address_bytes || \false === $network_bytes || \strlen($address_bytes) !== \strlen($network_bytes)) {
            return \false;
        }
        $prefix = (int) $prefix;
        $whole_bytes = \intdiv($prefix, 8);
        $remaining_bits = $prefix % 8;
        if ($whole_bytes > 0 && \substr($address_bytes, 0, $whole_bytes) !== \substr($network_bytes, 0, $whole_bytes)) {
            return \false;
        }
        if (0 === $remaining_bits) {
            return \true;
        }
        $mask = 0xff << 8 - $remaining_bits & 0xff;
        return (\ord($address_bytes[$whole_bytes]) & $mask) === (\ord($network_bytes[$whole_bytes]) & $mask);
    }
}
class PdfHtmlTemplates
{
    private $tempdir;
    private const PDF_MAX_CONCURRENT_GENERATIONS = 2;
    private const PDF_RATE_LIMIT = 20;
    private const PDF_CLIENT_RATE_LIMIT = 5;
    private const PDF_RATE_WINDOW = 60;
    private const PDF_RATE_LOCK_TTL = 10;
    private const PDF_GENERATION_LEASE_TTL = 300;
    private const PDF_GENERATION_SLOT_PREFIX = 'dce_pdf_generation_slot_';
    private const PDF_RATE_STATE_OPTION = 'dce_pdf_rate_state';
    private const PDF_RATE_LOCK_OPTION = 'dce_pdf_rate_lock';
    private string $pdf_generation_slot_option = '';
    private string $pdf_generation_slot_lease = '';
    const CPT = 'dce_html_template';
    const FONTS_CACHE_TRANSIENT = 'dce_html_template_fonts_cache';
    const TEMPLATE_META_KEY = 'dce_html_template';
    const FIELD_IS_TEMPLATE = 'dce-html-is-template';
    const FIELD_TEMPLATE_ID = 'dce-html-template-id';
    const FIELD_CODE = 'dce-html-code';
    const FIELD_PREVIEW_FORM_DATA = 'dce-preview-form-data';
    const FIELD_PREVIEW_POST = 'dce-preview-post';
    const FIELD_FORMAT = 'dce-html-format';
    const FIELD_ORIENTATION = 'dce-html-orientation';
    const DEFAULT_HTML_CODE = <<<EOF
<head>
<style>
@page {
\theader: html_myHeader;
}
body {
\tfont-family: chelvetica;
}
h1 {
\tfont-family: ctimes;
}
code {
\tfont-family: ccourier; color: orange;
}
</style>
</head>
<body>
<htmlpageheader name="myHeader">
\tPage {data:page-number} of {data:number-of-pages}
</htmlpageheader>
<h1>
\tDynamic.ooo PDF Generator
</h1>
<p>
Hi {form:name}, your favorite animals are:
\t<ul>
\t{for:animal {form:animals @raw}
\t\t[<li>{get:animal}</li>]
\t}
\t</ul>
</p>
</body>
EOF;
    const TIMBER_HTML_CODE = <<<EOF
<head>
<style>
@page {
\theader: html_myHeader;
}
body {
\tfont-family: chelvetica;
}
h1 {
\tfont-family: ctimes;
}
code {
\tfont-family: ccourier; color: orange;
}
</style>
</head>
<body>
<htmlpageheader name="myHeader">
\tPage {PAGENO}/{nbpg}
</htmlpageheader>
<h1>
\tDynamic.ooo PDF Generator
</h1>
<p>
Hi {{ form.name }}, your favorite animals are:
<ul>
{% for animal in form_raw.animals %}
\t<li>
\t{{ animal }}
\t</li>
{% endfor %}
</ul>
</p>
<p>
\tNotice that this is a Timber Template to be used with expressions like <code>{{ '{{ form.name }}' }}</code> and not tokens like <code>[form:name]</code>. Read this example code for more details.
</p>
</body>
EOF;
    const DEFAULT_PREVIEW_DATA = <<<EOF
name|Joe
animals|Dog,Llama
EOF;
    public $post_type_object;
    public function __construct()
    {
        $this->tempdir = get_temp_dir() . 'mpdf';
        add_action('init', [$this, 'register_post_type']);
        add_action('wp_ajax_dce_pdf_button', [$this, 'pdf_button_ajax']);
        add_action('wp_ajax_nopriv_dce_pdf_button', [$this, 'pdf_button_ajax']);
        add_action('wp_ajax_dce_preview_pdf_html_template', [$this, 'preview_pdf_html_template']);
        add_action('wp_ajax_dce_get_posts', [$this, 'dce_get_posts_ajax_callback']);
        add_action('add_meta_boxes_' . self::CPT, [$this, 'add_meta_boxes']);
        add_action('save_post_' . self::CPT, [$this, 'save_post_meta'], 10, 3);
        add_action('save_post_elementor_font', function () {
            delete_transient(self::FONTS_CACHE_TRANSIENT);
        }, 100);
    }
    private function clean_temp_dir()
    {
        $files = \glob("{$this->tempdir}/mpdf/ttfontdata/*");
        if ($files !== \false) {
            \array_map('unlink', $files);
        }
    }
    private function can_view_pdf_content(int $post_id) : bool
    {
        $post = get_post($post_id);
        if (!$post) {
            return \false;
        }
        if (post_password_required($post) && !current_user_can('edit_post', $post_id)) {
            return \false;
        }
        return is_post_publicly_viewable($post) || current_user_can('read_post', $post_id);
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
    private function acquire_pdf_generation_slot() : bool
    {
        $max_concurrent = \min(20, \max(1, (int) apply_filters('dynamicooo/pdf-button/max-concurrent-generations', self::PDF_MAX_CONCURRENT_GENERATIONS)));
        $execution_limit = (int) \ini_get('max_execution_time');
        $default_ttl = $execution_limit > 0 ? \max(self::PDF_GENERATION_LEASE_TTL, $execution_limit + 60) : 3600;
        $ttl = \max(60, (int) apply_filters('dynamicooo/pdf-button/generation-lease-ttl', $default_ttl));
        for ($slot = 0; $slot < $max_concurrent; ++$slot) {
            $option_name = self::PDF_GENERATION_SLOT_PREFIX . $slot;
            $lease = $this->acquire_option_lease($option_name, $ttl);
            if (null === $lease) {
                continue;
            }
            $this->pdf_generation_slot_option = $option_name;
            $this->pdf_generation_slot_lease = $lease;
            \register_shutdown_function(function () {
                $this->release_pdf_generation_slot();
            });
            return \true;
        }
        return \false;
    }
    private function release_pdf_generation_slot() : void
    {
        if ('' === $this->pdf_generation_slot_option || '' === $this->pdf_generation_slot_lease) {
            return;
        }
        $this->release_option_lease($this->pdf_generation_slot_option, $this->pdf_generation_slot_lease);
        $this->pdf_generation_slot_option = '';
        $this->pdf_generation_slot_lease = '';
    }
    private function get_pdf_rate_client_key() : string
    {
        if (is_user_logged_in()) {
            $client = 'user:' . get_current_user_id();
        } else {
            $client = 'ip:' . Helper::get_client_ip();
        }
        return \hash_hmac('sha256', $client, wp_salt('nonce'));
    }
    private function consume_pdf_rate_budget() : bool
    {
        $rate_lock = $this->acquire_option_lease(self::PDF_RATE_LOCK_OPTION, self::PDF_RATE_LOCK_TTL);
        if (null === $rate_lock) {
            return \false;
        }
        try {
            $limit = \min(1000, \max(1, (int) apply_filters('dynamicooo/pdf-button/rate-limit', self::PDF_RATE_LIMIT)));
            $client_limit = \min($limit, \max(1, (int) apply_filters('dynamicooo/pdf-button/client-rate-limit', self::PDF_CLIENT_RATE_LIMIT)));
            $window = \max(1, (int) apply_filters('dynamicooo/pdf-button/rate-window', self::PDF_RATE_WINDOW));
            $now = \microtime(\true);
            $cutoff = $now - $window;
            $client_key = $this->get_pdf_rate_client_key();
            $state = get_option(self::PDF_RATE_STATE_OPTION, []);
            $timestamps = [];
            $client_count = 0;
            if (\is_array($state) && isset($state['timestamps']) && \is_array($state['timestamps'])) {
                foreach ($state['timestamps'] as $entry) {
                    $timestamp = 0.0;
                    $entry_client = '';
                    if (\is_numeric($entry)) {
                        $timestamp = (float) $entry;
                    } elseif (\is_array($entry) && isset($entry['time']) && \is_numeric($entry['time'])) {
                        $timestamp = (float) $entry['time'];
                        $entry_client = isset($entry['client']) && \is_string($entry['client']) ? $entry['client'] : '';
                    }
                    if ($timestamp <= $cutoff) {
                        continue;
                    }
                    if ('' === $entry_client) {
                        $timestamps[] = $timestamp;
                    } else {
                        $timestamps[] = ['time' => $timestamp, 'client' => $entry_client];
                        if (\hash_equals($entry_client, $client_key)) {
                            ++$client_count;
                        }
                    }
                }
            }
            if (\count($timestamps) >= $limit || $client_count >= $client_limit || !$this->owns_option_lease(self::PDF_RATE_LOCK_OPTION, $rate_lock)) {
                return \false;
            }
            $timestamps[] = ['time' => $now, 'client' => $client_key];
            return update_option(self::PDF_RATE_STATE_OPTION, ['timestamps' => $timestamps], \false);
        } finally {
            $this->release_option_lease(self::PDF_RATE_LOCK_OPTION, $rate_lock);
        }
    }
    private function send_pdf_busy_response() : void
    {
        \header('Retry-After: 10');
        wp_send_json_error(['message' => __('PDF generation is temporarily busy. Please try again.', 'dynamic-content-for-elementor')], 429);
    }
    public function pdf_button_ajax()
    {
        // No nonce verification here on purpose. This is a read-only endpoint: it
        // only renders a PDF and never changes state. Access is gated below by post
        // status and the read_post capability, which is what actually protects
        // non-public content. The nonce was removed so the button keeps working on
        // pages served from a full page cache, where a nonce embedded in the cached
        // HTML would expire before the cached page is refreshed.
        try {
            // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Read-only, capability-gated below; nonce intentionally removed for full-page-cache compatibility.
            $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
            // phpcs:ignore WordPress.Security.NonceVerification.Missing -- See note above.
            $element_id = isset($_POST['element_id']) ? sanitize_text_field(wp_unslash($_POST['element_id'])) : '';
            // phpcs:ignore WordPress.Security.NonceVerification.Missing -- See note above.
            $queried_id = isset($_POST['queried_id']) ? absint($_POST['queried_id']) : $post_id;
            if (!$this->can_view_pdf_content($queried_id)) {
                wp_send_json_error(['message' => 'Unauthorized']);
            }
            if (!$this->can_view_pdf_content($post_id) && ('elementor_library' !== get_post_type($post_id) || 'publish' !== get_post_status($post_id))) {
                wp_send_json_error(['message' => 'Unauthorized']);
            }
            $pdf_button = Helper::get_elementor_element_from_post_data($post_id, $element_id, $queried_id);
            if (!\is_array($pdf_button) || !isset($pdf_button['settings']) || !\is_array($pdf_button['settings'])) {
                wp_send_json_error(['message' => 'Invalid PDF element']);
            }
            $settings = $pdf_button['settings'];
            $get_template_from = $settings['html_converter_get_template_from'];
            if (!\in_array($get_template_from, ['post', 'html_template'], \true)) {
                wp_send_json_error(['message' => __('Invalid PDF source.', 'dynamic-content-for-elementor')]);
            }
            if (!$this->acquire_pdf_generation_slot()) {
                $this->send_pdf_busy_response();
                return;
            }
            if (!$this->consume_pdf_rate_budget() || !$this->owns_option_lease($this->pdf_generation_slot_option, $this->pdf_generation_slot_lease)) {
                $this->release_pdf_generation_slot();
                $this->send_pdf_busy_response();
                return;
            }
            switch ($get_template_from) {
                case 'post':
                    $format = $settings['html_page_size'];
                    $orientation = $settings['orientation'] === 'landscape' ? 'L' : 'P';
                    $this->generate_pdf_from_elementor_post($queried_id, $format, $orientation, \false);
                    break;
                case 'html_template':
                    $t = $settings['html_converter_html_template'];
                    $this->generate_pdf_from_template_id($t, [], [], \false);
                    break;
            }
        } catch (\Throwable $e) {
            \error_log('DCE PDF Button: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            $message = current_user_can('administrator') ? $e->getMessage() : __('PDF generation failed. Please try again or contact support.', 'dynamic-content-for-elementor');
            wp_send_json_error(['message' => $message]);
        } finally {
            $this->release_pdf_generation_slot();
        }
        die;
    }
    /**
     * SPDX-SnippetBegin
     * SPDX-FileCopyrightText: Rudrastyh
     * SPDX-License-Identifier: 
     * Code from https://rudrastyh.com/wordpress/select2-for-metaboxes-with-ajax.html
     */
    public function dce_get_posts_ajax_callback()
    {
        if (!current_user_can('administrator')) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }
        if (!check_ajax_referer('dce_get_posts', 'nonce', \false)) {
            wp_send_json_error(['message' => 'Nonce verification failed'], 403);
        }
        // we will pass post IDs and titles to this array
        $return = array();
        $args = [
            's' => isset($_GET['q']) ? sanitize_text_field($_GET['q']) : '',
            'post_status' => 'publish',
            // if you don't want drafts to be returned
            'ignore_sticky_posts' => 1,
            'posts_per_page' => 50,
        ];
        if (!empty($_GET['dce_post_type'])) {
            $args['post_type'] = sanitize_text_field($_GET['dce_post_type']);
        }
        // you can use WP_Query, query_posts() or get_posts() here - it doesn't matter
        $search_results = new \WP_Query($args);
        while ($search_results->have_posts()) {
            $search_results->the_post();
            // shorten the title a little
            $title = \mb_strlen($search_results->post->post_title) > 50 ? \mb_substr($search_results->post->post_title, 0, 49) . '...' : $search_results->post->post_title;
            $return[] = array($search_results->post->ID, esc_html($title));
            // array( Post ID, Post Title )
        }
        echo wp_json_encode($return);
        die;
    }
    public function register_post_type()
    {
        $labels = ['name' => _x('HTML Templates', 'CPT Name', 'dynamic-content-for-elementor'), 'singular_name' => _x('HTML Template', 'CPT Singular Name', 'dynamic-content-for-elementor'), 'add_new' => esc_html__('Add New', 'dynamic-content-for-elementor'), 'add_new_item' => esc_html__('Add New HTML Template', 'dynamic-content-for-elementor'), 'edit_item' => esc_html__('Edit HTML Template', 'dynamic-content-for-elementor'), 'new_item' => esc_html__('New HTML Template', 'dynamic-content-for-elementor'), 'all_items' => esc_html__('All HTML Template', 'dynamic-content-for-elementor'), 'view_item' => esc_html__('View HTML Template', 'dynamic-content-for-elementor'), 'search_items' => esc_html__('Search HTML Template', 'dynamic-content-for-elementor'), 'not_found' => esc_html__('No HTML Template found', 'dynamic-content-for-elementor'), 'not_found_in_trash' => esc_html__('No HTML Template found in trash', 'dynamic-content-for-elementor'), 'parent_item_colon' => '', 'menu_name' => _x('HTML Templates', 'CPT Menu Name', 'dynamic-content-for-elementor')];
        $args = ['labels' => $labels, 'public' => \false, 'rewrite' => \false, 'show_ui' => \true, 'show_in_menu' => \false, 'show_in_nav_menus' => \false, 'exclude_from_search' => \true, 'capability_type' => 'post', 'hierarchical' => \false, 'supports' => ['title']];
        $this->post_type_object = register_post_type(self::CPT, $args);
    }
    // Convert Elementor font settings to Mpdf. For example bold, italic to "BI".
    // Return false if not supported. For example when using weight as a number.
    private function get_font_settings($weight, $style)
    {
        if ($weight === 'normal') {
            if ($style === 'normal') {
                return 'R';
            } elseif ($style === 'italic') {
                return 'I';
            }
        } elseif ($weight === 'bold') {
            if ($style === 'normal') {
                return 'B';
            } elseif ($style === 'italic') {
                return 'BI';
            }
        }
        return \false;
    }
    /**
     * Check if a font has an otl table.
     *
     * Works by trying to creat a pdf with OTL on and catch a potential error.
     */
    private function font_has_otl($dir, $filename)
    {
        $dirs = [$dir];
        $conf = ['test' => ['R' => $filename, 'useOTL' => 0xff, 'useKashida' => 75]];
        $this->clean_temp_dir();
        try {
            $mpdf = new \DynamicOOOS\Mpdf\Mpdf(['tempDir' => $this->tempdir, 'fontDir' => $dirs, 'fontdata' => $conf, 'default_font' => 'test', 'mono_fonts' => ['test'], 'serif_fonts' => ['test'], 'sans_fonts' => ['test']]);
            $mpdf->WriteHTML('hello world');
            $mpdf->Output(null, 'S');
        } catch (\DynamicOOOS\Mpdf\Exception\FontException $e) {
            return 'no';
        } catch (\Throwable $t) {
            return \false;
        }
        return 'yes';
    }
    // given a font by its CPT id, if supported return an array of containing:
    // - dirs: the directories where the font files are contained.
    // - config: the font configuration array.
    private function get_font($id)
    {
        $directories = [];
        $config = [];
        $saved = get_post_meta($id, Custom_Fonts::FONT_META_KEY, \true);
        $has_otl = \true;
        foreach ($saved as $variation) {
            $id = $variation['ttf']['id'] ?? 0;
            if (!$id) {
                continue;
            }
            $font_settings = $this->get_font_settings($variation['font_weight'], $variation['font_style']);
            if (!$font_settings) {
                continue;
            }
            $path = get_attached_file($id);
            if (!$path) {
                continue;
            }
            $file_name = \basename($path);
            $dir = \dirname($path);
            $res_otl = $this->font_has_otl($dir, $file_name);
            // unexpected error during otl test, skip this variant
            if ($res_otl === \false) {
                continue;
            }
            $has_otl = $res_otl === 'yes';
            $directories[$dir] = \true;
            $config[$font_settings] = $file_name;
        }
        if (empty($config)) {
            return \false;
        } else {
            if ($has_otl) {
                $config['useOTL'] = 0xff;
                $config['useKashida'] = 75;
            }
            return ['dirs' => $directories, 'config' => $config];
        }
    }
    // Find suitable Elementor custom fonts and return an associative array with:
    // - 'fonts' : fontData as used by Mpdf.
    // - 'dirs' : fontDirs as used by Mpdf.
    public function get_fonts()
    {
        if (!\class_exists(Fonts_Manager::class)) {
            return ['dirs' => [], 'fonts' => []];
        }
        $fonts_cache = get_transient(self::FONTS_CACHE_TRANSIENT);
        if (\is_array($fonts_cache)) {
            return $fonts_cache;
        }
        $fonts = new \WP_Query(['post_type' => Fonts_Manager::CPT, 'posts_per_page' => -1]);
        $directories = [];
        $fonts_config = [];
        foreach ($fonts->posts as $font) {
            $font_name = \strtolower($font->post_title);
            $font_name = \str_replace(' ', '', $font_name);
            $res = $this->get_font($font->ID);
            if (\is_array($res)) {
                $directories += $res['dirs'];
                $fonts_config[$font_name] = $res['config'];
            }
        }
        $res = ['dirs' => \array_keys($directories), 'fonts' => $fonts_config];
        set_transient(self::FONTS_CACHE_TRANSIENT, $res, 3600);
        return $res;
    }
    private function get_form_data($text)
    {
        $lines = \explode("\n", $text);
        $data = [];
        foreach ($lines as $line) {
            if (!(\strpos($line, '|') > 0)) {
                continue;
            }
            list($name, $value) = \explode('|', $line);
            $field = ['id' => $name, 'value' => $value, 'raw_value' => $value];
            if (\strpos($value, ',') > 0) {
                $field['raw_value'] = \explode(',', $value);
            }
            $data[$name] = $field;
        }
        return $data;
    }
    /**
     * @param int $template_id
     * @param array<string,mixed> $dsh_bindings
     * @param array<string,mixed> $timber_bindings
     * @param boolean $return_string
     * @return string
     */
    public function generate_pdf_from_template_id($template_id, $dsh_bindings, $timber_bindings, $return_string)
    {
        $post_data = get_post_meta($template_id, self::TEMPLATE_META_KEY, \true);
        if (!$post_data) {
            throw new \Error(esc_html__('PDF HTML: Could not fetch HTML Template, was it deleted?', 'dynamic-content-for-elementor'));
        }
        return $this->generate_pdf_from_html_template($post_data, $dsh_bindings, $timber_bindings, $return_string);
    }
    /**
     * @param array<string,mixed> $post_data
     * @param array<string,mixed> $dsh_bindings
     * @param array<string,mixed> $timber_bindings
     * @param boolean $return_string
     * @return string
     */
    private function generate_pdf_from_html_template($post_data, $dsh_bindings, $timber_bindings, $return_string = \false)
    {
        if (!($post_data[self::FIELD_IS_TEMPLATE] ?? \false)) {
            $code = Plugin::instance()->text_templates->expand_shortcodes_or_callback($post_data[self::FIELD_CODE], $dsh_bindings + ['page-number' => '{PAGENO}', 'number-of-pages' => '{nbpg}'], function ($str) use($timber_bindings) {
                return Plugin::instance()->text_templates->timber->expand($str, $timber_bindings);
            });
        } else {
            $dsh = Plugin::instance()->text_templates->dce_shortcodes;
            $code = \false;
            $dsh->call_with_data($dsh_bindings, function () use(&$code, $post_data) {
                $code = \Elementor\Plugin::instance()->frontend->get_builder_content($post_data[self::FIELD_TEMPLATE_ID], \true);
            });
            if (!$code) {
                throw new \Error(esc_html__('PDF HTML: Could not fetch Elementor Template', 'dynamic-content-for-elementor'));
            }
        }
        $code = apply_filters('dynamicooo/html-pdf/html-template', $code);
        return $this->generate_pdf($code, $post_data[self::FIELD_FORMAT], $post_data[self::FIELD_ORIENTATION], $return_string);
    }
    private function generate_pdf_from_elementor_post($post_id, $format, $orientation, $return_string)
    {
        $code = \Elementor\Plugin::instance()->frontend->get_builder_content($post_id, \true);
        $this->generate_pdf($code, $format, $orientation, $return_string);
    }
    private function generate_pdf($code, $format, $orientation, $return_string)
    {
        try {
            return $this->mpdf_generate_pdf($code, $format, $orientation, $return_string);
        } catch (\DynamicOOOS\Mpdf\MpdfException $e) {
            // maybe temp dir is corrupted, reattempt after clening it:
            $this->clean_temp_dir();
            return $this->mpdf_generate_pdf($code, $format, $orientation, $return_string);
        }
    }
    private function mpdf_generate_pdf($code, $format, $orientation, $return_string)
    {
        $fonts = $this->get_fonts();
        $font_dirs = $fonts['dirs'];
        $font_data = $fonts['fonts'];
        $container = new \DynamicOOOS\Mpdf\Container\SimpleContainer(['httpClient' => new \DynamicContentForElementor\PdfHtmlTemplatesHttpClient()]);
        $mpdf = new \DynamicOOOS\Mpdf\Mpdf(['tempDir' => $this->tempdir, 'fontDir' => $font_dirs, 'fontdata' => $font_data, 'default_font' => 'ctimes', 'mono_fonts' => ['ccourier'], 'serif_fonts' => ['ctimes'], 'sans_fonts' => ['chelvetica'], 'format' => $format . '-' . $orientation], $container);
        $mpdf->WriteHTML($code);
        return $mpdf->Output(null, $return_string ? 'S' : null);
    }
    public function preview_pdf_html_template()
    {
        // Check if our nonce is set.
        if (!isset($_POST[self::CPT . '_nonce'])) {
            wp_send_json_error(['message' => 'Nonce missing']);
        }
        // Verify that the nonce is valid.
        if (!wp_verify_nonce($_POST[self::CPT . '_nonce'], self::CPT)) {
            wp_send_json_error(['message' => 'Nonce Verification Error']);
        }
        if (!current_user_can('administrator')) {
            wp_send_json_error(['message' => 'Unauthorized']);
        }
        try {
            $post_data = stripslashes_deep($_POST);
            $form_data = $this->get_form_data($post_data[self::FIELD_PREVIEW_FORM_DATA]);
            $timber_bindings = ['form' => \array_map(function ($field) {
                return $field['value'];
            }, $form_data), 'form_raw' => \array_map(function ($field) {
                return $field['raw_value'];
            }, $form_data)];
            if ($post_data[self::FIELD_PREVIEW_POST] ?? \false) {
                \Elementor\Plugin::instance()->db->switch_to_post($post_data[self::FIELD_PREVIEW_POST]);
            }
            // set the global dce_form so that preview data works with the
            // widget Text Editor with tokens inside Elementor Templates:
            global $dce_form;
            $dce_form = $form_data;
            $this->generate_pdf_from_html_template($post_data, ['form-fields' => $form_data], $timber_bindings);
        } catch (\DynamicOOOS\Mpdf\MpdfException $e) {
            \error_log('DCE PDF Preview: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            $message = current_user_can('administrator') ? $e->getMessage() : __('PDF preview failed. Please try again or contact support.', 'dynamic-content-for-elementor');
            wp_send_json_error(['message' => $message]);
        } catch (\Throwable $e) {
            \error_log('DCE PDF Preview: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            $message = current_user_can('administrator') ? $e->getMessage() : __('PDF preview failed. Please try again or contact support.', 'dynamic-content-for-elementor');
            wp_send_json_error(['message' => $message]);
        }
        die;
    }
    public function save_post_meta($post_id, $post, $update)
    {
        // If this is an autosave, our form has not been submitted,
        // so we don't want to do anything.
        if (\defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }
        // Check the user's permissions.
        if (!current_user_can('administrator', $post_id)) {
            return $post_id;
        }
        // Check if our nonce is set.
        if (!isset($_POST[self::CPT . '_nonce'])) {
            return $post_id;
        }
        // Verify that the nonce is valid.
        if (!wp_verify_nonce($_POST[self::CPT . '_nonce'], self::CPT)) {
            return $post_id;
        }
        $this->save_meta($post_id, $_POST);
    }
    private function save_meta($post_id, $post_data)
    {
        if (!isset($post_data[self::FIELD_CODE])) {
            return;
        }
        $post_data = stripslashes_deep($post_data);
        $data = [self::FIELD_CODE => $post_data[self::FIELD_CODE] ?? '', self::FIELD_IS_TEMPLATE => $post_data[self::FIELD_IS_TEMPLATE] ?? '', self::FIELD_TEMPLATE_ID => $post_data[self::FIELD_TEMPLATE_ID] ?? '', self::FIELD_PREVIEW_FORM_DATA => $post_data[self::FIELD_PREVIEW_FORM_DATA] ?? '', self::FIELD_PREVIEW_POST => $post_data[self::FIELD_PREVIEW_POST] ?? '', self::FIELD_FORMAT => $post_data[self::FIELD_FORMAT] ?? '', self::FIELD_ORIENTATION => $post_data[self::FIELD_ORIENTATION] ?? ''];
        update_post_meta($post_id, self::TEMPLATE_META_KEY, $data);
    }
    public function get_attribute_string($attributes)
    {
        $attributes_array = [];
        foreach ($attributes as $name => $value) {
            $attributes_array[] = \sprintf('%s="%s"', $name, esc_attr($value));
        }
        return \implode(' ', $attributes_array);
    }
    public function render_code_metabox($post)
    {
        $notice = Plugin::instance()->text_templates->get_notice_html_templates();
        if (!empty($notice)) {
            echo '<p class="dce-pdf-template-notice">';
            echo $notice['content'];
            echo '</p>';
            if ($notice['required'] ?? \false) {
                return;
            }
        }
        wp_enqueue_script('dce-pdf-html-template', DCE_URL . 'assets/js/pdf-html-templates.js', [], DCE_VERSION, \true);
        wp_localize_script('dce-pdf-html-template', 'dce_pdf_html_vars', ['nonce' => wp_create_nonce('dce_get_posts')]);
        $data = get_post_meta($post->ID, self::TEMPLATE_META_KEY, \true);
        wp_nonce_field(self::CPT, self::CPT . '_nonce');
        $attr = ['name' => self::FIELD_IS_TEMPLATE, 'id' => self::FIELD_IS_TEMPLATE, 'type' => 'checkbox'];
        $checked = $data[self::FIELD_IS_TEMPLATE] ?? \false ? ' checked' : '';
        echo '<p><input ' . $this->get_attribute_string($attr) . $checked . '>';
        echo '<label for="' . self::FIELD_IS_TEMPLATE . '">' . esc_html__('Get the HTML from an Elementor Template (easier, but you have less control of the result).', 'dynamic-content-for-elementor') . '</label></p>';
        $template_id = $data[self::FIELD_TEMPLATE_ID] ?? \false;
        echo '<div id="dce-html-template-section">';
        $label = esc_html__('Select the Elementor Template', 'dynamic-content-for-elementor');
        $this->render_select2(self::FIELD_TEMPLATE_ID, $template_id, $label);
        echo '</div>';
        $attr = ['name' => self::FIELD_CODE, 'id' => self::FIELD_CODE];
        $code = $data[self::FIELD_CODE] ?? $this->get_default_html_code();
        echo '<div id="dce-html-code-section">';
        echo '<textarea ' . $this->get_attribute_string($attr) . ' >' . esc_textarea($code) . '</textarea>';
        echo '</div>';
        $this->enqueue_code_editor_scripts(self::FIELD_CODE);
    }
    /**
     * @return string
     */
    protected function get_default_html_code()
    {
        $notice = Plugin::instance()->text_templates->get_notice_html_templates();
        if ('timber_only' === ($notice['case'] ?? \false)) {
            return self::TIMBER_HTML_CODE;
        }
        return self::DEFAULT_HTML_CODE;
    }
    public function render_preview_metabox($post)
    {
        $btn_url = admin_url('admin-ajax.php');
        $attr = ['data-action' => 'dce_preview_pdf_html_template', 'data-url' => $btn_url, 'id' => 'dce-preview-pdf', 'type' => 'button', 'class' => 'dce-pdf-preview-button'];
        echo '<button ' . $this->get_attribute_string($attr) . ' >' . esc_html__('Preview PDF', 'dynamic-content-for-elementor') . '</button>';
        echo '<div style="display: none" id="dce-preview-error" class="notice inline notice-alt notice-error"></div>';
    }
    // This function will only present the font options to the user.
    public function render_fonts_metabox($post)
    {
        echo '<p>' . \sprintf(esc_html__('The following fonts can be used with the %sfont-family%s CSS property. ', 'dynamic-content-for-elementor'), '<code>', '</code>') . '</p>';
        echo '<h4>' . esc_html__('Core Fonts', 'dynamic-content-for-elementor') . '</h4>';
        echo '<p>';
        echo esc_html__('The available core fonts are: ', 'dynamic-content-for-elementor');
        echo '<code>ctimes</code>, <code>chelvetica</code>, <code>ccourier</code>';
        echo '</p>';
        echo '<p>' . esc_html__('RTL languages: please notice that you cannot use the core fonts in a page that contains also an RTL language, like Arabic or Hebrew. Upload them as Custom Fonts if you need them.', 'dynamic-content-for-elementor') . '</p>';
        echo '<h4>' . esc_html__('Custom Fonts', 'dynamic-content-for-elementor') . '</h4>';
        $text = \sprintf(
            /* translators: %s: URL for the Elementor Custom Fonts menu page. */
            esc_html__('Custom Fonts can be added in the %sElementor Custom Fonts menu page%s. Only the TTF type is supported. Weight can only be normal or bold, style can only be normal or italic. The following are the ones that were detected:', 'dynamic-content-for-elementor'),
            '<a href="' . esc_url(admin_url('edit.php?post_type=elementor_font')) . '">',
            '</a>'
        );
        echo '<p>' . $text . '</p>';
        $font_data = $this->get_fonts()['fonts'];
        echo '<ul>';
        foreach ($font_data as $font_name => $data) {
            echo '<li><code>' . $font_name . '</code>, ' . esc_html__('weight-style variants:', 'dynamic-content-for-elementor') . ' (';
            echo '<ul style="display: inline;">';
            foreach ($data as $config => $_) {
                if ('useOTL' === $config || 'useKashida' === $config) {
                    // these font configs are for mpdf and don't need to be displayed
                    continue;
                }
                echo '<li style="display: inline;">';
                $bold = esc_html__('bold', 'dynamic-content-for-elementor');
                $normal = esc_html__('normal', 'dynamic-content-for-elementor');
                $italic = esc_html__('italic', 'dynamic-content-for-elementor');
                echo \strpos($config, 'B') !== \false ? $bold : $normal;
                echo '-';
                echo \strpos($config, 'I') !== \false ? $italic : $normal;
            }
            echo '</ul>';
            echo ')';
        }
        echo '</ul>';
    }
    /**
     * @return void
     */
    public function render_images_metabox()
    {
        $media_url = get_admin_url() . '/upload.php';
        echo '<p>' . esc_html__('To insert an image, first go to the ', 'dynamic-content-for-elementor');
        echo "<a href='{$media_url}'>" . esc_html__('WordPress Media Library', 'dynamic-content-for-elementor') . '</a>';
        echo esc_html__(', select an image and find its ID. Then you can use the image like this:', 'dynamic-content-for-elementor') . '</p>';
        if (Helper::is_plugin_active('dynamic-shortcodes')) {
            echo '<code>{media:file-path @ID=your-id-here}</code>';
            echo '<p>' . esc_html__('Replace "your-id-here" with the ID of the image.', 'dynamic-content-for-elementor') . '</p>';
            echo '<p>' . esc_html__('To insert a signature, use the following Dynamic Shortcode:', 'dynamic-content-for-elementor') . '</p>';
            echo '<code>' . esc_attr('<img src="{form:signature-field-id @raw}">') . '</code>';
        } elseif (Helper::is_plugin_active('timber')) {
            echo '<code>&lt;img src="{{ Image( &lt;ID&gt; ).file_loc }}"&gt;</code>';
            echo '<p>' . \sprintf(esc_html__('Notice how we used %1$s.file_loc%2$s, which is a file system path, instead of a URL. Avoid image URLs as they will be slow to fetch.', 'dynamic-content-for-elementor'), '<code>', '</code>') . '</p>';
            echo '<p>' . esc_html__('To insert a signature you can use:', 'dynamic-content-for-elementor') . '</p>';
            echo '<code>&lt;img src="{{ form_raw.signature_field_id }}"&gt;</code>';
        }
    }
    // Render a select2 input where $id is its id, and $post_id is the
    // preselected post id.
    public function render_select2($id, $post_id, $label)
    {
        // do not forget about WP Nonces for security purposes
        $attr = ['name' => $id, 'id' => $id, 'style' => 'width:99%;max-width:25em;'];
        if ($post_id) {
            $title = wp_kses_post(get_the_title($post_id));
            $title = \mb_strlen($title) > 50 ? \mb_substr($title, 0, 49) . '...' : $title;
        }
        echo '<p><label for="' . $attr['id'] . '">' . $label . '</label><br />';
        echo '<select ' . $this->get_attribute_string($attr) . '>';
        if ($post_id) {
            echo '<option value="' . esc_attr($post_id) . '">' . esc_html($title) . '</option>';
        }
        echo '</select></p>';
    }
    public function render_preview_data_metabox($post)
    {
        wp_enqueue_script('dce-pdf-html-template', DCE_URL . 'assets/js/pdf-html-templates.js', [], DCE_VERSION, \true);
        $data = get_post_meta($post->ID, self::TEMPLATE_META_KEY, \true);
        $post_id = $data[self::FIELD_PREVIEW_POST] ?? \false;
        $label = esc_html__('Select a Post to get things like Post Title, ACF fields etc. for the preview (can leave empty if these are not used)', 'dynamic-content-for-elementor');
        $this->render_select2(self::FIELD_PREVIEW_POST, $post_id, $label);
        $attr = ['name' => self::FIELD_PREVIEW_FORM_DATA, 'id' => self::FIELD_PREVIEW_FORM_DATA, 'style' => 'width: 100%; height: 10em;'];
        $form_data = $data[self::FIELD_PREVIEW_FORM_DATA] ?? self::DEFAULT_PREVIEW_DATA;
        echo '<p><label for="' . $attr['id'] . '">' . esc_html__('Here you can insert form data so that you can see them in the preview. The name of the field is followed by a | and then by its value. For fields that allow multiple selection like Checkbox you can separate the selected values by a comma.', 'dynamic-content-for-elementor') . '</label>';
        echo '<textarea ' . $this->get_attribute_string($attr) . ' >' . esc_textarea($form_data) . '</textarea></p>';
    }
    public function render_dimensions_metabox($post)
    {
        $data = get_post_meta($post->ID, self::TEMPLATE_META_KEY, \true);
        $format_attr = ['name' => self::FIELD_FORMAT, 'id' => self::FIELD_FORMAT];
        $orientation_attr = ['name' => self::FIELD_ORIENTATION, 'type' => 'radio'];
        $selected_format = $data[self::FIELD_FORMAT] ?? 'A4';
        $selected_orientation = $data[self::FIELD_ORIENTATION] ?? 'P';
        $formats = ['A4', 'A5', 'A6', 'Letter', 'Legal', 'Executive', 'Folio'];
        echo '<select ' . $this->get_attribute_string($format_attr) . ' >';
        foreach ($formats as $format) {
            if ($format === $selected_format) {
                echo '<option selected>';
            } else {
                echo '<option>';
            }
            echo $format . '</option>';
        }
        echo '</select>';
        echo '<p>';
        echo '<label for="portrait">' . esc_html__('Portrait', 'dynamic-content-for-elementor') . '</label>';
        $checked = $selected_orientation === 'P' ? ' checked ' : '';
        echo '<input value="P" ' . $this->get_attribute_string($orientation_attr) . $checked . '>';
        echo '<label for="landscape">' . esc_html__('Landscape', 'dynamic-content-for-elementor') . '</label>';
        $checked = $selected_orientation === 'L' ? ' checked ' : '';
        echo '<input value="L" ' . $this->get_attribute_string($orientation_attr) . $checked . '>';
        echo '</p>';
    }
    private function get_code_editor_settings()
    {
        // TODO: Handle `enqueue_code_editor_scripts` to work with `lint => 'true'`.
        return ['type' => 'text/html', 'codemirror' => ['indentUnit' => 2, 'tabSize' => 2, 'mode' => ['name' => 'twig', 'base' => 'text/html']]];
    }
    private function enqueue_code_editor_scripts($field_code_id)
    {
        wp_enqueue_script('htmlhint');
        wp_enqueue_script('csslint');
        wp_add_inline_script(
            // fix as described here: https://make.wordpress.org/core/2017/10/22/code-editing-improvements-in-wordpress-4-9/
            'wp-codemirror',
            'window.CodeMirror = wp.CodeMirror;'
        );
        wp_enqueue_script('codemirror-twig', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.29.0/mode/twig/twig.min.js', ['wp-codemirror'], DCE_VERSION, \false);
        /**
         * Some of the plugins may load 'code-editor' for their needs and change the default behavior, so it should
         * re-initialize the code editor with 'custom code' settings.
         */
        if (wp_script_is('code-editor')) {
            wp_add_inline_script('code-editor', \sprintf('wp.codeEditor.initialize( jQuery( "#%s"), %s );', $field_code_id, wp_json_encode(wp_get_code_editor_settings($this->get_code_editor_settings()))));
        } else {
            wp_enqueue_code_editor($this->get_code_editor_settings());
            wp_add_inline_script('code-editor', \sprintf('wp.codeEditor.initialize( jQuery( "#%s") );', $field_code_id));
        }
    }
    public function add_meta_boxes()
    {
        add_meta_box('elementor-pdf-html-code-metabox', esc_html__('HTML', 'dynamic-content-for-elementor'), [$this, 'render_code_metabox'], self::CPT, 'normal', 'default');
        add_meta_box('elementor-pdf-html-preview-metabox', esc_html__('Preview', 'dynamic-content-for-elementor'), [$this, 'render_preview_metabox'], self::CPT, 'side', 'default');
        add_meta_box('elementor-pdf-html-dimensions-metabox', esc_html__('Dimensions', 'dynamic-content-for-elementor'), [$this, 'render_dimensions_metabox'], self::CPT, 'normal', 'default');
        add_meta_box('elementor-pdf-html-fonts-metabox', esc_html__('Fonts', 'dynamic-content-for-elementor'), [$this, 'render_fonts_metabox'], self::CPT, 'normal', 'default');
        add_meta_box('elementor-pdf-html-preview-data-metabox', esc_html__('Preview Data', 'dynamic-content-for-elementor'), [$this, 'render_preview_data_metabox'], self::CPT, 'normal', 'default');
        add_meta_box('elementor-pdf-html-image-metabox', esc_html__('Inserting Images', 'dynamic-content-for-elementor'), [$this, 'render_images_metabox'], self::CPT, 'side', 'default');
    }
}
