<?php
/**
 * ebox Shortcode Section for Video Progress placeholder [ld_video].
 *
 * @since 2.4.5
 * @package ebox\Settings\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Shortcodes_Section' ) ) && ( ! class_exists( 'ebox_Shortcodes_Section_ld_video' ) ) ) {
	/**
	 * Class ebox Shortcode Section for Video Progress placeholder [ld_video].
	 *
	 * @since 2.4.5
	 */
	class ebox_Shortcodes_Section_ld_video extends ebox_Shortcodes_Section /* phpcs:ignore PEAR.NamingConventions.ValidClassName.Invalid */ {

		/**
		 * Public constructor for class.
		 *
		 * @since 2.4.5
		 *
		 * @param array $fields_args Field Args.
		 */
		public function __construct( $fields_args = array() ) {
			$this->fields_args = $fields_args;

			$this->shortcodes_section_key  = 'ld_video';
			$this->shortcodes_section_type = 1;

			// translators: placeholders: modules, Topics.
			$this->shortcodes_section_description = sprintf( esc_html_x( 'This shortcode is used on %1$s and %2$s where Video Progression is enabled. The video player will be added above the content. This shortcode allows positioning the player elsewhere within the content. This shortcode does not take any parameters.', 'placeholders: modules, Topics', 'ebox' ), ebox_Custom_Label::get_label( 'modules' ), ebox_Custom_Label::get_label( 'topics' ) );

			if ( ebox_get_post_type_slug( 'lesson' ) == $this->fields_args['post_type'] ) {
				// translators: placeholder: lesson.
				$this->shortcodes_section_title = sprintf( esc_html_x( '%s Video', 'placeholder: lesson', 'ebox' ), ebox_Custom_Label::get_label( 'lesson' ) );
			} elseif ( ebox_get_post_type_slug( 'topic' ) == $this->fields_args['post_type'] ) {
				// translators: placeholder: topic.
				$this->shortcodes_section_title = sprintf( esc_html_x( '%s Video', 'placeholder: topic', 'ebox' ), ebox_Custom_Label::get_label( 'topic' ) );
			}

			parent::__construct();
		}

		/**
		 * Initialize the shortcode fields.
		 *
		 * @since 2.4.5
		 */
		public function init_shortcodes_section_fields() {
			$this->shortcodes_option_fields = array();

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
			$this->shortcodes_option_fields = apply_filters( 'ebox_settings_fields', $this->shortcodes_option_fields, $this->shortcodes_section_key );

			parent::init_shortcodes_section_fields();
		}
	}
}
