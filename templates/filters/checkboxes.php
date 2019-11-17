<?php

if ( empty( $args ) ) {
	return;
}

$options       = $args['options'];
$query_var     = $args['query_var'];
$by_parents    = $args['by_parents'];
$extra_classes = '';

if ( ! $options ) {
	return;
}

$current = $this->get_current_filter_value( $args );

?>
<div class="ava-checkboxes-list" <?php $this->filter_data_atts( $args, $filter ); ?>><?php

	include ava_smart_filters()->get_template( 'common/filter-label.php' );

	echo '<div class="ava-checkboxes-list-wrapper">';
	if ( $by_parents ) {

		if ( ! class_exists( 'Ava_Smart_Filters_Terms_Walker' ) ) {
			require_once ava_smart_filters()->plugin_path( 'includes/walkers/terms-walker.php' );
		}

		$walker = new Ava_Smart_Filters_Terms_Walker();

		$walker->tree_type = $query_var;

		$args['item_template'] = ava_smart_filters()->get_template( 'filters/checkboxes-item.php' );
		$args['current']       = $current;

		echo '<div class="ava-list-tree">';
		echo $walker->walk( $options, 0, $args );
		echo '</div>';

	} else {

		foreach ( $options as $value => $label ) {

			$checked = '';

			if ( $current ) {

				if ( is_array( $current ) && in_array( $value, $current ) ) {
					$checked = 'checked';
				}

				if ( ! is_array( $current ) && $value == $current ) {
					$checked = 'checked';
				}

			}

			include ava_smart_filters()->get_template( 'filters/checkboxes-item.php' );

		}

	}
	echo '</div>';

	?></div>
