<?php
/**
 * ebox LD30 Displays an Exam Questions
 *
 * Available Variables:
 * $questions_content    : (strong/HTML) Questions content.
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

?>
<ul class="ld-exam-questions">
	<?php
	// We don't escape the $question_content because it's already escaped in the template where it was built.
	echo $questions_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	?>
</ul>
