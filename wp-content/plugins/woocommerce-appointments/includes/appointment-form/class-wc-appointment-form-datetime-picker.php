<?php
/**
 * Class dependencies
 */
if ( ! class_exists( 'WC_Appointment_Form_Date_Picker' ) ) {
	include_once( 'class-wc-appointment-form-date-picker.php' );
}

/**
 * Date and time Picker class
 */
class WC_Appointment_Form_Datetime_Picker extends WC_Appointment_Form_Date_Picker {

	private $field_type = 'datetime-picker';
	private $field_name = 'start_date';

	/**
	 * Constructor
	 * @param object $appointment_form The appointment form which called this picker
	 */
	public function __construct( $appointment_form ) {
		$this->appointment_form                    = $appointment_form;
		$this->args                            = array();
		$this->args['type']                    = $this->field_type;
		$this->args['name']                    = $this->field_name;
		$this->args['min_date']                = $this->appointment_form->product->get_min_date();
		$this->args['max_date']                = $this->appointment_form->product->get_max_date();
		$this->args['default_availability']    = $this->appointment_form->product->get_default_availability();
		$this->args['label']                   = $this->get_field_label( __( 'Date', 'woocommerce-appointments' ) );
		$this->args['min_date_js']             = $this->get_min_date();
		$this->args['max_date_js']             = $this->get_max_date();
		$this->args['interval']                = $this->appointment_form->product->wc_appointment_duration;
		$this->args['availability_rules']      = array();
		$this->args['availability_rules'][0]   = $this->appointment_form->product->get_availability_rules();
		
		// Try to guess the first available day -- temporarily switch to 'day' when calculating the slots since we just want to pull out a close date,
		// and not try to filter by tiny minute|hour slots
		add_filter( 'woocommerce_appointments_get_duration_unit', array( __CLASS__, 'set_duration_to_day' ) );
		$this->args['default_date'] = date( 'Y-m-d', $this->get_default_date() );
		remove_filter( 'woocommerce_appointments_get_duration_unit', array( __CLASS__, 'set_duration_to_day' ) );


		if ( $this->appointment_form->product->has_staff() ) {
			foreach ( $this->appointment_form->product->get_staff() as $staff ) {
				$this->args['availability_rules'][ $staff->ID ] = $this->appointment_form->product->get_availability_rules( $staff->ID );
			}
		}

		if ( 'hour' === $this->appointment_form->product->wc_appointment_duration_unit ) {
			$this->args['interval'] = $this->args['interval'] * 60;
		} else if ( 'day' === $this->appointment_form->product->wc_appointment_duration_unit ) {
			$this->args['interval'] = $this->args['interval'] * 60 * 24;
		}

		$this->find_fully_scheduled_slots();
	}
	
	public static function set_duration_to_day() {
		return 'day';
	}
}