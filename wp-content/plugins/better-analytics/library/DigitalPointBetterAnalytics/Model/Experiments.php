<?php

class DigitalPointBetterAnalytics_Model_Experiments
{
	public static function getStatuses()
	{
		$output = array(
			'DRAFT' => esc_html__('Draft', 'better-analytics'),
			'READY_TO_RUN' => esc_html__('Ready to run', 'better-analytics'),
			'RUNNING' => esc_html__('Running', 'better-analytics'),
			'ENDED' => esc_html__('Ended', 'better-analytics'),
		);

		return $output;
	}

	public static function getTypes()
	{
		$types = array(
			'PAGE_TITLE' => esc_html__('Page Title', 'better-analytics'),
			'POST_TITLE' => esc_html__('Post Title', 'better-analytics'),
			'CSS' => esc_html__('CSS', 'better-analytics'),
			'THEME' => esc_html__('Theme', 'better-analytics'),
		);

		$types = apply_filters('better_analytics_experiment_types_label', $types);

		return $types;
	}


	public static function getObjectiveMetrics()
	{
		$output = array(
			esc_html__('Site Usage', 'better-analytics') => array(
				'ga:pageviews' => esc_html__('Page Views', 'better-analytics'),
				'ga:bounces' => esc_html__('Bounces', 'better-analytics'),
				'ga:sessionDuration' => esc_html__('Session Duration', 'better-analytics'),
			),
			esc_html__('AdSense', 'better-analytics') => array(
				'ga:adsenseAdsClicks' => esc_html__('AdSense Ads Clicked', 'better-analytics'),
				'ga:adsenseAdsViewed' => esc_html__('AdSense Impressions', 'better-analytics'),
				'ga:adsenseRevenue' => esc_html__('AdSense Revenue', 'better-analytics'),
			),
			esc_html__('Ecommerce', 'better-analytics') => array(
				'ga:transactionRevenue' => esc_html__('Ecommerce Revenue', 'better-analytics'),
				'ga:transactions' => esc_html__('Ecommerce Transactions', 'better-analytics'),
			),
			esc_html__('Goals', 'better-analytics') => array(
			)
		);

		if ($goals = DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->getGoals())
		{
			$betterAnalyticsOptions = get_option('better_analytics');
			$totals = array();

			if ($goals = DigitalPointBetterAnalytics_Model_Reporting::filterGoalsByProfile($goals, @$betterAnalyticsOptions['property_id'], @$betterAnalyticsOptions['api']['profile'], $totals))
			{
				$goalsKey = esc_html__('Goals', 'better-analytics');
				foreach ($goals as $goal)
				{
					if (!empty($goal['active']))
					{
						$output[$goalsKey]['ga:goal' . $goal['id'] . 'Completions'] = sprintf(esc_html__('Goal %1$u Completions (%2$s)', 'better-analytics'), $goal['id'], $goal['name']);
					}
				}
			}
		}

		return $output;
	}

	public static function getStatusNameByCode($statusCode)
	{
		$_types = self::getStatuses();
		return @$_types[$statusCode];
	}

	public static function getExperimentByExperimentId($accountId, $webPropertyId, $profileId, $experimentId)
	{
		$experiment = DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->getExperiments($accountId, $webPropertyId, $profileId, $experimentId);
		self::decodeExperimentData($experiment);
		return $experiment;
	}

	public static function decodeExperimentData(&$experiment)
	{
		$experiment['extraData'] = @json_decode(@$experiment['description'], true);
		$experiment['fromBetterAnalytics'] = is_array($experiment['extraData']) && !empty($experiment['extraData']['type']);

		if (is_array($experiment['variations']))
		{
			$highestWeight = 0;

			foreach ($experiment['variations'] as $key => $variation)
			{
				if (!empty($variation['weight']) && $variation['weight'] > $highestWeight)
				{
					$highestWeight = $variation['weight'];
					$experiment['variationWinning'] = $key;
				}

				if (!empty($variation['won']))
				{
					$experiment['variationWinner'] = $key;
					break;
				}
			}
		}
	}

	public static function getAllExperiments($accountId, $webPropertyId, $profileId)
	{
		$experiments = DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->getExperiments($accountId, $webPropertyId, $profileId);

		self::compileActiveExperiments($experiments);

		return $experiments;

	}

	public static function compileActiveExperiments($experiments)
	{
		$experimentsCompiled = array();

		if (!empty($experiments['totalResults']) && is_array($experiments['items']))
		{
			foreach ($experiments['items'] as &$experiment)
			{
				self::decodeExperimentData($experiment);

				if (!empty($experiment['fromBetterAnalytics']) && $experiment['status'] == 'RUNNING')
				{
					switch ($experiment['extraData']['type'])
					{
						case 'POST_TITLE':
							$postId = $experiment['extraData']['post_title']['post_id'];

							$experimentsCompiled['post_title'][$postId] = array('id' => $experiment['id'], 'coverage' => $experiment['trafficCoverage']);

							if ($experiment['variations'][0]['status'] == 'ACTIVE')
							{
								$experimentsCompiled['post_title'][$postId]['variations'][0] = array(
									'weight' => $experiment['variations'][0]['weight']
								);
							}

							foreach ($experiment['variations'] as $key => $variation)
							{
								if ($key > 0 && ($variation['status'] == 'ACTIVE'))
								{
									$experimentsCompiled['post_title'][$postId]['variations'][$key] = array(
										'weight' => $variation['weight'],
										'title' => $experiment['extraData']['post_title']['titles'][$key - 1]
									);
								}
							}
							break;

						case 'PAGE_TITLE':
							$postId = $experiment['extraData']['page_title']['post_id'];

							$experimentsCompiled['page_title'][$postId] = array('id' => $experiment['id'], 'coverage' => $experiment['trafficCoverage']);

							if ($experiment['variations'][0]['status'] == 'ACTIVE')
							{
								$experimentsCompiled['page_title'][$postId]['variations'][0] = array(
									'weight' => $experiment['variations'][0]['weight']
								);
							}

							foreach ($experiment['variations'] as $key => $variation)
							{
								if ($key > 0 && ($variation['status'] == 'ACTIVE'))
								{
									$experimentsCompiled['page_title'][$postId]['variations'][$key] = array(
										'weight' => $variation['weight'],
										'title' => $experiment['extraData']['page_title']['titles'][$key - 1]
									);
								}
							}
							break;

						case 'CSS':
							$experimentsCompiled['css'] = array('id' => $experiment['id'], 'coverage' => $experiment['trafficCoverage']);

							if ($experiment['variations'][0]['status'] == 'ACTIVE')
							{
								$experimentsCompiled['css']['variations'][0] = array(
									'weight' => $experiment['variations'][0]['weight']
								);
							}

							foreach ($experiment['variations'] as $key => $variation)
							{
								if ($key > 0 && ($variation['status'] == 'ACTIVE'))
								{
									$experimentsCompiled['css']['variations'][$key] = array(
										'weight' => $variation['weight'],
										'code' => $experiment['extraData']['css']['code'][$key - 1]
									);
								}
							}
							break;

						case 'THEME':
							$experimentsCompiled['theme'] = array('id' => $experiment['id'], 'coverage' => $experiment['trafficCoverage']);

							if ($experiment['variations'][0]['status'] == 'ACTIVE')
							{
								$experimentsCompiled['theme']['variations'][0] = array(
									'weight' => $experiment['variations'][0]['weight']
								);
							}

							foreach ($experiment['variations'] as $key => $variation)
							{
								if ($key > 0 && ($variation['status'] == 'ACTIVE'))
								{
									$experimentsCompiled['theme']['variations'][$key] = array(
										'weight' => $variation['weight'],
										'theme' => $experiment['extraData']['theme']['themes'][$key - 1]
									);
								}
							}

							break;

					}
				}
			}
		}

		set_transient('ba_exp_live', $experimentsCompiled, WEEK_IN_SECONDS);
	}

	public static function runExperiment($coverage)
	{
		return $coverage >= (mt_rand() / mt_getrandmax());
	}

	public static function pickVariation($variations)
	{
		$randomPick = mt_rand() / mt_getrandmax();
		$weightSum = 0;

		foreach ($variations as $variationId => $variation)
		{
			$weightSum += $variation['weight'];
			if ($randomPick <= $weightSum)
			{
				break;
			}
		}
		return $variationId;
	}

}