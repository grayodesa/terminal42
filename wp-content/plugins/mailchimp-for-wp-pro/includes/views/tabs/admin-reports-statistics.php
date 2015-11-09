<?php
if( ! defined( 'MC4WP_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}
?>

    <form method="get">
        <div class="tablenav top">
            <div class="alignleft actions">
                <input type="hidden" name="page" value="mailchimp-for-wp-reports" />

                <select id="mc4wp-graph-range" name="range">
					<option value="today" <?php selected( $range, 'today' ); ?>><?php _e( 'Today', 'mailchimp-for-wp' ); ?></option>
					<option value="yesterday" <?php selected( $range, 'yesterday' ); ?>><?php _e( 'Yesterday', 'mailchimp-for-wp' ); ?></option>
					<option value="last_week" <?php selected( $range, 'last_week' ); ?>><?php _e( 'Last Week', 'mailchimp-for-wp' ); ?></option>
					<option value="last_month" <?php selected( $range, 'last_month' ); ?>><?php _e( 'Last Month', 'mailchimp-for-wp' ); ?></option>
					<option value="last_quarter" <?php selected( $range, 'last_quarter' ); ?>><?php _e( 'Last Quarter', 'mailchimp-for-wp' ); ?></option>
					<option value="last_year" <?php selected( $range, 'last_year' ); ?>><?php _e( 'Last Year', 'mailchimp-for-wp' ); ?></option>
					<option value="custom" <?php selected( $range, 'custom' ); ?>><?php _e( 'Custom', 'mailchimp-for-wp' ); ?></option>
                </select>

				<div id="mc4wp-graph-custom-range-options" <?php if( $range !== 'custom' ) { ?>style="display:none;"<?php } ?>>
                    <span><?php _e( 'From', 'mailchimp-for-wp' ); ?> </span>
                    <select name="start_day">
						<?php for($day = 1; $day <= 31; $day++) { ?>
							<option <?php selected( $start_day, $day ); ?>><?php echo $day++; ?></option>
						<?php } ?>
                    </select>
                    <select name="start_month">
						<option value="1" <?php selected( $start_month, '1' ); ?>><?php _e( 'Jan', 'mailchimp-for-wp' ); ?></option>
						<option value="2" <?php selected( $start_month, '2' ); ?>><?php _e( 'Feb', 'mailchimp-for-wp' ); ?></option>
						<option value="3" <?php selected( $start_month, '3' ); ?>><?php _e( 'Mar', 'mailchimp-for-wp' ); ?></option>
						<option value="4" <?php selected( $start_month, '4' ); ?>><?php _e( 'Apr', 'mailchimp-for-wp' ); ?></option>
						<option value="5" <?php selected( $start_month, '5' ); ?>><?php _e( 'May', 'mailchimp-for-wp' ); ?></option>
						<option value="6" <?php selected( $start_month, '6' ); ?>><?php _e( 'Jun', 'mailchimp-for-wp' ); ?></option>
						<option value="7" <?php selected( $start_month, '7' ); ?>><?php _e( 'Jul', 'mailchimp-for-wp' ); ?></option>
						<option value="8" <?php selected( $start_month, '8' ); ?>><?php _e( 'Aug', 'mailchimp-for-wp' ); ?></option>
						<option value="9" <?php selected( $start_month, '9' ); ?>><?php _e( 'Sep', 'mailchimp-for-wp' ); ?></option>
						<option value="10" <?php selected( $start_month, '10' ); ?>><?php _e( 'Oct', 'mailchimp-for-wp' ); ?></option>
						<option value="11" <?php selected( $start_month, '11' ); ?>><?php _e( 'Nov', 'mailchimp-for-wp' ); ?></option>
						<option value="12" <?php selected( $start_month, '12' ); ?>><?php _e( 'Dec', 'mailchimp-for-wp' ); ?></option>
                    </select>
                    <select name="start_year">
	                    <?php foreach( range( 2013, date( 'Y' ) ) as $year ) { ?>
		                    <option value="<?php echo $year; ?>" <?php selected( $start_year, $year ); ?>><?php echo $year; ?></option>
	                    <?php } ?>
                    </select>
                    <span><?php _e( 'To', 'mailchimp-for-wp' ); ?> </span>
                    <select name="end_day">
						<?php for($day = 1; $day <= 31; $day++) { ?>
							<option <?php selected( $end_day, $day ); ?>><?php echo $day; ?></option>
						<?php } ?>
                    </select>
                    <select name="end_month">
	                    <option value="1" <?php selected( $end_month, '1' ); ?>><?php _e( 'Jan', 'mailchimp-for-wp' ); ?></option>
	                    <option value="2" <?php selected( $end_month, '2' ); ?>><?php _e( 'Feb', 'mailchimp-for-wp' ); ?></option>
	                    <option value="3" <?php selected( $end_month, '3' ); ?>><?php _e( 'Mar', 'mailchimp-for-wp' ); ?></option>
	                    <option value="4" <?php selected( $end_month, '4' ); ?>><?php _e( 'Apr', 'mailchimp-for-wp' ); ?></option>
	                    <option value="5" <?php selected( $end_month, '5' ); ?>><?php _e( 'May', 'mailchimp-for-wp' ); ?></option>
	                    <option value="6" <?php selected( $end_month, '6' ); ?>><?php _e( 'Jun', 'mailchimp-for-wp' ); ?></option>
	                    <option value="7" <?php selected( $end_month, '7' ); ?>><?php _e( 'Jul', 'mailchimp-for-wp' ); ?></option>
	                    <option value="8" <?php selected( $end_month, '8' ); ?>><?php _e( 'Aug', 'mailchimp-for-wp' ); ?></option>
	                    <option value="9" <?php selected( $end_month, '9' ); ?>><?php _e( 'Sep', 'mailchimp-for-wp' ); ?></option>
	                    <option value="10" <?php selected( $end_month, '10' ); ?>><?php _e( 'Oct', 'mailchimp-for-wp' ); ?></option>
	                    <option value="11" <?php selected( $end_month, '11' ); ?>><?php _e( 'Nov', 'mailchimp-for-wp' ); ?></option>
	                    <option value="12" <?php selected( $end_month, '12' ); ?>><?php _e( 'Dec', 'mailchimp-for-wp' ); ?></option>
                    </select>
                    <select  name="end_year">
	                    <?php foreach( range( 2013, date( 'Y' ) ) as $year ) { ?>
		                    <option value="<?php echo $year; ?>" <?php selected( $end_year, $year ); ?>><?php echo $year; ?></option>
	                    <?php } ?>
                    </select>
                </div>
                <input type="submit" class="button-secondary" value="<?php esc_attr_e( 'Filter', 'mailchimp-for-wp' ); ?>">
            </div>
        </div>
    </form>

    <div id="mc4wp-graph"></div>
        
    <h3><?php _e( 'Show these lines:', 'mailchimp-for-wp' ); ?> </h3>
    
    <p id="mc4wp-graph-line-toggles">
		<?php foreach( $statistics_data as $key => $data ) { ?>
			<label <?php if( $data['total_count'] == 0 ) { echo 'class="disabled"'; } ?>><input type="checkbox" name="mc4wp_graph_toggle" value="<?php echo $key; ?>" <?php if( $data['total_count'] == 0 ) { echo 'disabled '; } checked( $key, 'totals' ); ?>/> <?php echo $data['label']; ?> (<?php echo $data['total_count']; ?>)</label>
		<?php } ?>
    </p>
    
    <div id="mc4wp-graph-summary">
		<?php /*<p>Total sign-ups in shown period: <?php echo $totals['all']; ?></p>
        <p>Total form sign-ups in shown period: <?php echo $totals['form']; ?></p>
		<p>Total checkbox sign-ups in shown period: <?php echo $totals['checkbox']; ?></p> */ ?>
    </div>
