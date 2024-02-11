<?php
/**
 * ebox REST API V2 Teams Post Controller.
 *
 * This Controller class is used to GET/UPDATE/DELETE the ebox
 * custom post type Teams (teams).
 *
 * This class extends the LD_REST_Posts_Controller_V2 class.
 *
 * @since 3.3.0
 * @package ebox\REST\V2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( ! class_exists( 'LD_REST_Teams_Controller_V2' ) ) && ( class_exists( 'LD_REST_Posts_Controller_V2' ) ) ) {

	/**
	 * Class ebox REST API V2 Teams Post Controller.
	 *
	 * @since 3.3.0
	 * @uses LD_REST_Posts_Controller_V2
	 */
	class LD_REST_Teams_Controller_V2 extends LD_REST_Posts_Controller_V2 /* phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound */ {

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
			$this->post_type = $post_type;
			$this->metaboxes = array();

			parent::__construct( $this->post_type );

			/**
			 * Set the rest_base after the parent __constructor
			 * as it will set these var with WP specific details.
			 */
			$this->rest_base = $this->get_rest_base( 'teams' );
		}

		/**
		 * Registers the routes for the objects of the controller.
		 *
		 * @since 3.3.0
		 *
		 * @see register_rest_route() in WordPress core.
		 */
		public function register_routes() {
			// Register all the default routes first.
			parent::register_routes();

			include ebox_REST_API_DIR . '/' . $this->version . '/class-ld-rest-teams-courses-controller.php';
			$this->sub_controllers['LD_REST_Teams_Courses_Controller_V2'] = new LD_REST_Teams_Courses_Controller_V2();
			$this->sub_controllers['LD_REST_Teams_Courses_Controller_V2']->register_routes();

			include ebox_REST_API_DIR . '/' . $this->version . '/class-ld-rest-teams-leaders-controller.php';
			$this->sub_controllers['LD_REST_Teams_Leaders_Controller_V2'] = new LD_REST_Teams_Leaders_Controller_V2();
			$this->sub_controllers['LD_REST_Teams_Leaders_Controller_V2']->register_routes();

			include ebox_REST_API_DIR . '/' . $this->version . '/class-ld-rest-teams-users-controller.php';
			$this->sub_controllers['LD_REST_Teams_Users_Controller_V2'] = new LD_REST_Teams_Users_Controller_V2();
			$this->sub_controllers['LD_REST_Teams_Users_Controller_V2']->register_routes();
		}

		/**
		 * Prepare the ebox Post Type Settings.
		 *
		 * @since 3.3.0
		 */
		protected function register_fields() {
			$this->register_fields_metabox();

			do_action( 'ebox_rest_register_fields', $this->post_type, $this );
		}

		/**
		 * Register the Settings Fields from the Post Metaboxes.
		 *
		 * @since 3.3.0
		 */
		protected function register_fields_metabox() {
			require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/settings-metaboxes/class-ld-settings-metabox-team-display-content.php';
			$this->metaboxes['ebox_Settings_Metabox_Team_Display_Content'] = ebox_Settings_Metabox_Team_Display_Content::add_metabox_instance();

			require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/settings-metaboxes/class-ld-settings-metabox-team-access-settings.php';
			$this->metaboxes['ebox_Settings_Metabox_Team_Access_Settings'] = ebox_Settings_Metabox_Team_Access_Settings::add_metabox_instance();

			if ( ! empty( $this->metaboxes ) ) {
				foreach ( $this->metaboxes as $metabox ) {
					$metabox->load_settings_values();
					$metabox->load_settings_fields();
					$this->register_rest_fields( $metabox->get_settings_metabox_fields(), $metabox );
				}
			}
		}

		/**
		 * Permissions check for getting teams.
		 *
		 * @since 3.3.0
		 *
		 * @param WP_REST_Request $request Full details about the request.
		 *
		 * @return true|WP_Error True if the request has read access, otherwise WP_Error object.
		 */
		public function get_items_permissions_check( $request ) {
			if ( ( 'yes' === ebox_Settings_Section::get_section_setting( 'ebox_Settings_Teams_CPT', 'public' ) ) || ( ebox_is_admin_user() ) || ( ebox_is_team_leader_user() ) ) {
				return true;
			} else {
				return new WP_Error( 'ld_rest_cannot_view', esc_html__( 'Sorry, you are not allowed to view this item.', 'ebox' ), array( 'status' => rest_authorization_required_code() ) );
			}
		}

		/**
		 * Checks if a given request has access to read a post.
		 *
		 * @since 3.3.0
		 *
		 * @param WP_REST_Request $request Full details about the request.
		 *
		 * @return bool|WP_Error True if the request has read access for the item, WP_Error object otherwise.
		 */
		public function get_item_permissions_check( $request ) {
			if ( ( 'yes' === ebox_Settings_Section::get_section_setting( 'ebox_Settings_Teams_CPT', 'public' ) ) || ( ebox_is_admin_user() ) || ( ebox_is_team_leader_user() ) ) {
				return true;
			} else {
				return new WP_Error( 'ld_rest_cannot_view', esc_html__( 'Sorry, you are not allowed to view this item.', 'ebox' ), array( 'status' => rest_authorization_required_code() ) );
			}
		}

		/**
		 * Filters Teams request query arguments.
		 *
		 * @since 3.3.0
		 *
		 * @param array           $query_args    Key value array of query var to query value.
		 * @param WP_REST_Request $request The request used.
		 *
		 * @return array Key value array of query var to query value.
		 */
		public function rest_query_filter( $query_args, $request ) {
			if ( ! $this->is_rest_request( $request ) ) {
				return $query_args;
			}

			if ( ebox_is_team_leader_user() ) {
				$team_ids = ebox_get_administrators_team_ids( get_current_user_id() );
				if ( ! empty( $team_ids ) ) {
					$query_args['post__in'] = $team_ids;
				} else {
					$query_args['post__in'] = array( 0 );
				}
			}

			return $query_args;
		}

		/**
		 * Override the REST response links. This is needed when Course Shared Steps is enabled.
		 *
		 * @since 3.3.0
		 *
		 * @param WP_REST_Response $response WP_REST_Response instance.
		 * @param WP_Post          $post     WP_Post instance.
		 * @param WP_REST_Request  $request  WP_REST_Request instance.
		 */
		public function rest_prepare_response_filter( WP_REST_Response $response, WP_Post $post, WP_REST_Request $request ) {
			if ( $this->post_type === $post->post_type ) {
				$base          = sprintf( '/%s/%s', $this->namespace, $this->rest_base );
				$request_route = $request->get_route();

				if ( ( ! empty( $request_route ) ) && ( strpos( $request_route, $base ) !== false ) ) {
					$links = array();

					$current_links = $response->get_links();

					if ( ! isset( $current_links['users'] ) ) {
						$links['users'] = array(
							'href'       => rest_url( trailingslashit( $base ) . $post->ID ) . '/' . $this->get_rest_base( 'teams-users' ),
							'embeddable' => true,
						);
					}

					if ( ! isset( $current_links['leaders'] ) ) {
						$links['leaders'] = array(
							'href'       => rest_url( trailingslashit( $base ) . $post->ID ) . '/' . $this->get_rest_base( 'teams-leaders' ),
							'embeddable' => true,
						);
					}

					if ( ! isset( $current_links['courses'] ) ) {
						$links['courses'] = array(
							'href'       => rest_url( trailingslashit( $base ) . $post->ID ) . '/' . $this->get_rest_base( 'teams-courses' ),
							'embeddable' => true,
						);
					}

					if ( ! empty( $links ) ) {
						$response->add_links( $links );
					}
				}
			}

			return $response;
		}

		// End of functions.
	}
}
