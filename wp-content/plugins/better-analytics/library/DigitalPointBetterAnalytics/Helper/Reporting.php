<?php

class DigitalPointBetterAnalytics_Helper_Reporting extends DigitalPointBetterAnalytics_Helper_Reporting_Advanced
{
	/**
	 * These credentials are Google API Project credentials, not credentials for a Google account.
	 *
	 * For reference, the credentials ARE supposed to be embedded in the source for "Installed Applications" (which is what a WordPress plugin is).
	 *
	 * Quote from: https://developers.google.com/identity/protocols/OAuth2#installed
	 *
	 * "The process results in a client ID and, in some cases, a client secret, which you embed in the source
	 * code of your application. (In this context, the client secret is obviously not treated as a secret.)"
	 *
	 * OAuth2 tokens (which are never stored or transmitted outside the individual WordPress install) are the magic sauce that allows API calls to be made.
	 **/

	protected $_credentials = array(
		'client_id' => '416831151869-fks5s6f1d9q3a1j6ua0158hqclde21ta.apps.googleusercontent.com',
		'client_secret' => 'zkhfn3qPn0y-Dg8ZXHBeHBR9',
		'auth_url' => 'https://api.digitalpoint.com/v1/better-analytics/link'
	);

	protected $_urlInfo = array();

	protected function _getOption($option)
	{
		switch ($option)
		{
			case 'apiClientId':
				$betterAnalyticsSiteOptions = get_site_option('better_analytics_site');
				$betterAnalyticsOptions = get_option('better_analytics');
				if (!empty($betterAnalyticsSiteOptions['api']['use_own']))
				{
					return @$betterAnalyticsSiteOptions['api']['client_id'];
				}
				elseif (!empty($betterAnalyticsOptions['api']['use_own']))
				{
					return @$betterAnalyticsOptions['api']['client_id'];
				}
				else
				{
					return $this->_credentials['client_id'];
				}
			case 'apiClientSecret':
				$betterAnalyticsSiteOptions = get_site_option('better_analytics_site');
				$betterAnalyticsOptions = get_option('better_analytics');
				if (!empty($betterAnalyticsSiteOptions['api']['use_own']))
				{
					return @$betterAnalyticsSiteOptions['api']['client_secret'];
				}
				elseif (!empty($betterAnalyticsOptions['api']['use_own']))
				{
					return @$betterAnalyticsOptions['api']['client_secret'];
				}
				else
				{
					return $this->_credentials['client_secret'];
				}
			case 'apiProfile':
				$betterAnalyticsOptions = get_option('better_analytics');
				return @$betterAnalyticsOptions['api']['profile'];

			case 'tokens':
				return @json_decode(DigitalPointBetterAnalytics_Base_Public::getInstance()->getTokens());

			case 'internalV':
				$betterAnalyticsOptions = get_transient('ba_int');
				return @$betterAnalyticsOptions['v'];

			default:
				return false;
		}
	}

	protected function _saveTokens($tokens)
	{
		DigitalPointBetterAnalytics_Base_Public::getInstance()->updateTokens($tokens);
	}

	protected function _deleteTokens()
	{
		if (!$this->_overrideTokens)
		{
			DigitalPointBetterAnalytics_Base_Public::getInstance()->deleteTokens();
		}
	}

	protected function _throwException()
	{
		$this->_cacheSave('ba_last_error', esc_html__('No API tokens to refresh.', 'better-analytics'), 0.15); // 9 seconds
		return;
	}

	protected function _showException($message)
	{
		$this->_cacheSave('ba_last_error', $message, 0.15); // 9 seconds
		error_log($message);
		return;
	}

	public function getCreateAccountMessage()
	{
		return sprintf(esc_html__('If you don\'t have a Google Analytics account, you can %1$screate one here%2$s.  "Create an account" is on the upper right of that page.', 'better-analytics'), '<a href="http://www.google.com/analytics/" target="_blank">', '</a>');
	}

	protected function _getAdminAuthUrl()
	{
		$betterAnalyticsOptions = get_option('better_analytics');
		if (@$betterAnalyticsOptions['api']['use_own'])
		{
			return menu_page_url('better-analytics_auth', false);
		}
		else
		{
			return $this->_credentials['auth_url'];
		}
	}


	protected function _initHttp($url)
	{
		$this->_urlInfo['url'] = $url;
	}

	protected function _setParamsAction($params)
	{
		$this->_urlInfo['params'] = $params;
	}

	protected function _execHandlerAction($action = 'POST')
	{
		if ($action == 'POST')
		{
			$response = wp_remote_post($this->_urlInfo['url'],
				array(
					'body' => $this->_urlInfo['params']
				)
			);
		}
		elseif($action == 'INSERT')
		{
			$accessToken = $this->_urlInfo['params']['access_token'];
			unset($this->_urlInfo['params']['access_token']);

			$response = wp_remote_request($this->_urlInfo['url'] . '?access_token=' . urlencode($accessToken),
				array(
					'method' => 'POST',
					'headers' => array('Content-Type' => 'application/json'),
					'body' => $this->_urlInfo['params']['body']
				)
			);
		}
		elseif($action == 'DELETE')
		{
			$accessToken = $this->_urlInfo['params']['access_token'];
			unset($this->_urlInfo['params']['access_token']);

			$response = wp_remote_request($this->_urlInfo['url'] . '?access_token=' . urlencode($accessToken),
				array(
					'method' => 'DELETE',
					'headers' => array('Content-Type' => 'application/json')
				)
			);
		}
		elseif($action == 'PATCH')
		{
			$accessToken = $this->_urlInfo['params']['access_token'];
			unset($this->_urlInfo['params']['access_token']);

			$response = wp_remote_request($this->_urlInfo['url'] . '?access_token=' . urlencode($accessToken),
				array(
					'method' => 'PATCH',
					'headers' => array('Content-Type' => 'application/json'),
					'body' => $this->_urlInfo['params']['body']
				)
			);
		}
		else
		{
			$response = wp_remote_get($this->_urlInfo['url'] . '?' . http_build_query($this->_urlInfo['params']));
		}

		if (is_wp_error($response))
		{
			$this->_showException($response->get_error_message());
			return false;
		}

		return $response['body'];
	}


	protected function _cacheLoad($cacheKey)
	{
		$result = get_transient($cacheKey);

		self::$_cachedResults[$cacheKey] = $result;

		return $result;
	}

	protected function _cacheSave($cacheKey, $data, $minutes)
	{
		if (!empty($data['id']) || !empty($data['totalResults']))
		{
			set_transient($cacheKey, $data, intval($minutes * 60));

			self::$_cachedResults[$cacheKey] = $data;
		}
		elseif (!empty($data['error']['message']))
		{
			set_transient('ba_last_error', $data['error']['message'], 10);
		}
		else
		{
			set_transient($cacheKey, $data, intval($minutes * 60));
		}
	}

	protected function _cacheDelete($cacheKey)
	{
		delete_transient($cacheKey);
	}

}