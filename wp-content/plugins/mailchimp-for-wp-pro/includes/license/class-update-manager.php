<?php

if( ! class_exists( 'DVK_Update_Manager', false ) ) {

	class DVK_Update_Manager {

		/**
		 * @var DVK_Product
		 */
		protected $product;

		/**
		 * @var DVK_License_Manager
		 */
		protected $license_manager;

		/**
		 * @var string
		 */
		protected $error_message = '';

		/**
		 * @var object
		 */
		protected $update_response = null;

		/**
		 * @param DVK_Product $product
		 * @param DVK_License_Manager $license_manager
		 */
		public function __construct( DVK_Product $product, DVK_License_Manager $license_manager ) {

			$this->product = $product;
			$this->license_manager = $license_manager;

			// maybe delete transient
			$this->maybe_delete_transients();
		}

		/**
		 * Deletes the various transients
		 * If we're on the update-core.php?force-check=1 page
		 */
		private function maybe_delete_transients() {
			global $pagenow;

			if( $pagenow === 'update-core.php' && isset( $_GET['force-check'] ) ) {
				delete_site_transient( $this->product->prefix . 'update-response' );
				delete_site_transient( $this->product->prefix . 'update-request-failed' );
			}
		}

		/**
		 * If the update check returned a WP_Error, show it to the user
		 */
		public function show_error() {

			if ( $this->error_message === '' ) {
				return;
			}

			?>
			<div class="error">
				<p><?php printf( __( '%s failed to check for updates because of the following error: <em>%s</em>', $this->product->text_domain ), $this->product->item_name, $this->error_message ); ?></p>
			</div>
			<?php
		}

		/**
		 * @param string $error_message
		 */
		protected function schedule_error( $error_message ) {
			$this->error_message = $error_message;
			add_action( 'admin_notices', array( $this, 'show_error' ) );
		}

		/**
		 * Calls the API and, if successfull, returns the object delivered by the API.
		 *
		 * @uses         get_bloginfo()
		 * @uses         wp_remote_post()
		 * @uses         is_wp_error()
		 *
		 * @return false||object
		 */
		private function call_remote_api() {

			// only check if the failed transient is not set (or if it's expired)
			if( get_site_transient( $this->product->prefix . 'update-request-failed' ) !== false ) {
				return false;
			}

			// set a transient to prevent failed update checks on every page load
			// this transient will be removed if a request succeeds
			set_site_transient( $this->product->prefix . 'update-request-failed', 'failed', 10800 );

			// setup api parameters
			$api_params = array(
				'edd_action' => 'get_version',
				'license'    => $this->license_manager->get_license_key(),
				'item_version'     => $this->product->version,
				'url' =>  ( $this->license_manager->is_network_activated ) ? network_site_url() : get_option( 'home' ),
			);

			$url = add_query_arg( $api_params, $this->product->api_url );

			require_once dirname( __FILE__ ) . '/class-api-request.php';
			$request = new DVK_API_Request( $url );

			if( $request->is_valid() !== true ) {
				$this->schedule_error( $request->get_error_message() );
				return false;
			}

			// request succeeded, delete transient indicating a request failed
			delete_site_transient( $this->product->prefix . 'update-request-failed' );

			// decode response
			$response = $request->get_response();

			// check if response returned that a given site was inactive
			if( isset( $response->license_check ) && ! empty( $response->license_check ) && (string) $response->license_check !== 'valid' ) {

				// deactivate local license
				$this->license_manager->set_license_status( 'invalid' );

				// show notice to let the user know we deactivated his/her license
				$message = __( 'This site has not been activated properly on mc4wp.com and so cannot check for updates. Please activate your site with a valid license key.', $this->product->text_domain );
				$this->schedule_error( $message );
				return false;
			}

			// update license expiration for renewed license
			// todo: take lifetime licenses into account (we're not using that yet, but for future ref)
			if( ! empty( $response->license_expiration ) ) {
				$this->license_manager->set_license_expiry_date( $response->license_expiration );
			}

			// add slug so we can omit it in the request
			$response->slug = $this->product->slug;
			$response->sections = maybe_unserialize( $response->sections );
			$response->banners = (array) $response->banners;
			$response->plugin = $this->product->plugin_basename;

			// store response
			set_site_transient( $this->product->prefix . 'update-response', $response, 10800 );

			return $response;
		}

		/**
		 * Gets the remote product data (from the EDD API)
		 *
		 * - If it was previously fetched in the current requests, this gets it from the instance property
		 * - Next, it tries the 3-hour transient
		 * - Next, it calls the remote API and stores the result
		 *
		 * @return object
		 */
		protected function get_remote_data() {

			// always use property if it's set
			if( null !== $this->update_response ) {
				return $this->update_response;
			}

			// get cached remote data
			$data = $this->get_cached_remote_data();

			// if cache is empty or expired, call remote api
			if( $data === false ) {
				$data = $this->call_remote_api();
			}

			$this->update_response = $data;
			return $data;
		}

		/**
		 * Gets the remote product data from a 3-hour transient
		 *
		 * @return bool|mixed
		 */
		private function get_cached_remote_data() {

			$data = get_site_transient( $this->product->prefix . 'update-response' );

			if( $data ) {
				return $data;
			}

			return false;
		}

	}

}