<?php
/**
 * ebox Settings Page Teams Options.
 *
 * @since 3.2.0
 * @package ebox\Settings\Pages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Page' ) ) && ( ! class_exists( 'ebox_Settings_Page_Teams_Options' ) ) ) {
	/**
	 * Class ebox Settings Page Teams Options.
	 *
	 * @since 3.2.0
	 */
	class ebox_Settings_Page_Teams_Options extends ebox_Settings_Page {

		/**
		 * Public constructor for class
		 *
		 * @since 3.2.0
		 */
		public function __construct() {

			$this->parent_menu_page_url = 'edit.php?post_type=teams';
			$this->menu_page_capability = ebox_ADMIN_CAPABILITY_CHECK;
			$this->settings_page_id     = 'teams-options';
			$this->settings_page_title  = esc_html_x( 'Settings', 'Team Settings', 'ebox' );
			$this->settings_tab_title   = $this->settings_page_title;

			parent::__construct();
		}
	}
}
add_action(
	'ebox_settings_pages_init',
	function() {
		ebox_Settings_Page_Teams_Options::add_page_instance();
	}
);
