<?php

class DigitalPointBetterAnalytics_Helper_Analytics extends DigitalPointBetterAnalytics_Helper_Analytics_Advanced
{
	protected $_urlInfo = array();

	protected function _getOption($option)
	{
		switch ($option)
		{
			case 'userDimensionIndex':
				$betterAnalyticsOptions = get_option('better_analytics');
				return @$betterAnalyticsOptions['dimension']['user'];

			case 'internalV':
				$betterAnalyticsOptions = get_transient('ba_int');
				return @$betterAnalyticsOptions['v'];

			default:
				return false;
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

	protected function _execHandlerAction()
	{
		wp_remote_post($this->_urlInfo['url'],
			array(
				'body' => $this->_urlInfo['params']
			)
		);
	}

}