<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use DynamicContentForElementor\ExtensionInfo;
use DynamicContentForElementor\Utils\ExpressionEvaluator;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Tokens;
use DynamicContentForElementor\Plugin;
use ElementorPro\Core\Utils;
use ElementorPro\Modules\Forms\Fields\Upload;
use ElementorPro\Modules\Forms\Classes\Form_Record;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class DynamicEmail extends \ElementorPro\Modules\Forms\Classes\Action_Base
{
    use ExtensionInfo;
    public $has_action = \true;
    public $action_priority = 100;
    public static $txt = '';
    public $doc_url;
    public function __construct()
    {
        self::add_dce_email_template_type();
        // Add specific Template Type
        add_filter('wpml_elementor_widgets_to_translate', [$this, 'wpml_widgets_to_translate_filter'], 50, 1);
    }
    public function run_once()
    {
        $save_guard = \DynamicContentForElementor\Plugin::instance()->save_guard;
        $base_settings = ['dce_form_pdf_converter', 'dce_form_pdf_svg_not_recommended', 'dce_form_pdf_missing_imagick', 'dce_form_pdf_disable_imagick', 'dce_form_pdf_name', 'dce_form_pdf_folder', 'dce_pdf_html_template', 'dce_form_pdf_template', 'dce_form_pdf_size', 'dce_form_pdf_orientation', 'dce_form_pdf_margin', 'dce_form_pdf_button_dpi', 'dce_form_section_page', 'dce_form_pdf_save', 'dce_form_pdf_title', 'dce_form_pdf_content'];
        foreach ($base_settings as $setting) {
            $save_guard->register_unsafe_control('form', $setting);
        }
        $email_repeater_settings = ['dce_form_email_repeater::dce_form_email_enable', 'dce_form_email_repeater::dce_form_email_condition_field', 'dce_form_email_repeater::dce_form_email_condition_status', 'dce_form_email_repeater::dce_form_email_condition_value', 'dce_form_email_repeater::dce_form_email_condition_mode', 'dce_form_email_repeater::dce_form_email_condition_expression', 'dce_form_email_repeater::dce_form_email_invert_condition', 'dce_form_email_repeater::dce_form_email_subject', 'dce_form_email_repeater::dce_form_email_to', 'dce_form_email_repeater::dce_form_email_from', 'dce_form_email_repeater::dce_form_email_from_name', 'dce_form_email_repeater::dce_form_email_reply_to', 'dce_form_email_repeater::dce_form_email_to_cc', 'dce_form_email_repeater::dce_form_email_to_bcc', 'dce_form_email_repeater::dce_form_email_content_type', 'dce_form_email_repeater::dce_form_email_content_type_advanced', 'dce_form_email_repeater::dce_form_email_content', 'dce_form_email_repeater::dce_form_email_content_template', 'dce_form_email_repeater::dce_form_email_content_template_style', 'dce_form_email_repeater::dce_form_email_content_template_layout', 'dce_form_email_repeater::dce_form_email_attachments', 'dce_form_email_repeater::dce_form_email_attachments_delete', 'dce_form_email_repeater::dce_form_pdf_attachments_delete', 'dce_form_email_repeater::dce_form_signature_attachments_delete'];
        foreach ($email_repeater_settings as $setting) {
            $save_guard->register_unsafe_control('form', $setting);
        }
    }
    /**
     * Get Name
     *
     * Return the action name
     *
     * @access public
     * @return string
     */
    public function get_name()
    {
        return 'dce_form_email';
    }
    /**
     * Get Label
     *
     * Returns the action label
     *
     * @access public
     * @return string
     */
    public function get_label()
    {
        return esc_html__('Dynamic Email', 'dynamic-content-for-elementor');
    }
    public function get_script_depends()
    {
        return [];
    }
    public function get_style_depends()
    {
        return [];
    }
    /**
     * Register Settings Section
     *
     * Registers the Action controls
     *
     * @access public
     * @param \Elementor\Widget_Base $widget
     */
    public function register_settings_section($widget)
    {
        $widget->start_controls_section('section_dce_form_email', ['label' => Helper::dce_logo() . $this->get_label(), 'condition' => ['submit_actions' => $this->get_name()]]);
        if (!\DynamicContentForElementor\Helper::can_register_unsafe_controls()) {
            $widget->add_control('admin_notice', ['name' => 'admin_notice', 'type' => Controls_Manager::RAW_HTML, 'raw' => esc_html__('You will need administrator capabilities to edit these settings.', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning']);
            $widget->end_controls_section();
            return;
        }
        Plugin::instance()->text_templates->maybe_add_notice($widget, 'dynamic_email');
        $repeater_fields = new \Elementor\Repeater();
        $repeater_fields->add_control('dce_form_email_enable', ['label' => esc_html__('Enable email', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'description' => esc_html__('You can temporary disable and reactivate it next time without deleting settings ', 'dynamic-content-for-elementor'), 'separator' => 'after']);
        $repeater_fields->add_control('dce_form_email_condition_mode', ['label' => esc_html__('Condition Mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['simple' => esc_html__('Simple', 'dynamic-content-for-elementor'), 'advanced' => esc_html__('Advanced', 'dynamic-content-for-elementor')], 'default' => 'simple']);
        $repeater_fields->add_control('dce_form_email_condition_field', ['label' => esc_html__('Field ID', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'description' => esc_html__('Type here the form field ID to check', 'dynamic-content-for-elementor'), 'condition' => ['dce_form_email_condition_mode' => 'simple']]);
        $repeater_fields->add_control('dce_form_email_condition_status', ['label' => esc_html__('Condition Status', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['empty' => ['title' => esc_html__('Empty', 'dynamic-content-for-elementor'), 'icon' => 'eicon-circle-o'], 'valued' => ['title' => esc_html__('Valorized with any value', 'dynamic-content-for-elementor'), 'icon' => 'eicon-dot-circle-o'], 'lt' => ['title' => esc_html__('Less than', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-angle-left'], 'gt' => ['title' => esc_html__('Greater than', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-angle-right'], 'equal' => ['title' => esc_html__('Equal to', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-circle'], 'contain' => ['title' => esc_html__('Contains', 'dynamic-content-for-elementor'), 'icon' => 'eicon-check']], 'default' => 'valued', 'toggle' => \false, 'label_block' => \true, 'condition' => ['dce_form_email_condition_mode' => 'simple']]);
        $repeater_fields->add_control('dce_form_email_condition_value', ['label' => esc_html__('Condition Value', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'description' => esc_html__('A value to compare the value of the field', 'dynamic-content-for-elementor'), 'condition' => ['dce_form_email_condition_mode' => 'simple', 'dce_form_email_condition_status' => ['lt', 'gt', 'equal', 'contain']]]);
        $repeater_fields->add_control('dce_form_email_invert_condition', ['label' => esc_html__('Invert Condition', 'dynamic-content-for-elementor'), 'description' => esc_html__('The email will be sent when the chosen condition is false.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_form_email_condition_mode' => 'simple']]);
        $repeater_fields->add_control('dce_form_email_condition_expression', ['label' => esc_html__('Expression', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXTAREA, 'description' => \sprintf(
            /* translators: %1$s: opening tag of the link to the Conditions Generator, %2$s: closing tag of the link */
            esc_html__('The email will be sent only if this expression is true. One condition per line. All conditions are and-connected. You can use our online tool %1$sConditions Generator%2$s to generate your conditions more easily.', 'dynamic-content-for-elementor'),
            '<a target="_blank" href="' . esc_url('https://dnmc.ooo/condgen') . '">',
            '</a>'
        ), 'ai' => ['active' => \false], 'label_block' => \true, 'condition' => ['dce_form_email_condition_mode' => 'advanced']]);
        /* translators: %s: Site title. */
        $default_message = \sprintf(esc_html__('New message from "%s"', 'dynamic-content-for-elementor'), get_option('blogname'));
        $repeater_fields->add_control('dce_form_email_subject', ['label' => esc_html__('Subject', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => $default_message, 'placeholder' => $default_message, 'label_block' => \true, 'render_type' => 'none', 'separator' => 'before']);
        $repeater_fields->add_control('dce_form_email_to', ['label' => esc_html__('To', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => get_option('admin_email'), 'placeholder' => get_option('admin_email'), 'label_block' => \true, 'title' => esc_html__('Separate emails with commas', 'dynamic-content-for-elementor'), 'render_type' => 'none', 'separator' => 'before']);
        $site_domain = Utils::get_site_domain();
        $repeater_fields->add_control('dce_form_email_from', ['label' => esc_html__('From Email', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => 'email@' . $site_domain, 'render_type' => 'none']);
        $repeater_fields->add_control('dce_form_email_from_name', ['label' => esc_html__('From Name', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => get_bloginfo('name'), 'render_type' => 'none']);
        $repeater_fields->add_control('dce_form_email_reply_to', ['label' => esc_html__('Reply-To', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'render_type' => 'none']);
        $repeater_fields->add_control('dce_form_email_to_cc', ['label' => esc_html__('Cc', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '', 'title' => esc_html__('Separate emails with commas', 'dynamic-content-for-elementor'), 'render_type' => 'none']);
        $repeater_fields->add_control('dce_form_email_to_bcc', ['label' => esc_html__('Bcc', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '', 'title' => esc_html__('Separate emails with commas', 'dynamic-content-for-elementor'), 'render_type' => 'none']);
        $repeater_fields->add_control('dce_form_email_content_type', ['label' => esc_html__('Send As', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'html', 'render_type' => 'none', 'options' => ['html' => esc_html__('HTML', 'dynamic-content-for-elementor'), 'plain' => esc_html__('Plain', 'dynamic-content-for-elementor')], 'separator' => 'before']);
        $repeater_fields->add_control('dce_form_email_content_type_advanced', ['label' => esc_html__('Email body', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['text' => ['title' => esc_html__('Message', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'template' => ['title' => esc_html__('Template', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-th-large']], 'toggle' => \false, 'default' => 'text', 'condition' => ['dce_form_email_content_type' => 'html']]);
        $all_fields_shortcode = \DynamicContentForElementor\Plugin::instance()->text_templates->get_default_value(['dynamic-shortcodes' => '{form:all-fields}', 'tokens' => '[all-fields]']);
        $repeater_fields->add_control('dce_form_email_content', [
            'label' => esc_html__('Message', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::WYSIWYG,
            'default' => $all_fields_shortcode,
            /* translators: %s: shortcode for all fields */
            'description' => \sprintf(esc_html__('By default, all form fields are sent via %s. To customize sent fields, copy the shortcode that appears inside each field and paste it above.', 'dynamic-content-for-elementor'), '<code>' . $all_fields_shortcode . '</code>'),
            'label_block' => \true,
            'render_type' => 'none',
            'condition' => ['dce_form_email_content_type_advanced' => 'text'],
        ]);
        $repeater_fields->add_control('dce_form_email_content_template', ['label' => esc_html__('Template', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Template Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'object_type' => 'elementor_library', 'description' => esc_html__('Use an Elementor Template as body for this Email.', 'dynamic-content-for-elementor'), 'condition' => ['dce_form_email_content_type' => 'html', 'dce_form_email_content_type_advanced' => 'template']]);
        $repeater_fields->add_control('dce_form_email_content_template_style', ['label' => esc_html__('Styles', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['' => ['title' => esc_html__('Only HTML', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-window-close-o'], 'inline' => ['title' => esc_html__('Inline', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left']], 'default' => 'inline', 'condition' => ['dce_form_email_content_type' => 'html', 'dce_form_email_content_type_advanced' => 'template']]);
        $repeater_fields->add_control('dce_form_email_content_template_layout', ['label' => esc_html__('Flex or Table', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['flex' => ['title' => esc_html__('CSS FLEX', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-leaf'], 'table' => ['title' => esc_html__('CSS TABLE', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-th-large'], 'html' => ['title' => esc_html__('HTML TABLE', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-table']], 'default' => 'table', 'description' => esc_html__('Add more compatibility for columned layout visualization', 'dynamic-content-for-elementor'), 'condition' => ['dce_form_email_content_type' => 'html', 'dce_form_email_content_type_advanced' => 'template', 'dce_form_email_content_template_style' => 'inline']]);
        $repeater_fields->add_control('dce_form_signature_attachments_delete', ['label' => esc_html__('Delete Signature Files from Server after Emails are sent', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'separator' => 'before']);
        $repeater_fields->add_control('dce_form_email_attach_pdf', ['label' => esc_html__('Attach the PDF generated by PDF Generator Action', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
        $repeater_fields->add_control('dce_form_pdf_attachments_delete', ['label' => esc_html__('Delete PDF attachments from Server after Emails are sent', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'no', 'condition' => ['dce_form_email_attach_pdf!' => '']]);
        $repeater_fields->add_control('dce_form_email_attachments', ['label' => esc_html__('Add Uploaded files as Attachments', 'dynamic-content-for-elementor'), 'description' => esc_html__('In order for this to work make sure the Upload Field "Send" setting is set to either "with Link" or "with Both"', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
        $repeater_fields->add_control('dce_form_email_attachments_delete', ['label' => esc_html__('Delete Uploaded Files from Server after Emails are sent', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_form_email_attachments!' => '']]);
        $widget->add_control('dce_form_email_repeater', ['label' => esc_html__('Emails', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::REPEATER, 'title_field' => '{{{ dce_form_email_subject }}}', 'fields' => $repeater_fields->get_controls(), 'description' => esc_html__('Send all Email you need', 'dynamic-content-for-elementor')]);
        $widget->add_control('dce_form_email_help', ['type' => Controls_Manager::RAW_HTML, 'raw' => '<div id="elementor-panel__editor__help" class="p-0"><a id="elementor-panel__editor__help__link" href="' . $this->doc_url . '" target="_blank">' . esc_html__('Need Help', 'dynamic-content-for-elementor') . ' <i class="eicon-help-o"></i></a></div>', 'separator' => 'before']);
        $widget->end_controls_section();
    }
    public function run($record, $ajax_handler)
    {
        $fields = Helper::get_form_data($record);
        $post_id = absint($_POST['post_id']);
        $form_id = sanitize_text_field($_POST['form_id']);
        if (!empty($fields['submitted_on_id'])) {
            $submitted_on_id = absint($fields['submitted_on_id']);
            if ($submitted_on_id) {
                $submitted_post = get_post($submitted_on_id);
                if ($submitted_post instanceof \WP_Post) {
                    $is_published = 'publish' === $submitted_post->post_status;
                    $user_can_read = current_user_can('read_post', $submitted_on_id);
                    if ($is_published || $user_can_read) {
                        global $post, $wp_query;
                        $post = $submitted_post;
                        $wp_query->queried_object = $submitted_post;
                        $wp_query->queried_object_id = $submitted_on_id;
                    }
                }
            }
        }
        $document = \Elementor\Plugin::$instance->documents->get($post_id);
        if ($document) {
            $form = \ElementorPro\Modules\Forms\Module::find_element_recursive($document->get_elements_data(), $form_id);
            $widget = \Elementor\Plugin::$instance->elements_manager->create_element_instance($form);
            $settings = $widget->get_settings_for_display();
        } else {
            $settings = $record->get('form_settings');
        }
        $this->email($fields, $settings, $ajax_handler, $record);
    }
    private function expand($str, $record_fields, $callback)
    {
        return Plugin::instance()->text_templates->expand_shortcodes_or_callback($str, ['form-fields' => $record_fields], $callback);
    }
    /**
     * Remove CR/LF from a header value.
     *
     * @param string $value
     * @return string */
    protected function sanitize_header_value($value)
    {
        return \trim((string) \preg_replace("/[\r\n]+/", '', (string) $value));
    }
    /**
     * Sanitize the From name: strip tags and CR/LF.
     *
     * @param string $name
     * @return string */
    protected function sanitize_from_name($name)
    {
        $name = wp_strip_all_tags((string) $name, \true);
        return $this->sanitize_header_value($name);
    }
    protected function email($fields, $settings = null, $ajax_handler = null, $record = null)
    {
        $remove_uploaded_files = \false;
        $all_pdf_attachments = [];
        $remove_pdf_files = \false;
        $remove_signature_files = \false;
        foreach ($settings['dce_form_email_repeater'] as $mkey => $amail) {
            if ($amail['dce_form_email_enable']) {
                $condition_satisfy = \true;
                $condition_mode = $amail['dce_form_email_condition_mode'] ?? 'simple';
                if ($condition_mode === 'advanced') {
                    // Advanced expression mode
                    if (!empty($amail['dce_form_email_condition_expression'])) {
                        try {
                            $condition_satisfy = ExpressionEvaluator::evaluate($amail['dce_form_email_condition_expression'], $fields);
                        } catch (\Throwable $e) {
                            $ajax_handler->add_admin_error_message(esc_html__('Dynamic Email condition error: ', 'dynamic-content-for-elementor') . $e->getMessage());
                            $condition_satisfy = \false;
                        }
                    }
                } elseif ($condition_mode === 'simple' && !empty($amail['dce_form_email_condition_field'])) {
                    // Simple condition mode
                    $field_value = $fields[$amail['dce_form_email_condition_field']] ?? '';
                    $condition_status = $amail['dce_form_email_condition_status'] ?? 'valued';
                    $condition_value = $amail['dce_form_email_condition_value'] ?? '';
                    $invert = ($amail['dce_form_email_invert_condition'] ?? '') === 'yes';
                    // Convert checkbox values to array for contain check
                    if ($condition_status === 'contain') {
                        $field_type = Helper::get_field_type($amail['dce_form_email_condition_field'], $settings);
                        if ($field_type === 'checkbox') {
                            $field_value = Helper::str_to_array(', ', $field_value);
                        }
                    }
                    $mapped_status = Helper::map_form_condition_status($condition_status, $invert);
                    $condition_satisfy = Helper::is_condition_satisfied($field_value, $mapped_status, $condition_value);
                }
                $use_template = \false;
                if (!empty($amail['dce_form_email_content_type_advanced']) && $amail['dce_form_email_content_type_advanced'] == 'template') {
                    $use_template = \true;
                }
                $send_html = 'plain' !== $amail['dce_form_email_content_type'] || $use_template;
                $line_break = $send_html ? '<br />' : "\n";
                $attachments = array();
                $email_fields = [
                    'dce_form_email_to' => get_option('admin_email'),
                    /* translators: %s: Site title. */
                    'dce_form_email_subject' => \sprintf(esc_html__('New message from "%s"', 'dynamic-content-for-elementor'), get_bloginfo('name')),
                    'dce_form_email_content' => '[all-fields]',
                    'dce_form_email_from_name' => get_bloginfo('name'),
                    'dce_form_email_from' => get_bloginfo('admin_email'),
                    'dce_form_email_reply_to' => 'no-reply@' . Utils::get_site_domain(),
                    'dce_form_email_to_cc' => '',
                    'dce_form_email_to_bcc' => '',
                ];
                foreach ($email_fields as $key => $default) {
                    $setting = $amail[$key];
                    if (!empty($setting)) {
                        $email_fields[$key] = $setting;
                    }
                }
                $callback = null;
                if (\DynamicContentForElementor\Tokens::is_active()) {
                    $callback = function ($str) use($fields, $record, $line_break) {
                        $str = $this->remove_attachment_tokens($str, $fields);
                        $str = $this->replace_content_shortcodes($str, $record, $line_break);
                        return Helper::get_dynamic_value($str, $fields);
                    };
                }
                $record_fields = $record->get('fields');
                $dce_form_email_to = $this->expand($email_fields['dce_form_email_to'], $record_fields, $callback);
                $dce_form_email_subject = $this->expand($email_fields['dce_form_email_subject'], $record_fields, $callback);
                $email_fields['dce_form_email_from_name'] = $this->expand($email_fields['dce_form_email_from_name'], $record_fields, $callback);
                $email_fields['dce_form_email_from'] = $this->expand($email_fields['dce_form_email_from'], $record_fields, $callback);
                $email_fields['dce_form_email_reply_to'] = $this->expand($email_fields['dce_form_email_reply_to'] ?? '', $record_fields, $callback);
                $email_fields['dce_form_email_to_cc'] = $this->expand($email_fields['dce_form_email_to_cc'], $record_fields, $callback);
                $email_fields['dce_form_email_to_bcc'] = $this->expand($email_fields['dce_form_email_to_bcc'], $record_fields, $callback);
                // Sanitize header fields
                $dce_form_email_to = $this->sanitize_header_value($dce_form_email_to);
                $dce_form_email_subject = $this->sanitize_header_value($dce_form_email_subject);
                $email_fields['dce_form_email_from_name'] = $this->sanitize_from_name($email_fields['dce_form_email_from_name']);
                $email_fields['dce_form_email_from'] = $this->sanitize_header_value($email_fields['dce_form_email_from']);
                $email_fields['dce_form_email_reply_to'] = $this->sanitize_header_value($email_fields['dce_form_email_reply_to']);
                $email_fields['dce_form_email_to_cc'] = $this->sanitize_header_value($email_fields['dce_form_email_to_cc']);
                $email_fields['dce_form_email_to_bcc'] = $this->sanitize_header_value($email_fields['dce_form_email_to_bcc']);
                $headers = \sprintf('From: %s <%s>' . "\r\n", $email_fields['dce_form_email_from_name'], $email_fields['dce_form_email_from']);
                if (!empty($email_fields['dce_form_email_reply_to']) && is_email($email_fields['dce_form_email_reply_to'])) {
                    $headers .= \sprintf('Reply-To: %s' . "\r\n", $email_fields['dce_form_email_reply_to']);
                }
                if ($send_html) {
                    $headers .= 'Content-Type: text/html; charset=UTF-8' . "\r\n";
                }
                $cc_header = '';
                if (!empty($email_fields['dce_form_email_to_cc'])) {
                    // Validate each email in the Cc field (comma-separated)
                    $cc_emails = \array_map('trim', \explode(',', $email_fields['dce_form_email_to_cc']));
                    $valid_cc_emails = \array_filter($cc_emails, fn($email) => (bool) is_email($email));
                    if (!empty($valid_cc_emails)) {
                        $cc_header = 'Cc: ' . \implode(', ', $valid_cc_emails) . "\r\n";
                    }
                }
                $bcc_header = '';
                if (!empty($email_fields['dce_form_email_to_bcc'])) {
                    // Validate each email in the Bcc field (comma-separated)
                    $bcc_emails = \array_map('trim', \explode(',', $email_fields['dce_form_email_to_bcc']));
                    $valid_bcc_emails = \array_filter($bcc_emails, fn($email) => (bool) is_email($email));
                    if (!empty($valid_bcc_emails)) {
                        $bcc_header = 'Bcc: ' . \implode(', ', $valid_bcc_emails) . "\r\n";
                    }
                }
                /**
                 * Email headers.
                 *
                 * Filters the additional headers sent when the form send an email.
                 *
                 * @since 1.0.0
                 *
                 * @param string|array $headers Additional headers.
                 */
                $headers = apply_filters('elementor_pro/forms/wp_mail_headers', $headers);
                /**
                 * Email content.
                 *
                 * Filters the content of the email sent by the form.
                 *
                 * @since 1.0.0
                 *
                 * @param string $email_content Email content.
                 */
                if ($use_template) {
                    if (empty($amail['dce_form_email_content_template'])) {
                        $ajax_handler->add_error_message('Dynamic Email: No Elementor template selected.');
                        return;
                    }
                    $atts = ['id' => $amail['dce_form_email_content_template']];
                    if ($amail['dce_form_email_content_template_style'] == 'embed') {
                        $atts['inlinecss'] = \true;
                    }
                    if (get_the_ID()) {
                        $atts['post_id'] = get_the_ID();
                    }
                    $template_system = \DynamicContentForElementor\Plugin::instance()->template_system;
                    $dsh = Plugin::instance()->text_templates->dce_shortcodes;
                    // dsh dynamic tags rendered in the template should do the expand. They can do it as have the
                    // form data that we're passing with call_with_data.
                    $dce_form_email_content = $dsh->call_with_data(['form-fields' => $record->get('fields')], [$template_system, 'build_elementor_template_special'], [$atts]);
                    if ($dce_form_email_content === null) {
                        $ajax_handler->add_error_message('Dynamic Email: Template not found. Does it still exist?');
                        return;
                    }
                    $pdf_attachments = $this->get_email_pdf_attachments($dce_form_email_content, $fields, $amail, $settings);
                    $all_pdf_attachments += $pdf_attachments;
                    $upload_attachments = $this->get_email_upload_attachments($dce_form_email_content, $fields, $amail, $settings);
                    $attachments = $pdf_attachments + $upload_attachments;
                    $attachments = apply_filters('dynamicooo/dynamic-email/attachments', $attachments, $settings['form_name'] ?? '');
                    if (\DynamicContentForElementor\Tokens::is_active()) {
                        $dce_form_email_content = $this->remove_attachment_tokens($dce_form_email_content, $fields);
                        $dce_form_email_content = $this->replace_content_shortcodes($dce_form_email_content, $record, $line_break);
                        $dce_form_email_content = Helper::get_dynamic_value($dce_form_email_content, $fields);
                    }
                    if ($amail['dce_form_email_content_template_style']) {
                        $css = Helper::get_post_css($amail['dce_form_email_content_template']);
                        // add some fixies
                        $css .= '/*.elementor-column-wrap,*/ .elementor-widget-wrap { display: block !important; }';
                        if (!empty($amail['dce_form_email_content_template_layout']) && $amail['dce_form_email_content_template_layout'] != 'flex') {
                            // from flex to table
                            $css .= '.elementor-section .elementor-container { display: table !important; width: 100% !important; }';
                            $css .= '.elementor-row { display: table-row !important; }';
                            $css .= '.elementor-column { display: table-cell !important; }';
                            $css .= '.elementor-column-wrap, .elementor-widget-wrap { display: block !important; }';
                            $css = \str_replace(':not(.elementor-motion-effects-element-type-background) > .elementor-element-populated', ':not(.elementor-motion-effects-element-type-background)', $css);
                        }
                        if ($amail['dce_form_email_content_template_style'] == 'inline') {
                            // https://github.com/tijsverkoyen/CssToInlineStyles
                            // create instance
                            $cssToInlineStyles = new \DynamicOOOS\TijsVerkoyen\CssToInlineStyles\CssToInlineStyles();
                            // output
                            $dce_form_email_content = $cssToInlineStyles->convert($dce_form_email_content, $css);
                        }
                        if (!empty($amail['dce_form_email_content_template_layout']) && $amail['dce_form_email_content_template_layout'] == 'html') {
                            // from div to table
                            $dce_form_email_content = Helper::tablefy($dce_form_email_content);
                        }
                        if ($amail['dce_form_email_content_template_style'] == 'embed') {
                            $dce_form_email_content = '<style>' . $css . '</style>' . $dce_form_email_content;
                        }
                    }
                    $dce_form_email_content_txt = '';
                } else {
                    $settings_raw = $record->get('form_settings');
                    // from message textarea with dynamic token
                    $dce_form_email_content = $settings_raw['dce_form_email_repeater'][$mkey]['dce_form_email_content'];
                    $pdf_attachments = $this->get_email_pdf_attachments($dce_form_email_content, $fields, $amail, $settings);
                    $all_pdf_attachments += $pdf_attachments;
                    $upload_attachments = $this->get_email_upload_attachments($dce_form_email_content, $fields, $amail, $settings);
                    $attachments = \array_merge($pdf_attachments, $upload_attachments);
                    $attachments = apply_filters('dynamicooo/dynamic-email/attachments', $attachments, $settings['form_name'] ?? '');
                    $dce_form_email_content = Plugin::instance()->text_templates->expand_shortcodes_or_callback($dce_form_email_content, ['form-fields' => $record->get('fields')], $callback);
                    // generate the TEXT/PLAIN version
                    $dce_form_email_content_txt = $dce_form_email_content;
                    $dce_form_email_content_txt = \str_replace('</p>', '</p><br /><br />', $dce_form_email_content_txt);
                    $dce_form_email_content_txt = \str_replace('<br />', "\n", $dce_form_email_content_txt);
                    $dce_form_email_content_txt = \str_replace('<br>', "\n", $dce_form_email_content_txt);
                    $dce_form_email_content_txt = \strip_tags($dce_form_email_content_txt);
                    if ($send_html) {
                        add_action('phpmailer_init', [$this, 'set_wp_mail_altbody']);
                    } else {
                        $dce_form_email_content = $dce_form_email_content_txt;
                        $dce_form_email_content_txt = '';
                    }
                    $dce_form_email_content = apply_filters('elementor_pro/forms/wp_mail_message', $dce_form_email_content);
                }
                self::$txt = $dce_form_email_content_txt;
                // replace single fields shorcode
                $dce_form_email_content = Helper::replace_setting_shortcodes($dce_form_email_content, $fields);
                if ($condition_satisfy) {
                    $email_sent = wp_mail($dce_form_email_to, $dce_form_email_subject, $dce_form_email_content, $headers . $cc_header . $bcc_header, $attachments);
                    do_action('elementor_pro/forms/mail_sent', $amail, $record);
                    if (!$email_sent) {
                        $ajax_handler->add_error_message(\ElementorPro\Modules\Forms\Classes\Ajax_Handler::get_default_message(\ElementorPro\Modules\Forms\Classes\Ajax_Handler::SERVER_ERROR, $amail));
                    }
                }
                if ($amail['dce_form_email_attachments'] && $amail['dce_form_email_attachments_delete']) {
                    $remove_uploaded_files = \true;
                }
                if (($amail['dce_form_pdf_attachments_delete'] ?? '') === 'yes') {
                    $remove_pdf_files = \true;
                }
                if (($amail['dce_form_signature_attachments_delete'] ?? '') === 'yes') {
                    $remove_signature_files = \true;
                }
                global $phpmailer;
                if ($phpmailer !== null) {
                    $phpmailer->AltBody = '';
                    // clear the previous alt body for the next email.
                }
                remove_action('phpmailer_init', [$this, 'set_wp_mail_altbody']);
            }
        }
        if ($remove_signature_files && $ajax_handler->is_success && !empty($fields) && \is_array($fields)) {
            foreach ($fields as $akey => $adatas) {
                $afield = Helper::get_field($akey, $settings);
                if (!$afield || $afield['field_type'] !== 'dce_form_signature') {
                    continue;
                }
                if (!\filter_var($adatas, \FILTER_VALIDATE_URL)) {
                    continue;
                }
                $filename = Helper::url_to_path($adatas);
                if (\is_string($filename) && \is_file($filename)) {
                    $this->safe_unlink_upload($filename);
                }
            }
        }
        if ($remove_pdf_files) {
            foreach ($all_pdf_attachments as $pdf_path) {
                $this->safe_unlink_upload($pdf_path);
            }
        }
        if (!$remove_uploaded_files || !$ajax_handler->is_success || empty($fields) || !\is_array($fields)) {
            return;
        }
        foreach ($fields as $akey => $adatas) {
            $afield = Helper::get_field($akey, $settings);
            if (!$afield || $afield['field_type'] !== 'upload') {
                continue;
            }
            $files = Helper::str_to_array(',', $adatas);
            foreach ($files as $adata) {
                if (!\filter_var($adata, \FILTER_VALIDATE_URL)) {
                    continue;
                }
                $filename = Helper::url_to_path($adata);
                if (\is_string($filename) && \is_file($filename)) {
                    $this->safe_unlink_upload($filename);
                }
            }
        }
    }
    /**
     * Safely delete a file from the uploads directory
     *
     * Validates that the file path is within the WordPress uploads directory
     * before attempting deletion to prevent unauthorized file removal.
     *
     * @param string $path File path to delete
     * @return bool True if file was deleted, false if validation failed or deletion failed
     */
    protected function safe_unlink_upload($path)
    {
        $uploads = wp_upload_dir();
        $real = \realpath($path);
        $base = \realpath($uploads['basedir']);
        if (!$real || !$base) {
            return \false;
        }
        // Ensure the file is inside the uploads directory
        if (\strpos($real, \rtrim($base, \DIRECTORY_SEPARATOR) . \DIRECTORY_SEPARATOR) !== 0) {
            return \false;
            // outside uploads, do not delete
        }
        return @\unlink($real);
    }
    public static function set_wp_mail_altbody($phpmailer)
    {
        if ($phpmailer !== null) {
            $phpmailer->AltBody = self::$txt;
        }
    }
    public function remove_attachment_tokens($dce_form_email_content, $fields)
    {
        $attachments_tokens = \explode(':attachment]', $dce_form_email_content);
        foreach ($attachments_tokens as $akey => $avalue) {
            $pieces = \explode('[form:', $avalue);
            if (\count($pieces) > 2) {
                $field = \end($pieces);
                if (isset($fields[$field])) {
                    $dce_form_email_content = \str_replace('[form:' . $field . ':attachment]', '', $dce_form_email_content);
                }
            }
        }
        return $dce_form_email_content;
    }
    public function get_email_pdf_attachments($dce_form_email_content, $fields, $amail, $settings)
    {
        $attachments = array();
        global $dce_form;
        if (($amail['dce_form_email_attach_pdf'] ?? 'no') === 'yes') {
            if (isset($dce_form['pdf']) && isset($dce_form['pdf']['path'])) {
                $attachments[] = $dce_form['pdf']['path'];
            }
        }
        $pdf_attachment = '<!--[dce_form_pdf:attachment]-->';
        $pdf_form = '[form:pdf]';
        $pos_pdf_token = \strpos($dce_form_email_content, $pdf_attachment);
        $pos_pdf_form = \strpos($dce_form_email_content, $pdf_form);
        if ($pos_pdf_token !== \false || $pos_pdf_form !== \false) {
            // add PDF as attachment
            if (isset($dce_form['pdf']) && isset($dce_form['pdf']['path'])) {
                $pdf_path = $dce_form['pdf']['path'];
                $attachments[] = $pdf_path;
            }
            $dce_form_email_content = \str_replace($pdf_attachment, '', $dce_form_email_content);
            $dce_form_email_content = \str_replace($pdf_form, '', $dce_form_email_content);
        }
        $attachments_tokens = \explode(':attachment]', $dce_form_email_content);
        foreach ($attachments_tokens as $akey => $avalue) {
            $pieces = \explode('[form:', $avalue);
            if (\count($pieces) > 1) {
                $field = \end($pieces);
                if (isset($fields[$field])) {
                    $files = Helper::str_to_array(',', $fields[$field]);
                    if (!empty($files)) {
                        foreach ($files as $adata) {
                            if (\filter_var($adata, \FILTER_VALIDATE_URL)) {
                                $file_path = Helper::url_to_path($adata);
                                $uploads_base = \realpath(wp_upload_dir()['basedir']);
                                $real_path = \realpath((string) $file_path);
                                $file_path = $real_path && $uploads_base && \strpos($real_path, $uploads_base . \DIRECTORY_SEPARATOR) === 0 ? $real_path : '';
                                if (\is_file($file_path)) {
                                    if (!\in_array($file_path, $attachments)) {
                                        $attachments[] = $file_path;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $attachments;
    }
    public function get_email_upload_attachments($dce_form_email_content, $fields, $amail, $settings)
    {
        $attachments = [];
        if ($amail['dce_form_email_attachments']) {
            if (!empty($fields) && \is_array($fields)) {
                foreach ($fields as $akey => $adatas) {
                    $afield = Helper::get_field($akey, $settings);
                    if ($afield) {
                        if ($afield['field_type'] == 'upload') {
                            $files = Helper::str_to_array(',', $adatas);
                            if (!empty($files)) {
                                foreach ($files as $adata) {
                                    if (\filter_var($adata, \FILTER_VALIDATE_URL)) {
                                        $file_path = Helper::url_to_path($adata);
                                        if (\is_file($file_path)) {
                                            if (!\in_array($file_path, $attachments)) {
                                                $attachments[] = $file_path;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $attachments;
    }
    /**
     * SPDX-FileCopyrightText: Elementor
     * SPDX-License-Identifier: GPL-3.0-or-later
     */
    public function replace_content_shortcodes($email_content, $record, $line_break)
    {
        $all_fields_shortcode = '[all-fields]';
        $text = $this->get_shortcode_value($all_fields_shortcode, $email_content, $record, $line_break);
        $email_content = \str_replace($all_fields_shortcode, $text, $email_content);
        $all_valued_fields_shortcode = '[all-fields|!empty]';
        $text = $this->get_shortcode_value($all_valued_fields_shortcode, $email_content, $record, $line_break, \false);
        $email_content = \str_replace($all_valued_fields_shortcode, $text, $email_content);
        return $email_content;
    }
    /**
     * SPDX-FileCopyrightText: Elementor
     * SPDX-License-Identifier: GPL-3.0-or-later
     */
    public function get_shortcode_value($shortcode, $email_content, $record, $line_break, $show_empty = \true)
    {
        $text = '';
        if (\false !== \strpos($email_content, $shortcode)) {
            foreach ($record->get('fields') as $field) {
                $formatted = '';
                if (!empty($field['title'])) {
                    $formatted = \sprintf('%s: %s', $field['title'], $field['value']);
                } elseif (!empty($field['value'])) {
                    $formatted = \sprintf('%s', $field['value']);
                }
                if ('textarea' === $field['type'] && '<br>' === $line_break) {
                    $formatted = \str_replace(["\r\n", "\n", "\r"], '<br />', $formatted);
                }
                if (!$show_empty && empty($field['value'])) {
                    continue;
                }
                $text .= $formatted . $line_break;
            }
        }
        return $text;
    }
    public static function add_dce_email_template_type()
    {
        // Add Email Template Type
        $dce_email = 'Elementor\\Modules\\Library\\Documents\\Email';
        \Elementor\Plugin::instance()->documents->register_document_type($dce_email::get_name_static(), \Elementor\Modules\Library\Documents\Email::get_class_full_name());
    }
    public function on_export($element)
    {
        return $element;
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
        $widgets['form']['fields'][] = ['field' => 'dce_form_email_repeater', 'type' => esc_html__('Dynamic Email', 'dynamic-content-for-elementor'), 'editor_type' => 'REPEATER', 'fields' => [['field' => 'dce_form_email_subject', 'type' => esc_html__('Email Subject', 'dynamic-content-for-elementor'), 'editor_type' => 'LINE'], ['field' => 'dce_form_email_content', 'type' => esc_html__('Email Content', 'dynamic-content-for-elementor'), 'editor_type' => 'AREA']]];
        return $widgets;
    }
}
