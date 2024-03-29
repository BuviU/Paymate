<?php
/**
 * Displays the course admin details.
 *
 * @since 2.6.0
 *
 * @package ebox\Templates\Legacy\Course
 *
 * @param integer $user_id User ID currently displayed.
 * @param integer $course_id Course ID currently displayed.
 * @param array   $course_progress User's course progress for courses.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wp_locale;

// Ensure the user has access to the course.
if ( ( ! empty( $course_id ) ) && ( ! empty( $user_id ) ) && ( ebox_lms_has_access( $course_id, $user_id ) ) ) {
	// Ensure the enrollment is not via a team.
	$team_enrolled_since = ebox_user_team_enrolled_to_course_from( $user_id, $course_id );
	if ( empty( $team_enrolled_since ) ) {
		$course_enrolled_since = ld_course_access_from( $course_id, $user_id );
		?>
		<div class="ebox-user-courses-access-edit">
			<?php esc_html_e( 'Set Enrolled Date:', 'ebox' ); ?>
			<input type="checkbox" class="ebox-user-courses-access-changed" title="<?php esc_html_e( 'Edit date', 'ebox' ); ?>" name="ebox-user-courses-access-changed[<?php echo intval( $user_id ); ?>][]" value="<?php echo intval( $course_id ); ?>" />
			<?php
			if ( empty( $course_enrolled_since ) ) {
				$value_jj = '';
				$value_mm = '';
				$value_aa = '';
				$value_hh = '';
				$value_mn = '';

			} else {
				$course_enrolled_since = ebox_adjust_date_time_display( $course_enrolled_since, 'Y-m-d H:i:s' );
				$course_enrolled_since = strtotime( $course_enrolled_since );

				$value_jj = (int) gmdate( 'd', $course_enrolled_since );
				$value_mm = (int) gmdate( 'n', $course_enrolled_since );
				$value_aa = (int) gmdate( 'Y', $course_enrolled_since );
				$value_hh = (int) gmdate( 'H', $course_enrolled_since );
				$value_mn = (int) gmdate( 'i', $course_enrolled_since );
			}

				$field_name = 'ebox-user-courses-access[' . $user_id . '][' . $course_id . ']';

				$month_field = '<span class="screen-reader-text">' . esc_html__( 'Month', 'ebox' ) . '</span><select disabled="disabled" data-default="' . $value_mm . '" class="ld_date_mm" name="' . $field_name . '[mm]" ><option value=""></option>';
			for ( $i = 1; $i < 13; $i++ ) {
				$monthnum = zeroise( $i, 2 );

				$selected_mm = selected( $i, $value_mm, false );

				$monthtext    = $wp_locale->get_month_abbrev( $wp_locale->get_month( $i ) );
				$month_field .= "\t\t\t" . '<option value="' . $i . '" data-text="' . $monthtext . '" ' . $selected_mm . '>';
				// translators: placeholder: month number, month text.
				$month_field .= sprintf( esc_html_x( '%1$s-%2$s', 'placeholder: month number, month text', 'ebox' ), $monthnum, $monthtext ) . "</option>\n";
			}
				$month_field .= '</select>';

				$day_field = '<span class="screen-reader-text">' . esc_html__( 'Day', 'ebox' ) . '</span><input disabled="disabled" data-default="' . $value_jj . '" type="number" placeholder="DD" min="1" max="31" class="small-text ld_date_jj" name="' . $field_name . '[jj]" value="' . $value_jj . '" size="2" maxlength="2" autocomplete="off" />';

				$year_field = '<span class="screen-reader-text">' . esc_html__( 'Year', 'ebox' ) . '</span><input disabled="disabled" data-default="' . $value_aa . '" type="number" placeholder="YYYY" min="0000" max="9999" class="small-text ld_date_aa" name="' . $field_name . '[aa]" value="' . $value_aa . '" size="4" maxlength="4" autocomplete="off" />';

				$hour_field = '<span class="screen-reader-text">' . esc_html__( 'Hour', 'ebox' ) . '</span><input disabled="disabled" data-default="' . $value_hh . '" type="number" min="0" max="23" placeholder="HH" class="small-text ld_date_hh" name="' . $field_name . '[hh]" value="' . $value_hh . '" size="2" maxlength="2" autocomplete="off" />';

				$minute_field = '<span class="screen-reader-text">' . esc_html__( 'Minute', 'ebox' ) . '</span><input disabled="disabled" data-default="' . $value_mn . '" type="number" min="0" max="59" placeholder="MM" class="small-text ld_date_mn" name="' . $field_name . '[mn]" value="' . $value_mn . '" size="2" maxlength="2" autocomplete="off" />';

				$field_buf = sprintf(
					// translators: placeholders: Month Name, Day number, Year number, Hour number, Minute number.
					esc_html__( '%1$s %2$s, %3$s @ %4$s:%5$s', 'ebox' ),
					$month_field,
					$day_field,
					$year_field,
					$hour_field,
					$minute_field
				);
				echo $field_buf;

				/*
				?> <input type="button" disabled="disabled" class="ebox-user-courses-access-today button button-secondary" title="<?php esc_html_e( 'Set date to today', 'ebox' ); ?>" value="<?php esc_html_e( 'today', 'ebox' ); ?>" /><?php
				*/
			?>
		</div>
		<?php
	}
}
