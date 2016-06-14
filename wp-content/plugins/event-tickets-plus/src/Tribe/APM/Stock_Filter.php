<?php


class Tribe__Tickets_Plus__APM__Stock_Filter extends Tribe__Tickets_Plus__APM__Abstract_Filter {

	/**
	 * @var string
	 */
	protected $type = 'custom_ticket_stock';

	/**
	 * @var string
	 */
	public static $key = 'tickets_plus_stock_filter_key';

	/**
	 * Sets up the query search options for the filter.
	 */
	protected function set_up_query_search_options() {
		$this->query_search_options = array(
			'is'  => __( 'Is', 'event-tickets-plus' ),
			'not' => __( 'Is Not', 'event-tickets-plus' ),
			'gte' => __( 'Is at least', 'event-tickets-plus' ),
			'lte' => __( 'Is at most', 'event-tickets-plus' ),
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