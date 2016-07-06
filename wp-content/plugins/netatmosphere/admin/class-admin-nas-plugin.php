<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); 

class NAS_Admin_Plugin {

	private static $instance = NULL;
	
   /**
	* static method for getting the instance of this singleton object
	*
	* @return NAS_Admin_Plugin
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
			//add_action( 'admin_bar_init',        array($this, 'init') );
			add_action( 'admin_enqueue_scripts', array($this, 'enqueueScripts') );
			
			// filters
			//add_filter( 'admin_footer_text', 	array($this, 'footerText') );
			//add_filter( 'update_footer', 		array($this, 'footerVersion'), 11 );
			
			// custom admin action
			add_action( 'admin_action_nas_refresh_devices',      array($this, 'refreshDevicesOnDemand') );
			add_action( 'admin_action_nas_refresh_data',         array($this, 'refreshDataOnDemand') );
			add_action( 'admin_action_nas_clear_devices_cache',  array($this, 'clearDeviceCacheOnDemand') );
			add_action( 'admin_action_nas_clear_data_cache',     array($this, 'clearDataCacheOnDemand') );
			add_action( 'admin_action_nas_disconnect_netatmo',   array($this, 'disconnectFromNetAtmo') );

            // additional action on option change
            add_filter( 'update_option_nas_admin_options_caching', array($this, 'adminOptionsCachingChanged') );
		}

        // start session for maybe later use in OAuth for netatmo
        session_start();

		NAS_Admin_Menu::getInstance();
		NAS_Admin_Options::getInstance();
        //NetAtmo_Client_Wrapper::getInstance();
	}
	
	/**
	 * filter callbacks
	 */
	function footerText($default) {
		return $default;
	}
	 
	function footerVersion($default) {
		return $default;
	}
    
    function adminOptionsCachingChanged( $oldOptions ) { 
        //NAS_Plugin::debugFile($oldOptions, __FILE__, __LINE__, __FUNCTION__ . " - oldOptions");
        
        NAS_Cron::recreateAll();
        
        try {
            NAS_Devices_Adapter::updateAllSynch( NAS_Options::getInstance()->GetIgnoreModuleIds() );
        } catch ( Exception $ex ) { }
    }

	/**
	 * action callbacks
	 */
	function enqueueScripts() {
		/* styles */
		wp_register_style( 'netatmosphere-style', plugins_url("/" . NAS_PLUGIN_NAME . '/css/netatmosphere.css') );
		wp_enqueue_style( 'netatmosphere-style' );
		
		wp_register_style( 'netatmosphere-admin-style', plugins_url("/" . NAS_PLUGIN_NAME . '/admin/css/netatmosphere-admin.css') );
		wp_enqueue_style( 'netatmosphere-admin-style' );
		
		/* scripts */
		wp_enqueue_script (  'jquery' );
		// use jquery ui
		/*wp_enqueue_script (  'jquery-ui-dialog' );
		wp_enqueue_style (  'jquery-ui-dialog' );*/

		// my scripts
		wp_register_script( 'netatmosphere-admin-script', plugins_url("/" . NAS_PLUGIN_NAME . '/admin/js/netatmosphere-admin.js') );
		wp_enqueue_script( 'netatmosphere-admin-script' );
	}

	/* custom admin action
	--------------------------------------------------*/
	function refreshDevicesOnDemand() {
		check_admin_referer('netatmosphere-admin-refresh-devices');
		
        NAS_Cron::requestSingleDeviceRefresh();
		
		wp_redirect( $_SERVER['HTTP_REFERER'] );
		exit();
	}
	function refreshDataOnDemand() {
		check_admin_referer('netatmosphere-admin-refresh-data');

        NAS_Cron::requestSingleDataMerge();
		
		wp_redirect( $_SERVER['HTTP_REFERER'] );
		exit();
	}
	function clearDeviceCacheOnDemand() {
		check_admin_referer('netatmosphere-admin-clear-device_cache');
        
		try {
			NAS_Devices_Adapter::clear();
		} 
		catch(Exception $ex) {
			NAS_Plugin::sendAdminMail($ex,
				__('Clear of device data failed', 'netatmosphere'),
                __FILE__, __LINE__);
            NAS_Plugin::debugFile($ex, __FILE__, __LINE__, __FUNCTION__);
		}
		
		wp_redirect( $_SERVER['HTTP_REFERER'] );
		exit();
	}
	function clearDataCacheOnDemand() {
		check_admin_referer('netatmosphere-admin-clear-data_cache');

		try {
			NAS_Data_Adapter::clear();
		} 
		catch(Exception $ex) {
            NAS_Plugin::debugFile($ex, __FILE__, __LINE__, __FUNCTION__);
			NAS_Plugin::sendAdminMail($ex,
				__('Clear of measurement data failed', 'netatmosphere'),
                __FILE__, __LINE__);
		}
		
		wp_redirect( $_SERVER['HTTP_REFERER'] );
		exit();
	}
    function disconnectFromNetAtmo() {
        check_admin_referer('netatmosphere-admin-disconnect-netatmo');

		try {
			$wrap = NetAtmo_Client_Wrapper::getInstance();
            $wrap->disconnect();
            
            NAS_Cron::clearAll();
		} 
		catch(Exception $ex) {
			NAS_Plugin::sendAdminMail(
				print_r($ex),
				__('Disconnect from NetAtmo account failed', 'netatmosphere'));
		}
		
		wp_redirect( $_SERVER['HTTP_REFERER'] );
		exit();
    }    
}

?>