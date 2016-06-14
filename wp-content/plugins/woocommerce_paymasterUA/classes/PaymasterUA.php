<?php
class PaymasterUA extends WC_Payment_Gateway{
	public $id;
	public $paymaster_merchant;
	public $action_url;
	public $icon;
	public $has_fields;
	public $method_title;
	public $method_description;
	public $title;
	public $plugin_folder_name;
	
	public function __construct(){
		$this -> id = 'paymasterua';
		$this -> action_url = 'https://lmi.paymaster.ua';
		$this -> plugin_folder_name = 'woocommerce_paymasterUA';
		$this -> icon = plugins_url($this -> plugin_folder_name . '/img/logo.png');
		$this -> has_fields = false;
		$this -> method_title = 'Paymaster[UA]';
		$this -> method_description = '<strong>Оплата только в гривнах.</strong>';
		$this -> init_form_fields();
		$this -> init_settings();
		$this -> title = $this->get_option('title');
		$this -> paymaster_merchant = $this->get_option('paymaster_merchant');
		add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
		add_action('woocommerce_api_wc_' . $this->id, array($this, 'check_ipn_response'));
		if(!$this->valid_currency()){$this->enabled = false;} // отключаем систему оплаты если валюта не корректна
		add_action('woocommerce_receipt_' . $this->id, array($this, 'generate_form')); //форма оплаты на сайте
	}
	
	// страница настроек в админке
	public function init_form_fields(){
		$this -> form_fields = array(
			'enabled' => array(
				'title' => __('', 'woocommerce'),
				'type' => 'checkbox',
				'label' => __('Вкл./Выкл.', 'woocommerce'),
				'default' => 'yes'
			),
			'statusok' => array(
				'title' => __('', 'woocommerce'),
				'type' => 'checkbox',
				'label' => __('Установка статуса "выполнен" при подтверждении оплаты от Paymaster', 'woocommerce'),
				'default' => 'no'
			),
			'title' => array(
				'title' => __('Заголовок', 'woocommerce'),
				'type' => 'text',
				'description' => __('Заголовок который видит пользователь', 'woocommerce'),
				'default' => 'Paymaster[UA]'
			),
			'description' => array(
				'title' => __('Описание', 'woocommerce'),
				'type' => 'textarea',
				'default' => 'Платежные инструменты. Оплата в гривнах.'
			),
			'paymaster_merchant' => array(
				'title' => __('Идентификатор магазина', 'woocommerce'),
				'type' => 'text',
				'description' => __('Идентификатор вашего магазина. Узнать его можно на странице "Мои магазины и проекты" в личном кабинете Paymaster в колонке MERCHANT_ID.', 'woocommerce'),
				'default' => ''
			),
			'secret_key' => array(
				'title' => __('Секретный ключ', 'woocommerce'),
				'type' => 'text',
				'description' => __('Секретное слово, должно совпадать с аналогичной настройкой в кабинете Paymaster.', 'woocommerce'),
				'default' => ''
			),
			'hashtype' => array(
				'title' => __( 'Метод формирования контрольной подписи', 'woocommerce' ),
				'type'        => 'select',
				'description'	=>  __( 'Способ формирования контрольной подписи (рекомендуется SHA256), должно совпадать с аналогичной настройкой в кабинете Paymaster', 'woocommerce' ),
				'default'	=> 'SHA256',
				'desc_tip'    => true,
				'options'     => array(
					'MD5' => __( 'MD5', 'woocommerce' ),
					'SHA1' => __( 'SHA1', 'woocommerce' ),
					'SHA256' => __( 'SHA256', 'woocommerce' ),
				)
			)
			
		);
	}
	
	public function admin_options(){
			if($this -> valid_currency()){
				$logo_url = plugins_url($this -> plugin_folder_name . '/img/paymaster_logo_green.gif');
				echo 
				"<img src=\"".get_site_url()."/wp-content/plugins/woocommerce_paymasterUA/img/paymaster_logo_green.gif\" /><p>В личном кабинете Paymaster укажите следующие параметры:</p>
				<ul>
				<li>URL страницы результата оплаты: <code>".get_bloginfo("url")."/?wc-api=wc_paymasterua&paymasterua=result</code></li>
				</ul>
				Метод отсылки данных для всех строк: <code>POST</code><hr>";
				$this -> generate_settings_html();
			}
			else{echo '<div class="inline error below-h2"><p>Валюта вашего магазина не поддерживается платежной системой</p></div>';}
	}
	
	//процесс оплаты (переход на страницу оплаты)
	public function process_payment($order_id){
		$order = new WC_Order($order_id);
		return array(
			'result' => 'success',
			'redirect'	=> add_query_arg('order', $order->id, add_query_arg('key', $order->order_key, get_permalink(woocommerce_get_page_id('pay'))))
		);
	}
	
	// обратные запросы от paymaster
	public function check_ipn_response(){
		global $woocommerce;
		@ob_clean();
		if(isset($_GET['paymasterua']) && $_GET['paymasterua'] === 'result' && !empty($_POST['LMI_PAYMENT_NO'])){
			$_POST = stripslashes_deep($_POST);
			$order_id = $_POST['LMI_PAYMENT_NO'];
			$order = new WC_Order($order_id);
			
			// предзапрос (проверка платежа со стороны paymaster)
			if(isset($_POST['LMI_PREREQUEST'])){
				if($_POST['LMI_MERCHANT_ID'] == $this->paymaster_merchant && $_POST['LMI_PAYMENT_AMOUNT'] == $order->order_total){
					//$order->update_status('on-hold');
					$woocommerce->cart->empty_cart();
					ob_clean();
					echo 'YES';
					}
				else{
					$order->update_status('failed');
					ob_clean();
					echo 'NO';
				}
				exit;
			}
			
			// форма оповещения о платеже
			if(! isset($_POST['LMI_PREREQUEST']) && isset($_POST['LMI_HASH'])){
				$hash = $this ->	create_hash_result($_POST['LMI_MERCHANT_ID'],$_POST['LMI_PAYMENT_NO'],$_POST['LMI_SYS_PAYMENT_ID'],$_POST['LMI_SYS_PAYMENT_DATE'],$_POST['LMI_PAYMENT_AMOUNT'],$_POST['LMI_PAID_AMOUNT'],$_POST['LMI_PAYMENT_SYSTEM'],$_POST['LMI_MODE'], $this->get_option('secret_key'),$this->get_option('hashtype'));
				if($_POST['LMI_MERCHANT_ID'] == $this->get_option('paymaster_merchant') && $_POST['LMI_PAYMENT_AMOUNT'] == $order->order_total && $_POST['LMI_HASH'] == $hash){
					$woocommerce->cart->empty_cart();
					$order->reduce_order_stock(); // уменьшаем количество товаров
					if($this->get_option('statusok') !== 'no'){$order->update_status('completed');} // ставим статус выполнен
				}
			}
		}
	}
	
	// проверяем использование гривен
	public function valid_currency(){
		return (get_option('woocommerce_currency') === 'UAH') ? true : false;
	}
	
	// вывод формы оплаты
	public function generate_form($order_id){
		global $woocommerce;
		$htm = '<p>'.__('Спасибо за Ваш заказ, пожалуйста, нажмите кнопку ниже, чтобы оплатить.', 'woocommerce').'</p>';
		$order = new WC_Order($order_id);
		$out_summ = number_format($order->order_total, 2, '.', '');
		$hash = $this ->	create_hash($this->paymaster_merchant, $order_id, $order->order_total, $this->get_option('secret_key'), $this->get_option('hashtype'));
		$args = array(
			'LMI_MERCHANT_ID' => $this->paymaster_merchant,
			'LMI_PAYMENT_AMOUNT' => $out_summ,
			'LMI_PAYMENT_NO' => $order_id,
			'LMI_HASH' => $hash
		);
		if(sizeof($order -> get_items()) > 0){
			foreach($order -> get_items() as $item){ 
				$descar[] = $item['name'].' '.sprintf( 'x %s', $item['qty'] );
			} 
			$desc = implode(", ", $descar);
		}
		else{$desc = '';}
		if(count($order->get_used_coupons())>0){
			$desc = 'Купоны: "'.implode("\", \"", $order->get_used_coupons()).'" (общая сумма скидки: '.$order->get_total_discount().'). '.$desc;
		}
			
		if($order->customer_message!='') $desc .= '. Сообщение: '.$order->customer_message;
		$args['LMI_PAYMENT_DESC'] = $desc;
		$args_array = array();
		foreach ($args as $key => $value){
			$args_array[] = '<input type="hidden" name="'.esc_attr($key).'" value="'.esc_attr($value).'" />';
		}
		$htm .= '<form action="'.esc_url($this -> action_url).'" method="POST" id="paymaster_form">'."\n".
			implode("\n", $args_array).
			'<span id="ppform">
			<input type="submit" class="button btn btn-default" id="submit_paymaster_payment_form" value="'.__('Оплатить', 'woocommerce').'" style="display:inline-block" /> <a class="button btn alt btn-black" href="'.$order->get_cancel_order_url().'">'.__('Отказаться от оплаты и вернуться в корзину', 'woocommerce').'</a>
			</span>'."\n".
			'</form>
			';
		echo $htm;
	}
	
	// создание подписи для проверки платежа
	public function create_hash($merchant_id, $order_id, $order_price, $sekret_key, $algo){
		$str = $merchant_id.$order_id.$order_price.$sekret_key;
		return strtoupper(hash($algo, $str));
	}
	
	// создание подписи для проверки уведомления о оплате
	public function create_hash_result($merchant_id, $paym_no, $sys_paym_id, $sys_paym_date, $paym_amount, $paid_amount, $paym_system, $lmi_mode, $sekret_key, $algo){
		$str = $merchant_id.$paym_no.$sys_paym_id.$sys_paym_date.$paym_amount.$paid_amount.$paym_system.$lmi_mode.$sekret_key;
		return strtoupper(hash($algo, $str));
	}
}
?>