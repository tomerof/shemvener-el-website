<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor;

use DynamicContentForElementor\Utils\DateFormatDetector;
use Elementor\Controls_Manager;
use Elementor\Core\Common\Modules\Ajax\Module as Elementor_Ajax;
use Elementor\Widget_Base;
use Elementor\TemplateLibrary\Source_Local;
if (!\defined('ABSPATH')) {
    exit;
}
class Ajax
{
    /**
     * Maximum number of distinct 'dce-file-<md5>' option rows the file-hit
     * counter will create, to bound wp_options growth from anonymous requests.
     */
    private const MAX_FILE_HIT_OPTIONS = 5000;
    /**
     * Per-client-IP rate limit for the anonymous file-hit endpoint: at most
     * FILE_HIT_RATE_LIMIT requests per FILE_HIT_RATE_WINDOW seconds.
     */
    private const FILE_HIT_RATE_LIMIT = 30;
    private const FILE_HIT_RATE_WINDOW = 60;
    public $query_control;
    public function __construct()
    {
        add_action('wp_ajax_dce_file_browser_hits', array($this, 'dce_file_browser_hits'));
        add_action('wp_ajax_nopriv_dce_file_browser_hits', array($this, 'dce_file_browser_hits'));
        add_action('wp_ajax_dce_favorite_action', [$this, 'favorite_action']);
        add_action('wp_ajax_nopriv_dce_favorite_action', [$this, 'favorite_action']);
        add_action('wp_ajax_dce_clear_favorites', [$this, 'clear_favorites']);
        add_action('wp_ajax_nopriv_dce_clear_favorites', [$this, 'clear_favorites']);
        add_action('wp_ajax_dce_generate_dynamic_shortcode', [$this, 'generate_dynamic_shortcode']);
        add_action('wp_ajax_load_elementor_template_content', [$this, 'load_elementor_template_content']);
        add_action('wp_ajax_nopriv_load_elementor_template_content', [$this, 'load_elementor_template_content']);
        add_action('wp_ajax_dce_detect_date_format', [$this, 'detect_date_format']);
        add_action('wp_ajax_dce_save_filebrowser_titles', [$this, 'save_filebrowser_titles']);
        add_action('wp_ajax_dce_get_filebrowser_titles', [$this, 'get_filebrowser_titles']);
        add_action('wp_ajax_dce_save_feature_status', [$this, 'save_feature_status']);
        // Ajax Select2 autocomplete
        $this->query_control = new \DynamicContentForElementor\Modules\QueryControl\Module();
    }
    /**
     * @return void
     */
    public function generate_dynamic_shortcode()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Access denied', 403);
        }
        if (!check_ajax_referer('dce_generate_shortcode', 'nonce', \false)) {
            wp_send_json_error('Nonce verification failed', 403);
        }
        $settings = isset($_POST['settings']) ? $_POST['settings'] : null;
        if (!$settings) {
            wp_send_json_error('Settings not provided or invalid');
        }
        $result = \DynamicContentForElementor\Modules\DynamicTags\Tags\DynamicShortcodesWizard\Engine::process_settings($settings);
        if (!empty($result['error'])) {
            wp_send_json_error($result['error']);
        }
        wp_send_json_success($result['result']);
    }
    /**
     * Handle AJAX requests for favorite actions (add/remove).
     *
     * @return void
     */
    public function favorite_action()
    {
        if (!check_ajax_referer('dce_add_to_favorites', 'nonce', \false)) {
            wp_send_json_error(['message' => 'Nonce verification failed'], 403);
        }
        $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
        $list_key = isset($_POST['key']) && \is_string($_POST['key']) ? sanitize_key(wp_unslash($_POST['key'])) : '';
        $scope = isset($_POST['scope']) && \is_string($_POST['scope']) ? sanitize_key(wp_unslash($_POST['scope'])) : '';
        $action = isset($_POST['action_type']) && \is_string($_POST['action_type']) ? sanitize_key(wp_unslash($_POST['action_type'])) : '';
        $whitelist_token = isset($_POST['whitelist_token']) && \is_string($_POST['whitelist_token']) ? sanitize_text_field(wp_unslash($_POST['whitelist_token'])) : '';
        if (!$post_id || !$list_key || !$scope || !\in_array($action, ['add', 'remove'], \true)) {
            wp_send_json_error(['message' => 'Invalid request'], 400);
        }
        if (!\DynamicContentForElementor\Favorites::is_action_whitelisted($list_key, $scope, $post_id, $whitelist_token)) {
            wp_send_json_error(['message' => 'Favorites list not allowed'], 403);
        }
        $post_id = apply_filters('wpml_object_id', $post_id, get_post_type($post_id), \true);
        $expiration = \time() + 30 * DAY_IN_SECONDS;
        switch ($action) {
            case 'add':
                \DynamicContentForElementor\Favorites::add_favorite($scope, $list_key, $post_id, $expiration);
                break;
            case 'remove':
                \DynamicContentForElementor\Favorites::remove_favorite($scope, $list_key, $post_id, $expiration);
                break;
        }
        wp_send_json_success();
    }
    /**
     * Handle AJAX requests for clearing all favorites.
     *
     * @return void
     */
    public function clear_favorites()
    {
        if (!check_ajax_referer('dce_clear_favorites', 'nonce', \false)) {
            wp_send_json_error(['message' => 'Nonce verification failed'], 403);
        }
        $list_key = isset($_POST['key']) && \is_string($_POST['key']) ? sanitize_key(wp_unslash($_POST['key'])) : '';
        $scope = isset($_POST['scope']) && \is_string($_POST['scope']) ? sanitize_key(wp_unslash($_POST['scope'])) : '';
        $whitelist_token = isset($_POST['whitelist_token']) && \is_string($_POST['whitelist_token']) ? sanitize_text_field(wp_unslash($_POST['whitelist_token'])) : '';
        if (!$list_key || !$scope) {
            wp_send_json_error(['message' => 'Invalid request'], 400);
        }
        if (!\DynamicContentForElementor\Favorites::is_whitelisted($list_key, $scope, $whitelist_token)) {
            wp_send_json_error(['message' => 'Favorites list not allowed'], 403);
        }
        \DynamicContentForElementor\Favorites::clear_favorites($scope, $list_key);
        wp_send_json_success();
    }
    /**
     * Track file download hits
     *
     * @return void
     */
    public function dce_file_browser_hits()
    {
        if (!check_ajax_referer('dce_file_browser_hits', 'nonce', \false)) {
            wp_send_json_error('Nonce verification failed', 403);
        }
        $md5 = isset($_REQUEST['md5']) && \is_scalar($_REQUEST['md5']) ? sanitize_text_field(wp_unslash((string) $_REQUEST['md5'])) : '';
        if (!\preg_match('/^[a-f0-9]{32}$/i', $md5)) {
            wp_send_json_error('Invalid md5', 400);
        }
        $md5 = \strtolower($md5);
        $hits = 1;
        if (isset($_REQUEST['post_id'])) {
            $post_id = \is_scalar($_REQUEST['post_id']) ? absint(wp_unslash((string) $_REQUEST['post_id'])) : 0;
            if (!$this->file_hit_matches_attachment($post_id, $md5)) {
                wp_send_json_error('Invalid post', 400);
            }
            if (!$this->file_hit_within_rate_limit('attachment:' . $post_id . ':' . $md5)) {
                wp_send_json_error('Too many requests', 429);
            }
            $key = 'dce-file';
            $tmp = get_post_meta($post_id, $key, \true);
            $value = array('hits' => 1);
            if (!empty($tmp) && \is_array($tmp)) {
                $tmp['hits'] = isset($tmp['hits']) ? \intval($tmp['hits']) + 1 : 1;
                $value = $tmp;
            }
            update_post_meta($post_id, $key, $value);
            $hits = $value['hits'];
        } else {
            if (!$this->file_hit_within_rate_limit('file:' . $md5)) {
                wp_send_json_error('Too many requests', 429);
            }
            $key = 'dce-file-' . $md5;
            $tmp = get_option($key);
            $value = array('hits' => 1);
            if (!empty($tmp) && \is_array($tmp)) {
                $tmp['hits'] = isset($tmp['hits']) ? \intval($tmp['hits']) + 1 : 1;
                $value = $tmp;
            } elseif ($this->count_file_hit_options() >= self::MAX_FILE_HIT_OPTIONS) {
                // Do not create a new option row once the cap is reached: this
                // bounds wp_options growth from anonymous requests supplying
                // arbitrary md5 values. Existing counters keep incrementing.
                wp_send_json_error('File hit limit reached', 429);
            }
            update_option($key, $value, \false);
            $hits = $value['hits'];
        }
        wp_send_json_success(['hits' => $hits]);
    }
    /**
     * @param int $post_id
     * @param string $md5
     * @return bool
     */
    private function file_hit_matches_attachment($post_id, $md5)
    {
        if (!$post_id || 'attachment' !== get_post_type($post_id) || !\DynamicContentForElementor\Helper::can_current_user_view_post($post_id)) {
            return \false;
        }
        $attached_file = get_attached_file($post_id);
        if (!\is_string($attached_file)) {
            return \false;
        }
        $real_file = \realpath($attached_file);
        return \is_string($real_file) && \is_file($real_file) && \hash_equals(\md5($real_file), $md5);
    }
    /**
     * Per-IP throttle for the anonymous file-hit endpoint.
     *
     * @param string $file_id File identifier.
     *
     * @return bool True if the current client is within the allowed rate.
     */
    private function file_hit_within_rate_limit($file_id)
    {
        $ip = \DynamicContentForElementor\Helper::get_client_ip();
        if (!$ip) {
            return \true;
        }
        $transient = 'dce_fbh_rl_' . \md5($ip);
        $stored = get_transient($transient);
        if (\is_array($stored)) {
            $count = isset($stored['count']) ? (int) $stored['count'] : 0;
            $files = isset($stored['files']) && \is_array($stored['files']) ? $stored['files'] : [];
        } else {
            $count = (int) $stored;
            $files = [];
        }
        $file_hash = \md5($file_id);
        if ($count >= self::FILE_HIT_RATE_LIMIT || isset($files[$file_hash])) {
            return \false;
        }
        $files[$file_hash] = \true;
        set_transient($transient, ['count' => $count + 1, 'files' => $files], self::FILE_HIT_RATE_WINDOW);
        return \true;
    }
    /**
     * Count existing 'dce-file-<md5>' option rows (used to cap their number).
     *
     * @return int
     */
    private function count_file_hit_options()
    {
        global $wpdb;
        return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name LIKE 'dce-file-%'");
    }
    /**
     * @return void
     */
    public function load_elementor_template_content()
    {
        if (!check_ajax_referer('dce_load_template', 'nonce', \false)) {
            wp_send_json_error('Nonce verification failed', 403);
        }
        $template_id = isset($_REQUEST['template_id']) ? \intval($_REQUEST['template_id']) : 0;
        if (!$template_id) {
            wp_send_json_error('Template ID not set');
        }
        if (!is_post_publicly_viewable($template_id)) {
            wp_send_json_error('Template not viewable');
        }
        $post_id = isset($_REQUEST['post_id']) ? absint($_REQUEST['post_id']) : 0;
        if ($post_id && !is_post_publicly_viewable($post_id)) {
            wp_send_json_error('Post not viewable');
        }
        $template_system = \DynamicContentForElementor\Plugin::instance()->template_system;
        $content = $template_system->build_elementor_template_special(['id' => $template_id, 'post_id' => $post_id]);
        if ($content) {
            wp_send_json_success($content);
        } else {
            wp_send_json_error('Template content could not be loaded');
        }
    }
    /**
     * Auto-detect date format from a meta field
     *
     * Uses multi-sample analysis to disambiguate formats like d/m/Y vs m/d/Y
     *
     * @return void
     */
    public function detect_date_format()
    {
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => 'Permission denied'], 403);
        }
        if (!check_ajax_referer('dce-custom-editor', 'nonce', \false)) {
            wp_send_json_error(['message' => 'Nonce verification failed'], 403);
        }
        $meta_key = isset($_POST['meta_key']) ? sanitize_key($_POST['meta_key']) : '';
        // Handle post_type as both string and array (since the control is multiple)
        if (isset($_POST['post_type'])) {
            if (\is_array($_POST['post_type'])) {
                $post_type = \array_map('sanitize_key', $_POST['post_type']);
            } else {
                $post_type = sanitize_key($_POST['post_type']);
            }
        } else {
            $post_type = 'post';
        }
        if (empty($meta_key)) {
            wp_send_json_error(['message' => 'Meta key is required']);
        }
        // Fetch multiple posts for disambiguation (more samples = better accuracy)
        /** @var \WP_Post[] $posts */
        $posts = get_posts(['post_type' => $post_type, 'posts_per_page' => 20, 'meta_key' => $meta_key, 'orderby' => 'date', 'order' => 'DESC', 'post_status' => 'any', 'perm' => 'editable']);
        if (empty($posts)) {
            wp_send_json_error(['message' => 'No posts found with this meta field']);
        }
        // Collect samples from all posts
        $samples = [];
        $first_post_id = 0;
        $first_sample = '';
        foreach ($posts as $post) {
            $value = get_post_meta($post->ID, $meta_key, \true);
            if (!empty($value) && \is_string($value)) {
                $samples[] = $value;
                if ($first_post_id === 0) {
                    $first_post_id = $post->ID;
                    $first_sample = $value;
                }
            }
        }
        if (empty($samples)) {
            wp_send_json_error(['message' => 'No valid date values found']);
        }
        // Use the DateFormatDetector for intelligent format detection
        $detector = new DateFormatDetector();
        $result = $detector->detect($samples);
        if ($result['success']) {
            wp_send_json_success(['format' => $result['format'], 'sample' => $result['sample'], 'readable' => $result['readable'], 'description' => $result['description'], 'alternatives' => $result['alternatives'], 'samples_analyzed' => \count($samples), 'post_id' => $first_post_id, 'post_title' => esc_html(get_the_title($first_post_id))]);
        }
        // Fallback: could not detect format
        wp_send_json_error(['message' => $result['message'], 'sample' => $result['sample'] ?? $first_sample, 'post_id' => $first_post_id, 'post_title' => esc_html(get_the_title($first_post_id))]);
    }
    /**
     * Save custom titles for FileBrowser folders and files
     *
     * @return void
     */
    public function save_filebrowser_titles()
    {
        // Permission check - only administrators
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied'], 403);
        }
        // Nonce check
        if (!check_ajax_referer('dce-custom-editor', 'nonce', \false)) {
            wp_send_json_error(['message' => 'Nonce verification failed'], 403);
        }
        // Get and validate titles data
        $titles_json = isset($_POST['titles']) ? wp_unslash($_POST['titles']) : '';
        if (empty($titles_json)) {
            wp_send_json_error(['message' => 'No titles data provided']);
        }
        $titles = \json_decode($titles_json, \true);
        if (!\is_array($titles)) {
            wp_send_json_error(['message' => 'Invalid titles data']);
        }
        $saved_count = 0;
        // Save folder titles
        if (!empty($titles['folders']) && \is_array($titles['folders'])) {
            foreach ($titles['folders'] as $dir_id => $title) {
                // Validate dir_id format (should be safe directory identifier)
                $dir_id = sanitize_text_field($dir_id);
                if (empty($dir_id)) {
                    continue;
                }
                $option_key = 'dce-dir-' . $dir_id;
                $title = sanitize_text_field($title);
                if (!empty($title)) {
                    // Get existing option data or create new
                    $existing = get_option($option_key, []);
                    if (!\is_array($existing)) {
                        $existing = [];
                    }
                    $existing['title'] = $title;
                    update_option($option_key, $existing, \false);
                    ++$saved_count;
                } else {
                    // Empty title - remove custom title but keep other data
                    $existing = get_option($option_key, []);
                    if (\is_array($existing) && isset($existing['title'])) {
                        unset($existing['title']);
                        if (empty($existing)) {
                            delete_option($option_key);
                        } else {
                            update_option($option_key, $existing, \false);
                        }
                    }
                }
            }
        }
        // Save file titles
        if (!empty($titles['files']) && \is_array($titles['files'])) {
            foreach ($titles['files'] as $md5 => $title) {
                // Validate md5 format (32 hex characters)
                if (!\preg_match('/^[a-f0-9]{32}$/i', $md5)) {
                    continue;
                }
                $option_key = 'dce-file-' . $md5;
                $title = sanitize_text_field($title);
                if (!empty($title)) {
                    // Get existing option data or create new
                    $existing = get_option($option_key, []);
                    if (!\is_array($existing)) {
                        $existing = [];
                    }
                    $existing['title'] = $title;
                    update_option($option_key, $existing, \false);
                    ++$saved_count;
                } else {
                    // Empty title - remove custom title but keep other data
                    $existing = get_option($option_key, []);
                    if (\is_array($existing) && isset($existing['title'])) {
                        unset($existing['title']);
                        if (empty($existing)) {
                            delete_option($option_key);
                        } else {
                            update_option($option_key, $existing, \false);
                        }
                    }
                }
            }
        }
        wp_send_json_success(['message' => \sprintf('%d title(s) saved', $saved_count), 'count' => $saved_count]);
    }
    /**
     * AJAX handler for saving feature status from the features page.
     *
     * @return void
     */
    public function save_feature_status()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Access denied', 403);
        }
        check_ajax_referer('dce_features_nonce', 'nonce');
        $features = isset($_POST['features']) ? \array_map('sanitize_text_field', wp_unslash($_POST['features'])) : [];
        if (empty($features)) {
            wp_send_json_error('No features provided');
        }
        $valid_feature_keys = \array_keys(\DynamicContentForElementor\Plugin::instance()->features->all_features);
        $sanitized = [];
        foreach ($features as $name => $status) {
            $name = sanitize_text_field($name);
            if (!\in_array($name, $valid_feature_keys, \true)) {
                continue;
            }
            if (!\in_array($status, ['active', 'inactive'], \true)) {
                wp_send_json_error('Invalid status value');
            }
            $sanitized[$name] = $status;
        }
        if (empty($sanitized)) {
            wp_send_json_error('No valid features provided');
        }
        \DynamicContentForElementor\Plugin::instance()->features->db_update_features_status($sanitized);
        wp_send_json_success();
    }
    /**
     * Get existing custom titles for FileBrowser folders and files
     *
     * @return void
     */
    public function get_filebrowser_titles()
    {
        // Permission check
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied'], 403);
        }
        // Nonce check
        if (!check_ajax_referer('dce-custom-editor', 'nonce', \false)) {
            wp_send_json_error(['message' => 'Nonce verification failed'], 403);
        }
        // Get IDs from request
        $ids_json = isset($_POST['ids']) ? wp_unslash($_POST['ids']) : '';
        if (empty($ids_json)) {
            wp_send_json_error(['message' => 'No IDs provided']);
        }
        $ids = \json_decode($ids_json, \true);
        if (!\is_array($ids)) {
            wp_send_json_error(['message' => 'Invalid IDs data']);
        }
        $titles = ['folders' => [], 'files' => []];
        // Get folder titles
        if (!empty($ids['folders']) && \is_array($ids['folders'])) {
            foreach ($ids['folders'] as $dir_id) {
                $dir_id = sanitize_text_field($dir_id);
                if (empty($dir_id)) {
                    continue;
                }
                $option = get_option('dce-dir-' . $dir_id, []);
                if (\is_array($option) && !empty($option['title'])) {
                    $titles['folders'][$dir_id] = $option['title'];
                }
            }
        }
        // Get file titles
        if (!empty($ids['files']) && \is_array($ids['files'])) {
            foreach ($ids['files'] as $md5) {
                // Validate md5 format
                if (!\preg_match('/^[a-f0-9]{32}$/i', $md5)) {
                    continue;
                }
                $option = get_option('dce-file-' . $md5, []);
                if (\is_array($option) && !empty($option['title'])) {
                    $titles['files'][$md5] = $option['title'];
                }
            }
        }
        wp_send_json_success($titles);
    }
}
