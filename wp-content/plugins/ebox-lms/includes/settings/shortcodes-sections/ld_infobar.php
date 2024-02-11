<?php
/**
 * ebox Shortcode Section for Infobar [ld_infobar].
 *
 * @since 4.0.0
 * @package ebox\Settings\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Shortcodes_Section' ) ) && ( ! class_exists( 'ebox_Shortcodes_Section_ld_infobar' ) ) ) {
	/**
	 * Class ebox Shortcode Section for Infobar [ld_infobar].
	 *
	 * @since 4.0.0
	 */
	class ebox_Shortcodes_Section_ld_infobar extends ebox_Shortcodes_Section /* phpcs:ignore PEAR.NamingConventions.ValidClassName.Invalid */ {

		/**
		 * Public constructor for class.
		 *
		 * @since 4.0.0
		 *
		 * @param array $fields_args Field Args.
		 */
		public function __construct( $fields_args = array() ) {
			$this->fields_args = $fields_args;

			$this->shortcodes_section_key         = 'ld_infobar';
			$this->shortcodes_section_title       = esc_html__( 'Infobar', 'ebox' );
			$this->shortcodes_section_type        = 1;
			$this->shortcodes_section_description = esc_html__( 'Displays the infobar for the current post or of a specified post.', 'ebox' );

			add_action( 'ebox_shortcodes_section_header_before_title', array( $this, 'show_legacy_template_support_message' ), 10, 1 );

			parent::__construct();
		}

		/**
		 * Initialize the shortcode fields.
		 *
		 * @since 4.0.0
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
						// translators: placeholders: Course, Course.
						esc_html_x( 'Enter single %1$s ID. Leave blank for current %2$s.', 'placeholders: Course, Course', 'ebox' ),
						ebox_Custom_Label::get_label( 'course' ),
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
					'help_text' => esc_html__( 'Enter a User ID. Leave blank for current user.', 'ebox' ),
					'value'     => '',
					'class'     => 'small-text',
				),
			);

			if ( ( isset( $this->fields_args['post_type'] ) ) && ( in_array( $this->fields_args['post_type'], ebox_get_post_types( 'course' ), true ) ) ) {
				unset( $this->shortcodes_option_fields['display_type']['required'] );
				unset( $this->shortcodes_option_fields['course_id']['required'] );
			}

			if ( ( isset( $this->fields_args['post_type'] ) ) && ( ebox_get_post_type_slug( 'team' ) === $this->fields_args['post_type'] ) ) {
				unset( $this->shortcodes_option_fields['display_type']['required'] );
				unset( $this->shortcodes_option_fields['team_id']['required'] );
			}

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
			$this->shortcodes_option_fields = apply_filters( 'ebox_settings_fields', $this->shortcodes_option_fields, $this->shortcodes_section_key );

			parent::init_shortcodes_section_fields();
		}

		/**
		 * Show "Legacy" template not supported message.
		 *
		 * @since 4.0.0
		 * @param string $shortcodes_section_key Shortcodes section key.
		 */
		public function show_legacy_template_support_message( $shortcodes_section_key ) {
			if ( $this->shortcodes_section_key === $shortcodes_section_key ) {
				$message = ebox_get_legacy_not_supported_message();
				if ( ! empty( $message ) ) {
					?><p class="ebox-block-error-message"><?php echo wp_kses_post( $message ); ?></p>
					<?php
				}
			}
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
					if ( jQuery( 'form#ebox_shortcodes_form_ld_infobar select#ld_infobar_display_type' ).length) {
						jQuery( 'form#ebox_shortcodes_form_ld_infobar select#ld_infobar_display_type').on( 'change', function() {
							var selected = jQuery(this).val();

							if ( selected == 'ebox-courses' ) {
								jQuery( 'form#ebox_shortcodes_form_ld_infobar #ld_infobar_team_id_field').hide();
								jQuery( 'form#ebox_shortcodes_form_ld_infobar input#ld_infobar_team_id').val('');
								if ( jQuery( 'form#ebox_shortcodes_form_ld_infobar #ld_infobar_team_id_field').hasClass('ebox-settings-input-required') ) {
									jQuery( 'form#ebox_shortcodes_form_ld_infobar input#ld_infobar_team_id').attr('required', false);
								}

								jQuery( 'form#ebox_shortcodes_form_ld_infobar #ld_infobar_course_id_field').slideDown();
								if ( jQuery( 'form#ebox_shortcodes_form_ld_infobar #ld_infobar_course_id_field').hasClass('ebox-settings-input-required') ) {
									jQuery( 'form#ebox_shortcodes_form_ld_infobar input#ld_infobar_course_id').attr('required', 'required');
								}
								jQuery( 'form#ebox_shortcodes_form_ld_infobar #ld_infobar_post_id_field').slideDown();
								jQuery( 'form#ebox_shortcodes_form_ld_infobar #ld_infobar_user_id_field').slideDown();
							} else if ( selected == 'teams' ) {
								jQuery( 'form#ebox_shortcodes_form_ld_infobar #ld_infobar_course_id_field').hide();
								jQuery( 'form#ebox_shortcodes_form_ld_infobar input#ld_infobar_course_id').val('');
								if ( jQuery( 'form#ebox_shortcodes_form_ld_infobar #ld_infobar_course_id_field').hasClass('ebox-settings-input-required') ) {
									jQuery( 'form#ebox_shortcodes_form_ld_infobar input#ld_infobar_course_id').attr('required', false);
								}
								jQuery( 'form#ebox_shortcodes_form_ld_infobar #ld_infobar_post_id_field').hide();

								jQuery( 'form#ebox_shortcodes_form_ld_infobar #ld_infobar_team_id_field').slideDown();
								if ( jQuery( 'form#ebox_shortcodes_form_ld_infobar #ld_infobar_team_id_field').hasClass('ebox-settings-input-required') ) {
									jQuery( 'form#ebox_shortcodes_form_ld_infobar input#ld_infobar_team_id').attr('required', 'required');
								}

								jQuery( 'form#ebox_shortcodes_form_ld_infobar #ld_infobar_user_id_field').slideDown();
							} else {
								jQuery( 'form#ebox_shortcodes_form_ld_infobar #ld_infobar_course_id_field').hide();
								jQuery( 'form#ebox_shortcodes_form_ld_infobar input#ld_infobar_course_id').val('');
								if ( jQuery( 'form#ebox_shortcodes_form_ld_infobar #ld_infobar_course_id_field').hasClass('ebox-settings-input-required') ) {
									jQuery( 'form#ebox_shortcodes_form_ld_infobar input#ld_infobar_course_id').attr('required', false);
								}

								jQuery( 'form#ebox_shortcodes_form_ld_infobar #ld_infobar_team_id_field').hide();
								jQuery( 'form#ebox_shortcodes_form_ld_infobar input#ld_infobar_team_id').val('');
								if ( jQuery( 'form#ebox_shortcodes_form_ld_infobar #ld_infobar_team_id_field').hasClass('ebox-settings-input-required') ) {
									jQuery( 'form#ebox_shortcodes_form_ld_infobar input#ld_infobar_team_id').attr('required', false);
								}

								jQuery( 'form#ebox_shortcodes_form_ld_infobar #ld_infobar_post_id_field').hide();
								jQuery( 'form#ebox_shortcodes_form_ld_infobar #ld_infobar_user_id_field').hide();
							}
						});
						jQuery( 'form#ebox_shortcodes_form_ld_infobar select#ld_infobar_display_type').change();
					}
				});
			</script>
			<?php
		}

	}
}
