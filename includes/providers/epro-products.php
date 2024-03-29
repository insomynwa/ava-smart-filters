<?php
/**
 * Class: Ava_Smart_Filters_Provider_EPro_Products
 * Name: Elementor Avator Products
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Ava_Smart_Filters_Provider_EPro_Products' ) ) {

	/**
	 * Define Ava_Smart_Filters_Provider_EPro_Products class
	 */
	class Ava_Smart_Filters_Provider_EPro_Products extends Ava_Smart_Filters_Provider_Base {

		/**
		 * Watch for default query
		 */
		public function __construct() {

			if ( ! ava_smart_filters()->query->is_ajax_filter() ) {
				add_action( 'elementor/widget/before_render_content', array( $this, 'store_default_settings' ), 0 );
				add_filter( 'woocommerce_shortcode_products_query', array( $this, 'store_shortcode_query' ), 0, 3 );
			}

		}

		/**
		 * Store default query args
		 *
		 * @param  array  $args       Query arguments.
		 * @param  array  $attributes Shortcode attributes.
		 * @param  string $type       Shortcode type.
		 * @return array
		 */
		public function store_shortcode_query( $args, $attributes, $type ) {

			if ( empty( $attributes['class'] ) ) {
				$query_id = 'default';
			} else {
				$query_id = $attributes['class'];
			}

			$args['suppress_filters']  = false;
			$args['no_found_rows']     = false;
			$args['ava_smart_filters'] = ava_smart_filters()->query->encode_provider_data(
				$this->get_id(),
				$query_id
			);

			ava_smart_filters()->query->store_provider_default_query( $this->get_id(), $args, $query_id );

			if ( isset( $_REQUEST['product-page'] ) ) {
				$attributes['page'] = absint( $_REQUEST['product-page'] );
			}

			ava_smart_filters()->providers->store_provider_settings( $this->get_id(), array(
				'query_type'     => 'shortcode',
				'shortcode_type' => $type,
				'attributes'     => $attributes,
			), $query_id );

			add_action( "woocommerce_shortcode_before_{$type}_loop", array( $this, 'store_props' ) );

			return $args;

		}

		/**
		 * Get provider name
		 *
		 * @return string
		 */
		public function get_name() {
			return __( 'Elementor Avator Products', 'ava-smart-filters' );
		}

		/**
		 * Get provider ID
		 *
		 * @return string
		 */
		public function get_id() {
			return 'epro-products';
		}

		/**
		 * Returns Elementor Avator apropriate widget name
		 * @return [type] [description]
		 */
		public function widget_name() {
			return 'woocommerce-products';
		}

		/**
		 * Returns settings to store list
		 * @return [type] [description]
		 */
		public function settings_to_store() {
			return array(
				'rows',
				'columns',
				'columns_tablet',
				'columns_mobile',
				'paginate',
				'show_result_count',
				'allow_order',
				'query_post_type',
				'query_posts_ids',
				'query_product_cat_ids',
				'query_product_tag_ids',
				'orderby',
				'order',
				'exclude',
				'exclude_ids',
				'avoid_duplicates',
				'query_include',
				'query_include_term_ids',
				'query_include_authors',
				'query_exclude',
				'query_exclude_ids',
				'query_exclude_term_ids',
				'query_select_date',
				'query_date_before',
				'query_date_after',
				'query_orderby',
				'query_order',
				'products_class',
			);
		}

		/**
		 * Save default widget settings
		 *
		 * @param  [type] $widget [description]
		 * @return [type]         [description]
		 */
		public function store_default_settings( $widget ) {

			if ( $this->widget_name() !== $widget->get_name() ) {
				return;
			}

			$settings         = $widget->get_settings();
			$store_settings   = $this->settings_to_store();
			$default_settings = array();

			if ( ! empty( $settings['_element_id'] ) ) {
				$query_id = $settings['_element_id'];
			} else {
				$query_id = 'default';
			}

			foreach ( $store_settings as $key ) {
				$default_settings[ $key ] = isset( $settings[ $key ] ) ? $settings[ $key ] : '';
			}

			$default_settings['_el_widget_id'] = $widget->get_id();

			ava_smart_filters()->providers->store_provider_settings( $this->get_id(), $default_settings, $query_id );

		}

		/**
		 * Ensure all settings are passed
		 * @return [type] [description]
		 */
		public function ensure_settings( $settings ) {

			foreach ( $this->settings_to_store() as $setting ) {
				if ( ! isset( $settings[ $setting ] ) ) {
					if ( false !== strpos( $setting, '_meta_data' ) ) {
						$settings[ $setting ] = array();
					} else {
						$settings[ $setting ] = false;
					}
				}
			}

			return $settings;

		}

		public function ajax_get_content() {

			if ( ! function_exists( 'wc' ) ) {
				return;
			}

			$settings  = ava_smart_filters()->query->get_query_settings();

			$settings  = $this->ensure_settings( $settings );
			$widget_id = $settings['_el_widget_id'];

			unset( $settings['_el_widget_id'] );

			$data = array(
				'id'         => $widget_id,
				'elType'     => 'widget',
				'settings'   => $settings,
				'elements'   => array(),
				'widgetType' => $this->widget_name(),
			);

			add_filter( 'woocommerce_shortcode_products_query', array( $this, 'add_query_args' ), 10, 2 );
			add_action( 'woocommerce_shortcode_before_products_loop', array( $this, 'store_props' ) );

			$widget     = Elementor\Plugin::$instance->elements_manager->create_element_instance( $data );

			if ( ! $widget ) {
				throw new \Exception( 'Widget not found.' );
			}

			ob_start();
			$widget->render_content();
			$content = ob_get_clean();

			if ( $content ) {
				echo $content;
			} else {
				echo '<div class="elementor-widget-container"></div>';
			}

		}

		/**
		 * Store query ptoperties
		 *
		 * @return [type] [description]
		 */
		public function store_props() {
			global $woocommerce_loop;

			ava_smart_filters()->query->set_props(
				$this->get_id(),
				array(
					'found_posts'   => $woocommerce_loop['total'],
					'max_num_pages' => $woocommerce_loop['total_pages'],
					'page'          => $woocommerce_loop['current_page'],
				)
			);

		}

		/**
		 * Get provider wrapper selector
		 *
		 * @return string
		 */
		public function get_wrapper_selector() {
			return '.elementor-widget-woocommerce-products .elementor-widget-container';
		}

		/**
		 * Action for wrapper selector - 'insert' into it or 'replace'
		 *
		 * @return string
		 */
		public function get_wrapper_action() {
			return 'replace';
		}

		/**
		 * Set prefix for unique ID selector. Mostly is default '#' sign, but sometimes class '.' sign needed
		 *
		 * @return bool
		 */
		public function id_prefix() {
			return '#';
		}

		/**
		 * Pass args from reuest to provider
		 */
		public function apply_filters_in_request() {

			$args = ava_smart_filters()->query->get_query_args();

			if ( ! $args ) {
				return;
			}

			add_filter( 'woocommerce_shortcode_products_query', array( $this, 'add_query_args' ), 10, 2 );

		}

		/**
		 * Add custom query arguments
		 *
		 * @param array $args [description]
		 */
		public function add_query_args( $args = array(), $attributes = array() ) {

			$filter_args = ava_smart_filters()->query->get_query_args();

			if ( ! isset( $filter_args['ava_smart_filters'] ) ) {
				return $args;
			}

			if ( $filter_args['ava_smart_filters'] !== ava_smart_filters()->render->request_provider( 'raw' ) ) {
				return $args;
			}

			if ( ! ava_smart_filters()->query->is_ajax_filter() ) {

				if ( empty( $attributes['class'] ) ) {
					$query_id = 'default';
				} else {
					$query_id = $attributes['class'];
				}

				if ( $query_id !== ava_smart_filters()->render->request_provider( 'query_id' ) ) {
					return $args;
				}

			}

			if ( isset( $filter_args['no_found_rows'] ) ){
				$filter_args['no_found_rows'] = filter_var( $filter_args['no_found_rows'], FILTER_VALIDATE_BOOLEAN );
			}

			if ( isset( $filter_args['ignore_sticky_posts'] ) ){
				$filter_args['ignore_sticky_posts'] = filter_var( $filter_args['ignore_sticky_posts'], FILTER_VALIDATE_BOOLEAN );
			}

			return array_merge( $args, $filter_args );

		}

	}

}
