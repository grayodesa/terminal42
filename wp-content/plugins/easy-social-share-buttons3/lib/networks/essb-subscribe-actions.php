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
	
	private static $version = "2.0";
	
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
		$user_name = isset ($_REQUEST['mailchimp_name']) ? $_REQUEST['mailchimp_name'] : '';
		
		if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
			$output['code'] = "99";
			$output['message'] = __('Invalid email address', 'essb');
		}
		else {
			$output = self::subscribe($user_email, $user_name);
		}
		
		
		print json_encode($output);
	}
	
	public static function subscribe($user_email, $user_name = '') {
		global $essb_options;
		
		$connector = ESSBOptionValuesHelper::options_value ( $essb_options, 'subscribe_connector', 'mailchimp' );
		if ($connector == '') {
			$connector = 'mailchimp';
		}
		
		$mc_api = ESSBOptionValuesHelper::options_value ( $essb_options, 'subscribe_mc_api' );
		$mc_list = ESSBOptionValuesHelper::options_value ( $essb_options, 'subscribe_mc_list' );
		$mc_welcome = ESSBOptionValuesHelper::options_bool_value($essb_options, 'subscribe_mc_welcome');
		$mc_double = ESSBOptionValuesHelper::options_bool_value($essb_options, 'subscribe_mc_double');
			
		$gr_api = ESSBOptionValuesHelper::options_value ( $essb_options, 'subscribe_gr_api' );
		$gr_list = ESSBOptionValuesHelper::options_value ( $essb_options, 'subscribe_gr_list' );
		
		$mm_list = ESSBOptionValuesHelper::options_value ( $essb_options, 'subscribe_mm_list' );
		
		$mp_list = ESSBOptionValuesHelper::options_value ( $essb_options, 'subscribe_mp_list' );
		
		$output = array();
		$output['name'] = $user_name;
		$output['email'] = $user_email;
				
		switch ($connector) {
			case "mailchimp":
				$result = self::subscribe_mailchimp($mc_api, $mc_list, $user_email, $mc_double, $mc_welcome, $user_name);
				$output['name'] = $user_name;
				$output['email'] = $user_email;
					
				if ($result) {
					$result = json_decode($result);
					if (isset($result->euid)) {
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
				break;
			case "getresponse":
				$output = self::subscribe_getresponse($gr_api, $gr_list, $user_email, $user_name);
				break;
			case "mymail":

				$output = self::subscribe_mymail($mm_list, $user_email, $user_name);
				break;
			case "mailpoet":
				
				$output = self::subscribe_mailpoet($mp_list, $user_email, $user_name);
				break;
		}
		
		return $output;
	}
	
	public static function subscribe_mailchimp($api_key, $list_id, $email, $double_option = false, $send_welcome = false, $name = '') {
		
		$dc = "us1";
		if (strstr ( $api_key, "-" )) {
			list ( $key, $dc ) = explode ( "-", $api_key, 2 );
			if (! $dc)
				$dc = "us1";
		}
		$mailchimp_url = 'https://' . $dc . '.api.mailchimp.com/2.0/lists/subscribe.json';
		$data = array ('apikey' => $api_key, 
				'id' => $list_id, 
				'email' => array ('email' => $email ), 
				'merge_vars' => array (
						'optin_ip' => $_SERVER ['REMOTE_ADDR'] ), 
				'replace_interests' => false, 
				'double_optin' => ($double_option ? true : false), 
				'send_welcome' => ($send_welcome == 'on' ? true : false), 
				'update_existing' => true );
		
		if (!empty($name)) {
			$fname = $name;
			$lname = '';
			if ($space_pos = strpos($name, ' ')) {
				$fname = substr($name, 0, $space_pos);
				$lname = substr($name, $space_pos);
			}

			$data['merge_vars']['FNAME'] = $fname;
			$data['merge_vars']['LNAME'] = $lname;
		}
		
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
	
	public static function subscribe_getresponse($api_key, $list_id, $email, $name = '') {
	
		if (!class_exists('GetResponse')) {
			include_once (ESSB3_PLUGIN_ROOT . 'lib/external/getresponse/getresponse.php');
				
		}
		
		$response = array();
		
		$api = new GetResponse ( $api_key );
		$campaignName = $list_id;
		$subscriberName = $name;
		$subscriberEmail = $email;
		
		$result = $api->getCampaigns ( 'EQUALS', $campaignName );
		$campaigns = array_keys ( ( array ) $result );
		$campaignId = array_pop ( $campaigns );
		
		$response = $api->addContact ( $campaignId, $subscriberName, $subscriberEmail );
		
		if (is_object ( $response ) && empty ( $response->code )) {
			$response ['code'] = '1';
			$response ['message'] = 'Thank you';
		} else {
			$response ['code'] = "99";
			$response ['message'] = __ ( 'Missing connection', 'essb' );
		}
		
		return $response;
	}

	public static function subscribe_mymail($list_id, $email, $name = '') {
		$response = array();
		
		
		if (function_exists('mymail_subscribe') || function_exists('mymail')) {
			$response ['code'] = '1';
			$response ['message'] = 'Thank you';
			
			if (function_exists('mymail')) {
				$list = mymail('lists')->get($list_id);
			} else {
				$list = get_term_by('id', $list_id, 'newsletter_lists');
			}
			
			if (!empty($list)) {
				try {
					$double = false;
					if (function_exists('mymail')) {
						$entry = array(
								'firstname' => $name,
								'email' => $email,
								'status' => $double ? 0 : 1,
								'ip' => $_SERVER['REMOTE_ADDR'],
								'signup_ip' => $_SERVER['REMOTE_ADDR'],
								'referer' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
								'signup' =>time()
						);
						
						$subscriber_id = mymail('subscribers')->add($entry, true);
						if (is_wp_error( $subscriber_id )) {
							$response['code'] = '99';
							return $response;
						}
						$result = mymail('subscribers')->assign_lists($subscriber_id, array($list->ID));
					} else {
						$result = mymail_subscribe($_subscriber['{subscription-email}'], array('firstname' => $_subscriber['{subscription-name}']), array($list->slug), $double);
					}
				} catch (Exception $e) {
					$response['code'] = '99';
				}
			}
		}
		else {
			$response ['code'] = "99";
			$response ['message'] = __ ( 'Missing connection', 'essb' );
		}
		
		return $response;
	}
	
	public static function subscribe_mailpoet($list_id, $email, $name = '') {
		$response = array();
	
	
		if (function_exists('mymail_subscribe') || function_exists('mymail')) {
			$response ['code'] = '1';
			$response ['message'] = 'Thank you';
				
			if (function_exists('mymail')) {
				$list = mymail('lists')->get($list_id);
			} else {
				$list = get_term_by('id', $list_id, 'newsletter_lists');
			}
				
			if (class_exists('WYSIJA')) {
				try {
					$user_data = array(
							'email' => $email,
							'firstname' => $name,
							'lastname' => '');
					$data_subscriber = array(
							'user' => $user_data,
							'user_list' => array('list_ids' => array($list_id))
					);
					$helper_user = WYSIJA::get('user','helper');
					$helper_user->addSubscriber($data_subscriber);
				} catch (Exception $e) {
					$response['code'] = '99';
				}
			}
		}
		else {
			$response ['code'] = "99";
			$response ['message'] = __ ( 'Missing connection', 'essb' );
		}
	
		return $response;
	}
}