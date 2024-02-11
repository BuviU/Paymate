<?php
/**
 * ebox LD30 Helper functions.
 *
 * @since 3.0.0
 *
 * @package ebox\Templates\LD30
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ebox_30_defs = array(
	'LD_30_TEMPLATE_DIR' => ebox_LMS_PLUGIN_DIR . 'themes/ld30/templates/',
	'LD_30_VER'          => '1.0',
);

foreach ( $ebox_30_defs as $ebox_30_definition => $ebox_30_value ) {
	if ( ! defined( $ebox_30_definition ) ) {
		define( $ebox_30_definition, $ebox_30_value ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.VariableConstantNameFound -- Used inside foreach loop
	}
}

require 'shortcodes.php';
require 'login-register-functions.php';

/**
 * Prints breadcrumbs output.
 *
 * Sames as ebox_get_breadcrumbs only it actually outputs escaped markup.
 *
 * @since 3.0.0
 *
 * @global WP_Post $post Global post object.
 *
 * @param int|WP_Post|null $post `WP_Post` object. Default to global $post.
 */
function ebox_the_breadcrumbs( $post = null ) {

	if ( null === $post ) {
		global $post;
	}

	if ( is_numeric( $post ) ) {
		$post = get_post( $post ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- I suppose it's what they wanted.
	}

	echo wp_kses_post( ebox_get_breadcrumbs( $post ) );

}

/**
 * Gets the breadcrumbs hierarchy.
 *
 * Builds an array of breadcrumbs for the current ebox post.
 *
 * @since 3.0.0
 *
 * @global WP_Post $post Global post object.
 *
 * @param int|WP_Post|null $post `WP_Post` object. Default to global $post.
 * @param array|false      $args Arguments used to generate breadcrumbs. Default is false.
 *
 * @return array The hierarchy of breadcrumbs.
 */
function ebox_get_breadcrumbs( $post = null, $args = false ) {

	if ( null === $post ) {
		global $post;
	}

	if ( is_numeric( $post ) ) {
		$post = get_post( $post ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- I suppose it's what they wanted.
	}

	if ( $args ) {
		extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract -- Bad idea, but better keep it for now.
	}

	// Get the course ID of the current element.
	$course_id = ebox_get_course_id( $post->ID );
	if ( empty( $course_id ) ) {
		return array();
	}

	$breadcrumbs = array(
		'course'  => array(
			'permalink' => ebox_get_step_permalink( $course_id ),
			'title'     => get_the_title( $course_id ),
		),
		'current' => array(
			'permalink' => ebox_get_step_permalink( $post->ID ),
			'title'     => get_the_title( $post->ID ),
		),
	);

	// If this is a topic or a quiz we might need a third hierarchy.
	switch ( get_post_type( $post->ID ) ) {

		case 'ebox-topic':
			$lesson_id             = ebox_course_get_single_parent_step( $course_id, $post->ID );
			$breadcrumbs['lesson'] = array(
				'permalink' => ebox_get_step_permalink( $lesson_id ),
				'title'     => get_the_title( $lesson_id ),
			);
			break;
		case 'ebox-quiz':
			// A quiz can have a parent of a course, lesson or topic...
			$parent_ids = ebox_course_get_all_parent_step_ids( $course_id, $post->ID );
			if ( ! empty( $parent_ids ) ) {
				foreach ( $parent_ids as $parent_id ) {
					if ( get_post_type( $parent_id ) === ebox_get_post_type_slug( 'topic' ) ) {
						$key = 'topic';
					} elseif ( get_post_type( $parent_id ) === ebox_get_post_type_slug( 'lesson' ) ) {
						$key = 'lesson';

					} else {
						$key = '';
					}

					if ( ! empty( $key ) ) {
						$breadcrumbs[ $key ] = array(
							'permalink' => ebox_get_step_permalink( $parent_id ),
							'title'     => get_the_title( $parent_id ),
						);
					}
				}
			}

			break;
	}

	/**
	 * Filters Breadcrumbs for the ebox post.
	 *
	 * @since 3.0.0
	 *
	 * @param array $breadcrumbs Hierarchy of breadcrumbs.
	 */
	$breadcrumbs = apply_filters( 'ebox_breadcrumbs', $breadcrumbs );

	return $breadcrumbs;

}

/**
 * Gets the essays from a specific quiz attempt - DEPRECATED
 *
 * Look up all the essay responses from a particular quiz attempt
 *
 * @since 3.0.0
 *
 * @deprecated
 *
 * @param int|null $attempt_id Post ID.
 * @param int|null $user_id    User ID.
 *
 * @return array|boolean An array of essay post IDs.
 */
function ebox_get_essays_by_quiz_attempt( $attempt_id = null, $user_id = null ) {

	// Fail gracefully.
	if ( null === $attempt_id ) {
		return false;
	}

	if ( null === $user_id ) {
		$user    = wp_get_current_user();
		$user_id = $user->ID;
	}

	$quiz_attempts = get_user_meta( $user_id, '_ebox-quizzes', true );
	$essays        = array();

	if ( ! $quiz_attempts || empty( $quiz_attempts ) ) {
		return false;
	}

	foreach ( $quiz_attempts as $attempt ) {

		if ( $attempt['quiz'] != $attempt_id || ! isset( $attempt['graded'] ) ) {
			continue;
		}

		foreach ( $attempt['graded'] as $essay ) {
			$essays[] = $essay['post_id'];
		}
	}

	return $essays;

}

/**
 * Gets the essay details.
 *
 * Returns details about essay such as points details and status.
 *
 * @since 3.0.0
 *
 * @param int|null $post_id Post ID of the essay.
 *
 * @return array|false An array of essay details.
 */
function ebox_get_essay_details( $post_id = null ) {

	if ( null === $post_id ) {
		return false;
	}

	$essay = get_post( $post_id );

	if ( ! $essay || empty( $essay ) ) {
		return false;
	}

	$details = array(
		'points' => array(
			'awarded' => 0,
			'total'   => 0,
		),
		'status' => $essay->post_status,
	);

	$quiz_id     = get_post_meta( $post_id, 'quiz_id', true );
	$question_id = get_post_meta( $post_id, 'question_id', true );

	if ( ! empty( $quiz_id ) ) {
		$question_mapper = new WpProQuiz_Model_QuestionMapper();
		$question        = $question_mapper->fetchById( intval( $question_id ), null );
		if ( $question instanceof WpProQuiz_Model_Question ) {

			$submitted_essay_data = ebox_get_submitted_essay_data( $quiz_id, $question_id, $essay );

			$details['points']['total'] = $question->getPoints();

			if ( isset( $submitted_essay_data['points_awarded'] ) ) {
				$details['points']['awarded'] = intval( $submitted_essay_data['points_awarded'] );
			}
		}
	}

	return $details;

}

/**
 * Gets the current lesson progress.
 *
 * Returns stats about a user's current progress within a lesson.
 *
 * @since 3.0.0
 *
 * @param array|null $topics An array of the topic of the modules, contextualized for the user's progress.
 *
 * @return array An array of stats including percentage, completed and total
 */
function ebox_get_lesson_progress( $topics = null ) {

	/**
	 * Filters default values for lesson progress.
	 *
	 * @since 3.0.0
	 *
	 * @param array $lesson_progress_defaults Default values for lesson progress.
	 */
	$progress = apply_filters(
		'ebox_get_lesson_progress_defaults',
		array(
			'percentage' => 0,
			'completed'  => 0,
			'total'      => 0,
		)
	);

	// Fail gracefully, return zero's.
	if ( null === $topics || empty( $topics ) ) {
		return $progress;
	}

	foreach ( $topics as $key => $topic ) {

		$progress['total']++;

		if ( ! empty( $topic->completed ) ) {
			$progress['completed']++;
		}
	}

	if ( 0 === ! $progress['completed'] ) {
		$progress['percentage'] = floor( $progress['completed'] / $progress['total'] * 100 );
	}

	/**
	 * Filters ebox lesson progress.
	 *
	 * @since 3.0.0
	 *
	 * @param array $progress An Associative array of lesson progress with keys total, completed and percentage.
	 * @param array $topics   An array of the topics of the modules.
	 */
	return apply_filters( 'ebox_get_lesson_progress', $progress, $topics );

}

/**
 * Checks if any ebox content type is complete.
 *
 * Works on modules or topics, single function for simpler logic in the templates.
 *
 * @since 3.0.0
 *
 * @global WP_Post $post Global post object.
 *
 * @param int|WP_Post|null $post      `WP_Post` object. Default to global $post.
 * @param int|null         $user_id   The user to check against.
 * @param int|null         $course_id The course to check against (required for reusable content).
 *
 * @return boolean Returns true if the item is complete otherwise false.
 */
function ebox_is_item_complete( $post = null, $user_id = null, $course_id = null ) {

	$complete = false;

	if ( null === $post ) {
		global $post;
	}

	if ( is_numeric( $post ) ) {
		$post = get_post( $post ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- I suppose it's what they wanted.
	}

	if ( null === $user_id ) {
		$user    = wp_get_current_user();
		$user_id = $user->ID;
	}

	if ( null === $course_id ) {
		$course_id = ebox_get_course_id( $post->ID );
	}

	switch ( get_post_type( $post ) ) {
		case ( 'ebox-modules' ):
			$complete = ebox_is_lesson_complete( $user_id, $post->ID, $course_id );
			break;
		case ( 'ebox-topic' ):
			$complete = ebox_is_topic_complete( $user_id, $post->ID, $course_id );
			break;
		case ( 'ebox-quiz' ):
			break;

	}
	/**
	 * Filters whether the ebox content type is complete or not.
	 *
	 * @since 3.0.0
	 *
	 * @param boolean $complete  Whether any ebox content is complete or not.
	 * @param int     $user_id   User ID.
	 * @param int     $post_id   Post ID.
	 * @param int     $course_id Course ID.
	 */
	return apply_filters( 'ebox_is_item_complete', $complete, $user_id, $post->ID, $course_id );

}

/**
 * Gets a label for the content type by post type.
 *
 * Universal function for simpler template logic and reusable templates
 *
 * @since 3.0.0
 *
 * @param string $post_type The post type slug to check.
 * @param array  $args      An array of arguments used to get the content label.
 *
 * @return string The label for the content type based on user settings
 */
function ebox_get_content_label( $post_type = null, $args = null ) {

	if ( $args ) {
		extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract -- Bad idea, but better keep it for now.
	}

	$post_type = ( null === $post_type ? get_post_type() : $post_type );
	$label     = '';

	switch ( $post_type ) {
		case ( 'ebox-courses' ):
			$label = ebox_Custom_Label::get_label( 'course' );
			break;
		case ( 'ebox-modules' ):
			if ( isset( $parent ) ) {
				$label = ebox_Custom_Label::get_label( 'course' );
			} else {
				$label = ebox_Custom_Label::get_label( 'lesson' );
			}
			break;
		case ( 'ebox-topic' ):
			if ( isset( $parent ) ) {
				$label = ebox_Custom_Label::get_label( 'lesson' );
			} else {
				$label = ebox_Custom_Label::get_label( 'topic' );
			}
			break;
	}

	/**
	 * Filters label for the content type by post type. Used to override label settings set by the user.
	 *
	 * @since 3.0.0
	 *
	 * @param string $label     Label for the content type
	 * @param string $post_type Post type
	 */
	return apply_filters( 'ebox_get_content_label', $label, $post_type );

}

/**
 * Gets the assignment progress.
 *
 * Returns details of assignment progress.
 *
 * @since 3.0.0
 *
 * @param array $assignments An array of assignment `WP_Post` objects.
 *
 * @return array An Associative array of assignment statistics with keys total, complete.
 */
function ebox_get_assignment_progress( $assignments = null ) {

	$stats = array(
		'total'    => 0,
		'complete' => 0,
	);

	if ( null === $assignments || empty( $assignments ) ) {

		/**
		 * Filters progress of an assignment.
		 *
		 * @since 3.0.0
		 *
		 * @param array $stats An Associative array of assignment statistics with keys total, complete.
		 */
		return apply_filters( 'ebox_get_assignment_progress', $stats );
	}

	foreach ( $assignments as $assignment ) {

		$stats['total']++;

		if ( ebox_is_assignment_approved_by_meta( $assignment->ID ) ) {
			$stats['complete']++;

		}
	}

	/** This filter is documented in themes/ld30/includes/helpers.php */
	return apply_filters( 'ebox_get_assignment_progress', $stats );

}

/**
 * Gets the Lesson Progress.
 *
 * Return stats about the user's current progress within a lesson.
 *
 * @since 3.0.0
 *
 * @global WP_Post $post Global post object.
 *
 * @param int|WP_Post $post      Lesson `WP_Post` object or post ID. Default to global $post.
 * @param int         $course_id The course ID of the lesson.
 *
 * @return array An array of total steps, completed steps and percentage complete.
 */
function ebox_lesson_progress( $post = null, $course_id = null ) {

	if ( null === $post ) {
		global $post;
	}

	if ( is_numeric( $post ) ) {
		$post = get_post( $post ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- I suppose it's what they wanted.
	}

	if ( null === $course_id ) {
		$course_id = ebox_get_course_id( $post->ID );
	}

	if ( 'ebox-modules' === get_post_type( $post->ID ) ) {
		$lesson_id = $post->ID;
	} else {
		$lesson_id = ebox_course_get_single_parent_step( $course_id, $post->ID );
	}

	$topics = ebox_topic_dots( $lesson_id, false, 'array', null, $course_id );

	if ( ! $topics || empty( $topics ) ) {
		return false;
	}

	$progress = array(
		'total'      => 0,
		'completed'  => 0,
		'percentage' => 0,
	);

	foreach ( $topics as $key => $topic ) {

		$progress['total']++;

		if ( isset( $topic->completed ) && $topic->completed ) {
			$progress['completed']++;
		}
	}

	/**
	 * Note: Since we're not counting quizzes at all in the modules or topics we don't need to count quizzes
	 *
	 * @var [type]
	 */

	if ( 0 !== absint( $progress['completed'] ) ) {
		$progress['percentage'] = floor( $progress['completed'] / $progress['total'] * 100 );
	}

	/**
	 * Filters stats about the user's current progress within a lesson
	 *
	 * @since 3.0.0
	 *
	 * @param array      $progress lesson progress details.
	 * @param int|object $post     Post ID or post object.
	 */
	return apply_filters( 'ebox_lesson_progress', $progress, $post );

}

/**
 * Gets the count of the number of topics and quizzes for a lesson.
 *
 * Counts the number of topics, topic quizzes and lesson quizzes, and returns them as an array.
 *
 * @since 3.0.0
 *
 * @param int|WP_Post $lesson    Lesson `WP_Post` object.
 * @param int         $course_id The course ID of the lesson.
 *
 * @return array Count of topics and quizzes.
 */
function ebox_get_lesson_content_count( $lesson, $course_id ) {

	$count = array(
		'topics'  => 0,
		'quizzes' => 0,
	);

	$quizzes       = ebox_get_lesson_quiz_list( $lesson['post']->ID, get_current_user_id(), $course_id );
	$lesson_topics = ebox_topic_dots( $lesson['post']->ID, false, 'array', null, $course_id );

	if ( ! empty( $quizzes ) ) {
		$count['quizzes'] += count( $quizzes );
	}

	if ( ! empty( $lesson_topics ) ) {

		foreach ( $lesson_topics as $topic ) {

			$count['topics']++;

			$quizzes = ebox_get_lesson_quiz_list( $topic, null, $course_id );

			if ( ! $quizzes || empty( $quizzes ) ) {
				continue;
			}

			$count['quizzes'] += count( $quizzes );

		}
	}

	return $count;

}

/**
 * Outputs lesson row CSS class.
 *
 * Filterable string of class names populated based on lesson status and attributes.
 *
 * @since 3.0.0
 *
 * @param int|WP_Post $lesson     Lesson `WP_Post` object or post ID. Default to global $post.
 * @param int         $has_access Whether the lesson is accessible or not.
 * @param array       $topics     Topics within the Lesson.
 * @param array       $quizzes    Quizzes within the lesson.
 *
 * @return string|void Lesson row CSS class names.
 */
function ebox_lesson_row_class( $lesson = null, $has_access = false, $topics = array(), $quizzes = array() ) {

	if ( null === $lesson ) {
		return;
	}

	/**
	 * Base classes.
	 *
	 * Class ld-item-list-item   -- for styling
	 * Class ld-item-lesson-item -- more specific
	 * Class ld-lesson-item-{post_id}
	 * Class is_sample (if sample)
	 */
	$lesson_class = 'ld-item-list-item ld-item-lesson-item ld-lesson-item-' . $lesson['post']->ID . ' ' . $lesson['sample'];

	$bypass_course_limits_admin_users = ebox_can_user_bypass( get_current_user_id(), 'ebox_course_lesson_not_available' );
	if ( true !== $bypass_course_limits_admin_users ) {
		$lesson_class .= ( ! empty( $lesson['lesson_access_from'] ) || ! $has_access ? ' ebox-not-available' : '' );
	}
	// Complete or not complete.
	$lesson_class .= ' ' . ( 'completed' === $lesson['status'] ? 'ebox-complete' : 'ebox-incomplete' );

	// If expandable or not.
	if ( ! empty( $topics ) || ! empty( $quizzes ) ) {
		$lesson_class .= ' ld-expandable';
	}

	if ( ( isset( $_GET['widget_instance']['widget_instance']['current_lesson_id'] ) && absint( $_GET['widget_instance']['widget_instance']['current_lesson_id'] ) === absint( $lesson['post']->ID ) ) ) {  // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Data is only used for conditional and not further processed.
		$lesson_class .= ' ld-current-lesson';
	}

	/**
	 * Filters lesson row CSS class names.
	 *
	 * @since 3.0.0
	 * @deprecated 4.5.0
	 *
	 * @param string $lesson_class Lesson row CSS class names.
	 * @param object $lesson       The lesson post object to evaluate
	 */
	$lesson_class = apply_filters_deprecated(
		'ebox-lesson-row-class',
		array( $lesson_class, $lesson ),
		'4.5.0',
		'ebox_lesson_row_class'
	);

	/**
	 * Filters lesson row CSS class names.
	 *
	 * @since 4.5.0
	 *
	 * @param string $lesson_class Lesson row CSS class names.
	 * @param object $lesson       The lesson post object to evaluate
	 */
	$lesson_class = apply_filters( 'ebox_lesson_row_class', $lesson_class, $lesson );

	echo esc_attr( $lesson_class );
}

/**
 * Outputs the quiz row CSS classes.
 *
 * @since 3.0.0
 *
 * @param array  $quiz    The quiz details array.
 * @param string $context The context where quiz is shown.
 *
 * @return string Quiz row CSS class.
 */
function ebox_quiz_row_classes( $quiz = null, $context = 'course' ) {

	$classes = array(
		'wrapper' => '',
		'anchor'  => '',
		'preview' => '',
	);

	if ( 'course' === $context ) {
		$classes['wrapper'] .= 'ld-item-list-item ld-item-list-item-quiz';
		$classes['preview'] .= 'ld-item-list-item-preview';
		$classes['anchor']  .= 'ld-item-name ld-primary-color-hover';
	} else {
		$classes['wrapper'] .= 'ld-table-list-item';
		$classes['preview'] .= 'ld-table-list-item-quiz';
		$classes['anchor']  .= 'ld-table-list-item-preview ld-topic-row ld-primary-color-hover';
	}

	$classes['wrapper'] .= ' ' . $quiz['sample'] . ' ' . ( 'completed' === $quiz['status'] ? 'ebox-complete' : 'ebox-incomplete' );

	/**
	 * Filters quiz row CSS classes.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $classes Array of CSS classes with keys wrapper, preview, and anchor.
	 * @param array  $quiz    The quiz array
	 * @param string $context The context where the quiz is being shown.
	 */
	return apply_filters( 'ebox_quiz_row_classes', $classes, $quiz, $context );

}

/**
 * Gets the Lesson attributes.
 *
 * Populates an array of attributes about a lesson, if it's a sample or if it isn't currently available
 *
 * @since 3.0.0
 *
 * @param array $lesson Lesson details array.
 *
 * @return array Attributes including label, icon and class name.
 */
function ebox_get_lesson_attributes( $lesson = null ) {
	$attributes = array();

	if ( ( isset( $lesson['post'] ) ) && ( is_a( $lesson['post'], 'WP_Post' ) ) && ( ebox_get_post_type_slug( 'lesson' ) === $lesson['post']->post_type ) ) {
		$attributes = ebox_get_course_step_attributes( $lesson['post']->ID );

		/**
		 * Filters attributes of a lesson. Used to modify details about a lesson like label, icon and class name.
		 *
		 * @since 3.0.0
		 *
		 * @param array   $attributes Array of lesson attributes.
		 * @param WP_Post $lesson     The lesson post object.
		 */
		return apply_filters( 'ebox_lesson_attributes', $attributes, $lesson['post'] );
	}

	return $attributes;
}

/**
 * Gets the course step attributes.
 *
 * Populates an array of attributes about the step, if it's a sample or if it isn't currently available
 *
 * @since 4.2.0
 *
 * @param int $step_id   Post ID.
 * @param int $course_id Optional. Course ID.
 * @param int $user_id   Optional. User ID.
 *
 * @return array Attributes including label, icon and class name.
 */
function ebox_get_course_step_attributes( $step_id = 0, $course_id = 0, $user_id = 0 ) {

	$attributes = array();

	$step_id   = absint( $step_id );
	$course_id = absint( $course_id );
	$user_id   = absint( $user_id );

	if ( ! empty( $step_id ) ) {
		$step_post_type = get_post_type( $step_id );
		if ( in_array( $step_post_type, ebox_get_post_types( 'course_steps' ), true ) ) {

			if ( empty( $course_id ) ) {
				$course_id = ebox_get_course_id( $step_id );
			}

			if ( empty( $user_id ) ) {
				$user_id = get_current_user_id();
			}

			if ( ebox_get_post_type_slug( 'lesson' ) === $step_post_type ) {

				$show_sample = true;
				$is_sample   = (bool) ebox_is_sample( $step_id );
				if ( ( ! empty( $course_id ) ) && ( ! empty( $user_id ) ) ) {
					$has_access = (bool) ebox_lms_has_access( $course_id, $user_id );
					if ( ( true === $has_access ) && ( true === $is_sample ) ) {
						$show_sample = false;

						/**
						 * Filters attributes of the step. Used to modify details about a lesson like label, icon and class name
						 *
						 * @since 4.2.0
						 *
						 * @param bool $show_sample True to show sample attributes. Default is false.
						 * @param int  $step_id     The step ID.
						 * @param int  $course_id   The step course ID.
						 * @param int  $user_id     The user ID.
						 */
						$show_sample = (bool) apply_filters( 'ebox_course_step_attributes_show_sample', $show_sample, $step_id, $course_id, $user_id );
					}
				}
				if ( ( true === $is_sample ) && ( true === $show_sample ) ) {
					$attributes[] = array(
						// translators: placeholder: Lesson.
						'label' => sprintf( esc_html_x( 'Sample %s', 'placeholder: Lesson', 'ebox' ), ebox_Custom_Label::get_label( 'lesson' ) ),
						'icon'  => 'ld-icon-unlocked',
						'class' => 'ld-status-unlocked ld-primary-color',
					);
				}
			}

			$bypass_course_limits_admin_users = ebox_can_user_bypass( get_current_user_id(), 'ebox_course_lesson_not_available' );
			if ( true !== $bypass_course_limits_admin_users ) {

				$step_access_from = ld_lesson_access_from( $step_id, $user_id, $course_id );

				if ( ! empty( $step_access_from ) ) {
					$attributes[] = array(
						'label' => sprintf(
							// translators: placeholder: Date when lesson will be available.
							esc_html_x( 'Available on %s', 'placeholder: Date when lesson will be available', 'ebox' ),
							ebox_adjust_date_time_display( $step_access_from )
						),
						'class' => 'ld-status-waiting ld-tertiary-background',
						'icon'  => 'ld-icon-calendar',
					);
				}
			}

			/**
			 * Filters attributes of the step. Used to modify details about a lesson like label, icon and class name
			 *
			 * @since 3.0.0
			 *
			 * @param array $attributes Array of lesson attributes.
			 * @param int   $step_id    The step ID.
			 * @param int   $course_id  The step course ID.
			 * @param int   $user_id    The user ID.
			 */
			$attributes = apply_filters( 'ebox_course_step_attributes', $attributes, $step_id, $course_id, $user_id );
		}
	}

	return $attributes;
}

/**
 * Gets the template Part.
 *
 * Function to facilitate including sub-templates.
 *
 * @since 3.0.0
 *
 * @param string  $filepath The path to the template file to include.
 * @param array   $args    Any variables to pass along to the template.
 * @param boolean $echo    Whether to print or return the template output. Default is false.
 *
 * @return ($echo is false ? string : void )
 */
function ebox_get_template_part( $filepath, $args = null, $echo = false ) {
	// Keep this in the logic from LD core to allow the same overrides.
	$filepath = ebox_LMS::get_template( $filepath, $args, null, true );

	if ( ( ! empty( $filepath ) ) && ( file_exists( $filepath ) ) ) {

		ob_start();
		extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract -- Bad idea, but better keep it for now.
		include $filepath;
		$output = ob_get_clean();

		if ( $echo ) {
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Outputting HTML from templates
		} else {
			return $output;
		}
	}
}

/**
 * Gets the ebox content wrapper CSS class.
 *
 * Filterable function to add a class to all ebox content, allows conditional adding of additional classes
 *
 * @since 3.0.0
 *
 * @global WP_Post $post Global post object.
 *
 * @param int|WP_Post $post `WP_Post` object or post ID. Default to global $post.
 *
 * @return string Wrapper CSS class.
 */
function ebox_get_wrapper_class( $post = null ) {

	if ( null === $post ) {
		global $post;
	}

	if ( is_numeric( $post ) ) {
		$post = get_post( $post ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- I suppose it's what they wanted.
	}

	/**
	 * Filters ebox content wrapper class.
	 *
	 * @since 3.0.0
	 *
	 * @param string     $wrapper_class Wrapper class.
	 * @param int|object $post          Post ID or post object.
	 */
	return apply_filters( 'ebox_wrapper_class', 'ebox-wrapper', $post );

}

/**
 * Outputs the ebox content wrapper CSS class.
 *
 * Same as ebox_get_wrapper_class only outputs it.
 *
 * @since 3.0.0
 *
 * @param  int|WP_Post $post `WP_Post` object or post ID. Default to global $post.
 */
function ebox_the_wrapper_class( $post = null ) {

	if ( null === $post ) {
		global $post;
	}

	if ( is_numeric( $post ) ) {
		$post = get_post( $post ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- I suppose it's what they wanted.
	}

	echo esc_attr( ebox_get_wrapper_class( $post ) );

}

/**
 * Gets or prints the ebox status icon.
 *
 * Output the status icon for a course element. Simplifies template logic.
 *
 * @since 3.0.0
 *
 * @param string  $status    The current item's status, either not-completed or completed (based on current logic and labeling).
 * @param string  $post_type What post type we're checking against so this can be used for courses, modules, topics, and quizzes.
 * @param array   $args      The arguments to get the status icon.
 * @param boolean $echo      True to print the output and false to return the output.
 *
 * @return void|string Returns the status icon markup if echo is false.
 */
function ebox_status_icon( $status = 'not-completed', $post_type = null, $args = null, $echo = false ) {

	$class = 'ld-status-icon ';

	$markup = '';

	if ( 'ebox-quiz' !== $post_type ) {

		switch ( $status ) {
			case ( 'not-completed' ):
				$class .= 'ld-status-incomplete';
				$markup = '<div class="' . $class . '"></div>';
				break;
			case ( 'completed' ):
				$class .= 'ld-status-complete ld-secondary-background';
				$markup = '<div class="' . $class . '"><span class="ld-icon-checkmark ld-icon"></span></div>';
				break;
			case ( 'progress' ):
			case ( 'in-progress' ):
				$class .= 'ld-status-in-progress ld-secondary-in-progress-icon';
				$markup = '<div class="' . $class . '"></div>';
				break;
			case ( 'not-started' ):
			default:
				$class .= 'ld-status-incomplete';
				$markup = '<div class="' . $class . '"></div>';
				break;
		}
	} else {

		switch ( $status ) {
			case ( 'notcompleted' ):
			case ( 'failed' ):
				$class .= 'ld-quiz-incomplete';
				$markup = '<div class="' . $class . '"><span class="ld-icon ld-icon-quiz"></span></div>';
				break;
			case ( 'completed' ):
			case ( 'passed' ):
				$class .= 'ld-quiz-complete ld-secondary-color';
				$markup = '<div class="' . $class . '"><span class="ld-icon ld-icon-quiz"></span></div>';
				break;
			case ( 'pending' ):
				$class .= 'ld-quiz-pending';
				$markup = '<div class="' . $class . '"><span class="ld-icon ld-icon-quiz"></span></div>';
				break;
		}
	}

	/**
	 * Filters status icon markup for the course element.
	 *
	 * @since 3.0.0
	 *
	 * @param string $markup    Icon markup.
	 * @param string $status    The current item's status.
	 * @param string $post_type What post type we're checking against so this can be used for courses, modules, topics, and quizzes.
	 * @param array   $args      The arguments to get the status icon.
	 * @param boolean $echo      True to print the output and false to return the output.
	 */
	$markup = apply_filters( 'ebox_status_icon', $markup, $status, $post_type, $args, $echo );

	if ( $echo ) {
		echo wp_kses_post( $markup );
	}

	return $markup;

}

/**
 * Gets or prints the ebox status bubble.
 *
 * Output the status bubble of an element. Simplifies template logic.
 *
 * @since 3.0.0
 *
 * @param string  $status  The current item's status, either incomplete or complete.
 * @param string  $context The current context the bubble is being output, used for color management.
 * @param boolean $echo    True to print the output and false to return the output.
 *
 * @return void|string Returns the status bubble markup if echo is false.
 */
function ebox_status_bubble( $status = 'incomplete', $context = null, $echo = true ) {

	$bubble = '';

	switch ( $status ) {
		case 'In Progress':
		case 'progress':
		case 'incomplete':
			$bubble = '<div class="ld-status ld-status-progress ld-primary-background">' . esc_html_x( 'In Progress', 'In Progress item status', 'ebox' ) . '</div>';
			break;

		case 'complete':
		case 'completed':
		case 'Completed':
			$bubble = '<div class="ld-status ld-status-complete ld-secondary-background">' . esc_html_x( 'Complete', 'In Progress item status', 'ebox' ) . '</div>';
			break;

		case 'graded':
			$bubble = '<div class="ld-status ld-status-complete ld-secondary-background">' . esc_html_x( 'Graded', 'In Progress item status', 'ebox' ) . '</div>';
			break;

		case 'not_graded':
			$bubble = '<div class="ld-status ld-status-progress ld-primary-background">' . esc_html_x( 'Not Graded', 'In Progress item status', 'ebox' ) . '</div>';
			break;

		case '':
		default:
			break;
	}

	/**
	 * Filters item status bubble markup.
	 *
	 * @since 3.0.0
	 *
	 * @param string $bubble Status bubble markup.
	 * @param string $status The current item status
	 */
	$bubble = apply_filters( 'ebox_status_bubble', $bubble, $status );

	if ( $echo ) {
		echo wp_kses_post( $bubble );
	} else {
		return $bubble;
	}

}

/**
 * Looks like it was never used. Should be deprecated I guess.
 */
function ebox_test_admin_icon() {
	?>
	<style type="text/css">
		#adminmenu #toplevel_page_ebox-lms div.wp-menu-image:before {
			background: url('<?php echo esc_url( ebox_LMS_PLUGIN_URL . '/themes/ld30/assets/iconfont/admin-icons/browser-checkmark.svg' ); ?>') center center no-repeat;
			content: '';
			opacity: 0.7;
		}
	</style>
	<?php
}

/**
 * Gets the course assignments.
 *
 * Returns `WP_query` object to get course assignments.
 *
 * @since 3.0.0
 *
 * @param int|null $course_id Course ID.
 * @param int|null $user_id   User ID.
 *
 * @return WP_Query|false Return `WP_Query` object if there are assignments in course otherwise false.
 */
function ebox_get_course_assignments( $course_id = null, $user_id = null ) {

	if ( null === $course_id ) {
		$course_id = get_the_ID();
	}

	if ( null === $user_id ) {
		$user    = wp_get_current_user();
		$user_id = $user->ID;
	}

	$args = array(
		'posts_per_page' => -1,
		'post_type'      => 'ebox-assignment',
		'meta_query'     => array(
			'relation' => 'AND',
			array(
				'key'   => 'course_id',
				'value' => $course_id,
			),
			array(
				'key'   => 'user_id',
				'value' => $user_id,
			),
		),
	);

	$assignments = new WP_Query( $args );

	if ( ! $assignments->have_posts() ) {
		return false;
	}

	return $assignments;

}

add_action( 'wp_enqueue_scripts', 'ebox_30_remove_legacy_css' );
/**
 * Removes the legacy css.
 *
 * Fires on `wp_enqueue_scripts` hook.
 *
 * @since 3.1.4
 */
function ebox_30_remove_legacy_css() {

	$styles = array(
		'ebox_front_css',
		'ebox_style',
		'ebox_quiz_front',
	);

	foreach ( $styles as $handle ) {
		wp_dequeue_style( $handle );
	}

}

/**
 * Gets the user statistics.
 *
 * @since 3.0.0
 *
 * @param int|null $user_id The ID of the user. Defaults to current logged in user.
 *
 * @return array An array of user statistics.
 */
function ebox_get_user_stats( $user_id = null ) {

	if ( null === $user_id ) {
		$user    = wp_get_current_user();
		$user_id = $user->ID;
	} else {
		$user_id = absint( $user_id );
	}

	$progress = get_user_meta( $user_id, '_ebox-course_progress' );

	$stats = array(
		'courses'      => 0,
		'completed'    => 0,
		'points'       => ebox_get_user_course_points( $user_id ),
		'certificates' => ebox_get_certificate_count( $user_id ),
	);

	$courses = ebox_user_get_enrolled_courses( $user_id, array(), true );

	if ( $courses ) {

		$stats['courses'] = count( $courses );

		foreach ( $courses as $course_id ) {

			$progress = ebox_course_progress(
				array(
					'user_id'   => $user_id,
					'course_id' => $course_id,
					'array'     => true,
				)
			);

			if ( 100 === absint( $progress['percentage'] ) ) {
				$stats['completed']++;
			}
		}
	}

	/**
	 * Filters ebox user stats. Used to modify user details like courses, points, certificates.
	 *
	 * @since 3.0.0
	 *
	 * @param array $stats   User statistics.
	 * @param int   $user_id User ID.
	 */
	$stats = apply_filters_deprecated(
		'ebox-get-user-stats',
		array( $stats, $user_id ),
		'4.5.0',
		'ebox_user_statistics'
	);

	/**
	 * Filters ebox user stats. Used to modify user details like courses, points, certificates.
	 *
	 * @since 4.5.0
	 *
	 * @param array $stats   User statistics.
	 * @param int   $user_id User ID.
	 */
	return apply_filters( 'ebox_user_statistics', $stats, $user_id );
}

global $ebox_in_focus_mode;
$ebox_in_focus_mode = false;

add_filter( 'template_include', 'ebox_30_focus_mode', 99 );

/**
 * Returns the focus template path if the focus mode is enabled.
 *
 * Fires on `template_include` hook.
 *
 * @since 3.0.0
 *
 * @param string $template The path of the template to include.
 *
 * @return string The path of the template to include.
 */
function ebox_30_focus_mode( $template ) {

	$focus_mode = ebox_Settings_Section::get_section_setting( 'ebox_Settings_Theme_LD30', 'focus_mode_enabled' );
	if ( 'yes' !== $focus_mode ) {
		/**
		 * Seems this is only used here and above this function.
		 * Not sure what setting this global to true controls. Why set to true if FM is not enabled.
		 */
		global $ebox_in_focus_mode;
		$ebox_in_focus_mode = true;
	} else {
		$post_types = array(
			'ebox-modules',
			'ebox-topic',
			'ebox-assignment',
			'ebox-quiz',
		);

		if ( in_array( get_post_type(), $post_types, true ) && is_singular( $post_types ) ) {
			$focus_index_template = ebox_LMS::get_template( 'focus/index.php', null, false, true );
			if ( empty( $focus_index_template ) ) {
				$active_theme_base_dir = ebox_Theme_Register::get_active_theme_base_dir();
				if ( ( ! empty( $active_theme_base_dir ) ) && ( file_exists( trailingslashit( $active_theme_base_dir ) . 'templates/focus/index.php' ) ) ) {
					$focus_index_template = trailingslashit( $active_theme_base_dir ) . 'templates/focus/index.php';
				}
			}

			/**
			 * Allow override of the Focus Mode index template.
			 *
			 * @since 3.2.0
			 *
			 * @param string $focus_index_template Path to Focus Mode Index Template.
			 */
			$template = apply_filters( 'ebox_ld30_focus_mode_template_index', $focus_index_template );
		}
	}

	return $template;
}

add_filter( 'ebox_template_filename', 'ebox_30_template_filename', 1000, 5 );

/**
 * Gets the template file path by name.
 * Fires on `ebox_template_filename` hook.
 *
 * @since 3.0.3
 *
 * @param string  $filepath         Template file path.
 * @param string  $name            Template name.
 * @param array   $args            Template data.
 * @param boolean $echo            Whether to echo the template output or not.
 * @param boolean $return_file_path Whether to return file or path or not.
 *
 * @return string Returns template file path.
 */
function ebox_30_template_filename( $filepath = '', $name = '', $args = array(), $echo = false, $return_file_path = false ) {
	/**
	 * The Transition Routes array contains the legacy template filename as the key
	 * and the value is the alternate filename to be used.
	 */
	$transition_template_filenames = array(
		// LD Core templates.
		'course.php'                                  => 'course.php',
		'lesson.php'                                  => 'lesson.php',
		'topic.php'                                   => 'topic.php',
		'quiz.php'                                    => 'quiz.php',

		// LD Core Shortcode templates.
		'profile.php'                                 => 'shortcodes/profile.php',
		'ld_course_list.php'                          => 'shortcodes/ld_course_list.php',
		'course_list_template.php'                    => 'shortcodes/course_list_template.php',
		'ld_topic_list.php'                           => 'shortcodes/ld_topic_list.php',
		'user_teams_shortcode.php'                   => 'shortcodes/user_teams_shortcode.php',
		'course_content_shortcode.php'                => 'shortcodes/course_content_shortcode.php',

		// LD Core Widgets.
		'course_navigation_widget.php'                => 'widgets/course-navigation.php',
		'course_progress_widget.php'                  => 'widgets/course-progress.php',

		// LD Core Messages.
		'ebox_course_prerequisites_message.php'  => 'modules/messages/prerequisites.php',
		'ebox_course_points_access_message.php'  => 'modules/messages/course-points.php',
		'ebox_course_lesson_not_available.php'   => 'modules/messages/lesson-not-available.php',

		// LD Core Modules.
		'ebox_lesson_video.php'                  => 'modules/lesson-video.php',

		'ebox_lesson_assignment_upload_form.php' => false,

	);

	if ( ( ! empty( $filepath ) ) && ( isset( $transition_template_filenames[ $filepath ] ) ) ) {
		$filepath = $transition_template_filenames[ $filepath ];
	}

	return $filepath;
}

add_action( 'wp_enqueue_scripts', 'ebox_30_template_assets' );

/**
 * Enqueues the ld30 theme template assets.
 *
 * Fires on `wp_enqueue_scripts` hook.
 *
 * @since 3.0.0
 */
function ebox_30_template_assets() {
	// If this function is being called then we are the active theme.
	$theme_template_url = ebox_Theme_Register::get_active_theme_base_url();

	// These assets really should be moved to the /templates directory since they are part of the theme.
	wp_register_style( 'ebox-front', $theme_template_url . '/assets/css/ebox' . ebox_min_asset() . '.css', array(), ebox_SCRIPT_VERSION_TOKEN );
	wp_register_script( 'ebox-front', $theme_template_url . '/assets/js/ebox.js', array( 'jquery' ), ebox_SCRIPT_VERSION_TOKEN, true );

	if ( get_post_type() === ebox_get_post_type_slug( 'exam' ) ) {
		wp_register_script( 'ebox-exam', $theme_template_url . '/assets/js/ebox-exam' . ebox_min_asset() . '.js', array(), ebox_SCRIPT_VERSION_TOKEN, true );
	}

	wp_register_style( 'ebox-quiz-front', $theme_template_url . '/assets/css/ebox.quiz.front' . ebox_min_asset() . '.css', array(), ebox_SCRIPT_VERSION_TOKEN );

	wp_enqueue_style( 'ebox-front' );
	wp_style_add_data( 'ebox-front', 'rtl', 'replace' );
	wp_enqueue_script( 'ebox-front' );

	if ( get_post_type() === ebox_get_post_type_slug( 'exam' ) ) {
		wp_enqueue_script( 'ebox-exam' );
	}

	wp_localize_script(
		'ebox-front',
		'ldVars',
		array(
			'postID'      => get_the_ID(),
			'videoReqMsg' => esc_html__( 'You must watch the video before accessing this content', 'ebox' ),
			'ajaxurl'     => admin_url( 'admin-ajax.php' ),
		)
	);

	if ( get_post_type() == 'ebox-quiz' ) {
		wp_enqueue_style( 'ebox-quiz-front' );
		wp_style_add_data( 'ebox-quiz-front', 'rtl', 'replace' );
	}

	$dequeue_styles = array(
		'ebox_pager_css',
		'ebox_template_style_css',
	);

	foreach ( $dequeue_styles as $style ) {
		wp_dequeue_style( $style );
	}

}

add_action( 'enqueue_block_editor_assets', 'ebox_30_editor_scripts' );
/**
 * Enqueues the ld30 theme editor scripts.
 *
 * Fires on `enqueue_block_editor_assets` hook.
 *
 * @since 3.0.0
 */
function ebox_30_editor_scripts() {
	ebox_30_template_assets();
}

add_action( 'init', 'ebox_30_nav_menus' );
/**
 * Registers the ld30 theme nav menus.
 *
 * Fires on `init` hook.
 *
 * @since 3.0.0
 */
function ebox_30_nav_menus() {

	register_nav_menus(
		/**
		 * Filters nav menu locations
		 *
		 * @since 3.0.0
		 *
		 * @param array $locations An Associative array of menu location identifiers (like a slug) and descriptive text.
		 */
		apply_filters(
			'ebox_30_nav_menus',
			array(
				'ld30_focus_mode' => esc_html__( 'ebox: Focus Mode Dropdown', 'ebox' ),
			)
		)
	);

}

/**
 * Gets the ld30 theme position of the sidebar.
 *
 * @since 4.1.0
 *
 * @return string A string with the sidebar position.
 */
function ebox_30_get_focus_mode_sidebar_position() {

	$sidebar_position = ebox_Settings_Section::get_section_setting( 'ebox_Settings_Theme_LD30', 'focus_mode_sidebar_position', 'default' );

	if ( is_rtl() ) {
		$sidebar_position = 'right' === $sidebar_position ? 'left' : $sidebar_position;
		return 'ld-focus-position-rtl-' . $sidebar_position;
	}

	$sidebar_position = 'left' === $sidebar_position ? 'right' : $sidebar_position;
	return 'ld-focus-position-' . $sidebar_position;

}

/**
 * Gets the ld30 theme arrow class of the sidebar.
 *
 * @since 4.1.0
 *
 * @return string A string with the sidebar arrow class.
 */
function ebox_30_get_focus_mode_sidebar_arrow_class() {
	$arrows = array(
		'ld-focus-position-rtl-default' => 'ld-icon-arrow-right',
		'ld-focus-position-rtl-left'    => 'ld-icon-arrow-left',
		'ld-focus-position-default'     => 'ld-icon-arrow-left',
		'ld-focus-position-right'       => 'ld-icon-arrow-right',
	);

	$sidebar_position = ebox_30_get_focus_mode_sidebar_position();

	return isset( $arrows[ $sidebar_position ] ) ? $arrows[ $sidebar_position ] : 'ld-icon-arrow-left';

}

/**
 * Gets the ld30 theme custom focus menu items.
 *
 * @since 3.0.0
 *
 * @return array|false An array of menu items, otherwise false.
 */
function ebox_30_get_custom_focus_menu_items() {

	$theme_locations = get_nav_menu_locations();

	if ( ! isset( $theme_locations['ld30_focus_mode'] ) ) {
		return false;
	}

	$menu_obj = get_term( $theme_locations['ld30_focus_mode'], 'nav_menu' );

	if ( ! $menu_obj || ! isset( $menu_obj->term_id ) ) {
		return false;
	}

	return wp_get_nav_menu_items( $menu_obj->term_id );

}

add_action( 'wp_enqueue_scripts', 'ebox_30_custom_colors' );

/**
 * Enqueues the ld30 theme custom colors style.
 *
 * Fires on `wp_enqueue_scripts` hook.
 *
 * @since 3.0.0
 */
function ebox_30_custom_colors() {

	/**
	 * Filters default custom colors used in settings to set accent color, progress color, and notifications settings.
	 *
	 * @since 3.0.0
	 *
	 * @param array $custom_colors An Associative array of color name and values in hex code.
	 */
	$colors = apply_filters(
		'ebox_30_custom_colors',
		array(
			'primary'   => ebox_Settings_Section::get_section_setting( 'ebox_Settings_Theme_LD30', 'color_primary' ),
			'secondary' => ebox_Settings_Section::get_section_setting( 'ebox_Settings_Theme_LD30', 'color_secondary' ),
			'tertiary'  => ebox_Settings_Section::get_section_setting( 'ebox_Settings_Theme_LD30', 'color_tertiary' ),
		)
	);

	/**
	 * Filters responsive videos setting value. Override the value of responsive video set in settings.
	 *
	 * @since 3.0.1
	 *
	 * @param string|int $responsive_video_setting Value is yes if enabled and empty string if disabled. Default is set to 0.
	 */
	$responsive_video = apply_filters( 'ebox_30_responsive_video', ebox_Settings_Section::get_section_setting( 'ebox_Settings_Theme_LD30', 'responsive_video_enabled' ) );

	/**
	 * Filters focus mode width setting value. Override the focus mode width set in settings.
	 *
	 * @since 3.0.5
	 *
	 * @param string $focus_width_setting Focus mode width. Default value is default.
	 */
	$focus_width = apply_filters( 'ebox_30_focus_mode_width', ebox_Settings_Section::get_section_setting( 'ebox_Settings_Theme_LD30', 'focus_mode_content_width' ) );

	ob_start();
	if ( ( isset( $colors['primary'] ) ) && ( ! empty( $colors['primary'] ) ) && ( LD_30_COLOR_PRIMARY != $colors['primary'] ) ) {
		// Convert HEX to RGB for use with rgba().
		list( $r, $g, $b ) = sscanf( $colors['primary'], '#%02x%02x%02x' );

		$primary_rgb = "$r, $g, $b";
		?>
		.ebox-wrapper .ld-item-list .ld-item-list-item.ld-is-next,
		.ebox-wrapper .wpProQuiz_content .wpProQuiz_questionListItem label:focus-within {
			border-color: <?php echo esc_attr( $colors['primary'] ); ?>;
		}

		/*
		.ebox-wrapper a:not(.ld-button):not(#quiz_continue_link):not(.ld-focus-menu-link):not(.btn-blue):not(#quiz_continue_link):not(.ld-js-register-account):not(#ld-focus-mode-course-heading):not(#btn-join):not(.ld-item-name):not(.ld-table-list-item-preview):not(.ld-lesson-item-preview-heading),
		 */

		.ebox-wrapper .ld-breadcrumbs a,
		.ebox-wrapper .ld-lesson-item.ld-is-current-lesson .ld-lesson-item-preview-heading,
		.ebox-wrapper .ld-lesson-item.ld-is-current-lesson .ld-lesson-title,
		.ebox-wrapper .ld-primary-color-hover:hover,
		.ebox-wrapper .ld-primary-color,
		.ebox-wrapper .ld-primary-color-hover:hover,
		.ebox-wrapper .ld-primary-color,
		.ebox-wrapper .ld-tabs .ld-tabs-navigation .ld-tab.ld-active,
		.ebox-wrapper .ld-button.ld-button-transparent,
		.ebox-wrapper .ld-button.ld-button-reverse,
		.ebox-wrapper .ld-icon-certificate,
		.ebox-wrapper .ld-login-modal .ld-login-modal-login .ld-modal-heading,
		#wpProQuiz_user_content a,
		.ebox-wrapper .ld-item-list .ld-item-list-item a.ld-item-name:hover,
		.ebox-wrapper .ld-focus-comments__heading-actions .ld-expand-button,
		.ebox-wrapper .ld-focus-comments__heading a,
		.ebox-wrapper .ld-focus-comments .comment-respond a,
		.ebox-wrapper .ld-focus-comment .ld-comment-reply a.comment-reply-link:hover,
		.ebox-wrapper .ld-expand-button.ld-button-alternate {
			color: <?php echo esc_attr( $colors['primary'] ); ?> !important;
		}

		.ebox-wrapper .ld-focus-comment.bypostauthor>.ld-comment-wrapper,
		.ebox-wrapper .ld-focus-comment.role-team_leader>.ld-comment-wrapper,
		.ebox-wrapper .ld-focus-comment.role-administrator>.ld-comment-wrapper {
			background-color:rgba(<?php echo esc_attr( $primary_rgb ); ?>, 0.03) !important;
		}


		.ebox-wrapper .ld-primary-background,
		.ebox-wrapper .ld-tabs .ld-tabs-navigation .ld-tab.ld-active:after {
			background: <?php echo esc_attr( $colors['primary'] ); ?> !important;
		}



		.ebox-wrapper .ld-course-navigation .ld-lesson-item.ld-is-current-lesson .ld-status-incomplete,
		.ebox-wrapper .ld-focus-comment.bypostauthor:not(.ptype-ebox-assignment) >.ld-comment-wrapper>.ld-comment-avatar img,
		.ebox-wrapper .ld-focus-comment.role-team_leader>.ld-comment-wrapper>.ld-comment-avatar img,
		.ebox-wrapper .ld-focus-comment.role-administrator>.ld-comment-wrapper>.ld-comment-avatar img {
			border-color: <?php echo esc_attr( $colors['primary'] ); ?> !important;
		}



		.ebox-wrapper .ld-loading::before {
			border-top:3px solid <?php echo esc_attr( $colors['primary'] ); ?> !important;
		}

		.ebox-wrapper .ld-button:hover:not(.ebox-link-previous-incomplete):not(.ld-button-transparent),
		#ebox-tooltips .ld-tooltip:after,
		#ebox-tooltips .ld-tooltip,
		.ebox-wrapper .ld-primary-background,
		.ebox-wrapper .btn-join,
		.ebox-wrapper #btn-join,
		.ebox-wrapper .ld-button:not(.ld-button-reverse):not(.ebox-link-previous-incomplete):not(.ld-button-transparent),
		.ebox-wrapper .ld-expand-button,
		.ebox-wrapper .wpProQuiz_content .wpProQuiz_button:not(.wpProQuiz_button_reShowQuestion):not(.wpProQuiz_button_restartQuiz),
		.ebox-wrapper .wpProQuiz_content .wpProQuiz_button2,
		.ebox-wrapper .ld-focus .ld-focus-sidebar .ld-course-navigation-heading,
		.ebox-wrapper .ld-focus .ld-focus-sidebar .ld-focus-sidebar-trigger,
		.ebox-wrapper .ld-focus-comments .form-submit #submit,
		.ebox-wrapper .ld-login-modal input[type='submit'],
		.ebox-wrapper .ld-login-modal .ld-login-modal-register,
		.ebox-wrapper .wpProQuiz_content .wpProQuiz_certificate a.btn-blue,
		.ebox-wrapper .ld-focus .ld-focus-header .ld-user-menu .ld-user-menu-items a,
		#wpProQuiz_user_content table.wp-list-table thead th,
		#wpProQuiz_overlay_close,
		.ebox-wrapper .ld-expand-button.ld-button-alternate .ld-icon {
			background-color: <?php echo esc_attr( $colors['primary'] ); ?> !important;
		}

		.ebox-wrapper .ld-focus .ld-focus-header .ld-user-menu .ld-user-menu-items:before {
			border-bottom-color: <?php echo esc_attr( $colors['primary'] ); ?> !important;
		}

		.ebox-wrapper .ld-button.ld-button-transparent:hover {
			background: transparent !important;
		}

		.ebox-wrapper .ld-focus .ld-focus-header .ebox-mark-complete .ebox_mark_complete_button,
		.ebox-wrapper .ld-focus .ld-focus-header #ebox-mark-complete #ebox_mark_complete_button,
		.ebox-wrapper .ld-button.ld-button-transparent,
		.ebox-wrapper .ld-button.ld-button-alternate,
		.ebox-wrapper .ld-expand-button.ld-button-alternate {
			background-color:transparent !important;
		}

		.ebox-wrapper .ld-focus-header .ld-user-menu .ld-user-menu-items a,
		.ebox-wrapper .ld-button.ld-button-reverse:hover,
		.ebox-wrapper .ld-alert-success .ld-alert-icon.ld-icon-certificate,
		.ebox-wrapper .ld-alert-warning .ld-button:not(.ebox-link-previous-incomplete),
		.ebox-wrapper .ld-primary-background.ld-status {
			color:white !important;
		}

		.ebox-wrapper .ld-status.ld-status-unlocked {
			background-color: <?php echo esc_attr( ebox_hex2rgb( $colors['primary'], '0.2' ) ); ?> !important;
			color: <?php echo esc_attr( $colors['primary'] ); ?> !important;
		}

		.ebox-wrapper .wpProQuiz_content .wpProQuiz_addToplist {
			background-color: <?php echo esc_attr( ebox_hex2rgb( $colors['primary'], '0.1' ) ); ?> !important;
			border: 1px solid <?php echo esc_attr( $colors['primary'] ); ?> !important;
		}

		.ebox-wrapper .wpProQuiz_content .wpProQuiz_toplistTable th {
			background: <?php echo esc_attr( $colors['primary'] ); ?> !important;
		}

		.ebox-wrapper .wpProQuiz_content .wpProQuiz_toplistTrOdd {
			background-color: <?php echo esc_attr( ebox_hex2rgb( $colors['primary'], '0.1' ) ); ?> !important;
		}

		.ebox-wrapper .wpProQuiz_content .wpProQuiz_reviewDiv li.wpProQuiz_reviewQuestionTarget {
			background-color: <?php echo esc_attr( $colors['primary'] ); ?> !important;
		}
		.ebox-wrapper .wpProQuiz_content .wpProQuiz_time_limit .wpProQuiz_progress {
			background-color: <?php echo esc_attr( $colors['primary'] ); ?> !important;
		}
		<?php
	}

	if ( ( isset( $colors['secondary'] ) ) && ( ! empty( $colors['secondary'] ) ) && ( LD_30_COLOR_SECONDARY != $colors['secondary'] ) ) {
		?>

		.ebox-wrapper #quiz_continue_link,
		.ebox-wrapper .ld-secondary-background,
		.ebox-wrapper .ebox_mark_complete_button,
		.ebox-wrapper #ebox_mark_complete_button,
		.ebox-wrapper .ld-status-complete,
		.ebox-wrapper .ld-alert-success .ld-button,
		.ebox-wrapper .ld-alert-success .ld-alert-icon {
			background-color: <?php echo esc_attr( $colors['secondary'] ); ?> !important;
		}

		.ebox-wrapper .wpProQuiz_content a#quiz_continue_link {
			background-color: <?php echo esc_attr( $colors['secondary'] ); ?> !important;
		}

		.ebox-wrapper .course_progress .sending_progress_bar {
			background: <?php echo esc_attr( $colors['secondary'] ); ?> !important;
		}

		.ebox-wrapper .wpProQuiz_content .wpProQuiz_button_reShowQuestion:hover, .ebox-wrapper .wpProQuiz_content .wpProQuiz_button_restartQuiz:hover {
			background-color: <?php echo esc_attr( $colors['secondary'] ); ?> !important;
			opacity: 0.75;
		}

		.ebox-wrapper .ld-secondary-color-hover:hover,
		.ebox-wrapper .ld-secondary-color,
		.ebox-wrapper .ld-focus .ld-focus-header .ebox-mark-complete .ebox_mark_complete_button,
		.ebox-wrapper .ld-focus .ld-focus-header #ebox-mark-complete #ebox_mark_complete_button,
		.ebox-wrapper .ld-focus .ld-focus-header .ebox-mark-complete:after {
			color: <?php echo esc_attr( $colors['secondary'] ); ?> !important;
		}

		.ebox-wrapper .ld-secondary-in-progress-icon {
			border-left-color: <?php echo esc_attr( $colors['secondary'] ); ?> !important;
			border-top-color: <?php echo esc_attr( $colors['secondary'] ); ?> !important;
		}

		.ebox-wrapper .ld-alert-success {
			border-color: <?php echo esc_attr( $colors['secondary'] ); ?>;
			background-color: transparent !important;
			color: <?php echo esc_attr( $colors['secondary'] ); ?>;
		}

		.ebox-wrapper .wpProQuiz_content .wpProQuiz_reviewQuestion li.wpProQuiz_reviewQuestionSolved,
		.ebox-wrapper .wpProQuiz_content .wpProQuiz_box li.wpProQuiz_reviewQuestionSolved {
			background-color: <?php echo esc_attr( $colors['secondary'] ); ?> !important;
		}

		.ebox-wrapper .wpProQuiz_content  .wpProQuiz_reviewLegend span.wpProQuiz_reviewColor_Answer {
			background-color: <?php echo esc_attr( $colors['secondary'] ); ?> !important;
		}

		<?php
	}

	if ( ( isset( $colors['tertiary'] ) ) && ( ! empty( $colors['tertiary'] ) ) && ( LD_30_COLOR_TERTIARY != $colors['tertiary'] ) ) {
		?>

		.ebox-wrapper .ld-alert-warning {
			background-color:transparent;
		}

		.ebox-wrapper .ld-status-waiting,
		.ebox-wrapper .ld-alert-warning .ld-alert-icon {
			background-color: <?php echo esc_attr( $colors['tertiary'] ); ?> !important;
		}

		.ebox-wrapper .ld-tertiary-color-hover:hover,
		.ebox-wrapper .ld-tertiary-color,
		.ebox-wrapper .ld-alert-warning {
			color: <?php echo esc_attr( $colors['tertiary'] ); ?> !important;
		}

		.ebox-wrapper .ld-tertiary-background {
			background-color: <?php echo esc_attr( $colors['tertiary'] ); ?> !important;
		}

		.ebox-wrapper .ld-alert-warning {
			border-color: <?php echo esc_attr( $colors['tertiary'] ); ?> !important;
		}

		.ebox-wrapper .ld-tertiary-background,
		.ebox-wrapper .ld-alert-warning .ld-alert-icon {
			color:white !important;
		}

		.ebox-wrapper .wpProQuiz_content .wpProQuiz_reviewQuestion li.wpProQuiz_reviewQuestionReview,
		.ebox-wrapper .wpProQuiz_content .wpProQuiz_box li.wpProQuiz_reviewQuestionReview {
			background-color: <?php echo esc_attr( $colors['tertiary'] ); ?> !important;
		}

		.ebox-wrapper .wpProQuiz_content  .wpProQuiz_reviewLegend span.wpProQuiz_reviewColor_Review {
			background-color: <?php echo esc_attr( $colors['tertiary'] ); ?> !important;
		}

		<?php
	}

	if ( isset( $focus_width ) && ! empty( $focus_width ) && 'default' !== $focus_width ) {
		?>
		.ebox-wrapper .ld-focus .ld-focus-main .ld-focus-content {
			max-width: <?php echo esc_attr( $focus_width ); ?>;
		}
		<?php
	}

	$custom_css = ob_get_clean();

	if ( ! empty( $custom_css ) ) {
		wp_add_inline_style( 'ebox-front', $custom_css );
	}

}

add_action( 'wp_ajax_ld30_ajax_profile_search', 'ebox_30_ajax_profile_search' );

/**
 * Gets the ajax profile search data.
 *
 * Fires on `wp_ajax_ld30_ajax_profile_search` and `wp_ajax_nopriv_ld30_ajax_profile_search` ajax action.
 *
 * @since 3.0.0
 */
function ebox_30_ajax_profile_search() {
	if ( ( ! isset( $_GET['ld-profile-search-nonce'] ) ) || ( empty( $_GET['ld-profile-search-nonce'] ) ) || ( ! wp_verify_nonce( $_GET['ld-profile-search-nonce'], 'ebox_profile_course_search_nonce' ) ) ) {
		wp_send_json_error(
			array(
				'success' => false,
				'message' => __(
					'verify failed',
					'ebox'
				),
			)
		);
	}

	ob_start();

	if ( ! isset( $_GET['shortcode_instance'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Used for conditional, no data processed
		wp_send_json_error(
			array(
				'success' => false,
				'message' => __(
					'No attributes passed in',
					'ebox'
				),
			)
		);
	}

	if ( isset( $_GET['profile_search'] ) ) {
		$atts['search']            = sanitize_text_field( $_GET['profile_search'] );
		$_GET['ld-profile-search'] = sanitize_text_field( $_GET['profile_search'] );
	}

	/**
	 * Filters ajax profile search attributes.
	 *
	 * @since 3.0.0
	 *
	 * @param array $shortcode_instance Shortcode instance.
	 */
	$atts = apply_filters( 'ebox_profile_ajax_search_atts', $_GET['shortcode_instance'] );

	echo ebox_profile( $atts ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Outputs the ebox Profile template

	wp_send_json_success(
		array(
			'success' => true,
			'markup'  => ob_get_clean(),
		)
	);

}

add_action( 'wp_ajax_ld30_ajax_pager', 'ebox_30_ajax_pager' );
add_action( 'wp_ajax_nopriv_ld30_ajax_pager', 'ebox_30_ajax_pager' );

/**
 * Gets the ld30 theme ajax pagination.
 *
 * Fires on `wp_ajax_ld30_ajax_pager` and `wp_ajax_nopriv_ld30_ajax_pager` ajax action.
 *
 * @since 3.0.0
 */
function ebox_30_ajax_pager() {
	if ( ( ! isset( $_GET['pager_nonce'] ) ) || ( empty( $_GET['pager_nonce'] ) ) || ( ! wp_verify_nonce( $_GET['pager_nonce'], 'ld30_ajax_pager' ) ) ) {
		wp_send_json_error(
			array(
				'success' => false,
				'message' => __(
					'No Pagination Match',
					'ebox'
				),
			)
		);
	}

	$course_id = ( isset( $_GET['course_id'] ) ? absint( $_GET['course_id'] ) : false );
	$lesson_id = ( isset( $_GET['lesson_id'] ) ? absint( $_GET['lesson_id'] ) : false );
	$team_id  = ( isset( $_GET['team_id'] ) ? absint( $_GET['team_id'] ) : false );

	$context = ( isset( $_GET['context'] ) ? esc_attr( $_GET['context'] ) : false );

	$widget_instance = ( isset( $_GET['widget_instance'] ) ? $_GET['widget_instance'] : array() );

	// Assumed Course Navigation Widget but always check.
	if ( isset( $widget_instance['widget_instance']['show_lesson_quizzes'] ) ) {
		$widget_instance['widget_instance']['show_lesson_quizzes'] = (bool) $widget_instance['widget_instance']['show_lesson_quizzes'];
	} else {
		$widget_instance['widget_instance']['show_lesson_quizzes'] = true;
	}

	if ( isset( $widget_instance['widget_instance']['show_topic_quizzes'] ) ) {
		$widget_instance['widget_instance']['show_topic_quizzes'] = (bool) $widget_instance['widget_instance']['show_topic_quizzes'];
	} else {
		$widget_instance['widget_instance']['show_topic_quizzes'] = true;
	}

	if ( isset( $widget_instance['widget_instance']['show_course_quizzes'] ) ) {
		$widget_instance['widget_instance']['show_course_quizzes'] = (bool) $widget_instance['widget_instance']['show_course_quizzes'];
	} else {
		$widget_instance['widget_instance']['show_course_quizzes'] = true;
	}

	$user    = wp_get_current_user();
	$user_id = ( is_user_logged_in() ? $user->ID : false );

	global $course_pager_results;

	$contexts_without_course_id = array(
		'profile',
		'profile_quizzes',
		'course_info_courses',
		'team_courses',
	);

	if ( ! in_array( $context, $contexts_without_course_id, true ) && ( ! isset( $course_id ) || empty( $course_id ) ) ) {
		wp_send_json_error(
			array(
				'success' => false,
				'message' => sprintf(
					// translators: placeholder: course.
					esc_html_x(
						'No %s ID supplied',
						'placeholder: course',
						'ebox'
					),
					ebox_get_custom_label( 'course' )
				),
			)
		);
	}

	if ( 'team_courses' === $context ) {
		if ( ! empty( $team_id ) ) {
			if ( ebox_is_user_in_team( $user_id, $team_id ) ) {
				$has_access = true;
			} else {
				$has_access = false;
			}

			$team_course_ids = ebox_get_team_courses_list( $team_id );
			ob_start();
			ebox_get_template_part(
				'team/listing.php',
				array(
					'team_id'             => $team_id,
					'user_id'              => $user_id,
					'team_courses'        => $team_course_ids,
					'has_access'           => $has_access,
					'course_pager_results' => $course_pager_results,
				),
				true
			);
			$team_courses_list = ob_get_clean();

			wp_send_json_success(
				array(
					'success' => true,
					'markup'  => $team_courses_list,
				)
			);
			die();
		}
	}

	// We're paginating topics.
	if ( isset( $lesson_id ) && ! empty( $lesson_id ) ) {

		$all_topics = ebox_topic_dots( $lesson_id, $course_id, 'array' );

		/**
		 * Filters topic ajax pagination arguments.
		 *
		 * @since 3.0.0
		 *
		 * @param array $pagination_arguments Topic pagination arguments
		 */
		$topic_pager_args = apply_filters(
			'ld30_ajax_topic_pager_args', // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Legacy prefix.
			array(
				'course_id' => $course_id,
				'lesson_id' => $lesson_id,
			)
		);

		$topics = ebox_process_lesson_topics_pager( $all_topics, $topic_pager_args );

		if ( empty( $topics ) || ! $topics ) {
			wp_send_json_error(
				array(
					'success' => false,
					'message' => sprintf(
						// translators: No topics for this lesson.
						esc_html_x( 'No %1$s for this $2$s', 'placeholder: topics, lesson', 'ebox' ),
						ebox_get_custom_label_lower( 'topics' ),
						ebox_get_custom_label_lower( 'lesson' )
					),
				)
			);
		}

		ob_start();

		foreach ( $topics as $key => $topic ) {
			ebox_get_template_part(
				'topic/partials/row.php',
				array(
					'topic'     => $topic,
					'user_id'   => $user_id,
					'course_id' => $course_id,
				),
				true
			);
		}

		$topic_list = ob_get_clean();

		$nav_topics = '';

		if ( isset( $_GET['widget_instance'] ) ) {

			ob_start();

			foreach ( $topics as $key => $topic ) {
				ebox_get_template_part(
					'widgets/navigation/topic-row.php',
					array(
						'topic'           => $topic,
						'course_id'       => $course_id,
						'user_id'         => $user_id,
						'widget_instance' => $widget_instance['widget_instance'],
					),
					true
				);
			}

			$nav_topics = ob_get_clean();

		}

		/**
		 * Add in quizzes if needed
		 *
		 * @var [type]
		 */

		$show_lesson_quizzes = true;

		if ( isset( $course_pager_results[ $lesson_id ]['pager'] ) && ! empty( $course_pager_results[ $lesson_id ]['pager'] ) ) :
			$show_lesson_quizzes = ( $course_pager_results[ $lesson_id ]['pager']['paged'] == $course_pager_results[ $lesson_id ]['pager']['total_pages'] ? true : false );
		endif;

		/**
		 * Filters whether to show quiz for a particular lesson or not.
		 *
		 * @since 3.0.0
		 *
		 * @param boolean $show_lesson_quizzes Boolean value determines whether to show a quiz or not.
		 * @param int     $lesson_id           Lesson ID.
		 * @param int     $course_id           Course ID.
		 * @param int     $user_id             User ID.
		 */
		$show_lesson_quizzes = apply_filters( 'ebox-show-lesson-quizzes', $show_lesson_quizzes, $lesson_id, $course_id, $user_id ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores -- Used in multiple places, better to keep it for now.

		if ( $show_lesson_quizzes ) {

			$quizzes = ebox_get_lesson_quiz_list( $lesson_id, $user_id, $course_id );

			if ( $quizzes && ! empty( $quizzes ) ) {

				/**
				 * First add them to the lesson listing
				 *
				 * @var [type]
				 */

				ob_start();

				foreach ( $quizzes as $quiz ) {

					ebox_get_template_part(
						'quiz/partials/row.php',
						array(
							'quiz'      => $quiz,
							'user_id'   => $user_id,
							'course_id' => $course_id,
							'context'   => 'lesson',
						),
						true
					);
				}

				$topic_list .= ob_get_clean();

				/**
				 * See if we should add them to the widget nav
				 *
				 * @var [type]
				 */

				if ( isset( $widget_instance['show_lesson_quizzes'] ) && true === (bool) $widget_instance['show_lesson_quizzes'] ) {

					ob_start();

					foreach ( $quizzes as $quiz ) {
						ebox_get_template_part(
							'widgets/navigation/quiz-row.php',
							array(
								'course_id' => $course_id,
								'user_id'   => $user_id,
								'context'   => 'lesson',
								'quiz'      => $quiz,
							),
							true
						);
					}

					$nav_topics .= ob_get_clean();

				}
			}
		}

		ob_start();

		ebox_get_template_part(
			'modules/pagination.php',
			array(
				'pager_results'   => $course_pager_results[ $lesson_id ]['pager'],
				'pager_context'   => 'course_topics',
				'href_query_arg'  => 'ld-topic-page',
				'lesson_id'       => $lesson_id,
				'course_id'       => $course_id,
				'href_val_prefix' => $lesson_id . '-',
			),
			true
		);

		$pager = ob_get_clean();

		wp_send_json_success(
			array(
				'success'    => true,
				'context'    => $context,
				'topics'     => $topic_list,
				'nav_topics' => $nav_topics,
				'pager'      => $pager,
				'lesson_id'  => $lesson_id,
			)
		);

	} elseif ( 'course_modules' === $context ) {

		$lesson_query_args          = ebox_focus_mode_lesson_query_args( $course_id );
		$modules                    = ebox_30_get_course_navigation( $course_id, array(), $lesson_query_args );
		$has_access                 = ebox_lms_has_access( $course_id );
		$lesson_progression_enabled = ebox_lesson_progression_enabled( $course_id );
		$lesson_topics              = array();

		if ( ! empty( $modules ) ) {
			foreach ( $modules as $lesson ) {

				$all_topics = ebox_topic_dots( $lesson['post']->ID, false, 'array', null, $course_id );

				/** This filter is documented in themes/ld30/includes/helpers.php */
				$topic_pager_args = apply_filters(
					'ld30_ajax_topic_pager_args', // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Legacy prefix.
					array(
						'course_id' => $course_id,
						'lesson_id' => $lesson['post']->ID,
					)
				);

				$lesson_topics[ $lesson['post']->ID ] = ebox_process_lesson_topics_pager( $all_topics, $topic_pager_args );

				if ( ! empty( $lesson_topics[ $lesson['post']->ID ] ) ) {
					$has_topics = true;
				}
			}
		}

		$quizzes = ebox_get_course_quiz_list( $course_id );

		ob_start();

		ebox_get_template_part(
			'course/listing.php',
			array(
				'course_id'                  => $course_id,
				'user_id'                    => $user_id,
				'modules'                    => $modules,
				'lesson_topics'              => $lesson_topics,
				'quizzes'                    => $quizzes,
				'has_access'                 => $has_access,
				'course_pager_results'       => $course_pager_results,
				'lesson_progression_enabled' => $lesson_progression_enabled,
			),
			true
		);

		$lesson_list = ob_get_clean();

		// Need to adjust based on widget settings.
		$modules = ebox_get_course_modules_list( $course_id, $user_id, $lesson_query_args );

		ob_start();

		ebox_get_template_part(
			'widgets/navigation/rows.php',
			array(
				'course_id'            => $course_id,
				'widget_instance'      => ( isset( $widget_instance['widget_instance'] ) ? $widget_instance['widget_instance'] : false ),
				'modules'              => $modules,
				'course_pager_results' => $course_pager_results,
				'has_access'           => $has_access,
				'user_id'              => $user_id,
			),
			true
		);

		$nav_modules = ob_get_clean();

		wp_send_json_success(
			array(
				'success'         => true,
				'context'         => $context,
				'modules'         => $lesson_list,
				'nav_modules'     => $nav_modules,
				'course_id'       => $course_id,
				'widget_instance' => $widget_instance,
			)
		);

	} elseif ( 'profile' === $context ) {

		ob_start();

		if ( ! isset( $_GET['shortcode_instance'] ) ) {
			wp_send_json_error(
				array(
					'success' => false,
					'message' => __(
						'No attributes passed in',
						'ebox'
					),
				)
			);
		}

		/**
		 * Filters ajax profile search attributes
		 *
		 * @since 3.0.0
		 *
		 * @param array $shortcode_instance Shortcode instance
		 */
		$atts = apply_filters( 'ebox_profile_ajax_pagination_atts', $_GET['shortcode_instance'] );

		echo ebox_profile( $atts ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Outputs the ebox Profile shortcode

		wp_send_json_success(
			array(
				'success' => true,
				'markup'  => ob_get_clean(),
			)
		);

	} elseif ( 'profile_quizzes' === $context ) {

		$quiz_attempts = ebox_get_user_profile_quiz_attempts( $user_id );

		$paging_results = $_GET['pager_results'];
		$per_page       = intval( $paging_results['quiz_num'] );
		$course_id      = intval( $paging_results['quiz_course_id'] );

		$posts_per_page   = ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_General_Per_Page', 'per_page' );
		$quizzes_per_page = ( 0 !== $per_page ? $per_page : $posts_per_page );
		if ( isset( $quiz_attempts[ $course_id ] ) ) {
			$quiz_attempts['total_quiz_items'] = count( $quiz_attempts[ $course_id ] );
			$quiz_attempts['total_quiz_pages'] = ceil( count( $quiz_attempts[ $course_id ] ) / $quizzes_per_page );
			$quiz_attempts['quizzes-paged']    = ( isset( $_GET['profile-quizzes'] ) ? intval( $_GET['profile-quizzes'] ) : 1 );
			if ( $quiz_attempts['total_quiz_items'] >= $quiz_attempts['total_quiz_pages'] ) {
				$quiz_attempts[ $course_id ] = array_slice( $quiz_attempts[ $course_id ], ( $quiz_attempts['quizzes-paged'] * $quizzes_per_page ) - $quizzes_per_page, $quizzes_per_page, false );
			}
		}

		ob_start();
		echo '<div class="ld-item-contents">';
		// Need to output the quiz attempts template here for paginating across the ld_profile quiz attempts for each quiz.
		ebox_get_template_part(
			'shortcodes/profile/quizzes.php',
			array(
				'user_id'       => $user_id,
				'course_id'     => $course_id,
				'quiz_attempts' => $quiz_attempts,
			),
			true
		);
		$ebox_profile_quiz_pager = array(
			'paged'          => $quiz_attempts['quizzes-paged'],
			'total_items'    => $quiz_attempts['total_quiz_items'],
			'total_pages'    => $quiz_attempts['total_quiz_pages'],
			'quiz_num'       => $per_page,
			'quiz_course_id' => $course_id,
		);

		if ( isset( $_GET['profile-quizzes'] ) ) {
			ebox_get_template_part(
				'modules/pagination',
				array(
					'pager_results' => $ebox_profile_quiz_pager,
					'pager_context' => 'profile_quizzes',
				),
				true
			);
		}
		echo '</div>';
		wp_send_json_success(
			array(
				'success' => true,
				'markup'  => ob_get_clean(),
			)
		);

	} elseif ( 'course_content_shortcode' === $context ) {

		ob_start();

		/**
		 * Filters course content shortcode ajax pagination arguments.
		 *
		 * @since 3.0.0
		 *
		 * @param array $shortcode_instance Shortcode instance
		 */
		$atts = apply_filters( 'ebox_course_content_shortcode_ajax_pagination_atts', $_GET['shortcode_instance'] );

		if ( isset( $_GET['ld-courseinfo-lesson-page'] ) ) {
			$atts['paged'] = intval( $_GET['ld-courseinfo-lesson-page'] );
		}

		// On the AJAX Pager logic we don't want to include the outer wrappers.
		$atts['wrapper'] = 0;

		echo ebox_course_content_shortcode( $atts ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Outputs the ebox Course Content shortcode

		wp_send_json_success(
			array(
				'success' => true,
				'markup'  => ob_get_clean(),
			)
		);

	} elseif ( 'course_info_courses' === $context ) {

		$args = array(
			'return' => true,
			'paged'  => ( isset( $_GET['ld-user-status'] ) ? intval( $_GET['ld-user-status'] ) : 1 ),
		);

		add_filter(
			'ebox_course_info_paged',
			function( $paged = 1, $context = '' ) {
				if ( ( 'registered' === $context ) && ( isset( $_GET['ld-user-status'] ) ) && ( ! empty( $_GET['ld-user-status'] ) ) ) {
					$paged = intval( $_GET['ld-user-status'] );
				}

				return $paged;
			},
			10,
			2
		);

		/**
		 * Filters user stats widget ajax pagination arguments.
		 *
		 * @since 3.0.0
		 *
		 * @param array $shortcode_instance Shortcode instance
		 */
		$instance = apply_filters( 'ebox_user_status_widget_ajax_pagination_atts', $_GET['shortcode_instance'] );

		if ( isset( $instance['registered_num'] ) ) {
			$args['registered_num'] = intval( $instance['registered_num'] );
		}

		if ( isset( $instance['registered_orderby'] ) ) {
			$args['registered_orderby'] = sanitize_text_field( $instance['registered_orderby'] );
		}

		if ( isset( $instance['registered_order'] ) ) {
			$args['registered_order'] = sanitize_text_field( $instance['registered_order'] );
		}

		if ( isset( $instance['isblock'] ) && ! empty( $instance['isblock'] ) ) {
			$instance['isblock'] = '1';
		}

		/*
		If we are using the new ebox User Status block, set the right context, else the old User Status widget is being used
		*/
		if ( '1' === $instance['isblock'] ) {
			$context         = 'block';
			$args['isblock'] = 'block';
		} else {
			$context         = 'widget';
			$args['isblock'] = '';
		}

		$course_info = ebox_LMS::get_course_info( $user_id, $args );

		ob_start();

		ebox_get_template_part(
			'shortcodes/user-status.php',
			array(
				'course_info'    => $course_info,
				'shortcode_atts' => $args,
				'context'        => $context,
			),
			true
		);

		wp_send_json_success(
			array(
				'success' => true,
				'markup'  => ob_get_clean(),
			)
		);
	}

	wp_send_json_error(
		array(
			'success' => false,
			'message' => __(
				'No Pagination Match',
				'ebox'
			),
		)
	);

}

/**
 * Gets the focus mode lesson query arguments.
 *
 * @global WP_Post $post Global post object.
 *
 * @since 3.0.0
 *
 * @param int      $course_id               Course ID.
 * @param int|null $course_modules_per_page Number of course modules per page.
 *
 * @return array An array of query arguments to get lesson.
 */
function ebox_focus_mode_lesson_query_args( $course_id, $course_modules_per_page = null ) {

	global $post;

	$lesson_query_args = array();
	$instance          = array();

	if ( null === $course_modules_per_page ) {
		$course_modules_per_page = ebox_get_course_modules_per_page( $course_id );
	}

	if ( $course_modules_per_page > 0 && ( $post instanceof WP_Post ) ) {

		if ( in_array( $post->post_type, array( 'ebox-modules', 'ebox-topic', 'ebox-quiz' ), true ) ) {

			$instance['current_step_id'] = $post->ID;
			if ( 'ebox-modules' === $post->post_type ) {
				$instance['current_lesson_id'] = $post->ID;
			} elseif ( in_array( $post->post_type, array( 'ebox-topic', 'ebox-quiz' ), true ) ) {
				$instance['current_lesson_id'] = ebox_course_get_single_parent_step( $course_id, $post->ID, 'ebox-modules' );
				$instance['current_lesson_id'] = absint( $instance['current_lesson_id'] );
			}

			if ( ! empty( $instance['current_lesson_id'] ) ) {
				$course_lesson_ids = ebox_course_get_steps_by_type( $course_id, 'ebox-modules' );
				if ( ! empty( $course_lesson_ids ) ) {
					$course_modules_paged = array_chunk( $course_lesson_ids, $course_modules_per_page, true );
					$modules_paged        = 0;
					foreach ( $course_modules_paged as $paged => $paged_set ) {
						if ( in_array( $instance['current_lesson_id'], $paged_set, true ) ) {
							$modules_paged = $paged + 1;
							break;
						}
					}

					if ( ! empty( $modules_paged ) ) {
						$lesson_query_args['pagination'] = 'true';
						$lesson_query_args['paged']      = $modules_paged;
					}
				}
			} elseif ( in_array( $post->post_type, array( 'ebox-quiz' ), true ) ) {
				// If here we have a global Quiz. So we set the pager to the max number.
				$course_lesson_ids = ebox_course_get_steps_by_type( $course_id, 'ebox-modules' );
				if ( ! empty( $course_lesson_ids ) ) {
					$course_modules_paged       = array_chunk( $course_lesson_ids, $course_modules_per_page, true );
					$lesson_query_args['paged'] = count( $course_modules_paged );
				}
			}
		}
	} else {
		if ( ( $post ) && ( is_a( $post, 'WP_Post' ) ) && ( in_array( $post->post_type, array( 'ebox-modules', 'ebox-topic', 'ebox-quiz' ), true ) ) ) {

			$instance['current_step_id'] = $post->ID;
			if ( 'ebox-modules' === $post->post_type ) {
				$instance['current_lesson_id'] = $post->ID;
			} elseif ( in_array( $post->post_type, array( 'ebox-topic', 'ebox-quiz' ), true ) ) {
				$instance['current_lesson_id'] = ebox_course_get_single_parent_step( $course_id, $post->ID, 'ebox-modules' );
			}
		}
	}

	return $lesson_query_args;

}

/**
 * Converts the hex color values to rgb.
 *
 * @since 3.0.0
 *
 * @param string            $color  Color value in hex format.
 * @param float|int|boolean $opacity The opacity of color.
 *
 * @return string Color value in rgb format.
 */
function ebox_hex2rgb( $color, $opacity = false ) {

	$default = 'rgb(0,0,0)';

	// Return default if no color provided.
	if ( empty( $color ) ) {
		return $default;
	}

	// Sanitize $color if "#" is provided.
	if ( '#' === $color[0] ) {
		$color = substr( $color, 1 );
	}

		// Check if color has 6 or 3 characters and get values.
	if ( strlen( $color ) == 6 ) {
			$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
	} elseif ( strlen( $color ) == 3 ) {
			$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
	} else {
			return $default;
	}

		// Convert hexadecimal to rgb.
		$rgb = array_map( 'hexdec', $hex );

		// Check if opacity is set (rgba or rgb).
	if ( $opacity ) {
		if ( abs( $opacity ) > 1 ) {
			$opacity = 1.0;
		}
		$output = 'rgba(' . implode( ',', $rgb ) . ',' . $opacity . ')';
	} else {
		$output = 'rgb(' . implode( ',', $rgb ) . ')';
	}

	// Return rgb(a) color string.
	return $output;
}

/**
 * Gets the ld30 theme course navigation.
 *
 * @since 3.0.0
 *
 * @global array $course_navigation_widget_pager Global course navigation widget pager.
 *
 * @param int   $course_id         Course ID.
 * @param array $widget_instance   An array of widget instance data.
 * @param array $lesson_query_args An array of query arguments to get lesson.
 *
 * @return string|void Course navigation HTML output.
 */
function ebox_30_get_course_navigation( $course_id, $widget_instance = array(), $lesson_query_args = array() ) {

	$course = get_post( $course_id );

	if ( empty( $course->ID ) || $course_id != $course->ID ) {
		return;
	}

	if ( empty( $course->ID ) || 'ebox-courses' !== $course->post_type ) {
		return;
	}

	if ( is_user_logged_in() ) {
		$user_id = get_current_user_id();
	} else {
		$user_id = 0;
	}

	$course_navigation_widget_pager = array();

	global $course_navigation_widget_pager;

	add_action(
		'ebox_course_modules_list_pager',
		function( $query_result = null ) {

			global $course_navigation_widget_pager;

			$course_navigation_widget_pager['paged'] = 1;

			if ( ( isset( $query_result->query_vars['paged'] ) ) && ( $query_result->query_vars['paged'] > 1 ) ) {
				$course_navigation_widget_pager['paged'] = $query_result->query_vars['paged'];
			}

			$course_navigation_widget_pager['total_items'] = $query_result->found_posts;
			$course_navigation_widget_pager['total_pages'] = $query_result->max_num_pages;

		}
	);

	$modules = ebox_get_course_modules_list( $course, $user_id, $lesson_query_args );

	return $modules;

}

/**
 * Gets the ld30 theme course sections.
 *
 * @since 3.0.0
 *
 * @param int|null $course_id Course ID.
 *
 * @return array|false An array of sections or false.
 */
function ebox_30_get_course_sections( $course_id = null ) {

	if ( empty( $course_id ) ) {
		$course_id = get_the_ID();
	}

	if ( ebox_get_post_type_slug( 'course' ) !== get_post_type( $course_id ) ) {
		$course_id = ebox_get_course_id( $course_id );
	}

	$course_sections = ebox_course_get_sections( $course_id );
	$sections        = array();

	if ( ! empty( $course_sections ) ) {
		foreach ( $course_sections as $section ) {
			if ( ( property_exists( $section, 'steps' ) ) && ( ! empty( $section->steps ) ) ) {
				$sections[ $section->steps[0] ] = $section;
			}
		}
	}

	return $sections;
}

add_filter( 'body_class', 'ebox_30_custom_body_classes' );

/**
 * Gets the ld30 theme custom body classes.
 *
 * Fires on `body_class` hook.
 *
 * @since 3.0.0
 *
 * @param array $classes An array of body class names.
 *
 * @return array An array of body class names.
 */
function ebox_30_custom_body_classes( $classes ) {

	$focus_mode = ebox_Settings_Section::get_section_setting( 'ebox_Settings_Theme_LD30', 'focus_mode_enabled' );

	$post_types = array(
		'ebox-modules',
		'ebox-topic',
		'ebox-quiz',
		'ebox-assignment',
	);

	if ( 'yes' === $focus_mode && in_array( get_post_type(), $post_types, true ) ) {
		$classes[] = 'ld-in-focus-mode';
	}

	return $classes;

}

/**
 * Checks whether a post can be marked as complete or not in focus mode.
 *
 * @since 3.0.0
 * @deprecated 4.0.3 Use `ebox_can_complete_step()` instead.
 *
 * @param int|WP_Post|null $post      `WP_Post` object or post ID. Default to global $post.
 * @param int|null         $course_id Course ID.
 *
 * @return boolean Whether a post can be marked as complete.
 */
function ebox_30_focus_mode_can_complete( $post = null, $course_id = null ) {

	if ( null === $post ) {
		global $post;
	}

	if ( is_int( $post ) ) {
		$post = get_post( $post ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- I suppose it's what they wanted.
	}

	if ( ! $course_id ) {
		$course_id = ebox_get_course_id( $course_id );
	}

	// Shouldn't appear regardless if this is a quiz.
	if ( get_post_type( $post ) == 'ebox-quiz' ) {
		return false;
	}

	$complete_button = ebox_mark_complete( $post );

	// If the complete button returns empty, also just return false.
	if ( empty( $complete_button ) ) {
		return false;
	}

	// Check if has any outstanding quizzes.
	$quizzes = ebox_get_lesson_quiz_list( $post->ID, get_current_user_id(), $course_id );

	// If there is a quiz then the quiz is the mark complete.
	if ( $quizzes ) {
		return false;
	}

	return true;
}

/**
 * Deprecated
 *
 * @deprecated
 *
 * @param string $html    Html.
 * @param string $url     Url.
 * @param string $attr    Attr.
 * @param int    $post_id Post ID.
 *
 * @return false|mixed|string
 */
function ebox_30_responsive_videos( $html, $url, $attr, $post_id ) {
	/** This filter is documented in themes/ld30/includes/helpers.php */
	$responsive_video = apply_filters( 'ebox_30_responsive_video', ebox_Settings_Section::get_section_setting( 'ebox_Settings_Theme_LD30', 'responsive_video_enabled' ) );

	if ( ! isset( $responsive_video ) || 'yes' !== $responsive_video ) {
		return false;
	}

	/**
	 * Filters Responsive video supported post types.
	 *
	 * @param array $post_types Array of supported post type.
	 */
	$post_types = apply_filters(
		'ebox_responsive_video_post_types',
		array(
			'ebox-courses',
			'ebox-modules',
			'ebox-topic',
			'ebox-quiz',
			'ebox-assignments',
		)
	);

	if ( ! in_array( get_post_type( $post_id ), $post_types, true ) ) {
		return $html;
	}

	/**
	 * Filters responsive video domains. Used to modify the supported domains for the responsive video.
	 *
	 * @since 3.0.0
	 *
	 * @param array $video_domains Array of video domains to support responsive video.
	 */
	$matches = apply_filters(
		'ebox_responsive_video_domains',
		array(
			'youtube.com',
			'vimeo.com',
		)
	);

	foreach ( $matches as $match ) {
		if ( strpos( $url, $match ) !== false ) {
			return '<div class="ld-resp-video">' . $html . '</div>';
		}
	}

	return $html;
}

/**
 * Gets the certificate count for a user.
 *
 * @since 3.0.0
 *
 * @param WP_User|int|null $user `WP_User` object or user ID. Defaults to current logged in user.
 *
 * @return int|false Returns users certificate count.
 */
function ebox_get_certificate_count( $user = null ) {

	if ( null === $user ) {
		$user = wp_get_current_user();
	}

	if ( is_int( $user ) ) {
		$user = get_user_by( 'id', $user );
	}

	if ( ! $user ) {
		return false;
	}

	$certificates = 0;

	$course_ids = ebox_user_get_enrolled_courses( $user->ID, array(), true );
	$quizzes    = get_user_meta( $user->ID, '_ebox-quizzes', true );

	if ( $course_ids && ! empty( $course_ids ) ) {
		foreach ( $course_ids as $course_id ) {

			$link = ebox_get_course_certificate_link( $course_id, $user->ID );

			if ( ! empty( $link ) ) {
				$certificates++;
			}
		}
	}

	if ( $quizzes && ! empty( $quizzes ) ) {
		foreach ( $quizzes as $quiz_attempt ) {
			if ( isset( $quiz_attempt['certificate']['certificateLink'] ) ) {
				$certificates++;
			}
		}
	}

	return $certificates;

}

/**
 * Gets whether the lesson has quiz or not.
 *
 * @since 3.0.0
 *
 * @param int|null $course_id Course ID. Defaults to current post ID in WordPress loop.
 * @param int|null $modules   An array of lesson `WP_Post` object.
 *
 * @return boolean Returns whether a lesson has quiz or not.
 */
function ebox_30_has_lesson_quizzes( $course_id = null, $modules = null ) {

	if ( null === $course_id && get_post_type() == 'ebox-courses' ) {
		$course_id = get_the_ID();
	} elseif ( null === $course_id ) {
		$course_id = ebox_get_course_id( get_the_ID() );
	}

	if ( null === $modules ) {
		$modules = ebox_get_course_modules_list( $course_id );
	}

	foreach ( $modules as $lesson ) {

		$quizzes = ebox_get_lesson_quiz_list( $lesson['post']->ID, null, $course_id );

		if ( ! empty( $quizzes ) ) {
			return true;
		}
	}

	return false;

}

/**
 * Gets an array of points awarded for an assignment.
 *
 * @since 3.0.0
 *
 * @param int $assignment_id Assignment ID.
 *
 * @return false|array An array of points awarded for an assignment or false if the points are disabled.
 */
function ebox_get_points_awarded_array( $assignment_id ) {
	$points_enabled = ebox_assignment_is_points_enabled( $assignment_id );

	if ( ! $points_enabled ) {
		return false;
	}

	$current = get_post_meta( $assignment_id, 'points', true );

	if ( is_numeric( $current ) ) {
		$assignment_settings_id = intval( get_post_meta( $assignment_id, 'lesson_id', true ) );
		$max_points             = ebox_get_setting( $assignment_settings_id, 'lesson_assignment_points_amount' );
		$max_points             = intval( $max_points );
		if ( ! empty( $max_points ) ) {
			$percentage = ( intval( $current ) / intval( $max_points ) ) * 100;
			$percentage = round( $percentage, 2 );
		} else {
			$percentage = 0.00;
		}

		/**
		 * Filters Points awarded data. Used to modify points given for any particular assignment.
		 *
		 * @since 3.0.0
		 *
		 * @param array $points_awarded Array for points awarded details.
		 * @param int   $assignment_id  Assignment ID.
		 */
		return apply_filters(
			'ebox_get_points_awarded_array',
			array(
				'current'    => $current,
				'max'        => $max_points,
				'percentage' => $percentage,
			),
			$assignment_id
		);
	}

	return false;
}

/**
 * Gets whether a lesson has topics or not.
 *
 * @since 3.0.0
 *
 * @param int|null   $course_id Course ID.
 * @param array|null $modules   An array of lesson objects.
 *
 * @return bool True if the lesson has topics otherwise false.
 */
function ebox_30_has_topics( $course_id = null, $modules = null ) {
	$course_id = ( null === $course_id ? ebox_get_course_id() : $course_id );
	$user_id   = get_current_user_id();

	if ( ! empty( $modules ) ) {
		foreach ( $modules as $lesson ) {
			$lesson_topics[ $lesson['post']->ID ] = ebox_topic_dots( $lesson['post']->ID, false, 'array', $user_id, $course_id );
			if ( ! empty( $lesson_topics[ $lesson['post']->ID ] ) ) {
				return true;
			}
		}
	}

	return false;
}

/**
 * Genesis doesn't use the normal wp_enqueue_scripts or wp_head so we need to call the enqueue function specifically for Genesis
 */
add_action( 'ebox-focus-head', 'ebox_studiopress_compatibility' ); // cspell:disable-line.

/**
 * Enqueues the genesis main stylesheet.
 * Fires on `ebox-focus-head` hook.
 *
 * @since 3.0.1
 */
function ebox_studiopress_compatibility() {

	if ( function_exists( 'genesis_enqueue_main_stylesheet' ) ) {
		genesis_enqueue_main_stylesheet();
	}

}

add_filter(
	'ebox_lms_has_access',
	function( $access, $post_id, $user_id ) {
		if ( ( is_single() ) && ( ! is_admin() ) ) {
			$lesson_id = ebox_get_lesson_id( $post_id );
			if ( ( true === (bool) $access ) && ( ! empty( $lesson_id ) ) && ( ebox_is_sample( $lesson_id ) ) ) {

				/**
				 * Filters whether to allow access to the sample lesson or not.
				 *
				 * By default a sample lesson is available even to anonymous users. This
				 * filter will override that access. The filer 'ebox_can_access_sample'
				 * is also used themes/ld30/templates/lesson/partials/row.php to control
				 * visibility of the lesson and sub-steps.
				 *
				 * @since 3.2.0
				 *
				 * @param bool $access    Access status true if the user can access $post_id.
				 * @param int  $lesson_id Lesson ID.
				 * @param int  $post_id   Course step the user is trying to access.
				 * @param int  $user_id   User ID.
				 */
				$access = apply_filters(
					'ebox_lesson_sample_access',
					$access,
					(int) $lesson_id,
					ebox_get_course_id(),
					$user_id
				);
			}
		}

		return $access;
	},
	30,
	3
);
