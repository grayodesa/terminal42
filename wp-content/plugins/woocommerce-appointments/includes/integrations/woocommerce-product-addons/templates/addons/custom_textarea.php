<?php foreach ( $addon['options'] as $key => $option ) :
	$addon_key     = 'addon-' . sanitize_title( $addon['field-name'] );
	$option_key    = empty( $option['label'] ) ? $key : sanitize_title( $option['label'] );
	$current_value = isset( $_POST[ $addon_key ] ) && isset( $_POST[ $addon_key ][ $option_key ] ) ? wc_clean( $_POST[ $addon_key ][ $option_key ] ) : '';
	$price         = $option['price'] > 0 ? '<span class="amount-symbol">+</span>' . wc_price( get_product_addon_price_for_display( $option['price'] ) ) . '' : '';
	$duration	   = absint( $option['duration'] ) > 0 ? '<span class="addon-duration"> + ' . wc_appointment_convert_to_hours_and_minutes( absint( $option['duration'] ) ) . '</span>' : '';
	?>

	<p class="form-row form-row-wide addon-wrap-<?php echo sanitize_title( $addon['field-name'] ); ?>">
		<?php if ( ! empty( $option['label'] ) ) : ?>
			<label><?php echo wptexturize( $option['label'] ) . ' ' . $price . $duration; ?></label>
		<?php endif; ?>
		<textarea type="text" class="input-text addon addon-custom-textarea" data-raw-price="<?php echo esc_attr( $option['price'] ); ?>" data-price="<?php echo get_product_addon_price_for_display( $option['price'] ); ?>" name="<?php echo $addon_key ?>[<?php echo $option_key; ?>]" rows="4" cols="20" <?php if ( ! empty( $option['max'] ) ) echo 'maxlength="' . $option['max'] .'"'; ?>><?php echo esc_textarea( $current_value ); ?></textarea>
	</p>

<?php endforeach; ?>