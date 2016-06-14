<?php

wp_enqueue_script('jsapi', 'https://www.google.com/jsapi?autoload=%7B%22modules%22%3A%5B%7B%22name%22%3A%22visualization%22%2C%22version%22%3A%221.1%22%2C%22packages%22%3A%5B%22corechart%22%2C%22geochart%22%2C%22table%22%5D%7D%5D%7D', array(), null );

wp_enqueue_script('better_analytics_admin_js', BETTER_ANALYTICS_PLUGIN_URL . 'assets/digitalpoint/js/admin.js', array(), BETTER_ANALYTICS_VERSION );
wp_enqueue_style('better_analytics_admin_css', BETTER_ANALYTICS_PLUGIN_URL . 'assets/digitalpoint/css/admin.css', array(), BETTER_ANALYTICS_VERSION);

$betterAnalyticsOptions = get_option('better_analytics');
DigitalPointBetterAnalytics_Helper_Api::check();

if (!DigitalPointBetterAnalytics_Base_Public::getInstance()->getTokens() || !$betterAnalyticsOptions['api']['profile'])
{
	printf(esc_html__('%1$sSet up API access%2$s to utilize Better Analytics charts.', 'better-analytics'),
		'<a href="' . esc_url(menu_page_url('better-analytics', false) . '#top#api') . '">',
		'</a>'
	);
}
else
{
	$betterAnalyticsDashboardPick = get_option('ba_dashboard_pick');

	$dimensions = DigitalPointBetterAnalytics_ControllerAdmin_Analytics::getDimensionsForCharts();
	$metrics = DigitalPointBetterAnalytics_ControllerAdmin_Analytics::getMetricsForCharts();

	if (!$betterAnalyticsDashboardPick['dimension'])
	{
		$betterAnalyticsDashboardPick['dimension'] = 'p:ga:date';
	}

	if (!$betterAnalyticsDashboardPick['metric'])
	{
		$betterAnalyticsDashboardPick['metric'] = 'ga:pageviews';
	}

	if (!$betterAnalyticsDashboardPick['days'])
	{
		$betterAnalyticsDashboardPick['days'] = '30';
	}

	echo '<div id="ba_history" style="display: inline; padding-right: 10px;' . ($betterAnalyticsDashboardPick['realtime'] ? 'color=grey;' : '') . '">
		<select id="ba_metric"' . ($betterAnalyticsDashboardPick['realtime'] ? ' disabled="disabled"' : '') . '>';
	foreach ($metrics as $key => $name)
	{
		echo '<option value="' . $key . '"' . ($key == $betterAnalyticsDashboardPick['metric'] ? ' selected="selected"' : '') . '>' . htmlentities($name) . '</option>';
	}
	echo '</select>';


	echo ' &nbsp; ' . esc_html__('by', 'better-analytics') . ' &nbsp; ';

	echo '<select data-placeholder="Pick chart" id="ba_dimension" class="chosen-select"' . ($betterAnalyticsDashboardPick['realtime'] ? ' disabled="disabled"' : '') . '>';
	foreach ($dimensions as $key => $name)
	{
		echo '<option value="' . $key . '"' . ($key == $betterAnalyticsDashboardPick['dimension'] ? ' selected="selected"' : '') . '>' . htmlentities($name) . '</option>';
	}
	echo '</select>';


	echo ' &nbsp; ' . esc_html__('for last', 'better-analytics') . ' &nbsp; ';


	$chartDays = array(
		'1' => esc_html__('1 Day', 'better-analytics'),
		'7' => esc_html__('7 Days', 'better-analytics'),
		'14' => esc_html__('14 Days', 'better-analytics'),
		'30' => esc_html__('1 Month', 'better-analytics'),
		'90' => esc_html__('3 Months', 'better-analytics'),
		'365' => esc_html__('1 Year', 'better-analytics'),
		'1825' => esc_html__('5 Years', 'better-analytics'),
		'3650' => esc_html__('10 Years', 'better-analytics'),
	);


	echo '<select id="ba_days"' . ($betterAnalyticsDashboardPick['realtime'] ? ' disabled="disabled"' : '') . '>';
	foreach ($chartDays as $key => $name)
	{
		echo '<option value="' . $key . '"' . ($key == $betterAnalyticsDashboardPick['days'] ? ' selected="selected"' : '') . '>' . htmlentities($name) . '</option>';
	}
	echo '</select>
</div>';

	echo '<label style="white-space: nowrap;"><input type="checkbox" id="ba_realtime"' . ($betterAnalyticsDashboardPick['realtime'] ? ' checked="checked"' : '') . '> ' . esc_html__('Real-time', 'better-analytics') . '</label>';

	echo '<div id="ba_chart"></div>';

	echo '<div id="ba_realtime_charts">
			<div id="ba_rt_users"><span class="number" data-number="0"></span><div class="label">' . esc_html__('active users', 'better-analytics') . '</div></div><div id="ba_rt_map"></div>
			<div id="ba_rt_medium"></div><div id="ba_rt_device"></div>
			<div id="ba_rt_keywords"></div><div id="ba_rt_referral_path"></div>
			<div id="ba_rt_page_path"></div>
		</div>';

}