<?php
/**
 * ebox Shortcode Section for Team List [ld_team_list].
 *
 * @since 3.2.0
 * @package ebox\Settings\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Shortcodes_Section' ) ) && ( ! class_exists( 'ebox_Shortcodes_Section_ld_team_list' ) ) ) {
	/**
	 * Class ebox Shortcode Section for Team List [ld_team_list].
	 *
	 * @since 3.2.0
	 */
	class ebox_Shortcodes_Section_ld_team_list extends ebox_Shortcodes_Section /* phpcs:ignore PEAR.NamingConventions.ValidClassName.Invalid */ {

		/**
		 * Public constructor for class.
		 *
		 * @since 3.2.0
		 *
		 * @param array $fields_args Field Args.
		 */
		public function __construct( $fields_args = array() ) {
			$this->fields_args = $fields_args;
			$teams_public     = ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Teams_CPT', 'public' ) === '' ) ? ebox_teams_get_not_public_message() : '';

			$this->shortcodes_section_key = 'ld_team_list';
			// translators: placeholder: Team.
			$this->shortcodes_section_title = sprintf( esc_html_x( '%s List', 'placeholder: Team', 'ebox' ), ebox_Custom_Label::get_label( 'team' ) );
			$this->shortcodes_section_type  = 1;
			// translators: placeholders: teams, teams (URL slug).
			$this->shortcodes_section_description = sprintf( wp_kses_post( _x( 'This shortcode shows list of %1$s. You can use this shortcode on any page if you do not want to use the default <code>/%2$s/</code> page. %3$s', 'placeholders: teams, teams (URL slug)', 'ebox' ) ), ebox_get_custom_label_lower( 'teams' ), ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_Permalinks', 'teams' ), $teams_public );

			parent::__construct();
		}

		/**
		 * Initialize the shortcode fields.
		 *
		 * @since 3.2.0
		 */
		public function init_shortcodes_section_fields() {
			$this->shortcodes_option_fields = array(
				'orderby'        => array(
					'id'        => $this->shortcodes_section_key . '_orderby',
					'name'      => 'orderby',
					'type'      => 'select',
					'label'     => esc_html__( 'Order by', 'ebox' ),
					'help_text' => wp_kses_post( __( 'See <a target="_blank" href="https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters">the full list of available orderby options here.</a>', 'ebox' ) ),
					'value'     => 'ID',
					'options'   => array(
						'ID'         => esc_html__( 'ID - Order by post id. (default)', 'ebox' ),
						'title'      => esc_html__( 'Title - Order by post title', 'ebox' ),
						'date'       => esc_html__( 'Date - Order by post date', 'ebox' ),
						'menu_order' => esc_html__( 'Menu - Order by Page Order Value', 'ebox' ),
					),
				),
				'order'          => array(
					'id'        => $this->shortcodes_section_key . '_order',
					'name'      => 'order',
					'type'      => 'select',
					'label'     => esc_html__( 'Order', 'ebox' ),
					'help_text' => esc_html__( 'Order', 'ebox' ),
					'value'     => 'ID',
					'options'   => array(
						''    => esc_html__( 'DESC - highest to lowest values (default)', 'ebox' ),
						'ASC' => esc_html__( 'ASC - lowest to highest values', 'ebox' ),
					),
				),
				'num'            => array(
					'id'        => $this->shortcodes_section_key . '_num',
					'name'      => 'num',
					'type'      => 'number',
					// translators: placeholder: Teams.
					'label'     => sprintf( esc_html_x( '%s Per Page', 'placeholder: Teams', 'ebox' ), ebox_Custom_Label::get_label( 'teams' ) ),
					// translators: placeholders: teams, default per page.
					'help_text' => sprintf( esc_html_x( '%1$s per page. Default is %2$d. Set to zero for all.', 'placeholders: teams, default per page', 'ebox' ), ebox_Custom_Label::get_label( 'teams' ), ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_General_Per_Page', 'per_page' ) ),
					'value'     => '',
					'class'     => 'small-text',
					'attrs'     => array(
						'min'  => 0,
						'step' => 1,
					),
				),
				'price_type'     => array(
					'id'        => $this->shortcodes_section_key . '_price_type',
					'name'      => 'price_type',
					'type'      => 'multiselect',
					// translators: placeholder: Team Access Modes.
					'label'     => sprintf( esc_html_x( '%s Access Mode(s)', 'placeholder: Team Access Modes', 'ebox' ), ebox_get_custom_label( 'teams' ) ),
					// translators: placeholder: teams.
					'help_text' => sprintf( esc_html_x( 'Filter %s by access mode(s), Ctrl+click to deselect selected items.', 'placeholder: teams', 'ebox' ), ebox_get_custom_label_lower( 'teams' ) ),
					'value'     => '',
					'options'   => array(
						'free'      => esc_html__( 'Free', 'ebox' ),
						'paynow'    => esc_html__( 'Buy Now', 'ebox' ),
						'subscribe' => esc_html__( 'Recurring', 'ebox' ),
						'closed'    => esc_html__( 'Closed', 'ebox' ),
					),
				),
				'myteams'       => array(
					'id'        => $this->shortcodes_section_key . '_myteams',
					'name'      => 'myteams',
					'type'      => 'select',
					// translators: placeholder: Teams.
					'label'     => sprintf( esc_html_x( 'My %s', 'placeholder: Teams', 'ebox' ), ebox_Custom_Label::get_label( 'teams' ) ),
					// translators: placeholder: teams.
					'help_text' => sprintf( esc_html_x( 'show current user\'s %s.', 'placeholders: teams', 'ebox' ), ebox_get_custom_label_lower( 'teams' ) ),
					'value'     => '',
					'options'   => array(
						// translators: placeholders: teams.
						''             => sprintf( esc_html_x( 'Show All %s (default)', 'placeholders: teams', 'ebox' ), ebox_get_custom_label_lower( 'Teams' ) ),
						// translators: placeholders: teams.
						'enrolled'     => sprintf( esc_html_x( 'Show Enrolled %s only', 'placeholders: teams', 'ebox' ), ebox_get_custom_label_lower( 'Teams' ) ),
						// translators: placeholders: teams.
						'not-enrolled' => sprintf( esc_html_x( 'Show not-Enrolled %s only', 'placeholders: teams', 'ebox' ), ebox_get_custom_label_lower( 'Teams' ) ),
					),
				),
				'status'         => array(
					'id'        => $this->shortcodes_section_key . '_status',
					'name'      => 'status',
					'type'      => 'multiselect',
					// translators: placeholder: Team.
					'label'     => sprintf( esc_html_x( 'All %s Status', 'placeholder: Team', 'ebox' ), ebox_Custom_Label::get_label( 'team' ) ),
					// translators: placeholder: teams.
					'help_text' => sprintf( esc_html_x( 'filter %s by status.', 'placeholders: teams', 'ebox' ), ebox_get_custom_label_lower( 'teams' ) ),
					'value'     => array( 'not_started', 'in_progress', 'completed' ),
					'options'   => array(
						'not_started' => esc_html__( 'Not Started', 'ebox' ),
						'in_progress' => esc_html__( 'In Progress', 'ebox' ),
						'completed'   => esc_html__( 'Completed', 'ebox' ),
					),
				),
				'show_content'   => array(
					'id'        => $this->shortcodes_section_key . 'show_content',
					'name'      => 'show_content',
					'type'      => 'select',
					// translators: placeholder: Team.
					'label'     => sprintf( esc_html_x( 'Show %s Content', 'placeholder: Team', 'ebox' ), ebox_Custom_Label::get_label( 'team' ) ),
					// translators: placeholder: team.
					'help_text' => sprintf( esc_html_x( 'shows %s content.', 'placeholder: team', 'ebox' ), ebox_get_custom_label_lower( 'team' ) ),
					'value'     => 'true',
					'options'   => array(
						''      => esc_html__( 'Yes (default)', 'ebox' ),
						'false' => esc_html__( 'No', 'ebox' ),
					),
				),
				'show_thumbnail' => array(
					'id'        => $this->shortcodes_section_key . '_show_thumbnail',
					'name'      => 'show_thumbnail',
					'type'      => 'select',
					// translators: placeholder: Team.
					'label'     => sprintf( esc_html_x( 'Show %s Thumbnail', 'placeholder: Team', 'ebox' ), ebox_Custom_Label::get_label( 'team' ) ),
					// translators: placeholder: team.
					'help_text' => sprintf( esc_html_x( 'shows a %s thumbnail.', 'placeholder: team', 'ebox' ), ebox_get_custom_label_lower( 'team' ) ),
					'value'     => 'true',
					'options'   => array(
						''      => esc_html__( 'Yes (default)', 'ebox' ),
						'false' => esc_html__( 'No', 'ebox' ),
					),
				),
			);

			if ( defined( 'ebox_COURSE_GRID_FILE' ) ) {
				$this->shortcodes_option_fields['col'] = array(
					'id'        => $this->shortcodes_section_key . '_col',
					'name'      => 'col',
					'type'      => 'number',
					'label'     => esc_html__( 'Columns', 'ebox' ),
					// translators: placeholder: team.
					'help_text' => sprintf( esc_html_x( 'number of columns to show when using %s grid addon', 'placeholder: team', 'ebox' ), ebox_get_custom_label_lower( 'team' ) ),
					'value'     => '',
					'class'     => 'small-text',
				);
			}

			if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Teams_Taxonomies', 'ld_team_category' ) == 'yes' ) {

				$this->shortcodes_option_fields['team_category_name'] = array(
					'id'        => $this->shortcodes_section_key . 'team_category_name',
					'name'      => 'team_category_name',
					'type'      => 'text',
					// translators: placeholder: Team.
					'label'     => sprintf( esc_html_x( '%s Category Slug', 'placeholder: Team', 'ebox' ), ebox_Custom_Label::get_label( 'team' ) ),
					// translators: placeholder: teams.
					'help_text' => sprintf( esc_html_x( 'shows %s with mentioned category slug.', 'placeholder: teams', 'ebox' ), ebox_get_custom_label_lower( 'teams' ) ),
					'value'     => '',
				);

				$this->shortcodes_option_fields['team_cat'] = array(
					'id'        => $this->shortcodes_section_key . 'team_cat',
					'name'      => 'team_cat',
					'type'      => 'number',
					// translators: placeholder: Team.
					'label'     => sprintf( esc_html_x( '%s Category ID', 'placeholder: Team', 'ebox' ), ebox_Custom_Label::get_label( 'team' ) ),
					// translators: placeholder: teams.
					'help_text' => sprintf( esc_html_x( 'shows %s with mentioned category id.', 'placeholder: teams', 'ebox' ), ebox_get_custom_label_lower( 'teams' ) ),
					'value'     => '',
					'class'     => 'small-text',
				);

				$this->shortcodes_option_fields['team_categoryselector'] = array(
					'id'        => $this->shortcodes_section_key . 'team_categoryselector',
					'name'      => 'team_categoryselector',
					'type'      => 'checkbox',
					// translators: placeholder: Team.
					'label'     => sprintf( esc_html_x( '%s Category Selector', 'placeholder: Team', 'ebox' ), ebox_Custom_Label::get_label( 'team' ) ),
					// translators: placeholder: team.
					'help_text' => sprintf( esc_html_x( 'shows a %s category dropdown.', 'placeholder: team', 'ebox' ), ebox_get_custom_label_lower( 'team' ) ),
					'value'     => '',
					'options'   => array(
						'true' => esc_html__( 'Yes', 'ebox' ),
					),
				);
			}

			if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Teams_Taxonomies', 'ld_team_tag' ) == 'yes' ) {
				$this->shortcodes_option_fields['team_tag'] = array(
					'id'        => $this->shortcodes_section_key . 'team_tag',
					'name'      => 'team_tag',
					'type'      => 'text',
					// translators: placeholder: Team.
					'label'     => sprintf( esc_html_x( '%s Tag Slug', 'placeholder: Team', 'ebox' ), ebox_Custom_Label::get_label( 'team' ) ),
					// translators: placeholder: teams.
					'help_text' => sprintf( esc_html_x( 'shows %s with mentioned tag slug.', 'placeholder: teams', 'ebox' ), ebox_get_custom_label_lower( 'teams' ) ),
					'value'     => '',
				);

				$this->shortcodes_option_fields['team_tag_id'] = array(
					'id'        => $this->shortcodes_section_key . 'team_tag_id',
					'name'      => 'team_tag_id',
					'type'      => 'number',
					// translators: placeholder: Team.
					'label'     => sprintf( esc_html_x( '%s Tag ID', 'placeholder: Team', 'ebox' ), ebox_Custom_Label::get_label( 'team' ) ),
					// translators: placeholder: teams.
					'help_text' => sprintf( esc_html_x( 'shows %s with mentioned tag id.', 'placeholder: teams', 'ebox' ), ebox_get_custom_label_lower( 'teams' ) ),
					'value'     => '',
					'class'     => 'small-text',
				);
			}

			if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Teams_Taxonomies', 'wp_post_category' ) == 'yes' ) {

				$this->shortcodes_option_fields['category_name'] = array(
					'id'        => $this->shortcodes_section_key . 'category_name',
					'name'      => 'category_name',
					'type'      => 'text',
					'label'     => esc_html__( 'WP Category Slug', 'ebox' ),
					// translators: placeholder: teams.
					'help_text' => sprintf( esc_html_x( 'shows %s with mentioned WP category slug.', 'placeholder: teams', 'ebox' ), ebox_get_custom_label_lower( 'teams' ) ),
					'value'     => '',
				);

				$this->shortcodes_option_fields['cat'] = array(
					'id'        => $this->shortcodes_section_key . 'cat',
					'name'      => 'cat',
					'type'      => 'number',
					'label'     => esc_html__( 'WP Category ID', 'ebox' ),
					// translators: placeholder: teams.
					'help_text' => sprintf( esc_html_x( 'shows %s with mentioned WP category id.', 'placeholder: teams', 'ebox' ), ebox_get_custom_label_lower( 'teams' ) ),
					'value'     => '',
					'class'     => 'small-text',
				);

				$this->shortcodes_option_fields['categoryselector'] = array(
					'id'        => $this->shortcodes_section_key . 'categoryselector',
					'name'      => 'categoryselector',
					'type'      => 'checkbox',
					'label'     => esc_html__( 'WP Category Selector', 'ebox' ),
					'help_text' => esc_html__( 'shows a WP category dropdown.', 'ebox' ),
					'value'     => '',
					'options'   => array(
						'true' => esc_html__( 'Yes', 'ebox' ),
					),
				);
			}

			if ( ebox_Settings_Section::get_section_setting( 'ebox_Settings_Teams_Taxonomies', 'wp_post_tag' ) == 'yes' ) {
				$this->shortcodes_option_fields['tag'] = array(
					'id'        => $this->shortcodes_section_key . 'tag',
					'name'      => 'tag',
					'type'      => 'text',
					'label'     => esc_html__( 'WP Tag Slug', 'ebox' ),
					// translators: placeholder: teams.
					'help_text' => sprintf( esc_html_x( 'shows %s with mentioned WP tag slug.', 'placeholder: teams', 'ebox' ), ebox_get_custom_label_lower( 'teams' ) ),
					'value'     => '',
				);

				$this->shortcodes_option_fields['tag_id'] = array(
					'id'        => $this->shortcodes_section_key . 'tag_id',
					'name'      => 'tag_id',
					'type'      => 'number',
					'label'     => esc_html__( 'WP Tag ID', 'ebox' ),
					// translators: placeholder: teams.
					'help_text' => sprintf( esc_html_x( 'shows %s with mentioned WP tag id.', 'placeholder: teams', 'ebox' ), ebox_get_custom_label_lower( 'teams' ) ),
					'value'     => '',
					'class'     => 'small-text',
				);
			}

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-team-access-settings.php */
			$this->shortcodes_option_fields = apply_filters( 'ebox_settings_fields', $this->shortcodes_option_fields, $this->shortcodes_section_key );

			parent::init_shortcodes_section_fields();
		}

		/**
		 * Show Shortcode section footer extra
		 *
		 * @since 3.2.0
		 */
		public function show_shortcodes_section_footer_extra() {
			?>
			<script>
				jQuery( function() {
					if ( jQuery( 'form#ebox_shortcodes_form_ld_team_list select#ld_team_list_myteams' ).length) {
						jQuery( 'form#ebox_shortcodes_form_ld_team_list select#ld_team_list_myteams').on( 'change', function() {
							var selected = jQuery(this).val();
							if ( selected == 'enrolled' ) {
								jQuery( 'form#ebox_shortcodes_form_ld_team_list #ld_team_list_status_field select option').attr('selected', true);
								jQuery( 'form#ebox_shortcodes_form_ld_team_list #ld_team_list_status_field').slideDown();
							} else {
								jQuery( 'form#ebox_shortcodes_form_ld_team_list #ld_team_list_status_field').hide();
								jQuery( 'form#ebox_shortcodes_form_ld_team_list #ld_team_list_status_field select').val('');
							}
						});
						jQuery( 'form#ebox_shortcodes_form_ld_team_list select#ld_team_list_myteams').change();
					}
				});
			</script>
			<?php
		}
	}
}
