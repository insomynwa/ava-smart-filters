<?php
/**
 * Filter notes template
 */
?>
<p><b>*Query Variable</b> – <?php _e( 'you need to add the meta field name by which you want to filter the data, into the field. The Query Variable is set automatically for taxonomies, search filters and filters via the post publication date.', 'ava-smart-filters' ); ?></p>
<h4><?php _e( 'Popular plugins fields', 'ava-smart-filters' ); ?></h4>
<p><b><?php _e( 'WooCommerce:', 'ava-smart-filters' ); ?></b></p>
<ul><?php
	printf( '<li><b>_price</b>: %s</li>', __( 'filter by product price;', 'ava-smart-filters' ) );
	printf( '<li><b>_wc_average_rating</b>: %s</li>', __( 'filter by product rating;', 'ava-smart-filters' ) );
	printf( '<li><b>total_sales</b>: %s</li>', __( 'filter by sales count;', 'ava-smart-filters' ) );
	printf( '<li><b>_weight</b>: %s</li>', __( 'product weight;', 'ava-smart-filters' ) );
	printf( '<li><b>_length</b>: %s</li>', __( 'product length;', 'ava-smart-filters' ) );
	printf( '<li><b>_width</b>: %s</li>', __( 'product width;', 'ava-smart-filters' ) );
	printf( '<li><b>_height</b>: %s</li>', __( 'product height;', 'ava-smart-filters' ) );
	printf( '<li><b>_sale_price_dates_from</b>: %s</li>', __( 'filter by product sale start date;', 'ava-smart-filters' ) );
	printf( '<li><b>_sale_price_dates_to</b>: %s</li>', __( 'filter by product sale end date;', 'ava-smart-filters' ) );
?></ul>