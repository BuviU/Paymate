<?php
/**
 * ebox logger.
 *
 * @since 4.5.0
 *
 * @package ebox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const ebox_LOGGERS_PATH = ebox_LMS_PLUGIN_DIR . 'includes/loggers/';
require_once ebox_LOGGERS_PATH . 'class-ebox-logger.php';

// Requires all loggers. Please don't forget to create an instance of the loggers below, if needed.
require_once ebox_LOGGERS_PATH . 'class-ebox-transaction-logger.php';
require_once ebox_LOGGERS_PATH . 'class-ebox-import-export-logger.php';

ebox_Logger::init_log_directory();

add_action(
	'init',
	function () {
		/**
		 * Filters the list of loggers.
		 *
		 * @since 4.5.0
		 *
		 * @param ebox_Logger[] $loggers List of logger instances.
		 *
		 * @return ebox_Logger[] List of logger instances.
		 */
		foreach ( apply_filters( 'ebox_loggers', array() ) as $logger ) {
			if ( ! $logger instanceof ebox_Logger ) {
				continue;
			}

			$logger->init();
		}
	}
);
