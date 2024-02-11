<?php
/**
 * ebox Settings Section for Permalinks section shown on WP Settings > Permalinks page.
 *
 * @since 2.4.0
 * @package ebox\Settings\Sections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Section' ) ) && ( ! class_exists( 'ebox_Settings_Section_Permalinks' ) ) ) {
	/**
	 * Class ebox Settings Section for Permalinks section shown on WP Settings > Permalinks page.
	 *
	 * @since 2.4.0
	 */
	class ebox_Settings_Section_Permalinks extends ebox_Settings_Section {

		/**
		 * Protected constructor for class
		 *
		 * @since 2.4.0
		 */
		protected function __construct() {
			$this->settings_page_id = 'permalink';

			// This is the 'option_name' key used in the wp_options table.
			$this->setting_option_key = 'ebox_settings_permalinks';

			// This is the HTML form field prefix used.
			$this->setting_field_prefix = 'ebox_settings_permalinks';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_section_key = 'ebox_settings_permalinks';

			// Section label/header.
			$this->settings_section_label = __( 'ebox Permalinks', 'ebox' );

			// Used to show the section description above the fields. Can be empty.
			$this->settings_section_description = __( 'Controls the URL slugs for the custom posts used by ebox.', 'ebox' );

			add_action( 'admin_init', array( $this, 'admin_init' ) );

			parent::__construct();
			$this->save_settings_fields();
		}

		/**
		 * Hook into the admin init action to fire up the LD settings page init processing.
		 * Remember the Permalinks page is not a LD page.
		 *
		 * @since 2.4.0
		 */
		public function admin_init() {
			/** This filter is documented in includes/settings/class-ld-settings-pages.php */
			do_action( 'ebox_settings_page_init', $this->settings_page_id );
		}

		/**
		 * Function to handle metabox init.
		 *
		 * @since 2.4.0
		 *
		 * @param string $settings_screen_id Screen ID of current page.
		 */
		public function add_meta_boxes( $settings_screen_id = '' ) {
			global $wp_rewrite;
			if ( $wp_rewrite->using_permalinks() ) {

				add_meta_box(
					$this->metabox_key,
					$this->settings_section_label,
					array( $this, 'show_meta_box' ),
					$this->settings_screen_id,
					$this->metabox_context,
					$this->metabox_priority
				);
			}
		}

		/**
		 * Initialize the metabox settings values.
		 *
		 * @since 2.4.0
		 */
		public function load_settings_values() {
			parent::load_settings_values();

			if ( false === $this->setting_option_values ) {
				$this->setting_option_values = array();

				// On the initial if we don't have saved values we grab them from the Custom Labels.
				$custom_label_settings = get_option( 'ebox_custom_label_settings', array() );

				if ( ( isset( $custom_label_settings['courses'] ) ) && ( ! empty( $custom_label_settings['courses'] ) ) ) {
					$this->setting_option_values['courses'] = ebox_get_custom_label_slug( 'courses' );
				} else {
					$this->setting_option_values['courses'] = 'courses';
				}

				if ( ( isset( $custom_label_settings['modules'] ) ) && ( ! empty( $custom_label_settings['modules'] ) ) ) {
					$this->setting_option_values['modules'] = ebox_get_custom_label_slug( 'modules' );
				} else {
					$this->setting_option_values['modules'] = 'modules';
				}

				if ( ( isset( $custom_label_settings['topic'] ) ) && ( ! empty( $custom_label_settings['topic'] ) ) ) {
					$this->setting_option_values['topics'] = ebox_get_custom_label_slug( 'topic' );
				} else {
					$this->setting_option_values['topics'] = 'topics';
				}

				if ( ( isset( $custom_label_settings['quizzes'] ) ) && ( ! empty( $custom_label_settings['quizzes'] ) ) ) {
					$this->setting_option_values['quizzes'] = ebox_get_custom_label_slug( 'quizzes' );
				} else {
					$this->setting_option_values['quizzes'] = 'quizzes';
				}

				if ( ( isset( $custom_label_settings['teams'] ) ) && ( ! empty( $custom_label_settings['teams'] ) ) ) {
					$this->setting_option_values['teams'] = ebox_get_custom_label_slug( 'teams' );
				} else {
					$this->setting_option_values['teams'] = 'teams';
				}

				$this->settings_bypass_nonce_check = true;

				// As we don't have existing values we want to save here and force the flush rewrite.
				update_option( $this->settings_section_key, $this->setting_option_values );
				ebox_setup_rewrite_flush();
			}

			$this->setting_option_values = wp_parse_args(
				$this->setting_option_values,
				array(
					'courses'     => 'courses',
					'modules'     => 'modules',
					'topics'      => 'topic',
					'quizzes'     => 'quizzes',
					'teams'      => 'teams',
					'nested_urls' => '',
				)
			);

			if ( ( ebox_is_course_shared_steps_enabled() ) && ( 'yes' !== $this->setting_option_values['nested_urls'] ) ) {
				$this->setting_option_values['nested_urls'] = 'yes';
				update_option( $this->settings_section_key, $this->setting_option_values );
				ebox_setup_rewrite_flush();
			}
		}

		/**
		 * Initialize the metabox settings fields.
		 *
		 * @since 2.4.0
		 */
		public function load_settings_fields() {
			global $wp_rewrite;
			if ( $wp_rewrite->using_permalinks() ) {
				$this->setting_option_fields = array(
					'courses' => array(
						'name'  => 'courses',
						'type'  => 'text',
						'label' => ebox_Custom_Label::get_label( 'courses' ),
						'value' => $this->setting_option_values['courses'],
						'class' => 'regular-text',
					),
					'modules' => array(
						'name'  => 'modules',
						'type'  => 'text',
						'label' => ebox_Custom_Label::get_label( 'modules' ),
						'value' => $this->setting_option_values['modules'],
						'class' => 'regular-text',
					),
					'topics'  => array(
						'name'  => 'topics',
						'type'  => 'text',
						'label' => ebox_Custom_Label::get_label( 'topics' ),
						'value' => $this->setting_option_values['topics'],
						'class' => 'regular-text',
					),
					'quizzes' => array(
						'name'  => 'quizzes',
						'type'  => 'text',
						'label' => ebox_Custom_Label::get_label( 'quizzes' ),
						'value' => $this->setting_option_values['quizzes'],
						'class' => 'regular-text',
					),
					'teams'  => array(
						'name'  => 'teams',
						'type'  => 'text',
						'label' => ebox_Custom_Label::get_label( 'teams' ),
						'value' => $this->setting_option_values['teams'],
						'class' => 'regular-text',
					),
				);
			}

			if ( $wp_rewrite->using_permalinks() ) {
				$example_regular_topic_url = get_option( 'home' ) . '/' . $this->setting_option_values['topics'] . '/topic-slug';
				$example_nested_topic_url  = get_option( 'home' ) . '/' . $this->setting_option_values['courses'] . '/course-slug/' . $this->setting_option_values['modules'] . '/lesson-slug/' . $this->setting_option_values['topics'] . '/topic-slug';
			} else {
				$example_regular_topic_url = add_query_arg( ebox_get_post_type_slug( 'topic' ), 'topic-slug', get_option( 'home' ) );

				$example_nested_topic_url = get_option( 'home' );
				$example_nested_topic_url = add_query_arg( ebox_get_post_type_slug( 'course' ), 'course-slug', $example_nested_topic_url );
				$example_nested_topic_url = add_query_arg( ebox_get_post_type_slug( 'lesson' ), 'lesson-slug', $example_nested_topic_url );
				$example_nested_topic_url = add_query_arg( ebox_get_post_type_slug( 'topic' ), 'topic-slug', $example_nested_topic_url );
			}

			$this->setting_option_fields['nested_urls'] = array(
				'name'    => 'nested_urls',
				'type'    => 'checkbox',
				'label'   => __( 'Enable Nested URLs', 'ebox' ),
				'desc'    => wp_kses_post(
					sprintf(
						// translators: placeholders: Lesson, Topic, Quiz, Course, topic, Site Home URL, URL to Course Builder Settings.
						_x(
							'This option will restructure %1$s, %2$s and %3$s URLs so they are nested hierarchically within the %4$s URL.<br />For example instead of the default %5$s URL <code>%6$s</code> the nested URL would be <code>%7$s</code>. If <a href="%7$s">Course Builder Share Steps</a> has been enabled this setting is also automatically enabled.',
							'placeholders: Lesson, Topic, Quiz, Course, topic, Site Home URL, URL to Course Builder Settings',
							'ebox'
						),
						ebox_get_custom_label( 'lesson' ),
						ebox_get_custom_label( 'topic' ),
						ebox_get_custom_label( 'quiz' ),
						ebox_get_custom_label( 'course' ),
						ebox_get_custom_label_lower( 'topic' ),
						$example_regular_topic_url,
						$example_nested_topic_url,
						admin_url( 'admin.php?page=courses-options' )
					)
				),
				'value'   => isset( $this->setting_option_values['nested_urls'] ) ? $this->setting_option_values['nested_urls'] : '',
				'options' => array(
					'yes' => __( 'Yes', 'ebox' ),
				),
			);

			$this->setting_option_fields['nonce'] = array(
				'name'  => 'nonce',
				'type'  => 'hidden',
				'label' => '',
				'value' => wp_create_nonce( 'ebox_permalinks_nonce' ),
				'class' => 'hidden',
			);

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
			$this->setting_option_fields = apply_filters( 'ebox_settings_fields', $this->setting_option_fields, $this->settings_section_key );

			parent::load_settings_fields();
		}

		/**
		 * Save the metabox fields. This is needed due to special processing needs.
		 *
		 * @since 2.4.0
		 */
		public function save_settings_fields() {

			if ( isset( $_POST[ $this->setting_field_prefix ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				if ( $this->verify_metabox_nonce_field() ) {
					$post_fields = $_POST[ $this->setting_field_prefix ]; // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

					if ( ( isset( $post_fields['courses'] ) ) && ( ! empty( $post_fields['courses'] ) ) ) {
						$this->setting_option_values['courses'] = $this->esc_url( $post_fields['courses'] );

						ebox_setup_rewrite_flush();
					}

					if ( ( isset( $post_fields['modules'] ) ) && ( ! empty( $post_fields['modules'] ) ) ) {
						$this->setting_option_values['modules'] = $this->esc_url( $post_fields['modules'] );

						ebox_setup_rewrite_flush();
					}

					if ( ( isset( $post_fields['topics'] ) ) && ( ! empty( $post_fields['topics'] ) ) ) {
						$this->setting_option_values['topics'] = $this->esc_url( $post_fields['topics'] );

						ebox_setup_rewrite_flush();
					}

					if ( ( isset( $post_fields['quizzes'] ) ) && ( ! empty( $post_fields['quizzes'] ) ) ) {
						$this->setting_option_values['quizzes'] = $this->esc_url( $post_fields['quizzes'] );

						ebox_setup_rewrite_flush();
					}

					if ( ( isset( $post_fields['teams'] ) ) && ( ! empty( $post_fields['teams'] ) ) ) {
						$this->setting_option_values['teams'] = $this->esc_url( $post_fields['teams'] );

						ebox_setup_rewrite_flush();
					}

					if ( ( isset( $post_fields['nested_urls'] ) ) && ( ! empty( $post_fields['nested_urls'] ) ) ) {
						$this->setting_option_values['nested_urls'] = $this->esc_url( $post_fields['nested_urls'] );

						ebox_setup_rewrite_flush();
					} else {
						// We check the Course Options > Course Builder setting. If this is set to 'yes' then we MUST keep the nested URLs set to true.
						if ( ! isset( $this->setting_option_values['nested_urls'] ) ) {
							$this->setting_option_values['nested_urls'] = 'no';
						}

						if ( 'yes' !== $this->setting_option_values['nested_urls'] ) {
							$ebox_settings_courses_builder = get_option( 'ebox_settings_courses_management_display', array() );
							if ( ! isset( $ebox_settings_courses_builder['course_builder_shared_steps'] ) ) {
								$ebox_settings_courses_builder['course_builder_shared_steps'] = 'no';
							}

							if ( 'yes' === $ebox_settings_courses_builder['course_builder_shared_steps'] ) {
								$this->setting_option_values['nested_urls'] = 'yes';

								ebox_setup_rewrite_flush();
							}
						}
					}

					update_option( $this->settings_section_key, $this->setting_option_values );
				}
			}
		}

		/**
		 * Class utility function to escape the URL
		 *
		 * @since 2.4.0
		 *
		 * @param string $value URL to Escape.
		 *
		 * @return string filtered URL.
		 */
		public function esc_url( $value = '' ) {
			if ( ! empty( $value ) ) {
				$value = esc_url_raw( trim( $value ) );
				$value = str_replace( 'http://', '', $value );
				return untrailingslashit( $value );
			}
			return '';
		}

		/**
		 * Verify Settings Section nonce field POST value.
		 *
		 * @since 3.6.0.1
		 */
		public function verify_metabox_nonce_field() {
			if ( ( true === $this->settings_bypass_nonce_check ) || ( ( isset( $_POST[ $this->setting_field_prefix ]['nonce'] ) ) && ( wp_verify_nonce( $_POST[ $this->setting_field_prefix ]['nonce'], 'ebox_permalinks_nonce' ) ) ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				return true;
			}

			return false;
		}

		/**
		 * Show Settings Section Description
		 *
		 * @since 3.6.0.1
		 */
		public function show_settings_section_description() {

			if ( ! empty( $this->settings_section_description ) ) {
				echo wp_kses_post( wpautop( $this->settings_section_description ) );
			}
		}


		// End of functions.
	}
}
add_action(
	'ebox_settings_sections_init',
	function() {
		ebox_Settings_Section_Permalinks::add_section_instance();
	}
);
