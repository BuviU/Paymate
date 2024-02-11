<?php
/**
 * ebox Settings Section for Per Page Metabox.
 *
 * @since 2.5.5
 * @package ebox\Settings\Sections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Section' ) ) && ( ! class_exists( 'ebox_Settings_Section_General_Per_Page' ) ) ) {
	/**
	 * Class ebox Settings Section for Per Page Metabox.
	 *
	 * @since 2.5.5
	 */
	class ebox_Settings_Section_General_Per_Page extends ebox_Settings_Section {

		/**
		 * Protected constructor for class
		 *
		 * @since 2.5.5
		 */
		protected function __construct() {
			$this->settings_page_id = 'ebox_lms_settings';

			// This is the 'option_name' key used in the wp_options table.
			$this->setting_option_key = 'ebox_settings_per_page';

			// This is the HTML form field prefix used.
			$this->setting_field_prefix = 'ebox_settings_per_page';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_section_key = 'settings_per_page';

			// Section label/header.
			$this->settings_section_label       = esc_html__( 'Global Pagination Settings', 'ebox' );
			$this->settings_section_description = esc_html__( 'Specify the default number of items displayed per page for various listing outputs.', 'ebox' );

			parent::__construct();
		}

		/**
		 * Initialize the metabox settings values.
		 *
		 * @since 2.5.5
		 */
		public function load_settings_values() {
			parent::load_settings_values();

			if ( ! isset( $this->setting_option_values['progress_num'] ) ) {
				$this->setting_option_values['progress_num'] = ebox_LMS_DEFAULT_WIDGET_PER_PAGE;
			}

			if ( ! isset( $this->setting_option_values['quiz_num'] ) ) {
				$this->setting_option_values['quiz_num'] = ebox_LMS_DEFAULT_WIDGET_PER_PAGE;
			}

			if ( ( ebox_LMS_DEFAULT_WIDGET_PER_PAGE === $this->setting_option_values['progress_num'] ) && ( ebox_LMS_DEFAULT_WIDGET_PER_PAGE === $this->setting_option_values['quiz_num'] ) ) {
				$this->setting_option_values['profile_enabled'] = '';
			} else {
				$this->setting_option_values['profile_enabled'] = 'yes';
			}

			if ( ! isset( $this->setting_option_values['per_page'] ) ) {
				$this->setting_option_values['per_page'] = ebox_LMS_DEFAULT_WIDGET_PER_PAGE;
			}

			if ( ! isset( $this->setting_option_values['question_num'] ) ) {
				$this->setting_option_values['question_num'] = ebox_LMS_DEFAULT_WIDGET_PER_PAGE;
			}
		}

		/**
		 * Validate settings field.
		 *
		 * @since 2.5.5
		 *
		 * @param string $val Value to be validated.
		 * @param string $key settings fields key.
		 * @param array  $args Settings field args array.
		 *
		 * @return integer $val.
		 */
		public function validate_section_field_per_page( $val, $key, $args = array() ) {
			// Get the digits only.
			if ( ( isset( $args['field']['validate_args']['allow_empty'] ) ) && ( true === $args['field']['validate_args']['allow_empty'] ) ) {
				$val = preg_replace( '/[^0-9]/', '', $val );
			}

			if ( '' === $val ) {
				switch ( $key ) {
					case 'per_page':
						$val = ebox_LMS_DEFAULT_WIDGET_PER_PAGE;
						break;

					case 'progress_num':
						$val = ebox_LMS_DEFAULT_WIDGET_PER_PAGE;
						break;

					case 'quiz_num':
						$val = ebox_LMS_DEFAULT_WIDGET_PER_PAGE;
						break;

					case 'question_num':
						$val = ebox_LMS_DEFAULT_WIDGET_PER_PAGE;
						break;
				}
			}

			// IF profile is NOT enabled we make sure to clear the quiz and progress values.
			if ( ! isset( $args['post_fields']['profile_enabled'] ) ) {
				if ( ( 'quiz_num' === $key ) || ( 'progress_num' === $key ) ) {
					return ebox_LMS_DEFAULT_WIDGET_PER_PAGE;
				}
			}

			return intval( $val );
		}

		/**
		 * Initialize the metabox settings fields.
		 *
		 * @since 2.5.5
		 */
		public function load_settings_fields() {

			$this->setting_option_fields = array(
				'profile_enabled' => array(
					'name'                => 'profile_enabled',
					'type'                => 'checkbox-switch',
					'label'               => esc_html__( 'WP Profile', 'ebox' ),
					'help_text'           => esc_html__( 'Controls the pagination for the WordPress Profile ebox elements.', 'ebox' ),
					'value'               => $this->setting_option_values['profile_enabled'],
					'options'             => array(
						'yes' => '',
						''    => sprintf(
							// translators: placeholder: default per page number.
							esc_html_x( 'Pagination defaults to %d', 'placeholder: default per page number', 'ebox' ),
							ebox_LMS_DEFAULT_WIDGET_PER_PAGE
						),
					),
					'child_section_state' => ( 'yes' === $this->setting_option_values['profile_enabled'] ) ? 'open' : 'closed',
				),
				'progress_num'    => array(
					'name'              => 'progress_num',
					'type'              => 'number',
					'label'             => sprintf(
						// translators: placeholder: Course.
						esc_html_x( '%s Progress', 'placeholder: Course', 'ebox' ),
						ebox_Custom_Label::get_label( 'course' )
					),
					'value'             => $this->setting_option_values['progress_num'],
					'attrs'             => array(
						'step' => 1,
						'min'  => 1,
					),
					'input_label'       => sprintf(
						// translators: placeholder: courses.
						esc_html_x( '%s per page', 'placeholder: courses', 'ebox' ),
						ebox_get_custom_label_lower( 'course' )
					),
					'validate_callback' => array( $this, 'validate_section_field_per_page' ),
					'validate_args'     => array(
						'allow_empty' => 1,
					),
					'parent_setting'    => 'profile_enabled',
				),
				'quiz_num'        => array(
					'name'              => 'quiz_num',
					'type'              => 'number',
					'label'             => sprintf(
						// translators: placeholder: Quiz.
						esc_html_x( '%s Attempts', 'placeholder: Quiz.', 'ebox' ),
						ebox_get_custom_label( 'quiz' )
					),
					'value'             => $this->setting_option_values['quiz_num'],
					'attrs'             => array(
						'step' => 1,
						'min'  => 1,
					),
					'input_label'       => sprintf(
						// translators: placeholder: quizzes.
						esc_html_x( '%s per page', 'placeholder: quizzes', 'ebox' ),
						ebox_get_custom_label_lower( 'quizzes' )
					),
					'validate_callback' => array( $this, 'validate_section_field_per_page' ),
					'validate_args'     => array(
						'allow_empty' => 1,
					),
					'parent_setting'    => 'profile_enabled',
				),

				'per_page'        => array(
					'name'              => 'per_page',
					'type'              => 'number',
					'label'             => esc_html__( 'Shortcodes & Widgets', 'ebox' ),
					'help_text'         => esc_html__( 'Controls the global pagination for the LD shortcodes as well as courseinfo widget. These can be overridden individually.', 'ebox' ),
					'value'             => $this->setting_option_values['per_page'],
					'attrs'             => array(
						'step' => 1,
						'min'  => 1,
					),
					'validate_callback' => array( $this, 'validate_section_field_per_page' ),
					'validate_args'     => array(
						'allow_empty' => 1,
					),
				),
				'question_num'    => array(
					'name'              => 'question_num',
					'type'              => 'number',
					'label'             => sprintf(
						// translators: placeholders: Question.
						esc_html_x( 'Backend %s Widget', 'placeholder: Question', 'ebox' ),
						ebox_Custom_Label::get_label( 'question' )
					),
					'help_text'         => sprintf(
						// translators: placeholders: Questions, quiz, question.
						esc_html_x( 'Controls the pagination for the %1$s admin widget when editing a %2$s or %3$s.', 'Questions, quiz, question', 'ebox' ),
						ebox_get_custom_label( 'questions' ),
						ebox_get_custom_label_lower( 'quiz' ),
						ebox_get_custom_label_lower( 'question' )
					),
					'value'             => $this->setting_option_values['question_num'],
					'attrs'             => array(
						'step' => 1,
						'min'  => 1,
					),
					'validate_callback' => array( $this, 'validate_section_field_per_page' ),
					'validate_args'     => array(
						'allow_empty' => 1,
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
		ebox_Settings_Section_General_Per_Page::add_section_instance();
	}
);
