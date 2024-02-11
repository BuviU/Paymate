<?php
/**
 * Handles all server side logic for the ld-course-content Gutenberg Block. This block is functionally the same
 * as the course_content shortcode used within ebox.
 *
 * @package ebox
 * @since 2.5.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Gutenberg_Block' ) ) && ( ! class_exists( 'ebox_Gutenberg_Block_Course_Content' ) ) ) {
	/**
	 * Class for handling ebox Course Content Block
	 */
	class ebox_Gutenberg_Block_Course_Content extends ebox_Gutenberg_Block {

		/**
		 * Object constructor
		 */
		public function __construct() {
			$this->shortcode_slug   = 'course_content';
			$this->block_slug       = 'ld-course-content';
			$this->block_attributes = array(
				'display_type'      => array(
					'type' => 'string',
				),
				'course_id'         => array(
					'type' => 'string',
				),
				'post_id'           => array(
					'type' => 'string',
				),
				'team_id'          => array(
					'type' => 'string',
				),
				'per_page'          => array(
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

			if ( ( isset( $block_attributes['example_show'] ) ) && ( ! empty( $block_attributes['example_show'] ) ) ) {
				$block_attributes['course_id']    = $this->get_example_post_id( ebox_get_post_type_slug( 'course' ) );
				$block_attributes['preview_show'] = true;

				unset( $block_attributes['example_show'] );
			}

			// Only the 'editing_post_meta' element will be sent from within the post edit screen.
			if ( $this->block_attributes_is_editing_post( $block_attributes ) ) {
				/**
				 * We are only attempting to validate the block settings to display any errors to the user editing the post.
				 */
				$block_attributes['course_id'] = $this->block_attributes_get_post_id( $block_attributes, 'course' );
				$block_attributes['post_id']   = $this->block_attributes_get_post_id( $block_attributes, 'post' );
				$block_attributes['team_id']  = $this->block_attributes_get_post_id( $block_attributes, 'team' );

				if ( ( empty( $block_attributes['course_id'] ) ) && ( empty( $block_attributes['post_id'] ) ) && ( empty( $block_attributes['team_id'] ) ) ) {
					$edit_post_type = $this->block_attributes_get_editing_post_type( $block_attributes );
					$edit_post_id   = $this->block_attributes_get_editing_post_id( $block_attributes );

					if ( ebox_get_post_type_slug( 'team' ) === $edit_post_type ) {
						if ( ! empty( $edit_post_id ) ) {
							$block_attributes['team_id'] = $edit_post_id;
						}
					}

					if ( in_array( $edit_post_type, ebox_get_post_types( 'course' ), true ) ) {
						if ( ! empty( $edit_post_id ) ) {
							$block_attributes['post_id'] = $edit_post_id;
						}

						$course_id = $this->block_attributes_get_editing_course_id( $block_attributes );
						if ( ! empty( $course_id ) ) {
							$block_attributes['course_id'] = $course_id;
						} elseif ( ! empty( $edit_post_id ) ) {
							$course_id = ebox_get_course_id( $edit_post_id );
							if ( ! empty( $course_id ) ) {
								$block_attributes['course_id'] = $course_id;
							}
						}
					}
				}

				if ( ( empty( $block_attributes['course_id'] ) ) && ( empty( $block_attributes['team_id'] ) ) ) {
					return $this->render_block_wrap(
						'<span class="ebox-block-error-message">' . sprintf(
						// translators: placeholder: Course, Team.
							_x( '%1$s ID or %2$s ID is required.', 'placeholder: Course, Team', 'ebox' ),
							ebox_Custom_Label::get_label( 'course' ),
							ebox_Custom_Label::get_label( 'team' )
						) . '</span>'
					);
				}

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

				if ( ! empty( $block_attributes['team_id'] ) ) {
					$team_post = get_post( $block_attributes['team_id'] );
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

				if ( ! empty( $block_attributes['post_id'] ) ) {
					$post_post = get_post( $block_attributes['post_id'] );
					if ( ( ! is_a( $post_post, 'WP_Post' ) ) || ( ! in_array( $post_post->post_type, ebox_get_post_types( 'course' ), true ) ) ) {
						return $this->render_block_wrap(
							'<span class="ebox-block-error-message">' . esc_html__( 'Invalid Step ID.', 'ebox' ) . '</span>'
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
				}
			}

			return $shortcode_out;
		}

		/**
		 * Called from the LD function ebox_convert_block_markers_shortcode() when parsing the block content.
		 *
		 * @since 2.5.9
		 *
		 * @param array  $block_attributes The array of attributes parse from the block content.
		 * @param string $shortcode_slug This will match the related LD shortcode ld_profile, ld_course_list, etc.
		 * @param string $block_slug This is the block token being processed. Normally same as the shortcode but underscore replaced with dash.
		 * @param string $content This is the original full content being parsed.
		 *
		 * @return array $block_attributes.
		 */
		public function ebox_block_markers_shortcode_atts_filter( $block_attributes = array(), $shortcode_slug = '', $block_slug = '', $content = '' ) {
			if ( $shortcode_slug === $this->shortcode_slug ) {
				if ( isset( $block_attributes['per_page'] ) ) {
					if ( ! isset( $block_attributes['num'] ) ) {
						$block_attributes['num'] = $block_attributes['per_page'];
					}
				}
			}
			return $block_attributes;
		}

		// End of functions.
	}
}
new ebox_Gutenberg_Block_Course_Content();
