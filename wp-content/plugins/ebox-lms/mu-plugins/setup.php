<?php
/**
 * Must use plugins.
 *
 * @package ebox
 */

/**
 * Copy our Multisite support file(s) to the /wp-content/mu-plugins directory.
 */
if ( is_multisite() ) {
	$wpmu_plugin_dir = ( defined( 'WPMU_PLUGIN_DIR' ) && defined( 'WPMU_PLUGIN_URL' ) ) ? WPMU_PLUGIN_DIR : trailingslashit( WP_CONTENT_DIR ) . 'mu-plugins'; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	if ( is_writable( $wpmu_plugin_dir ) ) {
		$dest_file = trailingslashit( $wpmu_plugin_dir ) . 'ebox-multisite.php'; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
		if ( ! file_exists( $dest_file ) ) {
			$source_file = trailingslashit( ebox_LMS_PLUGIN_DIR ) . 'mu-plugins/ebox-multisite.php'; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
			if ( file_exists( $source_file ) ) {
				copy( $source_file, $dest_file );
			}
		}
	}
}

/**
 * Install the License Manager.
 */
if ( file_exists( trailingslashit( ebox_LMS_PLUGIN_DIR ) . 'mu-plugins/ebox-hub.zip' ) ) {
	$ebox_hub_unzip_dir = trailingslashit( ebox_LMS_PLUGIN_DIR ) . 'mu-plugins/_tmp';

	if ( file_exists( $ebox_hub_unzip_dir ) ) {
		ebox_recursive_rmdir( $ebox_hub_unzip_dir );
	}

	WP_Filesystem();
	$ebox_unzip_ret = unzip_file( trailingslashit( ebox_LMS_PLUGIN_DIR ) . 'mu-plugins/ebox-hub.zip', $ebox_hub_unzip_dir );

	if ( is_wp_error( $ebox_unzip_ret ) ) {
		WP_DEBUG && error_log( 'Failed to unzip the ebox license management plugin: ' . $ebox_unzip_ret->get_error_message() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	}

	$ebox_hub_install_file = trailingslashit( $ebox_hub_unzip_dir ) . 'ebox-hub/install.php';

	if ( file_exists( $ebox_hub_install_file ) ) {
		include $ebox_hub_install_file;

		// try to activate the hub plugin.
		if ( is_file( trailingslashit( WP_PLUGIN_DIR ) . 'ebox-hub/ebox-hub.php' ) ) {
			activate_plugin(
				'ebox-hub/ebox-hub.php',
				'',
				is_plugin_active_for_network( ebox_LMS_PLUGIN_KEY ),
				true
			);
		}
	}

	ebox_recursive_rmdir( $ebox_hub_unzip_dir );
}
