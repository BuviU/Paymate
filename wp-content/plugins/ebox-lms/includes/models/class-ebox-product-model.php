<?php
/**
 * This class provides the easy way to operate products (courses and teams).
 *
 * @since 4.5.0
 *
 * @package ebox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ebox_Product_Model' ) && class_exists( 'ebox_Model' ) ) {
	/**
	 * Product model class.
	 *
	 * @since 4.5.0
	 */
	class ebox_Product_Model extends ebox_Model {
		/**
		 * Returns allowed post types.
		 *
		 * @since 4.5.0
		 *
		 * @return string[]
		 */
		public static function get_allowed_post_types(): array {
			return array(
				LDLMS_Post_Types::get_post_type_slug( LDLMS_Post_Types::COURSE ),
				LDLMS_Post_Types::get_post_type_slug( LDLMS_Post_Types::GROUP ),
			);
		}

		/**
		 * Returns a product type (buy now, subscription, free, open, closed, etc).
		 *
		 * @since 4.5.0
		 *
		 * @return string
		 */
		public function get_pricing_type(): string {
			/**
			 * Filters product pricing type.
			 *
			 * @since 4.5.0
			 *
			 * @param string                  $pricing_type Product pricing type.
			 * @param ebox_Product_Model $product      Product model.
			 *
			 * @return string Product pricing type.
			 */
			return apply_filters(
				'ebox_model_product_pricing_type',
				$this->get_pricing_settings()['type'] ?? '',
				$this
			);
		}

		/**
		 * Returns a product type label. Usually "Course" or "Team".
		 *
		 * @since 4.5.0
		 *
		 * @return string
		 */
		public function get_type_label(): string {
			/**
			 * Filters product type label.
			 *
			 * @since 4.5.0
			 *
			 * @param string                  $type_label Product type label. Course/Team.
			 * @param ebox_Product_Model $product    Product model.
			 *
			 * @return string Product type label.
			 */
			return apply_filters(
				'ebox_model_product_type_label',
				ebox_Custom_Label::get_label(
					LDLMS_Post_Types::get_post_type_key( $this->post->post_type )
				),
				$this
			);
		}

		/**
		 * Returns a pricing DTO.
		 *
		 * @since 4.5.0
		 *
		 * @param WP_User|null $user If the special pricing for a user is needed.
		 *
		 * @throws ebox_DTO_Validation_Exception If DTO is not valid.
		 *
		 * @return ebox_Pricing_DTO
		 */
		public function get_pricing( WP_User $user = null ): ebox_Pricing_DTO {
			$pricing_settings = $this->get_pricing_settings( $user );

			$pricing = array(
				'currency'              => ebox_get_currency_code(),
				'price'                 => isset( $pricing_settings['price'] )
					? ebox_get_price_as_float( strval( $pricing_settings['price'] ) )
					: 0,
				'recurring_times'       => $pricing_settings['repeats'] ?? 0,
				'duration_value'        => $pricing_settings['interval'] ?? 0,
				'duration_length'       => $pricing_settings['frequency_raw'] ?? '',
				'trial_price'           => isset( $pricing_settings['trial_price'] )
					? ebox_get_price_as_float( strval( $pricing_settings['trial_price'] ) )
					: 0,
				'trial_duration_value'  => $pricing_settings['trial_interval'] ?? 0,
				'trial_duration_length' => $pricing_settings['trial_frequency_raw'] ?? '',
			);

			/**
			 * Filters product pricing.
			 *
			 * @since 4.5.0
			 *
			 * @param ebox_Pricing_DTO   $pricing_dto Product Pricing DTO.
			 * @param ebox_Product_Model $product     Product model.
			 *
			 * @return ebox_Pricing_DTO Product pricing DTO.
			 */
			return apply_filters(
				'ebox_model_product_pricing',
				new ebox_Pricing_DTO( $pricing ),
				$this
			);
		}

		/**
		 * Returns true if a user has access to this product, false otherwise.
		 *
		 * @since 4.5.0
		 *
		 * @param WP_User $user WP_User object.
		 *
		 * @return bool
		 */
		public function user_has_access( WP_User $user ): bool {
			$has_access = false;

			if ( $user->ID > 0 ) {
				if ( ebox_is_course_post( $this->post ) ) {
					$has_access = ebox_lms_has_access( $this->post->ID, $user->ID );
				} elseif ( ebox_is_team_post( $this->post ) ) {
					$has_access = ebox_is_user_in_team( $user->ID, $this->post->ID );
				}
			}

			/**
			 * Filters whether a user has access to a product.
			 *
			 * @since 4.5.0
			 *
			 * @param bool                    $has_access True if a user has access, false otherwise.
			 * @param ebox_Product_Model $product    Product model.
			 * @param WP_User                 $user       User.
			 *
			 * @return bool True if a user has access, false otherwise.
			 */
			return apply_filters( 'ebox_model_product_user_has_access', $has_access, $this, $user );
		}

		/**
		 * Adds access for a user.
		 *
		 * @since 4.5.0
		 *
		 * @param WP_User $user WP_User object.
		 *
		 * @return bool Returns true if it successfully enrolled a user, false otherwise.
		 */
		public function enroll( WP_User $user ): bool {
			$enrolled = false;

			if ( ebox_is_course_post( $this->post ) ) {
				$enrolled = ld_update_course_access( $user->ID, $this->post->ID );
			} elseif ( ebox_is_team_post( $this->post ) ) {
				$enrolled = ld_update_team_access( $user->ID, $this->post->ID );
			}

			/**
			 * Filters whether a user was enrolled to a product.
			 *
			 * @since 4.5.0
			 *
			 * @param bool                    $enrolled True if a user was enrolled, false otherwise.
			 * @param ebox_Product_Model $product  Product model.
			 * @param WP_User                 $user     User.
			 *
			 * @return bool True if a user was enrolled, false otherwise.
			 */
			return apply_filters( 'ebox_model_product_user_enrolled', $enrolled, $this, $user );
		}

		/**
		 * Removes access for a user.
		 *
		 * @since 4.5.0
		 *
		 * @param WP_User $user WP_User object.
		 *
		 * @return bool Returns true if it successfully unenrolled a user, false otherwise.
		 */
		public function unenroll( WP_User $user ): bool {
			$unenrolled = false;

			if ( ebox_is_course_post( $this->post ) ) {
				$unenrolled = ld_update_course_access( $user->ID, $this->post->ID, true );
			} elseif ( ebox_is_team_post( $this->post ) ) {
				$unenrolled = ld_update_team_access( $user->ID, $this->post->ID, true );
			}

			/**
			 * Filters whether a user was unenrolled from a product.
			 *
			 * @since 4.5.0
			 *
			 * @param bool                    $unenrolled True if a user was unenrolled, false otherwise.
			 * @param ebox_Product_Model $product    Product model.
			 * @param WP_User                 $user       User.
			 *
			 * @return bool True if a user was unenrolled, false otherwise.
			 */
			return apply_filters( 'ebox_model_product_user_unenrolled', $unenrolled, $this, $user );
		}

		/**
		 * Returns formatted post pricing data.
		 *
		 * @since 4.5.0
		 *
		 * @param WP_User|null $user If the special pricing for a user is needed.
		 *
		 * @return array{
		 *     type?: string,
		 *     price?: float|string,
		 *     interval?: int,
		 *     frequency?: string,
		 *     frequency_raw?: string,
		 *     repeats?: int,
		 *     repeat_frequency?: string,
		 *     trial_price?: float,
		 *     trial_interval?: int,
		 *     trial_frequency?: string,
		 *     trial_frequency_raw?: string
		 * }
		 */
		private function get_pricing_settings( WP_User $user = null ): array {
			$pricing_settings = array();

			$user_id = $user ? $user->ID : 0;

			if ( ebox_is_course_post( $this->post ) ) {
				$pricing_settings = ebox_get_course_price( $this->post, $user_id );
			} elseif ( ebox_is_team_post( $this->post ) ) {
				$pricing_settings = ebox_get_team_price( $this->post, $user_id );
			}

			return $pricing_settings;
		}
	}
}
