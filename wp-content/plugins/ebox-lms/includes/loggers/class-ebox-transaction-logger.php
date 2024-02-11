<?php
/**
 * This class provides an easy way to log everything.
 *
 * @since 4.5.0
 *
 * @package ebox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ebox_Transaction_Logger' ) && class_exists( 'ebox_Logger' ) ) {
	/**
	 * Transaction logger class.
	 *
	 * @since 4.5.0
	 */
	class ebox_Transaction_Logger extends ebox_Logger {
		/**
		 * Gateway.
		 *
		 * @since 4.5.0
		 *
		 * @var ebox_Payment_Gateway $gateway
		 */
		private $gateway;

		/**
		 * Logger constructor.
		 *
		 * @since 4.5.0
		 *
		 * @param ebox_Payment_Gateway $gateway Gateway.
		 *
		 * @return void
		 */
		public function __construct( ebox_Payment_Gateway $gateway ) {
			$this->gateway = $gateway;
		}

		/**
		 * Returns the label.
		 *
		 * @since 4.5.0
		 *
		 * @return string
		 */
		public function get_label(): string {
			return $this->gateway->get_label() . ' ' . ebox_Custom_Label::get_label( 'transactions' );
		}

		/**
		 * Returns the name.
		 *
		 * @since 4.5.0
		 *
		 * @return string
		 */
		public function get_name(): string {
			return $this->gateway->get_name() . '_transactions';
		}
	}
}
