<?php
/**
 * ebox Data Upgrades for Course Access List.
 *
 * @since 3.1.0
 * @package ebox\Data_Upgrades
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Admin_Data_Upgrades' ) ) && ( ! class_exists( 'ebox_Admin_Data_Upgrades_Course_Access_List_Convert' ) ) ) {

	/**
	 * Class ebox Data Upgrades for Course Access List.
	 *
	 * @since 3.1.0
	 * @uses ebox_Admin_Data_Upgrades
	 */
	class ebox_Admin_Data_Upgrades_Course_Access_List_Convert extends ebox_Admin_Data_Upgrades {

		/**
		 * Protected constructor for class
		 *
		 * @since 3.1.0
		 */
		protected function __construct() {
			$this->data_slug = 'course-access-lists-convert';
			parent::__construct();
			parent::register_upgrade_action();
		}

		/**
		 * Show data upgrade row for this instance.
		 *
		 * @since 3.1.0
		 */
		public function show_upgrade_action() {

			/**
			 * Don't show the data upgrade panel after the initial run.
			 */
			if ( true !== ebox_use_legacy_course_access_list() ) {
				return;
			}
			?>
			<tr id="ebox-data-upgrades-container-<?php echo esc_attr( $this->data_slug ); ?>" class="ebox-data-upgrades-container">
				<td class="ebox-data-upgrades-button-container">
					<button class="ebox-data-upgrades-button button button-primary" data-nonce="<?php echo esc_attr( wp_create_nonce( 'ebox-data-upgrades-' . $this->data_slug . '-' . get_current_user_id() ) ); ?>" data-slug="<?php echo esc_attr( $this->data_slug ); ?>">
					<?php
						esc_html_e( 'Upgrade', 'ebox' );
					?>
					</button>
				</td>
				<td class="ebox-data-upgrades-status-container">
					<span class="ebox-data-upgrades-name">
					<?php
					printf(
						// translators: placeholder: Course.
						esc_html_x( 'Convert %s Access Lists', 'placeholder: Course', 'ebox' ),
						ebox_Custom_Label::get_label( 'course' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
					);
					?>
					</span>
					<p>
					<?php
						printf(
							// translators: placeholder: course.
							esc_html_x( 'This convert the %s access list setting to user meta where it can be managed more efficiently. (Optional)', 'placeholder: course', 'ebox' ),
							ebox_get_custom_label_lower( 'course' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
						);
					?>
					</p>
					<p class="description"><?php echo esc_html( $this->get_last_run_info() ); ?></p>

					<?php
					$show_progress        = false;
					$this->transient_key  = $this->data_slug;
					$this->transient_data = $this->get_transient( $this->transient_key );
					if ( ! empty( $this->transient_data ) ) {
						if ( isset( $this->transient_data['result_count'] ) ) {
							$this->transient_data['result_count'] = intval( $this->transient_data['result_count'] );
						} else {
							$this->transient_data['result_count'] = 0;
						}
						if ( isset( $this->transient_data['total_count'] ) ) {
							$this->transient_data['total_count'] = intval( $this->transient_data['total_count'] );
						} else {
							$this->transient_data['total_count'] = 0;
						}

						if ( ( ! empty( $this->transient_data['result_count'] ) ) && ( ! empty( $this->transient_data['total_count'] ) ) && ( $this->transient_data['result_count'] != $this->transient_data['total_count'] ) ) {

							$show_progress = true;
							?>
							<p id="ebox-data-upgrades-continue-<?php echo esc_attr( $this->data_slug ); ?>" class="ebox-data-upgrades-continue"><input type="checkbox" name="ebox-data-upgrades-continue" value="1" /> <?php esc_html_e( 'Continue previous upgrade processing?', 'ebox' ); ?></p>
							<?php
						}
					}

					$progress_style       = 'display:none;';
					$progress_meter_style = '';
					$progress_label       = '';
					$progress_slug        = '';

					if ( true === $show_progress ) {
						$progress_style = '';
						$data           = $this->transient_data;
						$data           = $this->build_progress_output( $data );
						if ( ( isset( $data['progress_percent'] ) ) && ( ! empty( $data['progress_percent'] ) ) ) {
							$progress_meter_style = 'width: ' . $data['progress_percent'] . '%';
						}

						if ( ( isset( $data['progress_label'] ) ) && ( ! empty( $data['progress_label'] ) ) ) {
							$progress_label = $data['progress_label'];
						}

						if ( ( isset( $data['progress_slug'] ) ) && ( ! empty( $data['progress_slug'] ) ) ) {
							$progress_slug = 'progress-label-' . $data['progress_slug'];
						}
					}
					?>
					<div style="<?php echo esc_attr( $progress_style ); ?>" class="meter ebox-data-upgrades-status">
						<div class="progress-meter">
							<span class="progress-meter-image" style="<?php echo esc_attr( $progress_meter_style ); ?>"></span>
						</div>
						<div class="progress-label <?php echo esc_attr( $progress_slug ); ?>"><?php echo esc_attr( $progress_label ); ?></div>
					</div>
				</td>
			</tr>
			<?php
		}

		/**
		 * Class method for the AJAX update logic
		 * This function will determine what users need to be converted. Then the course and quiz functions
		 * will be called to convert each individual user data set.
		 *
		 * @since 3.1.0
		 *
		 * @param  array $data Post data from AJAX call.
		 * @return array $data Post data from AJAX call
		 */
		public function process_upgrade_action( $data = array() ) {
			global $wpdb;

			$this->init_process_times();

			if ( ( isset( $data['nonce'] ) ) && ( ! empty( $data['nonce'] ) ) ) {
				if ( ( wp_verify_nonce( $data['nonce'], 'ebox-data-upgrades-' . $this->data_slug . '-' . get_current_user_id() ) ) && ( current_user_can( ebox_ADMIN_CAPABILITY_CHECK ) ) ) {
					$this->transient_key = $this->data_slug;

					if ( ( isset( $data['init'] ) ) && ( true == $data['init'] ) ) {
						unset( $data['init'] );

						if ( ( ! isset( $data['continue'] ) ) || ( 'true' != $data['continue'] ) ) {
							/**
							 * Transient_data is used to store the local server state information and will
							 * saved in a transient type options variable.
							 */
							$this->transient_data = array();
							// Hold the number of completed/processed items.
							$this->transient_data['result_count']     = 0;
							$this->transient_data['current_course']   = array();
							$this->transient_data['progress_started'] = time();
							$this->transient_data['progress_user']    = get_current_user_id();

							$this->query_items();
						} else {
							$this->transient_data = $this->get_transient( $this->transient_key );
						}

						$this->set_option_cache( $this->transient_key, $this->transient_data );
					} else {
						$this->transient_data = $this->get_transient( $this->transient_key );
						if ( ( ! isset( $this->transient_data['process_courses'] ) ) || ( empty( $this->transient_data['process_courses'] ) ) ) {
							$this->query_items();
						}

						if ( ( isset( $this->transient_data['process_courses'] ) ) && ( ! empty( $this->transient_data['process_courses'] ) ) ) {
							foreach ( $this->transient_data['process_courses'] as $course_idx => $course_id ) {
								$course_id = intval( $course_id );
								if ( ( ! isset( $this->transient_data['current_course'][ $course_id ] ) ) || ( empty( $this->transient_data['current_course'][ $course_id ] ) ) ) {
									$this->transient_data['current_course'][ $course_id ] = array();
								}

								$course_complete = $this->process_course_access_list( $course_id );
								if ( true === $course_complete ) {
									$this->transient_data['current_course'] = 0;
									unset( $this->transient_data['process_courses'][ $course_idx ] );
									$this->transient_data['result_count']   = (int) $this->transient_data['result_count'] + 1;
									$this->transient_data['current_course'] = array();
								}

								$this->set_option_cache( $this->transient_key, $this->transient_data );

								if ( $this->out_of_timer() ) {
									break;
								}
							}
						}
					}
				}
			}

			$data = $this->build_progress_output( $data );

			// If we are at 100% then we update the internal data settings so other parts of LD know the upgrade has been run.
			if ( ( isset( $data['progress_percent'] ) ) && ( 100 == $data['progress_percent'] ) ) {

				$this->set_last_run_info( $data );
				$data['last_run_info'] = $this->get_last_run_info();

				$this->remove_transient( $this->transient_key );
			}

			return $data;
		}

		/**
		 * Common function to query needed items.
		 *
		 * @since 3.1.0
		 *
		 * @param boolean $increment_paged default true to increment paged.
		 */
		private function query_items( $increment_paged = true ) {
			// Initialize or increment the current paged or items.
			if ( ! isset( $this->transient_data['paged'] ) ) {
				$this->transient_data['paged'] = 1;
			} else {
				if ( true === $increment_paged ) {
					$this->transient_data['paged'] = (int) $this->transient_data['paged'] + 1;
				}
			}

			$this->transient_data['query_args'] = array(
				'post_type'      => 'ebox-courses',
				'post_status'    => 'any',
				'fields'         => 'ids',
				'posts_per_page' => ebox_LMS_DEFAULT_DATA_UPGRADE_BATCH_SIZE, // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page
				'paged'          => $this->transient_data['paged'],
			);

			/**
			 * Filters data upgrade query arguments.
			 *
			 * @since 2.6.0
			 *
			 * @param array  $query_args An array of query arguments.
			 * @param string $data_slug  Data Slug used to identify each instance.
			 */
			$this->transient_data['query_args'] = apply_filters( 'ebox_data_upgrade_query', $this->transient_data['query_args'], $this->data_slug );
			$courses_query                      = new WP_Query( $this->transient_data['query_args'] );
			if ( is_a( $courses_query, 'WP_Query' ) ) {
				$this->transient_data['total_count']     = intval( $courses_query->found_posts );
				$this->transient_data['process_courses'] = $courses_query->posts;
			}
		}

		/**
		 * Common function to build the returned data progress output.
		 *
		 * @since 3.1.0
		 *
		 * @param array $data Array of existing data elements.
		 * @return array or data.
		 */
		private function build_progress_output( $data = array() ) {
			if ( isset( $this->transient_data['result_count'] ) ) {
				$data['result_count'] = intval( $this->transient_data['result_count'] );
			} else {
				$data['result_count'] = 0;
			}

			if ( isset( $this->transient_data['total_count'] ) ) {
				$data['total_count'] = intval( $this->transient_data['total_count'] );
			} else {
				$data['total_count'] = 0;
			}

			if ( ! empty( $data['total_count'] ) ) {
				$data['progress_percent'] = ( $data['result_count'] / $data['total_count'] ) * 100;
			} else {
				$data['progress_percent'] = 0;
			}

			if ( 100 == $data['progress_percent'] ) {
				$progress_status       = __( 'Complete', 'ebox' );
				$data['progress_slug'] = 'complete';
			} else {
				if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
					$progress_status       = __( 'In Progress', 'ebox' );
					$data['progress_slug'] = 'in-progress';
				} else {
					$progress_status       = __( 'Incomplete', 'ebox' );
					$data['progress_slug'] = 'in-complete';
				}
			}

			$data['progress_label'] = sprintf(
				// translators: placeholders: result count, total count, Courses.
				esc_html_x( '%1$s: %2$d of %3$d %4$s', 'placeholders: progress status, result count, total count, Courses', 'ebox' ),
				$progress_status,
				$data['result_count'],
				$data['total_count'],
				ebox_get_custom_label( 'courses' )
			);

			if ( ( isset( $this->transient_data['current_course'] ) ) && ( ! empty( $this->transient_data['current_course'] ) ) ) {
				foreach ( $this->transient_data['current_course'] as $current_course_id => $current_course_data ) {
					$course_total_users = 0;
					if ( isset( $current_course_data['course_total_users'] ) ) {
						$course_total_users = absint( $current_course_data['course_total_users'] );
					}

					$course_access_list_new = 0;
					if ( isset( $current_course_data['course_access_list_new'] ) ) {
						$course_access_list_new = count( $current_course_data['course_access_list_new'] );
					}

					$data['progress_label'] .= ' - ' . sprintf(
						// translators: placeholders: placeholders: Course title, users processed, users total.
						esc_html_x( '%1$s: %2$d of %3$d users processed', 'placeholders: Course title, users processed, users total', 'ebox' ),
						get_the_title( $current_course_id ),
						$course_access_list_new,
						$course_total_users
					);
				}
			}
			return $data;
		}

		/**
		 * Convert single course access list.
		 *
		 * @since 3.1.0
		 *
		 * @param int $course_id Course ID of post to convert.
		 *
		 * @return boolean true if complete, false if not.
		 */
		private function process_course_access_list( $course_id = 0 ) {
			$course_id = absint( $course_id );

			if ( ( ! isset( $this->transient_data['current_course'][ $course_id ]['course_access_list'] ) ) || ( empty( $this->transient_data['current_course'][ $course_id ]['course_access_list'] ) ) ) {
				$this->transient_data['current_course'][ $course_id ]['course_access_list']     = array();
				$this->transient_data['current_course'][ $course_id ]['course_access_list_new'] = array();

				$course_access_list = $this->get_course_access_list( $course_id );
				if ( ! empty( $course_access_list ) ) {
					$this->transient_data['current_course'][ $course_id ]['course_access_list'] = $course_access_list;
				}
				$this->transient_data['current_course'][ $course_id ]['course_total_users'] = count( $course_access_list );
			}

			if ( ( ! empty( $this->transient_data['current_course'][ $course_id ]['course_access_list'] ) ) && ( ! empty( $this->transient_data['current_course'][ $course_id ]['course_access_list'] ) ) ) {
				foreach ( $this->transient_data['current_course'][ $course_id ]['course_access_list'] as $idx => $user_id ) {
					if ( $this->out_of_timer() ) {
						return false;
					}

					$user_id = absint( $user_id );
					$user    = get_user_by( 'id', $user_id );
					if ( ! is_a( $user, 'WP_User' ) ) {
						ebox_report_clear_user_activity_by_types( $user_id );
					} else {

						$course_activity = $this->get_course_activity_access_for_user( $course_id, $user_id );

						if ( ! empty( $course_activity ) ) {
							$course_activity['changed']     = false;
							$course_activity['activity_id'] = absint( $course_activity['activity_id'] );
							$course_activity['user_id']     = absint( $course_activity['user_id'] );
							$course_activity['post_id']     = absint( $course_activity['post_id'] );
							$course_activity['course_id']   = absint( $course_activity['course_id'] );
							if ( ( empty( $course_activity['course_id'] ) ) || ( $course_activity['course_id'] !== $course_activity['post_id'] ) ) {
								$course_activity['course_id'] = $course_activity['post_id'];
								$course_activity['changed']   = true;
							}
							$course_activity['activity_started']   = absint( $course_activity['activity_started'] );
							$course_activity['activity_completed'] = absint( $course_activity['activity_completed'] );
							$course_activity['activity_updated']   = absint( $course_activity['activity_updated'] );

						} else {
							$course_activity['changed']            = true;
							$course_activity['activity_type']      = 'access';
							$course_activity['user_id']            = absint( $user_id );
							$course_activity['post_id']            = absint( $course_id );
							$course_activity['course_id']          = absint( $course_id );
							$course_activity['activity_started']   = 0;
							$course_activity['activity_completed'] = 0;
							$course_activity['activity_updated']   = 0;
						}

						$user_meta_course_access = get_user_meta( $user_id, 'course_' . $course_id . '_access_from', true );
						$user_meta_course_access = absint( $user_meta_course_access );

						$user_meta_course_expire = get_user_meta( $user_id, 'ebox_course_expired_' . $course_id, true );
						$user_meta_course_expire = absint( $user_meta_course_expire );

						if ( empty( $user_meta_course_expire ) ) {
							if ( ! empty( $user_meta_course_access ) ) {
								if ( $course_activity['activity_started'] !== $user_meta_course_access ) {
									$course_activity['activity_started'] = $user_meta_course_access;
									if ( empty( $course_activity['activity_updated'] ) ) {
										$course_activity['activity_updated'] = $course_activity['activity_started'];
									}
									$course_activity['changed'] = true;
								}
							} else {
								$user_meta_course_access = time();
								update_user_meta( $user_id, 'course_' . $course_id . '_access_from', $user_meta_course_access );
								$course_activity['activity_started'] = $user_meta_course_access;
								if ( empty( $course_activity['activity_updated'] ) ) {
									$course_activity['activity_updated'] = $course_activity['activity_started'];
								}
								$course_activity['changed'] = true;
							}
						} else {
							if ( $course_activity['activity_completed'] !== $user_meta_course_expire ) {
								$course_activity['activity_completed'] = $user_meta_course_expire;
								if ( empty( $course_activity['activity_updated'] ) ) {
									$course_activity['activity_updated'] = $course_activity['activity_completed'];
								}
								$course_activity['changed'] = true;
							}

							// If course access has expired and we still have the user meta course access we need to remove it.
							if ( ! empty( $user_meta_course_access ) ) {
								delete_user_meta( $user_id, 'course_' . $course_id . '_access_from' );
							}
						}

						if ( true === $course_activity['changed'] ) {
							$course_activity['data_upgrade'] = true;
							ebox_update_user_activity( $course_activity );
						}

						// And the processed user to our master list. This will update the 'course_access_list' meta when done.
						$this->transient_data['current_course'][ $course_id ]['course_access_list_new'][] = $user_id;
					}
					unset( $this->transient_data['current_course'][ $course_id ]['course_access_list'][ $idx ] );
				}
			}

			if ( ( isset( $this->transient_data['current_course'][ $course_id ]['course_access_list_new'] ) ) && ( ! empty( $this->transient_data['current_course'][ $course_id ]['course_access_list_new'] ) ) ) {
				$course_access_list_new = ebox_convert_course_access_list( $this->transient_data['current_course'][ $course_id ]['course_access_list_new'] );
				ebox_update_setting( $course_id, 'course_access_list', $course_access_list_new );

				$this->remove_course_activity_access( $course_id, $this->transient_data['current_course'][ $course_id ]['course_access_list_new'] );
			} else {
				delete_post_meta( $course_id, 'course_access_list_new' );
			}

			return true;
		}

		/**
		 * Get course access list for Course
		 *
		 * @since 3.1.0
		 *
		 * @param integer $course_id Course ID.
		 */
		private function get_course_access_list( $course_id = 0 ) {
			$course_access_list = array();

			if ( ! empty( $course_id ) ) {
				$course_access_list_setting = ebox_get_setting( $course_id, 'course_access_list' );
				$course_access_list_setting = ebox_convert_course_access_list( $course_access_list_setting, true );

				$course_access_list_post_meta = get_post_meta( $course_id, 'course_access_list', true );
				$course_access_list_post_meta = ebox_convert_course_access_list( $course_access_list_post_meta, true );

				$course_access_list_user_meta = ebox_get_course_users_access_from_meta( $course_id );
				$course_access_list_user_meta = ebox_convert_course_access_list( $course_access_list_user_meta, true );

				$course_access_list = array_merge( $course_access_list_setting, $course_access_list_post_meta, $course_access_list_user_meta );
				$course_access_list = array_unique( $course_access_list );
				if ( ! empty( $course_access_list ) ) {
					asort( $course_access_list );
				}
			}
			return $course_access_list;
		}

		/**
		 * Get course activity access for user
		 *
		 * @since 3.1.0
		 *
		 * @param integer $course_id Course ID.
		 * @param integer $user_id   User ID.
		 *
		 * @return array activity row
		 */
		private function get_course_activity_access_for_user( $course_id = 0, $user_id = 0 ) {
			global $wpdb;
			if ( ( ! empty( $course_id ) ) && ( ! empty( $user_id ) ) ) {
				$sql_str = $wpdb->prepare( 'SELECT * FROM ' . esc_sql( LDLMS_DB::get_table_name( 'user_activity' ) ) . ' WHERE activity_type=%s AND user_id = %d AND ( post_id = %d OR course_id = %d )', 'access', $user_id, $course_id, $course_id );

				//phpcs:ignore: WordPress.DB.PreparedSQL.NotPrepared
				$activity_row = $wpdb->get_row( $sql_str, ARRAY_A ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
				return $activity_row;
			}

			return array();
		}

		/**
		 * Remove course activity access for users
		 *
		 * @since 3.1.0
		 *
		 * @param integer $course_id Course ID.
		 * @param array   $user_ids  User IDs.
		 */
		private function remove_course_activity_access( $course_id = 0, $user_ids = array() ) {
			global $wpdb;

			if ( ( ! empty( $course_id ) ) && ( ! empty( $user_ids ) ) ) {
				$user_ids = array_map( 'absint', $user_ids );
				$sql_str  = $wpdb->prepare( 'SELECT activity_id, user_id FROM ' . esc_sql( LDLMS_DB::get_table_name( 'user_activity' ) ) . ' WHERE activity_type=%s AND ( post_id = %d OR course_id = %d )', 'access', $course_id, $course_id );

				$activity = $wpdb->get_results( $sql_str ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
				if ( ! empty( $activity ) ) {
					$activity_ids = array();
					foreach ( $activity as $item ) {
						if ( in_array( absint( $item->user_id ), $user_ids, true ) !== false ) {
							$activity_ids[] = $item->activity_id;
						}
					}
					if ( ! empty( $activity_ids ) ) {
						$activity_ids_chunks = array_chunk( $activity_ids, 100 );
						foreach ( $activity_ids_chunks as $activity_ids_chunk ) {
							ebox_report_clear_by_activity_ids( $activity_ids_chunk );
						}
					}
				}
			}
		}

		// End of functions.
	}
}

add_action(
	'ebox_data_upgrades_init',
	function() {
		ebox_Admin_Data_Upgrades_Course_Access_List_Convert::add_instance();
	}
);
