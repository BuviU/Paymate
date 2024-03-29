<?php
/**
 * Displays a lesson.
 *
 * Available Variables:
 *
 * $course_id       : (int) ID of the course
 * $course      : (object) Post object of the course
 * $course_settings : (array) Settings specific to current course
 * $course_status   : Course Status
 * $has_access  : User has access to course or is enrolled.
 *
 * $courses_options : Options/Settings as configured on Course Options page
 * $modules_options : Options/Settings as configured on modules Options page
 * $quizzes_options : Options/Settings as configured on Quiz Options page
 *
 * $user_id         : (object) Current User ID
 * $logged_in       : (true/false) User is logged in
 * $current_user    : (object) Currently logged in user object
 *
 * $quizzes         : (array) Quizzes Array
 * $post            : (object) The lesson post object
 * $topics      : (array) Array of Topics in the current lesson
 * $all_quizzes_completed : (true/false) User has completed all quizzes on the lesson Or, there are no quizzes.
 * $lesson_progression_enabled  : (true/false)
 * $show_content    : (true/false) true if lesson progression is disabled or if previous lesson is completed.
 * $previous_lesson_completed   : (true/false) true if previous lesson is completed
 * $lesson_settings : Settings specific to the current lesson.
 *
 * @since 2.1.0
 *
 * @package ebox\Templates\Legacy\Lesson
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php if ( @$lesson_progression_enabled && ! @$previous_lesson_completed ) : ?>
	<span id="ebox_complete_prev_lesson">
	<?php
		$previous_item = ebox_get_previous( $post );
	if ( ( ! empty( $previous_item ) ) && ( $previous_item instanceof WP_Post ) ) {
		if ( $previous_item->post_type == 'ebox-quiz' ) {
			echo sprintf(
				// translators: placeholder: Quiz URL.
				esc_html_x( 'Please go back and complete the previous %s.', 'placeholder: Quiz URL', 'ebox' ),
				'<a class="ebox-link-previous-incomplete" href="' . esc_url( ebox_get_step_permalink( $previous_item->ID, $course_id ) ) . '">' . esc_html( ebox_get_custom_label_lower( 'quiz' ) ) . '</a>'
			);

		} elseif ( $previous_item->post_type == 'ebox-topic' ) {
			echo sprintf(
				// translators: placeholder: Topic URL.
				esc_html_x( 'Please go back and complete the previous %s.', 'placeholder: Topic URL', 'ebox' ),
				'<a class="ebox-link-previous-incomplete" href="' . esc_url( ebox_get_step_permalink( $previous_item->ID, $course_id ) ) . '">' . esc_html( ebox_get_custom_label_lower( 'topic' ) ) . '</a>'
			);
		} else {
			echo sprintf(
				// translators: placeholder: Lesson URL.
				esc_html_x( 'Please go back and complete the previous %s.', 'placeholder: Lesson URL', 'ebox' ),
				'<a class="ebox-link-previous-incomplete" href="' . esc_url( ebox_get_step_permalink( $previous_item->ID, $course_id ) ) . '">' . esc_html( ebox_get_custom_label_lower( 'lesson' ) ) . '</a>'
			);
		}
	} else {
		// translators: placeholder: lesson.
		echo sprintf( esc_html_x( 'Please go back and complete the previous %s.', 'placeholder lesson', 'ebox' ), ebox_get_custom_label_lower( 'lesson' ) );
	}
	?>
	</span><br />
	<?php add_filter( 'comments_array', 'ebox_remove_comments', 1, 2 ); ?>
<?php endif; ?>

<?php if ( $show_content ) : ?>

	<?php if ( ( isset( $materials ) ) && ( ! empty( $materials ) ) ) : ?>
		<div id="ebox_lesson_materials" class="ebox_lesson_materials">
			<h4>
			<?php
			// translators: placeholder: Lesson.
			printf( esc_html_x( '%s Materials', 'placeholder: Lesson', 'ebox' ), ebox_Custom_Label::get_label( 'lesson' ) );
			?>
			</h4>
			<p><?php echo $materials; ?></p>
		</div>
	<?php endif; ?>

	<div class="ebox_content"><?php echo $content; ?></div>
	<?php
	/**
	 * Lesson Topics
	 */
	?>
	<?php if ( ! empty( $topics ) ) : ?>
		<div id="ebox_lesson_topics_list" class="ebox_lesson_topics_list">
			<div id='ebox_topic_dots-<?php echo esc_attr( $post->ID ); ?>' class="ebox_topic_dots type-list">
				<strong>
				<?php
				// translators: placeholders: Lesson, Topics.
				printf( esc_html_x( '%1$s %2$s', 'placeholders: Lesson, Topics', 'ebox' ), ebox_Custom_Label::get_label( 'lesson' ), ebox_Custom_Label::get_label( 'topics' ) );
				?>
				</strong>
				<ul>
					<?php $odd_class = ''; ?>

					<?php foreach ( $topics as $key => $topic ) : ?>

						<?php $odd_class = empty( $odd_class ) ? 'nth-of-type-odd' : ''; ?>
						<?php $completed_class = empty( $topic->completed ) ? 'topic-notcompleted' : 'topic-completed'; ?>

						<li class='<?php echo esc_attr( $odd_class ); ?>'>
							<span class="topic_item">
								<a class='<?php echo esc_attr( $completed_class ); ?>' href='<?php echo esc_url( ebox_get_step_permalink( $topic->ID, $course_id ) ); ?>' title='<?php echo esc_html( $topic->post_title ); ?>'>
									<span><?php echo apply_filters( 'the_title', $topic->post_title, $topic->ID ); ?></span>
								</a>
							</span>
						</li>

					<?php endforeach; ?>

				</ul>
			</div>
		</div>
		<?php
		global $course_pager_results;
		if ( isset( $course_pager_results[ $post->ID ]['pager'] ) ) {
			echo ebox_LMS::get_template(
				'ebox_pager.php',
				array(
					'pager_results'   => $course_pager_results[ $post->ID ]['pager'],
					'pager_context'   => 'course_topics',
					'href_query_arg'  => 'ld-topic-page',
					'href_val_prefix' => $post->ID . '-',
				)
			);
		}
		?>
	<?php endif; ?>


	<?php
	/**
	 * Show Quiz List
	 */
	?>
	<?php if ( ! empty( $quizzes ) ) : ?>
		<div id="ebox_quizzes" class="ebox_quizzes">
			<div id="quiz_heading"><span><?php echo ebox_Custom_Label::get_label( 'quizzes' ); ?></span><span class="right"><?php esc_html_e( 'Status', 'ebox' ); ?></span></div>
			<div id="quiz_list" class="quiz_list">

			<?php foreach ( $quizzes as $quiz ) : ?>
				<div id="post-<?php echo esc_attr( $quiz['post']->ID ); ?>" class="<?php echo esc_attr( $quiz['sample'] ); ?>">
					<div class="list-count"><?php echo esc_attr( $quiz['sno'] ); ?></div>
					<h4>
						<a class="<?php echo esc_attr( $quiz['status'] ); ?>" href="<?php echo esc_url( $quiz['permalink'] ); ?>"><?php echo apply_filters( 'the_title', $quiz['post']->post_title, $quiz['post']->ID ); ?></a>
					</h4>
				</div>
			<?php endforeach; ?>

			</div>
		</div>
	<?php endif; ?>


	<?php
	/**
	 * Display Lesson Assignments
	 */
	?>
	<?php if ( ( ebox_lesson_hasassignments( $post ) ) && ( ! empty( $user_id ) ) ) : // cspell:disable-line. ?>
		<?php
			$ret = ebox_LMS::get_template(
				'ebox_lesson_assignment_uploads_list.php',
				array(
					'course_step_post' => $post,
					'user_id'          => $user_id,
				)
			);
			echo $ret;
		?>

	<?php endif; ?>


	<?php
	/**
	 * Display Mark Complete Button
	 */
	?>
	<?php if ( $all_quizzes_completed && $logged_in && ! empty( $course_id ) ) : ?>
		<br />
		<?php
		echo ebox_mark_complete(
			$post,
			array(
				'form'   => array(
					'id' => 'ebox-mark-complete',
				),
				'button' => array(
					'id' => 'ebox_mark_complete_button',
				),
				'timer'  => array(
					'id' => 'ebox_timer',
				),
			)
		);
		?>
	<?php endif; ?>

<?php endif; ?>

<br />

<?php
$ret = ebox_LMS::get_template(
	'ebox_course_steps_navigation.php',
	array(
		'course_id'        => $course_id,
		'course_step_post' => $post,
		'user_id'          => $user_id,
		'course_settings'  => isset( $course_settings ) ? $course_settings : array(),
	)
);
echo $ret;
