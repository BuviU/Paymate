<?php
/**
 * ebox `[course_content]` shortcode processing.
 *
 * @since 2.1.0
 * @package ebox\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Builds the `[course_content]` shortcode output.
 *
 * @global boolean $ebox_shortcode_used
 *
 * @since 2.1.0
 *
 * @param array  $atts {
 *    The shortcode attributes.
 *
 *    @type int         $course_id The ID of the course. Default 0.
 *    @type boolean|int $num       Unused Default false.
 * }
 * @param string $content        The shortcode content. Default empty.
 * @param string $shortcode_slug The shortcode slug. Default 'course_content'.
 *
 * @return string The output of the shortcode.
 */
function ebox_course_content_shortcode( $atts = array(), $content = '', $shortcode_slug = 'course_content' ) {
	global $ebox_shortcode_used, $course_pager_results;

	static $shown_content = array();

	if ( ! is_array( $atts ) ) {
		$atts = array();
	}

	$viewed_post_id   = (int) get_the_ID();
	$viewed_post_type = get_post_type( $viewed_post_id );

	$atts_defaults = array(
		'course_id' => '',
		'post_id'   => '',
		'team_id'  => '',
		'paged'     => 1,
		'num'       => '',
		'wrapper'   => 1,
	);
	$atts          = shortcode_atts( $atts_defaults, $atts );

	/** This filter is documented in includes/shortcodes/ld_course_resume.php */
	$atts = apply_filters( 'ebox_shortcode_atts', $atts, $shortcode_slug );

	// This is not an official shortcode attribute so we add it after the filter as it will be used below.
	$atts['user_id'] = get_current_user_id();

	if ( ! empty( $atts['course_id'] ) ) {
		if ( ebox_get_post_type_slug( 'course' ) !== get_post_type( $atts['course_id'] ) ) {
			$atts['course_id'] = 0;
		}
	}

	if ( ! empty( $atts['post_id'] ) ) {
		if ( ! in_array( get_post_type( $atts['post_id'] ), ebox_get_post_types( 'course' ), true ) ) {
			$atts['post_id'] = 0;
		}
	}

	if ( ! empty( $atts['team_id'] ) ) {
		if ( get_post_type( $atts['team_id'] ) !== ebox_get_post_type_slug( 'team' ) ) {
			$atts['team_id'] = 0;
		}
	}

	if ( ( '' === $atts['course_id'] ) && ( ! empty( $viewed_post_id ) ) && ( in_array( get_post_type( $viewed_post_id ), ebox_get_post_types( 'course' ), true ) ) ) {
		$atts['course_id'] = ebox_get_course_id( $viewed_post_id );
	}

	/**
	 * Support for legacy course_content logic. If the $atts['course_id'] is set, but not the same as
	 * the viewed post ID. And the $atts['post_id'] is literally blank string. Then we will set the
	 * $atts['post_id'] to 0 (int) to force the logic to run.
	 */
	if ( ( '' === $atts['post_id'] ) && ( ! empty( $atts['course_id'] ) ) && ( absint( $atts['course_id'] ) !== $viewed_post_id ) ) {
		$atts['post_id'] = '0';
	}

	// If the 'post_id' is set to '0' it will trigger showing the course modules listing.
	if ( '0' === $atts['post_id'] ) {
		if ( ! empty( $atts['course_id'] ) ) {
			$atts['post_id'] = $atts['course_id'];
		}
	} else {
		if ( empty( $atts['post_id'] ) ) {
			if ( ! empty( $viewed_post_id ) ) {
				if ( in_array( get_post_type( $viewed_post_id ), ebox_get_post_types( 'course' ), true ) ) {
					$atts['post_id'] = $viewed_post_id;
				} elseif ( get_post_type( $viewed_post_id ) === ebox_get_post_type_slug( 'team' ) ) {
					$atts['team_id']  = $viewed_post_id;
					$atts['post_id']   = 0;
					$atts['course_id'] = 0;
				} else {
					if ( ! empty( $atts['course_id'] ) ) {
						$atts['post_id'] = $atts['course_id'];
					} elseif ( ! empty( $atts['team_id'] ) ) {
						$atts['post_id'] = $atts['team_id'];
					}
				}
			}
		}
	}

	$atts['course_id'] = absint( $atts['course_id'] );
	$atts['post_id']   = absint( $atts['post_id'] );
	$atts['team_id']  = absint( $atts['team_id'] );
	$atts['user_id']   = absint( $atts['user_id'] );
	$atts['paged']     = absint( $atts['paged'] );
	$atts['wrapper']   = (bool) $atts['wrapper'];

	if ( '' !== $atts['num'] ) {
		$atts['num'] = absint( $atts['num'] );
	}

	if ( ! empty( $atts['team_id'] ) ) {
		$shown_content_key = $atts['team_id'] . '_' . $atts['user_id'];
	} elseif ( ! empty( $atts['course_id'] ) ) {
		$shown_content_key = $atts['course_id'] . '_' . $atts['post_id'] . '_' . $atts['user_id'];
	}

	if ( ( ! isset( $shown_content_key ) ) || ( empty( $shown_content_key ) ) ) {
		return $content;
	}

	$shown_content[ $shown_content_key ] = '';

	if ( ( ! empty( $atts['team_id'] ) ) && ( ( ebox_get_post_type_slug( 'team' ) === get_post_type( $atts['team_id'] ) ) ) ) {
		if ( ebox_is_user_in_team( $atts['user_id'], $atts['team_id'] ) ) {
			$has_access   = true;
			$team_status = ebox_get_user_team_status( $atts['team_id'], $atts['user_id'] );
		} else {
			$has_access   = false;
			$team_status = '';
		}

		if ( '' === $atts['num'] ) {
			$team_courses_per_page = ebox_get_team_courses_per_page( $atts['team_id'] );
			if ( ! empty( $team_courses_per_page ) ) {
				$atts['num'] = absint( $team_courses_per_page );
			} else {
				unset( $atts['num'] );
			}
		}
		$team_courses     = ebox_get_team_courses_list( $atts['team_id'], $atts );
		$has_team_content = ( ( is_array( $team_courses ) ) && ( ! empty( $team_courses ) ) );

		$show_team_content = ( ! $has_access && 'on' === ebox_get_setting( $atts['team_id'], 'team_disable_content_table' ) ? false : true );

		if ( $has_team_content && $show_team_content ) {
			$level = ob_get_level();
			ob_start();

			ebox_LMS::get_template(
				'shortcodes/team_content_shortcode',
				array(
					'user_id'              => $atts['user_id'],
					'team_id'             => $atts['team_id'],
					'team_status'         => $team_status,
					'team_courses'        => $team_courses,
					'has_access'           => $has_access,
					'course_pager_results' => $course_pager_results,
				),
				true
			);

			$shortcode_out = ebox_ob_get_clean( $level );

			if ( ( defined( 'ebox_NEW_LINE_AND_CR_TO_SPACE' ) ) && ( true === ebox_NEW_LINE_AND_CR_TO_SPACE ) ) {
				$shortcode_out = str_replace( array( "\n", "\r" ), ' ', $shortcode_out );
			}

			if ( ! empty( $shortcode_out ) ) {
				$shown_content[ $shown_content_key ] .= $shortcode_out;
			}
		}
	} elseif ( ( ! empty( $atts['post_id'] ) ) && ( ( ebox_get_post_type_slug( 'course' ) === get_post_type( $atts['post_id'] ) ) ) ) {
		$course    = get_post( $atts['post_id'] );
		$post_post = $course;

		$course_id = intval( $atts['course_id'] );

		$user_id = $atts['user_id'];
		if ( ! empty( $user_id ) ) {
			$logged_in = true;
		} else {
			$logged_in = false;
		}

		if ( '' === $atts['num'] ) {
			$modules_per_page = ebox_get_course_modules_per_page( $atts['course_id'] );
			if ( ! empty( $modules_per_page ) ) {
				$atts['num'] = absint( $modules_per_page );
			} else {
				unset( $atts['num'] );
			}
		}

		$course_settings            = ebox_get_setting( $course );
		$courses_options            = ebox_get_option( 'ebox-courses' );
		$modules_options            = ebox_get_option( 'ebox-modules' );
		$quizzes_options            = ebox_get_option( 'ebox-quiz' );
		$lesson_progression_enabled = ebox_lesson_progression_enabled( $atts['post_id'] );
		$course_status              = ebox_course_status( $atts['post_id'], null );
		$has_access                 = ebox_lms_has_access( $atts['post_id'], $atts['user_id'] );
		$modules                    = ebox_get_course_modules_list( $course, $atts['user_id'], $atts );
		$quizzes                    = ebox_get_course_quiz_list( $course );
		$has_course_content         = ( ! empty( $modules ) || ! empty( $quizzes ) );

		$has_topics = false;

		if ( ! empty( $modules ) ) {
			foreach ( $modules as $lesson ) {
				$lesson_topics[ $lesson['post']->ID ] = ebox_topic_dots( $lesson['post']->ID, false, 'array', $atts['user_id'], $atts['post_id'] );
				if ( ! empty( $lesson_topics[ $lesson['post']->ID ] ) ) {
					$has_topics = true;

					$topic_pager_args                     = array(
						'course_id' => $course_id,
						'lesson_id' => $lesson['post']->ID,
					);
					$lesson_topics[ $lesson['post']->ID ] = ebox_process_lesson_topics_pager( $lesson_topics[ $lesson['post']->ID ], $topic_pager_args );
				}
			}
		}

		$level = ob_get_level();
		ob_start();
		$template_file = ebox_LMS::get_template( 'course_content_shortcode', null, null, true );
		if ( ! empty( $template_file ) ) {
			include $template_file;
		}

		$shortcode_out = ebox_ob_get_clean( $level );

		if ( ( defined( 'ebox_NEW_LINE_AND_CR_TO_SPACE' ) ) && ( true === ebox_NEW_LINE_AND_CR_TO_SPACE ) ) {
			$shortcode_out = str_replace( array( "\n", "\r" ), ' ', $shortcode_out );
		}

		$user_has_access = $has_access ? 'user_has_access' : 'user_has_no_access';

		if ( ! empty( $shortcode_out ) ) {
			if ( $atts['wrapper'] ) {
				$shortcode_out = '<div class="ebox ' . $user_has_access . '" id="ebox_post_' . $course_id . '">' . $shortcode_out . '</div>';
			}
			$shown_content[ $shown_content_key ] .= $shortcode_out;
		}
	} elseif ( ( ! empty( $atts['post_id'] ) ) && ( ( ebox_get_post_type_slug( 'topic' ) === get_post_type( $atts['post_id'] ) ) ) ) {
		$post_post = get_post( $atts['post_id'] );
		$lesson_id = ebox_course_get_single_parent_step( $atts['course_id'], $post_post->ID );
		if ( empty( $lesson_id ) ) {
			return '';
		}
		$lesson_id = absint( $lesson_id );

		$quizzes = ebox_get_lesson_quiz_list( $post_post, null, $atts['course_id'] );
		$quizids = array();

		if ( ! empty( $quizzes ) ) {
			foreach ( $quizzes as $quiz ) {
				$quizids[ $quiz['post']->ID ] = $quiz['post']->ID;
			}
		}

		if ( ebox_lesson_hasassignments( $post_post ) && ! empty( $atts['user_id'] ) ) { // cspell:disable-line.
			$bypass_course_limits_admin_users = ebox_can_user_bypass( $atts['user_id'], 'ebox_lesson_assignment' );
			$course_children_steps_completed  = ebox_user_is_course_children_progress_complete( $atts['user_id'], $atts['course_id'], $atts['post_id'] );
			if ( ( ebox_lesson_progression_enabled() && ( true === $course_children_steps_completed ) ) || ( ! ebox_lesson_progression_enabled() ) || ( true === $bypass_course_limits_admin_users ) ) {

				$level = ob_get_level();
				ob_start();

				ebox_LMS::get_template(
					'assignment/listing.php',
					array(
						'course_step_post' => $post_post,
						'user_id'          => $atts['user_id'],
						'course_id'        => $atts['course_id'],
						'context'          => 'topic',
					),
					true
				);

				$shortcode_out = ebox_ob_get_clean( $level );
				if ( ! empty( $shortcode_out ) ) {
					$shown_content[ $shown_content_key ] .= $shortcode_out;
				}
			}
		}

		if ( ! empty( $quizzes ) ) {
			$level = ob_get_level();
			ob_start();

			ebox_LMS::get_template(
				'quiz/listing.php',
				array(
					'user_id'   => $atts['user_id'],
					'course_id' => $atts['course_id'],
					'lesson_id' => $lesson_id,
					'quizzes'   => $quizzes,
					'context'   => 'topic',
				),
				true
			);

			$shortcode_out = ebox_ob_get_clean( $level );
			if ( ! empty( $shortcode_out ) ) {
				$shown_content[ $shown_content_key ] .= $shortcode_out;
			}
		}
	} elseif ( ( ! empty( $atts['post_id'] ) ) && ( ( ebox_get_post_type_slug( 'lesson' ) === get_post_type( $atts['post_id'] ) ) ) ) {
		$post_post = get_post( $atts['post_id'] );
		if ( ebox_lesson_hasassignments( $post_post ) && ! empty( $atts['user_id'] ) ) { // cspell:disable-line.
			$bypass_course_limits_admin_users = ebox_can_user_bypass( $atts['user_id'], 'ebox_lesson_assignment' );
			$course_children_steps_completed  = ebox_user_is_course_children_progress_complete( $atts['user_id'], $atts['course_id'], $atts['post_id'] );
			if ( ( ebox_lesson_progression_enabled() && ( true === $course_children_steps_completed ) ) || ( ! ebox_lesson_progression_enabled() ) || ( true === $bypass_course_limits_admin_users ) ) {

				$level = ob_get_level();
				ob_start();

				ebox_LMS::get_template(
					'assignment/listing.php',
					array(
						'course_step_post' => $post_post,
						'user_id'          => $atts['user_id'],
						'course_id'        => $atts['course_id'],
						'context'          => 'lesson',
					),
					true
				);

				$shortcode_out = ebox_ob_get_clean( $level );
				if ( ! empty( $shortcode_out ) ) {
					$shown_content[ $shown_content_key ] .= $shortcode_out;
				}
			}
		}

		if ( '' === $atts['num'] ) {
			$topics_per_page = ebox_get_course_topics_per_page( $atts['course_id'], $atts['post_id'] );
			if ( ! empty( $topics_per_page ) ) {
				$atts['num'] = absint( $topics_per_page );
			} else {
				unset( $atts['num'] );
			}
		}

		$topics = ebox_topic_dots( $atts['post_id'], false, 'array', null, $atts['course_id'] );
		if ( ( isset( $atts['num'] ) ) && ( ! empty( $atts['num'] ) ) ) {
			$topic_pager_args = array(
				'course_id' => $atts['course_id'],
				'lesson_id' => $atts['post_id'],
				'per_page'  => $atts['num'],
			);
			$topics           = ebox_process_lesson_topics_pager( $topics, $topic_pager_args );
		}

		$quizids = array();
		$quizzes = ebox_get_lesson_quiz_list( $post_post, null, $atts['course_id'] );
		if ( ! empty( $quizzes ) ) {
			foreach ( $quizzes as $quiz ) {
				$quizids[ $quiz['post']->ID ] = $quiz['post']->ID;
			}
		}

		if ( ! empty( $topics ) || ! empty( $quizzes ) ) {
			$lesson = array(
				'post' => $post_post,
			);

			$level = ob_get_level();
			ob_start();

			ebox_LMS::get_template(
				'lesson/listing.php',
				array(
					'course_id' => $atts['course_id'],
					'lesson'    => $lesson,
					'topics'    => $topics,
					'quizzes'   => $quizzes,
					'user_id'   => $atts['user_id'],
				),
				true
			);

			$shortcode_out = ebox_ob_get_clean( $level );
			if ( ! empty( $shortcode_out ) ) {
				$shown_content[ $shown_content_key ] .= '<div class="ld-lesson-topic-list">' . $shortcode_out . '</div>';
			}
		}
	}

	if ( ( isset( $shown_content[ $shown_content_key ] ) ) && ( ! empty( $shown_content[ $shown_content_key ] ) ) ) {
		$content                 .= '<div class="ebox-wrapper ebox-wrap ebox-shortcode-wrap ebox-shortcode-wrap-' . $shortcode_slug . '-' . $shown_content_key . '">' . $shown_content[ $shown_content_key ] . '</div>';
		$ebox_shortcode_used = true;
	}

	return $content;
}
add_shortcode( 'course_content', 'ebox_course_content_shortcode', 10, 3 );
