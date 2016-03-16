<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WC_Appointments_Admin_Staff_Profile {

	/**
	 * Constructor
	 */
	public function __construct() {		
		add_action( 'show_user_profile', array( $this, 'add_staff_meta_fields' ), 20 );
		add_action( 'edit_user_profile', array( $this, 'add_staff_meta_fields' ), 20 );

		add_action( 'personal_options_update', array( $this, 'save_staff_meta_fields' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_staff_meta_fields' ) );
	}

	/**
	 * Show meta box
	 */
	public function add_staff_meta_fields( $user ) {		
		wp_enqueue_script( 'wc_appointments_writepanel_js' );
		
		if ( current_user_can( 'edit_user', $user->ID ) ) {
			?>
			<style type="text/css">
				#minor-publishing-actions, #visibility { display:none }
			</style>
			<h3><?php _e( 'Staff details', 'woocommerce-appointments' ); ?></h3>
			<table class="form-table">
				<tr>
					<th><label><?php _e( 'Custom Availability', 'woocommerce-appointments' ); ?></label></th>
					<td>
						<div class="woocommerce">
							<div class="panel-wrap" id="appointments_availability">
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
												$values = get_user_meta( $user->ID, '_wc_appointment_availability', true );
												if ( ! empty( $values ) && is_array( $values ) ) {
													foreach ( $values as $availability ) {
														include( 'views/html-appointment-availability-fields.php' );
													}
												}
											?>
										</tbody>
										<tfoot>
											<tr>
												<th colspan="8">
													<a href="#" class="button add_row" data-row="<?php
														ob_start();
														include( 'views/html-appointment-availability-fields.php' );
														$html = ob_get_clean();
														echo esc_attr( $html );
													?>"><?php _e( 'Add Range', 'woocommerce-appointments' ); ?></a>
													<span class="description"><?php _e( 'Rules further down the table will override those at the top.', 'woocommerce-appointments' ); ?></span>
												</th>
											</tr>
										</tfoot>
									</table>
								</div>
								<div class="clear"></div>
							</div>
						</div>
					</td>
				</tr>
			</table>
			<?php
		}
	}

	/**
	 * Save handler
	 */
	public function save_staff_meta_fields( $user_id ) {
		// Availability
		$availability = array();
		$row_size     = isset( $_POST[ "wc_appointment_availability_type" ] ) ? sizeof( $_POST[ "wc_appointment_availability_type" ] ) : 0;
		for ( $i = 0; $i < $row_size; $i ++ ) {
			$availability[ $i ]['type']     = wc_clean( $_POST[ "wc_appointment_availability_type" ][ $i ] );
			$availability[ $i ]['appointable'] = wc_clean( $_POST[ "wc_appointment_availability_appointable" ][ $i ] );
			$availability[ $i ]['qty'] = wc_clean( $_POST[ 'wc_appointment_availability_qty' ][ $i ] );

			switch ( $availability[ $i ]['type'] ) {
				case 'custom' :
					$availability[ $i ]['from'] = wc_clean( $_POST[ "wc_appointment_availability_from_date" ][ $i ] );
					$availability[ $i ]['to']   = wc_clean( $_POST[ "wc_appointment_availability_to_date" ][ $i ] );
				break;
				case 'months' :
					$availability[ $i ]['from'] = wc_clean( $_POST[ "wc_appointment_availability_from_month" ][ $i ] );
					$availability[ $i ]['to']   = wc_clean( $_POST[ "wc_appointment_availability_to_month" ][ $i ] );
				break;
				case 'weeks' :
					$availability[ $i ]['from'] = wc_clean( $_POST[ "wc_appointment_availability_from_week" ][ $i ] );
					$availability[ $i ]['to']   = wc_clean( $_POST[ "wc_appointment_availability_to_week" ][ $i ] );
				break;
				case 'days' :
					$availability[ $i ]['from'] = wc_clean( $_POST[ "wc_appointment_availability_from_day_of_week" ][ $i ] );
					$availability[ $i ]['to']   = wc_clean( $_POST[ "wc_appointment_availability_to_day_of_week" ][ $i ] );
				break;
				/* DEPRECATED
				case 'time_date' :
					$availability[ $i ]['from'] = wc_appointment_sanitize_time( $_POST[ 'wc_appointment_availability_from_time' ][ $i ] );
					$availability[ $i ]['to']   = wc_appointment_sanitize_time( $_POST[ 'wc_appointment_availability_to_time' ][ $i ] );
					$availability[ $i ]['on'] 	= wc_clean( $_POST[ 'wc_appointment_availability_on_date' ][ $i ] );
				break;
				*/
				case 'time' :
				case 'time:1' :
				case 'time:2' :
				case 'time:3' :
				case 'time:4' :
				case 'time:5' :
				case 'time:6' :
				case 'time:7' :
					$availability[ $i ]['from'] = wc_appointment_sanitize_time( $_POST[ "wc_appointment_availability_from_time" ][ $i ] );
					$availability[ $i ]['to']   = wc_appointment_sanitize_time( $_POST[ "wc_appointment_availability_to_time" ][ $i ] );
				break;
				case 'time:range' :
					$availability[ $i ]['from'] = wc_appointment_sanitize_time( $_POST[ "wc_appointment_availability_from_time" ][ $i ] );
					$availability[ $i ]['to']   = wc_appointment_sanitize_time( $_POST[ "wc_appointment_availability_to_time" ][ $i ] );

					$availability[ $i ]['from_date'] = wc_clean( $_POST[ 'wc_appointment_availability_from_date' ][ $i ] );
					$availability[ $i ]['to_date']   = wc_clean( $_POST[ 'wc_appointment_availability_to_date' ][ $i ] );
				break;
			}
		}
		update_user_meta( $user_id, '_wc_appointment_availability', $availability );
	}
}

return new WC_Appointments_Admin_Staff_Profile();
