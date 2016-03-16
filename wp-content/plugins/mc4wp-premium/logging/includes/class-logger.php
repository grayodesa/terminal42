<?php

/**
 * Class MC4WP_Logger
 *
 * @ignore
 */
class MC4WP_Logger {

	/**
	 * @var string
	 */
	private $table_name = '';

	/**
	 * @var WPDB
	 */
	private $db;

	/**
	 * Constructor
	 *
	 * @param WPDB $wpdb
	 */
	public function __construct( $wpdb = null ) {

		if( null === $wpdb ) {
			global $wpdb;
		}

		$this->db = $wpdb;
		$this->table_name = $wpdb->prefix . 'mc4wp_log';
	}

	/**
	 *
	 */
	public function add_hooks() {
		// add listeners for core logging
		add_action( 'mctb_subscribed', array( $this, 'log_top_bar_request' ), 10, 3 );
		add_action( 'mc4wp_integration_subscribed', array( $this, 'log_integration_request' ), 10, 4 );
		add_action( 'mc4wp_form_subscribed', array( $this, 'log_form_request' ) );
	}

	/**
	 * @param string $list
	 * @param string $email
	 * @param string $fields
	 * @return int
	 */
	public function log_top_bar_request( $list, $email, $fields ) {
		return $this->add(
			$email,
			$list,
			$fields,
			'mc4wp-top-bar',
			null,
			MC4WP_Request::create_from_globals()->get_referer()
		);
	}

	/**
	 * @param MC4WP_Integration $integration
	 * @param string $email
	 * @param array $data
	 * @param int $related_object_id
	 *
	 * @return false|int
	 */
	public function log_integration_request( MC4WP_Integration $integration, $email, $data, $related_object_id = 0 ) {
		return $this->add(
			$email,
			$integration->get_lists(),
			$data,
			$integration->slug,
			$related_object_id,
			MC4WP_Request::create_from_globals()->get_referer()
		);
	}

	/**
	 * @param MC4WP_Form $form
	 *
	 * @return false|int
	 */
	public function log_form_request( MC4WP_Form $form ) {
		$data = $form->data;
		unset( $data['EMAIL'] );
		return $this->add(
			$form->data['EMAIL'],
			$form->get_lists(),
			$data,
			'mc4wp-form',
			$form->ID,
			MC4WP_Request::create_from_globals()->get_referer()
		);
	}


	/**
	 * @param string $email Email address to log
	 * @param string|array  $list_ids String or array of list ID's
	 * @param array  $data The data that was sent to MailChimp
	 * @param string $type The type that was used (form, integration slug, ...)
	 * @param int    $related_object_id ID of the related object, if any (form, comment, user, etc..)
	 * @param string $url The URl the request originated from
	 *
	 * @return false|int
	 */
	public function add( $email, $list_ids, $data = array(), $type = 'form', $related_object_id = 0, $url = '' ) {

		// make sure `$list_ids` is a string
		if( is_array( $list_ids ) ) {
			$list_ids = implode( ',', array_map( 'trim', $list_ids ) );
		}

		return $this->db->insert( $this->table_name, array(
				'email'     => $email,
				'list_ids'  => $list_ids,
				'data'      => json_encode( $data ),
				'type'      => $type,
				'related_object_ID' => (int) $related_object_id,
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
			'type' => '',
			'email' => '',
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

		// add type to WHERE clause
		if ( '' !== $args['type'] ) {
			$where[] = 'type = %s';
			$params[] = $args['type'];
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


}
