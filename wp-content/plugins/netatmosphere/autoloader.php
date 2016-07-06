<?php defined( 'NAS_PLUGIN_ROOT' ) or die( 'No script kiddies please!' );

function nas_autoload($class_name) {
	static $classMap = array (
        // base folder
        'NAS_Cron'              => 'inc/class-nas-cron.php',
		'NAS_Plugin'  		    => 'inc/class-nas-plugin.php',
        'NetAtmo_Client_Wrapper'=> 'inc/class-netatmo-client-wrapper.php',
        // commons
        'NAS_Device_Locations'  => 'inc/Commons/enum-device-locations.php',
        'NAS_Measure_Units'     => 'inc/Commons/enum-measure-units.php',
        'NAS_Time_Units'        => 'inc/Commons/dropdown-time-units.php',
        // data
        'NAS_Devices_Adapter'   => 'inc/Data/class-adapter-devices.php',
        'NAS_Data_Adapter'      => 'inc/Data/class-adapter-data.php',
        'NAS_Data_Cell'         => 'inc/Data/class-nas-data-cell.php',
        'NAS_Data_Row'          => 'inc/Data/class-nas-data-row.php',
        'NAS_Data_Table'        => 'inc/Data/class-nas-data-table.php',
        // shortcodes
        'NAS_Shortcode_Base'        => 'inc/Shortcodes/class-shortcode-base.php',
        'NAS_Shortcode_Data'        => 'inc/Shortcodes/class-shortcode-data.php',
        'NAS_Shortcode_Devices'     => 'inc/Shortcodes/class-shortcode-devices.php',
        'NAS_Shortcode_Schedules'   => 'inc/Shortcodes/class-shortcode-schedules.php',
        // tools
		'NAS_DB_Tool'           => 'inc/Tools/class-tool-db.php',
        'NAS_Options'           => 'inc/Tools/class-tool-options.php',
        'NAS_Tool_Weather'      => 'inc/Tools/class-tool-weather.php',
        'NAS_Synch_Data'        => 'inc/Tools/class-synch-data.php',
        'NAS_Synch_Devices'     => 'inc/Tools/class-synch-devices.php',
        // widgets
        'NAS_Widget'    => 'inc/Widgets/class-widget.php',
        // admin
		'NAS_Admin_Plugin'      => 'admin/class-admin-nas-plugin.php',
		'NAS_Admin_Menu'  		=> 'admin/class-admin-menu.php',
		'NAS_Admin_Options' 	=> 'admin/class-admin-options.php',
	);
	
	// add references to lib
	//$classMap['parseCSV'] = 'lib/parsecsv/parsecsv.lib.php';
	//$classMap['Encoding'] = 'lib/forceutf8/Encoding.php';


    try {
        if (isset($classMap[$class_name])) {
            require_once(NAS_PLUGIN_ROOT . $classMap[$class_name]);
        }
    } catch(Exception $ex) {
        echo "<b>Cant load file!</b>";
    }
}

// register a function for autoloading required classes
spl_autoload_register('nas_autoload');

?>