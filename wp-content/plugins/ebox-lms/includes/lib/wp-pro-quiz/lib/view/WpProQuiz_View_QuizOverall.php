<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// phpcs:disable WordPress.NamingConventions.ValidVariableName,WordPress.NamingConventions.ValidFunctionName,WordPress.NamingConventions.ValidHookName
class WpProQuiz_View_QuizOverall extends WpProQuiz_View_View {

	public function show() {
		?>
<style>
.wpProQuiz_exportList ul {
	list-style: none;
	margin: 0;
	padding: 0;
}
.wpProQuiz_exportList li {
	float: left;
	padding: 3px;
	border: 1px solid #B3B3B3;
	margin-right: 5px;
	background-color: #F3F3F3;
}
.wpProQuiz_exportList, .wpProQuiz_importList {
	padding: 20px;
	background-color: rgb(223, 238, 255);
	border: 1px dotted;
	margin-top: 10px;
	display: none;
}
.wpProQuiz_exportCheck {
	display: none;
}

.ebox-pager a {
	font-size: 110%;
	padding: 3px
}
</style>
<div class="wrap wpProQuiz_quizOverall" style="position: relative;">
	<h1>
		<?php
		echo sprintf(
		// translators: placeholder: Quiz.
			esc_html_x( '%s Import/Export', 'placeholder: Quiz', 'ebox' ),
			ebox_get_custom_label( 'quiz' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
		);
		?>
	</h1>
		<?php
		$quiz_page = 1;
		if ( ( isset( $_GET['paged'] ) ) && ( ! empty( $_GET['paged'] ) ) ) {
			$quiz_page = absint( $_GET['paged'] );
		}

		if ( empty( $quiz_page ) ) {
			$quiz_page = 1;
		}

		$quiz_per_page = ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_General_Per_Page', 'quiz_num' );

		$search_value = '';
		if ( ( isset( $_GET['s'] ) ) && ( ! empty( $_GET['s'] ) ) ) {
			$search_value = esc_attr( $_GET['s'] );
		}
		$quiz_query_args = array(
			'post_type'      => ebox_get_post_type_slug( 'quiz' ),
			'post_status'    => 'any',
			'posts_per_page' => $quiz_per_page,
			'paged'          => $quiz_page,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'fields'         => 'ID',
			's'              => $search_value,
		);

		if ( ( empty( $search_value ) ) && ( isset( $_GET['quiz_id'] ) ) && ( ! empty( $_GET['quiz_id'] ) ) ) {
			$quiz_query_args['post__in'] = array( absint( $_GET['quiz_id'] ) );
		}

		$quiz_query_results = new WP_Query( $quiz_query_args );
		?>
	<form id="posts-filter" method="get">
		<p class="search-box">
			<label class="screen-reader-text" for="post-search-input">Search Courses:</label>
			<input type="hidden" name="page" value="ldAdvQuiz">
			<input type="search" id="quiz-search-input" name="s" value="<?php echo esc_attr( $search_value ); ?>">
			<input type="submit" id="search-submit" class="button" value="
			<?php
			echo sprintf(
				// translators: placeholder: Quiz.
				esc_html_x( 'Search %s', 'placeholder: Quiz', 'ebox' ),
				ebox_get_custom_label( 'quiz' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
			);
			?>
			">
		</p>
	</form>
	<table class="wp-list-table widefat">
		<thead>
			<tr>
				<th scope="col" width="30px" class="wpProQuiz_exportCheck"><input type="checkbox" name="exportItemsAll" value="0"></th>
				<th scope="col"><?php esc_html_e( 'Title', 'ebox' ); ?></th>
				<th scope="col"><?php esc_html_e( 'Settings', 'ebox' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			if ( ( is_a( $quiz_query_results, 'WP_Query' ) ) && ( property_exists( $quiz_query_results, 'posts' ) ) && ( ! empty( $quiz_query_results->posts ) ) ) {
				foreach ( $quiz_query_results->posts as $quiz_post ) {
					?>
						<tr>
							<th class="wpProQuiz_exportCheck"><input type="checkbox" name="exportItems" value="<?php echo absint( $quiz_post->ID ); ?>"></th>
							<td class="wpProQuiz_quizName">
								<strong><?php echo absint( $quiz_post->ID ); ?> - <?php echo wp_kses_post( get_the_title( $quiz_post->ID ) ); ?></strong>
							<?php if ( current_user_can( 'wpProQuiz_edit_quiz' ) ) { ?>
									<div class="row-actions">
										<span>
											<a href="<?php echo esc_url( get_edit_post_link( $quiz_post->ID ) ); ?>"><?php esc_html_e( 'edit', 'ebox' ); ?></a>
										</span>
									</div>
								<?php } ?>
							</td>
							<td class="wpProQuiz_quizName">
						<?php
						$valid_quiz_pro = false;
						$quiz_pro_id    = ebox_get_setting( $quiz_post->ID, 'quiz_pro' );
						$quiz_pro_id    = absint( $quiz_pro_id );
						if ( ! empty( $quiz_pro_id ) ) {
							$quiz_mapper = new WpProQuiz_Model_QuizMapper();
							$quiz_pro    = $quiz_mapper->fetch( $quiz_pro_id );
							if ( ( is_a( $quiz_pro, 'WpProQuiz_Model_Quiz' ) ) && ( $quiz_pro_id === $quiz_pro->getId() ) ) {
								$valid_quiz_pro = true;
								echo absint( $quiz_pro_id ) . ' - ' . esc_attr( $quiz_pro->getName() );
							}
						}

						if ( false === $valid_quiz_pro ) {
							?>
								<span class="ld-error"><?php esc_html_e( 'Missing ProQuiz Associated Settings.', 'ebox' ); ?></span>
								<?php
						}
						?>
							</td>
						</tr>
						<?php
				}
			} else {
				?>
					<tr>
					<td colspan="3" style="text-align: center; font-weight: bold; padding: 10px;"><?php esc_html_e( 'No data available', 'ebox' ); ?></td>
					</tr>
					<?php
			}
			?>
		</tbody>
	</table>
		<?php
		$total_quizzes = 0;
		if ( is_a( $quiz_query_results, 'WP_Query' ) ) {
			$pager_results = array(
				'paged'       => $quiz_page,
				'total_items' => absint( $quiz_query_results->found_posts ),
				'total_pages' => absint( $quiz_query_results->max_num_pages ),
			);
			$total_quizzes = absint( $quiz_query_results->found_posts );

			echo ebox_LMS::get_template( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Need to output HTML
				'ebox_pager.php',
				array(
					'href_query_arg' => 'paged',
					'pager_results'  => $pager_results,
					'pager_context'  => 'course_list',
				)
			);
		}
		?>


	<p>
		<?php if ( current_user_can( 'wpProQuiz_import' ) ) { ?>
		<a class="button-secondary wpProQuiz_import" href="#"><?php esc_html_e( 'Import', 'ebox' ); ?></a>
		<?php } if ( current_user_can( 'wpProQuiz_export' ) && $total_quizzes ) { ?>
		<a class="button-secondary wpProQuiz_export" href="#"><?php esc_html_e( 'Export', 'ebox' ); ?></a>
		<?php } ?>
	</p>
	<div class="wpProQuiz_exportList">
		<form action="admin.php?page=ldAdvQuiz&module=importExport&action=export&noheader=true" method="POST">
			<h3 style="margin-top: 0;"><?php esc_html_e( 'Export', 'ebox' ); ?></h3>
			<p><?php esc_html_e( 'Choose the respective Quiz, which you would like to export and press on "Start export"', 'ebox' ); ?></p>
			<div style="clear: both; margin-bottom: 10px;"></div>
			<div id="exportHidden"></div>
			<div style="margin-bottom: 15px;">
				<?php esc_html_e( 'Format:', 'ebox' ); ?>
				<label><input type="radio" name="exportType" value="wpq" checked="checked"> <?php esc_html_e( '*.wpq', 'ebox' ); ?></label>
				<?php esc_html_e( 'or', 'ebox' ); ?>
				<label><input type="radio" name="exportType" value="xml"> <?php esc_html_e( '*.xml', 'ebox' ); ?></label>
			</div>
			<input class="button-primary" name="exportStart" id="exportStart" value="<?php esc_html_e( 'Start export', 'ebox' ); ?>" type="submit">
		</form>
	</div>
	<div class="wpProQuiz_importList">
		<form action="admin.php?page=ldAdvQuiz&module=importExport&action=import" method="POST" enctype="multipart/form-data">
			<h3 style="margin-top: 0;"><?php esc_html_e( 'Import', 'ebox' ); ?></h3>
			<p><?php esc_html_e( 'Import only *.wpq or *.xml files from known and trusted sources.', 'ebox' ); ?></p>
			<div style="margin-bottom: 10px">
			<?php
				$maxUpload   = (int) ( ini_get( 'upload_max_filesize' ) );
				$maxPost     = (int) ( ini_get( 'post_max_size' ) );
				$memoryLimit = (int) ( ini_get( 'memory_limit' ) );
				$uploadMB    = min( $maxUpload, $maxPost, $memoryLimit );
			?>
				<input type="file" name="import" accept=".wpq,.xml" required="required">
				<?php
				// translators: placeholder: Upload Mb.
				printf( esc_html_x( 'Maximal %d MiB', 'placeholder: Upload Mb', 'ebox' ), esc_html( $uploadMB ) );
				?>
			</div>
			<input class="button-primary" name="exportStart" id="exportStart" value="<?php esc_html_e( 'Start import', 'ebox' ); ?>" type="submit">
		</form>
	</div>
</div>

		<?php
	}
}
