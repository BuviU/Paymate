<?php
/**
 * ebox Admin Export Has Media Interface.
 *
 * @since 4.3.0
 *
 * @package ebox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

interface ebox_Admin_Export_Has_Media {
	/**
	 * Returns media IDs.
	 *
	 * @since 4.3.0
	 *
	 * @return int[]
	 */
	public function get_media(): array;
}
