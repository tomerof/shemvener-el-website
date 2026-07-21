<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\AdminPages\Features;

use DynamicContentForElementor\Helper;
abstract class GroupedListTab extends \DynamicContentForElementor\AdminPages\Features\ListTab
{
    public abstract function get_groups();
    public abstract function get_groups_key();
    public function render_list()
    {
        $features = $this->features;
        $elementor_disabled_elements = (array) get_option('elementor_disabled_elements', []);
        foreach ($this->get_groups() as $group_name => $group_label) {
            $group_features = wp_list_filter($features, [$this->get_groups_key() => $group_name]);
            if (empty($group_features)) {
                continue;
            }
            $active_count = 0;
            foreach ($group_features as $f) {
                if ($f['status'] !== 'active') {
                    continue;
                }
                if (!empty($f['plugin_depends']) && !Helper::is_dependencies_satisfied($f['plugin_depends'])) {
                    continue;
                }
                if ($f['type'] === 'widget' && isset($f['name']) && \in_array($f['name'], $elementor_disabled_elements, \true)) {
                    continue;
                }
                ++$active_count;
            }
            $inactive_count = \count($group_features) - $active_count;
            $total_count = \count($group_features);
            $activate_label = esc_html__('Activate all', 'dynamic-content-for-elementor');
            $deactivate_label = esc_html__('Deactivate all', 'dynamic-content-for-elementor');
            ?>
			<div class="dce-feature-group">
				<div class="dce-feature-group-header">
					<div style="display:flex;align-items:center;">
						<span class="dce-group-arrow">&#9660;</span>
						<span class="dce-group-name"><?php 
            echo esc_html($group_label);
            ?></span>
						<span class="dce-group-count"><?php 
            echo esc_html((string) $total_count);
            ?></span>
					</div>
					<div class="dce-group-bulk">
						<a href="#" class="bulk-activate dce-group-activate-all"><?php 
            echo esc_html($activate_label);
            ?></a>
						<span style="color:#ccc;">|</span>
						<a href="#" class="bulk-deactivate dce-group-deactivate-all"><?php 
            echo esc_html($deactivate_label);
            ?></a>
					</div>
					<div class="dce-group-summary">
						<?php 
            if ($active_count > 0) {
                ?>
							<span class="active-count">&#9679; <?php 
                echo esc_html((string) $active_count);
                ?> active</span>
						<?php 
            }
            if ($inactive_count > 0) {
                ?>
							<span class="inactive-count">&#9679; <?php 
                echo esc_html((string) $inactive_count);
                ?> inactive</span>
						<?php 
            }
            ?>
					</div>
				</div>
				<div class="dce-feature-group-body">
					<?php 
            foreach ($group_features as $fname => $finfo) {
                $this->show_feature($fname, $finfo);
            }
            ?>
				</div>
			</div>
			<?php 
        }
    }
}
