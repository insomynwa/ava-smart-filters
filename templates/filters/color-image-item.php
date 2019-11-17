<?php
/**
 * Checkbox list item template
 */
$color           = $option['color'];
$image           = wp_get_attachment_image_src( $option['image'], $display_options['filter_image_size'] );
$image           = $image[0];
$show_label      = $display_options['show_items_label'];
$label           = $option['label'];

if ( empty( $image ) ) {
	$image = Elementor\Utils::get_placeholder_image_src();
}

?>
<div class="ava-color-image-list__row<?php echo $extra_classes; ?>">
	<label class="ava-color-image-list__item">
		<input
			type="<?php echo $filter_type; ?>"
			class="ava-color-image-list__input"
			autocomplete="off"
			name="<?php echo $query_var; ?>"
			value="<?php echo $value; ?>"
			<?php ava_smart_filters()->filter_types->control_data_atts( $args ); ?>
			<?php echo $checked; ?>
		>
		<span class="ava-color-image-list__decorator">
			<?php if ( 'color' === $type ) : ?>
				<span class="ava-color-image-list__color" style="background-color: <?php echo $color ?>"></span>
			<?php endif; ?>
			<?php if ( 'image' === $type ) : ?>
				<span class="ava-color-image-list__image"><img src="<?php echo $image; ?>" alt="<?php echo $label ?>"></span>
			<?php endif; ?>
		</span>
		<?php if ( $show_label ) : ?>
			<span class="ava-color-image-list__label"><?php echo $label; ?></span>
		<?php endif; ?>
	</label>
</div>