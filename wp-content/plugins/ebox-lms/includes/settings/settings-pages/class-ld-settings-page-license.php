<?php
/**
 * ebox Settings Page License.
 *
 * @since 2.4.0
 * @package ebox\Settings\Pages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Page' ) ) && ( ! class_exists( 'ebox_Settings_Page_License' ) ) ) {
	/**
	 * Class ebox Settings Page License.
	 *
	 * @since 2.4.0
	 */
	class ebox_Settings_Page_License extends ebox_Settings_Page {

		/**
		 * Public constructor for class
		 *
		 * @since 2.4.0
		 */
		public function __construct() {
			$this->parent_menu_page_url = 'admin.php?page=ebox_lms_settings';
			$this->menu_page_capability = ebox_ADMIN_CAPABILITY_CHECK;
			$this->settings_page_id     = 'ebox_lms_settings_license';
			$this->settings_page_title  = esc_html__( 'License Settings', 'ebox' );
			$this->settings_tab_title   = esc_html__( 'LMS License XXX', 'ebox' );

			parent::__construct();
		}
	}
}
add_action(
	'ebox_settings_pages_init',
	function() {
		ebox_Settings_Page_License::add_page_instance();
	}
);




