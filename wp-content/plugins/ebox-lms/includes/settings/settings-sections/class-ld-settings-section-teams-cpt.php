<?php
/**
 * ebox Settings Section for Teams Custom Post Type Metabox.
 *
 * @since 3.2.0
 * @package ebox\Settings\Sections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Section' ) ) && ( ! class_exists( 'ebox_Settings_Teams_CPT' ) ) ) {
	/**
	 * Class ebox Settings Section for Teams Custom Post Type Metabox.
	 *
	 * @since 3.2.0
	 */
	class ebox_Settings_Teams_CPT extends ebox_Settings_Section {

		/**
		 * Protected constructor for class
		 *
		 * @since 3.2.0
		 */
		protected function __construct() {

			// What screen ID are we showing on.
			$this->settings_screen_id = 'teams_page_teams-options';

			// The page ID (different than the screen ID).
			$this->settings_page_id = 'teams-options';

			// This is the 'option_name' key used in the wp_options table.
			$this->setting_option_key = 'ebox_settings_teams_cpt';

			// This is the HTML form field prefix used.
			$this->setting_field_prefix = 'ebox_settings_teams_cpt';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_section_key = 'cpt_options';

			// Section label/header.
			$this->settings_section_label = sprintf(
				// translators: placeholder: Team.
				esc_html_x( '%s Custom Post Type Options', 'Team Custom Post Type Options', 'ebox' ),
				ebox_get_custom_label( 'team' )
			);

			// Used to show the section description above the fields. Can be empty.
			$this->settings_section_description = sprintf(
				// translators: placeholder: Team.
				esc_html_x( 'Control the ebox %s Custom Post Type Options.', 'placeholder: Team', 'ebox' ),
				ebox_get_custom_label( 'team' )
			);

			add_filter( 'ebox_settings_row_outside_before', array( $this, 'ebox_settings_row_outside_before' ), 30, 2 );

			parent::__construct();
		}

		/**
		 * Initialize the metabox settings values.
		 *
		 * @since 3.2.0
		 */
		public function load_settings_values() {
			parent::load_settings_values();

			if ( ( false === $this->setting_option_values ) || ( '' === $this->setting_option_values ) ) {
				if ( '' === $this->setting_option_values ) {
					$this->setting_option_values = array();
				}

				$ld_prior_version = ebox_data_upgrades_setting( 'prior_version' );

				$this->setting_option_values = array(
					'public'            => ( 'new' === $ld_prior_version ) ? 'yes' : '',
					'include_in_search' => 'yes',
					'has_archive'       => 'yes',
					'has_feed'          => '',
					'supports'          => array( 'thumbnail', 'revisions' ),
				);
			}

			if ( ! isset( $this->setting_option_values['public'] ) ) {
				$this->setting_option_values['public'] = '';
			}

			if ( ! isset( $this->setting_option_values['include_in_search'] ) ) {
				if ( ( isset( $this->setting_option_values['exclude_from_search'] ) ) && ( 'yes' === $this->setting_option_values['exclude_from_search'] ) ) {
					$this->setting_option_values['include_in_search'] = '';
				} else {
					$this->setting_option_values['include_in_search'] = 'yes';
				}
			}

			if ( ! isset( $this->setting_option_values['has_archive'] ) ) {
				$this->setting_option_values['has_archive'] = 'yes';
			}

			if ( ! isset( $this->setting_option_values['has_feed'] ) ) {
				$this->setting_option_values['has_feed'] = '';
			}

			if ( ! isset( $this->setting_option_values['supports'] ) ) {
				$this->setting_option_values['supports'] = array( 'thumbnail', 'revisions' );
			}
		}

		/**
		 * Initialize the metabox settings fields.
		 *
		 * @since 3.2.0
		 */
		public function load_settings_fields() {
			$cpt_archive_url = home_url( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_Permalinks', 'teams' ) );
			$cpt_rss_url     = add_query_arg( 'post_type', 'ebox-courses', get_post_type_archive_feed_link( 'post' ) );

			$this->setting_option_fields = array(
				'public'            => array(
					'name'      => 'public',
					'type'      => 'checkbox-switch',
					'label'     => esc_html__( 'Public', 'ebox' ),
					'help_text' => sprintf(
						// translators: placeholder: Teams.
						esc_html_x( '%s are public on the front-end', 'placeholder: Teams', 'ebox' ),
						ebox_get_custom_label( 'teams' )
					),
					'value'     => $this->setting_option_values['public'],
					'options'   => array(
						'yes' => '',
					),
				),
				'include_in_search' => array(
					'name'      => 'include_in_search',
					'type'      => 'checkbox-switch',
					'label'     => sprintf(
						// translators: placeholder: Team.
						esc_html_x( '%s Search', 'placeholder: Team', 'ebox' ),
						ebox_get_custom_label( 'team' )
					),
					'help_text' => sprintf(
						// translators: placeholder: team.
						esc_html_x( 'Includes the %s post type in front end search results', 'placeholder: team', 'ebox' ),
						ebox_get_custom_label_lower( 'team' )
					),
					'value'     => $this->setting_option_values['include_in_search'],
					'options'   => array(
						'yes' => '',
					),
				),
				'has_archive'       => array(
					'name'                => 'has_archive',
					'type'                => 'checkbox-switch',
					'label'               => esc_html__( 'Archive Page', 'ebox' ),
					'help_text'           => sprintf(
						// translators: placeholders: teams, link to WP Permalinks page.
						esc_html_x( 'Enables the front end archive page where all %1$s are listed. You must %2$s for the change to take effect.', 'placeholders: teams, link to WP Permalinks page', 'ebox' ),
						ebox_get_custom_label_lower( 'teams' ),
						'<a href="' . admin_url( 'options-permalink.php' ) . '">' . esc_html__( 're-save your permalinks', 'ebox' ) . '</a>'
					),
					'value'               => $this->setting_option_values['has_archive'],
					'options'             => array(
						''    => '',
						'yes' => sprintf(
							// translators: placeholder: URL for CPT Archive.
							esc_html_x( 'Archive URL: %s', 'placeholder: URL for CPT Archive', 'ebox' ),
							'<code><a target="blank" href="' . $cpt_archive_url . '">' . $cpt_archive_url . '</a></code>'
						),
					),
					'child_section_state' => ( 'yes' === $this->setting_option_values['has_archive'] ) ? 'open' : 'closed',
				),
				'has_feed'          => array(
					'name'           => 'has_feed',
					'type'           => 'checkbox-switch',
					'label'          => esc_html__( 'RSS/Atom Feed', 'ebox' ),
					'help_text'      => sprintf(
						// translators: placeholder: team.
						esc_html_x( 'Enables an RSS feed for all %s posts.', 'placeholder: team', 'ebox' ),
						ebox_get_custom_label_lower( 'team' )
					),
					'value'          => $this->setting_option_values['has_feed'],
					'options'        => array(
						''    => '',
						'yes' => sprintf(
							// translators: placeholder: URL for CPT Archive.
							esc_html_x( 'RSS Feed URL: %s', 'placeholder: URL for RSS Feed', 'ebox' ),
							'<code><a target="blank" href="' . $cpt_rss_url . '">' . $cpt_rss_url . '</a></code>'
						),
					),
					'parent_setting' => 'has_archive',
				),
				'supports'          => array(
					'name'      => 'supports',
					'type'      => 'checkbox',
					'label'     => esc_html__( 'Editor Supported Settings', 'ebox' ),
					'help_text' => esc_html__( 'Enables WordPress supported settings within the editor and theme.', 'ebox' ),
					'value'     => $this->setting_option_values['supports'],
					'options'   => array(
						'thumbnail'     => esc_html__( 'Featured image', 'ebox' ),
						'comments'      => esc_html__( 'Comments', 'ebox' ),
						'custom-fields' => esc_html__( 'Custom Fields', 'ebox' ),
						'revisions'     => esc_html__( 'Revisions', 'ebox' ),
					),
				),
			);

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
			$this->setting_option_fields = apply_filters( 'ebox_settings_fields', $this->setting_option_fields, $this->settings_section_key );

			parent::load_settings_fields();
		}

		/**
		 * Intercept the WP options save logic and check that we have a valid nonce.
		 *
		 * @since 3.2.0
		 *
		 * @param array  $new_values         Array of section fields values.
		 * @param array  $old_values         Array of old values.
		 * @param string $setting_option_key Section option key should match $this->setting_option_key.
		 */
		public function section_pre_update_option( $new_values = '', $old_values = '', $setting_option_key = '' ) {
			if ( $setting_option_key === $this->setting_option_key ) {
				$new_values = parent::section_pre_update_option( $new_values, $old_values, $setting_option_key );
				if ( ! isset( $new_values['public'] ) ) {
					$new_values['public'] = '';
				}
				if ( ! isset( $new_values['include_in_search'] ) ) {
					$new_values['include_in_search'] = '';
				}

				if ( ! isset( $new_values['has_archive'] ) ) {
					$new_values['has_archive'] = '';
					$new_values['has_feed']    = '';
				}

				if ( ! isset( $new_values['has_feed'] ) ) {
					$new_values['has_feed'] = '';
				}

				if ( ! isset( $new_values['supports'] ) ) {
					$new_values['supports'] = array();
				}

				if ( 'yes' !== $new_values['public'] ) {
					$new_values['include_in_search'] = '';
					$new_values['has_archive']       = '';
					$new_values['has_feed']          = '';
				}

				if ( $new_values !== $old_values ) {
					if ( ( ! isset( $old_values['has_archive'] ) ) || ( $new_values['has_archive'] !== $old_values['has_archive'] ) ) {
						ebox_setup_rewrite_flush();
					}
				}
			}

			return $new_values;
		}

		/**
		 * Settings row outside before
		 *
		 * @since 3.2.0
		 *
		 * @param string $content    Content to show before row.
		 * @param array  $field_args Row field Args.
		 */
		public function ebox_settings_row_outside_before( $content = '', $field_args = array() ) {
			if ( ( isset( $field_args['name'] ) ) && ( in_array( $field_args['name'], array( 'public' ), true ) ) && ebox_Settings_Section::get_section_setting( 'ebox_Settings_Teams_CPT', 'public' ) === '' ) {

				$content .= '<div class="ld-settings-info-banner ld-settings-info-banner-alert">';

				$message = sprintf(
					// translators: placeholder: team.
					esc_html_x(
						'%s is not set to public, set to Public to allow access on the front end.',
						'placeholder: team',
						'ebox'
					),
					ebox_get_custom_label( 'team' )
				);
				$content .= wpautop( wptexturize( do_shortcode( $message ) ) );
				$content .= '</div>';
			}
			return $content;
		}

		// End of functions.
	}
}

add_action(
	'ebox_settings_sections_init',
	function() {
		ebox_Settings_Teams_CPT::add_section_instance();
	}
);
