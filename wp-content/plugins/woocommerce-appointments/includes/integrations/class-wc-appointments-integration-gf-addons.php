<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Gravity Froms Addons integration class.
 */
class WC_Appointments_Integration_GF_Addons {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'appointment_form_calculated_appointment_cost', array( $this, 'adjust_appointment_cost' ), 10, 3 );
	}
	
	/**
	 * Adjust the final appointment cost
	 */	
	public function adjust_appointment_cost( $appointment_cost, $appointment_form, $posted ) {
		$addon_costs  = 0;
		
		$gform_form_id = isset( $posted['gform_form_id'] ) ? $posted['gform_form_id'] : '';

		$form_meta = RGFormsModel::get_form_meta( $gform_form_id );
		
		// var_dump( $form_meta['fields'] );

		if ( ! empty( $form_meta ) ) {
			
			$valid_fields = array();
			
			foreach ( $form_meta['fields'] as $field ) {

				if (
					$field['type'] 		== 'product' 		||
					$field['inputType'] == 'hiddenproduct' 	||
					$field['type'] 		== 'total' 			||
					( isset( $field['displayOnly'] ) && $field['displayOnly'] )
				) {
					continue;
				}
				
				$valid_fields[] = $field['id'];
				
				// var_dump( $field['inputs'] );
								
				if ( ! empty( $field['inputs'] ) ) {
					
					foreach ( $field['inputs'] as $k => $v ) {
				
						$valid_fields[] = preg_replace( '#\.#', '_', $v['id'] );
					
					}
					
				}

			}
			
			// var_dump( $valid_fields );
			
			if ( ! empty( $valid_fields ) ) {
		
				foreach ( $valid_fields as $valid ) {
					$addon_cost = 0;
					
					if ( isset( $posted[ 'input_' . $valid ] ) ) {
						
						$pieces = explode("|", $posted[ 'input_' . $valid ]);
						
						$addon_cost += isset( $pieces[1] ) ? $pieces[1] : 0;
						
					}
					
					$addon_costs += $addon_cost;	
				}
			
			}
			
		}

		return $appointment_cost + $addon_costs;
	}
}

$GLOBALS['wc_appointments_integration_gf_addons'] = new WC_Appointments_Integration_GF_Addons();