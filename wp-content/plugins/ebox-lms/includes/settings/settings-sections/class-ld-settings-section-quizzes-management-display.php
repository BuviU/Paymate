<?php
/**
 * ebox Settings Section for Quizzes Management and Display Metabox.
 *
 * @since 3.0.0
 * @package ebox\Settings\Sections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Section' ) ) && ( ! class_exists( 'ebox_Settings_Quizzes_Management_Display' ) ) ) {
	/**
	 * Class ebox Settings Section for Quizzes Management and Display Metabox.
	 *
	 * @since 3.0.0
	 */
	class ebox_Settings_Quizzes_Management_Display extends ebox_Settings_Section {

		/**
		 * Protected constructor for class
		 *
		 * @since 3.0.0
		 */
		protected function __construct() {

			// What screen ID are we showing on.
			$this->settings_screen_id = 'ebox-quiz_page_quizzes-options';

			// The page ID (different than the screen ID).
			$this->settings_page_id = 'quizzes-options';

			// This is the 'option_name' key used in the wp_options table.
			$this->setting_option_key = 'ebox_settings_quizzes_management_display';

			// This is the HTML form field prefix used.
			$this->setting_field_prefix = 'ebox_settings_quizzes_management_display';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_section_key = 'quiz_builder';

			// Section label/header.
			$this->settings_section_label = sprintf(
				// translators: placeholder: Quiz.
				esc_html_x( 'Global %s Management & Display Settings', 'Quiz Builder', 'ebox' ),
				ebox_Custom_Label::get_label( 'quiz' )
			);

			$this->settings_section_description = sprintf(
				// translators: placeholder: Quiz.
				esc_html_x( 'Control settings for %s creation, and visual organization', 'placeholder: Quiz', 'ebox' ),
				ebox_Custom_Label::get_label( 'quiz' )
			);

			// Define the deprecated Class and Fields.
			$this->settings_deprecated = array(
				'ebox_Settings_Quizzes_Builder'      => array(
					'option_key' => 'ebox_settings_quizzes_builder',
					'fields'     => array(
						'enabled'                => 'quiz_builder_enabled',
						'shared_questions'       => 'quiz_builder_shared_questions',
						'per_page'               => 'quiz_builder_per_page',
						'force_quiz_builder'     => 'force_quiz_builder',
						'force_shared_questions' => 'force_shared_questions',
					),
				),
				'ebox_Settings_Quizzes_Time_Formats' => array(
					'option_key' => 'ebox_settings_quizzes_time_formats',
					'fields'     => array(
						'toplist_time_format'    => 'statistics_time_format',
						'statistics_time_format' => 'toplist_time_format',
					),
				),
			);

			add_action( 'wp_ajax_' . $this->setting_field_prefix, array( $this, 'ajax_action' ) );
			add_filter( 'ebox_settings_field', array( $this, 'ebox_settings_field_filter' ), 1, 1 );

			parent::__construct();
		}

		/**
		 * Initialize the metabox settings values.
		 *
		 * @since 3.0.0
		 */
		public function load_settings_values() {
			parent::load_settings_values();

			// If the settings set as a whole is empty then we set a default.
			if ( empty( $this->setting_option_values ) ) {
				// If the settings set as a whole is empty then we set a default.
				if ( false === $this->setting_option_values ) {
					$this->transition_deprecated_settings();
				}

				if ( true === ebox_is_data_upgrade_quiz_questions_updated() ) {
					$this->setting_option_values['quiz_builder_enabled'] = 'yes';
				} else {
					$this->setting_option_values['quiz_builder_enabled']          = '';
					$this->setting_option_values['quiz_builder_shared_questions'] = '';
				}
			}

			if ( ! isset( $this->setting_option_values['quiz_builder_enabled'] ) ) {
				$this->setting_option_values['quiz_builder_enabled'] = '';
			}

			if ( ! isset( $this->setting_option_values['quiz_builder_per_page'] ) ) {
				$this->setting_option_values['quiz_builder_per_page'] = ebox_LMS_DEFAULT_WIDGET_PER_PAGE;
			} else {
				$this->setting_option_values['quiz_builder_per_page'] = absint( $this->setting_option_values['quiz_builder_per_page'] );
			}

			if ( empty( $this->setting_option_values['quiz_builder_per_page'] ) ) {
				$this->setting_option_values['quiz_builder_per_page'] = ebox_LMS_DEFAULT_WIDGET_PER_PAGE;
			}

			if ( empty( $this->setting_option_values['quiz_builder_shared_questions'] ) ) {
				$this->setting_option_values['quiz_builder_shared_questions'] = '';
			}

			if ( ! isset( $this->setting_option_values['force_quiz_builder'] ) ) {
				$this->setting_option_values['force_quiz_builder'] = '';
			}
			if ( ! isset( $this->setting_option_values['force_shared_questions'] ) ) {
				$this->setting_option_values['force_shared_questions'] = '';
			}

			if ( true !== ebox_is_data_upgrade_quiz_questions_updated() ) {
				$this->setting_option_values['quiz_builder_enabled']          = '';
				$this->setting_option_values['quiz_builder_shared_questions'] = '';
				$this->setting_option_values['force_quiz_builder']            = '';
				$this->setting_option_values['force_shared_questions']        = '';
			}

			$wp_date_format      = get_option( 'date_format' );
			$wp_time_format      = get_option( 'time_format' );
			$wp_date_time_format = esc_attr( $wp_date_format ) . ' ' . esc_attr( $wp_time_format );

			if ( ( ! isset( $this->setting_option_values['toplist_time_format'] ) ) || ( empty( $this->setting_option_values['toplist_time_format'] ) ) ) {
				$this->setting_option_values['toplist_time_format'] = $wp_date_time_format;
			}
			if ( ( ! isset( $this->setting_option_values['statistics_time_format'] ) ) || ( empty( $this->setting_option_values['statistics_time_format'] ) ) ) {
				$this->setting_option_values['statistics_time_format'] = $wp_date_time_format;
			}

			if ( ( $wp_date_time_format === $this->setting_option_values['statistics_time_format'] ) && ( $wp_date_time_format === $this->setting_option_values['toplist_time_format'] ) ) {
				$this->setting_option_values['quiz_builder_time_formats'] = '';
			} else {
				$this->setting_option_values['quiz_builder_time_formats'] = 'yes';
			}

			$this->setting_option_values['quiz_templates'] = array(
				'' => __( 'Select a template', 'ebox' ),
			);
		}

		/**
		 * Filter the Settings Field args.
		 *
		 * This function is called via the `ebox_settings_field` filter and allows
		 * late filtering of the field args just before the display. This is a way to
		 * defer queries etc.
		 *
		 * @since 3.0.0
		 *
		 * @param array $field_args An array of field arguments used to process the output.
		 */
		public function ebox_settings_field_filter( $field_args = array() ) {
			if ( ( ! isset( $field_args['setting_option_key'] ) ) || ( $this->setting_option_key !== $field_args['setting_option_key'] ) ) {
				return $field_args;
			}

			if ( ! isset( $field_args['name'] ) ) {
				return $field_args;
			}

			if ( 'quiz_template' === $field_args['name'] ) {
				$template_mapper = new WpProQuiz_Model_TemplateMapper();
				$quiz_templates  = $template_mapper->fetchAll( WpProQuiz_Model_Template::TEMPLATE_TYPE_QUIZ, false );
				if ( ( ! empty( $quiz_templates ) ) && ( is_array( $quiz_templates ) ) ) {
					$_templates = array();
					foreach ( $quiz_templates as $template_quiz ) {
						$template_name = $template_quiz->getName();
						$template_id   = $template_quiz->getTemplateId();

						if ( ( ! empty( $template_name ) ) && ( ! isset( $_templates[ $template_id ] ) ) ) {
							$_templates[ $template_id ] = esc_html( $template_name );
						}
					}
					asort( $_templates );

					if ( ! isset( $field_args['options'] ) ) {
						$field_args['options'] = array();
					}
					$field_args['options'] += $_templates;
				}
			}

			return $field_args;
		}

		/**
		 * Initialize the metabox settings fields.
		 *
		 * @since 3.0.0
		 */
		public function load_settings_fields() {
			$this->setting_option_fields = array();

			if ( ( defined( 'ebox_QUIZ_BUILDER' ) ) && ( ebox_QUIZ_BUILDER === true ) ) {

				$desc_before_enabled = '';
				if ( true !== ebox_is_data_upgrade_quiz_questions_updated() ) {
					// Used to show the section description above the fields. Can be empty.
					$desc_before_enabled = '<span class="error">' . sprintf(
						// translators: placeholder: Link to Data Upgrade page.
						esc_html_x( 'The Data Upgrade %s must be run to enable the following settings.', 'placeholder: Link to Data Upgrade page', 'ebox' ),
						'<strong><a href="' . add_query_arg(
							array(
								'page'             => 'ebox_lms_advanced',
								'section-advanced' => 'settings_data_upgrades',
							),
							'admin.php'
						) . '">' .
						// translators: placeholder: Question.
						sprintf( esc_html_x( 'Upgrade WPProQuiz %s', 'placeholder: Question.', 'ebox' ), ebox_get_custom_label( 'question' ) ) . '</a></strong>'
					) . '</span>';
				}

				$this->setting_option_fields = array_merge(
					$this->setting_option_fields,
					array(
						'quiz_builder_enabled'          => array(
							'name'                => 'quiz_builder_enabled',
							'type'                => 'checkbox-switch',
							'desc_before'         => $desc_before_enabled,
							'label'               => sprintf(
								// translators: placeholder: Quiz.
								esc_html_x( '%s Builder', 'placeholder: Quiz', 'ebox' ),
								ebox_get_custom_label( 'quiz' )
							),
							'help_text'           => sprintf(
								// translators: placeholder: quizzes, Quiz.
								esc_html_x( 'Manage and create full %1$s within the %2$s Builder.', 'placeholder: quizzes, Quiz', 'ebox' ),
								ebox_get_custom_label_lower( 'quizzes' ),
								ebox_get_custom_label( 'Quiz' )
							),
							'value'               => $this->setting_option_values['quiz_builder_enabled'],
							'options'             => array(
								'yes' => '',
							),
							'child_section_state' => ( 'yes' === $this->setting_option_values['quiz_builder_enabled'] ) ? 'open' : 'closed',
						),
						'quiz_builder_per_page'         => array(
							'name'           => 'quiz_builder_per_page',
							'type'           => 'number',
							'label'          => sprintf(
								// translators: placeholder: Questions.
								esc_html_x( '%s displayed', 'placeholder: Questions', 'ebox' ),
								ebox_get_custom_label( 'questions' )
							),
							'help_text'      => sprintf(
								// translators: placeholder: questions, Quiz.
								esc_html_x( 'Number of additional %1$s displayed in the %2$s Builder sidebar when clicking the "Load More" link.', 'placeholder: questions, Quiz', 'ebox' ),
								ebox_get_custom_label_lower( 'questions' ),
								ebox_get_custom_label( 'quiz' )
							),
							'value'          => $this->setting_option_values['quiz_builder_per_page'],
							'input_label'    => esc_html__( 'per page', 'ebox' ),
							'class'          => 'small-text',
							'attrs'          => array(
								'step' => 1,
								'min'  => 0,
							),
							'parent_setting' => 'quiz_builder_enabled',
						),
						'quiz_builder_shared_questions' => array(
							'name'           => 'quiz_builder_shared_questions',
							'type'           => 'checkbox-switch',
							'label'          => sprintf(
								// translators: placeholder: Quiz, Questions.
								esc_html_x( 'Shared %1$s %2$s', 'placeholder: Quiz, Questions', 'ebox' ),
								ebox_Custom_Label::get_label( 'quiz' ),
								ebox_Custom_Label::get_label( 'questions' )
							),
							'help_text'      => sprintf(
								// translators: placeholder: questions, quizzes, quiz.
								esc_html_x( 'Share %1$s across multiple %2$s. Progress and statistics are maintained on a per-%3$s basis.', 'placeholder: placeholder: questions, quizzes, quiz', 'ebox' ),
								ebox_get_custom_label_lower( 'questions' ),
								ebox_get_custom_label_lower( 'quizzes' ),
								ebox_get_custom_label_lower( 'quiz' )
							),
							'value'          => $this->setting_option_values['quiz_builder_shared_questions'],
							'options'        => array(
								''    => '',
								'yes' => sprintf(
									// translators: placeholder: questions, quizzes.
									esc_html_x( 'All %1$s can be used across multiple %2$s', 'placeholder: questions, quizzes', 'ebox' ),
									ebox_get_custom_label_lower( 'questions' ),
									ebox_get_custom_label_lower( 'quizzes' )
								),
							),
							'parent_setting' => 'quiz_builder_enabled',
						),
						'force_quiz_builder'            => array(
							'name'  => 'force_quiz_builder',
							'label' => 'force_quiz_builder',
							'type'  => 'hidden',
							'value' => $this->setting_option_values['force_quiz_builder'],
						),
						'force_shared_questions'        => array(
							'name'  => 'force_shared_questions',
							'label' => 'force_shared_questions',
							'type'  => 'hidden',
							'value' => $this->setting_option_values['force_shared_questions'],
						),
					)
				);

				if ( true !== ebox_is_data_upgrade_quiz_questions_updated() ) {
					$this->setting_option_fields['quiz_builder_enabled']['attrs'] = array(
						'disabled' => 'disabled',
					);

					$this->setting_option_fields['quiz_builder_per_page']['attrs']         = array(
						'disabled' => 'disabled',
					);
					$this->setting_option_fields['quiz_builder_shared_questions']['attrs'] = array(
						'disabled' => 'disabled',
					);
				}

				if ( 'yes' === $this->setting_option_values['force_quiz_builder'] ) {
					$this->setting_option_fields['quiz_builder_enabled']['attrs'] = array(
						'disabled' => 'disabled',
					);
				}

				if ( 'yes' === $this->setting_option_values['force_shared_questions'] ) {
					$this->setting_option_fields['quiz_builder_shared_questions']['attrs'] = array(
						'disabled' => 'disabled',
					);
				}
			}

			$time_formats_off_state_text = sprintf(
				// translators: placeholder: Date preview, Time preview, Date format string, Time format string.
				esc_html_x( 'Default format: %1$s %2$s  %3$s %4$s ', '', 'ebox' ),
				date_i18n( get_option( 'date_format' ) ),
				date_i18n( get_option( 'time_format' ) ),
				'<code>' . get_option( 'date_format' ) . '</code>',
				'<code>' . get_option( 'time_format' ) . '</code>'
			);

			$this->setting_option_fields = array_merge(
				$this->setting_option_fields,
				array(
					'quiz_builder_time_formats' => array(
						'name'                => 'quiz_builder_time_formats',
						'type'                => 'checkbox-switch',
						'label'               => sprintf(
							// translators: placeholder: Quiz.
							esc_html_x( 'Custom %s Time Formats', 'placeholder: Quiz', 'ebox' ),
							ebox_get_custom_label( 'quiz' )
						),
						'help_text'           => sprintf(
							// translators: placeholder: Quiz, Quiz.
							esc_html_x( 'Customize the default time format for the %1$s Leaderboard and %2$s Statistics. ', 'placeholder: Quiz, Quiz', 'ebox' ),
							ebox_get_custom_label( 'Quiz' ),
							ebox_get_custom_label( 'Quiz' )
						),
						'value'               => $this->setting_option_values['quiz_builder_time_formats'],
						'options'             => array(
							''    => $time_formats_off_state_text,
							'yes' => '',
						),
						'child_section_state' => ( 'yes' === $this->setting_option_values['quiz_builder_time_formats'] ) ? 'open' : 'closed',
					),
				)
			);

			$wp_date_format      = get_option( 'date_format' );
			$wp_time_format      = get_option( 'time_format' );
			$wp_date_time_format = $wp_date_format . ' ' . $wp_time_format;

			$date_time_formats = array_unique(
				/**
				 * Filters the quiz date and time formats.
				 *
				 * @param array $date_time_formats An array of quiz date and time formats.
				 */
				apply_filters(
					'ebox_quiz_date_time_formats',
					array(
						$wp_date_time_format,
						'd.m.Y H:i',
						'Y/m/d g:i A',
						'Y/m/d \a\t g:i A',
						'Y/m/d \a\t g:ia',
						__( 'M j, Y @ G:i', 'ebox' ),
					)
				)
			);

			if ( ! empty( $date_time_formats ) ) {
				$options = array(
					$wp_date_time_format => '<span class="date-time-text format-i18n">' . date_i18n( $wp_date_time_format ) . '</span><code>' . $wp_date_format . ' ' . $wp_time_format . '</code> - ' . __( 'WordPress default', 'ebox' ),
				);

				foreach ( $date_time_formats as $format ) {
					if ( ! isset( $options[ $format ] ) ) {
						$options[ $format ] = '<span class="date-time-text format-i18n">' . date_i18n( $format ) . '</span><code>' . $format . '</code>';
					}
				}
			}

			if ( ! in_array( $this->setting_option_values['statistics_time_format'], $date_time_formats, true ) ) {
				$options['custom'] = '<span class="date-time-text format-i18n">' . esc_html__( 'Custom', 'ebox' ) . '</span><input type="text" class="-small" name="statistics_time_format_custom" id="statistics_time_format_custom" value="' . esc_attr( $this->setting_option_values['statistics_time_format'] ) . '">';

				$this->setting_option_values['statistics_time_format'] = 'custom';
			} else {
				$options['custom'] = '<span class="date-time-text format-i18n">' . esc_html__( 'Custom', 'ebox' ) . '</span><input type="text" class="-small" name="statistics_time_format_custom" id="statistics_time_format_custom" value="">';
			}

			$this->setting_option_fields['statistics_time_format'] = array(
				'name'           => 'statistics_time_format',
				'type'           => 'radio',
				'label'          => esc_html__( 'Statistic time format ', 'ebox' ),
				'help_text'      => esc_html__( 'Statistic time format ', 'ebox' ),
				'default'        => $wp_date_time_format,
				'value'          => $this->setting_option_values['statistics_time_format'],
				'options'        => $options,
				'parent_setting' => 'quiz_builder_time_formats',
			);

			if ( ! in_array( $this->setting_option_values['toplist_time_format'], $date_time_formats, true ) ) {
				$options['custom'] = '<span class="date-time-text format-i18n">' . esc_html__( 'Custom', 'ebox' ) . '</span><input type="text" class="-small" name="toplist_date_format_custom" id="toplist_time_format_custom" value="' . esc_attr( $this->setting_option_values['toplist_time_format'] ) . '">';

				$this->setting_option_values['toplist_time_format'] = 'custom';
			} else {
				$options['custom'] = '<span class="date-time-text format-i18n">' . esc_html__( 'Custom', 'ebox' ) . '</span><input type="text" class="-small" name="toplist_date_format_custom" id="toplist_time_format_custom" value="">';

			}

			$this->setting_option_fields['toplist_time_format'] = array(
				'name'           => 'toplist_time_format',
				'type'           => 'radio',
				'label'          => esc_html__( 'Leaderboard time format', 'ebox' ),
				'help_text'      => esc_html__( 'Leaderboard time format', 'ebox' ),
				'default'        => $wp_date_time_format,
				'value'          => $this->setting_option_values['toplist_time_format'],
				'options'        => $options,
				'parent_setting' => 'quiz_builder_time_formats',
			);

			$this->setting_option_fields['quiz_template'] = array(
				'name'        => 'quiz_template',
				'type'        => 'select-edit-delete',
				'label'       => sprintf(
					// translators: placeholder: Quiz.
					esc_html_x( '%s Template Management', 'placeholder: Quiz', 'ebox' ),
					ebox_Custom_Label::get_label( 'quiz' )
				),
				'help_text'   => esc_html__( 'Select a template to update or delete the title.', 'ebox' ),
				'value'       => '',
				'placeholder' => esc_html__( 'Select a template', 'ebox' ),
				'options'     => $this->setting_option_values['quiz_templates'],
				'buttons'     => array(
					'delete' => esc_html__( 'Delete', 'ebox' ),
					'update' => esc_html__( 'Update', 'ebox' ),
				),
			);

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
			$this->setting_option_fields = apply_filters( 'ebox_settings_fields', $this->setting_option_fields, $this->settings_section_key );

			parent::load_settings_fields();
		}

		/**
		 * Intercept the WP options save logic and check that we have a valid nonce.
		 *
		 * @since 3.0.0
		 *
		 * @param array  $current_values Array of section fields values.
		 * @param array  $old_values     Array of old values.
		 * @param string $option         Section option key should match $this->setting_option_key.
		 */
		public function section_pre_update_option( $current_values = '', $old_values = '', $option = '' ) {
			if ( $option === $this->setting_option_key ) {
				$current_values = parent::section_pre_update_option( $current_values, $old_values, $option );
				if ( $current_values !== $old_values ) {
					if ( ( isset( $current_values['quiz_builder_enabled'] ) ) && ( 'yes' === $current_values['quiz_builder_enabled'] ) ) {
						$current_values['quiz_builder_per_page'] = absint( $current_values['quiz_builder_per_page'] );
					} else {
						$current_values['quiz_builder_shared_questions'] = '';
						$current_values['quiz_builder_per_page']         = ebox_LMS_DEFAULT_WIDGET_PER_PAGE;
					}

					$wp_date_format      = get_option( 'date_format' );
					$wp_time_format      = get_option( 'time_format' );
					$wp_date_time_format = $wp_date_format . ' ' . $wp_time_format;

					if ( ( isset( $current_values['quiz_builder_time_formats'] ) ) && ( 'yes' === $current_values['quiz_builder_time_formats'] ) ) {
						if ( ( isset( $current_values['statistics_time_format'] ) ) && ( 'custom' === $current_values['statistics_time_format'] ) ) {
							// phpcs:ignore WordPress.Security.NonceVerification.Missing -- POST nonce verification takes place in parent::verify_metabox_nonce_field().
							if ( ( isset( $_POST['statistics_time_format_custom'] ) ) && ( ! empty( $_POST['statistics_time_format_custom'] ) ) ) {
								// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
								$current_values['statistics_time_format'] = esc_attr( stripslashes( $_POST['statistics_time_format_custom'] ) );
							} else {
								$current_values['statistics_time_format'] = '';
							}
						}

						if ( $wp_date_time_format === $current_values['statistics_time_format'] ) {
							$current_values['statistics_time_format'] = '';
						}

						if ( ( isset( $current_values['toplist_time_format'] ) ) && ( 'custom' === $current_values['toplist_time_format'] ) ) {
							// phpcs:ignore WordPress.Security.NonceVerification.Missing
							if ( ( isset( $_POST['toplist_date_format_custom'] ) ) && ( ! empty( $_POST['toplist_date_format_custom'] ) ) ) {
								// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
								$current_values['toplist_time_format'] = esc_attr( stripslashes( $_POST['toplist_date_format_custom'] ) );
							} else {
								$current_values['toplist_time_format'] = '';
							}
						}

						if ( $wp_date_time_format === $current_values['toplist_time_format'] ) {
							$current_values['toplist_time_format'] = '';
						}
					} else {
						$current_values['statistics_time_format'] = '';
						$current_values['toplist_time_format']    = '';
					}
				}
			}

			return $current_values;
		}

		/**
		 * This function handles the AJAX actions from the browser.
		 *
		 * @since 3.0.0
		 */
		public function ajax_action() {
			$reply_data = array( 'status' => false );

			if ( current_user_can( 'wpProQuiz_edit_quiz' ) ) {
				if ( ( isset( $_POST['field_nonce'] ) ) && ( ! empty( $_POST['field_nonce'] ) ) && ( isset( $_POST['field_key'] ) ) && ( ! empty( $_POST['field_key'] ) ) && ( wp_verify_nonce( esc_attr( $_POST['field_nonce'] ), $_POST['field_key'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

					if ( isset( $_POST['field_action'] ) ) {
						if ( 'update' === $_POST['field_action'] ) {
							if ( ( isset( $_POST['field_value'] ) ) && ( ! empty( $_POST['field_value'] ) ) && ( isset( $_POST['field_text'] ) ) && ( ! empty( $_POST['field_text'] ) ) ) {
								$template_id       = intval( $_POST['field_value'] );
								$template_new_name = esc_attr( $_POST['field_text'] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

								$template_mapper = new WpProQuiz_Model_TemplateMapper();
								$template        = $template_mapper->fetchById( $template_id );
								if ( ( $template ) && ( is_a( $template, 'WpProQuiz_Model_Template' ) ) ) {
									$template_current_name = $template->getName();
									if ( $template_current_name !== $template_new_name ) {
										$update_ret = $template_mapper->updateName( $template_id, $template_new_name );
										if ( $update_ret ) {
											$reply_data['status']  = true;
											$reply_data['message'] = '<span style="color: green" >' . __( 'Template updated.', 'ebox' ) . '</span>';
										}
									}
								}
							}
						} elseif ( 'delete' === $_POST['field_action'] ) {
							if ( ( isset( $_POST['field_value'] ) ) && ( ! empty( $_POST['field_value'] ) ) ) {
								$template_id = intval( $_POST['field_value'] );

								$template_mapper = new WpProQuiz_Model_TemplateMapper();
								$template        = $template_mapper->fetchById( $template_id );
								if ( ( $template ) && ( is_a( $template, 'WpProQuiz_Model_Template' ) ) ) {
									$update_ret = $template_mapper->delete( $template_id );
									if ( $update_ret ) {
										$reply_data['status']  = true;
										$reply_data['message'] = '<span style="color: green" >' . __( 'Template deleted.', 'ebox' ) . '</span>';
									}
								}
							}
						}
					}
				}
			}

			if ( ! empty( $reply_data ) ) {
				echo wp_json_encode( $reply_data );
			}

			wp_die(); // This is required to terminate immediately and return a proper response.

		}

		// End of functions.
	}
}
add_action(
	'ebox_settings_sections_init',
	function() {
		ebox_Settings_Quizzes_Management_Display::add_section_instance();
	}
);
