<?php
/**
 * Adds WooCommerce-specific settings to the tickets settings screen.
 */
class Tribe__Tickets_Plus__Commerce__WooCommerce__Settings {
	public function __construct() {
		add_filter( 'tribe_tickets_settings_tab_fields', array( $this, 'add_settings' ) );
	}

	public function add_settings( array $settings_fields ) {
		$extra_settings = $this->additional_settings();
		return Tribe__Main::array_insert_before_key( 'tribe-form-content-end', $settings_fields, $extra_settings );
	}

	protected function additional_settings() {
		$label = esc_html__( 'Try to set the status of new ticket orders to "complete" automatically', 'event-tickets' );

		return array(
			'tickets-woo-options-title' => array(
				'type' => 'html',
				'html' => '<h3>' . esc_html__( 'WooCommerce Support', 'event-tickets' ) . '</h3>',
			),
			'tickets-woo-autocomplete' => array(
				'type'            => 'checkbox_bool',
				'default'         => false,
				'validation_type' => 'boolean',
				'label'           => $label,
			),
		);
	}
}