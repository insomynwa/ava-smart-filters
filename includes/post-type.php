<?php
/**
 * Class description
 *
 * @package   package_name
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Ava_Smart_Filters_Post_Type' ) ) {

	/**
	 * Define Ava_Smart_Filters_Post_Type class
	 */
	class Ava_Smart_Filters_Post_Type {

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

			add_action( 'init', array( $this, 'register_post_type' ) );
			add_action( 'admin_init', array( $this, 'init_meta' ) );

			if ( is_admin() ) {
				add_action( 'add_meta_boxes_' . $this->slug(), array( $this, 'disable_metaboxes' ), 9999 );
			}

			add_filter( 'post_row_actions', array( $this, 'remove_view_action' ), 10, 2 );

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );

		}

		/**
		 * Enqueue post type page-related assets
		 *
		 * @return [type] [description]
		 */
		public function enqueue_assets() {

			$screen = get_current_screen();

			if ( $this->post_type !== $screen->id ) {
				return;
			}

			wp_enqueue_script(
				'ava-smart-filters',
				ava_smart_filters()->plugin_url( 'assets/js/admin.js' ),
				array( 'jquery' ),
				ava_smart_filters()->get_version(),
				true
			);

			wp_enqueue_style(
				'ava-smart-filters-admin',
				ava_smart_filters()->plugin_url( 'assets/css/admin/admin.css' ),
				array(),
				ava_smart_filters()->get_version()
			);

		}

		/**
		 * Actions posts
		 *
		 * @param  [type] $actions [description]
		 * @param  [type] $post    [description]
		 * @return [type]          [description]
		 */
		public function remove_view_action( $actions, $post ) {

			if ( $this->slug() === $post->post_type ) {
				unset( $actions['view'] );
			}

			return $actions;

		}

		/**
		 * Templates post type slug
		 *
		 * @return string
		 */
		public function slug() {
			return $this->post_type;
		}

		/**
		 * Disable metaboxes from Ava Templates
		 *
		 * @return void
		 */
		public function disable_metaboxes() {
			global $wp_meta_boxes;
			unset( $wp_meta_boxes[ $this->slug() ]['side']['core']['pageparentdiv'] );
		}

		/**
		 * Register templates post type
		 *
		 * @return void
		 */
		public function register_post_type() {

			$args = array(
				'labels' => array(
					'name'               => esc_html__( 'Smart Filters', 'ava-smart-filters' ),
					'singular_name'      => esc_html__( 'Filter', 'ava-smart-filters' ),
					'add_new'            => esc_html__( 'Add New', 'ava-smart-filters' ),
					'add_new_item'       => esc_html__( 'Add New Filter', 'ava-smart-filters' ),
					'edit_item'          => esc_html__( 'Edit Filter', 'ava-smart-filters' ),
					'new_item'           => esc_html__( 'Add New Item', 'ava-smart-filters' ),
					'view_item'          => esc_html__( 'View Filter', 'ava-smart-filters' ),
					'search_items'       => esc_html__( 'Search Filter', 'ava-smart-filters' ),
					'not_found'          => esc_html__( 'No Filters Found', 'ava-smart-filters' ),
					'not_found_in_trash' => esc_html__( 'No Filters Found In Trash', 'ava-smart-filters' ),
					'menu_name'          => esc_html__( 'Smart Filters', 'ava-smart-filters' ),
				),
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_admin_bar'   => true,
				'menu_position'       => 71,
				'menu_icon'           => 'dashicons-filter',
				'show_in_nav_menus'   => false,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'has_archive'         => false,
				'query_var'           => false,
				'can_export'          => true,
				'rewrite'             => false,
				'capability_type'     => 'post',
				'supports'            => array( 'title' ),
			);

			$post_type = register_post_type(
				$this->slug(),
				apply_filters( 'ava-smart-filters/post-type/args', $args )
			);

		}

		/**
		 * Initialize filters meta
		 *
		 * @return void
		 */
		public function init_meta() {

			$filter_types = ava_smart_filters()->data->filter_types();
			$filter_types = array( 0 => __( 'Select filter type...', 'ava-smart-filters' ) ) + $filter_types;

			$meta_fields_labels = apply_filters( 'ava-smart-filters/post-type/meta-fields-labels', array(
				'_filter_label' => array(
					'title'   => __( 'Filter Label', 'ava-smart-filters' ),
					'type'    => 'text',
					'value'   => '',
					'element' => 'control',
				),
				'_active_label' => array(
					'title'   => __( 'Active Filter Label', 'ava-smart-filters' ),
					'type'    => 'text',
					'value'   => '',
					'element' => 'control',
				),
			) );

			$meta_fields_settings = apply_filters( 'ava-smart-filters/post-type/meta-fields-settings', array(
				'_filter_type' => array(
					'title'   => __( 'Filter Type', 'ava-smart-filters' ),
					'type'    => 'select',
					'element' => 'control',
					'options' => $filter_types,
				),
				'_date_source' => array(
					'title'   => __( 'Filter by', 'ava-smart-filters' ),
					'type'    => 'select',
					'element' => 'control',
					'options' => array(
						'meta_query' => __( 'Meta Date', 'ava-smart-filters' ),
						'date_query' => __( 'Post Date', 'ava-smart-filters' ),
					),
					'conditions' => array(
						'_filter_type' => 'date-range',
					),
				),
				'_data_source' => array(
					'title'   => __( 'Data Source', 'ava-smart-filters' ),
					'type'    => 'select',
					'element' => 'control',
					'options' => array(
						''              => __( 'Select data source...', 'ava-smart-filters' ),
						'manual_input'  => __( 'Manual Input', 'ava-smart-filters' ),
						'taxonomies'    => __( 'Taxonomies', 'ava-smart-filters' ),
						'posts'         => __( 'Posts', 'ava-smart-filters' ),
						'custom_fields' => __( 'Custom Fields', 'ava-smart-filters' ),
					),
					'conditions' => array(
						'_filter_type' => array( 'checkboxes', 'select', 'radio', 'color-image' ),
					),
				),
				'_is_custom_checkbox' => array(
					'title'   => __( 'Is Checkbox Meta Field (Ava Engine)', 'ava-smart-filters' ),
					'description' => __( 'This option should to be enabled if you need to filter data from Checkbox meta fields type, created with AvaEngine plugin.', 'ava-smart-filters' ),
					'type'    => 'switcher',
					'element' => 'control',
					'conditions' => array(
						'_filter_type' => array( 'checkboxes', 'select', 'radio', 'color-image' ),
					),
				),
				'_rating_options' => array(
					'title'      => __( 'Stars count', 'ava-smart-filters' ),
					'type'       => 'stepper',
					'element'    => 'control',
					'value'       => 5,
					'max_value'   => 10,
					'min_value'   => 1,
					'step_value'  => 1,
					'conditions' => array(
						'_filter_type'   => array( 'rating' ),
					),
				),
				'_rating_compare_operand' => array(
					'title'       => __( 'Inequality operator', 'ava-smart-filters' ),
					'description' => __( 'Set relation between values', 'ava-smart-filters' ),
					'type'        => 'select',
					'options'     => array(
						'greater' => __( 'Greater than or equals (>=)', 'ava-smart-filters' ),
						'less'    => __( 'Less than or equals (<=)', 'ava-smart-filters' ),
						'equal'   => __( 'Equals (=)', 'ava-smart-filters' ),
					),
					'element'     => 'control',
					'conditions'  => array(
						'_filter_type' => array( 'rating' ),
					),
				),
				'_source_taxonomy' => array(
					'title'            => __( 'Taxonomy', 'ava-smart-filters' ),
					'type'             => 'select',
					'element'          => 'control',
					'options_callback' => array( $this, 'get_taxonomies_for_options' ),
					'conditions'       => array(
						'_filter_type' => array( 'checkboxes', 'select', 'radio', 'color-image' ),
						'_data_source' => 'taxonomies',
					),
				),
				'_source_post_type' => array(
					'title'            => __( 'Post Type', 'ava-smart-filters' ),
					'type'             => 'select',
					'element'          => 'control',
					'options_callback' => array( $this, 'get_post_types_for_options' ),
					'conditions'       => array(
						'_filter_type' => array( 'checkboxes', 'select', 'radio', 'color-image' ),
						'_data_source' => 'posts',
					),
				),
				'_only_child' => array(
					'title'   => __( 'Show only childs of current term', 'ava-smart-filters' ),
					'type'    => 'switcher',
					'element' => 'control',
					'conditions' => array(
						'_filter_type' => array( 'checkboxes', 'select', 'radio', 'color-image' ),
						'_data_source' => 'taxonomies',
					),
				),
				'_group_by_parent' => array(
					'title'   => __( 'Group terms by parents', 'ava-smart-filters' ),
					'type'    => 'switcher',
					'element' => 'control',
					'conditions' => array(
						'_filter_type' => array( 'checkboxes', 'radio' ),
						'_data_source' => 'taxonomies',
					),
				),
				'_source_custom_field' => array(
					'title'   => __( 'Custom Field Key', 'ava-smart-filters' ),
					'type'    => 'text',
					'element' => 'control',
					'conditions' => array(
						'_filter_type' => array( 'checkboxes', 'select', 'radio', 'color-image' ),
						'_data_source' => 'custom_fields',
					),
				),
				'_source_manual_input' => array(
					'title'       => __( 'Options List', 'ava-smart-filters' ),
					'element'     => 'control',
					'type'        => 'repeater',
					'add_label'   => __( 'New Option', 'ava-smart-filters' ),
					'title_field' => 'label',
					'fields'      => array(
						'value' => array(
							'type'  => 'text',
							'id'    => 'value',
							'name'  => 'value',
							'label' => __( 'Value', 'ava-smart-filters' ),
						),
						'label' => array(
							'type'  => 'text',
							'id'    => 'label',
							'name'  => 'label',
							'label' => __( 'Label', 'ava-smart-filters' ),
						),
					),
					'conditions' => array(
						'_filter_type' => array( 'checkboxes', 'select', 'radio' ),
						'_data_source' => 'manual_input',
					),
				),
				'_color_image_type' => array(
					'title'      => __( 'Type', 'ava-smart-filters' ),
					'type'       => 'select',
					'options'    => array(
						0       => __( 'Choose Type', 'ava-smart-filters' ),
						'color' => __( 'Color', 'ava-smart-filters' ),
						'image' => __( 'Image', 'ava-smart-filters' ),
					),
					'element'    => 'control',
					'conditions' => array(
						'_filter_type' => array( 'color-image' ),
						'_data_source' => array( 'taxonomies', 'posts', 'custom_fields', 'manual_input' ),
					),
				),
				'_color_image_behavior' => array(
					'title'      => __( 'Behavior', 'ava-smart-filters' ),
					'type'       => 'select',
					'options'    => array(
						'checkbox' => __( 'Checkbox', 'ava-smart-filters' ),
						'radio'    => __( 'Radio', 'ava-smart-filters' ),
					),
					'element'    => 'control',
					'conditions' => array(
						'_filter_type' => array( 'color-image' ),
						'_data_source' => array( 'taxonomies', 'posts', 'custom_fields', 'manual_input' ),
					),
				),
				'_source_color_image_input' => array(
					'title'       => __( 'Options List', 'ava-smart-filters' ),
					'element'     => 'control',
					'type'        => 'repeater',
					'add_label'   => __( 'New Option', 'ava-smart-filters' ),
					'title_field' => 'label',
					'class'       => 'ava-smart-filters-color-image',
					'fields'      => array(
						'label' => array(
							'type'  => 'text',
							'id'    => 'label',
							'name'  => 'label',
							'label' => __( 'Label', 'ava-smart-filters' ),
							'class' => 'color-image-type-control label-control',
						),
						'value' => array(
							'type'  => 'text',
							'id'    => 'value',
							'name'  => 'value',
							'label' => __( 'Value', 'ava-smart-filters' ),
							'class' => 'color-image-type-control value-control',
						),
						'selected_value' => array(
							'type'    => 'select',
							'id'      => 'selected_value',
							'name'    => 'selected_value',
							'options' => array(),
							'label'   => __( 'Value', 'ava-smart-filters' ),
							'class'   => 'color-image-type-control selected-value-control',
						),
						'source_color' => array(
							'type'  => 'colorpicker',
							'id'    => 'source_color',
							'name'  => 'source_color',
							'label' => __( 'Color', 'ava-smart-filters' ),
							'class' => 'color-image-type-control color-control',
						),
						'source_image' => array(
							'type'         => 'media',
							'id'           => 'source_image',
							'name'         => 'source_image',
							'multi_upload' => false,
							'library_type' => 'image',
							'label'        => __( 'Image', 'ava-smart-filters' ),
							'class'        => 'color-image-type-control image-control',
						),
					),
					'conditions' => array(
						'_filter_type'      => array( 'color-image' ),
						'_data_source'      => array( 'taxonomies', 'posts', 'custom_fields', 'manual_input' ),
						'_color_image_type' => array( 'color', 'image' ),
					),
				),
				'_source_manual_input_range' => array(
					'title'       => __( 'Options List', 'ava-smart-filters' ),
					'element'     => 'control',
					'type'        => 'repeater',
					'add_label'   => __( 'New Option', 'ava-smart-filters' ),
					'title_field' => 'label',
					'fields'      => array(
						'min' => array(
							'type'  => 'text',
							'id'    => 'min',
							'name'  => 'min',
							'label' => __( 'Min Value', 'ava-smart-filters' ),
						),
						'max' => array(
							'type'  => 'text',
							'id'    => 'max',
							'name'  => 'max',
							'label' => __( 'Max Value', 'ava-smart-filters' ),
						),
					),
					'conditions' => array(
						'_filter_type' => 'check-range',
					),
				),
				'_placeholder' => array(
					'title'   => __( 'Placeholder', 'ava-smart-filters' ),
					'type'    => 'text',
					'value'   => __( 'Select...', 'ava-smart-filters' ),
					'element' => 'control',
					'conditions' => array(
						'_filter_type' => 'select',
					),
				),
				'_s_placeholder' => array(
					'title'   => __( 'Placeholder', 'ava-smart-filters' ),
					'type'    => 'text',
					'value'   => __( 'Search...', 'ava-smart-filters' ),
					'element' => 'control',
					'conditions' => array(
						'_filter_type' => 'search',
					),
				),
				'_s_by' => array(
					'title'   => __( 'Search by', 'ava-smart-filters' ),
					'type'    => 'select',
					'element' => 'control',
					'options' => array(
						'default' => __( 'Default WordPress search', 'ava-smart-filters' ),
						'meta'    => __( 'By Custom Field (from Query Variable)', 'ava-smart-filters' ),
					),
					'conditions' => array(
						'_filter_type' => 'search',
					),
				),
				'_date_from_placeholder' => array(
					'title'   => __( 'From Placeholder', 'ava-smart-filters' ),
					'type'    => 'text',
					'value'   => '',
					'element' => 'control',
					'conditions' => array(
						'_filter_type' => 'date-range',
					),
				),
				'_date_to_placeholder' => array(
					'title'   => __( 'To Placeholder', 'ava-smart-filters' ),
					'type'    => 'text',
					'value'   => '',
					'element' => 'control',
					'conditions' => array(
						'_filter_type' => 'date-range',
					),
				),
				'_values_prefix' => array(
					'title'   => __( 'Values prefix', 'ava-smart-filters' ),
					'type'    => 'text',
					'value'   => '',
					'element' => 'control',
					'conditions' => array(
						'_filter_type' => array( 'range', 'check-range' ),
					),
				),
				'_values_suffix' => array(
					'title'   => __( 'Values suffix', 'ava-smart-filters' ),
					'type'    => 'text',
					'value'   => '',
					'element' => 'control',
					'conditions' => array(
						'_filter_type' => array( 'range', 'check-range' ),
					),
				),
				'_values_thousand_sep' => array(
					'title'      => __( 'Thousands separator', 'ava-smart-filters' ),
					'type'       => 'text',
					'description' => __( 'Use &amp;nbsp; for space', 'ava-smart-filters' ),
					'value'      => '',
					'element'    => 'control',
					'conditions' => array(
						'_filter_type' => array( 'range', 'check-range' ),
					),
				),
				'_values_decimal_sep' => array(
					'title'   => __( 'Decimal separator', 'ava-smart-filters' ),
					'type'    => 'text',
					'value'   => '.',
					'element' => 'control',
					'conditions' => array(
						'_filter_type' => array( 'range', 'check-range' ),
					),
				),
				'_values_decimal_num' => array(
					'title'      => __( 'Number of decimals', 'ava-smart-filters' ),
					'type'       => 'text',
					'value'      => 0,
					'element'    => 'control',
					'conditions' => array(
						'_filter_type' => array( 'range', 'check-range' ),
					),
				),
				'_source_min' => array(
					'title'   => __( 'Min Value', 'ava-smart-filters' ),
					'type'    => 'text',
					'element' => 'control',
					'conditions' => array(
						'_filter_type' => 'range',
					),
				),
				'_source_max' => array(
					'title'   => __( 'Max Value', 'ava-smart-filters' ),
					'type'    => 'text',
					'element' => 'control',
					'conditions' => array(
						'_filter_type' => 'range',
					),
				),
				'_source_step' => array(
					'title'             => __( 'Step', 'ava-smart-filters' ),
					'type'              => 'text',
					'element'           => 'control',
					'default'           => 1,
					'sanitize_callback' => array( $this, 'sanitize_range_step' ),
					'description'       => __( '1, 10, 100, 0.1 etc', 'ava-smart-filters' ),
					'conditions'        => array(
						'_filter_type' => 'range',
					),
				),
				'_source_callback' => array(
					'title'   => __( 'Get min/max dynamically', 'ava-smart-filters' ),
					'type'    => 'select',
					'options' => apply_filters( 'ava-smart-filters/range/source-callbacks', array(
						0                              => __( 'Select...', 'ava-smart-filters' ),
						'ava_smart_filters_woo_prices' => __( 'WooCommerce min/max prices', 'ava-smart-filters' ),
					) ),
					'element' => 'control',
					'conditions' => array(
						'_filter_type' => 'range',
					),
				),
				'_use_exclude_include' => array(
					'title'   => __( 'Exclude/Include', 'ava-smart-filters' ),
					'type'    => 'select',
					'options' => array(
						0         => __( 'None', 'ava-smart-filters' ),
						'exclude' => __( 'Exclude', 'ava-smart-filters' ),
						'include' => __( 'Include', 'ava-smart-filters' ),
					),
					'element' => 'control',
					'conditions' => array(
						'_filter_type' => array( 'checkboxes', 'select', 'radio' ),
						'_data_source' => array( 'taxonomies', 'posts' ),
					),
				),
				'_data_exclude_include' => array(
					'title'   => __( 'Exclude Or Include Items', 'ava-smart-filters' ),
					'type'    => 'select',
					'element' => 'control',
					'multiple' => true,
					'options' => array(
						'' => '',
					),
					'conditions' => array(
						'_filter_type' => array( 'checkboxes', 'select', 'radio' ),
						'_data_source' => array( 'taxonomies', 'posts' ),
						'_use_exclude_include' => array( 'exclude', 'include' )
					),
				),
			) );

			$meta_query_settings = apply_filters( 'ava-smart-filters/post-type/meta-query-settings', array(
				'_query_var' => array(
					'title'       => __( 'Query Variable *', 'ava-smart-filters' ),
					'type'        => 'text',
					'description' => __( 'Set queried field key.', 'ava-smart-filters' ),
					'element'     => 'control',
					'required'    => true,
				),
			) );

			new Cherry_X_Post_Meta( array(
				'id'            => 'filter-labels',
				'title'         => __( 'Filter Labels', 'ava-smart-filters' ),
				'page'          => array( $this->slug() ),
				'context'       => 'normal',
				'priority'      => 'high',
				'callback_args' => false,
				'builder_cb'    => array( $this, 'get_builder' ),
				'fields'        => $meta_fields_labels,
			) );

			new Cherry_X_Post_Meta( array(
				'id'            => 'filter-settings',
				'title'         => __( 'Filter Settings', 'ava-smart-filters' ),
				'page'          => array( $this->slug() ),
				'context'       => 'normal',
				'priority'      => 'high',
				'callback_args' => false,
				'builder_cb'    => array( $this, 'get_builder' ),
				'fields'        => $meta_fields_settings,
			) );

			new Cherry_X_Post_Meta( array(
				'id'            => 'query-settings',
				'title'         => 'Query Settings',
				'page'          => array( $this->slug() ),
				'context'       => 'normal',
				'priority'      => 'high',
				'callback_args' => false,
				'builder_cb'    => array( $this, 'get_builder' ),
				'fields'        => $meta_query_settings,
			) );

			ob_start();
			include ava_smart_filters()->get_template( 'admin/filter-notes.php' );
			$filter_notes = ob_get_clean();

			new Cherry_X_Post_Meta( array(
				'id'            => 'filter-notes',
				'title'         => __( 'Notes', 'ava-smart-filters' ),
				'page'          => array( $this->slug() ),
				'context'       => 'normal',
				'priority'      => 'high',
				'callback_args' => false,
				'builder_cb'    => array( $this, 'get_builder' ),
				'fields'        => array(
					'license' => array(
						'type'   => 'html',
						'class'  => 'cx-component',
						'html'   => $filter_notes,
					),
				),
			) );

		}

		/**
		 * Santize range step before save
		 *
		 * @param  [type] $input [description]
		 * @return [type]        [description]
		 */
		public function sanitize_range_step( $input ) {
			return trim( str_replace( ',', '.', $input ) );
		}

		/**
		 * Get taxonomies list for options.
		 *
		 * @return array
		 */
		public function get_taxonomies_for_options() {

			$args       = array();
			$taxonomies = get_taxonomies( $args, 'objects', 'and' );

			return wp_list_pluck( $taxonomies, 'label', 'name' );
		}

		/**
		 * Returns post types list for options
		 *
		 * @return array
		 */
		public function get_post_types_for_options() {

			$args = array(
				'public' => true,
			);

			$post_types = get_post_types( $args, 'objects', 'and' );
			$post_types = wp_list_pluck( $post_types, 'label', 'name' );

			if ( isset( $post_types[ $this->slug() ] ) ) {
				unset( $post_types[ $this->slug() ] );
			}

			return $post_types;
		}

		/**
		 * Return UI builder instance
		 *
		 * @return [type] [description]
		 */
		public function get_builder() {

			$data = ava_smart_filters()->framework->get_included_module_data( 'cherry-x-interface-builder.php' );

			return new CX_Interface_Builder(
				array(
					'path' => $data['path'],
					'url'  => $data['url'],
				)
			);

		}

	}

}
