<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\TextTemplates\DynamicShortcodes\Types;

use DynamicShortcodes\Core\Shortcodes\BaseShortcode;
use ElementorPro\Modules\Forms\Fields\Upload;
use ElementorPro\Modules\Forms\Classes\Form_Record;
class Form extends BaseShortcode
{
    /**
     * @param mixed $context
     * @return array<string>
     */
    public static function get_shortcode_types($context)
    {
        return ['form'];
    }
    /**
     * @return mixed
     */
    public function evaluate()
    {
        $this->arity_check(1, 1);
        $this->init_keyargs(['raw' => [], 'not-empty' => []]);
        $key = $this->get_arg(0, 'string');
        $data = $this->context['dce-manager']->get_context_data();
        if (!isset($data['form-fields'])) {
            $this->evaluation_error(esc_html__('Form fields not found', 'dynamic-content-for-elementor'));
        }
        $fields = $data['form-fields'];
        if (!isset($fields['array']) && $key === 'array') {
            return $fields;
        }
        if (!isset($fields['all-fields']) && $key === 'all-fields') {
            $filled = !$this->get_bool_keyarg('not-empty');
            return $this->all_fields_formatted($fields, $filled);
        }
        $raw = $this->get_bool_keyarg('raw');
        if (!isset($fields[$key])) {
            return null;
        }
        return $fields[$key][$raw ? 'raw_value' : 'value'];
    }
    /**
     * @param array<string,mixed> $fields
     * @param boolean $filled
     * @return string
     *
     * SPDX-FileCopyrightText: Elementor
     * SPDX-License-Identifier: GPL-3.0-or-later
     */
    private function all_fields_formatted($fields, $filled)
    {
        $text = '';
        foreach ($fields as $field) {
            // Skip upload fields that only attached to the email
            if (isset($field['attachment_type']) && Upload::MODE_ATTACH === $field['attachment_type']) {
                continue;
            }
            if (!$filled && empty($field['value'])) {
                continue;
            }
            $formatted = $this->field_formatted($field);
            if ('textarea' === $field['type']) {
                $formatted = \str_replace(["\r\n", "\n", "\r"], '<br />', $formatted);
            }
            $text .= $formatted . '<br />';
        }
        return $text;
    }
    /**
     * @param array<mixed> $field
     * @return string
     *
     * SPDX-FileCopyrightText: Elementor
     * SPDX-License-Identifier: GPL-3.0-or-later
     */
    private function field_formatted($field)
    {
        $formatted = '';
        if (!empty($field['title'])) {
            $formatted = \sprintf('%s: %s', $field['title'], $field['value']);
        } elseif (!empty($field['value'])) {
            $formatted = \sprintf('%s', $field['value']);
        }
        return $formatted;
    }
}
