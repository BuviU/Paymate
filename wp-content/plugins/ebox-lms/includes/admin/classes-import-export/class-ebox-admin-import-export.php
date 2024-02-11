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

if ( ! class_exists( 'ebox_Admin_Import_Export' ) ) {
	/**
	 * Class ebox Admin Import/Export.
	 *
	 * @since 4.3.0
	 */
	class ebox_Admin_Import_Export {
		const SCHEDULER_EXPORT_GROUP_NAME = 'export';
		const SCHEDULER_IMPORT_GROUP_NAME = 'import';

		const EXPORT_PATH = ebox_LMS_PLUGIN_DIR . 'includes/admin/classes-import-export/export/';
		const IMPORT_PATH = ebox_LMS_PLUGIN_DIR . 'includes/admin/classes-import-export/import/';
		const COMMON_PATH = ebox_LMS_PLUGIN_DIR . 'includes/admin/classes-import-export/common/';

		/**
		 * Inits.
		 *
		 * @since 4.3.0
		 *
		 * @return void
		 */
		public static function init(): void {
			self::init_common_classes();
			self::init_import_classes();
			self::init_export_classes();
		}

		/**
		 * Inits utils classes.
		 *
		 * @since 4.3.0
		 *
		 * @return void
		 */
		protected static function init_common_classes():void {
			require_once self::COMMON_PATH . 'class-ebox-admin-import-export-handler.php';
			require_once self::COMMON_PATH . 'class-ebox-admin-import-export-file-handler.php';
			require_once self::COMMON_PATH . 'trait-ebox-admin-import-export-post-type-settings.php';
			require_once self::COMMON_PATH . 'trait-ebox-admin-import-export-settings.php';
			require_once self::COMMON_PATH . 'trait-ebox-admin-import-export-taxonomies.php';
			require_once self::COMMON_PATH . 'trait-ebox-admin-import-export-posts.php';
			require_once self::COMMON_PATH . 'trait-ebox-admin-import-export-proquiz.php';
			require_once self::COMMON_PATH . 'trait-ebox-admin-import-export-pages.php';
			require_once self::COMMON_PATH . 'trait-ebox-admin-import-export-users.php';
			require_once self::COMMON_PATH . 'trait-ebox-admin-import-export-user-activity.php';
			require_once self::COMMON_PATH . 'trait-ebox-admin-import-export-media.php';
			require_once self::COMMON_PATH . 'trait-ebox-admin-import-export-utils.php';
		}

		/**
		 * Inits import classes.
		 *
		 * @since 4.3.0
		 *
		 * @return void
		 */
		protected static function init_import_classes(): void {
			require_once self::IMPORT_PATH . 'class-ebox-admin-import-mapper.php';
			require_once self::IMPORT_PATH . 'class-ebox-admin-import-handler.php';
			require_once self::IMPORT_PATH . 'class-ebox-admin-import-file-handler.php';
			require_once self::IMPORT_PATH . 'class-ebox-admin-import.php';
			require_once self::IMPORT_PATH . 'class-ebox-admin-import-post-type-settings.php';
			require_once self::IMPORT_PATH . 'class-ebox-admin-import-settings.php';
			require_once self::IMPORT_PATH . 'class-ebox-admin-import-taxonomies.php';
			require_once self::IMPORT_PATH . 'class-ebox-admin-import-posts.php';
			require_once self::IMPORT_PATH . 'class-ebox-admin-import-proquiz.php';
			require_once self::IMPORT_PATH . 'class-ebox-admin-import-proquiz-statistics.php';
			require_once self::IMPORT_PATH . 'class-ebox-admin-import-pages.php';
			require_once self::IMPORT_PATH . 'class-ebox-admin-import-users.php';
			require_once self::IMPORT_PATH . 'class-ebox-admin-import-user-activity.php';
			require_once self::IMPORT_PATH . 'class-ebox-admin-import-media.php';
			require_once self::IMPORT_PATH . 'class-ebox-admin-import-associations-handler.php';

			$import_logger = new ebox_Import_Export_Logger( ebox_Import_Export_Logger::$log_type_import );
			add_filter(
				'ebox_loggers',
				function( array $loggers ) use ( $import_logger ): array {
					$loggers[] = $import_logger;

					return $loggers;
				}
			);

			new ebox_Admin_Import_Handler(
				new ebox_Admin_Import_File_Handler(),
				new ebox_Admin_Action_Scheduler( self::SCHEDULER_IMPORT_GROUP_NAME ),
				$import_logger
			);
		}

		/**
		 * Inits export classes.
		 *
		 * @since 4.3.0
		 *
		 * @return void
		 */
		protected static function init_export_classes(): void {
			require_once self::EXPORT_PATH . 'interface-ebox-admin-export-has-media.php';
			require_once self::EXPORT_PATH . 'class-ebox-admin-export-mapper.php';
			require_once self::EXPORT_PATH . 'class-ebox-admin-export-handler.php';
			require_once self::EXPORT_PATH . 'class-ebox-admin-export-file-handler.php';
			require_once self::EXPORT_PATH . 'class-ebox-admin-export.php';
			require_once self::EXPORT_PATH . 'class-ebox-admin-export-chunkable.php';
			require_once self::EXPORT_PATH . 'class-ebox-admin-export-configuration.php';
			require_once self::EXPORT_PATH . 'class-ebox-admin-export-taxonomies.php';
			require_once self::EXPORT_PATH . 'class-ebox-admin-export-post-type-settings.php';
			require_once self::EXPORT_PATH . 'class-ebox-admin-export-settings.php';
			require_once self::EXPORT_PATH . 'class-ebox-admin-export-taxonomies.php';
			require_once self::EXPORT_PATH . 'class-ebox-admin-export-posts.php';
			require_once self::EXPORT_PATH . 'class-ebox-admin-export-proquiz.php';
			require_once self::EXPORT_PATH . 'class-ebox-admin-export-pages.php';
			require_once self::EXPORT_PATH . 'class-ebox-admin-export-users.php';
			require_once self::EXPORT_PATH . 'class-ebox-admin-export-user-activity.php';

			$export_logger = new ebox_Import_Export_Logger( ebox_Import_Export_Logger::$log_type_export );
			add_filter(
				'ebox_loggers',
				function( array $loggers ) use ( $export_logger ): array {
					$loggers[] = $export_logger;

					return $loggers;
				}
			);

			new ebox_Admin_Export_Handler(
				new ebox_Admin_Export_File_Handler(),
				new ebox_Admin_Action_Scheduler( self::SCHEDULER_EXPORT_GROUP_NAME ),
				$export_logger
			);
		}
	}
}
