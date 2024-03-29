<?php
/**
 * ebox Shortcode Section for Quizzes List [ld_quiz_list].
 *
 * @since 2.4.0
 * @package ebox\Settings\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Shortcodes_Section' ) ) && ( ! class_exists( 'ebox_Shortcodes_Section_ld_quiz_list' ) ) ) {
	/**
	 * Class ebox Shortcode Section for Quizzes List [ld_quiz_list].
	 *
	 * @since 2.4.0
	 */
	class ebox_Shortcodes_Section_ld_quiz_list extends ebox_Shortcodes_Section /* phpcs:ignore PEAR.NamingConventions.ValidClassName.Invalid */ {

		/**
		 * Public constructor for class.
		 *
		 * @since 2.4.0
		 *
		 * @param array $fields_args Field Args.
		 */
		public function __construct( $fields_args = array() ) {
			$this->fields_args              = $fields_args;
			$this->shortcodes_section_key   = 'ld_quiz_list';
			$this->shortcodes_section_title = sprintf(
				// translators: placeholder: Quiz.
				esc_html_x( '%s List', 'placeholder: Quiz', 'ebox' ),
				ebox_Custom_Label::get_label( 'quiz' )
			);
			$this->shortcodes_section_type        = 1;
			$this->shortcodes_section_description = sprintf(
				wp_kses_post(
					// translators: placeholders: quizzes, quizzes (URL slug).
					_x( 'This shortcode shows list of %1$s. You can use this shortcode on any page if you don\'t want to use the default <code>/%2$s/</code> page.', 'placeholders: quizzes, quizzes (URL slug)', 'ebox' )
				),
				ebox_get_custom_label_lower( 'quizzes' ),
				ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_Permalinks', 'quizzes' )
			);

			parent::__construct();
		}

		/**
		 * Initialize shortcode fields.
		 *
		 * @since 2.4.0
		 */
		public function init_shortcodes_section_fields() {
			$this->shortcodes_option_fields = array(
				'course_id'      => array(
					'id'        => $this->shortcodes_section_key . '_course_id',
					'name'      => 'course_id',
					'type'      => 'number',
					'label'     => sprintf(
						// translators: placeholder: Course.
						esc_html_x( '%s ID', 'placeholder: Course', 'ebox' ),
						ebox_Custom_Label::get_label( 'course' )
					),
					'help_text' => sprintf(
						// translators: placeholders: Course, Courses.
						esc_html_x( 'Enter single %1$s ID. Leave blank for all %2$s.', 'placeholders: Course, Courses', 'ebox' ),
						ebox_Custom_Label::get_label( 'course' ),
						ebox_Custom_Label::get_label( 'courses' )
					),
					'value'     => '',
					'class'     => 'small-text',
				),

				'lesson_id'      => array(
					'id'        => $this->shortcodes_section_key . '_lesson_id',
					'name'      => 'lesson_id',
					'type'      => 'number',
					// translators: placeholder: Lesson.
					'label'     => sprintf( esc_html_x( '%s ID', 'placeholder: Lesson', 'ebox' ), ebox_Custom_Label::get_label( 'lesson' ) ),
					// translators: placeholders: Lesson, Topic, Course.
					'help_text' => sprintf( esc_html_x( 'Must be used with course_id. Enter single %1$s or %2$s ID. Leave blank for all step within %3$s. Set zero for global.', 'placeholders: Lesson, Topic, Course', 'ebox' ), ebox_Custom_Label::get_label( 'lesson' ), ebox_Custom_Label::get_label( 'topic' ), ebox_Custom_Label::get_label( 'lesson' ) ),
					'value'     => '',
					'class'     => 'small-text',
				),

				'orderby'        => array(
					'id'        => $this->shortcodes_section_key . '_orderby',
					'name'      => 'orderby',
					'type'      => 'select',
					'label'     => esc_html__( 'Order by', 'ebox' ),
					'help_text' => wp_kses_post( __( 'See <a target="_blank" href="https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters">the full list of available orderby options here.</a>', 'ebox' ) ),
					'value'     => 'ID',
					'options'   => array(
						// translators: placeholder: course.
						''           => sprintf( esc_html_x( 'Order by %s. (default)', 'placeholder: course', 'ebox' ), ebox_get_custom_label_lower( 'course' ) ),
						'id'         => esc_html__( 'ID - Order by post id.', 'ebox' ),
						'title'      => esc_html__( 'Title - Order by post title', 'ebox' ),
						'date'       => esc_html__( 'Date - Order by post date', 'ebox' ),
						'menu_order' => esc_html__( 'Menu - Order by Page Order Value', 'ebox' ),
					),
				),
				'order'          => array(
					'id'        => $this->shortcodes_section_key . '_order',
					'name'      => 'order',
					'type'      => 'select',
					'label'     => esc_html__( 'Order', 'ebox' ),
					'help_text' => esc_html__( 'Order', 'ebox' ),
					'value'     => 'ID',
					'options'   => array(
						// translators: placeholder: course.
						''     => sprintf( esc_html_x( 'Order per %s (default)', 'placeholder: course', 'ebox' ), ebox_get_custom_label_lower( 'course' ) ),
						'DESC' => esc_html__( 'DESC - highest to lowest values', 'ebox' ),
						'ASC'  => esc_html__( 'ASC - lowest to highest values', 'ebox' ),
					),
				),
				'num'            => array(
					'id'        => $this->shortcodes_section_key . '_num',
					'name'      => 'num',
					'type'      => 'number',
					// translators: placeholder: Quizzes.
					'label'     => sprintf( esc_html_x( '%s Per Page', 'placeholder: quizzes', 'ebox' ), ebox_Custom_Label::get_label( 'quizzes' ) ),
					// translators: placeholders: Quizzes, default per page.
					'help_text' => sprintf( esc_html_x( '%1$s per page. Default is %2$d. Set to zero for all.', 'placeholders: Quizzes, default per page', 'ebox' ), ebox_Custom_Label::get_label( 'quizzes' ), ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_General_Per_Page', 'per_page' ) ),
					'value'     => '',
					'class'     => 'small-text',
					'attrs'     => array(
						'min'  => 0,
						'step' => 1,
					),
				),
				'show_content'   => array(
					'id'        => $this->shortcodes_section_key . 'show_content',
					'name'      => 'show_content',
					'type'      => 'select',
					// translators: placeholder: Quiz.
					'label'     => sprintf( esc_html_x( 'Show %s Content', 'placeholder: Quiz', 'ebox' ), ebox_Custom_Label::get_label( 'quiz' ) ),
					// translators: placeholder: quiz.
					'help_text' => sprintf( esc_html_x( 'shows %s content.', 'placeholder: quiz', 'ebox' ), ebox_get_custom_label_lower( 'quiz' ) ),
					'value'     => 'true',
					'options'   => array(
						''      => esc_html__( 'Yes (default)', 'ebox' ),
						'false' => esc_html__( 'No', 'ebox' ),
					),
				),
				'show_thumbnail' => array(
					'id'        => $this->shortcodes_section_key . 'show_thumbnail',
					'name'      => 'show_thumbnail',
					'type'      => 'select',
					// translators: placeholder: Quiz.
					'label'     => sprintf( esc_html_x( 'Show %s Thumbnail', 'placeholder: Quiz', 'ebox' ), ebox_Custom_Label::get_label( 'quiz' ) ),
					// translators: placeholder: quiz.
					'help_text' => sprintf( esc_html_x( 'shows a %s thumbnail.', 'placeholder: quiz', 'ebox' ), ebox_get_custom_label_lower( 'quiz' ) ),
					'value'     => 'true',
					'options'   => array(
						''      => esc_html__( 'Yes (default)', 'ebox' ),
						'false' => esc_html__( 'No', 'ebox' ),
					),
				),
			);

			if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Courses_Builder', 'shared_steps' ) != 'yes' ) {
				foreach ( $this->shortcodes_option_fields['orderby']['options'] as $option_key => $option_label ) {
					if ( empty( $option_key ) ) {
						unset( $this->shortcodes_option_fields['orderby']['options'][ $option_key ] );
					}
				}

				foreach ( $this->shortcodes_option_fields['order']['options'] as $option_key => $option_label ) {
					if ( empty( $option_key ) ) {
						unset( $this->shortcodes_option_fields['order']['options'][ $option_key ] );
					}
				}
			}

			if ( defined( 'ebox_COURSE_GRID_FILE' ) ) {
				$this->shortcodes_option_fields['col'] = array(
					'id'        => $this->shortcodes_section_key . '_col',
					'name'      => 'col',
					'type'      => 'number',
					'label'     => esc_html__( 'Columns', 'ebox' ),
					// translators: placeholder: course.
					'help_text' => sprintf( esc_html_x( 'number of columns to show when using %s grid addon', 'placeholder: course', 'ebox' ), ebox_get_custom_label_lower( 'course' ) ),
					'value'     => '',
					'class'     => 'small-text',
				);
			}

			if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Quizzes_Taxonomies', 'ld_quiz_category' ) == 'yes' ) {
				$this->shortcodes_option_fields['quiz_cat'] = array(
					'id'        => $this->shortcodes_section_key . '_quiz_cat',
					'name'      => 'quiz_cat',
					'type'      => 'number',
					// translators: placeholder: Quiz.
					'label'     => sprintf( esc_html_x( '%s Category ID', 'placeholder: Quiz', 'ebox' ), ebox_Custom_Label::get_label( 'quiz' ) ),
					// translators: placeholder: quizzes.
					'help_text' => sprintf( esc_html_x( 'shows %s with mentioned category id.', 'placeholder: quizzes', 'ebox' ), ebox_get_custom_label_lower( 'quizzes' ) ),
					'value'     => '',
					'class'     => 'small-text',
				);

				$this->shortcodes_option_fields['quiz_category_name'] = array(
					'id'        => $this->shortcodes_section_key . '_quiz_category_name',
					'name'      => 'quiz_category_name',
					'type'      => 'text',
					// translators: placeholder: Quiz.
					'label'     => sprintf( esc_html_x( '%s Category Slug', 'placeholder: Quiz', 'ebox' ), ebox_Custom_Label::get_label( 'quiz' ) ),
					// translators: placeholder: quizzes.
					'help_text' => sprintf( esc_html_x( 'shows %s with mentioned category slug.', 'placeholder: quizzes', 'ebox' ), ebox_get_custom_label_lower( 'quizzes' ) ),
					'value'     => '',
				);

				$this->shortcodes_option_fields['quiz_categoryselector'] = array(
					'id'        => $this->shortcodes_section_key . '_quiz_categoryselector',
					'name'      => 'quiz_categoryselector',
					'type'      => 'checkbox',
					// translators: placeholder: Quiz.
					'label'     => sprintf( esc_html_x( '%s Category Selector', 'placeholder: Quiz', 'ebox' ), ebox_Custom_Label::get_label( 'quiz' ) ),
					// translators: placeholder: quiz.
					'help_text' => sprintf( esc_html_x( 'shows a %s category dropdown.', 'placeholder: quiz', 'ebox' ), ebox_get_custom_label_lower( 'quiz' ) ),
					'value'     => '',
					'options'   => array(
						'true' => esc_html__( 'Yes', 'ebox' ),
					),
				);
			}

			if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Quizzes_Taxonomies', 'ld_quiz_tag' ) == 'yes' ) {
				$this->shortcodes_option_fields['quiz_tag_id'] = array(
					'id'        => $this->shortcodes_section_key . '_quiz_tag_id',
					'name'      => 'quiz_tag_id',
					'type'      => 'number',
					// translators: placeholder: Quiz.
					'label'     => sprintf( esc_html_x( '%s Tag ID', 'placeholder: Quizzes', 'ebox' ), ebox_Custom_Label::get_label( 'quiz' ) ),
					// translators: placeholder: quizzes.
					'help_text' => sprintf( esc_html_x( 'shows %s with mentioned tag id.', 'placeholder: quizzes', 'ebox' ), ebox_get_custom_label_lower( 'quizzes' ) ),
					'value'     => '',
					'class'     => 'small-text',
				);

				$this->shortcodes_option_fields['quiz_tag'] = array(
					'id'        => $this->shortcodes_section_key . '_quiz_tag',
					'name'      => 'quiz_tag',
					'type'      => 'text',
					// translators: placeholder: Quiz.
					'label'     => sprintf( esc_html_x( '%s Tag Slug', 'placeholder: Quiz', 'ebox' ), ebox_Custom_Label::get_label( 'quiz' ) ),
					// translators: placeholder: quizzes.
					'help_text' => sprintf( esc_html_x( 'shows %s with mentioned tag slug.', 'placeholder: quizzes', 'ebox' ), ebox_get_custom_label_lower( 'quizzes' ) ),
					'value'     => '',
				);
			}

			if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Quizzes_Taxonomies', 'wp_post_category' ) == 'yes' ) {
				$this->shortcodes_option_fields['cat'] = array(
					'id'        => $this->shortcodes_section_key . '_cat',
					'name'      => 'cat',
					'type'      => 'number',
					'label'     => esc_html__( 'WP Category ID', 'ebox' ),
					// translators: placeholder: quizzes.
					'help_text' => sprintf( esc_html_x( 'shows %s with mentioned WP category id.', 'placeholder: quizzes', 'ebox' ), ebox_get_custom_label_lower( 'quizzes' ) ),
					'value'     => '',
					'class'     => 'small-text',
				);

				$this->shortcodes_option_fields['category_name'] = array(
					'id'        => $this->shortcodes_section_key . '_category_name',
					'name'      => 'category_name',
					'type'      => 'text',
					'label'     => esc_html__( 'WP Category Slug', 'ebox' ),
					// translators: placeholder: quizzes.
					'help_text' => sprintf( esc_html_x( 'shows %s with mentioned WP category slug.', 'placeholder: quizzes', 'ebox' ), ebox_get_custom_label_lower( 'quizzes' ) ),
					'value'     => '',
				);

				$this->shortcodes_option_fields['categoryselector'] = array(
					'id'        => $this->shortcodes_section_key . '_categoryselector',
					'name'      => 'categoryselector',
					'type'      => 'checkbox',
					'label'     => esc_html__( 'WP Category Selector', 'ebox' ),
					'help_text' => esc_html__( 'shows a WP category dropdown.', 'ebox' ),
					'value'     => '',
					'options'   => array(
						'true' => esc_html__( 'Yes', 'ebox' ),
					),
				);
			}

			if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Quizzes_Taxonomies', 'wp_post_tag' ) == 'yes' ) {
				$this->shortcodes_option_fields['tag'] = array(
					'id'        => $this->shortcodes_section_key . '_tag',
					'name'      => 'tag',
					'type'      => 'text',
					'label'     => esc_html__( 'WP Tag Slug', 'ebox' ),
					// translators: placeholder: quizzes.
					'help_text' => sprintf( esc_html_x( 'shows %s with mentioned WP tag slug.', 'placeholder: quizzes', 'ebox' ), ebox_get_custom_label_lower( 'quizzes' ) ),
					'value'     => '',
				);

				$this->shortcodes_option_fields['tag_id'] = array(
					'id'        => $this->shortcodes_section_key . '_tag_id',
					'name'      => 'tag_id',
					'type'      => 'number',
					'label'     => esc_html__( 'WP Tag ID', 'ebox' ),
					// translators: placeholder: quizzes.
					'help_text' => sprintf( esc_html_x( 'shows %s with mentioned WP tag id.', 'placeholder: quizzes', 'ebox' ), ebox_get_custom_label_lower( 'quizzes' ) ),
					'value'     => '',
					'class'     => 'small-text',
				);
			}

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
			$this->shortcodes_option_fields = apply_filters( 'ebox_settings_fields', $this->shortcodes_option_fields, $this->shortcodes_section_key );

			parent::init_shortcodes_section_fields();
		}
	}
}
