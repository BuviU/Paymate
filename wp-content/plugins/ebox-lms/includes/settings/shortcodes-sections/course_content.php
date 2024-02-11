<?php
/**
 * ebox Shortcode Section for Course Content [course_content].
 *
 * @since 2.4.0
 * @package ebox\Settings\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Shortcodes_Section' ) ) && ( ! class_exists( 'ebox_Shortcodes_Section_course_content' ) ) ) {
	/**
	 * Class ebox Shortcode Section for Course Content [course_content]
	 *
	 * @since 2.4.0
	 */
	class ebox_Shortcodes_Section_course_content extends ebox_Shortcodes_Section  /* phpcs:ignore PEAR.NamingConventions.ValidClassName.Invalid */ {

		/**
		 * Public constructor for class.
		 *
		 * @since 2.4.0
		 *
		 * @param array $fields_args Field Args.
		 */
		public function __construct( $fields_args = array() ) {
			$this->fields_args = $fields_args;

			$this->shortcodes_section_key   = 'course_content';
			$this->shortcodes_section_title = sprintf(
				// translators: placeholder: Course.
				esc_html_x( '%s Content', 'placeholder: Course', 'ebox' ),
				ebox_Custom_Label::get_label( 'course' )
			);
			$this->shortcodes_section_type        = 1;
			$this->shortcodes_section_description = sprintf(
				// translators: placeholders: Course, lesson, topics, quizzes.
				esc_html_x( 'This shortcode displays the %1$s Content table (%2$s, %3$s, and %4$s) when inserted on a page or post.', 'placeholders: Course, lesson, topics, quizzes', 'ebox' ),
				ebox_Custom_Label::get_label( 'course' ),
				ebox_get_custom_label_lower( 'modules' ),
				ebox_get_custom_label_lower( 'topics' ),
				ebox_get_custom_label_lower( 'quizzes' )
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
					),
					'attrs'    => array(
						'data-shortcode-exclude' => '1',
					),
					'required' => 'required',
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
						// translators: placeholder: Course.
						esc_html_x( 'Enter single %s ID', 'placeholders: Course', 'ebox' ),
						ebox_Custom_Label::get_label( 'course' )
					),
					'value'     => '',
					'class'     => 'small-text',
					'required'  => 'required',
				),
				'post_id'      => array(
					'id'        => $this->shortcodes_section_key . '_post_id',
					'name'      => 'post_id',
					'type'      => 'number',
					'label'     => esc_html__( 'Step ID', 'ebox' ),
					'help_text' => sprintf(
						// translators: placeholder: Course.
						esc_html_x( 'Enter single Step ID. Leave blank if used within a %s.', 'placeholders: Course', 'ebox' ),
						ebox_Custom_Label::get_label( 'course' )
					),
					'value'     => '',
					'class'     => 'small-text',
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
						// translators: placeholder: Team.
						esc_html_x( 'Enter single %s ID', 'placeholders: Team', 'ebox' ),
						ebox_Custom_Label::get_label( 'team' )
					),
					'value'     => '',
					'class'     => 'small-text',
					'required'  => 'required',
				),
				'num'          => array(
					'id'        => $this->shortcodes_section_key . '_num',
					'name'      => 'num',
					'type'      => 'number',
					'label'     => esc_html__( 'Items Per Page', 'ebox' ),
					'help_text' => esc_html__( 'Leave empty for default or 0 to show all items.', 'ebox' ),
					'value'     => '',
					'class'     => 'small-text',
					'attrs'     => array(
						'min'  => 0,
						'step' => 1,
					),
				),

			);

			if ( ( isset( $this->fields_args['post_type'] ) ) && ( in_array( $this->fields_args['post_type'], ebox_get_post_types( 'course' ), true ) ) ) {
				unset( $this->shortcodes_option_fields['display_type']['required'] );
				unset( $this->shortcodes_option_fields['course_id']['required'] );
			} elseif ( ( isset( $this->fields_args['post_type'] ) ) && ( ebox_get_post_type_slug( 'team' ) === $this->fields_args['post_type'] ) ) {
				unset( $this->shortcodes_option_fields['display_type']['required'] );
				unset( $this->shortcodes_option_fields['team_id']['required'] );
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
					if ( jQuery( 'form#ebox_shortcodes_form_course_content select#course_content_display_type' ).length) {
						jQuery( 'form#ebox_shortcodes_form_course_content select#course_content_display_type').on( 'change', function() {
							var selected = jQuery(this).val();

							if ( selected == 'ebox-courses' ) {
								jQuery( 'form#ebox_shortcodes_form_course_content #course_content_team_id_field').hide();
								jQuery( 'form#ebox_shortcodes_form_course_content input#course_content_team_id').val('');
								if ( jQuery( 'form#ebox_shortcodes_form_course_content #course_content_team_id_field').hasClass('ebox-settings-input-required') ) {
									jQuery( 'form#ebox_shortcodes_form_course_content input#course_content_team_id').attr('required', false);
								}

								jQuery( 'form#ebox_shortcodes_form_course_content #course_content_course_id_field').slideDown();
								if ( jQuery( 'form#ebox_shortcodes_form_course_content #course_content_course_id_field').hasClass('ebox-settings-input-required') ) {
									jQuery( 'form#ebox_shortcodes_form_course_content input#course_content_course_id').attr('required', 'required');
								}

								jQuery( 'form#ebox_shortcodes_form_course_content #course_content_post_id_field').slideDown();

								jQuery( 'form#ebox_shortcodes_form_course_content #course_content_num_field').slideDown();
							} else if ( selected == 'teams' ) {
								jQuery( 'form#ebox_shortcodes_form_course_content #course_content_course_id_field').hide();
								jQuery( 'form#ebox_shortcodes_form_course_content input#course_content_course_id').val('');
								if ( jQuery( 'form#ebox_shortcodes_form_course_content #course_content_course_id_field').hasClass('ebox-settings-input-required') ) {
									jQuery( 'form#ebox_shortcodes_form_course_content input#course_content_course_id').attr('required', false);
								}

								jQuery( 'form#ebox_shortcodes_form_course_content #course_content_post_id_field').hide();
								jQuery( 'form#ebox_shortcodes_form_course_content input#course_content_post_id').val('');

								jQuery( 'form#ebox_shortcodes_form_course_content #course_content_team_id_field').slideDown();
								if ( jQuery( 'form#ebox_shortcodes_form_course_content #course_content_team_id_field').hasClass('ebox-settings-input-required') ) {
									jQuery( 'form#ebox_shortcodes_form_course_content input#course_content_team_id').attr('required', 'required');
								}

								jQuery( 'form#ebox_shortcodes_form_course_content #course_content_num_field').slideDown();
							} else {
								jQuery( 'form#ebox_shortcodes_form_course_content #course_content_course_id_field').hide();
								jQuery( 'form#ebox_shortcodes_form_course_content input#course_content_course_id').val('');
								if ( jQuery( 'form#ebox_shortcodes_form_course_content #course_content_course_id_field').hasClass('ebox-settings-input-required') ) {
									jQuery( 'form#ebox_shortcodes_form_course_content input#course_content_course_id').attr('required', false);
								}

								jQuery( 'form#ebox_shortcodes_form_course_content #course_content_post_id_field').hide();
								jQuery( 'form#ebox_shortcodes_form_course_content input#course_content_post_id').val('');

								jQuery( 'form#ebox_shortcodes_form_course_content #course_content_team_id_field').hide();
								jQuery( 'form#ebox_shortcodes_form_course_content input#course_content_team_id').val('');
								if ( jQuery( 'form#ebox_shortcodes_form_course_content #course_content_team_id_field').hasClass('ebox-settings-input-required') ) {
									jQuery( 'form#ebox_shortcodes_form_course_content input#course_content_team_id').attr('required', false);
								}

								jQuery( 'form#ebox_shortcodes_form_course_content #course_content_num_field').hide();
								jQuery( 'form#ebox_shortcodes_form_course_content input#course_content_num').val('');
							}
						});
						jQuery( 'form#ebox_shortcodes_form_course_content select#course_content_display_type').change();
					}
				});
			</script>
			<?php
		}
	}
}
