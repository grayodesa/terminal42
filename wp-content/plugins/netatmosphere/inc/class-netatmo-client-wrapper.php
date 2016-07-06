<?php //defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * 
 */
class NetAtmo_Client_Wrapper {
    private static $instance = NULL;
    
    public $client = null;
    
    const CLIENT_ID = '55e3fedf2baa3cc87c79da56';
    const CLIENT_SECRET = 'rZGgi95ZtcIBIlb3s3Fw4ZJl9NEac4';

    /**
	* static method for getting the instance of this singleton object
	*
	* @return NetAtmo_Client_Wrapper
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
        require_once (NAS_PLUGIN_ROOT . '/lib/Netatmo-API/src/Netatmo/autoload.php');
        
		if( function_exists( 'add_action') ) {
            //NAS_Plugin::debugFile('__construct', __FILE__, __LINE__);
            //add_action( 'plugins_loaded', array($this, 'SaveTokensFromSession') );
        }
		
        //API client configuration
        $config = array("client_id"     => NetAtmo_Client_Wrapper::CLIENT_ID,
                        "client_secret" => NetAtmo_Client_Wrapper::CLIENT_SECRET,
                        "scope"         => Netatmo\Common\NAScopes::SCOPE_READ_STATION);
        $this->client = new Netatmo\Clients\NAWSApiClient($config);
        
        $this->loadTokensFromOptions();
    }
    
    private function netatmoLoginFilePath() {
        return NAS_PLUGIN_ROOT . '../netatmosphere-debug-cfg.php';
    }
    private function isLocalhost() {
        $whitelist = array(
            '127.0.0.1',
            '::1'
        );

        if(in_array($_SERVER['REMOTE_ADDR'], $whitelist)){
            return true;
        }
        return false;
    }
    
    public function getAccessToken() {
        return $this->client->getAccessToken();
    }
    public function getRefreshToken() {
        $token = $this->client->getRefreshToken();
        if(!$token) {
            $token = get_option( 'nas_netatmo_token_refresh' );
        }
        $token = explode('|', $token);
        if(count($token) > 0 )
            return $token[1];
        return '';
    }
    protected static function saveTokensToOptions($access, $refresh) {
        update_option( 'nas_netatmo_token_access',  $access );
        update_option( 'nas_netatmo_token_refresh', $refresh );
        //NAS_Plugin::debugFile('tokens saved to options', __FILE__, __LINE__, __FUNCTION__);
    }
    public function loadTokensFromOptions() {
        //NAS_Plugin::debugFile('loadTokensFromOptions', __FILE__, __LINE__, __FUNCTION__);
        if( function_exists( 'get_option' ) ) {
            $access_token  = get_option( 'nas_netatmo_token_access' );
            $refresh_token = get_option( 'nas_netatmo_token_refresh' );
            
            //NAS_Plugin::debugFile($access_token, __FILE__, __LINE__, __FUNCTION__);
            //NAS_Plugin::debugFile($refresh_token, __FILE__, __LINE__, __FUNCTION__);
            
            if( false !== $access_token && false !== $refresh_token) {
                $tokens = array();
                $tokens['access_token']  = $access_token;
                $tokens['refresh_token'] = $refresh_token;
                
                //NAS_Plugin::debugFile($tokens, __FILE__, __LINE__, __FUNCTION__);

                $this->client->setTokensFromStore($tokens);
            }
        }
    }
    public function disconnect() {

        if( function_exists( 'delete_option' ) ) {
            delete_option( 'nas_netatmo_token_access' );
            delete_option( 'nas_netatmo_token_refresh' );
        }
        
        $this->client->unsetTokens();
        
        if( isset( $_SESSION['NETATMOSPHERE'] ) ) {
            
            if( isset( $_SESSION['NETATMOSPHERE']['ACCESS_TOKEN']) || isset( $_SESSION['NETATMOSPHERE']['REFRESH_TOKEN']) ) {

                unset( $_SESSION['NETATMOSPHERE']['ACCESS_TOKEN'] );
                unset( $_SESSION['NETATMOSPHERE']['REFRESH_TOKEN'] );
            }
        }
    }
    
    public static function TokensInSession() {
        if( isset( $_SESSION['NETATMOSPHERE'] ) ) {
            if( isset( $_SESSION['NETATMOSPHERE']['ACCESS_TOKEN']) || isset( $_SESSION['NETATMOSPHERE']['REFRESH_TOKEN']) ) {
                return true;
            }
        }
        return false;
    }
    
    // check session for access / refresh token
    public static function SaveTokensFromSession() {
                
        if( isset( $_SESSION['NETATMOSPHERE'] ) ) {
            
            if( isset( $_SESSION['NETATMOSPHERE']['ACCESS_TOKEN']) || isset( $_SESSION['NETATMOSPHERE']['REFRESH_TOKEN']) ) {
                NAS_Plugin::debugFile('tokens set', __FILE__, __LINE__, __FUNCTION__);
                $access_token  = $_SESSION['NETATMOSPHERE']['ACCESS_TOKEN'];
                $refresh_token = $_SESSION['NETATMOSPHERE']['REFRESH_TOKEN'];
                
                if( function_exists( 'update_option' ) && ( isset( $access_token ) || isset( $refresh_token) ) ) {
                    unset( $_SESSION['NETATMOSPHERE']['ACCESS_TOKEN'] );
                    unset( $_SESSION['NETATMOSPHERE']['REFRESH_TOKEN'] );
                    
                    self::saveTokensToOptions($access_token, $refresh_token);
                    
                    if(self::$instance !== null) {
                        self::$instance->loadTokensFromOptions();
                    }
                }
            }
        }
    }
    
    public function reauthRequired() {
        $ret = true;
        if( function_exists( 'get_option' ) ) {
            $access_token  = get_option( 'nas_netatmo_token_access' );
            $refresh_token = get_option( 'nas_netatmo_token_refresh' );
            
            if( ! (false === $access_token || false === $refresh_token ) ) {
                $ret = false;
            }
        }
        return $ret;
    }
    
    public function htmlOAuthLink($class = '') {
        return "<p><a href='" . plugins_url(NAS_PLUGIN_NAME . '/netatmo.php') . "' class='" . $class . "'>" . __('NetAtmo Login', 'netatmosphere') . "</a></p>";
    }
}

?>