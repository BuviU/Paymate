<?php
/**
 * Functions related to payments
 *
 * @since 4.1.0
 *
 * @package ebox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const ebox_PRICE_TYPE_OPEN      = 'open';
const ebox_PRICE_TYPE_CLOSED    = 'closed';
const ebox_PRICE_TYPE_FREE      = 'free';
const ebox_PRICE_TYPE_PAYNOW    = 'paynow';
const ebox_PRICE_TYPE_SUBSCRIBE = 'subscribe';

require_once ebox_LMS_PLUGIN_DIR . 'includes/payments/class-ebox-payment-button.php';

/**
 * Outputs the ebox global currency symbol.
 *
 * @since 4.1.0
 *
 * @return void
 */
function ebox_the_currency_symbol(): void {
	echo wp_kses_post( ebox_get_currency_symbol() );
}

/**
 * Gets the ebox global currency symbol.
 *
 * @since 4.1.0
 * @since 4.2.0 Added $currency_code parameter.
 *
 * @param string $currency_code Optional. The country currency code (@since 4.2.0).
 *
 * @return string Currency symbol.
 */
function ebox_get_currency_symbol( string $currency_code = '' ): string {
	$currency = ! empty( $currency_code ) ? $currency_code : ebox_get_currency_code();

	if ( ! empty( $currency ) && class_exists( 'NumberFormatter' ) ) {
		$number_format = new NumberFormatter(
			get_locale() . '@currency=' . $currency,
			NumberFormatter::CURRENCY
		);
		$currency      = $number_format->getSymbol( NumberFormatter::CURRENCY_SYMBOL );
	}

	/**
	 * Filter the ebox global currency symbol.
	 *
	 * @since 4.1.0
	 *
	 * @param string $currency The currency symbol.
	 */
	return apply_filters( 'ebox_currency_symbol', $currency );
}

/**
 * Gets the ebox global currency code.
 *
 * @since 4.1.0
 *
 * @return string Currency code.
 */
function ebox_get_currency_code(): string {
	$currency = ebox_Settings_Section::get_section_setting(
		'ebox_Settings_Section_Payments_Defaults',
		'currency'
	);

	$currency = trim( $currency );

	/**
	 * Filter the ebox global currency code.
	 *
	 * @since 4.1.0
	 *
	 * @param string $currency The currency code.
	 */
	return apply_filters( 'ebox_currency_code', $currency );
}

/**
 * Gets the price formatted based on the ebox global currency configuration.
 *
 * @since 4.1.0
 * @since 4.2.0 Added $currency_code parameter.
 *
 * @param mixed  $price         The price to format.
 * @param string $currency_code Optional. The country currency code (@since 4.2.0).
 *
 * @return string Returns price formatted.
 */
function ebox_get_price_formatted( $price, string $currency_code = '' ): string {
	// Empty prices should not be displayed.
	if ( '' === $price ) {
		return '';
	}

	$currency_code = ! empty( $currency_code ) ? $currency_code : ebox_get_currency_code();

	// Price is shown as is if no currency set.
	if ( empty( $currency_code ) ) {
		return $price;
	}

	// Price is shown as is if non-numeric.
	if ( ! is_numeric( $price ) ) {
		return $price;
	}

	if ( class_exists( 'NumberFormatter' ) ) {
		$number_format = new NumberFormatter(
			get_locale() . '@currency=' . $currency_code,
			NumberFormatter::CURRENCY
		);

		return $number_format->format(
			floatval( $price )
		);
	}

	$currency_symbol = ebox_get_currency_symbol( $currency_code );

	return strlen( $currency_symbol ) > 1
		? "$price $currency_symbol" // it's currency code: we should display at the end of the price.
		: "$currency_symbol{$price}"; // show the currency symbol at the beginning of the price (en_US style).
}


/**
 * Gets the price as float value.
 *
 * @since 4.1.1
 *
 * @param string $price The price to convert.
 *
 * @return float Returns price as float value.
 */
function ebox_get_price_as_float( string $price ): float {
	if ( is_numeric( $price ) ) {
		return floatval( $price );
	}

	// trying to convert it into a numeric string.
	$dot_position   = strpos( $price, '.' );
	$comma_position = strpos( $price, ',' );

	if ( false !== $dot_position && false !== $comma_position ) {
		if ( $dot_position < $comma_position ) {
			// dot is before comma. Comma is decimal separator.
			$price = str_replace( '.', '', $price ); // remove dot.
			$price = str_replace( ',', '.', $price ); // convert comma to dot.
		} else {
			// comma is before dot. Dot is decimal separator.
			$price = str_replace( ',', '', $price ); // remove comma.
		}
	} elseif ( ! empty( $comma_position ) ) {
		$number_before_comma      = (int) mb_substr( $price, 0, $comma_position );
		$digits_count_after_comma = mb_strlen( mb_substr( $price, $comma_position + 1 ) );

		$price = str_replace(
			',',
			3 === $digits_count_after_comma && 0 !== $number_before_comma ? '' : '.',
			$price
		);
	}

	$price = preg_replace( '/[^0-9.]/', '', $price );

	return floatval( $price );
}

/**
 * Checks currency code is a zero decimal currency.
 *
 * @since 4.1.0
 *
 * @param string $currency Stripe currency ISO code.
 *
 * @return bool
 */
function ebox_is_zero_decimal_currency( string $currency = '' ): bool {
	$zero_decimal_currencies = array(
		'BIF',
		'CLP',
		'DJF',
		'GNF',
		'JPY',
		'KMF',
		'KRW',
		'MGA',
		'PYG',
		'RWF',
		'VND',
		'VUV',
		'XAF',
		'XOF',
		'XPF',
	);

	return in_array( strtoupper( $currency ), $zero_decimal_currencies, true );
}

/**
 * Gets the course price.
 *
 * Return an array of price type, amount and cycle.
 *
 * @since 3.0.0
 * @since 4.1.0 Optional $user_id param added.
 * @since 4.5.0   Param $user_id is not nullable.
 *
 * @global WP_Post $post Global post object.
 *
 * @param int|WP_Post|null $course  Course `WP_Post` object or post ID. Default to global $post.
 * @param int              $user_id User ID. Default to current user ID.
 *
 * @return array Course price details.
 */
function ebox_get_course_price( $course = null, int $user_id = 0 ): array {
	if ( is_null( $course ) ) {
		global $post;
		$course = $post;
	}

	if ( is_numeric( $course ) ) {
		$course = get_post( $course );
	}

	if ( ! is_a( $course, 'WP_Post' ) ) {
		return array();
	}

	// Get the course price.
	$meta = get_post_meta( $course->ID, '_ebox-courses', true );

	$pricing = array(
		'type'  => ! empty( $meta['ebox-courses_course_price_type'] )
			? $meta['ebox-courses_course_price_type']
			: ebox_DEFAULT_COURSE_PRICE_TYPE,
		'price' => ! empty( $meta['ebox-courses_course_price'] )
			? $meta['ebox-courses_course_price']
			: '',
	);

	// Get the price a user had when was applying a coupon.

	if ( 0 === $user_id ) {
		$user_id = get_current_user_id();
	}

	if (
		$user_id > 0 &&
		ebox_get_price_as_float( strval( $pricing['price'] ) ) > 0 &&
		ebox_post_has_attached_coupon( $course->ID, $user_id )
	) {
		$attached_coupon_data = ebox_get_attached_coupon_data( $course->ID, $user_id );

		if ( ! empty( $attached_coupon_data ) ) {
			$pricing['price'] = $attached_coupon_data->price;
		}
	}

	// Add subscription data.
	if ( ebox_PRICE_TYPE_SUBSCRIBE === $pricing['type'] ) {
		$interval        = intval( ebox_get_setting( $course->ID, 'course_price_billing_p3' ) );
		$frequency       = strval( ebox_get_setting( $course->ID, 'course_price_billing_t3' ) );
		$repeats         = intval( ebox_get_setting( $course->ID, 'course_no_of_cycles' ) );
		$trial_interval  = intval( ebox_get_setting( $course->ID, 'course_trial_duration_p1' ) );
		$trial_frequency = strval( ebox_get_setting( $course->ID, 'course_trial_duration_t1' ) );

		$pricing['interval']      = $interval;
		$pricing['frequency']     = ebox_get_grammatical_number_label_for_interval( $interval, $frequency );
		$pricing['frequency_raw'] = $frequency;

		$pricing['repeats']          = $repeats;
		$pricing['repeat_frequency'] = empty( $repeats ) ? '' : ebox_get_grammatical_number_label_for_interval( $repeats, $frequency );

		$pricing['trial_price']         = strval( ebox_get_setting( $course->ID, 'course_trial_price' ) );
		$pricing['trial_interval']      = $trial_interval;
		$pricing['trial_frequency']     = empty( $trial_interval ) ? '' : ebox_get_grammatical_number_label_for_interval( $trial_interval, $trial_frequency );
		$pricing['trial_frequency_raw'] = $trial_frequency;
	}

	/**
	 * Filters price details for a course.
	 *
	 * @since 3.0.0
	 *
	 * @param array $pricing Course price details.
	 */
	return apply_filters( 'ebox_get_course_price', $pricing );
}

/**
 * Get team price
 *
 * Return an array of price type, amount and cycle
 *
 * @since 3.2.0
 * @since 4.1.0 Optional $user_id param added.
 * @since 4.5.0   Param $user_id is not nullable.
 *
 * @param int|WP_Post|null $team   Team `WP_Post` object or post ID. Default to global $post.
 * @param int              $user_id User ID. Default to current user id.
 *
 * @return array price details.
 */
function ebox_get_team_price( $team = null, int $user_id = 0 ): array {
	if ( is_null( $team ) ) {
		global $post;
		$team = $post;
	}

	if ( is_numeric( $team ) ) {
		$team = get_post( $team );
	}

	if ( ! is_a( $team, 'WP_Post' ) ) {
		return array();
	}

	// Get the team price.

	$meta = get_post_meta( $team->ID, '_teams', true );

	$pricing = array(
		'type'  => ! empty( $meta['teams_team_price_type'] )
			? $meta['teams_team_price_type']
			: ebox_DEFAULT_GROUP_PRICE_TYPE,
		'price' => ! empty( $meta['teams_team_price'] )
			? $meta['teams_team_price']
			: '',
	);

	// Get the price a user had when was applying a coupon.

	if ( 0 === $user_id ) {
		$user_id = get_current_user_id();
	}

	if (
		$user_id > 0 &&
		ebox_get_price_as_float( strval( $pricing['price'] ) ) > 0 &&
		ebox_post_has_attached_coupon( $team->ID, $user_id )
	) {
		$attached_coupon_data = ebox_get_attached_coupon_data( $team->ID, $user_id );

		if ( ! empty( $attached_coupon_data ) ) {
			$pricing['price'] = $attached_coupon_data->price;
		}
	}

	// Add subscription data.

	if ( ebox_PRICE_TYPE_SUBSCRIBE === $pricing['type'] ) {
		$interval        = intval( ebox_get_setting( $team->ID, 'team_price_billing_p3' ) );
		$frequency       = strval( ebox_get_setting( $team->ID, 'team_price_billing_t3' ) );
		$repeats         = intval( ebox_get_setting( $team->ID, 'post_no_of_cycles' ) );
		$trial_interval  = intval( ebox_get_setting( $team->ID, 'team_trial_duration_p1' ) );
		$trial_frequency = strval( ebox_get_setting( $team->ID, 'team_trial_duration_t1' ) );

		$pricing['interval']      = $interval;
		$pricing['frequency']     = ebox_get_grammatical_number_label_for_interval( $interval, $frequency );
		$pricing['frequency_raw'] = $frequency;

		$pricing['repeats']          = $repeats;
		$pricing['repeat_frequency'] = empty( $repeats ) ? '' : ebox_get_grammatical_number_label_for_interval( $repeats, $frequency );

		$pricing['trial_price']         = strval( ebox_get_setting( $team->ID, 'team_trial_price' ) );
		$pricing['trial_interval']      = $trial_interval;
		$pricing['trial_frequency']     = empty( $trial_interval ) ? '' : ebox_get_grammatical_number_label_for_interval( $trial_interval, $trial_frequency );
		$pricing['trial_frequency_raw'] = $trial_frequency;
	}

	/**
	 * Filter Team Price details.
	 *
	 * @since 3.2.0
	 *
	 * @param array $pricing Team Price Details array.
	 */
	return apply_filters( 'ebox_get_team_price', $pricing );
}

/**
 * Get the singular or plural label for recurring payment intervals
 *
 * @since 3.6.0
 * @since 4.5.0   $interval must be integer.
 *
 * @param int    $interval  Number of payment intervals.
 * @param string $frequency A symbol for day, week, month or year.
 *
 * @return string
 */
function ebox_get_grammatical_number_label_for_interval( int $interval, string $frequency ): string {
	switch ( $frequency ) {
		case ( 'D' ):
			return _n( 'day', 'days', $interval, 'ebox' );

		case ( 'W' ):
			return _n( 'week', 'weeks', $interval, 'ebox' );

		case ( 'M' ):
			return _n( 'month', 'months', $interval, 'ebox' );

		case ( 'Y' ):
			return _n( 'year', 'years', $interval, 'ebox' );

		default:
			return '';
	}
}

/**
 * Generates the ebox payment buttons output.
 *
 * @since 2.1.0
 *
 * @param int|WP_Post $post Post ID or `WP_Post` object.
 *
 * @return string The payment buttons HTML output.
 */
function ebox_payment_buttons( $post ): string {
	$payment_button_generator = new ebox_Payment_Button( $post );

	/**
	 * Filters payment button HTML right before output. Fires in the end.
	 *
	 * @since 4.5.0
	 *
	 * @param string $button Payment button HTML markup. Can contain all types of buttons.
	 *
	 * @return string Payment button HTML markup.
	 */
	$button = (string) apply_filters( 'ebox_payment_button_markup', $payment_button_generator->map() );

	/**
	 * Fires when the payment button is added.
	 *
	 * @since 4.5.0
	 *
	 * @param string $button Payment button HTML markup. Can contain all types of buttons.
	 */
	do_action( 'ebox_payment_button_added', $button );

	return $button;
}

/**
 * Output array of country currency code data.
 *
 * @since 4.4.0
 *
 * @return array
 */
function ebox_currency_codes_list(): array {
	$currency_codes       = array();
	$currency_codes_array = array_map( 'str_getcsv', file( ebox_LMS_PLUGIN_DIR . 'assets/misc/payment-currencies.csv' ) );
	// Remove CSV headers from array.
	unset( $currency_codes_array[0] );

	foreach ( $currency_codes_array as $code ) {
		list(
			$country,
			$currency,
			$currency_code,
			$numeric_code,
			$minor_unit,
			$withdrawal_date,
		) = $code;

		$currency_codes[] = array(
			'currency_code' => $currency_code,
			'option_label'  => ucwords( mb_strtolower( $country ) ) . ' (' . ebox_get_currency_symbol( $currency_code ) . ') ',
			'country'       => $country,
			'currency'      => mb_strtoupper( $currency ),
		);
	}

	/**
	 * Filters list of currency codes.
	 *
	 * @since 4.4.0
	 *
	 * @param array $currency_codes List of currency codes.
	 */
	return apply_filters( 'ebox_currency_code_list', $currency_codes );
}
