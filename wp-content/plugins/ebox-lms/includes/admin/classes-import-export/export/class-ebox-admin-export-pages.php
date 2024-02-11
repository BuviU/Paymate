<?php
/**
 * ebox Admin Export Pages.
 *
 * @since 4.3.0
 *
 * @package ebox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (
	class_exists( 'ebox_Admin_Export_Posts' ) &&
	trait_exists( 'ebox_Admin_Import_Export_Pages' ) &&
	! class_exists( 'ebox_Admin_Export_Pages' )
) {
	/**
	 * Class ebox Admin Export Pages.
	 *
	 * @since 4.3.0
	 */
	class ebox_Admin_Export_Pages extends ebox_Admin_Export_Posts {
		use ebox_Admin_Import_Export_Pages;

		/**
		 * Constructor.
		 *
		 * @since 4.3.0
		 * @since 4.5.0   Changed the $logger param to the `ebox_Import_Export_Logger` class.
		 *
		 * @param ebox_Admin_Export_File_Handler $file_handler File Handler class instance.
		 * @param ebox_Import_Export_Logger      $logger       Logger class instance.
		 *
		 * @return void
		 */
		public function __construct(
			ebox_Admin_Export_File_Handler $file_handler,
			ebox_Import_Export_Logger $logger
		) {
			$this->additional_query_args = array(
				'include' => $this->map_page_ids(),
			);

			parent::__construct( 'page', $file_handler, $logger );
		}

		/**
		 * Returns the array of page ids to export.
		 *
		 * @since 4.3.0
		 *
		 * @return array
		 */
		protected function map_page_ids(): array {
			$ids = array(
				ebox_Settings_Section::get_section_setting(
					'ebox_Settings_Section_Registration_Pages',
					'registration'
				),
				ebox_Settings_Section::get_section_setting(
					'ebox_Settings_Section_Registration_Pages',
					'registration_success'
				),
			);

			$ids = array_values(
				array_filter(
					wp_parse_id_list( $ids )
				)
			);

			/**
			 * Filters page ids to be exported.
			 *
			 * @since 4.3.0
			 *
			 * @param array $ids The page ids.
			 *
			 * @return array The page ids.
			 */
			return apply_filters( 'ebox_export_page_ids', $ids );
		}
	}
}
