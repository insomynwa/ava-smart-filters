<?php

if ( empty( $args ) ) {
	return;
}

$options   = $args['options'];
$widget_id = $args['__widget_id'];
$query_var = $args['query_var'];

if ( ! $options ) {
	return;
}

$current = $this->get_current_filter_value( $args );

?>
<div class="ava-rating" <?php $this->filter_data_atts( $args, $filter ); ?>>
	<?php include ava_smart_filters()->get_template( 'common/filter-label.php' ); ?>
	<div class="ava-rating__control">
		<div class="ava-rating-stars">
			<fieldset class="ava-rating-stars__fields">
		  <?php

		  $options = array_reverse( $options );

		  foreach ( $options as $key => $value ) {

			  $checked = '';

			  if ( $current ) {

				  if ( is_array( $current ) && in_array( $value, $current ) ) {
					  $checked = ' checked';
				  }

				  if ( ! is_array( $current ) && $value == $current ) {
					  $checked = ' checked';
				  }

			  }

			  ?>
			  <input
				  class="ava-rating-star__input"
				  type="radio"
				  id="ava-rating-<?php echo $widget_id . '-' . $value ?>"
				  autocomplete="off"
				  name="<?php echo $query_var; ?>"
				  <?php ava_smart_filters()->filter_types->control_data_atts( $args ); ?>
				  value="<?php echo $value; ?>"
				  <?php echo $checked; ?>
			  />
			  <label class="ava-rating-star__label" for="ava-rating-<?php echo $widget_id . '-' . $value ?>"><span class="ava-rating-star__icon"><?php echo $args['rating_icon']; ?></span></label>
		    <?php
		  }

		  ?>
			</fieldset>
		</div>
	</div>
</div>
