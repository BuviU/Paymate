<?php
/**
 * ebox `[ld_reset_password]` shortcode processing.
 *
 * @since 4.4.0
 * @package ebox\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Builds the `[ld_reset_password]` shortcode output.
 *
 * @global boolean $ebox_shortcode_used
 *
 * @since 4.4.0
 *
 * @param array  $attr {
 *    An array of shortcode attributes.
 *
 *    @type string $width Width of the reset password form. Default empty.
 * }.
 * @param string $content The shortcode content. Default empty.
 * @param string $shortcode_slug The shortcode slug. Default 'ld_reset_password'.
 *
 * @return string The `ld_reset_password` shortcode output.
 */
function ebox_reset_password( $attr = array(), $content = '', $shortcode_slug = 'ld_reset_password' ) {
	global $ebox_shortcode_used;
	$ebox_shortcode_used = true;
	if ( ! is_array( $attr ) ) {
		$attr = array();
	}
	$attr = shortcode_atts(
		array(
			'width' => '',
		),
		$attr
	);
	/** This filter is documented in includes/shortcodes/ld_course_resume.php */
	$attr = apply_filters( 'ebox_shortcode_atts', $attr, $shortcode_slug );

	$level = ob_get_level();

	ob_start();
	ebox_reset_password_output( $attr );
	$content .= ebox_ob_get_clean( $level );
	return $content;
}
add_shortcode( 'ld_reset_password', 'ebox_reset_password' );
