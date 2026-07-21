<?php

namespace DynamicOOO\PluginUtils\AdminPages;

use DynamicOOO\PluginUtils;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly.
}
class AdminNotices
{
    /**
     * @var \DynamicOOO\PluginUtils\Manager
     */
    protected $plugin_utils_manager;
    /**
     * @var string
     */
    protected $meta_prefix;
    /**
     * @var array<int,array{id:string,message:string,class:string,dismissible:bool}>
     */
    protected $notices = [];
    /**
     * @var bool
     */
    protected $notices_completed = \false;
    /**
     * @param \DynamicOOO\PluginUtils\Manager $plugin_utils_manager
     */
    public function __construct($plugin_utils_manager)
    {
        $this->plugin_utils_manager = $plugin_utils_manager;
        $this->meta_prefix = $this->plugin_utils_manager->get_config('prefix') . '_dismissed_';
        add_action('admin_notices', [$this, 'display_notices']);
        add_action('wp_ajax_' . $this->plugin_utils_manager->get_config('prefix') . '_dismiss_notice', [$this, 'ajax_dismiss_notice']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
    }
    /**
     * @return void
     */
    public function enqueue_scripts()
    {
        $handle = $this->plugin_utils_manager->get_config('prefix') . '-admin-notices';
        wp_enqueue_script($handle, $this->plugin_utils_manager->assets->get_assets_url('/js/admin-notices.js'), ['jquery', 'wp-util'], $this->plugin_utils_manager->get_config('version'), \true);
        wp_localize_script($handle, 'AdminNotices', ['pluginPrefix' => $this->plugin_utils_manager->get_config('prefix'), 'nonce' => wp_create_nonce('dismiss_admin_notice')]);
    }
    /**
     * Check if a notice has been dismissed by the current user
     *
     * @param string $notice_id The notice ID to check
     * @return bool
     */
    protected function is_notice_dismissed($notice_id)
    {
        $meta_value = get_user_meta(get_current_user_id(), $this->meta_prefix . $notice_id, \true);
        return !empty($meta_value);
    }
    /**
     * Adds a notice or renders it immediately if notices are already displayed.
     *
     * @param string $id Unique identifier for the notice
     * @param string $message The notice message
     * @param string $class CSS class for the notice
     * @param bool $dismissible Whether the notice is dismissible
     * @return void
     */
    public function add_notice($id, $message, $class = 'updated', $dismissible = \false)
    {
        if ($dismissible && $this->is_notice_dismissed($id)) {
            return;
        }
        if ($this->notices_completed) {
            $this->render_notice($message, $class, $dismissible, $id);
        } else {
            $this->notices[] = ['id' => $id, 'message' => $message, 'class' => $class, 'dismissible' => $dismissible];
        }
    }
    /**
     * Renders a single notice.
     *
     * @param string $message The notice message
     * @param string $class CSS class for the notice
     * @param bool $dismissible Whether the notice is dismissible
     * @param string $notice_id ID for dismissible notices
     * @return void
     */
    protected function render_notice($message, $class = 'updated', $dismissible = \false, $notice_id = '')
    {
        $plugin_prefix = $this->plugin_utils_manager->get_config('prefix');
        $product_name_long = $this->plugin_utils_manager->get_config('product_name_long');
        $logo_url = $this->plugin_utils_manager->assets->get_logo_url();
        self::print_notice($message, $class . ' ' . esc_attr($plugin_prefix), $product_name_long, $dismissible, $notice_id, $logo_url);
    }
    /**
     * Print a notice in the admin panel
     *
     * @param string $message The message to display in the notice
     * @param string $class The CSS class for the notice (e.g., 'updated', 'notice-error', 'notice-warning')
     * @param string $product_name_long The product name to display in the notice header
     * @param bool $dismissible Whether the notice can be dismissed by the user
     * @param string $notice_id The unique identifier for the notice (required if dismissible is true)
     * @param string $logo_url URL to the logo image to display in the notice
     *
     * @return void
     */
    public static function print_notice($message, $class = 'updated', $product_name_long = 'Dynamic.ooo', $dismissible = \false, $notice_id = '', $logo_url = '')
    {
        $data_attr = $dismissible ? ' data-notice-id="' . esc_attr($notice_id) . '"' : '';
        $final_class = $class . ($dismissible ? ' is-dismissible' : '');
        echo '<div class="notice ooo-notice ' . esc_attr($final_class) . '"' . $data_attr . '>';
        if (!empty($logo_url)) {
            echo '<div class="img-responsive pull-left">';
            echo '<img class="ooo-logo" src="' . esc_url($logo_url) . '" title="' . esc_attr($product_name_long) . '">';
            echo '</div>';
        }
        echo '<p><strong>' . esc_html($product_name_long) . '</strong><br>' . wp_kses_post($message) . '</p>';
        echo '</div>';
    }
    /**
     * Displays the notices that have not been dismissed.
     *
     * @return void
     */
    public function display_notices()
    {
        foreach ($this->notices as $notice) {
            $this->render_notice($notice['message'], $notice['class'], $notice['dismissible'], $notice['id']);
        }
        $this->notices_completed = \true;
    }
    /**
     * Handles the AJAX request to dismiss a notice.
     *
     * Updates the current user's meta to mark the notice as dismissed.
     *
     * @return void
     */
    public function ajax_dismiss_notice()
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        if (!check_ajax_referer('dismiss_admin_notice', 'nonce', \false)) {
            wp_send_json_error('Nonce verification failed', 403);
        }
        if (empty($_POST['notice_id'])) {
            wp_send_json_error('Missing notice id');
        }
        $notice_id = sanitize_text_field($_POST['notice_id']);
        $result = update_user_meta(get_current_user_id(), $this->meta_prefix . $notice_id, \true);
        if ($result) {
            wp_send_json_success(\true);
        } else {
            wp_send_json_error('Failed to update user meta');
        }
    }
    /**
     * Displays a success notice.
     *
     * @param string $message The notice message
     * @param string $id Optional ID for dismissible notices
     * @return void
     */
    public function success($message, $id = '')
    {
        $this->add_notice($id, $message, 'notice-success', !empty($id));
    }
    /**
     * Displays an error notice.
     *
     * @param string $message The notice message
     * @param string $id Optional ID for dismissible notices
     * @return void
     */
    public function error($message, $id = '')
    {
        $this->add_notice($id, $message, 'notice-error', !empty($id));
    }
    /**
     * Displays a warning notice.
     *
     * @param string $message The notice message
     * @param string $id Optional ID for dismissible notices
     * @return void
     */
    public function warning($message, $id = '')
    {
        $this->add_notice($id, $message, 'notice-warning', !empty($id));
    }
    /**
     * Displays an info notice.
     *
     * @param string $message The notice message
     * @param string $id Optional ID for dismissible notices
     * @return void
     */
    public function info($message, $id = '')
    {
        $this->add_notice($id, $message, 'notice-info', !empty($id));
    }
    /**
     * Purge the notices
     *
     * @return void
     */
    public function purge_notices()
    {
        $this->notices = [];
    }
}
