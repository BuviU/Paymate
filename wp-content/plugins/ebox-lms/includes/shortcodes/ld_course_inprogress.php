<?php
/**
 * ebox `[course_inprogress]` shortcode processing.
 *
 * @since 2.1.0
 * @package ebox\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Builds the `[course_inprogress]` shortcode output.
 *
 * Shortcode that shows the content if the user is in progress on the course.
 *
 * @global boolean $ebox_shortcode_used
 *
 * @since 2.1.0
 *
 * @param array  $atts {
 *    An array of shortcode attributes.
 *
 *    @type string  $content   The shortcode content. Default empty.
 *    @type int     $course_id Course ID. Default false.
 *    @type int     $user_id   User ID. Default false.
 *    @type boolean $autop     Whether to replace line breaks with paragraph elements. Default true.
 * }
 * @param string $content The shortcode content. Default empty.
 * @param string $shortcode_slug The shortcode slug. Default 'course_inprogress'.
 *
 * @return string The `course_inprogress` shortcode output.
 */
function ebox_course_inprogress_shortcode( $atts = array(), $content = '', $shortcode_slug = 'course_inprogress' ) {
	global $ebox_shortcode_used;
	$ebox_shortcode_used = true;

	if ( ! empty( $content ) ) {

		if ( ! is_array( $atts ) ) {
			if ( ! empty( $atts ) ) {
				$atts = array( $atts );
			} else {
				$atts = array();
			}
		}

		$defaults = array(
			'content'   => $content,
			'course_id' => false,
			'user_id'   => false,
			'autop'     => true,
		);
		$atts     = wp_parse_args( $atts, $defaults );
		if ( ( true === $atts['autop'] ) || ( 'true' === $atts['autop'] ) || ( '1' === $atts['autop'] ) ) {
			$atts['autop'] = true;
		} else {
			$atts['autop'] = false;
		}

		/** This filter is documented in includes/shortcodes/ld_course_resume.php */
		$atts = apply_filters( 'ebox_shortcode_atts', $atts, $shortcode_slug );

		/**
		 * Filters `course_inprogress` shortcode attributes.
		 *
		 * @param array $attributes An array of course_inprogress shortcode attributes.
		 */
		$atts = apply_filters( 'ebox_course_inprogress_shortcode_atts', $atts );

		$atts['content'] = ebox_course_status_content_shortcode( $atts, $atts['content'], esc_html__( 'In Progress', 'ebox' ) );
		return ebox_LMS::get_template(
			'ebox_course_inprogress_message',
			array(
				'shortcode_atts' => $atts,
			),
			false
		);
	}
	return '';
}

add_shortcode( 'course_inprogress', 'ebox_course_inprogress_shortcode', 10, 3 );
