<?php
/**
 * Functions for uninstall ebox
 *
 *
 * @package ebox
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . 'vendor-prefixed/autoload.php';

/**
 * Remove our Multisite support file(s) to the /wp-content/mu-plugins directory.
 */
$ebox_wpmu_plugin_dir = ( defined( 'WPMU_PLUGIN_DIR' ) && defined( 'WPMU_PLUGIN_URL' ) ) ? WPMU_PLUGIN_DIR : trailingslashit( WP_CONTENT_DIR ) . 'mu-plugins';
if ( is_writable( $ebox_wpmu_plugin_dir ) ) {
	$ebox_wpmu_plugin_dir_file = trailingslashit( $ebox_wpmu_plugin_dir ) . 'ebox-multisite.php';
	if ( file_exists( $ebox_wpmu_plugin_dir_file ) ) {
		unlink( $ebox_wpmu_plugin_dir_file );
	}
}

/**
 * Fires on plugin uninstall.
 */
do_action( 'ebox_uninstall' );

/**
 * Uninstalls Telemetry.
 *
 */
StellarWP\ebox\StellarWP\Telemetry\Uninstall::run( 'ebox' );
