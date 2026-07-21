<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Extensions\DynamicTags;

use DynamicContentForElementor\Extensions\ExtensionPrototype;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class WooProductCategories extends ExtensionPrototype
{
    public function __construct()
    {
        parent::__construct();
        $this->add_dynamic_tag('WooProductCategories');
    }
}
