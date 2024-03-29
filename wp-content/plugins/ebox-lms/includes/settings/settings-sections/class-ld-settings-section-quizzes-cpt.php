<?php
/**
 * ebox Settings Section for Quiz Custom Post Type Metabox.
 *
 * @since 2.6.0
 * @package ebox\Settings\Sections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Section' ) ) && ( ! class_exists( 'ebox_Settings_Quizzes_CPT' ) ) ) {
	/**
	 * Class ebox Settings Section for Quiz Custom Post Type Metabox.
	 *
	 * @since 2.6.0
	 */
	class ebox_Settings_Quizzes_CPT extends ebox_Settings_Section {

		/**
		 * Protected constructor for class
		 *
		 * @since 2.6.0
		 */
		protected function __construct() {
			// What screen ID are we showing on.
			$this->settings_screen_id = 'ebox-quiz_page_quizzes-options';

			// The page ID (different than the screen ID).
			$this->settings_page_id = 'quizzes-options';

			// This is the 'option_name' key used in the wp_options table.
			$this->setting_option_key = 'ebox_settings_quizzes_cpt';

			// This is the HTML form field prefix used.
			$this->setting_field_prefix = 'ebox_settings_quizzes_cpt';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_section_key = 'cpt_options';

			// Section label/header.
			$this->settings_section_label = sprintf(
				// translators: placeholder: Quiz.
				esc_html_x( '%s Custom Post Type Options', 'placeholder: Quiz', 'ebox' ),
				ebox_Custom_Label::get_label( 'quiz' )
			);

			// Used to show the section description above the fields. Can be empty.
			$this->settings_section_description = sprintf(
				// translators: placeholder: Quizzes.
				esc_html_x( 'Control the ebox %s Custom Post Type Options.', 'placeholder: Quizzes', 'ebox' ),
				ebox_Custom_Label::get_label( 'quizzes' )
			);

			parent::__construct();
		}

		/**
		 * Initialize the metabox settings values.
		 *
		 * @since 2.6.0
		 */
		public function load_settings_values() {
			parent::load_settings_values();

			if ( ( false === $this->setting_option_values ) || ( '' === $this->setting_option_values ) ) {
				if ( '' === $this->setting_option_values ) {
					$this->setting_option_values = array();
				}

				$this->setting_option_values = array(
					'include_in_search'    => '',
					'search_login_only'    => '',
					'search_enrolled_only' => '',
					'has_archive'          => '',
					'has_feed'             => '',
					'supports'             => array( 'thumbnail', 'revisions' ),
				);
			}

			if ( ! isset( $this->setting_option_values['include_in_search'] ) ) {
				if ( ( isset( $this->setting_option_values['exclude_from_search'] ) ) && ( 'yes' === $this->setting_option_values['exclude_from_search'] ) ) {
					$this->setting_option_values['include_in_search'] = '';
				} else {
					$this->setting_option_values['include_in_search'] = 'yes';
				}
			}

			if ( ! isset( $this->setting_option_values['search_login_only'] ) ) {
				$this->setting_option_values['search_login_only'] = '';
			}

			if ( ! isset( $this->setting_option_values['search_enrolled_only'] ) ) {
				$this->setting_option_values['search_enrolled_only'] = '';
			}

			if ( ! isset( $this->setting_option_values['has_archive'] ) ) {
				$this->setting_option_values['has_archive'] = '';
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
		 * @since 2.6.0
		 */
		public function load_settings_fields() {
			$cpt_archive_url = home_url( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_Permalinks', 'quizzes' ) );
			$cpt_rss_url     = add_query_arg( 'post_type', 'ebox-quiz', get_post_type_archive_feed_link( 'post' ) );

			$this->setting_option_fields = array(
				'include_in_search'    => array(
					'name'                => 'include_in_search',
					'type'                => 'checkbox-switch',
					'label'               => sprintf(
						// translators: placeholder: Quiz.
						esc_html_x( '%s Search', 'placeholder: Quiz', 'ebox' ),
						ebox_get_custom_label( 'quiz' )
					),
					'help_text'           => sprintf(
						// translators: placeholder: quiz.
						esc_html_x( 'Includes the %s post type in front end search results', 'placeholder: quiz', 'ebox' ),
						ebox_get_custom_label_lower( 'quiz' )
					),
					'value'               => $this->setting_option_values['include_in_search'],
					'options'             => array(
						'yes' => '',
					),
					'child_section_state' => ( 'yes' === $this->setting_option_values['include_in_search'] ) ? 'open' : 'closed',
				),
				'search_login_only'    => array(
					'name'           => 'search_login_only',
					'type'           => 'checkbox-switch',
					'label'          => esc_html__( 'Logged-in User only', 'ebox' ),
					'help_text'      => sprintf(
					// translators: placeholder: Quizzes.
						esc_html_x( 'Include %s only if user is logged in.', 'placeholder: Quizzes', 'ebox' ),
						ebox_get_custom_label( 'quizzes' )
					),
					'value'          => $this->setting_option_values['search_login_only'],
					'options'        => array(
						''    => '',
						'yes' => '',
					),
					'parent_setting' => 'include_in_search',
				),
				'search_enrolled_only' => array(
					'name'           => 'search_enrolled_only',
					'type'           => 'checkbox-switch',
					'label'          => esc_html__( 'Enrolled only', 'ebox' ),
					'help_text'      => sprintf(
						// translators: placeholder: Quizzes.
						esc_html_x( 'Include %s only if user is enrolled.', 'placeholder: Quizzes', 'ebox' ),
						ebox_get_custom_label( 'quizzes' )
					),
					'value'          => $this->setting_option_values['search_enrolled_only'],
					'options'        => array(
						''    => '',
						'yes' => '',
					),
					'parent_setting' => 'include_in_search',
				),
				'has_archive'          => array(
					'name'                => 'has_archive',
					'type'                => 'checkbox-switch',
					'label'               => esc_html__( 'Archive Page', 'ebox' ),
					'help_text'           => sprintf(
						// translators: placeholder: quizzes, link to WP Permalinks page.
						esc_html_x( 'Enables the front end archive page where all %1$s are listed. You must %2$s for the change to take effect.', 'placeholder: quizzes, link to WP Permalinks page', 'ebox' ),
						ebox_get_custom_label_lower( 'quizzes' ),
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
				'has_feed'             => array(
					'name'           => 'has_feed',
					'type'           => 'checkbox-switch',
					'label'          => esc_html__( 'RSS/Atom Feed', 'ebox' ),
					'help_text'      => sprintf(
						// translators: placeholder: quiz.
						esc_html_x( 'Enables an RSS feed for all %1$s posts.', 'placeholder: quiz', 'ebox' ),
						ebox_get_custom_label_lower( 'quiz' )
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
				'supports'             => array(
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

			if ( ( ! defined( 'ebox_FILTER_SEARCH' ) ) || ( ebox_FILTER_SEARCH !== true ) ) {
				unset( $this->setting_option_fields['search_login_only'] );
				unset( $this->setting_option_fields['search_enrolled_only'] );
			}

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
			$this->setting_option_fields = apply_filters( 'ebox_settings_fields', $this->setting_option_fields, $this->settings_section_key );

			parent::load_settings_fields();
		}

		/**
		 * Intercept the WP options save logic and check that we have a valid nonce.
		 *
		 * @since 2.6.0
		 *
		 * @param array  $new_values         Array of section fields values.
		 * @param array  $old_values         Array of old values.
		 * @param string $setting_option_key Section option key should match $this->setting_option_key.
		 */
		public function section_pre_update_option( $new_values = '', $old_values = '', $setting_option_key = '' ) {
			if ( $setting_option_key === $this->setting_option_key ) {
				$new_values = parent::section_pre_update_option( $new_values, $old_values, $setting_option_key );
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

				if ( $new_values !== $old_values ) {
					if ( ( ! isset( $old_values['has_archive'] ) ) || ( $new_values['has_archive'] !== $old_values['has_archive'] ) ) {
						ebox_setup_rewrite_flush();
					}
				}
			}

			return $new_values;
		}

		// End of functions.
	}
}

add_action(
	'ebox_settings_sections_init',
	function() {
		ebox_Settings_Quizzes_CPT::add_section_instance();
	}
);
