<?php
/**
 * Displays a user's profile.
 *
 * Available Variables:
 *
 * $user_id         : Current User ID
 * $current_user    : (object) Currently logged in user object
 * $user_courses    : Array of course ID's of the current user
 * $quiz_attempts   : Array of quiz attempts of the current user
 * $shortcode_atts  : Array of values passed to shortcode
 *
 * @since 2.1.0
 *
 * @package ebox\Templates\Legacy\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php
	global $ebox_assets_loaded;
if ( ! isset( $ebox_assets_loaded['scripts']['ebox_template_script_js'] ) ) {
	$filepath = ebox_LMS::get_template( 'ebox_template_script.js', null, null, true );
	if ( ! empty( $filepath ) ) {
		wp_enqueue_script( 'ebox_template_script_js', ebox_template_url_from_path( $filepath ), array( 'jquery' ), ebox_SCRIPT_VERSION_TOKEN, true );
		$ebox_assets_loaded['scripts']['ebox_template_script_js'] = __FUNCTION__;

		$data            = array();
		$data['ajaxurl'] = admin_url( 'admin-ajax.php' );
		$data            = array( 'json' => wp_json_encode( $data ) );
		wp_localize_script( 'ebox_template_script_js', 'ebox_data', $data );
	}
}
	LD_QuizPro::showModalWindow();
?>
<div id="ebox_profile">

	<div class="expand_collapse">
		<a href="#" onClick='return flip_expand_all("#course_list");'><?php esc_html_e( 'Expand All', 'ebox' ); ?></a> | <a href="#" onClick='return flip_collapse_all("#course_list");'><?php esc_html_e( 'Collapse All', 'ebox' ); ?></a>
	</div>

	<?php if ( ( isset( $shortcode_atts['show_header'] ) ) && ( 'yes' === $shortcode_atts['show_header'] ) ) { ?>

	<div class="ebox_profile_heading">
		<span><?php esc_html_e( 'Profile', 'ebox' ); ?></span>
	</div>

	<div class="profile_info clear_both">
		<div class="profile_avatar">
			<?php echo get_avatar( $current_user->user_email, 96 ); ?>
			<?php
			/** This filter is documented in themes/ld30/templates/shortcodes/profile.php */
			if ( ( current_user_can( 'read' ) ) && ( isset( $shortcode_atts['profile_link'] ) ) && ( true === $shortcode_atts['profile_link'] ) && ( apply_filters( 'ebox_show_profile_link', $shortcode_atts['profile_link'] ) ) ) {
				?>
				<div class="profile_edit_profile" align="center">
					<a href='<?php echo esc_url( get_edit_user_link() ); ?>'><?php esc_html_e( 'Edit profile', 'ebox' ); ?></a>
				</div>
				<?php
			}
			?>
		</div>

		<div class="ebox_profile_details">
			<?php if ( ( ! empty( $current_user->user_lastname ) ) || ( ! empty( $current_user->user_firstname ) ) ) : ?>
				<div><b><?php esc_html_e( 'Name', 'ebox' ); ?>:</b> <?php echo $current_user->user_firstname . ' ' . $current_user->user_lastname; ?></div>
			<?php endif; ?>
			<div><b><?php esc_html_e( 'Username', 'ebox' ); ?>:</b> <?php echo $current_user->user_login; ?></div>
			<div><b><?php esc_html_e( 'Email', 'ebox' ); ?>:</b> <?php echo $current_user->user_email; ?></div>

			<?php if ( ( isset( $shortcode_atts['course_points_user'] ) ) && ( $shortcode_atts['course_points_user'] == 'yes' ) ) { ?>
				<?php echo do_shortcode( '[ld_user_course_points user_id="' . $current_user->ID . '" context="ld_profile"]' ); ?>
			<?php } ?>
		</div>
	</div>
	<?php } ?>

	<div class="ebox_profile_heading no_radius clear_both">
		<span class="ld_profile_course">
		<?php
		// translators: placeholder: Courses.
		printf( esc_html_x( 'Registered %s', 'placeholder: Courses', 'ebox' ), ebox_Custom_Label::get_label( 'courses' ) );
		?>
		</span>
		<span class="ld_profile_status"><?php esc_html_e( 'Status', 'ebox' ); ?></span>
		<span class="ld_profile_certificate"><?php esc_html_e( 'Certificate', 'ebox' ); ?></span>
	</div>

	<div id="course_list">

		<?php if ( ! empty( $user_courses ) ) : ?>

			<?php foreach ( $user_courses as $course_id ) : ?>
				<?php
					$course = get_post( $course_id );

					$course_link = get_permalink( $course_id );

					$progress = ebox_course_progress(
						array(
							'user_id'   => $user_id,
							'course_id' => $course_id,
							'array'     => true,
						)
					);

					$status = ( $progress['percentage'] == 100 ) ? 'completed' : 'notcompleted';
				?>
				<div id='course-<?php echo esc_attr( $user_id ) . '-' . esc_attr( $course->ID ); ?>'>
					<div class="list_arrow collapse flippable"  onClick='return flip_expand_collapse("#course-<?php echo esc_attr( $user_id ); ?>", <?php echo esc_attr( $course->ID ); ?>);'></div>


					<?php
					// @todo Remove h4 container.
					?>
					<h4>
						<div class="ebox-course-link"><a href="<?php echo esc_url( $course_link ); ?>"><?php echo $course->post_title; ?></a></div>

						<div class="ebox-course-status"><a class="<?php echo esc_attr( $status ); ?>" href="<?php echo esc_url( $course_link ); ?>"><?php echo $course->post_title; ?></a></div>
						<div class="ebox-course-certificate">
						<?php
							$certificateLink = ebox_get_course_certificate_link( $course->ID, $user_id );
						if ( ! empty( $certificateLink ) ) {
							?>
								<a target="_blank" href="<?php echo esc_url( $certificateLink ); ?>"><div class="certificate_icon_large"></div></a>
								<?php
						} else {
							?>
								<a style="padding: 10px 2%;" href="#">-</a>
								<?php
						}
						?>
						</div>
						<div class="flip" style="clear: both; display:none;">

							<div class="ebox_profile_heading course_overview_heading">
							<?php
							// translators: placeholder: Course.
							printf( esc_html_x( '%s Progress Overview', 'placeholder: Course', 'ebox' ), ebox_Custom_Label::get_label( 'course' ) );
							?>
							</div>

							<div>
								<dd class="course_progress" title='
								<?php
								echo sprintf(
									// translators: placeholders: completed steps, total steps.
									esc_html_x( '%1$d out of %2$d steps completed', 'placeholders: completed steps, total steps', 'ebox' ),
									$progress['completed'],
									$progress['total']
								);
								?>
									'>
									<div class="course_progress_blue" style='width: <?php echo esc_attr( $progress['percentage'] ); ?>%;'>
								</dd>

								<div class="right">
									<?php
									// translators: placeholder: percent complete.
									echo sprintf( esc_html_x( '%s%% Complete', 'placeholder: percent complete', 'ebox' ), $progress['percentage'] );
									?>
								</div>
							</div>

							<?php
							/** This filter is documented in themes/ld30/templates/shortcodes/profile/course-row.php */
							if ( ( ! empty( $quiz_attempts[ $course_id ] ) ) && ( isset( $shortcode_atts['show_quizzes'] ) ) && ( true === $shortcode_atts['show_quizzes'] ) && ( apply_filters( 'ebox_show_profile_quizzes', $shortcode_atts['show_quizzes'] ) ) ) {
								?>

								<div class="ebox_profile_quizzes clear_both">

									<div class="ebox_profile_quiz_heading">
										<div class="quiz_title"><?php echo ebox_Custom_Label::get_label( 'quizzes' ); ?></div>
										<div class="certificate"><?php esc_html_e( 'Certificate', 'ebox' ); ?></div>
										<div class="scores"><?php esc_html_e( 'Score', 'ebox' ); ?></div>
										<div class="statistics"><?php esc_html_e( 'Statistics', 'ebox' ); ?></div>
										<div class="quiz_date"><?php esc_html_e( 'Date', 'ebox' ); ?></div>
									</div>

									<?php foreach ( $quiz_attempts[ $course_id ] as $k => $quiz_attempt ) : ?>
										<?php
											$certificateLink = null;

											$certificateLink = @$quiz_attempt['certificate']['certificateLink'];
											$status          = empty( $quiz_attempt['pass'] ) ? 'failed' : 'passed';

											$quiz_title = ! empty( $quiz_attempt['post']->post_title ) ? $quiz_attempt['post']->post_title : @$quiz_attempt['quiz_title'];

											$quiz_link = ! empty( $quiz_attempt['post']->ID ) ? ebox_get_step_permalink( intval( $quiz_attempt['post']->ID ), $course_id ) : '#';
										?>
										<?php if ( ! empty( $quiz_title ) ) : ?>
											<div class='<?php echo esc_attr( $status ); ?>'>

												<div class="quiz_title">
													<span class='<?php echo esc_attr( $status ); ?>_icon'></span>
													<a href='<?php echo esc_url( $quiz_link ); ?>'><?php echo esc_attr( $quiz_title ); ?></a>
												</div>

												<div class="certificate">
													<?php if ( ! empty( $certificateLink ) ) : ?>
														<a href='<?php echo esc_url( $certificateLink ); ?>&time=<?php echo esc_attr( $quiz_attempt['time'] ); ?>' target="_blank">
														<div class="certificate_icon"></div></a>
													<?php else : ?>
														<?php echo '-'; ?>
													<?php endif; ?>
												</div>

												<div class="scores">
													<?php if ( ( isset( $quiz_attempt['has_graded'] ) ) && ( true === $quiz_attempt['has_graded'] ) && ( true === LD_QuizPro::quiz_attempt_has_ungraded_question( $quiz_attempt ) ) ) : ?>
														<?php echo esc_html_x( 'Pending', 'Pending Certificate Status Label', 'ebox' ); ?>
													<?php else : ?>
														<?php echo round( $quiz_attempt['percentage'], 2 ); ?>%
													<?php endif; ?>
												</div>

												<div class="statistics">
												<?php
												if ( ( $user_id == get_current_user_id() ) || ( ebox_is_admin_user() ) || ( ebox_is_team_leader_user() ) ) {
													if ( ( ! isset( $quiz_attempt['statistic_ref_id'] ) ) || ( empty( $quiz_attempt['statistic_ref_id'] ) ) ) {
														$quiz_attempt['statistic_ref_id'] = ebox_get_quiz_statistics_ref_for_quiz_attempt( $user_id, $quiz_attempt );
													}

													if ( ( isset( $quiz_attempt['statistic_ref_id'] ) ) && ( ! empty( $quiz_attempt['statistic_ref_id'] ) ) ) {
														/** This filter is documented in themes/ld30/templates/quiz/partials/attempt.php */
														if ( apply_filters(
															'show_user_profile_quiz_statistics',
															get_post_meta( $quiz_attempt['post']->ID, '_viewProfileStatistics', true ),
															$user_id,
															$quiz_attempt,
															basename( __FILE__ )
														) ) {

															?>
																<a class="user_statistic" data-statistic_nonce="<?php echo wp_create_nonce( 'statistic_nonce_' . $quiz_attempt['statistic_ref_id'] . '_' . get_current_user_id() . '_' . $user_id ); ?>" data-user_id="<?php echo $user_id; ?>" data-quiz_id="<?php echo $quiz_attempt['pro_quizid']; ?>" data-ref_id="<?php echo intval( $quiz_attempt['statistic_ref_id'] ); ?>" href="#"><div class="statistic_icon"></div></a>
																<?php
														}
													}
												}
												?>
												</div>

												<div class="quiz_date"><?php echo ebox_adjust_date_time_display( $quiz_attempt['time'] ); ?></div>

											</div>
										<?php endif; ?>
									<?php endforeach; ?>

								</div>
							<?php } ?>

						</div>
					</h4>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>

	</div>
</div>
<?php
echo ebox_LMS::get_template(
	'ebox_pager.php',
	array(
		'pager_results' => $profile_pager,
		'pager_context' => 'profile',
	)
);
?>
<?php
/** This filter is documented in themes/ld30/templates/course.php */
if ( apply_filters( 'ebox_course_steps_expand_all', $shortcode_atts['expand_all'], 0, 'profile_shortcode' ) ) {
	?>
	<script>
		jQuery( function() {
			setTimeout(function(){
				jQuery("#ebox_profile .list_arrow").trigger('click');
			}, 1000);
		});
	</script>
	<?php
}
