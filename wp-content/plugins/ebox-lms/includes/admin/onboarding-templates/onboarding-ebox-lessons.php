<?php
/**
 * Onboarding modules Template.
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
		printf(
			// translators: placeholder: modules.
			esc_html_x( 'You don\'t have any %s yet', 'placeholder: modules', 'ebox' ),
			\ebox_Custom_Label::get_label( 'modules' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
		);
		?>
		</h2>
		<p>
			<?php
				printf(
					// translators: placeholder: %1$s: modules, %2$s: Course, %3$s: modules, %4$s: Course, %5$s: Course.
					esc_html_x( '%1$s are where you add your content for your %2$s. You can add %3$s using the %4$s Builder, or you can create them individually and assign them to a %5$s later', 'placeholder: %1$s: modules, %2$s: Course, %3$s: modules, %4$s: Course, %5$s: Course', 'ebox' ),
					\ebox_Custom_Label::get_label( 'modules' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
					\ebox_Custom_Label::get_label( 'course' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
					\ebox_Custom_Label::get_label( 'modules' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
					\ebox_Custom_Label::get_label( 'course' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
					\ebox_Custom_Label::get_label( 'course' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
				);
				?>
		</p>
		<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=ebox-modules' ) ); ?>" class="button button-secondary">
			<span class="dashicons dashicons-plus-alt"></span>
			<?php
			printf(
				// translators: placeholder: Lesson.
				esc_html_x( 'Add your first %s', 'placeholder: Lesson', 'ebox' ),
				\ebox_Custom_Label::get_label( 'lesson' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
			);
			?>
		</a>
	</div> <!-- .ld-onboarding-main -->

	<div class="ld-onboarding-more-help">
		<div class="ld-onboarding-row">
			<div class="ld-onboarding-col">
				<h3>
				<?php
				printf(
					// translators: placeholder: %1$s: modules, %2$s: Course.
					esc_html_x( 'Creating %1$s for Your %2$s', 'placeholder: %1$s: modules, %2$s: Course', 'ebox' ),
					\ebox_Custom_Label::get_label( 'modules' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
					\ebox_Custom_Label::get_label( 'course' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
				);
				?>
				</h3>
				<div class="ld-bootcamp__embed">
					<iframe width="560" height="315" src="https://www.youtube.com/embed/PD1KKzdakHw" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
				</div>
			</div>
			<div class="ld-onboarding-col">
				<h3><?php esc_html_e( 'Related help and documentation', 'ebox' ); ?></h3>
				<ul>
					<li><a href="https://arttricks.com.au/" target="_blank" rel="noopener noreferrer">
					<?php
					echo sprintf(
						// translators: placeholder: Course.
						esc_html_x( '%s Builder [Article] (only available in English)', 'placeholder: Course', 'ebox' ),
						ebox_Custom_Label::get_label( 'course' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
					);
					?>
					</a></li>
					<li><a href="https://arttricks.com.au/">
					<?php
					echo sprintf(
						// translators: placeholder: modules.
						esc_html_x( '%s Documentation (only available in English)', 'placeholder: modules', 'ebox' ),
						ebox_Custom_Label::get_label( 'modules' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
					);
					?>
					</a></li>
				</ul>
				<p><a href="https://arttricks.com.au/"><?php esc_html_e( 'View all docs', 'ebox' ); ?></a></p>
			</div>
		</div>

	</div> <!-- .ld-onboarding-more-help -->

</section> <!-- .ld-onboarding-screen -->
