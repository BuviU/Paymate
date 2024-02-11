<?php
/**
 * ebox Admin Import/Export Taxonomies.
 *
 * @since 4.3.0
 *
 * @package ebox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! trait_exists( 'ebox_Admin_Import_Export_Taxonomies' ) ) {
	/**
	 * Trait ebox Admin Import/Export Taxonomies.
	 *
	 * @since 4.3.0
	 */
	trait ebox_Admin_Import_Export_Taxonomies {
		/**
		 * Returns the file name.
		 *
		 * @since 4.3.0
		 *
		 * @return string The file name.
		 */
		protected function get_file_name(): string {
			return 'taxonomies';
		}
	}
}
