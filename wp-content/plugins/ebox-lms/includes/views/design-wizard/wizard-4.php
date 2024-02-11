<?php
/**
 * Page 4 of setup wizard template file
 *
 * @package ebox_Design_Wizard
 *
 * @var array<string, mixed> $template_details
 * @var ebox_Design_Wizard $design_wizard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

// Needed for confirm popup before setting up template.
wp_enqueue_script( 'jquery-ui-dialog' );
wp_enqueue_style( 'wp-jquery-ui-dialog' );

?>
<div class="design-wizard layout-2">
	<div class="header">
		<div class="logo">
            <?php // phpcs:ignore Generic.Files.LineLength.TooLong?>
			<img src="<?php echo esc_url( \ebox_LMS_PLUGIN_URL . '/assets/images/Arttricks-logo.svg' ); ?>" alt="ebox" >
		</div>
		<div class="exit">
			<span class="text"><?php esc_html_e( 'Exit to Setup', 'ebox' ); ?></span> <img
				src="<?php echo esc_url( \ebox_LMS_PLUGIN_URL . '/assets/images/design-wizard/svg/exit.svg' ); ?>"
			>
		</div>
	</div>
	<div class="content">
		<?php
			ebox_LMS::get_view(
				'design-wizard/live-preview',
				compact( 'template_details', 'design_wizard' ),
				true
			);
			?>
	</div>
	<div class="footer">
		<div class="back">
			<img
				class="icon"
				src="<?php echo esc_url( \ebox_LMS_PLUGIN_URL . '/assets/images/design-wizard/svg/back.svg' ); ?>"
			> <span class="text"><?php esc_html_e( 'Back', 'ebox' ); ?></span>
		</div>
		<div class="steps">
			<ol class="list">
				<li class="active"><span class="number">1</span> <span
						class="text"><?php esc_html_e( 'Choose a template', 'ebox' ); ?></span></li>
				<li class="active"><span class="number">2</span> <span
						class="text"><?php esc_html_e( 'Fonts', 'ebox' ); ?></span></li>
				<li class="active"><span class="number">3</span> <span
						class="text"><?php esc_html_e( 'Colors', 'ebox' ); ?></span></li>
			</ol>
		</div>
		<div class="buttons">
			<a
				href="#"
				class="button init-button next-button"
			><?php esc_html_e( 'Save & Continue', 'ebox' ); ?></a>
		</div>
	</div>
	<div id="ld_dw_confirm" style="display: none;">
		Upon clicking continue we’ll install your selected template which will include:
			<ul style="list-style-type: disc;margin-left: 20px;">
				<li>Theme</li>
				<li>Plugins</li>
				<li>Content</li>
			</ul>
		<p>This will overwrite your existing theme. It will not replace content or plugins but the theme will impact your entire site, not only ebox content.</p>
	</div>
</div>
