'use strict';

var ProgressBar = require('./_progress-bar.js');
var request = require('./_request.js');

var count = mc4wp_ecommerce.untracked_order_count;
var form = document.getElementById('add-untracked-orders-form');
var progress_bar,
	progress_poll,
	worker;

// hook into form submit
if( form ) {
	form.addEventListener('submit', start );
}

function start(e) {
	// prevent default form submit
	e.preventDefault();

	var button = form.querySelector('input[type="submit"]');
	button.setAttribute('disabled', true);

	// init progress bar
	progress_bar = new ProgressBar(document.getElementById('add-untracked-orders-progress'), count);
	progress_poll = window.setTimeout(fetchProgress, 500);

	work();
}

function work() {
	var limit = parseInt( form.elements["limit"].value );
	var offset = parseInt( form.elements["offset"].value );
	var url = ajaxurl + "?action=mc4wp_ecommerce_add_untracked_orders&offset=" + offset + "&limit=" + limit;

	worker = request( url, {
		onSuccess: function(data) {
			updateProgress(data);

			// Keep going if there's more
			// TODO: This needs a failsafe
			if( data > 0 ) {
				work();
			}
		},

		onError: function( code, response ) {
			// if we got a 504 Gateway Timeout, try again.
			if( code == 504 ) {
				work();
			}
		}
	});
}

function updateProgress(new_count) {
	progress_bar.tick( count - new_count );
	count = new_count;
}

function fetchProgress() {

	if( progress_bar.done() ) {

		// refresh page
		window.setTimeout( function() {
			window.location.reload()
		}, 2500 );

		return;
	}

	var url = ajaxurl + "?action=mc4wp_ecommerce_get_untracked_orders_count";
	request( url, {
		onSuccess: function(data) {
			updateProgress(data);
			window.setTimeout( fetchProgress, 2000 );
		}
	});
}