<?php
/**
 * ebox Widget Alert Message.
 *
 * @since 4.0.0
 * @package ebox\Widgets
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Displays alert message for ebox related Appearance->Widgets
 *
 * @since 4.0.0
 * @package ebox\Widgets
 */
function ebox_replace_widgets_alert() {
	echo '<p><strong>';
	echo esc_html__( 'Notice: This widget may no longer be supported in future versions of ebox or WordPress, please use a block instead.', 'ebox' );
	echo '</strong></p>';
}
