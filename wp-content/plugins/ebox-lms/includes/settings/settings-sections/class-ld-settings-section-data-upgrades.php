<?php
/**
 * ebox Settings Section for Data Upgrades Metabox.
 *
 * @since 2.6.0
 * @package ebox\Settings\Sections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Section' ) ) && ( ! class_exists( 'ebox_Settings_Section_Data_Upgrades' ) ) ) {
	/**
	 * Class ebox Settings Section for Data Upgrades Metabox.
	 *
	 * @since 2.6.0
	 */
	class ebox_Settings_Section_Data_Upgrades extends ebox_Settings_Section {

		/**
		 * Protected constructor for class
		 *
		 * @since 2.6.0
		 */
		protected function __construct() {
			$this->settings_page_id = 'ebox_lms_advanced';

			// This is the 'option_name' key used in the wp_options table.
			$this->setting_option_key = 'ebox_data_upgrades';

			// This is the HTML form field prefix used.
			$this->setting_field_prefix = 'ebox_data_upgrades';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_section_key = 'settings_data_upgrades';

			// Section label/header.
			$this->settings_section_label = esc_html__( 'Data Upgrades', 'ebox' );

			add_action( 'ebox_settings_page_load', array( $this, 'load_settings_page' ), 30, 2 );

			parent::__construct();

			add_filter(
				'ebox_admin_settings_advanced_sections_with_hidden_metaboxes',
				function( array $section_keys ) {
					$section_keys[] = $this->settings_section_key;

					return $section_keys;
				}
			);
		}

		/**
		 * Show Settings Section meta box.
		 *
		 * @since 2.6.0
		 */
		public function show_meta_box() {
			$ld_admin_data_upgrades = ebox_Admin_Data_Upgrades::get_instance();
			$ld_admin_data_upgrades->admin_page();
		}

		/**
		 * Load settings page.
		 *
		 * Called from action `ebox_settings_page_load`.
		 *
		 * @since 3.6.0
		 *
		 * @param string $settings_screen_id Settings Screen ID.
		 * @param string $settings_page_id   Settings Page ID.
		 */
		public function load_settings_page( $settings_screen_id = '', $settings_page_id = '' ) {
			if ( $settings_page_id === $this->settings_page_id ) {
				global $ebox_assets_loaded;

				wp_enqueue_style(
					'ebox-admin-style',
					ebox_LMS_PLUGIN_URL . 'assets/css/ebox-admin-style' . ebox_min_asset() . '.css',
					array(),
					ebox_SCRIPT_VERSION_TOKEN
				);
				wp_style_add_data( 'ebox-admin-style', 'rtl', 'replace' );
				$ebox_assets_loaded['styles']['ebox-admin-style'] = __FUNCTION__;

				wp_enqueue_script(
					'ebox-admin-settings-data-upgrades-script',
					ebox_LMS_PLUGIN_URL . 'assets/js/ebox-admin-settings-data-upgrades' . ebox_min_asset() . '.js',
					array( 'jquery' ),
					ebox_SCRIPT_VERSION_TOKEN,
					true
				);
				$ebox_assets_loaded['scripts']['ebox-admin-settings-data-upgrades-script'] = __FUNCTION__;
			}
		}
	}
}
add_action(
	'ebox_settings_sections_init',
	function() {
		ebox_Settings_Section_Data_Upgrades::add_section_instance();
	}
);
