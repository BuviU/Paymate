<?php
/**
 * Deprecated functions from LD 2.6.4
 * The functions will be removed in a later version.
 *
 * @package ebox\Deprecated
 * @since 2.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'ebox_team_updated_messages' ) ) {
	/**
	 * Set 'updated' admin messages for Teams post type
	 *
	 * @since 2.1.0
	 *
	 * @deprecated 2.6.4 Use {@see 'ebox_post_updated_messages'} instead.
	 *
	 * @param  array $messages Messages.
	 *
	 * @return array $messages Messages.
	 */
	function ebox_team_updated_messages( $messages ) {
		if ( function_exists( '_deprecated_function' ) ) {
			_deprecated_function( __FUNCTION__, '2.6.4', 'ebox_post_updated_messages()' );
		}

		return ebox_post_updated_messages( $messages );
	}
}

// Get all users with explicit 'course_XX_access_from' access.
if ( ! function_exists( 'get_course_users_access_from_meta' ) ) {
	/**
	 * Gets the user course access from the meta.
	 *
	 * @deprecated 2.6.4 Use {@see 'ebox_get_course_users_access_from_meta'} instead.
	 *
	 * @param int $course_id Optional. Course ID. Default 0.
	 *
	 * @return array
	 */
	function get_course_users_access_from_meta( $course_id = 0 ) {
		if ( function_exists( '_deprecated_function' ) ) {
			_deprecated_function( __FUNCTION__, '2.6.4', 'ebox_get_course_users_access_from_meta()' );
		}

		return ebox_get_course_users_access_from_meta( $course_id );
	}
}

// Get all the users for a given course_id that have 'ebox_course_expired_XX' user meta records.
if ( ! function_exists( 'get_course_expired_access_from_meta' ) ) {
	/**
	 * Gets the user expired course access from the meta.
	 *
	 * @deprecated 2.6.4 Use {@see 'ebox_get_course_expired_access_from_meta'} instead.
	 *
	 * @param int $couese_id Optional. Course ID. Default 0.
	 *
	 * @return array
	 */
	function get_course_expired_access_from_meta( $couese_id = 0 ) {
		if ( function_exists( '_deprecated_function' ) ) {
			_deprecated_function( __FUNCTION__, '2.6.4', 'ebox_get_course_expired_access_from_meta()' );
		}

		return ebox_get_course_expired_access_from_meta( $couese_id );
	}
}

// Utility function to att the course settings in meta. Better than having this over inline over and over again.
if ( ! function_exists( 'get_course_meta_setting' ) ) {

	/**
	 * Gets the course settings from the meta.
	 *
	 * @deprecated 2.6.4 Use {@see ebox_get_course_meta_setting()
	 *
	 * @param int    $course_id   Optional. Course ID. Default 0.
	 * @param string $setting_key Optional. Settings key. Default empty.
	 *
	 * @return array|void
	 */
	function get_course_meta_setting( $course_id = 0, $setting_key = '' ) {
		if ( function_exists( '_deprecated_function' ) ) {
			_deprecated_function( __FUNCTION__, '2.6.4', 'ebox_get_course_meta_setting()' );
		}

		return ebox_get_course_meta_setting( $course_id, $setting_key );
	}
}
