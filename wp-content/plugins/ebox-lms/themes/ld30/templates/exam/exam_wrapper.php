<?php
/**
 * ebox LD30 Displays an Exam Wrapper
 *
 * Available Variables:
 *
 * $exam_content        : (string/HTML) Content for Exam.
 * $ebox_exam_model : (object) LDLMS_Model_Exam instance.
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

$ebox_question_classes = 'ld-exam-content';
if ( true === $ebox_exam_model->is_graded ) {
	$ebox_question_classes .= ' ld-exam-graded';
	if ( true === $ebox_exam_model->get_grade ) {
		$ebox_question_classes .= ' ld-exam-graded-passed';
	} else {
		$ebox_question_classes .= ' ld-exam-graded-failed';
	}
} else {
	$ebox_question_classes .= ' ld-exam-not-graded';
}
?>
<div id="ld-exam-content-<?php echo absint( $ebox_exam_model->exam_id ); ?>" class="<?php echo esc_attr( $ebox_question_classes ); ?>">

	<?php if ( true !== $ebox_exam_model->is_graded ) { ?>
		<form method="POST" action="<?php echo esc_url( get_permalink( $ebox_exam_model->exam_id ) ); ?>">
			<input type="hidden" name="exam-nonce" value="<?php echo esc_attr( $ebox_exam_model->form_nonce ); ?>" />
			<input type="hidden" id="ld-form-exam-id" name="exam_id" value="<?php echo absint( $ebox_exam_model->exam_id ); ?>" />
			<input type="hidden" id="ld-form-exam-started" name="exam_started" value="0" />
			<input type="hidden" id="ld-form-exam-course-id" name="course_id" value="<?php echo absint( $ebox_exam_model->course_id ); ?>" />
			<input type="hidden" id="ld-form-exam-user-id" name="user_id" value="<?php echo absint( $ebox_exam_model->user_id ); ?>" />
		<?php
	}

	// We don't escape the $exam_content because it's already escaped in the template where it was built.
	echo $exam_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	?>

	<?php if ( true !== $ebox_exam_model->is_graded ) { ?>
		</form>
	<?php } ?>
</div>
