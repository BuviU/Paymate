<?php
/**
 * ebox Admin Filter Post Status.
 *
 * @since 4.2.0
 *
 * @package ebox\Filters
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (
	class_exists( 'ebox_Admin_Filter_Post' ) &&
	! class_exists( 'ebox_Admin_Filter_Post_Status' )
) {
	/**
	 * Filters by post status.
	 *
	 * @since 4.2.0
	 */
	class ebox_Admin_Filter_Post_Status extends ebox_Admin_Filter_Post {
		/**
		 * Construct.
		 *
		 * @since 4.2.0
		 *
		 * @param string $post_label The post type label.
		 */
		public function __construct( string $post_label ) {
			parent::__construct( 'post_status', $post_label . ' ' . __( 'Status', 'ebox' ) );
		}

		/**
		 * Echoes the input HTML.
		 *
		 * @since 4.2.0
		 *
		 * @return void
		 */
		public function display(): void {
			?>
			<div class="ebox_option_input">
				<select
					name="<?php echo esc_attr( $this->get_parameter_name() ); ?>"
					class="<?php echo esc_attr( $this->get_input_class() ); ?>"
					multiple="multiple"
					autocomplete="off"
					type="select"
					data-ld-select2="1"
				>
					<?php foreach ( ebox_get_step_post_statuses() as $value => $label ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>">
							<?php echo esc_html( $label ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
			<?php
		}
	}
}
