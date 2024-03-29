<?php
/**
 * ebox Data Upgrades for User Courses.
 *
 * @since 2.3.0
 * @package ebox\Data_Upgrades
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Admin_Data_Upgrades' ) ) && ( ! class_exists( 'ebox_Admin_Data_Upgrades_User_Meta_Courses' ) ) ) {

	/**
	 * Class ebox Data Upgrades for User Courses.
	 *
	 * @since 2.3.0
	 * @uses ebox_Admin_Data_Upgrades
	 */
	class ebox_Admin_Data_Upgrades_User_Meta_Courses extends ebox_Admin_Data_Upgrades {

		/**
		 * Protected constructor for class
		 */
		protected function __construct() {
			$this->data_slug = 'user-meta-courses';
			parent::__construct();
			parent::register_upgrade_action();
		}

		/**
		 * Show data upgrade row for this instance.
		 */
		public function show_upgrade_action() {
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
						esc_html_x( 'Upgrade User %s Data', 'placeholder: Course', 'ebox' ),
						ebox_Custom_Label::get_label( 'course' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
					);
					?>
					</span>
					<p>
					<?php
					printf(
						// translators: placeholder: Course, course.
						esc_html_x( 'This upgrade will sync your existing user data for %s into a new database table for better reporting. (Required)', 'placeholder: course', 'ebox' ),
						esc_html( ebox_get_custom_label_lower( 'course' ) )
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
							<p id="ebox-data-upgrades-continue-
							<?php
							echo esc_attr( $this->data_slug );
							?>
							" class="ebox-data-upgrades-continue"><input type="checkbox" name="ebox-data-upgrades-continue" value="1" /> <?php esc_html_e( 'Continue previous upgrade processing?', 'ebox' ); ?></p>
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
		 * @param  array $data Post data from AJAX call.
		 * @return array $data Post data from AJAX call.
		 */
		public function process_upgrade_action( $data = array() ) {
			global $wpdb;

			$this->init_process_times();

			if ( ( isset( $data['nonce'] ) ) && ( ! empty( $data['nonce'] ) ) ) {
				if ( ( wp_verify_nonce( $data['nonce'], 'ebox-data-upgrades-' . $this->data_slug . '-' . get_current_user_id() ) ) && ( current_user_can( ebox_ADMIN_CAPABILITY_CHECK ) ) ) {
					$this->transient_key = $this->data_slug;

					if ( ( isset( $data['init'] ) ) && ( '1' === $data['init'] ) ) {
						unset( $data['init'] );

						if ( ( ! isset( $data['continue'] ) ) || ( 'true' != $data['continue'] ) ) {
							ebox_activity_clear_mismatched_users();
							ebox_activity_clear_mismatched_posts();

							/**
							 * Transient_data is used to store the local server state information and will
							 * saved in a transient type options variable.
							 */
							$this->transient_data = array();
							// Hold the number of completed/processed items.
							$this->transient_data['result_count']     = 0;
							$this->transient_data['current_user']     = array();
							$this->transient_data['progress_started'] = time();
							$this->transient_data['progress_user']    = get_current_user_id();

							$this->query_items();
						} else {
							$this->transient_data = $this->get_transient( $this->transient_key );
						}
						$this->set_option_cache( $this->transient_key, $this->transient_data );
					} else {

						$this->transient_data = $this->get_transient( $this->transient_key );
						if ( ( ! isset( $this->transient_data['process_users'] ) ) || ( empty( $this->transient_data['process_users'] ) ) ) {
							$this->query_items();
						}

						if ( ( isset( $this->transient_data['process_users'] ) ) && ( ! empty( $this->transient_data['process_users'] ) ) ) {
							foreach ( $this->transient_data['process_users'] as $user_idx => $user_id ) {
								$user_id = intval( $user_id );
								if ( ( ! isset( $this->transient_data['current_user']['user_id'] ) ) || ( $this->transient_data['current_user']['user_id'] !== $user_id ) ) {
									$this->transient_data['current_user'] = array(
										'user_id'  => $user_id,
										'item_idx' => 0,
									);
								}

								$user_complete = $this->convert_user_meta_courses_progress_to_activity( intval( $user_id ) );
								if ( true === $user_complete ) {
									$this->transient_data['current_user'] = array();
									unset( $this->transient_data['process_users'][ $user_idx ] );

									if ( ! isset( $this->transient_data['result_count'] ) ) {
										$this->transient_data['result_count'] = 0;
									}
									$this->transient_data['result_count'] = (int) $this->transient_data['result_count'] + 1;
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
		 * @param boolean $increment_paged default true to increment paged.
		 */
		protected function query_items( $increment_paged = true ) {
			// Initialize or increment the current paged or items.
			if ( ! isset( $this->transient_data['paged'] ) ) {
				$this->transient_data['paged'] = 1;
			} else {
				if ( true === $increment_paged ) {
					$this->transient_data['paged'] = (int) $this->transient_data['paged'] + 1;
				}
			}

			$this->transient_data['query_args'] = array(
				'fields' => 'ID',
				'paged'  => $this->transient_data['paged'],
				'number' => ebox_LMS_DEFAULT_DATA_UPGRADE_BATCH_SIZE,
			);
			/** This filter is documented in includes/admin/classes-data-upgrades-actions/class-ebox-admin-data-upgrades-course-access-list-convert.php */
			$this->transient_data['query_args'] = apply_filters( 'ebox_data_upgrade_query', $this->transient_data['query_args'], $this->data_slug );
			$user_query                         = new WP_User_Query( $this->transient_data['query_args'] );
			if ( is_a( $user_query, 'WP_User_Query' ) ) {
				$this->transient_data['total_count']   = $user_query->get_total();
				$this->transient_data['process_users'] = $user_query->get_results();
			}
		}

		/**
		 * Common function to build the returned data progress output.
		 *
		 * @param array $data Array of existing data elements.
		 * @return array or data.
		 */
		protected function build_progress_output( $data = array() ) {
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
				// translators: placeholders: result count, total count.
				esc_html_x( '%1$s: %2$d of %3$d Users', 'placeholders: progress status, result count, total count', 'ebox' ),
				$progress_status,
				$data['result_count'],
				$data['total_count']
			);

			return $data;
		}

		/**
		 * Convert single user quiz attempts to Activity DB entries.
		 *
		 * @param int $user_id User ID of user to convert.
		 *
		 * @return boolean true if complete, false if not.
		 */
		private function convert_user_meta_courses_progress_to_activity( $user_id = 0 ) {
			global $wpdb;

			if ( ( empty( $user_id ) ) || ( ! isset( $this->transient_data['current_user']['user_id'] ) ) || ( $user_id !== $this->transient_data['current_user']['user_id'] ) ) {
				return false;
			}

			delete_user_meta( $user_id, $this->meta_key );

			if ( isset( $this->transient_data['current_user']['activity_ids'] ) ) {
				$activity_ids = $this->transient_data['current_user']['activity_ids'];
			} else {
				$activity_ids = array();
			}

			if ( ! isset( $activity_ids['last_course_id'] ) ) {
				$activity_ids['last_course_id'] = 0;
			} else {
				$activity_ids['last_course_id'] = intval( $activity_ids['last_course_id'] );
			}

			if ( ! isset( $activity_ids['existing'] ) ) {
				$activity_ids['existing'] = array();
			}
			if ( ! isset( $activity_ids['current'] ) ) {
				$activity_ids['current'] = array();
			}
			if ( ! isset( $activity_ids['course_ids_used'] ) ) {
				$activity_ids['course_ids_used'] = array();
			}

			$user_meta_courses_progress = get_user_meta( $user_id, '_ebox-course_progress', true );
			if ( ( ! empty( $user_meta_courses_progress ) ) && ( is_array( $user_meta_courses_progress ) ) ) {
				/**
				 * We sort the course progress array because we may need to save our place and
				 * need to know where we left off.
				 */
				ksort( $user_meta_courses_progress );

				foreach ( $user_meta_courses_progress as $course_id => $course_data ) {

					// Need a way to seek to a specific key starting point in an array.
					if ( $activity_ids['last_course_id'] >= $course_id ) {
						continue;
					}

					$course_post = get_post( $course_id );
					if ( ( $course_post ) && is_a( $course_post, 'WP_Post' ) ) {

						$total_activity_items    = 0;
						$user_course_completed   = (int) get_user_meta( $user_id, 'course_completed_' . $course_id, true );
						$user_course_access_from = (int) get_user_meta( $user_id, 'course_' . $course_id . '_access_from', true );

						// We replace the $course_data with the newer logic.
						$course_data = ebox_user_get_course_progress( $user_id, $course_id, 'legacy' );

						// Then loop over modules.
						if ( ( isset( $course_data['modules'] ) ) && ( ! empty( $course_data['modules'] ) ) ) {
							foreach ( $course_data['modules'] as $lesson_id => $lesson_complete ) {
								$lesson_post = get_post( $lesson_id );
								if ( ( $lesson_post ) && is_a( $lesson_post, 'WP_Post' ) ) {

									$lesson_args = array(
										'course_id'     => $course_id,
										'post_id'       => $lesson_id,
										'user_id'       => $user_id,
										'activity_type' => 'lesson',
										'data_upgrade'  => true,
										'activity_meta' => array(),
									);

									if ( true == $lesson_complete ) {
										$lesson_args['activity_status'] = true;
										if ( ! empty( $user_course_completed ) ) {
											$lesson_args['activity_completed'] = $user_course_completed;
										}
									}
									$activity_id = ebox_update_user_activity( $lesson_args );
									if ( ! empty( $activity_id ) ) {
										$activity_ids['current'][] = $activity_id;
									}

									$total_activity_items++;
								}
							}
						}

						// Then loop over Topics.
						if ( ( isset( $course_data['topics'] ) ) && ( ! empty( $course_data['topics'] ) ) ) {
							foreach ( $course_data['topics'] as $lesson_id => $modules_topics ) {
								if ( ! empty( $modules_topics ) ) {
									foreach ( $modules_topics as $topic_id => $topic_complete ) {
										$topic_post = get_post( $topic_id );
										if ( ( $topic_post ) && is_a( $topic_post, 'WP_Post' ) ) {

											$topic_args = array(
												'course_id' => $course_id,
												'post_id' => $topic_id,
												'user_id' => $user_id,
												'activity_type' => 'topic',
												'data_upgrade' => true,
												'activity_meta' => array(),
											);

											if ( true == $topic_complete ) {
												$topic_args['activity_status'] = true;
												if ( ! empty( $user_course_completed ) ) {
													$topic_args['activity_completed'] = $user_course_completed;
												}
											}

											$activity_id = ebox_update_user_activity( $topic_args );
											if ( ! empty( $activity_id ) ) {
												$activity_ids['current'][] = $activity_id;
											}
											$total_activity_items++;
										}
									}
								}
							}
						}

						if ( ! empty( $user_course_access_from ) ) {
							$activity_id = ebox_update_user_activity(
								array(
									'course_id'        => $course_id,
									'post_id'          => $course_id,
									'user_id'          => $user_id,
									'activity_type'    => 'access',
									'activity_started' => $user_course_access_from,
									'data_upgrade'     => true,
								)
							);
							if ( ! empty( $activity_id ) ) {
								$activity_ids['current'][] = $activity_id;
							}
						}

						$course_activity = ebox_get_user_activity(
							array(
								'course_id'     => $course_id,
								'post_id'       => $course_id,
								'activity_type' => 'course',
								'user_id'       => $user_id,
							)
						);

						if ( $course_activity ) {
							if ( is_object( $course_activity ) ) {
								$course_args = json_decode( wp_json_encode( $course_activity ), true );
							} elseif ( is_array( $course_activity ) ) {
								$course_args = $course_activity;
							}
						} else {
							$course_args = array(
								'course_id'     => $course_id,
								'post_id'       => $course_id,
								'activity_type' => 'course',
								'user_id'       => $user_id,
								'data_upgrade'  => true,
								'activity_meta' => array(
									'steps_total'     => intval( $course_data['total'] ),
									'steps_completed' => intval( $course_data['completed'] ),
								),
							);
						}

						if ( ( ! isset( $course_args['activity_meta'] ) ) || ( ! is_array( $course_args['activity_meta'] ) ) ) {
							$course_args['activity_meta'] = array();
						}

						if ( ! isset( $course_args['activity_meta']['steps_total'] ) ) {
							$course_args['activity_meta']['steps_total'] = intval( $course_data['total'] );
						}

						if ( ! isset( $course_args['activity_meta']['steps_completed'] ) ) {
							$course_args['activity_meta']['steps_completed'] = intval( $course_data['completed'] );
						}

						$steps_completed = intval( $course_data['completed'] );
						if ( ( ! empty( $steps_completed ) ) && ( $steps_completed >= intval( $course_data['total'] ) ) ) {
							$course_args['activity_status'] = true;

							// Finally if there is a Course Complete date we add it.
							if ( ! empty( $user_course_completed ) ) {
								$course_args['activity_completed'] = $user_course_completed;
							}
						} elseif ( ! empty( $steps_completed ) ) {
							$course_args['activity_status'] = false;
						}

						if ( isset( $course_data['last_id'] ) ) {
							if ( absint( $course_data['last_id'] ) === absint( $course_id ) ) {
								$last_activity = ebox_activity_course_get_latest_completed_step( $user_id, $course_id );
								if ( ( isset( $last_activity['post_id'] ) ) && ( absint( $course_data['last_id'] ) !== absint( $last_activity['post_id'] ) ) ) {
									$course_data['last_id'] = absint( $last_activity['post_id'] );

									// Need to update the real user's meta course progress.
									ebox_user_set_course_progress( $user_id, $course_id, $course_data );
								} else {
									$course_data['last_id'] = 0;
								}
							}

							if ( ! empty( $course_data['last_id'] ) ) {
								$course_args['activity_meta']['steps_last_id'] = intval( $course_data['last_id'] );
							}
						}

						$activity_started  = $user_course_access_from;
						$activity_earliest = ebox_activity_course_get_earliest_started( $user_id, $course_id, 0 );

						if ( ( ! empty( $activity_earliest ) ) && ( $activity_earliest > $activity_started ) ) {
							$activity_started = $activity_earliest;
						}

						if ( (int) $activity_started !== (int) $course_args['activity_started'] ) {
							$course_args['activity_started'] = (int) $activity_started;
						}

						$activity_id = ebox_update_user_activity( $course_args );
						if ( ! empty( $activity_id ) ) {
							$activity_ids['current'][] = $activity_id;
						}
					}

					$activity_ids['last_course_id']                       = $course_id;
					$activity_ids['course_ids_used'][ $course_id ]        = $course_id;
					$this->transient_data['current_user']['activity_ids'] = $activity_ids;

					if ( $this->out_of_timer() ) {
						return false;
					}
				}
			}

			/**
			 * Finally we go through the user's meta again to grab the random course access items. These
			 * would be there If the user was granted access but didn't actually start a lesson/quiz etc.
			 */
			$user_courses_access = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->prepare(
					"SELECT user_id, meta_key, meta_value as course_access_from FROM {$wpdb->usermeta} WHERE user_id = %d AND meta_key LIKE %s",
					$user_id,
					'course_%_access_from'
				)
			);

			if ( ! empty( $user_courses_access ) ) {
				foreach ( $user_courses_access as $user_course_access ) {

					if ( ( property_exists( $user_course_access, 'meta_key' ) ) && ( ! empty( $user_course_access->meta_key ) ) ) {
						$user_course_access->course_id = str_replace( 'course_', '', $user_course_access->meta_key );  // @phpstan-ignore-line
						$user_course_access->course_id = str_replace( '_access_from', '', $user_course_access->course_id );

						if ( ! isset( $activity_ids['course_ids_used'][ $user_course_access->course_id ] ) ) {

							$activity_id = ebox_update_user_activity(
								array(
									'course_id'     => $user_course_access->course_id,
									'post_id'       => $user_course_access->course_id,
									'user_id'       => $user_id,
									'activity_type' => 'access',
									'data_upgrade'  => true,
								)
							);

							if ( ! empty( $activity_id ) ) {
								$activity_ids['current'][] = $activity_id;
							}
						}
					}
				}
			}

			// Here we purge items from the Activity DB where we don't have a match to processed 'current' course items.
			$activity_ids['existing'] = ebox_report_get_activity_by_user_id( $user_id, array( 'access', 'course', 'lesson', 'topic' ) );
			if ( empty( $activity_ids['existing'] ) ) {
				$activity_ids['existing'] = array();
			}

			if ( ( ! empty( $activity_ids['existing'] ) ) && ( ! empty( $activity_ids['current'] ) ) ) {

				$activity_ids['existing'] = array_map( 'intval', $activity_ids['existing'] );
				sort( $activity_ids['existing'] );

				$activity_ids['current'] = array_map( 'intval', $activity_ids['current'] );
				sort( $activity_ids['current'] );

				$activity_ids_delete = array_diff( $activity_ids['existing'], $activity_ids['current'] );

				if ( ! empty( $activity_ids_delete ) ) {
					ebox_report_clear_by_activity_ids( $activity_ids_delete );
				}
			}

			return true;
		}
	}
}

add_action(
	'ebox_data_upgrades_init',
	function() {
		ebox_Admin_Data_Upgrades_User_Meta_Courses::add_instance();
	}
);
