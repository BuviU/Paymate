<?php
/**
 * ebox Settings Metabox for Coupon Settings.
 *
 * @since 4.1.0
 *
 * @package ebox\Settings\Metaboxes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (
	class_exists( 'ebox_Settings_Metabox' ) &&
	! class_exists( 'ebox_Settings_Metabox_Coupon_Settings' )
) {
	/**
	 * Class ebox Settings Metabox for Coupon Settings.
	 *
	 * @since 4.1.0
	 */
	class ebox_Settings_Metabox_Coupon_Settings extends ebox_Settings_Metabox {
		/**
		 * Public constructor for class.
		 *
		 * @since 4.1.0
		 */
		public function __construct() {
			// What screen ID are we showing on.
			$this->settings_screen_id = LDLMS_Post_Types::get_post_type_slug(
				LDLMS_Post_Types::COUPON
			);

			// Used within the Settings API to uniquely identify this section.
			$this->settings_metabox_key = 'ebox_coupon_settings';

			// Section label/header.
			$this->settings_section_label = esc_html__( 'Settings', 'ebox' );

			$this->settings_section_description = '';

			add_filter(
				'ebox_metabox_save_fields_' . $this->settings_metabox_key,
				array( $this, 'filter_saved_fields' ),
				30,
				3
			);

			// Map internal settings field ID to legacy field ID.
			$this->settings_fields_map = array(
				'code'                 => 'code',
				'type'                 => 'type',
				'amount'               => 'amount',
				'max_redemptions'      => 'max_redemptions',
				'start_date'           => 'start_date',
				'end_date'             => 'end_date',
				'apply_to_all_courses' => 'apply_to_all_courses',
				'courses'              => 'courses',
				'apply_to_all_teams'  => 'apply_to_all_teams',
				'teams'               => 'teams',
			);

			parent::__construct();
		}

		/**
		 * Initialize the metabox settings values.
		 *
		 * @since 4.1.0
		 */
		public function load_settings_values() {
			parent::load_settings_values();

			// Process courses & teams.

			foreach ( ebox_COUPON_ASSOCIATED_FIELDS as $field ) {
				if (
					isset( $this->setting_option_values[ $field ] ) &&
					is_array( $this->setting_option_values[ $field ] )
				) {
					$this->setting_option_values[ $field ] = array_map(
						'intval',
						$this->setting_option_values[ $field ]
					);
				}
			}

			// Set default values.

			$default_values = array(
				'code'                 => $this->generate_unique_coupon_code(),
				'max_redemptions'      => 0,
				'apply_to_all_courses' => 'on',
				'apply_to_all_teams'  => 'on',
			);

			foreach ( $default_values as $option => $default_value ) {
				if ( ! isset( $this->setting_option_values[ $option ] ) ) {
					$this->setting_option_values[ $option ] = $default_value;
				}
			}

			// Ensure all settings fields are present.

			foreach ( $this->settings_fields_map as $_internal => $_external ) {
				if ( ! isset( $this->setting_option_values[ $_internal ] ) ) {
					$this->setting_option_values[ $_internal ] = '';
				}
			}
		}

		/**
		 * Initialize the metabox settings fields.
		 *
		 * @since 4.1.0
		 */
		public function load_settings_fields() {
			list(
				$select_course_options,
				$select_course_query_data_json,
				$select_course_options_default
			) = $this->map_courses_field_data();

			list(
				$select_team_options,
				$select_team_query_data_json,
				$select_team_options_default
			) = $this->map_teams_field_data();

			$this->setting_option_fields = array(
				'code'                 => array(
					'name'     => 'code',
					'label'    => esc_html__( 'Coupon Code', 'ebox' ),
					'type'     => 'text',
					'value'    => $this->setting_option_values['code'],
					'required' => true,
					'class'    => '-medium',
				),
				'type'                 => array(
					'name'    => 'type',
					'label'   => esc_html__( 'Type', 'ebox' ),
					'type'    => 'radio',
					'value'   => $this->setting_option_values['type'],
					'default' => ebox_COUPON_TYPE_FLAT,
					'options' => array(
						ebox_COUPON_TYPE_FLAT       => array(
							'label' => esc_html__( 'Flat Rate', 'ebox' ),
						),
						ebox_COUPON_TYPE_PERCENTAGE => array(
							'label' => esc_html__( 'Percentage Off', 'ebox' ),
						),
					),
				),
				'amount'               => array(
					'name'      => 'amount',
					'label'     => esc_html__( 'Amount', 'ebox' ),
					'type'      => 'number',
					'value'     => $this->setting_option_values['amount'],
					'help_text' => esc_html__( 'Numerical number of the flat rate or percentage off to provide.', 'ebox' ),
					'required'  => true,
					'class'     => '-small',
					'attrs'     => array(
						'step'        => 'any',
						'min'         => 0,
						'can_decimal' => 2,
						'can_empty'   => false,
					),
				),
				'max_redemptions'      => array(
					'name'      => 'max_redemptions',
					'label'     => esc_html__( 'Number of Redemptions', 'ebox' ),
					'type'      => 'number',
					'value'     => $this->setting_option_values['max_redemptions'],
					'help_text' => esc_html__( 'How many times can the coupon be redeemed (0 for unlimited).', 'ebox' ),
					'required'  => true,
					'class'     => '-small',
					'attrs'     => array(
						'step'        => 1,
						'min'         => 0,
						'can_decimal' => false,
						'can_empty'   => false,
					),
				),
				'start_date'           => array(
					'name'      => 'start_date',
					'label'     => esc_html__( 'Start Date', 'ebox' ),
					'value'     => $this->setting_option_values['start_date'],
					'type'      => 'date-entry',
					'class'     => 'ebox-datepicker-field',
					'help_text' => esc_html__( 'When is the coupon valid from?', 'ebox' ),
					'rest'      => array(
						'show_in_rest' => false,
						'rest_args'    => array(),
					),
				),
				'end_date'             => array(
					'name'      => 'end_date',
					'label'     => esc_html__( 'End Date', 'ebox' ),
					'value'     => $this->setting_option_values['end_date'],
					'type'      => 'date-entry',
					'class'     => 'ebox-datepicker-field',
					'help_text' => esc_html__( 'When does the coupon expire?', 'ebox' ),
					'rest'      => array(
						'show_in_rest' => false,
						'rest_args'    => array(),
					),
				),
				'apply_to_all_courses' => array(
					'name'                => 'apply_to_all_courses',
					'label'               => sprintf(
						// Translators: placeholder: courses.
						esc_html_x( 'Apply to all %s', 'placeholder: courses', 'ebox' ),
						ebox_get_custom_label_lower( 'courses' )
					),
					'type'                => 'checkbox-switch',
					'value'               => $this->setting_option_values['apply_to_all_courses'],
					'options'             => array( 'on' => '' ),
					'attrs'               => array(
						'data-inverted' => true,
					),
					'child_section_state' => 'on' === $this->setting_option_values['apply_to_all_courses'] ? 'closed' : 'open',
					'rest'                => array(
						'show_in_rest' => false,
						'rest_args'    => array(),
					),
				),
				'courses'              => array(
					'name'           => 'courses',
					'label'          => sprintf(
						// Translators: placeholder: Courses.
						esc_html_x( 'Associated %s', 'placeholder: Courses', 'ebox' ),
						ebox_get_custom_label( 'courses' )
					),
					'type'           => 'multiselect',
					'default'        => '',
					'value'          => $this->setting_option_values['courses'],
					'value_type'     => 'intval',
					'lazy_load'      => true,
					'options'        => $select_course_options,
					'placeholder'    => $select_course_options_default,
					'attrs'          => array(
						'data-ld_selector_nonce'   => wp_create_nonce( ebox_get_post_type_slug( 'course' ) ),
						'data-ld_selector_default' => '1',
						'data-select2-query-data'  => $select_course_query_data_json,
					),
					'help_text'      => sprintf(
						// Translators: placeholder: courses.
						esc_html_x( 'Select specific %s the coupon can be used for.', 'placeholder: courses', 'ebox' ),
						ebox_get_custom_label_lower( 'courses' )
					),
					'parent_setting' => 'apply_to_all_courses',
					'rest'           => array(
						'show_in_rest' => false,
						'rest_args'    => array(),
					),
				),
				'apply_to_all_teams'  => array(
					'name'                => 'apply_to_all_teams',
					'label'               => sprintf(
						// Translators: placeholder: teams.
						esc_html_x( 'Apply to all %s', 'placeholder: teams', 'ebox' ),
						ebox_get_custom_label_lower( 'teams' )
					),
					'type'                => 'checkbox-switch',
					'value'               => $this->setting_option_values['apply_to_all_teams'],
					'options'             => array( 'on' => '' ),
					'attrs'               => array(
						'data-inverted' => true,
					),
					'child_section_state' => 'on' === $this->setting_option_values['apply_to_all_teams'] ? 'closed' : 'open',
					'rest'                => array(
						'show_in_rest' => false,
						'rest_args'    => array(),
					),
				),
				'teams'               => array(
					'name'           => 'teams',
					'label'          => sprintf(
						// Translators: placeholder: Teams.
						esc_html_x( 'Associated %s', 'placeholder: Teams', 'ebox' ),
						ebox_get_custom_label( 'teams' )
					),
					'type'           => 'multiselect',
					'value'          => $this->setting_option_values['teams'],
					'value_type'     => 'intval',
					'lazy_load'      => true,
					'options'        => $select_team_options,
					'placeholder'    => $select_team_options_default,
					'attrs'          => array(
						'data-ld_selector_nonce'   => wp_create_nonce( ebox_get_post_type_slug( 'team' ) ),
						'data-ld_selector_default' => '1',
						'data-select2-query-data'  => $select_team_query_data_json,
					),
					'help_text'      => sprintf(
						// Translators: placeholder: teams.
						esc_html_x( 'Select specific %s the coupon can be used for.', 'placeholder: teams', 'ebox' ),
						ebox_get_custom_label_lower( 'teams' )
					),
					'parent_setting' => 'apply_to_all_teams',
					'rest'           => array(
						'show_in_rest' => false,
						'rest_args'    => array(),
					),
				),
			);

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
			$this->setting_option_fields = apply_filters( 'ebox_settings_fields', $this->setting_option_fields, $this->settings_metabox_key );

			parent::load_settings_fields();
		}

		/**
		 * Filter settings values for metabox before save to database.
		 *
		 * @since 4.1.0
		 *
		 * @param array  $settings_values Array of settings values.
		 * @param string $settings_metabox_key Metabox key.
		 * @param string $settings_screen_id Screen ID.
		 *
		 * @return array $settings_values.
		 */
		public function filter_saved_fields(
			array $settings_values = array(),
			string $settings_metabox_key = '',
			string $settings_screen_id = ''
		): array {
			if ( $settings_screen_id !== $this->settings_screen_id || $settings_metabox_key !== $this->settings_metabox_key ) {
				return $settings_values;
			}

			// Check if the coupon code is unique and
			// generate the new one unless it is unique.

			global $wpdb;

			// phpcs:ignore
			$existing_coupon_codes = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s AND post_id != %d",
					ebox_COUPON_META_KEY_CODE,
					$this->_post->ID
				)
			);

			do {
				$coupon_is_not_unique = in_array(
					$settings_values[ ebox_COUPON_META_KEY_CODE ],
					$existing_coupon_codes,
					true
				);

				if ( $coupon_is_not_unique ) {
					$settings_values[ ebox_COUPON_META_KEY_CODE ] = $this->generate_unique_coupon_code();
				}
			} while ( $coupon_is_not_unique );

			// Duplicate code to the additional meta to be able to check a coupon code uniqueness easier.
			ebox_update_setting( $this->_post, ebox_COUPON_META_KEY_CODE, $settings_values[ ebox_COUPON_META_KEY_CODE ] );

			// Duplicate type to the additional meta to be able to filter.
			ebox_update_setting( $this->_post, ebox_COUPON_META_KEY_TYPE, $settings_values[ ebox_COUPON_META_KEY_TYPE ] );

			// Duplicate dates to the additional metas to be able to filter.
			ebox_update_setting( $this->_post, ebox_COUPON_META_KEY_START_DATE, $settings_values[ ebox_COUPON_META_KEY_START_DATE ] );
			ebox_update_setting( $this->_post, ebox_COUPON_META_KEY_END_DATE, $settings_values[ ebox_COUPON_META_KEY_END_DATE ] );

			// Set redemptions to 0 by default.
			if ( ! metadata_exists( 'post', $this->_post->ID, ebox_COUPON_META_KEY_REDEMPTIONS ) ) {
				ebox_update_setting( $this->_post, ebox_COUPON_META_KEY_REDEMPTIONS, 0 );
			}

			// Process courses & teams fields along with duplicating apply_to_all metas.

			foreach ( ebox_COUPON_ASSOCIATED_FIELDS as $field ) {
				$apply_to_all_value = $settings_values[ ebox_COUPON_META_KEY_PREFIX_APPLY_TO_ALL . $field ];

				// Duplicate apply to all fields.
				ebox_update_setting(
					$this->_post,
					ebox_COUPON_META_KEY_PREFIX_APPLY_TO_ALL . $field,
					$apply_to_all_value
				);

				if ( empty( $settings_values[ $field ] ) || 'on' === $apply_to_all_value ) {
					$settings_values[ $field ] = array();
				}

				ebox_sync_coupon_associated_metas( $this->_post->ID, $field, $settings_values[ $field ] );
			}

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
			return apply_filters(
				'ebox_settings_save_values',
				$settings_values,
				$this->settings_metabox_key
			);
		}

		/**
		 * Generates a coupon code.
		 *
		 * @return string
		 */
		protected function generate_unique_coupon_code(): string {
			return substr(
				md5( uniqid( wp_rand(), true ) ),
				0,
				12
			);
		}

		/**
		 * Maps data for the courses field.
		 *
		 * @since 4.1.0
		 *
		 * @return array
		 */
		protected function map_courses_field_data(): array {
			global $ebox_lms;

			return $this->map_multiselect_field_data(
				'courses',
				'course',
				$ebox_lms->select_a_course(),
				sprintf(
					// Translators: placeholder: courses.
					esc_html_x( 'Search or select %s…', 'placeholder: courses', 'ebox' ),
					ebox_get_custom_label_lower( 'courses' )
				)
			);
		}

		/**
		 * Maps data for the courses field.
		 *
		 * @since 4.1.0
		 *
		 * @return array
		 */
		protected function map_teams_field_data(): array {
			global $ebox_lms;

			return $this->map_multiselect_field_data(
				'teams',
				'team',
				$ebox_lms->select_a_team(),
				sprintf(
					// Translators: placeholder: teams.
					esc_html_x( 'Search or select %s…', 'placeholder: teams', 'ebox' ),
					ebox_get_custom_label_lower( 'teams' )
				)
			);
		}

		/**
		 * Maps data for the multiselect field.
		 *
		 * @since 4.1.0
		 *
		 * @param string $field Field jey.
		 * @param string $post_type_key Post type key: course|team.
		 * @param array  $available_select_options Available items from the database.
		 * @param string $default_label Default select option label.
		 *
		 * @return array
		 */
		protected function map_multiselect_field_data(
			string $field,
			string $post_type_key,
			array $available_select_options,
			string $default_label
		): array {
			$selected_options = array();

			if ( ! empty( $this->setting_option_values[ $field ] ) ) {
				foreach ( $this->setting_option_values[ $field ] as $id ) {
					$post = get_post( $id );

					if ( is_null( $post ) ) {
						continue;
					}

					$selected_options[ $post->ID ] = get_the_title( $post->ID );
				}
			}

			if ( ! ebox_use_select2_lib() ) {
				return array(
					$selected_options + $available_select_options,
					'',
					'',
				);
			}

			$select_default_options = array(
				'-1' => $default_label,
			);

			if ( ebox_use_select2_lib_ajax_fetch() ) {
				$query_data_json = $this->build_settings_select2_lib_ajax_fetch_json(
					array(
						'query_args'       => array(
							'post_type' => ebox_get_post_type_slug( $post_type_key ),
						),
						'settings_element' => array(
							'settings_parent_class' => get_parent_class( __CLASS__ ),
							'settings_class'        => __CLASS__,
							'settings_field'        => $post_type_key,
						),
					)
				);
			} else {
				$query_data_json = '';
			}

			$select_options = $select_default_options + $available_select_options;

			return array(
				$select_options,
				$query_data_json,
				$select_default_options,
			);
		}
	}

	add_filter(
		'ebox_post_settings_metaboxes_init_' . ebox_get_post_type_slug( LDLMS_Post_Types::COUPON ),
		function ( $metaboxes = array() ) {
			if (
				! isset( $metaboxes['ebox_Settings_Metabox_Coupon_Settings'] ) &&
				class_exists( 'ebox_Settings_Metabox_Coupon_Settings' )
			) {
				$metaboxes['ebox_Settings_Metabox_Coupon_Settings'] = ebox_Settings_Metabox_Coupon_Settings::add_metabox_instance();
			}

			return $metaboxes;
		},
		50,
		1
	);
}
