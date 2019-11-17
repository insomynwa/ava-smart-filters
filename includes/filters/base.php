<?php
/**
 * Provider base class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Ava_Smart_Filters_Filter_Base' ) ) {

	/**
	 * Define Ava_Smart_Filters_Filter_Base class
	 */
	abstract class Ava_Smart_Filters_Filter_Base {

		/**
		 * Get filter name
		 *
		 * @return string
		 */
		abstract public function get_name();

		/**
		 * Get filter ID
		 *
		 * @return string
		 */
		abstract public function get_id();

		/**
		 * Get filter JS files
		 *
		 * @return string
		 */
		abstract public function get_scripts();

		/**
		 * Return filter value in human-readable format
		 *
		 * @param  string $input     Filter value to format.
		 * @param  int    $filter_id Filter ID.
		 * @return string
		 */
		abstract public function get_verbosed_val( $input, $filter_id );

		/**
		 * Get filtered provider content
		 *
		 * @return string
		 */
		public function get_template() {
			return ava_smart_filters()->get_template( 'filters/' . $this->get_id() . '.php' );
		}

		/**
		 * Get filter widget file
		 *
		 * @return string
		 */
		public function widget() {
			return ava_smart_filters()->plugin_path( 'includes/widgets/' . $this->get_id() . '.php' );
		}

	}

}
