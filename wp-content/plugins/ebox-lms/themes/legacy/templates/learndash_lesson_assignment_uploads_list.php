<?php
/**
 * Lesson/topic assignment uploads list.
 *
 * If ther user has previouly uploaded assignment files they will be show via this template
 *
 * Available Variables:
 *
 * $course_step_post        : WP_Post object for the Lesson/Topic being shown
 *
 * @since 2.5.0
 *
 * @package ebox\Templates\Legacy\Assignment
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( isset( $course_step_post ) ) && ( $course_step_post instanceof WP_Post ) ) {

	$assignment_messages = get_user_meta( get_current_user_id(), 'ld_assignment_message', true );
	if ( ! empty( $assignment_messages ) ) {

		if ( is_array( $assignment_messages ) ) {
			foreach ( $assignment_messages as $assignment_message ) {
				if ( ( isset( $assignment_message['message'] ) ) && ( ! empty( $assignment_message['message'] ) ) ) {
					if ( ( isset( $assignment_message['type'] ) ) && ( ! empty( $assignment_message['type'] ) ) ) {
						if ( 'error' === $assignment_message['type'] ) {
							echo '<p class="ld-error">' . $assignment_message['message'] . '</p>';
						} elseif ( 'success' === $assignment_message['type'] ) {
							echo '<p class="ld-success">' . $assignment_message['message'] . '</p>';
						}
					}
				}
			}
		}
		delete_user_meta( get_current_user_id(), 'ld_assignment_message' );
	}


	$post_settings = ebox_get_setting( $course_step_post->ID );
	$assignments   = ebox_get_user_assignments( $course_step_post->ID, $user_id );
	if ( ! empty( $assignments ) ) {

		$assignment_post_type_object = get_post_type_object( 'ebox-assignment' );
		?>
		<div id="ebox_uploaded_assignments" class="ebox_uploaded_assignments">
			<h2><?php esc_html_e( 'Files you have uploaded', 'ebox' ); ?></h2>
			<table>
				<?php foreach ( $assignments as $assignment ) { ?>
					<tr>
						<td class="ld-assignment-delete-col">
						<?php
						if ( ! ebox_is_assignment_approved_by_meta( $assignment->ID ) ) {
							if ( ( isset( $post_settings['lesson_assignment_deletion_enabled'] ) ) && ( $post_settings['lesson_assignment_deletion_enabled'] == 'on' ) && ( ( $assignment->post_author == $user_id ) || ( ebox_is_admin_user( $current_user_id ) ) || ( ebox_is_team_leader_of_user( $current_user_id, $post->post_author ) ) ) ) {
								?>
							<a href="<?php echo esc_url( add_query_arg( 'ebox_delete_attachment', $assignment->ID ) ); ?>" title="<?php esc_html_e( 'Delete this uploaded Assignment', 'ebox' ); ?>"><?php esc_html_e( 'X', 'ebox' ); ?></a>
												<?php
							}
						}
						?>
						</td>
						<td class="ld-assignment-filename-col">
							<a href='<?php echo esc_attr( get_post_meta( $assignment->ID, 'file_link', true ) ); ?>' target="_blank"><?php esc_html_e( 'Download', 'ebox' ); ?> <?php echo get_post_meta( $assignment->ID, 'file_name', true ); ?></a><br />
							<span class="ebox_uploaded_assignment_points"><?php echo ebox_assignment_points_awarded( $assignment->ID ); ?></span>
						</td>
						<td class="ld-assignment-comments-col">
						<?php
						if ( true === $assignment_post_type_object->publicly_queryable ) {
							?>
								<a href='<?php echo esc_url( get_permalink( $assignment->ID ) ); ?>'><?php esc_html_e( 'View', 'ebox' ); ?></a>
								<?php
								if ( post_type_supports( 'ebox-assignment', 'comments' ) ) {
									/** This filter is documented in https://developer.wordpress.org/reference/hooks/comments_open/ */
									if ( apply_filters( 'comments_open', $assignment->comment_status, $assignment->ID ) ) {
										?>
										<a href='<?php echo esc_url( get_comments_link( $assignment->ID ) ); ?>'>
															<?php
															echo sprintf(
															// translators: placeholder: comments count.
																esc_html_x( 'Comments (%d)', 'placeholder: commentd count', 'ebox' ),
																get_comments_number( $assignment->ID )
															)
															?>
										</a>
										<?php
									}
								}
						}
						?>
						</td>
					</tr>
				<?php } ?>
			</table>
		</div>
		<?php
	}
}

