<?php
if ( ! defined( 'MC4WP_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}


if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class MC4WP_Log_Table extends WP_List_Table {

	/**
	 * @var int
	 */
	private $per_page = 20;

	/**
	 * @var array
	 */
	private $log_counts = array(
		'all'      => 0,
		'checkbox' => 0,
		'form'     => 0
	);

	/**
	 * @var MC4WP_MailChimp
	 */
	private $mailchimp;

	/**
	 * @var MC4WP_Logger
	 */
	private $log;

	/**
	 * Constructor
	 */
	public function __construct( MC4WP_MailChimp $mailchimp ) {
		//Set parent defaults
		parent::__construct(
			array(
				'singular' => __( 'Log', 'mailchimp-for-wp' ),
				'plural'   => __( 'Log Items', 'mailchimp-for-wp' ),
				'ajax'     => false
			)
		);

		$this->log = MC4WP::instance()->get_log();
		$this->mailchimp = $mailchimp;
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
			'cb'          => '<input type="checkbox" />',
			'email'       => __( 'Email', 'mailchimp-for-wp' ),
			'list'        => __( 'List', 'mailchimp-for-wp' ),
			'data'  => __( 'Data', 'mailchimp-for-wp' ),
			'success' => __( 'Success', 'mailchimp-for-wp' ),
			'type' => __( 'Type', 'mailchimp-for-wp' ),
			'source'      => __( 'Source', 'mailchimp-for-wp' ),
			'datetime'    => __( 'Subscribed', 'mailchimp-for-wp' )
		);

		return $columns;
	}

	/**
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'email'       => array( 'email', false ),
			'datetime'    => array( 'datetime', false ),
			'type'        => array( 'type', false ),
			'list'        => array( 'list_ids', false )
		);

		return $sortable_columns;
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

		$this->log_counts = array(
			'all'      => $this->get_total_log_count(),
			'checkbox' => $this->get_log_count( 'checkbox' ),
			'form'     => $this->get_log_count( 'form' )
		);

		if ( isset( $_GET['view'] ) && in_array( $_GET['view'], array_keys( $this->get_views() ) ) ) {
			$total_items = $this->log_counts[ $_GET['view'] ];
		} else {
			$total_items = $this->log_counts['all'];
		}

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
		switch ( $column_name ) {
			case 'success':

				$color = ( $item->success ) ? 'green' : 'red';
				$icon = ( $item->success ) ? '&#10003;' : '&#10006;';

				return sprintf( '<span style="color: %s;">%s</span>', $color, $icon );
				break;
			case 'datetime':
				$date = MC4WP_Tools::mysql_datetime_to_local_datetime( $item->$column_name );
				return esc_html( $date );
				break;
			default:
				return '';
				break;
		}
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
			return '';
		}

		// build string
		$content = '';
		foreach ( $data as $name => $value ) {

			// skip non-scalar values for now
			// todo: show selected groups
			if( $name === 'GROUPINGS' && is_array( $value ) ) {
				$content .= $this->list_groupings( $item->list_ids, $value );
			} elseif ( ! is_scalar( $value ) ) {
				continue;
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

		foreach( $groupings as $grouping ) {
			$grouping_name = $this->mailchimp->get_list_grouping_name( $list_id, $grouping['id'] );
			$grouping_name = ( $grouping_name ) ? $grouping_name : $grouping['id'];
			$groups = implode( ', ', $grouping['groups'] );
			$content .= sprintf( '<strong>%s</strong>: %s<br />', esc_html( $grouping_name ), esc_html( $groups ) );
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
		$field_name = $this->mailchimp->get_list_field_name_by_tag( $list_id, $field_tag );
		$field_name = ( $field_name ) ? $field_name : $field_tag;
		$content = sprintf( '<strong>%s</strong>: %s<br />', esc_html( $field_name ), esc_html( $value ) );
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
		$list_ids   = explode( ',', $item->list_ids );

		foreach ( $list_ids as $list_id ) {
			$list_names[] = $this->mailchimp->get_list_name( $list_id );
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
		return sprintf(
			'<input type="checkbox" name="log[]" value="%s" />', $item->ID
		);
	}

	/**
	 * Outputs the text for the "type" column
	 *
	 * @param $item
	 *
	 * @return string|void
	 */
	public function column_type( $item ) {

		$type = strtolower( trim( $item->type ) );

		switch ( $type ) {
			case 'comment':
				return $this->_get_comment_link( $item->related_object_ID );
				break;

			case 'registration':
				return __( 'Registration', 'mailchimp-for-wp') . ': ' . $this->_get_user_link( $item->related_object_ID );
				break;

			case 'form':
			case 'sign-up-form':
				return $this->_get_form_link( $item->related_object_ID );
				break;

			case 'top-bar':
				return __( 'Top Bar', 'mailchimp-for-wp' );
				break;

			case 'buddypress_registration':
				return __( 'BuddyPress registration', 'mailchimp-for-wp' ) . ': ' . $this->_get_user_link( $item->related_object_ID );
				break;

			case 'multisite_registration':
				return __( 'MultiSite registration', 'mailchimp-for-wp' ) . ': ' . $this->_get_user_link( $item->related_object_ID );
				break;

			case 'edd_checkout':
				return $this->_get_easy_digital_downloads_payment_link( $item->related_object_ID );
				break;

			case 'woocommerce_checkout':
				return $this->_get_woocommerce_order_link( $item->related_object_ID );
				break;

			case 'cf7':
			case 'contact_form_7':
				return __( 'Contact Form 7', 'mailchimp-for-wp' );
				break;

			case 'bbpress_new_topic':
				return __( 'bbPress: New Topic', 'mailchimp-for-wp' );
				break;

			case 'bbpress_new_reply':
				return __( 'bbPress: New Reply', 'mailchimp-for-wp' );
				break;

			case 'other_form':
			case 'other':
			case 'general':
				return __( 'Other Form', 'mailchimp-for-wp' );
				break;
		};

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

		if ( isset( $_GET['view'] ) ) {
			$args['method'] = sanitize_text_field( $_GET['view'] );
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
	 * @return array
	 */
	private function get_total_log_count() {

		$args           = array();
		$args['select'] = 'COUNT(*)';

		if ( isset( $_GET['s'] ) ) {
			$args['email'] = sanitize_text_field( $_GET['s'] );
		}

		return $this->log->find( $args );
	}

	/**
	 * @param $method
	 *
	 * @return array
	 */
	private function get_log_count( $method ) {
		$args                  = array();
		$args['select']        = 'COUNT(*)';
		$args['method'] = $method;

		return $this->log->find( $args );
	}

	/**
	 * The text to show when there are no log items to show.
	 */
	public function no_items() {
		_e( 'No subscribe requests found.', 'mailchimp-for-wp' );
	}


	/**
	 * Setup available views
	 *
	 * @access      private
	 * @since       1.0
	 * @return      array
	 */
	public function get_views() {

		$base    = admin_url( 'admin.php?page=mailchimp-for-wp-reports&tab=log' );
		$current = isset( $_GET['view'] ) ? $_GET['view'] : '';

		$link_html = '<a href="%s"%s>%s</a>(%s)';

		$views = array(
			'all'     => sprintf( $link_html,
				esc_url( remove_query_arg( 'view', $base ) ),
				$current === 'all' || $current === '' ? ' class="current"' : '',
				esc_html__( 'All', 'mailchimp-for-wp' ),
				$this->log_counts['all']
			),
			'form'    => sprintf( $link_html,
				esc_url( add_query_arg( 'view', 'form', $base ) ),
				$current === 'form' ? ' class="current"' : '',
				esc_html__( 'Form', 'mailchimp-for-wp' ),
				$this->log_counts['form']
			),
			'comment' => sprintf( $link_html,
				esc_url( add_query_arg( 'view', 'checkbox', $base ) ),
				$current === 'checkbox' ? ' class="current"' : '',
				esc_html__( 'Checkbox', 'mailchimp-for-wp' ),
				$this->log_counts['checkbox']
			)
		);

		return $views;
	}

	/**
	 * @param     $text
	 * @param int $limit
	 *
	 * @return string
	 */
	private function shorten_text( $text, $limit = 30 ) {

		if( strlen( $text ) <= $limit ) {
			return $text;
		}

		return substr( $text, 0, $limit - 2 ) . '..';
	}

	/**
	 * @param $form_id
	 *
	 * @return string
	 */
	private function _get_form_link( $form_id ) {
		$form_title = get_the_title( $form_id );
		$form_name = __( 'Form', 'mailchimp-for-wp' ) . sprintf( " #%d: %s", $form_id, $this->shorten_text( $form_title, 20 ) );
		return '<a href="' . admin_url( 'post.php?action=edit&post=' . $form_id ) . '">' . $form_name . '</a>';
	}

	/**
	 * @param $user_id
	 *
	 * @return string|void
	 */
	private function _get_user_link( $user_id ) {
		if( '' === $user_id ) {
			return '';
		}

		$user = get_userdata( $user_id );

		if( ! $user instanceof WP_User ) {
			return '';
		}

		return sprintf( '<a href="%s">%s</a>', get_edit_user_link( $user_id ), $user->user_login );
	}

	/**
	 * @param $payment_id
	 *
	 * @return string
	 */
	private function _get_easy_digital_downloads_payment_link( $payment_id ) {

		if( $payment_id > 0 ) {
			return sprintf( '<a href="%s">%s #%d</a>', admin_url( 'edit.php?post_type=download&page=edd-payment-history&view=view-order-details&id=' . $payment_id ), __( 'Order', 'easy-digital-downloads', 'mailchimp-for-wp' ), $payment_id );
		}

		return 'Easy Digital Downloads ' . __( 'Checkout', 'mailchimp-for-wp' );
	}

	/**
	 * @param $order_id
	 *
	 * @return string
	 */
	private function _get_woocommerce_order_link( $order_id ) {

		if( $order_id > 0 ) {
			return sprintf( '<a href="%s">%s #%d</a>', get_edit_post_link( $order_id ), __( 'Order', 'woocommerce', 'mailchimp-for-wp' ), $order_id );
		}

		return 'WooCommerce ' . __( 'Checkout', 'mailchimp-for-wp' );
	}

	/**
	 * @param $comment_id
	 *
	 * @return string
	 */
	private function _get_comment_link( $comment_id ) {
		$comment = get_comment( $comment_id );

		if ( ! is_object( $comment ) || $comment->comment_approved === 'trash' ) {
			return __( 'Comment', 'mailchimp-for-wp' ) . ' <em>(' . __( 'deleted', 'mailchimp-for-wp' ) . ')</em>';
		}

		// build link to comment
		$link = get_permalink( $comment->comment_post_ID );
		$link = $link . '#comment-' . $comment->comment_ID;

		return '<a href="' . esc_url( $link ) . '">' . __( 'Comment', 'mailchimp-for-wp' ) . '</a>';
	}
}
