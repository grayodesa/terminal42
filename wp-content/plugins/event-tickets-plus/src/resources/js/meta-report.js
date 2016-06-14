var tribe_event_tickets_plus = tribe_event_tickets_plus || {};
tribe_event_tickets_plus.meta = tribe_event_tickets_plus.meta || {};
tribe_event_tickets_plus.meta.report = tribe_event_tickets_plus.meta.report || {};
tribe_event_tickets_plus.meta.report.event = tribe_event_tickets_plus.meta.report.event || {};

(function ( window, document, $, my ) {
	'use strict';

	/**
	 * Initializes the meta functionality
	 */
	my.init = function() {
		$( '.wp-list-table.attendees' ).on( 'click', '.event-tickets-meta-toggle', this.event.toggle_meta_view );
	};

	/**
	 * Toggles an attendee's meta data open/closed
	 */
	my.toggle_meta_view = function( $row ) {
		$row.toggleClass( 'event-tickets-meta-toggle-open' );
	};

	/**
	 * Event to handle the toggling of an attendee's meta data open/closed
	 */
	my.event.toggle_meta_view = function( e ) {
		e.preventDefault();

		my.toggle_meta_view( $( this ).closest( 'tr' ) );
	};

	$( function() {
		my.init();
	} );
} )( window, document, jQuery, tribe_event_tickets_plus.meta.report );
