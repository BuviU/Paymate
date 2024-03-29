<?php
/**
 * ebox Settings Section Question Management and Display.
 *
 * @since 3.0.0
 * @package ebox\Settings\Sections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Section' ) ) && ( ! class_exists( 'ebox_Settings_Questions_Management_Display' ) ) ) {
	/**
	 * Class ebox Settings Section Question Management and Display.
	 *
	 * @since 3.0.0
	 */
	class ebox_Settings_Questions_Management_Display extends ebox_Settings_Section {

		/**
		 * Protected constructor for class
		 *
		 * @since 3.0.0
		 */
		protected function __construct() {
			// What screen ID are we showing on.
			$this->settings_screen_id = 'ebox-question_page_questions-options';

			// The page ID (different than the screen ID).
			$this->settings_page_id = 'questions-options';

			// This is the 'option_name' key used in the wp_options table.
			$this->setting_option_key = 'ebox_settings_questions_management_display';

			// This is the HTML form field prefix used.
			$this->setting_field_prefix = 'ebox_settings_questions_management_display';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_section_key = 'questions_management_display';

			// Section label/header.
			$this->settings_section_label = sprintf(
				// translators: placeholder: Quiz.
				esc_html_x( 'Global %s Management & Display Settings', 'placeholder: Question', 'ebox' ),
				ebox_Custom_Label::get_label( 'question' )
			);

			$this->settings_section_description = sprintf(
				// translators: placeholder: questions.
				esc_html_x( 'Control which templates can be used to better organize your ebox %s.', 'placeholder: questions', 'ebox' ),
				ebox_get_custom_label_lower( 'questions' )
			);

			parent::__construct();

			// Hook to handle the AJAX delete/update actions.
			add_action( 'wp_ajax_' . $this->setting_field_prefix, array( $this, 'ajax_action' ) );
		}

		/**
		 * Load the field settings values
		 *
		 * @since 3.0.0
		 */
		public function load_settings_values() {
			parent::load_settings_values();

			$this->setting_option_values = array(
				'question_templates' => array(
					'' => __( 'Select a template', 'ebox' ),
				),
			);

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( ( is_admin() ) && ( isset( $_GET['page'] ) ) && ( 'questions-options' === $_GET['page'] ) ) {
				$template_mapper    = new WpProQuiz_Model_TemplateMapper();
				$question_templates = $template_mapper->fetchAll( WpProQuiz_Model_Template::TEMPLATE_TYPE_QUESTION, false );
				if ( ( ! empty( $question_templates ) ) && ( is_array( $question_templates ) ) ) {
					$_templates = array();
					foreach ( $question_templates as $template_question ) {
						$template_name = $template_question->getName();
						$template_id   = $template_question->getTemplateId();

						if ( ( ! empty( $template_name ) ) && ( ! isset( $_templates[ $template_id ] ) ) ) {
							$_templates[ $template_id ] = esc_html( $template_name );
						}
					}

					asort( $_templates );

					$this->setting_option_values['question_templates'] += $_templates;
				}
			}
		}

		/**
		 * Load the field settings fields
		 *
		 * @since 3.0.0
		 */
		public function load_settings_fields() {
			$this->setting_option_fields = array(
				'question_template' => array(
					'name'      => 'question_template',
					'type'      => 'select-edit-delete',
					'label'     => sprintf(
						// translators: placeholder: Question.
						esc_html_x( '%s templates', 'placeholder: Question', 'ebox' ),
						ebox_Custom_Label::get_label( 'question' )
					),
					'help_text' => sprintf(
						// translators: placeholder: Question.
						esc_html_x( 'Manage %s templates. Select a template then update the title or delete.', 'placeholder: Question', 'ebox' ),
						ebox_Custom_Label::get_label( 'question' )
					),
					'value'     => '',
					'options'   => $this->setting_option_values['question_templates'],
					'buttons'   => array(
						'delete' => esc_html__( 'Delete', 'ebox' ),
						'update' => esc_html__( 'Update', 'ebox' ),
					),
				),
			);

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
			$this->setting_option_fields = apply_filters( 'ebox_settings_fields', $this->setting_option_fields, $this->settings_section_key );

			parent::load_settings_fields();
		}

		/**
		 * This function handles the AJAX actions from the browser.
		 *
		 * @since 3.0.0
		 */
		public function ajax_action() {
			$reply_data = array( 'status' => false );

			if ( current_user_can( 'wpProQuiz_edit_quiz' ) ) {
				if ( ( isset( $_POST['field_nonce'] ) ) && ( ! empty( $_POST['field_nonce'] ) ) && ( isset( $_POST['field_key'] ) ) && ( ! empty( $_POST['field_key'] ) ) && ( wp_verify_nonce( esc_attr( $_POST['field_nonce'] ), $_POST['field_key'] ) ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

					if ( isset( $_POST['field_action'] ) ) {
						if ( 'update' === $_POST['field_action'] ) {
							if ( ( isset( $_POST['field_value'] ) ) && ( ! empty( $_POST['field_value'] ) ) && ( isset( $_POST['field_text'] ) ) && ( ! empty( $_POST['field_text'] ) ) ) {
								$template_id       = intval( $_POST['field_value'] );
								$template_new_name = esc_attr( $_POST['field_text'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

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
		ebox_Settings_Questions_Management_Display::add_section_instance();
	}
);
