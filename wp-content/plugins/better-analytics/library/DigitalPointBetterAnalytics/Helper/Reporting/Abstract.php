<?php

abstract class DigitalPointBetterAnalytics_Helper_Reporting_Abstract
{
	protected static $_instance;

	protected static $_oAuthEndpoint = 'https://accounts.google.com/o/oauth2/';
	protected static $_dataEndpoint = 'https://www.googleapis.com/analytics/v3/data/ga';
	protected static $_realtimeEndpoint = 'https://www.googleapis.com/analytics/v3/data/realtime';

	protected static $_accountsEndpoint = 'https://www.googleapis.com/analytics/v3/management/accounts';

	protected static $_webPropertiesEndpoint = '/%s/webproperties';
	protected static $_profilesEndpoint = '/%s/profiles';
	protected static $_dimensionsEndpoint = '/%s/customDimensions';
	protected static $_goalsEndpoint = '/%s/goals';
	protected static $_experimentsEndpoint = '/%s/experiments';

	protected static $_curlHandles = array();

	protected static $_cachedResults = array();

	protected $_currentHandle = null;
	protected $_url = null;

	protected $_overrideTokens = null;

	/**
	 * Protected constructor. Use {@link getInstance()} instead.
	 */
	protected function __construct()
	{
	}

	/**
	 * Need to put this method in the abstract class unfortunately because PHP 5.2 doesn't support late static binding
	 */
	protected static function _resolveClass()
	{
		if(class_exists('XenForo_Application'))
		{
			$class = XenForo_Application::resolveDynamicClass('DigitalPointBetterAnalytics_Helper_Reporting');
			self::$_instance = new $class();
		}
		else
		{
			self::$_instance = new DigitalPointBetterAnalytics_Helper_Reporting();
		}
	}

	protected function _postResolveClass()
	{

	}


	/**
	 * Gets the single instance of class.
	 *
	 * @return DigitalPointBetterAnalytics_Helper_Reporting
	 */
	public static final function getInstance()
	{
		if (!self::$_instance)
		{
			self::_resolveClass();
			self::$_instance->_postResolveClass();
		}

		return self::$_instance;
	}

	abstract protected function _getOption($type);

	abstract protected function _saveTokens($tokens);

	abstract protected function _deleteTokens();

	abstract protected function _throwException();

	abstract protected function _showException($message);

	abstract public function getCreateAccountMessage();

	abstract protected function _getAdminAuthUrl();

	abstract protected function _initHttp($url);

	abstract protected function _setParamsAction($params);

	abstract protected function _execHandlerAction($action = 'POST');

	abstract protected function _cacheLoad($cacheKey);

	abstract protected function _cacheSave($cacheKey, $data, $minutes);

	abstract protected function _cacheDelete($cacheKey);



	public function getAuthenticationUrl($state = null, $includeEditScope = false, $accessType = 'offline')
	{
		return self::$_oAuthEndpoint . 'auth?redirect_uri=' . urlencode($this->_getAdminAuthUrl()) . ($state ? '&state=' . urlencode($state) : '') . '&response_type=code&client_id=' . urlencode($this->_getOption('apiClientId')) . '&scope=https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fanalytics' . ($includeEditScope ? '+https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fanalytics.edit' : '') . '&approval_prompt=force&access_type=' . urlencode($accessType);
	}

	public function exchangeCodeForToken($code)
	{
		$this->_cacheDelete('analytics_profiles');

		$this->_initHttp(self::$_oAuthEndpoint . 'token');
		$this->_setParamsAction(array(
				'code' => $code,
				'redirect_uri' => $this->_getAdminAuthUrl(),
				'client_id' => $this->_getOption('apiClientId'),
				//	'scope' => '',
				'client_secret' => $this->_getOption('apiClientSecret'),
				'grant_type' => 'authorization_code'
			));
		return json_decode($this->_execHandlerAction());
	}

	public function overrideTokens($tokens)
	{
		$this->_overrideTokens = $tokens;
	}

	public function checkAccessToken($throwException = true)
	{
		if ($this->_overrideTokens)
		{
			return $this->_overrideTokens;
		}

		$tokens = $this->_getOption('tokens');
		if (empty($tokens->refresh_token))
		{
			if ($throwException)
			{
				$this->_throwException();
			}
			else
			{
				return false;
			}
		}

		if ($tokens->expires_at <= time())
		{
			// token has expired... exchange for new one.
			$this->_initHttp(self::$_oAuthEndpoint . 'token');
			$this->_setParamsAction(array(
				'client_id' => $this->_getOption('apiClientId'),
				'client_secret' => $this->_getOption('apiClientSecret'),
				'grant_type' => 'refresh_token',
				'refresh_token' => $tokens->refresh_token
			));
			$response = json_decode($this->_execHandlerAction());

			if (!empty($response->error) && $response->error == 'unauthorized_client')
			{
				$this->_deleteTokens();
				return;
			}
			else
			{
				$tokens->access_token = $response->access_token;
				$tokens->token_type = $response->token_type;
				$tokens->expires_at = time() + $response->expires_in - 100;

				$this->_saveTokens($tokens);
				return $tokens;

			}

		}
		return $tokens;

	}


	public function getProfiles($accountId = '~all', $profileId = '~all')
	{
		$cacheKey = 'ba_prof_' . md5($accountId . '-' . $profileId);

		$profiles = $this->_cacheLoad($cacheKey);

		if (!$profiles)
		{
			$fromCache = false;

			if ($tokens = $this->checkAccessToken())
			{
				if ($profileId)
				{
					$url = sprintf(self::$_accountsEndpoint . self::$_webPropertiesEndpoint . self::$_profilesEndpoint, $accountId, $profileId);
				}
				else
				{
					$url = sprintf(self::$_accountsEndpoint . self::$_webPropertiesEndpoint, $accountId);
				}

				$this->_initHttp($url);
				$this->_setParamsAction(array('access_token' => $tokens->access_token));

				$response = $this->_execHandlerAction('GET');

				$profiles = json_decode($response, true);

				if ($this->_hasError($profiles))
				{
					$fromCache = true;
				}
			}
		}
		else
		{
			$fromCache = true;
		}

		if (!$fromCache)
		{
			$this->_cacheSave($cacheKey, $profiles, 5);
		}
		return $profiles;
	}

	public function deleteProfileCache($accountId = '~all', $profileId = '~all')
	{
		$cacheKey = 'ba_prof_' . md5($accountId . '-' . $profileId);
		$this->_cacheDelete($cacheKey);
	}


	public function getAccounts()
	{
		$cacheKey = 'ba_accts';

		$accounts = $this->_cacheLoad($cacheKey);

		if (!$accounts)
		{
			$fromCache = false;

			if ($tokens = $this->checkAccessToken())
			{
				$url = self::$_accountsEndpoint;

				$this->_initHttp($url);
				$this->_setParamsAction(array('access_token' => $tokens->access_token));

				$response = $this->_execHandlerAction('GET');

				$accounts = json_decode($response, true);

				if ($this->_hasError($accounts))
				{
					$fromCache = true;
				}
			}
		}
		else
		{
			$fromCache = true;
		}

		if (!$fromCache)
		{
			$this->_cacheSave($cacheKey, $accounts, 15);
		}
		return $accounts;
	}

	public function insertWebProperty($accountId, $fields = array())
	{
		if ($tokens = $this->checkAccessToken())
		{
			$fields = json_encode($fields);

			$url = sprintf(self::$_accountsEndpoint . self::$_webPropertiesEndpoint, $accountId);

			$this->_initHttp($url);
			$this->_setParamsAction(array(
				'access_token' => $tokens->access_token,
				'body' => $fields
			));

			$results = $this->_execHandlerAction('INSERT');

			$this->_hasError($results);

			return json_decode($results, true);
		}
	}

	public function patchWebProperty($accountId, $webPropertyId, $fields = array())
	{
		if ($tokens = $this->checkAccessToken())
		{
			$fields = json_encode($fields);

			$url = sprintf(self::$_accountsEndpoint . self::$_webPropertiesEndpoint . '/%s', $accountId, $webPropertyId);

			$this->_initHttp($url);
			$this->_setParamsAction(array(
				'access_token' => $tokens->access_token,
				'body' => $fields
			));

			$results = $this->_execHandlerAction('PATCH');

			$this->_hasError($results);

			return json_decode($results, true);
		}
	}

	public function insertProfile($accountId, $webPropertyId, $fields = array())
	{
		if ($tokens = $this->checkAccessToken())
		{
			$fields = json_encode($fields);

			$url = sprintf(self::$_accountsEndpoint . self::$_webPropertiesEndpoint . self::$_profilesEndpoint, $accountId, $webPropertyId);

			$this->_initHttp($url);
			$this->_setParamsAction(array(
				'access_token' => $tokens->access_token,
				'body' => $fields
			));

			$results = $this->_execHandlerAction('INSERT');

			$this->_hasError($results);

			return json_decode($results, true);
		}
	}

	public function patchProfile($accountId, $webPropertyId, $profileId, $fields = array())
	{
		if ($tokens = $this->checkAccessToken())
		{
			$fields = json_encode($fields);
			$url = sprintf(self::$_accountsEndpoint . self::$_webPropertiesEndpoint . self::$_profilesEndpoint . '/%s', $accountId, $webPropertyId, $profileId);

			$this->_initHttp($url);
			$this->_setParamsAction(array(
				'access_token' => $tokens->access_token,
				'body' => $fields
			));

			$results = $this->_execHandlerAction('PATCH');

			$this->_hasError($results);

			return json_decode($results, true);
		}
	}

	public function insertCustomDimension($accountId, $webPropertyId, $fields = array())
	{
		if ($tokens = $this->checkAccessToken())
		{
			$fields = json_encode($fields);

			$url = sprintf(self::$_accountsEndpoint . self::$_webPropertiesEndpoint . self::$_dimensionsEndpoint, $accountId, $webPropertyId);

			$this->_initHttp($url);
			$this->_setParamsAction(array(
				'access_token' => $tokens->access_token,
				'body' => $fields
			));

			$results = $this->_execHandlerAction('INSERT');

			$this->_hasError($results);

			return json_decode($results, true);
		}
	}

	public function getDimensions($accountId = '~all', $propertyId = '~all')
	{
		$cacheKey = 'ba_dim_' . md5($accountId . '-' . $propertyId);

		$dimensions = $this->_cacheLoad($cacheKey);

		if (!$dimensions)
		{
			$fromCache = false;

			if ($tokens = $this->checkAccessToken())
			{
				if ($propertyId)
				{
					$url = sprintf(self::$_accountsEndpoint . self::$_webPropertiesEndpoint . self::$_dimensionsEndpoint, $accountId, $propertyId);
				}
				else
				{
					$url = sprintf(self::$_accountsEndpoint . self::$_webPropertiesEndpoint, $accountId);
				}

				$this->_initHttp($url);
				$this->_setParamsAction(array('access_token' => $tokens->access_token));

				$response = $this->_execHandlerAction('GET');

				$this->_hasError($response);

				$dimensions = json_decode($response, true);
			}
		}
		else
		{
			$fromCache = true;
		}

		if (!$fromCache)
		{
			$this->_cacheSave($cacheKey, $dimensions, 1);
		}
		return $dimensions;
	}

	public function deleteDimensionCache($accountId = '~all', $propertyId = '~all')
	{
		$cacheKey = 'ba_dim_' . md5($accountId . '-' . $propertyId);
		$this->_cacheDelete($cacheKey);
	}


	public function getDimensionsByPropertyId($accountId, $propertyId, $names)
	{
		$dimensions = $this->getDimensions($accountId, $propertyId);

		$foundDimensions = array();
		if(!empty($dimensions['items']))
		{
			foreach ($dimensions['items'] as $dimension)
			{
				$key = array_search($dimension['name'], $names);

				if ($key !== false && $dimension['scope'] == 'HIT')
				{
					$foundDimensions[$dimension['name']] = $dimension;
				}
			}
		}

		return $foundDimensions;
	}


	public function getProfileByPropertyId($propertyId)
	{
		$profiles = $this->getProfiles();

		$foundProfile = null;
		if(!empty($profiles['items']))
		{
			foreach ($profiles['items'] as $profile)
			{
				if ($profile['webPropertyId'] == $propertyId)
				{
					$foundProfile = $profile;
					break;
				}
			}
		}

		return $foundProfile;
	}

	public function getProfileByProfileId($profileId)
	{
		$profiles = $this->getProfiles();

		$foundProfile = null;
		if(!empty($profiles['items']))
		{
			foreach ($profiles['items'] as $profile)
			{
				if ($profile['id'] == $profileId)
				{
					$foundProfile = $profile;
					break;
				}
			}
		}

		return $foundProfile;
	}


	// this is a little weird... getting profiles with ~all doesn't return industryVertical, but this does.  Bug on their end?
	public function getPropertyByPropertyId($accountId, $propertyId)
	{
		$profiles = $this->getProfiles($accountId, null);

		$foundProfile = null;
		if(!empty($profiles['items']))
		{
			foreach ($profiles['items'] as $profile)
			{
				if ($profile['id'] == $propertyId)
				{
					$foundProfile = $profile;
					break;
				}
			}
		}

		return $foundProfile;
	}

	public function getGoals($accountId = '~all', $webPropertyId = '~all', $profileId = '~all', $goalId = null)
	{
		return $this->_makeApiCall(
			sprintf(self::$_accountsEndpoint . self::$_webPropertiesEndpoint . self::$_profilesEndpoint . self::$_goalsEndpoint . ($goalId ? '/%s' : ''), $accountId, $webPropertyId, $profileId, $goalId),
			'ba_goals_' . md5($accountId . '-' . $webPropertyId . '-' . $profileId . '-' . $goalId),
			'GET',
			60
		);
	}

	public function insertGoal($accountId, $webPropertyId, $profileId, $fields = array())
	{
		return $this->_makeApiCall(
			sprintf(self::$_accountsEndpoint . self::$_webPropertiesEndpoint . self::$_profilesEndpoint . self::$_goalsEndpoint, $accountId, $webPropertyId, $profileId),
			null,
			'INSERT',
			0,
			$fields
		);
	}

	public function patchGoal($accountId, $webPropertyId, $profileId, $goalId, $fields = array())
	{
		return $this->_makeApiCall(
			sprintf(self::$_accountsEndpoint . self::$_webPropertiesEndpoint . self::$_profilesEndpoint . self::$_goalsEndpoint . ($goalId ? '/%s' : ''), $accountId, $webPropertyId, $profileId, $goalId),
			null,
			'PATCH',
			0,
			$fields
		);
	}

	public function deleteGoalCache($accountId = '~all', $webPropertyId = '~all', $profileId = '~all', $goalId = null)
	{
		$cacheKey = 'ba_goals_' . md5($accountId . '-' . $webPropertyId . '-' . $profileId . '-' . $goalId);
		$this->_cacheDelete($cacheKey);
	}


	public function getExperiments($accountId, $webPropertyId, $profileId, $experimentId = null)
	{
		return $this->_makeApiCall(
			sprintf(self::$_accountsEndpoint . self::$_webPropertiesEndpoint . self::$_profilesEndpoint . self::$_experimentsEndpoint . ($experimentId ? '/%s' : ''), $accountId, $webPropertyId, $profileId, $experimentId),
			'ba_exp_' . md5($accountId . '-' . $webPropertyId . '-' . $profileId . '-' . $experimentId),
			'GET',
			60
		);
	}

	public function insertExperiment($accountId, $webPropertyId, $profileId, $fields = array())
	{
		return $this->_makeApiCall(
			sprintf(self::$_accountsEndpoint . self::$_webPropertiesEndpoint . self::$_profilesEndpoint . self::$_experimentsEndpoint, $accountId, $webPropertyId, $profileId),
			null,
			'INSERT',
			0,
			$fields
		);
	}

	public function deleteExperiment($accountId, $webPropertyId, $profileId, $experimentId)
	{
		return $this->_makeApiCall(
			sprintf(self::$_accountsEndpoint . self::$_webPropertiesEndpoint . self::$_profilesEndpoint . self::$_experimentsEndpoint . '/%s', $accountId, $webPropertyId, $profileId, $experimentId),
			null,
			'DELETE',
			0
		);
	}


	public function patchExperiment($accountId, $webPropertyId, $profileId, $experimentId, $fields = array())
	{
		return $this->_makeApiCall(
			sprintf(self::$_accountsEndpoint . self::$_webPropertiesEndpoint . self::$_profilesEndpoint . self::$_experimentsEndpoint . ($experimentId ? '/%s' : ''), $accountId, $webPropertyId, $profileId, $experimentId),
			null,
			'PATCH',
			0,
			$fields
		);
	}

	public function deleteExperimentCache($accountId, $webPropertyId, $profileId, $experimentId = null)
	{
		$cacheKey = 'ba_exp_' . md5($accountId . '-' . $webPropertyId . '-' . $profileId . '-' . $experimentId);
		$this->_cacheDelete($cacheKey);
	}



	protected function _makeApiCall($endpoint, $cacheKey = null, $method = 'GET', $cacheMinutes = 60, $fields = array())
	{
		if (empty($cacheKey) || !$results = $this->_cacheLoad($cacheKey))
		{
			$fromCache = false;

			if ($tokens = $this->checkAccessToken())
			{
				$this->_initHttp($endpoint);

				$params = array('access_token' => $tokens->access_token);
				if ($fields)
				{
					$params['body'] = json_encode($fields);
				}

				$this->_setParamsAction($params);

				$response = $this->_execHandlerAction($method);

				$results = json_decode($response, true);

				if ($this->_hasError($results))
				{
					$fromCache = true;
				}
			}
		}
		else
		{
			$fromCache = true;
		}

		if ($cacheKey && !$fromCache)
		{
			$this->_cacheSave($cacheKey, $results, $cacheMinutes);
		}
		return $results;

	}




	public function getWeeklyHeatmap($endDaysAgo, $weeks, $metric, $segment = null)
	{
		$filters = null;

		if (strpos($metric, '|'))
		{
			$split = explode('|', $metric);
			$metric = $split[0];
			$filters = $split[1];
		}

		$cacheKey = $this->getData(($endDaysAgo + ($weeks * 7) - 1) . 'daysAgo', $endDaysAgo . 'daysAgo', $metric, 'ga:hour,ga:dayOfWeek', 'ga:hour,ga:dayOfWeek', $filters, $segment);
		$data = $this->getResults($cacheKey);

		$preparedData = array();

		for ($i = 0; $i < 24; $i++)
		{
			$preparedData[$i] = array_fill(0, 7, 0);
		}

		if (!empty($data['rows']))
		{
			foreach ($data['rows'] as &$row)
			{
				$preparedData[intval($row[0])][intval($row[1])] = intval($row[2]);
			}
		}

		return $preparedData;
	}


	public function getChart($endDaysAgo, $days, $metric, $dimension, $segment = null)
	{
		$filters = null;

		if (strpos($metric, '|'))
		{
			$split = explode('|', $metric);
			$metric = $split[0];
			$filters = $split[1];
		}

		$cacheKey = $this->getData(($endDaysAgo + $days - 1) . 'daysAgo', $endDaysAgo . 'daysAgo', $metric, $dimension, ($dimension == 'ga:date' ? $dimension : '-' . $metric), $filters, $segment);
		$data = $this->getResults($cacheKey);

		if ($dimension == 'ga:date')
		{
			return $data['rows'];
		}

		$chartData = $outputData = array();
		if (!empty($data['rows']))
		{
			foreach($data['rows'] as $row)
			{
				$split = explode(',', $row[0]);
				foreach ($split as $name)
				{
					$name = trim($name);
					@$chartData[$name] += $row[1];
				}
			}
		}

		arsort($chartData);
		if ($chartData)
		{
			foreach ($chartData as $name => $value)
			{
				$outputData[] = array((string)$name, $value);
			}
		}

		return $outputData;
	}



	public function getData($startDate, $endDate, $metrics, $dimensions = null, $sort = null, $filters = null, $segment = null, $samplingLevel = null, $maxResults = 10000, $output = 'json', $userIp = null)
	{
		$profile = $this->_getOption('apiProfile');
		$cacheKey = 'ba_data_' . md5($profile . ' ' . $startDate . ' ' . $endDate . ' ' . $metrics . ' ' . $dimensions . ' ' . $sort . ' ' . $filters . ' ' . $segment . ' ' . $samplingLevel . ' ' . $maxResults . ' ' . $output);

		if (!$data = $this->_cacheLoad($cacheKey))
		{
			$tokens = $this->checkAccessToken();

			$this->_getHandler(self::$_dataEndpoint);

			$params = array(
				'ids' => 'ga:' . $profile,
				'start-date' => $startDate,
				'end-date' => $endDate,
				'metrics' => $metrics,
				'max-results' => $maxResults,
				'output' => $output,
				'access_token' => $tokens->access_token
			);

			if (!empty($dimensions))
			{
				$params['dimensions'] = $dimensions;
			}

			if (!empty($sort))
			{
				$params['sort'] = $sort;
			}

			if (!empty($filters))
			{
				$params['filters'] = $filters;
			}

			if (!empty($segment))
			{
				$params['segment'] = $segment;
			}

			if (!empty($samplingLevel))
			{
				$params['samplingLevel'] = $samplingLevel;
			}

			if (!empty($userIp))
			{
				$params['userIp'] = $userIp;
			}
			elseif (!empty($_SERVER['REMOTE_ADDR']))
			{
				$params['userIp'] = $_SERVER['REMOTE_ADDR'];
			}

			$this->_setParams($params);
			$this->_execHandler($cacheKey);
		}

		return $cacheKey;
	}

	public function getRealtime($metrics, $dimensions = null, $sort = null, $filters = null, $maxResults = 10000)
	{
		$profile = $this->_getOption('apiProfile');
		$cacheKey = 'ba_rt_' . md5($profile . ' ' . $metrics . ' ' . $dimensions . ' ' . $sort . ' ' . $filters . ' ' . $maxResults);

		//if (!$data = self::_cacheLoad($cacheKey))
		//{
		$tokens = $this->checkAccessToken();

		$this->_getHandler(self::$_realtimeEndpoint);

		$params = array(
			'ids' => 'ga:' . $profile,
			'metrics' => $metrics,
			'max-results' => $maxResults,
			'access_token' => $tokens->access_token
		);

		if (!empty($dimensions))
		{
			$params['dimensions'] = $dimensions;
		}

		if (!empty($sort))
		{
			$params['sort'] = $sort;
		}

		if (!empty($filters))
		{
			$params['filters'] = $filters;
		}

		$this->_setParams($params);
		$this->_execHandler($cacheKey);
		//}

		return $cacheKey;
	}

	protected function _hasError($results, $deleteTokens = true)
	{
		if (!empty($results['error']['errors']))
		{
			$extraMessage = '';

			if ($this->checkApiErrorType($results, 'noCredentials'))
			{
				if ($deleteTokens)
				{
					$this->_deleteTokens();
				}
				if ($results['error']['code'] == 403)
				{
					$extraMessage = $this->getCreateAccountMessage();
				}
			}

			$this->_showException(@$results['error']['errors'][0]['domain'] . ' / ' . @$results['error']['errors'][0]['reason'] . ': ' . @$results['error']['errors'][0]['message'] . '  ' . @$results['error']['errors'][0]['extendedHelp'] . ($extraMessage ? '<br /><br />' . $extraMessage : ''));

			return true;
		}

		return false;
	}



	protected function _canUseCurlMulti()
	{
		return false;
	}


	protected function _getHandler($url)
	{
		$this->_currentHandle = $this->_initHttp($url);
	}

	protected function _setParams(array $params)
	{
		$params['v'] = 1;
		$params['ds'] = 'server side';

		$this->_setParamsAction($params);
	}


	protected function _execHandler($cacheKey)
	{
		$this->_currentHandle = $this->_execHandlerAction('GET');

		$this->_hasError($this->_currentHandle);

		self::$_curlHandles[$cacheKey] = $this->_currentHandle;
	}



	public function getResults($cacheKey)
	{
		$results = @json_decode(self::$_curlHandles[$cacheKey], true);

		$this->_cacheSave($cacheKey, $results, 60);

		if (!empty(self::$_cachedResults[$cacheKey]))
		{
			return self::$_cachedResults[$cacheKey];
		}
		else
		{
			return false;
		}
	}


	public function getIndustryVerticals()
	{
		return array(
			'UNSPECIFIED',
			'ARTS_AND_ENTERTAINMENT',
			'AUTOMOTIVE',
			'BEAUTY_AND_FITNESS',
			'BOOKS_AND_LITERATURE',
			'BUSINESS_AND_INDUSTRIAL_MARKETS',
			'COMPUTERS_AND_ELECTRONICS',
			'FINANCE',
			'FOOD_AND_DRINK',
			'GAMES',
			'HEALTHCARE',
			'HOBBIES_AND_LEISURE',
			'HOME_AND_GARDEN',
			'INTERNET_AND_TELECOM',
			'JOBS_AND_EDUCATION',
			'LAW_AND_GOVERNMENT',
			'NEWS',
			'ONLINE_COMMUNITIES',
			'OTHER',
			'PEOPLE_AND_SOCIETY',
			'PETS_AND_ANIMALS',
			'REAL_ESTATE',
			'REFERENCE',
			'SCIENCE',
			'SHOPPING',
			'SPORTS',
			'TRAVEL'
		);
	}

	protected function _getGoogleErrors()
	{
		// see:  https://developers.google.com/analytics/devguides/reporting/core/v3/coreErrors
		return array(
			'noCredentials' => array(
				'invalidCredentials' => 401,
				'insufficientPermissions' => 403,
			),
			'noQuota' => array(
				'dailyLimitExceeded' => 403,
				'usageLimits.userRateLimitExceededUnreg' => 403,
				'userRateLimitExceeded' => 403,
				'quotaExceeded' => 403,
			),
			'other' => array(
				'invalidParameter' => 400,
				'badRequest' => 400,
				'backendError' => 503,

			)
		);
	}

	public function checkApiErrorType($results, $type)
	{
		$errors = $this->_getGoogleErrors();

		if (!empty($results['error']))
		{
			if (isset($errors[$type][@$results['error']['errors'][0]['reason']]))
			{
				if ($errors[$type][@$results['error']['errors'][0]['reason']] == @$results['error']['code'])
				{
					return true;
				}
			}
		}

		return false;
	}

}