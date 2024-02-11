<?php
/**
 * ebox ebox Legacy Theme Register.
 *
 * @since 3.0.0
 *
 * @package ebox\Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'ebox_Theme_Register' ) && ! class_exists( 'ebox_Theme_Register_Legacy' ) ) {
	/**
	 * Class to create the settings section.
	 */
	class ebox_Theme_Register_Legacy extends ebox_Theme_Register {
		/**
		 * Protected constructor for class
		 */
		protected function __construct() {
			parent::__construct();

			$this->theme_key          = 'legacy';
			$this->theme_name         = esc_html__( 'Legacy', 'ebox' );
			$this->theme_base_dir     = trailingslashit( ebox_LMS_PLUGIN_DIR ) . 'themes/' . $this->theme_key;
			$this->theme_base_url     = trailingslashit( ebox_LMS_PLUGIN_URL ) . 'themes/' . $this->theme_key;
			$this->theme_template_dir = $this->theme_base_dir . '/templates';
			$this->theme_template_url = $this->theme_base_url . '/templates';
		}
	}
}

add_action(
	'ebox_themes_init',
	function() {
		ebox_Theme_Register_Legacy::add_theme_instance( 'legacy' );
	}
);
