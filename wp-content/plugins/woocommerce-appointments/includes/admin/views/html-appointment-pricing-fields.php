<?php
	$intervals = array();

	$intervals['months'] = array(
		'1'  => __( 'January', 'woocommerce-appointments' ),
		'2'  => __( 'February', 'woocommerce-appointments' ),
		'3'  => __( 'March', 'woocommerce-appointments' ),
		'4'  => __( 'April', 'woocommerce-appointments' ),
		'5'  => __( 'May', 'woocommerce-appointments' ),
		'6'  => __( 'June', 'woocommerce-appointments' ),
		'7'  => __( 'July', 'woocommerce-appointments' ),
		'8'  => __( 'August', 'woocommerce-appointments' ),
		'9'  => __( 'September', 'woocommerce-appointments' ),
		'10' => __( 'October', 'woocommerce-appointments' ),
		'11' => __( 'November', 'woocommerce-appointments' ),
		'12' => __( 'December', 'woocommerce-appointments' )
	);

	$intervals['days'] = array(
		'1' => __( 'Monday', 'woocommerce-appointments' ),
		'2' => __( 'Tuesday', 'woocommerce-appointments' ),
		'3' => __( 'Wednesday', 'woocommerce-appointments' ),
		'4' => __( 'Thursday', 'woocommerce-appointments' ),
		'5' => __( 'Friday', 'woocommerce-appointments' ),
		'6' => __( 'Saturday', 'woocommerce-appointments' ),
		'7' => __( 'Sunday', 'woocommerce-appointments' )
	);

	for ( $i = 1; $i <= 53; $i ++ ) {
		$intervals['weeks'][ $i ] = sprintf( __( 'Week %s', 'woocommerce-appointments' ), $i );
	}

	if ( ! isset( $pricing['type'] ) ) {
		$pricing['type'] = 'custom';
	}

	if ( ! isset( $pricing['modifier'] ) ) {
		$pricing['modifier'] = '';
	}
	if ( ! isset( $pricing['base_modifier'] ) ) {
		$pricing['base_modifier'] = '';
	}
	if ( ! isset( $pricing['base_cost'] ) ) {
		$pricing['base_cost'] = '';
	}
?>
<tr>
	<td class="sort">&nbsp;</td>
	<td class="range_type">
		<div class="select wc_appointment_pricing_type">
			<select name="wc_appointment_pricing_type[]">
				<option value="custom" <?php selected( $pricing['type'], 'custom' ); ?>><?php _e( 'Date range', 'woocommerce-appointments' ); ?></option>
				<option value="months" <?php selected( $pricing['type'], 'months' ); ?>><?php _e( 'Range of months', 'woocommerce-appointments' ); ?></option>
				<option value="weeks" <?php selected( $pricing['type'], 'weeks' ); ?>><?php _e( 'Range of weeks', 'woocommerce-appointments' ); ?></option>
				<option value="days" <?php selected( $pricing['type'], 'days' ); ?>><?php _e( 'Range of days', 'woocommerce-appointments' ); ?></option>
				<!--<option value="slots" <?php selected( $pricing['type'], 'slots' ); ?>><?php _e( 'Slot count', 'woocommerce-appointments' ); ?></option>-->
				<option value="quant" <?php selected( $pricing['type'], 'quant' ); ?>><?php _e( 'Capacity count', 'woocommerce-appointments' ); ?></option>
				<optgroup label="<?php _e( 'Time Ranges', 'woocommerce-appointments' ); ?>">
					<option value="time" <?php selected( $pricing['type'], 'time' ); ?>><?php _e( 'Time Range (all week)', 'woocommerce-appointments' ); ?></option>
					<option value="time:range" <?php selected( $pricing['type'], 'time:range' ); ?>><?php _e( 'Time Range (date range)', 'woocommerce-appointments' ); ?></option>
					<?php foreach ( $intervals['days'] as $key => $label ) : ?>
						<option value="time:<?php echo $key; ?>" <?php selected( $pricing['type'], 'time:' . $key ) ?>><?php echo $label; ?></option>
					<?php endforeach; ?>
				</optgroup>
			</select>
		</div>
	</td>
	<td class="range_from">
		<div class="appointments-datetime-select-from">
			<div class="select from_day_of_week">
				<select name="wc_appointment_pricing_from_day_of_week[]">
					<?php foreach ( $intervals['days'] as $key => $label ) : ?>
						<option value="<?php echo $key; ?>" <?php selected( isset( $pricing['from'] ) && $pricing['from'] == $key, true ) ?>><?php echo $label; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="select from_month">
				<select name="wc_appointment_pricing_from_month[]">
					<?php foreach ( $intervals['months'] as $key => $label ) : ?>
						<option value="<?php echo $key; ?>" <?php selected( isset( $pricing['from'] ) && $pricing['from'] == $key, true ) ?>><?php echo $label; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="select from_week">
				<select name="wc_appointment_pricing_from_week[]">
					<?php foreach ( $intervals['weeks'] as $key => $label ) : ?>
						<option value="<?php echo $key; ?>" <?php selected( isset( $pricing['from'] ) && $pricing['from'] == $key, true ) ?>><?php echo $label; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="from_date">
				<?php
				$from_date = '';
				if ( 'custom' === $pricing['type'] && ! empty( $pricing['from'] ) ) {
					$from_date = $pricing['from'];
				} else if ( 'time:range' === $pricing['type'] && ! empty( $pricing['from_date'] ) ) {
					$from_date = $pricing['from_date'];
				}
				?>
				<input type="text" class="date-picker" name="wc_appointment_pricing_from_date[]" value="<?php echo esc_attr( $from_date ); ?>" />
			</div>
			<div class="from_time">
				<input type="time" class="time-picker" name="wc_appointment_pricing_from_time[]" value="<?php if ( strrpos( $pricing['type'], 'time' ) === 0 && ! empty( $pricing['from'] ) ) echo $pricing['from'] ?>" placeholder="HH:MM" />
			</div>
			<div class="from">
				<input type="number" step="1" name="wc_appointment_pricing_from[]" value="<?php if ( ! empty( $pricing['from'] ) && is_numeric( $pricing['from'] ) ) echo $pricing['from'] ?>" />
			</div>
		</div>
	</td>
	<td class="range_to">
		<div class='appointments-datetime-select-to'>
			<div class="select to_day_of_week">
				<select name="wc_appointment_pricing_to_day_of_week[]">
					<?php foreach ( $intervals['days'] as $key => $label ) : ?>
						<option value="<?php echo $key; ?>" <?php selected( isset( $pricing['to'] ) && $pricing['to'] == $key, true ) ?>><?php echo $label; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="select to_month">
				<select name="wc_appointment_pricing_to_month[]">
					<?php foreach ( $intervals['months'] as $key => $label ) : ?>
						<option value="<?php echo $key; ?>" <?php selected( isset( $pricing['to'] ) && $pricing['to'] == $key, true ) ?>><?php echo $label; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="select to_week">
				<select name="wc_appointment_pricing_to_week[]">
					<?php foreach ( $intervals['weeks'] as $key => $label ) : ?>
						<option value="<?php echo $key; ?>" <?php selected( isset( $pricing['to'] ) && $pricing['to'] == $key, true ) ?>><?php echo $label; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="to_date">
				<?php
				$to_date = '';
				if ( 'custom' === $pricing['type'] && ! empty( $pricing['to'] ) ) {
					$to_date = $pricing['to'];
				} else if ( 'time:range' === $pricing['type'] && ! empty( $pricing['to_date'] ) ) {
					$to_date = $pricing['to_date'];
				}
				?>
				<input type="text" class="date-picker" name="wc_appointment_pricing_to_date[]" value="<?php echo esc_attr( $to_date ); ?>" />
			</div>

			<div class="to_time">
				<input type="time" class="time-picker" name="wc_appointment_pricing_to_time[]" value="<?php if ( strrpos( $pricing['type'], 'time' ) === 0 && ! empty( $pricing['to'] ) ) echo $pricing['to']; ?>" placeholder="HH:MM" />
			</div>
			<div class="to">
				<input type="number" step="1" name="wc_appointment_pricing_to[]" value="<?php if ( ! empty( $pricing['to'] ) && is_numeric( $pricing['to'] ) ) echo $pricing['to'] ?>" />
			</div>
		</div>
	</td>
	<td>
		<div class="price_wrap">
			<select name="wc_appointment_pricing_base_cost_modifier[]">
				<option <?php selected( $pricing['base_modifier'], '' ); ?> value="">+</option>
				<option <?php selected( $pricing['base_modifier'], 'minus' ); ?> value="minus">-</option>
				<option <?php selected( $pricing['base_modifier'], 'times' ); ?> value="times">&times;</option>
				<option <?php selected( $pricing['base_modifier'], 'divide' ); ?> value="divide">&divide;</option>
			</select>
			<input type="number" step="0.01" name="wc_appointment_pricing_base_cost[]" value="<?php if ( ! empty( $pricing['base_cost'] ) ) echo $pricing['base_cost']; ?>" placeholder="0" />
			<?php do_action( 'woocommerce_appointments_after_appointment_pricing_base_cost', $pricing, $post_id ); ?>
		</div>
	</td>
	<td>
		<div class="price_wrap">
			<select name="wc_appointment_pricing_cost_modifier[]">
				<option <?php selected( $pricing['modifier'], '' ); ?> value="">+</option>
				<option <?php selected( $pricing['modifier'], 'minus' ); ?> value="minus">-</option>
				<option <?php selected( $pricing['modifier'], 'times' ); ?> value="times">&times;</option>
				<option <?php selected( $pricing['modifier'], 'divide' ); ?> value="divide">&divide;</option>
			</select>
			<input type="number" step="0.01" name="wc_appointment_pricing_cost[]" value="<?php if ( ! empty( $pricing['cost'] ) ) echo $pricing['cost']; ?>" placeholder="0" />
			<?php do_action( 'woocommerce_appointments_after_appointment_pricing_cost', $pricing, $post_id ); ?>
		</div>
	</td>
	<td class="remove">&nbsp;</td>
</tr>