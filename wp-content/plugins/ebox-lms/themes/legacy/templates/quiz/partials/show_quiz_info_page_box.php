<?php
/**
 * Displays Quiz Info Page Box
 *
 * Available Variables:
 *
 * @var object $quiz_view WpProQuiz_View_FrontQuiz instance.
 * @var object $quiz      WpProQuiz_Model_Quiz instance.
 * @var array  $shortcode_atts Array of shortcode attributes to create the Quiz.
 *
 * @since 3.2.0
 *
 * @package ebox\Templates\Legacy\Quiz
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wpProQuiz_infopage" style="display: none;">
	<h4><?php esc_html_e( 'Information', 'ebox' ); ?></h4>
	<?php
	if ( $quiz->isFormActivated() && $quiz->getFormShowPosition() == WpProQuiz_Model_Quiz::QUIZ_FORM_POSITION_END && ( ! $quiz->isShowReviewQuestion() || $quiz->isQuizSummaryHide() ) ) {
		$quiz_view->showFormBox();
	}
	?>
	<input type="button" name="endInfopage" value="<?php echo wp_kses_post( // phpcs:ignore Squiz.PHP.EmbeddedPhp.ContentBeforeOpen,Squiz.PHP.EmbeddedPhp.ContentAfterOpen
		ebox_LMS::get_template(
			'ebox_quiz_messages',
			array(
				'quiz_post_id' => $quiz->getID(),
				'context'      => 'quiz_finish_button_label',
				// translators: placeholder: Quiz.
				'message'      => sprintf( esc_html_x( 'Finish %s', 'placeholder: Quiz', 'ebox' ), ebox_Custom_Label::get_label( 'quiz' ) ),
			)
		) ); ?>" class="wpProQuiz_button" /> <?php // phpcs:ignore Squiz.PHP.EmbeddedPhp.ContentBeforeEnd,PEAR.Functions.FunctionCallSignature.Indent,PEAR.Functions.FunctionCallSignature.CloseBracketLine ?>
</div>
