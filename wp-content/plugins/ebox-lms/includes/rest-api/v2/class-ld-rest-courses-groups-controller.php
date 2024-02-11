<?php
/**
 * ebox REST API V2 Courses Teams Controller.
 *
 * This Controller class is used to GET/UPDATE/DELETE the association
 * between the ebox Courses (ebox-courses) and Teams (teams)
 * custom post types.
 *
 * This class extends the LD_REST_Posts_Controller_V2 class.
 *
 * @since 3.3.0
 * @package ebox\REST\V2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( ! class_exists( 'LD_REST_Courses_Teams_Controller_V2' ) ) && ( class_exists( 'LD_REST_Posts_Controller_V2' ) ) ) {

	/**
	 * Class ebox REST API V2 Courses Teams Controller.
	 *
	 * @since 3.3.0
	 * @uses LD_REST_Posts_Controller_V2
	 */
	class LD_REST_Courses_Teams_Controller_V2 extends LD_REST_Posts_Controller_V2 /* phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound */ {

		/**
		 * Public constructor for class
		 *
		 * @since 3.3.0
		 *
		 * @param string $post_type Post type.
		 */
		public function __construct( $post_type = '' ) {
			if ( empty( $post_type ) ) {
				$post_type = ebox_get_post_type_slug( 'team' );
			}

			$this->post_type  = $post_type;
			$this->taxonomies = array();

			parent::__construct( $this->post_type );

			/**
			 * Set the rest_base after the parent __constructor
			 * as it will set these var with WP specific details.
			 */
			$this->rest_base     = $this->get_rest_base( 'courses' );
			$this->rest_sub_base = $this->get_rest_base( 'courses-teams' );
		}

		/**
		 * Registers the routes for the objects of the controller.
		 *
		 * @since 3.3.0
		 *
		 * @see register_rest_route() in WordPress core.
		 */
		public function register_routes() {
			$this->register_fields();

			$collection_params = $this->get_collection_params();
			$schema            = $this->get_item_schema();

			$get_item_args = array(
				'context' => $this->get_context_param( array( 'default' => 'view' ) ),
			);
			if ( isset( $schema['properties']['password'] ) ) {
				$get_item_args['password'] = array(
					'description' => esc_html__( 'The password for the post if it is password protected.', 'ebox' ),
					'type'        => 'string',
				);
			}

			register_rest_route(
				$this->namespace,
				'/' . $this->rest_base . '/(?P<id>[\d]+)/' . $this->rest_sub_base,
				array(
					'args'   => array(
						'id' => array(
							'description' => sprintf(
								// translators: placeholder: Course.
								esc_html_x(
									'%s ID',
									'placeholder: Course',
									'ebox'
								),
								ebox_Custom_Label::get_label( 'course' )
							),
							'required'    => true,
							'type'        => 'integer',
						),
					),
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, 'get_courses_teams' ),
						'permission_callback' => array( $this, 'get_courses_teams_permissions_check' ),
						'args'                => $this->get_collection_params(),
					),
					array(
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => array( $this, 'update_courses_teams' ),
						'permission_callback' => array( $this, 'update_courses_teams_permissions_check' ),
						'args'                => array(
							'team_ids' => array(
								'description' => sprintf(
									// translators: placeholder: Team, Course.
									esc_html_x(
										'%1$s IDs to enroll into %2$s.',
										'placeholder: Team, Course',
										'ebox'
									),
									ebox_get_custom_label( 'team' ),
									ebox_get_custom_label( 'course' )
								),
								'required'    => true,
								'type'        => 'array',
								'items'       => array(
									'type' => 'integer',
								),
							),
						),
					),
					array(
						'methods'             => WP_REST_Server::DELETABLE,
						'callback'            => array( $this, 'delete_courses_teams' ),
						'permission_callback' => array( $this, 'delete_courses_teams_permissions_check' ),
						'args'                => array(
							'team_ids' => array(
								'description' => sprintf(
									// translators: placeholder: Team, Course.
									esc_html_x(
										'%1$s IDs to remove from %2$s.',
										'placeholder: Team, Course',
										'ebox'
									),
									ebox_get_custom_label( 'team' ),
									ebox_get_custom_label( 'course' )
								),
								'required'    => true,
								'type'        => 'array',
								'items'       => array(
									'type' => 'integer',
								),
							),
						),
					),
					'schema' => array( $this, 'get_public_item_schema' ),
				)
			);
		}

		/**
		 * Gets public schema.
		 *
		 * @since 3.3.0
		 *
		 * @return array
		 */
		public function get_public_item_schema() {

			$schema = parent::get_public_item_schema();

			$schema['title']  = 'course-teams';
			$schema['parent'] = 'course';

			return $schema;
		}

		/**
		 * Filter Course Teams query args.
		 *
		 * @since 3.3.0
		 *
		 * @param array           $query_args Key value array of query var to query value.
		 * @param WP_REST_Request $request    The request used.
		 *
		 * @return array Key value array of query var to query value.
		 */
		public function rest_query_filter( $query_args, $request ) {
			if ( ! $this->is_rest_request( $request ) ) {
				return $query_args;
			}

			$query_args = parent::rest_query_filter( $query_args, $request );

			$route_url    = $request->get_route();
			$ld_route_url = '/' . $this->namespace . '/' . $this->rest_base . '/' . absint( $request['id'] ) . '/' . $this->rest_sub_base;
			if ( ( ! empty( $route_url ) ) && ( $ld_route_url === $route_url ) ) {

				$course_id = (int) $request['id'];
				if ( ! empty( $course_id ) ) {
					$query_args['post_type'] = ebox_get_post_type_slug( 'team' );

					$course_has_teams = false;

					$this->course_post = get_post( $course_id );
					if ( ( $this->course_post ) && ( is_a( $this->course_post, 'WP_Post' ) ) && ( ebox_get_post_type_slug( 'course' ) === $this->course_post->post_type ) ) {
						$course_teams = ebox_get_course_teams( $this->course_post->ID, true );
						if ( ! empty( $course_teams ) ) {
							$course_has_teams      = true;
							$query_args['post__in'] = $query_args['post__in'] ? array_intersect( $course_teams, $query_args['post__in'] ) : $course_teams;
						}
					}

					if ( true !== $course_has_teams ) {
						$query_args['post__in'] = array( 0 );
					}
				}
			}

			return $query_args;
		}

		/**
		 * Override the REST response links.
		 *
		 * When WP renders the post response the 'self' and 'collection' links will have
		 * have a path containing the course slug '/wp-json/ldlms/v2/ebox-courses/XXX'
		 * even though the post type is a team. So this function corrects those links
		 * to correctly point to the team post.
		 *
		 * @since 3.3.0
		 *
		 * @param WP_REST_Response $response WP_REST_Response instance.
		 * @param WP_Post          $post     WP_Post instance.
		 * @param WP_REST_Request  $request  WP_REST_Request instance.
		 */
		public function rest_prepare_response_filter( WP_REST_Response $response, WP_Post $post, WP_REST_Request $request ) {
			$course_id = (int) $request['id'];
			if ( ! empty( $course_id ) ) {
				// Need to compare the requested route to this controller route.
				$route_url    = $request->get_route();
				$ld_route_url = '/' . $this->namespace . '/' . $this->rest_base . '/' . $course_id . '/' . $this->get_rest_base( 'teams' );
				if ( ( ! empty( $route_url ) ) && ( $ld_route_url === $route_url ) && ( $post->post_type === $this->post_type ) ) {
					$current_links = $response->get_links();

					if ( ! empty( $current_links ) ) {
						foreach ( $current_links as $rel => $links ) {
							if ( in_array( $rel, array( 'self', 'collection' ), true ) ) {
								$links_changed = false;
								foreach ( $links as $lidx => $link ) {
									if ( ( isset( $link['href'] ) ) && ( ! empty( $link['href'] ) ) ) {
										$link_href = str_replace(
											'/' . $this->namespace . '/' . $this->rest_base,
											'/' . $this->namespace . '/' . $this->get_rest_base( 'teams' ),
											$link['href']
										);
										if ( $link['href'] !== $link_href ) {
											$links[ $lidx ]['href'] = $link_href;
											$links_changed          = true;
										}
									}
								}

								if ( true === $links_changed ) {
									$response->remove_link( $rel );
									$response->add_links( array( $rel => $links ) );
								}
							}
						}
					}
				}
			}

			return $response;
		}

		/**
		 * Permissions check for getting course teams.
		 *
		 * @since 3.3.0
		 *
		 * @param WP_REST_Request $request Full details about the request.
		 *
		 * @return true|WP_Error True if the request has read access, otherwise WP_Error object.
		 */
		public function get_courses_teams_permissions_check( $request ) {
			if ( ebox_is_admin_user() ) {
				return true;
			} else {
				return new WP_Error( 'ld_rest_cannot_view', esc_html__( 'Sorry, you are not allowed to view this item.', 'ebox' ), array( 'status' => rest_authorization_required_code() ) );
			}
		}

		/**
		 * Checks if a given request has access to update a course teams.
		 *
		 * @since 3.3.0
		 *
		 * @param WP_REST_Request $request Full details about the request.
		 *
		 * @return bool|WP_Error True if the request has access to update the item, WP_Error object otherwise.
		 */
		public function update_courses_teams_permissions_check( $request ) {
			if ( ebox_is_admin_user() ) {
				return true;
			} else {
				return new WP_Error( 'ld_rest_cannot_view', esc_html__( 'Sorry, you are not allowed to view this item.', 'ebox' ), array( 'status' => rest_authorization_required_code() ) );
			}
		}

		/**
		 * Checks if a given request has access to delete a course teams.
		 *
		 * @since 3.3.0
		 *
		 * @param WP_REST_Request $request Full details about the request.
		 *
		 * @return bool|WP_Error True if the request has access to delete the item, WP_Error object otherwise.
		 */
		public function delete_courses_teams_permissions_check( $request ) {
			if ( ebox_is_admin_user() ) {
				return true;
			} else {
				return new WP_Error( 'ld_rest_cannot_view', esc_html__( 'Sorry, you are not allowed to view this item.', 'ebox' ), array( 'status' => rest_authorization_required_code() ) );
			}
		}

		/**
		 * Updates a course teams.
		 *
		 * @since 3.3.0
		 *
		 * @param WP_REST_Request $request Full details about the request.
		 *
		 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
		 */
		public function update_courses_teams( $request ) {
			$course_id = $request['id'];
			if ( empty( $course_id ) ) {
				return new WP_Error(
					'rest_post_invalid_id',
					sprintf(
						// translators: placeholder: Course.
						esc_html_x(
							'Invalid %s ID.',
							'placeholder: Course',
							'ebox'
						),
						ebox_Custom_Label::get_label( 'course' )
					),
					array( 'status' => 404 )
				);
			}

			$course_post = get_post( $course_id );
			if ( ( ! $course_post ) || ( ! is_a( $course_post, 'WP_Post' ) ) || ( ebox_get_post_type_slug( 'course' ) !== $course_post->post_type ) ) {
				return new WP_Error(
					'rest_post_invalid_id',
					sprintf(
						// translators: placeholder: Course.
						esc_html_x(
							'Invalid %s ID.',
							'placeholder: Course',
							'ebox'
						),
						ebox_Custom_Label::get_label( 'course' )
					),
					array( 'status' => 404 )
				);
			}

			$team_ids = $request['team_ids'];
			if ( ( ! is_array( $team_ids ) ) || ( empty( $team_ids ) ) ) {
				return new WP_Error(
					'rest_post_invalid_id',
					sprintf(
						// translators: placeholder: Team.
						esc_html_x(
							'Missing %s IDs.',
							'placeholder: Team',
							'ebox'
						),
						ebox_Custom_Label::get_label( 'team' )
					),
					array(
						'status' => 404,
					)
				);
			}
			$team_ids = array_map( 'absint', $team_ids );

			$data = array();

			foreach ( $team_ids as $team_id ) {
				if ( empty( $team_id ) ) {
					continue;
				}

				$data_item = new stdClass();

				$team_post = get_post( $team_id );
				if ( ( ! $team_post ) || ( ! is_a( $team_post, 'WP_Post' ) ) || ( ebox_get_post_type_slug( 'team' ) !== $team_post->post_type ) ) {
					$data_item->team_id = $team_id;
					$data_item->status   = 'failed';
					$data_item->code     = 'ebox_rest_invalid_id';
					$data_item->message  = sprintf(
						// translators: placeholder: Team.
						esc_html_x(
							'Invalid %s ID.',
							'placeholder: Team',
							'ebox'
						),
						ebox_Custom_Label::get_label( 'team' )
					);
					$data[] = $data_item;

					continue;
				}

				$ret = ld_update_course_team_access( $course_id, $team_id, false );
				if ( true === $ret ) {
					$data_item->team_id = $team_id;
					$data_item->status   = 'success';
					$data_item->code     = 'ebox_rest_enroll_success';
					$data_item->message  = sprintf(
						// translators: placeholder: Course, Team.
						esc_html_x(
							'%1$s enrolled in %2$s success.',
							'placeholder: Course, Team',
							'ebox'
						),
						ebox_Custom_Label::get_label( 'course' ),
						ebox_Custom_Label::get_label( 'team' )
					);
				} else {
					$data_item->team_id = $team_id;
					$data_item->status   = 'failed';
					$data_item->code     = 'ebox_rest_enroll_failed';
					$data_item->message  = sprintf(
						// translators: placeholder: Course, Team.
						esc_html_x(
							'%1$s already enrolled in %2$s.',
							'placeholder: Course, Team',
							'ebox'
						),
						ebox_Custom_Label::get_label( 'course' ),
						ebox_Custom_Label::get_label( 'team' )
					);
				}
				$data[] = $data_item;
			}

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;

		}

		/**
		 * Delete course teams.
		 *
		 * @since 3.3.0
		 *
		 * @param WP_REST_Request $request Full details about the request.
		 *
		 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
		 */
		public function delete_courses_teams( $request ) {
			$course_id = $request['id'];
			if ( empty( $course_id ) ) {
				return new WP_Error(
					'rest_post_invalid_id',
					sprintf(
						// translators: placeholder: Course.
						esc_html_x(
							'Invalid %s ID.',
							'placeholder: Course',
							'ebox'
						),
						ebox_Custom_Label::get_label( 'course' )
					),
					array( 'status' => 404 )
				);
			}

			$course_post = get_post( $course_id );
			if ( ( ! $course_post ) || ( ! is_a( $course_post, 'WP_Post' ) ) || ( ebox_get_post_type_slug( 'course' ) !== $course_post->post_type ) ) {
				return new WP_Error(
					'rest_post_invalid_id',
					sprintf(
						// translators: placeholder: Course.
						esc_html_x(
							'Invalid %s ID.',
							'placeholder: Course',
							'ebox'
						),
						ebox_Custom_Label::get_label( 'course' )
					),
					array( 'status' => 404 )
				);
			}

			$team_ids = $request['team_ids'];
			if ( ( ! is_array( $team_ids ) ) || ( empty( $team_ids ) ) ) {
				return new WP_Error(
					'rest_post_invalid_id',
					sprintf(
						// translators: placeholder: Team.
						esc_html_x(
							'Missing %s IDs.',
							'placeholder: Team',
							'ebox'
						),
						ebox_Custom_Label::get_label( 'team' )
					),
					array(
						'status' => 404,
					)
				);
			}
			$team_ids = array_map( 'absint', $team_ids );

			$data = array();

			foreach ( $team_ids as $team_id ) {
				if ( empty( $team_id ) ) {
					continue;
				}

				$data_item = new stdClass();

				$team_post = get_post( $team_id );
				if ( ( ! $team_post ) || ( ! is_a( $team_post, 'WP_Post' ) ) || ( ebox_get_post_type_slug( 'team' ) !== $team_post->post_type ) ) {
					$data_item->team_id = $team_id;
					$data_item->status   = 'failed';
					$data_item->code     = 'ebox_rest_invalid_id';
					$data_item->message  = sprintf(
						// translators: placeholder: Team.
						esc_html_x(
							'Invalid %s ID.',
							'placeholder: Team',
							'ebox'
						),
						ebox_Custom_Label::get_label( 'team' )
					);
					$data[] = $data_item;

					continue;
				}

				$ret = ld_update_course_team_access( $course_id, $team_id, true );
				if ( true === $ret ) {
					$data_item->team_id = $team_id;
					$data_item->status   = 'success';
					$data_item->code     = 'ebox_rest_unenroll_success';
					$data_item->message  = sprintf(
						// translators: placeholder: Course, Team.
						esc_html_x(
							'%1$s enrolled from %2$s success.',
							'placeholder: Course, Team',
							'ebox'
						),
						ebox_Custom_Label::get_label( 'course' ),
						ebox_Custom_Label::get_label( 'team' )
					);
				} else {
					$data_item->team_id = $team_id;
					$data_item->status   = 'failed';
					$data_item->code     = 'ebox_rest_unenroll_failed';
					$data_item->message  = sprintf(
						// translators: placeholder: Course, Team.
						esc_html_x(
							'%1$s not unenrolled from %2$s.',
							'placeholder: Course, Team',
							'ebox'
						),
						ebox_Custom_Label::get_label( 'course' ),
						ebox_Custom_Label::get_label( 'team' )
					);
				}
				$data[] = $data_item;
			}

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Retrieves a course users.
		 *
		 * @since 3.3.0
		 *
		 * @param WP_REST_Request $request Full details about the request.
		 *
		 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
		 */
		public function get_courses_teams( $request ) {
			return parent::get_items( $request );
		}

		// End of functions.
	}
}
