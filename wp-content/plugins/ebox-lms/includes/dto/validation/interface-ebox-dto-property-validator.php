<?php
/**
 * This interface for a DTO property validator.
 *
 * @since 4.5.0
 *
 * @package ebox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! interface_exists( 'ebox_DTO_Property_Validator' ) ) {
	/**
	 * DTO property validator interface.
	 *
	 * @since 4.5.0
	 */
	interface ebox_DTO_Property_Validator {
		/**
		 * Validates a property and returns a validation result.
		 *
		 * @since 4.5.0
		 *
		 * @param mixed $value Value to validate.
		 *
		 * @return ebox_DTO_Property_Validation_Result
		 */
		public function validate( $value ): ebox_DTO_Property_Validation_Result;
	}
}
