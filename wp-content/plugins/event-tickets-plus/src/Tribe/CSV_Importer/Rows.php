<?php


/**
 * Class Tribe__Tickets_Plus__CSV_Importer__Rows
 *
 * Modifies the CSV Importer import option rows.
 */
class Tribe__Tickets_Plus__CSV_Importer__Rows {

	/**
	 * @var self
	 */
	protected static $instance;

	/**
	 * @var Tribe__Tickets_Plus__Commerce__Loader
	 */
	protected $commerce_loader;

	/**
	 * The class singleton constructor.
	 *
	 * @param Tribe__Tickets_Plus__Commerce__Loader $commerce_loader
	 *
	 * @return Tribe__Tickets_Plus__CSV_Importer__Rows
	 */
	public static function instance( Tribe__Tickets_Plus__Commerce__Loader $commerce_loader ) {
		if ( empty( self::$instance ) ) {
			self::$instance = new self( $commerce_loader );
		}

		return self::$instance;
	}

	/**
	 * Tribe__Tickets_Plus__CSV_Importer__Rows constructor.
	 *
	 * @param Tribe__Tickets_Plus__Commerce__Loader $commerce_loader
	 */
	public function __construct( Tribe__Tickets_Plus__Commerce__Loader $commerce_loader ) {
		$this->commerce_loader = $commerce_loader;
	}

	/**
	 * @param array $import_options
	 *
	 * @return array
	 */
	public function filter_import_options_rows( array $import_options ) {
		if ( $this->commerce_loader->is_woocommerce_active() ) {
			$import_options['tickets_woo'] = esc_html__( 'Tickets', 'event-tickets-plus' );
		}

		return $import_options;
	}
}