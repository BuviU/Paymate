<?php
/**
 * ebox LD30 Displays an Exam Result Message.
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

?><div class="ld-exam-result-message">
<?php
if ( true === $ebox_exam_model->is_graded ) {
	$ebox_exam_result_message = $ebox_exam_model->get_result_message();
	$ebox_exam_result_message = trim( $ebox_exam_result_message );
	if ( ! empty( $ebox_exam_result_message ) ) {
		echo wp_kses_post( $ebox_exam_result_message );
	}

	$ebox_exam_result_button_params = $ebox_exam_model->get_result_button_params();
	if ( ( isset( $ebox_exam_result_button_params['redirect_url'] ) ) && ( isset( $ebox_exam_result_button_params['button_label'] ) ) ) {
		?>
		<p class="result-button"><a href="<?php echo esc_url( $ebox_exam_result_button_params['redirect_url'] ); ?>" class="ld-exam-result-button"><?php echo esc_html( $ebox_exam_result_button_params['button_label'] ); ?></a></p>
		<?php
	}
}
?>
</div>
