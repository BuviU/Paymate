<?php
/**
 * Team Leader Teams Listing table.
 *
 * @since 2.3.0
 * @package ebox\Team_Users
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

if ( ! class_exists( 'ebox_Admin_Teams_Users_List_Table' ) ) {
	/**
	 * Class Team Leader Teams Listing table.
	 *
	 * @since 2.3.0
	 * @uses WP_List_Table
	 */
	class ebox_Admin_Teams_Users_List_Table extends WP_List_Table {

		/**
		 * Array of filters
		 *
		 * @var array $filters
		 */
		public $filters = array();

		/**
		 * Items per page
		 *
		 * @var integer $per_page
		 */
		public $per_page = 20;

		/**
		 * Array of columns
		 *
		 * @var array $columns
		 */
		public $columns = array();

		/**
		 * Team ID
		 *
		 * @var integer $team_id
		 */
		public $team_id = 0;

		/**
		 * Public constructor for class
		 *
		 * @since 2.3.0
		 */
		public function __construct() {
			global $status, $page;

			// Set parent defaults.
			parent::__construct(
				array(
					'singular' => 'team',        // singular name of the listed records.
					'plural'   => 'teams',       // plural name of the listed records.
					'ajax'     => true,           // does this table support ajax?
				)
			);
		}

		/**
		 * Check table filters
		 *
		 * @since 2.3.0
		 */
		public function check_table_filters() {
			$this->filters = array();

			if ( ( isset( $_GET['s'] ) ) && ( ! empty( $_GET['s'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$this->filters['search'] = sanitize_text_field( wp_unslash( $_GET['s'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			}
		}

		/**
		 * Table search box
		 *
		 * @since 2.3.0
		 *
		 * @param string $text      Search text.
		 * @param string $input_id  Search field HTML ID.
		 */
		public function search_box( $text, $input_id ) {
			if ( empty( $_REQUEST['s'] ) && ! $this->has_items() ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				return;
			}

			$input_id = $input_id . '-search-input';

			echo '<input type="hidden" name="paged" value="1" />';

			?><p class="search-box"><label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php echo esc_html( $text ); ?>:</label><input type="search" id="<?php echo esc_attr( $input_id ); ?>" name="s" value="<?php _admin_search_query(); ?>" /><?php submit_button( $text, 'button', 'ebox-search', false, array( 'id' => 'search-submit' ) ); ?></p>
			<?php
		}

		/**
		 * Extra table nav
		 *
		 * @since 2.3.0
		 *
		 * @param string $which Which (top/bottom ) nav being rendered.
		 */
		public function extra_tablenav( $which ) {
			if ( 'top' == $which ) {
				if ( ! empty( $this->team_id ) ) {

					?>
					<div class="alignleft actions">
						<a href="<?php echo esc_url( add_query_arg( 'action', 'ebox-team-email' ) ); ?>" class="button button-secondary">
											<?php
											echo sprintf(
											// translators: placeholder: Team.
												esc_html_x( 'Email %s', 'placeholder: Team', 'ebox' ),
												ebox_Custom_Label::get_label( 'team' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
											);
											?>
						</a>
					</div>
					<?php
				}
			}
		}

		/**
		 * Get table columns
		 *
		 * @since 2.3.0
		 */
		public function get_columns() {
			return $this->columns;
		}

		/**
		 * Show row item team name column
		 *
		 * @since 2.3.0
		 *
		 * @param object $item Team WP_Post object.
		 */
		public function column_team_name( $item ) {
			$output = '';

			if ( current_user_can( 'edit_team', $item->ID ) ) {
				$output .= '<strong><a href="' . get_edit_post_link( $item->ID ) . '">' . apply_filters( 'the_title', $item->post_title, $item->ID ) . '</a></strong>'; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WP Core Hook

				$row_actions = array( 'edit' => '<a href="' . get_edit_post_link( $item->ID ) . '">' . esc_html__( 'edit', 'ebox' ) . '</a>' );
				$output     .= $this->row_actions( $row_actions );

			} else {
				$output .= '<strong>' . apply_filters( 'the_title', $item->post_title, $item->ID ) . '</strong>'; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WP Core Hook
			}

			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Need to output HTML
		}

		/**
		 * Show row item team actions column
		 *
		 * @since 2.3.0
		 *
		 * @param object $item Team WP_Post object.
		 */
		public function column_team_actions( $item ) {
			$data_settings_courses = ebox_data_upgrades_setting( 'user-meta-courses' );
			$data_settings_quizzes = ebox_data_upgrades_setting( 'user-meta-quizzes' );

			$actions              = array();
			$actions['list-view'] = '<a href="' . esc_url( add_query_arg( 'team_id', $item->ID, remove_query_arg( array( 's', 'paged', 'ebox-search', 'ld-team-list-view-nonce', '_wp_http_referer', '_wpnonce' ) ) ) ) . '">' . esc_html__( 'List Users', 'ebox' ) . '</a>';

			if ( ( ! empty( $data_settings_courses ) ) && ( ! empty( $data_settings_quizzes ) ) ) {
				$data_slug             = 'user-courses';
				$actions[ $data_slug ] = '<a href="" class="ebox-data-team-reports-button" data-nonce="' . wp_create_nonce( 'ebox-data-reports-' . $data_slug . '-' . get_current_user_id() ) . '" data-team-id="' . $item->ID . '" data-slug="' . $data_slug . '">' . esc_html__( 'Export Progress', 'ebox' ) . '<span class="status"></span></a>';

				$data_slug             = 'user-quizzes';
				$actions[ $data_slug ] = '<a href="" class="ebox-data-team-reports-button" data-nonce="' . wp_create_nonce( 'ebox-data-reports-' . $data_slug . '-' . get_current_user_id() ) . '" data-team-id="' . $item->ID . '" data-slug="' . $data_slug . '">' . esc_html__( 'Export Results', 'ebox' ) . '<span class="status"></span></a>';
			} else {
				$data_slug             = 'user-courses';
				$actions[ $data_slug ] = '<a href="' . add_query_arg(
					array(
						'nonce-ebox'            => wp_create_nonce( 'ebox-nonce' ),
						'courses_export_submit' => 'Export',
						'team_id'              => $item->ID,
					),
					admin_url( 'admin.php?page=team_admin_page' )
				)
													. '">' . esc_html__( 'Export Progress', 'ebox' ) . '</a>';

				$data_slug             = 'user-quizzes';
				$actions[ $data_slug ] = '<a href="' . add_query_arg(
					array(
						'nonce-ebox'         => wp_create_nonce( 'ebox-nonce' ),
						'quiz_export_submit' => 'Export',
						'team_id'           => $item->ID,
					),
					admin_url( 'admin.php?page=team_admin_page' )
				)
													. '">' . esc_html__( 'Export Results', 'ebox' ) . '</a>';

			}

			if ( current_user_can( 'edit_teams' ) ) {
				$data_slug             = 'edit-team';
				$actions[ $data_slug ] = '<a href="' . get_edit_post_link( $item->ID ) . '">' . sprintf(
					// translators: placeholder: Team.
					esc_html_x( 'Edit %s', 'placeholder: Team', 'ebox' ),
					ebox_Custom_Label::get_label( 'team' )
				) . '</a>';
			}

			echo implode( ' | ', $actions ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Need to output HTML

			/**
			 * Fires after admin page team actions column.
			 *
			 * @since 2.3.0
			 *
			 * @param int $team_id Team ID.
			 */
			do_action( 'ebox_team_admin_page_actions', $item->ID );
		}

		/**
		 * Show row item username column
		 *
		 * @since 2.3.0
		 *
		 * @param object $item Team WP_Post object.
		 */
		public function column_username( $item ) {
			$output = '';

			$output .= get_avatar( $item->ID, 32 );

			if ( current_user_can( 'edit_users' ) ) {
				$output .= '<strong><a href="' . get_edit_user_link( $item->ID ) . '">' . $item->display_name . '</a></strong>';

				$row_actions = array( 'edit' => '<a href="' . get_edit_user_link( $item->ID ) . '">' . esc_html__( 'edit', 'ebox' ) . '</a>' );
				$output     .= $this->row_actions( $row_actions );

			} else {
				$output .= '<strong>' . $item->display_name . '</strong>';
			}
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Need to output HTML
		}

		/**
		 * Show row item name column
		 *
		 * @since 2.3.0
		 *
		 * @param object $item Team WP_Post object.
		 */
		public function column_name( $item ) {
			echo esc_html( $item->user_login );
		}

		/**
		 * Show row item email column
		 *
		 * @since 2.3.0
		 *
		 * @param object $item Team WP_Post object.
		 */
		public function column_email( $item ) {
			echo esc_html( $item->user_email );
		}

		/**
		 * Show row item user actions
		 *
		 * @since 2.3.0
		 *
		 * @param object $item Team WP_Post object.
		 */
		public function column_user_actions( $item ) {
			?>
			<a href="<?php echo esc_url( add_query_arg( 'user_id', $item->ID, remove_query_arg( array( 's', 'paged', 'ebox-search', 'ld-team-list-view-nonce', '_wp_http_referer', '_wpnonce' ) ) ) ); ?>"><?php esc_html_e( 'Report', 'ebox' ); ?></a>
			<?php
		}

		/**
		 * Prepare table list items
		 * This function performs the query and sets up the items to display.
		 *
		 * @since 2.3.0
		 */
		public function prepare_items() {

			$current_page = $this->get_pagenum();
			$total_items  = 0;
			$per_page     = $this->per_page;

			$this->items = array();

			if ( ! empty( $this->team_id ) ) {
				$users_ids = ebox_get_teams_user_ids( $this->team_id, true );
				if ( ! empty( $users_ids ) ) {
					$user_query_args = array(
						'include' => $users_ids,
						'orderby' => 'display_name',
						'order'   => 'ASC',
						'number'  => $per_page,
						'paged'   => $current_page,
					);

					if ( ! empty( $this->filters['search'] ) ) {
						$user_query_args['search'] = '*' . $this->filters['search'] . '*';
					}

					$user_query = new WP_User_Query( $user_query_args );

					$this->items = $user_query->get_results();
					$total_items = $user_query->get_total();
				}
			} else {
				$current_user = wp_get_current_user();

				$team_ids = ebox_get_administrators_team_ids( $current_user->ID, true );
				if ( ! empty( $team_ids ) ) {

					$teams_query_args = array(
						'post_type'      => 'teams',
						'posts_per_page' => $per_page,
						'paged'          => $current_page,
						'post__in'       => $team_ids,
					);

					if ( ! empty( $this->filters['search'] ) ) {
						$teams_query_args['s'] = '"' . $this->filters['search'] . '"';
						add_filter( 'posts_search', array( $this, 'search_filter_by_title' ), 10, 2 );
					}

					$teams_query = new WP_Query( $teams_query_args );

					if ( ! empty( $this->filters['search'] ) ) {
						remove_filter( 'posts_search', array( $this, 'search_filter_by_title' ), 10, 2 );
					}

					if ( ! empty( $teams_query->posts ) ) {
						$this->items = $teams_query->posts;
						$total_items = $teams_query->found_posts;
					}
				}
			}

			$this->set_pagination_args(
				array(
					'total_items' => intval( $total_items ),    // We have to calculate the total number of items.
					'per_page'    => intval( $per_page ),       // We have to determine how many items to show on a page.
					'total_pages' => ceil( intval( $total_items ) / intval( $per_page ) ),   // We have to calculate the total number of pages.
				)
			);
		}

		/**
		 * Search filter by title
		 *
		 * @since 2.3.0
		 *
		 * @param string $search   Search SQL for WHERE clause.
		 * @param object $wp_query The current WP_Query object.
		 */
		public function search_filter_by_title( $search, $wp_query ) {
			if ( ! empty( $search ) && ! empty( $wp_query->query_vars['search_terms'] ) ) {
				global $wpdb;

				$q = $wp_query->query_vars;
				$n = ! empty( $q['exact'] ) ? '' : '%';

				$search = array();

				foreach ( (array) $q['search_terms'] as $term ) {
					$search[] = $wpdb->prepare( "$wpdb->posts.post_title LIKE %s", $n . $term . $n );
				}

				if ( ! is_user_logged_in() ) {
					$search[] = "$wpdb->posts.post_password = ''";
				}

				$search = ' AND ' . implode( ' AND ', $search );
			}
			return $search;
		}

	}
}
