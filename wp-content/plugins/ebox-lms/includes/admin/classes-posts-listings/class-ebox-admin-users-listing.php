<?php
/**
 * ebox Users Listing.
 *
 * @since 3.2.3
 * @package ebox\Users\Listing
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Admin_Posts_Listing' ) ) && ( ! class_exists( 'ebox_Admin_Users_Listing' ) ) ) {

	/**
	 * Class ebox Users Listing.
	 *
	 * @since 3.2.3
	 * @uses ebox_Admin_Posts_Listing
	 */
	class ebox_Admin_Users_Listing extends ebox_Admin_Posts_Listing {

		/**
		 * Public constructor for class
		 *
		 * @since 3.2.3
		 */
		public function __construct() {
			$this->post_type = 'user';

			parent::__construct();
		}

		/**
		 * Called via the WordPress init action hook.
		 *
		 * @since 3.2.3
		 */
		public function listing_init() {
			if ( $this->listing_init_done ) {
				return;
			}

			$this->selectors = array(
				'team_id'  => array(
					'type'                     => 'post_type',
					'post_type'                => ebox_get_post_type_slug( 'team' ),
					'show_all_value'           => '',
					'show_all_label'           => sprintf(
						// translators: placeholder: Teams.
						esc_html_x( 'All %s', 'placeholder: Teams', 'ebox' ),
						ebox_Custom_Label::get_label( 'teams' )
					),
					'listing_query_function'   => array( $this, 'listing_filter_by_user_team' ),
					'selector_filter_function' => array( $this, 'selector_filter_for_user_team' ),
					'selector_value_function'  => array( $this, 'selector_value_for_team' ),
				),
				'course_id' => array(
					'type'                     => 'post_type',
					'post_type'                => ebox_get_post_type_slug( 'course' ),
					'show_all_value'           => '',
					'show_all_label'           => sprintf(
						// translators: placeholder: Courses.
						esc_html_x( 'All %s', 'placeholder: Courses', 'ebox' ),
						ebox_Custom_Label::get_label( 'courses' )
					),
					'selector_filters'         => array( 'team_id' ),
					'listing_query_function'   => array( $this, 'listing_filter_by_user_course' ),
					'selector_filter_function' => array( $this, 'selector_filter_for_user_course' ),
					'selector_value_function'  => array( $this, 'selector_value_for_course' ),
				),
			);

			$this->columns = array(
				'teams_courses' => array(
					'label'   => sprintf(
						// translators: placeholder: Teams, Courses.
						esc_html_x( 'Enrolled %1$s / %2$s', 'placeholder: Teams, Courses', 'ebox' ),
						ebox_Custom_Label::get_label( 'teams' ),
						ebox_Custom_Label::get_label( 'courses' )
					),
					'display' => array( $this, 'show_column_user_teams_courses' ),
				),
			);

			parent::listing_init();

			$this->listing_init_done = true;
		}

		/**
		 * Call via the WordPress load sequence for admin pages.
		 *
		 * @since 3.2.3
		 */
		public function on_load_listing() {
			if ( $this->post_type_check() ) {
				$this->listing_init();

				if ( 'user' === $this->post_type ) {
					// Add the top nav filter selectors.
					add_action( 'manage_users_extra_tablenav', array( $this, 'restrict_manage_users_selectors' ), 50 );

					// Add the columns headers and rows.
					add_filter( 'manage_users_columns', array( $this, 'manage_column_headers' ), 50, 1 );
					add_filter( 'manage_users_custom_column', array( $this, 'manage_user_column_rows' ), 50, 3 );

					// Filter the Users listing query args.
					add_filter( 'users_list_table_query_args', array( $this, 'users_list_table_query_args' ), 50, 1 );

					if ( ( ! current_user_can( 'edit_teams' ) ) && ( ! current_user_can( 'edit_courses' ) ) ) {
						if ( isset( $this->columns['teams_courses'] ) ) {
							unset( $this->columns['teams_courses'] );
						}
					}
				}
			}
		}

		/**
		 * Adds the user course filter in admin.
		 *
		 * Fires on `restrict_manage_users` hook.
		 *
		 * @since 3.2.3
		 *
		 * @param string $location Optional. The location of the extra table nav markup: 'top' or 'bottom'. Default empty.
		 */
		public function restrict_manage_users_selectors( $location = '' ) {
			if ( ! $this->post_type_check() ) {
				return;
			}

			if ( 'top' !== $location ) {
				return;
			}

			$this->show_nonce_field();
			$this->show_early_selectors();
			$this->show_post_type_selectors();
			$this->show_late_selectors();

			$button_id = 'bottom' === $location ? 'ld_submit' : 'ld_submit_bottom'; // @phpstan-ignore-line
			submit_button( esc_html__( 'Filter', 'ebox' ), 'ebox', $button_id, false );
		}

		/**
		 * This function fill filter the table listing items based on filters selected.
		 * Called via 'parse_query' filter from WP.
		 *
		 * @since 3.2.3
		 *
		 * @param array $q_vars Query vars.
		 *
		 * @return array $q_vars Query vars
		 */
		public function users_list_table_query_args( $q_vars = array() ) {
			if ( $this->post_type_check() ) {

				// First build a list of the filter values.
				$this->fill_selectors_values();

				if ( ! empty( $this->selectors ) ) {
					foreach ( $this->selectors as $post_type_key => &$selector ) {
						if ( ( isset( $selector['listing_query_function'] ) ) && ( ! empty( $selector['listing_query_function'] ) ) && ( is_callable( $selector['listing_query_function'] ) ) ) {
							$q_vars = call_user_func( $selector['listing_query_function'], $q_vars, $selector );
						}
					}
				}

				return $q_vars;
			}

			return array();
		}

		/**
		 * Filter the main query listing by the team_id
		 *
		 * @since 3.2.3
		 *
		 * @param array $q_vars   Query vars for table listing.
		 * @param array $selector Array of attributes used to display the filter selector.
		 *
		 * @return array $q_vars Query vars for table listing.
		 */
		protected function listing_filter_by_user_team( $q_vars = array(), $selector = array() ) {
			if ( ( isset( $selector['selected'] ) ) && ( ! empty( $selector['selected'] ) ) ) {
				if ( ebox_is_team_leader_user( get_current_user_id() ) ) {
					$team_ids = ebox_get_administrators_team_ids( get_current_user_id() );
					$team_ids = array_map( 'absint', $team_ids );

					// If the Team Leader doesn't have teams or not a managed team them clear our selected team_id.
					if ( ( empty( $team_ids ) ) || ( in_array( absint( $selector['selected'] ), $team_ids, true ) === false ) ) {
						$selector['selected'] = 0;
					}
				}

				if ( ! empty( $selector['selected'] ) ) {
					$q_vars['meta_key']     = 'ebox_team_users_' . $selector['selected']; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
					$q_vars['meta_value']   = $selector['selected']; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
					$q_vars['meta_compare'] = '=';
				}
			}

			return $q_vars;
		}

		/**
		 * Filter the main query listing by the course_id
		 *
		 * @since 3.2.3
		 *
		 * @param  array $q_vars   Query vars used for the table listing.
		 * @param  array $selector Array of attributes used to display the filter selector.
		 *
		 * @return array $q_vars.
		 */
		protected function listing_filter_by_user_course( $q_vars = array(), $selector = array() ) {
			if ( ( isset( $selector['selected'] ) ) && ( ! empty( $selector['selected'] ) ) ) {
				if ( ebox_is_team_leader_user( get_current_user_id() ) ) {
					$team_ids = ebox_get_administrators_team_ids( get_current_user_id() );
					if ( ! empty( $team_ids ) && is_array( $team_ids ) ) {
						$course_ids = array();
						foreach ( $team_ids as $team_id ) {
							$team_course_ids = ebox_team_enrolled_courses( $team_id );
							if ( ! empty( $team_course_ids ) && is_array( $team_course_ids ) ) {
								$course_ids = array_merge( $course_ids, $team_course_ids );
							}
						}
						if ( empty( $course_ids ) ) {
							$course_ids = array_map( 'absint', $course_ids );
							if ( ! in_array( absint( $selector['selected'] ), $course_ids, true ) ) {
								return $q_vars;
							}
						}
					}
				}

				if ( ! empty( $selector['selected'] ) ) {
					$course_price_type = ebox_get_setting( $selector['selected'], 'course_price_type' );
					if ( 'open' !== $course_price_type ) {
						$q_vars['include'] = array( 0 );

						$course_users_query = ebox_get_users_for_course( $selector['selected'], array(), false );
						if ( is_a( $course_users_query, 'WP_User_Query' ) ) {
							$q_vars['include'] = $course_users_query->get_results();
						}

						if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_General_Admin_User', 'courses_autoenroll_admin_users' ) === 'yes' ) {
							$admin_users = get_users(
								array(
									'role'   => 'administrator',
									'fields' => array( 'ID' ),
								)
							);
							if ( ! empty( $admin_users ) ) {
								$user_ids = wp_list_pluck( $admin_users, 'ID' );
								if ( ! empty( $user_ids ) ) {
									$user_ids = array_map( 'absint', $user_ids );
									$user_ids = array_diff( $user_ids, array( 0 ) );
								}
								if ( ! empty( $user_ids ) ) {
									$q_vars['include'] = array_merge( $q_vars['include'], $user_ids );
								}
							}
						}
					}
				}
			}

			return $q_vars;
		}

		/**
		 * Team Selector Filter.
		 *
		 * @since 3.2.3
		 *
		 * @param array $q_vars   Array of query vars.
		 * @param array $selector Selector array.
		 *
		 * @return array $q_vars  Query Args array.
		 */
		protected function selector_filter_for_user_team( $q_vars = array(), $selector = array() ) {
			if ( ( ebox_is_team_leader_user( get_current_user_id() ) ) && ( 'advanced' !== ebox_get_team_leader_manage_teams() ) ) {
				$gl_team_ids = ebox_get_administrators_team_ids( get_current_user_id() );
				if ( ! empty( $gl_team_ids ) ) {
					$q_vars['post__in'] = $gl_team_ids;
				} else {
					$q_vars['post__in'] = array( 0 );
				}
			}

			if ( has_filter( 'ebox_user_teams_options_filter' ) ) {
				/**
				 * Filters user teams filter query arguments.
				 *
				 * @since 2.5.0
				 * @deprecated 3.2.3
				 *
				 * @param array  $query_options_team An array of user teams filter query arguments.
				 * @param string $post_type           Post type to check.
				 */
				apply_filters_deprecated( 'ebox_user_teams_options_filter', array(), ebox_get_post_type_slug( 'team' ), '3.2.3' );
			}

			return $q_vars;
		}

		/**
		 * Course Selector Filter.
		 *
		 * @since 3.2.3
		 *
		 * @param array $q_vars   Array of query vars.
		 * @param array $selector Selector array.
		 *
		 * @return array $q_vars   Query Args array.
		 */
		protected function selector_filter_for_user_course( $q_vars = array(), $selector = array() ) {
			$team_selector = $this->get_selector( 'team_id' );
			if ( ( $team_selector ) && ( isset( $team_selector['selected'] ) ) && ( ! empty( $team_selector['selected'] ) ) ) {
				$team_course_ids = ebox_team_enrolled_courses( absint( $team_selector['selected'] ) );
				$team_course_ids = array_map( 'absint', $team_course_ids );
				if ( ! empty( $team_course_ids ) ) {
					$q_vars['post__in'] = $team_course_ids;
				} else {
					$q_vars['post__in'] = array( 0 );
				}
			} else {
				if ( ( ebox_is_team_leader_user( get_current_user_id() ) ) && ( 'advanced' !== ebox_get_team_leader_manage_courses() ) ) {
					$gl_course_ids = ebox_get_teams_administrators_courses( get_current_user_id() );
					if ( ! empty( $gl_course_ids ) ) {
						$q_vars['post__in'] = $gl_course_ids;
					} else {
						$q_vars['post__in'] = array( 0 );
					}
				}
			}

			if ( has_filter( 'ebox_user_courses_options_filter' ) ) {
				/**
				 * Filters users filter query arguments.
				 *
				 * @since 2.5.0
				 * @deprecated 3.2.3
				 *
				 * @param array  $query_options_course An array of users filter query arguments.
				 * @param string $post_type            Post type to check.
				 */
				apply_filters_deprecated( 'ebox_user_courses_options_filter', array(), ebox_get_post_type_slug( 'course' ), '3.2.3' );
			}

			return $q_vars;
		}


		/**
		 * Output custom user column row data
		 *
		 * @since 3.2.3
		 *
		 * @param string  $column_content Optional. Column content. Default empty.
		 * @param string  $column_name    Column slug or row being displayed.
		 * @param integer $user_id        User ID of row being displayed.
		 */
		public function manage_user_column_rows( $column_content = '', $column_name = '', $user_id = 0 ) {
			if ( $this->post_type_check() ) {
				if ( ! empty( $this->columns ) ) {
					foreach ( $this->columns as $column_key => $column ) {
						if ( $column_key === $column_name ) {
							if ( ( isset( $column['display'] ) ) && ( ! empty( $column['display'] ) ) && ( is_callable( $column['display'] ) ) ) {
								$column_content .= call_user_func( $column['display'], $column_content, $column_name, $user_id );
							}
						}
					}
				}
			}

			return $column_content;
		}

		/**
		 * Show the User Courses column.
		 *
		 * @since 3.2.3
		 *
		 * @param string $column_content Optional. Column content. Default empty.
		 * @param string $column_name    Optional. Name of the column. Default empty.
		 * @param int    $user_id        Optional. User ID. Default 0.
		 *
		 * @return string Users custom column content.
		 */
		public function show_column_user_teams_courses( $column_content = '', $column_name = '', $user_id = 0 ) {
			$hidden = (array) get_hidden_columns( get_current_screen()->id );
			if ( in_array( $column_name, $hidden, true ) ) {
				$column_content = esc_html__( 'reload', 'ebox' );
				return $column_content;
			}

			if ( current_user_can( 'edit_teams' ) ) {
				$user_teams = ebox_get_users_team_ids( $user_id, false );
				if ( empty( $user_teams ) ) {
					$user_teams = array();
				}

				if ( ! empty( $user_teams ) ) {
					$filter_url = add_query_arg(
						array(
							'post_type' => ebox_get_post_type_slug( 'team' ),
							'user_id'   => $user_id,
						),
						admin_url( 'edit.php' )
					);

					if ( ! empty( $column_content ) ) {
						$column_content .= '<br />';
					}

					$link_aria_label = sprintf(
						// translators: placeholder: Teams, User Nicename.
						esc_html_x( 'Filter %1$s by user "%2$s"', 'placeholder: Teams, User Nicename', 'ebox' ),
						ebox_Custom_Label::get_label( 'teams' ),
						get_user_by( 'ID', $user_id )->display_name
					);

					$column_content .= sprintf(
						// translators: placeholder: Teams, filter Teams by user URL.
						esc_html_x( 'Total %1$s: %2$s', 'placeholder: Teams, filter Teams by user URL', 'ebox' ),
						ebox_Custom_Label::get_label( 'teams' ),
						'<a href="' . esc_url( $filter_url ) . '" aria-label="' . esc_attr( $link_aria_label ) . '">' . count( $user_teams ) . '</a>'
					);

					$row_actions     = array(
						'ld-post-filter' => '<a href="' . esc_url( $filter_url ) . '" aria-label="' . esc_attr( $link_aria_label ) . '">' . esc_html__( 'filter', 'ebox' ) . '</a>',
					);
					$column_content .= $this->list_table_row_actions( $row_actions );
				}
			}

			if ( current_user_can( 'edit_courses' ) ) {
				$user_courses = ebox_user_get_enrolled_courses( $user_id );
				if ( empty( $user_courses ) ) {
					$user_courses = array();
				}

				if ( ! empty( $user_courses ) ) {
					$filter_url = add_query_arg(
						array(
							'post_type' => ebox_get_post_type_slug( 'course' ),
							'user_id'   => $user_id,
						),
						admin_url( 'edit.php' )
					);

					if ( ! empty( $column_content ) ) {
						$column_content .= '<br />';
					}

					$link_aria_label = sprintf(
						// translators: placeholder: Courses, User Nicename.
						esc_html_x( 'Filter %1$s by user "%2$s"', 'placeholder: Courses, User Nicename', 'ebox' ),
						ebox_Custom_Label::get_label( 'courses' ),
						get_user_by( 'ID', $user_id )->display_name
					);

					$column_content .= sprintf(
						// translators: placeholder: Courses, filter Courses by user URL.
						esc_html_x( 'Total %1$s: %2$s', 'placeholder: Courses, filter Courses by user URL', 'ebox' ),
						ebox_Custom_Label::get_label( 'courses' ),
						'<a href="' . esc_url( $filter_url ) . '" aria-label="' . esc_attr( $link_aria_label ) . '">' . count( $user_courses ) . '</a>'
					);

					$row_actions     = array(
						'ld-post-filter' => '<a href="' . esc_url( $filter_url ) . '" aria-label="' . esc_attr( $link_aria_label ) . '">' . esc_html__( 'filter', 'ebox' ) . '</a>',
					);
					$column_content .= $this->list_table_row_actions( $row_actions );
				}
			}

			return $column_content;
		}

		// End of functions.
	}
}
new ebox_Admin_Users_Listing();
