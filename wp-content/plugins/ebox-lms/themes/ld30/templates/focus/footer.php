<?php
/**
 * ebox LD30 focus mode footer.
 *
 * @since 3.0.0
 *
 * @package ebox\Templates\LD30
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php
					/**
					 * Fires after the assignments upload message.
					 *
					 * @since 3.0.0
					 *
					 * @param int $course_id Course ID.
					 */
					do_action( 'ebox-focus-template-end', $course_id );
?>
				</div> <!--/.ld-focus-->
			</div> <!--/.ld-ebox-wrapper-->

			<?php ebox_load_login_modal_html(); ?>
			<?php wp_footer(); ?>

	</body>
</html>
