<?php
/**
 * ebox REST API V2 Teams Leader Users Controller.
 *
 * This Controller class is used to GET/UPDATE/DELETE the association
 * between a Course (ebox-courses) and Team Admin (Team Leader).
 *
 * This class extends the LD_REST_Users_Controller_V2 class.
 *
 * @since 3.3.0
 * @package ebox\REST\V2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( ! class_exists( 'LD_REST_Teams_Leaders_Controller_V2' ) ) && ( class_exists( 'LD_REST_Users_Controller_V2' ) ) ) {

	/**
	 * Class ebox REST API V2 Teams Leader Users Controller.
	 *
	 * @since 3.3.0
	 * @uses LD_REST_Users_Controller_V2
	 */
	class LD_REST_Teams_Leaders_Controller_V2 extends LD_REST_Users_Controller_V2 /* phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound */ {

		/**
		 * Public constructor for class
		 *
		 * @since 3.3.0
		 */
		public function __construct() {

			parent::__construct();

			/**
			 * Set the rest_base after the parent __constructor
			 * as it will set these var with WP specific details.
			 */
			$this->rest_base     = $this->get_rest_base( 'teams' );
			$this->rest_sub_base = $this->get_rest_base( 'teams-leaders' );
		}

		/**
		 * Registers the routes for the objects of the controller.
		 *
		 * @since 3.3.0
		 *
		 * @see register_rest_route()
		 */
		public function register_routes() {
			$schema = $this->get_item_schema();

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
							// translators: placeholder: Team.
							'description' => sprintf( esc_html_x( '%s ID', 'placeholder: Team.', 'ebox' ), ebox_get_custom_label( 'team' ) ),
							'required'    => true,
							'type'        => 'integer',
						),
					),
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, 'get_teams_leaders' ),
						'permission_callback' => array( $this, 'get_teams_leaders_permissions_check' ),
						'args'                => $this->get_collection_params(),
					),
					array(
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => array( $this, 'update_teams_leaders' ),
						'permission_callback' => array( $this, 'update_teams_leaders_permissions_check' ),
						'args'                => array(
							'user_ids' => array(
								'description' => sprintf(
									// translators: placeholders: Team Leader, Team.
									esc_html_x(
										'%1$s User IDs to enroll into %2$s',
										'placeholders: team leader, team',
										'ebox'
									),
									ebox_get_custom_label( 'team_leader' ),
									ebox_get_custom_label( 'team' )
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
						'callback'            => array( $this, 'delete_teams_leaders' ),
						'permission_callback' => array( $this, 'delete_teams_leaders_permissions_check' ),
						'args'                => array(
							'user_ids' => array(
								'description' => sprintf(
									// translators: placeholders: Team Leader, Team.
									esc_html_x(
										'%1$s User IDs to remove from %2$s',
										'placeholders: team leader, team',
										'ebox'
									),
									ebox_get_custom_label( 'team_leader' ),
									ebox_get_custom_label( 'team' )
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

			$schema['title']  = 'team-leaders';
			$schema['parent'] = 'teams';

			return $schema;
		}

		/**
		 * Permissions check for getting team leaders.
		 *
		 * @since 3.3.0
		 *
		 * @param WP_REST_Request $request Full details about the request.
		 *
		 * @return true|WP_Error True if the request has read access, otherwise WP_Error object.
		 */
		public function get_teams_leaders_permissions_check( $request ) {
			if ( ebox_is_admin_user() ) {
				return true;
			} else {
				return new WP_Error( 'ld_rest_cannot_view', esc_html__( 'Sorry, you are not allowed to view this item.', 'ebox' ), array( 'status' => rest_authorization_required_code() ) );
			}
		}

		/**
		 * Checks permission to update team leaders.
		 *
		 * @since 3.3.0
		 *
		 * @param WP_REST_Request $request Full details about the request.
		 *
		 * @return true|WP_Error True if the request has access to update the item, WP_Error object otherwise.
		 */
		public function update_teams_leaders_permissions_check( $request ) {
			if ( ebox_is_admin_user() ) {
				return true;
			} else {
				return new WP_Error( 'ld_rest_cannot_view', esc_html__( 'Sorry, you are not allowed to view this item.', 'ebox' ), array( 'status' => rest_authorization_required_code() ) );
			}
		}

		/**
		 * Checks permission to delete team leaders.
		 *
		 * @since 3.3.0
		 *
		 * @param WP_REST_Request $request Full details about the request.
		 *
		 * @return true|WP_Error True if the request has access to update the item, WP_Error object otherwise.
		 */
		public function delete_teams_leaders_permissions_check( $request ) {
			if ( ebox_is_admin_user() ) {
				return true;
			} else {
				return new WP_Error( 'ld_rest_cannot_view', esc_html__( 'Sorry, you are not allowed to view this item.', 'ebox' ), array( 'status' => rest_authorization_required_code() ) );
			}
		}

		/**
		 * Get a team leaders.
		 *
		 * @since 3.3.0
		 *
		 * @param WP_REST_Request $request Full details about the request.
		 *
		 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
		 */
		public function get_teams_leaders( $request ) {
			return parent::get_items( $request );
		}

		/**
		 * Updates a team leaders.
		 *
		 * @since 3.3.0
		 *
		 * @param WP_REST_Request $request Full details about the request.
		 *
		 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
		 */
		public function update_teams_leaders( $request ) {
			$team_id = $request['id'];
			if ( empty( $team_id ) ) {
				return new WP_Error(
					'rest_post_invalid_id',
					sprintf(
						// translators: placeholder: team.
						esc_html_x(
							'Invalid %s ID.',
							'placeholder: team',
							'ebox'
						),
						ebox_Custom_Label::get_label( 'team' )
					),
					array( 'status' => 404 )
				);
			}

			$team_post = get_post( $team_id );
			if ( ( ! $team_post ) || ( ! is_a( $team_post, 'WP_Post' ) ) || ( ebox_get_post_type_slug( 'team' ) !== $team_post->post_type ) ) {
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
					array( 'status' => 404 )
				);
			}

			$user_ids = $request['user_ids'];
			if ( ( ! is_array( $user_ids ) ) || ( empty( $user_ids ) ) ) {
				return new WP_Error(
					'rest_post_invalid_id',
					esc_html__( 'Missing User IDs.', 'ebox' ),
					array(
						'status' => 404,
					)
				);
			}
			$user_ids = array_map( 'absint', $user_ids );

			$data = array();

			foreach ( $user_ids as $user_id ) {
				if ( empty( $user_id ) ) {
					continue;
				}

				$data_item = new stdClass();

				$user = get_user_by( 'id', $user_id );
				if ( ( ! $user ) || ( ! is_a( $user, 'WP_User' ) ) ) {
					$data_item->user_id = $user_id;
					$data_item->status  = 'failed';
					$data_item->code    = 'rest_user_invalid_id';
					$data_item->message = esc_html__( 'Invalid User ID.', 'ebox' );
					$data[]             = $data_item;

					continue;
				}

				$ret = ld_update_leader_team_access( $user_id, $team_id, false );
				if ( true === $ret ) {
					$data_item->user_id = $user_id;
					$data_item->status  = 'success';
					$data_item->code    = 'ebox_rest_enroll_success';
					$data_item->message = sprintf(
						// translators: placeholder: Team.
						esc_html_x(
							'Leader enrolled in %s success.',
							'placeholder: Team',
							'ebox'
						),
						ebox_Custom_Label::get_label( 'team' )
					);
				} else {
					$data_item->user_id = $user_id;
					$data_item->status  = 'failed';
					$data_item->code    = 'ebox_rest_enroll_failed';
					$data_item->message = sprintf(
						// translators: placeholder: Team.
						esc_html_x(
							'Leader already enrolled in %s.',
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
		 * Delete a team leaders.
		 *
		 * @since 3.3.0
		 *
		 * @param WP_REST_Request $request Full details about the request.
		 *
		 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
		 */
		public function delete_teams_leaders( $request ) {
			$team_id = $request['id'];
			if ( empty( $team_id ) ) {
				return new WP_Error(
					'rest_post_invalid_id',
					sprintf(
						// translators: placeholder: team.
						esc_html_x(
							'Invalid %s ID.',
							'placeholder: team',
							'ebox'
						),
						ebox_Custom_Label::get_label( 'team' )
					),
					array( 'status' => 404 )
				);
			}

			$team_post = get_post( $team_id );
			if ( ( ! $team_post ) || ( ! is_a( $team_post, 'WP_Post' ) ) || ( ebox_get_post_type_slug( 'team' ) !== $team_post->post_type ) ) {
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
					array( 'status' => 404 )
				);
			}

			$user_ids = $request['user_ids'];
			if ( ( ! is_array( $user_ids ) ) || ( empty( $user_ids ) ) ) {
				return new WP_Error(
					'rest_post_invalid_id',
					esc_html__( 'Missing User IDs.', 'ebox' ),
					array(
						'status' => 404,
					)
				);
			}
			$user_ids = array_map( 'absint', $user_ids );

			$data = array();

			foreach ( $user_ids as $user_id ) {
				if ( empty( $user_id ) ) {
					continue;
				}

				$data_item = new stdClass();

				$user = get_user_by( 'id', $user_id );
				if ( ( ! $user ) || ( ! is_a( $user, 'WP_User' ) ) ) {
					$data_item->user_id = $user_id;
					$data_item->status  = 'failed';
					$data_item->code    = 'rest_user_invalid_id';
					$data_item->message = esc_html__( 'Invalid User ID.', 'ebox' );
					$data[]             = $data_item;

					continue;
				}

				$ret = ld_update_leader_team_access( $user_id, $team_id, true );
				if ( true === $ret ) {
					$data_item->user_id = $user_id;
					$data_item->status  = 'success';
					$data_item->code    = 'ebox_rest_unenroll_success';
					$data_item->message = sprintf(
						// translators: placeholder: Team.
						esc_html_x(
							'Leader unenroll from %s success.',
							'placeholder: Team',
							'ebox'
						),
						ebox_Custom_Label::get_label( 'team' )
					);
				} else {
					$data_item->user_id = $user_id;
					$data_item->status  = 'failed';
					$data_item->code    = 'ebox_rest_unenroll_failed';
					$data_item->message = sprintf(
						// translators: placeholder: Team.
						esc_html_x(
							'Leader not unenrolled from %s.',
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
		 * Filter Teams Leaders Users query args.
		 *
		 * @since 3.3.0
		 *
		 * @param array           $query_args Key value array of query var to query value.
		 * @param WP_REST_Request $request    The request used.
		 *
		 * @return array          $query_args Key value array of query var to query value.
		 */
		public function rest_query_filter( $query_args, $request ) {
			if ( ! $this->is_rest_request( $request ) ) {
				return $query_args;
			}

			$query_args = parent::rest_query_filter( $query_args, $request );

			$route_url    = $request->get_route();
			$ld_route_url = '/' . $this->namespace . '/' . $this->rest_base . '/' . absint( $request['id'] ) . '/' . $this->rest_sub_base;
			if ( ( ! empty( $route_url ) ) && ( $ld_route_url === $route_url ) ) {
				$team_id = (int) $request['id'];
				if ( empty( $team_id ) ) {
					return new WP_Error(
						'rest_post_invalid_id',
						sprintf(
							// translators: placeholder: team.
							esc_html_x(
								'Invalid %s ID.',
								'placeholder: team',
								'ebox'
							),
							ebox_Custom_Label::get_label( 'team' )
						),
						array( 'status' => 404 )
					);
				}

				if ( is_user_logged_in() ) {
					$current_user_id = get_current_user_id();
				} else {
					$current_user_id = 0;
				}

				$query_args['include'] = array( 0 );
				if ( ! empty( $current_user_id ) ) {
					$team_user_ids = ebox_get_teams_administrator_ids( $team_id );
					if ( ! empty( $team_user_ids ) ) {
						$query_args['include'] = $team_user_ids;
					}
				}
			}

			return $query_args;
		}

		// End of functions.
	}
}
