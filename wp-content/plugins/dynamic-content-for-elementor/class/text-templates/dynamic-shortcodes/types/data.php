<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\TextTemplates\DynamicShortcodes\Types;

use DynamicShortcodes\Core\Shortcodes\BaseShortcode;
class Data extends BaseShortcode
{
    /**
     * @param mixed $context
     * @return array<string>
     */
    public static function get_shortcode_types($context)
    {
        return ['data'];
    }
    /**
     * @return mixed
     */
    public function evaluate()
    {
        $this->arity_check(1, 1);
        $this->init_keyargs([]);
        $key = $this->get_arg(0, 'string');
        $data = $this->context['dce-manager']->get_context_data();
        if (!isset($data[$key])) {
            $this->evaluation_error(esc_html__('Type of data unrecognized or unavailable here', 'dynamic-content-for-elementor'));
        }
        return $data[$key];
    }
}
