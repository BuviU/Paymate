<?php
/**
 * ebox Shortcode Section for User Course Points [ld_user_course_points].
 *
 * @since 2.4.0
 * @package ebox\Settings\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Shortcodes_Section' ) ) && ( ! class_exists( 'ebox_Shortcodes_Section_ld_user_course_points' ) ) ) {
	/**
	 * Class ebox Shortcode Section for User Course Points [ld_user_course_points].
	 *
	 * @since 2.4.0
	 */
	class ebox_Shortcodes_Section_ld_user_course_points extends ebox_Shortcodes_Section /* phpcs:ignore PEAR.NamingConventions.ValidClassName.Invalid */ {

		/**
		 * Public constructor for class.
		 *
		 * @since 2.4.0
		 *
		 * @param array $fields_args Field Args.
		 */
		public function __construct( $fields_args = array() ) {
			$this->fields_args = $fields_args;

			$this->shortcodes_section_key = 'ld_user_course_points';
			// translators: placeholder: Course.
			$this->shortcodes_section_title = sprintf( esc_html_x( 'User %s Points', 'placeholder: Course', 'ebox' ), ebox_Custom_Label::get_label( 'course' ) );
			$this->shortcodes_section_type  = 1;
			// translators: placeholder: course.
			$this->shortcodes_section_description = sprintf( esc_html_x( 'This shortcode shows the earned %s points for the user.', 'placeholders: course, course', 'ebox' ), ebox_get_custom_label_lower( 'course' ) );

			parent::__construct();
		}

		/**
		 * Initialize the shortcode fields.
		 *
		 * @since 2.4.0
		 */
		public function init_shortcodes_section_fields() {
			$this->shortcodes_option_fields = array(
				'user_id' => array(
					'id'        => $this->shortcodes_section_key . '_user_id',
					'name'      => 'user_id',
					'type'      => 'number',
					'label'     => esc_html__( 'User ID', 'ebox' ),
					'help_text' => esc_html__( 'Enter specific User ID. Leave blank for current User.', 'ebox' ),
					'value'     => '',
					'class'     => 'small-text',
				),
			);

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
			$this->shortcodes_option_fields = apply_filters( 'ebox_settings_fields', $this->shortcodes_option_fields, $this->shortcodes_section_key );

			parent::init_shortcodes_section_fields();
		}
	}
}
