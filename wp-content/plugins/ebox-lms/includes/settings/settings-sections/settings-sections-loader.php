<?php
/**
 * ebox Settings Sections Loader.
 *
 * @since 3.0.0
 * @package ebox\Settings\Sections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/class-ld-settings-section-side-submit.php';
require_once __DIR__ . '/class-ld-settings-section-side-quick-links.php';

// Course Options.
require_once __DIR__ . '/class-ld-settings-section-courses-management-display.php';
require_once __DIR__ . '/class-ld-settings-section-courses-taxonomies.php';
require_once __DIR__ . '/class-ld-settings-section-courses-cpt.php';

// modules Options.
require_once __DIR__ . '/class-ld-settings-section-modules-taxonomies.php';
require_once __DIR__ . '/class-ld-settings-section-modules-cpt.php';

// Topics Options.
require_once __DIR__ . '/class-ld-settings-section-topics-taxonomies.php';
require_once __DIR__ . '/class-ld-settings-section-topics-cpt.php';

// Quizzes Options.
require_once __DIR__ . '/class-ld-settings-section-quizzes-management-display.php';
require_once __DIR__ . '/class-ld-settings-section-quizzes-email-settings.php';
require_once __DIR__ . '/class-ld-settings-section-quizzes-taxonomies.php';
require_once __DIR__ . '/class-ld-settings-section-quizzes-cpt.php';

// Question Options.
require_once __DIR__ . '/class-ld-settings-section-questions-taxonomies.php';
require_once __DIR__ . '/class-ld-settings-section-questions-management-display.php';

// Certificate Options.
require_once __DIR__ . '/class-ld-settings-section-certificates-cpt.php';
require_once __DIR__ . '/class-ld-settings-section-certificates-styles.php';

// Teams.
require_once __DIR__ . '/class-ld-settings-section-teams-team-leader-user.php';
require_once __DIR__ . '/class-ld-settings-section-teams-management-display.php';
require_once __DIR__ . '/class-ld-settings-section-teams-cpt.php';
require_once __DIR__ . '/class-ld-settings-section-teams-taxonomies.php';
require_once __DIR__ . '/class-ld-settings-section-teams-membership.php';


// Settings General tab.
require_once __DIR__ . '/class-ld-settings-section-courses-themes.php';
require_once __DIR__ . '/class-ld-settings-section-general-per-page.php';
require_once __DIR__ . '/class-ld-settings-section-general-admin-user.php';

// Registration.
require_once __DIR__ . '/class-ld-settings-section-registration-fields.php';
require_once __DIR__ . '/class-ld-settings-section-registration-pages.php';

// Emails.
require_once __DIR__ . '/class-ld-settings-section-emails-list.php';
require_once __DIR__ . '/class-ld-settings-section-emails-sender-settings.php';

// Emails sub-sections.
require_once __DIR__ . '/settings-sections-emails/class-ld-settings-section-emails-new-user-registration.php';
require_once __DIR__ . '/settings-sections-emails/class-ld-settings-section-emails-course-purchase-success.php';
require_once __DIR__ . '/settings-sections-emails/class-ld-settings-section-emails-team-purchase-success.php';
require_once __DIR__ . '/settings-sections-emails/class-ld-settings-section-emails-purchase-invoice.php';

// Payments tab.
require_once __DIR__ . '/class-ld-settings-section-payments-list.php';
require_once __DIR__ . '/class-ld-settings-section-payments-defaults.php';

// Payments sub-sections.
require_once __DIR__ . '/settings-sections-payments/class-ld-settings-section-paypal.php';
require_once __DIR__ . '/settings-sections-payments/class-ld-settings-section-stripe-connect.php';
require_once __DIR__ . '/settings-sections-payments/class-ld-settings-section-razorpay.php';

// Support tab.
require_once __DIR__ . '/class-ld-settings-section-support-ebox.php';
require_once __DIR__ . '/class-ld-settings-section-support-server.php';
require_once __DIR__ . '/class-ld-settings-section-support-wordpress.php';
require_once __DIR__ . '/class-ld-settings-section-support-templates.php';
require_once __DIR__ . '/class-ld-settings-section-support-database-tables.php';
require_once __DIR__ . '/class-ld-settings-section-support-wordpress-themes.php';
require_once __DIR__ . '/class-ld-settings-section-support-wordpress-plugins.php';
require_once __DIR__ . '/class-ld-settings-section-support-copy-system-info.php';
require_once __DIR__ . '/class-ld-settings-section-support-data-reset.php';

// Translations tab.
if ( ( defined( 'ebox_TRANSLATIONS' ) ) && ( ebox_TRANSLATIONS === true ) ) {
	require_once __DIR__ . '/class-ld-settings-section-translations-refresh.php';
	require_once __DIR__ . '/class-ld-settings-section-translations-ebox.php';
}

// Advanced Page ( 3.6.0).
require_once __DIR__ . '/class-ld-settings-section-custom-labels.php';
require_once __DIR__ . '/class-ld-settings-section-bulk-edit.php';
require_once __DIR__ . '/class-ld-settings-section-data-upgrades.php';
require_once __DIR__ . '/class-ld-settings-section-import-export.php';
require_once __DIR__ . '/class-ld-settings-section-logs.php';
if ( ( defined( 'ebox_REST_API_ENABLED' ) ) && ( true === ebox_REST_API_ENABLED ) ) {
	require_once __DIR__ . '/class-ld-settings-section-general-rest-api.php';
}
require_once __DIR__ . '/class-ld-settings-section-telemetry.php';

// Assignments.
require_once __DIR__ . '/class-ld-settings-section-assignments-cpt.php';

// Shows settings section on the WP Settings > Permalinks page.
require_once __DIR__ . '/class-ld-settings-section-permalinks.php';
require_once __DIR__ . '/class-ld-settings-section-permalinks-taxonomies.php';
