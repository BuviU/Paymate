<?php
/**
 * ebox LD30 focus mode comments list wrapper.
 *
 * @since 3.0.0
 *
 * @package ebox\Templates\LD30
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( post_password_required() ) {
	return;
}
?>
<div class="ld-focus-comments">
	<?php
	/**
	 * Fires before the comments in focus mode.
	 *
	 * @since 3.0.0
	 *
	 * @param int $course_id Course ID.
	 * @param int $user_id   User ID.
	 */
	do_action( 'ebox-focus-content-comments-before', $course_id, $user_id );

	$ebox_comment_count = wp_count_comments( get_the_id() );
	if ( ( ! empty( $ebox_comment_count ) ) && ( $ebox_comment_count->approved > 0 ) && ( ! isset( $_GET['replytocom'] ) ) ) {
		?>
		<div class="ld-focus-comments__heading">
			<div class="ld-focus-comments__header">
				<?php
				printf(
					esc_html(
						// translators: single approved comment, multiple approved comments.
						_nx( '%s Comment', '%s Comments', $ebox_comment_count->approved, 'comments', 'ebox' )
					),
					esc_html( number_format_i18n( $ebox_comment_count->approved ) )
				);
				?>
			</div>
			<div class="ld-focus-comments__heading-actions">
				<div class="ld-expand-button ld-button-alternate ld-expanded" id="ld-expand-button-comments" data-ld-expands="ld-comments" data-ld-expand-text="<?php esc_html_e( 'Expand Comments', 'ebox' ); ?>" data-ld-collapse-text="<?php esc_html_e( 'Collapse Comments', 'ebox' ); ?>">
				<span class="ld-text"><?php esc_html_e( 'Collapse Comments', 'ebox' ); ?></span>
				<span class="ld-icon-arrow-down ld-icon"></span>
				</div>
			</div>
		</div>
		<?php
	}
	?>

	<div class="ld-focus-comments__comments ld-expanded" id="ld-comments" data-ld-expand-id="ld-comments">
		<div class="ld-focus-comments__comments-items" id="ld-comments-wrapper">
			<?php
			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) {
				// Add filter to direct comments to our template.
				add_filter(
					'comments_template',
					function( $theme_template = '' ) {
						$theme_template_alt = ebox_LMS::get_template( 'focus/comments_list.php', null, null, true );
						if ( ! empty( $theme_template_alt ) ) {
							$theme_template = $theme_template_alt;
						}

						return $theme_template;
					},
					999,
					1
				);

				comments_template();

				if ( ! isset( $_GET['replytocom'] ) ) {
					the_comments_navigation();
				}
			}
			?>
		</div>
	</div>
	<?php
	/**
	 * Fires after the comments in focus mode.
	 *
	 * @since 3.0.0
	 *
	 * @param int $course_id Course ID.
	 * @param int $user_id   User ID.
	 */
	do_action( 'ebox-focus-content-comments-after', $course_id, $user_id );
	if ( ! empty( $ebox_comment_count ) && 0 === absint( $ebox_comment_count->approved ) ) :
		?>
	<div class="ld-expand-button ld-button-alternate" id="ld-comments-post-button">
		<span class="ld-icon-arrow-down ld-icon"></span>
		<span class="ld-text"><?php esc_html_e( 'Post a comment', 'ebox' ); ?></span>
	</div>
		<?php
	endif;
	$ebox_comment_form_state = ( ! empty( $ebox_comment_count ) && 0 === absint( $ebox_comment_count->approved ) ) ? ' ld-collapsed' : '';
	?>
	<div class="ld-focus-comments__form-container<?php echo esc_attr( $ebox_comment_form_state ); ?>" id="ld-comments-form">
		<?php
		/**
		 * Filters Focus mode comment form arguments.
		 *
		 * @since 3.0.0
		 *
		 * @param array $comment_arguments Focus mode comment form arguments to be used in comments_open function.
		 */
		$args = apply_filters(
			'ebox_focus_mode_comment_form_args',
			array(
				'title_reply' => esc_html__( 'Leave a Comment', 'ebox' ),
			)
		);

		comment_form( $args );
		?>
	</div>
	<?php
	/**
	 * Fires after the comment form in focus mode.
	 *
	 * @since 3.0.0
	 *
	 * @param int $course_id Course ID.
	 * @param int $user_id   User ID.
	 */
	do_action( 'ebox-focus-content-comment-form-after', $course_id, $user_id );
	?>
</div> <!--/ld-focus-comments-->
