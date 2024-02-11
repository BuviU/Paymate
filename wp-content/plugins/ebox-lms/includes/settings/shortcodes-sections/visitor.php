<?php
/**
 * ebox Shortcode Section for Visitor [visitor].
 *
 * @since 2.4.0
 * @package ebox\Settings\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Shortcodes_Section' ) ) && ( ! class_exists( 'ebox_Shortcodes_Section_visitor' ) ) ) {
	/**
	 * Class ebox Shortcode Section for Visitor [visitor].
	 *
	 * @since 2.4.0
	 */
	class ebox_Shortcodes_Section_visitor extends ebox_Shortcodes_Section /* phpcs:ignore PEAR.NamingConventions.ValidClassName.Invalid */ {

		/**
		 * Public constructor for class.
		 *
		 * @since 2.4.0
		 *
		 * @param array $fields_args Field Args.
		 */
		public function __construct( $fields_args = array() ) {
			$this->fields_args = $fields_args;

			$this->shortcodes_section_key   = 'visitor';
			$this->shortcodes_section_title = esc_html__( 'Visitor', 'ebox' );
			$this->shortcodes_section_type  = 2;
			// translators: placeholder: course.
			$this->shortcodes_section_description = sprintf( wp_kses_post( _x( 'This shortcode shows the content if the user is not enrolled in the %s. The shortcode can be used on <strong>any</strong> page or widget area.', 'placeholder: course', 'ebox' ) ), ebox_get_custom_label_lower( 'course' ) );

			parent::__construct();
		}

		/**
		 * Initialize the shortcode fields.
		 *
		 * @since 2.4.0
		 */
		public function init_shortcodes_section_fields() {
			$this->shortcodes_option_fields = array(
				'message'      => array(
					'id'        => $this->shortcodes_section_key . '_message',
					'name'      => 'message',
					'type'      => 'textarea',
					'label'     => esc_html__( 'Message shown to user', 'ebox' ),
					'help_text' => esc_html__( 'Message shown to user', 'ebox' ),
					'value'     => '',
					'required'  => 'required',
				),
				'display_type' => array(
					'id'      => $this->shortcodes_section_key . '_display_type',
					'name'    => 'display_type',
					'type'    => 'select',
					'label'   => esc_html__( 'Display Type', 'ebox' ),
					'value'   => '',
					'options' => array(
						'' => esc_html__( 'Select a Display Type', 'ebox' ),
						ebox_get_post_type_slug( 'course' ) => ebox_get_custom_label( 'course' ),
						ebox_get_post_type_slug( 'team' ) => ebox_get_custom_label( 'team' ),
					),
					'attrs'   => array(
						'data-shortcode-exclude' => '1',
					),
				),
				'course_id'    => array(
					'id'        => $this->shortcodes_section_key . '_course_id',
					'name'      => 'course_id',
					'type'      => 'number',
					'label'     => sprintf(
						// translators: placeholder: Course.
						esc_html_x( '%s ID', 'placeholder: Course', 'ebox' ),
						ebox_Custom_Label::get_label( 'course' )
					),
					'help_text' => sprintf(
						// translators: placeholders: Course, Course.
						esc_html_x( 'Enter single %1$s ID. Leave blank for current %2$s.', 'placeholders: Course, Course', 'ebox' ),
						ebox_Custom_Label::get_label( 'course' ),
						ebox_Custom_Label::get_label( 'course' )
					),
					'value'     => '',
					'class'     => 'small-text',
					'required'  => 'required',
				),
				'team_id'     => array(
					'id'        => $this->shortcodes_section_key . '_team_id',
					'name'      => 'team_id',
					'type'      => 'number',
					'label'     => sprintf(
						// translators: placeholder: Team.
						esc_html_x( '%s ID', 'placeholder: Team', 'ebox' ),
						ebox_Custom_Label::get_label( 'team' )
					),
					'help_text' => sprintf(
						// translators: placeholders: Team, Team.
						esc_html_x( 'Enter single %1$s ID. Leave blank for current %2$s.', 'placeholders: Team, Team', 'ebox' ),
						ebox_Custom_Label::get_label( 'team' ),
						ebox_Custom_Label::get_label( 'team' )
					),
					'value'     => '',
					'class'     => 'small-text',
					'required'  => 'required',
				),
				'user_id'      => array(
					'id'        => $this->shortcodes_section_key . '_user_id',
					'name'      => 'user_id',
					'type'      => 'number',
					'label'     => esc_html__( 'User ID', 'ebox' ),
					'help_text' => esc_html__( 'Enter specific User ID. Leave blank for current User.', 'ebox' ),
					'value'     => '',
					'class'     => 'small-text',
				),
				'autop'        => array(
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

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
			$this->shortcodes_option_fields = apply_filters( 'ebox_settings_fields', $this->shortcodes_option_fields, $this->shortcodes_section_key );

			parent::init_shortcodes_section_fields();
		}

		/**
		 * Show Shortcode section footer extra
		 *
		 * @since 4.0.0
		 */
		public function show_shortcodes_section_footer_extra() {
			?>
			<script>
				jQuery( function() {
					if ( jQuery( 'form#ebox_shortcodes_form_visitor select#visitor_display_type' ).length) {
						jQuery( 'form#ebox_shortcodes_form_visitor select#visitor_display_type').on( 'change', function() {
							var selected = jQuery(this).val();
							console.log( 'selected['+selected+']' );
							if ( selected == 'ebox-courses' ) {
								jQuery( 'form#ebox_shortcodes_form_visitor #visitor_team_id_field').hide();
								jQuery( 'form#ebox_shortcodes_form_visitor input#visitor_team_id').val('');
								jQuery( 'form#ebox_shortcodes_form_visitor input#visitor_team_id').attr('required', false);

								jQuery( 'form#ebox_shortcodes_form_visitor #visitor_course_id_field').slideDown();
								jQuery( 'form#ebox_shortcodes_form_visitor input#visitor_course_id').attr('required', 'required');

							} else if ( selected == 'teams' ) {
								jQuery( 'form#ebox_shortcodes_form_visitor #visitor_course_id_field').hide();
								jQuery( 'form#ebox_shortcodes_form_visitor input#visitor_course_id').val('');
								jQuery( 'form#ebox_shortcodes_form_visitor input#visitor_course_id').attr('required', false);

								jQuery( 'form#ebox_shortcodes_form_visitor #visitor_team_id_field').slideDown();
								jQuery( 'form#ebox_shortcodes_form_visitor input#visitor_team_id').attr('required', 'required');

							} else {
								jQuery( 'form#ebox_shortcodes_form_visitor #visitor_course_id_field').hide();
								jQuery( 'form#ebox_shortcodes_form_visitor input#visitor_course_id').val('');
								jQuery( 'form#ebox_shortcodes_form_visitor input#visitor_course_id').attr('required', false);

								jQuery( 'form#ebox_shortcodes_form_visitor #visitor_team_id_field').hide();
								jQuery( 'form#ebox_shortcodes_form_visitor input#visitor_team_id').val('');
								jQuery( 'form#ebox_shortcodes_form_visitor input#visitor_team_id').attr('required', false);
							}
						});
						jQuery( 'form#ebox_shortcodes_form_visitor select#visitor_display_type').change();
					}
				});
			</script>
			<?php
		}
	}
}

