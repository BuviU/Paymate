<?php
/**
 * ebox Settings Section for Login Registration Metabox.
 *
 * @since 3.0.0
 * @package ebox\Settings\Sections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Section' ) ) && ( ! class_exists( 'ebox_Settings_Section_General_Login_Registration' ) ) ) {
	/**
	 * Class ebox Settings Section for Login Registration Metabox.
	 *
	 * @since 3.0.0
	 */
	class ebox_Settings_Section_General_Login_Registration extends ebox_Settings_Section {

		/**
		 * Protected constructor for class
		 *
		 * @since 3.0.0
		 */
		protected function __construct() {
			$this->settings_page_id = 'ebox_lms_settings';

			// This is the 'option_name' key used in the wp_options table.
			$this->setting_option_key = 'ebox_settings_login_registration';

			// This is the HTML form field prefix used.
			$this->setting_field_prefix = 'ebox_settings_login_registration';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_section_key = 'settings_login_registration';

			// Section label/header.
			$this->settings_section_label = esc_html__( 'Login / Registration Settings', 'ebox' );

			parent::__construct();
		}

		/**
		 * Initialize the metabox settings values.
		 *
		 * @since 3.0.0
		 */
		public function load_settings_values() {
			parent::load_settings_values();

			if ( ! isset( $this->setting_option_values['login_logo'] ) ) {
				$this->setting_option_values['login_logo'] = 0;
			}

			if ( ! isset( $this->setting_option_values['login_logo2'] ) ) {
				$this->setting_option_values['login_logo2'] = 0;
			}
		}

		/**
		 * Initialize the metabox settings fields.
		 *
		 * @since 3.0.0
		 */
		public function load_settings_fields() {

			$this->setting_option_fields = array(
				'login_logo'  => array(
					'name'              => 'login_logo',
					'type'              => 'media-upload',
					'label'             => esc_html__( 'Login Logo', 'ebox' ),
					'value'             => $this->setting_option_values['login_logo'],
					'validate_callback' => array( $this, 'validate_section_field_media_upload' ),
					'validate_args'     => array(
						'allow_empty' => 1,
					),
				),
				'login_logo2' => array(
					'name'              => 'login_logo2',
					'type'              => 'media-upload',
					'label'             => esc_html__( 'Login Logo 2', 'ebox' ),
					'help_text'         => sprintf(
						// translators: placeholder: Default per page number.
						esc_html_x( 'Default per page controls all shortcodes and widget. Default is %d. Set to zero for no pagination.', 'placeholder: Default per page', 'ebox' ),
						ebox_LMS_DEFAULT_WIDGET_PER_PAGE
					),
					'value'             => $this->setting_option_values['login_logo2'],
					'validate_callback' => array( $this, 'validate_section_field_media_upload' ),
					'validate_args'     => array(
						'allow_empty' => 1,
					),
				),
			);

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
			$this->setting_option_fields = apply_filters( 'ebox_settings_fields', $this->setting_option_fields, $this->settings_section_key );

			parent::load_settings_fields();
		}

		/**
		 * Validate settings field.
		 *
		 * @since 3.0.0
		 *
		 * @param string $val Value to be validated.
		 * @param string $key settings fields key.
		 * @param array  $args Settings field args array.
		 *
		 * @return integer $val.
		 */
		public function validate_section_field_media_upload( $val, $key, $args = array() ) {
			// Get the digits only.
			$val = absint( $val );
			if ( ( isset( $args['field']['validate_args']['allow_empty'] ) ) && ( true === $args['field']['validate_args']['allow_empty'] ) && ( empty( $val ) ) ) {
				$val = '';
			}
			return $val;
		}

		// End of functions.
	}
}
add_action(
	'ebox_settings_sections_init',
	function() {
		ebox_Settings_Section_General_Login_Registration::add_section_instance();
	}
);
