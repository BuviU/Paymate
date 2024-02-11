<?php
/**
 * This class provides the easy way to operate transaction meta.
 *
 * @since 4.5.0
 *
 * @package ebox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ebox_Transaction_Meta_DTO' ) && class_exists( 'ebox_DTO' ) ) {
	/**
	 * Transaction meta DTO class. Used to map the transaction metadata.
	 *
	 * @since 4.5.0
	 */
	class ebox_Transaction_Meta_DTO extends ebox_DTO {
		private const VALID_PRICE_TYPES = array( ebox_PRICE_TYPE_PAYNOW, ebox_PRICE_TYPE_SUBSCRIBE );

		/**
		 * Properties are being cast to the specified type on construction.
		 *
		 * @since 4.5.0
		 *
		 * @var array<string, string>
		 */
		protected $cast = array(
			'ld_payment_processor' => 'string',
			'price_type'           => 'string',
			'pricing_info'         => ebox_Pricing_DTO::class,
			'gateway_transaction'  => ebox_Transaction_Gateway_Transaction_DTO::class,
			'has_trial'            => 'bool',
			'has_free_trial'       => 'bool',
		);

		/**
		 * Payment gateway name.
		 *
		 * @since 4.5.0
		 *
		 * @var string
		 */
		public $ld_payment_processor = '';

		/**
		 * Type. Valid values are ebox_PRICE_TYPE_PAYNOW and ebox_PRICE_TYPE_SUBSCRIBE.
		 *
		 * @since 4.5.0
		 *
		 * @var string
		 */
		public $price_type = '';

		/**
		 * Pricing DTO.
		 *
		 * @since 4.5.0
		 *
		 * @var ebox_Pricing_DTO
		 */
		public $pricing_info;

		/**
		 * Gateway transaction DTO.
		 *
		 * @since 4.5.0
		 *
		 * @var ebox_Transaction_Gateway_Transaction_DTO
		 */
		public $gateway_transaction;

		/**
		 * If it has a trial or not.
		 *
		 * @since 4.5.0
		 *
		 * @var bool
		 */
		public $has_trial = false;

		/**
		 * If it has a free trial or not.
		 *
		 * @since 4.5.0
		 *
		 * @var bool
		 */
		public $has_free_trial = false;

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

			if ( ! empty( $this->ld_payment_processor ) ) {
				$validators['ld_payment_processor'] = array(
					new ebox_DTO_Property_Validator_Possible_Values(
						array_keys( ebox_Payment_Gateway::get_select_list() )
					),
				);
			}

			if ( ! empty( $this->price_type ) ) {
				$validators['price_type'] = array(
					new ebox_DTO_Property_Validator_Possible_Values( self::VALID_PRICE_TYPES ),
				);
			}

			return $validators;
		}
	}
}
