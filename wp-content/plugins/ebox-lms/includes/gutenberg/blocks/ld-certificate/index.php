<?php
/**
 * Handles all server side logic for the ld-certificate Gutenberg Block. This block is functionally the same
 * as the [ld_certificate] shortcode used within ebox.
 *
 * @package ebox
 * @since 3.1.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Gutenberg_Block' ) ) && ( ! class_exists( 'ebox_Gutenberg_Block_Certificate' ) ) ) {
	/**
	 * Class for handling ebox ebox_Gutenberg_Block_Certificate Block
	 */
	class ebox_Gutenberg_Block_Certificate extends ebox_Gutenberg_Block {

		/**
		 * Object constructor
		 */
		public function __construct() {
			$this->shortcode_slug = 'ld_certificate';
			$this->block_slug     = 'ld-certificate';
			$this->self_closing   = true;

			$this->block_attributes = array(
				'display_type'      => array(
					'type' => 'string',
				),
				'course_id'         => array(
					'type' => 'string',
				),
				'team_id'          => array(
					'type' => 'string',
				),
				'quiz_id'           => array(
					'type' => 'string',
				),
				'user_id'           => array(
					'type' => 'string',
				),
				'display_as'        => array(
					'type' => 'string',
				),
				'label'             => array(
					'type' => 'string',
				),
				'class_html'        => array(
					'type' => 'string',
				),
				'context'           => array(
					'type' => 'string',
				),
				'callback'          => array(
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
				$block_attributes['course_id']    = $this->get_example_post_id( ebox_get_post_type_slug( 'course' ) );
				$block_attributes['user_id']      = $this->get_example_user_id();
				$block_attributes['preview_show'] = true;
				$block_attributes['display_as']   = 'button';

				unset( $block_attributes['example_show'] );
			}

			// Only the 'editing_post_meta' element will be sent from within the post edit screen.
			if ( $this->block_attributes_is_editing_post( $block_attributes ) ) {
				$block_attributes['course_id'] = $this->block_attributes_get_post_id( $block_attributes, 'course' );
				$block_attributes['team_id']  = $this->block_attributes_get_post_id( $block_attributes, 'team' );
				$block_attributes['quiz_id']   = $this->block_attributes_get_post_id( $block_attributes, 'quiz' );
				$block_attributes['user_id']   = $this->block_attributes_get_user_id( $block_attributes );

				if ( ( empty( $block_attributes['course_id'] ) ) && ( empty( $block_attributes['team_id'] ) ) && ( empty( $block_attributes['quiz_id'] ) ) ) {
					$edit_post_type = $this->block_attributes_get_editing_post_type( $block_attributes );
					$edit_post_id   = $this->block_attributes_get_editing_post_id( $block_attributes );

					if ( ebox_get_post_type_slug( 'team' ) === $edit_post_type ) {
						if ( ! empty( $edit_post_id ) ) {
							$block_attributes['team_id'] = $edit_post_id;
						}
					}

					if ( ebox_get_post_type_slug( 'quiz' ) === $edit_post_type ) {
						if ( ! empty( $edit_post_id ) ) {
							$block_attributes['quiz_id'] = $edit_post_id;
						}
					}

					if ( in_array( $edit_post_type, ebox_get_post_types( 'course' ), true ) ) {
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

				if ( ( empty( $block_attributes['course_id'] ) ) && ( empty( $block_attributes['team_id'] ) ) && ( empty( $block_attributes['quiz_id'] ) ) ) {
					return $this->render_block_wrap(
						'<span class="ebox-block-error-message">' . sprintf(
						// translators: placeholder: Course, Team, Quiz.
							_x( '%1$s ID, %2$s ID, or %3$s ID is required.', 'placeholder: Course, Team, Quiz', 'ebox' ),
							ebox_Custom_Label::get_label( 'course' ),
							ebox_Custom_Label::get_label( 'team' ),
							ebox_Custom_Label::get_label( 'quiz' )
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

				if ( ! empty( $block_attributes['quiz_id'] ) ) {
					$quiz_post = get_post( $block_attributes['quiz_id'] );
					if ( ( ! is_a( $quiz_post, 'WP_Post' ) ) || ( ebox_get_post_type_slug( 'quiz' ) !== $quiz_post->post_type ) ) {
						return $this->render_block_wrap(
							'<span class="ebox-block-error-message">' . sprintf(
								// translators: placeholder: Quiz.
								_x( 'Invalid %1$s ID.', 'placeholder: Quiz', 'ebox' ),
								ebox_Custom_Label::get_label( 'quiz' )
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
				}
			}

			return $shortcode_out;
		}

		/**
		 * Called from the LD function ebox_convert_block_markers_shortcode() when parsing the block content.
		 *
		 * @since 3.1.4
		 *
		 * @param array  $block_attributes The array of attributes parse from the block content.
		 * @param string $shortcode_slug   This will match the related LD shortcode ld_profile, ld_course_list, etc.
		 * @param string $block_slug       This is the block token being processed. Normally same as the shortcode but underscore replaced with dash.
		 * @param string $content          This is the original full content being parsed.
		 *
		 * @return array $block_attributes.
		 */
		public function ebox_block_markers_shortcode_atts_filter( $block_attributes = array(), $shortcode_slug = '', $block_slug = '', $content = '' ) {
			if ( $shortcode_slug === $this->shortcode_slug ) {
				if ( isset( $block_attributes['class_html'] ) ) {
					$block_attributes['class'] = esc_attr( $block_attributes['class_html'] );
					unset( $block_attributes['class_html'] );
				}
			}
			return $block_attributes;
		}

		// End of functions.
	}
}
new ebox_Gutenberg_Block_Certificate();
