<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Tokens;
use DynamicContentForElementor\Plugin;
use ElementorPro\Modules\QueryControl\Module as QueryModule;
if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
class PdfGenerator extends \ElementorPro\Modules\Forms\Classes\Action_Base
{
    public $has_action = \true;
    /**
     * @var bool
     */
    private $pdf_target_attempted = \false;
    /**
     * @var string|false $pdf_url will contain the pdf url after it's
     * generated. We provide this for custom user hooks.
     */
    public $pdf_url = \false;
    /**
     * @var string|false
     */
    public $pdf_path = \false;
    /**
     * @param string $path
     * @param bool $allow_empty
     * @return array<int,string>|false
     */
    private function normalize_relative_pdf_path($path, $allow_empty = \false)
    {
        if (!\is_string($path)) {
            return \false;
        }
        $path = \trim($path);
        if ('' === $path) {
            return $allow_empty ? [] : \false;
        }
        if (\strpos($path, '://') !== \false) {
            return \false;
        }
        if (\preg_match('/^(?:[a-zA-Z]:[\\/\\\\]|[\\/\\\\]{1,2})/', $path)) {
            return \false;
        }
        $segments = \array_values(\array_filter(\explode('/', wp_normalize_path($path)), static function ($segment) {
            return '' !== $segment;
        }));
        foreach ($segments as $segment) {
            if ('.' === $segment || '..' === $segment) {
                return \false;
            }
        }
        return $segments;
    }
    /**
     * @param string $folder
     * @param string $file_name
     * @return array<string,string>|false
     */
    private function get_safe_pdf_target($folder, $file_name)
    {
        $upload = wp_upload_dir();
        $upload_dir = \realpath($upload['basedir']);
        if (!\is_string($upload_dir)) {
            return \false;
        }
        $upload_dir = wp_normalize_path($upload_dir);
        $upload_dir_prefix = \rtrim($upload_dir, '/') . '/';
        $dir_segments = $this->normalize_relative_pdf_path($folder, \true);
        if (\false === $dir_segments) {
            return \false;
        }
        $file_name = sanitize_file_name($file_name);
        if ('' === $file_name) {
            return \false;
        }
        $dir_path = $upload_dir;
        foreach ($dir_segments as $segment) {
            $next_dir = $dir_path . '/' . $segment;
            if (\file_exists($next_dir)) {
                $resolved_dir = \realpath($next_dir);
                if (!\is_string($resolved_dir)) {
                    return \false;
                }
                $resolved_dir = wp_normalize_path($resolved_dir);
                if (\strpos($resolved_dir . '/', $upload_dir_prefix) !== 0 || !\is_dir($resolved_dir)) {
                    return \false;
                }
                $dir_path = $resolved_dir;
            } else {
                $dir_path = $next_dir;
            }
        }
        $file_path = $dir_path . '/' . $file_name;
        if (\file_exists($file_path)) {
            $resolved_file = \realpath($file_path);
            if (!\is_string($resolved_file)) {
                return \false;
            }
            $resolved_file = wp_normalize_path($resolved_file);
            if (\strpos($resolved_file, $upload_dir_prefix) !== 0) {
                return \false;
            }
            $file_path = $resolved_file;
        }
        $relative_dir = \implode('/', $dir_segments);
        $url = trailingslashit($upload['baseurl']);
        if ('' !== $relative_dir) {
            $url .= trailingslashit($relative_dir);
        }
        $url .= $file_name;
        return ['dir' => $dir_path, 'path' => $file_path, 'url' => esc_url_raw($url)];
    }
    /**
     * @param string $folder
     * @param string $file_name
     * @return void
     */
    private function log_blocked_pdf_target($folder, $file_name)
    {
        $parts = \array_filter([wp_normalize_path(\trim((string) $folder, "/\\ \t\n\r\x00\v")), wp_normalize_path(\trim((string) $file_name))], static function ($part) {
            return '' !== $part;
        });
        $path = \str_replace(["\r", "\n"], ' ', wp_normalize_path(\implode('/', $parts)));
        \error_log('Dynamic Content for Elementor - PDF Generator for Elementor Pro Form: blocked PDF path outside uploads directory: ' . $path);
    }
    /**
     * @param string $path
     * @return void
     */
    private function log_pdf_write_failure($path)
    {
        $path = \str_replace(["\r", "\n"], ' ', wp_normalize_path($path));
        \error_log('Dynamic Content for Elementor - PDF Generator for Elementor Pro Form: failed to write PDF file: ' . $path);
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
        return 'dce_form_pdf';
    }
    public function run_once()
    {
        add_filter('elementor_pro/forms/field_types', [$this, 'add_field_type']);
        // We update the dce_pdf_url field here, because during run() it's too
        // late, as the collect submission action is already done.
        add_action('elementor_pro/forms/process', [$this, 'update_dce_pdf_fields']);
        add_action('elementor_pro/forms/render_field/dce_pdf_url', [$this, 'url_field_render'], 10, 3);
        $save_guard = \DynamicContentForElementor\Plugin::instance()->save_guard;
        $settings = ['dce_form_pdf_converter', 'dce_form_pdf_svg_not_recommended', 'dce_form_pdf_missing_imagick', 'dce_form_pdf_disable_imagick', 'dce_form_pdf_name', 'dce_form_pdf_folder', 'dce_pdf_html_template', 'dce_form_pdf_template', 'dce_form_pdf_size', 'dce_form_pdf_orientation', 'dce_form_pdf_margin', 'dce_form_pdf_button_dpi', 'dce_form_section_page', 'dce_form_pdf_save', 'dce_form_pdf_title', 'dce_form_pdf_content'];
        foreach ($settings as $setting) {
            $save_guard->register_unsafe_control('form', $setting);
        }
        $save_guard->register_unsafe_control('form', 'dce_form_pdf_svg_code_repeater::text');
    }
    /**
     * @param \ElementorPro\Modules\Forms\Classes\Form_Record $record
     * @return bool
     */
    public function set_pdf_url_and_path($record)
    {
        global $dce_form;
        $this->pdf_target_attempted = \true;
        $this->pdf_url = \false;
        $this->pdf_path = \false;
        $fields = Helper::get_form_data($record);
        $dir_rel_path = Plugin::instance()->text_templates->expand_shortcodes_or_callback($record->get_form_settings('dce_form_pdf_folder'), ['form-fields' => $record->get('fields')], function ($str) use($fields) {
            return Helper::get_dynamic_value($str, $fields);
        });
        $file_name = Plugin::instance()->text_templates->expand_shortcodes_or_callback($record->get_form_settings('dce_form_pdf_name'), ['form-fields' => $record->get('fields')], function ($str) use($fields) {
            return Helper::get_dynamic_value($str, $fields);
        });
        $file_name .= '.pdf';
        $pdf_target = $this->get_safe_pdf_target($dir_rel_path, $file_name);
        if (\false === $pdf_target) {
            $this->log_blocked_pdf_target((string) $dir_rel_path, (string) $file_name);
            unset($dce_form['pdf']);
            return \false;
        }
        Helper::ensure_dir($pdf_target['dir']);
        // $dce_form global is used by the dynamic email action
        $dce_form['pdf']['path'] = $pdf_target['path'];
        $dce_form['pdf']['url'] = $pdf_target['url'];
        $this->pdf_url = $dce_form['pdf']['url'];
        $this->pdf_path = $dce_form['pdf']['path'];
        return \true;
    }
    /**
     * @param \ElementorPro\Modules\Forms\Classes\Form_Record $record
     * @return void
     */
    public function update_dce_pdf_fields($record)
    {
        $actions = $record->get_form_settings('submit_actions');
        if (\in_array('dce_form_pdf', $actions, \true)) {
            $converter = $record->get_form_settings('dce_form_pdf_converter');
            $this->set_pdf_url_and_path($record);
            $pdf_url = $this->pdf_url;
            if ($converter === 'dompdf') {
                $pdf_url = esc_html__('Not available with dompdf converter', 'dynamic-content-for-elementor');
            }
            $fields = $record->get_field([]);
            foreach ($fields as $field) {
                if ($field['type'] === 'dce_pdf_url') {
                    $record->update_field($field['id'], 'value', $pdf_url);
                    $record->update_field($field['id'], 'raw_value', $pdf_url);
                }
            }
        }
    }
    /**
     * @param array<string, string> $field_types
     * @return array<string, string>
     */
    public function add_field_type($field_types)
    {
        if (!\in_array('dce_pdf_url', $field_types)) {
            $field_types['dce_pdf_url'] = esc_html__('PDF Generator - URL', 'dynamic-content-for-elementor');
        }
        return $field_types;
    }
    /**
     * @return void
     * @param array<mixed> $item
     * @param int $item_index
     * @param \ElementorPro\Modules\Forms\Widgets\Form $form
     */
    public function url_field_render($item, $item_index, $form)
    {
        $form->add_render_attribute('input' . $item_index, 'type', 'hidden', \true);
        $form->add_render_attribute('input' . $item_index, 'value', 'pdf_url', \true);
        // added because otherwise if field is required a cryptic error is shown:
        echo '<input ' . $form->get_render_attribute_string('input' . $item_index) . '>';
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $msg = esc_html__('After submit the PDF Generator - URL Field will contained the URL of the PDF generated by the PDF Generator Action. This message is not visible in the frontend.', 'dynamic-content-for-elementor');
            Helper::notice(\false, $msg);
        }
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
        return esc_html__('PDF Generator', 'dynamic-content-for-elementor');
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
        $widget->start_controls_section('section_dce_form_pdf', ['label' => Helper::dce_logo() . $this->get_label(), 'condition' => ['submit_actions' => $this->get_name()]]);
        if (!\DynamicContentForElementor\Helper::can_register_unsafe_controls()) {
            $widget->add_control('admin_notice', ['name' => 'admin_notice', 'type' => Controls_Manager::RAW_HTML, 'raw' => '<div class="elementor-panel-alert elementor-panel-alert-warning">' . esc_html__('You will need administrator capabilities to edit these settings.', 'dynamic-content-for-elementor') . '</div>']);
            $widget->end_controls_section();
            return;
        }
        Plugin::instance()->text_templates->maybe_add_notice($widget, 'pdf');
        $widget->add_control('dce_form_pdf_converter', ['label' => esc_html__('Converter', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'description' => esc_html__('Choose the converter that will generate the PDF.', 'dynamic-content-for-elementor'), 'options' => ['html' => 'HTML'], 'toggle' => \false, 'default' => 'html', 'condition' => ['dce_form_pdf_converter!' => 'html']]);
        $warning = esc_html__('The SVG Converter is deprecated. It has problems with languages other than english and input text is not wrapped. We recommend the newer HTML converter, it doesn’t have these limitations.', 'dynamic-content-for-elementor');
        $widget->add_control('dce_form_pdf_svg_not_recommended', ['type' => \Elementor\Controls_Manager::RAW_HTML, 'raw' => $warning, 'separator' => 'before', 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning', 'condition' => ['dce_form_pdf_converter' => 'svg']]);
        $msg_txt = esc_html__('The only official supported SVG editor is ', 'dynamic-content-for-elementor');
        $warning = $msg_txt . '<a target="_blank" href="https://dnmc.ooo/svg">Dynamic SVG Editor</a>';
        $widget->add_control('dce_form_pdf_missing_imagick', ['type' => \Elementor\Controls_Manager::RAW_HTML, 'raw' => $warning, 'separator' => 'before', 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning', 'condition' => ['dce_form_pdf_converter' => 'svg']]);
        if (\extension_loaded('imagick')) {
            $msg_txt = esc_html__('Disable this if you want smaller PDFs and the quality is not affected.', 'dynamic-content-for-elementor');
            $widget->add_control('dce_form_pdf_disable_imagick', ['label' => esc_html__('Disable Imagick', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'description' => $msg_txt, 'return_value' => 'disable', 'default' => 'enable', 'separator' => 'before', 'condition' => ['dce_form_pdf_converter' => 'svg']]);
        }
        $widget->add_control('dce_form_pdf_name', ['label' => esc_html__('Name', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => \DynamicContentForElementor\Plugin::instance()->text_templates->get_default_value(['dynamic-shortcodes' => '{date:now @format=U}', 'tokens' => '[date|U]']), 'description' => esc_html__('The PDF file name, the .pdf extension will automatically added', 'dynamic-content-for-elementor'), 'label_block' => \true, 'separator' => 'before']);
        $widget->add_control('dce_form_pdf_folder', ['label' => esc_html__('Folder', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => \DynamicContentForElementor\Plugin::instance()->text_templates->get_default_value(['dynamic-shortcodes' => 'elementor/pdf/{date:now @format=Y}/{date:now @format=m}', 'tokens' => 'elementor/pdf/[date|Y]/[date|m]']), 'description' => esc_html__('The directory inside /wp-content/uploads/ where save the PDF file', 'dynamic-content-for-elementor'), 'label_block' => \true]);
        $svg_repeater = new \Elementor\Repeater();
        $svg_repeater->add_control('text', ['label' => esc_html__('SVG Page code', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CODE, 'language' => 'svg', 'dynamic' => ['active' => \false]]);
        $widget->add_control('dce_pdf_html_template', ['label' => esc_html__('HTML Template', 'dynamic-content-for-elementor'), 'type' => QueryModule::QUERY_CONTROL_ID, 'options' => [], 'label_block' => \true, 'autocomplete' => ['object' => QueryModule::QUERY_OBJECT_POST, 'display' => 'detailed', 'query' => ['post_type' => \DynamicContentForElementor\PdfHtmlTemplates::CPT]], 'condition' => ['dce_form_pdf_converter' => 'html']]);
        $widget->add_control('dce_form_pdf_svg_code_repeater', ['label' => esc_html__('PDF Pages', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::REPEATER, 'title_field' => 'Page', 'fields' => $svg_repeater->get_controls(), 'description' => esc_html__('The SVG template code that will be converted to PDF. One SVG per page. You can insert Tokens inside it.', 'dynamic-content-for-elementor'), 'condition' => ['dce_form_pdf_converter' => 'svg']]);
        $widget->add_control('dce_form_pdf_template', ['label' => esc_html__('Template', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => esc_html__('Template Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'object_type' => 'elementor_library', 'description' => esc_html__('Use an Elementor Template as body for this PDF', 'dynamic-content-for-elementor'), 'condition' => ['dce_form_pdf_converter' => 'dompdf']]);
        $paper_sizes = array(0 => '4a0', 1 => '2a0', 2 => 'a0', 3 => 'a1', 4 => 'a2', 5 => 'a3', 6 => 'a4', 7 => 'a5', 8 => 'a6', 9 => 'a7', 10 => 'a8', 11 => 'a9', 12 => 'a10', 13 => 'b0', 14 => 'b1', 15 => 'b2', 16 => 'b3', 17 => 'b4', 18 => 'b5', 19 => 'b6', 20 => 'b7', 21 => 'b8', 22 => 'b9', 23 => 'b10', 24 => 'c0', 25 => 'c1', 26 => 'c2', 27 => 'c3', 28 => 'c4', 29 => 'c5', 30 => 'c6', 31 => 'c7', 32 => 'c8', 33 => 'c9', 34 => 'c10', 35 => 'ra0', 36 => 'ra1', 37 => 'ra2', 38 => 'ra3', 39 => 'ra4', 40 => 'sra0', 41 => 'sra1', 42 => 'sra2', 43 => 'sra3', 44 => 'sra4', 45 => 'letter', 46 => 'half-letter', 47 => 'legal', 48 => 'ledger', 49 => 'tabloid', 50 => 'executive', 51 => 'folio', 52 => 'commercial #10 envelope', 53 => 'catalog #10 1/2 envelope', 54 => '8.5x11', 55 => '8.5x14', 56 => '11x17');
        $tmp = array();
        foreach ($paper_sizes as $asize) {
            $tmp[$asize] = \strtoupper($asize);
        }
        $paper_sizes = $tmp;
        $widget->add_control('dce_form_pdf_size', ['label' => esc_html__('Page Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'a4', 'options' => $paper_sizes, 'condition' => ['dce_form_pdf_converter' => 'dompdf']]);
        $widget->add_control('dce_form_pdf_orientation', ['label' => esc_html__('Page Orientation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['portrait' => esc_html__('Portrait', 'dynamic-content-for-elementor'), 'landscape' => esc_html__('Landscape', 'dynamic-content-for-elementor')], 'toggle' => \false, 'default' => 'portrait', 'condition' => ['dce_form_pdf_converter' => 'dompdf']]);
        $widget->add_control('dce_form_pdf_margin', ['label' => esc_html__('Page Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em'], 'condition' => ['dce_form_pdf_converter' => 'dompdf']]);
        $widget->add_control('dce_form_pdf_button_dpi', ['label' => esc_html__('DPI', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => '96', 'options' => ['72' => '72', '96' => '96', '150' => '150', '200' => '200', '240' => '240', '300' => '300'], 'condition' => ['dce_form_pdf_converter' => 'dompdf']]);
        $widget->add_control('dce_form_section_page', ['label' => esc_html__('Sections Page', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('Force every Template Section in a new page', 'dynamic-content-for-elementor'), 'condition' => ['dce_form_pdf_converter' => 'dompdf']]);
        $widget->add_control('dce_form_pdf_save', ['label' => esc_html__('Save PDF file as Media', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_form_pdf_converter' => 'dompdf']]);
        $widget->add_control('dce_form_pdf_title', ['label' => esc_html__('Title', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => \DynamicContentForElementor\Plugin::instance()->text_templates->get_default_value(['dynamic-shortcodes' => esc_html__('Form data by', 'dynamic-content-for-elementor') . " {form:name} in {date:now @format='Y-m-d H:i:s'}", 'tokens' => esc_html__('Form data by', 'dynamic-content-for-elementor') . ' [field id="name"] in [date|Y-m-d H:i:s]']), 'description' => esc_html__('The PDF file Title', 'dynamic-content-for-elementor'), 'label_block' => \true, 'condition' => ['dce_form_pdf_save!' => '', 'dce_form_pdf_converter' => 'dompdf']]);
        $widget->add_control('dce_form_pdf_content', ['label' => esc_html__('Description', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => \DynamicContentForElementor\Plugin::instance()->text_templates->get_default_value(['dynamic-shortcodes' => '{form:message}', 'tokens' => '[field id="message"]']), 'description' => esc_html__('The PDF file Description', 'dynamic-content-for-elementor'), 'label_block' => \true, 'condition' => ['dce_form_pdf_save!' => '', 'dce_form_pdf_converter' => 'dompdf']]);
        $widget->end_controls_section();
    }
    /**
     * Run
     *
     * Runs the action after submit
     *
     * @access public
     * @param \ElementorPro\Modules\Forms\Classes\Form_Record $record
     * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
     */
    public function run($record, $ajax_handler)
    {
        $fields = Helper::get_form_data($record);
        $settings = $record->get('form_settings');
        try {
            if (!$this->pdf_target_attempted) {
                $this->set_pdf_url_and_path($record);
            }
            $converter = $settings['dce_form_pdf_converter'] ?? 'html';
            if (!$this->pdf_path) {
                $ajax_handler->add_admin_error_message(esc_html__('PDF output path rejected because it resolves outside the uploads directory.', 'dynamic-content-for-elementor'));
                return;
            }
            if ($converter === 'dompdf') {
                $fields_to_expand = ['dce_form_pdf_name', 'dce_form_pdf_folder'];
                foreach ($fields_to_expand as $field) {
                    if (isset($settings[$field])) {
                        $settings[$field] = Plugin::instance()->text_templates->expand_shortcodes_or_callback($settings[$field], ['form-fields' => $record->get('fields')], function ($str) use($fields) {
                            return Helper::get_dynamic_value($str, $fields);
                        });
                    }
                }
                $this->dompdf_converter($settings, $fields, $ajax_handler);
            } else {
                if ($converter === 'svg') {
                    $raw_pdf = $this->svg_converter($settings, $record, $fields, $ajax_handler);
                } elseif ($converter === 'html') {
                    $raw_pdf = $this->html_converter($settings, $record, $fields, $ajax_handler);
                }
                if ($raw_pdf && \file_put_contents($this->pdf_path, $raw_pdf) === \false) {
                    $this->log_pdf_write_failure($this->pdf_path);
                    $ajax_handler->add_admin_error_message(esc_html__('PDF file could not be written.', 'dynamic-content-for-elementor'));
                }
            }
        } catch (\Throwable $e) {
            $ajax_handler->add_admin_error_message($e->getMessage());
        }
    }
    /** Get width, height and unit from svg root node. */
    private static function svg_get_dimensions(string $svg)
    {
        $svg = @\simplexml_load_string($svg);
        // try to get dimensions from width and height attrs:
        if (isset($svg['width'])) {
            \preg_match('/^([\\d\\.,]+)(\\S*)$/', $svg['width'], $matches);
            $width = $matches[1];
            $unit = $matches[2] ?: 'px';
            \preg_match('/^([\\d\\.,]+)(\\S*)$/', $svg['height'], $matches);
            $height = $matches[1];
        } elseif (isset($svg['viewBox'])) {
            // no luck, try with the viewBox attr:
            \preg_match('/^\\s*[\\d\\.,]+\\s+[\\d\\.,]+\\s+([\\d\\.,]+)\\s+([\\d\\.,]+)$/', $svg['viewBox'], $matches);
            $width = $matches[1];
            $height = $matches[2];
            $unit = 'px';
        } else {
            // fallback values (a4 paper):
            $width = 210;
            $unit = 'mm';
            $height = 297;
        }
        // This is to match what Illustrator considers as pixel, which is
        // not the same as tcpdf.
        if ('px' === $unit) {
            $width = \intval($width) / 2.834762;
            $height = \intval($height) / 2.834762;
            $unit = 'mm';
        }
        return ['unit' => $unit, 'width' => $width, 'height' => $height];
    }
    /** Set svg root width and height to the given values */
    private static function svg_set_width_height(string $svg, $dim)
    {
        $svg = @\simplexml_load_string($svg);
        if ($svg === \false) {
            return \false;
        }
        $svg['width'] = $dim['width'] . $dim['unit'];
        $svg['height'] = $dim['height'] . $dim['unit'];
        return $svg->asXML();
    }
    /**
     * Method ac editor seems to output non standard null values, remove
     * them.
     */
    private static function svg_fix_for_methodac(string $svg)
    {
        return \preg_replace('/[\\w-]+="null"/', '', $svg);
    }
    /**
     * Gets an array of SVG strings. Returns them converted to one pdf as a
     * string.
     */
    private static function svg_converter_convert(array $svgs, $disable_imagick)
    {
        $use_imagick = !$disable_imagick && \extension_loaded('imagick');
        // The unit will be the same for all pages. Get it from the first one.
        $dim = self::svg_get_dimensions($svgs[0]);
        $unit = $dim['unit'];
        $pdf = new \DynamicOOOS\TCPDF('P', $unit, 'A4', \true, 'UTF-8', \false);
        $pdf->setPrintHeader(\false);
        $pdf->setPrintFooter(\false);
        // Prevent TCPDF from automatically adding other pages:
        $pdf->SetAutoPageBreak(\false, PDF_MARGIN_BOTTOM);
        if ($use_imagick) {
            $pdf->setRasterizeVectorImages(\true);
        }
        foreach ($svgs as $svg) {
            $dim = self::svg_get_dimensions($svg);
            if ($use_imagick) {
                // The following is necessary in case of missing width and
                // height there could be a mismatch between imagick and tcpdf
                // in terms of resolution.
                $svg = self::svg_set_width_height($svg, $dim);
                if ($svg === \false) {
                    return \false;
                }
            } else {
                $svg = self::svg_fix_for_methodac($svg);
            }
            $width = $dim['width'];
            $height = $dim['height'];
            $orientation = $width >= $height ? 'L' : 'P';
            $pdf->AddPage($orientation, [$width, $height]);
            $pdf->ImageSVG('@' . \trim($svg), 0, 0, $width, $height);
        }
        return $pdf->Output('', 'S');
    }
    /**
     * Look for elements (should be rectangles) that have an SVG id like
     * "form:name". Check if name it corresponds to a form field with a
     * dataURL image inside (a signature).  If so replace the element with
     * the acutual image, it should be in the same place and with the same
     * size.
     */
    private static function replace_template_images($svg, $fields)
    {
        $dom = new \DOMDocument();
        $dom->loadXML($svg);
        $xpath = new \DOMXPath($dom);
        $els = $xpath->query('//*');
        foreach ($els as $el) {
            $el_id = $el->getAttribute('id');
            if (\preg_match('/^form:(\\S+)/', $el_id, $matches)) {
                if (isset($fields[$matches[1]])) {
                    $dataURL = $fields[$matches[1]]['raw_value'];
                    // Replace the rect element with a new image element in the
                    // correct position, by reading position information and
                    // copying it.
                    $x = $el->getAttribute('x');
                    $y = $el->getAttribute('y');
                    $width = $el->getAttribute('width');
                    $height = $el->getAttribute('height');
                    $img = $dom->createElement('image');
                    $img->setAttribute('xlink:href', $dataURL);
                    $img->setAttribute('x', $x);
                    $img->setAttribute('y', $y);
                    $img->setAttribute('width', $width);
                    $img->setAttribute('height', $height);
                    $img->setAttribute('preserveAspectRation', 'none');
                    $el->parentNode->replaceChild($img, $el);
                }
            }
        }
        return $dom->saveXML();
    }
    private function get_field_values($record)
    {
        $raw_fields = $record->get_field([]);
        $values = [];
        foreach ($raw_fields as $field) {
            $values[$field['id']] = $field['value'];
        }
        return $values;
    }
    private function get_raw_field_values($record)
    {
        $raw_fields = $record->get_field([]);
        $values = [];
        foreach ($raw_fields as $field) {
            $values[$field['id']] = $field['raw_value'];
        }
        return $values;
    }
    private function html_converter($settings, $record, $fields, $ajax_handler)
    {
        $form_data = $this->get_field_values($record);
        $raw_form_data = $this->get_raw_field_values($record);
        $template_id = $settings['dce_pdf_html_template'];
        if (!$template_id) {
            $ajax_handler->add_error_message(esc_html__('PDF Generator: Please select an HTML template.', 'dynamic-content-for-elementor'));
            return \false;
        }
        $module = Plugin::instance()->pdf_html_templates;
        $dsh_bindings = ['form-fields' => $record->get('fields')];
        $timber_bindings = ['form' => $form_data, 'form_raw' => $raw_form_data];
        return $module->generate_pdf_from_template_id($template_id, $dsh_bindings, $timber_bindings, \true);
    }
    private function svg_converter($settings, $record, $fields, $ajax_handler)
    {
        $svgs = $settings['dce_form_pdf_svg_code_repeater'];
        if (empty($svgs)) {
            $msg = esc_html__('PDF not generated, no SVG pages found.', 'dynamic-content-for-elementor');
            $ajax_handler->add_error_message($msg);
            return;
        }
        $tfun = function ($svg) use($record, $fields) {
            $svg = self::replace_template_images($svg['text'], $record->get('fields'));
            return Plugin::instance()->text_templates->expand_shortcodes_or_callback($svg, ['form-fields' => $record->get('fields')], function ($str) use($fields) {
                return Helper::get_dynamic_value($str, $fields);
            });
        };
        $svgs = \array_map($tfun, $svgs);
        $raw_pdf = self::svg_converter_convert($svgs, ($settings['dce_form_pdf_disable_imagick'] ?? '') === 'disable');
        if ($raw_pdf === \false) {
            $ajax_handler->add_admin_error_message('PDF: invalid SVG code.');
            return \false;
        }
        return $raw_pdf;
    }
    /**
     * Sanitize the remote resource URLs (image `src`, CSS `url()`/`@import`)
     * referenced by the PDF template before rendering. URLs that do not pass
     * wp_http_validate_url() are dropped; data: and relative references are
     * left untouched.
     *
     * @param string $html
     * @return string
     */
    protected function restrict_remote_resource_urls($html)
    {
        if ($html === '') {
            return $html;
        }
        $keep_if_public = function (string $url) {
            return wp_http_validate_url(\html_entity_decode($url, \ENT_QUOTES)) ? $url : '';
        };
        // src="..." / src='...'
        $html = (string) \preg_replace_callback('/(\\bsrc\\s*=\\s*)(["\'])\\s*(https?:\\/\\/[^"\']+?)\\s*\\2/i', function ($m) use($keep_if_public) {
            return $m[1] . $m[2] . $keep_if_public($m[3]) . $m[2];
        }, $html);
        // CSS url(...) and @import url(...)
        $html = (string) \preg_replace_callback('/url\\(\\s*(["\']?)(https?:\\/\\/[^)"\']+?)\\1\\s*\\)/i', function ($m) use($keep_if_public) {
            return 'url(' . $m[1] . $keep_if_public($m[2]) . $m[1] . ')';
        }, $html);
        return $html;
    }
    public function dompdf_converter($settings, $fields, $ajax_handler = null)
    {
        global $dce_form, $post;
        if (empty($settings['dce_form_pdf_template'])) {
            $ajax_handler->add_error_message(esc_html__('Error: PDF Template not found or not set', 'dynamic-content-for-elementor'));
            return;
        }
        // verify Template
        $template = get_post($settings['dce_form_pdf_template']);
        if (!$template || $template->post_type != 'elementor_library') {
            $ajax_handler->add_error_message(esc_html__('Error: PDF Template not set correctly', 'dynamic-content-for-elementor'));
            return;
        }
        $post = get_post($fields['submitted_on_id']);
        // to retrieve dynamic data from post where the form was submitted
        if (!$this->pdf_path) {
            $ajax_handler->add_admin_error_message(esc_html__('PDF output path rejected because it resolves outside the uploads directory.', 'dynamic-content-for-elementor'));
            return;
        }
        $template_system = \DynamicContentForElementor\Plugin::instance()->template_system;
        $pdf_html = $template_system->build_elementor_template_special(['id' => $settings['dce_form_pdf_template']]);
        $pdf_html = Helper::get_dynamic_value($pdf_html, $fields);
        // add CSS
        $css = Helper::get_post_css($settings['dce_form_pdf_template']);
        // from flex to table
        $css .= '.elementor-section .elementor-container { display: table !important; width: 100% !important; }';
        $css .= '.elementor-row { display: table-row !important; }';
        $css .= '.elementor-column { display: table-cell !important; }';
        $css .= '.elementor-column-wrap, .elementor-widget-wrap { display: block !important; }';
        $css = \str_replace(':not(.elementor-motion-effects-element-type-background) > .elementor-element-populated', ':not(.elementor-motion-effects-element-type-background)', $css);
        $css .= '.elementor-column .elementor-widget-image img { max-width: none !important; }';
        $cssToInlineStyles = new \DynamicOOOS\TijsVerkoyen\CssToInlineStyles\CssToInlineStyles();
        $pdf_html = $cssToInlineStyles->convert($pdf_html, $css);
        $pdf_html = Helper::template_unwrap($pdf_html);
        // link image from url to path
        $site_url = site_url();
        if (is_rtl()) {
            // fix for arabic and hebrew
            $pdf_html .= '<style>* { font-family: DejaVu Sans, sans-serif; }</style>';
        }
        if (!empty($settings['dce_form_section_page'])) {
            $pdf_html .= '<style>.elementor-top-section { page-break-before: always; }.elementor-top-section:first-child { page-break-before: no; }</style>';
        }
        if (!empty($settings['dce_form_pdf_background'])) {
            $bg_path = get_attached_file($settings['dce_form_pdf_background']);
            if ($bg_path) {
                $pdf_html .= '<style>body { background-image: url("' . esc_attr($bg_path) . '"); }</style>';
            }
            $pdf_html .= '<style>body { background-repeat: no-repeat; background-position: center; background-size: cover; }</style>';
        }
        $margin_unit = esc_attr($settings['dce_form_pdf_margin']['unit']);
        $pdf_html .= '<style>@page { margin: ' . \floatval($settings['dce_form_pdf_margin']['top']) . $margin_unit . ' ' . \floatval($settings['dce_form_pdf_margin']['right']) . $margin_unit . ' ' . \floatval($settings['dce_form_pdf_margin']['bottom']) . $margin_unit . ' ' . \floatval($settings['dce_form_pdf_margin']['left']) . $margin_unit . '; }</style>';
        // Stream context used by dompdf when fetching remote assets.
        $context = \stream_context_create(array('ssl' => array('verify_peer' => \true, 'verify_peer_name' => \true), 'http' => array('follow_location' => 0)));
        // Sanitize remote resource URLs referenced by the template.
        $pdf_html = $this->restrict_remote_resource_urls($pdf_html);
        // https://github.com/dompdf/dompdf
        $options = new \DynamicOOOS\Dompdf\Options();
        $options->set('isRemoteEnabled', \true);
        $options->setIsRemoteEnabled(\true);
        // Instantiate and use the dompdf class
        $dompdf = new \DynamicOOOS\Dompdf\Dompdf($options);
        $dompdf->setHttpContext($context);
        $dompdf->loadHtml($pdf_html);
        $dompdf->set_option('isRemoteEnabled', \true);
        $dompdf->set_option('isHtml5ParserEnabled', \true);
        // Setup the paper size and orientation
        $dompdf->setPaper($settings['dce_form_pdf_size'], $settings['dce_form_pdf_orientation']);
        // DPI
        $dompdf->set_option('dpi', $settings['dce_form_pdf_button_dpi']);
        // Render the HTML as PDF
        $dompdf->render();
        // Output the generated PDF to Browser
        $output = $dompdf->output();
        if (!\file_put_contents($this->pdf_path, $output)) {
            $this->log_pdf_write_failure($this->pdf_path);
            $ajax_handler->add_admin_error_message(esc_html__('PDF file could not be written.', 'dynamic-content-for-elementor'));
            return;
        }
        if ($settings['dce_form_pdf_save']) {
            // Insert the post into the database
            // https://codex.wordpress.org/Function_Reference/wp_insert_attachment
            // $filename should be the path to a file in the upload directory.
            $filename = $this->pdf_path;
            // The ID of the post this attachment is for.
            $parent_post_id = $fields['submitted_on_id'];
            // Check the type of file. We'll use this as the 'post_mime_type'.
            $filetype = wp_check_filetype(\basename($filename), null);
            // Get the path to the upload directory.
            $wp_upload_dir = wp_upload_dir();
            // Prepare an array of post data for the attachment.
            $attachment = array('guid' => $wp_upload_dir['url'] . '/' . \basename($filename), 'post_mime_type' => $filetype['type'], 'post_status' => 'inherit', 'post_title' => $settings['dce_form_pdf_title'], 'post_content' => $settings['dce_form_pdf_content']);
            // Insert the attachment.
            $attach_id = wp_insert_attachment($attachment, $filename, $parent_post_id);
            // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
            require_once ABSPATH . 'wp-admin/includes/image.php';
            // Generate the metadata for the attachment, and update the database record.
            $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
            wp_update_attachment_metadata($attach_id, $attach_data);
            if ($attach_id) {
                $dce_form['pdf']['id'] = $attach_id;
                $dce_form['pdf']['title'] = $settings['dce_form_pdf_title'];
                $dce_form['pdf']['description'] = $settings['dce_form_pdf_content'];
                if (!empty($fields) && \is_array($fields)) {
                    foreach ($fields as $akey => $adata) {
                        update_post_meta($attach_id, $akey, $adata);
                    }
                }
            } else {
                $ajax_handler->add_error_message(esc_html__('Error saving PDF as Media', 'dynamic-content-for-elementor'));
            }
        }
    }
    public function on_export($element)
    {
        return $element;
    }
}
