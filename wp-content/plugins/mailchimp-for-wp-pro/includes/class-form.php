<?php


class MC4WP_Form {

	/**
	 * @var array
	 */
	private static $instances = array();

	/**
	 * @param int|WP_post $post
	 * @param iMC4WP_Request $request
	 * @return MC4WP_Form|null
	 */
	public static function get( $post, iMC4WP_Request $request = null ) {

		if( ! is_object( $post) ) {
			$post = get_post( $post );
		}

		// create new instance
		if( is_object( $post )
		    && isset( $post->post_type )
		    && $post->post_type === 'mc4wp-form' ) {

			// has instance been created already?
			if( isset( self::$instances[ $post->ID ] ) ) {
				$form = self::$instances[ $post->ID ];
			} else {
				// create a new instance
				$form = new MC4WP_Form( $post, $request );
				self::$instances[ $post->ID ] = $form;
			}

			// attach request to form
			if( $request && ! $form->has( $request ) ) {
				$form->attach( $request );
			}

			return $form;
		}

		return null;
	}

	/**
	 * @var int
	 */
	public $ID = 0;

	/**
	 * @var string
	 */
	public $content = '';

	/**
	 * @var array
	 */
	public $settings = array();

	/**
	 * @var WP_Post
	 */
	protected $_post;

	/**
	 * @var iMC4WP_Request
	 */
	public $request;

	/**
	 * @param WP_Post $post
	 * @param iMC4WP_Request $request
	 */
	private function __construct( WP_Post $post, iMC4WP_Request $request = null ) {
		$this->_post = $post;
		$this->ID = $post->ID;
		$this->name = $post->post_title;
		$this->content = $post->post_content;
		$this->settings = $this->load_settings( true );
		$this->request = $request;
	}

	/**
	 * Simple check to see if form contains a given field type
	 *
	 * @param $field_type
	 *
	 * @return bool
	 */
	public function contains_field_type( $field_type ) {
		$html = sprintf( ' type="%s" ', $field_type );
		return stristr( $this->content, $html ) !== false;
	}


	/**
	 * @param string $element_id
	 * @param array $attributes
	 * @param string $response_html
	 * @return string
	 */
	public function get_visible_fields( $element_id, array $attributes = array(), $response_html = '' ) {

		$replacements = array(
			'{n}' => $element_id,
			'{response}' => $response_html,
		);

		$visible_fields = MC4WP_Tools::replace_variables( $this->content, $replacements, array_values( $this->settings['lists'] ) );

		// insert captcha
		if( function_exists( 'cptch_display_captcha_custom' ) ) {
			$captcha_fields = '<input type="hidden" name="_mc4wp_has_captcha" value="1" /><input type="hidden" name="cntctfrm_contact_action" value="true" />' . cptch_display_captcha_custom();
			$visible_fields = str_ireplace( array( '{captcha}', '[captcha]' ), $captcha_fields, $visible_fields );
		}

		/**
		 * @filter mc4wp_form_content
		 * @param int $form_id The ID of the form that is being shown
		 * @expects string
		 *
		 * Can be used to customize the content of the form mark-up, eg adding additional fields.
		 */
		$visible_fields = (string) apply_filters( 'mc4wp_form_content', $visible_fields, $this->ID );

		return $visible_fields;
	}

	/**
	 * @param string $element_id
	 * @param array $attributes Attributes passed to the shortcode
	 * @return string
	 */
	public function get_hidden_fields( $element_id, $attributes = array() ) {

		// hidden fields
		$hidden_fields = '<div style="display: none;"><input type="text" name="_mc4wp_ho_'. md5( time() ).'" value="" tabindex="-1" autocomplete="off" /></div>';
		$hidden_fields .= '<input type="hidden" name="_mc4wp_timestamp" value="'. time() . '" />';
		$hidden_fields .= '<input type="hidden" name="_mc4wp_form_id" value="'. esc_attr( $this->ID ) .'" />';
		$hidden_fields .= '<input type="hidden" name="_mc4wp_form_element_id" value="'. esc_attr( $element_id ) .'" />';
		$hidden_fields .= '<input type="hidden" name="_mc4wp_form_submit" value="1" />';
		$hidden_fields .= '<input type="hidden" name="_mc4wp_form_nonce" value="'. wp_create_nonce( '_mc4wp_form_nonce' ) .'" />';


		// was "lists" parameter passed in shortcode arguments?
		if( isset( $attributes['lists'] ) && ! empty( $attributes['lists'] ) ) {
			$lists_string = ( is_array( $attributes['lists'] ) ) ? join( ',', $attributes['lists'] ) : $attributes['lists'];
			$hidden_fields .= '<input type="hidden" name="_mc4wp_lists" value="'. esc_attr( $lists_string ) . '" />';
		}

		return (string) $hidden_fields;
	}

	/**
	 * Is this form submitted?
	 * @param string $element_id
	 * @return bool
	 */
	public function is_submitted( $element_id = null ) {

		// is this form (any instance) submitted)
		$form_submitted = $this->request instanceof iMC4WP_Request;

		// if an element ID is given, only return true if that specific element is submitted
		if( $element_id ) {
			return ( $form_submitted && $this->request->form_element_id == $element_id );
		}

		return $form_submitted;
	}

	/**
	 * @return string
	 */
	protected function get_response_position() {

		/**
		 * @deprecated
		 * @use `mc4wp_form_response_position` instead
		 */
		$message_position = (string) apply_filters( 'mc4wp_form_message_position', 'after' );

		/**
		 * @filter mc4wp_form_message_position
		 * @expects string before|after
		 *
		 * Can be used to change the position of the form success & error messages.
		 * Valid options are 'before' or 'after'
		 */
		$response_position = (string) apply_filters( 'mc4wp_form_response_position', $message_position );

		// check if content contains {response} tag
		if( stripos( $this->content, '{response}' ) !== false ) {
			$response_position = '';
		}

		return $response_position;
	}

	/**
	 * @param string $response_html
	 * @return string
	 */
	protected function get_html_before_form( $response_html = '' ) {
		$html = (string) apply_filters( 'mc4wp_form_before_form', '', $this );

		if( $this->get_response_position() === 'before' ) {
			$html = $html . $response_html;
		}

		return $html;
	}

	/**
	 * @param string $response_html
	 * @return string
	 */
	protected function get_html_after_form( $response_html = '' ) {
		$html = (string) apply_filters( 'mc4wp_form_after_form', '', $this );

		if( $this->get_response_position() === 'after' ) {
			$html = $response_html . $html;
		}

		return $html;
	}

	/**
	 * Get the `action` attribute of the form element.
	 *
	 * @return string
	 */
	protected function get_form_action_attribute() {

		/**
		 * @filter `mc4wp_form_action`
		 * @expects string
		 * @param MC4WP_Form $form
		 */
		$form_action = apply_filters( 'mc4wp_form_action', null, $this );

		if( is_string( $form_action ) ) {
			$form_action_attribute = sprintf( 'action="%s"', esc_attr( $form_action ) );
		} else {
			$form_action_attribute = '';
		}

		return $form_action_attribute;
	}

	/**
	 * @param string $element_id
	 * @param array $attributes
	 * @return string
	 */
	public function generate_html( $element_id = 'mc4wp-form', array $attributes = array() ) {

		// generate response html
		$response_html = ( $this->is_submitted( $element_id ) ) ? $this->request->get_response_html() : '';

		// Some vars we might fill later on
		$form_opening_html = '';
		$form_closing_html = '';
		$visible_fields = '';
		$hidden_fields = '';

		// add HTML that's needed for AJAX
		if( $this->settings['ajax'] ) {
			$response_html .= '<div class="mc4wp-response"></div>';
		}

		// generate before & after HTML
		$opening_html = '<!-- MailChimp for WordPress Pro v' . MC4WP_VERSION . ' - https://mc4wp.com/ -->';
		$opening_html .= '<div id="' . esc_attr( $element_id ) . '" class="' . esc_attr( $this->get_css_classes( $element_id ) ) . '">';
		$before_fields = apply_filters( 'mc4wp_form_before_fields', '' );
		$after_fields = apply_filters( 'mc4wp_form_after_fields', '' );
		$before_form = $this->get_html_before_form( $response_html );
		$after_form = $this->get_html_after_form( $response_html );
		$closing_html = '</div><!-- / MailChimp for WP Pro Plugin -->';

		// only generate form & fields HTML if necessary
		if( ! $this->is_submitted( $element_id )
		    || ! $this->settings['hide_after_success']
		    || ! $this->request->success ) {

			$form_opening_html = '<form method="post" '. $this->get_form_action_attribute() .'>';
			$visible_fields = $this->get_visible_fields( $element_id, $attributes, $response_html );
			$hidden_fields .= $this->get_hidden_fields( $element_id, $attributes );
			$form_closing_html = '</form>';
		}



		ob_start();

		// echo HTML parts of form
		echo $opening_html;
		echo $before_form;
		echo $form_opening_html;
		echo $before_fields;
		echo $visible_fields;
		echo $hidden_fields;
		echo $after_fields;
		echo $form_closing_html;
		echo $after_form;
		echo $closing_html;

		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}

	/**
	 * @param string $element_id
	 * @param array $attributes
	 * @param bool  $echo
	 *
	 * @return string
	 */
	public function output( $element_id = 'mc4wp-form', array $attributes = array(), $echo = true ) {

		$html = $this->generate_html( $element_id, $attributes );

		if( $echo ) {
			echo $html;
		}

		return $html;
	}


	/**
	 * Get a space separated list of CSS classes for this form
	 *
	 * @param string $element_id
	 * @return string
	 */
	public function get_css_classes( $element_id ) {

		/**
		 * @filter mc4wp_form_css_classes
		 * @expects array
		 *
		 * Can be used to add additional CSS classes to the form container
		 */
		$css_classes = apply_filters( 'mc4wp_form_css_classes', array( 'form' ) );

		// the following classes MUST be used
		$css_classes[] = 'mc4wp-form';
		$css_classes[] = 'mc4wp-form-' . $this->ID;

		if( $this->settings['ajax'] ) {
			$css_classes[] = 'mc4wp-ajax';
		}

		// Add form classes if this specific form instance was submitted
		if( $this->is_submitted( $element_id ) ) {

			$css_classes[] = 'mc4wp-form-submitted';

			if( $this->request->success ) {
				$css_classes[] = 'mc4wp-form-success';
			} else {
				$css_classes[] = 'mc4wp-form-error';
			}

		}

		return implode( ' ', $css_classes );
	}

	/**
	 * @param bool $inherit
	 *
	 * @return array
	 */
	public function load_settings( $inherit = false ) {
		$inherited_settings = ( $inherit ) ? mc4wp_get_options( 'form' ) : array();
		$form_settings = array();

		// set defaults
		$form_settings['lists'] = array();
		$form_settings['email_copy_receiver'] = get_bloginfo( 'admin_email' );

		// fill optional meta keys with empty strings
		$optional_meta_keys = array( 'double_optin', 'update_existing', 'replace_interests', 'send_welcome', 'ajax', 'hide_after_success', 'redirect', 'text_subscribed', 'text_error', 'text_invalid_email', 'text_already_subscribed', 'send_email_copy', 'text_invalid_captcha', 'text_required_field_missing', 'text_unsubscribed', 'text_not_subscribed' );
		foreach( $optional_meta_keys as $meta_key ) {
			if( $inherit ) {
				$form_settings[$meta_key] = $inherited_settings[$meta_key];
			} else {
				$form_settings[$meta_key] = '';
			}
		}

		$meta = get_post_meta( $this->ID, '_mc4wp_settings', true );
		if( is_array( $meta ) ) {
			foreach($meta as $key => $value) {
				// only add meta value if not empty
				if( $value != '' ) {
					$form_settings[$key] = $value;
				}
			}
		}

		return $form_settings;
	}

	/**
	 * Returns the various error and success messages in array format
	 *
	 * Example:
	 * array(
	 *      'invalid_email' => array(
	 *          'type' => 'css-class',
	 *          'text' => 'Message text'
	 *      ),
	 *      ...
	 * );
	 *
	 * @return array
	 */
	public function get_messages() {

		$messages = array(
			// email was successfully subscribed to the selected list(s)
			'subscribed' => array(
				'type' => 'success',
				'text' => $this->settings['text_subscribed'],
			),

			// email was successfully unsubscribed from the selected list(s)
			'unsubscribed' => array(
				'type' => 'success',
				'text' => $this->settings['text_unsubscribed'],
			),

			// a general (unknown) error occurred
			'error' => array(
				'type' => 'error',
				'text' => $this->settings['text_error'],
			),

			// an invalid email was given
			'invalid_email' => array(
				'type' => 'error',
				'text' => $this->settings['text_invalid_email'],
			),

			// the captcha was not filled correctly
			'invalid_captcha' => array(
				'type' => 'error',
				'text' => $this->settings['text_invalid_captcha'],
			),

			// a required field is missing for the selected list(s)
			'required_field_missing' => array(
				'type' => 'error',
				'text' => $this->settings['text_required_field_missing'],
			),

			// email is already subscribed to the selected list(s)
			'already_subscribed' => array(
				'type' => 'notice',
				'text' => $this->settings['text_already_subscribed'],
			),

			// email is not subscribed on the selected list(s)
			'not_subscribed' => array(
				'type' => 'notice',
				'text' => $this->settings['text_not_subscribed'],
			),
		);

		// add some admin-only messages
		if( current_user_can( 'manage_options' ) ) {
			$messages['no_lists_selected'] = array(
				'type' => 'notice',
				'text' => sprintf( __( 'You did not select a list in <a href="%s">your form settings</a>.', 'mailchimp-for-wp' ), admin_url( 'admin.php?page=mailchimp-for-wp-form-settings' ) )
			);
		}

		/**
		 * @filter mc4wp_form_messages
		 * @expects array
		 *
		 * Allows registering custom form messages, useful if you're using custom validation using the `mc4wp_valid_form_request` filter.
		 */
		$messages = apply_filters( 'mc4wp_form_messages', $messages, $this->ID );

		return (array) $messages;
	}

	/**
	 * @param iMC4WP_Request $request
	 *
	 * @return bool
	 */
	public function has( iMC4WP_Request $request ) {
		return $this->request === $request;
	}

	/**
	 * @param iMC4WP_Request $request
	 */
	public function attach( iMC4WP_Request $request ) {
		$this->request = $request;
	}

}