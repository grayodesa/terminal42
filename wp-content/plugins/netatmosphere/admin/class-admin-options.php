<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); 

class NAS_Admin_Options {

	private static $instance = NULL;
	
   /**
	* static method for getting the instance of this singleton object
	*
	* @return NAS_Admin_Options
	*/
	public static function getInstance() {

		if (is_null(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	* private constructor; can only instantiate via getInstance() class method
	*/
	protected function __construct() {		
		if ( is_admin() ) {
			add_action( 'admin_init', array(&$this, 'adminOptionsPage') );
		}
	}
	
	function adminOptionsPage() {
        $this->optionsCaching();
		$this->optionsDisplay();
		$this->optionsPluginUninstall();
	}
    
    function optionsCaching() {
		add_settings_section( 
			'nas_admin_options_caching',
			__('Caching Options', 'netatmosphere'),
			'',
			'nas_admin_options_caching'
		);
		/*add_settings_field(
			'nas_caching_enabled',
			__('Limit cache', 'netatmosphere'),
			array($this, 'options_checkbox_callback'),
			'nas_admin_options_caching',
			'nas_admin_options_caching',
			array('nas_caching_enabled', 'nas_admin_options_caching')
		);*/
        add_settings_field(
			'nas_caching_auto_merge_enabled',
			__('Auto merge measurements periodically?', 'netatmosphere'),
			array($this, 'options_checkbox_callback'),
			'nas_admin_options_caching',
			'nas_admin_options_caching',
			array('nas_caching_auto_merge_enabled', 'nas_admin_options_caching')
		);
        add_settings_field(
			'nas_caching_merge_interval',
			__('Data merge interval', 'netatmosphere'),
			array($this, 'options_dropdown_callback'),
			'nas_admin_options_caching',
			'nas_admin_options_caching',
			array('nas_caching_merge_interval', 'nas_admin_options_caching', NAS_Options::GetCronIntervals())
		);
        add_settings_field(
			'nas_caching_device_auto_refresh_enabled',
			__('Auto refresh device list periodically?', 'netatmosphere'),
			array($this, 'options_checkbox_callback'),
			'nas_admin_options_caching',
			'nas_admin_options_caching',
			array('nas_caching_device_auto_refresh_enabled', 'nas_admin_options_caching')
		);
        add_settings_field(
			'nas_caching_device_refresh_interval',
			__('Refresh device interval', 'netatmosphere'),
			array($this, 'options_dropdown_callback'),
			'nas_admin_options_caching',
			'nas_admin_options_caching',
			array('nas_caching_device_refresh_interval', 'nas_admin_options_caching', NAS_Options::GetCronIntervals())
		);
        
        add_settings_field(
			'nas_synch_devices_ignore_modules',
			__('List of module IDs to ignore on synchronization (separat by comma)', 'netatmosphere'),
			array($this, 'options_textarea_callback'),
			'nas_admin_options_caching',
			'nas_admin_options_caching',
			array('nas_synch_devices_ignore_modules', 'nas_admin_options_caching')
		);
		
		register_setting('nas_admin_options_caching', 'nas_admin_options_caching', array(&$this, 'optionsInputValidation') );
	}
	function optionsDisplay() {
		add_settings_section( 
			'nas_admin_options_display',
			__('Display Options', 'netatmosphere'),
			'',
			'nas_admin_options_display'
		);
		add_settings_field(
			'temperature_unit',
			__('Temperature unit', 'netatmosphere'),
			array($this, 'options_dropdown_callback'),
			'nas_admin_options_display',
			'nas_admin_options_display',
			array('temperature_unit', 'nas_admin_options_display', NAS_Options::GetTemperatureUnitNames())
		);
		add_settings_field(
			'pressure_unit',
			__('Pressure unit', 'netatmosphere'),
			array($this, 'options_dropdown_callback'),
			'nas_admin_options_display',
			'nas_admin_options_display',
			array('pressure_unit', 'nas_admin_options_display', NAS_Options::GetPressureUnitNames())
		);
        /*add_settings_field(
			'time_zone',
			__('Time zone', 'netatmosphere'),
			array($this, 'options_dropdown_callback'),
			'nas_admin_options_display',
			'nas_admin_options_display',
			array('time_zone', 'nas_admin_options_display', NAS_Options::GetTimeZones())
		);*/
		register_setting('nas_admin_options_display', 'nas_admin_options_display', array(&$this, 'optionsInputValidation') );
	}
	function optionsPluginUninstall() {
		add_settings_section( 
			'nas_admin_options_uninstall',
			__('Uninstall Options', 'netatmosphere'),
			'',
			'nas_admin_options_uninstall'
		);
		add_settings_field(
			'drop_views',
			__('Drop Views', 'netatmosphere'),
			array($this, 'options_checkbox_callback'),
			'nas_admin_options_uninstall',
			'nas_admin_options_uninstall',
			array('drop_views', 'nas_admin_options_uninstall')
		);	
		add_settings_field(
			'drop_device_table',
			__('Drop Device Table', 'netatmosphere'),
			array($this, 'options_checkbox_callback'),
			'nas_admin_options_uninstall',
			'nas_admin_options_uninstall',
			array('drop_device_table', 'nas_admin_options_uninstall')
		);	
		add_settings_field(
			'drop_data_table',
			__('Drop Table with measurements', 'netatmosphere'),
			array($this, 'options_checkbox_callback'),
			'nas_admin_options_uninstall',
			'nas_admin_options_uninstall',
			array('drop_data_table', 'nas_admin_options_uninstall')
		);
		register_setting('nas_admin_options_uninstall', 'nas_admin_options_uninstall', array(&$this, 'optionsInputValidation') );
	}

	/* Call Backs
	-----------------------------------------------------------------*/
	function options_section_callback($args) {
		//echo "<h3>" . $args['title'] . "</h3>";
		if($args['id'] == 'nas_admin_options_uninstall') {
			echo "<h4>" . __('Be careful! This takes affect when you uninstall the plugin!', 'netatmosphere') . "</h4>";
		}
	}
	function options_page_callback() { 
		echo '<p>Front Page Display Options:</p>'; 
	}
	function options_textbox_callback($args) { 
		$id   = $args[0];
		$sett = $args[1];
		$options = get_option($sett); 
		echo '<input type="text" size="40" id="'  . $id . '" name="' . $sett . '['  . $id . ']" value="' . $options[$id] . '"></input>';
	}
    function options_textarea_callback($args) { 
		$id   = $args[0];
		$sett = $args[1];
		$options = get_option($sett); 
		echo '<textarea rows="4" cols="50" id="'  . $id . '" name="' . $sett . '['  . $id . ']">' . $options[$id] . '</textarea>';
	}
	function options_password_callback($args) { 
		$id   = $args[0];
		$sett = $args[1];
		$options = get_option($sett); 

		echo '<input type="password" size="40" id="'  . $id . '" name="' . $sett . '['  . $id . ']" value="' . $options[$id] . '"></input>';
	}
	function options_checkbox_callback($args) { 
		$id   = $args[0];
		$sett = $args[1];
		$options = get_option($sett); 

		echo '<input type="checkbox" id="'  . $id . '" name="' . $sett . '['  . $id . ']" value="1"' . checked( 1, $options[$id], false ) . '></input>';
	}
	function options_dropdown_callback($args) { 
		$id    = $args[0];
		$sett  = $args[1];
		$items = $args[2];
		$options = get_option($sett); 
        			
		echo '<select id="'  . $id . '" name="' . $sett . '['  . $id . ']" value="' . $options[$id] . '">';
		foreach($items as $key => $text){
            $value = $key;
            if( empty ( $value ) ) 
                $value = $text;
            //$item = esc_attr($item);
			$selected = ($options[$id]==$value) ? 'selected="selected"' : '';
			echo "<option value='$value' $selected>$text</option>";
		}
		echo "</select>";
		
	}

	/* input validation
	--------------------------------------------------*/
	function optionsInputValidation($input) {
		//return $input;
		// Create our array for storing the validated options
		$output = array();
		 
		// Loop through each of the incoming options
		foreach( $input as $key => $value ) {
			 
			// Check to see if the current option has a value. If so, process it.
			if( isset( $input[$key] ) ) {
			 
				// Strip all HTML and PHP tags and properly handle quoted strings
				$output[$key] = strip_tags( stripslashes( $input[ $key ] ) );
                
                // take care of special options, encapsulated in comma list or whatever...
                switch ( $key ) {
                
                    // make elements unique before store in DB
                    case 'nas_synch_devices_ignore_modules':
                        $l = preg_split( "/[\s,;]+/", $input [ $key ] );
                        $output[$key] = implode ( ", ", array_unique ( $l ) );
                        break;
                }
				 
			} // end if
			 
		} // end foreach
		 
		// Return the array processing any additional functions filtered by this action
		return $output;
	}
}

?>