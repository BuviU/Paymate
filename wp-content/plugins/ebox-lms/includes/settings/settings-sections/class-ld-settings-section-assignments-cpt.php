<?php
/**
 * ebox Settings Section for Assignments Custom Post Type Metabox.
 *
 * @since 2.6.0
 * @package ebox\Settings\Sections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Section' ) ) && ( ! class_exists( 'ebox_Settings_Assignments_CPT' ) ) ) {
	/**
	 * Class ebox Settings Section for Assignments Custom Post Type Metabox.
	 *
	 * @since 2.6.0
	 */
	class ebox_Settings_Assignments_CPT extends ebox_Settings_Section {

		/**
		 * Protected constructor for class
		 *
		 * @since 2.6.0
		 */
		protected function __construct() {

			// What screen ID are we showing on.
			$this->settings_screen_id = 'ebox-assignment_page_assignments-options';

			// The page ID (different than the screen ID).
			$this->settings_page_id = 'assignments-options';

			// This is the 'option_name' key used in the wp_options table.
			$this->setting_option_key = 'ebox_settings_assignments_cpt';

			// This is the HTML form field prefix used.
			$this->setting_field_prefix = 'ebox_settings_assignments_cpt';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_section_key = 'cpt_options';

			// Section label/header.
			$this->settings_section_label = esc_html__( 'Assignment Custom Post Type Options', 'ebox' );

			// Used to show the section description above the fields. Can be empty.
			$this->settings_section_description = esc_html__( 'Control the ebox Assignment Custom Post Type Options.', 'ebox' );

			parent::__construct();
		}

		/**
		 * Initialize the metabox settings values.
		 *
		 * @since 2.6.0
		 */
		public function load_settings_values() {
			parent::load_settings_values();

			if ( false === $this->setting_option_values ) {
				$this->setting_option_values = array(
					'exclude_from_search' => 'yes',
					'publicly_queryable'  => 'yes',
					'comment_status'      => 'yes',
				);
			}
		}

		/**
		 * Initialize the metabox settings fields.
		 *
		 * @since 2.6.0
		 */
		public function load_settings_fields() {

			$this->setting_option_fields = array(
				'exclude_from_search' => array(
					'name'      => 'exclude_from_search',
					'type'      => 'checkbox',
					'label'     => esc_html__( 'Exclude From Search', 'ebox' ),
					'help_text' => esc_html__( 'Exclude From Search', 'ebox' ),
					'value'     => isset( $this->setting_option_values['exclude_from_search'] ) ? $this->setting_option_values['exclude_from_search'] : '',
					'options'   => array(
						'yes' => esc_html__( 'Exclude', 'ebox' ),
					),
				),
				'publicly_queryable'  => array(
					'name'      => 'publicly_queryable',
					'type'      => 'checkbox',
					'label'     => esc_html__( 'Publicly Viewable', 'ebox' ),
					'help_text' => esc_html__( 'Controls access to view single Assignments on the front-end.', 'ebox' ),
					'value'     => isset( $this->setting_option_values['publicly_queryable'] ) ? $this->setting_option_values['publicly_queryable'] : '',
					'options'   => array(
						'yes' => esc_html__( 'Public', 'ebox' ),
					),
				),
				'comment_status'      => array(
					'name'      => 'comment_status',
					'type'      => 'checkbox',
					'label'     => esc_html__( 'Comments enabled', 'ebox' ),
					'help_text' => esc_html__( 'Controls if comments are enabled.', 'ebox' ),
					'value'     => isset( $this->setting_option_values['comment_status'] ) ? $this->setting_option_values['comment_status'] : '',
					'options'   => array(
						'yes' => esc_html__( 'Enabled', 'ebox' ),
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
		ebox_Settings_Assignments_CPT::add_section_instance();
	}
);
