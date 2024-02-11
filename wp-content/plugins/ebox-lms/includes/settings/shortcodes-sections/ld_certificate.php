<?php
/**
 * ebox Shortcode Section for Certificate [ld_certificate].
 *
 * @since 2.4.0
 * @package ebox\Settings\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Shortcodes_Section' ) ) && ( ! class_exists( 'ebox_Shortcodes_Section_ld_certificate' ) ) ) {
	/**
	 * Class ebox Shortcode Section for Certificate [ld_certificate].
	 *
	 * @since 2.4.0
	 */
	class ebox_Shortcodes_Section_ld_certificate extends ebox_Shortcodes_Section /* phpcs:ignore PEAR.NamingConventions.ValidClassName.Invalid */ {

		/**
		 * Public constructor for class.
		 *
		 * @since 2.4.0
		 *
		 * @param array $fields_args Field Args.
		 */
		public function __construct( $fields_args = array() ) {
			$this->fields_args = $fields_args;

			$this->shortcodes_section_key         = 'ld_certificate';
			$this->shortcodes_section_title       = esc_html__( 'Certificate', 'ebox' );
			$this->shortcodes_section_type        = 2;
			$this->shortcodes_section_description = esc_html__( 'This shortcode shows a Certificate download link.', 'ebox' );

			parent::__construct();
		}

		/**
		 * Initialize the shortcode fields.
		 *
		 * @since 2.4.0
		 */
		public function init_shortcodes_section_fields() {
			$this->shortcodes_option_fields = array(
				'display_type' => array(
					'id'       => $this->shortcodes_section_key . '_display_type',
					'name'     => 'display_type',
					'type'     => 'select',
					'label'    => esc_html__( 'Display Type', 'ebox' ),
					'value'    => '',
					'options'  => array(
						'' => esc_html__( 'Select a Display Type', 'ebox' ),
						ebox_get_post_type_slug( 'course' ) => ebox_get_custom_label( 'course' ),
						ebox_get_post_type_slug( 'team' ) => ebox_get_custom_label( 'team' ),
						ebox_get_post_type_slug( 'quiz' ) => ebox_get_custom_label( 'quiz' ),
					),
					'attrs'    => array(
						'data-shortcode-exclude' => '1',
					),
					'required' => 'required',
				),
				'quiz_id'      => array(
					'id'        => $this->shortcodes_section_key . '_quiz_id',
					'name'      => 'quiz_id',
					'type'      => 'number',
					'label'     => sprintf(
						// translators: placeholder: Quiz.
						esc_html_x( '%s ID', 'placeholder: Quiz', 'ebox' ),
						ebox_Custom_Label::get_label( 'quiz' )
					),
					'help_text' => sprintf(
						// translators: placeholders: Quiz, Quiz.
						esc_html_x( 'Enter single %1$s ID. Leave blank for current %2$s.', 'placeholders: Quiz, Quiz', 'ebox' ),
						ebox_Custom_Label::get_label( 'quiz' ),
						ebox_Custom_Label::get_label( 'quiz' )
					),
					'value'     => '',
					'class'     => 'small-text',
					'required'  => 'required',
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
				'display_as'   => array(
					'id'      => $this->shortcodes_section_key . '_display_as',
					'name'    => 'display_as',
					'type'    => 'select',
					'label'   => esc_html__( 'Display as', 'ebox' ),
					'value'   => 'banner',
					'options' => array(
						'button' => esc_html__( 'Button', 'ebox' ),
						'banner' => sprintf(
							// translators: placeholders: Course, Team.
							esc_html_x( 'Banner (%1$s or %2$s only)', 'placeholders: Course, Team', 'ebox' ),
							ebox_Custom_Label::get_label( 'course' ),
							ebox_Custom_Label::get_label( 'team' )
						),
					),
				),
				'label'        => array(
					'id'        => $this->shortcodes_section_key . '_label',
					'name'      => 'label',
					'type'      => 'text',
					'label'     => esc_html__( 'Label', 'ebox' ),
					'help_text' => esc_html__( 'Label for link shown to user', 'ebox' ),
					'value'     => '',
				),
				'class'        => array(
					'id'        => $this->shortcodes_section_key . '_class',
					'name'      => 'class',
					'type'      => 'text',
					'label'     => esc_html__( 'HTML Class', 'ebox' ),
					'help_text' => esc_html__( 'HTML class for link element', 'ebox' ),
					'value'     => '',
				),
				'context'      => array(
					'id'        => $this->shortcodes_section_key . '_context',
					'name'      => 'context',
					'type'      => 'text',
					'label'     => esc_html__( 'Context', 'ebox' ),
					'help_text' => esc_html__( 'User defined value to be passed into shortcode handler', 'ebox' ),
					'value'     => '',
				),
				'callback'     => array(
					'id'        => $this->shortcodes_section_key . '_callback',
					'name'      => 'callback',
					'type'      => 'text',
					'label'     => esc_html__( 'Callback', 'ebox' ),
					'help_text' => esc_html__( 'Custom callback function to be used instead of default output', 'ebox' ),
					'value'     => '',
				),
			);

			if ( ( isset( $this->fields_args['post_type'] ) ) && ( in_array( $this->fields_args['post_type'], ebox_get_post_types( 'course' ), true ) ) ) {
				unset( $this->shortcodes_option_fields['display_type']['required'] );
				unset( $this->shortcodes_option_fields['course_id']['required'] );
			} elseif ( ( isset( $this->fields_args['post_type'] ) ) && ( ebox_get_post_type_slug( 'team' ) === $this->fields_args['post_type'] ) ) {
				unset( $this->shortcodes_option_fields['display_type']['required'] );
				unset( $this->shortcodes_option_fields['team_id']['required'] );
			} elseif ( ( isset( $this->fields_args['post_type'] ) ) && ( ebox_get_post_type_slug( 'quiz' ) === $this->fields_args['post_type'] ) ) {
				unset( $this->shortcodes_option_fields['display_type']['required'] );
				unset( $this->shortcodes_option_fields['quiz_id']['required'] );
			}

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
					if ( jQuery( 'form#ebox_shortcodes_form_ld_certificate select#ld_certificate_display_type' ).length) {
						jQuery( 'form#ebox_shortcodes_form_ld_certificate select#ld_certificate_display_type').on( 'change', function() {
							var selected = jQuery(this).val();

							if ( selected == 'ebox-courses' ) {
								jQuery( 'form#ebox_shortcodes_form_ld_certificate #ld_certificate_team_id_field').hide();
								jQuery( 'form#ebox_shortcodes_form_ld_certificate input#ld_certificate_team_id').val('');
								if ( jQuery( 'form#ebox_shortcodes_form_ld_certificate #ld_certificate_team_id_field').hasClass('ebox-settings-input-required') ) {
									jQuery( 'form#ebox_shortcodes_form_ld_certificate input#ld_certificate_team_id').attr('required', false);
								}

								jQuery( 'form#ebox_shortcodes_form_ld_certificate #ld_certificate_quiz_id_field').hide();
								jQuery( 'form#ebox_shortcodes_form_ld_certificate input#ld_certificate_quiz_id').val('');
								if ( jQuery( 'form#ebox_shortcodes_form_ld_certificate #ld_certificate_quiz_id_field').hasClass('ebox-settings-input-required') ) {
									jQuery( 'form#ebox_shortcodes_form_ld_certificate input#ld_certificate_quiz_id').attr('required', false);
								}

								jQuery( 'form#ebox_shortcodes_form_ld_certificate #ld_certificate_course_id_field span.ebox_required_field').show();
								jQuery( 'form#ebox_shortcodes_form_ld_certificate #ld_certificate_course_id_field').slideDown();
								if ( jQuery( 'form#ebox_shortcodes_form_ld_certificate #ld_certificate_course_id_field').hasClass('ebox-settings-input-required') ) {
									jQuery( 'form#ebox_shortcodes_form_ld_certificate input#ld_certificate_course_id').attr('required', 'required');
								}
							} else if ( selected == 'teams' ) {
								jQuery( 'form#ebox_shortcodes_form_ld_certificate #ld_certificate_course_id_field').hide();
								jQuery( 'form#ebox_shortcodes_form_ld_certificate input#ld_certificate_course_id').val('');
								if ( jQuery( 'form#ebox_shortcodes_form_ld_certificate #ld_certificate_course_id_field').hasClass('ebox-settings-input-required') ) {
									jQuery( 'form#ebox_shortcodes_form_ld_certificate input#ld_certificate_course_id').attr('required', false);
								}

								jQuery( 'form#ebox_shortcodes_form_ld_certificate #ld_certificate_quiz_id_field').hide();
								jQuery( 'form#ebox_shortcodes_form_ld_certificate input#ld_certificate_quiz_id').val('');
								if ( jQuery( 'form#ebox_shortcodes_form_ld_certificate #ld_certificate_quiz_id_field').hasClass('ebox-settings-input-required') ) {
									jQuery( 'form#ebox_shortcodes_form_ld_certificate input#ld_certificate_quiz_id').attr('required', false);
								}

								jQuery( 'form#ebox_shortcodes_form_ld_certificate #ld_certificate_team_id_field').slideDown();
								if ( jQuery( 'form#ebox_shortcodes_form_ld_certificate #ld_certificate_team_id_field').hasClass('ebox-settings-input-required') ) {
									jQuery( 'form#ebox_shortcodes_form_ld_certificate input#ld_certificate_team_id').attr('required', 'required');
								}
							} else if ( selected == 'ebox-quiz' ) {
								jQuery( 'form#ebox_shortcodes_form_ld_certificate #ld_certificate_team_id_field').hide();
								jQuery( 'form#ebox_shortcodes_form_ld_certificate input#ld_certificate_team_id').val('');
								if ( jQuery( 'form#ebox_shortcodes_form_ld_certificate #ld_certificate_team_id_field').hasClass('ebox-settings-input-required') ) {
									jQuery( 'form#ebox_shortcodes_form_ld_certificate input#ld_certificate_team_id').attr('required', false);
								}

								jQuery( 'form#ebox_shortcodes_form_ld_certificate #ld_certificate_course_id_field').slideDown();
								// When the quiz cert is selected we explicitly set the course_id as not required.
								jQuery( 'form#ebox_shortcodes_form_ld_certificate input#ld_certificate_course_id').attr('required', false);

								jQuery( 'form#ebox_shortcodes_form_ld_certificate #ld_certificate_course_id_field span.ebox_required_field').hide();

								jQuery( 'form#ebox_shortcodes_form_ld_certificate #ld_certificate_quiz_id_field').slideDown();
								if ( jQuery( 'form#ebox_shortcodes_form_ld_certificate #ld_certificate_quiz_id_field').hasClass('ebox-settings-input-required') ) {
									jQuery( 'form#ebox_shortcodes_form_ld_certificate input#ld_certificate_quiz_id').attr('required', 'required');
								}
							} else {
								jQuery( 'form#ebox_shortcodes_form_ld_certificate #ld_certificate_course_id_field').hide();
								jQuery( 'form#ebox_shortcodes_form_ld_certificate input#ld_certificate_course_id').val('');
								if ( jQuery( 'form#ebox_shortcodes_form_ld_certificate #ld_certificate_course_id_field').hasClass('ebox-settings-input-required') ) {
									jQuery( 'form#ebox_shortcodes_form_ld_certificate input#ld_certificate_course_id').attr('required', false);
								}

								jQuery( 'form#ebox_shortcodes_form_ld_certificate #ld_certificate_team_id_field').hide();
								jQuery( 'form#ebox_shortcodes_form_ld_certificate input#ld_certificate_team_id').val('');
								if ( jQuery( 'form#ebox_shortcodes_form_ld_certificate #ld_certificate_team_id_field').hasClass('ebox-settings-input-required') ) {
									jQuery( 'form#ebox_shortcodes_form_ld_certificate input#ld_certificate_team_id').attr('required', false);
								}

								jQuery( 'form#ebox_shortcodes_form_ld_certificate #ld_certificate_quiz_id_field').hide();
								jQuery( 'form#ebox_shortcodes_form_ld_certificate input#ld_certificate_quiz_id').val('');
								if ( jQuery( 'form#ebox_shortcodes_form_ld_certificate #ld_certificate_quiz_id_field').hasClass('ebox-settings-input-required') ) {
									jQuery( 'form#ebox_shortcodes_form_ld_certificate input#ld_certificate_quiz_id').attr('required', false);
								}
							}
						});
						jQuery( 'form#ebox_shortcodes_form_ld_certificate select#ld_certificate_display_type').change();
					}

					if ( jQuery( 'form#ebox_shortcodes_form_ld_certificate select#ld_certificate_display_as' ).length) {
						jQuery( 'form#ebox_shortcodes_form_ld_certificate select#ld_certificate_display_as').on( 'change', function() {
							var selected = jQuery(this).val();

							if ( selected == 'banner' ) {
								jQuery( 'form#ebox_shortcodes_form_ld_certificate #ld_certificate_label_field').hide();
								jQuery( 'form#ebox_shortcodes_form_ld_certificate #ld_certificate_class_field').hide();
								jQuery( 'form#ebox_shortcodes_form_ld_certificate #ld_certificate_context_field').hide();
								jQuery( 'form#ebox_shortcodes_form_ld_certificate #ld_certificate_callback_field').hide();
							} else {
								jQuery( 'form#ebox_shortcodes_form_ld_certificate #ld_certificate_label_field').slideDown();
								jQuery( 'form#ebox_shortcodes_form_ld_certificate #ld_certificate_class_field').slideDown();
								jQuery( 'form#ebox_shortcodes_form_ld_certificate #ld_certificate_context_field').slideDown();
								jQuery( 'form#ebox_shortcodes_form_ld_certificate #ld_certificate_callback_field').slideDown();
							}
						});
						jQuery( 'form#ebox_shortcodes_form_ld_certificate select#ld_certificate_display_as').change();
					}
				});
			</script>
			<?php
		}

	}
}
