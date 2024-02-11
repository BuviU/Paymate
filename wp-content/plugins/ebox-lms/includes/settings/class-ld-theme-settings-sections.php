<?php
/**
 * ebox Theme Settings Class.
 *
 * @since 3.3.0
 * @package ebox\Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Section' ) ) && ( ! class_exists( 'ebox_Theme_Settings_Section' ) ) ) {
	/**
	 * Class to create the settings section.
	 *
	 * @since 3.3.0
	 */
	abstract class ebox_Theme_Settings_Section extends ebox_Settings_Section {
		/**
		 * Match Theme Key.
		 * This should match the theme_key set within the ebox_Theme_Register instance.
		 *
		 * @var string $settings_theme_key Settings Theme ID.
		 */
		protected $settings_theme_key = '';

		/**
		 * Protected constructor for class
		 *
		 * @since 3.3.0
		 */
		protected function __construct() {
			parent::__construct();

			if ( ! empty( $this->settings_theme_key ) ) {
				ebox_Theme_Register::register_theme_settings_section( $this->settings_theme_key, $this->settings_section_key, $this );
			}
			add_filter( 'ebox_show_metabox', array( $this, 'ebox_show_metabox' ), 1, 3 );
		}

		/**
		 * Show theme metabox
		 *
		 * @since 3.3.0
		 *
		 * @param bool   $show_metabox       True to show metabox.
		 * @param string $metabox_key        Metabox key.
		 * @param string $settings_screen_id Screen ID.
		 */
		final public function ebox_show_metabox( $show_metabox = true, $metabox_key = '', $settings_screen_id = '' ) {
			if ( $metabox_key === $this->metabox_key ) {
				$show_metabox = false;
			}

			return $show_metabox;
		}
	}
}
