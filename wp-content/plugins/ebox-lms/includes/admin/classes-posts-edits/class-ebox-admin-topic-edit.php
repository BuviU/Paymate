<?php
/**
 * ebox Admin Topic Edit.
 *
 * @since 2.6.0
 * @package ebox\Topic\Edit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Admin_Post_Edit' ) ) && ( ! class_exists( 'ebox_Admin_Topic_Edit' ) ) ) {

	/**
	 * Class ebox Admin Topic Edit.
	 *
	 * @since 2.6.0
	 * @uses ebox_Admin_Post_Edit
	 */
	class ebox_Admin_Topic_Edit extends ebox_Admin_Post_Edit {

		/**
		 * Public constructor for class.
		 *
		 * @since 2.6.0
		 */
		public function __construct() {
			$this->post_type = ebox_get_post_type_slug( 'topic' );

			parent::__construct();
		}

		/**
		 * On Load handler function for this post type edit.
		 * This function is called by a WP action when the admin
		 * page 'post.php' or 'post-new.php' are loaded.
		 *
		 * @since 3.0.0
		 */
		public function on_load() {
			if ( $this->post_type_check() ) {
				require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/settings-metaboxes/class-ld-settings-metabox-topic-display-content.php';
				require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/settings-metaboxes/class-ld-settings-metabox-topic-access-settings.php';

				parent::on_load();
			}
		}

		/**
		 * Save metabox handler function.
		 *
		 * @since 3.0.0
		 *
		 * @param integer $post_id Post ID Question being edited.
		 * @param object  $post WP_Post Question being edited.
		 * @param boolean $update If update true, else false.
		 */
		public function save_post( $post_id = 0, $post = null, $update = false ) {
			if ( ! $this->post_type_check( $post ) ) {
				return false;
			}

			if ( ! parent::save_post( $post_id, $post, $update ) ) {
				return false;
			}

			if ( ( isset( $_POST['ld-course-primary-set-nonce'] ) ) && ( ! empty( $_POST['ld-course-primary-set-nonce'] ) ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ld-course-primary-set-nonce'] ) ), 'ld-course-primary-set-nonce' ) ) {
				if ( isset( $_POST['ld-course-primary-set'] ) ) {
					$course_primary = absint( $_POST['ld-course-primary-set'] );
					if ( ! empty( $course_primary ) ) {
						ebox_set_primary_course_for_step( $post_id, $course_primary );
					}
				}
			}

			if ( ! empty( $this->_metaboxes ) ) {
				foreach ( $this->_metaboxes as $_metaboxes_instance ) {
					$settings_fields = array();
					$settings_fields = $_metaboxes_instance->get_post_settings_field_updates( $post_id, $post, $update );
					$_metaboxes_instance->save_post_meta_box( $post_id, $post, $update, $settings_fields );
				}
			}
		}
		// End of functions.
	}
}
new ebox_Admin_Topic_Edit();
