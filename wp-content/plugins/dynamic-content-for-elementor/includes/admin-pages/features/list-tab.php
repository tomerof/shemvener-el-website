<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\AdminPages\Features;

use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Plugin;
abstract class ListTab
{
    private $name;
    private $label;
    // Contains all features for which this tab is responsible for:
    protected $features;
    public function __construct($name)
    {
        $this->name = $name;
        $this->features = $this->get_all_tab_features();
    }
    public function get_name()
    {
        return $this->name;
    }
    public abstract function get_label();
    public abstract function get_all_tab_features();
    public function get_count()
    {
        return \count($this->features);
    }
    public function should_display_count()
    {
        return \true;
    }
    public function are_all_active()
    {
        return !empty(wp_list_filter($this->features, ['status' => 'inactive']));
    }
    public function render_toggle_all_button()
    {
        $total = \count($this->features);
        $active = 0;
        $elementor_disabled_elements = (array) get_option('elementor_disabled_elements', []);
        foreach ($this->features as $f) {
            if ($f['status'] !== 'active') {
                continue;
            }
            if (!empty($f['plugin_depends']) && !Helper::is_dependencies_satisfied($f['plugin_depends'])) {
                continue;
            }
            if ($f['type'] === 'widget' && isset($f['name']) && \in_array($f['name'], $elementor_disabled_elements, \true)) {
                continue;
            }
            ++$active;
        }
        $label = $this->get_label();
        ?>
		<div class="dce-features-header">
			<div>
				<h1><?php 
        echo esc_html($label);
        ?></h1>
				<span class="dce-features-subtitle"><?php 
        echo esc_html((string) $active);
        ?> of <?php 
        echo esc_html((string) $total);
        ?> active</span>
			</div>
			<div class="dce-features-bulk-actions">
				<a href="#" class="bulk-activate" id="dce-feature-activate-all"><?php 
        esc_html_e('Activate all', 'dynamic-content-for-elementor');
        ?></a>
				&nbsp;|&nbsp;
				<a href="#" class="bulk-deactivate" id="dce-feature-deactivate-all"><?php 
        esc_html_e('Deactivate all', 'dynamic-content-for-elementor');
        ?></a>
			</div>
		</div>
		<?php 
    }
    public function show_feature($feature_name, $feature_info)
    {
        $is_active = $feature_info['status'] === 'active';
        $plugin_dependencies_not_satisfied = Helper::get_missing_dependencies($feature_info['plugin_depends']);
        $php_version_not_satisfied = isset($feature_info['minimum_php']) && \version_compare(\phpversion(), $feature_info['minimum_php'], '<');
        $is_bundled_feature = isset($feature_info['activated_by']);
        $elementor_disabled_elements = get_option('elementor_disabled_elements', []);
        $is_feature_disabled_in_elementor = $feature_info['type'] === 'widget' && isset($feature_info['name']) && \in_array($feature_info['name'], (array) $elementor_disabled_elements, \true);
        $row_classes = ['dce-feature-row'];
        if (!$is_active) {
            $row_classes[] = 'is-inactive';
        }
        if (!empty($plugin_dependencies_not_satisfied)) {
            $row_classes[] = 'required-plugin';
        }
        if ($php_version_not_satisfied) {
            $row_classes[] = 'required-php';
        }
        if ($is_feature_disabled_in_elementor) {
            $row_classes[] = 'elementor-disabled';
        }
        if ($is_bundled_feature) {
            $row_classes[] = 'bundled-feature';
        }
        // Calculate usage for data attribute
        $usage_count = 0;
        if ($this->should_calculate_usage() && isset($feature_info['name'])) {
            $elementor_controls_usage = get_option('elementor_controls_usage');
            if ($elementor_controls_usage) {
                $usage_count = $this->calculate_usage($feature_info['name'], $elementor_controls_usage);
            }
        }
        $can_toggle = empty($plugin_dependencies_not_satisfied) && !$php_version_not_satisfied && !$is_bundled_feature && !$is_feature_disabled_in_elementor;
        ?>
		<div class="<?php 
        echo esc_attr(\implode(' ', $row_classes));
        ?>"
			data-feature="<?php 
        echo esc_attr($feature_name);
        ?>"
			data-usage="<?php 
        echo esc_attr((string) $usage_count);
        ?>"
			data-title="<?php 
        echo esc_attr(\strtolower($feature_info['title']));
        ?>"
			data-description="<?php 
        echo esc_attr(\strtolower($feature_info['description'] ?? ''));
        ?>">

			<?php 
        if ($can_toggle) {
            ?>
				<div class="dce-toggle">
					<input type="checkbox"
							id="dce-feature-<?php 
            echo esc_attr($feature_name);
            ?>"
							<?php 
            checked($is_active);
            ?>>
					<label for="dce-feature-<?php 
            echo esc_attr($feature_name);
            ?>"></label>
				</div>
			<?php 
        } else {
            ?>
				<div class="dce-toggle">
					<input type="checkbox" disabled>
					<label style="cursor: not-allowed; opacity: 0.5;"></label>
				</div>
			<?php 
        }
        ?>

			<?php 
        if (isset($feature_info['icon'])) {
            ?>
				<div class="dce-feature-icon">
					<i class="<?php 
            echo esc_attr($feature_info['icon']);
            ?>" aria-hidden="true"></i>
				</div>
			<?php 
        }
        ?>

			<div class="dce-feature-info">
				<span class="dce-feature-title"><?php 
        echo esc_html($feature_info['title']);
        ?></span>
				<?php 
        if (isset($feature_info['description'])) {
            ?>
					<span class="dce-feature-desc"><?php 
            echo esc_html(wp_strip_all_tags($feature_info['description']));
            ?></span>
				<?php 
        }
        ?>
			</div>

			<?php 
        // Warnings
        if (!empty($plugin_dependencies_not_satisfied)) {
            ?>
				<span class="dce-feature-warning"><?php 
            esc_html_e('Requires', 'dynamic-content-for-elementor');
            ?> <?php 
            echo esc_html(\implode(', ', $plugin_dependencies_not_satisfied));
            ?></span>
			<?php 
        }
        if ($php_version_not_satisfied) {
            ?>
				<span class="dce-feature-warning"><?php 
            \printf(esc_html__('Requires PHP v%1$s+', 'dynamic-content-for-elementor'), esc_html($feature_info['minimum_php']));
            ?></span>
			<?php 
        }
        if ($is_bundled_feature) {
            $activated_by = Plugin::instance()->features->get_feature_title($feature_info['activated_by']);
            if (Plugin::instance()->features->is_feature_active($feature_info['activated_by'])) {
                $status = esc_html__('active', 'dynamic-content-for-elementor');
            } else {
                $status = esc_html__('deactivated', 'dynamic-content-for-elementor');
            }
            ?>
				<span class="dce-feature-warning"><?php 
            /* translators: 1: feature status, 2: activation dependency */
            echo wp_kses_post(\sprintf(esc_html__('This feature is %1$s. Its activation depends on %2$s', 'dynamic-content-for-elementor'), '<strong>' . esc_html($status) . '</strong>', '<strong>' . esc_html($activated_by) . '</strong>'));
            ?></span>
			<?php 
        }
        if ($is_feature_disabled_in_elementor) {
            ?>
				<span class="dce-feature-warning"><?php 
            echo esc_html__('This widget is disabled in Elementor (Element Manager).', 'dynamic-content-for-elementor');
            ?> <?php 
            echo esc_html__('Enable it there to activate it here.', 'dynamic-content-for-elementor');
            ?></span>
			<?php 
        }
        // Usage badge
        if ($usage_count > 0) {
            ?>
				<span class="dce-feature-usage"><?php 
            echo esc_html((string) $usage_count);
            ?>&times;</span>
			<?php 
        }
        // Details link
        if (!empty($feature_info['doc_url'])) {
            ?>
				<a class="dce-feature-details" href="<?php 
            echo esc_url(DCE_FEATURES_URL . $feature_info['doc_url']);
            ?>" target="_blank"><?php 
            esc_html_e('Details', 'dynamic-content-for-elementor');
            ?></a>
			<?php 
        }
        // Legacy notice
        if (isset($feature_info['legacy'])) {
            if (isset($feature_info['replaced_by_custom_message'])) {
                ?>
					<span class="dce-feature-warning"><?php 
                echo esc_html($feature_info['replaced_by_custom_message']);
                ?></span>
				<?php 
            } elseif (isset($feature_info['replaced_by'])) {
                $new_version_name = Plugin::instance()->features->get_feature_info($feature_info['replaced_by'], 'title');
                ?>
					<span class="dce-feature-warning"><?php 
                \printf(esc_html__('Deprecated. Use %s', 'dynamic-content-for-elementor'), esc_html($new_version_name));
                ?></span>
				<?php 
            } else {
                ?>
					<span class="dce-feature-warning"><?php 
                esc_html_e('Deprecated', 'dynamic-content-for-elementor');
                ?></span>
				<?php 
            }
        }
        ?>
		</div>
		<?php 
        // Confirmation banner placeholder
        ?>
		<div class="dce-confirmation-banner" data-feature="<?php 
        echo esc_attr($feature_name);
        ?>">
			<span class="dce-confirm-message"></span>
			<button type="button" class="dce-confirm-btn dce-confirm-yes"><?php 
        esc_html_e('Confirm', 'dynamic-content-for-elementor');
        ?></button>
			<button type="button" class="dce-confirm-btn dce-confirm-cancel"><?php 
        esc_html_e('Cancel', 'dynamic-content-for-elementor');
        ?></button>
		</div>
		<?php 
    }
    public function should_calculate_usage()
    {
        return \false;
    }
    /**
     * @param string $feature
     * @param array<mixed> $elementor_controls_usage
     * @return int
     */
    public function calculate_usage($feature, $elementor_controls_usage)
    {
        return 0;
    }
    public function show_calculate_usage($feature_name)
    {
        $elementor_controls_usage = get_option('elementor_controls_usage');
        $feature_used = \false;
        if ($elementor_controls_usage) {
            $feature_used = $this->calculate_usage($feature_name, $elementor_controls_usage);
        }
        if ($feature_used) {
            echo '<p class="used">';
            /* translators: %s: number of times the feature is used */
            \printf(esc_html(_n('Used %s time', 'Used %s times', $feature_used, 'dynamic-content-for-elementor')), esc_html((string) $feature_used));
            echo '</p>';
        }
    }
    public function render_list()
    {
        echo '<div class="dce-feature-group is-open">';
        echo '<div class="dce-feature-group-body" style="display:block;">';
        foreach ($this->features as $fname => $finfo) {
            $this->show_feature($fname, $finfo);
        }
        echo '</div>';
        echo '</div>';
    }
    public function render()
    {
        $this->render_toggle_all_button();
        echo '<div class="dce-features-search"><input type="text" id="dce-feature-search" placeholder="' . esc_attr__('Search features...', 'dynamic-content-for-elementor') . '"></div>';
        $this->render_list();
    }
}
