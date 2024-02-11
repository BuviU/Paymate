<?php
/**
 * Deprecated functions from LD 3.1.2
 * The functions will be removed in a later version.
 *
 * @package ebox\Deprecated
 * @since 3.1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'ebox_get_prior_installed_version' ) ) {
	/**
	 * Gets the prior installed version.
	 *
	 * @deprecated 3.1.2 Use {@see 'LDLMS_Transients::purge_all'} instead.
	 *
	 * @return mixed
	 */
	function ebox_get_prior_installed_version() {
		if ( function_exists( '_deprecated_function' ) ) {
			_deprecated_function( __FUNCTION__, '3.1.2', 'ebox_data_upgrades_setting()' );
		}

		return ebox_data_upgrades_setting( 'prior_version' );
	}
}
