<?php
/**
 * Displays the Course Lesson Not Available message
 *
 * Available Variables:
 * user_id : (integer) The user_id whose points to show
 * course_id : (integer) The ID of the couse shown
 * lesson_id: (integer) The Of of the lesson not available
 * ld_lesson_access_from_int : (integer) timestamp when lesson will become available
 * ld_lesson_access_from_date : (string) Formatted human readable date/time of ld_lesson_access_from_int
 * context : (string) The context will be set based on where this message is shown. course, lesson, loop, etc.
 *
 * @since 2.4.0
 *
 * @package ebox\Templates\Legacy\Course
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// First generate the message.
$message = sprintf(
	// translators: Date when content will be available.
	wp_kses_post( __( '<span class="ld-display-label">Available on:</span> <span class="ld-display-date">%s</span>', 'ebox' ) ),
	ebox_adjust_date_time_display( $lesson_access_from_int )
);
$wrap_start = '<small class="notavailable_message">';
$wrap_end   = '</small>';

// The figure out how to display it.
if ( $context == 'lesson' ) {
	// On the lesson single we display additional information.
	$message .= '<br><br><a href="' . get_permalink( $course_id ) . '">' .
	// translators: placeholder: Course.
	sprintf( esc_html_x( 'Return to %s Overview', 'placeholder: Course', 'ebox' ), ebox_Custom_Label::get_label( 'course' ) ) . '</a>';

	$wrap_start = '<div class="notavailable_message">';
	$wrap_end   = '</div>';

} elseif ( $context == 'course' ) {
	// No changes for course.
} else {
	// Default no changes.
}
/** This filter is documented in themes/ld30/templates/modules/messages/lesson-not-available.php */
echo $wrap_start . apply_filters( 'ebox_lesson_available_from_text', $message, get_post( $lesson_id ), $lesson_access_from_int ) . $wrap_end;
