<?php
/**
 * ebox `modules` Widget Class.
 *
 * @since 2.1.0
 * @package ebox\Widgets
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( ! class_exists( 'Lesson_Widget' ) ) && ( class_exists( 'WP_Widget' ) ) ) {

	/**
	 * Class for ebox `modules` Widget.
	 *
	 * @since 2.1.0
	 * @uses WP_Widget
	 */
	class Lesson_Widget extends WP_Widget /* phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound */ {

		/**
		 * Post type
		 *
		 *  @var string $post_type.
		 */
		protected $post_type = 'ebox-modules';

		/**
		 * Post name
		 *
		 * @var string $post_name.
		 */
		protected $post_name = 'Lesson';

		/**
		 * Post arguments
		 *
		 * @var object $post_args.
		 */
		protected $post_args;

		/**
		 * Public constructor for Widget Class.
		 *
		 * @since 2.1.0
		 */
		public function __construct() {
			$args = array();

			$this->post_name = ebox_Custom_Label::get_label( 'lesson' );

			// translators: placeholders: modules, Course, Lesson.
			$args['description'] = sprintf( esc_html_x( 'Displays a list of %1$s for a %2$s and tracks %3$s progress.', 'placeholders: modules, Course, Lesson', 'ebox' ), ebox_Custom_Label::get_label( 'modules' ), ebox_Custom_Label::get_label( 'course' ), ebox_Custom_Label::get_label( 'lesson' ) );

			if ( empty( $this->post_args ) ) {
				$this->post_args = array(
					'post_type'   => $this->post_type,
					'numberposts' => -1,
					'order'       => 'DESC',
					'orderby'     => 'date',
				);
			}

			parent::__construct( "{$this->post_type}-widget", $this->post_name, $args );
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

			extract( $args, EXTR_SKIP ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract

			/* Before Widget content */
			$buf = $before_widget;

			/** This filter is documented in https://developer.wordpress.org/reference/hooks/widget_title/ */
			$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

			if ( ! empty( $title ) ) {
				$buf .= $before_title . $title . $after_title;
			}

			$buf .= '<ul>';

			/* Display Widget Data */
			$course_id = ebox_get_course_id();

			if ( empty( $course_id ) || ! is_single() ) {
				return '';
			}

			$course_modules_list          = $this->course_modules_list( $course_id );
			$stripped_course_modules_list = wp_strip_all_tags( $course_modules_list );

			if ( empty( $stripped_course_modules_list ) ) {
				return '';
			}

			$buf .= $course_modules_list;

			/* After Widget content */
			$buf .= '</ul>' . $after_widget;

			echo $buf; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Need to output HTML.

			$ebox_shortcode_used = true;

		}

		/**
		 * Sets up course lesson list HTML
		 *
		 * @since 2.1.0
		 *
		 * @param int $course_id course id.
		 *
		 * @return string $html output
		 */
		public function course_modules_list( $course_id ) {
			$course = get_post( $course_id );

			if ( empty( $course->ID ) || $course_id != $course->ID ) {
				return '';
			}

			$html                  = '';
			$course_lesson_orderby = ebox_get_setting( $course_id, 'course_lesson_orderby' );
			$course_lesson_order   = ebox_get_setting( $course_id, 'course_lesson_order' );
			$modules               = ebox_lms_get_post_options( 'ebox-modules' );
			$orderby               = ( empty( $course_lesson_orderby ) ) ? $modules['orderby'] : $course_lesson_orderby;
			$order                 = ( empty( $course_lesson_order ) ) ? $modules['order'] : $course_lesson_order;
			$post__in              = '';
			$meta_key              = 'course_id';
			$meta_value            = $course_id;

			if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Courses_Builder', 'shared_steps' ) == 'yes' ) {
				$course_modules = ebox_course_get_steps_by_type( $course_id, 'ebox-modules' );
				if ( ! empty( $course_modules ) ) {
					$order      = '';
					$orderby    = 'post__in';
					$post__in   = implode( ',', $course_modules );
					$meta_key   = '';
					$meta_value = '';
				}
			}

			$shortcode = '[ebox-modules meta_key="' . $meta_key . '" meta_value="' . $meta_value . '" order="' . $order . '" orderby="' . $orderby . '" post__in="' . $post__in . '" posts_per_page="' . $modules['posts_per_page'] . '" wrapper="li"]';

			$modules = wptexturize( do_shortcode( $shortcode ) );

			$html .= $modules;
			return $html;
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
			/* Updates widget title value */
			$instance          = $old_instance;
			$instance['title'] = wp_strip_all_tags( $new_instance['title'] );
			return $instance;
		}

		/**
		 * Display widget form in admin
		 *
		 * @since 2.1.0
		 *
		 * @param array $instance widget instance.
		 * @return string Default return is 'noform'.
		 */
		public function form( $instance ) {
			if ( $instance ) {
				$title = esc_attr( $instance['title'] );
			} else {
				$title = $this->post_name;
			}
			ebox_replace_widgets_alert();
			?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'ebox' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>
			<?php
			return '';
		}
	}

	add_action(
		'widgets_init',
		function() {
			return register_widget( 'Lesson_Widget' );
		}
	);
}
