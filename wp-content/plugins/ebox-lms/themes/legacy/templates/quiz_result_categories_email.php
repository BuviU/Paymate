<?php
/**
 * Displays Quiz Result Categories per Question teams.
 *
 * Available Variables:
 *
 * $question_categories : Array of the Quiz Question Categories with id and name.
 * $category_scores : Array breakdown of scores per category.
 *
 * @since 2.6.0
 *
 * @package ebox\Templates\Legacy\Quiz
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$category_output = esc_html__( 'Categories', 'ebox' ) . ":\n";
foreach ( $category_scores as $cat_id => $score ) {
	if ( ! isset( $question_categories[ $cat_id ] ) ) {
		continue;
	}

	$category_output .= '* ' . str_pad( $question_categories[ $cat_id ], 35, '.' ) . ( (float) $score ) . "%\n";
}

echo $category_output;
