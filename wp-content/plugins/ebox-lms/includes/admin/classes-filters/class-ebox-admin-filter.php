<?php
/**
 * ebox Admin filter abstract class.
 *
 * @since 4.2.0
 *
 * @package ebox\Filters
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ebox_Admin_Filter' ) ) {
	/**
	 * ebox admin filter abstract class.
	 *
	 * @since 4.2.0
	 */
	abstract class ebox_Admin_Filter {
		/**
		 * Returns the input CSS classes.
		 *
		 * @since 4.2.0
		 *
		 * @param string $additional_classes Additional classes if needed.
		 *
		 * @return string
		 */
		public function get_input_class( string $additional_classes = '' ): string {
			return 'ebox-filter ' . $additional_classes;
		}

		/**
		 * Returns the label.
		 *
		 * @since 4.2.0
		 *
		 * @return string
		 */
		abstract public function get_label(): string;

		/**
		 * Returns the parameter name.
		 *
		 * @since 4.2.0
		 *
		 * @return string
		 */
		abstract public function get_parameter_name(): string;

		/**
		 * Echoes the input HTML.
		 *
		 * @since 4.2.0
		 *
		 * @return void
		 */
		abstract public function display(): void;

		/**
		 * Returns the SQL join clause to apply this filter.
		 *
		 * @since 4.2.0
		 *
		 * @param mixed $filter_value The value of the filter.
		 *
		 * @return string The SQL join clause.
		 */
		abstract public function get_sql_join_clause( $filter_value): string;

		/**
		 * Returns the SQL where clause to apply this filter.
		 *
		 * @since 4.2.0
		 *
		 * @param mixed $filter_value The value of the filter.
		 *
		 * @return string The SQL where clause.
		 */
		abstract public function get_sql_where_clause( $filter_value): string;
	}
}
