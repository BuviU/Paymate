<?php
/**
 * ebox LD30 Displays the infobar in team context
 *
 * @var int    $team_id     Team ID.
 * @var int    $user_id      User ID.
 * @var bool   $has_access   User has access to team or is enrolled.
 * @var bool   $team_status User's Team Status. Completed, No Started, or In Complete.
 * @var object $post         Team Post Object.
 *
 * @since 3.2.0
 *
 * @package ebox\Templates\LD30\Modules
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$team_pricing = ebox_get_team_price( $team_id );

if ( is_user_logged_in() && isset( $has_access ) && $has_access ) :
	?>
	<div class="ld-course-status ld-course-status-enrolled">
		<?php
		/**
		 * Action to add custom content inside the ld-course-status infobox before the progress bar
		 *
		 * @since 3.2.0
		 *
		 * @param string|false $post_type Post type slug.
		 * @param int          $team_id  Team ID.
		 * @param int          $user_id   User ID.
		 */
		do_action( 'ebox-team-infobar-access-progress-before', get_post_type(), $team_id, $user_id );

		ebox_get_template_part(
			'modules/progress-team.php',
			array(
				'context'  => 'team',
				'user_id'  => $user_id,
				'team_id' => $team_id,
			),
			true
		);

		/**
		 * Action to add custom content inside the ld-course-status infobox after the progress bar
		 *
		 * @since 3.2.0
		 *
		 * @param string|false $post_type Post type slug.
		 * @param int          $team_id  Team ID.
		 * @param int          $user_id   User ID.
		 */
		do_action( 'ebox-team-infobar-access-progress-after', get_post_type(), $team_id, $user_id );

		ebox_status_bubble( $team_status );

		/**
		 * Action to add custom content inside the ld-course-status infobox after the access status
		 *
		 * @since 3.2.0
		 *
		 * @param string|false $post_type Post type slug.
		 * @param int          $team_id  Team ID.
		 * @param int          $user_id   User ID.
		 */
		do_action( 'ebox-team-infobar-access-status-after', get_post_type(), $team_id, $user_id );
		?>

	</div> <!--/.ld-course-status-->

<?php elseif ( 'open' !== $team_pricing['type'] ) : ?>

	<div class="ld-course-status ld-course-status-not-enrolled">

		<?php
		/**
		 * Action to add custom content inside the un-enrolled ld-course-status infobox before the status
		 *
		 * @since 3.2.0
		 *
		 * @param string|false $post_type Post type slug.
		 * @param int          $team_id  Team ID.
		 * @param int          $user_id   User ID.
		 */
		do_action( 'ebox-team-infobar-noaccess-status-before', get_post_type(), $team_id, $user_id );
		?>

		<div class="ld-course-status-segment ld-course-status-seg-price">

			<?php do_action( 'ebox-team-infobar-status-cell-before', get_post_type(), $team_id, $user_id ); ?>

			<span class="ld-course-status-label"><?php echo esc_html__( 'Current Status', 'ebox' ); ?></span>
			<div class="ld-course-status-content">
				<span class="ld-status ld-status-waiting ld-tertiary-background" data-ld-tooltip="
				<?php
					printf(
						// translators: placeholder: team
						esc_attr_x( 'Enroll in this %s to get access', 'placeholder: team', 'ebox' ),
						esc_html( ebox_get_custom_label_lower( 'team' ) )
					);
				?>
				">
				<?php esc_html_e( 'Not Enrolled', 'ebox' ); ?></span>
			</div>

			<?php do_action( 'ebox-team-infobar-status-cell-after', get_post_type(), $team_id, $user_id ); ?>

		</div> <!--/.ld-course-status-segment-->

		<?php
		/**
		 * Action to add custom content inside the un-enrolled ld-course-status infobox before the price
		 *
		 * @since 3.0.0
		 *
		 * @param string|false $post_type Post type slug.
		 * @param int          $team_id  Team ID.
		 * @param int          $user_id   User ID.
		 */
		do_action( 'ebox-team-infobar-noaccess-price-before', get_post_type(), $team_id, $user_id );

		/**
		 * Fires inside the un-enrolled course infobox before the price.
		 *
		 * @since 3.0.0
		 *
		 * @param string|false $post_type Post type slug.
		 * @param int          $course_id Course ID.
		 * @param int          $user_id   User ID.
		 */
		do_action( 'ebox-course-infobar-noaccess-price-before', get_post_type(), $team_id, $user_id );
		?>

		<div class="ld-course-status-segment ld-course-status-seg-price ld-course-status-mode-<?php echo esc_attr( $team_pricing['type'] ); ?>">

			<?php
			/**
			 * Fires before the course infobar price cell.
			 *
			 * @since 3.0.0
			 *
			 * @param string|false $post_type Post type slug.
			 * @param int          $course_id Course ID.
			 * @param int          $user_id   User ID.
			 */
			do_action( 'ebox-course-infobar-price-cell-before', get_post_type(), $team_id, $user_id );
			?>

			<span class="ld-course-status-label"><?php echo esc_html__( 'Price', 'ebox' ); ?></span>

			<div class="ld-course-status-content">
			<?php
			// Some simple price settings validation logic. Not 100%.
			$team_pricing = wp_parse_args(
				$team_pricing,
				array(
					'type'             => ebox_DEFAULT_GROUP_PRICE_TYPE,
					'price'            => '',
					'interval'         => '',
					'frequency'        => '',
					'trial_price'      => '',
					'trial_interval'   => '',
					'trial_frequency'  => '',
					'repeats'          => '',
					'repeat_frequency' => '',
				)
			);

			if ( 'subscribe' === $team_pricing['type'] ) {
				if ( ( empty( $team_pricing['price'] ) ) || ( empty( $team_pricing['interval'] ) ) || ( empty( $team_pricing['frequency'] ) ) ) {
					$team_pricing['type']             = ebox_DEFAULT_GROUP_PRICE_TYPE;
					$team_pricing['interval']         = '';
					$team_pricing['frequency']        = '';
					$team_pricing['trial_price']      = '';
					$team_pricing['trial_interval']   = '';
					$team_pricing['trial_frequency']  = '';
					$team_pricing['repeats']          = '';
					$team_pricing['repeat_frequency'] = '';
				} else {
					if ( empty( $team_pricing['trial_price'] ) ) {
						$team_pricing['trial_interval']  = '';
						$team_pricing['trial_frequency'] = '';
					} elseif ( ( empty( $team_pricing['trial_interval'] ) ) || ( empty( $team_pricing['trial_frequency'] ) ) ) {
						$team_pricing['trial_price'] = '';
					}
				}
			}

			if ( 'subscribe' !== $team_pricing['type'] ) {
				?>
				<span class="ld-course-status-price">
					<?php
					if ( ! empty( $team_pricing['price'] ) ) {
						echo wp_kses_post( ebox_get_price_formatted( $team_pricing['price'] ) );
					} elseif ( in_array( $team_pricing['type'], array( 'closed', 'free' ), true ) ) {
							/**
							 * Filters label to be displayed when there is no price set for a course or it is closed.
							 *
							 * @since 3.0.0
							 *
							 * @param string $label The label displayed when there is no price.
							 */
							$label = apply_filters( 'ebox_no_price_price_label', ( 'closed' === $team_pricing['type'] ? __( 'Closed', 'ebox' ) : __( 'Free', 'ebox' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Late escaped on output
							echo esc_html( $label );
					}
					?>
				</span>
				<?php
			} elseif ( 'subscribe' === $team_pricing['type'] ) {
				if ( ! empty( $team_pricing['price'] ) ) {
					if ( ! empty( $team_pricing['trial_price'] ) ) {
						?>
						<span class="ld-course-status-trial-price">
						<?php
						echo '<p class="ld-text ld-trial-text">';
						echo wp_kses_post( ebox_get_price_formatted( $team_pricing['trial_price'] ) );
						echo '</p>';
						echo '<p class="ld-trial-pricing ld-pricing">';
						if ( ( ! empty( $team_pricing['trial_interval'] ) ) && ( ! empty( $team_pricing['trial_frequency'] ) ) ) {
							printf(
								// translators: placeholders: Trial interval, Trial frequency.
								esc_html_x( 'Trial price for %1$s %2$s, then', 'placeholders: Trial interval, Trial frequency', 'ebox' ),
								absint( $team_pricing['trial_interval'] ),
								esc_html( $team_pricing['trial_frequency'] )
							);
						}
						echo '</p>'; // closing '<p class="ld-trial-pricing ld-pricing">'
						?>
						</span>
						<span class="ld-course-status-course-price">
							<?php
							echo '<p class="ld-text ld-course-text">';
							echo wp_kses_post( ebox_get_price_formatted( $team_pricing['price'] ) );
							echo '</p>';
							echo '<p class="ld-course-pricing ld-pricing">';

							if ( ( ! empty( $team_pricing['interval'] ) ) && ( ! empty( $team_pricing['frequency'] ) ) ) {
								printf(
									// translators: placeholders: %1$s Interval of recurring payments (number), %2$s Frequency of recurring payments: day, week, month or year.
									esc_html_x( 'Full price every %1$s %2$s afterward', 'Recurring duration message', 'ebox' ),
									absint( $team_pricing['interval'] ),
									esc_html( $team_pricing['frequency'] )
								);

								if ( ! empty( $team_pricing['repeats'] ) ) {
									echo ' ';
									printf(
										// translators: placeholders: %1$s Number of times the recurring payment repeats, %2$s Frequency of recurring payments: day, week, month, year.
										esc_html__( 'for %1$s %2$s', 'ebox' ),
										// Get correct total time by multiplying interval by number of repeats
										absint( $team_pricing['interval'] * $team_pricing['repeats'] ),
										esc_html( $team_pricing['repeat_frequency'] )
									);
								}
							}

							echo '</p>'; // closing '<p class="ld-course-pricing ld-pricing">'.
							?>
						</span>
						<?php
					} else {
						?>
						<span class="ld-course-status-price">
						<?php
						if ( ! empty( $team_pricing['price'] ) ) {
							echo wp_kses_post( ebox_get_price_formatted( $team_pricing['price'] ) );
						}
						?>
						</span>
						<span class="ld-text ld-recurring-duration">
								<?php
								if ( ( ! empty( $team_pricing['interval'] ) ) && ( ! empty( $team_pricing['frequency'] ) ) ) {
									echo sprintf(
										// translators: Recurring duration message.
										esc_html_x( 'Every %1$s %2$s', 'Recurring duration message', 'ebox' ),
										esc_html( $team_pricing['interval'] ),
										esc_html( $team_pricing['frequency'] )
									);

									if ( ( ! empty( $team_pricing['repeats'] ) ) && ( ! empty( $team_pricing['repeat_frequency'] ) ) ) {
										printf(
											// translators: placeholders: %1$s Number of times the recurring payment repeats, %2$s Frequency of recurring payments: day, week, month, year.
											esc_html__( ' for %1$s %2$s', 'ebox' ),
											// Get correct total time by multiplying interval by number of repeats
											absint( $team_pricing['interval'] * $team_pricing['repeats'] ),
											esc_html( $team_pricing['repeat_frequency'] )
										);
									}
								}
								?>
						</span>
						<?php
					}
				}
			}
			?>
			</div>

			<?php
			/**
			 * Fires after the infobar price cell.
			 *
			 * @since 3.0.0
			 *
			 * @param string|false $post_type Post type slug.
			 * @param int          $course_id Course ID.
			 * @param int          $user_id   User ID.
			 */
			do_action( 'ebox-course-infobar-price-cell-after', get_post_type(), $team_id, $user_id );
			?>

		</div> <!--/.ld-team-status-segment-->

		<?php
		/**
		 * Action to add custom content inside the un-enrolled ld-course-status infobox before the action
		 *
		 * @since 3.2.0
		 *
		 * @param string|false $post_type Post type slug.
		 * @param int          $team_id  Team ID.
		 * @param int          $user_id   User ID.
		 */
		do_action( 'ebox-team-infobar-noaccess-action-before', get_post_type(), $team_id, $user_id );

		$team_status_class = apply_filters(
			'ld-course-status-segment-class',
			'ld-course-status-segment ld-course-status-seg-action status-' .
			( isset( $team_pricing['type'] ) ? sanitize_title( $team_pricing['type'] ) : '' )
		);
		?>

		<div class="<?php echo esc_attr( $team_status_class ); ?>">
			<span class="ld-course-status-label"><?php echo esc_html_e( 'Get Started', 'ebox' ); ?></span>
			<div class="ld-course-status-content">
				<div class="ld-course-status-action">
					<?php
						do_action( 'ebox-course-infobar-action-cell-before', get_post_type(), $team_id, $user_id );

						$login_model = ebox_Settings_Section::get_section_setting( 'ebox_Settings_Theme_LD30', 'login_mode_enabled' );

						/** This filter is documented in themes/ld30/includes/shortcode.php */
						$login_url = apply_filters( 'ebox_login_url', ( 'yes' === $login_model ? '#login' : wp_login_url( get_permalink() ) ) );

					switch ( $team_pricing['type'] ) {
						case ( 'open' ):
						case ( 'free' ):
							if ( apply_filters( 'ebox_login_modal', true, $team_id, $user_id ) && ! is_user_logged_in() ) :
								echo '<a class="ld-button" href="' . esc_url( $login_url ) . '">' . esc_html__( 'Login to Enroll', 'ebox' ) . '</a></span>';
								else :
									echo ebox_payment_buttons( $post ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Outputs Payment button HTML
								endif;
							break;
						case ( 'paynow' ):
						case ( 'subscribe' ):
							// Price (Free / Price)
							$ld_payment_buttons = ebox_payment_buttons( $post );
							echo $ld_payment_buttons; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Outputs Button HTML
							if ( apply_filters( 'ebox_login_modal', true, $team_id, $user_id ) && ! is_user_logged_in() ) :
								echo '<span class="ld-text">';
								if ( ! empty( $ld_payment_buttons ) ) {
									esc_html_e( 'or', 'ebox' );
								}
								echo '<a class="ld-login-text" href="' . esc_url( $login_url ) . '">' . esc_html__( 'Login', 'ebox' ) . '</a></span>';
								endif;
							break;
						case ( 'closed' ):
							$button = ebox_payment_buttons( $post );
							if ( empty( $button ) ) :
								echo '<span class="ld-text">' . sprintf(
									// translators: placeholder: team
									esc_html_x( 'This %s is currently closed', 'placeholder: team', 'ebox' ),
									esc_html( ebox_get_custom_label_lower( 'team' ) )
								)
									. '</span>';
								else :
									echo $button; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Outputs Button HTML
								endif;
							break;
					}

					/**
					 * Fires after the team infobar action cell.
					 *
					 * @since 3.2.0
					 *
					 * @param string|false $post_type Post type slug.
					 * @param int          $team_id  Team ID.
					 * @param int          $user_id   User ID.
					 */
					do_action( 'ebox-team-infobar-action-cell-after', get_post_type(), $team_id, $user_id );
					?>
				</div>
			</div>
		</div> <!--/.ld-team-status-action-->

		<?php
		/**
		 * Fires inside the un-enrolled team infobox after the price
		 *
		 * @since 3.2.0
		 *
		 * @param string|false $post_type Post type slug.
		 * @param int          $team_id  Team ID.
		 * @param int          $user_id   User ID.
		 */
		do_action( 'ebox-team-infobar-noaccess-price-after', get_post_type(), $team_id, $user_id );
		?>

	</div> <!--/.ld-course-status-->

<?php endif; ?>
