<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class NAS_Plugin {

	private static $instance = NULL;
    public static $shortcode_schedules  = 'netatmosphere-schedules';
    public static $shortcode_devices    = 'netatmosphere-devices';
    public static $shortcode_data       = 'netatmosphere-data';

   /**
	* static method for getting the instance of this singleton object
	*
	* @return NAS_Plugin
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
		// always call parent constructor manually :(
		add_action( 'init',               array($this, 'init'));
		add_action( 'plugins_loaded',     array($this, 'loadPluginTextdomain') );
		add_action( 'plugins_loaded',     array($this, 'pluginsLoaded') );
		add_action( 'wp_enqueue_scripts', array($this, 'enqueueScripts' ) );
		add_action( 'widgets_init',       array($this, 'registerWidget' ) );
        add_action( 'admin_bar_menu',     array($this, 'beforeToolbarRender' ), 999 );
        // filters
        add_filter( 'cron_schedules',     array($this, 'addCronScheduleTypes'));

		// or better load only in case of frontend?
		if (! is_admin () ) {
			$this->registerShortCodes();
		}

		if ( is_admin() ) {
			NAS_Admin_Plugin::getInstance();
		}

        // check / update tokens from session
        if ( NetAtmo_Client_Wrapper::TokensInSession() ) {
            // save tokens
            NetAtmo_Client_Wrapper::SaveTokensFromSession();
            // and (re)schedule cron's
            NAS_Cron::scheduleAll(true);
        }
	}

	/**
	 * call backs
	 */
	function init() {

	}
    function beforeToolbarRender($wp_admin_bar) {
        $base_url = 'options-general.php?page=netatmosphere';
        $base_id = 'nas_admin';

        $args = array(
            'id'    => $base_id . '',
            'title' => __('NetAtmoSphere', 'netatmosphere'),
            'href'  => admin_url($base_url . '&tab=overview'),
        );
        $wp_admin_bar->add_node( $args );
        $args = array(
            'id'    => $base_id . '_admin',
            'title' => __('Administration', 'netatmosphere'),
            'href'  => admin_url($base_url . '&tab=admin'),
            'parent'=> $base_id,
        );
        $wp_admin_bar->add_node( $args );
        $args = array(
            'id'    => $base_id . '_options',
            'title' => __('Options', 'netatmosphere'),
            'href'  => admin_url($base_url . '&tab=options'),
            'parent'=> $base_id,
        );
        $wp_admin_bar->add_node( $args );
        $args = array(
            'id'    => $base_id . '_latest',
            'title' => __('Latest', 'netatmosphere') . ": " . NAS_Data_Adapter::getLastRecord(),
            'parent'=> $base_id,
        );
        $wp_admin_bar->add_node( $args );
    }
	function pluginsLoaded() {
		// verify database first
		self::verifyDatabase();

        // any upgrade stuff to do?
        self::upgradeTasks();
    }
	function enqueueScripts() {
		if (! is_admin () ) {
			$this->enqueueUiScripts();
			$this->enqueueUiStyles();
		}
	}
    function registerWidget() {
        register_widget( 'NAS_Widget' );
    }
	function registerShortCodes() {

		if( self::isDebug() ) {
			add_shortcode( 'netatmosphere-test', array($this, 'shortcodeTest' ));
		}
		add_shortcode( self::$shortcode_schedules,   array($this, 'shortcodeSchedules' ) );
        add_shortcode( self::$shortcode_devices,     array($this, 'shortcodeDevices' ) );
        add_shortcode( self::$shortcode_data,        array($this, 'shortcodeData' ) );
	}

	// setup the translation
	function loadPluginTextdomain() {
		$ret = load_plugin_textdomain( 'netatmosphere', FALSE, NAS_PLUGIN_NAME . '/lang' );
	}

	function enqueueUiScripts() {
		// enqueue jquery for filtering
		wp_enqueue_script ( 'jquery' );
		wp_enqueue_script ( 'jquery-ui-tooltip');

		wp_register_script( 'cdn-datatables-script', '//cdn.datatables.net/s/dt/dt-1.10.10,b-1.1.0,se-1.1.0/datatables.min.js');
		wp_enqueue_script ( 'cdn-datatables-script' );

		// my scripts
		wp_register_script( 'nas-ui-script', plugins_url("/" . NAS_PLUGIN_NAME . '/js/nas-ui.js') );
		wp_enqueue_script ( 'nas-ui-script' );
		return;
	}

	function enqueueUiStyles() {
		wp_register_style( 'cdn-datatables-style', '//cdn.datatables.net/s/dt/dt-1.10.10,b-1.1.0,se-1.1.0/datatables.min.css' );
		wp_enqueue_style ( 'cdn-datatables-style' );

		wp_register_style( 'nas-ui-results-style', plugins_url("/" . NAS_PLUGIN_NAME . '/css/netatmosphere.css') );
		wp_enqueue_style ( 'nas-ui-results-style' );

		//wp_register_style( 'nas-ui-tournaments-style', plugins_url("/" . NAS_PLUGIN_NAME . '/css/ui.shortcode.tournaments.css') );
		//wp_enqueue_style ( 'nas-ui-tournaments-style' );
	}
    function addCronScheduleTypes( $schedules ) {

        foreach ( NAS_Time_Units::GetCustomUnits() as $u ) {
            $name = $u->name;
            $interval = $u->interval;
            $translation = $u->translation;

            if ( !isset ( $schedules[ $name ] ) ) {
                $schedules[$name] = array(
                    'interval' => $interval,
                    'display' => $translation
                );
            }
        }

        return $schedules;
    }

	/**
	 * installer / deactivator / deinstaller (must be public static)
	 */
	public static function pluginActivation() {
		// check, verify and update database if necessary
		self::verifyDatabase();

		/*// create custom rewrite's
		$this->rewriteRules();
		flush_rewrite_rules();*/

        // schedule WP cron jobs
        NAS_Cron::scheduleDeviceRefresh();
        NAS_Cron::scheduleDataMerge();

        // manually run the jobs, but async via WP cron
        NAS_Cron::requestSingleDeviceRefresh();
        NAS_Cron::requestSingleDataMerge();
	}
	public static function pluginDeactivation() {
		//self::debugFile('pluginDeactivation', __FILE__, __LINE__);
		flush_rewrite_rules();

        // clear all cron jobs
        NAS_Cron::clearDeviceRefresh();
        NAS_Cron::clearDataMerge();
	}
	public static function pluginUninstall() {
		//global $wpdb;

		// do the DB cleanup
		$options = get_option("nas_admin_options_uninstall");

		if(isset($options)) {

			if( isset($options['drop_views']) && $options['drop_views'] == true) {
				// firstly drop the views
				NAS_DB_Tool::DropAllViews();
			}
			if( isset($options['drop_device_table']) && $options['drop_device_table'] == true) {
				// after this the underlying tables
				NAS_DB_Tool::DropDeviceTable();
            }
			if( isset($options['drop_data_table']) && $options['drop_tables'] == true) {
				// after this the underlying tables
				NAS_DB_Tool::DropDataTable();
            }
		}
	}


    /**
     * SHORTCODE's
     */
    function shortcodeTest($att) {
        $html = "<h2>shortcodeTest</h2>";
        ob_start();
        
        echo "<h3>option: widget_netatmosphere_widget</h3>";
        $opt = get_option ( 'widget_netatmosphere_widget');
        
        echo "<h4>original</h4>";
        var_dump( $opt );

        echo "<h4>get 'show_latest'</h4>";
        $sub = array_column ( $opt, 'show_latest' );
        var_dump( $sub );
        var_dump( isset($sub));
        var_dump ( empty ($sub ));
        
        echo "<h4>get 'hugo'</h4>";
        $sub = array_column ( $opt, 'hugo' );
        var_dump( $sub );
        var_dump( isset($sub));
        var_dump ( empty ($sub ));
        
        
        /*try {

            $client = NetAtmo_Client_Wrapper::getInstance()->client;
			$devicesData = $client->getData(null, false);
            echo "<h3>fav = false</h3>";
            var_dump ( $devicesData );
            echo "<hr/>";
            
            $devicesData = $client->getData(null, true);
            echo "<h3>fav = true</h3>";
            var_dump ( $devicesData );
            echo "<hr/>";
        } catch ( Exception $ex) {
            var_dump ( $ex );
        }*/
        //update_option ( 'nas_synch_devices_ignore_modules' , "03:00:00:02:23:2a,");
        
        /*var_dump ( NAS_Devices_Adapter::getActive() );
        
        $text = get_option ( 'nas_synch_devices_ignore_modules' );
        $split = preg_split( "/[\s,;]+/", $text);
        
        var_dump( $split );*/

        //$options = get_option("nas_admin_options_caching");
        //var_dump($options);

        //echo "date_default_timezone_get():" . date_default_timezone_get() . "<br/>";
        //echo "wp timezone: " . get_option('timezone_string') . "<br/>";

        /*NAS_Cron::clearDataMerge();
        NAS_Cron::requestSingleDataMerge();*/

        /*$unix = array(1445733851,1445734159,1445734466,1445734774,1445735082,1445735389,1445735646,1445735953);
        echo "<table>";
        foreach($unix as $u) {
            $u_fmt = date('Y-m-d H:i:s', $u);
            $u_loc = self::utc2date($u);
            echo sprintf("<tr><td>%s</td><td>%s</td><td>%s</td></tr>", $u, $u_fmt, $u_loc);
        }
        echo "</table>";*/


        /*NAS_Devices_Adapter::clear();
        $d = new NAS_Synch_Devices();
        $d->refresh();*/

        /*NAS_Data_Adapter::clear();
        $d = new NAS_Synch_Data();
        $d->merge();*/

        /*if(false) {
            // schedule if not already
            if( !wp_get_schedule( NAS_CRON_DEVICES_EVENT ))
                NAS_Cron::scheduleDeviceRefresh();

            if( !wp_get_schedule( NAS_CRON_DATA_EVENT ))
                NAS_Cron::scheduleDataMerge();
        } else {
            // clear schedules
            NAS_Cron::clearDeviceRefresh();
            NAS_Cron::clearDataMerge();
        }*/

        /*$date = "2016-01-01 00:00:00";
        $utc = NAS_Plugin::date2utc($date);
        $html .= "date: $date<br/>";
        $html .= "utc: $utc<br/>";
        $html .= "<hr/>";

        $utc = "1451606400";
        $date = NAS_Plugin::utc2date($utc);
        $html .= "utc: $utc<br/>";
        $html .= "date: $date<br/>";
        $html .= "<hr/>";*/

        //nas_cron_execute_data_merge();

        //echo "device: " . NAS_Cron::getNextDeviceRefresh() . "<br/>";
        //echo "data: " . NAS_Cron::getNextDataMerge() . "<br/>";


        $html .= ob_get_contents();
        ob_end_clean();
        return $html;
    }
    function shortcodeSchedules($att) {
        $sc = new NAS_Shortcode_Schedules();
		$sc->init($att);

		return $sc->render();
    }
    function shortcodeDevices($att) {
        $sc = new NAS_Shortcode_Devices();
		$sc->init($att);

		return $sc->render();
    }
    function shortcodeData($att) {
        $sc = new NAS_Shortcode_Data();
		$sc->init($att);

		return $sc->render();
    }

    protected static function upgradeTasks() {
        $installed_version = get_option( "nas_plugin_version" );

        try {
            if ( $installed_version != NAS_PLUGIN_VERSION ) {
                
                if ( version_compare ( $installed_version, '2.0.0', '<' ) ) {

                    // this is the old login: with user and password. its deprecated, now its OAuth.
                    // clean up those old data stored in db and also renew the cron jobs
                    // also there was an bug in datetime conversion, easiest to clear all data and start from zero
                    // (reason is the missing time zone as user input)
                    delete_option ( 'nas_admin_options_netatmo' );

                    // clear all cron jobs
                    NAS_Cron::clearAll();
                    // now clear all data
                    NAS_Data_Adapter::clear();
                    NAS_Devices_Adapter::clear();
                    // and now recreate them back with request single
                    NAS_Cron::scheduleAll(true);

                }
                if ( version_compare ( $installed_version, '2.0.2', '<' ) ) {
                    // remove the old cron jobs, they were hooked up through a different callback

                    if ( false !== wp_next_scheduled ( 'hourlyDataUpdate_event' ) )
                        wp_clear_scheduled_hook('hourlyDataUpdate_event');
                    if ( false !== wp_next_scheduled ( 'deviceUpdate_event' ) )
                        wp_clear_scheduled_hook('deviceUpdate_event');
                }

                // save plugin version
				update_option( 'nas_plugin_version', NAS_PLUGIN_VERSION );
            }
        } catch(Exception $ex) {
			self::debugFile( $ex, __FILE__, __LINE__ );
			self::sendAdminMail($ex, 'verifyDatabase');
		}
    }
	// compare DB version from option with constant to check wether to run db update scripts or not.
	protected static function verifyDatabase($forced = false) {
		global $wpdb;

		$installed_version = get_option( "nas_db_version" );

		try {
			if(true === $forced || $installed_version != NAS_DB_VERSION) {
				NAS_Plugin::debugFile( 'verifyDatabase: ' . $installed_version, __FILE__, __LINE__ );

				// 1st create all tables
				NAS_DB_Tool::CreateAllTables();

				// 2nd create all views
				NAS_DB_Tool::CreateAllViews();

				// 3rd fill/update with default data
				NAS_DB_Tool::InsertDefaultData();

				// save db version
				update_option( 'nas_db_version', NAS_DB_VERSION );
			}
		} catch(Exception $ex) {
			self::debugFile( $ex, __FILE__, __LINE__ );
			self::sendAdminMail($ex, 'verifyDatabase');
		}
	}

	/**--------------------------
	 * helper functions
	 * --------------------------
	 */
    public static function utc2date($utc, $timeZoneName = null) {
        try {
            if ( $utc == 0 )
                return "-";

            if( null === $timeZoneName )
                $timeZoneName = NAS_Options::GetTimeZone();

            // if it comes formatted along
            if ( !is_numeric ( $utc ) )
                $utc = strtotime ( $utc );

            $fmt = 'Y-m-d H:i:s';
            $dateFromUtc = date($fmt, $utc);
            $utc_date = DateTime::createFromFormat($fmt, $dateFromUtc, new DateTimeZone('UTC'));
            if ( !empty($timeZoneName) ) {
                $utc_date->setTimeZone(new DateTimeZone($timeZoneName));
            } else {
                $gmt_offset = get_option('gmt_offset');
                $utc_date->add(new DateInterval("PT{$gmt_offset}H"));
            }
            return $utc_date->format($fmt);
        } catch (Exception $ex) {
            return "-";
        }
	}
    public static function date2utc($date, $timeZoneName = null) {
        try {
            if ( $date == 0 )
                return "-";

            if( null === $timeZoneName )
                $timeZoneName = NAS_Options::GetTimeZone();

            $fmt = 'Y-m-d H:i:s';
            $utc = DateTime::createFromFormat($fmt, $date, new DateTimeZone($timeZoneName));
            $utc->setTimeZone(new DateTimeZone('UTC'));
            return $utc->getTimestamp();
        } catch (Exception $ex) {
            return "-";
        }
	}
	public static function sendAdminMail($msg, $subject = null, $file = null, $line = null) {
		$to = get_bloginfo('admin_email');
		$prefix = 'Wordpress Plugin';

		if(!isset($subject)) {
			$prefix .= " - ";
		}
		$subject = $prefix . $subject;

        if( isset($file) ) {
            $msg .= "\n\n($file";
            if( isset($line) ) {
                $msg .= " @ $line";
            }
            $msg .= ")";
        }

		wp_mail($to, $subject, $msg);
	}
	public static function isDebug() {
		if( defined('WP_DEBUG') && WP_DEBUG ) {
			return true;
		}
		return false;
	}
	public static function debugBar ( $msg='', $header='nas', $file=null, $line=null ) {

		if( self::isDebug() && isset($GLOBALS['DebugMyPlugin']) && isset($GLOBALS['DebugMyPlugin']->panels['main'] ) ) {

			/*if ( !is_string( $msg ) ) {
				$msg = print_r ( $msg, true );
				$msg = nl2br ( $msg );
				$msg = str_replace ( "\t", "&nbsp;&nbsp;&nbsp;&nbsp;", $msg );
			}*/

			if ( !is_string( $msg ) ) {
				$GLOBALS['DebugMyPlugin']->panels['main']->addMessage($header,$msg,$file,$line);
			} else {
				$GLOBALS['DebugMyPlugin']->panels['main']->addPR($header,$msg,$file,$line);
			}
		}
	}
    public static function debugFile ( $log, $file = null, $line = null, $func = null )  {
        if ( self::isDebug() ) {
			$postfix = ""; $prefix = "";
			if( null !== $file ) {
				$postfix .= basename($file);
				if( null !== $line ) {
					$postfix .= " @ " . $line;
				}
				$postfix = " (" . $postfix . ")";
			}
            if( null !== $func ) {
                $prefix = $func . ": ";
            }

            if ( !is_string( $log ) ) {
                $log = var_export( $log, true );
            }

			error_log( $prefix . $log . $postfix );
        }
    }
	public static function debugPrintTable($tab) {
		if( !self::isDebug() )
			return;

		echo self::resultToTable($tab);
	}
	public static function resultToTable($tab, $tableParam = '') {

		$tabHead = "";
		foreach($tab[0] as $key => $row) {
			$tabHead .= "<th>$key</th>";
		}
		$html = "<table $tableParam><tr>$tabHead</tr>";
		foreach($tab as $key => $row) {
			$html .= "<tr>";
			foreach($row as $val) {
				$html .= "<td>" . $val . "</td>";
			}
			$html .= "</tr>";
		}
		$html .= "</table>";

		return $html;
	}

	/**
	 * helper functions
	 */
	public static function getPostData($key, $default = '') {
		if(!isset($key)) {
			return null;
		}

		$ret = isset($_POST[$key]) ? stripslashes(trim($_POST[$key])) : $default;
		return stripslashes(trim($ret));
	}
	public static function getGetData($key, $default = '') {
		if(!isset($key)) {
			return null;
		}

		$ret = isset($_GET[$key]) ? $_GET[$key] : $default;
		return stripslashes(trim($ret));
	}

}

?>