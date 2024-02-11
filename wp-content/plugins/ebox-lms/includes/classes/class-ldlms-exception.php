<?php
/**
 * Class to extend Exception to LDLMS_Exception.
 *
 * @package ebox\Exception
 * @since 3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; }

// phpcs:ignore Generic.Files.OneObjectStructurePerFile.MultipleFound, Squiz.Commenting.ClassComment.Missing
class LDLMS_Exception extends Exception {}

// phpcs:ignore Generic.Files.OneObjectStructurePerFile.MultipleFound, Squiz.Commenting.ClassComment.Missing
class LDLMS_Exception_NotFound extends LDLMS_Exception {}
