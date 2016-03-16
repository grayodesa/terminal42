<?php

wp_enqueue_script( 'jquery-ui-datepicker' );
wp_enqueue_script( 'wc_appointments_writepanel_js' );

?>

<div id="appointments_settings">
	<input type="hidden" name="appointments_availability_submitted" value="1" />
	<div id="poststuff">
		<div class="postbox">
			<h3 class="hndle"><?php _e( 'Global availability', 'woocommerce-appointments' ); ?></h3>
			<div class="inside">
				<p><?php _e( 'The availability rules you define here will affect all appointable products. You can override them for each product, staff and location.', 'woocommerce-appointments' ); ?></p>
				<div class="table_grid" id="appointments_availability">
					<table class="widefat">
						<thead>
							<tr>
								<th class="sort" width="1%">&nbsp;</th>
								<th class="range_type"><?php _e( 'Range type', 'woocommerce-appointments' ); ?></th>
								<th class="range_name"><?php _e( 'Range', 'woocommerce-appointments' ); ?></th>
								<th class="range_name2"></th>
								<th class="range_capacity"><?php _e( 'Capacity', 'woocommerce-appointments' ); ?>&nbsp;<a class="tips" data-tip="<?php _e( 'The maximum number of appointments per slot. Overrides general product capacity.', 'woocommerce-appointments' ); ?>">[?]</a></th>
								<th class="range_appointable"><?php _e( 'Appointable', 'woocommerce-appointments' ); ?>&nbsp;<a class="tips" data-tip="<?php _e( 'If not appointable, users won\'t be able to choose slots in this range for their appointment.', 'woocommerce-appointments' ); ?>">[?]</a></th>
								<th class="remove" width="1%">&nbsp;</th>
							</tr>
						</thead>
						<tbody id="availability_rows">
							<?php
								$values = get_option( 'wc_global_appointment_availability' );
								if ( ! empty( $values ) && is_array( $values ) ) {
									foreach ( $values as $availability ) {
										include( 'html-appointment-availability-fields.php' );
									}
								}
							?>
						</tbody>
						<tfoot>
							<tr>
								<th colspan="8">
									<a href="#" class="button add_row" data-row="<?php
										ob_start();
										include( 'html-appointment-availability-fields.php' );
										$html = ob_get_clean();
										echo esc_attr( $html );
									?>"><?php _e( 'Add Range', 'woocommerce-appointments' ); ?></a>
									<span class="description"><?php _e( 'Rules further down the table will override those at the top.', 'woocommerce-appointments' ); ?></span>
								</th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>