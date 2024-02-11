<?php
/**
 * ebox Settings Page Questions Options.
 *
 * @since 2.6.0
 * @package ebox\Settings\Pages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Page' ) ) && ( ! class_exists( 'ebox_Settings_Page_Questions_Options' ) ) ) {

	/**
	 * Class ebox Settings Page Questions Options.
	 *
	 * @since 2.6.0
	 */
	class ebox_Settings_Page_Questions_Options extends ebox_Settings_Page {

		/**
		 * Public constructor for class
		 *
		 * @since 2.6.0
		 */
		public function __construct() {

			$this->parent_menu_page_url  = 'edit.php?post_type=ebox-question';
			$this->menu_page_capability  = ebox_ADMIN_CAPABILITY_CHECK;
			$this->settings_page_id      = 'questions-options';
			$this->settings_tab_priority = 10;
			$this->settings_page_title   = esc_html_x( 'Settings', 'Question Settings', 'ebox' );
			$this->show_submit_meta      = true;
			$this->show_quick_links_meta = true;

			parent::__construct();
		}
	}
}

add_action(
	'ebox_settings_pages_init',
	function() {
		ebox_Settings_Page_Questions_Options::add_page_instance();
	}
);
