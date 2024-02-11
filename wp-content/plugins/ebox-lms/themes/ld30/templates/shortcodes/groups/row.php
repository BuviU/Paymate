<?php
/**
 * ebox LD30 Displays a team row.
 *
 * @since 3.0.0
 *
 * @package ebox\Templates\LD30
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$has_content = ( empty( $team->post_content ) ? false : true ); ?>

<div class="ld-item-list-item ld-expandable ld-item-team-item" id="<?php echo esc_attr( 'ld-expand-' . $team->ID ); ?>">
	<div class="ld-item-list-item-preview ld-team-row">
		<?php if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Teams_CPT', 'public' ) === 'yes' ) { ?>
			<a href="<?php echo esc_url( get_the_permalink( $team->ID ) ); ?>" class="ld-item-name">
			<span class="ld-item-name"><?php echo esc_html( get_the_title( $team->ID ) ); ?></span></a>
			<?php
		} else {
			echo esc_html( get_the_title( $team->ID ) );
		}
		?>
		<?php if ( $has_content ) : ?>
			<div class="ld-item-details">
				<div class="ld-expand-button ld-button-alternate" id="<?php echo esc_attr( 'ld-expand-button-' . $team->ID ); ?>" data-ld-expands="<?php echo esc_attr( 'ld-team-list-item-' . $team->ID ); ?>">
					<span class="ld-icon-arrow-down ld-icon ld-primary-background"></span>
					<span class="ld-text ld-primary-color"><?php esc_html_e( 'Expand', 'ebox' ); ?></span>
				</div> <!--/.ld-expand-button-->
			</div> <!--/.ld-item-details-->
		<?php endif; ?>
	</div> <!--/.ld-item-list-item-preview-->
	<?php if ( $has_content ) : ?>
		<div class="ld-item-list-item-expanded" data-ld-expand-id="<?php echo esc_attr( 'ld-team-list-item-' . $team->ID ); ?>">
			<div class="ld-item-list-content">
				<?php
				ebox_LMS::content_filter_control( false );

				/** This filter is documented in https://developer.wordpress.org/reference/hooks/the_content/ */
				$team_content = apply_filters( 'the_content', $team->post_content );
				$team_content = str_replace( ']]>', ']]&gt;', $team_content );
				echo $team_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Outputting HTML content

				ebox_LMS::content_filter_control( true );
				?>
			</div>
		</div>
	<?php endif; ?>
</div> <!--/.ld-table-list-item-->
