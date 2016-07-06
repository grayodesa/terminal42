<?php
global $esml_data, $essb_options;

$esml_data = new ESMLRenderResultsHelper ();
$esml_data->generate_data ( $essb_options );
function esml_render_dashboard_view($options) {

}

if (ESSB3_ADDONS_ACTIVE) {
	$addons = ESSBAddonsHelper::get_instance();
	$new_addons = $addons->get_new_addons();

	foreach ($new_addons as $key => $data) {
		$all_addons_button = '<a href="'.admin_url ("admin.php?page=essb_addons").'"  text="' . __ ( 'Add-ons', ESSB3_TEXT_DOMAIN ) . '" class="button button-orange float_right" style="margin-right: 5px;"><i class="fa fa-gear"></i>&nbsp;' . __ ( 'View list of all addons', ESSB3_TEXT_DOMAIN ) . '</a>';

		$dismiss_url = esc_url_raw(add_query_arg(array('dismiss' => 'true', 'addon' => $key), admin_url ("admin.php?page=essb_options")));

		$dismiss_addons_button = '<a href="'.$dismiss_url.'"  text="' . __ ( 'Add-ons', ESSB3_TEXT_DOMAIN ) . '" class="button button-orange float_right" style="margin-right: 5px;"><i class="fa fa-close"></i>&nbsp;' . __ ( 'Dismiss', ESSB3_TEXT_DOMAIN ) . '</a>';
		printf ( '<div class="essb-information-box fade"><div class="icon orange"><i class="fa fa-cube"></i></div><div class="inner">New add-on for Easy Social Share Buttons for WordPress is available: <a href="%2$s" target="_blank"><b>%1$s</b></a> %4$s%3$s</div></div>', $data['title'], $data['url'], $all_addons_button, $dismiss_addons_button );
	}
}

?>


<div class="wrap">
	<div class="essb-title-panel">
	<?php echo '<a href="http://support.creoworx.com" target="_blank" text="' . __ ( 'Need Help? Click here to visit our support center', ESSB3_TEXT_DOMAIN ) . '" class="button float_right">' . __ ( 'Support Center', ESSB3_TEXT_DOMAIN ) . '</a>'; ?>
	
	<h3>Easy Social Metrics Lite</h3>
		<p>
			Easy Social Share Buttons for WordPress Version <strong><?php echo ESSB3_VERSION;?></strong>.
			&nbsp;<strong><a href="http://fb.creoworx.com/essb/change-log/"
				target="_blank">See what's new in this version</a></strong>&nbsp;&nbsp;&nbsp;<strong><a
				href="http://codecanyon.net/item/easy-social-share-buttons-for-wordpress/6394476?ref=appscreo"
				target="_blank">Easy Social Share Buttons plugin homepage</a></strong>
		</p>
	</div>

	<div class="essb-clear"></div>
	
	<?php EasySocialMetricsUpdater::printQueueLength(); ?>     

	<div class="essb-clear"></div>

	<div class="essb-title-panel">
	<form id="easy-social-metrics-lite" method="get" action="admin.php?page=easy-social-metrics-lite">
	<input type="hidden" name="page" value="<?php echo sanitize_text_field($_REQUEST['page']) ?>" />
	<?php
	$range = (isset ( $_GET ['range'] )) ? $_GET ['range'] : 0;
	?>
	    			<label for="range">Show only:</label> <select name="range">
			<option value="1"
				<?php if ($range == 1) echo 'selected="selected"'; ?>>Items
				published within 1 Month</option>
			<option value="3"
				<?php if ($range == 3) echo 'selected="selected"'; ?>>Items
				published within 3 Months</option>
			<option value="6"
				<?php if ($range == 6) echo 'selected="selected"'; ?>>Items
				published within 6 Months</option>
			<option value="12"
				<?php if ($range == 12) echo 'selected="selected"'; ?>>Items
				published within 12 Months</option>
			<option value="0"
				<?php if ($range == 0) echo 'selected="selected"'; ?>>Items
				published anytime</option>
		</select>
	    					
	    					<?php do_action( 'esml_dashboard_query_options' ); // Allows developers to add additional sort options ?>
	    
	    					<input type="submit" name="filter" id="submit_filter"
			class="button" value="Filter"> <a
			href="<?php echo admin_url('admin.php?page=easy-social-metrics-lite&esml_sync_all=true'); ?>"
			class="button">Update all posts</a>
	    			<?php
								?>
								</form>
	</div>

	<!-- dashboard start -->
	<div class="essb-dashboard">

		<div class="row">

			<div class="twocols">
				<div class="essb-dashboard-panel">
					<div class="essb-dashboard-panel-title">
						<h4>Social Network Presentation</h4>
					</div>
					<div class="essb-dashboard-panel-content">
					<?php
					$esml_data->output_total_results ();
					
					//$esml_data->output_total_chart();
					?>
					</div>
				</div>
			</div>

			<div class="twocols left">
				<div class="essb-dashboard-panel">
					<div class="essb-dashboard-panel-title">
						<h4>Top Shared Content by Social Network</h4>
					</div>
					<div class="essb-dashboard-panel-content">
					<?php
					$esml_data->output_total_content ();
					?>
					</div>
				</div>
			</div>

		</div>

		<div class="row">

			<div class="essb-dashboard-panel">
				<div class="essb-dashboard-panel-title">
					<h4>Detailed Content Report</h4>
				</div>
				<div class="essb-dashboard-panel-content">
					<?php
					$esml_data->output_main_result ();
					?>
					</div>
			</div>
		</div>

	</div>
</div>

<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#esml-result').DataTable({ pageLength: 50});
} );
</script>

