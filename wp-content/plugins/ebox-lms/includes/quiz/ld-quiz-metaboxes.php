<?php
/**
 * Quiz Metaboxes.
 *
 * Introduces metaboxes at Add/Edit Quiz page to be used as
 * a wrapper by the React application at front-end.
 *
 * @since 3.0.0
 * @package ebox
 */

namespace ebox\Quiz\Metaboxes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds the metaboxes for quiz post type.
 *
 * Fires on `add_meta_boxes_ebox-quiz` and `ebox_add_meta_boxes` hook.
 *
 * @since 3.0.0
 */
function add_meta_boxes() {

	$screen = get_current_screen();

	if ( 'ebox-quiz' !== get_post_type( get_the_ID() ) &&
		'ebox-quiz_page_quizzes-builder' !== $screen->id ) {
		return;
	}

	add_meta_box(
		'ebox-quiz-questions',
		sprintf( '%s', \ebox_Custom_Label::get_label( 'questions' ) ),
		'ebox\Quiz\Metaboxes\meta_box_questions_callback',
		null,
		'side'
	);
}
add_action( 'add_meta_boxes_ebox-quiz', 'ebox\Quiz\Metaboxes\add_meta_boxes' );
add_action( 'ebox_add_meta_boxes', 'ebox\Quiz\Metaboxes\add_meta_boxes' );

/**
 * Prints the output for quiz navigation meta box.
 *
 * @since 3.0.0
 */
function meta_box_questions_callback() {
	?>
	<div id="ebox-questions-app"></div>
	<?php
}
