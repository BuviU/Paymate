<?php
/**
 * Displays course progress rows for a user
 *
 * Available:
 * $courses_registered: course
 * $course_progress: Progress in courses
 *
 * @since 2.5.5
 *
 * @package ebox\Templates\Legacy\Course
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
foreach ( $course_progress as $course_id => $coursep ) {

	$course = get_post( $course_id );
	if ( ( ! ( $course instanceof WP_Post ) ) || ( 'ebox-courses' !== $course->post_type ) || ( empty( $course->post_title ) ) ) {
		continue;
	}

	?><span class="ebox-profile-course-title"><strong><a href="<?php echo esc_url( get_permalink( $course->ID ) ); ?>"><?php echo wp_kses_post( get_the_title( $course->ID ) ); ?></a></strong>:</span>
	<?php

	$completed_on = get_user_meta( $user_id, 'course_completed_' . $course_id, true );

	if ( ( defined( 'ebox_COURSE_FUNCTIONS_LEGACY' ) ) && ( true === ebox_COURSE_FUNCTIONS_LEGACY ) ) {
		$course_status_slug = ebox_course_status( $course_id, $user_id, true );

		/** This filter is documented in includes/course/ld-course-progress.php */
		if ( apply_filters( 'ebox_course_status_recalc_total_steps', true, $coursep, $user_id, $course_id ) ) {
			$coursep['total'] = ebox_get_course_steps_count( $course_id );
		}

		$coursep['completed'] = ebox_course_get_completed_steps( $user_id, $course_id, $coursep );

		if ( $coursep['completed'] > $coursep['total'] ) {
			$coursep['completed'] = $coursep['total'];
		}
	} else {
		$course_progress_summary = ebox_user_get_course_progress( $user_id, $course_id, 'summary' );

		if ( isset( $course_progress_summary['status'] ) ) {
			$course_status_slug = esc_attr( $course_progress_summary['status'] );
		}

		if ( isset( $course_progress_summary['completed'] ) ) {
			$coursep['completed'] = absint( $course_progress_summary['completed'] );
		}

		if ( isset( $course_progress_summary['total'] ) ) {
			$coursep['total'] = absint( $course_progress_summary['total'] );
		}
	}

	$expired_timestamp = get_user_meta( $user_id, 'ebox_course_expired_' . $course_id, true );
	if ( ! empty( $expired_timestamp ) ) {
		$course_status = esc_html__( 'Expired', 'ebox' );
	}
	esc_html_e( 'Status:', 'ebox' );
	?>
	<span class="leardash-course-status leardash-course-status-<?php echo esc_attr( sanitize_title_with_dashes( $course_status_slug ) ); ?>"><?php echo esc_html( ebox_course_status_label( $course_status_slug ) ); ?></span>
	<?php

	// translators: placeholders: Course steps completed, Course steps total.
	echo ' ' . wp_kses_post( sprintf( _x( 'Completed <strong>%1$d</strong> out of <strong>%2$d</strong> steps', 'placeholders: Course steps completed, Course steps total', 'ebox' ), $coursep['completed'], $coursep['total'] ) );

	$team_enrollment_since = ebox_user_team_enrolled_to_course_from( $user_id, $course_id );

	if ( ! empty( $team_enrollment_since ) ) {
		// translators: placeholder: Team Access Date.
		echo ' <span class="ebox-profile-course-access-label">' . sprintf( esc_html_x( 'Since: %1$s (%2$s Access)', 'placeholder: Team Access Date', 'ebox' ), ebox_adjust_date_time_display( $team_enrollment_since ), ebox_get_custom_label( 'team' ) ) . '</span>';
	} else {
		$since = ld_course_access_from( $course_id, $user_id );
		if ( ! empty( $since ) ) {
			// translators: placeholder: Access Date.
			echo ' <span class="ebox-profile-course-access-label">' . sprintf( esc_html_x( 'Since: %s', 'placeholder: Access Date', 'ebox' ), ebox_adjust_date_time_display( $since ) ) . '</span>';
		}
	}

	// Display the Course Access if expired or expiring.
	$expire_access = ebox_get_setting( $course_id, 'expire_access' );
	if ( ! empty( $expire_access ) ) {
		$expired           = ld_course_access_expired( $course_id, $user_id );
		$expired_timestamp = get_user_meta( $user_id, 'ebox_course_expired_' . $course_id, true );
		if ( ( $expired ) || ( ! empty( $expired_timestamp ) ) ) {
			if ( ! empty( $expired_timestamp ) ) {
				?>
				<span class="leardash-course-expired"><?php echo sprintf(
					// translators: placeholder: Course Access Expired Date.
					esc_html_x( '(access expired %s)', 'placeholder: Course Access Expired Date', 'ebox' ),
					ebox_adjust_date_time_display( $expired_timestamp )
				); ?> </span>
			<?php } else { ?>
				<span class="leardash-course-expired"><?php echo esc_html__( '(access expired)', 'ebox' ); ?></span>
				<?php
			}
		} else {
			$expired_on = ld_course_access_expires_on( $course_id, $user_id );
			if ( ! empty( $expired_on ) ) {
				?>
				<span class="leardash-course-expired"> <?php echo sprintf(
					// translators: placeholder: Course Access Expire Date.
					esc_html_x( '(expires %s)', 'placeholder: Course Access Expire Date', 'ebox' ),
					ebox_adjust_date_time_display( $expired_on )
				); ?> </span>
			<?php
			}
		}
	}

	if ( ( get_current_user_id() == $user_id ) || ( ebox_is_admin_user() ) || ( ebox_is_team_leader_user() ) ) {
		$certificate_link = ebox_get_course_certificate_link( $course_id, $user_id );
		if ( ! empty( $certificate_link ) ) {
		?>
			- <a class="ebox-profile-course-certificate-link" href="<?php echo esc_url( $certificate_link ); ?>" target="_blank"><?php echo __( 'Certificate', 'ebox' ); ?></a>
			<?php
		}
	}

	if ( current_user_can( 'edit_courses', intval( $course_id ) ) ) {
		?> <a class="ebox-profile-edit-course-link" href="<?php echo esc_url( get_edit_post_link( intval( $course_id ) ) ); ?>"><?php echo wp_kses_post( esc_html_x( '(edit)', 'profile edit course link label', 'ebox' ) ); ?></a><?php
	}

	if ( ebox_show_user_course_complete( $user_id ) ) {

		$lesson_query_args = array(
			'pagination'     => 'false',
			'posts_per_page' => -1,
			'nopaging'       => true,
		);

		$modules          = ebox_get_course_modules_list( $course_id, $user_id, $lesson_query_args );
		$course_quiz_list = ebox_get_course_quiz_list( $course_id, $user_id );

		if ( ( ! empty( $modules ) ) || ( ! empty( $course_quiz_list ) ) ) {
			$user_course_progress                = array();
			$user_course_progress['user_id']     = $user_id;
			$user_course_progress['course_id']   = $course_id;
			$user_course_progress['course_data'] = $coursep;

			if ( 'completed' === $course_status_slug ) {
				$course_checked                  = ' checked="checked" ';
				$user_course_progress['checked'] = true;
			} else {
				$course_checked                  = '';
				$user_course_progress['checked'] = false;
			}
			?>
			<a href="#" id="ebox-profile-course-details-link-<?php echo esc_attr( $course_id ); ?>" class="ebox-profile-course-details-link"><?php echo esc_html_x( '(details)', 'Course progress details link', 'ebox' ); ?></a>
			<?php
			$unchecked_children_message = '';
			if ( ( ! empty( $modules ) ) || ( ! empty( $course_quiz_list ) ) ) {
				$unchecked_children_message = ' data-title-unchecked-children="' . htmlspecialchars( __( 'Set all children steps as incomplete?', 'ebox' ), ENT_QUOTES ) . '" ';
			}
			?>
			<div id="ebox-profile-course-details-container-<?php echo absint( $course_id ); ?>" class="ebox-profile-course-details-container" style="display:none">
				<?php
					ebox_LMS::get_template(
						'course_details_admin',
						array(
							'user_id'         => $user_id,
							'course_id'       => $course_id,
							'course_progress' => $coursep,
						),
						true
					);
				?>
				<?php
					ebox_LMS::get_template(
						'exam/profile_course_exam_challenge_admin.php',
						array(
							'user_id'         => $user_id,
							'course_id'       => $course_id,
						),
						true
					);
				?>

				<input id="ebox-mark-course-complete-<?php echo absint( $course_id ); ?>" type="checkbox" <?php echo $course_checked; ?> class="ebox-mark-course-complete" data-name="<?php echo htmlspecialchars( json_encode( $user_course_progress, JSON_FORCE_OBJECT ) ) ?>" <?php echo $unchecked_children_message ?> /><label for="ebox-mark-course-complete-<?php echo absint( $course_id ); ?>"><?php
				// translators: placeholder: Course.
				echo sprintf( esc_html_x( '%s All Complete', 'placeholder: Course', 'ebox' ), esc_html( ebox_Custom_Label::get_label( 'course' ) ) ) ?></label><br />
				<?php
					$template_file = ebox_LMS::get_template(
						'course_navigation_admin',
						array(
							'course_id'        => $course_id,
							'course'           => $course,
							'course_progress'  => $course_progress,
							'modules'          => $modules,
							'course_quiz_list' => $course_quiz_list,
							'user_id'          => $user_id,
							'widget'           => array(
								'show_widget_wrapper' => true,
								'current_lesson_id'   => 0,
								'current_step_id'     => 0,
							),
						),
						null,
						true
					);
					if ( ! empty( $template_file ) ) {
						include $template_file;
					}
					?>
			</div>
				<?php
		}
	}
	?>
	<br/>
	<?php
}
