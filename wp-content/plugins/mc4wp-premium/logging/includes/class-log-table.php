<?php
defined( 'ABSPATH' ) or exit;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Class MC4WP_Log_Table
 *
 * @ignore
 *
 * TODO: Add "export" option to bulk actions
 */
class MC4WP_Log_Table extends WP_List_Table {

	/**
	 * @var int
	 */
	private $per_page = 20;

	/**
	 * @var MC4WP_MailChimp
	 */
	private $mailchimp;

	/**
	 * @var MC4WP_Logger
	 */
	private $log;

	/**
	 * @var MC4WP_Integration[]
	 */
	private $integrations;

	/**
	 * @var array
	 */
	private $views;

	/**
	 * Constructor
	 */
	public function __construct( MC4WP_MailChimp $mailchimp ) {

		$this->log          = new MC4WP_Logger();
		$this->mailchimp    = $mailchimp;
		$this->integrations = mc4wp_get_integrations();
		$this->per_page = $this->get_items_per_page( 'mc4wp_log_per_page' );

		//Set parent defaults
		parent::__construct(
			array(
				'singular' => __( 'Log', 'mailchimp-for-wp' ),
				'plural'   => __( 'Log Items', 'mailchimp-for-wp' ),
				'ajax'     => false
			)
		);

		$this->process_bulk_action();
		$this->prepare_items();

	}

	/**
	 * @return array
	 */
	function get_bulk_actions() {
		$actions = array(
			'delete' => 'Delete'
		);

		return $actions;
	}

	/**
	 * @return array
	 */
	public function get_columns() {

		$columns = array(
			'cb'       => '<input type="checkbox" />',
			'email'    => __( 'Email', 'mailchimp-for-wp' ),
			'list'     => __( 'List', 'mailchimp-for-wp' ),
			'data'     => __( 'Data', 'mailchimp-for-wp' ),
			'type'     => __( 'Type', 'mailchimp-for-wp' ),
			'source'   => __( 'Source', 'mailchimp-for-wp' ),
			'datetime' => __( 'Subscribed', 'mailchimp-for-wp' )
		);

		return $columns;
	}

	/**
	 * @return array
	 */
	public function get_sortable_columns() {
		return array(
			'email'    => array( 'email', false ),
			'datetime' => array( 'datetime', false ),
			'type'     => array( 'type', false ),
			'list'     => array( 'list_ids', false )
		);
	}

	/**
	 * Prepare table items
	 */
	public function prepare_items() {
		$columns  = $this->get_columns();
		$sortable = $this->get_sortable_columns();
		$hidden   = array();

		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->items           = $this->get_log_items();
		$this->views           = $this->prepare_views();

		$view = ( isset( $_GET['view'] ) ) ? $_GET['view'] : 'all';
		$total_items = $this->views[ $view ]['count'];

		$this->set_pagination_args( array(
				'total_items' => $total_items,
				'per_page'    => $this->per_page
			)
		);
	}

	/**
	 * @return false|int|void
	 */
	public function process_bulk_action() {

		if ( ! isset( $_GET['log'] ) ) {
			return false;
		}

		check_admin_referer( 'bulk-' . $this->_args['plural'] );

		$ids = $_GET['log'];

		if ( ! is_array( $ids ) ) {
			$ids = array( absint( $ids ) );
		}

		if ( $this->current_action() === 'delete' ) {
			add_settings_error( 'mc4wp', 'mc4wp-logs-deleted', __( 'Log items deleted.', 'mailchimp-for-wp' ), 'updated' );

			return $this->log->delete_by_id( $ids );
		}
	}

	/**
	 * @param $item
	 * @param $column_name
	 *
	 * @return string
	 */
	public function column_default( $item, $column_name ) {
		if ( property_exists( $item, $column_name ) ) {
			return $item->$column_name;
		}

		return '';
	}

	public function column_datetime( $item ) {
		$date = MC4WP_Tools::mysql_datetime_to_local_datetime( $item->datetime );
		return esc_html( $date );
	}

	/**
	 * @param $item
	 *
	 * @return string
	 */
	public function column_data( $item ) {

		// don't print any merge vars if none were submitted
		$data = json_decode( $item->data, true );
		if ( ! is_array( $data ) || empty( $data ) ) {
			return '&mdash;';
		}

		// build string
		$content = '';
		foreach ( $data as $name => $value ) {

			if( $name === 'GROUPINGS' ) {
				$content .= $this->list_groupings( $item->list_ids, $value );
			} else {
				$content .= $this->list_field( $item->list_ids, $name, $value );
			}

		}

		return $content;
	}

	/**
	 * @param $list_id
	 * @param $groupings
	 *
	 * @return string
	 */
	protected function list_groupings( $list_id, $groupings ) {
		$content = '';

		$list = $this->mailchimp->get_list( $list_id );

		foreach ( $groupings as $grouping_id => $group_ids ) {

			// For BC with pre 3.0 data, check for old style of array subkeys.
			if( isset( $group_ids['id'] ) ) {
				$grouping_id = $group_ids['id'];
			}

			if( isset( $group_ids['groups'] ) ) {
				$group_ids = $group_ids['groups'];
			}

			$grouping = $list->get_grouping( $grouping_id );

			// for some weird reason, sometimes this is a single string instead of an array.
			$group_ids = (array) $group_ids;

			// for BC with MailChimp for WordPress < 3.1.6
			if( ! $grouping instanceof MC4WP_MailChimp_Grouping || ! method_exists( $grouping, 'get_group_name_by_id' ) ) {
				$group_ids_string = implode( ', ', $group_ids );
				$content .= sprintf( '<strong>%s</strong>: %s<br />', esc_html( $grouping_id), esc_html( $group_ids_string ) );
				continue;
			}

			// get pretty group names
			$group_names = array_map( array( $grouping, 'get_group_name_by_id' ), $group_ids );
			$group_names_string = implode( ', ', $group_names );
			$content .= sprintf( '<strong>%s</strong>: %s<br />', esc_html( $grouping->name ), esc_html( $group_names_string ) );
		}

		return $content;
	}

	/**
	 * @param $list_id
	 * @param $field_tag
	 * @param $value
	 *
	 * @return string
	 */
	protected function list_field( $list_id, $field_tag, $value ) {
		$list       = $this->mailchimp->get_list( $list_id );
		$field_name = $list->get_field_name_by_tag( $field_tag );
		$field_name = $field_name ? $field_name : $field_tag;

		// make sure value is scalar
		$value = is_array( $value ) ? join( ', ', $value ) : $value;
		$content    = sprintf( '<strong>%s</strong>: %s<br />', esc_html( $field_name ), esc_html( $value ) );

		return $content;
	}


	/**
	 * @param $item
	 *
	 * @return string
	 */
	public function column_source( $item ) {
		$parsed_url = parse_url( $item->url );

		if ( is_array( $parsed_url ) ) {
			$url = $parsed_url['path'];

			if ( ! empty( $parsed_url['query'] ) ) {
				$url .= '?' . $parsed_url['query'];
			}
		} else {
			$url = $item->url;
		}

		return '<a href="' . esc_url( $item->url ) . '">' . esc_html( $this->shorten_text( $url ) ) . '</a>';
	}

	/**
	 * @param $item
	 *
	 * @return string
	 */
	public function column_list( $item ) {
		$list_names = array();
		$list_ids   = array_map( 'trim', explode( ',', $item->list_ids ) );

		foreach ( $list_ids as $list_id ) {
			$list         = $this->mailchimp->get_list( $list_id );
			$list_names[] = sprintf( '<a href="%s" target="_blank">%s</a>', $list->get_web_url(), $list->name );
		}

		return join( ', ', $list_names );
	}

	/**
	 * @param $item
	 *
	 * @return string
	 */
	public function column_email( $item ) {
		$actions = array(
			'delete' => '<a href="' . wp_nonce_url( admin_url( sprintf( 'admin.php?page=%s&action=%s&log=%s&tab=log', $_REQUEST['page'], 'delete', $item->ID ) ), 'bulk-' . $this->_args['plural'] ) . '">' . __( 'Delete', 'mailchimp-for-wp' ) . '</a>',
		);

		return sprintf( '<span id="item-%d"></span>', $item->ID ) . esc_html( $item->email ) . $this->row_actions( $actions );
	}

	/**
	 * @param $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="log[]" value="%s" />', $item->ID );
	}

	/**
	 * Outputs the text for the "type" column
	 *
	 * @param $item
	 *
	 * @return string|void
	 */
	public function column_type( $item ) {

		if ( isset( $this->integrations[ $item->type ] ) ) {
			$object_link = $this->integrations[ $item->type ]->get_object_link( $item->related_object_ID );
			if ( ! empty( $object_link ) ) {
				return $object_link;
			}

			return $this->integrations[ $item->type ]->name;
		}

		if ( $item->type === 'mc4wp-form' ) {

			$form_id = $item->related_object_ID;

			try {
				$form = mc4wp_get_form( $form_id );

				return '<a href="' . mc4wp_get_edit_form_url( $form->ID ) . '">' . esc_html( $form->name ) . '</a>';
			} catch ( Exception $e ) {
				return __( 'Form', 'mailchimp-for-wp' ) . ' ' . $form_id . ' <em>(' . __( 'deleted', 'mailchimp-for-wp' ) . ')</em>';
			}
		} elseif( $item->type === 'mc4wp-top-bar' ) {
			return 'MailChimp Top Bar';
		}

		return $item->type;
	}

	/**
	 * @return array
	 */
	private function get_log_items() {
		$args           = array();
		$args['offset'] = ( $this->get_pagenum() - 1 ) * $this->per_page;
		$args['limit']  = $this->per_page;

		if ( isset( $_GET['s'] ) ) {
			$args['email'] = sanitize_text_field( $_GET['s'] );
		}

		if( isset( $_GET['view'] ) && $_GET['view'] !== 'all' ) {
			$args['type'] = sanitize_text_field( $_GET['view'] );
		}

		if ( isset( $_GET['orderby'] ) ) {
			$args['orderby'] = sanitize_text_field( $_GET['orderby'] );
		}

		if ( isset( $_GET['order'] ) ) {
			$args['order'] = sanitize_text_field( $_GET['order'] );
		}

		return $this->log->find( $args );
	}


	/**
	 * The text to show when there are no log items to show.
	 */
	public function no_items() {
		_e( 'No subscribe requests found.', 'mailchimp-for-wp' );
	}

	public function get_view_link( $key, $view ) {
		$current = empty( $_GET['view'] ) ? 'all' : $_GET['view'];
		$url = admin_url( 'admin.php?page=mailchimp-for-wp-reports&tab=log&view=' . $key );
		$class = $current === $key ? 'current' : '';
		return sprintf( '<a href="%s" class="%s">%s</a> (%d)', $url, $class, $view['name'], $view['count']);
	}

	/**
	 * Prepares the various views
	 */
	public function prepare_views() {
		$this->views = array(
			'all'           => array(
				'name'  => esc_html__( 'All', 'mailchimp-for-wp' ),
				'count' => $this->log->count()
			),
			'mc4wp-form'    => array(
				'name'  => esc_html__( 'Form', 'mailchimp-for-wp' ),
				'count' => $this->log->count( array( 'type' => 'mc4wp-form' ) )
			),
			'mc4wp-top-bar' => array(
				'name'  => 'MailChimp Top Bar',
				'count' => $this->log->count( array( 'type' => 'mc4wp-top-bar' ) )
			)
		);

		foreach ( $this->integrations as $integration ) {
			$this->views[ $integration->slug ] = array(
				'name'  => $integration->name,
				'count' => $this->log->count( array( 'type' => $integration->slug )
				)
			);
		}

		return $this->views;
	}

	/**
	 * Get available views
	 *
	 * @access      private
	 * @since       1.0
	 * @return      array
	 */
	public function get_views() {

		$links = array();
		foreach( $this->views as $key => $view ) {
			$links[ $key ] = $this->get_view_link( $key, $view );
		}

		return $links;
	}

	/**
	 * @param     $text
	 * @param int $limit
	 *
	 * @return string
	 */
	private function shorten_text( $text, $limit = 30 ) {

		if ( strlen( $text ) <= $limit ) {
			return $text;
		}

		return substr( $text, 0, $limit - 2 ) . '..';
	}

}
