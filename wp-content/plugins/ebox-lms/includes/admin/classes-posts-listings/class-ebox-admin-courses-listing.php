<?php
/**
 * ebox Courses (ebox-courses) Posts Listing.
 *
 * @since 2.6.0
 * @package ebox\Course\Listing
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Admin_Posts_Listing' ) ) && ( ! class_exists( 'ebox_Admin_Courses_Listing' ) ) ) {

	/**
	 * Class ebox Courses (ebox-courses) Posts Listing.
	 *
	 * @since 2.6.0
	 * @uses ebox_Admin_Posts_Listing
	 */
	class ebox_Admin_Courses_Listing extends ebox_Admin_Posts_Listing {

		/**
		 * Public constructor for class
		 *
		 * @since 3.2.3
		 */
		public function __construct() {
			$this->post_type = ebox_get_post_type_slug( 'course' );

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
				'user_id'        => array(
					'type'                     => 'user',
					'show_all_value'           => '',
					'show_all_label'           => esc_html__( 'All Users', 'ebox' ),
					'selector_filter_function' => array( $this, 'selector_filter_for_author' ),
					'selector_value_function'  => array( $this, 'selector_value_for_author' ),
				),
				'team_id'       => array(
					'type'                     => 'post_type',
					'post_type'                => ebox_get_post_type_slug( 'team' ),
					'show_all_value'           => '',
					'show_all_label'           => sprintf(
						// translators: placeholder: Teams.
						esc_html_x( 'All %s', 'placeholder: Teams', 'ebox' ),
						ebox_Custom_Label::get_label( 'teams' )
					),
					'show_empty_value'         => 'empty',
					'show_empty_label'         => sprintf(
						// translators: placeholder: Team.
						esc_html_x( '-- No %s --', 'placeholder: Team', 'ebox' ),
						ebox_Custom_Label::get_label( 'team' )
					),
					'listing_query_function'   => array( $this, 'listing_filter_by_team' ),
					'selector_filter_function' => array( $this, 'selector_filter_for_team' ),
				),
				'certificate_id' => array(
					'type'                   => 'post_type',
					'post_type'              => ebox_get_post_type_slug( 'certificate' ),
					'show_all_value'         => '',
					'show_all_label'         => esc_html__( 'All Certificates', 'ebox' ),
					'show_empty_value'       => 'empty',
					'show_empty_label'       => esc_html__( '-- No Certificate --', 'ebox' ),
					'listing_query_function' => array( $this, 'listing_filter_by_certificate' ),
				),
				'price_type'     => array(
					'type'                   => 'early',
					'display'                => array( $this, 'show_selector_price_type' ),
					'show_all_value'         => '',
					'show_all_label'         => esc_html__( 'All Price Types', 'ebox' ),
					'options'                => array(
						'open'      => esc_html__( 'Open', 'ebox' ),
						'free'      => esc_html__( 'Free', 'ebox' ),
						'paynow'    => esc_html__( 'Buy now', 'ebox' ),
						'subscribe' => esc_html__( 'Recurring', 'ebox' ),
						'closed'    => esc_html__( 'Closed', 'ebox' ),
					),
					'listing_query_function' => array( $this, 'filter_listing_by_price_type' ),
					'select2'                => true,
					'select2_fetch'          => false,
				),
			);

			$this->columns = array(
				'price_type' => array(
					'label'    => esc_html__( 'Price Type', 'ebox' ),
					'after'    => 'title',
					'display'  => array( $this, 'show_column_price_type' ),
					'required' => true,
				),
			);

			// If Team Leader remove the selector empty option.
			if ( ebox_is_team_leader_user() ) {
				$gl_manage_teams_capabilities = ebox_get_team_leader_manage_teams();
				if ( 'advanced' !== $gl_manage_teams_capabilities ) {
					if ( isset( $this->selectors['team_id'] ) ) { // @phpstan-ignore-line
						if ( isset( $this->selectors['team_id']['show_empty_value'] ) ) { // @phpstan-ignore-line
							unset( $this->selectors['team_id']['show_empty_value'] );
						}
						if ( isset( $this->selectors['team_id']['show_empty_label'] ) ) { // @phpstan-ignore-line
							unset( $this->selectors['team_id']['show_empty_label'] );
						}
					}
				}
			}

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
				parent::on_load_listing();

				add_filter( 'ebox_listing_table_query_vars_filter', array( $this, 'listing_table_query_vars_filter_courses' ), 30, 3 );

				/**
				 * Convert the Course Post Meta items.
				 *
				 * @since 3.4.1
				 */
				$ld_data_upgrade_course_post_meta = ebox_Admin_Data_Upgrades::get_instance( 'ebox_Admin_Data_Upgrades_Course_Post_Meta' );
				if ( ( $ld_data_upgrade_course_post_meta ) && ( is_a( $ld_data_upgrade_course_post_meta, 'ebox_Admin_Data_Upgrades_Course_Post_Meta' ) ) ) {
					$ld_data_upgrade_course_post_meta->process_post_meta( true );
				}
			}
		}

		/**
		 * Listing table query vars
		 *
		 * @since 3.2.3
		 *
		 * @param array  $q_vars    Array of query vars.
		 * @param string $post_type Post Type being displayed.
		 * @param array  $query     Main Query.
		 */
		public function listing_table_query_vars_filter_courses( $q_vars, $post_type, $query ) {
			$user_selector = $this->get_selector( 'user_id' );
			if ( ( is_array( $user_selector ) ) && ( isset( $user_selector['selected'] ) ) && ( ! empty( $user_selector['selected'] ) ) ) {
				$user_course_ids = ebox_user_get_enrolled_courses( $user_selector['selected'], array(), true );
				if ( ! empty( $user_course_ids ) ) {
					$q_vars['post__in'] = $user_course_ids;
				} else {
					$q_vars['post__in'] = array( 0 );
				}
			}

			return $q_vars;
		}

		/**
		 * Add Course Builder link to Courses row action array.
		 *
		 * @since 3.0.0
		 *
		 * @param array   $row_actions Existing Row actions for course.
		 * @param WP_Post $post Course Post object for current row.
		 *
		 * @return array $row_actions
		 */
		public function post_row_actions( $row_actions = array(), $post = null ) {
			if ( $this->post_type_check() ) {
				$row_actions = parent::post_row_actions( $row_actions, $post );

				if ( ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Courses_Builder', 'enabled' ) == 'yes' ) && ( current_user_can( 'edit_post', $post->ID ) ) && ( ! isset( $row_actions['ld-course-builder'] ) ) ) {
					/**
					 * Filters whether to show course builder row actions or not.
					 *
					 * @since 2.5.0
					 *
					 * @param boolean      $show_row_actions Whether to show row actions.
					 * @param WP_Post|null $course_post      Course post object.
					 */
					if ( apply_filters( 'ebox_show_course_builder_row_actions', true, $post ) === true ) {
						$course_label = sprintf(
							// translators: placeholder: Course.
							esc_html_x( 'Use %s Builder', 'placeholder: Course', 'ebox' ),
							ebox_Custom_Label::get_label( 'course' )
						);

						$row_actions['ld-course-builder'] = sprintf(
							'<a href="%s" rel="bookmark" aria-label="%s">%s</a>',
							add_query_arg(
								array(
									'currentTab' => 'ebox_course_builder',
								),
								get_edit_post_link( $post->ID )
							),
							esc_attr( $course_label ),
							esc_html__( 'Builder', 'ebox' )
						);
					}
				}
			}

			return $row_actions;
		}

		/**
		 * Show selector for price type.
		 *
		 * @since 3.4.1
		 *
		 * @param array $selector Selector args.
		 */
		protected function show_selector_price_type( $selector = array() ) {

			if ( ( isset( $selector['options'] ) ) && ( ! empty( $selector['options'] ) ) ) {
				$this->show_selector_start( $selector );
				$this->show_selector_all_option( $selector );

				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				if ( ( isset( $_GET['price_type'] ) ) && ( ! empty( $_GET['price_type'] ) ) ) {
					// phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$selected_price_type = sanitize_text_field( wp_unslash( $_GET['price_type'] ) );
				} else {
					$selected_price_type = '';
				}

				foreach ( $selector['options'] as $price_type_key => $price_type_label ) {
					echo '<option value="' . esc_attr( $price_type_key ) . '" ' . selected( $price_type_key, $selected_price_type, false ) . '>' . esc_attr( $price_type_label ) . '</option>';
				}

				$this->show_selector_end( $selector );
			}
		}

		/**
		 * This function fill filter the table listing items by the selected Price Type.
		 * Called via 'parse_query' filter from WP.
		 *
		 * @since 3.4.2
		 *
		 * @param  object $q_vars Query vars used for the table listing.
		 * @param  array  $selector Array of attributes used to display the filter selector.
		 *
		 * @return object $q_vars.
		 */
		protected function filter_listing_by_price_type( $q_vars, $selector ) {
			if ( ( isset( $selector['selected'] ) ) && ( ! empty( $selector['selected'] ) ) ) {
				if ( ebox_post_meta_processed( $this->post_type ) ) {
					if ( ! isset( $q_vars['meta_query'] ) ) {
						$q_vars['meta_query'] = array(); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
					}

					$q_vars['meta_query'][] = array(
						'key'   => '_ld_price_type',
						'value' => $selector['selected'],
					);
				} else {
					$post_ids = ebox_get_posts_by_price_type( $this->post_type, $selector['selected'] );
					if ( ! empty( $post_ids ) ) {
						$q_vars['post__in'] = $post_ids;
					} else {
						$q_vars['post__in'] = array( 0 );
					}
				}
			}

			return $q_vars;
		}

		/**
		 * Show Price Type column.
		 *
		 * @since 3.4.1
		 *
		 * @param int    $post_id     The Step post ID shown.
		 * @param string $column_name Column name/slug being processed.
		 */
		protected function show_column_price_type( $post_id = 0, $column_name = '' ) {
			if ( ( ! empty( $post_id ) ) && ( 'price_type' === $column_name ) ) {
				$price_type_key = ebox_get_setting( $post_id, 'course_price_type' );
				if ( ! empty( $price_type_key ) ) {
					if ( isset( $this->selectors[ $column_name ]['options'][ $price_type_key ] ) ) {
						$price_type_label = $this->selectors[ $column_name ]['options'][ $price_type_key ];

						$row_actions = array();

						$filter_url = add_query_arg( 'price_type', $price_type_key, $this->get_clean_filter_url() );

						$link_aria_label = esc_html__( 'Filter listing by Price type', 'ebox' );

						echo '<a href="' . esc_url( $filter_url ) . '" aria-label="' . esc_attr( $link_aria_label ) . '">' . esc_attr( $price_type_label ) . '</a>';
						$row_actions['ld-post-filter'] = '<a href="' . esc_url( $filter_url ) . '" aria-label="' . esc_attr( $link_aria_label ) . '">' . esc_html__( 'filter', 'ebox' ) . '</a>';
						echo $this->list_table_row_actions( $row_actions ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Need to output HTML
					}
				}
			}
		}

		// End of functions.
	}
}
new ebox_Admin_Courses_Listing();
