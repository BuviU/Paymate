<?php
/**
 * ebox Settings Metabox for Course Access Settings.
 *
 * @since 3.1.0
 * @package ebox\Settings\Metaboxes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Metabox' ) ) && ( ! class_exists( 'ebox_Settings_Metabox_Course_Users_Settings' ) ) ) {
	/**
	 * Class ebox Settings Metabox for Course Access Settings.
	 *
	 * @since 3.1.0
	 */
	class ebox_Settings_Metabox_Course_Users_Settings extends ebox_Settings_Metabox {

		/**
		 * Public constructor for class
		 *
		 * @since 3.1.0
		 */
		public function __construct() {
			// What screen ID are we showing on.
			$this->settings_screen_id = 'ebox-courses';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_metabox_key = 'ebox-course-users-settings';

			// Section label/header.
			$this->settings_section_label = sprintf(
				// translators: placeholder: Course.
				esc_html_x( '%s Users', 'placeholder: Course', 'ebox' ),
				ebox_get_custom_label( 'course' )
			);

			parent::__construct();
		}

		/**
		 * Show Settings Section Fields.
		 *
		 * @since 3.1.0
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

					$course_price_type = ebox_get_setting( $course_id, 'course_price_type' );
					if ( 'open' !== $course_price_type ) {
						$selected_user_ids   = array();
						$metabox_description = '';

						$course_access_users = ebox_get_course_users_access_from_meta( $course_id );
						if ( ! empty( $course_access_users ) ) {
							$course_access_users = ebox_convert_course_access_list( $course_access_users, true );
						} else {
							$course_access_users = array();
						}

						$course_users_binary_args = array(
							'html_title'            => '',
							'course_id'             => $course_id,
							'search_posts_per_page' => 100,
							'selected_ids'          => $course_access_users,
							'selected_meta_query'   => array(
								array(
									'key'     => 'course_' . $course_id . '_access_from',
									'compare' => 'EXISTS',
								),
							),
						);

						// Use nonce for verification.
						wp_nonce_field( 'ebox_course_users_nonce_' . $course_id, 'ebox_course_users_nonce' );

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
						$ld_binary_selector_course_users = new ebox_Binary_Selector_Course_Users( $course_users_binary_args );
						$ld_binary_selector_course_users->show();
						?>
						</div>
						<?php
					} else {
						?>
						<p>
						<?php
						printf(
							// translators: placeholder: Course.
							esc_html_x( 'The %s price type is set to "open". This means ALL are automatically enrolled.', 'placeholder: Course', 'ebox' ),
							ebox_get_custom_label( 'course' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
						);
						?>
						</p>
						<?php
					}
				}
			}
		}

		/**
		 * Save Settings Metabox
		 *
		 * @since 3.1.0
		 *
		 * @param integer $post_id $Post ID is post being saved.
		 * @param object  $saved_post WP_Post object being saved.
		 * @param boolean $update If update true, otherwise false.
		 * @param array   $settings_field_updates array of settings fields to update.
		 */
		public function save_post_meta_box( $post_id = 0, $saved_post = null, $update = null, $settings_field_updates = null ) {
			if ( ( isset( $_POST['ebox_course_users_nonce'] ) ) && ( wp_verify_nonce( $_POST['ebox_course_users_nonce'], 'ebox_course_users_nonce_' . $post_id ) ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				if ( ( isset( $_POST['ebox_course_users'] ) ) && ( isset( $_POST['ebox_course_users'][ $post_id ] ) ) && ( ! empty( $_POST['ebox_course_users'][ $post_id ] ) ) && isset( $_POST[ 'ebox_course_users-' . $post_id . '-changed' ] ) && ( ! empty( $_POST[ 'ebox_course_users-' . $post_id . '-changed' ] ) ) ) {
					$course_users = (array) json_decode( stripslashes( $_POST['ebox_course_users'][ $post_id ] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					ebox_set_users_for_course( $post_id, $course_users );
				}
			}
		}

		// End of functions.
	}

	add_filter(
		'ebox_post_settings_metaboxes_init_' . ebox_get_post_type_slug( 'course' ),
		function( $metaboxes = array() ) {
			if ( ( ! isset( $metaboxes['ebox_Settings_Metabox_Course_Users_Settings'] ) ) && ( class_exists( 'ebox_Settings_Metabox_Course_Users_Settings' ) ) ) {
				$metaboxes['ebox_Settings_Metabox_Course_Users_Settings'] = ebox_Settings_Metabox_Course_Users_Settings::add_metabox_instance();
			}

			return $metaboxes;
		},
		50,
		1
	);
}
