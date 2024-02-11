<?php
/**
 * Displays the Team shortcode message.
 * This template is called from the [ld_team] shortcode.
 *
 * @param array $shortcode_atts {
 *   integer $team_id Team ID context for message shown.
 *   string  $content Message to be shown.
 *   boolean $autop True to filter message via wpautop() function.
 * }
 *
 * @since 2.5.9
 *
 * @package ebox\Templates\Legacy\Team
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ( isset( $shortcode_atts['content'] ) ) && ( ! empty( $shortcode_atts['content'] ) ) ) {
	?><div class="ebox-team-message">
	<?php
	if ( ( isset( $shortcode_atts['autop'] ) ) && ( true === $shortcode_atts['autop'] ) ) {
		echo wpautop( $shortcode_atts['content'] );
	} else {
		echo $shortcode_atts['content'];
	}
	?>
	</div>
	<?php
}
