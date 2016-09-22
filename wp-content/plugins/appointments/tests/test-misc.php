<?php


/**
 * Class App_Activate_Test
 * @group misc
 */
class App_Misc_Test extends App_UnitTestCase {

	public function test_datepick_localfile() {
		add_filter( 'locale', function() {
			return 'ca';
		});
		$file = appointments()->datepick_localfile();
		$this->assertEquals('/js/jquery.datepick-ca.js', $file);

		add_filter( 'locale', function() {
			return 'es_ES';
		});
		$file = appointments()->datepick_localfile();
		$this->assertEquals('/js/jquery.datepick-es.js', $file);


		add_filter( 'locale', function() {
			return 'ca';
		});
		define( 'APP_FLAG_NO_GLOB', true );
		$file = appointments()->datepick_localfile();
		$this->assertEquals('/js/jquery.datepick-ca.js', $file);


	}

	public function test_datepick_local_months() {
		add_filter( 'locale', function() {
			return 'es_ES';
		});
		$months = appointments()->datepick_local_months();
		$this->assertCount( 12, $months );
		$this->assertEquals(
			array(
				" Enero",
				"Febrero",
				"Marzo",
				"Abril",
				"Mayo",
				"Junio",
				"Julio",
				"Agosto",
				"Septiembre",
				"Octubre",
				"Noviembre",
				"Diciembre"
			),
			$months
		);
	}

	public function test_datepick_abb_local_months() {
		add_filter( 'locale', function() {
			return 'es_ES';
		});
		$months = appointments()->datepick_abb_local_months();
		$this->assertCount( 12, $months );
		$this->assertEquals(
			array(
				" Ene",
				"Feb",
				"Mar",
				"Abr",
				"May",
				"Jun",
				"Jul",
				"Ago",
				"Sep",
				"Oct",
				"Nov",
				"Dic"
			),
			$months
		);
	}
}