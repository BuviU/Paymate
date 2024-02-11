<?php
/**
 * ebox Shortcode Section for Quizinfo [quizinfo].
 *
 * @since 2.4.0
 * @package ebox\Settings\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Shortcodes_Section' ) ) && ( ! class_exists( 'ebox_Shortcodes_Section_quizinfo' ) ) ) {
	/**
	 * Class ebox Shortcode Section for Quizinfo [quizinfo].
	 *
	 * @since 2.4.0
	 */
	class ebox_Shortcodes_Section_quizinfo extends ebox_Shortcodes_Section /* phpcs:ignore PEAR.NamingConventions.ValidClassName.Invalid */ {

		/**
		 * Public constructor for class.
		 *
		 * @since 2.4.0
		 *
		 * @param array $fields_args Field Args.
		 */
		public function __construct( $fields_args = array() ) {
			$this->fields_args = $fields_args;

			$this->shortcodes_section_key = 'quizinfo';
			// translators: placeholder: Quiz.
			$this->shortcodes_section_title = sprintf( esc_html_x( '%s Info', 'placeholder: Quiz', 'ebox' ), ebox_Custom_Label::get_label( 'quiz' ) );
			$this->shortcodes_section_type  = 1;
			// translators: placeholder: quiz.
			$this->shortcodes_section_description = sprintf( esc_html_x( 'This shortcode displays information regarding %s attempts on the certificate. This shortcode can use the following parameters:', 'placeholder: quiz', 'ebox' ), ebox_get_custom_label_lower( 'quiz' ) );

			parent::__construct();
		}

		/**
		 * Initialize the shortcode fields.
		 *
		 * @since 2.4.0
		 */
		public function init_shortcodes_section_fields() {
			$this->shortcodes_option_fields = array(
				'show'     => array(
					'id'      => $this->shortcodes_section_key . '_show',
					'name'    => 'show',
					'type'    => 'select',
					'label'   => esc_html__( 'Show', 'ebox' ),
					'value'   => 'quiz_title',
					'options' => array(
						'quiz_title'   => sprintf(
							// translators: placeholder: Quiz.
							esc_html_x( '%s Title', 'placeholder: Quiz', 'ebox' ),
							ebox_Custom_Label::get_label( 'quiz' )
						),
						'score'        => esc_html__( 'Score', 'ebox' ),
						'count'        => esc_html__( 'Count', 'ebox' ),
						'pass'         => esc_html__( 'Pass', 'ebox' ),
						'timestamp'    => esc_html__( 'Timestamp', 'ebox' ),
						'points'       => esc_html__( 'Points', 'ebox' ),
						'total_points' => esc_html__( 'Total Points', 'ebox' ),
						'percentage'   => esc_html__( 'Percentage', 'ebox' ),
						// translators: placeholder: Course.
						'course_title' => sprintf( _x( '%s Title', 'placeholder: Course', 'ebox' ), ebox_Custom_Label::get_label( 'course' ) ),
						'timespent'    => esc_html__( 'Time Spent', 'ebox' ),
						'field'        => esc_html__( 'Custom Field', 'ebox' ),
					),
				),
				'field_id' => array(
					'id'        => $this->shortcodes_section_key . '_field_id',
					'name'      => 'field_id',
					'type'      => 'text',
					'label'     => esc_html__( 'Custom Field ID', 'ebox' ),
					// translators: placeholder: quiz.
					'help_text' => sprintf( esc_html_x( 'The Field ID is shown on the %s Custom Fields table.', 'placeholder: quiz', 'ebox' ), ebox_get_custom_label( 'quiz' ) ),
					'value'     => '',
				),
				'format'   => array(
					'id'          => $this->shortcodes_section_key . '_format',
					'name'        => 'format',
					'type'        => 'text',
					'label'       => esc_html__( 'Format', 'ebox' ),
					'placeholder' => esc_html__( 'F j, Y, g:i a shown as March 10, 2001, 5:16 pm', 'ebox' ),
					'help_text'   => wp_kses_post( __( 'This can be used to change the date format. Default: "F j, Y, g:i a" shows as <i>March 10, 2001, 5:16 pm</i>. See <a target="_blank" href="http://php.net/manual/en/function.date.php">the full list of available date formatting strings  here.</a>', 'ebox' ) ),
					'value'       => '',
				),
			);

			$this->shortcodes_option_fields['quiz'] = array(
				'id'        => $this->shortcodes_section_key . '_quiz',
				'name'      => 'quiz',
				'type'      => 'number',
				'label'     => sprintf(
					// translators: placeholder: Quiz.
					esc_html_x( '%s ID', 'placeholder: Quiz', 'ebox' ),
					ebox_Custom_Label::get_label( 'quiz' )
				),
				'help_text' => sprintf(
					// translators: placeholder: Quiz, Quiz.
					esc_html_x( 'Enter a single %1$s ID. Leave blank if used within a %2$s or Certificate.', 'placeholder: Quiz, Quiz', 'ebox' ),
					ebox_Custom_Label::get_label( 'quiz' ),
					ebox_Custom_Label::get_label( 'quiz' )
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
				'help_text' => esc_html__( 'Enter a single User ID. Leave blank if used within a Certificate.', 'ebox' ),
				'value'     => '',
				'class'     => 'small-text',
			);

			$this->shortcodes_option_fields['time'] = array(
				'id'        => $this->shortcodes_section_key . '_time',
				'name'      => 'time',
				'type'      => 'text',
				'label'     => esc_html__( 'Attempt timestamp', 'ebox' ),
				'help_text' => sprintf(
					// translators: placeholder: Quiz.
					esc_html_x( 'Single %s attempt timestamp. See WP user profile "#" link on attempt row. Leave blank to use latest attempt or within a Certificate', 'placeholder: Quiz', 'ebox' ),
					ebox_Custom_Label::get_label( 'quiz' )
				),
				'value'     => '',
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
					if ( jQuery( 'form#ebox_shortcodes_form_quizinfo select#quizinfo_show' ).length) {
						jQuery( 'form#ebox_shortcodes_form_quizinfo select#quizinfo_show').on( 'change', function() {
							var selected = jQuery(this).val();
							console.log( 'selected[%o]', selected );
							if ( ( selected == 'timestamp' ) || ( selected == 'field' ) ) {

								// Show the format field row.
								jQuery( 'form#ebox_shortcodes_form_quizinfo #quizinfo_format_field').slideDown();

								// Show the custom field row.
								if ( selected == 'field' ) {
									jQuery( 'form#ebox_shortcodes_form_quizinfo #quizinfo_field_id_field').slideDown();
								} else {
									// Hide and clear the custom field row.
									jQuery( 'form#ebox_shortcodes_form_quizinfo #quizinfo_field_id_field').hide();
									jQuery( 'form#ebox_shortcodes_form_quizinfo #quizinfo_field_id_field input').val('');
								}
							} else {
								// Hide and clear the custom field row.
								jQuery( 'form#ebox_shortcodes_form_quizinfo #quizinfo_field_id_field').hide();
								jQuery( 'form#ebox_shortcodes_form_quizinfo #quizinfo_field_id_field input').val('');

								// Hide and clear the format field row.
								jQuery( 'form#ebox_shortcodes_form_quizinfo #quizinfo_format_field').hide();
								jQuery( 'form#ebox_shortcodes_form_quizinfo #quizinfo_format_field input').val('');
							}
						});
						jQuery( 'form#ebox_shortcodes_form_quizinfo select#quizinfo_show').change();
					}

					if ( jQuery( 'form#ebox_shortcodes_form_quizinfo input#quizinfo_time' ).length) {
						jQuery( 'form#ebox_shortcodes_form_quizinfo input#quizinfo_time').on( 'change', function(event) {
							var input_value = jQuery(event.currentTarget).val();
							if ( ( input_value.length ) && ( input_value.startsWith('data:quizinfo:', 0 ) ) ) {
								var input_value_parts = input_value.split(':');
								if ( input_value_parts.length > 2 ) {
									var field_id = '';
									for (let index = 2; index < input_value_parts.length; index++) {
										if ( field_id == '' ) {
											if ( input_value_parts[index] == 'quiz' ) {
												field_id = 'quiz_id';
											} else if ( input_value_parts[index] == 'user' ) {
												field_id = 'user_id';
											} else if ( input_value_parts[index] == 'time' ) {
												field_id = 'time';
											}
											continue;
										} else {
											if ( field_id == 'quiz_id' ) {
												jQuery( 'form#ebox_shortcodes_form_quizinfo input#quizinfo_quiz').val(input_value_parts[index]);
											} else if ( field_id == 'user_id' ) {
												jQuery( 'form#ebox_shortcodes_form_quizinfo input#quizinfo_user_id').val(input_value_parts[index]);
											} else if ( field_id == 'time' ) {
												jQuery( 'form#ebox_shortcodes_form_quizinfo input#quizinfo_time').val(input_value_parts[index]);
											}
											field_id = '';
											continue;
										}									
									}
								}
							}

						});
						jQuery( 'form#ebox_shortcodes_form_quizinfo select#quizinfo_time').change();
					}
				});
			</script>
			<?php
		}

	}
}
