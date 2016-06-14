<?php
/**
 * Get Admin Settings
 *
 * @return array
 * @author PixelYourSite 
 **/
function woofp_admin_settings($option_name='all'){


	$settings 			= get_option('woofp_admin_settings');
	
	if(empty($settings)) {

		$settings = array();
	
	} else {
	
		$options = array('facebookpixel', 'standardevent', 'woocommerce');
		foreach ( $options as $key => $name ) {
			
			if( !isset( $settings[$name]) || empty( $settings[$name] ) ){

				$settings[$name] = array();
			}
		}
	}


	switch ($option_name) {
		case 'all':
			
			return $settings;
			
			break;

		case 'facebookpixel':
			
			return ( isset($settings['facebookpixel']) ? $settings['facebookpixel'] : '' );
			
			break;
		
		case 'standardevent':
			
			return ( isset($settings['standardevent']) ? $settings['standardevent'] : '' );
			
			
			break;

		case 'woocommerce':
				
			return ( isset($settings['woocommerce']) ? $settings['woocommerce'] : '' );
			
			
			break;	
		
		default:

			return $settings;
			
			break;
	}
}

/**
 * Get Saved Option Value
 *
 * @return array|string
 * @author PixelYourSite 
 **/
function woofp_get_option($name='', $option=''){

		if(empty($name) || empty($option))
			return '';

		$settings = woofp_admin_settings();

		if( isset( $settings[$name][$option] ) )
			return  $settings[$name][$option];
		else 
			return '';
}

/**
 * Current Page Full URL
 *
 * @return string
 * @author PixelYourSite 
 **/
function woofp_current_url(){

	$current_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$current_url = rtrim($current_url, '/');

	return $current_url;
}

/**
 * Return Javascript Code for facebook standard event 
 * ( With fallback for No Javascript )
 * 
 * @return array
 * @author PixelYourSite 
 **/
function woofp_page_event(){

	$facebookpixel 			= woofp_admin_settings('facebookpixel');
	$standardevent 			= woofp_admin_settings('standardevent');
	$woocommerce_settings   = woofp_admin_settings('woocommerce');

	
	$pixelcode = "";

	/* whether facebook pixel is activated */
	if( isset($facebookpixel['activate']) && $facebookpixel['activate'] == 1 ){

	$pixel_id =  isset($facebookpixel['ID']) ? $facebookpixel['ID'] : '';
	$current_url = woofp_current_url();
	$event_added = array();

	
	$nojs_pixelcode = "";

	$pixelcode .= "\n";
	$nojs_pixelcode .= "\n";


	$pixelcode .= "// Insert Your Facebook Pixel ID below. \n fbq('init', '".$pixel_id."'); \n\n";

	$eventtype = 'PageView';
	
	$pixelcode .= "//Default Event \n" . woofp_get_event( $eventtype ) . " \n\n";
	$nojs_pixelcode .= "<!-- Default Event --> \n" . woofp_get_event_noscript( $pixel_id, $eventtype ) . " \n\n";
	$event_added[] = array( $current_url, $eventtype );

	
	/* whether standerd events is activated */
	if( isset($standardevent['activate']) && $standardevent['activate'] == 1 ){

	


	if( isset($standardevent['pageurl']) && !empty( $standardevent['pageurl'] ) ) {

		foreach ( $standardevent['pageurl'] as $key => $link) {

			//if url or event is empty don't go further
			if( empty($standardevent['pageurl'][$key]) || empty($standardevent['eventtype'][$key]) )
				continue;

			$pageurl 	= rtrim($standardevent['pageurl'][$key], '/');
			$cartvalue 	= $standardevent['value'][$key];
			$currency 	= $standardevent['currency'][$key];
			$eventtype  = $standardevent['eventtype'][$key];
			
			

			
			

			//Add pixel event for added url
			if( !woofp_is_eventadded( $event_added, $eventtype ) && woofp_match_url($current_url, $pageurl, $eventtype) == 'true' ){
				$event_added[] = array($current_url, $eventtype );
				
				
				$pixelcode .= "//Standard Event\n" . woofp_get_event( $eventtype, $cartvalue, $currency, 'standardevent') . " \n\n";
				$nojs_pixelcode .= "<!-- Standard Event --> \n" . woofp_get_event_noscript( $pixel_id, $eventtype, 'standardevent') . " \n\n";

			}
			
		} /* foreach standardevent['pageurl'] */
	
	} /* not empty $standardevent['pageurl']  */

		
	} /* standerd event activated */

		/* Woocommerce settings */
		if( isset($woocommerce_settings['activate']) && $woocommerce_settings['activate'] == 1 && woofp_is_woocommerce() ){
			
			global $woocommerce;

			$cartvalue = $woocommerce->cart->get_cart_total();
			$currency = get_woocommerce_currency();

			$symbol = get_woocommerce_currency_symbol();
			$cartvalue = str_replace($symbol, '', $cartvalue);


			$cartvalue = strip_tags($cartvalue);
			$cartvalue = preg_replace("/[^0-9\.]/", "", $cartvalue);

			//Add pixel event for woocommerce pages
			foreach (woofp_woocommerce_events() as $key => $woocommerc_event) {
						
						$eventtype = $woocommerc_event['event'];

						if( 
							woofp_is_woocommerce_page($woocommerc_event['page'])
						&& !woofp_is_eventadded( $event_added, $woocommerc_event['event'] ) 
						&& woofp_is_woocommerce_event($woocommerc_event['event'], $woocommerce_settings, false)
						)
							{

					
							$event_added[] = array( $current_url, $eventtype );

							$pixelcode .= "//WooCommerce Event\n" . woofp_get_event( $eventtype, $cartvalue, $currency, 'woocommerce') . " \n\n";
							$nojs_pixelcode .= "<!-- WooCommerce Event --> \n" . woofp_get_event_noscript( $pixel_id, $eventtype, 'woocommerce') . " \n\n";

							}


			} /* foreach woofp_woocommerce_events*/
	
	} /* Woocommerce Settings */



		//Search Page
		if( isset($_GET['s']) && $_GET['s'] !='' ){

			$eventtype = 'Search';
		
			if( !woofp_is_eventadded( $event_added, $eventtype ) ) {
				$pixelcode .= "//Default Event \n" . woofp_get_event( $eventtype, 'default') . " \n";
				$nojs_pixelcode .= "<!-- Default Event--> \n" . woofp_get_event_noscript( $pixel_id, $eventtype, 'default') . " \n\n";
				$event_added[] = array( $current_url, $eventtype );
			}	
		}

		//registered page
		if( isset($_GET['checkemail']) && $_GET['checkemail'] =='registered' ){

				$eventtype = 'CompleteRegistration';
				if( !woofp_is_eventadded( $event_added, $eventtype ) ) {		
				$pixelcode .= "//Default Event \n" . woofp_get_event( $eventtype, 'default') . " \n\n";
				$nojs_pixelcode .= "<!-- Default Event--> \n" . woofp_get_event_noscript( $pixel_id, $eventtype, 'default') . " \n\n";
				$event_added[] = array( $current_url, $eventtype );
			}
		}

	
	}/* facebook pixel activated */

	$pixelcode .= "\n";
	$nojs_pixelcode .= "\n";

	return array($pixelcode, $nojs_pixelcode);
}



/* Todo Prevent Duplicate Event*/
function woofp_is_eventadded($addedevents, $event){

	return false;
	
	if(!empty($addedevents)){

		$result = false;
		$current_url = woofp_current_url();


		foreach ($addedevents as $key => $addedevent) {
			
			if( woofp_match_url($current_url, $addedevent[0]) == 'true' && $event == $addedevent[1] ){

				$result = true;
			}
		}	

	} else {

		$result = false;
	}

	return $result;
}

function woofp_get_event($event='', $value=false, $currency='USD', $type=''){

		if( 'ProductAddToCart' == $event )
			$event = 'AddToCart';

		if( 'Purchase' == $event ){

			if(  $type == 'standardevent' && $value != false && is_numeric( $value )  )	
				return "fbq('track', '".$event."', {currency:'".$currency."', value:".$value."});";
			else	
				return "fbq('track', '".$event."', {currency:'USD', value:0.00});";
			
		} else {

			if( $type == 'standardevent' && $value != false && is_numeric( $value ) )
				return "fbq('track', '".$event."', {currency:'".$currency."', value:".$value."});";
			else
				return "fbq('track', '".$event."');";

		}
}

function woofp_get_event_noscript($pixel_id, $event, $type=''){

	return "<noscript><img height='1' width='1' style='display:none'
src='https://www.facebook.com/tr?id=".$pixel_id."&ev=".$event."&noscript=1'
/></noscript>";

}

function woofp_event_types($current=''){
?>

	<option <?php echo selected( 'ViewContent', $current, true ); ?> value="ViewContent">ViewContent</option>
	<option <?php echo selected( 'Search', $current, true ); ?> value="Search">Search</option>
	<option <?php echo selected( 'AddToCart', $current, true ); ?> value="AddToCart">AddToCart</option>
	<option <?php echo selected( 'AddToWishlist', $current, true ); ?> value="AddToWishlist">AddToWishlist</option>
	<option <?php echo selected( 'InitiateCheckout', $current, true ); ?> value="InitiateCheckout">InitiateCheckout</option>
	<option <?php echo selected( 'AddPaymentInfo', $current, true ); ?> value="AddPaymentInfo">AddPaymentInfo</option>
	<option <?php echo selected( 'Purchase', $current, true ); ?> value="Purchase">Purchase</option>
	<option <?php echo selected( 'Lead', $current, true ); ?> value="Lead">Lead</option>
	<option <?php echo selected( 'CompleteRegistration', $current, true ); ?> value="CompleteRegistration">CompleteRegistration</option>

<?php
}

function woofp_event_help(){
?>
<ul class="woofp-event-help">
	<li><b>ViewContent:</b>	Track key page views (ex: product page, landing page or article)</li>
	<li><b>Search:</b>	Track searches on your website (ex. product searches)</li>	
	<li><b>AddToCart:</b>	Track when items are added to a shopping cart (ex. click/landing page on Add to Cart button)</li>
	<li><b>AddToWishlist:</b>	Track when items are added to a wishlist (ex. click/landing page on Add to Wishlist button)</li>
	<li><b>InitiateCheckout:</b>	Track when people enter the checkout flow (ex. click/landing page on checkout button)</li>
	<li><b>AddPaymentInfo:</b>	Track when payment information is added in the checkout flow (ex. click/landing page on billing info)</li>	
	<li><b>Purchase:</b>	Track purchases or checkout flow completions (ex. landing on "Thank You" or confirmation page)</li>
	<li><b>Lead:</b>	Track when a user expresses interest in your offering (ex. form submission, sign up for trial, landing on pricing page)</li>	
	<li><b>CompleteRegistration:</b>	Track when a registration form is completed (ex. complete subscription, sign up for a service)</li>
</ul>
<?php
}



function woofp_currency_options( $current='USD' ){
?>
	<option <?php echo selected( 'AUD', $current, true ); ?> value="AUD">Australian Dollar</option>
	<option <?php echo selected( 'BRL', $current, true ); ?> value="BRL">Brazilian Real </option>
	<option <?php echo selected( 'CAD', $current, true ); ?> value="CAD">Canadian Dollar</option>
	<option <?php echo selected( 'CZK', $current, true ); ?> value="CZK">Czech Koruna</option>
	<option <?php echo selected( 'DKK', $current, true ); ?> value="DKK">Danish Krone</option>
	<option <?php echo selected( 'EUR', $current, true ); ?> value="EUR">Euro</option>
	<option <?php echo selected( 'HKD', $current, true ); ?> value="HKD">Hong Kong Dollar</option>
	<option <?php echo selected( 'HUF', $current, true ); ?> value="HUF">Hungarian Forint </option>
	<option <?php echo selected( 'ILS', $current, true ); ?> value="ILS">Israeli New Sheqel</option>
	<option <?php echo selected( 'JPY', $current, true ); ?> value="JPY">Japanese Yen</option>
	<option <?php echo selected( 'MYR', $current, true ); ?> value="MYR">Malaysian Ringgit</option>
	<option <?php echo selected( 'MXN', $current, true ); ?> value="MXN">Mexican Peso</option>
	<option <?php echo selected( 'NOK', $current, true ); ?> value="NOK">Norwegian Krone</option>
	<option <?php echo selected( 'NZD', $current, true ); ?> value="NZD">New Zealand Dollar</option>
	<option <?php echo selected( 'PHP', $current, true ); ?> value="PHP">Philippine Peso</option>
	<option <?php echo selected( 'PLN', $current, true ); ?> value="PLN">Polish Zloty</option>
	<option <?php echo selected( 'GBP', $current, true ); ?> value="GBP">Pound Sterling</option>
	<option <?php echo selected( 'SGD', $current, true ); ?> value="SGD">Singapore Dollar</option>
	<option <?php echo selected( 'SEK', $current, true ); ?> value="SEK">Swedish Krona</option>
	<option <?php echo selected( 'CHF', $current, true ); ?> value="CHF">Swiss Franc</option>
	<option <?php echo selected( 'TWD', $current, true ); ?> value="TWD">Taiwan New Dollar</option>
	<option <?php echo selected( 'THB', $current, true ); ?> value="THB">Thai Baht</option>
	<option <?php echo selected( 'TRY', $current, true ); ?> value="TRY">Turkish Lira</option>
	<option <?php echo selected( 'USD', $current, true ); ?> value="USD">U.S. Dollar</option>

<?php
}


function woofp_pixelcode(){

		//load facebook pixel on front only
if( !is_admin() ){

	$facebookpixel 			= woofp_admin_settings('facebookpixel');

if( isset($facebookpixel['activate']) && $facebookpixel['activate'] == 1 ) {
?>


<!-- Facebook Pixel Code -->
<script>
var PYS_DOMReady = function(a,b,c){b=document,c='addEventListener';b[c]?b[c]('DOMContentLoaded',a):window.attachEvent('onload',a)}
	!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
	n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
	n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
	t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
	document,'script','//connect.facebook.net/en_US/fbevents.js');

/* pixel plugin code */
PYS_DOMReady(function(){

<?php 
$facebookpixel_code = woofp_page_event(); 
echo $facebookpixel_code[0];
?>

});
/* pixel plugin code */

</script>

<!-- End Facebook Pixel Code -->

<?php
}

} else {
?>
	<img src="<?php echo admin_url('admin-ajax.php'); ?>" style="display:none;">
	
<?php
	}



}


/*
Woocommerce Related Functions
Check if woocommerce is install and activated.
*/

function woofp_addtocart_urls($woofp_options=''){

	
		$facebookpixel 			= $woofp_options['facebookpixel'];
		$standardevent 			= $woofp_options['standardevent'];
		$woocommerce_settings   = $woofp_options['woocommerce'];
	
		

	/* whether facebook pixel is activated */
	if( woofp_get_option('facebookpixel', 'activate') == 1 ){

	/* whether standerd events is activated */
	if( woofp_get_option('standardevent', 'activate') == 1 ){

	

		$results = array();
		if( isset( $standardevent['pageurl'] ) && !empty($standardevent['pageurl']) ){

			foreach ( $standardevent['pageurl'] as $key => $link) {

				$pageurl 	= rtrim($standardevent['pageurl'][$key], '/');
				$cartvalue 	= $standardevent['value'][$key];
				$currency 	= $standardevent['currency'][$key];
				$eventtype = $standardevent['eventtype'][$key];
				
				if( !in_array($pageurl, $results) && $eventtype == 'AddToCart' )
					$results[] = $pageurl;


			}

		}

		if( woofp_get_option('woocommerce', 'activate') == 1 ){
			
			//Add pixel event for woocommerce pages
			foreach (woofp_woocommerce_events() as $key => $woocommerc_event) {

					$eventtype = $woocommerc_event['event'];
					if( !in_array( '***', $results) && woofp_is_woocommerce_event($eventtype, $woocommerce_settings, false) && $eventtype == 'AddToCart' )
						$results[] = '***';
			}

		}

	}

	}


	return $results;
					
}


function woofp_addtocart_pixel(){


	//

	?>

	<!-- noscript facebook pixel -->
	
	<?php 
	$facebookpixel_code = woofp_page_event();
	echo $facebookpixel_code[1]; 
	?>

	<!-- end noscript facebook pixel -->

	<?php 
	//Run AddToCart Event when product are added using ajax
	//todo: woocommerce_cart_redirect_after_add
	
		$woofp_options 			= get_option('woofp_admin_settings');
		$facebookpixel 			= $woofp_options['facebookpixel'];
		$standardevent 			= $woofp_options['standardevent'];
		$woocommerce_settings   = $woofp_options['woocommerce'];



?>
<!-- Dynamic AddToCart when Ajax enabled on Woocommerce -->
<?php

if ( get_option( 'woocommerce_enable_ajax_add_to_cart' ) == 'yes' && ( is_woocommerce() || is_page() || is_single() ) ) { 

		$php_array = woofp_addtocart_urls($woofp_options);
		$js_array = json_encode($php_array);
?>


<script type="text/javascript">
var fbpmpaddtocart_eventcode = '';

jQuery(function($){

/*$.ajax({
	url: '<?php echo admin_url("admin-ajax.php"); ?>',
	type: 'POST',
	dataType: 'json',
	data: {action: 'fbpmpaddtocart', nonce:'<?php echo wp_create_nonce("fbpmp_nonce_ajaxaddtocart"); ?>'},
})
.done(function(result){
		
		if( 1 == result.status ){
			fbpmpaddtocart_eventcode = result.event_code;
		}
		console.log(result);
		console.log(fbpmpaddtocart_eventcode);

});*/

//Run AddToCart Event when product are added using ajax	
jQuery(document).on( 'click', '.add_to_cart_button', function(e) {
		
		e.preventDefault();
	
		var current_url = jQuery(this).attr('href') || '';
		var product_id = jQuery(this).attr('data-product_id') || 0;
		
		<?php echo "var url_array = ". $js_array . ";\n"; ?>
		<?php echo "var homeurl = '". home_url() . "';\n"; ?>

		if (current_url.indexOf('http://') < 0 || current_url.indexOf('https://') < 0){
			current_url = homeurl + current_url;
		}



			for (i = 0; i < url_array.length; i++) {
					
				if( url_array[i] == '***' ){

					
						fbq('track', 'AddToCart');

				} else if( current_url.search(url_array[i]) !== -1 ){

					
						fbq('track', 'AddToCart');					
					
				}

			}
		

});

});
</script>
<?php } 
}



function woofp_is_woocommerce_page ($page) {
    
	switch ($page) {

		case 'woocommerce_view_content':

				return is_product(); 

		case 'woocommerce_added_to_cart':
			
			if ( isset($_REQUEST['add-to-cart']) && is_numeric($_REQUEST['add-to-cart']) )
					return true;
				else 
					return false;


			break;


		case 'woocommerce_cart_page_id':
				
				return is_cart();

			break;

		case 'woocommerce_checkout_page_id':

				if( is_checkout() && !is_wc_endpoint_url() )
					return true;
				else
					return false;

			break;

		case 'woocommerce_thanks_page_id':
				
				if( is_wc_endpoint_url( 'order-received' ) )
					return true;
				else 
					return false;


			break;

		case 'woocommerce_search_product':
				
				if ( isset($_GET['s']) && ( isset($_GET['post_type']) && $_GET['post_type'] == 'product' ) ) {

                        return true ;

                } else {

                	return false;
                }

			break;
		
		default:
			return false;
		break;
	}

}


/**
 * Return appropriate pixel events for woocommerce pages
 *
 * @return array
 * @author PixelYourSite 
 **/
function woofp_woocommerce_events(){

	$woocommerce_events = array(

							array(
								'event' => 'ViewContent', 
								'page' => 'woocommerce_view_content',
								'title' => 'Any WooCommerce Page',
								'label' => 'ViewContent Event'
							),

							array(
								'event' => 'ProductAddToCart', 
								'page' => 'woocommerce_added_to_cart',
								'title' => 'Product Added to Cart',
								'label' => 'AddToCart Event'
							),

							array(
								'event' => 'AddToCart', 
								'page' => 'woocommerce_cart_page_id',
								'title' => 'AddToCart Page',
								'label' => 'AddToCart Event'
								),
							array(
								'event' => 'InitiateCheckout',
							 	'page' => 'woocommerce_checkout_page_id',   
							 	'title' => 'CheckOut Page',   
							 	'label' => 'InitiateCheckout Event'
							 ),
							/*array(
								'event' => 'AddPaymentInfo',
								'page' => 'woocommerce_checkout_page_id',  
								'title' => 'Checkout Page',  
								'label' => 'AddPaymentInfo Event'
								 ),*/
							array(
								'event' => 'Purchase',
								'page' => 'woocommerce_thanks_page_id',  
								'title' => 'Thankyou Page',  
								'label' => 'Purchase Event'
								 ),
							/*array(
								'event' => 'Search',
								'page' => 'woocommerce_search_product',
								'title' => 'Product Search',
								'label' => ' Search Event'
								 ),*/

							);

	return $woocommerce_events;
}

/**
 * Check if woocommerce event is activated in settings
 * @return string | true/false
 * @author PixelYourSite 
 **/
function woofp_is_woocommerce_event($event, $settings, $echo=true){


	if( isset( $settings['events'] ) && !empty($settings['events']) && isset($settings['events'][$event]) && $settings['events'][$event] == 1 ){
		 $checked = 'checked="checked"';
		 $return = true;	
	} else {

		$checked = '';
		$return = false;
	}

	if($echo){
		echo $checked;
	} else {

		return $return;
	}
}

function woofp_startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}

function woofp_endsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}


function woofp_valid_url($value){
        $pattern = "@^(?:https?://)?(?:[a-z0-9-]+\.)*((?:[a-z0-9-]+\.)[a-z]+)@";

        if (!preg_match($pattern, $value)){
            return false;
        }

        return true;
}

/**
 * add http:// to url if missing
 *
 * @return string
 * @author PixelYourSite 
 **/
function woofp_addhttp($url=''){

	if (preg_match("#https?://#", $url) === 0) {
    	$url = 'http://'.$url;
	}

	return $url;
}


/**
 * Remove http(s)/www. from url
 *
 * @return string
 * @author PixelYourSite 
 **/
function woofp_remove_protocol($url=''){

	if(empty($url) && !woofp_valid_url($url))
		return $url;

	$url = str_replace(array('http://', 'https://', 'http://www.', 'https://www.'), '', $url);
	
	$url = trim($url);
	$url = trim($url, '//');
	$url = rtrim($url, '/');

	return $url;
}


function woofp_match_url($current_url, $match_url, $eventtype='' ){

	$current_url = woofp_remove_protocol($current_url);
	$match_url = woofp_remove_protocol($match_url);
	
	if(strpos($match_url, '*') !== false && woofp_endsWith($match_url, '*') ){

		$temp_url = $match_url;
		$temp_url = rtrim($temp_url, '*');

		$temp_url = trim($temp_url);
		$temp_url = trim($temp_url, '//');
		$temp_url = rtrim($temp_url, '/');



		if ( woofp_valid_url($temp_url) === true) {
			
			$current_postid = url_to_postid( woofp_addhttp($current_url) );
			$match_postid   = url_to_postid( woofp_addhttp($temp_url) );
			$current_length = strlen($current_url);
			$match_length = strlen($temp_url);

			$postmatch = false;
			
			if( $current_postid && $match_postid && $current_postid == $match_postid ){
				$postmatch = true;
			}
			

			$strcmp1 = strcmp($temp_url, $current_url);
			$strcmp2 = strcmp($current_url, $temp_url);
			
			$found1 = ( (strpos($temp_url, $current_url) !== false ) ? 'true' : 'false' );
			$found2 = ( (strpos($current_url, $temp_url) !== false ) ? 'true' : 'false' );

			if( $match_length > $current_length && $found1 == 'true' ){
			
				$ret = ( ( $strcmp1  < 0 || $postmatch ) ? 'true' : 'false' );
				
			} else if( $match_length < $current_length && $found2 == 'true' ){
			
				$ret = ( ( $strcmp2  > 0 || $postmatch ) ? 'true' : 'false' );
				
			} else {
			
				$ret = ( ( $strcmp1  == 0 || $postmatch ) ? 'true' : 'false' );
				
			}

			
			return $ret;

		} else {

			
			return 'false';
		}

	} else {

		
		if ( woofp_valid_url($match_url) === true ) {

			$current_postid = url_to_postid( woofp_addhttp($current_url) );
			$match_postid   = url_to_postid( woofp_addhttp($match_url) );

			$postmatch = false;
			if( $current_postid && $match_postid && $current_postid == $match_postid ){
				$postmatch = true;
			}

			$strcmp = strcmp( $current_url, $match_url );
			$ret = ( ( $strcmp == 0 || $postmatch ) ? 'true' : 'false' );
			
			
			return $ret;

		} else {

			$strpos = strpos($current_url, $match_url);
			$ret = ( $strpos !== false ? 'true' : 'false' );
			
			
			return $ret;

		}
		
	}
}

function woofp_is_woocommerce(){
	
	if ( !class_exists( 'WooCommerce' ) ) {

		return false;

	} else {


		return true;
	}

}