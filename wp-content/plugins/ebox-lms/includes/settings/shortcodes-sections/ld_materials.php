<?php
/**
 * ebox Shortcode Section for Materials [ld_materials].
 *
 * @since 4.0.0
 * @package ebox\Settings\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Shortcodes_Section' ) ) && ( ! class_exists( 'ebox_Shortcodes_Section_ld_materials' ) ) ) {
	/**
	 * Class ebox Shortcode Section for Materials [ld_materials]].
	 *
	 * @since 4.0.0
	 */
	class ebox_Shortcodes_Section_ld_materials extends ebox_Shortcodes_Section /* phpcs:ignore PEAR.NamingConventions.ValidClassName.Invalid */ {

		/**
		 * Public constructor for class.
		 *
		 * @since 4.0.0
		 *
		 * @param array $fields_args Field Args.
		 */
		public function __construct( $fields_args = array() ) {
			$this->fields_args = $fields_args;

			$this->shortcodes_section_key         = 'ld_materials';
			$this->shortcodes_section_title       = esc_html__( 'ebox Materials', 'ebox' );
			$this->shortcodes_section_type        = 2;
			$this->shortcodes_section_description = esc_html__( 'This shortcode displays the materials for a specific ebox related post.', 'ebox' );

			parent::__construct();
		}

		/**
		 * Initialize the shortcode fields.
		 *
		 * @since 4.0.0
		 */
		public function init_shortcodes_section_fields() {
			$this->shortcodes_option_fields = array(
				'post_id' => array(
					'id'        => $this->shortcodes_section_key . '_message',
					'name'      => 'post_id',
					'type'      => 'number',
					'label'     => esc_html__( 'Post ID', 'ebox' ),
					'help_text' => esc_html__( 'Enter a Post ID of the ebox post that you want to display materials for.', 'ebox' ),
					'value'     => '',
				),
				'autop'   => array(
					'id'        => $this->shortcodes_section_key . 'autop',
					'name'      => 'autop',
					'type'      => 'select',
					'label'     => esc_html__( 'Auto Paragraph', 'ebox' ),
					'help_text' => esc_html__( 'Format shortcode content into proper paragraphs.', 'ebox' ),
					'value'     => 'true',
					'options'   => array(
						''      => esc_html__( 'Yes (default)', 'ebox' ),
						'false' => esc_html__( 'No', 'ebox' ),
					),
				),
			);

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
			$this->shortcodes_option_fields = apply_filters( 'ebox_settings_fields', $this->shortcodes_option_fields, $this->shortcodes_section_key );

			parent::init_shortcodes_section_fields();
		}
	}
}
