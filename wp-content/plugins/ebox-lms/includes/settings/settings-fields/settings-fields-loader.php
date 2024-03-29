<?php
/**
 * ebox Settings Fields Loader.
 *
 * @since 3.0.0
 * @package ebox\Settings\Field
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// All known LD setting field type (for now).
require_once __DIR__ . '/class-ld-settings-fields-text.php';
require_once __DIR__ . '/class-ld-settings-fields-email.php';
require_once __DIR__ . '/class-ld-settings-fields-html.php';
require_once __DIR__ . '/class-ld-settings-fields-number.php';
require_once __DIR__ . '/class-ld-settings-fields-hidden.php';
require_once __DIR__ . '/class-ld-settings-fields-checkbox.php';
require_once __DIR__ . '/class-ld-settings-fields-radio.php';
require_once __DIR__ . '/class-ld-settings-fields-textarea.php';
require_once __DIR__ . '/class-ld-settings-fields-select.php';
require_once __DIR__ . '/class-ld-settings-fields-multiselect.php';
require_once __DIR__ . '/class-ld-settings-fields-wpeditor.php';
require_once __DIR__ . '/class-ld-settings-fields-colorpicker.php';
require_once __DIR__ . '/class-ld-settings-fields-media-upload.php';
require_once __DIR__ . '/class-ld-settings-fields-url.php';
require_once __DIR__ . '/class-ld-settings-fields-checkbox-switch.php';

// Specialty fields.
require_once __DIR__ . '/class-ld-settings-fields-custom.php';
require_once __DIR__ . '/class-ld-settings-fields-date-entry.php';
require_once __DIR__ . '/class-ld-settings-fields-timer-entry.php';

require_once __DIR__ . '/class-ld-settings-fields-select-edit-delete.php';
require_once __DIR__ . '/class-ld-settings-fields-quiz-custom-fields.php';
require_once __DIR__ . '/class-ld-settings-fields-quiz-templates-load.php';
require_once __DIR__ . '/class-ld-settings-fields-quiz-templates-save.php';
