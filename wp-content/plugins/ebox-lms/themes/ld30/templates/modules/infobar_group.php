<?php
/**
 * ebox LD30 Displays an informational bar in team
 *
 * Is contextulaized by passing in a $context variable that indicates post type
 *
 * @var string $context      Context used for display. 'team'.
 * @var int    $team_id     Team ID.
 * @var int    $user_id      User ID.
 * @var bool   $has_access   User has access to team or is enrolled.
 * @var bool   $team_status User's Team Status. Completed, No Started, or In Complete.
 * @var object $post         Team Post Object.
 *
 * @since 3.1.7
 *
 * @package ebox\Templates\LD30
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** This filter is documented in themes/ld30/templates/modules/infobar.php */
do_action( 'ebox-infobar-before', get_post_type( $team_id ), $team_id, $user_id );

/** This filter is documented in themes/ld30/templates/modules/infobar.php */
do_action( 'ebox-' . $context . '-infobar-before', $team_id, $user_id );

/** This filter is documented in themes/ld30/templates/modules/infobar.php */
do_action( 'ebox-infobar-inside-before', get_post_type( $team_id ), $team_id, $user_id );

/** This filter is documented in themes/ld30/templates/modules/infobar.php */
do_action( 'ebox-' . $context . '-infobar-inside-before', $team_id, $user_id );

ebox_get_template_part(
	'modules/infobar/team.php',
	array(
		'has_access'   => $has_access,
		'user_id'      => $user_id,
		'team_id'     => $team_id,
		'team_status' => $team_status,
		'post'         => $post,
	),
	true
);

/** This filter is documented in themes/ld30/templates/modules/infobar.php */
do_action( 'ebox-infobar-inside-after', get_post_type( $team_id ), $team_id, $user_id );

/** This filter is documented in themes/ld30/templates/modules/infobar.php */
do_action( 'ebox-' . $context . '-infobar-inside-after', $team_id, $user_id );

/** This filter is documented in themes/ld30/templates/modules/infobar.php */
do_action( 'ebox-infobar-after', get_post_type( $team_id ), $team_id, $user_id );

/** This filter is documented in themes/ld30/templates/modules/infobar.php */
do_action( 'ebox-' . $context . '-infobar-after', $team_id, $user_id );
