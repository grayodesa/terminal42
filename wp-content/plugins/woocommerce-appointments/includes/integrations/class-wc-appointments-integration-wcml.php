<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WooCommerce TM Extra Product Options integration class.
 */
class WC_Appointments_Integration_WCML {

    public $tp;

    function __construct(){
		
        add_action( 'wcml_before_sync_product_data', array( $this, 'sync_appointments' ), 10, 3 );
        add_action( 'wcml_before_sync_product', array( $this, 'sync_appointment_data' ), 10, 2 );

        add_filter( 'wcml_cart_contents_not_changed', array( $this, 'filter_bundled_product_in_cart_contents' ), 10, 3 );

        add_action( 'woocommerce_appointments_after_create_appointment_page', array( $this, 'appointment_currency_dropdown' ) );
        add_action( 'init', array( $this, 'set_appointment_currency') );
        add_action( 'wp_ajax_wcml_appointment_set_currency', array( $this, 'set_appointment_currency_ajax' ) );
        add_action( 'woocommerce_appointments_create_appointment_page_add_order_item', array( $this, 'set_order_currency_on_create_appointment_page' ) );
        add_filter( 'woocommerce_currency_symbol', array( $this, 'filter_appointment_currency_symbol' ) );
        add_filter( 'get_appointment_products_args', array( $this, 'filter_get_appointment_products_args' ) );
        add_filter( 'wcml_filter_currency_position', array( $this, 'create_appointment_page_client_currency' ) );
        add_filter( 'wcml_client_currency', array( $this, 'create_appointment_page_client_currency' ) );

		add_filter( 'wcml_product_content_fields', array( $this, 'product_content_fields'), 10, 2 );
        add_filter( 'wcml_product_content_fields_label', array( $this, 'product_content_fields_label'), 10, 2 );
        add_filter( 'wcml_check_is_single', array( $this, 'show_custom_blocks_for_staff'), 10, 3 );
        add_filter( 'wcml_product_content_label', array( $this, 'product_content_staff_label' ), 10, 2 );
        add_action( 'wcml_update_extra_fields', array( $this, 'wcml_products_tab_sync_staff'), 10, 3 );
		
        add_action( 'woocommerce_new_appointment', array( $this, 'duplicate_appointment_for_translations') );

        $appointments_statuses = array( 'unpaid', 'pending-confirmation', 'confirmed', 'paid', 'cancelled', 'complete', 'in-cart', 'was-in-cart' );
        foreach ( $appointments_statuses as $status ) {
            add_action('woocommerce_appointment_' . $status, array( $this, 'update_status_for_translations' ) );
        }

        add_filter( 'parse_query', array( $this, 'appointment_filters_query' ) );
        add_filter( 'woocommerce_appointments_in_date_range_query', array( $this, 'appointments_in_date_range_query' ));
        add_action( 'before_delete_post', array( $this, 'delete_appointments' ) );
        add_action( 'wp_trash_post', array( $this, 'trash_appointments' ) );

        if ( is_admin() ) {
            $this->tp = new WPML_Element_Translation_Package;
        }

        $this->clear_transient_fields();

    }

    // sync existing product appointments for translations
    function sync_appointments( $original_product_id, $product_id, $lang ){
        global $wpdb;

        $all_appointments_for_product =  WC_Appointments_Controller::get_appointments_for_product( $original_product_id , array( 'in-cart', 'unpaid', 'confirmed', 'paid' ) );

        foreach( $all_appointments_for_product as $appointment ){
            $check_if_exists = $wpdb->get_row( $wpdb->prepare( "SELECT pm3.* FROM {$wpdb->postmeta} AS pm1
                                            LEFT JOIN {$wpdb->postmeta} AS pm2 ON pm1.post_id = pm2.post_id
                                            LEFT JOIN {$wpdb->postmeta} AS pm3 ON pm1.post_id = pm3.post_id
                                            WHERE pm1.meta_key = '_appointment_duplicate_of' AND pm1.meta_value = %s AND pm2.meta_key = '_language_code' AND pm2.meta_value = %s AND pm3.meta_key = '_appointment_product_id'"
                , $appointment->id, $lang ) );

            if ( is_null( $check_if_exists ) ){
                $this->duplicate_appointment_for_translations( $appointment->id, $lang );
            } elseif ( $check_if_exists->meta_value === '' ) {
                update_post_meta( $check_if_exists->post_id, '_appointment_product_id', $this->get_translated_appointment_product_id( $appointment->id, $lang ) );
				update_post_meta( $check_if_exists->post_id, '_appointment_staff_id', $this->get_translated_appointment_staff_id( $appointment->id, $lang ) );
            }
        }

    }

    function sync_appointment_data( $original_product_id, $current_product_id ){

        if( has_term( 'appointment', 'product_type', $original_product_id ) ){
            global $wpdb, $sitepress, $pagenow, $iclTranslationManagement;

            // get language code
            $language_details = $sitepress->get_element_language_details( $original_product_id, 'post_product' );
            if ( $pagenow == 'admin.php' && empty( $language_details ) ) {
                //translation editor support: sidestep icl_translations_cache
                $language_details = $wpdb->get_row( $wpdb->prepare( "SELECT element_id, trid, language_code, source_language_code FROM {$wpdb->prefix}icl_translations WHERE element_id = %d AND element_type = 'post_product'", $original_product_id ) );
            }
            if ( empty( $language_details ) ) {
                return;
            }

            // pick posts to sync
            $posts = array();
            $translations = $sitepress->get_element_translations( $language_details->trid, 'post_product' );
            foreach ( $translations as $translation ) {

                if ( !$translation->original ) {
                    $posts[ $translation->element_id ] = $translation;
                }
            }
			
			foreach ( $posts as $post_id => $translation ) {

                $trn_lang = $sitepress->get_language_for_element( $post_id, 'post_product' );

                //sync_staff
                $this->sync_staff( $original_product_id, $post_id, $trn_lang );
            }

        }

    }

    function filter_bundled_product_in_cart_contents( $cart_item, $key, $current_language ){

        if( $cart_item[ 'data' ] instanceof WC_Product_Appointment && isset( $cart_item[ 'appointment' ] ) ){
            global $woocommerce_wpml;

            $current_id = apply_filters( 'translate_object_id', $cart_item[ 'data' ]->id, 'product', true, $current_language );
            $cart_product_id = $cart_item['data']->id;

            if( $current_id != $cart_product_id ) {

                $cart_item['data'] = new WC_Product_Appointment( $current_id );

            }

            if( $woocommerce_wpml->settings['enable_multi_currency'] == WCML_MULTI_CURRENCIES_INDEPENDENT || $current_id != $cart_product_id ){

                $appointment_info = array(
                    'wc_appointments_field_start_date_year' => $cart_item[ 'appointment' ][ '_year' ],
                    'wc_appointments_field_start_date_month' => $cart_item[ 'appointment' ][ '_month' ],
                    'wc_appointments_field_start_date_day' => $cart_item[ 'appointment' ][ '_day' ],
                    'add-to-cart' => $current_id,
                );

                if( isset( $cart_item[ 'appointment' ][ '_staff_id' ]  ) ){
                    $appointment_info[ 'wc_appointments_field_staff' ] = $cart_item[ 'appointment' ][ '_staff_id' ];
                }

                if( isset( $cart_item[ 'appointment' ][ '_duration' ]  ) ){
                    $appointment_info[ 'wc_appointments_field_duration' ] = $cart_item[ 'appointment' ][ '_duration' ];
                }

                if( isset( $cart_item[ 'appointment' ][ '_time' ]  ) ){
                    $appointment_info[ 'wc_appointments_field_start_date_time' ] = $cart_item[ 'appointment' ][ '_time' ];
                }

                $prod_qty = get_post_meta( $current_id, '_wc_appointment_qty', true );
                update_post_meta( $current_id, '_wc_appointment_qty', intval( $prod_qty + $cart_item[ 'appointment' ][ '_qty' ] ) );
                update_post_meta( $current_id, '_wc_appointment_qty', $prod_qty );
            }

        }

        return $cart_item;

    }

    function appointment_currency_dropdown(){
        global $woocommerce_wpml, $sitepress;

        if( $woocommerce_wpml->settings['enable_multi_currency'] == WCML_MULTI_CURRENCIES_INDEPENDENT ){
            $current_appointment_currency = $this->get_cookie_appointment_currency();

            $wc_currencies = get_woocommerce_currencies();
            $order_currencies = $woocommerce_wpml->multi_currency->get_orders_currencies();
            ?>
            <tr valign="top">
                <th scope="row"><?php _e( 'Appointment currency', 'woocommerce-appointments' ); ?></th>
                <td>
                    <select id="dropdown_appointment_currency">

                        <?php foreach($order_currencies as $currency => $count ): ?>

                            <option value="<?php echo $currency ?>" <?php echo $current_appointment_currency == $currency ? 'selected="selected"':''; ?>><?php echo $wc_currencies[$currency]; ?></option>

                        <?php endforeach; ?>

                    </select>
                </td>
            </tr>

            <?php

            $wcml_appointment_set_currency_nonce = wp_create_nonce( 'appointment_set_currency' );

            wc_enqueue_js( "

            jQuery(document).on('change', '#dropdown_appointment_currency', function(){
               jQuery.ajax({
                    url: ajaxurl,
                    type: 'post',
                    data: {
                        action: 'wcml_appointment_set_currency',
                        currency: jQuery('#dropdown_appointment_currency').val(),
                        wcml_nonce: '".$wcml_appointment_set_currency_nonce."'
                    },
                    success: function( response ){
                        if(typeof response.error !== 'undefined'){
                            alert(response.error);
                        }else{
                           window.location = window.location.href;
                        }
                    }
                })
            });
        ");

        }

    }

    function set_appointment_currency_ajax(){

        $nonce = filter_input( INPUT_POST, 'wcml_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        if(!$nonce || !wp_verify_nonce($nonce, 'appointment_set_currency')){
            echo json_encode(array('error' => __('Invalid nonce', 'woocommerce-appointments')));
            die();
        }

        $this->set_appointment_currency(filter_input( INPUT_POST, 'currency', FILTER_SANITIZE_FULL_SPECIAL_CHARS ));

        die();
    }

    function set_appointment_currency( $currency_code = false ){

        if( !isset( $_COOKIE [ '_wcml_appointment_currency' ]) && !headers_sent()) {
            global $woocommerce_wpml;

            $currency_code = get_woocommerce_currency();

            if ( $woocommerce_wpml->settings['enable_multi_currency'] == WCML_MULTI_CURRENCIES_INDEPENDENT ){
                $order_currencies = $woocommerce_wpml->multi_currency->get_orders_currencies();

                if (!isset($order_currencies[$currency_code])) {
                    foreach ($order_currencies as $currency_code => $count) {
                        $currency_code = $currency_code;
                        break;
                    }
                }
            }
        }

        if( $currency_code ){
            setcookie('_wcml_appointment_currency', $currency_code , time() + 86400, COOKIEPATH, COOKIE_DOMAIN);
        }

    }

    function get_cookie_appointment_currency(){

        if( isset( $_COOKIE [ '_wcml_appointment_currency' ] ) ){
            $currency = $_COOKIE[ '_wcml_appointment_currency' ];
        }else{
            $currency = get_woocommerce_currency();
        }

        return $currency;
    }

    function filter_appointment_currency_symbol( $currency ){
        global $pagenow;

        remove_filter( 'woocommerce_currency_symbol', array( $this, 'filter_appointment_currency_symbol' ) );
        if( isset( $_COOKIE [ '_wcml_appointment_currency' ] ) && $pagenow == 'edit.php' && isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'create_appointment' ){
            $currency = get_woocommerce_currency_symbol( $_COOKIE [ '_wcml_appointment_currency' ] );
        }
        add_filter( 'woocommerce_currency_symbol', array( $this, 'filter_appointment_currency_symbol' ) );

        return $currency;
    }

    function create_appointment_page_client_currency( $currency ){
        global $pagenow;

        if( wpml_is_ajax() && isset( $_POST[ 'form' ] ) ){
            parse_str( $_POST[ 'form' ], $posted );
        }

        if( ( $pagenow == 'edit.php' && isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'create_appointment' ) || ( isset( $posted[ '_wp_http_referer' ] ) && strpos( $posted[ '_wp_http_referer' ], 'page=create_appointment' ) !== false ) ){
            $currency = $this->get_cookie_appointment_currency();
        }

        return $currency;
    }

    function set_order_currency_on_create_appointment_page( $order_id ){
        global $sitepress;

        update_post_meta( $order_id, '_order_currency', $this->get_cookie_appointment_currency() );

        update_post_meta( $order_id, 'wpml_language', $sitepress->get_current_language() );

    }

    function filter_get_appointment_products_args( $args ){
        if( isset( $args['suppress_filters'] ) ){
            $args['suppress_filters'] = false;
        }
        return $args;
    }
	
	function product_content_fields( $fields, $product_id ){

         return  $fields;

    }

    function product_content_fields_label( $fields, $product_id ){

         return  $fields;

    }

    function show_custom_blocks_for_staff( $check, $product_id, $product_content ){
        if( in_array( $product_content, array( 'wc_appointment_staff' ) ) ){
            return false;
        }
        return $check;
    }

    function product_content_staff_label( $meta_key, $product_id ){
        if ($meta_key == '_wc_appointment_staff_label'){
            return __( 'Staff label', 'woocommerce-appointments' );
        }
        return $meta_key;
    }

    function wcml_products_tab_sync_staff( $tr_product_id, $data, $language ){
        global $wpdb, $woocommerce_wpml;

        //sync staff
        if( isset( $data[ 'wc_appointment_staff_'.$language ] ) ){

            $original_product_lang = $woocommerce_wpml->products->get_original_product_language( $tr_product_id );
            $original_product_id = apply_filters( 'translate_object_id', $tr_product_id, 'product', true, $original_product_lang );

            foreach( $data[ 'wc_appointment_staff_'.$language ][ 'id' ] as $key => $staff_id ){

                if( !$staff_id ){

                    $staff_id = apply_filters( 'translate_object_id', $data[ 'wc_appointment_staff_'.$language ][ 'orig_id' ][ $key ], 'appointable_staff', false, $language );

                    $orig_staff = $wpdb->get_row( $wpdb->prepare( "SELECT staff_id, sort_order FROM {$wpdb->prefix}wc_appointment_relationships WHERE staff_id = %d AND product_id = %d", $data[ 'wc_appointment_staff_'.$language ][ 'orig_id' ][ $key ], $original_product_id ), OBJECT );

                    if( is_null( $staff_id ) ){

                        if( $orig_staff ) {
                            $staff_id = $this->duplicate_staff($tr_product_id, $orig_staff, $language);
                        } else {
                            continue;
                        }

                    }else{
                        //update_relationship

                        $exist = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->prefix}wc_appointment_relationships WHERE staff_id = %d AND product_id = %d", $staff_id, $tr_product_id ) );

                        if( !$exist ){

                            $wpdb->insert(
                                $wpdb->prefix . 'wc_appointment_relationships',
                                array(
                                    'product_id' => $tr_product_id,
                                    'staff_id' => $staff_id,
                                    'sort_order' => $orig_staff->sort_order
                                )
                            );

                        }

                    }

                }

                $wpdb->update(
                    $wpdb->posts,
                    array(
                        'post_title' => $data[ 'wc_appointment_staff_'.$language ][ 'title' ][ $key ]
                    ),
                    array(
                        'ID' => $staff_id
                    )
                );

            }

            //sync staff data
            $this->sync_staff( $original_product_id, $tr_product_id, $language, false );

            add_filter( 'update_post_metadata', array( $this, 'update_wc_appointment_costs' ), 10, 5 );

        }

    }
	
	function sync_staff( $original_product_id, $trnsl_product_id, $lang_code, $duplicate = true ){
        global $wpdb;

        $orig_staff = $wpdb->get_results( $wpdb->prepare( "SELECT staff_id, sort_order FROM {$wpdb->prefix}wc_appointment_relationships WHERE product_id = %d", $original_product_id ) );

        $trnsl_product_staff = $wpdb->get_col( $wpdb->prepare( "SELECT staff_id FROM {$wpdb->prefix}wc_appointment_relationships WHERE product_id = %d", $trnsl_product_id ) );
		
		foreach ($trnsl_product_staff as $trnsl_product_staff) {

            $wpdb->delete(
                $wpdb->prefix . 'wc_appointment_relationships',
                array(
                    'product_id' => $trnsl_product_id,
                    'staff_id' => $trnsl_product_staff
                )
            );

            wp_delete_post( $trnsl_product_staff );

        }

        foreach ($orig_staff as $staff) {

            $trns_staff_id = apply_filters( 'translate_object_id', $staff->staff_id, 'appointable_staff', false, $lang_code );

            if ( !is_null( $trns_staff_id ) && in_array( $trns_staff_id, $trnsl_product_staff ) ) {

                if ( ( $key = array_search( $trns_staff_id, $trnsl_product_staff ) ) !== false ) {

                    unset($trnsl_product_staff[$key]);

                    $wpdb->update(
                        $wpdb->prefix . 'wc_appointment_relationships',
                        array(
                            'sort_order' => $staff->sort_order
                        ),
                        array(
                            'product_id' => $trnsl_product_id,
                            'staff_id' => $trns_staff_id
                        )
                    );

                    update_post_meta( $trns_staff_id, 'qty', get_post_meta( $staff->staff_id, 'qty', true ) );
                    update_post_meta( $trns_staff_id, '_wc_appointment_availability', get_post_meta( $staff->staff_id, '_wc_appointment_availability', true ) );

                }

            } else {

                if( $duplicate ){

                    $trns_staff_id = $this->duplicate_staff( $trnsl_product_id, $staff, $lang_code );

                }else{

                    continue;

                }

            }

        }

    }
	
	function duplicate_staff( $tr_product_id, $staff, $lang_code){
        global $sitepress, $wpdb, $iclTranslationManagement;

        $wpdb->insert(
            $wpdb->prefix . 'wc_appointment_relationships',
            array(
                'product_id' => $tr_product_id,
                'staff_id' => $staff->staff_id,
                'sort_order' => $staff->sort_order
            )
        );

        delete_post_meta( $trns_staff_id, '_icl_lang_duplicate_of' );

        return $trns_staff_id;
    }

    function duplicate_appointment_for_translations( $appointment_id, $lang = false ){
        global $sitepress;

        $appointment_object = get_post( $appointment_id );

        $appointment_data = array(
            'post_type'   => 'wc_appointment',
            'post_title'  => $appointment_object->post_title,
            'post_status' => $appointment_object->post_status,
            'ping_status' => 'closed',
            'post_parent' => $appointment_object->post_parent,
        );

        $active_languages = $sitepress->get_active_languages();

        foreach( $active_languages as $language ){

            $appointment_product_id = get_post_meta( $appointment_id, '_appointment_product_id', true );

            if( !$lang ){
                $appointment_language = $sitepress->get_element_language_details( $appointment_product_id, 'post_product' );
                if ( $appointment_language->language_code == $language['code'] ) {
                    continue;
                }
            }elseif( $lang != $language['code'] ){
                continue;
            }

            $trnsl_appointment_id = wp_insert_post( $appointment_data );

            $meta_args = array(
                '_appointment_order_item_id' => get_post_meta( $appointment_id, '_appointment_order_item_id', true ),
                '_appointment_product_id'    => $this->get_translated_appointment_product_id( $appointment_id, $language['code'] ),
                '_appointment_staff_id'   	 => $this->get_translated_appointment_staff_id( $appointment_id, $language['code'] ),
                '_appointment_cost'          => get_post_meta( $appointment_id, '_appointment_cost', true ),
                '_appointment_start'         => get_post_meta( $appointment_id, '_appointment_start', true ),
                '_appointment_end'           => get_post_meta( $appointment_id, '_appointment_end', true ),
                '_appointment_all_day'       => intval( get_post_meta( $appointment_id, '_appointment_all_day', true ) ),
                '_appointment_parent_id'     => get_post_meta( $appointment_id, '_appointment_parent_id', true ),
                '_appointment_customer_id'   => get_post_meta( $appointment_id, '_appointment_customer_id', true ),
                '_appointment_duplicate_of'   => $appointment_id,
                '_language_code'   => $language['code'],
            );

            foreach ( $meta_args as $key => $value ) {
                update_post_meta( $trnsl_appointment_id, $key, $value );
            }

            WC_Cache_Helper::get_transient_version( 'appointments', true );

        }


    }

    function get_translated_appointment_product_id( $appointment_id, $language ){

        $appointment_product_id = get_post_meta( $appointment_id, '_appointment_product_id', true );

        if( $appointment_product_id ){
            $trnsl_appointment_product_id = apply_filters( 'translate_object_id', $appointment_product_id, 'product', false, $language );
            if( is_null( $trnsl_appointment_product_id ) ){
                $trnsl_appointment_product_id = '';
            }
        }

        return $trnsl_appointment_product_id;

    }

    function get_translated_appointment_staff_id( $appointment_id, $language ){

        $appointment_staff_id = get_post_meta( $appointment_id, '_appointment_staff_id', true );
        $trnsl_appointment_staff_id = '';

        if( $appointment_staff_id ){
            $trnsl_appointment_staff_id = apply_filters( 'translate_object_id', $appointment_staff_id, 'appointable_staff', false, $language );

            if( is_null( $trnsl_appointment_staff_id ) ){
                $trnsl_appointment_staff_id = '';
            }
        }

        return $trnsl_appointment_staff_id;
    }

    function update_status_for_translations( $appointment_id ){
        global $wpdb;

        $translated_appointments = $this->get_translated_appointments( $appointment_id );

        foreach( $translated_appointments as $appointment ){

            $status = $wpdb->get_var( $wpdb->prepare( "SELECT post_status FROM {$wpdb->posts} WHERE ID = %d", $appointment_id ) ); //get_post_status( $appointment_id );
            $language = get_post_meta( $appointment->post_id, '_language_code', true );

            $wpdb->update(
                $wpdb->posts,
                array(
                    'post_status' => $status,
                    'post_parent' => wp_get_post_parent_id( $appointment_id ),
                ),
                array(
                    'ID' => $appointment->post_id
                )
            );

            update_post_meta( $appointment->post_id, '_appointment_product_id', $this->get_translated_appointment_product_id( $appointment_id, $language ) );
            update_post_meta( $appointment->post_id, '_appointment_staff_id', $this->get_translated_appointment_staff_id( $appointment_id, $language ) );

        }

    }

    function get_translated_appointments($appointment_id){
        global $wpdb;

        $translated_appointments = $wpdb->get_results( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_appointment_duplicate_of' AND meta_value = %d", $appointment_id ) );

        return $translated_appointments;
    }

    public function appointment_filters_query( $query ) {
        global $typenow, $sitepress, $wpdb;

        if ( ( isset( $query->query_vars['post_type'] ) && $query->query_vars['post_type'] == 'wc_appointment' ) || ( $typenow == 'wc_appointment' && isset( $_GET['post_type'] ) && $_GET['post_type'] == 'wc_appointment' && !isset( $_GET['page'] ) ) ) {

            $product_ids = $wpdb->get_col( $wpdb->prepare(
                "SELECT element_id
					FROM {$wpdb->prefix}icl_translations
					WHERE language_code = %s AND element_type = 'post_product'", $sitepress->get_current_language() ) );

            $query->query_vars[ 'meta_query' ][] = array(
                array(
                    'key'   => '_appointment_product_id',
                    'value' => $product_ids,
                    'compare ' => 'IN'
                )
            );
        }
    }

    function appointments_in_date_range_query($appointment_ids){
        global $sitepress;

        foreach ( $appointment_ids as $key => $appointment_id ) {

            $language_code = $sitepress->get_language_for_element( get_post_meta( $appointment_id, '_appointment_product_id', true ) , 'post_product' );
            $current_language = $sitepress->get_current_language();

            if( $language_code != $current_language ){
                unset( $appointment_ids[$key] );
            }

        }

        return $appointment_ids;

    }

    function clear_transient_fields(){

        if ( isset( $_GET['post_type'] ) && $_GET['post_type'] == 'wc_appointment' && isset( $_GET['page'] ) && $_GET['page'] == 'appointment_calendar' ) {

            global $wpdb;
            //delete transient fields
            $wpdb->query("
                DELETE FROM $wpdb->options
		        WHERE option_name LIKE '%schedule_dr_%'
		    ");

        }

    }

    function delete_appointments( $appointment_id ){

        if( $appointment_id > 0 && get_post_type( $appointment_id ) == 'wc_appointment' ){

            $translated_appointments = $this->get_translated_appointments( $appointment_id );

            remove_action( 'before_delete_post', array( $this, 'delete_appointments' ) );

            foreach( $translated_appointments as $appointment ){

                global $wpdb;

                $wpdb->update(
                    $wpdb->posts,
                    array(
                        'post_parent' => 0
                    ),
                    array(
                        'ID' => $appointment->post_id
                    )
                );

                wp_delete_post( $appointment->post_id );

            }

            add_action( 'before_delete_post', array( $this, 'delete_appointments' ) );
        }

    }

    function trash_appointments( $appointment_id ){

        if( $appointment_id > 0 && get_post_type( $appointment_id ) == 'wc_appointment' ){

            $translated_appointments = $this->get_translated_appointments( $appointment_id );

            foreach( $translated_appointments as $appointment ){
                global $wpdb;

                $wpdb->update(
                    $wpdb->posts,
                    array(
                        'post_status' => 'trash'
                    ),
                    array(
                        'ID' => $appointment->post_id
                    )
                );

            }

        }

    }
}

$GLOBALS['wc_appointments_integration_wmcl'] = new WC_Appointments_Integration_WCML();