<?php
/**
 * Class: Ava_Smart_Filters_Provider_EPro_Posts
 * Name: Elementor Avator Posts
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Ava_Smart_Filters_Provider_EPro_Posts' ) ) {

	/**
	 * Define Ava_Smart_Filters_Provider_EPro_Posts class
	 */
	class Ava_Smart_Filters_Provider_EPro_Posts extends Ava_Smart_Filters_Provider_Base {

		/**
		 * Watch for default query
		 */
		public function __construct() {

			if ( ! ava_smart_filters()->query->is_ajax_filter() ) {

				if ( defined( 'ELEMENTOR_PRO_VERSION' ) && version_compare( ELEMENTOR_PRO_VERSION, '2.5.0', '>=' ) ) {
					add_action(
						'elementor/query/ava-smart-filters',
						array( $this, 'posts_store_default_query' ),
						0, 2
					);
				} else {
					add_action(
						'elementor_pro/posts/query/ava-smart-filters',
						array( $this, 'posts_store_default_query' ),
						0, 2
					);
				}

				add_action( 'elementor/widget/before_render_content', array( $this, 'store_default_settings' ), 0 );

			}

		}

		/**
		 * Hook apply query function
		 *
		 * @return [type] [description]
		 */
		public function hook_apply_query() {
			if ( defined( 'ELEMENTOR_PRO_VERSION' ) && version_compare( ELEMENTOR_PRO_VERSION, '2.5.0', '>=' ) ) {
				add_action( 'elementor/query/ava-smart-filters', array( $this, 'posts_add_query_args' ), 0, 2 );
			} else {
				add_action( 'elementor_pro/posts/query/ava-smart-filters', array( $this, 'posts_add_query_args' ), 0, 2 );
			}
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
		 * Returns Elementor Avator apropriate widget name
		 * @return [type] [description]
		 */
		public function widget_name() {
			return 'posts';
		}

		/**
		 * Save default query
		 *
		 * @param  [type] $wp_query [description]
		 * @return [type]        [description]
		 */
		public function posts_store_default_query( $wp_query, $widget ) {

			$settings = $widget->get_settings();

			if ( ! empty( $settings['_element_id'] ) ) {
				$query_id = $settings['_element_id'];
			} else {
				$query_id = 'default';
			}

			$wp_query->set( 'ava_smart_filters', $this->get_id() . '/' . $query_id );

			ava_smart_filters()->query->store_provider_default_query( $this->get_id(), array(
				'post_type'      => $wp_query->get( 'post_type' ),
				'paged'          => $wp_query->get( 'paged' ),
				'posts_per_page' => $wp_query->get( 'posts_per_page' ),
			), $query_id );

			$query['ava_smart_filters'] = ava_smart_filters()->query->encode_provider_data(
				$this->get_id(),
				$query_id
			);

		}

		/**
		 * Get provider name
		 *
		 * @return string
		 */
		public function get_name() {
			return __( 'Elementor Avator Posts', 'ava-smart-filters' );
		}

		/**
		 * Get provider ID
		 *
		 * @return string
		 */
		public function get_id() {
			return 'epro-posts';
		}

		/**
		 * Get provider wrapper selector
		 *
		 * @return string
		 */
		public function get_wrapper_selector() {
			return '.elementor-widget-posts .elementor-widget-container';
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
		 * If added unique ID this paramter will determine - search selector inside this ID, or is the same element
		 *
		 * @return bool
		 */
		public function in_depth() {
			return false;
		}

		/**
		 * Returns settings to store list
		 * @return [type] [description]
		 */
		public function settings_to_store() {
			return array(
				'_skin',
				'classic_show_excerpt',
				'classic_meta_separator',
				'classic_read_more_text',
				'cards_meta_separator',
				'cards_read_more_text',
				'classic_columns',
				'classic_columns_tablet',
				'classic_columns_mobile',
				'classic_posts_per_page',
				'classic_thumbnail',
				'classic_masonry',
				'classic_thumbnail_size_size',
				'classic_item_ratio',
				'classic_item_ratio_tablet',
				'classic_item_ratio_mobile',
				'classic_image_width',
				'classic_image_width_tablet',
				'classic_image_width_mobile',
				'classic_show_title',
				'classic_title_tag',
				'classic_excerpt_length',
				'classic_meta_data',
				'classic_show_read_more',
				'cards_columns',
				'cards_columns_tablet',
				'cards_columns_mobile',
				'cards_posts_per_page',
				'cards_thumbnail',
				'cards_masonry',
				'cards_thumbnail_size_size',
				'cards_item_ratio',
				'cards_item_ratio_tablet',
				'cards_item_ratio_mobile',
				'cards_show_title',
				'cards_title_tag',
				'cards_show_excerpt',
				'cards_excerpt_length',
				'cards_meta_data',
				'cards_show_read_more',
				'cards_show_badge',
				'cards_badge_taxonomy',
				'cards_show_avatar',
				'pagination_type',
				'pagination_numbers_shorten',
				'pagination_page_limit',
				'pagination_prev_label',
				'pagination_next_label',
				'nothing_found_message',
				'posts_post_type',
				'posts_posts_ids',
				'posts_include_term_ids',
				'posts_include_authors',
				'posts_related_taxonomies',
				'posts_include',
				'posts_exclude',
				'posts_exclude_ids',
				'posts_exclude_term_ids',
				'posts_exclude_authors',
				'posts_avoid_duplicates',
				'posts_authors',
				'posts_category_ids',
				'posts_post_tag_ids',
				'posts_post_format_ids',
				'orderby',
				'order',
				'offset',
				'exclude',
				'exclude_ids',
				'avoid_duplicates',
				'posts_query_id',
				'posts_offset',
				'posts_related_fallback',
				'posts_fallback_ids',
				'posts_select_date',
				'posts_date_before',
				'posts_date_after',
				'posts_orderby',
				'posts_order',
				'posts_ignore_sticky_posts',
				'custom_skin_template'
			);
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

		/**
		 * Get filtered provider content
		 *
		 * @return string
		 */
		public function ajax_get_content() {

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

			$this->hook_apply_query();

			$attributes = ava_smart_filters()->query->get_query_settings();
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
		 * Pass args from reuest to provider
		 */
		public function apply_filters_in_request() {

			$args = ava_smart_filters()->query->get_query_args();

			if ( ! $args ) {
				return;
			}

			$this->hook_apply_query();

		}

		/**
		 * Add custom query arguments
		 *
		 * @param array $args [description]
		 */
		public function posts_add_query_args( $wp_query, $widget ) {

			$settings = $widget->get_settings();

			if ( ! empty( $settings['_element_id'] ) ) {
				$query_id = $settings['_element_id'];
			} else {
				$query_id = 'default';
			}

			$wp_query->set( 'ava_smart_filters', $this->get_id() . '/' . $query_id );

			foreach ( ava_smart_filters()->query->get_query_args() as $query_var => $value ) {
				$wp_query->set( $query_var, $value );
			}

		}

	}

}
