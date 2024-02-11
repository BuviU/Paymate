<?php
/**
 * ebox Binary Selector Team Courses Enroll.
 *
 * @since 3.2.0
 * @package ebox\Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( ! class_exists( 'ebox_Binary_Selector_Team_Courses_Enroll' ) ) && ( class_exists( 'ebox_Binary_Selector_Posts' ) ) ) {

	/**
	 *  Class ebox Binary Selector Team Courses Enroll.
	 *
	 * @since 3.2.0
	 * @uses ebox_Binary_Selector_Posts
	 */
	class ebox_Binary_Selector_Team_Courses_Enroll extends ebox_Binary_Selector_Posts {

		/**
		 * Public constructor for class
		 *
		 * @since 3.2.0
		 *
		 * @param array $args Array of arguments for class.
		 */
		public function __construct( $args = array() ) {

			$this->selector_class = get_class( $this );

			$defaults = array(
				'team_id'           => 0,
				'post_type'          => 'ebox-courses',
				'html_title'         => '<h3>' . sprintf(
					// translators: placeholder: Team, Courses.
					esc_html_x( '%1$s %2$s Auto-enroll', 'placeholder: Team, Courses', 'ebox' ),
					ebox_Custom_Label::get_label( 'team' ),
					ebox_Custom_Label::get_label( 'courses' )
				) . '</h3>',
				'html_id'            => 'ebox_team_courses_enroll',
				'html_class'         => 'ebox_team_courses_enroll',
				'html_name'          => 'ebox_team_courses_enroll',
				'search_label_left'  => sprintf(
					// translators: placeholders: Team, Courses.
					esc_html_x( 'Search All %1$s %2$s', 'placeholders: Team, Courses', 'ebox' ),
					ebox_Custom_Label::get_label( 'team' ),
					ebox_Custom_Label::get_label( 'courses' )
				),
				'search_label_right' => sprintf(
					// translators: placeholders: Team, Courses.
					esc_html_x( 'Search Assigned %1$s %2$s', 'placeholders: Team, Courses', 'ebox' ),
					ebox_Custom_Label::get_label( 'team' ),
					ebox_Custom_Label::get_label( 'courses' )
				),
			);

			$args = wp_parse_args( $args, $defaults );

			$args['html_id']   = $args['html_id'] . '-' . $args['team_id'];
			$args['html_name'] = $args['html_name'] . '[' . $args['team_id'] . ']';

			parent::__construct( $args );
		}
	}
}
