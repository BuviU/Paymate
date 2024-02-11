<?php
/**
 * ebox Shortcode Section for Team [ld_team].
 *
 * @since 2.4.0
 * @package ebox\Settings\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Shortcodes_Section' ) ) && ( ! class_exists( 'ebox_Shortcodes_Section_ld_team' ) ) ) {
	/**
	 * Class ebox Shortcode Section for Team [ld_team].
	 */
	class ebox_Shortcodes_Section_ld_team extends ebox_Shortcodes_Section /* phpcs:ignore PEAR.NamingConventions.ValidClassName.Invalid */ {

		/**
		 * Public constructor for class.
		 *
		 * @param array $fields_args Field Args.
		 */
		public function __construct( $fields_args = array() ) {
			$this->fields_args = $fields_args;

			$this->shortcodes_section_key         = 'ld_team';
			$this->shortcodes_section_title       = ebox_get_custom_label( 'team' );
			$this->shortcodes_section_type        = 2;
			$this->shortcodes_section_description = sprintf(
				// translators: team.
				esc_html_x( 'This shortcode shows the content if the user is enrolled in a specific %s.', 'placeholder: team', 'ebox' ),
				ebox_get_custom_label_lower( 'team' )
			);

			parent::__construct();
		}

		/**
		 * Initialize the shortcode fields.
		 */
		public function init_shortcodes_section_fields() {
			$this->shortcodes_option_fields = array(
				'message'  => array(
					'id'        => $this->shortcodes_section_key . '_message',
					'name'      => 'message',
					'type'      => 'textarea',
					'label'     => esc_html__( 'Message shown to user', 'ebox' ),
					'help_text' => esc_html__( 'Message shown to user', 'ebox' ),
					'value'     => '',
					'required'  => 'required',
				),
				'team_id' => array(
					'id'        => $this->shortcodes_section_key . '_team_id',
					'name'      => 'team_id',
					'type'      => 'number',
					// translators: team.
					'label'     => sprintf( esc_html_x( '%s ID', 'placeholder: team', 'ebox' ), ebox_get_custom_label( 'team' ) ),
					'help_text' => sprintf(
						// translators: team, team.
						esc_html_x( 'Enter single %1$s ID. Leave blank for any %2$s.', 'placeholder: team, team', 'ebox' ),
						ebox_get_custom_label_lower( 'team' ),
						ebox_get_custom_label_lower( 'team' )
					),
					'value'     => '',
					'class'     => 'small-text',
				),
				'user_id'  => array(
					'id'        => $this->shortcodes_section_key . '_user_id',
					'name'      => 'user_id',
					'type'      => 'number',
					'label'     => esc_html__( 'User ID', 'ebox' ),
					'help_text' => esc_html__( 'Enter specific User ID. Leave blank for current User.', 'ebox' ),
					'value'     => '',
					'class'     => 'small-text',
				),
				'autop'    => array(
					'id'        => $this->shortcodes_section_key . 'autop',
					'name'      => 'autop',
					'type'      => 'select',
					'label'     => esc_html__( 'Auto Paragraph', 'ebox' ),
					'help_text' => esc_html__( 'Format shortcode content into proper paragraphs.', 'ebox' ),
					'value'     => 'true',
					'options'   => array(
						''      => esc_html__( 'Yes (default)', 'ebox' ),
						'false' => esc_html__( 'No', 'ebox' ),
					),
				),
			);

			if ( ( ! isset( $this->fields_args['post_type'] ) ) || ( 'teams' != $this->fields_args['post_type'] ) ) {
				$this->shortcodes_option_fields['team_id']['required']  = 'required';
				$this->shortcodes_option_fields['team_id']['help_text'] = sprintf(
					// translators: placeholder: team.
					esc_html_x( 'Enter single %s ID.', 'placeholder: team', 'ebox' ),
					ebox_get_custom_label_lower( 'team' )
				);
			}

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
			$this->shortcodes_option_fields = apply_filters( 'ebox_settings_fields', $this->shortcodes_option_fields, $this->shortcodes_section_key );

			parent::init_shortcodes_section_fields();
		}
	}
}
