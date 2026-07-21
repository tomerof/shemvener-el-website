<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Widgets;

if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly.
}
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Controls_Manager;
use DynamicContentForElementor\CryptocurrencyApiError;
use Elementor\Group_Control_Typography;
class CryptocoinBadge extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    /**
     * @return array<string>
     */
    public function get_script_depends()
    {
        return [];
    }
    /**
     * @return array<string>
     */
    public function get_style_depends()
    {
        return ['dce-crypto-badge'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('coin_section', ['label' => esc_html__('Coin', 'dynamic-content-for-elementor')]);
        $crypto = \DynamicContentForElementor\Plugin::instance()->cryptocurrency;
        if (!$crypto->has_api_key()) {
            $this->add_control('api_key_required', ['type' => Controls_Manager::NOTICE, 'notice_type' => 'danger', 'content' => \sprintf(esc_html__('In order to use this feature you should set %1$sCoinMarketCap API key%2$s on Integrations section', 'dynamic-content-for-elementor'), '<a href="' . admin_url('admin.php?page=dce-integrations') . '" target="_blank">', '</a>')]);
            $this->end_controls_section();
            return;
            // Stop here - no other controls if no API key
        }
        try {
            $coins_options = $crypto->get_coins_options();
            $convert_options = $crypto->get_convert_options();
        } catch (\Error $e) {
            $this->add_control('notice_api', ['type' => Controls_Manager::NOTICE, 'notice_type' => 'warning', 'content' => $e->getMessage()]);
            $this->end_controls_section();
            return;
        }
        $this->add_control('coin_id', ['label' => esc_html__('Coin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => $coins_options, 'default' => 1]);
        $this->add_control('convert_id', ['label' => esc_html__('Convert to', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => $convert_options, 'default' => 2781]);
        $this->add_control('cache_age', ['label' => esc_html__('Store in cache for', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => $crypto->get_cache_age_options(), 'default' => '5m', 'label_block' => \true]);
        $this->end_controls_section();
        $this->start_controls_section('section_badge_style', ['label' => esc_html__('Badge', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography', 'label' => esc_html__('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}}']);
        $this->add_control('text_color', ['label' => esc_html__('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-cryptobadge-wrapper' => 'color: {{VALUE}};']]);
        $this->add_control('background_color', ['label' => esc_html__('Background', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '#EEEEEE', 'selectors' => ['{{WRAPPER}} .dce-cryptobadge-wrapper' => 'background-color: {{VALUE}};']]);
        $this->add_control('border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em', 'rem', 'custom'], 'default' => ['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1, 'unit' => 'rem'], 'selectors' => ['{{WRAPPER}} .dce-cryptobadge-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_control('padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em', 'rem'], 'default' => ['top' => '3.5', 'right' => '3.5', 'bottom' => '3.5', 'left' => '3.5', 'unit' => 'rem'], 'selectors' => ['{{WRAPPER}} .dce-cryptobadge-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->end_controls_section();
    }
    /**
     * @param array<string,mixed> $data
     * @return void
     */
    private function render_template($data)
    {
        ?>
		<div class="dce-cryptobadge-wrapper">
			<div class="dce-cryptobadge-main">
				<div class="dce-cryptobadge-context">
					<?php 
        if (!empty($data['coin_logo'])) {
            ?>
					<div class="dce-cryptobadge-logo">
						<img src="<?php 
            echo esc_url($data['coin_logo']);
            ?>" alt="<?php 
            echo esc_attr($data['coin_name']);
            ?> logo">
					</div>
					<?php 
        }
        ?>
					<div class="dce-cryptobadge-name"><?php 
        echo esc_html($data['coin_name']);
        ?></div>
					<div class="dce-cryptobadge-pair"><?php 
        echo esc_html($data['pair']);
        ?></div>
				</div>
				<div class="dce-cryptobadge-latest-box">
					<div class="dce-cryptobadge-latest-price"><?php 
        echo esc_html($data['latest_price']);
        ?></div>
				</div>
			</div>
			<div class="dce-cryptobadge-list-items-wrapper">
				<?php 
        foreach ($data['list_items'] as $item) {
            ?>
					<div class="dce-cryptobadge-list-item-wrapper">
						<span class="dce-cryptobadge-list-item-label"><?php 
            echo esc_html($item['label']);
            ?></span>
						<span class="dce-cryptobadge-list-item-content"><?php 
            echo esc_html($item['content']);
            ?></span>
					</div>
				<?php 
        }
        ?>
			</div>
		</div>
		<?php 
    }
    protected function render_badge($coin_info, $coin_logo, $convert_info, $quotes)
    {
        $fields = ['percent_change_1h' => esc_html__('Percent Change 1 Hour', 'dynamic-content-for-elementor'), 'percent_change_24h' => esc_html__('Percent Change 24 Hours', 'dynamic-content-for-elementor'), 'percent_change_7d' => esc_html__('Percent Change 7 Days', 'dynamic-content-for-elementor'), 'percent_change_30d' => esc_html__('Percent Change 30 Days', 'dynamic-content-for-elementor'), 'market_cap' => esc_html__('Market Cap', 'dynamic-content-for-elementor')];
        $pair = $coin_info['symbol'] ?? '' . ' / ' . $convert_info['symbol'] ?? '';
        $price = \number_format_i18n($quotes['price'], 2);
        $list_items = [];
        foreach ($fields as $key => $label) {
            $list_items[] = ['label' => $label, 'content' => \number_format_i18n($quotes[$key], 2)];
        }
        $this->render_template(['coin_name' => $coin_info['name'] ?? '', 'coin_logo' => $coin_logo, 'pair' => $pair, 'latest_price' => $price, 'list_items' => $list_items]);
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
        $crypto = \DynamicContentForElementor\Plugin::instance()->cryptocurrency;
        if (!$crypto->has_api_key()) {
            return;
        }
        $coin_id = $settings['coin_id'];
        $convert_id = $settings['convert_id'];
        $cache_age = $settings['cache_age'];
        try {
            $quotes = $crypto->get_coin_quote($coin_id, $convert_id, $cache_age);
            $coin_info = $crypto->get_fiat_and_crypto_info($coin_id);
            $convert_info = $crypto->get_fiat_and_crypto_info($convert_id);
            $coin_logo = $crypto->get_coin_logo($coin_id);
        } catch (CryptocurrencyApiError $e) {
            $quotes = 'NA';
            $coin_info = 'NA';
            $convert_info = 'NA';
            $coin_logo = 'NA';
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                echo $e->getMessage();
            }
        }
        $this->render_badge($coin_info, $coin_logo, $convert_info, $quotes);
    }
}
