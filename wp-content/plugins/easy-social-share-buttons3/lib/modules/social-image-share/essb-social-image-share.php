<?php

class ESSBSocialImageShare {
	private static $instance = null;
	
	public static function get_instance() {
		
		if (null == self::$instance) {
			self::$instance = new self ();
		}
		
		return self::$instance;
	
	} // end get_instance;
	
	function __construct() {
		add_action ( 'wp_enqueue_scripts', array (&$this, 'enqueue_scripts' ) );
		add_action ( 'wp_footer', array (&$this, 'include_social_image_share' ) );
		add_action ( 'template_redirect', array ($this, 'essb_proccess_share_this_image' ), 1 );
	}
	
	function essb_proccess_share_this_image() {
		$current_action = isset($_REQUEST['essb-image-share']) ? $_REQUEST['essb-image-share'] : '';
		
		if ($current_action == "yes") {
			define('DOING_AJAX', true);
			send_nosniff_header();
			header('Pragma: no-cache');
				
			include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/social-image-share/essb-social-image-share-selected.php');
				
			exit;
		}
	}
	
	function enqueue_scripts() {
		global $essb_options;
		
		$mobile_detect = new ESSB_Mobile_Detect ();
		
		if ($mobile_detect->isMobile () && ! ESSBOptionValuesHelper::options_bool_value ( $essb_options, 'sis_on_mobile' )) {
			return false;
		}
		
		if (ESSBCoreHelper::is_plugin_deactivated_on() || ESSBCoreHelper::is_module_deactivate_on('sis')) {
			return;
		}
				
		essb_resource_builder()->add_static_resource_footer(ESSB3_PLUGIN_URL . '/lib/modules/social-image-share/assets/css/easy-social-image-share2.min.css', 'essb-social-image-share', 'css');
		wp_enqueue_script ( 'jquery' );
		wp_enqueue_script ( 'essb-social-image-share', ESSB3_PLUGIN_URL . '/lib/modules/social-image-share/assets/js/easy-social-image-share2.min.js', array ('jquery' ), false, true );
	}
	
	function include_social_image_share() {
		global $essb_options;
		
		if (ESSBCoreHelper::is_plugin_deactivated_on() || ESSBCoreHelper::is_module_deactivate_on('sis')) {
			return;
		}
		
		
		$mobile_detect = new ESSB_Mobile_Detect ();
		
		if ($mobile_detect->isMobile () && ! ESSBOptionValuesHelper::options_bool_value ( $essb_options, 'sis_on_mobile' )) {
			return false;
		}
		
		$current_post_address = ESSBUrlHelper::get_current_page_url();
		$current_post_address = ESSBUrlHelper::attach_tracking_code($current_post_address, 'essb-image-share=yes');

		$calling = 'jQuery(document).ready(function(){jQuery("'.$this->get_settings('sis_selector', 'img').'").essbis({selector:"'.$this->get_settings('sis_selector', 'img').'",dontshow:"'.$this->get_settings('sis_dontshow').'",minWidth:'.$this->get_settings('sis_minWidth', '100').',minHeight:'.$this->get_settings('sis_minHeight', '100').',align:{x:"'.$this->get_settings('sis_align_x', 'left').'",y:"'.$this->get_settings('sis_align_y', 'top').'"},offset:{x:'.$this->get_settings('sis_offset_x', '0').',y:'.$this->get_settings('sis_offset_y', '0').'},orientation:"'.$this->get_settings('sis_orientation').'",style:"'.$this->get_settings('sis_style').'",sharer:"'.(( $this->get_settings('sis_sharer') == 'true' ) ? $current_post_address : '').'",is_mobile:'.($mobile_detect->isMobile () ? 'true' : 'false').',always_show:'.$this->get_settings('sis_always_show', 'false').',pinterest_alt:'.$this->get_settings('sis_pinterest_alt', 'false').',primary_menu: [ '.$this->get_primary_menu().'],avoid_class: "'.$this->get_settings('sis_dontaddclass').'"});});';
		essb_resource_builder()->add_js($calling, true, 'essb-onmedia-code');
	
	}
	
	function get_primary_menu() {
		global $essb_options;
		
		$sis_networks = ESSBOptionValuesHelper::options_value($essb_options, 'sis_networks');
		$sis_network_order = ESSBOptionValuesHelper::options_value($essb_options, 'sis_network_order');
				
		$result_list = "";
		
		foreach ($sis_network_order as $network) {
			if (is_array($sis_networks)) {
				if (in_array($network, $sis_networks)) {
					if ($result_list != '') {
						$result_list .= ',';
					}
					
					$result_list .= "'".$network."'";
				}
			}
		}
			
		
		return $result_list;
	}
	
	function get_settings($option_name, $default_value = '', $boolean = false) {
		global $essb_options;

		
		$value = ESSBOptionValuesHelper::options_value($essb_options, $option_name);
		if (trim($value) == '') {
			$value = $default_value;
		}
		
		// @new image share support only top position @since version 3.2.6
		if ($option_name == "sis_align_y") { $value = "top";}
		
		return $value;
	}
}

?>