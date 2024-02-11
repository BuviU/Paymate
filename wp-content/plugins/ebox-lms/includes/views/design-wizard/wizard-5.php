<?php
/**
 * Setup wizard template of page 5
 *
 * @package ebox_Design_Wizard
 *
 * @var array<string, mixed> $template_details
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

?>

<div class="design-wizard layout-2 step-5">
	<div class="header">
		<div class="logo">
            <?php // phpcs:ignore Generic.Files.LineLength.TooLong?>
			<img  src="<?php echo esc_url( \ebox_LMS_PLUGIN_URL . '/assets/images/Arttricks-logo.svg' ); ?>" alt="ebox" />
		</div>
	</div>
	<div class="content">
		<div class="icon">
            <?php // phpcs:ignore Generic.Files.LineLength.TooLong?>
			<img src="<?php echo esc_url( \ebox_LMS_PLUGIN_URL . '/assets/images/design-wizard/wizard/palette.png' ); ?>" alt="Palette" />
		</div>
		<div class="title-wrapper">
			<h1 class="title"><?php esc_html_e( 'Nice choices', 'ebox' ); ?></h1>
		</div>
		<div class="progress">
			<div class="bar-wrapper">
				<div class="bar">
					<progress id="progress" value="0" max="100"> 0% </progress>
				</div>
				<div class="percentage">
					<span class="number"></span>
				</div>
			</div>
			<div class="status">
				<span class="message"><?php esc_html_e( 'Start building the template', 'ebox' ); ?>...</span>
			</div>
		</div>
		<div class="text">
			<p>
			<?php
			esc_html_e(
				'A wizard is never late, nor are they early, they arrive precisely when they mean to. 
            Give us just a moment as the Wizard summons your template.',
				'ebox'
			);
			?>
			</p>
		</div>
	</div>
	<div class="footer"></div>
</div>
