<?php

if( ! defined( 'MC4WP_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

class MC4WP_Statistics {

	/**
	* @var string
	*/
	private $table_name;

	/**
	 * @var array
	 */
	private $initial_data = array();

	/**
	 * @var array
	 */
	private $config = array();

	/**
	 * @var string
	 */
	public $range = '';

	/**
	 * @var
	 */
	public $start_date;

	/**
	 * @var
	 */
	public $end_date;

	/**
	 * @var string
	 */
	public $step_size = 'day';

	/**
	 * @var
	 */
	private $day;

	/**
	* Constructor
	*/
	public function __construct( $config ) {

		// store config
		$this->config = $config;

		// set table prefix
		global $wpdb;
		$this->table_name = $wpdb->prefix . 'mc4wp_log';

		// get range and setup start dates etc..
		$this->range = ( isset( $config['range'] ) ) ? $config['range'] : 'last_week';
		$this->init();


	}

	/**
	* Initialize various settings to use
	*/
	private function init() {

		$this->day = date( 'd' );

		switch ( $this->range ) {
			case 'today':
				$this->start_date = strtotime( 'now midnight' );
				$this->end_date = strtotime( 'tomorrow midnight' );
				$this->step_size = 'hour';
			break;

			case 'yesterday':
				$this->start_date = strtotime( 'yesterday midnight' );
				$this->end_date = strtotime( 'today midnight' );
				$this->step_size = 'hour';
			break;

			case 'last_week':
				$this->start_date = strtotime( '-6 days midnight' );
				$this->end_date = strtotime( 'tomorrow midnight' );
				$this->step_size = 'day';
			break;

			case 'last_month':
				$this->start_date = strtotime( '-1 month midnight' );
				$this->end_date = strtotime( 'tomorrow midnight' );
				$this->step_size = 'day';
			break;

			case 'last_quarter':
				$this->start_date = strtotime( '-3 months midnight' );
				$this->end_date = strtotime( 'tomorrow midnight' );
				$this->step_size = 'month';
			break;

			case 'last_year':
				$this->start_date = strtotime( '-1 year midnight' );
				$this->end_date = strtotime( 'tomorrow midnight' );
				$this->step_size = 'month';
			break;

			case 'custom':
				$this->start_date = strtotime( implode( '-', array( $this->config['start_year'], $this->config['start_month'], $this->config['start_day'] ) ) );
				$this->end_date = strtotime( implode( '-', array( $this->config['end_year'], $this->config['end_month'], $this->config['end_day'] ) ) );
				$this->step_size = $this->calculate_step_size( $this->start_date, $this->end_date );
				$this->day = $this->config['start_day'];
				break;

			default:
				$this->start_date = strtotime( '-1 week midnight' );
				$this->end_date = strtotime( 'tomorrow midnight' );
				$this->step_size = 'day';
			break;
		}

		// If start is before end, revert back to "week" range and re-init.
		if( $this->start_date > $this->end_date ) {
			add_settings_error( 'mc4wp', 'mc4wp-stats', __( 'End date can\'t be before the start date', 'mailchimp-for-wp' ) );
			$this->range = 'last_week';
			$this->init();
		}

		// setup array of dates with 0's
		$current = $this->start_date;
		$this->initial_data = array();
		while ( $current < $this->end_date ) {
			$this->initial_data["{$current}"] = 0;
			$current = strtotime( "+1 {$this->step_size}", $current );
		}

	}

	/**
	 * Calculates an appropriate step size
	 *
	* @param int $start
	* @param int $end
	*
	* @return string
	*/
	public function calculate_step_size( $start, $end ) {
		$difference = $end - $start;
		$dayseconds = 86400;
		$monthseconds = 2592000;

		if ( $difference > $monthseconds ) {
			$step = 'month';
		} elseif ( $difference > ( $dayseconds * 2 ) ) {
			$step = 'day';
		} else {
			$step = 'hour';
		}

		return $step;
	}

	/**
	 * @return mixed
	 */
	protected function get_date_format() {
		$date_formats = array(
			'hour' => '%Y-%m-%d %H:00:00',
			'day' => '%Y-%m-%d 00:00:00',
			'week' => '%YW%v 00:00:00',
			'month' => "%Y-%m-{$this->day} 00:00:00"
		);

		return $date_formats[ $this->step_size ];
	}

	/**
	 * @return array
	 */
	public function get_statistics() {
		// Start with an empty stats array
		$stats = array(
			'totals' => array(
				'label' => 'Total subscriptions',
				'data' => array(),
				'id' => 'total',
				'total_count' => 0
			),
			'form' => array(
				'label' => 'Using a form',
				'data' => array(),
				'id' => 'form-subscriptions',
				'total_count' => 0
			),
			'checkbox' => array(
				'label' => 'Using a checkbox',
				'data' => array(),
				'id' => 'checkbox-subscriptions',
				'total_count' => 0
			)
		);

		$day_counts = $this->get_total_day_counts();
		$stats['totals']['total_count'] = array_sum( $day_counts );
		$stats['totals']['data'] = array_map( array( $this, 'format_graph_data' ), $day_counts, array_keys( $day_counts ) );

		$day_counts = $this->get_checkbox_day_counts();
		$stats['checkbox']['total_count'] = array_sum( $day_counts );
		$stats['checkbox']['data'] = array_map( array( $this, 'format_graph_data' ), $day_counts, array_keys( $day_counts ) );

		$day_counts = $this->get_form_day_counts();
		$stats['form']['total_count'] = array_sum( $day_counts );
		$stats['form']['data'] = array_map( array( $this, 'format_graph_data' ), $day_counts, array_keys( $day_counts ) );

		// get stats for each individual form
		$forms = get_posts(
			array(
				'post_type' => 'mc4wp-form',
				'numberposts' => -1
			)
		);
		if ( is_array( $forms ) ) {
			foreach ( $forms as $f ) {

				$title = strlen( $f->post_title ) > 20 ? substr( $f->post_title, 0, 20 ) . '..' : $f->post_title;
				$day_counts = $this->get_day_counts_for_form( $f->ID );

				$form_stats = array(
					'label' => "Form #{$f->ID}: {$title}",
					'data' => array_map( array( $this, 'format_graph_data' ), $day_counts, array_keys( $day_counts ) ),
					'id' => "form-{$f->ID}-subscriptions",
					'total_count' => array_sum( $day_counts )
				);

				$stats["form_{$f->ID}"] = $form_stats;
			}
		}

		return $stats;
	}

	/**
	 * @param $totals
	 *
	 * @return array
	 */
	public function get_day_counts( $totals ) {
		$counts = $this->initial_data;

		foreach ( $totals as $day ) {
			$timestamp = strtotime( $day->date_group );
			$counts["{$timestamp}"] = $day->count;
		}

		return $counts;
	}

	/**
	 * @return array
	 */
	public function get_total_day_counts() {
		global $wpdb;
		$sql = "SELECT COUNT(id) AS count, datetime, DATE_FORMAT(datetime, '%s') AS date_group FROM `{$this->table_name}` WHERE `success` = 1 AND UNIX_TIMESTAMP(datetime) >= %d AND UNIX_TIMESTAMP(datetime) <= %d GROUP BY date_group";
		$query = $wpdb->prepare( $sql, $this->get_date_format(), $this->start_date, $this->end_date );
		$totals = $wpdb->get_results( $query );
		return $this->get_day_counts( $totals );
	}

	/**
	 * @return array
	 */
	public function get_checkbox_day_counts() {
		global $wpdb;
		$sql = "SELECT COUNT(id) AS count, datetime, DATE_FORMAT(datetime, '%s') AS date_group FROM `{$this->table_name}` WHERE `success` = 1 AND method = 'checkbox' AND UNIX_TIMESTAMP(datetime) >= %d AND UNIX_TIMESTAMP(datetime) <= %d GROUP BY date_group";
		$query = $wpdb->prepare( $sql, $this->get_date_format(), $this->start_date, $this->end_date );
		$totals = $wpdb->get_results( $query );
		return $this->get_day_counts( $totals );
	}

	/**
	 * @return array
	 */
	public function get_form_day_counts() {
		global $wpdb;
		$sql = "SELECT COUNT(id) AS count, datetime, DATE_FORMAT(datetime, '%s') AS date_group FROM `{$this->table_name}` WHERE `success` = 1 AND method = 'form' AND UNIX_TIMESTAMP(datetime) >= %d AND UNIX_TIMESTAMP(datetime) <= %d GROUP BY date_group";
		$query = $wpdb->prepare( $sql, $this->get_date_format(), $this->start_date, $this->end_date );
		$totals = $wpdb->get_results( $query );
		return $this->get_day_counts( $totals );
	}

	/**
	 * @param int $form_id
	 * @return array
	 */
	public function get_day_counts_for_form( $form_id ) {
		global $wpdb;
		$sql = "SELECT COUNT(id) AS count, datetime, DATE_FORMAT(datetime, '%s') AS date_group FROM `{$this->table_name}` WHERE `related_object_ID` = %d AND `success` = 1 AND method = 'form' AND UNIX_TIMESTAMP(datetime) >= %d AND UNIX_TIMESTAMP(datetime) <= %d GROUP BY date_group";
		$query = $wpdb->prepare( $sql, $this->get_date_format(), $form_id, $this->start_date, $this->end_date );
		$totals = $wpdb->get_results( $query );
		return $this->get_day_counts( $totals );
	}

	/**
	 * @param $count
	 * @param $timestamp
	 *
	 * @return array
	 */
	public function format_graph_data( $count, $timestamp ) {
		return array( $timestamp * 1000, $count );
	}
}
