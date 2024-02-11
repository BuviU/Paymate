<?php
/**
 * ebox Settings Metabox for Team Courses Settings.
 *
 * @since 3.2.0
 * @package ebox\Settings\Metaboxes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Metabox' ) ) && ( ! class_exists( 'ebox_Settings_Metabox_Team_Courses_Enroll_Settings' ) ) ) {
	/**
	 * Class ebox Settings Metabox for Team Courses Settings.
	 *
	 * @since 3.2.0
	 */
	class ebox_Settings_Metabox_Team_Courses_Enroll_Settings extends ebox_Settings_Metabox {

		/**
		 * Public constructor for class
		 *
		 * @since 3.2.0
		 */
		public function __construct() {
			// What screen ID are we showing on.
			$this->settings_screen_id = 'teams';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_metabox_key = 'ebox_team_courses_enroll';

			// Section label/header.
			$this->settings_section_label = sprintf(
				// translators: placeholder: Team, Courses.
				esc_html_x( '%1$s %2$s Auto-enroll', 'placeholder: Team, Courses', 'ebox' ),
				ebox_get_custom_label( 'team' ),
				ebox_get_custom_label( 'courses' )
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
					$team_id = $metabox->post->ID;
				} else {
					$team_id = get_the_ID();
				}

				if ( ( ! empty( $team_id ) ) && ( get_post_type( $team_id ) === ebox_get_post_type_slug( 'team' ) ) ) {

					$ld_auto_enroll_team_courses = get_post_meta( $team_id, 'ld_auto_enroll_team_courses', true );
					?>
					<div id="ebox_course_users_page_box" class="ebox_course_users_page_box">
						<p><input type="checkbox" id="ebox_auto_enroll_team_courses" name="ebox_auto_enroll_team_courses" value="yes"
						<?php checked( $ld_auto_enroll_team_courses, 'yes' ); ?> />
							<?php
							printf(
								// translators: placeholder: team, team, course.
								esc_html_x( 'Enable automatic %1$s enrollment when a user enrolls into any associated %2$s %3$s', 'placeholder: team, team, course', 'ebox' ),
								esc_html( ebox_get_custom_label_lower( 'team' ) ),
								esc_html( ebox_get_custom_label_lower( 'team' ) ),
								esc_html( ebox_get_custom_label_lower( 'course' ) )
							);
							?>
						</p>
						<?php

						$ld_auto_enroll_team_course_ids = get_post_meta( $team_id, 'ld_auto_enroll_team_course_ids', true );
						if ( ! is_array( $ld_auto_enroll_team_course_ids ) ) {
							$ld_auto_enroll_team_course_ids = array();
						}
						$ld_auto_enroll_team_course_ids = array_map( 'absint', $ld_auto_enroll_team_course_ids );

						$team_selected_ids = ebox_team_enrolled_courses( $team_id, true );
						if ( ! empty( $team_selected_ids ) ) {
							$team_selected_ids              = array_map( 'absint', $team_selected_ids );
							$ld_auto_enroll_team_course_ids = array_intersect( $ld_auto_enroll_team_course_ids, $team_selected_ids );
						}

						$ld_binary_selector_team_courses_enroll = new ebox_Binary_Selector_Team_Courses_Enroll(
							array(
								'html_title'   => '',
								'team_id'     => $team_id,
								'included_ids' => ebox_team_enrolled_courses( $team_id, true ),
								'selected_ids' => $ld_auto_enroll_team_course_ids,
							)
						);
						$ld_binary_selector_team_courses_enroll->show();
						?>
					</div>
					<script>
						// Coordinate change between the checkbox and binary selector.
						var ebox_auto_enroll_team_courses_checkbox = document.getElementById('ebox_auto_enroll_team_courses');
						ebox_auto_enroll_team_courses_checkbox.addEventListener('change', e => {
							ebox_auto_enroll_team_courses_checkbox_handle_change( e.target );
						});
						ebox_auto_enroll_team_courses_checkbox_handle_change( ebox_auto_enroll_team_courses_checkbox );
						function ebox_auto_enroll_team_courses_checkbox_handle_change( checkbox ) {
							if ( checkbox.checked ) {
								document.getElementById('ebox_team_courses_enroll-<?php echo esc_attr( $team_id ); ?>').style.visibility = 'hidden';
							} else {
								document.getElementById('ebox_team_courses_enroll-<?php echo esc_attr( $team_id ); ?>').style.visibility = 'visible';
							}
						}
					</script>
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
			if ( true === $this->verify_metabox_nonce_field() ) {

				if ( ( isset( $_POST[ $this->settings_metabox_key . '-' . $post_id . '-changed' ] ) ) && ( ! empty( $_POST[ $this->settings_metabox_key . '-' . $post_id . '-changed' ] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification
					if ( ( isset( $_POST[ $this->settings_metabox_key ][ $post_id ] ) ) && ( ! empty( $_POST[ $this->settings_metabox_key ][ $post_id ] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
						$team_enroll_courses = (array) json_decode( stripslashes( $_POST[ $this->settings_metabox_key ][ $post_id ] ) ); // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
						$team_enroll_courses = array_map( 'absint', $team_enroll_courses );
						if ( ! empty( $team_enroll_courses ) ) {
							update_post_meta( $post_id, 'ld_auto_enroll_team_course_ids', $team_enroll_courses );
						} else {
							delete_post_meta( $post_id, 'ld_auto_enroll_team_course_ids' );
						}
					}
				}

				if ( ( isset( $_POST['ebox_auto_enroll_team_courses'] ) ) && ( 'yes' == $_POST['ebox_auto_enroll_team_courses'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
					update_post_meta( $post_id, 'ld_auto_enroll_team_courses', 'yes' );
				} else {
					delete_post_meta( $post_id, 'ld_auto_enroll_team_courses' );
				}
			}
		}

		// End of functions.
	}

	add_filter(
		'ebox_post_settings_metaboxes_init_' . ebox_get_post_type_slug( 'team' ),
		function( $metaboxes = array() ) {
			if ( ( ! isset( $metaboxes['ebox_Settings_Metabox_Team_Courses_Enroll_Settings'] ) ) && ( class_exists( 'ebox_Settings_Metabox_Team_Courses_Enroll_Settings' ) ) ) {
				$metaboxes['ebox_Settings_Metabox_Team_Courses_Enroll_Settings'] = ebox_Settings_Metabox_Team_Courses_Enroll_Settings::add_metabox_instance();
			}

			return $metaboxes;
		},
		50,
		1
	);
}
