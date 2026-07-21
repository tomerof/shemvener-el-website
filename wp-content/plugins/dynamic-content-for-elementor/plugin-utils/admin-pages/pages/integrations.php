<?php

namespace DynamicOOO\PluginUtils\AdminPages\Pages;

if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly.
}
abstract class Integrations extends \DynamicOOO\PluginUtils\AdminPages\Pages\Base
{
    /**
     * @var array<string, (
     *     array{
     *         label?: string,
     *         type: 'text'|'select'|'checkbox'|'password',
     *         section: string,
     *         options?: array<string, string>,
     *         checkbox_label?: string,
     *         description?: string
     *     } |
     *     array{
     *         label: string,
     *         type: 'group',
     *         section: string,
     *         fields: array<string, array{
     *             label: string,
     *             type: 'text'|'password',
     *             description?: string
     *         }>
     *     }
     * )>
     */
    protected $fields = [];
    /**
     * @var array<string,string>
     */
    protected $section_descriptions = [];
    /**
     * @var string
     */
    protected $prefix;
    /**
     * @var array<string, array{
     *     name: string,
     *     description: string
     * }>
     */
    protected $sections = [];
    /**
     * @var \DynamicOOO\PluginUtils\Manager
     */
    protected $plugin_utils_manager;
    /**
     * @param \DynamicOOO\PluginUtils\Manager $plugin_utils_manager
     */
    public function __construct($plugin_utils_manager)
    {
        parent::__construct($plugin_utils_manager);
        $this->plugin_utils_manager = $plugin_utils_manager;
        $config = $this->get_config();
        $this->prefix = $plugin_utils_manager->get_config('prefix') . '_';
        // Add prefix to fields and their sections
        $fields = [];
        foreach ($config['fields'] as $key => $field) {
            $prefixed_key = $this->prefix . $key;
            $field['section'] = $this->prefix . $field['section'];
            if ($field['type'] === 'group' && !empty($field['fields'])) {
                $prefixed_subfields = [];
                foreach ($field['fields'] as $subkey => $subfield) {
                    $prefixed_subfields[$this->prefix . $subkey] = $subfield;
                }
                $field['fields'] = $prefixed_subfields;
            }
            $fields[$prefixed_key] = $field;
        }
        $this->fields = $fields;
        // Add prefix to sections
        $sections = [];
        foreach ($config['sections'] as $key => $section) {
            $sections[$this->prefix . 'section_' . $key] = $section;
        }
        $this->sections = $sections;
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_init', [$this, 'maybe_show_settings_saved_notice']);
    }
    /**
     * Get configuration for integrations
     *
     * @return array{
     *     fields: array<string, (
     *     array{
     *         label?: string,
     *         type: 'text'|'select'|'checkbox'|'password',
     *         section: string,
     *         options?: array<string, string>,
     *         checkbox_label?: string,
     *         description?: string
     *     } |
     *     array{
     *         label: string,
     *         type: 'group',
     *         section: string,
     *         fields: array<string, array{
     *             label: string,
     *             type: 'text'|'password',
     *             description?: string
     *         }>
     *     }
     *     )>,
     *     sections: array<string, array{
     *         name: string,
     *         description: string
     *     }>
     * }
     */
    protected abstract function get_config();
    /**
     * @return void
     */
    public function register_settings()
    {
        // Register settings
        foreach ($this->fields as $option_name => $field_args) {
            if ($field_args['type'] === 'group') {
                foreach ($field_args['fields'] as $sub_option_name => $sub_field_args) {
                    register_setting($this->prefix . 'integrations', $sub_option_name, [$this, 'sanitize_field']);
                }
            } else {
                register_setting($this->prefix . 'integrations', $option_name, [$this, 'sanitize_field']);
            }
        }
        // Add sections and fields
        foreach ($this->sections as $section_id => $section) {
            add_settings_section($section_id, $section['name'], [$this, 'section_callback'], $this->prefix . 'integrations');
        }
        // Fields
        foreach ($this->fields as $option_name => $field_args) {
            add_settings_field($option_name, isset($field_args['label']) ? $field_args['label'] : '', [$this, 'field_callback'], $this->prefix . 'integrations', $field_args['section'], ['option_name' => $option_name, 'type' => $field_args['type'], 'options' => $field_args['options'] ?? [], 'fields' => $field_args['fields'] ?? []]);
        }
    }
    /**
     * @param array{
     *     id: string,
     *     title?: string,
     *     callback?: callable
     * } $args
     * @return void
     */
    public function section_callback($args)
    {
        echo '<hr>';
        if (isset($this->sections[$args['id']]['description'])) {
            echo '<p class="description">' . esc_html($this->sections[$args['id']]['description']) . '</p>';
        }
    }
    /**
     * @param string|bool $input
     * @return string|bool
     */
    public function sanitize_field($input)
    {
        return \is_string($input) ? sanitize_text_field($input) : $input;
    }
    /**
     * @param array{
     *     option_name: string,
     *     type: string,
     *     options: array<string,string>,
     *     fields: array<string,array{type: string, label?: string}>
     * } $args
     * @return void
     */
    public function field_callback($args)
    {
        $option_name = $args['option_name'];
        $type = $args['type'];
        $options = $args['options'];
        $fields = $args['fields'];
        $field = $this->fields[$option_name] ?? [];
        $description = isset($field['description']) ? $field['description'] : '';
        if ($type === 'group') {
            echo '<div style="display:flex; gap:20px; align-items:center;">';
            foreach ($fields as $sub_option_name => $sub_field_args) {
                $sub_type = $sub_field_args['type'];
                /**
                 * @var string
                 */
                $sub_value = get_option($sub_option_name, '');
                echo '<div>';
                if (isset($sub_field_args['label']) && $sub_field_args['label']) {
                    echo '<label style="display:block;" for="' . esc_attr($sub_option_name) . '">' . esc_html($sub_field_args['label']) . '</label>';
                }
                $this->print_input_field($sub_option_name, $sub_type, $sub_value);
                echo '</div>';
            }
            echo '</div>';
        } else {
            /**
             * @var string
             */
            $value = get_option($option_name, '');
            $this->print_input_field($option_name, $type, $value, $options);
            if ($description) {
                echo ' <span class="description">' . esc_html($description) . '</span>';
            }
        }
    }
    /**
     * @param string $name
     * @param string $type
     * @param string $value
     * @param array<string,mixed> $options
     * @return void
     */
    protected function print_input_field($name, $type, $value, $options = [])
    {
        $common_attrs = 'class="' . $this->prefix . 'integrations" name="' . esc_attr($name) . '" autocomplete="off"';
        $field = $this->fields[$name] ?? [];
        switch ($type) {
            case 'text':
                echo '<input type="text" ' . $common_attrs . ' value="' . esc_attr($value) . '">';
                break;
            case 'password':
                echo '<input type="password" ' . $common_attrs . ' value="' . esc_attr($value) . '">';
                break;
            case 'checkbox':
                echo '<input type="checkbox" ' . $common_attrs . ' ' . checked($value, 'on', \false) . '>';
                if (!empty($field['checkbox_label'])) {
                    echo ' <label for="' . esc_attr($name) . '">' . esc_html($field['checkbox_label']) . '</label>';
                }
                break;
            case 'select':
                echo '<select ' . $common_attrs . '>';
                /**
                 * @var string $option_key
                 * @var string $label
                 */
                foreach ($options as $option_key => $label) {
                    echo '<option value="' . esc_attr($option_key) . '" ' . selected($value, $option_key, \false) . '>' . esc_html($label) . '</option>';
                }
                echo '</select>';
                break;
        }
    }
    /**
     * Shows settings saved notice if settings were just updated
     *
     * @return void
     */
    public function maybe_show_settings_saved_notice()
    {
        if (isset($_GET['settings-updated']) && $_GET['settings-updated'] && isset($_GET['page']) && $_GET['page'] === $this->plugin_utils_manager->get_config('prefix') . '-integrations') {
            $this->plugin_utils_manager->admin_pages->admin_notices->success(__('Your preferences have been saved.', 'dynamic-ooo'));
        }
    }
    /**
     * @return void
     */
    public function render_content()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        ?>
		<form class="ooo-integration-section" action="options.php" method="post" autocomplete="off">
			<?php 
        settings_fields($this->prefix . 'integrations');
        do_settings_sections($this->prefix . 'integrations');
        submit_button(__('Save Integrations', 'dynamic-ooo'));
        ?>
		</form>
		<?php 
    }
}
