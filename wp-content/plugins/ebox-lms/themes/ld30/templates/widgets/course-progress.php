<?php
/**
 * ebox LD30 Displays the course progress widget.
 *
 * @since 3.0.0
 *
 * @package ebox\Templates\LD30
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! isset( $user_id ) ) {
	$cuser   = wp_get_current_user();
	$user_id = $cuser->ID;
}

if ( ! isset( $course_id ) ) {
	$course_id = ( get_post_type() == 'ebox-courses' ? get_the_ID() : ebox_get_course_id( get_the_ID() ) );
} ?>

<div class="ebox-wrapper ebox-widget">
	<?php
	ebox_get_template_part(
		'modules/progress.php',
		array(
			'context'   => 'course',
			'course_id' => $course_id,
			'user_id'   => $user_id,
		),
		true
	);
	?>
</div>
