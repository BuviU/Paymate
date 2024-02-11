<?php
/**
 * ebox Settings Page Registration.
 *
 * @since 2.4.0
 * @package ebox\Settings\Pages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Page' ) ) && ( ! class_exists( 'ebox_Settings_Page_Registration' ) ) ) {
	/**
	 * Class ebox Settings Page Registration.
	 *
	 * @since 2.4.0
	 */
	class ebox_Settings_Page_Registration extends ebox_Settings_Page {

		/**
		 * Public constructor for class
		 *
		 * @since 2.4.0
		 */
		public function __construct() {

			$this->parent_menu_page_url = 'admin.php?page=ebox_lms_settings';
			$this->menu_page_capability = ebox_ADMIN_CAPABILITY_CHECK;
			$this->settings_page_id     = 'ebox_lms_registration';

			// translators: Course Shortcodes Label.
			$this->settings_page_title   = esc_html_x( 'Registration/Login', 'Registration/Login Tab Label', 'ebox' );
			$this->settings_columns      = 2;
			$this->show_quick_links_meta = false;

			$this->settings_tab_priority = 10;
			parent::__construct();
		}

		/**
		 * Action hook to handle admin_tabs processing from ebox.
		 *
		 * @since 2.4.0
		 *
		 * @param string $admin_menu_section Current admin menu section.
		 */
		public function admin_tabs( $admin_menu_section ) {
			if ( $admin_menu_section === $this->parent_menu_page_url ) {
				if ( ( ! is_multisite() ) && ( 'legacy' !== ebox_Theme_Register::get_active_theme_key() ) ) {
					parent::admin_tabs( $admin_menu_section );
				}
			}
		}
	}
}
add_action(
	'ebox_settings_pages_init',
	function() {
		ebox_Settings_Page_Registration::add_page_instance();
	}
);
