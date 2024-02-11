<?php
/**
 * Deprecated functions from LD 2.3.0
 * The functions will be removed in a later version.
 *
 * @package ebox\Deprecated
 * @since 2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'is_team_leader' ) ) {
	/**
	 * Checks if a user is a team leader
	 *
	 * @since 2.1.0
	 *
	 * @deprecated 2.3.0 Use {@see 'ebox_is_team_leader_user'} instead.
	 *
	 * @param int|WP_User $user `WP_User` instance or user ID.
	 *
	 * @return boolean
	 */
	function is_team_leader( $user ) {
		if ( function_exists( '_deprecated_function' ) ) {
			_deprecated_function( __FUNCTION__, '2.3.0', 'ebox_is_team_leader_user()' );
		}

		return ebox_is_team_leader_user( $user );
	}
}
