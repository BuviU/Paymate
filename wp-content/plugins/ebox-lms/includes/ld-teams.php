<?php
/**
 * Team functions
 *
 * @since 2.1.0
 *
 * @package ebox\Teams
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles team email messages.
 *
 * Fires on `ebox_team_emails` AJAX action.
 *
 * @since 2.1.0
 */
function ebox_team_emails() {
	if ( ( isset( $_POST['action'] ) ) && ( 'ebox_team_emails' === $_POST['action'] ) && ( isset( $_POST['team_email_data'] ) ) && ( ! empty( $_POST['team_email_data'] ) ) ) {

		if ( ! is_user_logged_in() ) {
			exit;
		}
		$current_user = wp_get_current_user();
		if ( ( ! ebox_is_team_leader_user( $current_user->ID ) ) && ( ! ebox_is_admin_user( $current_user->ID ) ) ) {
			exit;
		}

		$team_email_data = json_decode( stripslashes( $_POST['team_email_data'] ), true );

		if ( ( ! isset( $team_email_data['team_id'] ) ) || ( empty( $team_email_data['team_id'] ) ) ) {
			die();
		}
		$team_email_data['team_id'] = intval( $team_email_data['team_id'] );

		if ( ( ! isset( $_POST['nonce'] ) ) || ( empty( $_POST['nonce'] ) ) || ( ! wp_verify_nonce( $_POST['nonce'], 'team_email_nonce_' . $team_email_data['team_id'] . '_' . $current_user->ID ) ) ) {
			die();
		}

		if ( ( ! isset( $team_email_data['email_subject'] ) ) || ( empty( $team_email_data['email_subject'] ) ) ) {
			die();
		}
		$team_email_data['email_subject'] = wp_strip_all_tags( stripcslashes( $team_email_data['email_subject'] ) );

		if ( ( ! isset( $team_email_data['email_message'] ) ) || ( empty( $team_email_data['email_message'] ) ) ) {
			die();
		}
		$team_email_data['email_message'] = wpautop( stripcslashes( $team_email_data['email_message'] ) );

		$team_admin_ids = ebox_get_teams_administrator_ids( $team_email_data['team_id'] );
		if ( in_array( $current_user->ID, $team_admin_ids, true ) === false ) {
			die();
		}

		$mail_args = array(
			'to'          => $current_user->user_email,
			'subject'     => $team_email_data['email_subject'],
			'message'     => $team_email_data['email_message'],
			'attachments' => '',
			'headers'     => array(
				'MIME-Version: 1.0',
				'content-type: text/html',
				'From: ' . $current_user->display_name . ' <' . $current_user->user_email . '>',
				'Reply-to: ' . $current_user->display_name . ' <' . $current_user->user_email . '>',
			),
		);

		$team_user_ids = ebox_get_teams_user_ids( $team_email_data['team_id'] );
		if ( ! empty( $team_user_ids ) ) {
			$email_addresses = array();
			if ( ( defined( 'ebox_GROUP_EMAIL_SINGLE' ) ) && ( true === ebox_GROUP_EMAIL_SINGLE ) ) {
				$team_email_error_message = array();
				foreach ( $team_user_ids as $user_id ) {
					$user = get_user_by( 'id', $user_id );

					$team_email_error = null;
					add_action(
						'wp_mail_failed',
						function ( $mail_error ) {
							global $team_email_error;
							$team_email_error = $mail_error; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- It is what it is.
						}
					);

					if ( $user ) {
						$mail_args['to'] = sanitize_email( $user->user_email );

						/**
						 * Filters team email user arguments.
						 *
						 * @param array $mail_args Team mail arguments.
						 */
						$mail_args = apply_filters( 'ld_team_email_users_args', $mail_args );
						if ( ! empty( $mail_args ) ) {

							/**
							 * Fires before sending user team email.
							 *
							 * @param array $mail_args Mail arguments.
							 */
							do_action( 'ld_team_email_users_before', $mail_args );

							$mail_ret = wp_mail( $mail_args['to'], $mail_args['subject'], $mail_args['message'], $mail_args['headers'], $mail_args['attachments'] );

							/**
							 * Fires after sending user team email.
							 *
							 * @param array   $mail_args Mail arguments.
							 * @param boolean $success   Whether the email contents were sent successfully.
							 */
							do_action( 'ld_team_email_users_after', $mail_args, $mail_ret );

							if ( ! $mail_ret ) {
								if ( is_wp_error( $team_email_error ) ) { // @phpstan-ignore-line - No time to investigate.
									$team_email_error_message[ $user->user_email ] = $team_email_error->get_error_message();
								}
								wp_send_json_error(
									array(
										// translators: mail_ret error, team email error message.
										'message' => sprintf( wp_kses_post( __( '<span style="color:red">Error: Email(s) not sent. Please try again or check with your hosting provider.<br />wp_mail() returned %1$d.<br />Error: %2$s</span>', 'ebox' ) ), $mail_ret, $team_email_error_message[ $user->user_email ] ),
									)
								);
								die();
							} else {
								$email_addresses[] = $user->user_email;
							}
						} else {
							wp_send_json_error(
								array(
									'message' => '<span style="color:red">' . esc_html__( 'Mail Args empty. Unexpected condition from filter: ld_team_email_users_args', 'ebox' ) . '</span>',
								)
							);
						}
					}
				}

				wp_send_json_success(
					array(
						'message' => '<span style="color:green">' .
						sprintf(
							// translators: total of users emailed, team.
							esc_html__(
								'Success: Email sent to %1$d %2$s users.',
								'ebox'
							),
							count( $email_addresses ),
							ebox_get_custom_label_lower( 'team' )
						),
						'</span>',
					)
				);
			} else {
				foreach ( $team_user_ids as $user_id ) {
					$user = get_user_by( 'id', $user_id );

					if ( $user ) {
						$email_addresses[] = 'Bcc: ' . sanitize_email( $user->user_email );
					}
				}

				$team_email_error = null; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- It is what it is.
				add_action(
					'wp_mail_failed',
					function ( $mail_error ) {
						global $team_email_error;
						$team_email_error = $mail_error; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- It is what it is.
					}
				);

				if ( $email_addresses ) {
					$mail_args['headers'] = array_merge( $mail_args['headers'], $email_addresses );

					/**
					 * Filters team email user arguments.
					 *
					 * @param array $mail_args Team mail arguments.
					 */
					$mail_args = apply_filters( 'ld_team_email_users_args', $mail_args );
					if ( ! empty( $mail_args ) ) {

						/**
						 * Fires before sending user team email.
						 *
						 * @param array $mail_args Mail arguments.
						 */
						do_action( 'ld_team_email_users_before', $mail_args );

						$mail_ret = wp_mail( $mail_args['to'], $mail_args['subject'], $mail_args['message'], $mail_args['headers'], $mail_args['attachments'] );

						/**
						 * Fires after sending user team email.
						 *
						 * @param array   $mail_args Mail arguments.
						 * @param boolean $success   Whether the email contents were sent successfully.
						 */
						do_action( 'ld_team_email_users_after', $mail_args, $mail_ret );

						if ( ! $mail_ret ) {
							$team_email_error_message = '';

							if ( is_wp_error( $team_email_error ) ) { // @phpstan-ignore-line - No time to investigate.
								$team_email_error_message = $team_email_error->get_error_message();
							}
							wp_send_json_error(
								array(
									// translators: mail_ret error, team email error message.
									'message' => sprintf( wp_kses_post( __( '<span style="color:red">Error: Email(s) not sent. Please try again or check with your hosting provider.<br />wp_mail() returned %1$d.<br />Error: %2$s</span>', 'ebox' ) ), $mail_ret, $team_email_error_message ),
								)
							);
						} else {
							wp_send_json_success(
								array(
									'message' => '<span style="color:green">' . sprintf(
										wp_kses_post(
											// translators: total of users emailed, team.
											_nx(
												'Success: Email sent to %1$d %2$s user.',
												'Success: Email sent to %1$d %2$s users.',
												count( $email_addresses ),
												'placeholders: email addresses, team.',
												'ebox'
											)
										),
										number_format_i18n( count( $email_addresses ) ),
										ebox_get_custom_label_lower( 'team' )
									) . '</span>',
								)
							);
						}
					} else {
						wp_send_json_error(
							array(
								'message' => '<span style="color:red">' . esc_html__( 'Mail Args empty. Unexpected condition from filter: ld_team_email_users_args', 'ebox' ) . '</span>',
							)
						);
					}
				}
			}
		} else {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'No users found.', 'ebox' ),
				)
			);
		}
		wp_send_json_error();
		die();
	}
}
add_action( 'wp_ajax_ebox_team_emails', 'ebox_team_emails' );

/**
 * Adds Team Leader role if it does not exist.
 *
 * Fires on `ebox_activated` hook.
 *
 * @since 2.1.0
 */
function ebox_add_team_admin_role() {
	$team_leader = get_role( 'team_leader' );

	// We can't call the class settings because it is not loaded yet.
	$team_leader_user_caps = get_option( 'ebox_teams_team_leader_user', array() );

	$role_caps = array(
		'read'                      => true,
		'team_leader'              => true,
		'wpProQuiz_show_statistics' => true,
	);

	/**
	 * Controls showing the Team Leader user in the Authors selector shown on the post editor. Seems that metabox query checks the
	 * user_meta key wp_user_level value to ensure the level is greater than 0. By default Team Leaders are set to level 0.
	 */
	if ( ( isset( $team_leader_user_caps['show_authors_selector'] ) ) && ( 'yes' === $team_leader_user_caps['show_authors_selector'] ) ) {
		$role_caps['level_1'] = true;
		$role_caps['level_0'] = false;
	} else {
		$role_caps['level_1'] = false;
		$role_caps['level_0'] = true;
	}

	if ( is_null( $team_leader ) ) {
		$team_leader = add_role(
			'team_leader',
			'Team Leader',
			$role_caps
		);
	} else {
		foreach ( $role_caps as $role_cap => $active ) {
			$team_leader->add_cap( $role_cap, $active );
		}
	}

	/**
	 * Added to correct issues with Team Leader User capabilities.
	 * See ebox-5707. See changes in
	 * includes/settings/settings-sections/class-ld-settings-section-teams-team-leader-user.php
	 *
	 * @since 3.4.0.2
	 */
	update_option( 'ebox_teams_team_leader_user_activate', time() );
}

add_action( 'ebox_activated', 'ebox_add_team_admin_role' );

/**
 * Allows team leader access to the admin dashboard.
 *
 * WooCommerce prevents access to the dashboard for all non-admin user roles. This filter allows
 * us to check if the current user is team_leader and override WC access.
 * Fires on `woocommerce_prevent_admin_access` hook.
 *
 * @since 2.2.0.1
 *
 * @param boolean $prevent_access value from WC.
 *
 * @return boolean The adjusted value based on user's access/role.
 */
function ebox_check_team_leader_access( $prevent_access ) {
	if ( ebox_is_team_leader_user() ) {

		if ( defined( 'ebox_GROUP_LEADER_DASHBOARD_ACCESS' ) ) {
			if ( ebox_GROUP_LEADER_DASHBOARD_ACCESS == true ) {
				$prevent_access = false;
			} elseif ( ebox_GROUP_LEADER_DASHBOARD_ACCESS == false ) {
				$prevent_access = true;
			}
		} else {
			$prevent_access = false;
		}
	}

	return $prevent_access;
}
add_filter( 'woocommerce_prevent_admin_access', 'ebox_check_team_leader_access', 20, 1 );

/**
 * Gets the list of enrolled courses for a team.
 *
 * @since 2.1.0
 *
 * @param int     $team_id         Optional. Team ID. Default 0.
 * @param boolean $bypass_transient Optional. Whether to bypass transient cache or not. Default false.
 *
 * @return array An array of course IDs.
 */
function ebox_team_enrolled_courses( $team_id = 0, $bypass_transient = false ) {
	$course_ids = array();

	$team_id = absint( $team_id );
	if ( ! empty( $team_id ) ) {

		$query_args = array(
			'post_type'      => ebox_get_post_type_slug( 'course' ),
			'fields'         => 'ids',
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'     => 'ebox_team_enrolled_' . $team_id,
					'compare' => 'EXISTS',
				),
			),
		);

		$query = new WP_Query( $query_args );
		if ( ( is_a( $query, 'WP_Query' ) ) && ( property_exists( $query, 'posts' ) ) ) {
			$course_ids = $query->posts;
		}
	}

	return $course_ids;
}

/**
 * Sets the list of enrolled courses for a team.
 *
 * @since 2.2.1
 *
 * @param int   $team_id          Optional. Team ID. Default 0.
 * @param array $team_courses_new Optional. An array of courses to enroll a team. Default empty array.
 */
function ebox_set_team_enrolled_courses( $team_id = 0, $team_courses_new = array() ) {
	$team_id = absint( $team_id );
	if ( ! empty( $team_id ) ) {

		$team_courses_old = ebox_team_enrolled_courses( $team_id, true );

		$team_courses_intersect = array_intersect( $team_courses_new, $team_courses_old );

		$team_courses_add = array_diff( $team_courses_new, $team_courses_intersect );
		if ( ! empty( $team_courses_add ) ) {
			foreach ( $team_courses_add as $course_id ) {
				ld_update_course_team_access( $course_id, $team_id, false );
			}
		}

		$team_courses_remove = array_diff( $team_courses_old, $team_courses_intersect );
		if ( ! empty( $team_courses_remove ) ) {
			foreach ( $team_courses_remove as $course_id ) {
				ld_update_course_team_access( $course_id, $team_id, true );
			}
		}

		/**
		 * Finally clear our cache for other services.
		 * $transient_key = 'ebox_team_courses_' . $team_id;
		 * LDLMS_Transients::delete( $transient_key );
		 */
	}
}

/**
 * Teams all the related course ids for a set of teams IDs.
 *
 * @since 2.3.0
 *
 * @param int   $user_id   Optional. The User ID to get the associated teams.
 *                         Defaults to current user ID.
 * @param array $team_ids Optional. An array of team IDs to source the course IDs from.
 *                         If not provided will use team ids based on user_id access.
 *                         Default empty array.
 *
 * @return array An array of course_ids.
 */
function ebox_get_teams_courses_ids( $user_id = 0, $team_ids = array() ) {
	$course_ids = array();

	$user_id = absint( $user_id );
	if ( ( is_array( $team_ids ) ) && ( ! empty( $team_ids ) ) ) {
		$team_ids = array_map( 'absint', $team_ids );
	}

	if ( empty( $user_id ) ) {
		// If the current user is not able to be determined. Then abort.
		if ( ! is_user_logged_in() ) {
			return $course_ids;
		}

		$user_id = get_current_user_id();
	}

	if ( ebox_is_team_leader_user( $user_id ) ) {
		$team_leader_team_ids = ebox_get_administrators_team_ids( $user_id );

		// If user is team leader and the team ids is empty, nothing else to do. abort.
		if ( empty( $team_leader_team_ids ) ) {
			return $course_ids;
		}

		if ( empty( $team_ids ) ) {
			$team_ids = $team_leader_team_ids;
		} else {
			$team_ids = array_intersect( $team_leader_team_ids, $team_ids );
		}
	} elseif ( ! ebox_is_admin_user( $user_id ) ) {
		return $course_ids;
	}

	if ( ! empty( $team_ids ) ) {

		foreach ( $team_ids as $team_id ) {
			$team_course_ids = ebox_team_enrolled_courses( $team_id );
			if ( ! empty( $team_course_ids ) ) {
				$course_ids = array_merge( $course_ids, $team_course_ids );
			}
		}
	}

	if ( ! empty( $course_ids ) ) {
		$course_ids = array_unique( $course_ids );
	}

	return $course_ids;
}

/**
 * Checks whether a team is enrolled in a certain course.
 *
 * @since 2.1.0
 *
 * @param int $team_id  Team ID.
 * @param int $course_id Course ID.
 *
 * @return boolean Whether a team is enrolled in a course or not.
 */
function ebox_team_has_course( $team_id = 0, $course_id = 0 ) {
	$team_id  = absint( $team_id );
	$course_id = absint( $course_id );
	if ( ( ! empty( $team_id ) ) && ( ! empty( $course_id ) ) ) {
		return get_post_meta( $course_id, 'ebox_team_enrolled_' . $team_id, true );
	}

	return false;
}

/**
 * Gets the timestamp of when a course is available to the team.
 *
 * @since 2.1.0
 *
 * @param int $team_id  Team ID.
 * @param int $course_id Course ID.
 *
 * @return string The timestamp of when a course is available to the team.
 */
function ebox_team_course_access_from( $team_id = 0, $course_id = 0 ) {
	$team_id  = absint( $team_id );
	$course_id = absint( $course_id );
	if ( ( ! empty( $team_id ) ) && ( ! empty( $course_id ) ) ) {
		$timestamp = absint( get_post_meta( $course_id, 'ebox_team_enrolled_' . $team_id, true ) );

		/**
		 * Filters team courses order query arguments.
		 *
		 * @param int $timestamp The timestamp when the course was enrolled to the Team.
		 * @param int $team_id  Team ID.
		 * @param int $course_id Course ID.
		 */
		return apply_filters( 'ebox_team_course_access_from', $timestamp, $team_id, $course_id );
	}

	return '';
}

/**
 * Checks whether a course can be accessed by the user's team.
 *
 * @since 2.1.0
 *
 * @param int $user_id   User ID.
 * @param int $course_id Course ID.
 *
 * @return boolean Whether a course can be accessed by the user's team.
 */
function ebox_user_team_enrolled_to_course( $user_id = 0, $course_id = 0 ) {
	$user_id   = absint( $user_id );
	$course_id = absint( $course_id );
	if ( ( ! empty( $user_id ) ) && ( ! empty( $course_id ) ) ) {
		$team_ids = ebox_get_users_team_ids( $user_id );
		if ( ! empty( $team_ids ) ) {
			foreach ( $team_ids as $team_id ) {
				if ( ebox_team_has_course( $team_id, $course_id ) ) {
					return true;
				}
			}
		}
	}
	return false;
}



/**
 * Gets timestamp of when the course is available to a user in a team.
 *
 * @since 2.1.0
 *
 * @param int     $user_id   User ID.
 * @param int     $course_id Course ID.
 * @param boolean $bypass_transient Optional. Whether to bypass transient cache. Default false.
 *
 * @return string|void The timestamp of when a course is available to a user in a team.
 */
function ebox_user_team_enrolled_to_course_from( $user_id = 0, $course_id = 0, $bypass_transient = false ) {
	$enrolled_from = null;
	$user_id       = absint( $user_id );
	$course_id     = absint( $course_id );
	if ( ( empty( $user_id ) ) || ( empty( $course_id ) ) ) {
		return $enrolled_from;
	}

	$userdata = get_userdata( $user_id );
	if ( ! $userdata ) {
		return $enrolled_from;
	}
	$user_registered_timestamp = strtotime( $userdata->user_registered );

	$user_team_ids = ebox_get_users_team_ids( $user_id, $bypass_transient );
	if ( empty( $user_team_ids ) ) {
		return $enrolled_from;
	}
	$user_team_ids = array_map( 'absint', $user_team_ids );

	$course_team_ids = ebox_get_course_teams( $course_id );
	if ( empty( $course_team_ids ) ) {
		return $enrolled_from;
	}
	$course_team_ids = array_map( 'absint', $course_team_ids );

	$course_team_ids = array_intersect( $course_team_ids, $user_team_ids );
	if ( empty( $course_team_ids ) ) {
		return $enrolled_from;
	}

	if ( ! empty( $course_team_ids ) ) {
		$team_course_enrolled_times = array();

		foreach ( $course_team_ids as $course_team_id ) {
			$enrolled_from_temp = ebox_team_course_access_from( $course_team_id, $course_id );
			if ( ! empty( $enrolled_from_temp ) ) {
				$team_course_enrolled_times[ $course_team_id ] = absint( $enrolled_from_temp );
			}
		}

		if ( ! empty( $team_course_enrolled_times ) ) {
			asort( $team_course_enrolled_times );

			/**
			 * Filter the user team enrollment to course timestamps.
			 *
			 * @since 3.5.0
			 *
			 * @param array $team_course_enrolled_times Array of course to team enrollment timestamps.
			 * @param int   $user_id                     User ID.
			 * @param int   $course_id                   Course Post ID.
			 */
			$team_course_enrolled_times = apply_filters( 'ebox_user_team_enrolled_to_course_from_timestamps', $team_course_enrolled_times, $user_id, $course_id );

			foreach ( $team_course_enrolled_times as $team_id => $team_course_timestamp ) {
				$enrolled_from = $team_course_timestamp;
				break;
			}
		}
	}

	if ( ! is_null( $enrolled_from ) ) {
		if ( $enrolled_from <= time() ) {
			/** If the user registered AFTER the course was enrolled into the team
			 * then we use the user registration date.
			 */
			if ( $user_registered_timestamp > $enrolled_from ) {
				if ( ( defined( 'ebox_GROUP_ENROLLED_COURSE_FROM_USER_REGISTRATION' ) ) && ( true === ebox_GROUP_ENROLLED_COURSE_FROM_USER_REGISTRATION ) ) {
					$enrolled_from = $user_registered_timestamp;
				}
			}
		} else {
			/**
			 * If $enrolled_from is greater than the current timestamp
			 * we reset the enrolled from time to null. Not sure why.
			 */
			$enrolled_from = null;
		}
	}

	/**
	 * Filters user courses order query arguments.
	 *
	 * @param int $enrolled_from Calculated timestamp when user enrolled to course through team.
	 * @param int $user_id   User ID.
	 * @param int $course_id Course ID.
	 * @param int $team_id  Determined Team ID.
	 */
	return apply_filters( 'ebox_user_team_enrolled_to_course_from', $enrolled_from, $user_id, $course_id, $team_id );
}

/**
 * Gets the list of team IDs administered by the user.
 *
 * @since 2.1.0
 *
 * @global wpdb   $wpdb    WordPress database abstraction object.
 *
 * @param int     $user_id User ID.
 * @param boolean $menu    Optional. Menu. Default false.
 *
 * @return array A list of team ids managed by user.
 */
function ebox_get_administrators_team_ids( $user_id, $menu = false ) {
	$team_ids = array();

	$user_id = absint( $user_id );
	if ( ! empty( $user_id ) ) {
		if ( ( ebox_is_admin_user( $user_id ) ) && ( true !== $menu ) ) {
			$team_ids = ebox_get_teams( true, $user_id );
		} else {
			$all_user_meta = get_user_meta( $user_id );
			if ( ! empty( $all_user_meta ) ) {
				foreach ( $all_user_meta as $meta_key => $meta_set ) {
					if ( 'ebox_team_leaders_' == substr( $meta_key, 0, strlen( 'ebox_team_leaders_' ) ) ) {
						$team_ids = array_merge( $team_ids, $meta_set );
					}
				}
			}

			if ( ! empty( $team_ids ) ) {
				$team_ids = array_map( 'absint', $team_ids );
				$team_ids = array_diff( $team_ids, array( 0 ) ); // Removes zeros.
				$team_ids = ebox_validate_teams( $team_ids );
				if ( ! empty( $team_ids ) ) {
					if ( ebox_is_teams_hierarchical_enabled() ) {
						foreach ( $team_ids as $team_id ) {
							$team_children = ebox_get_team_children( $team_id );
							if ( ! empty( $team_children ) ) {
								$team_ids = array_merge( $team_ids, $team_children );
							}
						}
					}

					$team_ids = array_map( 'absint', $team_ids );
					$team_ids = array_unique( $team_ids, SORT_NUMERIC );
				}
			}
		}
	}

	return $team_ids;
}

/**
 * Makes user an administrator of the given team IDs.
 *
 * @since 2.2.1
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int   $user_id           User ID.
 * @param array $leader_teams_new Optional. A list of team ids. Default empty array.
 *
 * @return array
 */
function ebox_set_administrators_team_ids( $user_id = 0, $leader_teams_new = array() ) {
	global $wpdb;

	$user_id = absint( $user_id );
	if ( ! is_array( $leader_teams_new ) ) {
		$leader_teams_new = array();
	}

	if ( ! empty( $user_id ) ) {
		$leader_teams_old       = ebox_get_administrators_team_ids( $user_id, true );
		$leader_teams_intersect = array_intersect( $leader_teams_new, $leader_teams_old );

		$leader_teams_add = array_diff( $leader_teams_new, $leader_teams_intersect );
		if ( ! empty( $leader_teams_add ) ) {
			foreach ( $leader_teams_add as $team_id ) {
				ld_update_leader_team_access( $user_id, $team_id, false );
			}
		}

		$leader_teams_remove = array_diff( $leader_teams_old, $leader_teams_intersect );
		if ( ! empty( $leader_teams_remove ) ) {
			foreach ( $leader_teams_remove as $team_id ) {
				ld_update_leader_team_access( $user_id, $team_id, true );
			}
		}

		/**
		 * Finally clear our cache for other services.
		 * $transient_key = "ebox_user_teams_" . $user_id;
		 * LDLMS_Transients::delete( $transient_key );
		 */
	}
	return array();
}



/**
 * Gets the list of all teams.
 *
 * @since 2.1.0
 *
 * @param boolean $id_only         Optional. Whether to return only IDs. Default false.
 * @param int     $current_user_id Optional. ID of the user for checking capabilities. Default 0.
 *
 * @return array An array of team IDs.
 */
function ebox_get_teams( $id_only = false, $current_user_id = 0 ) {

	if ( empty( $current_user_id ) ) {
		if ( ! is_user_logged_in() ) {
			return array();
		}
		$current_user_id = get_current_user_id();
	}

	if ( ebox_is_team_leader_user( $current_user_id ) ) {
		return ebox_get_administrators_team_ids( $current_user_id );
	} elseif ( ebox_is_admin_user( $current_user_id ) ) {

		$teams_query_args = array(
			'post_type'   => 'teams',
			'nopaging'    => true,
			'post_status' => array( 'publish', 'pending', 'draft', 'future', 'private' ),
		);

		if ( $id_only ) {
			$teams_query_args['fields'] = 'ids';
		}

		$teams_query = new WP_Query( $teams_query_args );
		return $teams_query->posts;
	}
	return array();
}

/**
 * Get a users team IDs.
 *
 * @since 2.1.0
 *
 * @param int     $user_id          Optional. User ID. Default 0.
 * @param boolean $bypass_transient Optional. Whether to bypass transient cache or not. Default false.
 *
 * @return array A list of user's team IDs.
 */
function ebox_get_users_team_ids( $user_id = 0, $bypass_transient = false ) {
	$team_ids = array();

	$user_id = absint( $user_id );
	if ( ! empty( $user_id ) ) {
		$transient_key = 'ebox_user_teams_' . $user_id;
		if ( ! $bypass_transient ) {
			$team_ids_transient = LDLMS_Transients::get( $transient_key );
		} else {
			$team_ids_transient = false;
		}

		if ( false === $team_ids_transient ) {
			if ( ebox_is_team_leader_user( $user_id ) && ( 'yes' === ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_Teams_Team_Leader_User', 'teams_autoenroll_managed' ) ) ) {
				$team_ids = ebox_get_administrators_team_ids( $user_id );
			} else {
				$all_user_meta = get_user_meta( $user_id );
				if ( ! empty( $all_user_meta ) ) {
					foreach ( $all_user_meta as $meta_key => $meta_set ) {
						if ( 'ebox_team_users_' == substr( $meta_key, 0, strlen( 'ebox_team_users_' ) ) ) {
							$team_ids = array_merge( $team_ids, $meta_set );
						}
					}
				}
			}

			if ( ! empty( $team_ids ) ) {
				$team_ids = array_map( 'absint', $team_ids );
				$team_ids = array_diff( $team_ids, array( 0 ) ); // Removes zeros.
				$team_ids = ebox_validate_teams( $team_ids );
				if ( ! empty( $team_ids ) ) {
					if ( ebox_is_teams_hierarchical_enabled() ) {
						foreach ( $team_ids as $team_id ) {
							$team_children = ebox_get_team_children( $team_id );
							if ( ! empty( $team_children ) ) {
								$team_ids = array_merge( $team_ids, $team_children );
							}
						}
					}

					$team_ids = array_map( 'absint', $team_ids );
					$team_ids = array_unique( $team_ids, SORT_NUMERIC );
				}
			}
			LDLMS_Transients::set( $transient_key, $team_ids, MINUTE_IN_SECONDS );
		} else {
			$team_ids = $team_ids_transient;
		}
	}

	return $team_ids;
}

/**
 * Adds a user to the list of given team IDs.
 *
 * @param int   $user_id         Optional. User ID. Default 0.
 * @param array $user_teams_new Optional. An array of team IDs to add a user. Default empty array.
 */
function ebox_set_users_team_ids( $user_id = 0, $user_teams_new = array() ) {

	$user_id = absint( $user_id );
	if ( ! is_array( $user_teams_new ) ) {
		$user_teams_new = array();
	}

	if ( ! empty( $user_id ) ) {
		$user_teams_old = ebox_get_users_team_ids( $user_id, true );

		$user_teams_intersect = array_intersect( $user_teams_new, $user_teams_old );

		$user_teams_add = array_diff( $user_teams_new, $user_teams_intersect );
		if ( ! empty( $user_teams_add ) ) {
			foreach ( $user_teams_add as $team_id ) {
				ld_update_team_access( $user_id, $team_id, false );
			}
		}

		$user_teams_remove = array_diff( $user_teams_old, $user_teams_intersect );
		if ( ! empty( $user_teams_remove ) ) {
			foreach ( $user_teams_remove as $team_id ) {
				ld_update_team_access( $user_id, $team_id, true );
			}
		}
	}
}

/**
 * Gets the list of teams associated with the course.
 *
 * @since 2.2.1
 *
 * @param int     $course_id        Optional. Course ID. Default 0.
 * @param boolean $bypass_transient Optional. Whether to bypass transient cache or not. Default false.
 *
 * @return array An array of team IDs associated with the course.
 */
function ebox_get_course_teams( $course_id = 0, $bypass_transient = false ) {
	$team_ids = array();

	$course_id = absint( $course_id );
	if ( ! empty( $course_id ) ) {
		$course_post_meta = get_post_meta( $course_id );
		if ( ! empty( $course_post_meta ) ) {
			foreach ( $course_post_meta as $meta_key => $meta_set ) {
				if ( 'ebox_team_enrolled_' == substr( $meta_key, 0, strlen( 'ebox_team_enrolled_' ) ) ) {
					/**
					 * For Course Teams the meta_value is a datetime. This is the datetime the course
					 * was added to the team. So we need to pull the team_id from the meta_key.
					 */
					$team_id    = str_replace( 'ebox_team_enrolled_', '', $meta_key );
					$team_ids[] = absint( $team_id );
				}
			}

			if ( ! empty( $team_ids ) ) {
				$team_ids = ebox_validate_teams( $team_ids );
			}
		}
	}

	return $team_ids;
}

/**
 * Adds a course to the list of the given team IDs.
 *
 * @param int   $course_id         Optional. Course ID. Default 0.
 * @param array $course_teams_new Optional. A list of team IDs to add a course. Default empty array.
 */
function ebox_set_course_teams( $course_id = 0, $course_teams_new = array() ) {

	$course_id = absint( $course_id );
	if ( ! is_array( $course_teams_new ) ) {
		$course_teams_new = array();
	}

	if ( ! empty( $course_id ) ) {
		$course_teams_old       = ebox_get_course_teams( $course_id, true );
		$course_teams_intersect = array_intersect( $course_teams_new, $course_teams_old );

		$course_teams_add = array_diff( $course_teams_new, $course_teams_intersect );
		if ( ! empty( $course_teams_add ) ) {
			foreach ( $course_teams_add as $team_id ) {
				ld_update_course_team_access( $course_id, $team_id, false );
			}
		}

		$course_teams_remove = array_diff( $course_teams_old, $course_teams_intersect );
		if ( ! empty( $course_teams_remove ) ) {
			foreach ( $course_teams_remove as $team_id ) {
				ld_update_course_team_access( $course_id, $team_id, true );
			}
		}

		// Finally clear our cache for other services.
		$transient_key = 'ebox_course_teams_' . $course_id;
		LDLMS_Transients::delete( $transient_key );
	}
}

/**
 * Gets the list of users ids that belong to a team.
 *
 * @since 2.1.0
 *
 * @param int     $team_id         Optional. Team ID. Default 0.
 * @param boolean $bypass_transient Optional. Whether to bypass transient cache or not. Default false.
 *
 * @return array An array of user ids that belong to team.
 */
function ebox_get_teams_user_ids( $team_id = 0, bool $bypass_transient = false ): array {
	$team_id = absint( $team_id );

	if ( empty( $team_id ) ) {
		return array();
	}

	$team_users = ebox_get_teams_users( $team_id, $bypass_transient );

	if ( empty( $team_users ) ) {
		return array();
	}

	return wp_list_pluck( $team_users, 'ID' );
}

/**
 * Gets the list of user objects that belong to a team.
 *
 * @since 2.1.2
 *
 * @param int     $team_id         Team ID.
 * @param boolean $bypass_transient Optional. Whether to bypass transient cache or not. Default false.
 *
 * @return array An array user objects that belong to team.
 */
function ebox_get_teams_users( $team_id, $bypass_transient = false ) {

	$team_id = absint( $team_id );
	if ( ! empty( $team_id ) ) {
		if ( ! $bypass_transient ) {
			$transient_key       = 'ebox_team_users_' . $team_id;
			$team_users_objects = LDLMS_Transients::get( $transient_key );
		} else {
			$team_users_objects = false;
		}

		if ( false === $team_users_objects ) {

			/**
			 * Changed in v2.3 we no longer exclude ALL team leaders from teams.
			 * A team leader CAN be a member of a team user list.
			 *
			 * For this team get the team leaders. They will be excluded from the regular users.
			 * $team_leader_user_ids = ebox_get_teams_administrator_ids( $team_id );
			 */

			$user_query_args = array(
				'orderby'    => 'display_name',
				'order'      => 'ASC',
				'meta_query' => array(
					array(
						'key'     => 'ebox_team_users_' . intval( $team_id ),
						'compare' => 'EXISTS',
					),
				),
			);
			$user_query      = new WP_User_Query( $user_query_args );
			if ( isset( $user_query->results ) ) {
				$team_users_objects = $user_query->results;
			} else {
				$team_users_objects = array();
			}

			if ( ! $bypass_transient ) {
				LDLMS_Transients::set( $transient_key, $team_users_objects, MINUTE_IN_SECONDS );
			}
		}

		return $team_users_objects;
	}
	return array();
}


/**
 * Adds the list of given users to the team.
 *
 * @since 2.1.2
 *
 * @param int   $team_id        Optional. Team ID. Default 0.
 * @param array $team_users_new Optional. A list of user IDs to add to the team. Default empty array.
 */
function ebox_set_teams_users( $team_id = 0, $team_users_new = array() ) {

	$team_id = absint( $team_id );
	if ( ( is_array( $team_users_new ) ) && ( ! empty( $team_users_new ) ) ) {
		$team_users_new = array_map( 'absint', $team_users_new );
	} else {
		$team_users_new = array();
	}
	if ( ! empty( $team_id ) ) {
		update_post_meta( $team_id, 'ebox_team_users_' . $team_id, $team_users_new );

		$team_users_old = ebox_get_teams_user_ids( $team_id, true );

		$team_users_intersect = array_intersect( $team_users_new, $team_users_old );

		$team_users_add = array_diff( $team_users_new, $team_users_intersect );
		if ( ! empty( $team_users_add ) ) {
			foreach ( $team_users_add as $user_id ) {
				ld_update_team_access( $user_id, $team_id, false );
			}
		}

		$team_users_remove = array_diff( $team_users_old, $team_users_intersect );
		if ( ! empty( $team_users_remove ) ) {
			foreach ( $team_users_remove as $user_id ) {
				ld_update_team_access( $user_id, $team_id, true );
			}

			/**
			 * Fires after removing a user from the team.
			 *
			 * $team_id           int   ID of the team.
			 * $team_users_remove array An array of user IDs that are removed from the team.
			 */
			do_action( 'ebox_remove_team_users', $team_id, $team_users_remove );
		}

		// Finally clear our cache for other services.
		$transient_key = 'ebox_team_users_' . $team_id;
		LDLMS_Transients::delete( $transient_key );
	}
}

/**
 * Gets the list of administrator IDs for a team.
 *
 * @since 2.1.0
 *
 * @param int     $team_id         Team ID.
 * @param boolean $bypass_transient Optional. Whether to bypass transient cache or not. Default false.
 *
 * @return array An array of team administrator IDs.
 */
function ebox_get_teams_administrator_ids( $team_id = 0, $bypass_transient = false ) {

	$team_leader_user_ids = array();

	$team_id = absint( $team_id );
	if ( ! empty( $team_id ) ) {
		$team_leader_users = ebox_get_teams_administrators( $team_id, $bypass_transient );
		if ( ! empty( $team_leader_users ) ) {
			$team_leader_user_ids = wp_list_pluck( $team_leader_users, 'ID' );
		}
	}
	return $team_leader_user_ids;
}

/**
 * Gets the list of team leaders for the given team ID.
 *
 * @since 2.1.2
 *
 * @param int     $team_id         Team ID.
 * @param boolean $bypass_transient Optional. Whether to bypass transient cache or not. Default 0.
 *
 * @return array An array of team leader user objects.
 */
function ebox_get_teams_administrators( $team_id = 0, $bypass_transient = false ) {

	$team_id = absint( $team_id );
	if ( ! empty( $team_id ) ) {
		$transient_key = 'ebox_team_leaders_' . $team_id;

		if ( ! $bypass_transient ) {
			$team_user_objects = LDLMS_Transients::get( $transient_key );
		} else {
			$team_user_objects = false;
		}
		if ( false === $team_user_objects ) {

			$user_query_args = array(
				'orderby'    => 'display_name',
				'order'      => 'ASC',
				'meta_query' => array(
					array(
						'key'     => 'ebox_team_leaders_' . intval( $team_id ),
						'value'   => intval( $team_id ),
						'compare' => '=',
						'type'    => 'NUMERIC',
					),
				),
			);
			$user_query      = new WP_User_Query( $user_query_args );
			if ( isset( $user_query->results ) ) {
				$team_user_objects = $user_query->results;
			} else {
				$team_user_objects = array();
			}

			if ( ! $bypass_transient ) {
				LDLMS_Transients::set( $transient_key, $team_user_objects, MINUTE_IN_SECONDS );
			}
		}

		return $team_user_objects;
	}
	return array();
}

/**
 * Makes the user leader for the given team ID.
 *
 * @since 2.1.2
 *
 * @param int   $team_id          Optional. Team ID. Default 0.
 * @param array $team_leaders_new Optional. A list of user IDs to make team leader. Default empty array.
 */
function ebox_set_teams_administrators( $team_id = 0, $team_leaders_new = array() ) {

	$team_id = absint( $team_id );
	if ( ! empty( $team_id ) ) {
		$team_leaders_old = ebox_get_teams_administrator_ids( $team_id, true );

		$team_leaders_intersect = array_intersect( $team_leaders_new, $team_leaders_old );
		$team_leaders_add       = array_diff( $team_leaders_new, $team_leaders_intersect );
		if ( ! empty( $team_leaders_add ) ) {
			foreach ( $team_leaders_add as $user_id ) {
				ld_update_leader_team_access( $user_id, $team_id );
			}
		}

		$team_leaders_remove = array_diff( $team_leaders_old, $team_leaders_intersect );
		if ( ! empty( $team_leaders_remove ) ) {
			foreach ( $team_leaders_remove as $user_id ) {
				ld_update_leader_team_access( $user_id, $team_id, true );
			}
		}

		// Finally clear our cache for other services.
		$transient_key = 'ebox_team_leaders_' . $team_id;
		LDLMS_Transients::delete( $transient_key );
	}
}

/**
 * Gets the list of teams associated with the course step.
 *
 * @since 3.1.8
 *
 * @param int $step_id Course Step ID. Required.
 *
 * @return array An array of team IDs associated with the course step.
 */
function ebox_get_course_step_teams( $step_id = 0 ) {
	$step_team_ids = array();

	$step_id = absint( $step_id );
	if ( ! empty( $step_id ) ) {
		$step_courses = ebox_get_courses_for_step( $step_id, true );
		if ( ! empty( $step_courses ) ) {
			foreach ( array_keys( $step_courses ) as $course_id ) {
				$step_team_ids = array_merge( $step_team_ids, ebox_get_course_teams( $course_id ) );
			}
		}
	}

	if ( ! empty( $step_team_ids ) ) {
		$step_team_ids = array_unique( $step_team_ids );
	}

	return $step_team_ids;
}

/**
 * Get all Users within all Teams managed by the Team Leader.
 *
 * @since   3.1.8
 *
 * @param  integer $team_leader_id  WP_User ID.
 * @return array WP_User IDs
 */
function ebox_get_teams_administrators_users( $team_leader_id = 0 ) {
	$user_ids = array();

	$team_leader_id = absint( $team_leader_id );
	if ( ! empty( $team_leader_id ) ) {
		// Get all the Team IDs of Teams they Manage.
		$team_ids = ebox_get_administrators_team_ids( $team_leader_id );
		if ( ! empty( $team_ids ) ) {
			foreach ( $team_ids as $team_id ) {
				// Get all the User IDs belonging to their Teams.
				$user_ids = array_merge( $user_ids, ebox_get_teams_user_ids( $team_id ) );
			}
		}
	}

	// Remove any overlap.
	if ( ! empty( $user_ids ) ) {
		$user_ids = array_unique( $user_ids );
	}

	return $user_ids;
}

/**
 * Get all Courses within all Teams managed by the Team Leader.
 *
 * @since   3.1.8
 *
 * @param integer $team_leader_id WP_User ID.
 * @return array Array of WP_Post Course IDs.
 */
function ebox_get_teams_administrators_courses( $team_leader_id = 0 ) {
	$course_ids = array();

	$team_leader_id = absint( $team_leader_id );
	if ( ! empty( $team_leader_id ) ) {
		// Get all the Team IDs of Teams they Manage.
		$team_ids = ebox_get_administrators_team_ids( $team_leader_id );
		if ( ! empty( $team_ids ) ) {
			foreach ( $team_ids as $team_id ) {
				// Get all the User IDs belonging to their Teams.
				$course_ids = array_merge( $course_ids, ebox_team_enrolled_courses( $team_id ) );
			}
		}
	}

	// Remove any overlap.
	if ( ! empty( $course_ids ) ) {
		$course_ids = array_unique( $course_ids );
	}

	return $course_ids;
}

/**
 * Get the Team Leader user for a specific Course step.
 *
 * @since 3.1.8
 *
 * @param integer $step_id         Course Step Post ID.
 * @param integer $team_leader_id Team Leader User ID. Optional.
 * @return array of user IDs.
 */
function ebox_get_teams_leaders_users_for_course_step( $step_id = 0, $team_leader_id = 0 ) {
	$user_ids = array();

	$step_id = absint( $step_id );

	if ( empty( $team_leader_id ) ) {
		$team_leader_id = get_current_user_id();
		if ( ! ebox_is_team_leader_user( $team_leader_id ) ) {
			$team_leader_id = 0;
		}
	}

	if ( ( ! empty( $step_id ) ) && ( ! empty( $team_leader_id ) ) ) {
		$gl_teams = ebox_get_administrators_team_ids( $team_leader_id );
		if ( ! empty( $gl_teams ) ) {
			$step_teams = ebox_get_course_step_teams( $step_id );
			$gl_teams   = array_intersect( $gl_teams, $step_teams );
		}

		if ( ! empty( $gl_teams ) ) {
			foreach ( $gl_teams as $team_id ) {
				$user_ids = array_merge( $user_ids, ebox_get_teams_user_ids( $team_id ) );
			}
		}
	}

	return $user_ids;
}

/**
 * Filter Quiz Statistics user listing to show only related users.
 *
 * @since 3.1.8
 *
 * @param string $where Statistics WHERE clause string.
 * @param array  $args  Array of query args.
 * @return string $where
 */
function ebox_fetch_quiz_statistic_history_where_filter( $where = '', $args = array() ) {

	if ( ! ebox_is_admin_user( get_current_user_id() ) ) {

		if ( ebox_is_team_leader_user( get_current_user_id() ) ) {
			$team_user_ids = array();
			if ( ( isset( $args['quiz'] ) ) && ( ! empty( $args['quiz'] ) ) ) {
				$team_user_ids = ebox_get_teams_leaders_users_for_course_step( $args['quiz'], get_current_user_id() );
			} else {
				$team_user_ids = ebox_get_teams_administrators_users( get_current_user_id() );
			}

			if ( ! empty( $team_user_ids ) ) {
				$where .= ' AND user_id IN (' . implode( ',', $team_user_ids ) . ') ';
			} else {
				$where .= ' AND user_id = -1 ';
			}
		} else {
			$where .= ' AND user_id =' . get_current_user_id() . ' ';
		}
	}

	// Always return $where.
	return $where;
}
add_filter( 'ebox_fetch_quiz_statistic_history_where', 'ebox_fetch_quiz_statistic_history_where_filter', 10, 2 );
add_filter( 'ebox_fetch_quiz_toplist_history_where', 'ebox_fetch_quiz_statistic_history_where_filter', 10, 2 );
add_filter( 'ebox_fetch_quiz_statistic_overview_where', 'ebox_fetch_quiz_statistic_history_where_filter', 10, 2 );


/**
 * Checks if a user has the team leader capabilities.
 *
 * Replaces the `is_team_leader` function.
 *
 * @since 2.3.9
 *
 * @param int|WP_User $user Optional. The `WP_User` object or user ID to check. Default 0.
 *
 * @return boolean Returns true if the user is team leader otherwise false.
 */
function ebox_is_team_leader_user( $user = 0 ) {
	$user_id = 0;

	if ( ( is_numeric( $user ) ) && ( ! empty( $user ) ) ) {
		$user_id = $user;
	} elseif ( $user instanceof WP_User ) {
		$user_id = $user->ID;
	} else {
		$user_id = get_current_user_id();
	}

	if ( ( ! empty( $user_id ) ) && ( ! ebox_is_admin_user( $user_id ) ) && ( defined( 'ebox_GROUP_LEADER_CAPABILITY_CHECK' ) ) && ( ebox_GROUP_LEADER_CAPABILITY_CHECK != '' ) ) {
		return user_can( $user_id, ebox_GROUP_LEADER_CAPABILITY_CHECK );
	}

	return false;
}

/**
 * Checks if a user has the admin capabilities.
 *
 * @param int|WP_User $user Optional. The `WP_User` object or user ID to check. Default 0.
 *
 * @return boolean Returns true if the user is admin otherwise false.
 */
function ebox_is_admin_user( $user = 0 ) {
	$user_id = 0;

	if ( ( is_numeric( $user ) ) && ( ! empty( $user ) ) ) {
		$user_id = $user;
	} elseif ( $user instanceof WP_User ) {
		$user_id = $user->ID;
	} else {
		$user_id = get_current_user_id();
	}

	if ( ( ! empty( $user_id ) ) && ( defined( 'ebox_ADMIN_CAPABILITY_CHECK' ) ) && ( ebox_ADMIN_CAPABILITY_CHECK != '' ) ) {
		return user_can( $user_id, ebox_ADMIN_CAPABILITY_CHECK );
	}

	return false;
}

/**
 * Checks whether a team leader is an admin of a user's team.
 *
 * @since 2.1.0
 *
 * @param int $team_leader_id Team leader ID.
 * @param int $user_id         User ID.
 *
 * @return boolean Returns true if team leader is an admin of a user's team otherwise false.
 */
function ebox_is_team_leader_of_user( $team_leader_id = 0, $user_id = 0 ) {
	$team_leader_id = absint( $team_leader_id );
	$user_id         = absint( $user_id );

	$admin_teams     = ebox_get_administrators_team_ids( $team_leader_id );
	$has_admin_teams = ! empty( $admin_teams ) && is_array( $admin_teams ) && ! empty( $admin_teams[0] );

	foreach ( $admin_teams as $team_id ) {
		$ebox_is_user_in_team = ebox_is_user_in_team( $user_id, $team_id );

		if ( $ebox_is_user_in_team ) {
			return true;
		}
	}

	return false;
}



/**
 * Checks whether a user is part of the team or not.
 *
 * @since 2.1.0
 *
 * @param int $user_id  User ID.
 * @param int $team_id Team ID.
 *
 * @return boolean Returns true if the user is part of the team otherwise false.
 */
function ebox_is_user_in_team( $user_id = 0, $team_id = 0 ) {
	$user_id  = absint( $user_id );
	$team_id = absint( $team_id );
	if ( ( ! empty( $user_id ) ) && ( ! empty( $team_id ) ) ) {
		if ( ebox_is_teams_hierarchical_enabled() ) {
			$team_ids = ebox_get_users_team_ids( $user_id );
			if ( in_array( $team_id, $team_ids, true ) ) {
				return true;
			}
		} else {
			return get_user_meta( $user_id, 'ebox_team_users_' . $team_id, true );
		}
	}

	return false;
}

/**
 * Deletes team ID from all users meta when the team is deleted.
 *
 * Fires on `delete_post` hook.
 *
 * @todo  restrict function to only run if post type is team
 *        will run against db every time a post is deleted
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @since 2.1.0
 *
 * @param int $pid ID of the team being deleted.
 *
 * @return boolean|void Returns true if the deletion was successful.
 */
function ebox_delete_team( $pid = 0 ) {
	global $wpdb;

	$pid = absint( $pid );
	if ( ! empty( $pid ) && is_numeric( $pid ) ) {
		$wpdb->delete(
			$wpdb->usermeta,
			array(
				'meta_key'   => 'ebox_team_users_' . $pid,
				'meta_value' => $pid,
			)
		);
		$wpdb->delete(
			$wpdb->usermeta,
			array(
				'meta_key'   => 'ebox_team_leaders_' . $pid,
				'meta_value' => $pid,
			)
		);
	}

	return true;
}

add_action( 'delete_post', 'ebox_delete_team', 10 );


/**
 * Updates a user's team access.
 *
 * @since 2.1.0
 * @since 3.4.0 Added return boolean.
 *
 * @param int     $user_id  User ID.
 * @param int     $team_id Team ID.
 * @param boolean $remove   Optional. Whether to remove user from the team. Default false.
 *
 * @return bool true on action success otherwise false.
 */
function ld_update_team_access( $user_id = 0, $team_id = 0, $remove = false ): bool {
	$action_success = false;

	$user_id  = absint( $user_id );
	$team_id = absint( $team_id );

	if ( ( ! empty( $user_id ) ) && ( ! empty( $team_id ) ) ) {
		$activity_type = 'team_access_user';

		if ( $remove ) {
			$user_enrolled = get_user_meta( $user_id, 'ebox_team_users_' . $team_id, true );
			if ( $user_enrolled ) {
				$action_success = true;
				delete_user_meta( $user_id, 'ebox_team_users_' . $team_id );

				/**
				 * If the user is removed from the course then also remove the team_progress Activity.
				 */
				$team_user_activity_args = array(
					'activity_type' => 'team_progress',
					'user_id'       => $user_id,
					'post_id'       => $team_id,
					'course_id'     => 0,
				);

				$team_user_activity = ebox_get_user_activity( $team_user_activity_args );
				if ( is_object( $team_user_activity ) ) {
					ebox_delete_user_activity( $team_user_activity->activity_id );
				}

				/**
				 * Fires after the user is removed from team access meta.
				 *
				 * @since 2.1.0
				 *
				 * @param int $user_id  User ID.
				 * @param int $team_id Team ID.
				 */
				do_action( 'ld_removed_team_access', $user_id, $team_id );
			}
		} else {
			$user_enrolled = get_user_meta( $user_id, 'ebox_team_users_' . $team_id, true );
			if ( ! $user_enrolled ) {
				$action_success = true;
				update_user_meta( $user_id, 'ebox_team_users_' . $team_id, $team_id );

				/**
				 * Fires after the user is added to team access meta.
				 *
				 * @since 2.1.0
				 *
				 * @param int $user_id  User ID.
				 * @param int $team_id Team ID.
				 */
				do_action( 'ld_added_team_access', $user_id, $team_id );
			}
		}

		// Purge User Teams cache.
		$transient_key = 'ebox_user_teams_' . $user_id;
		LDLMS_Transients::delete( $transient_key );

		// Purge User Courses cache.
		$transient_key = 'ebox_user_courses_' . $user_id;
		LDLMS_Transients::delete( $transient_key );

	}

	return $action_success;
}


/**
 * Updates the course team access.
 *
 * @since 2.1.0
 * @since 3.4.0 Added return boolean.
 *
 * @param int     $course_id Course ID.
 * @param int     $team_id  Team ID.
 * @param boolean $remove    Optional. Whether to remove the team from the course. Default false.
 *
 * @return boolean true on action success otherwise false.
 */
function ld_update_course_team_access( $course_id = 0, $team_id = 0, $remove = false ) {
	$action_success = false;

	$course_id = absint( $course_id );
	$team_id  = absint( $team_id );

	if ( ( ! empty( $course_id ) ) && ( ! empty( $team_id ) ) ) {
		$activity_type = 'team_access_course';

		if ( $remove ) {
			$team_enrolled = get_post_meta( $course_id, 'ebox_team_enrolled_' . $team_id, true );
			if ( $team_enrolled ) {
				$action_success = true;
				delete_post_meta( $course_id, 'ebox_team_enrolled_' . $team_id );

				/**
				 * Fires after the user is removed from the course team meta.
				 *
				 * @since 2.1.0
				 *
				 * @param int $user_id  User ID.
				 * @param int $team_id Team ID.
				 */
				do_action( 'ld_removed_course_team_access', $course_id, $team_id );
			}
		} else {
			$team_enrolled = get_post_meta( $course_id, 'ebox_team_enrolled_' . $team_id, true );
			if ( empty( $team_enrolled ) ) {
				$action_success = true;
				update_post_meta( $course_id, 'ebox_team_enrolled_' . $team_id, time() );

				/**
				 * Fires after the user is added to the course team access meta.
				 *
				 * @since 2.1.0
				 *
				 * @param int $user_id  User ID.
				 * @param int $team_id Team ID.
				 */
				do_action( 'ld_added_course_team_access', $course_id, $team_id );
			}
		}
	}

	return $action_success;
}


/**
 * Updates the team access for a team leader.
 *
 * @since 2.2.1
 * @since 3.4.0 Added return boolean.
 *
 * @param int  $user_id       User ID.
 * @param int  $team_id      Team ID.
 * @param bool $remove_access Optional. Whether to remove user from the team. Default false.
 *
 * @return bool True on action success, otherwise false.
 */
function ld_update_leader_team_access( int $user_id, int $team_id, bool $remove_access = false ): bool {
	if ( empty( $user_id ) || empty( $team_id ) ) {
		return false;
	}

	$team_leader_meta_key = 'ebox_team_leaders_' . $team_id;
	$has_team_leader_meta = ! empty( get_user_meta( $user_id, $team_leader_meta_key, true ) );

	// Adding (not updating, updating always returns false).

	if ( ! $remove_access && ! $has_team_leader_meta ) {
		update_user_meta( $user_id, $team_leader_meta_key, $team_id );

		/**
		 * Fires after the user is added to the team as a leader.
		 *
		 * @since 2.1.0
		 *
		 * @param int $user_id  User ID.
		 * @param int $team_id Team ID.
		 */
		do_action( 'ld_added_leader_team_access', $user_id, $team_id );

		return true;
	}

	// Removing.

	if ( $remove_access && $has_team_leader_meta ) {
		delete_user_meta( $user_id, $team_leader_meta_key );

		/**
		 * Fires after the user is removed from a team as a leader.
		 *
		 * @since 2.1.0
		 *
		 * @param int $user_id  User ID.
		 * @param int $team_id Team ID.
		 */
		do_action( 'ld_removed_leader_team_access', $user_id, $team_id );

		return true;
	}

	return false;
}

/**
 * Gets the team's user IDs if the course is associated with the team.
 *
 * @since 2.3.0
 *
 * @param int $course_id Optional. Course ID. Default 0.
 *
 * @return array An array of user IDs.
 */
function ebox_get_course_teams_users_access( $course_id = 0 ) {
	$user_ids = array();

	$course_id = absint( $course_id );
	if ( ! empty( $course_id ) ) {
		$course_teams = ebox_get_course_teams( $course_id );
		if ( ( is_array( $course_teams ) ) && ( ! empty( $course_teams ) ) ) {
			foreach ( $course_teams as $team_id ) {
				$team_users_ids = ebox_get_teams_user_ids( $team_id );
				if ( ! empty( $team_users_ids ) ) {
					$user_ids = array_merge( $user_ids, $team_users_ids );
				}
			}
		}
	}

	if ( ! empty( $user_ids ) ) {
		$user_ids = array_unique( $user_ids );
	}

	return $user_ids;
}

/**
 * Gets all quizzes related to Team Courses.
 *
 * Given a team ID will determine all quizzes associated with courses of the team
 *
 * @since 2.3.0
 *
 * @param int $team_id Optional. Team ID. Default 0.
 *
 * @return array An array of quiz IDs.
 */
function ebox_get_team_course_quiz_ids( $team_id = 0 ) {
	$team_quiz_ids = array();

	$team_id = absint( $team_id );
	if ( ! empty( $team_id ) ) {
		$team_course_ids = ebox_team_enrolled_courses( intval( $team_id ) );
		if ( ! empty( $team_course_ids ) ) {
			foreach ( $team_course_ids as $course_id ) {
				$team_quiz_query_args = array(
					'post_type'  => 'ebox-quiz',
					'nopaging'   => true,
					'fields'     => 'ids',
					'meta_query' => array(
						'relation' => 'OR',
						array(
							'key'     => 'course_id',
							'value'   => $course_id,
							'compare' => '=',
						),
						array(
							'key'     => 'ld_course_' . $course_id,
							'value'   => $course_id,
							'compare' => '=',
						),
					),
				);

				$team_quiz_query = new WP_Query( $team_quiz_query_args );
				if ( ! empty( $team_quiz_query->posts ) ) {
					$team_quiz_ids = array_merge( $team_quiz_ids, $team_quiz_query->posts );
					$team_quiz_ids = array_unique( $team_quiz_ids );
				}
			}
		}
	}

	return $team_quiz_ids;
}

/**
 * Check and recalculate the the status of the Team Courses for the User.
 *
 * @since 3.2.0
 *
 * @param integer $team_id Team ID to check.
 * @param integer $user_id  User ID to check.
 * @param boolean $recalc   Force the logic to recheck all courses.
 */
function ebox_get_user_team_progress( $team_id = 0, $user_id = 0, $recalc = false ) {
	static $progress_team_user = array();

	$team_id = absint( $team_id );
	$user_id  = absint( $user_id );

	if ( empty( $user_id ) ) {
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
		}
	}

	if ( ( empty( $team_id ) ) || ( empty( $user_id ) ) ) {
		return array();
	}

	if ( ! ebox_is_user_in_team( $user_id, $team_id ) ) {
		return array();
	}

	if ( ( isset( $progress_team_user[ $team_id ][ $user_id ] ) ) && ( ! empty( $progress_team_user[ $team_id ][ $user_id ] ) ) && ( true !== $recalc ) ) {
		return $progress_team_user[ $team_id ][ $user_id ];
	}

	$progress = array(
		'percentage'      => 0,
		'in-progress'     => 0,
		'not-started'     => 0,
		'completed'       => 0,
		'total'           => 0,
		'completed_on'    => 0,
		'started_on'      => 0,
		'course_ids'      => array(),
		'course_activity' => array(),
		'activity_id'     => 0,
		'team_activity'  => array(),
	);

	$team_user_activity_args = array(
		'activity_type' => 'team_progress',
		'user_id'       => $user_id,
		'post_id'       => $team_id,
		'course_id'     => 0,
	);

	$team_user_activity = ebox_get_user_activity( $team_user_activity_args );
	if ( is_object( $team_user_activity ) ) {
		$team_user_activity = json_decode( wp_json_encode( $team_user_activity ), true );
		if ( ( true === $team_user_activity['activity_status'] ) && ( true !== $recalc ) ) {
			$activity_meta = ebox_get_user_activity_meta( $team_user_activity['activity_id'] );
			if ( ( $activity_meta ) && ( ! empty( $activity_meta ) ) ) {

				foreach ( $activity_meta as $activity_set ) {
					if ( ( property_exists( $activity_set, 'activity_meta_key' ) ) && ( ! empty( $activity_set->activity_meta_key ) ) ) {
						if ( property_exists( $activity_set, 'activity_meta_value' ) ) {
							$meta[ $activity_set->activity_meta_key ] = $activity_set->activity_meta_value;
						} else {
							$meta[ $activity_set->activity_meta_key ] = '';
						}
					}
				}

				foreach ( $progress as $key => $val ) {
					switch ( $key ) {
						case 'percentage':
						case 'in-progress':
						case 'not-started':
						case 'completed':
						case 'total':
						case 'completed_on':
						case 'started_on':
							if ( isset( $meta[ $key ] ) ) {
								$progress[ $key ] = intval( $meta[ $key ] );
							}
							break;

						case 'team_activity':
							$progress[ $key ] = $team_user_activity;
							break;

						case 'activity_id':
							if ( isset( $team_user_activity['activity_id'] ) ) {
								$progress[ $key ] = absint( $team_user_activity['activity_id'] );
							}
							break;

						case 'course_ids':
						case 'course_activity':
						default:
							break;
					}
				}

				$progress_team_user[ $team_id ][ $user_id ] = $progress;
				return $progress;
			}
		}
	} else {
		$team_user_activity                    = $team_user_activity_args;
		$team_user_activity['changed']         = true;
		$team_user_activity['activity_status'] = 0;
	}

	$last_completed_course_time = 0;
	$last_started_course_time   = 0;
	$last_updated_course_time   = 0;

	$progress['course_ids'] = ebox_team_enrolled_courses( $team_id );
	if ( ! empty( $progress['course_ids'] ) ) {
		$progress['course_ids'] = array_map( 'absint', $progress['course_ids'] );
		$progress['total']      = count( $progress['course_ids'] );

		$team_courses_activity_args = array(
			'user_ids'       => $user_id,
			'post_types'     => ebox_get_post_type_slug( 'course' ),
			'activity_types' => 'course',
			'course_ids'     => $progress['course_ids'],
			'per_page'       => '',
		);

		$team_courses_activity = ebox_reports_get_activity( $team_courses_activity_args );
		if ( ( isset( $team_courses_activity['results'] ) ) && ( ! empty( $team_courses_activity['results'] ) ) ) {
			$progress['course_activity'] = array();
			foreach ( $team_courses_activity['results'] as $result ) {
				$result->activity_status    = absint( $result->activity_status );
				$result->activity_completed = absint( $result->activity_completed );
				$result->activity_started   = absint( $result->activity_started );
				$result->activity_updated   = absint( $result->activity_updated );

				$progress['course_activity'][ $result->activity_course_id ] = json_decode( wp_json_encode( $result ), true );

				if ( ( empty( $result->activity_started ) ) && ( ! empty( $result->activity_updated ) ) ) {
					$result->activity_started = $result->activity_updated;
				}

				if ( ( empty( $last_started_course_time ) ) || ( $result->activity_started < $last_started_course_time ) ) {
					$last_started_course_time = $result->activity_started;
				}

				if ( ( empty( $last_updated_course_time ) ) || ( $result->activity_updated < $last_updated_course_time ) ) {
					$last_updated_course_time = $result->activity_updated;
				}

				if ( ( 1 === $result->activity_status ) && ( ! empty( $result->activity_completed ) ) ) {
					$progress['completed']++;

					if ( $result->activity_completed > $last_completed_course_time ) {
						$last_completed_course_time = $result->activity_completed;
					}
				} elseif ( ! empty( $result->activity_started ) ) {
					$progress['in-progress']++;
				}
			}
		}
	}

	$progress['completed']   = absint( $progress['completed'] );
	$progress['total']       = absint( $progress['total'] );
	$progress['in-progress'] = absint( $progress['in-progress'] );
	$progress['not-started'] = $progress['total'] - $progress['completed'] - $progress['in-progress'];

	if ( ( ! empty( $progress['total'] ) ) && ( ! empty( $progress['completed'] ) ) ) {
		$progress['percentage'] = ceil( ( $progress['completed'] / $progress['total'] ) * 100 );
	} else {
		$progress['percentage'] = 0;
	}

	// Fire the Team Completed action. But after we add the activity record.
	$send_team_complete_action = false;

	if ( ( ! empty( $progress['total'] ) ) && ( $progress['total'] === $progress['completed'] ) ) {
		if ( true !== $team_user_activity['activity_status'] ) {
			$send_team_complete_action = true;
		}

		$team_user_activity['activity_status']    = true;
		$team_user_activity['activity_completed'] = absint( $last_completed_course_time );
		$progress['completed_on']                  = absint( $last_completed_course_time );
	} else {
		$team_user_activity['activity_status']    = false;
		$team_user_activity['activity_completed'] = 0;
	}

	$team_user_activity['activity_started'] = absint( $last_started_course_time );
	$progress['started_on']                  = absint( $last_started_course_time );

	$team_user_activity['activity_updated'] = absint( $last_updated_course_time );

	$team_user_activity['activity_meta'] = $progress;
	unset( $team_user_activity['activity_meta']['course_activity'] );
	unset( $team_user_activity['activity_meta']['team_activity'] );

	$progress['activity_id'] = ebox_update_user_activity( $team_user_activity );

	if ( true === $send_team_complete_action ) {
		/**
		 *
		 * Fires after the team is completed.
		 *
		 * @param array $team_data An array of team complete data.
		 */
		do_action(
			'ebox_team_completed',
			array(
				'user'            => get_user_by( 'id', $user_id ),
				'team'           => get_post( $team_id ),
				'progress'        => $progress,
				'team_completed' => $team_user_activity['activity_completed'],
			)
		);
	}

	$progress_team_user[ $team_id ][ $user_id ] = $progress;

	return $progress;
}

/**
 * Get User's team status
 *
 * @since 3.2.0
 *
 * @param int  $team_id Team ID.
 * @param int  $user_id  User ID.
 * @param bool $return_slug Optional. Default false.
 */
function ebox_get_user_team_status( $team_id = 0, $user_id = 0, $return_slug = false ) {
	$ebox_team_status_str = '';

	$team_id = absint( $team_id );
	$user_id  = absint( $user_id );

	if ( empty( $user_id ) ) {
		if ( ! is_user_logged_in() ) {
			return $ebox_team_status_str;
		}

		$user_id = get_current_user_id();
	} else {
		$user_id = absint( $user_id );
	}

	if ( ( empty( $team_id ) ) || ( empty( $user_id ) ) ) {
		return '';
	}

	$progress = ebox_get_user_team_progress( $team_id, $user_id );
	if ( ( ! empty( $progress ) ) && ( is_array( $progress ) ) && ( isset( $progress['percentage'] ) ) ) {
		if ( 100 === absint( $progress['percentage'] ) ) {
			if ( true === $return_slug ) {
				$ebox_team_status_str = 'completed';
			} else {
				$ebox_team_status_str = esc_html__( 'Completed', 'ebox' );
			}
		} elseif ( $progress['in-progress'] > 0 ) {
			if ( true === $return_slug ) {
				$ebox_team_status_str = 'in-progress';
			} else {
				$ebox_team_status_str = esc_html__( 'In Progress', 'ebox' );
			}
		}
	}

	if ( empty( $ebox_team_status_str ) ) {
		if ( true === $return_slug ) {
			$ebox_team_status_str = 'not-started';
		} else {
			$ebox_team_status_str = esc_html__( 'Not Started', 'ebox' );
		}
	}

	return $ebox_team_status_str;
}

/**
 * Get the user started team timestamp.
 *
 * @since 3.2.0
 *
 * @param  integer $team_id Team ID to check.
 * @param  integer $user_id  User ID to check.
 * @return integer time user started team courses.
 */
function ebox_get_user_team_started_timestamp( $team_id = 0, $user_id = 0 ) {
	$team_timestamp = 0;

	$team_id = absint( $team_id );
	$user_id  = absint( $user_id );

	if ( empty( $user_id ) ) {
		if ( ! is_user_logged_in() ) {
			return $team_timestamp;
		}

		$user_id = get_current_user_id();
	} else {
		$user_id = absint( $user_id );
	}

	if ( ( empty( $team_id ) ) || ( empty( $user_id ) ) ) {
		return '';
	}

	$progress = ebox_get_user_team_progress( $team_id, $user_id );
	if ( ( ! empty( $progress ) ) && ( is_array( $progress ) ) && ( isset( $progress['started_on'] ) ) ) {
		$team_timestamp = absint( $progress['started_on'] );
	}

	return $team_timestamp;
}

/**
 * Get the user completed team timestamp.
 *
 * @since 3.2.0
 *
 * @param  integer $team_id Team ID to check.
 * @param  integer $user_id  User ID to check.
 * @return integer time user started team courses.
 */
function ebox_get_user_team_completed_timestamp( $team_id = 0, $user_id = 0 ) {
	$team_timestamp = 0;

	$team_id = absint( $team_id );
	$user_id  = absint( $user_id );

	if ( empty( $user_id ) ) {
		if ( ! is_user_logged_in() ) {
			return $team_timestamp;
		}

		$user_id = get_current_user_id();
	} else {
		$user_id = absint( $user_id );
	}

	if ( ( empty( $team_id ) ) || ( empty( $user_id ) ) ) {
		return '';
	}

	$progress = ebox_get_user_team_progress( $team_id, $user_id );
	if ( ( ! empty( $progress ) ) && ( is_array( $progress ) ) && ( isset( $progress['completed_on'] ) ) ) {
		$team_timestamp = absint( $progress['completed_on'] );
	}

	return $team_timestamp;
}

/**
 * Get the user completed team percentage.
 *
 * @since 3.2.0
 *
 * @param  integer $team_id Team ID to check.
 * @param  integer $user_id  User ID to check.
 * @return integer time user started team courses.
 */
function ebox_get_user_team_completed_percentage( $team_id = 0, $user_id = 0 ) {
	$team_percentage = 0;

	$team_id = absint( $team_id );
	$user_id  = absint( $user_id );

	if ( empty( $user_id ) ) {
		if ( ! is_user_logged_in() ) {
			return $team_percentage;
		}

		$user_id = get_current_user_id();
	} else {
		$user_id = absint( $user_id );
	}

	if ( ( empty( $team_id ) ) || ( empty( $user_id ) ) ) {
		return '';
	}

	$progress = ebox_get_user_team_progress( $team_id, $user_id );
	if ( ( ! empty( $progress ) ) && ( is_array( $progress ) ) && ( isset( $progress['percentage'] ) ) ) {
		$team_percentage = $progress['percentage'];
	}

	return $team_percentage;
}

/**
 * Hook into the User Course Complete action.
 *
 * When the user completes a Course we check if that course
 * is part of any team the user is enrolled into.
 *
 * @since 3.2.0
 *
 * @param array $course_data Array of course data.
 */
function ebox_team_course_completed( $course_data = array() ) {

	if ( ( isset( $course_data['course'] ) ) && ( isset( $course_data['user'] ) ) ) {
		ebox_update_team_course_user_progress( $course_data['course']->ID, $course_data['user']->ID, true );
	}
}
add_action( 'ebox_course_completed', 'ebox_team_course_completed', 30, 1 );


/**
 * Update Team User Course progress.
 *
 * @since 3.2.0
 *
 * @param integer $course_id Course ID.
 * @param integer $user_id   User ID.
 * @param boolean $recalc    Force the logic to recheck all courses.
 */
function ebox_update_team_course_user_progress( $course_id = 0, $user_id = 0, $recalc = false ) {
	$course_id = absint( $course_id );
	$user_id   = absint( $user_id );

	if ( ( ! empty( $user_id ) ) && ( ! empty( $course_id ) ) ) {
		$user_team_ids = ebox_get_users_team_ids( $user_id );
		if ( empty( $user_team_ids ) ) {
			return;
		}

		$course_team_ids = ebox_get_course_teams( $course_id );
		if ( empty( $course_team_ids ) ) {
			return;
		}

		$team_ids = array_intersect( $user_team_ids, $course_team_ids );
		if ( ! empty( $team_ids ) ) {
			foreach ( $team_ids as $team_id ) {
				ebox_get_user_team_progress( $team_id, $user_id, $recalc );
			}
		}
	}
}

/**
 * Utility function to return all teams below the parent.
 *
 * @since 3.2.0
 *
 * @param integer $team_id Team parent ID.
 * @return array of children teams IDs.
 */
function ebox_get_team_children( $team_id = 0 ) {
	$team_children = array();

	$team_id = absint( $team_id );
	if ( ! empty( $team_id ) ) {

		$child_args = array(
			'post_parent' => $team_id, // The parent id.
			'post_type'   => ebox_get_post_type_slug( 'team' ),
		);

		$children = get_children( $child_args );
		if ( ! empty( $children ) ) {
			foreach ( $children as $child_team ) {
				$team_children[] = $child_team->ID;
				$children2        = ebox_get_team_children( $child_team->ID );
				if ( ! empty( $children2 ) ) {
					$team_children = array_merge( $team_children, $children2 );
				}
			}
		}
	}

	if ( ! empty( $team_children ) ) {
		$team_children = array_map( 'absint', $team_children );
		$team_children = array_unique( $team_children, SORT_NUMERIC );
	}

	return $team_children;
}

/**
 * Validate an array of Team post IDs.
 *
 * @param array $team_ids Array of Teams post IDs to check.
 * @return array validated Team post IDS.
 */
function ebox_validate_teams( $team_ids = array() ) {
	if ( ( is_array( $team_ids ) ) && ( ! empty( $team_ids ) ) ) {
		$teams_query_args = array(
			'post_type'      => ebox_get_post_type_slug( 'team' ),
			'fields'         => 'ids',
			'orderby'        => 'title',
			'order'          => 'ASC',
			'post__in'       => $team_ids,
			'posts_per_page' => -1,
		);

		$teams_query = new WP_Query( $teams_query_args );
		if ( ( is_a( $teams_query, 'WP_Query' ) ) && ( property_exists( $teams_query, 'posts' ) ) ) {
			return $teams_query->posts;
		}
	}

	return array();
}

/**
 * Gets the team courses per page setting.
 *
 * @since 3.2.0
 *
 * @param int $team_id Optional. The ID of the team. Default 0.
 *
 * @return int The number of modules per page or 0.
 */
function ebox_get_team_courses_per_page( $team_id = 0 ) {
	$team_courses_per_page = 0;

	// From the WP > Settings > Reading > Posts per page.
	$team_courses_per_page = (int) get_option( 'posts_per_page' );

	// From the ebox > Settings > General > Global Pagination Settings > Shortcodes & Widgets per page.
	$team_courses_per_page = ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_General_Per_Page', 'per_page', $team_courses_per_page );

	// From the ebox > Courses > Settings > Global Team Management > Team Table Pagination > Courses per page.
	$team_global_settings = ebox_Settings_Section::get_section_settings_all( 'ebox_Settings_Teams_Management_Display' );
	if ( ( isset( $team_global_settings['team_pagination_enabled'] ) ) && ( 'yes' === $team_global_settings['team_pagination_enabled'] ) ) {
		if ( isset( $team_global_settings['team_pagination_courses'] ) ) {
			$team_courses_per_page = absint( $team_global_settings['team_pagination_courses'] );
		} else {
			$team_courses_per_page = ebox_LMS_DEFAULT_WIDGET_PER_PAGE;
		}
	} else {
		$team_courses_per_page = ebox_LMS_DEFAULT_WIDGET_PER_PAGE;
	}

	if ( ! empty( $team_id ) ) {
		$team_settings = ebox_get_setting( intval( $team_id ) );

		if ( ( isset( $team_settings['team_courses_per_page_enabled'] ) ) && ( 'CUSTOM' === $team_settings['team_courses_per_page_enabled'] ) && ( isset( $team_settings['team_courses_per_page_custom'] ) ) ) {
			$team_courses_per_page = absint( $team_settings['team_courses_per_page_custom'] );
		}
	}

	/**
	 * Filters team courses per page.
	 *
	 * @since 3.2.0
	 *
	 * @param int $team_courses_per_page Per page value.
	 * @param int $team_id               Team ID.
	 */
	return apply_filters( 'ebox_team_courses_per_page', $team_courses_per_page, $team_id );
}

/**
 * Gets the team courses order query arguments.
 *
 * @since 3.2.0
 *
 * @param int $team_id Optional. The ID of the team. Default 0.
 *
 * @return array An array of team courses order query arguments.
 */
function ebox_get_team_courses_order( $team_id = 0 ) {
	$team_courses_args = array(
		'order'   => ebox_DEFAULT_GROUP_ORDER,
		'orderby' => ebox_DEFAULT_GROUP_ORDERBY,
	);

	$team_global_settings = ebox_Settings_Section::get_section_settings_all( 'ebox_Settings_Teams_Management_Display' );
	if ( ( isset( $team_global_settings['team_courses_orderby'] ) ) && ( ebox_DEFAULT_GROUP_ORDERBY !== $team_global_settings['team_courses_orderby'] ) ) {
		$team_courses_args['orderby'] = esc_attr( $team_global_settings['team_courses_orderby'] );
	}
	if ( ( isset( $team_global_settings['team_courses_order'] ) ) && ( ebox_DEFAULT_GROUP_ORDER !== $team_global_settings['team_courses_order'] ) ) {
		$team_courses_args['order'] = esc_attr( $team_global_settings['team_courses_order'] );
	}

	if ( ! empty( $team_id ) ) {
		$team_settings = ebox_get_setting( $team_id );
		if ( ( isset( $team_settings['team_courses_order_enabled'] ) ) && ( 'on' === $team_settings['team_courses_order_enabled'] ) ) {
			if ( ( isset( $team_settings['team_courses_order'] ) ) && ( ! empty( $team_settings['team_courses_order'] ) ) ) {
				$team_courses_args['order'] = esc_attr( $team_settings['team_courses_order'] );
			}

			if ( ( isset( $team_settings['team_courses_orderby'] ) ) && ( ! empty( $team_settings['team_courses_orderby'] ) ) ) {
				$team_courses_args['orderby'] = esc_attr( $team_settings['team_courses_orderby'] );
			}
		}
	}

	/**
	 * Filters team courses order query arguments.
	 *
	 * @since 3.2.0
	 *
	 * @param array $team_courses_args An array of team courses order arguments.
	 * @param int   $team_id          Team ID.
	 */
	return apply_filters( 'ebox_team_courses_order', $team_courses_args, $team_id );
}


/**
 * Gets the list of enrolled courses for a team.
 *
 * @since 2.1.0
 * @since 4.0.0 Added `$query_args` parameter.
 *
 * @param int   $team_id   Optional. Team ID. Default 0.
 * @param array $query_args Optional. An array of query arguments to get lesson list. Default empty array. (@since 4.0.0).
 *
 * @return array An array of course IDs.
 */
function ebox_get_team_courses_list( $team_id = 0, $query_args = array() ) {
	global $course_pager_results;

	$courses_ids = array();

	$team_id = absint( $team_id );
	if ( ! empty( $team_id ) ) {

		if ( ! isset( $query_args['paged'] ) ) {
			$query_args['paged'] = 1;
			if ( isset( $_GET['ld-team-courses-page'] ) ) {
				$query_args['paged'] = absint( $_GET['ld-team-courses-page'] );
			}
		}

		if ( isset( $query_args['num'] ) ) {
			$query_args['per_page'] = intval( $query_args['num'] );
			unset( $query_args['num'] );
		}

		if ( isset( $query_args['posts_per_page'] ) ) {
			if ( ( ! isset( $query_args['per_page'] ) ) || ( empty( $query_args['per_page'] ) ) ) {
				$query_args['per_page'] = intval( $query_args['posts_per_page'] );
			}
			unset( $query_args['posts_per_page'] );
		}

		if ( ! isset( $query_args['per_page'] ) ) {
			$query_args['per_page'] = ebox_get_team_courses_per_page( $team_id );
		}
		$team_courses_order_args = ebox_get_team_courses_order( $team_id );

		$query_args = array(
			'post_type'      => ebox_get_post_type_slug( 'course' ),
			'fields'         => 'ids',
			'posts_per_page' => $query_args['per_page'],
			'paged'          => $query_args['paged'],
			'meta_query'     => array(
				array(
					'key'     => 'ebox_team_enrolled_' . $team_id,
					'compare' => 'EXISTS',
				),
			),
		);
		$query_args = array_merge( $query_args, $team_courses_order_args );

		$query = new WP_Query( $query_args );
		if ( ( is_a( $query, 'WP_Query' ) ) && ( property_exists( $query, 'posts' ) ) ) {
			$course_ids = $query->posts;

			if ( ! isset( $course_pager_results['pager'] ) ) {
				$course_pager_results['pager'] = array();
			}
			$course_pager_results['pager']['paged']       = $query_args['paged'];
			$course_pager_results['pager']['total_items'] = $query->found_posts;
			$course_pager_results['pager']['total_pages'] = $query->max_num_pages;
		}
	}

	return $course_ids;
}

/**
 * Utility function to check if Teams post type is hierarchical.
 *
 * @since 3.2.1
 *
 * @return bool Returns true if hierarchical.
 */
function ebox_is_teams_hierarchical_enabled() {
	$team_hierarchical_enabled = ebox_Settings_Section::get_section_setting( 'ebox_Settings_Teams_Management_Display', 'team_hierarchical_enabled' );
	if ( 'yes' === $team_hierarchical_enabled ) {
		$team_hierarchical_enabled = true;
	} else {
		$team_hierarchical_enabled = false;
	}

	return $team_hierarchical_enabled;
}

/**
 * Get all Courses having Team associations.
 *
 * @since 3.2.3
 * @return array Array of Course ID or empty array.
 */
function ebox_get_all_courses_with_teams() {
	$query_args = array(
		'post_type'      => ebox_get_post_type_slug( 'course' ),
		'fields'         => 'ids',
		'posts_per_page' => -1,
		'meta_query'     => array(
			array(
				'key'     => '[LD_XXX_GROUP_LIKE_FILTER]',
				'compare' => 'EXISTS',
			),
		),
	);

	add_filter( 'posts_where', 'ebox_filter_by_team_where_filter' );
	$query = new WP_Query( $query_args );
	remove_filter( 'posts_where', 'ebox_filter_by_team_where_filter' );
	if ( ( is_a( $query, 'WP_Query' ) ) && ( property_exists( $query, 'posts' ) ) ) {
		return $query->posts;
	}

	return array();
}

/**
 * Filter by team WHERE filter
 *
 * @since 3.2.3
 *
 * @param string $where WHERE clause.
 */
function ebox_filter_by_team_where_filter( $where ) {
	if ( false !== strpos( $where, '[LD_XXX_GROUP_LIKE_FILTER]' ) ) {
		return str_replace( "meta_key = '[LD_XXX_GROUP_LIKE_FILTER]'", "meta_key LIKE 'ebox_team_enrolled_%'", $where );
	}
}

/**
 * Utility function to check if a Team Leader can manage Teams.
 *
 * @since 3.2.3
 */
function ebox_get_team_leader_manage_teams() {
	if ( 'yes' === ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_Teams_Team_Leader_User', 'manage_teams_enabled' ) ) {
		return ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_Teams_Team_Leader_User', 'manage_teams_capabilities' );
	}
}

/**
 * Utility function to check if a Team Leader can manage Courses.
 *
 * @since 3.2.3
 */
function ebox_get_team_leader_manage_courses() {
	if ( 'yes' === ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_Teams_Team_Leader_User', 'manage_courses_enabled' ) ) {
		return ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_Teams_Team_Leader_User', 'manage_courses_capabilities' );
	}
}

/**
 * Utility function to check if a Team Leader can manage Users.
 *
 * @since 3.2.3
 */
function ebox_get_team_leader_manage_users() {
	if ( 'yes' === ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_Teams_Team_Leader_User', 'manage_users_enabled' ) ) {
		return ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_Teams_Team_Leader_User', 'manage_users_capabilities' );
	}
}

/**
 * Check if the Team Leader can edit the Team or Course posts.
 *
 * Override the default WordPress user capability when editing a Team.
 * See wp-includes/class-wp-user.php for details.
 *
 * @since 3.2.3
 *
 * @param bool|array   $allcaps Array of key/value pairs where keys represent a capability name
 *                              and boolean values represent whether the user has that capability.
 * @param string|array $cap     Required primitive capabilities for the requested capability.
 * @param array        $args    Additional arguments.
 * @param WP_User      $user    WP_User object.
 */
function ebox_team_leader_has_cap_filter( $allcaps, $cap, $args, $user ) {

	global $pagenow;

	if ( in_array( 'edit_posts', $cap, true ) ) {
		/**
		 * If the Team Leader is attempting to manage a comment we enable that
		 * IF they are viewing the comments for an Assignment or Essay.
		 * At this point we are not concerned about other LD post types.
		 */
		if ( ( 'edit-comments.php' === $pagenow ) && ( isset( $_GET['p'] ) ) ) {
			$comment_post = get_post( absint( $_GET['p'] ) );
			if ( ( $comment_post ) && ( is_a( $comment_post, 'WP_Post' ) ) && ( in_array( $comment_post->post_type, array( ebox_get_post_type_slug( 'assignment' ), ebox_get_post_type_slug( 'essay' ) ), true ) ) ) {
				$course_id = get_post_meta( $comment_post->ID, 'course_id', true );
				$course_id = absint( $course_id );
				if ( ( ! empty( $course_id ) ) && ( ebox_check_team_leader_course_user_intersect( get_current_user_id(), $comment_post->post_author, $course_id ) ) ) {
					foreach ( $cap as $cap_slug ) {
						$allcaps[ $cap_slug ] = true;
					}

					return $allcaps;
				}
			}
		}

		if ( in_array( ebox_get_team_leader_manage_courses(), array( 'basic', 'advanced' ), true ) ) {
			/** This filter is documented in includes/ld-teams.php */
			if ( apply_filters( 'ebox_team_leader_has_cap_filter', true, $cap, $args, $user ) ) {
				if ( ! isset( $args[2] ) ) {
					$post_id = get_the_id();
					if ( $post_id ) {
						if ( ( in_array( get_post_type( $post_id ), ebox_get_post_type_slug( array( 'course', 'lesson', 'topic', 'quiz', 'team' ) ), true ) ) ) {
							$args[2] = $post_id;
						}
					}
				}

				if ( ( isset( $args[2] ) ) && ( ! empty( $args[2] ) ) ) {
					foreach ( $cap as $cap_slug ) {
						$allcaps[ $cap_slug ] = true;
					}
				} elseif ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
					// Total KLUDGE. When editing in Gutenberg there is a call to /wp/v2/blocks with 'edit' context.
					$route = untrailingslashit( $GLOBALS['wp']->query_vars['rest_route'] );
					if ( '/wp/v2/blocks' === $route ) {
						foreach ( $cap as $cap_slug ) {
							$allcaps[ $cap_slug ] = true;
						}
					}
				}
			}
		}
	} elseif ( in_array( 'edit_others_teams', $cap, true ) ) { // Check if Team Leader can edit Teams they are Leader of.
		if ( ( ! isset( $allcaps['edit_others_teams'] ) ) || ( true !== $allcaps['edit_others_teams'] ) ) {
			if ( 'basic' === ebox_get_team_leader_manage_teams() ) {
				/**
				 * Filter override for Team Leader edit cap.
				 *
				 * @since 3.2.3
				 *
				 * @param bool     $true Always True if user can edit post.
				 * @param bool[]   $allcaps Array of key/value pairs where keys represent a capability name
				 *                          and boolean values represent whether the user has that capability.
				 * @param array    $args {
				 *     @type string    $index_0 Requested capability.
				 *     @type int       $index_1 Concerned user ID.
				 *     @type mixed  ...$other   Optional second and further parameters, typically object ID.
				 * } Arguments that accompany the requested capability check.
				 * @param WP_User  $user    The user object.
				 *
				 * @return bool True if Team Leader is allowed to edit post.
				 */
				if ( apply_filters( 'ebox_team_leader_has_cap_filter', true, $cap, $args, $user ) ) {
					/**
					 * During the save post cycle the args[2] is empty. So we can't check if the GL can edit a specific
					 * Team ID. But if we find the 'action' and 'post_ID' POST vars we can check indirectly.
					 */
					if ( ! isset( $args[2] ) ) {
						if ( ( isset( $_POST['action'] ) ) && ( 'editpost' === $_POST['action'] ) ) {
							if ( isset( $_POST['post_ID'] ) ) {
								$args[2] = absint( $_POST['post_ID'] );
							}
						}
					}

					if ( ( isset( $args[2] ) ) && ( in_array( get_post_type( $args[2] ), array( ebox_get_post_type_slug( 'team' ) ), true ) ) ) {
						if ( ( isset( $args[1] ) ) && ( ! empty( $args[1] ) ) ) {
							$gl_team_ids = ebox_get_administrators_team_ids( absint( $args[1] ) );
							if ( ( ! empty( $gl_team_ids ) ) && ( in_array( absint( $args[2] ), $gl_team_ids, true ) ) ) {
								foreach ( $cap as $cap_slug ) {
									$allcaps[ $cap_slug ] = true;
								}
							}
						}
					}
				}
			}
		}
	} // phpcs:ignore Squiz.ControlStructures.ControlSignature.SpaceAfterCloseBrace -- Explanatory comment follows
	// Check if Team Leader can edit Course or Steps within their Teams.
	elseif ( ( ( in_array( 'edit_others_courses', $cap, true ) ) ) && ( isset( $allcaps['edit_others_courses'] ) ) && ( true !== $allcaps['edit_others_courses'] ) ) {
		if ( 'basic' === ebox_get_team_leader_manage_courses() ) {

			/** This filter is documented in includes/ld-teams.php */
			if ( apply_filters( 'ebox_team_leader_has_cap_filter', true, $cap, $args, $user ) ) {
				/**
				 * During the save post cycle the args[2] is empty. So we can't check if the GL can edit a specific
				 * Course Post ID. But if we find the 'action' and 'post_ID' POST vars we can check indirectly.
				 */
				if ( ! isset( $args[2] ) ) {
					if ( ( isset( $_POST['action'] ) ) && ( 'editpost' === $_POST['action'] ) ) {
						if ( isset( $_POST['post_ID'] ) ) {
							$args[2] = absint( $_POST['post_ID'] );
						}
					}
				}

				if ( ( isset( $args[2] ) ) && ( in_array( get_post_type( $args[2] ), ebox_get_post_types( 'course' ), true ) ) ) {
					if ( get_post_type( $args[2] ) === ebox_get_post_type_slug( 'course' ) ) {
						$courses = array( $args[2] );
					} else {
						$courses = ebox_get_courses_for_step( $args[2], true );
						$courses = array_keys( $courses );
					}

					$leader_team_ids = array();
					if ( ( isset( $args[1] ) ) && ( ! empty( $args[1] ) ) ) {
						$leader_team_ids = ebox_get_administrators_team_ids( absint( $args[1] ) );
					}

					if ( ! empty( $leader_team_ids ) ) {

						$course_team_ids = array();
						foreach ( $courses as $course_id ) {
							$course_team_ids = array_merge( $course_team_ids, ebox_get_course_teams( absint( $course_id ) ) );
						}

						if ( ( ! empty( $leader_team_ids ) ) && ( ! empty( $course_team_ids ) ) ) {
							$common_course_ids = array_intersect( $leader_team_ids, $course_team_ids );
							if ( ! empty( $common_course_ids ) ) {
								$include_caps = true;
								if ( true === $include_caps ) {
									foreach ( $cap as $cap_slug ) {
										$allcaps[ $cap_slug ] = true;
									}
								}
							}
						}
					}
				}
			}
		}
	}

	return $allcaps;
}
add_action(
	'init',
	function () {
		if ( ebox_is_team_leader_user() ) {
			add_filter( 'user_has_cap', 'ebox_team_leader_has_cap_filter', 10, 4 );
		}
	},
	10
);

/**
 * Check if the Team Leader AND User and Course have common Teams.
 *
 * @since 3.4.0
 *
 * @param int $gl_user_id Team Leader User ID.
 * @param int $user_id    User ID.
 * @param int $course_id  Course ID.
 *
 * @return bool true if a common team intersect is determined.
 */
function ebox_check_team_leader_course_user_intersect( $gl_user_id = 0, $user_id = 0, $course_id = 0 ) {

	if ( ( empty( $gl_user_id ) ) || ( empty( $user_id ) ) || ( empty( $course_id ) ) ) {
		return false;
	}

	if ( ! ebox_is_team_leader_user( $gl_user_id ) ) {
		return false;
	}

	$common_team_ids = array();
	// And that the Course is associated with some Teams.
	$course_team_ids = ebox_get_course_teams( $course_id );
	$course_team_ids = array_map( 'absint', $course_team_ids );
	if ( ! empty( $course_team_ids ) ) {
		/**
		 * If the Team Leader can manage all Users or all Teams then return. Note
		 * we are performing this check AFTER we check if the Course is part of a
		 * Team. This is on purpose.
		 */
		if ( ( 'advanced' === ebox_get_team_leader_manage_users() ) || ( 'advanced' === ebox_get_team_leader_manage_teams() ) ) {
			return true;
		}

		// Now check the Team Leader managed Teams...
		$leader_team_ids = ebox_get_administrators_team_ids( $gl_user_id );
		$leader_team_ids = array_map( 'absint', $leader_team_ids );
		if ( ! empty( $leader_team_ids ) ) {
			// ...and the user (post author) Teams...
			$author_team_ids = ebox_get_users_team_ids( $user_id );
			$author_team_ids = array_map( 'absint', $author_team_ids );

			// ...and the course teams have an intersect.
			$common_team_ids = array_intersect( $leader_team_ids, $course_team_ids, $author_team_ids );
			$common_team_ids = array_map( 'absint', $common_team_ids );
		}
	}

	if ( ! empty( $common_team_ids ) ) {
		return true;
	}

	return false;
}

/**
 * Returns message if teams are not public in the admin dashboard
 *
 * @since 3.4.2
 */
function ebox_teams_get_not_public_message() {
	$teams_setting_link = '<a href="' . esc_url( add_query_arg( array( 'page' => 'teams-options' ), admin_url( 'admin.php' ) ) . '#ebox_settings_teams_cpt_cpt_options' ) . '">' . esc_html__( 'Settings', 'ebox' ) . '</a>';

	// translators: placeholders: Teams, link to Team settings page.
	$message = '<div class="notice notice-error is-dismissible"><p>' . sprintf( esc_html_x( '%1$s are not public, please visit the %2$s page and set them to Public to enable access on the front end.', 'placeholders: Teams, link to Team settings page', 'ebox' ), esc_html( ebox_get_custom_label( 'teams' ) ), $teams_setting_link ) . '</p></div>';

	/**
	 * Filters teams not set to Public message
	 *
	 * @since 3.4.2
	 *
	 * @param string $message The message when teams are not set to Public
	 * @return string $message The message when teams are not set to Public
	 */
	return apply_filters( 'ebox_teams_get_not_public_message', $message );
}

/**
 * Returns true if it's a team post.
 *
 * @param WP_Post|int|null $post Post or Post ID.
 *
 * @since 4.1.0
 *
 * @return bool
 */
function ebox_is_team_post( $post ): bool {
	if ( empty( $post ) ) {
		return false;
	}

	$post_type = is_a( $post, WP_Post::class ) ? $post->post_type : get_post_type( $post );

	return LDLMS_Post_Types::get_post_type_slug( 'team' ) === $post_type;
}

/**
 * Returns team enrollment url.
 *
 * @param WP_Post|int|null $post Post or Post ID.
 *
 * @since 4.1.0
 *
 * @return string
 */
function ebox_get_team_enrollment_url( $post ): string {
	if ( empty( $post ) ) {
		return '';
	}

	if ( is_int( $post ) ) {
		$post = get_post( $post );

		if ( is_null( $post ) ) {
			return '';
		}
	}

	$url = get_permalink( $post );

	$settings = ebox_get_setting( $post );

	if ( 'paynow' === $settings['team_price_type'] && ! empty( $settings['team_price_type_paynow_enrollment_url'] ) ) {
		$url = $settings['team_price_type_paynow_enrollment_url'];
	} elseif ( 'subscribe' === $settings['team_price_type'] && ! empty( $settings['team_price_type_subscribe_enrollment_url'] ) ) {
		$url = $settings['team_price_type_subscribe_enrollment_url'];
	}

	/** This filter is documented in includes/course/ld-course-functions.php */
	return apply_filters( 'ebox_team_join_redirect', $url, $post->ID );
}

/**
 * Deletes team leader metadata when a team leader role is changed to another.
 *
 * @since 4.5.0
 */
add_action(
	'set_user_role',
	function( int $user_id, string $role, array $old_roles ) {
		if (
			in_array( 'team_leader', $old_roles, true )
			&& 'team_leader' !== $role
		) {
			global $wpdb;

			$wpdb->query(
				$wpdb->prepare(
					"DELETE FROM {$wpdb->usermeta} WHERE user_id = %d AND meta_key LIKE %s",
					$user_id,
					'ebox_team_leaders_%'
				)
			);
		}
	},
	10,
	3
);
