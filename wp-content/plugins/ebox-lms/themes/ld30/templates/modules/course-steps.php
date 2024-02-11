<?php
/**
 * ebox LD30 Displays a Course Prev/Next navigation.
 *
 * Available Variables:
 *
 * $course_id        : (int) ID of Course
 * $course_step_post : (object) WP_Post instance of lesson/topic post
 * $user_id          : (int) ID of User
 * $course_settings  : (array) Settings specific to current course
 * $can_complete     : (bool) Can the user mark this lesson/topic complete?
 * $context		     : (string) Context of the usage. Either 'lesson', 'topic' or 'focus' use for Focus Mode header navigation.
 *
 * @since 3.0.0
 *
 * @package ebox\Templates\LD30
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! isset( $can_complete ) ) {
	$can_complete = false;
}


// TODO @37designs this is a bit confusing still, as you can still navigate left / right on modules even with topics.
if ( ( isset( $course_step_post ) ) && ( is_a( $course_step_post, 'WP_Post' ) ) && ( in_array( $course_step_post->post_type, ebox_get_post_types( 'course' ), true ) ) ) {
	if ( ebox_get_post_type_slug( 'lesson' ) === $course_step_post->post_type ) {
		$parent_id = absint( $course_id	);
	} else {
		$parent_id = ebox_course_get_single_parent_step( $course_id, $course_step_post->ID );
	}
} else {
	$parent_id = ( get_post_type() === 'ebox-modules' ? absint( $course_id ) : ebox_course_get_single_parent_step( $course_id, get_the_ID() ) );
}

$ebox_previous_step_id = ebox_previous_post_link( null, 'id', $course_step_post );
if ( ( empty( $ebox_previous_step_id ) ) && ( ebox_get_post_type_slug( 'topic' ) === $course_step_post->post_type ) ) {

	/**
	 * Filters whether to show parent previous link in the course navigation.
	 *
	 * @since 3.1.0
	 *
	 * @param boolean $show_previous_link Whether to show parent previous link.
	 * @param int     $course_step_post   ID of the lesson/topic post.
	 * @param int     $user_id            User ID.
	 * @param int     $course_id          Course ID.
	 */
	if ( apply_filters( 'ebox_show_parent_previous_link', true, $course_step_post, $user_id, $course_id ) ) {
		$ebox_previous_step_id = ebox_previous_post_link( null, 'id', get_post( $parent_id ) );
	}
}

$ebox_next_step_id = '';
$button_class           = 'ld-button ' . ( 'focus' === $context ? 'ld-button-transparent' : '' );

/*
 * See details for filter 'ebox_show_next_link' at https://developers.ebox.com/hook/ebox_show_next_link/
 *
 * @since version 2.3
 */

$current_complete = false;

if ( ( empty( $course_settings ) ) && ( ! empty( $course_id ) ) ) {
	$course_settings = ebox_get_setting( $course_id );
}

if ( 'ebox-topic' === $course_step_post->post_type ) {
	$current_complete = ebox_is_topic_complete( $user_id, $course_step_post->ID, $course_id );
} elseif ( 'ebox-modules' === $course_step_post->post_type ) {
	$current_complete = ebox_is_lesson_complete( $user_id, $course_step_post->ID, $course_id );
}

if ( ebox_lesson_hasassignments( $course_step_post ) ) {
	$user_assignments     = ebox_get_user_assignments( $course_step_post->ID, $user_id, absint( $course_id ), 'ids' );
	$approved_assignments = ebox_assignment_list_approved( $user_assignments, $course_step_post->ID, $user_id );
	if ( ! $approved_assignments ) {
		$current_complete = false;
	}
}

$ebox_maybe_show_next_step_link = $current_complete;
//if ( ( isset( $course_settings['course_disable_lesson_progression'] ) ) && ( 'on' === $course_settings['course_disable_lesson_progression'] ) ) {

$course_lesson_progression_enabled = ebox_lesson_progression_enabled( $course_id );
if ( ! $course_lesson_progression_enabled ) {
	$ebox_maybe_show_next_step_link = true;
}

if ( $ebox_maybe_show_next_step_link !== true ) {
	$bypass_course_limits_admin_users = ebox_can_user_bypass( $user_id, 'ebox_course_progression' );
	if ( true === $bypass_course_limits_admin_users ) {
		$ebox_maybe_show_next_step_link = true;
	}
}

/**
 * Filters whether to show the next link in the course navigation.
 *
 * @since 2.3.0
 *
 * @param bool $show_next_link Whether to show next link.
 * @param int  $user_id        User ID.
 * @param int  $step_id        ID of the lesson/topic post.
 *
 */
$ebox_maybe_show_next_step_link = apply_filters( 'ebox_show_next_link', $ebox_maybe_show_next_step_link, $user_id, $course_step_post->ID );

// Only complete modules/topics.
if ( ! in_array( $course_step_post->post_type, ebox_get_post_type_slug( array( 'lesson', 'topic' ) ), true ) ) {
	$can_complete = false;
	$current_complete = false;
	$ebox_maybe_show_next_step_link = false;
}

if ( true === (bool) $ebox_maybe_show_next_step_link ) {
	$ebox_next_step_id = ebox_next_post_link( null, 'id', $course_step_post );
	if ( ( empty( $ebox_next_step_id ) ) && ( ebox_get_post_type_slug( 'topic' ) === $course_step_post->post_type ) ) {
		$ebox_show_parent_next_link = false;
		if ( ( ! $course_lesson_progression_enabled ) || ( true === $current_complete ) || ( ebox_is_lesson_complete( $user_id, $parent_id ) ) ) {
			$ebox_show_parent_next_link = true;
		}

		/**
		 * Filters whether to show parent next link in the course navigation.
		 *
		 * @since 3.1.0
		 *
		 * @param bool $ebox_show_parent_next_link Whether to show parent next link.
		 * @param int  $course_step_post                ID of the lesson/topic post.
		 * @param int  $user_id                         User ID.
		 * @param int  $course_id                       Course ID.
		 */
		if ( apply_filters( 'ebox_show_parent_next_link', $ebox_show_parent_next_link, $course_step_post, $user_id, $course_id ) ) {
			$ebox_next_step_id = ebox_next_post_link( null, 'id', get_post( $parent_id ) );
		}
	}
} elseif ( ( ! is_user_logged_in() ) && ( empty( $ebox_next_step_id ) ) ) {
	$ebox_next_step_id = ebox_next_post_link( null, 'id', $course_step_post );
	if ( ( empty( $ebox_next_step_id ) ) && ( ebox_get_post_type_slug( 'topic' ) === $course_step_post->post_type ) ) {
		$ebox_next_step_id = ebox_next_post_link( null, 'id', get_post( $parent_id ) );
	}
	if ( ! empty( $ebox_next_step_id ) ) {
		if ( ! ebox_is_sample( $ebox_next_step_id ) ) {
			if ( ( ! isset( $course_settings['course_price_type'] ) ) || ( 'open' !== $course_settings['course_price_type'] ) ) {
				$ebox_next_step_id = '';
			}
		}
	}
}

/**
 * Filters to override next step post ID.
 *
 * @since 3.1.2
 *
 * @param int $ebox_next_step_id The next step post ID.
 * @param int $course_step_post       The current step WP_Post ID.
 * @param int $course_id              The current Course ID.
 * @param int $user_id                The current User ID.
 *
 * @return int $ebox_next_step_id
 */
$ebox_next_step_id = apply_filters( 'ebox_next_step_id', $ebox_next_step_id, $course_step_post->ID, $course_id, $user_id );

/**
 * Check if we need to show the Mark Complete form. see ebox-4722
 */
$parent_lesson_id = 0;
if ( $course_step_post->post_type == 'ebox-modules' ) {
	$parent_lesson_id = $course_step_post->ID;
} elseif ( $course_step_post->post_type == 'ebox-topic' || $course_step_post->post_type == 'ebox-quiz' ) {
	if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Courses_Builder', 'shared_steps' ) == 'yes' ) {
		$parent_lesson_id = ebox_course_get_single_parent_step( $course_id, $course_step_post->ID );
	} else {
		$parent_lesson_id = ebox_get_setting( $course_step_post, 'lesson' );
	}
}
if ( ! empty( $parent_lesson_id ) ) {
	$lesson_access_from = ld_lesson_access_from( $parent_lesson_id, $user_id, $course_id );
	if ( ( empty( $lesson_access_from ) ) || ( ! empty( $bypass_course_limits_admin_users ) ) ) {
		$complete_button = ebox_mark_complete( $course_step_post );
	} else {
		$complete_button = '';

	}
} else {
	$complete_button = ebox_mark_complete( $course_step_post );
}

if ( ( true === $current_complete ) && ( is_a( $course_step_post, 'WP_Post' ) ) ){
	$incomplete_button = ebox_show_mark_incomplete( $course_step_post );
} else {
	$incomplete_button = '';
}

?>
<div class="ld-content-actions">

	<?php
	/**
	 * Fires before the course steps (all locations).
	 *
	 * @since 3.0.0
	 *
	 * @param string|false $post_type Post type slug.
	 * @param int          $course_id Course ID.
	 * @param int          $user_id   User ID.
	 */
	do_action( 'ebox-all-course-steps-before', get_post_type(), $course_id, $user_id );

	/**
	 * Fires before the course steps for any context.
	 *
	 * The dynamic portion of the hook name, `$context`, refers to the context for which the hook is fired,
	 * such as `course`, `lesson`, `topic`, `quiz`, etc.
	 *
	 * @since 3.0.0
	 *
	 * @param string|false $post_type Post type slug.
	 * @param int          $course_id Course ID.
	 * @param int          $user_id   User ID.
	 */
	do_action( 'ebox-' . $context . '-course-steps-before', get_post_type(), $course_id, $user_id );
	//$ebox_current_post_type = get_post_type();
	?>
	<div class="ld-content-action <?php if ( ! $ebox_previous_step_id ) : ?>ld-empty<?php endif; ?>">
	<?php if ( $ebox_previous_step_id ) : ?>
		<a class="<?php echo esc_attr( $button_class ); ?>" href="<?php echo esc_url( ebox_get_step_permalink( $ebox_previous_step_id, $course_id ) ); ?>">
			<?php if ( is_rtl() ) { ?>
			<span class="ld-icon ld-icon-arrow-right"></span>
			<?php } else { ?>
			<span class="ld-icon ld-icon-arrow-left"></span>
			<?php } ?>
			<span class="ld-text"><?php echo esc_html( ebox_get_label_course_step_previous( get_post_type( $ebox_previous_step_id ) ) ); ?></span>
		</a>
	<?php endif; ?>
	</div>

	<?php

	if ( $parent_id && 'focus' !== $context ) :
		if ( $ebox_maybe_show_next_step_link ) :
			?>
			<div class="ld-content-action">
			<?php
			if ( ( true === $can_complete ) && ( true !== $current_complete ) && ( ! empty( $complete_button ) ) ) :
				echo ebox_mark_complete( $course_step_post );
			elseif ( ( true === $can_complete ) && ( true === $current_complete ) &&  ( ! empty( $incomplete_button ) ) ) :
				echo $incomplete_button;
				?>

			<?php endif; ?>
			<a href="<?php echo esc_url( ebox_get_step_permalink( $parent_id, $course_id ) ); ?>" class="ld-primary-color ld-course-step-back"><?php echo ebox_get_label_course_step_back( get_post_type( $parent_id ) ); ?></a>
			</div>
			<div class="ld-content-action <?php if ( ( ! $ebox_next_step_id ) ) : ?>ld-empty<?php endif; ?>">
			<?php if ( $ebox_next_step_id ) : ?>
				<a class="<?php echo esc_attr( $button_class ); ?>" href="<?php echo esc_url( ebox_get_step_permalink( $ebox_next_step_id, $course_id ) ); ?>">
					<span class="ld-text"><?php echo ebox_get_label_course_step_next( get_post_type( $ebox_next_step_id ) ); ?></span>
					<?php if ( is_rtl() ) { ?>
						<span class="ld-icon ld-icon-arrow-left"></span>
						<?php } else { ?>
						<span class="ld-icon ld-icon-arrow-right"></span>
					<?php } ?>
				</a>
			<?php endif; ?>
			</div>
			<?php else : ?>
			<a href="<?php echo esc_attr( ebox_get_step_permalink( $parent_id, $course_id ) ); ?>" class="ld-primary-color"><?php echo ebox_get_label_course_step_back( get_post_type( $parent_id ) ); ?></a>
			<div class="ld-content-action <?php if ( ( ! $can_complete ) && ( ! $ebox_next_step_id ) ) : ?>ld-empty<?php endif; ?>">
				<?php
				if ( ( true === $can_complete ) && ( true !== $current_complete ) && ( ! empty( $complete_button ) ) ) :
					echo $complete_button;
				elseif ( $ebox_next_step_id ) :
					?>
					<a class="<?php echo esc_attr( $button_class ); ?>" href="<?php echo esc_attr( ebox_get_step_permalink( $ebox_next_step_id, $course_id ) ); ?>">
					<span class="ld-text"><?php echo ebox_get_label_course_step_next( get_post_type( $ebox_next_step_id ) ); ?></span>
						<?php if ( is_rtl() ) { ?>
						<span class="ld-icon ld-icon-arrow-left"></span>
						<?php } else { ?>
						<span class="ld-icon ld-icon-arrow-right"></span>
						<?php } ?>
					</a>
			<?php endif; ?>
			</div>
		<?php endif; ?>
	<?php elseif ( $parent_id && 'focus' === $context ) : ?>
	<div class="ld-content-action <?php if ( ( ! $can_complete ) && ( ! $ebox_next_step_id ) ) : ?>ld-empty<?php endif; ?>">
		<?php
		if ( ( true === $can_complete ) && ( true !== $current_complete ) && ( ! empty( $complete_button ) ) ) :
			echo ebox_mark_complete( $course_step_post );
		elseif ( ( true === $can_complete ) && ( true === $current_complete ) &&  ( ! empty( $incomplete_button ) ) ) :
			echo $incomplete_button;
		elseif ( $ebox_next_step_id ) :
			?>
			<a class="<?php echo esc_attr( $button_class ); ?>" href="<?php echo esc_attr( ebox_get_step_permalink( $ebox_next_step_id, $course_id ) ); ?>">
				<span class="ld-text"><?php echo ebox_get_label_course_step_next( get_post_type( $ebox_next_step_id ) ); ?></span>
				<?php if ( is_rtl() ) { ?>
				<span class="ld-icon ld-icon-arrow-left"></span>
				<?php } else { ?>
				<span class="ld-icon ld-icon-arrow-right"></span>
				<?php } ?>
			</a>
		<?php endif; ?>
	</div>
	<?php endif; ?>
	<?php
	/**
	 * Fires after the course steps (all locations).
	 *
	 * @since 3.0.0
	 *
	 * @param string|false $post_type Post type slug.
	 * @param int          $course_id Course ID.
	 * @param int          $user_id   User ID.
	 */
	do_action( 'ebox-all-course-steps-after', get_post_type(), $course_id, $user_id );

	/**
	 * Fires after the course steps for any context.
	 *
	 * The dynamic portion of the hook name, `$context`, refers to the context for which the hook is fired,
	 * such as `course`, `lesson`, `topic`, `quiz`, etc.
	 *
	 * @since 3.0.0
	 *
	 * @param string|false $post_type Post type slug.
	 * @param int          $course_id Course ID.
	 * @param int          $user_id   User ID.
	 */
	do_action( 'ebox-' . $context . '-course-steps-after', get_post_type(), $course_id, $user_id );
	?>

</div> <!--/.ld-topic-actions-->

<?php
//endif;
