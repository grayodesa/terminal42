(function () { var require = undefined; var define = undefined; (function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
'use strict';

var mount = document.getElementById( 'email-message-template-tags' );
var tags = [];
var tagOpen = '[';
var tagClose = ']';

function updateAvailableEmailTags() {
	var fields = mc4wp.forms.editor.query('[name]');
	tags = [ '_ALL_' ];

	for( var i=0; i<fields.length; i++) {

		var tagName = fields[i].getAttribute('name').toUpperCase();

		// strip empty arrays []
		// add in semicolon for named array keys
		tagName = tagName
			.replace('[]','')
			.replace(/\[(\w+)\]/, ':$1');

		if( tags.indexOf( tagName ) < 0 ) {
			tags.push( tagName );
		}

	}

	mount.innerHTML = tags.map(function(tagName) {
		return '<input readonly style="background: transparent; border: 0;" onclick="this.select();" onfocus="this.select()" value="' + tagOpen + tagName + tagClose + '" />';
	}).join(' ');
}

window.addEventListener('load', function() {
	mc4wp.forms.editor.on('change', mc4wp.helpers.debounce(updateAvailableEmailTags, 1000 ) );
	updateAvailableEmailTags();
});
},{}]},{},[1]);
 })();