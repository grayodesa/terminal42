<?php
/**
 * Subsctibe Actions
 *
 * @since 3.6
 *
 * @package EasySocialShareButtons
 * @author  appscreo <http://codecanyon.net/user/appscreo/portfolio>
 */

class ESSBNetworks_SubscribeActions {
	
	private static $version = "1.0";
	
	public static function process_subscribe() {
		global $essb_options;
		// send no caching headers
		
		define ( 'DOING_AJAX', true );
		
		send_nosniff_header ();
		header ( 'content-type: application/json' );
		header ( 'Cache-Control: no-cache' );
		header ( 'Pragma: no-cache' );
		
		$output = array("code" => "", "message" => "");
		
		$user_email = isset ( $_REQUEST ['mailchimp_email'] ) ? $_REQUEST ['mailchimp_email'] : '';
		
		if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
			$output['code'] = "99";
			$output['message'] = __('Invalid email address', 'essb');
		}
		else {
			$mc_api = ESSBOptionValuesHelper::options_value ( $essb_options, 'subscribe_mc_api' );
			$mc_list = ESSBOptionValuesHelper::options_value ( $essb_options, 'subscribe_mc_list' );
			$mc_welcome = ESSBOptionValuesHelper::options_bool_value($essb_options, 'subscribe_mc_welcome');
			
			$result = self::subscribe($mc_api, $mc_list, $user_email, false, $mc_welcome);
			
			if ($result) {
				$result = json_decode($result);
				
				if ($result->euid) {
					$output['code'] = '1';
					$output['message'] = 'Thank you';
				}
				else {
					$output['code'] = "99";
					$output['message'] = __('Missing connection', 'essb');
						
				}
			}
			else {
				$output['code'] = "99";
				$output['message'] = __('Missing connection', 'essb');
				
			}
		}
		
		
		print json_encode($output);
	}
	
	public static function subscribe($api_key, $list_id, $email, $double_option = false, $send_welcome = false) {
		
		$dc = "us1";
		if (strstr ( $api_key, "-" )) {
			list ( $key, $dc ) = explode ( "-", $api_key, 2 );
			if (! $dc)
				$dc = "us1";
		}
		$mailchimp_url = 'https://' . $dc . '.api.mailchimp.com/2.0/lists/subscribe.json';
		$data = array ('apikey' => $api_key, 'id' => $list_id, 'email' => array ('email' => $email ), 'merge_vars' => array ('optin_ip' => $_SERVER ['REMOTE_ADDR'] ), 'replace_interests' => false, 'double_optin' => ($double_option ? true : false), 'send_welcome' => ($send_welcome == 'on' ? true : false), 'update_existing' => true );
		
		$request = json_encode ( $data );
		$response = array();
		try {
			$curl = curl_init ( $mailchimp_url );
			curl_setopt ( $curl, CURLOPT_POST, 1 );
			curl_setopt ( $curl, CURLOPT_POSTFIELDS, $request );
			curl_setopt ( $curl, CURLOPT_TIMEOUT, 10 );
			curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt ( $curl, CURLOPT_FORBID_REUSE, 1 );
			curl_setopt ( $curl, CURLOPT_FRESH_CONNECT, 1 );
			// curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, 0 );
			curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, 0 );
			
			$response = curl_exec ( $curl );
			curl_close ( $curl );
		} 
		catch ( Exception $e ) {
		}
		
		return $response;
	}

}