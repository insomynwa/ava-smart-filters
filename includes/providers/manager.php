<?php
/**
 * Data class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Ava_Smart_Filters_Providers_Manager' ) ) {

	/**
	 * Define Ava_Smart_Filters_Providers_Manager class
	 */
	class Ava_Smart_Filters_Providers_Manager {

		private $_providers = array();
		private $_provider_settings = array();

		/**
		 * Constructor for the class
		 */
		public function __construct() {
			$this->register_providers();
		}

		/**
		 * Store provider settings
		 * @return [type] [description]
		 */
		public function store_provider_settings( $provider_id, $settings, $query_id = false ) {

			if ( ! $query_id ) {
				$query_id = 'default';
			}

			if ( empty( $this->_provider_settings[ $provider_id ] ) ) {
				$this->_provider_settings[ $provider_id ] = array();
			}

			if ( ! empty( $this->_provider_settings[ $provider_id ][ $query_id ] ) ) {
				return;
			}

			$this->_provider_settings[ $provider_id ][ $query_id ] = $settings;

		}

		/**
		 * Store provider settings
		 * @return [type] [description]
		 */
		public function add_provider_settings( $provider_id, $settings, $query_id = 'default' ) {

			if ( empty( $this->_provider_settings[ $provider_id ] ) ) {
				$all_settings = array();
			} else {
				$all_settings = $this->_provider_settings[ $provider_id ];
			}

			if ( empty( $all_settings[ $query_id ] ) ) {
				$all_settings[ $query_id ] = array();
			}

			$all_settings[ $query_id ] = array_merge( $all_settings[ $query_id ], $settings );

			$this->_provider_settings[ $provider_id ] = $all_settings;

		}

		/**
		 * Returns all provider settings
		 *
		 * @return [type] [description]
		 */
		public function get_provider_settings() {
			return $this->_provider_settings;
		}

		/**
		 * Register all providers.
		 *
		 * @return void
		 */
		public function register_providers() {

			$base_path = ava_smart_filters()->plugin_path( 'includes/providers/' );
			$default_providers = array();
			$available_providers = ava_smart_filters()->settings->get( 'avaliable_providers' );

			foreach ( glob( $base_path . '*.php' ) as $file ) {
				$data = get_file_data( $file, array( 'class'=>'Class', 'name' => 'Name', 'slug'=>'Slug' ) );
				$class = $data['class'];
				$name = $data['name'];

				if( $name ){
					$enabled = isset( $available_providers[ $class ] ) ? $available_providers[ $class ] : '';
					if( filter_var( $enabled, FILTER_VALIDATE_BOOLEAN ) || ! $available_providers ){
						$default_providers[ $data['class'] ] = $file;
					}
				}

			}

			require $base_path . 'base.php';

			foreach ( $default_providers as $provider_class => $provider_file ) {
				$this->register_provider( $provider_class, $provider_file );
			}

			/**
			 * Register custom providers on this hook
			 */
			do_action( 'ava-smart-filters/providers/register', $this );

		}

		/**
		 * Register new provider.
		 *
		 * @param  string $provider_class Provider class name.
		 * @param  string $provider_file  Path to file with provider class.
		 * @return void
		 */
		public function register_provider( $provider_class, $provider_file ) {

			if ( ! file_exists( $provider_file ) ) {
				return;
			}

			require $provider_file;

			if ( class_exists( $provider_class ) ) {
				$instance = new $provider_class();
				$this->_providers[ $instance->get_id() ] = $instance;
			}

		}

		/**
		 * Return all providers list or specific provider by ID
		 *
		 * @param  string $provider optional, provider ID.
		 * @return array|provider object|false
		 */
		public function get_providers( $provider = null ) {

			if ( $provider ) {
				return isset( $this->_providers[ $provider ] ) ? $this->_providers[ $provider ] : false;
			}

			return $this->_providers;
		}

	}

}