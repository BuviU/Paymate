<?php
/**
 * ebox Settings Section for Email Course Purchase Success Metabox.
 *
 * @since 3.6.0
 * @package ebox\Settings\Sections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Section' ) ) && ( ! class_exists( 'ebox_Settings_Section_Emails_Course_Purchase_Success' ) ) ) {

	/**
	 * Class ebox Settings Section for Emails Course Purchase Success Metabox.
	 *
	 * @since 3.6.0
	 */
	class ebox_Settings_Section_Emails_Course_Purchase_Success extends ebox_Settings_Section {
		/**
		 * Protected constructor for class
		 *
		 * @since 3.6.0
		 */
		protected function __construct() {
			$this->settings_page_id = 'ebox_lms_emails';

			// This is the 'option_name' key used in the wp_options table.
			$this->setting_option_key = 'ebox_settings_emails_course_purchase_success';

			// This is the HTML form field prefix used.
			$this->setting_field_prefix = 'ebox_settings_emails_course_purchase_success';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_section_key = 'settings_emails_course_purchase_success';

			// Used to associate this section with the parent section.
			$this->settings_parent_section_key = 'settings_emails_list';

			// Section label/header.
			$this->settings_section_label = sprintf(
				// translators: Placeholder: Course.
				esc_html_x( '%s Purchase Success', 'Placeholder: Course', 'ebox' ),
				ebox_get_custom_label( 'course' )
			);

			parent::__construct();
		}

		/**
		 * Initialize the metabox settings values.
		 *
		 * @since 3.6.0
		 */
		public function load_settings_values() {
			parent::load_settings_values();

			$new_settings = false;
			if ( ! is_array( $this->setting_option_values ) ) {
				$new_settings                = true;
				$this->setting_option_values = array();
			}

			if ( ! isset( $this->setting_option_values['enabled'] ) ) {
				if ( true === $new_settings ) {
					$this->setting_option_values['enabled'] = 'yes';
				} else {
					$this->setting_option_values['enabled'] = '';
				}
			}

			if ( ! isset( $this->setting_option_values['recipients'] ) ) {
				$this->setting_option_values['recipients'] = esc_html__( 'Customer', 'ebox' );
			}
			if ( ! isset( $this->setting_option_values['subject'] ) ) {
				$this->setting_option_values['subject'] = '';
			}
			if ( ! isset( $this->setting_option_values['message'] ) ) {
				$this->setting_option_values['message'] = '';
			}
			if ( ! isset( $this->setting_option_values['content_type'] ) ) {
				$this->setting_option_values['content_type'] = 'html';
			}
		}

		/**
		 * Initialize the metabox settings fields.
		 *
		 * @since 3.6.0
		 */
		public function load_settings_fields() {

			$this->setting_option_fields = array();

			$this->setting_option_fields['enabled']    = array(
				'name'    => 'enabled',
				'type'    => 'checkbox-switch',
				'label'   => esc_html__( 'Active', 'ebox' ),
				'value'   => $this->setting_option_values['enabled'],
				'default' => '',
				'options' => array(
					'on' => '',
					''   => '',
				),
			);
			$this->setting_option_fields['recipients'] = array(
				'name'  => 'recipients',
				'label' => esc_html__( 'Recipient(s)', 'ebox' ),
				'type'  => 'html',
				'value' => $this->setting_option_values['recipients'],
			);

			$this->setting_option_fields['subject'] = array(
				'name'    => 'subject',
				'label'   => esc_html__( 'Subject', 'ebox' ),
				'type'    => 'text',
				'class'   => '-medium',
				'value'   => $this->setting_option_values['subject'],
				'default' => '',
			);
			$this->setting_option_fields['message'] = array(
				'name'              => 'message',
				'label'             => esc_html__( 'Message', 'ebox' ),
				'type'              => 'wpeditor',
				'value'             => $this->setting_option_values['message'],
				'default'           => '',
				'editor_args'       => array(
					'textarea_name' => $this->setting_option_key . '[message]',
					'textarea_rows' => 8,
				),
				'label_description' => '<div>
					<h4>' . esc_html__( 'Supported placeholders', 'ebox' ) . ':</h4>
					<ul>
						<li><span>{user_login}</span> - ' . esc_html__( 'User Login', 'ebox' ) . '</li>
						<li><span>{first_name}</span> - ' . esc_html__( 'User first name', 'ebox' ) . '</li>
						<li><span>{last_name}</span> - ' . esc_html__( 'User last name', 'ebox' ) . '</li>
						<li><span>{display_name}</span> - ' . esc_html__( 'User display name', 'ebox' ) . '</li>
						<li><span>{user_email}</span> - ' . esc_html__( 'User email', 'ebox' ) . '</li>
						<li><span>{post_title}</span> - ' . sprintf(
							// translators: placeholder: Course label.
							esc_html_x( '%s Title', 'placeholder: Course label', 'ebox' ),
							ebox_get_custom_label( 'course' )
						) . '</li>
						<li><span>{post_url}</span> - ' . sprintf(
							// translators: placeholder: Course label.
							esc_html_x( '%s URL', 'placeholder: Course label', 'ebox' ),
							ebox_get_custom_label( 'course' )
						) . '</li>
						<li><span>{site_title}</span> - ' . esc_html__( 'Site Title', 'ebox' ) . '</li>
						<li><span>{site_url}</span> - ' . esc_html__( 'Site URL', 'ebox' ) . '</li>
					</ul>
				</div>',
			);
			$this->setting_option_fields['content_type'] = array(
				'name'    => 'content_type',
				'type'    => 'select',
				'label'   => esc_html__( 'Content Type', 'ebox' ),
				'value'   => $this->setting_option_values['content_type'],
				'default' => 'text/html',
				'options' => array(
					'text/html'  => esc_html__( 'HTML/Text', 'ebox' ),
					'text/plain' => esc_html__( 'Text only', 'ebox' ),
				),
			);

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
			$this->setting_option_fields = apply_filters( 'ebox_settings_fields', $this->setting_option_fields, $this->settings_section_key );

			parent::load_settings_fields();
		}

		/**
		 * Filter the section saved values.
		 *
		 * @since 3.6.0
		 *
		 * @param array  $value                An array of setting fields values.
		 * @param array  $old_value            An array of setting fields old values.
		 * @param string $settings_section_key Settings section key.
		 * @param string $settings_screen_id   Settings screen ID.
		 */
		public function filter_section_save_fields( $value, $old_value, $settings_section_key, $settings_screen_id ) {
			if ( $settings_section_key === $this->settings_section_key ) {
				if ( ! isset( $value['enabled'] ) ) {
					$value['enabled'] = '';
				}

				if ( isset( $_POST['ebox_settings_emails_list_nonce'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
					if ( ! is_array( $old_value ) ) {
						$old_value = array();
					}

					foreach ( $value as $value_idx => $value_val ) {
						$old_value[ $value_idx ] = $value_val;
					}

					$value = $old_value;
				}
			}

			return $value;
		}

		// End of functions.
	}
}
add_action(
	'ebox_settings_sections_init',
	function() {
		ebox_Settings_Section_Emails_Course_Purchase_Success::add_section_instance();
	}
);
