<?php
/**
 * Displays the Quiz Switcher displayed within the Quiz Questions admin widget.
 * Available Variables:
 * none
 *
 * @since 2.6.0
 *
 * @package ebox\Templates\Legacy
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Quizzes_Builder', 'shared_questions' ) !== 'yes' ) {
	return;
}

if ( ( isset( $_GET['post'] ) ) && ( ! empty( $_GET['post'] ) ) ) {
	$post = get_post( intval( $_GET['post'] ) );
	if ( is_a( $post, 'WP_Post' ) && ( in_array( $post->post_type, array( 'ebox-question' ) ) ) ) {
		$cb_quizzes      = ebox_get_quizzes_for_question( $post->ID );
		$count_primary   = 0;
		$count_secondary = 0;

		if ( isset( $cb_quizzes['primary'] ) ) {
			$count_primary = count( $cb_quizzes['primary'] );
		}

		if ( isset( $cb_quizzes['secondary'] ) ) {
			$count_secondary = count( $cb_quizzes['secondary'] );
		}

		if ( ( $count_primary > 0 ) || ( $count_secondary > 0 ) ) {
			$default_quiz_id = ebox_get_quiz_id( $post->ID, true );

			if ( ( 1 === $count_primary ) && ( empty( $count_secondary ) ) ) {
				if ( isset( $cb_quizzes['primary'][ $default_quiz_id ] ) ) {
					return;
				}
			}
			$use_select_opt_teams = false;
			if ( ( $count_primary > 0 ) && ( $count_secondary > 0 ) ) {
				$use_select_opt_teams = true;
			}

			$quiz_post_id = 0;
			if ( isset( $_GET['quiz_id'] ) ) {
				$quiz_post_id = intval( $_GET['quiz_id'] );
			}

			?><p class="widget_quiz_switcher">
			<?php
			echo sprintf(
				// translators: placeholder: Quiz.
				esc_html_x( '%s switcher', 'placeholder: Quiz', 'ebox' ),
				ebox_Custom_Label::get_label( 'quiz' )
			);
			?>
			<br />
			<?php
				$item_url = get_edit_post_link( $post->ID );
			?>
			<input type="hidden" id="ld-quiz-primary" name="ld-quiz-primary" value="<?php echo $default_quiz_id; ?>" />
			<select name="ld-quiz-switcher" id="ld-quiz-switcher">
				<option value="">
				<?php
				echo sprintf(
					// translators: placeholder: Quiz.
					esc_html_x( 'Select a %s', 'placeholder: Quiz', 'ebox' ),
					ebox_Custom_Label::get_label( 'Quiz' )
				);
				?>
				</option>
				<?php
				foreach ( $cb_quizzes as $quiz_key => $quiz_set ) {
					if ( true === $use_select_opt_teams ) {
						if ( 'primary' === $quiz_key ) {
							?>
							<optteam label="
							<?php
							echo sprintf(
								// translators: placeholder: Quiz.
								esc_html_x( 'Primary %s', 'placeholder: Quiz', 'ebox' ),
								ebox_Custom_Label::get_label( 'Quiz' )
							);
							?>
							">
											<?php
						} elseif ( 'secondary' === $quiz_key ) {
							?>
							<optteam label="
							<?php
							echo sprintf(
								// translators: placeholder: Quizzes.
								esc_html_x( 'Other %s', 'placeholder: Quizzes', 'ebox' ),
								ebox_Custom_Label::get_label( 'quizzes' )
							);
							?>
							">
							<?php
						}
					}

					foreach ( $quiz_set as $quiz_id => $quiz_title ) {
						$item_url = add_query_arg( 'quiz_id', $quiz_id, $item_url );
						$selected = '';

						if ( 'ebox-quiz' == $post->post_type ) {
							if ( $quiz_id == $quiz_post_id ) {
								$selected = ' selected="selected" ';
							}
						} else {
							if ( ( $quiz_id == $quiz_post_id ) || ( ( empty( $quiz_post_id ) ) && ( $quiz_id == $default_quiz_id ) ) ) {
								$selected = ' selected="selected" ';
							}
						}
						?>
						<option <?php echo $selected; ?> data-course_id="<?php echo $quiz_id; ?>" value="<?php echo $item_url; ?>"><?php echo get_the_title( $quiz_id ); ?></option>
						<?php
					}
					if ( $use_select_opt_teams === true ) {
						?>
						</optteam>
						<?php
					}
				}
				?>
			</select></p>
			<?php
		}
	}
}
