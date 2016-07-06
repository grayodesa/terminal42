<?php
global $essb_networks;
$mode = isset ( $_GET ["mode"] ) ? $_GET ["mode"] : "1";
$month = isset ( $_GET ['essb_month'] ) ? $_GET ['essb_month'] : '';
$date = isset ( $_GET ['date'] ) ? $_GET ['date'] : '';

if (!defined('ESSB3_SSA_ACTIVE')) {
	print "<h2>Social Share Analytics is not active. To activate it please go to Social Buttons -> Social Sharing -> Share Analytics and activte it";
	return;
}

ESSBSocialShareAnalyticsBackEnd::init_addional_settings();

// overall stats by social network
if ($date != '') {
	$overall_stats = ESSBSocialShareAnalyticsBackEnd::essb_stats_by_networks ('', '', $date);
	$position_stats = ESSBSocialShareAnalyticsBackEnd::essb_stats_by_position('', '', $date);
}
else {
	$overall_stats = ESSBSocialShareAnalyticsBackEnd::essb_stats_by_networks ($month);
	$position_stats = ESSBSocialShareAnalyticsBackEnd::essb_stats_by_position($month);
	
}


// print_r($overall_stats);

$calculated_total = 0;
$networks_with_data = array ();

if (isset ( $overall_stats )) {
	$cnt = 0;
	foreach ( $essb_networks as $k => $v ) {
		
		$calculated_total += intval ( $overall_stats->{$k} );
		if (intval ( $overall_stats->{$k} ) != 0) {
			$networks_with_data [$k] = $k;
		}
	}
}

$device_stats = ESSBSocialShareAnalyticsBackEnd::essb_stats_by_device ($month);

$today = date ( 'Y-m-d' );
$today_month = date ( 'Y-m' );

$essb_date_to = "";
$essb_date_from = "";

if ($essb_date_to == '') {
	$essb_date_to = date ( "Y-m-d" );
}

if ($essb_date_from == '') {
	$essb_date_from = date ( "Y-m-d", strtotime ( date ( "Y-m-d", strtotime ( date ( "Y-m-d" ) ) ) . "-1 month" ) );
}

if ($mode == "1") {
	$sqlObject = ESSBSocialShareAnalyticsBackEnd::getDateRangeRecords ( $essb_date_from, $essb_date_to );
	// print_r($sqlObject);
	$dataPeriodObject = ESSBSocialShareAnalyticsBackEnd::sqlDateRangeRecordConvert ( $essb_date_from, $essb_date_to, $sqlObject );
	
	$sqlMonthsData = ESSBSocialShareAnalyticsBackEnd::essb_stats_by_networks_by_months ();
}

?>

<div class="essb-dashboard">
<?php if ($mode == '1') { ?>
	<!--  dashboard type2  -->
	<div class="essb-dashboard-panel">
		<div class="essb-dashboard-panel-title">
			<h4>Total clicks on social buttons since statistics is activated</h4>
		</div>
		<div class="essb-dashboard-panel-content">

			<div class="row">
				<div class="oneforth">
					<div class="essb-stats-panel shadow panel100 total">
						<div class="essb-stats-panel-inner">
							<div class="essb-stats-panel-text">Total clicks on share buttons</div>
							<div class="essb-stats-panel-value"><?php echo ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($calculated_total); ?>
						</div>
						</div>
						
				
				
				<?php
				
				if (isset ( $device_stats )) {
					$desktop = $device_stats->desktop;
					$mobile = $device_stats->mobile;
					
					if ($calculated_total != 0) {
						$percentd = $desktop * 100 / $calculated_total;
					}
					else {
						$percentd = 0;
					}
					$print_percentd = round ( $percentd, 2 );
					$percentd = round ( $percentd );
					
					if ($percentd > 90) {
						$percentd -= 2;
					}
					
					if ($calculated_total != 0) {
						$percentm = $mobile * 100 / $calculated_total;
					}
					else {
						$percentm = 0;
					}
					$print_percentm = round ( $percentm, 2 );
					$percentm = round ( $percentm );
					if ($percentm > 90) {
						$percentm -= 2;
					}
				}
				
				?>
				</div>
					<div class="essb-stats-panel shadow panel50">
						<div class="essb-stats-panel-inner">
							<div class="essb-stats-panel-text">
								Desktop <span class="percent"><?php echo $print_percentd;?> %</span>
							</div>
							<div class="essb-stats-panel-value"><?php echo ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($desktop); ?>
						</div>
						</div>
						<div class="essb-stats-panel-graph">

							<div class="graph widget-color-mwp" style="width: <?php echo $percentd;?>%;"></div>

						</div>
					</div>

					<div class="essb-stats-panel shadow panel50">
						<div class="essb-stats-panel-inner">
							<div class="essb-stats-panel-text">
								Mobile <span class="percent"><?php echo $print_percentm;?> %</span>
							</div>
							<div class="essb-stats-panel-value"><?php echo ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($mobile); ?>
						</div>
						</div>
						<div class="essb-stats-panel-graph">

							<div class="graph widget-color-mwp" style="width: <?php echo $percentm;?>%;"></div>

						</div>
					</div>
					<h5>Stats by position</h5>
					<!-- begin stats by displayed position -->
<?php

if (isset ( $overall_stats )) {
	$cnt = 0;
	foreach ( ESSBSocialShareAnalyticsBackEnd::$positions as $k ) {
		
		$key = "position_".$k;
		
		$single = intval ( $position_stats->{$key} );
		
		if ($single > 0) {
			if ($calculated_total != 0) {
				$percent = $single * 100 / $calculated_total;
			}
			else {
				$percent = 0;
			}
			$print_percent = round ( $percent, 2 );
			$percent = round ( $percent );
			?>
			
			<div class="essb-stats-panel shadow panel50">
						<div class="essb-stats-panel-inner">
							<div class="essb-stats-panel-text"><?php echo $k; ?> <span
									class="percent"><?php echo $print_percent;?> %</span>
							</div>
							<div class="essb-stats-panel-value"><?php echo ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($single); ?>
						</div>
						</div>
						<div class="essb-stats-panel-graph">

							<div class="graph widget-color-ok" style="width: <?php echo $percent;?>%;"></div>

						</div>
					</div>
									
									<?php
		}
	}
}

?>					
				</div>



				<div class="threeforth">



					
<?php

if (isset ( $overall_stats )) {
	$cnt = 0;
	foreach ( $essb_networks as $k => $v ) {
		
		$single = intval ( $overall_stats->{$k} );
		
		if ($single > 0) {
			$percent = $single * 100 / $calculated_total;
			$print_percent = round ( $percent, 2 );
			$percent = round ( $percent );
			?>
			
			<div class="essb-stats-panel shadow panel20">
						<div class="essb-stats-panel-inner">
							<div class="essb-stats-panel-text"><?php echo $v["name"]; ?> <span
									class="percent"><?php echo $print_percent;?> %</span>
							</div>
							<div class="essb-stats-panel-value"><?php echo ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($single); ?>
						</div>
						</div>
						<div class="essb-stats-panel-graph">

							<div class="graph widget-color-<?php echo $k; ?>" style="width: <?php echo $percent;?>%;"></div>

						</div>
					</div>
									
									<?php
		}
	}
}

?>
				</div>

			</div>



		</div>
	</div>
	<div class="clear"></div>

	<div class="essb-dashboard-panel">
		<div class="essb-dashboard-panel-title">
			<h4>Social clicks dynamics for the last 30 days</h4>
		</div>
		<div class="essb-dashboard-panel-content" id="essb-changes-graph"
			style="height: 300px;"></div>
	</div>

	<div class="clear"></div>

	<div class="essb-dashboard-panel">
		<div class="essb-dashboard-panel-title">
			<h4>Social clicks by months</h4>

		</div>
		<div class="essb-dashboard-panel-content">
			<?php ESSBSocialShareAnalyticsBackEnd::essb_stat_admin_detail_by_month ($sqlMonthsData, $networks_with_data); ?>
			</div>
	</div>

	<div class="clear"></div>

	<div class="essb-dashboard-panel">
		<div class="essb-dashboard-panel-title">
			<h4>Leading posts in social actions</h4>
			<button class="button-primary"
				style="float: right; margin-top: -22px;"
				onclick="window.location='admin.php?page=essb_redirect_analytics&tab=analytics&mode=3';">Full
				content report</button>
		</div>
		<div class="essb-dashboard-panel-content">
			<?php ESSBSocialShareAnalyticsBackEnd::essb_stat_admin_detail_by_post ('', $networks_with_data, 20); ?>
			</div>
	</div>
	
	<?php } ?>
	
	<?php if ($mode == '2') { ?>
	
	<?php if ($month != '') { ?>
	
	<!--  dashboard type2  -->
	<div class="essb-dashboard-panel">
		<div class="essb-dashboard-panel-title">
			<h4>Total clicks on social buttons for month: <?php echo $month;?></h4>
		</div>
		<div class="essb-dashboard-panel-content">

			<div class="row">
				<div class="oneforth">
					<div class="essb-stats-panel shadow panel100 total">
						<div class="essb-stats-panel-inner">
							<div class="essb-stats-panel-text">Total clicks on share buttons</div>
							<div class="essb-stats-panel-value"><?php echo ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($calculated_total); ?>
						</div>
						</div>
						
				
				
				<?php
				
				if (isset ( $device_stats )) {
					$desktop = $device_stats->desktop;
					$mobile = $device_stats->mobile;
					
					$percentd = $desktop * 100 / $calculated_total;
					$print_percentd = round ( $percentd, 2 );
					$percentd = round ( $percentd );
					
					if ($percentd > 90) {
						$percentd -= 2;
					}
					
					$percentm = $mobile * 100 / $calculated_total;
					$print_percentm = round ( $percentm, 2 );
					$percentm = round ( $percentm );
					if ($percentm > 90) {
						$percentm -= 2;
					}
				}
				
				?>
				</div>
					<div class="essb-stats-panel shadow panel50">
						<div class="essb-stats-panel-inner">
							<div class="essb-stats-panel-text">
								Desktop <span class="percent"><?php echo $print_percentd;?> %</span>
							</div>
							<div class="essb-stats-panel-value"><?php echo ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($desktop); ?>
						</div>
						</div>
						<div class="essb-stats-panel-graph">

							<div class="graph widget-color-mwp" style="width: <?php echo $percentd;?>%;"></div>

						</div>
					</div>

					<div class="essb-stats-panel shadow panel50">
						<div class="essb-stats-panel-inner">
							<div class="essb-stats-panel-text">
								Mobile <span class="percent"><?php echo $print_percentm;?> %</span>
							</div>
							<div class="essb-stats-panel-value"><?php echo ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($mobile); ?>
						</div>
						</div>
						<div class="essb-stats-panel-graph">

							<div class="graph widget-color-mwp" style="width: <?php echo $percentm;?>%;"></div>

						</div>
					</div>
					<h5>Stats by position</h5>
					<!-- begin stats by displayed position -->
<?php

if (isset ( $overall_stats )) {
	$cnt = 0;
	foreach ( ESSBSocialShareAnalyticsBackEnd::$positions as $k ) {
		
		$key = "position_".$k;
		
		$single = intval ( $position_stats->{$key} );
		
		if ($single > 0) {
			$percent = $single * 100 / $calculated_total;
			$print_percent = round ( $percent, 2 );
			$percent = round ( $percent );
			?>
			
			<div class="essb-stats-panel shadow panel50">
						<div class="essb-stats-panel-inner">
							<div class="essb-stats-panel-text"><?php echo $k; ?> <span
									class="percent"><?php echo $print_percent;?> %</span>
							</div>
							<div class="essb-stats-panel-value"><?php echo ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($single); ?>
						</div>
						</div>
						<div class="essb-stats-panel-graph">

							<div class="graph widget-color-ok" style="width: <?php echo $percent;?>%;"></div>

						</div>
					</div>
									
									<?php
		}
	}
}

?>					
				</div>



				<div class="threeforth">



					
<?php

if (isset ( $overall_stats )) {
	$cnt = 0;
	foreach ( $essb_networks as $k => $v ) {
		
		$single = intval ( $overall_stats->{$k} );
		
		if ($single > 0) {
			$percent = $single * 100 / $calculated_total;
			$print_percent = round ( $percent, 2 );
			$percent = round ( $percent );
			?>
			
			<div class="essb-stats-panel shadow panel20">
						<div class="essb-stats-panel-inner">
							<div class="essb-stats-panel-text"><?php echo $v["name"]; ?> <span
									class="percent"><?php echo $print_percent;?> %</span>
							</div>
							<div class="essb-stats-panel-value"><?php echo ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($single); ?>
						</div>
						</div>
						<div class="essb-stats-panel-graph">

							<div class="graph widget-color-<?php echo $k; ?>" style="width: <?php echo $percent;?>%;"></div>

						</div>
					</div>
									
									<?php
		}
	}
}

?>
				</div>

			</div>



		</div>
	</div>
	<div class="clear"></div>
	
	
		<div class="essb-dashboard-panel">
			<div class="essb-dashboard-panel-title">
				<h4>Activity by date of month</h4>
			</div>
			<div class="essb-dashboard-panel-content">
			<?php ESSBSocialShareAnalyticsBackEnd::generate_bar_graph_month($month, $networks_with_data);?>
			</div>
		</div>

		<div class="essb-dashboard-panel">
			<div class="essb-dashboard-panel-title">
				<h4>Content details for this month</h4>
			</div>
			<div class="essb-dashboard-panel-content">
			<?php ESSBSocialShareAnalyticsBackEnd::essb_stat_admin_detail_by_post( $month, $networks_with_data );?>
			</div>
		</div>
	
			
		
		<?php } ?>
	
	<?php } ?>
	
	<?php if ($mode == '3') { ?>
	<div class="essb-dashboard-panel">
			<div class="essb-dashboard-panel-title">
				<h4>Full social activity content report</h4>
			</div>
			<div class="essb-dashboard-panel-content">
			<?php ESSBSocialShareAnalyticsBackEnd::essb_stat_admin_detail_by_post( '', $networks_with_data );?>
			</div>
		</div>
	<?php } ?>
	
	<?php if ($mode == '4') { ?>
	
	<?php if ($date != '') { ?>
	
	<!--  dashboard type2  -->
	<div class="essb-dashboard-panel">
		<div class="essb-dashboard-panel-title">
			<h4>Total clicks on social buttons for date <?php echo $date; ?></h4>
		</div>
		<div class="essb-dashboard-panel-content">

			<div class="row">
				<div class="oneforth">
					<div class="essb-stats-panel shadow panel100 total">
						<div class="essb-stats-panel-inner">
							<div class="essb-stats-panel-text">Total clicks on share buttons</div>
							<div class="essb-stats-panel-value"><?php echo ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($calculated_total); ?>
						</div>
						</div>
						
				
				
				<?php
				
				if (isset ( $device_stats )) {
					$desktop = $device_stats->desktop;
					$mobile = $device_stats->mobile;
					
					$percentd = $desktop * 100 / $calculated_total;
					$print_percentd = round ( $percentd, 2 );
					$percentd = round ( $percentd );
					
					if ($percentd > 90) {
						$percentd -= 2;
					}
					
					$percentm = $mobile * 100 / $calculated_total;
					$print_percentm = round ( $percentm, 2 );
					$percentm = round ( $percentm );
					if ($percentm > 90) {
						$percentm -= 2;
					}
				}
				
				?>
				</div>
					<div class="essb-stats-panel shadow panel50">
						<div class="essb-stats-panel-inner">
							<div class="essb-stats-panel-text">
								Desktop <span class="percent"><?php echo $print_percentd;?> %</span>
							</div>
							<div class="essb-stats-panel-value"><?php echo ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($desktop); ?>
						</div>
						</div>
						<div class="essb-stats-panel-graph">

							<div class="graph widget-color-mwp" style="width: <?php echo $percentd;?>%;"></div>

						</div>
					</div>

					<div class="essb-stats-panel shadow panel50">
						<div class="essb-stats-panel-inner">
							<div class="essb-stats-panel-text">
								Mobile <span class="percent"><?php echo $print_percentm;?> %</span>
							</div>
							<div class="essb-stats-panel-value"><?php echo ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($mobile); ?>
						</div>
						</div>
						<div class="essb-stats-panel-graph">

							<div class="graph widget-color-mwp" style="width: <?php echo $percentm;?>%;"></div>

						</div>
					</div>
					<h5>Stats by position</h5>
					<!-- begin stats by displayed position -->
<?php

if (isset ( $overall_stats )) {
	$cnt = 0;
	foreach ( ESSBSocialShareAnalyticsBackEnd::$positions as $k ) {
		
		$key = "position_".$k;
		
		$single = intval ( $position_stats->{$key} );
		
		if ($single > 0) {
			$percent = $single * 100 / $calculated_total;
			$print_percent = round ( $percent, 2 );
			$percent = round ( $percent );
			?>
			
			<div class="essb-stats-panel shadow panel50">
						<div class="essb-stats-panel-inner">
							<div class="essb-stats-panel-text"><?php echo $k; ?> <span
									class="percent"><?php echo $print_percent;?> %</span>
							</div>
							<div class="essb-stats-panel-value"><?php echo ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($single); ?>
						</div>
						</div>
						<div class="essb-stats-panel-graph">

							<div class="graph widget-color-ok" style="width: <?php echo $percent;?>%;"></div>

						</div>
					</div>
									
									<?php
		}
	}
}

?>					
				</div>



				<div class="threeforth">



					
<?php

if (isset ( $overall_stats )) {
	$cnt = 0;
	foreach ( $essb_networks as $k => $v ) {
		
		$single = intval ( $overall_stats->{$k} );
		
		if ($single > 0) {
			$percent = $single * 100 / $calculated_total;
			$print_percent = round ( $percent, 2 );
			$percent = round ( $percent );
			?>
			
			<div class="essb-stats-panel shadow panel20">
						<div class="essb-stats-panel-inner">
							<div class="essb-stats-panel-text"><?php echo $v["name"]; ?> <span
									class="percent"><?php echo $print_percent;?> %</span>
							</div>
							<div class="essb-stats-panel-value"><?php echo ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($single); ?>
						</div>
						</div>
						<div class="essb-stats-panel-graph">

							<div class="graph widget-color-<?php echo $k; ?>" style="width: <?php echo $percent;?>%;"></div>

						</div>
					</div>
									
									<?php
		}
	}
}

?>
				</div>

			</div>



		</div>
	</div>
	<div class="clear"></div>
	
	
		

		<div class="essb-dashboard-panel">
			<div class="essb-dashboard-panel-title">
				<h4>Content details for this date</h4>
			</div>
			<div class="essb-dashboard-panel-content">
			<?php ESSBSocialShareAnalyticsBackEnd::essb_stat_admin_detail_by_post( '', $networks_with_data, '', $date );?>
			</div>
		</div>
	
			
		
		<?php } ?>
	
	<?php } ?>
</div>

<script type="text/javascript">
jQuery(document).ready(function($){
      <?php
						if ($mode == "1" || $mode == '2') {
							echo ESSBSocialShareAnalyticsBackEnd::keyObjectToMorrisLineGraph ( 'essb-changes-graph', $dataPeriodObject, 'Social activity' );
						}
						?>
});

var essb_analytics_date_report = function(date) {
	window.location='admin.php?page=essb_redirect_analytics&tab=analytics&mode=4&date='+date;

}
	
</script>