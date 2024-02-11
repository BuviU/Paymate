<?php
/**
 * This class provides the easy way to operate data.
 *
 * @since 4.5.0
 *
 * @package ebox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ebox_Transaction_Coupon_DTO' ) && class_exists( 'ebox_DTO' ) ) {
	/**
	 * Coupon transaction DTO class. Used to map the transaction coupon data.
	 *
	 * @since 4.5.0
	 */
	class ebox_Transaction_Coupon_DTO extends ebox_DTO {
		private const VALID_TYPES = array( ebox_COUPON_TYPE_FLAT, ebox_COUPON_TYPE_PERCENTAGE );

		/**
		 * Properties are being cast to the specified type on construction.
		 *
		 * @since 4.5.0
		 *
		 * @var array<string, string>
		 */
		protected $cast = array(
			'coupon_id' => 'int',
			'code'      => 'string',
			'type'      => 'string',
			'amount'    => 'float',
		);

		/**
		 * Coupon ID.
		 *
		 * @since 4.5.0
		 *
		 * @var int
		 */
		public $coupon_id = 0;

		/**
		 * Code.
		 *
		 * @since 4.5.0
		 *
		 * @var string
		 */
		public $code = '';

		/**
		 * Type. Valid values are ebox_COUPON_TYPE_FLAT and ebox_COUPON_TYPE_PERCENTAGE.
		 *
		 * @since 4.5.0
		 *
		 * @var string
		 */
		public $type = '';

		/**
		 * Amount.
		 *
		 * @since 4.5.0
		 *
		 * @var float
		 */
		public $amount = 0.0;

		/**
		 * Validates properties on construction based on validators.
		 * Key is a property name, value is an array of validator objects.
		 *
		 * @since 4.5.0
		 *
		 * @return array<string,mixed>
		 */
		protected function get_validators(): array {
			$validators = parent::get_validators();

			if ( ! empty( $this->type ) ) {
				$validators['type'] = array(
					new ebox_DTO_Property_Validator_Possible_Values( self::VALID_TYPES ),
				);
			}

			return $validators;
		}
	}
}
