<?php
/**
 * Handles adding attendee meta data to attendee list exports.
 */
class Tribe__Tickets_Plus__Meta__Export {
	/**
	 * List of the possible meta columns for any given event.
	 *
	 * @var array
	 */
	protected $meta_columns = array();


	/**
	 * Listen out for the generation of a filtered (exportable) attendee list:
	 * we don't need to do anything unless that fires.
	 */
	public function __construct() {
		add_action( 'tribe_events_tickets_generate_filtered_attendees_list', array( $this, 'setup_columns' ) );
	}

	/**
	 * If the current event has tickets that support attendee meta data, hook into
	 * the list to add the appropriate number of extra columns.
	 *
	 * @param int $event_id
	 */
	public function setup_columns( $event_id ) {
		$this->meta_columns = Tribe__Tickets_Plus__Main::instance()->meta()->get_meta_fields_by_event( $event_id );

		if ( empty( $this->meta_columns ) ) {
			return;
		}

		//Add Handler for Community Tickets to Prevent Notices in Exports
		if ( ! is_admin() ) {
			$screen_base = 'tribe_events_page_tickets-attendees';
		} else {
			$screen      = get_current_screen();
			$screen_base = $screen->base;
		}
		$filter_name = "manage_{$screen_base}_columns";

		add_filter( $filter_name, array( $this, 'add_columns' ), 20 );
		add_filter( 'tribe_events_tickets_attendees_table_column', array( $this, 'populate_columns' ), 10, 3 );
	}

	/**
	 * Add headers for our extra columns.
	 *
	 * @param array $columns
	 *
	 * @return array
	 */
	public function add_columns( $columns ) {

		foreach ( $this->meta_columns as $meta_field ) {
			if ( 'checkbox' === $meta_field->type && isset( $meta_field->extra['options'] ) ) {
				foreach ( $meta_field->extra['options'] as $option ) {
					$key = $meta_field->slug . '_' . sanitize_title( $option );

					$columns[ $key ] = "{$meta_field->label}: {$option}";
				}
				continue;
			}

			$columns[ $meta_field->slug ] = $meta_field->label;
		}

		return $columns;
	}

	/**
	 * Handle the actual population of attendee meta fields.
	 *
	 * @param string $existing
	 * @param array  $item
	 * @param string $column
	 *
	 * @return string
	 */
	public function populate_columns( $existing, $item, $column ) {
		$meta_data = get_post_meta( $item['attendee_id'], Tribe__Tickets_Plus__Meta::META_KEY, true );

		if ( isset( $meta_data[ $column ] ) ) {
			return $meta_data[ $column ];
		}

		return $existing;
	}
}
