<?php
/**
 * ebox Settings Section for Teams Taxonomies Metabox.
 *
 * @since 3.2.0
 * @package ebox\Settings\Sections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Section' ) ) && ( ! class_exists( 'ebox_Settings_Teams_Taxonomies' ) ) ) {
	/**
	 * Class ebox Settings Section for Teams Taxonomies Metabox.
	 *
	 * @since 3.2.0
	 */
	class ebox_Settings_Teams_Taxonomies extends ebox_Settings_Section {

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
			$this->setting_option_key = 'ebox_settings_teams_taxonomies';

			// This is the HTML form field prefix used.
			$this->setting_field_prefix = 'ebox_settings_teams_taxonomies';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_section_key = 'taxonomies';

			// Section label/header.
			$this->settings_section_label = sprintf(
				// translators: placeholder: Team.
				esc_html_x( '%s Taxonomies', 'placeholder: Team', 'ebox' ),
				ebox_get_custom_label( 'team' )
			);

			// Used to show the section description above the fields. Can be empty.
			$this->settings_section_description = sprintf(
				// translators: placeholder: teams.
				esc_html_x( 'Control which taxonomies can be used to better organize your ebox %s.', 'placeholder: team', 'ebox' ),
				ebox_get_custom_label_lower( 'team' )
			);

			parent::__construct();
		}

		/**
		 * Initialize the metabox settings values.
		 *
		 * @since 3.2.0
		 */
		public function load_settings_values() {
			parent::load_settings_values();

			$_init = false;
			if ( false === $this->setting_option_values ) {
				$__init                      = true;
				$this->setting_option_values = array(
					'ld_team_category' => 'yes',
					'ld_team_tag'      => 'yes',
					'wp_post_category'  => 'yes',
					'wp_post_tag'       => 'yes',
				);
			}

			$this->setting_option_values = wp_parse_args(
				$this->setting_option_values,
				array(
					'ld_team_category' => '',
					'ld_team_tag'      => '',
					'wp_post_category'  => '',
					'wp_post_tag'       => '',
				)
			);
		}

		/**
		 * Initialize the metabox settings fields.
		 *
		 * @since 3.2.0
		 */
		public function load_settings_fields() {

			$this->setting_option_fields = array(
				'ld_team_category' => array(
					'name'    => 'ld_team_category',
					'type'    => 'checkbox-switch',
					'label'   => sprintf(
						// translators: placeholder: Team.
						esc_html_x( '%s Categories', 'placeholder: Team', 'ebox' ),
						ebox_get_custom_label( 'team' )
					),
					'value'   => $this->setting_option_values['ld_team_category'],
					'options' => array(
						''    => '',
						'yes' => sprintf(
							// translators: placeholder: Team.
							esc_html_x( 'Manage %s Categories via the Actions dropdown', 'placeholder: Team', 'ebox' ),
							ebox_get_custom_label( 'team' )
						),
					),
				),
				'ld_team_tag'      => array(
					'name'    => 'ld_team_tag',
					'type'    => 'checkbox-switch',
					'label'   => sprintf(
						// translators: placeholder: Team.
						esc_html_x( '%s Tags', 'placeholder: Team', 'ebox' ),
						ebox_Custom_Label::get_label( 'team' )
					),
					'value'   => $this->setting_option_values['ld_team_tag'],
					'options' => array(
						''    => '',
						'yes' => sprintf(
							// translators: placeholder: Team.
							esc_html_x( 'Manage %s Tags via the Actions dropdown', 'placeholder: Team', 'ebox' ),
							ebox_get_custom_label( 'team' )
						),
					),
				),
				'wp_post_category'  => array(
					'name'    => 'wp_post_category',
					'type'    => 'checkbox-switch',
					'label'   => esc_html__( 'WP Post Categories', 'ebox' ),
					'value'   => $this->setting_option_values['wp_post_category'],
					'options' => array(
						''    => '',
						'yes' => esc_html__( 'Manage WP Categories via the Actions dropdown', 'ebox' ),
					),
				),
				'wp_post_tag'       => array(
					'name'    => 'wp_post_tag',
					'type'    => 'checkbox-switch',
					'label'   => esc_html__( 'WP Post Tags', 'ebox' ),
					'value'   => $this->setting_option_values['wp_post_tag'],
					'options' => array(
						''    => '',
						'yes' => esc_html__( 'Manage WP Tags via the Actions dropdown', 'ebox' ),
					),
				),

			);

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
			$this->setting_option_fields = apply_filters( 'ebox_settings_fields', $this->setting_option_fields, $this->settings_section_key );

			parent::load_settings_fields();
		}
	}
}
add_action(
	'ebox_settings_sections_init',
	function() {
		ebox_Settings_Teams_Taxonomies::add_section_instance();
	}
);
