<?php
/**
 * Team Leader Teams Listing.
 *
 * @since 2.1.2
 * @package ebox\Team_Users
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ebox_Admin_Teams_Users_List' ) ) {

	/**
	 * Class Team Leader Teams Listing.
	 *
	 * @since 2.1.2
	 */
	class ebox_Admin_Teams_Users_List {

		/**
		 * List table object
		 *
		 * @var object $list_table Post List table instance
		 */
		public $list_table;

		/**
		 * Form method
		 *
		 * @var string $form_method Form Method
		 */
		public $form_method = 'get';

		/**
		 * Title
		 *
		 * @var string $title Title
		 */
		public $title = '';

		/**
		 * Current table action
		 *
		 * @var string $current_action Current table action
		 */
		public $current_action = '';

		/**
		 * Team ID
		 *
		 * @var integer $team_id Team ID
		 */
		public $team_id = 0;

		/**
		 * User ID
		 *
		 * @var integer $user_id User ID
		 */
		public $user_id = 0;

		/**
		 * Public constructor for class
		 *
		 * @since 2.1.2
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'ebox_team_admin_menu' ) );
		}

		/**
		 * Register Team Administration submenu page
		 *
		 * @since 2.1.2
		 */
		public function ebox_team_admin_menu() {

			$menu_user_cap = '';
			$menu_parent   = '';
			$position      = 0;

			if ( current_user_can( 'edit_teams' ) ) {
				$user_team_ids = ebox_get_administrators_team_ids( get_current_user_id(), true );
				if ( ! empty( $user_team_ids ) ) {
					$menu_user_cap = 'edit_teams';
					$menu_parent   = 'edit.php?post_type=teams';
					$position      = null; // Let the position be natural.
				}
			} elseif ( ebox_is_team_leader_user() ) {
				$user_team_ids = ebox_get_administrators_team_ids( get_current_user_id(), true );
				if ( ! empty( $user_team_ids ) ) {
					$menu_user_cap = ebox_GROUP_LEADER_CAPABILITY_CHECK;
					$menu_parent   = 'ebox-lms';

					if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_Teams_Team_Leader_User', 'manage_courses_enabled' ) === 'yes' ) {
						$position = 6; // Position to the top for Team Leader.
					} else {
						$position = 0; // Position to the top for Team Leader.
					}
				}
			}

			if ( ! empty( $menu_user_cap ) ) {
				global $submenu;

				$page_hook = add_submenu_page(
					$menu_parent,
					ebox_Custom_Label::get_label( 'teams' ),
					ebox_Custom_Label::get_label( 'teams' ),
					$menu_user_cap,
					'team_admin_page',
					array( $this, 'show_page' ),
					$position
				);
				add_action( 'load-' . $page_hook, array( $this, 'on_load' ) );

				if ( ( isset( $submenu['ebox-lms'] ) ) && ( ! empty( $submenu['ebox-lms'] ) ) ) {
					foreach ( $submenu['ebox-lms'] as $menu_idx => &$menu_item ) {
						if ( ( isset( $menu_item['2'] ) ) && ( 'team_admin_page' === $menu_item['2'] ) ) {
							if ( ! isset( $menu_item['4'] ) ) {
								$menu_item['4'] = 'submenu-ldlms-teams';
							}
						}
					}
				}
			}
		}

		/**
		 * On page load
		 *
		 * @since 2.1.2
		 */
		public function on_load() {
			global $ebox_assets_loaded;

			if ( ( isset( $_GET['action'] ) ) && ( ! empty( $_GET['action'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$this->current_action = sanitize_text_field( wp_unslash( $_GET['action'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			}

			if ( ( isset( $_GET['team_id'] ) ) && ( ! empty( $_GET['team_id'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$this->team_id = intval( $_GET['team_id'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			}

			if ( ( isset( $_GET['user_id'] ) ) && ( ! empty( $_GET['user_id'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$this->user_id = intval( $_GET['user_id'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			}

			wp_enqueue_style(
				'ebox-module-style',
				ebox_LMS_PLUGIN_URL . '/assets/css/ebox_module' . ebox_min_asset() . '.css',
				array(),
				ebox_SCRIPT_VERSION_TOKEN
			);
			wp_style_add_data( 'ebox-module-style', 'rtl', 'replace' );
			$ebox_assets_loaded['styles']['ebox-module-style'] = __FUNCTION__;

			wp_enqueue_script(
				'ebox-module-script',
				ebox_LMS_PLUGIN_URL . '/assets/js/ebox_module' . ebox_min_asset() . '.js',
				array( 'jquery' ),
				ebox_SCRIPT_VERSION_TOKEN,
				true
			);
			$ebox_assets_loaded['scripts']['ebox-module-script'] = __FUNCTION__;

			// Because we need the ajaxurl for the pagination AJAX.
			$data = array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
			);

			$data = array( 'json' => wp_json_encode( $data ) );
			wp_localize_script( 'ebox-module-script', 'ebox_data', $data );

			$filepath = ebox_LMS::get_template( 'ebox_pager.css', null, null, true );
			if ( ! empty( $filepath ) ) {
				wp_enqueue_style( 'ebox_pager_css', ebox_template_url_from_path( $filepath ), array(), ebox_SCRIPT_VERSION_TOKEN );
				$ebox_assets_loaded['styles']['ebox_pager_css'] = __FUNCTION__;
			}

			$filepath = ebox_LMS::get_template( 'ebox_pager.js', null, null, true );
			if ( ! empty( $filepath ) ) {
				wp_enqueue_script( 'ebox_pager_js', ebox_template_url_from_path( $filepath ), array( 'jquery' ), ebox_SCRIPT_VERSION_TOKEN, true );
				$ebox_assets_loaded['scripts']['ebox_pager_js'] = __FUNCTION__;
			}

			if ( empty( $this->current_action ) ) {

				require_once ebox_LMS_PLUGIN_DIR . 'includes/admin/class-ebox-admin-teams-users-list-table.php';
				$this->list_table = new ebox_Admin_Teams_Users_List_Table();
				$screen           = get_current_screen();

				$screen_key = $screen->id;
				if ( ! empty( $this->team_id ) ) {
					$screen_key .= '_users';
				} else {
					$screen_key .= '_teams';
				}
				$screen_key .= '_per_page';

				$screen_per_page_option = str_replace( '-', '_', $screen_key );

				if ( isset( $_POST['wp_screen_options']['option'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing

					if ( isset( $_POST['wp_screen_options']['value'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
						$per_page = intval( $_POST['wp_screen_options']['value'] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
						if ( ( ! $per_page ) || ( $per_page < 1 ) ) {
							$per_page = 20;
						}
						update_user_meta( get_current_user_id(), $screen_per_page_option, $per_page );
					}
				}
				$per_page = get_user_meta( get_current_user_id(), $screen_per_page_option, true );
				if ( ( ! $per_page ) || ( $per_page < 1 ) ) {
					$per_page = 20;
				}

				$this->title = '';

				$this->list_table->per_page = $per_page;
				add_screen_option(
					'per_page',
					array(
						'label'   => esc_html__( 'per Page', 'ebox' ),
						'default' => $per_page,
					)
				);

				if ( ( ! empty( $this->team_id ) ) && ( ! empty( $this->user_id ) ) ) {

					$this->on_process_actions_list();

					$this->form_method = 'post';

					$user = get_user_by( 'id', $this->user_id );
					return;
				} elseif ( ! empty( $this->team_id ) ) {
					$team_post = get_post( $this->team_id );
					if ( $team_post ) {
						$this->list_table->team_id = $this->team_id;

						$this->list_table->columns['username']     = esc_html__( 'Username', 'ebox' );
						$this->list_table->columns['name']         = esc_html__( 'Name', 'ebox' );
						$this->list_table->columns['email']        = esc_html__( 'Email', 'ebox' );
						$this->list_table->columns['user_actions'] = esc_html__( 'Actions', 'ebox' );

						return;
					}
				}
			} elseif ( 'ebox-team-email' == $this->current_action ) {

				$team_post = get_post( $this->team_id );
				if ( $team_post ) {
					return;
				}
			}

			$this->list_table->columns['team_name']    = ebox_Custom_Label::get_label( 'teams' );
			$this->list_table->columns['team_actions'] = esc_html__( 'Actions', 'ebox' );
		}

		/**
		 * Show page
		 *
		 * @since 2.3.0
		 */
		public function show_page() {
			?>
			<div class="wrap wrap-ebox-team-list">
				<hr class="wp-header-end">
				<?php if ( ! empty( $this->title ) ) { ?>
				<h2><?php echo wp_kses_post( $this->title ); ?></h2>
				<?php } ?>
				<?php
					$current_user = wp_get_current_user();
				if ( ( ! ebox_is_team_leader_user( $current_user ) ) && ( ! ebox_is_admin_user( $current_user ) ) ) {
					die(
						sprintf(
							// translators: placeholder: Team.
							esc_html_x( 'Please login as a %s Administrator', 'placeholder: Team', 'ebox' ),
							ebox_Custom_Label::get_label( 'team' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
						)
					);
				}
				?>
				<div class="wrap-ebox-view-content">
					<?php
					if ( 'ebox-team-email' == $this->current_action ) {
						?>
						<input id="team_email_ajaxurl" type="hidden" name="team_email_ajaxurl" value="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" />
						<input id="team_email_team_id" type="hidden" name="team_email_team_id" value="<?php echo absint( $this->team_id ); ?>" />
						<input id="team_email_nonce" type="hidden" name="team_email_nonce" value="<?php echo esc_attr( wp_create_nonce( 'team_email_nonce_' . $this->team_id . '_' . $current_user->ID ) ); ?>" />

						<!-- Email Team feature below the Team Table (on the Team Leader page) -->
						<table class="form-table">
							<tr>
								<th scope="row"><label for="team_email_sub"><?php esc_html_e( 'Email Subject:', 'ebox' ); ?></label></th>
								<td><input id="team_email_sub" rows="5" class="regular-text team_email_sub"/></td>
							</tr>
							<tr>
								<th scope="row"><label for="text"><strong><?php esc_html_e( 'Email Message:', 'ebox' ); ?></strong></label></th>
								<td><div class="teamemailtext" >
								<?php
								wp_editor(
									'',
									'teamemailtext',
									array(
										'media_buttons' => true,
										'wpautop'       => true,
									)
								);
								?>
								</div></td>
							</tr>
						</table>

						<p>
							<button id="email_team" class="button button-primary" type="button"><?php esc_html_e( 'Send', 'ebox' ); ?></button>
							<button id="email_reset" class="button button-secondary" type="button"><?php esc_html_e( 'Reset', 'ebox' ); ?></button><br />
							<span class="empty_status" style="color: red; display: none;"><?php esc_html_e( 'Both Email Subject and Message are required and cannot be empty.', 'ebox' ); ?></span>
							<span class="sending_status" style="display: none;"><?php esc_html_e( 'Sending...', 'ebox' ); ?></span>
							<span class="sending_result" style="display: none;"></span>
						</p>
						<?php
					} else {

						$this->list_table->views();
						?>
						<form id="ebox-view-form" action="" method="<?php echo esc_attr( $this->form_method ); ?>">
							<input type="hidden" name="page" value="team_admin_page" />
							<?php
							if ( empty( $this->user_id ) ) {
								?>
									<input type="hidden" name="user_id" value="<?php echo absint( $this->user_id ); ?>" />
									<?php
									$this->list_table->check_table_filters();
									$this->list_table->prepare_items();

									if ( ! empty( $this->team_id ) ) {
										?>
										<input type="hidden" name="team_id" value="<?php echo absint( $this->team_id ); ?>" />
										<?php
										$this->list_table->search_box( esc_html__( 'Search Users', 'ebox' ), 'users' );
									} else {
										$this->list_table->search_box(
											sprintf(
											// translators: placeholder: Teams.
												esc_html_x( 'Search %s', 'placeholder: Teams', 'ebox' ),
												ebox_Custom_Label::get_label( 'teams' )
											),
											'teams'
										);
									}
									wp_nonce_field( 'ld-team-list-view-nonce-' . get_current_user_id(), 'ld-team-list-view-nonce' );
									$this->list_table->display();
							} else {
								$team_user_ids = ebox_get_teams_user_ids( $this->team_id );
								if ( ! empty( $team_user_ids ) ) {
									if ( in_array( $this->user_id, $team_user_ids, true ) ) {
										$atts = array(
											'user_id'      => $this->user_id,
											'team_id'     => $this->team_id,
											'progress_num' => ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_General_Per_Page', 'progress_num' ),
											'progress_orderby' => 'title',
											'progress_order' => 'ASC',
											'quiz_num'     => ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_General_Per_Page', 'quiz_num' ),
											'quiz_orderby' => 'taken',
											'quiz_order'   => 'DESC',
										);

										/**
										 * Filters team administration course info attributes.
										 *
										 * @since 2.5.7
										 *
										 * @param array         $atts An array of team admin course info attributes.
										 * @param WP_User|false $user User Object.
										 */
										$atts = apply_filters( 'ebox_team_administration_course_info_atts', $atts, get_user_by( 'id', $this->user_id ) );

										echo ebox_course_info_shortcode( $atts ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Need to output HTML

										if ( ebox_show_user_course_complete( $this->user_id ) ) {
											echo submit_button( esc_html__( 'Update User', 'ebox' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										}
									}
								}
							}
							?>
						</form>
						<?php
					}
					?>
				</div>
			</div>
			<?php
		}

		/**
		 * Handle actions from table
		 *
		 * @since 2.3.0
		 *
		 * @return void
		 */
		public function on_process_actions_list() {
			if ( ! empty( $this->user_id ) ) {
				ebox_save_user_course_complete( $this->user_id );
			}
		}

		// End of functions.
	}
}

/**
 * Handle Teams Table AJAX for Reports.
 *
 * @since 2.3.0
 *
 * @return void
 */
function ebox_data_team_reports_ajax() {
	$reply_data = array( 'status' => false );

	if ( ( is_user_logged_in() ) && ( ( ebox_is_admin_user() ) || ( ebox_is_team_leader_user() ) ) ) {
		if ( ( isset( $_POST['nonce'] ) ) && ( ! empty( $_POST['nonce'] ) ) && ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'ld-team-list-view-nonce-' . get_current_user_id() ) ) ) {
			if ( ( isset( $_POST['data'] ) ) && ( ! empty( $_POST['data'] ) ) ) {
				$ld_admin_settings_data_reports = new ebox_Admin_Settings_Data_Reports();
				$reply_data['data']             = $ld_admin_settings_data_reports->do_data_reports( $_POST['data'], $reply_data ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

				echo wp_json_encode( $reply_data );
			}
		}
	}

	wp_die(); // this is required to terminate immediately and return a proper response.
}

add_action( 'wp_ajax_ebox_data_team_reports', 'ebox_data_team_reports_ajax' );
