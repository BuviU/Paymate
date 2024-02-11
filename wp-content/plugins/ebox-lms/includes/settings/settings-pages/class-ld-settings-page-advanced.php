<?php
/**
 * ebox Settings Page Advanced.
 *
 * @since   3.6.0
 * @package ebox\Settings\Pages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'ebox_Settings_Page' ) && ! class_exists( 'ebox_Settings_Page_Advanced' ) ) {
	/**
	 * Class ebox Settings Page Advanced.
	 *
	 * @since 3.6.0
	 */
	class ebox_Settings_Page_Advanced extends ebox_Settings_Page {
		/**
		 * Public constructor for class
		 *
		 * @since 3.6.0
		 */
		public function __construct() {
			$this->parent_menu_page_url  = 'admin.php?page=ebox_lms_settings';
			$this->menu_page_capability  = ebox_ADMIN_CAPABILITY_CHECK;
			$this->settings_page_id      = 'ebox_lms_advanced';
			$this->settings_page_title   = esc_html_x( 'Advanced', 'Advanced settings Label', 'ebox' );
			$this->settings_tab_priority = 100;

			$this->show_quick_links_meta   = false;
			$this->settings_metabox_as_sub = true;

			add_action( 'ebox_settings_page_init', array( $this, 'ebox_settings_page_init' ), 10, 1 );

			parent::__construct();
		}

		/**
		 * Settings page init. Called from `ebox_settings_page_init` action.
		 *
		 * @since 3.6.0
		 *
		 * @param string $settings_page_id Settings Page ID.
		 */
		public function ebox_settings_page_init( string $settings_page_id ) {
			if ( $settings_page_id !== $this->settings_page_id ) {
				return;
			}

			if ( true !== $this->settings_metabox_as_sub ) {
				return;
			}

			/**
			 * Filters the list of advanced settings pages which should not display metaboxes.
			 *
			 * @since 4.5.0
			 *
			 * @param string[] $section_keys Section keys.
			 */
			$section_keys = apply_filters( 'ebox_admin_settings_advanced_sections_with_hidden_metaboxes', array() );

			if ( in_array( $this->get_current_settings_section_as_sub(), $section_keys, true ) ) {
				$this->show_submit_meta      = false;
				$this->show_quick_links_meta = false;
				$this->settings_columns      = 1;
			}
		}
	}
}

add_action(
	'ebox_settings_pages_init',
	function () {
		ebox_Settings_Page_Advanced::add_page_instance();
	}
);
