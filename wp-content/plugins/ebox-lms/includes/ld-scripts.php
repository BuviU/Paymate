<?php
/**
 * Scripts & Styles
 *
 * @since 2.1.0
 *
 * @package ebox\Scripts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueues styles for front-end.
 *
 * Fires on `wp_enqueue_scripts` hook.
 *
 * @global array $ebox_assets_loaded An array of loaded styles and scripts.
 *
 * @since 2.1.0
 */
function ebox_load_resources() {
	global $ebox_assets_loaded;

	wp_enqueue_style(
		'ebox_style',
		ebox_LMS_PLUGIN_URL . 'assets/css/style' . ebox_min_asset() . '.css',
		array(),
		ebox_SCRIPT_VERSION_TOKEN
	);
	wp_style_add_data( 'ebox_style', 'rtl', 'replace' );
	$ebox_assets_loaded['styles']['ebox_style'] = __FUNCTION__;

	wp_enqueue_style(
		'ebox_front_css',
		ebox_LMS_PLUGIN_URL . 'assets/css/front' . ebox_min_asset() . '.css',
		array(),
		ebox_SCRIPT_VERSION_TOKEN
	);
	wp_style_add_data( 'ebox_front_css', 'rtl', 'replace' );
	$ebox_assets_loaded['styles']['ebox_front_css'] = __FUNCTION__;

	if ( ! is_admin() ) {
		wp_enqueue_style(
			'jquery-dropdown-css',
			ebox_LMS_PLUGIN_URL . 'assets/css/jquery.dropdown.min.css',
			array(),
			ebox_SCRIPT_VERSION_TOKEN
		);
		wp_style_add_data( 'jquery-dropdown-css', 'rtl', 'replace' );
		$ebox_assets_loaded['styles']['jquery-dropdown-css'] = __FUNCTION__;
	}

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

	$filepath = ebox_LMS::get_template( 'ebox_template_style.css', null, null, true );
	if ( ! empty( $filepath ) ) {
		wp_enqueue_style( 'ebox_template_style_css', ebox_template_url_from_path( $filepath ), array(), ebox_SCRIPT_VERSION_TOKEN );
		wp_style_add_data( 'ebox_template_style_css', 'rtl', 'replace' );
		$ebox_assets_loaded['styles']['ebox_template_style_css'] = __FUNCTION__;
	}

	$filepath = ebox_LMS_PLUGIN_URL . 'assets/js/ebox-payments' . ebox_min_asset() . '.js';
	if ( ! empty( $filepath ) ) {
		wp_register_script( 'ebox-payments', $filepath, array( 'jquery' ), ebox_SCRIPT_VERSION_TOKEN, true );
		$ebox_assets_loaded['scripts']['ebox-payments'] = __FUNCTION__;
		wp_localize_script(
			'ebox-payments',
			'ebox_payments',
			array(
				'ajaxurl'  => admin_url( 'admin-ajax.php' ),
				'messages' => array(
					'successful_transaction' => is_user_logged_in()
						? esc_html__( 'Your transaction was successful.', 'ebox' )
						: esc_html__( 'Your transaction was successful. Please log in to access your content.', 'ebox' ),
				),
			)
		);
	}

	$filepath = ebox_LMS_PLUGIN_URL . 'assets/js/ebox-password-strength-meter.js';
	if ( ! empty( $filepath ) ) {
		wp_register_script( 'ebox-password-strength-meter', $filepath, array( 'jquery', 'password-strength-meter' ), ebox_SCRIPT_VERSION_TOKEN, true );
		$ebox_assets_loaded['scripts']['ebox-password-strength-meter'] = __FUNCTION__;
	}

	/** This filter is documented in includes/ld-misc-functions.php */
	if ( true === apply_filters( 'ebox_responsive_video', true, get_post_type(), get_the_ID() ) ) {
		$filepath = ebox_LMS::get_template( 'ebox_lesson_video.css', null, null, true );
		if ( ! empty( $filepath ) ) {
			wp_enqueue_style( 'ebox_lesson_video', ebox_template_url_from_path( $filepath ), array(), ebox_SCRIPT_VERSION_TOKEN );
			$ebox_assets_loaded['styles']['ebox_lesson_video'] = __FUNCTION__;
		}
	}

	if ( ! isset( $ebox_assets_loaded['scripts']['ebox_template_script_js'] ) ) {
		// First check if the theme has the file ebox/ebox_template_script.js or ebox_template_script.js file.
		$filepath = ebox_LMS::get_template( 'ebox_template_script.js', null, null, true );
		if ( ! empty( $filepath ) ) {
			wp_enqueue_script( 'ebox_template_script_js', ebox_template_url_from_path( $filepath ), array( 'jquery' ), ebox_SCRIPT_VERSION_TOKEN, true );
			$ebox_assets_loaded['scripts']['ebox_template_script_js'] = __FUNCTION__;

			$data            = array();
			$data['ajaxurl'] = admin_url( 'admin-ajax.php' );
			$data            = array( 'json' => wp_json_encode( $data ) );
			wp_localize_script( 'ebox_template_script_js', 'ebox_data', $data );
		}
	}

	// This will be dequeued via the get_footer hook if the button was not used.
	if ( ! is_admin() ) {
		wp_enqueue_script( 'jquery-dropdown-js', ebox_LMS_PLUGIN_URL . 'assets/js/jquery.dropdown.min.js', array( 'jquery' ), ebox_SCRIPT_VERSION_TOKEN, true );
		$ebox_assets_loaded['scripts']['jquery-dropdown-js'] = __FUNCTION__;
	}
}

/**
 * Filters ebox resources load priority.
 *
 * @param string $priority Resources load priority.
 */
add_action( 'wp_enqueue_scripts', 'ebox_load_resources', apply_filters( 'ebox_load_resources_priority', '10' ) );

/**
 * Dequeues scripts.
 *
 * @global array $ebox_assets_loaded
 * @global array $ebox_shortcode_used
 * @global array $ebox_post_types
 */
function ebox_unload_resources() {
	global $ebox_shortcode_used;
	global $ebox_assets_loaded;

	// If we are showing a known LD post type then leave it all.
	global $ebox_post_types;
	if ( ( is_singular( $ebox_post_types ) ) || ( false !== $ebox_shortcode_used ) ) {
		return;
	}

	if ( ( isset( $ebox_assets_loaded['scripts'] ) ) && ( ! empty( $ebox_assets_loaded['scripts'] ) ) ) {
		foreach ( $ebox_assets_loaded['scripts'] as $script_tag => $function_loaded ) {
			// We *should* check these scripts to ensure we dequeue only ones set to load in the footer. Oh well.
			wp_dequeue_script( $script_tag );
		}
	}
}
add_action( 'wp_print_footer_scripts', 'ebox_unload_resources', 1 );
