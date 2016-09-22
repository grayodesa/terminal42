<?php
class wmoney_uah  extends WC_Payment_Gateway {
	// WebMoney
	private $wmpurse_uah;
	private $wpfailUrl;
	private $wpresultUrl;
	private $wpsuccessUrl;
	private $wpMode;
	private $wpModeshop;
	private $WM_CACERT;
	private $wpWmSecretKey;
	private $wpHashMethod;
	private $WM_WMSIGNER_PATH;
	private $wpWMID;
	private $unfiltered_request_saphalid;
	
	private $api_url = 'https://saphali.com/api';
	//private $_api_url = 'http://saphali.com/api';
	
	private $wpApiUrl = 'https://merchant.webmoney.ru/lmi/payment.asp';
	private $wpApiUrl_en = 'https://merchant.wmtransfer.com/lmi/payment.asp';

	
	public function __construct () {
		// Webmoney
		global $woocommerce;
		$this->WM_CACERT = SAPHALI_PLUGIN_DIR_PATH_WM_EXCLUDE . 'WebMoneyCA.crt'; 
		
		$this->id 			= 'wmoney_uah';

		$this->icon = apply_filters('woocommerce_wmoney_icon', SAPHALI_PLUGIN_DIR_URL_WM_EXCLUDE .'images/icons/webmoney.png');
		$this->WM_WMSIGNER_PATH = SAPHALI_PLUGIN_DIR_PATH_WM_EXCLUDE .'sign/WMSigner'; 
		$this->wpHashMethod = get_option('wpHashMethod');
		$this->wmpurse_uah = get_option('wmpurse_uah');
		$this->wpWMID = get_option('wpWMID');
		$this->wpfailUrl = get_option('wpfailUrl' . $this->id);
		$this->wpresultUrl = get_option('wpresultUrl' . $this->id);
		$this->wpsuccessUrl = get_option('wpsuccessUrl' . $this->id);
		$this->wpMode = get_option('wpSimMode');
		$this->wpModeshop = get_option('wpMode');
		$this->wpWmSecretKey = base64_decode(strrev(get_option('wpWmSecretKey')));

		$this->has_fields = false;
		
		$this->init_form_fields();
		$this->init_settings();
		$this->debug = $this->settings['debug'];
		$this->description = $this->settings['description'];
		$this->lang = $this->settings['lang'];
		$this->form_submission_method = $this->settings['form_submission_method'] == 'yes' ? true : false;
		$this->enabled = get_option('woocommerce_wmoney_enabled' . $this->id);
		$this->title = get_option('woocommerce_wmoney_title'  . $this->id);
		if ($this->debug=='yes') { if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '<' ) ) $this->log = $woocommerce->logger(); else $this->log = new WC_Logger(); }
		add_action('valid-'.$this->id.'-callback', array(&$this, 'successful_request_wm') );

		if ( version_compare( WOOCOMMERCE_VERSION, '2.0', '<' ) ) {
		add_action('woocommerce_update_options', array(&$this, 'process_admin_options'));
			add_action('woocommerce_update_options_payment_gateways', array(&$this, 'process_admin_options'));
			add_action('init', array(&$this, 'check_callback_wm') );
		} else {
			add_action( 'woocommerce_api_' . strtolower( get_class( $this ) ), array( $this, 'check_callback_wm' ) );
			add_action('woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		}
		add_action('woocommerce_receipt_' .  $this->id , array($this, 'receipt_page'));
		$transient_name = 'wc_saph_' . md5( 'payment-webmoney' . home_url() );
		$this->unfiltered_request_saphalid = get_transient( $transient_name );
		if(empty($this->title))
		add_option('woocommerce_wmoney_title'  . $this->id, __('WebMoney (WMU)', 'loc-saphali-wm') );
		//add_filter('woocommerce_payment_successful_result', array($this, '_receipt_page') );
		if ( false === $this->unfiltered_request_saphalid ) {
			// Get all visible posts, regardless of filters
			if( defined( 'SAPHALI_PLUGIN_VERSION_ST' ) ) $version = SAPHALI_PLUGIN_VERSION_ST; 
			elseif( defined( 'SAPHALI_PLUGIN_VERSION_WM_EXCLUDE' ) ) $version = SAPHALI_PLUGIN_VERSION_WM_EXCLUDE; else  $version ='1.0';
			$args = array(
				'method' => 'POST',
				'plugin_name' => "payment-webmoney", 
				'version' => $version,
				'username' => home_url() , 
				'password' => '1111',
				'action' => 'saphali_api'
			);
			$response = $this->prepare_request( $args );
			if(isset($response->errors) && $response->errors ) { echo '<div class="inline error"><p>'.$response->errors["http_request_failed"][0]; echo '</p></div>'; } else {
				if($response["response"]["code"] == 200 && $response["response"]["message"] == "OK") {
					$this->unfiltered_request_saphalid = $response['body'];
				} else {
					$this->unfiltered_request_saphalid = 'echo \'<div class="inline error"><p> Ошибка \'.$response["response"]["code"] . $response["response"]["message"].\'<br /><a href="mailto:saphali@ukr.net">Свяжитесь с разработчиком.</a></p></div>\';'; 
				}
			}
			if( !empty($this->unfiltered_request_saphalid) &&  $this->is_valid_for_use() ) {
				set_transient( $transient_name, $this->unfiltered_request_saphalid , 60*60*24*30 );			
			}
		}
		if ( false ===  $this->unfiltered_request_saphalid || !$this->is_valid_for_use_cur() ) $this->enabled = false;
	}

	function receipt_page( $order ) {
		
		echo '<p>'.__('Thank you for your order, please click the button below to pay with WebMoney.', 'themewoocommerce').'</p>';
		echo $this->generate_form( $order );
		
	}
	function init_form_fields() {
		$debug = sprintf(__( 'Log %s events, such as IPN requests, inside <code>woocommerce/logs/%s.txt</code>', 'loc-saphali-wm' ), 'WebMoney', $this->id);
		if ( !version_compare( WOOCOMMERCE_VERSION, '2.0', '<' ) ) {
			if ( version_compare( WOOCOMMERCE_VERSION, '2.2.0', '<' ) )
			$debug = str_replace( $this->id, $this->id . '-' . sanitize_file_name( wp_hash( $this->id ) ), $debug );
			elseif( function_exists('wc_get_log_file_path') ) {
				$debug = str_replace( 'woocommerce/logs/' . $this->id . '.txt', '<a href="/wp-admin/admin.php?page=wc-status&tab=logs&log_file=' . $this->id . '-' . sanitize_file_name( wp_hash( $this->id ) ) . '-log" target="_blank">' . wc_get_log_file_path( $this->id ) . '</a>' , $debug );
			}
		}
		$this->form_fields = array(
			'description' => array(
							'title' => __( 'Description', 'woocommerce' ),
							'type' => 'textarea',
							'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce' ),
							'default' => ''
						),
			'form_submission_method' => array(
							'title' => __( 'Submission method', 'woocommerce' ),
							'type' => 'checkbox',
							'label' => __( 'Use form submission method.', 'woocommerce' ),
							'description' => __( 'Clear the check here if you want to redirect to the site WM occurred immediately after clicking on the "Checkout / Place Order", bypassing the additional stage of transition to a page with a form.', 'loc-saphali-wm' ),
							'default' => 'yes'
						),
			'lang' => array(
							'title' => __( 'Language of the page of payment Russian?', 'loc-saphali-wm' ),
							'type' => 'checkbox',
							'label' => __( 'Yes', 'woocommerce' ),
							'description' => __( 'If you set the option, then going to pay for the site WebMoney village will be in Russian, otherwise - in English.', 'loc-saphali-wm' ),
							'desc_tip'    => true,
							'default' => 'yes'
						),
			'debug' => array(
							'title' => __( 'Debug Log', 'themewoocommerce' ),
							'type' => 'checkbox',
							'label' => __( 'Enable logging', 'themewoocommerce' ),
							'default' => 'no',
							'description' => $debug,
						)
		);
	}
	function prepare_request( $args ) {
		$request = wp_remote_post( $this->api_url, array(
			'method' => 'POST',
			'timeout' => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(),
			'body' => $args,
			'cookies' => array(),
			'sslverify' => false
		));
		// Make sure the request was successful
		return $request;
		if( is_wp_error( $request )
			or
			wp_remote_retrieve_response_code( $request ) != 200
		) { return false; }
		// Read server response, which should be an object
		$response = maybe_unserialize( wp_remote_retrieve_body( $request ) );
		if( is_object( $response ) ) {
				return $response;
		} else { return false; }
	} // End prepare_request()
	
	function is_valid_for_use_cur() {
		if (!in_array(get_option('woocommerce_currency'), array('UAH', 'RUR', 'RUB', 'USD') )) {
			return false;
		}
		return true;
	}
	function is_valid_for_use() {
			if( defined( 'SAPHALI_PLUGIN_VERSION_ST' ) ) $version = SAPHALI_PLUGIN_VERSION_ST; 
		elseif( defined( 'SAPHALI_PLUGIN_VERSION_WM_EXCLUDE' ) ) $version = SAPHALI_PLUGIN_VERSION_WM_EXCLUDE; else  $version ='1.0';
		$args = array(
			'method' => 'POST',
			'plugin_name' => "payment-webmoney", 
			'version' => $version,
			'username' => home_url(), 
			'password' => '1111',
			'action' => 'pre_saphali_api'
		);
		$response = $this->prepare_request( $args );
		if(isset($response->errors) && $response->errors) { return false; } else {
			if($response["response"]["code"] == 200 && $response["response"]["message"] == "OK") {
				eval($response['body']);
			}else {
				return false;
			}
		}
        return $is_valid_for_use;
    }
	function wm_GetSign($inStr)
	{ 
		$descriptorspec = array(
		   0 => array("pipe", "r"),
		   1 => array("pipe", "w"),
		   2 => array("pipe", "r") 
		);
		$process = proc_open($this->WM_WMSIGNER_PATH, $descriptorspec, $pipes );
		if (is_resource($process)) {
			fwrite($pipes[0], "$inStr\004\r\n");
			fclose($pipes[0]);
			$s = fgets($pipes[1], 133);
			fclose($pipes[1]);
			$return_value = proc_close($process);
			return $s;
	   }
	}
	function successful_request_wm( $posted ) {
	//die(1);
		if( isset($posted['LMI_PREREQUEST']) && $posted['LMI_PREREQUEST'] == 1){ # Prerequest
				if( isset($posted['LMI_PAYMENT_NO']) 
				&& preg_match('/^\d+$/',$posted['LMI_PAYMENT_NO']) == 1  # Payment inner id
					&& isset($posted['RND']) && preg_match('/^[A-Z0-9]{8}$/',$posted['RND'],$match) == 1){
					if (!class_exists('WC_Order')) $order = new woocommerce_order( $posted['LMI_PAYMENT_NO'] ); else
						$order = new WC_Order( $posted['LMI_PAYMENT_NO'] );
					if ( !($order_r = $order->get_order($posted['LMI_PAYMENT_NO'])) ) {
						if ($this->debug=='yes') $this->log->add( $this->id, 'Заказ #' . $posted['LMI_PAYMENT_NO'] . ' не найден.'   );
					} else {
						if(get_option('value_usd_cur') != '') {$curs_usd = get_option('value_usd_cur');}else {$curs_usd = 24;}
						if(get_option('value_rur_cur') != '') {$curs_rur = get_option('value_rur_cur');} else {$curs_rur = 0.4;}
						$_get_woocommerce_currency = isset($order->order_currency) ? $order->order_currency : get_woocommerce_currency();
						$get_woocommerce_currency = apply_filters('woocommerce_get_order_currency', $_get_woocommerce_currency, $order);
						if($posted['FIELD_2'] == 'USD') {
							$_PURSE_wm = get_option('wmpurse_usd');
							if($get_woocommerce_currency == 'UAH') {
								$order->order_total = number_format($order->order_total/$curs_usd, 2, '.', '');
							}elseif($get_woocommerce_currency == 'RUR' || $get_woocommerce_currency == 'RUB') {
								$order->order_total = number_format($order->order_total/($curs_usd/$curs_rur), 2, '.', '');
							} elseif($get_woocommerce_currency != 'USD') {
								$order->order_total = number_format($order->order_total/$curs_usd, 2, '.', '');
							}
						} elseif($posted['FIELD_2'] == 'UAH') {
							$_PURSE_wm = $this->wmpurse_uah;
							if($get_woocommerce_currency == 'RUR' || $get_woocommerce_currency == 'RUB') {
								$order->order_total = number_format($order->order_total*$curs_rur, 2, '.', '');
							} elseif($get_woocommerce_currency == 'USD') {
								$order->order_total = number_format($order->order_total*$curs_usd, 2, '.', '');
							}
						} elseif($posted['FIELD_2'] == 'RUB') {
							$_PURSE_wm = get_option('wmpurse_rur');
							if($get_woocommerce_currency == 'UAH') {
								$order->order_total = number_format($order->order_total/$curs_rur, 2, '.', '');
							}elseif($get_woocommerce_currency == 'USD') {
								$order->order_total = number_format($order->order_total*($curs_usd/$curs_rur), 2, '.', '');
							} elseif( !($get_woocommerce_currency == 'RUR' || $get_woocommerce_currency == 'RUB') ) {
								$order->order_total = number_format($order->order_total/$curs_rur, 2, '.', '');
							}
						}
						$order->order_total = number_format($order->order_total, 2, '.', '');
						# If no payment or items found
							 if( $posted['LMI_PAYMENT_NO'] == $order->id # Check if payment id, purse number and ammount correspond with each other 
								&& $posted['LMI_PAYEE_PURSE'] == $_PURSE_wm
								&& $posted['LMI_PAYMENT_AMOUNT'] == number_format($order->order_total, 2, '.', '') ){ # step 5
									# reserve
								# Update payment  as _reserved_ 
								if ($this->debug=='yes') $this->log->add( $this->id, 'Статус заказа #' . $order->id . ': in_process. Шаг 5.' );
								
								//$order->update_status('processing', __('Awaiting cheque payment', 'woocommerce'));
								//woocommerce_cart::empty_cart();
								if($posted['LMI_MODE ']) $mode = '. Платеж выполнялся в тестовом режиме'; else $mode = '';
								if(isset($posted['LMI_SDP_TYPE '])) {
									if( $posted['LMI_SDP_TYPE '] == 0) 
									$LMI_SDP_TYPE = '. Через системы денежных переводов'; 
									elseif( $posted['LMI_SDP_TYPE '] == 3) 
									$LMI_SDP_TYPE = '. Через Альфа-клик'; 
									elseif( $posted['LMI_SDP_TYPE '] == 4) 
									$LMI_SDP_TYPE = '. Через карты российских банков,'; 
									elseif( $posted['LMI_SDP_TYPE '] == 5) 
									$LMI_SDP_TYPE = '. Через интернет банкинг Русский стандарт'; 
									elseif( $posted['LMI_SDP_TYPE '] == 6) 
									$LMI_SDP_TYPE = '. Через интернет банкинг ВТБ24'; 
									elseif( $posted['LMI_SDP_TYPE '] == 7) 
									$LMI_SDP_TYPE = '. Бонусами Спасибо Сбербанка'; 
									elseif( $posted['LMI_SDP_TYPE '] == 8) 
									$LMI_SDP_TYPE = '. Через терминалы и банки (только для WMU-кошельков)'; 
									elseif( $posted['LMI_SDP_TYPE '] == 10) 
									$LMI_SDP_TYPE = '. С карт Visa\MasterCard\НСМЭП\Приват24 (только для WMU-кошельков)'; 
									else $LMI_SDP_TYPE = ''; 
								}
								$order->add_order_note( 'Заказ #'.$order->id.' в процессе оплаты' . $mode . $LMI_SDP_TYPE ) ;
								if ($this->debug=='yes') $this->log->add( $this->id, 'Оплата заказа #'.$order->id.'. В процессе выполнения.
								' );
								echo 'YES'; # if everything is ok and items are reserved,  give ok to transaction
								
							
							} else { # step 5
								if ($this->debug=='yes') $this->log->add( $this->id, 'Ошибка: Противоречивые параметры. Неверный кошелек WM. Заказ #' . $posted['LMI_PAYMENT_NO']. '. Шаг 5.');
							};
						}
				} else { # step 3
					if ($this->debug=='yes') $this->log->add( $this->id, 'Ошибка: Противоречивые параметры. Заказ #' . $posted['LMI_PAYMENT_NO']. '. Шаг 3.');
				};
			}else{
				if( isset($posted['LMI_PAYMENT_NO']) # Check payment id
				&&  preg_match('/^\d+$/',$posted['LMI_PAYMENT_NO']) == 1 
				&& isset($posted['RND']) && preg_match('/^[A-Z0-9]{8}$/',$posted['RND'],$match) == 1){ # Check ticket, step 11
				# Query form database about payment with such id
				if (!class_exists('WC_Order')) $order = new woocommerce_order( $posted['LMI_PAYMENT_NO'] ); else
									$order = new WC_Order( $posted['LMI_PAYMENT_NO'] );
				if ( !($order_r = $order->get_order($posted['LMI_PAYMENT_NO'])) ) {
						if ($this->debug=='yes') $this->log->add( $this->id, 'Заказ #' . $posted['LMI_PAYMENT_NO'] . ' не найден.'   );
					} else { # If payment or items were not found,

					# Create check string
					
					if(get_option('value_usd_cur') != '') {$curs_usd = get_option('value_usd_cur');}else {$curs_usd = 24;}
					if(get_option('value_rur_cur') != '') {$curs_rur = get_option('value_rur_cur');} else {$curs_rur = 0.4;}
					$_get_woocommerce_currency = isset($order->order_currency) ? $order->order_currency : get_woocommerce_currency();
					$get_woocommerce_currency = apply_filters('woocommerce_get_order_currency', $_get_woocommerce_currency, $order);
					if($posted['FIELD_2'] == 'USD') {
						$_PURSE_wm = get_option('wmpurse_usd');
						if($get_woocommerce_currency == 'UAH') {
							$order->order_total = number_format($order->order_total/$curs_usd, 2, '.', '');
						}elseif($get_woocommerce_currency == 'RUR' || $get_woocommerce_currency == 'RUB') {
							$order->order_total = number_format($order->order_total/($curs_usd/$curs_rur), 2, '.', '');
							} elseif($get_woocommerce_currency != 'USD') {
							$order->order_total = number_format($order->order_total/$curs_usd, 2, '.', '');
						}
					} elseif($posted['FIELD_2'] == 'UAH') {
						$_PURSE_wm = $this->wmpurse_uah;
						if($get_woocommerce_currency == 'RUR' || $get_woocommerce_currency == 'RUB') {
							$order->order_total = number_format($order->order_total*$curs_rur, 2, '.', '');
						} elseif($get_woocommerce_currency == 'USD') {
							$order->order_total = number_format($order->order_total*$curs_usd, 2, '.', '');
						}
					} elseif($posted['FIELD_2'] == 'RUB') {
						$_PURSE_wm = get_option('wmpurse_rur');
						if($get_woocommerce_currency == 'UAH') {
							$order->order_total = number_format($order->order_total/$curs_rur, 2, '.', '');
						}elseif($get_woocommerce_currency == 'USD') {
							$order->order_total = number_format($order->order_total*($curs_usd/$curs_rur), 2, '.', '');
						} elseif( !($get_woocommerce_currency == 'RUR' || $get_woocommerce_currency == 'RUB') ) {
							$order->order_total = number_format($order->order_total/$curs_rur, 2, '.', '');
						}
					}
					$order->order_total = number_format($order->order_total, 2, '.', '');
					$chkstring =  $_PURSE_wm . number_format($order->order_total, 2, '.', '') . $order->id.
						$posted['LMI_MODE'].$posted['LMI_SYS_INVS_NO'].$posted['LMI_SYS_TRANS_NO'].$posted['LMI_SYS_TRANS_DATE'].
							$this->wpWmSecretKey.$posted['LMI_PAYER_PURSE'].$posted['LMI_PAYER_WM'];
					if ( $this->wpHashMethod == 'MD5' ) {
						$md5sum = strtoupper(md5($chkstring));
					$hash_check = ($posted['LMI_HASH'] == $md5sum);
					}elseif( $this->wpHashMethod == 'SHA256' ) {
						$sha256 = strtoupper( hash('sha256', $chkstring) ) ;
						$hash_check = ($posted['LMI_HASH'] == $sha256);
					}  elseif( $this->wpHashMethod == 'SIGN' ) {
						$PlanStr=$this->wpWMID.'967909998006'.$chkstring.$posted['LMI_HASH'];
						error_log("PlanStr: $PlanStr");
						$SignStr=$this->wm_GetSign($PlanStr);
						error_log("SignStr: $SignStr");
						if( strlen($SignStr) < 132){
							 if ($this->debug=='yes') $this->log->add( $this->id, 'Заказ #' . $posted['LMI_PAYMENT_NO'] . "Error: WMSigner response: ".$SignStr   );
							die();
						};
						$req="/asp/classicauth.asp?WMID={$this->wpWMID}&CWMID=967909998006&CPS=".urlencode($chkstring).
						"&CSS=".$posted['LMI_HASH']."&SS=$SignStr";
						error_log("URL: $req");
						$resp=$this->wm_HttpsReq($req);
						if($resp=='Yes'){
							$hash_check = TRUE ;
						} else {
							if ($this->debug=='yes') $this->log->add( $this->id, 'Заказ #' . $posted['LMI_PAYMENT_NO'] . "Error: w3s.webmoney.ru response: ".$resp   );
							die();
						}
					} else {
						if ($this->debug=='yes') $this->log->add( $this->id, 'Заказ #' . $posted['LMI_PAYMENT_NO'] . "Config parameter LMI_HASH_METHOD incorrect!"   );
						die();
					};	  
					if( $posted['LMI_PAYMENT_NO'] == $order->id # Check if payment id, purse number and amount correspond
					&& $posted['LMI_PAYEE_PURSE'] == $_PURSE_wm 
					&& $posted['LMI_PAYMENT_AMOUNT'] == number_format($order->order_total, 2, '.', '')
					&& $posted['LMI_MODE'] == $this->wpModeshop
					&& $hash_check ) {  # checksum is correct, step 15
						# if everything is ok, payment receives status: Paid, item receives status: Sold,
						# enter payment and customer data into database
						
						$order->payment_complete();
						
								$order->add_order_note( sprintf( __("Payment Order #%s done by WebMoney<br /> Internal account number in the system WebMoney Transfer: %s<br /> Extension payment system WebMoney Transfer: %s<br /> Date and time of payment: %s<br /> Purse buyer - %s<br /> WMId buyer - '%s'", "loc-saphali-wm"), $order->id , $posted['LMI_SYS_INVS_NO'], $posted['LMI_SYS_TRANS_NO'], $posted['LMI_SYS_TRANS_DATE'], $posted['LMI_PAYER_PURSE'], $posted['LMI_PAYER_WM'] ) ) ;
								if ($this->debug=='yes') $this->log->add( $this->id, sprintf(__('Payment order #%s executed.', 'loc-saphali-wm'), $order->id) );
								
								//wp_redirect(add_query_arg('key', $order->order_key, add_query_arg('order', $order->id, get_permalink(get_option('woocommerce_thanks_page_id')))));
								exit;
					} else { # step 15
						if ($this->debug=='yes') $this->log->add( $this->id, 'Заказ #' . $posted['LMI_PAYMENT_NO'] . ". Inconsistent parameters" . '# step 15' . print_r($posted, true) .  print_r($sha256 . '|' . $md5sum . '|' . $posted['LMI_HASH'] . '|' . number_format($order->order_total, 2, '.', '') , true) );
						die('Inconsistent parameters');
					};
				  }
				} else { # step 11
					if ($this->debug=='yes') $this->log->add( $this->id, 'Заказ #' . $posted['LMI_PAYMENT_NO'] . "Inconsistent parameters" . '# step 11'  );
					die('Inconsistent parameters');
				};
			}
			exit;
	}
	function wm_HttpsReq($addr)
	{
	  $ch = curl_init("https://w3s.webmoney.ru".$addr);
	  curl_setopt($ch, CURLOPT_HEADER, 0);
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	#  If WebMoney CA root certificate is not installed into SSL, state path to it:
	  curl_setopt($ch, CURLOPT_CAINFO, $this->WM_CACERT);
	# Attention!Do not use curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE)!
	# It can allow DNS  attack.  
	  $result=curl_exec($ch);
	  if( curl_errno($ch) != 0 ) {
		die('CURL_error: ' . curl_errno($ch) . ', ' . curl_error($ch));
	  };
	  curl_close($ch);
	  return $result;
	}
	function check_callback_wm() {
		if ( strpos($_SERVER["REQUEST_URI"], 'order_results_go')!==false && strpos($_SERVER["REQUEST_URI"], 'wc-api=' . $this->id)!==false ) {
			error_log('WebMoney callback!');
			$_REQUEST = stripslashes_deep($_REQUEST);
			do_action("valid-".$this->id."-callback", $_REQUEST);
		} elseif(strpos($_SERVER["REQUEST_URI"], 'wc-api=' . $this->id)!==false) {
			$posted = $_REQUEST;
			if($_GET['wc-api'] == $this->id && $_GET['fail']==1) {
				if (!class_exists('WC_Order')) $order = new woocommerce_order( $posted['LMI_PAYMENT_NO'] ); else
				$order = new WC_Order( $posted['LMI_PAYMENT_NO'] );
				$order->update_status('failed', __('Awaiting cheque payment', 'woocommerce'));
				wp_redirect($order->get_cancel_order_url());
				exit;
			}elseif($_GET['wc-api'] == $this->id ) {
				if( isset($posted['LMI_PAYMENT_NO']) # Check payment id
				&&  preg_match('/^\d+$/',$posted['LMI_PAYMENT_NO']) == 1 
				&& isset($posted['RND']) && preg_match('/^[A-Z0-9]{8}$/',$posted['RND'],$match) == 1){ # Check ticket, step 11
					if (!class_exists('WC_Order')) $order = new woocommerce_order( $posted['LMI_PAYMENT_NO'] ); else
							$order = new WC_Order( $posted['LMI_PAYMENT_NO'] );
					if ( !($order_r = $order->get_order($posted['LMI_PAYMENT_NO'])) ) {
						if ($this->debug=='yes') $this->log->add( $this->id, 'Заказ #' . $posted['LMI_PAYMENT_NO'] . ' не найден.'   );
					} else { # If payment or items were not found,
						$orderid = $_REQUEST['LMI_PAYMENT_NO'] ;
						if (!class_exists('WC_Order')) $order = new woocommerce_order( $orderid ); else
						$order = new WC_Order( $orderid );
						if ( !version_compare( WOOCOMMERCE_VERSION, '2.1.0', '<' ) ) { wp_redirect( $this->get_return_url( $order ) );exit;}
						wp_redirect(add_query_arg('key', $order->order_key, add_query_arg('order', $orderid, get_permalink(get_option('woocommerce_thanks_page_id')))));
						exit;
					}
				} else { # step 11
					if ($this->debug=='yes') $this->log->add( $this->id, 'Заказ #'.$order->id.'. Противоречивые параметры. Шаг 11.' ); $order->update_status('failed', __('Awaiting cheque payment', 'woocommerce'));exit;
				};
			}
		}
	}
	
		public function admin_options()
		{
		//$title = 'Конфигурация WebMoney';
		if (!empty($message)) { ?>
			<div id="message" class="updated fade"><p><?php echo $message; ?></p></div>
<?php } 
		if($this->is_valid_for_use_cur()) {
		if($this->unfiltered_request_saphalid !== false)
		eval($this->unfiltered_request_saphalid); 
		if(isset($messege)) echo $messege;
		} else { ?>
		<div class="inline error"><p><strong><?php _e('Шлюз отключен', 'woocommerce'); ?></strong>: <?php echo sprintf(__('%s не поддерживает валюты Вашего магазина.', 'loc-saphali-wm' ), $this->title); ?></p></div>
		<?php
		}
				?>
<?php


	}
		public function process_admin_options () {
			if($_POST['woocommerce_wmoney_title'] . $this->id) {
				update_option('value_rur_cur',$_POST['value_rur_cur']);
				update_option('value_usd_cur',$_POST['value_usd_cur']);
				update_option('wpWmSecretKey',strrev(base64_encode($_POST['wpWmSecretKey'])));
				update_option('wpWMID',$_POST['wpWMID']);
				
				update_option('wmpurse_uah',$_POST['wmpurse_uah']);

				update_option('wpHashMethod',$_POST['wpHashMethod']);
				update_option('wpfailUrl'. $this->id,$_POST['wpfailUrl' . $this->id]);
				update_option('wpresultUrl'. $this->id,$_POST['wpresultUrl'  . $this->id]);
				update_option('wpsuccessUrl'. $this->id,$_POST['wpsuccessUrl'  . $this->id]);
				update_option('wpMode',$_POST['wpMode']);
				update_option('wpSimMode',$_POST['wpSimMode']);
				if(isset($_POST['woocommerce_wmoney_enabled' . $this->id])) update_option('woocommerce_wmoney_enabled' . $this->id, woocommerce_clean($_POST['woocommerce_wmoney_enabled' . $this->id])); else @delete_option('woocommerce_wmoney_enabled'. $this->id);
				if(isset($_POST['woocommerce_wmoney_title' . $this->id])) update_option('woocommerce_wmoney_title' . $this->id, woocommerce_clean($_POST['woocommerce_wmoney_title' . $this->id])); else @delete_option('woocommerce_wmoney_title' . $this->id);
				
				$this->validate_settings_fields();

				if ( count( $this->errors ) > 0 ) {
					$this->display_errors();
					return false;
				} else {
					if( version_compare( WOOCOMMERCE_VERSION, '2.6', '<' ) ){
						update_option( $this->plugin_id . $this->id . '_settings', $this->sanitized_fields );
						return true;
					}
					else {
						$post_data = $this->get_post_data();
						$form_fields = array_keys( $this->form_fields );
						foreach($form_fields as $array_keys) {
							$this->settings[ $array_keys ] = isset( $post_data['woocommerce_' . $this->id . '_' . $array_keys]) ?  $post_data['woocommerce_' . $this->id . '_' . $array_keys] : (isset( $post_data[$array_keys] ) ?  $post_data[$array_keys]  :  $this->form_fields[$array_keys]['default']);
							if( in_array($array_keys, array('enabled','debug','lang','form_submission_method')) ) {
								$this->settings[ $array_keys ] = $this->get_field_value( $array_keys, $this->settings[ $array_keys ], $post_data ) ? 'yes' : 'no';
							}
						}
						update_option( $this->plugin_id . $this->id . '_settings', $this->settings );
					}
					return true;
				}
			}
		}
	
	public function generate_form( $order_id ) {
		$order = new WC_Order( $order_id );
		$rnd = strtoupper(substr(md5(uniqid(microtime(), 1)).getmypid(),1,8));
		//$description = sanitize_title_with_translit(get_the_title());
		
		if ($this->debug=='yes') $this->log->add( $this->id, 'Создание платежной формы для заказа #' . $order_id . '.');
		
		$descRIPTION = $descRIPTION_ = '';
		
		$order_items = $order->get_items( apply_filters( 'woocommerce_admin_order_item_types', array( 'line_item', 'fee' ) ) );
		$count  = 0 ;
		foreach ( $order_items as $item_id => $item ) {
		
		$descRIPTION_ .= esc_attr( $item['name'] );
		if ( !version_compare( WOOCOMMERCE_VERSION, '2.0', '<' ) ) {
			if ( $metadata = $order->has_meta( $item_id )) {
						$_descRIPTION = '';
						$is_ = false;
						$is_count = 0;
						if ( !version_compare( WOOCOMMERCE_VERSION, '2.2', '<' ) ) {
							$product   = $order->get_product_from_item( $item );
							$item_meta = new WC_Order_Item_Meta( $item, $product );
							$metadata = $item_meta->get_formatted();
							foreach ( $metadata as $meta ) {
								$is_ = true;
								if($is_count == 0)
								$_descRIPTION .= esc_attr(' ['.$meta['label'] . ': ' . $meta['value'] );
								else
								$_descRIPTION .= esc_attr(', '.$meta['label'] . ': ' . $meta['value'] );
								$is_count++;
							}
						}
						else 
						foreach ( $metadata as $meta ) {

							// Skip hidden core fields
							if ( in_array( $meta['meta_key'], apply_filters( 'woocommerce_hidden_order_itemmeta', array(
								'_qty',
								'_tax_class',
								'_product_id',
								'_variation_id',
								'_line_subtotal',
								'_line_subtotal_tax',
								'_line_total',
								'_line_tax',
							) ) ) ) continue;

							// Handle serialised fields
							if ( is_serialized( $meta['meta_value'] ) ) {
								if ( is_serialized_string( $meta['meta_value'] ) ) {
									// this is a serialized string, so we should display it
									$meta['meta_value'] = maybe_unserialize( $meta['meta_value'] );
								} else {
									continue;
								}
							}
							$is_ = true;
							if($is_count == 0)
							$_descRIPTION .= esc_attr(' ['.$meta['meta_key'] . ': ' . $meta['meta_value'] );
							else
							$_descRIPTION .= esc_attr(', '.$meta['meta_key'] . ': ' . $meta['meta_value'] );
							$is_count++;
						}
						if($is_count > 0)
						$_descRIPTION = $_descRIPTION. '] - '.$item['qty']. '';
						else $_descRIPTION = $_descRIPTION. ' - '.$item['qty']. '';
					}
					if(($count + 1) != count($order_items) && !empty($descRIPTION_)) $descRIPTION .=  $descRIPTION_.$_descRIPTION . ', '; else $descRIPTION .=  ''.$descRIPTION_.$_descRIPTION; 
					$count++;
					$descRIPTION_ = $_descRIPTION = '';
			}else {
				if ( $metadata = $item["item_meta"]) {
					$_descRIPTION = '';
					foreach($metadata as $k =>  $meta) {
						if($k == 0)
						$_descRIPTION .= esc_attr(' - '.$meta['meta_name'] . ': ' . $meta['meta_value'] . '');
						else {
							$_descRIPTION .= esc_attr('; '.$meta['meta_name'] . ': ' . $meta['meta_value'] . '');
						}
					}
				}
				if($item_id == 0)$descRIPTION = esc_attr( $item['name'] ) . $_descRIPTION .' ('.$item["qty"].')'; else
				$descRIPTION .= ', '. esc_attr( $item['name'] ) . $_descRIPTION .' ('.$item["qty"].')';
			}
		}
		if(get_option('value_usd_cur') != '') {$curs_usd = get_option('value_usd_cur');}else {$curs_usd = 24;}
		if(get_option('value_rur_cur') != '') {$curs_rur = get_option('value_rur_cur');} else {$curs_rur = 0.4;}
		$currensy_simb = 'UAH';
		$_get_woocommerce_currency = isset($order->order_currency) ? $order->order_currency : get_woocommerce_currency();
		$get_woocommerce_currency = apply_filters('woocommerce_get_order_currency', $_get_woocommerce_currency, $order);
		if($get_woocommerce_currency == 'UAH') {
			$_PURSE_wm = "<input type='hidden' name='LMI_PAYEE_PURSE' value='{$this->wmpurse_uah}'>";
		}elseif($get_woocommerce_currency == 'RUR' || $get_woocommerce_currency == 'RUB') {
			$order->order_total = number_format($order->order_total*$curs_rur, 2, '.', '');
			$_PURSE_wm = "<input type='hidden' name='LMI_PAYEE_PURSE' value='{$this->wmpurse_uah}'>";
		}elseif($get_woocommerce_currency == 'USD') {
			$order->order_total = number_format($order->order_total*$curs_usd, 2, '.', '');
			$_PURSE_wm = "<input type='hidden' name='LMI_PAYEE_PURSE' value='{$this->wmpurse_uah}'>";
		}
	if ( ! $this->form_submission_method ) {
		if ( version_compare( WOOCOMMERCE_VERSION, '2.1.0', '<' ) ) {
		global $woocommerce;
		$woocommerce->add_inline_js('jQuery("body #frm_payment_method").parent().find("p").remove();jQuery("body #frm_payment_method").parent().block({message: "'.__('Thanks for your order. We are redirecting you to handle the payment of the order c using Webmoney.', 'loc-saphali-wm').'",overlayCSS:{background: "#fff",opacity: 0.8},css: {padding:20,textAlign:"center",color:"#555",border:"3px solid #aaa",backgroundColor:"#fff",cursor:"wait",lineHeight:"32px"}});jQuery("form#frm_payment_method input.button[type=\'submit\']").click();');
		} else wc_enqueue_js('jQuery("body #frm_payment_method").parent().find("p").remove();jQuery("body #frm_payment_method").parent().block({message: "'.__('Thanks for your order. We are redirecting you to handle the payment of the order c using Webmoney.', 'loc-saphali-wm').'",overlayCSS:{background: "#fff",opacity: 0.8},css: {padding:20,textAlign:"center",color:"#555",border:"3px solid #aaa",backgroundColor:"#fff",cursor:"wait",lineHeight:"32px"}});jQuery("form#frm_payment_method input.button[type=\'submit\']").click();');
		}
		if( $this->lang == 'no') $this->wpApiUrl = $this->wpApiUrl_en;
		return "<form  name='frm_payment_method' id='frm_payment_method'  action='{$this->wpApiUrl}' method='post'>
  <input type='hidden' name='LMI_PAYMENT_AMOUNT' value='".number_format($order->order_total, 2, '.', '')."'>
  <input type='hidden' name='LMI_PAYMENT_DESC' value='".$descRIPTION."'>
  <input type='hidden' name='LMI_PAYMENT_DESC_BASE64' value='".base64_encode ($descRIPTION)."'>
  <input type='hidden' name='LMI_PAYMENT_NO' value='".$order_id."'>
  ".$_PURSE_wm."
  <input type='hidden' name='LMI_SIM_MODE' value='{$this->wpMode}'>
  <input type='hidden' name='LMI_RESULT_URL' value='{$this->wpresultUrl}'>
  <input type='hidden' name='LMI_SUCCESS_URL' value='{$this->wpsuccessUrl}'>
  <input type='hidden' name='LMI_SUCCESS_METHOD' value='1'>
  <input type='hidden' name='LMI_FAIL_URL' value='{$this->wpfailUrl}'>
  <input type='hidden' name='LMI_FAIL_METHOD' value='1'>
  <input type='hidden' name='FIELD_1' value=''>
  <input type='hidden' name='FIELD_2' value='".$currensy_simb."'>
  <input type='hidden' name='RND' value='$rnd'>".
		  '<input type="submit" class="button-alt button" id="submit_dibs_payment_form" value="'.__('Pay', 'woocommerce').'" style="float: left; margin: 0px 23px 0px 0px; padding: 3px 8px 5px;" />'."
		</form>
		".'
		 <a class="button cancel" href="'.$order->get_cancel_order_url().'">'.__('Cancel order &amp; restore cart', 'woocommerce').'</a>';
	}
	
	function process_payment( $order_id ) {
		
		$order = new WC_Order( $order_id );
		
		return array(
			'result' => 'success',
			'redirect'	=> add_query_arg('order', $order->id, add_query_arg('key', $order->order_key, get_permalink(woocommerce_get_page_id('pay'))))
		);
		
	}
	function _receipt_page ($result) {
		if ( $result['result'] == 'success' ) {
			if(isset($result['new_redirect']) ) {
			var_dump($result['new_redirect']);
				if ( is_ajax() ) {
					
				} else {
					header("Location: " . $result['new_redirect']);exit;
				}
			}
		}
		return $result;
	}
}
?>