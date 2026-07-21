<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Integrations;

if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly.
}
class Manager
{
    /**
     * @var SearchAndFilterPro
     */
    public $search_and_filter_pro;
    /**
     * @var AdvancedCustomFields
     */
    public $advanced_custom_fields;
    public function __construct()
    {
        $this->search_and_filter_pro = new \DynamicContentForElementor\Integrations\SearchAndFilterPro();
        $this->advanced_custom_fields = new \DynamicContentForElementor\Integrations\AdvancedCustomFields();
    }
}
