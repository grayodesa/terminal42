<?php

class MC4WP_Dashboard_Log_Widget {

	/**
	 * Factory method
	 */
	public static function make() {
		$widget = new self;
		$widget->output();
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->logger = new MC4WP_Logger();
	}

	/**
	 * Output the widget code
	 */
	public function output() {
		$items = $this->logger->find(
			array(
				'limit' => 5,
				'include_errors' => false
			)
		);

		if( empty( $items ) ) {
			echo '<p>' . __( "No log entries found.", 'mailchimp-for-wp' ) . '</p>';
		} else {
			?>
			<style type="text/css" scoped>
				.mc4wp-dashboard-table {
					table-layout: fixed;
					width: 100%;
				}

				.mc4wp-dashboard-table th {
					text-align: left;
				}

				.mc4wp-dashboard-table td {
					overflow: hidden;
				}
			</style>

			<table class="mc4wp-dashboard-table">
			<thead>
				<tr>
					<th><?php _e( 'Email address', 'mailchimp-for-wp' ); ?></th>
					<th><?php _e( 'Date', 'mailchimp-for-wp' ); ?></th>
				</tr>
			</thead>
			<?php foreach( $items as $item ) { ?>
				<tr>
					<td><a href="<?php echo admin_url('admin.php?page=mailchimp-for-wp-reports&tab=log#item-' . $item->ID ) ?>"><?php echo esc_html( $item->email ); ?></a></td>
					<td><?php echo MC4WP_Tools::mysql_datetime_to_local_datetime( $item->datetime, 'M, j H:i' ); ?></td>
				</tr>
			<?php } ?>
			</table>
			<?php
		}

		echo '<p><a href="' . admin_url('admin.php?page=mailchimp-for-wp-reports&tab=log') . '">' . __( 'View entire log', 'mailchimp-for-wp' ) . '</a></p>';
	}
}