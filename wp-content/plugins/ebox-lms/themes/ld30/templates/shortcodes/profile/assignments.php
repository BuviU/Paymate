<?php
/**
 * ebox LD30 Displays a user's profile assignments listing.
 *
 * @since 3.0.0
 *
 * @package ebox\Templates\LD30
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$assignment_post_type_object = get_post_type_object( 'ebox-assignment' ); ?>

<div class="ld-table-list ld-assignment-list">
	<div class="ld-table-list-header ld-primary-background">
		<div class="ld-table-list-title">
			<?php echo esc_html_e( 'Assignments', 'ebox' ); ?>
		</div> <!--/.ld-table-list-tittle-->
		<div class="ld-table-list-columns">
			<?php
			$cols =	array(
				'comments' => esc_html__( 'Comments', 'ebox' ),
				'status'   => esc_html__( 'Status', 'ebox' ),
				'date'     => esc_html__( 'Date', 'ebox' ),
			);

			if ( ! post_type_supports( 'ebox-assignment', 'comments' ) ) {
				unset( $cols['comments'] );
			}

			/**
			 * Filters assignment columns in user's profile.
			 *
			 * @since 3.0.0
			 *
			 * @param array $assignment_columns An array of profile assignment column fields.
			 */
			$cols = apply_filters(
				'ebox-profile-assignment-cols',
				$cols
			);
			foreach ( $cols as $slug => $label ) :
				?>
				<div class="ld-table-list-column <?php echo esc_attr( 'ld-column-' . $slug ); ?>">
					<?php echo esc_html( $label ); ?>
				</div>
			<?php endforeach; ?>
		</div> <!--/.ld-table-list-columns-->
	</div> <!--/.ld-table-list-header-->
	<div class="ld-table-list-items">
			<?php
			if ( $assignments->have_posts() ) :
				/** This filter is documented in includes/shortcodes/ld_course_list.php */
				if ( apply_filters( 'ebox_shortcode_course_list_legacy_loop', false, array() ) ) {

					while ( $assignments->have_posts() ) :
						$assignments->the_post();

						global $post;

						ebox_get_template_part(
							'shortcodes/profile/assignment-row.php',
							array(
								'assignment_post_type_object' => get_post_type_object( 'ebox-assignment' ),
								'assignment' => $post,
								'course_id'  => $course_id,
								'user_id'    => $user_id,
							),
							true
						);

					endwhile;
				} else {
					foreach ( $assignments->posts as $ebox_assignment_post ) {
						ebox_get_template_part(
							'shortcodes/profile/assignment-row.php',
							array(
								'assignment_post_type_object' => get_post_type_object( 'ebox-assignment' ),
								'assignment' => $ebox_assignment_post,
								'course_id'  => $course_id,
								'user_id'    => $user_id,
							),
							true
						);
					}
				}

			else :
				// In theory this will never display, but fallback just in case.
				?>
				<div class="ld-table-list-item">
					<div class="ld-table-list-item-preview">
						<div class="ld-table-list-title"><?php esc_html_e( 'No assignments at this time', 'ebox' ); ?></div>
					</div> <!--/.ld-table-list-item-preview-->
				</div> <!--/.ld-table-list-item-->
				<?php
			endif;
			wp_reset_query();
			?>
	</div> <!--/.ld-table-list-items-->
	<div class="ld-table-list-footer">
	</div>
</div> <!--/.ld-assignment-list-->
