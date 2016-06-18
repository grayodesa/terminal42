<?php
/*
Plugin Name: PixelYourSite
Description: Add the Facebook Pixel code into your Wordpress site and set up standard events with just a few clicks. Fully compatible with Woocommerce, purchase event included.
Plugin URI: http://www.pixelyoursite.com/facebook-pixel-plugin-help
Author: PixelYourSite
Author URI: http://www.pixelyoursite.com
Version: 2.2.7
License: GPLv3
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*
 * Constants
*/
//Plugin Version
define( 'FBPMP_VERSION', '2.2');



require_once('inc/helper-functions.php');
require_once('inc/admin_notices.php');

add_action('init', 'woofp_init');


/* Register script for front end */
add_action('wp_enqueue_scripts', 'tkwoofp_publicscripts');


/* Register Admin Page for plugin */
add_action('admin_menu', 'woofp_adminmenu');

/* Register scripts for admin Settings */
add_action('admin_enqueue_scripts', 'woofp_adminscripts');

//Ajax Save Admin Settings
/*add_action('wp_ajax_nopriv_woofbsavesettings' , 'ajax_woofbsavesettings');*/
add_action('wp_ajax_woofbsavesettings' , 'ajax_woofbsavesettings');

add_action('wp_nopriv_fbpmpaddtocart' , 'ajax_fbpmpaddtocart');
add_action('wp_ajax_fbpmpaddtocart' , 'ajax_fbpmpaddtocart');


function woofp_init(){

if( !is_admin() ){		
	//Display Facebook Pixel Code
	add_action('login_enqueue_scripts', 'woofp_pixelcode', 1);
	add_action('wp_head', 'woofp_pixelcode', 1);
	

	//add addtocart ajax support
	//only if woocommerce installed
	if( woofp_is_woocommerce() ){
		add_action('wp_footer', 'woofp_addtocart_pixel');
	}
}

}

function ajax_fbpmpaddtocart(){

	if( wp_verify_nonce( $_POST['nonce'], 'fbpmp_nonce_ajaxaddtocart' ) ){

		$event_code = "fbq('track', 'AddToCart');";
		
		$results = array( 'status' => 1, 'event_code' => $event_code);

	} else {

		$results = array('status' => 0 );
	}

	die( json_encode( $results ) );
}


/* Plugin Front End Scripts */
function tkwoofp_publicscripts(){

	wp_enqueue_script( 'woo-facebookpixel-script', plugins_url( 'js/public.js', __FILE__ ), array('jquery'), '1.0');
	wp_enqueue_script('jquery');
}

/* Register Plugin Admin Menu*/
function woofp_adminmenu(){

	add_menu_page( 'PixelYourSite', 'PixelYourSite', 'manage_options', 'woo-facebookpixel', 'woofp_admin_page');
}

/* Admin page content */
function woofp_admin_page(){
	
	$facebookpixel 			= woofp_admin_settings('facebookpixel');
	$standardevent 			= woofp_admin_settings('standardevent');
	$woocommerce_settings   = woofp_admin_settings('woocommerce');


	include('inc/admin.php');

}

/* admin css and js */
function woofp_adminscripts(){

	/* include only if plugin admin page */
	if(isset($_GET['page']) && $_GET['page'] == 'woo-facebookpixel' ){

		wp_enqueue_style( 'woo-facebookpixel-style', plugins_url( 'css/admin.css', __FILE__ ));
		wp_enqueue_script( 'woo-facebookpixel-script', plugins_url( 'js/admin.js', __FILE__ ), array('jquery'), '1.0');
		wp_localize_script( 'woo-facebookpixel-script', 'woofp', array( 'ajaxurl'=> admin_url('admin-ajax.php'), 'loading' => admin_url('images/loading.gif') ) );
	}
}


/**
 * Save Admin Settings
 *
 * @return array
 * @author PixelYourSite
 **/
function ajax_woofbsavesettings(){

		$woo = 0;
	if( woofp_is_woocommerce() ){
		$woo = 1;
	}

	//if ajax is valid
	if( wp_verify_nonce( $_POST['woofpnonce'], 'woofp-nonce-action' ) ){

		$facebookpixel = isset($_POST['facebookpixel']) ? $_POST['facebookpixel'] : '';
		$standardevent = isset($_POST['standardevent']) ? $_POST['standardevent'] : '';
		$woocommerce   = isset($_POST['woocommerce']) ? $_POST['woocommerce'] : '';

		//$woofp_options = get_option('woofp_admin_settings');
		$woofp_options_update = array(

									'facebookpixel' => $facebookpixel, 
									'standardevent' => $standardevent,
									'woocommerce' 	=> $woocommerce

									);

		//Save admin settings
		update_option('woofp_admin_settings', $woofp_options_update);
		


		$status = array( 'msg' => 'Settings Saved.', 'status' => 2 , 'woo' => $woo);

	} else {

		$status = array( 'msg' => 'cheating huh!', 'status' => 1, 'woo' => $woo);
	
	}


	//exit ajax
	die(json_encode($status));

}


//When The p
function woofp_plugin_activated(){


	if ( !function_exists( 'is_plugin_active' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		if ( is_plugin_active( 'pixelyoursite-pro/pixelyoursite-pro.php' ) ) {
			wp_die( 'Please deactivate PixelYourSite Pro Version First.', 'Plugin Activation');
		}

}

register_activation_hook( __FILE__, 'woofp_plugin_activated');