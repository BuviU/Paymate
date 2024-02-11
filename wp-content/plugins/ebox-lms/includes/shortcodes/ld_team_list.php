<?php
/**
 * ebox `[ld_team_list]` shortcode processing.
 *
 * @since 2.1.0
 * @package ebox\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Builds the `[ld_team_list]` shortcode output.
 *
 * @global boolean $ebox_shortcode_used
 *
 * @since 3.1.7
 *
 * @param array  $attr {
 *    An array of shortcode attributes.
 *
 *    Default empty array {@see 'ld_course_list'}.
 * }.
 * @param string $content The shortcode content. Default empty.
 * @param string $shortcode_slug The shortcode slug. Default 'ld_team_list'.
 *
 * @return string The `ld_topic_list` shortcode output.
 */
function ld_team_list( $attr = array(), $content = '', $shortcode_slug = 'ld_team_list' ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
	global $ebox_shortcode_used;
	$ebox_shortcode_used = true;

	if ( ! is_array( $attr ) ) {
		$attr = array();
	}

	$attr['post_type'] = ebox_get_post_type_slug( 'team' );

	/** This filter is documented in includes/shortcodes/ld_course_resume.php */
	$attr = apply_filters( 'ebox_shortcode_atts', $attr, $shortcode_slug );

	return ld_course_list( $attr );

}

add_shortcode( 'ld_team_list', 'ld_team_list', 10, 3 );
