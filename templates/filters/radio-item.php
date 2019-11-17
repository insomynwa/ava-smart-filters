<?php
/**
 * Checkbox list item template
 */

$checked_icon = apply_filters( 'ava-smart-filters/templates/radio/checked-icon', 'fa fa-check' );

?>
<div class="ava-radio-list__row<?php echo $extra_classes; ?>">
	<label class="ava-radio-list__item">
		<input
			type="radio"
			class="ava-radio-list__input"
			autocomplete="off"
			name="<?php echo $query_var; ?>"
			value="<?php echo $value; ?>"
		<?php ava_smart_filters()->filter_types->control_data_atts( $args ); ?>
		<?php echo $checked; ?>
		>
		<span class="ava-radio-list__decorator"><i class="ava-radio-list__checked-icon <?php echo $checked_icon ?>"></i></span>
		<span class="ava-radio-list__label"><?php echo $label; ?></span>
	</label>
</div>