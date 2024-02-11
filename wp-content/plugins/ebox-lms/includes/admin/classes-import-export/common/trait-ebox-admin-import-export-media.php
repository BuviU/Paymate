<?php
/**
 * ebox Admin Import/Export Media.
 *
 * @since 4.3.0
 *
 * @package ebox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! trait_exists( 'ebox_Admin_Import_Export_Media' ) ) {
	/**
	 * Trait ebox Admin Import/Export Settings.
	 *
	 * @since 4.3.0
	 */
	trait ebox_Admin_Import_Export_Media {
		/**
		 * Returns the file name.
		 *
		 * @since 4.3.0
		 *
		 * @return string The file name.
		 */
		protected function get_file_name(): string {
			return 'media';
		}
	}
}
