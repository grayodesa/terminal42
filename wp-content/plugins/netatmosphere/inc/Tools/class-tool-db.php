<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); 

class NAS_DB_Tool {
	
	protected static function getTableNamePrefix() {
		global $wpdb;
		return $wpdb->prefix . "netatmosphere_"; 
	}
	protected static function getViewNamePrefix() {
		return "v_" . self::getTableNamePrefix();
	}
	protected static function getCollation() {
		global $wpdb;
		return $wpdb->get_charset_collate();
	}
	public static function executeQuery( $sql ) {
		global $wpdb;
		try {
			if(is_array($sql)) {
				foreach($sql as $s) {
					$wpdb->query( $s );
				}
			} else {
				$wpdb->query( $sql );
			}
		}
		catch(Exception $ex)
		{
			NAS_Plugin::debugBar($ex, 'netatmosphere - executeQuerys()', __FILE__, __LINE__);
		}
	}
	
	
	/* 
	 * TABLE / VIEW NAMES -------------------------
	 */
	public static function GetDataTableName() {
		$tableNamePrefix = self::getTableNamePrefix();
		return "${tableNamePrefix}data";
	}
	public static function GetDevicesTableName() {
		$tableNamePrefix = self::getTableNamePrefix();
		return "${tableNamePrefix}devices";
	}
	public static function GetDevicesTypesTableName() {
		$tableNamePrefix = self::getTableNamePrefix();
		return "${tableNamePrefix}devices_types";
	}
	
	public static function GetDataDetailsViewName() {
		$viewNamePrefix = self::getViewNamePrefix();
		return "${viewNamePrefix}data_details";
	}
	public static function GetDataOverviewViewName() {
		$viewNamePrefix = self::getViewNamePrefix();
		return "${viewNamePrefix}data_overview";
	}
	public static function GetDevicesDetailsViewName() {
		$viewNamePrefix = self::getViewNamePrefix();
		return "${viewNamePrefix}devices_details";
	}
    public static function GetDataGroupByHourViewName() {
        $viewNamePrefix = self::getViewNamePrefix();
		return "${viewNamePrefix}data_group_hour";
    }
    public static function GetDataGroupByDayViewName() {
        $viewNamePrefix = self::getViewNamePrefix();
		return "${viewNamePrefix}data_group_day";
    }


    /* 
     * SELECT -------------------------------
     */
    
    protected static function GetSelectSQLWithTimeFilter( $timeFilter = '> curdate()', $location = 'outdoor', $category = 'Temperature', $nl2br = false ) {
        $vn = self::GetDataDetailsViewName();
        $sql = "SELECT unix_timestamp(time_stamp) as time_stamp, value as {$location}_{$category} 
            FROM $vn  
            where value_category = '$category' 
            and meter_location = '$location' 
            and time_stamp $timeFilter ";
            
        if( true === $nl2br )
            $sql = nl2br( $sql );
        
        return $sql;
    }
    public static function GetSelectSQLForLastHours( $hours = 2, $location = 'outdoor', $category = 'Temperature', $nl2br = true ) {
        return self::GetSelectSQLWithTimeFilter( "> DATE_SUB(UTC_TIMESTAMP(), INTERVAL $hours HOUR)", $location, $category, $nl2br );
    }
    public static function GetSelectSQLForToday( $location = 'outdoor', $category = 'Temperature', $nl2br = true ) {
        return self::GetSelectSQLWithTimeFilter( "> curdate()", $location, $category, $nl2br );
    }
    public static function GetSelectSQLSummaryForLastDays( $location = 'outdoor', $category = 'Temperature', $days = 7, $summaryType = 'avg', $groupInterval = 3600, $nl2br = true ) {
        $vn = self::GetDataDetailsViewName();
        $sql = "SELECT unix_timestamp(time_stamp)-MOD(unix_timestamp(time_stamp),$groupInterval) as time_stamp, truncate({$summaryType}(value),2) as value 
                    FROM $vn 
                    where value_category = '$category' and meter_location = '$location' and time_stamp > DATE_SUB(UTC_TIMESTAMP(), INTERVAL {$days} DAY) 
                    group by 1";
                    
        if( true === $nl2br )
            $sql = nl2br( $sql );
        
        return $sql;
    }
    
	
	/* 
	 * DEFAULT DATA -------------------------
	 */
	public static function InsertDefaultData() {
		//global $wpdb;
		
		//$wpdb->replace(self::GetGenderTableName(), array("ID" => 1, "Code" => "m", "Description" => "maennlich"));
	}

	/*
	 * DROPS's
	 */
    public static function DropDataTable() {
        $sql = self::GetDropDataTable();
        self::executeQuery( $sql );
    }
    public static function GetDropDataTable() {
        $sql = array();
		
		$sql[] = "DROP TABLE " . self::GetDataTableName() . ";";
		
		return $sql;
    }
    public static function DropDeviceTable() {
        $sql = self::GetDropDeviceTable();
        self::executeQuery( $sql );
    }
    public static function GetDropDeviceTable() {
        $sql = array();
		
		$sql[] = "DROP TABLE " . self::GetDevicesTypesTableName() . ";";
		$sql[] = "DROP TABLE " . self::GetDevicesTableName() . ";";
		
		return $sql;
    }
	public static function DropAllTables() {
		// create abstract weather data table if not existing
		$sql = self::GetAllDropTables();
		if(is_array($sql)) {
			foreach($sql as $s) {
				self::executeQuery( $s );
			}
		} else {
			self::executeQuery( $sql );
		}
	}
	public static function GetAllDropTables() {
		$sql = array();
		
		$sql[] = "DROP TABLE " . self::GetDataTableName() . ";";
		$sql[] = "DROP TABLE " . self::GetDevicesTypesTableName() . ";";
		$sql[] = "DROP TABLE " . self::GetDevicesTableName() . ";";
		
		return $sql;
	}
	public static function GetDropTableSql($tableName) {
		return "DROP TABLE " . $tableName . ";";
	}
	public static function DropAllViews() {
		// create abstract weather data table if not existing
		$sql = self::GetAllDropViews();
		if(is_array($sql)) {
			foreach($sql as $s) {
				self::executeQuery( $s );
			}
		} else {
			self::executeQuery( $sql );
		}
	}
	public static function GetAllDropViews() {
		$sql = array();
		
		$sql[] = self::GetDropViewSql( self::GetDataDetailsViewName() );
		$sql[] = self::GetDropViewSql( self::GetDataOverviewViewName() );
		$sql[] = self::GetDropViewSql( self::GetDevicesDetailsViewName() );
        $sql[] = self::GetDropViewSql( self::GetDataGroupByHourViewName() );
        $sql[] = self::GetDropViewSql( self::GetDataGroupByDayViewName() );
		
		return $sql;
	}
	public static function GetDropViewSql($viewName) {
		return "DROP VIEW " . $viewName . ";";
	}

	/*
	 * TRUNCATE's
	 */
	public static function TruncateAllTables() {
		$sql = self::GetAllTruncateTables();
		if(is_array($sql)) {
			foreach($sql as $s) {
				self::executeQuery($s);
			}
		} else {
			self::executeQuery( $sql );
		}
	}
	public static function GetAllTruncateTables() {
		$sql = array();
		
		$sql[] = self::GetTruncateTableSql( self::GetDataTableName() );
		$sql[] = self::GetTruncateTableSql( self::GetDevicesTypesTableName() );
		$sql[] = self::GetTruncateTableSql( self::GetDevicesTableName() );
		
		return $sql;
	}
	public static function GetTruncateTableSql($tableName) {
		return "TRUNCATE TABLE " . $tableName . ";";
	}
	
	/*
	 * CREATE's
	 */
	public static function CreateAllTables() {
		// include for dbDelta
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		
		try {
		
			// create abstract weather data table if not existing
			$sql = self::GetAllCreateTables();
			if(is_array($sql)) {
				foreach($sql as $s) {
					dbDelta($s);
				}
			} else {
				dbDelta( $sql );
			}
		} catch(\Exception $ex) {
			NAS_Plugin::debugFile($ex, __FILE__, __LINE__);
		}
	}
	public static function GetAllCreateTables() {
		$sql = array();
		
		$sql[] = self::GetCreateTable4Data();
		$sql[] = self::GetCreateTable4Devices();
		$sql[] = self::GetCreateTable4DevicesTypes();
		
		return $sql;
	}	
	public static function CreateAllViews() {
		$sql = self::GetAllCreateViews();
		if(is_array($sql)) {
			foreach($sql as $s) {
				self::executeQuery($s);
			}
		} else {
			self::executeQuery( $sql );
		}
	}
	public static function GetAllCreateViews() {
		$sql = array();
		
		$sql[] = self::GetCreateViewDevicesDetails();
		$sql[] = self::GetCreateViewDataDetails();
		$sql[] = self::GetCreateViewDataOverview();
        $sql[] = self::GetCreateViewGroupByHour();
        $sql[] = self::GetCreateViewGroupByDay();
		
		return $sql;
	}
	
	/* 
	 * VIEWS -------------------------
	 */
	public static function GetCreateViewDevicesDetails() {
		$tnDevices = self::GetDevicesTableName();
		$tnDevicesTypes = self::GetDevicesTypesTableName();
		
		$vnDeviceDetails = self::GetDevicesDetailsViewName(); 
		$sql = "CREATE OR REPLACE VIEW $vnDeviceDetails AS 
					SELECT d.*, t.module_meter_type FROM $tnDevices as d, $tnDevicesTypes as t
					WHERE d.id_module = t.id_module;";
				
		return $sql;
	}
	public static function GetCreateViewDataDetails() {
		$tnData = self::GetDataTableName();
		$vnDeviceDetails = self::GetDevicesDetailsViewName(); 
		
		$vnDataDetails = self::GetDataDetailsViewName(); 
        
        // was before: SELECT v1.*, t1.time_stamp, t1.value_category, t1.value 
		$sql = "CREATE OR REPLACE VIEW $vnDataDetails AS 
					SELECT v1.id_device, v1.id_module, v1.last_refresh, v1.module_type, v1.meter_location, v1.module_name, v1.date_setup, t1.time_stamp, t1.value, t1.value_category
						FROM $tnData as t1, $vnDeviceDetails as v1
                        WHERE t1.group_type is null 
						AND t1.module_id = v1.id_module 
						AND t1.value_category = v1.module_meter_type;";
        
		return $sql;
	}
	public static function GetCreateViewDataOverview() {
		$vnDataDetails = self::GetDataDetailsViewName(); 
		$vnDataOverview = self::GetDataOverviewViewName(); 
		/** 
		 * NOTE: would be the ideal query, but subqueries are not allowed for VIEWs, unfortunatelly :(
		 $sql = "CREATE OR REPLACE VIEW $v AS 
					SELECT T1.*, DataOverview.* FROM $t2 as T1 
					INNER JOIN 
						(SELECT module_id, value_category, max(value) as max_val, min(value) as min_val, max(time_stamp) as max_time, min(time_stamp) as min_time, count(*) as cnt 
							FROM $t1
							GROUP BY module_id, value_category) as DataOverview 
						ON DataOverview.module_id = T1.id_module 
						AND DataOverview.value_category = T1.module_meter_type; ";
		 */
		$sql = "CREATE OR REPLACE VIEW $vnDataOverview AS 
				SELECT id_device, id_module, last_refresh, module_type, meter_location, module_name, date_setup, value_category, truncate(max(value), 2) as max_val, truncate(min(value), 2) as min_val, max(time_stamp) as max_time, min(time_stamp) as min_time, count(*) as cnt 
					FROM $vnDataDetails 
                    WHERE time_stamp > CONCAT(YEAR(CURDATE()),'-01-01')
					GROUP BY id_device, id_module, last_refresh, module_type, meter_location, module_name, date_setup, value_category; ";
				
		return $sql;
	}
    public static function GetCreateViewGroupByHour() {
        $vn = self::GetDataGroupByHourViewName();
        return self::GetCreateViewGroupBySeconds($vn);
    }
    public static function GetCreateViewGroupByDay() {
        $vn = self::GetDataGroupByDayViewName();
        return self::GetCreateViewGroupBySeconds($vn, 86400);
    }
    protected static function GetCreateViewGroupBySeconds($vn, $seconds = 3600) {
        $tn = self::GetDataTableName();
    
        $sql = "CREATE OR REPLACE VIEW $vn AS 
                SELECT FROM_UNIXTIME(unix_timestamp(time_stamp)-mod(unix_timestamp(time_stamp), $seconds)) as rounded_time_stamp, module_id, value_category, truncate(avg(value), 2) as value_avg, count(*) as cnt, min(value) as value_min, max(value) as value_max 
                    FROM $tn 
                    WHERE group_type is null 
                    GROUP BY FROM_UNIXTIME(unix_timestamp(time_stamp)-mod(unix_timestamp(time_stamp), $seconds)), module_id, value_category";
        
        return $sql;
    }
    
	/* 
	 * TABLES -------------------------
	 */

	public static function GetCreateTable4Data() {
		$table_name = self::GetDataTableName();
		$charset_collate = self::getCollation();

		$sql = "CREATE TABLE $table_name (
			id int NOT NULL AUTO_INCREMENT,
			time_stamp timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
			module_id varchar(55) NOT NULL COMMENT 'ID of the sensor module',
			value_category varchar(55) NOT NULL,
			value FLOAT NULL,
            group_type varchar(1) NULL COMMENT 'Code to identify the group of the values (group to hour, day, ...)',
			UNIQUE KEY id (id),
			INDEX idx_time_stamp (time_stamp, module_id, value_category, value) USING BTREE
		) $charset_collate;";
		
		return $sql;
	}
	
	public static function GetCreateTable4Devices() {
		$tableName = self::GetDevicesTableName();
		$charset_collate = self::getCollation();

		$sql = "CREATE TABLE $tableName (
			id int NOT NULL AUTO_INCREMENT,
			id_device varchar(55) NOT NULL COMMENT 'Base station ID',
			id_module varchar(55) NOT NULL COMMENT 'Module ID',
			last_refresh timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
			module_type varchar(20) NOT NULL COMMENT 'Module Type such as: NAMain, NAModule1, ...',
			meter_location varchar(15) DEFAULT 'outdoor' NOT NULL,
			module_name varchar(55) NULL COMMENT 'Readable name of module',
			date_setup timestamp NULL COMMENT 'UTC of setup',
			coord_latitude float NULL COMMENT 'Coordinates: Latitude',
			coord_longitude float NULL COMMENT 'Coordinates: Longitude',
            elevation float NULL COMMENT 'Elevation at coordinates',
			owned BOOLEAN DEFAULT true NULL COMMENT 'Device owned by me or is it a favorite device',
            synch BOOLEAN DEFAULT true NOT NULL COMMENT 'Synch measurements for this module',
			UNIQUE KEY id (id)
		) $charset_collate;";
		
		
		return $sql;
	}
	
	public static function GetCreateTable4DevicesTypes() {
		$tableName = self::GetDevicesTypesTableName();
		$charset_collate = self::getCollation();
		
		$sql = "CREATE TABLE $tableName (
			id int NOT NULL AUTO_INCREMENT,
			id_module varchar(55) NOT NULL COMMENT 'Module ID',
			module_meter_type varchar(55) NOT NULL COMMENT 'Such as Temperature, Noise, Humidity, ...',
			UNIQUE KEY id (id)
		) $charset_collate;";
		
		return $sql;
	}
	
}

?>