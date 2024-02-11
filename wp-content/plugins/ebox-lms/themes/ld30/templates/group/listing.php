<?php
/**
 * ebox LD30 Displays the listing of team content
 *
 * @var int    $team_id            Team ID.
 * @var int    $user_id             User ID.
 * @var bool   $has_access          User has access to team or is enrolled.
 * @var bool   $team_status        User's Team Status. Completed, No Started, or In Complete.
 * @var array  $team_courses       Array of Team Courses to display in listing.
 * @var array $course_pager_results Array of pager details.
 *
 * @since 3.1.7
 *
 * @package ebox\Templates\LD30
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Display team courses if they exist
 *
 * @since 3.1.7
 *
 * @var $team_courses [array]
 */

if ( ! empty( $team_courses ) ) :

	/**
	 * Filters ebox Team Courses table CSS class.
	 *
	 * @since 3.1.7
	 *
	 * @param string $table_class CSS classes for team courses table.
	 */
	$table_class = apply_filters( 'ebox_team_courses_table_class', 'ld-item-list-items ld-team-courses ld-team-courses-' . $team_id );

	/**
	 * Display the expand button if lesson has topics
	 *
	 * @since 3.0.0
	 *
	 * @var $modules [array]
	 */
	?>

	<div class="<?php echo esc_attr( $table_class ); ?>" id="<?php echo esc_attr( 'ld-item-list-' . $team_id ); ?>" data-ld-expand-list="true" data-ld-expand-id="<?php echo esc_attr( 'ld-item-list-' . $team_id ); ?>">
		<?php
		/**
		 * Fires before the team courses listing.
		 *
		 * @since 3.1.7
		 *
		 * @param int $team_id Team ID.
		 * @param int $user_id  User ID.
		 */
		do_action( 'ebox_team_courses_listing_before', $team_id, $user_id );

		if ( $team_courses && ! empty( $team_courses ) ) {

			foreach ( $team_courses as $course_id ) {
				ebox_get_template_part(
					'team/partials/course-row.php',
					array(
						'team_id'   => $team_id,
						'user_id'    => $user_id,
						'course_id'  => $course_id,
						'has_access' => $has_access,
					),
					true
				);
			}
		}

		/**
		 * Fires after the team courses listing.
		 *
		 * @since 3.1.7
		 *
		 * @param int $team_id Team ID.
		 * @param int $user_id  User ID.
		 */
		do_action( 'ebox_team_listing_after', $team_id, $user_id );

		/**
		 * Fires before the team pagination.
		 *
		 * @since 3.1.7
		 *
		 * @param int $team_id Team ID.
		 * @param int $user_id  User ID.
		 */
		do_action( 'ebox_team_pagination_before', $team_id, $user_id );

		if ( isset( $course_pager_results['pager'] ) ) :
			ebox_get_template_part(
				'modules/pagination.php',
				array(
					'pager_results' => $course_pager_results['pager'],
					'pager_context' => ( isset( $context ) ? $context : 'team_courses' ),
					'team_id'      => $team_id,
				),
				true
			);
		endif;

		/**
		 * Fires after the team pagination.
		 *
		 * @since 3.0.0
		 *
		 * @param int $team_id Team ID.
		 * @param int $user_id  User ID.
		 */
		do_action( 'ebox_team_pagination_after', $team_id, $user_id );
		?>
	</div> <!--/.ld-item-list-items-->
<?php endif; ?>
