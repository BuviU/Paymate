<?php
/**
 * ebox Settings Page for Certificate Shortcodes.
 *
 * @since 2.4.0
 * @package ebox\Settings\Pages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Page' ) ) && ( ! class_exists( 'ebox_Settings_Page_Certificates_Shortcodes' ) ) ) {
	/**
	 * Class ebox Settings Page for Certificate Shortcodes.
	 *
	 * @since 2.4.0
	 */
	class ebox_Settings_Page_Certificates_Shortcodes extends ebox_Settings_Page {

		/**
		 * Public constructor for class
		 *
		 * @since 2.4.0
		 */
		public function __construct() {
			$this->parent_menu_page_url = 'edit.php?post_type=ebox-certificates';
			$this->menu_page_capability = ebox_ADMIN_CAPABILITY_CHECK;
			$this->settings_page_id     = 'ebox-lms-certificate_shortcodes';
			$this->settings_page_title  = esc_html__( 'Shortcodes', 'ebox' );
			$this->settings_columns     = 1;

			parent::__construct();
		}

		/**
		 * Custom function to show settings page output
		 *
		 * @since 2.4.0
		 */
		public function show_settings_page() {
			?>
			<div  id="certificate-shortcodes"  class="wrap">
				<h1><?php esc_html_e( 'Certificate Shortcodes', 'ebox' ); ?></h1>
				<div class='ebox_options_wrapper ebox_settings_left'>
					<div class='postbox ' id='ebox-certificates_metabox'>
						<div class='inside'  style='padding: 0 12px 12px;'>
						<?php
							echo wpautop( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elements escaped within.
								sprintf(
									// translators: placeholder: URL to online documentation.
									esc_html_x( 'The documentation for Certificate Shortcodes has moved online (only available in English). %s', 'placeholder: URL to online documentation', 'ebox' ),
									'<a href="https://www.ebox.com/support/docs/core/certificates/certificate-shortcodes/" target="_blank" rel="noopener noreferrer" aria-label="' . esc_html__( 'External link to Certificate online documentation', 'ebox' )
								) . '">' . esc_html__( 'Click here', 'ebox' ) . sprintf(
									'<span class="screen-reader-text">%s</span><span aria-hidden="true" style="text-decoration: none !important;" class="dashicons dashicons-external"></span>',
									/* translators: Accessibility text. */
									esc_html__( '(opens in a new tab)', 'ebox' )
								) . '</a>'
							);
						?>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
	}
}
add_action(
	'ebox_settings_pages_init',
	function() {
		ebox_Settings_Page_Certificates_Shortcodes::add_page_instance();
	}
);
