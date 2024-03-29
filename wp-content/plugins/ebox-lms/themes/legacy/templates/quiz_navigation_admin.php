<?php
/**
 * This file contains the code that displays the quiz navigation admin.
 *
 * @since 2.1.0
 *
 * @package ebox\Templates\Legacy
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $pagenow;
global $typenow;
global $quiz_navigation_admin_pager;

if ( ( isset( $quiz_id ) ) && ( ! empty( $quiz_id ) ) ) {

	if ( ! isset( $widget ) ) {
		$widget = array(
			'show_widget_wrapper' => true,
			'current_question_id' => 0,
		);
	}

	$widget_json = htmlspecialchars( wp_json_encode( $widget ) );

	if ( ( isset( $widget['show_widget_wrapper'] ) ) && ( $widget['show_widget_wrapper'] == 'true' ) ) {
		?>
		<div id="quiz_navigation-<?php echo $quiz_id; ?>" class="quiz_navigation quiz_navigation_app" data-widget_instance="<?php echo $widget_json; ?>">
	<?php } ?>

	<div class="ebox_navigation_questions_list">
	<?php

	if ( ( isset( $questions_list ) ) && ( ! empty( $questions_list ) ) ) {

		$question_label_idx = 1;
		if ( ( isset( $quiz_navigation_admin_pager ) ) && ( ! empty( $quiz_navigation_admin_pager ) ) ) {
			if ( ( isset( $quiz_navigation_admin_pager['paged'] ) ) && ( $quiz_navigation_admin_pager['paged'] > 1 ) ) {
				$question_label_idx = ( absint( $quiz_navigation_admin_pager['paged'] ) - 1 ) * $quiz_navigation_admin_pager['per_page'] + 1;
			}
		}

		?>
		<ul class="ebox-quiz-questions" class="ld-question-overview-widget-list ebox-quiz-questions-<?php echo absint( $quiz_id ); ?>">
		<?php
		foreach ( $questions_list as $q_post_id => $q_pro_id ) {
			$question_title = get_the_title( $q_post_id );

			$question_mapper = new WpProQuiz_Model_QuestionMapper();
			$question_pro    = $question_mapper->fetch( $q_pro_id );
			if ( ( $question_pro ) && is_a( $question_pro, 'WpProQuiz_Model_Question' ) ) {
				$question_title = $question_pro->getTitle();
			}

			if ( absint( $q_post_id ) === absint( $widget['current_question_id'] ) ) {
				$selected_class = 'ld-question-overview-widget-item-current';
			} else {
				$selected_class = '';
			}
			$question_edit_link = get_edit_post_link( $q_post_id );
			$question_edit_link = add_query_arg( 'quiz_id', $quiz_id, $question_edit_link );

			?>
			<li class="ebox-quiz-question-item ld-question-overview-widget-item <?php echo $selected_class; ?>"></span> <a href="<?php echo esc_url( $question_edit_link ); ?>"><?php echo $question_title; ?></a></li>
			<?php
			$question_label_idx += 1;
		}
		?>
		</ul>
		<?php
	}
	if ( ( isset( $quiz_navigation_admin_pager ) ) && ( ! empty( $quiz_navigation_admin_pager ) ) ) {
		echo ebox_LMS::get_template(
			'ebox_pager.php',
			array(
				'pager_results' => $quiz_navigation_admin_pager,
				'pager_context' => 'quiz_navigation_admin',
			)
		);
	}
	?>
	<a href="<?php echo esc_url( add_query_arg( 'currentTab', 'ebox_quiz_builder', get_edit_post_link( $quiz_id ) ) ); ?>" class="ld-question-overview-widget-add">
						<?php
						echo sprintf(
						// translators: placeholder: Questions.
							esc_html_x( 'Manage %s in builder', 'placeholder: Questions', 'ebox' ),
							ebox_get_custom_label( 'questions' )
						);
						?>
		</a>
	</div>
	<?php
	if ( ( isset( $widget['show_widget_wrapper'] ) ) && ( $widget['show_widget_wrapper'] == 'true' ) ) {
		?>
		</div> <!-- Closing <div id='course_navigation'> -->

		<?php
		if ( ( isset( $quiz_navigation_admin_pager ) ) && ( ! empty( $quiz_navigation_admin_pager ) ) ) {
			?>
			<script>
				jQuery( function() {
					jQuery('#ebox_admin_quiz_navigation h2.hndle span.questions-count').html('('+<?php echo $quiz_navigation_admin_pager['total_items']; ?>+')');
				});
			</script>
			<?php
		}
		?>
		<?php } ?>
	<?php
}

