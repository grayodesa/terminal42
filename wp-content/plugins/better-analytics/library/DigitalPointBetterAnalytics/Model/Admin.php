<?php

class DigitalPointBetterAnalytics_Model_Admin
{
	public static function isLocaleSupported(&$locales = array())
	{
		$locales = array('en_US');
		/*
		foreach(glob(BETTER_ANALYTICS_PLUGIN_DIR . 'languages/*.mo') as $file)
		{
			if (preg_match('#better-analytics-(.*?)\.mo#si', $file, $matches))
			{
				$locales[] = $matches[1];
			}
		}
		*/
		return (array_search(get_locale(), $locales) !== false ? true : false);
	}
}