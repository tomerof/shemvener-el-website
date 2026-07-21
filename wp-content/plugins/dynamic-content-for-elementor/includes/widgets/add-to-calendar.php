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
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class AddToCalendar extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_style_depends()
    {
        return ['dce-add-to-calendar'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_button', ['label' => esc_html__('Button', 'dynamic-content-for-elementor')]);
        $this->add_control('button_type', ['label' => esc_html__('Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => '', 'options' => ['' => esc_html__('Default', 'dynamic-content-for-elementor'), 'info' => esc_html__('Info', 'dynamic-content-for-elementor'), 'success' => esc_html__('Success', 'dynamic-content-for-elementor'), 'warning' => esc_html__('Warning', 'dynamic-content-for-elementor'), 'danger' => esc_html__('Danger', 'dynamic-content-for-elementor')], 'prefix_class' => 'elementor-button-']);
        $this->add_control('text', ['label' => esc_html__('Text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'dynamic' => ['active' => \true], 'default' => esc_html__('Add to Calendar', 'dynamic-content-for-elementor'), 'placeholder' => esc_html__('Add to Calendar', 'dynamic-content-for-elementor')]);
        $this->add_responsive_control('align', ['label' => esc_html__('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => esc_html__('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-left'], 'center' => ['title' => esc_html__('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-center'], 'right' => ['title' => esc_html__('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-right'], 'justify' => ['title' => esc_html__('Justified', 'dynamic-content-for-elementor'), 'icon' => 'eicon-text-align-justify']], 'prefix_class' => 'elementor%s-align-', 'default' => '']);
        $this->add_control('size', ['label' => esc_html__('Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'sm', 'options' => Helper::get_button_sizes(), 'style_transfer' => \true]);
        $this->add_control('selected_icon', ['label' => esc_html__('Icon', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::ICONS, 'skin' => 'inline', 'label_block' => \false]);
        $this->add_control('icon_align', ['label' => esc_html__('Icon Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'left', 'options' => ['left' => esc_html__('Before', 'dynamic-content-for-elementor'), 'right' => esc_html__('After', 'dynamic-content-for-elementor')], 'condition' => ['selected_icon[value]!' => '']]);
        $this->add_control('icon_indent', ['label' => esc_html__('Icon Spacing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'range' => ['px' => ['max' => 50]], 'selectors' => ['{{WRAPPER}} .elementor-button .elementor-align-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .elementor-button .elementor-align-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};']]);
        $this->add_control('icon_size', ['label' => esc_html__('Icon Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['px', 'em', 'rem', 'vw', 'custom'], 'range' => ['px' => ['min' => 10, 'max' => 60], 'em' => ['min' => 0, 'max' => 10], 'rem' => ['min' => 0, 'max' => 10]], 'selectors' => ['{{WRAPPER}} .elementor-button .elementor-button-icon' => 'font-size: {{SIZE}}{{UNIT}};']]);
        $this->add_control('view', ['label' => esc_html__('View', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HIDDEN, 'default' => 'traditional']);
        $this->add_control('button_css_id', ['label' => esc_html__('Button ID', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'dynamic' => ['active' => \true], 'default' => '', 'title' => esc_html__('Add your custom id WITHOUT the Pound key. e.g: my-id', 'dynamic-content-for-elementor'), 'label_block' => \false, 'description' => \sprintf(
            /* translators: %1$s: opening <code> tag, %2$s: closing </code> tag */
            esc_html__('Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows %1$sA-z 0-9%2$s & underscore chars without spaces.', 'dynamic-content-for-elementor'),
            '<code>',
            '</code>'
        ), 'separator' => 'before']);
        $this->end_controls_section();
        $this->start_controls_section('section_style', ['label' => esc_html__('Button', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography', 'selector' => '{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button']);
        $this->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'text_shadow', 'selector' => '{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button']);
        $this->start_controls_tabs('tabs_button_style');
        $this->start_controls_tab('tab_button_normal', ['label' => esc_html__('Normal', 'dynamic-content-for-elementor')]);
        $this->add_control('button_text_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'fill: {{VALUE}}; color: {{VALUE}};']]);
        $this->add_control('background_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'background-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->start_controls_tab('tab_button_hover', ['label' => esc_html__('Hover', 'dynamic-content-for-elementor')]);
        $this->add_control('hover_color', ['label' => esc_html__('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} .elementor-button:hover, {{WRAPPER}} a.elementor-button:focus, {{WRAPPER}} .elementor-button:focus' => 'color: {{VALUE}};', '{{WRAPPER}} a.elementor-button:hover svg, {{WRAPPER}} .elementor-button:hover svg, {{WRAPPER}} a.elementor-button:focus svg, {{WRAPPER}} .elementor-button:focus svg' => 'fill: {{VALUE}};']]);
        $this->add_control('button_background_hover_color', ['label' => esc_html__('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} .elementor-button:hover, {{WRAPPER}} a.elementor-button:focus, {{WRAPPER}} .elementor-button:focus' => 'background-color: {{VALUE}};']]);
        $this->add_control('button_hover_border_color', ['label' => esc_html__('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['border_border!' => ''], 'selectors' => ['{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} .elementor-button:hover, {{WRAPPER}} a.elementor-button:focus, {{WRAPPER}} .elementor-button:focus' => 'border-color: {{VALUE}};']]);
        $this->add_control('hover_animation', ['label' => esc_html__('Hover Animation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HOVER_ANIMATION]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'border', 'selector' => '{{WRAPPER}} .elementor-button', 'separator' => 'before']);
        $this->add_control('border_radius', ['label' => esc_html__('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%', 'em', 'rem', 'custom'], 'selectors' => ['{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'button_box_shadow', 'selector' => '{{WRAPPER}} .elementor-button']);
        $this->add_responsive_control('text_padding', ['label' => esc_html__('Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'separator' => 'before']);
        $this->end_controls_section();
        $this->start_controls_section('section_calendar', ['label' => esc_html__('Calendar', 'dynamic-content-for-elementor')]);
        $this->add_control('dce_calendar_format', ['label' => esc_html__('Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['gcalendar' => esc_html__('Google Calendar', 'dynamic-content-for-elementor'), 'ics' => esc_html__('ICS (for iCal and Outlook)', 'dynamic-content-for-elementor'), 'web_outlook' => esc_html__('Outlook.com Calendar', 'dynamic-content-for-elementor'), 'yahoo' => esc_html__('Yahoo Calendar', 'dynamic-content-for-elementor')], 'default' => 'gcalendar', 'toggle' => \false]);
        $this->add_control('filename', ['label' => esc_html__('Filename', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'label_block' => \true, 'condition' => ['dce_calendar_format' => 'ics']]);
        $this->add_control('dce_calendar_title', ['label' => esc_html__('Title', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT]);
        $this->add_control('dce_calendar_datetime_format', ['label' => esc_html__('Date Field', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['picker' => ['title' => esc_html__('DateTime Picker', 'dynamic-content-for-elementor'), 'icon' => 'eicon-date'], 'string' => ['title' => esc_html__('Dynamic String', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-i-cursor']], 'default' => 'string', 'toggle' => \false]);
        $this->add_control('dce_calendar_datetime_start', ['label' => esc_html__('DateTime Start', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DATE_TIME, 'label_block' => \true, 'condition' => ['dce_calendar_datetime_format' => 'picker']]);
        $this->add_control('dce_calendar_datetime_end', ['label' => esc_html__('DateTime End', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DATE_TIME, 'label_block' => \true, 'condition' => ['dce_calendar_datetime_format' => 'picker']]);
        $this->add_control('dce_calendar_datetime_string_format', ['label' => esc_html__('Date Format', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => 'Y-m-d H:i', 'placeholder' => 'Y-m-d H:i', 'label_block' => \true, 'condition' => ['dce_calendar_datetime_format' => 'string']]);
        // This is required because unfortunately JetEngine can store dates like this.
        $this->add_control('dce_calendar_epoch_as_local', ['label' => esc_html__('Local Unix Epoch', 'dynamic-content-for-elementor'), 'description' => esc_html__('This Unix Epoch does not represent a specific point in time but a local time.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['dce_calendar_datetime_string_format' => 'U']]);
        $this->add_control('dce_calendar_datetime_start_string', ['label' => esc_html__('DateTime Start', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'label_block' => \true, 'condition' => ['dce_calendar_datetime_format' => 'string']]);
        $this->add_control('dce_calendar_datetime_end_string', ['label' => esc_html__('DateTime End', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'label_block' => \true, 'condition' => ['dce_calendar_datetime_format' => 'string']]);
        $this->add_control('dce_calendar_description', ['label' => esc_html__('Description', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::WYSIWYG]);
        $this->add_control('dce_calendar_location', ['label' => esc_html__('Address', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'label_block' => \true]);
        $this->end_controls_section();
    }
    /**
     * Create a new DateTime in the local tz using $date only for its local
     * representation.
     */
    private function create_datetime_from_local_representation($date)
    {
        $f = 'Y-m-d\\TH:i:s';
        return \DateTime::createFromFormat($f, $date->format($f), new \DateTimeZone(wp_timezone_string()));
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        // Dates
        $date_from = $settings['dce_calendar_datetime_format'] != 'string' ? $settings['dce_calendar_datetime_start'] : $settings['dce_calendar_datetime_start_string'];
        $date_to = $settings['dce_calendar_datetime_format'] != 'string' ? $settings['dce_calendar_datetime_end'] : $settings['dce_calendar_datetime_end_string'];
        // Don't render if the start date is empty
        if (empty($date_from)) {
            Helper::notice(\false, esc_html__('Please enter the start date', 'dynamic-content-for-elementor'));
            return;
        }
        // Date Format
        $date_format = $settings['dce_calendar_datetime_string_format'] ?? 'Y-m-d H:i';
        // From
        $from = \DateTime::createFromFormat($date_format, $date_from, new \DateTimeZone(wp_timezone_string()));
        if ($settings['dce_calendar_epoch_as_local'] === 'yes') {
            $from = $this->create_datetime_from_local_representation($from);
        }
        if (!$from) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                $content = \sprintf('%s <b>%s</b><br>%s <b>%s</b>', esc_html__('DateTime Format:', 'dynamic-content-for-elementor'), $date_format, esc_html__('Start date is wrong:', 'dynamic-content-for-elementor'), $date_from);
                Helper::notice(esc_html__('Warning', 'dynamic-content-for-elementor'), $content, 'danger');
            }
            return;
        }
        // To - If the end date is empty set it on +1 day from start date
        if (empty($date_to)) {
            $to = \DateTime::createFromFormat($date_format, $date_from, new \DateTimeZone(wp_timezone_string()));
            $to = $to->modify('+ 1 day');
        } else {
            $to = \DateTime::createFromFormat($date_format, $date_to, new \DateTimeZone(wp_timezone_string()));
            if ($settings['dce_calendar_epoch_as_local'] === 'yes') {
                $to = $this->create_datetime_from_local_representation($to);
            }
        }
        if (!$to) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                $content = \sprintf('%s <b>%s</b><br>%s <b>%s</b>', esc_html__('DateTime Format:', 'dynamic-content-for-elementor'), $date_format, esc_html__('End date is wrong:', 'dynamic-content-for-elementor'), $date_to);
                Helper::notice(esc_html__('Warning', 'dynamic-content-for-elementor'), $content, 'danger');
            }
            return;
        }
        if ($from > $to) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                $content = \sprintf('%s <b>%s</b><br>%s <b>%s</b>', esc_html__('TO time:', 'dynamic-content-for-elementor'), $to->format($date_format), esc_html__('FROM time:', 'dynamic-content-for-elementor'), $from->format($date_format));
                Helper::notice(esc_html__('Warning', 'dynamic-content-for-elementor'), $content, 'danger');
            }
            return;
        }
        $title = $settings['dce_calendar_title'] ?? esc_html__('Event', 'dynamic-content-for-elementor');
        $description = $settings['dce_calendar_description'] ?? '';
        $address = $settings['dce_calendar_location'] ?? '';
        $link = \DynamicOOOS\Spatie\CalendarLinks\Link::create($title, $from, $to)->description($description)->address($address);
        switch ($settings['dce_calendar_format']) {
            case 'gcalendar':
                $link = $link->google();
                break;
            case 'ics':
                if (current_user_can('administrator') && \strpos(wp_timezone_string(), ':')) {
                    echo '<div style="color: red">';
                    echo esc_html__('ICS file may be invalid. In the WordPress settings the Timezone is set as an offset, for example UTC+1. This isn\'t supported. To fix this please set it with a city, for example Rome.', 'dynamic-content-for-elementor');
                    echo '</div>';
                }
                $link = $link->formatWith(new \DynamicContentForElementor\Rfc5545IcsGenerator());
                if (!empty($settings['filename'])) {
                    $this->add_render_attribute('button', 'download', sanitize_file_name($settings['filename']));
                }
                break;
            case 'web_outlook':
                $link = $link->webOutlook();
                break;
            case 'yahoo':
                $link = $link->yahoo();
                break;
            default:
                $link = $link->google();
        }
        $this->add_render_attribute('wrapper', 'class', 'elementor-button-wrapper');
        $this->add_render_attribute('button', 'href', $link);
        $this->add_render_attribute('button', 'class', 'elementor-button-link');
        $this->add_render_attribute('button', 'target', '_blank');
        $this->add_render_attribute('button', 'rel', 'nofollow');
        $this->add_render_attribute('button', 'class', 'elementor-button');
        $this->add_render_attribute('button', 'role', 'button');
        if (!empty($settings['button_css_id'])) {
            $this->add_render_attribute('button', 'id', sanitize_text_field($settings['button_css_id']));
        }
        if (!empty($settings['size'])) {
            $this->add_render_attribute('button', 'class', 'elementor-size-' . sanitize_text_field($settings['size']));
        }
        if ($settings['hover_animation']) {
            $this->add_render_attribute('button', 'class', 'elementor-animation-' . sanitize_text_field($settings['hover_animation']));
        }
        ?>

		<div <?php 
        echo $this->get_render_attribute_string('wrapper');
        ?>>
			<a <?php 
        echo $this->get_render_attribute_string('button');
        ?>>
			<?php 
        $this->render_text();
        ?>
			</a>
		</div>
		<?php 
    }
    protected function render_text()
    {
        $settings = $this->get_settings_for_display();
        $this->add_render_attribute(['content-wrapper' => ['class' => ['elementor-button-content-wrapper', 'dce-flexbox']], 'icon-align' => ['class' => ['elementor-button-icon', 'elementor-align-icon-' . $settings['icon_align']]], 'text' => ['class' => 'elementor-button-text']]);
        $this->add_inline_editing_attributes('text', 'none');
        ?>
		<span <?php 
        echo $this->get_render_attribute_string('content-wrapper');
        ?>>
			<?php 
        if (!empty($settings['selected_icon']['value'])) {
            ?>
				<span <?php 
            echo $this->get_render_attribute_string('icon-align');
            ?>>
					<?php 
            Icons_Manager::render_icon($settings['selected_icon'], ['aria-hidden' => 'true']);
            ?>
				</span>
			<?php 
        }
        ?>
			<span <?php 
        echo $this->get_render_attribute_string('text');
        ?>><?php 
        echo wp_kses_post($settings['text']);
        ?></span>
		</span>
		<?php 
    }
}
