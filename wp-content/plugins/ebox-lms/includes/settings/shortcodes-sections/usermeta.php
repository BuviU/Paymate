<?php
/**
 * ebox Shortcode Section for Usermeta [usermeta].
 *
 * @since 2.4.0
 * @package ebox\Settings\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Shortcodes_Section' ) ) && ( ! class_exists( 'ebox_Shortcodes_Section_usermeta' ) ) ) {
	/**
	 * Class ebox Shortcode Section for Usermeta [usermeta].
	 *
	 * @since 2.4.0
	 */
	class ebox_Shortcodes_Section_usermeta extends ebox_Shortcodes_Section /* phpcs:ignore PEAR.NamingConventions.ValidClassName.Invalid */ {

		/**
		 * Public constructor for class.
		 *
		 * @since 2.4.0
		 *
		 * @param array $fields_args Field Args.
		 */
		public function __construct( $fields_args = array() ) {
			$this->fields_args = $fields_args;

			$this->shortcodes_section_key         = 'usermeta';
			$this->shortcodes_section_title       = esc_html__( 'User meta', 'ebox' );
			$this->shortcodes_section_type        = 1;
			$this->shortcodes_section_description = wp_kses_post( __( 'This shortcode takes a parameter named field, which is the name of the user meta data field to be displayed. See <a href="http://codex.wordpress.org/Function_Reference/get_userdata#Notes">the full list of available fields here. Note for security reasons some fields are not allowed.</a>', 'ebox' ) );

			parent::__construct();
		}

		/**
		 * Initialize the shortcode fields.
		 *
		 * @since 2.4.0
		 */
		public function init_shortcodes_section_fields() {
			$this->shortcodes_option_fields = array(
				'field'   => array(
					'id'        => $this->shortcodes_section_key . '_field',
					'name'      => 'field',
					'type'      => 'select',
					'label'     => esc_html__( 'Field', 'ebox' ),
					'help_text' => esc_html__( 'This parameter determines the information to be shown by the shortcode.', 'ebox' ),
					'value'     => 'ID',
					'options'   => ebox_get_usermeta_shortcode_available_fields(),
				),
				'user_id' => array(
					'id'        => $this->shortcodes_section_key . '_user_id',
					'name'      => 'user_id',
					'type'      => 'number',
					'label'     => esc_html__( 'User ID', 'ebox' ),
					'help_text' => esc_html__( 'Enter specific User ID. Leave blank for current User.', 'ebox' ),
					'value'     => '',
					'class'     => 'small-text',
				),
			);

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
			$this->shortcodes_option_fields = apply_filters( 'ebox_settings_fields', $this->shortcodes_option_fields, $this->shortcodes_section_key );

			parent::init_shortcodes_section_fields();
		}
	}
}
