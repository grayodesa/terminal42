<?php

class DigitalPointBetterAnalytics_Helper_Api
{
	public static function check($force = false)
	{
		$betterAnalyticsInternal = get_transient('ba_int');

		if ($force || @$betterAnalyticsInternal['d'] + 21600 < time()) // 6 hours
		{
			set_transient('ba_int', array('d' => time(), 'l' => null, 'v' => false));
			return false;
		}
		else
		{
			return @$betterAnalyticsInternal['v'];
		}
	}
}