<?php
/**
 * Handles all server side logic for the ld-payment-buttons Gutenberg Block. This block is functionally the same
 * as the ebox_payment_buttons shortcode used within ebox.
 *
 * @package ebox
 * @since 2.5.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Gutenberg_Block' ) ) && ( ! class_exists( 'ebox_Gutenberg_Payment_Buttons' ) ) ) {
	/**
	 * Class for handling ebox Payment Buttons Block
	 */
	class ebox_Gutenberg_Payment_Buttons extends ebox_Gutenberg_Block {

		/**
		 * Object constructor
		 */
		public function __construct() {

			$this->shortcode_slug   = 'ebox_payment_buttons';
			$this->block_slug       = 'ld-payment-buttons';
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
		 * @param array         $block_attributes The block attributes.
		 * @param string        $block_content    The block content.
		 * @param WP_Block|null $block            The block object.
		 *
		 * @return string
		 */
		public function render_block( $block_attributes = array(), $block_content = '', WP_Block $block = null ) {
			$course_post = null;

			$block_attributes = $this->preprocess_block_attributes( $block_attributes );

			// Only the 'editing_post_meta' element will be sent from within the post edit screen.
			if ( $this->block_attributes_is_editing_post( $block_attributes ) ) {
				$block_attributes['course_id'] = $this->block_attributes_get_post_id( $block_attributes, 'course' );
				$block_attributes['team_id']  = $this->block_attributes_get_post_id( $block_attributes, 'team' );

				if ( ( empty( $block_attributes['course_id'] ) ) && ( empty( $block_attributes['team_id'] ) ) ) {
					$edit_post_type = $this->block_attributes_get_editing_post_type( $block_attributes );
					$edit_post_id   = $this->block_attributes_get_editing_post_id( $block_attributes );

					if ( ebox_get_post_type_slug( 'team' ) === $edit_post_type ) {
						if ( ! empty( $edit_post_id ) ) {
							$block_attributes['team_id'] = $edit_post_id;
						}
					}

					if ( ebox_get_post_type_slug( 'course' ) === $edit_post_type ) {
						if ( ! empty( $edit_post_id ) ) {
							$block_attributes['course_id'] = $edit_post_id;
						}
					}
				}

				if ( ( empty( $block_attributes['course_id'] ) ) && ( empty( $block_attributes['team_id'] ) ) ) {
					return $this->render_block_wrap(
						'<span class="ebox-block-error-message">' . sprintf(
						// translators: placeholder: Course, Course.
							_x( '%1$s ID is required when not used within a %2$s.', 'placeholder: Course, Course', 'ebox' ),
							ebox_Custom_Label::get_label( 'course' ),
							ebox_Custom_Label::get_label( 'course' )
						) . '</span>'
					);
				}

				if ( ! empty( $block_attributes['course_id'] ) ) {
					$course_post = get_post( (int) $block_attributes['course_id'] );
					if ( ( ! is_a( $course_post, 'WP_Post' ) ) || ( 'ebox-courses' !== $course_post->post_type ) ) {
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

			if ( ( empty( $atts['course_id'] ) ) && ( empty( $atts['course_id'] ) ) ) {
				$viewed_post_id = (int) get_the_ID();
				if ( ! empty( $viewed_post_id ) ) {
					if ( in_array( get_post_type( $viewed_post_id ), ebox_get_post_types( 'course' ), true ) ) {
						$block_attributes['course_id'] = ebox_get_course_id( $viewed_post_id );
					} elseif ( get_post_type( $viewed_post_id ) === ebox_get_post_type_slug( 'team' ) ) {
						$block_attributes['team_id'] = $viewed_post_id;
					}
				}
			}

			$shortcode_out = '';

			if ( ! empty( $block_attributes['course_id'] ) ) {
				$course_price_type = ebox_get_setting( $course_post, 'course_price_type' );
				if ( empty( $course_price_type ) ) {
					$course_price_type = ebox_DEFAULT_COURSE_PRICE_TYPE;
				}

				if ( ! in_array( $course_price_type, array( 'free', 'paynow', 'subscribe' ), true ) ) {
					if ( $this->block_attributes_is_editing_post( $block_attributes ) ) {
						return $this->render_block_wrap(
							'<span class="ebox-block-error-message">' . sprintf(
							// translators: placeholder: Course.
								esc_html_x( '%s Price Type must be Free, PayNow or Subscribe.', 'placeholder: Course', 'ebox' ),
								ebox_Custom_Label::get_label( 'course' )
							) . '</span>'
						);
					}
				}

				$shortcode_str = $this->build_block_shortcode( $block_attributes, $block_content );
				if ( ! empty( $shortcode_str ) ) {
					$shortcode_out = do_shortcode( $shortcode_str );

					// In case the button shortcode does not render and if we are editing we show a default button for the output.
					if ( ( empty( $shortcode_out ) ) && ( $this->block_attributes_is_editing_post( $block_attributes ) ) ) {
						$button_text = ebox_Custom_Label::get_label( 'button_take_this_course' );
						if ( ! empty( $button_text ) ) {
							$shortcode_out = '<a class="btn-join" href="#" id="btn-join">' . $button_text . '</a>';
							if ( ! empty( $shortcode_out ) ) {
								$shortcode_out = $this->render_block_wrap( $shortcode_out );
							}
						}
					}
				}

				return $shortcode_out;
			} elseif ( ! empty( $block_attributes['team_id'] ) ) {
				$team_price_type = ebox_get_setting( $block_attributes['team_id'], 'team_price_type' );
				if ( empty( $team_price_type ) ) {
					$team_price_type = ebox_DEFAULT_GROUP_PRICE_TYPE;
				}

				if ( ! in_array( $team_price_type, array( 'free', 'paynow', 'subscribe' ), true ) ) {
					if ( $this->block_attributes_is_editing_post( $block_attributes ) ) {
						return $this->render_block_wrap(
							'<span class="ebox-block-error-message">' . sprintf(
							// translators: placeholder: Team.
								esc_html_x( '%s Price Type must be Free, PayNow or Subscribe.', 'placeholder: Team', 'ebox' ),
								ebox_Custom_Label::get_label( 'team' )
							) . '</span>'
						);
					}
				}

				$shortcode_str = $this->build_block_shortcode( $block_attributes, $block_content );
				if ( ! empty( $shortcode_str ) ) {
					$shortcode_out = do_shortcode( $shortcode_str );

					// In case the button shortcode does not render and if we are editing we show a default button for the output.
					if ( ( empty( $shortcode_out ) ) && ( $this->block_attributes_is_editing_post( $block_attributes ) ) ) {
						$button_text = ebox_Custom_Label::get_label( 'button_take_this_team' );
						if ( ! empty( $button_text ) ) {
							$shortcode_out = '<a class="btn-join" href="#" id="btn-join">' . $button_text . '</a>';
							if ( ! empty( $shortcode_out ) ) {
								$shortcode_out = $this->render_block_wrap( $shortcode_out );
							}
						}
					}
				}

				return $shortcode_out;
			}

			return '';
		}

		// End of functions.
	}
}
new ebox_Gutenberg_Payment_Buttons();
