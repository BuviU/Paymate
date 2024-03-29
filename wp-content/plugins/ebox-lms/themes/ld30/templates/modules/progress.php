<?php
/**
 * ebox LD30 Displays course progress
 *
 * @since 3.0.0
 *
 * @package ebox\Templates\LD30
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fires before the progress bar
 *
 * @since 3.0.0
 */

$context = ( isset( $context ) ? $context : 'ebox' );

/**
 * Fires before the progress bar.
 *
 * @since 3.0.0
 *
 * @param int $course_id Course ID.
 * @param int $user_id   User ID.
 */
do_action( 'ebox-progress-bar-before', $course_id, $user_id );

/**
 * Fires before the progress bar for any context.
 *
 * The dynamic portion of the hook name, `$context`, refers to the context for which the hook is fired,
 * such as `course`, `lesson`, `topic`, `quiz`, etc.
 *
 * @since 3.0.0
 *
 * @param int $course_id Course ID.
 * @param int $user_id   User ID.
 */
do_action( 'ebox-' . $context . '-progress-bar-before', $course_id, $user_id );

/**
 * In the topic context we're measuring progress through a lesson, not the course itself
 */
if ( 'topic' !== $context ) {

	/**
	 * Filters ebox progress arguments.
	 * This filter will not be called if the context is `topic`.
	 *
	 * @since 3.0.0
	 *
	 * @param array $progress_args An array of progress arguments.
	 * @param int   $course_id     Course ID.
	 * @param int   $user_id       User ID.
	 */
	$progress_args = apply_filters(
		'ebox_progress_args',
		array(
			'array'     => true,
			'course_id' => $course_id,
			'user_id'   => $user_id,
		),
		$course_id,
		$user_id,
		$context
	);

	/**
	 * Filters the progress statistics.
	 *
	 * The dynamic portion of the hook name, `$context`, refers to the context of progress,
	 * such as `course`, `lesson`, `topic`, `quiz`, etc.
	 *
	 * @since 3.0.0
	 *
	 * @param string $progress_markup The HTML template of users course/lesson progress
	 */
	$progress = apply_filters( 'ebox-' . $context . '-progress-stats', ebox_course_progress( $progress_args ) );

} else {
	//global $post;

	/** This filter is documented in themes/ld30/templates/modules/progress.php */
	$progress = apply_filters( 'ebox-' . $context . '-progress-stats', ebox_lesson_progress( $post, $course_id ) );
}

if ( $progress ) :
	/**
	 * This is just here for reference
	 */ ?>
	<div class="ld-progress
	<?php
	if ( 'course' === $context ) :
		?>
		ld-progress-inline<?php endif; ?>">
		<?php if ( 'focus' === $context ) : ?>
			<div class="ld-progress-wrap">
		<?php endif; ?>
			<div class="ld-progress-heading">
				<?php if ( 'topic' === $context ) : ?>
					<div class="ld-progress-label">
					<?php
					echo sprintf(
						// translators: placeholder: Lesson Progress.
						esc_html_x( '%s Progress', 'Placeholder: Lesson Progress', 'ebox' ),
						ebox_Custom_Label::get_label( 'lesson' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
					);
					?>
					</div>
				<?php endif; ?>
				<div class="ld-progress-stats">
					<div class="ld-progress-percentage ld-secondary-color">
					<?php
					echo sprintf(
						// translators: placeholder: Progress percentage.
						esc_html_x( '%s%% Complete', 'placeholder: Progress percentage', 'ebox' ),
						esc_html( $progress['percentage'] )
					);
					?>
					</div>
					<div class="ld-progress-steps">
						<?php
						if ( 'course' === $context || 'focus' === $context ) :
							$course_args     = array(
								'course_id'     => $course_id,
								'user_id'       => $user_id,
								'post_id'       => $course_id,
								'activity_type' => 'course',
							);
							$course_activity = ebox_get_user_activity( $course_args );

							if ( ! empty( $course_activity->activity_updated ) && 'course' === $context ) :
								echo sprintf(
									// translators: Last activity date in infobar.
									esc_html_x( 'Last activity on %s', 'Last activity date in infobar', 'ebox' ),
									esc_html( ebox_adjust_date_time_display( $course_activity->activity_updated ) )
								);
							else :
								echo sprintf(
									// translators: placeholders: completed steps, total steps.
									esc_html_x( '%1$d/%2$d Steps', 'placeholders: completed steps, total steps', 'ebox' ),
									esc_html( $progress['completed'] ),
									esc_html( $progress['total'] )
								);
							endif;
						endif;
						?>
					</div>
				</div> <!--/.ld-progress-stats-->
			</div>

			<div class="ld-progress-bar">
				<div class="ld-progress-bar-percentage ld-secondary-background" style="<?php echo esc_attr( 'width:' . $progress['percentage'] . '%' ); ?>"></div>
			</div>
			<?php if ( 'focus' === $context ) : ?>
				</div> <!--/.ld-progress-wrap-->
			<?php endif; ?>
	</div> <!--/.ld-progress-->
	<?php
endif;
/**
 * Fires before the course content progress bar.
 *
 * @since 3.0.0
 *
 * @param int $course_id Course ID.
 * @param int $user_id   User ID.
 */
do_action( 'ebox-progress-bar-after', $course_id, $user_id );

/**
 * Fires before the course steps for any context.
 *
 * The dynamic portion of the hook name, `$context`, refers to the context for which the hook is fired,
 * such as `course`, `lesson`, `topic`, `quiz`, etc.
 *
 * @since 3.0.0
 *
 * @param int $course_id Course ID.
 * @param int $user_id   User ID.
 */
do_action( 'ebox-' . $context . '-progress-bar-after', $course_id, $user_id );
