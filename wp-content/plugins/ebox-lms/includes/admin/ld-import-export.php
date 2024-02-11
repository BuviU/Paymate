<?php
/**
 * ebox import/export utilities
 *
 * @since 4.3.0
 *
 * @package ebox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// load import/export classes.
require_once ebox_LMS_PLUGIN_DIR . 'includes/admin/classes-import-export/class-ebox-admin-import-export.php';
ebox_Admin_Import_Export::init();
