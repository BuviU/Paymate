<?php
/**
 * Coupon functions
 *
 * @since 4.1.0
 *
 * @package ebox\Coupons
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const ebox_COUPON_META_KEY_CODE                = 'code';
const ebox_COUPON_META_KEY_TYPE                = 'type';
const ebox_COUPON_META_KEY_AMOUNT              = 'amount';
const ebox_COUPON_META_KEY_REDEMPTIONS         = 'redemptions';
const ebox_COUPON_META_KEY_START_DATE          = 'start_date';
const ebox_COUPON_META_KEY_END_DATE            = 'end_date';
const ebox_COUPON_META_KEY_PREFIX_APPLY_TO_ALL = 'apply_to_all_';

const ebox_TRANSACTION_COUPON_META_KEY = 'coupon';

const ebox_COUPON_TYPE_FLAT       = 'flat';
const ebox_COUPON_TYPE_PERCENTAGE = 'percentage';

const ebox_COUPON_ASSOCIATED_FIELDS = array( 'courses', 'teams' );

/**
 * Checks if a coupon is valid.
 *
 * @since 4.1.0
 *
 * @param string $coupon_code Coupon code.
 * @param int    $post_id     Course/Team ID.
 *
 * @return array{is_valid: bool, error: string}
 */
function ebox_check_coupon_is_valid( string $coupon_code, int $post_id ): array {
	$errors = array(
		'invalid'     => __( 'Coupon is invalid.', 'ebox' ),
		'expired'     => __( 'Coupon has expired.', 'ebox' ),
		'usage_limit' => __( 'Coupon max redemption limit reached.', 'ebox' ),
	);

	$course_post_type = LDLMS_Post_Types::get_post_type_slug( LDLMS_Post_Types::COURSE );
	$team_post_type  = LDLMS_Post_Types::get_post_type_slug( LDLMS_Post_Types::GROUP );

	// Check if params are empty.

	if ( empty( $coupon_code ) || empty( $post_id ) ) {
		return array(
			'is_valid' => false,
			'error'    => $errors['invalid'],
		);
	}

	// Check if post type is valid.

	$post_type = get_post_type( $post_id );

	$valid_post_types = array( $course_post_type, $team_post_type );

	if ( ! in_array( $post_type, $valid_post_types, true ) ) {
		return array(
			'is_valid' => false,
			'error'    => $errors['invalid'],
		);
	}

	// Check if a coupon exists.

	$coupon = ebox_get_coupon_by_code( $coupon_code );

	if ( is_null( $coupon ) ) {
		return array(
			'is_valid' => false,
			'error'    => $errors['invalid'],
		);
	}

	$coupon_settings = ebox_get_setting( $coupon );

	// Check if the passed course/team is allowed.

	$post_type_field_key_hash = array(
		$course_post_type => 'courses',
		$team_post_type  => 'teams',
	);

	$post_type_field_key = $post_type_field_key_hash[ $post_type ];

	if ( 'on' !== $coupon_settings[ ebox_COUPON_META_KEY_PREFIX_APPLY_TO_ALL . $post_type_field_key ] ) {
		$valid_post_ids = $coupon_settings[ $post_type_field_key ];

		if ( empty( $valid_post_ids ) || ! in_array( $post_id, $valid_post_ids, true ) ) {
			return array(
				'is_valid' => false,
				'error'    => $errors['invalid'],
			);
		}
	}

	// Check redemptions limit if needed.

	if (
		$coupon_settings['max_redemptions'] > 0 &&
		$coupon_settings[ ebox_COUPON_META_KEY_REDEMPTIONS ] >= $coupon_settings['max_redemptions']
	) {
		return array(
			'is_valid' => false,
			'error'    => $errors['usage_limit'],
		);
	}

	// Check dates if needed.

	$current_time = time();
	$start_date   = (int) $coupon_settings[ ebox_COUPON_META_KEY_START_DATE ];
	$end_date     = (int) $coupon_settings[ ebox_COUPON_META_KEY_END_DATE ];

	if ( $start_date > 0 && $current_time < $start_date ) {
		return array(
			'is_valid' => false,
			'error'    => $errors['invalid'],
		);
	}

	if ( $end_date > 0 && $current_time >= $end_date ) {
		return array(
			'is_valid' => false,
			'error'    => $errors['expired'],
		);
	}

	return array(
		'is_valid' => true,
		'error'    => '',
	);
}

/**
 * Returns a new price.
 *
 * @since 4.1.0
 *
 * @param int   $coupon_id Coupon ID.
 * @param float $price     Price.
 *
 * @return float
 */
function ebox_calculate_coupon_discounted_price( int $coupon_id, float $price ): float {
	$coupon = get_post( $coupon_id );

	if ( is_null( $coupon ) ) {
		return $price;
	}

	$coupon_settings = ebox_get_setting( $coupon );

	if ( empty( $coupon_settings ) ) {
		return $price;
	}

	$coupon_type = $coupon_settings[ ebox_COUPON_META_KEY_TYPE ];
	$amount      = (float) $coupon_settings[ ebox_COUPON_META_KEY_AMOUNT ];

	if ( ebox_COUPON_TYPE_PERCENTAGE === $coupon_type ) {
		$price = $price - ( $price / 100 * $amount );
	} elseif ( ebox_COUPON_TYPE_FLAT === $coupon_type ) {
		$price = $price - $amount;
	}

	if ( ebox_is_zero_decimal_currency( ebox_get_currency_code() ) ) {
		$price = ceil( $price );
	}

	if ( $price < 0 ) {
		$price = 0;
	}

	return round( $price, 2 );
}

/**
 * Finds a coupon post by a coupon code.
 *
 * @since 4.1.0
 *
 * @param string $coupon_code Coupon Code.
 *
 * @return WP_Post|null
 */
function ebox_get_coupon_by_code( string $coupon_code ): ?WP_Post {
	if ( empty( $coupon_code ) ) {
		return null;
	}

	$query_args = array(
		'post_type'      => LDLMS_Post_Types::get_post_type_slug( LDLMS_Post_Types::COUPON ),
		'posts_per_page' => - 1,
		'post_status'    => 'publish',
		// phpcs:ignore
		'meta_query'     => array(
			array(
				'key'     => ebox_COUPON_META_KEY_CODE,
				'value'   => $coupon_code,
				'compare' => '=',
			),
		),
	);

	$query = new WP_Query( $query_args );

	return empty( $query->posts ) ? null : $query->posts[0];
}

/**
 * Checks if active coupons exist.
 *
 * @since 4.1.0
 *
 * @return bool
 */
function ebox_active_coupons_exist(): bool {
	$coupon_post_type = LDLMS_Post_Types::get_post_type_slug( LDLMS_Post_Types::COUPON );

	if ( 0 === wp_count_posts( strval( $coupon_post_type ) )->publish ) {
		return false;
	}

	$current_time = time();

	$meta_query_teams = array(
		array(
			'relation' => 'AND',
			array(
				'key'     => ebox_COUPON_META_KEY_START_DATE,
				'compare' => '<=',
				'value'   => $current_time,
				'type'    => 'NUMERIC',
			),
			array(
				'key'     => ebox_COUPON_META_KEY_END_DATE,
				'compare' => '>',
				'value'   => $current_time,
				'type'    => 'NUMERIC',
			),
		),
		array(
			'relation' => 'AND',
			array(
				'key'     => ebox_COUPON_META_KEY_START_DATE,
				'compare' => '<=',
				'value'   => $current_time,
				'type'    => 'NUMERIC',
			),
			array(
				'key'     => ebox_COUPON_META_KEY_END_DATE,
				'compare' => '=',
				'value'   => 0,
				'type'    => 'NUMERIC',
			),
		),
	);

	foreach ( $meta_query_teams as $meta_query ) {
		$query_args = array(
			'post_type'      => $coupon_post_type,
			'posts_per_page' => 1,
			'post_status'    => 'publish',
			'fields'         => 'ids',
			'meta_query'     => $meta_query,
		);

		$query = new WP_Query( $query_args );

		if ( $query->post_count > 0 ) {
			return true;
		}
	}

	return false;
}

/**
 * Increments redemptions meta of a coupon.
 *
 * @since 4.1.0
 *
 * @param int $coupon_id Coupon Post ID.
 * @param int $post_id   Course/Team ID.
 * @param int $user_id   User ID.
 *
 * @return void
 */
function ebox_increment_coupon_redemptions( int $coupon_id, int $post_id, int $user_id ): void {
	ebox_detach_coupon( $post_id, $user_id );

	if ( is_null( get_post( $coupon_id ) ) ) {
		return;
	}

	$redemptions = (int) ebox_get_setting(
		$coupon_id,
		ebox_COUPON_META_KEY_REDEMPTIONS
	);

	ebox_update_setting(
		$coupon_id,
		ebox_COUPON_META_KEY_REDEMPTIONS,
		$redemptions + 1
	);
}

/**
 * Attaches a coupon to a course/team.
 *
 * @since 4.1.0
 *
 * @param int   $post_id          Course/Team ID.
 * @param int   $coupon_id        Coupon Post ID.
 * @param float $price            Full price.
 * @param float $discounted_price Price by a coupon.
 *
 * @return void
 */
function ebox_attach_coupon( int $post_id, int $coupon_id, float $price, float $discounted_price ): void {
	if ( ! is_user_logged_in() ) {
		return;
	}

	$coupon_settings = ebox_get_setting( $coupon_id );

	try {
		$coupon_dto = ebox_Coupon_DTO::create(
			array(
				'currency'                       => ebox_get_currency_code(),
				'price'                          => $price,
				'discount'                       => $price - $discounted_price,
				'discounted_price'               => $discounted_price,
				'coupon_id'                      => $coupon_id,
				ebox_COUPON_META_KEY_CODE   => $coupon_settings[ ebox_COUPON_META_KEY_CODE ],
				ebox_COUPON_META_KEY_TYPE   => $coupon_settings[ ebox_COUPON_META_KEY_TYPE ],
				ebox_COUPON_META_KEY_AMOUNT => $coupon_settings[ ebox_COUPON_META_KEY_AMOUNT ],
			)
		);
	} catch ( ebox_DTO_Validation_Exception $e ) {
		return;
	}

	set_transient(
		ebox_map_coupon_transient_key( $post_id, get_current_user_id() ),
		$coupon_dto->to_array(),
		DAY_IN_SECONDS
	);
}

/**
 * Detaches a coupon from a course/team.
 *
 * @since 4.1.0
 *
 * @param int $post_id Course/Team ID.
 * @param int $user_id User ID.
 *
 * @return void
 */
function ebox_detach_coupon( int $post_id, int $user_id ): void {
	delete_transient(
		ebox_map_coupon_transient_key( $post_id, $user_id )
	);
}

/**
 * Get an attached coupon data of a course/team.
 *
 * @since 4.1.0
 *
 * @param int $post_id Course/Team ID.
 * @param int $user_id User ID.
 *
 * @return ebox_Coupon_DTO|null
 */
function ebox_get_attached_coupon_data( int $post_id, int $user_id ): ?ebox_Coupon_DTO {
	$attached_coupon_data = get_transient(
		ebox_map_coupon_transient_key( $post_id, $user_id )
	);

	if ( false === $attached_coupon_data ) {
		return null;
	}

	try {
		return ebox_Coupon_DTO::create( (array) $attached_coupon_data );
	} catch ( ebox_DTO_Validation_Exception $e ) {
		return null;
	}
}

/**
 * Check if a course/team has an attached coupon.
 *
 * @since 4.1.0
 *
 * @param int $post_id Course/Team ID.
 * @param int $user_id User ID.
 *
 * @return bool
 */
function ebox_post_has_attached_coupon( int $post_id, int $user_id ): bool {
	$attached_coupon_data = get_transient(
		ebox_map_coupon_transient_key( $post_id, $user_id )
	);

	return false !== $attached_coupon_data;
}

/**
 * Maps a coupon transient key.
 *
 * @since 4.1.0
 *
 * @param int $post_id Post ID.
 * @param int $user_id User ID.
 *
 * @return string
 */
function ebox_map_coupon_transient_key( int $post_id, int $user_id ): string {
	return "ld_coupon_for_post_{$post_id}_by_user_{$user_id}";
}

/**
 * Syncs associated metas of a coupon.
 *
 * @since 4.1.0
 *
 * @param int    $post_id Coupon Post ID.
 * @param string $field   Field Name (courses|teams).
 * @param array  $ids     IDs.
 *
 * @return void
 */
function ebox_sync_coupon_associated_metas( int $post_id, string $field, array $ids ): void {
	if ( ! in_array( $field, ebox_COUPON_ASSOCIATED_FIELDS, true ) ) {
		return;
	}

	$meta_prefix = "{$field}_";

	/**
	 * Existing associated IDs.
	 *
	 * @var array<string> $existing_ids
	 */
	$existing_ids = (array) ebox_get_setting( $post_id, $field );

	// Delete associated metas that we no longer need.
	if ( ! empty( $existing_ids ) ) {
		foreach ( array_diff( $existing_ids, $ids ) as $id ) {
			delete_post_meta( $post_id, "{$meta_prefix}{$id}" );
		}
	}

	// Add associated metas we need.
	foreach ( $ids as $id ) {
		update_post_meta( $post_id, "{$meta_prefix}{$id}", $id );
	}
}

/**
 * Handles a coupon applying action made via AJAX request on LD Register page.
 *
 * @since 4.1.0
 *
 * @return void
 */
function ebox_apply_coupon(): void {
	if (
		empty( $_POST['nonce'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'ebox-coupon-nonce' ) ||
		empty( $_POST['post_id'] ) ||
		! is_user_logged_in() ) {
		wp_send_json_error(
			array(
				'message' => __( 'Invalid request.', 'ebox' ),
			)
		);
	}

	if ( empty( $_POST['coupon_code'] ) ) {
		wp_send_json_error(
			array(
				'message' => __( 'Please enter the coupon code.', 'ebox' ),
			)
		);
	}

	$product = ebox_Product_Model::find( (int) $_POST['post_id'] );

	if ( ! $product ) {
		wp_send_json_error(
			array(
				'message' => __( 'Product not found.', 'ebox' ),
			)
		);
	}

	// Check if the coupon code is valid.

	$coupon_code = sanitize_text_field( wp_unslash( $_POST['coupon_code'] ) );

	$coupon_validation_result = ebox_check_coupon_is_valid( $coupon_code, $product->get_id() );

	if ( ! $coupon_validation_result['is_valid'] ) {
		wp_send_json_error(
			array(
				'message' => $coupon_validation_result['error'],
			)
		);
	}

	// Check if we are processing the "buy now" pricing.

	if ( ebox_PRICE_TYPE_PAYNOW !== $product->get_pricing_type() ) {
		wp_send_json_error(
			array(
				'message' => __( 'Subscriptions are not supported for now.', 'ebox' ),
			)
		);
	}

	// Process an action.

	try {
		$product_pricing = $product->get_pricing();
	} catch ( ebox_DTO_Validation_Exception $e ) {
		wp_send_json_error(
			array(
				'message' => __( 'Something went wrong.', 'ebox' ),
			)
		);
	}

	$coupon           = ebox_get_coupon_by_code( $coupon_code );
	$discounted_price = ebox_calculate_coupon_discounted_price( $coupon->ID, $product_pricing->price );
	$discount         = ( $product_pricing->price - $discounted_price ) * -1;

	$price = $discounted_price;
	if ( ! ebox_is_zero_decimal_currency( ebox_get_currency_code() ) ) {
		$price = intval( $price * 100 );
	}

	ebox_attach_coupon( $product->get_id(), $coupon->ID, $product_pricing->price, $discounted_price );

	wp_send_json_success(
		array(
			'coupon_code' => $coupon_code,
			'discount'    => esc_html(
				ebox_get_price_formatted( $discount )
			),
			'total'       => array(
				'value'        => $discounted_price,
				'stripe_value' => $price,
				'formatted'    => esc_html(
					ebox_get_price_formatted( $discounted_price )
				),
			),
			'message'     => __( 'Coupon applied.', 'ebox' ),
		)
	);
}

/**
 * Handles a coupon removing action made via AJAX request on LD Register page.
 *
 * @since 4.1.0
 *
 * @return void
 */
function ebox_remove_coupon(): void {
	if (
		empty( $_POST['nonce'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'ebox-coupon-nonce' ) ||
		empty( $_POST['post_id'] ) ||
		! is_user_logged_in() ) {
		wp_send_json_error(
			array(
				'message' => __( 'Invalid request.', 'ebox' ),
			)
		);
	}

	// Check if we are processing a course/team.

	$product = ebox_Product_Model::find( (int) $_POST['post_id'] );

	if ( ! $product ) {
		wp_send_json_error(
			array(
				'message' => __( 'Invalid product.', 'ebox' ),
			)
		);
	}

	// Detach the coupon.

	ebox_detach_coupon( $product->get_id(), get_current_user_id() );

	// Calculate the price.

	try {
		$product_pricing = $product->get_pricing();
	} catch ( ebox_DTO_Validation_Exception $e ) {
		wp_send_json_error(
			array(
				'message' => __( 'Something went wrong.', 'ebox' ),
			)
		);
	}

	wp_send_json_success(
		array(
			'total'   => array(
				'value'        => number_format( $product_pricing->price, 2, '.', '' ),
				'stripe_value' => ebox_is_zero_decimal_currency( $product_pricing->currency )
					? $product_pricing->price
					: intval( $product_pricing->price * 100 ),
				'formatted'    => esc_html(
					ebox_get_price_formatted( $product_pricing->price )
				),
			),
			'message' => __( 'Coupon removed.', 'ebox' ),
		)
	);
}

/**
 * Increments coupon's redemptions and saves a coupon to transaction's meta.
 *
 * @since 4.1.0
 *
 * @param int $transaction_id Transaction ID.
 *
 * @return void
 */
function ebox_process_coupon_after_transaction( int $transaction_id ): void {
	$transaction = ebox_Transaction_Model::find( $transaction_id );

	if ( ! $transaction ) {
		return;
	}

	$product = $transaction->get_product();
	$user    = $transaction->get_user();

	if ( ! $product || 0 === $user->ID ) {
		return;
	}

	if ( ! ebox_post_has_attached_coupon( $product->get_id(), $user->ID ) ) {
		return;
	}

	$coupon_data = ebox_get_attached_coupon_data( $product->get_id(), $user->ID );

	if ( ! $coupon_data ) {
		return;
	}

	try {
		update_post_meta(
			$transaction_id,
			ebox_TRANSACTION_COUPON_META_KEY,
			ebox_Transaction_Coupon_DTO::create( $coupon_data->to_array() )->to_array()
		);

		update_post_meta(
			$transaction_id,
			ebox_Transaction_Model::$meta_key_pricing_info,
			ebox_Pricing_DTO::create( $coupon_data->to_array() )->to_array()
		);
	} catch ( ebox_DTO_Validation_Exception $e ) {
		return;
	}

	// Maybe we'll need to filter transactions by a coupon code.
	update_post_meta(
		$transaction_id,
		ebox_TRANSACTION_COUPON_META_KEY . '_code',
		$coupon_data->code
	);

	ebox_increment_coupon_redemptions( $coupon_data->coupon_id, $product->get_id(), $user->ID );
}

/**
 * Modifies course/team price if a coupon is attached.
 *
 * @since 4.1.0
 *
 * @param float    $price   Course/Team Price.
 * @param int      $post_id Course/Team ID.
 * @param int|null $user_id User ID.
 *
 * @return float
 */
function ebox_get_price_by_coupon( float $price, int $post_id, ?int $user_id ): float {
	if ( is_null( $user_id ) || 0 === $user_id ) {
		return $price;
	}

	if ( ! ebox_post_has_attached_coupon( $post_id, $user_id ) ) {
		return $price;
	}

	$attached_coupon_data = ebox_get_attached_coupon_data( $post_id, $user_id );

	if ( ! $attached_coupon_data ) {
		return $price;
	}

	return $attached_coupon_data->discounted_price;
}

/**
 * Enrolls a user if the price by coupon is 0.
 *
 * @since 4.1.0
 *
 * @return void
 */
function ebox_enroll_with_zero_price(): void {
	if (
		empty( $_POST['nonce'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'ebox-coupon-nonce' ) ||
		empty( (int) $_POST['post_id'] ) ||
		! is_user_logged_in()
	) {
		wp_send_json_error(
			array(
				'message' => __( 'Invalid request.', 'ebox' ),
			)
		);
	}

	// Check if we are processing a course/team.

	$product = ebox_Product_Model::find( (int) $_POST['post_id'] );

	if ( ! $product ) {
		wp_send_json_error(
			array(
				'message' => __( 'Product not found.', 'ebox' ),
			)
		);
	}

	// Check if the price by coupon is 0.

	try {
		$product_pricing = $product->get_pricing();
	} catch ( ebox_DTO_Validation_Exception $e ) {
		wp_send_json_error(
			array(
				'message' => __( 'Something went wrong.', 'ebox' ),
			)
		);
	}

	$user  = wp_get_current_user();
	$price = ebox_get_price_by_coupon( $product_pricing->price, $product->get_id(), $user->ID );

	if ( $price > 0 ) {
		wp_send_json_error(
			array(
				'message' => __( 'You have to pay for access.', 'ebox' ),
			)
		);
	}

	// Attach a coupon.

	$coupon_data = ebox_get_attached_coupon_data( $product->get_id(), $user->ID );

	if ( empty( $coupon_data ) ) {
		wp_send_json_error(
			array(
				'message' => __( 'Something went wrong.', 'ebox' ),
			)
		);
	}

	ebox_attach_coupon( $product->get_id(), $coupon_data->coupon_id, $product_pricing->price, 0 );

	// Create a transaction.

	$transaction_id = ebox_transaction_create(
		array(
			ebox_Transaction_Model::$meta_key_is_free => true,
		),
		$product->get_post(),
		$user
	);

	$transaction = ebox_Transaction_Model::find( $transaction_id );

	if ( ! $transaction ) {
		wp_send_json_error(
			array(
				'message' => __( 'Something went wrong.', 'ebox' ),
			)
		);
	}

	// Enroll.

	$product->enroll( $user );

	// Redirect.

	wp_send_json_success(
		array(
			'redirect_url' => ebox_Unknown_Gateway::get_url_success(
				array( $product )
			),
		)
	);
}

if ( ! function_exists( 'ebox_coupons_init' ) ) {
	/**
	 * Add filters and actions for the coupon functionality.
	 *
	 * @since 4.5.0
	 *
	 * @return void
	 */
	function ebox_coupons_init() {
		add_action( 'wp_ajax_ebox_apply_coupon', 'ebox_apply_coupon' );
		add_action( 'wp_ajax_ebox_remove_coupon', 'ebox_remove_coupon' );
		add_action( 'wp_ajax_ebox_enroll_with_zero_price', 'ebox_enroll_with_zero_price' );
		add_action( 'ebox_transaction_created', 'ebox_process_coupon_after_transaction' );
		add_filter( 'ebox_get_price_by_coupon', 'ebox_get_price_by_coupon', 10, 3 );
	}
}
