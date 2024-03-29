<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// phpcs:disable WordPress.NamingConventions.ValidVariableName,WordPress.NamingConventions.ValidFunctionName,WordPress.NamingConventions.ValidHookName
class WpProQuiz_View_Statistics extends WpProQuiz_View_View {
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
</style>


<div class="wrap wpProQuiz_statistics">
	<input type="hidden" id="quizId" value="<?php echo absint( $this->quiz->getId() ); ?>" name="quizId">
	<h2>
		<?php
		// translators: placeholders: Quiz, Quiz Name/Title.
		echo sprintf( esc_html_x( '%1$s: %2$s - Statistics', 'placeholders: Quiz, Quiz Name/Title', 'ebox' ), ebox_Custom_Label::get_label( 'quiz' ), wp_kses_post( $this->quiz->getName() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
		?>
	</h2>
	<p><a class="button-secondary" href="admin.php?page=ldAdvQuiz"><?php esc_html_e( 'back to overview', 'ebox' ); ?></a></p>

		<?php if ( ! $this->quiz->isStatisticsOn() ) { ?>
	<p style="padding: 30px; background: #F7E4E4; border: 1px dotted; width: 300px;">
		<span style="font-weight: bold; padding-right: 10px;"><?php esc_html_e( 'Stats not enabled', 'ebox' ); ?></span>
		<a class="button-secondary" href="admin.php?page=ldAdvQuiz&action=addEdit&quizId=<?php echo absint( $this->quiz->getId() ); ?>&post_id=<?php echo ( isset( $_GET['post_id'] ) ) ? absint( $_GET['post_id'] ) : '0'; ?>"><?php esc_html_e( 'Activate statistics', 'ebox' ); ?></a>
	</p>
	<?php return; } ?>

	<div style="padding: 10px 0px;">
		<a class="button-primary wpProQuiz_tab" id="wpProQuiz_typeUser" href="#"><?php esc_html_e( 'Users', 'ebox' ); ?></a>
		<a class="button-secondary wpProQuiz_tab" id="wpProQuiz_typeOverview" href="#"><?php esc_html_e( 'Overview', 'ebox' ); ?></a>
		<a class="button-secondary wpProQuiz_tab" id="wpProQuiz_typeForm" href="#"><?php esc_html_e( 'Custom fields', 'ebox' ); ?></a>
	</div>

	<div id="wpProQuiz_nonce" data-nonce="<?php echo esc_attr( wp_create_nonce( 'wpProQuiz_nonce' ) ); ?>" style="display:none;"></div>
	<div id="wpProQuiz_loadData" class="wpProQuiz_blueBox 2" style="background-color: #F8F5A8; display: none;">
		<img alt="load" src="<?php echo esc_url( admin_url( '/images/wpspin_light.gif' ) ); ?>" />
		<?php esc_html_e( 'Loading', 'ebox' ); ?>
	</div>

	<div id="wpProQuiz_content" style="display: none;">

		<?php $this->tabUser(); ?>
		<?php $this->tabOverview(); ?>
		<?php $this->tabForms(); ?>

	</div>

</div>

		<?php
	}

	private function tabUser() {
		?>
	<div id="wpProQuiz_tabUsers" class="wpProQuiz_tabContent">
			<div class="wpProQuiz_blueBox" id="wpProQuiz_userBox" style="margin-bottom: 20px;">
				<div style="float: left;">
					<div style="padding-top: 6px;">
						<?php esc_html_e( 'Please select user name:', 'ebox' ); ?>
					</div>

					<div style="padding-top: 6px;">
						<?php esc_html_e( 'Select a test:', 'ebox' ); ?>
					</div>

				</div>

				<div style="float: left;">
					<div>
						<select name="userSelect" id="userSelect">
							<?php
							foreach ( $this->users as $user ) {
								if ( 0 == $user->ID ) {
									echo '<option value="0">=== ', esc_html__( 'Anonymous user', 'ebox' ),' ===</option>';
								} else {
									echo '<option value="', absint( $user->ID ), '">', esc_html( $user->user_login ), ' (', esc_html( $user->display_name ), ')</option>';
								}
							}
							?>
						</select>
					</div>

					<div>
						<select id="testSelect">
							<option value="0">=== <?php esc_html_e( 'average', 'ebox' ); ?> ===</option>
						</select>
					</div>
				</div>
				<div style="clear: both;"></div>
			</div>

			<?php $this->formTable(); ?>

			<table class="wp-list-table widefat">
				<thead>
					<tr>
						<th scope="col" style="width: 50px;"></th>
						<th scope="col"><?php esc_html_e( 'Question', 'ebox' ); ?></th>
						<th scope="col" style="width: 100px;"><?php esc_html_e( 'Points', 'ebox' ); ?></th>
						<th scope="col" style="width: 100px;"><?php esc_html_e( 'Correct', 'ebox' ); ?></th>
						<th scope="col" style="width: 100px;"><?php esc_html_e( 'Incorrect', 'ebox' ); ?></th>
						<th scope="col" style="width: 100px;"><?php esc_html_e( 'Hints used', 'ebox' ); ?></th>
						<th scope="col" style="width: 100px;"><?php esc_html_e( 'Time', 'ebox' ); ?> <span style="font-size: x-small;">(hh:mm:ss)</span></th>
						<th scope="col" style="width: 100px;"><?php esc_html_e( 'Points scored', 'ebox' ); ?></th>
						<th scope="col" style="width: 60px;"><?php esc_html_e( 'Results', 'ebox' ); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php
				$gPoints = 0;
				foreach ( $this->questionList as $k => $ql ) {
					$index   = 1;
					$cPoints = 0;
					?>

					<tr class="categoryTr">
						<th colspan="9">
							<span><?php esc_html_e( 'Category', 'ebox' ); ?>:</span>
							<span style="font-weight: bold;"><?php echo esc_html( $this->categoryList[ $k ]->getCategoryName() ); ?></span>
						</th>
					</tr>

					<?php
					foreach ( $ql as $q ) {
						$gPoints += $q->getPoints();
						$cPoints += $q->getPoints();
						?>
						<tr id="wpProQuiz_tr_<?php echo absint( $q->getId() ); ?>">
							<th><?php echo absint( $index++ ); ?></th>
							<th><?php echo wp_kses_post( $q->getTitle() ); ?></th>
							<th class="wpProQuiz_points"><?php echo esc_html( $q->getPoints() ); ?></th>
							<th class="wpProQuiz_cCorrect" style="color: green;"></th>
							<th class="wpProQuiz_cIncorrect" style="color: red;"></th>
							<th class="wpProQuiz_cTip"></th>
							<th class="wpProQuiz_cTime"></th>
							<th class="wpProQuiz_cPoints"></th>
							<th></th>
						</tr>
					<?php } ?>

					<tr class="categoryTr" id="wpProQuiz_ctr_<?php echo esc_attr( $k ); ?>">
						<th colspan="2">
							<span><?php esc_html_e( 'Sub-Total: ', 'ebox' ); ?></span>
						</th>
						<th class="wpProQuiz_points"><?php echo esc_html( $cPoints ); ?></th>
						<th class="wpProQuiz_cCorrect" style="color: green;"></th>
						<th class="wpProQuiz_cIncorrect" style="color: red;"></th>
						<th class="wpProQuiz_cTip"></th>
						<th class="wpProQuiz_cTime"></th>
						<th class="wpProQuiz_cPoints"></th>
						<th class="wpProQuiz_cResult" style="font-weight: bold;"></th>
					</tr>

					<tr>
						<th colspan="9"></th>
					</tr>

				<?php } ?>
				</tbody>

				<tfoot>
					<tr id="wpProQuiz_tr_0">
						<th></th>
						<th><?php esc_html_e( 'Total', 'ebox' ); ?></th>
						<th class="wpProQuiz_points"><?php echo esc_html( $gPoints ); ?></th>
						<th class="wpProQuiz_cCorrect" style="color: green;"></th>
						<th class="wpProQuiz_cIncorrect" style="color: red;"></th>
						<th class="wpProQuiz_cTip"></th>
						<th class="wpProQuiz_cTime"></th>
						<th class="wpProQuiz_cPoints"></th>
						<th class="wpProQuiz_cResult" style="font-weight: bold;"></th>
					</tr>
				</tfoot>
			</table>

			<div style="margin-top: 10px;">
				<div style="float: left;">
					<a class="button-secondary wpProQuiz_update" href="#"><?php esc_html_e( 'Refresh', 'ebox' ); ?></a>
				</div>
				<div style="float: right;">
					<?php if ( current_user_can( 'wpProQuiz_reset_statistics' ) ) { ?>
						<a class="button-secondary" href="#" id="wpProQuiz_reset"><?php esc_html_e( 'Reset statistics', 'ebox' ); ?></a>
						<a class="button-secondary" href="#" id="wpProQuiz_resetUser"><?php esc_html_e( 'Reset user statistics', 'ebox' ); ?></a>
						<a class="button-secondary wpProQuiz_resetComplete" href="#"><?php esc_html_e( 'Reset entire statistic', 'ebox' ); ?></a>
					<?php } ?>
				</div>
				<div style="clear: both;"></div>
			</div>
		</div>

		<?php
	}

	private function tabOverview() {

		?>

		<div id="wpProQuiz_tabOverview" class="wpProQuiz_tabContent" style="display: none;">

			<input type="hidden" value="<?php echo 0; ?>" name="gPoints" id="wpProQuiz_gPoints">

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
									<input type="checkbox" value="1" id="wpProQuiz_onlyCompleted">
								</label>
							</li>
							<li>
								<label>
									<?php esc_html_e( 'How many entries should be shown on one page:', 'ebox' ); ?>
									<select id="wpProQuiz_pageLimit">
										<option>1</option>
										<option>10</option>
										<option>50</option>
										<option selected="selected">100</option>
										<option>500</option>
										<option>1000</option>
									</select>
								</label>
							</li>
						</ul>
					</div>
				</div>
			</div>

			<table class="wp-list-table widefat">
				<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'User', 'ebox' ); ?></th>
						<th scope="col" style="width: 100px;"><?php esc_html_e( 'Points', 'ebox' ); ?></th>
						<th scope="col" style="width: 100px;"><?php esc_html_e( 'Correct', 'ebox' ); ?></th>
						<th scope="col" style="width: 100px;"><?php esc_html_e( 'Incorrect', 'ebox' ); ?></th>
						<th scope="col" style="width: 100px;"><?php esc_html_e( 'Hints used', 'ebox' ); ?></th>
						<th scope="col" style="width: 100px;"><?php esc_html_e( 'Time', 'ebox' ); ?> <span style="font-size: x-small;">(hh:mm:ss)</span></th>
						<th scope="col" style="width: 60px;"><?php esc_html_e( 'Results', 'ebox' ); ?></th>
					</tr>
				</thead>
				<tbody id="wpProQuiz_statistics_overview_data">
					<tr style="display: none;">
						<th><a href="#"></a></th>
						<th class="wpProQuiz_cPoints"></th>
						<th class="wpProQuiz_cCorrect" style="color: green;"></th>
						<th class="wpProQuiz_cIncorrect" style="color: red;"></th>
						<th class="wpProQuiz_cTip"></th>
						<th class="wpProQuiz_cTime"></th>
						<th class="wpProQuiz_cResult" style="font-weight: bold;"></th>
					</tr>
				</tbody>
			</table>

			<div style="margin-top: 10px;">
				<div style="float: left;">
					<input style="font-weight: bold;" class="button-secondary" value="&lt;" type="button" id="wpProQuiz_pageLeft">
					<select id="wpProQuiz_currentPage"><option value="1">1</option></select>
					<input style="font-weight: bold;" class="button-secondary"value="&gt;" type="button" id="wpProQuiz_pageRight">
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

	private function tabForms() {
		?>

		<div id="wpProQuiz_tabFormOverview" class="wpProQuiz_tabContent" style="display: none;">

			<div id="poststuff">
				<div class="postbox">
					<h3 class="hndle"><?php esc_html_e( 'Filter', 'ebox' ); ?></h3>
					<div class="inside">
						<ul>
							<li>
								<label>
									<?php esc_html_e( 'Which users should be displayed:', 'ebox' ); ?>
									<select id="wpProQuiz_formUser">
										<option value="0"><?php esc_html_e( 'all', 'ebox' ); ?></option>
										<option value="1"><?php esc_html_e( 'only registered users', 'ebox' ); ?></option>
										<option value="2"><?php esc_html_e( 'only anonymous users', 'ebox' ); ?></option>
									</select>
								</label>
							</li>
							<li>
								<label>
									<?php esc_html_e( 'How many entries should be shown on one page:', 'ebox' ); ?>
									<select id="wpProQuiz_fromPageLimit">
										<option>1</option>
										<option>10</option>
										<option>50</option>
										<option selected="selected">100</option>
										<option>500</option>
										<option>1000</option>
									</select>
								</label>
							</li>
						</ul>
					</div>
				</div>
			</div>

			<table class="wp-list-table widefat">
				<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'Username', 'ebox' ); ?></th>
						<th scope="col" style="width: 200px;"><?php esc_html_e( 'Date', 'ebox' ); ?></th>
						<th scope="col" style="width: 60px;"><?php esc_html_e( 'Results', 'ebox' ); ?></th>
					</tr>
				</thead>
				<tbody id="wpProQuiz_statistics_form_data">
					<tr style="display: none;">
						<th><a href="#" class="wpProQuiz_cUsername"></a></th>
						<th class="wpProQuiz_cCreateTime"></th>
						<th class="wpProQuiz_cResult" style="font-weight: bold;"></th>
					</tr>
				</tbody>
			</table>

			<div style="margin-top: 10px;">
				<div style="float: left;">
					<input style="font-weight: bold;" class="button-secondary" value="&lt;" type="button" id="wpProQuiz_formPageLeft">
					<select id="wpProQuiz_formCurrentPage"><option value="1">1</option></select>
					<input style="font-weight: bold;" class="button-secondary"value="&gt;" type="button" id="wpProQuiz_formPageRight">
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

	private function formTable() {
		if ( ! $this->quiz->isFormActivated() ) {
			return;
		}
		?>
		<div id="wpProQuiz_form_box">
			<div id="poststuff">
				<div class="postbox">
					<h3 class="hndle"><?php esc_html_e( 'Custom fields', 'ebox' ); ?></h3>
					<div class="inside">
						<table>
							<tbody>
								<?php
								foreach ( $this->forms as $form ) {
									?>
									<tr>
										<td style="padding: 5px;"><?php echo esc_html( $form->getFieldname() ); ?></td>
										<td id="form_id_<?php echo absint( $form->getFormId() ); ?>">asdfffffffffffffffffffffsadfsdfa sf asd fas</td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
