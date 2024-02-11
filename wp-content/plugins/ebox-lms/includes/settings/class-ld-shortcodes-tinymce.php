<?php
/**
 * ebox Admin Shortcodes TinyMCE Class.
 *
 * @since 2.4.0
 * @package ebox\Settings\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ebox_Shortcodes_TinyMCE' ) ) {

	/**
	 * Class for ebox Admin Shortcodes TinyMCE.
	 *
	 * @since 2.4.0
	 */
	class ebox_Shortcodes_TinyMCE {

		/**
		 * Shortcode assets
		 *
		 * @var array ebox_admin_shortcodes_assets
		 */
		protected $ebox_admin_shortcodes_assets = array();

		/**
		 * Public constructor for class.
		 *
		 * @since 2.4.0
		 */
		public function __construct() {
			add_action( 'wp_enqueue_editor', array( $this, 'wp_enqueue_editor' ) );

			add_filter( 'mce_external_plugins', array( $this, 'add_tinymce_plugin' ), 1 );
			add_filter( 'mce_buttons', array( $this, 'register_button' ), 1 );

			add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_scripts' ) );
			add_action( 'admin_print_footer_scripts', array( $this, 'qt_button_script' ) );
			add_action( 'wp_ajax_ebox_generate_shortcodes_content', array( $this, 'ebox_generate_shortcodes_content' ) );
		}

		/**
		 * Shortcodes assets init
		 *
		 * @since 3.0.7
		 */
		protected function shortcodes_assets_init() {
			global $typenow, $pagenow, $post;

			if ( empty( $this->ebox_admin_shortcodes_assets ) ) {
				$this->ebox_admin_shortcodes_assets['popup_title'] = esc_html__( 'ebox Shortcodes', 'ebox' );

				/**
				 * Filters TinyMCE shortcode popup type.
				 *
				 * @param string $popup_type Type of the popup for TinyMCE.
				 */
				$this->ebox_admin_shortcodes_assets['popup_type'] = apply_filters( 'ebox_shortcodes_popup_type', ebox_ADMIN_POPUP_STYLE );
				$this->ebox_admin_shortcodes_assets['typenow']    = $typenow;
				$this->ebox_admin_shortcodes_assets['pagenow']    = $pagenow;
				$this->ebox_admin_shortcodes_assets['nonce']      = wp_create_nonce( 'ebox_admin_shortcodes_assets_nonce_' . get_current_user_id() . '_' . $pagenow );
			}
		}

		/**
		 * Enqueue Editor
		 *
		 * Fires on `wp_enqueue_editor` hook.
		 *
		 * @since 3.0.7
		 *
		 * @param array $editor_args Editor args array.
		 */
		public function wp_enqueue_editor( $editor_args = array() ) {
			$this->shortcodes_assets_init();

			if ( 'thickbox' === $this->ebox_admin_shortcodes_assets['popup_type'] ) {
				wp_enqueue_style( 'thickbox' );
				wp_enqueue_script( 'thickbox' );
			} elseif ( 'jQuery-dialog' === $this->ebox_admin_shortcodes_assets['popup_type'] ) {
				wp_enqueue_script( 'jquery-ui-dialog' ); // jquery and jquery-ui should be dependencies, didn't check though...
				wp_enqueue_style( 'wp-jquery-ui-dialog' );
			}

			if ( ( isset( $editor_args['tinymce'] ) ) && ( true === $editor_args['tinymce'] ) ) {
				$this->add_button();
			}

			if ( ( isset( $editor_args['quicktags'] ) ) && ( true === $editor_args['quicktags'] ) ) {
				add_action( 'admin_print_footer_scripts', array( $this, 'qt_button_script' ) );
			}
		}

		/**
		 * Quicktags hooks
		 *
		 * Fires on `admin_print_footer_scripts` hook.
		 *
		 * @since 2.4.0
		 */
		public function qt_button_script() {
			?>
			<script type="text/javascript">
				if (typeof QTags !== 'undefined') {
					QTags.addButton( 'ebox_shortcodes', '[ld]', ebox_shortcodes_qt_callback, '', '', '', 'ebox Shortcodes' );

					// In the QTags.addButton we need to call this intermediate function because ebox_shortcodes is now loaded yet.
					function ebox_shortcodes_qt_callback() {
						ebox_shortcodes.qt_callback();
					}
				}
			</script>
			<?php
		}

		/**
		 * Add TinyMCE buttons
		 *
		 * @since 2.4.0
		 */
		public function add_button() {
			add_filter( 'mce_external_plugins', array( $this, 'add_tinymce_plugin' ), 1 );
			add_filter( 'mce_buttons', array( $this, 'register_button' ), 1 );
		}

		/**
		 * Add TinyMCE support
		 *
		 * @since 2.4.0
		 *
		 * @param array $plugin_array Array of TinyMCE plugins.
		 *
		 * @return array
		 */
		public function add_tinymce_plugin( $plugin_array ) {
			$plugin_array['ebox_shortcodes_tinymce'] = ebox_LMS_PLUGIN_URL . 'assets/js/ebox-admin-shortcodes-tinymce' . ebox_min_asset() . '.js';

			return $plugin_array;
		}

		/**
		 * Register TinyMCE button callback
		 *
		 * @since 2.4.0
		 *
		 * @param array $buttons Array of buttons.
		 */
		public function register_button( $buttons ) {
			array_push( $buttons, 'ebox_shortcodes_tinymce' );
			return $buttons;
		}

		/**
		 * Load admin scripts
		 *
		 * @since 2.4.0
		 */
		public function load_admin_scripts() {
			global $typenow, $pagenow;
			global $ebox_assets_loaded;

			wp_enqueue_style(
				'ebox-module-style',
				ebox_LMS_PLUGIN_URL . 'assets/css/ebox_module' . ebox_min_asset() . '.css',
				array(),
				ebox_SCRIPT_VERSION_TOKEN
			);
			$ebox_assets_loaded['styles']['ebox-module-style'] = __FUNCTION__;

			wp_enqueue_script(
				'ebox-module-script',
				ebox_LMS_PLUGIN_URL . 'assets/js/ebox_module' . ebox_min_asset() . '.js',
				array( 'jquery' ),
				ebox_SCRIPT_VERSION_TOKEN,
				true
			);
			$ebox_assets_loaded['scripts']['ebox-module-script'] = __FUNCTION__;

			$data            = array();
			$data['ajaxurl'] = admin_url( 'admin-ajax.php' );

			$data = array( 'json' => wp_json_encode( $data ) );
			wp_localize_script( 'ebox-module-script', 'ebox_data', $data );

			wp_enqueue_style(
				'ebox_admin_shortcodes_style',
				ebox_LMS_PLUGIN_URL . 'assets/css/ebox-admin-shortcodes' . ebox_min_asset() . '.css',
				array(),
				ebox_SCRIPT_VERSION_TOKEN
			);
			wp_style_add_data( 'ebox_admin_shortcodes_style', 'rtl', 'replace' );
			$ebox_assets_loaded['styles']['ebox_shortcodes_admin_style'] = __FUNCTION__;

			$this->shortcodes_assets_init();

			wp_enqueue_script(
				'ebox_admin_shortcodes_script',
				ebox_LMS_PLUGIN_URL . 'assets/js/ebox-admin-shortcodes' . ebox_min_asset() . '.js',
				array( 'jquery' ),
				ebox_SCRIPT_VERSION_TOKEN,
				true
			);
			$ebox_assets_loaded['styles']['ebox_admin_shortcodes_script'] = __FUNCTION__;
			wp_localize_script( 'ebox_admin_shortcodes_script', 'ebox_admin_shortcodes_assets', $this->ebox_admin_shortcodes_assets );

			if ( 'jQuery-dialog' === $this->ebox_admin_shortcodes_assets['popup_type'] ) {
				// Hold until after LD 3.0 release.
				ebox_admin_settings_page_assets();
			}
		}

		/**
		 * Button callback content
		 *
		 * @since 2.4.0
		 */
		public function ebox_generate_shortcodes_content() {
			if ( ( ! isset( $_POST['atts'] ) ) || ( empty( $_POST['atts'] ) ) ) {
				die();
			}

			$fields_args = array(
				'typenow' => '',
				'pagenow' => '',
				'nonce'   => '',
			);

			// The wp_verify_nonce() call is just a few lines below.
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			$fields_args = shortcode_atts( $fields_args, $_POST['atts'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Missing

			if ( ( empty( $fields_args['nonce'] ) ) || ( empty( $fields_args['pagenow'] ) ) ) {
				die();
			}

			if ( ( empty( $fields_args['post_type'] ) ) && ( ! empty( $fields_args['typenow'] ) ) ) { // @phpstan-ignore-line -- false positive of does not exist.
				$fields_args['post_type'] = $fields_args['typenow'];
			}

			if ( ! wp_verify_nonce( $fields_args['nonce'], 'ebox_admin_shortcodes_assets_nonce_' . get_current_user_id() . '_' . $fields_args['pagenow'] ) ) {
				die();
			}

			$shortcode_sections = array();

			require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/class-ld-shortcodes-sections.php';

			if ( 'ebox-certificates' !== $fields_args['typenow'] ) {

				require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/shortcodes-sections/ebox_login.php';
				$shortcode_sections['ebox_login'] = new ebox_Shortcodes_Section_ebox_login( $fields_args );

				require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/shortcodes-sections/ld_profile.php';
				$shortcode_sections['ld_profile'] = new ebox_Shortcodes_Section_ld_profile( $fields_args );

				require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/shortcodes-sections/ld_course_list.php';
				$shortcode_sections['ld_course_list'] = new ebox_Shortcodes_Section_ld_course_list( $fields_args );

				require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/shortcodes-sections/ld_lesson_list.php';
				$shortcode_sections['ld_lesson_list'] = new ebox_Shortcodes_Section_ld_lesson_list( $fields_args );

				require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/shortcodes-sections/ld_topic_list.php';
				$shortcode_sections['ld_topic_list'] = new ebox_Shortcodes_Section_ld_topic_list( $fields_args );

				require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/shortcodes-sections/ld_quiz_list.php';
				$shortcode_sections['ld_quiz_list'] = new ebox_Shortcodes_Section_ld_quiz_list( $fields_args );

				require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/shortcodes-sections/ebox_course_progress.php';
				$shortcode_sections['ebox_course_progress'] = new ebox_Shortcodes_Section_ebox_course_progress( $fields_args );

				require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/shortcodes-sections/visitor.php';
				$shortcode_sections['visitor'] = new ebox_Shortcodes_Section_visitor( $fields_args );

				require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/shortcodes-sections/student.php';
				$shortcode_sections['student'] = new ebox_Shortcodes_Section_student( $fields_args );

				require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/shortcodes-sections/course_complete.php';
				$shortcode_sections['course_complete'] = new ebox_Shortcodes_Section_course_complete( $fields_args );

				require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/shortcodes-sections/course_inprogress.php';
				$shortcode_sections['course_inprogress'] = new ebox_Shortcodes_Section_course_inprogress( $fields_args );

				require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/shortcodes-sections/course_notstarted.php';
				$shortcode_sections['course_notstarted'] = new ebox_Shortcodes_Section_course_notstarted( $fields_args );

				require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/shortcodes-sections/ld_course_resume.php';
				$shortcode_sections['ld_course_resume'] = new ebox_Shortcodes_Section_ld_course_resume( $fields_args );

				require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/shortcodes-sections/ld_course_info.php';
				$shortcode_sections['ld_course_info'] = new ebox_Shortcodes_Section_ld_course_info( $fields_args );

				require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/shortcodes-sections/ld_user_course_points.php';
				$shortcode_sections['ld_user_course_points'] = new ebox_Shortcodes_Section_ld_user_course_points( $fields_args );

				require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/shortcodes-sections/user_teams.php';
				$shortcode_sections['user_teams'] = new ebox_Shortcodes_Section_user_teams( $fields_args );

				require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/shortcodes-sections/ld_team.php';
				$shortcode_sections['ld_team'] = new ebox_Shortcodes_Section_ld_team( $fields_args );

				require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/shortcodes-sections/ld_team_list.php';
				$shortcode_sections['ld_team_list'] = new ebox_Shortcodes_Section_ld_team_list( $fields_args );

				require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/shortcodes-sections/ebox_payment_buttons.php';
				$shortcode_sections['ebox_payment_buttons'] = new ebox_Shortcodes_Section_ebox_payment_buttons( $fields_args );

				require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/shortcodes-sections/course_content.php';
				$shortcode_sections['course_content'] = new ebox_Shortcodes_Section_course_content( $fields_args );

				require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/shortcodes-sections/ld_navigation.php';
				$shortcode_sections['ld_navigation'] = new ebox_Shortcodes_Section_ld_navigation( $fields_args );

				require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/shortcodes-sections/ld_course_expire_status.php';
				$shortcode_sections['ld_course_expire_status'] = new ebox_Shortcodes_Section_ld_course_expire_status( $fields_args );

				require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/shortcodes-sections/ld_certificate.php';
				$shortcode_sections['ld_certificate'] = new ebox_Shortcodes_Section_ld_certificate( $fields_args );

				require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/shortcodes-sections/ld_quiz_complete.php';
				$shortcode_sections['ld_quiz_complete'] = new ebox_Shortcodes_Section_ld_quiz_complete( $fields_args );

				if ( ( 'ebox-modules' === $fields_args['typenow'] ) || ( 'ebox-topic' === $fields_args['typenow'] ) ) {
					require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/shortcodes-sections/ld_video.php';
					$shortcode_sections['ld_video'] = new ebox_Shortcodes_Section_ld_video( $fields_args );
				}
			}

			require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/shortcodes-sections/courseinfo.php';
			$shortcode_sections['courseinfo'] = new ebox_Shortcodes_Section_courseinfo( $fields_args );

			require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/shortcodes-sections/teaminfo.php';
			$shortcode_sections['teaminfo'] = new ebox_Shortcodes_Section_teaminfo( $fields_args );

			require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/shortcodes-sections/quizinfo.php';
			$shortcode_sections['quizinfo'] = new ebox_Shortcodes_Section_quizinfo( $fields_args );

			require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/shortcodes-sections/usermeta.php';
			$shortcode_sections['usermeta'] = new ebox_Shortcodes_Section_usermeta( $fields_args );

			require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/shortcodes-sections/ld_registration.php';
			$shortcode_sections['ld_registration'] = new ebox_Shortcodes_Section_ld_registration( $fields_args );

			require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/shortcodes-sections/ld_infobar.php';
			$shortcode_sections['ld_infobar'] = new ebox_Shortcodes_Section_ld_infobar( $fields_args );

			require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/shortcodes-sections/ld_materials.php';
			$shortcode_sections['ld_materials'] = new ebox_Shortcodes_Section_ld_materials( $fields_args );

			require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/shortcodes-sections/ebox_user_status.php';
			$shortcode_sections['ebox_user_status'] = new ebox_Shortcodes_Section_ebox_user_status( $fields_args );

				/**
				 * Filters TinyMCE shortcode content arguments.
				 *
				 * @param array $shortcode_content The shortcode content arguments.
				 */
				$shortcode_sections = apply_filters( 'ebox_shortcodes_content_args', $shortcode_sections );

			?>
				<div id="ebox_shortcodes_wrap" class="wrap ebox_options">
					<div id="ebox_shortcodes_tabs">
						<ul>
							<?php foreach ( $shortcode_sections as $section ) { ?>
							<li><a data-nav="<?php echo esc_attr( $section->get_shortcodes_section_key() ); ?>" href="#"><?php echo wp_kses_post( $section->get_shortcodes_section_title() ); ?></a></li>
							<?php } ?>
						</ul>
					</div>

					<div id="ebox_shortcodes_sections">
						<?php foreach ( $shortcode_sections as $section ) { ?>
							<div id="tabs-<?php echo esc_attr( $section->get_shortcodes_section_key() ); ?>" class="hidable wrap" style="display: none;">
								<?php echo $section->show_section_fields(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Need to output HTML. ?>
							</div>
						<?php } ?>
					</div>
				</div>
					<?php
					// }
					die();
		}

		// End of functions.

	}
}

add_action(
	'plugins_loaded',
	function() {
		new ebox_Shortcodes_TinyMCE();
	}
);
