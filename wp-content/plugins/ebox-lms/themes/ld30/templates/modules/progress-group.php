<?php
/**
 * ebox LD30 Displays team progress
 *
 * @since 3.0.0
 *
 * @package ebox\Templates\LD30
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Action to add custom content before the progress bar
 *
 * @since 3.2.0
 */

$context = ( isset( $context ) ? $context : 'ebox' );

/**
 * Fires before the progress bar.
 *
 * @since 3.2.0
 *
 * @param int $team_id Team ID.
 * @param int $user_id  User ID.
 */
do_action( 'ebox-progress-bar-before', $team_id, $user_id );

/**
 * Fires before the progress bar for any context.
 *
 * The dynamic portion of the hook name, `$context`, refers to the context for which the hook is fired,
 * such as `course`, `lesson`, `topic`, `quiz`, etc.
 *
 * @since 3.2.0
 *
 * @param int $team_id Team ID.
 * @param int $user_id  User ID.
 */
do_action( 'ebox-' . $context . '-progress-bar-before', $team_id, $user_id );

/**
 * In the topic context we're measuring progress through a lesson, not the course itself
 */
if ( 'team' === $context ) {
	$progress = apply_filters( 'ebox-' . $context . '-progress-stats', ebox_get_user_team_progress( $team_id, $user_id ) );
	if ( $progress ) {
		/**
		 * This is just here for reference
		 */ ?>
		<div class="ld-progress ld-progress-inline">
			<div class="ld-progress-heading">
				<?php if ( 'topic' === $context ) : ?>
					<div class="ld-progress-label">
					<?php
					echo sprintf(
						// translators: placeholder: Team.
						esc_html_x( '%s Progress', 'Placeholder: Team', 'ebox' ),
						ebox_Custom_Label::get_label( 'team' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
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
						echo sprintf(
							// translators: placeholder: completed steps, total steps, Courses.
							esc_html_x( '%1$d/%2$d %3$s', 'placeholder: completed steps, total steps, Courses', 'ebox' ),
							esc_html( $progress['completed'] ),
							esc_html( $progress['total'] ),
							ebox_Custom_Label::get_label( 'courses' )
						);
						?>
					</div>
				</div> <!--/.ld-progress-stats-->
			</div>

			<div class="ld-progress-bar">
				<div class="ld-progress-bar-percentage ld-secondary-background" style="<?php echo esc_attr( 'width:' . $progress['percentage'] . '%' ); ?>"></div>
			</div>
		</div> <!--/.ld-progress-->
		<?php
	}
}

/**
 * Action to add custom content before the course content progress bar
 *
 * @since 3.0.0
 */
do_action( 'ebox-' . $context . '-progress-bar-after', $team_id, $user_id );

