<?php
/**
 * Handles all server side logic for the ld-teaminfo Gutenberg Block. This block is functionally the same
 * as the teaminfo shortcode used within ebox.
 *
 * @package ebox
 * @since 2.5.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Gutenberg_Block' ) ) && ( ! class_exists( 'ebox_Gutenberg_Block_Teaminfo' ) ) ) {
	/**
	 * Class for handling ebox Teaminfo Block
	 */
	class ebox_Gutenberg_Block_Teaminfo extends ebox_Gutenberg_Block {

		/**
		 * Object constructor
		 */
		public function __construct() {

			$this->shortcode_slug   = 'teaminfo';
			$this->block_slug       = 'ld-teaminfo';
			$this->block_attributes = array(
				'show'              => array(
					'type' => 'string',
				),
				'team_id'          => array(
					'type' => 'string',
				),
				'user_id'           => array(
					'type' => 'string',
				),
				'format'            => array(
					'type' => 'string',
				),
				'decimals'          => array(
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
		 * @since 3.2.0
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
				$block_attributes['team_id'] = $this->block_attributes_get_post_id( $block_attributes, 'team' );
				if ( empty( $block_attributes['team_id'] ) ) {
					if ( ( ! isset( $block_attributes_meta['team_id'] ) ) || ( empty( $block_attributes_meta['team_id'] ) ) ) {
						return $this->render_block_wrap(
							'<span class="ebox-block-error-message">' . sprintf(
							// translators: placeholder: Team, Team.
								_x( '%1$s ID is required when not used within a %2$s.', 'placeholder: Team, Team', 'ebox' ),
								ebox_Custom_Label::get_label( 'team' ),
								ebox_Custom_Label::get_label( 'team' )
							) . '</span>'
						);
					}
				}

				if ( ! empty( $block_attributes['team_id'] ) ) {
					$team_post = get_post( (int) $block_attributes['team_id'] );
					if ( ( ! is_a( $team_post, 'WP_Post' ) ) || ( ebox_get_post_type_slug( 'team' ) !== $team_post->post_type ) ) {
						return $this->render_block_wrap(
							'<span class="ebox-block-error-message">' . sprintf(
							// translators: placeholder: Team.
								_x( 'Invalid %1$s ID.', 'placeholder: Team', 'ebox' ),
								ebox_Custom_Label::get_label( 'team' )
							) . '</span>'
						);
					}
				}
			}

			/** This filter is documented in includes/gutenberg/blocks/ld-course-list/index.php */
			$block_attributes = apply_filters( 'ebox_block_markers_shortcode_atts', $block_attributes, $this->shortcode_slug, $this->block_slug, '' );

			$shortcode_out = '';

			$shortcode_str = $this->prepare_course_list_atts_to_param( $block_attributes );
			$shortcode_str = '[' . $this->shortcode_slug . ' ' . $shortcode_str . ']';

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
new ebox_Gutenberg_Block_Teaminfo();
