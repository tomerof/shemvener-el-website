<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Tokens;
use DynamicContentForElementor\Plugin;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Export extends \ElementorPro\Modules\Forms\Classes\Action_Base
{
    public $has_action = \true;
    public function run_once()
    {
        $save_guard = \DynamicContentForElementor\Plugin::instance()->save_guard;
        $save_guard->register_unsafe_control('form', 'dce_form_export_url');
        $save_guard->register_unsafe_control('form', 'dce_form_export_port');
        $save_guard->register_unsafe_control('form', 'dce_form_export_method');
        $save_guard->register_unsafe_control('form', 'dce_form_export_ssl');
        $save_guard->register_unsafe_control('form', 'dce_form_export_empty');
        $save_guard->register_unsafe_control('form', 'dce_form_export_json');
        $save_guard->register_unsafe_control('form', 'dce_form_export_fields::dce_form_export_field_key');
        $save_guard->register_unsafe_control('form', 'dce_form_export_fields::dce_form_export_field_value');
        $save_guard->register_unsafe_control('form', 'dce_form_export_headers::dce_form_export_header_key');
        $save_guard->register_unsafe_control('form', 'dce_form_export_headers::dce_form_export_header_value');
        $save_guard->register_unsafe_control('form', 'dce_form_export_timeout');
        $save_guard->register_unsafe_control('form', 'dce_form_pdf_log');
        $save_guard->register_unsafe_control('form', 'dce_form_pdf_log_path');
        $save_guard->register_unsafe_control('form', 'dce_form_pdf_error');
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
        return 'dce_form_export';
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
     * Get Label
     *
     * Returns the action label
     *
     * @access public
     * @return string
     */
    public function get_label()
    {
        return esc_html__('Export', 'dynamic-content-for-elementor');
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
        $widget->start_controls_section('section_dce_form_export', ['label' => Helper::dce_logo() . $this->get_label(), 'condition' => ['submit_actions' => $this->get_name()]]);
        if (!\DynamicContentForElementor\Helper::can_register_unsafe_controls()) {
            $widget->add_control('admin_notice', ['name' => 'admin_notice', 'type' => Controls_Manager::RAW_HTML, 'raw' => esc_html__('You will need administrator capabilities to edit these settings.', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning']);
            $widget->end_controls_section();
            return;
        }
        Plugin::instance()->text_templates->maybe_add_notice($widget, 'export');
        $widget->add_control('dce_form_export_url', ['label' => esc_html__('Endpoint URL', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'placeholder' => 'https://www.external.ext/save_data.php', 'label_block' => \true]);
        $widget->add_control('dce_form_export_port', ['label' => esc_html__('Port', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::NUMBER, 'placeholder' => '80']);
        $widget->add_control('dce_form_export_method', ['label' => esc_html__('Method', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SELECT, 'options' => ['get' => 'GET', 'post' => 'POST', 'head' => 'HEAD'], 'default' => 'get', 'toggle' => \false, 'label_block' => 'true']);
        $widget->add_control('dce_form_export_ssl', ['label' => esc_html__('Enable SSL Certificate verify', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
        $widget->add_control('dce_form_export_empty', ['label' => esc_html__('Ignore fields with empty value', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $widget->add_control('dce_form_export_json', ['label' => esc_html__('Encode Post Data in JSON', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_form_export_method' => 'post']]);
        $repeater_fields = new \Elementor\Repeater();
        $repeater_fields->add_control('dce_form_export_field_key', ['label' => esc_html__('Field Key', 'dynamic-content-for-elementor'), 'description' => esc_html__('It\'s the key of the parameter in the request', 'dynamic-content-for-elementor') . '<br>?<b>field_key</b>=FieldValue&<b>page</b>=2&<b>txt</b>=Test<br>', 'type' => Controls_Manager::TEXT]);
        $repeater_fields->add_control('dce_form_export_field_value', ['label' => esc_html__('Field Value', 'dynamic-content-for-elementor'), 'description' => esc_html__('It\'s the value of the parameter in the request', 'dynamic-content-for-elementor') . '<br>?field_key=<b>FieldValue</b>&page=<b>2</b>&txt=<b>Test</b><br>', 'type' => Controls_Manager::TEXT]);
        $widget->add_control('dce_form_export_admin_notice', ['name' => 'admin_notice', 'type' => Controls_Manager::RAW_HTML, 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning', 'raw' => esc_html__("If you don't set at least one list argument nothing will be exported", 'dynamic-content-for-elementor')]);
        $widget->add_control('dce_form_export_fields', ['label' => esc_html__('Exported Arguments list', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::REPEATER, 'fields' => $repeater_fields->get_controls(), 'title_field' => '{{{ dce_form_export_field_key }}} = {{{ dce_form_export_field_value }}}', 'prevent_empty' => \false]);
        $repeater_headers = new \Elementor\Repeater();
        $repeater_headers->add_control('dce_form_export_header_key', ['label' => esc_html__('Header Key', 'dynamic-content-for-elementor'), 'placeholder' => 'Content-Type', 'type' => Controls_Manager::TEXT]);
        $repeater_headers->add_control('dce_form_export_header_value', ['label' => esc_html__('Header Value', 'dynamic-content-for-elementor'), 'placeholder' => 'application/json', 'type' => Controls_Manager::TEXT]);
        $widget->add_control('dce_form_export_headers', ['label' => esc_html__('Add Headers', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::REPEATER, 'fields' => $repeater_headers->get_controls(), 'title_field' => '{{{ dce_form_export_header_key }}}: {{{ dce_form_export_header_value }}}', 'default' => [['dce_form_export_header_key' => 'Connection', 'dce_form_export_header_value' => 'keep-alive']], 'prevent_empty' => \false]);
        $widget->add_control('dce_form_export_timeout', ['label' => esc_html__('Request Timeout in seconds', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '']);
        $widget->add_control('dce_form_pdf_log', ['label' => esc_html__('Enable log', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('Create a log for Export result', 'dynamic-content-for-elementor'), 'default' => 'yes']);
        $widget->add_control('dce_form_pdf_log_path', ['label' => esc_html__('Log Path', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => \DynamicContentForElementor\Plugin::instance()->text_templates->get_default_value(['dynamic-shortcodes' => 'elementor/export/log_' . $widget->get_id() . "_{date:now @format='Ymd'}.txt", 'tokens' => 'elementor/export/log_' . $widget->get_id() . '_[date|Ymd].txt']), 'label_block' => \true, 'condition' => ['dce_form_pdf_log!' => '']]);
        $widget->add_control('dce_form_pdf_error', ['label' => esc_html__('Show error on failure', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => esc_html__('If the remote request fails (not response code 200) then an error is going to be displayed', 'dynamic-content-for-elementor'), 'default' => 'yes']);
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
        $configured_url = isset($settings['dce_form_export_url']) && \is_string($settings['dce_form_export_url']) ? $settings['dce_form_export_url'] : '';
        $configured_port = isset($settings['dce_form_export_port']) && \is_scalar($settings['dce_form_export_port']) ? (string) $settings['dce_form_export_port'] : '';
        $simple_keys_to_expand = ['dce_form_export_url', 'dce_form_export_port', 'dce_form_export_timeout', 'dce_form_pdf_log', 'dce_form_pdf_log_path'];
        foreach ($simple_keys_to_expand as $key) {
            if (isset($settings[$key])) {
                $settings[$key] = Plugin::instance()->text_templates->expand_shortcodes_or_callback($settings[$key], ['form-fields' => $record->get('fields')], function ($str) use($fields) {
                    return Helper::get_dynamic_value($str, $fields);
                });
            }
        }
        $repeater_keys_to_expand = ['dce_form_export_fields', 'dce_form_export_headers'];
        foreach ($repeater_keys_to_expand as $key) {
            foreach ($settings[$key] as $i => $s) {
                $settings[$key][$i] = \array_map(function ($item) use($fields, $record) {
                    return Plugin::instance()->text_templates->expand_shortcodes_or_callback($item, ['form-fields' => $record->get('fields')], function ($str) use($fields) {
                        return Helper::get_dynamic_value($str, $fields);
                    });
                }, $s);
            }
        }
        $this->export($fields, $settings, $ajax_handler, $record, $configured_url, $configured_port);
    }
    /**
     * @param string $path
     * @return array<int,string>|false
     */
    private function normalize_relative_log_path($path)
    {
        if (!\is_string($path)) {
            return \false;
        }
        $path = \trim($path);
        if ('' === $path || \strpos($path, '://') !== \false) {
            return \false;
        }
        if (\preg_match('/^(?:[a-zA-Z]:[\\/\\\\]|[\\/\\\\]{1,2})/', $path)) {
            return \false;
        }
        $segments = \array_values(\array_filter(\explode('/', wp_normalize_path($path)), static function ($segment) {
            return '' !== $segment;
        }));
        if (empty($segments)) {
            return \false;
        }
        foreach ($segments as $segment) {
            if ('.' === $segment || '..' === $segment) {
                return \false;
            }
        }
        return $segments;
    }
    /**
     * @param string $upload_dir
     * @param string $log_path
     * @return array<string,string>|false
     */
    private function get_safe_log_target($upload_dir, $log_path)
    {
        $upload_dir = \realpath($upload_dir);
        if (!\is_string($upload_dir)) {
            return \false;
        }
        $upload_dir = wp_normalize_path($upload_dir);
        $upload_dir_prefix = \rtrim($upload_dir, '/') . '/';
        $segments = $this->normalize_relative_log_path($log_path);
        if (\false === $segments) {
            return \false;
        }
        $filename = \array_pop($segments);
        if (!\is_string($filename) || '' === $filename) {
            return \false;
        }
        $log_dir = $upload_dir;
        foreach ($segments as $segment) {
            $next_dir = $log_dir . '/' . $segment;
            if (\file_exists($next_dir)) {
                $resolved_dir = \realpath($next_dir);
                if (!\is_string($resolved_dir)) {
                    return \false;
                }
                $resolved_dir = wp_normalize_path($resolved_dir);
                if (\strpos($resolved_dir . '/', $upload_dir_prefix) !== 0) {
                    return \false;
                }
                if (!\is_dir($resolved_dir)) {
                    return \false;
                }
                $log_dir = $resolved_dir;
            } else {
                $log_dir = $next_dir;
            }
        }
        $log_file = $log_dir . '/' . $filename;
        if (\file_exists($log_file)) {
            $resolved_file = \realpath($log_file);
            if (!\is_string($resolved_file)) {
                return \false;
            }
            $resolved_file = wp_normalize_path($resolved_file);
            if (\strpos($resolved_file, $upload_dir_prefix) !== 0) {
                return \false;
            }
            $log_file = $resolved_file;
        }
        return ['dir' => $log_dir, 'file' => $log_file];
    }
    /**
     * @param string $path
     * @return void
     */
    private function log_blocked_log_path($path)
    {
        $path = \str_replace(["\r", "\n"], ' ', wp_normalize_path($path));
        \error_log('Dynamic Content for Elementor - Export for Elementor Pro Form: blocked log path outside uploads directory: ' . $path);
    }
    /**
     * @param string $path
     * @return void
     */
    private function log_log_write_failure($path)
    {
        $path = \str_replace(["\r", "\n"], ' ', wp_normalize_path($path));
        \error_log('Dynamic Content for Elementor - Export for Elementor Pro Form: failed to write log file: ' . $path);
    }
    /**
     * @return string|false
     */
    private function add_export_port(string $url, string $port)
    {
        if ('' === $url) {
            return \false;
        }
        if ('' === $port) {
            return $url;
        }
        if (!\ctype_digit($port) || (int) $port < 1 || (int) $port > 65535) {
            return \false;
        }
        $pieces = \explode('/', $url);
        if (\count($pieces) < 3) {
            return \false;
        }
        $pieces[2] .= ':' . $port;
        return \implode('/', $pieces);
    }
    /**
     * @return array{scheme:string,host:string,port:int}|false
     */
    private function get_export_origin(string $url)
    {
        if ('' === $url) {
            return \false;
        }
        $parts = wp_parse_url($url);
        if (!\is_array($parts) || empty($parts['scheme']) || empty($parts['host']) || isset($parts['user']) || isset($parts['pass'])) {
            return \false;
        }
        $scheme = \strtolower($parts['scheme']);
        $host = \strtolower(\trim($parts['host'], '.'));
        if (!\in_array($scheme, ['http', 'https'], \true) || '' === $host || \false !== \strpbrk($host, ':#?[]')) {
            return \false;
        }
        $port = isset($parts['port']) ? (int) $parts['port'] : ('https' === $scheme ? 443 : 80);
        return ['scheme' => $scheme, 'host' => $host, 'port' => $port];
    }
    /**
     * @param array{scheme:string,host:string,port:int} $configured_origin
     * @param array{scheme:string,host:string,port:int} $resolved_origin
     */
    private function export_origins_match(array $configured_origin, array $resolved_origin) : bool
    {
        return $configured_origin['scheme'] === $resolved_origin['scheme'] && $configured_origin['host'] === $resolved_origin['host'] && $configured_origin['port'] === $resolved_origin['port'];
    }
    /**
     * @param array<string,mixed> $args
     * @param array{scheme:string,host:string,port:int} $configured_origin
     * @return array<string,mixed>|\WP_Error
     */
    private function send_export_request(string $url, array $args, array $configured_origin)
    {
        $allow_host = function ($is_external, $host, $request_url) use($configured_origin) {
            $request_origin = \is_string($request_url) ? $this->get_export_origin($request_url) : \false;
            return \is_array($request_origin) && $this->export_origins_match($configured_origin, $request_origin) ? \true : $is_external;
        };
        $allow_port = function ($ports, $host, $request_url) use($configured_origin) {
            $request_origin = \is_string($request_url) ? $this->get_export_origin($request_url) : \false;
            if (\is_array($ports) && \is_array($request_origin) && $this->export_origins_match($configured_origin, $request_origin) && !\in_array($configured_origin['port'], $ports, \true)) {
                $ports[] = $configured_origin['port'];
            }
            return $ports;
        };
        add_filter('http_request_host_is_external', $allow_host, \PHP_INT_MAX, 3);
        add_filter('http_allowed_safe_ports', $allow_port, \PHP_INT_MAX, 3);
        try {
            return wp_safe_remote_request($url, $args);
        } finally {
            remove_filter('http_request_host_is_external', $allow_host, \PHP_INT_MAX);
            remove_filter('http_allowed_safe_ports', $allow_port, \PHP_INT_MAX);
        }
    }
    protected function export($fields, $settings, $ajax_handler, $record, string $configured_url = '', string $configured_port = '')
    {
        $export_data = [];
        if (!empty($settings['dce_form_export_fields'])) {
            foreach ($settings['dce_form_export_fields'] as $akey => $adata) {
                $pvalue = $adata['dce_form_export_field_value'];
                if ($pvalue == '' && $settings['dce_form_export_empty']) {
                    continue;
                }
                if (\substr(\trim($pvalue), 0, 1) == '{' && \substr(\trim($pvalue), -1, 1) == '}' || \substr(\trim($pvalue), 0, 1) == '[' && \substr(\trim($pvalue), -1, 1) == ']') {
                    $pvalue = \json_decode($pvalue);
                }
                $export_data[$adata['dce_form_export_field_key']] = $pvalue;
            }
        }
        $args = [];
        $exp_url = isset($settings['dce_form_export_url']) && \is_string($settings['dce_form_export_url']) ? $settings['dce_form_export_url'] : '';
        $resolved_port = isset($settings['dce_form_export_port']) && \is_scalar($settings['dce_form_export_port']) ? (string) $settings['dce_form_export_port'] : '';
        $configured_endpoint = $this->add_export_port($configured_url, $configured_port);
        $exp_url = $this->add_export_port($exp_url, $resolved_port);
        if (\false !== $configured_endpoint && \false !== $exp_url) {
            if ($settings['dce_form_export_method'] == 'get') {
                if (!empty($export_data)) {
                    foreach ($export_data as $akey => $avalue) {
                        $exp_url = add_query_arg($akey, $avalue, $exp_url);
                    }
                }
            } elseif ($settings['dce_form_export_json']) {
                $args['body'] = (string) wp_json_encode($export_data);
                $args['headers'] = ['Content-Type' => 'application/json; charset=utf-8'];
                $args['data_format'] = 'body';
            } else {
                $args['body'] = $export_data;
            }
            $timeout = $settings['dce_form_export_timeout'];
            if (\is_numeric($timeout) && $timeout > 0) {
                $args['timeout'] = (int) $timeout;
            }
            if (!empty($settings['dce_form_export_headers'])) {
                foreach ($settings['dce_form_export_headers'] as $akey => $adata) {
                    $pvalue = Plugin::instance()->text_templates->expand_shortcodes_or_callback($adata['dce_form_export_header_value'], ['form-fields' => $record->get('fields')], function ($str) use($fields) {
                        return Helper::get_dynamic_value($str, $fields);
                    });
                    $args['headers'][$adata['dce_form_export_header_key']] = $pvalue;
                }
            }
            $args['sslverify'] = !empty($settings['dce_form_export_ssl']);
            $args['redirection'] = 5;
            $req = 'wp_remote_' . $settings['dce_form_export_method'];
            $method = \strtolower($settings['dce_form_export_method']);
            if (!\in_array($method, ['get', 'post', 'head'], \true)) {
                $ajax_handler->add_admin_error_message('DCE Error: AHPH6P');
                return;
            }
            $args['method'] = \strtoupper($method);
            $configured_origin = $this->get_export_origin($configured_endpoint);
            $resolved_origin = $this->get_export_origin($exp_url);
            $ret = \false === $configured_origin || \false === $resolved_origin || !$this->export_origins_match($configured_origin, $resolved_origin) ? new \WP_Error('dce_export_endpoint', esc_html__('Export endpoint URL is not allowed.', 'dynamic-content-for-elementor')) : $this->send_export_request($exp_url, $args, $configured_origin);
            if (!is_wp_error($ret)) {
                $log = 'Form Export: OK';
            } else {
                $ret_code = wp_remote_retrieve_response_code($ret);
                $log = 'Form Export: ERROR ' . $ret_code;
                if ($settings['dce_form_pdf_error']) {
                    $ajax_handler->add_admin_error_message($ret->get_error_message());
                    $ajax_handler->add_error_message(\ElementorPro\Modules\Forms\Classes\Ajax_Handler::get_default_message(\ElementorPro\Modules\Forms\Classes\Ajax_Handler::SERVER_ERROR, $settings));
                }
            }
            if ($settings['dce_form_pdf_log']) {
                $ret_body = $ret;
                $log = $log . ' - ' . $req . \PHP_EOL;
                $log .= 'request_url: ' . $exp_url . \PHP_EOL;
                if ($settings['dce_form_export_method'] == 'post') {
                    $log .= 'request_data: ' . \var_export($args['body'] ?? '', \true) . \PHP_EOL;
                }
                $log .= 'return_body: ' . \var_export($ret_body, \true);
                $log = \PHP_EOL . '[' . \date('Y-m-d H:i:s') . '] ' . $log;
                $upload = wp_upload_dir();
                $upload_dir = $upload['basedir'];
                $log_target = $this->get_safe_log_target($upload_dir, $settings['dce_form_pdf_log_path']);
                if (\false === $log_target) {
                    $this->log_blocked_log_path((string) $settings['dce_form_pdf_log_path']);
                    $ajax_handler->add_admin_error_message(esc_html__('Export log path rejected because it resolves outside the uploads directory.', 'dynamic-content-for-elementor'));
                } elseif (!\is_dir($log_target['dir']) && !wp_mkdir_p($log_target['dir']) || !\file_put_contents($log_target['file'], $log, \FILE_APPEND)) {
                    $this->log_log_write_failure($log_target['file']);
                    $ajax_handler->add_admin_error_message(esc_html__('Export log could not be written.', 'dynamic-content-for-elementor'));
                }
            }
        }
    }
    public function on_export($element)
    {
        unset($element['settings']['dce_form_export_url'], $element['settings']['dce_form_export_port'], $element['settings']['dce_form_export_headers'], $element['settings']['dce_form_pdf_log_path']);
        return $element;
    }
}
