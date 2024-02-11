<?php
/**
 * ebox Admin Import/Export Pages.
 *
 * @since 4.3.0
 *
 * @package ebox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! trait_exists( 'ebox_Admin_Import_Export_Pages' ) ) {
	/**
	 * Trait ebox Admin Import/Export Pages.
	 *
	 * @since 4.3.0
	 */
	trait ebox_Admin_Import_Export_Pages {
		/**
		 * Returns the file name.
		 *
		 * @since 4.3.0
		 *
		 * @return string The file name.
		 */
		protected function get_file_name(): string {
			return 'page';
		}
	}
}
