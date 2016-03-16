<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="woocommerce_appointment_staff wc-metabox closed">
	<h3>
		<button type="button" class="remove_appointment_staff button" rel="<?php echo esc_attr( absint( $staff_id ) ); ?>"><?php _e( 'Remove', 'woocommerce-appointments' ); ?></button>

		<a href="<?php echo get_edit_user_link( $staff_id ); ?>" target="_blank" class="edit_staff"><?php _e( 'Edit', 'woocommerce-appointments' ); ?></a>
		
		<div class="handlediv" title="<?php _e( 'Click to toggle', 'woocommerce-appointments' ); ?>"></div>

		<?php echo get_avatar( $staff_id, 22, '', $staff->display_name ); ?>
		
		<strong><span class="staff_name"><?php echo $staff->display_name; ?></span></strong>

		<input type="hidden" name="staff_id[<?php echo $loop; ?>]" value="<?php echo esc_attr( $staff_id ); ?>" />
		<input type="hidden" class="staff_menu_order" name="staff_menu_order[<?php echo $loop; ?>]" value="<?php echo $loop; ?>" />
	</h3>
	<table cellpadding="0" cellspacing="0" class="wc-metabox-content">
		<tbody>
			<tr>
				<td>
					<label><?php _e( 'Additional Cost', 'woocommerce-appointments' ); ?>:</label>
					<input type="number" class="" name="staff_cost[<?php echo $loop; ?>]" value="<?php if ( ! empty( $staff_base_cost ) ) echo esc_attr( $staff_base_cost ); ?>" placeholder="0.00" step="0.01" style="width: 4em;" />
                    <?php do_action( 'woocommerce_appointments_after_staff_cost', $staff_id, $post_id ); ?>
				</td>
			</tr>
		</tbody>
	</table>
</div>
