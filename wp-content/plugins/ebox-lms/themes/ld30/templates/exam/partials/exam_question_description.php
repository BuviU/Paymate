<?php
/**
 * ebox LD30 Displays an Exam Questions
 *
 * Available Variables:
 * $ebox_question_description : (strong/HTML) Block content for description.
 *
 * $ebox_exam_model           : (object) LDLMS_Model_Exam instance.
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

if ( isset( $ebox_question_description ) ) {
	$ebox_question_description = trim( $ebox_question_description );
	if ( ( ! empty( $ebox_question_description ) ) && ( '<p></p>' !== $ebox_question_description ) ) {
		?>
		<div class="ld-exam-question-description">
			<?php echo wp_kses_post( $ebox_question_description ); ?>
		</div>
		<?php
	}
}
