<?php
/**
 * Setup wizard template of page 1
 *
 * @package ebox_Design_Wizard
 *
 * @var array<string, mixed> $templates
 * @var ebox_Design_Wizard $design_wizard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

?>
<div class="design-wizard">
	<div class="sidebar">
		<div class="logo">
            <?php // phpcs:ignore Generic.Files.LineLength.TooLong?>
			<img src="<?php echo esc_url( \ebox_LMS_PLUGIN_URL . '/assets/images/Arttricks-logo.svg' ); ?>"
				alt="ebox" >
		</div>
		<div class="header">
			<h1 class="title">
				<?php esc_html_e( 'Choose a template', 'ebox' ); ?>
			</h1>
			<p class="description">
				<?php
				esc_html_e(
					'Our setup wizard will help you 
                get the most out of your store.',
					'ebox'
				);
				?>
			</p>
		</div>
	</div>
	<div class="content">
		<div class="header">
			<div class="exit">
				<span class="text"><?php esc_html_e( 'Exit to Setup', 'ebox' ); ?></span>
                <?php // phpcs:ignore Generic.Files.LineLength.TooLong?>
				<img src="<?php echo esc_url( \ebox_LMS_PLUGIN_URL . '/assets/images/design-wizard/svg/exit.svg' ); ?>" >
			</div>
		</div>
		<div class="templates">
			<?php // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound?>
			<?php foreach ( $templates as $template_details ) : ?>
				<?php
					ebox_LMS::get_view(
						'design-wizard/template',
						compact( 'template_details', 'design_wizard' ),
						true
					);
				?>
			<?php endforeach; ?>
		</div>
		<div class="footer">
			<div class="back">
                <?php // phpcs:ignore Generic.Files.LineLength.TooLong?>
				<img class="icon" src="<?php echo esc_url( \ebox_LMS_PLUGIN_URL . '/assets/images/design-wizard/svg/back.svg' ); ?>" > 
				<span class="text"><?php esc_html_e( 'Back', 'ebox' ); ?></span>
			</div>
			<div class="steps">
				<ol class="list">
					<li class="active"><span class="number">1</span> <span
							class="text"><?php esc_html_e( 'Choose a template', 'ebox' ); ?></span></li>
					<li><span class="number">2</span> <span
							class="text"><?php esc_html_e( 'Fonts', 'ebox' ); ?></span></li>
					<li><span class="number">3</span> <span
							class="text"><?php esc_html_e( 'Colors', 'ebox' ); ?></span></li>
				</ol>
			</div>
			<div class="buttons">
				<a
					href="#"
					class="button next-button"
				><?php esc_html_e( 'Next', 'ebox' ); ?></a>
			</div>
		</div>
	</div>
	<div class="preview-wrapper">
		<div class="background"></div>
		<div class="preview">
			<div class="text-wrapper"><?php esc_html_e( 'Loading', 'ebox' ); ?>...</div>
			<div class="buttons-wrapper">
				<div class="close">
					<span class="icon dashicons dashicons-no-alt"></span>
					<span class="text"><?php esc_html_e( 'Close', 'ebox' ); ?></span>
				</div>
				<div class="clear"></div>
			</div>
			<div class="iframe-wrapper">
				<iframe
					class="ld-site-preview"
					id="ld-site-preview"
					src=""
					frameborder="0"
				></iframe>
			</div>
		</div>
	</div>
</div>
