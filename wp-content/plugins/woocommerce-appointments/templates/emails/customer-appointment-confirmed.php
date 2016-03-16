<?php
/**
 * Customer appointment confirmed email
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php do_action( 'woocommerce_email_header', $email_heading ); ?>

<?php if ( $appointment->get_order() ) : ?>
	<p><?php printf( __( 'Hello %s', 'woocommerce-appointments' ), $appointment->get_order()->billing_first_name ); ?></p>
<?php endif; ?>

<p><?php _e( 'Your appointment has been confirmed. The details of your appointment are shown below.', 'woocommerce-appointments' ); ?></p>

<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee; margin:0 0 16px;" border="1" bordercolor="#eee">
	<tbody>
		<tr>
			<th scope="row" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Scheduled Product', 'woocommerce-appointments' ); ?></th>
			<td style="text-align:left; border: 1px solid #eee;"><?php echo $appointment->get_product()->get_title(); ?></td>
		</tr>
		<tr>
			<th style="text-align:left; border: 1px solid #eee;" scope="row"><?php _e( 'Appointment ID', 'woocommerce-appointments' ); ?></th>
			<td style="text-align:left; border: 1px solid #eee;"><?php echo $appointment->get_id(); ?></td>
		</tr>
		<?php if ( $appointment->has_staff() && ( $staff = $appointment->get_staff_member() ) ) : ?>
			<tr>
				<th style="text-align:left; border: 1px solid #eee;" scope="row"><?php _e( 'Appointment Provider', 'woocommerce-appointments' ); ?></th>
				<td style="text-align:left; border: 1px solid #eee;"><?php echo $staff->display_name; ?></td>
			</tr>
		<?php endif; ?>
		<tr>
			<th style="text-align:left; border: 1px solid #eee;" scope="row"><?php _e( 'Appointment Date', 'woocommerce-appointments' ); ?></th>
			<td style="text-align:left; border: 1px solid #eee;"><?php echo $appointment->get_start_date( wc_date_format(), '' ); ?></td>
		</tr>
		<tr>
			<th style="text-align:left; border: 1px solid #eee;" scope="row"><?php _e( 'Appointment Time', 'woocommerce-appointments' ); ?></th>
			<td style="text-align:left; border: 1px solid #eee;"><?php echo $appointment->get_start_date( '', get_option( 'time_format' ) ) . ' &mdash; ' . $appointment->get_end_date( '', get_option( 'time_format' ) ); ?></td>
		</tr>
	</tbody>
</table>

<?php if ( $order = $appointment->get_order() ) : ?>

	<?php if ( $order->status == 'pending' ) : ?>
		<p><?php printf( __( 'To pay for this appointment please use the following link: %s', 'woocommerce-appointments' ), '<a href="' . esc_url( $order->get_checkout_payment_url() ) . '">' . __( 'Pay for appointment', 'woocommerce-appointments' ) . '</a>' ); ?></p>
	<?php endif; ?>

	<?php do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text ); ?>

	<h2><?php echo __( 'Order', 'woocommerce-appointments' ) . ' ' . $order->get_order_number(); ?> (<?php printf( '<time datetime="%s">%s</time>', date_i18n( 'c', strtotime( $order->order_date ) ), date_i18n( wc_date_format(), strtotime( $order->order_date ) ) ); ?>)</h2>

	<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
		<thead>
			<tr>
				<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Product', 'woocommerce-appointments' ); ?></th>
				<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Quantity', 'woocommerce-appointments' ); ?></th>
				<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Price', 'woocommerce-appointments' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
				switch ( $order->status ) {
					case "completed" :
						echo $order->email_order_items_table( $order->is_download_permitted(), false, true );
					break;
					case "processing" :
						echo $order->email_order_items_table( $order->is_download_permitted(), true, true );
					break;
					default :
						echo $order->email_order_items_table( $order->is_download_permitted(), true, false );
					break;
				}
			?>
		</tbody>
		<tfoot>
			<?php
				if ( $totals = $order->get_order_item_totals() ) {
					$i = 0;
					foreach ( $totals as $total ) {
						$i++;
						?><tr>
							<th scope="row" colspan="2" style="text-align:left; border: 1px solid #eee; <?php if ( $i == 1 ) echo 'border-top-width: 4px;'; ?>"><?php echo $total['label']; ?></th>
							<td style="text-align:left; border: 1px solid #eee; <?php if ( $i == 1 ) echo 'border-top-width: 4px;'; ?>"><?php echo $total['value']; ?></td>
						</tr><?php
					}
				}
			?>
		</tfoot>
	</table>

	<?php do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text ); ?>

	<?php do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text ); ?>

<?php endif; ?>

<?php do_action( 'woocommerce_email_footer' ); ?>
