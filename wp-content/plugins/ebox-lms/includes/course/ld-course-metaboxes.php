<?php
/**
 * Course Metaboxes.
 *
 * Introduces metaboxes at Add/Edit Course page to be used as
 * a wrapper by the React application at front-end.
 *
 * @since 3.0.0
 * @package ebox\Course
 */

namespace ebox\Course\Metaboxes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds the meta boxes to course post type.
 *
 * Fires on `ebox_add_meta_boxes` and `add_meta_boxes_ebox-courses` hook.
 *
 * @since 3.0.0
 */
function add_meta_boxes() {

	$screen = get_current_screen();

	if ( 'ebox-courses' !== get_post_type( get_the_ID() ) &&
		'ebox-courses_page_courses-builder' !== $screen->id ) {
		return;
	}

	add_meta_box(
		'ebox-course-modules',
		sprintf( '%s', \ebox_Custom_Label::get_label( 'modules' ) ),
		'ebox\Course\Metaboxes\meta_box_modules_callback',
		null,
		'side'
	);

	add_meta_box(
		'ebox-course-topics',
		sprintf( '%s', \ebox_Custom_Label::get_label( 'topics' ) ),
		'ebox\Course\Metaboxes\meta_box_topics_callback',
		null,
		'side'
	);

	add_meta_box(
		'ebox-course-quizzes',
		sprintf( '%s', \ebox_Custom_Label::get_label( 'quizzes' ) ),
		'ebox\Course\Metaboxes\meta_box_quizzes_callback',
		null,
		'side'
	);

}
add_action( 'add_meta_boxes_ebox-courses', 'ebox\Course\Metaboxes\add_meta_boxes' );
add_action( 'ebox_add_meta_boxes', 'ebox\Course\Metaboxes\add_meta_boxes' );

/**
 * Prints the lesson metabox content.
 *
 * @since 3.0.0
 */
function meta_box_modules_callback() {
	?>
	<div id="ebox-modules-app"></div>
	<?php
}

/**
 * Prints the topics metabox content.
 *
 * @since 3.0.0
 */
function meta_box_topics_callback() {
	?>
	<div id="ebox-topics-app"></div>
	<?php
}

/**
 * Prints the quizzes meta box content.
 *
 * @since 3.0.0
 */
function meta_box_quizzes_callback() {
	?>
	<div id="ebox-quizzes-app"></div>
	<?php
}
