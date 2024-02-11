<?php
/**
 * ebox Settings Section for Translations ebox Metabox.
 *
 * @since 2.5.2
 * @package ebox\Settings\Sections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Section' ) ) && ( ! class_exists( 'ebox_Settings_Section_Translations_ebox' ) ) ) {
	/**
	 * Class ebox Settings Section for Translations ebox Metabox.
	 *
	 * @since 2.5.2
	 */
	class ebox_Settings_Section_Translations_ebox extends ebox_Settings_Section {

		/**
		 * Must match the Text Domain.
		 *
		 * @var string $project_slug String for project.
		 */
		private $project_slug = 'ebox';

		/**
		 * Protected constructor for class
		 *
		 * @since 2.5.2
		 */
		protected function __construct() {
			$this->settings_page_id = 'ebox_lms_translations';

			$this->setting_option_key = 'ebox';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_section_key = 'settings_translations_' . $this->project_slug;

			// Section label/header.
			$this->settings_section_label = esc_html__( 'ebox LMS', 'ebox' );

			$this->load_options = false;

			ebox_Translations::register_translation_slug( $this->project_slug, ebox_LMS_PLUGIN_DIR . 'languages/' );

			parent::__construct();
		}

		/**
		 * Custom function to metabox.
		 *
		 * @since 2.5.2
		 */
		public function show_meta_box() {
			$ld_translations = new ebox_Translations( $this->project_slug );
			$ld_translations->show_meta_box();
		}
	}

	add_action(
		'init',
		function() {
			ebox_Settings_Section_Translations_ebox::add_section_instance();
		},
		1
	);
}
