<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( ! class_exists( 'Tribe__Tickets_Plus__Commerce__Loader' ) ) {
	class Tribe__Tickets_Plus__Commerce__Loader {

		public $nag_data = array();

		/**
		 * @var Tribe__Tickets__Tickets[] 
		 */
		protected $commerce_providers = array();

		public function __construct() {
			add_action( 'admin_notices', array( $this, 'maybe_nagit' ) );

			$this->woocommerce();
			$this->easy_digital_downloads();
			$this->wpecommerce();
			$this->shopp();
		}

		/**
		 * Allows us to add only one action to the `admin_notices`
		 *
		 * @return void
		 */
		public function maybe_nagit() {
			if ( ! is_array( $this->nag_data ) ){
				return;
			}

			foreach ( $this->nag_data as $plugin => $data ) {
				call_user_func_array( array( $this, 'nag' ), $data );
			}
		}

		/**
		 * Check if WooCommerce is installed and active.
		 * If it is and the version is compatible, load our WooCommerce connector.
		 */
		public function woocommerce() {
			// Check if the legacy plugin exists
			if ( class_exists( 'Tribe__Events__Tickets__Woo__Main' ) ) {
				$args = array(
					'action' => 'deactivate',
					'plugin' => $this->get_plugin_file( 'The Events Calendar: WooCommerce Tickets' ),
					'plugin_status' => 'all',
					'paged' => 1,
					's' => '',
				);
				$deactivate_url = wp_nonce_url( add_query_arg( $args, 'plugins.php' ), 'deactivate-plugin_' . $args['plugin'] );

				$this->nag_data['woocommerce'] = array(
					__( 'WooCommerce', 'event-tickets-plus' ),
					$deactivate_url,
					'legacy-plugin',
				);

				return;
			}

			if ( ! $this->is_woocommerce_active() ) {
				return;
			}

			global $woocommerce;
			// Here we will check for Comptibility problems
			if ( ! version_compare( $woocommerce->version, Tribe__Tickets_Plus__Commerce__WooCommerce__Main::REQUIRED_WC_VERSION, '>=' ) ) {
				$this->nag_data['woocommerce'] = array(
					__( 'WooCommerce', 'event-tickets-plus' ),
					add_query_arg( array(
						'tab'       => 'plugin-information',
						'plugin'    => 'woocommerce',
						'TB_iframe' => 'true',
					), admin_url( 'plugin-install.php' ) ),
					'incompatible',
				);

				return;
			}

			$this->commerce_providers['woocommerce'] = Tribe__Tickets_Plus__Commerce__WooCommerce__Main::get_instance();
		}

		/**
		 * Check if EDD is installed and active.
		 * If it is and the version is compatible, load our EDD connector.
		 */
		public function easy_digital_downloads() {
			// Check if the legacy plugin exists
			if ( class_exists( 'Tribe__Events__Tickets__EDD__Main' ) ) {
				$args = array(
					'action' => 'deactivate',
					'plugin' => $this->get_plugin_file( 'The Events Calendar: EDD Tickets' ),
					'plugin_status' => 'all',
					'paged' => 1,
					's' => '',
				);
				$deactivate_url = wp_nonce_url( add_query_arg( $args, 'plugins.php' ), 'deactivate-plugin_' . $args['plugin'] );

				$this->nag_data['easy_digital_downloads'] = array(
					__( 'Easy Digital Downloads', 'event-tickets-plus' ),
					$deactivate_url,
					'legacy-plugin',
				);

				return;
			}

			if ( ! class_exists( 'Easy_Digital_Downloads' ) || ! defined( 'EDD_VERSION' ) ) {
				return;
			}

			// Here we will check for Comptibility problems
			if ( ! version_compare( EDD_VERSION, Tribe__Tickets_Plus__Commerce__EDD__Main::REQUIRED_EDD_VERSION, '>=' ) ) {
				$this->nag_data['easy_digital_downloads'] = array(
					__( 'Easy Digital Downloads', 'event-tickets-plus' ),
					add_query_arg( array(
						'tab'       => 'plugin-information',
						'plugin'    => 'easy-digital-downloads',
						'TB_iframe' => 'true',
					), admin_url( 'plugin-install.php' ) ),
					'incompatible',
				);

				return;
			}

			$this->commerce_providers['easy_digital_downloads'] = Tribe__Tickets_Plus__Commerce__EDD__Main::get_instance();
		}

		/**
		 * Check if WPEC is installed and active.
		 * If it is and the version is compatible, load our WPEC connector.
		 */
		public function wpecommerce() {
			// Check if the legacy plugin exists
			if ( class_exists( 'Tribe__Events__Tickets__Wpec__Main' ) ) {
				$args = array(
					'action' => 'deactivate',
					'plugin' => $this->get_plugin_file( 'The Events Calendar: WPEC Tickets' ),
					'plugin_status' => 'all',
					'paged' => 1,
					's' => '',
				);
				$deactivate_url = wp_nonce_url( add_query_arg( $args, 'plugins.php' ), 'deactivate-plugin_' . $args['plugin'] );

				$this->nag_data['wpecommerce'] = array(
					__( 'WP eCommerce', 'event-tickets-plus' ),
					$deactivate_url,
					'legacy-plugin',
				);

				return;
			}

			if ( ! class_exists( 'WP_eCommerce' ) ) {
				return;
			}

			// Here we will check for Comptibility problems
			if ( ! version_compare( WPSC_VERSION, Tribe__Tickets_Plus__Commerce__WPEC__Main::REQUIRED_WPEC_VERSION, '>=' )  ) {
				$this->nag_data['wpecommerce'] = array(
					__( 'WP eCommerce', 'event-tickets-plus' ),
					add_query_arg( array(
						'tab'       => 'plugin-information',
						'plugin'    => 'wp-e-commerce',
						'TB_iframe' => 'true',
					), admin_url( 'plugin-install.php' ) ),
					'incompatible',
				);

				return;
			}

			$this->commerce_providers['wpecommerce'] = Tribe__Tickets_Plus__Commerce__WPEC__Main::get_instance();
		}

		/**
		 * Check if Shopp is installed and active.
		 * If it is and the version is compatible, load our Shopp connector.
		 */
		public function shopp() {
			// Check if the legacy plugin exists
			if ( class_exists( 'Tribe__Events__Tickets__Shopp__Main' ) ) {
				$args = array(
					'action' => 'deactivate',
					'plugin' => $this->get_plugin_file( 'The Events Calendar: Shopp Tickets' ),
					'plugin_status' => 'all',
					'paged' => 1,
					's' => '',
				);
				$deactivate_url = wp_nonce_url( add_query_arg( $args, 'plugins.php' ), 'deactivate-plugin_' . $args['plugin'] );

				$this->nag_data['shopp'] = array(
					__( 'Shopp', 'event-tickets-plus' ),
					$deactivate_url,
					'legacy-plugin',
				);

				return;
			}

			$shoppVersion = false;

			if ( class_exists( 'ShoppVersion' ) ) {
				$shoppVersion = ShoppVersion::release();
			} // 1.3+
			elseif ( defined( 'SHOPP_VERSION' ) ) {
				$shoppVersion = SHOPP_VERSION;
			} // Pre-1.3

			if ( empty( $shoppVersion ) ) {
				return;
			}

			// Here we will check for Comptibility problems
			if ( ! version_compare( $shoppVersion, Tribe__Tickets_Plus__Commerce__Shopp__Main::REQUIRED_SHOPP_VERSION, '>=' ) ) {
				$this->nag_data['shopp'] = array(
					__( 'Shopp', 'event-tickets-plus' ),
					add_query_arg( array(
						'tab'       => 'plugin-information',
						'plugin'    => 'shopp',
						'TB_iframe' => 'true',
					), admin_url( 'plugin-install.php' ) ),
					'incompatible',
				);

				return;
			}

			$this->commerce_providers['shopp'] = Tribe__Tickets_Plus__Commerce__Shopp__Main::get_instance();
		}

		/**
		 * We need a way to fetch the file so that we can provide a Deactivate the link
		 *
		 * @param  string $plugin_name The plugin name as it's on the legacy Plugin
		 * @return string|null
		 */
		protected function get_plugin_file( $plugin_name ) {
			if ( ! function_exists( 'get_plugins' ) ) {
				require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
			}
			$plugins = get_plugins();
			foreach ( $plugins as $plugin_file => $plugin_info ) {
				if ( $plugin_info['Name'] == $plugin_name ){
					return $plugin_file;
				}
			}

			return null;
		}

		/**
		 * Whether at least one commerce provider is installed and activated or not.
		 *
		 * @return bool
		 */
		public function has_commerce_providers() {
			return count( $this->commerce_providers ) > 0;
		}

		/**
		 * Prints the HTML for the error we are talking about based on the arguments
		 *
		 * @param  string $plugin Name of the plugin
		 * @param  string $url    The url we are using
		 * @param  string $type   Currently only have 'incompatible' or 'legacy-plugin' types
		 * @return void
		 */
		protected function nag( $plugin, $url, $type = 'incompatible' ) {

			switch ( $type ) {
				case 'legacy-plugin':
					// Check if the user needs to see the message
					if ( ! current_user_can( 'activate_plugins' ) ) {
						return;
					}

					$nag = __( 'To begin using Tickets Plus with %3$s, you need to <a href="%1$s" title="Deactivate the legacy addon for %2$s">deactivate the old legacy plugin</a>.', 'event-tickets-plus' );
					break;

				case 'incompatible':
				default:
					// Check if the user needs to see the message
					if ( ! current_user_can( 'activate_plugins' ) ) {
						return;
					}

					$nag = __( 'To begin using Tickets Plus, please install and activate the latest version of <a href="%s" class="thickbox" title="%s">%s</a>.', 'event-tickets-plus' );
					break;
			}


			echo '<div class="error"><p>';

			printf( $nag, esc_url( $url ), esc_attr( $plugin ), esc_html( $plugin ) );

			echo '</p></div>';
		}

		/**
		 * Whether the WooCommerce plugin is installed and activated.
		 * @return bool
		 */
		public function is_woocommerce_active() {
			return class_exists( 'Woocommerce' );
		}
	}
}
