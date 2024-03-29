<?php
/**
 * ebox Settings Side Submit Metabox.
 *
 * @since 2.6.0
 * @package ebox\Settings\Sections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Section' ) ) && ( ! class_exists( 'ebox_Settings_Section_Side_Submit' ) ) ) {
	/**
	 * Class ebox Settings Side Submit Metabox.
	 *
	 * @since 2.6.0
	 */
	class ebox_Settings_Section_Side_Submit extends ebox_Settings_Section {

		/**
		 * Public constructor for class
		 *
		 * @since 2.6.0
		 *
		 * @param array $args Array of class args.
		 */
		public function __construct( $args = array() ) {

			if ( ( isset( $args['settings_screen_id'] ) ) && ( ! empty( $args['settings_screen_id'] ) ) ) {
				$this->settings_screen_id = $args['settings_screen_id'];
			}

			if ( ( isset( $args['settings_page_id'] ) ) && ( ! empty( $args['settings_page_id'] ) ) ) {
				$this->settings_page_id = $args['settings_page_id'];
			}

			if ( ( ! empty( $this->settings_screen_id ) ) && ( ! empty( $this->settings_page_id ) ) ) {

				// This is the 'option_name' key used in the wp_options table.
				$this->setting_option_key = 'submitdiv';

				// Section label/header.
				$this->settings_section_label = esc_html__( 'Save Options', 'ebox' );

				$this->metabox_context  = 'side';
				$this->metabox_priority = 'high';

				$this->load_options = false;

				parent::__construct();

				// We override the parent value set for $this->metabox_key because we want the div ID to match the details WordPress
				// value so it will be hidden.
				$this->metabox_key = 'submitdiv';
			}
		}

		/**
		 * Primary function to show the metabox output
		 *
		 * @since 2.6.0
		 */
		public function show_meta_box() {

			?>
			<div id="submitpost" class="submitbox">

				<div id="major-publishing-actions">

					<div id="publishing-action">
						<span class="spinner"></span>
						<?php submit_button( esc_html__( 'Save', 'ebox' ), 'primary', 'submit', false ); ?>
					</div>

					<div class="clear"></div>

				</div><!-- #major-publishing-actions -->

			</div><!-- #submitpost -->
			<?php
		}

		/**
		 * Load settings fields
		 *
		 * This is a requires function.
		 */
		public function load_settings_fields() {
		}
	}
}
