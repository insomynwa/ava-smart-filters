<?php
/**
 * Class: Ava_Smart_Filters_Provider_Ava_Engine
 * Name: AvaEngine
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Ava_Smart_Filters_Provider_Ava_Engine' ) ) {

	/**
	 * Define Ava_Smart_Filters_Provider_Ava_Engine class
	 */
	class Ava_Smart_Filters_Provider_Ava_Engine extends Ava_Smart_Filters_Provider_Base {

		/**
		 * Watch for default query
		 */
		public function __construct() {

			if ( ! ava_smart_filters()->query->is_ajax_filter() ) {
				add_filter('ava-engine/listing/grid/posts-query-args', array( $this, 'store_default_query' ), 0, 2 );
			}

		}

		/**
		 * Store default query args
		 *
		 * @param  [type] $args [description]
		 * @return [type]       [description]
		 */
		public function store_default_query( $args, $widget ) {

			if ( 'ava-listing-grid' !== $widget->get_name() ) {
				return $args;
			}

			$settings = $widget->get_settings();

			if ( empty( $settings['_element_id'] ) ) {
				$query_id = false;
			} else {
				$query_id = $settings['_element_id'];
			}

			if ( 'yes' === $settings['is_archive_template'] ){
				ava_smart_filters()->query->set_props(
					$this->get_id(),
					array(
						'found_posts'   => $args['found_posts'],
						'max_num_pages' => $args['max_num_pages'],
						'page'          => $args['paged'],
					),
					$query_id
				);
			}

			add_filter( 'found_posts', array( $this, 'adjust_offset_pagination' ), 1, 2 );

			ava_smart_filters()->query->store_provider_default_query( $this->get_id(), $args, $query_id );

			ava_smart_filters()->providers->store_provider_settings( $this->get_id(), array(
				'lisitng_id'           => $settings['lisitng_id'],
				'columns'              => ! empty( $settings['columns'] ) ? $settings['columns'] : 3,
				'columns_tablet'       => ! empty( $settings['columns_tablet'] ) ? $settings['columns_tablet'] : false,
				'columns_mobile'       => ! empty( $settings['columns_mobile'] ) ? $settings['columns_mobile'] : false,
				'not_found_message'    => ! empty( $settings['not_found_message'] ) ? $settings['not_found_message'] : '',
				'equal_columns_height' => ! empty( $settings['equal_columns_height'] ) ? $settings['equal_columns_height'] : '',
				'carousel_enabled'     => ! empty( $settings['carousel_enabled'] ) ? $settings['carousel_enabled'] : '',
				'slides_to_scroll'     => ! empty( $settings['slides_to_scroll'] ) ? $settings['slides_to_scroll'] : '',
				'arrows'               => ! empty( $settings['arrows'] ) ? $settings['arrows'] : '',
				'arrow_icon'           => ! empty( $settings['arrow_icon'] ) ? $settings['arrow_icon'] : '',
				'dots'                 => ! empty( $settings['dots'] ) ? $settings['dots'] : '',
				'autoplay'             => ! empty( $settings['autoplay'] ) ? $settings['autoplay'] : '',
				'autoplay_speed'       => ! empty( $settings['autoplay_speed'] ) ? $settings['autoplay_speed'] : '',
				'infinite'             => ! empty( $settings['infinite'] ) ? $settings['infinite'] : '',
				'effect'               => ! empty( $settings['effect'] ) ? $settings['effect'] : '',
				'speed'                => ! empty( $settings['speed'] ) ? $settings['speed'] : '',
				'is_masonry'           => ! empty( $settings['is_masonry'] ) ? $settings['is_masonry'] : '',
			), $query_id );

			$args['suppress_filters']  = false;
			$args['ava_smart_filters'] = ava_smart_filters()->query->encode_provider_data(
				$this->get_id(),
				$query_id
			);

			return $args;
		}

		/**
		 * Get provider name
		 *
		 * @return string
		 */
		public function get_name() {
			return __( 'AvaEngine', 'ava-smart-filters' );
		}

		/**
		 * Get provider ID
		 *
		 * @return string
		 */
		public function get_id() {
			return 'ava-engine';
		}

		/**
		 * Get filtered provider content
		 *
		 * @return string
		 */
		public function ajax_get_content() {

			if ( ! function_exists( 'ava_engine' ) ) {
				return;
			}

			add_filter( 'ava-engine/listing/grid/posts-query-args', array( $this, 'add_query_args' ), 10, 2 );
			add_filter( 'ava-engine/listing/grid/custom-settings', array( $this, 'add_settings' ), 10, 2 );

			if ( ! class_exists( 'Elementor\Ava_Listing_Grid_Widget' ) ) {
				if ( version_compare( ava_engine()->get_version(), '2.0', '<' ) ) {
					require_once ava_engine()->plugin_path( 'includes/listings/static-widgets/grid.php' );
				} else {
					require_once ava_engine()->plugin_path( 'includes/components/elementor-views/static-widgets/grid.php' );
				}
			}

			Elementor\Plugin::instance()->frontend->start_excerpt_flag( null );

			$widget = new Elementor\Ava_Listing_Grid_Widget();
			$widget->render_posts();

		}

		/**
		 * Get provider wrapper selector
		 *
		 * @return string
		 */
		public function get_wrapper_selector() {
			return '.elementor-widget-ava-listing-grid > .elementor-widget-container';
		}

		/**
		 * Add custom settings for AJAX request
		 */
		public function add_settings( $settings, $widget ) {

			if ( 'ava-listing-grid' !== $widget->get_name() ) {
				return $settings;
			}

			return ava_smart_filters()->query->get_query_settings();
		}

		/**
		 * Pass args from reuest to provider
		 */
		public function apply_filters_in_request() {

			$args = ava_smart_filters()->query->get_query_args();

			if ( ! $args ) {
				return;
			}

			add_filter( 'ava-engine/listing/grid/posts-query-args', array( $this, 'add_query_args' ), 10, 2 );

		}

		/**
		 * Updates the arguments based on the offset parameter
		 *
		 * @param $args
		 *
		 * @return mixed
		 */
		public function query_maybe_has_offset( $args ){

			if ( isset( $args['offset'] ) ){

				add_filter( 'found_posts', array( $this, 'adjust_offset_pagination' ), 1, 2 );

				if( isset( $args['paged'] ) ){
					$args['offset'] = $args['offset'] + ( ( $args['paged'] - 1 ) * $args['posts_per_page'] );
				}

			}

			return $args;

		}

		/**
		 * Adjusts page number shift
		 *
		 * @param $found_posts
		 * @param $query
		 *
		 * @return mixed
		 */
		function adjust_offset_pagination( $found_posts, $query ) {

			$found_posts = (int) $found_posts;
			$offset      = (int) $query->get( 'offset' );

			if ( $query->get( 'ava_smart_filters' ) && isset( $offset ) ){

				$paged = $query->get( 'paged' );
				$posts_per_page = $query->get( 'posts_per_page' );

				if ( 0 < $paged ){
					$offset = $offset - ( ( $paged - 1 ) * $posts_per_page );
				}

				return $found_posts - $offset;

			}

			return $found_posts;

		}

		/**
		 * Add custom query arguments
		 *
		 * @param array $args [description]
		 */
		public function add_query_args( $args = array(), $widget ) {

			if ( 'ava-listing-grid' !== $widget->get_name() ) {
				return $args;
			}

			if ( ! ava_smart_filters()->query->is_ajax_filter() ) {

				$settings = $widget->get_settings();

				if ( empty( $settings['_element_id'] ) ) {
					$query_id = 'default';
				} else {
					$query_id = $settings['_element_id'];
				}

				$request_query_id = ava_smart_filters()->query->get_current_provider( 'query_id' );

				if ( $query_id !== $request_query_id ) {
					return $args;
				}

			}

			$query_args = array_merge( $args, ava_smart_filters()->query->get_query_args() );
			$query_args = $this->query_maybe_has_offset( $query_args );

			return $query_args;

		}

	}

}
