<?php
/**
 * ebox payment gateways.
 *
 * @since 4.5.0
 *
 * @package ebox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const ebox_GATEWAYS_PATH = ebox_LMS_PLUGIN_DIR . 'includes/payments/gateways/';
require_once ebox_GATEWAYS_PATH . 'class-ebox-payment-gateway.php';

// Requires all gateways. Please don't forget to create an instance of the gateways below.
require_once ebox_GATEWAYS_PATH . 'class-ebox-unknown-gateway.php';
require_once ebox_GATEWAYS_PATH . 'class-ebox-paypal-ipn-gateway.php';
require_once ebox_GATEWAYS_PATH . 'class-ebox-stripe-gateway.php';
require_once ebox_GATEWAYS_PATH . 'class-ebox-razorpay-gateway.php';

add_action(
	'init',
	function () {
		/**
		 * Filters the list of payment gateways.
		 *
		 * @since 4.5.0
		 *
		 * @param ebox_Payment_Gateway[] $gateways List of payment gateway instances.
		 *
		 * @return ebox_Payment_Gateway[] List of payment gateway instances.
		 */
		$gateways = apply_filters(
			'ebox_payment_gateways',
			array(
				// gateways instances initialization.
				new ebox_Unknown_Gateway(),
				new ebox_Paypal_IPN_Gateway(),
				new ebox_Stripe_Gateway(),
				new ebox_Razorpay_Gateway(),
			)
		);

		foreach ( $gateways as $gateway ) {
			if ( ! $gateway instanceof ebox_Payment_Gateway ) {
				continue;
			}

			$gateway->init();
		}
	}
);
