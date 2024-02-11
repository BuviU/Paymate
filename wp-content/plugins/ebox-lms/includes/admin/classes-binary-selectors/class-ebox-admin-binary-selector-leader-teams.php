<?php
/**
 * ebox Binary Selector Leader Teams.
 *
 * @since 2.2.1
 * @package ebox\Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( ! class_exists( 'ebox_Binary_Selector_Leader_Teams' ) ) && ( class_exists( 'ebox_Binary_Selector_Posts' ) ) ) {
	/**
	 * Class ebox Binary Selector Leader Teams.
	 *
	 * @since 2.2.1
	 * @uses ebox_Binary_Selector_Posts
	 */
	class ebox_Binary_Selector_Leader_Teams extends ebox_Binary_Selector_Posts {

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
				'post_type'          => 'teams',
				'html_title'         => '<h3>' . sprintf(
					// translators: Teams.
					esc_html_x( 'Leader of %s', 'placeholder: Teams', 'ebox' ),
					ebox_Custom_Label::get_label( 'teams' )
				) . '</h3>',
				'html_id'            => 'ebox_leader_teams',
				'html_class'         => 'ebox_leader_teams',
				'html_name'          => 'ebox_leader_teams',
				'search_label_left'  => sprintf(
					// translators: Teams.
					esc_html_x( 'Search All %s', 'placeholder: Teams', 'ebox' ),
					ebox_Custom_Label::get_label( 'teams' )
				),
				'search_label_right' => sprintf(
					// translators: Teams.
					esc_html_x( 'Search Leader %s', 'placeholder: Teams', 'ebox' ),
					ebox_Custom_Label::get_label( 'teams' )
				),
			);

			$args = wp_parse_args( $args, $defaults );

			$args['html_id']   = $args['html_id'] . '-' . $args['user_id'];
			$args['html_name'] = $args['html_name'] . '[' . $args['user_id'] . ']';

			parent::__construct( $args );
		}
	}
}
