<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Extensions;

use ElementorPro\Modules\Forms\Classes\Form_Record;
use ElementorPro\Modules\Forms\Classes\Ajax_Handler;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class UniqueId extends \ElementorPro\Modules\Forms\Fields\Field_Base
{
    public $has_action = \false;
    public $depended_scripts = [];
    public $depended_styles = [];
    /**
     * @return array<string>
     */
    public function get_script_depends() : array
    {
        return $this->depended_scripts;
    }
    public function get_name()
    {
        return 'Unique ID';
    }
    public function get_label()
    {
        return esc_html__('Unique ID', 'dynamic-content-for-elementor');
    }
    public function get_type()
    {
        return 'dce_unique_id';
    }
    /**
     * @return array<string>
     */
    public function get_style_depends() : array
    {
        return $this->depended_styles;
    }
    public function render($item, $item_index, $form)
    {
        $form->add_render_attribute('input' . $item_index, 'type', 'hidden', \true);
        $value = esc_html__('This field value is not available before submit', 'dynamic-content-for-elementor');
        $form->add_render_attribute('input' . $item_index, 'value', $value, \true);
        echo '<input ' . $form->get_render_attribute_string('input' . $item_index) . '>';
    }
    public function process_field($field, Form_Record $record, Ajax_Handler $ajax_handler)
    {
        $record->update_field($field['id'], 'value', \uniqid());
    }
}
