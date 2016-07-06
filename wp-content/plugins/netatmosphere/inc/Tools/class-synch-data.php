<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); 

class NAS_Synch_Data {
    
    /** 
     * merge data from netatmo to local database
     */
	public function merge() {
		
        $html = '';
        
		try {
            // list of all modules
			$devices = NAS_Devices_Adapter::getActive();
            
            
            // now rotate the devices to continue with the last started one
            $lastModuleId = NAS_Options::GetLastSynchedModuleId( );
            if ( $lastModuleId !== false && !empty ( $lastModuleId) ) {
                $cnt = count ( $devices );
                while ( $cnt > 0 && $devices[0]['id_module'] != $lastModuleId ) {
                    array_push($devices, array_shift($devices));
                    $cnt --;
                }
            }

            
            $finished = true;
            foreach($devices as $module) {
                // merge per module + type
                $finished &= $this->mergePerModule($module['id_device'], $module['id_module'], $module['module_meter_type'], $module['date_setup'], true);
            }
            
            
            // are we at the newest measures? if yes, its ok to proceed with interval, otherwise request a new merge
            if ( false == $finished ) {
                $diff = 0; $nextSchedule = NAS_Cron::getNextDataMerge( );
                if( $nextSchedule !== false ) {
                    $diff = strtotime( $nextSchedule ) - time();
                }
                if( $nextSchedule === false ||  $diff > NAS_Cron::MIN_DELAY_BETWEEN_REQUESTS ) {
                    // request immediatelly, caus we dont wait for new ones, we wanna merge the old history
                    NAS_Cron::requestSingleDataMerge( );
                }
            }
		}
		catch(\Exception $ex)
		{
			$html .= "<strong>ERROR during merging the cache for netatmo data!</strong>";
            NAS_Plugin::debugFile($ex, __FILE__, __LINE__, __FUNCTION__);
		}
        
        return $html;
	}
    /**
     * get start date
     */
    protected function getStartDate($id_module, $type, $date_setup = null) {
        global $wpdb;
        
        $tn = NAS_DB_Tool::GetDataTableName();
        // get the start date 
        // 1. get max per module + type -> start date
        $sql = "SELECT max(time_stamp) FROM $tn WHERE module_id = '$id_module' AND value_category = '$type'";
        $row = $wpdb->get_row($sql, ARRAY_N);

        if( null !== $row && isset( $row ) && isset( $row[0] ) ) {
            //$start = NAS_Plugin::date2utc($row[0]);
            $start = strtotime($row[0]);
            return $start;
        }
        
        if( NAS_Plugin::isDebug() ) {
            // TODO: only for testing:
            NAS_Plugin::debugFile("TODO: only for testing", __FILE__, __LINE__, __FUNCTION__);
            return strtotime(date('Y-m-d 00:00:00'));
        }

        // 2. if nothing is found in cache, try to use setup date 
        if( null !== $date_setup && $date_setup > 0 ) {
            //$start = NAS_Plugin::date2utc($date_setup);
            $start = strtotime($date_setup);
            return $start;
        }
        
        // 3. fallback is NULL caus this will hopefully take the very first records 
        return null;
    }
    /**
     * merge per module and measure type
     */
    protected function mergePerModule($id_device, $id_module, $type, $date_setup, $onlyOnce = false) {
		global $wpdb;
        
        //NAS_Plugin::debugFile('mergePerModule:', __FILE__, __LINE__);
        //NAS_Plugin::debugFile('-------------------------------------', __FILE__, __LINE__);
        NAS_Options::SetLastSynchedModuleId( $id_module );

        // indicates that we reached the most recent measures
		$finished = false;
        // get API client
        $client = NetAtmo_Client_Wrapper::getInstance()->client;
        
        $rows_inserted = 0;

        //NAS_Plugin::debugFile('id_device: ' . $id_device, __FILE__, __LINE__);
        //NAS_Plugin::debugFile('id_module: ' . $id_module, __FILE__, __LINE__);
        //NAS_Plugin::debugFile('type: ' . $type, __FILE__, __LINE__);
        
        while( true !== $finished ) {
            
            // get the start date here, cause after insert, it modifies of course
            $date_start = $this->getStartDate($id_module, $type, $date_setup);
            // always use current time for upper limit to avoid truncations caus of upper limit
            $date_end = time();

            //NAS_Plugin::debugFile($date_start, __FILE__, __LINE__, __FUNCTION__ ." - date_start");
            //NAS_Plugin::debugFile($date_end, __FILE__, __LINE__, __FUNCTION__ ." - date_end");

            try {

                $res = $client->getMeasure($id_device, $id_module, "max", $type, $date_start, $date_end, NULL, "false", NULL);
                if (isset($res)) {

                    foreach($res as $key => $row) {
                        // just convert time (in sec) to mysql datetime format (no timezone conversion here!)
                        $timestamp = date ("Y-m-d H:i:s", $key);
                        $value = $row[0];
                        
                        // check if its maybe already in cache db
                        $sql = "SELECT * FROM " . NAS_DB_Tool::getDataTableName() 
                                                    . " WHERE time_stamp = '$timestamp' "
                                                    . " AND module_id = '$id_module' "
                                                    . " AND value_category = '$type' "
                                                    . " AND value = '$value' ";
                        $exists = $wpdb->get_row($sql, ARRAY_N);

                        if($exists === null) { 
                            $insert = $wpdb->insert(NAS_DB_Tool::getDataTableName(),
                                array(
                                    "time_stamp" 	=> $timestamp,
                                    "module_id"     => $id_module,
                                    "value_category"=> $type,
                                    "value" 		=> $value));
                            
                            $rows_inserted++;
                        }
                                
                    }

                    $finished = (count($res) < Netatmo\Common\NAStationConstants::GETMEASURE_LIMIT_MAX);
                    if ( true === $onlyOnce )
                        break;
                }
            }
            catch(NAClientException $ex)
            {
                NAS_Plugin::debugFile($ex, __FILE__, __LINE__);
                
                $html .= "<p><strong>" 
                    . __('ERROR', 'netatmosphere') 
                    . ":</strong>" 
                    . __('Something happend during retrieving the data from netatmo.com!', 'netatmosphere') 
                    . "</p>";
                    
                // skip possible others for this device
                break;
            }
        }
        
        //NAS_Plugin::debugFile($rows_inserted, __FILE__, __LINE__, __FUNCTION__ . " - totally rows inserted");
        //NAS_Plugin::debugFile($finished, __FILE__, __LINE__, __FUNCTION__ . " - finished?");
        //NAS_Plugin::debugFile('-------------------------------------', __FILE__, __LINE__);
        
        return $finished;
    }
}

?>