<?php

class DigitalPointBetterAnalytics_ControllerAdmin_Analytics
{

	public function actionCharts()
	{
		// sanitize
		$_POST['dimension'] = preg_replace('#[^a-z0-9\:]#i' ,'', @$_POST['dimension']);
		$_POST['metric'] = preg_replace('#[^a-z0-9\:]#i' ,'', @$_POST['metric']);
		$_POST['days'] = absint(@$_POST['days']);
		$_POST['realtime'] = absint(@$_POST['realtime']);

		$dimension = substr($_POST['dimension'], 2);
		$type = substr($_POST['dimension'], 0, 1);

		$betterAnalyticsPick = get_option('ba_dashboard_pick');

		if ($_POST['dimension'] != @$betterAnalyticsPick['dimension'] || $_POST['metric'] != @$betterAnalyticsPick['metric'] || $_POST['days'] != @$betterAnalyticsPick['days'] || $_POST['realtime'] != @$betterAnalyticsPick['realtime'])
		{
			update_option('ba_dashboard_pick', array('dimension' => $_POST['dimension'], 'metric' => $_POST['metric'], 'days' => $_POST['days'], 'realtime' => $_POST['realtime']));
		}

		if (!empty($_POST['realtime']))
		{
			if (!$realTime = get_transient('ba_realtime'))
			{
				$realTime = DigitalPointBetterAnalytics_Model_Widget::getRealtimeData(55, true);
			}

			$realTimeOutput = array('users' => 0);

			if (!empty($realTime))
			{
				foreach ($realTime as $key => $value)
				{
					if (is_array($value))
					{
						$realTimeOutput[$key][] = array(ucwords(strtolower(($key == 'keywords' ? __('Organic Search Keywords', 'better-analytics') : ($key == 'referral_path' ? __('Referring URL', 'better-analytics') : ($key == 'page_path' ? __('Current Page', 'better-analytics') : ($key == 'medium' ? __('Medium', 'better-analytics') : ($key == 'devices' ? __('Devices', 'better-analytics') : ''))))))), __('Visitors', 'better-analytics'));
						foreach ($value as $name => $amount)
						{
							$realTimeOutput[$key][] = array($name, intval($amount));
						}
					}
					else
					{
						$realTimeOutput[$key] = intval($value);
					}
				}
			}

			wp_send_json(array(
				'realtime_data' => $realTimeOutput,
				'title' => __('Real-time', 'better-analytics')
			));

		}
		else
		{
			$validDimensions = $this->getDimensionsForCharts();
			$validMetrics = $this->getMetricsForCharts();

			$chartData = array_merge(array(array($validDimensions[$_POST['dimension']], $validMetrics[$_POST['metric']])), DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->getChart(1, intval($_POST['days']), $_POST['metric'], $dimension));
			wp_send_json(array(
				'chart_data' => $chartData,
				'title' => ($dimension == 'ga:date' ? $validMetrics[$_POST['metric']] : $validDimensions[$_POST['dimension']]),
				'type' => $type
			));
		}

	}

	public function actionHeatmaps()
	{
		if (!$this->_assertLinkedAccount())
		{
			return;
		}

		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			// sanitize
			$regEx = '#[^a-z0-9\:\-\=\|\ \;]#i';
			$_POST['metric'] = preg_replace($regEx ,'', @$_POST['metric']);
			$_POST['segment'] = preg_replace($regEx ,'', @$_POST['segment']);
			$_POST['end'] = absint(@$_POST['end']);
			$_POST['weeks'] = absint(@$_POST['weeks']);
			if (empty($_POST['page_path']))
			{
				$_POST['page_path'] = false;
			}



			if (!DigitalPointBetterAnalytics_Helper_Api::check())
			{
				if (array_search($_POST['metric'], array('ga:users', 'ga:sessions', 'ga:hits', 'ga:organicSearches')) === false)
				{

					wp_send_json(array('error' => sprintf(__('Not all metrics are available for unlicensed copies of the Better Analytics plugin.%1$s%2$sYou can license a copy over here.%3$s%1$sIf this is a valid license, make sure the purchaser of the add-on has verified ownership of this domain %4$sover here%3$s.', 'better-analytics'),
						'<br /><br />',
						'<a href="' . esc_url(BETTER_ANALYTICS_PRO_PRODUCT_URL . '#utm_source=admin_reports_ajax&utm_medium=wordpress&utm_campaign=plugin') . '" target="_blank">',
						'</a>',
						'<a href="' . esc_url('https://forums.digitalpoint.com/marketplace/domain-verification#utm_source=admin_reports_ajax&utm_medium=wordpress&utm_campaign=plugin') . '" target="_blank">'
					)));
				}
			}
			if ($_POST['page_path'])
			{
				$_POST['metric'] .= (strpos($_POST['metric'], '|') ? ';' : '|') . 'ga:pagePath==' . $_POST['page_path'];
			}

			$heatmapData = DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->getWeeklyHeatmap($_POST['end'], $_POST['weeks'], $_POST['metric'], $_POST['segment']);

			wp_send_json(array('heatmap_data' => $heatmapData));
		}

		$heatmapData = DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->getWeeklyHeatmap(1, 4, 'ga:sessions' . (!empty($_REQUEST['page_path']) ? '|ga:pagePath==' . $_REQUEST['page_path'] : ''));
		$_hourMap = array();
		for($i = 0; $i < 24; $i++)
		{
			$_hourMap[$i] = date('g A', $i * 3600);
		}

		$this->_view('reports/heatmaps', array(
			'heatmap_data' => $heatmapData,
			'metrics' => DigitalPointBetterAnalytics_Model_Reporting::getMetrics(),
			'segments' => DigitalPointBetterAnalytics_Model_Reporting::getSegments(),
			'hour_map' => $_hourMap
		));
	}


	public function actionAreacharts()
	{
		if (!$this->_assertLinkedAccount())
		{
			return;
		}

		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			// sanitize
			$_POST['dimension'] = preg_replace('#[^a-z0-9]#i' ,'', @$_POST['dimension']);
			$_POST['scope'] = preg_replace('#[^a-z0-9]#i' ,'', @$_POST['scope']);
			$_POST['time_frame'] = absint(@$_POST['time_frame']);
			$_POST['minimum'] = absint(@$_POST['minimum']);
			$_POST['chart_type'] = preg_replace('#[^a-z]#i' ,'', @$_POST['chart_type']);
			if (!$_POST['chart_type'])
			{
				$_POST['chart_type'] = false;
			}
			if (empty($_POST['page_path']))
			{
				$_POST['page_path'] = false;
			}


			if (!$_POST['time_frame'])
			{
				wp_send_json(array('error' => __('Invalid number of days.', 'better-analytics')));
			}
			elseif (!$_POST['dimension'])
			{
				wp_send_json(array('error' => __('Invalid dimension.', 'better-analytics')));
			}
			elseif (!DigitalPointBetterAnalytics_Helper_Api::check())
			{
				if (array_search($_POST['dimension'], array('browser', 'operatingSystem', 'source', 'medium')) === false)
				{
					wp_send_json(array('error' => sprintf(__('Not all dimensions are available for unlicensed copies of the Better Analytics plugin.%1$s%2$sYou can license a copy over here.%3$s%1$sIf this is a valid license, make sure the purchaser of the add-on has verified ownership of this domain %4$sover here%3$s.', 'better-analytics'),
						'<br /><br />',
						'<a href="' . esc_url(BETTER_ANALYTICS_PRO_PRODUCT_URL . '#utm_source=admin_reports_ajax&utm_medium=wordpress&utm_campaign=plugin') . '" target="_blank">',
						'</a>',
						'<a href="' . esc_url('https://forums.digitalpoint.com/marketplace/domain-verification#utm_source=admin_reports_ajax&utm_medium=wordpress&utm_campaign=plugin') . '" target="_blank">'
					)));
				}
			}


			$dateStart = date("Y-m-d", max(1104580800, time() - (86400 * ($_POST['time_frame'] + 1))));
			$dateEnd = date("Y-m-d", time() - (86400));

			switch ($_POST['scope'])
			{
				case 'month':
					$scope = 'yearMonth';
					break;
				case 'year':
					$scope = 'year';
					break;
				default:
					$scope = 'date';
			}
			$originalDimension = $_POST['dimension'];

			if ($_POST['dimension'] == 'searchNotProvided')
			{
				$extraFilter = ';ga:medium==organic;ga:keyword==(not provided)';
				$_POST['dimension'] = 'keyword';
			}
			elseif ($_POST['dimension'] == 'oraganicSearchMarketshare')
			{
				$extraFilter = ';ga:medium==organic';
				$_POST['dimension'] = 'source,ga:medium';

			}
			elseif ($_POST['dimension'] == 'mobileOperatingSystem')
			{
				$extraFilter = ';ga:isMobile==Yes';
				$_POST['dimension'] = 'operatingSystem';
			}
			else
			{
				$extraFilter = '';
			}

			if ($_POST['page_path'])
			{
				$extraFilter .= ';ga:pagePath==' . $_POST['page_path'];
			}

			$cacheKey = DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->getData(
				$dateStart,
				$dateEnd,
				'ga:sessions', // metric
				'ga:' . $scope . ',ga:' . $_POST['dimension'], // dimensions
				'ga:' . $scope . ',-ga:sessions', // sort
				'ga:sessions>' . $_POST['minimum'] . $extraFilter // filters
			);

			if ($originalDimension == 'searchNotProvided')
			{
				$cacheKey2 = DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->getData(
					$dateStart,
					$dateEnd,
					'ga:sessions', // metric
					'ga:' . $scope, // dimensions
					'ga:' . $scope . ',-ga:sessions', // sort
					'ga:sessions>' . $_POST['minimum'] . ';ga:medium==organic'// filters
				);
			}

			$results = DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->getResults($cacheKey);

			if ($originalDimension == 'searchNotProvided')
			{
				$resultTotal = DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->getResults($cacheKey2);

				$totalNotProvided = $consolidated = array();

				if (!empty($results['rows']))
				{
					foreach ($results['rows'] as $row)
					{
						if ($row[1] == '(not provided)')
						{
							$totalNotProvided[$row[0]] = $row[2];
						}
					}
					foreach ($resultTotal['rows'] as $row)
					{
						$consolidated[] = array_merge(array($row[0]), array(__('Keywords Provided', 'better-analytics'), $row[1] - @$totalNotProvided[$row[0]]));
						$consolidated[] = array_merge(array($row[0]), array(__('Keywords Not Provided', 'better-analytics'), @$totalNotProvided[$row[0]] + 0));
					}

					$results['rows'] = $consolidated;
				}



			}
			elseif ($originalDimension == 'oraganicSearchMarketshare')
			{

				if (!empty($results['rows']))
				{
					foreach ($results['rows'] as &$row)
					{
						$row[2] = $row[3];
						$row[1] = (strlen($row[1]) > 3 ? ucwords($row[1]) : strtoupper($row[1]));
						unset($row[3]);
					}
				}
			}

			$resultsOrdered = $resultsOutput = $allLabels = array();
			if (!empty($results['rows']))
			{
				foreach ($results['rows'] as $row)
				{
					$allLabels[$row[1]] = null;
					$resultsOrdered[$row[0]][$row[1]] = intval($row[2]);
				}

				ksort($allLabels, SORT_STRING);

				foreach ($resultsOrdered as &$labels)
				{
					$labels = array_merge($labels, array_diff_key($allLabels, $labels));
					ksort($labels, SORT_STRING);
				}

				$resultsOutput = array(array_merge(array('Date'), array_keys($allLabels)));
				foreach ($resultsOutput[0] as &$item)
				{
					$item = (string)$item;
				}

				foreach ($resultsOrdered as $date => $values)
				{
					$resultsOutput[] = array_merge(array($date), array_values($values));
				}

				$title = '';
				$dimensions = DigitalPointBetterAnalytics_Model_Reporting::getDimensions();
				foreach ($dimensions as $group)
				{
					foreach ($group as $key => $value)
					{
						if ($originalDimension == $key)
						{
							$title = $value;
							break;
						}
					}
				}

				wp_send_json(array(
					'chart_data' => $resultsOutput,
					'chart_type' => $_POST['chart_type'],
					'title' =>sprintf('%1$s %2$s', __('History for', 'better-analytics'), esc_html__($title))
				));
			}

			wp_send_json(array(
				'error' => __('No data for the criteria given', 'better-analytics')
			));


		}



		$this->_view('reports/area_charts', array(
			'dimensions' => DigitalPointBetterAnalytics_Model_Reporting::getDimensions(),
		));
	}

	public function actionMonitor()
	{
		if (!$this->_assertLinkedAccount())
		{
			return;
		}

		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			// sanitize
			$_POST['days'] = absint(@$_POST['days']);
			if (empty($_POST['page_path']))
			{
				$_POST['page_path'] = false;
			}

			if (!$_POST['days'])
			{
				wp_send_json(array('error' => __('Invalid number of days.', 'better-analytics')));
			}

			$cacheKey = DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->getData(
				$_POST['days'] . 'daysAgo',
				'yesterday',
				'ga:totalEvents', // metric
				'ga:eventCategory,ga:eventAction,ga:eventLabel', // dimensions
				'-ga:totalEvents,ga:eventCategory,ga:eventAction,ga:eventLabel', // sort
				(!empty($_POST['page_path']) ? 'ga:pagePath==' . $_POST['page_path'] . ';' : '') . 'ga:eventCategory==Error,ga:eventCategory==Image' // filters
			);

			$results = DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->getResults($cacheKey);

			$chartOutput = array(array(
				__('Events', 'better-analytics'),
				__('Category', 'better-analytics'),
				__('Type', 'better-analytics'),
				__('Detail', 'better-analytics')
			));
			if (@$results['rows'])
			{
				foreach ($results['rows'] as $row)
				{
					$chartOutput[] = array(absint($row[3]), $row[0], $row[1], $row[2]);
				}

				wp_send_json(array(
					'chart_data' => $chartOutput
				));
			}

			wp_send_json(array(
				'error' => __('No data for the criteria given', 'better-analytics')
			));
		}

		$this->_view('reports/events', array('type' => 'monitor'));
	}

	public function actionEvents()
	{
		if (!$this->_assertLinkedAccount())
		{
			return;
		}

		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			// sanitize
			$_POST['days'] = absint(@$_POST['days']);
			if (empty($_POST['page_path']))
			{
				$_POST['page_path'] = false;
			}

			if (!$_POST['days'])
			{
				wp_send_json(array('error' => __('Invalid number of days.', 'better-analytics')));
			}

			$cacheKey = DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->getData(
				$_POST['days'] . 'daysAgo',
				'yesterday',
				'ga:totalEvents', // metric
				'ga:eventCategory,ga:eventAction,ga:eventLabel', // dimensions
				'-ga:totalEvents,ga:eventCategory,ga:eventAction,ga:eventLabel', // sort
				(!empty($_POST['page_path']) ? 'ga:pagePath==' . $_POST['page_path'] . ';' : '') . 'ga:eventCategory!=Error;ga:eventCategory!=Image' // filters
			);

			$results = DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->getResults($cacheKey);

			$chartOutput = array(array(
				__('Events', 'better-analytics'),
				__('Category', 'better-analytics'),
				__('Type', 'better-analytics'),
				__('Detail', 'better-analytics')
			));
			if (@$results['rows'])
			{
				foreach ($results['rows'] as $row)
				{
					$chartOutput[] = array(absint($row[3]), $row[0], $row[1], $row[2]);
				}

				wp_send_json(array(
					'chart_data' => $chartOutput
				));
			}


			wp_send_json(array(
				'error' => __('No data for the criteria given', 'better-analytics')
			));
		}

		$this->_view('reports/events', array('type' => 'events'));
	}

	public function actionGoals()
	{
		if (!$this->_assertLinkedAccount())
		{
			return;
		}

		if (isset($_REQUEST['action2']) && $_REQUEST['action2'] != -1)
		{
			$_REQUEST['action'] = $_REQUEST['action2'];
		}

		$betterAnalyticsOptions = get_option('better_analytics');
		$reportingClass = DigitalPointBetterAnalytics_Helper_Reporting::getInstance();

		if (@$_REQUEST['action'] == 'activate' || @$_REQUEST['action'] == 'deactivate')
		{
			if ($goalId = absint(@$_REQUEST['id']))
			{
				check_admin_referer($_REQUEST['action'] . '-goal');

				if ($profile = $reportingClass->getProfileByProfileId($betterAnalyticsOptions['api']['profile']))
				{
					$goal = $reportingClass->patchGoal($profile['accountId'], $profile['webPropertyId'], $profile['id'], $goalId, array(
						'active' => ($_REQUEST['action'] == 'activate' ? true : false),
					));

					$reportingClass->deleteGoalCache();
				}
			}
		}
		elseif (@$_REQUEST['action'] == 'activate-selected' || @$_REQUEST['action'] == 'deactivate-selected')
		{
			if (!empty($_REQUEST['checked']) && is_array($_REQUEST['checked']))
			{
				$checkIds = array();
				foreach ($_REQUEST['checked'] as $check)
				{
					if ($id = absint($check))
					{
						$checkIds[] = $id;
					}
				}

				if ($checkIds)
				{
					check_admin_referer('bulk-goals');

					if ($profile = $reportingClass->getProfileByProfileId($betterAnalyticsOptions['api']['profile']))
					{
						foreach ($checkIds as $id)
						{
							$goal = $reportingClass->patchGoal($profile['accountId'], $profile['webPropertyId'], $profile['id'], $id, array(
								'active' => ($_REQUEST['action'] == 'activate-selected' ? true : false),
							));
						}

						$reportingClass->deleteGoalCache();
					}
				}
			}
		}

		global $totals;
		$totals = array();

		$goals = $reportingClass->getGoals();
		$goals = DigitalPointBetterAnalytics_Model_Reporting::filterGoalsByProfile($goals, @$betterAnalyticsOptions['property_id'], @$betterAnalyticsOptions['api']['profile'], $totals);


		$this->_view('goals', array('goals' => $goals));

	}


	public function actionExperiments()
	{
		if (!$this->_assertLinkedAccount())
		{
			return;
		}

		if (isset($_REQUEST['action2']) && $_REQUEST['action2'] != -1)
		{
			$_REQUEST['action'] = $_REQUEST['action2'];
		}

		$betterAnalyticsOptions = get_option('better_analytics');
		$reportingClass = DigitalPointBetterAnalytics_Helper_Reporting::getInstance();

		if (@$_REQUEST['action'] == 'start-selected' || @$_REQUEST['action'] == 'end-selected' || @$_REQUEST['action'] == 'delete-selected')
		{
			if (!empty($_REQUEST['checked']) && is_array($_REQUEST['checked']))
			{
				$checkIds = array();
				foreach ($_REQUEST['checked'] as $check)
				{
					if ($id = sanitize_text_field($check))
					{
						$checkIds[] = $id;
					}
				}

				if ($checkIds)
				{
					check_admin_referer('bulk-experiments');

					if ($profile = $reportingClass->getProfileByProfileId($betterAnalyticsOptions['api']['profile']))
					{
						foreach ($checkIds as $id)
						{
							if ($_REQUEST['action'] == 'delete-selected')
							{
								$reportingClass->deleteExperiment($profile['accountId'], $profile['webPropertyId'], $profile['id'], $id);

							}
							else
							{
								$experiment = $reportingClass->patchExperiment($profile['accountId'], $profile['webPropertyId'], $profile['id'], $id, array(
									'status' => ($_REQUEST['action'] == 'start-selected' ? 'RUNNING' : 'ENDED'),
								));
							}
							$reportingClass->deleteExperimentCache($profile['accountId'], $profile['webPropertyId'], $profile['id'], $id);
						}

						$reportingClass->deleteExperimentCache($profile['accountId'], $profile['webPropertyId'], $profile['id']);
					}
				}
			}
		}

		$this->_view('experiments');
	}




	protected function _assertLinkedAccount()
	{
		$betterAnalyticsOptions = get_option('better_analytics');

		if (!DigitalPointBetterAnalytics_Base_Public::getInstance()->getTokens() || !@$betterAnalyticsOptions['api']['profile'])
		{
			$this->_responseException(sprintf('%1$s <a href="%2$s">%3$s</a>',
				__('No Linked Google Analytics Account.', 'better-analytics'),
				menu_page_url('better-analytics', false) . '#top#api',
				__('You can link one in the Better Analytics API settings.', 'better-analytics')
			));

			return false;
		}
		return true;
	}


	protected function _responseException($error)
	{
		echo '<div class="error"><p>' . $error . '</p></div>';
	}

	protected function _responseError($error)
	{
		echo '<div class="error"><p>' . $error . '</p></div>';
	}

	static public function getDimensionsForCharts()
	{
		$dimensions = array();

		$betterAnalyticsOptions = get_option('better_analytics');

		$dimensions['l:ga:date'] = esc_html__('Date', 'better-analytics');

		if (!empty($betterAnalyticsOptions['dimension']['category']))
		{
			$dimensions['p:ga:dimension' . $betterAnalyticsOptions['dimension']['category']] = esc_html__('Categories', 'better-analytics');
		}
		if (!empty($betterAnalyticsOptions['dimension']['author']))
		{
			$dimensions['p:ga:dimension' . $betterAnalyticsOptions['dimension']['author']] = esc_html__('Authors', 'better-analytics');
		}
		if (!empty($betterAnalyticsOptions['dimension']['tag']))
		{
			$dimensions['p:ga:dimension' . $betterAnalyticsOptions['dimension']['tag']] = esc_html__('Tags', 'better-analytics');
		}
		if (!empty($betterAnalyticsOptions['dimension']['year']))
		{
			$dimensions['p:ga:dimension' . $betterAnalyticsOptions['dimension']['year']] = esc_html__('Publication Year', 'better-analytics');
		}
		if (!empty($betterAnalyticsOptions['dimension']['role']))
		{
			$dimensions['p:ga:dimension' . $betterAnalyticsOptions['dimension']['role']] = esc_html__('User Role', 'better-analytics');
		}
		$dimensions['p:ga:browser'] = esc_html__('Browser', 'better-analytics');
		$dimensions['p:ga:operatingSystem'] = esc_html__('Operating System', 'better-analytics');
		$dimensions['p:ga:deviceCategory'] = esc_html__('Device Category', 'better-analytics');
		$dimensions['p:ga:source'] = esc_html__('Source', 'better-analytics');
		$dimensions['p:ga:fullReferrer'] = esc_html__('Referrer', 'better-analytics');

		$dimensions['p:ga:medium'] = esc_html__('Medium', 'better-analytics');
		$dimensions['g:ga:country'] = esc_html__('Country', 'better-analytics');
		//$dimensions['g:ga:region'] = esc_html__('Region', 'better-analytics');
		//$dimensions['g:ga:city'] = esc_html__('City', 'better-analytics');


		/*
		if (!empty($betterAnalyticsOptions['dimension']['user']))
		{
			$dimensions['p:ga:dimension' . $betterAnalyticsOptions['dimension']['user']] = esc_html__('Top Registered Users', 'better-analytics');
		}
		*/
		return $dimensions;
	}

	static public function getMetricsForCharts()
	{
		$metrics = array(
			'ga:pageviews' => esc_html__('Page Views', 'better-analytics'),
			'ga:sessions' => esc_html__('Sessions', 'better-analytics'),
			'ga:users' => esc_html__('Users', 'better-analytics'),
			'ga:avgSessionDuration' => esc_html__('Session Length', 'better-analytics'),
			'ga:organicSearches' => esc_html__('Organic Search', 'better-analytics')
		);

		return $metrics;
	}

	protected function _view($name, array $args = array())
	{
		DigitalPointBetterAnalytics_Base_Admin::getInstance()->view($name, $args);
	}
}