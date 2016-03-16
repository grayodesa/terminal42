<?php
if ( ! class_exists( 'DVK_Plugin_License_Manager', false ) ) {

	class DVK_Plugin_License_Manager extends DVK_License_Manager {

		/**
		 * Constructor
		 *
		 * @param DVK_Product $product
		 */
		public function __construct( DVK_Product $product ) {

			parent::__construct( $product );

			// Check if plugin is network activated. We should use site(wide) options in that case.
			if( is_admin() && is_multisite() ) {

				if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
					require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
				}

				$this->is_network_activated = is_plugin_active_for_network( $product->plugin_basename );
			}
		}

		/**
		 * Setup auto updater for plugins
		 */
		public function setup_auto_updater() {
			if ( $this->license_is_valid() ) {
				// setup auto updater
				require_once( dirname( __FILE__ ) . '/class-update-manager.php' );
				require_once( dirname( __FILE__ ) . '/class-plugin-update-manager.php' );
				new DVK_Plugin_Update_Manager( $this->product, $this );
			}
		}

		/**
		 * Setup hooks
		 */
		public function specific_hooks() {
			add_action( 'after_plugin_row_' . $this->product->plugin_basename, array( $this, 'after_plugin_row' ), 10, 2 );

			// deactivate the license remotely on plugin deactivation
			register_deactivation_hook( $this->product->slug, array( $this, 'deactivate_license' ) );
		}

		/**
		 * @param string $file
		 * @param array $plugin_data
		 */
		public function after_plugin_row( $file, $plugin_data ) {

			// Don't show if license is valid
			if( $this->license_is_valid() ) {
				return;
			}

			// Output row with message telling people to activate their license
			$id = sanitize_title( $plugin_data['Name'] );
			echo '<tr class="plugin-update-tr active">';
			echo '<td colspan="3" class="plugin-update colspanchange">';
			echo '<div style="padding: 6px 12px; margin: 0 10px 8px 31px; background: lightYellow;">';
			printf( __( '<a href="%s">Register your copy</a> of <strong>%s</strong> to receive access to automatic upgrades and support. Need a license key? <a href="%s">Purchase one now</a>.', $this->product->text_domain ), $this->product->license_page_url, $this->product->item_name, $this->product->get_tracking_url( '/checkout/', 'plugins_page' ) );
			echo '</div></td></tr>';

			// Disable bottom border on parent row
			echo '<style scoped="scoped">';
			echo sprintf( "#%s td, #%s th { box-shadow: none !important; }", $id, $id );
			echo '</style>';
		}
	}
}

