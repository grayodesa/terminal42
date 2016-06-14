<?php

abstract class DigitalPointBetterAnalytics_Helper_Analytics_Abstract
{
	protected static $_instance;

	protected static $_analyticsEndPoint = 'https://www.google-analytics.com/collect';

	protected static $_options = null;

	protected $_currentHandle = null;


	/**
	 * Protected constructor. Use {@link getInstance()} instead.
	 */
	protected function __construct()
	{
	}

	/**
	 * Need to put this method in the abstract class unfortunately because PHP 5.2 doesn't support late static binding
	 */
	protected static final function _resolveClass()
	{
		if(class_exists('XenForo_Application'))
		{
			$class = XenForo_Application::resolveDynamicClass('DigitalPointBetterAnalytics_Helper_Analytics');
			self::$_instance = new $class();
		}
		else
		{
			self::$_instance = new DigitalPointBetterAnalytics_Helper_Analytics();
		}
	}

	protected function _postResolveClass()
	{

	}

	/**
	 * Gets the single instance of class.
	 *
	 * @return DigitalPointBetterAnalytics_Helper_Analytics
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

	abstract protected function _initHttp($url);

	abstract protected function _setParamsAction($params);

	abstract protected function _execHandlerAction();


	public function transaction($trackingId, $clientId, $userId, $ipAddress, $transactionId, $transactionRevenue, $currencyCode = false, array $items, $affiliation = false)
	{
		$this->prepareClientId($clientId);

		$this->_getHandler();

		$params = array(
			'tid' => $trackingId,
			'cid' => $clientId,
			'uid' => $userId,
			'uip' => $ipAddress,
			't' => 'transaction',
			'ni' => 1,
			'ti' => $transactionId,

			'tr' => $transactionRevenue
		);

		$dimensionIndex = $this->_getOption('userDimensionIndex');
		if ($dimensionIndex)
		{
			$params['cd' . $dimensionIndex] = $userId;
		}

		if ($affiliation)
		{
			$params['ta'] = $affiliation;
		}
		if ($currencyCode)
		{
			$params['cu'] = $currencyCode;
		}

		$this->_setParams($params);
		$this->_execHandler();

		if ($items)
		{
			foreach ($items as $item)
			{
				$this->_getHandler();

				$params = array(
					'tid' => $trackingId,
					'cid' => $clientId,
					'uid' => $userId,
					'uip' => $ipAddress,
					't' => 'item',
					'ni' => 1,
					'ti' => $transactionId,

					'in' => $item['name'],
					'ip' => $item['price'],
					'iq' => $item['quantity'],
					'ic' => $item['code'],
					'iv' => $item['category'],

					'pa' => $item['action']
				);

				if ($dimensionIndex)
				{
					$params['cd' . $dimensionIndex] = $userId;
				}

				if ($affiliation)
				{
					$params['ta'] = $affiliation;
				}
				if ($currencyCode)
				{
					$params['cu'] = $currencyCode;
				}

				$this->_setParams($params);
				$this->_execHandler();
			}
		}
	}

	public function social($trackingId, $clientId, $userId, $ipAddress, $socialNetwork, $socialAction, $socialActionTarget)
	{
		$this->prepareClientId($clientId);

		$this->_getHandler();

		$params = array(
			'tid' => $trackingId,
			'cid' => $clientId,
			'uid' => $userId,
			'uip' => $ipAddress,
			't' => 'social',

			'sn' => $socialNetwork,
			'sa' => $socialAction,
			'st' => $socialActionTarget
		);

		$dimensionIndex = $this->_getOption('userDimensionIndex');
		if ($dimensionIndex)
		{
			$params['cd' . $dimensionIndex] = $userId;
		}

		$this->_setParams($params);
		$this->_execHandler();
	}

	public function event($trackingId, $clientId, $userId, $ipAddress, $category, $action, $label = null, $campaignMedium = null, $nonInteractive = false)
	{
		$this->prepareClientId($clientId);

		$this->_getHandler();

		$params = array(
			'tid' => $trackingId,
			'cid' => $clientId,
			't' => 'event',

			'ec' => $category,
			'ea' => $action
		);

		if($label)
		{
			$params['el'] = $label;
		}

		if($ipAddress)
		{
			$params['uip'] = $ipAddress;
		}

		if($nonInteractive)
		{
			$params['ni'] = 1;
		}

		if($campaignMedium)
		{
			$params['cm'] = $campaignMedium;
		}

		$userId = intval($userId);
		if ($userId)
		{
			$params['uid'] = $userId;

			$dimensionIndex = $this->_getOption('userDimensionIndex');
			if ($dimensionIndex)
			{
				$params['cd' . $dimensionIndex] = $userId;
			}
		}

		$this->_setParams($params);
		$this->_execHandler();
	}

	public function pageview($trackingId, $clientId, $userId, $params)
	{
		$this->prepareClientId($clientId);

		$this->_getHandler();

		$params = array(
				'tid' => $trackingId,
				'cid' => $clientId,
				't' => 'pageview',
			) + $params;

		$userId = intval($userId);
		if ($userId)
		{
			$params['uid'] = $userId;

			$dimensionIndex = $this->_getOption('userDimensionIndex');
			if ($dimensionIndex)
			{
				$params['cd' . $dimensionIndex] = $userId;
			}
		}

		$this->_setParams($params);
		$this->_execHandler();
	}

	public function prepareClientId(&$clientId)
	{
		if (substr($clientId, 0, 6) == 'GA1.2.')
		{
			$clientId = substr($clientId, 6);
		}

		if (!$clientId)
		{
			$clientId = uniqid('', true);
		}
	}

	protected function _canUseCurlMulti()
	{
		return false;
	}


	protected function _getHandler()
	{
		$this->_currentHandle = $this->_initHttp(self::$_analyticsEndPoint);
	}

	protected function _setParams(array $params)
	{
		$params['v'] = 1;
		$params['ds'] = 'server side';

		$this->_setParamsAction($params);
	}

	protected function _execHandler()
	{
		$this->_execHandlerAction();
	}

}