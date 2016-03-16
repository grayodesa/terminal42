<?php

/**
 * Class DVK_License_Manager
 *
 * Modified version of the Yoast License Manager class - https://github.com/Yoast/License-Manager
 *
 *
 * @todo Check if updates are retrieved for the correct item (does it use item name?)
 */
abstract class DVK_License_Manager {

	/**
	* @const VERSION The version number of the License_Manager class
	*/
	const VERSION = 10;

	/**
	 * @var DVK_Product The license
	 */
	public $product;

	/**
	* @var boolean True if remote license activation just failed
	*/
	protected $remote_license_activation_failed = false;

	/**
	* @var array Array of license related options
	*/
	public $options = array();

	/**
	 * @var bool Boolean indicating whether this plugin is network activated
	 */
	public $is_network_activated = false;

	/**
	 * Constructor
	 *
	 * @param DVK_Product $product
	 */
	public function __construct( DVK_Product $product ) {
		$this->product = $product;
	}

	abstract function specific_hooks();
	abstract function setup_auto_updater();


	/**
	 * Setup hooks
	 *
	 */
	public function setup_hooks() {

		// show admin notice if license is not active
		add_action( 'admin_notices', array( $this, 'display_admin_notices' ) );

		// catch general GET requests
		add_action( 'admin_init', array( $this, 'catch_get_request' ) );

		// catch POST requests from license form
		add_action( 'admin_init', array( $this, 'catch_post_request') );

		// setup item type (plugin|theme) specific hooks
		$this->specific_hooks();

		// setup the auto updater
		$this->setup_auto_updater();
	}

	/**
	* Display license specific admin notices, namely:
	*
	* - License for the product isn't activated
	* - External requests are blocked through WP_HTTP_BLOCK_EXTERNAL
	*/
	public function display_admin_notices() {

		// show notice if license is invalid
		if( ! $this->license_is_valid() ) {

			if( $this->get_license_key() == '' ) {
				$message = '<b>Warning!</b> You didn\'t set your %s license key yet, which means you\'re missing out on updates and support! <a href="%s">Enter your license key</a> or <a href="%s" target="_blank">get a license here</a>.';
			} else {
				$message = '<b>Warning!</b> Your %s license is inactive which means you\'re missing out on updates and support! <a href="%s">Activate your license</a> or <a href="%s" target="_blank">get a license here</a>.';
			}

			$on_settings_page = isset( $_GET['page'] ) && stripos( $_GET['page'], 'mailchimp-for-wp' ) === 0;

			if( $this->get_option( 'show_notice' ) !== false || $on_settings_page ) {
				$message = sprintf( __( $message, $this->product->text_domain ), $this->product->item_name, $this->product->license_page_url, $this->product->get_tracking_url( '/', 'activate-license-notice' ) );
				?>
				<div class="notice notice-warning" style="padding-right: 40px; position: relative;">
					<p><?php echo $message; ?></p>

					<?php if( ! $on_settings_page ) {
						echo '<a style="text-decoration: none;" class="notice-dismiss" href="'. wp_nonce_url( add_query_arg( array( $this->product->prefix . 'action' => 'dismiss_license_notice' ) ), $this->product->prefix . 'dismiss_license_notice' ) .'"><span class="screen-reader-text">'. __( 'Dismiss this notice.', 'mailchimp-for-wp' ) . '</span></a>';
					} ?>
				</div>
			<?php
			}
		}

		// show notice if external requests are blocked through the WP_HTTP_BLOCK_EXTERNAL constant
		if( defined( 'WP_HTTP_BLOCK_EXTERNAL' ) && WP_HTTP_BLOCK_EXTERNAL === true ) {

			// check if our API endpoint is in the allowed hosts
			$host = parse_url( $this->product->api_url, PHP_URL_HOST );

			if( ! defined( 'WP_ACCESSIBLE_HOSTS' ) || stristr( WP_ACCESSIBLE_HOSTS, $host ) === false ) {
				?>
				<div class="notice notice-warning">
					<p><?php printf( __( '<b>Warning!</b> You\'re blocking external requests which means you won\'t be able to get %s updates. Please add %s to %s.', $this->product->text_domain ), $this->product->item_name, '<strong>' . $host . '</strong>', '<code>WP_ACCESSIBLE_HOSTS</code>' ); ?></p>
				</div>
				<?php
			}

		}
	}

	/**
	* Set a notice to display in the admin area
	*
	* @param string $message The message to display
	 * @param bool $success
	*/
	protected function set_notice( $message, $success = true ) {
		$css_class = ( $success ) ? 'updated' : 'error';
		add_settings_error( $this->product->prefix . 'license', 'license-notice', $message, $css_class );
	}

	/**
	 * Remotely activate License
	 * @return boolean True if the license is now activated, false if not
	 */
	public function activate_license() {

		$result = $this->call_license_api( 'activate' );

		if( $result ) {

			// story expiry date
			if( isset( $result->expires ) ) {
				$this->set_license_expiry_date( $result->expires );
				$expiry_date = $this->get_license_expiry_date();
			} else {
				$expiry_date = false;
			}

			// show success notice if license is valid
			if( $result->license === 'valid' ) {

				// show a custom notice if users have an unlimited license
				if( $result->license_limit == 0 ) {
					$message = sprintf( __( 'Your %s license has been activated. You have an unlimited license. ', $this->product->text_domain ), $this->product->item_name );
				} else {
					$message = sprintf( __( 'Your %s license has been activated. You have used %d/%d activations. ', $this->product->text_domain ), $this->product->item_name, $result->site_count, $result->license_limit );
				}

				// add upgrade notice if user has less than 3 activations left
				if( $result->license_limit > 0 && ( $result->license_limit - $result->site_count ) <= 3 ) {
					$message .= sprintf( __( '<a href="%s">Did you know you can upgrade your license?</a>', $this->product->text_domain ), $this->product->get_tracking_url( '/licenses/', 'license-nearing-limit-notice' ) );
					// add extend notice if license is expiring in less than 1 month
				} elseif( $expiry_date !== false && $expiry_date < strtotime( '+1 month' ) ) {
					$days_left = round( ( $expiry_date - strtotime( 'now' ) ) / 86400 );
					$message .= sprintf( __( '<a href="%s">Your license is expiring in %d days. Would you like to renew it for another year?</a>', $this->product->text_domain ), 'https://mc4wp.com/checkout/?edd_license_key=' . esc_attr( $this->get_license_key() ), $days_left );
				}

				$this->set_notice( $message, true );

			} else {

				if( isset( $result->error ) && $result->error === 'no_activations_left' ) {
					// show notice if user is at their activation limit
					$this->set_notice( sprintf( __( 'You\'ve reached your activation limit. You must <a href="%s">reset</a> or <a href="%s">upgrade your license</a> to use it on this site.', $this->product->text_domain ), 'https://mc4wp.com/kb/resetting-license-activations/', $this->product->get_tracking_url( '/licenses/', 'license-at-limit-notice' ) ), false );
				} elseif( isset($result->error) && $result->error === 'expired' ) {
					// show notice if the license is expired
					$result->license = 'expired';
					$this->set_notice( sprintf( __( 'Your license has expired. You must <a href="%s">renew your license</a> if you want to use it again.', $this->product->text_domain ), 'https://mc4wp.com/checkout/?edd_license_key=' . esc_attr( $this->get_license_key() ), false ) );
				} else {
					// show a general notice if it's any other error
					$this->set_notice( __( 'Failed to activate your license as your license key seems to be invalid.', $this->product->text_domain ), false );
				}

				$this->remote_license_activation_failed = true;
			}

			$this->set_license_status( $result->license );
		}

		return ( $this->license_is_valid() );
	}

	/**
	 * Remotely deactivate License
	 * @return boolean True if the license is now deactivated, false if not
	 */
	public function deactivate_license () {

		$result = $this->call_license_api( 'deactivate' );

		if( $result ) {

			// show notice if license is deactivated
			if( $result->license === 'deactivated' ) {
				$this->set_license_status( $result->license );
				$this->set_notice( sprintf( __( 'Your %s license has been deactivated.', $this->product->text_domain ), $this->product->item_name ) );
				// deactivation failed, check if license has expired
			} elseif( ! empty( $result->expires ) && strtotime( 'now' ) > strtotime( $result->expires ) ) {
				$this->set_license_status( 'expired' );
				$this->set_notice( sprintf( __( 'Your plugin license has expired. You will no longer have access to plugin updates unless you <a href="%s">renew your license</a>.', 'mailchimp-for-wp' ), 'https://mc4wp.com/checkout/?edd_license_key=' . esc_attr( $this->get_license_key() ) ) );
			} else {
				$this->set_notice( sprintf( __( 'Failed to deactivate your %s license.', $this->product->text_domain ), $this->product->item_name ), false );
			}

		}

		return ( $this->get_license_status() === 'deactivated' );
	}

	/**
	* @param string $action activate|deactivate
	* @return mixed
	*/
	protected function call_license_api( $action ) {

		// don't make a request if license key is empty
		if( $this->get_license_key() === '' ) {
			return false;
		}

		// data to send in our API request
		$api_params = array(
			'edd_action' => $action . '_license',
			'license'    => $this->get_license_key(),
			'url' => ( $this->is_network_activated ) ? network_site_url() : get_option( 'home' ),
			'item_id' => $this->product->item_id,
		);

		// setup request parameters
		$request_params = array(
			'method' => 'POST',
			'body'      => $api_params
		);

		require_once dirname( __FILE__ ) . '/class-api-request.php';
		$request = new DVK_API_Request( $this->product->api_url, $request_params );

		if( $request->is_valid() !== true ) {

			$notice = __( 'Request error', $this->product->text_domain ) . sprintf( ': "%s"', $request->get_error_message() );

			$this->set_notice( $notice, false );
		}

		// get response
		$response = $request->get_response();

		// update license status
		$license_data = $response;

		return $license_data;
	}



	/**
	* Set the license status
	*
	* @param string $license_status
	*/
	public function set_license_status( $license_status ) {
		$this->set_option( 'status', $license_status );
	}

	/**
	* Get the license status
	*
	* @return string $license_status;
	*/
	public function get_license_status() {
		$license_status = $this->get_option( 'status' );
		return trim( $license_status );
	}

	/**
	* Set the license key
	*
	* @param string $license_key
	*/
	public function set_license_key( $license_key ) {
		$this->set_option( 'key', $license_key );
	}

	/**
	* Gets the license key from option
	*
	* @return string $license_key
	*/
	public function get_license_key() {
		$license_key = $this->get_option( 'key' );
		return trim( $license_key );
	}

	/**
	* Gets the license expiry date
	*
	* @return int
	*/
	public function get_license_expiry_date() {
		$expiry_date = $this->get_option( 'expiry_date' );
		return $expiry_date;
	}

	/**
	 * Stores the license expiry date
	 * @param int $expiry_date
	 */
	public function set_license_expiry_date( $expiry_date ) {

		if( ! is_numeric( $expiry_date ) ) {
			$expiry_date =  strtotime( $expiry_date );
		}

		$this->set_option( 'expiry_date', $expiry_date );
	}

	/**
	* Checks whether the license status is active
	*
	* @return boolean True if license is active
	*/
	public function license_is_valid() {
		return ( $this->get_license_status() === 'valid' );
	}

	/**
	* Get all license related options
	*
	* @return array Array of license options
	*/
	protected function get_options() {

		// create option name
		$option_name = $this->product->prefix . 'license';

		// get array of options from db
		if( $this->is_network_activated ) {
			$options = get_site_option( $option_name, array( ) );
		} else {
			$options = get_option( $option_name, array( ) );
		}

		// setup array of defaults
		$defaults = array(
			'key' => '',
			'status' => '',
			'expiry_date' => '',
			'show_notice' => true
		);

		// merge options with defaults
		$this->options = array_merge( $defaults, $options );

		return $this->options;
	}

	/**
	* Set license related options
	*
	* @param array $options Array of new license options
	*/
	protected function set_options( array $options ) {
		// create option name
		$option_name = $this->product->prefix . 'license';

		// update db
		if( $this->is_network_activated ) {
			update_site_option( $option_name, $options );
		} else {
			update_option( $option_name, $options );
		}

	}

	/**
	* Gets a license related option
	*
	* @param string $name The option name
	* @return mixed The option value
	*/
	protected function get_option( $name ) {
		$options = $this->get_options();
		return $options[ $name ];
	}

	/**
	* Set a license related option
	*
	* @param string $name The option name
	* @param mixed $value The option value
	*/
	protected function set_option( $name, $value ) {
		// get options
		$options = $this->get_options();

		// update option
		$options[ $name ] = $value;

		// save options
		$this->set_options( $options );
	}

	/**
	* Show a form where users can enter their license key
	*
	* @param boolean $embedded Boolean indicating whether this form is embedded in another form?
	*/
	public function show_license_form( $embedded = true ) {

		$key_name = $this->product->prefix . 'license_key';
		$nonce_name = $this->product->prefix . 'license_nonce';
		$action_name = $this->product->prefix . 'license_action';
		$visible_license_key = $this->get_license_key();

		// obfuscate license key
		$obfuscate = ( strlen( $this->get_license_key() ) > 5 && ( $this->license_is_valid() || ! $this->remote_license_activation_failed ) && $this->get_license_status() !== 'expired' );

		if($obfuscate) {
			$visible_license_key = str_repeat( '*', strlen( $this->get_license_key() ) - 4 ) . substr( $this->get_license_key(), -4 );
		}

		// make license key readonly when license key is valid
		$readonly = ( $this->license_is_valid() );

		require dirname( __FILE__ ) . '/../views/form.php';

		// enqueue script in the footer
		add_action( 'admin_footer', array( $this, 'output_script'), 99 );
	}

	/**
	 * Perform actions on some GET requests
	 */
	public function catch_get_request() {

		// only act on $prefix_action
		if( ! isset( $_GET[ $this->product->prefix . 'action' ] ) ) {
			return;
		}

		// Get action
		$action = $_GET[ $this->product->prefix . 'action' ];

		// make sure we're coming from an admin page
		if( ! check_admin_referer( $this->product->prefix . $action ) ) {
			return;
		}

		switch( $action ) {
			case 'dismiss_license_notice':
				$this->set_option( 'show_notice', false );
				break;
		}


	}

	/**
	* Check if the license form has been submitted
	*/
	public function catch_post_request() {

		$name = $this->product->prefix . 'license_key';

		// check if license key was posted and not empty
		if( ! isset( $_POST[$name] ) ) {
			return;
		}

		// run a quick security check
		$nonce_name = $this->product->prefix . 'license_nonce';

		if ( ! check_admin_referer( $nonce_name, $nonce_name ) ) {
			return;
		}

		if( ! current_user_can( 'manage_options' ) ) {
            return;
		}

		// get key from posted value
		$license_key = $_POST[$name];

		// check if license key doesn't accidentally contain asterisks
		if( strstr( $license_key, '*' ) === false ) {

			// sanitize key
			$license_key = sanitize_text_field( $_POST[$name] );

			// save license key
			$this->set_license_key( $license_key );

			// does user have an activated valid license
			if( ! $this->license_is_valid() ) {

				// try to auto-activate license
				return $this->activate_license();

			}
		}

		$action_name = $this->product->prefix . 'license_action';

		// was one of the action buttons clicked?
		if( isset( $_POST[ $action_name ] ) ) {

			$action = trim( $_POST[ $action_name ] );

			switch($action) {

				case 'activate':
					return $this->activate_license();
					break;

				case 'deactivate':
					return $this->deactivate_license();
					break;
			}

		}

	}

	/**
	* Output the script containing the YoastLicenseManager JS Object
	*
	* This takes care of disabling the 'activate' and 'deactivate' buttons
	*/
	public function output_script() {
		include dirname( __FILE__ ) . '/../views/script.php';
	}

}

