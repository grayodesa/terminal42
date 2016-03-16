<?php
/**
 * Admin appointment cancelled email
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php do_action( 'woocommerce_email_header', $email_heading ); ?>

<p><?php _e( 'The following appointment has been cancelled by the customer. The details of the cancelled appointment can be found below.', 'woocommerce-appointments' ); ?></p>

<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee; margin:0 0 16px;" border="1" bordercolor="#eee">
	<tbody>
		<?php if ( $appointment->get_product() ) : ?>
			<tr>
				<th scope="row" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Scheduled Product', 'woocommerce-appointments' ); ?></th>
				<td style="text-align:left; border: 1px solid #eee;"><?php echo $appointment->get_product()->get_title(); ?></td>
			</tr>
		<?php endif; ?>
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

<p><?php echo make_clickable( sprintf( __( 'You can view and edit this appointment in the dashboard here: %s', 'woocommerce-appointments' ), admin_url( 'post.php?post=' . $appointment->get_id() . '&action=edit' ) ) ); ?></p>

<?php do_action( 'woocommerce_email_footer' ); ?>