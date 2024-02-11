<?php
/**
 * Functions for wp-admin
 *
 * @since 2.1.0
 * @package ebox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds the ebox post type to the admin body class.
 *
 * Fires on `admin_body_class` hook.
 *
 * @since 2.5.8
 *
 * @param string $class Optional. The admin body CSS classes. Default empty.
 *
 * @return string Admin body CSS classes.
 */
function ebox_admin_body_class( $class = '' ) {
	global $ebox_post_types;

	$screen = get_current_screen();
	if ( in_array( $screen->id, $ebox_post_types, true ) ) {
		$class .= ' ebox-post-type ' . $screen->post_type;
	}

	if ( in_array( $screen->post_type, $ebox_post_types, true ) ) {
		$class .= ' ebox-screen';
	}

	if ( ebox_is_team_leader_user() ) {
		$class .= ' ebox-user-team-leader';
	} else {
		$class .= ' ebox-user-admin';
	}

	return $class;
}
add_filter( 'admin_body_class', 'ebox_admin_body_class' );

/**
 * Hides the top-level menus with no submenus.
 *
 * Fires on `admin_footer` hook.
 *
 * @since 2.1.0
 */
function ebox_hide_menu_when_not_required() {
	?>
		<script>
		jQuery( function() {
		if(jQuery(".toplevel_page_ebox-lms").length && jQuery(".toplevel_page_ebox-lms").find("li").length <= 1)
			jQuery(".toplevel_page_ebox-lms").hide();
		});
		</script>
	<?php
}

add_filter( 'admin_footer', 'ebox_hide_menu_when_not_required', 99 );

/**
 * Checks whether to load the admin assets.
 *
 * @global string  $pagenow
 * @global string  $typenow
 * @global WP_Post $post                 Global post object.
 * @global array   $ebox_post_types An array of ebox post types.
 * @global array   $ebox_pages      An array of ebox pages.
 *
 * @since 3.0.0
 *
 * @return boolean Returns true to load the admin assets otherwise false.
 */
function ebox_should_load_admin_assets() {
	global $pagenow, $post, $typenow;
	global $ebox_post_types, $ebox_pages;

	// Get post type.
	$post_type = get_post_type();
	if ( ! $post_type ) {
		$post_type = isset( $_GET['post_type'] ) ? sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) : $post_type; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	$is_ld_page = false;
	if ( ( isset( $_GET['page'] ) ) && ( in_array( $_GET['page'], $ebox_pages, true ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$is_ld_page = true;
	}

	$is_ld_post_type = false;
	if ( ( ! empty( $post_type ) ) && ( in_array( $post_type, $ebox_post_types, true ) ) ) {
		$is_ld_post_type = true;
	}

	$is_ld_pagenow = false;
	if ( ( in_array( $pagenow, array( 'post.php', 'post-new.php' ), true ) ) && ( is_a( $post, 'WP_Post' ) ) && ( in_array( $post->post_type, $ebox_post_types, true ) ) ) {
		$is_ld_pagenow = true;
	}

	$load_admin_assets = false;
	if ( ( true === $is_ld_page ) || ( true === $is_ld_post_type ) || ( true === $is_ld_pagenow ) ) {
		$load_admin_assets = true;
	}

	/**
	 * Filters whether to load the admin assets or not.
	 *
	 * @param boolean $load_admin_assets Whether to load admin assets.
	 */
	return apply_filters( 'ebox_load_admin_assets', $load_admin_assets );
}

/**
 * Enqueues the scripts and styles for admin.
 *
 * Fires on `admin_enqueue_scripts` hook.
 *
 * @global string  $pagenow
 * @global string  $typenow
 * @global WP_Post $post                    Global post object.
 * @global array   $ebox_assets_loaded An array of loaded styles and scripts.
 *
 * @since 2.1.0
 */
function ebox_load_admin_resources() {
	global $pagenow, $post, $typenow;
	global $ebox_assets_loaded;

	wp_enqueue_style(
		'ebox-admin-menu-style',
		ebox_LMS_PLUGIN_URL . 'assets/css/ebox-admin-menu' . ebox_min_asset() . '.css',
		array(),
		ebox_SCRIPT_VERSION_TOKEN
	);
	wp_style_add_data( 'ebox-admin-menu-style', 'rtl', 'replace' );
	$ebox_assets_loaded['styles']['ebox-admin-menu-style'] = __FUNCTION__;

	wp_enqueue_script(
		'ebox-admin-menu-script',
		ebox_LMS_PLUGIN_URL . 'assets/js/ebox-admin-menu' . ebox_min_asset() . '.js',
		array( 'jquery' ),
		ebox_SCRIPT_VERSION_TOKEN,
		true
	);
	wp_style_add_data( 'ebox-admin-menu-script', 'rtl', 'replace' );
	$ebox_assets_loaded['scripts']['ebox-admin-menu-script'] = __FUNCTION__;

	if ( ebox_should_load_admin_assets() ) {

		/**
		 * Needed for standalone Builders.
		 */
		// to get the tinyMCE editor.
		wp_enqueue_editor();

		// for media uploads.
		wp_enqueue_media();

		wp_enqueue_style(
			'ebox_style',
			ebox_LMS_PLUGIN_URL . 'assets/css/style' . ebox_min_asset() . '.css',
			array(),
			ebox_SCRIPT_VERSION_TOKEN
		);
		wp_style_add_data( 'ebox_style', 'rtl', 'replace' );
		$ebox_assets_loaded['styles']['ebox_style'] = __FUNCTION__;

		wp_enqueue_style(
			'ebox-admin-style',
			ebox_LMS_PLUGIN_URL . 'assets/css/ebox-admin-style' . ebox_min_asset() . '.css',
			array(),
			ebox_SCRIPT_VERSION_TOKEN
		);
		wp_style_add_data( 'ebox-admin-style', 'rtl', 'replace' );
		$ebox_assets_loaded['styles']['ebox-admin-style'] = __FUNCTION__;

		wp_enqueue_style(
			'ebox-module-style',
			ebox_LMS_PLUGIN_URL . 'assets/css/ebox_module' . ebox_min_asset() . '.css',
			array(),
			ebox_SCRIPT_VERSION_TOKEN
		);
		wp_style_add_data( 'ebox-module-style', 'rtl', 'replace' );
		$ebox_assets_loaded['styles']['ebox-module-style'] = __FUNCTION__;

		if ( ( 'edit.php' === $pagenow ) && ( in_array( $typenow, array( 'ebox-essays', 'ebox-assignment', 'ebox-topic', 'ebox-quiz' ), true ) ) ) {
			wp_enqueue_script(
				'ebox-module-script',
				ebox_LMS_PLUGIN_URL . 'assets/js/ebox_module' . ebox_min_asset() . '.js',
				array( 'jquery' ),
				ebox_SCRIPT_VERSION_TOKEN,
				true
			);
			$ebox_assets_loaded['scripts']['ebox-module-script'] = __FUNCTION__;
			wp_localize_script( 'ebox-module-script', 'ebox_data', array() );
		}
	}

	if ( ( in_array( $pagenow, array( 'post.php', 'post-new.php' ), true ) ) && ( 'ebox-quiz' === $post->post_type ) ) {
		wp_enqueue_script(
			'wpProQuiz_admin_javascript',
			plugins_url( 'js/wpProQuiz_admin' . ebox_min_asset() . '.js', WPPROQUIZ_FILE ),
			array( 'jquery' ),
			ebox_SCRIPT_VERSION_TOKEN,
			true
		);
		$ebox_assets_loaded['scripts']['wpProQuiz_admin_javascript'] = __FUNCTION__;
	}

	if ( ( in_array( $pagenow, array( 'post.php', 'post-new.php' ), true ) ) && ( 'ebox-modules' === $post->post_type ) ) {
		wp_enqueue_style(
			'ld-datepicker-ui-css',
			ebox_LMS_PLUGIN_URL . 'assets/css/jquery-ui' . ebox_min_asset() . '.css',
			array(),
			ebox_SCRIPT_VERSION_TOKEN
		);
		wp_style_add_data( 'ld-datepicker-ui-css', 'rtl', 'replace' );
		$ebox_assets_loaded['styles']['ld-datepicker-ui-css'] = __FUNCTION__;
	}

	if ( ( ( 'admin.php' === $pagenow ) && ( isset( $_GET['page'] ) ) && ( 'ldAdvQuiz' === $_GET['page'] ) ) && ( ( isset( $_GET['module'] ) ) && ( 'statistics' === $_GET['module'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		wp_enqueue_style(
			'ld-datepicker-ui-css',
			ebox_LMS_PLUGIN_URL . 'assets/css/jquery-ui' . ebox_min_asset() . '.css',
			array(),
			ebox_SCRIPT_VERSION_TOKEN
		);
		wp_style_add_data( 'ld-datepicker-ui-css', 'rtl', 'replace' );
		$ebox_assets_loaded['styles']['ld-datepicker-ui-css'] = __FUNCTION__;
	}
}
add_action( 'admin_enqueue_scripts', 'ebox_load_admin_resources' );

/**
 * Outputs the Reports Page.
 *
 * @since 2.1.0
 */
function ebox_lms_reports_page() {
	?>
		<div  id="ebox-reports"  class="wrap">
			<h1><?php esc_html_e( 'User Reports', 'ebox' ); ?></h1>
			<br>
			<div class="ebox_settings_left">
				<div class=" " id="ebox-ebox-reports_metabox">
					<div class="inside">
						<a class="button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=ebox-lms-reports&action=sfp_update_module&nonce-ebox=' . esc_attr( wp_create_nonce( 'ebox-nonce' ) ) . '&page_options=sfp_home_description&courses_export_submit=Export' ) ); ?>">
						<?php
						// translators: Export User Course Data Label.
						printf( esc_html_x( 'Export User %s Data', 'Export User Course Data Label', 'ebox' ), ebox_Custom_Label::get_label( 'course' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
						?>
						</a>
						<a class="button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=ebox-lms-reports&action=sfp_update_module&nonce-ebox=' . esc_attr( wp_create_nonce( 'ebox-nonce' ) ) . '&page_options=sfp_home_description&quiz_export_submit=Export' ) ); ?>">
						<?php
						printf(
						// translators: Export Quiz Data Label.
							esc_html_x( 'Export %s Data', 'Export Quiz Data Label', 'ebox' ),
							ebox_Custom_Label::get_label( 'quiz' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
						);
						?>
						</a>
						<?php
							/**
							 * Fires after report page buttons.
							 *
							 * @since 2.1.0
							 */
							do_action( 'ebox_report_page_buttons' );
						?>
					</div>
				</div>
			</div>
		</div>
	<?php
}

/**
 * Adds JavaScript code to the admin footer.
 *
 * @since 2.1.0
 *
 * @global string $ebox_current_page_link
 * @global string $parent_file
 * @global string $submenu_file
 *
 * @TODO We need to get rid of this JS logic and replace with filter to set the $parent_file
 * See:
 * https://developer.wordpress.org/reference/hooks/parent_file/
 * https://developer.wordpress.org/reference/hooks/submenu_file/
 */
function ebox_select_menu() {
	global $ebox_current_page_link;
	global $parent_file, $submenu_file;

	if ( ! empty( $ebox_current_page_link ) ) {
		?>
		<script type="text/javascript">
		//jQuery(window).on('load', function( $) {
			jQuery("body").removeClass("sticky-menu");
			jQuery("#toplevel_page_ebox-lms, #toplevel_page_ebox-lms > a").removeClass('wp-not-current-submenu' );
			jQuery("#toplevel_page_ebox-lms").addClass('current wp-has-current-submenu wp-menu-open' );
			jQuery("#toplevel_page_ebox-lms a[href='<?php echo esc_url( $ebox_current_page_link ); ?>']").parent().addClass("current");
		//});
		</script>
		<?php
	}
};

/**
 * Prints the AJAX lazy loaded element results.
 *
 * Fires on `ebox_element_lazy_loader` AJAX action.
 *
 * @since 2.2.1
 */
function ebox_element_lazy_loader() {

	$reply_data = array();

	if ( current_user_can( 'read' ) ) {
		if ( ( isset( $_POST['query_data']['nonce'] ) ) && ( ! empty( $_POST['query_data']['nonce'] ) ) ) {
			if ( ( isset( $_POST['query_data']['query_vars']['post_type'] ) ) && ( ! empty( $_POST['query_data']['query_vars']['post_type'] ) ) ) {
				if ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['query_data']['nonce'] ) ), sanitize_text_field( wp_unslash( $_POST['query_data']['query_vars']['post_type'] ) ) ) ) {

					if ( ( isset( $_POST['query_data']['query_vars'] ) ) && ( ! empty( $_POST['query_data']['query_vars'] ) ) ) {
						$reply_data['query_data'] = $_POST['query_data']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

						if ( isset( $_POST['query_data']['query_type'] ) ) {
							switch ( $_POST['query_data']['query_type'] ) {
								case 'WP_Query':
									$query = new WP_Query( $_POST['query_data']['query_vars'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
									if ( $query instanceof WP_Query ) {
										if ( ! empty( $query->posts ) ) {
											$reply_data['html_options'] = '';
											foreach ( $query->posts as $p ) {
												if ( intval( $p->ID ) == intval( $_POST['query_data']['value'] ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
													$selected = ' selected="selected" ';
												} else {
													$selected = '';
												}
												$reply_data['html_options'] .= '<option ' . $selected . ' value="' . $p->ID . '">' . apply_filters( 'the_title', $p->post_title, $p->ID ) . '</option>'; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WP Core Hook
											}
										}
									}
									break;

								case 'WP_User_Query':
									$query = new WP_User_Query( $_POST['query_data']['query_vars'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
									break;

								default:
									break;
							}
						}
					}
				}
			}
		}
	}

	echo wp_json_encode( $reply_data );

	wp_die(); // this is required to terminate immediately and return a proper response.
}
add_action( 'wp_ajax_ebox_element_lazy_loader', 'ebox_element_lazy_loader' );

/**
 * Adds the changelog link in plugin row meta.
 *
 * Fires on `plugin_row_meta` hook.
 *
 * @since 2.4.0
 *
 * @param array  $plugin_meta An array of the plugin's metadata.
 * @param string $plugin_file  Path to the plugin file.
 * @param array  $plugin_data An array of plugin data.
 * @param string $status      Status of the plugin.
 *
 * @return array An array of the plugin's metadata.
 */
function ebox_plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {
	if ( ebox_LMS_PLUGIN_KEY === $plugin_file ) {
		if ( ! isset( $plugin_meta['changelog'] ) ) {
			$plugin_meta['changelog'] = '<a target="_blank" href="https://www.ebox.com/changelog">' . esc_html__( 'Changelog', 'ebox' ) . '</a>';
		}
	}

	return $plugin_meta;
}
add_filter( 'plugin_row_meta', 'ebox_plugin_row_meta', 10, 4 );

/**
 * Overrides the post tag edit 'count' column to show only the related count for the ebox post types.
 *
 * Fires on `manage_edit-post_tag_columns` and `manage_edit-category_columns` hook.
 *
 * @since 2.4.0
 *
 * @param array $columns Optional. An array of column headers. Default empty array.
 *
 * @return array An array of column headers.
 */
function ebox_manage_edit_post_tag_columns( $columns = array() ) {
	if ( ( isset( $_GET['post_type'] ) ) && ( ! empty( $_GET['post_type'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( in_array( $_GET['post_type'], array( 'ebox-courses', 'ebox-modules', 'ebox-topic' ), true ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( isset( $columns['posts'] ) ) {
				unset( $columns['posts'] );
			}
			$columns['ld_posts'] = esc_html__( 'Count', 'ebox' );
		}
	}

	return $columns;
}
add_filter( 'manage_edit-post_tag_columns', 'ebox_manage_edit_post_tag_columns' );
add_filter( 'manage_edit-category_columns', 'ebox_manage_edit_post_tag_columns' );

/**
 * Gets the custom column content for post_tag taxonomy in the terms list table.
 *
 * Fires on `manage_post_tag_custom_column` hook.
 *
 * @since 2.4.0
 *
 * @param string $column_content Column content. Default empty.
 * @param string $column_name    Name of the column.
 * @param int    $term_id        Term ID.
 *
 * @return string Taxonomy custom column content.
 */
function ebox_manage_post_tag_custom_column( $column_content, $column_name, $term_id ) {
	if ( 'ld_posts' === $column_name ) {
		if ( ( isset( $_GET['post_type'] ) ) && ( ! empty( $_GET['post_type'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( in_array( $_GET['post_type'], array( 'ebox-courses', 'ebox-modules', 'ebox-topic' ), true ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$query_args = array(
					'post_type'   => sanitize_text_field( wp_unslash( $_GET['post_type'] ) ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					'post_status' => 'publish',
					'tag_id'      => $term_id,
					'fields'      => 'ids',
					'nopaging'    => true,
				);

				$query_results = new WP_Query( $query_args );
				if ( is_a( $query_results, 'WP_Query' ) ) {
					$count = count( $query_results->posts );
					if ( $count > 0 ) {
						$term = get_term_by( 'id', $term_id, 'category' );
						if ( is_a( $term, 'WP_Term' ) ) {
							$column_content = "<a href='" . esc_url(
								add_query_arg(
									array(
										'post_type' => sanitize_text_field( wp_unslash( $_GET['post_type'] ) ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
										'taxonomy'  => 'post_tag',
										'post_tag'  => $term->slug,
									),
									'edit.php'
								)
							) . "'>" . count( $query_results->posts ) . '</a>';
						}
					} else {
						$column_content = 0;
					}
				}
			}
		}
	}
	return $column_content;
}
add_filter( 'manage_post_tag_custom_column', 'ebox_manage_post_tag_custom_column', 10, 3 );

/**
 * Gets the custom column content for category taxonomy in the terms list table.
 *
 * Fires on `manage_category_custom_column` hook.
 *
 * @since 2.4.0
 *
 * @param string $column_content Column content. Default empty.
 * @param string $column_name    Name of the column.
 * @param int    $term_id        Term ID.
 *
 * @return string Taxonomy custom column content.
 */
function ebox_manage_category_custom_column( $column_content, $column_name, $term_id ) {
	if ( 'ld_posts' === $column_name ) {
		if ( ( isset( $_GET['post_type'] ) ) && ( ! empty( $_GET['post_type'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( in_array( $_GET['post_type'], array( 'ebox-courses', 'ebox-modules', 'ebox-topic' ), true ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$query_args = array(
					'post_type'   => sanitize_text_field( wp_unslash( $_GET['post_type'] ) ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					'post_status' => 'publish',
					'cat'         => $term_id,
					'fields'      => 'ids',
					'nopaging'    => true,
				);

				$query_results = new WP_Query( $query_args );
				if ( is_a( $query_results, 'WP_Query' ) ) {
					$count = count( $query_results->posts );
					if ( $count > 0 ) {
						$column_content = "<a href='" . esc_url(
							add_query_arg(
								array(
									'post_type' => sanitize_text_field( wp_unslash( $_GET['post_type'] ) ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
									'taxonomy'  => 'category',
									'cat'       => $term_id,
								),
								'edit.php'
							)
						) . "'>" . count( $query_results->posts ) . '</a>';
					} else {
						$column_content = 0;
					}
				}
			}
		}
	}
	return $column_content;
}
add_filter( 'manage_category_custom_column', 'ebox_manage_category_custom_column', 10, 3 );

/**
 * Deletes all the ebox data.
 *
 * @since 2.4.5
 *
 * @global wpdb  $wpdb                 WordPress database abstraction object.
 * @global array $ebox_post_types An array of ebox post types.
 * @global array $ebox_taxonomies An array of ebox taxonomies.
 */
function ebox_delete_all_data() {
	global $wpdb, $ebox_post_types, $ebox_taxonomies;

	/**
	 * Under Multisite we don't even want to remove user data. This is because users and usermeta
	 * is shared. Removing the LD user data could result in lost information for other sites.
	 */
	if ( ! is_multisite() ) {
		// USER META SETTINGS.

		$wpdb->query( 'DELETE FROM ' . $wpdb->usermeta . " WHERE meta_key='_ebox-course_progress'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->query( 'DELETE FROM ' . $wpdb->usermeta . " WHERE meta_key='_ebox-quizzes'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

		$wpdb->query( 'DELETE FROM ' . $wpdb->usermeta . " WHERE meta_key LIKE 'completed_%'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->query( 'DELETE FROM ' . $wpdb->usermeta . " WHERE meta_key LIKE 'course_%_access_from'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->query( 'DELETE FROM ' . $wpdb->usermeta . " WHERE meta_key LIKE 'course_completed_%'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->query( 'DELETE FROM ' . $wpdb->usermeta . " WHERE meta_key LIKE 'ebox_course_expired_%'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->query( 'DELETE FROM ' . $wpdb->usermeta . " WHERE meta_key LIKE 'ebox_team_users_%'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->query( 'DELETE FROM ' . $wpdb->usermeta . " WHERE meta_key LIKE 'ebox_team_leaders_%'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

		$wpdb->query( 'DELETE FROM ' . $wpdb->usermeta . " WHERE meta_key = 'ld-upgraded-user-meta-courses'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->query( 'DELETE FROM ' . $wpdb->usermeta . " WHERE meta_key = 'ld-upgraded-user-meta-quizzes'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->query( 'DELETE FROM ' . $wpdb->usermeta . " WHERE meta_key = 'course_points'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	}

	// CUSTOM OPTIONS.

	$wpdb->query( 'DELETE FROM ' . $wpdb->options . " WHERE option_name LIKE 'ebox_%'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$wpdb->query( 'DELETE FROM ' . $wpdb->options . " WHERE option_name LIKE 'wpProQuiz_%'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

	// CUSTOMER POST TYPES.

	$ld_post_types = '';
	foreach ( $ebox_post_types as $post_type ) {
		if ( ! empty( $ld_post_types ) ) {
			$ld_post_types .= ',';
		}
		$ld_post_types .= "'" . $post_type . "'";
	}

	$post_ids = $wpdb->get_col( 'SELECT ID FROM ' . $wpdb->posts . ' WHERE post_type IN (' . $ld_post_types . ')' ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
	if ( ! empty( $post_ids ) ) {

		$offset = 0;

		while ( true ) {
			$post_ids_part = array_slice( $post_ids, $offset, 1000 );
			if ( empty( $post_ids_part ) ) {
				break;
			} else {
				$wpdb->query( 'DELETE FROM ' . $wpdb->postmeta . ' WHERE post_id IN (' . implode( ',', $post_ids ) . ')' ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
				$wpdb->query( 'DELETE FROM ' . $wpdb->posts . ' WHERE post_parent IN (' . implode( ',', $post_ids ) . ')' ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
				$wpdb->query( 'DELETE FROM ' . $wpdb->posts . ' WHERE ID IN (' . implode( ',', $post_ids ) . ')' ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared,

				$offset += 1000;
			}
		}
	}

	// CUSTOM TAXONOMIES & TERMS.

	foreach ( $ebox_taxonomies as $taxonomy ) {
		// Prepare & execute SQL.
		$terms = $wpdb->get_results( $wpdb->prepare( "SELECT t.*, tt.* FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy IN ('%s') ORDER BY t.name ASC", $taxonomy ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQLPlaceholders.QuotedSimplePlaceholder

			// Delete Terms.
		if ( $terms ) {
			foreach ( $terms as $term ) {
				$wpdb->delete( $wpdb->term_taxonomy, array( 'term_taxonomy_id' => $term->term_taxonomy_id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->delete( $wpdb->terms, array( 'term_id' => $term->term_id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			}
		}

		// Delete Taxonomy.
		$wpdb->delete( $wpdb->term_taxonomy, array( 'taxonomy' => $taxonomy ), array( '%s' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	}

	// CUSTOM DB TABLES.

	$ebox_db_tables = LDLMS_DB::get_tables();
	if ( ! empty( $ebox_db_tables ) ) {
		foreach ( $ebox_db_tables as $table_name ) {
			if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name ) { // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$wpdb->query( 'DROP TABLE ' . $table_name ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
			}
		}
	}

	// USER ROLES AND CAPABILITIES.

	remove_role( 'team_leader' );

	// Remove any user/role capabilities we added.
	$roles = get_editable_roles();
	if ( ! empty( $roles ) ) {
		foreach ( $roles as $role_name => $role_info ) {
			$role = get_role( $role_name );
			if ( ( $role ) && ( $role instanceof WP_Role ) ) {
				$role->remove_cap( 'read_assignment' );
				$role->remove_cap( 'edit_assignment' );
				$role->remove_cap( 'edit_assignments' );
				$role->remove_cap( 'edit_others_assignments' );
				$role->remove_cap( 'publish_assignments' );
				$role->remove_cap( 'read_assignment' );
				$role->remove_cap( 'read_private_assignments' );
				$role->remove_cap( 'delete_assignment' );
				$role->remove_cap( 'edit_published_assignments' );
				$role->remove_cap( 'delete_others_assignments' );
				$role->remove_cap( 'delete_published_assignments' );

				$role->remove_cap( 'team_leader' );
				$role->remove_cap( 'enroll_users' );

				$role->remove_cap( 'edit_essays' );
				$role->remove_cap( 'edit_others_essays' );
				$role->remove_cap( 'publish_essays' );
				$role->remove_cap( 'read_essays' );
				$role->remove_cap( 'read_private_essays' );
				$role->remove_cap( 'delete_essays' );
				$role->remove_cap( 'edit_published_essays' );
				$role->remove_cap( 'delete_others_essays' );
				$role->remove_cap( 'delete_published_essays' );

				$role->remove_cap( 'wpProQuiz_show' );
				$role->remove_cap( 'wpProQuiz_add_quiz' );
				$role->remove_cap( 'wpProQuiz_edit_quiz' );
				$role->remove_cap( 'wpProQuiz_delete_quiz' );
				$role->remove_cap( 'wpProQuiz_show_statistics' );
				$role->remove_cap( 'wpProQuiz_reset_statistics' );
				$role->remove_cap( 'wpProQuiz_import' );
				$role->remove_cap( 'wpProQuiz_export' );
				$role->remove_cap( 'wpProQuiz_change_settings' );
				$role->remove_cap( 'wpProQuiz_toplist_edit' );
				$role->remove_cap( 'wpProQuiz_toplist_edit' );
			}
		}
	}

	// ASSIGNMENT & ESSAY UPLOADS.

	$url_link_arr   = wp_upload_dir();
	$assignment_dir = $url_link_arr['basedir'] . '/assignments';
	ebox_recursive_rmdir( $assignment_dir );

	$essays_dir = $url_link_arr['basedir'] . '/essays';
	ebox_recursive_rmdir( $essays_dir );

	$ld_template_dir = $url_link_arr['basedir'] . '/template';
	ebox_recursive_rmdir( $ld_template_dir );
}

/**
 * Loads the plugin translations into `wp.i18n` for use in JavaScript.
 *
 * @since 3.0.0
 */
function ebox_load_inline_script_locale_data() {
	static $loaded = false;

	if ( false === $loaded ) {
		$loaded      = true;
		$locale_data = ebox_get_jed_locale_data( ebox_LMS_TEXT_DOMAIN );
		wp_add_inline_script(
			'wp-i18n',
			'wp.i18n.setLocaleData( ' . wp_json_encode( $locale_data ) . ', "ebox" );'
		);
	}
}

/**
 * Loads the translations MO file into memory.
 *
 * @since 3.0.0
 *
 * @param string $domain The textdomain.
 *
 * @return array An array of translated strings.
 */
function ebox_get_jed_locale_data( $domain = '' ) {
	if ( empty( $domain ) ) {
		$domain = ebox_LMS_TEXT_DOMAIN;
	}
	$translations = get_translations_for_domain( $domain );

	$locale = array(
		'' => array(
			'domain' => $domain,
			'lang'   => is_admin() ? get_user_locale() : get_locale(),
		),
	);

	if ( ! empty( $translations->headers['Plural-Forms'] ) ) {
		$locale['']['plural_forms'] = $translations->headers['Plural-Forms'];
	}

	foreach ( $translations->entries as $msgid => $entry ) {
		$locale[ $msgid ] = $entry->translations;
	}

	return $locale;
}

$ebox_other_plugins_active_text = '';
global $ebox_other_plugins_active_text;

/**
 * Check for other LMS plugins
 *
 * @since 3.2.3
 */
function ebox_check_other_lms_plugins() {
	global $ebox_other_plugins_active_text;

	$ebox_other_plugins_active_text = '';

	$lms_plugins = array(
		'lifterlms/lifterlms.php'         => array(
			'label'    => 'Lifter LMS',
			'plugin'   => 'lifterlms/lifterlms.php',
			'function' => 'llms',
		),
		'sensei-lms/sensei-lms.php'       => array(
			'label'  => 'Sensei LMS',
			'plugin' => 'sensei-lms/sensei-lms.php',
		),
		'tutor/tutor.php'                 => array(
			'label'  => 'Tutor LMS',
			'plugin' => 'tutor/tutor.php',
		),
		'wp-courses/wp-courses.php'       => array(
			'label'  => 'WP Courses LMS',
			'plugin' => 'wp-courses/wp-courses.php',
		),
		'wp-courseware/wp-courseware.php' => array( // cspell:disable-line.
			'label'    => 'WP Courseware', // cspell:disable-line.
			'function' => 'WPCW_plugin_init',
		),
		'WPLMS'                           => array(
			'label'  => 'WPLMS Theme',
			'define' => 'WPLMS_VERSION',
		),
	);

	foreach ( $lms_plugins as $plugin_set ) {
		$plugin_active = false;

		if ( ( isset( $plugin_set['plugin'] ) ) && ( ! empty( $plugin_set['plugin'] ) ) ) { // @phpstan-ignore-line
			if ( ( is_plugin_active( $plugin_set['plugin'] ) ) || ( ( is_multisite() ) && ( is_plugin_active_for_network( $plugin_set['plugin'] ) ) ) ) {
				$plugin_active = true;
			}
		} elseif ( ( isset( $plugin_set['class'] ) ) && ( ! empty( $plugin_set['class'] ) ) ) { // @phpstan-ignore-line
			if ( class_exists( $plugin_set['class'] ) ) {
				$plugin_active = true;
			}
		} elseif ( ( isset( $plugin_set['function'] ) ) && ( ! empty( $plugin_set['function'] ) ) ) { // @phpstan-ignore-line
			if ( function_exists( $plugin_set['function'] ) ) { // @phpstan-ignore-line
				$plugin_active = true;
			}
		} elseif ( ( isset( $plugin_set['define'] ) ) && ( ! empty( $plugin_set['define'] ) ) ) { // @phpstan-ignore-line
			if ( defined( $plugin_set['define'] ) ) {
				$plugin_active = true;
			}
		}

		if ( ( $plugin_active ) && ( isset( $plugin_set['label'] ) ) && ( ! empty( $plugin_set['label'] ) ) ) { // @phpstan-ignore-line
			if ( ! empty( $ebox_other_plugins_active_text ) ) {
				$ebox_other_plugins_active_text .= ', ';
			}
			$ebox_other_plugins_active_text .= $plugin_set['label'];
		}
	}
}

add_action( 'admin_init', 'ebox_check_other_lms_plugins' );

/**
 * Admin notice other LMS plugins
 *
 * @since 3.2.3
 */
function ebox_admin_notice_other_lms_plugins() {
	global $ebox_other_plugins_active_text;

	$current_screen = get_current_screen();

	if ( ! empty( $ebox_other_plugins_active_text ) ) {
		$notice_time = get_user_meta( get_current_user_id(), 'ebox_other_plugins_notice_dismissed_nonce', true );
		$notice_time = absint( $notice_time );
		if ( ! empty( $notice_time ) ) {
			return;
		}

		?>
		<div class="notice notice-error notice-alt is-dismissible ld-plugin-other-plugins-notice" data-notice-dismiss-nonce="<?php echo esc_attr( wp_create_nonce( 'notice-dismiss-nonce-' . get_current_user_id() ) ); ?>">
		<?php
			echo wp_kses_post(
				wpautop(
					sprintf(
						// translators: placeholder: list of active LMS plugins.
						_x( '<strong>Ebox LMS</strong> has detected other active LMS plugins which may cause conflicts: <strong>%s</strong>', 'placeholder: list of active LMS plugins', 'ebox' ),
						$ebox_other_plugins_active_text
					)
				)
			);
		?>
		</div>
		<?php
	}
}
add_action( 'admin_notices', 'ebox_admin_notice_other_lms_plugins' );


/**
 * AJAX function to handle other plugins notice dismiss action from browser.
 *
 * @since 3.2.3
 */
function ebox_admin_other_plugins_notice_dismissed_ajax() {
	$user_id = get_current_user_id();
	if ( ! empty( $user_id ) ) {
		if ( ( isset( $_POST['action'] ) ) && ( 'ebox_other_plugins_notice_dismissed' === $_POST['action'] ) ) {
			if ( ( isset( $_POST['ebox_other_plugins_notice_dismissed_nonce'] ) ) && ( ! empty( $_POST['ebox_other_plugins_notice_dismissed_nonce'] ) ) && ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ebox_other_plugins_notice_dismissed_nonce'] ) ), 'notice-dismiss-nonce-' . $user_id ) ) ) {
				update_user_meta( $user_id, 'ebox_other_plugins_notice_dismissed_nonce', time() );
			}
		}
	}

	die();
}
add_action( 'wp_ajax_ebox_other_plugins_notice_dismissed', 'ebox_admin_other_plugins_notice_dismissed_ajax' );
