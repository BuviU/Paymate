<?php
/**
 * ebox LD30 Displays a user's profile search results.
 *
 * @since 3.0.0
 *
 * @package ebox\Templates\LD30
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="ld-item-search ld-profile-search-string">
	<div class="ld-item-search-wrapper">
		<span class="ld-text">
		<?php
		echo sprintf(
			// translators: placeholder: search term.
			esc_html_x( 'You searched for "%s"', 'placeholder: search term', 'ebox' ),
			esc_html( $ebox_profile_search_query )
		);
		?>
		</span>
		<a class="ld-reset-link" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Reset', 'ebox' ); ?></a>
	</div>
</div> <!--/.ld-profile-search-string-->
