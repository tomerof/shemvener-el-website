<?php

namespace DynamicOOO\PluginUtils\AdminPages\Pages;

if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly.
}
abstract class Base
{
    /**
     * @var \DynamicOOO\PluginUtils\Manager
     */
    protected $plugin_utils_manager;
    /**
     * Constructor
     *
     * @param \DynamicOOO\PluginUtils\Manager $plugin_utils_manager Plugin utils manager instance
     */
    public function __construct($plugin_utils_manager)
    {
        $this->plugin_utils_manager = $plugin_utils_manager;
        $this->init();
        add_action('admin_enqueue_scripts', [$this, 'enqueue_styles']);
    }
    /**
     * @return void
     */
    public function init()
    {
    }
    /**
     * Render page header
     *
     * @return void
     */
    protected function render_header()
    {
        ?>
		<div class="wrap">
			<div class="ooo-admin-header-wrap">
				<div class="ooo-admin-header">
					<img src="<?php 
        echo esc_url($this->plugin_utils_manager->assets->get_logo_url_svg());
        ?>" 
						 alt="<?php 
        echo esc_attr($this->plugin_utils_manager->get_config('product_name'));
        ?>" 
						 class="ooo-admin-logo"
						 width="200"
						 height="40">
				</div>
			</div>
		<?php 
    }
    /**
     * Render page footer
     *
     * @return void
     */
    protected function render_footer()
    {
        ?>
		</div>
		<?php 
    }
    /**
     * Render page content
     *
     * @return void
     */
    protected abstract function render_content();
    /**
     * Render complete page
     *
     * @return void
     */
    public function render_page()
    {
        $this->render_header();
        ?>
		<h1><?php 
        echo esc_html(get_admin_page_title());
        ?></h1>
		<?php 
        $this->render_content();
        $this->render_footer();
    }
    /**
     * @return void
     */
    public function enqueue_styles()
    {
        wp_enqueue_style($this->plugin_utils_manager->get_config('prefix') . '-admin-pages', $this->plugin_utils_manager->assets->get_assets_url('css/admin-style.css'), [], $this->plugin_utils_manager->get_config('version'));
    }
}
