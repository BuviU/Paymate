<?php
/**
 * ebox Settings Section for Question Custom Post Type Metabox.
 *
 * @since 2.6.0
 * @package ebox\Settings\Sections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Section' ) ) && ( ! class_exists( 'ebox_Settings_Questions_CPT' ) ) ) {
	/**
	 * Class ebox Settings Section for Question Custom Post Type Metabox.
	 *
	 * @since 2.6.0
	 */
	class ebox_Settings_Questions_CPT extends ebox_Settings_Section {

		/**
		 * Protected constructor for class
		 *
		 * @since 2.6.0
		 */
		protected function __construct() {
			// What screen ID are we showing on.
			$this->settings_screen_id = 'ebox-question_page_questions-options';

			// The page ID (different than the screen ID).
			$this->settings_page_id = 'questions-options';

			// This is the 'option_name' key used in the wp_options table.
			$this->setting_option_key = 'ebox_settings_questions_cpt';

			// This is the HTML form field prefix used.
			$this->setting_field_prefix = 'ebox_settings_questions_cpt';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_section_key = 'cpt_options';

			// Section label/header.
			$this->settings_section_label = sprintf(
				// translators: placeholder: Quiz.
				esc_html_x( '%s Custom Post Type Options', 'placeholder: Question', 'ebox' ),
				ebox_Custom_Label::get_label( 'question' )
			);

			// Used to show the section description above the fields. Can be empty.
			$this->settings_section_description = sprintf(
				// translators: placeholder: Quizzes.
				esc_html_x( 'Control the ebox %s Custom Post Type Options.', 'placeholder: Questions', 'ebox' ),
				ebox_Custom_Label::get_label( 'questions' )
			);

			parent::__construct();
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
		ebox_Settings_Questions_CPT::add_section_instance();
	}
);
