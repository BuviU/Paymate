<?php
/**
 * ebox Settings Page General.
 *
 * @since 2.4.0
 * @package ebox\Settings\Pages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Page' ) ) && ( ! class_exists( 'ebox_Settings_Page_General' ) ) ) {
	/**
	 * Class ebox Settings Page General.
	 *
	 * @since 2.4.0
	 */
	class ebox_Settings_Page_General extends ebox_Settings_Page {
		/**
		 * Public constructor for class
		 *
		 * @since 2.4.0
		 */
		public function __construct() {
			$this->parent_menu_page_url  = 'admin.php?page=ebox_lms_settings';
			$this->menu_page_capability  = ebox_ADMIN_CAPABILITY_CHECK;
			$this->settings_page_id      = 'ebox_lms_settings';
			$this->settings_page_title   = esc_html__( 'General', 'ebox' );
			$this->settings_tab_title    = $this->settings_page_title;
			$this->settings_tab_priority = 0;

			parent::__construct();
		}
	}
}
add_action(
	'ebox_settings_pages_init',
	function() {
		ebox_Settings_Page_General::add_page_instance();
	}
);



