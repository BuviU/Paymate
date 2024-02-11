<?php
/**
 * ebox Settings Page Courses Options.
 *
 * @since 2.4.0
 * @package ebox\Settings\Pages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Page' ) ) && ( ! class_exists( 'ebox_Settings_Page_Courses_Options' ) ) ) {
	/**
	 * Class ebox Settings Page Courses Options.
	 *
	 * @since 2.4.0
	 */
	class ebox_Settings_Page_Courses_Options extends ebox_Settings_Page {

		/**
		 * Public constructor for class
		 *
		 * @since 2.4.0
		 */
		public function __construct() {

			$this->parent_menu_page_url = 'edit.php?post_type=ebox-courses';
			$this->menu_page_capability = ebox_ADMIN_CAPABILITY_CHECK;
			$this->settings_page_id     = 'courses-options';
			$this->settings_page_title  = esc_html_x( 'Settings', 'Course Settings', 'ebox' );
			$this->settings_tab_title   = $this->settings_page_title;

			parent::__construct();
		}
	}
}
add_action(
	'ebox_settings_pages_init',
	function() {
		ebox_Settings_Page_Courses_Options::add_page_instance();
	}
);
