<?php
/**
 * ebox Settings Page Courses Shortcodes.
 *
 * @since 2.4.0
 * @package ebox\Settings\Pages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Page' ) ) && ( ! class_exists( 'ebox_Settings_Page_Courses_Shortcodes' ) ) ) {
	/**
	 * Class ebox Settings Page Courses Shortcodes.
	 *
	 * @since 2.4.0
	 */
	class ebox_Settings_Page_Courses_Shortcodes extends ebox_Settings_Page {

		/**
		 * Public constructor for class
		 *
		 * @since 2.4.0
		 */
		public function __construct() {

			$this->parent_menu_page_url = 'edit.php?post_type=ebox-courses';
			$this->menu_page_capability = ebox_ADMIN_CAPABILITY_CHECK;
			$this->settings_page_id     = 'courses-shortcodes';

			// translators: Course Shortcodes Label.
			$this->settings_page_title   = esc_html_x( 'Shortcodes', 'Course Shortcodes Label', 'ebox' );
			$this->settings_columns      = 1;
			$this->show_quick_links_meta = false;

			parent::__construct();
		}

		/**
		 * Show settings page output.
		 *
		 * @since 2.4.0
		 */
		public function show_settings_page() {
			?>
			<div  id='course-shortcodes'  class='wrap'>
				<h1>
				<?php
				printf(
					// translators: placeholder: Course Label.
					esc_html_x( '%s Shortcodes', 'placeholder: Course Label', 'ebox' ),
					esc_attr( ebox_Custom_Label::get_label( 'course' ) )
				);
				?>
				</h1>
				<div class='ebox_options_wrapper ebox_settings_left'>
					<div class='postbox ' id='ebox-course_metabox'>
						<div class="inside"  style="padding: 0 12px 12px;">
						<?php
							echo wpautop( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elements escaped within.
								sprintf(
									// translators: placeholder: Course label, URL to online documentation.
									esc_html_x( 'The documentation for %1$s Shortcodes and Blocks has moved online (only available in English). %2$s', 'placeholder: Course label, URL to online documentation', 'ebox' ),
									ebox_Custom_Label::get_label( 'course' ),
									'<a href="https://www.ebox.com/support/docs/core/shortcodes-blocks/" target="_blank" rel="noopener noreferrer"  aria-label="' . esc_attr(
										sprintf(
											// translators: placeholder: Course label.
											esc_html_x( 'External link to %s Shortcodes and Blocks online documentation', 'placeholder: Course label.', 'ebox' ),
											ebox_Custom_Label::get_label( 'course' )
										)
									) . '">' . esc_html__( 'Click here', 'ebox' ) . sprintf(
										'<span class="screen-reader-text">%s</span><span aria-hidden="true" class="dashicons dashicons-external"></span>',
										/* translators: Accessibility text. */
										esc_html__( '(opens in a new tab)', 'ebox' )
									) . '</a>'
								)
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
		ebox_Settings_Page_Courses_Shortcodes::add_page_instance();
	}
);
