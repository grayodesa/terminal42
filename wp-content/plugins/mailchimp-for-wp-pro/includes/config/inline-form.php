<?php
$email_placeholder = __( 'Your email address', 'mailchimp-for-wp' );
$signup_button = __( 'Sign up', 'mailchimp-for-wp' );

$markup ="<p>\n\t<input type=\"email\" name=\"EMAIL\" required placeholder=\"{$email_placeholder}\" />\n";
$markup .= "\t<input type=\"submit\" value=\"{$signup_button}\" />\n</p>";

return $markup;