<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WC_Appointments_Admin_Save_Meta_Box {
	public $id;
	public $title;
	public $context;
	public $priority;
	public $post_types;

	public function __construct() {
		$this->id         = 'woocommerce-appointment-save';
		$this->title      = __( 'Save Appointment', 'woocommerce-appointments' );
		$this->context    = 'side';
		$this->priority   = 'high';
		$this->post_types = array( 'wc_appointment' );

		add_action( 'save_post', array( $this, 'meta_box_save' ), 10, 1 );
	}

	public function meta_box_inner( $post ) {
		wp_nonce_field( 'wc_appointments_save_appointment_meta_box', 'wc_appointments_save_appointment_meta_box_nonce' );

		?>
		<input type="submit" class="button save_order button-primary tips" name="save" value="<?php _e( 'Save Appointment', 'woocommerce-appointments' ); ?>" data-tip="<?php _e( 'Save/update the appointment', 'woocommerce-appointments' ); ?>" />
		<?php
	}

	public function meta_box_save( $post_id ) {
		if ( ! isset( $_POST['wc_appointments_save_appointment_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['wc_appointments_save_appointment_meta_box_nonce'], 'wc_appointments_save_appointment_meta_box' ) ) {
			return $post_id;
		}

      	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
      	}

		if ( ! in_array( $_POST['post_type'], $this->post_types ) ) {
			return $post_id;
		}
	}
}

return new WC_Appointments_Admin_Save_Meta_Box();