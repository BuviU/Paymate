<?php
/**
 * ebox Shortcode Section for Course In Progress [course_inprogress].
 *
 * @since 2.4.0
 * @package ebox\Settings\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Shortcodes_Section' ) ) && ( ! class_exists( 'ebox_Shortcodes_Section_course_inprogress' ) ) ) {
	/**
	 * Class ebox Shortcode Section for Course In Progress [course_inprogress].
	 *
	 * @since 2.4.0
	 */
	class ebox_Shortcodes_Section_course_inprogress extends ebox_Shortcodes_Section /* phpcs:ignore PEAR.NamingConventions.ValidClassName.Invalid */ {

		/**
		 * Public constructor for class.
		 *
		 * @since 2.4.0
		 *
		 * @param array $fields_args Field Args.
		 */
		public function __construct( $fields_args = array() ) {
			$this->fields_args = $fields_args;

			$this->shortcodes_section_key = 'course_inprogress';
			// translators: placeholder: Course.
			$this->shortcodes_section_title = sprintf( esc_html_x( '%s In Progress', 'placeholder: Course', 'ebox' ), ebox_Custom_Label::get_label( 'course' ) );
			$this->shortcodes_section_type  = 2;

			// translators: placeholder: course.
			$this->shortcodes_section_description = sprintf( wp_kses_post( _x( 'This shortcode shows the content if the user has started but not completed the %s. The shortcode can be used on <strong>any</strong> page or widget area.', 'placeholders: course', 'ebox' ) ), ebox_get_custom_label_lower( 'course' ) );

			parent::__construct();
		}

		/**
		 * Initialize the shortcode fields.
		 *
		 * @since 2.4.0
		 */
		public function init_shortcodes_section_fields() {
			$this->shortcodes_option_fields = array(
				'message'   => array(
					'id'        => $this->shortcodes_section_key . '_message',
					'name'      => 'message',
					'type'      => 'textarea',
					'label'     => esc_html__( 'Message shown to user', 'ebox' ),
					'help_text' => esc_html__( 'Message shown to user', 'ebox' ),
					'value'     => '',
					'required'  => 'required',
				),
				'course_id' => array(
					'id'        => $this->shortcodes_section_key . '_course_id',
					'name'      => 'course_id',
					'type'      => 'number',
					'label'     => sprintf(
						// translators: placeholder: Course.
						esc_html_x( '%s ID', 'placeholder: Course', 'ebox' ),
						ebox_Custom_Label::get_label( 'course' )
					),
					'help_text' => sprintf(
						// translators: placeholders: Course, Course.
						esc_html_x( 'Enter single %1$s ID. Leave blank for current %2$s.', 'placeholders: Course, Course', 'ebox' ),
						ebox_Custom_Label::get_label( 'course' ),
						ebox_Custom_Label::get_label( 'course' )
					),
					'value'     => '',
					'class'     => 'small-text',
					'required'  => 'required',
				),
				'user_id'   => array(
					'id'        => $this->shortcodes_section_key . '_user_id',
					'name'      => 'user_id',
					'type'      => 'number',
					'label'     => esc_html__( 'User ID', 'ebox' ),
					'help_text' => esc_html__( 'Enter specific User ID. Leave blank for current User.', 'ebox' ),
					'value'     => '',
					'class'     => 'small-text',
				),
				'autop'     => array(
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

			if ( ( isset( $this->fields_args['post_type'] ) ) && ( in_array( $this->fields_args['post_type'], ebox_get_post_types( 'course' ), true ) ) ) {
				unset( $this->shortcodes_option_fields['course_id']['required'] );
			}

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
			$this->shortcodes_option_fields = apply_filters( 'ebox_settings_fields', $this->shortcodes_option_fields, $this->shortcodes_section_key );

			parent::init_shortcodes_section_fields();
		}
	}
}
