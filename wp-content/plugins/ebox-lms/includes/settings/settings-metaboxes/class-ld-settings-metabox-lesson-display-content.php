<?php
/**
 * ebox Settings Metabox for Lesson Display and Content Options.
 *
 * @since 3.0.0
 * @package ebox\Settings\Metaboxes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Metabox' ) ) && ( ! class_exists( 'ebox_Settings_Metabox_Lesson_Display_Content' ) ) ) {
	/**
	 * Class ebox Settings Metabox for Lesson Display and Content Options.
	 *
	 * @since 3.0.0
	 */
	class ebox_Settings_Metabox_Lesson_Display_Content extends ebox_Settings_Metabox {

		/**
		 * Public constructor for class
		 *
		 * @since 3.0.0
		 */
		public function __construct() {
			// What screen ID are we showing on.
			$this->settings_screen_id = 'ebox-modules';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_metabox_key = 'ebox-lesson-display-content-settings';

			// Section label/header.
			$this->settings_section_label = esc_html__( 'Display and Content Options', 'ebox' );

			// Used to show the section description above the fields. Can be empty.
			$this->settings_section_description = sprintf(
				// translators: placeholder: lesson.
				esc_html_x( 'Controls the look and feel of the %s and optional content settings', 'placeholder: lesson', 'ebox' ),
				ebox_get_custom_label_lower( 'lesson' )
			);

			add_filter( 'ebox_metabox_save_fields_' . $this->settings_metabox_key, array( $this, 'filter_saved_fields' ), 30, 3 );

			// Map internal settings field ID to legacy field ID.
			$this->settings_fields_map = array(
				// New fields.
				'lesson_materials_enabled'           => 'lesson_materials_enabled',
				'lesson_materials'                   => 'lesson_materials',

				'lesson_video_enabled'               => 'lesson_video_enabled',
				'lesson_video_url'                   => 'lesson_video_url',
				'lesson_video_shown'                 => 'lesson_video_shown',
				'lesson_video_auto_start'            => 'lesson_video_auto_start',
				'lesson_video_show_controls'         => 'lesson_video_show_controls',
				'lesson_video_focus_pause'           => 'lesson_video_focus_pause',
				'lesson_video_track_time'            => 'lesson_video_track_time',
				'lesson_video_auto_complete'         => 'lesson_video_auto_complete',
				'lesson_video_auto_complete_delay'   => 'lesson_video_auto_complete_delay',
				'lesson_video_hide_complete_button'  => 'lesson_video_hide_complete_button',
				'lesson_video_show_complete_button'  => 'lesson_video_show_complete_button',

				'lesson_assignment_upload'           => 'lesson_assignment_upload',
				'assignment_upload_limit_extensions' => 'assignment_upload_limit_extensions',
				'assignment_upload_limit_size'       => 'assignment_upload_limit_size',
				'lesson_assignment_points_enabled'   => 'lesson_assignment_points_enabled',
				'lesson_assignment_points_amount'    => 'lesson_assignment_points_amount',
				'assignment_upload_limit_count'      => 'assignment_upload_limit_count',
				'lesson_assignment_deletion_enabled' => 'lesson_assignment_deletion_enabled',
				'auto_approve_assignment'            => 'auto_approve_assignment',

				'forced_lesson_time_enabled'         => 'forced_lesson_time_enabled',
				'forced_lesson_time'                 => 'forced_lesson_time',
			);

			parent::__construct();
		}

		/**
		 * Initialize the metabox settings values.
		 *
		 * @since 3.0.0
		 */
		public function load_settings_values() {
			global $ebox_lms;

			parent::load_settings_values();
			if ( true === $this->settings_values_loaded ) {

				if ( ! isset( $this->setting_option_values['lesson_materials'] ) ) {
					$this->setting_option_values['lesson_materials'] = '';
				}
				if ( ! empty( $this->setting_option_values['lesson_materials'] ) ) {
					$this->setting_option_values['lesson_materials_enabled'] = 'on';
				} else {
					$this->setting_option_values['lesson_materials_enabled'] = '';
				}

				if ( ! isset( $this->setting_option_values['lesson_video_enabled'] ) ) {
					$this->setting_option_values['lesson_video_enabled'] = '';
				}

				if ( ! isset( $this->setting_option_values['lesson_video_url'] ) ) {
					$this->setting_option_values['lesson_video_url'] = '';
				}

				if ( ( ! isset( $this->setting_option_values['lesson_video_shown'] ) ) || ( empty( $this->setting_option_values['lesson_video_shown'] ) ) ) {
					$this->setting_option_values['lesson_video_shown'] = 'BEFORE';
				}

				if ( ! isset( $this->setting_option_values['lesson_video_auto_start'] ) ) {
					$this->setting_option_values['lesson_video_auto_start'] = '';
				}

				if ( ! isset( $this->setting_option_values['lesson_video_show_controls'] ) ) {
					$this->setting_option_values['lesson_video_show_controls'] = '';
				}

				if ( ! isset( $this->setting_option_values['lesson_video_focus_pause'] ) ) {
					$this->setting_option_values['lesson_video_focus_pause'] = '';
				}

				if ( ! isset( $this->setting_option_values['lesson_video_track_time'] ) ) {
					$this->setting_option_values['lesson_video_track_time'] = '';
				}

				if ( ! isset( $this->setting_option_values['lesson_video_auto_complete'] ) ) {
					$this->setting_option_values['lesson_video_auto_complete'] = '';
				}

				if ( ! isset( $this->setting_option_values['lesson_video_auto_complete_delay'] ) ) {
					$this->setting_option_values['lesson_video_auto_complete_delay'] = '0';
				}

				if ( ! isset( $this->setting_option_values['lesson_video_hide_complete_button'] ) ) {
					$this->setting_option_values['lesson_video_hide_complete_button'] = '';
				}

				if ( 'on' === $this->setting_option_values['lesson_video_hide_complete_button'] ) {
					$this->setting_option_values['lesson_video_show_complete_button'] = '';
				} else {
					$this->setting_option_values['lesson_video_show_complete_button'] = 'on';
				}

				if ( ! isset( $this->setting_option_values['lesson_assignment_upload'] ) ) {
					$this->setting_option_values['lesson_assignment_upload'] = '';
				}

				if ( ! isset( $this->setting_option_values['assignment_upload_limit_extensions'] ) ) {
					$this->setting_option_values['assignment_upload_limit_extensions'] = '';
				}
				if ( ! empty( $this->setting_option_values['assignment_upload_limit_extensions'] ) ) {
					if ( is_array( $this->setting_option_values['assignment_upload_limit_extensions'] ) ) {
						if ( count( $this->setting_option_values['assignment_upload_limit_extensions'] ) > 1 ) {
							$this->setting_option_values['assignment_upload_limit_extensions'] = implode( ',', $this->setting_option_values['assignment_upload_limit_extensions'] );
						} else {
							$this->setting_option_values['assignment_upload_limit_extensions'] = $this->setting_option_values['assignment_upload_limit_extensions'][0];
						}
					}
				}

				if ( ! isset( $this->setting_option_values['assignment_upload_limit_size'] ) ) {
					$this->setting_option_values['assignment_upload_limit_size'] = '';
				}

				if ( ! isset( $this->setting_option_values['lesson_assignment_points_enabled'] ) ) {
					$this->setting_option_values['lesson_assignment_points_enabled'] = '';
				}

				if ( ! isset( $this->setting_option_values['lesson_assignment_points_amount'] ) ) {
					$this->setting_option_values['lesson_assignment_points_amount'] = '';
				}

				if ( ! isset( $this->setting_option_values['assignment_upload_limit_count'] ) ) {
					$this->setting_option_values['assignment_upload_limit_count'] = '';
				}
				$this->setting_option_values['assignment_upload_limit_count'] = absint( $this->setting_option_values['assignment_upload_limit_count'] );
				if ( empty( $this->setting_option_values['assignment_upload_limit_count'] ) ) {
					$this->setting_option_values['assignment_upload_limit_count'] = 1;
				}

				if ( ! isset( $this->setting_option_values['lesson_assignment_deletion_enabled'] ) ) {
					$this->setting_option_values['lesson_assignment_deletion_enabled'] = '';
				}

				if ( ! isset( $this->setting_option_values['auto_approve_assignment'] ) ) {
					$this->setting_option_values['auto_approve_assignment'] = 'on';
				}

				if ( ! isset( $this->setting_option_values['forced_lesson_time'] ) ) {
					$this->setting_option_values['forced_lesson_time'] = '';
				}

				if ( ! isset( $this->setting_option_values['forced_lesson_time_enabled'] ) ) {
					$this->setting_option_values['forced_lesson_time_enabled'] = '';
				}

				if ( ( isset( $this->setting_option_values['forced_lesson_time'] ) ) && ( ! empty( $this->setting_option_values['forced_lesson_time'] ) ) ) {
					$this->setting_option_values['forced_lesson_time_enabled'] = 'on';
				} else {
					$this->setting_option_values['forced_lesson_time_enabled'] = '';
				}
			}

			// Ensure all settings fields are present.
			foreach ( $this->settings_fields_map as $_internal => $_external ) {
				if ( ! isset( $this->setting_option_values[ $_internal ] ) ) {
					$this->setting_option_values[ $_internal ] = '';
				}
			}

			if ( 'on' === $this->setting_option_values['lesson_video_enabled'] ) {
				$this->setting_option_values['lesson_assignment_upload']   = '';
				$this->setting_option_values['forced_lesson_time_enabled'] = '';
			} elseif ( 'on' === $this->setting_option_values['lesson_assignment_upload'] ) {
				$this->setting_option_values['lesson_video_enabled']       = '';
				$this->setting_option_values['forced_lesson_time_enabled'] = '';
			} elseif ( 'on' === $this->setting_option_values['forced_lesson_time_enabled'] ) {
				$this->setting_option_values['lesson_video_enabled']     = '';
				$this->setting_option_values['lesson_assignment_upload'] = '';
			}
			if ( 'on' !== $this->setting_option_values['lesson_video_enabled'] ) {
				$this->setting_option_values['lesson_video_enabled']              = '';
				$this->setting_option_values['lesson_video_url']                  = '';
				$this->setting_option_values['lesson_video_shown']                = '';
				$this->setting_option_values['lesson_video_auto_start']           = '';
				$this->setting_option_values['lesson_video_show_controls']        = '';
				$this->setting_option_values['lesson_video_focus_pause']          = '';
				$this->setting_option_values['lesson_video_track_time']           = '';
				$this->setting_option_values['lesson_video_auto_complete']        = '';
				$this->setting_option_values['lesson_video_auto_complete_delay']  = '0';
				$this->setting_option_values['lesson_video_show_complete_button'] = '';

			} elseif ( 'on' !== $this->setting_option_values['lesson_assignment_upload'] ) {
				$this->setting_option_values['lesson_assignment_upload']           = '';
				$this->setting_option_values['assignment_upload_limit_extensions'] = '';
				$this->setting_option_values['assignment_upload_limit_size']       = '';
				$this->setting_option_values['lesson_assignment_points_enabled']   = '';
				$this->setting_option_values['lesson_assignment_points_amount']    = '';
				$this->setting_option_values['assignment_upload_limit_count']      = '';
				$this->setting_option_values['lesson_assignment_deletion_enabled'] = '';
				$this->setting_option_values['auto_approve_assignment']            = 'on';
			} elseif ( 'on' !== $this->setting_option_values['forced_lesson_time_enabled'] ) {
				$this->setting_option_values['forced_lesson_time_enabled'] = '';
				$this->setting_option_values['forced_lesson_time']         = '';
			}
		}

		/**
		 * Initialize the metabox settings fields.
		 *
		 * @since 3.0.0
		 */
		public function load_settings_fields() {

			$this->setting_option_fields = array(
				'lesson_video_auto_complete'        => array(
					'name'      => 'lesson_video_auto_complete',
					'type'      => 'checkbox-switch',
					'label'     => sprintf(
						// translators: placeholder: Lesson.
						esc_html_x( '%s auto-completion', 'placeholder: Lesson', 'ebox' ),
						ebox_get_custom_label( 'lesson' )
					),
					'default'   => '',
					'value'     => $this->setting_option_values['lesson_video_auto_complete'],
					'options'   => array(
						''   => '',
						'on' => '',
					),
					'help_text' => sprintf(
						// translators: placeholder: lesson.
						esc_html_x( ' Automatically mark the %s as completed once the user has watched the full video.', 'placeholder: lesson', 'ebox' ),
						ebox_get_custom_label_lower( 'lesson' )
					),
					'rest'      => array(
						'show_in_rest' => ebox_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'video_auto_complete',
								// translators: placeholder: Lesson.
								'description' => sprintf( esc_html_x( '%s Video Auto-complete', 'placeholder: Lesson', 'ebox' ), ebox_get_custom_label( 'lesson' ) ),
								'type'        => 'boolean',
								'default'     => false,
							),
						),
					),
				),
				'lesson_video_auto_complete_delay'  => array(
					'name'        => 'lesson_video_auto_complete_delay',
					'label'       => esc_html__( 'Completion delay', 'ebox' ),
					'type'        => 'number',
					'class'       => '-small',
					'default'     => 0,
					'value'       => $this->setting_option_values['lesson_video_auto_complete_delay'],
					'attrs'       => array(
						'step' => 1,
						'min'  => 0,
					),
					'input_label' => esc_html__( 'seconds', 'ebox' ),
					'help_text'   => sprintf(
						// translators: placeholder: lesson.
						esc_html_x( 'Specify a delay between video completion and %s completion.', 'placeholder: lesson', 'ebox' ),
						ebox_get_custom_label_lower( 'lesson' )
					),
					'rest'        => array(
						'show_in_rest' => ebox_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'video_auto_complete_delay',
								// translators: placeholder: Lesson.
								'description' => sprintf( esc_html_x( '%s Video Completion Delay (seconds).', 'placeholder: Lesson', 'ebox' ), ebox_get_custom_label( 'lesson' ) ),
								'type'        => 'integer',
								'default'     => 0,
							),
						),
					),
				),
				'lesson_video_show_complete_button' => array(
					'name'      => 'lesson_video_show_complete_button',
					'label'     => esc_html__( 'Mark Complete Button', 'ebox' ),
					'type'      => 'checkbox-switch',
					'value'     => $this->setting_option_values['lesson_video_show_complete_button'],
					'help_text' => sprintf(
						// translators: placeholder: lesson.
						esc_html_x( 'Display the Mark Complete button on a %s even if not yet clickable.', 'placeholder: lesson', 'ebox' ),
						ebox_get_custom_label_lower( 'lesson' )
					),
					'default'   => '',
					'options'   => array(
						'on' => '',
					),
					'rest'      => array(
						'show_in_rest' => ebox_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'video_show_complete_button',
								// translators: placeholder: Lesson.
								'description' => sprintf( esc_html_x( '%s Video Show Mark Complete Button', 'placeholder: Lesson', 'ebox' ), ebox_get_custom_label( 'lesson' ) ),
								'type'        => 'boolean',
								'default'     => false,
							),
						),
					),
				),
			);
			parent::load_settings_fields();
			$this->settings_sub_option_fields['video_display_timing_after_fields'] = $this->setting_option_fields;

			$this->setting_option_fields = array(
				'assignment_upload_limit_count'      => array(
					'name'        => 'assignment_upload_limit_count',
					'label'       => esc_html__( 'Limit number of uploaded files', 'ebox' ),
					'type'        => 'number',
					'value'       => $this->setting_option_values['assignment_upload_limit_count'],
					'default'     => '1',
					'class'       => 'small-text',
					'input_label' => esc_html__( 'file(s) maximum', 'ebox' ),
					'attrs'       => array(
						'step' => 1,
						'min'  => 1,
					),
					'help_text'   => esc_html__( 'Specify the maximum number of files a user can upload for this assignment.', 'ebox' ),
					'rest'        => array(
						'show_in_rest' => ebox_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								// translators: placeholder: Lesson.
								'description' => sprintf( esc_html_x( '%s Assignment Upload Count Limit.', 'placeholder: Lesson', 'ebox' ), ebox_get_custom_label( 'lesson' ) ),
								'type'        => 'integer',
								'default'     => 0,
							),
						),
					),
				),
				'lesson_assignment_deletion_enabled' => array(
					'name'      => 'lesson_assignment_deletion_enabled',
					'label'     => esc_html__( 'Allow file deletion', 'ebox' ),
					'type'      => 'checkbox-switch',
					'value'     => $this->setting_option_values['lesson_assignment_deletion_enabled'],
					'default'   => '',
					'help_text' => esc_html__( 'Allow the user to delete their own uploaded files. This is only possible up until the assignment has been approved.', 'ebox' ),
					'options'   => array(
						'on' => '',
					),
					'rest'      => array(
						'show_in_rest' => ebox_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'assignment_deletion_enabled',
								// translators: placeholder: Lesson.
								'description' => sprintf( esc_html_x( '%s Assignment Allow File Deletion.', 'placeholder: Lesson', 'ebox' ), ebox_get_custom_label( 'lesson' ) ),
								'type'        => 'boolean',
								'default'     => false,
							),
						),
					),
				),
			);
			parent::load_settings_fields();
			$this->settings_sub_option_fields['lesson_assignment_grading_manual_fields'] = $this->setting_option_fields;

			$this->setting_option_fields = array(
				'lesson_materials_enabled'           => array(
					'name'                => 'lesson_materials_enabled',
					'type'                => 'checkbox-switch',
					'label'               => sprintf(
						// translators: placeholder: Lesson.
						esc_html_x( '%s Materials', 'placeholder: Lesson', 'ebox' ),
						ebox_get_custom_label( 'lesson' )
					),
					'help_text'           => sprintf(
						// translators: placeholder: lesson, lesson.
						esc_html_x( 'List and display support materials for the %1$s. This is visible to any user having access to the %2$s.', 'placeholder: lesson, lesson', 'ebox' ),
						ebox_get_custom_label_lower( 'lesson' ),
						ebox_get_custom_label_lower( 'lesson' )
					),
					'value'               => $this->setting_option_values['lesson_materials_enabled'],
					'default'             => '',
					'options'             => array(
						'on' => sprintf(
							// translators: placeholder: Lesson.
							esc_html_x( 'Any content added below is displayed on the %s page', 'placeholder: Lesson', 'ebox' ),
							ebox_get_custom_label( 'lesson' )
						),
						''   => '',
					),
					'child_section_state' => ( 'on' === $this->setting_option_values['lesson_materials_enabled'] ) ? 'open' : 'closed',
					'rest'                => array(
						'show_in_rest' => ebox_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'materials_enabled',
								'description' => esc_html__( 'Materials Enabled', 'ebox' ),
								'type'        => 'boolean',
								'default'     => false,
							),
						),
					),
				),
				'lesson_materials'                   => array(
					'name'           => 'lesson_materials',
					'type'           => 'wpeditor',
					'parent_setting' => 'lesson_materials_enabled',
					'value'          => $this->setting_option_values['lesson_materials'],
					'default'        => '',
					'placeholder'    => esc_html__( 'Add a list of needed documents or URLs. This field supports HTML.', 'ebox' ),
					'editor_args'    => array(
						'textarea_name' => $this->settings_metabox_key . '[lesson_materials]',
						'textarea_rows' => 3,
					),
					'rest'           => array(
						'show_in_rest' => ebox_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'materials',
								'description' => esc_html__( 'Materials', 'ebox' ),
								'type'        => 'object',
								'properties'  => array(
									'raw'      => array(
										'description' => 'Content for the object, as it exists in the database.',
										'type'        => 'string',
										'context'     => array( 'edit' ),
									),
									'rendered' => array(
										'description' => 'HTML content for the object, transformed for display.',
										'type'        => 'string',
										'context'     => array( 'view', 'edit' ),
										'readonly'    => true,
									),
								),
								'arg_options' => array(
									'sanitize_callback' => null, // Note: sanitization performed in rest_pre_insert_filter().
									'validate_callback' => null,
								),
							),
						),
					),
				),
				'lesson_video_enabled'               => array(
					'name'                => 'lesson_video_enabled',
					'label'               => esc_html__( 'Video Progression', 'ebox' ),
					'type'                => 'checkbox-switch',
					'help_text'           => sprintf(
						// translators: placeholder: Course.
						esc_html_x( 'Require users to watch the full video as part of the %s progression. Use shortcode [ld_video] to move within the post content.', 'placeholder: Course', 'ebox' ),
						ebox_get_custom_label_lower( 'course' )
					),
					'value'               => $this->setting_option_values['lesson_video_enabled'],
					'default'             => '',
					'options'             => array(
						''   => '',
						'on' => array(
							'description' => '',
							'label'       => sprintf(
								// translators: placeholder: Course.
								esc_html_x( 'The below video is tied to %s progression', 'placeholder: Course', 'ebox' ),
								ebox_get_custom_label( 'course' )
							),
							'tooltip'     => sprintf(
								// translators: placeholder: Lesson.
								esc_html_x( 'Cannot be enabled while %s timer or Assignments are enabled', 'placeholder: Lesson', 'ebox' ),
								ebox_get_custom_label( 'lesson' )
							),
						),
					),
					'child_section_state' => ( 'on' === $this->setting_option_values['lesson_video_enabled'] ) ? 'open' : 'closed',
					'rest'                => array(
						'show_in_rest' => ebox_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'video_enabled',
								// translators: placeholder: Lesson.
								'description' => sprintf( esc_html_x( '%s Video Progression Enabled', 'placeholder: Lesson', 'ebox' ), ebox_get_custom_label( 'lesson' ) ),
								'type'        => 'boolean',
								'default'     => false,
							),
						),
					),
				),
				'lesson_video_url'                   => array(
					'name'           => 'lesson_video_url',
					'label'          => esc_html__( 'Video URL', 'ebox' ),
					'type'           => 'textarea',
					'class'          => 'full-text',
					'value'          => $this->setting_option_values['lesson_video_url'],
					'default'        => '',
					'placeholder'    => esc_html__( 'Input URL, iFrame, or shortcode here.', 'ebox' ),
					'attrs'          => array(
						'rows' => '1',
						'cols' => '57',
					),
					'parent_setting' => 'lesson_video_enabled',
					'rest'           => array(
						'show_in_rest' => ebox_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'video_url',
								// translators: placeholder: Lesson.
								'description' => sprintf( esc_html_x( '%s Video Progression URL', 'placeholder: Lesson', 'ebox' ), ebox_get_custom_label( 'lesson' ) ),
								'type'        => 'text',
								'default'     => '',
							),
						),
					),
				),
				'lesson_video_shown'                 => array(
					'name'           => 'lesson_video_shown',
					'label'          => esc_html__( 'Display Timing', 'ebox' ),
					'type'           => 'radio',
					'value'          => $this->setting_option_values['lesson_video_shown'],
					'default'        => 'BEFORE',
					'parent_setting' => 'lesson_video_enabled',
					'options'        => array(
						'BEFORE' => array(
							'label'       => esc_html__( 'Before completed sub-steps', 'ebox' ),
							'description' => sprintf(
								// translators: placeholder: Lesson.
								esc_html_x( 'The video will be shown and must be fully watched before the user can access the %s’s associated steps.', 'placeholder: Lesson', 'ebox' ),
								ebox_get_custom_label_lower( 'lesson' )
							),
						),
						'AFTER'  => array(
							'label'               => esc_html__( 'After completing sub-steps', 'ebox' ),
							'description'         => sprintf(
								// translators: placeholder: Lesson, Lesson.
								esc_html_x( 'The video will be visible after the user has completed the %1$s’s associated steps. The full video must be watched in order to complete the %2$s.', 'placeholder: Lesson, Lesson', 'ebox' ),
								ebox_get_custom_label_lower( 'lesson' ),
								ebox_get_custom_label_lower( 'lesson' )
							),
							'inline_fields'       => array(
								'lesson_video_display_timing_after' => $this->settings_sub_option_fields['video_display_timing_after_fields'],
							),
							'inner_section_state' => ( 'AFTER' === $this->setting_option_values['lesson_video_shown'] ) ? 'open' : 'closed',
						),
					),
					'rest'           => array(
						'show_in_rest' => ebox_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'video_shown',
								// translators: placeholder: Lesson.
								'description' => sprintf( esc_html_x( '%s Video Shown before or after sub-steps', 'placeholder: Lesson', 'ebox' ), ebox_get_custom_label( 'lesson' ) ),
								'default'     => 'BEFORE',
								'type'        => 'string',
								'enum'        => array(
									'BEFORE',
									'AFTER',
								),
							),
						),
					),
				),
				'lesson_video_auto_start'            => array(
					'name'           => 'lesson_video_auto_start',
					'label'          => esc_html__( 'Autostart', 'ebox' ),
					'type'           => 'checkbox-switch',
					'value'          => $this->setting_option_values['lesson_video_auto_start'],
					'help_text'      => esc_html__( 'Note, due to browser requirements videos will be automatically muted for autoplay to work.', 'ebox' ),
					'default'        => '',
					'options'        => array(
						'on' => esc_html__( 'The video now starts automatically on page load', 'ebox' ),
						''   => '',
					),
					'parent_setting' => 'lesson_video_enabled',
					'rest'           => array(
						'show_in_rest' => ebox_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'video_auto_start',
								// translators: placeholder: Lesson.
								'description' => sprintf( esc_html_x( '%s Video Autostart', 'placeholder: Lesson', 'ebox' ), ebox_get_custom_label( 'lesson' ) ),
								'type'        => 'boolean',
								'default'     => false,
							),
						),
					),
				),
				'lesson_video_show_controls'         => array(
					'name'           => 'lesson_video_show_controls',
					'label'          => esc_html__( 'Video Controls Display', 'ebox' ),
					'type'           => 'checkbox-switch',
					'help_text'      => esc_html__( 'Only available for YouTube and local videos. Vimeo supported if autostart is enabled.', 'ebox' ),
					'value'          => $this->setting_option_values['lesson_video_show_controls'],
					'default'        => '',
					'options'        => array(
						''   => '',
						'on' => esc_html__( 'Users can pause, move backward and forward within the video', 'ebox' ),
					),
					'parent_setting' => 'lesson_video_enabled',
					'rest'           => array(
						'show_in_rest' => ebox_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'video_show_controls',
								// translators: placeholder: Lesson.
								'description' => sprintf( esc_html_x( '%s Video Controls Display. YouTube and local videos only', 'placeholder: Lesson', 'ebox' ), ebox_get_custom_label( 'lesson' ) ),
								'type'        => 'boolean',
								'default'     => false,
							),
						),
					),
				),
				'lesson_video_focus_pause'           => array(
					'name'           => 'lesson_video_focus_pause',
					'label'          => esc_html__( 'Video Pause on Window Unfocused', 'ebox' ),
					'type'           => 'checkbox-switch',
					'value'          => $this->setting_option_values['lesson_video_focus_pause'],
					'help_text'      => esc_html__( 'Pause the video if user switches to a different window.', 'ebox' ),
					'default'        => '',
					'options'        => array(
						'on' => '',
						''   => '',
					),
					'parent_setting' => 'lesson_video_enabled',
					'rest'           => array(
						'show_in_rest' => ebox_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key' => 'video_focus_pause',
								'type'      => 'boolean',
								'default'   => false,
							),
						),
					),
				),
				'lesson_video_track_time'            => array(
					'name'           => 'lesson_video_track_time',
					'label'          => esc_html__( 'Video Resume', 'ebox' ),
					'type'           => 'checkbox-switch',
					'value'          => $this->setting_option_values['lesson_video_track_time'],
					'help_text'      => esc_html__( 'Allows user to resume video position. Uses browser cookie.', 'ebox' ),
					'default'        => '',
					'options'        => array(
						'on' => '',
						''   => '',
					),
					'parent_setting' => 'lesson_video_enabled',
					'rest'           => array(
						'show_in_rest' => ebox_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key' => 'video_resume',
								'type'      => 'boolean',
								'default'   => false,
							),
						),
					),
				),

				'lesson_assignment_upload'           => array(
					'name'                => 'lesson_assignment_upload',
					'label'               => esc_html__( 'Assignment Uploads', 'ebox' ),
					'type'                => 'checkbox-switch',
					'default'             => '',
					'value'               => $this->setting_option_values['lesson_assignment_upload'],
					'options'             => array(
						'on' => array(
							'label'       => '',
							'description' => '',
							'tooltip'     => sprintf(
								// translators: placeholder: Lesson.
								esc_html_x( 'Cannot be enabled while %s timer or Video progression are enabled', 'placeholder: Lesson', 'ebox' ),
								ebox_get_custom_label( 'lesson' )
							),
						),
					),
					'child_section_state' => ( 'on' === $this->setting_option_values['lesson_assignment_upload'] ) ? 'open' : 'closed',
					'rest'                => array(
						'show_in_rest' => ebox_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'assignment_upload_enabled',
								// translators: placeholder: Lesson.
								'description' => sprintf( esc_html_x( '%s Assignment Uploads Enabled', 'placeholder: Lesson', 'ebox' ), ebox_get_custom_label( 'lesson' ) ),
								'type'        => 'boolean',
								'default'     => false,
							),
						),
					),
				),
				'assignment_upload_limit_extensions' => array(
					'name'           => 'assignment_upload_limit_extensions',
					'label'          => esc_html__( 'File Extensions', 'ebox' ),
					'type'           => 'text',
					'placeholder'    => esc_html__( 'pdf, xls, zip', 'ebox' ),
					'help_text'      => esc_html__( 'Specify the type of files users can upload. Leave blank for any.', 'ebox' ),
					'class'          => '-small',
					'default'        => '',
					'value'          => $this->setting_option_values['assignment_upload_limit_extensions'],
					'parent_setting' => 'lesson_assignment_upload',
					'rest'           => array(
						'show_in_rest' => ebox_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								// translators: placeholder: Lesson.
								'description' => sprintf( esc_html_x( '%s Assignment Allowed file extensions. Comma separated pdf, xls, zip', 'placeholder: Lesson', 'ebox' ), ebox_get_custom_label( 'lesson' ) ),
								'type'        => 'text',
								'default'     => '',
							),
						),
					),
				),
				'assignment_upload_limit_size'       => array(
					'name'           => 'assignment_upload_limit_size',
					'label'          => esc_html__( 'File Size Limit', 'ebox' ),
					'type'           => 'text',
					'class'          => '-small',
					'placeholder'    => ini_get( 'upload_max_filesize' ),
					'help_text'      => esc_html__( 'Default maximum file size supported is controlled by your host.', 'ebox' ),
					'default'        => '',
					'value'          => $this->setting_option_values['assignment_upload_limit_size'],
					'parent_setting' => 'lesson_assignment_upload',
					'rest'           => array(
						'show_in_rest' => ebox_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								// translators: placeholder: Lesson.
								'description' => sprintf( esc_html_x( '%1$s Assignment Upload size limit. Max per server is %2$s ', 'placeholder: Lesson, Upload size limit', 'ebox' ), ebox_get_custom_label( 'lesson' ), ini_get( 'upload_max_filesize' ) ),
								'type'        => 'text',
								'default'     => '',
							),
						),
					),
				),

				'lesson_assignment_points_enabled'   => array(
					'name'                => 'lesson_assignment_points_enabled',
					'label'               => esc_html__( 'Points', 'ebox' ),
					'type'                => 'checkbox-switch',
					'default'             => 0,
					'value'               => $this->setting_option_values['lesson_assignment_points_enabled'],
					'options'             => array(
						'on' => esc_html__( 'Award points for submitting assignments', 'ebox' ),
						''   => '',
					),
					'parent_setting'      => 'lesson_assignment_upload',
					'child_section_state' => ( 'on' === $this->setting_option_values['lesson_assignment_points_enabled'] ) ? 'open' : 'closed',
					'rest'                => array(
						'show_in_rest' => ebox_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'assignment_points_enabled',
								// translators: placeholder: Lesson.
								'description' => sprintf( esc_html_x( '%s Assignment Points Enabled', 'placeholder: Lesson', 'ebox' ), ebox_get_custom_label( 'lesson' ) ),
								'type'        => 'boolean',
								'default'     => false,
							),
						),
					),
				),
				'lesson_assignment_points_amount'    => array(
					'name'           => 'lesson_assignment_points_amount',
					'type'           => 'number',
					'class'          => '-small',
					'attrs'          => array(
						'step' => 1,
						'min'  => 0,
					),
					'default'        => 0,
					'value'          => $this->setting_option_values['lesson_assignment_points_amount'],
					'input_label'    => esc_html__( 'available point(s)', 'ebox' ),
					'parent_setting' => 'lesson_assignment_points_enabled',
					'rest'           => array(
						'show_in_rest' => ebox_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'assignment_points_amount',
								// translators: placeholder: Lesson.
								'description' => sprintf( esc_html_x( '%s Assignment Points Amount', 'placeholder: Lesson', 'ebox' ), ebox_get_custom_label( 'lesson' ) ),
								'type'        => 'integer',
								'default'     => 0,
							),
						),
					),
				),

				'auto_approve_assignment'            => array(
					'name'           => 'auto_approve_assignment',
					'label'          => esc_html__( 'Grading Type', 'ebox' ),
					'type'           => 'radio',
					'value'          => $this->setting_option_values['auto_approve_assignment'],
					'options'        => array(
						'on' => array(
							'label'       => esc_html__( 'Auto-approve', 'ebox' ),
							'description' => esc_html__( 'No grading or approval needed. The assignment will be automatically approved and full points will be awarded.', 'ebox' ),
						),
						''   => array(
							'label'               => esc_html__( 'Manually grade', 'ebox' ),
							'description'         => sprintf(
								// translators: placeholder: Team, lesson.
								esc_html_x( 'Admin or %1$s leader approval and grading required. The %2$s cannot be completed until the assignment is approved.', 'placeholder: Team, lesson', 'ebox' ),
								ebox_get_custom_label( 'team' ),
								ebox_get_custom_label_lower( 'lesson' )
							),
							'inline_fields'       => array(
								'lesson_assignment_grading_manual' => $this->settings_sub_option_fields['lesson_assignment_grading_manual_fields'],
							),
							'inner_section_state' => ( '' === $this->setting_option_values['auto_approve_assignment'] ) ? 'open' : 'closed',
						),
					),
					'parent_setting' => 'lesson_assignment_upload',
					'rest'           => array(
						'show_in_rest' => ebox_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'assignment_auto_approve',
								// translators: placeholder: Lesson.
								'description' => sprintf( esc_html_x( '%s Assignment Auto-approve Enabled', 'placeholder: Lesson', 'ebox' ), ebox_get_custom_label( 'lesson' ) ),
								'type'        => 'boolean',
								'default'     => true,
								'required'    => false,
							),
						),
					),
				),
				'forced_lesson_time_enabled'         => array(
					'name'                => 'forced_lesson_time_enabled',
					'label'               => sprintf(
						// translators: Forced Lesson Timer Label.
						esc_html_x( 'Forced %s Timer', 'Forced Lesson Timer Label', 'ebox' ),
						ebox_get_custom_label( 'lesson' )
					),
					'default'             => '',
					'type'                => 'checkbox-switch',
					'value'               => $this->setting_option_values['forced_lesson_time_enabled'],
					'help_text'           => sprintf(
						// translators: placeholder: topic.
						esc_html_x( 'The %s cannot be marked as completed until the set time has elapsed.', 'placeholder: Lesson', 'ebox' ),
						ebox_get_custom_label_lower( 'lesson' )
					),
					'options'             => array(
						'on' => array(
							'label'       => '',
							'description' => '',
							'tooltip'     => esc_html__( 'Cannot be enabled while Video progression or Assignments are enabled', 'ebox' ),
						),
					),
					'child_section_state' => ( 'on' === $this->setting_option_values['forced_lesson_time_enabled'] ) ? 'open' : 'closed',
					'rest'                => array(
						'show_in_rest' => ebox_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'forced_timer_enabled',
								// translators: placeholder: Lesson.
								'description' => sprintf( esc_html_x( '%s Time Enabled', 'placeholder: Lesson', 'ebox' ), ebox_get_custom_label( 'lesson' ) ),
								'type'        => 'boolean',
								'default'     => false,
							),
						),
					),
				),
				'forced_lesson_time'                 => array(
					'name'           => 'forced_lesson_time',
					'type'           => 'timer-entry',
					'default'        => '',
					'class'          => 'small-text',
					'value'          => $this->setting_option_values['forced_lesson_time'],
					'parent_setting' => 'forced_lesson_time_enabled',
					'rest'           => array(
						'show_in_rest' => ebox_REST_API::enabled(),
						'rest_args'    => array(
							'schema' => array(
								'field_key'   => 'forced_timer_amount',
								// translators: placeholder: Lesson.
								'description' => sprintf( esc_html_x( '%s Timer Amount.', 'placeholder: Lesson', 'ebox' ), ebox_get_custom_label( 'lesson' ) ),
								'type'        => 'integer',
								'default'     => 0,
							),
						),
					),
				),
			);

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
			$this->setting_option_fields = apply_filters( 'ebox_settings_fields', $this->setting_option_fields, $this->settings_metabox_key );

			parent::load_settings_fields();
		}

		/**
		 * Update Metabox Settings values.
		 *
		 * @since 3.4.0
		 *
		 * @param array $settings_field_updates Array of key/value settings changes.
		 */
		public function apply_metabox_settings_fields_changes( $settings_field_updates = array() ) {
			$settings_field_values = $this->get_settings_metabox_values();

			if ( ! empty( $settings_field_updates ) ) {
				$settings_changes_only = array();
				foreach ( $settings_field_updates as $setting_key => $setting_value ) {
					if ( isset( $settings_field_values[ $setting_key ] ) ) {
						$settings_changes_only[ $setting_key ] = $setting_value;
					}
				}

				if ( ! empty( $settings_changes_only ) ) {
					if ( ( isset( $settings_changes_only['lesson_video_enabled'] ) ) && ( 'on' === $settings_changes_only['lesson_video_enabled'] ) ) {
						$settings_changes_only['lesson_assignment_upload']   = '';
						$settings_changes_only['forced_lesson_time_enabled'] = '';
					} elseif ( ( isset( $settings_changes_only['lesson_assignment_upload'] ) ) && ( 'on' === $settings_changes_only['lesson_assignment_upload'] ) ) {
						$settings_changes_only['lesson_video_enabled']       = '';
						$settings_changes_only['forced_lesson_time_enabled'] = '';
					} elseif ( ( isset( $settings_changes_only['forced_lesson_time_enabled'] ) ) && ( 'on' === $settings_changes_only['forced_lesson_time_enabled'] ) ) {
						$settings_changes_only['lesson_video_enabled']     = '';
						$settings_changes_only['lesson_assignment_upload'] = '';
					} else {
						$settings_changes_only['lesson_video_enabled']       = '';
						$settings_changes_only['lesson_assignment_upload']   = '';
						$settings_changes_only['forced_lesson_time_enabled'] = '';
					}

					foreach ( $settings_changes_only as $setting_key => $setting_value ) {
						if ( isset( $settings_field_values[ $setting_key ] ) ) {
							$settings_field_values[ $setting_key ] = $setting_value;
						}
					}
				}
			}

			return $settings_field_values;
		}

		/**
		 * Filter settings values for metabox before save to database.
		 *
		 * @since 3.0.0
		 *
		 * @param array  $settings_values Array of settings values.
		 * @param string $settings_metabox_key Metabox key.
		 * @param string $settings_screen_id Screen ID.
		 *
		 * @return array $settings_values.
		 */
		public function filter_saved_fields( $settings_values = array(), $settings_metabox_key = '', $settings_screen_id = '' ) {
			if ( ( $settings_screen_id === $this->settings_screen_id ) && ( $settings_metabox_key === $this->settings_metabox_key ) ) {

				if ( ( 'on' !== $settings_values['lesson_materials_enabled'] ) || ( empty( $settings_values['lesson_materials'] ) ) ) {
					$settings_values['lesson_materials_enabled'] = '';
					$settings_values['lesson_materials']         = '';
				}

				// If video progression is enables but the video URL is empty then turn off video progression.
				if ( ( 'on' !== $settings_values['lesson_video_enabled'] ) || ( empty( $settings_values['lesson_video_url'] ) ) ) {
					$settings_values['lesson_video_enabled'] = '';
					$settings_values['lesson_video_url']     = '';
				}

				if ( ( 'on' !== $settings_values['forced_lesson_time_enabled'] ) || ( empty( $settings_values['forced_lesson_time'] ) ) ) {
					$settings_values['forced_lesson_time_enabled'] = '';
					$settings_values['forced_lesson_time']         = '';
				}

				if ( ( 'on' !== $settings_values['lesson_assignment_points_enabled'] ) || ( empty( $settings_values['lesson_assignment_points_amount'] ) ) ) {
					$settings_values['lesson_assignment_points_amount']  = '';
					$settings_values['lesson_assignment_points_enabled'] = '';
				}

				if ( 'on' === $settings_values['lesson_video_enabled'] ) {
					$settings_values['lesson_assignment_upload']   = '';
					$settings_values['forced_lesson_time_enabled'] = '';
				} elseif ( 'on' === $settings_values['lesson_assignment_upload'] ) {
					$settings_values['lesson_video_enabled']       = '';
					$settings_values['forced_lesson_time_enabled'] = '';
				} elseif ( 'on' === $settings_values['forced_lesson_time_enabled'] ) {
					$settings_values['lesson_video_enabled']     = '';
					$settings_values['lesson_assignment_upload'] = '';
				} else {
					$settings_values['lesson_video_enabled']       = '';
					$settings_values['lesson_assignment_upload']   = '';
					$settings_values['forced_lesson_time_enabled'] = '';
				}

				if ( 'on' !== $settings_values['lesson_video_enabled'] ) {
					$settings_values['lesson_video_url']                  = '';
					$settings_values['lesson_video_shown']                = '';
					$settings_values['lesson_video_auto_start']           = '';
					$settings_values['lesson_video_show_controls']        = '';
					$settings_values['lesson_video_focus_pause']          = '';
					$settings_values['lesson_video_track_time']           = '';
					$settings_values['lesson_video_auto_complete']        = '';
					$settings_values['lesson_video_auto_complete_delay']  = '';
					$settings_values['lesson_video_show_complete_button'] = '';
					$settings_values['lesson_video_hide_complete_button'] = '';
				}

				if ( 'on' !== $settings_values['lesson_assignment_upload'] ) {
					$settings_values['assignment_upload_limit_extensions'] = '';
					$settings_values['assignment_upload_limit_size']       = '';
					$settings_values['lesson_assignment_points_enabled']   = '';
					$settings_values['lesson_assignment_points_amount']    = '';
					$settings_values['assignment_upload_limit_count']      = '';
					$settings_values['lesson_assignment_deletion_enabled'] = '';
					$settings_values['auto_approve_assignment']            = '';
				}

				if ( 'on' !== $settings_values['forced_lesson_time_enabled'] ) {
					$settings_values['forced_lesson_time_enabled'] = '';
					$settings_values['forced_lesson_time']         = '';
				}

				if ( 'on' === $settings_values['lesson_video_enabled'] ) {
					if ( ( 'on' === $settings_values['lesson_video_show_complete_button'] ) ) {
						$settings_values['lesson_video_hide_complete_button'] = '';
					} else {
						$settings_values['lesson_video_hide_complete_button'] = 'on';
					}
				}

				if ( 'on' === $settings_values['lesson_assignment_upload'] ) {
					if ( ! empty( $settings_values['assignment_upload_limit_extensions'] ) ) {
						$settings_values['assignment_upload_limit_extensions'] = ebox_validate_extensions( $settings_values['assignment_upload_limit_extensions'] );
					}

					if ( ! empty( $settings_values['assignment_upload_limit_size'] ) ) {
						$limit_file_size    = ebox_return_bytes_from_shorthand( $settings_values['assignment_upload_limit_size'] );
						$wp_limit_file_size = wp_max_upload_size();

						if ( $limit_file_size > $wp_limit_file_size ) {
							$settings_values['assignment_upload_limit_size'] = '';
						}
					}
				}
			}

			return $settings_values;
		}

		// End of functions.
	}

	add_filter(
		'ebox_post_settings_metaboxes_init_' . ebox_get_post_type_slug( 'lesson' ),
		function( $metaboxes = array() ) {
			if ( ( ! isset( $metaboxes['ebox_Settings_Metabox_Lesson_Display_Content'] ) ) && ( class_exists( 'ebox_Settings_Metabox_Lesson_Display_Content' ) ) ) {
				$metaboxes['ebox_Settings_Metabox_Lesson_Display_Content'] = ebox_Settings_Metabox_Lesson_Display_Content::add_metabox_instance();
			}

			return $metaboxes;
		},
		50,
		1
	);
}
