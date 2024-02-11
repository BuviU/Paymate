<?php
/**
 * ebox Shortcode Section for modules List [ld_lesson_list].
 *
 * @since 2.4.0
 * @package ebox\Settings\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Shortcodes_Section' ) ) && ( ! class_exists( 'ebox_Shortcodes_Section_ld_lesson_list' ) ) ) {
	/**
	 * Class ebox Shortcode Section for modules List [ld_lesson_list].
	 *
	 * @since 2.4.0
	 */
	class ebox_Shortcodes_Section_ld_lesson_list extends ebox_Shortcodes_Section /* phpcs:ignore PEAR.NamingConventions.ValidClassName.Invalid */ {

		/**
		 * Public constructor for class.
		 *
		 * @since 2.4.0
		 *
		 * @param array $fields_args Field Args.
		 */
		public function __construct( $fields_args = array() ) {
			$this->fields_args = $fields_args;

			$this->shortcodes_section_key = 'ld_lesson_list';
			// translators: placeholder: Lesson.
			$this->shortcodes_section_title = sprintf( esc_html_x( '%s List', 'placeholder: Lesson', 'ebox' ), ebox_Custom_Label::get_label( 'lesson' ) );
			$this->shortcodes_section_type  = 1;
			// translators: placeholders: modules, modules (URL slug).
			$this->shortcodes_section_description = sprintf( wp_kses_post( _x( 'This shortcode shows list of %1$s. You can use this shortcode on any page if you do not want to use the default <code>/%2$s/</code> page.', 'placeholders: modules, modules (URL slug)', 'ebox' ) ), ebox_get_custom_label_lower( 'modules' ), ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_Permalinks', 'modules' ) );
			parent::__construct();
		}

		/**
		 * Initialize the shortcode fields.
		 *
		 * @since 2.4.0
		 */
		public function init_shortcodes_section_fields() {
			$this->shortcodes_option_fields = array(

				'course_id'      => array(
					'id'        => $this->shortcodes_section_key . '_course_id',
					'name'      => 'course_id',
					'type'      => 'number',
					// translators: placeholder: Course.
					'label'     => sprintf( esc_html_x( '%s ID', 'placeholder: Course', 'ebox' ), ebox_Custom_Label::get_label( 'course' ) ),
					// translators: placeholders: Course, Courses.
					'help_text' => sprintf( esc_html_x( 'Enter single %1$s ID. Leave blank for all %2$s.', 'placeholders: Course, Courses', 'ebox' ), ebox_Custom_Label::get_label( 'course' ), ebox_Custom_Label::get_label( 'courses' ) ),
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
					// translators: placeholder: modules.
					'label'     => sprintf( esc_html_x( '%s Per Page', 'placeholder: modules', 'ebox' ), ebox_Custom_Label::get_label( 'modules' ) ),
					// translators: placeholders: modules, default per page.
					'help_text' => sprintf( esc_html_x( '%1$s per page. Default is %2$d. Set to zero for all.', 'placeholders: modules, default per page', 'ebox' ), ebox_Custom_Label::get_label( 'modules' ), ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_General_Per_Page', 'per_page' ) ),
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
					// translators: placeholder: Lesson.
					'label'     => sprintf( esc_html_x( 'Show %s Content', 'placeholder: Lesson', 'ebox' ), ebox_Custom_Label::get_label( 'lesson' ) ),
					// translators: placeholder: lesson.
					'help_text' => sprintf( esc_html_x( 'show %s content.', 'placeholders: lesson', 'ebox' ), ebox_get_custom_label_lower( 'lesson' ) ),
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
					// translators: placeholder: Lesson.
					'label'     => sprintf( esc_html_x( 'Show %s Thumbnail', 'placeholder: Lesson', 'ebox' ), ebox_Custom_Label::get_label( 'lesson' ) ),
					// translators: placeholder: lesson.
					'help_text' => sprintf( esc_html_x( 'shows a %s thumbnail.', 'placeholders: lesson', 'ebox' ), ebox_get_custom_label_lower( 'lesson' ) ),
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

			if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_modules_Taxonomies', 'ld_lesson_category' ) == 'yes' ) {
				$this->shortcodes_option_fields['lesson_cat'] = array(
					'id'        => $this->shortcodes_section_key . '_lesson_cat',
					'name'      => 'lesson_cat',
					'type'      => 'number',
					// translators: placeholder: Lesson.
					'label'     => sprintf( esc_html_x( '%s Category ID', 'placeholder: Lesson', 'ebox' ), ebox_Custom_Label::get_label( 'lesson' ) ),
					// translators: placeholder: modules.
					'help_text' => sprintf( esc_html_x( 'shows %s with mentioned category id.', 'placeholder: modules', 'ebox' ), ebox_get_custom_label_lower( 'modules' ) ),
					'value'     => '',
					'class'     => 'small-text',
				);

				$this->shortcodes_option_fields['lesson_category_name'] = array(
					'id'        => $this->shortcodes_section_key . '_lesson_category_name',
					'name'      => 'lesson_category_name',
					'type'      => 'text',
					// translators: placeholder: Lesson.
					'label'     => sprintf( esc_html_x( '%s Category Slug', 'placeholder: Lesson', 'ebox' ), ebox_Custom_Label::get_label( 'lesson' ) ),
					// translators: placeholder: modules.
					'help_text' => sprintf( esc_html_x( 'shows %s with mentioned category slug.', 'placeholders: modules', 'ebox' ), ebox_get_custom_label_lower( 'modules' ) ),
					'value'     => '',
				);

				$this->shortcodes_option_fields['lesson_categoryselector'] = array(
					'id'        => $this->shortcodes_section_key . '_lesson_categoryselector',
					'name'      => 'lesson_categoryselector',
					'type'      => 'checkbox',
					// translators: placeholder: Lesson.
					'label'     => sprintf( esc_html_x( '%s Category Selector', 'placeholder: Lesson', 'ebox' ), ebox_Custom_Label::get_label( 'lesson' ) ),
					// translators: placeholder: lesson.
					'help_text' => sprintf( esc_html_x( 'shows a %s category dropdown.', 'placeholder: lesson', 'ebox' ), ebox_get_custom_label_lower( 'lesson' ) ),
					'value'     => '',
					'options'   => array(
						'true' => esc_html__( 'Yes', 'ebox' ),
					),
				);
			}

			if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_modules_Taxonomies', 'ld_lesson_tag' ) == 'yes' ) {
				$this->shortcodes_option_fields['lesson_tag_id'] = array(
					'id'        => $this->shortcodes_section_key . '_lesson_tag_id',
					'name'      => 'lesson_tag_id',
					'type'      => 'number',
					// translators: placeholder: Lesson.
					'label'     => sprintf( esc_html_x( '%s Tag ID', 'placeholder: Lesson', 'ebox' ), ebox_Custom_Label::get_label( 'Lesson' ) ),
					// translators: placeholder: modules.
					'help_text' => sprintf( esc_html_x( 'shows %s with mentioned tag id.', 'placeholders: modules', 'ebox' ), ebox_get_custom_label_lower( 'modules' ) ),
					'value'     => '',
					'class'     => 'small-text',
				);

				$this->shortcodes_option_fields['lesson_tag'] = array(
					'id'        => $this->shortcodes_section_key . '_lesson_tag',
					'name'      => 'lesson_tag',
					'type'      => 'text',
					// translators: placeholder: Lesson.
					'label'     => sprintf( esc_html_x( '%s Tag Slug', 'placeholder: Lesson', 'ebox' ), ebox_Custom_Label::get_label( 'lesson' ) ),
					// translators: placeholder: modules.
					'help_text' => sprintf( esc_html_x( 'shows %s with mentioned tag slug.', 'placeholder: modules', 'ebox' ), ebox_get_custom_label_lower( 'modules' ) ),
					'value'     => '',
				);
			}

			if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_modules_Taxonomies', 'wp_post_category' ) == 'yes' ) {

				$this->shortcodes_option_fields['cat'] = array(
					'id'        => $this->shortcodes_section_key . '_cat',
					'name'      => 'cat',
					'type'      => 'number',
					'label'     => esc_html__( 'WP Category ID', 'ebox' ),
					// translators: placeholder: modules.
					'help_text' => sprintf( esc_html_x( 'shows %s with mentioned WP category id.', 'placeholder: modules', 'ebox' ), ebox_get_custom_label_lower( 'modules' ) ),
					'value'     => '',
					'class'     => 'small-text',
				);

				$this->shortcodes_option_fields['category_name'] = array(
					'id'        => $this->shortcodes_section_key . '_category_name',
					'name'      => 'category_name',
					'type'      => 'text',
					'label'     => esc_html__( 'WP Category Slug', 'ebox' ),
					// translators: placeholder: modules.
					'help_text' => sprintf( esc_html_x( 'shows %s with mentioned WP category slug.', 'placeholder: modules', 'ebox' ), ebox_get_custom_label_lower( 'modules' ) ),
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

			if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_modules_Taxonomies', 'wp_post_tag' ) == 'yes' ) {
				$this->shortcodes_option_fields['tag'] = array(
					'id'        => $this->shortcodes_section_key . '_tag',
					'name'      => 'tag',
					'type'      => 'text',
					'label'     => esc_html__( 'WP Tag Slug', 'ebox' ),
					// translators: placeholder: modules.
					'help_text' => sprintf( esc_html_x( 'shows %s with mentioned WP tag slug.', 'placeholder: modules', 'ebox' ), ebox_get_custom_label_lower( 'modules' ) ),
					'value'     => '',
				);

				$this->shortcodes_option_fields['tag_id'] = array(
					'id'        => $this->shortcodes_section_key . '_tag_id',
					'name'      => 'tag_id',
					'type'      => 'number',
					'label'     => esc_html__( 'WP Tag ID', 'ebox' ),
					// translators: placeholder: modules.
					'help_text' => sprintf( esc_html_x( 'shows %s with mentioned WP tag id.', 'placeholder: modules', 'ebox' ), ebox_get_custom_label_lower( 'modules' ) ),
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
