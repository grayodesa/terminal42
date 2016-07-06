<?php
class ESSBAdminBarMenu3 {
	function __construct() {
		add_action ( 'admin_bar_menu', array ($this, "attach_admin_barmenu" ), 89 );
	}
	
	public function attach_admin_barmenu() {
		global $post;
		
		$url = '';
		if (isset ( $post )) {
			$url = get_permalink ( $post->ID );
		} else {
			$url = get_bloginfo ( 'url' );
		}
		
		// https://developers.facebook.com/tools/debug/og/object?q='.$url
		
		$this->add_root_menu ( "Easy Social Share Buttons", "essb", get_admin_url () . 'admin.php?page=essb_options' );
		$this->add_sub_menu ( "ESSB Settings", get_admin_url () . 'admin.php?page=essb_options', "essb", "essb_p1" );
		$this->add_sub_menu ( "Social Settings", get_admin_url () . 'admin.php?page=essb_options', "essb_p1", "essb_p11" );
		if (!defined('ESSB3_LIGHTMODE')) {
			$this->add_sub_menu ( "Display Settings", get_admin_url () . 'admin.php?page=essb_redirect_display', "essb_p1", "essb_p21" );
		}
		$this->add_sub_menu ( "Advanced Settings", get_admin_url () . 'admin.php?page=essb_redirect_advanced', "essb_p1", "essb_p51" );
		$this->add_sub_menu ( "Style Settings", get_admin_url () . 'admin.php?page=essb_redirect_style', "essb_p1", "essb_p41" );
		$this->add_sub_menu ( "Shortcode Generator", get_admin_url () . 'admin.php?page=essb_redirect_shortcode&tab=shortcode', "essb", "essb_p3" );
		$this->add_sub_menu ( "[easy-social-share]", get_admin_url () . 'admin.php?page=essb_redirect_shortcode&tab=shortcode&code=easy-social-share', "essb_p3", "essb_p31" );
		$this->add_sub_menu ( "[easy-social-like]", get_admin_url () . 'admin.php?page=essb_redirect_shortcode&tab=shortcode&code=easy-social-like', "essb_p3", "essb_p32" );
		$this->add_sub_menu ( "[easy-total-shares]", get_admin_url () . 'admin.php?page=essb_redirect_shortcode&tab=shortcode&code=easy-total-shares', "essb_p3", "essb_p33" );
		$this->add_sub_menu ( "All available shortcodes", get_admin_url () . 'admin.php?page=essb_redirect_shortcode&tab=shortcode', "essb_p3", "essb_p34" );
		$this->add_sub_menu ( "Validation Tools", '', "essb", "essb_v" );
		$this->add_sub_menu ( "Facebook Open Graph Debugger", 'https://developers.facebook.com/tools/debug/og/object?q=' . $url, "essb_v", "essb_v1" );
		$this->add_sub_menu ( "Twitter Card Validator", 'https://dev.twitter.com/docs/cards/validation/validator/?link=' . $url, "essb_v", "essb_v2" );
		$this->add_sub_menu ( "Google Rich Snippet Validator", 'http://www.google.com/webmasters/tools/richsnippets?q=' . $url, "essb_v", "essb_v3" );
		
		if (defined ( 'ESSB3_CACHE_ACTIVE' )) {
			$this->add_sub_menu ( "<b>Purge ESSB Cache</b>", get_admin_url () . 'admin.php?page=essb_redirect_advanced&tab=advanced&purge-cache=true', "essb", "essb_p7" );
		}
		
		if (defined('ESSB3_CACHED_COUNTERS')) {
			if (is_single () || is_page ()) {
				$this->add_sub_menu ( "<b>Update ESSB Cached Counters</b>", $url . '?essb_counter_update=true', "essb", "essb_p8" );				
			}
		}
		
		$this->add_sub_menu ( "Need Help?", 'http://support.creoworx.com/', "essb", "essb_p6" );
		
		if (ESSB3_ADDONS_ACTIVE) {
			$this->add_sub_menu ( '<span style="color:#f39c12;">Extensions</span>', get_admin_url () . 'admin.php?page=essb_addons', "essb", "essb_p7" );
				
		}
	}
	
	function add_root_menu($name, $id, $href = FALSE) {
		global $wp_admin_bar;
		if (! is_super_admin () || ! is_admin_bar_showing ())
			return;
		
		$wp_admin_bar->add_menu ( array ('id' => $id, 'meta' => array (), 'title' => $name, 'href' => $href ) );
	}
	
	function add_sub_menu($name, $link, $root_menu, $id, $meta = FALSE) {
		global $wp_admin_bar;
		if (! is_super_admin () || ! is_admin_bar_showing ())
			return;
		
		$wp_admin_bar->add_menu ( array ('parent' => $root_menu, 'id' => $id, 'title' => $name, 'href' => $link, 'meta' => $meta ) );
	}

}
