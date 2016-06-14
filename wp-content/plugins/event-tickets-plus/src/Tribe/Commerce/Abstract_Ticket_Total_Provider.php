<?php


abstract class Tribe__Tickets_Plus__Commerce__Abstract_Ticket_Total_Provider {

	/**
	 * @var Tribe__Tickets_Plus__Commerce__Loader
	 */
	protected $commerce_loader;

	/**
	 * Tribe__Tickets_Plus__Commerce__Sales_Counter constructor.
	 *
	 * @param Tribe__Tickets_Plus__Commerce__Loader $commerce_loader
	 */
	public function __construct( Tribe__Tickets_Plus__Commerce__Loader $commerce_loader ) {
		$this->commerce_loader = $commerce_loader;
	}
}