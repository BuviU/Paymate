<?php
/**
 * ebox Shortcode Section for User Teams [user_teams].
 *
 * @since 2.4.0
 * @package ebox\Settings\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Shortcodes_Section' ) ) && ( ! class_exists( 'ebox_Shortcodes_Section_user_teams' ) ) ) {
	/**
	 * Class ebox Shortcode Section for User Teams [user_teams].
	 *
	 * @since 2.4.0
	 */
	class ebox_Shortcodes_Section_user_teams extends ebox_Shortcodes_Section /* phpcs:ignore PEAR.NamingConventions.ValidClassName.Invalid */ {

		/**
		 * Public constructor for class.
		 *
		 * @since 2.4.0
		 *
		 * @param array $fields_args Field Args.
		 */
		public function __construct( $fields_args = array() ) {
			$this->fields_args = $fields_args;
			$teams_public     = ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Teams_CPT', 'public' ) === '' ) ? ebox_teams_get_not_public_message() : '';

			$this->shortcodes_section_key   = 'user_teams';
			$this->shortcodes_section_title = sprintf(
				// translators: placeholder: Teams.
				esc_html_x( 'User %s', 'placeholder: Teams', 'ebox' ),
				ebox_get_custom_label( 'teams' )
			);
			$this->shortcodes_section_type        = 1;
			$this->shortcodes_section_description = sprintf(
				// translators: placeholder : team.
				esc_html_x( 'This shortcode displays the list of %1$s users are assigned to as users or leaders. %2$s', 'placeholder: Team', 'ebox' ),
				ebox_get_custom_label( 'team' ),
				$teams_public
			);

			parent::__construct();
		}

		/**
		 * Initialize the shortcode fields.
		 *
		 * @since 2.4.0
		 */
		public function init_shortcodes_section_fields() {
			$this->shortcodes_option_fields = array(
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
