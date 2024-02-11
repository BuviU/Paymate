<?php
/**
 * ebox Shortcode Section for Course info [ld_course_info].
 *
 * @since 2.4.0
 * @package ebox\Settings\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Shortcodes_Section' ) ) && ( ! class_exists( 'ebox_Shortcodes_Section_ld_course_info' ) ) ) {
	/**
	 * Class ebox Shortcode Section for Course info [ld_course_info].
	 *
	 * @since 2.4.0
	 */
	class ebox_Shortcodes_Section_ld_course_info extends ebox_Shortcodes_Section /* phpcs:ignore PEAR.NamingConventions.ValidClassName.Invalid */ {

		/**
		 * Public constructor for class.
		 *
		 * @since 2.4.0
		 *
		 * @param array $fields_args Field Args.
		 */
		public function __construct( $fields_args = array() ) {
			$this->fields_args = $fields_args;

			$this->shortcodes_section_key = 'ld_course_info';
			// translators: placeholder: Course.
			$this->shortcodes_section_title = sprintf( esc_html_x( 'LD %s Info', 'placeholder: Course', 'ebox' ), ebox_Custom_Label::get_label( 'course' ) );
			$this->shortcodes_section_type  = 1;
			// translators: placeholder: courses.
			$this->shortcodes_section_description = sprintf( esc_html_x( 'This shortcode shows the %s and progress for the user.', 'placeholder: courses', 'ebox' ), ebox_get_custom_label_lower( 'courses' ) );

			parent::__construct();
		}

		/**
		 * Initialize the shortcode fields.
		 *
		 * @since 2.4.0
		 */
		public function init_shortcodes_section_fields() {
			$this->shortcodes_option_fields = array(
				'user_id'                   => array(
					'id'        => $this->shortcodes_section_key . '_user_id',
					'name'      => 'user_id',
					'type'      => 'number',
					'label'     => esc_html__( 'User ID', 'ebox' ),
					'help_text' => esc_html__( 'Enter specific User ID. Leave blank for current User.', 'ebox' ),
					'value'     => '',
					'class'     => 'small-text',
				),

				'registered_show_thumbnail' => array(
					'id'        => $this->shortcodes_section_key . '_registered_show_thumbnail',
					'name'      => 'registered_show_thumbnail',
					'type'      => 'select',
					// translators: placeholder: Course.
					'label'     => sprintf( esc_html_x( 'Show %s Thumbnail', 'placeholder: Course', 'ebox' ), ebox_Custom_Label::get_label( 'course' ) ),
					// translators: placeholder: course.
					'help_text' => sprintf( esc_html_x( 'shows a %s thumbnail.', 'placeholder: course', 'ebox' ), ebox_get_custom_label_lower( 'course' ) ),
					'value'     => 'true',
					'options'   => array(
						''      => esc_html__( 'Yes (default)', 'ebox' ),
						'false' => esc_html__( 'No', 'ebox' ),
					),
				),

				'registered_num'            => array(
					'id'        => $this->shortcodes_section_key . '_registered_num',
					'name'      => 'registered_num',
					'type'      => 'number',
					'label'     => esc_html__( 'Registered per page', 'ebox' ),
					// translators: placeholders: courses, default per page.
					'help_text' => sprintf( esc_html_x( 'Registered %1$s per page. Default is %2$d. Set to zero for all.', 'placeholders: courses, default per page', 'ebox' ), ebox_get_custom_label_lower( 'courses' ), ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_General_Per_Page', 'per_page' ) ),
					'value'     => '',
					'class'     => 'small-text',
					'attrs'     => array(
						'min'  => 0,
						'step' => 1,
					),
				),
				'registered_orderby'        => array(
					'id'        => $this->shortcodes_section_key . '_registered_orderby',
					'name'      => 'registered_orderby',
					'type'      => 'select',
					'label'     => esc_html__( 'Registered order by', 'ebox' ),
					'help_text' => wp_kses_post( __( 'See <a target="_blank" href="https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters">the full list of available orderby options here.</a>', 'ebox' ) ),
					'value'     => '',
					'options'   => array(
						''           => esc_html__( 'Title (default) - Order by post title', 'ebox' ),
						'ID'         => esc_html__( 'ID - Order by post id', 'ebox' ),
						'date'       => esc_html__( 'Date - Order by post date', 'ebox' ),
						'menu_order' => esc_html__( 'Menu - Order by Page Order Value', 'ebox' ),
					),
				),
				'registered_order'          => array(
					'id'        => $this->shortcodes_section_key . '_registered_order',
					'name'      => 'registered_order',
					'type'      => 'select',
					'label'     => esc_html__( 'Progress Order', 'ebox' ),
					// translators: placeholder: Courses.
					'help_text' => sprintf( esc_html_x( 'Order of %s displayed', 'placeholder: Courses', 'ebox' ), ebox_Custom_Label::get_label( 'courses' ) ),
					'value'     => '',
					'options'   => array(
						''     => esc_html__( 'ASC (default) - lowest to highest values', 'ebox' ),
						'DESC' => esc_html__( 'DESC - highest to lowest values', 'ebox' ),
					),
				),

				'progress_num'              => array(
					'id'        => $this->shortcodes_section_key . '_progress_num',
					'name'      => 'progress_num',
					'type'      => 'number',
					// translators: placeholder: Courses.
					'label'     => sprintf( esc_html_x( '%s per page', 'placeholder: Courses', 'ebox' ), ebox_Custom_Label::get_label( 'courses' ) ),
					// translators: placeholders: Courses, default per page.
					'help_text' => sprintf( esc_html_x( '%1$s per page. Default is %2$d. Set to zero for all.', 'placeholders: Courses, default per page', 'ebox' ), ebox_Custom_Label::get_label( 'courses' ), ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_General_Per_Page', 'per_page' ) ),
					'value'     => '',
					'class'     => 'small-text',
					'attrs'     => array(
						'min'  => 0,
						'step' => 1,
					),
				),
				'progress_orderby'          => array(
					'id'        => $this->shortcodes_section_key . '_progress_orderby',
					'name'      => 'progress_orderby',
					'type'      => 'select',
					// translators: placeholder: Courses.
					'label'     => sprintf( esc_html_x( '%s order by', 'placeholder: Courses', 'ebox' ), ebox_Custom_Label::get_label( 'courses' ) ),
					'help_text' => wp_kses_post( __( 'See <a target="_blank" href="https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters">the full list of available orderby options here.</a>', 'ebox' ) ),
					'value'     => '',
					'options'   => array(
						''           => esc_html__( 'Title (default) - Order by post title', 'ebox' ),
						'ID'         => esc_html__( 'ID - Order by post id', 'ebox' ),
						'date'       => esc_html__( 'Date - Order by post date', 'ebox' ),
						'menu_order' => esc_html__( 'Menu - Order by Page Order Value', 'ebox' ),
					),
				),
				'progress_order'            => array(
					'id'        => $this->shortcodes_section_key . '_progress_order',
					'name'      => 'progress_order',
					'type'      => 'select',
					// translators: placeholder: Courses.
					'label'     => sprintf( esc_html_x( '%s Order', 'placeholder: Courses', 'ebox' ), ebox_Custom_Label::get_label( 'courses' ) ),
					// translators: placeholder: Courses.
					'help_text' => sprintf( esc_html_x( 'Order of %s displayed', 'placeholder: Courses', 'ebox' ), ebox_Custom_Label::get_label( 'courses' ) ),
					'value'     => '',
					'options'   => array(
						''     => esc_html__( 'ASC (default) - lowest to highest values', 'ebox' ),
						'DESC' => esc_html__( 'DESC - highest to lowest values', 'ebox' ),
					),
				),

				'quiz_num'                  => array(
					'id'        => $this->shortcodes_section_key . '_quiz_num',
					'name'      => 'quiz_num',
					'type'      => 'number',
					// translators: placeholder: Quizzes.
					'label'     => sprintf( esc_html_x( '%s per page', 'placeholder: Quizzes', 'ebox' ), ebox_Custom_Label::get_label( 'quizzes' ) ),
					// translators: placeholders: Quizzes, default per page.
					'help_text' => sprintf( esc_html_x( '%1$s per page. Default is %1$d. Set to zero for all.', 'placeholders: Quizzes, default per page', 'ebox' ), ebox_Custom_Label::get_label( 'quizzes' ), ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_General_Per_Page', 'per_page' ) ),
					'value'     => '',
					'class'     => 'small-text',
					'attrs'     => array(
						'min'  => 0,
						'step' => 1,
					),
				),

				'quiz_orderby'              => array(
					'id'        => $this->shortcodes_section_key . '_quiz_orderby',
					'name'      => 'quiz_orderby',
					'type'      => 'select',
					// translators: placeholder: Quizzes.
					'label'     => sprintf( esc_html_x( '%s order by', 'placeholder: Quizzes', 'ebox' ), ebox_Custom_Label::get_label( 'quizzes' ) ),
					'help_text' => wp_kses_post( __( 'See <a target="_blank" href="https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters">the full list of available orderby options here</a>.', 'ebox' ) ),
					'value'     => '',
					'options'   => array(
						''           => esc_html__( 'Date Taken (default) - Order by date taken', 'ebox' ),
						'title'      => esc_html__( 'Title - Order by post title', 'ebox' ),
						'ID'         => esc_html__( 'ID - Order by post id', 'ebox' ),
						'date'       => esc_html__( 'Date - Order by post date', 'ebox' ),
						'menu_order' => esc_html__( 'Menu - Order by Page Order Value', 'ebox' ),
					),
				),
				'quiz_order'                => array(
					'id'        => $this->shortcodes_section_key . '_quiz_order',
					'name'      => 'quiz_order',
					'type'      => 'select',
					// translators: placeholder: Quizzes.
					'label'     => sprintf( esc_html_x( '%s Order', 'placeholder: Quizzes', 'ebox' ), ebox_Custom_Label::get_label( 'quizzes' ) ),
					// translators: placeholder: Quizzes.
					'help_text' => sprintf( esc_html_x( 'Order of %s displayed.', 'placeholder: Quizzes', 'ebox' ), ebox_Custom_Label::get_label( 'quizzes' ) ),
					'value'     => '',
					'options'   => array(
						''    => esc_html__( 'DESC (default) - highest to lowest values', 'ebox' ),
						'ASC' => esc_html__( 'ASC - lowest to highest values', 'ebox' ),
					),
				),

			);

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
			$this->shortcodes_option_fields = apply_filters( 'ebox_settings_fields', $this->shortcodes_option_fields, $this->shortcodes_section_key );

			parent::init_shortcodes_section_fields();
		}
	}
}
