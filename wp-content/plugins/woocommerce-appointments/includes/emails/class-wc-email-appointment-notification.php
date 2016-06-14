<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Appointment Notifications
 *
 * An email sent manually for appointments.
 *
 * @class 		WC_Email_Appointment_Notification
 * @extends 	WC_Email
 */
class WC_Email_Appointment_Notification extends WC_Email {

	/**
	 * Constructor
	 */
	function __construct() {

		$this->id 				= 'appointment_notification';
		$this->title 			= __( 'Appointment Notification', 'woocommerce-appointments' );
		$this->description		= __( 'Appointment notification emails are sent manually from WooCommerce > Appointments > Send Notification.', 'woocommerce-appointments' );

		$this->heading 			= ''; // Controlled via form
		$this->subject      	= ''; // Controlled via form

		$this->template_html 	= 'emails/customer-appointment-notification.php';
		$this->template_plain 	= 'emails/plain/customer-appointment-notification.php';

		// Call parent constructor
		parent::__construct();

		// Other settings
		$this->template_base = WC_APPOINTMENTS_TEMPLATE_PATH;
	}

	/**
	 * trigger function.
	 *
	 * @access public
	 * @return void
	 */
	function trigger( $appointment_id, $notification_subject, $notification_message, $attachments = array() ) {
		if ( $appointment_id ) {
			$this->object    = get_wc_appointment( $appointment_id );
			$this->find[]    = '{product_title}';
			$this->replace[] = $this->object->get_product()->get_title();

			if ( $this->object->get_order() ) {
				$this->find[]    = '{order_date}';
				$this->replace[] = date_i18n( wc_date_format(), strtotime( $this->object->get_order()->order_date ) );

				$this->find[]    = '{order_number}';
				$this->replace[] = $this->object->get_order()->get_order_number();

				$this->find[]    = '{customer_name}';
				$this->replace[] = $this->object->get_order()->billing_first_name . ' ' . $this->object->get_order()->billing_last_name;

				$this->find[]    = '{customer_first_name}';
				$this->replace[] = $this->object->get_order()->billing_first_name;

				$this->find[]    = '{customer_last_name}';
				$this->replace[] = $this->object->get_order()->billing_last_name;

				$this->recipient = apply_filters( 'woocommerce_email_notification_recipients', $this->object->get_order()->billing_email );
			} else {
				$this->find[]    = '{order_date}';
				$this->replace[] = date_i18n( wc_date_format(), strtotime( $this->object->appointment_date ) );

				$this->find[]    = '{order_number}';
				$this->replace[] = __( 'N/A', 'woocommerce-appointments' );

				$this->find[]    = '{customer_name}';
				$this->replace[] = __( 'N/A', 'woocommerce-appointments' );

				$this->find[]    = '{customer_first_name}';
				$this->replace[] = __( 'N/A', 'woocommerce-appointments' );

				$this->find[]    = '{customer_last_name}';
				$this->replace[] = __( 'N/A', 'woocommerce-appointments' );

				if ( $this->object->customer_id && ( $customer = get_user_by( 'id', $this->object->customer_id ) ) ) {
					$this->recipient = apply_filters( 'woocommerce_email_notification_recipients', $customer->user_email );
				}
			}
		}

		if ( ! $this->is_enabled() || ! $this->get_recipient() )
			return;

		$this->heading              = str_replace( $this->find, $this->replace, $notification_subject );
		$this->subject              = str_replace( $this->find, $this->replace, $notification_subject );
		$this->notification_message = str_replace( $this->find, $this->replace, $notification_message );
		$attachments                = apply_filters( 'woocommerce_email_attachments', $attachments, $this->id, $this->object );

		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $attachments );
	}
	
	/**
	 * Reset tags for find/replace in notification message.
	 *
	 * @return void
	 */
	public function reset_tags() {
		$tags = array(
			'product_title',
			'order_data',
			'order_number',
			'customer_name',
			'customer_first_name',
			'customer_last_name',
		);

		foreach ( $tags as $tag ) {
			$key = array_search( '{' . $tag . '}', $this->find );
			if ( false !== $key ) {
				unset( $this->find[ $key ] );
				unset( $this->replace[ $key ] );
			}
		}
	}

	/**
	 * get_content_html function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_html() {
		ob_start();
		wc_get_template( $this->template_html, array(
			'appointment' 		=> $this->object,
			'email_heading' => $this->get_heading(),
			'notification_message' => $this->notification_message
		), 'woocommerce-appointments/', $this->template_base );
		return ob_get_clean();
	}

	/**
	 * get_content_plain function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_plain() {
		ob_start();
		wc_get_template( $this->template_plain, array(
			'appointment' 		=> $this->object,
			'email_heading' => $this->get_heading(),
			'notification_message' => $this->notification_message
		), 'woocommerce-appointments/', $this->template_base );
		return ob_get_clean();
	}

    /**
     * Initialise Settings Form Fields
     *
     * @access public
     * @return void
     */
    function init_form_fields() {
    	$this->form_fields = array(
			'enabled' => array(
				'title' 		=> __( 'Enable/Disable', 'woocommerce-appointments' ),
				'type' 			=> 'checkbox',
				'label' 		=> __( 'Enable this email notification', 'woocommerce-appointments' ),
				'default' 		=> 'yes'
			),
			'email_type' => array(
				'title' 		=> __( 'Email type', 'woocommerce-appointments' ),
				'type' 			=> 'select',
				'description' 	=> __( 'Choose which format of email to send.', 'woocommerce-appointments' ),
				'default' 		=> 'html',
				'class'			=> 'email_type',
				'options'		=> array(
					'plain'		 	=> __( 'Plain text', 'woocommerce-appointments' ),
					'html' 			=> __( 'HTML', 'woocommerce-appointments' ),
					'multipart' 	=> __( 'Multipart', 'woocommerce-appointments' ),
				)
			)
		);
    }
}

return new WC_Email_Appointment_Notification();
