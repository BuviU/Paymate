<?php
/**
 * ebox modules (ebox-modules) Posts Listing.
 *
 * @since 3.0.0
 * @package ebox\Lesson\Listing
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Admin_Posts_Listing' ) ) && ( ! class_exists( 'ebox_Admin_modules_Listing' ) ) ) {

	/**
	 * Class ebox modules (ebox-modules) Posts Listing.
	 *
	 * @since 3.0.0
	 * @uses ebox_Admin_Posts_Listing
	 */
	class ebox_Admin_modules_Listing extends ebox_Admin_Posts_Listing {

		/**
		 * Public constructor for class
		 *
		 * @since 3.0.0
		 */
		public function __construct() {
			$this->post_type = ebox_get_post_type_slug( 'lesson' );

			parent::__construct();
		}

		/**
		 * Called via the WordPress init action hook.
		 *
		 * @since 3.0.0
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
			);

			if ( 'yes' === ebox_Settings_Section::get_section_setting( 'ebox_Settings_Courses_Builder', 'shared_steps' ) ) {
				unset( $this->columns['course'] );
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

		// End of functions.
	}
}
new ebox_Admin_modules_Listing();
