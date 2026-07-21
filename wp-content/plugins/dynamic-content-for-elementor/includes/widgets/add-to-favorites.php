<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

use Elementor\Icons_Manager;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Favorites;
if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
class AddToFavorites extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    /**
     * @return void
     */
    public function run_once()
    {
        add_shortcode('dce-favorites', function ($atts = [], $content = null) {
            $key = $atts['key'] ?? 'my_favorites';
            $get = $atts['get'] ?? '';
            if ($get === 'count') {
                $favs = get_user_meta(get_current_user_id(), $key, \true);
                if (\is_array($favs)) {
                    return \count($favs);
                }
                return '';
            }
            return '';
        });
    }
    /**
     * @return array<string>
     */
    public function get_style_depends()
    {
        return ['dce-add-to-favorites'];
    }
    /**
     * @return array<string>
     */
    public function get_script_depends()
    {
        return ['dce-add-to-favorites'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_content', ['label' => $this->get_title()]);
        $this->add_control('scope', ['label' => esc_html__('Scope', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['cookie' => ['title' => esc_html__('Cookie', 'dynamic-content-for-elementor'), 'icon' => 'icon-dce-cookie'], 'user' => ['title' => esc_html__('User', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-user']], 'toggle' => \false, 'default' => 'user']);
        $this->add_control('key', ['label' => esc_html__('Key', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => 'my_favorites', 'ai' => ['active' => \false], 'description' => esc_html__('The unique name that identifies the favorites. If you change it, you will lose the favorites saved until now', 'dynamic-content-for-elementor')]);
        $this->add_control('remove', ['label' => esc_html__('Allow Removal', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']);
        $this->start_controls_tabs('repe_tabs');
        $this->start_controls_tab('add_repe_tab', ['label' => esc_html__('Add', 'dynamic-content-for-elementor')]);
        $this->add_control('title_add', ['label' => esc_html__('Title Add', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Add to my Favorites', 'dynamic-content-for-elementor')]);
        $this->add_control('title_added', ['label' => esc_html__('Title When Added', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Added to my Favorites', 'dynamic-content-for-elementor'), 'condition' => ['remove' => '']]);
        $this->add_control('icon_add', ['label' => esc_html__('Icon Add', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ICONS, 'label_block' => \true]);
        $this->end_controls_tab();
        $this->start_controls_tab('remove_repe_tab', ['label' => esc_html__('Remove', 'dynamic-content-for-elementor'), 'condition' => ['remove!' => '']]);
        $this->add_control('title_remove', ['label' => esc_html__('Title Remove', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => esc_html__('Remove from my Favorites', 'dynamic-content-for-elementor')]);
        $this->add_control('icon_remove', ['label' => esc_html__('Icon Remove', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ICONS, 'label_block' => \true]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_control('cookie_days', ['label' => esc_html__('Cookie expiration', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 365, 'min' => 0, 'description' => esc_html__('Value is in days. Set 0 or empty for session duration.', 'dynamic-content-for-elementor'), 'condition' => ['scope' => 'cookie']]);
        $this->end_controls_section();
        $this->start_controls_section('section_button', ['label' => esc_html__('Button', 'dynamic-content-for-elementor')]);
        $this->add_control('button_type', ['label' => esc_html__('Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => '', 'options' => ['' => esc_html__('Default', 'dynamic-content-for-elementor'), 'info' => esc_html__('Info', 'dynamic-content-for-elementor'), 'success' => esc_html__('Success', 'dynamic-content-for-elementor'), 'warning' => esc_html__('Warning', 'dynamic-content-for-elementor'), 'danger' => esc_html__('Danger', 'dynamic-content-for-elementor')], 'prefix_class' => 'elementor-button-']);
        $this->add_control('size', ['label' => esc_html__('Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'sm', 'options' => Helper::get_button_sizes(), 'style_transfer' => \true]);
        $this->add_control('icon_align', ['label' => esc_html__('Icon Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'default' => 'left', 'options' => ['left' => ['title' => esc_html__('Before', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'right' => ['title' => esc_html__('After', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'toggle' => \false]);
        $this->add_responsive_control('icon_size', ['label' => esc_html__('Icon Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px', 'em', 'rem', 'vw', 'custom'], 'range' => ['px' => ['max' => 200, 'min' => 10], 'em' => ['min' => 0, 'max' => 10], 'rem' => ['min' => 0, 'max' => 10]], 'selectors' => ['{{WRAPPER}} .elementor-button-icon i' => 'font-size: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('icon_indent', ['label' => esc_html__('Icon Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 50]], 'selectors' => ['{{WRAPPER}} .elementor-button .elementor-align-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .elementor-button .elementor-align-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};']]);
        $this->end_controls_section();
        $this->start_controls_section('section_visitors', ['label' => esc_html__('Visitors', 'dynamic-content-for-elementor'), 'condition' => ['scope' => 'user', 'key!' => 'dce_wishlist']]);
        $this->add_control('visitor_hide', ['label' => esc_html__('Hide Button for non-logged-in users', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
        $this->add_control('visitor_login', ['label' => esc_html__('Login URL', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::URL, 'default' => ['url' => wp_login_url(), 'is_external' => \false, 'nofollow' => \false], 'condition' => ['visitor_hide' => '']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style', ['label' => esc_html__('Button', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('align', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-right'], 'justify' => ['title' => esc_html__('Justified', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-justify']], 'prefix_class' => 'elementor%s-align-', 'default' => '']);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography', 'selector' => '{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button']);
        $this->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'text_shadow', 'selector' => '{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button']);
        $this->start_controls_tabs('tabs_button_style');
        $this->start_controls_tab('tab_button_normal', ['label' => esc_html__('Add', 'dynamic-content-for-elementor')]);
        $this->add_control('button_text_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .elementor-button.dce-add-to-favorites-add' => 'fill: {{VALUE}}; color: {{VALUE}};']]);
        $this->add_control('background_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-button.dce-add-to-favorites-add' => 'background-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->start_controls_tab('tab_button_remove', ['label' => esc_html__('Remove', 'dynamic-content-for-elementor'), 'condition' => ['remove!' => '']]);
        $this->add_control('button_text_color_remove', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .elementor-button.dce-add-to-favorites-remove' => 'fill: {{VALUE}}; color: {{VALUE}} !important;']]);
        $this->add_control('background_color_remove', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-button.dce-add-to-favorites-remove' => 'background-color: {{VALUE}} !important;']]);
        $this->end_controls_tab();
        $this->start_controls_tab('tab_button_hover', ['label' => esc_html__('Hover', 'dynamic-content-for-elementor')]);
        $this->add_control('hover_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} .elementor-button:hover, {{WRAPPER}} a.elementor-button:focus, {{WRAPPER}} .elementor-button:focus' => 'color: {{VALUE}};', '{{WRAPPER}} a.elementor-button:hover svg, {{WRAPPER}} .elementor-button:hover svg, {{WRAPPER}} a.elementor-button:focus svg, {{WRAPPER}} .elementor-button:focus svg' => 'fill: {{VALUE}};']]);
        $this->add_control('button_background_hover_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} .elementor-button:hover, {{WRAPPER}} a.elementor-button:focus, {{WRAPPER}} .elementor-button:focus' => 'background-color: {{VALUE}};']]);
        $this->add_control('button_hover_border_color', ['label' => esc_html__('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['border_border!' => ''], 'selectors' => ['{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} .elementor-button:hover, {{WRAPPER}} a.elementor-button:focus, {{WRAPPER}} .elementor-button:focus' => 'border-color: {{VALUE}};']]);
        $this->add_control('hover_animation', ['label' => esc_html__('Hover Animation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HOVER_ANIMATION, 'prefix_class' => 'dce-elementor-animation-']);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'border', 'selector' => '{{WRAPPER}} .elementor-button', 'separator' => 'before']);
        $this->add_control('border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em', 'rem', 'custom'], 'selectors' => ['{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'button_box_shadow', 'selector' => '{{WRAPPER}} .elementor-button']);
        $this->add_responsive_control('text_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'separator' => 'before']);
        $this->end_controls_section();
        $this->start_controls_section('section_counter', ['label' => esc_html__('Counter', 'dynamic-content-for-elementor')]);
        $this->add_control('counter', ['label' => esc_html__('Show counter', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER]);
        $this->add_control('counter_icon', ['label' => esc_html__('Icon Counter', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ICONS, 'label_block' => \true, 'condition' => ['counter!' => '']]);
        $this->end_controls_section();
        $this->start_controls_section('section_counter_style', ['label' => esc_html__('Counter', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['counter!' => '']]);
        $this->add_control('counter_align', ['label' => esc_html__('Counter Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'default' => 'left', 'options' => ['left' => ['title' => esc_html__('Before', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'right' => ['title' => esc_html__('After', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'toggle' => \false, 'selectors' => ['{{WRAPPER}} .elementor-button .elementor-button-counter' => 'float: {{VALUE}};', '{{WRAPPER}} .elementor-button .elementor-align-counter-left' => 'border-right: 4px solid;', '{{WRAPPER}} .elementor-button .elementor-align-counter-right' => 'border-left: 4px solid;'], 'render_type' => 'template']);
        $this->add_control('counter_separator_width', ['label' => esc_html__('Separator width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 3], 'range' => ['px' => ['max' => 10, 'min' => 0]], 'selectors' => ['{{WRAPPER}} .elementor-button .elementor-align-counter-right' => 'border-left-width: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .elementor-button .elementor-align-counter-left' => 'border-right-width: {{SIZE}}{{UNIT}};']]);
        $this->add_control('counter_padding', ['label' => esc_html__('Counter Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 50]], 'default' => ['size' => 5], 'selectors' => ['{{WRAPPER}} .elementor-button .elementor-align-counter-right' => 'padding-left: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .elementor-button .elementor-align-counter-left' => 'padding-right: {{SIZE}}{{UNIT}};']]);
        $this->add_control('counter_indent', ['label' => esc_html__('Counter Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 50]], 'default' => ['size' => 5], 'selectors' => ['{{WRAPPER}} .elementor-button .elementor-align-counter-right' => 'margin-left: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .elementor-button .elementor-align-counter-left' => 'margin-right: {{SIZE}}{{UNIT}};']]);
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
        $scope = $settings['scope'];
        if ('global' === $scope) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                Helper::notice(\false, esc_html__('Global scope is not more supported, please use a different scope', 'dynamic-content-for-elementor'));
            }
            return;
        }
        if (empty($settings['key'])) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                Helper::notice(\false, esc_html__('Please set a key for your favorites', 'dynamic-content-for-elementor'));
            }
            return;
        }
        if (!is_user_logged_in() && !empty($settings['visitor_hide']) && 'user' === $settings['scope']) {
            return;
        }
        $post_ID = apply_filters('wpml_object_id', get_the_ID(), get_post_type(), \true);
        $key = $settings['key'];
        $whitelist_token = Favorites::get_action_token($key, $scope, (int) $post_ID);
        if (!$whitelist_token) {
            return;
        }
        // Don't show Add to Wishlist if the post isn't a product
        if ('dce_wishlist' === $key && 'product' !== get_post_type($post_ID)) {
            return;
        }
        $this->add_render_attribute('container', 'class', 'elementor-button-wrapper');
        $is_favorited = Favorites::is_favorited($key, $scope, $post_ID);
        $allow_removal = $this->allow_removal();
        if ($is_favorited && $allow_removal) {
            $this->add_render_attribute('button', 'class', 'dce-add-to-favorites-remove');
            $button_text = $settings['title_remove'];
        } else {
            $this->add_render_attribute('button', 'class', 'dce-add-to-favorites-add');
            $button_text = $settings['title_add'];
        }
        $action_settings = ['add' => ['title' => $settings['title_add'] ?? '', 'icon_html' => Icons_Manager::try_get_icon_html($settings['icon_add'], ['aria-hidden' => 'true'], 'i')]];
        if ($allow_removal) {
            $action_settings['remove'] = ['title' => $settings['title_remove'] ?? '', 'icon_html' => Icons_Manager::try_get_icon_html($settings['icon_remove'], ['aria-hidden' => 'true'], 'i')];
        } else {
            $action_settings['added'] = ['title' => $settings['title_added'] ?? esc_html__('Added to my Favorites', 'dynamic-content-for-elementor'), 'icon_html' => Icons_Manager::try_get_icon_html($settings['icon_add'], ['aria-hidden' => 'true'], 'i')];
        }
        $this->add_render_attribute('button', 'data-dce-is-favorited', $is_favorited ? '1' : '0');
        $this->add_render_attribute('button', 'data-dce-post-id', $post_ID);
        $this->add_render_attribute('button', 'data-dce-key', $settings['key']);
        $this->add_render_attribute('button', 'data-dce-scope', $settings['scope']);
        $this->add_render_attribute('button', 'data-dce-whitelist-token', $whitelist_token);
        $this->add_render_attribute('button', 'data-dce-can-remove-favorites', $allow_removal ? '1' : '0');
        // Add login URL for non-logged in users if required
        if (!is_user_logged_in() && 'user' === $settings['scope'] && isset($settings['visitor_login']) && !empty($settings['visitor_login']['url'])) {
            $this->add_render_attribute('button', 'data-dce-login-url', $settings['visitor_login']['url']);
        }
        $json_action_settings = wp_json_encode($action_settings);
        if (\false !== $json_action_settings) {
            $this->add_render_attribute('button', 'data-dce-action-settings', \htmlspecialchars($json_action_settings, \ENT_QUOTES, 'UTF-8'));
        }
        $this->add_render_attribute('button', 'class', ['elementor-button']);
        if (!empty($settings['counter'])) {
            $this->add_render_attribute('button', 'data-dce-counter', \strval(Favorites::get_counter($key, $scope, $post_ID)));
            $counter_icon_html = Icons_Manager::try_get_icon_html($settings['counter_icon'], ['aria-hidden' => 'true'], 'i');
            $this->add_render_attribute('button', 'data-dce-counter-icon', \htmlspecialchars($counter_icon_html, \ENT_QUOTES, 'UTF-8'));
            $this->add_render_attribute('button', 'data-dce-has-counter', !empty($settings['counter']) ? '1' : '0');
        }
        if (!empty($settings['size'])) {
            $this->add_render_attribute('button', 'class', 'elementor-size-' . $settings['size']);
        }
        $this->add_render_attribute('button', 'rel', apply_filters('dynamicooo/add-to-favorites/rel', 'nofollow'));
        $this->add_render_attribute(['content-wrapper' => ['class' => 'elementor-button-content-wrapper'], 'icon-align' => ['class' => ['elementor-button-icon', 'elementor-align-icon-' . $settings['icon_align']]], 'text' => ['class' => 'elementor-button-text']]);
        if (!empty($settings['counter'])) {
            $this->add_render_attribute(['counter-align' => ['class' => ['elementor-button-counter', 'elementor-align-counter-' . $settings['counter_align']]]]);
        }
        // Hover Animation
        if (!empty($settings['hover_animation'])) {
            $this->add_render_attribute('button', 'class', 'elementor-animation-' . $settings['hover_animation']);
        }
        $trigger_finish = apply_filters('dynamicooo/favorites/trigger-finish', \true);
        $this->add_render_attribute('button', 'data-dce-trigger-finish', $trigger_finish);
        // Render the button
        ?>
		<div <?php 
        echo $this->get_render_attribute_string('container');
        ?>>
			<button <?php 
        echo $this->get_render_attribute_string('button');
        ?>>
				<span <?php 
        echo $this->get_render_attribute_string('content-wrapper');
        ?>>
					<?php 
        // Show counter if enabled
        if (!empty($settings['counter'])) {
            ?>
						<span <?php 
            echo $this->get_render_attribute_string('counter-align');
            ?>>
							<!-- Populated by JavaScript -->
						</span>
						<?php 
        }
        ?>
	
					<span <?php 
        echo $this->get_render_attribute_string('icon-align');
        ?>>
						<?php 
        if ($is_favorited && $allow_removal) {
            Icons_Manager::render_icon($settings['icon_remove'], ['aria-hidden' => 'true']);
        } else {
            Icons_Manager::render_icon($settings['icon_add'], ['aria-hidden' => 'true']);
        }
        ?>
					</span>
	
					<span <?php 
        echo $this->get_render_attribute_string('text');
        ?>><?php 
        echo wp_kses_post($button_text);
        ?></span>
				</span>
			</button>
		</div>
		<?php 
    }
    /**
     * @return bool
     */
    protected function allow_removal()
    {
        $settings = $this->get_settings_for_display();
        return !empty($settings['remove']);
    }
}
