<?php
/**
 * ebox `[ld_team]` shortcode processing.
 *
 * @since 2.1.0
 *
 * @package ebox\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Builds the `[ld_team]` shortcode output.
 *
 * Shortcode to display content to users that have access to current team id.
 *
 * @global boolean $ebox_shortcode_used
 *
 * @since 2.3.0
 *
 * @param array  $atts {
 *    An array of shortcode attributes.
 *
 *    @type int     $team_id Course ID.
 *    @type int     $user_id  User ID.
 *    @type string  $content  The shortcode content.
 *    @type boolean $autop    Whether to replace line breaks with paragraph elements.
 * }
 * @param string $content The shortcode content. Default empty.
 * @param string $shortcode_slug The shortcode slug. Default 'ld_team'.
 *
 * @return string The `ld_team` shortcode output.
 */
function ebox_ld_team_check_shortcode( $atts = array(), $content = '', $shortcode_slug = 'ld_team' ) {
	global $ebox_shortcode_used;

	if ( ( is_singular() ) && ( ! is_null( $content ) ) && ( is_user_logged_in() ) ) {

		$defaults = array(
			'team_id' => 0,
			'user_id'  => get_current_user_id(),
			'content'  => $content,
			'autop'    => true,
		);
		$atts     = wp_parse_args( $atts, $defaults );

		$atts['user_id']  = absint( $atts['user_id'] );
		$atts['team_id'] = absint( $atts['team_id'] );

		if ( ( true === $atts['autop'] ) || ( 'true' === $atts['autop'] ) || ( '1' === $atts['autop'] ) ) {
			$atts['autop'] = true;
		} else {
			$atts['autop'] = false;
		}

		/** This filter is documented in includes/shortcodes/ld_course_resume.php */
		$atts = apply_filters( 'ebox_shortcode_atts', $atts, $shortcode_slug );

		/**
		 * Filters ld_team shortcode attributes.
		 *
		 * @param array  $attribute An array of ld_team shortcode attributes.
		 * @param string $content   Shortcode Content.
		 */
		$atts = apply_filters( 'ebox_ld_team_shortcode_atts', $atts, $content );

		if ( ( ! empty( $atts['content'] ) ) && ( ! empty( $atts['user_id'] ) ) && ( ! empty( $atts['team_id'] ) ) && ( get_current_user_id() == $atts['user_id'] ) ) {
			if ( ebox_is_user_in_team( $atts['user_id'], $atts['team_id'] ) ) {
				$ebox_shortcode_used = true;
				$atts['content']          = do_shortcode( $atts['content'] );
				return ebox_LMS::get_template(
					'ebox_team_message',
					array(
						'shortcode_atts' => $atts,
					),
					false
				);
			}
		}
	}

	return '';
}
add_shortcode( 'ld_team', 'ebox_ld_team_check_shortcode', 10, 3 );
