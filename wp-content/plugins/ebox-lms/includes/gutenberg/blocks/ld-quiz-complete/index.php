<?php
/**
 * Handles all server side logic for the ld-quiz-complete Gutenberg Block. This block is functionally the same
 * as the [ld_quiz_complete] shortcode used within ebox.
 *
 * @package ebox
 * @since 3.1.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Gutenberg_Block' ) ) && ( ! class_exists( 'ebox_Gutenberg_Block_Quiz_Complete' ) ) ) {
	/**
	 * Class for handling ebox ebox_Gutenberg_Block_ebox_Gutenberg_Block_Quiz_Complete Block
	 */
	class ebox_Gutenberg_Block_Quiz_Complete extends ebox_Gutenberg_Block {

		/**
		 * Object constructor
		 */
		public function __construct() {
			$this->shortcode_slug = 'ld_quiz_complete';
			$this->block_slug     = 'ld-quiz-complete';
			$this->self_closing   = false;

			$this->block_attributes = array(
				'course_id' => array(
					'type' => 'string',
				),
				'quiz_id'   => array(
					'type' => 'string',
				),
				'user_id'   => array(
					'type' => 'string',
				),
				'autop'     => array(
					'type' => 'boolean',
				),
			);

			$this->init();
		}

		/**
		 * Render Block
		 *
		 * This function is called per the register_block_type() function above. This function will output
		 * the block rendered content. In the case of this function the rendered output will be for the
		 * [ld_profile] shortcode.
		 *
		 * @since 3.1.4
		 *
		 * @param array    $block_attributes The block attributes.
		 * @param string   $block_content    The block content.
		 * @param WP_block $block            The block object.
		 *
		 * @return none The output is echoed.
		 */
		public function render_block( $block_attributes = array(), $block_content = '', WP_block $block = null ) {
			$block_attributes = $this->preprocess_block_attributes( $block_attributes );

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
new ebox_Gutenberg_Block_Quiz_Complete();
