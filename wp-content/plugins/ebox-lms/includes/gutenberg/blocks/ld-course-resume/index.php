<?php
/**
 * Handles all server side logic for the ld-course-resume Gutenberg Block. This block is functionally the same
 * as the [ld_course_resume] shortcode used within ebox.
 *
 * @package ebox
 * @since 3.1.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Gutenberg_Block' ) ) && ( ! class_exists( 'ebox_Gutenberg_Block_Course_Resume' ) ) ) {
	/**
	 * Class for handling ebox ebox_Gutenberg_Block_Course_Resume Block
	 */
	class ebox_Gutenberg_Block_Course_Resume extends ebox_Gutenberg_Block {

		/**
		 * Object constructor
		 */
		public function __construct() {
			$this->shortcode_slug = 'ld_course_resume';
			$this->block_slug     = 'ld-course-resume';
			$this->self_closing   = true;

			$this->block_attributes = array(
				'course_id'         => array(
					'type' => 'string',
				),
				'user_id'           => array(
					'type' => 'string',
				),
				'label'             => array(
					'type' => 'string',
				),
				'html_class'        => array(
					'type' => 'string',
				),
				'button'            => array(
					'type' => 'string',
				),
				'preview_show'      => array(
					'type' => 'boolean',
				),
				'preview_user_id'   => array(
					'type' => 'string',
				),
				'example_show'      => array(
					'type' => 'boolean',
				),
				'editing_post_meta' => array(
					'type' => 'object',
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

			if ( ( isset( $block_attributes['example_show'] ) ) && ( ! empty( $block_attributes['example_show'] ) ) ) {
				$block_attributes['user_id']      = $this->get_example_user_id();
				$block_attributes['preview_show'] = true;

				unset( $block_attributes['example_show'] );
			}

			// Only the 'editing_post_meta' element will be sent from within the post edit screen.
			if ( $this->block_attributes_is_editing_post( $block_attributes ) ) {
				$block_attributes['course_id'] = $this->block_attributes_get_post_id( $block_attributes, 'course' );
				$block_attributes['user_id']   = $this->block_attributes_get_user_id( $block_attributes );

				if ( ! empty( $block_attributes['course_id'] ) ) {
					$course_post = get_post( $block_attributes['course_id'] );
					if ( ( ! is_a( $course_post, 'WP_Post' ) ) || ( ebox_get_post_type_slug( 'course' ) !== $course_post->post_type ) ) {
						return $this->render_block_wrap(
							'<span class="ebox-block-error-message">' . sprintf(
							// translators: placeholder: Course.
								_x( 'Invalid %1$s ID.', 'placeholder: Course', 'ebox' ),
								ebox_Custom_Label::get_label( 'course' )
							) . '</span>'
						);
					}
				}

				if ( empty( $block_attributes['course_id'] ) ) {
					$edit_post_type = $this->block_attributes_get_editing_post_type( $block_attributes );
					if ( ! in_array( $edit_post_type, ebox_get_post_types( 'course' ), true ) ) {
						return $this->render_block_wrap(
							'<span class="ebox-block-error-message">' . sprintf(
							// translators: placeholder: Course, Course.
								_x( '%1$s ID is required when not used within a %2$s.', 'placeholder: Course, Course', 'ebox' ),
								ebox_Custom_Label::get_label( 'course' ),
								ebox_Custom_Label::get_label( 'course' )
							) . '</span>'
						);
					}
				}
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
new ebox_Gutenberg_Block_Course_Resume();
