<?php

class MC4WP_Email_Notification {

	/**
	 * @var array Receivers of the email notification
	 */
	public $receivers = array();

	/**
	 * @var string Email subject
	 */
	public $subject = '';

	/**
	 * @var string Email message
	 */
	public $message = '';

	/**
	 * @var MC4WP_Form Form this notification is set-up for
	 */
	public $form;

	/**
	 * @var iMC4WP_Request Data for this notification
	 */
	public $request;

	/**
	 * @param string|array $receivers
	 * @param MC4WP_Form   $form
	 * @param iMC4WP_Request $request
	 */
	public function __construct( $receivers, MC4WP_Form $form, iMC4WP_Request $request ) {

		// make sure receivers is an array
		if( ! is_array( $receivers ) ) {
			$receivers = array_map( 'trim', explode( ',', $receivers ) );
		}

		$this->receivers = $receivers;
		$this->subject = __( 'New MailChimp Sign-Up', 'mailchimp-for-wp' ) . ' - ' . get_bloginfo( 'name' );
		$this->mailchimp = new MC4WP_MailChimp();
		$this->form = $form;
		$this->request = $request;

		// finally, build message string.
		$this->message = $this->build_message();
	}

	/**
	 * Build email message
	 *
	 * @return string
	 */
	public function build_message() {
		ob_start();

		?>
		<h3>MailChimp for WordPress: <?php _e( 'New Sign-Up', 'mailchimp-for-wp' ); ?></h3>
		<p><?php printf( __( '<strong>%s</strong> signed-up at %s on %s using the form "%s".', 'mailchimp-for-wp' ), $this->request->user_data['EMAIL'], date( get_option( 'time_format' ) ), date( get_option( 'date_format' ) ), $this->form->name ); ?></p>
		<table cellspacing="0" cellpadding="10" border="0" style="border: 1px solid #EEEEEE;">
			<tbody>
			<?php foreach( $this->request->map->list_fields as $list_id => $field_data ) { ?>
				<tr>
					<td colspan="2"><h4 style="border-bottom: 1px solid #efefef; margin-bottom: 0; padding-bottom: 5px;"><?php echo __( 'List', 'mailchimp-for-wp' ) . ': ' . $this->mailchimp->get_list_name( $list_id ); ?></h4></td>
				</tr>
				<tr>
					<td><strong><?php _e( 'Email address', 'mailchimp-for-wp' ); ?>:</strong></td>
					<td><?php echo $this->request->user_data['EMAIL']; ?></td>
				</tr>
				<?php
				foreach( $field_data as $field_tag => $field_value ) {

					if( $field_tag === 'GROUPINGS' && is_array( $field_value ) ) {

						foreach( $field_value as $grouping ) {

							$groups = implode( ', ', $grouping['groups'] ); ?>
							<tr>
								<td><strong><?php echo $this->mailchimp->get_list_grouping_name( $list_id, $grouping['id'] ); ?>:</strong></td>
								<td><?php echo esc_html( $groups ); ?></td>
							</tr>
						<?php
						}

					} else {
						$field_name = $this->mailchimp->get_list_field_name_by_tag( $list_id, $field_tag );

						// convert array values to comma-separated string value
						if( is_array( $field_value ) ) {
							$field_value = implode( ', ', $field_value );
						}
						?>
						<tr>
							<td><strong><?php echo esc_html( $field_name ); ?>:</strong></td>
							<td><?php echo esc_html( $field_value ); ?></td>
						</tr>
					<?php
					}
				} ?>
			<?php } ?>

			<?php if( count( $this->request->map->custom_fields ) > 0 ) { ?>
				<tr>
					<td colspan="2"><h4 style="border-bottom: 1px solid #efefef; margin-bottom: 0; padding-bottom: 5px;"><?php _e( 'Other fields', 'mailchimp-for-wp' ); ?></h4></td>
				</tr>
				<?php
				foreach( $this->request->map->custom_fields as $field_tag => $field_value ) {

					// convert array values to comma-separated string value
					if( is_array( $field_value ) ) {
						$field_value = implode( ', ', $field_value );
					}
					?>
					<tr>
						<td><strong><?php echo esc_html( $field_tag ); ?>:</strong></td>
						<td><?php echo esc_html( $field_value ); ?></td>
					</tr>
				<?php
				} ?>
			<?php } ?>

			</tbody>
		</table>
		<br />

		<p><?php printf( __( 'User subscribed from %s from IP %s.', 'mailchimp-for-wp' ), esc_html( $this->request->http_referer ), MC4WP_Tools::get_client_ip() ); ?></p>

		<?php  if( $this->form->settings['double_optin'] ) { ?>
			<p style="color:#666;"><?php printf( __( 'Note that you\'ve enabled double opt-in for the "%s" form. The user won\'t be added to the selected MailChimp lists until they confirm their email address.', 'mailchimp-for-wp' ), $this->form->name ); ?></p>
		<?php } ?>
		<p style="color:#666;"><?php _e( 'This email was auto-sent by the MailChimp for WordPress plugin.', 'mailchimp-for-wp' ); ?></p>
		<?php
		return ob_get_clean();
	}

	/**
	 * @return array|string
	 */
	public function get_receivers() {

		/**
		 * @filter mc4wp_email_summary_receiver
		 * @expects string|array String or array of emails
		 * @param   int     $form_id        The ID of the submitted form
		 * @param   string  $email          The email of the subscriber
		 * @param   array   $lists_data     Additional list fields, like FNAME etc (if any)
		 *
		 * Use to set email addresses to send the email summary to
		 */
		$receivers = apply_filters( 'mc4wp_email_summary_receiver', $this->receivers, $this->form->ID, $this->request->user_data['EMAIL'], $this->request->user_data );

		return $receivers;
	}

	/**
	 * @return string
	 */
	public function get_subject() {

		/**
		 * @filter mc4wp_email_summary_subject
		 * @expects string|array String or array of emails
		 * Use to set subject of email summaries
		 */
		$subject = (string) apply_filters( 'mc4wp_email_summary_subject', $this->subject, $this->form->ID, $this->request->user_data['EMAIL'], $this->request->user_data );

		return $subject;
	}

	/**
	 * @return string
	 */
	public function get_message() {

		/**
		 * @filter mc4wp_email_summary_message
		 * @expects string|array String or array of emails
		 * Use to set or customize message of email summaries
		 */
		$message = (string) apply_filters( 'mc4wp_email_summary_message', $this->message, $this->form->ID, $this->request->user_data['EMAIL'], $this->request->user_data );

		return $message;
	}

	/**
	 * Send email
	 */
	public function send() {
		// send email
		wp_mail( $this->get_receivers(), $this->get_subject(), $this->get_message(), 'Content-Type: text/html' );
	}

}