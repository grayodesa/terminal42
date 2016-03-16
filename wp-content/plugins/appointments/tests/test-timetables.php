<?php

/**
 * Class App_Appointments_Test
 *
 * @group timetables
 */
class App_Timetables_Test extends App_UnitTestCase {

	function test_timetables_cache() {
		global $appointments;

		// Insert a worker
		$args = $this->factory->user->generate_args();
		$user_id_1 = $this->factory->user->create_object( $args );

		$args = $this->factory->user->generate_args();
		$user_id_2 = $this->factory->user->create_object( $args );

		$service_id = appointments_insert_service( array( 'name' => 'My Service' ) );

		$args = array(
			'ID' => $user_id_1,
			'price' => '19.7',
			'services_provided' => array( $service_id ),
			'dummy' => true
		);
		$result = appointments_insert_worker( $args );

		$time = time();
		$date = date( 'Y-m-01', $time );
		$capacity = appointments_get_capacity();
		$date_start = strtotime("{$date} 00:00");
		$service = appointments_get_service($service_id);
		$min_step_time = $appointments->get_min_time() * 60;
		$step = (!empty($service->duration) ? $service->duration : $min_step_time) * 60;
		$key = $date_start . '-' . $capacity . '-0' . '-' . $appointments->worker . '-' . date( 'Ym', $appointments->local_time ) . '-' . $step;



		// WORKERS

		// Trigger the timetables cache
		appointments_get_timetable( $date_start, $capacity );
		// This saves the timetables
		do_action( 'shutdown' );
		$timetables_cache = get_transient( 'app_timetables' );
		$this->assertNotEmpty( $timetables_cache[ $key . '-' . $appointments->service ] );

		// Insert another worker
		$args = array(
			'ID' => $user_id_2,
			'price' => '19.7',
			'services_provided' => array( $service_id ),
			'dummy' => true
		);
		appointments_insert_worker( $args );
		$timetables_cache = get_transient( 'app_timetables' );
		$this->assertEmpty( $timetables_cache[ $key . '-' . $appointments->service ] );

		// Trigger the timetables cache
		appointments_get_timetable( $date_start, $capacity );

		// This saves the timetables
		do_action( 'shutdown' );

		$timetables_cache = get_transient( 'app_timetables' );
		$this->assertNotEmpty( $timetables_cache[ $key . '-' . $appointments->service ] );

		// Delete a worker
		appointments_delete_worker( $user_id_2 );
		$timetables_cache = get_transient( 'app_timetables' );
		$this->assertEmpty( $timetables_cache[ $key . '-' . $appointments->service ] );

		// Trigger the timetables cache
		appointments_get_timetable( $date_start, $capacity );

		// This saves the timetables
		do_action( 'shutdown' );

		$timetables_cache = get_transient( 'app_timetables' );
		$this->assertNotEmpty( $timetables_cache[ $key . '-' . $appointments->service ] );

		// Update worker
		appointments_update_worker( $user_id_1, array( 'price' => '10' ) );
		$timetables_cache = get_transient( 'app_timetables' );
		$this->assertEmpty( $timetables_cache[ $key . '-' . $appointments->service ] );


		// APPOINTMENTS

		// Trigger the timetables cache
		appointments_get_timetable( $date_start, $capacity );

		// This saves the timetables
		do_action( 'shutdown' );

		$timetables_cache = get_transient( 'app_timetables' );
		$this->assertNotEmpty( $timetables_cache[ $key . '-' . $appointments->service ] );

		// Insert appointment
		$args = array(
			'user' => $user_id_2,
			'email' => 'tester@tester.com',
			'name' => 'Tester',
			'phone' => '667788',
			'address' => 'An address',
			'city' => 'Madrid',
			'service' => $service_id,
			'worker' => $user_id_1,
			'price' => '90',
			'date' => 'December 18, 2024',
			'time' => '07:30',
			'note' => 'It\'s a note',
			'status' => 'paid',
			'location' => 5,
			'gcal_updated' => '2015-12-01',
			'gcal_ID' => 'test'
		);
		$app_id = appointments_insert_appointment( $args );

		$timetables_cache = get_transient( 'app_timetables' );
		$this->assertEmpty( $timetables_cache[ $key . '-' . $appointments->service ] );

		// Trigger the timetables cache
		appointments_get_timetable( $date_start, $capacity );

		// This saves the timetables
		do_action( 'shutdown' );

		$timetables_cache = get_transient( 'app_timetables' );
		$this->assertNotEmpty( $timetables_cache[ $key . '-' . $appointments->service ] );

		// Update Appointment
		appointments_update_appointment( $app_id, array( 'address' => 'New address' ) );
		$timetables_cache = get_transient( 'app_timetables' );
		$this->assertEmpty( $timetables_cache[ $key . '-' . $appointments->service ] );

		// Trigger the timetables cache
		appointments_get_timetable( $date_start, $capacity );

		// This saves the timetables
		do_action( 'shutdown' );

		$timetables_cache = get_transient( 'app_timetables' );
		$this->assertNotEmpty( $timetables_cache[ $key . '-' . $appointments->service ] );

		// Delete appointment
		appointments_delete_appointment( $app_id );
		$timetables_cache = get_transient( 'app_timetables' );
		$this->assertEmpty( $timetables_cache[ $key . '-' . $appointments->service ] );


		// SERVICES

		// Insert a service

		// Trigger the timetables cache
		appointments_get_timetable( $date_start, $capacity );

		// This saves the timetables
		do_action( 'shutdown' );

		$timetables_cache = get_transient( 'app_timetables' );
		$this->assertNotEmpty( $timetables_cache[ $key . '-' . $appointments->service ] );

		$service_id_2 = appointments_insert_service( array( 'name' => 'My Service 2' ) );

		$timetables_cache = get_transient( 'app_timetables' );
		$this->assertEmpty( $timetables_cache[ $key . '-' . $appointments->service ] );

		// Trigger the timetables cache
		appointments_get_timetable( $date_start, $capacity );

		// This saves the timetables
		do_action( 'shutdown' );

		$timetables_cache = get_transient( 'app_timetables' );
		$this->assertNotEmpty( $timetables_cache[ $key . '-' . $appointments->service ] );

		// Update service
		appointments_update_service( $service_id_2, array( 'name' => 'My Service updated' ) );
		$timetables_cache = get_transient( 'app_timetables' );
		$this->assertEmpty( $timetables_cache[ $key . '-' . $appointments->service ] );

		// Trigger the timetables cache
		appointments_get_timetable( $date_start, $capacity );

		// This saves the timetables
		do_action( 'shutdown' );

		$timetables_cache = get_transient( 'app_timetables' );
		$this->assertNotEmpty( $timetables_cache[ $key . '-' . $appointments->service ] );

		// Delete service
		appointments_delete_service( $service_id_2 );
		$timetables_cache = get_transient( 'app_timetables' );
		$this->assertEmpty( $timetables_cache[ $key . '-' . $appointments->service ] );

	}

}