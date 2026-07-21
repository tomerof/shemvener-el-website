<?php

// SPDX-FileCopyrightText: 2018-2026 Ovation S.r.l. <help@dynamic.ooo>
// SPDX-License-Identifier: GPL-3.0-or-later
namespace DynamicContentForElementor\AdminPages\Features;

use DynamicContentForElementor\Assets;
use DynamicContentForElementor\LicenseSystem;
use DynamicContentForElementor\Plugin;
use DynamicOOO\PluginUtils\AdminPages\Pages\Base;

class FeaturesPage extends Base {
	/** @var array<string,mixed> */
	private $tabs;

	public function __construct( $plugin_utils_manager ) {
		parent::__construct( $plugin_utils_manager );
		$this->tabs = $this->init_tabs();
	}

	/** @return array<string,mixed> */
	public function init_tabs() {
		$tabs = [
			new class('widgets') extends GroupedListTab {
				/** @return string */
				public function get_label() {
					return esc_html__( 'Widgets', 'dynamic-content-for-elementor' );
				}
				/** @return string */
				public function get_groups_key() {
					return 'category';
				}
				/** @return array<string,mixed> */
				public function get_groups() {
					return \DynamicContentForElementor\Features::get_widgets_groups();
				}
				/** @return boolean */
				public function should_calculate_usage() {
					return true;
				}
				/**
				 * @param string $feature
				 * @param array<mixed> $elementor_controls_usage
				 * @return int
				 */
				public function calculate_usage( $feature, $elementor_controls_usage ) {
					$calculate_usage = 0;
					foreach ( $elementor_controls_usage as $key => $value ) {
						if ( isset( $elementor_controls_usage[ $key ][ $feature ] ) ) {
							$calculate_usage += $elementor_controls_usage[ $key ][ $feature ]['count'];
						}
					}
					return $calculate_usage;
				}
				/** @return array<mixed> */
				public function get_all_tab_features() {
					$features = Plugin::instance()->features->filter( [ 'type' => 'widget' ] );
					// We want all extension that are not legacy
					$features = array_filter( $features, function ( $f ) {
						return ! ( $f['legacy'] ?? false );
					});
					return $features;
				}
			},
			new class('extensions') extends GroupedListTab {
				/** @return string */
				public function get_label() {
					return esc_html__( 'Extensions', 'dynamic-content-for-elementor' );
				}
				/** @return array<string,mixed> */
				public function get_groups() {
					return \DynamicContentForElementor\Features::get_extensions_groups();
				}
				/** @return string */
				public function get_groups_key() {
					return 'category';
				}
				/** @return array<mixed> */
				public function get_all_tab_features() {
					$extensions = Plugin::instance()->features->filter( [ 'type' => 'extension' ] );
					// We want all extension that are not dynamic tag or legacy
					$extensions = array_filter( $extensions, function ( $e ) {
						return ( $e['extension_type'] ?? '' ) !== 'dynamic-tag' && ! ( $e['legacy'] ?? false );
					});
					return $extensions;
				}
			},
			new class( 'dynamic-tags' ) extends GroupedListTab {
				/** @return string */
				public function get_label() {
					return esc_html__( 'Dynamic Tags', 'dynamic-content-for-elementor' );
				}
				/** @return array<string,mixed> */
				public function get_groups() {
					return \DynamicContentForElementor\Features::get_dynamic_tags_groups();
				}
				/** @return string */
				public function get_groups_key() {
					return 'category';
				}
				/** @return array<mixed> */
				public function get_all_tab_features() {
					$extensions = Plugin::instance()->features->filter( [ 'type' => 'extension' ] );
					$bundled = Plugin::instance()->features->filter_bundled( [ 'type' => 'extension' ] );
					$extensions += $bundled;
					// We want all extension that are dynamic tag and not legacy
					$extensions = array_filter( $extensions, function ( $e ) {
						return ( $e['extension_type'] ?? '' ) === 'dynamic-tag' && ! ( $e['legacy'] ?? false );
					});
					return $extensions;
				}
			},
			new class( 'page-settings' ) extends ListTab {
				/** @return string */
				public function get_label() {
					return esc_html__( 'Page Settings', 'dynamic-content-for-elementor' );
				}
				/** @return array<mixed> */
				public function get_all_tab_features() {
					$features = Plugin::instance()->features->filter( [ 'type' => 'page-setting' ] );
					$features = array_filter( $features, function ( $f ) {
						return ! ( $f['legacy'] ?? false );
					});
					return $features;
				}
			},
			new FrontendNavigator(),
			new TokensSettings(),
			new TemplateSystem(),
			new class( 'legacy' ) extends GroupedListTab {
				/**
				 * @return boolean
				 */
				public function should_display_count() {
					return false;
				}
				/**
				 * @return string
				 */
				public function get_label() {
					return esc_html__( 'Legacy', 'dynamic-content-for-elementor' );
				}
				/**
				 * @return string
				 */
				public function get_groups_key() {
					return 'type';
				}
				/**
				 * @return array<string,string>
				 */
				public function get_groups() {
					return [
						'widget' => esc_html__( 'Widgets', 'dynamic-content-for-elementor' ),
						'extension' => esc_html__( 'Extensions', 'dynamic-content-for-elementor' ),
						'global-setting' => esc_html__( 'Global Settings', 'dynamic-content-for-elementor' ),
					];
				}
				/**
				 * @return array<mixed>
				 */
				public function get_all_tab_features() {
					return Plugin::instance()->features->filter( [ 'legacy' => true ] );
				}
			},
		];
		$tabs_dict = [];
		foreach ( $tabs as $tab ) {
			$tabs_dict[ $tab->get_name() ] = $tab;
		}
		return $tabs_dict;
	}

	/**
	 * Render page content
	 *
	 * @return void
	 */
	public function render_content() {
		// check user capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {
			if ( ! isset( $_POST['dce-settings-page'] )
				|| ! wp_verify_nonce( $_POST['dce-settings-page'], 'dce-settings-page' )
			) {
				wp_die( esc_html__( 'Nonce verification error.', 'dynamic-content-for-elementor' ) );
			}
		}

		// add error/update messages
		// check if the user have submitted the settings
		// WordPress will add the "settings-updated" $_GET parameter to the url
		if ( isset( $_GET['settings-updated'] ) ) {
			// add settings saved message with the class of "updated"
			add_settings_error( 'dce_messages', 'dce_message', esc_html__( 'Settings Saved', 'dynamic-content-for-elementor' ), 'updated' );
		}

		Plugin::instance()->assets::enqueue_dce_icons();

		// show error/update messages
		settings_errors( 'dce_messages' );
		$admin_page_title = esc_html( get_admin_page_title() );
		echo '<div id="dce-settings-tabs-wrapper" class="nav-tab-wrapper">';
		$active_tab_name = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'widgets';
		if ( ! isset( $this->tabs[ $active_tab_name ] ) ) {
			$active_tab_name = 'widgets';
		}
		foreach ( $this->tabs as $tab ) {
			$tab_class = '';
			if ( $active_tab_name === $tab->get_name() ) {
				$tab_class = 'nav-tab-active';
			}
			$tab_name = $tab->get_name();
			$tab_url = add_query_arg( [
				'page' => 'dce-features',
				'tab' => $tab_name,
			], admin_url( 'admin.php' ) );
			echo '<a class="' . esc_attr( 'nav-tab ' . $tab_class ) . '" href="' . esc_url( $tab_url ) . '">';
			echo esc_html( $tab->get_label() );
			if ( $tab->should_display_count() ) {
				$count = $tab->get_count();
				echo '&nbsp;<span class="dce-badge">' . esc_html( (string) $count ) . '</span>';
			}
			echo '</a>';
		}
		echo '</div>';
		echo '<div class="dce-container" style="display: block;">';
		$active_tab = $this->tabs[ $active_tab_name ];
		$active_tab->render();
		echo '</div>';
	}
}
