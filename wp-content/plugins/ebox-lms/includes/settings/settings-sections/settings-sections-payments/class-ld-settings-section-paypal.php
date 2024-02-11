<?php
/**
 * ebox Settings Section for PayPal Metabox.
 *
 * @since 2.4.0
 * @package ebox\Settings\Sections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Section' ) ) && ( ! class_exists( 'ebox_Settings_Section_PayPal' ) ) ) {

	/**
	 * Class ebox Settings Section for PayPal Metabox.
	 *
	 * @since 2.4.0
	 */
	class ebox_Settings_Section_PayPal extends ebox_Settings_Section {

		/**
		 * Protected constructor for class
		 *
		 * @since 2.4.0
		 */
		protected function __construct() {
			$this->settings_page_id = 'ebox_lms_payments';

			// This is the 'option_name' key used in the wp_options table.
			$this->setting_option_key = 'ebox_settings_paypal';

			// This is the HTML form field prefix used.
			$this->setting_field_prefix = 'ebox_settings_paypal';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_section_key = 'settings_paypal';

			// Section label/header.
			$this->settings_section_label = esc_html__( 'PayPal Settings', 'ebox' );

			$this->reset_confirm_message = esc_html__( 'Are you sure want to reset the PayPal values?', 'ebox' );

			// Used to associate this section with the parent section.
			$this->settings_parent_section_key = 'settings_payments_list';

			$this->settings_section_listing_label = esc_html__( 'PayPal', 'ebox' );

			parent::__construct();
		}

		/**
		 * Initialize the metabox settings values.
		 *
		 * @since 2.4.0
		 */
		public function load_settings_values() {
			parent::load_settings_values();

			if ( false === $this->setting_option_values ) {
				$ebox_cpt_options = get_option( 'ebox_cpt_options' );

				if ( ( isset( $ebox_cpt_options['modules']['ebox-courses_options'] ) ) && ( ! empty( $ebox_cpt_options['modules']['ebox-courses_options'] ) ) ) {
					foreach ( $ebox_cpt_options['modules']['ebox-courses_options'] as $key => $val ) {
						$key = str_replace( 'ebox-courses_', '', $key );
						if ( 'paypal_sandbox' === $key ) {
							if ( 'on' === $val ) {
								$val = 'yes';
							} else {
								$val = 'no';
							}
						}

						$this->setting_option_values[ $key ] = $val;
					}
				}
			}

			if ( ( isset( $_GET['action'] ) ) && ( 'ld_reset_settings' === $_GET['action'] ) && ( isset( $_GET['page'] ) ) && ( $_GET['page'] == $this->settings_page_id ) ) {
				if ( ( isset( $_GET['ld_wpnonce'] ) ) && ( ! empty( $_GET['ld_wpnonce'] ) ) ) {
					if ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['ld_wpnonce'] ) ), get_current_user_id() . '-' . $this->setting_option_key ) ) {
						if ( ! empty( $this->setting_option_values ) ) {
							foreach ( $this->setting_option_values as $key => $val ) {
								$this->setting_option_values[ $key ] = '';
							}
							$this->save_settings_values();
						}

						$reload_url = remove_query_arg( array( 'action', 'ld_wpnonce' ) );
						ebox_safe_redirect( $reload_url );
					}
				}
			}

			if ( ! isset( $this->setting_option_values['enabled'] ) ) {
				if ( ( isset( $this->setting_option_values['paypal_email'] ) ) && ( ! empty( $this->setting_option_values['paypal_email'] ) ) ) {
					$this->setting_option_values['enabled'] = 'yes';
				} else {
					$this->setting_option_values['enabled'] = '';
				}
			}
		}

		/**
		 * Initialize the metabox settings fields.
		 *
		 * @since 2.4.0
		 */
		public function load_settings_fields() {
			$this->setting_option_fields = array(
				'enabled'          => array(
					'name'    => 'enabled',
					'type'    => 'checkbox-switch',
					'label'   => esc_html__( 'Active', 'ebox' ),
					'value'   => $this->setting_option_values['enabled'],
					'default' => '',
					'options' => array(
						'on' => '',
						''   => '',
					),
				),
				'paypal_sandbox'   => array(
					'name'      => 'paypal_sandbox',
					'type'      => 'checkbox',
					'label'     => esc_html__( 'Test Mode', 'ebox' ),
					'help_text' => esc_html__( 'Check to enable the PayPal sandbox.', 'ebox' ),
					'value'     => isset( $this->setting_option_values['paypal_sandbox'] ) ? $this->setting_option_values['paypal_sandbox'] : 'no',
					'options'   => array(
						'yes' => esc_html__( 'Yes', 'ebox' ),
					),
				),
				'paypal_email'     => array(
					'name'              => 'paypal_email',
					'type'              => 'text',
					'label'             => esc_html__( 'PayPal Email', 'ebox' ),
					'help_text'         => esc_html__( 'Enter your PayPal email here.', 'ebox' ),
					'value'             => ( ( isset( $this->setting_option_values['paypal_email'] ) ) && ( ! empty( $this->setting_option_values['paypal_email'] ) ) ) ? $this->setting_option_values['paypal_email'] : '',
					'class'             => 'regular-text',
					'validate_callback' => array( $this, 'validate_section_paypal_email' ),
				),
				'paypal_country'   => array(
					'name'              => 'paypal_country',
					'type'              => 'text',
					'label'             => esc_html__( 'Country Code', 'ebox' ),
					'help_text'         => sprintf(
						// translators: placeholder: Link to PayPal Country Codes.
						esc_html_x( 'Enter your country code here. See PayPal %s Documentation', 'placeholder: URL to PayPal Country Codes.', 'ebox' ),
						'<a href="https://developer.paypal.com/docs/api/reference/country-codes/" target="_blank">' . esc_html__( 'Country Codes', 'ebox' ) . '</a>'
					),
					'value'             => ( ( isset( $this->setting_option_values['paypal_country'] ) ) && ( ! empty( $this->setting_option_values['paypal_country'] ) ) ) ? $this->setting_option_values['paypal_country'] : 'US',
					'class'             => 'regular-text',
					'validate_callback' => array( $this, 'validate_section_paypal_country' ),
				),
				'paypal_returnurl' => array(
					'name'      => 'paypal_returnurl',
					'type'      => 'text',
					'label'     => esc_html__( 'Return URL', 'ebox' ),
					'help_text' => esc_html__( 'Enter the URL used for completed purchases (typically a thank you page).', 'ebox' ),
					'value'     => ( ( isset( $this->setting_option_values['paypal_returnurl'] ) ) && ( ! empty( $this->setting_option_values['paypal_returnurl'] ) ) ) ? $this->setting_option_values['paypal_returnurl'] : '',
					'class'     => 'regular-text',
				),
				'paypal_cancelurl' => array(
					'name'      => 'paypal_cancelurl',
					'type'      => 'text',
					'label'     => esc_html__( 'Cancel URL', 'ebox' ),
					'help_text' => esc_html__( 'Enter the URL used for purchase cancellations.', 'ebox' ),
					'value'     => ( ( isset( $this->setting_option_values['paypal_cancelurl'] ) ) && ( ! empty( $this->setting_option_values['paypal_cancelurl'] ) ) ) ? $this->setting_option_values['paypal_cancelurl'] : '',
					'class'     => 'regular-text',
				),
				'paypal_notifyurl' => array(
					'name'      => 'paypal_notifyurl',
					'type'      => 'text',
					'label'     => esc_html__( 'Webhook URL', 'ebox' ),
					'help_text' => esc_html__( 'Enter the URL used for IPN notifications.', 'ebox' ),
					'value'     => ! empty( $this->setting_option_values['paypal_notifyurl'] )
						? $this->setting_option_values['paypal_notifyurl']
						: add_query_arg( array( 'ebox-integration' => 'paypal_ipn' ), esc_url_raw( get_site_url() ) ),
					'class'     => 'regular-text',
					'attrs'     => defined( 'ebox_DEBUG' ) && ebox_DEBUG // @phpstan-ignore-line -- Constant can be true/false.
						? array()
						: array(
							'readonly' => 'readonly',
							'disable'  => 'disable',
						),
				),
			);

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
			$this->setting_option_fields = apply_filters( 'ebox_settings_fields', $this->setting_option_fields, $this->settings_section_key );

			parent::load_settings_fields();
		}

		/**
		 * Validate PayPal Email.
		 *
		 * @since 2.4.0
		 *
		 * @param string $val to be validated.
		 * @param string $key Settings key.
		 * @param array  $args Settings field args.
		 *
		 * @return string $val.
		 */
		public static function validate_section_paypal_email( $val, $key, $args = array() ) {
			$val = trim( $val );
			if ( ( ! empty( $val ) ) && ( ! is_email( $val ) ) ) {

				add_settings_error( $args['setting_option_key'], $key, esc_html__( 'PayPal Email must be a valid email.', 'ebox' ), 'error' );
			}

			return $val;
		}

		/**
		 * Validate Settings Country field.
		 *
		 * @since 2.4.0
		 *
		 * @param string $val to be validated.
		 * @param string $key Settings key.
		 * @param array  $args Settings field args.
		 *
		 * @return string $val.
		 */
		public static function validate_section_paypal_country( $val, $key, $args = array() ) {
			if ( ( isset( $args['post_fields']['paypal_email'] ) ) && ( ! empty( $args['post_fields']['paypal_email'] ) ) ) {
				$val = sanitize_text_field( $val );
				if ( empty( $val ) ) {
					add_settings_error( $args['setting_option_key'], $key, esc_html__( 'PayPal Country Code cannot be empty.', 'ebox' ), 'error' );
				} elseif ( strlen( $val ) > 2 ) {
					add_settings_error( $args['setting_option_key'], $key, esc_html__( 'PayPal Country Code should not be longer than 2 letters.', 'ebox' ), 'error' );
				}
			}

			return $val;
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

				if ( isset( $_POST['ebox_settings_payments_list_nonce'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
					if ( ! is_array( $old_value ) ) {
						$old_value = array();
					}

					foreach ( $value as $value_idx => $value_val ) {
						$old_value[ $value_idx ] = $value_val;
					}

					$value = $old_value;
				}

				if (
				isset( $value['paypal_notifyurl'] ) &&
				( ! isset( $old_value['paypal_notifyurl'] ) || $value['paypal_notifyurl'] !== $old_value['paypal_notifyurl'] )
				) {
					ebox_setup_rewrite_flush();
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
		ebox_Settings_Section_PayPal::add_section_instance();
	}
);
