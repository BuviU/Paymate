<?php
/**
 * ebox LD30 Displays an Exam Question row
 *
 * Available Variables:
 * $ebox_question_answers : (array) Question answers array.
 *
 * $ebox_exam_model       : (object) LDLMS_Model_Exam instance.
 * $ebox_question_model   : (object) LDLMS_Model_Exam_Question instance.
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

$ebox_block_content = '';

if ( ( isset( $ebox_question_answers ) ) && ( is_array( $ebox_question_answers ) ) ) {
	foreach ( $ebox_question_answers as $ebox_question_answer ) {
		$ebox_answer_content = '';

		$ebox_answer_content = ebox_LMS::get_template(
			'exam/partials/exam_question_answer_types/exam_question_answer_' . $ebox_question_model->question_type . '.php',
			array(
				'ebox_question_answer' => $ebox_question_answer,
				'ebox_exam_model'      => $ebox_exam_model,
				'ebox_question_model'  => $ebox_question_model,
			)
		);

		if ( ( is_string( $ebox_answer_content ) ) && ( ! empty( $ebox_answer_content ) ) ) {
			$ebox_block_content .= $ebox_answer_content;
		}
	}
}
?>
<div class="ld-exam-question-answers">
	<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $ebox_block_content;
	?>
</div>

