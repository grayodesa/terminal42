<?php
if( ! defined( 'MC4WP_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

?>
<p class="help">
	<?php printf( __( 'Need help? Have a look at the <a href="%s">plugin documentation</a> or email us directly at <a href="%s">support@mc4wp.com</a>.', 'mailchimp-for-wp' ), 'https://mc4wp.com/kb/', 'mailto:support%40mc4wp.com?subject=MailChimp%20for%20WP%20premium%20support&body=My%20website%3A%20' . site_url() . '%0AMailChimp%20for%20WP%20v' . MC4WP_VERSION . '%0ALicense%20Key:%20'. $this->license_manager->get_license_key() .'%0AWordPress%20v' . get_bloginfo( 'version' ) . '%0APHP%20v' . phpversion() . '%0A%0A' ); ?>.
	<?php printf( __( 'Please use the same email address as you used when purchasing the plugin.', 'mailchimp-for-wp' ) ); ?>
</p>
<p class="help"><?php _e( 'This plugin is not developed by or affiliated with MailChimp in any way.', 'mailchimp-for-wp' ); ?></p>
