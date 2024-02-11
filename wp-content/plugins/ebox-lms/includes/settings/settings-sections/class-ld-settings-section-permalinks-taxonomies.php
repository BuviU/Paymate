<?php
/**
 * ebox Settings Section for Permalink Taxonomies.
 *
 * These are shown are input fields on the WP Settings > Permalinks
 * page to allow override of the default slugs
 *
 * @since 2.5.8
 * @package ebox\Settings\Sections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Section' ) ) && ( ! class_exists( 'ebox_Settings_Section_Permalinks_Taxonomies' ) ) ) {
	/**
	 * Class ebox Settings Section for Permalink Taxonomies.
	 *
	 * @since 2.5.8
	 */
	class ebox_Settings_Section_Permalinks_Taxonomies extends ebox_Settings_Section {

		/**
		 * Protected constructor for class
		 *
		 * @since 2.5.8
		 */
		protected function __construct() {
			$this->settings_page_id = 'permalink';

			// This is the 'option_name' key used in the wp_options table.
			$this->setting_option_key = 'ebox_settings_permalinks_taxonomies';

			// This is the HTML form field prefix used.
			$this->setting_field_prefix = 'ebox_settings_permalinks_taxonomies';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_section_key = 'ebox_settings_permalinks_taxonomies';

			// Section label/header.
			$this->settings_section_label = __( 'ebox Taxonomy Permalinks', 'ebox' );

			// Used to show the section description above the fields. Can be empty.
			$this->settings_section_description = __( 'Controls the URL slugs for the custom taxonomies used by ebox.', 'ebox' );

			add_action( 'admin_init', array( $this, 'admin_init' ) );

			global $wp_rewrite;
			if ( $wp_rewrite->using_permalinks() ) {
				parent::__construct();
				$this->save_settings_fields();
			}
		}

		/**
		 * Function to hook into WP admin init action.
		 *
		 * @since 2.5.8
		 */
		public function admin_init() {
			/** This filter is documented in includes/settings/class-ld-settings-pages.php */
			do_action( 'ebox_settings_page_init', $this->settings_page_id );
		}

		/**
		 * Function to handle metabox init.
		 *
		 * @since 2.5.8
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
		 * @since 2.5.8
		 */
		public function load_settings_values() {
			parent::load_settings_values();

			if ( false === $this->setting_option_values ) {
				$this->setting_option_values = array();
			}

			$this->setting_option_values = wp_parse_args(
				$this->setting_option_values,
				array(
					'ld_course_category' => 'course-category',
					'ld_course_tag'      => 'course-tag',
					'ld_lesson_category' => 'lesson-category',
					'ld_lesson_tag'      => 'lesson-tag',
					'ld_topic_category'  => 'topic-category',
					'ld_topic_tag'       => 'topic-tag',
					'ld_quiz_category'   => 'quiz-category',
					'ld_quiz_tag'        => 'quiz-tag',
					'ld_team_category'  => 'team-category',
					'ld_team_tag'       => 'team-tag',
				)
			);
		}

		/**
		 * Initialize the metabox settings fields.
		 *
		 * @since 2.5.8
		 */
		public function load_settings_fields() {
			global $ebox_lms;

			$this->setting_option_fields = array();

			// Course Taxonomies.
			$courses_taxonomies = $ebox_lms->get_post_args_section( 'ebox-courses', 'taxonomies' );
			if ( ( isset( $courses_taxonomies['ld_course_category'] ) ) && ( true === $courses_taxonomies['ld_course_category']['public'] ) ) {
				$this->setting_option_fields['ld_course_category'] = array(
					'name'  => 'ld_course_category',
					'type'  => 'text',
					'label' => sprintf(
						// translators: placeholder: Course.
						_x( '%s Category base', 'placeholder: Course', 'ebox' ),
						ebox_Custom_Label::get_label( 'course' )
					),
					'value' => $this->setting_option_values['ld_course_category'],
					'class' => 'regular-text',
				);
			}

			if ( ( isset( $courses_taxonomies['ld_course_tag'] ) ) && ( true === $courses_taxonomies['ld_course_tag']['public'] ) ) {
				$this->setting_option_fields['ld_course_tag'] = array(
					'name'  => 'ld_course_tag',
					'type'  => 'text',
					'label' => sprintf(
						// translators: placeholder: Course.
						_x( '%s Tag base', 'placeholder: Course', 'ebox' ),
						ebox_Custom_Label::get_label( 'course' )
					),
					'value' => $this->setting_option_values['ld_course_tag'],
					'class' => 'regular-text',
				);
			}

			// Lesson Taxonomies.
			$modules_taxonomies = $ebox_lms->get_post_args_section( 'ebox-modules', 'taxonomies' );
			if ( ( isset( $modules_taxonomies['ld_lesson_category'] ) ) && ( true === $modules_taxonomies['ld_lesson_category']['public'] ) ) {
				$this->setting_option_fields['ld_lesson_category'] = array(
					'name'  => 'ld_lesson_category',
					'type'  => 'text',
					'label' => sprintf(
						// translators: placeholder: Lesson.
						_x( '%s Category base', 'placeholder: Lesson', 'ebox' ),
						ebox_Custom_Label::get_label( 'lesson' )
					),
					'value' => $this->setting_option_values['ld_lesson_category'],
					'class' => 'regular-text',
				);
			}

			if ( ( isset( $modules_taxonomies['ld_lesson_tag'] ) ) && ( true === $modules_taxonomies['ld_lesson_tag']['public'] ) ) {
				$this->setting_option_fields['ld_lesson_tag'] = array(
					'name'  => 'ld_lesson_tag',
					'type'  => 'text',
					'label' => sprintf(
						// translators: placeholder: Lesson.
						_x( '%s Tag base', 'placeholder: Lesson', 'ebox' ),
						ebox_Custom_Label::get_label( 'lesson' )
					),
					'value' => $this->setting_option_values['ld_lesson_tag'],
					'class' => 'regular-text',
				);
			}

			// Topic Taxonomies.
			$topics_taxonomies = $ebox_lms->get_post_args_section( 'ebox-topic', 'taxonomies' );
			if ( ( isset( $topics_taxonomies['ld_topic_category'] ) ) && ( true === $topics_taxonomies['ld_topic_category']['public'] ) ) {
				$this->setting_option_fields['ld_topic_category'] = array(
					'name'  => 'ld_topic_category',
					'type'  => 'text',
					'label' => sprintf(
						// translators: placeholder: Topic.
						_x( '%s Category base', 'placeholder: Topic', 'ebox' ),
						ebox_Custom_Label::get_label( 'topic' )
					),
					'value' => $this->setting_option_values['ld_topic_category'],
					'class' => 'regular-text',
				);
			}

			if ( ( isset( $topics_taxonomies['ld_topic_tag'] ) ) && ( true === $topics_taxonomies['ld_topic_tag']['public'] ) ) {
				$this->setting_option_fields['ld_topic_tag'] = array(
					'name'  => 'ld_topic_tag',
					'type'  => 'text',
					'label' => sprintf(
						// translators: placeholder: Topic.
						_x( '%s Tag base', 'placeholder: Topic', 'ebox' ),
						ebox_Custom_Label::get_label( 'topic' )
					),
					'value' => $this->setting_option_values['ld_topic_tag'],
					'class' => 'regular-text',
				);
			}

			// Quiz Taxonomies.
			$quizzes_taxonomies = $ebox_lms->get_post_args_section( 'ebox-quiz', 'taxonomies' );
			if ( ( isset( $quizzes_taxonomies['ld_quiz_category'] ) ) && ( true === $quizzes_taxonomies['ld_quiz_category']['public'] ) ) {
				$this->setting_option_fields['ld_quiz_category'] = array(
					'name'  => 'ld_quiz_category',
					'type'  => 'text',
					'label' => sprintf(
						// translators: placeholder: Quiz.
						_x( '%s Category base', 'placeholder: Quiz', 'ebox' ),
						ebox_Custom_Label::get_label( 'quiz' )
					),
					'value' => $this->setting_option_values['ld_quiz_category'],
					'class' => 'regular-text',
				);
			}

			if ( ( isset( $quizzes_taxonomies['ld_quiz_tag'] ) ) && ( true === $quizzes_taxonomies['ld_quiz_tag']['public'] ) ) {
				$this->setting_option_fields['ld_quiz_tag'] = array(
					'name'  => 'ld_quiz_tag',
					'type'  => 'text',
					'label' => sprintf(
						// translators: placeholder: Quiz.
						_x( '%s Tag base', 'placeholder: Quiz', 'ebox' ),
						ebox_Custom_Label::get_label( 'quiz' )
					),
					'value' => $this->setting_option_values['ld_quiz_tag'],
					'class' => 'regular-text',
				);
			}

			// Team Taxonomies.
			$teams_taxonomies = $ebox_lms->get_post_args_section( 'teams', 'taxonomies' );
			if ( ( isset( $teams_taxonomies['ld_team_category'] ) ) && ( true === $teams_taxonomies['ld_team_category']['public'] ) ) {
				$this->setting_option_fields['ld_team_category'] = array(
					'name'  => 'ld_team_category',
					'type'  => 'text',
					'label' => sprintf(
						// translators: placeholder: Team.
						_x( '%s Category base', 'placeholder: Team', 'ebox' ),
						ebox_Custom_Label::get_label( 'team' )
					),
					'value' => $this->setting_option_values['ld_team_category'],
					'class' => 'regular-text',
				);
			}

			if ( ( isset( $teams_taxonomies['ld_team_tag'] ) ) && ( true === $teams_taxonomies['ld_team_tag']['public'] ) ) {
				$this->setting_option_fields['ld_team_tag'] = array(
					'name'  => 'ld_team_tag',
					'type'  => 'text',
					'label' => sprintf(
						// translators: placeholder: Team.
						_x( '%s Tag base', 'placeholder: Team', 'ebox' ),
						ebox_Custom_Label::get_label( 'team' )
					),
					'value' => $this->setting_option_values['ld_team_tag'],
					'class' => 'regular-text',
				);
			}

			if ( ! empty( $this->setting_option_fields ) ) {
				$this->setting_option_fields['nonce'] = array(
					'name'  => 'nonce',
					'type'  => 'hidden',
					'label' => '',
					'value' => wp_create_nonce( 'ebox_permalinks_taxonomies_nonce' ),
					'class' => 'hidden',
				);
			}

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
			$this->setting_option_fields = apply_filters( 'ebox_settings_fields', $this->setting_option_fields, $this->settings_section_key );

			parent::load_settings_fields();
		}

		/**
		 * Save the metabox fields. This is needed due to special processing needs.
		 *
		 * @since 2.5.8
		 */
		public function save_settings_fields() {
			if ( isset( $_POST[ $this->setting_field_prefix ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				if ( $this->verify_metabox_nonce_field() ) {
					$post_fields = $_POST[ $this->setting_field_prefix ]; // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

					foreach ( array( 'course', 'lesson', 'topic', 'quiz', 'team' ) as $slug ) {

						if ( ( isset( $post_fields[ 'ld_' . $slug . '_category' ] ) ) && ( ! empty( $post_fields[ 'ld_' . $slug . '_category' ] ) ) ) {
							$this->setting_option_values[ 'ld_' . $slug . '_category' ] = $this->esc_url( $post_fields[ 'ld_' . $slug . '_category' ] );

							ebox_setup_rewrite_flush();
						}

						if ( ( isset( $post_fields[ 'ld_' . $slug . '_tag' ] ) ) && ( ! empty( $post_fields[ 'ld_' . $slug . '_tag' ] ) ) ) {
							$this->setting_option_values[ 'ld_' . $slug . '_tag' ] = $this->esc_url( $post_fields[ 'ld_' . $slug . '_tag' ] );

							ebox_setup_rewrite_flush();
						}
					}

					update_option( $this->settings_section_key, $this->setting_option_values );
				}
			}
		}

		/**
		 * Class utility function to escape the URL
		 *
		 * @since 2.5.8
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
			if ( ( isset( $_POST[ $this->setting_field_prefix ]['nonce'] ) ) && ( wp_verify_nonce( $_POST[ $this->setting_field_prefix ]['nonce'], 'ebox_permalinks_taxonomies_nonce' ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
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
		ebox_Settings_Section_Permalinks_Taxonomies::add_section_instance();
	}
);
