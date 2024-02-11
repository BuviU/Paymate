<?php
/**
 * ebox LD30 Displays an Exam Header
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
<div class="ld-exam-header">
	<button type="submit" class="ld-exam-button-start">
		<?php
		echo sprintf(
		// translators: placeholder: Exam.
			esc_html_x( 'Start %s', 'placeholder: Exam', 'ebox' ),
			esc_html( ebox_get_custom_label( 'exam' ) )
		);
		?>
		</button>
	<button type="submit" class="ld-exam-button-results"><?php echo esc_html__( 'View Results', 'ebox' ); ?></button>
	<?php
	/**
	 * Filter to show exam progress.
	 *
	 * @since 4.0.0
	 *
	 * @param bool $show_exam_progress   Whether to show exam progress.
	 * @param int  $exam_id              ID of the Exam post.
	 */
	if ( apply_filters( 'ebox_exam_question_row_show_number', true, $ebox_exam_model->exam_id ) ) {
		?>
			<div class="ld-exam-progress">
				<div class="ld-exam-progress-text">
				<?php
				echo wp_kses_post(
					sprintf(
						// translators: plaseholders: Exam label, Question number, Questions count, Question(s) label, Percentage.
						_x( '%1$s progress: %2$s of %3$s %4$s (%5$s%%)', 'Exam label, Question number, Questions count, Question(s) label, Percentage', 'ebox' ),
						ebox_get_custom_label( 'exam' ),
						'<span class="ld-exam-progress-text-current">0</span>',
						'<span class="ld-exam-progress-text-total">0</span>',
						$ebox_exam_model->questions_count > 1 ? ebox_get_custom_label_lower( 'questions' ) : ebox_get_custom_label_lower( 'question' ),
						'<span class="ld-exam-progress-text-percentage">0</span>'
					)
				);
				?>
				</div>
				<div class="ld-exam-progress-bar">
					<span class="ld-exam-progress-bar-fill" style="width: 30%;"></span>
				</div>
			</div>
			<?php
	}
	?>
</div>
