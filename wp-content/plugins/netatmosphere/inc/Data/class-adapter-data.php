<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); 

class NAS_Data_Adapter {
	const MEASURE_INTERVALL_SEC = 300;
	const TRANSFER_INTERVALL_SEC = 600;
    
	public static function clear() {
        $sql = array();
        $sql[] = NAS_DB_Tool::GetTruncateTableSql( NAS_DB_Tool::GetDataTableName() );

        NAS_DB_Tool::executeQuery( $sql );
    }
	public static function getLastRecord($utc = false) {
        global $wpdb;
		
		$sql = 'SELECT max(time_stamp) FROM ' . NAS_DB_Tool::GetDataTableName();
		$latest = $wpdb->get_row($sql, ARRAY_N);
        
        if ( null === $latest || !isset($latest) || !isset($latest[0]) )
            return "-";
        
        if ( $utc )
            return $latest[0];
            
        return NAS_Plugin::utc2date($latest[0]);
    }
	public static function getCount() {
		global $wpdb;
		
        $tableName = NAS_DB_Tool::GetDataTableName();
		$sql = "SELECT count(*) FROM $tableName";
		$r = $wpdb->get_row($sql, ARRAY_N);
		if($r === null) {
			return 0;
		}
		return $r[0];
	}
    public static function getLatest($location = null) {
		global $wpdb;
		
		// prepare the possible outdoor values from device cache	
		$tableName = NAS_DB_Tool::GetDataTableName();
		$view = NAS_DB_Tool::GetDataOverviewViewName(); 
		$sql = "SELECT distinct time_stamp, v.id_module, v.meter_location, v.value_category, value 
				FROM $tableName as t, $view as v 
				WHERE v.max_time = t.time_stamp 
				AND v.id_module = t.module_id 
				AND v.value_category = t.value_category "
			. (isset($location) ? " AND v.meter_location = '$location' " : "")
            . " AND time_stamp >= '" . date('Y-m-d 00:00:00') . "' 
                ORDER BY v.id_module, v.value_category ";
            
		$results = $wpdb->get_results($sql, ARRAY_A);
		
        self::refmtDate( $results );
		self::ensureEntities( $results );
		
		return $results;
	}
    public static function getAvgOfDay($date = null, $location = null) {
        global $wpdb;
        
        if( null === $date ) {
            // this is working, but not really useful:
            /*$view = NAS_DB_Tool::GetDataDetailsViewName(); 
            $sql = "SELECT max( v.time_stamp ) as MAX_TIME_STAMP FROM $view as v WHERE 1 "
                . (isset($location) ? " AND v.meter_location = '$location' " : "");
            $results = $wpdb->get_results( $sql, ARRAY_A );

            if ( null !== $results ) {
                $date = date ( 'Y-m-d 00:00:00', strtotime( $results[0]['MAX_TIME_STAMP'] ) );
            } else {
                $date = date( 'Y-m-d 00:00:00' ) ;
            }*/
            
            // only use current date:
            $date = date( 'Y-m-d 00:00:00' ) ;
        }
        $date = new DateTime( $date );
        
        //$tableName = NAS_DB_Tool::GetDataTableName();
		$view = NAS_DB_Tool::GetDataDetailsViewName(); 
        $sql = "SELECT '" . $date->format('Y-m-d') . "' as time_stamp, v.id_module, v.meter_location, v.value_category, truncate(avg(v.value), 2) as value 
                FROM $view as v 
                WHERE 1
                AND v.time_stamp > '" . $date->format('Y-m-d') . " 00:00:00' 
                AND v.time_stamp <= '" . $date->format('Y-m-d') . " 23:59:59' "
			. (isset($location) ? " AND v.meter_location = '$location' " : "") . "
                GROUP BY v.id_module, v.meter_location, v.value_category";
        
        $results = $wpdb->get_results($sql, ARRAY_A);
        //NAS_Plugin::debugFile($sql, __FILE__, __LINE__, __FUNCTION__);
        
        self::refmtDate( $results );
		self::ensureEntities($results);
        
        return $results;
    }
    
    public static function getDataOverview( ) {
        global $wpdb;
        
        $vn = NAS_DB_Tool::GetDataOverviewViewName();
        $sql = "SELECT * FROM $vn as v ";
        
        $results = $wpdb->get_results($sql, ARRAY_A);
        
        self::refmtDate( $results, array('max_time', 'min_time') );
        
        return $results;
    }
    
    public static function results2row($results, $category = '', $categoryHead = '') {
        $list = new NAS_Data_Row($category, $categoryHead);
                
        foreach($results as $key => $row) {
            $e = new NAS_Data_Cell();
            $e->loadFromResult($row);
            
            $list->addElement($e);
        }
        
        return $list;
    }
    
    protected static function refmtDate ( &$results, $time_col_names = 'time_stamp' ) {

        foreach ( $results as $key => $row ) {
            
            if ( is_array ( $time_col_names ) ) {
                foreach( $time_col_names as $col_name ) {
                    if ( isset ( $row[$col_name] ) ) 
                        $results[$key][$col_name] = NAS_Plugin::utc2date ( $row[$col_name] );
                }
            } else {
                if ( isset ( $row[$time_col_names] ) ) 
                    $results[$key][$time_col_names] = NAS_Plugin::utc2date ( $row[$time_col_names] );
            }
        }
    }
    
    // helper function to convert units
    protected static function ensureEntities(&$results) {
        
        if ( null === $results || count( $results ) <= 0 ) 
            return $results;
        
        // create the default units for the module meter types
		foreach($results as $key => $row) {
			$results[$key]['value_unit'] = self::measureType2Entity($results[$key]['value_category']);
		}
		
		$options = NAS_Options::getInstance();
		
        foreach($results as $key => $row) {
            if($options->isFahrenheit() && $row['value_category'] == "Temperature") {
                $results[$key]['value'] = number_format( NAS_Tool_Weather::celsius2fahrenheit(floatval($row['value'])), 2);
                $results[$key]['value_unit'] = '&deg;F';
            } elseif($options->ismmHg() && $row['value_category'] == "Pressure") {
                $results[$key]['value'] = number_format( NAS_Tool_Weather::hpa2mmhg(floatval($row['value'])), 2);
                $results[$key]['value_unit'] = 'mmHg';
            }
        }
    }
    protected static function measureType2Entity($type) {
		switch(strtolower($type)) {
			case "temperature":
				return "&deg;C";
				break;
			case "noise":
				return "dB";
				break;
			case "humidity":
				return "%";
				break;
			case "co2":
				return "ppm";
				break;
			case "pressure":
				return "mbar";
				break;
			case "rain":
				return "mm";
				break;
			case "windspeed":
				return "km/h";
				break;
			case "winddirection":
				return "&deg;";
				break;
			default:
				return null;
				break;
		}
	}
}

?>