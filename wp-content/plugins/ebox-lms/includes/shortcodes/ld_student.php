<?php
/**
 * ebox `[student]` shortcode processing.
 *
 * @since 2.1.0
 *
 * @package ebox\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Builds the `[student]` shortcode output.
 *
 * Shortcode to display content to users that have access to current course ID.
 *
 * @global boolean $ebox_shortcode_used
 *
 * @since 2.1.0
 *
 * @param array  $atts {
 *     An array of shortcode attributes.
 *
 *    @type int     $course_id Course ID. Default current course ID.
 *    @type int     $user_id   User ID. Default current user ID.
 *    @type string  $content   The shortcode content. Default null.
 *    @type boolean $autop     Whether to replace line breaks with paragraph elements. Default true.
 * }
 * @param string $content The shortcode content. Default empty.
 * @param string $shortcode_slug The shortcode slug. Default 'student'.
 *
 * @return string The `student` shortcode output.
 */
function ebox_student_check_shortcode( $atts = array(), $content = '', $shortcode_slug = 'student' ) {
	global $ebox_shortcode_used;

	if ( ( ! empty( $content ) ) && ( is_user_logged_in() ) ) {
		if ( ! is_array( $atts ) ) {
			if ( ! empty( $atts ) ) {
				$atts = array( $atts );
			} else {
				$atts = array();
			}
		}

		$defaults = array(
			'course_id' => '',
			'team_id'  => '',
			'user_id'   => get_current_user_id(),
			'content'   => $content,
			'autop'     => true,
		);
		$atts     = wp_parse_args( $atts, $defaults );

		/** This filter is documented in includes/shortcodes/ld_course_resume.php */
		$atts = apply_filters( 'ebox_shortcode_atts', $atts, $shortcode_slug );

		if ( ( true === $atts['autop'] ) || ( 'true' === $atts['autop'] ) || ( '1' === $atts['autop'] ) ) {
			$atts['autop'] = true;
		} else {
			$atts['autop'] = false;
		}

		if ( ! empty( $atts['course_id'] ) ) {
			if ( ebox_get_post_type_slug( 'course' ) !== get_post_type( $atts['course_id'] ) ) {
				$atts['course_id'] = 0;
			}
		}

		if ( ! empty( $atts['team_id'] ) ) {
			if ( ebox_get_post_type_slug( 'team' ) !== get_post_type( $atts['team_id'] ) ) {
				$atts['team_id'] = 0;
			}
		}

		// If 'course_id' and 'team_id' are empty we check if we are showing a Course or Team related post type.
		if ( ( empty( $atts['course_id'] ) ) && ( empty( $atts['team_id'] ) ) ) {
			$viewed_post_id = (int) get_the_ID();
			if ( ! empty( $viewed_post_id ) ) {
				if ( in_array( get_post_type( $viewed_post_id ), ebox_get_post_types( 'course' ), true ) ) {
					$atts['course_id'] = ebox_get_course_id( $viewed_post_id );
				} elseif ( get_post_type( $viewed_post_id ) === ebox_get_post_type_slug( 'team' ) ) {
					$atts['team_id'] = $viewed_post_id;
				}
			}
		}

		/**
		 * Filters student shortcode attributes.
		 *
		 * @param array $attributes An array of student shortcode attributes.
		 */
		$atts = apply_filters( 'ebox_student_shortcode_atts', $atts );

		$atts['user_id']   = absint( $atts['user_id'] );
		$atts['team_id']  = absint( $atts['team_id'] );
		$atts['course_id'] = absint( $atts['course_id'] );

		$view_content = false;

		if ( ( ! empty( $atts['user_id'] ) ) && ( get_current_user_id() === $atts['user_id'] ) ) {
			if ( ! empty( $atts['course_id'] ) ) {
				// The reason we are doing this check is because 'ebox_lms_has_access' will return true if the course does not exist.
				// This needs to be changed to return some other value because true signals the calling function that all is well.
				$course_id = ebox_get_course_id( $atts['course_id'] );
				if ( (int) $course_id === (int) $atts['course_id'] ) {
					if ( ebox_lms_has_access( $atts['course_id'], $atts['user_id'] ) ) {
						$view_content = true;
					}
				}
			} elseif ( ! empty( $atts['team_id'] ) ) {
				if ( ebox_is_user_in_team( $atts['user_id'], $atts['team_id'] ) ) {
					$view_content = true;
				}
			} else {
				$user_enrolled_courses = ebox_user_get_enrolled_courses( $atts['user_id'], array() );
				$user_enrolled_teams  = ebox_get_users_team_ids( $atts['user_id'] );

				// If the user is enrolled in any courses or teams then we show the content.
				if ( ( count( $user_enrolled_courses ) ) || ( count( $user_enrolled_teams ) ) ) {
					$view_content = true;
				}
			}
		}

		/**
		 * Filters student shortcode if user can view content.
		 *
		 * @since 4.4.0
		 *
		 * @param bool  $view_content Whether to view content.
		 * @param array $atts         An array of shortcode attributes.
		 */
		$view_content = apply_filters( 'ebox_student_shortcode_view_content', $view_content, $atts );

		if ( $view_content ) {
			$ebox_shortcode_used = true;
			$atts['content']          = do_shortcode( $atts['content'] );

			$shortcode_out = ebox_LMS::get_template(
				'ebox_course_student_message',
				array(
					'shortcode_atts' => $atts,
				),
				false
			);
			if ( ! empty( $shortcode_out ) ) {
				$content = '<div class="ebox-wrapper ebox-wrap ebox-shortcode-wrap">' . $shortcode_out . '</div>';
			}
		} else {
			$content = '';
		}
	}

	if ( ! is_user_logged_in() ) {
		$content = '';
	}

	return $content;
}
add_shortcode( 'student', 'ebox_student_check_shortcode' );
