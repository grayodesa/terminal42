<?php
/**
 * My Appointments
 *
 * Shows appointments on the account page
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<h2><?php echo apply_filters( 'woocommerce_my_account_appointments_title', __( 'My appointments', 'woocommerce-appointments' ) ); ?></h2>

<table class="shop_table my_account_appointments">
	<thead>
		<tr>
			<th scope="col" class="appointment-id"><?php _e( 'ID', 'woocommerce-appointments' ); ?></th>
			<th scope="col" class="scheduled-product"><?php _e( 'Scheduled', 'woocommerce-appointments' ); ?></th>
			<th scope="col" class="order-number"><?php _e( 'Order', 'woocommerce-appointments' ); ?></th>
			<th scope="col" class="appointment-date"><?php _e( 'Date', 'woocommerce-appointments' ); ?></th>
			<th scope="col" class="appointment-time"><?php _e( 'Time', 'woocommerce-appointments' ); ?></th>
			<th scope="col" class="appointment-status"><?php _e( 'Status', 'woocommerce-appointments' ); ?></th>
			<th scope="col" class="appointment-cancel"></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $appointments as $appointment ) : ?>
			<tr>
				<td class="appointment-id"><?php echo $appointment->get_id(); ?></td>
				<td class="scheduled-product">
					<?php if ( $appointment->get_product() ) : ?>
					<a href="<?php echo get_permalink( $appointment->get_product()->id ); ?>">
						<?php echo $appointment->get_product()->get_title(); ?>
					</a>
					<?php endif; ?>
				</td>
				<td class="order-number">
					<?php if ( $appointment->get_order() ) : ?>
					<a href="<?php echo $appointment->get_order()->get_view_order_url(); ?>">
						<?php echo $appointment->get_order()->get_order_number(); ?>
					</a>
					<?php endif; ?>
				</td>
				<td class="appointment-date"><?php echo $appointment->get_start_date( wc_date_format(), '' ); ?></td>
				<td class="appointment-time"><?php echo $appointment->get_start_date( '', get_option( 'time_format' ) ) . ' &mdash; ' . $appointment->get_end_date( '', get_option( 'time_format' ) ); ?></td>
				<td class="appointment-status"><?php echo $appointment->get_status( false ); ?></td>
				<td class="appointment-cancel">
					<?php if ( $appointment->get_status() != 'cancelled' && $appointment->get_status() != 'completed' && ! $appointment->passed_cancel_day() ) : ?>
					<a href="<?php echo $appointment->get_cancel_url(); ?>" class="button cancel"><?php _e( 'Cancel', 'woocommerce-appointments' ); ?></a>
					<?php endif ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
