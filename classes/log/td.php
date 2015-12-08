<?php

//use Fluent\Logger\FluentLogger;

namespace Fluentd\Log;

class Td extends \Fuel\Core\Log{

	public static function _init(){

		\Fluent\Autoloader::register();
		
		parent::_init();
	}

	public static function write($level, $msg, $method = null)
	{
		$log_threshold = \Config::get('log_threshold');
		$config = \Config::get('log',array());

		if( isset($config['drivers']['td']['log_threshold']))
		{
			$log_threshold = $config['drivers']['td']['log_threshold'];
		}
		if ($level > $log_threshold)
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

		$host     = empty($config['drivers']['td']['host'])     ? null      : $config['drivers']['td']['host'];
		$port     = empty($config['drivers']['td']['port'])     ? null      : $config['drivers']['td']['port'];
		$options  = empty($config['drivers']['td']['options'])  ? array()   : $config['drivers']['td']['options'];
		$packer   = empty($config['drivers']['td']['packer'])   ? null      : $config['drivers']['td']['packer'];
		$database = empty($config['drivers']['td']['database']) ? 'default' : $config['drivers']['td']['database'];

		$logger = new \Fluent\Logger\FluentLogger($host,$port,$options,$packer);

		$message  = array();

		$call = array();
		if ( ! empty($method))
		{
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
							$msg = print_r($backtrace,true);
						}
						$break = true;
					}
				}

				if($break){
					break;
				}
			}
			if(isset($backtrace[$i])){
				$call['class']    = isset($backtrace[$i]['class'])    ? $backtrace[$i]['class']    : 'null';
				$call['type']     = isset($backtrace[$i]['type'])     ? $backtrace[$i]['type']     : 'null';
				$call['function'] = isset($backtrace[$i]['function']) ? $backtrace[$i]['function'] : 'null';
				$call['line']     = isset($backtrace[$i-1]['line'])   ? $backtrace[$i-1]['line']   : 'null';
			}
		}

		$message['level'] = $level;
		$message['date']  = date(\Config::get('log_date_format'));
		$message['msg']   = $msg;
	   	$message['call']  = $call;

		$res = $logger->post('td.'.$database.'.fuel_log',$message);
        if(!$res){
            return false;
        }

		return true;
	}
}
