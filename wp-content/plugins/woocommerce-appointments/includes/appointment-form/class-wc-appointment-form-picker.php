<?php
/**
 * Picker class
 */
abstract class WC_Appointment_Form_Picker {

	protected $appointment_form;
	protected $args = array();

	/**
	 * Get the label for the field based on appointment durations and type
	 * @param  string $text text to insert into label string
	 * @return string
	 */
	protected function get_field_label( $text ) {
		return sprintf( '%s', $text );
	}

	/**
	 * Get the min date in date picker format
	 * @return string
	 */
	protected function get_min_date() {
		$js_string = '';
		$min_date  = $this->appointment_form->product->get_min_date();
		if ( $min_date['value'] ) {
			$unit = strtolower( substr( $min_date['unit'], 0, 1 ) );

			if ( in_array( $unit, array( 'd', 'w', 'y', 'm' ) ) ) {
				$js_string = "+{$min_date['value']}{$unit}";
			} elseif ( 'h' === $unit ) {
				$current_d = date( 'd', current_time( 'timestamp' ) );
				$min_d     = date( 'd', strtotime( "+{$min_date['value']} hour", current_time( 'timestamp' ) ) );
				$js_string = "+" . ( $current_d == $min_d ? 0 : 1 ) . "d";
			}
		}
		return $js_string;
	}

	/**
	 * Get the max date in date picker format
	 * @return string
	 */
	protected function get_max_date() {
		$js_string = '';
		$max_date  = $this->appointment_form->product->get_max_date();
		$unit      = strtolower( substr( $max_date['unit'], 0, 1 ) );

		if ( in_array( $unit, array( 'd', 'w', 'y', 'm' ) ) ) {
			$js_string = "+{$max_date['value']}{$unit}";
		} elseif ( 'h' === $unit ) {
			$current_d = date( 'd', current_time( 'timestamp' ) );
			$max_d     = date( 'd', strtotime( "+{$max_date['value']}{$unit}", current_time( 'timestamp' ) ) );
			$js_string = "+" . ( $current_d == $max_d ? 0 : 1 ) . "d";
		}
		return $js_string;
	}

	/**
	 * Return args for the field
	 * @return array
	 */
	public function get_args() {
		return $this->args;
	}

}