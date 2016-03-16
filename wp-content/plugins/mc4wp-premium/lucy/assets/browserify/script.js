'use strict';

var Lucy = require('./third-party/lucy.js');

var lucy = new Lucy(
	'https://mc4wp.com/',
	'DA9YFSTRKA',
	'ce1c93fad15be2b70e0aa0b1c2e52d8e',
	'wpkb_articles',
	[
		{
			text: "<span class=\"dashicons dashicons-book\"></span> Knowledge Base",
			href: "https://mc4wp.com/kb/"
		},
		{
			text: "<span class=\"dashicons dashicons-editor-code\"></span> Code Reference",
			href: "http://developer.mc4wp.com/"
		},
		{
			text: "<span class=\"dashicons dashicons-editor-break\"></span> Changelog",
			href: "http://mc4wp.com/documentation/changelog/"
		}

	],
	lucy_config.email_link
);