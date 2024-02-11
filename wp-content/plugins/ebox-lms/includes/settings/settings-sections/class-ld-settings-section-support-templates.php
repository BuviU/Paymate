<?php
/**
 * ebox Settings Section for Support Templates Metabox.
 *
 * @since 3.1.0
 * @package ebox\Settings\Sections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Section' ) ) && ( ! class_exists( 'ebox_Settings_Section_Support_Templates' ) ) ) {
	/**
	 * Class ebox Settings Section for Support Templates Metabox.
	 *
	 * @since 3.1.0
	 */
	class ebox_Settings_Section_Support_Templates extends ebox_Settings_Section {

		/**
		 * Settings set array for this section.
		 *
		 * @var array $settings_set Array of settings used by this section.
		 */
		protected $settings_set = array();

		/**
		 * Settings template array for this section.
		 *
		 * @var array $template_array Array of template used by this section.
		 */
		protected $template_array = array();


		/**
		 * Protected constructor for class
		 *
		 * @since 3.1.0
		 */
		protected function __construct() {
			$this->settings_page_id = 'ebox_support';

			// This is the 'option_name' key used in the wp_options table.
			$this->setting_option_key = 'ld_templates';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_section_key = 'settings_support_ld_templates';

			// Section label/header.
			$this->settings_section_label = esc_html__( 'Templates', 'ebox' );

			$this->load_options = false;

			add_filter( 'ebox_support_sections_init', array( $this, 'ebox_support_sections_init' ) );
			add_action( 'ebox_section_fields_before', array( $this, 'show_support_section' ), 30, 2 );

			parent::__construct();
		}

		/**
		 * Support Sections Init
		 *
		 * @since 3.1.0
		 *
		 * @param array $support_sections Support sections array.
		 */
		public function ebox_support_sections_init( $support_sections = array() ) {
			global $wpdb, $wp_version, $wp_rewrite;
			global $ebox_lms;

			$abspath_tmp = str_replace( '\\', '/', ABSPATH );

			/************************************************************************************************
			 * ebox Templates.
			 */

			if ( ! isset( $support_sections[ $this->setting_option_key ] ) ) {
				$this->load_templates();

				$this->settings_set           = array();
				$this->settings_set['header'] = array(
					'html' => $this->settings_section_label,
					'text' => $this->settings_section_label,
				);

				$this->settings_set['columns'] = array(
					'label' => array(
						'html'  => esc_html__( 'Template Name', 'ebox' ),
						'text'  => 'Template Name',
						'class' => 'ebox-support-settings-left',
					),
					'value' => array(
						'html'  => esc_html__( 'Template Path', 'ebox' ),
						'text'  => 'Template Path',
						'class' => 'ebox-support-settings-right',
					),
				);

				$this->settings_set['desc'] = '';

				$this->settings_set['desc'] .= '<p><strong>' . esc_html__( 'Current Active LD Theme', 'ebox' ) . '</strong>: ' . ebox_Theme_Register::get_active_theme_name() . '</p>';

				$template_paths = ebox_LMS::get_template_paths( 'xxx.php' );

				$theme_root = get_theme_root();
				$theme_root = str_replace( '\\', '/', $theme_root );

				$this->settings_set['desc'] .= '<p>' . esc_html__( 'The following is the search order paths for override templates, relative to site root:', 'ebox' );

				$this->settings_set['desc'] .= '<ol>';

				if ( ( isset( $template_paths['theme'] ) ) && ( ! empty( $template_paths['theme'] ) ) ) {
					foreach ( $template_paths['theme'] as $theme_path ) {
						$theme_path = dirname( $theme_path );
						if ( '.' === $theme_path ) {
							$theme_path = '';
						} else {
							$theme_path = '/' . $theme_path;
						}
						$this->settings_set['desc'] .= '<li>' . str_replace( $abspath_tmp, '/', $theme_root ) . '/' . esc_html__( '<PARENT or CHILD THEME>', 'ebox' ) . $theme_path . '</li>';
					}
				}

				if ( ( isset( $template_paths['templates'] ) ) && ( ! empty( $template_paths['templates'] ) ) ) {
					foreach ( $template_paths['templates'] as $theme_path ) {
						$theme_path = dirname( $theme_path );
						if ( '.' === $theme_path ) {
							$theme_path = '';
						}
						$this->settings_set['desc'] .= '<li>' . str_replace( $abspath_tmp, '/', $theme_path ) . '</li>';
					}
				}

				$this->settings_set['desc'] .= '</ol></p>';

				$this->settings_set['settings'] = array();

				$abspath_tmp                  = str_replace( '\\', '/', ABSPATH );
				$ebox_lms_plugin_dir_tmp = str_replace( '\\', '/', ebox_LMS_PLUGIN_DIR );

				if ( ! empty( $this->template_array ) ) {
					foreach ( $this->template_array as $template_filename => $template_path ) {
						if ( ! empty( $template_path ) ) {
							$template_path = str_replace( '\\', '/', $template_path );

							$this->settings_set['settings'][ $template_filename ] = array(
								'label' => $template_filename,
							);

							if ( strncmp( $template_path, $ebox_lms_plugin_dir_tmp, strlen( $ebox_lms_plugin_dir_tmp ) ) != 0 ) {
								$this->settings_set['settings'][ $template_filename ]['value_html'] = '<span style="color: red;">' . str_replace( $abspath_tmp, '', $template_path ) . '</span>';
								$this->settings_set['settings'][ $template_filename ]['value']      = str_replace( $abspath_tmp, '', $template_path ) . ' (X)';
							} else {
								$this->settings_set['settings'][ $template_filename ]['value_html'] = str_replace( $abspath_tmp, '', $template_path );
								$this->settings_set['settings'][ $template_filename ]['value']      = str_replace( $abspath_tmp, '', $template_path );
							}
						}
					}
				}

				/** This filter is documented in includes/settings/settings-sections/class-ld-settings-section-support-database-tables.php */
				$support_sections[ $this->setting_option_key ] = apply_filters( 'ebox_support_section', $this->settings_set, $this->setting_option_key );
			}

			return $support_sections;
		}

		/**
		 * Show Support Section
		 *
		 * @since 3.1.0
		 *
		 * @param string $settings_section_key Section Key.
		 * @param string $settings_screen_id   Screen ID.
		 */
		public function show_support_section( $settings_section_key = '', $settings_screen_id = '' ) {
			if ( $settings_section_key === $this->settings_section_key ) {
				$support_page_instance = ebox_Settings_Page::get_page_instance( 'ebox_Settings_Page_Support' );
				if ( $support_page_instance ) {
					$support_page_instance->show_support_section( $this->setting_option_key );
				}
			}
		}

		/**
		 * Load template files in preparation for processing.
		 *
		 * @since 3.1.0
		 */
		public function load_templates() {
			$this->template_array = array();

			$abspath_tmp                  = str_replace( '\\', '/', ABSPATH );
			$ebox_lms_plugin_dir_tmp = str_replace( '\\', '/', ebox_LMS_PLUGIN_DIR );

			$active_theme_instance = ebox_Theme_Register::get_active_theme_instance();
			if ( is_a( $active_theme_instance, 'ebox_Theme_Register' ) ) {
				$active_theme_dir = $active_theme_instance->get_theme_template_dir();
				$template_files   = ebox_scandir_recursive( $active_theme_dir );
				if ( ! empty( $template_files ) ) {
					foreach ( $template_files as $idx => $template_file ) {
						$template_file = str_replace( '\\', '/', $template_file );
						$file_pathinfo = pathinfo( $template_file );
						if ( ( ! isset( $file_pathinfo['extension'] ) ) || ( empty( $file_pathinfo['extension'] ) ) ) {
							continue;
						}

						if ( ( ! isset( $file_pathinfo['filename'] ) ) || ( empty( $file_pathinfo['filename'] ) ) ) {
							continue;
						}

						if ( ! in_array( $file_pathinfo['extension'], array( 'php', 'css', 'js' ), true ) ) {
							continue;
						}

						if ( '_' === $file_pathinfo['filename'][0] ) {
							continue;
						}

						if ( false !== strpos( $file_pathinfo['filename'], '.min.' ) ) {
							continue;
						}

						if ( ! in_array( $template_file, $this->template_array, true ) ) {
							$template_filename = str_replace( $active_theme_dir . '/', '', $template_file );
							$template_path     = ebox_LMS::get_template( $template_filename, null, null, true );
							if ( ! empty( $template_path ) ) {
								$this->template_array[ $template_filename ] = $template_path;
							}
						}
					}
				}
			}

			if ( ebox_Theme_Register::get_active_theme_key() !== ebox_LEGACY_THEME ) {
				$legacy_theme_instance = ebox_Theme_Register::get_theme_instance( ebox_LEGACY_THEME );
				if ( is_a( $active_theme_instance, 'ebox_Theme_Register' ) ) {
					$legacy_theme_dir = $legacy_theme_instance->get_theme_template_dir();
					if ( ! empty( $legacy_theme_dir ) ) {
						$template_files = ebox_scandir_recursive( $legacy_theme_dir );
						if ( ! empty( $template_files ) ) {
							foreach ( $template_files as $idx => $template_file ) {
								$template_file = str_replace( '\\', '/', $template_file );
								$file_pathinfo = pathinfo( $template_file );
								if ( ( ! isset( $file_pathinfo['extension'] ) ) || ( empty( $file_pathinfo['extension'] ) ) ) {
									continue;
								}

								if ( ( ! isset( $file_pathinfo['filename'] ) ) || ( empty( $file_pathinfo['filename'] ) ) ) {
									continue;
								}

								if ( ! in_array( $file_pathinfo['extension'], array( 'php', 'css', 'js' ), true ) ) {
									continue;
								}

								if ( '_' === $file_pathinfo['filename'][0] ) {
									continue;
								}

								if ( false !== strpos( $file_pathinfo['filename'], '.min.' ) ) {
									continue;
								}

								$template_filename = str_replace( $legacy_theme_dir . '/', '', $template_file );
								if ( ! isset( $this->template_array[ $template_filename ] ) ) {
									$template_path = ebox_LMS::get_template( $template_filename, null, null, true );
									if ( ! empty( $template_path ) ) {
										$this->template_array[ $template_filename ] = $template_path;
									}
								}
							}
						}
					}
				}
			}

			if ( ! empty( $this->template_array ) ) {
				ksort( $this->template_array );

				// We want to reorder the.
				$templates_teamed = array(
					'override' => array(),
				);

				$active_theme_dir      = '';
				$legacy_theme_dir      = '';
				$active_theme_instance = ebox_Theme_Register::get_active_theme_instance();
				if ( is_a( $active_theme_instance, 'ebox_Theme_Register' ) ) {
					$templates_teamed['active'] = array();
					$active_theme_dir            = $active_theme_instance->get_theme_template_dir();
				}

				if ( ebox_Theme_Register::get_active_theme_key() !== ebox_LEGACY_THEME ) {
					$legacy_theme_instance = ebox_Theme_Register::get_theme_instance( ebox_LEGACY_THEME );
					if ( is_a( $active_theme_instance, 'ebox_Theme_Register' ) ) {
						$templates_teamed['legacy'] = array();
						$legacy_theme_dir            = $legacy_theme_instance->get_theme_template_dir();
					}
				}

				foreach ( $this->template_array as $template_filename => $template_path ) {
					if ( strncmp( $template_path, $ebox_lms_plugin_dir_tmp, strlen( $ebox_lms_plugin_dir_tmp ) ) != 0 ) {
						$templates_teamed['override'][ $template_filename ] = $template_path;
					} elseif ( ( ! empty( $active_theme_dir ) ) && ( strncmp( $template_path, $active_theme_dir, strlen( $active_theme_dir ) ) == 0 ) ) {
						$templates_teamed['active'][ $template_filename ] = $template_path;
					} elseif ( ( ! empty( $legacy_theme_dir ) ) && ( strncmp( $template_path, $legacy_theme_dir, strlen( $legacy_theme_dir ) ) == 0 ) ) {
						$templates_teamed['legacy'][ $template_filename ] = $template_path;
					}
				}

				$this->template_array = array();
				foreach ( $templates_teamed as $template_section => $template_array ) {
					if ( ! empty( $template_array ) ) {
						$this->template_array = array_merge( $this->template_array, $template_array );
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
		ebox_Settings_Section_Support_Templates::add_section_instance();
	}
);
