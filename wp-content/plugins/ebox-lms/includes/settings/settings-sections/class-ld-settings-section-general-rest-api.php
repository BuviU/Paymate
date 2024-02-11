<?php
/**
 * ebox Settings Section for REST API Metabox.
 *
 * @since 2.5.8
 * @package ebox\Settings\Sections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Section' ) ) && ( ! class_exists( 'ebox_Settings_Section_General_REST_API' ) ) ) {
	/**
	 * Class ebox Settings Section for REST API Metabox.
	 *
	 * @since 2.5.8
	 */
	class ebox_Settings_Section_General_REST_API extends ebox_Settings_Section {

		/**
		 * Setting Option Fields REST API V1
		 *
		 * @var array
		 */
		protected $setting_option_fields_v1 = array();

		/**
		 * Setting Option Fields REST API V2
		 *
		 * @var array
		 */
		protected $setting_option_fields_v2 = array();

		/**
		 * Protected constructor for class
		 *
		 * @since 2.5.8
		 */
		protected function __construct() {
			$this->settings_page_id = 'ebox_lms_advanced';

			// This is the 'option_name' key used in the wp_options table.
			$this->setting_option_key = 'ebox_settings_rest_api';

			// This is the HTML form field prefix used.
			$this->setting_field_prefix = 'ebox_settings_rest_api';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_section_key = 'settings_rest_api';

			// Section label/header.
			$this->settings_section_label     = esc_html__( 'REST API Settings', 'ebox' );
			$this->settings_section_sub_label = esc_html__( 'REST API', 'ebox' );

			$this->settings_section_description = esc_html__( 'Control and customize the REST API endpoints.', 'ebox' );

			add_filter( 'ebox_settings_row_outside_after', array( $this, 'ebox_settings_row_outside_after' ), 10, 2 );
			add_filter( 'ebox_settings_row_outside_before', array( $this, 'ebox_settings_row_outside_before' ), 30, 2 );

			parent::__construct();
		}

		/**
		 * Initialize the metabox settings values.
		 *
		 * @since 2.5.8
		 */
		public function load_settings_values() {
			parent::load_settings_values();

			if ( ! isset( $this->setting_option_values['enabled'] ) ) {
				$this->setting_option_values['enabled'] = 'yes';
			}

			// V1 Endpoint values.
			if ( ( ! isset( $this->setting_option_values['ebox-courses'] ) ) || ( empty( $this->setting_option_values['ebox-courses'] ) ) ) {
				$this->setting_option_values['ebox-courses'] = ebox_get_post_type_slug( 'course' );
			}

			if ( ( ! isset( $this->setting_option_values['ebox-modules'] ) ) || ( empty( $this->setting_option_values['ebox-modules'] ) ) ) {
				$this->setting_option_values['ebox-modules'] = ebox_get_post_type_slug( 'lesson' );
			}

			if ( ( ! isset( $this->setting_option_values['ebox-topic'] ) ) || ( empty( $this->setting_option_values['ebox-topic'] ) ) ) {
				$this->setting_option_values['ebox-topic'] = ebox_get_post_type_slug( 'topic' );
			}

			if ( ( ! isset( $this->setting_option_values['ebox-quiz'] ) ) || ( empty( $this->setting_option_values['ebox-quiz'] ) ) ) {
				$this->setting_option_values['ebox-quiz'] = ebox_get_post_type_slug( 'quiz' );
			}

			if ( ( ! isset( $this->setting_option_values['ebox-question'] ) ) || ( empty( $this->setting_option_values['ebox-question'] ) ) ) {
				$this->setting_option_values['ebox-question'] = ebox_get_post_type_slug( 'question' );
			}

			if ( ( ! isset( $this->setting_option_values['users'] ) ) || ( empty( $this->setting_option_values['users'] ) ) ) {
				$this->setting_option_values['users'] = 'users';
			}

			if ( ( ! isset( $this->setting_option_values['teams'] ) ) || ( empty( $this->setting_option_values['teams'] ) ) ) {
				$this->setting_option_values['teams'] = ebox_get_post_type_slug( 'team' );
			}

			// V2 Endpoint values.

			if ( ( ! isset( $this->setting_option_values['courses_v2'] ) ) || ( empty( $this->setting_option_values['courses_v2'] ) ) ) {
				$this->setting_option_values['courses_v2'] = ebox_get_post_type_slug( 'course' );
			}

			if ( ( ! isset( $this->setting_option_values['courses-users_v2'] ) ) || ( empty( $this->setting_option_values['courses-users_v2'] ) ) ) {
				$this->setting_option_values['courses-users_v2'] = 'users';
			}
			if ( ( ! isset( $this->setting_option_values['courses-steps_v2'] ) ) || ( empty( $this->setting_option_values['courses-steps_v2'] ) ) ) {
				$this->setting_option_values['courses-steps_v2'] = 'steps';
			}
			if ( ( ! isset( $this->setting_option_values['courses-teams_v2'] ) ) || ( empty( $this->setting_option_values['courses-teams_v2'] ) ) ) {
				$this->setting_option_values['courses-teams_v2'] = 'teams';
			}
			if ( ( ! isset( $this->setting_option_values['courses-prerequisites_v2'] ) ) || ( empty( $this->setting_option_values['courses-prerequisites_v2'] ) ) ) {
				$this->setting_option_values['courses-prerequisites_v2'] = 'prerequisites';
			}

			if ( ( ! isset( $this->setting_option_values['modules_v2'] ) ) || ( empty( $this->setting_option_values['modules_v2'] ) ) ) {
				$this->setting_option_values['modules_v2'] = ebox_get_post_type_slug( 'lesson' );
			}

			if ( ( ! isset( $this->setting_option_values['topics_v2'] ) ) || ( empty( $this->setting_option_values['topics_v2'] ) ) ) {
				$this->setting_option_values['topics_v2'] = ebox_get_post_type_slug( 'topic' );
			}

			if ( ( ! isset( $this->setting_option_values['quizzes_v2'] ) ) || ( empty( $this->setting_option_values['quizzes_v2'] ) ) ) {
				$this->setting_option_values['quizzes_v2'] = ebox_get_post_type_slug( 'quiz' );
			}

			if ( ( ! isset( $this->setting_option_values['questions_v2'] ) ) || ( empty( $this->setting_option_values['questions_v2'] ) ) ) {
				$this->setting_option_values['questions_v2'] = ebox_get_post_type_slug( 'question' );
			}

			if ( ( ! isset( $this->setting_option_values['quizzes-form-entries_v2'] ) ) || ( empty( $this->setting_option_values['quizzes-form-entries_v2'] ) ) ) {
				$this->setting_option_values['quizzes-form-entries_v2'] = 'form-entries';
			}

			if ( ( ! isset( $this->setting_option_values['quizzes-statistics_v2'] ) ) || ( empty( $this->setting_option_values['quizzes-statistics_v2'] ) ) ) {
				$this->setting_option_values['quizzes-statistics_v2'] = 'statistics';
			}

			if ( ( ! isset( $this->setting_option_values['quizzes-statistics-questions_v2'] ) ) || ( empty( $this->setting_option_values['quizzes-statistics-questions_v2'] ) ) ) {
				$this->setting_option_values['quizzes-statistics-questions_v2'] = 'questions';
			}

			if ( ( ! isset( $this->setting_option_values['teams_v2'] ) ) || ( empty( $this->setting_option_values['teams_v2'] ) ) ) {
				$this->setting_option_values['teams_v2'] = ebox_get_post_type_slug( 'team' );
			}
			if ( ( ! isset( $this->setting_option_values['teams-leaders_v2'] ) ) || ( empty( $this->setting_option_values['teams-leaders_v2'] ) ) ) {
				$this->setting_option_values['teams-leaders_v2'] = 'leaders';
			}
			if ( ( ! isset( $this->setting_option_values['teams-courses_v2'] ) ) || ( empty( $this->setting_option_values['teams-courses_v2'] ) ) ) {
				$this->setting_option_values['teams-courses_v2'] = 'courses';
			}
			if ( ( ! isset( $this->setting_option_values['teams-users_v2'] ) ) || ( empty( $this->setting_option_values['teams-users_v2'] ) ) ) {
				$this->setting_option_values['teams-users_v2'] = 'users';
			}

			$this->setting_option_values['exams_v2'] = $this->setting_option_values['exams_v2'] ?? 'exams';

			if ( ( ! isset( $this->setting_option_values['assignments_v2'] ) ) || ( empty( $this->setting_option_values['assignments_v2'] ) ) ) {
				$this->setting_option_values['assignments_v2'] = ebox_get_post_type_slug( 'assignment' );
			}

			if ( ( ! isset( $this->setting_option_values['essays_v2'] ) ) || ( empty( $this->setting_option_values['essays_v2'] ) ) ) {
				$this->setting_option_values['essays_v2'] = ebox_get_post_type_slug( 'essay' );
			}

			if ( ( ! isset( $this->setting_option_values['users_v2'] ) ) || ( empty( $this->setting_option_values['users_v2'] ) ) ) {
				$this->setting_option_values['users_v2'] = 'users';
			}
			if ( ( ! isset( $this->setting_option_values['users-courses_v2'] ) ) || ( empty( $this->setting_option_values['users-courses_v2'] ) ) ) {
				$this->setting_option_values['users-courses_v2'] = 'courses';
			}
			if ( ( ! isset( $this->setting_option_values['users-teams_v2'] ) ) || ( empty( $this->setting_option_values['users-teams_v2'] ) ) ) {
				$this->setting_option_values['users-teams_v2'] = 'teams';
			}
			if ( ( ! isset( $this->setting_option_values['users-course-progress_v2'] ) ) || ( empty( $this->setting_option_values['users-course-progress_v2'] ) ) ) {
				$this->setting_option_values['users-course-progress_v2'] = 'course-progress';
			}
			if ( ( ! isset( $this->setting_option_values['users-quiz-progress_v2'] ) ) || ( empty( $this->setting_option_values['users-quiz-progress_v2'] ) ) ) {
				$this->setting_option_values['users-quiz-progress_v2'] = 'quiz-progress';
			}

			if ( ( ! isset( $this->setting_option_values['progress-status_v2'] ) ) || ( empty( $this->setting_option_values['progress-status_v2'] ) ) ) {
				$this->setting_option_values['progress-status_v2'] = 'progress-status';
			}
			if ( ( ! isset( $this->setting_option_values['price-types_v2'] ) ) || ( empty( $this->setting_option_values['price-types_v2'] ) ) ) {
				$this->setting_option_values['price-types_v2'] = 'price-types';
			}
			if ( ( ! isset( $this->setting_option_values['question-types_v2'] ) ) || ( empty( $this->setting_option_values['question-types_v2'] ) ) ) {
				$this->setting_option_values['question-types_v2'] = 'question-types';
			}

			$this->setting_option_values = apply_filters( 'ebox_rest_settings_values', $this->setting_option_values );
		}

		/**
		 * Initialize the metabox settings fields.
		 *
		 * @since 2.5.8
		 */
		public function load_settings_fields() {

			$this->setting_option_fields = array(
				'enabled' => array(
					'name'      => 'enabled',
					'type'      => 'hidden',
					'label'     => esc_html__( 'Enabled REST API Active Version', 'ebox' ),
					'help_text' => esc_html__( 'Customize the ebox REST API namespace and endpoints. Leave text fields blank to revert to default.', 'ebox' ),
					'value'     => 'yes',
					'options'   => array(
						'yes' => array(
							'label'       => '',
							'description' => '',
							'tooltip'     => esc_html__( 'REST API must be enabled', 'ebox' ),
						),
					),
					'attrs'     => array(
						'disabled' => 'disabled',
					),
				),
			);

			$site_rest_url    = get_rest_url();
			$site_rest_prefix = rest_get_url_prefix();
			$value_prefix_top = rest_get_url_prefix() . '/' . ebox_REST_API_NAMESPACE . '/v1/';

			$value_prefix_courses = $value_prefix_top . $this->setting_option_values['ebox-courses'] . '/';
			$value_prefix_users   = $value_prefix_top . $this->setting_option_values['users'] . '/';
			$value_prefix_teams  = $value_prefix_top . $this->setting_option_values['teams'] . '/';

			$this->setting_option_fields_v1 = array(
				'ebox-courses'  => array(
					'name'         => 'ebox-courses',
					'type'         => 'text',
					'label'        => ebox_Custom_Label::get_label( 'course' ),
					'value'        => $this->setting_option_values['ebox-courses'],
					'value_prefix' => $value_prefix_top,
					'class'        => '-medium',
				),
				'ebox-modules'  => array(
					'name'         => 'ebox-modules',
					'type'         => 'text',
					'label'        => ebox_Custom_Label::get_label( 'lesson' ),
					'value'        => $this->setting_option_values['ebox-modules'],
					'value_prefix' => $value_prefix_top,
					'class'        => '-medium',
				),
				'ebox-topic'    => array(
					'name'         => 'ebox-topic',
					'type'         => 'text',
					'label'        => ebox_Custom_Label::get_label( 'topic' ),
					'value'        => $this->setting_option_values['ebox-topic'],
					'value_prefix' => $value_prefix_top,
					'class'        => '-medium',
				),
				'ebox-quiz'     => array(
					'name'         => 'ebox-quiz',
					'type'         => 'text',
					'label'        => ebox_Custom_Label::get_label( 'quiz' ),
					'value'        => $this->setting_option_values['ebox-quiz'],
					'value_prefix' => $value_prefix_top,
					'class'        => '-medium',
				),
				'ebox-question' => array(
					'name'         => 'ebox-question',
					'type'         => 'text',
					'label'        => ebox_Custom_Label::get_label( 'question' ),
					'value'        => $this->setting_option_values['ebox-question'],
					'value_prefix' => $value_prefix_top,
					'class'        => '-medium',
				),
				'users'         => array(
					'name'         => 'users',
					'type'         => 'text',
					'label'        => esc_html__( 'User', 'ebox' ),
					'value'        => $this->setting_option_values['users'],
					'value_prefix' => $value_prefix_top,
					'class'        => '-medium',
				),
				'teams'        => array(
					'name'         => 'teams',
					'type'         => 'text',
					'label'        => ebox_Custom_Label::get_label( 'team' ),
					'value'        => $this->setting_option_values['teams'],
					'value_prefix' => $value_prefix_top,
					'class'        => '-medium',
				),
			);

			$value_prefix_top = rest_get_url_prefix() . '/' . ebox_REST_API_NAMESPACE . '/v2/';

			$value_prefix_courses = $value_prefix_top . $this->setting_option_values['courses_v2'] . '/&lt;Course ID&gt;/';
			$value_prefix_users   = $value_prefix_top . $this->setting_option_values['users_v2'] . '/&lt;User ID&gt;/';
			$value_prefix_teams  = $value_prefix_top . $this->setting_option_values['teams_v2'] . '/&lt;Team ID&gt;/';
			$value_prefix_quizzes = $value_prefix_top . $this->setting_option_values['quizzes_v2'] . '/&lt;Quiz ID&gt;/';

			$value_prefix_statistics = $value_prefix_quizzes . $this->setting_option_values['quizzes-statistics_v2'] . '/&lt;Stat ID&gt;/';

			$this->setting_option_fields_v2 = array(
				'courses_v2'                      => array(
					'name'                => 'courses_v2',
					'type'                => 'text',
					'label'               => ebox_Custom_Label::get_label( 'course' ),
					'value'               => $this->setting_option_values['courses_v2'],
					'value_prefix'        => $value_prefix_top,
					'class'               => '-medium',
					'placeholder'         => ebox_get_post_type_slug( 'course' ),
					'child_section_state' => 'open',
				),
				'courses-users_v2'                => array(
					'name'           => 'courses-users_v2',
					'type'           => 'text',
					'label'          => sprintf(
						// translators: placeholder: Course.
						esc_html_x( '%s Users', 'placeholder: Course', 'ebox' ),
						ebox_Custom_Label::get_label( 'course' )
					),
					'value'          => $this->setting_option_values['courses-users_v2'],
					'value_prefix'   => $value_prefix_courses,
					'class'          => '-medium',
					'placeholder'    => 'users',
					'parent_setting' => 'courses_v2',
				),
				'courses-steps_v2'                => array(
					'name'           => 'courses-steps_v2',
					'type'           => 'text',
					'label'          => sprintf(
						// translators: placeholder: Course.
						esc_html_x( '%s Steps', 'placeholder: Course', 'ebox' ),
						ebox_Custom_Label::get_label( 'course' )
					),
					'value'          => $this->setting_option_values['courses-steps_v2'],
					'value_prefix'   => $value_prefix_courses,
					'class'          => '-medium',
					'placeholder'    => 'steps',
					'parent_setting' => 'courses_v2',
				),
				'courses-teams_v2'               => array(
					'name'           => 'courses-teams_v2',
					'type'           => 'text',
					'label'          => sprintf(
						// translators: placeholder: Course, Teams.
						esc_html_x( '%1$s %2$s', 'placeholder: Course, Teams', 'ebox' ),
						ebox_Custom_Label::get_label( 'course' ),
						ebox_Custom_Label::get_label( 'teams' )
					),
					'value'          => $this->setting_option_values['courses-teams_v2'],
					'value_prefix'   => $value_prefix_courses,
					'class'          => '-medium',
					'placeholder'    => 'teams',
					'parent_setting' => 'courses_v2',
				),
				'courses-prerequisites_v2'        => array(
					'name'           => 'courses-prerequisites_v2',
					'type'           => 'text',
					'label'          => sprintf(
						// translators: placeholder: Course.
						esc_html_x( '%s Prerequisites', 'placeholder: Course', 'ebox' ),
						ebox_Custom_Label::get_label( 'course' )
					),
					'value'          => $this->setting_option_values['courses-prerequisites_v2'],
					'value_prefix'   => $value_prefix_courses,
					'class'          => '-medium',
					'parent_setting' => 'courses_v2',
					'placeholder'    => 'prerequisites',
				),
				'modules_v2'                      => array(
					'name'         => 'modules_v2',
					'type'         => 'text',
					'label'        => ebox_Custom_Label::get_label( 'lesson' ),
					'value'        => $this->setting_option_values['modules_v2'],
					'value_prefix' => $value_prefix_top,
					'class'        => '-medium',
					'placeholder'  => ebox_get_post_type_slug( 'lesson' ),
				),
				'topics_v2'                       => array(
					'name'         => 'topics_v2',
					'type'         => 'text',
					'label'        => ebox_Custom_Label::get_label( 'topic' ),
					'value'        => $this->setting_option_values['topics_v2'],
					'value_prefix' => $value_prefix_top,
					'class'        => '-medium',
					'placeholder'  => ebox_get_post_type_slug( 'topic' ),
				),
				'quizzes_v2'                      => array(
					'name'                => 'quizzes_v2',
					'type'                => 'text',
					'label'               => ebox_Custom_Label::get_label( 'quiz' ),
					'value'               => $this->setting_option_values['quizzes_v2'],
					'value_prefix'        => $value_prefix_top,
					'class'               => '-medium',
					'placeholder'         => ebox_get_post_type_slug( 'quiz' ),
					'child_section_state' => 'open',
				),
				'quizzes-form-entries_v2'         => array(
					'name'           => 'quizzes-form-entries_v2',
					'type'           => 'text',
					'label'          => sprintf(
						// translators: placeholder: Quiz.
						esc_html_x( '%s Form Entries', 'placeholder: Quiz', 'ebox' ),
						ebox_Custom_Label::get_label( 'quiz' )
					),
					'value'          => $this->setting_option_values['quizzes-form-entries_v2'],
					'value_prefix'   => $value_prefix_quizzes,
					'class'          => '-medium',
					'placeholder'    => 'statistics',
					'parent_setting' => 'quizzes_v2',
				),
				'quizzes-statistics_v2'           => array(
					'name'           => 'quizzes-statistics_v2',
					'type'           => 'text',
					'label'          => sprintf(
						// translators: placeholder: Quiz.
						esc_html_x( '%s Statistics', 'placeholder: Quiz', 'ebox' ),
						ebox_Custom_Label::get_label( 'quiz' )
					),
					'value'          => $this->setting_option_values['quizzes-statistics_v2'],
					'value_prefix'   => $value_prefix_quizzes,
					'class'          => '-medium',
					'placeholder'    => 'statistics',
					'parent_setting' => 'quizzes_v2',
				),
				'quizzes-statistics-questions_v2' => array(
					'name'           => 'quizzes-statistics-questions_v2',
					'type'           => 'text',
					'label'          => sprintf(
						// translators: placeholder: Quiz, Questions.
						esc_html_x( '%1$s Statistics %2$s', 'placeholder: Quiz, Questions', 'ebox' ),
						ebox_get_custom_label( 'quiz' ),
						ebox_get_custom_label( 'questions' )
					),
					'value'          => $this->setting_option_values['quizzes-statistics-questions_v2'],
					'value_prefix'   => $value_prefix_statistics,
					'class'          => '-medium',
					'placeholder'    => 'questions',
					'parent_setting' => 'quizzes_v2',
				),
				'questions_v2'                    => array(
					'name'         => 'questions_v2',
					'type'         => 'text',
					'label'        => ebox_Custom_Label::get_label( 'question' ),
					'value'        => $this->setting_option_values['questions_v2'],
					'value_prefix' => $value_prefix_top,
					'class'        => '-medium',
					'placeholder'  => ebox_get_post_type_slug( 'question' ),
				),
				'assignments_v2'                  => array(
					'name'         => 'assignments_v2',
					'type'         => 'text',
					'label'        => esc_html__( 'Assignment', 'ebox' ),
					'value'        => $this->setting_option_values['assignments_v2'],
					'value_prefix' => $value_prefix_top,
					'class'        => '-medium',
					'placeholder'  => ebox_get_post_type_slug( 'assignment' ),
				),
				'essays_v2'                       => array(
					'name'         => 'essays_v2',
					'type'         => 'text',
					'label'        => esc_html__( 'Essay', 'ebox' ),
					'value'        => $this->setting_option_values['essays_v2'],
					'value_prefix' => $value_prefix_top,
					'class'        => '-medium',
					'placeholder'  => ebox_get_post_type_slug( 'essay' ),
				),
				'teams_v2'                       => array(
					'name'                => 'teams_v2',
					'type'                => 'text',
					'label'               => ebox_Custom_Label::get_label( 'team' ),
					'value'               => $this->setting_option_values['teams_v2'],
					'value_prefix'        => $value_prefix_top,
					'class'               => '-medium',
					'placeholder'         => ebox_get_post_type_slug( 'team' ),
					'child_section_state' => 'open',
				),
				'teams-courses_v2'               => array(
					'name'           => 'teams-courses_v2',
					'type'           => 'text',
					'label'          => sprintf(
						// translators: placeholder: Team, Courses.
						esc_html_x( '%1$s %2$s', 'placeholder: Team, Courses', 'ebox' ),
						ebox_Custom_Label::get_label( 'team' ),
						ebox_Custom_Label::get_label( 'courses' )
					),
					'value'          => $this->setting_option_values['teams-courses_v2'],
					'value_prefix'   => $value_prefix_teams,
					'class'          => '-medium',
					'parent_setting' => 'teams_v2',
				),
				'teams-leaders_v2'               => array(
					'name'           => 'teams-leaders_v2',
					'type'           => 'text',
					'label'          => sprintf(
						// translators: placeholder: Team.
						esc_html_x( '%s Leaders', 'placeholder: Team', 'ebox' ),
						ebox_Custom_Label::get_label( 'team' )
					),
					'value'          => $this->setting_option_values['teams-leaders_v2'],
					'value_prefix'   => $value_prefix_teams,
					'class'          => '-medium',
					'parent_setting' => 'teams_v2',
				),
				'teams-users_v2'                 => array(
					'name'           => 'teams-users_v2',
					'type'           => 'text',
					'label'          => sprintf(
						// translators: placeholder: Team.
						esc_html_x( '%s Users', 'placeholder: Team', 'ebox' ),
						ebox_Custom_Label::get_label( 'team' )
					),
					'value'          => $this->setting_option_values['teams-users_v2'],
					'value_prefix'   => $value_prefix_teams,
					'class'          => '-medium',
					'parent_setting' => 'teams_v2',
				),
				'exams_v2'                        => array(
					'name'         => 'exams_v2',
					'type'         => 'text',
					'label'        => ebox_Custom_Label::get_label( 'exam' ),
					'value'        => $this->setting_option_values['exams_v2'],
					'value_prefix' => $value_prefix_top,
					'class'        => '-medium',
					'placeholder'  => 'exams',
				),
				'users_v2'                        => array(
					'name'                => 'users_v2',
					'type'                => 'text',
					'label'               => esc_html__( 'User', 'ebox' ),
					'value'               => $this->setting_option_values['users_v2'],
					'value_prefix'        => $value_prefix_top,
					'class'               => '-medium',
					'placeholder'         => 'users',
					'child_section_state' => 'open',
				),
				'users-courses_v2'                => array(
					'name'           => 'users-courses_v2',
					'type'           => 'text',
					'label'          => sprintf(
						// translators: placeholder: Courses.
						esc_html_x( 'User %s', 'placeholder: Courses', 'ebox' ),
						ebox_Custom_Label::get_label( 'courses' )
					),
					'value'          => $this->setting_option_values['users-courses_v2'],
					'value_prefix'   => $value_prefix_users,
					'class'          => '-medium',
					'placeholder'    => 'courses',
					'parent_setting' => 'users_v2',
				),
				'users-teams_v2'                 => array(
					'name'           => 'users-teams_v2',
					'type'           => 'text',
					'label'          => sprintf(
						// translators: placeholder: Teams.
						esc_html_x( 'User %s', 'placeholder: Teams', 'ebox' ),
						ebox_get_custom_label( 'teams' )
					),
					'value'          => $this->setting_option_values['users-teams_v2'],
					'value_prefix'   => $value_prefix_users,
					'class'          => '-medium',
					'placeholder'    => 'teams',
					'parent_setting' => 'users_v2',
				),
				'users-course-progress_v2'        => array(
					'name'           => 'users-course-progress_v2',
					'type'           => 'text',
					'label'          => sprintf(
						// translators: placeholder: Course.
						esc_html_x( 'User %s Progress', 'placeholder: Course', 'ebox' ),
						ebox_Custom_Label::get_label( 'course' )
					),
					'value'          => $this->setting_option_values['users-course-progress_v2'],
					'value_prefix'   => $value_prefix_users,
					'class'          => '-medium',
					'placeholder'    => 'course-progress',
					'parent_setting' => 'users_v2',
				),
				'users-quiz-progress_v2'          => array(
					'name'           => 'users-quiz-progress_v2',
					'type'           => 'text',
					'label'          => sprintf(
						// translators: placeholder: Quiz.
						esc_html_x( 'User %s Attempts', 'placeholder: Quiz', 'ebox' ),
						ebox_Custom_Label::get_label( 'quiz' )
					),
					'value'          => $this->setting_option_values['users-quiz-progress_v2'],
					'value_prefix'   => $value_prefix_users,
					'class'          => '-medium',
					'placeholder'    => 'quiz-progress',
					'parent_setting' => 'users_v2',
				),

				'progress-status_v2'              => array(
					'name'         => 'progress-status_v2',
					'type'         => 'text',
					'label'        => esc_html__( 'Progress Status', 'ebox' ),
					'value'        => $this->setting_option_values['progress-status_v2'],
					'value_prefix' => $value_prefix_top,
					'class'        => '-medium',
					'placeholder'  => 'progress-status',
				),
				'price-types_v2'                  => array(
					'name'         => 'price-types_v2',
					'type'         => 'text',
					'label'        => esc_html__( 'Price Types', 'ebox' ),
					'value'        => $this->setting_option_values['price-types_v2'],
					'value_prefix' => $value_prefix_top,
					'class'        => '-medium',
					'placeholder'  => 'price-types',
				),
				'question-types_v2'               => array(
					'name'         => 'question-types_v2',
					'type'         => 'text',
					'label'        => sprintf(
						// translators: placeholder: Question.
						esc_html_x( '%s Types', 'placeholder: Question', 'ebox' ),
						ebox_Custom_Label::get_label( 'question' )
					),
					'value'        => $this->setting_option_values['question-types_v2'],
					'value_prefix' => $value_prefix_top,
					'class'        => '-medium',
					'placeholder'  => 'question-types',
				),
			);

			$this->setting_option_fields = array_merge( $this->setting_option_fields, $this->setting_option_fields_v1, $this->setting_option_fields_v2 );
			$this->setting_option_fields = apply_filters( 'ebox_settings_fields', $this->setting_option_fields, $this->settings_section_key );

			parent::load_settings_fields();
		}

		/**
		 * Hook into action after the fieldset is output. This allows adding custom content like JS/CSS.
		 *
		 * @since 3.3.0
		 *
		 * @param string $html This is the field output which will be send to the screen.
		 * @param array  $field_args Array of field args used to build the field HTML.
		 *
		 * @return string $html.
		 */
		public function ebox_settings_row_outside_after( $html = '', $field_args = array() ) {
			/**
			 * Here we hook into the bottom of the field HTML output and add some inline JS to handle the
			 * change event on the radio buttons. This is really just to update the 'custom' input field
			 * display.
			 */
			if ( ( isset( $field_args['setting_option_key'] ) ) && ( $this->setting_option_key === $field_args['setting_option_key'] ) ) {
				if ( ( isset( $field_args['name'] ) ) && ( 'teams' === $field_args['name'] ) ) {
					$html .= '<div class="ld-divider"></div>';
				}
			}
			return $html;
		}


		/**
		 * Settings row outside before
		 *
		 * @since 3.3.0
		 *
		 * @param string $content    Content to show before row.
		 * @param array  $field_args Row field Args.
		 */
		public function ebox_settings_row_outside_before( $content = '', $field_args = array() ) {
			if ( ( isset( $field_args['name'] ) ) && ( in_array( $field_args['name'], array( 'ebox-courses', 'courses_v2' ), true ) ) ) {
				if ( 'ebox-courses' === $field_args['name'] ) {
					$content .= '<div class="ld-settings-email-header-wrapper">';

					$content .= '<div class="ld-settings-email-header">';
					$content .= esc_html__( 'V1 Endpoints', 'ebox' );
					$content .= '</div>';

					$content .= '</div>';
				} elseif ( 'courses_v2' === $field_args['name'] ) {
					$content .= '<div class="ld-settings-email-header-wrapper">';

					$content .= '<div class="ld-settings-email-header">';
					$content .= esc_html__( 'V2 Endpoints (Beta)', 'ebox' );
					$content .= '</div>';

					$content .= '</div>';
				}
			}

			return $content;
		}

		// End of function.
	}
}
add_action(
	'ebox_settings_sections_init',
	function() {
		ebox_Settings_Section_General_REST_API::add_section_instance();
	}
);
