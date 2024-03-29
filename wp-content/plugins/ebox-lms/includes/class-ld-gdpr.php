<?php
/**
 * ebox class to handle GDPR requirements
 *
 * The following class handles integration with WordPress for new
 * Privacy Policy requirements per GDPR.
 *
 * @package ebox\GDPR
 * @since 2.5.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ebox_GDPR' ) ) {
	/**
	 * Class to handle GDPR
	 */
	class ebox_GDPR {
		const DEFAULT_ERASER_RESULT = array(
			'items_removed'  => 0,
			'items_retained' => 0,
			'messages'       => array(),
			'done'           => true,
		);

		const DEFAULT_EXPORTER_RESULT = array(
			'data' => array(),
			'done' => true,
		);

		/**
		 * Default per_page limit
		 *
		 * @since 2.5.8
		 *
		 * @var int
		 */
		private $per_page_default = 20;

		/**
		 * Class Constructor
		 */
		public function __construct() {
			add_action( 'admin_init', array( $this, 'ebox_add_privacy_policy_text' ) );
			add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'add_exporters' ) );
			add_filter( 'wp_privacy_personal_data_erasers', array( $this, 'add_erasers' ) );
		}

		/**
		 * Adds ebox Privacy Policy text to new WordPress GDPR hooks.
		 *
		 * @since 2.5.8
		 */
		public function ebox_add_privacy_policy_text() {
			if ( is_admin() ) {
				// Check we are on the WP Privacy Policy Guide page.
				$is_privacy_guide = current_user_can( 'manage_privacy_options' );

				if ( $is_privacy_guide ) {
					$pp_readme_file = ebox_LMS_PLUGIN_DIR . 'privacy_policy.txt';

					if ( file_exists( $pp_readme_file ) ) {
						$pp_readme_content = file_get_contents( $pp_readme_file ); // phpcs:ignore

						if ( ! empty( $pp_readme_content ) ) {
							$pp_readme_content = wpautop( stripcslashes( $pp_readme_content ) );
							wp_add_privacy_policy_content(
								'EBox LMS',
								wp_kses_post( wpautop( $pp_readme_content, false ) )
							);
						}
					}
				}
			}
		}

		/**
		 * Adds ebox Exporters to new WordPress GDPR hooks.
		 *
		 * @since 2.5.8
		 *
		 * @param array $exporters Array of Exporters.
		 *
		 * @return array $exporters Array of Exporters.
		 */
		public function add_exporters( array $exporters = array() ): array {
			$exporters['ebox-transactions'] = array(
				'exporter_friendly_name' => esc_html__( 'Ebox LMS Transactions', 'ebox' ),
				'callback'               => array( $this, 'export_transactions' ),
			);

			$exporters['ebox-course-assignments'] = array(
				'exporter_friendly_name' => sprintf(
					// translators: placeholder: Course.
					esc_html_x( 'ebox LMS %s Assignments', 'placeholder: Course', 'ebox' ),
					ebox_Custom_Label::get_label( 'course' )
				),
				'callback'               => array( $this, 'export_course_assignments' ),
			);

			$exporters['ebox-course-essays'] = array(
				'exporter_friendly_name' => sprintf(
					// translators: placeholder: Quiz.
					esc_html_x( 'ebox LMS %s Essays', 'placeholder: Quiz', 'ebox' ),
					ebox_Custom_Label::get_label( 'quiz' )
				),
				'callback'               => array( $this, 'export_quiz_essays' ),
			);

			$exporters['ebox-enrolled-teams'] = array(
				'exporter_friendly_name' => sprintf(
					// translators: placeholder: Teams.
					esc_html_x( 'ebox LMS Enrolled %s', 'placeholder: Teams', 'ebox' ),
					ebox_Custom_Label::get_label( 'teams' )
				),
				'callback'               => array( $this, 'export_enrolled_teams' ),
			);

			$exporters['ebox-enrolled-courses'] = array(
				'exporter_friendly_name' => sprintf(
					// translators: placeholder: Courses.
					esc_html_x( 'ebox LMS Enrolled %s', 'placeholder: Courses', 'ebox' ),
					ebox_Custom_Label::get_label( 'courses' )
				),
				'callback'               => array( $this, 'export_enrolled_courses' ),
			);

			$exporters['ebox-course-certificates'] = array(
				'exporter_friendly_name' => sprintf(
					// translators: placeholder: Course.
					esc_html_x( 'ebox LMS %s Certificates', 'placeholder: Course', 'ebox' ),
					ebox_Custom_Label::get_label( 'course' )
				),
				'callback'               => array( $this, 'export_course_certificates' ),
			);

			$exporters['ebox-quiz-certificates'] = array(
				'exporter_friendly_name' => sprintf(
					// translators: placeholder: Quiz.
					esc_html_x( 'ebox LMS %s Certificates', 'placeholder: Quiz', 'ebox' ),
					ebox_Custom_Label::get_label( 'quiz' )
				),
				'callback'               => array( $this, 'export_quiz_certificates' ),
			);

			$exporters['ebox-team-certificates'] = array(
				'exporter_friendly_name' => sprintf(
					// translators: placeholder: Team.
					esc_html_x( 'ebox LMS %s Certificates', 'placeholder: Team', 'ebox' ),
					ebox_Custom_Label::get_label( 'team' )
				),
				'callback'               => array( $this, 'export_team_certificates' ),
			);

			$exporters['ebox-course-progress'] = array(
				'exporter_friendly_name' => sprintf(
					// translators: placeholder: Course.
					esc_html_x( 'ebox LMS %s Progress', 'placeholder: Course', 'ebox' ),
					ebox_Custom_Label::get_label( 'course' )
				),
				'callback'               => array( $this, 'export_course_progress' ),
			);

			return $exporters;
		}

		/**
		 * Performs Privacy Data Export for Transactions.
		 *
		 * @since 2.5.8
		 *
		 * @param string $email_address Email address of user to export.
		 * @param int    $page Paged number to export.
		 *
		 * @return array
		 */
		public function export_transactions( string $email_address, int $page ): array {
			$user = $this->get_user_by_email( $email_address );

			if ( is_null( $user ) ) {
				return self::DEFAULT_EXPORTER_RESULT;
			}

			/**
			 * Filters value of per page privacy export transactions.
			 *
			 * @param int $per_page_default Per page limit.
			 */
			$number = apply_filters( 'ebox_privacy_export_transactions_per_page', $this->per_page_default );

			$transactions_query_args = array(
				'post_type'      => LDLMS_Post_Types::get_post_type_slug( 'transaction' ),
				'author'         => $user->ID,
				'posts_per_page' => $number,
				'paged'          => $page,
			);

			$transactions_query = new WP_Query( $transactions_query_args );

			if ( empty( $transactions_query->posts ) ) {
				return self::DEFAULT_EXPORTER_RESULT;
			}

			$transaction_to_export = array();

			foreach ( $transactions_query->posts as $transaction ) {
				$transaction_meta_data   = array();
				$transaction_meta_fields = array();

				$transaction_type = get_post_meta( $transaction->ID, 'action', true );

				if ( 'stripe' === $transaction_type ) {
					$transaction_meta_data[] = array(
						'name'  => __( 'Transaction Type', 'ebox' ),
						'value' => __( 'Stripe', 'ebox' ),
					);

					$transaction_meta_fields = array(
						'stripe_name'        => array(
							'label'       => __( 'Order Item', 'ebox' ),
							'format_type' => 'text',
						),
						'stripe_price'       => array(
							'label'       => __( 'Order Total', 'ebox' ),
							'format_type' => 'money_stripe',
						),
						'stripe_token_email' => array(
							'label'       => __( 'Order Email', 'ebox' ),
							'format_type' => 'email',
						),
					);
				}

				if ( empty( $transaction_meta_fields ) ) {
					$transaction_type = get_post_meta( $transaction->ID, 'ipn_track_id', true );
					if ( ! empty( $transaction_type ) ) {

						$transaction_meta_data[] = array(
							'name'  => __( 'Transaction Type', 'ebox' ),
							'value' => __( 'PayPal', 'ebox' ),
						);

						$transaction_meta_fields = array(
							'item_name'   => array(
								'label'       => __( 'Order Item', 'ebox' ),
								'format_type' => 'text',
							),
							'mc_gross'    => array(
								'label'       => __( 'Order Total', 'ebox' ),
								'format_type' => 'money',
							),
							'first_name'  => array(
								'label'       => __( 'First Name', 'ebox' ),
								'format_type' => 'text',
							),
							'last_name'   => array(
								'label'       => __( 'Last Name', 'ebox' ),
								'format_type' => 'text',
							),
							'payer_email' => array(
								'label'       => __( 'Order Email', 'ebox' ),
								'format_type' => 'email',
							),

						);
					}
				}

				if ( empty( $transaction_meta_fields ) ) {
					$transaction_type = get_post_meta( $transaction->ID, 'ebox-checkout', true );

					if ( '2co' === $transaction_type ) {
						$transaction_meta_data[] = array(
							'name'  => __( 'Transaction Type', 'ebox' ),
							'value' => __( '2Checkout', 'ebox' ),
						);

						$transaction_meta_fields = array(
							'invoice_id'       => array(
								'label'       => __( 'Invoice', 'ebox' ),
								'format_type' => 'text',
							),
							'li_0_name'        => array(
								'label'       => __( 'Order Item', 'ebox' ),
								'format_type' => 'text',
							),
							'total'            => array(
								'label'       => __( 'Order Total', 'ebox' ),
								'format_type' => 'money',
							),
							'card_holder_name' => array(
								'label'       => __( 'Cardholder Name', 'ebox' ),
								'format_type' => 'text',
							),

							'first_name'       => array(
								'label'       => __( 'Last Name', 'ebox' ),
								'format_type' => 'text',
							),
							'middle_initial'   => array(
								'label'       => __( 'Middle Initial', 'ebox' ),
								'format_type' => 'text',
							),
							'last_name'        => array(
								'label'       => __( 'Last Name', 'ebox' ),
								'format_type' => 'text',
							),
							'email'            => array(
								'label'       => __( 'Order Email', 'ebox' ),
								'format_type' => 'email',
							),
							'street_address'   => array(
								'label'       => __( 'Street Address', 'ebox' ),
								'format_type' => 'text',
							),
							'street_address2'  => array(
								'label'       => __( 'Street Address', 'ebox' ),
								'format_type' => 'text',
							),
							'city'             => array(
								'label'       => __( 'City', 'ebox' ),
								'format_type' => 'text',
							),
							'state'            => array(
								'label'       => __( 'State', 'ebox' ),
								'format_type' => 'text',
							),
							'zip'              => array(
								'label'       => __( 'Zip', 'ebox' ),
								'format_type' => 'text',
							),
						);
					}
				}

				// SAMCART Transactions.
				if ( empty( $transaction_meta_fields ) ) {
					$order_ip_address = get_post_meta( $transaction->ID, 'order_ip_address', true );

					if ( ! empty( $order_ip_address ) ) {
						$transaction_meta_data[] = array(
							'name'  => __( 'Transaction Type', 'ebox' ),
							'value' => __( 'Samcart', 'ebox' ),
						);

						$transaction_meta_fields = array(
							'customer_email'           => array(
								'label'       => __( 'Order Email', 'ebox' ),
								'format_type' => 'email',
							),
							'customer_first_name'      => array(
								'label'       => __( 'First Name', 'ebox' ),
								'format_type' => 'text',
							),
							'customer_last_name'       => array(
								'label'       => __( 'Last Name', 'ebox' ),
								'format_type' => 'text',
							),
							'customer_phone_number'    => array(
								'label'       => __( 'Phone #', 'ebox' ),
								'format_type' => 'text',
							),
							'order_ip_address'         => array(
								'label'       => __( 'IP Address', 'ebox' ),
								'format_type' => 'ip',
							),
							'customer_billing_address' => array(
								'label'       => __( 'Billing Address', 'ebox' ),
								'format_type' => 'text',
							),
							'customer_billing_city'    => array(
								'label'       => __( 'Billing City', 'ebox' ),
								'format_type' => 'text',
							),
							'customer_billing_state'   => array(
								'label'       => __( 'Billing State', 'ebox' ),
								'format_type' => 'text',
							),
							'customer_billing_zip'     => array(
								'label'       => __( 'Billing ZIP', 'ebox' ),
								'format_type' => 'text',
							),
						);
					}
				}

				if ( ! empty( $transaction_meta_fields ) ) {
					$transaction_meta_data[] = array(
						'name'  => __( 'Order ID', 'ebox' ),
						'value' => $transaction->ID,
					);

					$transaction_meta_data[] = array(
						'name'  => __( 'Order Date', 'ebox' ),
						'value' => ebox_adjust_date_time_display( strtotime( $transaction->post_date ) ),
					);

					foreach ( $transaction_meta_fields as $meta_key => $meta_set ) {
						$meta_value = get_post_meta( $transaction->ID, $meta_key, true );
						if ( ! empty( $meta_value ) ) {
							$transaction_meta_data[] = array(
								'name'  => $meta_set['label'],
								'value' => $this->format_value( $meta_value, $meta_set['format_type'] ),
							);
						}
					}

					$transaction_to_export[] = array(
						'team_id'    => 'ld-transactions',
						'team_label' => __( 'Ebox LMS Purchase Transactions', 'ebox' ),
						'item_id'     => "ld-transactions-{$transaction->ID}",
						'data'        => $transaction_meta_data,
					);
				}
			}

			return array(
				'data' => $transaction_to_export,
				'done' => $page >= $transactions_query->max_num_pages,
			);
		}

		/**
		 * Performs Privacy Data Export for Course Assignments.
		 *
		 * @since 4.1.0
		 *
		 * @param string $email_address Email Address of user to export.
		 * @param int    $page          Page number of export.
		 *
		 * @return array $result
		 */
		public function export_course_assignments( string $email_address, int $page ): array {
			$user = $this->get_user_by_email( $email_address );

			if ( is_null( $user ) ) {
				return self::DEFAULT_EXPORTER_RESULT;
			}

			/**
			 * Filters value of per page export for course assignments.
			 *
			 * @param int $per_page_default Per page limit.
			 */
			$posts_per_page = apply_filters( 'ebox_privacy_export_assignments_per_page', $this->per_page_default );

			$assignments_query_args = array(
				'post_type'      => LDLMS_Post_Types::get_post_type_slug( 'assignment' ),
				'author'         => $user->ID,
				'posts_per_page' => $posts_per_page,
				'paged'          => $page,
			);

			$assignments_query = new WP_Query( $assignments_query_args );

			if ( empty( $assignments_query->posts ) ) {
				return self::DEFAULT_EXPORTER_RESULT;
			}

			$assignments_to_export = array();

			foreach ( $assignments_query->posts as $assignment ) {
				$assignment_meta_data = array();

				$assignment_url         = get_permalink( $assignment->ID );
				$assignment_meta_data[] = array(
					'name'  => __( 'URL', 'ebox' ),
					'value' => $assignment_url,
				);

				$assignment_meta_data[] = array(
					'name'  => __( 'Date', 'ebox' ),
					'value' => ebox_adjust_date_time_display( strtotime( $assignment->post_date ) ),
				);

				$course_id = get_post_meta( $assignment->ID, 'course_id', true );

				if ( ! empty( $course_id ) ) {
					$course_title = get_the_title( $course_id );

					if ( ! empty( $course_title ) ) {
						$assignment_meta_data[] = array(
							'name'  => ebox_Custom_Label::get_label( 'course' ),
							'value' => $course_title,
						);
					}
				}

				$lesson_id = get_post_meta( $assignment->ID, 'lesson_id', true );

				if ( ! empty( $lesson_id ) ) {
					$lesson_title = get_the_title( $lesson_id );

					if ( ! empty( $lesson_title ) ) {
						$assignment_meta_data[] = array(
							'name'  => ebox_Custom_Label::get_label( 'lesson' ),
							'value' => $lesson_title,
						);
					}
				}

				$assignments_to_export[] = array(
					'team_id'    => 'ld-course-assignments',
					// translators: placeholder: Course.
					'team_label' => sprintf( esc_html_x( 'ebox LMS %s Assignments', 'placeholder: Course', 'ebox' ), ebox_Custom_Label::get_label( 'course' ) ),
					'item_id'     => "ld-course-assignments-{$assignment->ID}",
					'data'        => $assignment_meta_data,
				);
			}

			return array(
				'data' => $assignments_to_export,
				'done' => $page >= $assignments_query->max_num_pages,
			);
		}

		/**
		 * Performs Privacy Data Export for Quiz Essays
		 *
		 * @since 2.5.8
		 *
		 * @param string $email_address Email Address of user to export.
		 * @param int    $page          Page number of export.
		 *
		 * @return array $result
		 */
		public function export_quiz_essays( string $email_address, int $page ): array {
			$user = $this->get_user_by_email( $email_address );

			if ( is_null( $user ) ) {
				return self::DEFAULT_EXPORTER_RESULT;
			}

			/**
			 * Filters value of per page export for quiz essays.
			 *
			 * @param int $per_page_default Per page limit.
			 */
			$number = apply_filters( 'ebox_privacy_export_quiz_essays_per_page', $this->per_page_default );

			$essays_query_args = array(
				'post_type'      => LDLMS_Post_Types::get_post_type_slug( 'essay' ),
				'author'         => $user->ID,
				'posts_per_page' => $number,
				'paged'          => $page,
			);

			$essays_query = new WP_Query( $essays_query_args );

			if ( empty( $essays_query->posts ) ) {
				return self::DEFAULT_EXPORTER_RESULT;
			}

			$essays_to_export = array();

			foreach ( $essays_query->posts as $essay ) {
				$essay_meta_data = array();

				$essay_url = get_permalink( $essay->ID );

				if ( ! empty( $essay_url ) ) {
					$essay_meta_data[] = array(
						'name'  => __( 'URL', 'ebox' ),
						'value' => $essay_url,
					);
				}

				$essay_meta_data[] = array(
					'name'  => __( 'Date', 'ebox' ),
					'value' => ebox_adjust_date_time_display( strtotime( $essay->post_date ) ),
				);

				$course_id = get_post_meta( $essay->ID, 'course_id', true );

				if ( ! empty( $course_id ) ) {
					$course_title = get_the_title( $course_id );

					if ( ! empty( $course_title ) ) {
						$essay_meta_data[] = array(
							'name'  => ebox_Custom_Label::get_label( 'course' ),
							'value' => $course_title,
						);
					}
				}

				$lesson_id = get_post_meta( $essay->ID, 'lesson_id', true );

				if ( ! empty( $lesson_id ) ) {
					$lesson_title = get_the_title( $lesson_id );

					if ( ! empty( $lesson_title ) ) {
						$essay_meta_data[] = array(
							'name'  => ebox_Custom_Label::get_label( 'lesson' ),
							'value' => $lesson_title,
						);
					}
				}

				$essays_to_export[] = array(
					'team_id'    => 'ld-quiz-essays',
					// translators: placeholder: Quiz.
					'team_label' => sprintf( esc_html_x( 'ebox LMS %s Essays', 'placeholder: Quiz', 'ebox' ), ebox_Custom_Label::get_label( 'quiz' ) ),
					'item_id'     => "ld-quiz-essays-{$essay->ID}",
					'data'        => $essay_meta_data,
				);
			}

			return array(
				'data' => $essays_to_export,
				'done' => $page >= $essays_query->max_num_pages,
			);
		}

		/**
		 * Performs Privacy Data Export for Enrolled Teams.
		 *
		 * @since 4.1.0
		 *
		 * @param string $email_address Email Address of user to export.
		 * @param int    $page          Page number of export.
		 *
		 * @return array
		 */
		public function export_enrolled_teams( string $email_address, int $page ): array {
			$user = $this->get_user_by_email( $email_address );

			if ( is_null( $user ) ) {
				return self::DEFAULT_EXPORTER_RESULT;
			}

			$team_ids = ebox_get_users_team_ids( $user->ID, true );

			if ( empty( $team_ids ) ) {
				return self::DEFAULT_EXPORTER_RESULT;
			}

			/**
			 * Filters value of per page export for enrolled teams.
			 *
			 * @param int $per_page_default Per page limit.
			 */
			$posts_per_page = apply_filters(
				'ebox_privacy_export_enrolled_teams_per_page',
				$this->per_page_default
			);

			$query = new WP_Query(
				array(
					'post_type'      => LDLMS_Post_Types::get_post_type_slug( 'team' ),
					'post__in'       => $team_ids,
					'posts_per_page' => $posts_per_page,
					'paged'          => $page,
				)
			);

			if ( empty( $query->posts ) ) {
				return self::DEFAULT_EXPORTER_RESULT;
			}

			$data = array();

			foreach ( $query->posts as $post ) {
				$data[] = array(
					'team_id'    => 'ebox-enrolled-teams',
					'team_label' => sprintf(
						// translators: placeholder: Teams.
						esc_html_x( 'ebox LMS Enrolled %s', 'placeholder: Teams', 'ebox' ),
						ebox_Custom_Label::get_label( 'teams' )
					),
					'item_id'     => "ebox-enrolled-teams-{$post->ID}",
					'data'        => array(
						array(
							'name'  => ebox_Custom_Label::get_label( 'team' ),
							'value' => $post->post_title,
						),
						array(
							'name'  => __( 'URL', 'ebox' ),
							'value' => get_permalink( $post->ID ),
						),
					),
				);
			}

			return array(
				'data' => $data,
				'done' => $page >= $query->max_num_pages,
			);
		}

		/**
		 * Performs Privacy Data Export for Enrolled Courses.
		 *
		 * @since 4.1.0
		 *
		 * @param string $email_address Email Address of user to export.
		 * @param int    $page          Page number of export.
		 *
		 * @return array
		 */
		public function export_enrolled_courses( string $email_address, int $page ): array {
			$user = $this->get_user_by_email( $email_address );

			if ( is_null( $user ) ) {
				return self::DEFAULT_EXPORTER_RESULT;
			}

			$course_ids = ebox_user_get_enrolled_courses( $user->ID, array(), true );

			if ( empty( $course_ids ) ) {
				return self::DEFAULT_EXPORTER_RESULT;
			}

			/**
			 * Filters value of per page export for enrolled courses.
			 *
			 * @param int $per_page_default Per page limit.
			 */
			$posts_per_page = apply_filters(
				'ebox_privacy_export_enrolled_courses_per_page',
				$this->per_page_default
			);

			$query = new WP_Query(
				array(
					'post_type'      => LDLMS_Post_Types::get_post_type_slug( 'course' ),
					'post__in'       => $course_ids,
					'posts_per_page' => $posts_per_page,
					'paged'          => $page,
				)
			);

			if ( empty( $query->posts ) ) {
				return self::DEFAULT_EXPORTER_RESULT;
			}

			$data = array();

			foreach ( $query->posts as $post ) {
				$item = array(
					array(
						'name'  => ebox_Custom_Label::get_label( 'course' ),
						'value' => $post->post_title,
					),
					array(
						'name'  => __( 'URL', 'ebox' ),
						'value' => get_permalink( $post->ID ),
					),
				);

				$user_course_access_time = get_user_meta(
					$user->ID,
					"course_{$post->ID}_access_from",
					true
				);

				if ( ! empty( $user_course_access_time ) ) {
					$item[] = array(
						'name'  => __( 'Access From Date', 'ebox' ),
						'value' => ebox_adjust_date_time_display( $user_course_access_time ),
					);
				}

				$data[] = array(
					'team_id'    => 'ebox-enrolled-courses',
					'team_label' => sprintf(
						// translators: placeholder: Courses.
						esc_html_x( 'ebox LMS Enrolled %s', 'placeholder: Courses', 'ebox' ),
						ebox_Custom_Label::get_label( 'courses' )
					),
					'item_id'     => "ebox-enrolled-courses-{$post->ID}",
					'data'        => $item,
				);
			}

			return array(
				'data' => $data,
				'done' => $page >= $query->max_num_pages,
			);
		}

		/**
		 * Exports Course Certificates.
		 *
		 * @since 4.1.0
		 *
		 * @param string $email_address Email Address of user to export.
		 * @param int    $page          Page number of export.
		 *
		 * @return array
		 */
		public function export_course_certificates( string $email_address, int $page ): array {
			$user = $this->get_user_by_email( $email_address );

			if ( is_null( $user ) ) {
				return self::DEFAULT_EXPORTER_RESULT;
			}

			$course_ids = ebox_user_get_enrolled_courses( $user->ID, array(), true );

			if ( empty( $course_ids ) ) {
				return self::DEFAULT_EXPORTER_RESULT;
			}

			/**
			 * Filters value of per page export for course certificates.
			 *
			 * @param int $per_page_default Per page limit.
			 */
			$posts_per_page = apply_filters(
				'ebox_privacy_export_course_certificates_per_page',
				$this->per_page_default
			);

			$query = new WP_Query(
				array(
					'post_type'      => LDLMS_Post_Types::get_post_type_slug( 'course' ),
					'post__in'       => $course_ids,
					'posts_per_page' => $posts_per_page,
					'paged'          => $page,
				)
			);

			if ( empty( $query->posts ) ) {
				return self::DEFAULT_EXPORTER_RESULT;
			}

			$data = array();

			foreach ( $query->posts as $post ) {
				$certificate_link = ebox_get_course_certificate_link( $post->ID, $user->ID );

				if ( empty( $certificate_link ) ) {
					continue;
				}

				$data[] = array(
					'team_id'    => 'ebox-course-certificates',
					'team_label' => sprintf(
						// translators: placeholder: Course.
						esc_html_x( 'ebox LMS %s Certificates', 'placeholder: Course', 'ebox' ),
						ebox_Custom_Label::get_label( 'course' )
					),
					'item_id'     => "ebox-course-certificates-{$post->ID}",
					'data'        => array(
						array(
							'name'  => ebox_Custom_Label::get_label( 'course' ),
							'value' => $post->post_title,
						),
						array(
							'name'  => __( 'URL', 'ebox' ),
							'value' => $certificate_link,
						),
					),
				);
			}

			return array(
				'data' => $data,
				'done' => $page >= $query->max_num_pages,
			);
		}

		/**
		 * Exports Quiz Certificates.
		 *
		 * @since 4.1.0
		 *
		 * @param string $email_address Email Address of user to export.
		 * @param int    $page          Page number of export.
		 *
		 * @return array
		 */
		public function export_quiz_certificates( string $email_address, int $page ): array {
			$user = $this->get_user_by_email( $email_address );

			if ( is_null( $user ) ) {
				return self::DEFAULT_EXPORTER_RESULT;
			}

			$quizzes = get_user_meta( $user->ID, '_ebox-quizzes', true );

			if ( empty( $quizzes ) ) {
				return self::DEFAULT_EXPORTER_RESULT;
			}

			$quiz_ids = array_keys( array_column( $quizzes, 'quiz', 'quiz' ) );

			/**
			 * Filters value of per page export for quiz certificates.
			 *
			 * @param int $per_page_default Per page limit.
			 */
			$posts_per_page = apply_filters(
				'ebox_privacy_export_quiz_certificates_per_page',
				$this->per_page_default
			);

			$query = new WP_Query(
				array(
					'post_type'      => LDLMS_Post_Types::get_post_type_slug( 'quiz' ),
					'post__in'       => $quiz_ids,
					'posts_per_page' => $posts_per_page,
					'paged'          => $page,
				)
			);

			if ( empty( $query->posts ) ) {
				return self::DEFAULT_EXPORTER_RESULT;
			}

			$data = array();

			foreach ( $query->posts as $post ) {
				$certificate_link = ebox_get_certificate_link( $post->ID, $user->ID );

				if ( empty( $certificate_link ) ) {
					continue;
				}

				$data[] = array(
					'team_id'    => 'ebox-quiz-certificates',
					'team_label' => sprintf(
						// translators: placeholder: Quiz.
						esc_html_x( 'ebox LMS %s Certificates', 'placeholder: Quiz', 'ebox' ),
						ebox_Custom_Label::get_label( 'quiz' )
					),
					'item_id'     => "ebox-quiz-certificates-{$post->ID}",
					'data'        => array(
						array(
							'name'  => ebox_Custom_Label::get_label( 'quiz' ),
							'value' => $post->post_title,
						),
						array(
							'name'  => __( 'URL', 'ebox' ),
							'value' => $certificate_link,
						),
					),
				);
			}

			return array(
				'data' => $data,
				'done' => $page >= $query->max_num_pages,
			);
		}

		/**
		 * Exports Team Certificates.
		 *
		 * @since 4.1.0
		 *
		 * @param string $email_address Email Address of user to export.
		 * @param int    $page          Page number of export.
		 *
		 * @return array
		 */
		public function export_team_certificates( string $email_address, int $page ): array {
			$user = $this->get_user_by_email( $email_address );

			if ( is_null( $user ) ) {
				return self::DEFAULT_EXPORTER_RESULT;
			}

			$team_ids = ebox_get_users_team_ids( $user->ID, true );

			if ( empty( $team_ids ) ) {
				return self::DEFAULT_EXPORTER_RESULT;
			}

			/**
			 * Filters value of per page export for team certificates.
			 *
			 * @param int $per_page_default Per page limit.
			 */
			$posts_per_page = apply_filters(
				'ebox_privacy_export_team_certificates_per_page',
				$this->per_page_default
			);

			$query = new WP_Query(
				array(
					'post_type'      => LDLMS_Post_Types::get_post_type_slug( 'team' ),
					'post__in'       => $team_ids,
					'posts_per_page' => $posts_per_page,
					'paged'          => $page,
				)
			);

			if ( empty( $query->posts ) ) {
				return self::DEFAULT_EXPORTER_RESULT;
			}

			$data = array();

			foreach ( $query->posts as $post ) {
				$certificate_link = ebox_get_team_certificate_link( $post->ID, $user->ID );

				if ( empty( $certificate_link ) ) {
					continue;
				}

				$data[] = array(
					'team_id'    => 'ebox-team-certificates',
					'team_label' => sprintf(
						// translators: placeholder: Team.
						esc_html_x( 'ebox LMS %s Certificates', 'placeholder: Team', 'ebox' ),
						ebox_Custom_Label::get_label( 'team' )
					),
					'item_id'     => "ebox-team-certificates-{$post->ID}",
					'data'        => array(
						array(
							'name'  => ebox_Custom_Label::get_label( 'team' ),
							'value' => $post->post_title,
						),
						array(
							'name'  => __( 'URL', 'ebox' ),
							'value' => $certificate_link,
						),
					),
				);
			}

			return array(
				'data' => $data,
				'done' => $page >= $query->max_num_pages,
			);
		}

		/**
		 * Exports Course Progress.
		 *
		 * @since 4.1.0
		 *
		 * @param string $email_address Email Address of user to export.
		 * @param int    $page          Page number of export.
		 *
		 * @return array
		 */
		public function export_course_progress( string $email_address, int $page ): array {
			$user = $this->get_user_by_email( $email_address );

			if ( is_null( $user ) ) {
				return self::DEFAULT_EXPORTER_RESULT;
			}

			/**
			 * Filters value of per page export for team certificates.
			 *
			 * @param int $per_page_default Per page limit.
			 */
			$posts_per_page = apply_filters(
				'ebox_privacy_export_course_progress_per_page',
				$this->per_page_default
			);

			$courses_registered_all = ld_get_mycourses( $user->ID );

			$courses_registered_query_args = array(
				'post_type'      => 'ebox-courses',
				'fields'         => 'ids',
				'post__in'       => $courses_registered_all,
				'posts_per_page' => $posts_per_page,
				'paged'          => $page,
			);

			$usermeta        = get_user_meta( $user->ID, '_ebox-course_progress', true );
			$course_progress = empty( $usermeta ) ? array() : $usermeta;

			$course_progress_ids = array_merge( $courses_registered_all, array_keys( $course_progress ) );
			$course_progress_ids = array_diff( $course_progress_ids, ebox_get_expired_user_courses_from_meta( $user->ID ) );

			$course_progress_query_args = array(
				'post_type'      => 'ebox-courses',
				'fields'         => 'ids',
				'post__in'       => $course_progress_ids,
				'posts_per_page' => $posts_per_page,
				'paged'          => $page,
			);

			$course_progress_query = new WP_Query( $course_progress_query_args );

			$course_p        = $course_progress;
			$course_progress = array();
			foreach ( $course_progress_query->posts as $course_id ) {
				if ( isset( $course_p[ $course_id ] ) ) {
					$course_progress[ $course_id ] = $course_p[ $course_id ];
				} else {
					$course_progress[ $course_id ] = array();
				}
			}

			if ( empty( $course_progress ) ) {
				return self::DEFAULT_EXPORTER_RESULT;
			}

			$data = array();

			foreach ( $course_progress as $course_id => $coursep ) {
				$progress_summary = ebox_user_get_course_progress( $user->ID, $course_id, 'summary' );
				$status           = ebox_course_status_label( $progress_summary['status'] );
				$completed        = ( ! empty( $coursep['completed'] ) ? $coursep['completed'] : '0' );

				$modules = array();

				if ( ! empty( $coursep['modules'] ) ) {
					foreach ( $coursep['modules'] as $lesson_id => $status ) {
						$title = sprintf(
							// translators: placeholder: Lesson.
							esc_html_x( '%s: ', 'placeholder: Lesson', 'ebox' ),
							ebox_Custom_Label::get_label( 'lesson' )
						) . get_the_title( $lesson_id );
						$status = ( true === (bool) $status ? 'Completed' : 'Not Completed' );

						$modules[] = array(
							'name'  => $title,
							'value' => $status,
						);
					}
				}

				$topics = array();

				if ( ! empty( $course_progress[ $course_id ]['topics'] ) ) {
					foreach ( $course_progress[ $course_id ]['topics'] as $lesson_id ) {
						foreach ( $lesson_id as $topic_id => $status ) {
							$title = sprintf(
								// translators: placeholder: Topic.
								esc_html_x( '%s: ', 'placeholder: Topic', 'ebox' ),
								ebox_Custom_Label::get_label( 'topic' )
							) . get_the_title( $topic_id );
							$status = ( true === (bool) $status ? 'Completed' : 'Not Completed' );

							$topics[] = array(
								'name'  => $title,
								'value' => $status,
							);
						}
					}
				}

				$data[]                    = array(
					'team_id'    => 'ebox-course-progress',
					'team_label' => sprintf(
						// translators: placeholder: Course.
						esc_html_x( 'ebox LMS %s Progress', 'placeholder: Course', 'ebox' ),
						ebox_Custom_Label::get_label( 'course' )
					),
					'item_id'     => "ebox-course-progress-{$course_id}",
					'data'        => array(
						array(
							'name'  => ebox_Custom_Label::get_label( 'course' ),
							'value' => get_the_title( $course_id ),
						),
						array(
							'name'  => sprintf(
								// translators: placeholder: Course.
								esc_html_x( '%s ID', 'placeholder: Course', 'ebox' ),
								ebox_Custom_Label::get_label( 'course' )
							),
							'value' => absint( $course_id ),
						),
						array(
							'name'  => __( 'URL', 'ebox' ),
							'value' => get_permalink( $course_id ),
						),
						array(
							'name'  => __( 'Status', 'ebox' ),
							'value' => $status,
						),
						array(
							'name'  => __( 'Steps Completed', 'ebox' ),
							'value' => $progress_summary['completed'] . ' / ' . $progress_summary['total'],
						),
					),
				);
				$data_idx                  = count( $data ) - 1;
				$steps_progress            = array_merge( $modules, $topics );
				$data[ $data_idx ]['data'] = array_merge( $data[ $data_idx ]['data'], $steps_progress );

			}

			return array(
				'data' => $data,
				'done' => $page >= $course_progress_query->max_num_pages,
			);
		}

		/**
		 * Add ebox as an Eraser package for WordPress data.
		 *
		 * @since 2.5.8
		 *
		 * @param array $erasers Array of registered erasers.
		 *
		 * @return array $erasers Array of registered erasers
		 */
		public function add_erasers( array $erasers = array() ): array {
			$erasers[] = array(
				'eraser_friendly_name' => esc_html__( 'ebox LMS Transactions', 'ebox' ),
				'callback'             => array( $this, 'erase_transactions' ),
			);

			$erasers[] = array(
				'eraser_friendly_name' => sprintf(
					// translators: placeholder: Teams.
					esc_html_x( 'ebox LMS Enrolled %s', 'placeholder: Teams', 'ebox' ),
					ebox_Custom_Label::get_label( 'teams' )
				),
				'callback'             => array( $this, 'erase_enrolled_teams' ),
			);

			$erasers[] = array(
				'eraser_friendly_name' => sprintf(
					// translators: placeholder: Courses.
					esc_html_x( 'ebox LMS Enrolled %s', 'placeholder: Courses', 'ebox' ),
					ebox_Custom_Label::get_label( 'courses' )
				),
				'callback'             => array( $this, 'erase_enrolled_courses' ),
			);

			$erasers[] = array(
				'eraser_friendly_name' => esc_html__( 'ebox LMS User Progress', 'ebox' ),
				'callback'             => array( $this, 'erase_user_progress' ),
			);

			return $erasers;
		}

		/**
		 * Performs data eraser.
		 *
		 * Called by WordPress when performing data cleanup for specific user by email. This
		 * function makes users data contained in transaction generated via PayPal and Stripe anonymous.
		 *
		 * @since 2.5.8
		 *
		 * @param string $email_address Email of WP User to perform cleanup on.
		 * @param int    $page Page number or actions to perform. This is controlled by
		 * the function below. See the $number variable.
		 *
		 * @return array
		 */
		public function erase_transactions( string $email_address, int $page = 1 ): array {
			global $wpdb;

			$user = $this->get_user_by_email( $email_address );

			if ( is_null( $user ) ) {
				return self::DEFAULT_ERASER_RESULT;
			}

			/**
			 * Filters value of per page erase transactions.
			 *
			 * @param int $per_page_default Per page limit.
			 */
			$posts_per_page = apply_filters( 'ebox_privacy_transactions_erase', $this->per_page_default );

			$transactions_query_args = array(
				'post_type'      => LDLMS_Post_Types::get_post_type_slug( 'transaction' ),
				'author'         => $user->ID,
				'posts_per_page' => $posts_per_page,
				'paged'          => $page,
			);

			$transactions_query = new WP_Query( $transactions_query_args );

			if ( empty( $transactions_query->posts ) ) {
				return self::DEFAULT_ERASER_RESULT;
			}

			$result = self::DEFAULT_ERASER_RESULT;

			$deleted_email = wp_privacy_anonymize_data( 'email' );
			$deleted_text  = wp_privacy_anonymize_data( 'text' );
			$deleted_ip    = wp_privacy_anonymize_data( 'ip' );

			foreach ( $transactions_query->posts as $transaction ) {
				$transaction_meta_fields = array();

				$transaction->post_title = str_ireplace( $email_address, $deleted_email, $transaction->post_title );

				$updated = $wpdb->update( // phpcs:ignore
					$wpdb->posts,
					array(
						'post_title' => $transaction->post_title,
					),
					array(
						'ID' => $transaction->ID,
					),
					array( '%s' ),
					array( '%d' )
				);

				if ( false !== $updated ) {
					$result['items_removed'] += 1;

					// STRIPE Transactions.
					$transaction_type = get_post_meta( $transaction->ID, 'action', true );

					if ( 'stripe' === $transaction_type ) {
						$transaction_meta_fields = array(
							'stripe_token_email' => array(
								'format_type' => 'email',
							),
							'stripe_email'       => array(
								'format_type' => 'email',
							),
						);
					}

					// PAYPAL Transactions.
					if ( empty( $transaction_meta_fields ) ) {
						$transaction_type = get_post_meta( $transaction->ID, 'ipn_track_id', true );

						if ( ! empty( $transaction_type ) ) {
							$transaction_meta_fields = array(
								'first_name'  => array(
									'format_type' => 'text',
								),
								'last_name'   => array(
									'format_type' => 'text',
								),
								'payer_email' => array(
									'format_type' => 'email',
								),
							);
						}
					}

					// 2CHECKOUT Transactions
					if ( empty( $transaction_meta_fields ) ) {
						$transaction_type = get_post_meta( $transaction->ID, 'ebox-checkout', true );

						if ( '2co' === $transaction_type ) {
							$transaction_meta_fields = array(
								'first_name'       => array(
									'format_type' => 'text',
								),
								'middle_initial'   => array(
									'format_type' => 'text',
								),
								'last_name'        => array(
									'format_type' => 'text',
								),
								'email'            => array(
									'format_type' => 'email',
								),
								'street_address'   => array(
									'format_type' => 'text',
								),
								'street_address2'  => array(
									'format_type' => 'text',
								),
								'city'             => array(
									'format_type' => 'text',
								),
								'state'            => array(
									'format_type' => 'text',
								),
								'zip'              => array(
									'format_type' => 'text',
								),
								'card_holder_name' => array(
									'format_type' => 'text',
								),
							);
						}
					}

					// SAMCART Transactions.
					if ( empty( $transaction_meta_fields ) ) {
						$order_ip_address = get_post_meta( $transaction->ID, 'order_ip_address', true );

						if ( ! empty( $order_ip_address ) ) {
							$transaction_meta_fields = array(
								'customer_email'           => array(
									'format_type' => 'email',
								),
								'customer_first_name'      => array(
									'format_type' => 'text',
								),
								'customer_last_name'       => array(
									'format_type' => 'text',
								),
								'customer_phone_number'    => array(
									'format_type' => 'text',
								),
								'order_ip_address'         => array(
									'format_type' => 'ip',
								),
								'customer_billing_address' => array(
									'format_type' => 'text',
								),
								'customer_billing_city'    => array(
									'format_type' => 'text',
								),
								'customer_billing_state'   => array(
									'format_type' => 'text',
								),
								'customer_billing_zip'     => array(
									'format_type' => 'text',
								),
							);
						}
					}
				}

				if ( ! empty( $transaction_meta_fields ) ) {
					foreach ( $transaction_meta_fields as $meta_key => $meta_set ) {
						$meta_value = get_post_meta( $transaction->ID, $meta_key, true );

						if ( ! empty( $meta_value ) ) {
							switch ( $meta_set['format_type'] ) {
								case 'email':
									$meta_value_after = str_ireplace( $meta_value, $deleted_email, $meta_value );
									break;

								case 'ip':
									$meta_value_after = str_ireplace( $meta_value, $deleted_ip, $meta_value );
									break;

								default:
									$meta_value_after = str_ireplace( $meta_value, $deleted_text, $meta_value );
									break;
							}

							if ( $meta_value_after !== $meta_value ) {
								update_post_meta( $transaction->ID, $meta_key, $meta_value_after );
							}
						}
					}
				}
			}

			// $return_data['done'] is set to true by default.
			// If we not have reached the max_number_pages then we are not done.
			$result['done'] = $page >= $transactions_query->max_num_pages;

			return $result;
		}

		/**
		 * Performs data eraser.
		 *
		 * Called by WordPress when performing data cleanup for a specific user by email. This
		 * function removes a user from all teams.
		 *
		 * @since 4.1.0
		 *
		 * @param string $email_address Email of WP User to perform cleanup on.
		 *
		 * @return array
		 */
		public function erase_enrolled_teams( string $email_address ): array {
			$user = $this->get_user_by_email( $email_address );

			if ( is_null( $user ) ) {
				return self::DEFAULT_ERASER_RESULT;
			}

			$team_ids = ebox_get_users_team_ids( $user->ID, true );

			if ( empty( $team_ids ) ) {
				return self::DEFAULT_ERASER_RESULT;
			}

			$result = self::DEFAULT_ERASER_RESULT;

			foreach ( $team_ids as $team_id ) {
				$removed = ld_update_team_access( $user->ID, $team_id, true );

				if ( $removed ) {
					$result['items_removed'] += 1;
				}
			}

			$result['done'] = count( $team_ids ) === $result['items_removed'];

			return $result;
		}

		/**
		 * Performs data eraser.
		 *
		 * Called by WordPress when performing data cleanup for a specific user by email. This
		 * function removes a user from all courses.
		 *
		 * @since 4.1.0
		 *
		 * @param string $email_address Email of WP User to perform cleanup on.
		 *
		 * @return array $result
		 */
		public function erase_enrolled_courses( string $email_address ): array {
			$user = $this->get_user_by_email( $email_address );

			if ( is_null( $user ) ) {
				return self::DEFAULT_ERASER_RESULT;
			}

			$course_ids = ebox_user_get_enrolled_courses( $user->ID, array(), true );

			if ( empty( $course_ids ) ) {
				return self::DEFAULT_ERASER_RESULT;
			}

			$result = self::DEFAULT_ERASER_RESULT;

			foreach ( $course_ids as $course_id ) {
				ld_update_course_access( $user->ID, $course_id, true );
			}

			$result['items_removed'] = count( $course_ids );

			return $result;
		}

		/**
		 * Perform data eraser.
		 *
		 * Called by WordPress when performing data cleanup for a specific user by email. This
		 * function removes all user's course and quiz progress, assignments and essays.
		 *
		 * @since 4.1.0
		 *
		 * @param string $email_address Email of WP User to perform cleanup on.
		 *
		 * @return array $result
		 */
		public function erase_user_progress( string $email_address ): array {
			$result = self::DEFAULT_ERASER_RESULT;

			$email = trim( $email_address );

			if ( empty( $email ) ) {
				return $result;
			}

			$user = get_user_by( 'email', $email );

			if ( ! $user ) {
				return $result;
			}

			ebox_delete_user_data( $user->ID );

			return $result;
		}

		/**
		 * Formats the output value based on variable type.
		 *
		 * @since 2.5.8
		 *
		 * @param mixed  $meta_value The meta value for reformat.
		 * @param string $meta_type Will be the type of the meta_value. test, date, money etc.
		 *
		 * @return mixed $meta_value
		 */
		protected function format_value( $meta_value, string $meta_type ) {
			if ( empty( $meta_value ) || empty( $meta_type ) ) {
				return $meta_value;
			}

			switch ( $meta_type ) {
				case 'money_stripe':
					$meta_value = $meta_value / 100;
					// no break.

				case 'money':
					$meta_value = number_format_i18n( $meta_value, 2 );
					break;

				case 'date_string':
					$meta_value = strtotime( $meta_value );
					// no break.

				case 'date_number':
					$meta_value = ebox_adjust_date_time_display( $meta_value );
					break;

				default:
					break;
			}

			return $meta_value;
		}

		/**
		 * Finds user by email.
		 *
		 * @since 4.1.0
		 *
		 * @param string $email User Email.
		 *
		 * @return WP_User|null
		 */
		protected function get_user_by_email( string $email ): ?WP_User {
			$email = trim( $email );

			if ( empty( $email ) ) {
				return null;
			}

			$user = get_user_by( 'email', $email );

			if ( ! $user ) {
				return null;
			}

			return $user;
		}
	}
}

new ebox_GDPR();
