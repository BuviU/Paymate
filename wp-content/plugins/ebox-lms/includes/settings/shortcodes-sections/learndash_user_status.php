<?php
/**
 * ebox Shortcode Section for User Status [ebox_user_status].
 *
 * @since 4.0.0
 * @package ebox\Settings\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Shortcodes_Section' ) ) && ( ! class_exists( 'ebox_Shortcodes_Section_ebox_user_status' ) ) ) {
	/**
	 * Class ebox Shortcode Section for User Status [ebox_user_status].
	 *
	 * @since 4.0.0
	 */
	class ebox_Shortcodes_Section_ebox_user_status extends ebox_Shortcodes_Section /* phpcs:ignore PEAR.NamingConventions.ValidClassName.Invalid */ {

		/**
		 * Public constructor for class.
		 *
		 * @since 4.0.0
		 *
		 * @param array $fields_args Field Args.
		 */
		public function __construct( $fields_args = array() ) {
			$this->fields_args = $fields_args;

			$this->shortcodes_section_key         = 'ebox_user_status';
			$this->shortcodes_section_title       = esc_html__( 'User Status', 'ebox' );
			$this->shortcodes_section_type        = 2;
			$this->shortcodes_section_description = esc_html__( 'This shortcode displays information of enrolled courses and their progress for a user. Defaults to current logged in user if no ID specified.', 'ebox' );

			parent::__construct();
		}

		/**
		 * Initialize the shortcode fields.
		 *
		 * @since 4.0.0
		 */
		public function init_shortcodes_section_fields() {
			$this->shortcodes_option_fields = array(
				'user_id'             => array(
					'id'        => $this->shortcodes_section_key . '_user_d',
					'name'      => 'user_id',
					'type'      => 'number',
					'label'     => esc_html__( 'User ID', 'ebox' ),
					'help_text' => esc_html__( 'ID of the user to display information for.', 'ebox' ),
					'value'     => '',
				),
				'registered_num'      => array(
					'id'        => $this->shortcodes_section_key . '_registered_num',
					'name'      => 'registered_num',
					'type'      => 'number',
					'label'     => esc_html__( 'Courses Per Page', 'ebox' ),
					'help_text' => esc_html__( 'Number of courses to display per page. Set to 0 for no pagination.', 'ebox' ),
					'value'     => '',
				),
				'registered_order_by' => array(
					'id'      => $this->shortcodes_section_key . '_registered_order_by',
					'name'    => 'registered_order_by',
					'type'    => 'select',
					'label'   => esc_html__( 'Order By', 'ebox' ),
					'value'   => 'title',
					'options' => array(
						'post_title' => esc_html__( 'Title (default)', 'ebox' ),
						'post_id'    => esc_html__( 'No', 'ebox' ),
						'post_date'  => esc_html__( 'Date', 'ebox' ),
						'menu_order' => esc_html__( 'Menu', 'ebox' ),
					),
				),
				'registered_order'    => array(
					'id'      => $this->shortcodes_section_key . '_registered_order',
					'name'    => 'registered_order',
					'type'    => 'select',
					'label'   => esc_html__( 'Order', 'ebox' ),
					'value'   => 'ASC',
					'options' => array(
						'ASC'  => esc_html__( 'ASC (default)', 'ebox' ),
						'DESC' => esc_html__( 'DESC', 'ebox' ),
					),
				),
			);

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
			$this->shortcodes_option_fields = apply_filters( 'ebox_settings_fields', $this->shortcodes_option_fields, $this->shortcodes_section_key );

			parent::init_shortcodes_section_fields();
		}
	}
}
