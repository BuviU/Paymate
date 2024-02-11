<?php
/**
 * ebox Shortcode Section for Login [ebox_login].
 *
 * @since 3.0.7
 * @package ebox\Settings\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Shortcodes_Section' ) ) && ( ! class_exists( 'ebox_Shortcodes_Section_ebox_login' ) ) ) {
	/**
	 * Class ebox Shortcode Section for Login [ebox_login].
	 *
	 * @since 3.0.7
	 */
	class ebox_Shortcodes_Section_ebox_login extends ebox_Shortcodes_Section /* phpcs:ignore PEAR.NamingConventions.ValidClassName.Invalid */ {

		/**
		 * Public constructor for class.
		 *
		 * @since 3.0.7
		 *
		 * @param array $fields_args Field Args.
		 */
		public function __construct( $fields_args = array() ) {
			$this->fields_args = $fields_args;

			$this->shortcodes_section_key         = 'ebox_login';
			$this->shortcodes_section_title       = esc_html__( 'ebox Login', 'ebox' );
			$this->shortcodes_section_type        = 1;
			$this->shortcodes_section_description = esc_html__( 'This shortcode adds the login button on any page', 'ebox' );

			parent::__construct();
		}

		/**
		 * Initialize the shortcode fields.
		 *
		 * @since 3.0.7
		 */
		public function init_shortcodes_section_fields() {
			$this->shortcodes_option_fields = array(
				'login_description'  => array(
					'id'         => $this->shortcodes_section_key . '_login_description',
					'name'       => 'login_description',
					'type'       => 'html',
					'label'      => '',
					'label_none' => true,
					'input_full' => true,
					'value'      => wpautop( esc_html__( 'Controls the Login functionality.', 'ebox' ) ),
				),
				'login_url'          => array(
					'id'        => $this->shortcodes_section_key . '_login_url',
					'name'      => 'login_url',
					'type'      => 'text',
					'label'     => esc_html__( 'Login URL', 'ebox' ),
					'value'     => '',
					'help_text' => esc_html__( 'Override default login URL', 'ebox' ),
				),
				'login_label'        => array(
					'id'        => $this->shortcodes_section_key . '_login_label',
					'name'      => 'login_label',
					'type'      => 'text',
					'label'     => esc_html__( 'Login Label', 'ebox' ),
					'value'     => '',
					'help_text' => esc_html__( 'Override default label "Login"', 'ebox' ),
				),
				'login_placement'    => array(
					'id'        => $this->shortcodes_section_key . '_login_placement',
					'name'      => 'login_placement',
					'type'      => 'select',
					'label'     => esc_html__( 'Login Icon Placement', 'ebox' ),
					'help_text' => esc_html__( 'Login Icon Placement', 'ebox' ),
					'value'     => '',
					'options'   => array(
						''      => esc_html__( 'Left - To left of label', 'ebox' ),
						'right' => esc_html__( 'Right - To right of label', 'ebox' ),
						'none'  => esc_html__( 'None - No icon', 'ebox' ),
					),
				),
				'login_button'       => array(
					'id'        => $this->shortcodes_section_key . '_login_button',
					'name'      => 'login_button',
					'type'      => 'select',
					'label'     => esc_html__( 'Login Displayed as', 'ebox' ),
					'help_text' => esc_html__( 'Display as Button or link', 'ebox' ),
					'value'     => 'button',
					'options'   => array(
						''     => esc_html__( 'Button', 'ebox' ),
						'link' => esc_html__( 'Link', 'ebox' ),
					),
				),
				'logout_description' => array(
					'id'         => $this->shortcodes_section_key . '_logout_description',
					'name'       => 'logout_description',
					'type'       => 'html',
					'label'      => '',
					'label_none' => true,
					'input_full' => true,
					'value'      => wpautop( esc_html__( 'Controls the Logout functionality.', 'ebox' ) ),
				),
				'logout_url'         => array(
					'id'        => $this->shortcodes_section_key . '_logout_url',
					'name'      => 'logout_url',
					'type'      => 'text',
					'label'     => esc_html__( 'Logout URL Redirect', 'ebox' ),
					'value'     => '',
					'help_text' => esc_html__( 'Override default logout URL.', 'ebox' ),
				),
				'logout_label'       => array(
					'id'        => $this->shortcodes_section_key . '_logout_label',
					'name'      => 'logout_label',
					'type'      => 'text',
					'label'     => esc_html__( 'Logout Label', 'ebox' ),
					'value'     => '',
					'help_text' => esc_html__( 'Override default label "Logout"', 'ebox' ),
				),
				'logout_placement'   => array(
					'id'        => $this->shortcodes_section_key . '_logout_placement',
					'name'      => 'logout_placement',
					'type'      => 'select',
					'label'     => esc_html__( 'Logout Icon Placement', 'ebox' ),
					'help_text' => esc_html__( 'Logout Icon Placement', 'ebox' ),
					'value'     => '',
					'options'   => array(
						'left' => esc_html__( 'Left - To left of label', 'ebox' ),
						''     => esc_html__( 'Right - To right of label', 'ebox' ),
						'none' => esc_html__( 'None - No icon', 'ebox' ),
					),
				),
				'logout_button'      => array(
					'id'        => $this->shortcodes_section_key . '_logout_button',
					'name'      => 'logout_button',
					'type'      => 'select',
					'label'     => esc_html__( 'Logout Displayed as Button', 'ebox' ),
					'help_text' => esc_html__( 'Display as Button or link', 'ebox' ),
					'value'     => 'button',
					'options'   => array(
						''     => esc_html__( 'Button', 'ebox' ),
						'link' => esc_html__( 'Link', 'ebox' ),
					),
				),
			);

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
			$this->shortcodes_option_fields = apply_filters( 'ebox_settings_fields', $this->shortcodes_option_fields, $this->shortcodes_section_key );

			parent::init_shortcodes_section_fields();
		}
	}
}
