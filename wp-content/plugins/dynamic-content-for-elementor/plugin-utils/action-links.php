<?php

namespace DynamicOOO\PluginUtils;

class ActionLinks
{
    /**
     * @var Manager
     */
    protected $plugin_utils_manager;
    /**
     * @param Manager $plugin_utils_manager
     */
    public function __construct($plugin_utils_manager)
    {
        $this->plugin_utils_manager = $plugin_utils_manager;
        add_filter('plugin_row_meta', [$this, 'add_utilities'], 10, 2);
        add_filter('plugin_action_links_' . $this->plugin_utils_manager->get_config('plugin_base'), [$this, 'add_links_from_plugin']);
        if (!$this->plugin_utils_manager->license->is_license_active()) {
            add_filter('plugin_action_links_' . $this->plugin_utils_manager->get_config('plugin_base'), [$this, 'add_license']);
        }
    }
    /**
     * @param array<string,string> $plugin_meta
     * @param string $plugin_file
     * @return array<string,string>
     */
    public function add_utilities($plugin_meta, $plugin_file)
    {
        if ($this->plugin_utils_manager->get_config('plugin_slug') . '/' . $this->plugin_utils_manager->get_config('plugin_slug') . '.php' === $plugin_file) {
            $row_meta = ['docs' => '<a href="https://help.dynamic.ooo/" aria-label="' . esc_attr(esc_html__('View Documentation', 'dynamic-ooo')) . '" target="_blank">' . esc_html__('Docs', 'dynamic-ooo') . '</a>', 'community' => '<a href="https://facebook.com/groups/dynamic.ooo" aria-label="' . esc_attr(esc_html__('Facebook Community', 'dynamic-ooo')) . '" target="_blank">' . esc_html__('FB Community', 'dynamic-ooo') . '</a>'];
            $plugin_meta = \array_merge($plugin_meta, $row_meta);
        }
        return $plugin_meta;
    }
    /**
     * @param array<string,string> $links
     * @return array<string,string>
     */
    public function add_links_from_plugin($links)
    {
        $action_links = $this->plugin_utils_manager->get_action_links();
        if (!empty($action_links)) {
            foreach ($action_links as $key => $link_data) {
                $links[$key] = \sprintf('<a title="%s" href="%s">%s</a>', esc_attr($link_data['label']), esc_url(admin_url($link_data['url'])), esc_html($link_data['label']));
            }
        }
        return $links;
    }
    /**
     * @param array<mixed> $links
     * @return array<mixed>
     */
    public function add_license($links)
    {
        $license_page = $this->plugin_utils_manager->get_license_admin_page();
        $links['license'] = '<a style="color:brown;" title="' . esc_html__('Activate license', 'dynamic-ooo') . '" href="' . admin_url() . 'admin.php?page=' . $license_page . '"><b>' . esc_html__('License', 'dynamic-ooo') . '</b></a>';
        return $links;
    }
}
