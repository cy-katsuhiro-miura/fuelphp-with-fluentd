<?php

require_once APPPATH.'vendor/Fluent/Autoloader.php';
use Fluent\Logger\FluentLogger;
class Log_Td extends Fuel\Core\Log{

	public static function _init(){

		Fluent\Autoloader::register();
		
		parent::_init();
	}

	public static function write($level, $msg, $method = null){
		$log_config = \Config::get('log');

		$host = empty($log_config['td']['host']) ? null : $log_config['td']['host'];
		$port = empty($log_config['td']['port']) ? null : $log_config['td']['port'];
		$options = empty($log_config['td']['options']) ? array() : $log_config['td']['options'];
		$packer = empty($log_config['td']['packer']) ? null : $log_config['td']['packer'];
		$database = empty($log_config['td']['database']) ? 'default' : $log_config['td']['database'];

		if(array_key_exists('copy_driver',$log_config['td'])){
			foreach( $log_config['td']['copy_driver'] as $driver){
				$class = '\\Log_'.ucfirst($driver);

				try{
					$return = $class::write($level,$msg,$method);
				} catch (FuelException $e) {
				}
			}
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

		if (Config::get('profiling'))
		{
			\Console::log($method.' - '.$msg);
		}

		$logger = new FluentLogger($host,$port,$options,$packer);

		$message  = array();


		$call = array();
		if ( ! empty($method))
		{
			//$call .= $method;
			$call['method'] = $method;
		}else{
			$backtrace = debug_backtrace();
			if(isset($backtrace[4])){
				//$call .= isset($backtrace[3]['class']) ? $backtrace[3]['class'] : ' - ';
				//$call .= isset($backtrace[3]['type']) ? $backtrace[3]['type'] : ' - ';
				//$call .= isset($backtrace[3]['function']) ? $backtrace[3]['function'] : ' - ';
				//$call .= isset($backtrace[2]['line']) ? ':'.$backtrace[2]['line'] : ' - ';
				$call['class'] = isset($backtrace[4]['class']) ? $backtrace[4]['class'] : ' - ';
				$call['class'] = isset($backtrace[4]['type']) ? $backtrace[4]['type'] : ' - ';
				$call['class'] = isset($backtrace[4]['function']) ? $backtrace[4]['function'] : ' - ';
				$call['line']  = isset($backtrace[3]['line']) ? ':'.$backtrace[3]['line'] : ' - ';
			}
		}

		//$message .= $level.' '.(($level == 'info') ? ' -' : '-').' ';
		//$message .= date(\Config::get('log_date_format'));
		//$message .= ' --> '.(empty($call) ? '' : $call.' - ').$msg.PHP_EOL;

		$message['level'] = $level;
		$message['date']  = date(\Config::get('log_date_format'));
		$message['msg']   = $msg;
	   	$message['call']  = $call;

		$res = $logger->post('td.'.$database.'.fuel_log',$message);

		return true;
	}
}
