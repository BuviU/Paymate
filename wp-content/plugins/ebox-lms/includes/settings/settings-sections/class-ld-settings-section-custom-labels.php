<?php
/**
 * ebox Settings Section for Custom Labels Metabox.
 *
 * @since 2.4.0
 * @package ebox\Settings\Sections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Section' ) ) && ( ! class_exists( 'ebox_Settings_Section_Custom_Labels' ) ) ) {
	/**
	 * Class ebox Settings Section for Custom Labels Metabox.
	 *
	 * @since 2.4.0
	 */
	class ebox_Settings_Section_Custom_Labels extends ebox_Settings_Section {

		/**
		 * Protected constructor for class
		 *
		 * @since 2.4.0
		 */
		protected function __construct() {
			$this->settings_page_id = 'ebox_lms_advanced';

			// This is the 'option_name' key used in the wp_options table.
			$this->setting_option_key = 'ebox_settings_custom_labels';

			// This is the HTML form field prefix used.
			$this->setting_field_prefix = 'ebox_settings_custom_labels';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_section_key = 'settings_custom_labels';

			// Section label/header.
			$this->settings_section_label = esc_html__( 'Custom Labels', 'ebox' );

			$this->reset_confirm_message = esc_html__( 'Are you sure want to reset the custom labels?', 'ebox' );

			parent::__construct();
		}

		/**
		 * Initialize the metabox settings values.
		 *
		 * @since 2.4.0
		 */
		public function load_settings_values() {
			parent::load_settings_values();

			if ( false === $this->setting_option_values ) {
				$this->setting_option_values = get_option( 'ebox_custom_label_settings' );
			}

			if ( ( isset( $_GET['action'] ) ) && ( 'ld_reset_settings' === $_GET['action'] ) && ( isset( $_GET['page'] ) ) && ( $_GET['page'] === $this->settings_page_id ) ) {
				if ( ( isset( $_GET['ld_wpnonce'] ) ) && ( ! empty( $_GET['ld_wpnonce'] ) ) ) {
					if ( wp_verify_nonce( $_GET['ld_wpnonce'], get_current_user_id() . '-' . $this->setting_option_key ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
						if ( ! empty( $this->setting_option_values ) ) {
							foreach ( $this->setting_option_values as $key => $val ) {
								$this->setting_option_values[ $key ] = '';
							}
							$this->save_settings_values();
						}

						$reload_url = remove_query_arg( array( 'action', 'ld_wpnonce' ) );
						ebox_safe_redirect( $reload_url );
					}
				}
			}
		}

		/**
		 * Initialize the metabox settings fields.
		 *
		 * @since 2.4.0
		 */
		public function load_settings_fields() {

			$this->setting_option_fields = array(
				'course'                                 => array(
					'name'      => 'course',
					'type'      => 'text',
					'label'     => esc_html__( 'Course', 'ebox' ),
					'help_text' => esc_html__( 'Label to replace "course" (singular).', 'ebox' ),
					'value'     => isset( $this->setting_option_values['course'] ) ? $this->setting_option_values['course'] : '',
					'class'     => 'regular-text',
				),
				'courses'                                => array(
					'name'      => 'courses',
					'type'      => 'text',
					'label'     => esc_html__( 'Courses', 'ebox' ),
					'help_text' => esc_html__( 'Label to replace "courses" (plural).', 'ebox' ),
					'value'     => isset( $this->setting_option_values['courses'] ) ? $this->setting_option_values['courses'] : '',
					'class'     => 'regular-text',
				),
				'lesson'                                 => array(
					'name'      => 'lesson',
					'type'      => 'text',
					'label'     => esc_html__( 'Lesson', 'ebox' ),
					'help_text' => esc_html__( 'Label to replace "lesson" (singular).', 'ebox' ),
					'value'     => isset( $this->setting_option_values['lesson'] ) ? $this->setting_option_values['lesson'] : '',
					'class'     => 'regular-text',
				),
				'modules'                                => array(
					'name'      => 'modules',
					'type'      => 'text',
					'label'     => esc_html__( 'modules', 'ebox' ),
					'help_text' => esc_html__( 'Label to replace "modules" (plural).', 'ebox' ),
					'value'     => isset( $this->setting_option_values['modules'] ) ? $this->setting_option_values['modules'] : '',
					'class'     => 'regular-text',
				),
				'topic'                                  => array(
					'name'      => 'topic',
					'type'      => 'text',
					'label'     => esc_html__( 'Topic', 'ebox' ),
					'help_text' => esc_html__( 'Label to replace "topic" (singular).', 'ebox' ),
					'value'     => isset( $this->setting_option_values['topic'] ) ? $this->setting_option_values['topic'] : '',
					'class'     => 'regular-text',
				),
				'topics'                                 => array(
					'name'      => 'topics',
					'type'      => 'text',
					'label'     => esc_html__( 'Topics', 'ebox' ),
					'help_text' => esc_html__( 'Label to replace "topics" (plural).', 'ebox' ),
					'value'     => isset( $this->setting_option_values['topics'] ) ? $this->setting_option_values['topics'] : '',
					'class'     => 'regular-text',
				),
				'quiz'                                   => array(
					'name'      => 'quiz',
					'type'      => 'text',
					'label'     => esc_html__( 'Quiz', 'ebox' ),
					'help_text' => esc_html__( 'Label to replace "quiz" (singular).', 'ebox' ),
					'value'     => isset( $this->setting_option_values['quiz'] ) ? $this->setting_option_values['quiz'] : '',
					'class'     => 'regular-text',
				),
				'quizzes'                                => array(
					'name'      => 'quizzes',
					'type'      => 'text',
					'label'     => esc_html__( 'Quizzes', 'ebox' ),
					'help_text' => esc_html__( 'Label to replace "quizzes" (plural).', 'ebox' ),
					'value'     => isset( $this->setting_option_values['quizzes'] ) ? $this->setting_option_values['quizzes'] : '',
					'class'     => 'regular-text',
				),
				'question'                               => array(
					'name'      => 'question',
					'type'      => 'text',
					'label'     => esc_html__( 'Question', 'ebox' ),
					'help_text' => esc_html__( 'Label to replace "question" (singular).', 'ebox' ),
					'value'     => isset( $this->setting_option_values['question'] ) ? $this->setting_option_values['question'] : '',
					'class'     => 'regular-text',
				),
				'questions'                              => array(
					'name'      => 'questions',
					'type'      => 'text',
					'label'     => esc_html__( 'Questions', 'ebox' ),
					'help_text' => esc_html__( 'Label to replace "questions" (plural).', 'ebox' ),
					'value'     => isset( $this->setting_option_values['questions'] ) ? $this->setting_option_values['questions'] : '',
					'class'     => 'regular-text',
				),
				'team'                                  => array(
					'name'      => 'team',
					'type'      => 'text',
					'label'     => esc_html__( 'Team', 'ebox' ),
					'help_text' => esc_html__( 'Label to replace "team" (singular).', 'ebox' ),
					'value'     => isset( $this->setting_option_values['team'] ) ? $this->setting_option_values['team'] : '',
					'class'     => 'regular-text',
				),
				'teams'                                 => array(
					'name'      => 'teams',
					'type'      => 'text',
					'label'     => esc_html__( 'Teams', 'ebox' ),
					'help_text' => esc_html__( 'Label to replace "teams" (plural).', 'ebox' ),
					'value'     => isset( $this->setting_option_values['teams'] ) ? $this->setting_option_values['teams'] : '',
					'class'     => 'regular-text',
				),
				'team_leader'                           => array(
					'name'      => 'team_leader',
					'type'      => 'text',
					'label'     => esc_html__( 'Team Leader', 'ebox' ),
					'help_text' => esc_html__( 'Label to rename Team Leader user role.', 'ebox' ),
					'value'     => isset( $this->setting_option_values['team_leader'] ) ? $this->setting_option_values['team_leader'] : '',
					'class'     => 'regular-text',
				),
				'exam'                                   => array(
					'name'      => 'exam',
					'type'      => 'text',
					'label'     => esc_html__( 'Challenge Exam', 'ebox' ),
					'help_text' => esc_html__( 'Label to replace "challenge exam" (singular).', 'ebox' ),
					'value'     => $this->setting_option_values['exam'] ?? '',
					'class'     => 'regular-text',
				),
				'exams'                                  => array(
					'name'      => 'exams',
					'type'      => 'text',
					'label'     => esc_html__( 'Challenge Exams', 'ebox' ),
					'help_text' => esc_html__( 'Label to replace "challenge exams" (plural).', 'ebox' ),
					'value'     => $this->setting_option_values['exams'] ?? '',
					'class'     => 'regular-text',
				),
				'button_take_this_course'                => array(
					'name'      => 'button_take_this_course',
					'type'      => 'text',
					'label'     => esc_html__( 'Take this Course (Button)', 'ebox' ),
					'help_text' => esc_html__( 'Label to replace "Take this Course" button.', 'ebox' ),
					'value'     => isset( $this->setting_option_values['button_take_this_course'] ) ? $this->setting_option_values['button_take_this_course'] : '',
					'class'     => 'regular-text',
				),
				'button_take_this_team'                 => array(
					'name'      => 'button_take_this_team',
					'type'      => 'text',
					'label'     => esc_html__( 'Join Team (Button)', 'ebox' ),
					'help_text' => esc_html__( 'Label to replace "Join Team" button.', 'ebox' ),
					'value'     => isset( $this->setting_option_values['button_take_this_team'] ) ? $this->setting_option_values['button_take_this_team'] : '',
					'class'     => 'regular-text',
				),
				'button_mark_complete'                   => array(
					'name'      => 'button_mark_complete',
					'type'      => 'text',
					'label'     => esc_html__( 'Mark Complete (Button)', 'ebox' ),
					'help_text' => esc_html__( 'Label to replace "Mark Complete" button.', 'ebox' ),
					'value'     => isset( $this->setting_option_values['button_mark_complete'] ) ? $this->setting_option_values['button_mark_complete'] : '',
					'class'     => 'regular-text',
				),
				'button_mark_incomplete'                 => array(
					'name'      => 'button_mark_incomplete',
					'type'      => 'text',
					'label'     => esc_html__( 'Mark Incomplete (Button)', 'ebox' ),
					'help_text' => esc_html__( 'Label to replace "Mark Incomplete" button.', 'ebox' ),
					'value'     => isset( $this->setting_option_values['button_mark_incomplete'] ) ? $this->setting_option_values['button_mark_incomplete'] : '',
					'class'     => 'regular-text',
				),
				'button_click_here_to_continue'          => array(
					'name'      => 'button_click_here_to_continue',
					'type'      => 'text',
					'label'     => esc_html__( 'Click Here to Continue (Button)', 'ebox' ),
					'help_text' => esc_html__( 'Label to replace "Click Here to Continue" button.', 'ebox' ),
					'value'     => isset( $this->setting_option_values['button_click_here_to_continue'] ) ? $this->setting_option_values['button_click_here_to_continue'] : '',
					'class'     => 'regular-text',
				),
				'transaction'                            => array(
					'name'      => 'transaction',
					'type'      => 'text',
					'label'     => esc_html__( 'Transaction', 'ebox' ),
					'help_text' => esc_html__( 'Label to replace "transaction" (singular).', 'ebox' ),
					'value'     => isset( $this->setting_option_values['transaction'] ) ? $this->setting_option_values['transaction'] : '',
					'class'     => 'regular-text',
				),
				'transactions'                           => array(
					'name'      => 'transactions',
					'type'      => 'text',
					'label'     => esc_html__( 'Transactions', 'ebox' ),
					'help_text' => esc_html__( 'Label to replace "transactions" (plural).', 'ebox' ),
					'value'     => isset( $this->setting_option_values['transactions'] ) ? $this->setting_option_values['transactions'] : '',
					'class'     => 'regular-text',
				),
			);
			// Legacy custom labels filter.
			/**
			 * Filters custom labels setting fields.
			 *
			 * @param array $setting_option_fields Associative array of Setting field details like name,type,label,value.
			 */
			$this->setting_option_fields = apply_filters( 'ebox_custom_label_fields', $this->setting_option_fields );

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
			$this->setting_option_fields = apply_filters( 'ebox_settings_fields', $this->setting_option_fields, $this->settings_section_key );

			parent::load_settings_fields();
		}

		/**
		 * Changes team_leader role display name
		 *
		 * @since 2.4.0
		 *
		 * @param array  $new_values         Array of section fields values.
		 * @param array  $old_values         Array of old values.
		 * @param string $setting_option_key Section option key should match $this->setting_option_key.
		 */
		public function section_pre_update_option( $new_values = '', $old_values = '', $setting_option_key = '' ) {
			if ( $setting_option_key === $this->setting_option_key ) {
				$new_values = parent::section_pre_update_option( $new_values, $old_values, $setting_option_key );

				if ( ! isset( $new_values['team_leader'] ) ) {
					$new_values['team_leader'] = '';
				}

				if ( ! isset( $old_values['team_leader'] ) ) {
					$old_values['team_leader'] = '';
				}

				if ( empty( $new_values['team_leader'] ) ) {
					$new_values['team_leader'] = esc_html__( 'Team Leader', 'ebox' );
				}
				if ( $old_values['team_leader'] !== $new_values['team_leader'] ) {
					$team_leader = get_role( 'team_leader' );
					if ( ! is_null( $team_leader ) ) {
						remove_role( 'team_leader' );
						add_role( 'team_leader', $new_values['team_leader'], $team_leader->capabilities );
					}
				}
			}

			return $new_values;

		}
	}
}
add_action(
	'ebox_settings_sections_init',
	function() {
		ebox_Settings_Section_Custom_Labels::add_section_instance();
	}
);
