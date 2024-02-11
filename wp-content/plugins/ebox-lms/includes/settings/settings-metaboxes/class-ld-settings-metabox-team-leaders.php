<?php
/**
 * ebox Settings Metabox for Team Leaders Settings.
 *
 * @since 3.2.0
 * @package ebox\Settings\Metaboxes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Metabox' ) ) && ( ! class_exists( 'ebox_Settings_Metabox_Team_Leaders_Settings' ) ) ) {
	/**
	 * Class ebox Settings Metabox for Team Leaders Settings.
	 *
	 * @since 3.2.0
	 */
	class ebox_Settings_Metabox_Team_Leaders_Settings extends ebox_Settings_Metabox {

		/**
		 * Public constructor for class
		 *
		 * @since 3.2.0
		 */
		public function __construct() {
			// What screen ID are we showing on.
			$this->settings_screen_id = 'teams';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_metabox_key = 'ebox_team_leaders';

			// Section label/header.
			$this->settings_section_label = sprintf(
				// translators: placeholder: Team.
				esc_html_x( '%s Leaders', 'placeholder: Team', 'ebox' ),
				ebox_get_custom_label( 'team' )
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
					?>
					<div id="ebox_team_users_page_box" class="ebox_team_users_page_box">
					<?php
						$ld_binary_selector_team_leaders = new ebox_Binary_Selector_Team_Leaders(
							array(
								'html_title'   => '',
								'team_id'     => $team_id,
								'selected_ids' => ebox_get_teams_administrator_ids( $team_id, true ),
							)
						);
					$ld_binary_selector_team_leaders->show();

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
			if ( true === $this->verify_metabox_nonce_field() ) {
				if ( ( isset( $_POST[ $this->settings_metabox_key . '-' . $post_id . '-changed' ] ) ) && ( ! empty( $_POST[ $this->settings_metabox_key . '-' . $post_id . '-changed' ] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification
					if ( ( isset( $_POST[ $this->settings_metabox_key ][ $post_id ] ) ) && ( ! empty( $_POST[ $this->settings_metabox_key ][ $post_id ] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification
						$team_leaders = (array) json_decode( stripslashes( $_POST[ $this->settings_metabox_key ][ $post_id ] ) ); // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
						$team_leaders = array_map( 'absint', $team_leaders );

						/**
						 * Since LD 3.2.0 the Team Leader can be granted the ability to edit
						 * teams. There is a possible chance the GL can remove themselves from
						 * the leaders listing. So we check this here.
						 */
						if ( ebox_is_team_leader_user() ) {
							$gl_user_id = get_current_user_id();
							// We only care if the GL user is not the author.
							if ( $saved_post->post_author !== $gl_user_id ) {
								// And if they have the GL Basic capabilities.
								if ( 'yes' === ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_Teams_Team_Leader_User', 'manage_teams_enabled' ) ) {
									if ( 'basic' === ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_Teams_Team_Leader_User', 'manage_teams_capabilities' ) ) {
										/**
										 * Always make sure the GL user id is set in the leaders. This
										 * is to prevent the GL from removing themselves.
										 */
										$team_leaders[] = $gl_user_id;
									}
								}
							}
						}

						ebox_set_teams_administrators( $post_id, $team_leaders );
					}
				}
			}
		}

		// End of functions.
	}

	add_filter(
		'ebox_post_settings_metaboxes_init_' . ebox_get_post_type_slug( 'team' ),
		function( $metaboxes = array() ) {
			if ( ( ! isset( $metaboxes['ebox_Settings_Metabox_Team_Leaders_Settings'] ) ) && ( class_exists( 'ebox_Settings_Metabox_Team_Leaders_Settings' ) ) ) {
				$metaboxes['ebox_Settings_Metabox_Team_Leaders_Settings'] = ebox_Settings_Metabox_Team_Leaders_Settings::add_metabox_instance();
			}

			return $metaboxes;
		},
		50,
		1
	);
}
