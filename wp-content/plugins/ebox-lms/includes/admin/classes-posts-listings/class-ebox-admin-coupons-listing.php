<?php
/**
 * ebox Coupons Posts Listing Class.
 *
 * @package ebox\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'ebox_Admin_Posts_Listing' ) && ! class_exists( 'ebox_Admin_Coupons_Listing' ) ) {
	/**
	 * Class for ebox Coupons Listing Pages.
	 */
	class ebox_Admin_Coupons_Listing extends ebox_Admin_Posts_Listing {
		const COUPON_USED   = 'used';
		const COUPON_UNUSED = 'unused';

		const COUPON_ACTIVE  = 'active';
		const COUPON_EXPIRED = 'expired';
		const COUPON_FUTURE  = 'future';

		const ALL_VALUE = 'all';
		const ON_VALUE  = 'on';

		/**
		 * Class constructor.
		 */
		public function __construct() {
			$this->post_type = LDLMS_Post_Types::get_post_type_slug( LDLMS_Post_Types::COUPON );

			parent::__construct();
		}

		/**
		 * Called via the WordPress init action hook.
		 */
		public function listing_init() {
			if ( $this->listing_init_done ) {
				return;
			}

			$this->selectors = array(
				'type'        => array(
					'type'                   => 'early',
					'display'                => array( $this, 'selector_select_box' ),
					'show_all_value'         => '',
					'show_all_label'         => esc_html__( 'All Types', 'ebox' ),
					'options'                => array(
						ebox_COUPON_TYPE_FLAT       => esc_html__( 'Flat', 'ebox' ),
						ebox_COUPON_TYPE_PERCENTAGE => esc_html__( 'Percentage', 'ebox' ),
					),
					'listing_query_function' => array( $this, 'filter_type' ),
					'select2'                => false,
				),
				'redemptions' => array(
					'type'                   => 'early',
					'display'                => array( $this, 'selector_select_box' ),
					'show_all_value'         => '',
					'show_all_label'         => esc_html__( 'Used & Unused', 'ebox' ),
					'options'                => array(
						self::COUPON_USED   => esc_html__( 'Used', 'ebox' ),
						self::COUPON_UNUSED => esc_html__( 'Unused', 'ebox' ),
					),
					'listing_query_function' => array( $this, 'filter_redemptions' ),
					'select2'                => false,
				),
				'state'       => array(
					'type'                   => 'early',
					'display'                => array( $this, 'selector_select_box' ),
					'show_all_value'         => '',
					'show_all_label'         => esc_html__( 'All States', 'ebox' ),
					'options'                => array(
						self::COUPON_ACTIVE  => esc_html__( 'Active', 'ebox' ),
						self::COUPON_EXPIRED => esc_html__( 'Expired', 'ebox' ),
						self::COUPON_FUTURE  => esc_html__( 'Future Start Date', 'ebox' ),
					),
					'listing_query_function' => array( $this, 'filter_state' ),
					'select2'                => false,
				),
				'courses_id'  => array(
					'type'                   => 'post_type',
					'post_type'              => ebox_get_post_type_slug( 'course' ),
					'show_all_value'         => '',
					'show_all_label'         => sprintf(
						// Translators: placeholder: Courses.
						esc_html_x( 'All & Associated %s', 'placeholder: Courses', 'ebox' ),
						ebox_Custom_Label::get_label( 'courses' )
					),
					'show_empty_value'       => self::ALL_VALUE,
					'show_empty_label'       => sprintf(
						// Translators: placeholder: Courses.
						esc_html_x( 'All %s Only', 'placeholder: Courses', 'ebox' ),
						ebox_Custom_Label::get_label( 'courses' )
					),
					'listing_query_function' => array( $this, 'filter_associated' ),
				),
				'teams_id'   => array(
					'type'                   => 'post_type',
					'post_type'              => ebox_get_post_type_slug( 'team' ),
					'show_all_value'         => '',
					'show_all_label'         => sprintf(
						// Translators: placeholder: Teams.
						esc_html_x( 'All & Associated %s', 'placeholder: Teams', 'ebox' ),
						ebox_Custom_Label::get_label( 'teams' )
					),
					'show_empty_value'       => self::ALL_VALUE,
					'show_empty_label'       => sprintf(
						// Translators: placeholder: Teams.
						esc_html_x( 'All %s Only', 'placeholder: Teams', 'ebox' ),
						ebox_Custom_Label::get_label( 'teams' )
					),
					'listing_query_function' => array( $this, 'filter_associated' ),
				),
			);

			$this->columns = array(
				'code'        => array(
					'label'   => esc_html__( 'Code', 'ebox' ),
					'after'   => 'title',
					'display' => array( $this, 'column_code' ),
				),
				'discount'    => array(
					'label'   => esc_html__( 'Discount', 'ebox' ),
					'after'   => 'code',
					'display' => array( $this, 'column_discount' ),
				),
				'redemptions' => array(
					'label'   => esc_html__( 'Number of Uses', 'ebox' ),
					'after'   => 'discount',
					'display' => array( $this, 'column_redemptions' ),
				),
				'state'       => array(
					'label'   => esc_html__( 'State', 'ebox' ),
					'after'   => 'redemptions',
					'display' => array( $this, 'column_state' ),
				),
				'courses'     => array(
					'label'   => sprintf(
						// Translators: placeholder: Courses.
						esc_html_x( 'Associated %s', 'placeholder: Courses', 'ebox' ),
						ebox_get_custom_label( 'courses' )
					),
					'after'   => 'state',
					'display' => array( $this, 'column_associated' ),
				),
				'teams'      => array(
					'label'   => sprintf(
						// Translators: placeholder: Teams.
						esc_html_x( 'Associated %s', 'placeholder: Teams', 'ebox' ),
						ebox_get_custom_label( 'teams' )
					),
					'after'   => 'courses',
					'display' => array( $this, 'column_associated' ),
				),
			);

			parent::listing_init();

			$this->listing_init_done = true;
		}

		/**
		 * Selector for a select box.
		 *
		 * @since 4.1.0
		 *
		 * @param array $selector Selector args.
		 *
		 * @return void
		 */
		protected function selector_select_box( array $selector ): void {
			$this->show_selector_start( $selector );
			$this->show_selector_all_option( $selector );

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$selected_value = sanitize_text_field( wp_unslash( $_GET[ $selector['field_name'] ] ?? '' ) );

			foreach ( $selector['options'] as $value => $label ) {
				echo sprintf(
					'<option value="%s" %s>%s</option>',
					esc_attr( $value ),
					selected( $value, $selected_value, false ),
					esc_attr( $label )
				);
			}

			$this->show_selector_end( $selector );
		}

		/**
		 * Filter by type.
		 *
		 * @since 4.1.0
		 *
		 * @param array $query_arg Query vars used for the table listing.
		 * @param array $selector  Array of attributes used to display the filter selector.
		 *
		 * @return array
		 */
		protected function filter_type( array $query_arg, array $selector ): array {
			if ( empty( $selector['selected'] ) ) {
				return $query_arg;
			}

			$query_arg['meta_key']   = ebox_COUPON_META_KEY_TYPE; // phpcs:ignore
			$query_arg['meta_value'] = $selector['selected']; // phpcs:ignore

			return $query_arg;
		}

		/**
		 * Filter by redemptions (used or not used).
		 *
		 * @since 4.1.0
		 *
		 * @param array $query_arg Query vars used for the table listing.
		 * @param array $selector  Array of attributes used to display the filter selector.
		 *
		 * @return array.
		 */
		protected function filter_redemptions( array $query_arg, array $selector ): array {
			if ( empty( $selector['selected'] ) ) {
				return $query_arg;
			}

			if ( ! isset( $query_arg['meta_query'] ) ) {
				$query_arg['meta_query'] = array(); // phpcs:ignore
			}

			$query_arg['meta_query'][] = array(
				'key'     => ebox_COUPON_META_KEY_REDEMPTIONS,
				'compare' => self::COUPON_USED === $selector['selected'] ? '>' : '=',
				'value'   => 0,
				'type'    => 'NUMERIC',
			);

			return $query_arg;
		}

		/**
		 * Filter by state.
		 *
		 * @since 4.1.0
		 *
		 * @param array $query_arg Query vars used for the table listing.
		 * @param array $selector  Array of attributes used to display the filter selector.
		 *
		 * @return array.
		 */
		protected function filter_state( array $query_arg, array $selector ): array {
			if ( empty( $selector['selected'] ) ) {
				return $query_arg;
			}

			if ( ! isset( $query_arg['meta_query'] ) ) {
				$query_arg['meta_query'] = array(); // phpcs:ignore
			}

			$current_time = time();

			switch ( $selector['selected'] ) {
				case self::COUPON_FUTURE:
					$query_arg['meta_query'][] = array(
						'key'     => ebox_COUPON_META_KEY_START_DATE,
						'compare' => '>',
						'value'   => $current_time,
						'type'    => 'NUMERIC',
					);
					break;
				case self::COUPON_EXPIRED:
					$query_arg['meta_query'][] = array(
						'relation' => 'AND',
						array(
							'key'     => ebox_COUPON_META_KEY_END_DATE,
							'compare' => '>',
							'value'   => 0,
							'type'    => 'NUMERIC',
						),
						array(
							'key'     => ebox_COUPON_META_KEY_END_DATE,
							'compare' => '<=',
							'value'   => $current_time,
							'type'    => 'NUMERIC',
						),
					);
					break;
				case self::COUPON_ACTIVE:
					$query_arg['meta_query'][] = array(
						'relation' => 'OR',
						array(
							'relation' => 'AND',
							array(
								'key'     => ebox_COUPON_META_KEY_START_DATE,
								'compare' => '=',
								'value'   => 0,
							),
							array(
								'key'     => ebox_COUPON_META_KEY_END_DATE,
								'compare' => '=',
								'value'   => 0,
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
					break;
			}

			return $query_arg;
		}

		/**
		 * Filter by associated.
		 *
		 * @since 4.1.0
		 *
		 * @param array $query_arg Query vars used for the table listing.
		 * @param array $selector Array of attributes used to display the filter selector.
		 *
		 * @return array.
		 */
		protected function filter_associated( array $query_arg, array $selector ): array {
			if ( empty( $selector['selected'] ) ) {
				return $query_arg;
			}

			$meta_key = explode( '_', $selector['field_name'] )[0];

			if ( ! isset( $query_arg['meta_query'] ) ) {
				$query_arg['meta_query'] = array(); // phpcs:ignore
			}

			if ( self::ALL_VALUE === $selector['selected'] ) {
				$query_arg['meta_query'][] = array(
					'key'     => ebox_COUPON_META_KEY_PREFIX_APPLY_TO_ALL . $meta_key,
					'compare' => '=',
					'value'   => self::ON_VALUE,
				);
			} else {
				$query_arg['meta_query'][] = array(
					'key'     => "{$meta_key}_{$selector['selected']}",
					'compare' => 'EXISTS',
				);
			}

			return $query_arg;
		}

		/**
		 * Show a coupon code.
		 *
		 * @since 4.1.0
		 *
		 * @param int $post_id Post ID.
		 *
		 * @return void
		 */
		protected function column_code( int $post_id ): void {
			echo esc_html( ebox_get_setting( $post_id, ebox_COUPON_META_KEY_CODE ) );
		}

		/**
		 * Show a discount.
		 *
		 * @since 4.1.0
		 *
		 * @param int $post_id Post ID.
		 *
		 * @return void
		 */
		protected function column_discount( int $post_id ): void {
			$coupon_settings = ebox_get_setting( $post_id );

			if ( empty( $coupon_settings ) ) {
				return;
			}

			$html   = '';
			$type   = '';
			$amount = '';

			if ( isset( $coupon_settings[ ebox_COUPON_META_KEY_TYPE ] ) ) {
				$type = $coupon_settings[ ebox_COUPON_META_KEY_TYPE ];
			}

			if ( isset( $coupon_settings['amount'] ) ) {
				$amount = $coupon_settings['amount'];
			}

			if ( ebox_COUPON_TYPE_FLAT === $type ) {
				$html = ebox_get_price_formatted( $amount );
			} elseif ( ebox_COUPON_TYPE_PERCENTAGE === $type ) {
				$html = "{$amount}%";
			}

			echo esc_html( $html );
		}

		/**
		 * Show redemptions number.
		 *
		 * @since 4.1.0
		 *
		 * @param int $post_id Post ID.
		 *
		 * @return void
		 */
		protected function column_redemptions( int $post_id ): void {
			$redemptions     = (int) ebox_get_setting( $post_id, ebox_COUPON_META_KEY_REDEMPTIONS );
			$max_redemptions = (int) ebox_get_setting( $post_id, 'max_redemptions' );

			$html = $max_redemptions > 0
				? "$redemptions / {$max_redemptions}"
				: $redemptions;

			echo esc_html( $html );
		}

		/**
		 * Show a state (active, expired, future start date).
		 *
		 * @since 4.1.0
		 *
		 * @param int    $post_id Post ID.
		 * @param string $column_name Column Name.
		 *
		 * @return void
		 */
		protected function column_state( int $post_id, string $column_name ): void {
			$coupon_settings = ebox_get_setting( $post_id );

			if ( empty( $coupon_settings ) ) {
				return;
			}

			$current_time = time();

			$start_date = 0;
			$end_date   = 0;

			if ( isset( $coupon_settings[ ebox_COUPON_META_KEY_START_DATE ] ) ) {
				$start_date = (int) $coupon_settings[ ebox_COUPON_META_KEY_START_DATE ];
			}

			if ( isset( $coupon_settings[ ebox_COUPON_META_KEY_END_DATE ] ) ) {
				$end_date = (int) $coupon_settings[ ebox_COUPON_META_KEY_END_DATE ];
			}

			if ( $start_date > 0 && $current_time < $start_date ) {
				$filter_url = add_query_arg( $column_name, self::COUPON_FUTURE, $this->get_clean_filter_url() );
				$aria_label = esc_html__( 'Filter listing by future state', 'ebox' );

				$label  = esc_html__( 'Active from ', 'ebox' );
				$label .= esc_html(
					date_i18n(
						/** This filter is documented in includes/ld-misc-functions.php */
						apply_filters(
							'ebox_date_time_formats',
							get_option( 'date_format' ) . ' ' . get_option( 'time_format' )
						),
						strtotime( get_date_from_gmt( gmdate( 'Y-m-d H:i:s', $start_date ) ) )
					)
				);
			} elseif ( $end_date > 0 && $current_time >= $end_date ) {
				$filter_url = add_query_arg( $column_name, self::COUPON_EXPIRED, $this->get_clean_filter_url() );
				$aria_label = esc_html__( 'Filter listing by expired state', 'ebox' );
				$label      = esc_html__( 'Expired', 'ebox' );
			} else {
				$filter_url = add_query_arg( $column_name, self::COUPON_ACTIVE, $this->get_clean_filter_url() );
				$aria_label = esc_html__( 'Filter listing by active state', 'ebox' );
				$label      = esc_html__( 'Active', 'ebox' );
			}

			echo sprintf(
				'<a href="%s" aria-label="%s">%s</a>',
				esc_url( $filter_url ),
				esc_attr( $aria_label ),
				esc_html( $label )
			);
		}

		/**
		 * Show courses/teams.
		 *
		 * @since 4.1.0
		 *
		 * @param int    $post_id Post ID.
		 * @param string $column_name Column Name.
		 *
		 * @return void
		 */
		protected function column_associated( int $post_id, string $column_name ): void {
			$coupon_settings = ebox_get_setting( $post_id );

			if ( empty( $coupon_settings ) ) {
				return;
			}

			$query_arg = "{$column_name}_id";

			if ( ( isset( $coupon_settings[ ebox_COUPON_META_KEY_PREFIX_APPLY_TO_ALL . $column_name ] ) ) && ( self::ON_VALUE === $coupon_settings[ ebox_COUPON_META_KEY_PREFIX_APPLY_TO_ALL . $column_name ] ) ) {
				echo sprintf(
					'<a href="%s" aria-label="%s">%s</a>',
					esc_url( add_query_arg( $query_arg, self::ALL_VALUE, $this->get_clean_filter_url() ) ),
					esc_attr( esc_html__( 'Filter listing by all', 'ebox' ) ),
					esc_html__( 'All', 'ebox' )
				);
			} elseif ( ! empty( $coupon_settings[ $column_name ] ) ) {
				$items = array();

				foreach ( $coupon_settings[ $column_name ] as $id ) {
					$filter_url   = add_query_arg( $query_arg, $id, $this->get_clean_filter_url() );
					$course_title = get_the_title( $id );

					$items[] = sprintf(
						'<a href="%s" aria-label="%s">%s</a>',
						esc_url( $filter_url ),
						esc_attr( $this->get_aria_label_for_post( $id, 'filter' ) ),
						esc_html( $course_title )
					);
				}

				echo implode( ', ', $items ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}
	}

	new ebox_Admin_Coupons_Listing();
}
