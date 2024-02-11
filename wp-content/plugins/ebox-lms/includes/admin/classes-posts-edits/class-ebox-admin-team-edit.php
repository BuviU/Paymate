<?php
/**
 * ebox Admin Team Edit.
 *
 * @since 3.2.0
 * @package ebox\Team\Edit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Admin_Post_Edit' ) ) && ( ! class_exists( 'ebox_Admin_Team_Edit' ) ) ) {

	/**
	 * Class ebox Admin Team Edit.
	 *
	 * @since 3.2.0
	 * @uses ebox_Admin_Post_Edit
	 */
	class ebox_Admin_Team_Edit extends ebox_Admin_Post_Edit {
		/**
		 * Public constructor for class.
		 *
		 * @since 3.2.0
		 */
		public function __construct() {
			$this->post_type = ebox_get_post_type_slug( 'team' );
			parent::__construct();
		}

		/**
		 * On Load handler function for this post type edit.
		 * This function is called by a WP action when the admin
		 * page 'post.php' or 'post-new.php' are loaded.
		 *
		 * @since 3.2.0
		 */
		public function on_load() {
			if ( $this->post_type_check() ) {

				require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/settings-metaboxes/class-ld-settings-metabox-team-display-content.php';
				require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/settings-metaboxes/class-ld-settings-metabox-team-access-settings.php';

				require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/settings-metaboxes/class-ld-settings-metabox-team-users.php';
				require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/settings-metaboxes/class-ld-settings-metabox-team-leaders.php';

				/** This filter is documented in includes/admin/class-ebox-admin-menus-tabs.php */
				if ( true === apply_filters( 'ebox_show_metabox_team_courses', true ) ) {
					require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/settings-metaboxes/class-ld-settings-metabox-team-courses.php';
					require_once ebox_LMS_PLUGIN_DIR . 'includes/settings/settings-metaboxes/class-ld-settings-metabox-team-courses-enroll.php';
				}

				parent::on_load();

				$this->_metaboxes = apply_filters( 'ebox_post_settings_metaboxes_init_' . $this->post_type, $this->_metaboxes );
			}
		}

		/**
		 * Register Teams meta box for admin
		 * Managed enrolled teams, users and team leaders
		 *
		 * @since 3.2.0
		 *
		 * @param string $post_type Post Type being edited.
		 * @param object $post      WP_Post Post being edited.
		 */
		public function add_metaboxes( $post_type = '', $post = null ) {

			if ( $this->post_type_check( $post_type ) ) {
				parent::add_metaboxes( $post_type, $post );
			}

			add_meta_box(
				'ebox_team_attributes_metabox',
				sprintf(
					// translators: placeholder: Team.
					esc_html_x( '%s Attributes', 'placeholder: Team', 'ebox' ),
					ebox_get_custom_label( 'team' )
				),
				array( $this, 'team_attributes_metabox_content' ),
				ebox_get_post_type_slug( 'team' ),
				'side'
			);
		}

		/**
		 * Show Metabox
		 *
		 * @since 3.2.0
		 *
		 * @param object $post WP_Post object.
		 */
		public function team_attributes_metabox_content( $post ) {
			if ( is_post_type_hierarchical( $post->post_type ) ) {
				$dropdown_args = array(
					'post_type'        => $post->post_type,
					'exclude_tree'     => $post->ID,
					'selected'         => $post->post_parent,
					'name'             => 'team_parent_id',
					'show_option_none' => esc_html__( '(no parent)', 'ebox' ),
					'sort_column'      => 'menu_order, post_title',
					'echo'             => 0,
				);

				$teams = wp_dropdown_pages( $dropdown_args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- See list of args above
				if ( ! empty( $teams ) ) {
					wp_nonce_field( 'ld-team-attributes-metabox-nonce', 'ld-team-attributes-metabox-nonce', false );
					?>
					<p class="post-attributes-label-wrapper team-parent-id-label-wrapper"><label class="post-attributes-label" for="team_parent_id">
					<?php
					echo sprintf(
						// translators: placeholder: Team.
						esc_html_x( '%s Parent', 'placeholder: Team', 'ebox' ),
						ebox_get_custom_label( 'team' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
					);
					?>
					</label></p>
					<?php echo $teams; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php
				}
			}

			?>
			<p class="post-attributes-label-wrapper team-menu-order-label-wrapper"><label class="post-attributes-label" for="team_menu_order"><?php esc_html_e( 'Order', 'ebox' ); ?></label></p>
			<input name="team_menu_order" type="text" size="4" id="team_menu_order" value="<?php echo esc_attr( $post->menu_order ); ?>" />
			<?php
		}

		/**
		 * Save metabox handler function.
		 *
		 * @since 3.2.0
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

			if ( ! empty( $this->_metaboxes ) ) {
				foreach ( $this->_metaboxes as $_metaboxes_instance ) {
					$settings_fields = array();
					$settings_fields = $_metaboxes_instance->get_post_settings_field_updates( $post_id, $post, $update );
					$_metaboxes_instance->save_post_meta_box( $post_id, $post, $update, $settings_fields );
				}
			}

			$edit_post = array(
				'ID'          => $post->ID,
				'post_parent' => $post->post_parent,
				'menu_order'  => $post->menu_order,
			);

			if ( ( isset( $_POST['ld-team-attributes-metabox-nonce'] ) ) && ( ! empty( $_POST['ld-team-attributes-metabox-nonce'] ) ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ld-team-attributes-metabox-nonce'] ) ), 'ld-team-attributes-metabox-nonce' ) ) {

				$updated_post = false;

				if ( isset( $_POST['team_parent_id'] ) ) {
					$updated_post             = true;
					$edit_post['post_parent'] = absint( $_POST['team_parent_id'] );
				}

				if ( isset( $_POST['team_menu_order'] ) ) {
					$updated_post            = true;
					$edit_post['menu_order'] = absint( $_POST['team_menu_order'] );
				}

				if ( true === $updated_post ) {
					wp_update_post( $edit_post );
				}
			}

			$team_leaders = array();
			$team_users   = array();
			$team_courses = array();

			/**
			 * Fires after the team post data is updated.
			 *
			 * @since 2.3.1
			 * @deprecated 3.1.7
			 *
			 * @param integer $post_id       Post ID of the team
			 * @param array   $team_leaders An array of team leaders.
			 * @param array   $team_users   An array of team users.
			 * @param array   $team_courses An array of team courses.
			 */
			do_action_deprecated( 'ld_team_postdata_updated', array( $post_id, $team_leaders, $team_users, $team_courses ), '3.1.7' );
		}

		// End of functions.
	}
}
new ebox_Admin_Team_Edit();
