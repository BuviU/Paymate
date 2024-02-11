<?php
/**
 * ebox Settings Section for Course Themes Metabox.
 *
 * @since 3.0.0
 * @package ebox\Settings\Sections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Section' ) ) && ( ! class_exists( 'ebox_Settings_Courses_Themes' ) ) ) {
	/**
	 * Class ebox Settings Section for Course Themes Metabox.
	 *
	 * @since 3.0.0
	 */
	class ebox_Settings_Courses_Themes extends ebox_Settings_Section {

		/**
		 * List of themes
		 *
		 * @var array
		 */
		private $themes_list = array();

		/**
		 * Protected constructor for class
		 *
		 * @since 3.0.0
		 */
		protected function __construct() {

			// The page ID (different than the screen ID).
			$this->settings_page_id = 'ebox_lms_settings';

			// This is the 'option_name' key used in the wp_options table.
			$this->setting_option_key = 'ebox_settings_courses_themes';

			// This is the HTML form field prefix used.
			$this->setting_field_prefix = 'ebox_settings_courses_themes';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_section_key = 'themes';

			// Section label/header.
			$this->settings_section_label = esc_html__( 'Design & Content Elements', 'ebox' );

			// Used to show the section description above the fields. Can be empty.
			$this->settings_section_description = esc_html__( 'Alter the look and feel of your Learning Management System', 'ebox' );

			add_action( 'ebox_section_fields_after', array( $this, 'ebox_section_fields_after' ), 10, 2 );

			parent::__construct();
		}

		/**
		 * Initialize the metabox settings values.
		 *
		 * @since 3.0.0
		 */
		public function load_settings_values() {
			parent::load_settings_values();

			$this->themes_list = array();

			$themes = ebox_Theme_Register::get_themes();
			if ( ! is_array( $themes ) ) {
				$themes = array();
			}

			foreach ( $themes as $theme ) {
				$this->themes_list[ $theme['theme_key'] ] = $theme['theme_name'];
			}

			if ( ( ! isset( $this->setting_option_values['active_theme'] ) ) || ( empty( $this->setting_option_values['active_theme'] ) ) ) {
				$ld_prior_version = ebox_data_upgrades_setting( 'prior_version' );
				if ( 'new' === $ld_prior_version ) {
					$this->setting_option_values['active_theme'] = ebox_DEFAULT_THEME;
				} else {
					$this->setting_option_values['active_theme'] = ebox_LEGACY_THEME;
				}
			}

			$themes_list_options = array();

			$active_theme_key = $this->setting_option_values['active_theme'];
			if ( ( ! empty( $active_theme_key ) ) && ( isset( $this->themes_list[ $active_theme_key ] ) ) ) {
				$themes_list_options['active'] = array(
					'optteam_label'   => esc_html__( 'Active Theme', 'ebox' ),
					'optteam_options' => array(
						$active_theme_key => $this->themes_list[ $active_theme_key ],
					),
				);
				unset( $this->themes_list[ $active_theme_key ] );
			}

			if ( ! empty( $this->themes_list ) ) {
				$themes_list_options['available'] = array(
					'optteam_label'   => esc_html__( 'Available Themes', 'ebox' ),
					'optteam_options' => $this->themes_list,
				);
			}

			$this->themes_list = $themes_list_options;
		}

		/**
		 * Initialize the metabox settings fields.
		 *
		 * @since 3.0.0
		 */
		public function load_settings_fields() {
			$this->setting_option_fields = array(
				'active_theme' => array(
					'name'      => 'active_theme',
					'type'      => 'select',
					'label'     => esc_html__( 'Active Template', 'ebox' ),
					'help_text' => esc_html__( 'New front-end design options and settings can be used when the ebox 3.0 template is activated.', 'ebox' ),
					'value'     => $this->setting_option_values['active_theme'],
					'options'   => $this->themes_list,
				),
			);

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
			$this->setting_option_fields = apply_filters( 'ebox_settings_fields', $this->setting_option_fields, $this->settings_section_key );

			parent::load_settings_fields();
		}

		/**
		 * Section Fields After
		 *
		 * @since 3.0.0
		 *
		 * @param string $settings_section_key Section Key.
		 * @param string $settings_screen_id   Screen ID.
		 */
		public function ebox_section_fields_after( $settings_section_key, $settings_screen_id ) {
			if ( $settings_section_key === $this->settings_section_key ) {

				$themes = ebox_Theme_Register::get_themes();
				if ( ! empty( $themes ) ) {
					global $wp_settings_sections;
					global $wp_settings_fields;

					$active_theme_key = ebox_Theme_Register::get_active_theme_key();

					foreach ( $themes as $theme ) {
						$theme_instance          = ebox_Theme_Register::get_theme_instance( $theme['theme_key'] );
						$theme_settings_sections = $theme_instance->get_theme_settings_sections();
						if ( ! empty( $theme_settings_sections ) ) {
							foreach ( $theme_settings_sections as $section_key => $section_instance ) {
								if ( isset( $wp_settings_fields[ $section_instance->settings_page_id ][ $section_key ] ) ) {
									$theme_state = 'closed';
									if ( $active_theme_key === $theme_instance->get_theme_key() ) {
										$theme_state = 'open';
									}
									echo '<div id="ebox_theme_settings_section_' . esc_attr( $theme_instance->get_theme_key() ) . '" class="ld-theme-settings-section ld-theme-settings-section-' . esc_attr( $theme_instance->get_theme_key() ) . ' ld-theme-settings-section-state-' . esc_attr( $theme_state ) . '">';
									$section_instance->show_settings_section_nonce_field();
									$this->show_settings_section_fields( $section_instance->settings_page_id, $section_key );
									echo '</div>';
								}
							}
						}
					}
				}
			}
		}

		// End of functions.
	}
}

add_action(
	'ebox_settings_sections_init',
	function() {
		ebox_Settings_Courses_Themes::add_section_instance();
	}
);
