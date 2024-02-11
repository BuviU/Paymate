<?php
/**
 * ebox Binary Selector Course Leaders.
 *
 * @since 2.2.1
 * @package ebox\Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( ! class_exists( 'ebox_Binary_Selector_Team_Leaders' ) ) && ( class_exists( 'ebox_Binary_Selector_Users' ) ) ) {

	/**
	 * Class ebox Binary Selector Course Leaders.
	 *
	 * @since 2.2.1
	 * @uses ebox_Binary_Selector_Users
	 */
	class ebox_Binary_Selector_Team_Leaders extends ebox_Binary_Selector_Users {
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
				'team_id'           => 0,
				'html_title'         => '<h3>' . sprintf(
					// translators: placeholder: Team.
					esc_html_x( '%s Leaders', 'placeholder: Team', 'ebox' ),
					ebox_Custom_Label::get_label( 'team' )
				) . '</h3>',
				'html_id'            => 'ebox_team_leaders',
				'html_class'         => 'ebox_team_leaders',
				'html_name'          => 'ebox_team_leaders',
				'search_label_left'  => sprintf(
					// translators: placeholder: Team.
					esc_html_x( 'Search All %s Leaders', 'placeholder: Team', 'ebox' ),
					ebox_Custom_Label::get_label( 'team' )
				),
				'search_label_right' => sprintf(
					// translators: placeholder: Team.
					esc_html_x( 'Search Assigned %s Leaders', 'placeholder: Team', 'ebox' ),
					ebox_Custom_Label::get_label( 'team' )
				),
			);

			$args = wp_parse_args( $args, $defaults );

			$args['html_id']   = $args['html_id'] . '-' . $args['team_id'];
			$args['html_name'] = $args['html_name'] . '[' . $args['team_id'] . ']';

			if ( ( ! isset( $args['included_ids'] ) ) || ( empty( $args['included_ids'] ) ) ) {
				$args['role__in'] = array( 'team_leader', 'administrator' );
			}

			parent::__construct( $args );
		}
	}
}
