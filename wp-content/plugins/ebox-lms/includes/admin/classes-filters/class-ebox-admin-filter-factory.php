<?php
/**
 * ebox Admin filter factory.
 *
 * @since 4.2.0
 *
 * @package ebox\Filters
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ebox_Admin_Filter_Factory' ) ) {
	/**
	 * ebox admin filter factory class.
	 *
	 * @since 4.2.0
	 */
	class ebox_Admin_Filter_Factory {
		/**
		 * Available filter types.
		 *
		 * @since 4.2.0
		 *
		 * @var array
		 */
		protected static $filter_types = array(
			ebox_Admin_Filters::TYPE_POST_ID          => ebox_Admin_Filter_Post_ID::class,
			ebox_Admin_Filters::TYPE_POST_TITLE       => ebox_Admin_Filter_Post_Title::class,
			ebox_Admin_Filters::TYPE_POST_STATUS      => ebox_Admin_Filter_Post_Status::class,
			ebox_Admin_Filters::TYPE_META_SWITCH      => ebox_Admin_Filter_Meta_Switch::class,
			ebox_Admin_Filters::TYPE_META_SELECT      => ebox_Admin_Filter_Meta_Select::class,
			ebox_Admin_Filters::TYPE_META_SELECT_AJAX => ebox_Admin_Filter_Meta_Select_Ajax::class,
			ebox_Admin_Filters::TYPE_SHARED_STEPS     => ebox_Admin_Filter_Shared_Steps::class,
		);

		/**
		 * Returns ebox_Admin_Filter instance.
		 *
		 * @since 4.2.0
		 *
		 * @param string $type Filter type.
		 * @param mixed  ...$args Filter Parameters.
		 *
		 * @return ebox_Admin_Filter
		 */
		public static function create_filter( string $type, ...$args ): ebox_Admin_Filter {
			/**
			 * Filters admin filter types.
			 *
			 * @since 4.2.0
			 *
			 * @param array $filter_types Admin filters.
			 */
			$filter_types = apply_filters( 'ebox_filter_types', self::$filter_types );

			if ( ! isset( $filter_types[ $type ] ) ) {
				// translators: placeholder: field type.
				wp_die( sprintf( esc_html__( 'ebox admin filter with the "%s" type not found.', 'ebox' ), esc_attr( $type ) ) );
			}

			return new $filter_types[ $type ]( ...$args );
		}
	}
}
