<?php
/**
 * ebox Custom Label class.
 *
 * @package ebox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class ebox Custom Label
 */
class ebox_Custom_Label {
	/**
	 * Button take this course.
	 *
	 * @since 4.5.0
	 *
	 * @var string
	 */
	public static $button_take_course = 'button_take_this_course';

	/**
	 * Button take this team.
	 *
	 * @since 4.5.0
	 *
	 * @var string
	 */
	public static $button_take_team = 'button_take_this_team';

	/**
	 * Get label based on key name.
	 *
	 * @param string $key Key name of setting field.
	 *
	 * @return string Label entered on settings page.
	 */
	public static function get_label( string $key ): string {
		$key = strtolower( $key );

		$labels = get_option( 'ebox_settings_custom_labels', array() );
		if ( ! is_array( $labels ) ) {
			$labels = array();
		}

		if ( ! empty( $labels[ $key ] ) ) {
			$label = $labels[ $key ];
		} else {
			switch ( $key ) {
				case 'course':
					$label = esc_html__( 'Course', 'ebox' );
					break;

				case 'courses':
					$label = esc_html__( 'Courses', 'ebox' );
					break;

				case 'lesson':
					$label = esc_html__( 'Lesson', 'ebox' );
					break;

				case 'modules':
					$label = esc_html__( 'Modules', 'ebox' );
					break;

				case 'topic':
					$label = esc_html__( 'Topic', 'ebox' );
					break;

				case 'topics':
					$label = esc_html__( 'Topics', 'ebox' );
					break;

				case 'exam':
					$label = esc_html__( 'Examination', 'ebox' );
					break;

				case 'exams':
					$label = esc_html__( 'Examinations', 'ebox' );
					break;

				case 'coupon':
					$label = esc_html__( 'Coupon', 'ebox' );
					break;

				case 'coupons':
					$label = esc_html__( 'Coupons', 'ebox' );
					break;

				case 'quiz':
					$label = esc_html__( 'Benchmark Test', 'ebox' );
					break;

				case 'quizzes':
					$label = esc_html__( 'Benchmark Tests', 'ebox' );
					break;

				case 'question':
					$label = esc_html__( 'Question', 'ebox' );
					break;

				case 'questions':
					$label = esc_html__( 'Questions', 'ebox' );
					break;

				case 'transaction':
					$label = esc_html__( 'Transaction', 'ebox' );
					break;

				case 'transactions':
					$label = esc_html__( 'Transactions', 'ebox' );
					break;

				case 'team':
					$label = esc_html__( 'Team', 'ebox' );
					break;

				case 'teams':
					$label = esc_html__( 'Teams', 'ebox' );
					break;

				case 'team_leader':
					$label = esc_html__( 'Team Leader', 'ebox' );
					break;

				case 'assignment':
					$label = esc_html__( 'Assignment', 'ebox' );
					break;

				case 'assignments':
					$label = esc_html__( 'Assignments', 'ebox' );
					break;

				case 'essay':
					$label = esc_html__( 'Essay', 'ebox' );
					break;

				case 'essays':
					$label = esc_html__( 'Essays', 'ebox' );
					break;

				case 'certificate':
					$label = esc_html__( 'Certificate', 'ebox' );
					break;

				case 'certificates':
					$label = esc_html__( 'Certificates', 'ebox' );
					break;

				case self::$button_take_course:
					$label = esc_html__( 'Take this Course', 'ebox' );
					break;

				case self::$button_take_team:
					$label = esc_html__( 'Enroll in Team', 'ebox' );
					break;

				case 'button_mark_complete':
					$label = esc_html__( 'Mark Complete', 'ebox' );
					break;

				case 'button_click_here_to_continue':
					$label = esc_html__( 'Click Here to Continue', 'ebox' );
					break;

				default:
					$label = '';
			}
		}

		/**
		 * Filters the value of label settings entered on the settings page.
		 * Used to filter label value in get_label function.
		 *
		 * @param string $label Label entered on settings page.
		 * @param string $key   Key name of setting field.
		 */
		return apply_filters( 'ebox_get_label', $label, $key );
	}

	/**
	 * Get slug-ready string.
	 *
	 * @param string $key Key name of setting field.
	 *
	 * @return string Lowercase string.
	 */
	public static function label_to_lower( string $key ): string {
		$label = strtolower(
			self::get_label( $key )
		);

		/**
		 * Filters value of label after converting it to the lowercase. Used to filter label values in label_to_lower function.
		 *
		 * @param string $label Label entered on settings page.
		 * @param string $key   Key name of setting field.
		 */
		return apply_filters( 'ebox_label_to_lower', $label, $key );
	}

	/**
	 * Get slug-ready string.
	 *
	 * @param string $key Key name of setting field.
	 *
	 * @return string Slug-ready string.
	 */
	public static function label_to_slug( string $key ): string {
		$label = sanitize_title(
			self::get_label( $key )
		);

		/**
		 * Filters the value of the slug after the conversion from the label. Used to filter slug value in label_to_slug function.
		 *
		 * @deprecated 4.5.0 Use the {@see 'ebox_label_to_slug'} filter instead.
		 *
		 * @param string $label Label entered on settings page.
		 * @param string $key   Key name of setting field.
		 */
		$label = apply_filters_deprecated(
			'label_to_slug',
			array( $label, $key ),
			'4.5.0',
			'ebox_label_to_slug'
		);

		/**
		 * Filters the value of the slug after the conversion from the label. Used to filter slug value in label_to_slug function.
		 *
		 * @since 4.5.0
		 *
		 * @param string $label Label entered on settings page.
		 * @param string $key   Key name of setting field.
		 */
		return apply_filters( 'ebox_label_to_slug', $label, $key );
	}
}

/**
 * Utility function to get a custom field label.
 *
 * @since 2.6.0
 *
 * @param string $field Field label to retrieve.
 *
 * @return string Field label. Empty of none found.
 */
function ebox_get_custom_label( string $field ): string {
	return ebox_Custom_Label::get_label( $field );
}

/**
 * Utility function to get a custom field label lowercase.
 *
 * @since 2.6.0
 *
 * @param string $field Field label to retrieve.
 *
 * @return string Field label. Empty of none found.
 */
function ebox_get_custom_label_lower( string $field ): string {
	return ebox_Custom_Label::label_to_lower( $field );
}

/**
 * Utility function to get a custom field label slug.
 *
 * @since 2.6.0
 *
 * @param string $field Field label to retrieve.
 *
 * @return string Field label. Empty of none found.
 */
function ebox_get_custom_label_slug( string $field ): string {
	return ebox_Custom_Label::label_to_slug( $field );
}

/**
 * Get Course Step "Back to ..." label.
 *
 * @since 3.0.7
 *
 * @param string  $step_post_type The post_type slug of the post to return label for.
 * @param boolean $plural         True if the label should be the plural label. Default is false for single.
 *
 * @return string label.
 */
function ebox_get_label_course_step_back( string $step_post_type, bool $plural = false ): string {
	$post_type_object = get_post_type_object( $step_post_type );

	if ( $post_type_object && is_a( $post_type_object, 'WP_Post_Type' ) ) {
		switch ( $step_post_type ) {
			case ebox_get_post_type_slug( 'course' ):
				if ( true === $plural ) {
					$step_label = sprintf(
						// translators: placeholder: Courses.
						esc_html_x( 'Back to %s', 'placeholder: Courses', 'ebox' ),
						$post_type_object->labels->name
					);
				} else {
					$step_label = sprintf(
						// translators: placeholder: Course.
						esc_html_x( 'Back to %s', 'placeholder: Course', 'ebox' ),
						$post_type_object->labels->singular_name
					);
				}
				break;

			case ebox_get_post_type_slug( 'lesson' ):
				if ( true === $plural ) {
					$step_label = sprintf(
						// translators: placeholder: modules.
						esc_html_x( 'Back to %s', 'placeholder: modules', 'ebox' ),
						$post_type_object->labels->name
					);
				} else {
					$step_label = sprintf(
						// translators: placeholder: Lesson.
						esc_html_x( 'Back to %s', 'placeholder: Lesson', 'ebox' ),
						$post_type_object->labels->singular_name
					);
				}
				break;

			case ebox_get_post_type_slug( 'topic' ):
				if ( true === $plural ) {
					$step_label = sprintf(
						// translators: placeholder: Topics.
						esc_html_x( 'Back to %s', 'placeholder: Topics', 'ebox' ),
						$post_type_object->labels->name
					);
				} else {
					$step_label = sprintf(
						// translators: placeholder: Topic.
						esc_html_x( 'Back to %s', 'placeholder: Topic', 'ebox' ),
						$post_type_object->labels->singular_name
					);

				}
				break;

			case ebox_get_post_type_slug( 'quiz' ):
				if ( true === $plural ) {
					$step_label = sprintf(
						// translators: placeholder: Quizzes.
						esc_html_x( 'Back to %s', 'placeholder: Quizzes', 'ebox' ),
						$post_type_object->labels->name
					);
				} else {
					$step_label = sprintf(
						// translators: placeholder: Quiz.
						esc_html_x( 'Back to %s', 'placeholder: Quiz', 'ebox' ),
						$post_type_object->labels->singular_name
					);
				}
				break;

			case ebox_get_post_type_slug( 'question' ):
				if ( true === $plural ) {
					$step_label = sprintf(
						// translators: placeholder: Questions.
						esc_html_x( 'Back to %s', 'placeholder: Questions', 'ebox' ),
						$post_type_object->labels->name
					);
				} else {
					$step_label = sprintf(
						// translators: placeholder: Question.
						esc_html_x( 'Back to %s', 'placeholder: Question', 'ebox' ),
						$post_type_object->labels->singular_name
					);

				}
				break;

			case ebox_get_post_type_slug( 'transaction' ):
				if ( true === $plural ) {
					$step_label = sprintf(
						// translators: placeholder: Transactions.
						esc_html_x( 'Back to %s', 'placeholder: Transactions', 'ebox' ),
						$post_type_object->labels->name
					);
				} else {
					$step_label = sprintf(
						// translators: placeholder: Transaction.
						esc_html_x( 'Back to %s', 'placeholder: Transaction', 'ebox' ),
						$post_type_object->labels->singular_name
					);
				}
				break;

			case ebox_get_post_type_slug( 'team' ):
				if ( true === $plural ) {
					$step_label = sprintf(
						// translators: placeholder: Teams.
						esc_html_x( 'Back to %s', 'placeholder: Teams', 'ebox' ),
						$post_type_object->labels->name
					);
				} else {
					$step_label = sprintf(
						// translators: placeholder: Team.
						esc_html_x( 'Back to %s', 'placeholder: Team', 'ebox' ),
						$post_type_object->labels->singular_name
					);
				}
				break;

			case ebox_get_post_type_slug( 'assignment' ):
				if ( true === $plural ) {
					$step_label = sprintf(
						// translators: placeholder: Assignments.
						esc_html_x( 'Back to %s', 'placeholder: Assignments', 'ebox' ),
						$post_type_object->labels->name
					);
				} else {
					$step_label = sprintf(
						// translators: placeholder: Assignment.
						esc_html_x( 'Back to %s', 'placeholder: Assignment', 'ebox' ),
						$post_type_object->labels->singular_name
					);
				}
				break;

			case ebox_get_post_type_slug( 'essay' ):
				if ( true === $plural ) {
					$step_label = sprintf(
						// translators: placeholder: Essays.
						esc_html_x( 'Back to %s', 'placeholder: Essays', 'ebox' ),
						$post_type_object->labels->name
					);
				} else {
					$step_label = sprintf(
						// translators: placeholder: Essay.
						esc_html_x( 'Back to %s', 'placeholder: Essay', 'ebox' ),
						$post_type_object->labels->singular_name
					);
				}
				break;

			case ebox_get_post_type_slug( 'certificate' ):
				if ( true === $plural ) {
					$step_label = esc_html__( 'Back to Certificates', 'ebox' );
				} else {
					$step_label = esc_html__( 'Back to Certificate', 'ebox' );
				}
				break;

			default:
				if ( true === $plural ) {
					$step_label = sprintf(
						// translators: placeholder: Post Type Plural label.
						esc_html_x( 'Back to %s', 'placeholder: Post Type Plural label', 'ebox' ),
						$post_type_object->labels->name
					);
				} else {
					$step_label = sprintf(
						// translators: placeholder: Post Type Singular label.
						esc_html_x( 'Back to %s', 'placeholder: Post Type Singular label', 'ebox' ),
						$post_type_object->labels->singular_name
					);
				}
				break;
		}
	} else {
		$step_label = sprintf(
			// translators: placeholder: Post Type slug.
			esc_html_x( 'Back to %s', 'placeholder: Post Type slug', 'ebox' ),
			$step_post_type
		);
	}

	/**
	 * Filters value of course step back label. Used to update step back label in ebox_get_label_course_step_back function.
	 *
	 * @param string  $step_label     Course Step `Back to ...` label.
	 * @param string  $step_post_type The post_type slug of the post to return label for.
	 * @param boolean $plural         True if the label should be the plural label.
	 */
	return apply_filters( 'ebox_get_label_course_step_back', $step_label, $step_post_type, $plural );
}

/**
 * Get Course Step "Previous ..." label.
 *
 * @since 3.0.7
 *
 * @param string $step_post_type The post_type slug of the post to return label for.
 *
 * @return string label.
 */
function ebox_get_label_course_step_previous( string $step_post_type ): string {
	$post_type_object = get_post_type_object( $step_post_type );

	if ( $post_type_object && is_a( $post_type_object, 'WP_Post_Type' ) ) {
		switch ( $step_post_type ) {
			case ebox_get_post_type_slug( 'course' ):
				$step_label = sprintf(
					// translators: placeholder: Course.
					esc_html_x( 'Previous %s', 'placeholder: Course', 'ebox' ),
					$post_type_object->labels->singular_name
				);
				break;

			case ebox_get_post_type_slug( 'lesson' ):
				$step_label = sprintf(
					// translators: placeholder: Lesson.
					esc_html_x( 'Previous %s', 'placeholder: Lesson', 'ebox' ),
					$post_type_object->labels->singular_name
				);
				break;

			case ebox_get_post_type_slug( 'topic' ):
				$step_label = sprintf(
					// translators: placeholder: Topic.
					esc_html_x( 'Previous %s', 'placeholder: Topic', 'ebox' ),
					$post_type_object->labels->singular_name
				);
				break;

			case ebox_get_post_type_slug( 'quiz' ):
				$step_label = sprintf(
					// translators: placeholder: Quiz.
					esc_html_x( 'Previous %s', 'placeholder: Quiz', 'ebox' ),
					$post_type_object->labels->singular_name
				);
				break;

			case ebox_get_post_type_slug( 'question' ):
				$step_label = sprintf(
					// translators: placeholder: Question.
					esc_html_x( 'Previous %s', 'placeholder: Question', 'ebox' ),
					$post_type_object->labels->singular_name
				);
				break;

			case ebox_get_post_type_slug( 'transaction' ):
				$step_label = sprintf(
					// translators: placeholder: Transaction.
					esc_html_x( 'Previous %s', 'placeholder: Transaction', 'ebox' ),
					$post_type_object->labels->singular_name
				);
				break;

			case ebox_get_post_type_slug( 'team' ):
				$step_label = sprintf(
					// translators: placeholder: Team.
					esc_html_x( 'Previous %s', 'placeholder: Team', 'ebox' ),
					$post_type_object->labels->singular_name
				);
				break;

			case ebox_get_post_type_slug( 'assignment' ):
				$step_label = sprintf(
					// translators: placeholder: Assignment.
					esc_html_x( 'Previous %s', 'placeholder: Assignment', 'ebox' ),
					$post_type_object->labels->singular_name
				);
				break;

			case ebox_get_post_type_slug( 'essay' ):
				$step_label = sprintf(
					// translators: placeholder: Essay.
					esc_html_x( 'Previous %s', 'placeholder: Essay', 'ebox' ),
					$post_type_object->labels->singular_name
				);
				break;

			case ebox_get_post_type_slug( 'certificate' ):
				$step_label = esc_html__( 'Previous Certificate', 'ebox' );
				break;

			default:
				$step_label = sprintf(
					// translators: placeholder: Post Type Singular label.
					esc_html_x( 'Previous %s', 'placeholder: Post Type Singular label', 'ebox' ),
					$post_type_object->labels->singular_name
				);
				break;
		}
	} else {
		$step_label = sprintf(
			// translators: placeholder: Post Type slug.
			esc_html_x( 'Previous %s', 'placeholder: Post Type slug', 'ebox' ),
			$step_post_type
		);
	}

	/**
	 * Filters value of course step previous label. Used to update step previous label in ebox_get_label_course_step_previous function.
	 *
	 * @param string  $step_label     Course step `Previous ...` label.
	 * @param string  $step_post_type The post_type slug of the post to return label for.
	 */
	return apply_filters( 'ebox_get_label_course_step_previous', $step_label, $step_post_type );
}

/**
 * Get Course Step "Next ..." label.
 *
 * @since 3.0.7
 *
 * @param string $step_post_type The post_type slug of the post to return label for.
 *
 * @return string label.
 */
function ebox_get_label_course_step_next( string $step_post_type ): string {
	$post_type_object = get_post_type_object( $step_post_type );

	if ( $post_type_object && is_a( $post_type_object, 'WP_Post_Type' ) ) {
		switch ( $step_post_type ) {
			case ebox_get_post_type_slug( 'course' ):
				$step_label = sprintf(
					// translators: placeholder: Course.
					esc_html_x( 'Next %s', 'placeholder: Course', 'ebox' ),
					$post_type_object->labels->singular_name
				);
				break;

			case ebox_get_post_type_slug( 'lesson' ):
				$step_label = sprintf(
					// translators: placeholder: Lesson.
					esc_html_x( 'Next %s', 'placeholder: Lesson', 'ebox' ),
					$post_type_object->labels->singular_name
				);
				break;

			case ebox_get_post_type_slug( 'topic' ):
				$step_label = sprintf(
					// translators: placeholder: Topic.
					esc_html_x( 'Next %s', 'placeholder: Topic', 'ebox' ),
					$post_type_object->labels->singular_name
				);
				break;

			case ebox_get_post_type_slug( 'quiz' ):
				$step_label = sprintf(
					// translators: placeholder: Quiz.
					esc_html_x( 'Next %s', 'placeholder: Quiz', 'ebox' ),
					$post_type_object->labels->singular_name
				);
				break;

			case ebox_get_post_type_slug( 'question' ):
				$step_label = sprintf(
					// translators: placeholder: Question.
					esc_html_x( 'Next %s', 'placeholder: Question', 'ebox' ),
					$post_type_object->labels->singular_name
				);
				break;

			case ebox_get_post_type_slug( 'transaction' ):
				$step_label = sprintf(
					// translators: placeholder: Transaction.
					esc_html_x( 'Next %s', 'placeholder: Transaction', 'ebox' ),
					$post_type_object->labels->singular_name
				);
				break;

			case ebox_get_post_type_slug( 'team' ):
				$step_label = sprintf(
					// translators: placeholder: Team.
					esc_html_x( 'Next %s', 'placeholder: Team', 'ebox' ),
					$post_type_object->labels->singular_name
				);
				break;

			case ebox_get_post_type_slug( 'assignment' ):
				$step_label = sprintf(
					// translators: placeholder: Assignment.
					esc_html_x( 'Next %s', 'placeholder: Assignment', 'ebox' ),
					$post_type_object->labels->singular_name
				);
				break;

			case ebox_get_post_type_slug( 'essay' ):
				$step_label = sprintf(
					// translators: placeholder: Essay.
					esc_html_x( 'Next %s', 'placeholder: Essay', 'ebox' ),
					$post_type_object->labels->singular_name
				);
				break;

			case ebox_get_post_type_slug( 'certificate' ):
				$step_label = esc_html__( 'Next Certificate', 'ebox' );
				break;

			default:
				$step_label = sprintf(
					// translators: placeholder: Post Type Singular label.
					esc_html_x( 'Next %s', 'placeholder: Post Type Singular label', 'ebox' ),
					$post_type_object->labels->singular_name
				);
				break;
		}
	} else {
		$step_label = sprintf(
			// translators: placeholder: Post Type slug.
			esc_html_x( 'Next %s', 'placeholder: Post Type slug', 'ebox' ),
			$step_post_type
		);
	}

	/**
	 * Filters value of course step next label. Used to update step next label in ebox_get_label_course_step_next function.
	 *
	 * @param string  $step_label     Course step `Next ...` label.
	 * @param string  $step_post_type The post_type slug of the post to return label for.
	 */
	return apply_filters( 'ebox_get_label_course_step_next', $step_label, $step_post_type );
}

/**
 * Get Course Step "... Page" label.
 *
 * This is used on the Admin are when editing a post type. There is a return link in the top-left.
 *
 * @since 3.0.7
 *
 * @param string $step_post_type The post_type slug of the post to return label for.
 *
 * @return string label.
 */
function ebox_get_label_course_step_page( string $step_post_type ): string {
	$post_type_object = get_post_type_object( $step_post_type );

	if ( $post_type_object && is_a( $post_type_object, 'WP_Post_Type' ) ) {
		switch ( $step_post_type ) {
			case ebox_get_post_type_slug( 'course' ):
				$step_label = sprintf(
					// translators: placeholder: Course.
					esc_html_x( '%s page', 'placeholder: Course', 'ebox' ),
					$post_type_object->labels->singular_name
				);
				break;

			case ebox_get_post_type_slug( 'lesson' ):
				$step_label = sprintf(
					// translators: placeholder: Lesson.
					esc_html_x( '%s page', 'placeholder: Lesson', 'ebox' ),
					$post_type_object->labels->singular_name
				);
				break;

			case ebox_get_post_type_slug( 'topic' ):
				$step_label = sprintf(
					// translators: placeholder: Topic.
					esc_html_x( '%s page', 'placeholder: Topic', 'ebox' ),
					$post_type_object->labels->singular_name
				);
				break;

			case ebox_get_post_type_slug( 'quiz' ):
				$step_label = sprintf(
					// translators: placeholder: Quiz.
					esc_html_x( '%s page', 'placeholder: Quiz', 'ebox' ),
					$post_type_object->labels->singular_name
				);
				break;

			case ebox_get_post_type_slug( 'question' ):
				$step_label = sprintf(
					// translators: placeholder: Question.
					esc_html_x( '%s page', 'placeholder: Question', 'ebox' ),
					$post_type_object->labels->singular_name
				);
				break;

			case ebox_get_post_type_slug( 'transaction' ):
				$step_label = sprintf(
					// translators: placeholder: Transaction.
					esc_html_x( '%s page', 'placeholder: Transaction', 'ebox' ),
					$post_type_object->labels->singular_name
				);
				break;

			case ebox_get_post_type_slug( 'team' ):
				$step_label = sprintf(
					// translators: placeholder: Team.
					esc_html_x( '%s page', 'placeholder: Team', 'ebox' ),
					$post_type_object->labels->singular_name
				);
				break;

			case ebox_get_post_type_slug( 'assignment' ):
				$step_label = sprintf(
					// translators: placeholder: Assignment.
					esc_html_x( '%s page', 'placeholder: Assignment', 'ebox' ),
					$post_type_object->labels->singular_name
				);
				break;

			case ebox_get_post_type_slug( 'essay' ):
				$step_label = sprintf(
					// translators: placeholder: Essay.
					esc_html_x( '%s page', 'placeholder: Essay', 'ebox' ),
					$post_type_object->labels->singular_name
				);
				break;

			case ebox_get_post_type_slug( 'certificate' ):
				$step_label = esc_html__( 'Certificate page', 'ebox' );
				break;

			default:
				$step_label = sprintf(
					// translators: placeholder: Post Type Singular label.
					esc_html_x( '%s page', 'placeholder: Post Type Singular label', 'ebox' ),
					$post_type_object->labels->singular_name
				);
				break;
		}
	} else {
		$step_label = sprintf(
			// translators: placeholder: Post Type slug.
			esc_html_x( '%s page', 'placeholder: Post Type slug', 'ebox' ),
			$step_post_type
		);
	}

	/**
	 * Filters value of course step page label. Used to update step page label in ebox_get_label_course_step_page function.
	 *
	 * @param string  $step_label     Course Step `... Page` label.
	 * @param string  $step_post_type The post_type slug of the post to return label for.
	 */
	return apply_filters( 'ebox_get_label_course_step_page', $step_label, $step_post_type );
}
