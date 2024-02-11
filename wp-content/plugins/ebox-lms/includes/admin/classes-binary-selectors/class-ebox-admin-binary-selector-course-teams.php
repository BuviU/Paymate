<?php
/**
 * ebox Binary Selector Course Teams.
 *
 * @since 2.2.1
 * @package ebox\Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( ! class_exists( 'ebox_Binary_Selector_Course_Teams' ) ) && ( class_exists( 'ebox_Binary_Selector_Posts' ) ) ) {
	/**
	 *  Class ebox Binary Selector Course Teams.
	 *
	 * @since 2.2.1
	 * @uses ebox_Binary_Selector_Posts
	 */
	class ebox_Binary_Selector_Course_Teams extends ebox_Binary_Selector_Posts {

		/**
		 * Public constructor for class
		 *
		 * @since 2.2.1
		 *
		 * @param array $args Array of arguments for class.
		 */
		public function __construct( $args = array() ) {

			$this->selector_class = get_class( $this );

			$defaults = array(
				'course_id'          => 0,
				'post_type'          => 'teams',
				'html_title'         => '<h3>' . sprintf(
					// translators: placeholders: Teams, Course.
					esc_html_x( '%1$s Using %2$s', 'placeholders: Teams, Course', 'ebox' ),
					ebox_Custom_Label::get_label( 'teams' ),
					ebox_Custom_Label::get_label( 'course' )
				) . '</h3>',
				'html_id'            => 'ebox_course_teams',
				'html_class'         => 'ebox_course_teams',
				'html_name'          => 'ebox_course_teams',
				'search_label_left'  => sprintf(
					// translators: Teams.
					esc_html_x( 'Search All %s', 'placeholder: Teams', 'ebox' ),
					ebox_Custom_Label::get_label( 'teams' )
				),
				'search_label_right' => sprintf(
					// translators: placeholders: Course, Teams.
					esc_html_x( 'Search %1$s %2$s', 'placeholders: Course, Teams', 'ebox' ),
					ebox_Custom_Label::get_label( 'course' ),
					ebox_Custom_Label::get_label( 'teams' )
				),
			);

			$args = wp_parse_args( $args, $defaults );

			$args['html_id']   = $args['html_id'] . '-' . $args['course_id'];
			$args['html_name'] = $args['html_name'] . '[' . $args['course_id'] . ']';

			parent::__construct( $args );
		}
	}
}
