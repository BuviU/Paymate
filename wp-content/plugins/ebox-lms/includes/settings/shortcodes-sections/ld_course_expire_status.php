<?php
/**
 * ebox Shortcode Section for Course Expire Status [ld_course_expire_status].
 *
 * @since 2.4.0
 * @package ebox\Settings\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Shortcodes_Section' ) ) && ( ! class_exists( 'ebox_Shortcodes_Section_ld_course_expire_status' ) ) ) {
	/**
	 * Class ebox Shortcode Section for Course Expire Status [ld_course_expire_status].
	 *
	 * @since 2.4.0
	 */
	class ebox_Shortcodes_Section_ld_course_expire_status extends ebox_Shortcodes_Section /* phpcs:ignore PEAR.NamingConventions.ValidClassName.Invalid */ {
		/**
		 * Public constructor for class.
		 *
		 * @since 2.4.0
		 *
		 * @param array $fields_args Field Args.
		 */
		public function __construct( $fields_args = array() ) {
			$this->fields_args = $fields_args;

			$this->shortcodes_section_key = 'ld_course_expire_status';

			// translators: placeholder: Course.
			$this->shortcodes_section_title = sprintf( esc_html_x( '%s Expire Status', 'placeholder: Course', 'ebox' ), ebox_Custom_Label::get_label( 'course' ) );
			$this->shortcodes_section_type  = 1;

			// translators: placeholder: course.
			$this->shortcodes_section_description = sprintf( esc_html_x( 'This shortcode displays the user %s access expire date.', 'placeholders: course', 'ebox' ), ebox_get_custom_label_lower( 'course' ) );

			parent::__construct();
		}

		/**
		 * Initialize the shortcode fields.
		 *
		 * @since 2.4.0
		 */
		public function init_shortcodes_section_fields() {
			$this->shortcodes_option_fields = array(
				'course_id'    => array(
					'id'        => $this->shortcodes_section_key . '_course_id',
					'name'      => 'course_id',
					'type'      => 'number',

					// translators: placeholder: Course.
					'label'     => sprintf( esc_html_x( '%s ID', 'placeholder: Course', 'ebox' ), ebox_Custom_Label::get_label( 'course' ) ),

					// translators: placeholders: Course, Course.
					'help_text' => sprintf( esc_html_x( 'Enter single %1$s ID. Leave blank for current %2$s.', 'placeholders: Course, Course', 'ebox' ), ebox_Custom_Label::get_label( 'course' ), ebox_Custom_Label::get_label( 'course' ) ),

					'value'     => '',
					'class'     => 'small-text',
				),
				'user_id'      => array(
					'id'        => $this->shortcodes_section_key . '_user_id',
					'name'      => 'user_id',
					'type'      => 'number',
					'label'     => esc_html__( 'User ID', 'ebox' ),
					'help_text' => esc_html__( 'Enter specific User ID. Leave blank for current User.', 'ebox' ),
					'value'     => '',
					'class'     => 'small-text',
				),

				'label_before' => array(
					'id'        => $this->shortcodes_section_key . '_label_before',
					'name'      => 'label_before',
					'type'      => 'text',
					'label'     => esc_html__( 'Label before', 'ebox' ),
					'help_text' => esc_html__( 'The label prefix shown before the access expires', 'ebox' ),
					'value'     => '',
				),

				'label_after'  => array(
					'id'        => $this->shortcodes_section_key . '_label_after',
					'name'      => 'label_after',
					'type'      => 'text',
					'label'     => esc_html__( 'Label after', 'ebox' ),
					'help_text' => esc_html__( 'The label prefix shown after access has expired', 'ebox' ),
					'value'     => '',
				),
				'autop'        => array(
					'id'        => $this->shortcodes_section_key . 'autop',
					'name'      => 'autop',
					'type'      => 'select',
					'label'     => esc_html__( 'Auto Paragraph', 'ebox' ),
					'help_text' => esc_html__( 'Format shortcode content into proper paragraphs.', 'ebox' ),
					'value'     => 'true',
					'options'   => array(
						''      => esc_html__( 'Yes (default)', 'ebox' ),
						'false' => esc_html__( 'No', 'ebox' ),
					),
				),
			);

			if ( ( ! isset( $this->fields_args['post_type'] ) ) || ( ( 'ebox-courses' !== $this->fields_args['post_type'] ) && ( 'ebox-modules' !== $this->fields_args['post_type'] ) && ( 'ebox-topic' !== $this->fields_args['post_type'] ) ) ) {

				$this->shortcodes_option_fields['course_id']['required'] = 'required';

				// translators: placeholder: Course.
				$this->shortcodes_option_fields['course_id']['help_text'] = sprintf( esc_html_x( 'Enter single %s ID.', 'placeholders: Course', 'ebox' ), ebox_Custom_Label::get_label( 'course' ) );
			}

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
			$this->shortcodes_option_fields = apply_filters( 'ebox_settings_fields', $this->shortcodes_option_fields, $this->shortcodes_section_key );

			parent::init_shortcodes_section_fields();
		}
	}
}
