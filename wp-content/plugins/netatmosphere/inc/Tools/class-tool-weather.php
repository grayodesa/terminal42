<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * 
 */
class NAS_Tool_Weather {

    /* pass the NAS_Data_Row object */
    public static function calcMissingValuesInRow( &$row ) {

        if ( empty ( $row ) )
            return;

        $temp = $row->getElementByColumn("Temperature");
        if ( empty ( $temp ) )
            return;
            
        $hum = $row->getElementByColumn("Humidity");
        if ( empty ( $hum ) )
            return;
        
        /* dew point */
        $dewPt = new NAS_Data_Cell();
        $dewPt->category = 'Dewpoint';
        $dewPt->unit = $temp->unit;
        $dewPtValue = null;
        if ( NAS_Options::getInstance()->isFahrenheit() )
            $dewPtValue = self::calcDewPointFahrenheit( $temp->value, $hum->value );
        else 
            $dewPtValue = self::calcDewPoint( $temp->value, $hum->value );
        $dewPt->value = number_format ( $dewPtValue, 2 );
        $row->addElement ( $dewPt );
        
        
        /* cload base */
        /* ready working, but not useful in the lastest/daily widget
        $elevation = NAS_Devices_Adapter::getElevation();
        $cloudBase = new NAS_Data_Cell();
        $cloudBase->category = 'Cloud base';
        $cloudBase->unit = 'm';
        $cloudBase->value = self::calcCloudBase ( $temp->value, $hum->value, $elevation );
        $row->addElement ( $cloudBase );*/
        
        //NAS_Plugin::debugFile($row, __FILE__, __LINE__, __FUNCTION__);
    }
    

    /* helper functions */
    public static function celsius2fahrenheit($celsius) {
		return ($celsius * 1.8) + 32;
	}
	public static function fahrenheit2celsius($fahrenheit) {
		return ($fahrenheit - 32) / 1.8;
	}
	public static function mmhg2hpa($mmhg) {
		return $mmhg * 0.750061561303;
	}
	public static function hpa2mmhg($hpa) {
		return $hpa / 0.750061561303;
	}
	public static function calcDewPoint($tempCelsius, $relHumPercent) {
		return $tempCelsius - ((100 - $relHumPercent) / 5 );
	}
    public static function calcDewPointFahrenheit($tempFahrenheit, $relHumPercent) {
        $tempCelsius = self::fahrenheit2celsius ( $tempFahrenheit );
		return self::celsius2fahrenheit ( $tempCelsius - ((100 - $relHumPercent) / 5 ) );
	}
	public static function foot2meter($foot) {
		return $foot * 0.3048;
	}
	public static function meter2foot($meter) {
		return $meter / 0.3048;
	}
	public static function calcCloudBase($tempCelsius, $relHumPercent, $elevation = 0) {
		// calc temp and dew point in Fahrenheit
		$tf = self::celsius2fahrenheit( $tempCelsius );
		$tdf = self::celsius2fahrenheit( self::calcDewPoint($tempCelsius, $relHumPercent) );
		// calc the cloud base in feet
		$cbf = 1000 * (( $tf - $tdf ) / 4.5);
		
		// convert to meter and add stations elevation
		return self::foot2meter($cbf) + $elevation;
	}
}

?>