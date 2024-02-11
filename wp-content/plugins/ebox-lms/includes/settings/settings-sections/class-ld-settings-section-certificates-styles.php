<?php
/**
 * ebox Settings Section for Certificates Custom Styles Metabox.
 *
 * @since 3.2.0
 * @package ebox\Settings\Sections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Section' ) ) && ( ! class_exists( 'ebox_Settings_Certificates_Styles' ) ) ) {
	/**
	 * Class ebox Settings Section for Certificates Custom Styles Metabox.
	 *
	 * @since 3.2.0
	 */
	class ebox_Settings_Certificates_Styles extends ebox_Settings_Section {

		/**
		 * Protected constructor for class
		 *
		 * @since 3.2.0
		 */
		protected function __construct() {

			// What screen ID are we showing on.
			$this->settings_screen_id = 'ebox-certificates_page_certificate-options';

			// The page ID (different than the screen ID).
			$this->settings_page_id = 'certificate-options';

			// This is the 'option_name' key used in the wp_options table.
			$this->setting_option_key = 'ebox_settings_certificates_styles';

			// This is the HTML form field prefix used.
			$this->setting_field_prefix = 'ebox_settings_certificates_styles';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_section_key = 'styles';

			// Section label/header.
			$this->settings_section_label = esc_html__( 'Certificate Custom Styles', 'ebox' );

			// Used to show the section description above the fields. Can be empty.
			$this->settings_section_description = esc_html__( 'Add Custom Styles (CSS) to be used on all legacy certificates, does not work on certificates created with the Certificate Builder addon.', 'ebox' );

			parent::__construct();
		}

		/**
		 * Initialize the metabox settings values.
		 *
		 * @since 3.2.0
		 */
		public function load_settings_values() {
			parent::load_settings_values();

			if ( ( false === $this->setting_option_values ) || ( '' === $this->setting_option_values ) ) {
				if ( '' === $this->setting_option_values ) {
					$this->setting_option_values = array();
				}

				$this->setting_option_values = array(
					'styles' => '',
				);
			}

			if ( ! isset( $this->setting_option_values['styles'] ) ) {
				$this->setting_option_values['styles'] = '';
			}
		}

		/**
		 * Initialize the metabox settings fields.
		 *
		 * @since 3.2.0
		 */
		public function load_settings_fields() {

			$this->setting_option_fields = array(
				'styles' => array(
					'name'      => 'styles',
					'type'      => 'textarea',
					'label'     => esc_html__( 'Custom Styles (CSS)', 'ebox' ),
					'help_text' => esc_html__( 'Add custom styles (CSS) to be used on legacy certificates.', 'ebox' ),
					'value'     => $this->setting_option_values['styles'],
					'attrs'     => array(
						'rows' => '8',

					),
				),
			);

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
			$this->setting_option_fields = apply_filters( 'ebox_settings_fields', $this->setting_option_fields, $this->settings_section_key );

			parent::load_settings_fields();
		}

		/**
		 * Intercept the WP options save logic and check that we have a valid nonce.
		 *
		 * @since 3.2.0
		 *
		 * @param array  $new_values         Array of section fields values.
		 * @param array  $old_values         Array of old values.
		 * @param string $setting_option_key Section option key should match $this->setting_option_key.
		 */
		public function section_pre_update_option( $new_values = '', $old_values = '', $setting_option_key = '' ) {
			if ( $setting_option_key === $this->setting_option_key ) {
				$new_values = parent::section_pre_update_option( $new_values, $old_values, $setting_option_key );
			}

			return $new_values;
		}

		// End of functions.
	}
}

add_action(
	'ebox_settings_sections_init',
	function() {
		ebox_Settings_Certificates_Styles::add_section_instance();
	}
);
