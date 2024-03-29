<?php
/**
 * ebox `Course Navigation` Widget Class.
 *
 * @since 2.1.0
 * @package ebox\Widgets
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( ! class_exists( 'ebox_Course_Navigation_Widget' ) ) && ( class_exists( 'WP_Widget' ) ) ) {

	/**
	 * Class for ebox `Course Navigation` Widget.
	 *
	 * @since 2.1.0
	 * @uses WP_Widget
	 */
	class ebox_Course_Navigation_Widget extends WP_Widget {

		/**
		 * Public constructor for Widget Class.
		 *
		 * @since 2.1.0
		 */
		public function __construct() {
			$widget_ops  = array(
				'classname'   => 'widget_ldcoursenavigation',
				// translators: placeholder: Course, modules, topics, course.
				'description' => sprintf( esc_html_x( 'ebox - %1$s Navigation. Shows %2$s and %3$s on the current %4$s.', 'placeholder: Course, modules, topics, course', 'ebox' ), ebox_get_custom_label( 'course' ), ebox_get_custom_label_lower( 'modules' ), ebox_get_custom_label_lower( 'topics' ), ebox_get_custom_label_lower( 'course' ) ),
			);
			$control_ops = array();
			// translators: placeholder: Course.
			parent::__construct( 'widget_ldcoursenavigation', sprintf( esc_html_x( '%s Navigation', 'Course Navigation Label', 'ebox' ), ebox_get_custom_label( 'course' ) ), $widget_ops, $control_ops );
		}

		/**
		 * Displays widget
		 *
		 * @since 2.1.0
		 *
		 * @param array $args     widget arguments.
		 * @param array $instance widget instance.
		 */
		public function widget( $args, $instance ) {
			global $ebox_shortcode_used;

			$post = get_post( get_the_id() );

			if ( ( ! is_a( $post, 'WP_Post' ) ) || ( empty( $post->ID ) ) || ( ! is_single() ) ) {
				return;
			}

			$course_id = ebox_get_course_id( $post->ID );
			if ( empty( $course_id ) ) {
				return;
			}

			$instance['show_widget_wrapper'] = true;
			$instance['current_lesson_id']   = 0;
			$instance['current_step_id']     = 0;

			$lesson_query_args       = array();
			$course_modules_per_page = ebox_get_course_modules_per_page( $course_id );
			if ( $course_modules_per_page > 0 ) {
				if ( in_array( $post->post_type, array( 'ebox-modules', 'ebox-topic', 'ebox-quiz' ), true ) ) {

					$instance['current_step_id'] = $post->ID;
					if ( 'ebox-modules' === $post->post_type ) {
						$instance['current_lesson_id'] = $post->ID;
					} elseif ( in_array( $post->post_type, array( 'ebox-topic', 'ebox-quiz' ), true ) ) {
						$instance['current_lesson_id'] = ebox_course_get_single_parent_step( $course_id, $post->ID, 'ebox-modules' );
					}

					if ( ! empty( $instance['current_lesson_id'] ) ) {
						$course_lesson_ids = ebox_course_get_steps_by_type( $course_id, 'ebox-modules' );
						if ( ! empty( $course_lesson_ids ) ) {
							$course_modules_paged = array_chunk( $course_lesson_ids, $course_modules_per_page, true );
							$modules_paged        = 0;
							foreach ( $course_modules_paged as $paged => $paged_set ) {
								if ( in_array( $instance['current_lesson_id'], $paged_set ) ) { // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
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
				if ( in_array( $post->post_type, array( 'ebox-modules', 'ebox-topic', 'ebox-quiz' ), true ) ) {

					$instance['current_step_id'] = $post->ID;
					if ( 'ebox-modules' === $post->post_type ) {
						$instance['current_lesson_id'] = $post->ID;
					} elseif ( in_array( $post->post_type, array( 'ebox-topic', 'ebox-quiz' ), true ) ) {
						$instance['current_lesson_id'] = ebox_course_get_single_parent_step( $course_id, $post->ID, 'ebox-modules' );
					}
				}
			}

			extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract

			/** This filter is documented in https://developer.wordpress.org/reference/hooks/widget_title/ */
			$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance );

			echo $before_widget; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Need to output HTML.

			if ( ! empty( $title ) ) {
				echo $before_title . $title . $after_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Need to output HTML.
			}

			ebox_course_navigation( $course_id, $instance, $lesson_query_args );

			echo $after_widget; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Need to output HTML.

			$ebox_shortcode_used = true;
		}

		/**
		 * Handles widget updates in admin
		 *
		 * @since 2.1.0
		 *
		 * @param array $new_instance New instance.
		 * @param array $old_instance Old instance.
		 *
		 * @return array $instance
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = $old_instance;

			$instance['title'] = wp_strip_all_tags( $new_instance['title'] );

			$instance['show_lesson_quizzes'] = isset( $new_instance['show_lesson_quizzes'] ) ? (bool) $new_instance['show_lesson_quizzes'] : false;
			$instance['show_topic_quizzes']  = isset( $new_instance['show_topic_quizzes'] ) ? (bool) $new_instance['show_topic_quizzes'] : false;
			$instance['show_course_quizzes'] = isset( $new_instance['show_course_quizzes'] ) ? (bool) $new_instance['show_course_quizzes'] : false;

			return $instance;
		}

		/**
		 * Display widget form in admin
		 *
		 * @since 2.1.0
		 *
		 * @param array $instance widget instance.
		 *
		 * @return string Default return is 'noform'.
		 */
		public function form( $instance ) {
			$instance            = wp_parse_args( (array) $instance, array( 'title' => '' ) );
			$title               = wp_strip_all_tags( $instance['title'] );
			$show_lesson_quizzes = isset( $instance['show_lesson_quizzes'] ) ? (bool) $instance['show_lesson_quizzes'] : false;
			$show_topic_quizzes  = isset( $instance['show_topic_quizzes'] ) ? (bool) $instance['show_topic_quizzes'] : false;
			$show_course_quizzes = isset( $instance['show_course_quizzes'] ) ? (bool) $instance['show_course_quizzes'] : false;
			ebox_replace_widgets_alert();
			?>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'ebox' ); ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
				</p>
				<p>
					<input class="checkbox" type="checkbox" <?php checked( $show_course_quizzes ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_course_quizzes' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_course_quizzes' ) ); ?>" />
					<label for="<?php echo esc_attr( $this->get_field_id( 'show_course_quizzes' ) ); ?>">
					<?php
					// translators: placeholders: Course, Quizzes.
					echo sprintf( esc_html_x( 'Show %1$s %2$s?', 'placeholders: Course, Quizzes', 'ebox' ), ebox_Custom_Label::get_label( 'course' ), ebox_Custom_Label::get_label( 'quizzes' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
					?>
					</label>
				</p>
				<p>
					<input class="checkbox" type="checkbox" <?php checked( $show_lesson_quizzes ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_lesson_quizzes' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_lesson_quizzes' ) ); ?>" />
					<label for="<?php echo esc_attr( $this->get_field_id( 'show_lesson_quizzes' ) ); ?>">
					<?php
					// translators: placeholders: Lesson, Quizzes.
					echo sprintf( esc_html_x( 'Show %1$s %2$s?', 'placeholders: Lesson, Quizzes', 'ebox' ), ebox_Custom_Label::get_label( 'lesson' ), ebox_Custom_Label::get_label( 'quizzes' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
					?>
					</label>
				</p>
				<p>
					<input class="checkbox" type="checkbox" <?php checked( $show_topic_quizzes ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_topic_quizzes' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_topic_quizzes' ) ); ?>" />
					<label for="<?php echo esc_attr( $this->get_field_id( 'show_topic_quizzes' ) ); ?>">
					<?php
					// translators: placeholders: Topic, Quizzes.
					echo sprintf( esc_html_x( 'Show %1$s %2$s?', 'placeholders: Topic, Quizzes', 'ebox' ), ebox_Custom_Label::get_label( 'topic' ), ebox_Custom_Label::get_label( 'quizzes' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
					?>
					</label>
				</p>
			<?php
			return '';
		}
	}

	add_action(
		'widgets_init',
		function() {
			return register_widget( 'ebox_Course_Navigation_Widget' );
		}
	);
}
