<?php
/**
 * ebox LD30 Displays an Exam Question row
 *
 * Available Variables:
 * $ebox_question_content      : (strong/HTML) Question content.
 *
 * $ebox_exam_model            : (object) LDLMS_Model_Exam instance.
 * $ebox_question_model        : (object) LDLMS_Model_Exam_Question instance.
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

$ebox_question_classes = $ebox_question_model->get_question_classes( 'string' );

?>
<li class="<?php echo esc_attr( $ebox_question_classes ); ?>">
	<div class="ld-exam-question-title">
		<?php
		echo wp_kses_post( $ebox_question_model->question_title );
		?>
	</div>
	<?php
	// We don't escape the $question_content because it's already escaped in the template where it was built.
	echo $ebox_question_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	?>
</li>
