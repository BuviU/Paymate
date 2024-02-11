<?php
/**
 * Displays Quiz Review Box
 *
 * Available Variables:
 *
 * @var object $quiz_view WpProQuiz_View_FrontQuiz instance.
 * @var object $quiz      WpProQuiz_Model_Quiz instance.
 * @var array  $shortcode_atts Array of shortcode attributes to create the Quiz.
 * @var int    $question_count Number of Question to display.
 * @since 3.2.0
 *
 * @package ebox\Templates\Legacy\Quiz
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Common.
$ebox_quiz_legend_current_label = ebox_LMS::get_template(
	'ebox_quiz_messages',
	array(
		'quiz_post_id' => $quiz->getID(),
		'context'      => 'quiz_quiz_current_message',
		'message'      => esc_html__( 'Current', 'ebox' ),
	)
);
$ebox_quiz_legend_current_label = apply_filters( 'ebox_quiz_legend_current_label', $ebox_quiz_legend_current_label );

if ( ( 2 === (int) $quiz->getQuizModus() ) && ( ! $quiz->isSkipQuestionDisabled() ) ) {
	$ebox_quiz_legend_review = esc_html__( 'Review / Skip', 'ebox' );
} else {
	$ebox_quiz_legend_review = esc_html__( 'Review', 'ebox' );
}
$ebox_quiz_legend_review_label = ebox_LMS::get_template(
	'ebox_quiz_messages',
	array(
		'quiz_post_id' => $quiz->getID(),
		'context'      => 'quiz_quiz_review_message',
		'message'      => $ebox_quiz_legend_review,
	)
);
$ebox_quiz_legend_review_label = apply_filters( 'ebox_quiz_legend_review_label', $ebox_quiz_legend_review_label );

// Single Grading.
$ebox_quiz_legend_answered_label = ebox_LMS::get_template(
	'ebox_quiz_messages',
	array(
		'quiz_post_id' => $quiz->getID(),
		'context'      => 'quiz_quiz_answered_message',
		'message'      => esc_html__( 'Answered', 'ebox' ),
	)
);
$ebox_quiz_legend_answered_label = apply_filters( 'ebox_quiz_legend_answered_label', $ebox_quiz_legend_answered_label );

$ebox_quiz_legend_correct_label = ebox_LMS::get_template(
	'ebox_quiz_messages',
	array(
		'quiz_post_id' => $quiz->getID(),
		'context'      => 'quiz_quiz_answered_correct_message',
		'message'      => esc_html__( 'Correct', 'ebox' ),
	)
);
$ebox_quiz_legend_correct_label = apply_filters( 'ebox_quiz_legend_correct_label', $ebox_quiz_legend_correct_label );

$ebox_quiz_legend_incorrect_label = ebox_LMS::get_template(
	'ebox_quiz_messages',
	array(
		'quiz_post_id' => $quiz->getID(),
		'context'      => 'quiz_quiz_answered_incorrect_message',
		'message'      => esc_html__( 'Incorrect', 'ebox' ),
	)
);
$ebox_quiz_legend_incorrect_label = apply_filters( 'ebox_quiz_legend_incorrect_label', $ebox_quiz_legend_incorrect_label );

?>
<div class="wpProQuiz_reviewLegend">
	<ol>
		<li class="ebox-quiz-review-legend-item-current">
			<span class="wpProQuiz_reviewColor wpProQuiz_reviewQuestion_Target"></span>
			<span class="wpProQuiz_reviewText"><?php echo wp_kses_post( $ebox_quiz_legend_current_label ); ?></span>
		</li>
		<li class="ebox-quiz-review-legend-item-review">
			<span class="wpProQuiz_reviewColor wpProQuiz_reviewColor_Review"></span>
			<span class="wpProQuiz_reviewText"><?php echo wp_kses_post( $ebox_quiz_legend_review_label ); ?></span>
		</li>
		<li class="ebox-quiz-review-legend-item-answered">
			<span class="wpProQuiz_reviewColor wpProQuiz_reviewColor_Answer"></span>
			<span class="wpProQuiz_reviewText"><?php echo wp_kses_post( $ebox_quiz_legend_answered_label ); ?></span>
		</li>
		<li class="ebox-quiz-review-legend-item-correct">
			<span class="wpProQuiz_reviewColor wpProQuiz_reviewColor_AnswerCorrect"></span>
			<span class="wpProQuiz_reviewText"><?php echo wp_kses_post( $ebox_quiz_legend_correct_label ); ?></span>
		</li>
		<li class="ebox-quiz-review-legend-item-incorrect">
			<span class="wpProQuiz_reviewColor wpProQuiz_reviewColor_AnswerIncorrect"></span>
			<span class="wpProQuiz_reviewText"><?php echo wp_kses_post( $ebox_quiz_legend_incorrect_label ); ?></span>
		</li>
	</ol>
	<div style="clear: both;"></div>
</div>
