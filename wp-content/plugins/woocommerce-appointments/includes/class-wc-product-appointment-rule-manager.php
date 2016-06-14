<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class that parses and returns rules for appointable products
 */
class WC_Product_Appointment_Rule_Manager {

	/**
	 * Get a range and put value inside each day
	 *
	 * @param  string $from
	 * @param  string $to
	 * @param  mixed $value
	 * @return array
	 */
	private static function get_custom_range( $from, $to, $value ) {
		$availability = array();
		$from_date    = strtotime( $from );
		$to_date      = strtotime( $to );

		if ( empty( $to ) || empty( $from ) || $to_date < $from_date )
			return;

		// We have at least 1 day, even if from_date == to_date
		$numdays = 1 + ( $to_date - $from_date ) / 60 / 60 / 24;

		for ( $i = 0; $i < $numdays; $i ++ ) {
			$year  = date( 'Y', strtotime( "+{$i} days", $from_date ) );
			$month = date( 'n', strtotime( "+{$i} days", $from_date ) );
			$day   = date( 'j', strtotime( "+{$i} days", $from_date ) );

			$availability[ $year ][ $month ][ $day ] = $value;
		}

		return $availability;
	}

	/**
	 * Get a range and put value inside each day
	 *
	 * @param  string $from
	 * @param  string $to
	 * @param  mixed $value
	 * @return array
	 */
	private static function get_months_range( $from, $to, $value ) {
		$months = array();
		$diff   = $to - $from;
		$diff   = ( $diff < 0 ) ? 12 + $diff : $diff;
		$month  = $from;

		for ( $i = 0; $i <= $diff; $i ++ ) {
			$months[ $month ] = $value;

			$month ++;

			if ( $month > 52 )
				$month = 1;
		}

		return $months;
	}

	/**
	 * Get a range and put value inside each day
	 *
	 * @param  string $from
	 * @param  string $to
	 * @param  mixed $value
	 * @return array
	 */
	private static function get_weeks_range( $from, $to, $value ) {
		$weeks = array();
		$diff  = $to - $from;
		$diff  = ( $diff < 0 ) ? 52 + $diff : $diff;
		$week  = $from;

		for ( $i = 0; $i <= $diff; $i ++ ) {
			$weeks[ $week ] = $value;

			$week ++;

			if ( $week > 52 )
				$week = 1;
		}

		return $weeks;
	}

	/**
	 * Get a range and put value inside each day
	 *
	 * @param  string $from
	 * @param  string $to
	 * @param  mixed $value
	 * @return array
	 */
	private static function get_days_range( $from, $to, $value ) {
		$day_of_week  = $from;
		$diff         = $to - $from;
		$diff         = ( $diff < 0 ) ? 7 + $diff : $diff;
		$days         = array();

		for ( $i = 0; $i <= $diff; $i ++ ) {
			$days[ $day_of_week ] = $value;

			$day_of_week ++;

			if ( $day_of_week > 7 ) {
				$day_of_week = 1;
			}
		}

		return $days;
	}
	
	/**
	 * Get a range and put value inside each day
	 *
	 * DEPRECATED
	 *
	 * @param  string $from
	 * @param  string $to
	 * @param  mixed $value
	 * @return array
	 */
	private static function get_time_date_range( $from, $to, $value, $date = '' ) {
		return array(
			'from' => $from,
			'to'   => $to,
			'rule' => $value,
			'date' => $date,
		);
	}

	/**
	 * Get a range and put value inside each day
	 *
	 * @param  string $from
	 * @param  string $to
	 * @param  mixed $value
	 * @return array
	 */
	private static function get_time_range( $from, $to, $value, $day = 0 ) {
		return array(
			'from' => $from,
			'to'   => $to,
			'rule' => $value,
			'day'  => $day,
		);
	}
	
	/**
	 * Get a time range for a set of custom dates
	 * @param  string $from_date
	 * @param  string $to_date
	 * @param  string $from_time
	 * @param  string $to_time
	 * @param  mixed $value
	 * @return array
	 */
	private static function get_time_range_for_custom_date( $from_date, $to_date, $from_time, $to_time, $value ) {
		$time_range = array(
			'from' => $from_time,
			'to'   => $to_time,
			'rule' => $value,
		);
		return self::get_custom_range( $from_date, $to_date, $time_range );
	}

	/**
	 * Get duration range
	 * @param  [type] $from
	 * @param  [type] $to
	 * @param  [type] $value
	 * @return [type]
	 */
	private static function get_duration_range( $from, $to, $value ) {
		return array(
			'from' => $from,
			'to'   => $to,
			'rule' => $value,
			);
	}

	/**
	 * Get slots range
	 * @param  [type] $from
	 * @param  [type] $to
	 * @param  [type] $value
	 * @return [type]
	 */
	private static function get_slots_range( $from, $to, $value ) {
		return array(
			'from' => $from,
			'to'   => $to,
			'rule' => $value,
			);
	}
	
	/**
	 * Get quant range
	 * @param  [type] $from
	 * @param  [type] $to
	 * @param  [type] $value
	 * @return [type]
	 */
	private static function get_quant_range( $from, $to, $value ) {
		return array(
			'from' => $from,
			'to'   => $to,
			'rule' => $value
			);
	}
	
	/**
	 * Process and return formatted cost rules
	 * @param  $rules array
	 * @return array
	 */
	public static function process_pricing_rules( $rules ) {
		$costs = array();
		$index = 1;
		
		if ( ! is_array( $rules ) ) {
			return $costs;
		}
		
		// Go through rules
		foreach ( $rules as $key => $fields ) {
			if ( empty( $fields['cost'] ) && empty( $fields['base_cost'] ) && empty( $fields['override_slot'] ) ) {
				continue;
			}

			$cost           = apply_filters( 'woocommerce_appointments_process_cost_rules_cost', $fields['cost'], $fields, $key );
			$modifier       = $fields['modifier'];
			$base_cost      = apply_filters( 'woocommerce_appointments_process_cost_rules_base_cost', $fields['base_cost'], $fields, $key );
			$base_modifier  = $fields['base_modifier'];
			$override_slot = apply_filters( 'woocommerce_appointments_process_cost_rules_override_slot', ( isset( $fields['override_slot'] ) ? $fields['override_slot'] : '' ), $fields, $key );

			$cost_array = array(
				'base'     => array( $base_modifier, $base_cost ),
				'slot'     => array( $modifier, $cost ),
				'override' => $override_slot,
			);

			$type_function = self::get_type_function( $fields['type'] );
			if ( 'get_time_range_for_custom_date' === $type_function ) {
				$type_costs = self::$type_function( $fields['from_date'], $fields['to_date'], $fields['from'], $fields['to'], $cost_array );
			} else {
				$type_costs = self::$type_function( $fields['from'], $fields['to'], $cost_array );
			}

			// Ensure day gets specified for time: rules
			if ( strrpos( $fields['type'], 'time:' ) === 0 && 'time:range' !== $fields['type'] ) {
				list( , $day ) = explode( ':', $fields['type'] );
				$type_costs['day'] = absint( $day );
			}

			if ( $type_costs ) {
				$costs[ $index ] = array( $fields['type'], $type_costs );
				$index ++;
			}
		}

		return $costs;
	}
	
	/**
	 * Returns a function name (for this class) that returns our time or date range
	 * @param  string $type rule type
	 * @return string       function name
	 */
	public static function get_type_function( $type ) {
		if ( 'time:range' === $type ) {
			return 'get_time_range_for_custom_date';
		}
		return strrpos( $type, 'time:' ) === 0 ? 'get_time_range' : 'get_' . $type . '_range';
	}

	/**
	 * Process and return formatted availability rules
	 * @param  $rules array
	 * @return array
	 */
	public static function process_availability_rules( $rules, $which ) {
		$processed_rules = array();

		if ( empty( $rules ) ) {
			return $processed_rules;
		}

		// See what types of rules we have before getting the rules themselves
		$rule_types = array();

		foreach ( $rules as $fields ) {
			if ( empty( $fields['appointable'] ) ) {
				continue;
			}
			$rule_types[] = $fields['type'];
		}
		$rule_types = array_filter( $rule_types );

		// Go through rules
		foreach ( $rules as $fields ) {
			if ( empty( $fields['appointable'] ) ) {
				continue;
			}
						
			$type_function     = self::get_type_function( $fields['type'] );
			if ( 'get_time_range_for_custom_date' === $type_function ) {
				$type_availability = self::$type_function( $fields['from_date'], $fields['to_date'], $fields['from'], $fields['to'], $fields['appointable'] === 'yes' ? true : false );
			} else {
				$type_availability = self::$type_function( $fields['from'], $fields['to'], $fields['appointable'] === 'yes' ? true : false );
			}
			
			$priority = isset( $fields['priority'] ) ? $fields['priority'] : 10;
			$qty = isset( $fields['qty'] ) ? absint( $fields['qty'] ) : 0;

			// Ensure day gets specified for time: rules
			if ( strrpos( $fields['type'], 'time:' ) === 0 && 'time:range' !== $fields['type'] ) {
				list( , $day ) = explode( ':', $fields['type'] );
				$type_availability['day'] = absint( $day );
			}
			
			// Ensure date gets specified for time_date rule
			if ( in_array( 'time_date', $rule_types ) ) {
				$type_availability['date'] = isset ( $fields['on'] ) ? $fields['on'] : '';
			}
			
			// Enable days when user defines time rules, but not day rules
			if ( ! in_array( 'custom', $rule_types ) && ! in_array( 'days', $rule_types ) && ! in_array( 'months', $rule_types ) && ! in_array( 'weeks', $rule_types ) ) {
				if ( 'time:range' === $fields['type'] ) {
					if ( 'yes' === $fields['appointable'] ) {
						$processed_rules[] = array( 'custom', self::get_custom_range( $fields['from_date'], $fields['to_date'], true ), $priority, $which, $qty );
					}
				} elseif ( strrpos( $fields['type'], 'time_date' ) === 0 ) {
					if ( $fields['appointable'] === 'yes' ) {
						$processed_rules[] = array( 'time_date', self::get_time_date_range( $fields['on'], $fields['on'], true, $fields['on'] ), $priority, $which, $qty );
					}
				} else {
					if ( strrpos( $fields['type'], 'time:' ) === 0 ) {
						list( , $day ) = explode( ':', $fields['type'] );
						if ( $fields['appointable'] === 'yes' ) {
							$processed_rules[] = array( 'days', self::get_days_range( $day, $day, true ), $priority, $which, $qty );
						}
					} elseif ( strrpos( $fields['type'], 'time' ) === 0 ) {
						if ( $fields['appointable'] === 'yes' ) {
							$processed_rules[] = array( 'days', self::get_days_range( 0, 7, true ), $priority, $which, $qty );
						}
					}
				}
			}
			
			if ( $type_availability ) {
				$processed_rules[] = array( $fields['type'], $type_availability, $priority, $which, $qty );
			}
		}

		return $processed_rules;
	}
}