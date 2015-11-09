<?php
if( ! defined( 'MC4WP_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}
?>

<?php $table->views(); ?>
<form method="get" action="<?php echo admin_url( 'admin.php' ); ?>">
    <input type="hidden" name="page" value="mailchimp-for-wp-reports" />
    <input type="hidden" name="tab" value="log" />
	<?php $table->search_box( 'search', 'mc4wp-log-search' ); ?>
	<?php $table->display(); ?>
</form>