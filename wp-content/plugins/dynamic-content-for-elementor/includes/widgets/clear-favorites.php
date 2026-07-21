<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Favorites;
use Elementor\Icons_Manager;
if (!\defined('ABSPATH')) {
    exit;
}
class ClearFavorites extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    /**
     * @return array<string>
     */
    public function get_script_depends()
    {
        return ['dce-clear-favorites'];
    }
    /**
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_content', ['label' => $this->get_title()]);
        $this->add_control('scope', ['label' => esc_html__('Scope', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['cookie' => ['title' => esc_html__('Cookie', 'dynamic-content-for-elementor'), 'icon' => 'icon-dce-cookie'], 'user' => ['title' => esc_html__('User', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-user']], 'toggle' => \false, 'default' => 'user']);
        $this->add_control('key', ['label' => esc_html__('Key', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => 'my_favorites', 'ai' => ['active' => \false], 'description' => esc_html__('The unique name that identifies the favorites to clear', 'dynamic-content-for-elementor')]);
        $this->add_control('title', ['label' => esc_html__('Button Text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Clear Favorites', 'dynamic-content-for-elementor')]);
        $this->add_control('icon', ['label' => esc_html__('Icon', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ICONS, 'label_block' => \true]);
        $this->add_control('icon_align', ['label' => esc_html__('Icon Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'default' => 'left', 'options' => ['left' => ['title' => esc_html__('Before', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'right' => ['title' => esc_html__('After', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'toggle' => \false, 'condition' => ['dce_favorite_icon[value]!' => '']]);
        $this->add_responsive_control('icon_size', ['label' => esc_html__('Icon Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px', 'em', 'rem', 'vw', 'custom'], 'range' => ['px' => ['max' => 200, 'min' => 10], 'em' => ['min' => 0, 'max' => 10], 'rem' => ['min' => 0, 'max' => 10]], 'selectors' => ['{{WRAPPER}} .elementor-button-icon i' => 'font-size: {{SIZE}}{{UNIT}};'], 'condition' => ['dce_favorite_icon[value]!' => '']]);
        $this->add_responsive_control('icon_indent', ['label' => esc_html__('Icon Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 50]], 'selectors' => ['{{WRAPPER}} .elementor-button .elementor-align-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .elementor-button .elementor-align-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};'], 'condition' => ['dce_favorite_icon[value]!' => '']]);
        $this->add_control('show_confirmation', ['label' => esc_html__('Show Confirmation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->add_control('confirmation_text', ['label' => esc_html__('Confirmation Text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Are you sure you want to remove all favorites?', 'dynamic-content-for-elementor'), 'condition' => ['show_confirmation' => 'yes']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style', ['label' => esc_html__('Button', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('align', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-right']], 'prefix_class' => 'elementor%s-align-', 'default' => is_rtl() ? 'right' : 'left']);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography', 'selector' => '{{WRAPPER}} .elementor-button']);
        $this->start_controls_tabs('tabs_button_style');
        $this->start_controls_tab('tab_button_normal', ['label' => esc_html__('Normal', 'dynamic-content-for-elementor')]);
        $this->add_control('button_text_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .elementor-button' => 'color: {{VALUE}};']]);
        $this->add_control('background_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-button' => 'background-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->start_controls_tab('tab_button_hover', ['label' => esc_html__('Hover', 'dynamic-content-for-elementor')]);
        $this->add_control('hover_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-button:hover' => 'color: {{VALUE}};']]);
        $this->add_control('button_background_hover_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-button:hover' => 'background-color: {{VALUE}};']]);
        $this->add_control('button_hover_border_color', ['label' => esc_html__('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['border_border!' => ''], 'selectors' => ['{{WRAPPER}} .elementor-button:hover' => 'border-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'border', 'selector' => '{{WRAPPER}} .elementor-button', 'separator' => 'before']);
        $this->add_control('border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em', 'rem', 'custom'], 'selectors' => ['{{WRAPPER}} .elementor-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'button_box_shadow', 'selector' => '{{WRAPPER}} .elementor-button']);
        $this->add_responsive_control('text_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'separator' => 'before']);
        $this->end_controls_section();
    }
    /**
     * @return void
     */
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        if (empty($settings['key'])) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                Helper::notice(\false, esc_html__('Please set a key for your favorites', 'dynamic-content-for-elementor'));
            }
            return;
        }
        if (!is_user_logged_in() && 'user' === $settings['scope']) {
            return;
        }
        $whitelist_token = Favorites::get_whitelist_token($settings['key'], $settings['scope']);
        if (!$whitelist_token) {
            return;
        }
        $this->add_render_attribute(['wrapper' => ['class' => 'elementor-button-wrapper'], 'button' => ['class' => ['elementor-button', 'dce-clear-favorites-button'], 'role' => 'button', 'data-dce-key' => $settings['key'], 'data-dce-scope' => $settings['scope'], 'data-dce-whitelist-token' => $whitelist_token, 'data-dce-confirm' => !empty($settings['show_confirmation']) ? $settings['confirmation_text'] : ''], 'text' => ['class' => 'elementor-button-text']]);
        $trigger_finish = apply_filters('dynamicooo/favorites/trigger-finish', \true);
        $this->add_render_attribute('button', 'data-dce-trigger-finish', $trigger_finish);
        if (!empty($settings['icon']['value'])) {
            $this->add_render_attribute('icon-align', ['class' => ['elementor-button-icon', 'elementor-align-icon-' . $settings['icon_align']]]);
        }
        ?>
		<div <?php 
        echo $this->get_render_attribute_string('wrapper');
        ?>>
			<button <?php 
        echo $this->get_render_attribute_string('button');
        ?>>
				<span <?php 
        echo $this->get_render_attribute_string('text');
        ?>>
					<?php 
        if (!empty($settings['icon']['value']) && $settings['icon_align'] === 'left') {
            ?>
						<span <?php 
            echo $this->get_render_attribute_string('icon-align');
            ?>>
							<?php 
            Icons_Manager::render_icon($settings['icon'], ['aria-hidden' => 'true']);
            ?>
						</span>
					<?php 
        }
        ?>

					<span <?php 
        echo $this->get_render_attribute_string('text');
        ?>>
						<?php 
        echo esc_html($settings['title']);
        ?>
					</span>

					<?php 
        if (!empty($settings['icon']['value']) && $settings['icon_align'] === 'right') {
            ?>
						<span <?php 
            echo $this->get_render_attribute_string('icon-align');
            ?>>
							<?php 
            Icons_Manager::render_icon($settings['icon'], ['aria-hidden' => 'true']);
            ?>
						</span>
					<?php 
        }
        ?>
				</span>
			</button>
		</div>
		<?php 
    }
}
