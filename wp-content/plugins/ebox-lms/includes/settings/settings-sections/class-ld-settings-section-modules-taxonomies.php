<?php
/**
 * ebox Settings Section for modules Taxonomies Metabox.
 *
 * @since 2.4.0
 * @package ebox\Settings\Sections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Section' ) ) && ( ! class_exists( 'ebox_Settings_modules_Taxonomies' ) ) ) {
	/**
	 * Class ebox Settings Section for modules Taxonomies Metabox.
	 *
	 * @since 2.4.0
	 */
	class ebox_Settings_modules_Taxonomies extends ebox_Settings_Section {

		/**
		 * Protected constructor for class
		 *
		 * @since 2.4.0
		 */
		protected function __construct() {
			$this->settings_screen_id = 'ebox-modules_page_modules-options';

			// The page ID (different than the screen ID).
			$this->settings_page_id = 'modules-options';

			// This is the 'option_name' key used in the wp_options table.
			$this->setting_option_key = 'ebox_settings_modules_taxonomies';

			// This is the HTML form field prefix used.
			$this->setting_field_prefix = 'ebox_settings_modules_taxonomies';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_section_key = 'taxonomies';

			// Section label/header.
			$this->settings_section_label = sprintf(
				// translators: placeholder: Lesson.
				esc_html_x( '%s Taxonomies', 'placeholder: Lesson', 'ebox' ),
				ebox_get_custom_label( 'lesson' )
			);

			// Used to show the section description above the fields. Can be empty.
			$this->settings_section_description = sprintf(
				// translators: placeholder: modules.
				esc_html_x( 'Control which taxonomies can be used to better organize your ebox %s.', 'placeholder: modules', 'ebox' ),
				ebox_get_custom_label_lower( 'modules' )
			);

			parent::__construct();
		}

		/**
		 * Initialize the metabox settings values.
		 *
		 * @since 2.4.0
		 */
		public function load_settings_values() {
			parent::load_settings_values();

			$_init = false;
			if ( false === $this->setting_option_values ) {
				$__init                      = true;
				$this->setting_option_values = array(
					'ld_lesson_category' => 'yes',
					'ld_lesson_tag'      => 'yes',
					'wp_post_category'   => 'yes',
					'wp_post_tag'        => 'yes',
				);

				// If this is a new install we want to turn off WP Post Category/Tag.

				$ld_prior_version = ebox_data_upgrades_setting( 'prior_version' );
				if ( 'new' === $ld_prior_version ) {
					$this->setting_option_values['wp_post_category'] = '';
					$this->setting_option_values['wp_post_tag']      = '';
				}
			}

			$this->setting_option_values = wp_parse_args(
				$this->setting_option_values,
				array(
					'ld_lesson_category' => '',
					'ld_lesson_tag'      => '',
					'wp_post_category'   => '',
					'wp_post_tag'        => '',
				)
			);
		}

		/**
		 * Initialize the metabox settings fields.
		 *
		 * @since 2.4.0
		 */
		public function load_settings_fields() {

			$this->setting_option_fields = array(
				'ld_lesson_category' => array(
					'name'    => 'ld_lesson_category',
					'type'    => 'checkbox-switch',
					'label'   => sprintf(
						// translators: placeholder: Lesson.
						esc_html_x( '%s Categories', 'placeholder: Lesson', 'ebox' ),
						ebox_get_custom_label( 'lesson' )
					),
					'value'   => $this->setting_option_values['ld_lesson_category'],
					'options' => array(
						''    => '',
						'yes' => sprintf(
							// translators: placeholder: Lesson.
							esc_html_x( 'Manage %s Categories via the Actions dropdown', 'placeholder: Lesson', 'ebox' ),
							ebox_get_custom_label( 'lesson' )
						),
					),
				),
				'ld_lesson_tag'      => array(
					'name'    => 'ld_lesson_tag',
					'type'    => 'checkbox-switch',
					'label'   => sprintf(
						// translators: placeholder: Lesson.
						esc_html_x( '%s Tags', 'placeholder: Lesson', 'ebox' ),
						ebox_get_custom_label( 'lesson' )
					),
					'value'   => $this->setting_option_values['ld_lesson_tag'],
					'options' => array(
						''    => '',
						'yes' => sprintf(
							// translators: placeholder: Lesson.
							esc_html_x( 'Manage %s Tags via the Actions dropdown', 'placeholder: Lesson', 'ebox' ),
							ebox_get_custom_label( 'lesson' )
						),
					),
				),
				'wp_post_category'   => array(
					'name'    => 'wp_post_category',
					'type'    => 'checkbox-switch',
					'label'   => esc_html__( 'WP Post Categories', 'ebox' ),
					'value'   => $this->setting_option_values['wp_post_category'],
					'options' => array(
						''    => '',
						'yes' => esc_html__( 'Manage WP Categories via the Actions dropdown', 'ebox' ),
					),
				),
				'wp_post_tag'        => array(
					'name'    => 'wp_post_tag',
					'type'    => 'checkbox-switch',
					'label'   => esc_html__( 'WP Post Tags', 'ebox' ),
					'value'   => $this->setting_option_values['wp_post_tag'],
					'options' => array(
						''    => '',
						'yes' => esc_html__( 'Manage WP Tags via the Actions dropdown', 'ebox' ),
					),
				),
			);

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
			$this->setting_option_fields = apply_filters( 'ebox_settings_fields', $this->setting_option_fields, $this->settings_section_key );

			parent::load_settings_fields();
		}
	}
}
add_action(
	'ebox_settings_sections_init',
	function() {
		ebox_Settings_modules_Taxonomies::add_section_instance();
	}
);
