<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * 
 */
class NAS_Options {
    private static $instance = NULL;
	
    protected $isFahrenheit = false; 
    protected $ismmHg = false; 
    
    protected $autoDataMergeEnabled = false;
    protected $dataMergeIntervalName = '10min';
    
    protected $autoDeviceRefreshEnabled = false;
    protected $deviceRefreshIntervalName = 'weekly';
    protected $ignoreModuleIds = null;
    
    // another plugin "WP Business Intelligence Lite" installed to display charts or not?
    protected $isWPBI_installed = null;
    
   /**
	* static method for getting the instance of this singleton object
	*
	* @return NAS_Options
	*/
	public static function getInstance() {

		if (is_null(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}
    
    private function __construct() {
        $options = get_option("nas_admin_options_display");
		if(isset($options)) {
            $this->isFahrenheit = ( isset( $options['temperature_unit']) && $options['temperature_unit'] == NAS_Measure_Units::TEMPERATURE_FAHRENHEIT );
            $this->ismmHg = ( isset( $options['pressure_unit']) && $options['pressure_unit'] == NAS_Measure_Units::PRESSURE_MMHG );
        }
        
        $options = get_option("nas_admin_options_caching");
		if(isset($options)) {
            if( isset( $options['nas_caching_auto_merge_enabled']) && !empty( $options['nas_caching_auto_merge_enabled'] ) )
                $this->autoDataMergeEnabled = $options['nas_caching_auto_merge_enabled'];
            if( isset( $options['nas_caching_merge_interval']) && !empty( $options['nas_caching_merge_interval'] ) )
                $this->dataMergeIntervalName = $options['nas_caching_merge_interval'];

            if( isset( $options['nas_caching_device_auto_refresh_enabled']) && !empty( $options['nas_caching_device_auto_refresh_enabled'] ) )
                $this->autoDeviceRefreshEnabled = $options['nas_caching_device_auto_refresh_enabled'];
            if( isset( $options['nas_caching_device_refresh_interval']) && !empty( $options['nas_caching_device_refresh_interval'] ) )
                $this->deviceRefreshIntervalName = $options['nas_caching_device_refresh_interval'];
                
            if( isset( $options['nas_synch_devices_ignore_modules']) && !empty( $options['nas_synch_devices_ignore_modules'] ) )
                $this->ignoreModuleIds = preg_split( "/[\s,;]+/", $options['nas_synch_devices_ignore_modules']);
        }
    }
    
    public static function GetCronIntervals() {
        return NAS_Time_Units::GetAllNames();
    }
    public static function GetTemperatureUnitNames() {
        $a = array();
        $a[] = NAS_Measure_Units::TEMPERATURE_CELSIUS;
        $a[] = NAS_Measure_Units::TEMPERATURE_FAHRENHEIT;
        return $a;
    }
    public static function GetPressureUnitNames() {
        $a = array();
        $a[] = NAS_Measure_Units::PRESSURE_HPA;
        $a[] = NAS_Measure_Units::PRESSURE_MMHG;
        return $a;
    }
    public static function GetTimeZones() {
        return DateTimeZone::listIdentifiers();
    }
    public static function GetTimeZone() {
        return get_option('timezone_string');
    }
    public static function GetLastSynchedModuleId() {
        return get_option( 'nas_synch_data_merge_lastmodule' );
    }
    public static function SetLastSynchedModuleId( $id_module ) {
        update_option( 'nas_synch_data_merge_lastmodule', $id_module );
    }
    
    

    public function IsChartActive() {
        if ( null === $this->isWPBI_installed ) {
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            $this->isWPBI_installed = is_plugin_active(NAS_WPBI_PLUGIN_FILE);
        }
        return $this->isWPBI_installed;
    }
    public function IsFahrenheit() {
        return $this->isFahrenheit;
    }
    public function IsmmHg() {
        return $this->ismmHg;
    }
    
    public function GetAutoDataMergeEnabled() {
        return $this->autoDataMergeEnabled;
    }
    public function GetDataMergeIntervalName() {
        return $this->dataMergeIntervalName;
    }

    public function GetAutoDeviceRefreshEnabled() {
        return $this->autoDeviceRefreshEnabled;
    }
    public function GetDeviceRefreshIntervalName() {
        return $this->deviceRefreshIntervalName;
    }
    
    public function GetIgnoreModuleIds() {
        return $this->ignoreModuleIds;
    }
    
}

?>