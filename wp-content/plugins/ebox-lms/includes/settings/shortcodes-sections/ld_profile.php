<?php
/**
 * ebox Shortcode Section for Profile [ld_profile].
 *
 * @since 2.4.0
 * @package ebox\Settings\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Shortcodes_Section' ) ) && ( ! class_exists( 'ebox_Shortcodes_Section_ld_profile' ) ) ) {
	/**
	 * Class ebox Shortcode Section for Profile [ld_profile].
	 *
	 * @since 2.4.0
	 */
	class ebox_Shortcodes_Section_ld_profile extends ebox_Shortcodes_Section /* phpcs:ignore PEAR.NamingConventions.ValidClassName.Invalid */ {

		/**
		 * Public constructor for class.
		 *
		 * @since 2.4.0
		 *
		 * @param array $fields_args Field Args.
		 */
		public function __construct( $fields_args = array() ) {
			$this->fields_args = $fields_args;

			$this->shortcodes_section_key   = 'ld_profile';
			$this->shortcodes_section_title = esc_html__( 'Profile', 'ebox' );
			$this->shortcodes_section_type  = 1;

			// translators: placeholder: placeholder: placeholder: courses, course, quiz.
			$this->shortcodes_section_description = sprintf( esc_html_x( 'Displays user\'s enrolled %1$s, %2$s progress, %3$s scores, and achieved certificates.', 'placeholder: courses, course, quiz', 'ebox' ), ebox_get_custom_label_lower( 'courses' ), ebox_get_custom_label_lower( 'course' ), ebox_get_custom_label_lower( 'quiz' ) );

			parent::__construct();
		}

		/**
		 * Initialize the shortcode fields.
		 *
		 * @since 2.4.0
		 */
		public function init_shortcodes_section_fields() {
			$this->shortcodes_option_fields = array(

				'per_page'           => array(
					'id'        => $this->shortcodes_section_key . '_per_page',
					'name'      => 'per_page',
					'type'      => 'number',
					'label'     => sprintf(
						// translators: placeholder: Courses.
						esc_html_x( '%s per page', 'placeholder: Courses', 'ebox' ),
						ebox_Custom_Label::get_label( 'Courses' )
					),
					'help_text' => sprintf(
						// translators: placeholder: placeholder: Courses, default per page.
						esc_html_x( '%1$s per page. Default is %2$d. Set to zero for all.', 'placeholder: Courses, default per page', 'ebox' ),
						ebox_Custom_Label::get_label( 'Courses' ),
						ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_General_Per_Page', 'per_page' )
					),
					'value'     => false,
					'class'     => 'small-text',
				),
				'quiz_num'           => array(
					'id'        => $this->shortcodes_section_key . '_quiz_num',
					'name'      => 'quiz_num',
					'type'      => 'number',
					'label'     => sprintf(
						// translators: placeholder: Quiz, Course.
						esc_html_x( '%1$s attempts per %2$s', 'placeholder: Quiz, Course', 'ebox' ),
						ebox_Custom_Label::get_label( 'Quiz' ),
						ebox_Custom_Label::get_label( 'Course' )
					),
					'help_text' => sprintf(
						// translators: placeholder: placeholder: Quiz, Course, default per page.
						esc_html_x( '%1$s attempts per %2$s. Default is %2$d. Set to zero for all.', 'placeholder: Quiz, default per page', 'ebox' ),
						ebox_Custom_Label::get_label( 'Quiz' ),
						ebox_Custom_Label::get_label( 'Course' ),
						ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_General_Per_Page', 'per_page' )
					),
					'value'     => false,
					'class'     => 'small-text',
				),
				'orderby'            => array(
					'id'        => $this->shortcodes_section_key . '_orderby',
					'name'      => 'orderby',
					'type'      => 'select',
					'label'     => esc_html__( 'Order by', 'ebox' ),
					'help_text' => wp_kses_post( __( 'See <a target="_blank" href="https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters">the full list of available orderby options here.</a>', 'ebox' ) ),
					'value'     => 'ID',
					'options'   => array(
						''           => esc_html__( 'ID - Order by post id. (default)', 'ebox' ),
						'title'      => esc_html__( 'Title - Order by post title', 'ebox' ),
						'date'       => esc_html__( 'Date - Order by post date', 'ebox' ),
						'menu_order' => esc_html__( 'Menu - Order by Page Order Value', 'ebox' ),
					),
				),
				'order'              => array(
					'id'        => $this->shortcodes_section_key . '_order',
					'name'      => 'order',
					'type'      => 'select',
					'label'     => esc_html__( 'Order', 'ebox' ),
					'help_text' => esc_html__( 'Order', 'ebox' ),
					'value'     => 'ID',
					'options'   => array(
						''    => esc_html__( 'DESC - highest to lowest values (default)', 'ebox' ),
						'ASC' => esc_html__( 'ASC - lowest to highest values', 'ebox' ),
					),
				),

				'show_search'        => array(
					'id'        => $this->shortcodes_section_key . 'show_search',
					'name'      => 'show_search',
					'type'      => 'select',
					'label'     => esc_html__( 'Show Search', 'ebox' ),
					'value'     => 'yes',
					'options'   => array(
						''   => esc_html__( 'Yes', 'ebox' ),
						'no' => esc_html__( 'No', 'ebox' ),
					),
					'help_text' => esc_html__( 'LD30 template only', 'ebox' ),
				),

				'show_header'        => array(
					'id'        => $this->shortcodes_section_key . 'show_header',
					'name'      => 'show_header',
					'type'      => 'select',
					// translators: placeholder: Course.
					'label'     => esc_html__( 'Show Profile Header', 'ebox' ),
					'help_text' => esc_html__( 'show_header', 'ebox' ),
					'value'     => '',
					'options'   => array(
						''   => esc_html__( 'Yes', 'ebox' ),
						'no' => esc_html__( 'No', 'ebox' ),
					),
				),
				'course_points_user' => array(
					'id'        => $this->shortcodes_section_key . 'course_points_user',
					'name'      => 'course_points_user',
					'type'      => 'select',
					// translators: placeholder: Course.
					'label'     => sprintf( esc_html_x( 'Show Earned %s Points', 'placeholder: Course', 'ebox' ), ebox_Custom_Label::get_label( 'course' ) ),
					// translators: placeholder: Course.
					'help_text' => sprintf( esc_html_x( 'Show Earned %s Points', 'placeholder: Course', 'ebox' ), ebox_Custom_Label::get_label( 'course' ) ),
					'value'     => '',
					'options'   => array(
						''   => esc_html__( 'Yes', 'ebox' ),
						'no' => esc_html__( 'No', 'ebox' ),
					),
				),
				'profile_link'       => array(
					'id'        => $this->shortcodes_section_key . 'profile_link',
					'name'      => 'profile_link',
					'type'      => 'select',
					'label'     => esc_html__( 'Show Profile Link', 'ebox' ),
					'help_text' => esc_html__( 'Show Profile Link', 'ebox' ),
					'value'     => 'yes',
					'options'   => array(
						''   => esc_html__( 'Yes', 'ebox' ),
						'no' => esc_html__( 'No', 'ebox' ),
					),
				),
				'show_quizzes'       => array(
					'id'        => $this->shortcodes_section_key . 'show_quizzes',
					'name'      => 'show_quizzes',
					'type'      => 'select',
					'label'     => sprintf(
						// translators: placeholder: Quiz.
						esc_html_x( 'Show User %s Attempts', 'placeholder: Quiz', 'ebox' ),
						ebox_get_custom_label( 'quiz' )
					),
					'help_text' => sprintf(
						// translators: placeholder: Quiz.
						esc_html_x( 'Show User %s Attempts', 'placeholder: Quiz', 'ebox' ),
						ebox_get_custom_label( 'quiz' )
					),
					'value'     => 'yes',
					'options'   => array(
						''   => esc_html__( 'Yes', 'ebox' ),
						'no' => esc_html__( 'No', 'ebox' ),
					),
				),

				'expand_all'         => array(
					'id'        => $this->shortcodes_section_key . 'expand_all',
					'name'      => 'expand_all',
					'type'      => 'select',
					// translators: placeholder: Course.
					'label'     => sprintf( esc_html_x( 'Expand All %s Sections', 'placeholder: Course', 'ebox' ), ebox_Custom_Label::get_label( 'course' ) ),
					// translators: placeholder: Course.
					'help_text' => sprintf( esc_html_x( 'Expand All %s sections', 'placeholder: Course', 'ebox' ), ebox_Custom_Label::get_label( 'course' ) ),
					'value'     => 'no',
					'options'   => array(
						''    => esc_html__( 'No', 'ebox' ),
						'yes' => esc_html__( 'Yes', 'ebox' ),
					),
				),
			);

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
			$this->shortcodes_option_fields = apply_filters( 'ebox_settings_fields', $this->shortcodes_option_fields, $this->shortcodes_section_key );

			parent::init_shortcodes_section_fields();
		}
	}
}
