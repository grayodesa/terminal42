<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Enum like classes
 */
class NAS_Time_Units {
    protected static $customUnits = null;
    protected static $unitNames = null;
    
    public static function GetCustomUnits() {
        
        if( is_null ( self::$customUnits ) ) {
            self::$customUnits = array();
            
            self::$customUnits['10min']  = new NAS_TimeUnit('10min', 600, __('Every 10 minutes') );
            self::$customUnits['15min']  = new NAS_TimeUnit('15min', 900, __('Every 15 minutes') );
            self::$customUnits['weekly'] = new NAS_TimeUnit('weekly', 604800, __('Once in a week') );
        }
        
        return self::$customUnits;
    }
    public static function GetAllNames() {
        if( is_null ( self::$unitNames ) ) {
            self::$unitNames = array();
            
            include_once( ABSPATH . 'wp-includes/cron.php' );
            
            $schedules = wp_get_schedules();
            uasort( $schedules, create_function( '$a, $b', 'return $a["interval"] - $b["interval"];' ) );
            foreach ( $schedules as $key => $s) {

                self::$unitNames[$key]  = $s [ 'display' ];
            }
        }
        
        return self::$unitNames;
    }
}

class NAS_TimeUnit {
    public $name = '';
    public $translation = '';
    public $interval = 0;
    
    public function __construct($name, $interval, $translation = '') {
        $this->name = $name;
        $this->interval = $interval;

        $this->translation = $name;
        if( isset ( $translation ) && !empty ( $translation ) ) {
            $this->translation = $translation;
        }
    }
}

?>