<?php
/**
 * Displays content of course
 *
 * Available Variables:
 * $course_id       : (int) ID of the course
 * $course      : (object) Post object of the course
 * $course_settings : (array) Settings specific to current course
 *
 * $courses_options : Options/Settings as configured on Course Options page
 * $modules_options : Options/Settings as configured on modules Options page
 * $quizzes_options : Options/Settings as configured on Quiz Options page
 *
 * $user_id         : Current User ID
 * $logged_in       : User is logged in
 * $current_user    : (object) Currently logged in user object
 *
 * $course_status   : Course Status
 * $has_access  : User has access to course or is enrolled.
 * $has_course_content      : Course has course content
 * $modules         : modules Array
 * $quizzes         : Quizzes Array
 * $lesson_progression_enabled  : (true/false)
 *
 * @since 2.1.0
 *
 * @package ebox\Templates\Legacy\Course
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Show Course Status
 */
?>
<?php if ( $has_course_content ) : ?>
	<div id='ebox_course_content'>
		<h4 id='ebox_course_content_title'>
			<?php
				// translators: placeholder: Course.
				printf( esc_html_x( '%s Content', 'placeholder: Course', 'ebox' ), ebox_Custom_Label::get_label( 'course' ) );
			?>
		</h4>

		<?php /* Show Lesson List */ ?>
		<?php if ( ! empty( $modules ) ) : ?>

			<?php if ( $has_topics ) : ?>
				<div class='expand_collapse'>
					<a href='#' onClick='jQuery("#ebox_post_<?php echo $course_id; ?> .ebox_topic_dots").slideDown(); return false;'><?php esc_html_e( 'Expand All', 'ebox' ); ?></a> | <a href='#' onClick='jQuery("#ebox_post_<?php echo $course_id; ?> .ebox_topic_dots").slideUp(); return false;'><?php esc_html_e( 'Collapse All', 'ebox' ); ?></a>
				</div>
			<?php endif; ?>

			<div id='ebox_modules'>

				<div id='lesson_heading'>
					<span><?php echo ebox_Custom_Label::get_label( 'modules' ); ?></span>
					<span class='right'><?php esc_html_e( 'Status', 'ebox' ); ?></span>
				</div>
				<div id='modules_list'>

					<?php foreach ( $modules as $lesson ) : ?>
						<div id='post-<?php echo absint( $lesson['post']->ID ); ?>' class='<?php echo esc_attr( $lesson['sample'] ); ?>'>
							<div class='list-count'><?php echo esc_attr( $lesson['sno'] ); ?></div>
							<h4>
								<a class='<?php echo esc_attr( $lesson['status'] ); ?>' href='<?php echo esc_url( ebox_get_step_permalink( $lesson['post']->ID, $course_id ) ); ?>'><?php echo apply_filters( 'the_title', $lesson['post']->post_title, $lesson['post']->ID ); ?></a>
								<?php /* Not available message for drip feeding modules */ ?>
								<?php if ( ! empty( $lesson['lesson_access_from'] ) ) : ?>
									<?php
										ebox_LMS::get_template(
											'ebox_course_lesson_not_available',
											array(
												'user_id' => $user_id,
												'course_id' => ebox_get_course_id( $lesson['post']->ID ),
												'lesson_id' => $lesson['post']->ID,
												'lesson_access_from_int' => $lesson['lesson_access_from'],
												'lesson_access_from_date' => ebox_adjust_date_time_display( $lesson['lesson_access_from'] ),
												'context' => 'course_content_shortcode',
											),
											true
										);
									?>
								<?php endif; ?>
								<?php /* Lesson Topis */ ?>
								<?php $topics = @$lesson_topics[ $lesson['post']->ID ]; ?> 

								<?php if ( ! empty( $topics ) ) : ?>
									<div id='ebox_topic_dots-<?php echo $lesson['post']->ID; ?>' class='ebox_topic_dots type-list'>
										<ul>
											<?php $odd_class = ''; ?>
											<?php foreach ( $topics as $key => $topic ) : ?>
												<?php $odd_class = empty( $odd_class ) ? 'nth-of-type-odd' : ''; ?>
												<?php $completed_class = empty( $topic->completed ) ? 'topic-notcompleted' : 'topic-completed'; ?>
												<li class='<?php echo $odd_class; ?>'>
													<span class='topic_item'>
														<a class='<?php echo esc_attr( $completed_class ); ?>' href='<?php echo esc_url( ebox_get_step_permalink( $topic->ID, $course_id ) ); ?>' title='<?php echo esc_html( apply_filters( 'the_title', $topic->post_title, $topic->ID ) ); ?>'>
															<span><?php echo apply_filters( 'the_title', $topic->post_title, $topic->ID ); ?></span>
														</a>
													</span>
												</li>
											<?php endforeach; ?>
										</ul>
									</div>
								<?php endif; ?>

							</h4>
						</div>
					<?php endforeach; ?>

				</div>
			</div>
			<?php
				global $course_pager_results;
			if ( isset( $course_pager_results['pager'] ) ) {
				echo ebox_LMS::get_template(
					'ebox_pager.php',
					array(
						'pager_results' => $course_pager_results['pager'],
						'pager_context' => 'course_content',
					)
				);
			}
			?>
		<?php endif; ?>
		<?php
		if ( ( isset( $course_pager_results['pager'] ) ) && ( ! empty( $course_pager_results['pager'] ) ) ) {
			if ( $course_pager_results['pager']['paged'] == $course_pager_results['pager']['total_pages'] ) {
				$show_course_quizzes = true;
			} else {
				$show_course_quizzes = false;
			}
		} else {
			$show_course_quizzes = true;
		}
		?>


		<?php /* Show Quiz List */ ?>	
		<?php
		if ( $show_course_quizzes == true ) {
			if ( ! empty( $quizzes ) ) :
				?>
				<div id='ebox_quizzes'>
					<div id='quiz_heading'>
						<span><?php echo ebox_Custom_Label::get_label( 'quizzes' ); ?></span><span class='right'><?php esc_html_e( 'Status', 'ebox' ); ?></span>
					</div>
					<div id='quiz_list'>
						<?php foreach ( $quizzes as $quiz ) : ?>
							<div id='post-<?php echo $quiz['post']->ID; ?>' class='<?php echo $quiz['sample']; ?>'>
								<div class='list-count'><?php echo $quiz['sno']; ?></div>
								<h4><a class='<?php echo esc_attr( $quiz['status'] ); ?>' href='<?php echo esc_url( ebox_get_step_permalink( $quiz['post']->ID, $course_id ) ); ?>'><?php echo apply_filters( 'the_title', $quiz['post']->post_title, $quiz['post']->ID ); ?></a></h4>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
				<?php
			endif;
		}
		?>
			

	</div>
<?php endif; ?>
