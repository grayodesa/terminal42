<?php


class MC4WP_Log_Exporter {

	/**
	 * @var MC4WP_Logger
	 */
	protected $logger;

	/**
	 * @var string The entire CSV string
	 */
	protected $csv_string = '';

	/**
	 * @var bool
	 */
	protected $built = false;

	/**
	 * @var string
	 */
	protected $filename = "mailchimp-for-wp-log.csv";

	/**
	 * @var array
	 */
	protected $log_filter = array(
		'limit' => 5000,
		'include_errors' => false
	);

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->logger = new MC4WP_Logger();
	}

	/**
	 * @param array $arguments
	 */
	public function filter( $arguments = array() ) {
		$this->log_filter = array_merge( $this->log_filter, $arguments );
	}

	/**
	 * @return array
	 */
	public function get_logs() {
		return $this->logger->find( $this->log_filter );
	}

	/**
	 * Build the CSV string
	 */
	public function build() {

		$log_items = $this->get_logs();

		// Open a memory "file" for read/write...
		$fp = fopen('php://temp', 'r+');

		// create csv header
		fputcsv( $fp, array( "email", "mailchimp_lists", "data", "type", "method", "successful", "source", "datetime" ) );

		// loop through log items
		foreach( $log_items as $item ) {

			fputcsv( $fp, array(
					$item->email,
					$item->list_ids,
					maybe_serialize( $item->data ),
					$item->method,
					$item->type,
					( $item->success ) ? "Yes" : "No",
					$item->url,
					$item->datetime
				)
			);

		}

		// ... rewind the "file" so we can read what we just wrote...
		rewind($fp);

		// ... read the entire line into a variable...
		$data = fread($fp, 1048576);

		// ... close the "file"...
		fclose($fp);

		$this->csv_string = rtrim($data, "\n");
		$this->built = true;
		return $this->csv_string;
	}

	/**
	 * Output the CSV string
	 */
	public function output() {

		if( ! $this->built ) {
			_doing_it_wrong( __METHOD__, 'You need to call the ' . __CLASS__ . '::build() method first.', MC4WP_VERSION );
		}

		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"{$this->filename}\";" );
		header("Content-Transfer-Encoding: binary");
		die( $this->csv_string );
	}
}