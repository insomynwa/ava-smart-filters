<?php

if ( empty( $args ) ) {
	return;
}

$query_var = $args['query_var'];
$prefix    = $args['prefix'];
$suffix    = $args['suffix'];
$current   = $this->get_current_filter_value( $args );

if ( $current ) {
	$slider_val = explode( ':', $current );
	$input_val  = $current;
} else {
	$slider_val = array( $args['min'], $args['max'] );
	$input_val  = $args['min'] . ':' . $args['max'];
}

?>
<div class="ava-range" <?php $this->filter_data_atts( $args, $filter ); ?>>
	<?php include ava_smart_filters()->get_template( 'common/filter-label.php' ); ?>
	<div
		class="ava-range__slider"
		data-defaults="<?php echo htmlspecialchars( json_encode( $slider_val ) ); ?>"
		data-min="<?php echo $args['min']; ?>"
		data-max="<?php echo $args['max']; ?>"
		data-step="<?php echo $args['step']; ?>"
		data-format="<?php echo htmlspecialchars( json_encode( $args['format'] ) ); ?>"
	></div>
	<div class="ava-range__values">
		<span class="ava-range__values-prefix"><?php
			echo $prefix;
		?></span><span class="ava-range__values-min"><?php
			echo number_format(
				$slider_val[0],
				$args['format']['decimal_num'],
				$args['format']['decimal_sep'],
				$args['format']['thousands_sep']
			);
		?></span><span class="ava-range__values-suffix"><?php
			echo $suffix;
		?></span> — <span class="ava-range__values-prefix"><?php
			echo $prefix;
		?></span><span class="ava-range__values-max"><?php
			echo number_format(
				$slider_val[1],
				$args['format']['decimal_num'],
				$args['format']['decimal_sep'],
				$args['format']['thousands_sep']
			);;
		?></span><span class="ava-range__values-suffix"><?php
			echo $suffix;
		?></span>
	</div>
	<input
		class="ava-range__input"
		type="hidden"
		autocomplete="off"
		name="<?php echo $query_var; ?>"
		value="<?php echo $input_val; ?>"
		<?php $this->control_data_atts( $args ); ?>
	>
</div>
