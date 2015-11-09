<?php
if( ! defined( 'MC4WP_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}
?>
<div id="mc4wp-admin" class="wrap reports">

	<h1 class="page-title">
		<?php _e( 'MailChimp for WordPress', 'mailchimp-for-wp' ); ?>: <?php _e( 'Reports', 'mailchimp-for-wp' ); ?>
	</h1>

	<?php settings_errors(); ?>

	<h2 class="nav-tab-wrapper">  
		<a href="?page=mailchimp-for-wp-reports&tab=statistics" class="nav-tab <?php echo ($tab === 'statistics') ? 'nav-tab-active' : ''; ?>"><?php _e( 'Statistics', 'mailchimp-for-wp' ); ?></a>
		<a href="?page=mailchimp-for-wp-reports&tab=log" class="nav-tab <?php echo ($tab === 'log') ? 'nav-tab-active' : ''; ?>"><?php _e( 'Log', 'mailchimp-for-wp' ); ?></a>
		<a href="?page=mailchimp-for-wp-reports&tab=export" class="nav-tab <?php echo ($tab === 'export') ? 'nav-tab-active' : ''; ?>"><?php _e( 'Export', 'mailchimp-for-wp' ); ?></a>
	</h2>

	<br class="clear" />

	<?php

	if( file_exists( dirname( __FILE__ ) . "/../tabs/admin-reports-{$tab}.php" ) ) {
		require dirname( __FILE__ ) . "/../tabs/admin-reports-{$tab}.php";
	}

	require dirname( __FILE__ ) . '/../parts/admin-footer.php';

	?>

</div>