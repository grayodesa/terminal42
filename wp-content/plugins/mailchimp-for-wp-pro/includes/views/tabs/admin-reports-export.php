<?php
if( ! defined( 'MC4WP_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

$current_year = date('Y');
$current_month = date('n');
?>
<div class="metabox-holder">
<div class="postbox edd-export-log">
	<h3 style="margin-top: 0;"><span>Export sign-up attempts</span></h3>
	<div class="inside">
		<form method="POST">
			<input type="hidden" name="_mc4wp_action" value="export_log" />
			<p>
				<?php _e( 'Use the following button to export your entire log to a CSV file.', 'mailchimp-for-wp' ); ?>
			</p>
			<p>
				<select name="start_year">
					<?php for( $i=2012; $i <= $current_year; $i++) { ?>
						<option><?php echo $i; ?></option>
					<?php } ?>
				</select>
				<select name="start_month">
					<?php foreach( $this->get_months() as $index => $month) { ?>
						<option value="<?php echo $index; ?>"><?php echo $month; ?></option>
					<?php }?>
				</select>
				<?php _e( 'to', 'mailchimp-for-wp' ); ?>
				<select name="end_year">
					<?php for( $i=2012; $i <= $current_year; $i++) { ?>
						<option <?php selected( $i, $current_year ); ?>><?php echo $i; ?></option>
					<?php
					}
					?>
				</select>
				<select name="end_month">
					<?php foreach( $this->get_months() as $index => $month) { ?>
						<option value="<?php echo $index; ?>" <?php selected( $index, $current_month ); ?>><?php echo $month; ?></option>
					<?php }?>
				</select>
			</p>
			<p>
				<label>
					<input type="checkbox" name="include_errors" value="1" />
					<?php _e( 'Include failures?', 'mailchimp-for-wp' ); ?>
				</label>
			</p>
			<p>
				<input type="submit" class="button" value="<?php esc_attr_e( 'Export to CSV', 'mailchimp-for-wp' ); ?>" />
			</p>
		</form>
	</div><!-- .inside -->
</div>
</div>