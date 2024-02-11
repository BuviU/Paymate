<?php
/**
 * ebox Settings Section for Support WordPress Plugins Metabox.
 *
 * @since 3.1.0
 * @package ebox\Settings\Sections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Section' ) ) && ( ! class_exists( 'ebox_Settings_Section_Support_WordPress_Plugins' ) ) ) {
	/**
	 * Class ebox Settings Section for Support WordPress Plugins Metabox.
	 *
	 * @since 3.1.0
	 */
	class ebox_Settings_Section_Support_WordPress_Plugins extends ebox_Settings_Section {

		/**
		 * Settings set array for this section.
		 *
		 * @var array $settings_set Array of settings used by this section.
		 */
		protected $settings_set = array();

		/**
		 * Protected constructor for class
		 *
		 * @since 3.1.0
		 */
		protected function __construct() {
			$this->settings_page_id = 'ebox_support';

			// This is the 'option_name' key used in the wp_options table.
			$this->setting_option_key = 'wp_active_plugins';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_section_key = 'settings_support_wp_active_plugins';

			// Section label/header.
			$this->settings_section_label = esc_html__( 'WordPress Active Plugins', 'ebox' );

			$this->load_options = false;

			add_filter( 'ebox_support_sections_init', array( $this, 'ebox_support_sections_init' ) );
			add_action( 'ebox_section_fields_before', array( $this, 'show_support_section' ), 30, 2 );

			parent::__construct();
		}

		/**
		 * Support Sections Init
		 *
		 * @since 3.1.0
		 *
		 * @param array $support_sections Support sections array.
		 */
		public function ebox_support_sections_init( $support_sections = array() ) {
			global $wpdb, $wp_version, $wp_rewrite;
			global $ebox_lms;

			/************************************************************************************************
			 * WordPress Active Plugins.
			 */
			if ( ! isset( $support_sections[ $this->setting_option_key ] ) ) {
				$this->settings_set = array();

				$this->settings_set['header'] = array(
					'html' => $this->settings_section_label,
					'text' => $this->settings_section_label,
				);

				$this->settings_set['columns'] = array(
					'label' => array(
						'html'  => esc_html__( 'Plugin', 'ebox' ),
						'text'  => 'Plugin',
						'class' => 'ebox-support-settings-left',
					),
					'value' => array(
						'html'  => esc_html__( 'Details', 'ebox' ),
						'text'  => 'Details',
						'class' => 'ebox-support-settings-right',
					),
				);

				$this->settings_set['settings'] = array();

				$current_plugins = get_site_transient( 'update_plugins' );

				$all_plugins = get_plugins();

				if ( ! empty( $all_plugins ) ) {
					foreach ( $all_plugins as $plugin_key => $plugin_data ) {
						if ( is_plugin_active( $plugin_key ) ) {

							$plugin_value      = 'Version: ' . $plugin_data['Version'];
							$plugin_value_html = esc_html__( 'Version', 'ebox' ) . ': ' . $plugin_data['Version'];

							if ( isset( $current_plugins->response[ $plugin_key ] ) ) {
								if ( version_compare( $plugin_data['Version'], $current_plugins->response[ $plugin_key ]->new_version, '<' ) ) {
									$plugin_value      .= ' Update available: ' . $current_plugins->response[ $plugin_key ]->new_version . ' (X)';
									$plugin_value_html .= ' <span style="color:red;">' . esc_html__( 'Update available', 'ebox' ) . ': ' . $current_plugins->response[ $plugin_key ]->new_version . '</span>';
								}
							}

							$plugin_value      .= ' Path: ' . $plugin_data['PluginURI'];
							$plugin_value_html .= '<br />' . esc_html__( 'Path', 'ebox' ) . ': ' . $plugin_data['PluginURI'];

							$this->settings_set['settings'][ $plugin_key ] = array(
								'label'      => $plugin_data['Name'],
								'value'      => $plugin_value,
								'value_html' => $plugin_value_html,
							);
						}
					}
				}

				/** This filter is documented in includes/settings/settings-sections/class-ld-settings-section-support-database-tables.php */
				$support_sections[ $this->setting_option_key ] = apply_filters( 'ebox_support_section', $this->settings_set, $this->setting_option_key );
			}

			return $support_sections;
		}

		/**
		 * Show Support Section
		 *
		 * @since 3.1.0
		 *
		 * @param string $settings_section_key Section Key.
		 * @param string $settings_screen_id   Screen ID.
		 */
		public function show_support_section( $settings_section_key = '', $settings_screen_id = '' ) {
			if ( $settings_section_key === $this->settings_section_key ) {
				$support_page_instance = ebox_Settings_Page::get_page_instance( 'ebox_Settings_Page_Support' );
				if ( $support_page_instance ) {
					$support_page_instance->show_support_section( $this->setting_option_key );
				}
			}
		}

		// End of functions.
	}
}
add_action(
	'ebox_settings_sections_init',
	function() {
		ebox_Settings_Section_Support_WordPress_Plugins::add_section_instance();
	}
);
