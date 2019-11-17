<?php
/**
 * Compatibility filters and actions
 */

// WPML and Woo compatibility
add_filter( 'wcml_multi_currency_ajax_actions', 'ava_smart_filters_add_action_to_multi_currency_ajax', 10, 1 );

function ava_smart_filters_add_action_to_multi_currency_ajax( $ajax_actions = array() ) {

	$ajax_actions[] = 'ava_smart_filters';

	return $ajax_actions;
}

add_filter( 'ava-smart-filters/render_filter_template/filter_id', 'ava_smart_filters_modify_filter_id' );

function ava_smart_filters_modify_filter_id( $filter_id ) {

	// WPML String Translation plugin exist check
	if ( defined( 'WPML_ST_VERSION' ) ) {
		return apply_filters( 'wpml_object_id', $filter_id, ava_smart_filters()->post_type->post_type, true );
	}

	return $filter_id;
}
add_filter( 'ava-smart-filters/filters/localized-data', 'ava_smart_filters_datepicker_texts' );

function ava_smart_filters_datepicker_texts( $args ) {

	 $args['datePickerData'] = array(
		'closeText'       => esc_html__( 'Done', 'ava-smart-filters' ),
		'prevText'        => esc_html__( 'Prev', 'ava-smart-filters' ),
		'nextText'        => esc_html__( 'Next', 'ava-smart-filters' ),
		'currentText'     => esc_html__( 'Today', 'ava-smart-filters' ),
		'monthNames'      => array(
			esc_html__( 'January', 'ava-smart-filters' ),
			esc_html__( 'February', 'ava-smart-filters' ),
			esc_html__( 'March', 'ava-smart-filters' ),
			esc_html__( 'April', 'ava-smart-filters' ),
			esc_html__( 'May', 'ava-smart-filters' ),
			esc_html__( 'June', 'ava-smart-filters' ),
			esc_html__( 'July', 'ava-smart-filters' ),
			esc_html__( 'August', 'ava-smart-filters' ),
			esc_html__( 'September', 'ava-smart-filters' ),
			esc_html__( 'October', 'ava-smart-filters' ),
			esc_html__( 'November', 'ava-smart-filters' ),
			esc_html__( 'December', 'ava-smart-filters' ),
		),
		'monthNamesShort' => array(
			esc_html__( 'Jan', 'ava-smart-filters' ),
			esc_html__( 'Feb', 'ava-smart-filters' ),
			esc_html__( 'Mar', 'ava-smart-filters' ),
			esc_html__( 'Apr', 'ava-smart-filters' ),
			esc_html__( 'May', 'ava-smart-filters' ),
			esc_html__( 'Jun', 'ava-smart-filters' ),
			esc_html__( 'Jul', 'ava-smart-filters' ),
			esc_html__( 'Aug', 'ava-smart-filters' ),
			esc_html__( 'Sep', 'ava-smart-filters' ),
			esc_html__( 'Oct', 'ava-smart-filters' ),
			esc_html__( 'Nov', 'ava-smart-filters' ),
			esc_html__( 'Dec', 'ava-smart-filters' ),
		),
		'dayNames'        => array(
			esc_html__( 'Sunday', 'ava-smart-filters' ),
			esc_html__( 'Monday', 'ava-smart-filters' ),
			esc_html__( 'Tuesday', 'ava-smart-filters' ),
			esc_html__( 'Wednesday', 'ava-smart-filters' ),
			esc_html__( 'Thursday', 'ava-smart-filters' ),
			esc_html__( 'Friday', 'ava-smart-filters' ),
			esc_html__( 'Saturday', 'ava-smart-filters' )
		),
		'dayNamesShort'   => array( "Sun",
			esc_html__( 'Mon', 'ava-smart-filters' ),
			esc_html__( 'Tue', 'ava-smart-filters' ),
			esc_html__( 'Wed', 'ava-smart-filters' ),
			esc_html__( 'Thu', 'ava-smart-filters' ),
			esc_html__( 'Fri', 'ava-smart-filters' ),
			esc_html__( 'Sat', 'ava-smart-filters' )
		),
		'dayNamesMin'     => array(
			esc_html__( 'Su', 'ava-smart-filters' ),
			esc_html__( 'Mo', 'ava-smart-filters' ),
			esc_html__( 'Tu', 'ava-smart-filters' ),
			esc_html__( 'We', 'ava-smart-filters' ),
			esc_html__( 'Th', 'ava-smart-filters' ),
			esc_html__( 'Fr', 'ava-smart-filters' ),
			esc_html__( 'Sa', 'ava-smart-filters' ),
		),
		'weekHeader'      => esc_html__( 'Wk', 'ava-smart-filters' ),
	);

	 return $args;
}