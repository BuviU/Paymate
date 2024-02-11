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

if ( class_exists( 'ebox_Settings_Page' ) && ! class_exists( 'ebox_Settings_Page_Help' ) ) {
	/**
	 * Class ebox Settings Page Overview.
	 *
	 * @since 4.4.0
	 */
	class ebox_Settings_Page_Help extends ebox_Settings_Page {

		/**
		 * Public constructor for class
		 *
		 * @since 4.4.0
		 */
		public function __construct() {
			$this->parent_menu_page_url  = 'admin.php?page=ebox-help';
			$this->menu_page_capability  = ebox_ADMIN_CAPABILITY_CHECK;
			$this->settings_page_id      = 'ebox-help';
			$this->settings_page_title   = esc_html__( 'ebox Help', 'ebox' );
			$this->settings_tab_title    = esc_html__( 'Help', 'ebox' );
			$this->settings_tab_priority = 100;

			if ( ! ebox_cloud_is_enabled() ) {
				add_filter( 'ebox_submenu', array( $this, 'submenu_item' ), 200 );

				add_filter( 'ebox_admin_tab_sets', array( $this, 'ebox_admin_tab_sets' ), 10, 3 );
				add_filter( 'ebox_header_data', array( $this, 'admin_header' ), 40, 3 );
				add_action( 'admin_head', array( $this, 'output_admin_inline_scripts' ) );

				parent::__construct();
			}
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
					$submenu,
					array(
						$this->settings_page_id => array(
							'name'  => $this->settings_tab_title,
							'cap'   => $this->menu_page_capability,
							'link'  => $this->parent_menu_page_url,
							'class' => 'submenu-ldlms-help',
						),
					)
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
			?>
            <?php // phpcs:ignore?>
            <?php if ( isset( $_GET['page'] ) && in_array( $_GET['page'], [ 'ebox-help' ], true ) ) : ?>
				<style>
					body .notice {
						display: none;
					}
				</style>
			<?php endif; ?>
			<?php
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
		 * Enqueue support assets
		 *
		 * @since 4.4.0
		 *
		 * @return void
		 */
		public static function enqueue_support_assets() : void {
			wp_enqueue_style(
				'ebox-help',
				ebox_LMS_PLUGIN_URL . '/assets/css/help.css',
				array(),
				ebox_VERSION,
				'all'
			);

			wp_enqueue_script(
				'ebox-help',
				ebox_LMS_PLUGIN_URL . '/assets/js/help.js',
				array( 'jquery' ),
				ebox_VERSION,
				true
			);
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

			self::enqueue_support_assets();

			$ebox_assets_loaded['styles']['ebox-admin-help-page-style'] = __FUNCTION__;

			$ebox_assets_loaded['scripts']['ebox-admin-help-page-script'] = __FUNCTION__;
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
				if ( 'admin_page_ebox-help' === $current_page_id ) {
					?>
					<style> h1.nav-tab-wrapper { display: none; }</style>
					<?php
				}
			}
			return $tab_set;
		}

		/**
		 * Output page display HTML.
		 *
		 * @since 4.4.0
		 *
		 * @return void
		 */
		public function show_settings_page() : void {
			$categories = self::get_categories();

			ebox_LMS::get_view(
				'support/help',
				array(
					'categories' => $categories,
				),
				true
			);
		}

		/**
		 * Get categories
		 *
		 * @since 4.4.0
		 *
		 * @return array<string, array<string, string>>
		 */
		public static function get_categories() : array {
			$categories = array(
				'getting-started'     => array(
					'id'          => 'getting-started',
					'helpScoutId' => '',
					'label'       => __( 'Getting Started', 'ebox' ),
                    // phpcs:ignore Generic.Files.LineLength.TooLong
					'description' => __( 'Not sure what to do next? Read our top articles to get more information.', 'ebox' ),
					'icon'        => 'getting-started',
				),
				'ebox-core'      => array(
					'id'          => 'ebox-core',
					'helpScoutId' => '',
					'label'       => __( 'Ebox Core', 'ebox' ),
                    // phpcs:ignore Generic.Files.LineLength.TooLong
					'description' => __( 'Everything about Ebox LMS core plugin.', 'ebox' ),
					'icon'        => 'core',
				),
				'add-ons'             => array(
					'id'          => 'add-ons',
					'helpScoutId' => '',
					'label'       => __( 'Add-Ons', 'ebox' ),
                    // phpcs:ignore Generic.Files.LineLength.TooLong
					'description' => __( 'Course Grid, Stripe, WooCommerce, Zapier, and other official add-ons documentations.', 'ebox' ),
					'icon'        => 'addons',
				),
				'users-and-teams'    => array(
					'id'          => 'users-and-teams',
					'helpScoutId' => '',
					'label'       => __( 'Users & Teams', 'ebox' ),
                    // phpcs:ignore Generic.Files.LineLength.TooLong
					'description' => __( 'Have questions about users & teams? Our articles may help.', 'ebox' ),
					'icon'        => 'users-teams',
				),
				'reporting'           => array(
					'id'          => 'reporting',
					'helpScoutId' => '',
					'label'       => __( 'Reporting', 'ebox' ),
                    // phpcs:ignore Generic.Files.LineLength.TooLong
					'description' => __( 'ebox reporting guides.', 'ebox' ),
					'icon'        => 'reporting',
				),
				'user-guides'         => array(
					'id'          => 'user-guides',
					'helpScoutId' => '',
					'label'       => __( 'User Guides', 'ebox' ),
                    // phpcs:ignore Generic.Files.LineLength.TooLong
					'description' => __( 'Collection of guides that will help you accomplish certain tasks.', 'ebox' ),
					'icon'        => 'user-guides',
				),
				'troubleshooting'     => array(
					'id'          => 'troubleshooting',
					'helpScoutId' => '',
					'label'       => __( 'Troubleshooting', 'ebox' ),
                    // phpcs:ignore Generic.Files.LineLength.TooLong
					'description' => __( 'Have issues? Follow our troubleshooting guides to resolve them.', 'ebox' ),
					'icon'        => 'troubleshooting',
				),
				'faqs'                => array(
					'id'          => 'faqs',
					'helpScoutId' => '',
					'label'       => __( 'FAQs', 'ebox' ),
                    // phpcs:ignore Generic.Files.LineLength.TooLong
					'description' => __( 'Have a question? See if it\'s already been answered.', 'ebox' ),
					'icon'        => 'faqs',
				),
				'account-and-billing' => array(
					'id'          => 'account-and-billing',
					'helpScoutId' => '',
					'label'       => __( 'Accounts & Billing', 'ebox' ),
                    // phpcs:ignore Generic.Files.LineLength.TooLong
					'description' => __( 'Accounts & Billing related articles.', 'ebox' ),
					'icon'        => 'accounts-billing',
				),
			);

			return $categories;
		}

		/**
		 * Get article categories.
		 *
		 * @since 4.4.0
		 *
		 * @param array<string> $exclude_categories Category keys that will excluded in the result.
		 *
		 * @return array<string, string>
		 */
		public static function get_articles_categories( array $exclude_categories = array() ) : array {
			$categories = array(
				'additional_resources' => __( 'Additional Resources', 'ebox' ),
				'build_courses'        => __( 'Build Courses', 'ebox' ),
				'sell_courses'         => __( 'Sell Your Courses', 'ebox' ),
				'manage_students'      => __( 'Manage Students', 'ebox' ),
			);

			if ( ! empty( $exclude_categories ) ) {
				$categories = array_filter(
					$categories,
					function ( $category ) use ( $exclude_categories ) {
						return ! in_array( $category, $exclude_categories, true );
					},
					ARRAY_FILTER_USE_KEY
				);
			}

			return $categories;
		}

		/**
		 * Get selected articles.
		 *
		 * @since 4.4.0
		 *
		 * @param string        $category           Category key the returned articles are from.
		 * @param array<string> $exclude_categories Excluded category keys the returned articles are from.
		 *
		 * @return array<int, array<string, array<int, string>|string>>
		 */
		public static function get_articles( string $category = null, array $exclude_categories = array() ) : array {
			$articles = array(
				array(
					'type'       => 'youtube_video',
					'title'      => __( 'Welcome to ebox', 'ebox' ),
					'youtube_id' => 'hcSTaMhZi64',
					'category'   => 'overview_video',
				),
				array(
					'type'     => 'vimeo_video',
					'title'    => __( 'A Brief Overview of ebox', 'ebox' ),
					'vimeo_id' => '797750743',
					'category' => 'overview_article',
				),
				array(
					'type'     => 'url',
					'title'    => __( 'ebox 101', 'ebox' ),
					'url'      => 'https://academy.ebox.com/courses/ebox-101/',
					'category' => 'additional_resources',
				),
				array(
					'type'     => 'url',
					'title'    => __( 'WordPress 101', 'ebox' ),
					'url'      => 'https://academy.ebox.com/courses/wordpress-101/',
					'category' => 'additional_resources',
				),
				array(
					'type'     => 'helpscout_action',
					'title'    => __( 'ebox Documentation', 'ebox' ),
					'action'   => 'open_doc',
					'keyword'  => '',
					'category' => 'additional_resources',
				),
				array(
					'type'         => 'article',
					'title'        => __( 'Getting Started', 'ebox' ),
					'helpscout_id' => '62a0e4f0e1d2cf0eac00f2bb',
					'category'     => 'additional_resources',
				),
				array(
					'type'     => 'helpscout_action',
					'title'    => __( 'Contact Support', 'ebox' ),
					'action'   => 'open_chat',
					'keyword'  => '',
					'category' => 'additional_resources',
				),
				array(
					'type'     => 'vimeo_video',
					'title'    => __( 'Creating Courses with the Course Builder [Video]', 'ebox' ),
					'vimeo_id' => '798775119',
					'category' => 'build_courses',
				),
				array(
					'type'     => 'vimeo_video',
					'title'    => __( 'Adding Content with modules & Topics [Video]', 'ebox' ),
					'vimeo_id' => '798793610',
					'category' => 'build_courses',
				),
				array(
					'type'     => 'vimeo_video',
					'title'    => __( 'Creating Quizzes [Video]', 'ebox' ),
					'vimeo_id' => '799349718',
					'category' => 'build_courses',
				),
				array(
					'type'     => 'vimeo_video',
					'title'    => __( 'PayPal Settings [Video]', 'ebox' ),
					'vimeo_id' => '799333129',
					'category' => 'sell_courses',
				),
				array(
					'type'     => 'vimeo_video',
					'title'    => __( 'Stripe Integration [Video]', 'ebox' ),
					'vimeo_id' => '799333097',
					'category' => 'sell_courses',
				),
				array(
					'type'         => 'article',
					'title'        => __( 'WooCommerce Integration [Article]', 'ebox' ),
					'helpscout_id' => '6216b293aca5bb2b753c5c7f',
					'category'     => 'sell_courses',
				),
				array(
					'type'     => 'vimeo_video',
					'title'    => __( 'Course Access Settings [Video]', 'ebox' ),
					'vimeo_id' => '798788916',
					'category' => 'sell_courses',
				),
				array(
					'type'     => 'vimeo_video',
					'title'    => __( 'Setting Up User Registration [Video]', 'ebox' ),
					'vimeo_id' => '799330885',
					'category' => 'manage_students',
				),
				array(
					'type'         => 'article',
					'title'        => __( 'Adding a User Profile Page', 'ebox' ),
					'helpscout_id' => '6216c2961173d072c69fb37a',
					'category'     => 'manage_students',
				),
				array(
					'type'         => 'article',
					'title'        => __( 'ebox Login & Registration [Guide]', 'ebox' ),
					'helpscout_id' => '6217ffea1173d072c69fba4d',
					'category'     => 'manage_students',
				),
			);

			if ( ! empty( $category ) ) {
				$articles = array_values(
					array_filter(
						$articles,
						function ( $article ) use ( $category ) {
							return $article['category'] === $category;
						}
					)
				);
			}

			if ( ! empty( $exclude_categories ) ) {
				$articles = array_values(
					array_filter(
						$articles,
						function ( $article ) use ( $exclude_categories ) {
							return ! in_array( $article['category'], $exclude_categories, true );
						}
					)
				);
			}

			return $articles;
		}
	}
}

add_action(
	'ebox_settings_pages_init',
	function() {
		ebox_Settings_Page_Help::add_page_instance();
	}
);
