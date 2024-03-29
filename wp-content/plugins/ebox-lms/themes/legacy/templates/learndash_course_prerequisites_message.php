<?php
/**
 * Displays the Prerequisites
 *
 * Available Variables:
 * $current_post : (WP_Post Object) Current Post object being display. Equal to global $post in most cases.
 * $prerequisite_post : (WP_Post Object) Post object needed to be taken prior to $current_post
 * $prerequisite_posts_all : (WP_Post Object) Post object needed to be taken prior to $current_post
 * $content_type : (string) Will contain the singlar lowercase common label 'course', 'lesson', 'topic', 'quiz'
 * $course_settings : (array) Settings specific to current course
 *
 * @since 2.2.1.2
 *
 * @package ebox\Templates\Legacy\Course
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_links = '';
$i          = 0;
if ( ! empty( $prerequisite_posts_all ) ) {
	foreach ( $prerequisite_posts_all as $pre_post_id => $pre_status ) {
		if ( false === (bool) $pre_status ) {
			$i++;
			if ( ! empty( $post_links ) ) {
				$post_links .= ', ';
			}
			$post_links .= '<a href="' . esc_url( get_the_permalink( $pre_post_id ) ) . '">' . wp_kses_post( get_the_title( $pre_post_id ) ) . '</a>';
		}
	}
}
?>
<div id="ebox_complete_prerequisites">
<?php

	$course_prereq_compare = ebox_get_setting( $current_post, 'course_prerequisite_compare' );

	if ( 'ANY' === $course_prereq_compare && $i > 1 ) {

		echo sprintf(
			// translators: placeholders: course, courses.
			esc_html_x(
				'To take this %1$s, you need to complete any of the following %2$s first:',
				'placeholders: course, courses',
				'ebox'
			),
			$content_type,
			esc_html( ebox_get_custom_label_lower( 'courses' ) )
		);

	} else {

		echo sprintf(
			// translators: placeholders: (1) course singular, (2) course or courses.
			esc_html_x(
				'To take this %1$s, you need to complete the following %2$s first:',
				'placeholders: (1) course singular, (2) course or courses',
				'ebox'
			),
			$content_type,
			esc_html( _n( ebox_get_custom_label_lower( 'course' ), ebox_get_custom_label_lower( 'courses' ), $i, 'ebox' ) ) // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralSingle, WordPress.WP.I18n.NonSingularStringLiteralPlural
		);

	}

	echo '<br>';

	$post_links = '';
	if ( ! empty( $prerequisite_posts_all ) ) {
		foreach ( $prerequisite_posts_all as $pre_post_id => $pre_status ) {
			if ( $pre_status === false ) {
				if ( ! empty( $post_links ) ) {
					$post_links .= ', ';
				}
				$post_links .= '<a href="' . get_the_permalink( $pre_post_id ) . '">' . get_the_title( $pre_post_id ) . '</a>';
			}
		}
	}
	if ( ! empty( $post_links ) ) {
		echo $post_links;
	}
	?>
	</div>
