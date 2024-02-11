<?php
/**
 * ebox Admin Import/Export User Activity.
 *
 * @since 4.3.0
 *
 * @package ebox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! trait_exists( 'ebox_Admin_Import_Export_User_Activity' ) ) {
	/**
	 * Trait ebox Admin Import/Export User Activity.
	 *
	 * @since 4.3.0
	 */
	trait ebox_Admin_Import_Export_User_Activity {
		/**
		 * Returns the file name.
		 *
		 * @since 4.3.0
		 *
		 * @return string The file name.
		 */
		protected function get_file_name(): string {
			return 'user_activity';
		}
	}
}
