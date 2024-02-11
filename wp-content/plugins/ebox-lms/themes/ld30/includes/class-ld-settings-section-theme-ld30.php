<?php
/**
 * ebox LD30 Settings Section for Theme Colors Metabox.
 *
 * @since 3.0.0
 *
 * @package ebox\Templates\LD30
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Theme_Settings_Section' ) ) && ( ! class_exists( 'ebox_Settings_Theme_LD30' ) ) ) {
	/**
	 * Class to create the settings section.
	 *
	 * @since 3.0.0
	 */
	class ebox_Settings_Theme_LD30 extends ebox_Theme_Settings_Section {

		/**
		 * Protected constructor for class
		 *
		 * @since 3.0.0
		 */
		protected function __construct() {

			// The page ID (different than the screen ID).
			$this->settings_page_id = 'ebox_lms_settings';

			// This is the 'option_name' key used in the wp_options table.
			$this->setting_option_key = 'ebox_settings_theme_ld30';

			// This is the HTML form field prefix used.
			$this->setting_field_prefix = 'ebox_settings_theme_ld30';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_section_key = 'settings_theme_ld30';

			// Section label/header.
			$this->settings_section_label = esc_html__( 'Theme ebox 3.0 Options', 'ebox' );

			// Set Associated Theme ID.
			$this->settings_theme_key = 'ld30';

			$ld30_colors_defs = array(
				'LD_30_COLOR_PRIMARY'   => '#00a2e8',
				'LD_30_COLOR_SECONDARY' => '#019e7c',
				'LD_30_COLOR_TERTIARY'  => '#ffd200',
			);

			foreach ( $ld30_colors_defs as $definition => $value ) {
				if ( ! defined( $definition ) ) {
					/**
					 * Ignore
					 *
					 * @ignore
					 */
					define( $definition, $value ); //phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.VariableConstantNameFound -- Inside a protected constructor
				}
			}

			parent::__construct();
		}

		/**
		 * Initialize the metabox settings values.
		 *
		 * @since 3.0.0
		 */
		public function load_settings_values() {
			parent::load_settings_values();

			if ( ! isset( $this->setting_option_values['login_logo'] ) ) {
				$this->setting_option_values['login_logo'] = 0;
			}

			if ( ! isset( $this->setting_option_values['focus_mode_enabled'] ) ) {
				$this->setting_option_values['focus_mode_enabled'] = 0;
			}

			if ( ! isset( $this->setting_option_values['login_mode_enabled'] ) ) {
				$this->setting_option_values['login_mode_enabled'] = 0;
			}

			if ( ! isset( $this->setting_option_values['responsive_video_enabled'] ) ) {
				$this->setting_option_values['responsive_video_enabled'] = 0;
			}

			if ( ( ! isset( $this->setting_option_values['color_primary'] ) ) || ( empty( $this->setting_option_values['color_primary'] ) ) ) {
				$this->setting_option_values['color_primary'] = LD_30_COLOR_PRIMARY;
			}

			if ( ( ! isset( $this->setting_option_values['color_secondary'] ) ) || ( empty( $this->setting_option_values['color_secondary'] ) ) ) {
				$this->setting_option_values['color_secondary'] = LD_30_COLOR_SECONDARY;
			}

			if ( ( ! isset( $this->setting_option_values['color_tertiary'] ) ) || ( empty( $this->setting_option_values['color_tertiary'] ) ) ) {
				$this->setting_option_values['color_tertiary'] = LD_30_COLOR_TERTIARY;
			}

			if ( ( ! isset( $this->setting_option_values['focus_mode_content_width'] ) ) || ( empty( $this->setting_option_values['focus_mode_content_width'] ) ) ) {
				$this->setting_option_values['focus_mode_content_width'] = 'default';
			}

			if ( ( ! isset( $this->setting_option_values['focus_mode_sidebar_position'] ) ) || ( empty( $this->setting_option_values['focus_mode_sidebar_position'] ) ) ) {
				$this->setting_option_values['focus_mode_sidebar_position'] = 'default';
			}
		}

		/**
		 * Initialize the metabox settings fields.
		 *
		 * @since 3.0.0
		 */
		public function load_settings_fields() {
			$this->setting_option_fields = array(

				'color_primary'               => array(
					'name'      => 'color_primary',
					'type'      => 'colorpicker',
					'label'     => esc_html__( 'Accent Color', 'ebox' ),
					'help_text' => esc_html__( 'Main color used throughout the theme (buttons, action items, and highlights).', 'ebox' ),
					'value'     => ! empty( $this->setting_option_values['color_primary'] ) ? $this->setting_option_values['color_primary'] : '',
					'attrs'     => array(
						'data-default-color' => LD_30_COLOR_PRIMARY,
						'placeholder'        => LD_30_COLOR_PRIMARY,
					),
				),
				'color_secondary'             => array(
					'name'      => 'color_secondary',
					'type'      => 'colorpicker',
					'label'     => esc_html__( 'Progress Color', 'ebox' ),
					'help_text' => esc_html__( 'Color used for all successful progress-related items (completed items, certificates, and progress bars).', 'ebox' ),
					'value'     => ! empty( $this->setting_option_values['color_secondary'] ) ? $this->setting_option_values['color_secondary'] : '',
					'attrs'     => array(
						'data-default-color' => LD_30_COLOR_SECONDARY,
						'placeholder'        => LD_30_COLOR_SECONDARY,
					),
				),
				'color_tertiary'              => array(
					'name'      => 'color_tertiary',
					'type'      => 'colorpicker',
					'label'     => esc_html__( 'Notifications, Warnings, etc...', 'ebox' ),
					'help_text' => esc_html__( 'This color is used when there are warning, important messages.', 'ebox' ),
					'value'     => ! empty( $this->setting_option_values['color_tertiary'] ) ? $this->setting_option_values['color_tertiary'] : '',
					'attrs'     => array(
						'data-default-color' => LD_30_COLOR_TERTIARY,
						'placeholder'        => LD_30_COLOR_TERTIARY,
					),
				),
				'focus_mode_enabled'          => array(
					'name'                => 'focus_mode_enabled',
					'type'                => 'checkbox-switch',
					'label'               => esc_html__( 'Focus Mode', 'ebox' ),
					'help_text'           => sprintf(
						// translators: placeholder: course, courses.
						esc_html_x( 'Provide a distraction-free %1$s experience allowing users to focus on the content. This applies to ALL %2$s.', 'placeholder: course, courses.', 'ebox' ),
						ebox_get_custom_label_lower( 'course' ),
						ebox_get_custom_label_lower( 'courses' )
					),
					'value'               => $this->setting_option_values['focus_mode_enabled'],
					'options'             => array(
						''    => '',
						'yes' => sprintf(
							// translators: placeholder: course.
							esc_html_x( 'Distraction-free %s experience', 'placeholder: course', 'ebox' ),
							ebox_get_custom_label_lower( 'course' )
						),
					),
					'child_section_state' => ( 'yes' === $this->setting_option_values['focus_mode_enabled'] ) ? 'open' : 'closed',
				),
				'focus_mode_content_width'    => array(
					'name'           => 'focus_mode_content_width',
					'type'           => 'select',
					'label'          => esc_html__( 'Focus Mode Content Width', 'ebox' ),
					'help_text'      => esc_html__( 'Adjust the maximum width of the content area within Focus Mode', 'ebox' ),
					'value'          => $this->setting_option_values['focus_mode_content_width'],
					'options'        => array(
						'default' => __( 'Default (960px)', 'ebox' ),
						'768px'   => __( 'Narrow (768px)', 'ebox' ),
						'1180px'  => __( 'Wide (1180px)', 'ebox' ),
						'1600px'  => __( 'Extra-wide (1600px)', 'ebox' ),
						'inherit' => __( 'Full width', 'ebox' ),
					),
					'parent_setting' => 'focus_mode_enabled',
				),
				'focus_mode_sidebar_position' => array(
					'name'           => 'focus_mode_sidebar_position',
					'type'           => 'select',
					'label'          => esc_html__( 'Focus Mode Sidebar Position', 'ebox' ),
					'help_text'      => esc_html__( 'Set the Position of the Sidebar while on Focus Mode', 'ebox' ),
					'value'          => $this->setting_option_values['focus_mode_sidebar_position'],
					'options'        => $this->focus_mode_sidebar_position_options(),
					'parent_setting' => 'focus_mode_enabled',
				),
				'login_mode_enabled'          => array(
					'name'      => 'login_mode_enabled',
					'type'      => 'checkbox-switch',
					'label'     => esc_html__( 'Login & Registration', 'ebox' ),
					'help_text' => sprintf(
						// translators: placeholder: Link to Registration article.
						esc_html_x( 'When active the ebox templates will be used for user login and %s pages.', 'placeholder: Link to Registration article.', 'ebox' ),
						'<a href="https://www.ebox.com/support/docs/faqs/why-is-the-registration-form-not-showing/" target="_blank">' . esc_html__( 'registration', 'ebox' ) . '</a>'
					),
					'value'     => $this->setting_option_values['login_mode_enabled'],
					'options'   => array(
						''    => esc_html__( 'Default registration used', 'ebox' ),
						'yes' => sprintf(
							// translators: placeholder: courses.
							esc_html_x( 'Customized registration enabled for ebox %s', 'placeholder: courses', 'ebox' ),
							ebox_get_custom_label_lower( 'courses' )
						),
					),
				),
				'login_logo'                  => array(
					'name'              => 'login_logo',
					'type'              => 'media-upload',
					'label'             => esc_html__( 'Logo Upload', 'ebox' ),
					'help_text'         => esc_html__( 'This logo will appear in the Focus Mode and ebox Login form when enabled. Optional.', 'ebox' ),
					'value'             => $this->setting_option_values['login_logo'],
					'validate_callback' => array( $this, 'validate_section_field_media_upload' ),
					'validate_args'     => array(
						'allow_empty' => 1,
					),
				),
				'responsive_video_enabled'    => array(
					'name'      => 'responsive_video_enabled',
					'type'      => 'checkbox-switch',
					'label'     => esc_html__( 'Video Responsive CSS', 'ebox' ),
					'help_text' => esc_html__( 'This will make your videos within video progression responsive. Disable if you notice video display issues.', 'ebox' ),
					'value'     => $this->setting_option_values['responsive_video_enabled'],
					'options'   => array(
						''    => '',
						'yes' => esc_html__( 'Videos will automatically resize based on screen size', 'ebox' ),
					),
				),
			);

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
			$this->setting_option_fields = apply_filters( 'ebox_settings_fields', $this->setting_option_fields, $this->settings_section_key );

			parent::load_settings_fields();
		}

		/**
		 * Options for focus_mode_sidebar_position.
		 *
		 * @since 4.1.0
		 *
		 * @return array Options.
		 */
		private function focus_mode_sidebar_position_options() {
			if ( is_rtl() ) {
				return array(
					'default' => __( 'Right (default)', 'ebox' ),
					'left'    => __( 'Left', 'ebox' ),
				);
			}

			return array(
				'default' => __( 'Left (default)', 'ebox' ),
				'right'   => __( 'Right', 'ebox' ),
			);
		}

		/**
		 * Validate settings field.
		 *
		 * @since 3.0.0
		 *
		 * @param string $val Value to be validated.
		 * @param string $key settings fields key.
		 * @param array  $args Settings field args array.
		 *
		 * @return integer $val.
		 */
		public function validate_section_field_media_upload( $val, $key, $args = array() ) {
			// Get the digits only.
			$val = absint( $val );
			if ( ( isset( $args['field']['validate_args']['allow_empty'] ) ) && ( true == $args['field']['validate_args']['allow_empty'] ) && ( empty( $val ) ) ) {
				$val = '';
			}
			return $val;
		}
	}
}

add_action(
	'ebox_settings_sections_init',
	function() {
		ebox_Settings_Theme_LD30::add_section_instance();
	}
);
