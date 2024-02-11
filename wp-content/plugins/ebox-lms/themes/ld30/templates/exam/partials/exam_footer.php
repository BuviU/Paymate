<?php
/**
 * ebox LD30 Displays an Exam Footer
 *
 * Available Variables:
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
<div class="ld-exam-footer">
	<button type="submit" class="ld-exam-button-next"><?php echo esc_html__( 'Next', 'ebox' ); ?></button>
	<button type="submit" class="ld-exam-button-submit"><?php echo esc_html__( 'Submit', 'ebox' ); ?></button>
</div>
