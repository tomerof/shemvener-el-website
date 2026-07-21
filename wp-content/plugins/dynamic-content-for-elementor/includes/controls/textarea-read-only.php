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
class Control_Textarea_Read_Only extends \Elementor\Control_Textarea
{
    /**
     * @return string
     */
    public function get_type()
    {
        return 'dce-textarea-readonly';
    }
    /**
     * @return array<string,mixed>
     */
    protected function get_default_settings()
    {
        return ['label_block' => \true, 'rows' => 3, 'placeholder' => '', 'ai' => ['active' => \false], 'dynamic' => ['active' => \false]];
    }
    /**
     * @return void
     */
    public function content_template()
    {
        ?>
		<div class="elementor-control-field">
			<label for="<?php 
        $this->print_control_uid();
        ?>" class="elementor-control-title">{{{ data.label }}}</label>
			<div class="elementor-control-input-wrapper elementor-control-dynamic-switcher-wrapper">
				<textarea id="<?php 
        $this->print_control_uid();
        ?>" class="elementor-control-tag-area" rows="{{ data.rows }}" data-setting="{{ data.name }}" placeholder="{{ view.getControlPlaceholder() }}" readonly="readonly" style="background-color: #f3f4f5; cursor: not-allowed;"></textarea>
			</div>
		</div>
		<# if ( data.description ) { #>
			<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php 
    }
}
