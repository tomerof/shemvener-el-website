<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use DynamicContentForElementor\Plugin;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
abstract class WidgetPrototype extends Widget_Base
{
    /**
     * @var array
     */
    public $settings;
    public $categories;
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $title;
    /**
     * @var string
     */
    public $description;
    /**
     * @var string
     */
    public $icon;
    /**
     * @var array<string>
     */
    public $plugin_depends = [];
    /**
     * @var string
     */
    public $doc_url = 'https://www.dynamic.ooo';
    /**
     * @var array<string>
     */
    public $keywords = [];
    /**
     * @var boolean
     */
    public $admin_only;
    /**
     * @var array<string>
     */
    public $tags;
    /**
     * Raw Data.
     *
     * Holds all the raw data including the element type, the child elements,
     * the user data.
     *
     * @access public
     *
     * @var null|array
     */
    public $data;
    public static $info;
    public function __construct($data = [], $args = null)
    {
        parent::__construct($data, $args);
        $class = \explode('\\', \get_called_class());
        $class = \array_pop($class);
        $class = \substr(\get_called_class(), 27);
        // remove main namespace prefix
        $info = Plugin::instance()->features->filter(['class' => $class]);
        $info = \reset($info);
        if (isset($info['category'])) {
            $this->categories = $info['category'];
        }
        if (isset($info['name'])) {
            $this->name = $info['name'];
        }
        if (isset($info['title'])) {
            $this->title = $info['title'];
        }
        if (isset($info['description'])) {
            $this->description = $info['description'];
        }
        if (isset($info['icon'])) {
            $this->icon = $info['icon'];
        }
        if (isset($info['plugin_depends'])) {
            $this->plugin_depends = $info['plugin_depends'];
        }
        if (isset($info['doc_url'])) {
            $this->doc_url = DCE_FEATURES_URL . $info['doc_url'];
        }
        if (isset($info['keywords'])) {
            $this->keywords = $info['keywords'];
        }
        $this->admin_only = $info['admin_only'] ?? \false;
        $this->tags = $info['tag'] ?? [];
    }
    public function run_once()
    {
        if ($this->admin_only) {
            \DynamicContentForElementor\Plugin::instance()->save_guard->register_unsafe_widget($this->get_name());
        }
    }
    /**
     * @return string
     */
    public function get_name()
    {
        return $this->name;
    }
    /**
     * @return string
     */
    public function get_title()
    {
        return $this->title;
    }
    /**
     * @return string
     */
    public function get_description()
    {
        return $this->description;
    }
    /**
     * @return string
     */
    public function get_docs()
    {
        return $this->doc_url;
    }
    /**
     * @return array<string>
     */
    public function get_keywords()
    {
        return $this->keywords;
    }
    /**
     * @return string
     */
    public function get_help_url()
    {
        return 'https://help.dynamic.ooo';
    }
    /**
     * @return string
     */
    public function get_custom_help_url()
    {
        return $this->get_docs();
    }
    /**
     * @return string
     */
    public function get_icon()
    {
        return $this->icon;
    }
    /**
     * Is Reload Preview Required
     *
     * @return boolean
     */
    public function is_reload_preview_required()
    {
        return \false;
    }
    /**
     * Get Categories
     *
     * @return array<string>
     */
    public function get_categories()
    {
        return ['dynamic-content-for-elementor-' . \strtolower($this->categories)];
    }
    /**
     * @return array<string>
     */
    public function get_plugin_depends()
    {
        return $this->plugin_depends;
    }
    /**
     * Register Controls
     *
     * @return void
     */
    protected final function register_controls()
    {
        if ($this->admin_only && !Helper::can_register_unsafe_controls()) {
            $this->register_controls_non_admin_notice();
        } else {
            $this->safe_register_controls();
        }
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected abstract function safe_register_controls();
    /**
     * Show a notice for non administrators
     *
     * @return void
     */
    protected function register_controls_non_admin_notice()
    {
        $this->start_controls_section('section_non_admin', ['label' => $this->get_title() . esc_html__(' - Notice', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT]);
        $this->add_control('html_notice', ['type' => Controls_Manager::RAW_HTML, 'raw' => esc_html__('You will need administrator capabilities to edit this widget.', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning']);
        $this->end_controls_section();
    }
    /**
     * @return boolean
     */
    public function show_in_panel()
    {
        if ($this->admin_only && !Helper::current_user_is_admin()) {
            return \false;
        }
        return \true;
    }
    /**
     * Render
     *
     * @return void
     */
    protected final function render()
    {
        if ($this->admin_only && !Helper::can_register_unsafe_controls()) {
            $this->render_non_admin_notice();
        }
        if (\in_array('loop', $this->tags, \true)) {
            $this->add_render_attribute('_wrapper', 'class', 'dce-fix-background-loop');
        }
        $this->safe_render();
    }
    /**
     * Safe Render
     *
     * @return void
     */
    protected abstract function safe_render();
    /**
     * Render Non Admin Notice
     *
     * @return void
     */
    protected function render_non_admin_notice()
    {
        _e('You will need administrator capabilities to edit this widget.', 'dynamic-content-for-elementor');
    }
    /**
     * Content Template
     *
     * @return void
     */
    protected function content_template()
    {
    }
}
