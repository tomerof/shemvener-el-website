<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\AdminPages\Features;

use DynamicContentForElementor\AdminPages\Settings;
use DynamicContentForElementor\Tokens;
use DynamicOOO\PluginUtils\AdminPages\Pages\Base;
class TokensSettings extends Settings\SettingsPage
{
    const PAGE_ID = 'dce-settings';
    /**
     * Constructor - register AJAX action and hooks
     */
    public function __construct()
    {
        parent::__construct();
        add_action('wp_ajax_dce_clear_detected_tokens', [$this, 'ajax_clear_detected_tokens']);
        add_action('update_option_dce_tokens_status', [$this, 'maybe_clear_detected_tokens'], 10, 2);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_detector_scripts']);
        add_action('admin_init', [$this, 'register_detector_setting']);
    }
    /**
     * Register detector setting explicitly
     *
     * @return void
     */
    public function register_detector_setting()
    {
        register_setting('dce-tokens-detector', 'dce_tokens_detector_status', ['type' => 'string', 'default' => 'disable', 'sanitize_callback' => function ($value) {
            return \in_array($value, ['enable', 'disable'], \true) ? $value : 'disable';
        }]);
    }
    /**
     * Enqueue scripts for Tokens Detector page
     *
     * @param string $hook Current admin page hook
     * @return void
     */
    public function enqueue_detector_scripts($hook)
    {
        // Check if we're on a DCE admin page
        if (\strpos($hook, 'dce') === \false) {
            return;
        }
        // Check if we're on the detector subtab
        $active_tab = isset($_GET['subtab']) ? sanitize_text_field($_GET['subtab']) : '';
        if ($active_tab !== 'detector') {
            return;
        }
        wp_enqueue_script('dce-tokens-detector', DCE_URL . 'assets/js/tokens-detector.js', ['jquery'], DCE_VERSION, \true);
        wp_localize_script('dce-tokens-detector', 'dceTokensDetector', ['ajaxUrl' => admin_url('admin-ajax.php'), 'confirmMessage' => __('Are you sure you want to clear all detected tokens? This action cannot be undone.', 'dynamic-content-for-elementor'), 'clearingText' => __('Clearing...', 'dynamic-content-for-elementor'), 'errorMessage' => __('Failed to clear tokens.', 'dynamic-content-for-elementor'), 'ajaxErrorMessage' => __('An error occurred. Please try again.', 'dynamic-content-for-elementor')]);
    }
    /**
     * Fix old filter whitelist bug
     *
     * @return void
     */
    public function before_register()
    {
        if (\is_array(get_option('dce_tokens_filters_whitelist'))) {
            Tokens::fix_filters_whitelist();
        }
    }
    /**
     * Get Name
     *
     * @return string
     */
    public function get_name()
    {
        return 'tokens';
    }
    /**
     * Get Label
     *
     * @return string
     */
    public function get_label()
    {
        return 'Tokens ' . esc_html__('(Deprecated)', 'dynamic-content-for-elementor');
    }
    /**
     * Should Display Count
     *
     * @return boolean
     */
    public function should_display_count()
    {
        return \false;
    }
    /**
     * @param string $id
     * @return string
     */
    protected function tokens_filters_whitelist($id)
    {
        $value = esc_textarea(get_option('dce_' . $id, ''));
        $html = "<textarea placeholder='my_function' cols='30' rows='5' id='dce_{$id}' name='dce_{$id}'>{$value}</textarea>";
        $html .= '<p class="description">' . esc_html__('One filter per line', 'dynamic-content-for-elementor') . '</p>';
        return $html;
    }
    /**
     * @return void
     */
    protected function render_tokens_intro()
    {
        $notice = \DynamicContentForElementor\Plugin::instance()->text_templates->get_notice_content();
        if (empty($notice)) {
            return;
        }
        if ('dynamic_shortcodes_only' === $notice['case'] || 'none' === $notice['case']) {
            echo '<p><strong>' . esc_html__('Tokens is now deprecated. Do not activate unless strictly necessary for backward compatibility.', 'dynamic-content-for-elementor') . '</strong></p>';
        } else {
            echo '<p><strong>' . wp_kses_post($notice['content']) . '</strong></p>';
        }
    }
    /**
     * Render the entire detector section (intro + table)
     *
     * @return void
     */
    protected function render_detector_section()
    {
        // Check if detector is enabled
        $is_disabled = !Tokens::is_detector_enabled();
        // Check if limit has been reached
        $limit_reached = get_option('dce_tokens_detector_limit_reached', \false);
        // Render enable/disable field manually at the top
        echo '<table class="form-table" role="presentation">';
        echo '<tbody>';
        echo '<tr>';
        echo '<th scope="row"><label for="dce_tokens_detector_status">' . esc_html__('Tokens Detector Status', 'dynamic-content-for-elementor') . '</label></th>';
        echo '<td>';
        echo '<select id="dce_tokens_detector_status" name="dce_tokens_detector_status">';
        $detector_enabled = Tokens::is_detector_enabled();
        echo '<option value="enable"' . selected($detector_enabled, \true, \false) . '>' . esc_html__('Enable', 'dynamic-content-for-elementor') . '</option>';
        echo '<option value="disable"' . selected($detector_enabled, \false, \false) . '>' . esc_html__('Disable', 'dynamic-content-for-elementor') . '</option>';
        echo '</select>';
        echo '<p class="description">' . esc_html__('Track which tokens are being used on your site. Disable to stop tracking.', 'dynamic-content-for-elementor') . '</p>';
        echo '</td>';
        echo '</tr>';
        echo '</tbody>';
        echo '</table>';
        // Save button
        submit_button();
        // Info section
        echo '<div style="margin: 15px 0 20px 0; line-height: 1.6;">';
        if (!$is_disabled) {
            // Show limit reached warning if applicable
            if ($limit_reached) {
                echo '<div class="notice notice-warning inline" style="margin: 15px 0;"><p>';
                echo '<strong>' . esc_html__('Maximum tracking limit reached (150 items).', 'dynamic-content-for-elementor') . '</strong><br>';
                echo esc_html__('New tokens will not be tracked until you clear some existing items or clear the entire list. This limit helps maintain site performance.', 'dynamic-content-for-elementor');
                echo '</p></div>';
            }
            echo '<p style="margin-bottom: 8px;">' . esc_html__('This detector automatically tracks which tokens are being used on your site to help you identify what needs to be migrated to Dynamic Shortcodes.', 'dynamic-content-for-elementor') . '</p>';
            echo '<p style="margin-bottom: 8px;"><strong>' . esc_html__('How it works:', 'dynamic-content-for-elementor') . '</strong> ';
            echo esc_html__('Tokens are detected only when pages containing them are visited. For accurate results, you may need to wait several weeks as visitors browse your site, or manually visit all pages that might contain tokens.', 'dynamic-content-for-elementor');
            echo '</p>';
            echo '<p style="margin-bottom: 8px; padding: 8px 12px; background: #f0f6fc; border-left: 3px solid #2271b1;"><strong>' . esc_html__('Performance optimization:', 'dynamic-content-for-elementor') . '</strong> ';
            echo esc_html__('To keep your site fast, the detector limits tracking to 150 unique items and updates timestamps every 5 minutes instead of on every page load. All unique token content will still be detected over time.', 'dynamic-content-for-elementor');
            echo '</p>';
            echo '<p><a href="https://help.dynamic.ooo/en/articles/9287082-how-to-migrate-from-tokens-to-dynamic-shortcodes" target="_blank">' . esc_html__('Learn how to migrate from Tokens to Dynamic Shortcodes', 'dynamic-content-for-elementor') . ' &rarr;</a></p>';
            echo '</div>';
            // Render table
            echo $this->render_tokens_detector_table('tokens_detector_table');
        } else {
            echo '</div>';
        }
    }
    /**
     * Clear detected tokens when tokens are disabled
     *
     * @param mixed $old_value Old option value
     * @param mixed $new_value New option value
     * @return void
     */
    public function maybe_clear_detected_tokens($old_value, $new_value)
    {
        // When tokens status changes to disable, clear detected tokens
        if ($new_value === 'disable') {
            delete_option('dce_tokens_detected');
            delete_option('dce_tokens_detector_limit_reached');
        }
    }
    /**
     * AJAX handler to clear detected tokens
     *
     * @return void
     */
    public function ajax_clear_detected_tokens()
    {
        // Check nonce
        check_ajax_referer('dce_clear_detected_tokens', 'nonce');
        // Check user capabilities
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('You do not have sufficient permissions.', 'dynamic-content-for-elementor')]);
        }
        // Clear the options
        delete_option('dce_tokens_detected');
        delete_option('dce_tokens_detector_limit_reached');
        // Verify the option doesn't exist anymore (success either way)
        $option_exists = get_option('dce_tokens_detected', \false) !== \false;
        if (!$option_exists) {
            wp_send_json_success(['message' => __('Detected tokens list cleared successfully.', 'dynamic-content-for-elementor')]);
        } else {
            wp_send_json_error(['message' => __('Failed to clear detected tokens.', 'dynamic-content-for-elementor')]);
        }
    }
    /**
     * Render table header
     *
     * @return string
     */
    private function render_table_header()
    {
        $columns = ['content' => ['label' => __('Content', 'dynamic-content-for-elementor'), 'width' => '40%'], 'first_seen' => ['label' => __('First Seen', 'dynamic-content-for-elementor'), 'width' => '15%'], 'last_seen' => ['label' => __('Last Seen', 'dynamic-content-for-elementor'), 'width' => '15%'], 'pages' => ['label' => __('Pages', 'dynamic-content-for-elementor'), 'width' => '30%']];
        $html = '<thead><tr>';
        foreach ($columns as $column) {
            $html .= \sprintf('<th style="width: %s; padding: 8px 10px;">%s</th>', esc_attr($column['width']), esc_html($column['label']));
        }
        $html .= '</tr></thead>';
        return $html;
    }
    /**
     * Render empty state row
     *
     * @return string
     */
    private function render_empty_state()
    {
        $is_tokens_active = \DynamicContentForElementor\Tokens::is_active();
        if ($is_tokens_active) {
            $message = esc_html__('No deprecated tokens detected on visited pages. If you have tokens in your content, visit those pages to track them here.', 'dynamic-content-for-elementor');
        } else {
            $message = \sprintf(
                /* translators: %s: Link to Tokens tab */
                esc_html__('Tokens are currently disabled. To detect where they are used, temporarily enable them in the %s.', 'dynamic-content-for-elementor'),
                '<a href="' . esc_url(add_query_arg('subtab', 'tokens')) . '">' . esc_html__('Tokens tab', 'dynamic-content-for-elementor') . '</a>'
            );
        }
        return \sprintf('<tr><td colspan="4" style="text-align: center; padding: 40px; color: #666;">
				<span class="dashicons dashicons-search" style="font-size: 48px; opacity: 0.3; display: block; margin: 0 auto 10px auto;"></span>
				<strong style="display: block; margin-bottom: 8px; font-size: 14px;">%s</strong>
				<small style="color: #999;">%s</small>
			</td></tr>', esc_html__('No tokens detected yet', 'dynamic-content-for-elementor'), $message);
    }
    /**
     * Render pages list for a token
     *
     * @param array<mixed> $pages
     * @return string
     */
    private function render_token_pages($pages)
    {
        if (empty($pages)) {
            return '';
        }
        $page_count = \count($pages);
        $summary = \sprintf(esc_html__('%d page(s)', 'dynamic-content-for-elementor'), $page_count);
        $pages_list = '<ul style="margin: 5px 0 0 20px;">';
        foreach ($pages as $page_data) {
            // Get post_id from the data
            $post_id = !empty($page_data['post_id']) ? absint($page_data['post_id']) : 0;
            if (!$post_id) {
                continue;
                // Skip if no post_id
            }
            // Get current post data dynamically
            $page_title = get_the_title($post_id);
            if (empty($page_title)) {
                $page_title = __('Untitled', 'dynamic-content-for-elementor');
            }
            // Get edit link
            $edit_link = get_edit_post_link($post_id);
            if (!$edit_link) {
                continue;
                // Skip if post doesn't exist or user can't edit
            }
            // Get post type label
            $post_type = $page_data['post_type'] ?? get_post_type($post_id);
            $post_type_obj = get_post_type_object($post_type);
            $post_type_label = $post_type_obj ? $post_type_obj->labels->singular_name : __('Post', 'dynamic-content-for-elementor');
            $link_title = \sprintf(
                /* translators: %s: Post type singular name */
                __('Edit %s', 'dynamic-content-for-elementor'),
                $post_type_label
            );
            $pages_list .= \sprintf('<li>
					<a href="%s" target="_blank" title="%s">
						<span class="dashicons dashicons-edit" style="font-size: 14px; vertical-align: middle;"></span> %s
					</a>
				</li>', esc_url($edit_link), esc_attr($link_title), esc_html($page_title));
        }
        $pages_list .= '</ul>';
        return \sprintf('<details><summary style="cursor: pointer;">%s</summary>%s</details>', $summary, $pages_list);
    }
    /**
     * Format timestamp for display
     *
     * @param string $mysql_date MySQL datetime string
     * @return string Formatted date with timezone
     */
    private function format_datetime($mysql_date)
    {
        if (empty($mysql_date)) {
            return '-';
        }
        // Convert to timestamp and format according to WP settings
        $timestamp = \strtotime($mysql_date);
        // Handle malformed date strings
        if ($timestamp === \false) {
            return '-';
        }
        $date_format = get_option('date_format');
        $time_format = get_option('time_format');
        return wp_date($date_format . ' ' . $time_format, $timestamp);
    }
    /**
     * Render a single token row
     *
     * @param array<mixed> $token_data
     * @return string
     */
    private function render_token_row($token_data)
    {
        $content = $token_data['content'] ?? '';
        $is_truncated = $token_data['is_truncated'] ?? \false;
        // Add truncated badge if content was truncated
        $truncated_badge = '';
        if ($is_truncated) {
            $truncated_badge = \sprintf('<span style="display: inline-block; margin-left: 8px; padding: 2px 8px; background: #f0ad4e; color: #fff; font-size: 11px; border-radius: 3px; font-weight: 600;" title="%s">%s</span>', esc_attr__('Content was truncated to 200 characters for storage optimization.', 'dynamic-content-for-elementor'), esc_html__('TRUNCATED', 'dynamic-content-for-elementor'));
        }
        // Render content in a simple container (200 chars max, ~2-3 lines)
        $content_html = \sprintf('<div style="max-width: 100%%; word-wrap: break-word; padding: 8px; background: #f6f7f7; border-radius: 3px;">
				<code style="font-size: 12px; white-space: pre-wrap;">%s</code>%s
			</div>', esc_html($content), $truncated_badge);
        return \sprintf('<tr>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
			</tr>', $content_html, esc_html($this->format_datetime($token_data['first_seen'] ?? '')), esc_html($this->format_datetime($token_data['last_seen'] ?? '')), $this->render_token_pages($token_data['pages'] ?? []));
    }
    /**
     * Render clear button
     *
     * @return string
     */
    private function render_clear_button()
    {
        $nonce = wp_create_nonce('dce_clear_detected_tokens');
        return \sprintf('<div style="margin-top: 20px;">
				<button type="button" id="dce-clear-tokens-btn" class="button button-secondary" data-nonce="%s">%s</button>
			</div>', esc_attr($nonce), esc_html__('Clear All Detected Tokens', 'dynamic-content-for-elementor'));
    }
    /**
     * Render tokens detector table
     *
     * @param string $id
     * @return string
     */
    protected function render_tokens_detector_table($id)
    {
        $detected_tokens = get_option('dce_tokens_detected', []);
        // Ensure we have a valid array to prevent TypeError on corrupted data
        if (!\is_array($detected_tokens)) {
            $detected_tokens = [];
        }
        $has_tokens = !empty($detected_tokens);
        // Build table
        $html = '<div class="dce-tokens-detector-container">';
        // Summary message
        if ($has_tokens) {
            $html .= \sprintf('<p class="description">%s</p>', \sprintf(
                /* translators: %d: number of unique content pieces containing tokens */
                esc_html__('Unique content pieces containing tokens: %d (each may contain multiple tokens)', 'dynamic-content-for-elementor'),
                \count($detected_tokens)
            ));
        }
        // Table structure
        $html .= '<table class="wp-list-table widefat fixed striped">';
        $html .= $this->render_table_header();
        $html .= '<tbody>';
        if ($has_tokens) {
            // Sort by last seen (most recent first)
            \uasort($detected_tokens, function ($a, $b) {
                return \strcmp($b['last_seen'] ?? '', $a['last_seen'] ?? '');
            });
            foreach ($detected_tokens as $token_data) {
                $html .= $this->render_token_row($token_data);
            }
        } else {
            $html .= $this->render_empty_state();
        }
        $html .= '</tbody></table>';
        // Clear button
        if ($has_tokens) {
            $html .= $this->render_clear_button();
        }
        $html .= '</div>';
        return $html;
    }
    /**
     * Override render to add tab navigation
     *
     * @return void
     */
    public function render()
    {
        $this->before_register();
        $this->register_settings_fields();
        $tabs = $this->get_tabs();
        // Get active tab from URL or use first tab
        $active_tab_id = isset($_GET['subtab']) ? sanitize_text_field($_GET['subtab']) : \array_key_first($tabs);
        if (!isset($tabs[$active_tab_id])) {
            $active_tab_id = \array_key_first($tabs);
        }
        ?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php 
        echo esc_html($this->get_label());
        ?></h1>

			<?php 
        if (\count($tabs) > 1) {
            ?>
			<nav class="nav-tab-wrapper wp-clearfix">
				<?php 
            foreach ($tabs as $tab_id => $tab) {
                ?>
					<?php 
                $tab_url = add_query_arg('subtab', $tab_id);
                $active_class = $active_tab_id === $tab_id ? ' nav-tab-active' : '';
                ?>
					<a href="<?php 
                echo esc_url($tab_url);
                ?>" class="nav-tab<?php 
                echo esc_attr($active_class);
                ?>">
						<?php 
                echo esc_html($tab['label']);
                ?>
					</a>
				<?php 
            }
            ?>
			</nav>
			<?php 
        }
        ?>

			<form id="elementor-settings-form" method="post" action="options.php">
				<?php 
        $settings_group = $active_tab_id === 'detector' ? 'dce-tokens-detector' : static::PAGE_ID;
        settings_fields($settings_group);
        foreach ($tabs as $tab_id => $tab) {
            if ($active_tab_id !== $tab_id) {
                continue;
                // Only render active tab
            }
            echo '<div id="tab-' . esc_attr($tab_id) . '" class="elementor-settings-form-page elementor-active">';
            foreach ($tab['sections'] as $section_id => $section) {
                $full_section_id = 'dce_' . $section_id . '_section';
                if (!empty($section['label'])) {
                    echo '<h2>' . esc_html($section['label']) . '</h2>';
                }
                if (!empty($section['callback'])) {
                    $section['callback']();
                }
                echo '<table class="form-table">';
                do_settings_fields(static::PAGE_ID, $full_section_id);
                echo '</table>';
            }
            echo '</div>';
        }
        // Only show submit button for tabs with settings (not for detector tab)
        if ($active_tab_id !== 'detector') {
            submit_button();
        }
        ?>
			</form>
		</div><!-- /.wrap -->
		<?php 
    }
    /**
     * @return array<string,mixed>
     */
    public function create_tabs()
    {
        $default_status = Tokens::status_with_unsaved_option();
        $tabs = ['tokens' => ['label' => esc_html__('Tokens', 'dynamic-content-for-elementor'), 'sections' => ['tokens' => ['callback' => [$this, 'render_tokens_intro'], 'fields' => ['tokens_status' => ['label' => esc_html__('Tokens Status', 'dynamic-content-for-elementor'), 'field_args' => ['type' => 'select', 'std' => $default_status, 'options' => ['enable' => esc_html__('Enable', 'dynamic-content-for-elementor'), 'disable' => esc_html__('Disable', 'dynamic-content-for-elementor')]]], 'active_tokens' => ['label' => esc_html__('Active Tokens', 'dynamic-content-for-elementor'), 'field_args' => ['type' => 'checkbox_list', 'std' => \array_keys(Tokens::get_tokens_list()), 'options' => Tokens::get_tokens_options()]], 'tokens_filters_whitelist' => ['label' => esc_html__('Filters Whitelist', 'dynamic-content-for-elementor'), 'field_args' => ['type' => 'raw_html', 'html' => $this->tokens_filters_whitelist('tokens_filters_whitelist')]]]]]]];
        // Only show Tokens Detector tab if tokens are actually enabled (not just default)
        if (Tokens::is_active()) {
            $tabs['detector'] = ['label' => esc_html__('Tokens Detector', 'dynamic-content-for-elementor'), 'sections' => ['detector' => ['callback' => [$this, 'render_detector_section'], 'fields' => []]]];
        }
        return $tabs;
    }
}
