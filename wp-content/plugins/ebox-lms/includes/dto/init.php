<?php
/**
 * ebox DTO.
 *
 * @since 4.5.0
 *
 * @package ebox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Basic validation.
require_once ebox_LMS_PLUGIN_DIR . 'includes/dto/validation/interface-ebox-dto-property-validator.php';
require_once ebox_LMS_PLUGIN_DIR . 'includes/dto/validation/class-ebox-dto-property-validation-result.php';
require_once ebox_LMS_PLUGIN_DIR . 'includes/dto/validation/class-ebox-dto-validation-exception.php';

// Basic DTO.
require_once ebox_LMS_PLUGIN_DIR . 'includes/dto/class-ebox-dto.php';

// Validators.
require_once ebox_LMS_PLUGIN_DIR . 'includes/dto/validation/class-ebox-dto-property-validator-string-case.php';
require_once ebox_LMS_PLUGIN_DIR . 'includes/dto/validation/class-ebox-dto-property-validator-possible-values.php';

// DTOs.
require_once ebox_LMS_PLUGIN_DIR . 'includes/dto/class-ebox-pricing-dto.php';
require_once ebox_LMS_PLUGIN_DIR . 'includes/dto/class-ebox-coupon-dto.php';
require_once ebox_LMS_PLUGIN_DIR . 'includes/dto/class-ebox-transaction-meta-dto.php';
require_once ebox_LMS_PLUGIN_DIR . 'includes/dto/class-ebox-transaction-coupon-dto.php';
require_once ebox_LMS_PLUGIN_DIR . 'includes/dto/class-ebox-transaction-gateway-transaction-dto.php';
