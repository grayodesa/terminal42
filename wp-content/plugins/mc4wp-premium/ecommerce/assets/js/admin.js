(function () { var require = undefined; var define = undefined; (function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
function ProgressBar( element, count ) {
	var wrapper = element,
		bar = document.createElement('div'),
		step_size = 100 / count,
		progress = 0;

	wrapper.style.height = "40px";
	wrapper.style.width = "100%";
	wrapper.style.border = "1px solid #ccc";
	wrapper.style.lineHeight = "40px";

	bar.style.boxSizing = 'border-box';
	bar.style.backgroundColor = '#cc4444';
	bar.style.textAlign = 'center';
	bar.style.fontWeight = 'bold';
	bar.style.height = "100%";
	bar.style.color = 'white';
	bar.style.fontSize = '16px';
	bar.style.width = progress + "%";
	wrapper.appendChild( bar );

	function tick( ticks ) {
		if( done() ) { return; }

		ticks = ticks === undefined ? 1 : ticks;
		progress += ( step_size * ticks );
		bar.style.width = progress + "%";

		bar.innerHTML =  parseInt( progress ) + "%";

		if( done() ) {
			bar.innerHTML = 'Done!';
		}
	}

	function done() {
		return progress >= 100;
	}

	return {
		'tick': tick,
		'done': done
	}
}

module.exports = ProgressBar;
},{}],2:[function(require,module,exports){
function request( url, options ) {

	var request = new XMLHttpRequest();
	request.onreadystatechange = function() {
		if (this.readyState === 4) {
			if (this.status >= 200 && this.status < 400) {
				options.onSuccess && options.onSuccess(this.responseText);
			} else {
				options.onError && options.onError( this.status, this.responseText);
			}
		}
	};
	request.open(options.method || 'GET', url, true);

	if( options.method && options.method.toUpperCase() === 'POST' ) {
		request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
	}

	request.send( options.data || {} );
	return request;
}

module.exports = request;
},{}],3:[function(require,module,exports){
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
},{"./_progress-bar.js":1,"./_request.js":2}]},{},[3]);
 })();