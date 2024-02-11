<?php
/**
 * ebox Shortcode Section for Teaminfo [teaminfo].
 *
 * @since 2.4.0
 * @package ebox\Settings\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Shortcodes_Section' ) ) && ( ! class_exists( 'ebox_Shortcodes_Section_teaminfo' ) ) ) {
	/**
	 * Class ebox Shortcode Section for Teaminfo [teaminfo].
	 *
	 * @since 2.4.0
	 */
	class ebox_Shortcodes_Section_teaminfo extends ebox_Shortcodes_Section  /* phpcs:ignore PEAR.NamingConventions.ValidClassName.Invalid */ {

		/**
		 * Public constructor for class.
		 *
		 * @since 2.4.0
		 *
		 * @param array $fields_args Field Args.
		 */
		public function __construct( $fields_args = array() ) {
			$this->fields_args = $fields_args;

			$this->shortcodes_section_key = 'teaminfo';
			// translators: placeholder: Team.
			$this->shortcodes_section_title = sprintf( esc_html_x( '%s Info', 'placeholder: Team', 'ebox' ), ebox_Custom_Label::get_label( 'team' ) );
			$this->shortcodes_section_type  = 1;

			// translators: placeholder: team.
			$this->shortcodes_section_description = sprintf( wp_kses_post( _x( 'This shortcode displays %1$s related information.', 'placeholder: team', 'ebox' ) ), ebox_get_custom_label_lower( 'team' ) );

			parent::__construct();
		}

		/**
		 * Initialize the shortcode fields.
		 *
		 * @since 2.4.0
		 */
		public function init_shortcodes_section_fields() {
			$this->shortcodes_option_fields = array(
				'show'   => array(
					'id'        => $this->shortcodes_section_key . '_show',
					'name'      => 'show',
					'type'      => 'select',
					'label'     => esc_html__( 'Show', 'ebox' ),
					'help_text' => esc_html__( 'This parameter determines the information to be shown by the shortcode.', 'ebox' ),
					'value'     => 'ID',
					'options'   => array(
						// translators: placeholder: Team.
						'team_title'         => sprintf( esc_html_x( '%s Title', 'placeholder: Team', 'ebox' ), ebox_Custom_Label::get_label( 'team' ) ),
						// translators: placeholder: Team.
						'team_url'           => sprintf( esc_html_x( '%s URL', 'placeholder: Team', 'ebox' ), ebox_Custom_Label::get_label( 'team' ) ),
						// translators: placeholder: Team.
						'team_price_type'    => sprintf( esc_html_x( '%s Price Type', 'placeholder: Team', 'ebox' ), ebox_Custom_Label::get_label( 'team' ) ),
						// translators: placeholder: Team.
						'team_price'         => sprintf( esc_html_x( '%s Price', 'placeholder: Team', 'ebox' ), ebox_Custom_Label::get_label( 'team' ) ),
						// translators: placeholder: Team.
						'team_users_count'   => sprintf( esc_html_x( '%s Enrolled Users Count', 'placeholder: Team', 'ebox' ), ebox_Custom_Label::get_label( 'team' ) ),
						// translators: placeholder: Team, Courses.
						'team_courses_count' => sprintf( esc_html_x( '%1$s %2$s Count', 'placeholder: Team, Courses', 'ebox' ), ebox_Custom_Label::get_label( 'team' ), ebox_Custom_Label::get_label( 'courses' ) ),

						// The following require User ID.
						// translators: placeholder: Team.
						'user_team_status'   => sprintf( esc_html_x( 'User %s Status', 'placeholder: Team', 'ebox' ), ebox_Custom_Label::get_label( 'team' ) ),

						// translators: placeholder: Team.
						'enrolled_on'         => sprintf( esc_html_x( 'Enrolled On (date)', 'placeholder: Team', 'ebox' ), ebox_Custom_Label::get_label( 'team' ) ),
						// translators: placeholder: Team.
						'completed_on'        => sprintf( esc_html_x( '%s Completed On (date)', 'placeholder: Team', 'ebox' ), ebox_Custom_Label::get_label( 'team' ) ),
						// translators: placeholder: Team.
						'percent_completed'   => sprintf( esc_html_x( '%s Completed Percentage', 'placeholder: Team', 'ebox' ), ebox_Custom_Label::get_label( 'team' ) ),
					),
				),
				'format' => array(
					'id'          => $this->shortcodes_section_key . '_format',
					'name'        => 'format',
					'type'        => 'text',
					'label'       => esc_html__( 'Format', 'ebox' ),
					'help_text'   => wp_kses_post( __( 'This can be used to change the date format. Default: "F j, Y, g:i a" shows as <i>March 10, 2001, 5:16 pm</i>. See <a target="_blank" href="http://php.net/manual/en/function.date.php">the full list of available date formatting strings here.</a>', 'ebox' ) ),
					'value'       => '',
					'placeholder' => 'F j, Y, g:i a',
				),
			);

			$post_types   = array();
			$post_types[] = ebox_get_post_type_slug( 'team' );
			$post_types[] = ebox_get_post_type_slug( 'certificate' );
			if ( ( ! isset( $this->fields_args['typenow'] ) ) || ( ! in_array( $this->fields_args['typenow'], $post_types, true ) ) ) {
				$this->shortcodes_option_fields['team_id'] = array(
					'id'        => $this->shortcodes_section_key . '_team_id',
					'name'      => 'team_id',
					'type'      => 'number',
					'label'     => sprintf(
						// translators: placeholder: Team.
						esc_html_x( '%s ID', 'placeholder: Team', 'ebox' ),
						ebox_Custom_Label::get_label( 'team' )
					),
					'help_text' => sprintf(
						// translators: placeholders: Team.
						esc_html_x( 'Enter single %s ID.', 'placeholders: Team', 'ebox' ),
						ebox_Custom_Label::get_label( 'team' )
					),
					'value'     => '',
					'class'     => 'small-text',
					'required'  => 'required',
				);

				$this->shortcodes_option_fields['user_id'] = array(
					'id'        => $this->shortcodes_section_key . '_user_id',
					'name'      => 'user_id',
					'type'      => 'number',
					'label'     => esc_html__( 'User ID', 'ebox' ),
					'help_text' => esc_html__( 'Enter specific User ID. Leave blank for current User.', 'ebox' ),
					'value'     => '',
					'class'     => 'small-text',
				);
			}
			$this->shortcodes_option_fields['decimals'] = array(
				'id'        => $this->shortcodes_section_key . '_decimals',
				'name'      => 'decimals',
				'type'      => 'number',
				'label'     => esc_html__( 'Decimals', 'ebox' ),
				'help_text' => esc_html__( 'Number of decimal places to show. Default is 2.', 'ebox' ),
				'value'     => '',
				'class'     => 'small-text',
			);

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
			$this->shortcodes_option_fields = apply_filters( 'ebox_settings_fields', $this->shortcodes_option_fields, $this->shortcodes_section_key );

			parent::init_shortcodes_section_fields();
		}

		/**
		 * Show Shortcode section footer extra
		 *
		 * @since 2.4.0
		 */
		public function show_shortcodes_section_footer_extra() {
			?>
			<script>
				jQuery( function() {
					if ( jQuery( 'form#ebox_shortcodes_form_teaminfo select#teaminfo_show' ).length) {
						jQuery( 'form#ebox_shortcodes_form_teaminfo select#teaminfo_show').on( 'change', function() {
							var selected = jQuery(this).val();
							if ( ['completed_on', 'enrolled_on'].includes( selected) ) {
								jQuery( 'form#ebox_shortcodes_form_teaminfo #teaminfo_format_field').slideDown();
							} else {
								jQuery( 'form#ebox_shortcodes_form_teaminfo #teaminfo_format_field').hide();
								jQuery( 'form#ebox_shortcodes_form_teaminfo #teaminfo_format_field input').val('');
							}

							if ( ['user_team_status', 'enrolled_on', 'completed_on', 'percent_completed'].includes( selected) ) {
								jQuery( 'form#ebox_shortcodes_form_teaminfo #teaminfo_user_id_field').slideDown();
							} else {
								jQuery( 'form#ebox_shortcodes_form_teaminfo #teaminfo_user_id_field').hide();
								jQuery( 'form#ebox_shortcodes_form_teaminfo #teaminfo_user_id_field input').val('');
							}

							if ( ['percent_completed'].includes( selected) ) {
								jQuery( 'form#ebox_shortcodes_form_teaminfo #teaminfo_decimals_field').slideDown();
							} else {
								jQuery( 'form#ebox_shortcodes_form_teaminfo #teaminfo_decimals_field').hide();
								jQuery( 'form#ebox_shortcodes_form_teaminfo #teaminfo_decimals_field input').val('');
							}
						});
						jQuery( 'form#ebox_shortcodes_form_teaminfo select#teaminfo_show').change();
					}
				});
			</script>
			<?php
		}
	}
}
