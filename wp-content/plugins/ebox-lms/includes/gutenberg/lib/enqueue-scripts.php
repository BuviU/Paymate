<?php
/**
 * Enqueue scripts and stylesheets for Blocks
 *
 * @package ebox
 * @since 2.5.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueues block editor styles and scripts.
 *
 * Fires on `enqueue_block_editor_assets` hook.
 *
 * @since 2.5.8
 */
function ebox_editor_scripts() {
	// Make paths variables so we don't write em twice ;).
	$ebox_block_path         = '../assets/js/index.js';
	$ebox_editor_style_path  = '../assets/css/blocks.editor.css';
	$ebox_block_dependencies = include dirname( dirname( __FILE__ ) ) . '/assets/js/index.asset.php';

	// Enqueue the bundled block JS file.
	wp_enqueue_script(
		'ldlms-blocks-js',
		plugins_url( $ebox_block_path, __FILE__ ),
		$ebox_block_dependencies['dependencies'],
		ebox_SCRIPT_VERSION_TOKEN,
		true
	);

	// @TODO: This needs to move to an external JS library since it will be used globally.
	$ldlms = array(
		'settings' => array(),
	);

	$ldlms_settings['version'] = ebox_VERSION;

	$ldlms_settings['settings']['custom_labels'] = ebox_Settings_Section_Custom_Labels::get_section_settings_all();
	if ( ( is_array( $ldlms_settings['settings']['custom_labels'] ) ) && ( ! empty( $ldlms_settings['settings']['custom_labels'] ) ) ) {
		foreach ( $ldlms_settings['settings']['custom_labels'] as $key => $val ) {
			if ( empty( $val ) ) {
				$ldlms_settings['settings']['custom_labels'][ $key ] = ebox_Custom_Label::get_label( $key );
				if ( substr( $key, 0, strlen( 'button' ) ) != 'button' ) {
					$ldlms_settings['settings']['custom_labels'][ $key . '_lower' ] = ebox_get_custom_label_lower( $key );
					$ldlms_settings['settings']['custom_labels'][ $key . '_slug' ]  = ebox_get_custom_label_slug( $key );
				}
			}
		}
	}

	$ldlms_settings['settings']['per_page']            = ebox_Settings_Section_General_Per_Page::get_section_settings_all();
	$ldlms_settings['settings']['courses_taxonomies']  = ebox_Settings_Courses_Taxonomies::get_section_settings_all();
	$ldlms_settings['settings']['modules_taxonomies']  = ebox_Settings_modules_Taxonomies::get_section_settings_all();
	$ldlms_settings['settings']['topics_taxonomies']   = ebox_Settings_Topics_Taxonomies::get_section_settings_all();
	$ldlms_settings['settings']['quizzes_taxonomies']  = ebox_Settings_Quizzes_Taxonomies::get_section_settings_all();
	$ldlms_settings['settings']['teams_taxonomies']   = ebox_Settings_Teams_Taxonomies::get_section_settings_all();
	$ldlms_settings['settings']['teams_cpt']          = array( 'public' => ebox_Settings_Section::get_section_setting( 'ebox_Settings_Teams_CPT', 'public' ) );
	$ldlms_settings['settings']['registration_fields'] = ebox_Settings_Section_Registration_Fields::get_section_settings_all();

	// Templates - Added LD v4.0.0.
	$ldlms_settings['templates'] = array(
		'active' => ebox_Theme_Register::get_active_theme_key(),
	);

	$themes = ebox_Theme_Register::get_themes();
	if ( ! is_array( $themes ) ) {
		$themes = array();
	}

	$themes_list = array();
	foreach ( $themes as $theme ) {
		$ldlms_settings['templates']['list'][ $theme['theme_key'] ] = $theme['theme_name'];
	}

	/**
	 * Include the LD post types with key.
	 *
	 * @since 4.0.0
	 */
	$ldlms_settings['post_types'] = LDLMS_Post_Types::get_all_post_types_set();

	$ldlms_settings['plugins'] = array();

	$ldlms_settings['plugins']['ebox-core']            = array();
	$ldlms_settings['plugins']['ebox-core']['version'] = ebox_VERSION;

	$ldlms_settings['plugins']['ebox-course-grid']                = array();
	$ldlms_settings['plugins']['ebox-course-grid']['enabled']     = ebox_enqueue_course_grid_scripts();
	$ldlms_settings['plugins']['ebox-course-grid']['col_default'] = 3;
	$ldlms_settings['plugins']['ebox-course-grid']['col_max']     = 12;

	if ( true === $ldlms_settings['plugins']['ebox-course-grid']['enabled'] ) {
		if ( defined( 'ebox_COURSE_GRID_COLUMNS' ) ) {
			$col_default = intval( ebox_COURSE_GRID_COLUMNS );
			if ( ( ! empty( $col_default ) ) && ( $col_default > 0 ) ) {
				$ldlms_settings['plugins']['ebox-course-grid']['col_default'] = $col_default;
			}
		}

		if ( defined( 'ebox_COURSE_GRID_MAX_COLUMNS' ) ) {
			$col_max = intval( ebox_COURSE_GRID_MAX_COLUMNS );
			if ( ( ! empty( $col_max ) ) && ( $col_max > 0 ) ) {
				$ldlms_settings['plugins']['ebox-course-grid']['col_max'] = $col_max;
			}
		}
	}

	$ldlms_settings['meta']                   = array();
	$ldlms_settings['meta']['posts_per_page'] = get_option( 'posts_per_page' ); // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page

	$ldlms_settings['meta']['post']              = array();
	$ldlms_settings['meta']['post']['post_id']   = 0;
	$ldlms_settings['meta']['post']['post_type'] = '';
	$ldlms_settings['meta']['post']['editing']   = '';
	$ldlms_settings['meta']['post']['course_id'] = 0;

	if ( is_admin() ) {
		$current_screen = get_current_screen();
		if ( 'post' === $current_screen->base ) {

			global $post, $post_type, $editing;
			$ldlms_settings['meta']['post'] = array();

			$ldlms_settings['meta']['post']['post_id']   = $post->ID;
			$ldlms_settings['meta']['post']['post_type'] = $post_type;
			$ldlms_settings['meta']['post']['editing']   = $editing;

			$ldlms_settings['meta']['post']['course_id'] = 0;

			if ( ! empty( $post_type ) ) {
				$course_post_types = LDLMS_Post_Types::get_post_types( 'course' );

				if ( 'ebox-courses' === $post_type ) {
					$ldlms_settings['meta']['post']['course_id'] = $post->ID;
				} elseif ( in_array( $post_type, $course_post_types, true ) ) {
					$ldlms_settings['meta']['post']['course_id'] = ebox_get_course_id();
				}
			}
		}
	}

	// Load the MO file translations into wp.i18n script hook.
	ebox_load_inline_script_locale_data();

	wp_localize_script( 'ldlms-blocks-js', 'ldlms_settings', $ldlms_settings );

	// Enqueue optional editor only styles.
	wp_enqueue_style(
		'ldlms-blocks-editor-css',
		plugins_url( $ebox_editor_style_path, __FILE__ ),
		array(),
		ebox_SCRIPT_VERSION_TOKEN
	);
	wp_style_add_data( 'ldlms-blocks-editor-css', 'rtl', 'replace' );

	// Call our function to load CSS/JS used by the shortcodes.
	ebox_load_resources();

	$filepath = ebox_LMS::get_template( 'ebox_pager.css', null, null, true );
	if ( ! empty( $filepath ) ) {
		wp_enqueue_style( 'ebox_pager_css', ebox_template_url_from_path( $filepath ), array(), ebox_SCRIPT_VERSION_TOKEN );
		wp_style_add_data( 'ebox_pager_css', 'rtl', 'replace' );
		$ebox_assets_loaded['styles']['ebox_pager_css'] = __FUNCTION__;
	}

	$filepath = ebox_LMS::get_template( 'ebox_pager.js', null, null, true );
	if ( ! empty( $filepath ) ) {
		wp_enqueue_script( 'ebox_pager_js', ebox_template_url_from_path( $filepath ), array( 'jquery' ), ebox_SCRIPT_VERSION_TOKEN, true );
		$ebox_assets_loaded['scripts']['ebox_pager_js'] = __FUNCTION__;
	}
}
// Hook scripts function into block editor hook.
add_action( 'enqueue_block_editor_assets', 'ebox_editor_scripts' );

/**
 * Enqueues the required styles and scripts for the course grid.
 *
 * @since 2.5.9
 *
 * @return boolean Returns true if the assets are enqueued otherwise false.
 */
function ebox_enqueue_course_grid_scripts() {

	// Check if Course Grid add-on is installed.
	if ( ( defined( 'ebox_COURSE_GRID_FILE' ) ) && ( file_exists( ebox_COURSE_GRID_FILE ) ) ) {
		// Newer versions of Course Grid have a function to load resources.
		if ( function_exists( 'ebox_course_grid_load_resources' ) ) {
			ebox_course_grid_load_resources();
			return true;
		}
	}

	return false;
}


/**
 * Registers a custom block category.
 *
 * Fires on `block_categories` hook.
 *
 * @since 2.6.0
 *
 * @param array         $block_categories Optional. An array of current block categories. Default empty array.
 * @param WP_Post|false $post             Optional. The `WP_Post` instance of post being edited. Default false.
 *
 * @return array An array of block categories.
 */
function ebox_block_categories( $block_categories = array(), $post = false ) {
	if ( is_array( $block_categories ) ) {
		if ( ! in_array( 'ebox-blocks', wp_list_pluck( $block_categories, 'slug' ), true ) ) {
			if ( ( $post ) && ( is_a( $post, 'WP_Post' ) ) && ( in_array( $post->post_type, LDLMS_Post_Types::get_post_types(), true ) ) ) {
				$block_categories = array_merge(
					array(
						array(
							'slug'  => 'ebox-blocks',
							'title' => esc_html__( 'Ebox LMS Blocks', 'ebox' ),
							'icon'  => false,
						),
					),
					$block_categories
				);
			} else {
				$block_categories[] = array(
					'slug'  => 'ebox-blocks',
					'title' => esc_html__( 'Ebox LMS Blocks', 'ebox' ),
					'icon'  => false,
				);
			}
		}
	}

	// Always return $default_block_categories.
	return $block_categories;
}

/**
 * Registers a custom block category.
 *
 * Fires on `block_categories_all` hook.
 *
 * @since 3.4.2
 *
 * @param array                   $block_categories Optional. An array of current block categories. Default empty array.
 * @param WP_Block_Editor_Context $block_editor_context The current block editor context.
 *
 * @return array An array of block categories.
 */
function ebox_block_categories_all( $block_categories, $block_editor_context ) {
	if ( ( is_object( $block_editor_context ) ) && ( property_exists( $block_editor_context, 'post' ) ) && ( is_a( $block_editor_context->post, 'WP_Post' ) ) ) {
		$block_categories = ebox_block_categories( $block_categories, $block_editor_context->post );
	} else {
		$block_categories = ebox_block_categories( $block_categories );
	}

	return $block_categories;
}

/**
 * Register Block Pattern Categories.
 */
function ebox_block_pattern_categories() {
	register_block_pattern_category(
		'ebox',
		array(
			'label' => __( 'ebox', 'ebox' ),
		)
	);
}

/**
 * Register Block Patterns.
 */
function ebox_register_block_patterns() {
	register_block_pattern(
		'ebox/course-content',
		array(
			'title'       => __( 'Course Content Blocks', 'ebox' ),
			'categories'  => array( 'ebox' ),
			'description' => esc_html_x( 'Display the course or step content blocks collection.', 'Block pattern description', 'ebox' ),
			'content'     => "<!-- wp:ebox/ld-infobar /-->\n<!-- wp:ebox/ld-course-content /-->",
			'blockTypes'  => array( 'ld-course-content', 'ld-course-progress' ),
		)
	);
}

add_action(
	'ebox_init',
	function() {
		global $wp_version;

		if ( version_compare( $wp_version, '5.7.99', '>' ) ) {
			add_filter( 'block_categories_all', 'ebox_block_categories_all', 30, 2 );
		} else {
			add_filter( 'block_categories', 'ebox_block_categories', 30, 2 );
		}

		ebox_block_pattern_categories();
		ebox_register_block_patterns();
	}
);

/**
 * Get the Legacy template not supported message.
 *
 * This message is shows on blocks and shortcodes which don't support the "Legacy"
 * templates.
 *
 * @since 4.0.0
 */
function ebox_get_legacy_not_supported_message() {
	$message = '';
	if ( 'legacy' === ebox_Theme_Register::get_active_theme_key() ) {
		$message = sprintf(
			// translators: placeholder: current template name.
			esc_html_x(
				'The current ebox template "%s" may not support this block. Please select a different template.',
				'placeholder: current template name',
				'ebox'
			),
			ebox_Theme_Register::get_active_theme_name()
		);
	}

	return $message;
}
