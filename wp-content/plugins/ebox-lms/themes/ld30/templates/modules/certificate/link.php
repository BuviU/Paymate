<?php
/**
 * ebox LD30.
 *
 * @since 3.0.0
 *
 * @package ebox\Templates\LD30
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ebox LD30 Displays a link to the relevant certificate if it exists
 *
 * This will have to be variable based on the current users context.
 * Different information is passed in based on if they are on a course, lesson,
 * topic etc...
 *
 * Having it in one place is advantagous over multiple instances of the status
 * bar for Guttenburg block placement.
 *
 * Available Variables:
 *
 * $user_id         : Current User ID
 * $logged_in       : User is logged in
 * $current_user    : (object) Currently logged in user object
 *
 * @since 3.0.0
 *
 * @package ebox\Templates\LD30\Modules
 */

/**
 * Thought process:
 *
 * Have some function that checks for the existance of a post type specific
 * variant of a template and falls back to a generic one if it doesn't
 * exist.
 *
 * e.g
 *
 * get_contextualized_template( $slug, $string );
 *
 * if( file_exists( $slug . '-' . $string . '.php' ) ) {
 *      return $slug . '-' . $string . '.php';
 * } else {
 *      return $slug . '-' . 'generic.php';
 * }
 *
 */

// No access to any certificates if you're not logged in.
if ( $logged_in ) {

	// ebox::get_template_part( 'modules/link', get_post_type() );

}
