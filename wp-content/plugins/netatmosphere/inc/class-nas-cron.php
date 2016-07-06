<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if(!defined('NAS_CRON_DATA_EVENT')) define('NAS_CRON_DATA_EVENT', 'nas_cron_execute_data_merge_event');
if(!defined('NAS_CRON_DEVICES_EVENT')) define('NAS_CRON_DEVICES_EVENT', 'nas_cron_execute_device_refresh_event');

add_action(NAS_CRON_DATA_EVENT,     'nas_cron_execute_data_merge');
add_action(NAS_CRON_DEVICES_EVENT,  'nas_cron_execute_device_refresh');

if ( !function_exists( 'nas_cron_execute_data_merge' ) ) {

    function nas_cron_execute_data_merge() {
        NAS_Plugin::debugFile('', __FILE__, __LINE__, __FUNCTION__);
        //return;
        
        try {
            set_time_limit ( NAS_Cron::MAX_SCRIPT_TIME );
            
            $d = new NAS_Synch_Data();
            $d->merge();
        } catch ( \Exception $ex) {
            NAS_Plugin::debugFile($ex, __FILE__, __LINE__, __FUNCTION__);
            NAS_Plugin::sendAdminMail($ex, 'nas_cron_execute_data_merge', __FILE__, __LINE__);
        }
    }
}
if ( !function_exists( 'nas_cron_execute_device_refresh' ) ) {

    function nas_cron_execute_device_refresh() {
        NAS_Plugin::debugFile('', __FILE__, __LINE__, __FUNCTION__);
        //return;
        
        try {
            set_time_limit ( NAS_Cron::MAX_SCRIPT_TIME );

            $d = new NAS_Synch_Devices();
            $d->refresh();
        } catch ( \Exception $ex) {
            NAS_Plugin::debugFile($ex, __FILE__, __LINE__, __FUNCTION__);
            NAS_Plugin::sendAdminMail($ex, 'nas_cron_execute_device_refresh', __FILE__, __LINE__);
        }
    }
}

/**
 * 
 */
class NAS_Cron {
    const MAX_SCRIPT_TIME = 120;
    const REQUEST_SINGLE_DELAY = 300; // 5 min
    const MIN_DELAY_BETWEEN_REQUESTS = 600; // 10 min
    
    public static function recreateAll() {
        try {           
            self::clearAll();
            self::scheduleAll();
		} 
		catch(Exception $ex) {
			NAS_Plugin::sendAdminMail(
				print_r($ex),
				__('Recreate of crons failed', 'netatmosphere'));
		}
    }
    public static function clearAll() {
        self::clearDeviceRefresh();
        self::clearDataMerge();
    }
    public static function scheduleAll($initial = false) {
        self::scheduleDeviceRefresh();
        self::scheduleDataMerge();
        
        if ( $initial ) {
            self::requestAll();
        }
    }
    public static function requestAll() {
        self::requestSingleDeviceRefresh();
        self::requestSingleDataMerge(5);
    }
    
    /* device schedules & callbacks */
    public static function getNextDeviceRefresh() {
        return NAS_Plugin::utc2date( wp_next_scheduled( NAS_CRON_DEVICES_EVENT ) );
    }
    public static function clearDeviceRefresh() {
       	wp_clear_scheduled_hook( NAS_CRON_DEVICES_EVENT );
    }
    public static function scheduleDeviceRefresh( $seconds = 20 ) {
        $op = NAS_Options::GetInstance();
        if ( $op->GetAutoDeviceRefreshEnabled() ) {
            $schedule = $op->GetDeviceRefreshIntervalName();
            wp_schedule_event(time() + $seconds, $schedule, NAS_CRON_DEVICES_EVENT );
        }
    }
    public static function requestSingleDeviceRefresh($seconds = 0) {
        wp_schedule_single_event(time() + $seconds, NAS_CRON_DEVICES_EVENT );
    }
    
    
    /* data schedules & callbacks */
    public static function getNextDataMerge() {
        return NAS_Plugin::utc2date( wp_next_scheduled( NAS_CRON_DATA_EVENT ) );
    }
    public static function clearDataMerge() {
       	wp_clear_scheduled_hook( NAS_CRON_DATA_EVENT );
    }
    public static function scheduleDataMerge( $seconds = 30 ) {
        $op = NAS_Options::GetInstance();
        if ( $op->GetAutoDataMergeEnabled() ) {
            $schedule = $op->GetDataMergeIntervalName();
            wp_schedule_event(time() + $seconds, $schedule, NAS_CRON_DATA_EVENT );
        }
    }
    public static function requestSingleDataMerge($seconds = 0) {
        wp_schedule_single_event(time() + $seconds, NAS_CRON_DATA_EVENT );
    }
}

?>