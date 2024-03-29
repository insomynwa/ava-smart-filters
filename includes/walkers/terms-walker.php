<?php
/**
 * Ava_Smart_Filters_Terms_Walker class
 *
 * @package WooCommerce/Classes/Walkers
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'Ava_Smart_Filters_Terms_Walker', false ) ) {
	return;
}

/**
 * Product cat list walker class.
 */
class Ava_Smart_Filters_Terms_Walker extends Walker {

	/**
	 * What the class handles.
	 *
	 * @var string
	 */
	public $tree_type = null;

	/**
	 * Item template path
	 *
	 * @var string
	 */
	public $item_template = null;

	/**
	 * DB fields to use.
	 *
	 * @var array
	 */
	public $db_fields = array(
		'parent' => 'parent',
		'id'     => 'term_id',
		'slug'   => 'slug',
	);

	/**
	 * Starts the list before the elements are added.
	 *
	 * @see Walker::start_lvl()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth Depth of category. Used for tab indentation.
	 * @param array  $args Will only append content if style argument value is 'list'.
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent  = str_repeat( "\t", $depth );
		$output .= "$indent<div class='ava-list-tree__children'>\n";
	}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @see Walker::end_lvl()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth Depth of category. Used for tab indentation.
	 * @param array  $args Will only append content if style argument value is 'list'.
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent  = str_repeat( "\t", $depth );
		$output .= "$indent</div>\n";
	}

	/**
	 * Start the element output.
	 *
	 * @see Walker::start_el()
	 * @since 2.1.0
	 *
	 * @param string  $output            Passed by reference. Used to append additional content.
	 * @param object  $cat               Category.
	 * @param int     $depth             Depth of category in reference to parents.
	 * @param array   $args              Arguments.
	 * @param integer $current_object_id Current object ID.
	 */
	public function start_el( &$output, $cat, $depth = 0, $args = array(), $current_object_id = 0 ) {

		$value         = $cat->term_id;
		$query_var     = $args['query_var'];
		$label         = $cat->name;
		$current       = $args['current'];
		$template      = $args['item_template'];
		$extra_classes = '';
		$checked       = '';

		if ( $this->has_children ) {
			$extra_classes = ' ava-list-tree__parent';
		}

		if ( $current ) {

			if ( is_array( $current ) && in_array( $value, $current ) ) {
				$checked = 'checked';
			}

			if ( ! is_array( $current ) && $value === $current ) {
				$checked = 'checked';
			}

		}

		ob_start();

		include $template;

		$output .= ob_get_clean();

	}

	/**
	 * Ends the element output, if needed.
	 *
	 * @see Walker::end_el()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $cat    Category.
	 * @param int    $depth  Depth of category. Not used.
	 * @param array  $args   Only uses 'list' for whether should append to output.
	 */
	public function end_el( &$output, $cat, $depth = 0, $args = array() ) {}

}