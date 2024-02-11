<?php
/**
 * Deprecated functions from LD 3.4.1
 * The functions will be removed in a later version.
 *
 * @package ebox\Deprecated
 * @since 3.4.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'ebox_assignment_migration' ) ) {
	/**
	 * Migrates the assignments from post meta to assignments custom post type.
	 *
	 * Fires on `admin_init` hook.
	 *
	 * @since 2.1.0
	 * @deprecated 3.4.1
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @since 2.1.0
	 */
	function ebox_assignment_migration() {

		if ( ! ebox_is_admin_user() ) {
			return;
		}

		if ( function_exists( '_deprecated_function' ) ) {
			_deprecated_function( __FUNCTION__, '3.4.1' );
		}

		global $wpdb;
		$old_assignment_ids = $wpdb->get_col( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'ebox_modules-assignment'" );

		if ( ! empty( $old_assignment_ids ) && ! empty( $old_assignment_ids[0] ) ) {

			foreach ( $old_assignment_ids as $post_id ) {
				$assignment_meta_data = get_post_meta( $post_id, 'ebox_modules-assignment', true );

				if ( ! empty( $assignment_meta_data ) && ! empty( $assignment_meta_data['assignment'] ) ) {
					$assignment_data      = $assignment_meta_data['assignment'];
					$post                 = get_post( $post_id );
					$assignment_posts_ids = array();

					if ( ! empty( $assignment_data ) ) {
						$error = false;

						foreach ( $assignment_data as $k => $v ) {

							if ( empty( $v['file_name'] ) ) {
								continue;
							}

							$fname     = $v['file_name'];
							$dest      = $v['file_link'];
							$username  = $v['user_name'];
							$dispname  = $v['disp_name'];
							$file_path = $v['file_path'];

							$user_id = 0;
							if ( ! empty( $v['user_name'] ) ) {
								$user = get_user_by( 'login', $v['user_name'] );
								if ( ( $user ) && ( is_a( $user, 'WP_User' ) ) ) {
									$user_id = $user->ID;
								}
							}

							$course_id = ebox_get_course_id( $post->ID );

							$assignment_meta = array(
								'file_name'    => $fname,
								'file_link'    => $dest,
								'user_name'    => $username,
								'disp_name'    => $dispname,
								'file_path'    => $file_path,
								'user_id'      => $user_id,
								'lesson_id'    => $post->ID,
								'course_id'    => $course_id,
								'lesson_title' => $post->post_title,
								'lesson_type'  => $post->post_type,
								'migrated'     => '1',
							);

							$assignment = array(
								'post_title'   => $fname,
								'post_type'    => ebox_get_post_type_slug( 'assignment' ),
								'post_status'  => 'publish',
								'post_content' => "<a href='" . $dest . "' target='_blank'>" . $fname . '</a>',
								'post_author'  => $user_id,
							);

							$assignment_post_id = wp_insert_post( $assignment );

							if ( $assignment_post_id ) {
								$assignment_posts_ids[] = $assignment_post_id;

								foreach ( $assignment_meta as $key => $value ) {
									update_post_meta( $assignment_post_id, $key, $value );
								}

								if ( ebox_is_assignment_approved( $assignment_post_id ) === true ) {
									ebox_approve_assignment_by_id( $assignment_post_id );
								}
							} else {
								$error = true;

								foreach ( $assignment_posts_ids as $assignment_posts_id ) {
									wp_delete_post( $assignment_posts_id, true );
								}

								break;
							}
						}

						if ( ! $error ) {
							global $wpdb;
							$wpdb->query(
								$wpdb->prepare(
									"UPDATE $wpdb->postmeta SET meta_key = %s WHERE meta_key = %s AND post_id = %d",
									'ebox_modules-assignment_migrated',
									'ebox_modules-assignment',
									$post_id
								)
							);
						}
					}
				}
			}
		}
	}
}

if ( ! function_exists( 'ebox_get_assignments_list' ) ) {
	/**
	 * Gets the list of all assignments.
	 *
	 * @todo  first argument not used
	 * @since 2.1.0
	 * @deprecated 3.4.1
	 *
	 * @param WP_Post $post WP_Post object( Not used ).
	 *
	 * @return array An array of post objects.
	 */
	function ebox_get_assignments_list( $post ) {

		if ( function_exists( '_deprecated_function' ) ) {
			_deprecated_function( __FUNCTION__, '3.4.1' );
		}

		$posts = get_posts( 'post_type=ebox-assignment&posts_per_page=-1' );

		if ( ! empty( $posts ) ) {

			foreach ( $posts as $key => $p ) {
				$meta = get_post_meta( $p->ID, '', true );

				foreach ( $meta as $meta_key => $value ) {

					if ( is_string( $value ) || is_numeric( $value ) ) {
						$posts[ $key ]->{$meta_key} = $value;
					} elseif ( is_string( $value[0] ) || is_numeric( $value[0] ) ) {
						$posts[ $key ]->{$meta_key} = $value[0];
					}

					if ( 'file_path' === $meta_key ) {
						$posts[ $key ]->{$meta_key} = rawurldecode( $posts[ $key ]->{$meta_key} );
					}
				}
			}
		}

		return $posts;
	}
}

if ( ! function_exists( 'ebox_all_team_leader_ids' ) ) {
	/**
	 * Gets the list of all team leader user IDs.
	 *
	 * @since 2.1.2
	 * @deprecated 3.4.1
	 *
	 * @return array An array of team leader user IDs.
	 */
	function ebox_all_team_leader_ids() {
		if ( function_exists( '_deprecated_function' ) ) {
			_deprecated_function( __FUNCTION__, '3.4.1' );
		}

		$team_leader_user_ids = array();
		$team_leader_users    = ebox_all_team_leaders();
		if ( ! empty( $team_leader_users ) ) {
			$team_leader_user_ids = wp_list_pluck( $team_leader_users, 'ID' );
		}
		return $team_leader_user_ids;
	}
}

if ( ! function_exists( 'ebox_all_team_leaders' ) ) {
	/**
	 * Gets the list of all team leader user objects.
	 *
	 * @since 2.1.2
	 * @deprecated 3.4.1
	 *
	 * @return array An array of team leaders user objects.
	 */
	function ebox_all_team_leaders() {
		if ( function_exists( '_deprecated_function' ) ) {
			_deprecated_function( __FUNCTION__, '3.4.1' );
		}

		$transient_key      = 'ebox_team_leaders';
		$team_user_objects = LDLMS_Transients::get( $transient_key );
		if ( false === $team_user_objects ) {

			$user_query_args = array(
				'role'    => 'team_leader',
				'orderby' => 'display_name',
				'order'   => 'ASC',
			);

			$user_query = new WP_User_Query( $user_query_args );
			if ( isset( $user_query->results ) ) {
				$team_user_objects = $user_query->results;
			} else {
				$team_user_objects = array();
			}

			LDLMS_Transients::set( $transient_key, $team_user_objects, MINUTE_IN_SECONDS );
		}
		return $team_user_objects;
	}
}
