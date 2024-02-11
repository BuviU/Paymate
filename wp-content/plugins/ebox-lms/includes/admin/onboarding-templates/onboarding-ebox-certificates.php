<?php
/**
 * Onboarding Certificates Template.
 *
 * Displayed when no entities were added to help the user.
 *
 * @since 3.0.0
 * @package ebox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="ld-onboarding-screen">
	<div class="ld-onboarding-main">
		<span class="dashicons dashicons-welcome-add-page"></span>
		<h2>
		<?php
			echo esc_html_x( 'You don\'t have any Certificates yet', 'Placeholder text when no certificates have been created', 'ebox' );
		?>
		</h2>
		<p>
		<?php
				printf(
					// translators: placeholder: %1$s: Quiz, %2$s: Course.
					esc_html_x( 'Certificates can be awarded based on %1$s performance or at the very end of a %2$s. Once earned, the Certificate is available for PDF download from the user’s ebox profile.', 'placeholder: %1$s: Quiz, %2$s: Course', 'ebox' ),
					\ebox_Custom_Label::get_label( 'quiz' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
					\ebox_Custom_Label::get_label( 'course' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
				);
				?>
		</p>
		<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=ebox-certificates' ) ); ?>" class="button button-secondary">
			<span class="dashicons dashicons-plus-alt"></span>
			<?php echo esc_html_x( 'Add your first Certificate', 'Button to add a certificate', 'ebox' ); ?>
		</a>
	</div> <!-- .ld-onboarding-main -->

	<div class="ld-onboarding-more-help">
		<h2><?php esc_html_e( 'Need more help?', 'ebox' ); ?></h2>

		<div class="ld-onboarding-row">
			<div class="ld-onboarding-col">
				<h3>
				<?php
					printf(
						// translators: placeholder: Course.
						esc_html_x( 'Creating a Great %s Certificate', 'placeholder: Course', 'ebox' ),
						\ebox_Custom_Label::get_label( 'course' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
					);
					?>
				</h3>
				<div class="ld-bootcamp__embed">
					<iframe width="560" height="315" src="https://www.youtube.com/embed/niS7Upk4LEc" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
				</div>
			</div>
			<div class="ld-onboarding-col">
				<h3><?php esc_html_e( 'Related help and documentation', 'ebox' ); ?></h3>
				<ul>
					<li><a href="https://arttricks.com.au/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Certificates Documentation (only available in English)', 'ebox' ); ?></a></li>
				</ul>
			</div>
		</div>

	</div> <!-- .ld-onboarding-more-help -->

</section> <!-- .ld-onboarding-screen -->
