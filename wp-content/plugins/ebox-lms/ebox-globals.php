<?php
/**
 * ebox global variables.
 *
 * @since 4.5.0
 *
 * @package ebox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Globals that hold CPT's and Pages to be set up
 */
global $ebox_taxonomies, $ebox_pages, $ebox_question_types;

$ebox_taxonomies = array(
	'ld_course_category',
	'ld_course_tag',
	'ld_lesson_category',
	'ld_lesson_tag',
	'ld_topic_category',
	'ld_topic_tag',
	'ld_quiz_category',
	'ld_quiz_tag',
	'ld_question_category',
	'ld_question_tag',
	'ld_team_category',
	'ld_team_tag',
);

$ebox_pages = array(
	'team_admin_page',
	'ebox-lms-reports',
);

// This is a global variable which is set in any of the shortcode handler functions.
// The purpose is to let the plugin know when and if the any of the shortcodes were used.
global $ebox_shortcode_used;
$ebox_shortcode_used = false;

global $ebox_shortcode_atts;
$ebox_shortcode_atts = array();

/**
 * Metaboxes registered for settings pages etc.
 */
global $ebox_metaboxes;
$ebox_metaboxes = array();

global $ebox_assets_loaded;
$ebox_assets_loaded            = array();
$ebox_assets_loaded['styles']  = array();
$ebox_assets_loaded['scripts'] = array();
