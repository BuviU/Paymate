<?php
/**
 * ebox LD30 Displays an Exam Question row
 *
 * Available Variables:
 * $ebox_question_answer : (array) Question answer array.
 *
 * $ebox_exam_model      : (object) LDLMS_Model_Exam instance.
 * $ebox_question_model  : (object) LDLMS_Model_Exam_Question instance.
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

$ebox_block_content  = '';
$ebox_answer_content = '';
$ebox_input_disabled = '';
$ebox_input_checked  = '';

if ( true === $ebox_exam_model->is_graded ) {
	if ( ( isset( $ebox_question_answer['student_answer_value'] ) ) && ( $ebox_question_answer['student_answer_value'] ) ) {
		$ebox_input_checked = ' checked="checked" ';
	}
}
if ( true === $ebox_exam_model->is_graded ) {
	$ebox_input_disabled = ' disabled="disabled" ';
}

$ebox_answer_classes = $ebox_question_model->get_answer_classes( $ebox_question_answer, 'string' );

$ebox_answer_content .= '<input type="checkbox" id="ld-exam-question-answer-' . $ebox_question_model->question_idx . '-' . $ebox_question_answer['answer_idx'] . '" name="ld-exam-question-answer[' . $ebox_question_model->question_idx . '][' . $ebox_question_answer['answer_idx'] . ']" value="1" ' . $ebox_input_checked . ' ' . $ebox_input_disabled . '/>';

if ( ! empty( $ebox_answer_content && ! empty( $ebox_question_answer['answer_label'] ) ) ) {
	$ebox_answer_content .= '<label for="ld-exam-question-answer-' . $ebox_question_model->question_idx . '-' . $ebox_question_answer['answer_idx'] . '">' . wp_kses_post( $ebox_question_answer['answer_label'] ) . '</label>';

	$ebox_block_content .= '<div class="' . $ebox_answer_classes . '">' . $ebox_answer_content . '</div>';
}

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo $ebox_block_content;
