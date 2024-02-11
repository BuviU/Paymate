<?php
/**
 * Class to extend LDLMS_Model_Post to LDLMS_Model_Team.
 *
 * @since 3.4.0
 * @package ebox\Team
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'LDLMS_Model_Post' ) ) && ( ! class_exists( 'LDLMS_Model_Team' ) ) ) {
	/**
	 * Class for ebox Model Team.
	 *
	 * @since 3.4.0
	 * @uses LDLMS_Model_Post
	 */
	class LDLMS_Model_Team extends LDLMS_Model_Post {

		/**
		 * Initialize post.
		 *
		 * @since 3.4.0
		 *
		 * @param int $team_id Team Post ID to load.
		 */
		public function __construct( $team_id = 0 ) {
			$this->post_type = ebox_get_post_type_slug( 'team' );

			$this->load( $team_id );
		}

		/**
		 * Load team
		 *
		 * @param int $team_id Team ID.
		 */
		public function load( $team_id ) {}

		// End of functions.
	}
}
