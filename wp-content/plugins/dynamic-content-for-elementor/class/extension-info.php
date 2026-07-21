<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor;

if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
trait ExtensionInfo
{
    public $info;
    public function get_info($info)
    {
        if ($this->info === null) {
            $class = \explode('\\', __CLASS__);
            $class = \array_pop($class);
            $this->info = \DynamicContentForElementor\Extensions::$extensions[$class];
        }
        return $this->info[$info];
    }
    public function get_docs()
    {
        return '';
    }
}
