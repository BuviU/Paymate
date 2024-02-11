<?php
/**
 * ebox Admin Assignment Edit.
 *
 * @since 3.2.3
 * @package ebox\Assignment\Edit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Admin_Post_Edit' ) ) && ( ! class_exists( 'ebox_Admin_Assignment_Edit' ) ) ) {

	/**
	 * Class ebox Admin Assignment Edit.
	 *
	 * @since 3.2.3
	 * @uses ebox_Admin_Post_Edit
	 */
	class ebox_Admin_Assignment_Edit extends ebox_Admin_Post_Edit {

		/**
		 * Public constructor for class.
		 *
		 * @since 3.2.3
		 */
		public function __construct() {
			$this->post_type = ebox_get_post_type_slug( 'assignment' );

			parent::__construct();
		}

		/**
		 * On Load handler function for this post type edit.
		 * This function is called by a WP action when the admin
		 * page 'post.php' or 'post-new.php' are loaded.
		 *
		 * @since 3.2.3
		 */
		public function on_load() {
			if ( $this->post_type_check() ) {

				parent::on_load();

				add_meta_box(
					'ebox_assignment_metabox',
					esc_html__( 'Assignment', 'ebox' ),
					array( $this, 'assignment_metabox_content' ),
					ebox_get_post_type_slug( 'assignment' ),
					'advanced',
					'high'
				);
			}
		}

		/**
		 * Adds approval Link to assignment metabox.
		 *
		 * @global WP_Post  $post     Global post object.
		 * @global ebox_LMS $ebox_lms Global ebox_LMS object.
		 *
		 * @since 3.2.3
		 */
		public function assignment_metabox_content() {
			global $post, $ebox_lms;

			$assignment_course_id = intval( get_post_meta( $post->ID, 'course_id', true ) );
			$assignment_lesson_id = intval( get_post_meta( $post->ID, 'lesson_id', true ) );

			wp_nonce_field( 'ld-assignment-nonce-' . $post->ID, 'ld-assignment-nonce' );

			?>
			<div class="ebox ebox_options ebox-assignment_settings">
				<div class="ebox_input " id="ebox-assignment_course">
					<span class="ebox_option_label" style="text-align:right;vertical-align:top;"><a class="ebox_help_text_link" style="cursor:pointer;" title="<?php esc_html_e( 'Click for Help!', 'ebox' ); ?>" onclick="toggleVisibility('ebox-assignment_course_tip');"><img src="<?php echo esc_url( ebox_LMS_PLUGIN_URL ); ?>/assets/images/question.png" /><label class="ebox_label textinput">
					<?php
					// translators: placeholder: Course.
					echo sprintf( esc_html_x( 'Associated %s', 'placeholder: Course', 'ebox' ), esc_attr( ebox_Custom_Label::get_label( 'course' ) ) );
					?>
					</label></a></span>
					<span class="ebox_option_input"><div class="ebox_option_div">
					<?php
					if ( empty( $assignment_course_id ) ) {
						?>
						<select name="ebox-assignment_course">
							<option value="">
							<?php
							// translators: placeholder: Course.
							echo sprintf( esc_html_x( '-- Select a %s --', 'placeholder: Course', 'ebox' ), esc_attr( ebox_Custom_Label::get_label( 'course' ) ) );
							?>
							</option>
							<?php
								$cb_courses = array();
							if ( ! empty( $assignment_lesson_id ) ) {
								$cb_courses = ebox_get_courses_for_step( $assignment_lesson_id, true );
								if ( ! empty( $cb_courses ) ) {
									$cb_courses = array_keys( $cb_courses );
								}
							}

								$query_courses_args = array(
									'post_type'      => 'ebox-courses',
									'post_status'    => 'any',
									'posts_per_page' => -1,
									'post__in'       => $cb_courses,
									'orderby'        => 'title',
									'order'          => 'ASC',
								);

								$query_courses = new WP_Query( $query_courses_args );

								if ( ! empty( $query_courses->posts ) ) {
									foreach ( $query_courses->posts as $p ) {
										?>
										<option value="<?php echo absint( $p->ID ); ?>"><?php echo wp_kses_post( $p->post_title ); ?></option>
										<?php
									}
								}
								?>
							</select>
							<?php
					} else {
						echo '<p>' . wp_kses_post( get_the_title( $assignment_course_id ) ) . ' (<a href="' . esc_url( get_permalink( $assignment_course_id ) ) . '">' . esc_html__( 'edit', 'ebox' ) . '</a>)</p>';

					}
					?>
				</div><div class="ebox_help_text_div" style="display:none" id="ebox-assignment_course_tip"><label class="ebox_help_text">
				<?php
				// translators: placeholder: Course.
				echo sprintf( esc_html_x( 'Associate with a %s.', 'placeholder: Course', 'ebox' ), esc_attr( ebox_Custom_Label::get_label( 'course' ) ) );
				?>
				</label></div></span><p style="clear:left"></p></div>
			</div>

			<div class="ebox ebox_options ebox-assignment_settings">
				<div class="ebox_input " id="ebox-assignment_lesson">
					<span class="ebox_option_label" style="text-align:right;vertical-align:top;"><a class="ebox_help_text_link" style="cursor:pointer;" title="<?php esc_html_e( 'Click for Help!', 'ebox' ); ?>" onclick="toggleVisibility('ebox-assignment_lesson_tip');"><img src="<?php echo esc_url( ebox_LMS_PLUGIN_URL . '/assets/images/question.png' ); ?>" /><label class="ebox_label textinput">
					<?php
					// translators: placeholder: Lesson.
					echo sprintf( esc_html_x( 'Associated %s', 'placeholder: Lesson', 'ebox' ), esc_attr( ebox_Custom_Label::get_label( 'lesson' ) ) );
					?>
					</label></a></span>
					<span class="ebox_option_input"><div class="ebox_option_div">
					<?php
					if ( empty( $assignment_lesson_id ) ) {
						?>
						<select name="ebox-assignment_lesson">
							<option value="">
							<?php
							// translators: placeholder: Lesson.
							echo sprintf( esc_html_x( '-- Select a %s --', 'placeholder: Lesson', 'ebox' ), esc_attr( ebox_Custom_Label::get_label( 'lesson' ) ) );
							?>
							</option>
							<?php
							if ( ! empty( $assignment_course_id ) ) {
								$course_modules = $ebox_lms->select_a_lesson_or_topic( $assignment_course_id, true );
								if ( ! empty( $course_modules ) ) {
									foreach ( $course_modules as $l_id => $l_label ) {
										?>
											<option value="<?php echo esc_attr( $l_id ); ?>"><?php echo esc_html( $l_label ); ?></option>
											<?php
									}
								}
							}
							?>
							</select>
							<?php
					} else {
						echo '<p>' . wp_kses_post( get_the_title( $assignment_lesson_id ) ) . ' (<a href="' . esc_url( get_permalink( $assignment_lesson_id ) ) . '">' . esc_html__( 'edit', 'ebox' ) . '</a>)</p>';
					}
					?>
				</div><div class="ebox_help_text_div" style="display:none" id="ebox-assignment_lesson_tip"><label class="ebox_help_text">
				<?php
				// translators: placeholder: Lesson.
				echo sprintf( esc_html_x( 'Associate with a %s.', 'placeholder: Lesson', 'ebox' ), esc_attr( ebox_Custom_Label::get_label( 'lesson' ) ) );
				?>
				</label></div></span><p style="clear:left"></p></div>
			</div>

			<div class="ebox ebox_options ebox-assignment_settings">
				<div class="ebox_input " id="ebox-assignment_status">
					<span class="ebox_option_label" style="text-align:right;vertical-align:top;"><a class="ebox_help_text_link" style="cursor:pointer;" title="<?php esc_html_e( 'Click for Help!', 'ebox' ); ?>" onclick="toggleVisibility('ebox-assignment_status_tip');"><img src="<?php echo esc_url( ebox_LMS_PLUGIN_URL . '/assets/images/question.png' ); ?>" /><label class="ebox_label textinput"><?php esc_html_e( 'Status', 'ebox' ); ?></label></a></span>
					<span class="ebox_option_input"><div class="ebox_option_div">
					<?php
						$approval_status_flag = ebox_is_assignment_approved_by_meta( $post->ID );
					if ( 1 == $approval_status_flag ) {
						$approval_status_label = esc_html__( 'Approved', 'ebox' );
						echo '<p>' . esc_html( $approval_status_label ) . '</p>';
					} else {
						if ( ( ebox_get_setting( $assignment_lesson_id, 'lesson_assignment_points_enabled' ) === 'on' ) && ( intval( ebox_get_setting( $assignment_lesson_id, 'lesson_assignment_points_amount' ) ) > 0 ) ) {
							$approval_status_label = esc_html__( 'Not Approved', 'ebox' );
							echo '<p>' . esc_html( $approval_status_label ) . '</p>';
						} else {
							$approve_text = esc_html__( 'Approve', 'ebox' );
							echo '<p><input name="assignment-status" type="submit" class="button button-primary button-large" id="publish" value="' . esc_attr( $approve_text ) . '"></p>';
						}
					}
					?>
				</div><div class="ebox_help_text_div" style="display:none" id="ebox-assignment_status_tip"><label class="ebox_help_text">
				<?php
				esc_html_e( 'Assignment status.', 'ebox' );
				?>
				</label></div></span><p style="clear:left"></p></div>
			</div>

			<div class="ebox ebox_options ebox-assignment_settings">
				<div class="ebox_input " id="ebox-assignment_points">
					<span class="ebox_option_label" style="text-align:right;vertical-align:top;"><a class="ebox_help_text_link" style="cursor:pointer;" title="<?php esc_html_e( 'Click for Help!', 'ebox' ); ?>" onclick="toggleVisibility('ebox-assignment_points_tip');"><img src="<?php echo esc_url( ebox_LMS_PLUGIN_URL . '/assets/images/question.png' ); ?>" /><label class="ebox_label textinput"><?php esc_html_e( 'Points', 'ebox' ); ?></label></a></span>
					<span class="ebox_option_input"><div class="ebox_option_div">
					<?php
					if ( ( ! empty( $assignment_course_id ) ) && ( ! empty( $assignment_lesson_id ) ) ) {

						if ( ( ebox_get_setting( $assignment_lesson_id, 'lesson_assignment_points_enabled' ) === 'on' ) && ( intval( ebox_get_setting( $assignment_lesson_id, 'lesson_assignment_points_amount' ) ) > 0 ) ) {
							$max_points     = intval( ebox_get_setting( $assignment_lesson_id, 'lesson_assignment_points_amount' ) );
							$current_points = intval( get_post_meta( $post->ID, 'points', true ) );
							$update_text    = ebox_is_assignment_approved_by_meta( $post->ID ) ? esc_html__( 'Update', 'ebox' ) : esc_html__( 'Update & Approve', 'ebox' );

							echo '<p>';
							echo "<label for='assignment-points'>" .
							// translators: placeholder: max points.
							sprintf( esc_html_x( 'Awarded Points (Out of %d):', 'placeholder: max points', 'ebox' ), esc_attr( $max_points ) ) . '</label><br />';
							echo '<input name="assignment-points" type="number" min="0" max="' . absint( $max_points ) . '" value="' . absint( $current_points ) . '">';
							echo '<p><input name="save" type="submit" class="button button-primary button-large" id="publish" value="' . esc_attr( $update_text ) . '"></p>';
							echo '</p>';
						} else {
							echo '<p>' . esc_html__( 'Points not enabled', 'ebox' ) . '</p>';
						}
					}
					?>
				</div><div class="ebox_help_text_div" style="display:none" id="ebox-assignment_points_tip"><label class="ebox_help_text"><?php esc_html_e( 'Assignment Points.', 'ebox' ); ?></label></div></span><p style="clear:left"></p></div>
			</div>

			<?php
				$file_link = get_post_meta( $post->ID, 'file_link', true );
			if ( ! empty( $file_link ) ) {
				?>
				<div class="ebox ebox_options ebox-assignment_settings">
					<div class="ebox_input " id="ebox-assignment_download">
						<span class="ebox_option_label" style="text-align:right;vertical-align:top;"><a class="ebox_help_text_link" style="cursor:pointer;" title="<?php esc_html_e( 'Click for Help!', 'ebox' ); ?>" onclick="toggleVisibility('ebox-assignment_download_tip');"><img src="<?php echo esc_url( ebox_LMS_PLUGIN_URL . '/assets/images/question.png' ); ?>" /><label class="ebox_label textinput"><?php esc_html_e( 'Actions', 'ebox' ); ?></label></a></span>
						<span class="ebox_option_input"><div class="ebox_option_div">
						<?php

							// Link handling.
							$file_link = get_post_meta( $post->ID, 'file_link', true );

							echo "<a href='" . esc_url( $file_link ) . "' target='_blank' class='button'>" . esc_html__( 'Download', 'ebox' ) . '</a>';
						?>
						</div><div class="ebox_help_text_div" style="display:none" id="ebox-assignment_download_tip"><label class="ebox_help_text"><?php esc_html_e( 'Assignment download.', 'ebox' ); ?></label></div></span><p style="clear:left"></p></div>
					</div>
					<?php
			}
		}

		/**
		 * Save metabox handler function.
		 *
		 * @since 3.2.3
		 *
		 * @param integer $post_id Post ID Question being edited.
		 * @param object  $post WP_Post Question being edited.
		 * @param boolean $update If update true, else false.
		 */
		public function save_post( $post_id = 0, $post = null, $update = false ) {
			if ( ! $this->post_type_check( $post ) ) {
				return false;
			}

			if ( ! parent::save_post( $post_id, $post, $update ) ) {
				return false;
			}

			$this->assignment_save_metabox_content( $post_id );
		}

		/**
		 * Updates assignment points and approval status.
		 *
		 * Fires on `save_post` hook.
		 *
		 * @since 3.2.3
		 *
		 * @param int $assignment_id Assignment ID.
		 */
		protected function assignment_save_metabox_content( $assignment_id ) {
			if ( ! isset( $_POST['ld-assignment-nonce'] ) ) {
				return;
			}

			if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ld-assignment-nonce'] ) ), 'ld-assignment-nonce-' . $assignment_id ) ) {
				return;
			}

			$assignment_course_id = intval( get_post_meta( $assignment_id, 'course_id', true ) );
			if ( ( empty( $assignment_course_id ) ) && ( isset( $_POST['ebox-assignment_course'] ) ) && ( ! empty( $_POST['ebox-assignment_course'] ) ) ) {
				update_post_meta( $assignment_id, 'course_id', intval( $_POST['ebox-assignment_course'] ) );
			}

			$assignment_lesson_id = intval( get_post_meta( $assignment_id, 'lesson_id', true ) );
			if ( ( empty( $assignment_lesson_id ) ) && ( isset( $_POST['ebox-assignment_lesson'] ) ) && ( ! empty( $_POST['ebox-assignment_lesson'] ) ) ) {
				update_post_meta( $assignment_id, 'lesson_id', intval( $_POST['ebox-assignment_lesson'] ) );
			}

			if ( isset( $_POST['assignment-points'] ) ) {

				// update points.
				$points = intval( $_POST['assignment-points'] );
				update_post_meta( $assignment_id, 'points', $points );

				// approve assignment.
				$assignment_post = get_post( $assignment_id );
				$lesson_id       = get_post_meta( $assignment_id, 'lesson_id', true );
				ebox_approve_assignment( (int) $assignment_post->post_author, $lesson_id, $assignment_post->ID );
			} elseif ( ( isset( $_POST['assignment-status'] ) ) && ( esc_html__( 'Approve', 'ebox' ) == $_POST['assignment-status'] ) ) {

				// approve assignment.
				$assignment_post = get_post( $assignment_id );
				$lesson_id       = get_post_meta( $assignment_id, 'lesson_id', true );
				ebox_approve_assignment( (int) $assignment_post->post_author, $lesson_id, $assignment_post->ID );
			}
		}


		// End of functions.
	}
}
new ebox_Admin_Assignment_Edit();

/**
 * Check if Team Leader can edit Essay.
 *
 * @since 3.4.0
 *
 * parameters documented in /wp-includes/class-wp-user.php
 */
// phpcs:ignore Squiz.Commenting.FunctionComment
function ebox_team_leader_can_edit_assignment_filter( $allcaps, $cap, $args, $user ) {
	global $pagenow, $typenow;

	if ( ( 'post.php' !== $pagenow ) && ( 'post-new.php' !== $pagenow ) ) {
		return $allcaps;
	}

	if ( ebox_get_post_type_slug( 'assignment' ) !== $typenow ) {
		return $allcaps;
	}

	if ( ! in_array( 'edit_others_assignments', $cap, true ) ) {
		return $allcaps;
	}

	if ( ( ! isset( $args[2] ) ) || ( empty( $args[2] ) ) || ( get_post_type( $args[2] ) !== ebox_get_post_type_slug( 'assignment' ) ) ) {
		return $allcaps;
	}
	$post_id = absint( $args[2] );
	$post    = get_post( $post_id );

	if ( ( ! isset( $args[1] ) ) || ( empty( $args[1] ) ) || ( ! ebox_is_team_leader_user( $args[1] ) ) ) {
		return $allcaps;
	}
	$gl_user_id = absint( $args[1] );

	$course_id = get_post_meta( $post_id, 'course_id', true );
	$course_id = absint( $course_id );

	if ( ! ebox_check_team_leader_course_user_intersect( $gl_user_id, (int) $post->post_author, $course_id ) ) {
		foreach ( $cap as $cap_slug ) {
			$allcaps[ $cap_slug ] = false;
		}
	}
	return $allcaps;
}

add_action(
	'init',
	function () {
		if ( ebox_is_team_leader_user() ) {
			add_filter( 'user_has_cap', 'ebox_team_leader_can_edit_assignment_filter', 10, 4 );
		}
	},
	10
);

