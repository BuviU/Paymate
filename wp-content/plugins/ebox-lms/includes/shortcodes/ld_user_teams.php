<?php
/**
 * ebox `[user_teams]` shortcode processing.
 *
 * @since 2.1.0
 *
 * @package ebox\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Builds the `[user_teams]` shortcode output.
 *
 * @since 2.1.0
 *
 * @global boolean $ebox_shortcode_used
 *
 * @param array  $attr {
 *    An array of shortcode attributes.
 *
 *    @type int $user_id User ID. Default to current user ID.
 * }
 * @param string $content The shortcode content. Default empty.
 * @param string $shortcode_slug The shortcode slug. Default 'user_teams'.
 *
 * @return string The output for `user_teams` shortcode.
 */
function ebox_user_teams( $attr = array(), $content = '', $shortcode_slug = 'user_teams' ) {

	global $ebox_shortcode_used;
	$ebox_shortcode_used = true;

	$shortcode_atts = shortcode_atts(
		array(
			'user_id' => '',
		),
		$attr
	);

	/** This filter is documented in includes/shortcodes/ld_course_resume.php */
	$shortcode_atts = apply_filters( 'ebox_shortcode_atts', $shortcode_atts, $shortcode_slug );

	if ( empty( $shortcode_atts['user_id'] ) ) {
		$shortcode_atts['user_id'] = get_current_user_id();
	}

	if ( ! empty( $user_id ) ) {
		return '';
	}

	$admin_teams     = ebox_get_administrators_team_ids( $shortcode_atts['user_id'] );
	$user_teams      = ebox_get_users_team_ids( $shortcode_atts['user_id'] );
	$has_admin_teams = ! empty( $admin_teams ) && is_array( $admin_teams ) && ! empty( $admin_teams[0] );
	$has_user_teams  = ! empty( $user_teams ) && is_array( $user_teams ) && ! empty( $user_teams[0] );

	if ( ! $has_admin_teams && ! $has_user_teams ) {
		return '';
	}

	return ebox_LMS::get_template(
		'user_teams_shortcode',
		array(
			'admin_teams'     => $admin_teams,
			'user_teams'      => $user_teams,
			'has_admin_teams' => $has_admin_teams,
			'has_user_teams'  => $has_user_teams,
		)
	);
}
add_shortcode( 'user_teams', 'ebox_user_teams', 10, 3 );
