<?php
/**
 * Displays Quiz Load Prerequisite Box
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
<div style="display: none;" class="wpProQuiz_prerequisite">
	<?php
	echo wp_kses_post(
		ebox_LMS::get_template(
			'ebox_quiz_messages',
			array(
				'quiz_post_id' => $quiz->getID(),
				'context'      => 'quiz_prerequisite_message',
				'message'      => '<p>' . esc_html__( 'You must first complete the following:', 'ebox' ) . ' <span></span></p>',
			)
		)
	);
	?>
</div>
