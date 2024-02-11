<?php
/**
 * Deprecated functions from LD 4.1.0
 * The functions will be removed in a later version.
 *
 * @package ebox\Deprecated
 * @since 4.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'ebox_30_the_currency_symbol' ) ) {


	/**
	 * Outputs the currency symbol.
	 *
	 * @deprecated 4.1.0 Please use {@see 'ebox_the_currency_symbol'} instead.
	 *
	 * @since 3.0.0
	 */
	function ebox_30_the_currency_symbol() {
		if ( function_exists( '_deprecated_function' ) ) {
			_deprecated_function( __FUNCTION__, '4.1.0', 'ebox_the_currency_symbol' );
		}

		echo wp_kses_post( ebox_30_get_currency_symbol() );
	}
}

if ( ! function_exists( 'ebox_30_get_currency_symbol' ) ) {
	/**
	 * Gets the currency symbol.
	 *
	 * @deprecated 4.1.0 Please use {@see 'ebox_get_currency_symbol'} instead.
	 *
	 * @since 3.0.0
	 *
	 * @return string|false Returns currency symbol.
	 */
	function ebox_30_get_currency_symbol() {
		if ( function_exists( '_deprecated_function' ) ) {
			_deprecated_function( __FUNCTION__, '4.1.0', 'ebox_get_currency_symbol' );
		}

		$currency = '';

		$options         = get_option( 'ebox_cpt_options' );
		$stripe_settings = get_option( 'ebox_stripe_settings' );

		if ( class_exists( 'ebox_Settings_Section' ) ) {
			$paypal_enabled          = ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_PayPal', 'enabled' );
			$paypal_currency         = ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_PayPal', 'paypal_currency' );
			$stripe_connect_enabled  = ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_Stripe_Connect', 'enabled' );
			$stripe_connect_currency = ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_Stripe_Connect', 'currency' );
		}

		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		if ( is_plugin_active( 'ebox-stripe/ebox-stripe.php' ) && ! empty( $stripe_settings ) && ! empty( $stripe_settings['currency'] ) ) {
			$currency = $stripe_settings['currency'];
		} elseif ( isset( $paypal_enabled ) && $paypal_enabled && ! empty( $paypal_currency ) ) {
			$currency = $paypal_currency;
		} elseif ( isset( $stripe_connect_enabled ) && $stripe_connect_enabled && ! empty( $stripe_connect_currency ) ) {
			$currency = $stripe_connect_currency;
		} elseif ( isset( $options['modules'] ) && isset( $options['modules']['ebox-courses_options'] ) && isset( $options['modules']['ebox-courses_options']['ebox-courses_paypal_currency'] ) ) {
			$currency = $options['modules']['ebox-courses_options']['ebox-courses_paypal_currency'];
		}

		if ( class_exists( 'NumberFormatter' ) ) {
			$locale        = get_locale();
			$number_format = new NumberFormatter( $locale . '@currency=' . $currency, NumberFormatter::CURRENCY );
			$currency      = $number_format->getSymbol( NumberFormatter::CURRENCY_SYMBOL );
		}

		return $currency;

	}
}
