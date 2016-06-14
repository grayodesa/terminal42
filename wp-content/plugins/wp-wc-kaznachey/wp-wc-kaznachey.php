<?php
/*
Plugin Name: kaznachey for WooCommerce
Plugin URI: http://www.kaznachey.ua
Description: Кредитная карта Visa/MC, Webmoney, Liqpay, Qiwi... (www.kaznachey.ua)
Version: 1.3
Author: §wInG
Author email : info@kaznachey.ua
*/

add_action("init", "wp_wc_kaznachey_init");
function wp_wc_kaznachey_init(){
    load_plugin_textdomain("wp_wc_kaznachey", false, basename(dirname(__FILE__)));
}

add_action( 'plugins_loaded', 'init_wc_kaznachey_Payment_Gateway' );
function init_wc_kaznachey_Payment_Gateway() {
	if (!class_exists('WC_Payment_Gateway'))
		return; // if the WC payment gateway class is not available, do nothing
    /**
     * Класс для работы с методом оплаты kaznachey для WooCommerce.
     * Смотри также наследуемый абстрактный класс WC_Payment_Gateway (есть комменты перед заготовками методов)
     */
    class WC_kaznachey_Payment_Gateway extends WC_Payment_Gateway{
        public function __construct(){
            $this->id = 'kaznachey';
			
			$this->urlGetMerchantInfo = 'http://payment.kaznachey.net/api/PaymentInterface/CreatePayment';
			$this->urlGetClientMerchantInfo = 'http://payment.kaznachey.net/api/PaymentInterface/GetMerchatInformation';

            $this->has_fields = false;
            $this->method_title = 'kaznachey';
			$cc_types = $this->GetMerchnatInfo();
			if(isset($cc_types["PaySystems"])){
				$box = '<br><br><label for="cc_types">Выберите способ оплаты</label><select name="cc_types" id="cc_types">';
				$term_url = $this->GetTermToUse();
					foreach ($cc_types["PaySystems"] as $paysystem)
					{
						//$PaySystems[$paysystem['Id']] = $paysystem['PaySystemName'];
						$box .= "<option value='$paysystem[Id]'>$paysystem[PaySystemName]</option>";
					}
				$box .= '</select><br><input type="checkbox" checked="checked" value="1" name="cc_agreed" id="cc_agreed"><label for="cc_agreed"><a href="'.$term_url.'" target="_blank">Согласен с условиями использования</a></label>';
				
				$box .= "<script type=\"text/javascript\">
				(function(){ 
				var cc_a = jQuery('#cc_agreed');
					 cc_a.on('click', function(){
						if(cc_a.is(':checked')){	
							jQuery('.custom_gateway').find('.error').text('');
						}else{
							cc_a.next().after('<span class=\"error\">Примите условие!</span>');
						}
					 });
					jQuery('body').on('click', function() {
						 document.cookie='cc_types='+jQuery('#cc_types').val();
					});	
				})(); 
				</script> ";
			}
			
			$this->method_description = __( 'Payment by <a href="http://www.kaznachey.ua/" title="kaznachey is a full service of your website in the field of organization and receiving electronic payments." target="_blank">kaznachey</a>.', 'wp_wc_kaznachey' );

            $this->init_form_fields();
            $this->init_settings();

            $this->title = $this->get_option('title');
            $this->description = $this->get_option('description').$box;
            $this->merchantGuid = $this->get_option('merchantGuid');
            $this->merchnatSecretKey = $this->get_option('merchnatSecretKey');
            $this->merchnatCurrency = $this->get_option('merchnatCurrency');
            $this->pay_mode = $this->get_option('pay_mode');
            $this->icon_type = $this->get_option('icon_type');
            if($this->icon_type)
                $this->icon = apply_filters('woocommerce_kaznachey_icon', plugin_dir_url(__FILE__) . 'kaznachey_' . $this->icon_type . '.png');


            // хук для сохранения опций, доступных в настройках метода оплаты в админке (опр. в ф-ции init_form_fields)
            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
            // хук для отрисовки формы перед переходом на мерчант (см. ф-цию receipt_page)
            add_action( 'woocommerce_receipt_kaznachey', array( $this, 'receipt_page' ) );
            // хук для обработки Result URL
            add_action( 'woocommerce_api_wc_kaznachey_payment_gateway', array( $this, 'kaznachey_result' ) );
        }

        /**
         * Метод определяет, какие поля будут доступны в настройках метода оплаты в админке.
         * Описание API см. здесь - http://docs.woothemes.com/document/settings-api/
         * @return string|void
         */
        public function init_form_fields(){
            $this->form_fields = array(
                'enabled' => array(
                    'title' => __( 'Enable/Disable', 'wp_wc_kaznachey' ),
                    'type' => 'checkbox',
                    'label' => __( 'Enable', 'wp_wc_kaznachey' ),
                    'default' => 'yes'
                ),
                'title' => array(
                    'title' => __( 'Title', 'wp_wc_kaznachey' ),
                    'type' => 'text',
                    'description' => __( 'This controls the title which the user sees during checkout.', 'wp_wc_kaznachey' ),
                    'default' => 'kaznachey',
                    'desc_tip' => true,
                ),
                'description' => array(
                    'title' => __( 'Description', 'wp_wc_kaznachey' ),
                    'type' => 'textarea',
                    'description' => __( 'This controls the description which the user sees during checkout.', 'wp_wc_kaznachey' ),
                    'default' => __( 'Кредитная карта Visa/MC, Webmoney, Liqpay, Qiwi... (www.kaznachey.ua)' ),
                ),
                'merchantGuid' => array(
                    'title' => __( 'Merchant ID', 'wp_wc_kaznachey' ),
                    'type' => 'text',
                    'description' => __( 'Unique id of the store in kaznachey system. You can find it in your <a href="http://kaznachey.ua" target="_blank">shop control panel</a>.', 'wp_wc_kaznachey' ),
                ),
                'merchnatSecretKey' => array(
                    'title' => __( 'Secret key', 'wp_wc_kaznachey' ),
                    'type' => 'text',
                    'description' => __( 'Custom character set is used to sign messages are forwarded.', 'wp_wc_kaznachey' ),
                ), 
				'merchnatCurrency' => array(
                    'title' => __( 'Currency', 'wp_wc_kaznachey' ),
                    'type' => 'text',
					'default' => '1',
                    'description' => __( 'Currency to UAH - default 1', 'wp_wc_kaznachey' ),
                ),

                'icon_type' => array(
                    'title' => __( 'Image', 'wp_wc_kaznachey' ),
                    'description' =>  __( '(optional) kaznachey icon which the user sees during checkout on payment selection page.', 'wp_wc_kaznachey' ),
                    'type' => 'select',
                    'options' => array(
                        '' => __( "Don't use", 'wp_wc_kaznachey' ),
                        'transp' => __( 'Transparent', 'wp_wc_kaznachey' ),
                    ),
                ),
            );
        }

        /**
         * Метод обрабатывает событие "Размещения заказа".
         * Переводит покупателя на страницу, где формируется форма для перехода на мерчант.
         * @param $order_id номер заказа
         * @return array|void
         */
        public function process_payment( $order_id ){
            global $woocommerce;
            $order = new WC_Order( $order_id );
            return array(
                'result' => 'success',
                'redirect'	=> add_query_arg('order', $order->id, add_query_arg('key', $order->order_key, get_permalink(woocommerce_get_page_id('pay'))))
            );
        }

        public function receipt_page($order_id){
            global $woocommerce;
            $order = new WC_Order( $order_id );
            $lang = get_locale();
            switch($lang){
                case 'en_EN':
                    $lang = 'en';
                    break;
                case 'ru_RU':
                    $lang = 'ru';
                    break;
                default:
                    $lang = 'ru';
                    break;
            }
            $amount = number_format($order->order_total*$this->merchnatCurrency, 2, '.', '');
            $currency = get_woocommerce_currency();
            $available_currencies = array('BYR', 'EUR', 'RUB', 'UAH', 'USD', 'UZS');
            if($currency == 'RUR')
                $currency = 'RUB';
            if(!in_array($currency, $available_currencies))
                $currency = 'USD';
            $desc = 'Оплата заказа №' . $order_id;
            $success_url = $this->get_return_url($order).'&status=success';
            $result_url = str_replace( 'https:', 'https:', add_query_arg( 'wc-api', __CLASS__, home_url( '/' ) ) ).'&status=done';

            //$fail_url = $order->get_cancel_order_url();

	$i = 0;
	$amount2 = 0;
	$product_count =  0;
	
	$products_items = $order->get_items();	
  	foreach ($products_items as $key=>$pr_item)
	{
		$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($pr_item['product_id']), 'large' );
		$products[$i]['ImageUrl'] = (isset($thumb[0]))?$thumb[0]:'';
		$products[$i]['ProductItemsNum'] = number_format($pr_item['qty'], 2, '.', '');
		$products[$i]['ProductName'] = $pr_item['name'];
		$products[$i]['ProductPrice'] = number_format(($pr_item['line_total']*$this->merchnatCurrency)/$pr_item['qty'], 2, '.', '');
		$products[$i]['ProductId'] = $pr_item['product_id'];
		$amount2 += $pr_item['line_total']*$this->merchnatCurrency;
		$product_count += $pr_item['qty'];
		$i++;
	}
	
	if($amount != $amount2){
		$tt = $amount - $amount2; 
		$products[$i]['ProductItemsNum'] = '1.00';
		$products[$i]['ProductName'] = 'Delivery or discount';
		$products[$i]['ProductPrice'] = number_format(($tt*$this->merchnatCurrency), 2, '.', '');
		$products[$i]['ProductId'] = '00001'; 
		$pr_c = '1.00';
		$amount2  = number_format(($amount2*$this->merchnatCurrency) + ($tt*$this->merchnatCurrency), 2, '.', '');
	}
	
	$user_id = ($order->user_id < 1)?$order->user_id:1;
	$amounts = $order->order_total*$this->merchnatCurrency;

	$signature_u = md5(md5(
		$this->merchantGuid.
		$this->merchnatSecretKey.
		"$amounts".
		$order_id
	));

    $paymentDetails = Array(
       "MerchantInternalPaymentId"=>"$order_id",
       "MerchantInternalUserId"=>$user_id,
       "EMail"=>$order->billing_email,
       "PhoneNumber"=>$order->billing_phone,
       "CustomMerchantInfo"=>"$signature_u",
       "StatusUrl"=>"$result_url",
       "ReturnUrl"=>"$success_url",
       "BuyerCountry"=>$order->billing_country,
       "BuyerFirstname"=>$order->billing_first_name,
       "BuyerPatronymic"=>$order->billing_company,
       "BuyerLastname"=>$order->billing_last_name,
       "BuyerStreet"=>$order->billing_address_1,
       "BuyerZone"=>"",
       "BuyerZip"=>$order->billing_postcode,
       "BuyerCity"=>$order->billing_city,

       "DeliveryFirstname"=>$order->shipping_first_name,
       "DeliveryLastname"=>$order->shipping_last_name,
       "DeliveryZip"=>$order->shipping_postcode, 
       "DeliveryCountry"=>$order->shipping_country,
       "DeliveryPatronymic"=>$order->shipping_company,
       "DeliveryStreet"=>$order->shipping_address_1,
       "DeliveryCity"=>$order->shipping_city,
       "DeliveryZone"=>"",
    );

	$product_count = isset($pr_c) ? $product_count + $pr_c : $product_count;
	$product_count = number_format($product_count, 2, '.', '');	
	$amount2 = number_format($amount2, 2, '.', '');	
		
	$selectedPaySystemId = $_COOKIE['cc_types'] ? $_COOKIE['cc_types'] : $this->GetMerchnatInfo(false, true);
		
	$signature = md5(
		$this->merchantGuid.
		"$amount2".
		"$product_count".
		$paymentDetails["MerchantInternalUserId"].
		$paymentDetails["MerchantInternalPaymentId"].
		$selectedPaySystemId.
		$this->merchnatSecretKey
	);
	
	$request = Array(
        "SelectedPaySystemId"=>$selectedPaySystemId,
        "Products"=>$products,
        "PaymentDetails"=>$paymentDetails,
        "Signature"=>$signature,
        "MerchantGuid"=>$this->merchantGuid,
		"Currency"=> $currency
    );
	$res = $this->sendRequestKaznachey($this->urlGetMerchantInfo, json_encode($request));
	
	$result = json_decode($res,true);

	if($result['ErrorCode'] != 0){
		wp_redirect( home_url() ); exit;
	}
	
		echo(base64_decode($result["ExternalForm"]));
		exit();
	
    }

        function kaznachey_result(){
			global $woocommerce, $wpdb, $wpsc_cart, $wpsc_coupons;
			//$woocommerce->logger()->add('kaznachey', 'returned');
			switch ($_GET['status'])
			{
				case 'done':
					$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : file_get_contents('php://input');

					$hrpd = json_decode($HTTP_RAW_POST_DATA);

					$order_id = intval($hrpd->MerchantInternalPaymentId); 
					if(!$order_id){wp_redirect( home_url() ); exit; }
					$order = new WC_Order($order_id);
					
					$merchnatSecretKey = $this->get_option('merchnatSecretKey');
					$merchantGuid = $this->get_option('merchantGuid');

					$amounts = $order->order_total*$this->merchnatCurrency;
					
					$signature_u = md5(md5(
						$merchantGuid.
						$merchnatSecretKey.
						"$amounts".
						$order_id
					));
					
					if(isset($hrpd->MerchantInternalPaymentId)){
						if($hrpd->ErrorCode == 0)
						{
							if($hrpd->CustomMerchantInfo == $signature_u)
							{
								$order->payment_complete();
								$order->add_order_note("Заказ оплачен. Платеж через www.kaznachey.ua");
								//$woocommerce->logger()->add('kaznachey', 'OK');
							}
						}
					}
					
					wp_redirect( home_url() ); exit;
					
				break;		
				
				case 'success':
					wp_redirect( home_url() ); exit;
				break;
			}
		}
		
		function sendRequestKaznachey($url,$data)
		{
			$curl =curl_init();
			if (!$curl)
				return false;

			curl_setopt($curl, CURLOPT_URL,$url );
			curl_setopt($curl, CURLOPT_POST,true);
			curl_setopt($curl, CURLOPT_HTTPHEADER, 
					array("Expect: ","Content-Type: application/json; charset=UTF-8",'Content-Length: ' 
						. strlen($data)));
			curl_setopt($curl, CURLOPT_POSTFIELDS,$data);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER,True);
			$res =  curl_exec($curl);
			curl_close($curl);

			return $res;
		}

		function GetMerchnatInfo($id = false, $first = false)
		{
			$merchantGuid = $this->get_option('merchantGuid');
			$merchnatSecretKey = $this->get_option('merchnatSecretKey');

			$requestMerchantInfo = Array(
				"MerchantGuid"=>$merchantGuid,
				"Signature"=>md5($merchantGuid.$merchnatSecretKey)
			);

			$resMerchantInfo = json_decode($this->sendRequestKaznachey($this->urlGetClientMerchantInfo , json_encode($requestMerchantInfo)),true); 
			
			if(isset($first)){
				return $resMerchantInfo["PaySystems"][0]['Id'];
			}elseif(isset($id))
			{
				foreach ($resMerchantInfo["PaySystems"] as $key=>$paysystem)
				{
					if($paysystem['Id'] == $id)
					{
						return $paysystem;
					}
				}
			}else{
				return $resMerchantInfo;
			}
		}

		function GetTermToUse()
		{
			$merchantGuid = $this->get_option('merchantGuid');
			$merchnatSecretKey = $this->get_option('merchnatSecretKey');

			$requestMerchantInfo = Array(
				"MerchantGuid"=>$merchantGuid,
				"Signature"=>md5($merchantGuid.$merchnatSecretKey)
			);

			$resMerchantInfo = json_decode($this->sendRequestKaznachey($this->urlGetClientMerchantInfo , json_encode($requestMerchantInfo)),true); 

			return $resMerchantInfo["TermToUse"];

		}
    }
}

add_filter( 'woocommerce_payment_gateways', 'add_wc_kaznachey_Payment_Gateway' );
function add_wc_kaznachey_Payment_Gateway( $methods ){
    $methods[] = 'WC_kaznachey_Payment_Gateway';
    return $methods;
}