<?php
/**
 * Import loader
 *
 * @since 1.0.0
 * @package ebox\Import
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once ebox_LMS_PLUGIN_DIR . 'includes/import/class-ld-import-post.php';
require_once ebox_LMS_PLUGIN_DIR . 'includes/import/class-ld-import-course.php';
require_once ebox_LMS_PLUGIN_DIR . 'includes/import/class-ld-import-lesson.php';
require_once ebox_LMS_PLUGIN_DIR . 'includes/import/class-ld-import-topic.php';
require_once ebox_LMS_PLUGIN_DIR . 'includes/import/class-ld-import-quiz.php';
require_once ebox_LMS_PLUGIN_DIR . 'includes/import/class-ld-import-quiz-question.php';
require_once ebox_LMS_PLUGIN_DIR . 'includes/import/class-ld-import-quiz-statistics.php';
require_once ebox_LMS_PLUGIN_DIR . 'includes/import/class-ld-import-user-progress.php';
