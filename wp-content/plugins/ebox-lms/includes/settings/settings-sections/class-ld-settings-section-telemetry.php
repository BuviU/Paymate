<?php
/**
 * ebox Settings Section for Telemetry.
 *
 * @since 4.5.0
 * @package ebox\Settings\Sections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


use StellarWP\ebox\StellarWP\Telemetry\Core as Telemetry;
use StellarWP\ebox\StellarWP\Telemetry\Opt_In\Status;

if ( class_exists( 'ebox_Settings_Section' ) && ! class_exists( 'ebox_Settings_Section_Telemetry' ) ) {
	/**
	 * Class ebox Settings Section for Telemetry.
	 *
	 * @since 4.5.0
	 */
	class ebox_Settings_Section_Telemetry extends ebox_Settings_Section {
		/**
		 * Constructor.
		 *
		 * @since 4.5.0
		 */
		protected function __construct() {
			$this->settings_page_id = 'ebox_lms_advanced';

			// This is the 'option_name' key used in the wp_options table.
			$this->setting_option_key = 'ebox_telemetry';

			// This is the HTML form field prefix used.
			$this->setting_field_prefix = 'ebox_telemetry';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_section_key = 'settings_telemetry';

			// Section label/header.
			$this->settings_section_label = esc_html__( 'Data Sharing', 'ebox' );

			parent::__construct();

			/**
			 * Updates the opt-in status.
			 *
			 * @since 4.5.0
			 */
			add_action(
				'admin_init',
				function() {
					if (
						! isset( $_POST['telemetry_opt_in_status'] )
						|| ! isset( $_POST['ebox_telemetry_nonce'] )
						|| ! wp_verify_nonce(
							sanitize_text_field( wp_unslash( $_POST['ebox_telemetry_nonce'] ) ),
							'ebox_telemetry'
						)
					) {
						return;
					}

					$container = Telemetry::instance()->container();
					$enabled   = Status::STATUS_ACTIVE === (int) sanitize_key( (string) wp_unslash( $_POST['telemetry_opt_in_status'] ) );

					/**
					 * Status handler.
					 *
					 * @var Status $status_handler Status handler.
					 */
					$status_handler = $container->get( Status::class );

					$status_handler->set_status( $enabled );
				}
			);
		}

		/**
		 * Shows settings content.
		 *
		 * @since 4.5.0
		 *
		 * @return void
		 */
		public function show_meta_box(): void {
			$container = Telemetry::instance()->container();

			/**
			 * Status handler.
			 *
			 * @var Status $status_handler Status handler.
			 */
			$status_handler = $container->get( Status::class );

			$opt_in_status_value = $status_handler->get();
			?>
			<div class="ebox_options">
				<?php wp_nonce_field( 'ebox_telemetry', 'ebox_telemetry_nonce' ); ?>

				<div class="ebox_input">
					<label for="telemetry_opt_in_status_active" style="display: inline-block; margin-bottom: 5px;">
						<input type="radio" value="<?php echo esc_attr( (string) Status::STATUS_ACTIVE ); ?>" name="telemetry_opt_in_status" id="telemetry_opt_in_status_active" <?php checked( Status::STATUS_ACTIVE, $opt_in_status_value ); ?>/>
						<?php esc_html_e( 'Yes, share plugin usage data with ebox, part of the StellarWP family of brands', 'ebox' ); ?>
					</label><br/>

					<label for="telemetry_opt_in_status_inactive">
						<input type="radio" value="<?php echo esc_attr( (string) Status::STATUS_INACTIVE ); ?>" name="telemetry_opt_in_status" id="telemetry_opt_in_status_inactive" <?php checked( Status::STATUS_INACTIVE, $opt_in_status_value ); ?>/>
						<?php esc_html_e( 'No, do not share plugin usage data with ebox, part of the StellarWP family of brands', 'ebox' ); ?>
					</label>
				</div>
			</div>
			<?php
		}
	}
}

add_action(
	'ebox_settings_sections_init',
	array( ebox_Settings_Section_Telemetry::class, 'add_section_instance' ),
	20
);
