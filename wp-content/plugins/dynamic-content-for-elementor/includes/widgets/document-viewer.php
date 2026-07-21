<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
class DocumentViewer extends \DynamicContentForElementor\Widgets\Iframe
{
    /**
     * @param string $url
     * @return string
     */
    protected function get_src($url)
    {
        return 'https://docs.google.com/viewer?embedded=true&url=' . \urlencode($url);
    }
}
