<?php

class DigitalPointBetterAnalytics_CronEntry_Jobs
{
	public static function minute()
	{
		if (is_active_widget(false, false, 'better-analytics_popular_widget'))
		{
			DigitalPointBetterAnalytics_Model_Widget::getRealtimeData();
		}
	}

	public static function hour($all = false)
	{
		DigitalPointBetterAnalytics_Model_Widget::getStatsWidgetData();

		//TODO: do this based on a hash of site name instead of current_time() - API method stats are showing very few sites change default UTC time, so there's a flood of API calls at midnight UTC

		if (get_transient('ba_exp_live') || date('G', current_time('timestamp')) == 0)
		{
			$betterAnalyticsOptions = get_option('better_analytics');

			if ($profile = DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->getProfileByProfileId($betterAnalyticsOptions['api']['profile']))
			{
				DigitalPointBetterAnalytics_Model_Experiments::getAllExperiments($profile['accountId'], $profile['webPropertyId'], $profile['id']);
			}
		}

		// This really should be a core WordPress function (deleting expired transients), but w/e...
		global $wpdb;

		if (!$all)
		{
			$time = time();
		}
		else
		{
			$time = time() + (86400 * 365);
		}

		$sql = "DELETE a, b FROM $wpdb->options a, $wpdb->options b
                WHERE a.option_name LIKE %s
                AND a.option_name NOT LIKE %s
                AND b.option_name = CONCAT( '_transient_timeout_', SUBSTRING( a.option_name, 12 ) )
                AND b.option_value < %d";
		$wpdb->query( $wpdb->prepare( $sql, $wpdb->esc_like( '_transient_ba_' ) . '%', $wpdb->esc_like( '_transient_timeout_' ) . '%', $time ) );
	}
}