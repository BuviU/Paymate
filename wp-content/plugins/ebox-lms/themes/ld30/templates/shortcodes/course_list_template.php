<?php
/**
 * ebox LD30 Displays course list
 *
 * @since 3.0.0
 *
 * @package ebox\Templates\LD30
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;

$course_id = (int) $shortcode_atts['course_id'];

if ( is_user_logged_in() ) {
	$user_id = get_current_user_id();
} else {
	$user_id = false;
}
?>

<div class="ebox-wrapper">
	<div class="ld-item-list">
		<div class="ld-item-list-item">
			<div class="ld-item-list-item-preview">
				<?php
				if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Teams_CPT', 'public' ) !== 'yes' && $shortcode_atts['post_type'] === 'teams' ) {
					echo esc_html( get_the_title() );
				} else {
					?>
					<a class="ld-item-name ld-primary-color-hover" href="<?php echo esc_url( ebox_get_step_permalink( get_the_ID() ) ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
					<?php
				}
				?>
			</div>
		</div>
	</div>

	<?php
	switch ( get_post_type() ) {

		case ( 'ebox-courses' ):
				$wrapper = array(
					'<div class="ebox-wrapper">
                        <div class="ld-item-list">',
					'</div>
                    </div>',
				);

				$output = ebox_get_template_part(
					'/course/partials/row.php',
					array(
						'course_id' => $course_id,
						'user_id'   => $user_id,
					)
				);


			break;

		case ( 'ebox-modules' ):
			global $course_modules_results;

			if ( isset( $course_modules_results['pager'] ) ) :
				ebox_get_template_part(
					'modules/pagination.php',
					array(
						'pager_results' => $course_modules_results['pager'],
						'pager_context' => 'course_modules',
					),
					true
				);
			endif;

			break;

		case ( 'ebox-topic' ):
			$wrapper = array(
				'<div class="ebox-wrapper">
                    <div class="ld-item-list">',
				'</div>
                </div>',
			);

			$output = ebox_get_template_part(
				'/topic/partials/row.php',
				array(
					'topic'     => $post,
					'course_id' => $course_id,
					'user_id'   => $user_id,
				)
			);

			break;
	}
	?>
</div>
