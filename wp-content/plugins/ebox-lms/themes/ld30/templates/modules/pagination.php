<?php
/**
 * ebox LD30 Displays pagination for the module
 *
 * @since 3.0.0
 *
 * @package ebox\Templates\LD30\Modules
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
* Available Variables:
* $pager_context : (string) value defining context of pager output. For example 'course_modules' would be the course template modules listing.
* $pager_results : (array) query result details containing
* results<pre>Array
* (
*    [paged] => 1
*    [total_items] => 30
*    [total_pages] => 2
* )
*/

if ( ( isset( $pager_results ) ) && ( ! empty( $pager_results ) ) ) {

	if ( ! isset( $pager_context ) ) {
		$pager_context = '';
	}

	if ( ! isset( $href_val_prefix ) ) {
		$href_val_prefix = '';
	}

	if ( isset( $atts ) && isset( $atts['num'] ) ) {
		$pager_results['num'] = $atts['num'];
	}

	$pager_json = htmlspecialchars( wp_json_encode( $pager_results ) );

	// Generic wrappers. These can be changes via the switch below.
	$wrapper_before = '<div class="ld-pagination ld-pagination-page-' . esc_attr( $pager_context ) . '" data-pager-nonce="' . wp_create_nonce( 'ld30_ajax_pager' ) . '" data-pager-results="' . $pager_json . '">
						<div class="ld-pages">';
	$wrapper_after  = '</div>
						</div>';

	if ( $pager_results['total_pages'] > 1 ) {
		if ( ( ! isset( $href_query_arg ) ) || ( empty( $href_query_arg ) ) ) {

			switch ( $pager_context ) {
				case 'course_modules':
					$href_query_arg = 'ld-lesson-page';
					break;

				case 'course_lesson_topics':
					$href_query_arg = 'ld-topic-page';
					break;

				case 'profile':
					$href_query_arg = 'ld-profile-page';
					break;

				case 'course_content_shortcode':
				case 'course_content':
					$href_query_arg = 'ld-courseinfo-lesson-page';
					break;

				case 'course_info_courses':
					$href_query_arg = 'ld-user-status';
					break;

				case 'team_courses':
					$href_query_arg = 'ld-team-courses-page';
					break;

				case 'profile_quizzes':
					$href_query_arg = 'profile-quizzes';
					break;

				// These are just here to show the existing different context items.
				case 'course_info_registered':
				case 'course_info_quizzes':
				case 'course_navigation_widget':
				case 'course_navigation_admin':
				case 'course_list':
				default:
					break;
			}
		}

		$pager_left_disabled = '';
		$pager_left_class    = '';
		if ( 1 === absint( $pager_results['paged'] ) ) {
			$pager_left_disabled = ' disabled="disabled" ';
			$pager_left_class    = 'disabled';
		}
		$prev_page_number = ( $pager_results['paged'] > 1 ) ? $pager_results['paged'] - 1 : 1;

		$pager_right_disabled = '';
		$pager_right_class    = '';
		if ( $pager_results['paged'] == $pager_results['total_pages'] ) {
			$pager_right_disabled = ' disabled="disabled" ';
			$pager_right_class    = 'disabled';
		}
		$next_page_number = ( $pager_results['paged'] < $pager_results['total_pages'] ) ? $pager_results['paged'] + 1 : $pager_results['total_pages'];

		$data_course_id = ( isset( $course_id ) ? 'data-course_id="' . absint( $course_id ) . '"' : '' );
		$data_lesson_id = ( isset( $lesson_id ) ? 'data-lesson_id="' . absint( $lesson_id ) . '"' : '' );
		$data_team_id  = ( isset( $team_id ) ? 'data-team_id="' . absint( $team_id ) . '"' : '' );

		$search_arg = ( isset( $search ) ? '&ld-profile-search=' . $search : '' );

		/**
		 * Fires before the pagination wrapper.
		 *
		 * @since 3.0.0
		 */
		do_action( 'ebox_pagination_before_wrapper' );

		echo $wrapper_before; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Outputs HTML

		/**
		 *
		 * Fires before the register modal heading.
		 *
		 * @since 3.0.0
		 */
		do_action( 'ebox_pagination_before' ); ?>

		<a class="prev ld-primary-color-hover <?php echo esc_attr( $pager_left_class ); ?>" <?php if ( ( isset( $href_query_arg ) ) && ( ! empty( $href_query_arg ) ) ) { ?>
			href="<?php echo esc_url( add_query_arg( $href_query_arg, $href_val_prefix . $prev_page_number ) ); ?>"
		<?php } ?> data-context="<?php echo esc_attr( $pager_context ); ?>" data-paged="<?php echo esc_attr( $href_val_prefix . $prev_page_number . $search_arg ); ?>" <?php echo $data_course_id; ?> <?php echo $data_lesson_id; ?> <?php echo $data_team_id; ?> class="<?php echo esc_attr( $pager_left_class ); ?>" <?php echo esc_attr( $pager_left_disabled ); ?> title="<?php esc_attr_e( 'Previous Page', 'ebox' ); ?>" aria-label="<?php esc_attr_e( 'Previous Page', 'ebox' ); ?>">
		<?php if ( is_rtl() ) { ?>
			<span class="ld-icon-arrow-right ld-icon"></span></a>
		<?php } else { ?>
			<span class="ld-icon-arrow-left ld-icon"></span></a>
		<?php } ?>
		</span>
		<span>
		<?php
		// translators: placeholder: current page numer of total pages.
		echo sprintf(
			// translators: placeholder: current page number of total pages.
			esc_html_x( '%1$d of %2$d', 'placeholder: current page numer of total pages', 'ebox' ),
			esc_html( $pager_results['paged'] ),
			esc_html( $pager_results['total_pages'] )
		);
		?>
		</span>
			<a class="next ld-primary-color-hover <?php echo esc_attr( $pager_right_class ); ?>" <?php if ( ( isset( $href_query_arg ) ) && ( ! empty( $href_query_arg ) ) ) { ?>
				href="<?php echo esc_url( add_query_arg( $href_query_arg, $href_val_prefix . $next_page_number ) ); ?>"
			<?php } ?> data-context="<?php echo esc_attr( $pager_context ); ?>" data-paged="<?php echo esc_attr( $href_val_prefix . $next_page_number . $search_arg ); ?>" <?php echo $data_course_id; ?> <?php echo $data_lesson_id; ?> <?php echo $data_team_id; ?> class="<?php echo esc_attr( $pager_right_class ); ?>" <?php echo esc_attr( $pager_right_disabled ); ?> title="<?php esc_attr_e( 'Next Page', 'ebox' ); ?>" aria-label="<?php esc_attr_e( 'Next Page', 'ebox' ); ?>">
			<?php if ( is_rtl() ) { ?>
				<span class="ld-icon-arrow-left ld-icon"></span></a>
			<?php } else { ?>
				<span class="ld-icon-arrow-right ld-icon"></span></a>
			<?php } ?>

		<?php

		/**
		 *
		 * Fires before the register modal heading.
		 *
		 * @since 3.0.0
		 */
		do_action( 'ebox_pagination_after' );

		echo $wrapper_after; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Outputs HTML

		/**
		 * Fires after the pagination wrapper.
		 *
		 * @since 3.0.0
		 */
		do_action( 'ebox_pagination_after_wrapper' );

	}
}
