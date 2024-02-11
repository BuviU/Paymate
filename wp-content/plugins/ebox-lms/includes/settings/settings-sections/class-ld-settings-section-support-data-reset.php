<?php
/**
 * ebox Settings Section for Support Data Reset Metabox.
 *
 * @since 3.1.0
 * @package ebox\Settings\Sections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Section' ) ) && ( ! class_exists( 'ebox_Settings_Section_Data_Reset' ) ) ) {
	/**
	 * Class ebox Settings Section for Support Data Reset Metabox.
	 *
	 * @since 3.1.0
	 */
	class ebox_Settings_Section_Data_Reset extends ebox_Settings_Section {

		/**
		 * Settings set array for this section.
		 *
		 * @var array $settings_set Array of settings used by this section.
		 */
		protected $settings_set = array();

		/**
		 * Protected constructor for class
		 *
		 * @since 3.1.0
		 */
		protected function __construct() {
			$this->settings_page_id = 'ebox_support';

			// This is the 'option_name' key used in the wp_options table.
			$this->setting_option_key = 'ld_data_reset';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_section_key = 'settings_support_data_reset';

			// Section label/header.
			$this->settings_section_label = esc_html__( 'Reset ALL ebox Data', 'ebox' );

			$this->load_options = false;

			$this->metabox_context  = 'side';
			$this->metabox_priority = 'high';

			add_action( 'ebox_settings_page_load', array( $this, 'on_settings_page_load' ), 10, 2 );
			add_action( 'ebox_section_fields_before', array( $this, 'show_support_section' ), 30, 2 );

			parent::__construct();
		}

		/**
		 * On Settings Page Load
		 *
		 * @since 3.1.0
		 *
		 * @param string $settings_screen_id Screen ID.
		 * @param string $settings_page_id   Page ID.
		 */
		public function on_settings_page_load( $settings_screen_id = '', $settings_page_id = '' ) {
			global $ebox_lms;

			if ( $settings_page_id === $this->settings_page_id ) {
				if ( ebox_is_admin_user() ) {

					if ( ( isset( $_POST['ld_data_remove_nonce'] ) ) && ( ! empty( $_POST['ld_data_remove_nonce'] ) ) && ( wp_verify_nonce( $_POST['ld_data_remove_nonce'], 'ld_data_remove_' . get_current_user_id() ) ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

						if ( ( isset( $_POST['ld_data_remove_verify'] ) ) && ( ! empty( $_POST['ld_data_remove_verify'] ) ) && ( wp_verify_nonce( $_POST['ld_data_remove_verify'], 'ld_data_remove_' . get_current_user_id() ) ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
							ebox_delete_all_data();

							$active_plugins = (array) get_option( 'active_plugins', array() );
							if ( ! empty( $active_plugins ) ) {
								$active_plugins = array_diff( $active_plugins, array( ebox_LMS_PLUGIN_KEY ) );
								update_option( 'active_plugins', $active_plugins );

								// Hook into our own deactivate function.
								ebox_deactivated();

								// finally redirect the admin to the plugins listing.
								ebox_safe_redirect( admin_url( 'plugins.php' ) );
							}
						}
					}
				}
			}
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
				if ( ebox_is_admin_user() ) {
					$remove_nonce = wp_create_nonce( 'ld_data_remove_' . get_current_user_id() );
					?>
					<hr style="margin-top: 30px; border-top: 5px solid red;"/>
					<div class="ebox-support-settings-desc"><p><span style="color:red;"><?php esc_html_e( 'Warning: This will remove ALL ebox data including any custom database tables.', 'ebox' ); ?></span></p></div>
					<hr style="margin-top: 0px; border-top: 5px solid red;"/>
					<form id="ld_data_remove_form" method="POST">
						<input type="hidden" name="ld_data_remove_nonce" value="<?php echo esc_attr( $remove_nonce ); ?>" />
						<p>
							<label for="ld_data_remove_verify"><strong><?php esc_html_e( 'Confirm the data deletion', 'ebox' ); ?></strong></label><br />
							<input id="ld_data_remove_verify" name="ld_data_remove_verify" type="text" size="50" style="width: 100%;" value="" data-confirm="<?php esc_html_e( 'Are you sure that you want to remove ALL ebox data?', 'ebox' ); ?>" /><br />
							<span class="description">
							<?php
							printf(
								// translators: placeholder: secret generated code.
								esc_html_x( 'Enter %s in the above field and click the submit button', 'placeholder: secret generated code', 'ebox' ),
								'<code>' . esc_attr( $remove_nonce ) . '</code>'
							);
							?>
						</span></p>
						<p><input class="button" type="submit" value="<?php esc_html_e( 'Submit', 'ebox' ); ?>" /></p>
					</form>
					<?php
						$js_confirm_message = esc_html__( 'Are you sure that you want to remove ALL ebox data?', 'ebox' );
					?>
					<?php
				}
			}
		}

		// End of functions.
	}
}

add_action(
	'ebox_settings_sections_init',
	function() {
		ebox_Settings_Section_Data_Reset::add_section_instance();
	}
);
