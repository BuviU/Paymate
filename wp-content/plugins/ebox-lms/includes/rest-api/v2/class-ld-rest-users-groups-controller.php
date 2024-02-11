<?php
/**
 * ebox V2 REST API Users Courses Controller.
 *
 * This Controller class is used to GET/UPDATE/DELETE the association
 * between a User and the enrolled Teams (teams).
 *
 * This class extends the LD_REST_Posts_Controller_V2 class.
 *
 * @since 3.3.0
 * @package ebox\REST\V2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( ! class_exists( 'LD_REST_Users_Teams_Controller_V2' ) ) && ( class_exists( 'LD_REST_Posts_Controller_V2' ) ) ) {

	/**
	 * Class ebox V2 REST API Users Courses Controller.
	 *
	 * @since 3.3.0
	 * @uses LD_REST_Posts_Controller_V2
	 */
	class LD_REST_Users_Teams_Controller_V2 extends LD_REST_Posts_Controller_V2 /* phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound */ {

		/**
		 * Public constructor for class
		 *
		 * @since 3.3.0
		 */
		public function __construct() {
			$this->post_type  = ebox_get_post_type_slug( 'team' );
			$this->taxonomies = array();

			parent::__construct( $this->post_type );

			/**
			 * Set the rest_base after the parent __constructor
			 * as it will set these var with WP specific details.
			 */
			$this->rest_base     = $this->get_rest_base( 'users' );
			$this->rest_sub_base = $this->get_rest_base( 'users-teams' );
		}

		/**
		 * Registers the routes for the objects of the controller.
		 *
		 * @since 3.3.0
		 *
		 * @see register_rest_route()
		 */
		public function register_routes() {

			$collection_params = $this->get_collection_params();
			$schema            = $this->get_item_schema();

			$get_item_args = array(
				'context' => $this->get_context_param( array( 'default' => 'view' ) ),
			);

			register_rest_route(
				$this->namespace,
				'/' . $this->rest_base . '/(?P<id>[\d]+)/' . $this->rest_sub_base,
				array(
					'args'   => array(
						'id' => array(
							'description' => esc_html__( 'User ID', 'ebox' ),
							'required'    => true,
							'type'        => 'integer',
						),
					),
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, 'get_user_teams' ),
						'permission_callback' => array( $this, 'get_user_teams_permissions_check' ),
						'args'                => $this->get_collection_params(),
					),
					array(
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => array( $this, 'update_user_teams' ),
						'permission_callback' => array( $this, 'update_user_teams_permissions_check' ),
						'args'                => array(
							'team_ids' => array(
								// translators: team.
								'description' => sprintf( esc_html_x( '%s IDs to add to User.', 'placeholder: team', 'ebox' ), ebox_get_custom_label( 'team' ) ),
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
						'callback'            => array( $this, 'delete_user_teams' ),
						'permission_callback' => array( $this, 'delete_user_teams_permissions_check' ),
						'args'                => array(
							'team_ids' => array(
								// translators: team.
								'description' => sprintf( esc_html_x( '%s IDs to remove from User.', 'placeholder: team', 'ebox' ), ebox_get_custom_label( 'team' ) ),
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

			$schema['title']  = 'user-teams';
			$schema['parent'] = '';

			return $schema;
		}

		/**
		 * Permissions check for getting user teams.
		 *
		 * @since 3.3.0
		 *
		 * @param WP_REST_Request $request Full details about the request.
		 *
		 * @return true|WP_Error True if the request has read access, otherwise WP_Error object.
		 */
		public function get_user_teams_permissions_check( $request ) {
			if ( ebox_is_admin_user() ) {
				return true;
			} elseif ( get_current_user_id() == $request['id'] ) {
				return true;
			} else {
				return new WP_Error( 'ld_rest_cannot_view', esc_html__( 'Sorry, you are not allowed to view this item.', 'ebox' ), array( 'status' => rest_authorization_required_code() ) );
			}
		}

		/**
		 * Permissions check for updating user teams.
		 *
		 * @since 3.3.0
		 *
		 * @param WP_REST_Request $request Full details about the request.
		 *
		 * @return true|WP_Error True if the request has read access, otherwise WP_Error object.
		 */
		public function update_user_teams_permissions_check( $request ) {
			if ( ebox_is_admin_user() ) {
				return true;
			} elseif ( get_current_user_id() == $request['id'] ) {
				return true;
			} else {
				return new WP_Error( 'ld_rest_cannot_view', esc_html__( 'Sorry, you are not allowed to view this item.', 'ebox' ), array( 'status' => rest_authorization_required_code() ) );
			}
		}

		/**
		 * Permissions check for deleting user teams.
		 *
		 * @since 3.3.0
		 *
		 * @param WP_REST_Request $request Full details about the request.
		 *
		 * @return true|WP_Error True if the request has read access, otherwise WP_Error object.
		 */
		public function delete_user_teams_permissions_check( $request ) {
			if ( ebox_is_admin_user() ) {
				return true;
			} elseif ( get_current_user_id() == $request['id'] ) {
				return true;
			} else {
				return new WP_Error( 'ld_rest_cannot_view', esc_html__( 'Sorry, you are not allowed to view this item.', 'ebox' ), array( 'status' => rest_authorization_required_code() ) );
			}
		}

		/**
		 * Get a user teams.
		 *
		 * @since 3.3.0
		 *
		 * @param WP_REST_Request $request Full details about the request.
		 *
		 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
		 */
		public function get_user_teams( $request ) {
			return $this->get_items( $request );
		}

		/**
		 * Update a user teams.
		 *
		 * @since 3.3.0
		 *
		 * @param WP_REST_Request $request Full details about the request.
		 *
		 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
		 */
		public function update_user_teams( $request ) {
			$user_id = $request['id'];
			if ( empty( $user_id ) ) {
				return new WP_Error(
					'rest_post_invalid_id',
					esc_html__( 'Invalid User ID.', 'ebox' ),
					array(
						'status' => 404,
					)
				);
			}

			$user = get_user_by( 'id', $user_id );
			if ( ( ! $user ) || ( ! is_a( $user, 'WP_User' ) ) ) {
				return new WP_Error(
					'rest_user_invalid_id',
					esc_html__( 'Invalid User ID.', 'ebox' ),
					array(
						'status' => 404,
					)
				);
			}

			$team_ids = $request['team_ids'];
			if ( ( ! is_array( $team_ids ) ) || ( empty( $team_ids ) ) ) {
				return new WP_Error(
					'rest_post_invalid_id',
					sprintf(
						// translators: placeholder: Team.
						esc_html_x(
							'Invalid %s ID.',
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

				$ret = ld_update_team_access( $user_id, $team_id, false );
				if ( true === $ret ) {
					$data_item->team_id = $team_id;
					$data_item->status   = 'success';
					$data_item->code     = 'ebox_rest_enroll_success';
					$data_item->message  = sprintf(
						// translators: placeholder: Team.
						esc_html_x(
							'User enrolled in %s success.',
							'placeholder: Team',
							'ebox'
						),
						ebox_Custom_Label::get_label( 'team' )
					);
				} else {
					$data_item->team_id = $team_id;
					$data_item->status   = 'failed';
					$data_item->code     = 'ebox_rest_enroll_failed';
					$data_item->message  = sprintf(
						// translators: placeholder: Team.
						esc_html_x(
							'User already enrolled in %s.',
							'placeholder: Team',
							'ebox'
						),
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
		 * Delete a user teams.
		 *
		 * @since 3.3.0
		 *
		 * @param WP_REST_Request $request Full details about the request.
		 *
		 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
		 */
		public function delete_user_teams( $request ) {
			$user_id = $request['id'];
			if ( empty( $user_id ) ) {
				return new WP_Error( 'rest_post_invalid_id', esc_html__( 'Invalid User ID.', 'ebox' ) . ' ' . __CLASS__, array( 'status' => 404 ) );
			}

			$team_ids = $request['team_ids'];
			if ( ( ! is_array( $team_ids ) ) || ( empty( $team_ids ) ) ) {
				return new WP_Error(
					'rest_post_invalid_id',
					sprintf(
						// translators: placeholder: Team.
						esc_html_x(
							'Invalid %s ID.',
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

				$ret = ld_update_team_access( $user_id, $team_id, true );
				if ( true === $ret ) {
					$data_item->team_id = $team_id;
					$data_item->status   = 'success';
					$data_item->code     = 'ebox_rest_unenroll_success';
					$data_item->message  = sprintf(
						// translators: placeholder: Team.
						esc_html_x(
							'User unenrolled from %s success.',
							'placeholder: Team',
							'ebox'
						),
						ebox_Custom_Label::get_label( 'team' )
					);
				} else {
					$data_item->team_id = $team_id;
					$data_item->status   = 'failed';
					$data_item->code     = 'ebox_rest_unenroll_failed';
					$data_item->message  = sprintf(
						// translators: placeholder: Team.
						esc_html_x(
							'User not enrolled from %s.',
							'placeholder: Team',
							'ebox'
						),
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
		 * Filter Users Teams query args.
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

				$user_id = $request['id'];
				if ( empty( $user_id ) ) {
					return new WP_Error( 'rest_user_invalid_id', esc_html__( 'Invalid User ID.', 'ebox' ), array( 'status' => 404 ) );
				}

				if ( is_user_logged_in() ) {
					$current_user_id = get_current_user_id();
				} else {
					$current_user_id = 0;
				}

				$query_args['post__in'] = array( 0 );
				if ( ! empty( $current_user_id ) ) {
					$team_ids = ebox_get_users_team_ids( $user_id );
					if ( ! empty( $team_ids ) ) {
						$query_args['post__in'] = $team_ids;
					}
				}
			}

			return $query_args;
		}
	}
}
