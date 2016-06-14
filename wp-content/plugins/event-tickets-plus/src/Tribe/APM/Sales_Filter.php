<?php


class Tribe__Tickets_Plus__APM__Sales_Filter extends Tribe__Tickets_Plus__APM__Abstract_Filter {

	/**
	 * @var string
	 */
	protected $type = 'custom_ticket_sales';

	/**
	 * @var string
	 */
	public static $key  = 'tickets_plus_sales_filter_key';

	/**
	 * @var Tribe__Tickets_Plus__Commerce__Sales_Counter
	 */
	private $sales_counter;

	/**
	 * Sets up the `$query_search_options` field.
	 */
	protected function set_up_query_search_options() {
		$this->query_search_options = array(
			'is'  => __( 'Are', 'event-tickets-plus' ),
			'not' => __( 'Are Not', 'event-tickets-plus' ),
			'gte' => __( 'Are at least', 'event-tickets-plus' ),
			'lte' => __( 'Are at most', 'event-tickets-plus' ),
		);
	}

	/**
	 * Returns the filter identifying key.
	 *
	 * Workaround for missing late static binding.
	 *
	 * @return mixed
	 */
	protected function key() {
		return self::$key;
	}
}