<?php
/**
 * ebox Data Upgrades for Team Leader Role.
 *
 * @since 2.5.6
 * @package ebox\Data_Upgrades
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Admin_Data_Upgrades' ) ) && ( ! class_exists( 'ebox_Admin_Data_Upgrades_Team_Leader_Role' ) ) ) {

	/**
	 * Class ebox Data Upgrades for Team Leader Role.
	 *
	 * @since 2.5.6
	 * @uses ebox_Admin_Data_Upgrades
	 */
	class ebox_Admin_Data_Upgrades_Team_Leader_Role extends ebox_Admin_Data_Upgrades {

		/**
		 * Protected constructor for class
		 *
		 * @since 2.5.6
		 */
		protected function __construct() {
			$this->data_slug = 'team-leader-role';
			parent::__construct();
			add_action( 'init', array( $this, 'create_team_leader_role' ) );
			parent::register_upgrade_action();
		}

		/**
		 * Create Team Leader Role
		 * Checks to see if settings needs to be updated.
		 *
		 * @since 2.5.6
		 */
		public function create_team_leader_role() {

			if ( is_admin() ) {
				$gl_role_created = $this->get_data_settings( 'gl_role_created' );
				if ( ( defined( 'ebox_ACTIVATED' ) && ebox_ACTIVATED ) || ( ! $gl_role_created ) ) {

					ebox_add_team_admin_role();

					$this->set_data_settings( 'gl_role_created', time() );
				}
			}
		}

		// End of functions.
	}
}

add_action(
	'ebox_data_upgrades_init',
	function() {
		ebox_Admin_Data_Upgrades_Team_Leader_Role::add_instance();
	}
);
