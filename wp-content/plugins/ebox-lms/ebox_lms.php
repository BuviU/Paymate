<?php
/**
 * Plugin Name: Ebox LMS by Arttricks
 * Plugin URI: https://arttricks.com.au/
 * Description: Transform your WordPress website into a learning management system with the Ebox LMS Plugin.
 * Version: Version 1.0.0
 * Author: Arttricks
 * Author URI: https://arttricks.com.au/
 * Text Domain: arttricks
 * Domain Path: /languages/
 *
 * 
 *
 * @package ebox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
require_once plugin_dir_path( __FILE__ ) . 'vendor-prefixed/autoload.php';

use ebox\Core\Container;
use StellarWP\ebox\StellarWP\Telemetry\Config;
use StellarWP\ebox\StellarWP\Telemetry\Core as Telemetry;
use StellarWP\ebox\StellarWP\DB\DB;

// CONSTANTS.


define( 'ebox_VERSION', '4.5.3' );

if ( ! defined( 'ebox_LMS_PLUGIN_DIR' ) ) {
	/**
	 * Define ebox LMS - Set the plugin install path.
	 *
	 * Will be set based on the WordPress define `WP_PLUGIN_DIR`.
	 *
	 *
	 * Directory path to plugin install directory.
	 */
	define( 'ebox_LMS_PLUGIN_DIR', trailingslashit( str_replace( '\\', '/', WP_PLUGIN_DIR ) . '/' . basename( dirname( __FILE__ ) ) ) );
}

if ( ! defined( 'ebox_LMS_PLUGIN_URL' ) ) {
	$ebox_plugin_url = trailingslashit( WP_PLUGIN_URL . '/' . basename( dirname( __FILE__ ) ) );
	$ebox_plugin_url = str_replace( array( 'https://', 'http://' ), array( '//', '//' ), $ebox_plugin_url );

	/**
	 * Define ebox LMS - Set the plugin relative URL.
	 *
	 * Will be set based on the WordPress define `WP_PLUGIN_URL`.
	 *
	 *
	 * URL to plugin install directory.
	 */
	define( 'ebox_LMS_PLUGIN_URL', $ebox_plugin_url );
}

if ( ! defined( 'ebox_LMS_PLUGIN_KEY' ) ) {
	$ebox_plugin_dir = ebox_LMS_PLUGIN_DIR;
	$ebox_plugin_dir = basename( $ebox_plugin_dir ) . '/' . basename( __FILE__ );

	/**
	 * Define ebox LMS - Set the plugin key.
	 *
	 * This define is the plugin directory and filename.
	 * directory.
	 *
	 *
	 * Default value is `ebox-lms/ebox_lms.php`.
	 */
	define( 'ebox_LMS_PLUGIN_KEY', $ebox_plugin_dir );
}

// Defining other scalar constants.
require_once __DIR__ . '/ebox-scalar-constants.php';

/**
 * Configures packages.
 *
 */
add_action(
	'plugins_loaded',
	function () {
		// Telemetry.

		$telemetry_server_url = defined( 'ebox_TELEMETRY_URL' ) && ! empty( ebox_TELEMETRY_URL )
			? ebox_TELEMETRY_URL
			: 'https://telemetry.stellarwp.com/api/v1';

		Config::set_container( new Container() );
		Config::set_server_url( $telemetry_server_url );
		Config::set_hook_prefix( 'ebox' );
		Config::set_stellar_slug( 'ebox' );

		Telemetry::instance()->init( __FILE__ );

		// DB.

		DB::init();
	},
	0
);

/**
 * Action Scheduler
 */
add_action(
	'plugins_loaded',
	static function () {
		require_once __DIR__ . '/includes/lib/action-scheduler/action-scheduler.php';
	},
	-10
);

add_action(
	'plugins_loaded',
	static function() {
		require_once __DIR__ . '/ebox-includes.php';
		require_once __DIR__ . '/ebox-constants.php';
		require_once __DIR__ . '/ebox-globals.php';
	},
	0
);

// Activation and deactivation hooks.

register_activation_hook(
	__FILE__,
	function () {
		// Save a flag in the DB to allow later activation tasks (legacy stuff).
		update_option( 'ebox_activation', true );
	}
);

register_deactivation_hook( __FILE__, 'ebox_deactivated' );

/**
 * Deactivate ebox LMS.
 *
 *
 * @return void
 */
function ebox_deactivated() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	/**
	 * Fires on ebox plugin deactivation.
	 *
	 */
	do_action( 'ebox_deactivated' );
}
