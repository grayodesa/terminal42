<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WooCommerce TM Extra Product Options integration class.
 */
class WC_Appointments_Integration_WCFUE {

	/**
     * @var array The different appointment statuses
     */
    public static $statuses = array();

    function __construct() {

		self::$statuses = array_unique( array_merge( get_wc_appointment_statuses(), get_wc_appointment_statuses( 'user' ), get_wc_appointment_statuses( 'cancel') ) );

        add_filter( 'fue_email_types', array( $this, 'register_email_type' ) );

		// manual emails
        add_action( 'fue_manual_types', array( $this, 'manual_types' ) );
        add_action( 'fue_manual_type_actions', array( $this, 'manual_type_actions' ) );
        add_action( 'fue_manual_js', array($this, 'manual_js') );
        add_filter( 'fue_manual_email_recipients', array( $this, 'manual_email_recipients' ), 10, 2 );

        // trigger fields
        add_filter( 'fue_email_form_trigger_fields', array( $this, 'add_product_selector' ) );

        add_action( 'fue_email_form_scripts', array( $this, 'email_form_script' ) );
        add_action( 'fue_manual_js', array( $this, 'manual_form_script' ) );

        add_filter( 'fue_trigger_str', array( $this, 'date_trigger_string' ), 10, 2 );

        add_action( 'fue_email_variables_list', array( $this, 'email_variables_list' ) );
        add_action( 'fue_email_manual_variables_list', array( $this, 'email_variables_list' ) );

        add_action( 'fue_before_variable_replacements', array( $this, 'register_variable_replacements' ), 10, 4 );

        add_action( 'woocommerce_new_appointment', array( $this, 'appointment_created' ) );
        foreach ( self::$statuses as $status ) {
            add_action( 'woocommerce_appointment_'. $status, array( $this, 'appointment_status_updated' ) );
        }


        // manually trigger status changes because WC Appointments doesn't trigger these when saving from the admin screen
        add_action( 'save_post', array( $this, 'maybe_trigger_status_update' ), 11, 1 );

        add_action( 'fue_email_form_trigger_fields', array( $this, 'email_form_triggers' ), 9, 3 );

        // Order Importer
        add_filter( 'fue_import_orders_supported_types', array( $this, 'declare_import_support' ) );
        add_filter( 'fue_wc_get_orders_for_email', array( $this, 'get_orders_for_email' ), 10, 2 );
        add_filter( 'fue_wc_filter_orders_for_email', array( $this, 'filter_orders_for_email' ), 10, 2 );
        add_filter( 'fue_wc_import_insert', array( $this, 'add_appointment_id_to_meta' ), 1, 2 );
        add_filter( 'fue_wc_import_insert', array( $this, 'modify_insert_send_date' ), 10, 2 );

    }

    /**
     * Register custom email type
     *
     * @param array $types
     * @return array
     */
    public function register_email_type( $types ) {
        $triggers = array(
            'before_appointment_event'      => __( 'Before Scheduled Date', 'woocommerce-appointments' ),
            'after_appointment_event'       => __( 'After Scheduled Date', 'woocommerce-appointments' ),
            'appointment_created'           => __( 'After Appointment is Created', 'woocommerce-appointments' )
        );

        // add appointment statuses
        foreach ( self::$statuses as $status ) {
            $triggers['appointment_status_'. $status] = sprintf( __( 'After Appointment Status: %s', 'woocommerce-appointments' ), $status );
        }

        $props = array(
            'label'                 => __('WooCommerce Appointments', 'woocommerce-appointments'),
            'singular_label'        => __('WooCommerce Appointment', 'woocommerce-appointments'),
            'triggers'              => $triggers,
            'durations'             => Follow_Up_Emails::$durations,
            'long_description'      => __('Send follow-up emails to customers that schedule appointments, services or rentals.<br />Increase revenue with a custom lifecycle marketing program from Outbound Commerce. It’s email marketing for busy eCommerce businesses built by experienced eCommerce and marketing professionals.', 'woocommerce-appointments'),
            'short_description'     => __('Not sure where to start? Let Outbound Commerce help. Get email marketing for busy eCommerce businesses built by experienced eCommerce and marketing professionals.', 'woocommerce-appointments')
        );
        $types[] = new FUE_Email_Type( 'wc_appointments', $props );

        return $types;
    }

	/**
     * Appointment option for manual emails
     */
    public function manual_types() {
        ?><option value="scheduled_event"><?php _e( 'Customers who scheduled this event', 'woocommerce-appointments' ); ?></option><?php
    }

    /**
     * Action for manual emails when appointment is selected
     */
    public function manual_type_actions() {
        $products = array();

        $posts = get_posts( array(
                'post_type'     => 'product',
                'post_status'   => 'publish',
                'nopaging'      => true
            ) );

        foreach ( $posts as $post ) {
            $product = WC_FUE_Compatibility::wc_get_product( $post->ID );

            if ( $product->is_type( array( 'appointment' ) ) ) {
                $products[] = $product;
			}
        }

        ?>
        <div class="send-type-appointments send-type-div">
            <select id="appointment_event_id" name="appointment_event_id" class="select2" style="width: 400px;">
                <?php foreach ( $products as $product ): ?>
                    <option value="<?php echo $product->id; ?>"><?php echo esc_html( $product->get_title() ); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    <?php
    }

    /**
     * JS for manual emails
     */
    public function manual_js() {
        ?>
        jQuery("#send_type").change(function() {
            switch (jQuery(this).val()) {
                case "scheduled_event":
                    jQuery(".send-type-appointments").show();
                    break;
            }
        }).change();
    <?php
    }

    /**
     * Get users who scheduled the selected event
     *
     * @param array $recipients
     * @param array $post
     *
     * @return array
     */
    public function manual_email_recipients( $recipients, $post ) {
        global $wpdb;

        if ( $post['send_type'] == 'scheduled_event' ) {

            $search_args = array(
                'post_type'     => 'wc_appointment',
                'post_status'   => array( 'complete', 'paid' ),
                'meta_query'    => array(
                                        array(
                                            'key'       => '_appointment_product_id',
                                            'value'     => $post['appointment_event_id'],
                                            'compare'   => '='
                                        )
                                    )
            );

            $appointments = get_posts( $search_args );

            foreach ( $appointments as $appointment ) {

                $order_item_id  = get_post_meta( $appointment->ID, '_appointment_order_item_id', true );
                $user_id        = get_post_meta( $appointment->ID, '_appointment_customer_id', true );
                $order_id       = $wpdb->get_var( $wpdb->prepare("SELECT order_id FROM {$wpdb->prefix}woocommerce_order_items WHERE order_item_id = %d", $order_item_id) );
                $order          = WC_FUE_Compatibility::wc_get_order( $order_id );

                $key = $user_id .'|'. $order->billing_email .'|'. $order->billing_first_name .' '. $order->billing_last_name;
                $recipients[$key] = array($user_id, $order->billing_email, $order->billing_first_name .' '. $order->billing_last_name);

            }

        }

        return $recipients;
    }

    public function add_product_selector( $email ) {
        if ( in_array( $email->type, apply_filters('fue_appointment_form_products_selector_email_types', array('wc_appointments') ) ) ) {
            // load the categories
            $categories     = get_terms( 'product_cat', array( 'order_by' => 'name', 'order' => 'ASC' ) );
            $has_variations = (!empty($email->product_id) && FUE_Addon_Woocommerce::product_has_children($email->product_id)) ? true : false;
            $storewide_type = (!empty($email->meta['storewide_type'])) ? $email->meta['storewide_type'] : 'all';

            include( 'woocommerce-follow-up-emails/templates/email-form/appointments/email-form.php' );
        }
    }

    /**
     * Javascript for the email form
     */
    public function email_form_script() {
        wp_enqueue_script( 'fue-form-appointments', FUE_TEMPLATES_URL .'/js/email-form-appointments.js' );
    }

    /**
     * Javascript for manual emails
     */
    public function manual_form_script() {
        ?>
        jQuery("#send_type").change(function() {
            if ( jQuery(this).val() == "scheduled_event" ) {
                jQuery(".var_wc_appointments").show();
            } else {
                jQuery(".var_wc_appointments").hide();
            }
        }).change();
        <?php
    }

    /**
     * Return the correct trigger string for date-based emails
     *
     * @param string $trigger
     * @param $email $email
     * @return string
     */
    public function date_trigger_string( $trigger, $email ) {
        if ( $email->type != 'wc_appointments' ) {
            return $trigger;
        }

        if ( $email->duration == 'date' ) {
            $trigger = sprintf( __('Send on %s'), fue_format_send_datetime( $email ) );
        }
        return $trigger;
    }

    /**
     * List of available variables
     * @param FUE_Email $email
     */
    public function email_variables_list( $email ) {
        global $woocommerce;

        if ( $email->type != 'wc_appointments') {
            return;
        }
        ?>
        <li class="var hideable var_wc_appointments"><strong>{item_name}</strong> <img class="help_tip" title="<?php _e('The name of the purchased item.', 'woocommerce-appointments'); ?>" src="<?php echo FUE_TEMPLATES_URL; ?>/images/help.png" width="16" height="16" /></li>
        <li class="var hideable var_wc_appointments"><strong>{item_category}</strong> <img class="help_tip" title="<?php _e('The list of categories where the purchased item is under.', 'woocommerce-appointments'); ?>" src="<?php echo FUE_TEMPLATES_URL; ?>/images/help.png" width="16" height="16" /></li>
        <li class="var hideable var_wc_appointments var_wc_appointments_item_quantity"><strong>{item_quantity}</strong> <img class="help_tip" title="<?php _e('The quantity of the purchased item.', 'woocommerce-appointments'); ?>" src="<?php echo FUE_TEMPLATES_URL; ?>/images/help.png" width="16" height="16" /></li>
        <li class="var hideable var_wc_appointments"><strong>{appointment_start}</strong> <img class="help_tip" title="<?php _e('The start date of the scheduled product or service', 'woocommerce-appointments'); ?>" src="<?php echo $woocommerce->plugin_url(); ?>/assets/images/help.png" width="16" height="16" /></li>
        <li class="var hideable var_wc_appointments"><strong>{appointment_end}</strong> <img class="help_tip" title="<?php _e('The end date of the scheduled product or service', 'woocommerce-appointments'); ?>" src="<?php echo $woocommerce->plugin_url(); ?>/assets/images/help.png" width="16" height="16" /></li>
        <li class="var hideable var_wc_appointments"><strong>{appointment_duration}</strong> <img class="help_tip" title="<?php _e('The duration of the scheduled product or service', 'woocommerce-appointments'); ?>" src="<?php echo $woocommerce->plugin_url(); ?>/assets/images/help.png" width="16" height="16" /></li>
        <li class="var hideable var_wc_appointments"><strong>{appointment_date}</strong> <img class="help_tip" title="<?php _e('The date of the scheduled product or service', 'woocommerce-appointments'); ?>" src="<?php echo $woocommerce->plugin_url(); ?>/assets/images/help.png" width="16" height="16" /></li>
        <li class="var hideable var_wc_appointments"><strong>{appointment_time}</strong> <img class="help_tip" title="<?php _e('The time of the scheduled product or service', 'woocommerce-appointments'); ?>" src="<?php echo $woocommerce->plugin_url(); ?>/assets/images/help.png" width="16" height="16" /></li>
        <li class="var hideable var_wc_appointments"><strong>{appointment_amount}</strong> <img class="help_tip" title="<?php _e('The amount or cost of the scheduled product or service', 'woocommerce-appointments'); ?>" src="<?php echo $woocommerce->plugin_url(); ?>/assets/images/help.png" width="16" height="16" /></li>
        <li class="var hideable var_wc_appointments"><strong>{appointment_staff}</strong> <img class="help_tip" title="<?php _e('The staff scheduled', 'woocommerce-appointments'); ?>" src="<?php echo $woocommerce->plugin_url(); ?>/assets/images/help.png" width="16" height="16" /></li>
    <?php
    }

    /**
     * Register subscription variables to be replaced
     *
     * @param FUE_Sending_Email_Variables   $var
     * @param array                 $email_data
     * @param FUE_Email             $email
     * @param object                $queue_item
     */
    public function register_variable_replacements( $var, $email_data, $email, $queue_item ) {
        if ( $email->type != 'wc_appointments' ) {
            return;
        }

        $variables = array(
            'item_category' => '',
            'item_name' => '',
            'item_quantity' => '',
            'appointment_start' => '',
            'appointment_end' => '',
            'appointment_duration' => '',
            'appointment_date' => '',
            'appointment_time' => '',
            'appointment_amount' => '',
            'appointment_staff' => ''
        );

        // use test data if the test flag is set
        if ( isset( $email_data['test'] ) && $email_data['test'] ) {
            $variables = $this->add_test_variable_replacements( $variables, $email_data, $email );
        } else {
            $variables = $this->add_variable_replacements( $variables, $email_data, $queue_item, $email );
        }

        $var->register( $variables );
    }

    /**
     * Scan through the keys of $variables and apply the replacement if one is found
     * @param array     $variables
     * @param array     $email_data
     * @param object    $queue_item
     * @param FUE_Email $email
     * @return array
     */
    protected function add_variable_replacements( $variables, $email_data, $queue_item, $email ) {
        if ( $queue_item->order_id && $queue_item->product_id ) {
            $item_id = $queue_item->product_id;

            // appointment data
            $meta       = maybe_unserialize( $queue_item->meta );
            $appointment_id = !empty( $meta['appointment_id'] ) ? $meta['appointment_id'] : 0;

            if ( $appointment_id == 0 ) {
                return $variables;
            }

            /**
             * @var $appointment WC_Appointment
             * @var $appointment_product WC_Product_Appointment
             */
            $appointment            = get_wc_appointment( $appointment_id );
            $appointment_product    = $appointment->get_product();
            $appointment_order      = $appointment->get_order();
            $appointment_start      = $appointment->get_start_date( get_option( 'date_format' ) .' ', get_option( 'time_format' ) );
            $appointment_end        = $appointment->get_end_date( get_option( 'date_format' ) .' ', get_option( 'time_format' ) );
            $appointment_date     = $appointment->get_start_date( get_option( 'date_format' ), '' );
            $appointment_time     = $appointment->get_start_date( '', get_option( 'time_format' ) );
            $appointment_amount   = woocommerce_price( $appointment->cost );
            $appointment_staff = ( $appointment->staff_id > 0 ) ? get_the_title( $appointment->staff_id ) : '';

            $used_cats  = array();
            $item_cats  = '<ul>';

            $categories = get_the_terms($appointment->product_id, 'product_cat');

            if ( is_array( $categories ) ) {
                foreach ( $categories as $category ) {

                    if ( !in_array( $category->term_id, $used_cats ) ) {
                        $item_cats .= apply_filters(
                            'fue_email_cat_list',
                            '<li>'. $category->name .'</li>',
                            $queue_item->id,
                            $categories
                        );
                        $used_cats[] = $category->term_id;
                    }

                }
            }

            $item_url = FUE_Sending_Mailer::create_email_url(
                $queue_item->id,
                $queue_item->email_id,
                $email_data['user_id'],
                $email_data['email_to'],
                get_permalink($queue_item->product_id)
            );

            $variables['item_name']                 = FUE_Addon_Woocommerce::get_product_name( $appointment_product );
            $variables['item_url']                  = $item_url;
            $variables['item_category']             = $item_cats;
            $variables['appointment_start']         = $appointment_start;
            $variables['appointment_end']           = $appointment_end;
            $variables['appointment_date']          = $appointment_date;
            $variables['appointment_time']          = $appointment_time;
            $variables['appointment_amount']        = $appointment_amount;
            $variables['appointment_staff']         = $appointment_staff;
            $variables['order_billing_address']     = '';
            $variables['order_shipping_address']    = '';
            $variables['item_quantity']             = 1;

            if ( $appointment_order ) {
                $variables['order_billing_address']     = $appointment_order->get_formatted_billing_address();
                $variables['order_shipping_address']    = $appointment_order->get_formatted_shipping_address();

                foreach ( $appointment_order->get_items() as $item_id => $item ) {
                    $product_id     = !empty( $item['product_id'] ) ? $item['product_id'] : $item['id'];

                    if ( $appointment_product->id == $product_id ) {
                        $variables['item_quantity'] = $item['qty'];

                        if ( isset( $item['Duration'] ) ) {
                            $variables['appointment_duration'] = $item['Duration'];
                        }
                        break;
                    }
                }
            }

        }

        return $variables;
    }

    /**
     * Add variable replacements for test emails
     *
     * @param array     $variables
     * @param array     $email_data
     * @param FUE_Email $email
     *
     * @return array
     */
    protected function add_test_variable_replacements( $variables, $email_data, $email ) {
        $variables['item_url']                  = '#';
        $variables['item_category']             = 'Appointments';
        $variables['item_quantity']             = 1;
        $variables['appointment_start']         = date( get_option( 'date_format' ) .' '. get_option( 'time_format' ), current_time('timestamp') + 86400 );
        $variables['appointment_end']           = date( get_option( 'date_format' ) .' '. get_option( 'time_format' ), current_time('timestamp') + (86400*2) );
        $variables['appointment_duration']      = '3 Hours';
        $variables['appointment_date']          = date( get_option( 'date_format' ), current_time('timestamp') + 86400 );
        $variables['appointment_time']          = date( get_option( 'time_format' ), current_time('timestamp') + 86400 );
        $variables['appointment_amount']        = woocommerce_price( 77 );
        $variables['appointment_staff']         = '';
        $variables['order_billing_address']     = '77 North Beach Dr., Miami, FL 35122';
        $variables['order_shipping_address']    = '77 North Beach Dr., Miami, FL 35122';

        return $variables;
    }

    /**
     * Queue emails after a appointment has been created
     * @param int $appointment_id
     */
    public function appointment_created( $appointment_id ) {
        $appointment = get_wc_appointment( $appointment_id );

        // stop FUE from scheduling blank emails after adding a appointment product to the cart
        if ( $appointment->status == 'in-cart' ) {
            return;
        }

        $this->create_email_order( $appointment_id, array('appointment_created') );
    }

    /**
     * Fires after a appointment's status has been updated
     * @param $appointment_id
     */
    public function appointment_status_updated( $appointment_id ) {
        global $wpdb;

        // get the status directly from wp_posts to make sure that we have the latest
        $status = $wpdb->get_var( $wpdb->prepare(
            "SELECT post_status FROM {$wpdb->posts} WHERE ID = %d",
            $appointment_id
        ));

        $triggers = array('appointment_status_'. $status);

        if ( $status == 'paid' || $status == 'confirmed' ) {
            $triggers[] = 'before_appointment_event';
            $triggers[] = 'after_appointment_event';
        }

        $this->create_email_order( $appointment_id, $triggers );

        // update the _last_status meta
        update_post_meta( $appointment_id, '_last_status', $status );

    }

    /**
     * Triggered from the save_post hook, we have to manually trigger
     * status update hooks because WC Appointments does not broadcast
     * status updates when a appointment is updated using the admin screen
     * @param $post_id
     */
    public function maybe_trigger_status_update( $post_id ) {
        if ( !empty($_POST['post_type']) && $_POST['post_type'] != 'wc_appointment' ) {
            return $post_id;
        }

        if ( empty( $_POST['_appointment_status'] ) ) {
            return $post_id;
        }

        // remove hook to avoid inifinite loop
        remove_action( 'save_post', array( $this, 'maybe_trigger_status_update' ), 11 );

        $appointment_status     = wc_clean( $_POST['_appointment_status'] );
        $last_status        = get_post_meta( $post_id, '_last_status', true );


        if ( $last_status != $appointment_status ) {
            $this->appointment_status_updated( $post_id );
        }

        return $post_id;
    }

    /**
     * Add the ability to additionally check the last status before executing a trigger
     *
     * @param FUE_Email $email
     */
    public function email_form_triggers( FUE_Email $email ) {
        include( 'woocommerce-follow-up-emails/templates/email-form/appointments/triggers.php' );
    }

    /**
     * Add appointments to the email types that support order importing
     *
     * @param array $types
     * @return array
     */
    public function declare_import_support( $types ) {
        $types[] = 'wc_appointments';
        return $types;
    }

    /**
     * Get orders that match the $email's criteria
     * @param array     $orders Matching Order IDs
     * @param FUE_Email $email
     * @return array
     */
    public function get_orders_for_email( $orders, $email ) {
        $wpdb = Follow_Up_Emails::instance()->wpdb;

        if ( $email->type != 'wc_appointments' ) {
            return $orders;
        }

        $valid_statuses = array( 'confirmed', 'paid', 'complete' );

        // add appointment statuses
        $status_triggers = array();
        foreach ( self::$statuses as $status ) {
            $status_triggers[ $status ] = 'appointment_status_'. $status;
        }

        if ( ( $status = array_search( $email->trigger, $status_triggers ) ) ) {
            $appointment_posts = get_posts( array(
                'nopaging'      => true,
                'post_type'     => 'wc_appointment',
                'post_status'   => $status,
                'fields'        => 'ids'
            ) );

            foreach ( $appointment_posts as $appointment_id ) {
                $last_status = get_post_meta( $appointment_id, '_last_status', true );

                if ( !empty( $email->meta['appointments_last_status'] ) && $email->meta['appointments_last_status'] != $last_status ) {
                    continue;
                }

                $orders[] = $appointment_id;
            }
        } elseif ( $email->trigger == 'before_appointment_event' ) {
            $now    = date('Ymd') . '000000';
            $appointment_ids = $wpdb->get_col($wpdb->prepare(
                "SELECT post_id
                FROM {$wpdb->postmeta}
                WHERE meta_key = '_appointment_start'
                AND meta_value > %s",
                $now
            ));

            if ( !empty( $appointment_ids ) ) {
                foreach ( $appointment_ids as $appointment_id ) {
                    if ( !in_array( get_post_status( $appointment_id ), $valid_statuses ) ) {
                        continue;
                    }
                    $orders[] = $appointment_id;
                }

            }
        } elseif ( $email->trigger = 'after_appointment_event' ) {
            $now    = date('Ymd') . '000000';
            $appointment_ids = $wpdb->get_col($wpdb->prepare(
                "SELECT post_id
                FROM {$wpdb->postmeta}
                WHERE meta_key = '_appointment_end'
                AND meta_value < %s",
                $now
            ));

            if ( !empty( $appointment_ids ) ) {
                foreach ( $appointment_ids as $appointment_id ) {
                    if ( !in_array( get_post_status( $appointment_id ), $valid_statuses ) ) {
                        continue;
                    }
                    $orders[] = $appointment_id;
                }

            }
        }

        return array( $email->id => $orders );
    }

    /**
     * Run filters on appointments to remove invalid orders
     *
     * @param array $data
     * @param FUE_Email $email
     * @return array
     */
    public function filter_orders_for_email( $data, $email ) {
        if ( $email->type == 'wc_appointments' ) {
            foreach ( $data as $email_id => $orders ) {
                foreach ( $orders as $idx => $appointment_id ) {
                    $appointment = new WC_Appointment( $appointment_id );
                    $order   = $appointment->get_order();

                    if ( $appointment->post->post_type != 'wc_appointment' ) {
                        unset( $data[ $email_id ][ $idx ] );
                        continue;
                    }

                    if ( $this->is_category_excluded( $appointment, $email ) ) {
                        unset( $data[ $email_id ][ $idx ] );
                        continue;
                    }

                    // A appointment can have no order linked to it
                    if ( $order ) {
                        $customer = fue_get_customer_from_order( $order );
                        if ( Follow_Up_Emails::instance()->fue_wc->wc_scheduler->exclude_customer_based_on_purchase_history( $customer, $email ) ) {
                            unset( $data[ $email_id ][ $idx ] );
                            continue;
                        }
                    }

                    // limit to selected product or category
                    if ( $email->meta['storewide_type'] == 'products' && $email->product_id > 0 && $email->product_id != $appointment->product_id ) {
                        unset( $data[ $email_id ][ $idx ] );
                        continue;
                    }  elseif ( $email->meta['storewide_type'] == 'categories' && $email->category_id > 0 ) {
                        $categories = wp_get_object_terms( $appointment->product_id, 'product_cat', array('fields' => 'ids') );

                        if ( is_wp_error( $categories ) ) {
                            unset( $data[ $email_id ][ $idx ] );
                            continue;
                        }

                        if ( empty( $categories ) || !in_array( $email->category_id, $categories ) ) {
                            unset( $data[ $email_id ][ $idx ] );
                            continue;
                        }
                    }

                    // look for a possible duplicate item in the queue
                    $dupes = Follow_Up_Emails::instance()->scheduler->get_items(array(
                        'email_id'      => $email->id,
                        'is_sent'       => 0,
                        'order_id'      => $appointment->order_id,
                        'product_id'    => $appointment->product_id
                    ));

                    if ( count( $dupes ) > 0 ) {
                        foreach ( $dupes as $dupe_item ) {
                            if ( !empty( $dupe_item->meta['appointment_id'] ) && $dupe_item->meta['appointment_id'] == $appointment_id ) {
                                // found exact appointment match
                                unset( $data[ $email_id ][ $idx ] );
                                continue 2;
                            }
                        }
                    }
                }
            }
        }

        return $data;
    }

    /**
     * If the post pointing to the order ID is of type 'wc_appointment', use the
     * order_id as the appointment_id and fill in the proper order ID.
     *
     * @param array     $insert
     * @param FUE_Email $email
     * @return array
     */
    public static function add_appointment_id_to_meta( $insert, $email ) {
        if ( empty( $insert['order_id'] ) ) {
            return $insert;
        }

        $post = get_post( $insert['order_id'] );

        if ( $post->post_type == 'wc_appointment' ) {
            $insert['meta']['appointment_id'] = $insert['order_id'];
            $insert['order_id'] = $post->post_parent;
        }

        return $insert;
    }

    /**
     * Change the send date of the email for 'before_appointment_event' and 'after_appointment_event' triggers
     * @param array $insert
     * @param FUE_Email $email
     * @return array
     */
    public function modify_insert_send_date( $insert, $email ) {
        if ( $email->type != 'wc_appointments' ) {
            return $insert;
        }

        $appointment_id = $insert['meta']['appointment_id'];

        if ( $email->trigger == 'before_appointment_event' ) {
            $start  = strtotime( get_post_meta( $appointment_id, '_appointment_start', true ) );
            $time   = FUE_Sending_Scheduler::get_time_to_add( $email->interval_num, $email->interval_duration );

            $insert['send_on'] = $start - $time;
        } elseif ( $email->trigger == 'after_appointment_event' ) {
            $start  = strtotime( get_post_meta( $appointment_id, '_appointment_end', true ) );
            $time   = FUE_Sending_Scheduler::get_time_to_add( $email->interval_num, $email->interval_duration );

            $insert['send_on'] = $start + $time;
        }

        return $insert;
    }

    /**
     * Send emails that matches the provided triggers to the queue
     * @param int $appointment_id
     * @param array $triggers
     */
    private function create_email_order( $appointment_id, $triggers = array() ) {
        /**
         * @var $appointment WC_Appointment
         * @var $order WC_Order
         */
        $appointment    = get_wc_appointment( $appointment_id );
        $last_status= get_post_meta( $appointment_id, '_last_status', true );
        $order      = WC_FUE_Compatibility::wc_get_order( $appointment->order_id );

        $emails     = fue_get_emails( 'any', '', array(
            'meta_query'    => array(
                array(
                    'key'       => '_interval_type',
                    'value'     => $triggers,
                    'compare'   => 'IN'
                )
            )
        ) );

        foreach ( $emails as $email ) {

            if ( $email->status != 'fue-active' ) {
                continue;
            }

            if ( !empty( $email->meta['appointments_last_status'] ) && $email->meta['appointments_last_status'] != $last_status ) {
                continue;
            }

            if ( $this->is_category_excluded( $appointment, $email ) ) {
                continue;
            }

            // A appointment can have no order linked to it
            if ( $order ) {
                $customer = fue_get_customer_from_order( $order );
                if ( Follow_Up_Emails::instance()->fue_wc->wc_scheduler->exclude_customer_based_on_purchase_history( $customer, $email ) ) {
                    continue;
                }
            }

            // limit to selected product or category
            if ( $email->meta['storewide_type'] == 'products' && $email->product_id > 0 && $email->product_id != $appointment->product_id ) {
                continue;
            }  elseif ( $email->meta['storewide_type'] == 'categories' && $email->category_id > 0 ) {
                $categories = wp_get_object_terms( $appointment->product_id, 'product_cat', array('fields' => 'ids') );

                if ( is_wp_error( $categories ) ) {
                    continue;
                }

                if ( empty( $categories ) || !in_array( $email->category_id, $categories ) ) {
                    continue;
                }
            }

            // look for a possible duplicate item in the queue
            $dupes = Follow_Up_Emails::instance()->scheduler->get_items(array(
                'email_id'      => $email->id,
                'is_sent'       => 0,
                'order_id'      => $appointment->order_id,
                'product_id'    => $appointment->product_id
            ));

            if ( count( $dupes ) > 0 ) {
                foreach ( $dupes as $dupe_item ) {
                    if ( !empty( $dupe_item->meta['appointment_id'] ) && $dupe_item->meta['appointment_id'] == $appointment_id ) {
                        // found exact appointment match
                        continue 2;
                    }
                }
            }

            if ( $email->duration == 'date' ) {
                $email->interval_type = 'date';
                $send_on = $email->get_send_timestamp();
            } else {
                if ( $email->interval_type == 'before_appointment_event' ) {
                    $start  = strtotime( get_post_meta( $appointment_id, '_appointment_start', true ) );
                    $time   = FUE_Sending_Scheduler::get_time_to_add( $email->interval_num, $email->interval_duration );

                    $send_on = $start - $time;
                } elseif ( $email->interval_type == 'after_appointment_event' ) {
                    $start  = strtotime( get_post_meta( $appointment_id, '_appointment_end', true ) );
                    $time   = FUE_Sending_Scheduler::get_time_to_add( $email->interval_num, $email->interval_duration );

                    $send_on = $start + $time;
                } else {
                    $send_on    = $email->get_send_timestamp();
                }
            }

            $insert = array(
                'send_on'       => $send_on,
                'email_id'      => $email->id,
                'product_id'    => $appointment->product_id,
                'order_id'      => $appointment->order_id,
                'meta'          => array('appointment_id' => $appointment_id)
            );

            if ( $order ) {
                $user_id = WC_FUE_Compatibility::get_order_user_id( $order );
                if ( $user_id ) {
                    $user                   = new WP_User($user_id);
                    $insert['user_id']      = $user_id;
                    $insert['user_email']   = $user->user_email;
                }
            }

            // Remove the nonce to avoid infinite loop because doing a
            // remove_action on WC_Appointments_Details_Meta_Box doesnt work
            unset( $_POST['wc_appointments_details_meta_box_nonce'] );

            if ( !is_wp_error( FUE_Sending_Scheduler::queue_email( $insert, $email ) ) ) {
                // Tell FUE that an email order has been created
                // to stop it from sending storewide emails
                if (! defined('FUE_ORDER_CREATED')) {
                    define('FUE_ORDER_CREATED', true);
                }

                if ( $order ) {

                    if ( empty( $insert['send_on'] ) ) {
                        $insert['send_on'] = $email->get_send_timestamp();
                    }

                    $email_trigger  = apply_filters( 'fue_interval_str', $email->get_trigger_string(), $email );
                    $send_date      = date( get_option('date_format') .' '. get_option('time_format'), $insert['send_on'] );

                    $note = sprintf(
                        __('Email queued: %s scheduled on %s<br/>Trigger: %s', 'woocommerce-appointments'),
                        $email->name,
                        $send_date,
                        $email_trigger
                    );

                    $order->add_order_note( $note );
                }
            }

        }
    }

    /**
     * Checks if $appointment is under a category that is excluded in $email
     *
     * @param WC_Appointment    $appointment
     * @param FUE_Email     $email
     * @return bool
     */
    private function is_category_excluded( $appointment, $email ) {
        $excluded = false;

        $categories = wp_get_object_terms( $appointment->product_id, 'product_cat' );

        if ( is_wp_error( $categories ) ) {
            return false;
        }

        $excludes = (isset($email->meta['excluded_categories'])) ? $email->meta['excluded_categories'] : array();

        if ( !is_array( $excludes ) ) {
            $excludes = array();
        }

        if ( count($excludes) > 0 ) {
            foreach ( $categories as $category ) {
                if ( in_array( $category->term_id, $excludes ) ) {
                    $excluded = true;
                    break;
                }
            }
        }

        return apply_filters( 'fue_appointments_category_excluded', $excluded, $appointment, $email );
    }

    private function duration_to_string( $duration, $unit ) {
        $unit = rtrim($unit, 's');

        return ($duration == 1) ? $duration .' '. $unit : $duration .' '. $unit .'s';
    }
}

$GLOBALS['wc_appointments_integration_wcfue'] = new WC_Appointments_Integration_WCFUE();