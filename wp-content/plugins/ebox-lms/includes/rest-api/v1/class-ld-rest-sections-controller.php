<?php
/**
 * ebox REST API V1 Sections Controller.
 *
 * @since 3.0.0
 * @package ebox\REST\V1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( ! class_exists( 'LD_REST_Sections_Controller_V1' ) ) && ( class_exists( 'WP_REST_Controller' ) ) ) {

	/**
	 * Class ebox REST API V1 Sections Controller.
	 *
	 * @since 3.0.0
	 */
	class LD_REST_Sections_Controller_V1 extends WP_REST_Controller /* phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound */ {

		/**
		 * Registers the routes for the objects of the controller.
		 *
		 * @since 3.0.0
		 *
		 * @see register_rest_route() in WordPress core.
		 */
		public function register_routes() {
			$version   = '1';
			$namespace = ebox_REST_API_NAMESPACE . '/v' . $version;
			$base      = 'sections';

			register_rest_route(
				$namespace,
				'/' . $base . '/(?P<id>[\d]+)',
				array(
					array(
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => array( $this, 'update_item' ),
						'permission_callback' => array( $this, 'permissions_check' ),
						'args'                => array(
							'id' => array(
								'required'          => true,
								'validate_callback' => function( $param, $request, $key ) {
									return is_numeric( $param );
								},
								'sanitize_callback' => 'absint',
							),
						),
					),
				)
			);
		}

		/**
		 * Check if a given request has access manage the item.
		 *
		 * @since 3.0.0
		 *
		 * @param WP_REST_Request $request Full data about the request.
		 *
		 * @return WP_Error|bool
		 */
		public function permissions_check( $request ) {
			$params    = $request->get_params();
			$course_id = $params['id'];

			return current_user_can( 'edit_post', $course_id );
		}

		/**
		 * Update sections data.
		 *
		 * @since 3.0.0
		 *
		 * @param WP_REST_Request $request Full data about the request.
		 *
		 * @return WP_Error|WP_REST_Request
		 */
		public function update_item( $request ) {
			$params    = $request->get_params();
			$course_id = $params['id'];
			$sections  = isset( $params['sections'] ) ? wp_slash( $params['sections'] ) : '';

			update_post_meta( $course_id, 'course_sections', $sections );

			return new WP_REST_Response( $this->get_sections_data( $course_id ), 200 );
		}

		/**
		 * Get sections data.
		 *
		 * @since 3.0.0
		 *
		 * @param int $course_id The course ID.
		 *
		 * @return object
		 */
		public function get_sections_data( $course_id ) {
			$sections = get_post_meta( $course_id, 'course_sections', true );

			return $sections;
		}
	}
}
