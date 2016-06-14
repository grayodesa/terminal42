<?php
	header('Content-Type: application/javascript');
	header('Cache-Control: public, max-age=31536000');
	readfile('ba.js');
	if (file_exists('../../better-analytics-pro/js/ba.js'))
	{
		readfile('../../better-analytics-pro/js/ba.js');
	}
	if (file_exists('../../better-analytics-ecommerce/js/ba.js'))
	{
		readfile('../../better-analytics-ecommerce/js/ba.js');
	}
	echo 'BetterAnalytics._BA=new BetterAnalytics.BA();';
	exit;