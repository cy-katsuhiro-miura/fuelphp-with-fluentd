<?php

//use Fluent\Logger\FluentLogger;

namespace Fluentd\Log;

class Td extends \Fuel\Core\Log{

	public static function _init(){

		\Fluent\Autoloader::register();
		
		parent::_init();
	}

	public static function write($level, $msg, $method = null){

		$host = empty($log_config['td']['host']) ? null : $log_config['td']['host'];
		$port = empty($log_config['td']['port']) ? null : $log_config['td']['port'];
		$options = empty($log_config['td']['options']) ? array() : $log_config['td']['options'];
		$packer = empty($log_config['td']['packer']) ? null : $log_config['td']['packer'];
		$database = empty($log_config['td']['database']) ? 'default' : $log_config['td']['database'];

		$log_threshold = \Config::get('log_threshold');
		$config = \Config::get('log',array());

		if( isset($config['drivers']['td']['log_threshold']))
		{
			$log_threshold = $config['drivers']['td']['log_threshold'];
		}
		if ($level > \Config::get('log_threshold'))
		{
			return false;
		}
		$levels = array(
			1  => 'Error',
			2  => 'Warning',
			3  => 'Debug',
			4  => 'Info',
		);
		$level = isset($levels[$level]) ? $levels[$level] : $level;

		if (\Config::get('profiling'))
		{
			\Console::log($method.' - '.$msg);
		}

		$logger = new \Fluent\Logger\FluentLogger($host,$port,$options,$packer);

		$message  = array();


		$call = array();
		if ( ! empty($method))
		{
			//$call .= $method;
			$call['method'] = $method;
		}else{
			$backtrace = debug_backtrace();
			$i=0;
			for(;$i<count($backtrace);$i++){
				$backtrace[$i]['object'] = null;
				$break = false;

				if(isset($backtrace[$i]['class'])){
					if(!strstr($backtrace[$i]['class'],__NAMESPACE__)
						and !strstr($backtrace[$i]['class'],'Fuel\Core\Log')) {
	
						//
						if($level === 'Error'){
							//if ($level == 'Error') var_dump($backtrace);
						}
						$break = true;
					}
				}

				if($break){
					break;
				}
			}
			if(isset($backtrace[$i])){
				$call['class']    = isset($backtrace[$i]['class']) ? $backtrace[$i]['class'] : 'null';
				$call['type']     = isset($backtrace[$i]['type']) ? $backtrace[$i]['type'] : 'null';
				$call['function'] = isset($backtrace[$i]['function']) ? $backtrace[$i]['function'] : 'null';
				$call['line']     = isset($backtrace[$i-1]['line']) ? $backtrace[$i-1]['line'] : 'null';
			}
		}

		$message['level'] = $level;
		$message['date']  = date(\Config::get('log_date_format'));
		$message['msg']   = $msg;
	   	$message['call']  = $call;

		$res = $logger->post('td.'.$database.'.fuel_log',$message);

		return true;
	}
}
