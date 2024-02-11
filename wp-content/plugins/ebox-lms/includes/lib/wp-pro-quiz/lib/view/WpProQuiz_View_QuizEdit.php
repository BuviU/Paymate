<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// phpcs:disable WordPress.NamingConventions.ValidVariableName,WordPress.NamingConventions.ValidFunctionName,WordPress.NamingConventions.ValidHookName
class WpProQuiz_View_QuizEdit extends WpProQuiz_View_View {

	/**
	 * @var WpProQuiz_Model_Quiz
	 */
	public $quiz;

	public function show_advanced( $get = null ) {
		?>
		<input name="name" id="wpProQuiz_title" type="hidden" class="regular-text" value="<?php echo esc_attr( $this->quiz->getName() ); ?>">
		<input name="text" type="hidden" value="AAZZAAZZ" />
		<div class="wrap wpProQuiz_quizEdit">
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<?php
							// translators: placeholder: quiz.
							printf( esc_html_x( 'Hide %s title', 'placeholder: quiz', 'ebox' ), esc_html( ebox_get_custom_label_lower( 'quiz' ) ) );
							?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php esc_html_e( 'Hide title', 'ebox' ); ?></span>
								</legend>
								<label for="title_hidden">
									<input type="checkbox" id="title_hidden" value="1" name="titleHidden" <?php echo $this->quiz->isTitleHidden() ? 'checked="checked"' : ''; ?> >
									<?php esc_html_e( 'Activate', 'ebox' ); ?>
								</label>
								<p class="description">
									<?php
									// translators: placeholder: quiz.
									printf( esc_html_x( 'The title serves as %s heading.', 'placeholder: quiz', 'ebox' ), esc_html( ebox_get_custom_label_lower( 'quiz' ) ) );
									?>
								</p>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php
							// translators: placeholder: quiz.
							printf( esc_html_x( 'Hide "Restart %s" button', 'placeholder: quiz', 'ebox' ), esc_html( ebox_get_custom_label_lower( 'quiz' ) ) );
							?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span>
									<?php
									// translators: placeholder: quiz.
									printf( esc_html_x( 'Hide "Restart %s" button', 'placeholder: quiz', 'ebox' ), esc_html( ebox_get_custom_label_lower( 'quiz' ) ) );
									?>
									</span>
								</legend>
								<label for="btn_restart_quiz_hidden">
									<input type="checkbox" id="btn_restart_quiz_hidden" value="1" name="btnRestartQuizHidden" <?php echo $this->quiz->isBtnRestartQuizHidden() ? 'checked="checked"' : ''; ?> >
									<?php esc_html_e( 'Activate', 'ebox' ); ?>
								</label>
								<p class="description">
									<?php
									// translators: placeholder: quiz.
									printf( esc_html_x( 'Hide the "Restart %s" button in the Frontend.', 'placeholder: quiz', 'ebox' ), esc_html( ebox_get_custom_label_lower( 'quiz' ) ) );
									?>
								</p>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Hide "View question" button', 'ebox' ); ?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php esc_html_e( 'Hide "View question" button', 'ebox' ); ?></span>
								</legend>
								<label for="btn_view_question_hidden">
									<input type="checkbox" id="btn_view_question_hidden" value="1" name="btnViewQuestionHidden" <?php echo $this->quiz->isBtnViewQuestionHidden() ? 'checked="checked"' : ''; ?> >
									<?php esc_html_e( 'Activate', 'ebox' ); ?>
								</label>
								<p class="description">
									<?php esc_html_e( 'Hide the "View question" button in the Frontend.', 'ebox' ); ?>
								</p>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php
							// translators: questions
							printf( esc_html_x( 'Display %s randomly', 'placeholder: question', 'ebox' ), ebox_get_custom_label_lower( 'questions' ) );
							?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span>
									<?php
									// translators: questions
									printf( esc_html_x( 'Display %s randomly', 'placeholder: question', 'ebox' ), ebox_get_custom_label_lower( 'questions' ) );
									?>
									</span>
								</legend>
								<label for="question_random">
									<input type="checkbox" id="question_random" value="1" name="questionRandom" <?php echo $this->quiz->isQuestionRandom() ? 'checked="checked"' : ''; ?> >
									<?php esc_html_e( 'Activate', 'ebox' ); ?>
								</label>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Display answers randomly', 'ebox' ); ?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php esc_html_e( 'Display answers randomly', 'ebox' ); ?></span>
								</legend>
								<label for="answer_random">
									<input type="checkbox" id="answer_random" value="1" name="answerRandom" <?php echo $this->quiz->isAnswerRandom() ? 'checked="checked"' : ''; ?> >
									<?php esc_html_e( 'Activate', 'ebox' ); ?>
								</label>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
						<?php
							// translators: questions
							printf( esc_html_x( 'Sort %s by category', 'placeholder: question', 'ebox' ), ebox_get_custom_label_lower( 'questions' ) );
						?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span>
									<?php
										// translators: questions
										printf( esc_html_x( 'Sort %s by category', 'placeholder: question', 'ebox' ), ebox_get_custom_label_lower( 'questions' ) );
									?>
									</span>
								</legend>
								<label>
									<input type="checkbox" value="1" name="sortCategories" <?php $this->checked( $this->quiz->isSortCategories() ); ?> >
									<?php esc_html_e( 'Activate', 'ebox' ); ?>
								</label>
								<p class="description">
								<?php
									// translators: questions
									printf( esc_html_x( 'Also works in conjunction with the "display random %s question" option.', 'placeholder: questions', 'ebox' ), ebox_get_custom_label_lower( 'questions' ) );
								?>
								</p>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Time limit', 'ebox' ); ?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php esc_html_e( 'Time limit', 'ebox' ); ?></span>
								</legend>
								<label for="time_limit">
									<input type="number" min="0" class="small-text" id="time_limit" value="<?php echo esc_attr( $this->quiz->getTimeLimit() ); ?>" name="timeLimit"> <?php esc_html_e( 'Seconds', 'ebox' ); ?>
								</label>
								<p class="description">
									<?php esc_html_e( '0 = no limit', 'ebox' ); ?>
								</p>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php
							// translators: placeholder: Quiz.
							printf( esc_html_x( 'Protect %s Answers in Browser Cookie', 'placeholder: Quiz', 'ebox' ), ebox_Custom_Label::get_label( 'quiz' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
							?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span>
									<?php
									// translators: placeholder: Quiz.
									printf( esc_html_x( 'Use cookies for %s Answers', 'placeholder: Quiz', 'ebox' ), ebox_Custom_Label::get_label( 'quiz' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
									?>
									</span>
								</legend>
								<label for="time_limit_cookie">
									<input type="number" min="0" class="small-text" id="time_limit_cookie" value="<?php echo intval( $this->quiz->getTimeLimitCookie() ); ?>" name="timeLimitCookie"> <?php esc_html_e( 'Seconds', 'ebox' ); ?>
								</label>
								<p class="description">
									<?php
									// translators: placeholder: Quiz.
									printf( esc_html_x( "0 = Don't save answers. This option will save the user's answers into a browser cookie until the %s is submitted.", 'placeholder: Quiz', 'ebox' ), ebox_Custom_Label::get_label( 'quiz' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
									?>
								</p>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Statistics', 'ebox' ); ?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php esc_html_e( 'Statistics', 'ebox' ); ?></span>
								</legend>
								<label for="statistics_on">
									<input type="checkbox" id="statistics_on" value="1" name="statisticsOn" <?php echo ( ! isset( $_GET['post'] ) || $this->quiz->isStatisticsOn() ) ? 'checked="checked"' : ''; ?>>
									<?php esc_html_e( 'Activate', 'ebox' ); ?>
								</label>
								<p class="description">
									<?php
									// translators: placeholder: quiz.
									printf( esc_html_x( 'Statistics about right or wrong answers. Statistics will be saved by completed %s, not after every question. The statistics is only visible over administration menu. (internal statistics)', 'placeholder: quiz', 'ebox' ), esc_html( ebox_get_custom_label_lower( 'quiz' ) ) );
									?>
								</p>
							</fieldset>
						</td>
					</tr>
					<tr id="statistics_ip_lock_tr" style="display: none;">
						<th scope="row">
							<?php esc_html_e( 'Statistics IP-lock', 'ebox' ); ?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php esc_html_e( 'Statistics IP-lock', 'ebox' ); ?></span>
								</legend>
								<label for="statistics_ip_lock">
									<input type="number" min="0" class="small-text" id="statistics_ip_lock" value="<?php echo null === $this->quiz->getStatisticsIpLock() ? 0 : esc_attr( $this->quiz->getStatisticsIpLock() ); ?>" name="statisticsIpLock">
									<?php esc_html_e( 'in minutes (recommended 1440 minutes = 1 day)', 'ebox' ); ?>
								</label>
								<p class="description">
									<?php esc_html_e( 'Protect the statistics from spam. Result will only be saved every X minutes from same IP. (0 = deactivated)', 'ebox' ); ?>
								</p>
							</fieldset>
						</td>
					</tr>

					<tr id="statistics_show_profile_tr" style="display: none;">
						<th scope="row">
							<?php esc_html_e( 'View Profile Statistics', 'ebox' ); ?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php esc_html_e( 'View Profile Statistics', 'ebox' ); ?></span>
								</legend>
								<label for="statistics_on">
									<input type="checkbox" id="view_profile_statistics_on" value="1" name="viewProfileStatistics" <?php echo ( ! isset( $_GET['post'] ) || $this->quiz->getViewProfileStatistics() ) ? 'checked="checked"' : ''; ?>>
									<?php esc_html_e( 'Activate', 'ebox' ); ?>
								</label>
								<p class="description">
									<?php
									// translators: placeholder: quiz.
									printf( esc_html_x( 'Enable user to view statistics for this %s on their profile.', 'placeholder: quiz', 'ebox' ), esc_html( ebox_get_custom_label_lower( 'quiz' ) ) );
									?>
								</p>
							</fieldset>
						</td>
					</tr>


					<tr>
						<th scope="row">
							<?php
							// translators: placeholder: quiz.
							printf( esc_html_x( 'Execute %s only once', 'placeholder: quiz', 'ebox' ), esc_html( ebox_get_custom_label_lower( 'quiz' ) ) );
							?>
						</th>
						<td>
							<fieldset>

								<legend class="screen-reader-text">
									<span>
									<?php
									// translators: placeholder: quiz.
									printf( esc_html_x( 'Execute %s only once', 'placeholder: quiz', 'ebox' ), esc_html( ebox_get_custom_label_lower( 'quiz' ) ) );
									?>
									</span>
								</legend>

								<label>
									<input type="checkbox" value="1" name="quizRunOnce" <?php echo $this->quiz->isQuizRunOnce() ? 'checked="checked"' : ''; ?>>
									<?php esc_html_e( 'Activate', 'ebox' ); ?>
								</label>
								<p class="description">
									<?php
									// translators: placeholders: quiz, quiz.
									printf( esc_html_x( 'If you activate this option, the user can complete the %1$s only once. Afterwards the %2$s is blocked for this user.', 'placeholders: quiz, quiz', 'ebox' ), esc_html( ebox_get_custom_label_lower( 'quiz' ) ), esc_html( ebox_get_custom_label_lower( 'quiz' ) ) );
									?>
								</p>

								<div id="wpProQuiz_quiz_run_once_type" style="margin-bottom: 5px; display: none;">
									<?php
									esc_html_e( 'This option applies to:', 'ebox' );

									$quizRunOnceType = $this->quiz->getQuizRunOnceType();
									$quizRunOnceType = ( 0 == $quizRunOnceType ) ? 1 : $quizRunOnceType;
									?>

									<label>
										<input name="quizRunOnceType" type="radio" value="1" <?php echo ( 1 == $quizRunOnceType ) ? 'checked="checked"' : ''; ?>>
										<?php esc_html_e( 'all users', 'ebox' ); ?>
									</label>
									<label>
										<input name="quizRunOnceType" type="radio" value="2" <?php echo ( 2 == $quizRunOnceType ) ? 'checked="checked"' : ''; ?>>
										<?php esc_html_e( 'registered useres only', 'ebox' ); ?>
									</label>
									<label>
										<input name="quizRunOnceType" type="radio" value="3" <?php echo ( 3 == $quizRunOnceType ) ? 'checked="checked"' : ''; ?>>
										<?php esc_html_e( 'anonymous users only', 'ebox' ); ?>
									</label>

									<div id="wpProQuiz_quiz_run_once_cookie" style="margin-top: 10px;">
										<label>
											<input type="checkbox" value="1" name="quizRunOnceCookie" <?php echo $this->quiz->isQuizRunOnceCookie() ? 'checked="checked"' : ''; ?>>
											<?php esc_html_e( 'user identification by cookie', 'ebox' ); ?>
										</label>
										<p class="description">
											<?php esc_html_e( 'If you activate this option, a cookie is set additionally for unregistrated (anonymous) users. This ensures a longer assignment of the user than the simple assignment by the IP address.', 'ebox' ); ?>
										</p>
									</div>

									<div style="margin-top: 15px;">
										<input class="button-secondary" data-nonce="<?php echo esc_attr( wp_create_nonce( 'ebox-wpproquiz-reset-lock' ) ); ?>" type="button" name="resetQuizLock" value="<?php esc_html_e( 'Reset the user identification', 'ebox' ); ?>">
										<span id="resetLockMsg" style="display:none; background-color: rgb(255, 255, 173); border: 1px solid rgb(143, 143, 143); padding: 4px; margin-left: 5px; "><?php esc_html_e( 'User identification has been reset.', 'ebox' ); ?></span>
										<p class="description">
											<?php esc_html_e( 'Resets user identification for all users.', 'ebox' ); ?>
										</p>
									</div>
								</div>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
						<?php
							// translators: placeholder: questions.
							printf( esc_html_x( 'Show only specific number of %s', 'placeholder: questions', 'ebox' ), esc_html( ebox_get_custom_label_lower( 'questions' ) ) );
						?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span>
									<?php
									// translators: placeholder: questions.
									printf( esc_html_x( 'Show only specific number of %s', 'placeholder: questions', 'ebox' ), esc_html( ebox_get_custom_label_lower( 'questions' ) ) );
									?>
									</span>
								</legend>
								<label>
									<input type="checkbox" value="1" name="showMaxQuestion" <?php echo $this->quiz->isShowMaxQuestion() ? 'checked="checked"' : ''; ?>>
									<?php esc_html_e( 'Activate', 'ebox' ); ?>
								</label>
								<p class="description">
								<?php
									// translators: placeholders: questions, questions.
									printf( esc_html_x( 'If you enable this option, maximum number of displayed %1$s will be X from X %2$s', 'placeholders: questions, questions', 'ebox' ), esc_html( ebox_get_custom_label_lower( 'questions' ) ), esc_html( ebox_get_custom_label_lower( 'questions' ) ) );
								?>
								</p>
								<div id="wpProQuiz_showMaxBox" style="display: none;">
									<label>
										<?php
										// translators: questions
										printf( esc_html_x( 'How many %s should be displayed simultaneously:', 'placeholder: questions', 'ebox' ), ebox_get_custom_label( 'questions' ) );
										?>
										<input class="small-text" type="text" name="showMaxQuestionValue" value="<?php echo esc_attr( $this->quiz->getShowMaxQuestionValue() ); ?>">
									</label>
									<label>
										<input type="checkbox" value="1" name="showMaxQuestionPercent" <?php echo $this->quiz->isShowMaxQuestionPercent() ? 'checked="checked"' : ''; ?>>
										<?php esc_html_e( 'in percent', 'ebox' ); ?>
									</label>
								</div>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Prerequisites', 'ebox' ); ?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php esc_html_e( 'Prerequisites', 'ebox' ); ?></span>
								</legend>
								<label>
									<input type="checkbox" value="1" name="prerequisite" <?php $this->checked( $this->quiz->isPrerequisite() ); ?>>
									<?php esc_html_e( 'Activate', 'ebox' ); ?>
								</label>
								<p class="description">
									<?php
									// translators: placeholders: quiz, quiz.
									printf( esc_html_x( 'If you enable this option, you can choose %1$s, which user have to finish before he can start this %2$s.', 'placeholders: quiz, quiz', 'ebox' ), esc_html( ebox_get_custom_label_lower( 'quiz' ) ), esc_html( ebox_get_custom_label_lower( 'quiz' ) ) );
									?>
								</p>
								<p class="description">
									<?php
									// translators: placeholder: quizzes.
									printf( esc_html_x( 'In all selected %s statistic function have to be active. If it is not it will be activated automatically.', 'placeholders: quizzes', 'ebox' ), esc_html( ebox_get_custom_label_lower( 'quizzes' ) ) );
									?>
								</p>
								<div id="prerequisiteBox" style="display: none;">
									<table id="ebox-prerequisite-table">
										<tr>
											<th class="ebox-quiz-prerequisite-list ebox-quiz-prerequisite-list-left">
											<?php
											echo ebox_Custom_Label::get_label( 'quiz' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
											?>
											</th>
											<th class="ebox-quiz-prerequisite-list ebox-quiz-prerequisite-list-center"></th>
											<th class="ebox-quiz-prerequisite-list ebox-quiz-prerequisite-list-right">
											<?php
											// translators: placeholder: quiz.
											printf( esc_html_x( 'Prerequisites (This %s has to be finished)', 'placeholder: quiz', 'ebox' ), esc_html( ebox_get_custom_label_lower( 'quiz' ) ) );
											?>
											</th>
										</tr>
										<tr>
											<td class="ebox-quiz-prerequisite-list ebox-quiz-prerequisite-list-left">
												<select class="ebox-quiz-prerequisite-list" multiple="multiple" size="8" name="quizList">
													<?php
													foreach ( $this->quizList as $list ) {
														if ( in_array( $list['id'], $this->prerequisiteQuizList ) ) {
															continue;
														}

														echo '<option value="' . esc_attr( $list['id'] ) . '" title="' . esc_attr( $list['name'] ) . '">' . wp_kses_post( $list['name'] ) . '</option>';
													}
													?>
												</select>
											</td>
											<td class="ebox-quiz-prerequisite-list ebox-quiz-prerequisite-list-center" style="text-align: center;">
												<div>
													<input type="button" id="btnPrerequisiteAdd" value="&gt;&gt;">
												</div>
												<div>
													<input type="button" id="btnPrerequisiteDelete" value="&lt;&lt;">
												</div>
											</td>
											<td class="ebox-quiz-prerequisite-list ebox-quiz-prerequisite-list-right">
												<select class="ebox-quiz-prerequisite-list" multiple="multiple" size="8" name="prerequisiteList[]">
													<?php
													foreach ( $this->quizList as $list ) {
														if ( ! in_array( $list['id'], $this->prerequisiteQuizList ) ) {
															continue;
														}

															echo '<option value="' . esc_attr( $list['id'] ) . '" title="' . esc_attr( $list['name'] ) . '">' . wp_kses_post( $list['name'] ) . '</option>';
													}
													?>
												</select>
											</td>
										</tr>
									</table>
								</div>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php
							printf(
								// translators: question
								esc_html_x( '%s overview', 'placeholder: question', 'ebox' ),
								ebox_get_custom_label( 'question' )
							)
							?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span>
									<?php
									printf(
										// translators: question
										esc_html_x( '%s overview', 'placeholder: question', 'ebox' ),
										ebox_get_custom_label( 'question' )
									)
									?>
									</span>
								</legend>
								<label>
									<input type="checkbox" value="1" name="showReviewQuestion" <?php $this->checked( $this->quiz->isShowReviewQuestion() ); ?>>
									<?php esc_html_e( 'Activate', 'ebox' ); ?>
								</label>
								<p class="description">
									<?php
									printf(
										// translators: placeholders: quiz, question, questions
										esc_html_x( 'Add at the top of the %1$s a %2$s overview, which allows easy navigation. Additional %3$s can be marked "to review".', 'placeholders: quiz, question, questions', 'ebox' ),
										ebox_get_custom_label_lower( 'quiz' ),
										ebox_get_custom_label_lower( 'question' ),
										ebox_get_custom_label_lower( 'questions' )
									);
									?>
								</p>
								<p class="description">
									<?php
									// translators: placeholders: quiz, quiz.
									echo sprintf( esc_html_x( 'Additional %1$s overview will be displayed, before %2$s is finished.', 'placeholders: quiz, quiz', 'ebox' ), esc_html( ebox_get_custom_label_lower( 'quiz' ) ), esc_html( ebox_get_custom_label_lower( 'quiz' ) ) );
									?>
								</p>

							</fieldset>
						</td>
					</tr>
					<tr class="wpProQuiz_reviewQuestionOptions" style="display: none;">
						<th scope="row">
							<?php
							// translators: placeholder: Quiz.
							echo sprintf( esc_html_x( '%s-summary', 'placeholder: Quiz', 'ebox' ), ebox_Custom_Label::get_label( 'quiz' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
							?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span>
									<?php
									// translators: placeholder: Quiz.
									echo sprintf( esc_html_x( '%s-summary', 'placeholder: Quiz', 'ebox' ), ebox_Custom_Label::get_label( 'quiz' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
									?>
									</span>
								</legend>
								<label>
									<input type="checkbox" value="1" name="quizSummaryHide" <?php $this->checked( $this->quiz->isQuizSummaryHide() ); ?>>
									<?php esc_html_e( 'Deactivate', 'ebox' ); ?>
								</label>
								<p class="description">
									<?php
									// translators: placeholders: quiz, quiz.
									echo sprintf( esc_html_x( 'If you enable this option, no %1$s overview will be displayed, before finishing %2$s.', 'placeholders: quiz, quiz', 'ebox' ), esc_html( ebox_get_custom_label_lower( 'quiz' ) ), esc_html( ebox_get_custom_label_lower( 'quiz' ) ) );
									?>
								</p>
							</fieldset>
						</td>
					</tr>
					<tr class="wpProQuiz_reviewQuestionOptions" style="display: none;">
						<th scope="row">
							<?php
							// translators: placeholder: question.
							printf( esc_html_x( 'Skip %s', 'placeholder: question', 'ebox' ), ebox_get_custom_label_lower( 'question' ) );
							?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span>
									<?php
									// translators: placeholder: question.
									printf( esc_html_x( 'Skip %s', 'placeholder: question', 'ebox' ), ebox_get_custom_label_lower( 'question' ) );
									?>
									</span>
								</legend>
								<label>
									<input type="checkbox" value="1" name="skipQuestionDisabled" <?php $this->checked( $this->quiz->isSkipQuestionDisabled() ); ?>>
									<?php esc_html_e( 'Deactivate', 'ebox' ); ?>
								</label>
								<p class="description">
									<?php
									// translators: placeholder: question, question
									printf( esc_html_x( 'If you enable this option, user won\'t be able to skip %1$s. (only in "Overview -> next" mode). User still will be able to navigate over "%2$s-Overview"', 'placeholder: question, question', 'ebox' ), ebox_get_custom_label_lower( 'question' ), ebox_get_custom_label( 'qusetion' ) );
									?>
								</p>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Admin e-mail notification', 'ebox' ); ?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php esc_html_e( 'Admin e-mail notification', 'ebox' ); ?></span>
								</legend>
								<label>
									<input type="radio" name="emailNotification" value="<?php echo esc_attr( WpProQuiz_Model_Quiz::QUIZ_EMAIL_NOTE_NONE ); ?>" <?php $this->checked( $this->quiz->getEmailNotification(), WpProQuiz_Model_Quiz::QUIZ_EMAIL_NOTE_NONE ); ?>>
									<?php esc_html_e( 'Deactivate', 'ebox' ); ?>
								</label>
								<label>
									<input type="radio" name="emailNotification" value="<?php echo esc_attr( WpProQuiz_Model_Quiz::QUIZ_EMAIL_NOTE_REG_USER ); ?>" <?php $this->checked( $this->quiz->getEmailNotification(), WpProQuiz_Model_Quiz::QUIZ_EMAIL_NOTE_REG_USER ); ?>>
									<?php esc_html_e( 'for registered users only', 'ebox' ); ?>
								</label>
								<label>
									<input type="radio" name="emailNotification" value="<?php echo esc_attr( WpProQuiz_Model_Quiz::QUIZ_EMAIL_NOTE_ALL ); ?>" <?php $this->checked( $this->quiz->getEmailNotification(), WpProQuiz_Model_Quiz::QUIZ_EMAIL_NOTE_ALL ); ?>>
									<?php esc_html_e( 'for all users', 'ebox' ); ?>
								</label>
								<p class="description">
									<?php
									// translators: placeholder: quiz.
									echo sprintf( esc_html_x( 'If you enable this option, you will be informed if a user completes this %s.', 'placeholder: quiz', 'ebox' ), esc_html( ebox_get_custom_label_lower( 'quiz' ) ) );
									?>
								</p>
								<p class="description">
									<?php esc_html_e( 'E-Mail settings can be edited in global settings.', 'ebox' ); ?>
								</p>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php esc_html_e( 'User e-mail notification', 'ebox' ); ?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php esc_html_e( 'User e-mail notification', 'ebox' ); ?></span>
								</legend>
								<label>
									<input type="checkbox" name="userEmailNotification" value="1" <?php $this->checked( $this->quiz->isUserEmailNotification() ); ?>>
									<?php esc_html_e( 'Activate', 'ebox' ); ?>
								</label>
								<p class="description">
									<?php
									// translators: placeholder: course.
									echo sprintf( esc_html_x( 'If you enable this option, an email is sent with their %s result to the user. (only registered users)', 'placeholder: course', 'ebox' ), esc_html( ebox_get_custom_label_lower( 'course' ) ) );
									?>
								</p>
								<p class="description">
									<?php esc_html_e( 'E-Mail settings can be edited in global settings.', 'ebox' ); ?>
								</p>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Autostart', 'ebox' ); ?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php esc_html_e( 'Autostart', 'ebox' ); ?></span>
								</legend>
								<label>
									<input type="checkbox" name="autostart" value="1" <?php $this->checked( $this->quiz->isAutostart() ); ?>>
									<?php esc_html_e( 'Activate', 'ebox' ); ?>
								</label>
								<p class="description">
									<?php
									// translators: placeholder: quiz.
									echo sprintf( esc_html_x( 'If you enable this option, the %s will start automatically after the page is loaded.', 'placeholder: quiz', 'ebox' ), esc_html( ebox_get_custom_label_lower( 'quiz' ) ) );
									?>
								</p>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php
							// translators: placeholder: quiz.
							echo sprintf( esc_html_x( 'Only registered users are allowed to start the %s', 'placeholder: quiz', 'ebox' ), esc_html( ebox_get_custom_label_lower( 'quiz' ) ) );
							?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span>
									<?php
									// translators: placeholder: quiz.
									echo sprintf( esc_html_x( 'Only registered users are allowed to start the %s', 'placeholder: quiz', 'ebox' ), esc_html( ebox_get_custom_label_lower( 'quiz' ) ) );
									?>
									</span>
								</legend>
								<label>
									<input type="checkbox" name="startOnlyRegisteredUser" value="1" <?php $this->checked( $this->quiz->isStartOnlyRegisteredUser() ); ?>>
									<?php esc_html_e( 'Activate', 'ebox' ); ?>
								</label>
								<p class="description">
									<?php
									// translators: placeholder: quiz.
									echo sprintf( esc_html_x( 'If you enable this option, only registered users allowed start the %s.', 'placeholder: quiz', 'ebox' ), esc_html( ebox_get_custom_label_lower( 'quiz' ) ) );
									?>
								</p>
							</fieldset>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php
	}

	public function show_templates( $get = null ) {
		$template_loaded_id = 0;
		if ( ( isset( $_GET['templateLoadId'] ) ) && ( ! empty( $_GET['templateLoadId'] ) ) ) {
			$template_loaded_id = intval( $_GET['templateLoadId'] );
		}
		?>
		<div class="wrap wpProQuiz_quizEdit">
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Use Template', 'ebox' ); ?>
						</th>
						<td>
							<select name="templateLoadId">
								<option value=""><?php esc_html_e( 'Select Template', 'ebox' ); ?></option>
								<?php
								foreach ( $this->templates as $template ) {
									echo '<option ' . selected( $template_loaded_id, $template->getTemplateId() ) . ' value="', esc_attr( $template->getTemplateId() ), '">', esc_html( $template->getName() ), '</option>';
								}
								?>
							</select>
							<input type="submit" name="templateLoad" value="<?php esc_html_e( 'load template', 'ebox' ); ?>" class="button-primary">
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Save as Template', 'ebox' ); ?>
						</th>
						<td>
							<input type="text" placeholder="<?php esc_html_e( 'template name', 'ebox' ); ?>" class="regular-text" name="templateName" style="border: 1px solid rgb(255, 134, 134);">
							<select name="templateSaveList">
								<option value="0">=== <?php esc_html_e( 'Create new template', 'ebox' ); ?> === </option>
								<?php
								foreach ( $this->templates as $template ) {
									echo '<option value="', esc_attr( $template->getTemplateId() ), '">', esc_html( $template->getName() ), '</option>';
								}
								?>
							</select>

							<input type="submit" name="template" class="button-primary" id="wpProQuiz_saveTemplate" value="<?php esc_html_e( 'Save as template', 'ebox' ); ?>">
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php
	}

	public function resultOptions() {
		?>
		<div class="wrap wpProQuiz_quizEdit">
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Show average points', 'ebox' ); ?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php esc_html_e( 'Show average points', 'ebox' ); ?></span>
								</legend>
								<label>
									<input type="checkbox" value="1" name="showAverageResult" <?php $this->checked( $this->quiz->isShowAverageResult() ); ?>>
									<?php esc_html_e( 'Activate', 'ebox' ); ?>
								</label>
								<p class="description">
									<?php esc_html_e( 'Statistics-function must be enabled.', 'ebox' ); ?>
								</p>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Show category score', 'ebox' ); ?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php esc_html_e( 'Show category score', 'ebox' ); ?></span>
								</legend>
								<label>
									<input type="checkbox" name="showCategoryScore" value="1" <?php $this->checked( $this->quiz->isShowCategoryScore() ); ?>>
									<?php esc_html_e( 'Activate', 'ebox' ); ?>
								</label>
								<p class="description">
									<?php esc_html_e( 'If you enable this option, the results of each category is displayed on the results page.', 'ebox' ); ?>
								</p>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php
							printf(
								// translators: questions
								esc_html_x( 'Hide correct %s - display', 'placeholder: questions', 'ebox' ),
								ebox_get_custom_label( 'questions' )
							)
							?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span>
									<?php
									printf(
										// translators: questions
										esc_html_x( 'Hide correct %s - display', 'placeholder: questions', 'ebox' ),
										ebox_get_custom_label( 'questions' )
									)
									?>
									</span>
								</legend>
								<label>
									<input type="checkbox" name="hideResultCorrectQuestion" value="1" <?php $this->checked( $this->quiz->isHideResultCorrectQuestion() ); ?>>
									<?php esc_html_e( 'Activate', 'ebox' ); ?>
								</label>
								<p class="description">
									<?php
									printf(
										// translators: questions
										esc_html_x( 'If you select this option, no longer the number of correctly answered %s are dispayed on the results page.', 'placeholder: questions', 'ebox' ),
										ebox_get_custom_label( 'questions' )
									)
									?>
								</p>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php
							// translators: placeholder: quiz.
							printf( esc_html_x( 'Hide %s time - display', 'placeholder: quiz', 'ebox' ), esc_html( ebox_get_custom_label_lower( 'quiz' ) ) );
							?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span>
									<?php
									// translators: placeholder: quiz.
									printf( esc_html_x( 'Hide %s time - display', 'placeholder: quiz', 'ebox' ), esc_html( ebox_get_custom_label_lower( 'quiz' ) ) );
									?>
									</span>
								</legend>
								<label>
									<input type="checkbox" name="hideResultQuizTime" value="1" <?php $this->checked( $this->quiz->isHideResultQuizTime() ); ?>>
									<?php esc_html_e( 'Activate', 'ebox' ); ?>
								</label>
								<p class="description">
									<?php
									// translators: placeholder: quiz.
									echo sprintf( esc_html_x( 'If you enable this option, the time for finishing the %s won\'t be displayed on the results page anymore.', 'placeholder: quiz', 'ebox' ), esc_html( ebox_get_custom_label_lower( 'quiz' ) ) );
									?>
								</p>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Hide score - display', 'ebox' ); ?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php esc_html_e( 'Hide score - display', 'ebox' ); ?></span>
								</legend>
								<label>
									<input type="checkbox" name="hideResultPoints" value="1" <?php $this->checked( $this->quiz->isHideResultPoints() ); ?>>
									<?php esc_html_e( 'Activate', 'ebox' ); ?>
								</label>
								<p class="description">
									<?php esc_html_e( 'If you enable this option, final score won\'t be displayed on the results page anymore.', 'ebox' ); ?>
								</p>
							</fieldset>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php
	}

	public function questionOptions() {
		?>
		<div class="wrap wpProQuiz_quizEdit">
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Show points', 'ebox' ); ?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php esc_html_e( 'Show points', 'ebox' ); ?></span>
								</legend>
								<label for="show_points">
									<input type="checkbox" id="show_points" value="1" name="showPoints" <?php echo $this->quiz->isShowPoints() ? 'checked="checked"' : ''; ?> >
									<?php esc_html_e( 'Activate', 'ebox' ); ?>
								</label>
								<p class="description">
									<?php
									// translators: placeholder: quiz.
									echo sprintf( esc_html_x( 'Shows in %s, how many points are reachable for respective question.', 'placeholder: quiz', 'ebox' ), esc_html( ebox_get_custom_label_lower( 'quiz' ) ) );
									?>
								</p>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Number answers', 'ebox' ); ?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php esc_html_e( 'Number answers', 'ebox' ); ?></span>
								</legend>
								<label>
									<input type="checkbox" value="1" name="numberedAnswer" <?php echo $this->quiz->isNumberedAnswer() ? 'checked="checked"' : ''; ?>>
									<?php esc_html_e( 'Activate', 'ebox' ); ?>
								</label>
								<p class="description">
									<?php esc_html_e( 'If this option is activated, all answers are numbered (only single and multiple choice)', 'ebox' ); ?>
								</p>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Hide correct- and incorrect message', 'ebox' ); ?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php esc_html_e( 'Hide correct- and incorrect message', 'ebox' ); ?></span>
								</legend>
								<label>
									<input type="checkbox" value="1" name="hideAnswerMessageBox" <?php echo $this->quiz->isHideAnswerMessageBox() ? 'checked="checked"' : ''; ?>>
									<?php esc_html_e( 'Activate', 'ebox' ); ?>
								</label>
								<p class="description">
									<?php esc_html_e( 'If you enable this option, no correct- or incorrect message will be displayed.', 'ebox' ); ?>
								</p>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Correct and incorrect answer mark', 'ebox' ); ?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php esc_html_e( 'Correct and incorrect answer mark', 'ebox' ); ?></span>
								</legend>
								<label>
									<input type="checkbox" value="1" name="disabledAnswerMark" <?php echo $this->quiz->isDisabledAnswerMark() ? 'checked="checked"' : ''; ?>>
									<?php esc_html_e( 'Deactivate', 'ebox' ); ?>
								</label>
								<p class="description">
									<?php esc_html_e( 'If you enable this option, answers won\'t be color highlighted as correct or incorrect. ', 'ebox' ); ?>
								</p>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
						<?php
							printf(
								// translators: question
								esc_html_x( 'Force user to answer each %s', 'placeholder: question', 'ebox' ),
								ebox_get_custom_label_lower( 'question' )
							);
						?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span>
									<?php
									printf(
									// translators: question
										esc_html_x( 'Force user to answer each %s', 'placeholder: question', 'ebox' ),
										ebox_get_custom_label_lower( 'question' )
									);
									?>
									</span>
								</legend>
								<label>
									<input type="checkbox" value="1" name="forcingQuestionSolve" <?php $this->checked( $this->quiz->isForcingQuestionSolve() ); ?>>
									<?php esc_html_e( 'Activate', 'ebox' ); ?>
								</label>
								<p class="description">
									<?php
									printf(
										// translators: question
										esc_html_x( 'If you enable this option, the user is forced to answer each %s', 'placeholder: question', 'ebox' ),
										ebox_get_custom_label_lower( 'question' )
									);
									?>
									<br>
									<?php
									printf(
										// translators: question, quiz
										esc_html_x( 'If the option "%1$s overview" is activated, this notification will appear after the end of the %2$s, otherwise after each %2$s', 'placeholder: question, quiz', 'ebox' ),
										ebox_get_custom_label_lower( 'question' ),
										ebox_get_custom_label_lower( 'quiz' )
									);
									?>
								</p>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php
							printf(
								// translators: question
								esc_html_x( 'Hide %s position overview', 'placeholder: question', 'ebox' ),
								ebox_get_custom_label_lower( 'question' )
							);
							?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span>
									<?php
										printf(
											// translators: question
											esc_html_x( 'Hide %s position overview', 'placeholder: question', 'ebox' ),
											ebox_get_custom_label_lower( 'question' )
										);
									?>
									</span>
								</legend>
								<label>
									<input type="checkbox" value="1" name="hideQuestionPositionOverview" <?php $this->checked( $this->quiz->isHideQuestionPositionOverview() ); ?>>
									<?php esc_html_e( 'Activate', 'ebox' ); ?>
								</label>
								<p class="description">
									<?php
									printf(
										// translators: question
										esc_html_x( 'If you enable this option, the %s position overview is hidden.', 'placeholder: question', 'ebox' ),
										ebox_get_custom_label_lower( 'question' )
									);
									?>
								</p>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php
							printf(
								// translators: question
								esc_html_x( 'Hide %s numbering', 'placeholder: question', 'ebox' ),
								ebox_get_custom_label_lower( 'question' )
							);
							?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span>
									<?php
									printf(
										// translators: question
										esc_html_x( 'Hide %s numbering', 'placeholder: question', 'ebox' ),
										ebox_get_custom_label_lower( 'question' )
									);
									?>
									</span>
								</legend>
								<label>
									<input type="checkbox" value="1" name="hideQuestionNumbering" <?php $this->checked( $this->quiz->isHideQuestionNumbering() ); ?>>
									<?php esc_html_e( 'Activate', 'ebox' ); ?>
								</label>
								<p class="description">
									<?php
									printf(
										// translators: question
										esc_html_e( 'If you enable this option, the %s numbering is hidden.', 'ebox' ),
										ebox_get_custom_label_lower( 'question' )
									);
									?>
								</p>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Display category', 'ebox' ); ?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php esc_html_e( 'Display category', 'ebox' ); ?></span>
								</legend>
								<label>
									<input type="checkbox" value="1" name="showCategory" <?php $this->checked( $this->quiz->isShowCategory() ); ?>>
									<?php esc_html_e( 'Activate', 'ebox' ); ?>
								</label>
								<p class="description">
									<?php esc_html_e( 'If you enable this option, category will be displayed in the question.', 'ebox' ); ?>
								</p>
							</fieldset>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<?php
	}

	public function leaderboardOptions() {
		?>
		<div class="wrap wpProQuiz_quizEdit">
			<p><?php esc_html_e( 'The leaderboard allows users to enter results in public list and to share the result this way.', 'ebox' ); ?></p>
			<p><?php esc_html_e( 'The leaderboard works independent from internal statistics function.', 'ebox' ); ?></p>
			<table class="form-table">
				<tbody id="toplistBox">
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Leaderboard', 'ebox' ); ?>
						</th>
						<td>
							<label>
								<input type="checkbox" name="toplistActivated" value="1" <?php echo $this->quiz->isToplistActivated() ? 'checked="checked"' : ''; ?>>
								<?php esc_html_e( 'Activate', 'ebox' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Who can sign up to the list', 'ebox' ); ?>
						</th>
						<td>
							<label>
								<input name="toplistDataAddPermissions" type="radio" value="1" <?php echo $this->quiz->getToplistDataAddPermissions() == 1 ? 'checked="checked"' : ''; ?>>
								<?php esc_html_e( 'all users', 'ebox' ); ?>
							</label>
							<label>
								<input name="toplistDataAddPermissions" type="radio" value="2" <?php echo $this->quiz->getToplistDataAddPermissions() == 2 ? 'checked="checked"' : ''; ?>>
								<?php esc_html_e( 'registered users only', 'ebox' ); ?>
							</label>
							<label>
								<input name="toplistDataAddPermissions" type="radio" value="3" <?php echo $this->quiz->getToplistDataAddPermissions() == 3 ? 'checked="checked"' : ''; ?>>
								<?php esc_html_e( 'anonymous users only', 'ebox' ); ?>
							</label>
							<p class="description">
								<?php esc_html_e( 'Not registered users have to enter name and e-mail (e-mail won\'t be displayed)', 'ebox' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php esc_html_e( 'insert automatically', 'ebox' ); ?>
						</th>
						<td>
							<label>
								<input name="toplistDataAddAutomatic" type="checkbox" value="1" <?php $this->checked( $this->quiz->isToplistDataAddAutomatic() ); ?>>
								<?php esc_html_e( 'Activate', 'ebox' ); ?>
							</label>
							<p class="description">
								<?php esc_html_e( 'If you enable this option, logged in users will be automatically entered into leaderboard', 'ebox' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php esc_html_e( 'display captcha', 'ebox' ); ?>
						</th>
						<td>
							<label>
								<input type="checkbox" name="toplistDataCaptcha" value="1" <?php echo $this->quiz->isToplistDataCaptcha() ? 'checked="checked"' : ''; ?> <?php echo $this->captchaIsInstalled ? '' : 'disabled="disabled"'; ?>>
								<?php esc_html_e( 'Activate', 'ebox' ); ?>
							</label>
							<p class="description">
								<?php esc_html_e( 'If you enable this option, additional captcha will be displayed for users who are not registered.', 'ebox' ); ?>
							</p>
							<p class="description" style="color: red;">
								<?php esc_html_e( 'This option requires additional plugin:', 'ebox' ); ?>
									<a href="http://wordpress.org/extend/plugins/really-simple-captcha/" target="_blank">Really Simple CAPTCHA</a>
							</p>
							<?php if ( $this->captchaIsInstalled ) { ?>
							<p class="description" style="color: green;">
								<?php esc_html_e( 'Plugin has been detected.', 'ebox' ); ?>
							</p>
							<?php } else { ?>
							<p class="description" style="color: red;">
								<?php esc_html_e( 'Plugin is not installed.', 'ebox' ); ?>
							</p>
							<?php } ?>

						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Sort list by', 'ebox' ); ?>
						</th>
						<td>
							<label>
								<input name="toplistDataSort" type="radio" value="1" <?php echo ( $this->quiz->getToplistDataSort() == 1 ) ? 'checked="checked"' : ''; ?>>
								<?php esc_html_e( 'best user', 'ebox' ); ?>
							</label>
							<label>
								<input name="toplistDataSort" type="radio" value="2" <?php echo ( $this->quiz->getToplistDataSort() == 2 ) ? 'checked="checked"' : ''; ?>>
								<?php esc_html_e( 'newest entry', 'ebox' ); ?>
							</label>
							<label>
								<input name="toplistDataSort" type="radio" value="3" <?php echo ( $this->quiz->getToplistDataSort() == 3 ) ? 'checked="checked"' : ''; ?>>
								<?php esc_html_e( 'oldest entry', 'ebox' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Users can apply multiple times', 'ebox' ); ?>
						</th>
						<td>
							<div>
								<label>
									<input type="checkbox" name="toplistDataAddMultiple" value="1" <?php echo $this->quiz->isToplistDataAddMultiple() ? 'checked="checked"' : ''; ?>>
									<?php esc_html_e( 'Activate', 'ebox' ); ?>
								</label>
							</div>
							<div id="toplistDataAddBlockBox" style="display: none;">
								<label>
									<?php esc_html_e( 'User can apply after:', 'ebox' ); ?>
									<input type="number" min="0" class="small-text" name="toplistDataAddBlock" value="<?php echo esc_attr( $this->quiz->getToplistDataAddBlock() ); ?>">
										<?php esc_html_e( 'minute', 'ebox' ); ?>
								</label>
							</div>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php esc_html_e( 'How many entries should be displayed', 'ebox' ); ?>
						</th>
						<td>
							<div>
								<label>
									<input type="number" min="0" class="small-text" name="toplistDataShowLimit" value="<?php echo esc_attr( $this->quiz->getToplistDataShowLimit() ); ?>">
									<?php esc_html_e( 'Entries', 'ebox' ); ?>
								</label>
							</div>
						</td>
					</tr>
					<tr id="AutomaticallyDisplayLeaderboard">
						<th scope="row">
							<?php
							// translators: placeholder: quiz.
							echo sprintf( esc_html_x( 'Automatically display leaderboard in %s result', 'placeholder: quiz', 'ebox' ), esc_html( ebox_get_custom_label_lower( 'quiz' ) ) );
							?>
						</th>
						<td>
							<div style="margin-top: 6px;">
								<?php esc_html_e( 'Where should leaderboard be displayed:', 'ebox' ); ?><br>
								<label style="margin-right: 5px; margin-left: 5px;">
									<input type="radio" name="toplistDataShowIn" value="0" <?php echo ( $this->quiz->getToplistDataShowIn() == 0 ) ? 'checked="checked"' : ''; ?>>
									<?php esc_html_e( 'don\'t display', 'ebox' ); ?>
								</label>
								<label>
									<input type="radio" name="toplistDataShowIn" value="1" <?php echo ( $this->quiz->getToplistDataShowIn() == 1 ) ? 'checked="checked"' : ''; ?>>
									<?php esc_html_e( 'below the "result text"', 'ebox' ); ?>
								</label>

								<label>
									<input type="radio" name="toplistDataShowIn" value="2" <?php echo ( $this->quiz->getToplistDataShowIn() == 2 ) ? 'checked="checked"' : ''; ?>>
									<?php esc_html_e( 'in a button', 'ebox' ); ?>
								</label>

							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php
	}

	public function quizMode() {
		?>
		<style>
.wpProQuiz_quizModus th, .wpProQuiz_quizModus td {
	border-right: 1px solid #A0A0A0;
	padding: 5px;
}
</style>

		<div class="wrap wpProQuiz_quizEdit">
			<table style="width: 100%; border-collapse: collapse; border: 1px solid #A0A0A0;" class="wpProQuiz_quizModus">
				<thead>
					<tr>
						<th style="width: 25%;"><?php esc_html_e( 'Normal', 'ebox' ); ?></th>
						<th style="width: 25%;"><?php esc_html_e( 'Normal + Back-Button', 'ebox' ); ?></th>
						<th style="width: 25%;"><?php esc_html_e( 'Check -> continue', 'ebox' ); ?></th>
						<th style="width: 25%;">
						<?php
							printf(
								// translators: questions
								esc_html_x( '%s below each other', 'placeholder: question', 'ebox' ),
								ebox_get_custom_label( 'questions' )
							);
						?>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><label><input type="radio" name="quizModus" value="0" <?php $this->checked( $this->quiz->getQuizModus(), WpProQuiz_Model_Quiz::QUIZ_MODUS_NORMAL ); ?>> <?php esc_html_e( 'Activate', 'ebox' ); ?></label></td>
						<td><label><input type="radio" name="quizModus" value="1" <?php $this->checked( $this->quiz->getQuizModus(), WpProQuiz_Model_Quiz::QUIZ_MODUS_BACK_BUTTON ); ?>> <?php esc_html_e( 'Activate', 'ebox' ); ?></label></td>
						<td><label><input type="radio" name="quizModus" value="2" <?php $this->checked( $this->quiz->getQuizModus(), WpProQuiz_Model_Quiz::QUIZ_MODUS_CHECK ); ?>> <?php esc_html_e( 'Activate', 'ebox' ); ?></label></td>
						<td><label><input type="radio" name="quizModus" value="3" <?php $this->checked( $this->quiz->getQuizModus(), WpProQuiz_Model_Quiz::QUIZ_MODUS_SINGLE ); ?>> <?php esc_html_e( 'Activate', 'ebox' ); ?></label></td>
					</tr>
					<tr>
						<td>
							<?php
							// translators: placeholder: questions, quiz.
							printf( esc_html_x( 'Displays all %1$s sequentially, "right" or "false" will be displayed at the end of the %2$s.', 'placeholder: quiz', 'ebox' ), esc_html( ebox_get_custom_label_lower( 'questions' ) ), esc_html( ebox_get_custom_label_lower( 'quiz' ) ) );
							?>
						</td>
						<td>
							<?php
							// translators: question
							printf( esc_html_x( 'Allows to use the back button in a %s.', 'placeholder: question', 'ebox' ), ebox_get_custom_label_lower( 'question' ) )
							?>
						</td>
						<td>
							<?php
							// translators: question
								printf( esc_html_x( 'Shows "right or wrong" after each %s.', 'placeholder: question', 'ebox' ), ebox_get_custom_label_lower( 'question' ) )
							?>
						</td>
						<td>
							<?php
							// translators: questions
								printf( esc_html_x( 'If this option is activated, all answers are displayed below each other, i.e. all %s are on a single page.', 'placeholder: questions', 'ebox' ), ebox_get_custom_label_lower( 'questions' ) )
							?>
						</td>
					</tr>
					<tr>
						<td>

						</td>
						<td>

						</td>
						<td>

						</td>
						<td>

						</td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td>
						<?php
							// translators: s
							printf( esc_html_x( 'How many %s to be displayed on a page.', 'placeholder: questions', 'ebox' ), ebox_get_custom_label_lower( 'questions' ) )
						?>
							<br>
							<input type="number" name="questionsPerPage" value="<?php echo esc_attr( $this->quiz->getQuestionsPerPage() ); ?>" min="0">
							<span class="description">
								<?php esc_html_e( '(0 = All on one page)', 'ebox' ); ?>
							</span>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php
	}

	public function form() {
		$forms = $this->forms;
		$index = 0;

		if ( ! is_array( $forms ) ) {
			$forms = array();
		}

		if ( ! count( $forms ) ) {
			$forms = array( new WpProQuiz_Model_Form(), new WpProQuiz_Model_Form() );
		} else {
			array_unshift( $forms, new WpProQuiz_Model_Form() );
		}

		?>
		<div class="wrap wpProQuiz_quizEdit">

			<p class="description">
				<?php esc_html_e( 'You can create custom fields, e.g. to request the name or the e-mail address of the users.', 'ebox' ); ?>
			</p>
			<p class="description">
				<?php esc_html_e( 'The statistic function have to be enabled.', 'ebox' ); ?>
			</p>

			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Custom fields enable', 'ebox' ); ?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php esc_html_e( 'Custom fields enable', 'ebox' ); ?></span>
								</legend>
								<label>
									<input type="checkbox" id="formActivated" value="1" name="formActivated" <?php $this->checked( $this->quiz->isFormActivated() ); ?>>
									<?php esc_html_e( 'Activate', 'ebox' ); ?>
								</label>
								<p class="description">
									<?php esc_html_e( 'If you enable this option, custom fields are enabled.', 'ebox' ); ?>
								</p>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Display position', 'ebox' ); ?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php esc_html_e( 'Display position', 'ebox' ); ?></span>
								</legend>
								<?php esc_html_e( 'Where should the fields be displayed:', 'ebox' ); ?><br>
								<label>
									<input type="radio" value="<?php echo esc_attr( WpProQuiz_Model_Quiz::QUIZ_FORM_POSITION_START ); ?>" name="formShowPosition" <?php $this->checked( $this->quiz->getFormShowPosition(), WpProQuiz_Model_Quiz::QUIZ_FORM_POSITION_START ); ?>>
									<?php
									// translators: placeholder: quiz.
									echo sprintf( esc_html_x( 'On the %s startpage', 'placeholder: quiz', 'ebox' ), esc_html( ebox_get_custom_label_lower( 'quiz' ) ) );
									?>
								</label>
								<label>
									<input type="radio" value="<?php echo esc_attr( WpProQuiz_Model_Quiz::QUIZ_FORM_POSITION_END ); ?>" name="formShowPosition" <?php $this->checked( $this->quiz->getFormShowPosition(), WpProQuiz_Model_Quiz::QUIZ_FORM_POSITION_END ); ?> >
									<?php
									// translators: placeholders: quiz, quiz.
									echo sprintf( esc_html_x( 'At the end of the %1$s (before the %2$s result)', 'At the end of the quiz (before the quiz result)', 'ebox' ), esc_html( ebox_get_custom_label_lower( 'quiz' ) ), esc_html( ebox_get_custom_label_lower( 'quiz' ) ) );
									?>
								</label>
							</fieldset>
						</td>
					</tr>
				</tbody>
			</table>

			<div style="margin-top: 10px; padding: 10px; border: 1px solid #C2C2C2;">
				<table style=" width: 100%; text-align: left; " id="form_table">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Field name', 'ebox' ); ?></th>
							<th><?php esc_html_e( 'Type', 'ebox' ); ?></th>
							<th><?php esc_html_e( 'Required?', 'ebox' ); ?></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ( $forms as $form ) {
							$checkType = $this->selectedArray(
								$form->getType(),
								array(
									WpProQuiz_Model_Form::FORM_TYPE_TEXT,
									WpProQuiz_Model_Form::FORM_TYPE_TEXTAREA,
									WpProQuiz_Model_Form::FORM_TYPE_CHECKBOX,
									WpProQuiz_Model_Form::FORM_TYPE_SELECT,
									WpProQuiz_Model_Form::FORM_TYPE_RADIO,
									WpProQuiz_Model_Form::FORM_TYPE_NUMBER,
									WpProQuiz_Model_Form::FORM_TYPE_EMAIL,
									WpProQuiz_Model_Form::FORM_TYPE_YES_NO,
									WpProQuiz_Model_Form::FORM_TYPE_DATE,
								)
							);
							?>
						<tr <?php echo 0 == $index++ ? 'style="display: none;"' : ''; ?>>
							<td>
								<input type="text" name="form[][fieldname]" value="<?php echo esc_attr( $form->getFieldname() ); ?>" class="regular-text"/>
							</td>
							<td style="position: relative;">
								<select name="form[][type]">
									<option value="<?php echo esc_attr( WpProQuiz_Model_Form::FORM_TYPE_TEXT ); ?>" <?php echo $checkType[0]; ?>><?php esc_html_e( 'Text', 'ebox' ); ?></option> <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									<option value="<?php echo esc_attr( WpProQuiz_Model_Form::FORM_TYPE_TEXTAREA ); ?>" <?php echo $checkType[1]; ?>><?php esc_html_e( 'TextArea', 'ebox' ); ?></option> <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									<option value="<?php echo esc_attr( WpProQuiz_Model_Form::FORM_TYPE_CHECKBOX ); ?>" <?php echo $checkType[2]; ?>><?php esc_html_e( 'Checkbox', 'ebox' ); ?></option> <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									<option value="<?php echo esc_attr( WpProQuiz_Model_Form::FORM_TYPE_SELECT ); ?>" <?php echo $checkType[3]; ?>><?php esc_html_e( 'Drop-Down menu', 'ebox' ); ?></option> <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									<option value="<?php echo esc_attr( WpProQuiz_Model_Form::FORM_TYPE_RADIO ); ?>" <?php echo $checkType[4]; ?>><?php esc_html_e( 'Radio', 'ebox' ); ?></option> <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									<option value="<?php echo esc_attr( WpProQuiz_Model_Form::FORM_TYPE_NUMBER ); ?>" <?php echo $checkType[5]; ?>><?php esc_html_e( 'Number', 'ebox' ); ?></option> <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									<option value="<?php echo esc_attr( WpProQuiz_Model_Form::FORM_TYPE_EMAIL ); ?>" <?php echo $checkType[6]; ?>><?php esc_html_e( 'Email', 'ebox' ); ?></option> <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									<option value="<?php echo esc_attr( WpProQuiz_Model_Form::FORM_TYPE_YES_NO ); ?>" <?php echo $checkType[7]; ?>><?php esc_html_e( 'Yes/No', 'ebox' ); ?></option> <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									<option value="<?php echo esc_attr( WpProQuiz_Model_Form::FORM_TYPE_DATE ); ?>" <?php echo $checkType[8]; ?>><?php esc_html_e( 'Date', 'ebox' ); ?></option> <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</select>

								<a href="#" class="editDropDown"><?php esc_html_e( 'Edit list', 'ebox' ); ?></a>

								<div class="dropDownEditBox" style="position: absolute; border: 1px solid #AFAFAF; background: #EBEBEB; padding: 5px; bottom: 0;right: 0;box-shadow: 1px 1px 1px 1px #AFAFAF; display: none;">
									<h4><?php esc_html_e( 'One entry per line', 'ebox' ); ?></h4>
									<div>
										<textarea rows="5" cols="50" name="form[][data]"><?php echo $form->getData() === null ? '' : esc_textarea( implode( "\n", $form->getData() ) ); ?></textarea>
									</div>

									<input type="button" value="<?php esc_html_e( 'OK', 'ebox' ); ?>" class="button-primary">
								</div>
							</td>
							<td>
								<input type="checkbox" name="form[][required]" value="1" <?php $this->checked( $form->isRequired() ); ?>>
							</td>
							<td>
								<input type="button" name="form_delete" value="<?php esc_html_e( 'Delete', 'ebox' ); ?>" class="button-secondary">
								<a class="form_move button-secondary" href="#" style="cursor:move;"><?php esc_html_e( 'Move', 'ebox' ); ?></a>

								<input type="hidden" name="form[][form_id]" value="<?php echo esc_attr( $form->getFormId() ); ?>">
								<input type="hidden" name="form[][form_delete]" value="0">
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>

				<div style="margin-top: 10px;">
					<input type="button" name="form_add" id="form_add" value="<?php esc_html_e( 'Add field', 'ebox' ); ?>" class="button-secondary">
				</div>
			</div>
		</div>
		<?php
	}

	public function resultText() {
		return;
		?>
		<div class="wrap wpProQuiz_quizEdit">
			<h3 class="hndle"><?php esc_html_e( 'Results text', 'ebox' ); ?> <?php esc_html_e( '(optional)', 'ebox' ); ?></h3>
			<div class="inside">
				<p class="description">
					<?php
					// translators: placeholder: quiz.
					echo sprintf( esc_html_x( 'This text will be displayed at the end of the %s (in results). (this text is optional)', 'placeholder: quiz', 'ebox' ), esc_html( ebox_get_custom_label_lower( 'quiz' ) ) );
					?>
				</p>
				<div style="padding-top: 10px; padding-bottom: 10px;">
					<label for="wpProQuiz_resultGradeEnabled">
						<?php esc_html_e( 'Activate graduation', 'ebox' ); ?>
						<input type="checkbox" name="resultGradeEnabled" id="wpProQuiz_resultGradeEnabled" value="1" <?php echo $this->quiz->isResultGradeEnabled() ? 'checked="checked"' : ''; ?>>
					</label>
				</div>
				<div style="display: none;" id="resultGrade">
					<div>
						<strong><?php esc_html_e( 'Hint:', 'ebox' ); ?></strong>
						<ul style="list-style-type: square; padding: 5px; margin-left: 20px; margin-top: 0;">
							<li><?php esc_html_e( 'Maximal 15 levels', 'ebox' ); ?></li>
							<li>
								<?php
								// translators: placeholders: quiz, question points, question count.
								echo sprintf( esc_html_x( 'Percentages refer to the total score of the %1$s. (Current total %2$d points in %3$d questions.)', 'placeholders: quiz, question points, question count', 'ebox' ), esc_html( ebox_get_custom_label_lower( 'quiz' ) ), esc_html( $this->quiz->fetchSumQuestionPoints() ), esc_html( $this->quiz->fetchCountQuestions() ) );
								?>
								</li>
							<li><?php esc_html_e( 'Values can also be mixed up', 'ebox' ); ?></li>
							<li><?php esc_html_e( '10,15% or 10.15% allowed (max. two digits after the decimal point)', 'ebox' ); ?></li>
						</ul>

					</div>
					<div>
						<ul id="resultList">
						<?php
							$resultText = $this->quiz->getResultText();

						for ( $i = 0; $i < 15; $i++ ) {

							if ( $this->quiz->isResultGradeEnabled() && isset( $resultText['text'][ $i ] ) ) {
								?>
							<li style="padding: 5px; border: 1; border: 1px dotted;">
								<div style="margin-bottom: 5px;">
								<?php
								wp_editor(
									$resultText['text'][ $i ],
									'resultText_' . $i,
									array(
										'textarea_rows' => 3,
										'textarea_name' => 'resultTextGrade[text][]',
									)
								);
								?>
								</div>
								<div style="margin-bottom: 5px;background-color: rgb(207, 207, 207);padding: 10px;">
								<?php esc_html_e( 'from:', 'ebox' ); ?> <input type="text" name="resultTextGrade[prozent][]" class="small-text" value="<?php echo esc_attr( $resultText['prozent'][ $i ] ); ?>"> <?php esc_html_e( 'percent', 'ebox' ); ?> <?php
									// translators: placeholder: Result Text.
									printf( wp_kses_post( _x( '(Will be displayed, when result-percent is >= <span class="resultProzent">%s</span>%%)', 'placeholder: Result Text', 'ebox' ) ), esc_html( $resultText['prozent'][ $i ] ) );
								?>
									<input type="button" style="float: right;" class="button-primary deleteResult" value="<?php esc_html_e( 'Delete graduation', 'ebox' ); ?>">
									<div style="clear: right;"></div>
									<input type="hidden" value="1" name="resultTextGrade[activ][]">
								</div>
							</li>

						<?php } else { ?>
							<li style="padding: 5px; border: 1; border: 1px dotted; <?php echo $i ? 'display:none;' : ''; ?>">
								<div style="margin-bottom: 5px;">
								<?php
								wp_editor(
									'',
									'resultText_' . $i,
									array(
										'textarea_rows' => 3,
										'textarea_name' => 'resultTextGrade[text][]',
									)
								);
								?>
								</div>
								<div style="margin-bottom: 5px;background-color: rgb(207, 207, 207);padding: 10px;">
									<?php esc_html_e( 'from:', 'ebox' ); ?> <input type="text" name="resultTextGrade[prozent][]" class="small-text" value="0"> <?php esc_html_e( 'percent', 'ebox' ); ?> <?php
									// translators: placeholder: 0.
									printf( wp_kses_post( _x( '(Will be displayed, when result-percent is >= <span class="resultProzent">%s</span>%%)', 'placeholder: 0', 'ebox' ) ), '0' );
									?>

									<input type="button" style="float: right;" class="button-primary deleteResult" value="<?php esc_html_e( 'Delete graduation', 'ebox' ); ?>">
									<div style="clear: right;"></div>
									<input type="hidden" value="<?php echo $i ? '0' : '1'; ?>" name="resultTextGrade[activ][]">
								</div>
							</li>
								<?php
						}
						}
						?>
						</ul>
						<input type="button" class="button-primary addResult" value="<?php esc_html_e( 'Add graduation', 'ebox' ); ?>">
					</div>
				</div>
				<div id="resultNormal">
					<?php

						$resultText = is_array( $resultText ) ? '' : $resultText;
						wp_editor( $resultText, 'resultText', array( 'textarea_rows' => 10 ) );
					?>
				</div>
			</div>
		</div>
		<?php
	}
}
