<?php
/**
 * ebox Admin Filter Post Title.
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
	! class_exists( 'ebox_Admin_Filter_Post_Title' )
) {
	/**
	 * Filters by post title.
	 *
	 * @since 4.2.0
	 */
	class ebox_Admin_Filter_Post_Title extends ebox_Admin_Filter_Post {
		/**
		 * Construct.
		 *
		 * @since 4.2.0
		 *
		 * @param string $post_label The post type label.
		 */
		public function __construct( string $post_label ) {
			parent::__construct( 'post_title', $post_label . ' ' . __( 'Title', 'ebox' ) );
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
			<input
				type="text"
				name="<?php echo esc_attr( $this->get_parameter_name() ); ?>"
				class="<?php echo esc_attr( $this->get_input_class() ); ?>"
				placeholder="<?php esc_attr_e( 'contains this', 'ebox' ); ?>"
				autocomplete="off"
			/>
			<?php
		}
	}
}
