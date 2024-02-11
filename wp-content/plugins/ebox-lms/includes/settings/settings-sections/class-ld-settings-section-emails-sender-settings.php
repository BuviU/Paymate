<?php
/**
 * ebox Settings Section for Email Sender Settings Metabox.
 *
 * @since 3.6.0
 * @package ebox\Settings\Sections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Section' ) ) && ( ! class_exists( 'ebox_Settings_Section_Emails_Sender_Settings' ) ) ) {

	/**
	 * Class ebox Settings Section for Emails Sender Settings Metabox.
	 *
	 * @since 3.6.0
	 */
	class ebox_Settings_Section_Emails_Sender_Settings extends ebox_Settings_Section {

		/**
		 * Current Section
		 *
		 * @var string $current_section
		 */
		private $current_section = '';

		/**
		 * Protected constructor for class
		 *
		 * @since 3.6.0
		 */
		protected function __construct() {
			$this->settings_page_id = 'ebox_lms_emails';

			// This is the 'option_name' key used in the wp_options table.
			$this->setting_option_key = 'ebox_settings_emails_sender';

			// This is the HTML form field prefix used.
			$this->setting_field_prefix = 'ebox_settings_emails_sender';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_section_key = 'settings_emails_sender';

			// Section label/header.
			$this->settings_section_label = esc_html__( 'Email Sender Settings', 'ebox' );

			parent::__construct();
		}

		/**
		 * Initialize the metabox settings values.
		 *
		 * @since 3.6.0
		 */
		public function load_settings_values() {
			parent::load_settings_values();

			if ( ! isset( $this->setting_option_values['from_name'] ) ) {
				$this->setting_option_values['from_name'] = '';
			}

			if ( ! isset( $this->setting_option_values['from_email'] ) ) {
				$this->setting_option_values['from_email'] = '';
			}
		}

		/**
		 * Initialize the metabox settings fields.
		 *
		 * @since 3.6.0
		 */
		public function load_settings_fields() {

			$this->setting_option_fields = array();

			$this->setting_option_fields['from_name'] = array(
				'name'        => 'from_name',
				'label'       => esc_html__( '"From" name', 'ebox' ),
				'type'        => 'text',
				'help_text'   => esc_html__( 'How the sender name appears in outgoing emails.', 'ebox' ),
				'value'       => $this->setting_option_values['from_name'],
				'placeholder' => esc_html__( 'If empty will use site title', 'ebox' ),
			);

			$this->setting_option_fields['from_email'] = array(
				'name'        => 'from_email',
				'label'       => esc_html__( '"From" email', 'ebox' ),
				'type'        => 'email',
				'help_text'   => esc_html__( 'How the sender email appears in outgoing emails.', 'ebox' ),
				'value'       => $this->setting_option_values['from_email'],
				'placeholder' => esc_html__( 'If empty will use site administration email address', 'ebox' ),
			);

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
			$this->setting_option_fields = apply_filters( 'ebox_settings_fields', $this->setting_option_fields, $this->settings_section_key );

			parent::load_settings_fields();
		}


		// End of functions.
	}
}
add_action(
	'ebox_settings_sections_init',
	function() {
		ebox_Settings_Section_Emails_Sender_Settings::add_section_instance();
	}
);
