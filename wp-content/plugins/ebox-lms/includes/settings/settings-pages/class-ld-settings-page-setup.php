<?php
/**
 * ebox Settings Page Overview.
 *
 * @since 4.4.0
 * @package ebox\Settings\Pages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use ebox_Settings_Page_Help as Help_Page;
use ebox_Settings_Section_Stripe_Connect as Stripe_Connect;

if ( class_exists( 'ebox_Settings_Page' ) && ! class_exists( 'ebox_Settings_Page_Setup' ) ) {
	/**
	 * Class ebox Settings Page Overview.
	 *
	 * @since 4.4.0
	 */
	class ebox_Settings_Page_Setup extends ebox_Settings_Page {
		const SETUP_SLUG_CLOUD    = 'ebox-cloud-setup';
		const SETUP_SLUG          = 'ebox-setup';
		const OVERVIEW_PAGE_SLUG  = 'ebox_lms_overview';
		const ACTIVATION_URL_SLUG = 'ebox-activate-stripe-connect';

		/**
		 * Public constructor for class
		 *
		 * @since 4.4.0
		 */
		public function __construct() {
			$this->parent_menu_page_url  = 'admin.php?page=ebox-setup';
			$this->menu_page_capability  = ebox_ADMIN_CAPABILITY_CHECK;
			$this->settings_page_id      = 'ebox-setup';
			$this->settings_page_title   = esc_html__( 'ebox Setup', 'ebox' );
			$this->settings_tab_title    = esc_html__( 'Setup', 'ebox' );
			$this->settings_tab_priority = 0;

			if ( ! ebox_cloud_is_enabled() ) {
				add_filter( 'ebox_submenu', array( $this, 'submenu_item' ), 200 );

				add_filter( 'ebox_admin_tab_sets', array( $this, 'ebox_admin_tab_sets' ), 10, 3 );
				add_filter( 'ebox_header_data', array( $this, 'admin_header' ), 40, 3 );
				add_action( 'admin_head', array( $this, 'output_admin_inline_scripts' ) );

				parent::__construct();
			}

			add_action( 'wp_loaded', array( $this, 'redirect_overview_to_setup' ), 1 );
			add_action( 'wp_loaded', array( $this, 'activate_connect_stripe' ) );
		}

		/**
		 * Control visibility of submenu items based on license status
		 *
		 * @since 4.4.0
		 *
		 * @param array $submenu Submenu item to check.
		 *
		 * @return array
		 */
		public function submenu_item( array $submenu ) : array {
			if ( ! isset( $submenu[ $this->settings_page_id ] ) ) {
				$submenu = array_merge(
					array(
						$this->settings_page_id => array(
							'name'  => $this->settings_tab_title,
							'cap'   => $this->menu_page_capability,
							'link'  => $this->parent_menu_page_url,
							'class' => 'submenu-ldlms-setup',
						),
					),
					$submenu
				);
			}

			return $submenu;
		}

		/**
		 * Filter the admin header data. We don't want to show the header panel on the Overview page.
		 *
		 * @since 4.4.0
		 *
		 * @param array  $header_data Array of header data used by the Header Panel React app.
		 * @param string $menu_key The menu key being displayed.
		 * @param array  $menu_items Array of menu/tab items.
		 *
		 * @return array
		 */
		public function admin_header( array $header_data = array(), string $menu_key = '', array $menu_items = array() ) : array {
			// Clear out $header_data if we are showing our page.
			return $menu_key === $this->parent_menu_page_url ? array() : $header_data;
		}

		/**
		 * Output inline scripts or styles in HTML head tag.
		 *
		 * @since 4.4.0
		 *
		 * @return void
		 */
		public function output_admin_inline_scripts() : void {
			// Setup page.
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( isset( $_GET['page'] ) && 'ebox-setup' === $_GET['page'] ) {
				?>
				<style>
					body .notice {
						display: none;
					}
				</style>
				<?php
			}
		}

		/**
		 * Redirect the old ebox overview page to setup page.
		 *
		 * @since 4.4.0
		 *
		 * @return void
		 */
		public function redirect_overview_to_setup() : void {
			global $pagenow;

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( $pagenow === 'admin.php' && isset( $_GET['page'] ) && $_GET['page'] === self::OVERVIEW_PAGE_SLUG ) {
				$setup_slug = ebox_cloud_is_enabled() ? self::SETUP_SLUG_CLOUD : self::SETUP_SLUG;

				wp_safe_redirect( admin_url( 'admin.php?page=' . $setup_slug ) );
				exit();
			}
		}

		/**
		 * Activate Stripe connect.
		 *
		 * @since 4.4.0.1
		 *
		 * @return void
		 */
		public function activate_connect_stripe(): void {
			global $pagenow;
			if ( $pagenow !== 'admin.php'
				|| ! isset( $_GET['page'] ) || $_GET['page'] !== self::SETUP_SLUG
				|| ! isset( $_GET['action'] ) || $_GET['action'] !== self::ACTIVATION_URL_SLUG
				|| ! isset( $_GET['nonce'] )
			) {
				return;
			}

			if ( ! current_user_can( ebox_ADMIN_CAPABILITY_CHECK ) ) {
				return;
			}

			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			if ( ! wp_verify_nonce( $_GET['nonce'], self::ACTIVATION_URL_SLUG ) ) {
				return;
			}

			ebox_Settings_Section::set_section_setting( 'ebox_Settings_Section_Stripe_Connect', 'enabled', 'yes' );

			$redirect_url = admin_url( 'admin.php?page=' . self::SETUP_SLUG );

			ebox_safe_redirect( $redirect_url );
		}

		/**
		 * Filter for page title wrapper.
		 *
		 * @since 4.4.0
		 *
		 * @return string
		 */
		public function get_admin_page_title() : string {

			/** This filter is documented in includes/settings/class-ld-settings-pages.php */
			return apply_filters( 'ebox_admin_page_title', '<h1>' . $this->settings_page_title . '</h1>' );
		}

		/**
		 * Action function called when Add-ons page is loaded.
		 *
		 * @since 4.4.0
		 *
		 * @return void
		 */
		public function load_settings_page() : void {

			global $ebox_assets_loaded;

			$object = array(
				'ajaxurl'                          => admin_url( 'admin-ajax.php' ),
				'plugin_url'                       => ebox_LMS_PLUGIN_URL,
				'admin_dashboard_url'              => admin_url( '/' ),
				'ebox_cloud_setup_url'        => add_query_arg(
					array( 'page' => 'ebox-setup' ),
					admin_url( 'admin.php' )
				),
				'ebox_cloud_setup_wizard_url' => add_query_arg(
					array( 'page' => 'ebox-setup-wizard' ),
					admin_url( 'admin.php' )
				),
				'ebox_setup_wizard_url'       => add_query_arg(
					array( 'page' => 'ebox-setup-wizard' ),
					admin_url( 'admin.php' )
				),
			);

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( isset( $_GET['page'] ) && 'ebox-setup' === $_GET['page'] ) {
				Help_Page::enqueue_support_assets();

				wp_enqueue_style(
					'ebox-setup',
					ebox_LMS_PLUGIN_URL . '/assets/css/setup.css',
					array(),
					ebox_VERSION,
					'all'
				);
				$ebox_assets_loaded['styles']['ebox-admin-setup-page-style'] = __FUNCTION__;

				wp_enqueue_script(
					'ebox-setup',
					ebox_LMS_PLUGIN_URL . '/assets/js/setup.js',
					array( 'jquery', 'wp-element' ),
					ebox_VERSION,
					true
				);
				$ebox_assets_loaded['scripts']['ebox-admin-setup-page-script'] = __FUNCTION__;

				wp_localize_script(
					'ebox-setup',
					'eboxSetup',
					$object
				);
			}
		}

		/**
		 * Hide the tab menu items if on add-on page.
		 *
		 * @since 4.4.0
		 *
		 * @param array  $tab_set Tab Set.
		 * @param string $tab_key Tab Key.
		 * @param string $current_page_id ID of shown page.
		 *
		 * @return array
		 */
		public function ebox_admin_tab_sets( array $tab_set = array(), string $tab_key = '', string $current_page_id = '' ) : array {
			if ( ( ! empty( $tab_set ) ) && ( ! empty( $tab_key ) ) && ( ! empty( $current_page_id ) ) ) {
				if ( 'admin_page_ebox-setup' === $current_page_id ) {
					?>
					<style> h1.nav-tab-wrapper { display: none; }</style>
					<?php
				}
			}
			return $tab_set;
		}

		/**
		 * Output the page HTML.
		 *
		 * @since 4.4.0
		 *
		 * @return void
		 */
		public function show_settings_page() : void {
			$stripe_connect_connected        = Stripe_Connect::is_stripe_connected();
			$stripe_connect_activated        = ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_Stripe_Connect', 'enabled' ) === 'yes';
			$stripe_connect_needs_activation = $stripe_connect_connected && ! $stripe_connect_activated;
			$stripe_connect_completed        = $stripe_connect_connected && $stripe_connect_activated;
			$stripe_activation_url           = add_query_arg(
				array(
					'page'   => self::SETUP_SLUG,
					'action' => self::ACTIVATION_URL_SLUG,
					'nonce'  => wp_create_nonce( self::ACTIVATION_URL_SLUG ),
				),
				admin_url( 'admin.php' )
			);

			/**
			 * Filters steps shown on setup page.
			 *
			 * @since 4.4.0
			 *
			 * @param array $steps List of steps with arguments.
			 */
			$steps = apply_filters(
				'ebox_setup_steps',
				array(
					'site_setup'    => array(
						'class'              => 'setup',
						'completed'          => 'completed' === get_option( 'ebox_setup_wizard_status' ),
						'time_in_minutes'    => 5,
						'url'                => admin_url( 'admin.php?page=ebox-setup-wizard' ),
						'title'              => __( 'Setup your LMS', 'ebox' ),
						'description'        => __( 'Get Ready for the Fun Ride!', 'ebox' ),
						'action_label'       => __( 'LMS Details', 'ebox' ),
						'action_description' => __( 'Overview of Your Site: Briefly Describe Your Platform', 'ebox' ),
						'icon_url'           => ebox_LMS_PLUGIN_URL . '/assets/images/setup.png',
						'button_type'        => 'arrow',
						'button_class'       => '',
						'button_text'        => '',
					),
					'design_setup'  => array(
						'class'              => 'design',
						'completed'          => 'completed' === get_option( 'ebox_design_wizard_status' ),
						'time_in_minutes'    => 5,
						'url'                => admin_url( 'admin.php?page=ebox-design-wizard' ),
						'title'              => __( 'Crafting Your Website', 'ebox' ),
						'description'        => __( 'The Art of Impressions: Leveraging Appearances.', 'ebox' ),
						'action_label'       => __( 'Pick the Starter Template', 'ebox' ),
						'action_description' => __( 'Select a design as your starting point and personalize it. This action will replace your current theme, potentially add extra content, and modify settings on your website.', 'ebox' ),
						'icon_url'           => ebox_LMS_PLUGIN_URL . '/assets/images/design.png',
						'button_type'        => 'arrow',
						'button_class'       => '',
						'button_text'        => '',
					),
					'payment_setup' => array(
						'class'              => 'payment',
						'completed'          => $stripe_connect_completed,
						'time_in_minutes'    => $stripe_connect_needs_activation ? null : 5,
						'url'                => $stripe_connect_needs_activation ? $stripe_activation_url : Stripe_Connect::generate_connect_url(),
						'title'              => __( 'Set up payment configuration', 'ebox' ),
						'description'        => __( 'Maximize your earnings potential.', 'ebox' ),
						'action_label'       => __( 'Set Up Stripe', 'ebox' ),
						'action_description' => __( 'Process credit card payments and enjoy reduced merchant fees.', 'ebox' ),
						'icon_url'           => ebox_LMS_PLUGIN_URL . '/assets/images/payment.png',
						'button_type'        => 'button',
						'button_class'       => 'button-stripe',
						'button_text'        => $stripe_connect_needs_activation ? __( 'Activate', 'ebox' ) : __( 'Connect Stripe', 'ebox' ),
					),
				// 	'documentation' => array(
				// 		'class'              => 'courses',
				// 		'completed'          => null,
				// 		'time_in_minutes'    => null,
				// 		'url'                => null,
				// 		'title'              => __( 'Manage your courses', 'ebox' ),
				// 		'description'        => __( 'Get your coursework set up for success.', 'ebox' ),
				// 		'action_label'       => null,
				// 		'action_description' => null,
				// 		'icon_url'           => null,
				// 		'button_type'        => null,
				// 		'button_class'       => null,
				// 		'button_text'        => null,
				// 		'content_path'       => 'setup/components/content-documentation',
				// 	),
				)
			);

			ebox_LMS::get_view(
				'setup/setup',
				array(
					'steps'            => $steps,
					'setup_wizard'     => $this,
					'overview_video'   => Help_Page::get_articles( 'overview_video' )[0],
					'overview_article' => Help_Page::get_articles( 'overview_article' )[0],
				),
				true
			);
		}
	}
}

add_action(
	'ebox_settings_pages_init',
	function() {
		ebox_Settings_Page_Setup::add_page_instance();
	}
);
