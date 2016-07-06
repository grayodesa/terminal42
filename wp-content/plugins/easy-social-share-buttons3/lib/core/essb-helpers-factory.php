<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


if (!function_exists('essb_manager')) {
	function essb_manager() {
		return ESSB_Manager::getInstance();
	}
}

if (!function_exists('essb_core')) {
	function essb_core() {
		return essb_manager()->essb();
	}
}

if (!function_exists('essb_resource_builder')) {
	function essb_resource_builder() {
		return essb_manager()->resourceBuilder();
	}
}

if (!function_exists('easy_share_deactivate')) {
	function easy_share_deactivate() {
		essb_manager()->deactiveExecution();
	}
}

if (!function_exists('easy_share_reactivate')) {
	function easy_share_reactivate() {
		essb_manager()->reactivateExecution();
	}
}

if (!function_exists('essb_native_privacy')) {
	function essb_native_privacy() {
		return essb_manager()->privacyNativeButtons();
	}
}

if (!function_exists ('essb_options_value')) {
	function essb_options_value($param, $default = '') {
		return essb_manager()->optionsValue($param, $default);
	}
}

if (!function_exists('essb_options_bool_value')) {
	function essb_options_bool_value($param) {
		return essb_manager()->optionsBoolValue($param);
	}
}

if (!function_exists('essb_options')) {
	function essb_options() {
		return essb_manager()->essbOptions();
	}
}

if (!function_exists('essb_followers_counter')) {
	function essb_followers_counter() {
		return essb_manager()->socialFollowersCounter();
	}
}

if (!function_exists('essb_is_mobile')) {
	function essb_is_mobile() {
		return essb_manager()->isMobile();
	}
}

if (!function_exists('essb_is_plugin_activated_on')) {
	function essb_is_plugin_activated_on() {
		global $essb_options;
		
		if (is_admin()) {
			return;
		}
		
		//display_deactivate_on
		$is_activated = false;
		$display_include_on = ESSBOptionValuesHelper::options_value($essb_options, 'display_include_on');
		if ($display_include_on != "") {
			$excule_from = explode(',', $display_include_on);
	
			$excule_from = array_map('trim', $excule_from);
			if (in_array(get_the_ID(), $excule_from, false)) {
				$is_activated = true;
			}
		}
		return $is_activated;
	}
}