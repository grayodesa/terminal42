<?php

if( ! defined( 'MC4WP_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

class MC4WP_Logger {

	/**
	 * Version number of the database schema
	 */
	const DB_VERSION = '1.1';

	/**
	 * Option key of version number in database
	 */
	const OPTION_DB_VERSION = 'mc4wp_log_db_version';

	/**
	 * @var string
	 */
	private $table_name = '';

	/**
	 * @var wpdb
	 */
	private $db;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->db = $GLOBALS['wpdb'];
		$this->table_name = $this->db->prefix . 'mc4wp_log';
	}

	public function add_hooks() {
		add_action( 'admin_init', array( $this, 'upgrade' ) );

		// add listeners for core logging
		add_action( 'mc4wp_subscribe', array( $this, 'log_request' ), 10, 7 );
	}

	/**
	 * @see MC4WP_Logger::add
	 *
	 * @param        $email
	 * @param        $list_id
	 * @param        $data
	 * @param        $success
	 * @param        $method
	 * @param string $type
	 * @param int    $related_object_id
	 *
	 * @return false|int
	 */
	public function log_request( $email, $list_id, $data, $success, $method, $type = '', $related_object_id = 0 ) {
		$url = isset( $_SERVER['HTTP_REFERER'] ) ? esc_url( $_SERVER['HTTP_REFERER'] ) : '';
		return $this->add( $email, $list_id, $data, $success, $method, $type, $related_object_id, $url );
	}

	/**
	 * @param string $email Email address to log
	 * @param string|array  $list_ids String or array of list ID's
	 * @param array  $data The data that was sent to MailChimp
	 * @param int    $success Whether the sign-up succeeded
	 * @param string $method The method that was used (form or checkbox)
	 * @param string $type The type that was used (form, registration, cf7, etc..)
	 * @param int    $related_object_id ID of the related object, if any (form, comment, user, etc..)
	 * @param string $url The URl the request originated from
	 *
	 * @return false|int
	 */
	public function add( $email, $list_ids, $data = array(), $success = 1, $method = 'form', $type = 'form', $related_object_id = 0, $url = '' ) {

		if( is_array( $list_ids ) ) {
			$list_ids = implode( ',', $list_ids );
		}

		return $this->db->insert( $this->table_name, array(
				'email'     => $email,
				'list_ids'  => $list_ids,
				'success'   => ( $success ) ? 1 : 0,
				'data'      => json_encode( $data ),
				'method'    => $method,
				'type'      => $type,
				'related_object_ID' => absint( $related_object_id ),
				'url'       => $url,

				// store GMT date
				'datetime'  => current_time( 'mysql', true )
			)
		);
	}

	/**
	 * @param array $args
	 * @return int
	 */
	public function count( $args = array() ) {
		$args['select'] = 'COUNT(*)';
		return $this->find( $args );
	}

	/**
	 * @param array $args
	 * @param string $output_type
	 * @return int|mixed
	 */
	public function find( $args, $output_type = OBJECT ) {

		$args = wp_parse_args( $args, array(
			'select' => '*',
			'offset' => 0,
			'limit' => 1,
			'orderby' => 'id',
			'order' => 'DESC',

			// where params
			'email' => '',
			'method' => '',
			'datetime_after' => '',
			'datetime_before' => '',
			'include_errors' => true
		) );

		$where = array();
		$params = array();

		// build general select from query
		$query = sprintf( "SELECT %s FROM `%s`", $args['select'], $this->table_name );

		// add email to WHERE clause
		if ( '' !== $args['email'] ) {
			$where[] = 'email LIKE %s';
			$params[] = '%%' . $this->db->esc_like( $args['email'] ). '%%';
		}

		// add method to WHERE clause
		if ( '' !== $args['method'] ) {
			$where[] = 'method = %s';
			$params[] = $args['method'];
		}

		// add datetime to WHERE clause
		if( '' !== $args['datetime_after'] ) {
			$where[] = 'datetime >= %s';
			$params[] = $args['datetime_after'];
		}

		if( '' !== $args['datetime_before'] ) {
			$where[] = 'datetime <= %s';
			$params[] = $args['datetime_before'];
		}

		if( ! $args['include_errors'] ) {
			$where[] = 'success = %d';
			$params[] = 1;
		}

		// add where parameters
		if ( count( $where ) > 0 ) {
			$query .= ' WHERE '. implode( ' AND ', $where );
		}

		// prepare parameters
		if( ! empty( $params ) ) {
			$query = $this->db->prepare( $query, $params );
		}

		// return result count
		if ( $args['select'] === 'COUNT(*)' ) {
			return (int) $this->db->get_var( $query );
		}

		// return single row
		if( $args['limit'] === 1 ) {
			$query .= ' LIMIT 1';
			return $this->db->get_row( $query );
		}

		// perform rest of query
		$args['limit']  = absint( $args['limit'] );
		$args['offset'] = absint( $args['offset'] );
		$args['orderby'] = preg_replace( "/[^a-zA-Z]/", "", $args['orderby'] );
		$args['order'] = preg_replace( "/[^a-zA-Z]/", "", $args['order'] );

		// add ORDER BY, OFFSET and LIMIT to SQL
		$query .= sprintf( ' ORDER BY `%s` %s LIMIT %d, %d', $args['orderby'], $args['order'], $args['offset'], $args['limit'] );

		return $this->db->get_results( $query, $output_type );
	}

	/**
	 * @param $ids string|array
	 *
	 * @return mixed
	 */
	public function find_by_id( $ids ) {

		if( is_array( $ids ) ) {
			// create comma-separated string
			$ids = implode( ',', $ids );
		}

		// escape string for usage in IN clause
		$ids = esc_sql( $ids );

		$sql = sprintf( "SELECT * FROM `%s` WHERE ID IN (%s)", $this->table_name, $ids );

		// return single row if only one id is given
		if( substr_count( $ids, ',' ) === 0 ) {
			return $this->db->get_row( $sql );
		}

		return $this->db->get_results( $sql );
	}

	/**
	 * @param $ids Array or string of log ID's to delete
	 *
	 * @return false|int
	 */
	public function delete_by_id( $ids) {

		if( is_array( $ids ) ) {
			// create comma-separated string
			$ids = implode( ',', $ids );
		}

		// escape string for usage in IN clause
		$ids = esc_sql( $ids );

		$sql = sprintf( "DELETE FROM `%s` WHERE ID IN (%s)", $this->table_name, $ids );
		return $this->db->query( $sql );
	}

	/**
	 * Upgrade routine
	 * - Creates log table on plugin activation
	 * - Migrates table structure on updates
	 */
	public function upgrade() {

		$log_db_version = get_option( self::OPTION_DB_VERSION, 0 );

		// only run upgrade routine when database version is lower than code version
		if( version_compare( self::DB_VERSION, $log_db_version, '<=' ) ) {
			return;
		}

		global $charset_collate;

		// don't show errors as this would mess with plugin activation
		$this->db->hide_errors();

		// Create table if it does not exist
		$sql = "
		CREATE TABLE IF NOT EXISTS {$this->table_name} (
        ID BIGINT(20) NOT NULL AUTO_INCREMENT,
        email VARCHAR(255) NOT NULL,
        list_ids VARCHAR(255) NOT NULL,
        method VARCHAR(255) NOT NULL,
        type VARCHAR(255) NOT NULL,
        success TINYINT(1) DEFAULT 1,
		data TEXT NULL,
        related_object_ID BIGINT(20) NULL,
        url VARCHAR(255) DEFAULT '',
        datetime timestamp DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (ID)
		) $charset_collate";

		$this->db->query( $sql );

		// update to v1.0.4
		if( version_compare( $log_db_version, '1.0.4', '<=' ) ) {
			// change 'sign-up_form' in 'sign-up-form';
			$this->db->query( "UPDATE `{$this->table_name}` SET `signup_type` = 'sign-up-form' WHERE `signup_type` = 'sign-up_form'" );
		}

		// update to v1.1
		if( version_compare( $log_db_version, '1.1', '<=' ) ) {

			// merge columns `form_ID` and `comment_ID` into `related_object_ID`
			$this->db->query( "ALTER TABLE `{$this->table_name}` CHANGE COLUMN `form_ID` `related_object_ID` BIGINT(20)" );
			$this->db->query( "UPDATE `{$this->table_name}` SET `related_object_ID` = `comment_ID` WHERE `related_object_ID` = 0 AND `comment_ID` > 0 " );
			$this->db->query( "ALTER TABLE `{$this->table_name}` DROP COLUMN `comment_ID`" );

			// add 'success' column
			$this->db->query( "ALTER TABLE `{$this->table_name}` ADD COLUMN `success` TINYINT(1) DEFAULT 1" );

			// rename columns
			$this->db->query( "ALTER TABLE `{$this->table_name}` CHANGE COLUMN `signup_method` `method` VARCHAR(255)" );
			$this->db->query( "ALTER TABLE `{$this->table_name}` CHANGE COLUMN `signup_type` `type` VARCHAR(255)" );
			$this->db->query( "ALTER TABLE `{$this->table_name}` CHANGE COLUMN `merge_vars` `data` TEXT" );

			// alter datatype of `datetime`
			$this->db->query( "ALTER TABLE `{$this->table_name}` CHANGE COLUMN `datetime` `datetime` timestamp DEFAULT CURRENT_TIMESTAMP" );

			// change `sign-up-form` to just `form`
			$this->db->query( "UPDATE `{$this->table_name}` SET `type` = 'form' WHERE `type` = 'sign-up-form'" );
		}

		$this->db->show_errors();

		update_option( self::OPTION_DB_VERSION, self::DB_VERSION );
	}

}
