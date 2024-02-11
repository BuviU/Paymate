<?php
/**
 * ebox LD30 Displays a user team lists.
 * This template is called from the [user_teams] shortcode.
 *
 * @param array   $admin_teams     Array of admin team IDs.
 * @param array   $user_teams      Array of user team IDs.
 * @param boolean $has_admin_teams True if there are admin teams.
 * @param boolean $has_user_teams  True if there are user teams.
 *
 * @since 3.0.0
 *
 * @package ebox\Templates\LD30
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="ebox-wrapper">
	<div class="ld-user-teams ld-item-list">

		<?php if ( $has_admin_teams ) : ?>
			<div class="ld-item-list-team-leader ld-team-list">
				<div class="ld-section-heading">
					<h2><?php esc_html_e( 'Team Leader', 'ebox' ); ?></h2>
				</div> <!--/.ld-section-heading-->
				<div class="ld-item-list-items">
					<?php
					foreach ( $admin_teams as $ebox_team_id ) :

						if ( empty( $ebox_team_id ) ) {
							continue;
						}

						$ebox_team = get_post( $ebox_team_id );

						if ( ! $ebox_team || ! is_a( $ebox_team, 'WP_POST' ) ) {
							continue;
						}

						ebox_get_template_part(
							'shortcodes/teams/row.php',
							array(
								'team' => $ebox_team,
							),
							true
						);

					endforeach;
					?>
				</div> <!--/.ld-table-list-items-->
			</div> <!--/.ld-table-list-->
			<?php
		endif;

		if ( $has_user_teams ) :
			?>
			<div class="ld-item-list-team-leader ld-team-list">
				<div class="ld-item-list-team-leader">
					<div class="ld-section-heading">
						<h2>
						<?php
						printf(
							// translators: team.
							esc_html_x( 'Assigned %s(s)', 'placeholder: team', 'ebox' ),
							ebox_get_custom_label( 'team' )
						)
						?>
						</h2>
					</div>
					<div class="ld-item-list-items">
						<?php
						foreach ( $user_teams as $ebox_team_id ) :

							if ( empty( $ebox_team_id ) ) {
								continue;
							}

							$ebox_team = get_post( $ebox_team_id );

							if ( ! $ebox_team || ! is_a( $ebox_team, 'WP_POST' ) ) {
								continue;
							}

							ebox_get_template_part(
								'shortcodes/teams/row.php',
								array(
									'team' => $ebox_team,
								),
								true
							);

						endforeach;
						?>
					</div> <!--/.ld-table-list-items-->
				</div> <!--/.ld-table-list-->
			</div>
		<?php endif; ?>
	</div>
</div>
