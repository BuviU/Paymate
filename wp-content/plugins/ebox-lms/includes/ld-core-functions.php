<?php
/**
 * Core utility functions
 *
 * @since 4.4.0
 *
 * @package ebox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check if ebox cloud is enabled.
 *
 * @since 4.4.0
 *
 * @return bool
 */
function ebox_cloud_is_enabled(): bool {
	return defined( 'StellarWP\eboxCloud\PLUGIN_VERSION' );
}
