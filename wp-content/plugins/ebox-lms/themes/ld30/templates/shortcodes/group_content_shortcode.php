<?php
/**
 * ebox LD30 Displays content of Team
 *
 * Available Variables:
 * $team_id				: (int) ID of the team
 * $team					: (object) Post object of the team
 * $user_id					: Current User ID
 * $team_courses			: (array) Courses in the team
 * $team_status			: Team Status
 * $has_access				: User has access to course or is enrolled.
 * $has_team_content		: Team has course content
 * 
 * @since 4.0.0
 *
 * @package ebox\Templates\LD30
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="ld-item-list ld-lesson-list">
	<div class="ld-section-heading">
		<h2>
		<?php
		printf(
			// translators: placeholders: Team, Courses.
			esc_html_x( '%1$s %2$s', 'placeholders: Team, Courses', 'ebox' ),
			esc_attr( ebox_Custom_Label::get_label( 'team' ) ),
			esc_attr( ebox_Custom_Label::get_label( 'courses' ) )
		);
		?>
		</h2>

		<?php if ( true === $has_access ) { ?>
		<div class="ld-item-list-actions" data-ld-expand-list="true">
			<?php
			// Only display if there is something to expand.
			if ( ( isset( $team_courses ) ) && ( ! empty( $team_courses ) ) ) {
				?>
				<div class="ld-expand-button ld-primary-background" id="<?php echo esc_attr( 'ld-expand-button-' . $team_id ); ?>" data-ld-expands="<?php echo esc_attr( 'ld-item-list-' . $team_id ); ?>" data-ld-expand-text="<?php echo esc_attr_e( 'Expand All', 'ebox' ); ?>" data-ld-collapse-text="<?php echo esc_attr_e( 'Collapse All', 'ebox' ); ?>">
					<span class="ld-icon-arrow-down ld-icon"></span>
					<span class="ld-text"><?php echo esc_html_e( 'Expand All', 'ebox' ); ?></span>
				</div> <!--/.ld-expand-button-->
				<?php
			}
			?>
		</div> <!--/.ld-item-list-actions-->
		<?php } ?>
	</div> <!--/.ld-section-heading-->
	<?php
	ebox_LMS::get_template(
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
	?>

</div> <!--/.ld-item-list-->