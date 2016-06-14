<?php

class DigitalPointBetterAnalytics_Model_Reporting
{
	public static function getMetrics()
	{
		$metrics = array(
			__('User', 'better-analytics') => array(
				'ga:users' => __('Users', 'better-analytics'),
				'ga:newUsers' => __('New Users', 'better-analytics'),
				'ga:sessionsPerUser' => __('Sessions Per User', 'better-analytics'),
			),
			__('Session', 'better-analytics') => array(
				'ga:sessions' => __('Sessions', 'better-analytics'),
				'ga:bounces' => __('Bounces', 'better-analytics'),
				'ga:bounceRate' => __('Bounce Rate', 'better-analytics'),
				'ga:hits' => __('Hits', 'better-analytics'),
			),
			__('Traffic Sources', 'better-analytics') => array(
				'ga:organicSearches' => __('Organic Search', 'better-analytics'),
			),
			__('AdWords', 'better-analytics') => array(
				'ga:impressions' => __('Impressions', 'better-analytics'),
				'ga:adClicks' => __('Clicks', 'better-analytics'),
				'ga:adCost' => __('Cost', 'better-analytics'),
				'ga:CPM' => __('CPM', 'better-analytics'),
				'ga:CPC' => __('CPC', 'better-analytics'),
				'ga:CTR' => __('CTR', 'better-analytics'),
				'ga:RPC' => __('RPC', 'better-analytics'),
			),
			__('Goal Conversions', 'better-analytics') => array(
				'ga:goalStartsAll' => __('Goal Starts All', 'better-analytics'),
				'ga:goalCompletionsAll' => __('Goal Completions All', 'better-analytics'),
				'ga:goalValueAll' => __('Goal Value All', 'better-analytics'),
				'ga:goalValuePerSession' => __('Goal Value Per Session', 'better-analytics'),
				'ga:goalConversionRateAll' => __('Goal Conversion Rate All', 'better-analytics'),
				'ga:goalAbandonsAll' => __('Goal Abandons All', 'better-analytics'),
				'ga:goalAbandonRateAll' => __('Goal Abandon Rate All', 'better-analytics'),
			),
			__('Social Activities', 'better-analytics') => array(
				'ga:socialActivities' => __('Social Activities', 'better-analytics'),
			),
			__('Page Tracking', 'better-analytics') => array(
				'ga:pageviews' => __('Page Views', 'better-analytics'),
				'ga:pageviewsPerSession' => __('Page Views Per Session', 'better-analytics'),
				'ga:exits' => __('Exits', 'better-analytics'),
			),
			__('Internal Search', 'better-analytics') => array(
				'ga:searchUniques' => __('Unique Searches', 'better-analytics'),
			),
			__('Site Speed', 'better-analytics') => array(
				'ga:pageLoadTime' => __('Page Load Time', 'better-analytics'),
				'ga:domainLookupTime' => __('Domain Lookup Time', 'better-analytics'),
				'ga:serverConnectionTime' => __('Server Connection Time', 'better-analytics'),
				'ga:serverResponseTime' => __('Server Response Time', 'better-analytics'),
				'ga:domContentLoadedTime' => __('DOM Content Loaded Time', 'better-analytics'),
			),
			__('Event Tracking', 'better-analytics') => array(
				'ga:totalEvents' => __('Total Events', 'better-analytics'),
				'ga:uniqueEvents' => __('Unique Events', 'better-analytics'),
				'ga:totalEvents|ga:eventCategory==User;ga:eventAction==Registration' => __('User Registrations', 'better-analytics'),
				'ga:totalEvents|ga:eventCategory==Content;ga:eventAction==Comment' => __('Comments Created', 'better-analytics'),
				'ga:totalEvents|ga:eventCategory==YouTube Video;ga:eventAction==Playing' => __('YouTube Video Played', 'better-analytics'),
				'ga:totalEvents|ga:eventCategory==YouTube Video;ga:eventAction==Paused' => __('YouTube Video Paused', 'better-analytics'),
				'ga:totalEvents|ga:eventCategory==YouTube Video;ga:eventAction==Ended' => __('YouTube Video Plays To End', 'better-analytics'),
				'ga:totalEvents|ga:eventCategory==Email;ga:eventAction==Send' => __('Emails Sent', 'better-analytics'),
				'ga:totalEvents|ga:eventCategory==Email;ga:eventAction==Open' => __('Emails Opened', 'better-analytics'),
				'ga:totalEvents|ga:eventCategory==Link;ga:eventAction==Click' => __('External Links Clicked', 'better-analytics'),
				'ga:totalEvents|ga:eventCategory==Link;ga:eventAction==Download' => __('File Downloads', 'better-analytics'),
				'ga:totalEvents|ga:eventCategory==Image;ga:eventAction==Not Loaded' => __('Images Not Loading', 'better-analytics'),
				'ga:totalEvents|ga:eventCategory==Error;ga:eventAction==Page Not Found' => __('Page Not Found (404)', 'better-analytics'),
				'ga:totalEvents|ga:eventCategory==AJAX Request;ga:eventAction==Trigger' => __('AJAX Requests', 'better-analytics'),
				'ga:totalEvents|ga:eventCategory==Error;ga:eventAction==JavaScript' => __('JavaScript Errors', 'better-analytics'),
				'ga:totalEvents|ga:eventCategory==Error;ga:eventAction==AJAX' => __('AJAX Errors', 'better-analytics'),
				'ga:totalEvents|ga:eventCategory==Error;ga:eventAction==Browser Console' => __('Browser Console Errors', 'better-analytics'),
				'ga:totalEvents|ga:eventCategory==Error;ga:eventAction=~^YouTube' => __('YouTube Errors', 'better-analytics'),
				'ga:totalEvents|ga:eventCategory==Advertisement;ga:eventAction==Click' => __('Advertisement Clicked', 'better-analytics'),

			),
			__('Ecommerce', 'better-analytics') => array(
				'ga:transactions' => __('Transactions', 'better-analytics'),
				'ga:transactionRevenue' => __('Transaction Revenue', 'better-analytics'),
				'ga:revenuePerTransaction' => __('Revenue Per Transaction', 'better-analytics'),
			),
			__('Social Interactions', 'better-analytics') => array(
				'ga:socialInteractions' => __('Social Interactions', 'better-analytics'),
				'ga:uniqueSocialInteractions' => __('Unique Social Interactions', 'better-analytics'),
			),
			__('DoubleClick Campaign Manager', 'better-analytics') => array(
				'ga:dcmCPC' => __('CPC', 'better-analytics'),
				'ga:dcmCTR' => __('CTR', 'better-analytics'),
				'ga:dcmClicks' => __('Clicks', 'better-analytics'),
				'ga:dcmCost' => __('Cost', 'better-analytics'),
				'ga:dcmImpressions' => __('Impressions', 'better-analytics'),
				'ga:dcmRPC' => __('RPC', 'better-analytics'),
			),
			__('AdSense', 'better-analytics') => array(
				'ga:adsenseRevenue' => __('Revenue', 'better-analytics'),
				'ga:adsenseAdUnitsViewed' => __('Ad Units Views', 'better-analytics'),
				'ga:adsenseAdsViewed' => __('Views', 'better-analytics'),
				'ga:adsenseAdsClicks' => __('Clicks', 'better-analytics'),
				'ga:adsensePageImpressions' => __('Page Impressions', 'better-analytics'),
				'ga:adsenseCTR' => __('CTR', 'better-analytics'),
				'ga:adsenseECPM' => __('ECPM', 'better-analytics'),
				'ga:adsenseExits' => __('Exits', 'better-analytics'),
				'ga:adsenseViewableImpressionPercent' => __('Viewable Impressions', 'better-analytics'),
				'ga:adsenseCoverage' => __('Coverage', 'better-analytics'),
			)
		);

		return apply_filters('better_analytics_metrics', $metrics);
	}

	public static function getMetricNameByKey($metric)
	{
		$return = false;
		$metrics = self::getMetrics();
		foreach ($metrics as $groupKey => $group)
		{
			if (!empty($group[$metric]))
			{
				$return = $group[$metric];
				break;
			}
		}
		return $return;
	}


	public static function getSegments()
	{
		$segments = array(
			__('Default Segments', 'better-analytics') => array(
				'' => __('Everything', 'better-analytics'),
				'gaid::-1' => __('All Sessions', 'better-analytics'),
				'gaid::-2' => __('New Users', 'better-analytics'),
				'gaid::-3' => __('Returning Users', 'better-analytics'),
				'gaid::-4' => __('Paid Traffic', 'better-analytics'),
				'gaid::-5' => __('Organic Traffic', 'better-analytics'),
				'gaid::-6' => __('Search Traffic', 'better-analytics'),
				'gaid::-7' => __('Direct Traffic', 'better-analytics'),
				'gaid::-8' => __('Referral Traffic', 'better-analytics'),
				'gaid::-9' => __('Sessions with Conversions', 'better-analytics'),
				'gaid::-10' => __('Sessions with Transactions', 'better-analytics'),
				'gaid::-11' => __('Mobile and Tablet Traffic', 'better-analytics'),
				'gaid::-12' => __('Non-bounce Visits', 'better-analytics'),
				'gaid::-13' => __('Tablet Traffic', 'better-analytics'),
				'gaid::-14' => __('Mobile Traffic', 'better-analytics'),
				'gaid::-15' => __('Tablet and Desktop Traffic', 'better-analytics'),
				'gaid::-16' => __('Android Traffic', 'better-analytics'),
				'gaid::-17' => __('iOS Traffic', 'better-analytics'),
				'gaid::-18' => __('Other Traffic (Neither iOS nor Android)', 'better-analytics'),
				'gaid::-19' => __('Bounced Sessions', 'better-analytics'),
				'gaid::-100' => __('Single Session Users', 'better-analytics'),
				'gaid::-101' => __('Multi-session Users', 'better-analytics'),
				'gaid::-102' => __('Converters', 'better-analytics'),
				'gaid::-103' => __('Non-Converters', 'better-analytics'),
				'gaid::-104' => __('Made a Purchase', 'better-analytics'),
				'gaid::-105' => __('Performed Site Search', 'better-analytics'),
				'dynamic::ga:userGender==male' => __('Male Users', 'better-analytics'),
				'dynamic::ga:userGender==female' => __('Female Users', 'better-analytics'),

			)
		);

		return apply_filters('better_analytics_segments', $segments);

	}

	public static function getDimensions()
	{
		$dimensions = array(
			__('User', 'better-analytics') => array(
				'userType' => __('User Type', 'better-analytics'),
				'sessionCount' => __('Session Count', 'better-analytics'),
				'daysSinceLastSession' => __('Days Since Last Visit', 'better-analytics'),
			),
			__('Session', 'better-analytics') => array(
				'sessionDurationBucket' => __('Session Duration', 'better-analytics'),
			),
			__('Traffic Sources', 'better-analytics') => array(
				'campaign' => __('Campaign', 'better-analytics'),
				'source' => __('Source', 'better-analytics'),
				'medium' => __('Medium', 'better-analytics'),
				'keyword' => __('Keyword', 'better-analytics'),
				'socialNetwork' => __('Social Network', 'better-analytics'),
				'searchNotProvided' => __('Search Keywords Provided', 'better-analytics'),
				'oraganicSearchMarketshare' => __('Organic Search Marketshare', 'better-analytics'),
			),
			__('Platform', 'better-analytics') => array(
				'browser' => __('Browser', 'better-analytics'),
				'operatingSystem' => __('Operating System', 'better-analytics'),
				'operatingSystemVersion' => __('Operating System Version', 'better-analytics'),
				'mobileDeviceBranding' => __('Mobile Device Branding', 'better-analytics'),
				'mobileDeviceModel' => __('Mobile Device Model', 'better-analytics'),
				'mobileInputSelector' => __('Mobile Input Selector', 'better-analytics'),
				'mobileDeviceInfo' => __('Mobile Device Info', 'better-analytics'),
				'mobileDeviceMarketingName' => __('Mobile Device Marketing Name', 'better-analytics'),
				'deviceCategory' => __('Device Category', 'better-analytics'),
			),
			__('Geo / Network', 'better-analytics') => array(
				'continent' => __('Continent', 'better-analytics'),
				'subContinent' => __('Sub-Continent', 'better-analytics'),
				'country' => __('Country', 'better-analytics'),
				'region' => __('Region', 'better-analytics'),
				'city' => __('City', 'better-analytics'),
			),
			__('System', 'better-analytics') => array(
				'flashVersion' => __('Flash Version', 'better-analytics'),
				'javaEnabled' => __('Java Enabled', 'better-analytics'),
				'language' => __('Language', 'better-analytics'),
				'screenColors' => __('Screen Colors', 'better-analytics'),
				'screenResolution' => __('Screen Resolution', 'better-analytics'),
			),
			__('Page Tracking', 'better-analytics') => array(
				'hostname' => __('Hostname', 'better-analytics'),
			),
			__('Internal Search', 'better-analytics') => array(
				'searchUsed' => __('Search Used', 'better-analytics'),
				'searchKeyword' => __('Search Keyword', 'better-analytics'),
			),
			__('Event Tracking', 'better-analytics') => array(
				'eventCategory' => __('Event Category', 'better-analytics'),
				'eventAction' => __('Event Action', 'better-analytics'),
			),
			__('Social Interactions', 'better-analytics') => array(
				'socialInteractionNetwork' => __('Social Network', 'better-analytics'),
				'socialInteractionAction' => __('Social Action', 'better-analytics'),
				'socialInteractionNetworkAction' => __('Social Network Action', 'better-analytics'),
				'socialEngagementType' => __('Social Engagement Type', 'better-analytics'),
			),
			__('Custom Variables', 'better-analytics') => array(
				'dimension1' => __('Custom Dimension 1', 'better-analytics'),
				'dimension2' => __('Custom Dimension 2', 'better-analytics'),
				'dimension3' => __('Custom Dimension 3', 'better-analytics'),
				'dimension4' => __('Custom Dimension 4', 'better-analytics'),
				'dimension5' => __('Custom Dimension 5', 'better-analytics'),
				'customVarValue1' => __('Custom Variable Value 1', 'better-analytics'),
				'customVarValue2' => __('Custom Variable Value 2', 'better-analytics'),
				'customVarValue3' => __('Custom Variable Value 3', 'better-analytics'),
				'customVarValue4' => __('Custom Variable Value 4', 'better-analytics'),
				'customVarValue5' => __('Custom Variable Value 5', 'better-analytics'),
			),
			__('Audience', 'better-analytics') => array(
				'userAgeBracket' => __('Age Bracket', 'better-analytics'),
				'userGender' => __('Gender', 'better-analytics'),
				'interestAffinityCategory' => __('Interest Affinity', 'better-analytics'),
				'interestInMarketCategory' => __('Interest In Market', 'better-analytics'),
				'interestOtherCategory' => __('Interest Other', 'better-analytics'),
			),
		);

		return apply_filters('better_analytics_dimensions', $dimensions);

	}

	public static function parseDimensions($dimensions)
	{
		$output = array();
		if (@$dimensions['items'])
		{
			foreach($dimensions['items'] as $dimension)
			{
				$output[$dimension['index']] = $dimension['name'] . ($dimension['scope'] != 'HIT' ? ' ' . sprintf(esc_html__('(scope set to %s, should be HIT)', 'better-analytics'), $dimension['scope']) : '');
			}
		}
		return $output;
	}

	public static function parseAccounts($accounts, $editableOnly = false)
	{
		$output = array();
		if (@$accounts['items'])
		{
			foreach($accounts['items'] as $account)
			{
				if (!$editableOnly || array_search('EDIT', $account['permissions']['effective']) !== false)
				{
					$output[$account['id']] = $account['name'];
				}
			}
		}
		return $output;
	}

	public static function getSiteAccountId()
	{
		$betterAnalyticsOptions = get_option('better_analytics');
		$profiles = DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->getProfiles();

		$accountId = null;
		if (!empty($profiles['items']))
		{
			foreach($profiles['items'] as $profile)
			{
				if ($profile['webPropertyId'] == $betterAnalyticsOptions['property_id'])
				{
					$accountId = $profile['accountId'];
				}
			}
		}
		return $accountId;
	}

	public static function filterGoalsByProfile($goals, $webPropertyId, $profileId, &$totals)
	{
		$output = array();
		if (@$goals['items'])
		{
			foreach($goals['items'] as $goal)
			{
				if ($goal['webPropertyId'] == $webPropertyId && $goal['profileId'] == $profileId)
				{
					$output[$goal['id']] = $goal;
					@$totals['all']++;
					if ($goal['active'])
					{
						@$totals['active']++;
					}
					else
					{
						@$totals['inactive']++;
					}
				}
			}
		}
		return $output;
	}

	public static function getGoalByGoalId($goals, $webPropertyId, $profileId, $goalId)
	{
		$goal = null;
		if (!empty($goals['items']))
		{
			foreach($goals['items'] as $goal)
			{
				if ($goal['webPropertyId'] == $webPropertyId && $goal['profileId'] == $profileId && $goal['id'] == $goalId)
				{
					break;
				}
			}
		}
		return $goal;
	}


	public static function getExperimentTotals($experiments)
	{
		$totals = array();
		if (@$experiments['items'])
		{
			foreach($experiments['items'] as $experiment)
			{
				@$totals['all']++;

				if ($experiment['status'] == 'DRAFT')
				{
					@$totals['draft']++;
				}
				elseif ($experiment['status'] == 'RUNNING')
				{
					@$totals['running']++;
				}
				elseif ($experiment['status'] == 'ENDED')
				{
					@$totals['ended']++;
				}
				elseif ($experiment['status'] == 'READY_TO_RUN')
				{
					@$totals['ready']++;
				}
			}
		}
		return $totals;
	}



}