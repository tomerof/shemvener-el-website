<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
class LazyIframe extends \DynamicContentForElementor\Widgets\Iframe
{
    /**
     * @return array<string>
     */
    public function get_script_depends()
    {
        return ['dce-lazy-iframe'];
    }
    /**
     * @return array<string>
     */
    public function get_style_depends()
    {
        return ['dce-lazy-iframe'];
    }
    /**
     * @return string
     */
    protected function get_src_attribute()
    {
        return 'data-src';
    }
}
