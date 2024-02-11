<?php
/**
 * ebox_LMS
 *
 * @since 2.1.0
 *
 * @package ebox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// cspell:ignore i18nize .

if ( ! class_exists( 'ebox_LMS' ) ) {

	/**
	 * Class to create the ebox_LMS instance.
	 */
	class ebox_LMS extends Semper_Fi_Module {

		/**
		 * Array of post types
		 *
		 * @var array
		 */
		public $post_types = array();

		/**
		 * Cache key
		 *
		 * @var string
		 */
		public $cache_key = '';

		/**
		 * Quiz JSON
		 *
		 * @var string
		 */
		public $quiz_json = '';

		/**
		 * Count
		 *
		 * @var int
		 */
		public $count = null;

		/**
		 * Post arguments
		 *
		 * @var array
		 */
		private $post_args = array();

		/**
		 * All plugins called
		 *
		 * @var bool
		 */
		private $all_plugins_called = false;

		/**
		 * ebox plugin path
		 *
		 * @var string
		 */
		private $ebox_standard_plugin_path = 'ebox-lms/ebox_lms.php';


		/**
		 * ebox Admin Teams Users List instance
		 *
		 * @var ebox_Admin_Teams_Users_List
		 */
		public $ld_admin_teams_users_list = null;

		/**
		 * ebox Admin Data Upgrades instance
		 *
		 * @var ebox_Admin_Data_Upgrades
		 */
		public $ld_admin_data_upgrades = null;

		/**
		 * ebox Admin Settings Data Reports instance
		 *
		 * @var ebox_Admin_Settings_Data_Reports
		 */
		public $ld_admin_settings_data_reports = null;

		/**
		 * ebox Admin User Profile Edit instance
		 *
		 * @var ebox_Admin_User_Profile_Edit
		 */
		public $ld_admin_user_profile_edit = null;

		/**
		 * ebox Setup Wizard
		 *
		 * @var ebox_Setup_Wizard
		 */
		public $ld_setup_wizard = null;

		/**
		 * ebox Course Wizard instance
		 *
		 * @var ebox_Course_Wizard
		 */
		public $ld_course_wizard = null;

		/**
		 * ebox Design Wizard instance
		 *
		 * @var ebox_Design_Wizard
		 */
		public $ld_design_wizard = null;

		/**
		 * Set up properties and hooks for this class
		 */
		public function __construct() {
			self::$instance      =& $this;
			$this->file          = __FILE__;
			$this->name          = 'LMS';
			$this->plugin_name   = 'ebox LMS';
			$this->name          = 'LMS Options';
			$this->prefix        = 'ebox_lms_';
			$this->parent_option = 'ebox_lms_options';
			parent::__construct();

			// maybe call the activate function.
			add_action(
				'init',
				function () {
					if ( get_option( 'ebox_activation' ) ) {
						$this->activate();
						delete_option( 'ebox_activation' );
					}
				}
			);

			add_action( 'init', array( $this, 'trigger_actions' ), 1 );
			add_action( 'init', array( $this, 'add_post_types' ), 2 );

			// WPMU (Multisite) actions when a new blog is added/deleted.
			add_action( 'wpmu_new_blog', array( $this, 'wpmu_new_blog' ) );
			add_action( 'delete_blog', array( $this, 'delete_blog' ), 10, 2 );

			add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
			add_action( 'generate_rewrite_rules', array( $this, 'paypal_rewrite_rules' ) );
			add_filter( 'ebox_cpt_loop', array( $this, 'cpt_loop_filter' ) );
			add_filter( 'edit_term_count', array( $this, 'tax_term_count' ), 10, 3 );
			add_action( 'plugins_loaded', array( $this, 'i18nize' ) ); // cspell:disable-line.
			add_action( 'current_screen', array( $this, 'add_telemetry_modal' ) );

			require_once ebox_LMS_PLUGIN_DIR . 'includes/payments/gateways/init.php';

			add_filter( 'all_plugins', array( $this, 'all_plugins_proc' ) );
			add_action( 'pre_current_active_plugins', array( $this, 'pre_current_active_plugins_proc' ) );
			add_filter( 'option_active_plugins', array( $this, 'option_active_plugins_proc' ) );
			add_filter( 'site_option_active_sitewide_plugins', array( $this, 'site_option_active_sitewide_plugins_proc' ) );
			add_filter( 'pre_update_option_active_plugins', array( $this, 'pre_update_option_active_plugins' ) );
			add_filter( 'pre_update_site_option_active_sitewide_plugins', array( $this, 'pre_update_site_option_active_sitewide_plugins' ) );

			add_action( 'after_setup_theme', array( $this, 'load_template_functions' ), 50 );

			add_filter( 'category_row_actions', array( $this, 'ld_course_category_row_actions' ), 10, 2 );
			add_filter( 'post_tag_row_actions', array( $this, 'ld_course_category_row_actions' ), 10, 2 );

			add_action( 'admin_notices', array( $this, 'hub_after_upgrade_admin_notice' ), 99 );

			add_action( 'shutdown', array( $this, 'wp_shutdown' ), 0 );

			if ( is_admin() ) {
				require_once ebox_LMS_PLUGIN_DIR . 'includes/admin/class-ebox-admin-teams-users-list.php';
				$this->ld_admin_teams_users_list = new ebox_Admin_Teams_Users_List();

				require_once ebox_LMS_PLUGIN_DIR . 'includes/admin/class-ebox-admin-data-upgrades.php';
				$this->ld_admin_data_upgrades = ebox_Admin_Data_Upgrades::get_instance();

				require_once ebox_LMS_PLUGIN_DIR . 'includes/admin/class-ebox-admin-settings-data-reports.php';
				$this->ld_admin_settings_data_reports = new ebox_Admin_Settings_Data_Reports();

				require_once ebox_LMS_PLUGIN_DIR . 'includes/admin/class-ebox-admin-user-profile-edit.php';
				$this->ld_admin_user_profile_edit = new ebox_Admin_User_Profile_Edit();

				require_once ebox_LMS_PLUGIN_DIR . 'includes/admin/class-ebox-admin-posts-edit.php';
				require_once ebox_LMS_PLUGIN_DIR . 'includes/admin/class-ebox-admin-posts-listing.php';

				/**
				 * WP-admin pointers functions
				 */
				require_once ebox_LMS_PLUGIN_DIR . 'includes/admin/class-ebox-admin-pointers.php';

				/**
				 * Setup Wizard
				 */
				require_once ebox_LMS_PLUGIN_DIR . 'includes/class-ld-setup-wizard.php';
				$this->ld_setup_wizard = new ebox_Setup_Wizard();

				/**
				 * Course Wizard
				 */
				require_once ebox_LMS_PLUGIN_DIR . 'includes/class-ld-course-wizard.php';
				$this->ld_course_wizard = new ebox_Course_Wizard();
				$this->ld_course_wizard->init();

				if ( ! ebox_cloud_is_enabled() ) {
					/**
					 * Design wizard.
					 */
					require_once ebox_LMS_PLUGIN_DIR . 'includes/class-ld-design-wizard.php';
					$this->ld_design_wizard = new ebox_Design_Wizard();
				}
			}

			add_action( 'wp_ajax_select_a_lesson', array( $this, 'select_a_lesson_ajax' ) );
			add_action( 'wp_ajax_select_a_lesson_or_topic', array( $this, 'select_a_lesson_or_topic_ajax' ) );
			add_action( 'wp_ajax_select_a_quiz', array( $this, 'select_a_quiz_ajax' ) );
		}

		/**
		 * Triggered actions
		 */
		public function trigger_actions() {
			global $ebox_course_statuses, $ebox_question_types, $ebox_exam_challenge_statuses;

			$ebox_course_statuses = array(
				'not_started' => esc_html__( 'Not Started', 'ebox' ),
				'in_progress' => esc_html__( 'In Progress', 'ebox' ),
				'completed'   => esc_html__( 'Completed', 'ebox' ),
			);

			$ebox_question_types = array(
				'single'             => esc_html__( 'Single choice', 'ebox' ),
				'multiple'           => esc_html__( 'Multiple choice', 'ebox' ),
				'free_answer'        => esc_html__( '"Free" choice', 'ebox' ),
				'sort_answer'        => esc_html__( '"Sorting" choice', 'ebox' ),
				'matrix_sort_answer' => esc_html__( '"Matrix Sorting" choice', 'ebox' ),
				'cloze_answer'       => esc_html__( 'Fill in the blank', 'ebox' ),
				'assessment_answer'  => esc_html__( 'Assessment', 'ebox' ),
				'essay'              => esc_html__( 'Essay / Open Answer', 'ebox' ),
			);

			$ebox_exam_challenge_statuses = array(
				'not_taken' => esc_html__( 'Not Taken', 'ebox' ),
				'passed'    => esc_html__( 'Passed', 'ebox' ),
				'failed'    => esc_html__( 'Failed', 'ebox' ),
			);

			$this->upgrade_plugin();

			if ( is_admin() ) {
				if ( ( is_multisite() ) && ( ! is_network_admin() ) ) {
					if ( isset( $_GET['ebox_activate'] ) ) {
						$this->activate();
					}
				}
				/**
				 * Fires on plugin initialization init for admins.
				 */
				do_action( 'ebox_admin_init' );
			}

			/**
			 * Fires on plugin initialization.
			 */
			do_action( 'ebox_init' );

			/**
			 * Fires on ebox setting sections fields init.
			 */
			do_action( 'ebox_settings_sections_fields_init' );

			/**
			 * Fires on ebox setting sections init.
			 */
			do_action( 'ebox_settings_sections_init' );

			if ( is_admin() ) {
				/**
				 * Fires on ebox setting pages init.
				 */
				do_action( 'ebox_settings_pages_init' );
			}

			/**
			 * Fires to trigger active theme/template to load.
			 *
			 * @since 4.0.0
			 */
			do_action( 'ebox_themes_load' );

			/**
			 * Fires when ebox core is loaded.
			 *
			 * @since 4.0.0
			 */
			do_action( 'ebox_loaded' );
		}

		/**
		 * Called when new Multisite blog is created
		 * this is used to trigger the activate logic
		 *
		 * @since 2.5.5
		 *
		 * @param int $blog_id Blog ID.
		 */
		public function wpmu_new_blog( $blog_id = 0 ) {
			if ( ! empty( $blog_id ) ) {
				switch_to_blog( $blog_id );
				$this->activate();
				restore_current_blog();
			}
		}

		/**
		 * Called when Multisite blog is deleted
		 * this is used to remove any custom DB tables.
		 *
		 * @since 2.5.5
		 *
		 * @param int  $blog_id     Blog ID.
		 * @param bool $drop_tables Whether to delete DB tables.
		 */
		public function delete_blog( $blog_id = 0, $drop_tables = false ) {
			if ( ( ! empty( $blog_id ) ) && ( true === $drop_tables ) ) {
				switch_to_blog( $blog_id );
				ebox_delete_all_data();
				restore_current_blog();
			}
		}

		/**
		 * Get post args section
		 *
		 * @param string $section     Section.
		 * @param string $sub_section Sub-section.
		 */
		public function get_post_args_section( $section = '', $sub_section = '' ) {
			if ( ( ! empty( $section ) ) && ( isset( $this->post_args[ $section ] ) ) ) {
				if ( ( ! empty( $sub_section ) ) && ( isset( $this->post_args[ $section ][ $sub_section ] ) ) ) {
					return $this->post_args[ $section ][ $sub_section ];
				} else {
					return $this->post_args[ $section ];
				}
			}
		}

		/**
		 * Shutdown actions.
		 */
		public function wp_shutdown() {
			// If we are activating LD then we wait to flush the rewrite on the next page load because the $this->post_args is not setup yet.
			if ( defined( 'ebox_ACTIVATED' ) && ebox_ACTIVATED ) {
				return;
			}

			if ( defined( 'ebox_SETTINGS_UPDATING' ) && ebox_SETTINGS_UPDATING ) {
				return;
			}

			// check if we triggered the rewrite flush.
			$ebox_lms_rewrite_flush_transient = get_option( 'ebox_lms_rewrite_flush' );

			if ( $ebox_lms_rewrite_flush_transient ) {

				delete_option( 'ebox_lms_rewrite_flush' );

				$ld_rewrite_post_types = array(
					'ebox-courses'  => 'courses',
					'ebox-modules'  => 'modules',
					'ebox-topic'    => 'topics',
					'ebox-quiz'     => 'quizzes',
					'ebox-question' => 'questions',
					'teams'        => 'teams',
				);

				// First, we update the $post_args array item with the new permalink slug.
				foreach ( $ld_rewrite_post_types as $cpt_key => $custom_label_key ) {
					if ( isset( $this->post_args[ $cpt_key ] ) ) {
						$this->post_args[ $cpt_key ]['slug_name']                  = ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_Permalinks', $custom_label_key );
						$this->post_args[ $cpt_key ]['cpt_options']['has_archive'] = ebox_post_type_has_archive( $cpt_key );
					}
				}

				// Second, we allow external filters. This is the same filter used when the post types are registered.
				/**
				 * Filters post arguments used to create the custom post types and everything
				 * associated with them.
				 *
				 * @since 2.1.0
				 *
				 * @param array $post_args An array of custom post type arguments.
				 */
				$this->post_args = apply_filters( 'ebox_post_args', $this->post_args );

				// Last we need to update the registered post type.
				foreach ( $ld_rewrite_post_types as $cpt_key => $custom_label_key ) {
					$post_type_object = get_post_type_object( $cpt_key );
					if ( $post_type_object instanceof WP_Post_Type ) {
						$post_type_object->rewrite['slug'] = $this->post_args[ $cpt_key ]['slug_name'];
						$post_type_object->has_archive     = $this->post_args[ $cpt_key ]['cpt_options']['has_archive'];

						$post_type_object = wp_parse_args( $post_type_object );
						register_post_type( $cpt_key, $post_type_object );
					}
				}

				flush_rewrite_rules();
			}
		}

		/**
		 * Load functions used for templates
		 *
		 * @since 2.1.0
		 */
		public function load_template_functions() {
			$this->init_ld_templates_dir();
			$template_file = $this->get_template( 'ebox_template_functions', array(), false, true );
			if ( ( ! empty( $template_file ) ) && ( file_exists( $template_file ) ) && ( is_file( $template_file ) ) ) {
				include_once $template_file;
			}

			// Add support for generic name functions.php file in our template directory.
			$template_functions_file = ebox_TEMPLATES_DIR;
			$template_functions_file = trailingslashit( $template_functions_file ) . 'functions.php';
			if ( file_exists( $template_functions_file ) ) {
				include_once $template_functions_file;
			}
		}

		/**
		 * Loads the plugin's translated strings
		 *
		 * @since 2.1.0
		 */
		public function i18nize() {
			if ( ( defined( 'LD_LANG_DIR' ) ) && ( LD_LANG_DIR ) ) {
				load_plugin_textdomain( ebox_LMS_TEXT_DOMAIN, false, LD_LANG_DIR );
			} else {
				load_plugin_textdomain( ebox_LMS_TEXT_DOMAIN, false, dirname( plugin_basename( dirname( __FILE__ ) ) ) . '/languages' );
			}
		}

		/**
		 * Update count of posts with a term
		 *
		 * Callback for add_filter 'edit_term_count'
		 * There is no apply_filters or php call to execute this function
		 *
		 * @todo  consider for deprecation, other docblock tags removed
		 *
		 * @since 2.1.0
		 *
		 * @param string $columns Columns.
		 * @param string $id      Field slug.
		 * @param string $tax     Taxonomy.
		 */
		public function tax_term_count( $columns, $id, $tax ) {
			if ( empty( $tax ) || ( 'courses' != $tax ) ) {
				return $columns;
			}

			if ( ! empty( $_GET ) && ! empty( $_GET['post_type'] ) ) {
				$post_type   = $_GET['post_type'];
				$wpq         = array(
					'tax_query'      => array(
						array(
							'taxonomy' => $tax,
							'field'    => 'id',
							'terms'    => $id,
						),
					),
					'post_type'      => $post_type,
					'post_status'    => 'publish',
					'posts_per_page' => -1,
				);
				$q           = new WP_Query( $wpq );
				$this->count = $q->found_posts;
				add_filter( 'number_format_i18n', array( $this, 'column_term_number' ) );
			}

			return $columns;
		}

		/**
		 * Set column term number
		 *
		 * This function is called by the 'tax_term_count' method and is no longer being ran
		 * See tax_term_count()
		 *
		 * @todo  consider for deprecation, other docblock tags removed
		 *
		 * @since 2.1.0
		 *
		 * @param int $number Number.
		 */
		public function column_term_number( $number ) {
			remove_filter( 'number_format_i18n', array( $this, 'column_term_number' ) );
			if ( null !== $this->count ) {
				$number = $this->count;
				unset( $this->count );
			}
			return $number;
		}



		/**
		 * [usermeta] shortcode
		 *
		 * This shortcode takes a parameter named field, which is the name of the user meta data field to be displayed.
		 * Example: [usermeta field="display_name"] would display the user's Display Name.
		 *
		 * @since 2.1.0
		 *
		 * @param  array  $attr    shortcode attributes.
		 * @param  string $content content of shortcode.
		 * @return string            output of shortcode.
		 */
		public function usermeta_shortcode( $attr, $content = '' ) {
			return ebox_usermeta_shortcode( $attr, $content );
		}


		/**
		 * Callback for add_filter 'ebox_cpt_loop'
		 * There is no apply_filters or php call to execute this function
		 *
		 * @since 2.1.0
		 *
		 * @todo  consider for deprecation, other docblock tags removed
		 *
		 * @param string $content Content.
		 */
		public function cpt_loop_filter( $content ) {
			global $post;
			if ( 'ebox-quiz' === $post->post_type ) {
				$meta = get_post_meta( $post->ID, '_ebox-quiz' );
				if ( is_array( $meta ) && ! empty( $meta ) ) {
					$meta = $meta[0];
					if ( is_array( $meta ) && ( ! empty( $meta['ebox-quiz_lesson'] ) ) ) {
						$content = '';
					}
				}
			}
			return $content;
		}

		/**
		 * Upgrade plugin
		 */
		public function upgrade_plugin() {
			$ld_is_upgrade = ebox_data_upgrades_setting( 'is_upgrade' );
			if ( true === $ld_is_upgrade ) {
				$this->activate();

				$ld_admin_data_upgrades = ebox_Admin_Data_Upgrades::get_instance();
				$ld_admin_data_upgrades->set_data_settings( 'is_upgrade', false );
			}
		}

		/**
		 * Fire on plugin activation
		 *
		 * Currently sets 'ebox_lms_rewrite_flush' to true
		 *
		 * @param bool $network_wide Whether to enable the plugin for all sites in the network
		 *                           or just the current site. Multisite only. Default false.
		 *
		 * @since 4.1.1 Added $network_wide param.
		 * @since 2.1.0
		 */
		public function activate( $network_wide = false ) {
			ebox_setup_rewrite_flush();

			if ( ! defined( 'ebox_ACTIVATED' ) ) {
				$ebox_activated = true;

				/**
				 * Define ebox LMS - Set during plugin activation.
				 *
				 * @since 2.4.0
				 * @internal Will be set by ebox LMS.
				 */
				define( 'ebox_ACTIVATED', $ebox_activated );
			}

			/**
			 * Remove legacy option item
			 *
			 * @since 2.5.7
			 */
			delete_option( 'ld-repositories' );

			/**
			 * Ensure we call WPProQuiz activate functions
			 *
			 * @since 2.4.6.1
			 */
			WpProQuiz_Helper_Upgrade::upgrade();

			require_once ebox_LMS_PLUGIN_DIR . 'includes/admin/class-ebox-admin-data-upgrades.php';

			$ld_prior_version = ebox_data_upgrades_setting( 'prior_version' );

			ebox_init_admin_courses_capabilities();
			ebox_init_admin_teams_capabilities();
			ebox_init_admin_coupons_capabilities();
			ebox_init_assignments_capabilities();

			if ( 'new' === $ld_prior_version ) {

				// As this is a new install we want to set the prior data run on the Courses and Quizzes.
				$data_upgrade_courses = ebox_Admin_Data_Upgrades::get_instance( 'ebox_Admin_Data_Upgrades_User_Meta_Courses' );
				if ( $data_upgrade_courses ) {
					$data_upgrade_courses->set_last_run_info();
				}

				$data_upgrade_quizzes = ebox_Admin_Data_Upgrades::get_instance( 'ebox_Admin_Data_Upgrades_User_Meta_Quizzes' );
				if ( $data_upgrade_quizzes ) {
					$data_upgrade_quizzes->set_last_run_info();
				}

				$data_upgrade_course_access_list = ebox_Admin_Data_Upgrades::get_instance( 'ebox_Admin_Data_Upgrades_Course_Access_List_Convert' );
				if ( $data_upgrade_course_access_list ) {
					$data_upgrade_course_access_list->set_last_run_info();
				}

				$data_upgrade_quiz_questions = ebox_Admin_Data_Upgrades::get_instance( 'ebox_Admin_Data_Upgrades_Quiz_Questions' );
				if ( $data_upgrade_quiz_questions ) {
					$data_upgrade_quiz_questions->set_last_run_info();
				}

				$data_upgrade_course_post_meta = ebox_Admin_Data_Upgrades::get_instance( 'ebox_Admin_Data_Upgrades_Course_Post_Meta' );
				if ( $data_upgrade_course_post_meta ) {
					$data_upgrade_course_post_meta->set_last_run_info();
				}

				$data_upgrade_team_post_meta = ebox_Admin_Data_Upgrades::get_instance( 'ebox_Admin_Data_Upgrades_Team_Post_Meta' );
				if ( $data_upgrade_team_post_meta ) {
					$data_upgrade_team_post_meta->set_last_run_info();
				}

				$data_upgrade_quiz_post_meta = ebox_Admin_Data_Upgrades::get_instance( 'ebox_Admin_Data_Upgrades_Quiz_Post_Meta' );
				if ( $data_upgrade_quiz_post_meta ) {
					$data_upgrade_quiz_post_meta->set_last_run_info();
				}
			}

			$ld_admin_settings_data_upgrades_db = ebox_Admin_Data_Upgrades::get_instance( 'ebox_Admin_Data_Upgrades_User_Activity_DB_Table' );
			$ld_admin_settings_data_upgrades_db->upgrade_data_settings();

			$ld_admin_data_upgrades = ebox_Admin_Data_Upgrades::get_instance();
			$ld_admin_data_upgrades->set_data_settings( 'translations_installed', false );

			/**
			 * If the prior version is not empty we check if there are existing questions. If
			 * none found we set the questions data upgrade to completed.
			 */
			if ( 'new' !== $ld_prior_version ) {
				global $wpdb;

				$data_upgrade_quiz_questions = ebox_Admin_Data_Upgrades::get_instance( 'ebox_Admin_Data_Upgrades_Quiz_Questions' );
				if ( $data_upgrade_quiz_questions ) {
					$questions_data_settings = $data_upgrade_quiz_questions->get_data_settings( 'pro-quiz-questions' );

					$question_proquiz_count = $wpdb->get_var(
						$wpdb->prepare(
							'SELECT id FROM ' . esc_sql( LDLMS_DB::get_table_name( 'quiz_question' ) ) . ' LIMIT %d',
							1
						)
					);

					$question_post_count = $wpdb->get_var(
						$wpdb->prepare(
							'SELECT ID FROM ' . $wpdb->posts . ' WHERE post_type=%s LIMIT %d',
							ebox_get_post_type_slug( 'question' ),
							1
						)
					);

					if ( ( empty( $question_proquiz_count ) ) && ( empty( $question_post_count ) ) ) {
						$data_upgrade_quiz_questions->set_last_run_info();
					} elseif ( ( ! empty( $question_proquiz_count ) ) && ( empty( $question_post_count ) ) ) {
						$data_upgrade_quiz_questions->set_data_settings( 'pro-quiz-questions', false );
					} elseif ( ( ! empty( $question_proquiz_count ) ) && ( ! empty( $question_post_count ) ) ) {
						if ( false === $questions_data_settings ) {
							$data_upgrade_quiz_questions->set_last_run_info();
						}
					}
				}

				// Only show notice if upgrading from 4.3.0.2 to 4.3.1.
				if ( '4.3.0.2' === $ld_prior_version ) {
					update_option( 'ebox_show_hub_upgrade_admin_notice', true );
				}
			}

			/**
			 * Secure the Assignments & Essay uploads directory from browsing
			 *
			 * @since 2.5.5
			 */
			$wp_upload_dir      = wp_upload_dir();
			$wp_upload_base_dir = str_replace( '\\', '/', $wp_upload_dir['basedir'] );

			$ld_dirs = array( 'assignments', 'essays' );
			foreach ( array( 'assignments', 'essays' ) as $ld_dir ) {

				$_dir = trailingslashit( $wp_upload_base_dir ) . $ld_dir;
				if ( ! file_exists( $_dir ) ) {
					if ( is_writable( dirname( $_dir ) ) ) {
						wp_mkdir_p( $_dir );
					}
				}

				if ( file_exists( $_dir ) ) {
					$_index = trailingslashit( $_dir ) . 'index.php';
					if ( ! file_exists( $_index ) ) {
						file_put_contents( $_index, '//ebox is THE Best LMS' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents -- It's okay here.
					}
				}
			}

			if ( file_exists( trailingslashit( ebox_LMS_PLUGIN_DIR ) . 'mu-plugins/setup.php' ) ) {
				include trailingslashit( ebox_LMS_PLUGIN_DIR ) . 'mu-plugins/setup.php';
			}

			/**
			 * Fires on ebox plugin activation.
			 *
			 * @since 2.1.0
			 */
			do_action( 'ebox_activated' );
		}

		/**
		 * Add 'ebox-lms' to query vars
		 * Fired on filter 'query_vars'
		 *
		 * @since 2.1.0
		 *
		 * @param  array $vars  query vars.
		 * @return array    $vars  query vars
		 */
		public function add_query_vars( $vars ) {
			$paypal_email = ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_PayPal', 'paypal_email' );
			if ( ! empty( $paypal_email ) ) {
				$vars = array_merge( array( 'ebox-lms' ), $vars );
			}
			return $vars;
		}

		/**
		 * Adds paypal to already generated rewrite rules
		 * Fired on action 'generate_rewrite_rules'
		 *
		 * @since 2.1.0
		 *
		 * @param  object $wp_rewrite WP rewrite object.
		 */
		public function paypal_rewrite_rules( $wp_rewrite ) {
			$wp_rewrite->rules = array_merge( array( 'ebox-lms/paypal' => 'index.php?ebox-lms=paypal' ), $wp_rewrite->rules );
		}

		/**
		 * Sets up CPT's and creates a 'new ebox_CPT_Instance()' of each
		 *
		 * @since 2.1.0
		 */
		public function add_post_types() {
			$post = 0;

			if ( is_admin() && ! empty( $_GET ) && ( isset( $_GET['post'] ) ) ) {
				$post_id = $_GET['post'];
			}

			if ( ! empty( $post_id ) ) {
				$this->quiz_json = get_post_meta( $post_id, '_quizdata', true );
				if ( ! empty( $this->quiz_json ) ) {
					$this->quiz_json = $this->quiz_json['workingJson'];
				}
			}

			$options = get_option( 'ebox_cpt_options' );

			$level1 = '';
			$level2 = '';
			$level3 = '';
			$level4 = '';
			$level5 = '';

			if ( ! empty( $options['modules'] ) ) {
				$options = $options['modules'];
				if ( ! empty( $options['ebox-quiz_options'] ) ) {
					$options = $options['ebox-quiz_options'];
					foreach ( array( 'level1', 'level2', 'level3', 'level4', 'level5' ) as $level ) {
						$$level = '';
						if ( ! empty( $options[ "ebox-quiz_{$level}" ] ) ) {
							$$level = $options[ "ebox-quiz_{$level}" ];
						}
					}
				}
			}

			if ( empty( $this->quiz_json ) ) {
				$this->quiz_json = '{"info":{"name":"","main":"","results":"","level1":"' . $level1 . '","level2":"' . $level2 . '","level3":"' . $level3 . '","level4":"' . $level4 . '","level5":"' . $level5 . '"}}';
			}

			$posts_per_page = ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_General_Per_Page', 'per_page' );
			if ( empty( $posts_per_page ) ) {
				$posts_per_page = get_option( 'posts_per_page' );
				if ( empty( $posts_per_page ) ) {
					$posts_per_page = 5;
				}
			}

			ebox_init_admin_courses_capabilities();
			$course_capabilities = ebox_get_admin_courses_capabilities();

			$lcl_topic  = ebox_Custom_Label::get_label( 'topic' );
			$lcl_topics = ebox_Custom_Label::get_label( 'topics' );

			$lesson_topic_labels = array(
				'name'                     => $lcl_topics,
				'singular_name'            => $lcl_topic,
				'add_new'                  => esc_html_x( 'Add New', 'Add New Topic Label', 'ebox' ),
				// translators: placeholder: Topic.
				'add_new_item'             => sprintf( esc_html_x( 'Add New %s', 'placeholder: Topic', 'ebox' ), $lcl_topic ),
				// translators: placeholder: Topic.
				'edit_item'                => sprintf( esc_html_x( 'Edit %s', 'placeholder: Topic', 'ebox' ), $lcl_topic ),
				// translators: placeholder: Topic.
				'new_item'                 => sprintf( esc_html_x( 'New %s', 'placeholder: Topic', 'ebox' ), $lcl_topic ),
				'all_items'                => $lcl_topics,
				// translators: placeholder: Topic.
				'view_item'                => sprintf( esc_html_x( 'View %s', 'placeholder: Topic', 'ebox' ), $lcl_topic ),
				// translators: placeholder: Topics.
				'view_items'               => sprintf( esc_html_x( 'View %s', 'placeholder: Topics', 'ebox' ), $lcl_topics ),
				// translators: placeholder: Topics.
				'search_items'             => sprintf( esc_html_x( 'Search %s', 'placeholder: Topics', 'ebox' ), $lcl_topics ),
				// translators: placeholder: Topics.
				'not_found'                => sprintf( esc_html_x( 'No %s found', 'placeholder: Topics', 'ebox' ), $lcl_topics ),
				// translators: placeholder: Topic.
				'not_found_in_trash'       => sprintf( esc_html_x( 'No %s found in Trash', 'placeholder: Topic', 'ebox' ), $lcl_topics ),
				'parent_item_colon'        => '',
				'menu_name'                => $lcl_topics,
				// translators: placeholder: Topic.
				'item_published'           => sprintf( esc_html_x( '%s Published', 'placeholder: Topic', 'ebox' ), $lcl_topic ),
				// translators: placeholder: Topic.
				'item_published_privately' => sprintf( esc_html_x( '%s Published Privately', 'placeholder: Topic', 'ebox' ), $lcl_topic ),
				// translators: placeholder: Topic.
				'item_reverted_to_draft'   => sprintf( esc_html_x( '%s Reverted to Draft', 'placeholder: Topic', 'ebox' ), $lcl_topic ),
				// translators: placeholder: Topic.
				'item_scheduled'           => sprintf( esc_html_x( '%s Scheduled', 'placeholder: Topic', 'ebox' ), $lcl_topic ),
				// translators: placeholder: Topic.
				'item_updated'             => sprintf( esc_html_x( '%s Updated', 'placeholder: Topic', 'ebox' ), $lcl_topic ),
			);

			$lcl_quiz    = ebox_Custom_Label::get_label( 'quiz' );
			$lcl_quizzes = ebox_Custom_Label::get_label( 'quizzes' );

			$quiz_labels = array(
				'name'                     => $lcl_quizzes,
				'singular_name'            => $lcl_quiz,
				'add_new'                  => esc_html_x( 'Add New', 'Add New Quiz Label', 'ebox' ),
				// translators: placeholder: Quiz.
				'add_new_item'             => sprintf( esc_html_x( 'Add New %s', 'placeholder: Quiz', 'ebox' ), $lcl_quiz ),
				// translators: placeholder: Quiz.
				'edit_item'                => sprintf( esc_html_x( 'Edit %s', 'placeholder: Quiz', 'ebox' ), $lcl_quiz ),
				// translators: placeholder: Quiz.
				'new_item'                 => sprintf( esc_html_x( 'New %s', 'placeholder: Quiz', 'ebox' ), $lcl_quiz ),
				'all_items'                => $lcl_quizzes,
				// translators: placeholder: Quiz.
				'view_item'                => sprintf( esc_html_x( 'View %s', 'placeholder: Quiz', 'ebox' ), $lcl_quiz ),
				// translators: placeholder: Quizzes.
				'view_items'               => sprintf( esc_html_x( 'View %s', 'placeholder: Quizzes', 'ebox' ), $lcl_quizzes ),
				// translators: placeholder: Quizzes.
				'search_items'             => sprintf( esc_html_x( 'Search %s', 'placeholder: Quizzes', 'ebox' ), $lcl_quizzes ),
				// translators: placeholder: Quizzes.
				'not_found'                => sprintf( esc_html_x( 'No %s found', 'placeholder: Quizzes', 'ebox' ), $lcl_quizzes ),
				// translators: placeholder: Quizzes.
				'not_found_in_trash'       => sprintf( esc_html_x( 'No %s found in Trash', 'placeholder: Quizzes', 'ebox' ), $lcl_quizzes ),
				'parent_item_colon'        => '',
				'menu_name'                => $lcl_quizzes,
				// translators: placeholder: Quiz.
				'item_published'           => sprintf( esc_html_x( '%s Published', 'placeholder: Quiz', 'ebox' ), $lcl_quiz ),
				// translators: placeholder: Quiz.
				'item_published_privately' => sprintf( esc_html_x( '%s Published Privately', 'placeholder: Quiz', 'ebox' ), $lcl_quiz ),
				// translators: placeholder: Quiz.
				'item_reverted_to_draft'   => sprintf( esc_html_x( '%s Reverted to Draft', 'placeholder: Quiz', 'ebox' ), $lcl_quiz ),
				// translators: placeholder: Quiz.
				'item_scheduled'           => sprintf( esc_html_x( '%s Scheduled', 'placeholder: Quiz', 'ebox' ), $lcl_quiz ),
				// translators: placeholder: Quiz.
				'item_updated'             => sprintf( esc_html_x( '%s Updated', 'placeholder: Quiz', 'ebox' ), $lcl_quiz ),
			);

			$lcl_question  = ebox_Custom_Label::get_label( 'question' );
			$lcl_questions = ebox_Custom_Label::get_label( 'questions' );

			$question_labels = array(
				'name'                     => $lcl_questions,
				'singular_name'            => $lcl_question,
				'add_new'                  => esc_html_x( 'Add New', 'placeholder: Question', 'ebox' ),
				// translators: placeholder: Question.
				'add_new_item'             => sprintf( esc_html_x( 'Add New %s', 'placeholder: Question', 'ebox' ), $lcl_question ),
				// translators: placeholder: Question.
				'edit_item'                => sprintf( esc_html_x( 'Edit %s', 'placeholder: Question', 'ebox' ), $lcl_question ),
				// translators: placeholder: Question.
				'new_item'                 => sprintf( esc_html_x( 'New %s', 'placeholder: Question', 'ebox' ), $lcl_question ),
				'all_items'                => $lcl_questions,
				// translators: placeholder: Question.
				'view_item'                => sprintf( esc_html_x( 'View %s', 'placeholder: Question', 'ebox' ), $lcl_question ),
				// translators: placeholder: Questions.
				'view_items'               => sprintf( esc_html_x( 'View %s', 'placeholder: Questions', 'ebox' ), $lcl_questions ),
				// translators: placeholder: Questions.
				'search_items'             => sprintf( esc_html_x( 'Search %s', 'placeholder: Questions', 'ebox' ), $lcl_questions ),
				// translators: placeholder: Questions.
				'not_found'                => sprintf( esc_html_x( 'No %s found', 'placeholder: Questions', 'ebox' ), $lcl_questions ),
				// translators: placeholder: Questions.
				'not_found_in_trash'       => sprintf( esc_html_x( 'No %s found in Trash', 'placeholder: Questions', 'ebox' ), $lcl_questions ),
				'parent_item_colon'        => '',
				'menu_name'                => $lcl_questions,
				// translators: placeholder: Question.
				'item_published'           => sprintf( esc_html_x( '%s Published', 'placeholder: Question', 'ebox' ), $lcl_question ),
				// translators: placeholder: Question.
				'item_published_privately' => sprintf( esc_html_x( '%s Published Privately', 'placeholder: Question', 'ebox' ), $lcl_question ),
				// translators: placeholder: Question.
				'item_reverted_to_draft'   => sprintf( esc_html_x( '%s Reverted to Draft', 'placeholder: Question', 'ebox' ), $lcl_question ),
				// translators: placeholder: Question.
				'item_scheduled'           => sprintf( esc_html_x( '%s Scheduled', 'placeholder: Question', 'ebox' ), $lcl_question ),
				// translators: placeholder: Question.
				'item_updated'             => sprintf( esc_html_x( '%s Updated', 'placeholder: Question', 'ebox' ), $lcl_question ),
			);

			$lcl_lesson  = ebox_Custom_Label::get_label( 'lesson' );
			$lcl_modules = ebox_Custom_Label::get_label( 'modules' );

			$lesson_labels = array(
				'name'                     => $lcl_modules,
				'singular_name'            => $lcl_lesson,
				// translators: placeholder: Lesson.
				'add_new'                  => esc_html_x( 'Add New', 'placeholder: Lesson', 'ebox' ),
				// translators: placeholder: Lesson.
				'add_new_item'             => sprintf( esc_html_x( 'Add New %s', 'placeholder: Lesson', 'ebox' ), $lcl_lesson ),
				// translators: placeholder: Lesson.
				'edit_item'                => sprintf( esc_html_x( 'Edit %s', 'placeholder: Lesson', 'ebox' ), $lcl_lesson ),
				// translators: placeholder: Lesson.
				'new_item'                 => sprintf( esc_html_x( 'New %s', 'placeholder: Lesson', 'ebox' ), $lcl_lesson ),
				'all_items'                => $lcl_modules,
				// translators: placeholder: Lesson.
				'view_item'                => sprintf( esc_html_x( 'View %s', 'placeholder: Lesson', 'ebox' ), $lcl_lesson ),
				// translators: placeholder: modules.
				'view_items'               => sprintf( esc_html_x( 'View %s', 'placeholder: modules.', 'ebox' ), $lcl_modules ),
				// translators: placeholder: modules.
				'search_items'             => sprintf( esc_html_x( 'Search %s', 'placeholder: modules.', 'ebox' ), $lcl_modules ),
				// translators: placeholder: modules.
				'not_found'                => sprintf( esc_html_x( 'No %s found', 'placeholder: modules.', 'ebox' ), $lcl_modules ),
				// translators: placeholder: modules.
				'not_found_in_trash'       => sprintf( esc_html_x( 'No %s found in Trash', 'placeholder: modules.', 'ebox' ), $lcl_modules ),
				'parent_item_colon'        => '',
				'menu_name'                => $lcl_modules,
				// translators: placeholder: Lesson.
				'item_published'           => sprintf( esc_html_x( '%s Published', 'placeholder: Lesson', 'ebox' ), $lcl_lesson ),
				// translators: placeholder: Lesson.
				'item_published_privately' => sprintf( esc_html_x( '%s Published Privately', 'placeholder: Lesson', 'ebox' ), $lcl_lesson ),
				// translators: placeholder: Lesson.
				'item_reverted_to_draft'   => sprintf( esc_html_x( '%s Reverted to Draft', 'placeholder: Lesson', 'ebox' ), $lcl_lesson ),
				// translators: placeholder: Lesson.
				'item_scheduled'           => sprintf( esc_html_x( '%s Scheduled', 'placeholder: Lesson', 'ebox' ), $lcl_lesson ),
				// translators: placeholder: Lesson.
				'item_updated'             => sprintf( esc_html_x( '%s Updated', 'placeholder: Lesson', 'ebox' ), $lcl_lesson ),
			);

			$lcl_exam  = ebox_Custom_Label::get_label( 'exam' );
			$lcl_exams = ebox_Custom_Label::get_label( 'exams' );

			$exam_labels = array(
				'name'                     => $lcl_exams,
				'singular_name'            => $lcl_exam,
				// translators: placeholder: Exam.
				'add_new'                  => esc_html_x( 'Add New', 'placeholder: Exam', 'ebox' ),
				// translators: placeholder: Exam.
				'add_new_item'             => sprintf( esc_html_x( 'Add New %s', 'placeholder: Exam', 'ebox' ), $lcl_exam ),
				// translators: placeholder: Exam.
				'edit_item'                => sprintf( esc_html_x( 'Edit %s', 'placeholder: Exam', 'ebox' ), $lcl_exam ),
				// translators: placeholder: Exam.
				'new_item'                 => sprintf( esc_html_x( 'New %s', 'placeholder: Exam', 'ebox' ), $lcl_exam ),
				'all_items'                => $lcl_exams,
				// translators: placeholder: Exam.
				'view_item'                => sprintf( esc_html_x( 'View %s', 'placeholder: Exam', 'ebox' ), $lcl_exam ),
				// translators: placeholder: Exams.
				'view_items'               => sprintf( esc_html_x( 'View %s', 'placeholder: modules', 'ebox' ), $lcl_exams ),
				// translators: placeholder: Exams.
				'search_items'             => sprintf( esc_html_x( 'Search %s', 'placeholder: modules', 'ebox' ), $lcl_exams ),
				// translators: placeholder: Exams.
				'not_found'                => sprintf( esc_html_x( 'No %s found', 'placeholder: modules', 'ebox' ), $lcl_exams ),
				// translators: placeholder: Exams.
				'not_found_in_trash'       => sprintf( esc_html_x( 'No %s found in Trash', 'placeholder: modules', 'ebox' ), $lcl_exams ),
				'parent_item_colon'        => '',
				'menu_name'                => $lcl_exams,
				// translators: placeholder: Exam.
				'item_published'           => sprintf( esc_html_x( '%s Published', 'placeholder: Exam', 'ebox' ), $lcl_exam ),
				// translators: placeholder: Exam.
				'item_published_privately' => sprintf( esc_html_x( '%s Published Privately', 'placeholder: Exam', 'ebox' ), $lcl_exam ),
				// translators: placeholder: Exam.
				'item_reverted_to_draft'   => sprintf( esc_html_x( '%s Reverted to Draft', 'placeholder: Exam', 'ebox' ), $lcl_exam ),
				// translators: placeholder: Exam.
				'item_scheduled'           => sprintf( esc_html_x( '%s Scheduled', 'placeholder: Exam', 'ebox' ), $lcl_exam ),
				// translators: placeholder: Exam.
				'item_updated'             => sprintf( esc_html_x( '%s Updated', 'placeholder: Exam', 'ebox' ), $lcl_exam ),
			);

			$lcl_coupon  = ebox_Custom_Label::get_label( 'coupon' );
			$lcl_coupons = ebox_Custom_Label::get_label( 'coupons' );

			$coupon_labels = array(
				'name'                     => $lcl_coupons,
				'singular_name'            => $lcl_coupon,
				// translators: placeholder: Coupon.
				'add_new'                  => esc_html_x( 'Add New', 'placeholder: Coupon', 'ebox' ),
				// translators: placeholder: Coupon.
				'add_new_item'             => sprintf( esc_html_x( 'Add New %s', 'placeholder: Coupon', 'ebox' ), $lcl_coupon ),
				// translators: placeholder: Coupon.
				'edit_item'                => sprintf( esc_html_x( 'Edit %s', 'placeholder: Coupon', 'ebox' ), $lcl_coupon ),
				// translators: placeholder: Coupon.
				'new_item'                 => sprintf( esc_html_x( 'New %s', 'placeholder: Coupon', 'ebox' ), $lcl_coupon ),
				'all_items'                => $lcl_coupons,
				// translators: placeholder: Coupon.
				'view_item'                => sprintf( esc_html_x( 'View %s', 'placeholder: Coupon', 'ebox' ), $lcl_coupon ),
				// translators: placeholder: Coupons.
				'view_items'               => sprintf( esc_html_x( 'View %s', 'placeholder: modules', 'ebox' ), $lcl_coupons ),
				// translators: placeholder: Coupons.
				'search_items'             => sprintf( esc_html_x( 'Search %s', 'placeholder: modules', 'ebox' ), $lcl_coupons ),
				// translators: placeholder: Coupons.
				'not_found'                => sprintf( esc_html_x( 'No %s found', 'placeholder: modules', 'ebox' ), $lcl_coupons ),
				// translators: placeholder: Coupons.
				'not_found_in_trash'       => sprintf( esc_html_x( 'No %s found in Trash', 'placeholder: modules', 'ebox' ), $lcl_coupons ),
				'parent_item_colon'        => '',
				'menu_name'                => $lcl_coupons,
				// translators: placeholder: Coupon.
				'item_published'           => sprintf( esc_html_x( '%s Published', 'placeholder: Coupon', 'ebox' ), $lcl_coupon ),
				// translators: placeholder: Coupon.
				'item_published_privately' => sprintf( esc_html_x( '%s Published Privately', 'placeholder: Coupon', 'ebox' ), $lcl_coupon ),
				// translators: placeholder: Coupon.
				'item_reverted_to_draft'   => sprintf( esc_html_x( '%s Reverted to Draft', 'placeholder: Coupon', 'ebox' ), $lcl_coupon ),
				// translators: placeholder: Coupon.
				'item_scheduled'           => sprintf( esc_html_x( '%s Scheduled', 'placeholder: Coupon', 'ebox' ), $lcl_coupon ),
				// translators: placeholder: Coupon.
				'item_updated'             => sprintf( esc_html_x( '%s Updated', 'placeholder: Coupon', 'ebox' ), $lcl_coupon ),
			);

			$lcl_course  = ebox_Custom_Label::get_label( 'course' );
			$lcl_courses = ebox_Custom_Label::get_label( 'courses' );

			$course_labels = array(
				'name'                     => $lcl_courses,
				'singular_name'            => $lcl_course,
				'add_new'                  => esc_html_x( 'Add New', 'placeholder: Course', 'ebox' ),
				// translators: placeholder: Course.
				'add_new_item'             => sprintf( esc_html_x( 'Add New %s', 'placeholder: Course', 'ebox' ), $lcl_course ),
				// translators: placeholder: Course.
				'edit_item'                => sprintf( esc_html_x( 'Edit %s', 'placeholder: Course', 'ebox' ), $lcl_course ),
				// translators: placeholder: Course.
				'new_item'                 => sprintf( esc_html_x( 'New %s', 'placeholder: Course', 'ebox' ), $lcl_course ),
				'all_items'                => $lcl_courses,
				// translators: placeholder: Course.
				'view_item'                => sprintf( esc_html_x( 'View %s', 'placeholder: Course', 'ebox' ), $lcl_course ),
				// translators: placeholder: Courses.
				'view_items'               => sprintf( esc_html_x( 'View %s', 'placeholder: Courses', 'ebox' ), $lcl_courses ),
				// translators: placeholder: Courses.
				'search_items'             => sprintf( esc_html_x( 'Search %s', 'placeholder: Courses', 'ebox' ), $lcl_courses ),
				// translators: placeholder: Courses.
				'not_found'                => sprintf( esc_html_x( 'No %s found', 'placeholder: Courses', 'ebox' ), $lcl_courses ),
				// translators: placeholder: Courses.
				'not_found_in_trash'       => sprintf( esc_html_x( 'No %s found in Trash', 'placeholder: Courses', 'ebox' ), $lcl_courses ),
				'parent_item_colon'        => '',
				'menu_name'                => $lcl_courses,
				// translators: placeholder: Course.
				'item_published'           => sprintf( esc_html_x( '%s Published', 'placeholder: Course', 'ebox' ), $lcl_course ),
				// translators: placeholder: Course.
				'item_published_privately' => sprintf( esc_html_x( '%s Published Privately', 'placeholder: Course', 'ebox' ), $lcl_course ),
				// translators: placeholder: Course.
				'item_reverted_to_draft'   => sprintf( esc_html_x( '%s Reverted to Draft', 'placeholder: Course', 'ebox' ), $lcl_course ),
				// translators: placeholder: Course.
				'item_scheduled'           => sprintf( esc_html_x( '%s Scheduled', 'placeholder: Course', 'ebox' ), $lcl_course ),
				// translators: placeholder: Course.
				'item_updated'             => sprintf( esc_html_x( '%s Updated', 'placeholder: Course', 'ebox' ), $lcl_course ),
			);

			$course_taxonomies = array();
			if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Courses_Taxonomies', 'wp_post_category' ) == 'yes' ) {
				$course_taxonomies['category'] = 'category';
			}

			if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Courses_Taxonomies', 'wp_post_tag' ) == 'yes' ) {
				$course_taxonomies['post_tag'] = 'post_tag';
			}

			$ebox_settings_permalinks_taxonomies = get_option( 'ebox_settings_permalinks_taxonomies' );
			if ( ! is_array( $ebox_settings_permalinks_taxonomies ) ) {
				$ebox_settings_permalinks_taxonomies = array();
			}
			$ebox_settings_permalinks_taxonomies = wp_parse_args(
				$ebox_settings_permalinks_taxonomies,
				array(
					'ld_course_category'   => 'course-category',
					'ld_course_tag'        => 'course-tag',
					'ld_lesson_category'   => 'lesson-category',
					'ld_lesson_tag'        => 'lesson-tag',
					'ld_topic_category'    => 'topic-category',
					'ld_topic_tag'         => 'topic-tag',
					'ld_quiz_category'     => 'quiz-category',
					'ld_quiz_tag'          => 'quiz-tag',
					'ld_question_category' => 'question-category',
					'ld_question_tag'      => 'question-tag',
					'ld_team_category'    => 'team-category',
					'ld_team_tag'         => 'team-tag',
				)
			);

			if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Courses_Taxonomies', 'ld_course_category' ) == 'yes' ) {
				$course_taxonomies['ld_course_category'] = array(
					'public'            => true,
					'hierarchical'      => true,
					'show_ui'           => true,
					'show_in_menu'      => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'show_in_rest'      => ebox_REST_API::enabled( 'ebox-courses' ) || ebox_REST_API::gutenberg_enabled( 'ebox-courses' ),
					'rewrite'           => array( 'slug' => $ebox_settings_permalinks_taxonomies['ld_course_category'] ),
					'capabilities'      => array(
						'manage_terms' => 'manage_categories',
						'edit_terms'   => 'edit_categories',
						'delete_terms' => 'delete_categories',
						'assign_terms' => 'assign_categories',
					),

					'labels'            => array(
						// translators: placeholder: Course.
						'name'              => sprintf( esc_html_x( '%s Categories', 'placeholder: Course', 'ebox' ), $lcl_course ),
						// translators: placeholder: Course.
						'singular_name'     => sprintf( esc_html_x( '%s Category', 'placeholder: Course', 'ebox' ), $lcl_course ),
						// translators: placeholder: Course.
						'search_items'      => sprintf( esc_html_x( 'Search %s Categories', 'placeholder: Course', 'ebox' ), $lcl_course ),
						// translators: placeholder: Course.
						'all_items'         => sprintf( esc_html_x( 'All %s Categories', 'placeholder: Course', 'ebox' ), $lcl_course ),
						// translators: placeholder: Course.
						'parent_item'       => sprintf( esc_html_x( 'Parent %s Category', 'placeholder: Course', 'ebox' ), $lcl_course ),
						// translators: placeholder: Course.
						'parent_item_colon' => sprintf( esc_html_x( 'Parent %s Category:', 'placeholder: Course', 'ebox' ), $lcl_course ),
						// translators: placeholder: Course.
						'edit_item'         => sprintf( esc_html_x( 'Edit %s Category', 'placeholder: Course', 'ebox' ), $lcl_course ),
						// translators: placeholder: Course.
						'update_item'       => sprintf( esc_html_x( 'Update %s Category', 'placeholder: Course', 'ebox' ), $lcl_course ),
						// translators: placeholder: Course.
						'add_new_item'      => sprintf( esc_html_x( 'Add New %s Category', 'placeholder: Course', 'ebox' ), $lcl_course ),
						// translators: placeholder: Course.
						'new_item_name'     => sprintf( esc_html_x( 'New %s Category Name', 'placeholder: Course', 'ebox' ), $lcl_course ),
						// translators: placeholder: Course.
						'menu_name'         => sprintf( esc_html_x( '%s Categories', 'placeholder: Course', 'ebox' ), $lcl_course ),
					),
				);
			}

			if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Courses_Taxonomies', 'ld_course_tag' ) == 'yes' ) {
				$course_taxonomies['ld_course_tag'] = array(
					'public'            => true,
					'hierarchical'      => false,
					'show_ui'           => true,
					'show_in_menu'      => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'show_in_rest'      => ebox_REST_API::enabled( 'ebox-courses' ) || ebox_REST_API::gutenberg_enabled( 'ebox-courses' ),
					'rewrite'           => array( 'slug' => $ebox_settings_permalinks_taxonomies['ld_course_tag'] ),
					'labels'            => array(
						// translators: placeholder: Course.
						'name'              => sprintf( esc_html_x( '%s Tags', 'placeholder: Course', 'ebox' ), $lcl_course ),
						// translators: placeholder: Course.
						'singular_name'     => sprintf( esc_html_x( '%s Tag', 'placeholder: Course', 'ebox' ), $lcl_course ),
						// translators: placeholder: Course.
						'search_items'      => sprintf( esc_html_x( 'Search %s Tag', 'placeholder: Course', 'ebox' ), $lcl_course ),
						// translators: placeholder: Course.
						'all_items'         => sprintf( esc_html_x( 'All %s Tags', 'placeholder: Course', 'ebox' ), $lcl_course ),
						// translators: placeholder: Course.
						'parent_item'       => sprintf( esc_html_x( 'Parent %s Tag', 'placeholder: Course', 'ebox' ), $lcl_course ),
						// translators: placeholder: Course.
						'parent_item_colon' => sprintf( esc_html_x( 'Parent %s Tag:', 'placeholder: Course', 'ebox' ), $lcl_course ),
						// translators: placeholder: Course.
						'edit_item'         => sprintf( esc_html_x( 'Edit %s Tag', 'placeholder: Course', 'ebox' ), $lcl_course ),
						// translators: placeholder: Course.
						'update_item'       => sprintf( esc_html_x( 'Update %s Tag', 'placeholder: Course', 'ebox' ), $lcl_course ),
						// translators: placeholder: Course.
						'add_new_item'      => sprintf( esc_html_x( 'Add New %s Tag', 'placeholder: Course', 'ebox' ), $lcl_course ),
						// translators: placeholder: Course.
						'new_item_name'     => sprintf( esc_html_x( 'New %s Tag Name', 'placeholder: Course', 'ebox' ), $lcl_course ),
						// translators: placeholder: Course.
						'menu_name'         => sprintf( esc_html_x( '%s Tags', 'placeholder: Course', 'ebox' ), $lcl_course ),
					),
				);
			}

			$lesson_taxonomies = array();
			if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_modules_Taxonomies', 'wp_post_category' ) == 'yes' ) {
				$lesson_taxonomies['category'] = 'category';
			}

			if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_modules_Taxonomies', 'wp_post_tag' ) == 'yes' ) {
				$lesson_taxonomies['post_tag'] = 'post_tag';
			}

			if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_modules_Taxonomies', 'ld_lesson_category' ) == 'yes' ) {
				$lesson_taxonomies['ld_lesson_category'] = array(
					'public'            => true,
					'hierarchical'      => true,
					'show_ui'           => true,
					'show_in_menu'      => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'show_in_rest'      => ebox_REST_API::enabled( 'ebox-modules' ) || ebox_REST_API::gutenberg_enabled( 'ebox-modules' ),
					'rewrite'           => array( 'slug' => $ebox_settings_permalinks_taxonomies['ld_lesson_category'] ),
					'capabilities'      => array(
						'manage_terms' => 'manage_categories',
						'edit_terms'   => 'edit_categories',
						'delete_terms' => 'delete_categories',
						'assign_terms' => 'assign_categories',
					),
					'labels'            => array(
						// translators: placeholder: Lesson.
						'name'              => sprintf( esc_html_x( '%s Categories', 'placeholder: Lesson', 'ebox' ), $lcl_lesson ),
						// translators: placeholder: Lesson.
						'singular_name'     => sprintf( esc_html_x( '%s Category', 'placeholder: Lesson', 'ebox' ), $lcl_lesson ),
						// translators: placeholder: Lesson.
						'search_items'      => sprintf( esc_html_x( 'Search %s Categories', 'placeholder: Lesson', 'ebox' ), $lcl_lesson ),
						// translators: placeholder: Lesson.
						'all_items'         => sprintf( esc_html_x( 'All %s Categories', 'placeholder: Lesson', 'ebox' ), $lcl_lesson ),
						// translators: placeholder: Lesson.
						'parent_item'       => sprintf( esc_html_x( 'Parent %s Category', 'placeholder: Lesson', 'ebox' ), $lcl_lesson ),
						// translators: placeholder: Lesson.
						'parent_item_colon' => sprintf( esc_html_x( 'Parent %s Category:', 'placeholder: Lesson', 'ebox' ), $lcl_lesson ),
						// translators: placeholder: Lesson.
						'edit_item'         => sprintf( esc_html_x( 'Edit %s Category', 'placeholder: Lesson', 'ebox' ), $lcl_lesson ),
						// translators: placeholder: Lesson.
						'update_item'       => sprintf( esc_html_x( 'Update %s Category', 'placeholder: Lesson', 'ebox' ), $lcl_lesson ),
						// translators: placeholder: Lesson.
						'add_new_item'      => sprintf( esc_html_x( 'Add New %s Category', 'placeholder: Lesson', 'ebox' ), $lcl_lesson ),
						// translators: placeholder: Lesson.
						'new_item_name'     => sprintf( esc_html_x( 'New %s Category Name', 'placeholder: Lesson', 'ebox' ), $lcl_lesson ),
						// translators: placeholder: Lesson.
						'menu_name'         => sprintf( esc_html_x( '%s Categories', 'placeholder: Lesson', 'ebox' ), $lcl_lesson ),
					),
				);
			}

			if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_modules_Taxonomies', 'ld_lesson_tag' ) == 'yes' ) {
				$lesson_taxonomies['ld_lesson_tag'] = array(
					'public'            => true,
					'hierarchical'      => false,
					'show_ui'           => true,
					'show_in_menu'      => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'show_in_rest'      => ebox_REST_API::enabled( 'ebox-modules' ) || ebox_REST_API::gutenberg_enabled( 'ebox-modules' ),
					'rewrite'           => array( 'slug' => $ebox_settings_permalinks_taxonomies['ld_lesson_tag'] ),
					'labels'            => array(
						// translators: placeholder: Lesson.
						'name'              => sprintf( esc_html_x( '%s Tags', 'placeholder: Lesson', 'ebox' ), $lcl_lesson ),
						// translators: placeholder: Lesson.
						'singular_name'     => sprintf( esc_html_x( '%s Tag', 'placeholder: Lesson', 'ebox' ), $lcl_lesson ),
						// translators: placeholder: Lesson.
						'search_items'      => sprintf( esc_html_x( 'Search %s Tag', 'placeholder: Lesson', 'ebox' ), $lcl_lesson ),
						// translators: placeholder: Lesson.
						'all_items'         => sprintf( esc_html_x( 'All %s Tags', 'placeholder: Lesson', 'ebox' ), $lcl_lesson ),
						// translators: placeholder: Lesson.
						'parent_item'       => sprintf( esc_html_x( 'Parent %s Tag', 'placeholder: Lesson', 'ebox' ), $lcl_lesson ),
						// translators: placeholder: Lesson.
						'parent_item_colon' => sprintf( esc_html_x( 'Parent %s Tag:', 'placeholder: Lesson', 'ebox' ), $lcl_lesson ),
						// translators: placeholder: Lesson.
						'edit_item'         => sprintf( esc_html_x( 'Edit %s Tag', 'placeholder: Lesson', 'ebox' ), $lcl_lesson ),
						// translators: placeholder: Lesson.
						'update_item'       => sprintf( esc_html_x( 'Update %s Tag', 'placeholder: Lesson', 'ebox' ), $lcl_lesson ),
						// translators: placeholder: Lesson.
						'add_new_item'      => sprintf( esc_html_x( 'Add New %s Tag', 'placeholder: Lesson', 'ebox' ), $lcl_lesson ),
						// translators: placeholder: Lesson.
						'new_item_name'     => sprintf( esc_html_x( 'New %s Tag Name', 'placeholder: Lesson', 'ebox' ), $lcl_lesson ),
						// translators: placeholder: Lesson.
						'menu_name'         => sprintf( esc_html_x( '%s Tags', 'placeholder: Lesson', 'ebox' ), $lcl_lesson ),
					),
				);
			}

			$topic_taxonomies = array();
			if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Topics_Taxonomies', 'wp_post_category' ) == 'yes' ) {
				$topic_taxonomies['category'] = 'category';
			}

			if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Topics_Taxonomies', 'wp_post_tag' ) == 'yes' ) {
				$topic_taxonomies['post_tag'] = 'post_tag';
			}

			if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Topics_Taxonomies', 'ld_topic_category' ) == 'yes' ) {
				$topic_taxonomies['ld_topic_category'] = array(
					'public'            => true,
					'hierarchical'      => true,
					'show_ui'           => true,
					'show_in_menu'      => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'show_in_rest'      => ebox_REST_API::enabled( 'ebox-topic' ) || ebox_REST_API::gutenberg_enabled( 'ebox-topic' ),
					'rewrite'           => array( 'slug' => $ebox_settings_permalinks_taxonomies['ld_topic_category'] ),
					'capabilities'      => array(
						'manage_terms' => 'manage_categories',
						'edit_terms'   => 'edit_categories',
						'delete_terms' => 'delete_categories',
						'assign_terms' => 'assign_categories',
					),
					'labels'            => array(
						// translators: placeholder: Topic.
						'name'              => sprintf( esc_html_x( '%s Categories', 'placeholder: Topic', 'ebox' ), $lcl_topic ),
						// translators: placeholder: Topic.
						'singular_name'     => sprintf( esc_html_x( '%s Category', 'placeholder: Topic', 'ebox' ), $lcl_topic ),
						// translators: placeholder: Topic.
						'search_items'      => sprintf( esc_html_x( 'Search %s Categories', 'placeholder: Topic', 'ebox' ), $lcl_topic ),
						// translators: placeholder: Topic.
						'all_items'         => sprintf( esc_html_x( 'All %s Categories', 'placeholder: Topic', 'ebox' ), $lcl_topic ),
						// translators: placeholder: Topic.
						'parent_item'       => sprintf( esc_html_x( 'Parent %s Category', 'placeholder: Topic', 'ebox' ), $lcl_topic ),
						// translators: placeholder: Topic.
						'parent_item_colon' => sprintf( esc_html_x( 'Parent %s Category:', 'placeholder: Topic', 'ebox' ), $lcl_topic ),
						// translators: placeholder: Topic.
						'edit_item'         => sprintf( esc_html_x( 'Edit %s Category', 'placeholder: Topic', 'ebox' ), $lcl_topic ),
						// translators: placeholder: Topic.
						'update_item'       => sprintf( esc_html_x( 'Update %s Category', 'placeholder: Topic', 'ebox' ), $lcl_topic ),
						// translators: placeholder: Topic.
						'add_new_item'      => sprintf( esc_html_x( 'Add New %s Category', 'placeholder: Topic', 'ebox' ), $lcl_topic ),
						// translators: placeholder: Topic.
						'new_item_name'     => sprintf( esc_html_x( 'New %s Category Name', 'placeholder: Topic', 'ebox' ), $lcl_topic ),
						// translators: placeholder: Topic.
						'menu_name'         => sprintf( esc_html_x( '%s Categories', 'placeholder: Topic', 'ebox' ), $lcl_topic ),
					),
				);
			}

			if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Topics_Taxonomies', 'ld_topic_tag' ) == 'yes' ) {
				$topic_taxonomies['ld_topic_tag'] = array(
					'public'            => true,
					'hierarchical'      => false,
					'show_ui'           => true,
					'show_in_menu'      => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'show_in_rest'      => ebox_REST_API::enabled( 'ebox-topic' ) || ebox_REST_API::gutenberg_enabled( 'ebox-topic' ),
					'rewrite'           => array( 'slug' => $ebox_settings_permalinks_taxonomies['ld_topic_tag'] ),
					'labels'            => array(
						// translators: placeholder: Topic.
						'name'              => sprintf( esc_html_x( '%s Tags', 'placeholder: Topic', 'ebox' ), $lcl_topic ),
						// translators: placeholder: Topic.
						'singular_name'     => sprintf( esc_html_x( '%s Tag', 'placeholder: Topic', 'ebox' ), $lcl_topic ),
						// translators: placeholder: Topic.
						'search_items'      => sprintf( esc_html_x( 'Search %s Tag', 'placeholder: Topic', 'ebox' ), $lcl_topic ),
						// translators: placeholder: Topic.
						'all_items'         => sprintf( esc_html_x( 'All %s Tags', 'placeholder: Topic', 'ebox' ), $lcl_topic ),
						// translators: placeholder: Topic.
						'parent_item'       => sprintf( esc_html_x( 'Parent %s Tag', 'placeholder: Topic', 'ebox' ), $lcl_topic ),
						// translators: placeholder: Topic.
						'parent_item_colon' => sprintf( esc_html_x( 'Parent %s Tag:', 'placeholder: Topic', 'ebox' ), $lcl_topic ),
						// translators: placeholder: Topic.
						'edit_item'         => sprintf( esc_html_x( 'Edit %s Tag', 'placeholder: Topic', 'ebox' ), $lcl_topic ),
						// translators: placeholder: Topic.
						'update_item'       => sprintf( esc_html_x( 'Update %s Tag', 'placeholder: Topic', 'ebox' ), $lcl_topic ),
						// translators: placeholder: Topic.
						'add_new_item'      => sprintf( esc_html_x( 'Add New %s Tag', 'placeholder: Topic', 'ebox' ), $lcl_topic ),
						// translators: placeholder: Topic.
						'new_item_name'     => sprintf( esc_html_x( 'New %s Tag Name', 'placeholder: Topic', 'ebox' ), $lcl_topic ),
						// translators: placeholder: Topic.
						'menu_name'         => sprintf( esc_html_x( '%s Tags', 'placeholder: Topic', 'ebox' ), $lcl_topic ),
					),
				);
			}

			$quiz_taxonomies = array();
			if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Quizzes_Taxonomies', 'ld_quiz_category' ) == 'yes' ) {
				$quiz_taxonomies['ld_quiz_category'] = array(
					'public'            => true,
					'hierarchical'      => true,
					'show_ui'           => true,
					'show_in_menu'      => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'show_in_rest'      => ebox_REST_API::enabled( 'ebox-quiz' ) || ebox_REST_API::gutenberg_enabled( 'ebox-quiz' ),
					'rewrite'           => array( 'slug' => $ebox_settings_permalinks_taxonomies['ld_quiz_category'] ),
					'capabilities'      => array(
						'manage_terms' => 'manage_categories',
						'edit_terms'   => 'edit_categories',
						'delete_terms' => 'delete_categories',
						'assign_terms' => 'assign_categories',
					),
					'labels'            => array(
						// translators: placeholder: Quiz.
						'name'              => sprintf( esc_html_x( '%s Categories', 'placeholder: Quiz', 'ebox' ), $lcl_quiz ),
						// translators: placeholder: Quiz.
						'singular_name'     => sprintf( esc_html_x( '%s Category', 'placeholder: Quiz', 'ebox' ), $lcl_quiz ),
						// translators: placeholder: Quiz.
						'search_items'      => sprintf( esc_html_x( 'Search %s Categories', 'placeholder: Quiz', 'ebox' ), $lcl_quiz ),
						// translators: placeholder: Quiz.
						'all_items'         => sprintf( esc_html_x( 'All %s Categories', 'placeholder: Quiz', 'ebox' ), $lcl_quiz ),
						// translators: placeholder: Quiz.
						'parent_item'       => sprintf( esc_html_x( 'Parent %s Category', 'placeholder: Quiz', 'ebox' ), $lcl_quiz ),
						// translators: placeholder: Quiz.
						'parent_item_colon' => sprintf( esc_html_x( 'Parent %s Category:', 'placeholder: Quiz', 'ebox' ), $lcl_quiz ),
						// translators: placeholder: Quiz.
						'edit_item'         => sprintf( esc_html_x( 'Edit %s Category', 'placeholder: Quiz', 'ebox' ), $lcl_quiz ),
						// translators: placeholder: Quiz.
						'update_item'       => sprintf( esc_html_x( 'Update %s Category', 'placeholder: Quiz', 'ebox' ), $lcl_quiz ),
						// translators: placeholder: Quiz.
						'add_new_item'      => sprintf( esc_html_x( 'Add New %s Category', 'placeholder: Quiz', 'ebox' ), $lcl_quiz ),
						// translators: placeholder: Quiz.
						'new_item_name'     => sprintf( esc_html_x( 'New %s Category Name', 'placeholder: Quiz', 'ebox' ), $lcl_quiz ),
						// translators: placeholder: Quiz.
						'menu_name'         => sprintf( esc_html_x( '%s Categories', 'placeholder: Quiz', 'ebox' ), $lcl_quiz ),
					),
				);
			}

			if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Quizzes_Taxonomies', 'ld_quiz_tag' ) == 'yes' ) {
				$quiz_taxonomies['ld_quiz_tag'] = array(
					'public'            => true,
					'hierarchical'      => false,
					'show_ui'           => true,
					'show_in_menu'      => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'show_in_rest'      => ebox_REST_API::enabled( 'ebox-quiz' ) || ebox_REST_API::gutenberg_enabled( 'ebox-quiz' ),
					'rewrite'           => array( 'slug' => $ebox_settings_permalinks_taxonomies['ld_quiz_tag'] ),
					'labels'            => array(
						// translators: placeholder: Quiz.
						'name'              => sprintf( esc_html_x( '%s Tags', 'placeholder: Quiz', 'ebox' ), $lcl_quiz ),
						// translators: placeholder: Quiz.
						'singular_name'     => sprintf( esc_html_x( '%s Tag', 'placeholder: Quiz', 'ebox' ), $lcl_quiz ),
						// translators: placeholder: Quiz.
						'search_items'      => sprintf( esc_html_x( 'Search %s Tag', 'placeholder: Quiz', 'ebox' ), $lcl_quiz ),
						// translators: placeholder: Quiz.
						'all_items'         => sprintf( esc_html_x( 'All %s Tags', 'placeholder: Quiz', 'ebox' ), $lcl_quiz ),
						// translators: placeholder: Quiz.
						'parent_item'       => sprintf( esc_html_x( 'Parent %s Tag', 'placeholder: Quiz', 'ebox' ), $lcl_quiz ),
						// translators: placeholder: Quiz.
						'parent_item_colon' => sprintf( esc_html_x( 'Parent %s Tag:', 'placeholder: Quiz', 'ebox' ), $lcl_quiz ),
						// translators: placeholder: Quiz.
						'edit_item'         => sprintf( esc_html_x( 'Edit %s Tag', 'placeholder: Quiz', 'ebox' ), $lcl_quiz ),
						// translators: placeholder: Quiz.
						'update_item'       => sprintf( esc_html_x( 'Update %s Tag', 'placeholder: Quiz', 'ebox' ), $lcl_quiz ),
						// translators: placeholder: Quiz.
						'add_new_item'      => sprintf( esc_html_x( 'Add New %s Tag', 'placeholder: Quiz', 'ebox' ), $lcl_quiz ),
						// translators: placeholder: Quiz.
						'new_item_name'     => sprintf( esc_html_x( 'New %s Tag Name', 'placeholder: Quiz', 'ebox' ), $lcl_quiz ),
						// translators: placeholder: Quiz.
						'menu_name'         => sprintf( esc_html_x( '%s Tags', 'placeholder: Quiz', 'ebox' ), $lcl_quiz ),
					),
				);
			}
			if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Quizzes_Taxonomies', 'wp_post_category' ) == 'yes' ) {
				$quiz_taxonomies['category'] = 'category';
			}

			if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Quizzes_Taxonomies', 'wp_post_tag' ) == 'yes' ) {
				$quiz_taxonomies['post_tag'] = 'post_tag';
			}

			$question_taxonomies = array();
			if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Questions_Taxonomies', 'ld_question_category' ) == 'yes' ) {
				$question_taxonomies['ld_question_category'] = array(
					'public'            => false,
					'hierarchical'      => true,
					'show_ui'           => true,
					'show_in_menu'      => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'show_in_rest'      => ebox_REST_API::enabled( 'ebox-question' ) || ebox_REST_API::gutenberg_enabled( 'ebox-question' ),
					'rewrite'           => array( 'slug' => $ebox_settings_permalinks_taxonomies['ld_question_category'] ),
					'capabilities'      => array(
						'manage_terms' => 'manage_categories',
						'edit_terms'   => 'edit_categories',
						'delete_terms' => 'delete_categories',
						'assign_terms' => 'assign_categories',
					),
					'labels'            => array(
						// translators: placeholder: Question.
						'name'              => sprintf( esc_html_x( '%s Categories', 'placeholder: Question', 'ebox' ), $lcl_question ),
						// translators: placeholder: Question.
						'singular_name'     => sprintf( esc_html_x( '%s Category', 'placeholder: Question', 'ebox' ), $lcl_question ),
						// translators: placeholder: Question.
						'search_items'      => sprintf( esc_html_x( 'Search %s Categories', 'placeholder: Question', 'ebox' ), $lcl_question ),
						// translators: placeholder: Question.
						'all_items'         => sprintf( esc_html_x( 'All %s Categories', 'placeholder: Question', 'ebox' ), $lcl_question ),
						// translators: placeholder: Question.
						'parent_item'       => sprintf( esc_html_x( 'Parent %s Category', 'placeholder: Question', 'ebox' ), $lcl_question ),
						// translators: placeholder: Question.
						'parent_item_colon' => sprintf( esc_html_x( 'Parent %s Category:', 'placeholder: Question', 'ebox' ), $lcl_question ),
						// translators: placeholder: Question.
						'edit_item'         => sprintf( esc_html_x( 'Edit %s Category', 'placeholder: Question', 'ebox' ), $lcl_question ),
						// translators: placeholder: Question.
						'update_item'       => sprintf( esc_html_x( 'Update %s Category', 'placeholder: Question', 'ebox' ), $lcl_question ),
						// translators: placeholder: Question.
						'add_new_item'      => sprintf( esc_html_x( 'Add New %s Category', 'placeholder: Question', 'ebox' ), $lcl_question ),
						// translators: placeholder: Question.
						'new_item_name'     => sprintf( esc_html_x( 'New %s Category Name', 'placeholder: Question', 'ebox' ), $lcl_question ),
						// translators: placeholder: Question.
						'menu_name'         => sprintf( esc_html_x( '%s Categories', 'placeholder: Question', 'ebox' ), $lcl_question ),
					),
				);
			}

			if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Questions_Taxonomies', 'ld_question_tag' ) == 'yes' ) {
				$question_taxonomies['ld_question_tag'] = array(
					'public'            => false,
					'hierarchical'      => false,
					'show_ui'           => true,
					'show_in_menu'      => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'show_in_rest'      => ebox_REST_API::enabled( 'ebox-question' ) || ebox_REST_API::gutenberg_enabled( 'ebox-question' ),
					'rewrite'           => array( 'slug' => $ebox_settings_permalinks_taxonomies['ld_question_tag'] ),
					'labels'            => array(
						// translators: placeholder: Question.
						'name'              => sprintf( esc_html_x( '%s Tags', 'placeholder: Question', 'ebox' ), $lcl_question ),
						// translators: placeholder: Question.
						'singular_name'     => sprintf( esc_html_x( '%s Tag', 'placeholder: Question', 'ebox' ), $lcl_question ),
						// translators: placeholder: Question.
						'search_items'      => sprintf( esc_html_x( 'Search %s Tag', 'placeholder: Question', 'ebox' ), $lcl_question ),
						// translators: placeholder: Question.
						'all_items'         => sprintf( esc_html_x( 'All %s Tags', 'placeholder: Question', 'ebox' ), $lcl_question ),
						// translators: placeholder: Question.
						'parent_item'       => sprintf( esc_html_x( 'Parent %s Tag', 'placeholder: Question', 'ebox' ), $lcl_question ),
						// translators: placeholder: Question.
						'parent_item_colon' => sprintf( esc_html_x( 'Parent %s Tag:', 'placeholder: Question', 'ebox' ), $lcl_question ),
						// translators: placeholder: Question.
						'edit_item'         => sprintf( esc_html_x( 'Edit %s Tag', 'placeholder: Question', 'ebox' ), $lcl_question ),
						// translators: placeholder: Question.
						'update_item'       => sprintf( esc_html_x( 'Update %s Tag', 'placeholder: Question', 'ebox' ), $lcl_question ),
						// translators: placeholder: Question.
						'add_new_item'      => sprintf( esc_html_x( 'Add New %s Tag', 'placeholder: Question', 'ebox' ), $lcl_question ),
						// translators: placeholder: Question.
						'new_item_name'     => sprintf( esc_html_x( 'New %s Tag Name', 'placeholder: Question', 'ebox' ), $lcl_question ),
						// translators: placeholder: Question.
						'menu_name'         => sprintf( esc_html_x( '%s Tags', 'placeholder: Question', 'ebox' ), $lcl_question ),
					),
				);
			}
			if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Questions_Taxonomies', 'wp_post_category' ) == 'yes' ) {
				$question_taxonomies['category'] = 'category';
			}

			if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Questions_Taxonomies', 'wp_post_tag' ) == 'yes' ) {
				$question_taxonomies['post_tag'] = 'post_tag';
			}

			$course_modules_options_labels = array(
				'orderby' => ebox_Settings_Section::get_section_setting_select_option_label( 'ebox_Settings_Section_modules_Display_Order', 'orderby' ),
				'order'   => ebox_Settings_Section::get_section_setting_select_option_label( 'ebox_Settings_Section_modules_Display_Order', 'order' ),
			);

			$exam_post_type_slug   = ebox_get_post_type_slug( 'exam' );
			$coupon_post_type_slug = ebox_get_post_type_slug( LDLMS_Post_Types::COUPON );

			$this->post_args = array(
				'ebox-courses'       => array(
					'plugin_name'        => ebox_Custom_Label::get_label( 'course' ),
					'slug_name'          => ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_Permalinks', 'courses' ),
					'post_type'          => 'ebox-courses',
					'template_redirect'  => true,
					'taxonomies'         => $course_taxonomies,
					'cpt_options'        => array(
						'has_archive'         => ebox_post_type_has_archive( 'ebox-courses' ),
						'hierarchical'        => false,
						'supports'            => array_merge(
							array( 'title', 'editor', 'author', 'page-attributes' ),
							ebox_Settings_Section::get_section_setting( 'ebox_Settings_Courses_CPT', 'supports' )
						),
						'labels'              => $course_labels,
						'capability_type'     => 'course',
						'exclude_from_search' => ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Courses_CPT', 'include_in_search' ) !== 'yes' ) ? true : false,
						'capabilities'        => $course_capabilities,
						'map_meta_cap'        => true,
						'show_in_rest'        => ebox_REST_API::enabled( 'ebox-courses' ) || ebox_REST_API::gutenberg_enabled( 'ebox-courses' ),
					),
					'options_page_title' => sprintf(
						// translators: placeholder: Course.
						esc_html_x( 'ebox %s Settings', 'placeholder: Course', 'ebox' ),
						ebox_Custom_Label::get_label( 'course' )
					),
					'fields'             => array(
						'course_materials'              => array(
							// translators: placeholder: Course.
							'name'         => sprintf( esc_html_x( '%s Materials', 'placeholder: Course', 'ebox' ), ebox_Custom_Label::get_label( 'course' ) ),
							'type'         => 'textarea',
							// translators: placeholder: Course.
							'help_text'    => sprintf( esc_html_x( 'Options for %s materials', 'placeholder: Course', 'ebox' ), ebox_Custom_Label::get_label( 'course' ) ),
							'show_in_rest' => ebox_REST_API::enabled(),
							'rest_args'    => array(
								'schema' => array(
									'type' => 'html',
								),
							),
						),
						'course_price_type'             => array(
							// translators: placeholder: Course.
							'name'            => sprintf( esc_html_x( '%s Price Type', 'placeholder: Course', 'ebox' ), ebox_Custom_Label::get_label( 'course' ) ),
							'type'            => 'select',
							'initial_options' => array(
								'open'      => esc_html__( 'Open', 'ebox' ),
								'closed'    => esc_html__( 'Closed', 'ebox' ),
								'free'      => esc_html__( 'Free', 'ebox' ),
								'paynow'    => esc_html__( 'Buy Now', 'ebox' ),
								'subscribe' => esc_html__( 'Recurring', 'ebox' ),
							),
							'default'         => 'open',
							'help_text'       => esc_html__( 'Is it open to all, free join, one time purchase, or a recurring subscription?', 'ebox' ),
							'show_in_rest'    => ebox_REST_API::enabled(),
							'rest_args'       => array(
								'schema' => array(
									'type'    => 'string',
									'default' => 'open',
									'enum'    => array(
										'open',
										'closed',
										'free',
										'buynow',
										'subscribe',
									),
								),
							),
						),
						'custom_button_label'           => array(
							'name'         => esc_html__( 'Custom Button Label', 'ebox' ),
							'type'         => 'text',
							'placeholder'  => esc_html__( 'Optional', 'ebox' ),
							'show_in_rest' => ebox_REST_API::enabled(),
						),
						'custom_button_url'             => array(
							'name'         => esc_html__( 'Custom Button URL', 'ebox' ),
							'type'         => 'text',
							'placeholder'  => esc_html__( 'Optional', 'ebox' ),
							// translators: placeholder: "Take This Course" button label.
							'help_text'    => sprintf( esc_html_x( 'Entering a URL in this field will enable the "%s" button. The button will not display if this field is left empty. Relative URL beginning with a slash is acceptable.', 'placeholder: "Take This Course" button label', 'ebox' ), ebox_Custom_Label::get_label( 'button_take_this_course' ) ),
							'show_in_rest' => ebox_REST_API::enabled(),
						),
						'course_price'                  => array(
							// translators: placeholder: Course.
							'name'         => sprintf( esc_html_x( '%s Price', 'placeholder: Course', 'ebox' ), ebox_Custom_Label::get_label( 'course' ) ),
							'type'         => 'text',
							// translators: placeholders: Course, Course.
							'help_text'    => sprintf( esc_html_x( 'Enter %1$s price here. Leave empty if the %2$s is free.', 'placeholders: Course, Course', 'ebox' ), ebox_Custom_Label::get_label( 'course' ), ebox_Custom_Label::get_label( 'course' ) ),
							'show_in_rest' => ebox_REST_API::enabled(),
						),
						'course_price_billing_cycle'    => array(
							'name'         => esc_html__( 'Billing Cycle', 'ebox' ),
							'type'         => 'html',
							'default'      => '',
							'help_text'    => esc_html__( 'Billing Cycle for the recurring payments in case of a subscription.', 'ebox' ),
							'show_in_rest' => false,
						),
						'course_access_list'            => array(
							// translators: placeholder: Course.
							'name'         => sprintf( esc_html_x( '%s Access List', 'placeholder: Course', 'ebox' ), ebox_Custom_Label::get_label( 'course' ) ),
							'type'         => 'textarea',
							'help_text'    => esc_html__( 'This field is auto-populated with the UserIDs of those who have access to this course.', 'ebox' ),
							'show_in_rest' => false,
						),
						'course_lesson_orderby'         => array(
							// translators: placeholder: Lesson.
							'name'            => sprintf( esc_html_x( 'Sort %s By', 'placeholder: Lesson', 'ebox' ), ebox_Custom_Label::get_label( 'lesson' ) ),
							'type'            => 'select',
							'initial_options' => array(
								''           => esc_html__( 'Use Default', 'ebox' ) . ' ( ' . $course_modules_options_labels['orderby'] . ' )',
								'title'      => esc_html__( 'Title', 'ebox' ),
								'date'       => esc_html__( 'Date', 'ebox' ),
								'menu_order' => esc_html__( 'Menu Order', 'ebox' ),
							),
							'default'         => '',
							// translators: placeholders: modules, course.
							'help_text'       => sprintf( esc_html_x( 'Choose the sort order of %1$s in this %2$s.', 'placeholders: modules, course', 'ebox' ), ebox_get_custom_label_lower( 'modules' ), ebox_get_custom_label_lower( 'course' ) ),
							'show_in_rest'    => false,
						),
						'course_lesson_order'           => array(
							// translators: placeholder: Lesson.
							'name'            => sprintf( esc_html_x( 'Sort %s Direction', 'placeholder: Lesson', 'ebox' ), ebox_Custom_Label::get_label( 'lesson' ) ),
							'type'            => 'select',
							'initial_options' => array(
								''     => esc_html__( 'Use Default', 'ebox' ) . ' ( ' . $course_modules_options_labels['order'] . ' )',
								'ASC'  => esc_html__( 'Ascending', 'ebox' ),
								'DESC' => esc_html__( 'Descending', 'ebox' ),
							),
							'default'         => '',
							// translators: placeholders: modules, course.
							'help_text'       => sprintf( esc_html_x( 'Choose the sort order of %1$s in this %2$s.', 'placeholders: modules, course', 'ebox' ), ebox_get_custom_label_lower( 'modules' ), ebox_get_custom_label_lower( 'course' ) ),
							'show_in_rest'    => false,
						),

						'course_lesson_per_page'        => array(
							// translators: placeholder: modules.
							'name'            => sprintf( esc_html_x( '%s Per Page', 'placeholder: modules', 'ebox' ), ebox_Custom_Label::get_label( 'modules' ) ),
							'type'            => 'select',
							'initial_options' => array(
								''       => esc_html__( 'Use Default', 'ebox' ) . ' ( ' . ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_modules_Display_Order', 'posts_per_page' ) . ' )',
								'CUSTOM' => esc_html__( 'Custom', 'ebox' ),
							),
							'default'         => '',
							// translators: placeholders: modules, course.
							'help_text'       => sprintf( esc_html_x( 'Choose the per page of %1$s in this %2$s.', 'placeholders: modules, course', 'ebox' ), ebox_get_custom_label_lower( 'modules' ), ebox_get_custom_label_lower( 'course' ) ),
							'show_in_rest'    => false,
						),
						'course_lesson_per_page_custom' => array(
							// translators: placeholder: modules.
							'name'         => sprintf( esc_html_x( 'Custom %s Per Page', 'placeholder: modules', 'ebox' ), ebox_Custom_Label::get_label( 'modules' ) ),
							'type'         => 'number',
							'min'          => '0',
							// translators: placeholder: Lesson.
							'help_text'    => sprintf( esc_html_x( 'Enter %s per page value. Set to zero for no paging', 'placeholder: Lesson', 'ebox' ), ebox_Custom_Label::get_label( 'lesson' ) ),
							'default'      => 0,
							'show_in_rest' => false,
						),

						'course_prerequisite_enabled'   => array(
							// translators: placeholder: Course.
							'name'          => sprintf( esc_html_x( 'Enable %s Prerequisites', 'placeholder: Course', 'ebox' ), ebox_Custom_Label::get_label( 'course' ) ),
							'type'          => 'checkbox',
							'checked_value' => 'on',
							'help_text'     => esc_html__( 'Leave this field unchecked if prerequisite not used.', 'ebox' ),
							'show_in_rest'  => ebox_REST_API::enabled(),
							'rest_args'     => array(
								'schema' => array(
									'type'    => 'boolean',
									'default' => false,
								),
							),
						),
						'course_prerequisite'           => array(
							// translators: placeholder: Course.
							'name'            => sprintf( esc_html_x( '%s Prerequisites', 'placeholder: Course', 'ebox' ), ebox_Custom_Label::get_label( 'course' ) ),
							'type'            => 'multiselect',
							// translators: placeholders: course, course.
							'help_text'       => sprintf( esc_html_x( 'Select one or more %1$s as prerequisites to view this %2$s', 'placeholders: course, course', 'ebox' ), ebox_get_custom_label_lower( 'course' ), ebox_get_custom_label_lower( 'course' ) ),
							'lazy_load'       => true,
							'initial_options' => '',
							'default'         => '',
							'show_in_rest'    => ebox_REST_API::enabled(),
							'rest_args'       => array(
								'schema' => array(
									'default' => array(),
									'type'    => 'array',
								),
							),
						),
						'course_prerequisite_compare'   => array(
							// translators: placeholder: Course.
							'name'            => sprintf( esc_html_x( '%s Prerequisites Compare', 'placeholder: Course', 'ebox' ), ebox_Custom_Label::get_label( 'course' ) ),
							'type'            => 'select',
							'initial_options' => array(
								'ANY' => esc_html__( 'ANY (default) - The student must complete at least one of the prerequisites', 'ebox' ),
								'ALL' => esc_html__( 'ALL - The student must complete all the prerequisites', 'ebox' ),
							),
							'default'         => 'ANY',
							// translators: placeholder: Course.
							'help_text'       => sprintf( esc_html_x( 'Select how to compare the selected prerequisite %s.', 'placeholder: Course', 'ebox' ), ebox_get_custom_label_lower( 'course' ) ),
							'show_in_rest'    => ebox_REST_API::enabled(),
						),
						'course_points_enabled'         => array(
							// translators: placeholder: Course.
							'name'         => sprintf( esc_html_x( 'Enable %s Points', 'placeholder: Course', 'ebox' ), ebox_Custom_Label::get_label( 'course' ) ),
							'type'         => 'checkbox',
							'help_text'    => esc_html__( 'Leave this field unchecked if points not used.', 'ebox' ),
							'show_in_rest' => ebox_REST_API::enabled(),
							'rest_args'    => array(
								'schema' => array(
									'type' => 'boolean',
								),
							),
						),
						'course_points'                 => array(
							// translators: placeholder: Course.
							'name'         => sprintf( esc_html_x( '%s Points', 'Course Points', 'ebox' ), ebox_Custom_Label::get_label( 'course' ) ),
							'type'         => 'number',
							'step'         => 'any',
							'min'          => '0',
							// translators: placeholder: Course.
							'help_text'    => sprintf( esc_html_x( 'Enter the number of points a user will receive for this %s.', 'placeholder: Course', 'ebox' ), ebox_Custom_Label::get_label( 'course' ) ),
							'show_in_rest' => ebox_REST_API::enabled(),
						),
						'course_points_access'          => array(
							// translators: placeholder: Course.
							'name'         => sprintf( esc_html_x( '%s Points Access', 'placeholder: Course', 'ebox' ), ebox_Custom_Label::get_label( 'course' ) ),
							'type'         => 'number',
							'step'         => 'any',
							'min'          => '0',
							// translators: placeholder: Course.
							'help_text'    => sprintf( esc_html_x( 'Enter the number of points a user must have to access this %s.', 'placeholder: Course', 'ebox' ), ebox_Custom_Label::get_label( 'course' ) ),
							'show_in_rest' => ebox_REST_API::enabled(),
						),
						'course_disable_lesson_progression' => array(
							// translators: placeholder: Lesson.
							'name'         => sprintf( esc_html_x( 'Disable %s Progression', 'placeholder: Lesson', 'ebox' ), ebox_Custom_Label::get_label( 'lesson' ) ),
							'type'         => 'checkbox',
							'default'      => 0,
							// translators: placeholder: modules.
							'help_text'    => sprintf( esc_html_x( 'Disable the feature that allows attempting %s only in allowed order.', 'placeholder: modules', 'ebox' ), ebox_get_custom_label_lower( 'modules' ) ),
							'show_in_rest' => ebox_REST_API::enabled(),
						),
						'expire_access'                 => array(
							'name'         => esc_html__( 'Expire Access', 'ebox' ),
							'type'         => 'checkbox',
							'help_text'    => esc_html__( 'Leave this field unchecked if access never expires.', 'ebox' ),
							'show_in_rest' => ebox_REST_API::enabled(),
						),
						'expire_access_days'            => array(
							'name'         => esc_html__( 'Expire Access After (days)', 'ebox' ),
							'type'         => 'number',
							'min'          => '0',
							// translators: placeholder: Course.
							'help_text'    => sprintf( esc_html_x( 'Enter the number of days a user has access to this %s.', 'placeholder: Course', 'ebox' ), ebox_Custom_Label::get_label( 'course' ) ),
							'show_in_rest' => ebox_REST_API::enabled(),
						),
						'expire_access_delete_progress' => array(
							// translators: placeholders: Course, Quiz.
							'name'         => sprintf( esc_html_x( 'Delete %1$s and %2$s Data After Expiration', 'placeholders: Course, Quiz', 'ebox' ), ebox_Custom_Label::get_label( 'course' ), ebox_Custom_Label::get_label( 'quiz' ) ),
							'type'         => 'checkbox',
							// translators: placeholder: Course.
							'help_text'    => sprintf( esc_html_x( "Select this option if you want the user's %s progress to be deleted when their access expires.", 'placeholder: Course', 'ebox' ), ebox_Custom_Label::get_label( 'course' ) ),
							'show_in_rest' => ebox_REST_API::enabled(),
						),
						'course_disable_content_table'  => array(
							// translators: placeholder: Course.
							'name'         => sprintf( esc_html_x( 'Hide %s Content Table', 'placeholder: Course', 'ebox' ), ebox_Custom_Label::get_label( 'course' ) ),
							'type'         => 'checkbox',
							'default'      => 0,
							// translators: placeholder: Course.
							'help_text'    => sprintf( esc_html_x( 'Hide %s Content table when user is not enrolled.', 'placeholder: Course', 'ebox' ), ebox_Custom_Label::get_label( 'course' ) ),
							'show_in_rest' => false,
						),

						'certificate'                   => array(
							'name'         => esc_html__( 'Associated Certificate', 'ebox' ),
							'type'         => 'select',
							// translators: placeholder: course.
							'help_text'    => sprintf( esc_html_x( 'Select a certificate to be awarded upon %s completion (optional).', 'placeholder: course', 'ebox' ), ebox_get_custom_label_lower( 'course' ) ),
							'default'      => '',
							'show_in_rest' => false,
						),
					),
				),
				'ebox-modules'       => array(
					'plugin_name'        => ebox_Custom_Label::get_label( 'lesson' ),
					'slug_name'          => ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_Permalinks', 'modules' ),
					'post_type'          => 'ebox-modules',
					'template_redirect'  => true,
					'taxonomies'         => $lesson_taxonomies,
					'cpt_options'        => array(
						'has_archive'         => ebox_post_type_has_archive( 'ebox-modules' ),
						'supports'            => array_merge(
							array( 'title', 'editor', 'author', 'page-attributes' ),
							ebox_Settings_Section::get_section_setting( 'ebox_Settings_modules_CPT', 'supports' )
						),
						'labels'              => $lesson_labels,
						'capability_type'     => 'course',
						'exclude_from_search' => ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_modules_CPT', 'include_in_search' ) !== 'yes' ) ? true : false,
						'capabilities'        => $course_capabilities,
						'map_meta_cap'        => true,
						'show_in_rest'        => ebox_REST_API::enabled( 'ebox-modules' ) || ebox_REST_API::gutenberg_enabled( 'ebox-modules' ),
					),
					'options_page_title' => sprintf(
						// translators: placeholder: Lesson.
						esc_html_x( 'ebox %s Settings', 'placeholder: Lesson', 'ebox' ),
						ebox_Custom_Label::get_label( 'lesson' )
					),
					'fields'             => array(
						'lesson_materials'                 => array(
							// translators: placeholder: Lesson.
							'name'         => sprintf( esc_html_x( '%s Materials', 'placeholder: Lesson', 'ebox' ), ebox_Custom_Label::get_label( 'lesson' ) ),
							'type'         => 'textarea',
							// translators: placeholder: Lesson.
							'help_text'    => sprintf( esc_html_x( 'Options for %s materials', 'placeholder: Lesson', 'ebox' ), ebox_Custom_Label::get_label( 'lesson' ) ),
							'show_in_rest' => ebox_REST_API::enabled(),
							'rest_args'    => array(
								'schema' => array(
									'type' => 'html',
								),
							),
						),
						'course'                           => array(
							// translators: placeholder: Course.
							'name'         => sprintf( esc_html_x( 'Associated %s', 'placeholder: Course', 'ebox' ), ebox_Custom_Label::get_label( 'course' ) ),
							'type'         => 'select',
							'lazy_load'    => true,
							// translators: placeholders: Lesson, Course.
							'help_text'    => sprintf( esc_html_x( 'Associate this %1$s with a %2$s.', 'placeholders: Lesson, Course', 'ebox' ), ebox_Custom_Label::get_label( 'lesson' ), ebox_Custom_Label::get_label( 'course' ) ),
							'default'      => '',
							'required'     => true,
							'show_in_rest' => false,
						),
						'forced_lesson_time'               => array(
							// translators: placeholder: Lesson.
							'name'         => sprintf( esc_html_x( 'Forced %s Timer', 'placeholder: Lesson', 'ebox' ), ebox_Custom_Label::get_label( 'lesson' ) ),
							'type'         => 'text',
							// translators: placeholder: Lesson.
							'help_text'    => sprintf( esc_html_x( 'Minimum time a user has to spend on %s page before it can be marked complete. Examples: 40 (for 40 seconds), 20s, 45sec, 2m 30s, 2min 30sec, 1h 5m 10s, 1hr 5min 10sec', 'placeholder: Lesson', 'ebox' ), ebox_Custom_Label::get_label( 'lesson' ) ),
							'default'      => '',
							'show_in_rest' => ebox_REST_API::enabled(),
						),
						'lesson_assignment_upload'         => array(
							'name'         => esc_html__( 'Upload Assignment', 'ebox' ),
							'type'         => 'checkbox',
							'help_text'    => esc_html__( 'Check this if you want to make it mandatory to upload assignment', 'ebox' ),
							'default'      => 0,
							'show_in_rest' => ebox_REST_API::enabled(),
						),
						'auto_approve_assignment'          => array(
							'name'         => esc_html__( 'Auto Approve Assignment', 'ebox' ),
							'type'         => 'checkbox',
							'help_text'    => esc_html__( 'Check this if you want to auto-approve the uploaded assignment', 'ebox' ),
							'default'      => 'on',
							'show_in_rest' => ebox_REST_API::enabled(),
						),
						'assignment_upload_limit_count'    => array(
							'name'         => esc_html__( 'Limit number of uploaded files', 'ebox' ),
							'type'         => 'number',
							'placeholder'  => esc_html__( 'Default is 1', 'ebox' ),
							'help_text'    => esc_html__( 'Enter the maximum number of assignment uploads allowed. Default is 1. Use 0 to unlimited.', 'ebox' ),
							'default'      => '1',
							'class'        => 'small-text',
							'min'          => '1',
							'show_in_rest' => ebox_REST_API::enabled(),
						),
						'lesson_assignment_deletion_enabled' => array(
							'name'         => esc_html__( 'Allow Student to Delete own Assignment(s)', 'ebox' ),
							'type'         => 'checkbox',
							'help_text'    => esc_html__( 'Allow Student to Delete own Assignment(s)', 'ebox' ),
							'default'      => 0,
							'show_in_rest' => ebox_REST_API::enabled(),
						),

						'lesson_assignment_points_enabled' => array(
							'name'         => esc_html__( 'Award Points for Assignment', 'ebox' ),
							'type'         => 'checkbox',
							'help_text'    => esc_html__( 'Allow this assignment to be assigned points when it is approved.', 'ebox' ),
							'default'      => 0,
							'show_in_rest' => ebox_REST_API::enabled(),
						),
						'lesson_assignment_points_amount'  => array(
							'name'         => esc_html__( 'Set Number of Points for Assignment', 'ebox' ),
							'type'         => 'number',
							'min'          => 0,
							'help_text'    => esc_html__( 'Assign the max amount of points someone can earn for this assignment.', 'ebox' ),
							'default'      => 0,
							'show_in_rest' => ebox_REST_API::enabled(),
						),
						'assignment_upload_limit_extensions' => array(
							'name'         => esc_html__( 'Allowed File Extensions', 'ebox' ),
							'type'         => 'text',
							'placeholder'  => esc_html__( 'Example: pdf, xls, zip', 'ebox' ),
							'help_text'    => esc_html__( 'Enter comma-separated list of allowed file extensions: pdf, xls, zip or leave blank for any.', 'ebox' ),
							'default'      => '',
							'show_in_rest' => ebox_REST_API::enabled(),
						),
						'assignment_upload_limit_size'     => array(
							'name'         => esc_html__( 'Allowed File Size', 'ebox' ),
							'type'         => 'text',
							// translators: placeholder: PHP file upload size.
							'placeholder'  => sprintf( esc_html_x( 'Maximum upload file size: %s', 'placeholder: PHP file upload size', 'ebox' ), ini_get( 'upload_max_filesize' ) ),
							// translators: placeholder: PHP file upload size.
							'help_text'    => sprintf( esc_html_x( 'Enter maximum file upload size. Example: 100KB, 2M, 2MB, 1G. Maximum upload file size: %s', 'placeholder: PHP file upload size', 'ebox' ), ini_get( 'upload_max_filesize' ) ),
							'default'      => '',
							'show_in_rest' => ebox_REST_API::enabled(),
						),

						'sample_lesson'                    => array(
							// translators: placeholder: Lesson.
							'name'      => sprintf( esc_html_x( 'Sample %s', 'placeholder: Lesson', 'ebox' ), ebox_Custom_Label::get_label( 'lesson' ) ),
							'type'      => 'checkbox',
							// translators: placeholders: lesson, topics.
							'help_text' => sprintf( esc_html_x( 'Check this if you want this %1$s and all its %2$s to be available for free.', 'placeholders: lesson, topics', 'ebox' ), ebox_get_custom_label_lower( 'lesson' ), ebox_get_custom_label_lower( 'topics' ) ),
							'default'   => 0,
						),
						'visible_after'                    => array(
							// translators: placeholder: Lesson.
							'name'         => sprintf( esc_html_x( 'Make %s visible X Days After Sign-up', 'Make Lesson Visible X Days After Sign-up', 'ebox' ), ebox_Custom_Label::get_label( 'lesson' ) ),
							'type'         => 'number',
							'class'        => 'small-text',
							'min'          => '0',
							// translators: placeholder: Lesson.
							'help_text'    => sprintf( esc_html_x( 'Make %s visible ____ days after sign-up', 'placeholder: Lesson', 'ebox' ), ebox_Custom_Label::get_label( 'lesson' ) ),
							'default'      => 0,
							'show_in_rest' => ebox_REST_API::enabled(),
						),
						'visible_after_specific_date'      => array(
							// translators: placeholder: Lesson.
							'name'         => sprintf( esc_html_x( 'Make %s Visible on Specific Date', 'placeholder: Lesson', 'ebox' ), ebox_Custom_Label::get_label( 'lesson' ) ),
							'type'         => 'wp_date_selector',
							'class'        => 'ebox-datepicker-field',
							// translators: placeholder: lesson.
							'help_text'    => sprintf( esc_html_x( 'Set the date that you would like this %s to become available.', 'placeholder: lesson', 'ebox' ), ebox_get_custom_label_lower( 'lesson' ) ),
							'show_in_rest' => ebox_REST_API::enabled(),
						),
					),
				),
				'ebox-topic'         => array(
					// translators: placeholders: Lesson, Topic.
					'plugin_name'        => sprintf( esc_html_x( '%1$s %2$s', 'placeholders: Lesson, Topic', 'ebox' ), ebox_Custom_Label::get_label( 'lesson' ), ebox_Custom_Label::get_label( 'topic' ) ),
					'slug_name'          => ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_Permalinks', 'topics' ),
					'post_type'          => 'ebox-topic',
					'template_redirect'  => true,
					'taxonomies'         => $topic_taxonomies,
					'cpt_options'        => array(
						'supports'            => array_merge(
							array( 'title', 'editor', 'author', 'page-attributes' ),
							ebox_Settings_Section::get_section_setting( 'ebox_Settings_Topics_CPT', 'supports' )
						),
						'has_archive'         => ebox_post_type_has_archive( 'ebox-topic' ),
						'labels'              => $lesson_topic_labels,
						'capability_type'     => 'course',
						'exclude_from_search' => ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Topics_CPT', 'include_in_search' ) !== 'yes' ) ? true : false,
						'capabilities'        => $course_capabilities,
						'map_meta_cap'        => true,
						'show_in_rest'        => ebox_REST_API::enabled( 'ebox-topic' ) || ebox_REST_API::gutenberg_enabled( 'ebox-topic' ),
					),
					'options_page_title' => sprintf(
						// translators: placeholder: Topic.
						esc_html_x( 'ebox %s Settings', 'placeholder: Topic', 'ebox' ),
						ebox_Custom_Label::get_label( 'topic' )
					),
					'fields'             => array(
						'topic_materials'                  => array(
							// translators: placeholder: Topic.
							'name'         => sprintf( esc_html_x( '%s Materials', 'placeholder: Topic', 'ebox' ), ebox_Custom_Label::get_label( 'topic' ) ),
							'type'         => 'textarea',
							// translators: placeholder: Topic.
							'help_text'    => sprintf( esc_html_x( 'Options for %s materials', 'placeholder: Topic', 'ebox' ), ebox_Custom_Label::get_label( 'topic' ) ),
							'show_in_rest' => ebox_REST_API::enabled(),
							'rest_args'    => array(
								'schema' => array(
									'type' => 'html',
								),
							),
						),

						'course'                           => array(
							// translators: placeholder: Course.
							'name'         => sprintf( esc_html_x( 'Associated %s', 'placeholder: Course', 'ebox' ), ebox_Custom_Label::get_label( 'course' ) ),
							'type'         => 'select',
							'lazy_load'    => true,
							// translators: placeholders: Topic, Course.
							'help_text'    => sprintf( esc_html_x( 'Associate this %1$s with a %2$s.', 'placeholders: topic, course', 'ebox' ), ebox_Custom_Label::get_label( 'topic' ), ebox_Custom_Label::get_label( 'course' ) ),
							'default'      => '',
							'show_in_rest' => false,
						),
						'lesson'                           => array(
							// translators: placeholder: Lesson.
							'name'         => sprintf( esc_html_x( 'Associated %s', 'placeholder: Lesson', 'ebox' ), ebox_Custom_Label::get_label( 'lesson' ) ),
							'type'         => 'select',
							'lazy_load'    => true,
							// translators: placeholders: Topic, Lesson.
							'help_text'    => sprintf( esc_html_x( 'Associate this %1$s with a %2$s.', 'placeholders: Topic, Lesson', 'ebox' ), ebox_Custom_Label::get_label( 'topic' ), ebox_Custom_Label::get_label( 'lesson' ) ),
							'default'      => '',
							'show_in_rest' => false,
						),
						'forced_lesson_time'               => array(
							// translators: placeholder: Topic.
							'name'         => sprintf( esc_html_x( 'Forced %s Timer', 'placeholder: Topic', 'ebox' ), ebox_Custom_Label::get_label( 'topic' ) ),
							'type'         => 'text',
							// translators: placeholder: Topic.
							'help_text'    => sprintf( esc_html_x( 'Minimum time a user has to spend on %s page before it can be marked complete. Examples: 40 (for 40 seconds), 20s, 45sec, 2m 30s, 2min 30sec, 1h 5m 10s, 1hr 5min 10sec', 'placeholder: Topic', 'ebox' ), ebox_Custom_Label::get_label( 'topic' ) ),
							'default'      => '',
							'show_in_rest' => ebox_REST_API::enabled(),
						),
						'lesson_assignment_upload'         => array(
							'name'         => esc_html__( 'Upload Assignment', 'ebox' ),
							'type'         => 'checkbox',
							'help_text'    => esc_html__( 'Check this if you want to make it mandatory to upload assignment', 'ebox' ),
							'default'      => 0,
							'show_in_rest' => ebox_REST_API::enabled(),
						),
						'auto_approve_assignment'          => array(
							'name'         => esc_html__( 'Auto Approve Assignment', 'ebox' ),
							'type'         => 'checkbox',
							'help_text'    => esc_html__( 'Check this if you want to auto-approve the uploaded assignment', 'ebox' ),
							'default'      => 'on',
							'show_in_rest' => ebox_REST_API::enabled(),
						),
						'assignment_upload_limit_count'    => array(
							'name'         => esc_html__( 'Limit number of uploaded files', 'ebox' ),
							'type'         => 'number',
							'placeholder'  => esc_html__( 'Default is 1', 'ebox' ),
							'help_text'    => esc_html__( 'Enter the maximum number of assignment uploads allowed. Default is 1. Use 0 to unlimited.', 'ebox' ),
							'default'      => '1',
							'show_in_rest' => ebox_REST_API::enabled(),
							'class'        => 'small-text',
							'min'          => '1',
						),
						'lesson_assignment_deletion_enabled' => array(
							'name'      => esc_html__( 'Allow Student to Delete own Assignment(s)', 'ebox' ),
							'type'      => 'checkbox',
							'help_text' => esc_html__( 'Allow Student to Delete own Assignment(s)', 'ebox' ),
							'default'   => 0,
						),

						'lesson_assignment_points_enabled' => array(
							'name'         => esc_html__( 'Award Points for Assignment', 'ebox' ),
							'type'         => 'checkbox',
							'help_text'    => esc_html__( 'Allow this assignment to be assigned points when it is approved.', 'ebox' ),
							'default'      => 0,
							'show_in_rest' => ebox_REST_API::enabled(),
						),
						'lesson_assignment_points_amount'  => array(
							'name'         => esc_html__( 'Set Number of Points for Assignment', 'ebox' ),
							'type'         => 'number',
							'min'          => 0,
							'help_text'    => esc_html__( 'Assign the max amount of points someone can earn for this assignment.', 'ebox' ),
							'default'      => 0,
							'show_in_rest' => ebox_REST_API::enabled(),
						),

						'assignment_upload_limit_extensions' => array(
							'name'         => esc_html__( 'Allowed File Extensions', 'ebox' ),
							'type'         => 'text',
							'placeholder'  => esc_html__( 'Example: pdf,xls,zip', 'ebox' ),
							'help_text'    => esc_html__( 'Enter comma-separated list of allowed file extensions: pdf,xls,zip or leave blank for any.', 'ebox' ),
							'default'      => '',
							'show_in_rest' => ebox_REST_API::enabled(),
						),
						'assignment_upload_limit_size'     => array(
							'name'         => esc_html__( 'Allowed File Size', 'ebox' ),
							'type'         => 'text',
							// translators: placeholder: PHP file upload size.
							'placeholder'  => sprintf( esc_html_x( 'Maximum upload file size: %s', 'placeholder: PHP file upload size', 'ebox' ), ini_get( 'upload_max_filesize' ) ),
							// translators: placeholder: PHP file upload size.
							'help_text'    => sprintf( esc_html_x( 'Enter maximum file upload size. Example: 100KB, 2M, 2MB, 1G. Maximum upload file size: %s', 'placeholder: PHP file upload size', 'ebox' ), ini_get( 'upload_max_filesize' ) ),
							'default'      => '',
							'show_in_rest' => ebox_REST_API::enabled(),
						),
					),
					'default_options'    => array(
						'orderby' => array(
							'name'            => esc_html__( 'Sort By', 'ebox' ),
							'type'            => 'select',
							'initial_options' => array(
								''           => esc_html__( 'Select a choice...', 'ebox' ),
								'title'      => esc_html__( 'Title', 'ebox' ),
								'date'       => esc_html__( 'Date', 'ebox' ),
								'menu_order' => esc_html__( 'Menu Order', 'ebox' ),
							),
							'default'         => 'date',
							'help_text'       => esc_html__( 'Choose the sort order.', 'ebox' ),
						),
						'order'   => array(
							'name'            => esc_html__( 'Sort Direction YYY', 'ebox' ),
							'type'            => 'select',
							'initial_options' => array(
								''     => esc_html__( 'Select a choice...', 'ebox' ),
								'ASC'  => esc_html__( 'Ascending', 'ebox' ),
								'DESC' => esc_html__( 'Descending', 'ebox' ),
							),
							'default'         => 'DESC',
							'help_text'       => esc_html__( 'Choose the sort order.', 'ebox' ),
						),
					),
				),
				'ebox-quiz'          => array(
					'plugin_name'        => ebox_Custom_Label::get_label( 'quiz' ),
					'slug_name'          => ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_Permalinks', 'quizzes' ),
					'post_type'          => 'ebox-quiz',
					'template_redirect'  => true,
					'taxonomies'         => $quiz_taxonomies,
					'cpt_options'        => array(
						'has_archive'         => ebox_post_type_has_archive( 'ebox-quiz' ),
						'hierarchical'        => false,
						'supports'            => array_merge(
							array( 'title', 'editor', 'author', 'page-attributes' ),
							ebox_Settings_Section::get_section_setting( 'ebox_Settings_Quizzes_CPT', 'supports' )
						),
						'labels'              => $quiz_labels,
						'capability_type'     => 'course',
						'exclude_from_search' => ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Quizzes_CPT', 'include_in_search' ) !== 'yes' ) ? true : false,
						'capabilities'        => $course_capabilities,
						'map_meta_cap'        => true,
						'show_in_rest'        => ebox_REST_API::enabled( 'ebox-quiz' ) || ebox_REST_API::gutenberg_enabled( 'ebox-quiz' ),
					),
					'options_page_title' => sprintf(
						// translators: placeholder: Quiz.
						esc_html_x( 'ebox %s Settings', 'placeholder: Quiz', 'ebox' ),
						ebox_Custom_Label::get_label( 'quiz' )
					),
					'fields'             => array(
						'quiz_materials'    => array(
							// translators: placeholder: Quiz.
							'name'         => sprintf( esc_html_x( '%s Materials', 'placeholder: Quiz', 'ebox' ), ebox_Custom_Label::get_label( 'quiz' ) ),
							'type'         => 'textarea',
							// translators: placeholder: Quiz.
							'help_text'    => sprintf( esc_html_x( 'Options for %s materials', 'placeholder: Quiz', 'ebox' ), ebox_Custom_Label::get_label( 'quiz' ) ),
							'show_in_rest' => ebox_REST_API::enabled(),
							'rest_args'    => array(
								'schema' => array(
									'type' => 'html',
								),
							),
						),

						'repeats'           => array(
							'name'      => esc_html__( 'Repeats', 'ebox' ),
							'type'      => 'text',
							// translators: placeholder: quiz.
							'help_text' => sprintf( esc_html_x( 'Number of repeats allowed for %s. Blank = unlimited attempts. 0 = 1 attempt, 1 = 2 attempts, etc.', 'placeholder: quiz', 'ebox' ), ebox_get_custom_label_lower( 'quiz' ) ),
							'default'   => '',
						),
						'threshold'         => array(
							'name'         => esc_html__( 'Certificate Threshold', 'ebox' ),
							'type'         => 'text',
							'help_text'    => esc_html__( 'Minimum score required to award a certificate, between 0 and 1 where 1 = 100%.', 'ebox' ),
							'default'      => '0.8',
							'show_in_rest' => ebox_REST_API::enabled(),
						),
						'passingpercentage' => array(
							'name'         => esc_html__( 'Passing Percentage', 'ebox' ),
							'type'         => 'text',
							// translators: placeholder: quiz.
							'help_text'    => sprintf( esc_html_x( 'Passing percentage required to pass the %s (number only). e.g. 80 for 80%%.', 'placeholder: quiz', 'ebox' ), ebox_get_custom_label_lower( 'quiz' ) ),
							'default'      => '80',
							'show_in_rest' => ebox_REST_API::enabled(),
						),
						'course'            => array(
							// translators: placeholder: Course.
							'name'      => sprintf( esc_html_x( 'Associated %s', 'placeholder: Course', 'ebox' ), ebox_Custom_Label::get_label( 'course' ) ),
							'type'      => 'select',
							'lazy_load' => true,
							// translators: placeholders: Quiz, Course.
							'help_text' => sprintf( esc_html_x( 'Associate this %1$s with a %2$s.', 'placeholders: Quiz, Course', 'ebox' ), ebox_Custom_Label::get_label( 'quiz' ), ebox_Custom_Label::get_label( 'course' ) ),
							'default'   => '',
						),
						'lesson'            => array(
							// translators: placeholder: Lesson.
							'name'      => sprintf( esc_html_x( 'Associated %s', 'placeholder: Lesson', 'ebox' ), ebox_Custom_Label::get_label( 'lesson' ) ),
							'type'      => 'select',
							// translators: placeholders: Quiz, Lesson.
							'help_text' => sprintf( esc_html_x( 'Associate this %1$s with a %2$s.', 'placeholders: Quiz, Lesson', 'ebox' ), ebox_Custom_Label::get_label( 'quiz' ), ebox_Custom_Label::get_label( 'lesson' ) ),
							'default'   => '',
						),
						'certificate'       => array(
							'name'         => esc_html__( 'Associated Certificate', 'ebox' ),
							'type'         => 'select',
							// translators: placeholder: quiz.
							'help_text'    => sprintf( esc_html_x( 'Optionally associate a %s with a certificate.', 'placeholder: quiz', 'ebox' ), ebox_get_custom_label_lower( 'quiz' ) ),
							'default'      => '',
							'show_in_rest' => ebox_REST_API::enabled(),
						),
						'quiz_pro'          => array(
							'name'      => esc_html__( 'Associated Settings', 'ebox' ),
							'type'      => 'select',
							// translators: placeholder: quiz.
							'help_text' => sprintf( esc_html_x( 'If you imported a %s, use this field to select it. Otherwise, create new settings below. After saving or publishing, you will be able to add questions.', 'placeholder: quiz.', 'ebox' ), ebox_get_custom_label_lower( 'quiz' ) ) . '<a style="display:none" id="advanced_quiz_preview" class="wpProQuiz_prview" href="#">' . esc_html__( 'Preview', 'ebox' ) . '</a>', // cspell:disable-line.
							'default'   => '',
						),
					),
					'default_options'    => array(),
				),
				'ebox-question'      => array(
					'plugin_name'        => ebox_Custom_Label::get_label( 'question' ),
					'slug_name'          => 'ebox-question',
					'post_type'          => 'ebox-question',
					'template_redirect'  => false,
					'taxonomies'         => $question_taxonomies,
					'cpt_options'        => array(
						'public'              => false,
						'hierarchical'        => false,
						'supports'            => array( 'title', 'thumbnail', 'editor', 'author', 'revisions', 'page-attributes' ),
						'labels'              => $question_labels,
						'capability_type'     => 'course',
						'exclude_from_search' => true,
						'show_in_nav_menus'   => false,
						'capabilities'        => $course_capabilities,
						'map_meta_cap'        => true,
						'show_in_rest'        => true,
					),
					'options_page_title' => sprintf(
						// translators: placeholder: Question.
						esc_html_x( 'ebox %s Settings', 'placeholder: Question', 'ebox' ),
						ebox_Custom_Label::get_label( 'Question' )
					),
					'fields'             => array(
						'quiz' => array(
							// translators: placeholder: Quiz.
							'name'         => sprintf( esc_html_x( 'Associated %s', 'placeholder: Quiz', 'ebox' ), ebox_Custom_Label::get_label( 'quiz' ) ),
							'type'         => 'select',
							'lazy_load'    => true,
							// translators: placeholders: Question, Quiz.
							'help_text'    => sprintf( esc_html_x( 'Associate this %1$s with a %2$s.', 'placeholder: Question, Quiz', 'ebox' ), ebox_Custom_Label::get_label( 'question' ), ebox_Custom_Label::get_label( 'quiz' ) ),
							'default'      => '',
							'required'     => true,
							'show_in_rest' => false,
						),

					),
					'default_options'    => array(),
				),
				$exam_post_type_slug => array(
					'plugin_name'        => ebox_Custom_Label::get_label( 'exam' ),
					'slug_name'          => $exam_post_type_slug,
					'post_type'          => $exam_post_type_slug,
					'template_redirect'  => true,
					'taxonomies'         => array(),
					'cpt_options'        => array(
						'public'              => true,
						'hierarchical'        => false,
						'has_archive'         => false,
						'supports'            => array( 'title', 'editor', 'custom-fields', 'thumbnail', 'revisions' ),
						'labels'              => $exam_labels,
						'capability_type'     => 'course',
						'exclude_from_search' => true,
						'show_in_nav_menus'   => false,
						'capabilities'        => $course_capabilities,
						'map_meta_cap'        => true,
						'show_in_rest'        => ebox_REST_API::enabled( $exam_post_type_slug ) || ebox_REST_API::gutenberg_enabled( $exam_post_type_slug ),
						'template'            => array(
							array( 'ebox/ld-exam' ),
						),
					),
					'options_page_title' => sprintf(
						// translators: placeholder: Exam.
						esc_html_x( 'ebox %s Settings', 'placeholder: Exam', 'ebox' ),
						ebox_Custom_Label::get_label( 'exam' )
					),
					'fields'             => array(),
				),
			);

			$registration_page = ebox_Settings_Section::get_section_setting(
				'ebox_Settings_Section_Registration_Pages',
				'registration'
			);

			if ( ! empty( $registration_page ) ) {
				$this->post_args[ $coupon_post_type_slug ] = array(
					'plugin_name'        => ebox_Custom_Label::get_label( LDLMS_Post_Types::COUPON ),
					'slug_name'          => $coupon_post_type_slug,
					'post_type'          => $coupon_post_type_slug,
					'template_redirect'  => false,
					'cpt_options'        => array(
						'public'              => false,
						'hierarchical'        => false,
						'has_archive'         => false,
						'supports'            => array( 'title' ),
						'labels'              => $coupon_labels,
						'exclude_from_search' => true,
						'show_in_nav_menus'   => false,
						'capabilities'        => ebox_get_admin_coupons_capabilities(),
						'show_in_rest'        => false,
					),
					'options_page_title' => sprintf(
					// translators: placeholder: Coupon.
						esc_html_x( 'ebox %s Settings', 'placeholder: Coupon', 'ebox' ),
						ebox_Custom_Label::get_label( LDLMS_Post_Types::COUPON )
					),
					'fields'             => array(),
				);
			}

			$cert_defaults = array(
				'shortcode_options' => array(
					'name'    => 'Shortcode Options',
					'type'    => 'html',
					'default' => '',
					'save'    => false,
					'label'   => 'none',
				),
			);

			$certificates_labels = array(
				'name'                     => esc_html_x( 'Certificates', 'Certificates Post Type Label', 'ebox' ),
				'singular_name'            => esc_html_x( 'Certificate', 'Certificates Post Type Singular Name', 'ebox' ),
				'add_new'                  => esc_html_x( 'Add New', 'Add New Certificate Label', 'ebox' ),
				'add_new_item'             => esc_html_x( 'Add New Certificate', 'Add New Item Certificate Label', 'ebox' ),
				'edit_item'                => esc_html_x( 'Edit Certificate', 'Edit Certificate Label', 'ebox' ),
				'new_item'                 => esc_html_x( 'New Certificate', 'Edit Certificate Label', 'ebox' ),
				'all_items'                => esc_html_x( 'Certificates', 'All Certificates Label', 'ebox' ),
				'view_item'                => esc_html_x( 'View Certificate', 'View Certificate Label', 'ebox' ),
				'view_items'               => esc_html_x( 'View Certificates', 'View Certificates Label', 'ebox' ),
				'search_items'             => esc_html_x( 'Search Certificates', 'View Certificates Label', 'ebox' ),
				'not_found'                => esc_html_x( 'No Certificates found', 'No Certificates found Label', 'ebox' ),
				'not_found_in_trash'       => esc_html_x( 'No Certificates found in Trash', 'No Certificates found in Trash Label', 'ebox' ),
				'parent_item_colon'        => '',
				'menu_name'                => esc_html_x( 'Certificates', 'Certificates Menu Label', 'ebox' ),
				'item_published'           => esc_html_x( 'Certificate Published', 'Certificate Published Label', 'ebox' ),
				'item_published_privately' => esc_html_x( 'Certificate Published Privately', 'Certificate Published Privately Label', 'ebox' ),
				'item_reverted_to_draft'   => esc_html_x( 'Certificate Reverted to Draft', 'Certificate Reverted to Draft Label', 'ebox' ),
				'item_scheduled'           => esc_html_x( 'Certificate Scheduled', 'Certificate Scheduled Label', 'ebox' ),
				'item_updated'             => esc_html_x( 'Certificate Updated', 'Certificate Updated Label', 'ebox' ),
			);

			$this->post_args['ebox-certificates'] = array(
				'plugin_name'        => esc_html__( 'Certificates', 'ebox' ),
				'slug_name'          => 'certificates',
				'post_type'          => 'ebox-certificates',
				'template_redirect'  => false,
				'fields'             => array(),
				'options_page_title' => esc_html__( 'ebox Certificates Options', 'ebox' ),
				'default_options'    => $cert_defaults,
				'cpt_options'        => array(
					'labels'              => $certificates_labels,
					'exclude_from_search' => true,
					'has_archive'         => false,
					'hierarchical'        => false,
					'supports'            => array( 'title', 'editor', 'thumbnail', 'author', 'revisions' ),
					'show_in_nav_menus'   => false,
					'capability_type'     => 'course',
					'capabilities'        => $course_capabilities,
					'map_meta_cap'        => true,
					'show_in_rest'        => false,
				),
			);

			$lcl_team  = ebox_Custom_Label::get_label( 'team' );
			$lcl_teams = ebox_Custom_Label::get_label( 'teams' );

			$team_labels = array(
				'name'                     => $lcl_teams,
				'singular_name'            => $lcl_team,
				'add_new'                  => esc_html_x( 'Add New', 'Add New Team Label', 'ebox' ),
				// translators: placeholder: Team.
				'add_new_item'             => sprintf( esc_html_x( 'Add New %s', 'placeholder: Team', 'ebox' ), $lcl_team ),
				// translators: placeholder: Team.
				'edit_item'                => sprintf( esc_html_x( 'Edit %s', 'placeholder: Team', 'ebox' ), $lcl_team ),
				// translators: placeholder: Team.
				'new_item'                 => sprintf( esc_html_x( 'New %s', 'placeholder: Team', 'ebox' ), $lcl_team ),
				'all_items'                => $lcl_teams,
				// translators: placeholder: Team.
				'view_item'                => sprintf( esc_html_x( 'View %s', 'placeholder: Team', 'ebox' ), $lcl_team ),
				// translators: placeholder: Teams.
				'view_items'               => sprintf( esc_html_x( 'View %s', 'placeholder: Teams', 'ebox' ), $lcl_teams ),
				// translators: placeholder: Teams.
				'search_items'             => sprintf( esc_html_x( 'Search %s', 'placeholder: Teams', 'ebox' ), $lcl_teams ),
				// translators: placeholder: Teams.
				'not_found'                => sprintf( esc_html_x( 'No %s found', 'placeholder: Teams', 'ebox' ), $lcl_teams ),
				// translators: placeholder: Teams.
				'not_found_in_trash'       => sprintf( esc_html_x( 'No %s found in Trash', 'placeholder: Teams', 'ebox' ), $lcl_teams ),
				'parent_item_colon'        => '',
				'menu_name'                => $lcl_teams,
				// translators: placeholder: Team.
				'item_published'           => sprintf( esc_html_x( '%s Published', 'placeholder: Team', 'ebox' ), $lcl_team ),
				// translators: placeholder: Team.
				'item_published_privately' => sprintf( esc_html_x( '%s Published Privately', 'placeholder: Team', 'ebox' ), $lcl_team ),
				// translators: placeholder: Team.
				'item_reverted_to_draft'   => sprintf( esc_html_x( '%s Reverted to Draft', 'placeholder: Team', 'ebox' ), $lcl_team ),
				// translators: placeholder: Team.
				'item_scheduled'           => sprintf( esc_html_x( '%s Scheduled', 'placeholder: Team', 'ebox' ), $lcl_team ),
				// translators: placeholder: Team.
				'item_updated'             => sprintf( esc_html_x( '%s Updated', 'placeholder: Team', 'ebox' ), $lcl_team ),
			);

			$team_taxonomies = array();
			if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Teams_Taxonomies', 'wp_post_category' ) == 'yes' ) {
				$team_taxonomies['category'] = 'category';
			}

			if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Teams_Taxonomies', 'wp_post_tag' ) == 'yes' ) {
				$team_taxonomies['post_tag'] = 'post_tag';
			}

			/**
			 * Filter Taxonomy Capability.
			 *
			 * @since 3.2.0
			 *
			 * @param array  $taxonomy_capability Array of taxonomy capabilities.
			 * @param string $post_type           Post Type slug.
			 */
			$team_taxonomy_capability = apply_filters(
				'ebox_taxonomy_capabilities',
				array(
					'manage_terms' => 'manage_terms_team_categories',
					'edit_terms'   => 'edit_terms_team_categories',
					'delete_terms' => 'delete_terms_team_categories',
					'assign_terms' => 'assign_terms_team_categories',
				),
				ebox_get_post_type_slug( 'team' )
			);

			$team_taxonomies_public = ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Teams_CPT', 'public' ) === 'yes' ) ? true : false;

			if ( 'yes' === ebox_Settings_Section::get_section_setting( 'ebox_Settings_Teams_Taxonomies', 'ld_team_category' ) ) {
				$team_taxonomies['ld_team_category'] = array(
					'public'            => $team_taxonomies_public,
					'hierarchical'      => true,
					'show_ui'           => true,
					'show_in_menu'      => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'rewrite'           => array( 'slug' => $ebox_settings_permalinks_taxonomies['ld_team_category'] ),
					'capabilities'      => $team_taxonomy_capability,
					'show_in_rest'      => ebox_REST_API::enabled( 'teams' ) || ebox_REST_API::gutenberg_enabled( 'teams' ),
					'labels'            => array(
						// translators: placeholder: Team.
						'name'              => sprintf( esc_html_x( '%s Categories', 'placeholder: Team', 'ebox' ), $lcl_team ),
						// translators: placeholder: Team.
						'singular_name'     => sprintf( esc_html_x( '%s Category', 'placeholder: Team', 'ebox' ), $lcl_team ),
						// translators: placeholder: Team.
						'search_items'      => sprintf( esc_html_x( 'Search %s Categories', 'placeholder: Team', 'ebox' ), $lcl_team ),
						// translators: placeholder: Team.
						'all_items'         => sprintf( esc_html_x( 'All %s Categories', 'placeholder: Team', 'ebox' ), $lcl_team ),
						// translators: placeholder: Team.
						'parent_item'       => sprintf( esc_html_x( 'Parent %s Category', 'placeholder: Team', 'ebox' ), $lcl_team ),
						// translators: placeholder: Team.
						'parent_item_colon' => sprintf( esc_html_x( 'Parent %s Category:', 'placeholder: Team', 'ebox' ), $lcl_team ),
						// translators: placeholder: Team.
						'edit_item'         => sprintf( esc_html_x( 'Edit %s Category', 'placeholder: Team', 'ebox' ), $lcl_team ),
						// translators: placeholder: Team.
						'update_item'       => sprintf( esc_html_x( 'Update %s Category', 'placeholder: Team', 'ebox' ), $lcl_team ),
						// translators: placeholder: Team.
						'add_new_item'      => sprintf( esc_html_x( 'Add New %s Category', 'placeholder: Team', 'ebox' ), $lcl_team ),
						// translators: placeholder: Team.
						'new_item_name'     => sprintf( esc_html_x( 'New %s Category Name', 'placeholder: Team', 'ebox' ), $lcl_team ),
						// translators: placeholder: Team.
						'menu_name'         => sprintf( esc_html_x( '%s Categories', 'placeholder: Team', 'ebox' ), $lcl_team ),
					),
				);
			}

			if ( 'yes' === ebox_Settings_Section::get_section_setting( 'ebox_Settings_Teams_Taxonomies', 'ld_team_tag' ) ) {
				$team_taxonomies['ld_team_tag'] = array(
					'public'            => $team_taxonomies_public,
					'hierarchical'      => false,
					'show_ui'           => true,
					'show_in_menu'      => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'rewrite'           => array( 'slug' => $ebox_settings_permalinks_taxonomies['ld_team_tag'] ),
					'capabilities'      => $team_taxonomy_capability,
					'show_in_rest'      => ebox_REST_API::enabled( 'teams' ) || ebox_REST_API::gutenberg_enabled( 'teams' ),
					'labels'            => array(
						// translators: placeholder: Team.
						'name'              => sprintf( esc_html_x( '%s Tags', 'placeholder: Team', 'ebox' ), $lcl_team ),
						// translators: placeholder: Team.
						'singular_name'     => sprintf( esc_html_x( '%s Tag', 'placeholder: Team', 'ebox' ), $lcl_team ),
						// translators: placeholder: Team.
						'search_items'      => sprintf( esc_html_x( 'Search %s Tag', 'placeholder: Team', 'ebox' ), $lcl_team ),
						// translators: placeholder: Team.
						'all_items'         => sprintf( esc_html_x( 'All %s Tags', 'placeholder: Team', 'ebox' ), $lcl_team ),
						// translators: placeholder: Team.
						'parent_item'       => sprintf( esc_html_x( 'Parent %s Tag', 'placeholder: Team', 'ebox' ), $lcl_team ),
						// translators: placeholder: Team.
						'parent_item_colon' => sprintf( esc_html_x( 'Parent %s Tag:', 'placeholder: Team', 'ebox' ), $lcl_team ),
						// translators: placeholder: Team.
						'edit_item'         => sprintf( esc_html_x( 'Edit %s Tag', 'placeholder: Team', 'ebox' ), $lcl_team ),
						// translators: placeholder: Team.
						'update_item'       => sprintf( esc_html_x( 'Update %s Tag', 'placeholder: Team', 'ebox' ), $lcl_team ),
						// translators: placeholder: Team.
						'add_new_item'      => sprintf( esc_html_x( 'Add New %s Tag', 'placeholder: Team', 'ebox' ), $lcl_team ),
						// translators: placeholder: Team.
						'new_item_name'     => sprintf( esc_html_x( 'New %s Tag Name', 'placeholder: Team', 'ebox' ), $lcl_team ),
						// translators: placeholder: Team.
						'menu_name'         => sprintf( esc_html_x( '%s Tags', 'placeholder: Team', 'ebox' ), $lcl_team ),
					),
				);
			}

			$team_capabilities = ebox_get_admin_teams_capabilities();

			if ( is_admin() ) {
				$admin_role = get_role( 'administrator' );
				if ( ( $admin_role ) && ( is_a( $admin_role, 'WP_Role' ) ) ) {
					foreach ( $team_capabilities as $key => $cap ) {
						$admin_role->add_cap( $cap, true );
					}

					foreach ( $team_taxonomies as $tax_key => $tax_set ) {
						if ( in_array( $tax_key, array( 'category', 'post_tag' ), true ) ) {
							continue;
						}
						if ( ( is_array( $tax_set ) ) && ( ! empty( $tax_set['capabilities'] ) ) ) {
							foreach ( $tax_set['capabilities'] as $key => $cap ) {
								$admin_role->add_cap( $cap, true );
							}
						}
					}
				}
			}

			$this->post_args['teams'] = array(
				'plugin_name'       => ebox_Custom_Label::get_label( 'team' ),
				'slug_name'         => ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_Permalinks', 'teams' ),
				'post_type'         => 'teams',
				'template_redirect' => true,
				'taxonomies'        => $team_taxonomies,
				'cpt_options'       => array(
					'supports'            => array_merge(
						array( 'title', 'editor', 'author' ),
						ebox_Settings_Section::get_section_setting( 'ebox_Settings_Teams_CPT', 'supports' )
					),
					'has_archive'         => ebox_post_type_has_archive( 'teams' ),
					'labels'              => $team_labels,
					'capability_type'     => 'teams',
					'hierarchical'        => ebox_is_teams_hierarchical_enabled(),
					'public'              => ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Teams_CPT', 'public' ) === 'yes' ) ? true : false,
					'exclude_from_search' => ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Teams_CPT', 'include_in_search' ) !== 'yes' ) ? true : false,
					'capabilities'        => $team_capabilities,
					'map_meta_cap'        => true,
					'show_in_rest'        => ebox_REST_API::enabled( 'teams' ) || ebox_REST_API::gutenberg_enabled( 'teams' ),
				),
				'default_options'   => array(),
				'fields'            => array(),
			);

			if ( ( has_filter( 'ebox_post_args_teams' ) ) || ( has_filter( 'ebox-cpt-options' ) ) ) {
				$team_args                = $this->post_args['teams']['cpt_options'];
				$team_args['description'] = $this->post_args['teams']['plugin_name'];

				/**
				 * Filters the post type registration arguments.
				 *
				 * @param array $team_args Post type arguments.
				 */
				if ( has_filter( 'ebox_post_args_teams' ) ) {
					$team_args = apply_filters_deprecated( 'ebox_post_args_teams', array( $team_args, 'teams' ), '3.1.7', 'ebox_post_args' );
				}

				/** This filter is documented in includes/ld-assignment-uploads.php */
				$team_args = apply_filters( 'ebox-cpt-options', $team_args, 'teams' ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores -- Better to keep it this way for now.

				if ( isset( $team_args['description'] ) ) {
					if ( $team_args['description'] !== $this->post_args['teams']['plugin_name'] ) {
						$this->post_args['teams']['plugin_name'] = $team_args['description'];
					}
					unset( $team_args['description'] );
				}
				$this->post_args['teams']['cpt_options'] = $team_args;
			}

			if ( ebox_is_admin_user() ) {
				$this->post_args['ebox-transactions'] = array(
					'plugin_name'        => esc_html__( 'Transactions', 'ebox' ),
					'slug_name'          => 'transactions',
					'post_type'          => 'ebox-transactions',
					'template_redirect'  => false,
					'options_page_title' => esc_html__( 'ebox Transactions Options', 'ebox' ),
					'cpt_options'        => array(
						'supports'            => array( 'title', 'custom-fields', 'page-attributes' ),
						'exclude_from_search' => true,
						'publicly_queryable'  => false,
						'show_in_nav_menus'   => false,
						'show_in_admin_bar'   => false,
						'hierarchical'        => true,
					),
					'fields'             => array(),
					'default_options'    => array(
						null => array(
							'type'    => 'html',
							'save'    => false,
							'default' => esc_html__( 'Click the Export button below to export the transaction list.', 'ebox' ),
						),
					),
				);

				add_action( 'admin_init', array( $this, 'trans_export_init' ) );
			}

			// Added in v2.5.4 to hide the lesson, topic and quiz post type from nav menu when shared steps enabled.
			if ( ebox_is_course_shared_steps_enabled() ) {
				$this->post_args['ebox-modules']['cpt_options']['show_in_nav_menus'] = false;
				$this->post_args['ebox-topic']['cpt_options']['show_in_nav_menus']   = false;
				$this->post_args['ebox-quiz']['cpt_options']['show_in_nav_menus']    = false;
			}

			if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Courses_Builder', 'shared_steps' ) == 'yes' ) {
				if ( isset( $this->post_args['ebox-courses']['fields']['course_lesson_orderby'] ) ) {
					unset( $this->post_args['ebox-courses']['fields']['course_lesson_orderby'] );
				}
				if ( isset( $this->post_args['ebox-courses']['fields']['course_lesson_order'] ) ) {
					unset( $this->post_args['ebox-courses']['fields']['course_lesson_order'] );
				}
			}

			if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Quizzes_Builder', 'shared_questions' ) === 'yes' ) {
				if ( isset( $this->post_args['ebox-question']['fields']['quiz'] ) ) {
					unset( $this->post_args['ebox-question']['fields']['quiz'] );
				}
			}

			// Remove the filter to prevent Course Grid from adding a 'Short Description' field to the legacy metabox.
			// See CG-118.
			remove_filter( 'ebox_post_args', 'ebox_course_grid_post_args' );

			/** This filter is documented in includes/class-ld-lms.php */
			$this->post_args = apply_filters( 'ebox_post_args', $this->post_args );

			add_action( 'admin_init', array( $this, 'quiz_export_init' ) );
			add_action( 'admin_init', array( $this, 'course_export_init' ) );

			foreach ( $this->post_args as $p ) {
				$this->post_types[ $p['post_type'] ] = new ebox_CPT_Instance( $p );
			}

			add_action( 'init', array( $this, 'tax_registration' ), 11 );

			$ebox_question   = $this->post_types['ebox-question'];
			$question_prefix = $ebox_question->get_prefix();
			add_filter( "{$question_prefix}display_settings", array( $this, 'question_display_settings' ), 10, 3 );
		}

		/**
		 * Returns output of users course information for bottom of profile
		 *
		 * @since 2.1.0
		 *
		 * @param  int   $user_id  user id.
		 * @param  array $atts     Attributes.
		 * @return string|array  Output of course information
		 */
		public static function get_course_info( $user_id, $atts = array() ) {

			/**
			 * Filters course list shortcode attribute defaults.
			 *
			 * @param array $shortcode_default An array of default shortcode attributes.
			 */
			$atts_defaults = apply_filters(
				'ebox_ld_course_list_shortcode_defaults',
				array(
					'return'                    => false, // Set to true to return the array data instead of calling the template for output.
					// This function essentially produces the output of three sections. Registered Courses,
					// Course Progress and Quiz Attempts. This parameters lets us control which section to
					// return or all.
					'type'                      => array( 'registered', 'course', 'quiz' ),

					// Defaults.
					'num'                       => ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_General_Per_Page', 'per_page' ),
					'orderby'                   => 'ID',
					'order'                     => 'ASC',
					'team_id'                  => null,

					// Registered Courses.
					'registered_num'            => false,
					'registered_show_thumbnail' => 'true',
					'registered_orderby'        => 'title',
					'registered_order'          => 'ASC',

					// Course Progress.
					'progress_num'              => false,
					'progress_orderby'          => 'title',
					'progress_order'            => 'ASC',

					// Quizzes.
					'quiz_num'                  => false,
					'quiz_filter_quiz'          => null,
					'quiz_filter_course'        => null,
					'quiz_filter_lesson'        => null,
					'quiz_filter_topic'         => null,
					'quiz_orderby'              => 'taken',
					'quiz_order'                => 'DESC',
				)
			);

			$atts = shortcode_atts( $atts_defaults, $atts );

			if ( ! empty( $atts['type'] ) ) {
				if ( is_string( $atts['type'] ) ) {
					$atts['type'] = explode( ',', $atts['type'] );
				}
				$atts['type'] = array_map( 'trim', $atts['type'] );
			}

			if ( ! empty( $atts['team_id'] ) ) {
				$atts['course_ids'] = ebox_team_enrolled_courses( $atts['team_id'] );
				$atts['quiz_ids']   = ebox_get_team_course_quiz_ids( $atts['team_id'] );
			} else {
				$atts['course_ids'] = null;
				$atts['quiz_ids']   = null;
			}

			if ( ! is_null( $atts['course_ids'] ) ) {
				if ( is_string( $atts['course_ids'] ) ) {
					$atts['course_ids'] = explode( ',', $atts['course_ids'] );
				}
				$atts['course_ids'] = array_map( 'trim', $atts['course_ids'] );
			}

			if ( ! is_null( $atts['quiz_ids'] ) ) {
				if ( is_string( $atts['quiz_ids'] ) ) {
					$atts['quiz_ids'] = explode( ',', $atts['quiz_ids'] );
				}
				$atts['quiz_ids'] = array_map( 'trim', $atts['quiz_ids'] );
			}

			if ( ! is_null( $atts['course_ids'] ) ) {
				$courses_registered_all = $atts['course_ids'];
			} else {
				$courses_registered_all = ld_get_mycourses( $user_id );
			}

			$courses_registered       = array();
			$courses_registered_pager = array();
			if ( in_array( 'registered', $atts['type'], true ) ) {

				if ( empty( $atts['registered_show_thumbnail'] ) ) {
					$atts['registered_show_thumbnail'] = $atts_defaults['registered_show_thumbnail'];
				}

				if ( ! empty( $courses_registered_all ) ) {
					if ( false === $atts['registered_num'] ) {
						$atts['registered_num'] = intval( $atts_defaults['num'] );
					} else {
						$atts['registered_num'] = intval( $atts['registered_num'] );
					}

					if ( ( ! isset( $atts['registered_orderby'] ) ) || ( empty( $atts['registered_orderby'] ) ) ) {
						$atts['registered_orderby'] = $atts_defaults['registered_orderby'];
					}

					if ( ( ! isset( $atts['registered_order'] ) ) || ( empty( $atts['registered_order'] ) ) ) {
						$atts['registered_order'] = $atts_defaults['registered_order'];
					}

					$courses_registered_query_args = array(
						'post_type' => 'ebox-courses',
						'fields'    => 'ids',
						'orderby'   => $atts['registered_orderby'],
						'order'     => $atts['registered_order'],
						'post__in'  => $courses_registered_all,
					);

					/**
					 * Filters value of course information per page.
					 *
					 * @param int    $info_per_page Course info per page.
					 * @param string $context       The context of course info.
					 * @param int    $user_id       User ID.
					 * @param array  $atts          An array of shortcode attributes.
					 */
					$courses_registered_per_page = apply_filters( 'ebox_course_info_per_page', intval( $atts['registered_num'] ), 'registered', $user_id, $atts );
					if ( intval( $courses_registered_per_page ) > 0 ) {
						$courses_registered_query_args['posts_per_page'] = intval( $courses_registered_per_page );
						/**
						 * Filters paged query argument for course info.
						 *
						 * @param int    $paged   Number of Pages.
						 * @param string $context The context of course info.
						 */
						$courses_registered_query_args['paged'] = apply_filters( 'ebox_course_info_paged', 1, 'registered' );
					} else {
						$courses_registered_query_args['nopaging'] = true;
					}

					/**
					 * Filters query arguments for courses registered.
					 *
					 * @param array  $courses_registered_query_args An array of courses registered query arguments.
					 * @param string $context                       The context of course info.
					 * @param int    $user_id                       User ID.
					 * @param array  $atts                          An array of shortcode attributes.
					 */
					$courses_registered_query_args = apply_filters( 'ebox_course_info_query_args', $courses_registered_query_args, 'registered', $user_id, $atts );
					if ( ! empty( $courses_registered_query_args ) ) {
						$course_registered_query = new WP_Query( $courses_registered_query_args );
						if ( ( ! empty( $course_registered_query->posts ) ) ) {
							$courses_registered = $course_registered_query->posts;

							if ( isset( $course_registered_query->query_vars['paged'] ) ) {
								$courses_registered_pager['paged'] = $course_registered_query->query_vars['paged'];
							} else {
								$courses_registered_pager['paged'] = $courses_registered_query_args['paged'];
							}

							$courses_registered_pager['total_items'] = $course_registered_query->found_posts;
							$courses_registered_pager['total_pages'] = $course_registered_query->max_num_pages;
						} else {
							$courses_registered = array();
						}
					} else {
						$courses_registered = array();
					}
				}
			}

			$course_progress       = array();
			$course_progress_pager = array();

			if ( in_array( 'course', $atts['type'], true ) ) {

				$usermeta        = get_user_meta( $user_id, '_ebox-course_progress', true );
				$course_progress = empty( $usermeta ) ? array() : $usermeta;

				if ( ! is_null( $atts['course_ids'] ) ) {
					$course_progress_tmp = array();
					foreach ( $atts['course_ids'] as $course_id ) {
						if ( isset( $course_progress[ $course_id ] ) ) {
							$course_progress_tmp[ $course_id ] = $course_progress[ $course_id ];
						}
					}
					$course_progress     = $course_progress_tmp;
					$course_progress_ids = array_keys( $course_progress );

				} else {
					$course_progress_ids = array_merge( $courses_registered_all, array_keys( $course_progress ) );

					/**
					 * Filters expired courses from course info query
					 *
					 * @since 3.5.0
					 *
					 * @param bool  $include    Whether to include the expired courses or not ( default: true )
					 * @param int   $user_id    User ID
					 */
					if ( true !== apply_filters( 'ebox_user_courseinfo_courses_include_expired', true, $user_id ) ) {
						$course_progress_ids = array_diff( $course_progress_ids, ebox_get_expired_user_courses_from_meta( $user_id ) );
					}
				}

				// The course_info_shortcode.php template is driven be the $courses_registered array.
				// We want to make sure we show ALL the courses from both the $courses_registered and
				// the course_progress. Also we want to run through WP_Query so we can ensure they still
				// exist as valid posts AND we want to sort these by title
				// $courses_registered = array_merge( $courses_registered, array_keys( $course_progress ) );.
				if ( ! empty( $course_progress_ids ) ) {

					if ( false === $atts['progress_num'] ) {
						$atts['progress_num'] = intval( $atts_defaults['num'] );
					} else {
						$atts['progress_num'] = intval( $atts['progress_num'] );
					}

					if ( ( ! isset( $atts['progress_orderby'] ) ) || ( empty( $atts['progress_orderby'] ) ) ) {
						$atts['progress_orderby'] = $atts_defaults['progress_orderby'];
					}

					if ( ( ! isset( $atts['progress_order'] ) ) || ( empty( $atts['progress_order'] ) ) ) {
						$atts['progress_order'] = $atts_defaults['progress_order'];
					}

					$course_progress_query_args = array(
						'post_type' => 'ebox-courses',
						'fields'    => 'ids',
						'orderby'   => $atts['progress_orderby'],
						'order'     => $atts['progress_order'],
						'post__in'  => $course_progress_ids,
					);

					/** This filter is documented in includes/class-ld-lms.php */
					$courses_per_page = apply_filters( 'ebox_course_info_per_page', intval( $atts['progress_num'] ), 'courses', $user_id, $atts );
					if ( intval( $courses_per_page ) > 0 ) {
						$course_progress_query_args['posts_per_page'] = intval( $courses_per_page );

						/** This filter is documented in includes/class-ld-lms.php */
						$course_progress_query_args['paged'] = apply_filters( 'ebox_course_info_paged', 1, 'courses' );
					} else {
						$course_progress_query_args['nopaging'] = true;
					}
					/** This filter is documented in includes/class-ld-lms.php */
					$course_progress_query_args = apply_filters( 'ebox_course_info_query_args', $course_progress_query_args, 'courses', $user_id, $atts );

					if ( ! empty( $course_progress_query_args ) ) {
						$course_progress_query = new WP_Query( $course_progress_query_args );

						if ( ( ! empty( $course_progress_query->posts ) ) ) {
							$course_p        = $course_progress;
							$course_progress = array();
							foreach ( $course_progress_query->posts as $course_id ) {
								if ( isset( $course_p[ $course_id ] ) ) {
									$course_progress[ $course_id ] = $course_p[ $course_id ];
								} else {
									$course_progress[ $course_id ] = array();
								}
							}

							$course_progress_pager = array();
							if ( isset( $course_progress_query->query_vars['paged'] ) ) {
								$course_progress_pager['paged'] = $course_progress_query->query_vars['paged'];
							} else {
								$course_progress_pager['paged'] = $course_progress_query_args['paged'];
							}

							$course_progress_pager['total_items'] = $course_progress_query->found_posts;
							$course_progress_pager['total_pages'] = $course_progress_query->max_num_pages;
						}
					} else {
						$course_progress       = array();
						$course_progress_pager = array();
					}
				}
			}

			$quizzes       = array();
			$quizzes_pager = array();
			if ( in_array( 'quiz', $atts['type'], true ) ) {

				$usermeta = get_user_meta( $user_id, '_ebox-quizzes', true );
				$quizzes  = empty( $usermeta ) ? false : $usermeta;

				// We need to re-query the quiz (posts). This is partly to validate the listing. We don't
				// want to pass old or outdated quiz items to externals.
				if ( ! empty( $quizzes ) ) {

					if ( false === $atts['quiz_num'] ) {
						$atts['quiz_num'] = intval( $atts_defaults['num'] );
					} else {
						$atts['quiz_num'] = intval( $atts['quiz_num'] );
					}

					if ( ( ! isset( $atts['quiz_orderby'] ) ) || ( empty( $atts['quiz_orderby'] ) ) ) {
						$atts['quiz_orderby'] = $atts_defaults['quiz_orderby'];
					}

					if ( ( ! isset( $atts['quiz_order'] ) ) || ( empty( $atts['quiz_order'] ) ) ) {
						$atts['quiz_order'] = $atts_defaults['quiz_order'];
					}

					if ( ! is_null( $atts['quiz_ids'] ) ) {
						$quiz_ids = $atts['quiz_ids'];
					} elseif ( ! is_null( $atts['quiz_filter_quiz'] ) ) {
						$quiz_ids = $atts['quiz_filter_quiz'];
					} else {
						$quiz_ids = wp_list_pluck( $quizzes, 'quiz' );
					}

					if ( ! empty( $quiz_ids ) ) {
						if ( ! is_array( $quiz_ids ) ) {
							$quiz_ids = explode( ',', $quiz_ids );
						}
						$quiz_ids = array_map( 'absint', $quiz_ids );
					}

					if ( ! empty( $atts['quiz_filter_course'] ) ) {
						if ( ! is_array( $atts['quiz_filter_course'] ) ) {
							$atts['quiz_filter_course'] = explode( ',', $atts['quiz_filter_course'] );
						}
						$atts['quiz_filter_course'] = array_map( 'absint', $atts['quiz_filter_course'] );
					}

					if ( ! empty( $atts['quiz_filter_lesson'] ) ) {
						if ( ! is_array( $atts['quiz_filter_lesson'] ) ) {
							$atts['quiz_filter_lesson'] = explode( ',', $atts['quiz_filter_lesson'] );
						}
						$atts['quiz_filter_lesson'] = array_map( 'absint', $atts['quiz_filter_lesson'] );
					}

					if ( ! empty( $atts['quiz_filter_topic'] ) ) {
						if ( ! is_array( $atts['quiz_filter_topic'] ) ) {
							$atts['quiz_filter_topic'] = explode( ',', $atts['quiz_filter_topic'] );
						}
						$atts['quiz_filter_topic'] = array_map( 'absint', $atts['quiz_filter_topic'] );
					}

					$quiz_total_query_args = array(
						'post_type' => 'ebox-quiz',
						'fields'    => 'ids',
						'orderby'   => 'title',
						'order'     => 'ASC',
						'nopaging'  => true,
						'post__in'  => $quiz_ids,
					);

					if ( 'taken' === $atts['quiz_orderby'] ) {
						$quiz_total_query_args['orderby'] = 'title';
					}

					$quiz_query = new WP_Query( $quiz_total_query_args );
					if ( is_a( $quiz_query, 'WP_Query' ) ) {
						if ( ( property_exists( $quiz_query, 'posts' ) ) && ( ! empty( $quiz_query->posts ) ) ) {
							$quizzes_tmp = array();
							foreach ( $quiz_query->posts as $post_idx => $quiz_id ) {
								foreach ( $quizzes as $quiz_idx => $quiz_attempt ) {
									if ( (int) $quiz_attempt['quiz'] == (int) $quiz_id ) {
										if ( ! empty( $atts['quiz_filter_course'] ) ) {
											if ( ( ! isset( $quiz_attempt['course'] ) ) || ( empty( $quiz_attempt['course'] ) ) ) {
												continue;
											}
											if ( ! in_array( absint( $quiz_attempt['course'] ), $atts['quiz_filter_course'] ) ) {
												continue;
											}
										}

										if ( ! empty( $atts['quiz_filter_lesson'] ) ) {
											if ( ( ! isset( $quiz_attempt['lesson'] ) ) || ( empty( $quiz_attempt['lesson'] ) ) ) {
												continue;
											}
											if ( ! in_array( absint( $quiz_attempt['lesson'] ), $atts['quiz_filter_lesson'] ) ) {
												continue;
											}
										}

										if ( ! empty( $atts['quiz_filter_topic'] ) ) {
											if ( ( ! isset( $quiz_attempt['topic'] ) ) || ( empty( $quiz_attempt['topic'] ) ) ) {
												continue;
											}
											if ( ! in_array( absint( $quiz_attempt['topic'] ), $atts['quiz_filter_topic'] ) ) {
												continue;
											}
										}

										if ( 'taken' === $atts['quiz_orderby'] ) {
											$quiz_key = $quiz_attempt['time'] . '-' . $quiz_attempt['quiz'];
										} elseif ( 'title' == $atts['quiz_orderby'] ) {
											$quiz_key = $post_idx . '-' . $quiz_attempt['time'];
										} elseif ( 'ID' == $atts['quiz_orderby'] ) {
											$quiz_key = str_pad( (string) $quiz_attempt['quiz'], 10, '0', STR_PAD_LEFT ) . '-' . $quiz_attempt['time'];
										} elseif ( 'date' == $atts['quiz_orderby'] ) { // Quiz Post date.
											$quiz_post = get_post( $quiz_attempt['quiz'] );
											if ( is_a( $quiz_post, 'WP_Post' ) ) {
												$quiz_key = $quiz_post->post_date . '-' . $quiz_attempt['time'];
											} else {
												$quiz_key = $post_idx . '-' . $quiz_attempt['time'];
											}
										} elseif ( 'menu_order' == $atts['quiz_orderby'] ) { // Quiz Post menu_order.
											$quiz_post = get_post( $quiz_attempt['quiz'] );
											if ( is_a( $quiz_post, 'WP_Post' ) ) {
												$quiz_key = $quiz_post->menu_order . '-' . $quiz_attempt['time'];
											} else {
												$quiz_key = $post_idx . '-' . $quiz_attempt['time'];
											}
										}
										if ( ! empty( $quiz_key ) ) {
											$quizzes_tmp[ $quiz_key ] = $quiz_attempt;
											unset( $quizzes[ $quiz_idx ] );
										}
									}
								}
							}

							$quizzes = $quizzes_tmp;

							if ( 'DESC' == $atts['quiz_order'] ) {
								krsort( $quizzes );
							} else {
								ksort( $quizzes );
							}

							/**
							 * Filters value of quiz information per page.
							 *
							 * @param int    $info_per_page Quiz info per page.
							 * @param string $context       The context of course info.
							 * @param int    $user_id       User ID.
							 */
							$quizzes_per_page = apply_filters( 'ebox_quiz_info_per_page', $atts['quiz_num'], 'quizzes', $user_id );
							if ( $quizzes_per_page > 0 ) {

								/**
								 * Filters paged query argument for quiz info.
								 *
								 * @param int $paged Number of Pages.
								 */
								$quizzes_pager['paged']       = apply_filters( 'ebox_quiz_info_paged', 1 );
								$quizzes_pager['total_items'] = count( $quizzes );
								$quizzes_pager['total_pages'] = ceil( count( $quizzes ) / $quizzes_per_page );

								$quizzes = array_slice( $quizzes, ( $quizzes_pager['paged'] * $quizzes_per_page ) - $quizzes_per_page, $quizzes_per_page, false );
							}
						}
					}
				}
			}

			/**
			 * Filter Courses and Quizzes is showing the Team Admin > Report page
			 * IF we are viewing the team_admin_page we want to filter the Courses and Quizzes listing
			 * to only include those items related to the Team
			 *
			 * @since 2.3.0
			 */
			global $pagenow;
			if ( ( ! empty( $pagenow ) ) && ( 'admin.php' === $pagenow ) ) {
				if ( ( isset( $_GET['page'] ) ) && ( 'team_admin_page' == $_GET['page'] ) ) {
					if ( ( isset( $_GET['team_id'] ) ) && ( ! empty( $_GET['team_id'] ) ) ) {
						$team_id = intval( $_GET['team_id'] );

						if ( ( isset( $_GET['user_id'] ) ) && ( ! empty( $_GET['user_id'] ) ) ) {
							$user_id = intval( $_GET['user_id'] );

							if ( ebox_is_team_leader_of_user( get_current_user_id(), $user_id ) ) {
								if ( ebox_is_user_in_team( intval( $_GET['user_id'] ), intval( $_GET['team_id'] ) ) ) {
									if ( isset( $_POST['ebox_course_points'] ) ) {
										update_user_meta( $user_id, 'course_points', intval( $_POST['ebox_course_points'] ) );
									}
								}
							}
						}
					}
				}
			}

			if ( ! empty( $atts['return'] ) ) {
				return array(
					'user_id'                  => $user_id,
					'courses_registered'       => $courses_registered,
					'courses_registered_pager' => $courses_registered_pager,
					'course_progress'          => $course_progress,
					'course_progress_pager'    => $course_progress_pager,
					'quizzes'                  => $quizzes,
					'quizzes_pager'            => $quizzes_pager,
				);
			} else {

				if ( is_admin() ) {
					if ( ! empty( $pagenow ) ) {
						if ( ( 'profile.php' === $pagenow ) || ( 'user-edit.php' === $pagenow ) ) {
							$atts['pagenow']       = $pagenow;
							$atts['pagenow_nonce'] = wp_create_nonce( $pagenow . '-' . $user_id );
						} elseif ( ( 'admin.php' === $pagenow ) && ( isset( $_GET['page'] ) ) && ( 'team_admin_page' == $_GET['page'] ) ) {
							$atts['pagenow'] = esc_attr( $_GET['page'] );

							if ( ( isset( $_GET['team_id'] ) ) && ( ! empty( $_GET['team_id'] ) ) ) {
								$atts['team_id'] = intval( $_GET['team_id'] );
							} else {
								$atts['team_id'] = 0;
							}
							$atts['pagenow_nonce'] = wp_create_nonce( esc_attr( $_GET['page'] ) . '-' . $atts['team_id'] . '-' . $user_id );
						} else {
							$atts['pagenow']       = 'ebox';
							$atts['pagenow_nonce'] = wp_create_nonce( $atts['pagenow'] . '-' . $user_id );
						}
					}
				} else {
					$atts['pagenow']       = 'ebox';
					$atts['pagenow_nonce'] = wp_create_nonce( $atts['pagenow'] . '-' . $user_id );
				}
				$atts['user_id'] = $user_id;

				unset( $atts['course_ids'] );
				unset( $atts['quiz_ids'] );

				return self::get_template(
					'course_info_shortcode',
					array(
						'user_id'                  => $user_id,
						'courses_registered'       => $courses_registered,
						'courses_registered_pager' => $courses_registered_pager,
						'course_progress'          => $course_progress,
						'course_progress_pager'    => $course_progress_pager,
						'quizzes'                  => $quizzes,
						'quizzes_pager'            => $quizzes_pager,
						'shortcode_atts'           => $atts,
					)
				);
			}
		}

		/**
		 * Updates course price billy cycle on save
		 * Fires on action 'save_post'
		 *
		 * @since 2.1.0
		 *
		 * @param int    $post_id Post ID for save.
		 * @param object $post    WP_Post object for save.
		 * @param bool   $update  If save is update (true).
		 */
		public function ebox_course_price_billing_cycle_save( $post_id, $post, $update = false ) {
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			if ( empty( $post_id ) || empty( $_POST['post_type'] ) ) {
				return '';
			}

			// Check permissions.
			if ( 'page' == $_POST['post_type'] ) {
				if ( ! current_user_can( 'edit_page', $post_id ) ) {
					return;
				}
			} else {
				if ( ! current_user_can( 'edit_post', $post_id ) ) {
					return;
				}
			}

			if ( in_array( $post->post_type, array( ebox_get_post_type_slug( 'course' ), ebox_get_post_type_slug( 'team' ) ), true ) ) {

				if ( ebox_get_post_type_slug( 'course' ) === $post->post_type ) {
					$settings_prefix = 'course';
				} elseif ( ebox_get_post_type_slug( 'team' ) === $post->post_type ) {
					$settings_prefix = 'team';
				} else {
					// For phpstan check.
					return;
				}

				$price_billing_t3 = '';
				$price_billing_p3 = '';

				if ( isset( $_POST[ $settings_prefix . '_price_billing_t3' ] ) ) {
					$price_billing_t3 = strtoupper( esc_attr( $_POST[ $settings_prefix . '_price_billing_t3' ] ) );
					$price_billing_t3 = ebox_billing_cycle_field_frequency_validate( $price_billing_t3 );
				}

				if ( isset( $_POST[ $settings_prefix . '_price_billing_p3' ] ) ) {
					$price_billing_p3 = absint( $_POST[ $settings_prefix . '_price_billing_p3' ] );
					$price_billing_p3 = ebox_billing_cycle_field_interval_validate( $price_billing_p3, $price_billing_t3 );
				}

				if ( ( ! empty( $price_billing_t3 ) ) && ( ! empty( $price_billing_p3 ) ) ) {
					update_post_meta( $post_id, $settings_prefix . '_price_billing_p3', $price_billing_p3 );
					update_post_meta( $post_id, $settings_prefix . '_price_billing_t3', $price_billing_t3 );
				} else {
					delete_post_meta( $post_id, $settings_prefix . '_price_billing_p3' );
					delete_post_meta( $post_id, $settings_prefix . '_price_billing_t3' );
				}
			}
		}

		/**
		 * Billing Cycle field html output for courses
		 *
		 * @since 2.1.0
		 *
		 * @return string
		 */
		public function ebox_course_price_billing_cycle_html() {
			return ebox_billing_cycle_setting_field_html();
		}

		/**
		 * Course progress data
		 *
		 * @param int $course_id Course ID.
		 */
		public static function course_progress_data( $course_id = null ) {
			set_time_limit( 0 );
			global $wpdb;

			$current_user = wp_get_current_user();
			if ( ( ! ebox_is_admin_user( $current_user->ID ) ) && ( ! ebox_is_team_leader_user( $current_user->ID ) ) ) {
				return;
			}

			$team_id = 0;
			if ( isset( $_GET['team_id'] ) ) {
				$team_id = $_GET['team_id'];
			}

			if ( ebox_is_team_leader_user( $current_user->ID ) ) {

				$users_team_ids = ebox_get_administrators_team_ids( $current_user->ID );
				if ( ! count( $users_team_ids ) ) {
					return array();
				}

				if ( ! empty( $team_id ) ) {
					if ( ! in_array( $team_id, $users_team_ids ) ) {
						return;
					}
					$users_team_ids = array( $team_id );
				}

				$all_user_ids = array();
				// First get the user_ids for each team...
				foreach ( $users_team_ids as $users_team_id ) {
					$user_ids = ebox_get_teams_user_ids( $users_team_id );
					if ( ! empty( $user_ids ) ) {
						if ( ! empty( $all_user_ids ) ) {
							$all_user_ids = array_merge( $all_user_ids, $user_ids );
						} else {
							$all_user_ids = $user_ids;
						}
					}
				}

				// Then once we have all the teams user_id run a last query for the complete user ids.
				if ( ! empty( $all_user_ids ) ) {
					$user_query_args = array(
						'include' => $all_user_ids,
						'orderby' => 'display_name',
						'order'   => 'ASC',
					);

					$user_query = new WP_User_Query( $user_query_args );

					if ( ! empty( $user_query->get_results() ) ) {
						$users = $user_query->get_results();
					}
				}
			} elseif ( ebox_is_admin_user( $current_user->ID ) ) {
				if ( ! empty( $team_id ) ) {
					$users = ebox_get_teams_users( $team_id );
				} else {
					$users = get_users(
						array(
							'orderby' => 'display_name',
							'order'   => 'ASC',
						)
					);
				}
			} else {
				return array();
			}

			if ( empty( $users ) ) {
				return array();
			}

			$course_access_list = array();

			$course_progress_data = array();
			set_time_limit( 0 );

			$quiz_titles = array();
			$modules     = array();

			if ( ! empty( $course_id ) ) {
				$courses = array( get_post( $course_id ) );
			} elseif ( ! empty( $team_id ) ) {
				$courses = ebox_team_enrolled_courses( $team_id );
				$courses = array_map( 'intval', $courses );
				$courses = ld_course_list(
					array(
						'post__in' => $courses,
						'array'    => true,
					)
				);
			} else {
				$courses = ld_course_list( array( 'array' => true ) );
			}

			if ( is_array( $users ) ) {

				foreach ( $users as $u ) {

					$user_id  = $u->ID;
					$usermeta = get_user_meta( $user_id, '_ebox-course_progress', true );
					if ( ! empty( $usermeta ) ) {
						$usermeta = maybe_unserialize( $usermeta );
					}

					if ( is_array( $courses ) ) {
						foreach ( $courses as $course ) {
							if ( is_a( $course, 'WP_Post' ) ) {
								$c = $course->ID;

								if ( empty( $course->post_title ) || ! ebox_lms_has_access( $c, $user_id ) ) {
									continue;
								}

								$cv = ! empty( $usermeta[ $c ] ) ? $usermeta[ $c ] : array(
									'completed' => '',
									'total'     => '',
								);

								$course_completed_meta                                       = get_user_meta( $user_id, 'course_completed_' . $course->ID, true );
								( empty( $course_completed_meta ) ) ? $course_completed_date = '' : $course_completed_date = date_i18n( 'F j, Y H:i:s', $course_completed_meta );

								$row = array(
									'user_id'             => $user_id,
									'name'                => $u->display_name,
									'email'               => $u->user_email,
									'course_id'           => $c,
									'course_title'        => $course->post_title,
									'total_steps'         => $cv['total'],
									'completed_steps'     => $cv['completed'],
									'course_completed'    => ( ! empty( $cv['total'] ) && $cv['completed'] >= $cv['total'] ) ? 'YES' : 'NO',
									'course_completed_on' => $course_completed_date,
								);

								$i = 1;
								if ( ! empty( $cv['modules'] ) ) {
									foreach ( $cv['modules'] as $lesson_id => $completed ) {
										if ( ! empty( $completed ) ) {
											if ( empty( $modules[ $lesson_id ] ) ) {
												$lesson                = get_post( $lesson_id );
												$modules[ $lesson_id ] = $lesson;
											} else {
												$lesson = $modules[ $lesson_id ];
											}

											$row[ 'lesson_completed_' . $i ] = $lesson->post_title;
											$i++;
										}
									}
								}

								$course_progress_data[] = $row;
							}
						} // end foreach
					} else {
						$course_progress_data[] = array(
							'user_id' => $user_id,
							'name'    => $u->display_name,
							'email'   => $u->user_email,
							'status'  => esc_html__( 'No attempts', 'ebox' ),
						);
					} // end if
				} // end foreach
			}

			/**
			 * Filters course progress data to be displayed.
			 *
			 * @since 2.1.0
			 *
			 * @param array  $course_progress_data An array of course progress data.
			 * @param array  $users                An array of user list.
			 * @param int    $team_id             Team ID.
			 */
			$course_progress_data = apply_filters( 'course_progress_data', $course_progress_data, $users, (int) $team_id );

			return $course_progress_data;
		}



		/**
		 * Exports course progress data to CSV file
		 *
		 * @since 2.1.0
		 */
		public function course_export_init() {
			// @phpstan-ignore-next-line Constant may or may not be defined by user.
			if ( ( defined( 'ebox_ERROR_REPORTING_ZERO' ) ) && ( true === ebox_ERROR_REPORTING_ZERO ) ) {
				error_reporting( 0 ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.runtime_configuration_error_reporting, WordPress.PHP.DevelopmentFunctions.prevent_path_disclosure_error_reporting -- I hope they knew what they were doing.
			}

			if ( ! empty( $_REQUEST['courses_export_submit'] ) && ! empty( $_REQUEST['nonce-ebox'] ) ) {
				set_time_limit( 0 );

				$default_tz = get_option( 'timezone_string' );
				if ( ! empty( $default_tz ) ) {
					date_default_timezone_set( $default_tz ); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.timezone_change_date_default_timezone_set -- I hope they knew what they were doing.
				}

				$nonce = $_REQUEST['nonce-ebox'];

				if ( ! wp_verify_nonce( $nonce, 'ebox-nonce' ) ) {
					die( esc_html__( 'Security Check - If you receive this in error, log out and back in to WordPress', 'ebox' ) );
				}

				$content = self::course_progress_data();

				if ( empty( $content ) ) {
					$content[] = array( 'status' => esc_html__( 'No attempts', 'ebox' ) );
				}

				/**
				 * Include parseCSV to write csv file.
				 */
				require_once ebox_LMS_LIBRARY_DIR . '/parsecsv.lib.php';

				$csv                  = new lmsParseCSV();
				$csv->file            = 'courses.csv';
				$csv->output_filename = 'courses.csv';
				/**
				 * Filters csv object.
				 *
				 * @since 2.3.2
				 *
				 * @param \lmsParseCSV $csv CSV object.
				 * @param string       $context The context of the csv object.
				 */
				$csv = apply_filters( 'ebox_csv_object', $csv, 'courses' );
				/**
				 * Filters the content will print onto the exported CSV
				 *
				 * @since 2.1.0
				 *
				 * @param void|array|mixed $content CSV content.
				 */
				$content = apply_filters( 'course_export_data', $content );

				$csv->output( 'courses.csv', $content, array_keys( reset( $content ) ) );
				die();
			}
		}



		/**
		 * Course Export Button submit data
		 *
		 * Apply_filters ran in display_settings_page() in ebox_module_class.php
		 *
		 * @todo  currently no add_filter using this callback
		 *        consider for deprecation or implement add_filter
		 *
		 * @since 2.1.0
		 *
		 * @param  array $submit Submit.
		 * @return array $submit
		 */
		public function courses_filter_submit( $submit ) {
			$submit['courses_export_submit'] = array(
				'type'  => 'submit',
				'class' => 'button-primary',
				// translators: placeholder: Course.
				'value' => sprintf( esc_html_x( 'Export User %s Data &raquo;', 'placeholder: Course', 'ebox' ), ebox_Custom_Label::get_label( 'course' ) ),
			);
			return $submit;
		}

		/**
		 * Export quiz data to CSV
		 *
		 * @since 2.1.0
		 */
		public function quiz_export_init() {
			// @phpstan-ignore-next-line Constant may or may not be defined by user.
			if ( ( defined( 'ebox_ERROR_REPORTING_ZERO' ) ) && ( true === ebox_ERROR_REPORTING_ZERO ) ) {
				error_reporting( 0 ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.runtime_configuration_error_reporting, WordPress.PHP.DevelopmentFunctions.prevent_path_disclosure_error_reporting -- I hope they knew what they were doing.
			}

			global $wpdb;
			$current_user = wp_get_current_user();

			if ( ( ! ebox_is_admin_user( $current_user->ID ) ) && ( ! ebox_is_team_leader_user( $current_user->ID ) ) ) {
				return;
			}
			// Why are these 3 lines here??
			$ebox_quiz   = $this->post_types['ebox-quiz'];
			$quiz_prefix = $ebox_quiz->get_prefix();
			add_filter( $quiz_prefix . 'submit_options', array( $this, 'quiz_filter_submit' ) );

			if ( ! empty( $_REQUEST['quiz_export_submit'] ) && ! empty( $_REQUEST['nonce-ebox'] ) ) {
				$timezone_string = get_option( 'timezone_string' );
				if ( ! empty( $timezone_string ) ) {
					date_default_timezone_set( $timezone_string ); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.timezone_change_date_default_timezone_set -- I hope they knew what they were doing.
				}

				if ( ! wp_verify_nonce( $_REQUEST['nonce-ebox'], 'ebox-nonce' ) ) {
					die( esc_html__( 'Security Check - If you receive this in error, log out and back in to WordPress', 'ebox' ) );
				}

				/**
				 * Include parseCSV to write csv file.
				 */
				require_once ebox_LMS_LIBRARY_DIR . '/parsecsv.lib.php';

				$content = array();
				set_time_limit( 0 );
				// Need ability to export quiz results for team to CSV.

				$team_id = null;
				if ( isset( $_GET['team_id'] ) ) {
					$team_id = $_GET['team_id'];
				}

				$users = array();
				if ( ebox_is_team_leader_user( $current_user->ID ) ) {

					$users_team_ids = ebox_get_administrators_team_ids( $current_user->ID );
					if ( ! count( $users_team_ids ) ) {
						return array();
					}

					if ( isset( $team_id ) ) {
						if ( ! in_array( $team_id, $users_team_ids ) ) {
							return;
						}
						$users_team_ids = array( $team_id );
					}

					$all_user_ids = array();
					// First get the user_ids for each team...
					foreach ( $users_team_ids as $users_team_id ) {
						$user_ids = ebox_get_teams_user_ids( $users_team_id );
						if ( ! empty( $user_ids ) ) {
							if ( ! empty( $all_user_ids ) ) {
								$all_user_ids = array_merge( $all_user_ids, $user_ids );
							} else {
								$all_user_ids = $user_ids;
							}
						}
					}

					// Then once we have all the teams user_id run a last query for the complete user ids.
					if ( ! empty( $all_user_ids ) ) {
						$user_query_args = array(
							'include'    => $all_user_ids,
							'orderby'    => 'display_name',
							'order'      => 'ASC',
							'meta_query' => array(
								array(
									'key'     => '_ebox-quizzes',
									'compare' => 'EXISTS',
								),
							),
						);

						$user_query = new WP_User_Query( $user_query_args );

						if ( ! empty( $user_query->get_results() ) ) {
							$users = $user_query->get_results();
						}
					}
				} elseif ( ebox_is_admin_user( $current_user->ID ) ) {
					if ( ! empty( $team_id ) ) {
						$user_ids = ebox_get_teams_user_ids( $team_id );
						if ( ! empty( $user_ids ) ) {
							$user_query_args = array(
								'include'    => $user_ids,
								'orderby'    => 'display_name',
								'order'      => 'ASC',
								'meta_query' => array(
									array(
										'key'     => '_ebox-quizzes',
										'compare' => 'EXISTS',
									),
								),
							);

							$user_query = new WP_User_Query( $user_query_args );
							if ( ! empty( $user_query->get_results() ) ) {
								$users = $user_query->get_results();
							} else {
								$users = array();
							}
						}
					} else {

						$user_query_args = array(
							'orderby'    => 'display_name',
							'order'      => 'ASC',
							'meta_query' => array(
								array(
									'key'     => '_ebox-quizzes',
									'compare' => 'EXISTS',
								),
							),
						);

						$user_query = new WP_User_Query( $user_query_args );
						if ( ! empty( $user_query->get_results() ) ) {
							$users = $user_query->get_results();
						} else {
							$users = array();
						}
					}
				} else {
					return array();
				}

				$quiz_titles = array();

				if ( ! empty( $users ) ) {

					foreach ( $users as $u ) {

						$user_id  = $u->ID;
						$usermeta = get_user_meta( $user_id, '_ebox-quizzes', true );

						if ( ! empty( $usermeta ) ) {

							foreach ( $usermeta as $k => $v ) {

								if ( ! empty( $team_id ) ) {
									$course_id = ebox_get_course_id( intval( $v['quiz'] ) );
									if ( ! ebox_team_has_course( $team_id, $course_id ) ) {
										continue;
									}
								}

								if ( empty( $quiz_titles[ $v['quiz'] ] ) ) {

									if ( ! empty( $v['quiz'] ) ) {
										$quiz = get_post( $v['quiz'] );

										if ( empty( $quiz ) ) {
											continue;
										}

										$quiz_titles[ $v['quiz'] ] = $quiz->post_title;

									} elseif ( ! empty( $v['pro_quizid'] ) ) {

										$quiz = get_post( $v['pro_quizid'] );

										if ( empty( $quiz ) ) {
											continue;
										}

										$quiz_titles[ $v['quiz'] ] = $quiz->post_title;

									} else {
										$quiz_titles[ $v['quiz'] ] = '';
									}
								}

								// After LD v2.2.1.2 we made a changes to the quiz user meta 'count' value output. Up to that point if the quiz showed only partial
								// questions, like 5 of 10 total then the value of $v[count] would be 10 instead of only the shown count 5.
								// After LD v2.2.1.2 we added a new field 'question_show_count' to hold the number of questions shown to the user during
								// the quiz.
								// But on legacy quiz user meta we needed a way to pull that information from the quiz...

								if ( ! isset( $v['question_show_count'] ) ) {
									$v['question_show_count'] = $v['count'];

									// ...If we have the statistics ref ID then we can pull the number of questions from there.
									if ( ( isset( $v['statistic_ref_id'] ) ) && ( ! empty( $v['statistic_ref_id'] ) ) ) {
										global $wpdb;

										$count = $wpdb->get_var(
											$wpdb->prepare( ' SELECT count(*) as count FROM ' . esc_sql( LDLMS_DB::get_table_name( 'quiz_statistic' ) ) . ' WHERE statistic_ref_id = %d', $v['statistic_ref_id'] )
										);
										if ( ! $count ) {
											$count = 0;
										}
										$v['question_show_count'] = intval( $count );
									} else {
										// .. or if the statistics is not enabled for this quiz then we get the question show count from the
										// quiz data. Note there is a potential hole in the logic here. If this quiz setting changes then existing
										// quiz user meta reports will also be effected.
										$pro_quiz_id = get_post_meta( $v['quiz'], 'quiz_pro_id', true );
										if ( ! empty( $pro_quiz_id ) ) {
											$quiz_mapper = new WpProQuiz_Model_QuizMapper();
											$quiz        = $quiz_mapper->fetch( $pro_quiz_id );

											if ( ( $quiz->isShowMaxQuestion() ) && ( $quiz->getShowMaxQuestionValue() > 0 ) ) {
												$v['question_show_count'] = $quiz->getShowMaxQuestionValue();
											}
										}
									}
								}

								$content[] = array(
									'user_id'    => $user_id,
									'name'       => $u->display_name,
									'email'      => $u->user_email,
									'quiz_id'    => $v['quiz'],
									'quiz_title' => $quiz_titles[ $v['quiz'] ],
									'rank'       => $v['rank'],
									'score'      => $v['score'],
									'total'      => $v['question_show_count'],
									'date'       => date_i18n( DATE_RSS, $v['time'] ),
								);
							}
						} else {
							$content[] = array(
								'user_id'    => $user_id,
								'name'       => $u->display_name,
								'email'      => $u->user_email,
								'quiz_id'    => esc_html__(
									'No attempts',
									'ebox'
								),
								'quiz_title' => '',
								'rank'       => '',
								'score'      => '',
								'total'      => '',
								'date'       => '',
							);
						} // end if
					} // end foreach
				} // end if

				if ( empty( $content ) ) {
					$content[] = array( 'status' => esc_html__( 'No attempts', 'ebox' ) );
				}

				/**
				 * Filters quiz data that will print to CSV.
				 *
				 * @since 2.1.0
				 *
				 * @param array $content   CSV content.
				 * @param array $users     An array of users list.
				 * @param int   $team_id Team ID.
				 */
				$content = apply_filters( 'quiz_export_data', $content, $users, (int) $team_id );

				$csv                  = new lmsParseCSV();
				$csv->file            = 'quizzes.csv';
				$csv->output_filename = 'quizzes.csv';
				/** This filter is documented in includes/class-ld-lms.php */
				$csv = apply_filters( 'ebox_csv_object', $csv, 'quizzes' );

				$csv->output( 'quizzes.csv', $content, array_keys( reset( $content ) ) );
				die();

			}
		}

		/**
		 * Quiz Export Button submit data
		 *
		 * Filter callback for $quiz_prefix . 'submit_options'
		 * apply_filters ran in display_settings_page() in ebox_module_class.php
		 *
		 * @since 2.1.0
		 *
		 * @param  array $submit Submit.
		 * @return array
		 */
		public function quiz_filter_submit( $submit ) {
			$submit['quiz_export_submit'] = array(
				'type'  => 'submit',
				'class' => 'button-primary',
				// translators: placeholder: Quiz.
				'value' => sprintf( esc_html_x( 'Export %s Data &raquo;', 'placeholder: Quiz', 'ebox' ), ebox_Custom_Label::get_label( 'quiz' ) ),
			);
			return $submit;
		}



		/**
		 * Export transactions to CSV file
		 *
		 * Not currently being used in plugin
		 *
		 * @todo consider for deprecation or implement in plugin
		 *
		 * @since 2.1.0
		 */
		public function trans_export_init() {
			$ebox_trans   = $this->post_types['ebox-transactions'];
			$trans_prefix = $ebox_trans->get_prefix();
			add_filter( $trans_prefix . 'submit_options', array( $this, 'trans_filter_submit' ) );

			if ( ! empty( $_REQUEST['export_submit'] ) && ! empty( $_REQUEST['nonce-ebox'] ) ) {
				$nonce = $_REQUEST['nonce-ebox'];

				if ( ! wp_verify_nonce( $nonce, 'ebox-nonce' ) ) {
					die( esc_html__( 'Security Check - If you receive this in error, log out and back in to WordPress', 'ebox' ) );
				}

				/**
				 * Include parseCSV to write csv file
				 */
				require_once ebox_LMS_LIBRARY_DIR . '/parsecsv.lib.php';

				$content = array();
				set_time_limit( 0 );

				// phpcs:ignore WordPress.WP.DiscouragedFunctions.query_posts_query_posts -- Main file, better not to touch.
				$locations = query_posts(
					array(
						'post_status'    => 'publish',
						'post_type'      => 'ebox-transactions',
						'posts_per_page' => -1,
					)
				);

				foreach ( $locations as $key => $location ) {
					$location_data = get_post_custom( $location->ID );
					foreach ( $location_data as $k => $v ) {
						if ( '_' == $k[0] ) {
							unset( $location_data[ $k ] );
						} else {
							$location_data[ $k ] = $v[0];
						}
					}
					$content[] = $location_data;
				}

				if ( ! empty( $content ) ) {
					$csv                  = new lmsParseCSV();
					$csv->file            = 'transactions.csv';
					$csv->output_filename = 'transactions.csv';
					/** This filter is documented in includes/class-ld-lms.php */
					$csv = apply_filters( 'ebox_csv_object', $csv, 'transactions' );

					$csv->output( true, 'transactions.csv', $content, array_keys( reset( $content ) ) );
				}

				die();
			}
		}



		/**
		 * Transaction Export Button submit data
		 *
		 * Filter callback for $trans_prefix . 'submit_options'
		 * apply_filters ran in display_settings_page() in ebox_module_class.php
		 *
		 * @since 2.1.0
		 *
		 * @param  array $submit Submit.
		 * @return array
		 */
		public function trans_filter_submit( $submit ) {
			unset( $submit['Submit'] );
			unset( $submit['Submit_Default'] );

			$submit['export_submit'] = array(
				'type'  => 'submit',
				'class' => 'button-primary',
				'value' => esc_html__( 'Export &raquo;', 'ebox' ),
			);

			return $submit;
		}

		/**
		 * Set up quiz display settings
		 *
		 * Filter callback for '{$quiz_prefix}display_settings'
		 * apply_filters in display_options() in swfd_module_class.php
		 *
		 * @since 2.1.0
		 * @deprecated 3.4.0
		 *
		 * @param  array  $settings        quiz settings.
		 * @param  string $location        where these settings are being displayed.
		 * @param  array  $current_options current options stored for a given location.
		 * @return array                   quiz settings
		 */
		public function quiz_display_settings( $settings, $location, $current_options ) {
			if ( function_exists( '_deprecated_function' ) ) {
				_deprecated_function( __FUNCTION__, '3.4.0' );
			}

			return $settings;
		}

		/**
		 * Set up question display settings
		 *
		 * Filter callback for '{$question_prefix}display_settings'
		 * apply_filters in display_options() in swfd_module_class.php
		 *
		 * @since 2.1.0
		 *
		 * @param  array  $settings        quiz settings.
		 * @param  string $location        where these settings are being displayed.
		 * @param  array  $current_options current options stored for a given location.
		 * @return array                   quiz settings
		 */
		public function question_display_settings( $settings, $location, $current_options ) {
			global $ebox_lms;
			$ebox_question   = $ebox_lms->post_types['ebox-question'];
			$question_prefix = $ebox_question->get_prefix();

			$prefix_len       = strlen( $question_prefix );
			$question_options = $ebox_question->get_current_options();

			if ( ! empty( $location ) ) {
				global $pagenow;
				if ( ( 'post.php' == $pagenow ) || ( 'post-new.php' == $pagenow ) ) {
					$current_screen = get_current_screen();
					if ( 'ebox-question' === $current_screen->post_type ) {

						if ( ( isset( $settings[ "{$question_prefix}quiz" ] ) ) && ( ! empty( $settings[ "{$question_prefix}quiz" ] ) ) ) {

							$_settings = $settings[ "{$question_prefix}quiz" ];

							$query_options = array(
								'post_type'      => 'ebox-quiz',
								'post_status'    => 'any',
								'posts_per_page' => -1,
								'exclude'        => get_the_id(),
								'orderby'        => 'title',
								'order'          => 'ASC',
							);

							/** This filter is documented in includes/class-ld-lms.php */
							$lazy_load = apply_filters( 'ebox_element_lazy_load_admin', true );
							if ( ( true == $lazy_load ) && ( isset( $_settings['lazy_load'] ) ) && ( true == $_settings['lazy_load'] ) ) {
								$query_options['paged'] = 1;
								/** This filter is documented in includes/class-ld-lms.php */
								$query_options['posts_per_page'] = apply_filters( 'ebox_element_lazy_load_per_page', ebox_LMS_DEFAULT_LAZY_LOAD_PER_PAGE, "{$question_prefix}quiz" );
							}

							/**
							 * Filters quiz question query arguments.
							 *
							 * @since 2.1.0
							 *
							 * @param array $query_options Query arguments.
							 * @param array $settings      Quiz question settings.
							 */
							$query_options = apply_filters( 'ebox_question_quiz_post_options', $query_options, $_settings );

							$query_posts = new WP_Query( $query_options );

							// translators: placeholder: Quiz.
							$post_array = array( '0' => sprintf( esc_html_x( '-- Select a %s --', 'placeholder: Quiz', 'ebox' ), ebox_Custom_Label::get_label( 'Quiz' ) ) );

							if ( ! empty( $query_posts->posts ) ) {
								if ( count( $query_posts->posts ) >= $query_posts->found_posts ) {
									// If the number of returned posts is equal or greater then found_posts then no need to run lazy load.
									$_settings['lazy_load'] = false;
								}

								foreach ( $query_posts->posts as $p ) {
									if ( get_the_id() !== $p->ID ) {
										$post_array[ $p->ID ] = $p->post_title;
									}
								}
							} else {
								// If we don't have any items then override the lazy load flag.
								$_settings['lazy_load'] = false;
							}
							$settings[ "{$question_prefix}quiz" ]['initial_options'] = $post_array;

							if ( ( isset( $_settings['lazy_load'] ) ) && ( true == $_settings['lazy_load'] ) ) {
								$lazy_load_data               = array();
								$lazy_load_data['query_vars'] = $query_options;
								$lazy_load_data['query_type'] = 'WP_Query';
								$lazy_load_data['value']      = ( isset( $_settings['value'] ) ) ? $_settings['value'] : '';
								$settings[ "{$question_prefix}quiz" ]['lazy_load_data'] = $lazy_load_data;
							}
						}
					}
				}
			}

			return $settings;
		}

		/**
		 * Select a course
		 *
		 * @param string $current_post_type  Current post type.
		 *
		 * @return array
		 */
		public function select_a_course( $current_post_type = null ) {

			$opt = array(
				'post_type'   => 'ebox-courses',
				'post_status' => 'any',
				'numberposts' => -1,
				'orderby'     => 'title',
				'order'       => 'ASC',
			);

			$posts      = get_posts( $opt );
			$post_array = array();

			if ( ! empty( $posts ) ) {
				foreach ( $posts as $p ) {
					$post_array[ $p->ID ] = $p->post_title;
				}
			}

			return $post_array;
		}

		/**
		 * Select a team
		 *
		 * @param string $current_post_type Current post type.
		 *
		 * @return array
		 */
		public function select_a_team( $current_post_type = null ) {

			$opt = array(
				'post_type'   => 'teams',
				'post_status' => 'any',
				'numberposts' => -1,
				'orderby'     => 'title',
				'order'       => 'ASC',
			);

			$posts      = get_posts( $opt );
			$post_array = array();

			if ( ! empty( $posts ) ) {
				foreach ( $posts as $p ) {
					$post_array[ $p->ID ] = $p->post_title;
				}
			}

			return $post_array;
		}

		/**
		 * Select a certificate
		 *
		 * @param string $current_post_type Current post type.
		 *
		 * @return array
		 */
		public function select_a_certificate( $current_post_type = null ) {

			$opt = array(
				'post_type'   => 'ebox-certificates',
				'post_status' => 'any',
				'numberposts' => -1,
				'orderby'     => 'title',
				'order'       => 'ASC',
			);

			$posts      = get_posts( $opt );
			$post_array = array();

			if ( ! empty( $posts ) ) {
				foreach ( $posts as $p ) {
					$post_array[ $p->ID ] = $p->post_title;
				}
			}

			return $post_array;
		}


		/**
		 * Retrieves modules or topics for a course to populate dropdown on edit screen
		 *
		 * Ajax action callback for wp_ajax_select_a_lesson_or_topic
		 *
		 * @since 2.1.0
		 */
		public function select_a_lesson_or_topic_ajax() {
			$data        = array();
			$data['opt'] = array();

			if ( ( isset( $_POST['ld_selector_nonce'] ) ) && ( ! empty( $_POST['ld_selector_nonce'] ) ) && ( wp_verify_nonce( $_POST['ld_selector_nonce'], ebox_get_post_type_slug( 'lesson' ) ) ) ) {

				if ( ( isset( $_POST['ld_selector_default'] ) ) && ( ! empty( $_POST['ld_selector_default'] ) ) ) {
					$ld_selector_default = true;
				} else {
					$ld_selector_default = false;
				}
				$post_array = $this->select_a_lesson_or_topic(
					isset( $_REQUEST['course_id'] ) ? intval( $_REQUEST['course_id'] ) : null,
					true,
					$ld_selector_default
				);
				if ( ! empty( $post_array ) ) {
					$i = 0;
					foreach ( $post_array as $key => $value ) {
						$opt[ $i ]['key']   = $key;
						$opt[ $i ]['value'] = $value;
						$i++;
					}
					$data['opt'] = $opt;
				}
			}

			echo wp_json_encode( $data );
			exit;
		}



		/**
		 * Makes wp_query to retrieve modules or topics for a course
		 *
		 * @since 2.1.0
		 *
		 * @param int  $course_id       Course ID.
		 * @param bool $include_topics  Whether to include topics.
		 * @param bool $include_default Whether to include default.
		 *
		 * @return array    array of modules or topics
		 */
		public function select_a_lesson_or_topic( $course_id = null, $include_topics = true, $include_default = true ) {
			if ( ! is_admin() ) {
				return array();
			}
			$post_array = array();

			if ( ! is_null( $course_id ) ) {
				if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Courses_Builder', 'shared_steps' ) == 'yes' ) {
					$lesson_ids = ebox_course_get_children_of_step( $course_id, $course_id, 'ebox-modules' );
					if ( ! empty( $lesson_ids ) ) {
						foreach ( $lesson_ids as $lesson_id ) {
							$post_array[ $lesson_id ] = get_the_title( $lesson_id );
							if ( $include_topics ) {
								$topic_ids = ebox_course_get_children_of_step( $course_id, $lesson_id, 'ebox-topic' );
								if ( ! empty( $topic_ids ) ) {
									foreach ( $topic_ids as $topic_id ) {
										$post_array[ $topic_id ] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . get_the_title( $topic_id );
									}
								}
							}
						}
					}
				} else {
					$modules_options     = ebox_lms_get_post_options( 'ebox-modules' );
					$course_modules_args = ebox_get_course_modules_order( $course_id );
					$orderby             = isset( $course_modules_args['orderby'] ) ? $course_modules_args['orderby'] : $modules_options['orderby'];
					$order               = isset( $course_modules_args['order'] ) ? $course_modules_args['order'] : $modules_options['order'];
					$opt                 = array(
						'post_type'   => 'ebox-modules',
						'post_status' => 'any',
						'numberposts' => -1,
						'orderby'     => $orderby,
						'order'       => $order,
					);

					if ( empty( $course_id ) && isset( $_GET['post'] ) ) {
						$course_id = ebox_get_course_id( $_GET['post'] );
					}

					if ( ! empty( $course_id ) ) {
						$opt['meta_key']   = 'course_id';
						$opt['meta_value'] = $course_id;
					}

					$posts = get_posts( $opt );

					if ( true === $include_default ) {
						if ( true == $include_topics ) {
							if ( ebox_use_select2_lib() ) {
								$post_array = array(
									'-1' => sprintf(
										// translators: placeholders: Lesson, Topic.
										esc_html_x( 'Search or select a %1$s or %2$s…', 'placeholders: Lesson, Topic', 'ebox' ),
										ebox_Custom_Label::get_label( 'lesson' ),
										ebox_Custom_Label::get_label( 'topic' )
									),
								);
							} else {
								$post_array = array(
									'0' => sprintf(
										// translators: placeholders: Lesson, Topic Labels.
										esc_html_x( 'Select a %1$s or %2$s', 'placeholders: Lesson, Topic Labels', 'ebox' ),
										ebox_Custom_Label::get_label( 'lesson' ),
										ebox_Custom_Label::get_label( 'topic' )
									),
								);
							}
						} else {
							if ( ebox_use_select2_lib() ) {
								$post_array = array(
									'-1' => sprintf(
										// translators: placeholder: Lesson.
										esc_html_x( 'Search or select a %s…', 'placeholder: Lesson', 'ebox' ),
										ebox_Custom_Label::get_label( 'lesson' )
									),
								);
							} else {
								$post_array = array(
									'0' => sprintf(
										// translators: placeholder: Lesson.
										esc_html_x( 'Select a %s', 'placeholder: Lesson', 'ebox' ),
										ebox_Custom_Label::get_label( 'lesson' )
									),
								);
							}
						}
					}

					if ( ! empty( $posts ) ) {
						foreach ( $posts as $p ) {
							$lesson_post_title = ebox_format_step_post_title_with_status_label( $p );
							if ( empty( $lesson_post_title ) ) {
								$lesson_post_title = $p->ID . ' - /' . $p->post_name;
							}
							$post_array[ $p->ID ] = $lesson_post_title;
							if ( true == $include_topics ) {
								$topics_array = ebox_get_topic_list( $p->ID, $course_id );
								if ( ! empty( $topics_array ) ) {
									foreach ( $topics_array as $topic ) {
										$topic_post_title = ebox_format_step_post_title_with_status_label( $topic );
										if ( empty( $topic_post_title ) ) {
											$topic_post_title = $topic->ID . ' - /' . $topic->post_name;
										}
										$post_array[ $topic->ID ] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $topic_post_title;
									}
								}
							}
						}
					}
				}
			}
			return $post_array;
		}


		/**
		 * Retrieves modules for a course to populate dropdown on edit screen
		 *
		 * Ajax action callback for wp_ajax_select_a_lesson
		 *
		 * @since 2.1.0
		 */
		public function select_a_lesson_ajax() {
			$data        = array();
			$data['opt'] = array();

			if ( ( isset( $_POST['ld_selector_nonce'] ) ) && ( ! empty( $_POST['ld_selector_nonce'] ) ) && ( wp_verify_nonce( $_POST['ld_selector_nonce'], 'ebox-modules' ) ) ) {
				if ( ( isset( $_POST['ld_selector_default'] ) ) && ( ! empty( $_POST['ld_selector_default'] ) ) ) {
					$ld_selector_default = true;
				} else {
					$ld_selector_default = false;
				}
				$post_array = $this->select_a_lesson_or_topic(
					isset( $_REQUEST['course_id'] ) ? intval( $_REQUEST['course_id'] ) : null,
					false,
					$ld_selector_default
				);
				if ( ! empty( $post_array ) ) {
					$i = 0;
					foreach ( $post_array as $key => $value ) {
						$opt[ $i ]['key']   = $key;
						$opt[ $i ]['value'] = $value;
						$i++;
					}
					$data['opt'] = $opt;
				}
			}

			echo wp_json_encode( $data );
			exit;
		}



		/**
		 * Makes wp_query to retrieve modules a course
		 *
		 * @since 2.1.0
		 *
		 * @param  int $course_id Course ID.
		 * @return array    array of modules
		 */
		public function select_a_lesson( $course_id = null ) {
			if ( ! is_admin() ) {
				return array();
			}

			if ( ! empty( $_REQUEST['ld_action'] ) || ! empty( $_GET['post'] ) && is_array( $_GET['post'] ) ) {
				return array();
			}

			$opt = array(
				'post_type'   => 'ebox-modules',
				'post_status' => 'any',
				'numberposts' => -1,
				'orderby'     => ebox_get_option( 'ebox-modules', 'orderby' ),
				'order'       => ebox_get_option( 'ebox-modules', 'order' ),
			);

			if ( empty( $course_id ) ) {
				if ( empty( $_GET['post'] ) ) {
					$course_id = ebox_get_course_id();
				} else {
					$course_id = ebox_get_course_id( $_GET['post'] );
				}
			}

			if ( ! empty( $course_id ) ) {
				$opt['meta_key']   = 'course_id';
				$opt['meta_value'] = $course_id;
			}

			$posts = get_posts( $opt );
			if ( ebox_use_select2_lib() ) {
				$post_array = array(
					'-1' => sprintf(
						// translators: placeholder: Lesson.
						esc_html_x( 'Search or select a %s', 'placeholder: Lesson', 'ebox' ),
						ebox_Custom_Label::get_label( 'lesson' )
					),
				);
			} else {
				$post_array = array(
					'0' => sprintf(
						// translators: placeholder: Lesson.
						esc_html_x( 'Select a %s', 'placeholder: Lesson', 'ebox' ),
						ebox_Custom_Label::get_label( 'lesson' )
					),
				);
			}

			if ( ! empty( $posts ) ) {
				foreach ( $posts as $p ) {
					$post_array[ $p->ID ] = $p->post_title;
				}
			}

			return $post_array;
		}


		/**
		 * Retrieves quizzes for a course to populate dropdown on edit screen
		 *
		 * Ajax action callback for wp_ajax_select_a_lesson
		 *
		 * @since 2.5.0
		 */
		public function select_a_quiz_ajax() {
			$data        = array();
			$data['opt'] = array();

			if ( ( isset( $_POST['ld_selector_nonce'] ) ) && ( ! empty( $_POST['ld_selector_nonce'] ) ) && ( wp_verify_nonce( $_POST['ld_selector_nonce'], 'ebox-quiz' ) ) ) {
				$post_array = $this->select_a_quiz(
					isset( $_REQUEST['course_id'] ) ? intval( $_REQUEST['course_id'] ) : 0,
					isset( $_REQUEST['lesson_id'] ) ? intval( $_REQUEST['lesson_id'] ) : 0
				);
				if ( ! empty( $post_array ) ) {
					$i = 0;
					foreach ( $post_array as $key => $value ) {
						$opt[ $i ]['key']   = $key;
						$opt[ $i ]['value'] = $value;
						$i++;
					}
					$data['opt'] = $opt;
				}
			}
			echo wp_json_encode( $data );
			exit;
		}

		/**
		 * Makes wp_query to retrieve quizzes a course
		 *
		 * @since 2.5.0
		 *
		 * @param  int $course_id       Course ID.
		 * @param  int $lesson_topic_id Step ID.
		 * @return array    array of modules
		 */
		public function select_a_quiz( $course_id = 0, $lesson_topic_id = 0 ) {

			$post_array = array();

			if ( ! empty( $course_id ) ) {
				if ( ! empty( $lesson_topic_id ) ) {
					$quiz_ids = ebox_course_get_children_of_step( $course_id, $lesson_topic_id, 'ebox-quiz' );
				} else {
					$quiz_ids = ebox_course_get_steps_by_type( $course_id, 'ebox-quiz' );
				}
				if ( ! empty( $quiz_ids ) ) {
					foreach ( $quiz_ids as $quiz_id ) {
						$post_array[ $quiz_id ] = get_the_title( $quiz_id );
					}
				}
			} else {
				$opt = array(
					'post_type'   => 'ebox-quiz',
					'post_status' => 'any',
					'numberposts' => -1,
					'orderby'     => 'title',
					'order'       => 'ASC',
				);

				$posts      = get_posts( $opt );
				$post_array = array();

				if ( ! empty( $posts ) ) {
					foreach ( $posts as $p ) {
						$post_array[ $p->ID ] = $p->post_title;
					}
				}
			}
			return $post_array;
		}


		/**
		 * Set up course display settings
		 *
		 * Filter callback for '{$courses_prefix}display_settings'
		 * apply_filters in display_options() in swfd_module_class.php
		 *
		 * @since 2.1.0
		 * @deprecated 3.4.0
		 *
		 * @param  array $settings  quiz settings.
		 *
		 * @return array quiz settings
		 */
		public function course_display_settings( $settings ) {
			if ( function_exists( '_deprecated_function' ) ) {
				_deprecated_function( __FUNCTION__, '3.4.0' );
			}

			return $settings;

		}

		/**
		 * Set up lesson display settings
		 *
		 * Filter callback for '{$modules_prefix}display_settings'
		 * apply_filters in display_options() in swfd_module_class.php
		 *
		 * @since 2.2.0.2
		 * @deprecated 3.4.0
		 *
		 * @param  array $settings        lesson settings.
		 * @return array                   lesson settings
		 */
		public function lesson_display_settings( $settings ) {

			if ( function_exists( '_deprecated_function' ) ) {
				_deprecated_function( __FUNCTION__, '3.4.0' );
			}

			return $settings;
		}


		/**
		 * Set up topic display settings
		 *
		 * Filter callback for '{$topics_prefix}display_settings'
		 * apply_filters in display_options() in swfd_module_class.php
		 *
		 * @since 2.2.0.2
		 * @deprecated 3.4.0
		 *
		 * @param  array $settings        topic settings.
		 * @return array                   topic settings
		 */
		public function topic_display_settings( $settings ) {
			if ( function_exists( '_deprecated_function' ) ) {
				_deprecated_function( __FUNCTION__, '3.4.0' );
			}

			return $settings;
		}

		/**
		 * Insert course name as a term on course publish
		 *
		 * Action callback for 'publish_ebox-courses' (wp core filter action)
		 *
		 * @todo  consider for deprecation, action is commented
		 *
		 * @since 2.1.0
		 *
		 * @param int    $post_id Post ID.
		 * @param object $post    Post object.
		 */
		public function add_course_tax_entry( $post_id, $post ) {
			$term    = get_term_by( 'slug', $post->post_name, 'courses' );
			$term_id = isset( $term->term_id ) ? $term->term_id : 0;

			if ( ! $term_id ) {
				$term    = wp_insert_term( $post->post_title, 'courses', array( 'slug' => $post->post_name ) );
				$term_id = $term['term_id'];
			}

			wp_set_object_terms( (int) $post_id, (int) $term_id, 'courses', true );
		}



		/**
		 * Register taxonomies for each custom post type
		 *
		 * Action callback for 'init'
		 *
		 * @since 2.1.0
		 */
		public function tax_registration() {

			/**
			 * Filters list of taxonomies to be registered.
			 *
			 * Add_filters are currently added during the add_post_type() method in swfd_cpt.php
			 *
			 * @since 2.1.0
			 *
			 * @param array $taxonomies An array of taxonomy lists to be registered.
			 */
			$taxes = apply_filters( 'ebox_cpt_register_tax', array() );

			/**
			 * The expected return form of the array is:
			 *  array(
			 *      'tax_slug1' =>  array(
			 *                          'post_types' => array('ebox-courses', 'ebox-modules'),
			 *                          'tax_args' => array() // See register_taxonomy() third parameter for valid args options
			 *                      ),
			 *      'tax_slug2' =>  array(
			 *                          'post_types' => array('ebox-modules'),
			 *                          'tax_args' => array()
			 *                      ),
			 *  )
			 */

			if ( ! empty( $taxes ) ) {
				foreach ( $taxes as $tax_slug => $tax_options ) {
					if ( ! taxonomy_exists( $tax_slug ) ) {
						if ( ( isset( $tax_options['post_types'] ) ) && ( ! empty( $tax_options['post_types'] ) ) ) {
							if ( ( isset( $tax_options['tax_args'] ) ) && ( ! empty( $tax_options['tax_args'] ) ) ) {

								// Via the LD post type setup when the 'taxonomies' option is defined we can associate other taxonomies
								// with our custom post types by setting the tax slug and value as the same.
								if ( $tax_slug !== $tax_options['tax_args']['rewrite']['slug'] ) {
									/**
									 * Filters taxonomy arguments.
									 *
									 * @param array $tax_options An array of taxonomy arguments.
									 * @param string $tax_slug Taxonomy slug.
									 */
									$tax_options = apply_filters( 'ebox_taxonomy_args', $tax_options, $tax_slug );
									if ( ! empty( $tax_options ) ) {
										register_taxonomy( $tax_slug, $tax_options['post_types'], $tax_options['tax_args'] );
									}
								}
							}
						}
					} else {

						// If the taxonomy already exists we only need to then associated the post_types.
						if ( ( isset( $tax_options['post_types'] ) ) && ( ! empty( $tax_options['post_types'] ) ) ) {
							foreach ( $tax_options['post_types'] as $post_type ) {
								register_taxonomy_for_object_type( $tax_slug, $post_type );
							}
						}
					}
				}
			} // endif
		}

		/**
		 * Get template paths
		 *
		 * @param string $filename  File name.
		 */
		public static function get_template_paths( $filename = '' ) {
			$template_filenames = array();
			$template_paths     = array();

			$active_template_key = ebox_Theme_Register::get_active_theme_key();
			$file_pathinfo       = pathinfo( $filename );

			if ( ! isset( $file_pathinfo['dirname'] ) ) {
				$file_pathinfo['dirname'] = '';
			} elseif ( ! empty( $file_pathinfo['dirname'] ) ) {
				if ( '.' === $file_pathinfo['dirname'] ) {
					$file_pathinfo['dirname'] = '';
				} else {
					$file_pathinfo['dirname'] .= '/';
				}
			}

			if ( empty( $file_pathinfo['filename'] ) || ( ! is_string( $file_pathinfo['filename'] ) ) ) {
				$file_pathinfo['filename'] = '';
			}

			if ( ! isset( $file_pathinfo['extension'] ) ) {
				$file_pathinfo['extension'] = '';
			}

			if ( in_array( $file_pathinfo['extension'], array( 'js', 'css' ), true ) ) {
				if ( ( defined( 'ebox_SCRIPT_DEBUG' ) ) && ( ebox_SCRIPT_DEBUG == true ) ) {
					$template_filenames[] = $file_pathinfo['dirname'] . $file_pathinfo['filename'] . '.' . $file_pathinfo['extension'];
				}

				$template_filenames[] = $file_pathinfo['dirname'] . $file_pathinfo['filename'] . '.min.' . $file_pathinfo['extension'];
			} else {
				$template_filenames[] = $file_pathinfo['dirname'] . $file_pathinfo['filename'] . '.' . $file_pathinfo['extension'];
			}

			$template_paths['theme'] = array();
			foreach ( $template_filenames as $template_filename ) {
				$template_paths['theme'][] = 'ebox/' . $active_template_key . '/' . $template_filename;
			}

			if ( 'legacy' === $active_template_key ) {
				foreach ( $template_filenames as $template_filename ) {
					$template_paths['theme'][] = 'ebox/' . $template_filename;
				}

				foreach ( $template_filenames as $template_filename ) {
					$template_paths['theme'][] = $template_filename;
				}
			}

			$template_paths['templates'] = array();
			if ( defined( 'ebox_TEMPLATES_DIR' ) ) {
				$template_dir = trailingslashit( ebox_TEMPLATES_DIR );
				foreach ( $template_filenames as $template_filename ) {
					$template_paths['templates'][] = $template_dir . $active_template_key . '/' . $template_filename;
				}
				if ( 'ebox_template_functions.php' === $file_pathinfo['filename'] ) {
					$template_paths['templates'][] = $template_dir . $active_template_key . '/functions.php';
				}
				if ( 'legacy' === $active_template_key ) {
					foreach ( $template_filenames as $template_filename ) {
						$template_paths['templates'][] = $template_dir . $template_filename;
					}
					if ( 'ebox_template_functions.php' === $file_pathinfo['filename'] ) {
						$template_paths['templates'][] = $template_dir . 'functions.php';
					}
				}
			}

			$active_template_dir = ebox_Theme_Register::get_active_theme_template_dir();
			if ( ! empty( $active_template_dir ) ) {
				foreach ( $template_filenames as $template_filename ) {
					$template_paths['templates'][] = $active_template_dir . '/' . $template_filename;
				}
			}

			if ( ebox_LEGACY_THEME !== $active_template_key ) {
				$legacy_theme_instance = ebox_Theme_Register::get_theme_instance( ebox_LEGACY_THEME );
				$legacy_theme_dir      = $legacy_theme_instance->get_theme_template_dir();
				if ( ! empty( $legacy_theme_dir ) ) {
					foreach ( $template_filenames as $template_filename ) {
						$template_paths['templates'][] = $legacy_theme_dir . '/' . $template_filename;
					}
				}
			}
			return $template_paths;
		}

		/**
		 * Get ebox template and pass data to be used in template
		 *
		 * Checks to see if user has a 'ebox' directory in their current theme
		 * and uses the template if it exists.
		 *
		 * @since 2.1.0
		 *
		 * @param  string       $name             template name.
		 * @param  array|null   $args             data for template.
		 * @param  boolean|null $echo             echo or return.
		 * @param  boolean      $return_file_path return just file path instead of output.
		 */
		public static function get_template( $name, $args, $echo = false, $return_file_path = false ) {
			$template_paths = array();

			$template_filename = $name;

			// Ensure the template has a proper extension.
			$file_pathinfo = pathinfo( $template_filename );
			if ( ( ! isset( $file_pathinfo['extension'] ) ) || ( empty( $file_pathinfo['extension'] ) ) ) {
				$template_filename .= '.php';
			}

			/**
			 * Filters template file name.
			 *
			 * @since 3.0.0
			 *
			 * @param string  $template_filename Template file name.
			 * @param string  $name             Template name.
			 * @param array   $args             Template data.
			 * @param boolean $echo             Whether to echo the template output or not.
			 * @param boolean $return_file_path  Whether to return file or path or not.
			 */
			$template_filename = apply_filters( 'ebox_template_filename', $template_filename, $name, $args, $echo, $return_file_path );

			if ( empty( $template_filename ) ) {
				return;
			}

			$template_paths = self::get_template_paths( $template_filename );

			$filepath = '';
			if ( ( isset( $template_paths['theme'] ) ) && ( ! empty( $template_paths['theme'] ) ) ) {
				$filepath = locate_template( $template_paths['theme'] );
			}

			if ( empty( $filepath ) ) {
				if ( ( isset( $template_paths['templates'] ) ) && ( ! empty( $template_paths['templates'] ) ) ) {
					foreach ( $template_paths['templates'] as $template ) {
						if ( file_exists( $template ) ) {
							$filepath = $template;
							break;
						}
					}
				}
			}

			/**
			 * Filters file path for the ebox template being called.
			 *
			 * @since 2.1.0
			 * @since 3.0.3 - Allow override of empty or other checks.
			 *
			 * @param string  $filepath         Template file path.
			 * @param string  $name             Template name.
			 * @param array   $args             Template data.
			 * @param boolean $echo             Whether to echo the template output or not.
			 * @param boolean $return_file_path Whether to return file or path or not.
			 */
			$filepath = apply_filters( 'ebox_template', $filepath, $name, $args, $echo, $return_file_path );
			if ( ! $filepath ) {
				return false;
			}

			if ( $return_file_path ) {
				return $filepath;
			}

			// Added check to ensure external hooks don't return empty or non-accessible filenames.
			if ( ( file_exists( $filepath ) ) && ( is_file( $filepath ) ) ) {

				/**
				 * Filters template arguments.
				 *
				 * The dynamic part of the hook refers to the name of the template.
				 *
				 * @param array   $args             Template data.
				 * @param string  $filepath          Template file path.
				 * @param boolean $echo             Whether to echo the template output or not.
				 */
				$args = apply_filters( 'ld_template_args_' . $name, $args, $filepath, $echo );
				if ( ( ! empty( $args ) ) && ( is_array( $args ) ) ) {
					extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract -- Bad idea, but better keep it for now.
				}
				$level = ob_get_level();
				ob_start();
				include $filepath;
				$contents = ebox_ob_get_clean( $level );

				if ( ! $echo ) {
					return $contents;
				}

				echo $contents;
			}
		}

		/**
		 * Get or output view template file.
		 *
		 * @since 4.4.0
		 *
		 * @param string $name View template name.
		 * @param array  $args Template arguments.
		 * @param bool   $echo Whether to output or return the template.
		 *
		 * @return void|string
		 */
		public static function get_view( string $name, array $args = array(), bool $echo = false ) {
			extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract -- Bad idea, but better keep it for now.
			$template = ebox_LMS_PLUGIN_DIR . '/includes/views/' . $name . '.php';

			if ( file_exists( $template ) ) {
				$level = ob_get_level();
				ob_start();
				include $template;
				$contents = ebox_ob_get_clean( $level );

				if ( ! $echo ) {
					return $contents;
				}

				echo $contents; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}

		/**
		 * Called from the 'all_plugins' filter. This is called from the Plugins listing screen and will let us
		 * set our internal flag 'all_plugins_called' so we know when (and when not) to add the ebox plugin path
		 *
		 * @since 2.3.0.3
		 *
		 * @param array $all_plugins The array of plugins to be displayed on the Plugins listing.
		 * @return array $all_plugins
		 */
		public function all_plugins_proc( $all_plugins ) {
			$this->all_plugins_called = true;
			return $all_plugins;
		}

		/**
		 * Called from the 'pre_current_active_plugins' action. This is called after the Plugins listing checks for
		 * valid plugins. The will let us unset our internal flag 'ALL_PLUGINS_CALLED'.
		 *
		 * @since 2.3.0.3
		 */
		public function pre_current_active_plugins_proc() {
			$this->all_plugins_called = false;
		}

		/**
		 * This is called from the get_options() function for the option 'active_plugins'. Using this filter
		 * we can append our ebox plugin path, allowing other plugins to check via is_plugin_active()
		 * even if ebox is installed in a non-standard plugin directory.
		 *
		 * @since 2.3.0.3
		 *
		 * @param array $active_plugins An array of the current active plugins.
		 * @return array $active_plugins
		 */
		public function option_active_plugins_proc( $active_plugins ) {
			global $pagenow;

			if ( empty( $active_plugins ) ) {
				return $active_plugins;
			}

			// we don't need to add the plugin path for that call.
			if ( 'plugins.php' === $pagenow && $this->all_plugins_called ) {
				return $active_plugins;
			}

			// the current plugin is not active.
			if ( ! in_array( ebox_LMS_PLUGIN_KEY, $active_plugins, true ) ) {
				return $active_plugins;
			}

			// plugin is in the standard location.
			if ( ebox_LMS_PLUGIN_KEY === $this->ebox_standard_plugin_path ) {
				return $active_plugins;
			}

			if ( ! in_array( $this->ebox_standard_plugin_path, $active_plugins, true ) ) {
				$active_plugins[] = $this->ebox_standard_plugin_path;
			}

			return $active_plugins;
		}

		/**
		 * This is called from the update_options() function for the option 'active_plugins'. Using this filter
		 * we can remove our plugin path we added via the option_active_plugins_proc filter.
		 *
		 * @since 2.3.0.3
		 *
		 * @param array $active_plugins An array of the current active plugins.
		 * @return array $active_plugins
		 */
		public function pre_update_option_active_plugins( $active_plugins ) {
			if ( empty( $active_plugins ) ) {
				return $active_plugins;
			}

			// plugin is in the standard location.
			if ( ebox_LMS_PLUGIN_KEY === $this->ebox_standard_plugin_path ) {
				return $active_plugins;
			}

			$key = array_search( $this->ebox_standard_plugin_path, $active_plugins );
			if ( $key !== false ) {
				unset( $active_plugins[ $key ] );
			}

			return $active_plugins;
		}

		/**
		 * Site option active sitewide plugins
		 *
		 * @param array $active_plugins Array of active plugins.
		 *
		 * @return array
		 */
		public function site_option_active_sitewide_plugins_proc( $active_plugins ) {
			global $pagenow;

			if ( empty( $active_plugins ) ) {
				return $active_plugins;
			}

			// we don't need to add the plugin path for that call.
			if ( 'plugins.php' === $pagenow && $this->all_plugins_called ) {
				return $active_plugins;
			}

			// the current plugin is not active.
			if ( ! isset( $active_plugins[ ebox_LMS_PLUGIN_KEY ] ) ) {
				return $active_plugins;
			}

			// plugin is in the standard location.
			if ( ebox_LMS_PLUGIN_KEY === $this->ebox_standard_plugin_path ) {
				return $active_plugins;
			}

			if ( ! isset( $active_plugins[ $this->ebox_standard_plugin_path ] ) ) {
				$active_plugins[ $this->ebox_standard_plugin_path ] = $active_plugins[ ebox_LMS_PLUGIN_KEY ];
			}

			return $active_plugins;
		}

		/**
		 * Pre Update site option active sitewide plugins
		 *
		 * @param array $active_plugins Active plugins.
		 *
		 * @return array
		 */
		public function pre_update_site_option_active_sitewide_plugins( $active_plugins ) {
			if ( empty( $active_plugins ) ) {
				return $active_plugins;
			}

			// plugin is in the standard location.
			if ( ebox_LMS_PLUGIN_KEY === $this->ebox_standard_plugin_path ) {
				return $active_plugins;
			}

			if ( isset( $active_plugins[ $this->ebox_standard_plugin_path ] ) ) {
				unset( $active_plugins[ $this->ebox_standard_plugin_path ] );
			}

			return $active_plugins;
		}


		/**
		 * Add support for alternate templates directory.
		 * Normally LD will load template files from the active theme directory
		 * or if not found via the plugin templates directory. We now support
		 * a neutral directory wp-content/uploads/ebox/templates/
		 *
		 * If the site uses a functions.php it will be loaded from that directory
		 * This is the recommended place to add actions/filters to prevent theme updates
		 * from erasing them.
		 *
		 * @since 2.4.0
		 */
		public function init_ld_templates_dir() {
			if ( ! defined( 'ebox_TEMPLATES_DIR' ) ) {
				$wp_upload_dir    = wp_upload_dir();
				$ld_templates_dir = trailingslashit( $wp_upload_dir['basedir'] ) . 'ebox/templates/';

				/**
				 * Define ebox LMS - Set the Template override path.
				 *
				 * Will be set within the wp-content/uploads/ebox directory.
				 *
				 * @since 2.4.0
				 */
				define( 'ebox_TEMPLATES_DIR', $ld_templates_dir );

				if ( ! file_exists( $ld_templates_dir ) ) {
					if ( wp_mkdir_p( $ld_templates_dir ) !== false ) {
						// To prevent security browsing add an index.php file.
						file_put_contents( trailingslashit( $ld_templates_dir ) . 'index.php', '// nothing to see here' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents
					}
				}
			}

			// Piggy back to this logic and cleanup the reports directory.
			if ( ( is_admin() ) && ( ( ! defined( 'DOING_AJAX' ) ) || ( DOING_AJAX !== true ) ) ) {

				$wp_upload_dir  = wp_upload_dir();
				$ld_reports_dir = trailingslashit( $wp_upload_dir['basedir'] ) . 'ebox/';

				if ( file_exists( $ld_reports_dir ) ) {
					$filenames = array();

					$filenames_csv = glob( $ld_reports_dir . '*.csv' );
					if ( ( is_array( $filenames_csv ) ) && ( ! empty( $filenames_csv ) ) ) {
						$filenames = array_merge( $filenames, $filenames_csv );
					}

					$filenames_csv = glob( $ld_reports_dir . '/reports/*.csv' );
					if ( ( is_array( $filenames_csv ) ) && ( ! empty( $filenames_csv ) ) ) {
						$filenames = array_merge( $filenames, $filenames_csv );
					}

					if ( ! empty( $filenames ) ) {
						foreach ( $filenames as $filename ) {
							if ( filemtime( $filename ) < ( time() - 60 * 60 ) ) {
								$file = basename( $filename );

								if ( substr( $file, 0, strlen( 'ebox_reports_user_courses_' ) ) == 'ebox_reports_user_courses_' ) {
									$transient_hash = str_replace( array( 'ebox_reports_user_courses_', '.csv' ), '', $file );

									$options_key = 'ebox_reports_user_courses_' . $transient_hash;
									delete_option( $options_key );

									$options_key = '_transient_user-courses_' . $transient_hash;
									delete_option( $options_key );

									$options_key = '_transient_timeout_user-courses_' . $transient_hash;
									delete_option( $options_key );

									@unlink( $filename ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- Let it be.

								} elseif ( substr( $file, 0, strlen( 'ebox_reports_user_quizzes' ) ) == 'ebox_reports_user_quizzes' ) {
									$transient_hash = str_replace( array( 'ebox_reports_user_quizzes', '.csv' ), '', $file );

									$options_key = 'ebox_reports_user_quizzes_' . $transient_hash;
									delete_option( $options_key );

									$options_key = '_transient_user-quizzes_' . $transient_hash;
									delete_option( $options_key );

									$options_key = '_transient_timeout_user-quizzes_' . $transient_hash;
									delete_option( $options_key );

									@unlink( $filename ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- Let it be.
								}
							}
						}
					}
				}
			}
		}

		/**
		 * Course category row actions
		 *
		 * If on the Course, modules, Topics section we display the
		 * WP Post Categories or Post Tags. We want to hide the row action 'view' links.
		 *
		 * @param array  $actions Actions.
		 * @param string $tag     Tag.
		 */
		public function ld_course_category_row_actions( $actions, $tag ) {
			global $ebox_post_types;
			global $pagenow, $taxnow;

			if ( ( 'edit-tags.php' === $pagenow ) && ( ( 'category' == $taxnow ) || ( 'post_tag' == $taxnow ) ) ) {
				if ( in_array( get_current_screen()->post_type, $ebox_post_types, true ) !== false ) {
					if ( isset( $actions['view'] ) ) {
						$current_href_old = get_term_link( $tag );
						$current_href_new = add_query_arg( 'post_type', get_current_screen()->post_type, $current_href_old );
						$actions['view']  = str_replace( $current_href_old, $current_href_new, $actions['view'] );
					}
				}
			}

			return $actions;
		}

		/**
		 * Function to dynamically control the 'the_content' filtering for this post_type instance.
		 * This is needed for example when using the 'the_content' filters manually and do not want the
		 * normal filters recursively applied.
		 *
		 * @since 2.5.9
		 *
		 * @param boolean $filter_check True if the_content filter is to be enabled.
		 * @param array   $post_types Limit change to specific instance post types. default is all.
		 */
		public static function content_filter_control( $filter_check = true, $post_types = array() ) {

			if ( empty( $post_types ) ) {
				$post_types = array_keys( ebox_CPT_Instance::$instances );
			}
			foreach ( ebox_CPT_Instance::$instances as $post_type => $instance ) {
				if ( in_array( $post_type, $post_types, true ) ) {
					$instance->content_filter_control( $filter_check );
				}
			}
		}

		/**
		 * Show admin notice message after 4.3.0.2 hub upgrade.
		 *
		 * @since 4.3.1
		 */
		public function hub_after_upgrade_admin_notice() {
			$current_screen = get_current_screen();
			if ( 'admin_page_ebox_hub_licensing' === $current_screen->base ) {
				return;
			}

			$hub_upgrade_notice = get_option( 'ebox_show_hub_upgrade_admin_notice' );
			if ( ! $hub_upgrade_notice ) {
				return;
			}

			if ( ! ebox_is_ebox_hub_active() ) {
				return;
			}

			?>
			<div class="notice notice-info is-dismissible ebox_hub_upgrade_dismiss" data-notice-dismiss-nonce="<?php echo esc_attr( wp_create_nonce( 'notice-dismiss-nonce-' . get_current_user_id() ) ); ?>">
				<p>
					<?php
					$hub_admin_page = 'admin.php?page=ebox_hub_licensing';
					echo sprintf(
						// translators: Message for hub plugin upgrade from 4.3.0.2 to 4.3.1.
						esc_html__( 'The ebox licensing system has changed locations! You\'ll now find your licenses in the %s section under the ebox settings menu.', 'ebox' ),
						sprintf(
							'<a href="%s">%s</a>',
							esc_url( $hub_admin_page ),
							esc_html__( 'LMS License', 'ebox' )
						)
					);
					?>
				</p>
			</div>
			<?php
		}

		/**
		 * Shows Telemetry modal.
		 *
		 * @since 4.5.0
		 * @since 4.5.1 - Added $current_screen param.
		 *
		 * @param WP_Screen $current_screen Current screen.
		 *
		 * @return void
		 */
		public function add_telemetry_modal( WP_Screen $current_screen ): void {
			if (
				(
					! empty( $current_screen->post_type )
					&& in_array( $current_screen->post_type, ebox_get_post_types(), true )
				)
				|| (
					! empty( $current_screen->parent_file )
					&& 'ebox-lms' === $current_screen->parent_file
				)
				|| (
					is_admin()
					&& isset( $_GET['page'] )
					&& false !== strpos( sanitize_text_field( wp_unslash( $_GET['page'] ) ), 'ebox' )
					&& $_GET['page'] !== 'ebox-setup-wizard'
					&& $_GET['page'] !== 'ebox-design-wizard'
				)
			) {
				add_filter(
					'stellarwp/telemetry/ebox/optin_args', // cspell:disable-line.
					function( $args ) {
						$args['plugin_logo']        = ebox_LMS_PLUGIN_URL . 'assets/images/Arttricks-logo.svg';
						$args['plugin_logo_width']  = 205;
						$args['plugin_logo_height'] = 33;
						$args['plugin_logo_alt']    = 'Arttricks Logo';

						$args['heading'] = esc_html__( 'We hope you love Ebox LMS.', 'ebox' );

						$args['intro'] = sprintf(
							// translators: placeholder: username.
							esc_html__(
								'Hi, %1$s! This is an invitation to help us improve Ebox LMS by Arttricks products by sharing product usage data.',
								// 'Hi, %1$s! This is an invitation to help us improve Ebox LMS by Arttricks products by sharing product usage data with StellarWP. ebox is part of the StellarWP family of brands. If you opt-in we\'ll share some helpful WordPress and StellarWP product info with you from time to time. And if you skip this, that\'s okay! Our products will continue to work.',
								'ebox'
							),
							$args['user_name']
						);

						$args['permissions_url'] = 'https://www.ebox.com/telemetry-tracking/';
						$args['tos_url']         = 'https://www.ebox.com/terms-and-conditions/';

						return $args;
					}
				);

				// cspell:disable-next-line.
				do_action( 'stellarwp/telemetry/ebox/optin' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound,WordPress.NamingConventions.ValidHookName.UseUnderscores
			}
		}
	}
}

global $ebox_lms;
$ebox_lms = new ebox_LMS();
