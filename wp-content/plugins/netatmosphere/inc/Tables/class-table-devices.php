<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); 

class NAS_Devices {
	//protected static $tn = NAS_DB_Tool::GetDevicesTableName();
	
	public $id;
	public $id_device;
	public $id_module;
	public $last_refresh;
	public $module_type;
	public $meter_location;
	public $module_name;
	public $date_setup;
	public $coord_latitude;
	public $coord_longitude;

	
}

?>