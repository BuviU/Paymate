<?php
/**
 * ebox LD30 Displays the infobar in course context
 *
 * Will have access to same variables as course.php
 *
 * Available Variables:
 * $course_id                  : (int) ID of the course
 * $course                     : (object) Post object of the course
 * $course_settings            : (array) Settings specific to current course
 *
 * $courses_options            : Options/Settings as configured on Course Options page
 * $modules_options            : Options/Settings as configured on modules Options page
 * $quizzes_options            : Options/Settings as configured on Quiz Options page
 *
 * $user_id                    : Current User ID
 * $logged_in                  : User is logged in
 * $current_user               : (object) Currently logged in user object
 *
 * $course_status              : Course Status
 * $has_access                 : User has access to course or is enrolled.
 * $materials                  : Course Materials
 * $has_course_content         : Course has course content
 * $modules                    : modules Array
 * $quizzes                    : Quizzes Array
 * $lesson_progression_enabled : (true/false)
 * $has_topics                 : (true/false)
 * $lesson_topics              : (array) modules topics
 *
 * @since 3.0.0
 *
 * @package ebox\Templates\LD30\Modules
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$course_pricing = ebox_get_course_price( $course_id );

if ( is_user_logged_in() && isset( $has_access ) && $has_access ) :
	?>

	<div class="ld-course-status ld-course-status-enrolled">

		<?php
		/**
		 * Fires inside the breadcrumbs (before).
		 *
		 * @since 3.0.0
		 *
		 * @param string|false $post_type Post type slug.
		 * @param int          $course_id Course ID.
		 * @param int          $user_id   User ID.
		 */
		do_action( 'ebox-course-infobar-access-progress-before', get_post_type(), $course_id, $user_id );

		ebox_get_template_part(
			'modules/progress.php',
			array(
				'context'   => 'course',
				'user_id'   => $user_id,
				'course_id' => $course_id,
			),
			true
		);

		/**
		 * Fires inside the breadcrumbs after the progress bar.
		 *
		 * @since 3.0.0
		 *
		 * @param string|false $post_type Post type slug.
		 * @param int          $course_id Course ID.
		 * @param int          $user_id   User ID.
		 */
		do_action( 'ebox-course-infobar-access-progress-after', get_post_type(), $course_id, $user_id );

		ebox_status_bubble( $course_status );

		/**
		 * Fires inside the breadcrumbs after the status.
		 *
		 * @since 3.0.0
		 *
		 * @param string|false $post_type Post type slug.
		 * @param int          $course_id Course ID.
		 * @param int          $user_id   User ID.
		 */
		do_action( 'ebox-course-infobar-access-status-after', get_post_type(), $course_id, $user_id );
		?>

	</div> <!--/.ld-course-status-->

<?php elseif ( 'open' !== $course_pricing['type'] ) : ?>

	<div class="ld-course-status ld-course-status-not-enrolled">

		<?php
		/**
		 * Fires inside the un-enrolled course infobox before the status.
		 *
		 * @since 3.0.0
		 *
		 * @param string|false $post_type Post type slug.
		 * @param int          $course_id Course ID.
		 * @param int          $user_id   User ID.
		 */
		do_action( 'ebox-course-infobar-noaccess-status-before', get_post_type(), $course_id, $user_id );
		?>

		<div class="ld-course-status-segment ld-course-status-seg-status">

			<?php
			/**
			 * Fires before the course infobar status cell.
			 *
			 * @since 3.0.0
			 *
			 * @param string|false $post_type Post type slug.
			 * @param int          $course_id Course ID.
			 * @param int          $user_id   User ID.
			 */
			do_action( 'ebox-course-infobar-status-cell-before', get_post_type(), $course_id, $user_id );
			?>

			<span class="ld-course-status-label"><?php echo esc_html__( 'Current Status', 'ebox' ); ?></span>
			<div class="ld-course-status-content">
				<span class="ld-status ld-status-waiting ld-tertiary-background" data-ld-tooltip="
				<?php
					printf(
						// translators: placeholder: course.
						esc_attr_x( 'Enroll in this %s to get access', 'placeholder: course', 'ebox' ),
						esc_html( ebox_get_custom_label_lower( 'course' ) )
					);
				?>
				">
				<?php esc_html_e( 'Not Enrolled', 'ebox' ); ?></span>
			</div>

			<?php
			/**
			 * Fires after the course infobar status cell.
			 *
			 * @since 3.0.0
			 *
			 * @param string|false $post_type Post type slug.
			 * @param int          $course_id Course ID.
			 * @param int          $user_id   User ID.
			 */
			do_action( 'ebox-course-infobar-status-cell-after', get_post_type(), $course_id, $user_id );
			?>

		</div> <!--/.ld-course-status-segment-->

		<?php
		/**
		 * Fires inside the un-enrolled course infobox before the price.
		 *
		 * @since 3.0.0
		 *
		 * @param string|false $post_type Post type slug.
		 * @param int          $course_id Course ID.
		 * @param int          $user_id   User ID.
		 */
		do_action( 'ebox-course-infobar-noaccess-price-before', get_post_type(), $course_id, $user_id );
		?>

		<div class="ld-course-status-segment ld-course-status-seg-price ld-course-status-mode-<?php echo esc_attr( $course_pricing['type'] ); ?>">

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
			do_action( 'ebox-course-infobar-price-cell-before', get_post_type(), $course_id, $user_id );
			?>

			<span class="ld-course-status-label"><?php echo esc_html__( 'Price', 'ebox' ); ?></span>

			<div class="ld-course-status-content">
			<?php
			// Some simple price settings validation logic. Not 100%.
			$course_pricing = wp_parse_args(
				$course_pricing,
				array(
					'type'             => ebox_DEFAULT_COURSE_PRICE_TYPE,
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

			if ( 'subscribe' === $course_pricing['type'] ) {
				if ( ( empty( $course_pricing['price'] ) ) || ( empty( $course_pricing['interval'] ) ) || ( empty( $course_pricing['frequency'] ) ) ) {
					$course_pricing['type']             = ebox_DEFAULT_COURSE_PRICE_TYPE;
					$course_pricing['interval']         = '';
					$course_pricing['frequency']        = '';
					$course_pricing['trial_price']      = '';
					$course_pricing['trial_interval']   = '';
					$course_pricing['trial_frequency']  = '';
					$course_pricing['repeats']          = '';
					$course_pricing['repeat_frequency'] = '';
				} else {
					if ( empty( $course_pricing['trial_price'] ) ) {
						$course_pricing['trial_interval']  = '';
						$course_pricing['trial_frequency'] = '';
					} elseif ( ( empty( $course_pricing['trial_interval'] ) ) || ( empty( $course_pricing['trial_frequency'] ) ) ) {
						$course_pricing['trial_price'] = '';
					}
				}
			}

			if ( 'subscribe' !== $course_pricing['type'] ) {
				?>
				<span class="ld-course-status-price">
					<?php
					if ( ! empty( $course_pricing['price'] ) ) {
						echo wp_kses_post( ebox_get_price_formatted( $course_pricing['price'] ) );
					} elseif ( in_array( $course_pricing['type'], array( 'closed', 'free' ), true ) ) {
							/**
							 * Filters label to be displayed when there is no price set for a course or it is closed.
							 *
							 * @since 3.0.0
							 *
							 * @param string $label The label displayed when there is no price.
							 */
							$label = apply_filters( 'ebox_no_price_price_label', ( 'closed' === $course_pricing['type'] ? __( 'Closed', 'ebox' ) : __( 'Free', 'ebox' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Late escaped on output
							echo esc_html( $label );
					}
					?>
				</span>
				<?php
			} elseif ( 'subscribe' === $course_pricing['type'] ) {
				if ( ! empty( $course_pricing['price'] ) ) {
					if ( ! empty( $course_pricing['trial_price'] ) ) {
						?>
						<span class="ld-course-status-trial-price">
						<?php
						echo '<p class="ld-text ld-trial-text">';
						echo wp_kses_post( ebox_get_price_formatted( $course_pricing['trial_price'] ) );
						echo '</p>';
						echo '<p class="ld-trial-pricing ld-pricing">';
						if ( ( ! empty( $course_pricing['trial_interval'] ) ) && ( ! empty( $course_pricing['trial_frequency'] ) ) ) {
							printf(
								// translators: placeholders: Trial interval, Trial frequency.
								esc_html_x( 'Trial price for %1$s %2$s, then', 'placeholders: Trial interval, Trial frequency', 'ebox' ),
								absint( $course_pricing['trial_interval'] ),
								esc_html( $course_pricing['trial_frequency'] )
							);
						}
						echo '</p>'; // closing '<p class="ld-trial-pricing ld-pricing">'
						?>
						</span>
						<span class="ld-course-status-course-price">
							<?php
							echo '<p class="ld-text ld-course-text">';
							echo wp_kses_post( ebox_get_price_formatted( $course_pricing['price'] ) );
							echo '</p>';
							echo '<p class="ld-course-pricing ld-pricing">';

							if ( ( ! empty( $course_pricing['interval'] ) ) && ( ! empty( $course_pricing['frequency'] ) ) ) {
								printf(
									// translators: placeholders: %1$s Interval of recurring payments (number), %2$s Frequency of recurring payments: day, week, month or year.
									esc_html_x( 'Full price every %1$s %2$s afterward', 'Recurring duration message', 'ebox' ),
									absint( $course_pricing['interval'] ),
									esc_html( $course_pricing['frequency'] )
								);

								if ( ! empty( $course_pricing['repeats'] ) ) {
									echo ' ';
									printf(
										// translators: placeholders: %1$s Number of times the recurring payment repeats, %2$s Frequency of recurring payments: day, week, month, year.
										esc_html__( 'for %1$s %2$s', 'ebox' ),
										// Get correct total time by multiplying interval by number of repeats
										absint( $course_pricing['interval'] * $course_pricing['repeats'] ),
										esc_html( $course_pricing['repeat_frequency'] )
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
						if ( ! empty( $course_pricing['price'] ) ) {
							echo wp_kses_post( ebox_get_price_formatted( $course_pricing['price'] ) );
						}
						?>
						</span>
						<span class="ld-text ld-recurring-duration">
								<?php
								if ( ( ! empty( $course_pricing['interval'] ) ) && ( ! empty( $course_pricing['frequency'] ) ) ) {
									echo sprintf(
										// translators: Recurring duration message.
										esc_html_x( 'Every %1$s %2$s', 'Recurring duration message', 'ebox' ),
										esc_html( $course_pricing['interval'] ),
										esc_html( $course_pricing['frequency'] )
									);

									if ( ( ! empty( $course_pricing['repeats'] ) ) && ( ! empty( $course_pricing['repeat_frequency'] ) ) ) {
										printf(
											// translators: placeholders: %1$s Number of times the recurring payment repeats, %2$s Frequency of recurring payments: day, week, month, year.
											esc_html__( ' for %1$s %2$s', 'ebox' ),
											// Get correct total time by multiplying interval by number of repeats
											absint( $course_pricing['interval'] * $course_pricing['repeats'] ),
											esc_html( $course_pricing['repeat_frequency'] )
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
			do_action( 'ebox-course-infobar-price-cell-after', get_post_type(), $course_id, $user_id );
			?>

		</div> <!--/.ld-course-status-segment-->

		<?php
		/**
		 * Fires inside the un-enrolled course infobox before the action.
		 *
		 * @since 3.0.0
		 *
		 * @param string|false $post_type Post type slug.
		 * @param int          $course_id Course ID.
		 * @param int          $user_id   User ID.
		 */
		do_action( 'ebox-course-infobar-noaccess-action-before', get_post_type(), $course_id, $user_id );

		/**
		 * Filters infobar course status segment CSS class.
		 *
		 * @since 3.0.0
		 *
		 * @param string $segment_class The List of course segment CSS class.
		 */
		$course_status_class = apply_filters(
			'ld-course-status-segment-class',
			'ld-course-status-segment ld-course-status-seg-action status-' .
			( isset( $course_pricing['type'] ) ? sanitize_title( $course_pricing['type'] ) : '' )
		);
		?>

		<div class="<?php echo esc_attr( $course_status_class ); ?>">
			<span class="ld-course-status-label"><?php echo esc_html_e( 'Get Started', 'ebox' ); ?></span>
			<div class="ld-course-status-content">
				<div class="ld-course-status-action">
					<?php
						/**
						 * Fires before the course infobar action cell.
						 *
						 * @since 3.0.0
						 *
						 * @param string|false $post_type Post type slug.
						 * @param int          $course_id Course ID.
						 * @param int          $user_id   User ID.
						 */
						do_action( 'ebox-course-infobar-action-cell-before', get_post_type(), $course_id, $user_id );

						$login_model = ebox_Settings_Section::get_section_setting( 'ebox_Settings_Theme_LD30', 'login_mode_enabled' );

						/** This filter is documented in themes/ld30/includes/shortcodes.php */
						$login_url = apply_filters( 'ebox_login_url', ( 'yes' === $login_model ? '#login' : wp_login_url( get_permalink() ) ) );

					switch ( $course_pricing['type'] ) {
						case ( 'open' ):
						case ( 'free' ):
							/**
							 * Filters whether to show login modal.
							 *
							 * @since 3.0.0
							 *
							 * @param boolean $show_login_modal Whether to show login modal.
							 * @param int     $course_id        Course ID.
							 * @param int     $user_id          User ID.
							 */
							if ( apply_filters( 'ebox_login_modal', true, $course_id, $user_id ) && ! is_user_logged_in() ) :
								echo '<a class="ld-button" href="' . esc_url( $login_url ) . '">' . esc_html__( 'Login to Enroll', 'ebox' ) . '</a></span>';
								else :
									echo ebox_payment_buttons( $post ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Outputs Payment button HTML
								endif;
							break;
						case ( 'paynow' ):
						case ( 'subscribe' ):
							// Price (Free / Price).
							$ld_payment_buttons = ebox_payment_buttons( $post );
							echo $ld_payment_buttons; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Outputs Button HTML

							/** This filter is documented in themes/ld30/templates/modules/infobar/course.php */
							if ( apply_filters( 'ebox_login_modal', true, $course_id, $user_id ) && ! is_user_logged_in() ) :
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
									// translators: placeholder: course.
									esc_html_x( 'This %s is currently closed', 'placeholder: course', 'ebox' ),
									esc_html( ebox_get_custom_label_lower( 'course' ) )
								)
									. '</span>';
								else :
									echo $button; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Outputs Button HTML
								endif;
							break;
					}

					/**
					 * Fires after the course infobar action cell.
					 *
					 * @since 3.0.0
					 *
					 * @param string|false $post_type Post type slug.
					 * @param int          $course_id Course ID.
					 * @param int          $user_id   User ID.
					 */
					do_action( 'ebox-course-infobar-action-cell-after', get_post_type(), $course_id, $user_id );
					?>
				</div>
			</div>
		</div> <!--/.ld-course-status-action-->

		<?php
		/**
		 * Fires inside the un-enrolled course infobox after the price
		 *
		 * @since 3.0.0
		 *
		 * @param string|false $post_type Post type slug.
		 * @param int          $course_id Course ID.
		 * @param int          $user_id   User ID.
		 */
		do_action( 'ebox-course-infobar-noaccess-price-after', get_post_type(), $course_id, $user_id );
		?>

	</div> <!--/.ld-course-status-->

<?php endif; ?>
