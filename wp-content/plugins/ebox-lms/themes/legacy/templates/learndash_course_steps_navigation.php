<?php
/**
 * Displays a Course Prev/Next navigation.
 *
 * Available Variables:
 *
 * $course_id       : (int) ID of Course
 * $course_step_post : (int) ID of the lesson/topic post
 * $user_id         : (int) ID of User
 * $course_settings : (array) Settings specific to current course
 *
 * @since 2.5.8
 *
 * @package ebox\Templates\Legacy\Course
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ebox_previous_nav = ebox_previous_post_link();
$ebox_next_nav     = '';

/*
 * See details for filter 'ebox_show_next_link' https://developers.ebox.com/hook/ebox_show_next_link
 *
 * @since version 2.3
 */

$current_complete = false;

if ( ( isset( $course_settings['course_disable_lesson_progression'] ) ) && ( $course_settings['course_disable_lesson_progression'] === 'on' ) ) {
	$current_complete = true;
} else {

	if ( $course_step_post->post_type == 'ebox-topic' ) {
		$current_complete = ebox_is_topic_complete( $user_id, $course_step_post->ID, $course_id );
	} elseif ( $course_step_post->post_type == 'ebox-modules' ) {
		$current_complete = ebox_is_lesson_complete( $user_id, $course_step_post->ID, $course_id );
	}

	if ( $current_complete !== true ) {
		$bypass_course_limits_admin_users = ebox_can_user_bypass( $user_id, 'ebox_course_progression', $course_step_post->ID );
		if ( true === $bypass_course_limits_admin_users ) {
			$current_complete = true;
		}
	}
}

/** This filter is documented in themes/ld30/templates/modules/course-steps.php */
if ( apply_filters( 'ebox_show_next_link', $current_complete, $user_id, $course_step_post->ID ) ) {
	 $ebox_next_nav = ebox_next_post_link();
}

if ( ( ! empty( $ebox_previous_nav ) ) || ( ! empty( $ebox_next_nav ) ) ) {
	?><p id="ebox_next_prev_link"><?php echo $ebox_previous_nav; ?> <?php echo $ebox_next_nav; ?></p>
	<?php
}
