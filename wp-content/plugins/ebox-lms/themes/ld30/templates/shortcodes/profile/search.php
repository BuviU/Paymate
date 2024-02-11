<?php
/**
 * ebox LD30 Displays a user's profile search.
 *
 * @since 3.0.0
 *
 * @package ebox\Templates\LD30
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ebox_profile_search_query = (
	isset( $_GET['ld-profile-search'], $_GET['ld-profile-search-nonce'] ) &&
	! empty( $_GET['ld-profile-search'] ) &&
	wp_verify_nonce( $_GET['ld-profile-search-nonce'], 'ebox_profile_course_search_nonce' ) ?
	sanitize_text_field( $_GET['ld-profile-search'] ) :
	false
);

$ebox_search_is_expanded = ( false !== (bool) $ebox_profile_search_query ? 'ld-expanded' : '' ); ?>
<div class="ld-item-search ld-expandable <?php echo esc_attr( $ebox_search_is_expanded ); ?>" id="ld-course-search" data-ld-expand-id="ld-course-search">
<div class="ld-item-search-wrapper">

	<div class="ld-closer"><?php echo esc_html_e( 'close', 'ebox' ); ?></div>

	<h4>
	<?php
		printf(
			// translators: Profile Search Courses.
			esc_html_x( 'Search Your %s', 'Profile Search Courses', 'ebox' ),
			esc_attr( ebox_Custom_Label::get_label( 'courses' ) )
		);
		?>
	</h4>

	<form method="get" action="" class="ld-item-search-fields" data-nonce="<?php echo esc_attr( wp_create_nonce( 'ebox_profile_course_search_nonce' ) ); ?>">

		<div class="ld-item-search-name">
			<label for="course_name_field">
			<?php
				printf(
					// translators: Profile Course Label.
					esc_html_x( '%s Name', 'Profile Course Label', 'ebox' ),
					esc_attr( ebox_Custom_Label::get_label( 'course' ) )
				);
				?>
			</label>
			<input type="text" id="course_name_field" value="<?php echo esc_attr( $ebox_profile_search_query ); ?>" class="ld-course-nav-field" name="ld-profile-search">
			<?php if ( false !== (bool) $ebox_profile_search_query ) : ?>
				<a href="<?php the_permalink(); ?>" class="ld-reset-button"><?php esc_html_e( 'reset', 'ebox' ); ?></a>
			<?php endif; ?>
			<input type="hidden" name="ld-profile-page" value="1">
		</div> <!--/.ld-course-search-name-->
		<?php
		/*
		* Shortcode doesn't support search by status at this time
		*
		<div class="ld-item-search-status">
			<label for="course_status"><?php echo esc_html_e( 'Status', 'ebox' ); ?></label>
			<div class="ld-select-field">
				<select name="course_status">
					<?php
					$options = apply_filters( 'ebox_course_search_statues', array(
						array(
							'value' =>  'progress',
							'title' =>  __( 'In Progress', 'ebox' )
						),
						array(
							'value' =>  'completed',
							'title' =>  __( 'Completed', 'ebox' )
						),
					) );
					foreach( $options as $option ): ?>
						<option value="<?php echo esc_attr($option['value']); ?>"><?php echo esc_html($option['title']); ?></option>
					<?php
					endforeach; ?>
				</select>
			</div>
		</div> <!--/.ld-course-search-status-->
		*/
		?>

		<div class="ld-item-search-submit">
			<input type="submit" class="ld-button" value="<?php esc_html_e( 'Search', 'ebox' ); ?>" name="submit">
		</div> <!--/.ld-course-search-submit-->
		<?php
		$ebox_profile_course_search_nonce_field = 'ebox_profile_course_search_nonce_field_' . $user_id;
		wp_nonce_field( 'ebox_profile_course_search_nonce', $ebox_profile_course_search_nonce_field );
		?>
	</form> <!--/.ld-course-search-fields-->
</div> <!--/.ld-course-search-wrapper-->
</div> <!--/.ld-course-search-->
<?php
if ( $ebox_profile_search_query ) :
	ebox_get_template_part(
		'shortcodes/profile/search-results.php',
		array(
			'ebox_profile_search_query' => $ebox_profile_search_query,
		),
		true
	);
endif; ?>
