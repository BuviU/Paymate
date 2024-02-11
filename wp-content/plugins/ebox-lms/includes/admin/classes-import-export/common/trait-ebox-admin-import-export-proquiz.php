<?php
/**
 * ebox Admin Import/Export Pro Quiz.
 *
 * @since 4.3.0
 *
 * @package ebox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! trait_exists( 'ebox_Admin_Import_Export_Proquiz' ) ) {
	/**
	 * Trait ebox Admin Import/Export Pro Quiz.
	 *
	 * @since 4.3.0
	 */
	trait ebox_Admin_Import_Export_Proquiz {
		/**
		 * Returns the file name.
		 *
		 * @since 4.3.0
		 *
		 * @return string The file name.
		 */
		protected function get_file_name(): string {
			return 'proquiz';
		}
	}
}
