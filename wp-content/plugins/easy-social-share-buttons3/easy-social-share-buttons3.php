<?php

/*
* Plugin Name: Easy Social Share Buttons for WordPress
* Description: Easy Social Share Buttons automatically adds share bar to your post or pages with support of Facebook, Twitter, Google+, LinkedIn, Pinterest, Digg, StumbleUpon, VKontakte, Tumblr, Reddit, Print, E-mail and other 30 social networks. Easy Social Share Buttons for WordPress is compatible with WooCommerce, bbPress and BuddyPress
* Plugin URI: http://codecanyon.net/item/easy-social-share-buttons-for-wordpress/6394476?ref=appscreo
* Version: 3.6.1
* Author: CreoApps
* Author URI: http://codecanyon.net/user/appscreo/portfolio?ref=appscreo
*/


if (! defined ( 'WPINC' ))
	die ();

//error_reporting( E_ALL | E_STRICT );

define ( 'ESSB3_SELF_ENABLED', false );

define ( 'ESSB3_VERSION', '3.6.1' );
define ( 'ESSB3_PLUGIN_ROOT', dirname ( __FILE__ ) . '/' );
define ( 'ESSB3_PLUGIN_URL', plugins_url () . '/' . basename ( dirname ( __FILE__ ) ) );
define ( 'ESSB3_PLUGIN_BASE_NAME', plugin_basename ( __FILE__ ) );
define ( 'ESSB3_OPTIONS_NAME', 'easy-social-share-buttons3');
define ( 'ESSB3_NETWORK_LIST', 'easy-social-share-buttons3-networks');
define ( 'ESSB3_OPTIONS_NAME_FANSCOUNTER', 'easy-social-share-buttons3-fanscounter');
define ( 'ESSB3_EASYMODE_NAME', 'essb3-easymode');
define ( 'ESSB3_FIRST_TIME_NAME', 'essb3-firsttime');
define ( 'ESSB3_TEXT_DOMAIN', 'essb');
define ( 'ESSB3_TRACKER_TABLE', 'essb3_click_stats');
define ( 'ESSB3_MAIL_SALT', 'easy-social-share-buttons-mailsecurity');

define ( 'ESSB3_DEMO_MODE', true);
define ( 'ESSB3_ADDONS_ACTIVE', true);
define ( 'ESSB3_EASYMODE_ASKED', 'easy3-easymode-asked');

/**
 * Easy Social Share Buttons manager class to access all plugin features
 * 
 * @package EasySocialShareButtons
 * @author  appscreo
 * @since   3.4
 *
 */
class ESSB_Manager {
	
	/**
	 * Initialized as theme
	 * @since 3.4
	 */
	private $is_in_theme = false;
	
	/**
	 * Disable automatic plugin updates
	 * @since 3.4
	 */
	private $disable_updater = false;
	
	/**
	 * Component factory
	 * @since 3.4
	 */
	private $factory = array();
	
	/**
	 * Plugin settings for faster access
	 * @since 3.4
	 */
	public $settings;
	
	/**
	 * Is mobile device
	 * @var bool
	 * @since 3.4.2
	 */
	private $is_mobile = false;
	
	/**
	 * Is tablet device
	 * @var bool
	 * @since 3.4.2
	 */
	private $is_tablet = false;		
	
	/**
	 * Handle state of checked for mobile device 
	 * @var bool
	 * @since 3.4.2
	 */
	private $mobile_checked = false;
	
	private static $_instance;
	
	private function __construct() {
		// include the helper factory to get access to main plugin component
		include_once (ESSB3_PLUGIN_ROOT . 'lib/core/essb-helpers-factory.php');
		
		// default plugin options
		include_once (ESSB3_PLUGIN_ROOT . 'lib/core/options/essb-options-defaults.php');
		
		// activation/deactivation hooks
		register_activation_hook ( __FILE__, array ('ESSB_Manager', 'activate' ) );
		register_deactivation_hook ( __FILE__, array ('ESSB_Manager', 'deactivate' ) );

		// initialize plugin
		add_action( 'init', array( &$this, 'init' ), 9);
		add_action( 'plugins_loaded', array( &$this, 'load_widgets' ), 9);
		
		if (is_admin()) {
			if (!defined('ESSB3_AVOID_WELCOME') && !$this->isInTheme()) {
				function essb_page_welcome_redirect() {
					$redirect = get_transient( '_essb_page_welcome_redirect' );
					delete_transient( '_essb_page_welcome_redirect' );
					$redirect && wp_redirect( admin_url( 'admin.php?page=essb_about' ) );
				}
				add_action( 'init', 'essb_page_welcome_redirect' );
			}
		}
		
		add_action ( 'template_redirect', array ($this, 'essb_process_additional_ajax_requests' ), 1 );
		
	}
	
	/**
	 * Get static instance of class
	 * 
	 * @return ESSB_Manager
	 */
	public static function getInstance() {
		if ( ! ( self::$_instance instanceof self ) ) {
			self::$_instance = new self();
		}
	
		return self::$_instance;
	}
	
	
	/**
	 * Cloning disabled
	 */
	private function __clone() {
	}
	
	/**
	 * Serialization disabled
	 */
	private function __sleep() {
	}
	
	/**
	 * De-serialization disabled
	 */
	private function __wakeup() {
	}
	
	/**
	 * Initialize plugin load
	 */
	public function init() {		
		// activate plugin and resource builder		
		
		
		$this->resourceBuilder();		
		$this->essb();
		
		// Social Share Optimization
		if (defined('ESSB3_SSO_ACTIVE')) {
			$this->factoryActivate('sso', 'ESSBSocialShareOptimization');
		}
		
		// Social Share Analytics		
		if (defined('ESSB3_SSA_ACTIVE')) {
			$tracker = ESSBSocialShareAnalytics::get_instance();
			$this->resourceBuilder()->add_js($this->socialShareAnalytics()->generate_tracker_code(), true, 'essb-stats-tracker');
		}
		
		// After Share Actions
		if (defined('ESSB3_AFTERSHARE_ACTIVE')) {				
			foreach ($this->afterShareActions()->resource_files as $key => $object) {
				$this->resourceBuilder()->add_static_resource($object["file"], $object["key"], $object["type"]);
			}
				
			foreach ($this->afterShareActions()->js_code as $key => $code) {
				$this->resourceBuilder()->add_js($code, false, 'essbasc_custom'.$key);
			}
		
			foreach ($this->afterShareActions()->social_apis as $key => $code) {
				$this->resourceBuilder()->add_social_api($key);
			}
		}
		
		// Love this button
		if (defined('ESSB3_LOVEYOU_ACTIVE')) {
			$this->resourceBuilder()->add_js($this->loveThisButton()->generate_js_code(), true, 'essb-loveyou-code');
		}
		
		// On Media Sharing
		if (defined('ESSB3_IMAGESHARE_ACTIVE')) {
			$this->factoryActivate('essbis', 'ESSBSocialImageShare');
			$this->resourceBuilder()->add_css(ESSBResourceBuilderSnippets::css_build_imageshare_customizer(), 'essb-imageshare-customizer', 'footer');
		
		}
				
		// Social Profiles
		if (!defined('ESSB3_LIGHTMODE')) {
			if (defined('ESSB3_SOCIALPROFILES_ACTIVE')) {
				$this->factoryActivate('essbsp', 'ESSBSocialProfiles');
				$this->resourceBuilder()->add_static_resource(ESSB3_PLUGIN_URL . '/assets/css/essb-profiles.css', 'easy-social-share-buttons-profles', 'css');
			}
		}
		
		// Followers Counter
		if (defined('ESSB3_SOCIALFANS_ACTIVE')) {
			$this->factoryActivate('essbfc', 'ESSBSocialFollowersCounter');
			$this->resourceBuilder()->add_css(ESSBResourceBuilderSnippets::css_build_followerscounter_customizer(), 'essb-followerscounter-customizer', 'footer');
		}
		
		if (!defined('ESSB3_LIGHTMODE')) {
			if (defined('ESSB3_NATIVE_ACTIVE')) {
				// Social Privacy Buttons when active include resources
				$essb_spb = ESSBSocialPrivacyNativeButtons::get_instance();
				ESSBNativeButtonsHelper::$essb_spb = $essb_spb;
				foreach ($this->privacyNativeButtons()->resource_files as $key => $object) {
					$this->resourceBuilder()->add_static_resource($object["file"], $object["key"], $object["type"]);
				}
				foreach (ESSBSkinnedNativeButtons::get_assets() as $key => $object) {
					$this->resourceBuilder()->add_static_resource($object["file"], $object["key"], $object["type"]);
				}
				$this->resourceBuilder()->add_css(ESSBSkinnedNativeButtons::generate_skinned_custom_css(), 'essb-skinned-native-buttons');
					
				// asign instance of native buttons privacy class to helper
					
				// register active social network apis
				foreach (ESSBNativeButtonsHelper::get_list_of_social_apis() as $key => $code) {
					$this->resourceBuilder()->add_social_api($key);
				}
			}
		}
		
		if (is_admin()) {
			$this->asAdmin();
		}
		
	}
	
	public function essb_process_additional_ajax_requests() {
		global $essb_options;
		
		$subscribe_action = isset($_REQUEST['essb-malchimp-signup']) ? $_REQUEST['essb-malchimp-signup']: '';

		if ($subscribe_action == '1') {
			if (!class_exists('ESSBNetworks_SubscribeActions')) {
				include_once (ESSB3_PLUGIN_ROOT . 'lib/networks/essb-subscribe-actions.php');				
			}
			
			ESSBNetworks_SubscribeActions::process_subscribe();
			
			die();
		}
		
		if (defined('ESSB3_CACHED_COUNTERS')) {
			if (ESSBGlobalSettings::$cached_counters_cache_mode) {
				if (isset($_REQUEST['essb_counter_cache']) && $_REQUEST['essb_counter_cache'] == 'rebuild') {
					$share_details = essb_core()->get_post_share_details('');
					$share_details['full_url'] = $share_details['url'];
					$networks = ESSBOptionValuesHelper::options_value($essb_options, 'networks');
					$result = ESSBCachedCounters::get_counters(get_the_ID(), $share_details, $networks);
					echo json_encode($result);
					die();
				}		
			}
		}
	}
	
	/**
	 * Load plugin active widgets based on user settings
	 */
	public function load_widgets() {
		// include the main plugin required files
		include_once (ESSB3_PLUGIN_ROOT . 'lib/essb-core-includes.php');

		if (is_admin()) {
				
			global $essb_options;
			$exist_user_purchase_code = isset($essb_options['purchase_code']) ? $essb_options['purchase_code'] : '';
		
			if (!empty($exist_user_purchase_code) && !$this->isInTheme()) {
		
				include (ESSB3_PLUGIN_ROOT . 'lib/external/autoupdate/plugin-update-checker.php');
				// @since 1.3.3
				// autoupdate
				// activating autoupdate option
				$essb_autoupdate = PucFactory::buildUpdateChecker ( 'http://update.creoworx.com/essb3/', __FILE__, 'easy-social-share-buttons3' );
				// @since 1.3.7.2 - update to avoid issues with other plugins that uses same
				// method
				function addSecretKeyESSB3($query) {
					global $exist_user_purchase_code;
					$query ['license'] = $exist_user_purchase_code;
					return $query;
				}
				$essb_autoupdate->addQueryArgFilter ( 'addSecretKeyESSB3' );
			}
		
		}
	}
	
	/**
	 * setIsInTheme
	 * 
	 * Tell plugin that is loaded in theme - disable automatic updates and disable redirect after install
	 * @param bool $value
	 */
	public function setIsInTheme ( $value = true) {
		$this->is_in_theme = (boolean) $value;
	}
	
	public function isInTheme () {
		return (boolean) $this->is_in_theme;
	}
	
	public function disableUpdates() {
		$this->disable_updater = true;
	}
	
	public function resourceBuilder() {
		if (!isset($this->factory['resource_builder'])) {
			$this->factory['resource_builder'] = new ESSBResourceBuilder();
		}
		
		return $this->factory['resource_builder'];
	}
	
	public function essb() {
		if (!isset($this->factory['essb'])) {
			$this->factory['essb'] = new ESSBCore();
		}
		
		return $this->factory['essb'];
	}
	
	public function socialShareAnalytics() {
		if (!isset($this->factory['ssa'])) {
			$this->factory['ssa'] = new ESSBSocialShareAnalytics;
		}
		
		return $this->factory['ssa'];
	}
	
	public function afterShareActions() {
		if (!isset($this->factory['asc'])) {
			$this->factory['asc'] = new ESSBAfterCloseShare3;
		}
		
		return $this->factory['asc'];
	}
	
	public function loveThisButton() {
		//ESSBNetworks_LoveThis
		if (!isset($this->factory['loveThisButton'])) {
			$this->factory['loveThisButton'] = new ESSBNetworks_LoveThis;
		}
		
		return $this->factory['loveThisButton'];
	}
	
	public function privacyNativeButtons() {
		if (!isset($this->factory['nativeprivacy'])) {
			$this->factory['nativeprivacy'] = new ESSBSocialPrivacyNativeButtons;
		}
		
		return $this->factory['nativeprivacy'];
	}
	
	public function socialFollowersCounter() {
		if (!isset($this->factory['essbfc'])) {
			$this->factory['essbfc'] = new ESSBSocialFollowersCounter;
		}
		
		return $this->factory['essbfc'];
	}
	
	public function deactiveExecution() {
		$this->essb()->temporary_deactivate_content_filters();
	}
	
	public function reactivateExecution() {
		$this->essb()->reactivate_content_filters_after_temporary_deactivate();
	}
	
	public function essbOptions() {
		if (!isset($this->settings)) {
			$this->settings = get_option(ESSB3_OPTIONS_NAME);
		}
		
		return $this->settings;
	}
	
	public function optionsValue($param, $default = '') {
		return isset ( $this->settings [$param] ) ? $this->settings [$param]  : $default;
	}
	
	public function optionsBoolValue($param) {
		$value = isset ( $this->settings [$param] ) ? $this->settings [$param]  : 'false';
	
		if ($value == "true") {
			return true;
		}
		else {
			return false;
		}	
	}
	
	/**
	 * isMobile
	 * 
	 * Checks and return state of mobile device detected
	 * 
	 * @return boolean
	 * @since 3.4.2
	 */
	public function isMobile() {
		if (!$this->mobile_checked) {
			$this->mobile_checked = true;
			$mobile_detect = new ESSB_Mobile_Detect();
			
			$this->is_mobile = $mobile_detect->isMobile();
			$this->is_tablet = $mobile_detect->isTablet();
			
			if ($this->optionsBoolValue('mobile_exclude_tablet') && $this->is_tablet) {
				$this->is_mobile = false;
			}
			
			return $this->is_mobile;
		}
		else {
			return $this->is_mobile;
		}
	}
	
	/**
	 * Run admin part of code, when user with admin capabilites is detected
	 * 
	 * @since 3.4
	 */
	protected function asAdmin() {
		include_once (ESSB3_PLUGIN_ROOT . 'lib/admin/essb-admin-includes.php');
		$this->factoryActivate('essb_admin', 'ESSBAdminControler');
		
	}
	
	/**
	 * factoryActivate
	 * 
	 * Load plugin component into main class
	 * 
	 * @param string $module
	 * @param object $class_name
	 * @since 3.4
	 */
	protected function factoryActivate($module = '', $class_name) {
		if (!empty($module) && !isset($this->factory[$module])) {
			$this->factory[$module] = new $class_name;
		}
	}
		
	
	/*
	 * Static activation/deactivation hooks
	 */
	
	public static function activate() {
		global $essb_networks;
	
		update_option(ESSB3_NETWORK_LIST, $essb_networks);
	
		$mail_salt_check = get_option(ESSB3_MAIL_SALT);
		if (!$mail_salt_check || empty($mail_salt_check)) {
			$new_salt = mt_rand();
			update_option(ESSB3_MAIL_SALT, $new_salt);
		}
	
		$exist_settings = get_option(ESSB3_OPTIONS_NAME);
		if (!$exist_settings) {
			$default_options = 'eyJidXR0b25fc3R5bGUiOiJidXR0b24iLCJzdHlsZSI6IjIyIiwiY3NzX2FuaW1hdGlvbnMiOiJubyIsImZ1bGx3aWR0aF9zaGFyZV9idXR0b25zX2NvbHVtbnMiOiIxIiwibmV0d29ya3MiOlsiZmFjZWJvb2siLCJ0d2l0dGVyIiwiZ29vZ2xlIiwicGludGVyZXN0IiwibGlua2VkaW4iXSwibmV0d29ya3Nfb3JkZXIiOlsiZmFjZWJvb2siLCJ0d2l0dGVyIiwiZ29vZ2xlIiwicGludGVyZXN0IiwibGlua2VkaW4iLCJkaWdnIiwiZGVsIiwic3R1bWJsZXVwb24iLCJ0dW1ibHIiLCJ2ayIsInByaW50IiwibWFpbCIsImZsYXR0ciIsInJlZGRpdCIsImJ1ZmZlciIsImxvdmUiLCJ3ZWlibyIsInBvY2tldCIsInhpbmciLCJvayIsIm13cCIsIm1vcmUiLCJ3aGF0c2FwcCIsIm1lbmVhbWUiLCJibG9nZ2VyIiwiYW1hem9uIiwieWFob29tYWlsIiwiZ21haWwiLCJhb2wiLCJuZXdzdmluZSIsImhhY2tlcm5ld3MiLCJldmVybm90ZSIsIm15c3BhY2UiLCJtYWlscnUiLCJ2aWFkZW8iLCJsaW5lIiwiZmxpcGJvYXJkIiwiY29tbWVudHMiLCJ5dW1tbHkiLCJzbXMiLCJ2aWJlciIsInRlbGVncmFtIl0sIm1vcmVfYnV0dG9uX2Z1bmMiOiIxIiwibW9yZV9idXR0b25faWNvbiI6InBsdXMiLCJ0d2l0dGVyX3NoYXJlc2hvcnRfc2VydmljZSI6IndwIiwibWFpbF9mdW5jdGlvbiI6ImZvcm0iLCJ3aGF0c2FwcF9zaGFyZXNob3J0X3NlcnZpY2UiOiJ3cCIsImZsYXR0cl9sYW5nIjoic3FfQUwiLCJjb3VudGVyX3BvcyI6InJpZ2h0bSIsImZvcmNlX2NvdW50ZXJzX2FkbWluX3R5cGUiOiJ3cCIsInRvdGFsX2NvdW50ZXJfcG9zIjoibGVmdGJpZyIsInVzZXJfbmV0d29ya19uYW1lX2ZhY2Vib29rIjoiRmFjZWJvb2siLCJ1c2VyX25ldHdvcmtfbmFtZV90d2l0dGVyIjoiVHdpdHRlciIsInVzZXJfbmV0d29ya19uYW1lX2dvb2dsZSI6Ikdvb2dsZSsiLCJ1c2VyX25ldHdvcmtfbmFtZV9waW50ZXJlc3QiOiJQaW50ZXJlc3QiLCJ1c2VyX25ldHdvcmtfbmFtZV9saW5rZWRpbiI6IkxpbmtlZEluIiwidXNlcl9uZXR3b3JrX25hbWVfZGlnZyI6IkRpZ2ciLCJ1c2VyX25ldHdvcmtfbmFtZV9kZWwiOiJEZWwiLCJ1c2VyX25ldHdvcmtfbmFtZV9zdHVtYmxldXBvbiI6IlN0dW1ibGVVcG9uIiwidXNlcl9uZXR3b3JrX25hbWVfdHVtYmxyIjoiVHVtYmxyIiwidXNlcl9uZXR3b3JrX25hbWVfdmsiOiJWS29udGFrdGUiLCJ1c2VyX25ldHdvcmtfbmFtZV9wcmludCI6IlByaW50IiwidXNlcl9uZXR3b3JrX25hbWVfbWFpbCI6IkVtYWlsIiwidXNlcl9uZXR3b3JrX25hbWVfZmxhdHRyIjoiRmxhdHRyIiwidXNlcl9uZXR3b3JrX25hbWVfcmVkZGl0IjoiUmVkZGl0IiwidXNlcl9uZXR3b3JrX25hbWVfYnVmZmVyIjoiQnVmZmVyIiwidXNlcl9uZXR3b3JrX25hbWVfbG92ZSI6IkxvdmUgVGhpcyIsInVzZXJfbmV0d29ya19uYW1lX3dlaWJvIjoiV2VpYm8iLCJ1c2VyX25ldHdvcmtfbmFtZV9wb2NrZXQiOiJQb2NrZXQiLCJ1c2VyX25ldHdvcmtfbmFtZV94aW5nIjoiWGluZyIsInVzZXJfbmV0d29ya19uYW1lX29rIjoiT2Rub2tsYXNzbmlraSIsInVzZXJfbmV0d29ya19uYW1lX213cCI6Ik1hbmFnZVdQLm9yZyIsInVzZXJfbmV0d29ya19uYW1lX21vcmUiOiJNb3JlIEJ1dHRvbiIsInVzZXJfbmV0d29ya19uYW1lX3doYXRzYXBwIjoiV2hhdHNBcHAiLCJ1c2VyX25ldHdvcmtfbmFtZV9tZW5lYW1lIjoiTWVuZWFtZSIsInVzZXJfbmV0d29ya19uYW1lX2Jsb2dnZXIiOiJCbG9nZ2VyIiwidXNlcl9uZXR3b3JrX25hbWVfYW1hem9uIjoiQW1hem9uIiwidXNlcl9uZXR3b3JrX25hbWVfeWFob29tYWlsIjoiWWFob28gTWFpbCIsInVzZXJfbmV0d29ya19uYW1lX2dtYWlsIjoiR21haWwiLCJ1c2VyX25ldHdvcmtfbmFtZV9hb2wiOiJBT0wiLCJ1c2VyX25ldHdvcmtfbmFtZV9uZXdzdmluZSI6Ik5ld3N2aW5lIiwidXNlcl9uZXR3b3JrX25hbWVfaGFja2VybmV3cyI6IkhhY2tlck5ld3MiLCJ1c2VyX25ldHdvcmtfbmFtZV9ldmVybm90ZSI6IkV2ZXJub3RlIiwidXNlcl9uZXR3b3JrX25hbWVfbXlzcGFjZSI6Ik15U3BhY2UiLCJ1c2VyX25ldHdvcmtfbmFtZV9tYWlscnUiOiJNYWlsLnJ1IiwidXNlcl9uZXR3b3JrX25hbWVfdmlhZGVvIjoiVmlhZGVvIiwidXNlcl9uZXR3b3JrX25hbWVfbGluZSI6IkxpbmUiLCJ1c2VyX25ldHdvcmtfbmFtZV9mbGlwYm9hcmQiOiJGbGlwYm9hcmQiLCJ1c2VyX25ldHdvcmtfbmFtZV9jb21tZW50cyI6IkNvbW1lbnRzIiwidXNlcl9uZXR3b3JrX25hbWVfeXVtbWx5IjoiWXVtbWx5IiwiZ2FfdHJhY2tpbmdfbW9kZSI6InNpbXBsZSIsInR3aXR0ZXJfY2FyZF90eXBlIjoic3VtbWFyeSIsIm5hdGl2ZV9vcmRlciI6WyJnb29nbGUiLCJ0d2l0dGVyIiwiZmFjZWJvb2siLCJsaW5rZWRpbiIsInBpbnRlcmVzdCIsInlvdXR1YmUiLCJtYW5hZ2V3cCIsInZrIl0sImZhY2Vib29rX2xpa2VfdHlwZSI6Imxpa2UiLCJnb29nbGVfbGlrZV90eXBlIjoicGx1cyIsInR3aXR0ZXJfdHdlZXQiOiJmb2xsb3ciLCJwaW50ZXJlc3RfbmF0aXZlX3R5cGUiOiJmb2xsb3ciLCJza2luX25hdGl2ZV9za2luIjoiZmxhdCIsInByb2ZpbGVzX2J1dHRvbl90eXBlIjoic3F1YXJlIiwicHJvZmlsZXNfYnV0dG9uX2ZpbGwiOiJmaWxsIiwicHJvZmlsZXNfYnV0dG9uX3NpemUiOiJzbWFsbCIsInByb2ZpbGVzX2Rpc3BsYXlfcG9zaXRpb24iOiJsZWZ0IiwicHJvZmlsZXNfb3JkZXIiOlsidHdpdHRlciIsImZhY2Vib29rIiwiZ29vZ2xlIiwicGludGVyZXN0IiwiZm91cnNxdWFyZSIsInlhaG9vIiwic2t5cGUiLCJ5ZWxwIiwiZmVlZGJ1cm5lciIsImxpbmtlZGluIiwidmlhZGVvIiwieGluZyIsIm15c3BhY2UiLCJzb3VuZGNsb3VkIiwic3BvdGlmeSIsImdyb292ZXNoYXJrIiwibGFzdGZtIiwieW91dHViZSIsInZpbWVvIiwiZGFpbHltb3Rpb24iLCJ2aW5lIiwiZmxpY2tyIiwiNTAwcHgiLCJpbnN0YWdyYW0iLCJ3b3JkcHJlc3MiLCJ0dW1ibHIiLCJibG9nZ2VyIiwidGVjaG5vcmF0aSIsInJlZGRpdCIsImRyaWJiYmxlIiwic3R1bWJsZXVwb24iLCJkaWdnIiwiZW52YXRvIiwiYmVoYW5jZSIsImRlbGljaW91cyIsImRldmlhbnRhcnQiLCJmb3Jyc3QiLCJwbGF5IiwiemVycGx5Iiwid2lraXBlZGlhIiwiYXBwbGUiLCJmbGF0dHIiLCJnaXRodWIiLCJjaGltZWluIiwiZnJpZW5kZmVlZCIsIm5ld3N2aW5lIiwiaWRlbnRpY2EiLCJiZWJvIiwienluZ2EiLCJzdGVhbSIsInhib3giLCJ3aW5kb3dzIiwib3V0bG9vayIsImNvZGVyd2FsbCIsInRyaXBhZHZpc29yIiwiYXBwbmV0IiwiZ29vZHJlYWRzIiwidHJpcGl0IiwibGFueXJkIiwic2xpZGVzaGFyZSIsImJ1ZmZlciIsInJzcyIsInZrb250YWt0ZSIsImRpc3F1cyIsImhvdXp6IiwibWFpbCIsInBhdHJlb24iLCJwYXlwYWwiLCJwbGF5c3RhdGlvbiIsInNtdWdtdWciLCJzd2FybSIsInRyaXBsZWoiLCJ5YW1tZXIiLCJzdGFja292ZXJmbG93IiwiZHJ1cGFsIiwib2Rub2tsYXNzbmlraSIsImFuZHJvaWQiLCJtZWV0dXAiLCJwZXJzb25hIl0sImFmdGVyY2xvc2VfdHlwZSI6ImZvbGxvdyIsImFmdGVyY2xvc2VfbGlrZV9jb2xzIjoib25lY29sIiwiZXNtbF90dGwiOiIxIiwiZXNtbF9wcm92aWRlciI6InNoYXJlZGNvdW50IiwiZXNtbF9hY2Nlc3MiOiJtYW5hZ2Vfb3B0aW9ucyIsInNob3J0dXJsX3R5cGUiOiJ3cCIsImRpc3BsYXlfaW5fdHlwZXMiOlsicG9zdCJdLCJkaXNwbGF5X2V4Y2VycHRfcG9zIjoidG9wIiwidG9wYmFyX2J1dHRvbnNfYWxpZ24iOiJsZWZ0IiwidG9wYmFyX2NvbnRlbnRhcmVhX3BvcyI6ImxlZnQiLCJib3R0b21iYXJfYnV0dG9uc19hbGlnbiI6ImxlZnQiLCJib3R0b21iYXJfY29udGVudGFyZWFfcG9zIjoibGVmdCIsImZseWluX3Bvc2l0aW9uIjoicmlnaHQiLCJzaXNfbmV0d29ya19vcmRlciI6WyJmYWNlYm9vayIsInR3aXR0ZXIiLCJnb29nbGUiLCJsaW5rZWRpbiIsInBpbnRlcmVzdCIsInR1bWJsciIsInJlZGRpdCIsImRpZ2ciLCJkZWxpY2lvdXMiLCJ2a29udGFrdGUiLCJvZG5va2xhc3NuaWtpIl0sInNpc19zdHlsZSI6ImZsYXQtc21hbGwiLCJzaXNfYWxpZ25feCI6ImxlZnQiLCJzaXNfYWxpZ25feSI6InRvcCIsInNpc19vcmllbnRhdGlvbiI6Imhvcml6b250YWwiLCJtb2JpbGVfc2hhcmVidXR0b25zYmFyX2NvdW50IjoiMiIsInNoYXJlYmFyX2NvdW50ZXJfcG9zIjoiaW5zaWRlIiwic2hhcmViYXJfdG90YWxfY291bnRlcl9wb3MiOiJiZWZvcmUiLCJzaGFyZWJhcl9uZXR3b3Jrc19vcmRlciI6WyJmYWNlYm9va3xGYWNlYm9vayIsInR3aXR0ZXJ8VHdpdHRlciIsImdvb2dsZXxHb29nbGUrIiwicGludGVyZXN0fFBpbnRlcmVzdCIsImxpbmtlZGlufExpbmtlZEluIiwiZGlnZ3xEaWdnIiwiZGVsfERlbCIsInN0dW1ibGV1cG9ufFN0dW1ibGVVcG9uIiwidHVtYmxyfFR1bWJsciIsInZrfFZLb250YWt0ZSIsInByaW50fFByaW50IiwibWFpbHxFbWFpbCIsImZsYXR0cnxGbGF0dHIiLCJyZWRkaXR8UmVkZGl0IiwiYnVmZmVyfEJ1ZmZlciIsImxvdmV8TG92ZSBUaGlzIiwid2VpYm98V2VpYm8iLCJwb2NrZXR8UG9ja2V0IiwieGluZ3xYaW5nIiwib2t8T2Rub2tsYXNzbmlraSIsIm13cHxNYW5hZ2VXUC5vcmciLCJtb3JlfE1vcmUgQnV0dG9uIiwid2hhdHNhcHB8V2hhdHNBcHAiLCJtZW5lYW1lfE1lbmVhbWUiLCJibG9nZ2VyfEJsb2dnZXIiLCJhbWF6b258QW1hem9uIiwieWFob29tYWlsfFlhaG9vIE1haWwiLCJnbWFpbHxHbWFpbCIsImFvbHxBT0wiLCJuZXdzdmluZXxOZXdzdmluZSIsImhhY2tlcm5ld3N8SGFja2VyTmV3cyIsImV2ZXJub3RlfEV2ZXJub3RlIiwibXlzcGFjZXxNeVNwYWNlIiwibWFpbHJ1fE1haWwucnUiLCJ2aWFkZW98VmlhZGVvIiwibGluZXxMaW5lIiwiZmxpcGJvYXJkfEZsaXBib2FyZCIsImNvbW1lbnRzfENvbW1lbnRzIiwieXVtbWx5fFl1bW1seSJdLCJzaGFyZXBvaW50X2NvdW50ZXJfcG9zIjoiaW5zaWRlIiwic2hhcmVwb2ludF90b3RhbF9jb3VudGVyX3BvcyI6ImJlZm9yZSIsInNoYXJlcG9pbnRfbmV0d29ya3Nfb3JkZXIiOlsiZmFjZWJvb2t8RmFjZWJvb2siLCJ0d2l0dGVyfFR3aXR0ZXIiLCJnb29nbGV8R29vZ2xlKyIsInBpbnRlcmVzdHxQaW50ZXJlc3QiLCJsaW5rZWRpbnxMaW5rZWRJbiIsImRpZ2d8RGlnZyIsImRlbHxEZWwiLCJzdHVtYmxldXBvbnxTdHVtYmxlVXBvbiIsInR1bWJscnxUdW1ibHIiLCJ2a3xWS29udGFrdGUiLCJwcmludHxQcmludCIsIm1haWx8RW1haWwiLCJmbGF0dHJ8RmxhdHRyIiwicmVkZGl0fFJlZGRpdCIsImJ1ZmZlcnxCdWZmZXIiLCJsb3ZlfExvdmUgVGhpcyIsIndlaWJvfFdlaWJvIiwicG9ja2V0fFBvY2tldCIsInhpbmd8WGluZyIsIm9rfE9kbm9rbGFzc25pa2kiLCJtd3B8TWFuYWdlV1Aub3JnIiwibW9yZXxNb3JlIEJ1dHRvbiIsIndoYXRzYXBwfFdoYXRzQXBwIiwibWVuZWFtZXxNZW5lYW1lIiwiYmxvZ2dlcnxCbG9nZ2VyIiwiYW1hem9ufEFtYXpvbiIsInlhaG9vbWFpbHxZYWhvbyBNYWlsIiwiZ21haWx8R21haWwiLCJhb2x8QU9MIiwibmV3c3ZpbmV8TmV3c3ZpbmUiLCJoYWNrZXJuZXdzfEhhY2tlck5ld3MiLCJldmVybm90ZXxFdmVybm90ZSIsIm15c3BhY2V8TXlTcGFjZSIsIm1haWxydXxNYWlsLnJ1IiwidmlhZGVvfFZpYWRlbyIsImxpbmV8TGluZSIsImZsaXBib2FyZHxGbGlwYm9hcmQiLCJjb21tZW50c3xDb21tZW50cyIsInl1bW1seXxZdW1tbHkiXSwic2hhcmVib3R0b21fbmV0d29ya3Nfb3JkZXIiOlsiZmFjZWJvb2t8RmFjZWJvb2siLCJ0d2l0dGVyfFR3aXR0ZXIiLCJnb29nbGV8R29vZ2xlKyIsInBpbnRlcmVzdHxQaW50ZXJlc3QiLCJsaW5rZWRpbnxMaW5rZWRJbiIsImRpZ2d8RGlnZyIsImRlbHxEZWwiLCJzdHVtYmxldXBvbnxTdHVtYmxlVXBvbiIsInR1bWJscnxUdW1ibHIiLCJ2a3xWS29udGFrdGUiLCJwcmludHxQcmludCIsIm1haWx8RW1haWwiLCJmbGF0dHJ8RmxhdHRyIiwicmVkZGl0fFJlZGRpdCIsImJ1ZmZlcnxCdWZmZXIiLCJsb3ZlfExvdmUgVGhpcyIsIndlaWJvfFdlaWJvIiwicG9ja2V0fFBvY2tldCIsInhpbmd8WGluZyIsIm9rfE9kbm9rbGFzc25pa2kiLCJtd3B8TWFuYWdlV1Aub3JnIiwibW9yZXxNb3JlIEJ1dHRvbiIsIndoYXRzYXBwfFdoYXRzQXBwIiwibWVuZWFtZXxNZW5lYW1lIiwiYmxvZ2dlcnxCbG9nZ2VyIiwiYW1hem9ufEFtYXpvbiIsInlhaG9vbWFpbHxZYWhvbyBNYWlsIiwiZ21haWx8R21haWwiLCJhb2x8QU9MIiwibmV3c3ZpbmV8TmV3c3ZpbmUiLCJoYWNrZXJuZXdzfEhhY2tlck5ld3MiLCJldmVybm90ZXxFdmVybm90ZSIsIm15c3BhY2V8TXlTcGFjZSIsIm1haWxydXxNYWlsLnJ1IiwidmlhZGVvfFZpYWRlbyIsImxpbmV8TGluZSIsImZsaXBib2FyZHxGbGlwYm9hcmQiLCJjb21tZW50c3xDb21tZW50cyIsInl1bW1seXxZdW1tbHkiXSwiY29udGVudF9wb3NpdGlvbiI6ImNvbnRlbnRfYm90dG9tIiwiZXNzYl9jYWNoZV9tb2RlIjoiZnVsbCIsInR1cm5vZmZfZXNzYl9hZHZhbmNlZF9ib3giOiJ0cnVlIiwiZXNzYl9hY2Nlc3MiOiJtYW5hZ2Vfb3B0aW9ucyIsImFwcGx5X2NsZWFuX2J1dHRvbnNfbWV0aG9kIjoiZGVmYXVsdCIsIm1haWxfc3ViamVjdCI6IlZpc2l0IHRoaXMgc2l0ZSAlJXNpdGV1cmwlJSIsIm1haWxfYm9keSI6IkhpLCB0aGlzIG1heSBiZSBpbnRlcmVzdGluZyB5b3U6ICUldGl0bGUlJSEgVGhpcyBpcyB0aGUgbGluazogJSVwZXJtYWxpbmslJSIsImZhY2Vib29rdG90YWwiOiJ0cnVlIiwiYWN0aXZhdGVfdG90YWxfY291bnRlcl90ZXh0Ijoic2hhcmVzIiwiZnVsbHdpZHRoX2FsaWduIjoibGVmdCIsInR3aXR0ZXJfbWVzc2FnZV9vcHRpbWl6ZV9tZXRob2QiOiIxIiwibWFpbF9mdW5jdGlvbl9jb21tYW5kIjoiaG9zdCIsIm1haWxfZnVuY3Rpb25fc2VjdXJpdHkiOiJsZXZlbDEiLCJ0d2l0dGVyX2NvdW50ZXJzIjoic2VsZiIsImNhY2hlX2NvdW50ZXJfcmVmcmVzaCI6IjEiLCJ0d2l0dGVyX3NoYXJlc2hvcnQiOiJ0cnVlIn0=';

			// set that we run plugin for first time
			update_option(ESSB3_FIRST_TIME_NAME, 'true');
			$options_base = ESSB_Manager::convert_ready_made_option($default_options);
			if ($options_base) {
				update_option(ESSB3_OPTIONS_NAME, $options_base);
			}
		}
	
		// activate redirection hook
		if ( ! is_network_admin() ) {
			set_transient( '_essb_page_welcome_redirect', 1, 30 );
		}
	}
	
	public static function convert_ready_made_option($options) {
		$options = base64_decode ( $options );
	
		$options = htmlspecialchars_decode ( $options );
		$options = stripslashes ( $options );
	
		if ($options != '') {
			$imported_options = json_decode ( $options, true );
	
			return $imported_options;
		}
		else {
			return null;
		}
	}
	
	public static function deactivate() {
		delete_option(ESSB3_MAIL_SALT);
	}
}

/**
 * Initialize plugin with main global instace of ESSB_Manager
 * 
 * @since 3.4
 */

global $essb_manager;
if (!$essb_manager) {
	$essb_manager = ESSB_Manager::getInstance();
}