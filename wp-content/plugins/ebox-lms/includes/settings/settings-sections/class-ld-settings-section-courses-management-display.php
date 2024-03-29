<?php
/**
 * ebox Settings Section for Course Builder Metabox.
 *
 * @since 3.0.0
 * @package ebox\Settings\Sections
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'ebox_Settings_Section' ) ) && ( ! class_exists( 'ebox_Settings_Courses_Management_Display' ) ) ) {
	/**
	 * Class ebox Settings Section for Course Builder Metabox.
	 *
	 * @since 3.0.0
	 */
	class ebox_Settings_Courses_Management_Display extends ebox_Settings_Section {

		/**
		 * Protected constructor for class
		 *
		 * @since 3.0.0
		 */
		protected function __construct() {

			// What screen ID are we showing on.
			$this->settings_screen_id = 'ebox-courses_page_courses-options';

			// The page ID (different than the screen ID).
			$this->settings_page_id = 'courses-options';

			// This is the 'option_name' key used in the wp_options table.
			$this->setting_option_key = 'ebox_settings_courses_management_display';

			// This is the HTML form field prefix used.
			$this->setting_field_prefix = 'ebox_settings_courses_management_display';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_section_key = 'course_management_display';

			// Section label/header.
			$this->settings_section_label = sprintf(
				// translators: placeholder: Course.
				esc_html_x( 'Global %s Management & Display Settings', 'placeholder: Course', 'ebox' ),
				ebox_Custom_Label::get_label( 'course' )
			);

			// Used to show the section description above the fields. Can be empty.
			$this->settings_section_description = sprintf(
				// translators: placeholder: course.
				esc_html_x( 'Control settings for %s creation, and visual organization', 'placeholder: course', 'ebox' ),
				ebox_get_custom_label_lower( 'course' )
			);

			// Define the deprecated Class and Fields.
			$this->settings_deprecated = array(
				'ebox_Settings_Courses_Builder' => array(
					'option_key' => 'ebox_settings_courses_builder',
					'fields'     => array(
						'enabled'      => 'course_builder_enabled',
						'shared_steps' => 'course_builder_shared_steps',
						'per_page'     => 'course_builder_per_page',
					),
				),
				'ebox_Settings_Section_modules_Display_Order' => array(
					'option_key' => 'ebox_settings_modules_display_order',
					'fields'     => array(
						'posts_per_page' => 'course_pagination_modules', // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page
						'order'          => 'lesson_topic_order',
						'orderby'        => 'lesson_topic_orderby',
					),
				),
			);

			parent::__construct();
		}

		/**
		 * Initialize the metabox settings values.
		 *
		 * @since 3.0.0
		 */
		public function load_settings_values() {
			parent::load_settings_values();

			// If the settings set as a whole is empty then we set a default.
			if ( ( false === $this->setting_option_values ) || ( '' === $this->setting_option_values ) ) {
				if ( '' === $this->setting_option_values ) {
					$this->setting_option_values = array();
				}
				$this->transition_deprecated_settings();

				if ( ! isset( $this->setting_option_values['course_builder_enabled'] ) ) {
					$this->setting_option_values['course_builder_enabled'] = 'yes';
				}
			}

			if ( '' === $this->setting_option_values ) {
				$this->setting_option_values = array();
			}

			if ( ! isset( $this->setting_option_values['course_builder_enabled'] ) ) {
				$this->setting_option_values['course_builder_enabled'] = '';
			}

			if ( ! isset( $this->setting_option_values['course_builder_shared_steps'] ) ) {
				$this->setting_option_values['course_builder_shared_steps'] = '';
			}

			if ( ! isset( $this->setting_option_values['course_builder_per_page'] ) ) {
				$this->setting_option_values['course_builder_per_page'] = ebox_LMS_DEFAULT_WIDGET_PER_PAGE;
			}

			$this->setting_option_values['course_builder_per_page'] = absint( $this->setting_option_values['course_builder_per_page'] );
			if ( empty( $this->setting_option_values['course_builder_per_page'] ) ) {
				$this->setting_option_values['course_builder_per_page'] = ebox_LMS_DEFAULT_WIDGET_PER_PAGE;
			}

			if ( ! isset( $this->setting_option_values['course_pagination_modules'] ) ) {
				if ( isset( $this->setting_option_values['lesson_per_page'] ) ) {
					$this->setting_option_values['course_pagination_modules'] = absint( $this->setting_option_values['lesson_per_page'] );
				} else {
					$this->setting_option_values['course_pagination_modules'] = ebox_LMS_DEFAULT_WIDGET_PER_PAGE;
				}
			}

			if ( ! isset( $this->setting_option_values['course_pagination_topics'] ) ) {
				if ( isset( $this->setting_option_values['course_pagination_modules'] ) ) {
					$this->setting_option_values['course_pagination_topics'] = absint( $this->setting_option_values['course_pagination_modules'] );
				} else {
					$this->setting_option_values['course_pagination_topics'] = ebox_LMS_DEFAULT_WIDGET_PER_PAGE;
				}
			}

			if ( ( ebox_LMS_DEFAULT_WIDGET_PER_PAGE === $this->setting_option_values['course_pagination_modules'] ) && ( ebox_LMS_DEFAULT_WIDGET_PER_PAGE === $this->setting_option_values['course_pagination_topics'] ) ) {
				$this->setting_option_values['course_pagination_enabled'] = '';
			} else {
				$this->setting_option_values['course_pagination_enabled'] = 'yes';
			}

			if ( ! isset( $this->setting_option_values['lesson_topic_order'] ) ) {
				$this->setting_option_values['lesson_topic_order'] = 'ASC';
			}
			if ( ! isset( $this->setting_option_values['lesson_topic_orderby'] ) ) {
				$this->setting_option_values['lesson_topic_orderby'] = 'date';
			}

			if ( ( 'date' === $this->setting_option_values['lesson_topic_orderby'] ) && ( 'ASC' === $this->setting_option_values['lesson_topic_order'] ) ) {
				$this->setting_option_values['lesson_topic_order_enabled'] = '';
			} else {
				$this->setting_option_values['lesson_topic_order_enabled'] = 'yes';
			}

			if ( ! isset( $this->setting_option_values['course_mark_incomplete_enabled'] ) ) {
				if ( defined( 'ebox_SHOW_MARK_INCOMPLETE' ) && true === ebox_SHOW_MARK_INCOMPLETE ) {
					$this->setting_option_values['course_mark_incomplete_enabled'] = true;
				} else {
					$this->setting_option_values['course_mark_incomplete_enabled'] = false;
				}
			}
		}

		/**
		 * Initialize the metabox settings fields.
		 *
		 * @since 3.0.0
		 */
		public function load_settings_fields() {
			$this->setting_option_fields = array();

			if ( ( defined( 'ebox_COURSE_BUILDER' ) ) && ( ebox_COURSE_BUILDER === true ) ) {
				$this->setting_option_fields = array_merge(
					$this->setting_option_fields,
					array(
						'course_builder_enabled'      => array(
							'name'                => 'course_builder_enabled',
							'type'                => 'checkbox-switch',
							'label'               => sprintf(
								// translators: placeholder: Course.
								esc_html_x( '%s Builder', 'placeholder: Course', 'ebox' ),
								ebox_get_custom_label( 'course' )
							),
							'help_text'           => sprintf(
								// translators: placeholder: Lesson, Topic, Quiz, Course.
								esc_html_x( 'Manage all %1$s, %2$s, and %3$s associations within the %4$s Builder.', 'placeholder: Lesson, Topic, Quiz, Course.', 'ebox' ),
								ebox_get_custom_label( 'lesson' ),
								ebox_get_custom_label( 'topic' ),
								ebox_get_custom_label( 'quiz' ),
								ebox_get_custom_label( 'course' )
							),
							'value'               => $this->setting_option_values['course_builder_enabled'],
							'options'             => array(
								'yes' => '',
							),
							'child_section_state' => ( 'yes' === $this->setting_option_values['course_builder_enabled'] ) ? 'open' : 'closed',
						),
						'course_builder_per_page'     => array(
							'name'           => 'course_builder_per_page',
							'type'           => 'number',
							'label'          => esc_html__( 'Steps Displayed', 'ebox' ),
							'value'          => $this->setting_option_values['course_builder_per_page'],
							'class'          => 'small-text',
							'input_label'    => esc_html__( 'per page', 'ebox' ),
							'attrs'          => array(
								'step' => 1,
								'min'  => 0,
							),
							'parent_setting' => 'course_builder_enabled',
						),
						'course_builder_shared_steps' => array(
							'name'           => 'course_builder_shared_steps',
							'type'           => 'checkbox-switch',
							'label'          => sprintf(
								// translators: placeholder: Course.
								esc_html_x( 'Shared %s Steps', 'placeholder: Course', 'ebox' ),
								ebox_get_custom_label( 'course' )
							),
							'help_text'      => sprintf(
								wp_kses_post(
									// translators: placeholder: modules, topics, quizzes, courses, course, URL to admin Permalinks.
									_x( 'Share steps (%1$s, %2$s, %3$s) across multiple %4$s. Progress is maintained on a per-%5$s basis.<br /><br />Note: Enabling this option will also enable the <a href="%6$s">nested permalinks</a> setting.', 'placeholder: modules, topics, quizzes, courses, course, URL to admin Permalinks.', 'ebox' )
								),
								ebox_get_custom_label_lower( 'modules' ),
								ebox_get_custom_label_lower( 'topics' ),
								ebox_get_custom_label_lower( 'quizzes' ),
								ebox_get_custom_label_lower( 'courses' ),
								ebox_get_custom_label_lower( 'course' ),
								admin_url( 'options-permalink.php#ebox_settings_permalinks_nested_urls' )
							),
							'value'          => $this->setting_option_values['course_builder_shared_steps'],
							'options'        => array(
								''    => '',
								'yes' => sprintf(
									// translators: placeholders: Lesson, topics and quizzes, courses.
									esc_html_x( '%1$s, %2$s and %3$s can be shared across multiple %4$s', 'placeholders: Lesson, topics and quizzes, courses', 'ebox' ),
									ebox_get_custom_label( 'modules' ),
									ebox_get_custom_label_lower( 'topics' ),
									ebox_get_custom_label_lower( 'quizzes' ),
									ebox_get_custom_label_lower( 'courses' )
								),
							),
							'parent_setting' => 'course_builder_enabled',
						),
					)
				);
			}

			$this->setting_option_fields = array_merge(
				$this->setting_option_fields,
				array(
					'course_pagination_enabled' => array(
						'name'                => 'course_pagination_enabled',
						'type'                => 'checkbox-switch',
						'label'               => sprintf(
							// translators: placeholder: Course.
							esc_html_x( '%s Table Pagination', 'placeholder: Course', 'ebox' ),
							ebox_get_custom_label( 'course' )
						),
						'help_text'           => sprintf(
							// translators: placeholder: course, course.
							esc_html_x( 'Customize the pagination options for ALL %1$s content tables and %2$s navigation widgets.', 'placeholder: course, course', 'ebox' ),
							ebox_get_custom_label_lower( 'courses' ),
							ebox_get_custom_label_lower( 'courses' )
						),
						'value'               => $this->setting_option_values['course_pagination_enabled'],
						'options'             => array(
							''    => sprintf(
								// translators: placeholder: default per page number.
								esc_html_x( 'Currently showing default pagination %d', 'placeholder: default per page number', 'ebox' ),
								ebox_LMS_DEFAULT_WIDGET_PER_PAGE
							),
							'yes' => '',
						),
						'child_section_state' => ( 'yes' === $this->setting_option_values['course_pagination_enabled'] ) ? 'open' : 'closed',
					),
					'course_pagination_modules' => array(
						'name'           => 'course_pagination_modules',
						'type'           => 'number',
						'label'          => ebox_get_custom_label( 'modules' ),
						'value'          => $this->setting_option_values['course_pagination_modules'],
						'class'          => 'small-text',
						'input_label'    => esc_html__( 'per page', 'ebox' ),
						'attrs'          => array(
							'step' => 1,
							'min'  => 0,
						),
						'parent_setting' => 'course_pagination_enabled',
					),
					'course_pagination_topics'  => array(
						'name'           => 'course_pagination_topics',
						'type'           => 'number',
						'label'          => ebox_get_custom_label( 'topics' ),
						'value'          => $this->setting_option_values['course_pagination_topics'],
						'class'          => 'small-text',
						'input_label'    => esc_html__( 'per page', 'ebox' ),
						'attrs'          => array(
							'step' => 1,
							'min'  => 0,
						),
						'parent_setting' => 'course_pagination_enabled',
					),

				)
			);

			if ( 'yes' !== $this->setting_option_values['course_builder_enabled'] ) {
				$this->setting_option_fields = array_merge(
					$this->setting_option_fields,
					array(
						'lesson_topic_order_enabled' => array(
							'name'                => 'lesson_topic_order_enabled',
							'type'                => 'checkbox-switch',
							'label'               => sprintf(
								// translators: placeholder: Lesson, Topic.
								esc_html_x( '%1$s and %2$s Order', 'placeholder: Lesson, Topic', 'ebox' ),
								ebox_get_custom_label( 'lesson' ),
								ebox_get_custom_label( 'topic' )
							),
							'help_text'           => sprintf(
								// translators: placeholder: modules, topics.
								esc_html_x( 'Customize the display order of %1$s and %2$s.', 'placeholder: modules, topics', 'ebox' ),
								ebox_get_custom_label_lower( 'modules' ),
								ebox_get_custom_label_lower( 'topics' )
							),
							'value'               => $this->setting_option_values['lesson_topic_order_enabled'],
							'options'             => array(
								''    => array(
									'label'       => sprintf(
										// translators: placeholder: Default Order By, Order.
										esc_html_x( 'Using default sorting by %1$s in %2$s order', 'placeholder: Default Order By, Order', 'ebox' ),
										'<u>Date</u>',
										'<u>Ascending</u>'
									),
									'description' => '',
								),
								'yes' => array(
									'label'       => '',
									'description' => '',
								),
							),
							'child_section_state' => ( 'yes' === $this->setting_option_values['lesson_topic_order_enabled'] ) ? 'open' : 'closed',
						),
						'lesson_topic_orderby'       => array(
							'name'           => 'lesson_topic_orderby',
							'type'           => 'select',
							'label'          => esc_html__( 'Sort By', 'ebox' ),
							'value'          => $this->setting_option_values['lesson_topic_orderby'],
							'default'        => 'menu_order',
							'options'        => array(
								'menu_order' => esc_html__( 'Menu Order', 'ebox' ),
								'date'       => esc_html__( 'Date', 'ebox' ),
								'title'      => esc_html__( 'Title', 'ebox' ),
							),
							'parent_setting' => 'lesson_topic_order_enabled',
						),
						'lesson_topic_order'         => array(
							'name'           => 'lesson_topic_order',
							'type'           => 'select',
							'label'          => esc_html__( 'Order Direction', 'ebox' ),
							'value'          => $this->setting_option_values['lesson_topic_order'],
							'default'        => 'ASC',
							'options'        => array(
								'ASC'  => esc_html__( 'Ascending', 'ebox' ),
								'DESC' => esc_html__( 'Descending', 'ebox' ),
							),
							'parent_setting' => 'lesson_topic_order_enabled',
						),
					)
				);
			}

			$this->setting_option_fields = array_merge(
				$this->setting_option_fields,
				array(
					'course_mark_incomplete_enabled' => array(
						'name'      => 'course_mark_incomplete_enabled',
						'type'      => 'checkbox-switch',
						'label'     => sprintf(
							esc_html__( 'Mark Incomplete Enabled', 'ebox' )
						),
						'help_text' => sprintf(
							// translators: placeholders: Lesson, Topic.
							esc_html_x( 'Allows to mark a %1$s / %2$s as incomplete', 'placeholders: Lesson, Topic', 'ebox' ),
							ebox_get_custom_label_lower( 'lesson' ),
							ebox_get_custom_label_lower( 'topic' )
						),
						'value'     => $this->setting_option_values['course_mark_incomplete_enabled'],
						'options'   => array(
							'yes' => '',
						),
					),
				)
			);

			/** This filter is documented in includes/settings/settings-metaboxes/class-ld-settings-metabox-course-access-settings.php */
			$this->setting_option_fields = apply_filters( 'ebox_settings_fields', $this->setting_option_fields, $this->settings_section_key );

			global $wp_rewrite;
			if ( ! $wp_rewrite->using_permalinks() ) {
				$this->setting_option_fields['shared_steps']['value'] = '';
				$this->setting_option_fields['shared_steps']['attrs'] = array( 'disabled' => 'disabled' );
			}
			parent::load_settings_fields();
		}

		/**
		 * Intercept the WP options save logic and check that we have a valid nonce.
		 *
		 * @since 3.0.0
		 *
		 * @param array  $current_values Array of section fields values.
		 * @param array  $old_values     Array of old values.
		 * @param string $option         Section option key should match $this->setting_option_key.
		 */
		public function section_pre_update_option( $current_values = '', $old_values = '', $option = '' ) {
			if ( $option === $this->setting_option_key ) {
				$current_values = parent::section_pre_update_option( $current_values, $old_values, $option );
				if ( $current_values !== $old_values ) {
					// Manage Course Builder, Per Page, and Share Steps.
					if ( ( isset( $current_values['course_builder_enabled'] ) ) && ( 'yes' === $current_values['course_builder_enabled'] ) ) {
						$current_values['course_builder_per_page'] = absint( $current_values['course_builder_per_page'] );

					} else {
						$current_values['course_builder_shared_steps'] = '';
						$current_values['course_builder_per_page']     = ebox_LMS_DEFAULT_WIDGET_PER_PAGE;
					}

					if ( ( isset( $current_values['course_builder_shared_steps'] ) ) && ( 'yes' === $current_values['course_builder_shared_steps'] ) ) {
						$current_values['lesson_topic_order_enabled'] = '';
					}

					if ( ( isset( $current_values['course_pagination_enabled'] ) ) && ( 'yes' === $current_values['course_pagination_enabled'] ) ) {
						$current_values['course_pagination_modules'] = absint( $current_values['course_pagination_modules'] );
						$current_values['course_pagination_topics']  = absint( $current_values['course_pagination_topics'] );

						if ( ( ebox_LMS_DEFAULT_WIDGET_PER_PAGE === $current_values['course_pagination_topics'] ) && ( ebox_LMS_DEFAULT_WIDGET_PER_PAGE === $current_values['course_pagination_modules'] ) ) {
							$current_values['course_pagination_enabled'] = '';
						}
					} else {
						$current_values['course_pagination_modules'] = ebox_LMS_DEFAULT_WIDGET_PER_PAGE;
						$current_values['course_pagination_topics']  = ebox_LMS_DEFAULT_WIDGET_PER_PAGE;
					}

					// Lesson and Topic Order and Order By.
					if ( ( isset( $current_values['lesson_topic_order_enabled'] ) ) && ( 'yes' === $current_values['lesson_topic_order_enabled'] ) ) {
						if ( ( ! isset( $current_values['lesson_topic_order'] ) ) || ( empty( $current_values['lesson_topic_order'] ) ) ) {
							$current_values['lesson_topic_order'] = 'ASC';
						}
						if ( ( ! isset( $current_values['lesson_topic_orderby'] ) ) || ( empty( $current_values['lesson_topic_orderby'] ) ) ) {
							$current_values['lesson_topic_orderby'] = 'date';
						}

						if ( ( 'ASC' === $current_values['lesson_topic_order'] ) && ( 'date' === $current_values['lesson_topic_orderby'] ) ) {
							$current_values['lesson_topic_order_enabled'] = '';
						}
					} else {
						$current_values['lesson_topic_order']   = 'ASC';
						$current_values['lesson_topic_orderby'] = 'date';
					}
				}

				if ( ( isset( $current_values['course_builder_enabled'] ) ) && ( 'yes' === $current_values['course_builder_enabled'] ) && ( isset( $current_values['course_builder_shared_steps'] ) ) && ( 'yes' === $current_values['course_builder_shared_steps'] ) ) {
					$ld_permalink_options = ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_Permalinks', 'nested_urls', 'no' );
					if ( 'yes' !== $ld_permalink_options ) {
						ebox_Settings_Section::set_section_setting( 'ebox_Settings_Section_Permalinks', 'nested_urls', 'yes' );

						ebox_setup_rewrite_flush();
					}
				}

				if ( ! isset( $current_values['course_mark_incomplete_enabled'] ) ) {
					$current_values['course_mark_incomplete_enabled'] = '';
				}
			}

			return $current_values;
		}
	}
}
add_action(
	'ebox_settings_sections_init',
	function() {
		ebox_Settings_Courses_Management_Display::add_section_instance();
	}
);
