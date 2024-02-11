<?php
/**
 * ebox Team Membership.
 *
 * @since 3.2.0
 * @package ebox\Teams
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'LD_Teams_Membership' ) ) {
	/**
	 * Class to create the instance.
	 *
	 * @since 3.2.0
	 */
	class LD_Teams_Membership {
 // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound

		/**
		 * Static instance variable to ensure
		 * only one instance of class is used.
		 *
		 * @var object $instance
		 */
		protected static $instance = null;

		/**
		 * Team Membership metabox instance.
		 *
		 * @var object $mb_instance
		 */
		protected $mb_instance = null;

		/**
		 * Team Membership settings.
		 *
		 * @var array $global_setting
		 */
		protected $global_setting = null;

		/**
		 * Team Membership Post settings.
		 *
		 * @var array $post_setting
		 */
		protected $post_setting = null;

		/**
		 * Array of runtime vars.
		 *
		 * @var array $vars Includes post_id, post, user_id, user, debug.
		 */
		protected $vars = array();

		/**
		 * Get or create instance object of class.
		 *
		 * @since 3.2.0
		 */
		final public static function get_instance() {
			if ( ! isset( static::$instance ) ) {
				static::$instance = new self();
			}

			return static::$instance;
		}

		/**
		 * Public constructor for class
		 *
		 * @since 3.2.0
		 */
		protected function __construct() {
			add_action( 'load-post.php', array( $this, 'on_load' ) );
			add_action( 'load-post-new.php', array( $this, 'on_load' ) );
			add_filter( 'the_content', array( $this, 'the_content_filter' ), 99 );
			add_action( 'load-edit.php', array( $this, 'on_load_edit' ) );
		}

		/**
		 * Call via the WordPress load sequence for admin pages.
		 *
		 * @since 3.2.0
		 */
		public function on_load_edit() {
			global $typenow, $post;
			global $ebox_assets_loaded;

			if ( in_array( $typenow, $this->get_global_included_post_types(), true ) ) {

				if ( ebox_use_select2_lib() ) {
					if ( ! isset( $ebox_assets_loaded['styles']['ebox-select2-jquery-style'] ) ) {
						wp_enqueue_style(
							'ebox-select2-jquery-style',
							ebox_LMS_PLUGIN_URL . 'assets/vendor-libs/select2-jquery/css/select2.min.css',
							array(),
							ebox_SCRIPT_VERSION_TOKEN
						);
						$ebox_assets_loaded['styles']['ebox-select2-jquery-style'] = __FUNCTION__;
					}

					if ( ! isset( $ebox_assets_loaded['scripts']['ebox-select2-jquery-script'] ) ) {
						wp_enqueue_script(
							'ebox-select2-jquery-script',
							ebox_LMS_PLUGIN_URL . 'assets/vendor-libs/select2-jquery/js/select2.full.min.js',
							array( 'jquery' ),
							ebox_SCRIPT_VERSION_TOKEN,
							true
						);
						$ebox_assets_loaded['scripts']['ebox-select2-jquery-script'] = __FUNCTION__;
					}
				}

				if ( ! isset( $ebox_assets_loaded['styles']['ebox-admin-settings-bulk-edit'] ) ) {
					wp_enqueue_style(
						'ebox-admin-settings-page',
						ebox_LMS_PLUGIN_URL . 'assets/css/ebox-admin-settings-bulk-edit' . ebox_min_asset() . '.css',
						array(),
						ebox_SCRIPT_VERSION_TOKEN
					);
					wp_style_add_data( 'ebox-admin-settings-bulk-edit', 'rtl', 'replace' );
					$ebox_assets_loaded['styles']['ebox-admin-settings-bulk-edit'] = __FUNCTION__;
				}

				if ( ! isset( $ebox_assets_loaded['scripts']['ebox-admin-settings-bulk-edit'] ) ) {
					wp_enqueue_script(
						'ebox-admin-settings-bulk-edit',
						ebox_LMS_PLUGIN_URL . 'assets/js/ebox-admin-settings-bulk-edit' . ebox_min_asset() . '.js',
						array( 'jquery' ),
						ebox_SCRIPT_VERSION_TOKEN,
						true
					);
					$ebox_assets_loaded['scripts']['ebox-admin-settings-bulk-edit'] = __FUNCTION__;

				}

				add_filter( 'manage_edit-' . $typenow . '_columns', array( $this, 'add_data_columns' ), 10, 1 );
				add_action( 'manage_' . $typenow . '_posts_custom_column', array( $this, 'posts_custom_column' ), 10, 2 );
				add_action( 'bulk_edit_custom_box', array( $this, 'display_custom_bulk_edit' ), 10, 2 );

				add_action( 'save_post', array( $this, 'save_post_bulk_edit' ), 10, 2 );
			}
		}

		/**
		 * Adds the protection columns in the table listing.
		 *
		 * @global string $typenow
		 *
		 * @since 3.2.1
		 *
		 * @param array $cols An array of columns for admin posts listing.
		 * @return array $cols An array of columns for admin posts listing.
		 */
		public function add_data_columns( $cols = array() ) {
			global $typenow;

			if ( ! isset( $cols['ld_teams_membership'] ) ) {
				$cols['ld_teams_membership'] = sprintf(
					// translators: placeholder Team.
					esc_html_x( '%s Content Protection', 'placeholder Team', 'ebox' ),
					ebox_Custom_Label::get_label( 'team' )
				);
			}

			return $cols;
		}

		/**
		 * Show the protection columns in the table listing.
		 *
		 * @since 3.2.0
		 *
		 * @param string  $column_name Column name key.
		 * @param integer $post_id     Post ID of row.
		 */
		public function posts_custom_column( $column_name = '', $post_id = 0 ) {
			$column_name = esc_attr( $column_name );
			$post_id     = absint( $post_id );

			if ( ( 'ld_teams_membership' === $column_name ) && ( ! empty( $post_id ) ) ) {
				$settings = ebox_get_post_team_membership_settings( $post_id );
				if ( ( isset( $settings['teams_membership_enabled'] ) ) && ( 'on' === $settings['teams_membership_enabled'] ) ) {
					echo sprintf(
						// translators: placeholder: Teams Compare, Teams Listing link.
						esc_html_x( '%1$s of %2$s', 'placeholder: Teams Compare Type, Teams Listing link', 'ebox' ),
						esc_html( $settings['teams_membership_compare'] ),
						'<a href="' . esc_url(
							add_query_arg(
								array(
									'post_type' => ebox_get_post_type_slug( 'team' ),
									'ld-team-membership-post-id' => $post_id,
								),
								admin_url( 'edit.php' )
							)
						) . '">' . sprintf(
							// translators: placeholder: Count of Teams, Teams.
							esc_html_x( '%1$s %2$s', 'placeholder: Count of Teams, Teams', 'ebox' ),
							count( $settings['teams_membership_teams'] ),
							( 1 === count( $settings['teams_membership_teams'] ) ? ebox_Custom_Label::get_label( 'team' ) : ebox_Custom_Label::get_label( 'teams' ) ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
						) . '</a>'
					);
				}
			}
		}

		/**
		 * Display bulk edit on table listing
		 *
		 * @since 3.2.3
		 *
		 * @param string $column_name Column name key.
		 * @param string $post_type   Post Type slug.
		 */
		public function display_custom_bulk_edit( $column_name = '', $post_type = '' ) {
			global $ebox_lms;

			static $print_nonce = true;

			if ( ( 'ld_teams_membership' === $column_name ) && ( in_array( $post_type, $this->get_global_included_post_types(), true ) ) ) {
				if ( $print_nonce ) {
					$print_nonce = false;
					?><input type="hidden" name="ebox_teams_membership[nonce]" value="<?php echo esc_attr( wp_create_nonce( 'ebox_teams_membership_' . $post_type ) ); ?>" />
					<?php
				}

				$select_teams_options = $ebox_lms->select_a_team();
				if ( ! empty( $select_teams_options ) ) {
					if ( ebox_use_select2_lib() ) {
						$select_teams_options_default = sprintf(
							// translators: placeholder: Team.
							esc_html_x( 'Search or select a %s…', 'placeholder: Team', 'ebox' ),
							ebox_get_custom_label( 'team' )
						);
					} else {
						$select_teams_options_default = array(
							'' => sprintf(
								// translators: placeholder: Team.
								esc_html_x( 'Select %s', 'placeholder: Team', 'ebox' ),
								ebox_get_custom_label( 'team' )
							),
						);
						if ( ( is_array( $select_teams_options ) ) && ( ! empty( $select_teams_options ) ) ) {
							$select_teams_options = $select_teams_options_default + $select_teams_options;
						} else {
							$select_teams_options = $select_teams_options_default;
						}
						$select_teams_options_default = '';
					}

					?>
					<div class="ebox-inline-edit">
						<fieldset class="inline-edit-col-left inline-edit-col-<?php echo esc_attr( $column_name ); ?> inline-edit-col-<?php echo esc_attr( $column_name ); ?>-settings">
							<legend class="inline-edit-legend">
							<?php
								echo sprintf(
									// translators: placeholder: Team.
									esc_html_x( 'ebox %s Content Protection', 'placeholder: Team', 'ebox' ),
									ebox_Custom_Label::get_label( 'team' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
								);
							?>
								</legend>

							<div class="inline-edit-col ld-inline-edit-col ld-inline-edit-col-left ld-inline-edit-col-settings">
								<label class="ld-team-membership-inline-edit-action">
									<span class="title"><?php echo esc_html__( 'Action', 'ebox' ); ?></span>
									<select name="ebox_teams_membership[action]">
										<option value=""><?php echo esc_html__( '&mdash; No Change &mdash;', 'ebox' ); ?></option>
										<option value="replace"><?php echo esc_html__( 'Replace setting', 'ebox' ); ?></option>
										<option value="add"><?php echo esc_html__( 'Add settings', 'ebox' ); ?></option>
										<option value="remove"><?php echo esc_html__( 'Remove settings', 'ebox' ); ?></option>
									</select>
								</label>
								<label class="ld-team-membership-inline-edit-compare">
									<span class="title"><?php echo esc_html__( 'Compare', 'ebox' ); ?></span>
									<select name="ebox_teams_membership[compare]">
										<option value=""><?php echo esc_html__( '&mdash; No Change &mdash;', 'ebox' ); ?></option>
										<option value="any">
										<?php
										echo sprintf(
											// translators: placeholder: Team.
											esc_html_x( 'Any %s', 'placeholder: Team', 'ebox' ),
											esc_attr( ebox_get_custom_label( 'team' ) )
										);
										?>
										</option>
										<option value="all">
										<?php
										echo sprintf(
											// translators: placeholder: Teams.
											esc_html_x( 'All %s', 'placeholder: Teams', 'ebox' ),
											esc_attr( ebox_get_custom_label( 'teams' ) )
										);
										?>
										</option>
									</select>
								</label>

								<?php
								if ( is_post_type_hierarchical( $post_type ) ) {
									$post_type_object = get_post_type_object( $post_type );
									if ( ( $post_type_object ) && ( is_a( $post_type_object, 'WP_Post_Type' ) ) ) {
										$plural_label = $post_type_object->labels->name;
									} else {
										$plural_label = 'Post';
									}
									?>
										<label class="ld-team-membership-inline-edit-children">
											<span class="title">
											<?php
											echo sprintf(
											// translators: placeholder: Post type plural label.
												esc_html_x( 'Sub-%s', 'placeholder: Post type plural label', 'ebox' ),
												esc_attr( $plural_label )
											);
											?>
												</span>
											<select name="ebox_teams_membership[children]">
												<option value=""><?php echo esc_html__( '&mdash; No Change &mdash;', 'ebox' ); ?></option>
												<option value="yes">
												<?php
												echo sprintf(
												// translators: placeholder: Post type plural label.
													esc_html_x( 'Apply to sub-%s', 'placeholder: Post type plural label', 'ebox' ),
													esc_attr( $plural_label )
												);
												?>
												</option>
												<option value="no">
												<?php
												echo sprintf(
												// translators: placeholder: Post type plural label.
													esc_html_x( 'Do not apply to sub-%s', 'placeholder: Post type plural label', 'ebox' ),
													esc_attr( $plural_label )
												);
												?>
												</option>
											</select>
										</label>
										<?php
								}
								?>
							</div>
						</fieldset>
						<fieldset class="inline-edit-col-right inline-edit-col-<?php echo esc_attr( $column_name ); ?> inline-edit-col-<?php echo esc_attr( $column_name ); ?>-teams">
							<div class="inline-edit-col ld-inline-edit-col ld-inline-edit-col-right ld-inline-edit-col-teams
							<?php
							if ( is_post_type_hierarchical( $post_type ) ) {
								echo ' ld-inline-edit-col-teams-hierarchical'; }
							?>
							">
								<label class="ld-team-membership-inline-edit-teams">
									<span class="title">
									<?php
										echo ebox_get_custom_label( 'teams' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
									?>
									</span>
									<select multiple="" autocomplete="off" name="ebox_teams_membership[teams][]" id="ebox_teams_membership_teams" class="ebox-section-field ebox-section-field-multiselect select2-hidden-accessible"
									placeholder="
									<?php
									echo sprintf(
										// translators: placeholder: Team.
										esc_html_x( 'Search or select a %s…', 'placeholder: Team', 'ebox' ),
										esc_attr( ebox_get_custom_label( 'team' ) )
									);
									?>
									" data-ld-select2="1" data-select2-id="ebox_teams_membership_teams">
									<?php
									foreach ( $select_teams_options as $team_id => $team_title ) {
										?>
											<option value="<?php echo absint( $team_id ); ?>"><?php echo wp_kses_post( apply_filters( 'the_title', $team_title, $team_id ) ); ?></option> <?php // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WP Core Hook ?>
											<?php
									}
									?>
									</select>
								</label>
							</div>
						</fieldset>
					</div>
					<?php
				}
			}
		}

		/**
		 * Save bulk edit changes.
		 *
		 * @since 3.2.3
		 *
		 * @param integer $post_id Post ID.
		 * @param object  $post    WP_Post object.
		 */
		public function save_post_bulk_edit( $post_id, $post ) {
			global $typenow;

			if ( ! in_array( $typenow, $this->get_global_included_post_types(), true ) ) {
				return false;
			}

			if ( ( ! isset( $_GET['ebox_teams_membership']['nonce'] ) ) || ( empty( $_GET['ebox_teams_membership']['nonce'] ) ) || ( ! wp_verify_nonce( esc_attr( $_GET['ebox_teams_membership']['nonce'] ), 'ebox_teams_membership_' . $typenow ) ) ) {
				return false;
			}

			if ( ( ! isset( $_GET['post'] ) ) || ( empty( $_GET['post'] ) ) || ( ! is_array( $_GET['post'] ) ) ) {
				return false;
			}
			$bulk_post_ids = array_map( 'absint', $_GET['post'] );

			if ( ( isset( $_GET['ebox_teams_membership']['teams'] ) ) && ( ! empty( $_GET['ebox_teams_membership']['teams'] ) ) && ( is_array( $_GET['ebox_teams_membership']['teams'] ) ) ) {
				$bulk_team_ids = array_map( 'absint', $_GET['ebox_teams_membership']['teams'] );
			} else {
				$bulk_team_ids = array();
			}

			foreach ( $bulk_post_ids as $bulk_post_id ) {
				$post_team_settings = ebox_get_post_team_membership_settings( $bulk_post_id );

				if ( ( isset( $_GET['ebox_teams_membership']['compare'] ) ) && ( ! empty( $_GET['ebox_teams_membership']['compare'] ) ) ) {
					if ( 'all' === strtolower( $_GET['ebox_teams_membership']['compare'] ) ) {
						$post_team_settings['teams_membership_compare'] = 'ALL';
					} elseif ( 'any' === strtolower( $_GET['ebox_teams_membership']['compare'] ) ) {
						$post_team_settings['teams_membership_compare'] = 'ANY';
					}
				}

				if ( ( is_post_type_hierarchical( $typenow ) ) && ( isset( $_GET['ebox_teams_membership']['children'] ) ) && ( ! empty( $_GET['ebox_teams_membership']['children'] ) ) ) {
					if ( 'yes' === strtolower( $_GET['ebox_teams_membership']['children'] ) ) {
						$post_team_settings['teams_membership_children'] = 'on';
					} elseif ( 'no' === strtolower( $_GET['ebox_teams_membership']['children'] ) ) {
						$post_team_settings['teams_membership_children'] = '';
					}
				}

				if ( ( isset( $_GET['ebox_teams_membership']['action'] ) ) && ( ! empty( $_GET['ebox_teams_membership']['action'] ) ) ) {
					if ( 'replace' === $_GET['ebox_teams_membership']['action'] ) {
						$post_team_settings['teams_membership_teams'] = $bulk_team_ids;
					} elseif ( 'add' === $_GET['ebox_teams_membership']['action'] ) {
						$post_team_settings['teams_membership_teams'] = array_merge( $post_team_settings['teams_membership_teams'], $bulk_team_ids );
						$post_team_settings['teams_membership_teams'] = array_unique( $post_team_settings['teams_membership_teams'] );
					} elseif ( 'remove' === $_GET['ebox_teams_membership']['action'] ) {
						if ( ! empty( $bulk_team_ids ) ) {
							$post_team_settings['teams_membership_teams'] = array_diff( $post_team_settings['teams_membership_teams'], $bulk_team_ids );
						} else {
							$post_team_settings['teams_membership_teams'] = array();
						}
					}
				}

				ebox_set_post_team_membership_settings( $bulk_post_id, $post_team_settings );
			}
		}

		/**
		 * Get Team Membership post metabox instance.
		 *
		 * @since 3.2.0
		 */
		protected function get_metabox_instance() {
			if ( is_null( $this->mb_instance ) ) {
				require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/settings-metaboxes/class-ld-settings-metabox-team-membership-post-settings.php';
				$this->mb_instance = ebox_Settings_Metabox_Team_Membership_Post_Settings::add_metabox_instance();
			}

			return $this->mb_instance;
		}

		/**
		 * Initialize runtime vars.
		 *
		 * @since 3.2.0
		 */
		protected function init_vars() {
			$this->vars['post_id'] = get_the_ID();
			if ( ! empty( $this->vars['post_id'] ) ) {
				$this->vars['post'] = get_post( $this->vars['post_id'] );
			}

			if ( is_user_logged_in() ) {
				$this->vars['user_id'] = get_current_user_id();
				if ( ! empty( $this->vars['user_id'] ) ) {
					$this->vars['user'] = get_user_by( 'ID', $this->vars['user_id'] );
				}
			} else {
				$this->vars['user_id'] = 0;
			}

			if ( ( ! is_admin() ) && ( isset( $_GET['ld_debug'] ) ) ) {
				$this->vars['debug'] = true;
			} else {
				$this->vars['debug'] = false;
			}

			$this->vars['debug_messages'] = array();
		}

		/**
		 * Add debug message to array.
		 *
		 * @since 3.2.0
		 *
		 * @param string $message Message text to add.
		 */
		protected function add_debug_message( $message = '' ) {
			if ( ( isset( $this->vars['debug'] ) ) && ( true === $this->vars['debug'] ) ) {
				$this->vars['debug_messages'][] = $message;
			}
		}

		/**
		 * Output debug message.
		 *
		 * @since 3.2.0
		 */
		protected function output_debug_messages() {
			if ( ( isset( $this->vars['debug'] ) ) && ( true === $this->vars['debug'] ) && ( ! empty( $this->vars['debug_messages'] ) ) ) {
				echo '<code>';
				echo implode( '<br />', array_map( 'wp_kses_post', $this->vars['debug_messages'] ) );
				echo '<br /></code><br />';
			}
		}

		/**
		 * Load the Teams Membership Global settings
		 *
		 * @since 3.2.0
		 */
		protected function init_global_settings() {
			if ( is_null( $this->global_setting ) ) {
				$this->global_setting = ebox_Settings_Section::get_section_settings_all( 'ebox_Settings_Teams_Membership' );
			}

			if ( ! isset( $this->global_setting['teams_membership_enabled'] ) ) {
				$this->global_setting['teams_membership_enabled'] = '';
			}

			if ( ! isset( $this->global_setting['teams_membership_message'] ) ) {
				$this->global_setting['teams_membership_message'] = '';
			}

			if ( ! isset( $this->global_setting['teams_membership_post_types'] ) ) {
				$this->global_setting['teams_membership_post_types'] = array();
			}

			if ( ! isset( $this->global_setting['teams_membership_user_roles'] ) ) {
				$this->global_setting['teams_membership_user_roles'] = array();
			}
		}

		/**
		 * Get the managed membership post types.
		 *
		 * @since 3.2.0
		 */
		protected function get_global_included_post_types() {
			$included_post_types = array();

			$this->init_global_settings();

			if ( ! empty( $this->global_setting['teams_membership_enabled'] ) ) {
				if ( ( is_array( $this->global_setting['teams_membership_post_types'] ) ) && ( ! empty( $this->global_setting['teams_membership_post_types'] ) ) ) {
					$included_post_types = $this->global_setting['teams_membership_post_types'];
				}
			}

			return $included_post_types;
		}

		/**
		 * Get Team Membership excluded user roles.
		 *
		 * @since 3.2.0
		 */
		protected function get_excluded_user_roles() {
			$excluded_user_roles = array();

			$this->init_global_settings();

			if ( ! empty( $this->global_setting['teams_membership_enabled'] ) ) {
				if ( ( is_array( $this->global_setting['teams_membership_user_roles'] ) ) && ( ! empty( $this->global_setting['teams_membership_user_roles'] ) ) ) {
					$excluded_user_roles = $this->global_setting['teams_membership_user_roles'];
				}
			}

			return $excluded_user_roles;
		}

		/**
		 * Get Team Membership access denied message.
		 *
		 * @since 3.2.0
		 */
		protected function get_access_denied_message() {
			static $inline_css_loaded = false;

			$access_denied_message = '';

			$this->init_global_settings();

			if ( ! empty( $this->global_setting['teams_membership_enabled'] ) ) {
				$access_denied_message = $this->global_setting['teams_membership_message'];

				if ( ( ebox_is_active_theme( 'ld30' ) ) && ( function_exists( 'ebox_get_template_part' ) ) ) {

					/**
					 * Filter to show alert message box used in LD30 templates.
					 *
					 * @since 3.2.0
					 *
					 * @param boolean $show_alert true.
					 * @param int     $post_id    Current Post ID.
					 * @param int     $user_id    Current User ID.
					 * @return boolean True to process template. Anything else to abort.
					 */
					if ( true === apply_filters( 'ebox_team_membership_access_denied_show_ld30_alert', true, $this->vars['post_id'], $this->vars['user_id'] ) ) {
						if ( false === $inline_css_loaded ) {
							$inline_css_loaded      = true;
							$css_front_file_content = '.ebox-wrapper .ld-alert a.ld-button.ebox-team-membership-link { text-decoration: none !important; }';
							wp_add_inline_style( 'ebox-front-team-membership', $css_front_file_content );
						}

						$alert = array(
							'icon'    => 'alert',
							'message' => $access_denied_message,
							'type'    => 'warning',
						);

						if ( ( 1 === count( $this->post_setting['teams_membership_teams'] ) ) && ( 'yes' === ebox_Settings_Section::get_section_setting( 'ebox_Settings_Teams_CPT', 'public' ) ) ) {

							$alert['button'] = array(
								'url'   => get_permalink( $this->post_setting['teams_membership_teams'][0] ),
								'class' => 'ebox-link-previous-incomplete ebox-team-membership-link',
								'label' => sprintf(
									// translators: placeholder: Team.
									esc_html_x( 'View %s', 'placeholder: Team', 'ebox' ),
									ebox_get_custom_label( 'team' )
								),
							);
						}

						$access_denied_message = ebox_get_template_part( 'modules/alert.php', $alert, false );
						$access_denied_message = '<div class="ebox-wrapper">' . $access_denied_message . '</div>';
					}
				}
			}

			return $access_denied_message;
		}

		/**
		 * Get Team Membership Post metabox setting
		 *
		 * @since 3.2.0
		 *
		 * @param integer $post_id Post ID to get settings for.
		 *
		 * @return array of settings.
		 */
		protected function init_post_settings( $post_id = 0 ) {
			$this->post_setting = ebox_get_post_team_membership_settings( $post_id );
			return $this->post_setting;
		}

		/**
		 * Get the managed membership post teams.
		 *
		 * @since 3.2.0
		 *
		 * @param integer $post_id Post ID to get settings for.
		 *
		 * @return array of post teams.
		 */
		protected function get_post_included_teams( $post_id = 0 ) {
			$included_post_teams = array();

			if ( ! empty( $post_id ) ) {
				$this->init_post_settings( $post_id );

				if ( ! empty( $this->post_setting['teams_membership_enabled'] ) ) {
					if ( ( is_array( $this->post_setting['teams_membership_teams'] ) ) && ( ! empty( $this->post_setting['teams_membership_teams'] ) ) ) {
						$included_post_teams = $this->post_setting['teams_membership_teams'];
					}
				}
			}

			$this->add_debug_message( __FUNCTION__ . ': post_id [' . $post_id . '] post included teams [' . implode( ', ', $included_post_teams ) . ']' );

			return $included_post_teams;
		}

		/**
		 * Get the managed membership post teams compare.
		 *
		 * @since 3.2.0
		 *
		 * @param integer $post_id Post ID to get settings for.
		 *
		 * @return array of post teams.
		 */
		protected function get_post_teams_compare( $post_id = 0 ) {
			$post_teams_compare = '';

			if ( ! empty( $post_id ) ) {
				$this->init_post_settings( $post_id );

				if ( ! empty( $this->post_setting['teams_membership_enabled'] ) ) {
					$post_teams_compare = $this->post_setting['teams_membership_compare'];
				}
			}
			$this->add_debug_message( __FUNCTION__ . ': post_id [' . $post_id . '] post teams compare[' . $post_teams_compare . ']' );

			return $post_teams_compare;
		}

		/**
		 * Check if post type is managed by membership logic.
		 *
		 * @since 3.2.0
		 *
		 * @param string $post_type Post type slug to check.
		 */
		protected function is_included_post_type( $post_type = '' ) {
			if ( ( ! empty( $post_type ) ) && ( in_array( $post_type, $this->get_global_included_post_types(), true ) ) ) {
				$this->add_debug_message( __FUNCTION__ . ': post_type [' . $post_type . '] is included.' );
				return true;
			}
			$this->add_debug_message( __FUNCTION__ . ': post_type [' . $post_type . '] NOT included.' );
		}

		/**
		 * Check if user_role is excluded by membership logic.
		 *
		 * @since 3.2.0
		 *
		 * @param integer $user_id User ID.
		 */
		protected function is_excluded_user_role( $user_id = 0 ) {
			$this->add_debug_message( __FUNCTION__ . ': user_id [' . $user_id . '] ' );
			if ( ! empty( $user_id ) ) {
				$user = get_user_by( 'ID', $user_id );
				if ( ( is_object( $user ) ) && ( property_exists( $user, 'roles' ) ) && ( ! empty( $user->roles ) ) ) {
					$user_roles          = array_map( 'esc_attr', $user->roles );
					$excluded_user_roles = $this->get_excluded_user_roles();
					$excluded_user_roles = array_map( 'esc_attr', $excluded_user_roles );
					if ( ! empty( $excluded_user_roles ) ) {
						$this->add_debug_message( __FUNCTION__ . ': user_roles [' . implode( ', ', $user_roles ) . '] excluded_roles [' . implode( ', ', $excluded_user_roles ) . ']' );
						if ( array_intersect( $user_roles, $excluded_user_roles ) ) {
							$this->add_debug_message( __FUNCTION__ . ': user role excluded.' );
							return true;
						}
						$this->add_debug_message( __FUNCTION__ . ': user role NOT excluded.' );
					}
				}
			}
		}

		/**
		 * Check if Post is enabled and if the post type is included in the global settings.
		 *
		 * @since 3.2.0
		 *
		 * @param integer $post_id Post ID.
		 *
		 * @return boolean
		 */
		protected function is_post_blocked( $post_id = 0 ) {
			$this->add_debug_message( __FUNCTION__ . ': post_id [' . $post_id . ']' );

			if ( is_preview() || is_admin() ) {
				$this->add_debug_message( __FUNCTION__ . ': is_preview or is_admin true. aborting.' );
				return false;
			}

			if ( ! empty( $post_id ) ) {
				$this->init_post_settings( $post_id );

				if ( $this->is_included_post_type( get_post_type( $post_id ) ) ) {
					if ( ( ! empty( $this->post_setting['teams_membership_enabled'] ) ) && ( ! empty( $this->post_setting['teams_membership_teams'] ) ) ) {
						$this->add_debug_message( __FUNCTION__ . ': post type [' . get_post_type( $post_id ) . '] is under membership control.' );
						return true;
					}
				}
				$this->add_debug_message( __FUNCTION__ . ': post type [' . get_post_type( $post_id ) . '] not under membership control. bypassed' );
			}

			return false;
		}

		/**
		 * Check if User enrolled teams against Post and Membership settings.
		 *
		 * @since 3.2.0
		 *
		 * @param integer $post_id Post ID.
		 * @param integer $user_id USer ID.
		 *
		 * @return boolean
		 */
		protected function is_user_blocked( $post_id = 0, $user_id = 0 ) {
			if ( ! empty( $post_id ) ) {
				$this->init_post_settings( $post_id );

				if ( ! empty( $user_id ) ) {
					if ( $this->is_excluded_user_role( $user_id ) ) {
						$this->add_debug_message( __FUNCTION__ . ': user role excluded. bypassed.' );
						return false;
					} else {
						$this->add_debug_message( __FUNCTION__ . ': user role not excluded. blocked.' );
					}

					if ( $this->is_user_in_post_teams( $post_id, $user_id ) ) {
						$this->add_debug_message( __FUNCTION__ . ': user in post teams. bypassed.' );
						return false;
					} else {
						$this->add_debug_message( __FUNCTION__ . ': user not in post teams. blocked.' );
					}
				} else {
					$post_teams = $this->get_post_included_teams( $post_id );
					if ( empty( $post_teams ) ) {
						$this->add_debug_message( __FUNCTION__ . ': empty post teams. bypassed.' );
						return false;
					} else {
						$this->add_debug_message( __FUNCTION__ . ': empty user. post teams exists. blocked.' );
					}
				}
				return true;
			}
			return true;
		}

		/**
		 * Check if user if in the associated post membership teams.
		 *
		 * @since 3.2.0
		 *
		 * @param integer $post_id Post ID.
		 * @param integer $user_id User ID.
		 */
		protected function is_user_in_post_teams( $post_id = 0, $user_id = 0 ) {
			if ( ( ! empty( $user_id ) ) && ( ! empty( $post_id ) ) ) {
				$this->init_post_settings( $post_id );

				$post_teams = $this->get_post_included_teams( $post_id );
				$post_teams = array_map( 'absint', $post_teams );
				if ( ! empty( $post_teams ) ) {
					$user_teams = ebox_get_users_team_ids( $user_id );
					$user_teams = array_map( 'absint', $user_teams );
					if ( ! empty( $user_teams ) ) {
						$teams_compare = $this->get_post_teams_compare( $post_id );

						$common_teams = array_intersect( $user_teams, $post_teams );
						if ( 'ANY' === $teams_compare ) {
							if ( ! empty( $common_teams ) ) {
								$this->add_debug_message( __FUNCTION__ . ': user is in ANY teams.' );
								return true;
							}
							$this->add_debug_message( __FUNCTION__ . ': user not in ANY teams.' );
						} elseif ( 'ALL' === $teams_compare ) {
							if ( empty( array_diff( $common_teams, $post_teams ) ) && empty( array_diff( $post_teams, $common_teams ) ) ) {
								$this->add_debug_message( __FUNCTION__ . ': user is in ALL teams.' );
								return true;
							}
							$this->add_debug_message( __FUNCTION__ . ': user not in ALL teams.' );
						}
					} else {
						$this->add_debug_message( __FUNCTION__ . ': user teams empty.' );
					}
				}
			}
		}

		/**
		 * Called when the Post is Added or Edited.
		 *
		 * @since 3.2.0
		 */
		public function on_load() {
			global $typenow;

			if ( $this->is_included_post_type( $typenow ) ) {
				add_action( 'save_post', array( $this, 'save_post' ), 10, 3 );
				$this->get_metabox_instance();
			}
		}

		/**
		 * Called when the Post is Saved.
		 *
		 * @since 3.2.0
		 *
		 * @param integer $post_id Post ID.
		 * @param object  $post    WP_Post instance.
		 * @param boolean $update  If update to post.
		 */
		public function save_post( $post_id = 0, $post = null, $update = null ) {
			if ( $this->is_included_post_type( $post->post_type ) ) {
				$mb_instance = $this->get_metabox_instance();
				$mb_instance->save_post_meta_box( $post_id, $post, $update );
			}

			return true;
		}

		/**
		 * Start the logic to filter the content.
		 *
		 * @since 3.2.0
		 *
		 * @param string/HTML $content The Post content.
		 */
		public function the_content_filter( $content ) {
			if ( is_preview() || is_admin() ) {
				return $content;
			}

			$this->init_vars();

			if ( ( ! isset( $this->vars['post'] ) ) || ( ! is_a( $this->vars['post'], 'WP_Post' ) ) ) {
				return $content;
			}

			$post_blocked = $this->is_post_blocked( $this->vars['post_id'] );
			$user_blocked = $this->is_user_blocked( $this->vars['post_id'], $this->vars['user_id'] );

			$this->add_debug_message( __FUNCTION__ . ': post_blocked[' . $post_blocked . '] user_blocked[' . $user_blocked . ']' );

			if ( ( true === $post_blocked ) && ( true === $user_blocked ) ) {
				$this->add_debug_message( __FUNCTION__ . ': blocked.' );
				$this->output_debug_messages();

				return $this->get_access_denied_message();
			} else {
				$this->add_debug_message( __FUNCTION__ . ': not blocked.' );
				$this->output_debug_messages();

				return $content;
			}
		}

		// End of functions.
	}
	add_action(
		'init',
		function() {
			LD_Teams_Membership::get_instance();
		},
		10,
		1
	);
}

/**
 * Utility function to get the post team membership settings.
 *
 * @since 3.2.0
 *
 * @param integer $post_id Post ID.
 * @return array Array of settings.
 */
function ebox_get_post_team_membership_settings( $post_id = 0 ) {
	$ebox_settings = array();

	if ( ! empty( $post_id ) ) {
		$is_hierarchical = is_post_type_hierarchical( get_post_type( $post_id ) );

		$ebox_settings['teams_membership_enabled'] = get_post_meta( $post_id, '_ld_teams_membership_enabled', true );
		$ebox_settings['teams_membership_compare'] = get_post_meta( $post_id, '_ld_teams_membership_compare', true );
		$ebox_settings['teams_membership_teams']  = ebox_get_post_team_membership_teams( $post_id );

		if ( ( ! isset( $ebox_settings['teams_membership_enabled'] ) ) || ( 'on' !== $ebox_settings['teams_membership_enabled'] ) ) {
			$ebox_settings['teams_membership_enabled'] = '';
		}

		if ( ( ! isset( $ebox_settings['teams_membership_compare'] ) ) || ( empty( $ebox_settings['teams_membership_compare'] ) ) ) {
			$ebox_settings['teams_membership_compare'] = 'ANY';
		}

		if ( ! isset( $ebox_settings['teams_membership_teams'] ) ) {
			$ebox_settings['teams_membership_teams'] = array();
		}

		if ( ( 'on' === $ebox_settings['teams_membership_enabled'] ) && ( true === $is_hierarchical ) ) {
			$ebox_settings['teams_membership_children'] = get_post_meta( $post_id, '_ld_teams_membership_children', true );
			if ( ( ! isset( $ebox_settings['teams_membership_children'] ) ) || ( 'on' !== $ebox_settings['teams_membership_children'] ) ) {
				$ebox_settings['teams_membership_children'] = '';
			}
		} else {
			$ebox_settings['teams_membership_children'] = '';
		}

		if ( ( ! empty( $ebox_settings['teams_membership_teams'] ) ) && ( 'on' === $ebox_settings['teams_membership_enabled'] ) ) {
			$ebox_settings['teams_membership_teams'] = ebox_validate_teams( $ebox_settings['teams_membership_teams'] );
			if ( empty( $ebox_settings['teams_membership_teams'] ) ) {
				$ebox_settings['teams_membership_enabled']  = '';
				$ebox_settings['teams_membership_children'] = '';
			}
		} else {
			$ebox_settings['teams_membership_enabled']  = '';
			$ebox_settings['teams_membership_teams']   = array();
			$ebox_settings['teams_membership_children'] = '';
		}

		if ( ( empty( $ebox_settings['teams_membership_enabled'] ) ) && ( true === $is_hierarchical ) ) {
			$parents_post_id = wp_get_post_parent_id( $post_id );
			if ( ! empty( $parents_post_id ) ) {
				$parent_settings = ebox_get_post_team_membership_settings( $parents_post_id );
				if ( ( isset( $parent_settings['teams_membership_enabled'] ) ) && ( 'on' === $parent_settings['teams_membership_enabled'] ) ) {
					if ( ( isset( $parent_settings['teams_membership_children'] ) ) && ( 'on' === $parent_settings['teams_membership_children'] ) ) {
						$parent_settings['teams_membership_parent'] = absint( $parents_post_id );
						$ebox_settings                          = $parent_settings;
					}
				}
			}
		}
	}

	return $ebox_settings;
}

/**
 * Utility function to set the post team membership settings.
 *
 * @since 3.2.0
 *
 * @param integer $post_id  Post ID.
 * @param array   $settings Array of settings.
 */
function ebox_set_post_team_membership_settings( $post_id = 0, $settings = array() ) {
	if ( ! empty( $post_id ) ) {

		$default_settings = array(
			'teams_membership_enabled'  => '',
			'teams_membership_children' => '',
			'teams_membership_compare'  => 'ANY',
			'teams_membership_teams'   => array(),
		);

		$settings = wp_parse_args( $settings, $default_settings );

		if ( empty( $settings['teams_membership_compare'] ) ) {
			$settings['teams_membership_compare'] = 'ANY';
		}
		if ( ! is_array( $settings['teams_membership_teams'] ) ) {
			$settings['teams_membership_teams'] = array();
		} elseif ( ! empty( $settings['teams_membership_teams'] ) ) {
			$settings['teams_membership_teams'] = array_map( 'absint', $settings['teams_membership_teams'] );
		}

		if ( ! empty( $settings['teams_membership_teams'] ) ) {
			$settings['teams_membership_enabled'] = 'on';
		} else {
			$settings['teams_membership_enabled']  = '';
			$settings['teams_membership_children'] = '';
			$settings['teams_membership_compare']  = '';
		}

		foreach ( $settings as $_key => $_val ) {
			if ( 'teams_membership_teams' === $_key ) {
				ebox_set_post_team_membership_teams( $post_id, $_val );
			} else {
				if ( empty( $_val ) ) {
					delete_post_meta( $post_id, '_ld_' . $_key );
				} else {
					update_post_meta( $post_id, '_ld_' . $_key, $_val );
				}
			}
		}
	}
}

/**
 * Get the Teams related to the Post for Team Membership.
 *
 * @since 3.2.0
 *
 * @param integer $post_id Post ID.
 * @return array Array of settings.
 */
function ebox_get_post_team_membership_teams( $post_id = 0 ) {
	$team_ids = array();

	$post_id = absint( $post_id );
	if ( ! empty( $post_id ) ) {
		$post_meta = get_post_meta( $post_id );
		if ( ! empty( $post_meta ) ) {
			foreach ( $post_meta as $meta_key => $meta_set ) {
				if ( '_ld_teams_membership_team_' == substr( $meta_key, 0, strlen( '_ld_teams_membership_team_' ) ) ) {
					$team_id = str_replace( '_ld_teams_membership_team_', '', $meta_key );
					$team_id = absint( $team_id );
					if ( ebox_get_post_type_slug( 'team' ) === get_post_type( $team_id ) ) {
						$team_ids[] = $team_id;
					}
				}
			}
		}
	}

	return $team_ids;
}

/**
 * Set the Teams related to the Post for Team Membership.
 *
 * @since 3.2.0
 *
 * @param int   $post_id    Post ID to update.
 * @param array $teams_new Array of team IDs to set for the Post ID. Can be empty.
 */
function ebox_set_post_team_membership_teams( $post_id = 0, $teams_new = array() ) {
	$post_id = absint( $post_id );
	if ( ! is_array( $teams_new ) ) {
		$teams_new = array();
	} elseif ( ! empty( $teams_new ) ) {
		$teams_new = array_map( 'absint', $teams_new );
	}

	if ( ! empty( $post_id ) ) {

		$teams_old = ebox_get_post_team_membership_teams( $post_id );
		if ( ! is_array( $teams_old ) ) {
			$teams_old = array();
		} elseif ( ! empty( $teams_old ) ) {
			$teams_old = array_map( 'absint', $teams_old );
		}

		$teams_intersect = array_intersect( $teams_new, $teams_old );

		$teams_add = array_diff( $teams_new, $teams_intersect );
		if ( ! empty( $teams_add ) ) {
			foreach ( $teams_add as $team_id ) {
				add_post_meta( $post_id, '_ld_teams_membership_team_' . $team_id, time() );
			}
		}

		$teams_remove = array_diff( $teams_old, $teams_intersect );
		if ( ! empty( $teams_remove ) ) {
			foreach ( $teams_remove as $team_id ) {
				delete_post_meta( $post_id, '_ld_teams_membership_team_' . $team_id );
			}
		}
	}
}

