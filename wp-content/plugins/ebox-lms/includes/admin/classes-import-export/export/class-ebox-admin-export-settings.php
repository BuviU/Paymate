<?php
/**
 * ebox Admin Export Settings.
 *
 * @since 4.3.0
 *
 * @package ebox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (
	class_exists( 'ebox_Admin_Export' ) &&
	trait_exists( 'ebox_Admin_Import_Export_Settings' ) &&
	interface_exists( 'ebox_Admin_Export_Has_Media' ) &&
	! class_exists( 'ebox_Admin_Export_Settings' )
) {
	/**
	 * Class ebox Admin Export Settings.
	 *
	 * @since 4.3.0
	 */
	class ebox_Admin_Export_Settings extends ebox_Admin_Export implements ebox_Admin_Export_Has_Media {
		use ebox_Admin_Import_Export_Settings;

		/**
		 * Returns the list of LD settings.
		 *
		 * @since 4.3.0
		 *
		 * @return string
		 */
		public function get_data(): string {
			$result = array();

			foreach ( $this->get_sections() as $section ) {
				$fields = $section::get_settings_all();

				if ( empty( $fields ) ) {
					continue;
				}

				$data = array(
					'name'   => $section,
					'fields' => $fields,
				);

				/**
				 * Filters the settings object to export.
				 *
				 * @since 4.3.0
				 *
				 * @param array $data Settings object.
				 *
				 * @return array Settings object.
				 */
				$data = apply_filters( 'ebox_export_settings_object', $data );

				$result[] = $data;
			}

			return wp_json_encode( $result );
		}

		/**
		 * Returns media IDs.
		 *
		 * @since 4.3.0
		 *
		 * @return array
		 */
		public function get_media(): array {
			$media_ids = array();

			foreach ( $this->get_fields_with_media_id() as $section_name => $field_names ) {
				foreach ( $field_names as $field_name ) {
					$media_ids[] = ebox_Settings_Section::get_section_setting( $section_name, $field_name );
				}
			}

			foreach ( $this->get_fields_containing_media_urls() as $section_name => $field_names ) {
				foreach ( $field_names as $field_name ) {
					$media_ids = array_merge(
						$media_ids,
						$this->get_media_ids_from_string(
							ebox_Settings_Section::get_section_setting( $section_name, $field_name )
						)
					);
				}
			}

			/**
			 * Filters the settings media ids to export.
			 *
			 * @since 4.3.0
			 *
			 * @param array $media_ids Settings media ids.
			 *
			 * @return array Settings media ids.
			 */
			return apply_filters(
				'ebox_export_settings_media_ids',
				array_values( array_filter( $media_ids ) )
			);
		}

		/**
		 * Returns settings sections.
		 *
		 * @since 4.3.0
		 *
		 * @return array
		 */
		private function get_sections(): array {
			$sections = array();

			foreach ( ebox_Settings_Page::get_global_settings_page_names() as $page ) {
				$sections = array_merge(
					$sections,
					array_map(
						function( ebox_Settings_Section $section ) {
							return get_class( $section );
						},
						ebox_Settings_Page::get_page_instance( $page )->get_settings_sections()
					)
				);
			}

			/**
			 * Filters the list of settings sections to export.
			 *
			 * @since 4.3.0
			 *
			 * @param array $sections Settings sections.
			 *
			 * @return array Settings sections.
			 */
			$sections = apply_filters( 'ebox_export_settings_sections', $sections );

			return array_filter(
				$sections,
				function ( $section ) {
					return is_subclass_of( $section, 'ebox_Settings_Section' );
				}
			);
		}
	}
}
