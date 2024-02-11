<?php
/**
 * ebox Settings Section for Teams Management and Display Settings Metabox.
 *
 * @since 3.2.0
 * @package ebox\Settings\Sections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Section' ) ) && ( ! class_exists( 'ebox_Settings_Teams_Management_Display' ) ) ) {
	/**
	 * Class ebox Settings Section for Teams Management and Display Settings Metabox.
	 *
	 * @since 3.2.0
	 */
	class ebox_Settings_Teams_Management_Display extends ebox_Settings_Section {

		/**
		 * Protected constructor for class
		 *
		 * @since 3.2.0
		 */
		protected function __construct() {

			// What screen ID are we showing on.
			$this->settings_screen_id = 'teams_page_teams-options';

			// The page ID (different than the screen ID).
			$this->settings_page_id = 'teams-options';

			// This is the 'option_name' key used in the wp_options table.
			$this->setting_option_key = 'ebox_settings_teams_management_display';

			// This is the HTML form field prefix used.
			$this->setting_field_prefix = 'ebox_settings_teams_management_display';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_section_key = 'teams_management_display';

			// Section label/header.
			$this->settings_section_label = sprintf(
				// translators: placeholder: Team.
				esc_html_x( 'Global %s Management & Display Settings', 'placeholder: Team', 'ebox' ),
				ebox_Custom_Label::get_label( 'team' )
			);

			// Used to show the section description above the fields. Can be empty.
			$this->settings_section_description = sprintf(
				// translators: placeholder: team.
				esc_html_x( 'Control settings for %s creation, and visual organization', 'placeholder: team', 'ebox' ),
				ebox_get_custom_label_lower( 'team' )
			);

			// Define the deprecated Class and Fields.
			$this->settings_deprecated = array();

			parent::__construct();
		}

		/**
		 * Initialize the metabox settings values.
		 *
		 * @since 3.2.0
		 */
		public function load_settings_values() {
			parent::load_settings_values();

			// If the settings set as a whole is empty then we set a default.
			if ( ( false === $this->setting_option_values ) || ( '' === $this->setting_option_values ) ) {
				if ( '' === $this->setting_option_values ) {
					$this->setting_option_values = array();
				}
				$this->transition_deprecated_settings();
			}

			if ( '' === $this->setting_option_values ) {
				$this->setting_option_values = array();
			}

			if ( ! isset( $this->setting_option_values['team_hierarchical_enabled'] ) ) {
				$this->setting_option_values['team_hierarchical_enabled'] = '';
			}

			if ( ! isset( $this->setting_option_values['team_pagination_courses'] ) ) {
				$this->setting_option_values['team_pagination_courses'] = ebox_LMS_DEFAULT_WIDGET_PER_PAGE;
			}

			if ( ( ebox_LMS_DEFAULT_WIDGET_PER_PAGE === $this->setting_option_values['team_pagination_courses'] ) && ( ebox_LMS_DEFAULT_WIDGET_PER_PAGE === $this->setting_option_values['team_pagination_courses'] ) ) {
				$this->setting_option_values['team_pagination_enabled'] = '';
			} else {
				$this->setting_option_values['team_pagination_enabled'] = 'yes';
			}

			if ( ! isset( $this->setting_option_values['team_courses_order'] ) ) {
				$this->setting_option_values['team_courses_order'] = ebox_DEFAULT_GROUP_ORDER;
			}
			if ( ! isset( $this->setting_option_values['team_courses_orderby'] ) ) {
				$this->setting_option_values['team_courses_orderby'] = ebox_DEFAULT_GROUP_ORDERBY;
			}

			if ( ( ebox_DEFAULT_GROUP_ORDERBY === $this->setting_option_values['team_courses_orderby'] ) && ( ebox_DEFAULT_GROUP_ORDER === $this->setting_option_values['team_courses_order'] ) ) {
				$this->setting_option_values['team_courses_order_enabled'] = '';
			} else {
				$this->setting_option_values['team_courses_order_enabled'] = 'yes';
			}
		}

		/**
		 * Initialize the metabox settings fields.
		 *
		 * @since 3.2.0
		 */
		public function load_settings_fields() {

			$team_courses_orderby_options = array(
				'menu_order' => esc_html__( 'Menu Order', 'ebox' ),
				'date'       => esc_html__( 'Date', 'ebox' ),
				'title'      => esc_html__( 'Title', 'ebox' ),
			);

			if ( isset( $team_courses_orderby_options[ ebox_DEFAULT_GROUP_ORDERBY ] ) ) {
				$team_courses_orderby_default = esc_attr( $team_courses_orderby_options[ ebox_DEFAULT_GROUP_ORDERBY ] );
			} else {
				$team_courses_orderby_default = $team_courses_orderby_options['date'];
			}

			$team_courses_order_options = array(
				'ASC'  => esc_html__( 'Ascending', 'ebox' ),
				'DESC' => esc_html__( 'Descending', 'ebox' ),
			);
			if ( isset( $team_courses_order_options[ ebox_DEFAULT_GROUP_ORDER ] ) ) {
				$team_courses_order_default = esc_attr( $team_courses_order_options[ ebox_DEFAULT_GROUP_ORDER ] );
			} else {
				$team_courses_order_default = $team_courses_order_options['ASC'];
			}

			$this->setting_option_fields = array();

			$this->setting_option_fields['team_hierarchical_enabled'] = array(
				'name'      => 'team_hierarchical_enabled',
				'type'      => 'checkbox-switch',
				'label'     => sprintf(
					// translators: placeholder: Team.
					esc_html_x( '%s Hierarchy', 'placeholder: Team', 'ebox' ),
					ebox_get_custom_label( 'team' )
				),
				'help_text' => sprintf(
					// translators: placeholder: Team, Teams.
					esc_html_x( 'A %1$s can be nested within other %2$s.', 'placeholder: Team, Teams', 'ebox' ),
					ebox_get_custom_label_lower( 'team' ),
					ebox_get_custom_label_lower( 'teams' )
				),
				'value'     => $this->setting_option_values['team_hierarchical_enabled'],
				'options'   => array(
					''    => '',
					'yes' => '',
				),
			);

			$this->setting_option_fields['team_pagination_enabled']    = array(
				'name'                => 'team_pagination_enabled',
				'type'                => 'checkbox-switch',
				'label'               => sprintf(
					// translators: placeholder: Team.
					esc_html_x( '%s Table Pagination', 'placeholder: Team', 'ebox' ),
					ebox_get_custom_label( 'team' )
				),
				'help_text'           => sprintf(
					// translators: placeholder: team.
					esc_html_x( 'Customize the pagination options for ALL %s content tables.', 'placeholder: course, course', 'ebox' ),
					ebox_get_custom_label_lower( 'team' )
				),
				'value'               => $this->setting_option_values['team_pagination_enabled'],
				'options'             => array(
					''    => sprintf(
						// translators: placeholder: default per page number.
						esc_html_x( 'Currently showing default pagination %d', 'placeholder: default per page number', 'ebox' ),
						ebox_LMS_DEFAULT_WIDGET_PER_PAGE
					),
					'yes' => '',
				),
				'child_section_state' => ( 'yes' === $this->setting_option_values['team_pagination_enabled'] ) ? 'open' : 'closed',
			);
			$this->setting_option_fields['team_pagination_courses']    = array(
				'name'           => 'team_pagination_courses',
				'type'           => 'number',
				'label'          => ebox_get_custom_label( 'courses' ),
				'value'          => $this->setting_option_values['team_pagination_courses'],
				'class'          => 'small-text',
				'input_label'    => esc_html__( 'per page', 'ebox' ),
				'attrs'          => array(
					'step' => 1,
					'min'  => 0,
				),
				'parent_setting' => 'team_pagination_enabled',
			);
			$this->setting_option_fields['team_courses_order_enabled'] = array(
				'name'                => 'team_courses_order_enabled',
				'type'                => 'checkbox-switch',
				'label'               => sprintf(
					// translators: placeholder: Team, Courses.
					esc_html_x( '%1$s %2$s Order', 'placeholder: Team, Courses', 'ebox' ),
					ebox_get_custom_label( 'team' ),
					ebox_get_custom_label( 'courses' )
				),
				'help_text'           => sprintf(
					// translators: placeholder: courses.
					esc_html_x( 'Customize the display order of %s.', 'placeholder: courses', 'ebox' ),
					ebox_get_custom_label_lower( 'course' )
				),
				'value'               => $this->setting_option_values['team_courses_order_enabled'],
				'options'             => array(
					''    => array(
						'label'       => sprintf(
							// translators: placeholder: Default Order By, Order.
							esc_html_x( 'Using default sorting by %1$s in %2$s order', 'placeholder: Default Order By, Order', 'ebox' ),
							'<em>' . $team_courses_orderby_default . '</em>',
							'<em>' . $team_courses_order_default . '</em>'
						),
						'description' => '',
					),
					'yes' => array(
						'label'       => '',
						'description' => '',
					),
				),
				'child_section_state' => ( 'yes' === $this->setting_option_values['team_courses_order_enabled'] ) ? 'open' : 'closed',
			);
			$this->setting_option_fields['team_courses_orderby']       = array(
				'name'           => 'team_courses_orderby',
				'type'           => 'select',
				'label'          => esc_html__( 'Sort By', 'ebox' ),
				'value'          => $this->setting_option_values['team_courses_orderby'],
				'default'        => ebox_DEFAULT_GROUP_ORDERBY,
				'options'        => $team_courses_orderby_options,
				'parent_setting' => 'team_courses_order_enabled',
			);

			$this->setting_option_fields['team_courses_order'] = array(
				'name'           => 'team_courses_order',
				'type'           => 'select',
				'label'          => esc_html__( 'Order Direction', 'ebox' ),
				'value'          => $this->setting_option_values['team_courses_order'],
				'default'        => ebox_DEFAULT_GROUP_ORDER,
				'options'        => $team_courses_order_options,
				'parent_setting' => 'team_courses_order_enabled',
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
		 * @param array  $current_values Array of section fields values.
		 * @param array  $old_values     Array of old values.
		 * @param string $option         Section option key should match $this->setting_option_key.
		 */
		public function section_pre_update_option( $current_values = '', $old_values = '', $option = '' ) {
			if ( $option === $this->setting_option_key ) {
				$current_values = parent::section_pre_update_option( $current_values, $old_values, $option );
				if ( $current_values !== $old_values ) {

					if ( ( isset( $current_values['team_pagination_enabled'] ) ) && ( 'yes' === $current_values['team_pagination_enabled'] ) ) {
						$current_values['team_pagination_courses'] = absint( $current_values['team_pagination_courses'] );

						if ( ebox_LMS_DEFAULT_WIDGET_PER_PAGE === $current_values['team_pagination_courses'] ) {
							$current_values['team_pagination_courses'] = '';
						}
					} else {
						$current_values['team_pagination_courses'] = ebox_LMS_DEFAULT_WIDGET_PER_PAGE;
					}

					// Team Courses Order and Order By.
					if ( ( isset( $current_values['team_courses_order_enabled'] ) ) && ( 'yes' === $current_values['team_courses_order_enabled'] ) ) {
						if ( ( ! isset( $current_values['team_courses_order'] ) ) || ( empty( $current_values['team_courses_order'] ) ) ) {
							$current_values['team_courses_order'] = ebox_DEFAULT_GROUP_ORDER;
						}
						if ( ( ! isset( $current_values['team_courses_orderby'] ) ) || ( empty( $current_values['team_courses_orderby'] ) ) ) {
							$current_values['team_courses_orderby'] = ebox_DEFAULT_GROUP_ORDERBY;
						}

						if ( ( ebox_DEFAULT_GROUP_ORDER === $current_values['team_courses_order'] ) && ( ebox_DEFAULT_GROUP_ORDERBY === $current_values['team_courses_orderby'] ) ) {
							$current_values['team_courses_order_enabled'] = '';
						}
					} else {
						$current_values['team_courses_order']   = ebox_DEFAULT_GROUP_ORDER;
						$current_values['team_courses_orderby'] = ebox_DEFAULT_GROUP_ORDERBY;
					}
				}
			}

			return $current_values;
		}
	}
}
add_action(
	'ebox_settings_sections_init',
	function() {
		ebox_Settings_Teams_Management_Display::add_section_instance();
	}
);
