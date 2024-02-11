<?php
/**
 * ebox `[teaminfo]` shortcode processing.
 *
 * @since 3.2.0
 *
 * @package ebox\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Builds the `[teaminfo]` shortcode output.
 *
 * @since 3.2.0
 *
 * @param array  $attr {
 *    An array of shortcode attributes.
 *
 *    @type string     $show           The course info field to display. Default 'course_title'.
 *    @type int|string $user_id        User ID. Default empty.
 *    @type int|string $team_id       Team ID. Default empty.
 *    @type int|string $format         Date display format. Default 'F j, Y, g:i a'.
 *    @type int        $decimals       The number of decimal points. Default 2.
 * }
 * @param string $content The shortcode content. Default empty.
 * @param string $shortcode_slug The shortcode slug. Default 'teaminfo'.
 *
 * @return string shortcode output
 */
function ebox_teaminfo_shortcode( $attr = array(), $content = '', $shortcode_slug = 'teaminfo' ) {
	global $ebox_shortcode_used;
	$ebox_shortcode_used = true;

	$shortcode_atts             = shortcode_atts(
		array(
			'show'     => 'team_title',
			'user_id'  => '',
			'team_id' => '',
			'format'   => 'F j, Y, g:i a',
			'decimals' => 2,
		),
		$attr
	);
	$shortcode_atts['team_id'] = absint( $shortcode_atts['team_id'] );
	$shortcode_atts['user_id']  = absint( $shortcode_atts['user_id'] );

	/** This filter is documented in includes/shortcodes/ld_course_resume.php */
	$shortcode_atts = apply_filters( 'ebox_shortcode_atts', $shortcode_atts, $shortcode_slug );

	$shortcode_atts['team_id'] = ! empty( $shortcode_atts['team_id'] ) ? $shortcode_atts['team_id'] : '';
	if ( '' === $shortcode_atts['team_id'] ) {
		if ( ( isset( $_GET['team_id'] ) ) && ( ! empty( $_GET['team_id'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended 
			$shortcode_atts['team_id'] = intval( $_GET['team_id'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		} else {
			$post_id = get_the_id();
			if ( ebox_get_post_type_slug( 'team' ) === get_post_type( $post_id ) ) {
				$shortcode_atts['team_id'] = absint( $post_id );
			}
		}
	}

	$shortcode_atts['user_id'] = ! empty( $shortcode_atts['user_id'] ) ? $shortcode_atts['user_id'] : '';
	if ( '' === $shortcode_atts['user_id'] ) {
		if ( ( isset( $_GET['user_id'] ) ) && ( ! empty( $_GET['user_id'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$shortcode_atts['user_id'] = intval( $_GET['user_id'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}
	}

	if ( empty( $shortcode_atts['user_id'] ) ) {
		$shortcode_atts['user_id'] = get_current_user_id();

		/**
		 * Added logic to allow admin and team_leader to view certificate from other users.
		 *
		 * @since 2.3.0
		 */
		$post_type = '';
		if ( get_query_var( 'post_type' ) ) {
			$post_type = get_query_var( 'post_type' );
		}

		if ( 'ebox-certificates' == $post_type ) {
			if ( ( ( ebox_is_admin_user() ) || ( ebox_is_team_leader_user() ) ) && ( ( isset( $_GET['user'] ) ) && ( ! empty( $_GET['user'] ) ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$shortcode_atts['user_id'] = intval( $_GET['user'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			}
		}
	}

	if ( empty( $shortcode_atts['team_id'] ) || empty( $shortcode_atts['user_id'] ) ) {
		/**
		 * Filter Team info shortcode value.
		 *
		 * @since 3.2.0
		 *
		 * @param mixed $value          Determined return value.
		 * @param array $shortcode_atts Shortcode attributes.
		 */
		return apply_filters( 'ebox_teaminfo', '', $shortcode_atts );
	}

	$shortcode_atts['show'] = strtolower( $shortcode_atts['show'] );

	$team_post = get_post( $shortcode_atts['team_id'] );
	if ( ( $team_post ) && ( is_a( $team_post, 'WP_Post' ) ) && ( ebox_get_post_type_slug( 'team' ) === $team_post->post_type ) ) {
		switch ( $shortcode_atts['show'] ) {
			case 'team_title':
				$shortcode_atts[ $shortcode_atts['show'] ] = $team_post->post_title;
				/** This filter is documented in includes/shortcodes/ld_teaminfo.php */
				return apply_filters( 'ebox_teaminfo', $shortcode_atts[ $shortcode_atts['show'] ], $shortcode_atts );

			case 'team_url':
				$shortcode_atts[ $shortcode_atts['show'] ] = get_permalink( $shortcode_atts['team_id'] );
				/** This filter is documented in includes/shortcodes/ld_teaminfo.php */
				return apply_filters( 'ebox_teaminfo', $shortcode_atts[ $shortcode_atts['show'] ], $shortcode_atts );

			case 'team_price_type':
				$shortcode_atts[ $shortcode_atts['show'] ] = ebox_get_setting( $shortcode_atts['team_id'], 'team_price_type' );
				/** This filter is documented in includes/shortcodes/ld_teaminfo.php */
				return apply_filters( 'ebox_teaminfo', $shortcode_atts[ $shortcode_atts['show'] ], $shortcode_atts );

			case 'team_price':
				$shortcode_atts[ $shortcode_atts['show'] ] = ebox_get_setting( $shortcode_atts['team_id'], 'team_price' );
				/** This filter is documented in includes/shortcodes/ld_teaminfo.php */
				return apply_filters( 'ebox_teaminfo', $shortcode_atts[ $shortcode_atts['show'] ], $shortcode_atts );

			case 'team_users_count':
				$shortcode_atts[ $shortcode_atts['show'] ] = count( ebox_get_teams_user_ids( $shortcode_atts['team_id'] ) );
				/** This filter is documented in includes/shortcodes/ld_teaminfo.php */
				return apply_filters( 'ebox_teaminfo', $shortcode_atts[ $shortcode_atts['show'] ], $shortcode_atts );

			case 'team_courses_count':
				$shortcode_atts[ $shortcode_atts['show'] ] = count( ebox_team_enrolled_courses( $shortcode_atts['team_id'] ) );
				/** This filter is documented in includes/shortcodes/ld_teaminfo.php */
				return apply_filters( 'ebox_teaminfo', $shortcode_atts[ $shortcode_atts['show'] ], $shortcode_atts );

			default:
				/** This filter is documented in includes/shortcodes/ld_teaminfo.php */
				return apply_filters( 'ebox_teaminfo', '', $shortcode_atts );

			// The following cases required user_id.

			case 'user_team_status':
				if ( ( ! empty( $shortcode_atts['team_id'] ) ) && ( ! empty( $shortcode_atts['user_id'] ) ) ) {
					$shortcode_atts[ $shortcode_atts['show'] ] = ebox_get_user_team_status( $shortcode_atts['team_id'], $shortcode_atts['user_id'] );
					/** This filter is documented in includes/shortcodes/ld_teaminfo.php */
					return apply_filters( 'ebox_teaminfo', $shortcode_atts[ $shortcode_atts['show'] ], $shortcode_atts );
				}
				break;

			case 'enrolled_on':
				if ( ( ! empty( $shortcode_atts['team_id'] ) ) && ( ! empty( $shortcode_atts['user_id'] ) ) ) {
					$team_started_timestamp = ebox_get_user_team_started_timestamp( $shortcode_atts['team_id'], $shortcode_atts['user_id'] );
					if ( ! empty( $team_started_timestamp ) ) {
						$shortcode_atts[ $shortcode_atts['show'] ] = $team_started_timestamp;
						/** This filter is documented in includes/shortcodes/ld_teaminfo.php */
						return apply_filters( 'ebox_teaminfo', ebox_adjust_date_time_display( $team_started_timestamp, $shortcode_atts['format'] ), $shortcode_atts );
					} else {
						/** This filter is documented in includes/shortcodes/ld_teaminfo.php */
						return apply_filters( 'ebox_teaminfo', '-', $shortcode_atts );
					}
				}
				break;

			case 'completed_on':
				if ( ( ! empty( $shortcode_atts['team_id'] ) ) && ( ! empty( $shortcode_atts['user_id'] ) ) ) {
					$team_completed_timestamp = ebox_get_user_team_completed_timestamp( $shortcode_atts['team_id'], $shortcode_atts['user_id'] );
					if ( ! empty( $team_completed_timestamp ) ) {
						$shortcode_atts[ $shortcode_atts['show'] ] = $team_completed_timestamp;
						/** This filter is documented in includes/shortcodes/ld_teaminfo.php */
						return apply_filters( 'ebox_teaminfo', ebox_adjust_date_time_display( $team_completed_timestamp, $shortcode_atts['format'] ), $shortcode_atts );
					} else {
						/** This filter is documented in includes/shortcodes/ld_teaminfo.php */
						return apply_filters( 'ebox_teaminfo', '-', $shortcode_atts );
					}
				}
				break;

			case 'percent_completed':
				if ( ( ! empty( $shortcode_atts['team_id'] ) ) && ( ! empty( $shortcode_atts['user_id'] ) ) ) {
					$team_percent_completed = ebox_get_user_team_completed_percentage( $shortcode_atts['team_id'], $shortcode_atts['user_id'] );
					if ( ! empty( $team_percent_completed ) ) {
						$shortcode_atts[ $shortcode_atts['show'] ] = $team_percent_completed;
						/** This filter is documented in includes/shortcodes/ld_teaminfo.php */
						return apply_filters( 'ebox_teaminfo', number_format( $team_percent_completed, $shortcode_atts['decimals'] ), $shortcode_atts );
					} else {
						/** This filter is documented in includes/shortcodes/ld_teaminfo.php */
						return apply_filters( 'ebox_teaminfo', '-', $shortcode_atts );
					}
				}
				break;
		}
	}
	return '';
}
add_shortcode( 'teaminfo', 'ebox_teaminfo_shortcode', 10, 3 );
