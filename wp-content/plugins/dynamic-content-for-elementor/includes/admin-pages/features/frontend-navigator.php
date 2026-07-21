<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\AdminPages\Features;

class FrontendNavigator
{
    /**
     * Get Name
     *
     * @return string
     */
    public function get_name()
    {
        return 'frontend-navigator';
    }
    /**
     * Get Label
     *
     * @return string
     */
    public function get_label()
    {
        return esc_html__('Frontend Navigator', 'dynamic-content-for-elementor');
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
     * Render
     *
     * @return void
     */
    public function render()
    {
        ?>
		<div class="dce-notice dce-notice-info" style="padding: 20px; background: #f0f6fc; border-left: 4px solid #2271b1; margin: 20px 0;">
			<h3 style="margin-top: 0;">
				<span class="dashicons dashicons-info" style="color: #2271b1;"></span>
				<?php 
        esc_html_e('Frontend Navigator is now Dynamic Inspector for Elementor', 'dynamic-content-for-elementor');
        ?>
			</h3>
			<p>
				<?php 
        esc_html_e('We\'ve extracted and enhanced this feature into a standalone, free plugin available on WordPress.org.', 'dynamic-content-for-elementor');
        ?>
			</p>
			<h4><?php 
        esc_html_e('What\'s New in Dynamic Inspector:', 'dynamic-content-for-elementor');
        ?></h4>
			<ul style="list-style: disc; margin-left: 20px;">
				<li><?php 
        esc_html_e('Element Tree Navigation with hover-to-highlight', 'dynamic-content-for-elementor');
        ?></li>
				<li><?php 
        esc_html_e('Performance Profiling with execution times', 'dynamic-content-for-elementor');
        ?></li>
				<li><?php 
        esc_html_e('Dynamic Visibility inspection', 'dynamic-content-for-elementor');
        ?></li>
				<li><?php 
        esc_html_e('Free and works with any Elementor site', 'dynamic-content-for-elementor');
        ?></li>
			</ul>
			<p>
				<a href="https://wordpress.org/plugins/dynamic-inspector-for-elementor/" target="_blank" class="button button-primary">
					<?php 
        esc_html_e('Download from WordPress.org', 'dynamic-content-for-elementor');
        ?>
				</a>
				<a href="https://www.dynamic.ooo/dynamic-inspector-for-elementor/?utm_source=dce-plugin&utm_medium=link&utm_campaign=frontend-navigator" target="_blank" class="button" style="margin-left: 10px;">
					<?php 
        esc_html_e('Learn More', 'dynamic-content-for-elementor');
        ?>
				</a>
			</p>
		</div>
		<?php 
    }
}
