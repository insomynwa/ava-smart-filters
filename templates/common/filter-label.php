<?php
/**
 * Filters label template
 */

if ( isset( $args['show_label'] ) && true !== $args['show_label'] && ! empty( $args['filter_label'] ) ) {
	return;
}

?>
<div class="ava-filter-label"><?php echo $args['filter_label']; ?></div>
