<?php
/**
 * Handles all server side logic for the ld-user-teams Gutenberg Block. This block is functionally the same
 * as the user_teams shortcode used within ebox.
 *
 * @package ebox
 * @since 2.5.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Gutenberg_Block' ) ) && ( ! class_exists( 'ebox_Gutenberg_Block_User_Teams' ) ) ) {
	/**
	 * Class for handling ebox User Teams Block
	 */
	class ebox_Gutenberg_Block_User_Teams extends ebox_Gutenberg_Block {

		/**
		 * Object constructor
		 */
		public function __construct() {

			$this->shortcode_slug   = 'user_teams';
			$this->block_slug       = 'ld-user-teams';
			$this->block_attributes = array(
				'user_id'           => array(
					'type' => 'string',
				),
				'per_page'          => array(
					'type' => 'integer',
				),
				'order'             => array(
					'type' => 'string',
				),
				'orderby'           => array(
					'type' => 'string',
				),
				'preview_show'      => array(
					'type' => 'boolean',
				),
				'preview_user_id'   => array(
					'type' => 'string',
				),
				'editing_post_meta' => array(
					'type' => 'object',
				),
			);
			$this->self_closing     = true;

			$this->init();
		}

		/**
		 * Render Block
		 *
		 * This function is called per the register_block_type() function above. This function will output
		 * the block rendered content. In the case of this function the rendered output will be for the
		 * [ld_profile] shortcode.
		 *
		 * @since 2.5.9
		 *
		 * @param array    $block_attributes The block attributes.
		 * @param string   $block_content    The block content.
		 * @param WP_block $block            The block object.
		 *
		 * @return none The output is echoed.
		 */
		public function render_block( $block_attributes = array(), $block_content = '', WP_block $block = null ) {
			$block_attributes = $this->preprocess_block_attributes( $block_attributes );

			// Only the 'editing_post_meta' element will be sent from within the post edit screen.
			if ( $this->block_attributes_is_editing_post( $block_attributes ) ) {
				$block_attributes['user_id'] = $this->block_attributes_get_user_id( $block_attributes );
			}

			/** This filter is documented in includes/gutenberg/blocks/ld-course-list/index.php */
			$block_attributes = apply_filters( 'ebox_block_markers_shortcode_atts', $block_attributes, $this->shortcode_slug, $this->block_slug, '' );

			$shortcode_out = '';

			$shortcode_str = $this->build_block_shortcode( $block_attributes, $block_content );
			if ( ! empty( $shortcode_str ) ) {
				$shortcode_out = do_shortcode( $shortcode_str );
			}

			if ( ! empty( $shortcode_out ) ) {
				if ( $this->block_attributes_is_editing_post( $block_attributes ) ) {
					$shortcode_out = $this->render_block_wrap( $shortcode_out );
				} else {
					$shortcode_out = '<div class="ebox-wrap">' . $shortcode_out . '</div>';
				}
			}

			return $shortcode_out;
		}

		// End of functions.
	}
}
new ebox_Gutenberg_Block_User_Teams();
