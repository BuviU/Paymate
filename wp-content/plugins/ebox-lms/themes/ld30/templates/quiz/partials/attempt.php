<?php
/**
 * ebox LD30 Displays a quiz attempt
 *
 * @since 3.0.0
 *
 * @package ebox\Templates\LD30
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * DEPRECATED
 */

$certificateLink = null;

/**
 * Identify the quiz status and certification
 */
if ( isset( $quiz_attempt['has_graded'] ) && true === (bool) $quiz_attempt['has_graded'] && true === (bool) LD_QuizPro::quiz_attempt_has_ungraded_question( $quiz_attempt ) ) :
	$status = 'pending';
else :
	$certificateLink = @$quiz_attempt['certificate']['certificateLink'];
	$status          = empty( $quiz_attempt['pass'] ) ? 'failed' : 'passed';
endif;

/**
 * Set the quiz title and link
 */
$quiz_title = ! empty( $quiz_attempt['post']->post_title ) ? $quiz_attempt['post']->post_title : @$quiz_attempt['quiz_title'];
$quiz_link  = ! empty( $quiz_attempt['post']->ID ) ? ebox_get_step_permalink( intval( $quiz_attempt['post']->ID ), $course_id ) : '#';

/**
 * Only display the quiz if we've found a title
 */
if ( ! empty( $quiz_title ) ) : ?>

	<div class="<?php echo esc_attr( $status ); ?>">

		<?php echo esc_html( $status ); ?>

		<a href="<?php echo esc_url( $quiz_link ); ?>"><?php echo esc_html( $quiz_title ); ?></a>

		<?php
		if ( ! empty( $certificateLink ) ) :
			?>
			<a href="<?php echo esc_url( $certificateLink ); ?>&time=<?php echo esc_attr( $quiz_attempt['time'] ); ?>" target="_blank">
				<?php esc_html_e( 'Certificate', 'ebox' ); ?>
			</a>
			<?php
		else :
			echo '-';
		endif;
		?>

		<div class="scores">
			<?php
			if ( isset( $quiz_attempt['has_graded'] ) && true === (bool) $quiz_attempt['has_graded'] && true === (bool) LD_QuizPro::quiz_attempt_has_ungraded_question( $quiz_attempt ) ) :
				echo esc_html_x( 'Pending', 'Pending Certificate Status Label', 'ebox' );
			else :
				echo esc_html( round( $quiz_attempt['percentage'], 2 ) . '%' );
			endif;
			?>
		</div>

		<div class="statistics">
			<?php
			if ( get_current_user_id() === absint( $user_id ) || ebox_is_admin_user() || ebox_is_team_leader_user() ) :

				if ( ! isset( $quiz_attempt['statistic_ref_id'] ) || empty( $quiz_attempt['statistic_ref_id'] ) ) :
					$quiz_attempt['statistic_ref_id'] = ebox_get_quiz_statistics_ref_for_quiz_attempt( $user_id, $quiz_attempt );
				endif;

				if ( isset( $quiz_attempt['statistic_ref_id'] ) && ! empty( $quiz_attempt['statistic_ref_id'] ) ) :
					/**
					 * Filters whether to Display User Quiz Statistics.
					 *
					 * This filter allows display-time control over displaying the user quiz statistics link. This
					 * link is shown on the user profile when using the [ld_profile] shortcode, the Course Info
					 * Widget and the user's WP Profile.
					 *
					 * This filter is only called if the quiz_attempt contained the reference field 'statistic_ref_id' which
					 * links the user meta record to the statistics row. Also, the viewing user must a) match the used record
					 * being viewed OR b) be an administrator OR c) be a team leader and the user is within the team leader
					 * managed teams.
					 *
					 * @param boolean $show_stats   This will be true or false and determined from the 'View Profile Statistics' quiz setting.
					 * @param int     $user_id      The ID of the user quiz to be displayed.
					 * @param array   $quiz_attempt This is the quiz attempt array read from the user meta.
					 * @param string  $context      This will be the file where this filter is being called. Possible values
					 * are 'course_info_shortcode.php', 'profile.php' or other.
					 *
					 * @since 2.3.0
					 */
					if ( apply_filters( 'show_user_profile_quiz_statistics', get_post_meta( $quiz_attempt['post']->ID, '_viewProfileStatistics', true ), $user_id, $quiz_attempt, basename( __FILE__ ) ) ) :
						?>
						<a class="user_statistic" data-statistic-nonce="<?php echo esc_attr( wp_create_nonce( 'statistic_nonce_' . $quiz_attempt['statistic_ref_id'] . '_' . get_current_user_id() . '_' . $user_id ) ); ?>" data-user-id="<?php echo esc_attr( $user_id ); ?>" data-quiz-id="<?php echo esc_attr( $quiz_attempt['pro_quizid'] ); ?>" data-ref-id="<?php echo intval( $quiz_attempt['statistic_ref_id'] ); ?>" href="#"><div class="statistic_icon"></div></a>
						<?php
					endif;

				endif;

			endif;
			?>
		</div>

		<div class="quiz_date"><?php echo esc_html( ebox_adjust_date_time_display( $quiz_attempt['time'] ) ); ?></div>
	</div>
<?php endif; ?>
