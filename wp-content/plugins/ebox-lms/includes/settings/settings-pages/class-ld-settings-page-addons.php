<?php
/**
 * ebox Settings Page Add-ons.
 *
 * @since 2.5.4
 *
 * @package ebox\Settings\Pages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Page' ) ) && ( ! class_exists( 'ebox_Settings_Page_Addons' ) ) ) {
	/**
	 * Class ebox Settings Page Add-ons.
	 *
	 * @since 2.5.4
	 */
	class ebox_Settings_Page_Addons extends ebox_Settings_Page {

		/**
		 * Public constructor for class
		 *
		 * @since 2.5.4
		 */
		public function __construct() {
			$this->parent_menu_page_url  = 'admin.php?page=ebox_lms_addons';
			$this->menu_page_capability  = ebox_ADMIN_CAPABILITY_CHECK;
			$this->settings_page_id      = 'ebox_lms_addons';
			$this->settings_page_title   = esc_html__( 'ebox Add-ons', 'ebox' );
			$this->settings_tab_title    = esc_html__( 'Add-ons', 'ebox' );
			$this->settings_tab_priority = 0;

			// Override action with custom plugins function for add-ons.
			add_action( 'install_plugins_pre_plugin-information', array( $this, 'shows_addon_plugin_information' ), 5 );
			add_filter( 'ebox_submenu_last', array( $this, 'submenu_item' ), 200 );

			add_filter( 'ebox_admin_tab_sets', array( $this, 'ebox_admin_tab_sets' ), 10, 3 );
			add_filter( 'ebox_header_data', array( $this, 'admin_header' ), 40, 3 );

			parent::__construct();
		}

		/**
		 * Control visibility of submenu items based on license status
		 *
		 * @since 2.5.5
		 *
		 * @param array $submenu Submenu item to check.
		 * @return array $submenu
		 */
		public function submenu_item( $submenu ) {
			if ( ! isset( $submenu[ $this->settings_page_id ] ) ) {
				if ( ! ebox_is_ebox_hub_active() && ebox_is_ebox_license_valid() ) {
					$submenu[ $this->settings_page_id ] = array(
						'name' => $this->settings_tab_title,
						'cap'  => $this->menu_page_capability,
						'link' => $this->parent_menu_page_url,
					);
				}
			}

			return $submenu;
		}

		/**
		 * Filter the admin header data. We don't want to show the header panel on the Overview page.
		 *
		 * @since 3.0.0
		 *
		 * @param array  $header_data Array of header data used by the Header Panel React app.
		 * @param string $menu_key The menu key being displayed.
		 * @param array  $menu_items Array of menu/tab items.
		 *
		 * @return array $header_data.
		 */
		public function admin_header( $header_data = array(), $menu_key = '', $menu_items = array() ) {
			// Clear out $header_data if we are showing our page.
			if ( $menu_key === $this->parent_menu_page_url ) {
				$header_data = array();
			}

			return $header_data;
		}

		/**
		 * Filter for page title wrapper.
		 *
		 * @since 2.5.5
		 */
		public function get_admin_page_title() {

			/** This filter is documented in includes/settings/class-ld-settings-pages.php */
			return apply_filters( 'ebox_admin_page_title', '<h1>' . esc_html( $this->settings_page_title ) . '</h1>' );
		}

		/**
		 * Action function called when Add-ons page is loaded.
		 *
		 * @since 2.5.5
		 */
		public function load_settings_page() {

			$license_status = ebox_is_ebox_license_valid();
			if ( $license_status ) {
				require_once ebox_LMS_PLUGIN_DIR . 'includes/admin/class-ebox-admin-addons-list-table.php';

				wp_enqueue_style( 'plugin-install' );
				wp_enqueue_script( 'plugin-install' );
				wp_enqueue_script( 'updates' );

				add_thickbox();

				return;
			}

			$setup_url = add_query_arg( 'page', 'ebox-setup', admin_url( 'admin.php' ) );
			ebox_safe_redirect( $setup_url );
		}

		/**
		 * Hide the tab menu items if on add-one page.
		 *
		 * @since 2.5.5
		 *
		 * @param array  $tab_set Tab Set.
		 * @param string $tab_key Tab Key.
		 * @param string $current_page_id ID of shown page.
		 *
		 * @return array $tab_set
		 */
		public function ebox_admin_tab_sets( $tab_set = array(), $tab_key = '', $current_page_id = '' ) {
			if ( ( ! empty( $tab_set ) ) && ( ! empty( $tab_key ) ) && ( ! empty( $current_page_id ) ) ) {
				if ( 'admin_page_ebox_lms_addons' === $current_page_id ) {
					?>
					<style> h1.nav-tab-wrapper { display: none; }</style>
					<?php
				}
			}
			return $tab_set;
		}

		/**
		 * Custom display function for page content.
		 *
		 * @since 2.5.5
		 */
		public function show_settings_page() {

			?>
			<div class="wrap ebox-settings-page-wrap">

				<?php settings_errors(); ?>

				<?php
				/** This filter is documented in includes/settings/class-ld-settings-pages.php */
				do_action( 'ebox_settings_page_before_title', $this->settings_screen_id );
				?>
				<?php echo wp_kses_post( $this->get_admin_page_title() ); ?>
				<?php
				/** This filter is documented in includes/settings/class-ld-settings-pages.php */
				do_action( 'ebox_settings_page_after_title', $this->settings_screen_id );
				?>

				<?php
				/** This filter is documented in includes/settings/class-ld-settings-pages.php */
				do_action( 'ebox_settings_page_before_form', $this->settings_screen_id );
				?>
				<div id="plugin-filter-xxx">
				<?php echo $this->get_admin_page_form( true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Need to output HTML. ?>
				<?php
				/** This filter is documented in includes/settings/class-ld-settings-pages.php */
				do_action( 'ebox_settings_page_inside_form_top', $this->settings_screen_id );
				?>
					<?php
						$wp_list_table = new ebox_Admin_Addons_List_Table();
						$wp_list_table->prepare_items();

						$wp_list_table->views();
						$wp_list_table->display();
					?>
				<?php
				/** This filter is documented in includes/settings/class-ld-settings-pages.php */
				do_action( 'ebox_settings_page_inside_form_bottom', $this->settings_screen_id );
				?>
				<?php echo $this->get_admin_page_form( false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Need to output HTML. ?>
				</div>
				<?php
				/** This filter is documented in includes/settings/class-ld-settings-pages.php */
				do_action( 'ebox_settings_page_after_form', $this->settings_screen_id );
				?>
			</div>
			<?php
			/**
			 * The following is needed to trigger the wp-admin/js/updates.js logic in
			 * wp.updates.updatePlugin() where is checks for specific pagenow values
			 * but doesn't leave any option for externals.
			 */
			?>
			<script type="text/javascript">
				//pagenow = 'plugin-install';
			</script>
			<?php
		}

		/**
		 * Display plugin information in dialog box form.
		 *
		 * @since 2.5.5
		 *
		 * @global string $tab
		 */
		public function shows_addon_plugin_information() {
			if ( empty( $_REQUEST['plugin'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				return;
			}

			$addon_updater             = ebox_Addon_Updater::get_instance();
			$plugin_readme_information = $addon_updater->get_plugin_information( esc_attr( $_REQUEST['plugin'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			if ( empty( $plugin_readme_information ) ) {
				return;
			}

			$api = new StdClass();
			foreach ( $plugin_readme_information as $_k => $_s ) {
				$api->$_k = $_s;
			}

			$plugins_allowed_tags = array(
				'a'          => array(
					'href'   => array(),
					'title'  => array(),
					'target' => array(),
				),
				'abbr'       => array(
					'title' => array(),
				),
				'acronym'    => array(
					'title' => array(),
				),
				'code'       => array(),
				'pre'        => array(),
				'em'         => array(),
				'strong'     => array(),
				'div'        => array(
					'class' => array(),
				),
				'span'       => array(
					'class' => array(),
				),
				'p'          => array(),
				'br'         => array(),
				'ul'         => array(),
				'ol'         => array(),
				'li'         => array(),
				'h1'         => array(),
				'h2'         => array(),
				'h3'         => array(),
				'h4'         => array(),
				'h5'         => array(),
				'h6'         => array(),
				'img'        => array(
					'src'   => array(),
					'class' => array(),
					'alt'   => array(),
				),
				'blockquote' => array(
					'cite' => true,
				),
			);

			$plugins_section_titles = array(
				'description'  => _x( 'Description', 'Plugin installer section title', 'ebox' ),
				'installation' => _x( 'Installation', 'Plugin installer section title', 'ebox' ),
				'faq'          => _x( 'FAQ', 'Plugin installer section title', 'ebox' ),
				'screenshots'  => _x( 'Screenshots', 'Plugin installer section title', 'ebox' ),
				'changelog'    => _x( 'Changelog', 'Plugin installer section title', 'ebox' ),
				'reviews'      => _x( 'Reviews', 'Plugin installer section title', 'ebox' ),
				'other_notes'  => _x( 'Other Notes', 'Plugin installer section title', 'ebox' ),
			);

			// Sanitize HTML.
			foreach ( (array) $api->sections as $section_name => $content ) {
				$api->sections[ $section_name ] = wp_kses( $content, $plugins_allowed_tags );
			}

			foreach ( array( 'version', 'author', 'requires', 'tested', 'homepage', 'downloaded', 'slug' ) as $key ) {
				if ( isset( $api->$key ) ) {
					$api->$key = wp_kses( $api->$key, $plugins_allowed_tags );
				}
			}

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$section = isset( $_REQUEST['section'] ) ? wp_unslash( $_REQUEST['section'] ) : 'description'; // Default to the Description tab, Do not translate, API returns English.
			if ( empty( $section ) || ! isset( $api->sections[ $section ] ) ) {
				$section_titles = array_keys( (array) $api->sections );
				$section        = reset( $section_titles );
			}

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( ( isset( $_GET['tab'] ) ) && ( ! empty( $_GET['tab'] ) ) ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
				$tab = esc_attr( $_GET['tab'] );
			} else {
				$tab = 'plugin-information';
			}
			$_tab = $tab;

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$section = isset( $_REQUEST['section'] ) ? wp_unslash( $_REQUEST['section'] ) : 'description'; // Default to the Description tab, Do not translate, API returns English.
			if ( empty( $section ) || ! isset( $api->sections[ $section ] ) ) {
				$section_titles = array_keys( (array) $api->sections );
				$section        = reset( $section_titles );
			}

			iframe_header( __( 'Plugin Installation', 'ebox' ) );

			$_with_banner = '';

			if ( ! empty( $api->banners ) && ( ! empty( $api->banners['low'] ) || ! empty( $api->banners['high'] ) ) ) {
				$_with_banner = 'with-banner';
				$low          = empty( $api->banners['low'] ) ? $api->banners['high'] : $api->banners['low'];
				$high         = empty( $api->banners['high'] ) ? $api->banners['low'] : $api->banners['high'];
				?>
				<style type="text/css">
					#plugin-information-title.with-banner {
						background-image: url( <?php echo esc_url( $low ); ?> );
					}
					@media only screen and ( -webkit-min-device-pixel-ratio: 1.5 ) {
						#plugin-information-title.with-banner {
							background-image: url( <?php echo esc_url( $high ); ?> );
						}
					}
				</style>
				<?php
			}

			echo '<div id="plugin-information-scrollable">';
			echo "<div id='" . esc_attr( $_tab ) . "-title' class='" . esc_attr( $_with_banner ) . "'><div class='vignette'></div><h2>" . esc_html( $api->name ) . '</h2></div>';
			echo "<div id='" . esc_attr( $_tab ) . "-tabs' class='" . esc_attr( $_with_banner ) . "'>\n";

			foreach ( (array) $api->sections as $section_name => $content ) {
				if ( 'reviews' === $section_name && ( empty( $api->ratings ) || 0 === array_sum( (array) $api->ratings ) ) ) {
					continue;
				}

				if ( isset( $plugins_section_titles[ $section_name ] ) ) {
					$title = $plugins_section_titles[ $section_name ];
				} else {
					$title = ucwords( str_replace( '_', ' ', $section_name ) );
				}

				$class = ( $section_name === $section ) ? ' class="current"' : '';
				$href  = add_query_arg(
					array(
						'tab'     => $tab,
						'section' => $section_name,
					)
				);
				echo "\t<a name='" . esc_attr( $section_name ) . "' href='" . esc_url( $href ) . "' $class>" . esc_html( $title ) . "</a>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $class hardcoded above.
			}

			echo "</div>\n";

			?>
		<div id="<?php echo esc_attr( $_tab ); ?>-content" class='<?php echo esc_attr( $_with_banner ); ?>'>
			<div class="fyi">
				<ul>
					<?php if ( ! empty( $api->version ) ) { ?>
						<li><strong><?php esc_html_e( 'Version:', 'ebox' ); ?></strong> <?php echo esc_html( $api->version ); ?></li>
					<?php } if ( ! empty( $api->author ) ) { ?>
						<li><strong><?php esc_html_e( 'Author:', 'ebox' ); ?></strong> <?php echo wp_kses_post( links_add_target( $api->author, '_blank' ) ); ?></li>
					<?php } if ( ! empty( $api->last_updated ) ) { ?>
						<li><strong><?php esc_html_e( 'Last Updated:', 'ebox' ); ?></strong>
							<?php
							/* translators: %s: Time since the last update */
							printf( esc_html__( '%s ago', 'ebox' ), esc_html( human_time_diff( strtotime( $api->last_updated ) ) ) );
							?>
						</li>
					<?php } if ( ! empty( $api->requires ) ) { ?>
						<li>
							<strong><?php esc_html_e( 'Requires WordPress Version:', 'ebox' ); ?></strong>
							<?php
							/* translators: %s: WordPress version */
							printf( esc_html__( '%s or higher', 'ebox' ), esc_html( $api->requires ) );
							?>
						</li>
					<?php } if ( ! empty( $api->tested ) ) { ?>
						<li><strong><?php esc_html_e( 'Compatible up to:', 'ebox' ); ?></strong> <?php echo esc_html( $api->tested ); ?></li>
					<?php } if ( isset( $api->active_installs ) ) { ?>
						<li><strong><?php esc_html_e( 'Active Installations:', 'ebox' ); ?></strong>
						<?php
						if ( $api->active_installs >= 1000000 ) {
							echo esc_html_x( '1+ Million', 'Active plugin installations', 'ebox' );
						} elseif ( 0 == $api->active_installs ) {
							echo esc_html_x( 'Less Than 10', 'Active plugin installations', 'ebox' );
						} else {
							echo esc_html( number_format_i18n( $api->active_installs ) ) . '+';
						}
						?>
						</li>
					<?php } if ( ! empty( $api->slug ) && empty( $api->external ) ) { ?>
						<li><a target="_blank" href="<?php echo esc_url( __( 'https://wordpress.org/plugins/', 'ebox' ) ) . esc_html( $api->slug ); ?>/"><?php esc_html_e( 'WordPress.org Plugin Page &#187;', 'ebox' ); ?></a></li>
					<?php } if ( ! empty( $api->homepage ) ) { ?>
						<li><a target="_blank" href="<?php echo esc_url( $api->homepage ); ?>"><?php esc_html_e( 'Plugin Homepage &#187;', 'ebox' ); ?></a></li>
					<?php } if ( ! empty( $api->donate_link ) && empty( $api->contributors ) ) { ?>
						<li><a target="_blank" href="<?php echo esc_url( $api->donate_link ); ?>"><?php esc_html_e( 'Donate to this plugin &#187;', 'ebox' ); ?></a></li>
					<?php } ?>
				</ul>
				<?php if ( ! empty( $api->rating ) ) { ?>
					<h3><?php esc_html_e( 'Average Rating', 'ebox' ); ?></h3>
					<?php
					wp_star_rating(
						array(
							'rating' => $api->rating,
							'type'   => 'percent',
							'number' => $api->num_ratings,
						)
					);
					?>
					<p aria-hidden="true" class="fyi-description">
					<?php
					printf(
						// translators: placeholder: Number of ratings.
						esc_html( _n( '(based on %s rating)', '(based on %s ratings)', esc_html( $api->num_ratings ), 'ebox' ) ),
						esc_html( number_format_i18n( $api->num_ratings ) )
					);
					?>
					</p>
					<?php
				}

				if ( ! empty( $api->ratings ) && array_sum( (array) $api->ratings ) > 0 ) {
					?>
					<h3><?php esc_html_e( 'Reviews', 'ebox' ); ?></h3>
					<p class="fyi-description"><?php esc_html_e( 'Read all reviews on WordPress.org or write your own!', 'ebox' ); ?></p>
					<?php
					foreach ( $api->ratings as $key => $rate_count ) {
						// Avoid div-by-zero.
						$_rating    = $api->num_ratings ? ( $rate_count / $api->num_ratings ) : 0;
						$aria_label = esc_attr(
							sprintf(
								// translators: 1: number of stars (used to determine singular/plural), 2: number of reviews.
								_n( 'Reviews with %1$d star: %2$s. Opens in a new window.', 'Reviews with %1$d stars: %2$s. Opens in a new window.', $key, 'ebox' ),
								$key,
								number_format_i18n( $rate_count ),
								'ebox'
							)
						);
						?>
						<div class="counter-container">
								<span class="counter-label">
									<a href="https://wordpress.org/support/view/plugin-reviews/<?php echo esc_attr( $api->slug ); ?>?filter=<?php echo esc_attr( $key ); ?>" target="_blank" aria-label="<?php echo esc_attr( $aria_label ); ?>">
									<?php
									printf(
										esc_html(
											// translators: placeholder: Number of stars.
											_n( '%d star', '%d stars', $key, 'ebox' )
										),
										esc_html( $key )
									);
									?>
									</a>
								</span>
								<span class="counter-back">
									<span class="counter-bar" style="width: <?php echo 92 * esc_attr( $_rating ); ?>px;"></span>
								</span>
							<span class="counter-count" aria-hidden="true"><?php echo esc_html( number_format_i18n( $rate_count ) ); ?></span>
						</div>
						<?php
					}
				}
				if ( ! empty( $api->contributors ) ) {
					?>
					<h3><?php esc_html_e( 'Contributors', 'ebox' ); ?></h3>
					<ul class="contributors">
						<?php
						foreach ( (array) $api->contributors as $contrib_username => $contrib_profile ) {
							if ( empty( $contrib_username ) && empty( $contrib_profile ) ) {
								continue;
							}
							if ( empty( $contrib_username ) ) {
								$contrib_username = preg_replace( '/^.+\/(.+)\/?$/', '\1', $contrib_profile );
							}
							$contrib_username = sanitize_user( $contrib_username );
							if ( empty( $contrib_profile ) ) {
								echo "<li><img src='https://wordpress.org/grav-redirect.php?user='" . esc_html( $contrib_username ) . "'&amp;s=36' width='18' height='18' alt='' />" . esc_html( $contrib_username ) . '</li>';
							} else {
								echo "<li><a href='" . esc_url( $contrib_profile ) . "' target='_blank'><img src='https://wordpress.org/grav-redirect.php?user='" . esc_html( $contrib_username ) . "'&amp;s=36' width='18' height='18' alt='' " . esc_html( $contrib_username ) . '</a></li>';
							}
						}
						?>
					</ul>
									<?php if ( ! empty( $api->donate_link ) ) { ?>
						<a target="_blank" href="<?php echo esc_url( $api->donate_link ); ?>"><?php esc_html_e( 'Donate to this plugin &#187;', 'ebox' ); ?></a>
					<?php } ?>
								<?php } ?>
			</div>
			<div id="section-holder">
			<?php
			$wp_version = get_bloginfo( 'version' );

			if ( ! empty( $api->tested ) && version_compare( substr( $wp_version, 0, strlen( $api->tested ) ), $api->tested, '>' ) ) {
				echo '<div class="notice notice-warning notice-alt"><p>' . wp_kses_post( __( '<strong>Warning:</strong> This plugin has <strong>not been tested</strong> with your current version of WordPress.', 'ebox' ) ) . '</p></div>';
			} elseif ( ! empty( $api->requires ) && version_compare( substr( $wp_version, 0, strlen( $api->requires ) ), $api->requires, '<' ) ) {
				echo '<div class="notice notice-warning notice-alt"><p>' . wp_kses_post( __( '<strong>Warning:</strong> This plugin has <strong>not been marked as compatible</strong> with your version of WordPress.', 'ebox' ) ) . '</p></div>';
			}

			foreach ( (array) $api->sections as $section_name => $content ) {
				$content = links_add_base_url( $content, 'https://wordpress.org/plugins/' . $api->slug . '/' );
				$content = links_add_target( $content, '_blank' );
				$display = ( $section_name === $section ) ? 'block' : 'none';

				echo "\t<div id='section-" . esc_attr( $section_name ) . "' class='section' style='display: " . esc_attr( $display ) . "'>\n";
				echo wp_kses_post( $content );
				echo "\t</div>\n";
			}
			echo "</div>\n";
			echo "</div>\n";
			echo "</div>\n"; // #plugin-information-scrollable
			echo "<div id='" . esc_attr( $tab ) . "-footer'>\n";
			if ( empty( $api->download_link ) && ( current_user_can( 'install_plugins' ) || current_user_can( 'update_plugins' ) ) ) {
				if ( isset( $api->plugin_status ) ) {
					$status = $api->plugin_status;
					switch ( $status['status'] ) {
						case 'install':
							if ( $status['url'] ) {
								echo '<a data-slug="' . esc_attr( $api->slug ) . '" id="plugin_install_from_iframe" class="button button-primary right" href="' . esc_url( $status['url'] ) . '" target="_parent">' . esc_html__( 'Install Now', 'ebox' ) . '</a>';
							}
							break;
						case 'update_available':
							if ( $status['url'] ) {
								echo '<a data-slug="' . esc_attr( $api->slug ) . '" data-plugin="' . esc_attr( $status['file'] ) . '" id="plugin_update_from_iframe" class="button button-primary right" href="' . esc_url( $status['url'] ) . '" target="_parent">' . esc_html__( 'Install Update Now', 'ebox' ) . '</a>';
							}
							break;
						case 'newer_installed':
							/* translators: %s: Plugin version */
							echo '<a class="button button-primary right disabled">' . sprintf( esc_html__( 'Newer Version (%s) Installed', 'ebox' ), esc_html( $status['version'] ) ) . '</a>';
							break;
						case 'latest_installed':
							echo '<a class="button button-primary right disabled">' . esc_html__( 'Latest Version Installed', 'ebox' ) . '</a>';
							break;
					}
				}
			}
			echo "</div>\n";

			iframe_footer();
			exit;
		}
	}
}
add_action(
	'ebox_settings_pages_init',
	function() {
		ebox_Settings_Page_Addons::add_page_instance();
	}
);
