<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\Extensions\DynamicVisibility\Triggers;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
class Geotargeting extends \DynamicContentForElementor\Extensions\DynamicVisibility\Triggers\Base
{
    /**
     * @return boolean
     */
    public function is_available()
    {
        return Helper::is_plugin_active('geoip-detect');
    }
    /**
     * @param \Elementor\Element_Base $element
     * @return void
     */
    public function register_controls($element)
    {
        $element->add_control('dce_visibility_geotargeting_notice_cache', ['type' => Controls_Manager::RAW_HTML, 'raw' => esc_html__('This features doesn\'t work correctly if you use a plugin to cache your site', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning']);
        $geoinfo = \geoip_detect2_get_info_from_current_ip();
        $countryInfo = new \YellowTree\GeoipDetect\Geonames\CountryInformation();
        // @phpstan-ignore class.notFound
        $countries = $countryInfo->getAllCountries();
        // @phpstan-ignore class.notFound
        $element->add_control('dce_visibility_country', ['label' => esc_html__('Country', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => $countries, 'description' => esc_html__('Trigger visibility for a specific country.', 'dynamic-content-for-elementor'), 'multiple' => \true, 'separator' => 'before']);
        $your_city = '';
        if (!empty($geoinfo) && !empty($geoinfo->city) && !empty($geoinfo->city->names)) {
            $your_city = '<br>' . esc_html__('Actually you are in:', 'dynamic-content-for-elementor') . ' ' . \implode(', ', $geoinfo->city->names);
        }
        $element->add_control('dce_visibility_city', ['label' => esc_html__('City', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'description' => esc_html__('Type here the name of the city which triggers the condition. Insert the city name translated in one of the supported languages (preferable in EN). You can insert multiple cities, comma-separated.', 'dynamic-content-for-elementor') . $your_city, 'ai' => ['active' => \false]]);
    }
    /**
     * @param array<string,mixed> $settings
     * @param array<string,mixed> &$triggers
     * @param array<string,mixed> &$conditions
     * @param array<string,mixed> &$required
     * @param \Elementor\Element_Base $element
     * @return void
     */
    public function check_conditions($settings, &$triggers, &$conditions, &$required, $element)
    {
        if (!empty($settings['dce_visibility_country'])) {
            $triggers['dce_visibility_country'] = esc_html__('Country', 'dynamic-content-for-elementor');
            if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                $geoinfo = \geoip_detect2_get_info_from_current_ip();
                $required['dce_visibility_country'] = \true;
                if (\in_array($geoinfo->country->isoCode, $settings['dce_visibility_country'])) {
                    $conditions['dce_visibility_country'] = esc_html__('Country', 'dynamic-content-for-elementor');
                }
            }
        }
        if (!empty($settings['dce_visibility_city'])) {
            $triggers['dce_visibility_country'] = esc_html__('City', 'dynamic-content-for-elementor');
            if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                $geoinfo = \geoip_detect2_get_info_from_current_ip();
                $ucity = \array_map('strtolower', $geoinfo->city->names);
                $scity = Helper::str_to_array(',', $settings['dce_visibility_city'], 'strtolower');
                $icity = \array_intersect($ucity, $scity);
                $required['dce_visibility_country'] = \true;
                if (!empty($icity)) {
                    $conditions['dce_visibility_country'] = esc_html__('City', 'dynamic-content-for-elementor');
                }
            }
        }
    }
    /**
     * @return string|null
     */
    public function get_availability_requirements_message()
    {
        return \str_replace('%s', '<a target="_blank" href="https://wordpress.org/plugins/geoip-detect/">GeoIP Detection</a>', __('You need to install the %s plugin to use this trigger.', 'dynamic-content-for-elementor'));
    }
}
