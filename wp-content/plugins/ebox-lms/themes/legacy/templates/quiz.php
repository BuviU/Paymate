<?php
/**
 * Displays a quiz.
 *
 * Available Variables:
 *
 * $course_id       : (int) ID of the course
 * $course      : (object) Post object of the course
 * $course_settings : (array) Settings specific to current course
 * $course_status   : Course Status
 * $has_access  : User has access to course or is enrolled.
 *
 * $courses_options : Options/Settings as configured on Course Options page
 * $modules_options : Options/Settings as configured on modules Options page
 * $quizzes_options : Options/Settings as configured on Quiz Options page
 *
 * $user_id         : (object) Current User ID
 * $logged_in       : (true/false) User is logged in
 * $current_user    : (object) Currently logged in user object
 * $post            : (object) The quiz post object () (Deprecated in LD 3.1. User $quiz_post instead).
 * $quiz_post       : (object) The quiz post object ().
 * $lesson_progression_enabled  : (true/false)
 * $show_content    : (true/false) true if user is logged in and lesson progression is disabled or if previous lesson and topic is completed.
 * $attempts_left   : (true/false)
 * $attempts_count : (integer) No of attempts already made
 * $quiz_settings   : (array)
 *
 * Note:
 *
 * To get lesson/topic post object under which the quiz is added:
 * $lesson_post = !empty($quiz_settings["lesson"])? get_post($quiz_settings["lesson"]):null;
 *
 * @since 2.1.0
 *
 * @package ebox\Templates\Legacy\Quiz
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( ! isset( $quiz_post ) ) || ( ! is_a( $quiz_post, 'WP_Post' ) ) ) {
	return;
}

if ( ! empty( $lesson_progression_enabled ) ) {

	$last_incomplete_step = ebox_is_quiz_accessable( null, $quiz_post, true, $course_id );
	if ( 1 !== $last_incomplete_step ) {
		if ( is_a( $last_incomplete_step, 'WP_Post' ) ) {
			if ( $last_incomplete_step->post_type === ebox_get_post_type_slug( 'topic' ) ) {
				echo sprintf(
					// translators: placeholder: topic URL.
					esc_html_x( 'Please go back and complete the previous %s.', 'placeholder: topic URL', 'ebox' ),
					'<a class="ebox-link-previous-incomplete" href="' . ebox_get_step_permalink( $last_incomplete_step->ID, $course_id ) . '">' . ebox_get_custom_label_lower( 'topic' ) . '</a>'
				);
			} elseif ( $last_incomplete_step->post_type === ebox_get_post_type_slug( 'lesson' ) ) {
				echo sprintf(
					// translators: placeholder: lesson URL.
					esc_html_x( 'Please go back and complete the previous %s.', 'placeholder: lesson URL', 'ebox' ),
					'<a class="ebox-link-previous-incomplete" href="' . ebox_get_step_permalink( $last_incomplete_step->ID, $course_id ) . '">' . ebox_get_custom_label_lower( 'lesson' ) . '</a>'
				);
			} elseif ( $last_incomplete_step->post_type === ebox_get_post_type_slug( 'quiz' ) ) {
				echo sprintf(
					// translators: placeholder: quiz URL.
					esc_html_x( 'Please go back and complete the previous %s.', 'placeholder: quiz URL', 'ebox' ),
					'<a class="ebox-link-previous-incomplete" href="' . ebox_get_step_permalink( $last_incomplete_step->ID, $course_id ) . '">' . ebox_get_custom_label_lower( 'quiz' ) . '</a>'
				);
			} else {
				echo esc_html__( 'Please go back and complete the previous step.', 'ebox' );
			}
		}
	}
}

if ( $show_content ) {
	if ( ( isset( $materials ) ) && ( ! empty( $materials ) ) ) :
		?>
		<div id="ebox_quiz_materials" class="ebox_quiz_materials">
			<h4>
			<?php
			// translators: placeholder: Quiz.
			printf( esc_html_x( '%s Materials', 'placeholder: Quiz', 'ebox' ), ebox_Custom_Label::get_label( 'quiz' ) );
			?>
			</h4>
			<p><?php echo $materials; ?></p>
		</div>
		<?php
	endif;

	echo $content;
	if ( $attempts_left ) {
		echo $quiz_content;
	} else {
		?>
			<p id="ebox_already_taken">
			<?php
			echo sprintf(
				// translators: placeholders: quiz, attempts count.
				esc_html_x( 'You have already taken this %1$s %2$d time(s) and may not take it again.', 'placeholders: quiz, attempts count', 'ebox' ),
				ebox_get_custom_label_lower( 'quiz' ),
				$attempts_count
			);
			?>
			</p>
		<?php
	}
}
