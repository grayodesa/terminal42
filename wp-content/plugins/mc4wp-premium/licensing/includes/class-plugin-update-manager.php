<?php

//set_site_transient( 'update_plugins', null );

if( ! class_exists( 'DVK_Plugin_Update_Manager', false ) ) {

	class DVK_Plugin_Update_Manager extends DVK_Update_Manager {

		/**
		 * Constructor
		 *
		 * @param DVK_Product         $product
		 * @param DVK_License_Manager $license_manager
		 */
		public function __construct( DVK_Product $product, DVK_License_Manager $license_manager ) {
			parent::__construct( $product, $license_manager );

			// setup hooks
			$this->setup_hooks();

		}

		/**
		* Setup hooks
		*/
		private function setup_hooks() {

			// check for updates
			add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'set_updates_available_data' ) );

			// get correct plugin information (when viewing details)
			add_filter( 'plugins_api', array( $this, 'plugins_api_filter' ), 10, 3 );
		}

		/**
		* Check for updates and if so, add to "updates available" data
		*
		* @param object $data
		* @return object $data
		*/
		public function set_updates_available_data( $data ) {

			if ( empty( $data ) ) {
				return $data;
			}

			// send of API request to check for updates
			$remote_data = $this->get_remote_data();

			// did we get a response?
			if( ! isset( $remote_data->new_version ) ) {
				return $data;
			}

			// if local version is higher or equal to remote version, do nothing
			if ( version_compare( $this->product->version, $remote_data->new_version, '>=' ) ) {
				return $data;
			}

			// add remote version to data
			$data->response[ $this->product->plugin_basename ] = $remote_data;

			return $data;
		}

		/**
		 * Gets new plugin version details (view version x.x.x details)
		 *
		 * @uses api_request()
		 *
		 * @param object $data
		 * @param string $action
		 * @param object $args (optional)
		 *
		 * @return object $data
		 */
		public function plugins_api_filter( $data, $action, $args ) {

			// only do something if we're checking for our plugin
			if ( $action !== 'plugin_information' || $args->slug !== $this->product->slug ) {
				return $data;
			}

			$api_response = $this->get_remote_data();

			// did we get a response?
			if( ! is_object( $api_response ) ) {
				return $data;
			}

			// return api response
			return $api_response;
		}

	}

}