<?php

return array(
	'enabled' => 0,
	'subject' => 'New form submission - MailChimp for WordPress',
	'recipients' => get_bloginfo( 'admin_email' ),
	'message_body' => 'Form was submitted with the following data.' . "\r\n\r\n" . '[_ALL_]',
	'content_type' => 'text/html',
);