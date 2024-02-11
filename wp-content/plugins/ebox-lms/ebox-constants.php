<?php
/**
 * ebox non-scalar constants
 *
 * @package ebox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'ebox_LICENSE_PANEL_SHOW' ) ) {
	$ebox_show_license_panel = ! ebox_is_ebox_hub_active();

	/**
	 * Define ebox LMS - Show license panel.
	 *
	 *
	 * @var bool $value {
	 *    Only one of the following values.
	 *    @type bool true  License panel/tab will be visible. Default.
	 *    @type bool false License panel/tab will not be visible.
	 * }
	 */
	define( 'ebox_LICENSE_PANEL_SHOW', $ebox_show_license_panel );
}
