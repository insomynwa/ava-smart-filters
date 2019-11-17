<?php

if ( empty( $args ) ) {
	return;
}

$options   = $args['options'];
$query_var = $args['query_var'];
$checked_icon = apply_filters( 'ava-smart-filters/templates/check-range/checked-icon', 'fa fa-check' );

if ( ! $options ) {
	return;
}

$current = $this->get_current_filter_value( $args );

?>
<div class="ava-checkboxes-list" <?php $this->filter_data_atts( $args, $filter ); ?>><?php

	include ava_smart_filters()->get_template( 'common/filter-label.php' );

	echo '<div class="ava-checkboxes-list-wrapper">';

	foreach ( $options as $value => $label ) {

		$checked = '';

		if ( $current ) {

			if ( is_array( $current ) && in_array( $value, $current ) ) {
				$checked = 'checked';
			}

			if ( ! is_array( $current ) && $value === $current ) {
				$checked = 'checked';
			}

		}

		?>
		<div class="ava-checkboxes-list__row">
			<label class="ava-checkboxes-list__item">
				<input
					type="checkbox"
					class="ava-checkboxes-list__input"
					autocomplete="off"
					name="<?php echo $query_var; ?>"
					value="<?php echo $value; ?>"
					<?php $this->control_data_atts( $args ); ?>
					<?php echo $checked; ?>
				>
				<span class="ava-checkboxes-list__decorator"><i class="ava-checkboxes-list__checked-icon <?php echo $checked_icon ?>"></i></span>
				<span class="ava-checkboxes-list__label"><?php echo $label; ?></span>
			</label>
		</div>
		<?php
	}

	echo '</div>';

?></div>
