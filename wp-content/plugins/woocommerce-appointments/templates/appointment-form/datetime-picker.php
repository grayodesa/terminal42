<?php
wp_enqueue_script( 'wc-appointments-date-picker' );
wp_enqueue_script( 'wc-appointments-time-picker' );
extract( $field );

$month_before_day = strpos( __( 'F j, Y' ), 'F' ) < strpos( __( 'F j, Y' ), 'j' );
?>
<fieldset class="wc-appointments-date-picker <?php echo implode( ' ', $class ); ?>">
	<legend>
		<span class="label"><small class="wc-appointments-date-picker-choose-date"><?php _e( 'Choose...', 'woocommerce-appointments' ); ?></small>
	</legend>
	<div class="picker" data-availability="<?php echo esc_attr( json_encode( $availability_rules ) ); ?>" data-default-availability="<?php echo $default_availability ? 'true' : 'false'; ?>" data-fully-scheduled-days="<?php echo esc_attr( json_encode( $fully_scheduled_days ) ); ?>" data-partially-scheduled-days="<?php echo esc_attr( json_encode( $partially_scheduled_days ) ); ?>" data-remaining-scheduled-days="<?php echo esc_attr( json_encode( $remaining_scheduled_days ) ); ?>" data-min_date="<?php echo ! empty( $min_date_js ) ? $min_date_js : 0; ?>" data-max_date="<?php echo $max_date_js; ?>" data-default_date="<?php echo esc_attr( $default_date ); ?>"></div>
	<div class="wc-appointments-date-picker-date-fields">
		<?php if ( $month_before_day ) : ?>
		<label>
			<input type="text" name="<?php echo $name; ?>_month" placeholder="<?php _e( 'mm', 'woocommerce-appointments' ); ?>" size="2" class="required_for_calculation appointment_date_month" />
			<span><?php _e( 'Month', 'woocommerce-appointments' ); ?></span>
		</label> / <label>
			<input type="text" name="<?php echo $name; ?>_day" placeholder="<?php _e( 'dd', 'woocommerce-appointments' ); ?>" size="2" class="required_for_calculation appointment_date_day" />
			<span><?php _e( 'Day', 'woocommerce-appointments' ); ?></span>
		</label>
		<?php else : ?>
		<label>
			<input type="text" name="<?php echo $name; ?>_day" placeholder="<?php _e( 'dd', 'woocommerce-appointments' ); ?>" size="2" class="required_for_calculation appointment_date_day" />
			<span><?php _e( 'Day', 'woocommerce-appointments' ); ?></span>
		</label> / <label>
			<input type="text" name="<?php echo $name; ?>_month" placeholder="<?php _e( 'mm', 'woocommerce-appointments' ); ?>" size="2" class="required_for_calculation appointment_date_month" />
			<span><?php _e( 'Month', 'woocommerce-appointments' ); ?></span>
		</label>
		<?php endif; ?>
		 / <label>
			<input type="text" value="<?php echo date( 'Y' ); ?>" name="<?php echo $name; ?>_year" placeholder="<?php _e( 'YYYY', 'woocommerce-appointments' ); ?>" size="4" class="required_for_calculation appointment_date_year" />
			<span><?php _e( 'Year', 'woocommerce-appointments' ); ?></span>
		</label>
	</div>
</fieldset>
<div class="form-field form-field-wide">
	<!--<label for="<?php echo $name; ?>" class="wc-appointments-time-picker-choose-time"><?php _e( 'Time', 'woocommerce-appointments' ); ?>:</label>-->
	<div class="slot-picker">
		<?php _e( 'Choose a date above to see available time slots.', 'woocommerce-appointments' ); ?>
	</div>
	<input type="hidden" class="required_for_calculation" name="<?php echo $name; ?>_time" id="<?php echo $name; ?>" />
</div>