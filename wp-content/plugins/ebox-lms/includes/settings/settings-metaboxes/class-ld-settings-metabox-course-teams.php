<?php
/**
 * ebox Settings Metabox for Course Teams Settings.
 *
 * @since 3.2.0
 * @package ebox\Settings\Metaboxes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Metabox' ) ) && ( ! class_exists( 'ebox_Settings_Metabox_Course_Teams_Settings' ) ) ) {
	/**
	 * Class ebox Settings Metabox for Course Teams Settings.
	 *
	 * @since 3.2.0
	 */
	class ebox_Settings_Metabox_Course_Teams_Settings extends ebox_Settings_Metabox {

		/**
		 * Public constructor for class
		 *
		 * @since 3.2.0
		 */
		public function __construct() {
			// What screen ID are we showing on.
			$this->settings_screen_id = 'ebox-courses';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_metabox_key = 'ebox-course-teams';

			// Section label/header.
			$this->settings_section_label = sprintf(
				// translators: placeholder: Course, Teams.
				esc_html_x( '%1$s %2$s', 'placeholder: Course, Teams', 'ebox' ),
				ebox_get_custom_label( 'course' ),
				ebox_get_custom_label( 'teams' )
			);

			parent::__construct();
		}

		/**
		 * Show Settings Section Fields.
		 *
		 * @since 3.2.0
		 *
		 * @param object $metabox Metabox object.
		 */
		protected function show_settings_metabox_fields( $metabox = null ) {
			if ( ( is_object( $metabox ) ) && ( is_a( $metabox, 'ebox_Settings_Metabox' ) ) && ( $metabox->settings_metabox_key === $this->settings_metabox_key ) ) {
				if ( ( isset( $metabox->post ) ) && ( is_a( $metabox->post, 'WP_Post ' ) ) ) {
					$course_id = $metabox->post->ID;
				} else {
					$course_id = get_the_ID();
				}

				if ( ( ! empty( $course_id ) ) && ( get_post_type( $course_id ) === ebox_get_post_type_slug( 'course' ) ) ) {

					$metabox_description = '';

					// Use nonce for verification.
					wp_nonce_field( 'ebox_course_teams_nonce_' . $course_id, 'ebox_course_teams_nonce' );

					if ( ! empty( $metabox_description ) ) {
						$metabox_description .= ' ';
					}

					$metabox_description .= sprintf(
						// translators: placeholder: Teams, Course, Team.
						esc_html_x( 'Users enrolled via %1$s using this %2$s are excluded from the listings below and should be manage via the %3$s admin screen.', 'placeholder: Teams, Course, Team', 'ebox' ),
						ebox_Custom_Label::get_label( 'teams' ),
						ebox_Custom_Label::get_label( 'course' ),
						ebox_Custom_Label::get_label( 'team' )
					);
					?>
					<div id="ebox_course_users_page_box" class="ebox_course_users_page_box">
					<?php
					if ( ! empty( $metabox_description ) ) {
						echo wp_kses_post( wpautop( $metabox_description ) );
					}

					$ld_binary_selector_course_teams = new ebox_Binary_Selector_Course_Teams(
						array(
							'html_title'            => '',
							'course_id'             => $course_id,
							'selected_ids'          => ebox_get_course_teams( $course_id, true ),
							'search_posts_per_page' => 100,
						)
					);
					$ld_binary_selector_course_teams->show();
					?>
					</div>
					<?php
				}
			}
		}

		/**
		 * Save Settings Metabox
		 *
		 * @since 3.2.0
		 *
		 * @param integer $post_id $Post ID is post being saved.
		 * @param object  $saved_post WP_Post object being saved.
		 * @param boolean $update If update true, otherwise false.
		 * @param array   $settings_field_updates array of settings fields to update.
		 */
		public function save_post_meta_box( $post_id = 0, $saved_post = null, $update = null, $settings_field_updates = null ) {
			if ( ( isset( $_POST['ebox_course_teams_nonce'] ) ) && ( wp_verify_nonce( $_POST['ebox_course_teams_nonce'], 'ebox_course_teams_nonce_' . $post_id ) ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- This is a nonce.
				if ( ( isset( $_POST['ebox_course_teams'] ) ) && ( isset( $_POST['ebox_course_teams'][ $post_id ] ) ) && ( ! empty( $_POST['ebox_course_teams'][ $post_id ] ) ) && ( isset( $_POST[ 'ebox_course_teams-' . $post_id . '-changed' ] ) ) && ( ! empty( $_POST[ 'ebox_course_teams-' . $post_id . '-changed' ] ) ) ) {
					$course_teams = (array) json_decode( stripslashes( $_POST['ebox_course_teams'][ $post_id ] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- This is a json decoded string.
					ebox_set_course_teams( $post_id, $course_teams );
				}
			}
		}

		// End of functions.
	}

	add_filter(
		'ebox_post_settings_metaboxes_init_' . ebox_get_post_type_slug( 'course' ),
		function( $metaboxes = array() ) {
			if ( ( ! isset( $metaboxes['ebox_Settings_Metabox_Course_Teams_Settings'] ) ) && ( class_exists( 'ebox_Settings_Metabox_Course_Teams_Settings' ) ) ) {
				$metaboxes['ebox_Settings_Metabox_Course_Teams_Settings'] = ebox_Settings_Metabox_Course_Teams_Settings::add_metabox_instance();
			}

			return $metaboxes;
		},
		50,
		1
	);
}
