<?php

// inialize plugin options
global $essb_options, $essb_networks;
$essb_options = get_option(ESSB3_OPTIONS_NAME);
$essb_networks = essb_available_social_networks();

//@since 3.4.1 - allow easy mode
$easymode_state = get_option(ESSB3_EASYMODE_NAME);
if ($easymode_state) {
	if ($easymode_state == 'true') {
		define('ESSB3_LIGHTMODE', true);
	}
}
//print_r($essb_options);
// end: initialize plugin working options

// include options helper functions
include_once (ESSB3_PLUGIN_ROOT . 'lib/core/options/essb-options-helper.php');
include_once (ESSB3_PLUGIN_ROOT . 'lib/core/essb-global-settings.php');
include_once (ESSB3_PLUGIN_ROOT . 'lib/core/essb-url-helper.php');
include_once (ESSB3_PLUGIN_ROOT . 'lib/core/widgets/essb-share-widget.php');
include_once (ESSB3_PLUGIN_ROOT . 'lib/core/widgets/essb-share-subscribe-widget.php');
include_once (ESSB3_PLUGIN_ROOT . 'lib/core/widgets/essb-popular-posts-widget-shortcode.php');

if (defined('ESSB3_LIGHTMODE')) {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/core/essb-lightmode-helper.php');
	$essb_options = ESSBLightModeHelper::apply_global_options($essb_options);
}

// initialize global plugin settings from version 3.4.1
ESSBGlobalSettings::load($essb_options);

// init admin bar menu
// admin bar menu
$disable_admin_menu = ESSBOptionValuesHelper::options_bool_value($essb_options, 'disable_adminbar_menu');
// update relted to WordPress 4.4 changes
if (!$disable_admin_menu) {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/core/essb-adminbar-menu.php');
	add_action ( "init", "ESSBAdminMenuInit3" );
	
	function ESSBAdminMenuInit3() {
		global $essb_adminmenu;
		
		if (is_admin_bar_showing()) {
			$essb_adminmenu = new ESSBAdminBarMenu3();
		}
	}
}

if (ESSBOptionValuesHelper::is_active_module('cachedynamic')) {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/core/cache/essb-dynamic-cache.php');
	$cache_mode = ESSBOptionValuesHelper::options_value($essb_options, 'essb_cache_mode');
	ESSBDynamicCache::activate($cache_mode);
}

if (ESSBOptionValuesHelper::is_active_module('precompiled')) {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/core/cache/essb-precompiled.php');
	ESSBPrecompiledResources::activate();
}


if (ESSBOptionValuesHelper::is_active_module('cachestatic')) {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/core/cache/essb-static-cache.php');
}


// dynamic resource builder
include_once (ESSB3_PLUGIN_ROOT . 'lib/core/essb-resource-builder.php');
include_once (ESSB3_PLUGIN_ROOT . 'lib/core/essb-resource-builder-snippets.php');

include_once (ESSB3_PLUGIN_ROOT . 'lib/external/mobile-detect/mobile-detect.php');

// include social network related plugin classes

if (ESSBOptionValuesHelper::is_active_module('loveyou')) {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/networks/essb-loveyou.php');
	define('ESSB3_LOVEYOU_ACTIVE', true);
}

if (!defined('ESSB3_LIGHTMODE')) {
	if (ESSBOptionValuesHelper::is_active_module('native')) {
		include_once (ESSB3_PLUGIN_ROOT . 'lib/core/native-buttons/essb-skinned-native-button.php');
		include_once (ESSB3_PLUGIN_ROOT . 'lib/core/native-buttons/essb-social-privacy.php');
		include_once (ESSB3_PLUGIN_ROOT . 'lib/core/native-buttons/essb-native-buttons-helper.php');
		define('ESSB3_NATIVE_ACTIVE', true);
	}
}
// including additional plugin modules
if (ESSBOptionValuesHelper::is_active_module('sso')) {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/social-share-optimization/essb-social-share-optimization-frontend.php');
	define('ESSB3_SSO_ACTIVE', true);
}

if (ESSBOptionValuesHelper::is_active_module('ssanalytics')) {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/social-share-analytics/essb-social-share-analytics.php');
	define('ESSB3_SSA_ACTIVE', true);
}

if (ESSBOptionValuesHelper::is_active_module('mycred')) {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/mycred/essb-mycred-integration.php');
	define('ESSB3_MYCRED_ACTIVE', true);
	ESSBMyCredIntegration::get_instance();
}

if (ESSBOptionValuesHelper::is_active_module('mycred_hook')) {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/mycred/essb-mycred-custom-hook.php');
	define('ESSB3_MYCRED_CUSTOM_ACTIVE', true);
}

if (ESSBOptionValuesHelper::is_active_module('aftershare')) {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/after-share-close/essb-after-share-close.php');
	define('ESSB3_AFTERSHARE_ACTIVE', true);
}
else{
	if (ESSB3_DEMO_MODE) {
		$is_active_option = isset($_REQUEST['aftershare']) ? $_REQUEST['aftershare'] : '';
		if ($is_active_option != '') {
			include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/after-share-close/essb-after-share-close.php');
			define('ESSB3_AFTERSHARE_ACTIVE', true);
			
		}
	}
}

if (ESSBOptionValuesHelper::is_active_module('imageshare')) {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/social-image-share/essb-social-image-share.php');
	define('ESSB3_IMAGESHARE_ACTIVE', true);
}

if (!defined('ESSB3_LIGHTMODE')) {
	if (ESSBOptionValuesHelper::is_active_module('socialprofiles')) {
		include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/social-profiles/essb-social-profiles.php');
		define('ESSB3_SOCIALPROFILES_ACTIVE', 'true');
	}
	// Social Profiles Widget is always available
	include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/social-profiles/essb-social-profiles-widget.php');
}

if (ESSBOptionValuesHelper::is_active_module('socialfans')) {
	define('ESSB3_SOCIALFANS_ACTIVE', 'true');
	
	global $essb_socialfans_options;
	$essb_socialfans_options = get_option(ESSB3_OPTIONS_NAME_FANSCOUNTER);
	include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/social-followers-counter/essb-social-followers-counter-helper.php');
	
	// if options does not exist we intialize the default settings
	if (!is_array($essb_socialfans_options)) { 
		$essb_socialfans_options = array();
		$essb_socialfans_options['expire'] = 1400;
		$essb_socialfans_options['format'] = 'short';
		
		// apply default values from structure helper
		$essb_socialfans_options = ESSBSocialFollowersCounterHelper::create_default_options_from_structure($essb_socialfans_options);
	}
	
		// include widget class
	include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/social-followers-counter/essb-social-followers-counter-widget.php');
	include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/social-followers-counter/essb-social-followers-counter.php');
}

if (!defined('ESSB3_LIGHTMODE')) {
	if (ESSBOptionValuesHelper::is_active_module('metricslite')) {
		define('ESSB3_ESML_ACTIVE', 'true');
		include_once(ESSB3_PLUGIN_ROOT . 'lib/modules/social-metrics-lite/easy-social-metrics-lite.php');
	}
	
	if (ESSBOptionValuesHelper::is_active_module('topsocialposts')) {
		define('ESSB3_ESML_TOPPOSTS_ACTIVE', 'true');
		include_once(ESSB3_PLUGIN_ROOT . 'lib/modules/top-posts-widget/essb-top-posts-widget.php');	
	}
}

if (ESSBOptionValuesHelper::is_active_module('cachedcounters')) {
	define('ESSB3_CACHED_COUNTERS', true);
	include_once(ESSB3_PLUGIN_ROOT . 'lib/core/share-counters/essb-cached-counters.php');	
	
	if (ESSBOptionValuesHelper::is_active_module('counterrecovery')) {
		define('ESSB3_SHARED_COUNTER_RECOVERY', true);
	}
}

// click to tweet module
if (ESSBOptionValuesHelper::is_active_module('ctt')) {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/click-to-tweet/essb-click-to-tweet.php');
}

// visual composer element bridge
if (function_exists('vc_map')) {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/visual-composer/essb-visual-composer-map.php');
}


//include_once (ESSB3_PLUGIN_ROOT . 'lib/core/essb-counters-helper.php');
include_once (ESSB3_PLUGIN_ROOT . 'lib/core/essb-core-helper.php');
include_once (ESSB3_PLUGIN_ROOT . 'lib/core/essb-button-helper.php');
include_once (ESSB3_PLUGIN_ROOT . 'lib/essb-core.php');

?>