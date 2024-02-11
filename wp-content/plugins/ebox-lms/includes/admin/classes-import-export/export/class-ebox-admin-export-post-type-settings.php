<?php
/**
 * ebox Admin Export Post Type Settings.
 *
 * @since   4.3.0
 *
 * @package ebox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (
	class_exists( 'ebox_Admin_Export' ) &&
	trait_exists( 'ebox_Admin_Import_Export_Post_Type_Settings' ) &&
	! class_exists( 'ebox_Admin_Export_Post_Type_Settings' )
) {
	/**
	 * Class ebox Admin Export Post Type Settings.
	 *
	 * @since 4.3.0
	 */
	class ebox_Admin_Export_Post_Type_Settings extends ebox_Admin_Export {
		use ebox_Admin_Import_Export_Post_Type_Settings;

		const POST_TYPE_SETTING_SECTIONS = array(
			LDLMS_Post_Types::COURSE      => array(
				'ebox_Settings_Courses_Management_Display',
				'ebox_Settings_Courses_Taxonomies',
				'ebox_Settings_Courses_CPT',
			),
			LDLMS_Post_Types::LESSON      => array(
				'ebox_Settings_modules_Taxonomies',
				'ebox_Settings_modules_CPT',
			),
			LDLMS_Post_Types::TOPIC       => array(
				'ebox_Settings_Topics_Taxonomies',
				'ebox_Settings_Topics_CPT',
			),
			LDLMS_Post_Types::QUIZ        => array(
				'ebox_Settings_Quizzes_Email',
				'ebox_Settings_Quizzes_Management_Display',
				'ebox_Settings_Quizzes_Taxonomies',
				'ebox_Settings_Quizzes_CPT',
			),
			LDLMS_Post_Types::QUESTION    => array(
				'ebox_Settings_Questions_Management_Display',
				'ebox_Settings_Questions_Taxonomies',
			),
			LDLMS_Post_Types::CERTIFICATE => array(
				'ebox_Settings_Certificates_Styles',
				'ebox_Settings_Certificates_CPT',
			),
			LDLMS_Post_Types::GROUP       => array(
				'ebox_Settings_Section_Teams_Team_Leader_User',
				'ebox_Settings_Teams_Membership',
				'ebox_Settings_Teams_Management_Display',
				'ebox_Settings_Teams_Taxonomies',
				'ebox_Settings_Teams_CPT',
			),
			LDLMS_Post_Types::ASSIGNMENT  => array(
				'ebox_Settings_Assignments_CPT',
			),
		);

		/**
		 * Constructor.
		 *
		 * @since 4.3.0
		 * @since 4.5.0   Changed the $logger param to the `ebox_Import_Export_Logger` class.
		 *
		 * @param string                              $post_type    Post type.
		 * @param ebox_Admin_Export_File_Handler $file_handler File Handler class instance.
		 * @param ebox_Import_Export_Logger      $logger       Logger class instance.
		 *
		 * @return void
		 */
		public function __construct(
			string $post_type,
			ebox_Admin_Export_File_Handler $file_handler,
			ebox_Import_Export_Logger $logger
		) {
			$this->post_type = $post_type;

			parent::__construct( $file_handler, $logger );
		}

		/**
		 * Returns the list of settings associated with the post type.
		 *
		 * @since 4.3.0
		 *
		 * @return string
		 */
		public function get_data(): string {
			$section_key = LDLMS_Post_Types::get_post_type_key( $this->post_type );

			if ( empty( $section_key ) ) {
				$section_key = $this->post_type;
			}

			$sections = $this->get_sections( $section_key );

			if ( empty( $sections ) ) {
				return wp_json_encode( array() );
			}

			$result = array();

			foreach ( $sections as $section ) {
				$data = array(
					'name'   => $section,
					'fields' => $section::get_settings_all(),
				);

				/**
				 * Filters the post type settings object to export.
				 *
				 * @since 4.3.0
				 *
				 * @param array $data Settings object.
				 *
				 * @return array Settings object.
				 */
				$data = apply_filters( 'ebox_export_post_type_settings_object', $data );

				$result[] = $data;
			}

			return wp_json_encode( $result );
		}

		/**
		 * Returns post type settings sections.
		 *
		 * @since 4.3.0
		 *
		 * @param string $section_key Section Key.
		 *
		 * @return array
		 */
		protected function get_sections( string $section_key ): array {
			if ( ! array_key_exists( $section_key, self::POST_TYPE_SETTING_SECTIONS ) ) {
				return array();
			}

			/**
			 * Filters the list of post type settings sections to export.
			 *
			 * @since 4.3.0
			 *
			 * @param array $sections Post type settings sections.
			 *
			 * @return array Post type settings sections.
			 */
			$sections = apply_filters(
				'ebox_export_post_type_settings_sections',
				self::POST_TYPE_SETTING_SECTIONS[ $section_key ]
			);

			return array_filter(
				$sections,
				function ( $section ) {
					return is_subclass_of( $section, 'ebox_Settings_Section' );
				}
			);
		}
	}
}
