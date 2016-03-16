<?php

$appointment_products = WC_Appointments_Admin::get_appointment_products();

// Hides the save button
echo '<style>p.submit input[type="submit"] { display: none }</style>';

?>

<div class="wrap woocommerce">
	<h3><?php _e( 'Send Notification', 'woocommerce-appointments' ); ?></h3>
	<p><?php _e( 'You may send an email notification to all customers who have a <strong>future</strong> appointment for a particular product. This will use the default template specified under <code>WooCommerce > Settings > Emails</code>.', 'woocommerce-appointments' ); ?></p>

	<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row">
					<label for="notification_product_id"><?php _e( 'Appointment Product', 'woocommerce-appointments' ); ?></label>
				</th>
				<td>
					<select id="notification_product_id" name="notification_product_id">
						<option value=""><?php _e( 'Select a appointment product...', 'woocommerce-appointments' ); ?></option>
						<?php foreach ( $appointment_products as $product ) : ?>
							<option value="<?php echo $product->ID; ?>"><?php echo $product->post_title; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="notification_subject"><?php _e( 'Subject', 'woocommerce-appointments' ); ?></label>
				</th>
				<td>
					<input type="text" placeholder="<?php _e( 'Email subject', 'woocommerce-appointments' ); ?>" name="notification_subject" id="notification_subject" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="notification_message"><?php _e( 'Message', 'woocommerce-appointments' ); ?></label>
				</th>
				<td>
					<textarea id="notification_message" name="notification_message" class="large-text code" placeholder="<?php _e( 'The message you wish to send', 'woocommerce-appointments' ); ?>"></textarea>
					<span class="description"><?php _e( 'The following tags can be inserted in your message/subject and will be replaced dynamically' , 'woocommerce-appointments' ); ?>: <code>{product_title} {order_date} {order_number} {customer_name} {customer_first_name} {customer_last_name}</code></span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<?php _e( 'Attachment', 'woocommerce-appointments' ); ?>
				</th>
				<td>
					<label><input type="checkbox" name="notification_ics" id="notification_ics" /> <?php _e( 'Attach <code>.ics</code> file', 'woocommerce-appointments' ); ?></label>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">&nbsp;</th>
				<td>
					<input type="submit" name="send" class="button-primary" value="<?php _e( 'Send Notification', 'woocommerce-appointments' ); ?>" />
					<?php wp_nonce_field( 'send_appointment_notification' ); ?>
				</td>
			</tr>
		</tbody>
	</table>
</div>