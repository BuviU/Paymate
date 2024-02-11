<?php
/**
 * ebox cloning utilities
 *
 * Used to cloning ebox custom posts.
 *
 * @since 4.2.0
 * @package ebox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// create a new action scheduler team.
$ldlms_cloning_scheduler = new ebox_Admin_Action_Scheduler( 'cloning' );

// load cloning classes.
require_once ebox_LMS_PLUGIN_DIR . 'includes/admin/classes-cloning/class-ebox-admin-cloning.php';
ebox_Admin_Cloning::init_classes( $ldlms_cloning_scheduler );
