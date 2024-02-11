<?php
/**
 * ebox Settings Admin Menus and Tabs class.
 *
 * @package ebox\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ebox_Admin_Menus_Tabs' ) ) {

	/**
	 * Class to create the settings section.
	 *
	 */
	class ebox_Admin_Menus_Tabs {

		/**
		 * Holder variable for instances of this class.
		 *
		 * @var object $instance Instance of this class object.
		 */
		private static $instance;

		/**
		 * Admin tab sets
		 *
		 * @var array
		 */
		protected $admin_tab_sets = array();

		/**
		 * Admin Tab Priorities
		 *
		 * @var array
		 */
		public $admin_tab_priorities = array(
			'private'  => 0,
			'high'     => 10,
			'normal'   => 20,
			'taxonomy' => 30,
			'misc'     => 100,
		);

		/**
		 * Public constructor for class
		 *
		 * @since 2.4.0
		 */
		public function __construct() {
			// We first add this hook so we are calling 'admin_menu' early.
			add_action( 'admin_menu', array( $this, 'ebox_admin_menu_early' ), 0 );

			/**
			 * Then within the 'wp_loaded' handler we add another hook into
			 * 'admin_menu' to be in the last-est position where we add all
			 * the misc menu items.
			 */
			add_action( 'wp_loaded', array( $this, 'wp_loaded' ), 1000 );

			add_action( 'in_admin_header', array( $this, 'ebox_admin_tabs' ), 20 );
		}

		/**
		 * Get instance of class
		 *
		 * @since 2.4.0
		 */
		final public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * We hook into the 'wp_loaded' action which comes just before the
		 * 'admin_menu' action. The reason for this we want to add a special
		 * 'admin_menu' and ensure it is the last action taken on the menu.
		 *
		 * @since 2.4.0
		 */
		public function wp_loaded() {

			global $wp_filter;

			/***********************************************************************
			 * Admin_menu
			 ***********************************************************************
			*/
			// Set a default priority.
			$top_priority = 100;
			if ( defined( 'ebox_SUBMENU_SETTINGS_PRIORITY' ) ) {
				$top_priority = intval( ebox_SUBMENU_SETTINGS_PRIORITY );
			}

			/**
			 * Filters ebox settings submenu priority.
			 *
			 * @param int $priority Settings submenu priority.
			 */
			$top_priority = apply_filters( 'ebox_submenu_settings_priority', $top_priority );

			add_action( 'admin_menu', array( $this, 'ebox_admin_menu_last' ), $top_priority );
		}

		/**
		 * Menu Args
		 *
		 * @since 2.4.0
		 *
		 * @param array $menu_args Menu args.
		 */
		public function ebox_menu_args( $menu_args = array() ) {
			if ( ( is_array( $menu_args['admin_tabs'] ) ) && ( ! empty( $menu_args['admin_tabs'] ) ) ) {
				foreach ( $menu_args['admin_tabs'] as &$admin_tab_item ) {

					// Similar to the logic from admin_menu above.
					// We need to convert the 'edit.php?post_type=ebox-courses&page=ebox-lms_ebox_lms.php_post_type_ebox-courses'
					// menu_links to 'admin.php?page=ebox_lms_settings' so all the ebox > Settings tabs connect
					// to that menu instead.
					if ( 'edit.php?post_type=ebox-courses&page=ebox-lms_ebox_lms.php_post_type_ebox-courses' === $admin_tab_item['menu_link'] ) {
						$admin_tab_item['menu_link'] = 'admin.php?page=ebox_lms_settings';
					}
				}
			}

			$menu_args['admin_tabs_on_page']['admin_page_ebox_lms_settings'] = $menu_args['admin_tabs_on_page']['ebox-courses_page_ebox-lms_ebox_lms_post_type_ebox-courses'];

			$menu_args['admin_tabs_on_page']['ebox-courses_page_ebox-lms_ebox_lms_post_type_ebox-courses'] = $menu_args['admin_tabs_on_page']['edit-ebox-courses'];

			return $menu_args;
		}

		/**
		 * Admin menu tabs
		 *
		 *
		 * @param array $menu_args Menu args.
		 */
		public function ebox_admin_menu_tabs( $menu_args = array() ) {
			$menu_item_tabs = array();

			// Now we take the current page id and collect all the tab items. This is the newer
			// form of the tab logic instead of them being global.
			$current_page_id = $menu_args['current_page_id'];
			if ( isset( $menu_args['admin_tabs_on_page'][ $current_page_id ] ) ) {
				$menu_link = '';

				foreach ( $menu_args['admin_tabs_on_page'][ $current_page_id ] as $admin_tabs_on_page_id ) {
					if ( isset( $menu_args['admin_tabs'][ $admin_tabs_on_page_id ] ) ) {
						if ( empty( $menu_link ) ) {
							$menu_link = $menu_args['admin_tabs'][ $admin_tabs_on_page_id ]['menu_link'];
						}

						$menu_item_tabs[ $admin_tabs_on_page_id ] = $menu_args['admin_tabs'][ $admin_tabs_on_page_id ];
					}
				}

				foreach ( $menu_args['admin_tabs'] as $admin_tab_id => $admin_tab ) {
					if ( $admin_tab['menu_link'] == $menu_link ) {
						if ( ! isset( $menu_item_tabs[ $admin_tab_id ] ) ) {
							$menu_item_tabs[ $admin_tab_id ] = $admin_tab;
						}
					}
				}
			}

			return $menu_item_tabs;
		}

		/**
		 * Add admin tab set
		 *
		 *
		 * @param string $menu_slug Menu slug.
		 * @param array  $menu_item Menu item. See WP $submenu global.
		 */
		public function add_admin_tab_set( $menu_slug, $menu_item ) {
			global $ebox_post_types, $ebox_taxonomies;

			$url_parts = wp_parse_url( $menu_slug );
			if ( ( isset( $url_parts['path'] ) ) && ( 'edit.php' === $url_parts['path'] ) && ( isset( $url_parts['query'] ) ) && ( ! empty( $url_parts['query'] ) ) ) {
				$menu_query_args = array();
				parse_str( $url_parts['query'], $menu_query_args );
				if ( ( isset( $menu_query_args['post_type'] ) ) && ( in_array( $menu_query_args['post_type'], $ebox_post_types, true ) ) ) {
					if ( ! isset( $this->admin_tab_sets[ $menu_slug ] ) ) {
						$this->admin_tab_sets[ $menu_slug ] = array();
					}

					foreach ( $menu_item as $menu_item_section ) {
						$url_parts = wp_parse_url( html_entity_decode( $menu_item_section[2] ) );
						if ( ( isset( $url_parts['query'] ) ) && ( ! empty( $url_parts['query'] ) ) ) {
							parse_str( $url_parts['query'], $link_params );
						} else {
							$link_params = array(
								'post_type' => $menu_query_args['post_type'],
								'taxonomy'  => '',
							);
						}

						// Edit - We add in the 1 position.
						if ( substr( $menu_item_section[2], 0, strlen( 'edit.php?' ) ) == 'edit.php?' ) {
							$all_title = $menu_item_section[0];
							if ( ( isset( $link_params['post_type'] ) ) && ( ! empty( $link_params['post_type'] ) ) ) {
								$post_type_object = get_post_type_object( strval( $link_params['post_type'] ) );
								if ( $post_type_object ) {
									$all_title = $post_type_object->labels->all_items;
								}
							}

							$this->admin_tab_sets[ $menu_slug ][1] = array(
								'id'   => 'edit-' . strval( $link_params['post_type'] ),
								'name' => $all_title,
								'cap'  => $menu_item_section[1],
								'link' => $menu_item_section[2],
							);
						} elseif ( 'edit-tags.php?' === substr( $menu_item_section[2], 0, strlen( 'edit-tags.php?' ) ) ) {
							$menu_priority = 50;
							if ( 'ebox-quiz' === $menu_query_args['post_type'] ) {
								$menu_priority = 23;
							} elseif ( ( isset( $link_params['taxonomy'] ) ) && ( ! empty( $link_params['taxonomy'] ) ) ) {
								if ( in_array( $link_params['taxonomy'], $ebox_taxonomies, true ) ) {
									$menu_priority = 40;
								}
							}

							$this->add_admin_tab_item(
								$menu_slug,
								array(
									'id'   => 'edit-' . strval( $link_params['taxonomy'] ),
									'name' => $menu_item_section[0],
									'cap'  => $menu_item_section[1],
									'link' => $menu_item_section[2],
								),
								$menu_priority
							);
						}
					}
				}
			}
		}

		/**
		 * Add admin tab item
		 *
		 *
		 * @param string  $menu_slug     Menu slug.
		 * @param array   $menu_item     Menu item. See WP $submenu global.
		 * @param integer $menu_priority Tab priority.
		 */
		public function add_admin_tab_item( $menu_slug, $menu_item, $menu_priority = 20 ) {

			if ( ! isset( $this->admin_tab_sets[ $menu_slug ] ) ) {
				$this->admin_tab_sets[ $menu_slug ] = array();
			} else {
				ksort( $this->admin_tab_sets[ $menu_slug ] );
			}

			if ( ! isset( $menu_item['cap'] ) ) {
				$menu_item['cap'] = ebox_ADMIN_CAPABILITY_CHECK;
			}

			while ( true ) {
				if ( ! isset( $this->admin_tab_sets[ $menu_slug ][ $menu_priority ] ) ) {
					$this->admin_tab_sets[ $menu_slug ][ $menu_priority ] = $menu_item;
					break;
				}
				$menu_priority++;
			}
		}


		/**
		 * The purpose of this early function is to setup the main 'ebox-lms' menu page. Then
		 * re-position the various custom post type submenu items to be found under it.
		 *
		 */
		public function ebox_admin_menu_early() {
			if ( ! is_admin() ) {
				return;    
			}

			global $submenu, $menu;

			$add_submenu = array();

			if ( current_user_can( 'edit_courses' ) ) {
				if ( isset( $submenu['edit.php?post_type=ebox-courses'] ) ) {
					$add_submenu['ebox-courses'] = array(
						'name'  => ebox_Custom_Label::get_label( 'courses' ),
						'cap'   => 'edit_courses',
						'link'  => 'edit.php?post_type=ebox-courses',
						'class' => 'submenu-ldlms-courses',
					);
					$this->add_admin_tab_set( 'edit.php?post_type=ebox-courses', $submenu['edit.php?post_type=ebox-courses'] );
				}

				if ( isset( $submenu['edit.php?post_type=ebox-modules'] ) ) {
					$add_submenu['ebox-modules'] = array(
						'name'  => ebox_Custom_Label::get_label( 'modules' ),
						'cap'   => 'edit_courses',
						'link'  => 'edit.php?post_type=ebox-modules',
						'class' => 'submenu-ldlms-modules',
					);
					$this->add_admin_tab_set( 'edit.php?post_type=ebox-modules', $submenu['edit.php?post_type=ebox-modules'] );
				}

				if ( isset( $submenu['edit.php?post_type=ebox-topic'] ) ) {
					$add_submenu['ebox-topic'] = array(
						'name'  => ebox_Custom_Label::get_label( 'topics' ),
						'cap'   => 'edit_courses',
						'link'  => 'edit.php?post_type=ebox-topic',
						'class' => 'submenu-ldlms-topics',
					);
					$this->add_admin_tab_set( 'edit.php?post_type=ebox-topic', $submenu['edit.php?post_type=ebox-topic'] );
				}

				if ( isset( $submenu['edit.php?post_type=ebox-quiz'] ) ) {
					$add_submenu['ebox-quiz'] = array(
						'name'  => ebox_Custom_Label::get_label( 'quizzes' ),
						'cap'   => 'edit_courses',
						'link'  => 'edit.php?post_type=ebox-quiz',
						'class' => 'submenu-ldlms-quizzes',
					);
					$this->add_admin_tab_set( 'edit.php?post_type=ebox-quiz', $submenu['edit.php?post_type=ebox-quiz'] );
				}

				if ( ( true === ebox_is_data_upgrade_quiz_questions_updated() ) && ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Quizzes_Builder', 'enabled' ) === 'yes' ) ) {
					if ( isset( $submenu[ 'edit.php?post_type=' . ebox_get_post_type_slug( 'question' ) ] ) ) {
						$add_submenu['ebox-question'] = array(
							'name'  => ebox_Custom_Label::get_label( 'questions' ),
							'cap'   => 'edit_courses',
							'link'  => add_query_arg(
								'post_type',
								ebox_get_post_type_slug( 'question' ),
								'edit.php'
							),
							'class' => 'submenu-ldlms-questions',
						);

						if ( isset( $_GET['quiz_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
							$quiz_id = absint( $_GET['quiz_id'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
							if ( ! empty( $quiz_id ) ) {
								foreach ( $submenu[ 'edit.php?post_type=' . ebox_get_post_type_slug( 'question' ) ] as &$link ) {
									$link[2] = add_query_arg( 'quiz_id', $quiz_id, $link[2] );
								}
							}
						}

						$this->add_admin_tab_set(
							add_query_arg(
								'post_type',
								ebox_get_post_type_slug( 'question' ),
								'edit.php'
							),
							$submenu[ 'edit.php?post_type=' . ebox_get_post_type_slug( 'question' ) ]
						);
					}
				}

				if ( isset( $submenu['edit.php?post_type=ebox-certificates'] ) ) {
					$add_submenu['ebox-certificates'] = array(
						'name'  => esc_html_x( 'Certificates', 'Certificates Menu Label', 'ebox' ),
						'cap'   => 'edit_courses',
						'link'  => 'edit.php?post_type=ebox-certificates',
						'class' => 'submenu-ldlms-certificates',
					);
					$this->add_admin_tab_set( 'edit.php?post_type=ebox-certificates', $submenu['edit.php?post_type=ebox-certificates'] );
				}
			}

			if ( current_user_can( 'edit_teams' ) ) {
				if ( isset( $submenu['edit.php?post_type=teams'] ) ) {
					$add_submenu['teams'] = array(
						'name'  => ebox_Custom_Label::get_label( 'teams' ),
						'cap'   => 'edit_teams',
						'link'  => 'edit.php?post_type=teams',
						'class' => 'submenu-ldlms-teams',
					);
					$this->add_admin_tab_set( 'edit.php?post_type=teams', $submenu['edit.php?post_type=teams'] );
				}
			}

			// Exams.

			$exam_post_type_slug = ebox_get_post_type_slug( 'exam' );
			$exam_post_type_url  = 'edit.php?post_type=' . $exam_post_type_slug;
			if ( isset( $submenu[ $exam_post_type_url ] ) ) {
				$add_submenu[ $exam_post_type_slug ] = array(
					'name'  => ebox_Custom_Label::get_label( 'exams' ),
					'cap'   => 'edit_courses',
					'link'  => $exam_post_type_url,
					'class' => 'submenu-ldlms-exams',
				);
				$this->add_admin_tab_set( $exam_post_type_url, $submenu[ $exam_post_type_url ] );
			}

			// Coupons.

			$coupon_post_type_slug = ebox_get_post_type_slug( LDLMS_Post_Types::COUPON );
			$coupon_post_type_url  = "edit.php?post_type={$coupon_post_type_slug}";

			if ( isset( $submenu[ $coupon_post_type_url ] ) ) {
				$add_submenu[ $coupon_post_type_slug ] = array(
					'name'  => ebox_Custom_Label::get_label( 'coupons' ),
					'cap'   => ebox_ADMIN_CAPABILITY_CHECK,
					'link'  => $coupon_post_type_url,
					'class' => 'submenu-ldlms-coupons',
				);
				$this->add_admin_tab_set( $coupon_post_type_url, $submenu[ $coupon_post_type_url ] );
			}

			// Assignments.

			if ( current_user_can( 'edit_assignments' ) ) {
				if ( isset( $submenu['edit.php?post_type=ebox-assignment'] ) ) {
					$add_submenu['ebox-assignment'] = array(
						'name'  => esc_html_x( 'Assignments', 'Assignments Menu Label', 'ebox' ),
						'cap'   => 'edit_assignments',
						'link'  => 'edit.php?post_type=ebox-assignment',
						'class' => 'submenu-ldlms-assignments',
					);
					$this->add_admin_tab_set( 'edit.php?post_type=ebox-assignment', $submenu['edit.php?post_type=ebox-assignment'] );
				}
			}

			if ( ebox_is_team_leader_user() ) {
				$add_submenu['ebox-essays'] = array(
					'name'  => esc_html_x( 'Submitted Essays', 'Submitted Essays Menu Label', 'ebox' ),
					'cap'   => 'team_leader',
					'link'  => 'edit.php?post_type=ebox-essays',
					'class' => 'submenu-ldlms-essays',
				);
			}

			/**
			 * Filters submenu array before it is registered.
			 *
			 * @since 2.1.0
			 *
			 * @param array $add_submenu An array of submenu items.
			 */
			$add_submenu = apply_filters( 'ebox_submenu', $add_submenu );

			if ( ! empty( $add_submenu ) ) {

				$menu_position = 2;
				if ( defined( 'ebox_MENU_POSITION' ) ) {
					$menu_position = intval( ebox_MENU_POSITION );
				}

				/**
				 * Filters ebox settings submenu menu position.
				 *
				 * @since 2.4.0
				 *
				 * @param int $menu_position Menu position.
				 */
				$menu_position = apply_filters( 'ebox-menu-position', $menu_position ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores

				$menu_icon = '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
									 viewBox="0 0 58 46.6" style="enable-background:new 0 0 58 46.6;" xml:space="preserve">
								<path fill="#fff" style="opacity:.45" d="M51,9.7V2.6C51,1.2,49.8,0,48.4,0H2.6C1.2,0,0,1.2,0,2.6v7.1L51,9.7z M12.8,4.6c0.4-0.4,1.2-0.4,1.6,0
									c0.2,0.2,0.4,0.5,0.4,0.8S14.7,6,14.5,6.2c-0.2,0.2-0.5,0.4-0.8,0.4s-0.6-0.1-0.8-0.4c-0.2-0.2-0.4-0.5-0.4-0.8
									C12.5,5.1,12.7,4.8,12.8,4.6z M8.6,4.6c0.4-0.4,1.2-0.4,1.6,0c0.2,0.2,0.4,0.5,0.4,0.8S10.4,6,10.2,6.2C10,6.4,9.7,6.5,9.4,6.5
									c-0.3,0-0.6-0.1-0.8-0.4C8.3,6,8.2,5.7,8.2,5.4S8.3,4.8,8.6,4.6z M4.3,4.6c0.4-0.4,1.2-0.4,1.6,0c0.2,0.2,0.4,0.5,0.4,0.8
									S6.1,6,5.9,6.2C5.7,6.4,5.4,6.5,5.1,6.5S4.5,6.4,4.3,6.2C4,6,3.9,5.7,3.9,5.4C3.9,5.1,4,4.8,4.3,4.6z"/>
								<path fill="#fff" style="opacity:.45" d="M30,34c0-8.6,7-15.5,15.5-15.5c1.9,0,3.7,0.4,5.4,1v-6.7H0v28.9c0,1.5,1.2,2.6,2.6,2.6H34C31.5,41.7,30,38,30,34z"/>
								<path fill="#fff" style="opacity:.45" d="M45.5,21.5C38.6,21.5,33,27.1,33,34s5.6,12.5,12.5,12.5C52.4,46.6,58,41,58,34S52.4,21.5,45.5,21.5z M52.3,30.7l-7.2,8.8h0
									c-0.3,0.4-0.8,0.6-1.3,0.6c-0.5,0-0.9-0.2-1.2-0.5l0,0l-3.9-4.2l0,0c-0.3-0.3-0.4-0.7-0.4-1.1c0-0.9,0.7-1.7,1.7-1.7
									c0.5,0,0.9,0.2,1.2,0.5l0,0l2.6,2.8l6-7.3l0,0C50,28.2,50.5,28,51,28c0.9,0,1.7,0.7,1.7,1.7C52.7,30,52.5,30.4,52.3,30.7L52.3,30.7z
									"/>
								</svg>';

				add_menu_page(
					esc_html__( 'Ebox LMS', 'ebox' ),
					esc_html__( 'Ebox LMS', 'ebox' ),
					'read',
					'ebox-lms',
					null, // @phpstan-ignore-line
					'data:image/svg+xml;base64,' . base64_encode( $menu_icon ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
					$menu_position
				);

				$location = 0;

				foreach ( $add_submenu as $key => $add_submenu_item ) {
					if ( current_user_can( $add_submenu_item['cap'] ) ) {
						$_tmp_menu_item = array( $add_submenu_item['name'], $add_submenu_item['cap'], $add_submenu_item['link'] );
						if ( ( isset( $add_submenu_item['class'] ) ) && ( ! empty( $add_submenu_item['class'] ) ) ) {
							$_tmp_menu_item[4] = $add_submenu_item['class'];
						}
						$submenu['ebox-lms'][ $location++ ] = $_tmp_menu_item; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					}
				}

				/**
				 * Fires after the ebox menu and submenu are added.
				 *
				 * Action added to trigger add-ons when LD menu and submenu items have been added to the system.
				 * This works better than trying to fiddle with priority on WP 'admin_menu' hook.
				 *
				 * @since 2.4.0
				 *
				 * @param string $parent_slug ebox menu parent slug.
				 */
				do_action( 'ebox_admin_menu', 'ebox-lms' );
			}

			global $ebox_post_types;
			foreach ( $ebox_post_types as $ld_post_type ) {
				$menu_slug = 'edit.php?post_type=' . $ld_post_type;
				if ( isset( $submenu[ $menu_slug ] ) ) {
					remove_menu_page( $menu_slug );
				}
			}
		}

		/**
		 * Admin menu last or late items.
		 *
		 * @since 2.4.0
		 */
		public function ebox_admin_menu_last() {
			global $submenu, $menu, $_wp_real_parent_file, $_wp_submenu_nopriv, $_registered_pages, $_parent_pages;
			$_parent_file = get_admin_page_parent();
			$add_submenu  = array();

			if ( ( isset( $submenu['ebox-lms-non-existant'] ) ) && ( ! empty( $submenu['ebox-lms-non-existant'] ) ) ) { // cspell:disable-line.
				foreach ( $submenu['ebox-lms-non-existant'] as $submenu_idx => $submenu_item ) { // cspell:disable-line.
					if ( isset( $_parent_pages[ $submenu_item[2] ] ) ) {
						$_parent_pages[ $submenu_item[2] ] = 'admin.php?page=ebox_lms_settings'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

						$submenu['admin.php?page=ebox_lms_settings'][] = $submenu_item; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					}
				}
			}

			/**
			 * Filters admin last submenu.
			 *
			 * @since 2.5.6
			 *
			 * @param array $add_submenu An array of submenu items.
			 */
			$add_submenu = apply_filters( 'ebox_submenu_last', $add_submenu );

			$add_submenu['settings'] = array(
				'name' => esc_html_x( 'Settings', 'Settings Menu Label', 'ebox' ),
				'cap'  => ebox_ADMIN_CAPABILITY_CHECK,
				'link' => 'admin.php?page=ebox_lms_settings',
			);

			foreach ( $add_submenu as $key => $add_submenu_item ) {
				if ( current_user_can( $add_submenu_item['cap'] ) ) {
					$submenu['ebox-lms'][] = array( $add_submenu_item['name'], $add_submenu_item['cap'], $add_submenu_item['link'] ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				}
			}
		}

		/**
		 * Set up admin tabs for each admin menu page under ebox
		 *
		 * @since 2.4.0
		 */
		public function ebox_admin_tabs() {
			if ( ! is_admin() ) {
				return;
			}
			global $submenu, $menu, $parent_file;
			global $ebox_current_page_link;
			$ebox_current_page_link = '';

			$current_screen  = get_current_screen();
			$current_page_id = $current_screen->id;

			if ( $parent_file ) {
				$current_screen_parent_file = $parent_file;
			} else {
				$current_screen_parent_file = $current_screen->parent_file;
			}

			if ( 'ebox-lms' === $current_screen_parent_file ) {
				if ( 'ebox-lms_page_ebox-lms-reports' === $current_screen->id ) {
					$current_screen_parent_file = 'admin.php?page=ebox-lms-reports';
				} // phpcs:ignore Squiz.ControlStructures.ControlSignature.SpaceAfterCloseBrace

				/**
				 * The above IF should work. However what we are seeing in ebox-3661 is
				 * due to the translation of 'ebox LMS' the screen ID gets changed by WP
				 * to something like 'lms-ebox_page_ebox-lms-reports' in the French
				 * or something entirely different in other languages. So we add a secondary
				 * check on the 'page' query string param.
				 *
				 * @since 3.0.7
				 */
				elseif ( ( isset( $_GET['page'] ) ) && ( 'ebox-lms-reports' === $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$current_screen_parent_file = 'admin.php?page=ebox-lms-reports';
				}

				// See ebox-581:
				// In a normal case when viewing the ebox > Courses > All Courses tab the screen ID is set to 'edit-ebox-courses' and the parent_file is set ''edit.php?post_type=ebox-courses'.
				// However when the Admin Menu Editor plugin is installed it somehow sets the parent_file to 'ebox-lms'. So below we need to change the value back. Note this is just for the
				// listing URL. The Add New and other tabs are not effected.
				if ( 'edit-ebox-courses' === $current_screen->id ) {
					$current_screen_parent_file = 'edit.php?post_type=ebox-courses';
				}

				if ( 'edit-ebox-modules' === $current_screen->id ) {
					$current_screen_parent_file = 'edit.php?post_type=ebox-modules';
				}

				if ( 'edit-ebox-topic' === $current_screen->id ) {
					$current_screen_parent_file = 'edit.php?post_type=ebox-topic';
				}

				if ( 'edit-ebox-quiz' === $current_screen->id ) {
					$current_screen_parent_file = 'edit.php?post_type=ebox-quiz';
				}

				if ( 'edit-ebox-question' === $current_screen->id ) {
					$current_screen_parent_file = 'edit.php?post_type=ebox-question';
				}

				if ( 'edit-ebox-certificates' === $current_screen->id ) {
					$current_screen_parent_file = 'edit.php?post_type=ebox-certificates';
				}

				if ( 'edit-teams' === $current_screen->id ) {
					$current_screen_parent_file = 'edit.php?post_type=teams';
				}

				if ( 'edit-ebox-assignment' === $current_screen->id ) {
					$current_screen_parent_file = 'edit.php?post_type=ebox-assignment';
				}

				if ( ebox_is_team_leader_user() ) {
					if ( 'edit-ebox-essays' === $current_screen->id ) {
						$current_screen_parent_file = 'edit.php?post_type=ebox-essays';
					}
				}
			}

			if ( ( 'edit.php?post_type=ebox-quiz' === $current_screen_parent_file ) || ( 'edit.php?post_type=ebox-essays' === $current_screen_parent_file ) ) {
				$post_id = ! empty( $_GET['post_id'] ) ? absint( $_GET['post_id'] ) : ( empty( $_GET['post'] ) ? 0 : absint( $_GET['post'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

				if ( ! empty( $_GET['module'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$current_page_id = $current_page_id . '_' . sanitize_text_field( wp_unslash( $_GET['module'] ) );  // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				} elseif ( ! empty( $post_id ) ) {
					$current_page_id = $current_page_id . '_edit';
				}

				$menu_user_cap = ebox_ADMIN_CAPABILITY_CHECK;
				$menu_parent   = 'edit.php?post_type=ebox-quiz';

				if ( ebox_is_admin_user() ) {
					$menu_user_cap = ebox_ADMIN_CAPABILITY_CHECK;
					$menu_parent   = 'edit.php?post_type=ebox-quiz';
				} elseif ( ebox_is_team_leader_user() ) {
					$menu_user_cap = ebox_GROUP_LEADER_CAPABILITY_CHECK;
					$menu_parent   = 'ebox-lms';
				}
				$this->add_admin_tab_item(
					'edit.php?post_type=ebox-quiz',
					array(
						'link'             => 'edit.php?post_type=ebox-essays',
						'name'             => esc_html_x( 'Submitted Essays', 'Quiz Submitted Essays Tab Label', 'ebox' ),
						'id'               => 'edit-ebox-essays',
						'cap'              => $menu_user_cap,
						'parent_menu_link' => $menu_parent,
					),
					$this->admin_tab_priorities['normal']
				);
			}

			// Somewhat of a kludge. The essays are shown within the quiz post type menu section. So we can't just use
			// the default logic. But we can (below) copy the quiz tab items to a new tab set for essays.
			if ( 'edit.php?post_type=ebox-essays' === $current_screen_parent_file ) {
				if ( 'admin.php?page=ebox_lms_settings' !== $current_screen_parent_file ) {

					/**
					 * Fires after admin tabs are set.
					 */
					do_action( 'ebox_admin_tabs_set', $current_screen_parent_file, $this );
				}

				$post_id = ! empty( $_GET['post_id'] ) ? absint( $_GET['post_id'] ) : ( empty( $_GET['post'] ) ? 0 : absint( $_GET['post'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				if ( ! empty( $post_id ) ) {
					$current_page_id = 'edit-ebox-essays';
				}

				$this->admin_tab_sets['edit.php?post_type=ebox-essays'] = array();

				foreach ( $this->admin_tab_sets['edit.php?post_type=ebox-quiz'] as $menu_key => $menu_item ) {
					$this->admin_tab_sets['edit.php?post_type=ebox-essays'][ $menu_key ] = $menu_item;
				}
			}

			if ( 'edit.php?post_type=ebox-quiz' === $current_screen_parent_file ) {

				if ( ( empty( $post_id ) ) && ( ! empty( $_GET['quiz_id'] ) ) && ( 'admin_page_ldAdvQuiz' === $current_page_id ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$post_id = ebox_get_quiz_id_by_pro_quiz_id( absint( $_GET['quiz_id'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				}

				if ( ! empty( $post_id ) ) {
					$quiz_id = ebox_get_setting( $post_id, 'quiz_pro' );
					if ( ! empty( $quiz_id ) ) {

						$this->add_admin_tab_item(
							$current_screen->parent_file,
							array(
								'link' => 'post.php?post=' . $post_id . '&action=edit',
								'name' => sprintf(
									// translators: placeholder: Edit Quiz Label.
									esc_html_x( 'Edit %s', 'Edit Quiz Label', 'ebox' ),
									ebox_Custom_Label::get_label( 'quiz' )
								),
								'id'   => 'ebox-quiz_edit',
							),
							$this->admin_tab_priorities['misc']
						);

						if ( ( true === ebox_is_data_upgrade_quiz_questions_updated() ) && ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Quizzes_Builder', 'enabled' ) === 'yes' ) ) {
							$question_tab_url = add_query_arg(
								array(
									'post_type' => ebox_get_post_type_slug( 'question' ),
									'quiz_id'   => $post_id,
								),
								'edit.php'
							);
						} else {
							$question_tab_url = add_query_arg(
								array(
									'page'    => 'ldAdvQuiz',
									'module'  => 'question',
									'quiz_id' => $quiz_id,
									'post_id' => $post_id,
								),
								'admin.php'
							);
						}

						if ( ebox_get_setting( $post_id, 'statisticsOn' ) ) {
							$this->add_admin_tab_item(
								$current_screen->parent_file,
								array(
									'link' => 'admin.php?page=ldAdvQuiz&module=statistics&id=' . $quiz_id . '&post_id=' . $post_id,
									'name' => esc_html_x( 'Statistics', 'Quiz Statistics Tab Label', 'ebox' ),
									'id'   => 'ebox-quiz_page_ldAdvQuiz_statistics',
								),
								$this->admin_tab_priorities['misc']
							);
						}

						if ( ebox_get_setting( $post_id, 'toplistActivated' ) ) {
							$this->add_admin_tab_item(
								$current_screen->parent_file,
								array(
									'link' => 'admin.php?page=ldAdvQuiz&module=toplist&id=' . $quiz_id . '&post_id=' . $post_id,
									'name' => esc_html_x( 'Leaderboard', 'Quiz Leaderboard Tab Label', 'ebox' ),
									'id'   => 'ebox-quiz_page_ldAdvQuiz_toplist',
								),
								$this->admin_tab_priorities['misc']
							);
						}
					}
				}
			}

			if ( ( 'admin.php?page=ebox-lms-reports' === $current_screen_parent_file ) || ( 'edit.php?post_type=ebox-transactions' === $current_screen_parent_file ) ) {

				$this->add_admin_tab_item(
					$current_screen_parent_file,
					array(
						'id'   => 'ebox-lms_page_ebox-lms-reports',
						'name' => esc_html_x( 'Reports', 'ebox Report Menu Label', 'ebox' ),
						'link' => 'admin.php?page=ebox-lms-reports',
						'cap'  => ebox_ADMIN_CAPABILITY_CHECK,
					),
					$this->admin_tab_priorities['high']
				);

				$this->add_admin_tab_item(
					$current_screen_parent_file,
					array(
						'id'               => 'edit-ebox-transactions',
						'name'             => esc_html_x( 'Transactions', 'Transactions Tab Label', 'ebox' ),
						'link'             => 'edit.php?post_type=ebox-transactions&orderby=date&order=desc',
						'parent_menu_link' => 'admin.php?page=ebox-lms-reports',
					),
					$this->admin_tab_priorities['high']
				);

				if ( 'edit.php?post_type=ebox-transactions' === $current_screen_parent_file ) {
					$post_id = ! empty( $_GET['post_id'] ) ? absint( $_GET['post_id'] ) : ( empty( $_GET['post'] ) ? 0 : absint( $_GET['post'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					if ( ! empty( $post_id ) ) {
						$current_page_id = 'edit-ebox-transactions';
					}
				}
			}

			if ( 'edit.php?post_type=teams' === $current_screen_parent_file ) {

				if ( current_user_can( 'edit_teams' ) ) {
					$user_team_ids = ebox_get_administrators_team_ids( get_current_user_id(), true );
					if ( ! empty( $user_team_ids ) ) {

						$this->add_admin_tab_item(
							$current_screen_parent_file,
							array(
								'id'   => 'teams_page_team_admin_page',
								'name' => sprintf(
									// translators: Team.
									esc_html_x( '%s Administration', 'placeholder: Team', 'ebox' ),
									ebox_Custom_Label::get_label( 'team' )
								),
								'link' => 'admin.php?page=team_admin_page',
								'cap'  => 'edit_teams',
							),
							$this->admin_tab_priorities['high']
						);
					}
				}
			}

			if ( 'ebox-lms_page_team_admin_page' === $current_screen->id ) {

				$this->add_admin_tab_item(
					$current_screen_parent_file,
					array(
						'id'   => 'ebox-lms_page_team_admin_page',
						'name' => ebox_Custom_Label::get_label( 'teams' ),
						'link' => 'admin.php?page=team_admin_page',
						'cap'  => ebox_GROUP_LEADER_CAPABILITY_CHECK,
					),
					$this->admin_tab_priorities['high']
				);
			}

			/**
			 * Filters admin setting tabs.
			 *
			 * @since 2.4.0
			 *
			 * @param array $admin_tabs An array of admin setting tabs data.
			 */
			$admin_tabs_legacy = apply_filters( 'ebox_admin_tabs', array() );
			foreach ( $admin_tabs_legacy as $tab_idx => $tab_item ) {
				if ( empty( $tab_item ) ) {
					unset( $admin_tabs_legacy[ $tab_idx ] );
				} else {
					if ( 'edit.php?post_type=ebox-courses&page=ebox-lms_ebox_lms.php_post_type_ebox-courses' === $admin_tabs_legacy[ $tab_idx ]['menu_link'] ) {
						$admin_tabs_legacy[ $tab_idx ]['menu_link'] = 'admin.php?page=ebox_lms_settings';
					}
				}
			}

			if ( 'ebox-lms-non-existant' === $current_screen_parent_file ) { // cspell:disable-line.
				$menu_link = '';
				foreach ( $admin_tabs_legacy as $tab_idx => $tab_item ) {
					if ( $tab_item['id'] === $current_page_id ) {
						$current_screen_parent_file = $tab_item['menu_link'];
						break;
					}
				}
			}

			if ( 'admin.php?page=ebox_lms_settings' === $current_screen_parent_file ) {

				if ( ( defined( 'ebox_LICENSE_PANEL_SHOW' ) ) && ( true === ebox_LICENSE_PANEL_SHOW ) ) {
					$this->add_admin_tab_item(
						'admin.php?page=ebox_lms_settings',
						array(
							'link' => 'admin.php?page=nss_plugin_license-ebox_lms-settings',
							'name' => esc_html_x( 'LMS License', 'LMS License Tab Label', 'ebox' ),
							'id'   => 'admin_page_nss_plugin_license-ebox_lms-settings',
						),
						50
					);
				}

				/** This action is documented in includes/admin/class-ebox-admin-menus-tabs.php */
				do_action( 'ebox_admin_tabs_set', $current_screen_parent_file, $this );

				// Here we add the legacy tabs to the end of the existing tabs.
				if ( ! empty( $admin_tabs_legacy ) ) {
					foreach ( $admin_tabs_legacy as $tab_idx => $tab_item ) {
						if ( $tab_item['menu_link'] === $current_screen_parent_file ) {
							$this->add_admin_tab_item(
								$current_screen_parent_file,
								$tab_item,
								80
							);
						}
					}
				}
			}

			if ( ( 'edit.php?post_type=ebox-essays' !== $current_screen_parent_file ) && ( 'admin.php?page=ebox_lms_settings' !== $current_screen_parent_file ) ) {

				/** This action is documented in includes/admin/class-ebox-admin-menus-tabs.php */
				do_action( 'ebox_admin_tabs_set', $current_screen_parent_file, $this );
			}

			$admin_tabs_on_page_legacy = array();
			$admin_tabs_on_page_legacy['ebox-courses_page_ebox-lms_ebox_lms_post_type_ebox-courses'] = array();

			/**
			 * Filters List of admin tabs on a page.
			 *
			 * @since 2.4.0
			 *
			 * @param array  $admin_tabs      An array of admin tabs on a page.
			 * @param array  $array           Unused filter parameter.
			 * @param string $current_page_id Current page id.
			 */
			$admin_tabs_on_page_legacy = apply_filters( 'ebox_admin_tabs_on_page', $admin_tabs_on_page_legacy, $array = array(), $current_page_id );
			foreach ( $admin_tabs_on_page_legacy as $tab_idx => $tab_set ) {
				if ( empty( $tab_set ) ) {
					unset( $admin_tabs_on_page_legacy[ $tab_idx ] );
				}
			}

			if ( isset( $admin_tabs_on_page_legacy[ $current_page_id ] ) ) {
				$admin_tabs_on_page_legacy_set = $admin_tabs_on_page_legacy[ $current_page_id ];
				if ( ( ! empty( $admin_tabs_on_page_legacy_set ) ) && ( is_array( $admin_tabs_on_page_legacy_set ) ) ) {
					foreach ( $admin_tabs_on_page_legacy_set as $admin_tab_idx ) {
						if ( isset( $admin_tabs_legacy[ $admin_tab_idx ] ) ) {
							$admin_tab_item             = $admin_tabs_legacy[ $admin_tab_idx ];
							$current_screen_parent_file = $admin_tab_item['menu_link'];
							$this->add_admin_tab_item(
								$admin_tab_item['menu_link'],
								$admin_tab_item,
								80
							);
							unset( $admin_tabs_legacy[ $admin_tab_idx ] );
						}
						unset( $admin_tabs_on_page_legacy_set[ $admin_tab_idx ] );
					}
				}
			}

			// Get tabs data to new tabs system.
			$this->show_admin_tabs( $current_screen_parent_file, $current_page_id );
		}

		/**
		 * Get admin tabs data to new tabs system.
		 *
		 * @since 3.0.0
		 *
		 * @param string $menu_tab_key    The menu tab key.
		 * @param string $current_page_id The current page id.
		 *
		 * @return array
		 */
		public function get_admin_tabs( $menu_tab_key = '', $current_page_id = '' ) {

			if ( isset( $this->admin_tab_sets[ $menu_tab_key ] ) ) {

				if ( ! empty( $this->admin_tab_sets[ $menu_tab_key ] ) ) {

					ksort( $this->admin_tab_sets[ $menu_tab_key ] );

					/**
					 * Filters current admin tab set.
					 *
					 * @since 2.5.0
					 *
					 * @param array  $admin_tab_sets  An array of admin tab sets data.
					 * @param string $menu_tab_key    The menu tab key.
					 * @param string $current_page_id The current page id.
					 */
					$this->admin_tab_sets[ $menu_tab_key ] = apply_filters( 'ebox_admin_tab_sets', $this->admin_tab_sets[ $menu_tab_key ], $menu_tab_key, $current_page_id );

					if ( ! empty( $this->admin_tab_sets[ $menu_tab_key ] ) ) {
						global $ebox_current_page_link;
						if ( ( isset( $this->admin_tab_sets[ $menu_tab_key ]['parent_menu_link'] ) ) && ( ! empty( $this->admin_tab_sets[ $menu_tab_key ]['parent_menu_link'] ) ) ) {
							$ebox_current_page_link = trim( $this->admin_tab_sets[ $menu_tab_key ]['parent_menu_link'] );
						} else {
							$ebox_current_page_link = $menu_tab_key;
						}
						add_action( 'admin_footer', 'ebox_select_menu' );

						return $this->admin_tab_sets[ $menu_tab_key ];
					}
				}
			}

			return array();
		}

		/**
		 * Show admin tabs
		 *
		 * @since 2.4.0
		 *
		 * @param string $menu_tab_key    Menu tab key.
		 * @param string $current_page_id Current Page ID.
		 */
		public function show_admin_tabs( $menu_tab_key = '', $current_page_id = '' ) {

			/**
			 * Control if admin tabs should be displayed.
			 *
			 * @param array $flag Defines if tabs should be displayed.
			 */
			if ( isset( $this->admin_tab_sets[ $menu_tab_key ] ) ) {

				if ( ! empty( $this->admin_tab_sets[ $menu_tab_key ] ) ) {

					ksort( $this->admin_tab_sets[ $menu_tab_key ] );

					/** This filter is documented in includes/admin/class-ebox-admin-menus-tabs.php */
					$this->admin_tab_sets[ $menu_tab_key ] = apply_filters( 'ebox_admin_tab_sets', $this->admin_tab_sets[ $menu_tab_key ], $menu_tab_key, $current_page_id );
					if ( ! empty( $this->admin_tab_sets[ $menu_tab_key ] ) ) {
						global $ebox_current_page_link;
						if ( ( isset( $this->admin_tab_sets[ $menu_tab_key ]['parent_menu_link'] ) ) && ( ! empty( $this->admin_tab_sets[ $menu_tab_key ]['parent_menu_link'] ) ) ) {
							$ebox_current_page_link = trim( $this->admin_tab_sets[ $menu_tab_key ]['parent_menu_link'] );
						} else {
							if ( 'edit.php?post_type=ebox-essays' === $menu_tab_key ) {
								if ( true !== ebox_is_team_leader_user() ) {
									$ebox_current_page_link = 'edit.php?post_type=ebox-quiz';
								}
							} elseif ( 'edit.php?post_type=ebox-transactions' === $menu_tab_key ) {
								$ebox_current_page_link = 'admin.php?page=ebox-lms-reports';
							} else {
								$ebox_current_page_link = $menu_tab_key;
							}
						}
						add_action( 'admin_footer', 'ebox_select_menu' );

						/**
						 * Filters whether to show admin settings header panel or not.
						 *
						 * @since 3.0.0
						 *
						 * @param boolean $setting_header_panel Whether to show admin header panel or not.
						 */
						if ( ( defined( 'ebox_SETTINGS_HEADER_PANEL' ) ) && ( true === apply_filters( 'ebox_settings_header_panel', ebox_SETTINGS_HEADER_PANEL ) ) ) {
							$this->admin_header_panel( $menu_tab_key );

						} else {
							echo '<h1 class="nav-tab-wrapper">';

							$post_id = ! empty( $_GET['post_id'] ) ? absint( $_GET['post_id'] ) : ( empty( $_GET['post'] ) ? 0 : absint( $_GET['post'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

							foreach ( $this->admin_tab_sets[ $menu_tab_key ] as $admin_tab_item ) {
								if ( ! isset( $admin_tab_item['id'] ) ) {
									$admin_tab_item['id'] = '';
								}

								if ( ! empty( $admin_tab_item['id'] ) ) {

									if ( $admin_tab_item['id'] == $current_page_id ) {
										$class = 'nav-tab nav-tab-active';

										global $ebox_current_page_link;
										if ( ( isset( $admin_tab_item['parent_menu_link'] ) ) && ( ! empty( $admin_tab_item['parent_menu_link'] ) ) ) {
											$ebox_current_page_link = trim( $admin_tab_item['parent_menu_link'] );
										} else {
											$ebox_current_page_link = $menu_tab_key;
										}

										add_action( 'admin_footer', 'ebox_select_menu' );

									} else {
										$class = 'nav-tab';
									}

									$target = ! empty( $admin_tab_item['target'] ) ? 'target="' . esc_attr( $admin_tab_item['target'] ) . '"' : '';

									$url = '';
									if ( ( isset( $admin_tab_item['external_link'] ) ) && ( ! empty( $admin_tab_item['external_link'] ) ) ) {
										$url = $admin_tab_item['external_link'];
									} elseif ( ( isset( $admin_tab_item['link'] ) ) && ( ! empty( $admin_tab_item['link'] ) ) ) {
										$url = $admin_tab_item['link'];

									} else {
										$pos = strpos( $admin_tab_item['id'], 'ebox-lms_page_' );
										if ( false !== $pos ) {
											$url_page = str_replace( 'ebox-lms_page_', '', $admin_tab_item['id'] );
											$url      = add_query_arg( array( 'page' => $url_page ), 'admin.php' );
										}
									}

									if ( ! empty( $url ) ) {
										echo '<a href="' . esc_url( $url ) . '" class="' . esc_attr( $class ) . ' nav-tab-' . esc_attr( $admin_tab_item['id'] ) . '"  ' . $target . '>' . esc_html( $admin_tab_item['name'] ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $target escaped when defined
									}
								}
							}
							echo '</h1>';
						}
					}
				}
			}
		}

		/**
		 * Show the new Admin header panel
		 *
		 * @since 3.0.0
		 *
		 * @param string $menu_tab_key Current tab key to show.
		 */
		protected function admin_header_panel( $menu_tab_key = '' ) {
			global $pagenow, $post, $typenow;
			global $ebox_assets_loaded;
			global $ebox_metaboxes;

			if ( ( empty( $menu_tab_key ) ) || ( ! isset( $this->admin_tab_sets[ $menu_tab_key ] ) ) || ( empty( $this->admin_tab_sets[ $menu_tab_key ] ) ) ) {
				return;
			}

			$screen = get_current_screen();

			$header_data = array(
				'tabs'           => array(),
				'currentTab'     => $screen->id,
				'editing'        => 1,
				'ajaxurl'        => admin_url( 'admin-ajax.php' ),
				'adminurl'       => admin_url( 'edit.php' ),
				'quizImportUrl'  => admin_url( 'admin.php?page=ldAdvQuiz' ),
				'postadminurl'   => admin_url( 'post.php' ),
				'back_to_title'  => '',
				'back_to_url'    => '',
				'error_messages' => array(
					'builder' => esc_html__( 'There was an unexpected error while loading. Please try refreshing the page. If the error continues, contact ebox support.', 'ebox' ),
					'header'  => esc_html__( 'There was an unexpected error while loading. Please try refreshing the page. If the error continues, contact ebox support.', 'ebox' ),
				),
				'labels'         => array(
					'section-heading'     => esc_html__( 'Section Heading', 'ebox' ),
					'section-headings'    => esc_html__( 'Section Headings', 'ebox' ),
					'answer'              => esc_html__( 'answer', 'ebox' ),
					'answers'             => esc_html__( 'answers', 'ebox' ),
					'certificate'         => esc_html__( 'Certificate', 'ebox' ),
					'certificates'        => esc_html__( 'Certificates', 'ebox' ),
					'course'              => \ebox_Custom_Label::get_label( 'course' ),
					'courses'             => \ebox_Custom_Label::get_label( 'courses' ),
					'lesson'              => \ebox_Custom_Label::get_label( 'lesson' ),
					'modules'             => \ebox_Custom_Label::get_label( 'modules' ),
					'topic'               => \ebox_Custom_Label::get_label( 'topic' ),
					'topics'              => \ebox_Custom_Label::get_label( 'topics' ),
					'quiz'                => \ebox_Custom_Label::get_label( 'quiz' ),
					'quizzes'             => \ebox_Custom_Label::get_label( 'quizzes' ),
					'question'            => \ebox_Custom_Label::get_label( 'question' ),
					'questions'           => \ebox_Custom_Label::get_label( 'questions' ),
					'ebox-course'         => \ebox_Custom_Label::get_label( 'course' ),
					'ebox-courses'        => \ebox_Custom_Label::get_label( 'courses' ),
					'ebox-lesson'         => \ebox_Custom_Label::get_label( 'lesson' ),
					'ebox-modules'        => \ebox_Custom_Label::get_label( 'modules' ),
					'ebox-topic'          => \ebox_Custom_Label::get_label( 'topic' ),
					'ebox-topics'         => \ebox_Custom_Label::get_label( 'topics' ),
					'ebox-quiz'           => \ebox_Custom_Label::get_label( 'quiz' ),
					'ebox-quizzes'        => \ebox_Custom_Label::get_label( 'quizzes' ),
					'ebox-question'       => \ebox_Custom_Label::get_label( 'question' ),
					'ebox-certificates'   => esc_html__( 'Certificates', 'ebox' ),
					'start-adding-lesson' => sprintf(
						// translators: placeholder: Lesson.
						esc_html_x( 'Start by adding a %s.', 'placeholder: Lesson', 'ebox' ),
						\ebox_Custom_Label::get_label( 'lesson' )
					),
				),
				'eboxMap'        => array(
					'lesson'   => 'ebox-modules',
					'topic'    => 'ebox-topic',
					'quiz'     => 'ebox-quiz',
					'question' => 'ebox-question',
				),
				'rest'           => array(
					'namespace' => ebox_REST_API_NAMESPACE . '/v1',
					'base'      => array(
						'modules'  => \ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_General_REST_API', 'ebox-modules' ),
						'topic'    => \ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_General_REST_API', 'ebox-topic' ),
						'quiz'     => \ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_General_REST_API', 'ebox-quiz' ),
						'question' => 'ebox-questions',
					),
					'root'      => esc_url_raw( rest_url() ),
					'nonce'     => wp_create_nonce( 'wp_rest' ),
				),
				'post_data'      => array(
					'builder_post_id'    => 0,
					'builder_post_title' => '',
					'builder_post_type'  => '',
				),
				'posts_per_page' => 0,
				'modules'        => array(),
				'topics'         => array(),
				'quizzes'        => array(),
				'questions'      => array(),
				'i18n'           => array(
					'back_to'                            => esc_html_x( 'Back to', 'Link back to the post type overview', 'ebox' ),
					'actions'                            => esc_html_x( 'Actions', 'Builder actions dropdown', 'ebox' ),
					'expand'                             => esc_html_x( 'Expand All', 'Builder elements', 'ebox' ),
					'collapse'                           => esc_html_x( 'Collapse All', 'Builder elements', 'ebox' ),
					'error'                              => esc_html__( 'An error occurred while submitting your request. Please try again.', 'ebox' ),
					'cancel'                             => esc_html__( 'Cancel', 'ebox' ),
					'edit'                               => esc_html__( 'Edit', 'ebox' ),
					'remove'                             => esc_html__( 'Remove', 'ebox' ),
					'save'                               => esc_html__( 'Save', 'ebox' ),
					'settings'                           => esc_html__( 'Settings', 'ebox' ),
					'edit_question'                      => sprintf(
						// translators: placeholder: question.
						esc_html_x( 'Click here to edit the %s', 'placeholder: question.', 'ebox' ),
						ebox_get_custom_label_lower( 'question' )
					),
					'correct_answer_message'             => esc_html__( 'Message for correct answer - optional', 'ebox' ),
					'different_incorrect_answer_message' => esc_html__( 'Use different message for incorrect answer', 'ebox' ),
					'same_answer_message'                => esc_html__( 'Currently same message is displayed as above.', 'ebox' ),
					'incorrect_answer_message'           => esc_html__( 'Message for incorrect answer - optional', 'ebox' ),

					'essay_answer_message'               => esc_html__( 'Message after Essay is submitted - optional', 'ebox' ),

					'solution_hint'                      => esc_html__( 'Solution hint', 'ebox' ),
					'points'                             => esc_html__( 'points', 'ebox' ),
					'edit_answer'                        => esc_html__( 'Click here to edit the answer', 'ebox' ),
					'update_answer'                      => esc_html__( 'Update Answer', 'ebox' ),
					'answer_missing'                     => esc_html__( 'Answer is missing', 'ebox' ),
					'correct_answer_missing'             => esc_html__( 'Required correct answer is missing', 'ebox' ),
					'allow_html'                         => esc_html__( 'Allow HTML', 'ebox' ),
					'correct'                            => esc_html__( 'Correct', 'ebox' ),
					'correct_1st'                        => wp_kses_post( _x( '1<sup>st</sup>', 'First sort answer correct', 'ebox' ) ),
					'correct_2nd'                        => wp_kses_post( _x( '2<sup>nd</sup>', 'Second sort answer correct', 'ebox' ) ),
					'correct_3rd'                        => wp_kses_post( _x( '3<sup>rd</sup>', 'Third sort answer correct', 'ebox' ) ),
					'correct_nth'                        => '<sup>' . esc_html_x( 'th', 'nth sort answer correct', 'ebox' ) . '</sup>',
					'answer_updated'                     => esc_html__( 'Answer updated', 'ebox' ),
					'edit_answer_settings'               => esc_html__( 'Edit answer settings', 'ebox' ),
					'answer'                             => esc_html__( 'Answer:', 'ebox' ),
					'edit_matrix'                        => esc_html__( 'Click here to edit the matrix', 'ebox' ),
					'new_element_labels'                 => array(
						'question'        => sprintf(
							/* translators: placeholders: Question. */
							esc_html_x( 'New %1$s', 'placeholder: Question', 'ebox' ),
							ebox_Custom_Label::get_label( 'question' )
						),
						'quiz'            => sprintf(
							/* translators: placeholders: Quiz. */
							esc_html_x( 'New %1$s', 'placeholder: Quiz', 'ebox' ),
							ebox_Custom_Label::get_label( 'quiz' )
						),
						'topic'           => sprintf(
							/* translators: placeholders: Topic. */
							esc_html_x( 'New %1$s', 'placeholder: Topic', 'ebox' ),
							ebox_Custom_Label::get_label( 'topic' )
						),
						'lesson'          => sprintf(
							/* translators: placeholders: Lesson. */
							esc_html_x( 'New %1$s', 'placeholder: Lesson', 'ebox' ),
							ebox_Custom_Label::get_label( 'lesson' )
						),
						'answer'          => esc_html__( 'New answer', 'ebox' ),
						'section-heading' => esc_html__( 'New Section Heading', 'ebox' ),
					),
					'enter_title'                        => esc_html_x( 'Enter a title', 'Title for the new course, lesson, quiz', 'ebox' ),
					'enter_answer'                       => esc_html_x( 'Enter an answer', 'Answer for a question', 'ebox' ),
					'please_wait'                        => esc_html_x( 'Please wait...', 'Please wait while the form is loading', 'ebox' ),
					'add_element'                        => esc_html_x( 'Add', 'Add lesson, topic, quiz...', 'ebox' ),
					'add_element_labels'                 => array(
						'question'        => sprintf(
							/* translators: placeholders: Question. */
							esc_html_x( 'Add %1$s', 'placeholder: Question', 'ebox' ),
							ebox_Custom_Label::get_label( 'question' )
						),
						'questions'       => sprintf(
							/* translators: placeholders: Question. */
							esc_html_x( 'Add %1$s', 'placeholder: Questions', 'ebox' ),
							ebox_Custom_Label::get_label( 'questions' )
						),
						'quiz'            => sprintf(
							/* translators: placeholders: Quiz. */
							esc_html_x( 'Add %1$s', 'placeholder: Quiz', 'ebox' ),
							ebox_Custom_Label::get_label( 'quiz' )
						),
						'topic'           => sprintf(
							/* translators: placeholders: Topic. */
							esc_html_x( 'Add %1$s', 'placeholder: Topic', 'ebox' ),
							ebox_Custom_Label::get_label( 'topic' )
						),
						'lesson'          => sprintf(
							/* translators: placeholders: Lesson. */
							esc_html_x( 'Add %1$s', 'placeholder: Lesson', 'ebox' ),
							ebox_Custom_Label::get_label( 'lesson' )
						),
						'answer'          => esc_html__( 'Add answer', 'ebox' ),
						'section-heading' => esc_html__( 'Add Section Heading', 'ebox' ),
					),
					'move_up'                            => esc_html_x( 'Move up', 'Move the current element up in the builder interface', 'ebox' ),
					'question_empty'                     => sprintf(
						/* translators: placeholders: question */
						esc_html_x( 'The %s is empty.', 'Warning when no question was entered', 'ebox' ),
						ebox_get_custom_label_lower( 'question' )
					),
					'question_data_invalid'              => sprintf(
						/* translators: placeholders: question */
						esc_html_x( 'The %s data is invalid.', 'placeholders: question', 'ebox' ),
						ebox_get_custom_label_lower( 'question' )
					),
					'move_down'                          => esc_html_x( 'Move down', 'Move the current element down in the builder interface', 'ebox' ),
					'rename'                             => esc_html_x( 'Rename', 'Rename the current element in the builder interface', 'ebox' ),
					'search_element_labels'              => array(
						'lesson'   => sprintf(
							/* translators: placeholders: modules. */
							esc_html_x( 'Search %1$s', 'placeholders: modules', 'ebox' ),
							ebox_Custom_Label::get_label( 'modules' )
						),
						'quiz'     => sprintf(
							/* translators: placeholders: Quizzes */
							esc_html_x( 'Search %1$s', 'placeholders: quizzes', 'ebox' ),
							ebox_Custom_Label::get_label( 'quizzes' )
						),
						'topic'    => sprintf(
							/* translators: placeholders: Topics. */
							esc_html_x( 'Search %1$s', 'placeholders: topics', 'ebox' ),
							ebox_Custom_Label::get_label( 'topics' )
						),
						'question' => sprintf(
							/* translators: placeholders: Questions. */
							esc_html_x( 'Search %1$s', 'placeholders: questions', 'ebox' ),
							ebox_Custom_Label::get_label( 'questions' )
						),
					),
					'recent'                             => esc_html_x( 'Recent', 'List of recent modules, topics, quizzes or questions', 'ebox' ),
					'view_all'                           => esc_html_x( 'View all', 'Lesson, Topic, Quiz or Question posts', 'ebox' ),
					'start_adding_element_labels'        => array(
						'lesson'   => sprintf(
							/* translators: placeholders: Lesson. */
							esc_html_x( 'Start adding your first %1$s', 'placeholders: Lesson', 'ebox' ),
							ebox_Custom_Label::get_label( 'lesson' )
						),
						'quiz'     => sprintf(
							/* translators: placeholders: Quiz. */
							esc_html_x( 'Start adding your first %1$s', 'placeholders: Quiz', 'ebox' ),
							ebox_Custom_Label::get_label( 'quiz' )
						),
						'topic'    => sprintf(
							/* translators: placeholders: Topic. */
							esc_html_x( 'Start adding your first %1$s', 'placeholders: Topic', 'ebox' ),
							ebox_Custom_Label::get_label( 'topic' )
						),
						'question' => sprintf(
							/* translators: placeholders: Question. */
							esc_html_x( 'Start adding your first %1$s', 'placeholders: Question', 'ebox' ),
							ebox_Custom_Label::get_label( 'question' )
						),
					),
					'all_elements_added_labels'          => array(
						'lesson'   => sprintf(
							/* translators: placeholders: modules. */
							esc_html_x( 'All available %1$s have been added.', 'placeholders: modules', 'ebox' ),
							ebox_Custom_Label::get_label( 'modules' )
						),
						'quiz'     => sprintf(
							/* translators: placeholders: Quizzes. */
							esc_html_x( 'All available %1$s have been added.', 'placeholders: Quizzes', 'ebox' ),
							ebox_Custom_Label::get_label( 'quizzes' )
						),
						'topic'    => sprintf(
							/* translators: placeholders: Topics. */
							esc_html_x( 'All available %1$s have been added.', 'placeholders: Topics', 'ebox' ),
							ebox_Custom_Label::get_label( 'topics' )
						),
						'question' => sprintf(
							/* translators: placeholders: Questions. */
							esc_html_x( 'All available %1$s have been added.', 'placeholders: Questions', 'ebox' ),
							ebox_Custom_Label::get_label( 'questions' )
						),
					),
					'start_adding'                       => esc_html_x( 'Start adding your first', 'Lesson, Topic, Quiz or Question', 'ebox' ),
					'refresh'                            => esc_html_x( 'Refresh', 'Builder - Refresh list of  modules, Topics, Quizzes or Questions', 'ebox' ),
					'load_more'                          => esc_html_x( 'Load More', 'Builder - Load more modules, Topics, Quizzes or Questions', 'ebox' ),
					'add_selected'                       => esc_html_x( 'Add Selected', 'Builder - Add selected modules, Topics, Quizzes or Questions', 'ebox' ),
					'undo'                               => esc_html_x( 'Undo', 'Undo action in the builder', 'ebox' ),
					'criterion'                          => esc_html_x( 'Criterion', 'Matrix answer Criterion', 'ebox' ),
					'sort_element'                       => esc_html_x( 'Sort element', 'Sort matrix answer element', 'ebox' ),
					'question_settings'                  => esc_html_x( 'Settings', 'Question settings. Placeholder in JavaScript', 'ebox' ),
					'select_option'                      => esc_html_x( 'Select', 'Select an option', 'ebox' ),
					'nothing_found'                      => esc_html_x( 'Nothing matches your search', 'No matching Lesson, Topic, Quiz or Question found', 'ebox' ),
					'drop_modules'                       => sprintf(
						/* translators: placeholders: modules. */
						esc_html_x( 'Drop %1$s here', 'placeholder: modules', 'ebox' ),
						ebox_Custom_Label::get_label( 'modules' )
					),
					'drop_question'                      => sprintf(
						/* translators: placeholders: Question. */
						esc_html_x( 'Drop %1$s here', 'placeholder: Question', 'ebox' ),
						ebox_Custom_Label::get_label( 'question' )
					),
					'drop_quizzes'                       => sprintf(
						/* translators: placeholders: Quizzes. */
						esc_html_x( 'Drop %1$s here', 'placeholder: Quizzes', 'ebox' ),
						ebox_Custom_Label::get_label( 'quizzes' )
					),
					'drop_quizzes_topics'                => sprintf(
						/* translators: placeholders: Topics, Quizzes. */
						esc_html_x( 'Drop %1$s or %2$s here', 'placeholder: Topics, Quizzes', 'ebox' ),
						ebox_Custom_Label::get_label( 'topics' ),
						ebox_Custom_Label::get_label( 'quizzes' )
					),
					'step'                               => esc_html_x( 'step', 'singular - Amount of steps in a course or quiz', 'ebox' ),
					'steps'                              => esc_html_x( 'steps', 'plural - Amount of steps in a course or quiz', 'ebox' ),
					'in_this'                            => esc_html_x( 'in this', 'Amount of steps in this course or quiz', 'ebox' ),
					'final_quiz'                         => esc_html_x( 'Final', 'Builder - Final quiz. Placeholder in JavaScript', 'ebox' ),
					'quiz_no_questions'                  => sprintf(
						// translators: placeholders: Quiz, Questions.
						esc_html_x( 'This %1$s has no %2$s yet', 'placeholders: Quiz, Questions', 'ebox' ),
						ebox_Custom_Label::get_label( 'quiz' ),
						ebox_Custom_Label::get_label( 'questions' )
					),
					'question_empty_edit'                => sprintf(
						/* translators: placeholders: question. */
						esc_html_x( 'The %s is empty, click here to edit it.', 'Warning when no question was entered', 'ebox' ),
						ebox_get_custom_label_lower( 'question' )
					),
					'unsaved_changes'                    => esc_html__( 'You have unsaved changes. If you proceed, they will be lost.', 'ebox' ),
					'manage_questions_builder'           => sprintf(
						/* translators: placeholders: Questions */
						esc_html_x( 'Manage %1$s in builder', 'Manage Questions in builder', 'ebox' ),
						ebox_Custom_Label::get_label( 'questions' )
					),
					'total_points'                       => esc_html_x( 'TOTAL:', 'Total points', 'ebox' ),
					'no_content'                         => esc_html_x( 'has no content yet.', 'Displayed when the post type, e.g. course, has no content', 'ebox' ),
					'add_content'                        => esc_html_x( 'Add a new', 'Content type, e.g. lesson', 'ebox' ),
					'add_from_sidebar'                   => esc_html_x( 'or add an existing one from the sidebar', 'Content type, e.g. lesson', 'ebox' ),
					'essay_answer_format'                => esc_html_x( 'Answer format', 'Type of essay answer', 'ebox' ),
					'essay_text_answer'                  => esc_html_x( 'Text entry', 'Submit essay answer in a text box', 'ebox' ),
					'essay_file_upload_answer'           => esc_html_x( 'File upload', 'Submit essay answer as an upload', 'ebox' ),
					'essay_after_submission'             => sprintf(
						/* translators: placeholder: quiz */
						esc_html_x( 'What should happen on %s submission?', 'What grading options should be used after essay submission', 'ebox' ),
						ebox_get_custom_label_lower( 'quiz' )
					),
					'essay_not_graded_no_points'         => esc_html_x( 'Not Graded, No Points Awarded', 'Essay answer grading option', 'ebox' ),
					'essay_not_graded_full_points'       => esc_html_x( 'Not Graded, Full Points Awarded', 'Essay answer grading option', 'ebox' ),
					'essay_graded_full_points'           => esc_html_x( 'Graded, Full Points Awarded', 'Essay answer grading option', 'ebox' ),
					'essay_not_set'                      => esc_html_x( 'Not set', 'Essay answer grading option has not been set', 'ebox' ),
					'supported_media_in_answers'         => esc_html_x( 'Only image, video and audio files are supported.', 'Supported media formats in question answers', 'ebox' ),
				),
			);

			$action_menu = array();

			$screen_post_type = '';
			if ( ! empty( $typenow ) ) {
				$screen_post_type = $typenow;
			} else {
				$menu_tab_parts = wp_parse_url( $menu_tab_key );
				if ( ( isset( $menu_tab_parts['query'] ) ) && ( ! empty( $menu_tab_parts['query'] ) ) ) {
					parse_str( $menu_tab_parts['query'], $menu_tab_url_parts );
					if ( ( isset( $menu_tab_url_parts['post_type'] ) ) && ( ! empty( $menu_tab_url_parts['post_type'] ) ) ) {
						$screen_post_type = $menu_tab_url_parts['post_type'];
					}
				}
			}

			if ( ! empty( $screen_post_type ) ) {
				$screen_post_type_object = get_post_type_object( $screen_post_type );
			}

			$header_data['post_data']['builder_post_id'] = get_the_ID();
			if ( ! empty( $header_data['post_data']['builder_post_id'] ) ) {
				$header_data['post_data']['builder_post_title'] = get_the_title( $header_data['post_data']['builder_post_id'] );
			}

			$header_data['post_data']['builder_post_type'] = $screen_post_type;

			$logic_control = '';

			if ( ( isset( $_GET['page'] ) ) && ( strtolower( $_GET['page'] ) === strtolower( 'ldAdvQuiz' ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$logic_control = 'post';
			} elseif ( 'ebox-courses_page_courses-builder' === $screen->id ) {

				$header_data['currentTab'] = 'ebox_course_builder';
				$header_data['tabs']       = array();

				$header_data['back_to_title'] = ebox_get_label_course_step_back( ebox_get_post_type_slug( 'course' ), true );
				$header_data['back_to_url']   = admin_url( 'edit.php?post_type=ebox-courses' );

				if ( isset( $_GET['course_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$header_data['tabs'][] = array(
						'id'         => 'post-body-content',
						'name'       => ebox_get_label_course_step_page( ebox_get_post_type_slug( 'course' ) ),
						'link'       => get_edit_post_link( absint( $_GET['course_id'] ) ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						'isExternal' => 'true',
					);
				}

				if ( 'yes' === ebox_Settings_Section::get_section_setting( 'ebox_Settings_Courses_Builder', 'enabled' ) ) {
					$header_data['tabs'][] = array(
						'id'        => 'ebox_course_builder',
						'name'      => esc_html__( 'Builder', 'ebox' ),
						'metaboxes' => array( 'ebox_courses_builder_courses_builder' ),
					);
				}

				if ( isset( $_GET['course_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$header_data['tabs'][] = array(
						'id'         => 'ebox-courses',
						'name'       => esc_html__( 'Settings', 'ebox' ),
						'link'       => get_edit_post_link( absint( $_GET['course_id'] ) ) . '&currentTab=ebox-courses', // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						'isExternal' => 'true',
					);
				}
			} elseif ( in_array( $pagenow, array( 'edit.php', 'edit-tags.php', 'admin.php', 'options-general.php' ), true ) ) {
				$logic_control = 'archive';
			} elseif ( in_array( $pagenow, array( 'post.php', 'post-new.php' ), true ) ) {
				$logic_control = 'post';
			}

			if ( 'archive' === $logic_control ) {
				if ( ebox_is_admin_user() ) {
					$header_data['back_to_title'] = esc_html__( 'Setup', 'ebox' );
					$header_data['back_to_url']   = admin_url( 'admin.php?page=ebox-setup' );
				} else {
					$header_data['back_to_title'] = '';
					$header_data['back_to_url']   = '';
				}

				if ( 'admin.php?page=ebox_lms_settings' === $screen->parent_file ) {
					$header_data['post_data']['builder_post_title'] = esc_html__( 'Settings', 'ebox' );
				}

				if ( ebox_get_post_type_slug( 'essay' ) === $screen_post_type ) {
					if ( ebox_is_team_leader_user() ) {
						$header_data['post_data']['builder_post_title'] = $screen_post_type_object->labels->name; // @phpstan-ignore-line
					} else {
						$header_data['post_data']['builder_post_title'] = ebox_get_custom_label( 'quizzes' );
					}
				} elseif ( ( isset( $screen_post_type_object ) ) && ( is_a( $screen_post_type_object, 'WP_Post_Type' ) ) ) {
					$header_data['post_data']['builder_post_title'] = $screen_post_type_object->labels->name;
				}

				if ( ebox_get_post_type_slug( 'quiz' ) === $screen_post_type ) {
					$action_menu[] = array(
						'title'      => esc_html_x( 'Import/Export', 'Quiz Import/Export Tab Label', 'ebox' ),
						'link'       => 'admin.php?page=ldAdvQuiz',
						'isExternal' => 'false',
					);
				}

				if ( ( 'teams_page_team_admin_page' === $screen->id ) || ( 'ebox-lms_page_team_admin_page' === $screen->id ) ) {
					if ( ( isset( $_GET['team_id'] ) ) && ( ! empty( $_GET['team_id'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						if ( ( isset( $_GET['user_id'] ) ) && ( ! empty( $_GET['user_id'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
							$user = get_user_by( 'id', absint( $_GET['user_id'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
							if ( ( $user ) && ( is_a( $user, 'WP_User' ) ) ) {
								if ( ! empty( $user->display_name ) ) {
									$user_name = $user->display_name;
								} else {
									$user_name = $user->first_name . ' ' . $user->last_name;
								}
								$header_data['post_data']['builder_post_title'] = $user_name;
								$header_data['back_to_title']                   = get_the_title( absint( $_GET['team_id'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
								$header_data['back_to_url']                     = add_query_arg(
									array(
										'team_id' => absint( $_GET['team_id'] ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
										'page'     => 'team_admin_page',
									),
									admin_url( 'admin.php' )
								);
							}
						} else {
							$header_data['post_data']['builder_post_title'] = get_the_title( absint( $_GET['team_id'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
							$header_data['back_to_title']                   = sprintf(
								// translators: Team.
								esc_html_x( '%s Administration', 'placeholder: Team', 'ebox' ),
								ebox_Custom_Label::get_label( 'team' )
							);
							$header_data['back_to_url'] = add_query_arg(
								array(
									'page' => 'team_admin_page',
								),
								admin_url( 'admin.php' )
							);
						}
					} else {
						if ( 'ebox-lms_page_team_admin_page' === $screen->id ) {
							$header_data['post_data']['builder_post_title'] = ebox_Custom_Label::get_label( 'teams' );
						} else {
							$header_data['post_data']['builder_post_title'] = sprintf(
								// translators: Team.
								esc_html_x( '%s Administration', 'placeholder: Team', 'ebox' ),
								ebox_Custom_Label::get_label( 'team' )
							);
						}
					}
				}

				foreach ( $this->admin_tab_sets[ $menu_tab_key ] as $menu_item ) {
					if ( ( isset( $menu_item['link'] ) ) && ( ! empty( $menu_item['link'] ) ) ) {
						$link_parts = wp_parse_url( $menu_item['link'] );
						if ( ( ! isset( $menu_item['cap'] ) ) || ( ! current_user_can( $menu_item['cap'] ) ) ) {
							continue;
						}

						if ( ( isset( $ebox_metaboxes[ $screen->id ] ) ) && ( ! empty( $ebox_metaboxes[ $screen->id ] ) ) ) {
							$metaboxes = array_keys( $ebox_metaboxes[ $screen->id ] );
						} else {
							$metaboxes = array();
						}

						if ( ( isset( $link_parts['path'] ) ) && ( ! empty( $link_parts['path'] ) ) ) {
							if ( 'edit.php' === $link_parts['path'] ) {

								$header_data['tabs'][] = array(
									'id'         => $menu_item['id'],
									'name'       => $menu_item['name'],
									'link'       => admin_url( $menu_item['link'] ),
									'isExternal' => 'true',
									'actions'    => array(),
									'metaboxes'  => $metaboxes,
								);
							} elseif ( ( 'admin.php' === $link_parts['path'] ) || ( 'options-general.php' === $link_parts['path'] ) ) {

								$header_data['tabs'][] = array(
									'id'         => $menu_item['id'],
									'name'       => $menu_item['name'],
									'link'       => admin_url( $menu_item['link'] ),
									'isExternal' => 'true',
									'actions'    => array(),
									'metaboxes'  => $metaboxes,
								);
							} elseif ( 'edit-tags.php' === $link_parts['path'] ) {
								$action_menu[] = array(
									'title'      => $menu_item['name'],
									'link'       => $menu_item['link'],
									'isExternal' => 'false',
									'metaboxes'  => $metaboxes,
								);
							}
						}
					}
				}

				if ( ( 'ebox-lms_page_ebox-lms-reports' === $screen->id ) || ( ( isset( $_GET['page'] ) ) && ( 'ebox-lms-reports' === $_GET['page'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					if ( isset( $header_data['tabs'][0] ) ) {
						$header_data['currentTab'] = $header_data['tabs'][0]['id'];
					}
				}
			} elseif ( 'post' === $logic_control ) {
				$header_data['back_to_title'] = esc_html__( 'Back', 'ebox' );
				$header_data['back_to_url']   = admin_url( 'edit.php?post_type=' . $screen_post_type );

				if ( ( isset( $_GET['currentTab'] ) ) && ( ! empty( $_GET['currentTab'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$header_data['currentTab'] = sanitize_text_field( wp_unslash( $_GET['currentTab'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				} else {
					$header_data['currentTab'] = 'post-body-content';
				}

				$header_data['post_data']['builder_post_id'] = get_the_ID();
				if ( ! $header_data['post_data']['builder_post_id'] ) {
					if ( ( isset( $_GET['post'] ) ) && ( ! empty( $_GET['post'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						$post_id = absint( $_GET['post'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						if ( get_post_type( $post_id ) === ebox_get_post_type_slug( 'quiz' ) ) {
							$header_data['post_data']['builder_post_id'] = $post_id;
						}
					} else {
						if ( ( isset( $_GET['post_id'] ) ) && ( ! empty( $_GET['post_id'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
							$post_id = absint( $_GET['post_id'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
							if ( get_post_type( $post_id ) === ebox_get_post_type_slug( 'quiz' ) ) {
								$header_data['post_data']['builder_post_id'] = $post_id;
							}
						}
					}
				}

				$header_data['post_data']['builder_post_title'] = '';
				if ( ! empty( $header_data['post_data']['builder_post_id'] ) ) {
					$header_data['post_data']['builder_post_title'] = get_the_title( $header_data['post_data']['builder_post_id'] );
				}

				$header_data['post_data']['builder_post_type'] = $screen_post_type;
				$header_data['back_to_title']                  = ebox_get_label_course_step_back( $screen_post_type, true );
				$header_data['tabs']                           = array(
					array(
						'id'      => 'post-body-content',
						'name'    => ebox_get_label_course_step_page( $screen_post_type ),
						'actions' => array(),
					),
				);

				if ( ( isset( $_GET['page'] ) ) && ( 'ldAdvQuiz' === $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					if ( ( isset( $_GET['post_id'] ) ) && ( ! empty( $_GET['post_id'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						if ( ( isset( $_GET['module'] ) ) && ( 'question' === $_GET['module'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
							if ( ( isset( $_GET['action'] ) ) && ( 'addEdit' === $_GET['action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
								$header_data['currentTab']    = $screen->id;
								$header_data['back_to_title'] = ebox_get_label_course_step_back( ebox_get_post_type_slug( 'question' ), true );
								$header_data['back_to_url']   = add_query_arg(
									array(
										'page'    => 'ldAdvQuiz',
										'module'  => 'question',
										'quiz_id' => isset( $_GET['quiz_id'] ) ? absint( $_GET['quiz_id'] ) : 0, // phpcs:ignore WordPress.Security.NonceVerification.Recommended
										'post_id' => absint( $_GET['post_id'] ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
									),
									'admin.php'
								);

								$header_data['currentTab'] = $screen->id;

								$header_data['tabs'] = array(
									array(
										'id'      => $screen->id,
										'name'    => ebox_get_label_course_step_page( ebox_get_post_type_slug( 'question' ) ),
										'actions' => array(),
									),
								);
							} else {
								$header_data['back_to_title'] = ebox_get_label_course_step_back( ebox_get_post_type_slug( 'quiz' ), true );
								$header_data['back_to_url']   = admin_url( 'edit.php?post_type=' . ebox_get_post_type_slug( 'quiz' ) );
								$header_data['currentTab']    = $screen->id;

								$header_data['tabs'] = array(
									array(
										'id'      => $screen->id,
										'name'    => ebox_get_custom_label( 'questions' ),
										'actions' => array(),
									),
								);
							}
						} else {
							$header_data['back_to_title'] = ebox_get_label_course_step_page( ebox_get_post_type_slug( 'quiz' ) );
							$header_data['back_to_url']   = get_edit_post_link( absint( $_GET['post_id'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
							$header_data['currentTab']    = $screen->id;
						}
					} else {
						// Quiz Import.Export page.
						$header_data['currentTab'] = 'import-export';
						$header_data['tabs']       = array(
							array(
								'id'         => $header_data['currentTab'],
								'name'       => 'Import/Export Page',
								'link'       => admin_url( 'admin.php?page=ldAdvQuiz' ),
								'isExternal' => 'true',
								'actions'    => array(),
							),
						);
					}

					if ( ( isset( $_GET['post'] ) ) && ( ! empty( $_GET['post'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended

						$action_menu = array_merge(
							$action_menu,
							array(
								array(
									'title'      => sprintf(
										// translators: placeholders: Quiz, Questions.
										esc_html_x( 'Reprocess %1$s %2$s', 'placeholders: Quiz, Questions', 'ebox' ),
										ebox_Custom_Label::get_label( 'Quiz' ),
										ebox_Custom_Label::get_label( 'Questions' )
									),
									'link'       => add_query_arg( 'quiz_id', absint( $_GET['post'] ), admin_url( 'admin.php?page=ebox_data_upgrades' ) ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
									'isExternal' => 'true',
								),
							)
						);

						if ( current_user_can( 'wpProQuiz_export' ) ) {

							$action_menu = array_merge(
								$action_menu,
								array(
									array(
										'title'      => sprintf(
											// translators: placeholder: Quiz.
											esc_html_x( 'Export %s', 'placeholder: Quiz', 'ebox' ),
											ebox_Custom_Label::get_label( 'quiz' )
										),
										'link'       => add_query_arg(
											array(
												'page'    => 'ldAdvQuiz',
												'quiz_id' => absint( $_GET['post'] ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
											),
											admin_url( 'admin.php' )
										),
										'isExternal' => 'true',
									),
								)
							);
						}

						if ( ebox_get_setting( absint( $_GET['post'] ), 'statisticsOn' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
							$action_menu = array_merge(
								$action_menu,
								array(
									array(
										'title'      => esc_html__( 'Statistics', 'ebox' ),
										'link'       => add_query_arg(
											array(
												'module' => 'statistics',
												'currentTab' => 'statistics',
											),
											$this->get_quiz_base_url()
										),
										'isExternal' => 'false',
									),
								)
							);
						}

						if ( ebox_get_setting( absint( $_GET['post'] ), 'toplistActivated' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
							$action_menu = array_merge(
								$action_menu,
								array(
									array(
										'title'      => esc_html__( 'Leaderboard', 'ebox' ),
										'link'       => add_query_arg(
											array(
												'module' => 'toplist',
												'currentTab' => 'leaderboard',
											),
											$this->get_quiz_base_url()
										),
										'isExternal' => 'false',
									),
								)
							);
						}
					}

					if ( ( isset( $_GET['module'] ) ) && ( 'statistics' === $_GET['module'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						$header_data['tabs'] = array(
							array(
								'id'      => $screen->id,
								'name'    => esc_html__( 'Statistics', 'ebox' ),
								'actions' => $action_menu,
							),
						);
					} elseif ( ( isset( $_GET['module'] ) ) && ( 'toplist' === $_GET['module'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						$header_data['tabs'] = array(
							array(
								'id'      => $screen->id,
								'name'    => esc_html__( 'Leaderboard', 'ebox' ),
								'actions' => $action_menu,
							),
						);
					}
				} elseif ( ebox_get_post_type_slug( 'course' ) === $screen_post_type ) {
					if ( 'yes' === ebox_Settings_Section::get_section_setting( 'ebox_Settings_Courses_Builder', 'enabled' ) ) {
						$header_data['tabs'] = array_merge(
							$header_data['tabs'],
							array(
								array(
									'id'   => 'ebox_course_builder',
									'name' => esc_html__( 'Builder', 'ebox' ),
								),
							)
						);
					}
					$header_data['tabs'] = array_merge(
						$header_data['tabs'],
						array(
							array(
								'id'                  => $screen_post_type . '-settings',
								'name'                => esc_html__( 'Settings', 'ebox' ),
								'metaboxes'           => array( 'ebox-courses', 'ebox-course-display-content-settings', 'ebox-course-access-settings', 'ebox-course-navigation-settings', 'ebox-course-users-settings', 'ebox-course-grid-meta-box' ),
								'showDocumentSidebar' => 'false',
							),
						)
					);

					if ( ( current_user_can( 'edit_teams' ) ) && ( ebox_get_total_post_count( ebox_get_post_type_slug( 'team' ) ) !== 0 ) ) {
						/**
						 * Filters whether to show course teams metabox or not.
						 *
						 * @since 3.1.0
						 *
						 * @param boolean $show_metabox Whether to show course teams metaboxes or not.
						 */
						if ( true === apply_filters( 'ebox_show_metabox_course_teams', true ) ) {
							$header_data['tabs'] = array_merge(
								$header_data['tabs'],
								array(
									array(
										'id'        => 'ebox_course_teams',
										'name'      => ebox_Custom_Label::get_label( 'teams' ),
										'metaboxes' => array( 'ebox-course-teams' ),
										'showDocumentSidebar' => 'false',
									),
								)
							);
						}
					}
				} elseif ( ebox_get_post_type_slug( 'quiz' ) === $screen_post_type ) {

					if ( ( true === ebox_is_data_upgrade_quiz_questions_updated() ) && ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Quizzes_Builder', 'enabled' ) === 'yes' ) ) {
						$header_data['tabs'] = array_merge(
							$header_data['tabs'],
							array(
								array(
									'id'   => 'ebox_quiz_builder',
									'name' => esc_html__( 'Builder', 'ebox' ),
								),
							)
						);
					}

					$header_data['tabs'] = array_merge(
						$header_data['tabs'],
						array(
							array(
								'id'                  => $screen_post_type . '-settings',
								'name'                => esc_html__( 'Settings', 'ebox' ),
								'metaboxes'           => array( $screen_post_type, 'ebox-quiz-access-settings', 'ebox-quiz-progress-settings', 'ebox-quiz-display-content-settings', 'ebox-quiz-results-options', 'ebox-quiz-admin-data-handling-settings', 'ebox-course-grid-meta-box' ),
								'showDocumentSidebar' => 'false',
							),
						)
					);

					if ( ( true !== ebox_is_data_upgrade_quiz_questions_updated() ) || ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Quizzes_Builder', 'enabled' ) !== 'yes' ) ) {
						$pro_quiz_id = ebox_get_setting( get_the_ID(), 'quiz_pro' );
						if ( ! empty( $pro_quiz_id ) ) {
							$header_data['tabs'] = array_merge(
								$header_data['tabs'],
								array(
									array(
										'id'         => 'ebox_quiz_questions',
										'name'       => esc_html__( 'Questions', 'ebox' ),
										'link'       => add_query_arg(
											array(
												'page'    => 'ldAdvQuiz',
												'module'  => 'question',
												'quiz_id' => $pro_quiz_id,
												'post_id' => absint( $_GET['post'] ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
											),
											admin_url( 'admin.php' )
										),
										'isExternal' => 'true',
									),
								)
							);
						}
					}
					/** This filter is documented in includes/class-ld-semper-fi-module.php */
					if ( apply_filters( 'ebox_settings_metaboxes_legacy_quiz', ebox_SETTINGS_METABOXES_LEGACY_QUIZ, $screen_post_type ) ) {
						$header_data['tabs'] = array_merge(
							$header_data['tabs'],
							array(
								array(
									'id'                  => 'ebox_quiz_advanced_aggregated',
									'name'                => esc_html__( 'Advanced Settings', 'ebox' ),
									'metaboxes'           => array( 'ebox_quiz_advanced_aggregated' ),
									'showDocumentSidebar' => 'false',
								),
							)
						);
					}

					$action_menu = array_merge(
						$action_menu,
						array(
							array(
								'title'      => sprintf(
									// translators: placeholders: Quiz, Questions.
									esc_html_x( 'Reprocess %1$s %2$s', 'placeholders: Quiz, Questions', 'ebox' ),
									ebox_Custom_Label::get_label( 'Quiz' ),
									ebox_Custom_Label::get_label( 'Questions' )
								),
								'link'       => add_query_arg( 'quiz_id', $post->ID, admin_url( 'admin.php?page=ebox_data_upgrades' ) ),
								'isExternal' => 'true',
							),
						)
					);

					if ( current_user_can( 'wpProQuiz_export' ) ) {
						$action_menu = array_merge(
							$action_menu,
							array(
								array(
									'title'      => sprintf(
										// translators: placeholder: Quiz.
										esc_html_x( 'Export %s', 'placeholder: Quiz', 'ebox' ),
										ebox_Custom_Label::get_label( 'quiz' )
									),
									'link'       => add_query_arg(
										array(
											'page'    => 'ldAdvQuiz',
											'quiz_id' => $post->ID,
										),
										admin_url( 'admin.php' )
									),
									'isExternal' => 'true',
								),
							)
						);
					}

					if ( ebox_get_setting( $post->ID, 'statisticsOn' ) ) {
						$action_menu = array_merge(
							$action_menu,
							array(
								array(
									'title'      => esc_html__( 'Statistics', 'ebox' ),
									'link'       => add_query_arg(
										array(
											'module'     => 'statistics',
											'currentTab' => 'statistics',
										),
										$this->get_quiz_base_url()
									),
									'isExternal' => 'false',
								),
							)
						);
					}

					if ( ebox_get_setting( $post->ID, 'toplistActivated' ) ) {
						$action_menu = array_merge(
							$action_menu,
							array(
								array(
									'title'      => esc_html__( 'Leaderboard', 'ebox' ),
									'link'       => add_query_arg(
										array(
											'module'     => 'toplist',
											'currentTab' => 'leaderboard',
										),
										$this->get_quiz_base_url()
									),
									'isExternal' => 'false',
								),
							)
						);
					}
				} elseif ( in_array(
					$screen_post_type,
					array(
						ebox_get_post_type_slug( 'lesson' ),
						ebox_get_post_type_slug( 'topic' ),
						ebox_get_post_type_slug( 'question' ),
						ebox_get_post_type_slug( 'team' ),
						ebox_get_post_type_slug( 'exam' ),
						ebox_get_post_type_slug( LDLMS_Post_Types::COUPON ),
					),
					true
				) ) {
					/* The above code is adding the metaboxes to the post type. */
					$post_settings_metaboxes = array();

					switch ( $screen_post_type ) {
						case ebox_get_post_type_slug( 'lesson' ):
							$post_settings_metaboxes = array_merge(
								$post_settings_metaboxes,
								array(
									$screen_post_type,
									'ebox-lesson-display-content-settings',
									'ebox-lesson-access-settings',
									'ebox-course-grid-meta-box',
								)
							);
							break;

						case ebox_get_post_type_slug( 'topic' ):
							$post_settings_metaboxes = array_merge(
								$post_settings_metaboxes,
								array(
									$screen_post_type,
									'ebox-topic-display-content-settings',
									'ebox-topic-access-settings',
									'ebox-course-grid-meta-box',
								)
							);
							break;

						case ebox_get_post_type_slug( 'question' ):
							if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Quizzes_Builder', 'shared_questions' ) !== 'yes' ) {
								$post_settings_metaboxes = array_merge(
									$post_settings_metaboxes,
									array(
										$screen_post_type,
									)
								);
							}

							if ( ! empty( $header_data['post_data']['builder_post_id'] ) ) {
								$question_pro_id = (int) get_post_meta( $header_data['post_data']['builder_post_id'], 'question_pro_id', true );
								if ( ! empty( $question_pro_id ) ) {
									$question_mapper   = new WpProQuiz_Model_QuestionMapper();
									$pro_question_edit = $question_mapper->fetch( $question_pro_id );
									if ( ( $pro_question_edit ) && is_a( $pro_question_edit, 'WpProQuiz_Model_Question' ) ) {
										$header_data['post_data']['builder_post_title'] = $pro_question_edit->getTitle();
									}
								}
							}

							break;

						case ebox_get_post_type_slug( 'team' ):
							$post_settings_metaboxes = array_merge(
								$post_settings_metaboxes,
								array(
									$screen_post_type,
									'ebox-team-display-content-settings',
									'ebox-team-access-settings',
								)
							);

							/**
							 * Filters whether to show team courses metabox or not.
							 *
							 * @since 3.2.0
							 *
							 * @param boolean $show_metabox Whether to show team courses metaboxes or not.
							 */
							if ( true === apply_filters( 'ebox_show_metabox_team_courses', true ) ) {
								$header_data['tabs'] = array_merge(
									$header_data['tabs'],
									array(
										array(
											'id'        => 'ebox_team_courses',
											'name'      => \ebox_Custom_Label::get_label( 'courses' ),
											'metaboxes' => array( 'ebox_team_courses', 'ebox_team_courses_enroll' ),
											'showDocumentSidebar' => 'false',
										),
									)
								);
							}

							/**
							 * Filters whether to show team users metabox or not.
							 *
							 * @since 3.2.0
							 *
							 * @param boolean $show_metabox Whether to show team users metaboxes or not.
							 */
							if ( true === apply_filters( 'ebox_show_metabox_team_users', true ) ) {
								$header_data['tabs'] = array_merge(
									$header_data['tabs'],
									array(
										array(
											'id'        => 'ebox_team_users',
											'name'      => esc_html__( 'Users', 'ebox' ),
											'metaboxes' => array( 'ebox_team_users', 'ebox_team_leaders' ),
											'showDocumentSidebar' => 'false',
										),
									)
								);
							}

							break;

						case ebox_get_post_type_slug( 'exam' ):
							$post_settings_metaboxes = array_merge(
								$post_settings_metaboxes,
								array(
									$screen_post_type,
									'ebox-exam-display-content-settings',
								)
							);

							break;
					}

					if ( ! empty( $post_settings_metaboxes ) ) {
						$header_data['tabs'] = array_merge(
							$header_data['tabs'],
							array(
								array(
									'id'                  => $screen_post_type . '-settings',
									'name'                => esc_html__( 'Settings', 'ebox' ),
									'metaboxes'           => $post_settings_metaboxes,
									'showDocumentSidebar' => 'false',
								),
							)
						);
					}
				}
			}

			// Reorder tabs Content, Builder, Settings, Anything else.
			if ( ( ! empty( $header_data['tabs'] ) ) && ( in_array( $pagenow, array( 'post.php', 'post-new.php' ), true ) ) ) {
				$header_data_tabs     = array();
				$header_data_tabs_ids = wp_list_pluck( $header_data['tabs'], 'id' );

				foreach ( array( 'post-body-content', 'ebox_course_builder', 'ebox_quiz_builder', $screen_post_type . '-settings' ) as $tab_id ) {

					$index_found = array_search( $tab_id, $header_data_tabs_ids, true );
					if ( false !== $index_found ) {
						$header_data_tabs[] = $header_data['tabs'][ $index_found ];
						unset( $header_data['tabs'][ $index_found ] );
					}
				}

				if ( ! empty( $header_data['tabs'] ) ) {
					$header_data_tabs = array_merge( $header_data_tabs, $header_data['tabs'] );
				}
				$header_data['tabs'] = $header_data_tabs;
			}

			/**
			 * Filters admin settings header action menu.
			 *
			 * @since 3.0.0
			 *
			 * @param array  $action_menu      An array of header action menu items.
			 * @param string $menu_tab_key     Menu tab key.
			 * @param string $screen_post_type Screen post type slug.
			 * @param array  $header_tabs_data An array of header tabs data.
			 */
			$action_menu = apply_filters( 'ebox_header_action_menu', $action_menu, $menu_tab_key, $screen_post_type, $header_data['tabs'] );
			if ( ! empty( $action_menu ) ) {
				if ( ! empty( $header_data['tabs'] ) ) {
					foreach ( $header_data['tabs'] as &$header_menu_item ) {
						$header_menu_item['actions'] = $action_menu;
					}
				}
			}

			/**
			 * Filters the list of header tabs data.
			 *
			 * @since 3.0.0
			 *
			 * @param array  $header_tabs_data An array of header tabs data.
			 * @param string $menu_tab_key     Menu tab key.
			 * @param string $screen_post_type Screen post type slug.
			 */
			$header_data['tabs'] = apply_filters( 'ebox_header_tab_menu', $header_data['tabs'], $menu_tab_key, $screen_post_type );

			if ( 'ebox-courses' === $screen_post_type ) {
				$header_data['posts_per_page'] = \ebox_Settings_Section::get_section_setting( 'ebox_Settings_Courses_Builder', 'per_page' ); // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page
			} elseif ( 'ebox-quiz' === $screen_post_type ) {
				$header_data['posts_per_page'] = \ebox_Settings_Section::get_section_setting( 'ebox_Settings_Quizzes_Builder', 'per_page' ); // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page
			} else {
				$header_data['posts_per_page'] = get_option( 'posts_per_page' ); // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page
			}

			// Load the MO file translations into wp.i18n script hook.
			ebox_load_inline_script_locale_data();

			/**
			 * Filters ebox menu header data.
			 *
			 * May be used to localize dynamic data to eboxData global at front-end.
			 *
			 * @since 3.0.0
			 *
			 * @param array  $header_data    Menu header data.
			 * @param string $menu_tab_key   Menu tab key.
			 * @param array  $admin_tab_sets An array of admin tab sets data.
			 */
			$ebox_data = apply_filters(
				'ebox_header_data',
				$header_data,
				$menu_tab_key,
				$this->admin_tab_sets[ $menu_tab_key ]
			);

			if ( ! empty( $ebox_data ) ) {
				echo '<div id="ebox-header"></div>';

				if ( ( ! empty( $screen_post_type ) ) && ( in_array( $screen_post_type, LDLMS_Post_Types::get_post_types(), true ) ) && ( 'edit-' . $screen_post_type === $screen->id ) ) {
					if ( ebox_get_total_post_count( $screen_post_type ) === 0 ) {

						// If there's an onboarding page, we render it.
						if ( file_exists( ebox_LMS_PLUGIN_DIR . "/includes/admin/onboarding-templates/onboarding-{$screen_post_type}.php" ) ) {
							include_once ebox_LMS_PLUGIN_DIR . "/includes/admin/onboarding-templates/onboarding-{$screen_post_type}.php";
						}
					}
				}

				if ( ! isset( $ebox_assets_loaded['styles']['ebox-new-header-style'] ) ) {
					wp_enqueue_style(
						'ebox-new-header-style',
						ebox_LMS_PLUGIN_URL . 'assets/js/builder/dist/header' . ebox_min_builder_asset() . '.css',
						array(),
						ebox_SCRIPT_VERSION_TOKEN
					);
					wp_style_add_data( 'ebox-new-header-style', 'rtl', 'replace' );
					$ebox_assets_loaded['styles']['ebox-new-header-style'] = __FUNCTION__;
				}

				$css_lesson_label     = \ebox_Custom_Label::get_label( 'lesson' )[0];
				$css_topic_label      = \ebox_Custom_Label::get_label( 'topic' )[0];
				$css_quiz_label       = \ebox_Custom_Label::get_label( 'quiz' )[0];
				$css_question_label   = \ebox_Custom_Label::get_label( 'question' )[0];
				$ebox_custom_css = "
				.ebox_navigation_lesson_topics_list .lesson > a:before,
				#ebox-course-modules h2:before {
					content: '{$css_lesson_label}';
				}
				.ebox_navigation_lesson_topics_list .topic_item > a > span:before,
				#ebox-course-topics h2:before {
					content: '{$css_topic_label}';
				}
				.ebox_navigation_lesson_topics_list .quiz_list_item .lesson > a:before,
				.ebox_navigation_lesson_topics_list .quiz-item > a > span:before,
				#ebox-course-quizzes h2:before {
					content: '{$css_quiz_label}';
				}
				#ebox-quiz-questions h2:before,
				.ld-question-overview-widget-item:before {
					content: '{$css_question_label}';
				}
				";
				wp_add_inline_style( 'ebox-new-header-style', $ebox_custom_css );

				if ( ! isset( $ebox_assets_loaded['scripts']['ebox-new-header-script'] ) ) {
					wp_enqueue_script(
						'ebox-new-header-script',
						ebox_LMS_PLUGIN_URL . 'assets/js/builder/dist/header' . ebox_min_builder_asset() . '.js',
						array( 'wp-i18n' ),
						ebox_SCRIPT_VERSION_TOKEN,
						true
					);
					$ebox_assets_loaded['scripts']['ebox-new-header-script'] = __FUNCTION__;

					wp_localize_script( 'ebox-new-header-script', 'eboxData', $ebox_data );
				}
			}
		}

		/**
		 * Get Quiz base URL
		 *
		 * @since 3.0.0
		 */
		public function get_quiz_base_url() {
			$quiz_post_id = get_the_ID();
			if ( ! $quiz_post_id ) {
				if ( ( isset( $_GET['post'] ) ) && ( ! empty( $_GET['post'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$post_id = absint( $_GET['post'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					if ( get_post_type( $post_id ) === ebox_get_post_type_slug( 'quiz' ) ) {
						$quiz_post_id = $post_id;
					}
				} else {
					if ( ( isset( $_GET['post_id'] ) ) && ( ! empty( $_GET['post_id'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						$post_id = absint( $_GET['post_id'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						if ( get_post_type( $post_id ) === ebox_get_post_type_slug( 'quiz' ) ) {
							$header_data['post_data']['builder_post_id'] = $post_id;
						}
					}
				}
			}

			$quiz_id = 0;
			if ( ! empty( $quiz_post_id ) ) {
				$quiz_id = ebox_get_setting( $quiz_post_id, 'quiz_pro' );
			}

			$url_params = array(
				'page'    => 'ldAdvQuiz',
				'id'      => $quiz_id,
				'post_id' => $quiz_post_id,
				'post'    => $quiz_post_id,
			);

			return add_query_arg( $url_params, admin_url( 'admin.php' ) );
		}
		// End of methods.
	}
}

$ld_admin_menus_tabs = ebox_Admin_Menus_Tabs::get_instance(); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound

/**
 * Add admin tab item interface function
 *
 * @since 2.4.0
 *
 * @param string  $menu_slug     Menu slug.
 * @param array   $menu_item     Menu item. See WP $submenu global.
 * @param integer $menu_priority Tab priority.
 */
function ebox_add_admin_tab_item( $menu_slug, $menu_item, $menu_priority ) {
	ebox_Admin_Menus_Tabs::get_instance()->add_admin_tab_item( $menu_slug, $menu_item, $menu_priority );
}

/**
 * Get current admin tabs set.
 *
 * @since 3.0.0
 *
 * @return array
 */
function ebox_get_current_tabs_set() {
	return ebox_Admin_Menus_Tabs::get_instance()->ebox_admin_tabs();
}
