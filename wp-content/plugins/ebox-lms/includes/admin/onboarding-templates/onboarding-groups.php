<?php
/**
 * Onboarding Teams Template.
 *
 * Displayed when no entities were added to help the user.
 *
 * @since 3.0.0
 * @package ebox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="ld-onboarding-screen">
	<div class="ld-onboarding-main">
		<span class="dashicons dashicons-welcome-add-page"></span>
		<h2>
		<?php
			echo sprintf(
				// translators: placeholder: Teams.
				esc_html_x( 'You don\'t have any %s yet', 'Placeholder: Teams', 'ebox' ),
				ebox_Custom_Label::get_label( 'teams' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
			);
			?>
		</h2>
		<p>
		<?php
			echo sprintf(
				// translators: Teams, Team, Team.
				esc_html_x( 'Users can be placed into %1$s and assigned a %2$s Leader who can track the progress and performance of any user in the %3$s.', 'Placeholder: Teams, Team, Teams', 'ebox' ),
				ebox_Custom_Label::get_label( 'teams' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
				ebox_Custom_Label::get_label( 'team' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
				ebox_Custom_Label::get_label( 'teams' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
			);
			?>
		</p>
		<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=' . ebox_get_post_type_slug( 'team' ) ) ); ?>" class="button button-secondary">
			<span class="dashicons dashicons-plus-alt"></span>
			<?php
				echo sprintf(
					// translators: placeholder: Team.
					esc_html_x( 'Add your first %s', 'placeholder: Team', 'ebox' ),
					ebox_Custom_Label::get_label( 'team' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
				);
				?>
		</a>
	</div> <!-- .ld-onboarding-main -->

	<div class="ld-onboarding-more-help">
		<div class="ld-onboarding-row">
		<div class="ld-onboarding-col">
				<h3>
				<?php
					echo sprintf(
						// translators: placeholder: Team.
						esc_html_x( 'Creating a %s', 'placeholder: Team', 'ebox' ),
						ebox_Custom_Label::get_label( 'team' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
					);
					?>
				</h3>
				<img src="<?php echo esc_url( ebox_LMS_PLUGIN_URL ); ?>assets/images/post-type-empty-state.jpg" alt="" />
			</div>
			<div class="ld-onboarding-col">
				<h3><?php esc_html_e( 'Related help and documentation', 'ebox' ); ?></h3>
				<ul>
					<li><a href="hthttps://arttricks.com.au/" target="_blank" rel="noopener noreferrer">
					<?php
					echo sprintf(
						// translators: placeholder: Teams.
						esc_html_x( 'Users & %s Documentation (only available in English)', 'placeholder: Team', 'ebox' ),
						ebox_Custom_Label::get_label( 'teams' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
					);
					?>
					</a></li>
				</ul>
			</div>
		</div>

	</div> <!-- .ld-onboarding-more-help -->

</section> <!-- .ld-onboarding-screen -->
