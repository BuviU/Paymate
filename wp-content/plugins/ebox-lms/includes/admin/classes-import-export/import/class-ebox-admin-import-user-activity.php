<?php
/**
 * ebox Admin Import User Activity.
 *
 * @since 4.3.0
 *
 * @package ebox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (
	class_exists( 'ebox_Admin_Import' ) &&
	trait_exists( 'ebox_Admin_Import_Export_User_Activity' ) &&
	! class_exists( 'ebox_Admin_Import_User_Activity' )
) {
	/**
	 * Class ebox Admin Import User Activity.
	 *
	 * @since 4.3.0
	 */
	class ebox_Admin_Import_User_Activity extends ebox_Admin_Import {
		use ebox_Admin_Import_Export_User_Activity;

		/**
		 * Imports user activity.
		 *
		 * @since 4.3.0
		 *
		 * @return void
		 */
		protected function import(): void {
			$old_new_statistic_ref_id_hash = get_transient( ebox_Admin_Import::TRANSIENT_KEY_STATISTIC_REF_IDS );
			$old_new_statistic_ref_id_hash = is_array( $old_new_statistic_ref_id_hash ) ? $old_new_statistic_ref_id_hash : array();

			$old_user_id_new_user_id_hash = $this->get_old_user_id_new_user_id_hash();

			foreach ( $this->get_file_lines() as $item ) {
				$this->processed_items_count++;

				$args                    = $item;
				$args['activity_action'] = 'insert';
				$args['user_id']         = $old_user_id_new_user_id_hash[ $args['user_id'] ] ?? null;

				if ( is_null( $args['user_id'] ) ) {
					continue;
				}

				$args['post_id']   = $this->get_new_post_id_by_old_post_id( $args['post_id'] );
				$args['course_id'] = $this->get_new_post_id_by_old_post_id( $args['course_id'] );

				if ( is_null( $args['post_id'] ) || is_null( $args['course_id'] ) ) {
					continue;
				}

				if ( isset( $args['activity_meta']['statistic_ref_id'] ) ) {
					$old_id                           = $args['activity_meta']['statistic_ref_id'];
					$quiz_attempt['statistic_ref_id'] = $old_new_statistic_ref_id_hash[ $old_id ] ?? null;

					if ( is_null( $quiz_attempt['statistic_ref_id'] ) ) {
						continue;
					}
				}

				foreach ( $args['activity_meta'] as $activity_meta_key => &$activity_meta_value ) {
					if ( in_array( $activity_meta_key, array( 'user_id', 'm_edit_by' ), true ) ) {
						$activity_meta_value = $old_user_id_new_user_id_hash[ $activity_meta_value ] ?? null;

						if ( is_null( $activity_meta_value ) ) {
							continue 2;
						}
					} elseif (
						in_array(
							$activity_meta_key,
							array( 'steps_last_id', 'course', 'lesson', 'topic', 'quiz', 'course_id', 'exam_id' ),
							true
						)
					) {
						$activity_meta_value = $this->get_new_post_id_by_old_post_id( $activity_meta_value );

						if ( is_null( $activity_meta_value ) ) {
							continue 2;
						}
					}
				}

				if ( isset( $args['activity_meta']['pro_quizid'] ) ) {
					$args['activity_meta']['pro_quizid'] = get_post_meta(
						$args['activity_meta']['quiz'],
						'quiz_pro_id',
						true
					);
				}

				if ( isset( $args['activity_meta']['quiz_key'] ) ) {
					$args['activity_meta']['quiz_key'] = implode(
						'_',
						array(
							$args['activity_meta']['completed'],
							absint( $args['activity_meta']['pro_quizid'] ),
							absint( $args['activity_meta']['quiz'] ),
							absint( $args['activity_meta']['course'] ),
						)
					);
				}

				$activity_id = ebox_update_user_activity( $args );

				if ( $activity_id ) {
					$this->imported_items_count++;
				}

				ebox_Admin_Import::clear_wpdb_query_cache();
			}
		}
	}
}
