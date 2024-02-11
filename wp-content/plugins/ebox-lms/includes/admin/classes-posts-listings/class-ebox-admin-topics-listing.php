<?php
/**
 * ebox Topics (ebox-topic) Posts Listing.
 *
 * @since 3.0.0
 * @package ebox\Topic\Listing
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Admin_Posts_Listing' ) ) && ( ! class_exists( 'ebox_Admin_Topics_Listing' ) ) ) {

	/**
	 * Class ebox Topics (ebox-topic) Posts Listing.
	 *
	 * @since 3.0.0
	 * @uses ebox_Admin_Posts_Listing
	 */
	class ebox_Admin_Topics_Listing extends ebox_Admin_Posts_Listing {

		/**
		 * Public constructor for class
		 *
		 * @since 3.0.0
		 */
		public function __construct() {
			$this->post_type = ebox_get_post_type_slug( 'topic' );

			parent::__construct();
		}

		/**
		 * Called via the WordPress init action hook.
		 *
		 * @since 3.2.3
		 */
		public function listing_init() {
			if ( $this->listing_init_done ) {
				return;
			}

			$this->selectors = array(
				'course_id' => array(
					'type'                     => 'post_type',
					'post_type'                => ebox_get_post_type_slug( 'course' ),
					'show_all_value'           => '',
					'show_all_label'           => sprintf(
						// translators: placeholder: Courses.
						esc_html_x( 'All %s', 'placeholder: Courses', 'ebox' ),
						ebox_Custom_Label::get_label( 'courses' )
					),
					'show_empty_value'         => 'empty',
					'show_empty_label'         => sprintf(
						// translators: placeholder: Course.
						esc_html_x( '-- No %s --', 'placeholder: Course', 'ebox' ),
						ebox_Custom_Label::get_label( 'course' )
					),
					'listing_query_function'   => array( $this, 'listing_filter_by_course' ),
					'selector_filter_function' => array( $this, 'selector_filter_for_course' ),
					'selector_value_function'  => array( $this, 'selector_value_for_course' ),
				),
				'lesson_id' => array(
					'type'                     => 'post_type',
					'post_type'                => ebox_get_post_type_slug( 'lesson' ),
					'show_all_value'           => '',
					'show_all_label'           => sprintf(
						// translators: placeholder: modules.
						esc_html_x( 'All %s', 'placeholder: modules', 'ebox' ),
						ebox_Custom_Label::get_label( 'modules' )
					),
					'show_empty_value'         => 'empty',
					'show_empty_label'         => sprintf(
						// translators: placeholder: Lesson.
						esc_html_x( '-- No %s --', 'placeholder: Lesson', 'ebox' ),
						ebox_Custom_Label::get_label( 'lesson' )
					),
					'listing_query_function'   => array( $this, 'listing_filter_by_lesson' ),
					'selector_filters'         => array( 'course_id' ),
					'selector_filter_function' => array( $this, 'selector_filter_for_lesson' ),
					'selector_value_function'  => array( $this, 'selector_value_integer' ),
				),
			);

			$this->columns = array(
				'course' => array(
					'label'    => sprintf(
						// translators: placeholder: Course.
						esc_html_x( 'Assigned %s', 'placeholder: Course', 'ebox' ),
						ebox_Custom_Label::get_label( 'course' )
					),
					'after'    => 'title',
					'display'  => array( $this, 'show_column_step_course' ),
					'required' => true,
				),
				'lesson' => array(
					'label'    => sprintf(
						// translators: placeholder: Lesson.
						esc_html_x( 'Assigned %s', 'placeholder: Lesson', 'ebox' ),
						ebox_Custom_Label::get_label( 'lesson' )
					),
					'after'    => 'course',
					'display'  => array( $this, 'show_column_step_lesson' ),
					'required' => true,
				),
			);

			if ( 'yes' === ebox_Settings_Section::get_section_setting( 'ebox_Settings_Courses_Builder', 'shared_steps' ) ) {
				unset( $this->columns['course'] );
				unset( $this->columns['lesson'] );
				unset( $this->selectors['lesson_id']['show_empty_value'] );
				unset( $this->selectors['lesson_id']['show_empty_label'] );
			}

			// If Team Leader remove the selector empty option.
			if ( ebox_is_team_leader_user() ) {
				$gl_manage_courses_capabilities = ebox_get_team_leader_manage_courses();
				if ( 'advanced' !== $gl_manage_courses_capabilities ) {
					unset( $this->selectors['course_id']['show_empty_value'] );
					unset( $this->selectors['course_id']['show_empty_label'] );
				}
			}

			parent::listing_init();

			$this->listing_init_done = true;
		}

		/**
		 * Call via the WordPress load sequence for admin pages.
		 *
		 * @since 3.2.3
		 */
		public function on_load_listing() {
			if ( $this->post_type_check() ) {
				parent::on_load_listing();
			}
		}

		/**
		 * Filter the Topics modules selector filters.
		 *
		 * @since 3.2.3
		 *
		 * @param array  $query_args Query Args for Selector.
		 * @param string $post_type  Post Type slug for selector.
		 */
		public function filter_course_modules_selector( $query_args = array(), $post_type = '' ) {
			global $ebox_lms;

			// Check that the selector post type matches for out listing post type.
			if ( $post_type === $this->post_type ) {
				if ( isset( $query_args['post_type'] ) ) {
					if ( ( ( is_string( $query_args['post_type'] ) ) && ( ebox_get_post_type_slug( 'lesson' ) === $query_args['post_type'] ) ) || ( ( is_array( $query_args['post_type'] ) ) && ( in_array( ebox_get_post_type_slug( 'lesson' ), $query_args['post_type'], true ) ) ) ) {
						$course_selector = $this->get_selector( 'course_id' );
						if ( ( $course_selector ) && ( isset( $course_selector['selected'] ) ) && ( ! empty( $course_selector['selected'] ) ) ) {
							$modules_items = $ebox_lms->select_a_lesson_or_topic( absint( $course_selector['selected'] ), false, false );
							if ( ! empty( $modules_items ) ) {
								$query_args['post__in'] = array_keys( $modules_items );
								$query_args['orderby']  = 'post__in';
							} else {
								$query_args['post__in'] = array( 0 );
							}
						}
					}
				}
			}

			return $query_args;
		}

		// End of functions.
	}
}
new ebox_Admin_Topics_Listing();
