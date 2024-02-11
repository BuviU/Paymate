<?php
/**
 * ebox Settings Page Payments.
 *
 * @since 2.4.0
 * @package ebox\Settings\Pages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Page' ) ) && ( ! class_exists( 'ebox_Settings_Page_Payments' ) ) ) {
	/**
	 * Class ebox Settings Page Payments.
	 *
	 * @since 2.4.0
	 */
	class ebox_Settings_Page_Payments extends ebox_Settings_Page {

		/**
		 * Public constructor for class
		 *
		 * @since 2.4.0
		 */
		public function __construct() {

			$this->parent_menu_page_url = 'admin.php?page=ebox_lms_settings';
			$this->menu_page_capability = ebox_ADMIN_CAPABILITY_CHECK;
			$this->settings_page_id     = 'ebox_lms_payments';

			// translators: Course Shortcodes Label.
			$this->settings_page_title = esc_html_x( 'Payments', 'Payments Tab Label', 'ebox' );

			$this->show_quick_links_meta = false;

			$this->settings_tab_priority = 20;
			parent::__construct();
		}
	}
}
add_action(
	'ebox_settings_pages_init',
	function() {
		ebox_Settings_Page_Payments::add_page_instance();
	}
);
