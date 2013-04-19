<?php

class Log extends Fuel\Core\Log{

	public static function _init(){
		\Config::load('log', true);
	}


	public static function write($level, $msg, $method = null){

		$config = \Config::get('log',array());


		
		
		if (empty($config['driver']))
		{
			throw new \FuelException('No log driver given or no default log driver set.');
		}

		$class = '\\Log_'.ucfirst($config['driver']);
		
		try{
			return $class::write($level,$msg,$method);
		} catch (FuelException $e) {
		}
	}
}
