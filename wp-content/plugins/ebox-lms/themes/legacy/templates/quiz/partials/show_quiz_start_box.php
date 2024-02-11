<?php
/**
 * Displays Quiz Start Box
 *
 * Available Variables:
 *
 * @var object $quiz_view WpProQuiz_View_FrontQuiz instance.
 * @var object $quiz      WpProQuiz_Model_Quiz instance.
 * @var array  $shortcode_atts Array of shortcode attributes to create the Quiz.
 *
 * @since 3.2.0
 *
 * @package ebox\Templates\Legacy\Quiz
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ebox_quiz_post_id = $quiz->getPostId();

$ebox_quiz_resume_id   = 0;
$ebox_quiz_resume_data = array();

if ( ( ! empty( $ebox_quiz_post_id ) ) && ( get_current_user_id() ) ) {
	$ebox_quiz_resume_enabled = (bool) ebox_get_setting( $ebox_quiz_post_id, 'quiz_resume' );
	if ( true === $ebox_quiz_resume_enabled ) {
		$ebox_course_id            = ebox_get_course_id();
		$ebox_quiz_resume_activity = LDLMS_User_Quiz_Resume::get_user_quiz_resume_activity( get_current_user_id(), $ebox_quiz_post_id, $ebox_course_id );
		if ( ( is_a( $ebox_quiz_resume_activity, 'LDLMS_Model_Activity' ) ) && ( property_exists( $ebox_quiz_resume_activity, 'activity_id' ) ) && ( ! empty( $ebox_quiz_resume_activity->activity_id ) ) ) {
			$ebox_quiz_resume_id = $ebox_quiz_resume_activity->activity_id;
			if ( ( property_exists( $ebox_quiz_resume_activity, 'activity_meta' ) ) && ( ! empty( $ebox_quiz_resume_activity->activity_meta ) ) ) {
				$ebox_quiz_resume_data = $ebox_quiz_resume_activity->activity_meta;
			}
		}
	}
}

if ( empty( $ebox_quiz_resume_data ) ) {
	// translators: placeholder Quiz.
	$ebox_quiz_message = sprintf( esc_html_x( 'Start %s', 'placeholder Quiz', 'ebox' ), ebox_Custom_Label::get_label( 'quiz' ) );
} else {
	// translators: placeholder Quiz.
	$ebox_quiz_message = sprintf( esc_html_x( 'Continue %s', 'placeholder Quiz', 'ebox' ), ebox_Custom_Label::get_label( 'quiz' ) );
}
?>
<div class="wpProQuiz_text">
	<?php
	if ( $quiz->isFormActivated() && $quiz->getFormShowPosition() == WpProQuiz_Model_Quiz::QUIZ_FORM_POSITION_START ) {
		$quiz_view->showFormBox();
	}
	?>
	<div>
		<input class="wpProQuiz_button" type="button" 
		value="<?php // phpcs:ignore Squiz.PHP.EmbeddedPhp.ContentBeforeOpen,Squiz.PHP.EmbeddedPhp.ContentAfterOpen
		echo wp_kses_post(
			ebox_LMS::get_template(
				'ebox_quiz_messages',
				array(
					'quiz_post_id' => $quiz->getID(),
					'context'      => 'quiz_start_button_label',
					'message'      => $ebox_quiz_message,
				)
			)
		); // phpcs:ignore Generic.WhiteSpace.ScopeIndent.Incorrect
		?>" name="startQuiz" /><?php // phpcs:ignore Squiz.PHP.EmbeddedPhp.ContentAfterEnd ?>
	</div>
</div>
