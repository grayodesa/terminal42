<div id="appointments_availability" class="panel woocommerce_options_panel wc-metaboxes-wrapper">
	<div class="options_group show_if_appointment">
		<?php woocommerce_wp_select( array(
				'id'			=> '_wc_appointment_fakeit',
				'label'			=> __( 'Busy Filter', 'woocommerce-appointments' ),
				'description'	=> __( 'Remove some of your available slots. If your appointable product is not busy enough, we recommend using it.', 'woocommerce-appointments' ),
				'desc_tip'		=> true,
				'value'			=> get_post_meta( $post_id, '_wc_appointment_fakeit', true ),
				'options' => array(
					'off' 	  => __( 'Off', 'woocommerce-appointments' ),
					'20'      => __( 'Remove roughly 20% of your openings', 'woocommerce-appointments' ),
					'35'      => __( 'Remove roughly 35% of your openings', 'woocommerce-appointments' ),
					'50'      => __( 'Remove roughly 50% of your openings', 'woocommerce-appointments' ),
				)
			) ); ?>
		<?php woocommerce_wp_select( array(
				'id'			=> '_wc_appointment_availability_span',
				'label'			=> __( 'Availability Check', 'woocommerce-appointments' ),
				'description'	=> __( 'By default availability per each slot in range is checked. You can also check avaulability for starting slot only.', 'woocommerce-appointments' ),
				'desc_tip'		=> true,
				'value'			=> get_post_meta( $post_id, '_wc_appointment_availability_span', true ),
				'options' => array(
					''        => __( 'All slots in availability range', 'woocommerce-appointments' ),
					'start'   => __( 'The starting slot only', 'woocommerce-appointments' ),
				)
			) ); ?>
	</div>
	<div class="options_group">
		<div class="toolbar">
			<h3><?php _e( 'Custom Availability', 'woocommerce-appointments' ); ?></h3>
		</div>
		<p><?php printf( __( 'Add custom availability rules to override <a href="%s">global availability</a> for this appointment only.', 'woocommerce-appointments' ), admin_url( 'admin.php?page=wc-settings&tab=appointments' ) ); ?></p>
		<div class="table_grid">
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
						$values = get_post_meta( $post_id, '_wc_appointment_availability', true );
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