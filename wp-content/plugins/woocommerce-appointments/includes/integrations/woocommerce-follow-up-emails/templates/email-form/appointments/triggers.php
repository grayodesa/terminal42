<?php
$last_status = ( !empty( $email->meta['appointments_last_status'] ) ) ? $email->meta['appointments_last_status'] : '';
?>
<p class="form-field show-if-appointment-status">
    <label for="meta_appointments_last_status"><?php _e('Last Status', 'woocommerce-appointments'); ?></label>
    <select name="meta[appointments_last_status]" id="meta_appointments_last_status">
        <option value="" <?php selected($last_status, ''); ?>><?php _e('Any status', 'woocommerce-appointments'); ?></option>
        <?php foreach ( self::$statuses as $status ): ?>
        <option value="<?php echo $status; ?>" <?php selected($last_status, $status); ?>><?php echo ucfirst( $status ); ?></option>
        <?php endforeach; ?>
    </select>
    <br/>
    <span class="description"><?php _e('Only send this email if the appointment\'s last status matches the selected value', 'woocommerce-appointments'); ?></span>
</p>