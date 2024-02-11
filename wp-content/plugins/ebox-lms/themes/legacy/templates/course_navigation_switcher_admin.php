<?php
/**
 * Displays the Course Switcher displayed within the Associate Content admin widget.
 * Available Variables:
 * none
 *
 * @since 2.5.0
 *
 * @package ebox\Templates\Legacy\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ( isset( $_GET['post'] ) ) && ( ! empty( $_GET['post'] ) ) ) {
	$ebox_edit_post = get_post( intval( $_GET['post'] ) );
	if ( is_a( $ebox_edit_post, 'WP_Post' ) && ( in_array( $ebox_edit_post->post_type, ebox_get_post_types( 'course_steps' ), true ) ) ) {
		$ebox_cb_courses      = ebox_get_courses_for_step( $ebox_edit_post->ID );
		$ebox_count_primary   = 0;
		$ebox_count_secondary = 0;

		if ( isset( $ebox_cb_courses['primary'] ) ) {
			$ebox_count_primary = count( $ebox_cb_courses['primary'] );
		}

		if ( isset( $ebox_cb_courses['secondary'] ) ) {
			$ebox_count_secondary = count( $ebox_cb_courses['secondary'] );
		}

		if ( ( $ebox_count_primary > 0 ) || ( $ebox_count_secondary > 0 ) ) {

			$ebox_use_select_opt_teams = false;
			if ( ( $ebox_count_primary > 0 ) && ( $ebox_count_secondary > 0 ) ) {
				$ebox_use_select_opt_teams = true;
			}

			$ebox_default_course_id = ebox_get_course_id( $ebox_edit_post->ID, true );

			$ebox_course_post_id = 0;
			if ( isset( $_GET['course_id'] ) ) {
				$ebox_course_post_id = intval( $_GET['course_id'] );
			}

			if ( ( empty( $ebox_course_post_id ) ) && ( ! empty( $ebox_default_course_id ) ) ) {
				$ebox_course_post_id = absint( $ebox_default_course_id );
			}

			?><p class="widget_course_switcher">
			<?php
			// translators: placeholder: Course.
			echo sprintf( esc_html_x( '%s switcher', 'placeholder: Course', 'ebox' ), ebox_Custom_Label::get_label( 'Course' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
			<br />
			<span class="ld-course-message" style="display:none">
			<?php
			// translators: placeholder: Course.
			echo sprintf( esc_html_x( 'Switch to the Primary %s to edit this setting', 'placeholder: Course', 'ebox' ), ebox_Custom_Label::get_label( 'course' ) ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
			</span>
			<input type="hidden" id="ld-course-primary" name="ld-course-primary" value="<?php echo absint( $ebox_default_course_id ); ?>" />

			<?php
				$ebox_item_url = get_edit_post_link( $ebox_edit_post->ID );
			?>
			<select name="ld-course-switcher" id="ld-course-switcher">
				<option value="">
				<?php
				// translators: placeholder: Course.
				echo sprintf( esc_html_x( 'Select a %s', 'placeholder: Course', 'ebox' ), ebox_Custom_Label::get_label( 'Course' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
				</option>
				<?php
				if ( ( ebox_get_post_type_slug( 'quiz' ) === $ebox_edit_post->post_type ) && ( empty( $ebox_count_primary ) ) && ( empty( $ebox_count_secondary ) ) ) {
					?>
						<option selected="selected" data-course_id="0" value="<?php echo esc_url( remove_query_arg( 'course_id', $ebox_item_url ) ); ?>">
						<?php
						// translators: placeholder: Quiz.
						echo sprintf( esc_html_x( 'Standalone %s', 'placeholder: Quiz', 'ebox' ), ebox_Custom_Label::get_label( 'Quiz' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
						</option>
						<?php
				}
				?>
				<?php
				$ebox_selected_course_id = 0;
				foreach ( $ebox_cb_courses as $ebox_course_key => $ebox_course_set ) {
					if ( true === $ebox_use_select_opt_teams ) {
						if ( 'primary' === $ebox_course_key ) {
							?>
							<optteam label="
							<?php
							// translators: placeholder: Course.
							echo sprintf( esc_html_x( 'Primary %s', 'placeholder: Course', 'ebox' ), ebox_Custom_Label::get_label( 'Course' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?>
							">
							<?php
						} elseif ( 'secondary' === $ebox_course_key ) {
							?>
							<optteam label="
							<?php
							// translators: placeholder: Courses.
							echo sprintf( esc_html_x( 'Shared %s', 'placeholder: Courses', 'ebox' ), ebox_Custom_Label::get_label( 'Courses' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?>
							">
							<?php
						}
					}

					foreach ( $ebox_course_set as $ebox_course_id => $ebox_course_title ) {
						$ebox_item_url = add_query_arg( 'course_id', $ebox_course_id, $ebox_item_url );

						$ebox_selected = '';
						if ( ebox_get_post_type_slug( 'quiz' ) === $ebox_edit_post->post_type ) {
							if ( $ebox_course_id == $ebox_course_post_id ) {
								$ebox_selected           = ' selected="selected" ';
								$ebox_selected_course_id = $ebox_course_id;
							}
						} else {
							if ( ( $ebox_course_id == $ebox_course_post_id ) || ( ( empty( $ebox_course_post_id ) ) && ( $ebox_course_id == $ebox_default_course_id ) ) ) {
								$ebox_selected           = ' selected="selected" ';
								$ebox_selected_course_id = $ebox_course_id;
							}
						}
						?>
						<option <?php echo esc_attr( $ebox_selected ); ?> data-course_id="<?php echo absint( $ebox_course_id ); ?>" value="<?php echo esc_url( $ebox_item_url ); ?>"><?php echo wp_kses_post( get_the_title( $ebox_course_id ) ); ?></option>
						<?php
					}

					if ( true === $ebox_use_select_opt_teams ) {
						?>
						</optteam>
						<?php
					}
				}
				?>
			</select></p>
			<?php

			if ( absint( $ebox_selected_course_id ) !== absint( $ebox_default_course_id ) ) {
				wp_nonce_field( 'ld-course-primary-set-nonce', 'ld-course-primary-set-nonce', false );
				?>
				<input type="checkbox" id="ld-course-primary-set" name="ld-course-primary-set" value="<?php echo absint( $ebox_selected_course_id ); ?>" /> <label for="ld-course-primary-set">
				<?php
					echo sprintf(
						// translators: placeholder: Course.
						esc_html_x( 'Set Primary %s', 'placeholder: Course', 'ebox' ),
						ebox_Custom_Label::get_label( 'course' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					);
				?>
				</label>
				<?php
			}
		}
	}
}
