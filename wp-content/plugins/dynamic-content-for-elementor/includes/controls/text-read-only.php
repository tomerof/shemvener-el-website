<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-FileCopyrightText: Elementor
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Controls;

use Elementor\Control_Select2;
use Elementor\Modules\DynamicTags\Module as TagsModule;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly.
}
class Control_Text_Read_Only extends \Elementor\Base_Data_Control
{
    /**
     * @return string
     */
    public function get_type()
    {
        return 'dce-text-readonly';
    }
    /**
     * @return array<string,mixed> Control default settings.
     */
    protected function get_default_settings()
    {
        return ['label_block' => \true];
    }
    /**
     * @return void
     */
    public function content_template()
    {
        ?>
		<div class="elementor-control-field">
			<label for="{{ data._cid }}" class="elementor-control-title">{{{ data.label }}}</label>
			<div class="elementor-control-input-wrapper">
				<input type="text" class="elementor-control-tag-area" data-setting="{{ data.name }}" readonly="readonly" style="background-color: #f3f4f5; cursor: not-allowed;">
			</div>
			<# if ( data.description ) { #>
				<div class="elementor-control-field-description">{{{ data.description }}}</div>
			<# } #>
		</div>
		<?php 
    }
}
