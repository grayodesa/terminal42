<?php
/**
 * Dashboard popup template: Message to display after project was installed.
 *
 * Displays a success message.
 *
 * Following variables are passed into the template:
 *   $pid (project ID)
 *
 * @since  4.0.0
 * @package WPMUDEV_Dashboard
 */

$item = WPMUDEV_Dashboard::$site->get_project_infos( $pid, true );

if ( ! $item || ! is_object( $item ) ) {
	include 'popup-no-data-found.php';
	return;
}

if ( 'plugin' == $item->type ) {
	$title = __( 'Plugin installed!', 'wpmudev' );
} else {
	$title = __( 'Theme installed!', 'wpmudev' );
}

?>
<dialog title="<?php echo esc_html( $title ); ?>" class="small no-margin">

<div class="wdp-success-msg">
<?php
printf(
	esc_html__( 'Successfully installed %s', 'wpmudev' ),
	'<strong>' . esc_html( $item->name ) . '</strong>'
);
?>

<p class="buttons">
	<a href="#close" class="close button button-small"><?php esc_html_e( 'Okay', 'wpmudev' ); ?></a>
</p>
</div>

</dialog>