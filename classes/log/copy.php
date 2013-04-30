<?php

namespace Fluentd\Log;

class Copy extends \Fluentd\Log{

	public static function write($level, $msg, $method = null){
		$log_config = \Config::get('log');

		$return = false;
		if(array_key_exists('copy',$log_config['drivers'])){
			foreach( $log_config['drivers']['copy'] as $driver){
				$class = __NAMESPACE__ .'\\'. ucfirst($driver);

				try{
					$return = $class::write($level,$msg,$method);
				} catch (FuelException $e) {
					//
				}
			}
		}

		return $return;
	}
}
