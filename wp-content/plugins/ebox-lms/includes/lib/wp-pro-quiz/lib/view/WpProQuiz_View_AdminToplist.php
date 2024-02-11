<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// phpcs:disable WordPress.NamingConventions.ValidVariableName,WordPress.NamingConventions.ValidFunctionName,WordPress.NamingConventions.ValidHookName
class WpProQuiz_View_AdminToplist extends WpProQuiz_View_View {

	public function show() {
		?>
<div class="wrap wpProQuiz_toplist">
		<?php /* ?><h2><?php esc_html_e('Leaderboard', 'ebox'); echo ': ', $this->quiz->getName(); ?></h2><?php */ ?>

		<?php
		if( ! $this->quiz->isToplistActivated() ) { ?>
			<p style="padding: 30px; background: #F7E4E4; border: 1px dotted;">
				<span style="font-weight: bold;"><?php esc_html_e('Leaderboard not enabled.', 'ebox' ); ?></span> <?php
					if ( isset( $_GET['post_id'] ) ) {
						$quiz_edit_url = add_query_arg(
							array(
								'action'     => 'edit',
								'post'       => absint( $_GET['post_id'] ),
								'currentTab' => 'ebox-quiz-settings',
							),
							admin_url( 'post.php' )
						); ?><a href="<?php echo $quiz_edit_url; ?>#ebox-quiz-admin-data-handling-settings_toplistActivated"><?php
						echo sprintf(
							// translators: placeholder: Quiz.
							esc_html_x( 'Edit %s Settings', 'placeholder: Quiz.', 'ebox' ), ebox_Custom_Label::get_label( 'Quiz' )
						);
						?></a><?php
					}
				?>
			</p>
		<?php
		return;
		}
		?>

	<div id="poststuff">
		<div class="postbox">
			<h3 class="hndle"><?php esc_html_e( 'Filter', 'ebox' ); ?></h3>
			<div class="inside">
				<ul>
					<li>
						<label>
							<?php esc_html_e( 'Sort by:', 'ebox' ); ?>
							<select id="wpProQuiz_sorting">
								<option value="<?php echo esc_attr( WpProQuiz_Model_Quiz::QUIZ_TOPLIST_SORT_BEST ); ?>"><?php esc_html_e( 'best user', 'ebox' ); ?></option>
								<option value="<?php echo esc_attr( WpProQuiz_Model_Quiz::QUIZ_TOPLIST_SORT_NEW ); ?>"><?php esc_html_e( 'newest entry', 'ebox' ); ?></option>
								<option value="<?php echo esc_attr( WpProQuiz_Model_Quiz::QUIZ_TOPLIST_SORT_OLD ); ?>"><?php esc_html_e( 'oldest entry', 'ebox' ); ?></option>
							</select>
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
					<li>
						<span style="font-weight: bold;"><?php esc_html_e( 'Type', 'ebox' ); ?>:</span> <?php esc_html_e( 'UR = unregistered user, R = registered user', 'ebox' ); ?>
					</li>
				</ul>
			</div>
		</div>
	</div>

	<div id="wpProQuiz_nonce" data-nonce="<?php echo esc_attr( wp_create_nonce( 'wpProQuiz_nonce' ) ); ?>" style="display:none;"></div>
	<div id="wpProQuiz_loadData" class="wpProQuiz_blueBox 1" data-nonce="<?php echo esc_attr( wp_create_nonce( 'ebox-wpproquiz-toplist' ) ); ?>" style="background-color: #F8F5A8;padding: 20px;border: 1px dotted;margin-top: 10px;">
		<img alt="load" src="<?php echo esc_url( admin_url( '/images/wpspin_light.gif' ) ); ?>" />
		<?php esc_html_e( 'Loading', 'ebox' ); ?>
	</div>

	<div id="wpProQuiz_content">
		<table class="wp-list-table widefat" id="wpProQuiz_toplistTable">
			<thead>
				<tr>
					<th scope="col" width="20px"><input style="margin: 0;" type="checkbox" value="0" id="wpProQuiz_checkedAll"></th>
					<th scope="col"><?php esc_html_e( 'User', 'ebox' ); ?></th>
					<th scope="col"><?php esc_html_e( 'E-Mail', 'ebox' ); ?></th>
					<th scope="col" width="50px"><?php esc_html_e( 'Type', 'ebox' ); ?></th>
					<th scope="col" width="150px"><?php esc_html_e( 'Entered on', 'ebox' ); ?></th>
					<th scope="col" width="70px"><?php esc_html_e( 'Points', 'ebox' ); ?></th>
					<th scope="col" width="100px"><?php esc_html_e( 'Results', 'ebox' ); ?></th>
				</tr>
			</thead>
			<tbody id="">
				<tr style="display: none;">
					<td><input type="checkbox" name="checkedData[]"></td>
					<td>
						<strong class="wpProQuiz_username"></strong>
						<input name="inline_editUsername" class="inline_editUsername" type="text" value="" style="display: none;">
						<div class="row-actions">

							<span style="display: none;">
								<a class="wpProQuiz_edit" href="#"><?php esc_html_e( 'Edit', 'ebox' ); ?></a> |
							</span>
							<span>
								<a style="color: red;" class="wpProQuiz_delete" href="#"><?php esc_html_e( 'Delete', 'ebox' ); ?></a>
							</span>

						</div>
						<div class="inline-edit" style="margin-top: 10px; display: none;">
							<input type="button" value="<?php esc_html_e( 'save', 'ebox' ); ?>" class="button-secondary inline_editSave">
							<input type="button" value="<?php esc_html_e( 'cancel', 'ebox' ); ?>" class="button-secondary inline_editCancel">
						</div>
					</td>
					<td>
						<span class="wpProQuiz_email"></span>
						<input name="inline_editEmail" class="inline_editEmail" value="" type="text" style="display: none;">
					</td>
					<td></td>
					<td></td>
					<td></td>
					<td style="font-weight: bold;"></td>
				</tr>
			</tbody>
		</table>

		<div style="margin-top: 10px;">
			<div style="float: left;">
				<select id="wpProQuiz_actionName">
					<option value="0" selected="selected"><?php esc_html_e( 'Action', 'ebox' ); ?></option>
					<option value="delete" ><?php esc_html_e( 'Delete', 'ebox' ); ?></option>
				</select>
				<input class="button-secondary" type="button" value="<?php esc_html_e( 'Apply', 'ebox' ); ?>" id="wpProQuiz_action">
				<input class="button-secondary" type="button" value="<?php esc_html_e( 'Delete all entries', 'ebox' ); ?>" id="wpProQuiz_deleteAll">
			</div>
			<div style="float: right;">
				<input style="font-weight: bold;" class="button-secondary" value="&lt;" type="button" id="wpProQuiz_pageLeft">
				<select id="wpProQuiz_currentPage"><option value="1">1</option></select>
				<input style="font-weight: bold;" class="button-secondary"value="&gt;" type="button" id="wpProQuiz_pageRight">
			</div>
			<div style="clear: both;"></div>
		</div>
	</div>
</div>

		<?php
	}
}
