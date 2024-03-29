<?php
/**
 * Abstract class for LDLMS_Model.
 *
 * @since 3.2.0
 * @package ebox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'LDLMS_Model' ) ) {
	/**
	 * Class for ebox LDLMS_Model.
	 *
	 * @since 3.2.0
	 */
	abstract class LDLMS_Model {

		/**
		 * Private constructor for class.
		 *
		 * @since 3.3.0
		 */
		private function __construct() {}
	}
}
