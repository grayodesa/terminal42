<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); 

class NAS_Admin_Menu {

	private static $instance = NULL;
	
   /**
	* static method for getting the instance of this singleton object
	*
	* @return NAS_Admin_Menu
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
			add_action( 'admin_menu',            array($this, 'createMenu') );
		}
	}
	
	function createMenu() {
		$page_title = 'NetAtmoSphere';
		$menu_title = 'NetAtmoSphere';
		$capability = 'manage_options';
		$menu_slug = 'netatmosphere';
		$icon_url = '';
		$position = '81';
		
		// add menu item to settings section
		$function = 'renderSettingsMenuItem';
		add_options_page( $page_title, $menu_title, $capability, $menu_slug . '', array($this, $function) );
		//add_management_page( $page_title, $menu_title, $capability, $menu_slug . '', $function );
		
		// create new admin menu
		//$function = 'nas_displayAdminMenu';
		//add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
		// add sub menu with same link
		//$page_title = __('Options', 'netatmosphere');
		//$menu_title = __('Options', 'netatmosphere');
		//add_submenu_page($menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
		/*// add new sub menu
		$page_title = __('Administration', 'netatmosphere');
		$menu_title = __('Administration', 'netatmosphere');
		$sub_menu_slug = $menu_slug . '-admin';
		$function = 'nas_displayAdminMenu';
		add_submenu_page($menu_slug, $page_title, $menu_title, $capability, $sub_menu_slug, $function);*/
		
		// load styles
		//wp_registerAdminStyles();
		// and scripts
		//wp_registerAdminScripts();
	}
	
	function renderSettingsMenuItem() {
		$_SESSION['NETATMOSPHERE']['RETURN_URL']  = $_SERVER['REQUEST_URI'];
        
        $wrap = NetAtmo_Client_Wrapper::getInstance();
        $netatmo_reauth_required = $wrap->reauthRequired();
        
        $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'admin';
        
        if( 'admin' === $active_tab ) {
        
            $lastDeviceRefresh = NAS_Devices_Adapter::getLastRefreshDate(true);
            $lastDataRefresh = NAS_Data_Adapter::getLastRecord(true);
            
            $showDeviceRefreshBtn = false;
            $diff_sec = time() - strtotime( $lastDeviceRefresh );
            if ( $diff_sec > NAS_Cron::MIN_DELAY_BETWEEN_REQUESTS ) 
                $showDeviceRefreshBtn = true;
                
            $showDataRefreshBtn = false;
            $diff_sec = time() - strtotime( $lastDataRefresh );
            if ( $diff_sec > NAS_Cron::MIN_DELAY_BETWEEN_REQUESTS ) 
                $showDataRefreshBtn = true;
        
        } elseif( 'overview' === $active_tab ) {
        
            $overviewData = NAS_Data_Adapter::getDataOverview();
        }
        
        
		include('tpl/admin.settings.tpl.php');
	}
}

?>