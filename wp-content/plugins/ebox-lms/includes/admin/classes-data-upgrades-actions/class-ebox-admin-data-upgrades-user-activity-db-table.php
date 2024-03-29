<?php
/**
 * ebox Data Upgrades for Database Tables.
 *
 * @since 2.3.0
 * @package ebox\Data_Upgrades
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Admin_Data_Upgrades' ) ) && ( ! class_exists( 'ebox_Admin_Data_Upgrades_User_Activity_DB_Table' ) ) ) {

	/**
	 * Class ebox Data Upgrades for Database Tables.
	 *
	 * @since 2.3.0
	 * @uses ebox_Admin_Data_Upgrades
	 */
	class ebox_Admin_Data_Upgrades_User_Activity_DB_Table extends ebox_Admin_Data_Upgrades {

		/**
		 * Protected constructor for class
		 *
		 * @since 2.3.0
		 */
		protected function __construct() {
			$this->data_slug = 'activity_db-tables';
			parent::__construct();
			add_action( 'init', array( $this, 'upgrade_data_settings' ) );
			parent::register_upgrade_action();
		}

		/**
		 * Update the ebox Settings.
		 * Checks to see if settings needs to be updated.
		 *
		 * @since 2.3.0
		 */
		public function upgrade_data_settings() {
			if ( is_admin() ) {
				$db_version = $this->get_data_settings( 'db_version' );
				if ( ( defined( 'ebox_ACTIVATED' ) ) || ( ( defined( 'ebox_SETTINGS_DB_VERSION' ) ) && ( ebox_SETTINGS_DB_VERSION != '' ) && ( $this->data_settings['db_version'] < ebox_SETTINGS_DB_VERSION ) ) ) { // @phpstan-ignore-line
					$this->upgrade_db_tables( $this->data_settings['db_version'] );
					$this->set_data_settings( 'db_version', ebox_SETTINGS_DB_VERSION );
				}
			}
		}

		/**
		 * Perform DB Tables upgrade.
		 *
		 * @since 2.3.0
		 *
		 * @param string $data_version Current database version we are upgrading from.
		 */
		public function upgrade_db_tables( $data_version = '' ) {
			global $wpdb;

			if ( ! function_exists( 'dbDelta' ) ) {
				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			}

			$charset_collate = '';
			if ( ! empty( $wpdb->charset ) ) {
				$charset_collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			}
			if ( ! empty( $wpdb->collate ) ) {
				$charset_collate .= " COLLATE $wpdb->collate";
			}

			$ebox_user_activity_db_table              = LDLMS_DB::get_table_name( 'user_activity' );
			$ebox_user_activity_db_table_create_query = 'CREATE TABLE ' . $ebox_user_activity_db_table . " (
				activity_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				user_id bigint(20) unsigned NOT NULL DEFAULT '0',
			  	post_id bigint(20) unsigned NOT NULL DEFAULT '0',
				course_id bigint(20) unsigned NOT NULL DEFAULT '0',
			  	activity_type varchar(50) DEFAULT NULL,
				activity_status tinyint(1) unsigned DEFAULT NULL,
			  	activity_started int(11) unsigned DEFAULT NULL,
			  	activity_completed int(11) unsigned DEFAULT NULL,
			  	activity_updated int(11) unsigned DEFAULT NULL,
			  	PRIMARY KEY  (activity_id),
			  	KEY user_id (user_id),
			  	KEY post_id (post_id),
				KEY course_id (course_id),
			  	KEY activity_status (activity_status),
			  	KEY activity_type (activity_type),
			  	KEY activity_started (activity_started),
			  	KEY activity_completed (activity_completed),
			  	KEY activity_updated (activity_updated)
				) " . $charset_collate . ';';
			dbDelta( $ebox_user_activity_db_table_create_query );

			$ebox_user_activity_meta_db_table              = LDLMS_DB::get_table_name( 'user_activity_meta' );
			$ebox_user_activity_meta_db_table_create_query = 'CREATE TABLE ' . $ebox_user_activity_meta_db_table . " (
				activity_meta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				activity_id bigint(20) unsigned NOT NULL DEFAULT '0',
				activity_meta_key varchar(255) DEFAULT NULL,
				activity_meta_value mediumtext,
				PRIMARY KEY  (activity_meta_id),
				KEY activity_id (activity_id),
				KEY activity_meta_key (activity_meta_key(191))
			) " . $charset_collate . ';';
			dbDelta( $ebox_user_activity_meta_db_table_create_query );

			/**
			 * 2.5.0 Patch here to reset the AUTO INCREMENT in the PRIMARY column. Had reports from one ticket
			 * this extra column setting was somehow dropped. After testing found that dbDelta does not re-add this
			 * attribute.
			 */
			$valid_index = LDLMS_DB::check_table_primary_index( 'user_activity' );
			if ( false === $valid_index ) {
				// If the AUTO_INCREMENT attribute is missing we want to also remove any records where the primary index field is zero.
				$wpdb->query( $wpdb->prepare( "DELETE FROM {$ebox_user_activity_db_table} WHERE activity_id = %d", 0 ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$wpdb->query( "ALTER TABLE {$ebox_user_activity_db_table} MODIFY COLUMN activity_id bigint(20) unsigned NOT NULL AUTO_INCREMENT" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.SchemaChange
			}

			$valid_index = LDLMS_DB::check_table_primary_index( 'user_activity_meta' );
			if ( false === $valid_index ) {
				// If the AUTO_INCREMENT attribute is missing we want to also remove any records where the primary index field is zero.
				$wpdb->query( $wpdb->prepare( "DELETE FROM {$ebox_user_activity_meta_db_table} WHERE activity_meta_id = %d", 0 ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$wpdb->query( "ALTER TABLE {$ebox_user_activity_meta_db_table} MODIFY COLUMN activity_meta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.SchemaChange
			}

			// v2.3.0.4 We changed the default from '0' to NULL for the activity_status column.
			$wpdb->query( "ALTER TABLE {$ebox_user_activity_db_table} CHANGE `activity_status` `activity_status` TINYINT(1) UNSIGNED NULL DEFAULT NULL" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.SchemaChange
		}

		// End of functions.
	}
}

add_action(
	'ebox_data_upgrades_init',
	function() {
		ebox_Admin_Data_Upgrades_User_Activity_DB_Table::add_instance();
	}
);
