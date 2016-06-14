<?php foreach ( $addon['options'] as $i => $option ) :

	$price = $option['price'] > 0 ? '<span class="amount-symbol">+</span>' . wc_price( get_product_addon_price_for_display( $option['price'] ) ) . '' : '';
	$duration = absint( $option['duration'] ) > 0 ? '<span class="addon-duration"> + ' . wc_appointment_convert_to_hours_and_minutes( absint( $option['duration'] ) ) . '</span>' : '';
	
	$selected = isset( $_POST[ 'addon-' . sanitize_title( $addon['field-name'] ) ] ) ? $_POST[ 'addon-' . sanitize_title( $addon['field-name'] ) ] : array();
	if ( ! is_array( $selected ) ) {
		$selected = array( $selected );
	}

	$current_value = ( in_array( sanitize_title( $option['label'] ), $selected ) ) ? 1 : 0;
	?>

	<p class="form-row form-row-wide addon-wrap-<?php echo sanitize_title( $addon['field-name'] ) . '-' . $i; ?>">
		<label><input type="checkbox" class="addon addon-checkbox" name="addon-<?php echo sanitize_title( $addon['field-name'] ); ?>[]" data-raw-price="<?php echo esc_attr( $option['price'] ); ?>" data-price="<?php echo get_product_addon_price_for_display( $option['price'] ); ?>" value="<?php echo sanitize_title( $option['label'] ); ?>" <?php checked( $current_value, 1 ); ?> /> <?php echo wptexturize( $option['label'] . ' ' . $price . $duration ); ?></label>
	</p>

<?php endforeach; ?>