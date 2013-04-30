<?php

namespace Fluentd;

class Log extends \Fuel\Core\Log
{
	protected static $opensocial_user_id = '';
	
	public static function setOpensocialUserId($opensocial_user_id) {
		self::$opensocial_user_id = $opensocial_user_id;
	}

	public static function _init(){
		\Config::load('log', true);
	}

	public static function info($msg, $method = null)
	{
		return static::write(\Fuel::L_INFO, $msg, $method);
	}

	public static function write($level, $msg, $method = null){

		\Config::load('log', true);
		$config = \Config::get('log',array());
		
		if (empty($config['driver']))
		{
			throw new \FuelException('No log driver given or no default log driver set.');
		}

		$class = 'Fluentd\\Log\\'.ucfirst($config['driver']);
		
		try{
			return $class::write($level,$msg,$method);
		} catch (FuelException $e) {
		}
	}
}
