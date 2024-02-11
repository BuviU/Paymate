<?php
/**
 * ebox LD30 Displays a team
 *
 * Available Variables:
 *
 * @var int    $team_id          Team ID.
 * @var int    $user_id           User ID.
 * @var bool   $has_access        User has access to team or is enrolled.
 * @var bool   $team_status      User's Team Status. Completed, No Started, or In Complete.
 * @var object $post              Team Post Object.
 * @var array  $team_courses     Array of Team Courses to display in listing.
 * @var string $materials         Team Material from Settings.
 * @var bool   $has_team_content True/False if there is Team Post content.
 *
 * @since 3.1.7
 *
 * @package ebox\Templates\LD30
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// $team_course_ids = ebox_team_enrolled_courses( $team_id );
?>
<div class="<?php echo esc_attr( ebox_the_wrapper_class() ); ?>">

	<?php
	global $course_pager_results;

	/**
	 * Fires before the team.
	 *
	 * @since 3.1.7
	 *
	 * @param int $post_id  Post ID.
	 * @param int $team_id Team ID.
	 * @param int $user_id  User ID.
	 */
	do_action( 'ebox_team_before', get_the_ID(), $team_id, $user_id );

	/**
	 * Fires before the team certificate link.
	 *
	 * @since 3.1.7
	 *
	 * @param int $team_id Team ID.
	 * @param int $user_id  User ID.
	 */
	do_action( 'ebox_team_certificate_link_before', $team_id, $user_id );

	/**
	 * Certificate link
	 */
	if ( ( defined( 'ebox_TEMPLATE_CONTENT_METHOD' ) ) && ( 'shortcode' === ebox_TEMPLATE_CONTENT_METHOD ) ) {
		$shown_content_key = 'ebox-shortcode-wrap-ld_certificate-' . absint( $team_id ) . '_' . absint( $user_id );
		if ( false === strstr( $content, $shown_content_key ) ) {
			$shortcode_out = do_shortcode( '[ld_certificate team_id="' . $team_id . '" user_id="' . $user_id . '" display_as="banner"]' );
			if ( ! empty( $shortcode_out ) ) {
				echo $shortcode_out;
			}
		}
	} else {
		if ( $team_certficate_link && ! empty( $team_certficate_link ) ) :

			ebox_get_template_part(
				'modules/alert.php',
				array(
					'type'    => 'success ld-alert-certificate',
					'icon'    => 'certificate',
					'message' => __( 'You\'ve earned a certificate!', 'ebox' ),
					'button'  => array(
						'url'    => $team_certficate_link,
						'icon'   => 'download',
						'label'  => __( 'Download Certificate', 'ebox' ),
						'target' => '_new',
					),
				),
				true
			);

		endif;
	}

	/**
	 * Fires after the team certificate link.
	 *
	 * @since 3.1.7
	 *
	 * @param int $team_id Team ID.
	 * @param int $user_id  User ID.
	 */
	do_action( 'ebox_team_certificate_link_after', $team_id, $user_id );

	/**
	 * Course info bar
	 */
	if ( ( defined( 'ebox_TEMPLATE_CONTENT_METHOD' ) ) && ( 'shortcode' === ebox_TEMPLATE_CONTENT_METHOD ) ) {
		$shown_content_key = 'ebox-shortcode-wrap-ld_infobar-' . absint( $team_id ) . '_' . absint( $user_id );
		if ( false === strstr( $content, $shown_content_key ) ) {
			$shortcode_out = do_shortcode( '[ld_infobar team_id="' . $team_id . '" user_id="' . $user_id . '"]' );
			if ( ! empty( $shortcode_out ) ) {
				echo $shortcode_out;
			}
		}
	} else {
		ebox_get_template_part(
			'modules/infobar_team.php',
			array(
				'context'      => 'team',
				'team_id'     => $team_id,
				'user_id'      => $user_id,
				'has_access'   => $has_access,
				'team_status' => $team_status,
				'post'         => $post,
			),
			true
		);
	}
	?>

	<?php
	/**
	 * Filters the content to be echoed after the team status section of the team template output.
	 *
	 * @since 3.1.7
	 * See https://developers.ebox.com/hook/ld_after_course_status_template_container/ for example use of this filter used for Courses.
	 *
	 * @param string $content            Custom content showed after the team status section. Can be empty.
	 * @param string $team_status_index Team status index from the course status label
	 * @param int    $team_id           Team ID.
	 * @param int    $user_id            User ID.
	 */
	echo apply_filters( 'ld_after_team_status_template_container', '', ebox_course_status_idx( $team_status ), $team_id, $user_id );

	/**
	 * Content tabs
	 */
	ebox_get_template_part(
		'modules/tabs_team.php',
		array(
			'team_id'  => $team_id,
			'post_id'   => get_the_ID(),
			'user_id'   => $user_id,
			'content'   => $content,
			'materials' => $materials,
			'context'   => 'team',
		),
		true
	);

	/**
	 * Identify if we should show the course content listing
	 *
	 * @var $show_course_content [bool]
	 */
	$show_team_content = ( ! $has_access && 'on' === ebox_get_setting( $team_id, 'team_disable_content_table' ) ? false : true );

	if ( $has_team_content && $show_team_content ) :

		if ( ( defined( 'ebox_TEMPLATE_CONTENT_METHOD' ) ) && ( 'shortcode' === ebox_TEMPLATE_CONTENT_METHOD ) ) {
			$shown_content_key = 'ebox-shortcode-wrap-course_content-' . absint( $team_id ) . '_' . absint( $user_id );
			if ( false === strstr( $content, $shown_content_key ) ) {
				$shortcode_out = do_shortcode( '[course_content team_id="' . $team_id . '" user_id="' . $user_id . '"]' );
				if ( ! empty( $shortcode_out ) ) {
					echo $shortcode_out;
				}
			}
		} else {
			?>
			<div class="ld-item-list ld-lesson-list">
				<div class="ld-section-heading">

					<?php
					/**
					 * Fires before the team heading.
					 *
					 * @since 3.1.7
					 *
					 * @param int $team_id Team ID.
					 * @param int $user_id  User ID.
					 */
					do_action( 'ebox_team_heading_before', $team_id, $user_id );
					?>

					<h2>
					<?php
					printf(
						// translators: placeholders: Team, Courses.
						esc_html_x( '%1$s %2$s', 'placeholders: Team, Courses', 'ebox' ),
						ebox_Custom_Label::get_label( 'team' ),
						ebox_Custom_Label::get_label( 'courses' )
					);
					?>
					</h2>

					<?php
					/**
					 * Fires after the team heading.
					 *
					 * @since 3.1.7
					 *
					 * @param int $team_id Team ID.
					 * @param int $user_id  User ID.
					 */
					do_action( 'ebox_team_heading_after', $team_id, $user_id );
					?>

					<?php if ( true === $has_access ) { ?>
					<div class="ld-item-list-actions" data-ld-expand-list="true">

						<?php
						/**
						 * Fires before the course expand.
						 *
						 * @since 3.1.7
						 *
						 * @param int $team_id Team ID.
						 * @param int $user_id  User ID.
						 */
						do_action( 'ebox_team_expand_before', $team_id, $user_id );

						// Only display if there is something to expand.
						if ( ( isset( $team_courses ) ) && ( ! empty( $team_courses ) ) ) {
							?>
							<div class="ld-expand-button ld-primary-background" id="<?php echo esc_attr( 'ld-expand-button-' . $team_id ); ?>" data-ld-expands="<?php echo esc_attr( 'ld-item-list-' . $team_id ); ?>" data-ld-expand-text="<?php echo esc_attr_e( 'Expand All', 'ebox' ); ?>" data-ld-collapse-text="<?php echo esc_attr_e( 'Collapse All', 'ebox' ); ?>">
								<span class="ld-icon-arrow-down ld-icon"></span>
								<span class="ld-text"><?php echo esc_html_e( 'Expand All', 'ebox' ); ?></span>
							</div> <!--/.ld-expand-button-->
							<?php
							/**
							 * Filters whether to expand all course steps by default. Default is false.
							 *
							 * @since 2.5.0
							 *
							 * @param boolean $expand_all Whether to expand all course steps.
							 * @param int     $course_id  Course ID.
							 * @param string  $context    The context where course is expanded.
							 */
							if ( apply_filters( 'ebox_course_steps_expand_all', false, $team_id, 'course_modules_listing_main' ) ) {
								?>
								<script>
									jQuery( function(){
										setTimeout(function(){
											jQuery("<?php echo esc_attr( '#ld-expand-button-' . $team_id ); ?>").trigger('click');
										}, 1000);
									});
								</script>
								<?php
							}
						}

						/**
						 * Action to add custom content after the course content expand button
						 *
						 * @since 3.0.0
						 *
						 * @param int $team_id Team ID.
						 * @param int $user_id  User ID.
						 */
						do_action( 'ebox_team_expand_after', $team_id, $user_id );
						?>

					</div> <!--/.ld-item-list-actions-->
					<?php } ?>
				</div> <!--/.ld-section-heading-->

				<?php
				/**
				 * Fires before the team content listing
				 *
				 * @since 3.1.7
				 *
				 * @param int $team_id Team ID.
				 * @param int $user_id  User ID.
				 */
				do_action( 'ebox_team_content_list_before', $team_id, $user_id );

				/**
				 * Content content listing
				 *
				 * @since 3.1.7
				 *
				 * ('listing.php');
				 */
				ebox_get_template_part(
					'team/listing.php',
					array(
						'team_id'             => $team_id,
						'user_id'              => $user_id,
						'team_courses'        => $team_courses,
						'has_access'           => $has_access,
						'course_pager_results' => $course_pager_results,
					),
					true
				);

				/**
				 * Fires before the team content listing.
				 *
				 * @since 3.1.7
				 *
				 * @param int $team_id Team ID.
				 * @param int $user_id  User ID.
				 */
				do_action( 'ebox_team_content_list_after', $team_id, $user_id );
				?>

			</div> <!--/.ld-item-list-->

			<?php
		}
	endif;

	/**
	 * Fires before the team listing.
	 *
	 * @since 3.1.7
	 *
	 * @param int $post_id  Post ID.
	 * @param int $team_id Team ID.
	 * @param int $user_id  User ID.
	 */
	do_action( 'ebox_team_after', get_the_ID(), $team_id, $user_id );
	ebox_load_login_modal_html();
	?>
</div>
