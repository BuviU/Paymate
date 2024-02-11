<?php
/**
 * ebox certificates (certificate) Posts Listing Class.
 *
 * @package ebox\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Admin_Posts_Listing' ) ) && ( ! class_exists( 'ebox_Admin_Certificates_Listing' ) ) ) {
	/**
	 * Class for ebox Certificates Listing Pages.
	 */
	class ebox_Admin_Certificates_Listing extends ebox_Admin_Posts_Listing {

		/**
		 * Public constructor for class
		 */
		public function __construct() {
			$this->post_type = ebox_get_post_type_slug( 'certificate' );

			parent::__construct();
		}

		/**
		 * Called via the WordPress init action hook.
		 */
		public function listing_init() {
			if ( $this->listing_init_done ) {
				return;
			}
			$this->selectors = array();

			$this->columns = array(
				'certificate_teams_courses_quizzes' => array(
					'label'   => esc_html__( 'Used in', 'ebox' ),
					'display' => array( $this, 'show_column_certificate_teams_courses_quizzes' ),
					'after'   => 'title',
				),
			);

			parent::listing_init();

			$this->listing_init_done = true;
		}

		/**
		 * Call via the WordPress load sequence for admin pages.
		 */
		public function on_load_listing() {
			if ( $this->post_type_check() ) {
				parent::on_load_listing();

				if ( isset( $this->columns['certificate_teams_courses_quizzes'] ) ) {
					if ( ( ! current_user_can( 'edit_teams' ) ) && ( ! current_user_can( 'edit_courses' ) ) ) {
						unset( $this->columns['certificate_teams_courses_quizzes'] );
					} elseif ( ( ! ebox_post_meta_processed( ebox_get_post_type_slug( 'course' ) ) ) && ( ! ebox_post_meta_processed( ebox_get_post_type_slug( 'quiz' ) ) ) && ( ! ebox_post_meta_processed( ebox_get_post_type_slug( 'team' ) ) ) ) {
						unset( $this->columns['certificate_teams_courses_quizzes'] );
					}
				}
			}
		}

		/**
		 * Show Team Course Users column.
		 *
		 * @since 3.4.1
		 *
		 * @param int   $post_id     The Step post ID shown.
		 * @param array $column_meta Array of column meta information.
		 */
		protected function show_column_certificate_teams_courses_quizzes( $post_id = 0, $column_meta = array() ) {
			if ( ! empty( $post_id ) ) {

				if ( current_user_can( 'edit_courses' ) ) {
					if ( ebox_post_meta_processed( ebox_get_post_type_slug( 'course' ) ) ) {
						$cert_sets = ebox_certificate_get_used_by( $post_id, ebox_get_post_type_slug( 'course' ) );
						if ( ! empty( $cert_sets ) ) {
							$filter_url = add_query_arg(
								array(
									'post_type'      => ebox_get_post_type_slug( 'course' ),
									'certificate_id' => $post_id,
								),
								admin_url( 'edit.php' )
							);

							$link_aria_label = sprintf(
								// translators: placeholder: Courses, Certificate.
								esc_html_x( 'Filter %1$s by Certificate "%2$s"', 'placeholder: Courses, Certificate Post title', 'ebox' ),
								ebox_Custom_Label::get_label( 'courses' ),
								get_the_title( $post_id )
							);

							echo sprintf(
								// translators: placeholder: Courses, Certificate Courses Count.
								esc_html_x( '%1$s: %2$s', 'placeholder: Courses, Certificate Courses Count', 'ebox' ),
								esc_attr( ebox_get_custom_label( 'courses' ) ),
								'<a href="' . esc_url( $filter_url ) . '" aria-label="' . esc_attr( $link_aria_label ) . '">' . count( $cert_sets ) . '</a>'
							);
							echo '<br />';
						}
					}

					if ( ebox_post_meta_processed( ebox_get_post_type_slug( 'course' ) ) ) {
						$cert_sets = ebox_certificate_get_used_by( $post_id, ebox_get_post_type_slug( 'quiz' ) );
						if ( ! empty( $cert_sets ) ) {
							$filter_url = add_query_arg(
								array(
									'post_type'      => ebox_get_post_type_slug( 'quiz' ),
									'certificate_id' => $post_id,
								),
								admin_url( 'edit.php' )
							);

							$link_aria_label = sprintf(
								// translators: placeholder: Quizzes, Certificate Post title.
								esc_html_x( 'Filter %1$s by Certificate "%2$s"', 'placeholder: Quizzes, Certificate Post title', 'ebox' ),
								ebox_Custom_Label::get_label( 'quizzes' ),
								get_the_title( $post_id )
							);

							echo sprintf(
								// translators: placeholder: Quizzes, Certificate Quizzes Count.
								esc_html_x( '%1$s: %2$s', 'placeholder: Quizzes, Certificate Quizzes Count', 'ebox' ),
								esc_attr( ebox_get_custom_label( 'quizzes' ) ),
								'<a href="' . esc_url( $filter_url ) . '" aria-label="' . esc_attr( $link_aria_label ) . '">' . count( $cert_sets ) . '</a>'
							);
							echo '<br />';
						}
					}
				}

				if ( current_user_can( 'edit_teams' ) ) {
					if ( ebox_post_meta_processed( ebox_get_post_type_slug( 'team' ) ) ) {
						$cert_sets = ebox_certificate_get_used_by( $post_id, ebox_get_post_type_slug( 'team' ) );
						if ( ! empty( $cert_sets ) ) {

							$filter_url = add_query_arg(
								array(
									'post_type'      => ebox_get_post_type_slug( 'team' ),
									'certificate_id' => $post_id,
								),
								admin_url( 'edit.php' )
							);

							$link_aria_label = sprintf(
								// translators: placeholder: Teams, Certificate Post title.
								esc_html_x( 'Filter %1$s by Certificate "%2$s"', 'placeholder: Teams, Certificate Post title', 'ebox' ),
								ebox_Custom_Label::get_label( 'teams' ),
								get_the_title( $post_id )
							);

							echo sprintf(
								// translators: placeholder: Teams, Certificate Teams Count.
								esc_html_x( '%1$s: %2$s', 'placeholder: Teams, Certificate Teams Count', 'ebox' ),
								esc_attr( ebox_get_custom_label( 'teams' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
								'<a href="' . esc_url( $filter_url ) . '" aria-label="' . esc_attr( $link_aria_label ) . '">' . count( $cert_sets ) . '</a>'
							);
						}
					}
				}
			}
		}

		// End of functions.
	}
}
new ebox_Admin_Certificates_Listing();
