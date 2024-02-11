<?php
/**
 * ebox Settings Section for Support Copy System Info Metabox.
 *
 * @since 3.1.0
 * @package ebox\Settings\Sections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Section' ) ) && ( ! class_exists( 'ebox_Settings_Section_Support_System_Info' ) ) ) {
	/**
	 * Class ebox Settings Section for Support Copy System Info Metabox.
	 *
	 * @since 3.1.0
	 */
	class ebox_Settings_Section_Support_System_Info extends ebox_Settings_Section {

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
			$this->setting_option_key = 'ld_copy_export';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_section_key = 'settings_support_copy_export';

			// Section label/header.
			$this->settings_section_label = esc_html__( 'Copy System Info', 'ebox' );

			$this->metabox_context = 'side';

			$this->load_options = false;

			add_action( 'ebox_section_fields_before', array( $this, 'show_support_section' ), 30, 2 );

			parent::__construct();
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
				$support_page_instance = ebox_Settings_Page::get_page_instance( 'ebox_Settings_Page_Support' );
				if ( $support_page_instance ) {
					?>
					<textarea id="ld-system-info-text" style="width: 100%; min-height: 80px; font-family: monospace">
					<?php
					foreach ( $support_page_instance->get_support_sections() as $_key => $_section ) {
						$support_page_instance->show_support_section( $_key, 'text' );
					}
					?>
					</textarea><br />
					<p>
					<a class="button" href="<?php echo esc_url( add_query_arg( 'ld_download_system_info_nonce', wp_create_nonce( 'ld_download_system_info_' . get_current_user_id() ) ) ); ?>"><?php esc_html_e( 'Download', 'ebox' ); ?></a>
					<button class="button" id="ld-system-info-copy-button"><?php esc_html_e( 'Copy', 'ebox' ); ?></button><br /><span style="display:none" id="ld-copy-status-success"><?php esc_html_e( 'Copy Success', 'ebox' ); ?></span><span style="display:none" id="ld-copy-status-failed"><?php esc_html_e( 'Copy Failed', 'ebox' ); ?></span></p>
					<script>
						var copyBtn = document.querySelector('#ld-system-info-copy-button');
						copyBtn.addEventListener('click', function(event) {
							// Select the email link anchor text
							var copy_text = document.querySelector('#ld-system-info-text');
							var range = document.createRange();
							range.selectNode(copy_text);
							window.getSelection().addRange(range);

							try {
								// Now that we've selected the anchor text, execute the copy command
								var successful = document.execCommand('copy');
								if ( successful ) {
									jQuery( '#ld-copy-status-success').show();
								}
							} catch(err) {
									console.log('Oops, unable to copy');
							}

							// Remove the selections - NOTE: Should use
							// removeRange(range) when it is supported
							window.getSelection().removeAllRanges();

							event.preventDefault()
						});
					</script>
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
		ebox_Settings_Section_Support_System_Info::add_section_instance();
	}
);
