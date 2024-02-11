<?php
/**
 * ebox Email Settings Field.
 *
 * @since 3.0.0
 * @package ebox\Settings\Field
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Fields_Text' ) ) && ( ! class_exists( 'ebox_Settings_Fields_Email' ) ) ) {

	/**
	 * Class ebox Email Settings Field.
	 *
	 * @since 3.0.0
	 * @uses ebox_Settings_Fields
	 */
	class ebox_Settings_Fields_Email extends ebox_Settings_Fields_Text {

		/**
		 * Public constructor for class
		 *
		 * @since 3.0.0
		 */
		public function __construct() {
			$this->field_type = 'email';

			parent::__construct();
		}

		/**
		 * Validate field
		 *
		 * @since 3.0.0
		 *
		 * @param mixed  $val Value to validate.
		 * @param string $key Key of value being validated.
		 * @param array  $args Array of field args.
		 *
		 * @return integer value.
		 */
		public function validate_section_field( $val, $key, $args = array() ) {
			if ( ( isset( $args['field']['type'] ) ) && ( $args['field']['type'] === $this->field_type ) ) {
				if ( ! empty( $val ) ) {
					$val = sanitize_email( $val );
				} else {
					$val = '';
				}
				return $val;
			}

			return false;
		}

		// End of functions.
	}
}
add_action(
	'ebox_settings_sections_fields_init',
	function() {
		ebox_Settings_Fields_Text::add_field_instance( 'email' );
	}
);
