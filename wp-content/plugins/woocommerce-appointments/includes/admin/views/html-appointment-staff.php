<div id="appointments_staff" class="woocommerce_options_panel panel wc-metaboxes-wrapper">

	<div class="options_group" id="staff_options">

		<?php woocommerce_wp_text_input( array( 'id' => '_wc_appointment_staff_label', 'placeholder' => __( 'Provider', 'woocommerce-appointments' ), 'label' => __( 'Label', 'woocommerce-appointments' ), 'desc_tip' => true, 'description' => __( 'The label shown on the frontend if the staff is customer defined.', 'woocommerce-appointments' ) ) ); ?>

		<?php woocommerce_wp_select( array( 'id' => '_wc_appointment_staff_assignment', 'label' => __( 'Staff selection', 'woocommerce-appointments' ), 'description' => '', 'desc_tip' => true, 'value' => get_post_meta( $post_id, '_wc_appointment_staff_assignment', true ), 'options' => array(
			'customer' 	  => __( 'Customer selected', 'woocommerce-appointments' ),
			'automatic'   => __( 'Automatically assigned', 'woocommerce-appointments' ),
		), 'description' => __( 'Customer selected staff allow customers to choose one from the appointment form.', 'woocommerce-appointments' ) ) ); ?>

	</div>

	<div class="options_group">
		<div class="toolbar">
			<h3><?php _e( 'Staff', 'woocommerce-appointments' ); ?></h3>
		</div>
		<p><?php _e( 'Capacity for this appointable product will equal number of staff linked.', 'woocommerce-appointments' ); ?></p>
		<div class="woocommerce_appointable_staff wc-metaboxes">
			<?php
			global $post, $wpdb;

			$product_staff = $wpdb->get_col( $wpdb->prepare( "SELECT staff_id FROM {$wpdb->prefix}wc_appointment_relationships WHERE product_id = %d ORDER BY sort_order;", $post->ID ) );
			$loop = 0;

			if ( $product_staff ) {
				$staff_base_costs  = get_post_meta( $post_id, '_staff_base_costs', true );
				
				foreach ( $product_staff as $staff_id ) {
					$staff = get_user_by( 'id', $staff_id );
					$staff_base_cost  = isset( $staff_base_costs[ $staff_id ] ) ? $staff_base_costs[ $staff_id ] : '';
					
					include( 'html-appointment-staff-member.php' );
					
					$loop++;
				}
			}
			?>
		</div>
		<p class="toolbar">
			<?php			
			$all_staff = WC_Appointments_Admin::get_appointment_staff();
	
			if ( $all_staff ) {
			?>
				<button type="button" class="button button-primary add_staff"><?php _e( 'Link Staff', 'woocommerce-appointments' ); ?></button>
				<select name="add_staff_id" class="add_staff_id">
					<?php					
					foreach ( $all_staff as $staff ) {
						echo '<option value="' . esc_attr( $staff->ID ) . '">' . esc_html( $staff->display_name ) . '</option>';
					}
					?>
				</select>
			<?php } ?>
			<a href="<?php echo admin_url( 'users.php?role=shop_staff' ); ?>" target="_blank"><?php _e( 'Manage Staff', 'woocommerce-appointments' ); ?></a>
			<?php if ( current_user_can('create_users') ) { ?>
				&middot; <a href="<?php echo admin_url('user-new.php'); ?>" target="_blank"><?php _e( 'Add Staff', 'woocommerce-appointments' ); ?></a>
			<?php } ?>
		</p>
	</div>
</div>