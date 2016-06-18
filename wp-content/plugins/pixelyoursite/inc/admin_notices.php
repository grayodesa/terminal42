<?php
function woofp_admin_notices($return=false){


$activation_time = get_option('pysf_activation_date', '');
$version = get_option('pysf_plugin_version', '');


$current_time = time();

if( empty($activation_time) || version_compare($version, FBPMP_VERSION, '<') ){
	$activation_time = $current_time;
	update_option( 'pysf_activation_date', $activation_time);
	update_option( 'pysf_plugin_version', FBPMP_VERSION);
	update_option( 'pysf_notice_dismiss', '');
	update_option( 'woo_pysf_notice_dismiss', '');
}


//Expiration time ( 3 days ) = ( number of days * hours * minutes * seconds )
$expiration_days = 3;
$expiration_time = $activation_time + ( $expiration_days * 24 * 60 * 60);

//calculate number of days passed since activation;
$days_passed = woofp_days_passed($current_time, $activation_time);

//date of expiration
$expiration_date = date('l jS \of F Y', $expiration_time);

$link_1 = 'http://www.pixelyoursite.com/limited-offer?utm_source=wadmin&utm_medium=wordpress&utm_campaign=limited-offer';
$link_2 = 'http://www.pixelyoursite.com/super-offer?utm_source=wadmin&utm_medium=wordpress&utm_campaign=super-offer';



//Message for day 1
$day_1_text  = '<span class="pysf_note"><b>Limited Offer, ends on “'.$expiration_date.'”:</b></span> If you are not using Dynamic Events - ';
$day_1_text .= '<a href="http://www.pixelyoursite.com/facebook-pixel-dynamic-events" target="_blank">see guide</a>';
$day_1_text .= ' - you are leaving money on the table. Optimize your ads for actions on your website, create better Custom Audiences, and get better conversion reports. ';
$day_1_text .= '<a href="'.$link_1.'" target="_blank">Enable Dynamic Events with PixelYourSite PRO</a>';

//Message for day 2
$day_2_text = '<span class="pysf_note"><b>Limited Offer, ending in 1 day:</b></span> Enable Dynamic Events and optimize your campaigns for site actions (newsletter sign up, contact forms, pop-ups). ';
$day_2_text .= '<a href="'.$link_1.'" target="_blank">Download now for a big discount</a>';

//Message for day 3
$day_3_text  = '<span class="pysf_note"><b>Limited Offer Ends Today, “'.$expiration_date.'”:</b></span> Last chance to get your big discount for PixelYourSite PRO and start to use <b>Dynamic Events.</b> ';
$day_3_text .= 'Don’t lose this opportunity, because you might need the plugin later: <a href="'.$link_1.'" target="_blank">Click here for your big discount</a>';

//Message for day 4 to 7;
$day_4_to_7_text = '';

//Message for day 7 to 12
$day_7_to_12_text = '<span class="pysf_note"><b>Special Offer:</b></span> Update to PixelYourSite PRO and enable Facebook Dynamic Events: optimize your ads for website actions, create better Custom Audiences, get better conversion reports. <a href="'.$link_2.'">Click here for your discount</a>';


$options = array(


		//message for day 1
		array(

			'day' 			=> 1,
			'message' 		=> $day_1_text, 
			'visibility' 	=> 'visible', /* visible OR hidden */

			),

		//message for day 2
		array(

			'day' 			=> 2,
			'message' 		=> $day_2_text, 
			'visibility' 	=> 'visible', /* visible OR hidden */
 

			),

		//message for day 3
		array(

			'day' 			=> 3,
			'message' 		=> $day_3_text, 
			'visibility' 	=> 'visible', /* visible OR hidden */
 

			),


		//message for day 4-7
		//This will start on day 4
		//and end on day 7
		array(

			'day' 			=> '4-7',
			'message' 		=> $day_4_to_7_text, 
			'visibility' 	=> 'hidden', /* visible OR hidden */
 

		),

		//message for day 7-12
		//This will start on day 7
		//and end on day 12
		array(

			'day' 			=> '7-12',
			'message' 		=> $day_7_to_12_text, 
			'visibility' 	=> 'visible', /* visible OR hidden */


		),


);


//Messages when WooCommerce is installed and activated

//WooCommerce Message for day 1
$woo_day_1_text  = 'WooCommerce Day 1 Message';
$woo_day_1_text  = '<span class="pysf_note"><b>Limited Offer, ends on “'.$expiration_date.'”:</b></span> Track Facebook Ads conversion value and enable Facebook Dynamic Ads for WooCommerce: ';
$woo_day_1_text .= '<a href="'.$link_1.'" target="_blank">Download PixelYourSite PRO for a big discount</a>';


//WooCommerce Message for day 2
$woo_day_2_text = 'WooCommerce Day 2 Message.';
$woo_day_2_text = '<span class="pysf_note"><b>Limited Offer, ending in 1 day:</b></span> Enable Facebook Dynamic Ads for WooCommerce and automatically retarget your visitors. ';
$woo_day_2_text .= '<a href="'.$link_1.'" target="_blank">Update now for a big discount</a>';

//WooCommerce Message for day 3
$woo_day_3_text  = 'WooCommerce Day 3 Message.';
$woo_day_3_text  = '<span class="pysf_note"><b>Limited Offer Ends Today, “'.$expiration_date.'”:</b></span> With PixelYourSite PRO you can enable WooCommerce Dynamic Ads and you can use Dynamic Events for your campaings. ';
$woo_day_3_text .= 'Don’t lose this opportunity, because you might need the plugin later: <a href="'.$link_1.'" target="_blank">Click here for your big discount</a>';


//WooCommerce Message for day 4 to 7;
$woo_day_4_to_7_text = 'WooCommerce Day 4 to 7 Message.';

//WooCommerce Message for day 7 to 12
$woo_day_7_to_12_text = 'WooCommerce Day 7 to 12 Message.';
$woo_day_7_to_12_text = '<span class="pysf_note"><b>Special Offer:</b></span> Update to PixelYourSite PRO and enable Facebook Dynamic Events: optimize your ads for website actions, create better Custom Audiences, get better conversion reports. WooCommerce Facebook Dynamic Ads fully suported. <a href="'.$link_2.'">Click here for your discount</a>';



$woo_options = array(


		//woocommerce message for day 1
		array(

			'day' 			=> 1,
			'message' 		=> $woo_day_1_text, 
			'visibility' 	=> 'visible', /* visible OR hidden */

			),

		//woocommerce message for day 2
		array(

			'day' 			=> 2,
			'message' 		=> $woo_day_2_text, 
			'visibility' 	=> 'visible', /* visible OR hidden */
 

			),

		//woocommerce message for day 3
		array(

			'day' 			=> 3,
			'message' 		=> $woo_day_3_text, 
			'visibility' 	=> 'visible', /* visible OR hidden */
 

			),


		//woocommerce message for day 4-7
		//This will start on day 4
		//and end on day 7
		array(

			'day' 			=> '4-7',
			'message' 		=> $woo_day_4_to_7_text, 
			'visibility' 	=> 'hidden', /* visible OR hidden */
 

		),

		//woocommerce message for day 7-12
		//This will start on day 7
		//and end on day 12
		array(

			'day' 			=> '7-12',
			'message' 		=> $woo_day_7_to_12_text, 
			'visibility' 	=> 'visible', /* visible OR hidden */


		),


);




	
	//we will store the message of each day
	$notice_message = '';


	if ( class_exists( 'WooCommerce' ) ) {
  		//WooCommerce is installed

		//Get dismissed notices
	   	$dismiss_option = get_option( 'woo_pysf_notice_dismiss', '');
		$options = $woo_options;

	} else {
	  	//No WooCommerce
	  
	  	//Get dismissed notices
	  	$dismiss_option = get_option( 'pysf_notice_dismiss', '');
			
	}

	
	//loop through notice settings
	foreach ($options as $key => $option) {
		
		$is_dismissed 	= isset( $dismiss_option[$option['day']] ) ? true : false;

		if( is_integer( $option['day']) ){

			//check if there is a message for a day and that it is on
			if( !$is_dismissed && $option['day'] == $days_passed && $option['visibility'] == 'visible' ){
				
				$dismiss_option = $option['day'];
				$notice_message = $option['message'];
				break;
			}

		} else {

			//check range of days

			$pieces = explode('-', $option['day']);
			$start 	= $pieces[0];
			$end 	= $pieces[1];

			//check if there is a message for a day and that it is on
			if( !$is_dismissed && ( $days_passed >= $start && $days_passed  <= $end) && $option['visibility'] == 'visible' ){
				
				$dismiss_option = $option['day'];
				$notice_message = $option['message'];
				break;
			}


		}
	}


	if( !empty( $notice_message  ) ){

		$notice = '<style type="text/css">
			.pysf_note{ color: #dd4e4e; }
			#pysf_notice {
			  border-left-width: 10px;
			  display: block;
			  margin: 25px 20px 10px 2px;
			  padding: 4px 10px;
			}
			#pysf_notice p{ width:98%;}
			.pysf_clear{ clear:both;}
		</style>';
		
		$html_class = '';
		$html_id = 'pysf_notice_tab';
		$button_html = '<button type="button" class="notice-dismiss" title="Dismiss Notice"><span class="screen-reader-text">Dismiss this notice.</span></button>';
		if( !$return){
			$html_class = 'update-nag notice is-dismissible';
			$html_id = 'pysf_notice';
			$button_html = '';
		}

		$notice .=  '<div id="'.$html_id.'" class="'.$html_class.' pysf_clear pysf_notice pysf_notice_day_'.$dismiss_option.'" data-pysf="pysf_notice_day_'.$dismiss_option.'">';
		$notice .= '<p>'.$notice_message.'</p>';
		$notice .= $button_html;
		$notice .= '</div>';

		$notice .= "<script type='text/javascript'>
			jQuery(function($){

				jQuery(document).on( 'click', '.pysf_notice .notice-dismiss', function() {
					
					jQuery(this).closest('.notice-dismiss').fadeOut('slow');

				    jQuery.ajax({
				        url: ajaxurl,
				        data: {
				            action: 'pysf_notice_dismiss',
				            option: '".$dismiss_option."'
				        }
				    });

				});

			});
		</script>";
		
	if( !$return)	
		echo $notice;
	else
		return $notice;
	}

}



function woofp_admin_notices_action(){
	woofp_admin_notices();
}
if( isset($_GET['page']) && $_GET['page'] == 'woo-facebookpixel' ){

} else {
	add_action( 'admin_notices', 'woofp_admin_notices_action' );
}


//add jquery if not already included;
function woofp_admin_notices_script(){

	wp_enqueue_script('jquery');

}
add_action('wp_enqueue_scripts', 'woofp_admin_notices_script');


function ajax_pysf_notice_dismiss(){

	if ( class_exists( 'WooCommerce' ) ) {
  		$name  = 'woo_pysf_notice_dismiss';
  	} else {
  		$name = 'pysf_notice_dismiss';
  	}

	if( isset($_REQUEST['option'])){
		$dismiss_option = get_option( $name, '');
		$dismiss_option[$_REQUEST['option']] = 1;
		update_option( $name, $dismiss_option); 	
	}

	die();
}
add_action('wp_ajax_pysf_notice_dismiss', 'ajax_pysf_notice_dismiss');

//save activated time on plugin activation
function pysf_plugin_activated(){

	$activation_date = get_option('pysf_activation_date', '');
	$version = get_option('pysf_plugin_version', '');

	if( empty($activation_date) || version_compare($version, FBPMP_VERSION, '<') ){
		update_option( 'pysf_activation_date', time());
		update_option( 'pysf_plugin_version', FBPMP_VERSION);
		update_option( 'pysf_notice_dismiss', '');
		update_option( 'woo_pysf_notice_dismiss', '');
	}
}
register_activation_hook(__FILE__, 'pysf_plugin_activated');



//get number of days passed since activation
function woofp_days_passed($current_time, $activation_time){

	$timepassed = $current_time-$activation_time;
	$timepassed = ( ( ( $timepassed/24 )/60 )/60 );
	$dayspassed = floor($timepassed); 
	$dayspassed = $dayspassed + 1;

	return $dayspassed;

}