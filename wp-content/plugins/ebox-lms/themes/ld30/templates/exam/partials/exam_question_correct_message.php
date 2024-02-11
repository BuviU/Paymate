<?php
/**
 * ebox LD30 Displays an Exam Question Correct Message.
 *
 * Available Variables:
 * $ebox_question_correct_message : (string/HTML) Question correct message.
 *
 * $ebox_exam_model               : (object) LDLMS_Model_Exam instance.
 * $ebox_question_model           : (object) LDLMS_Model_Exam_Question instance.
 *
 * @since 4.0.0
 *
 * @package ebox\Templates\LD30
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( ! isset( $ebox_exam_model ) ) || ( ! is_a( $ebox_exam_model, 'LDLMS_Model_Exam' ) ) ) {
	return;
}

if ( ( ! isset( $ebox_question_model ) ) || ( ! is_a( $ebox_question_model, 'LDLMS_Model_Exam_Question' ) ) ) {
	return;
}

if ( ! isset( $ebox_question_correct_message ) ) {
	$ebox_question_correct_message = '';
}

if ( ( true !== $ebox_exam_model->is_graded ) || ( true !== $ebox_question_model->get_grade ) ) {
	$ebox_question_correct_message = '';
}

$ebox_question_correct_message = trim( $ebox_question_correct_message );
if ( '<p></p>' === $ebox_question_correct_message ) {
	$ebox_question_correct_message = '';
}

if ( ! empty( $ebox_question_correct_message ) ) {
	?><div class="ld-exam-question-correct-message"><?php echo wp_kses_post( $ebox_question_correct_message ); ?></div>
	<?php
}
