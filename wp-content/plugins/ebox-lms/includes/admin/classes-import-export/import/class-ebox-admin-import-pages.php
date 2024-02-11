<?php
/**
 * ebox Admin Import Pages.
 *
 * @since 4.3.0
 *
 * @package ebox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (
	class_exists( 'ebox_Admin_Import_Posts' ) &&
	trait_exists( 'ebox_Admin_Import_Export_Pages' ) &&
	! class_exists( 'ebox_Admin_Import_Pages' )
) {
	/**
	 * Class ebox Admin Import Pages.
	 *
	 * @since 4.3.0
	 */
	class ebox_Admin_Import_Pages extends ebox_Admin_Import_Posts {
		use ebox_Admin_Import_Export_Pages;

		/**
		 * Constructor.
		 *
		 * @since 4.3.0
		 * @since 4.5.0   Changed the $logger param to the `ebox_Import_Export_Logger` class.
		 *
		 * @param int                                 $user_id      User ID. All posts are attached to this user.
		 * @param string                              $home_url     The previous home url.
		 * @param ebox_Admin_Import_File_Handler $file_handler File Handler class instance.
		 * @param ebox_Import_Export_Logger      $logger       Logger class instance.
		 *
		 * @return void
		 */
		public function __construct(
			int $user_id,
			string $home_url,
			ebox_Admin_Import_File_Handler $file_handler,
			ebox_Import_Export_Logger $logger
		) {
			parent::__construct( 'page', $user_id, $home_url, $file_handler, $logger );
		}
	}
}
