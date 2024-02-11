<?php
/**
 * Deprecated functions from LD 3.1.7
 * The functions will be removed in a later version.
 *
 * @package ebox\Deprecated
 * @since 3.1.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'ebox_user_can_bypass_course_limits' ) ) {
	/**
	 * ebox user can bypass course limits
	 *
	 * @deprecated 3.1.7 Use {@see 'ebox_can_user_bypass'} instead.
	 *
	 * @param int $user_id User ID.
	 */
	function ebox_user_can_bypass_course_limits( $user_id = null ) {
		if ( function_exists( '_deprecated_function' ) ) {
			_deprecated_function( __FUNCTION__, '3.2.0', 'ebox_can_user_bypass' );
		}

		return ebox_can_user_bypass( $user_id );
	}
}

if ( ! function_exists( 'is_course_prerequities_completed' ) ) {
	/**
	 * Is course prerequities completed
	 *
	 * @deprecated 3.1.7 Use {@see 'ebox_is_course_prerequities_completed'} instead.
	 *
	 * @param int $course_id Course ID.
	 */
	function is_course_prerequities_completed( $course_id = null ) {
		if ( function_exists( '_deprecated_function' ) ) {
			_deprecated_function( __FUNCTION__, '3.2.0', 'ebox_is_course_prerequities_completed' );
		}

		return ebox_is_course_prerequities_completed( $course_id );
	}
}
