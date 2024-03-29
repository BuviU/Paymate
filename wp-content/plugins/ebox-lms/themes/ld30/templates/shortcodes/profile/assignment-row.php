<?php
/**
 * ebox LD30 Displays a user's profile assignments row.
 *
 * @since 3.0.0
 *
 * @package ebox\Templates\LD30
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$assignment_points = ebox_get_points_awarded_array( $assignment->ID );
( $assignment->ID );
?>

<div class="ld-table-list-item">
	<div class="ld-table-list-item-preview">
		<div class="ld-table-list-title">

			<a class="ld-item-icon" href='<?php echo esc_url( get_post_meta( $assignment->ID, 'file_link', true ) ); ?>' target="_blank">
				<span class="ld-icon ld-icon-assignment" aria-label="<?php esc_html_e( 'Download Assignment', 'ebox' ); ?>"></span>
			</a>

			<?php
			$assignment_link = ( true === $assignment_post_type_object->publicly_queryable ? get_permalink( $assignment->ID ) : get_post_meta( $assignment->ID, 'file_link', true ) );
			?>

			<a class="ld-text" href="<?php echo esc_url( $assignment_link ); ?>"><?php echo esc_html( get_the_title( $assignment->ID ) ); ?></a>

		</div>
		<div class="ld-table-list-columns">
			<?php
			// Use an array so it can be filtered later.
			$row_columns = array();

			/**
			 * Comment count and link to assignment
			 */

			/** This action is documented in themes/ld30/templates/assignment/partials/row.php */
			do_action( 'ebox-assignment-row-columns-before', $assignment, get_the_ID(), $course_id, $user_id );

			/** This filter is documented in https://developer.wordpress.org/reference/hooks/comments_open/ */
			if ( post_type_supports( 'ebox-assignment', 'comments' ) && apply_filters( 'comments_open', $assignment->comment_status, $assignment->ID ) ) : // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WP Core hook

				ob_start();

				/** This action is documented in themes/ld30/templates/assignment/partials/row.php */
				do_action( 'ebox-assignment-row-comments-before', $assignment, get_the_ID(), $course_id, $user_id );

				if ( true === (bool) $assignment_post_type_object->publicly_queryable ) {
					?>
					<a href='<?php echo esc_url( get_comments_link( $assignment->ID ) ); ?>' data-ld-tooltip="<?php echo sprintf( // phpcs:ignore Squiz.PHP.EmbeddedPhp.ContentAfterOpen,Squiz.PHP.EmbeddedPhp.ContentBeforeOpen
						// translators: placeholder: commentd count.
						esc_html_x( '%d Comments', 'placeholder: commentd count', 'ebox' ),
						esc_html( get_comments_number( $assignment->ID ) )
					); ?> ">
					<?php
				}
				?>
				<?php echo esc_html( get_comments_number( $assignment->ID ) ); ?><span class="ld-icon ld-icon-comments"></span>
				<?php
				if ( true === (bool) $assignment_post_type_object->publicly_queryable ) {

					?></a> <?php
				}
				?>
				<?php
				// Add the markup to the array.
				$row_columns['comments'] = ob_get_clean();
				ob_flush();

				/** This action is documented in themes/ld30/templates/assignment/partials/row.php */
				do_action( 'ebox-assignment-row-comments-after', $assignment, get_the_ID(), $course_id, $user_id );

			endif;

			if ( ! ebox_is_assignment_approved_by_meta( $assignment->ID ) && ! $assignment_points ) :

				ob_start();
				?>

				<span class="ld-status ld-status-waiting ld-tertiary-background">
					<span class="ld-icon ld-icon-calendar"></span>
					<span class="ld-text"><?php esc_html_e( 'Waiting Review', 'ebox' ); ?></span>
				</span> <!--/.ld-status-waiting-->

				<?php
				$row_columns['status'] = ob_get_clean();
				ob_flush();

			elseif ( $assignment_points || ebox_is_assignment_approved_by_meta( $assignment->ID ) ) :

				ob_start();
				?>

				<span class="ld-status ld-status-complete">
					<span class="ld-icon ld-icon-checkmark"></span>
					<?php
					if ( $assignment_points ) :
						echo sprintf(
							// translators: placeholder: %1$s: Current points, %2$s: Maximum points.
							esc_html__( '%1$s/%2$s Points Awarded ', 'ebox' ),
							esc_html( $assignment_points['current'] ),
							esc_html( $assignment_points['max'] )
						) . ' - ';
					endif;

					esc_html_e( 'Approved', 'ebox' );
					?>
				</span>

				<?php
				$row_columns['status'] = ob_get_clean();
				ob_flush();

			endif;

			$row_columns['date'] = get_the_date( get_option( 'date_format' ), $assignment->ID );

			// Apply a fitler so devs can add more info here later.
			/** This filter is documented in themes/ld30/templates/assignment/partials/row.php */
			$row_columns = apply_filters( 'ebox-assignment-list-columns-content', $row_columns );
			if ( ! empty( $row_columns ) ) :
				foreach ( $row_columns as $slug => $content ) :

					/** This action is documented in themes/ld30/templates/assignment/partials/row.php */
					do_action( 'ebox-assignment-row-' . $slug . '-before', $assignment, get_the_ID(), $course_id, $user_id );
					?>
				<div class="<?php echo esc_attr( 'ld-table-list-column ld-' . $slug . '-column' ); ?>">
					<?php

					/** This action is documented in themes/ld30/templates/assignment/partials/row.php */
					do_action( 'ebox-assignment-row-' . $slug . '-inside-before', $assignment, get_the_ID(), $course_id, $user_id );

					echo wp_kses_post( $content );

					/** This action is documented in themes/ld30/templates/assignment/partials/row.php */
					do_action( 'ebox-assignment-row-' . $slug . '-inside-after', $assignment, get_the_ID(), $course_id, $user_id );
					?>
				</div>
					<?php

					/** This action is documented in themes/ld30/templates/assignment/partials/row.php */
					do_action( 'ebox-assignment-row-' . $slug . '-after', $assignment, get_the_ID(), $course_id, $user_id );
					?>
					<?php
				endforeach;
			endif;
			?>
		</div>
	</div>
</div>
