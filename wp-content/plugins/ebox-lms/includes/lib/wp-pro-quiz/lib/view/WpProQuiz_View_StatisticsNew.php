<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// phpcs:disable WordPress.NamingConventions.ValidVariableName,WordPress.NamingConventions.ValidFunctionName,WordPress.NamingConventions.ValidHookName
class WpProQuiz_View_StatisticsNew extends WpProQuiz_View_View {
	/**
	 * @var WpProQuiz_Model_Quiz
	 */
	public $quiz;

	public function show() {
		?>

<style>
.wpProQuiz_blueBox {
	padding: 20px;
	background-color: rgb(223, 238, 255);
	border: 1px dotted;
	margin-top: 10px;
}
.categoryTr th {
	background-color: #F1F1F1;
}
.wpProQuiz_modal_backdrop {
	background: #000;
	opacity: 0.7;
	top: 0;
	bottom: 0;
	right: 0;
	left: 0;
	position: fixed;
	z-index: 159900;
}
.wpProQuiz_modal_window {
	position: fixed;
	background: #FFF;
	top: 40px;
	bottom: 40px;
	left: 40px;
	right: 40px;
	z-index: 160000;
}
.wpProQuiz_actions {
	display: none;
	padding: 2px 0 0;
}

.mobile .wpProQuiz_actions {
	display: block;
}

tr:hover .wpProQuiz_actions {
	display: block;
}
</style>

	<div class="wrap wpProQuiz_statisticsNew">
		<input type="hidden" id="quizId" value="<?php echo $this->quiz->getId(); ?>" name="quizId">
		<input type="hidden" id="quiz" value="<?php echo $this->quiz->getPostId(); ?>" name="quizPostId">
		<?php if(!$this->quiz->isStatisticsOn()) { ?>
			<p style="padding: 30px; background: #F7E4E4; border: 1px dotted;">
				<span style="font-weight: bold;"><?php esc_html_e('Statistics not enabled.', 'ebox' ); ?></span> <?php
					if ( isset( $_GET['post_id'] ) ) {
						$quiz_edit_url = add_query_arg(
							array(
								'action'     => 'edit',
								'post'       => absint( $_GET['post_id'] ),
								'currentTab' => 'ebox-quiz-settings',
							),
							admin_url( 'post.php' )
						); ?><a href="<?php echo $quiz_edit_url; ?>##ebox-quiz-admin-data-handling-settings_statisticsOn_field"><?php
						echo sprintf(
							// translators: placholder: Quiz.
							esc_html_x( 'Edit %s Settings', 'placeholder: Quiz', 'ebox' ), ebox_Custom_Label::get_label( 'Quiz' )
						);
						?></a><?php
					}
				?>
			</p>
		<?php
		return;
		}
		?>
		<div style="padding: 10px 0px;" class="wpProQuiz_tab_wrapper">
			<a class="button-primary" href="#" data-tab="#wpProQuiz_tabHistory"><?php esc_html_e( 'History', 'ebox' ); ?></a>
			<a class="button-secondary" href="#" data-tab="#wpProQuiz_tabOverview"><?php esc_html_e( 'Overview', 'ebox' ); ?></a>
		</div>

		<div id="wpProQuiz_nonce" data-nonce="<?php echo esc_attr( wp_create_nonce( 'wpProQuiz_nonce' ) ); ?>" style="display:none;"></div>
		<div id="wpProQuiz_loadData" class="wpProQuiz_blueBox 3" style="background-color: #F8F5A8; display: none;">
			<img alt="load" src="<?php echo esc_url( admin_url( '/images/wpspin_light.gif' ) ); ?>" />
			<?php esc_html_e( 'Loading', 'ebox' ); ?>
		</div>

		<div id="wpProQuiz_content" style="display: block;">
			<?php $this->showHistory(); ?>
			<?php $this->showTabOverview(); ?>
		</div>

		<?php $this->showModalWindow(); ?>

	</div>

		<?php
	}

	private function showHistory() {
		?>
		<div id="wpProQuiz_tabHistory" class="wpProQuiz_tabContent" style="display: block;">

			<div id="poststuff">
				<div class="postbox">
					<h2 class="hndle"><?php esc_html_e( 'Filter', 'ebox' ); ?></h2>
					<div class="inside">
						<ul>
							<?php if ( ( ebox_is_admin_user( get_current_user_id() ) ) || ( ebox_is_team_leader_user( get_current_user_id() ) ) ) { ?>
								<li>
									<label>
										<?php esc_html_e( 'Which users should be displayed:', 'ebox' ); ?>
										<select id="wpProQuiz_historyUser" autocomplete="off" data-ld-select2="1" data-nonce="<?php echo esc_attr( wp_create_nonce( 'ebox-quiz-statistics-history' . get_current_user_id() )); ?>" placeholder="<?php esc_html__( 'Filter by User', 'ebox' ); ?>">
											<?php if ( ! ebox_use_select2_lib_ajax_fetch() ) { ?>
												<optteam label="<?php esc_html_e( 'special filter', 'ebox' ); ?>">
											<?php } ?>
											<option value="-1" selected="selected"><?php esc_html_e( 'all users', 'ebox' ); ?></option>

											<?php if ( ( ebox_is_admin_user( get_current_user_id() ) ) || ( ( ebox_is_team_leader_user( get_current_user_id() ) ) && ( 'advanced' === ebox_get_team_leader_manage_users() ) ) ) { ?>
												<option value="-2"><?php esc_html_e( 'only registered users', 'ebox' ); ?></option>
												<option value="-3"><?php esc_html_e( 'only anonymous users', 'ebox' ); ?></option>
											<?php } ?>

											<?php if ( ! ebox_use_select2_lib_ajax_fetch() ) { ?>
												</optteam>
											<?php } ?>

											<?php if ( ! ebox_use_select2_lib_ajax_fetch() ) { ?>
												<optteam label="<?php esc_html_e( 'User', 'ebox' ); ?>">
													<?php
													foreach ( $this->users as $user ) {
														if ( 0 !== $user->ID ) {
															echo '<option value="' . absint( $user->ID ) . '">' . esc_attr( $user->user_login ) . ' (' . esc_attr( $user->display_name ) . ')</option>';
														}
													}
													?>
												</optteam>
											<?php } ?>
										</select>
									</label>
								</li>
							<?php } ?>
							<li>
								<label>
									<?php esc_html_e( 'How many entries should be shown on one page:', 'ebox' ); ?>
									<select id="wpProQuiz_historyPageLimit">
										<option>1</option>
										<option>5</option>
										<option selected="selected">10</option>
										<option>50</option>
										<option>100</option>
										<option>500</option>
										<option>1000</option>
									</select>
								</label>
							</li>
							<li>
								<?php
									$dateVon = '<input type="text" id="datepickerFrom" class="ebox-datepicker-field" />';
									$dateBis = '<input type="text" id="datepickerTo" class="ebox-datepicker-field" />';
									// translators: placeholders: Date From, Date To.
									printf( esc_html_x( 'Search to date limit from %1$s to %2$s', 'placeholders: Date From, Date To', 'ebox' ), $dateVon, $dateBis ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Outputs HTML
								?>
							</li>
							<li>
								<input type="button" value="<?php esc_html_e( 'Filter', 'ebox' ); ?>" class="button-secondary" id="filter">
							</li>
						</ul>
					</div>
				</div>
			</div>

			<div id="wpProQuiz_loadDataHistory" class="wpProQuiz_blueBox" style="background-color: #F8F5A8; display: none;">
				<img alt="load" src="<?php echo esc_url( admin_url( '/images/wpspin_light.gif' ) ); ?>" />
				<?php esc_html_e( 'Loading', 'ebox' ); ?>
			</div>

			<div id="wpProQuiz_historyLoadContext"></div>

			<div style="margin-top: 10px;">

				<div style="float: left;" id="historyNavigation">
					<input style="font-weight: bold;" class="button-secondary navigationLeft" value="&lt;" type="button">
					<select class="navigationCurrentPage"><option value="1">1</option></select>
					<input style="font-weight: bold;" class="button-secondary navigationRight" value="&gt;" type="button">
				</div>

				<div style="float: right;">
					<a class="button-secondary wpProQuiz_update" href="#"><?php esc_html_e( 'Refresh', 'ebox' ); ?></a>
					<?php if ( current_user_can( 'wpProQuiz_reset_statistics' ) ) { ?>
					<a class="button-secondary wpProQuiz_resetComplete" href="#"><?php esc_html_e( 'Reset entire statistic', 'ebox' ); ?></a>
					<?php } ?>
				</div>

				<div style="clear: both;"></div>
			</div>

		</div>
		<?php
	}

	private function showModalWindow() {
		?>

		<div id="wpProQuiz_user_overlay" style="display: none;">
			<div class="wpProQuiz_modal_window" style="padding: 20px; overflow: scroll;">
				<input type="button" value="<?php esc_html_e( 'Close', 'ebox' ); ?>" class="button-primary" style=" position: fixed; top: 48px; right: 59px; z-index: 160001;" id="wpProQuiz_overlay_close">

				<div id="wpProQuiz_user_content" style="margin-top: 20px;"></div>

				<div id="wpProQuiz_loadUserData" class="wpProQuiz_blueBox" style="background-color: #F8F5A8; display: none; margin: 50px;">
					<img alt="load" src="<?php echo esc_url( admin_url( '/images/wpspin_light.gif' ) ); ?>" />
					<?php esc_html_e( 'Loading', 'ebox' ); ?>
				</div>
			</div>
			<div class="wpProQuiz_modal_backdrop"></div>
		</div>

		<?php
	}

	private function showTabOverview() {
		?>
		<div id="wpProQuiz_tabOverview" class="wpProQuiz_tabContent" style="display: none;">
			<div id="poststuff">
				<div class="postbox">
					<h3 class="hndle"><?php esc_html_e( 'Filter', 'ebox' ); ?></h3>
					<div class="inside">
						<ul>
							<li>
								<label>
									<?php
									// translators: placeholder: quiz.
									echo sprintf( esc_html_x( 'Show only users, who solved the %s:', 'placeholder: quiz', 'ebox' ), esc_html( ebox_get_custom_label_lower( 'quiz' ) ) );
									?>
									<input type="checkbox" value="1" id="wpProQuiz_overviewOnlyCompleted">
								</label>
							</li>
							<li>
								<label>
									<?php esc_html_e( 'How many entries should be shown on one page:', 'ebox' ); ?>
									<select id="wpProQuiz_overviewPageLimit">
										<option>1</option>
										<option>4</option>
										<option selected="selected">50</option>
										<option>100</option>
										<option>500</option>
										<option>1000</option>
									</select>
								</label>
							</li>
							<li>
								<input type="button" value="<?php esc_html_e( 'Filter', 'ebox' ); ?>" class="button-secondary" id="overviewFilter">
							</li>
						</ul>
					</div>
				</div>
			</div>

			<div id="wpProQuiz_loadDataOverview" class="wpProQuiz_blueBox" style="background-color: #F8F5A8; display: none;">
				<img alt="load" src="<?php echo esc_url( admin_url( '/images/wpspin_light.gif' ) ); ?>" />
				<?php esc_html_e( 'Loading', 'ebox' ); ?>
			</div>

			<div id="wpProQuiz_overviewLoadContext"></div>

			<div style="margin-top: 10px;">

				<div style="float: left;" id="overviewNavigation">
					<input style="font-weight: bold;" class="button-secondary navigationLeft" value="&lt;" type="button">
					<select class="navigationCurrentPage"><option value="1">1</option></select>
					<input style="font-weight: bold;" class="button-secondary navigationRight" value="&gt;" type="button">
				</div>

				<div style="float: right;">
					<a class="button-secondary wpProQuiz_update" href="#"><?php esc_html_e( 'Refresh', 'ebox' ); ?></a>
					<?php if ( current_user_can( 'wpProQuiz_reset_statistics' ) ) { ?>
					<a class="button-secondary wpProQuiz_resetComplete" href="#"><?php esc_html_e( 'Reset entire statistic', 'ebox' ); ?></a>
					<?php } ?>
				</div>

				<div style="clear: both;"></div>
			</div>

		</div>
		<?php
	}
}
