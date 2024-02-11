<?php
/**
 * ebox Data Upgrades for Translations.
 *
 * @since 2.5.5
 * @package ebox\Data_Upgrades
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Admin_Data_Upgrades' ) ) && ( ! class_exists( 'ebox_Admin_Data_Upgrades_Translations' ) ) ) {

	/**
	 * Class ebox Data Upgrades for Translations.
	 *
	 * @since 2.5.5
	 * @uses ebox_Admin_Data_Upgrades
	 */
	class ebox_Admin_Data_Upgrades_Translations extends ebox_Admin_Data_Upgrades {

		/**
		 * Protected constructor for class
		 *
		 * @since 2.5.5
		 */
		protected function __construct() {
			$this->data_slug = 'translations';
			parent::__construct();
			add_action( 'init', array( $this, 'upgrade_translations' ) );
			parent::register_upgrade_action();
		}

		/**
		 * Update the ebox Translations
		 * Checks to see if settings needs to be updated.
		 *
		 * @since 2.5.5
		 */
		public function upgrade_translations() {
			if ( is_admin() ) {
				$translations_installed = $this->get_data_settings( 'translations_installed' );
				if ( ( defined( 'ebox_ACTIVATED' ) && ebox_ACTIVATED ) || ( ! $translations_installed ) ) {
					$this->download_translations();
					$this->set_data_settings( 'translations_installed', time() );
				}
			}
		}

		/**
		 * Download the translations from glotpress server.
		 *
		 * @since 2.5.5
		 */
		public function download_translations() {
			$wp_installed_languages = get_available_languages();
			if ( ! in_array( 'en_US', $wp_installed_languages, true ) ) {
				$wp_installed_languages = array_merge( array( 'en_US' ), $wp_installed_languages );
			}

			if ( ! empty( $wp_installed_languages ) ) {
				ebox_Translations::get_available_translations( 'ebox', true );
				foreach ( $wp_installed_languages as $locale ) {
					$reply = ebox_Translations::install_translation( 'ebox', $locale );
				}
			}
		}

		// End of functions.
	}
}

add_action(
	'ebox_data_upgrades_init',
	function() {
		ebox_Admin_Data_Upgrades_Translations::add_instance();
	}
);
