<?php
/**
 * ebox Settings Section Side Quick Links Metabox.
 *
 * @since 2.6.0
 * @package ebox\Settings\Sections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Section' ) ) && ( ! class_exists( 'ebox_Settings_Section_Side_Quick_Links' ) ) ) {
	/**
	 * Class ebox Settings Section Side Quick Links Metabox.
	 *
	 * @since 2.6.0
	 */
	class ebox_Settings_Section_Side_Quick_Links extends ebox_Settings_Section {

		/**
		 * Public constructor for class
		 *
		 * @since 2.6.0
		 *
		 * @param array $args Array of class args.
		 */
		public function __construct( $args = array() ) {

			if ( ( isset( $args['settings_screen_id'] ) ) && ( ! empty( $args['settings_screen_id'] ) ) ) {
				$this->settings_screen_id = $args['settings_screen_id'];
			}

			if ( ( isset( $args['settings_page_id'] ) ) && ( ! empty( $args['settings_page_id'] ) ) ) {
				$this->settings_page_id = $args['settings_page_id'];
			}

			if ( ( ! empty( $this->settings_screen_id ) ) && ( ! empty( $this->settings_page_id ) ) ) {
				// This is the 'option_name' key used in the wp_options table.
				$this->setting_option_key = 'quick_links_div';

				// Section label/header.
				$this->settings_section_label = esc_html__( 'Quick Links', 'ebox' );

				$this->metabox_context  = 'side';
				$this->metabox_priority = 'high';

				parent::__construct();
			}
		}

		/**
		 * Show custom metabox output for Quick Links.
		 *
		 * @since 2.6.0
		 */
		public function show_meta_box() {
			global $wp_meta_boxes;
			?>
			<div id="ld_quick-links" class="submitbox">
				<?php
				$q_links = array();
				if ( ( isset( $wp_meta_boxes[ $this->settings_screen_id ] ) ) && ( ! empty( $wp_meta_boxes[ $this->settings_screen_id ] ) ) ) {
					foreach ( $wp_meta_boxes[ $this->settings_screen_id ] as $mb_context => $mb_set_priority ) {
						if ( 'side' !== $mb_context ) {
							foreach ( $mb_set_priority as $priority => $mb_set ) {
								if ( ! empty( $mb_set ) ) {
									foreach ( $mb_set as $mb ) {
										if ( ( ! empty( $mb['id'] ) ) && ( ! empty( $mb['title'] ) ) ) {
											$q_links[ $mb['id'] ] = $mb['title'];
										}
									}
								}
							}
						}
					}

					if ( ! empty( $q_links ) ) {
						echo '<ul>';
						$meta_box_order = get_user_option( 'meta-box-order_' . $this->settings_screen_id );
						if ( ( isset( $meta_box_order['normal'] ) ) && ( ! empty( $meta_box_order['normal'] ) ) ) {
							$meta_box_order_items = explode( ',', $meta_box_order['normal'] );
							foreach ( $meta_box_order_items as $meta_box_order_item ) {
								$meta_box_order_item = trim( $meta_box_order_item );
								if ( isset( $q_links[ $meta_box_order_item ] ) ) {
									echo '<li><a href="#' . esc_attr( $meta_box_order_item ) . '" >' . esc_html( $q_links[ $meta_box_order_item ] ) . '</a></li>';
									unset( $q_links[ $meta_box_order_item ] );
								}
							}
						}

						if ( ! empty( $q_links ) ) {
							foreach ( $q_links as $link_id => $link_title ) {
								echo '<li><a href="#' . esc_attr( $link_id ) . '" >' . esc_html( $link_title ) . '</a></li>';
							}
						}
						echo '</ul>';
					}
				}
				?>
			</div><!-- #submitpost -->
			<?php
		}

		/**
		 * Load settings fields
		 *
		 * This is a requires function.
		 */
		public function load_settings_fields() {
		}
	}
}
