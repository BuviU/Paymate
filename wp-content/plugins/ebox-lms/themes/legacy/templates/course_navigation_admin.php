<?php
/**
 * This file contains the code that displays the course navigation admin.
 *
 * @since 2.1.0
 *
 * @package ebox\Templates\Legacy\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $pagenow;
global $course_navigation_admin_pager;

if ( ( isset( $course_id ) ) && ( ! empty( $course_id ) ) ) {

	if ( ! isset( $widget ) ) {
		$widget = array(
			'show_widget_wrapper' => true,
			'current_lesson_id'   => 0,
			'current_step_id'     => 0,
		);
	}

	// Not sure why this is here.
	// if ( !isset( $course_progress ) )
	// $course_progress = array();


	$widget['nonce'] = wp_create_nonce( 'ld_course_navigation_admin_pager_nonce_' . $course_id . '_' . get_current_user_id() );
	$widget_json     = htmlspecialchars( wp_json_encode( $widget ) );

	if ( ( isset( $widget['show_widget_wrapper'] ) ) && ( $widget['show_widget_wrapper'] == 'true' ) ) {
		?>
		<div id="course_navigation-<?php echo $course_id; ?>" class="course_navigation" data-widget_instance="<?php echo $widget_json; ?>">
	<?php } ?>

	<div class="ebox_navigation_lesson_topics_list">
		<?php
		if ( ( isset( $modules ) ) && ( ! empty( $modules ) ) ) {

			foreach ( $modules as $course_lesson_id => $course_lesson ) {
				$lesson_meta         = get_post_meta( $course_lesson['post']->ID, '_ebox-modules', true );
				$current_topic_ids   = '';
				$lesson_topics_list  = ebox_topic_dots( $course_lesson['post']->ID, false, 'array', $user_id, $course_id );
				$load_lesson_quizzes = true;

				if ( true === $load_lesson_quizzes ) {
					$lesson_quizzes_list = ebox_get_lesson_quiz_list( $course_lesson['post']->ID, $user_id, $course_id );
				} else {
					$lesson_quizzes_list = array();
				}

				$is_current_lesson       = ( $widget['current_lesson_id'] == $course_lesson['post']->ID );
				$lesson_list_class       = ( $is_current_lesson ) ? 'active' : 'inactive';
				$lesson_lesson_completed = 'lesson_incomplete';
				$list_arrow_class        = ( $is_current_lesson && ! empty( $lesson_topics_list ) ) ? 'expand' : 'collapse';

				if ( ! empty( $lesson_topics_list ) ) {
					$list_arrow_class .= ' flippable';
				}
				?>
				<div class='<?php echo $lesson_list_class; ?>' id='lesson_list-<?php echo $course_id; ?>-<?php echo $course_lesson['post']->ID; ?>'>
					<div class='list_arrow <?php echo $list_arrow_class; ?> <?php echo $lesson_lesson_completed; ?>' onClick='return flip_expand_collapse("#lesson_list-<?php echo $course_id; ?>", <?php echo $course_lesson['post']->ID; ?>);' ></div>
					<div class="list_modules">
						<div class="lesson" >
							<?php
							if ( ebox_show_user_course_complete( $user_id ) ) {
								$user_lesson_progress              = array();
								$user_lesson_progress['user_id']   = $user_id;
								$user_lesson_progress['course_id'] = $course_id;
								$user_lesson_progress['lesson_id'] = $course_lesson['post']->ID;

								if ( $course_lesson['status'] == 'completed' ) {
									$user_lesson_progress['checked'] = true;
								} else {
									$user_lesson_progress['checked'] = false;
								}

									$unchecked_children_message = '';
								if ( ( ! empty( $lesson_topics_list ) ) || ( ! empty( $lesson_quizzes_list ) ) ) {
									$unchecked_children_message = ' data-title-unchecked-children="' . htmlspecialchars( esc_html__( 'Set all children steps as incomplete?', 'ebox' ), ENT_QUOTES ) . '" ';
								}
								?>
										<input id="ebox-mark-lesson-complete-<?php echo $course_id; ?>-<?php echo $course_lesson['post']->ID; ?>" type="checkbox" <?php checked( $course_lesson['status'], 'completed' ); ?> class="ebox-mark-lesson-complete" <?php echo $unchecked_children_message; ?> data-name="<?php echo htmlspecialchars( wp_json_encode( $user_lesson_progress, JSON_FORCE_OBJECT ) ); ?>" />
										<?php
							}
							?>
								<?php
									$edit_url = get_edit_post_link( $course_lesson['post']->ID );
								if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Courses_Builder', 'shared_steps' ) == 'yes' ) {
									$edit_url = add_query_arg( 'course_id', $course_id, $edit_url );
								}
								?>
								<a href='<?php echo esc_url( $edit_url ); ?>'><?php echo apply_filters( 'the_title', $course_lesson['post']->post_title, $course_lesson['post']->ID ); ?></a>
							</div>

							<?php
							if ( ( ! empty( $lesson_topics_list ) ) || ( ! empty( $lesson_quizzes_list ) ) ) {
								?>
								<div id='ebox_topic_dots-<?php echo absint( $course_id ); ?>-<?php echo absint( $course_lesson['post']->ID ); ?>' class="flip ebox_topic_widget_list"  style='<?php echo ( strpos( $list_arrow_class, 'collapse' ) !== false ) ? 'display:none' : ''; ?>'>
									<ul class="ebox-topic-list">
									<?php

									if ( ! empty( $lesson_topics_list ) ) {
										$odd_class = '';

										foreach ( $lesson_topics_list as $key => $topic ) {
											$odd_class       = empty( $odd_class ) ? 'nth-of-type-odd' : '';
											$completed_class = 'topic-notcompleted';

											$topic_quiz_list = ebox_get_lesson_quiz_list( $topic->ID, $user_id, $course_id );

											$unchecked_children_message = '';
											if ( ! empty( $topic_quiz_list ) ) {
												$unchecked_children_message = ' data-title-unchecked-children="' . htmlspecialchars( esc_html__( 'Set all children steps as incomplete?', 'ebox' ), ENT_QUOTES ) . '" ';
											}
											?>
											<li class="topic-item">
												<span class="topic_item">
													<?php
													if ( ebox_show_user_course_complete( $user_id ) ) {
														$user_topic_progress              = array();
														$user_topic_progress['user_id']   = $user_id;
														$user_topic_progress['course_id'] = $course_id;
														$user_topic_progress['lesson_id'] = $course_lesson['post']->ID;
														$user_topic_progress['topic_id']  = $topic->ID;

														if ( ( isset( $course_progress[ $course_id ]['topics'][ $course_lesson['post']->ID ][ $topic->ID ] ) )
														  && ( $course_progress[ $course_id ]['topics'][ $course_lesson['post']->ID ][ $topic->ID ] == true ) ) {
															$topic_checked                  = ' checked="checked" ';
															$user_topic_progress['checked'] = true;
														} else {
															$topic_checked                  = '';
															$user_topic_progress['checked'] = false;
														}

														?>
															<input type="checkbox" <?php echo $topic_checked; ?> id="ebox-mark-topic-complete-<?php echo $course_id; ?>-<?php echo $topic->ID; ?>" class="ebox-mark-topic-complete" <?php echo $unchecked_children_message; ?> data-name="<?php echo htmlspecialchars( wp_json_encode( $user_topic_progress, JSON_FORCE_OBJECT ) ); ?>" />
															<?php
													}
													?>
														<?php
														$edit_url = get_edit_post_link( $topic->ID );
														if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Courses_Builder', 'shared_steps' ) == 'yes' ) {
															$edit_url = add_query_arg( 'course_id', $course_id, $edit_url );
														}
														?>
														<a class='<?php echo $completed_class; ?>' href='<?php echo esc_url( $edit_url ); ?>' title='<?php echo esc_html( $topic->post_title ); ?>'><span><?php echo apply_filters( 'the_title', $topic->post_title, $topic->ID ); ?></span></a>	</span>
														<?php
														if ( ! empty( $topic_quiz_list ) ) {
															?>
															<ul id="ebox-quiz-list-<?php echo absint( $course_id ); ?>-<?php echo absint( $topic->ID ); ?>" class="ebox-quiz-list">
																<?php foreach ( $topic_quiz_list as $quiz ) { ?>
																		<li class="quiz-item">
																			<?php
																			if ( ebox_show_user_course_complete( $user_id ) ) {

																				$user_quiz_progress              = array();
																				$user_quiz_progress['user_id']   = $user_id;
																				$user_quiz_progress['course_id'] = $course_id;
																				$user_quiz_progress['lesson_id'] = $course_lesson['post']->ID;
																				$user_quiz_progress['topic_id']  = $topic->ID;
																				$user_quiz_progress['quiz_id']   = $quiz['post']->ID;

																				if ( $quiz['status'] == 'completed' ) {
																					$quiz_checked                  = ' checked="checked" ';
																					$user_quiz_progress['checked'] = true;
																				} else {
																					$quiz_checked                  = '';
																					$user_quiz_progress['checked'] = false;
																				}
																				$unchecked_message = ' data-title-unchecked="' . htmlspecialchars( esc_html__( 'Set all parent steps as incomplete?', 'ebox' ), ENT_QUOTES ) . '" ';

																				?>
																					<input type="checkbox" <?php echo $quiz_checked; ?>class="ebox-mark-topic-quiz-complete ebox-mark-quiz-complete" <?php echo $unchecked_message; ?> data-name="<?php echo htmlspecialchars( wp_json_encode( $user_quiz_progress, JSON_FORCE_OBJECT ) ); ?>" />
																					<?php
																			}
																			?>
																			<?php
																				$edit_url = get_edit_post_link( $quiz['post']->ID );
																			if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Courses_Builder', 'shared_steps' ) == 'yes' ) {
																				$edit_url = add_query_arg( 'course_id', $course_id, $edit_url );
																			}
																			?>

																			<a href='<?php echo esc_url( $edit_url ); ?>' title='<?php echo esc_html( apply_filters( 'the_title', $quiz['post']->post_title, $quiz['post']->ID ) ); ?>'><span><?php echo apply_filters( 'the_title', $quiz['post']->post_title, $quiz['post']->ID ); ?></span></a>

																		</li>
																	<?php } ?>
																</ul>
																<?php
														}
														?>
													</li>
													<?php

										}
									}

									if ( ! empty( $lesson_quizzes_list ) ) {
										foreach ( $lesson_quizzes_list as $quiz ) {
											?>
												<li class="quiz-item">
												<?php
												if ( ebox_show_user_course_complete( $user_id ) ) {

													$user_quiz_progress              = array();
													$user_quiz_progress['user_id']   = $user_id;
													$user_quiz_progress['course_id'] = $course_id;
													$user_quiz_progress['lesson_id'] = $course_lesson['post']->ID;
													$user_quiz_progress['quiz_id']   = $quiz['post']->ID;

													if ( $quiz['status'] == 'completed' ) {
														$quiz_checked                  = ' checked="checked" ';
														$user_quiz_progress['checked'] = true;
													} else {
														$quiz_checked                  = '';
														$user_quiz_progress['checked'] = false;
													}

													?>
														<input type="checkbox" <?php echo $quiz_checked; ?> class="ebox-mark-lesson-quiz-complete ebox-mark-quiz-complete" data-name="<?php echo htmlspecialchars( wp_json_encode( $user_quiz_progress, JSON_FORCE_OBJECT ) ); ?>" />
														<?php
												}
												?>
												<?php
													$edit_url = get_edit_post_link( $quiz['post']->ID );
												if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Courses_Builder', 'shared_steps' ) == 'yes' ) {
													$edit_url = add_query_arg( 'course_id', $course_id, $edit_url );
												}
												?>
													<a href="<?php echo esc_url( $edit_url ); ?>" title="<?php echo esc_html( apply_filters( 'the_title', $quiz['post']->post_title, $quiz['post']->ID ) ); ?>"><span><?php echo apply_filters( 'the_title', $quiz['post']->post_title, $quiz['post']->ID ); ?></span></a>
													</li>
													<?php
										}
									}
									?>
										</ul>
									</div>
									<?php
							}
							?>
						</div>
					</div>
				<?php } ?>

			<?php } ?>
			<?php
			if ( isset( $course_navigation_admin_pager ) ) {
				if ( $course_navigation_admin_pager['paged'] == $course_navigation_admin_pager['total_pages'] ) {
					$show_course_quizzes = true;
				} else {
					$show_course_quizzes = false;
				}
			} else {
				$show_course_quizzes = true;
			}
			if ( $show_course_quizzes == true ) {

				if ( ! empty( $course_quiz_list ) ) {
					foreach ( $course_quiz_list as $quiz ) {
						?>
						<div id="quiz_list-<?php echo $quiz['post']->ID; ?>" class="quiz_list_item quiz_list_item_global">
							<div class='list_arrow'></div>
							<div class="list_modules">
								<div class="lesson" >
									<?php
									if ( ebox_show_user_course_complete( $user_id ) ) {

										$user_quiz_progress              = array();
										$user_quiz_progress['user_id']   = $user_id;
										$user_quiz_progress['course_id'] = $course_id;
										$user_quiz_progress['quiz_id']   = $quiz['post']->ID;

										if ( $quiz['status'] == 'completed' ) {
											$quiz_checked                  = ' checked="checked" ';
											$user_quiz_progress['checked'] = true;
										} else {
											$quiz_checked                  = '';
											$user_quiz_progress['checked'] = false;
										}
										?>
											<input type="checkbox" <?php echo $quiz_checked; ?> class="ebox-mark-quiz-complete ebox-mark-course-quiz-complete" data-name="<?php echo htmlspecialchars( wp_json_encode( $user_quiz_progress, JSON_FORCE_OBJECT ) ); ?>" />
											<?php
									}
									?>
										<a href="<?php echo esc_url( add_query_arg( 'course_id', $course_id, get_edit_post_link( $quiz['post']->ID ) ) ); ?>" title="<?php echo esc_html( apply_filters( 'the_title', $quiz['post']->post_title, $quiz['post']->ID ) ); ?>"><?php echo apply_filters( 'the_title', $quiz['post']->post_title, $quiz['post']->ID ); ?></a>
									</div>
								</div>
							</div>
							<?php
					}
				}
			}
			?>
		</div> <!-- Closing <div class='ebox_navigation_lesson_topics_list'> -->
		<?php
		$ebox_course_navigation_admin_style = '
		$ebox_course_navigation_admin_meta .list_arrow.expand {
			background: url("' . ebox_LMS_PLUGIN_URL . 'assets/images/gray_arrow_expand.png") no-repeat scroll 0 50% transparent;
			padding: 5px;
		}

		#ebox_course_navigation_admin_meta .list_arrow.collapse {
			background: url("' . ebox_LMS_PLUGIN_URL . 'assets/images/gray_arrow_collapse.png") no-repeat scroll 0 50% transparent;
			padding: 5px;
		}

		#ebox_course_navigation_admin_meta .lesson_incomplete.list_arrow.collapse {
			background: url("' . ebox_LMS_PLUGIN_URL . 'assets/images/gray_arrow_collapse.png") no-repeat scroll 0 50% transparent;
			padding: 5px;
		}

		#ebox_course_navigation_admin_meta .lesson_incomplete.list_arrow.expand {
			background: url("' . ebox_LMS_PLUGIN_URL . 'assets/images/gray_arrow_expand.png") no-repeat scroll 0 50% transparent;
			padding: 5px;
		}
		';
		?>
		<style>
		<?php echo $ebox_course_navigation_admin_style; ?>
		</style>

		<?php
		if ( ( isset( $course_navigation_admin_pager ) ) && ( ! empty( $course_navigation_admin_pager ) ) ) {
			echo ebox_LMS::get_template(
				'ebox_pager.php',
				array(
					'pager_results' => $course_navigation_admin_pager,
					'pager_context' => 'course_navigation_admin',
				)
			);
		}
		?>
		<?php if ( ( $widget['current_step_id'] != 0 ) && ( $widget['current_step_id'] != $course->ID ) ) { ?>
			<p class="widget_course_return">
				<?php esc_html_e( 'Return to', 'ebox' ); ?> <a href='<?php echo esc_url( get_edit_post_link( $course_id ) ); ?>'>
					<?php echo apply_filters( 'the_title', $course->post_title, $course->ID ); ?>
				</a>
			</p>

		<?php } ?>

	<?php if ( ( isset( $widget['show_widget_wrapper'] ) ) && ( $widget['show_widget_wrapper'] == 'true' ) ) { ?>
		</div> <!-- Closing <div id='course_navigation'> -->
	<?php } ?>
	<?php
}

