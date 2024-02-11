<?php
/**
 * ebox scalar constants
 *
 *
 * @package ebox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define ebox LMS - Set the settings database version.
 *
 * This define controls logic specific to the Activity database tables schema.
 *
 * @internal Will be set by ebox LMS.
 *
 * @var string $value PHP version x.x.x or x.x.x.x format.
 */
define( 'ebox_SETTINGS_DB_VERSION', '2.5' );

/**
 * Define ebox LMS - Set the settings database upgrade trigger version.
 *
 * This define controls admin prompts to perform a data upgrades.
 *
 * @internal Will be set by ebox.
 *
 * @var string $value PHP version x.x.x or x.x.x.x format.
 */
define( 'ebox_SETTINGS_TRIGGER_UPGRADE_VERSION', '2.5' );

/**
 * Define ebox LMS - Set the text domain.
 *
 * This define is used when loading the text domain files.
 * Should NOT be used for actual text domain string markers.
 *
 * @internal Will be set by ebox LMS.
 *
 * @var string $value PHP version x.x.x or x.x.x.x format.
 */
define( 'ebox_LMS_TEXT_DOMAIN', 'ebox' );

/**
 * Define ebox LMS - Set the minimum supported PHP version.
 *
 * @internal Will be set by ebox LMS.
 *
 * @var string $value PHP version x.x.x or x.x.x.x format.
 */
define( 'ebox_MIN_PHP_VERSION', '7.3' );

/**
 * Define ebox LMS - Set the minimum supported MySQL version.
 *
 * @internal Will be set by ebox LMS.
 *
 * @var string $value PHP version x.x.x or x.x.x.x format.
 */
define( 'ebox_MIN_MYSQL_VERSION', '5.6' );

/**
 * Define ebox LMS - Set the minimum supported MariaDB version.
 *
 * @internal Will be set by ebox LMS.
 *
 * @var string $value PHP version x.x.x or x.x.x.x format.
 */
define( 'ebox_MIN_MARIA_VERSION', '10.0' );

if ( ! defined( 'ebox_LMS_LIBRARY_DIR' ) ) {
	/**
	 * Define ebox LMS - Set the plugin includes/lib path.
	 *
	 * Will be set based on the ebox define `ebox_LMS_PLUGIN_DIR`.
	 *
	 * @uses ebox_LMS_PLUGIN_DIR
	 *
	 * @var string $value Directory path to plugin includes/lib internal directory.
	 */
	define( 'ebox_LMS_LIBRARY_DIR', trailingslashit( ebox_LMS_PLUGIN_DIR ) . 'includes/lib' );
}

if ( ! defined( 'ebox_LMS_LIBRARY_URL' ) ) {
	/**
	 * Define ebox LMS - Set the plugin includes/lib relative URL.
	 *
	 * Will be set based on the ebox define `ebox_LMS_PLUGIN_URL`.
	 *
	 * @uses ebox_LMS_PLUGIN_URL
	 *
	 * @var string $value URL to plugin includes/lib directory.
	 */
	define( 'ebox_LMS_LIBRARY_URL', trailingslashit( ebox_LMS_PLUGIN_URL ) . 'includes/lib' );
}

if ( ! defined( 'ebox_OBJECT_CACHE_ENABLED' ) ) {
	/**
	 * Define ebox LMS - Enabled support for object cache used for temporary storage.
	 *
	 *
	 * @var bool $value {
	 *    Only one of the following values.
	 *    @type bool true  Will enable object storage support. Default.
	 *    @type bool false Will disable object cache support.
	 * }
	 */
	define( 'ebox_OBJECT_CACHE_ENABLED', true );
}

if ( ! defined( 'ebox_TRANSIENTS_DISABLED' ) ) {
	/**
	 * Define ebox LMS - Enabled support for Transients used for temporary storage.
	 *
	 *	Initial value `false`.
	 *  Set to `true` as default to disable transients.
	 *
	 * @var bool $value {
	 *    Only one of the following values.
	 *    @type bool true  Will disable transient storage. Default.
	 *    @type bool false Will enable transient storage.
	 * }
	 */
	define( 'ebox_TRANSIENTS_DISABLED', true );
}

if ( ! defined( 'ebox_REPORT_TRANSIENT_STORAGE' ) ) {
	/**
	 * Define ebox LMS - Controls the Course/Quiz Report transient cache storage used.
	 *
	 * 
	 * Use {@see 'ebox_TRANSIENT_CACHE_STORAGE'} instead.
	 *
	 * @var string|bool $value {
	 *    Only one of the following values.
	 *    @type bool   false     Default as of 3.5.0.
	 *    @type string 'options' Will use the `wp_options` table.
	 *    @type string 'file'    Will save cache data in file within `wp-content/uploads/ebox/reports/`.
	 * }
	 */
	define( 'ebox_REPORT_TRANSIENT_STORAGE', false );
}

if ( ! defined( 'ebox_TRANSIENT_CACHE_STORAGE' ) ) {
	$ebox_default_resource_transient_storage = 'file';
	if ( ( defined( 'ebox_REPORT_TRANSIENT_STORAGE' ) ) && ( is_string( ebox_REPORT_TRANSIENT_STORAGE ) ) ) {
		$ebox_default_resource_transient_storage = esc_attr( ebox_REPORT_TRANSIENT_STORAGE );
		if ( ! in_array( $ebox_default_resource_transient_storage, array( 'file', 'options' ), true ) ) {
			$ebox_default_resource_transient_storage = 'file';
		}
	}

	/**
	 * Define ebox LMS - Controls Resource transient cache storage used.
	 *
	 * This is used for Data Upgrades, Reports, and other processing.
	 *
	 * 
	 *
	 * @var string $value {
	 *    Only one of the following values.
	 *    @type string options Will use the wp_options table. Default.
	 *    @type string file    Will save cache data in file within `wp-content/uploads/ebox/reports/`.
	 * }
	 */
	define( 'ebox_TRANSIENT_CACHE_STORAGE', $ebox_default_resource_transient_storage );
}

if ( ! defined( 'ebox_DEBUG' ) ) {
	/**
	 * Define ebox LMS - Enable debug message output.
	 *
	 * 
	 *
	 * @var bool $value {
	 *    Only one of the following values.
	 *    @type bool true  Will output debug message similar to the WordPress WP_DEBUG define.
	 *    @type bool false Default
	 * }
	 */
	define( 'ebox_DEBUG', false );
}

if ( ! defined( 'ebox_ERROR_REPORTING_ZERO' ) ) {
	/**
	 * Define ebox LMS - Enable legacy error handling logic where the PHP
	 * error_reporting(0) was set.
	 *
	 * 
	 *
	 * @var bool $value {
	 *    Only one of the following values.
	 *    @type bool true  Enable the function error_reporting(0) to be used. Legacy.
	 *    @type bool false Default.
	 * }
	 */
	define( 'ebox_ERROR_REPORTING_ZERO', false );
}

if ( ! defined( 'ebox_SCRIPT_DEBUG' ) ) {
	if ( ( defined( 'SCRIPT_DEBUG' ) ) && ( SCRIPT_DEBUG === true ) ) {
		$ebox_define_script_debug_value = true;
	} else {
		$ebox_define_script_debug_value = false;
	}

	/**
	 * Define ebox LMS - Enable load of non-minified CSS/JS assets.
	 *
	 * If the WordPress SCRIPT_DEBUG or ebox ebox_SCRIPT_DEBUG
	 * are set then ebox_SCRIPT_DEBUG will also be set to (bool) true.
	 *
	 * 
	 *
	 * @var bool $value {
	 *    Only one of the following values.
	 *    @type bool true  The non-minified versions of CSS/JS assets will be used.
	 *    @type bool false The minified CSS/JS assets will be used. Default.
	 * }
	 */
	define( 'ebox_SCRIPT_DEBUG', $ebox_define_script_debug_value );
}

if ( ! defined( 'ebox_COURSE_FUNCTIONS_LEGACY' ) ) {
	/**
	 * Define ebox LMS - Enabled legacy Course Progression and Query logic.
	 *
	 * This define will be removed in a future release.
	 *
	 * 
	 *
	 * @var bool $value {
	 *    Only one of the following values.
	 *    @type bool true  The LD 3.3.x legacy course progression and query logic will be used.
	 *    @type bool false The LD 3.4.x improved course progression and query logic will be used. Default.
	 * }
	 */
	define( 'ebox_COURSE_FUNCTIONS_LEGACY', false );
}

if ( ! defined( 'ebox_BUILDER_STEPS_UPDATE_POST' ) ) {
	/**
	 * Define ebox LMS - Enables Controls the method used to update the builder step.
	 *
	 * 
	 *
	 * @var bool $value {
	 *    Only one of the following values.
	 *    @type bool true  Use the function `wp_update_post()` function.
	 *    @type bool false Use the default `wpdb::update()` and `clean_post_cache()` functions. Default.
	 * }
	 */
	define( 'ebox_BUILDER_STEPS_UPDATE_POST', false );
}

if ( ! defined( 'ebox_SCRIPT_VERSION_TOKEN' ) ) {
	$ebox_define_script_version_token_value = constant( 'ebox_VERSION' );

	if ( defined( 'ebox_SCRIPT_DEBUG' ) && ( ebox_SCRIPT_DEBUG === true ) ) {
		$ebox_define_script_version_token_value .= '-' . time();
	}

	/**
	 * Define ebox LMS - Sets a unique value to be appended to CSS/JS URLS.
	 *
	 * The default value is the plugin version `ebox_VERSION`. If `ebox_SCRIPT_DEBUG`
	 * is set to `true` the value will also append a timestamp ensuring a unique URL for each
	 * request.
	 *
	 * 
	 *
	 * @uses ebox_SCRIPT_DEBUG
	 * @uses ebox_VERSION
	 *
	 * @var string $value Default is define `ebox_VERSION` value.
	 */
	define( 'ebox_SCRIPT_VERSION_TOKEN', $ebox_define_script_version_token_value );
}

if ( ! defined( 'ebox_FILTER_PRIORITY_THE_CONTENT' ) ) {
	/**
	 * Define ebox LMS - Sets the priority when ebox hooks into the WordPress filter
	 * 'the_content' filter for the main course posts.
	 *
	 * 
	 *
	 * @var int $value Default is 30.
	 */
	define( 'ebox_FILTER_PRIORITY_THE_CONTENT', 30 );
}

if ( ! defined( 'ebox_REST_API_ENABLED' ) ) {
	/**
	 * Define ebox LMS - Enable support REST API.
	 *
	 * 
	 *
	 * @var bool $value Default is true.
	 */
	define( 'ebox_REST_API_ENABLED', true );
}

if ( ! defined( 'ebox_BLOCK_WORDPRESS_CPT_ROUTES' ) ) {
	/**
	 * Define ebox LMS - Enable block access to default WordPress CPT routes.
	 *
	 * Logic added to prevent access to the automatic routes created as part of
	 * WP core for Gutenberg enabled custom post types. This new logic will prevent
	 * visibility read access if used is not authenticated or does not have update
	 * capabilities.
	 *
	 * 
	 *
	 * @var bool $value Default is true.
	 */
	define( 'ebox_BLOCK_WORDPRESS_CPT_ROUTES', true );
}

if ( ! defined( 'ebox_LESSON_VIDEO' ) ) {
	/**
	 * Define ebox LMS - Enable support for Lesson/Topic Video Progression.
	 *
	 * 
	 *
	 * @var bool $value Default is true.
	 */
	define( 'ebox_LESSON_VIDEO', true );
}

if ( ! defined( 'ebox_COURSE_BUILDER' ) ) {
	/**
	 * Define ebox LMS - Enable support for Course Builder.
	 *
	 * 
	 *
	 * @var bool $value Default is true.
	 */
	define( 'ebox_COURSE_BUILDER', true );
}

/**
 * Define ebox LMS
 *
 * @ignore
 */
if ( ! defined( 'ebox_COURSE_STEPS_PRELOAD' ) ) {
	define( 'ebox_COURSE_STEPS_PRELOAD', true );
}

if ( ! defined( 'ebox_QUIZ_BUILDER' ) ) {
	/**
	 * Define ebox LMS - Enable support for Quiz Builder.
	 *
	 * 
	 *
	 * @var bool $value Default is true.
	 */
	define( 'ebox_QUIZ_BUILDER', true );
}

if ( ! defined( 'ebox_BUILDER_DEBUG' ) ) {
	/**
	 * Define ebox LMS - Enable load of non-minified CSS/JS assets for Builders.
	 *
	 * 
	 *
	 * @var bool $value Default is false.
	 */
	define( 'ebox_BUILDER_DEBUG', false );
}

if ( ! defined( 'ebox_GUTENBERG' ) ) {
	/**
	 * Define ebox LMS - Enable support for Gutenberg Editor.
	 *
	 *
	 *
	 * @var bool $value Default is true.
	 */
	define( 'ebox_GUTENBERG', true );
}

if ( ! defined( 'ebox_GUTENBERG_CONTENT_PARSE_LEGACY' ) ) {
	/**
	 * Define ebox LMS - Use legacy content parse for Gutenberg block rendering.
	 *
	 * 
	 *
	 * @var bool $value Default is false.
	 */
	define( 'ebox_GUTENBERG_CONTENT_PARSE_LEGACY', false );
}

if ( ! defined( 'ebox_TRANSLATIONS' ) ) {
	/**
	 * Define ebox LMS - Enable support for Translations downloads via GlotPress.
	 *
	 *
	 *
	 * @var bool $value Default is true.
	 */
	define( 'ebox_TRANSLATIONS', true );
}

if ( ! defined( 'ebox_HTTP_REMOTE_GET_TIMEOUT' ) ) {
	/**
	 * Define ebox LMS - Set timeout (seconds) on HTTP GET requests.
	 *
	 *
	 *
	 * @var int $value Default is 15.
	 */
	define( 'ebox_HTTP_REMOTE_GET_TIMEOUT', 15 );
}

if ( ! defined( 'ebox_HTTP_REMOTE_POST_TIMEOUT' ) ) {
	/**
	 * Define ebox LMS - Set timeout (seconds) on HTTP POST requests.
	 *
	 * 
	 *
	 * @var int $value Default is 15.
	 */
	define( 'ebox_HTTP_REMOTE_POST_TIMEOUT', 15 );
}

if ( ! defined( 'ebox_HTTP_BITBUCKET_README_DOWNLOAD_TIMEOUT' ) ) {
	/**
	 * Define ebox LMS - Set timeout (seconds) for BitBucket Readme download_url() request.
	 *
	 * 
	 *
	 * @var int $value Default is 15.
	 */
	define( 'ebox_HTTP_BITBUCKET_README_DOWNLOAD_TIMEOUT', 15 );
}

if ( defined( 'ebox_REPO_ERROR_THRESHOLD_COUNT' ) ) {
	/**
	 * Define ebox LMS - Set the number of consecutive errors before update attempts abort.
	 *
	 * 
	 *
	 * @var int $value Default is 3.
	 */
	define( 'ebox_REPO_ERROR_THRESHOLD_COUNT', 3 );
}

if ( defined( 'ebox_REPO_ERROR_THRESHOLD_TIME' ) ) {
	/**
	 * Define ebox LMS - Set the time (seconds) after abort before restarting tries.
	 *
	 * 
	 *
	 * @var int $value Default is 7200.
	 */
	define( 'ebox_REPO_ERROR_THRESHOLD_TIME', 2 * 60 * 60 );
}

if ( ! defined( 'ebox_LMS_DEFAULT_QUESTION_POINTS' ) ) {
	/**
	 * Define ebox LMS - Set the default quiz question points.
	 *
	 * 
	 *
	 * @var int $value Default is 1.
	 */
	define( 'ebox_LMS_DEFAULT_QUESTION_POINTS', 1 );
}

if ( ! defined( 'ebox_LMS_DEFAULT_ANSWER_POINTS' ) ) {
	/**
	 * Define ebox LMS - Set the default quiz question answer points.
	 *
	 * 
	 *
	 * @var int $value Default is 0.
	 */
	define( 'ebox_LMS_DEFAULT_ANSWER_POINTS', 0 );
}

if ( ! defined( 'ebox_LMS_DEFAULT_LAZY_LOAD_PER_PAGE' ) ) {
	/**
	 * Define ebox LMS - Set the number of items to lazy load per AJAX request.
	 *
	 * 
	 *
	 * @var int $value Default is 5000.
	 */
	define( 'ebox_LMS_DEFAULT_LAZY_LOAD_PER_PAGE', 5000 );
}

if ( ! defined( 'ebox_LMS_DEFAULT_DATA_UPGRADE_BATCH_SIZE' ) ) {
	/**
	 * Define ebox LMS - Set the number of items for Data Upgrade batch.
	 *
	 * 
	 *
	 * @var int $value Default is 1000.
	 */
	define( 'ebox_LMS_DEFAULT_DATA_UPGRADE_BATCH_SIZE', 1000 );
}

if ( ! defined( 'ebox_LMS_COURSE_STEPS_LOAD_BATCH_SIZE' ) ) {
	/**
	 * Define ebox LMS - Set the number of course steps objects load batch size.
	 *
	 * Used when loading course step WP_Post objects. On a very large course attempting
	 * to load too many post objects via a single query can impact server performance.
	 *
	 * 
	 *
	 * @var int $value Default is 500.
	 */
	define( 'ebox_LMS_COURSE_STEPS_LOAD_BATCH_SIZE', 500 );
}

if ( ! defined( 'ebox_LMS_DEFAULT_WIDGET_PER_PAGE' ) ) {
	/**
	 * Define ebox LMS - Set the default number of items per page.
	 *
	 * 
	 *
	 * @var int $value Default is 20.
	 */
	define( 'ebox_LMS_DEFAULT_WIDGET_PER_PAGE', 20 );
}

if ( ! defined( 'ebox_LMS_DEFAULT_CB_INSERT_CHUNK_SIZE' ) ) {
	/**
	 * Define ebox LMS - Set the number of items to insert/update when saving builder data.
	 *
	 * This value controls the query insert/update logic and does not limit the number of steps.
	 *
	 * 
	 *
	 * @var int $value Default is 10.
	 */
	define( 'ebox_LMS_DEFAULT_CB_INSERT_CHUNK_SIZE', 10 );
}

if ( ! defined( 'ebox_ADMIN_CAPABILITY_CHECK' ) ) {
	/**
	 * Define ebox LMS - Set the Administrator role capability check.
	 *
	 * The value should match a role capability used to determine if a user is
	 * and Administrator user. Default is 'manage_options'.
	 *
	 * 
	 *
	 * @var string $value Default is 'manage_options'.
	 */
	define( 'ebox_ADMIN_CAPABILITY_CHECK', 'manage_options' );
}

if ( ! defined( 'ebox_GROUP_LEADER_CAPABILITY_CHECK' ) ) {
	/**
	 * Define ebox LMS - Set the Team Leader role capability check.
	 *
	 * The value should match a role capability used to determine if a user is
	 * a Team Leader user. Default is 'team_leader'.
	 *
	 * 
	 *
	 * @var string $value Default is 'team_leader'.
	 */
	define( 'ebox_GROUP_LEADER_CAPABILITY_CHECK', 'team_leader' );
}

if ( ! defined( 'ebox_GROUP_LEADER_DASHBOARD_ACCESS' ) ) {

	/**
	 * Define ebox LMS - Control Team Leader access to WP Dashboard with WooCommerce.
	 *
	 * Used by `ebox_check_team_leader_access`
	 *
	 * 
	 *
	 * @var bool $value {
	 *    Only one of the following values.
	 *    @type bool true  Will allow Team Leader access to WP Dashboard. Default.
	 *    @type bool false Will prevent Team Leader access to WP Dashboard.
	 * }
	 */
	define( 'ebox_GROUP_LEADER_DASHBOARD_ACCESS', true );
}

if ( ! defined( 'ebox_DEFAULT_THEME' ) ) {
	/**
	 * Define ebox LMS - Set the default template used.
	 *
	 * This value is used to set the default theme on new installs.
	 *
	 * 
	 *
	 * @var string $value Default is 'ld30'.
	 */
	define( 'ebox_DEFAULT_THEME', 'ld30' );
}

if ( ! defined( 'ebox_LEGACY_THEME' ) ) {
	/**
	 * Define ebox LMS - Set the legacy template slug.
	 *
	 * 
	 *
	 * @var string $value Default is 'legacy'.
	 */
	define( 'ebox_LEGACY_THEME', 'legacy' );
}

if ( ! defined( 'ebox_DEFAULT_COURSE_PRICE_TYPE' ) ) {
	/**
	 * Define ebox LMS - Set the default course price type.
	 *
	 * 
	 *
	 * @var string $value {
	 *    Possible values one of the following.
	 *    @type string open      Price Type 'open'. Default.
	 *    @type string free      Price Type 'free'.
	 *    @type string paynow    Price Type 'paynow'.
	 *    @type string subscribe Price Type 'subscribe'.
	 *    @type string closed    Price Type 'closed'.
	 * }
	 */
	define( 'ebox_DEFAULT_COURSE_PRICE_TYPE', 'open' );
}

if ( ! defined( 'ebox_DEFAULT_COURSE_ORDER' ) ) {
	/**
	 * Define ebox LMS - Set the default course steps order. NOT USED
	 *
	 * 
	 * @ignore
	 *
	 * @var string $value {
	 *    Only one of the following values.
	 *    @type string ASC  Sort values Ascending. Default.
	 *    @type string DESC Sort values Descending.
	 * }
	 */
	define( 'ebox_DEFAULT_COURSE_ORDER', 'ASC' );
}

if ( ! defined( 'ebox_DEFAULT_COURSE_ORDERBY' ) ) {
	/**
	 * Define ebox LMS - Set the default course steps order by. NOT USED.
	 *
	 * 
	 * @ignore
	 *
	 * @var string $value {
	 *    Only one of the following values.
	 *    @type string date       Sort values by Date. Default.
	 *    @type string menu_order Sort values by menu_order.
	 *    @type string title      Sort values by title.
	 * }
	 */
	define( 'ebox_DEFAULT_COURSE_ORDERBY', 'date' );
}

if ( ! defined( 'ebox_COURSE_STEP_READ_CHECK' ) ) {
	/**
	 * Define ebox LMS - Enable logic to check if user can read course step WP_Post.
	 *
	 *
	 *
	 * @var bool $value Default is true.
	 */
	define( 'ebox_COURSE_STEP_READ_CHECK', true );
}

if ( ! defined( 'ebox_DEFAULT_GROUP_PRICE_TYPE' ) ) {
	/**
	 * Define ebox LMS - Set the default team price type.
	 *
	 *
	 *
	 * @var string $value {
	 *    Possible values one of the following.
	 *    @type string closed    Price Type 'closed'. Default.
	 *    @type string free      Price Type 'free'.
	 *    @type string paynow    Price Type 'paynow'.
	 *    @type string subscribe Price Type 'subscribe'.
	 * }
	 */
	define( 'ebox_DEFAULT_GROUP_PRICE_TYPE', 'closed' );
}

if ( ! defined( 'ebox_DEFAULT_GROUP_ORDER' ) ) {
	/**
	 * Define Ebox LMS - Set the default teams courses display order.
	 *
	 * 
	 *
	 * @var string $value {
	 *    Only one of the following values.
	 *    @type string ASC  Sort values Ascending. Default.
	 *    @type string DESC Sort values Descending.
	 * }
	 */
	define( 'ebox_DEFAULT_GROUP_ORDER', 'ASC' );
}

if ( ! defined( 'ebox_DEFAULT_GROUP_ORDERBY' ) ) {
	/**
	 * Define Ebox LMS - Set the default teams courses display order by.
	 *
	 * 
	 *
	 * @var string $value {
	 *    Only one of the following values.
	 *    @type string date       Sort values by Date. Default.
	 *    @type string menu_order Sort values by menu_order.
	 *    @type string title      Sort values by title.
	 * }
	 */
	define( 'ebox_DEFAULT_GROUP_ORDERBY', 'date' );
}

if ( ! defined( 'ebox_QUIZ_RESULT_MESSAGE_MAX' ) ) {
	/**
	 * Define ebox LMS - Set the maximum number of items used for the
	 * Quiz Result Message setting field.
	 *
	 * 
	 *
	 * @var int $value Default is 15.
	 */
	define( 'ebox_QUIZ_RESULT_MESSAGE_MAX', 15 );
}

if ( ! defined( 'ebox_QUIZ_RESUME_COOKIE_SEND_TIMER_MIN' ) ) {
	/**
	 * Define ebox LMS - Set the minimum second for sending quiz resume data to server.
	 *
	 * 
	 *
	 * @var int $value Default is 5.
	 */
	define( 'ebox_QUIZ_RESUME_COOKIE_SEND_TIMER_MIN', 5 );
}

if ( ! defined( 'ebox_QUIZ_RESUME_COOKIE_SEND_TIMER_DEFAULT' ) ) {
	/**
	 * Define ebox LMS - Set the default second for sending quiz resume data to server.
	 *
	 * 
	 *
	 * @var int $value Default is 5.
	 */
	define( 'ebox_QUIZ_RESUME_COOKIE_SEND_TIMER_DEFAULT', 20 );
}

if ( ! defined( 'ebox_QUIZ_ANSWER_MESSAGE_HTML_TYPE' ) ) {
	/**
	 * Define ebox LMS - Set the Quiz answer message wrapper
	 * HTML element type.
	 *
	 *
	 *
	 * @var string $value Default is 'div'.
	 */
	define( 'ebox_QUIZ_ANSWER_MESSAGE_HTML_TYPE', 'div' );
}


if ( ! defined( 'ebox_QUIZ_EXPORT_LEGACY' ) ) {
	/**
	 * Define ebox LMS - Use the legacy WPProQuiz import/export logic
	 * using unserialize/serialize instead of newer json_decode/json_encode.
	 *
	 * 
	 *
	 * @var bool $value Default is false.
	 */
	define( 'ebox_QUIZ_EXPORT_LEGACY', false );
}

if ( ! defined( 'ebox_QUIZ_PREREQUISITE_ALT' ) ) {
	/**
	 * Define ebox LMS - Controls the Quiz Prerequisite
	 * handling.
	 *
	 * If `true` the user must pass the prerequisite
	 * quizzes. If `false` the user must have only taken
	 * the prerequisite quizzes but not required to pass
	 * them.
	 *
	 * 
	 *
	 * @var bool $value Default is false.
	 */
	define( 'ebox_QUIZ_PREREQUISITE_ALT', true );
}


if ( ! defined( 'ebox_ADMIN_POPUP_STYLE' ) ) {
	/**
	 * Define ebox LMS - Set the popup method used for items like the
	 * TinyMCE popup used for shortcodes.
	 *
	 *
	 *
	 * @var string $value {
	 *    Only one of the following values.
	 *    @type string 'jQuery-dialog' Default.
	 *    @type string 'thickbox'      Legacy thickbox popup.
	 * }
	 */
	define( 'ebox_ADMIN_POPUP_STYLE', 'jQuery-dialog' );
}

if ( ! defined( 'ebox_USE_WP_SAFE_REDIRECT' ) ) {
	/**
	 * Define ebox LMS - Controls handling of redirects.
	 *
	 * @since 3.3.0.2
	 *
	 * @var bool $value {
	 *    Only one of the following values.
	 *    @type bool true  Use the WP function `wp_safe_redirect`. Default.
	 *    @type bool false Use the WP function `wp_redirect`.
	 * }
	 */
	define( 'ebox_USE_WP_SAFE_REDIRECT', true );
}

if ( ! defined( 'ebox_DISABLE_TEMPLATE_CONTENT_OUTSIDE_LOOP' ) ) {
	/**
	 * Define ebox LMS - Controls filtering of 'the_content' outside of the 'loop'.
	 *
	 * @since 3.2.3
	 *
	 * @var bool $value {
	 *    Only one of the following values.
	 *    @type bool true  If called outside the WP loop, content will not be processed. Default.
	 *    @type bool false Content will be processed.
	 * }
	 */
	define( 'ebox_DISABLE_TEMPLATE_CONTENT_OUTSIDE_LOOP', true );
}

if ( ! defined( 'ebox_TEMPLATE_CONTENT_METHOD' ) ) {
	/**
	 * Define ebox LMS - Controls the method the template content is rendered.
	 *
	 * @since 4.0.0
	 *
	 * @var string $value {
	 *    Only one of the following values.
	 *    @type string 'template'  Content will be rendered via the template. This is the legacy/default method.
	 *    @type string 'shortcode' Content will be rendered via shortcodes.
	 * }
	 */
	define( 'ebox_TEMPLATE_CONTENT_METHOD', 'shortcode' );
}

if ( ! defined( 'ebox_GROUP_ENROLLED_COURSE_FROM_USER_REGISTRATION' ) ) {
	/**
	 * Define ebox LMS - Control the determination of the user's Team enrollment time.
	 *
	 * @since 3.2.0
	 *
	 * @var bool $value {
	 *    Only one of the following values.
	 *    @type bool true  Use the user's registration for the Team enrollment time, if newer. Default.
	 *    @type bool false
	 * }
	 */
	define( 'ebox_GROUP_ENROLLED_COURSE_FROM_USER_REGISTRATION', true );
}

if ( ! defined( 'ebox_SELECT2_LIB' ) ) {
	/**
	 * Define ebox LMS - Enable use of the Select2 jQuery library.
	 *
	 * The Select2 library is used on post type listings and within admin setting
	 * used by ebox.
	 *
	 * @since 3.0.0
	 *
	 * @var bool $value Default is true.
	 */
	define( 'ebox_SELECT2_LIB', true );
}

if ( ! defined( 'ebox_SELECT2_LIB_AJAX_FETCH' ) ) {
	/**
	 * Define ebox LMS - Enable fetch logic as part of the Select2 library.
	 *
	 * Possible value:
	 * true (bool) Will enable callbacks to the server via AJAX to load selector
	 * items. This can improve performance. Default.
	 *
	 * The `ebox_SELECT2_LIB` define must be true.
	 *
	 * @since 3.2.3
	 *
	 * @var bool $value {
	 *    Only one of the following values.
	 *    @type bool true  Will enable callbacks to the server via AJAX to load selector. Default.
	 *    @type bool false
	 * }
	 */
	define( 'ebox_SELECT2_LIB_AJAX_FETCH', true );
}

if ( ! defined( 'ebox_SETTINGS_METABOXES_LEGACY' ) ) {
	/**
	 * Define ebox LMS - Enable legacy Post Type Settings Metaboxes.
	 *
	 * @since 3.0.0
	 *
	 * @var bool $value {
	 *    Only one of the following values.
	 *    @type bool true  Will use metabox containers when showing the settings outside of the post type editor. Default is true. Must be set to true.
	 *    @type bool false Not supported.
	 * }
	 */
	define( 'ebox_SETTINGS_METABOXES_LEGACY', true );
}

if ( ! defined( 'ebox_SETTINGS_METABOXES_LEGACY_QUIZ' ) ) {
	/**
	 * Define ebox LMS - Enable legacy WPProQuiz Post Type Settings Metaboxes.
	 *
	 * @since 3.0.0
	 *
	 * @var bool $value {
	 *    Only one of the following values.
	 *    @type bool true  Will show the legacy WPProQuiz linear listing of settings.
	 *    @type bool false Will display Quiz Post settings using newer metabox containers. Default.
	 * }
	 */
	define( 'ebox_SETTINGS_METABOXES_LEGACY_QUIZ', false );
}

if ( ! defined( 'ebox_SETTINGS_HEADER_PANEL' ) ) {
	/**
	 * Define ebox LMS - Enable the new (3.0.0) Header Panel.
	 *
	 * @since 3.0.0
	 *
	 * @var bool $value {
	 *    Only one of the following values.
	 *    @type bool true  Will show the ebox header panel on related admin pages. Default is true. Must be set to true.
	 *    @type bool false Not supported.
	 * }
	 */
	define( 'ebox_SETTINGS_HEADER_PANEL', true );
}

if ( ! defined( 'ebox_SHOW_MARK_INCOMPLETE' ) ) {
	/**
	 * Define ebox LMS - Enable the Mark Incomplete button on course steps. Beta.
	 *
	 * @since 3.1.4
	 *
	 * @var bool $value {
	 *    Only one of the following values.
	 *    @type bool true  Will display a button on completed course steps allowing the user. BETA.
	 *    @type bool false Default.
	 * }
	 */
	define( 'ebox_SHOW_MARK_INCOMPLETE', false );
}

if ( ! defined( 'ebox_FILTER_SEARCH' ) ) {
	/**
	 * Define ebox LMS - Enable search filter logic.
	 *
	 *
	 * @var bool $value {
	 *    Only one of the following values.
	 *    @type bool true  Will enable some logic to hook into the WP search processing.
	 *                     The logic can help filter display items to only show modules, topics, etc.
	 *                     the user has access to. Default.
	 *    @type bool false
	 * }
	 */
	define( 'ebox_FILTER_SEARCH', true );
}

if ( ! defined( 'ebox_LMS_DATABASE_PREFIX_SUB' ) ) {
	/**
	 * Define ebox LMS - Set the default database prefix.
	 *
	 * This prefix is appended to the WP table prefix.
	 *
	 * @since 3.1.0
	 *
	 * @var string $value Default is 'ebox_'.
	 */
	define( 'ebox_LMS_DATABASE_PREFIX_SUB', 'ebox_' );
}

if ( ! defined( 'ebox_PROQUIZ_DATABASE_PREFIX_SUB_DEFAULT' ) ) {
	/**
	 * Define ebox LMS - Set the default WPProQuiz database prefix.
	 *
	 * This prefix is appended to the WP table prefix.
	 *
	 * @since 3.1.0
	 *
	 * @var string $value Default is 'wp_'.
	 */
	define( 'ebox_PROQUIZ_DATABASE_PREFIX_SUB_DEFAULT', 'wp_' );
}

if ( ! defined( 'ebox_UPDATES_ENABLED' ) ) {
	/**
	 * Define ebox LMS - Enable support to check for updates for Core and Add-ons.
	 *
	 * @since 3.1.8
	 *
	 * @var bool $value {
	 *    Only one of the following values.
	 *    @type bool true  Will enable calls to support.ebox.com and bitbucket.org to check for updates. Default.
	 *    @type bool false Will disable outbound server calls.
	 * }
	 */
	define( 'ebox_UPDATES_ENABLED', true );
}

if ( ! defined( 'ebox_UPDATE_HTTP_METHOD' ) ) {
	/**
	 * Define ebox LMS - Configure the HTTP method use to connect to the support/license server.
	 *
	 * @since 3.6.0.3
	 *
	 * @var string $value {
	 *    Only one of the following values.
	 *    @type string 'post' Use HTTP POST (wp_remote_post) to connect to the server. Default.
	 *    @type string 'get'  Use HTTP GET (wp_remote_get) to connect to the server. Default.
	 * }
	 */
	define( 'ebox_UPDATE_HTTP_METHOD', 'get' );
}

if ( ! defined( 'ebox_PLUGIN_LICENSE_INTERVAL' ) ) {
	/**
	 * Define ebox LMS - Configure the interval for support license check.
	 *
	 * @since 3.6.0.3
	 *
	 * @var int $value number of minutes between license checks. Default is 3600 minutes (60 minutes).
	 */
	define( 'ebox_PLUGIN_LICENSE_INTERVAL', 3600 );
}

if ( ! defined( 'ebox_PLUGIN_LICENSE_OPTIONS_AUTOLOAD' ) ) {
	/**
	 * Define ebox LMS - Configure the autoload options for licensing.
	 *
	 * @since 4.3.0
	 *
	 * @var bool $value {
	 *    Only one of the following values.
	 *    @type bool true  Will enable autoload options.
	 *    @type bool false Will disable autoload options. Default.
	 * }
	 */
	define( 'ebox_PLUGIN_LICENSE_OPTIONS_AUTOLOAD', false );
}


if ( ! defined( 'ebox_PLUGIN_INFO_INTERVAL' ) ) {
	/**
	 * Define ebox LMS - Configure the interval for support information check.
	 *
	 * @since 3.6.0.3
	 *
	 * @var int $value number of minutes between information checks. Default is 600 minutes (10 minutes).
	 */
	define( 'ebox_PLUGIN_INFO_INTERVAL', 600 );
}

if ( ! defined( 'ebox_ADDONS_UPDATER' ) ) {
	$ebox_define_addons_updater_value = true;
	if ( defined( 'ebox_UPDATES_ENABLED' ) ) {
		$ebox_define_addons_updater_value = (bool) ebox_UPDATES_ENABLED;
	}

	/**
	 * Define ebox LMS - Enable support for Add-ons.
	 *
	 * @since 2.5.5
	 *
	 * @var bool $value {
	 *    Only one of the following values.
	 *    @type bool true  Will enable new menu items and install/update of related Add-ons. Default.
	 *    @type bool false
	 * }
	 */
	define( 'ebox_ADDONS_UPDATER', $ebox_define_addons_updater_value );
}
