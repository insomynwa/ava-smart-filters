<?php
/**
 * Ava Smart Filters Admin class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Ava_Smart_Filters_Admin' ) ) {

	/**
	 * Define Ava_Smart_Filters_Admin class
	 */
	class Ava_Smart_Filters_Admin {

		/**
		 * Post type slug.
		 *
		 * @var string
		 */
		public $post_type = 'ava-smart-filters';

		/**
		 * Constructor for the class
		 */
		public function __construct() {

			add_action( 'admin_enqueue_scripts', array( $this, 'ava_smart_filters_admin_data' ) );
			add_action( 'wp_ajax_ava_smart_filters_admin', array( $this, 'filters_admin_action' ) );
			add_action( 'wp_ajax_nopriv_ava_smart_filters_admin', array( $this, 'filters_admin_action' ) );

		}

		/**
		 * Admin action in AJAX request
		 *
		 * @return [type] [description]
		 */
		public function filters_admin_action() {

			$tax        = ! empty( $_REQUEST['taxonomy'] ) ? $_REQUEST['taxonomy'] : false;
			$post_type  = ! empty( $_REQUEST['post_type'] ) ? $_REQUEST['post_type'] : false;
			$posts_list = '';
			$terms_list = '';

			if ( $tax ) {

				$args = array(
					'taxonomy' => $tax,
				);

				$terms = get_terms( $args );
				$terms = wp_list_pluck( $terms, 'name', 'term_id' );

				foreach ( $terms as $terms_id => $term_name ) {
					$terms_list .= '<option value="' . $terms_id . '">' . $term_name . '</option>';
				}

			}

			if ( $post_type ) {

				$args = array(
					'post_type'      => $post_type,
					'post_status'    => 'publish',
					'posts_per_page' => - 1,
				);

				$posts = get_posts( $args );

				if ( ! empty( $posts ) ) {
					$posts = wp_list_pluck( $posts, 'post_title', 'ID' );
				}

				foreach ( $posts as $post_id => $post_title ) {
					$posts_list .= '<option value="' . $post_id . '">' . $post_title . '</option>';
				}

			}

			wp_send_json( array(
				'terms' => $terms_list,
				'posts' => $posts_list,
			) );

		}


		public function ava_smart_filters_admin_data() {

			$screen = get_current_screen();

			if ( $this->post_type !== $screen->id ) {
				return;
			}

			$post_id = $this->get_post_id();
			$data_exclude_include = array();
			$data_color_image = array();

			if ( !empty( $post_id ) ){
				$data_exclude_include = get_post_meta( $_REQUEST['post'], '_data_exclude_include', true );
				$data_color_image = get_post_meta( $_REQUEST['post'], '_source_color_image_input', true );
			}

			wp_localize_script( 'ava-smart-filters',
				'AvaSmartFiltersAdminData',
				array(
					'ajaxurl'            => admin_url( 'admin-ajax.php' ),
					'dataExcludeInclude' => $data_exclude_include,
					'dataColorImage'     => $data_color_image,
				)
			);

		}

		/**
		 * Try to get current post ID from request
		 *
		 * @return [type] [description]
		 */
		public function get_post_id() {

			$post_id = isset( $_GET['post'] ) ? $_GET['post'] : false;

			if ( ! $post_id && isset( $_REQUEST['post_ID'] ) ) {
				$post_id = $_REQUEST['post_ID'];
			}

			return $post_id;

		}
	}

}
