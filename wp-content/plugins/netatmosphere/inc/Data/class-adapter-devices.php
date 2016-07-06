<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); 

class NAS_Devices_Adapter {
    public static $devices = null;
    protected static $base_station = null;
    
    public static function getAll() {
		if(self::$devices==null || count(self::$devices) <= 0)
			self::$devices = self::getFiltered();
			
		return self::$devices;
	}
	public static function getActive() {
		return self::getFiltered ( " synch = 1 " );
	}
	public static function getFiltered($where = "1") {
		global $wpdb;

		$v = NAS_DB_Tool::GetDevicesDetailsViewName();
		$sql = "SELECT * FROM $v WHERE $where;";
		$devices = $wpdb->get_results($sql, ARRAY_A);
		
		return $devices;
	}
    
	public static function clear() {
        $sql = array();
        $sql[] = NAS_DB_Tool::GetTruncateTableSql( NAS_DB_Tool::GetDevicesTypesTableName() );
        $sql[] = NAS_DB_Tool::GetTruncateTableSql( NAS_DB_Tool::GetDevicesTableName() );

        NAS_DB_Tool::executeQuery( $sql );
        
        self::$devices = null;
    }
    public static function getLastRefreshDate($utc = false) {
        global $wpdb;
		
		$sql = 'SELECT max(last_refresh) FROM ' . NAS_DB_Tool::GetDevicesTableName();
		$latest = $wpdb->get_row($sql, ARRAY_N);
        
        if ( null === $latest || !isset($latest) || !isset($latest[0]) )
            return "-";
        
        if ( $utc )
            return $latest[0];
            
        return NAS_Plugin::utc2date($latest[0]);
    }
	public static function getDeviceCount() {
		global $wpdb;
		
        $tableName = NAS_DB_Tool::GetDevicesTableName();
		$sql = "SELECT count(*) FROM $tableName where synch = 1 ";
		//debugMP('pr', 'netatmosphere', $sql, __FILE__, __LINE__);
		$latest = $wpdb->get_row($sql, ARRAY_N);
		//debugMP('pr', 'netatmosphere', $latest, __FILE__, __LINE__);
		if($latest === null) {
			return 0;
		}
		return $latest[0];
	}
    public static function getMeasureTypesCount() {
        return count(self::getActive());
    }
    public static function getBaseStation() {
        
        if( null === self::$base_station ) {
        
            $baseStations = self::getFiltered("module_type = 'NAMain'");
            if(is_array($baseStations)) {
                self::$base_station = $baseStations[0];
            }
            else {
                self::$base_station = $baseStations;
            }
        }
        return self::$base_station;
	}
    public static function getLocation() {
        $baseStation = self::getBaseStation();
                
        $coord = array();
        $coord['lat'] = floatval( $baseStation['coord_latitude'] );
        $coord['lng'] = floatval( $baseStation['coord_longitude'] );
        
        return $coord;
    }
    public static function getElevation() {
        $baseStation = self::getBaseStation();
        $ele =  $baseStation['elevation'] ;
        
        if ( null == $ele ) 
            return 0;
        
        return floatval ( $ele );
    }
    public static function updateAllSynch( $blackList ) {
        global $wpdb;
        $tn = NAS_DB_Tool::GetDevicesTableName();
        
        // set all synch flags and unset those from the blacklist
        $sql = "UPDATE $tn SET synch = 1";
        $res = $wpdb->query ( $sql );
        
        // end here if the args are empty
        if ( $blackList == null || empty ( $blackList ) )
            return;
        
        // is it a string and contains the delims?
        if ( is_string ( $blackList) && strpos( $blackList, ',') !== false )
            $blackList = explode ( ",", $blackList );
       
        if ( is_array( $blackList ) ) {
            foreach( $blackList as $item ) {
                self::updateSynch ( $item, false );
            }
        } else {
            self::updateSynch ( $blackList, false );
        }
    }
    protected static function updateSynch( $moduleId, $synch ) {
        global $wpdb;
        $tn = NAS_DB_Tool::GetDevicesTableName();
        
        return $wpdb->update( $tn, array ( 'synch' => $synch ), array ( 'id_module' => $moduleId ) );
    }
}

?>