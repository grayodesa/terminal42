<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Appointment is cancelled
 *
 * An email sent to the user when a appointment is cancelled or not approved.
 *
 * @class 		WC_Email_Appointment_Confirmed
 * @extends 	WC_Email
 */
class WC_Email_Appointment_Cancelled extends WC_Email {

	/**
	 * Constructor
	 */
	function __construct() {
		$this->id             = 'appointment_cancelled';
		$this->title          = __( 'Appointment Cancelled', 'woocommerce-appointments' );
		$this->description    = __( 'Appointment cancelled emails are sent when the status of a appointment goes to cancelled.', 'woocommerce-appointments' );

		$this->heading        = __( 'Appointment Cancelled', 'woocommerce-appointments' );
		$this->subject        = __( '[{blogname}] Your appointment of "{product_title}" has been cancelled', 'woocommerce-appointments' );

		$this->template_html  = 'emails/customer-appointment-cancelled.php';
		$this->template_plain = 'emails/plain/customer-appointment-cancelled.php';

		// Triggers for this email
		add_action( 'woocommerce_appointment_pending-confirmation_to_cancelled_notification', array( $this, 'trigger' ) );
		add_action( 'woocommerce_appointment_confirmed_to_cancelled_notification', array( $this, 'trigger' ) );
		add_action( 'woocommerce_appointment_paid_to_cancelled_notification', array( $this, 'trigger' ) );

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
	function trigger( $appointment_id ) {
		if ( $appointment_id ) {
			// Only send the appointment email for appointment post types, not orders, etc
			if ( 'wc_appointment' !== get_post_type( $appointment_id ) ) {
				return;
			}
			
			$this->object    = get_wc_appointment( $appointment_id );

			if ( $this->object->get_product() ) {
				$this->find[]    = '{product_title}';
				$this->replace[] = $this->object->get_product()->get_title();
			}

			if ( $this->object->get_order() ) {
				$key = array_search( '{product_title}', $this->find );
					if ( false !== $key ) {
						unset( $this->find[ $key ] );
						unset( $this->replace[ $key ] );
					}
				$this->find[]    = '{order_date}';
				$this->replace[] = date_i18n( wc_date_format(), strtotime( $this->object->get_order()->order_date ) );

				$this->find[]    = '{order_number}';
				$this->replace[] = $this->object->get_order()->get_order_number();

				$this->recipient = apply_filters( 'woocommerce_email_cancelled_recipients', $this->object->get_order()->billing_email );
			} else {
				$this->find[]    = '{order_date}';
				$this->replace[] = date_i18n( wc_date_format(), strtotime( $this->object->appointment_date ) );

				$this->find[]    = '{order_number}';
				$this->replace[] = __( 'N/A', 'woocommerce-appointments' );

				if ( $this->object->customer_id && ( $customer = get_user_by( 'id', $this->object->customer_id ) ) ) {
					$this->recipient = apply_filters( 'woocommerce_email_cancelled_recipients', $customer->user_email );
				}
			}
		}

		if ( ! $this->is_enabled() || ! $this->get_recipient() )
			return;

		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
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
			'sent_to_admin' => false,
			'plain_text'    => false
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
			'sent_to_admin' => false,
			'plain_text'    => true
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
			'subject' => array(
				'title' 		=> __( 'Subject', 'woocommerce-appointments' ),
				'type' 			=> 'text',
				'description' 	=> sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'woocommerce-appointments' ), $this->subject ),
				'placeholder' 	=> '',
				'default' 		=> ''
			),
			'heading' => array(
				'title' 		=> __( 'Email Heading', 'woocommerce-appointments' ),
				'type' 			=> 'text',
				'description' 	=> sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'woocommerce-appointments' ), $this->heading ),
				'placeholder' 	=> '',
				'default' 		=> ''
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

return new WC_Email_Appointment_Cancelled();
