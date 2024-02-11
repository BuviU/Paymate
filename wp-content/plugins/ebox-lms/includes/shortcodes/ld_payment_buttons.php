<?php
/**
 * ebox `[ebox_payment_buttons]` shortcode processing.
 *
 * @since 2.1.0
 *
 * @package ebox\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Builds the `[ebox_payment_buttons]` shortcode output.
 *
 * @since 2.1.0
 *
 * @global boolean $ebox_shortcode_used
 *
 * @param array  $atts {
 *    An array of shortcode attributes.
 *
 *    @type int $course_id Course ID. Default 0.
 * }
 * @param string $content        The shortcode content. Default empty.
 * @param string $shortcode_slug The shortcode slug. Default 'ebox_payment_buttons'.
 *
 * @return string Returns the `ebox_payment_buttons` shortcode output.
 */
function ebox_payment_buttons_shortcode( $atts = array(), $content = '', $shortcode_slug = 'ebox_payment_buttons' ) {
	global $ebox_shortcode_used;

	$atts_defaults = array(
		'course_id' => '',
		'team_id'  => '',
	);

	$atts = shortcode_atts( $atts_defaults, $atts );

	/** This filter is documented in includes/shortcodes/ld_course_resume.php */
	$atts = apply_filters( 'ebox_shortcode_atts', $atts, $shortcode_slug );

	if ( ( empty( $atts['course_id'] ) ) && ( empty( $atts['course_id'] ) ) ) {
		$viewed_post_id = (int) get_the_ID();
		if ( ! empty( $viewed_post_id ) ) {
			if ( in_array( get_post_type( $viewed_post_id ), ebox_get_post_types( 'course' ), true ) ) {
				$atts['course_id'] = ebox_get_course_id( $viewed_post_id );
			} elseif ( get_post_type( $viewed_post_id ) === ebox_get_post_type_slug( 'team' ) ) {
				$atts['team_id'] = $viewed_post_id;
			}
		}
	}

	$atts['team_id']  = absint( $atts['team_id'] );
	$atts['course_id'] = absint( $atts['course_id'] );

	$shortcode_out = '';

	if ( ! empty( $atts['course_id'] ) ) {
		$shortcode_out = ebox_payment_buttons( $atts['course_id'] );
	} elseif ( ! empty( $atts['team_id'] ) ) {
		$shortcode_out = ebox_payment_buttons( $atts['team_id'] );
	}

	if ( ! empty( $shortcode_out ) ) {
		$ebox_shortcode_used = true;

		$content .= '<div class="ebox-wrapper ebox-wrap ebox-shortcode-wrap">' . $shortcode_out . '</div>';
	}

	return $content;
}
add_shortcode( 'ebox_payment_buttons', 'ebox_payment_buttons_shortcode', 10, 3 );
