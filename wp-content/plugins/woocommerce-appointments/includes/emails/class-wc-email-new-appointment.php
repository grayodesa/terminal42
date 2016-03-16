<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * New Appointment Email
 *
 * An email sent to the admin when a new appointment is created.
 *
 * @class 		WC_Email_New_Appointment
 * @extends 	WC_Email
 */
class WC_Email_New_Appointment extends WC_Email {
	/**
	 * Subject for pending confirmation emails.
	 *
	 * @var string
	 */
	public $subject_confirmation = '';

	/**
	 * Constructor
	 */
	function __construct() {

		$this->id                   = 'new_appointment';
		$this->title                = __( 'New Appointment', 'woocommerce-appointments' );
		$this->description          = __( 'New appointment emails are sent to the admin when a new appointment is created and paid. This email is also received when a Pending confirmation appointment is created.', 'woocommerce-appointments' );

		$this->heading              = __( 'New appointment', 'woocommerce-appointments' );
		$this->heading_confirmation = __( 'Confirm appointment', 'woocommerce-appointments' );
		$this->subject              = __( '[{blogname}] New appointment for {product_title} (Order {order_number}) - {order_date}', 'woocommerce-appointments' );
		$this->subject_confirmation = __( '[{blogname}] A new appointment for {product_title} (Order {order_number}) is awaiting your approval - {order_date}', 'woocommerce-appointments' );

		$this->template_html    = 'emails/admin-new-appointment.php';
		$this->template_plain   = 'emails/plain/admin-new-appointment.php';

		// Triggers for this email
		add_action( 'woocommerce_appointment_in-cart_to_paid_notification', array( $this, 'queue_notification' ) );
		add_action( 'woocommerce_appointment_in-cart_to_pending-confirmation_notification', array( $this, 'queue_notification' ) );
		add_action( 'woocommerce_appointment_unpaid_to_paid_notification', array( $this, 'queue_notification' ) );
		add_action( 'woocommerce_appointment_unpaid_to_pending-confirmation_notification', array( $this, 'queue_notification' ) );
		add_action( 'woocommerce_appointment_confirmed_to_paid_notification', array( $this, 'queue_notification' ) );
		add_action( 'woocommerce_new_appointment_notification', array( $this, 'trigger' ) );
		add_action( 'woocommerce_admin_new_appointment_notification', array( $this, 'trigger' ) );

		// Call parent constructor
		parent::__construct();

		// Other settings
		$this->template_base = WC_APPOINTMENTS_TEMPLATE_PATH;
		$this->recipient     = $this->get_option( 'recipient', get_option( 'admin_email' ) );
	}
	
	/**
	 * When appointments are created, orders and other parts may not exist yet. e.g. during order creation on checkout.
	 *
	 * This ensures emails are sent last, once all other logic is complete.
	 */
	public function queue_notification( $appointment_id ) {
		wp_schedule_single_event( time(), 'woocommerce_admin_new_appointment', array( 'appointment_id' => $appointment_id ) );
	}

	/**
	 * trigger function.
	 */
	function trigger( $appointment_id ) {
		if ( $appointment_id ) {
			$this->object = get_wc_appointment( $appointment_id );

			if ( $this->object->has_status( 'in-cart' ) ) {
				return;
			}
			
			$key = array_search( '{product_title}', $this->find );
				if ( false !== $key ) {
					unset( $this->find[ $key ] );
					unset( $this->replace[ $key ] );
				}

			$this->find[]    = '{product_title}';
			$this->replace[] = $this->object->get_product()->get_title();

			if ( $this->object->get_order() ) {
				$this->find[]    = '{order_date}';
				$this->replace[] = date_i18n( wc_date_format(), strtotime( $this->object->get_order()->order_date ) );

				$this->find[]    = '{order_number}';
				$this->replace[] = $this->object->get_order()->get_order_number();
			} else {
				$this->find[]    = '{order_date}';
				$this->replace[] = date_i18n( wc_date_format(), strtotime( $this->object->appointment_date ) );

				$this->find[]    = '{order_number}';
				$this->replace[] = __( 'N/A', 'woocommerce-appointments' );
			}


			if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
				return;
			}
			
			//* Staff reminder
			if ( $this->object->has_staff() && ( $staff = $this->object->get_staff_member() ) ) {
				$staff_member = get_user_by( 'id', $staff->ID );
				$recipients = array_map( 'trim', explode( ',', $this->recipient ) );
				$recipients = array_filter( $recipients, 'is_email' );
				$recipients = implode( ', ', $recipients );
				$this->recipient = implode( ', ', array( $recipients, $staff_member->user_email ) );
			}
			
			$this->recipient = apply_filters( 'woocommerce_email_new_recipients', $this->recipient );

			$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
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
			'appointment' 	=> $this->object,
			'email_heading' => $this->get_heading()
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
			'appointment' 	=> $this->object,
			'email_heading' => $this->get_heading()
		), 'woocommerce-appointments/', $this->template_base );
		return ob_get_clean();
	}

	/**
	 * get_subject function.
	 *
	 * @return string
	 */
	function get_subject() {
		if ( wc_appointment_order_requires_confirmation( $this->object->get_order() ) && $this->object->get_status() == 'pending-confirmation' ) {
			return apply_filters( 'woocommerce_email_subject_' . $this->id, $this->format_string( $this->subject_confirmation ), $this->object );
		} else {
			return apply_filters( 'woocommerce_email_subject_' . $this->id, $this->format_string( $this->subject ), $this->object );
		}
	}

	/**
	 * get_heading function.
	 *
	 * @return string
	 */
	public function get_heading() {
		if ( wc_appointment_order_requires_confirmation( $this->object->get_order() ) && $this->object->get_status() == 'pending-confirmation' ) {
			return apply_filters( 'woocommerce_email_heading_' . $this->id, $this->format_string( $this->heading_confirmation ), $this->object );
		} else {
			return apply_filters( 'woocommerce_email_heading_' . $this->id, $this->format_string( $this->heading ), $this->object );
		}
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
			'recipient' => array(
				'title' 		=> __( 'Recipient(s)', 'woocommerce-appointments' ),
				'type' 			=> 'text',
				'description' 	=> sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to <code>%s</code>.', 'woocommerce-appointments' ), esc_attr( get_option('admin_email') ) ),
				'placeholder' 	=> '',
				'default' 		=> ''
			),
			'subject' => array(
				'title' 		=> __( 'Subject', 'woocommerce-appointments' ),
				'type' 			=> 'text',
				'description' 	=> sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'woocommerce-appointments' ), $this->subject ),
				'placeholder' 	=> '',
				'default' 		=> ''
			),
			'subject_confirmation' => array(
				'title' 		=> __( 'Subject (Pending confirmation)', 'woocommerce-appointments' ),
				'type' 			=> 'text',
				'description' 	=> sprintf( __( 'This controls the email subject line for Pending confirmation appointments. Leave blank to use the default subject: <code>%s</code>.', 'woocommerce-appointments' ), $this->subject_confirmation ),
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
			'heading_confirmation' => array(
				'title' 		=> __( 'Email Heading (Pending confirmation)', 'woocommerce-appointments' ),
				'type' 			=> 'text',
				'description' 	=> sprintf( __( 'This controls the main heading contained within the email notification for Pending confirmation appointments. Leave blank to use the default heading: <code>%s</code>.', 'woocommerce-appointments' ), $this->heading_confirmation ),
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

return new WC_Email_New_Appointment();
