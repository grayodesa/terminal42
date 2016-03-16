<?php

class WC_Appointments_Admin_Calendar {

	private $appointments;

	/**
	 * Output the calendar view
	 */
	public function output() {
		if ( version_compare( WOOCOMMERCE_VERSION, '2.3', '<' ) ) {
			wp_enqueue_script( 'chosen' );
			wc_enqueue_js( '$( "select#calendar-appointments-filter" ).chosen();' );
		} else {
			wp_enqueue_script( 'wc-enhanced-select' );
		}

		$product_filter = isset( $_REQUEST['filter_appointments'] ) ? absint( $_REQUEST['filter_appointments'] ) : '';
		$view           = isset( $_REQUEST['view'] ) && $_REQUEST['view'] == 'day' ? 'day' : 'month';

		if ( $view == 'day' ) {
			$day            = isset( $_REQUEST['calendar_day'] ) ? wc_clean( $_REQUEST['calendar_day'] ) : date( 'Y-m-d' );

			$this->appointments = WC_Appointments_Controller::get_appointments_in_date_range(
				strtotime( 'midnight', strtotime( $day ) ),
				strtotime( 'midnight +1 day -1 min', strtotime( $day ) ),
				$product_filter,
				false
			);
		} else {
			$month          = isset( $_REQUEST['calendar_month'] ) ? absint( $_REQUEST['calendar_month'] ) : date( 'n' );
			$year           = isset( $_REQUEST['calendar_year'] ) ? absint( $_REQUEST['calendar_year'] ) : date( 'Y' );

			if ( $year < ( date( 'Y' ) - 10 ) || $year > 2100 )
				$year = date( 'Y' );

			if ( $month > 12 ) {
				$month = 1;
				$year ++;
			}

			if ( $month < 1 ) {
				$month = 1;
				$year --;
			}

			$start_of_week = absint( get_option( 'start_of_week', 1 ) );
			$last_day      = date( 't', strtotime( "$year-$month-01" ) );
			$start_date_w  = absint( date( 'w', strtotime( "$year-$month-01" ) ) );
			$end_date_w    = absint( date( 'w', strtotime( "$year-$month-$last_day" ) ) );

			// Calc day offset
			$day_offset = $start_date_w - $start_of_week;
			$day_offset = $day_offset >= 0 ? $day_offset : 7 - abs( $day_offset );

			// Calc end day offset
			$end_day_offset = 7 - ( $last_day % 7 ) - $day_offset;
			$end_day_offset = $end_day_offset >= 0 && $end_day_offset < 7 ? $end_day_offset : 7 - abs( $end_day_offset );
			
			// We want to get the last minute of the day, so we will go forward one day to midnight and subtract a min
			$end_day_offset = $end_day_offset + 1;

			$start_timestamp   = strtotime( "-{$day_offset} day", strtotime( "$year-$month-01" ) );
			$end_timestamp     = strtotime( "+{$end_day_offset} day midnight -1 min", strtotime( "$year-$month-$last_day" ) );

			$this->appointments     = WC_Appointments_Controller::get_appointments_in_date_range(
				$start_timestamp,
				$end_timestamp,
				$product_filter,
				false
			);
		}

		include( 'views/html-calendar-' . $view . '.php' );
	}

	/**
	 * List appointments for a day
	 *
	 * @param  [type] $day
	 * @param  [type] $month
	 * @param  [type] $year
	 * @return [type]
	 */
	public function list_appointments( $day, $month, $year ) {
		$date_start = strtotime( "$year-$month-$day 00:00" );
		$date_end   = strtotime( "$year-$month-$day 23:59" );

		foreach ( $this->appointments as $appointment ) {
			if (
				( $appointment->start >= $date_start && $appointment->start < $date_end ) ||
				( $appointment->start < $date_start && $appointment->end > $date_end ) ||
				( $appointment->end > $date_start && $appointment->end <= $date_end )
				) {
				echo '<li><a href="' . admin_url( 'post.php?post=' . $appointment->id . '&action=edit' ) . '">';
					echo '<strong>#' . $appointment->id . ' - ';
					if ( $product = $appointment->get_product() ) {
						echo $product->get_title();
					}
					echo '</strong>';
					echo '<ul>';
						if ( ( $customer = $appointment->get_customer() ) && ! empty( $customer->name ) ) {
							echo '<li>' . __( 'Scheduled by', 'woocommerce-appointments' ) . ' ' . $customer->name . '</li>';
						}
						echo '<li>';
						if ( $appointment->is_all_day() )
							echo __( 'All Day', 'woocommerce-appointments' );
						else
							echo $appointment->get_start_date( '', 'g:ia' ) . '&mdash;' . $appointment->get_end_date( '', 'g:ia' );
						echo '</li>';
						if ( $staff = $appointment->get_staff_member() )
							echo '<li>' . __( 'Staff #', 'woocommerce-appointments' ) . $staff->ID . ' - ' . $staff->display_name . '</li>';
					echo '</ul></a>';
				echo '</li>';
			}
		}
	}

	/**
	 * List appointments on a day
	 */
	public function list_appointments_for_day() {
		$appointments_by_time = array();
		$all_day_appointments = array();
		$unqiue_ids       = array();

		foreach ( $this->appointments as $appointment ) {
			if ( $appointment->is_all_day() ) {
				$all_day_appointments[] = $appointment;
			} else {
				$start_time = $appointment->get_start_date( '', 'Gi' );

				if ( ! isset( $appointments_by_time[ $start_time ] ) )
					$appointments_by_time[ $start_time ] = array();

				$appointments_by_time[ $start_time ][] = $appointment;
			}
			$unqiue_ids[] = $appointment->product_id . $appointment->staff_id;
		}

		ksort( $appointments_by_time );

		$unqiue_ids = array_flip( $unqiue_ids );
		$index      = 0;
		$colours    = array( '#3498db', '#34495e', '#1abc9c', '#2ecc71', '#f1c40f', '#e67e22', '#e74c3c', '#2980b9', '#8e44ad', '#2c3e50', '#16a085', '#27ae60', '#f39c12', '#d35400', '#c0392b' );

		foreach ( $unqiue_ids as $key => $value ) {
			if ( isset( $colours[ $index ] ) )
				$unqiue_ids[ $key ] = $colours[ $index ];
			else
				$unqiue_ids[ $key ] = $this->random_color();

			$index++;
		}

		$column = 0;

		foreach ( $all_day_appointments as $appointment ) {
			echo '<li data-tip="' . $this->get_tip( $appointment ) . '" style="background: ' . $unqiue_ids[ $appointment->product_id . $appointment->staff_id ] . '; left:' . 100 * $column . 'px; top: 0; bottom: 0;"><a href="' . admin_url( 'post.php?post=' . $appointment->id . '&action=edit' ) . '">#' . $appointment->id . '</a></li>';
			$column++;
		}

		$start_column = $column;
		$last_end     = 0;

		foreach ( $appointments_by_time as $appointments ) {
			foreach ( $appointments as $appointment ) {

				$start_time = $appointment->get_start_date( '', 'Gi' );
				$end_time   = $appointment->get_end_date( '', 'Gi' );
				$height     = ( $end_time - $start_time ) / 1.66666667;
				
				if ( $height < 30 ) {
					$height = 30;
				}

				if ( $last_end > $start_time )
					$column++;
				else
					$column = $start_column;

				echo '<li data-tip="' . $this->get_tip( $appointment ) . '" style="background: ' . $unqiue_ids[ $appointment->product_id . $appointment->staff_id ] . '; left:' . 100 * $column . 'px; top: ' . ( $start_time * 60 ) / 100 . 'px; height: ' . $height . 'px;"><a href="' . admin_url( 'post.php?post=' . $appointment->id . '&action=edit' ) . '">#' . $appointment->id . '</a></li>';

				if ( $end_time > $last_end )
					$last_end = $end_time;
			}
		}
	}

	/**
	 * Get a random colour
	 */
	public function random_color() {
		return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
	}

	/**
	 * Get a tooltip in day view
	 * @param  object $appointment
	 * @return string
	 */
	public function get_tip( $appointment ) {
		$return = "";

		$return .= '#' . $appointment->id . ' - ';
		if ( $product = $appointment->get_product() ) {
			$return .= $product->get_title();
		}
		if ( ( $customer = $appointment->get_customer() ) && ! empty( $customer->name ) ) {
			$return .= '<br/>' . __( 'Scheduled by', 'woocommerce-appointments' ) . ' ' . $customer->name;
		}
		if ( $staff = $appointment->get_staff_member() )
			$return .= '<br/>' . __( 'Staff #', 'woocommerce-appointments' ) . $staff->ID . ' - ' . $staff->display_name;

		return esc_attr( $return );
	}

	/**
	 * Filters products for narrowing search
	 */
	public function product_filters() {
		$filters = array();

		$products = WC_Appointments_Admin::get_appointment_products();

		foreach ( $products as $product ) {
			$filters[ $product->ID ] = $product->post_title;

			$staff = wc_appointment_get_product_staff( $product->ID );

			foreach ( $staff as $staff ) {
				$filters[ $staff->ID ] = '&nbsp;&nbsp;&nbsp;' . $staff->display_name;
			}
		}

		return $filters;
	}

	/**
	 * Filters staff for narrowing search
	 */
	public function staff_filters() {
		$filters = array();

		$staff = WC_Appointments_Admin::get_appointment_staff();

		foreach ( $staff as $staff ) {
			$filters[ $staff->ID ] = $staff->display_name;
		}

		return $filters;
	}

}