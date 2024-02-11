<?php
/**
 * ebox REST API V2 Exams Post Controller.
 *
 * This Controller class is used to GET/UPDATE/DELETE the ebox
 * custom post type exams (ld-exam).
 *
 * This class extends the LD_REST_Posts_Controller_V2 class.
 *
 * @since 4.0.0
 * @package ebox\REST\V2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( ! class_exists( 'LD_REST_Exams_Controller_V2' ) ) && ( class_exists( 'LD_REST_Posts_Controller_V2' ) ) ) {

	/**
	 * Class ebox REST API V2 Exams Post Controller.
	 *
	 * @since 4.0.0
	 * @uses LD_REST_Posts_Controller_V2
	 */
	class LD_REST_Exams_Controller_V2 extends LD_REST_Posts_Controller_V2 /* phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound */ {

		/**
		 * Public constructor for class
		 *
		 * @since 4.0.0
		 *
		 * @param string $post_type Post type.
		 */
		public function __construct( $post_type = '' ) {
			if ( empty( $post_type ) ) {
				$post_type = ebox_get_post_type_slug( 'exam' );
			}
			$this->post_type = $post_type;
			$this->metaboxes = array();

			parent::__construct( $this->post_type );

			/**
			 * Set the rest_base after the parent __constructor
			 * as it will set these var with WP specific details.
			 */
			$this->rest_base = $this->get_rest_base( 'exams' );
		}

		/**
		 * Prepare the ebox Post Type Settings.
		 *
		 * @since 4.0.0
		 */
		protected function register_fields() {
			$this->register_fields_metabox();

			do_action( 'ebox_rest_register_fields', $this->post_type, $this );
		}

		/**
		 * Gets public schema.
		 *
		 * @since 4.0.0
		 *
		 * @return array
		 */
		public function get_public_item_schema() {

			$schema = parent::get_public_item_schema();

			$schema['title'] = 'exam';

			return $schema;
		}

		// End of functions.
	}
}
