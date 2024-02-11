<?php
/**
 * ebox Binary Selector User Courses.
 *
 * @since 2.2.1
 * @package ebox\Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( ! class_exists( 'ebox_Binary_Selector_User_Courses' ) ) && ( class_exists( 'ebox_Binary_Selector_Posts' ) ) ) {

	/**
	 * Class ebox Binary Selector User Courses.
	 *
	 * @since 2.2.1
	 * @uses ebox_Binary_Selector_Posts
	 */
	class ebox_Binary_Selector_User_Courses extends ebox_Binary_Selector_Posts {

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
				'user_id'            => 0,
				'post_type'          => 'ebox-courses',
				'html_title'         => '<h3>' . sprintf(
					// translators: placeholder: Courses.
					esc_html_x( 'User Enrolled in %s', 'User Enrolled in Courses Label', 'ebox' ),
					ebox_Custom_Label::get_label( 'courses' )
				) . '</h3>',
				'html_id'            => 'ebox_user_courses',
				'html_class'         => 'ebox_user_courses',
				'html_name'          => 'ebox_user_courses',
				'search_label_left'  => sprintf(
					// translators: placeholder: Courses.
					esc_html_x( 'Search All %s', 'Search All Courses Label', 'ebox' ),
					ebox_Custom_Label::get_label( 'courses' )
				),
				'search_label_right' => sprintf(
					// translators: placeholder: Courses.
					esc_html_x( 'Search Enrolled %s', 'Search Enrolled Courses Label', 'ebox' ),
					ebox_Custom_Label::get_label( 'courses' )
				),
			);

			$args = wp_parse_args( $args, $defaults );

			$args['html_id']   = $args['html_id'] . '-' . $args['user_id'];
			$args['html_name'] = $args['html_name'] . '[' . $args['user_id'] . ']';

			parent::__construct( $args );
		}
	}
}
