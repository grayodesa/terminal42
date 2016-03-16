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

	if ( ! isset( $availability['type'] ) ) {
		$availability['type'] = 'custom';
	}
	
	if ( ! isset( $availability['priority'] ) ) {
		$availability['priority'] = 10;
	}
	
	//* Deprecated 'time_date' in favour of 'time:range'
	if ( $availability['type'] === 'time_date' ) {
		$availability['type'] = 'time:range'; #convert availability type
		$availability['from_date'] = $availability['on']; #convert on => from date
		$availability['to_date'] = $availability['on']; #convert on => to date
	}
?>
<tr>
	<td class="sort">&nbsp;</td>
	<td class="range_type">
		<div class="select wc_appointment_availability_type">
			<select name="wc_appointment_availability_type[]">
				<option value="custom" <?php selected( $availability['type'], 'custom' ); ?>><?php _e( 'Date range', 'woocommerce-appointments' ); ?></option>
				<option value="months" <?php selected( $availability['type'], 'months' ); ?>><?php _e( 'Range of months', 'woocommerce-appointments' ); ?></option>
				<option value="weeks" <?php selected( $availability['type'], 'weeks' ); ?>><?php _e( 'Range of weeks', 'woocommerce-appointments' ); ?></option>
				<option value="days" <?php selected( $availability['type'], 'days' ); ?>><?php _e( 'Range of days', 'woocommerce-appointments' ); ?></option>
				<optgroup label="<?php _e( 'Time Ranges', 'woocommerce-appointments' ); ?>">
					<option value="time" <?php selected( $availability['type'], 'time' ); ?>><?php _e( 'Time Range (all week)', 'woocommerce-appointments' ); ?></option>
					<option value="time:range" <?php selected( $availability['type'], 'time:range' ); ?>><?php _e( 'Time Range (date range)', 'woocommerce-appointments' ); ?></option>
					<?php foreach ( $intervals['days'] as $key => $label ) : ?>
						<option value="time:<?php echo $key; ?>" <?php selected( $availability['type'], 'time:' . $key ) ?>><?php echo $label; ?></option>
					<?php endforeach; ?>
				</optgroup>
			</select>
		</div>
	</td>
	<td class="range_from">
		<div class="appointments-datetime-select-from">
			<div class="select from_day_of_week">
				<select name="wc_appointment_availability_from_day_of_week[]">
					<?php foreach ( $intervals['days'] as $key => $label ) : ?>
						<option value="<?php echo $key; ?>" <?php selected( isset( $availability['from'] ) && $availability['from'] == $key, true ) ?>><?php echo $label; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="select from_month">
				<select name="wc_appointment_availability_from_month[]">
					<?php foreach ( $intervals['months'] as $key => $label ) : ?>
						<option value="<?php echo $key; ?>" <?php selected( isset( $availability['from'] ) && $availability['from'] == $key, true ) ?>><?php echo $label; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="select from_week">
				<select name="wc_appointment_availability_from_week[]">
					<?php foreach ( $intervals['weeks'] as $key => $label ) : ?>
						<option value="<?php echo $key; ?>" <?php selected( isset( $availability['from'] ) && $availability['from'] == $key, true ) ?>><?php echo $label; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="from_date">
				<?php
				$from_date = '';
				if ( 'custom' === $availability['type'] && ! empty( $availability['from'] ) ) {
					$from_date = $availability['from'];
				} else if ( 'time:range' === $availability['type'] && ! empty( $availability['from_date'] ) ) {
					$from_date = $availability['from_date'];
				}
				?>
				<input type="text" class="date-picker" name="wc_appointment_availability_from_date[]" value="<?php echo esc_attr( $from_date ); ?>" />
			</div>
			<div class="from_time">
				<input type="time" class="time-picker" name="wc_appointment_availability_from_time[]" value="<?php if ( strrpos( $availability['type'], 'time' ) === 0 && ! empty( $availability['from'] ) ) echo $availability['from'] ?>" placeholder="HH:MM" />
			</div>
		</div>
	</td>
	<td class="range_to">
		<div class='appointments-datetime-select-to'>
			<div class="select to_day_of_week">
				<select name="wc_appointment_availability_to_day_of_week[]">
					<?php foreach ( $intervals['days'] as $key => $label ) : ?>
						<option value="<?php echo $key; ?>" <?php selected( isset( $availability['to'] ) && $availability['to'] == $key, true ) ?>><?php echo $label; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="select to_month">
				<select name="wc_appointment_availability_to_month[]">
					<?php foreach ( $intervals['months'] as $key => $label ) : ?>
						<option value="<?php echo $key; ?>" <?php selected( isset( $availability['to'] ) && $availability['to'] == $key, true ) ?>><?php echo $label; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="select to_week">
				<select name="wc_appointment_availability_to_week[]">
					<?php foreach ( $intervals['weeks'] as $key => $label ) : ?>
						<option value="<?php echo $key; ?>" <?php selected( isset( $availability['to'] ) && $availability['to'] == $key, true ) ?>><?php echo $label; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="to_date">
				<?php
				$to_date = '';
				if ( 'custom' === $availability['type'] && ! empty( $availability['to'] ) ) {
					$to_date = $availability['to'];
				} else if ( 'time:range' === $availability['type'] && ! empty( $availability['to_date'] ) ) {
					$to_date = $availability['to_date'];
				}
				?>
				<input type="text" class="date-picker" name="wc_appointment_availability_to_date[]" value="<?php echo esc_attr( $to_date ); ?>" />
			</div>

			<div class="to_time">
				<input type="time" class="time-picker" name="wc_appointment_availability_to_time[]" value="<?php if ( strrpos( $availability['type'], 'time' ) === 0 && ! empty( $availability['to'] ) ) echo $availability['to']; ?>" placeholder="HH:MM" />
			</div>
		</div>
	</td>
	<td class="range_capacity">
		<input type="number" name="wc_appointment_availability_qty[]" id="wc_appointment_availability_qty" value="<?php if ( isset( $availability['qty'] ) && ! empty( $availability['qty'] ) ) echo $availability['qty']; ?>" step="1" min="1" placeholder="<?php echo max( absint( get_post_meta( $post_id, '_wc_appointment_qty', true ) ), 1 );?>" style="width: 6em;">
	</td>
	<td class="range_appointable">
		<div class="select">
			<select name="wc_appointment_availability_appointable[]">
				<option value="yes" <?php selected( isset( $availability['appointable'] ) && $availability['appointable'] == 'yes', true ) ?>><?php _e( 'Yes', 'woocommerce-appointments' ) ;?></option>
				<option value="no" <?php selected( isset( $availability['appointable'] ) && $availability['appointable'] == 'no', true ) ?>><?php _e( 'No', 'woocommerce-appointments' ) ;?></option>
			</select>
		</div>
	</td>
	<td class="remove">&nbsp;</td>
</tr>
