<?php
/**
 * ebox Settings Metabox for Team Access Settings.
 *
 * @since 3.2.0
 * @package ebox\Settings\Metaboxes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Metabox' ) ) && ( ! class_exists( 'ebox_Settings_Metabox_Team_Access_Settings' ) ) ) {
	/**
	 * Class ebox Settings Metabox for Team Access Settings.
	 *
	 * @since 3.2.0
	 */
	class ebox_Settings_Metabox_Team_Access_Settings extends ebox_Settings_Metabox {

		/**
		 * Public constructor for class
		 *
		 * @since 3.2.0
		 */
		public function __construct() {
			// What screen ID are we showing on.
			$this->settings_screen_id = 'teams';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_metabox_key = 'ebox-team-access-settings';

			// Section label/header.
			$this->settings_section_label = sprintf(
				// translators: placeholder: Team.
				esc_html_x( '%s Access Settings', 'placeholder: Team', 'ebox' ),
				ebox_get_custom_label( 'team' )
			);

			$this->settings_section_description = sprintf(
				// translators: placeholder: team.
				esc_html_x( 'Controls how users will gain access to the %s', 'placeholder: team', 'ebox' ),
				esc_html( ebox_get_custom_label_lower( 'team' ) )
			);

			add_filter( 'ebox_metabox_save_fields_' . $this->settings_metabox_key, array( $this, 'filter_saved_fields' ), 30, 3 );
			add_filter( 'ebox_admin_settings_data', array( $this, 'ebox_admin_settings_data' ), 30, 1 );

			// Map internal settings field ID to legacy field ID.
			$this->settings_fields_map = array(
				'team_price_type'                         => 'team_price_type',
				'team_price_type_paynow_price'            => 'team_price',
				'team_price_type_subscribe_price'         => 'team_price',
				'team_price_type_subscribe_billing_cycle' => 'team_price_type_subscribe_billing_cycle',
				'team_price_type_subscribe_billing_recurring_times' => 'post_no_of_cycles',

				'team_price_type_closed_custom_button_label' => 'custom_button_label',
				'team_price_type_closed_custom_button_url' => 'custom_button_url',
				'team_price_type_closed_price'            => 'team_price',
				'team_price_billing_p3'                   => 'team_price_billing_p3',
				'team_price_billing_t3'                   => 'team_price_billing_t3',

				'team_trial_price'                        => 'team_trial_price',
				'team_trial_duration_t1'                  => 'team_trial_duration_t1',
				'team_trial_duration_p1'                  => 'team_trial_duration_p1',
				'team_price_type_paynow_enrollment_url'   => 'team_price_type_paynow_enrollment_url',
				'team_price_type_subscribe_enrollment_url' => 'team_price_type_subscribe_enrollment_url',
			);

			parent::__construct();
		}

		/**
		 * Add script data to array.
		 *
		 * @since 3.2.0
		 *
		 * @param array $script_data Script data array to be sent out to browser.
		 *
		 * @return array $script_data
		 */
		public function ebox_admin_settings_data( $script_data = array() ) {

			$script_data['valid_recurring_paypal_day_range']   = esc_html__( 'Valid range is 1 to 90 when the Billing Cycle is set to days.', 'ebox' );
			$script_data['valid_recurring_paypal_week_range']  = esc_html__( 'Valid range is 1 to 52 when the Billing Cycle is set to weeks.', 'ebox' );
			$script_data['valid_recurring_paypal_month_range'] = esc_html__( 'Valid range is 1 to 24 when the Billing Cycle is set to months.', 'ebox' );
			$script_data['valid_recurring_paypal_year_range']  = esc_html__( 'Valid range is 1 to 5 when the Billing Cycle is set to years.', 'ebox' );

			return $script_data;
		}

		/**
		 * Initialize the metabox settings values.
		 *
		 * @since 3.2.0
		 */
		public function load_settings_values() {
			parent::load_settings_values();
			if ( true === $this->settings_values_loaded ) {

				if ( ! isset( $this->setting_option_values['team_price_type'] ) ) {
					$this->setting_option_values['team_price_type'] = ebox_DEFAULT_GROUP_PRICE_TYPE;
				}

				if ( ! isset( $this->setting_option_values['team_price_type_paynow_price'] ) ) {
					$this->setting_option_values['team_price_type_paynow_price'] = '';
				}

				if ( ! isset( $this->setting_option_values['team_price_type_subscribe_price'] ) ) {
					$this->setting_option_values['team_price_type_subscribe_price'] = '';
				}

				if ( ! isset( $this->setting_option_values['team_price_type_subscribe_billing_recurring_times'] ) ) {
					$this->setting_option_values['team_price_type_subscribe_billing_recurring_times'] = '';
				}

				if ( ! isset( $this->setting_option_values['team_price_type_closed_price'] ) ) {
					$this->setting_option_values['team_price_type_closed_price'] = '';
				}

				if ( ! isset( $this->setting_option_values['team_price_type_closed_custom_button_url'] ) ) {
					$this->setting_option_values['team_price_type_closed_custom_button_url'] = '';
				}

				if ( ! isset( $this->setting_option_values['team_price_type_closed_custom_button_label'] ) ) {
					$this->setting_option_values['team_price_type_closed_custom_button_label'] = '';
				}

				if ( ! isset( $this->setting_option_values['team_trial_price'] ) ) {
					$this->setting_option_values['team_trial_price'] = '';
				}

				if ( ! isset( $this->setting_option_values['team_trial_duration_t1'] ) ) {
					$this->setting_option_values['team_trial_duration_t1'] = '';
				}

				if ( ! isset( $this->setting_option_values['team_trial_duration_p1'] ) ) {
					$this->setting_option_values['team_trial_duration_p1'] = '';

				}
				if ( ! isset( $this->setting_option_values['team_price_type_paynow_enrollment_url'] ) ) {
					$this->setting_option_values['team_price_type_paynow_enrollment_url'] = '';
				}

				if ( ! isset( $this->setting_option_values['team_price_type_subscribe_enrollment_url'] ) ) {
					$this->setting_option_values['team_price_type_subscribe_enrollment_url'] = '';
				}
			}

			// Ensure all settings fields are present.
			foreach ( $this->settings_fields_map as $_internal => $_external ) {
				if ( ! isset( $this->setting_option_values[ $_internal ] ) ) {
					$this->setting_option_values[ $_internal ] = '';
				}
			}

			// Clear out the price type fields we are not using.
			switch ( $this->setting_option_values['team_price_type'] ) {
				case 'paynow':
					$this->setting_option_values['team_price_type_subscribe_price']                   = '';
					$this->setting_option_values['team_price_type_subscribe_billing_cycle']           = '';
					$this->setting_option_values['team_price_type_subscribe_billing_recurring_times'] = '';
					$this->setting_option_values['team_price_type_closed_price']                      = '';
					$this->setting_option_values['team_price_type_closed_custom_button_label']        = '';
					$this->setting_option_values['team_price_type_closed_custom_button_url']          = '';
					$this->setting_option_values['team_trial_price']                                  = '';
					$this->setting_option_values['team_trial_duration_t1']                            = '';
					$this->setting_option_values['team_trial_duration_p1']                            = '';
					$this->setting_option_values['team_price_type_subscribe_enrollment_url']          = '';
					break;

				case 'subscribe':
					$this->setting_option_values['team_price_type_paynow_price']               = '';
					$this->setting_option_values['team_price_type_closed_price']               = '';
					$this->setting_option_values['team_price_type_closed_custom_button_label'] = '';
					$this->setting_option_values['team_price_type_closed_custom_button_url']   = '';
					$this->setting_option_values['team_price_type_paynow_enrollment_url']      = '';
					break;

				case 'closed':
					$this->setting_option_values['team_price_type_subscribe_price']                   = '';
					$this->setting_option_values['team_price_type_subscribe_billing_cycle']           = '';
					$this->setting_option_values['team_price_type_subscribe_billing_recurring_times'] = '';
					$this->setting_option_values['team_price_type_paynow_price']                      = '';
					$this->setting_option_values['team_trial_price']                                  = '';
					$this->setting_option_values['team_trial_duration_t1']                            = '';
					$this->setting_option_values['team_trial_duration_p1']                            = '';
					$this->setting_option_values['team_price_type_paynow_enrollment_url']             = '';
					$this->setting_option_values['team_price_type_subscribe_enrollment_url']          = '';
					break;

				case 'free':
				default:
					$this->setting_option_values['team_price_type']                                   = 'free';
					$this->setting_option_values['team_price_type_paynow_price']                      = '';
					$this->setting_option_values['team_price_type_subscribe_price']                   = '';
					$this->setting_option_values['team_price_type_subscribe_billing_cycle']           = '';
					$this->setting_option_values['team_price_type_subscribe_billing_recurring_times'] = '';
					$this->setting_option_values['team_price_type_closed_price']                      = '';
					$this->setting_option_values['team_price_type_closed_custom_button_label']        = '';
					$this->setting_option_values['team_price_type_closed_custom_button_url']          = '';
					$this->setting_option_values['team_trial_price']                                  = '';
					$this->setting_option_values['team_trial_duration_t1']                            = '';
					$this->setting_option_values['team_trial_duration_p1']                            = '';
					$this->setting_option_values['team_price_type_paynow_enrollment_url']             = '';
					$this->setting_option_values['team_price_type_subscribe_enrollment_url']          = '';
					break;
			}
		}

		/**
		 * Initialize the metabox settings fields.
		 *
		 * @since 3.2.0
		 */
		public function load_settings_fields() {
			global $ebox_lms;

			$this->settings_sub_option_fields = array();

			$this->setting_option_fields = array(
				'team_price_type_paynow_price'          => array(
					'name'    => 'team_price_type_paynow_price',
					'label'   => sprintf(
						// translators: placeholder: Team.
						esc_html_x( '%s Price', 'placeholder: Team', 'ebox' ),
						ebox_get_custom_label( 'team' )
					),
					'type'    => 'text',
					'class'   => '-medium',
					'value'   => $this->setting_option_values['team_price_type_paynow_price'],
					'default' => '',
					'rest'    => array(
						'show_in_rest' => ebox_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'price_type_paynow_price',
								// translators: placeholder: Team.
								'description' => sprintf( esc_html_x( 'Pay Now %s Price', 'placeholder: Team', 'ebox' ), ebox_Custom_Label::get_label( 'team' ) ),
								'type'        => 'string',
								'default'     => '',
							),
						),
					),
				),
				'team_price_type_paynow_enrollment_url' => array(
					'name'      => 'team_price_type_paynow_enrollment_url',
					'label'     => sprintf(
						// translators: placeholder: Team.
						esc_html_x( '%s Enrollment URL', 'placeholder: Team', 'ebox' ),
						ebox_get_custom_label( 'team' )
					),
					'type'      => 'url',
					'class'     => 'full-text',
					'value'     => $this->setting_option_values['team_price_type_paynow_enrollment_url'],
					'help_text' => sprintf(
						// translators: placeholder: team.
						esc_html_x( 'Enter the URL of the page you want to redirect your enrollees after signing up for this specific %s', 'placeholder: team', 'ebox' ),
						ebox_get_custom_label_lower( 'team' )
					),
					'default'   => '',
					'rest'      => array(
						'show_in_rest' => ebox_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'team_price_type_paynow_enrollment_url',
								// translators: placeholder: team.
								'description' => sprintf( esc_html_x( 'Pay Now %s Enrollment URL', 'placeholder: team', 'ebox' ), ebox_get_custom_label_lower( 'team' ) ),
								'type'        => 'string',
								'default'     => '',
							),
						),
					),
				),
			);
			parent::load_settings_fields();
			$this->settings_sub_option_fields['team_price_type_paynow_fields'] = $this->setting_option_fields;

			$this->setting_option_fields = array(
				'team_price_type_subscribe_price'         => array(
					'name'    => 'team_price_type_subscribe_price',
					'label'   => sprintf(
						// translators: placeholder: Team.
						esc_html_x( '%s Price', 'placeholder: Team', 'ebox' ),
						ebox_get_custom_label( 'team' )
					),
					'type'    => 'text',
					'class'   => '-medium',
					'value'   => $this->setting_option_values['team_price_type_subscribe_price'],
					'default' => '',
					'rest'    => array(
						'show_in_rest' => ebox_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'price_type_subscribe_price',
								// translators: placeholder: Team.
								'description' => sprintf( esc_html_x( 'Subscribe %s Price', 'placeholder: Team', 'ebox' ), ebox_get_custom_label( 'team' ) ),
								'type'        => 'string',
								'default'     => '',
							),
						),
					),
				),
				'team_price_type_subscribe_billing_cycle' => array(
					'name'  => 'team_price_type_subscribe_billing_cycle',
					'label' => esc_html__( 'Billing Cycle', 'ebox' ),
					'type'  => 'custom',
					'html'  => ebox_billing_cycle_setting_field_html(
						0,
						ebox_get_post_type_slug( 'team' )
					),
				),
				'team_price_type_subscribe_billing_recurring_times' => array(
					'name'      => 'team_price_type_subscribe_billing_recurring_times',
					'label'     => esc_html__( 'Recurring Times', 'ebox' ),
					'type'      => 'text',
					'class'     => '-medium',
					'value'     => $this->setting_option_values['team_price_type_subscribe_billing_recurring_times'],
					'help_text' => esc_html__( 'How many times the billing cycle repeats. Leave empty for unlimited repeats.', 'ebox' ),
					'default'   => '',
				),
				'team_trial_price'                        => array(
					'name'      => 'team_trial_price',
					'label'     => sprintf(
						// translators: placeholder: Team.
						esc_html_x( '%s Trial Price', 'placeholder: Team', 'ebox' ),
						ebox_get_custom_label( 'team' )
					),
					'type'      => 'text',
					'class'     => '-medium',
					'value'     => $this->setting_option_values['team_trial_price'],
					'help_text' => sprintf(
						// translators: placeholder: team.
						esc_html_x( 'Enter the price for the trial period for this %s', 'placeholder: team', 'ebox' ),
						ebox_get_custom_label( 'team' )
					),
					'default'   => '',
					'rest'      => array(
						'show_in_rest' => ebox_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'trial_price',
								// translators: placeholder: team.
								'description' => sprintf( esc_html_x( '%s Trial Price', 'placeholder: team', 'ebox' ), ebox_get_custom_label( 'team' ) ),
								'type'        => 'string',
								'default'     => '',
							),
						),
					),
				),
				'team_trial_duration'                     => array(
					'name'      => 'team_trial_duration',
					'label'     => esc_html__( 'Trial Duration', 'ebox' ),
					'type'      => 'custom',
					'html'      => ebox_trial_duration_setting_field_html(
						0,
						ebox_get_post_type_slug( 'team' )
					),
					// translators: team.
					'help_text' => sprintf( esc_html_x( 'The length of the trial period, after the trial is over, the normal %s price billing goes into effect.', 'placeholder: team', 'ebox' ), ebox_get_custom_label_lower( 'team' ) ),
				),
				'team_price_type_subscribe_enrollment_url' => array(
					'name'      => 'team_price_type_subscribe_enrollment_url',
					'label'     => sprintf(
						// translators: placeholder: Team.
						esc_html_x( '%s Enrollment URL', 'placeholder: Team', 'ebox' ),
						ebox_get_custom_label( 'team' )
					),
					'type'      => 'url',
					'class'     => 'full-text',
					'value'     => $this->setting_option_values['team_price_type_subscribe_enrollment_url'],
					'help_text' => sprintf(
						// translators: placeholder: team.
						esc_html_x( 'Enter the URL of the page you want to redirect your enrollees after signing up for this specific %s', 'placeholder: team', 'ebox' ),
						ebox_get_custom_label_lower( 'team' )
					),
					'default'   => '',
					'rest'      => array(
						'show_in_rest' => ebox_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'team_price_type_subscribe_enrollment_url',
								// translators: placeholder: team.
								'description' => sprintf( esc_html_x( 'Pay Now %s Enrollment URL', 'placeholder: team', 'ebox' ), ebox_get_custom_label_lower( 'team' ) ),
								'type'        => 'string',
								'default'     => '',
							),
						),
					),
				),
			);
			parent::load_settings_fields();
			$this->settings_sub_option_fields['team_price_type_subscribe_fields'] = $this->setting_option_fields;

			$this->setting_option_fields = array(
				'team_price_type_closed_price' => array(
					'name'    => 'team_price_type_closed_price',
					'label'   => sprintf(
						// translators: placeholder: Team.
						esc_html_x( '%s Price', 'placeholder: Team', 'ebox' ),
						ebox_get_custom_label( 'team' )
					),
					'type'    => 'text',
					'class'   => '-medium',
					'value'   => $this->setting_option_values['team_price_type_closed_price'],
					'default' => '',
					'rest'    => array(
						'show_in_rest' => ebox_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'price_type_closed_price',
								// translators: placeholder: Team.
								'description' => sprintf( esc_html_x( 'Closed %s Price', 'placeholder: Team', 'ebox' ), ebox_get_custom_label( 'team' ) ),
								'type'        => 'string',
								'default'     => '',
							),
						),
					),
				),
				'team_price_type_closed_custom_button_url' => array(
					'name'      => 'team_price_type_closed_custom_button_url',
					'label'     => esc_html__( 'Button URL', 'ebox' ),
					'type'      => 'url',
					'class'     => 'full-text',
					'value'     => $this->setting_option_values['team_price_type_closed_custom_button_url'],
					'help_text' => sprintf(
						// translators: placeholder: "Take this Team" button label.
						esc_html_x( 'Redirect the "%s" button to a specific URL.', 'placeholder: "Join Team" button label', 'ebox' ),
						ebox_get_custom_label( 'button_take_this_team' )
					),
					'default'   => '',
					'rest'      => array(
						'show_in_rest' => ebox_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'price_type_closed_custom_button_url',
								// translators: placeholder: Team.
								'description' => sprintf( esc_html_x( 'Closed %s Button URL', 'placeholder: Team', 'ebox' ), ebox_get_custom_label( 'team' ) ),
								'type'        => 'string',
								'default'     => '',
							),
						),
					),
				),
			);

			parent::load_settings_fields();
			$this->settings_sub_option_fields['team_price_type_closed_fields'] = $this->setting_option_fields;

			$this->setting_option_fields = array(
				'team_price_type' => array(
					'name'    => 'team_price_type',
					'label'   => esc_html__( 'Access Mode', 'ebox' ),
					'type'    => 'radio',
					'value'   => $this->setting_option_values['team_price_type'],
					'default' => ebox_DEFAULT_GROUP_PRICE_TYPE,
					'options' => array(
						'free'      => array(
							'label'       => esc_html__( 'Free', 'ebox' ),
							'description' => sprintf(
								// translators: placeholder: team.
								esc_html_x( 'The %s is protected. Registration and enrollment are required in order to access the content.', 'placeholder: team', 'ebox' ),
								esc_html( ebox_get_custom_label_lower( 'team' ) )
							),
						),
						'paynow'    => array(
							'label'               => esc_html__( 'Buy now', 'ebox' ),
							'description'         => sprintf(
								// translators: placeholder: course, team.
								esc_html_x( 'The %1$s is protected via the ebox built-in PayPal and/or Stripe. Users need to purchase the %2$s (one-time fee) in order to gain access.', 'placeholder: team, team', 'ebox' ),
								ebox_get_custom_label_lower( 'team' ),
								ebox_get_custom_label_lower( 'team' )
							),
							'inline_fields'       => array(
								'team_price_type_paynow' => $this->settings_sub_option_fields['team_price_type_paynow_fields'],
							),
							'inner_section_state' => ( 'paynow' === $this->setting_option_values['team_price_type'] ) ? 'open' : 'closed',
						),
						'subscribe' => array(
							'label'               => esc_html__( 'Recurring', 'ebox' ),
							'description'         => sprintf(
								// translators: placeholder: team, team.
								esc_html_x( 'The %1$s is protected via the built-in ebox PayPal/Stripe functionality. Users need to purchase the %2$s to gain access and will be charged on a recurring basis.', 'placeholder: team, team', 'ebox' ),
								ebox_get_custom_label_lower( 'team' ),
								ebox_get_custom_label_lower( 'team' )
							),
							'inline_fields'       => array(
								'team_price_type_subscribe' => $this->settings_sub_option_fields['team_price_type_subscribe_fields'],
							),
							'inner_section_state' => ( 'subscribe' === $this->setting_option_values['team_price_type'] ) ? 'open' : 'closed',
						),
						'closed'    => array(
							'label'               => esc_html__( 'Closed', 'ebox' ),
							'description'         => sprintf(
								// translators: placeholder: team, team.
								esc_html_x( 'The %1$s can only be accessed through admin enrollment (manual), %2$s enrollment, or integration (shopping cart or membership) enrollment. No enrollment button will be displayed, unless a URL is set (optional).', 'placeholder: team, team', 'ebox' ),
								ebox_get_custom_label_lower( 'team' ),
								ebox_get_custom_label_lower( 'team' )
							),
							'inline_fields'       => array(
								'team_price_type_closed' => $this->settings_sub_option_fields['team_price_type_closed_fields'],
							),
							'inner_section_state' => ( 'closed' === $this->setting_option_values['team_price_type'] ) ? 'open' : 'closed',
						),
					),
					'rest'    => array(
						'show_in_rest' => ebox_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'price_type',
								// translators: placeholder: Team.
								'description' => sprintf( esc_html_x( '%s Price Type', 'placeholder: Team', 'ebox' ), ebox_Custom_Label::get_label( 'team' ) ),
								'type'        => 'string',
								'default'     => ebox_DEFAULT_GROUP_PRICE_TYPE,
								'enum'        => array(
									'closed',
									'free',
									'paynow',
									'subscribe',
								),
							),
						),
					),
				),
			);

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
			$this->setting_option_fields = apply_filters( 'ebox_settings_fields', $this->setting_option_fields, $this->settings_metabox_key );

			parent::load_settings_fields();
		}

		/**
		 * Save Metabox Settings Field Map Post Values.
		 * This function maps the external Post keys to the
		 * internal field keys.
		 *
		 * @since 3.2.0
		 *
		 * @param array $post_values Array of post values.
		 */
		public function get_save_settings_fields_map_form_post_values( $post_values = array() ) {
			$settings_fields_map = $this->settings_fields_map;
			if ( ( isset( $post_values['team_price_type'] ) ) && ( ! empty( $post_values['team_price_type'] ) ) ) {
				if ( 'paynow' === $post_values['team_price_type'] ) {
					unset( $settings_fields_map['team_price_type_subscribe_price'] );
					unset( $settings_fields_map['team_price_type_subscribe_billing_cycle'] );
					unset( $settings_fields_map['team_price_type_subscribe_billing_recurring_times'] );
					unset( $settings_fields_map['team_price_type_closed_price'] );
					unset( $settings_fields_map['team_price_type_closed_custom_button_label'] );
					unset( $settings_fields_map['team_price_type_closed_custom_button_url'] );
					unset( $settings_fields_map['team_trial_price'] );
					unset( $settings_fields_map['team_trial_duration_t1'] );
					unset( $settings_fields_map['team_trial_duration_p1'] );
					unset( $settings_fields_map['team_price_type_subscribe_enrollment_url'] );
				} elseif ( 'subscribe' === $post_values['team_price_type'] ) {
					unset( $settings_fields_map['team_price_type_paynow_price'] );
					unset( $settings_fields_map['team_price_type_closed_price'] );
					unset( $settings_fields_map['team_price_type_closed_custom_button_label'] );
					unset( $settings_fields_map['team_price_type_closed_custom_button_url'] );
					unset( $settings_fields_map['team_price_type_paynow_enrollment_url'] );
				} elseif ( 'closed' === $post_values['team_price_type'] ) {
					unset( $settings_fields_map['team_price_type_subscribe_price'] );
					unset( $settings_fields_map['team_price_type_subscribe_billing_cycle'] );
					unset( $settings_fields_map['team_price_type_subscribe_billing_recurring_times'] );
					unset( $settings_fields_map['team_price_type_paynow_price'] );
					unset( $settings_fields_map['team_trial_price'] );
					unset( $settings_fields_map['team_trial_duration_t1'] );
					unset( $settings_fields_map['team_trial_duration_p1'] );
					unset( $settings_fields_map['team_price_type_paynow_enrollment_url'] );
					unset( $settings_fields_map['team_price_type_subscribe_enrollment_url'] );
				} else {
					unset( $settings_fields_map['team_price_type_paynow_price'] );
					unset( $settings_fields_map['team_price_type_subscribe_price'] );
					unset( $settings_fields_map['team_price_type_subscribe_billing_cycle'] );
					unset( $settings_fields_map['team_price_type_subscribe_billing_recurring_times'] );
					unset( $settings_fields_map['team_price_type_closed_price'] );
					unset( $settings_fields_map['team_price_type_closed_custom_button_label'] );
					unset( $settings_fields_map['team_price_type_closed_custom_button_url'] );
					unset( $settings_fields_map['team_trial_price'] );
					unset( $settings_fields_map['team_trial_duration_t1'] );
					unset( $settings_fields_map['team_trial_duration_p1'] );
					unset( $settings_fields_map['team_price_type_paynow_enrollment_url'] );
					unset( $settings_fields_map['team_price_type_subscribe_enrollment_url'] );
				}
			}
			return $settings_fields_map;
		}

		/**
		 * Filter settings values for metabox before save to database.
		 *
		 * @since 3.2.0
		 *
		 * @param array  $settings_values Array of settings values.
		 * @param string $settings_metabox_key Metabox key.
		 * @param string $settings_screen_id Screen ID.
		 *
		 * @return array $settings_values.
		 */
		public function filter_saved_fields( $settings_values = array(), $settings_metabox_key = '', $settings_screen_id = '' ) {
			if ( ( $settings_screen_id === $this->settings_screen_id ) && ( $settings_metabox_key === $this->settings_metabox_key ) ) {

				if ( ! isset( $settings_values['team_price_type'] ) ) {
					$settings_values['team_price_type'] = '';
				}

				if ( isset( $settings_values['team_price_billing_t3'] ) ) {
					$settings_values['team_price_billing_t3'] = '';
				}
				if ( ! isset( $settings_values['team_price_billing_p3'] ) ) {
					$settings_values['team_price_billing_p3'] = 0;
				}

				if ( ! isset( $settings_values['team_price_type_subscribe_billing_recurring_times'] ) ) {
					$settings_values['team_price_type_subscribe_billing_recurring_times'] = '';
				}

				if ( isset( $_POST['team_price_billing_t3'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
					$settings_values['team_price_billing_t3'] = strtoupper( esc_attr( $_POST['team_price_billing_t3'] ) ); // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
					$settings_values['team_price_billing_t3'] = ebox_billing_cycle_field_frequency_validate( $settings_values['team_price_billing_t3'] );
				}

				if ( isset( $_POST['team_price_billing_p3'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
					$settings_values['team_price_billing_p3'] = absint( $_POST['team_price_billing_p3'] ); // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
					$settings_values['team_price_billing_p3'] = ebox_billing_cycle_field_interval_validate( $settings_values['team_price_billing_p3'], $settings_values['team_price_billing_t3'] );
				}

				if ( ! isset( $settings_values['team_trial_price'] ) ) {
					$settings_values['team_trial_price'] = '';
				}
				if ( isset( $_POST['team_trial_duration_t1'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
					$settings_values['team_trial_duration_t1'] = strtoupper( esc_attr( $_POST['team_trial_duration_t1'] ) ); // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
					$settings_values['team_trial_duration_t1'] = ebox_billing_cycle_field_frequency_validate( $settings_values['team_trial_duration_t1'] );
				}

				if ( isset( $_POST['team_trial_duration_p1'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
					$settings_values['team_trial_duration_p1'] = absint( $_POST['team_trial_duration_p1'] ); // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					$settings_values['team_trial_duration_p1'] = ebox_billing_cycle_field_interval_validate( $settings_values['team_trial_duration_p1'], $settings_values['team_trial_duration_t1'] );
				}

				if ( 'paynow' === $settings_values['team_price_type'] ) {
					$settings_values['custom_button_url']                                  = '';
					$settings_values['team_price_type_subscribe_billing_recurring_times'] = '';
					$settings_values['team_price_billing_p3']                             = '';
					$settings_values['team_price_billing_t3']                             = '';
					$settings_values['team_trial_price']                                  = '';
					$settings_values['team_trial_duration_t1']                            = '';
					$settings_values['team_trial_duration_p1']                            = '';
				} elseif ( 'subscribe' === $settings_values['team_price_type'] ) {
					$settings_values['custom_button_url'] = '';
				} elseif ( 'closed' === $settings_values['team_price_type'] ) {
					$settings_values['team_price_billing_p3']                             = '';
					$settings_values['team_price_billing_t3']                             = '';
					$settings_values['team_price_type_subscribe_billing_recurring_times'] = '';
					$settings_values['team_trial_price']                                  = '';
					$settings_values['team_trial_duration_t1']                            = '';
					$settings_values['team_trial_duration_p1']                            = '';
				} else {
					$settings_values['team_price']                                        = '';
					$settings_values['custom_button_url']                                  = '';
					$settings_values['team_price_type_subscribe_billing_recurring_times'] = '';
					$settings_values['team_price_billing_p3']                             = '';
					$settings_values['team_price_billing_t3']                             = '';
					$settings_values['team_trial_price']                                  = '';
					$settings_values['team_trial_duration_t1']                            = '';
					$settings_values['team_trial_duration_p1']                            = '';
				}

				/**
				 * Check the URL submitted for any leading/trailing spaces and remove them
				 */
				if ( ( isset( $settings_values['custom_button_url'] ) ) && ! empty( $settings_values['custom_button_url'] ) ) {
					$settings_values['custom_button_url'] = trim( urldecode( $settings_values['custom_button_url'] ) );
				}

				/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
				$settings_values = apply_filters( 'ebox_settings_save_values', $settings_values, $this->settings_metabox_key );
			}

			return $settings_values;
		}

		// End of functions.
	}

	add_filter(
		'ebox_post_settings_metaboxes_init_' . ebox_get_post_type_slug( 'team' ),
		function( $metaboxes = array() ) {
			if ( ( ! isset( $metaboxes['ebox_Settings_Metabox_Team_Access_Settings'] ) ) && ( class_exists( 'ebox_Settings_Metabox_Team_Access_Settings' ) ) ) {
				$metaboxes['ebox_Settings_Metabox_Team_Access_Settings'] = ebox_Settings_Metabox_Team_Access_Settings::add_metabox_instance();
			}

			return $metaboxes;
		},
		50,
		1
	);
}
