<?php
/**
 * Displays a user team lists.
 * This template is called from the [user_teams] shortcode.
 *
 * @param array $admin_teams Array of admin team IDs.
 * @param array $user_teams Array of user team IDs.
 * @param boolean $has_admin_teams True if there are admin teams.
 * @param boolean $has_user_teams True if there are user teams.
 *
 * @since 2.1.0
 *
 * @package ebox\Templates\Legacy\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="ebox-user-teams">
<?php if ( $has_admin_teams ) { ?>
	<div class="ebox-user-teams-section ebox-user-teams-section-leader-list">
		<div class="ebox-user-teams-header">
		<?php
		printf(
			// translators: placeholder: Team Leader.
			esc_html_x( '%s in : ', 'placeholder: Team Leader', 'ebox' ),
			ebox_get_custom_label( 'team_leader' )
		)
		?>
		</div>
		<ul class="ebox-user-teams-items">
			<?php
			foreach ( $admin_teams as $team_id ) {
				if ( ! empty( $team_id ) ) {
					$team = get_post( $team_id );
					if ( ( $team ) && ( is_a( $team, 'WP_Post' ) ) ) {
						?>
							<li class="ebox-user-teams-item">
								<span class="ebox-user-teams-item-title"><?php echo $team->post_title; ?></span>
							<?php
							if ( ! empty( $team->post_content ) ) {
								ebox_LMS::content_filter_control( false );
								/** This filter is documented in https://developer.wordpress.org/reference/hooks/the_content/ */
								$team_content = apply_filters( 'the_content', $team->post_content );
								$team_content = str_replace( ']]>', ']]&gt;', $team_content );
								echo $team_content;

								ebox_LMS::content_filter_control( true );
							}
							?>
								</li>
							<?php
					}
				}
			}
			?>
		</ul>
	</div>
<?php } ?>

<?php if ( $has_user_teams ) { ?>
	<div class="ebox-user-teams-section ebox-user-teams-section-assigned-list">
		<div class="ebox-user-teams-header">
		<?php
		printf(
			// translators: team.
			esc_html_x( 'Assigned %s(s) : ', 'placeholder: team', 'ebox' ),
			ebox_get_custom_label( 'team' )
		)
		?>
		</div>
		<ul class="ebox-user-teams-items">
			<?php
			foreach ( $user_teams as $team_id ) {
				if ( ! empty( $team_id ) ) {
					$team = get_post( $team_id );
					if ( ( $team ) && ( is_a( $team, 'WP_Post' ) ) ) {
						?>
							<li class="ebox-user-teams-item">
								<span class="ebox-user-teams-item-title"><?php echo $team->post_title; ?></span>
							<?php
							if ( ! empty( $team->post_content ) ) {
								/** This filter is documented in https://developer.wordpress.org/reference/hooks/the_excerpt/ */
								$team_content = apply_filters( 'the_excerpt', $team->post_content );
								$team_content = str_replace( ']]>', ']]&gt;', $team_content );
								echo $team_content;
							}
							?>
							</li>
							<?php
					}
				}
			}
			?>
		</ul>
	</div>
<?php } ?>
</div>
