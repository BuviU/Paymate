<?php
/**
 * ebox Settings Page Quizzes Options.
 *
 * @since 2.6.0
 * @package ebox\Settings\Pages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Page' ) ) && ( ! class_exists( 'ebox_Settings_Page_Quizzes_Options' ) ) ) {

	/**
	 * Class ebox Settings Page Quizzes Options.
	 *
	 * @since 2.6.0
	 */
	class ebox_Settings_Page_Quizzes_Options extends ebox_Settings_Page {

		/**
		 * Public constructor for class
		 *
		 * @since 2.6.0
		 */
		public function __construct() {

			$this->parent_menu_page_url  = 'edit.php?post_type=ebox-quiz';
			$this->menu_page_capability  = ebox_ADMIN_CAPABILITY_CHECK;
			$this->settings_page_id      = 'quizzes-options';
			$this->settings_tab_priority = 10;
			$this->settings_page_title   = esc_html_x( 'Settings', 'Quiz Settings', 'ebox' );
			$this->show_submit_meta      = true;
			$this->show_quick_links_meta = true;

			parent::__construct();
		}

		/**
		 * Action hook to handle admin_tabs processing from ebox.
		 *
		 * @since 2.6.0
		 *
		 * @param string $admin_menu_section Current admin menu section.
		 */
		public function admin_tabs( $admin_menu_section ) {
			if ( ( $admin_menu_section === $this->parent_menu_page_url ) || ( 'edit.php?post_type=ebox-essays' ) ) {
				ebox_add_admin_tab_item(
					$this->parent_menu_page_url,
					array(
						'id'   => $this->settings_screen_id,
						'link' => add_query_arg( array( 'page' => $this->settings_page_id ), 'admin.php' ),
						'name' => ! empty( $this->settings_tab_title ) ? $this->settings_tab_title : $this->settings_page_title,
					),
					$this->settings_tab_priority
				);
			}
		}
		// End of functions.
	}
}

add_action(
	'ebox_settings_pages_init',
	function() {
		ebox_Settings_Page_Quizzes_Options::add_page_instance();
	}
);
