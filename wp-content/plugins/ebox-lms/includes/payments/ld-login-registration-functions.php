<?php
/**
 * Functions related to login/registration functions
 *
 * @since 3.6.0
 *
 * @package ebox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ebox LD30 Shows registration form for user registration
 *
 * @since 3.6.0
 *
 * @param array $attr Array of attributes for shortcode.
 */
function ebox_registration_output( $attr = array() ) {

	$attr_defaults = array(
		'width' => 0,
	);
	$attr          = shortcode_atts( $attr_defaults, $attr );

	$form_width = $attr['width'];

	if ( is_multisite() ) {
		$ebox_can_register = users_can_register_signup_filter();
	} else {
		$ebox_can_register = get_option( 'users_can_register' );
	}

	$ebox_errors_conditions = ebox_login_error_conditions();

	$active_template_key = ebox_Theme_Register::get_active_theme_key();

	?>

	<div class="<?php echo ( 'ld30' === $active_template_key ) ? esc_attr( ebox_the_wrapper_class() ) : 'ebox-wrapper'; ?>">

	<div id="ebox-registration-wrapper" <?php echo ( ! empty( $form_width ) ) ? 'style="width: ' . esc_attr( $form_width ) . ';"' : ''; ?>>

	<?php
	if ( isset( $_GET['ld-registered'] ) && 'true' === $_GET['ld-registered'] ) {
		ebox_get_template_part(
			'modules/alert.php',
			array(
				'type'    => 'success',
				'icon'    => 'alert',
				'message' => __( 'Registration successful.', 'ebox' ),
			),
			true
		);

		/**
		 * Fires after the register modal errors.
		 *
		 * @since 3.6.0
		 */
		do_action( 'ebox_registration_successful_after' );
	}

	if ( isset( $_GET['ld_register_id'] ) && '0' < $_GET['ld_register_id'] ) :
		$register_id = absint( $_GET['ld_register_id'] );

		$post_type = get_post_type( $register_id );

		if ( LDLMS_Post_Types::get_post_type_slug( 'course' ) === $post_type ) {
			$course_pricing = ebox_get_course_price( $register_id );
		} elseif ( ebox_get_post_type_slug( 'team' ) === $post_type ) {
			$course_pricing = ebox_get_team_price( $register_id );
		} else {
			esc_html_e( 'Invalid Course or Team', 'ebox' );
			return;
		}

		$course_pricing['price'] = ebox_get_price_as_float( $course_pricing['price'] );

		if ( ! empty( $course_pricing['trial_price'] ) ) {
			$course_pricing['trial_price'] = ebox_get_price_as_float( $course_pricing['trial_price'] );
		}

		$attached_coupon_dto = array();
		if ( is_user_logged_in() && ebox_post_has_attached_coupon( $register_id, get_current_user_id() ) ) {
			$attached_coupon_dto = ebox_get_attached_coupon_data( $register_id, get_current_user_id() );
		}
		?>

		<div class="order-overview">
			<p class="order-heading">
				<?php esc_html_e( 'Order Overview', 'ebox' ); ?>
			</p>

			<p class="purchase-title">
				<?php echo esc_html( get_the_title( $register_id ) ); ?>
			</p>

			<?php
			if (
				is_user_logged_in()
				&& (
					(
						ebox_is_course_post( $register_id )
						&& ebox_lms_has_access( $register_id, get_current_user_id() )
					)
					|| (
						ebox_is_team_post( $register_id )
						&& ebox_is_user_in_team( get_current_user_id(), $register_id )
					)
				)
			) {
				echo sprintf(
					// translators: placeholder: You already have access to Course/Team - Click here to visit.
					esc_html_x(
						'You already have access to %1$s - %2$s',
						'placeholder: You already have access to Course/Team - Click here to visit',
						'ebox'
					),
					esc_html( get_the_title( $register_id ) ),
					'<a href="' . esc_url( get_permalink( $register_id ) ) . '">' . esc_html__( 'Click here to visit', 'ebox' ) . '</a>'
				);
			} else {

				if ( 'paynow' === $course_pricing['type'] && is_user_logged_in() ) :
					?>
					<div id="coupon-alerts">
						<div class="coupon-alert coupon-alert-success" style="display: none">
							<?php
							ebox_get_template_part(
								'modules/alert.php',
								array(
									'type'    => 'success',
									'icon'    => 'alert',
									'message' => ' ',
								),
								true
							);
							?>
						</div>
						<div class="coupon-alert coupon-alert-warning" style="display: none">
							<?php
							ebox_get_template_part(
								'modules/alert.php',
								array(
									'type'    => 'warning',
									'icon'    => 'alert',
									'message' => ' ',
								),
								true
							);
							?>
						</div>
					</div>
				<?php endif; ?>

				<div class="purchase-rows">
					<?php if ( 'subscribe' === $course_pricing['type'] && ! empty( $course_pricing['trial_interval'] ) && ! empty( $course_pricing['trial_frequency'] ) ) : ?>
						<div class="purchase-row">
							<span class="purchase-label">
								<?php esc_html_e( 'Trial', 'ebox' ); ?>
							</span>

							<span class="purchase-field-price">
								<?php echo esc_html( ebox_get_price_formatted( $course_pricing['trial_price'] ? $course_pricing['trial_price'] : 0 ) ); ?>

								<?php echo esc_html__( ' for ', 'ebox' ) . absint( $course_pricing['trial_interval'] ) . ' ' . esc_html( $course_pricing['trial_frequency'] ); ?>
							</span>
						</div>
					<?php endif; ?>

					<div class="purchase-row" id="price-row">
						<span class="purchase-label">
							<?php esc_html_e( 'Price', 'ebox' ); ?>
						</span>

						<span class="purchase-value">
							<?php
							echo esc_html(
								( 'free' === $course_pricing['type'] || 'open' === $course_pricing['type'] )
									? __( 'Free', 'ebox' )
									: ebox_get_price_formatted( $course_pricing['price'] )
							);

							if ( ! empty( $course_pricing['interval'] ) ) {
								echo esc_html__( ' every ', 'ebox' ) . absint( $course_pricing['interval'] ) . ' ' . esc_html( $course_pricing['frequency'] );

								if ( ! empty( $course_pricing['repeats'] ) ) {
									echo esc_html__( ' for ', 'ebox' ) . absint( $course_pricing['interval'] ) * absint( $course_pricing['repeats'] ) . ' ' . esc_html( $course_pricing['repeat_frequency'] );
								}
							}
							?>
						</span>
					</div>
				</div>

				<?php if ( 'paynow' === $course_pricing['type'] && is_user_logged_in() ) : ?>
					<?php if ( ebox_active_coupons_exist() ) : ?>
						<form
							class="coupon-form"
							id="apply-coupon-form"
							data-nonce="<?php echo esc_attr( wp_create_nonce( 'ebox-coupon-nonce' ) ); ?>"
							data-post-id="<?php echo esc_attr( (string) $register_id ); ?>"
						>
							<input type="text" id="coupon-field" placeholder="<?php esc_html_e( 'Coupon', 'ebox' ); ?>" />
							<input type="submit" value="<?php esc_html_e( 'Apply Coupon', 'ebox' ); ?>" />
						</form>
					<?php endif; ?>

					<div class="totals" id="totals" style="display: <?php echo ! empty( $attached_coupon_dto ) ? 'block' : 'none'; ?>">
						<span class="order-heading">
							<?php esc_html_e( 'Totals', 'ebox' ); ?>
						</span>

						<div class="purchase-rows">
							<div class="purchase-row" id="subtotal-row">
								<span class="purchase-label">
									<?php esc_html_e( 'Subtotal', 'ebox' ); ?>
								</span>
								<span class="purchase-value">
									<?php echo esc_html( ebox_get_price_formatted( $course_pricing['price'] ) ); ?>
								</span>
							</div>

							<div
								class="purchase-row"
								id="coupon-row"
								style="<?php echo esc_attr( empty( $attached_coupon_dto ) ? 'display: none' : '' ); ?>"
							>
								<span class="purchase-label">
									<?php esc_html_e( 'Coupon: ', 'ebox' ); ?>
									<span>
										<?php
										if ( ! empty( $attached_coupon_dto ) ) {
											echo esc_html( $attached_coupon_dto->code );
										}
										?>
									</span>
								</span>
								<span class="purchase-value">
									<form
										id="remove-coupon-form"
										data-nonce="<?php echo esc_attr( wp_create_nonce( 'ebox-coupon-nonce' ) ); ?>"
										data-post-id="<?php echo esc_attr( (string) $register_id ); ?>"
									>
										<span>
											<?php
											if ( ! empty( $attached_coupon_dto ) ) {
												echo esc_html( ebox_get_price_formatted( $attached_coupon_dto->discount ) );
											}
											?>
										</span>
										<input type="submit" class="button-small" value="<?php esc_html_e( 'Remove', 'ebox' ); ?>" />
									</form>
								</span>
							</div>

							<?php
							/** This filter is documented in includes/payments/class-ebox-stripe-connect-checkout-integration.php */
							$total = apply_filters( 'ebox_get_price_by_coupon', floatval( $course_pricing['price'] ), $register_id, get_current_user_id() );
							?>

							<div class="purchase-row" id="total-row" data-total="<?php echo esc_attr( $total ); ?>">
								<span class="purchase-label">
									<?php esc_html_e( 'Total', 'ebox' ); ?>
								</span>
								<span class="purchase-value">
									<?php
									echo esc_html( ebox_get_price_formatted( $total ) );
									?>
								</span>
							</div>
						</div>
					</div>
				<?php endif; ?>

				<?php
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				if ( isset( $_GET['ld-registered'] ) || is_user_logged_in() ) {
					echo ebox_payment_buttons( $register_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}

				// translators: placeholder: Return to Course/Team.
				echo '<span class="order-overview-return">' . sprintf( esc_html_x( 'Return to %s', 'placeholder: Return to Course/Team.', 'ebox' ), '<a href="' . esc_html( get_permalink( absint( $_GET['ld_register_id'] ) ) ) . '">' . esc_html( get_the_title( absint( $_GET['ld_register_id'] ) ) ) . '</a></p>' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			}
			?>
		</div>
	<?php endif; ?>

	<?php
	$registration_page_id = (int) ebox_Settings_Section::get_section_setting(
		'ebox_Settings_Section_Registration_Pages',
		'registration'
	);

	if (
		isset( $_REQUEST['attributes']['preview_show'] ) && // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		'true' === sanitize_text_field( wp_unslash( $_REQUEST['attributes']['preview_show'] ) ) || // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		! is_user_logged_in()
	) {
		$register_id   = $_GET['ld_register_id'] ?? '';
		$registered_id = '?ld_register_id=' . $register_id;
		$checkout_page = get_permalink( $registration_page_id ) . ( ! empty( $register_id ) ? $registered_id : '' );

		/**
		 * Filters login link on registration form.
		 *
		 * @since 4.4.0
		 *
		 * @param string $registration_login_link_redirect The location to redirect the login link to
		 */
		$registration_login_link_redirect = apply_filters( 'ebox_registration_login_link_redirect', '' );

		/**
		 * Url to redirect to after logging in through registration form login form. Notice this runs through the wp_safe_redirect function ( https://developer.wordpress.org/reference/functions/wp_safe_redirect/ )
		 *
		 * @since 4.4.0
		 *
		 * @param string $registration_login_form_redirect The location the user is redirected to after logging into their account
		 */
		$registration_login_form_redirect = apply_filters( 'ebox_registration_login_form_redirect', '' );

		// translators: placeholder: Message above registration form if user logged out.
		echo '<p class="registration-login">' . sprintf( esc_html_x( 'Already have an account? %1$s', 'placeholder: Message above registration form if user logged out.', 'ebox' ), '<a class="registration-login-link" href="' . esc_attr( $registration_login_link_redirect ) . '">' . esc_html__( 'Log In', 'ebox' ) . '</a>' ) . '</p>';

		ebox_login_failed_alert();

		echo '<div class="registration-login-form" style="display: none;">' . wp_login_form(
			array(
				'echo'     => false,
				'redirect' => ! empty( $registration_login_form_redirect ) ? $registration_login_form_redirect : $checkout_page,
			)
		) . '</div>';

		if ( ebox_reset_password_is_enabled() ) {
			// translators: placeholder: Forgot password link below login form.
			echo '<p class="show-password-reset-link" style="display: none;">' . sprintf( esc_html_x( 'Forgot password? %s', 'placeholder: Forgot password link below login form.', 'ebox' ), '<a href="' . esc_attr( get_permalink( ebox_get_reset_password_page_id() ) ) . '">' . esc_html__( 'Click here to reset it.', 'ebox' ) . '</a>' ) . '</p>';
		}

		if ( $ebox_can_register ) :
			echo '<p class="show-register-form" style="display: none;"><a href="">' . esc_html__( 'Show registration form', 'ebox' ) . '</a></p>';

			if ( has_action( 'ebox_registration_form_override' ) ) {
				/**
				* Allow for replacement of the default ebox Registration form
				*
				* @since 3.6.0
				*/
				do_action( 'ebox_registration_form_override' );
			} else {
				/**
				* Fires before the registration form heading.
				*
				* @since 3.6.0
				*/
				do_action( 'ebox_registration_form_before' );
				if ( is_multisite() ) {
					$ebox_register_action_url = network_site_url( 'wp-signup.php' );
					$ebox_field_name_login    = 'user_name';
					$ebox_field_name_email    = 'user_email';
				} else {
					$ebox_register_action_url = site_url( 'wp-login.php?action=register', 'login_post' );
					$ebox_field_name_login    = 'user_login';
					$ebox_field_name_email    = 'user_email';
				}

				$ebox_errors = array(
					'has_errors' => false,
					'message'    => '',
				);

				foreach ( $ebox_errors_conditions as $ebox_param => $ebox_message ) {
					if ( isset( $_GET[ $ebox_param ] ) ) {
						$ebox_errors['has_errors'] = true;
						if ( ! empty( $ebox_errors['message'] ) ) {
							$ebox_errors['message'] .= '<br />';
						}
						$ebox_errors['message'] .= $ebox_message;
					}
				}

				if ( $ebox_errors['has_errors'] ) :
					ebox_get_template_part(
						'modules/alert.php',
						array(
							'type'    => 'warning',
							'icon'    => 'alert',
							'message' => $ebox_errors['message'],
						),
						true
					);

						/**
						 * Fires after the register modal errors.
						 *
						 * @since 3.6.0
						 *
						 * @param array $errors An array of error details.
						 */
						do_action( 'ebox_registration_errors_after', $ebox_errors );

				endif;
				?>
				<form name="ebox_registerform" id="ebox_registerform" class="ldregister" action="<?php echo esc_url( $ebox_register_action_url ); ?>" method="post">
				<?php
				/**
				 * Fires before the loop when displaying the registration form fields
				 *
				 * @since 3.6.0
				 */
				do_action( 'ebox_registration_form_fields_before' );

				$ebox_registration_fields = ebox_Settings_Section_Registration_Fields::get_section_settings_all();
				$ebox_fields_order        = $ebox_registration_fields['fields_order'];

				foreach ( $ebox_fields_order as $ebox_field ) {
					$ebox_required = ( 'yes' === $ebox_registration_fields[ $ebox_field . '_required' ] ) ? 'aria-required="true"' : '';
					if ( 'username' === $ebox_field ) {
						$ebox_name_field = $ebox_field_name_login;
					} elseif ( 'email' === $ebox_field ) {
						$ebox_name_field = $ebox_field_name_email;
					} else {
						$ebox_name_field = $ebox_field;
					}
					if ( 'yes' === $ebox_registration_fields[ $ebox_field . '_enabled' ] ) {
						echo '<p class="ebox-registration-field ebox-registration-field-' . esc_attr( $ebox_field ) . ' ' . ( ! empty( $ebox_required ) ? 'ebox-required' : '' ) . '"><label for="' . esc_attr( $ebox_field ) . '">' . esc_html( $ebox_registration_fields[ $ebox_field . '_label' ] ) . ( ! empty( $ebox_required ) ? ' <span class="ebox-required-field">*</span>' : '' ) . '</label>
						<input ' . esc_attr( $ebox_required ) . ' type="' . ( 'password' === $ebox_field ? 'password' : 'text' ) . '" id="' . esc_attr( $ebox_field ) . '" name="' . esc_attr( $ebox_name_field ) . '" value="' . ( isset( $_GET[ $ebox_name_field ] ) ? sanitize_text_field( $_GET[ $ebox_name_field ] ) : '' ) . '" /></p>';
						if ( 'password' === $ebox_field ) {
							echo '<p class="ebox-registration-field ebox-registration-field-confirm' . esc_attr( $ebox_field ) . ' ' . ( ! empty( $ebox_required ) ? 'ebox-required' : '' ) . '"><label for="confirm_password">' . esc_html__( 'Confirm Password', 'ebox' ) . ( ! empty( $ebox_required ) ? ' <span class="ebox-required-field">*</span>' : '' ) . '</label><input ' . esc_attr( $ebox_required ) . ' type="password" id="confirm_password" name="confirm_password" /></p>';
						}
					}
				}

				/**
				 * Fires after the loop when displaying the registration form fields
				 *
				 * @since 3.6.0
				 */
				do_action( 'ebox_registration_form_fields_after' );

				if ( isset( $_POST['ld_register_id'] ) && isset( $_GET['ld_register_id'] ) ) {
					$register_id = sanitize_text_field( $_POST['ld_register_id'] );
				} elseif ( isset( $_POST['ld_register_id'] ) ) {
					$register_id = sanitize_text_field( $_POST['ld_register_id'] );
				} elseif ( isset( $_GET['ld_register_id'] ) ) {
					$register_id = sanitize_text_field( $_GET['ld_register_id'] );
				} else {
					$register_id = 0;
				}

				$ebox_redirect_to_url = remove_query_arg(
					array_keys( $ebox_errors_conditions ), // @phpstan-ignore-line -- It's string[].
					get_permalink()
				);

				if ( ! is_multisite() ) {
					$ebox_redirect_to_url = add_query_arg(
						array(
							'ld-registered'  => 'true',
							'ld_register_id' => $register_id,
						),
						$ebox_redirect_to_url
					);
				}

				if ( is_multisite() ) {
					signup_nonce_fields();
					?>
					<input type="hidden" name="signup_for" value="user" />
					<input type="hidden" name="stage" value="validate-user-signup" />
					<input type="hidden" name="blog_id" value="<?php echo get_current_blog_id(); ?>" />
					<?php
					/** This filter is documented in https://developer.wordpress.org/reference/hooks/signup_extra_fields/ */
					do_action( 'signup_extra_fields', '' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress core hook.
				} else {
					/** This filter is documented in https://developer.wordpress.org/reference/hooks/register_form/ */
					do_action( 'register_form' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress core hook.
				}

				/**
				 * Fires inside the registration form.
				 *
				 * @since 3.6.0
				 */
				do_action( 'ebox_registration_form' );
				?>
				<input name="ld_register_id" value="<?php echo absint( $register_id ); ?>" type="hidden" />
				<input type="hidden" name="ebox-registration-form" value="<?php echo esc_attr( wp_create_nonce( 'ebox-registration-form' ) ); ?>" />
				<input type="hidden" name="redirect_to" value="<?php echo esc_url( $ebox_redirect_to_url ); ?>" />
				<p><input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="<?php esc_attr_e( 'Register', 'ebox' ); ?>" /></p>
			</form>
				<?php
				/**
				 * Fires after the registration form heading.
				 *
				 * @since 3.6.0
				 */
				do_action( 'ebox_registration_form_after' );
			}
		endif;
	} else {
		if ( ! isset( $_GET['ld-registered'] ) && ! isset( $_GET['ld_register_id'] ) ) {
			$current_user = wp_get_current_user();
			echo sprintf(
			// translators: placeholders: Current Logged In Username, WP Logout Link.
				esc_html_x( 'Hello %1$s, looks like you\'re already logged in. Want to sign in as a different user? %2$s', 'placeholder: Current Logged In Username, WP Logout Link.', 'ebox' ),
				esc_html( $current_user->user_login ),
				'<a href="' . esc_url( wp_logout_url( get_permalink( $registration_page_id ) ) ) . '">' . esc_html__( 'Log Out', 'ebox' ) . '</a>'
			);
		}
	}

	echo '</div></div>';

	ebox_registerform_password_strength_data();
}

/**
 * Retrieves the LD login URL if using the LD30 template and the LD Login & Registration feature. If not using the Login & Registration feature, uses the wp_login_url() function redirecting back to current page
 *
 * @since 3.6.0
 *
 * @return string The login URL
 */
function ebox_get_login_url() {
	$active_template_key = ebox_Theme_Register::get_active_theme_key();
	$login_mode_enabled  = ebox_Settings_Section::get_section_setting( 'ebox_Settings_Theme_LD30', 'login_mode_enabled' );
	$ebox_login_url = '';
	if ( ( 'ld30' === $active_template_key ) && ( 'yes' === $login_mode_enabled ) ) {
		ebox_load_login_modal_html();
		$ebox_login_url = '#login';
	} else {
		$ebox_login_url = wp_login_url( get_permalink( get_the_ID() ) );
	}

	return $ebox_login_url;
}

/**
 * Checks whether the New User Registration email is enabled or not
 *
 * @since 3.6.0
 *
 * @return boolean True if option is enabled
 */
function ebox_new_user_email_enabled() {
	$enabled = ebox_Settings_Section_Emails_New_User_Registration::get_section_settings_all();
	if ( 'on' === $enabled['enabled'] ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Grabs email subject/message for the new user register email
 *
 * @since 3.6.0
 *
 * @param array  $wp_new_user_notification_email Email content for new user registration.
 * @param object $user WP_User Object.
 * @param string $blogname Title of the current site.
 *
 * @return array Array of email data to be sent
 */
function ebox_emails_content_new_user( $wp_new_user_notification_email = '', $user = '', $blogname = '' ) {
	$email_setting = ebox_Settings_Section_Emails_New_User_Registration::get_section_settings_all();
	if ( 'on' === $email_setting['enabled'] ) {

		$placeholders = array(
			'{user_login}'   => $user->user_login,
			'{first_name}'   => $user->user_firstname,
			'{last_name}'    => $user->user_lastname,
			'{display_name}' => $user->display_name,
			'{user_email}'   => $user->user_email,

			'{post_title}'   => isset( $_REQUEST['ld_register_id'] ) ? get_the_title( absint( $_REQUEST['ld_register_id'] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Recommended.
			'{post_url}'     => isset( $_REQUEST['ld_register_id'] ) ? get_permalink( absint( $_REQUEST['ld_register_id'] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Recommended.

			'{site_title}'   => $blogname,
			'{site_url}'     => wp_parse_url( home_url(), PHP_URL_HOST ),
		);
		/**
		 * Filters new registration email placeholders.
		 *
		 * @param array $placeholders Array of email placeholders and values.
		 * @param int   $user_id      User ID.
		 */
		$placeholders = apply_filters( 'ebox_registration_email_placeholders', $placeholders, $user->ID );

		/**
		 * Filters registration email subject.
		 *
		 * @param string $email_subject Email subject text.
		 * @param int    $user_id       User ID.
		 */
		$email_setting['subject'] = apply_filters( 'ebox_registration_email_subject', $email_setting['subject'], $user->ID );
		if ( ! empty( $email_setting['subject'] ) ) {
			$wp_new_user_notification_email['subject'] = ebox_emails_parse_placeholders( $email_setting['subject'], $placeholders );
		}

		/**
		 * Filters registration email message.
		 *
		 * @param string $email_message Email message text.
		 * @param int    $user_id       User ID.
		 */
		$email_setting['message'] = apply_filters( 'ebox_registration_email_message', $email_setting['message'], $user->ID );
		if ( ! empty( $email_setting['message'] ) ) {
			$email_setting['message'] = ebox_emails_parse_placeholders( $email_setting['message'], $placeholders );
			if ( 'text/html' === $email_setting['content_type'] ) {
				$email_setting['message'] = wpautop( stripcslashes( $email_setting['message'] ) );
			} else {
				$email_setting['message'] = esc_html( wp_strip_all_tags( wptexturize( $email_setting['message'] ) ) );
			}
			$wp_new_user_notification_email['message'] = $email_setting['message'];
		}

		if ( 'text/html' === $email_setting['content_type'] ) {
			$wp_new_user_notification_email['headers'] = 'Content-Type: ' . $email_setting['content_type'] . ' charset=' . get_option( 'blog_charset' );

			add_filter(
				'wp_mail_content_type',
				function() {
					return 'text/html';
				}
			);
		}
	}
	return $wp_new_user_notification_email;
}

/**
 * Validates that the password and confirm password fields match in the registration form
 *
 * @since 3.6.0
 *
 * @param WP_Error $errors A WP_Error object containing any errors encountered during registration.
 *
 * @return WP_Error
 */
function ebox_registration_form_validate( WP_Error $errors ) {
	if ( isset( $_POST['ld_register_id'] ) ) {
		if ( ( isset( $_POST['ebox-registration-form'] ) ) && ( wp_verify_nonce( $_POST['ebox-registration-form'], 'ebox-registration-form' ) ) ) {
			$ebox_registration_fields = ebox_Settings_Section_Registration_Fields::get_section_settings_all();

			$first_name = '';
			if ( isset( $_POST['first_name'] ) ) {
				$first_name = sanitize_text_field( $_POST['first_name'] );
			}
			if ( 'yes' === $ebox_registration_fields['first_name_enabled'] && 'yes' === $ebox_registration_fields['first_name_required'] && empty( $first_name ) ) {
				$errors->add( 'required_first_name', __( 'Registration requires a first name.', 'ebox' ) );
			}

			$last_name = '';
			if ( isset( $_POST['last_name'] ) ) {
				$last_name = sanitize_text_field( $_POST['last_name'] );
			}
			if ( 'yes' === $ebox_registration_fields['last_name_enabled'] && 'yes' === $ebox_registration_fields['last_name_required'] && empty( $last_name ) ) {
				$errors->add( 'required_last_name', __( 'Registration requires a last name.', 'ebox' ) );
			}

			$password           = '';
			$confirmed_password = '';
			if ( isset( $_POST['password'] ) ) {
				$password = sanitize_text_field( $_POST['password'] );
			}
			if ( 'yes' === $ebox_registration_fields['password_required'] && empty( $password ) ) {
				$errors->add( 'empty_password', __( 'Registration requires a password.', 'ebox' ) );
			}
			if ( isset( $_POST['confirm_password'] ) ) {
				$confirmed_password = sanitize_text_field( $_POST['confirm_password'] );
			}

			if ( $password !== $confirmed_password ) {
				$errors->add( 'confirm_password', __( 'Passwords do not match.', 'ebox' ) );
			}
		}
	}

	return $errors;
}
/** This filter is documented in https://developer.wordpress.org/reference/hooks/registration_errors/ */
add_filter( 'registration_errors', 'ebox_registration_form_validate' );

/**
 * Utility function to check the registration form course_id.
 *
 * @since 3.1.2
 *
 * @return int|false $course_id Valid course_id if valid otherwise false.
 */
function ebox_validation_registration_form_redirect_to() {
	if ( ( isset( $_POST['ebox-registration-form'] ) ) && ( wp_verify_nonce( $_POST['ebox-registration-form'], 'ebox-registration-form' ) ) || ( isset( $_POST['ebox-login-form'] ) ) && ( wp_verify_nonce( $_POST['ebox-login-form'], 'ebox-login-form' ) ) ) {
		if ( ( isset( $_POST['redirect_to'] ) ) && ( ! empty( $_POST['redirect_to'] ) ) ) {
			return esc_url_raw( $_POST['redirect_to'] );
		}
	}
	return false;
}

/**
 * Handles user registration failure.
 *
 * Fires on `register_post` hook.
 * From this function we capture the failed registration errors and send the user
 * back to the registration form part of the LD login modal.
 *
 * @since 3.1.1.1
 *
 * @param string $sanitized_user_login User entered login (sanitized).
 * @param string $user_email           User entered email.
 * @param array  $errors               Array of registration errors.
 */
function ebox_user_register_error( $sanitized_user_login, $user_email, $errors ) {

	$redirect_url = ebox_validation_registration_form_redirect_to();
	if ( $redirect_url ) {
		$redirect_url = remove_query_arg( 'ld-registered', $redirect_url );

		/** This filter is documented in https://developer.wordpress.org/reference/hooks/registration_errors/ */
		$errors = apply_filters( 'registration_errors', $errors, $sanitized_user_login, $user_email ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- It's a WP core filter.

		// This if check is copied from register_new_user function of wp-login.php.
		if ( ( $errors->has_errors() ) && ( $errors->get_error_code() ) ) {
			$has_errors = true;

			$ebox_registration_fields = ebox_Settings_Section_Registration_Fields::get_section_settings_all();
			$ebox_fields_order        = $ebox_registration_fields['fields_order'];

			if ( is_multisite() ) {
				$ebox_register_action_url        = network_site_url( 'wp-signup.php' );
				$ebox_ebox_field_name_login = 'user_name';
				$ebox_field_name_email           = 'user_email';
			} else {
				$ebox_register_action_url = site_url( 'wp-login.php?action=register', 'login_post' );
				$ebox_field_name_login    = 'user_login';
				$ebox_field_name_email    = 'user_email';
			}

			$field_array = array();
			foreach ( $ebox_fields_order as $ebox_field ) {
				if ( 'username' === $ebox_field ) {
					$ebox_name_field = $ebox_field_name_login;
				} elseif ( 'email' === $ebox_field ) {
					$ebox_name_field = $ebox_field_name_email;
				} else {
					$ebox_name_field = $ebox_field;
				}
				if ( 'yes' === $ebox_registration_fields[ $ebox_field . '_enabled' ] && 'password' !== $ebox_field ) {
					$ebox_field                      = sanitize_text_field( $_POST[ $ebox_name_field ] );
					$field_array[ $ebox_name_field ] = $ebox_field;
				}
			}

			$redirect_url = add_query_arg( $field_array, $redirect_url );

			// add error codes to custom redirection URL one by one.
			foreach ( $errors->errors as $e => $m ) {
				$redirect_url = add_query_arg( $e, '1', $redirect_url );
			}

			$login_mode_enabled      = ebox_Settings_Section::get_section_setting( 'ebox_Settings_Theme_LD30', 'login_mode_enabled' );
			$ld_registration_page_id = ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_Registration_Pages', 'registration' );

			// If we are NOT using our registration form...
			if ( ! isset( $_POST['ld_register_id'] ) ) {
				if ( 'yes' === $login_mode_enabled ) {
					// We add the '#login' hash.
					$redirect_url = ebox_add_login_hash( $redirect_url );
				}
			}

			/**
			 * Filters URL that a user should be redirected when there is an error while registration.
			 *
			 * @since 3.1.1.1
			 *
			 * @param string  $redirect_url The URL to be redirected when there are errors.
			 */
			$redirect_url = apply_filters( 'ebox_registration_error_url', $redirect_url );
			if ( ! empty( $redirect_url ) ) {
				// add finally, redirect to your custom page with all errors in attributes.
				ebox_safe_redirect( $redirect_url );
			}
		} else {
			if ( isset( $_POST['ld_register_id'] ) ) {
				if ( empty( $_POST['ld_register_id'] ) ) {
					// We set the 'redirect_to' only if there are not errors in the registration data.
					$ld_registration_success_id  = ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_Registration_Pages', 'registration_success' );
					$ld_registration_success_id  = absint( $ld_registration_success_id );
					$ld_registration_success_url = get_permalink( $ld_registration_success_id );
					if ( ! empty( $ld_registration_success_url ) ) {
						$_POST['redirect_to'] = $ld_registration_success_url;
					}
				}
			}
		}
	}
}
add_action( 'register_post', 'ebox_user_register_error', 99, 3 );

/**
 * Updates user course data on user login.
 *
 * Fires on `authenticate` hook.
 *
 * @since 3.0.7
 *
 * @param WP_User $user     WP_User object if success. wp_error is error.
 * @param string  $username Login form entered user login.
 * @param string  $password Login form entered user password.
 *
 * @return WP_User|WP_Error Returns WP_User if a valid user object is passed.
 */
function ebox_authenticate( $user, $username, $password ) {
	if ( ( $user ) && ( is_a( $user, 'WP_User' ) ) ) {
		/**
		 * If the user started from a Course and registered then once they
		 * go through the password setup they will login. The login form
		 * could be the default WP login, the LD course modal or some other
		 * plugin. During the registration if the captured course ID is saved
		 * in the user meta we enroll that user into that course.
		 */
		$registered_post_id = get_user_meta( $user->ID, '_ld_registered_post', true );
		if ( '' !== $registered_post_id ) {
			delete_user_meta( $user->ID, '_ld_registered_post' );
		}
		$registered_post_id = absint( $registered_post_id );
		if ( ! empty( $registered_post_id ) ) {
			if ( in_array( get_post_type( $registered_post_id ), array( ebox_get_post_type_slug( 'course' ) ), true ) ) {
				ld_update_course_access( $user->ID, $registered_post_id );
			} elseif ( in_array( get_post_type( $registered_post_id ), array( ebox_get_post_type_slug( 'team' ) ), true ) ) {
				ld_update_team_access( $user->ID, $registered_post_id );
			}
		}

		/**
		 * If the user login is coming from a LD course then we enroll the
		 * user into the course. This helps save a step for the user.
		 */
		$login_post_id = ebox_validation_login_form_course();
		$login_post_id = absint( $login_post_id );
		if ( ! empty( $login_post_id ) ) {
			if ( in_array( get_post_type( $login_post_id ), array( ebox_get_post_type_slug( 'course' ) ), true ) ) {
				ld_update_course_access( $user->ID, $login_post_id );
			} elseif ( in_array( get_post_type( $login_post_id ), array( ebox_get_post_type_slug( 'team' ) ), true ) ) {
				ld_update_team_access( $user->ID, $login_post_id );
			}
		}
	} elseif ( ( is_wp_error( $user ) ) && ( $user->has_errors() ) ) {
		/**
		 * This is here instead of ebox_login_failed() because WP
		 * handles 'empty_username', 'empty_password' conditions different
		 * then invalid values.
		 *
		 * See logic in wp_authenticate()
		 */
		$redirect_to = ebox_validation_registration_form_redirect_to();
		if ( $redirect_to ) {
			$ignore_codes = array( 'empty_username', 'empty_password' );

			if ( is_wp_error( $user ) && in_array( $user->get_error_code(), $ignore_codes, true ) ) {
				$redirect_to = add_query_arg( 'login', 'failed', $redirect_to );
				$redirect_to = ebox_add_login_hash( $redirect_to );
				ebox_safe_redirect( $redirect_to );
			}
		}
	}

	return $user;
}
add_filter( 'authenticate', 'ebox_authenticate', 99, 3 );

/**
 * Handles the login fail scenario from WP.
 *
 * Fires on `wp_login_failed` hook.
 * Note for 'empty_username', 'empty_password' error conditions this action
 * will not be called. Those conditions are handled in ebox_authenticate()
 * if the user logged in via the LD modal.
 *
 * @since 3.0.0
 *
 * @param string $username Login name from login form process. Not used.
 */
function ebox_login_failed( $username = '' ) {
	$redirect_to = ebox_validation_registration_form_redirect_to();
	if ( $redirect_to ) {
		$redirect_to = add_query_arg( 'login', 'failed', $redirect_to );
		$redirect_to = ebox_add_login_hash( $redirect_to );
		ebox_safe_redirect( $redirect_to );
	}
}
add_action( 'wp_login_failed', 'ebox_login_failed', 1, 1 );

/**
 * Gets the login form course ID.
 *
 * @since 3.1.2
 *
 * @return int|false $course_id Valid course_id if valid otherwise false.
 */
function ebox_validation_login_form_course() {
	if ( ( isset( $_POST['ebox-login-form'] ) ) && ( wp_verify_nonce( $_POST['ebox-login-form'], 'ebox-login-form' ) ) ) {
		if ( ( isset( $_POST['ebox-login-form-post'] ) ) && ( ! empty( $_POST['ebox-login-form-post'] ) ) ) {
			$post_id = absint( $_POST['ebox-login-form-post'] );
			if ( ( isset( $_POST['ebox-login-form-post-nonce'] ) ) && ( wp_verify_nonce( $_POST['ebox-login-form-post-nonce'], 'ebox-login-form-post-' . $post_id . '-nonce' ) ) ) {

				if ( in_array( get_post_type( $post_id ), array( ebox_get_post_type_slug( 'course' ) ), true ) ) {
					/** This filter is documented in themes/ld30/includes/login-register-functions.php */
					if ( ( ! empty( $post_id ) ) && ( apply_filters( 'ebox_login_form_include_course', true, $post_id ) ) ) {
						return absint( $post_id );
					}
				} elseif ( in_array( get_post_type( $post_id ), array( ebox_get_post_type_slug( 'team' ) ), true ) ) {
					/** This filter is documented in themes/ld30/includes/login-register-functions.php */
					if ( ( ! empty( $post_id ) ) && ( apply_filters( 'ebox_login_form_include_team', true, $post_id ) ) ) {
						return absint( $post_id );
					}
				}
			}
		}
	}
	return false;
}

/**
 * Handles user registration success.
 *
 * Fires on `user_register` hook.
 * When the user registers it if was from a Course we capture that for later
 * when the user goes through the password set logic. After the password set
 * we can redirect the user to the course. See ebox_password_reset()
 * function.
 *
 * @since 3.1.2
 *
 * @param integer $user_id The Registers user ID.
 */
function ebox_register_user_success( $user_id = 0 ) {
	if ( ! empty( $user_id ) ) {
		if ( ebox_new_user_email_enabled() ) {
			add_filter( 'wp_new_user_notification_email', 'ebox_emails_content_new_user', 30, 3 );
			add_filter( 'wp_mail_from', 'ebox_emails_from_email' );
			add_filter( 'wp_mail_from_name', 'ebox_emails_from_name' );
		}
		$post_id = ebox_validation_registration_form_course();
		if ( isset( $_POST['ld_register_id'] ) ) {
			if ( isset( $_POST['first_name'] ) ) {
				$first_name = sanitize_text_field( $_POST['first_name'] );
				if ( ! empty( $first_name ) ) {
					update_user_meta( $user_id, 'first_name', $first_name );
				}
			}
			if ( isset( $_POST['last_name'] ) ) {
				$last_name = sanitize_text_field( $_POST['last_name'] );
				if ( ! empty( $last_name ) ) {
					update_user_meta( $user_id, 'last_name', $last_name );
				}
			}
			if ( isset( $_POST['password'] ) ) {
				$password           = sanitize_text_field( $_POST['password'] );
				$confirmed_password = sanitize_text_field( $_POST['confirm_password'] );
				if ( ! empty( $password ) && ! empty( $confirmed_password ) ) {
					wp_set_password( $password, $user_id );
				}
			}
			update_user_meta( $user_id, 'ld_register_form', time() );
		}
		if ( ! empty( $post_id ) ) {
			add_user_meta( $user_id, '_ld_registered_post', absint( $post_id ) );
		}

		if ( ( isset( $_POST['ebox-registration-form'] ) ) && ( wp_verify_nonce( $_POST['ebox-registration-form'], 'ebox-registration-form' ) ) && isset( $password ) ) {
			wp_set_current_user( $user_id );
			wp_set_auth_cookie( $user_id );
		}
	}
}
add_action( 'user_register', 'ebox_register_user_success', 10, 1 );

/**
 * Utility function to check and return the registration form course_id.
 *
 * @since 3.1.2
 *
 * @return int|false $course_id Valid course_id if valid otherwise false.
 */
function ebox_validation_registration_form_course() {
	if ( ( isset( $_POST['ebox-registration-form'] ) ) && ( wp_verify_nonce( $_POST['ebox-registration-form'], 'ebox-registration-form' ) ) ) {
		if ( ( isset( $_POST['ebox-registration-form-post'] ) ) && ( ! empty( $_POST['ebox-registration-form-post'] ) ) ) {
			$post_id = absint( $_POST['ebox-registration-form-post'] );
			if ( ! empty( $post_id ) ) {
				if ( ! in_array( get_post_type( $post_id ), array( ebox_get_post_type_slug( 'course' ) ), true ) ) {
					/**
					 * Filters whether to allow user registration from the course.
					 *
					 * @since 3.1.0
					 *
					 * @param boolean $include_course whether to allow user registration from the course.
					 * @param int     $post_id      Course ID.
					 */
					if ( ( ! empty( $post_id ) ) && ( apply_filters( 'ebox_registration_form_include_course', true, $post_id ) ) ) {
						if ( ( isset( $_POST['ebox-registration-form-post-nonce'] ) ) && ( wp_verify_nonce( $_POST['ebox-registration-form-post-nonce'], 'ebox-registration-form-post-' . $post_id . '-nonce' ) ) ) {
							return absint( $post_id );
						}
					}
				} elseif ( ! in_array( get_post_type( $post_id ), array( ebox_get_post_type_slug( 'team' ) ), true ) ) {
					/**
					 * Filters whether to allow user registration from the team.
					 *
					 * @since 3.2.0
					 *
					 * @param boolean $include_team whether to allow user registration from the team.
					 * @param int     $post_id      Course ID.
					 */
					if ( ( ! empty( $post_id ) ) && ( apply_filters( 'ebox_registration_form_include_team', true, $post_id ) ) ) {
						if ( ( isset( $_POST['ebox-registration-form-post-nonce'] ) ) && ( wp_verify_nonce( $_POST['ebox-registration-form-post-nonce'], 'ebox-registration-form-post-' . $post_id . '-nonce' ) ) ) {
							return absint( $post_id );
						}
					}
				}
			}
		}
	}
	return false;
}

/**
 * PASSWORD RESET FUNCTIONS
 */

/**
 * Variable to capture the user from the reset password. This var
 * is used in the ebox_password_reset_login_url() function to
 * redirect the user back to the origin.
 */
global $ld_password_reset_user;
$ld_password_reset_user = ''; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound

/**
 * Handles password reset logic.
 *
 * Called after the user updates new password.
 *
 * @since 3.1.2
 *
 * @global WP_User $ld_password_reset_user Global password reset user.
 *
 * @param WP_User $user     WP_User object.
 * @param string  $new_pass New Password.
 */
function ebox_password_reset( $user, $new_pass ) {
	if ( $user ) {
		global $ld_password_reset_user;
		$ld_password_reset_user = $user; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound

		add_filter( 'login_url', 'ebox_password_reset_login_url', 30, 3 );
	}
}
add_action( 'password_reset', 'ebox_password_reset', 30, 2 );

/**
 * Handles password reset logic.
 *
 * Fires on `login_url` hook.
 *
 * @since 3.1.2
 *
 * @global WP_User $ld_password_reset_user Global password reset user.
 *
 * @param string         $login_url    Current login_url.
 * @param string         $redirect     Query string redirect_to parameter and value.
 * @param boolean|string $force_reauth Whether to force re-authentication.
 *
 * @return string Returns login URL.
 */
function ebox_password_reset_login_url( $login_url = '', $redirect = '', $force_reauth = '' ) {
	global $ld_password_reset_user;

	if ( ( isset( $_GET['action'] ) ) && ( 'resetpass' === $_GET['action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- No nonces on public facing login forms
		if ( ( ! empty( $login_url ) ) && ( empty( $redirect ) ) ) {
			$user = $ld_password_reset_user;
			if ( ( $user ) && ( is_a( $user, 'WP_User' ) ) ) {
				$ld_login_url = get_user_meta( $user->ID, '_ld_lostpassword_redirect_to', true );
				delete_user_meta( $user->ID, '_ld_lostpassword_redirect_to' );
				if ( ! empty( $ld_login_url ) ) {
					$login_url = esc_url( $ld_login_url );
				} else {
					$registered_post_id = get_user_meta( $user->ID, '_ld_registered_post', true );

					if ( ! empty( $registered_post_id ) ) {
						$registered_post_url = get_permalink( $registered_post_id );
						$registered_post_url = ebox_add_login_hash( $registered_post_url );
						$login_url           = esc_url( $registered_post_url );
					}
				}
			}
		}
	}

	return $login_url;
}
/**
 * Stores the password reset redirect_to URL.
 *
 * Fires on `login_form_lostpassword` hook.
 *
 * When the user clicks the password reset on the LD login popup we capture the
 * 'redirect_to' URL. This is done at step 2 of the password reset process after
 * the user has enter their username/email.
 *
 * The user will then receive an email from WP with a link to reset the
 * password. Once the user has created a new password they will be shown a
 * login link. That login URL will be the stored 'redirect_to' user meta value.
 * See the function ebox_password_reset_login_url() for that stage of the
 * processing.
 *
 * @since 3.1.1.1
 */
function ebox_login_form_lostpassword() {
	if ( isset( $_POST['ebox-registration-form'], $_REQUEST['redirect_to'] ) &&
		wp_verify_nonce( $_POST['ebox-registration-form'], 'ebox-registration-form' ) &&
		! empty( $_REQUEST['redirect_to'] ) ) {
		$redirect_to = esc_url( $_REQUEST['redirect_to'] );

		// Only if the 'redirect_to' link contains our parameter.
		if ( false !== strpos( $redirect_to, 'ld-resetpw=true' ) ) { // cspell:disable-line.
			if ( isset( $_POST['user_login'] ) && is_string( $_POST['user_login'] ) ) {
				$user_login = wp_unslash( $_POST['user_login'] );
				$user       = get_user_by( 'login', $user_login );
				if ( ( $user ) && ( is_a( $user, 'WP_User' ) ) ) {
					/**
					 * We remove the 'ld-resetpw' part because we don't want to trigger // cspell:disable-line.
					 * the login modal showing the password has been reset again.
					 */
					$redirect_to = remove_query_arg( 'ld-resetpw', $redirect_to ); // cspell:disable-line.

					/**
					 * Store the redirect URL in user meta. This will be retrieved in
					 * the function ebox_password_reset_login_url().
					 */
					update_user_meta( $user->ID, '_ld_lostpassword_redirect_to', $redirect_to );
				}
			}
		}
	}
}
add_action( 'login_form_lostpassword', 'ebox_login_form_lostpassword', 30 );


/**
 * Adds '#login' to the end of a the login URL.
 *
 * Used throughout the LD30 login model and processing functions.
 *
 * @since 3.1.2
 *
 * @param string $url URL to check and append hash.
 *
 * @return string Returns URL after adding login hash.
 */
function ebox_add_login_hash( $url = '' ) {
	if ( strpos( $url, '#login' ) === false ) {
		$url .= '#login';
	}

	return $url;
}

/**
 * Gets an array of login error conditions.
 *
 * @since 3.1.2
 *
 * @param bool $return_keys True to return keys of conditions only. Default false.
 *
 * @return array<string|int,string> Returns an array of login error conditions.
 */
function ebox_login_error_conditions( bool $return_keys = false ): array {
	$registration_errors = array(
		'username_exists'     => __( 'Registration username exists.', 'ebox' ),
		'email_exists'        => __( 'Registration email exists.', 'ebox' ),
		'empty_username'      => __( 'Registration requires a username.', 'ebox' ),
		'empty_email'         => __( 'Registration requires a valid email.', 'ebox' ),
		'invalid_username'    => __( 'Invalid username.', 'ebox' ),
		'invalid_email'       => __( 'Invalid email.', 'ebox' ),
		'empty_password'      => __( 'Registration requires a password.', 'ebox' ),
		'confirm_password'    => __( 'Passwords do not match.', 'ebox' ),
		'required_first_name' => __( 'Registration requires a first name.', 'ebox' ),
		'required_last_name'  => __( 'Registration requires a last name', 'ebox' ),
	);

	/**
	 * Filters a list of user registration errors.
	 *
	 * @since 3.0.0
	 * @deprecated 4.5.0
	 *
	 * @param array<string,string> $registration_errors An array of registration errors and descriptions.
	 */
	$registration_errors = apply_filters_deprecated(
		'ebox-registration-errors',
		array( $registration_errors ),
		'4.5.0',
		'ebox_registration_errors'
	);

	/**
	 * Filters a list of user registration errors.
	 *
	 * @since 4.5.0
	 *
	 * @param array<string,string> $registration_errors An array of registration errors and descriptions.
	 */
	$registration_errors = apply_filters( 'ebox_registration_errors', $registration_errors );

	if ( true === $return_keys ) {
		return array_keys( $registration_errors );
	}

	return $registration_errors;
}

/**
 * Defines data for the password strength meter on registration form
 *
 * @since 3.6.1
 */
function ebox_registerform_password_strength_data() {
	wp_enqueue_script(
		'ebox-password-strength-meter',
		ebox_LMS_PLUGIN_URL . 'assets/js/ebox-password-strength-meter.js',
		array( 'jquery', 'password-strength-meter' ),
		ebox_VERSION,
		true
	);

	$params = array();

	/**
	 * Filters the minimum password strength for the registration form
	 *
	 * @since 3.6.1
	 *
	 * @param int $min_password_strength Minimum password strength value
	 */
	$params['min_password_strength'] = apply_filters( 'ebox_min_password_strength', 3 );

	/**
	 * Additional text to show user defining password strength
	 *
	 * @since 3.6.1
	 *
	 * @param string $password_strength_hint Text that displays next to password strength rating.
	 */
	$params['i18n_password_error'] = esc_attr__( 'Please enter a stronger password.', 'ebox' );

	/**
	 * Additional text displayed below the password strength rating section to explain further
	 *
	 * @since 3.6.1
	 *
	 * @param string Message to display to user with additional information to help choose a better password
	 */
	$params['i18n_password_hint'] = esc_attr__( 'Hint: The password should be at least twelve characters long. To make it stronger, use upper and lower case letters, numbers, and symbols like ! " ? $ % ^ &amp; ).', 'ebox' );

	/**
	 * Controls disabling registration form submission
	 *
	 * @since 3.6.1
	 *
	 * @param bool $prevent_registration Whether to prevent the registration form submission with a weak password strength. Default true.
	 */
	$params['stop_register'] = apply_filters( 'ebox_weak_password_stop_register', true );

	wp_localize_script( 'ebox-password-strength-meter', 'ebox_password_strength_meter_params', $params );
}

/**
 * Returns true if the password reset page is enabled.
 *
 * @since 4.4.0
 *
 * @return bool
 */
function ebox_reset_password_is_enabled(): bool {
	$reset_password_page_id = (int) ebox_Settings_Section::get_section_setting(
		'ebox_Settings_Section_Registration_Pages',
		'reset_password'
	);

	return $reset_password_page_id > 0;
}

/**
 * Returns the reset password page ID or 0 if not set.
 *
 * @since 4.4.0
 *
 * @return int
 */
function ebox_get_reset_password_page_id(): int {
	if ( ! ebox_reset_password_is_enabled() ) {
		return 0;
	}

	return (int) ebox_Settings_Section::get_section_setting(
		'ebox_Settings_Section_Registration_Pages',
		'reset_password'
	);
}

/**
 * ebox LD30 Shows reset password form
 *
 * @since 4.4.0
 *
 * @param array $attr Array of attributes for shortcode.
 *
 * @return void
 */
function ebox_reset_password_output( $attr = array() ): void {
	$attr_defaults       = array( 'width' => 0 );
	$attr                = shortcode_atts( $attr_defaults, $attr );
	$form_width          = $attr['width'];
	$active_template_key = ebox_Theme_Register::get_active_theme_key();
	?>
	<div class="<?php echo ( 'ld30' === $active_template_key ) ? esc_attr( ebox_the_wrapper_class() ) : 'ebox-wrapper'; ?>">
	<?php
	if ( isset( $_GET['action'] ) && 'rp' === $_GET['action'] ) {
		$key        = ( isset( $_GET['key'] ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : '' );
		$user       = ( isset( $_GET['login'] ) ? get_user_by( 'login', sanitize_text_field( wp_unslash( $_GET['login'] ) ) ) : '' );
		$key_verify = ebox_reset_password_verification( $user, $key );
		if ( 'WP_Error' === get_class( $key_verify ) ) {
			$status['message'] = esc_html__( 'Invalid key, please check your reset password link and try again.', 'ebox' );
			$status['type']    = 'warning';
			$status['action']  = 'prevent_reset';
		}
	}
	if ( isset( $_POST['user_login'] ) ) {
		$status = ebox_reset_password_email_send();
	}
	if ( isset( $_POST['user_login'] ) && isset( $_POST['reset_password'] ) ) {
		$new_password = sanitize_text_field( wp_unslash( $_POST['reset_password'] ) );
		$user         = get_user_by( 'login', sanitize_text_field( wp_unslash( $_POST['user_login'] ) ) );
		ebox_reset_password_set_user_new_password( $user, $new_password );
	}
	if ( isset( $_GET['password_reset'] ) && 'true' === $_GET['password_reset'] && ! isset( $_POST['user_login'] ) && ! isset( $_GET['login'] ) ) {
		$status['message'] = esc_html__( 'Password reset, please log into your account.', 'ebox' );
		$status['type']    = 'success';
	}
	?>
	<div id="ebox-reset-password-wrapper" <?php echo ( ! empty( $form_width ) ) ? 'style="width: ' . esc_attr( $form_width ) . 'px;"' : ''; ?>>
	<?php
	if ( ! empty( $status ) ) {
		ebox_get_template_part(
			'modules/alert.php',
			array(
				'type'    => $status['type'],
				'icon'    => 'alert',
				'message' => $status['message'],
			),
			true
		);
	}

	ebox_login_failed_alert();

	if ( isset( $_GET['action'] ) && 'rp' === $_GET['action'] && ! isset( $status ) ) {
		?>
		<form action="" method="POST">
			<p>
				<label for="reset_password"><?php esc_html_e( 'Set new password', 'ebox' ); ?></label>
				<input type="password" name="reset_password" id="user_new_password" />
				<input type="hidden" name="user_login" id="user_login" value="<?php echo ( isset( $_GET['login'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_GET['login'] ) ) ) : '' ); ?>" />
			</p>
			<input type="submit" value="<?php esc_html_e( 'Reset Password', 'ebox' ); ?>"/>
		</form>
		<?php
	} elseif ( isset( $status['action'] ) && 'prevent_reset' === $status['action'] ) {
		// Password reset key is invalid here, don't allow them to reset the password and just show an error message.
		echo '';
	} elseif ( isset( $_GET['password_reset'] ) && 'true' === $_GET['password_reset'] ) {
		wp_login_form(
			array(
				'redirect' => get_permalink( ebox_get_reset_password_page_id() ),
			)
		);
	} else {
		?>
		<form action="" method="POST">
			<p>
				<label for="reset_password"><?php esc_html_e( 'Username or Email Address', 'ebox' ); ?></label>
				<input type="text" name="user_login" id="user_login" autocapitalize="off" autocomplete="off" />
			</p>
			<input type="submit" value="<?php esc_html_e( 'Reset Password', 'ebox' ); ?>"/>
		</form>
		<?php
	}
	?>
	</div>
	</div>
	<?php
}

/**
 * ebox Reset Password Email Send
 *
 * @since 4.4.0
 *
 * @return array $status Status type and message of email send success
 */
function ebox_reset_password_email_send(): array {
	$user_login = ! empty( $_POST['user_login'] ) // phpcs:ignore WordPress.Security.NonceVerification.Missing
		? trim( sanitize_text_field( wp_unslash( $_POST['user_login'] ) ) ) // phpcs:ignore WordPress.Security.NonceVerification.Missing
		: '';

	if ( strpos( $user_login, '@' ) ) {
		$user_data = get_user_by( 'email', $user_login );
	} else {
		$user_data = get_user_by( 'login', $user_login );
	}

	if ( ! $user_data ) {
		$status['message'] = esc_html__( 'There is no account with that username or email address.', 'ebox' );
		$status['type']    = 'warning';
		return $status;
	}

	$status['message'] = esc_html__( 'Reset password mail sent. Check your inbox.', 'ebox' );
	$status['type']    = 'success';
	wp_mail( $user_data->user_email, esc_html__( 'Password Reset', 'ebox' ), ebox_reset_password_email_message( $user_data ) );
	return $status;
}

/**
 * ebox Reset Password Email Message
 *
 * @since 4.4.0
 *
 * @param WP_User $user_data  WP_User object.
 *
 * @return string $message Content of reset password email message
 */
function ebox_reset_password_email_message( $user_data ): string {
	if ( is_multisite() ) {
		$site_name = get_network()->site_name;
	} else {
		/*
		 * The blogname option is escaped with esc_html on the way into the database
		 * in sanitize_option. We want to reverse this for the plain text arena of emails.
		 */
		$site_name = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	}
	$user_login         = $user_data->user_login;
	$reset_password_url = add_query_arg(
		array(
			'action' => 'rp',
			'key'    => get_password_reset_key( $user_data ),
			'login'  => rawurlencode( $user_login ),
		),
		get_permalink( ebox_get_reset_password_page_id() )
	);

	$message = esc_html__( 'Someone has requested a password reset for the following account:', 'ebox' ) . "\r\n\r\n";
	/* translators: %s: Site name. */
	$message .= sprintf( esc_html__( 'Site Name: %s', 'ebox' ), $site_name ) . "\r\n\r\n";
	/* translators: %s: User login. */
	$message .= sprintf( esc_html__( 'Username: %s', 'ebox' ), $user_login ) . "\r\n\r\n";
	$message .= esc_html__( 'If this was a mistake, ignore this email and nothing will happen.', 'ebox' ) . "\r\n\r\n";
	$message .= esc_html__( 'To reset your password, visit the following address:', 'ebox' ) . "\r\n\r\n";
	$message .= $reset_password_url . "\r\n\r\n";

	/**
	 * Filter the reset password email message.
	 *
	 * @since 4.4.0
	 *
	 * @param string $message Reset password email message content.
	 */
	return apply_filters( 'ebox_reset_password_email_message', $message );
}

/**
 * Reset password verification
 *
 * @since 4.4.0
 *
 * @param WP_User $user  WP_User object.
 * @param string  $key   Reset password activation key.
 *
 * @return object WP_User object on success or WP_Error object on invalid/expired key
 */
function ebox_reset_password_verification( $user, $key ) {
	return check_password_reset_key( $key, $user->user_login );
}

/**
 * Set new password for user from reset password process
 *
 * @since 4.4.0
 *
 * @param WP_User $user  WP_User object.
 * @param string  $new_password New password for user.
 *
 * @return void
 */
function ebox_reset_password_set_user_new_password( $user, $new_password ): void {
	reset_password( $user, $new_password );
	/**
	 * Fires after the user password has been updated
	 *
	 * @since 4.4.0
	 */
	do_action( 'ebox_reset_password_success' );
	remove_query_arg( 'action', get_permalink() );
	ebox_safe_redirect( add_query_arg( 'password_reset', 'true', get_permalink() ) );
}

/**
 * Display alert message if user login fails.
 *
 * @since 4.4.0
 *
 * @return void
 */
function ebox_login_failed_alert(): void {
	$login_failed = ( isset( $_GET['login'] ) && 'failed' === $_GET['login'] ? true : false );
	if ( isset( $_GET['login'] ) && 'failed' === $_GET['login'] ) :
		echo '<div class="ebox-login-failed-alert">';
		ebox_get_template_part(
			'modules/alert.php',
			array(
				'type'    => 'warning',
				'icon'    => 'alert',
				'message' => __( 'Incorrect username or password. Please try again', 'ebox' ),
			),
			true
		);
		echo '</div>';
	endif;
}

/**
 * Returns true if the registration page is set, false otherwise.
 *
 * @since 4.4.0
 *
 * @return bool
 */
function ebox_registration_page_is_set(): bool {
	if (
		is_multisite()
		|| ! ebox_is_active_theme( 'ld30' )
		|| 'yes' !== ebox_Settings_Section::get_section_setting( 'ebox_Settings_Theme_LD30', 'login_mode_enabled' )
	) {
		return false;
	}

	return ebox_registration_page_get_id() > 0;
}

if ( ! function_exists( 'ebox_registration_page_get_id' ) ) {
	/**
	 * Returns the registration page ID or 0.
	 *
	 * @since 4.5.0
	 *
	 * @return int
	 */
	function ebox_registration_page_get_id(): int {
		return (int) ebox_Settings_Section::get_section_setting(
			'ebox_Settings_Section_Registration_Pages',
			'registration'
		);
	}
}
