<?php
/**
 * ebox `[ld_registration]` shortcode processing.
 *
 * @since 3.6.0
 * @package ebox\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Builds the `[ld_registration]` shortcode output.
 *
 * @global boolean $ebox_shortcode_used
 *
 * @since 3.6.0
 *
 * @param array  $attr {
 *    An array of shortcode attributes.
 *
 *    @type string $width Width of the registration form. Default empty.
 * }.
 * @param string $content The shortcode content. Default empty.
 * @param string $shortcode_slug The shortcode slug. Default 'ld_registration'.
 *
 * @return string The `ld_registration` shortcode output.
 */
function ebox_registration( $attr = array(), $content = '', $shortcode_slug = 'ld_registration' ) {

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

	ebox_registration_output( $attr );

	$content .= ebox_ob_get_clean( $level );
	return $content;

}

add_shortcode( 'ld_registration', 'ebox_registration', 10, 3 );
