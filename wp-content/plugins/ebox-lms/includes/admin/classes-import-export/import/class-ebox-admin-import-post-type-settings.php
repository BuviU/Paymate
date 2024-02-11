<?php
/**
 * ebox Admin Import Post Type Settings.
 *
 * @since 4.3.0
 *
 * @package ebox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (
	class_exists( 'ebox_Admin_Import' ) &&
	trait_exists( 'ebox_Admin_Import_Export_Post_Type_Settings' ) &&
	! class_exists( 'ebox_Admin_Import_Post_Type_Settings' )
) {
	/**
	 * Class ebox Admin Import Post Type Settings.
	 *
	 * @since 4.3.0
	 */
	class ebox_Admin_Import_Post_Type_Settings extends ebox_Admin_Import {
		use ebox_Admin_Import_Export_Post_Type_Settings;

		/**
		 * Constructor.
		 *
		 * @since 4.3.0
		 * @since 4.5.0   Changed the $logger param to the `ebox_Import_Export_Logger` class.
		 *
		 * @param string                              $post_type    Post Type.
		 * @param string                              $home_url     The previous home url.
		 * @param ebox_Admin_Import_File_Handler $file_handler File Handler class instance.
		 * @param ebox_Import_Export_Logger      $logger       Logger class instance.
		 *
		 * @return void
		 */
		public function __construct(
			string $post_type,
			string $home_url,
			ebox_Admin_Import_File_Handler $file_handler,
			ebox_Import_Export_Logger $logger
		) {
			$this->post_type = $post_type;

			parent::__construct( $home_url, $file_handler, $logger );
		}

		/**
		 * Saves post type settings.
		 *
		 * @since 4.3.0
		 *
		 * @return void
		 */
		protected function import(): void {
			$sections = $this->load_and_decode_file();

			if ( empty( $sections ) ) {
				return;
			}

			foreach ( $sections as $section ) {
				ebox_Settings_Section::set_section_settings_all(
					$section['name'],
					$section['fields']
				);

				$this->processed_items_count++;
				$this->imported_items_count++;
			}
		}
	}
}
