<?php
/**
 * ebox LD30 Login and Registration functions
 *
 * Handles authentication, registering, resetting passwords and other user handling.
 *
 * @since 3.0.0
 *
 * @package ebox\Templates\LD30
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * LOGIN FUNCTIONS
 */

/**
 * Adds a hidden form field to the login form.
 *
 * Fires on `login_form_top` hook.
 *
 * @since 3.0.0
 *
 * @param string $content Login form content.
 *
 * @return string Login form hidden field content.
 */
function ebox_add_login_field_top( $content = '' ) {
	$content .= '<input id="ebox-login-form" type="hidden" name="ebox-login-form" value="' . wp_create_nonce( 'ebox-login-form' ) . '" />';

	$post_type = get_post_type( get_the_ID() );
	if ( in_array( $post_type, ebox_get_post_types( 'course' ), true ) ) {
		$course_id = ebox_get_course_id( get_the_ID() );

		/**
		 * Filters whether to allow enrollment of course with the login. The default value is true.
		 *
		 * @since 3.1.0
		 *
		 * @param boolean $include_course Whether to allow login from the course.
		 * @param int     $course_id      Course ID.
		 */
		if ( ( ! empty( $course_id ) ) && ( in_array( ebox_get_setting( $course_id, 'course_price_type' ), array( 'free' ), true ) ) && ( apply_filters( 'ebox_login_form_include_course', true, $course_id ) ) ) {
			$content .= '<input name="ebox-login-form-course" value="' . $course_id . '" type="hidden" />';
			$content .= wp_nonce_field( 'ebox-login-form-course-' . $course_id . '-nonce', 'ebox-login-form-course-nonce', false, false );
		}
	} elseif ( in_array( $post_type, array( ebox_get_post_type_slug( 'team' ) ), true ) ) {
		$team_id = get_the_ID();

		/**
		 * Filters whether to allow enrollment of team with the login. The default value is true.
		 *
		 * @since 3.2.0
		 *
		 * @param boolean $include_team Whether to allow login from the team.
		 * @param int     $team_id       Team ID.
		 */
		if ( ( ! empty( $team_id ) ) && ( in_array( ebox_get_setting( $team_id, 'team_price_type' ), array( 'free' ), true ) ) && ( apply_filters( 'ebox_login_form_include_team', true, $team_id ) ) ) {
			$content .= '<input name="ebox-login-form-post" value="' . $team_id . '" type="hidden" />';
			$content .= wp_nonce_field( 'ebox-login-form-post-' . $team_id . '-nonce', 'ebox-login-form-post-nonce', false, false );
		}
	}

	return $content;
}

// Add a filter for validation returns.
add_filter( 'login_form_top', 'ebox_add_login_field_top' );
