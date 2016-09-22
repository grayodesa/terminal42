<?php

class Tribe__Tickets_Plus__Meta {

	const ENABLE_META_KEY = '_tribe_tickets_meta_enabled';
	const META_KEY = '_tribe_tickets_meta';


	private $path;
	private $meta_fieldset;
	private $rsvp_meta;
	private $render;

	/**
	 * @var Tribe__Tickets_Plus__Meta__Storage
	 */
	protected $storage;

	/**
	 * @var Tribe__Tickets_Plus__Meta__Export
	 */
	protected $export;

	/**
	 * Get (and instantiate, if necessary) the instance of the class
	 *
	 * @static
	 * @return self
	 *
	 */
	public static function instance() {
		static $instance;

		if ( ! $instance instanceof self ) {
			$instance = new self;
		}

		return $instance;
	}

	/**
	 * Tribe__Tickets_Plus__Meta constructor.
	 *
	 * @param string                                   $path
	 * @param Tribe__Tickets_Plus__Meta__Storage|null $storage An instance of the meta storage handler.
	 */
	public function __construct( $path = null, Tribe__Tickets_Plus__Meta__Storage $storage = null ) {
		$this->storage = $storage ? $storage : new Tribe__Tickets_Plus__Meta__Storage();

		if ( ! is_null( $path ) ) {
			$this->path = trailingslashit( $path );
		}

		add_action( 'tribe_events_tickets_metabox_advanced', array( $this, 'metabox' ), 99, 2 );
		add_action( 'wp_ajax_tribe-tickets-info-render-field', array( $this, 'ajax_render_fields' ) );
		add_action( 'wp_ajax_tribe-tickets-load-saved-fields', array( $this, 'ajax_render_saved_fields' ) );
		add_action( 'event_tickets_after_save_ticket', array( $this, 'save_meta' ), 10, 3 );
		add_action( 'event_tickets_ticket_list_after_ticket_name', array( $this, 'maybe_render_custom_meta_icon' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );

		add_filter( 'event_tickets_ajax_ticket_add_data', array( $this, 'inject_fieldsets_in_json' ), 10, 2 );

		$this->meta_fieldset();
		$this->register_resources();
		$this->render();
		$this->rsvp_meta();
		$this->export();
	}

	public function meta_fieldset() {
		if ( ! $this->meta_fieldset ) {
			$this->meta_fieldset = new Tribe__Tickets_Plus__Meta__Fieldset;
		}

		return $this->meta_fieldset;
	}

	/**
	 * Object accessor method for the RSVP meta
	 *
	 * @return Tribe__Tickets_Plus__Meta__RSVP
	 */
	public function rsvp_meta() {
		if ( ! $this->rsvp_meta ) {
			$this->rsvp_meta = new Tribe__Tickets_Plus__Meta__RSVP;
		}

		return $this->rsvp_meta;
	}

	public function render() {
		if ( ! $this->render ) {
			$this->render = new Tribe__Tickets_Plus__Meta__Render;
		}

		return $this->render;
	}

	/**
	 * @return Tribe__Tickets_Plus__Meta__Export
	 */
	public function export() {
		if ( ! $this->export ) {
			$this->export = new Tribe__Tickets_Plus__Meta__Export;
		}

		return $this->export;
	}

	public function register_resources() {
		wp_register_script(
			'jquery-cookie',
			plugins_url( 'vendor/jquery.cookie/jquery.cookie.js', dirname( dirname( __FILE__ ) ) ),
			array( 'jquery' ),
			Tribe__Tickets__Main::instance()->js_version(),
			true
		);

		wp_register_script(
			'jquery-deparam',
			plugins_url( 'vendor/jquery.deparam/jquery.deparam.js', dirname( dirname( __FILE__ ) ) ),
			array(),
			Tribe__Tickets__Main::instance()->js_version()
		);

		wp_register_script(
			'event-tickets-meta',
			plugins_url( 'resources/js/meta.js', dirname( __FILE__ ) ),
			array( 'jquery-cookie', 'jquery-deparam' ),
			Tribe__Tickets__Main::instance()->js_version(),
			true
		);

		wp_register_style(
			'event-tickets-meta',
			plugins_url( 'resources/css/meta.css', dirname( __FILE__ ) ),
			array(),
			Tribe__Tickets__Main::instance()->css_version()
		);

		wp_register_script(
			'event-tickets-meta-admin',
			plugins_url( 'resources/js/meta-admin.js', dirname( __FILE__ ) ),
			array(
				'jquery-ui-draggable',
				'jquery-ui-droppable',
			),
			Tribe__Tickets__Main::instance()->js_version()
		);

		wp_register_script(
			'event-tickets-meta-report',
			plugins_url( 'resources/js/meta-report.js', dirname( __FILE__ ) ),
			array(),
			Tribe__Tickets__Main::instance()->js_version()
		);
	}

	public function wp_enqueue_scripts() {
		wp_enqueue_script( 'event-tickets-meta' );
	}

	/**
	 * Retrieves custom meta fields for a given ticket
	 *
	 * @param int $ticket_id Ticket ID
	 *
	 * @return array
	 */
	public function get_meta_fields_by_ticket( $ticket_id ) {
		$fields = array();

		if ( empty( $ticket_id ) ) {
			return $fields;
		}

		$field_meta = get_post_meta( $ticket_id, self::META_KEY, true );

		$fields = array();

		if ( $field_meta ) {
			foreach ( (array) $field_meta as $field ) {
				if ( empty( $field['type'] ) ) {
					continue;
				}

				$field_object = $this->generate_field( $ticket_id, $field['type'], $field );

				if ( ! $field_object ) {
					continue;
				}

				$fields[] = $field_object;
			}
		}

		/**
		 * Filters the fields for a ticket
		 *
		 * @var array $fields
		 * @var int $ticket_id
		 */
		$fields = apply_filters( 'event_tickets_plus_meta_fields_by_ticket', $fields, $ticket_id );

		return $fields;
	}

	/**
	 * Retrieves the meta fields for all tickets associated with the specified event.
	 *
	 * @param $event_id
	 *
	 * @return array
	 */
	public function get_meta_fields_by_event( $event_id ) {
		$fields = array();

		foreach ( Tribe__Tickets__Tickets::get_event_tickets( $event_id ) as $ticket ) {
			$meta_fields = $this->get_meta_fields_by_ticket( $ticket->ID );

			if ( is_array( $meta_fields ) && ! empty( $meta_fields ) ) {
				$fields = array_merge( $fields, $meta_fields );
			}
		}

		/**
		 * Returns a list of meta fields in use with various tickets associated with
		 * a specific event.
		 *
		 * @var array $fields
		 * @var int   $event_id
		 */
		return apply_filters( 'tribe_tickets_plus_get_meta_fields_by_event', $fields, $event_id );
	}

	/**
	 * Metabox to output the Custom Meta fields
	 *
	 * @since 4.1
	 *
	 * @param int $post_id Post ID the ticket is attached to
	 * @param int $ticket_id Ticket ID
	 */
	public function metabox( $post_id, $ticket_id ) {
		if ( ! is_admin() ) {
			return;
		}

		$enable_meta = $this->meta_enabled( $ticket_id );
		$active_meta = $this->get_meta_fields_by_ticket( $ticket_id );

		$templates = $this->meta_fieldset()->get_fieldsets();

		include $this->path . 'src/admin-views/meta.php';

		wp_enqueue_style( 'event-tickets-meta' );
		wp_enqueue_script( 'event-tickets-meta-admin' );
	}

	/**
	 * Returns whether or not custom meta is enabled for the given ticket
	 *
	 * @param int $ticket_id Ticket post ID
	 *
	 * @return bool
	 */
	public function meta_enabled( $ticket_id ) {
		$meta_enabled = get_post_meta( $ticket_id, self::ENABLE_META_KEY, true );

		return (
			'true' === strtolower( $meta_enabled )
			|| 'yes' === strtolower( $meta_enabled )
			|| true === strtolower( $meta_enabled )
			|| 1 == strtolower( $meta_enabled )
		);
	}

	/**
	 * Saves meta configuration on a ticket
	 *
	 * @since 4.1
	 *
	 * @param int $post_id Event ID
	 * @param Tribe__Tickets__Ticket_Object $ticket Ticket object
	 * @param array $data Post data that was submitted
	 */
	public function save_meta( $post_id, $ticket, $data ) {
		// save the enabled/disabled state of the custom meta
		update_post_meta( $ticket->ID, self::ENABLE_META_KEY, empty( $data['show_attendee_info'] ) ? 0 : 1 );

		// if we're not enabling custom meta, don't bother saving the configured fields
		if ( empty( $data['show_attendee_info'] ) ) {
			return;
		}

		if ( empty( $data['tribe-tickets-input'] ) ) {
			$meta = array();
		} else {
			$meta = $this->build_field_array( $ticket->ID, $data );
		}

		update_post_meta( $ticket->ID, self::META_KEY, $meta );

		if ( ! $meta ) {
			return;
		}

		// Save templates too
		if ( isset( $data['tribe-tickets-save-fieldset'] ) && ! empty( $data['tribe-tickets-saved-fieldset-name'] ) ) {
			$fieldset = wp_insert_post( array(
				'post_type' => Tribe__Tickets_Plus__Meta__Fieldset::POSTTYPE,
				'post_title' => $data['tribe-tickets-saved-fieldset-name'],
				'post_status' => 'publish',
			) );

			update_post_meta( $fieldset, Tribe__Tickets_Plus__Meta__Fieldset::META_KEY, $meta );
		}

	}

	public function build_field_array( $ticket_id, $data ) {
		if ( empty( $data['tribe-tickets-input'] ) ) {
			return array();
		}

		$meta = array();

		foreach ( (array) $data['tribe-tickets-input'] as $field_id => $field ) {
			$field_object = $this->generate_field( $ticket_id, $field['type'], $field );

			if ( ! $field_object ) {
				continue;
			}

			$meta[] = $field_object->build_field_settings( $field );
		}

		return $meta;
	}

	/**
	 * Outputs ticket custom meta admin fields for an Ajax request
	 */
	public function ajax_render_fields() {

		$data = null;

		if ( empty( $_POST['type'] ) ) {
			wp_send_json_error( '' );
		}

		$field = $this->generate_field( null, $_POST['type'] );

		if ( $field ) {
			$data = $field->render_admin_field();
		}

		if ( empty( $data ) ) {
			wp_send_json_error( $data );
		}

		wp_send_json_success( $data );
	}

	/**
	 * Outputs ticket custom meta admin fields loaded from a group of pre-saved fields for an Ajax request
	 */
	public function ajax_render_saved_fields() {

		$data = null;

		if ( empty( $_POST['fieldset'] ) ) {
			wp_send_json_error( '' );
		}

		$fieldset = get_post( $_POST['fieldset'] );

		if ( ! $fieldset ) {
			wp_send_json_error( '' );
		}

		$template = get_post_meta( $fieldset->ID, Tribe__Tickets_Plus__Meta__Fieldset::META_KEY, true );

		if ( ! $template ) {
			wp_send_json_error( '' );
		}

		foreach ( (array) $template as $field ) {
			$field_object = $this->generate_field( null, $field['type'], $field );

			if ( ! $field_object ) {
				continue;
			}

			$data .= $field_object->render_admin_field();
		}

		if ( empty( $data ) ) {
			wp_send_json_error( $data );
		}

		wp_send_json_success( $data );
	}

	/**
	 * Generates a field object
	 *
	 * @since 4.1
	 *
	 * @param null|int $ticket_id Ticket ID the field is attached to
	 * @param string $type Type of field being generated
	 * @param array $data Field settings for the field
	 *
	 * @return Tribe__Tickets_Plus__Meta__Field__Abstract_Field child class
	 */
	public function generate_field( $ticket_id, $type, $data = array() ) {
		$class = 'Tribe__Tickets_Plus__Meta__Field__' . ucwords( $type );

		if ( ! class_exists( $class ) ) {
			return null;
		}

		return new $class( $ticket_id, $data );
	}

	/**
	 * Retrieves custom meta data from the cookie
	 *
	 * @since 4.1
	 *
	 * @param int $product_id Commerce provider product ID
	 *
	 * @return array
	 */
	public function get_meta_cookie_data( $product_id ) {
		return $this->storage->get_meta_data_for($product_id);
	}

	/**
	 * Builds the meta data structure for storage in orders
	 *
	 * @since 4.1
	 *
	 * @param array $product_ids Collection of Product IDs in an order
	 *
	 * @return array
	 */
	public function build_order_meta( $product_ids ) {
		if ( ! $product_ids ) {
			return array();
		}

		$meta_object = Tribe__Tickets_Plus__Main::instance()->meta();
		$meta = array();

		foreach ( $product_ids as $product_id ) {
			$data = $meta_object->get_meta_cookie_data( $product_id );

			if ( ! $data ) {
				continue;
			}

			foreach ( $data as $id => $the_meta ) {
				if ( ! isset( $meta[ $id ] ) ) {
					$meta[ $id ] = array();
				}

				$meta[ $id ] = array_merge_recursive( $meta[ $id ], $the_meta );
			}
		}

		if ( empty( $meta ) ) {
			return array();
		}

		return $meta;
	}

	/**
	 * Clears the custom meta data stored in the cookie
	 *
	 * @since 4.1
	 *
	 * @param int $product_id Commerce product ID
	 */
	public function clear_meta_cookie_data( $product_id ) {
		$this->storage->clear_meta_data_for($product_id);
	}

	/**
	 * If the given ticket has attendee meta, render an icon to indicate that
	 */
	public function maybe_render_custom_meta_icon( $ticket ) {
		if ( ! is_admin() ) {
			return;
		}

		$meta = $this->get_meta_fields_by_ticket( $ticket->ID );
		if ( ! $meta ) {
			return;
		}
		?>
		<span title="<?php esc_html_e( 'This ticket has custom Attendee Information fields', 'event-tickets-plus' ); ?>" class="dashicons dashicons-id-alt"></span>
		<?php
	}

	/**
	 * Injects fieldsets into JSON data during ticket add ajax output
	 *
	 * @param array $return Data array to be output in the ajax response for ticket adds
	 * @param int $post_id Post ID number of post that tickets are tied to
	 */
	public function inject_fieldsets_in_json( $return, $post_id ) {
		$return['fieldsets'] = $this->meta_fieldset()->get_fieldsets();
		return $return;
	}
}
