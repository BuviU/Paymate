<?php
/**
 * This file contains the code that displays the course list.
 *
 * @since 2.1.0
 *
 * @package ebox\Templates\Legacy\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php
if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Teams_CPT', 'public' ) !== 'yes' && $shortcode_atts['post_type'] === 'teams' ) {
	the_title( '<h2 class="ld-entry-title entry-title">', '</h2>' );
} else {
	the_title( '<h2 class="ld-entry-title entry-title"><a href="' . ebox_get_step_permalink( get_the_ID(), $course_id ) . '" title="' . the_title_attribute( 'echo=0' ) . '" rel="bookmark">', '</a></h2>' );
}
?>

<div class="ld-entry-content entry-content">
	<?php
	if ( ( isset( $shortcode_atts['show_thumbnail'] ) ) && ( 'true' == $shortcode_atts['show_thumbnail'] ) ) {
		the_post_thumbnail();
	}
	?>
	<?php
	if ( ( isset( $shortcode_atts['show_content'] ) ) && ( 'true' == $shortcode_atts['show_content'] ) ) {
		global $more;
		$more = 0;
		the_content( __( 'Read more.', 'ebox' ) );
	}
	?>
</div>
