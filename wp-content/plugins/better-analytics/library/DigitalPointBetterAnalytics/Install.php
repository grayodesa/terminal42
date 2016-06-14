<?php

class DigitalPointBetterAnalytics_Install
{
	public static function uninstall()
	{
		if (is_multisite()) // Multi-site install
		{
			foreach (wp_get_sites() as $blog)
			{
				switch_to_blog($blog['blog_id']);

				self::_uninstallAction();

				restore_current_blog();
			}
			delete_site_option('ba_site_tokens');
			delete_site_option('better_analytics_site');
		}
		else
		{ // Cleanup Single install
			self::_uninstallAction();
		}
	}

	protected static function _uninstallAction()
	{
		// Delete transients
		DigitalPointBetterAnalytics_CronEntry_Jobs::hour(true);

		delete_option('ba_dashboard_pick');
		delete_option('ba_tokens');
		delete_option('better_analytics');
	}
}
