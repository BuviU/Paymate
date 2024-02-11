<?php
/**
 * ebox Admin Import/Export.
 *
 * @since 4.3.0
 *
 * @package ebox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! trait_exists( 'ebox_Admin_Import_Export_Utils' ) ) {
	/**
	 * Trait ebox Admin Import/Export.
	 *
	 * @since 4.3.0
	 */
	trait ebox_Admin_Import_Export_Utils {
		/**
		 * Returns Gutenberg blocks that support media.
		 *
		 * @since 4.3.0
		 *
		 * @return array
		 */
		protected function get_media_blocks(): array {
			return array(
				'core/audio'       => 'id',
				'core/cover'       => 'id',
				'core/cover-image' => 'id',
				'core/file'        => 'id',
				'core/image'       => 'id',
				'core/media-text'  => 'mediaId',
				'core/video'       => 'id',
			);
		}

	}
}
