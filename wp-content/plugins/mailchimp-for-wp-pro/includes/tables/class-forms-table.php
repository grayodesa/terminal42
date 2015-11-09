<?php
if ( ! defined( 'MC4WP_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}


if ( ! class_exists( 'WP_List_Table', false ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Class MC4WP_Forms_Table
 */
class MC4WP_Forms_Table extends WP_List_Table {

	/**
	 * @var MC4WP_MailChimp
	 */
	protected $mailchimp;

	/**
	 * Constructor
	 */
	public function __construct( MC4WP_MailChimp $mailchimp ) {
		parent::__construct(
			array(
				'singular' => 'form',
				'plural'   => 'forms',
				'ajax'     => false
			)
		);

		$columns  = $this->get_columns();
		$sortable = $this->get_sortable_columns();
		$hidden   = array();
		$this->mailchimp = $mailchimp;

		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->items           = $this->get_items();
	}

	/**
	 * @return array
	 */
	public function get_columns() {
		return array(
			'ID'            => __( 'ID', 'mailchimp-for-wp' ),
			'post_title'    => __( 'Form', 'mailchimp-for-wp' ),
			'shortcode'     => __( 'Shortcode', 'mailchimp-for-wp' ),
			'lists'         => __( 'List(s)', 'mailchimp-for-wp' ),
			'post_modified' => __( 'Last edited', 'mailchimp-for-wp' )
		);
	}

	/**
	 * @return array
	 */
	public function get_sortable_columns() {
		return array(
			'ID'            => array( 'id', true ),
			'post_title'    => array( 'title', false ),
			'post_modified' => array( 'post_modified', false )
		);
	}

	/**
	 * @return array
	 */
	public function get_items() {
		$orderby = isset( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : '';
		$order   = isset( $_GET['order'] ) ? sanitize_text_field( $_GET['order'] ) : '';

		$forms = get_posts(
			array(
				'post_type'      => 'mc4wp-form',
				'posts_per_page' => - 1,
				'orderby'        => $orderby,
				'order'          => $order
			)
		);

		return $forms;
	}

	/**
	 * @param $item
	 * @param $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
		return $item->$column_name;
	}

	/**
	 * @param $item
	 *
	 * @return string
	 */
	public function column_post_title( $item ) {
		$actions    = array(
			'edit'   => '<a class="" title="' . __( 'Edit Form', 'mailchimp-for-wp' ) . '" href="' . get_edit_post_link( $item->ID ) . '">' . __( 'Edit Form', 'mailchimp-for-wp' ) . '</a>',
			'delete' => '<a class="submitdelete" title="Delete Form" href="' . get_delete_post_link( $item->ID, '', true ) . '">' . __( 'Delete', 'mailchimp-for-wp' ) . '</a>'
		);
		$form_title = ( empty( $item->post_title ) ) ? '(no title)' : $item->post_title;
		$title      = '<strong><a class="row-title" title="' . __( 'Edit Form', 'mailchimp-for-wp' ) . '" href="' . get_edit_post_link( $item->ID ) . '">' . $form_title . '</a></strong>';

		return sprintf( '%1$s %2$s', $title, $this->row_actions( $actions ) );
	}

	/**
	 * @param $item
	 *
	 * @return string
	 */
	public function column_shortcode( $item ) {
		return '<input type="text" onfocus="this.select();" readonly="readonly" value="[mc4wp_form id=&quot;' . $item->ID . '&quot;]" class="mc4wp-shortcode-example">';
	}

	/**
	 * @param $item
	 *
	 * @return string
	 */
	public function column_lists( $item ) {

		$form = MC4WP_Form::get( $item->ID );
		$content       = '';

		if ( ! empty( $form->settings['lists'] ) ) {
			foreach ( $form->settings['lists'] as $list_id ) {
				$content .= $this->mailchimp->get_list_name( $list_id ) . '<br />';
			}
		} else {
			return '<a style="color: red; text-decoration: underline;" href="' . get_edit_post_link( $item->ID ) . '">' . __( 'No MailChimp list(s) selected yet.', 'mailchimp-for-wp' ) . '</a>';
		}

		return $content;
	}

	/**
	 * The text that is shown when there are no items to show
	 */
	public function no_items() {
		_e( 'You have not created any sign-up forms yet. Time to do so!', 'mailchimp-for-wp' );
	}

}